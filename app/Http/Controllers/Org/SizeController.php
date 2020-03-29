<?php

namespace App\Http\Controllers\Org;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Database\Eloquent\ModelNotFoundException;

use App\Http\Controllers\Controller;
use App\Models\Org\Size;
use App\Models\Org\CustomerSizeGrid;
use Exception;
use App\Libraries\AppAuthorize;
use Illuminate\Support\Facades\DB;


class SizeController extends Controller
{
    var $authorize = null;

    public function __construct()
    {
      //add functions names to 'except' paramert to skip authentication
      $this->middleware('jwt.verify', ['except' => ['index']]);
      $this->authorize = new AppAuthorize();
    }

    //get Size list
    public function index(Request $request)
    {
      $type = $request->type;
      if($type == 'datatable')   {
        $data = $request->all();
        return response($this->datatable_search($data));
      }
      else if($type == 'auto')    {
        $search = $request->search;
        $size_type = $request->size_type;
        $category_id = $request->category_id;
        $subcategory_id = $request->subcategory_id;
        return response($this->autocomplete_search($size_type, $search, $category_id, $subcategory_id));
      }
      else if($type == 'loadsizes'){
          return response($this->LoadSizes());
      }
      else if($type == 'size_selector'){
        $search = $request->search;
        return response([
          'data' => $this->size_selector_list($search)
        ]);
      }
      else {
        $active = $request->active;
        $fields = $request->fields;
        return response([
          'data' => $this->list($active , $fields)
        ]);
      }
    }


    //create a Size
    public function store(Request $request)
    {
      if($this->authorize->hasPermission('SIZE_MANAGE'))//check permission
      {
        $size = new Size();
        if($size->validate($request->all()))
        {
          $size->fill($request->all());
          $size->status = 1;
          $size->type='G';
          $size->size_name=strtoupper($size->size_name);
          $size->save();

          return response([ 'data' => [
            'message' => 'Size saved successfully',
            'size' => $size,
            'status'=>'1',
            'type'=>'G'
            ]
          ], Response::HTTP_CREATED );
        }
        else
        {
            $errors = $size->errors();// failure, get errors
            $errors_str = $size->errors_tostring();
            return response(['errors' => ['validationErrors' => $errors, 'validationErrorsText' => $errors_str]], Response::HTTP_UNPROCESSABLE_ENTITY);
        }
      }
      else{
        return response($this->authorize->error_response(), 401);
      }
    }


    //get a Size
    public function show($id)
    {
      if($this->authorize->hasPermission('SIZE_MANAGE'))//check permission
      {
        $size = Size::find($id);
        if($size == null)
          throw new ModelNotFoundException("Requested size not found", 1);
        else
          return response([ 'data' => $size ]);
      }
      else{
        return response($this->authorize->error_response(), 401);
      }
    }


    //update a Size
    public function update(Request $request, $id)
    {
      if($this->authorize->hasPermission('SIZE_MANAGE'))//check permission
      {
          $size = Size::find($id);

        if($size->validate($request->all()))
        {
          $is_exists_cust=DB::table('cust_size_grid')->where('size_id', $id)->exists();
          $is_exists_sales=DB::table('merc_customer_order_size')->where('size_id', $id)->exists();
          if($is_exists_cust||$is_exists_sales){
            return response([ 'data' => [
              'message' => 'Size Already in Use',
              'size' => $size,
              'status'=>'0'
            ]]);
          }
          else {
          $size->fill($request->except('size_name'));
          $size->save();

          return response([ 'data' => [
            'message' => 'Size updated successfully',
            'size' => $size,
            'status'=>'1'
          ]]);
        }
        }
        else
        {
          $errors = $size->errors();// failure, get errors
          return response(['errors' => ['validationErrors' => $errors]], Response::HTTP_UNPROCESSABLE_ENTITY);
        }
      }
      else{
        return response($this->authorize->error_response(), 401);
      }
    }


    //deactivate a Size
    public function destroy($id)
    {
      if($this->authorize->hasPermission('SIZE_DELETE'))//check permission
      {
        $is_exists_cust=DB::table('cust_size_grid')->where('size_id', $id)->exists();
        $is_exists_sales=DB::table('merc_customer_order_size')->where('size_id', $id)->exists();

      if($is_exists_cust||$is_exists_sales){
        return response([
          'data' => [
            'status'=>'0',
              'message'=>'Size Already in Use',
            ]
        ]);
      }
      else {
        $size = Size::where('size_id', $id)->update(['status' => 0]);
        return response([
          'data' => [
            'status'=>'1',
            'message' => 'Size was deactivated successfully.',
            'size' => $size
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
        return response($this->validate_duplicate_code($request->size_id , $request->size_name));
      }
    }


    //check Size code already exists
    private function validate_duplicate_code($id , $code)
    {
      $size = Size::where('size_name','=',$code)->first();
      if($size == null){
        return ['status' => 'success'];
      }
      else if($size->size_id == $id){
        return ['status' => 'success'];
      }
      else {
        return ['status' => 'error','message' => 'Size code already exists'];
      }
    }


    //get filtered fields only
    private function list($active = 0 , $fields = null)
    {
      $query = null;
      if($fields == null || $fields == '') {
        $query = Size::select('*');
      }
      else{
        $fields = explode(',', $fields);
        $query = Size::select($fields);
        if($active != null && $active != ''){
          $query->where([['status', '=', $active]]);
        }
      }
      return $query->get();
    }


    private function size_selector_list($search){
      $list = Size::select('size_id', 'size_name')
      ->where('size_name', 'like', '%' . $search . '%')
      ->get();
      return $list;
    }


    //search Size for autocomplete
    private function autocomplete_search($size_type, $search, $category_id, $subcategory_id)
  	{
      $active=1;
  		$query = Size::select('size_id','size_name')
  		->where([['size_name', 'like', '%' . $search . '%'], ['status','=',$active]]);

      if($size_type != null && $size_type == 'M'){
        $query->where([['category_id','=', $category_id], ['subcategory_id','=', $subcategory_id]]);
        $query->where('type','=','M');
      }
      else{
        $query->where('type','=','G');
      }
      $size_lists = $query->get();
  		return $size_lists;
  	}


    //get searched Sizes for datatable plugin format
    private function datatable_search($data)
    {
      if($this->authorize->hasPermission('SIZE_MANAGE'))//check permission
      {
        $start = $data['start'];
        $length = $data['length'];
        $draw = $data['draw'];
        $search = $data['search']['value'];
        $order = $data['order'][0];
        $order_column = $data['columns'][$order['column']]['data'];
        $order_type = $order['dir'];

        $size_list = Size::select('*')
        ->where('size_name'  , 'like', $search.'%' )
        ->where('type','=','G')
        ->orderBy($order_column, $order_type)
        ->offset($start)->limit($length)->get();

        $size_count = Size::where('size_name'  , 'like', $search.'%' )
        ->where('type','=','G')
        ->count();

        return [
            "draw" => $draw,
            "recordsTotal" => $size_count,
            "recordsFiltered" => $size_count,
            "data" => $size_list
        ];
      }
      else{
        return response($this->authorize->error_response(), 401);
      }
    }

    private function LoadSizes(){
        $sizeList = Size::all()->where('status','=','1');
        return $sizeList;
    }

}
