<?php

namespace App\Http\Controllers\Finance;

use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Database\Eloquent\ModelNotFoundException;

use App\Http\Controllers\Controller;
use App\Models\Finance\GoodsType;
use App\Models\Org\Supplier;
use App\Libraries\AppAuthorize;
use App\Libraries\CapitalizeAllFields;
use Illuminate\Support\Facades\DB;

class GoodsTypeController extends Controller
{
    var $authorize = null;

    public function __construct()
    {
      //add functions names to 'except' paramert to skip authentication
      $this->middleware('jwt.verify', ['except' => ['index']]);
      $this->authorize = new AppAuthorize();
    }

    //get goods type list
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

    //create g goods type
    public function store(Request $request)
    {
      if($this->authorize->hasPermission('GOODS_TYPE_MANAGE'))//check permission
      {
        $goodsType = new GoodsType();
        if($goodsType->validate($request->all()))
        {
          $goodsType->goods_type_description = $request->goods_type_description;
          $goodsType->status = 1;
          $capitalizeAllFields=CapitalizeAllFields::setCapitalAll($goodsType);
          $goodsType->save();

          return response([ 'data' => [
            'message' => 'Goods type saved successfully',
            'goodsType' => $goodsType,
            'status'=>'1'
            ]
          ], Response::HTTP_CREATED );
        }
        else {
          $errors = $goodsType->errors();// failure, get errors
          $errors_str = $goodsType->errors_tostring();
          return response(['errors' => ['validationErrors' => $errors, 'validationErrorsText' => $errors_str]], Response::HTTP_UNPROCESSABLE_ENTITY);
        }
      }
      else {
        return response($this->authorize->error_response(), 401);
      }
    }

    //get goods type
    public function show($id)
    {
      if($this->authorize->hasPermission('GOODS_TYPE_MANAGE'))//check permission
      {
        $goodsType = GoodsType::find($id);
        if($goodsType == null)
          throw new ModelNotFoundException("Requested goods type not found", 1);
        else
          return response( ['data' => $goodsType] );
      }
      else{
        return response($this->authorize->error_response(), 401);
      }
    }


    //update a goods type
    public function update(Request $request, $id)
    {
      if($this->authorize->hasPermission('GOODS_TYPE_MANAGE'))//check permission
        {
          $is_exsits_in_supplier=DB::table('org_supplier')->where('type_of_service',$id)->exists();
          $is_exsits_in_customer=DB::table('cust_customer')->where('type_of_service',$id)->exists();
          if($is_exsits_in_supplier||$is_exsits_in_customer){
            return response([ 'data' => [
              'message' => 'Goods Type Already in Use',
              'status' => '0'
            ]]);

          }else{
        $goodsType = GoodsType::find($id);
        if($goodsType->validate($request->all()))
        {
          $goodsType->goods_type_description = $request->goods_type_description;
          $capitalizeAllFields=CapitalizeAllFields::setCapitalAll($goodsType);
          $goodsType->save();

          return response([ 'data' => [
            'message' => 'Goods type updated successfully',
            'goodsType' => $goodsType,
            'status'=>'1'
          ]]);
        }
        else {
          $errors = $goodsType->errors();// failure, get errors
          $errors_str = $goodsType->errors_tostring();
          return response(['errors' => ['validationErrors' => $errors, 'validationErrorsText' => $errors_str]], Response::HTTP_UNPROCESSABLE_ENTITY);
        }
      }
    }
      else{
        return response($this->authorize->error_response(), 401);
      }
    }

    //deactivate a goods type
    public function destroy($id)
    {
      if($this->authorize->hasPermission('GOODS_TYPE_DELETE'))//check permission
      {
        $is_exsits_in_supplier=DB::table('org_supplier')->where('type_of_service',$id)->exists();
        $is_exsits_in_customer=DB::table('cust_customer')->where('type_of_service',$id)->exists();
        if($is_exsits_in_supplier||$is_exsits_in_customer){
          return response([ 'data' => [
            'message' => 'Goods type Already in Use',
            'status' => '0'
          ]]);
        }
        else{
        $goodsType = GoodsType::where('goods_type_id', $id)->update(['status' => 0]);
        return response([
          'data' => [
            'message' => 'Goods type was deactivated successfully.',
            'goodsType' => $goodsType,
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
        return response($this->validate_duplicate_code($request->goods_type_id , $request->goods_type_description));
      }
    }


    //check goods type description already exists
    private function validate_duplicate_code($id , $code)
    {
      $goodsType = GoodsType::where('goods_type_description','=',$code)->first();
      if($goodsType == null){
        return ['status' => 'success'];
      }
      else if($goodsType->goods_type_id == $id){
        return ['status' => 'success'];
      }
      else {
        return ['status' => 'error','message' => 'Goods type description already exists'];
      }
    }


    //get filtered fields only
    private function list($active = 0 , $fields = null)
    {
      $query = null;
      if($fields == null || $fields == '') {
        $query = GoodsType::select('*');
      }
      else{
        $fields = explode(',', $fields);
        $query = GoodsType::select($fields);
        if($active != null && $active != ''){
          $query->where([['status', '=', $active]]);
        }
      }
      return $query->get();
    }


    //search goods types for autocomplete
    private function autocomplete_search($search)
  	{
  		$goods_type_lists = GoodsType::select('goods_type_id','goods_type_description')
  		->where([['goods_type_description', 'like', '%' . $search . '%'],]) ->get();
  		return $goods_type_lists;
  	}


    //get searched goods types for datatable plugin format
    private function datatable_search($data)
    {
      if($this->authorize->hasPermission('GOODS_TYPE_MANAGE'))//check permission
      {
        $start = $data['start'];
        $length = $data['length'];
        $draw = $data['draw'];
        $search = $data['search']['value'];
        $order = $data['order'][0];
        $order_column = $data['columns'][$order['column']]['data'];
        $order_type = $order['dir'];

        $goods_type_list = GoodsType::select('*')
        ->where('goods_type_description'  , 'like', $search.'%' )
        ->orderBy($order_column, $order_type)
        ->offset($start)->limit($length)->get();

        $goods_type_count = GoodsType::where('goods_type_description'  , 'like', $search.'%' )
        ->count();

        return [
            "draw" => $draw,
            "recordsTotal" => $goods_type_count,
            "recordsFiltered" => $goods_type_count,
            "data" => $goods_type_list
        ];
      }
      else{
        return response($this->authorize->error_response(), 401);
      }
    }

}
