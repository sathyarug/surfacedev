<?php

namespace App\Http\Controllers\Store;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Database\Eloquent\ModelNotFoundException;

use App\Http\Controllers\Controller;
use App\Models\Store\Store;
use Exception;
use App\Libraries\AppAuthorize;
use App\Libraries\CapitalizeAllFields;
class StoreController extends Controller
{
    var $authorize = null;

    public function __construct()
    {
      //add functions names to 'except' paramert to skip authentication
      $this->middleware('jwt.verify', ['except' => ['index']]);
      $this->authorize = new AppAuthorize();
    }

    //get Store list
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
      }else if($type == 'loc-stores'){
        $active = $request->active;
        $fields = $request->fields;
        return response([ 'data' => $this->loc_stores_list($active , $fields) ]);
      }
      else {
        $active = $request->active;
        $fields = $request->fields;
        return response([
          'data' => $this->list($active , $fields)
        ]);
      }
    }


    //create a Store
    public function store(Request $request)
    {
      if($this->authorize->hasPermission('STORE_MANAGE'))//check permission
      {
        $store = new Store();
        if($store->validate($request->all()))
        {
          $store->fill($request->all());
          $capitalizeAllFields=CapitalizeAllFields::setCapitalAll($store);
          $store->email=$request->email;
          $store->status = 1;
          $store->save();

          return response([ 'data' => [
            'message' => 'Store Saved Successfully',
            'store' => $store,
            'status'=>1
            ]
          ], Response::HTTP_CREATED );
        }
        else
        {
            $errors = $store->errors();// failure, get errors
            return response(['errors' => ['validationErrors' => $errors]], Response::HTTP_UNPROCESSABLE_ENTITY);
        }
      }
      else{
        return response($this->authorize->error_response(), 401);
      }
    }


    //get a Store
    public function show($id)
    {
      if($this->authorize->hasPermission('STORE_MANAGE'))//check permission
      {
        $store = Store::find($id);
        if($store == null)
          throw new ModelNotFoundException("Requested store not found", 1);
        else
          return response([ 'data' => $store ]);
      }
      else{
        return response($this->authorize->error_response(), 401);
      }
    }


    //update a Store
    public function update(Request $request, $id)
    {
      if($this->authorize->hasPermission('STORE_MANAGE'))//check permission
      {
        $store = Store::find($id);
        if($store->validate($request->all()))
        {
          $is_exsists_bin=DB::table('org_store_bin')->where('store_bin_id','=',$id);
          if($is_exsists_bin==true){
            return response([ 'data' => [
              'message' => 'Store Already in Use',
              'status' => 0
            ]]);
          }
          $store->fill($request->except('store_name'));
          $capitalizeAllFields=CapitalizeAllFields::setCapitalAll($store);
          $store->email=$request->email;
          $store->save();

          return response([ 'data' => [
            'message' => 'Store Updated Successfully',
            'store' => $store,
            'status'=>1
          ]]);
        }
        else
        {
          $errors = $store->errors();// failure, get errors
          return response(['errors' => ['validationErrors' => $errors]], Response::HTTP_UNPROCESSABLE_ENTITY);
        }
      }
      else{
        return response($this->authorize->error_response(), 401);
      }
    }


    //deactivate a Store
    public function destroy($id)
    {
      if($this->authorize->hasPermission('STORE_DELETE'))//check permission
      {
        $store = Store::where('store_id', $id)->update(['status' => 0]);
        return response([
          'data' => [
            'message' => 'Store was deactivated successfully.',
            'store' => $store
          ]
        ] , Response::HTTP_NO_CONTENT);
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
        return response($this->validate_duplicate_code($request->store_id , $request->store_name));
      }
    }


    //check Store code already exists
    private function validate_duplicate_code($id , $code)
    {
      $store = Store::where('store_name','=',$code)->first();
      if($store == null){
        return ['status' => 'success'];
      }
      else if($store->store_id == $id){
        return ['status' => 'success'];
      }
      else {
        return ['status' => 'error','message' => 'Store code already exists'];
      }
    }


    //get filtered fields only
    private function list($active = 0 , $fields = null)
    {
      $query = null;
      if($fields == null || $fields == '') {
        $query = Store::select('*');
      }
      else{
        $fields = explode(',', $fields);
        $query = Store::select($fields);
        if($active != null && $active != ''){
          $payload = auth()->payload();
          $query->where([['status', '=', $active],['loc_id', '=', $payload->get('loc_id') ]]);
        }
      }
      return $query->get();
    }

    private function loc_stores_list($active = 0 , $fields = null)
    {
      $query = Store::select('store_id','store_name')
      ->where('org_store.loc_id' ,'=', auth()->payload()['loc_id'])
      ->get();
      return $query;
    }

    //search Store for autocomplete
    private function autocomplete_search($search)
  	{
      $user = auth()->payload();
      $location=$user['loc_id'];
  		$store_lists = Store::select('store_id','store_name')
  		->where([['store_name', 'like', '%' . $search . '%'],])
      ->where('status','=',1)
      ->where('loc_id','=',$location)
      ->get();
  		return $store_lists;
  	}


    //get searched Stores for datatable plugin format
    private function datatable_search($data)
    {
      if($this->authorize->hasPermission('STORE_MANAGE'))//check permission
      {
        $start = $data['start'];
        $length = $data['length'];
        $draw = $data['draw'];
        $search = $data['search']['value'];
        $order = $data['order'][0];
        $order_column = $data['columns'][$order['column']]['data'];
        $order_type = $order['dir'];

        $store_list = Store::join('org_location' , 'org_location.loc_id' , '=' , 'org_store.loc_id')
        ->select('org_store.*','org_location.loc_name')
        ->where('store_name'  , 'like', $search.'%' )
        ->orWhere('loc_name'  , 'like', $search.'%' )
        ->orderBy($order_column, $order_type)
        ->offset($start)->limit($length)->get();

        $store_count = Store::join('org_location' , 'org_location.loc_id' , '=' , 'org_store.loc_id')
        ->where('store_name'  , 'like', $search.'%' )
        ->orWhere('loc_name'  , 'like', $search.'%' )
        ->count();

        return [
            "draw" => $draw,
            "recordsTotal" => $store_count,
            "recordsFiltered" => $store_count,
            "data" => $store_list
        ];
      }
      else{
        return response($this->authorize->error_response(), 401);
      }
    }

}
