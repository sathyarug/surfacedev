<?php

namespace App\Http\Controllers\Merchandising;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Database\Eloquent\ModelNotFoundException;

use App\Http\Controllers\Controller;
use App\Models\Merchandising\ColorOption;
use App\Models\Merchandising\BulkCostingFeatureDetails;
use Exception;
use App\Libraries\AppAuthorize;
use Illuminate\Support\Facades\DB;

class ColorOptionController extends Controller
{

    var $authorize = null;

    public function __construct()
    {
      //add functions names to 'except' paramert to skip authentication
      $this->middleware('jwt.verify', ['except' => ['index']]);
      $this->authorize = new AppAuthorize();
    }

    //get Origin Type list
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


    //create a Origin Type
    public function store(Request $request)
    {
      if($this->authorize->hasPermission('COLOR_OPTION_MANAGE'))//check permission
      {
        $colorOption = new ColorOption;
        if($colorOption->validate($request->all()))
        {
          $colorOption->fill($request->all());
          $colorOption->color_option=strtoupper($colorOption->color_option);
          $colorOption->color_type_code=strtoupper($colorOption->color_type_code);
          $colorOption->status = 1;
          $colorOption->save();

          return response([ 'data' => [
            'message' => 'Color Type saved successfully',
            'originType' => $colorOption,
            'status'=>'1'
            ]
          ], Response::HTTP_CREATED );
        }
        else
        {
          $errors = $colorOption->errors();// failure, get errors
          $errors_str = $colorOption->errors_tostring();
          return response(['errors' => ['validationErrors' => $errors, 'validationErrorsText' => $errors_str]], Response::HTTP_UNPROCESSABLE_ENTITY);
        }
      }
      else{
        return response($this->authorize->error_response(), 401);
      }
    }


    //get a Origin Type
    public function show($id)
    {
      if($this->authorize->hasPermission('COLOR_OPTION_MANAGE'))//check permission
      {
        $colorOption = ColorOption::find($id);
        if($colorOption == null)
          throw new ModelNotFoundException("Requested Color Type not found", 1);
        else
          return response([ 'data' => $colorOption ]);
      }
      else{
        return response($this->authorize->error_response(), 401);
      }
    }


    //update a Origin Type
    public function update(Request $request, $id)
    {
      if($this->authorize->hasPermission('COLOR_OPTION_MANAGE'))//check permission
      {
          $is_exsits=DB::table('costing')->where('color_type_id',$id)->exists();
          if(  $is_exsits==true){
            return response(['data'=>[
              'message'=>'Color Type Already in Use',
              'status'=>'0'
              ]]);
          }
          else{
        $colorOption = ColorOption::find($id);
        if($colorOption->validate($request->all()))
        {
          $colorOption->fill($request->all());
          $colorOption->color_option=strtoupper($colorOption->color_option);
          $colorOption->color_type_code=strtoupper($colorOption->color_type_code);
          $colorOption->save();

          return response([ 'data' => [
            'message' => 'Color Type updated successfully',
            'colorOption' => $colorOption,
            'status'=>'1'
          ]]);
        }
        else
        {
          $errors = $colorOption->errors();// failure, get errors
          return response(['errors' => ['validationErrors' => $errors]], Response::HTTP_UNPROCESSABLE_ENTITY);
        }
      }
      }
      else{
        return response($this->authorize->error_response(), 401);
      }
    }


    //deactivate a Origin Type
    public function destroy($id)
    {
      if($this->authorize->hasPermission('COLOR_OPTION_DELETE'))//check permission
      {
        $is_exsits=DB::table('costing')->where('color_type_id',$id)->exists();
        if($is_exsits==true){
          return response([
            'data'=>[
              'message'=>'Color Type Already in Use.',
              'status'=>'0'
            ]
          ]);
        }
        else{
        $colorOption = ColorOption::where('col_opt_id', $id)->update(['status' => 0]);
        return response([
          'data' => [
            'message' => 'Color Type deactivated successfully.',
            'colorOption' => $colorOption,
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
        return response($this->validate_duplicate_code($request->col_opt_id , $request->color_option));
      }
    }


    //check OriginType code already exists
    private function validate_duplicate_code($id , $code)
    {
      $colorOption = ColorOption::where('color_option','=',$code)->first();
      if($colorOption == null){
        return ['status' => 'success'];
      }
      else if($colorOption->col_opt_id == $id){
        return ['status' => 'success'];
      }
      else {
        return ['status' => 'error','message' => 'Color Type already exists'];
      }
    }


    //get filtered fields only
    private function list($active = 0 , $fields = null)
    {
      $query = null;
      if($fields == null || $fields == '') {
        $query = ColorOption::select('*')->where('status','=','1');

      }
      else{
        $fields = explode(',', $fields);
        $query =ColorOption::select($fields);
        if($active != null && $active != ''){
          $query->where([['status', '=', $active]]);
        }
      }
      return $query->get();
    }

    //search Origin Type for autocomplete
    private function autocomplete_search($search)
  	{
  		$color_option_lists = ColorOption::select('col_opt_id','color_option')
  		->where([['color_option', 'like', '%' . $search . '%'],])
      ->where('status','=',1)->get();
  		return $color_option_lists;
  	}


    //get searched OriginTypes for datatable plugin format
    private function datatable_search($data)
    {
      if($this->authorize->hasPermission('COLOR_OPTION_MANAGE'))//check permission
      {
        $start = $data['start'];
        $length = $data['length'];
        $draw = $data['draw'];
        $search = $data['search']['value'];
        $order = $data['order'][0];
        $order_column = $data['columns'][$order['column']]['data'];
        $order_type = $order['dir'];

        $color_option_lists =ColorOption::select('*')
        ->where('color_option'  , 'like', $search.'%' )
        ->orderBy($order_column, $order_type)
        ->offset($start)->limit($length)->get();

      $color_option_count = ColorOption::where('color_option'  , 'like', $search.'%' )->count();

        return [
            "draw" => $draw,
            "recordsTotal" => $color_option_count,
            "recordsFiltered" =>$color_option_count,
            "data" => $color_option_lists
        ];
      }
      else{
        return response($this->authorize->error_response(), 401);
      }
    }

}
