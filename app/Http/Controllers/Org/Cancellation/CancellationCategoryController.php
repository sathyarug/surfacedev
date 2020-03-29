<?php

namespace App\Http\Controllers\Org\Cancellation;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Database\Eloquent\ModelNotFoundException;

use App\Http\Controllers\Controller;
use App\Models\Org\Cancellation\CancellationCategory;
use App\Models\Org\Cancellation\CancellationReason;
use App\Libraries\CapitalizeAllFields;
use Illuminate\Support\Facades\DB;
use Exception;
use App\Libraries\AppAuthorize;

class CancellationCategoryController extends Controller
{
    public function __construct()
    {
      //add functions names to 'except' paramert to skip authentication
      $this->middleware('jwt.verify', ['except' => ['index']]);
      $this->authorize = new AppAuthorize();
    }

    //get CancellationCategory list
    public function index(Request $request)
    {
      $type = $request->type;
      if($type == 'datatable')   {
        $data = $request->all();
        return response($this->datatable_search($data));
      }
      else if($type == 'auto')    {
        $search = $request->search;
        return response($this->autocomplete_search($search));
      }
      else {
        $active = $request->active;
        $fields = $request->fields;
        return response([
          'data' => $this->list($active , $fields)
        ]);
      }
    }


    //create a Cancellation Category
    public function store(Request $request)
    {
      if($this->authorize->hasPermission('CANCEL_CAT_MANAGE'))//check permission
      {
        $category = new CancellationCategory();
        if($category->validate($request->all()))
        {
          $category->fill($request->all());
          $category->status = 1;
          //$capitalizeAllFields=CapitalizeAllFields::setCapitalAll($category);
          $category->save();

          return response([ 'data' => [
            'message' => 'Cancellation category saved successfully',
            'cancellationCategory' => $category,
            'status'=>1
            ]
          ], Response::HTTP_CREATED );
        }
        else
        {
            $errors = $category->errors();// failure, get errors
            $errors_str = $category->errors_tostring();
            return response(['errors' => ['validationErrors' => $errors, 'validationErrorsText' => $errors_str]], Response::HTTP_UNPROCESSABLE_ENTITY);
        }
      }
      else{
        return response($this->authorize->error_response(), 401);
      }
    }


    //get a CancellationCategory
    public function show($id)
    {
      if($this->authorize->hasPermission('CANCEL_CAT_MANAGE'))//check permission
      {
        $category = CancellationCategory::find($id);
        if($category == null)
          throw new ModelNotFoundException("Requested Cancellation category not found", 1);
        else
          return response([ 'data' => $category ]);
      }
      else{
        return response($this->authorize->error_response(), 401);
      }
    }


    //update a Cancellation Category
    public function update(Request $request, $id)
    {
      if($this->authorize->hasPermission('CANCEL_CAT_MANAGE'))//check permission
      {
        $is_exists=DB::table('org_cancellation_reason')->where('reason_category','=',$id)->exists();

        $category = CancellationCategory::find($id);
        if($category->validate($request->all()))
        {
          if($is_exists==true){
              return response([ 'data' => [
              'message' => 'Cancellation category already in use',
              'cancellationCategory' => $category,
              'status'=>0
            ]]);
          }

          else if($is_exists==false){
          $category->fill($request->except('category_code'));
          //$capitalizeAllFields=CapitalizeAllFields::setCapitalAll($category);
          $category->save();

          return response([ 'data' => [
            'message' => 'Cancellation category updated successfully',
            'cancellationCategory' => $category,
            'status'=>1
          ]]);
        }
      }
        else
        {
          $errors = $category->errors();// failure, get errors
          $errors_str = $category->errors_tostring();
          return response(['errors' => ['validationErrors' => $errors, 'validationErrorsText' => $errors_str]], Response::HTTP_UNPROCESSABLE_ENTITY);
        }
      }

      else{
        return response($this->authorize->error_response(), 401);
      }
    }


    //deactivate a Cancellation Category
    public function destroy($id)
    {
      if($this->authorize->hasPermission('CANCEL_CAT_DELETE'))//check permission
      {
        $cancellationReason=CancellationReason::where([['reason_category','=',$id]]);
        if($cancellationReason!=null){
          return response([
            'data'=>[
              'status'=>'0',
              'message'=>'Cancellation Category Already in use'

            ]
          ]);
        }
        else if($cancellationReason==null){
        $category = CancellationCategory::where('category_id', $id)->update(['status' => 0]);
        return response([
          'data' => [
            'message' => 'Cancellation category was deactivated successfully.',
            'cancellationCategory' => $category,
            'status'=>'1'
          ]
        ]);
      }
      }
      else{
        return response($this->authorize->error_response(), 401);
      }
    }


    //validate anything based on requirements
    public function validate_data(Request $request){
      $for = $request->for;
      if($for == 'duplicate')
      {
        return response($this->validate_duplicate_code($request->category_id , $request->category_code));
      }
    }


    //check Cancellation Category code already exists
    private function validate_duplicate_code($id , $code)
    {
      $category = CancellationCategory::where('category_code','=',$code)->first();
      if($category == null){
        return ['status' => 'success'];
      }
      else if($category->category_id == $id){
        return ['status' => 'success'];
      }
      else {
        return ['status' => 'error','message' => 'Cancellation category code already exists'];
      }
    }


    //get filtered fields only
    private function list($active = 0 , $fields = null)
    {
      $query = null;
      if($fields == null || $fields == '') {
        $query = CancellationCategory::select('*');
      }
      else{
        $fields = explode(',', $fields);
        $query = CancellationCategory::select($fields);
        if($active != null && $active != ''){
          $query->where([['status', '=', $active]]);
        }
      }
      return $query->get();
    }

    //search Cancellation Category for autocomplete
    private function autocomplete_search($search)
  	{
  		$category_lists = CancellationCategory::select('category_id','category_description')
  		->where([['category_description', 'like', '%' . $search . '%'],]) ->get();
  		return $category_lists;
  	}


    //get searched Cancellation Categorys for datatable plugin format
    private function datatable_search($data)
    {
      if($this->authorize->hasPermission('CANCEL_CAT_MANAGE'))//check permission
      {
        $start = $data['start'];
        $length = $data['length'];
        $draw = $data['draw'];
        $search = $data['search']['value'];
        $order = $data['order'][0];
        $order_column = $data['columns'][$order['column']]['data'];
        $order_type = $order['dir'];

        $category_list = CancellationCategory::select('*')
        ->where('category_code'  , 'like', $search.'%' )
        ->orWhere('category_description','like',$search.'%')
        ->orderBy($order_column, $order_type)
        ->offset($start)->limit($length)->get();

        $category_count = CancellationCategory::where('category_code'  , 'like', $search.'%' )
        ->orWhere('category_description','like',$search.'%')
        ->count();

        return [
            "draw" => $draw,
            "recordsTotal" => $category_count,
            "recordsFiltered" => $category_count,
            "data" => $category_list
        ];
      }
      else{
        return response($this->authorize->error_response(), 401);
      }
    }

}
