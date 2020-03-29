<?php

namespace App\Http\Controllers\Store;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Http\Controllers\Controller;
use App\Models\Store\SubStore;
use App\Models\Store\StoreBin;
use App\Models\Store\Stock;
use App\Libraries\CapitalizeAllFields;
use Illuminate\Support\Facades\DB;
use App\Libraries\AppAuthorize;

class SubStoreController extends Controller
{
    var $authorize = null;

    public function __construct()
    {
      //add functions names to 'except' paramert to skip authentication
      $this->middleware('jwt.verify', ['except' => ['index']]);
      $this->authorize = new AppAuthorize();
    }

    public function index(Request $request) {
        $type = $request->type;

        if ($type == 'datatable') {
            $data = $request->all();
            return response($this->datatable_search($data));
        } else if ($type == 'auto') {
            $search = $request->search;
            $storeId=$request->storeId;
            return response($this->autocomplete_search($search,$storeId));
        } else if($type == 'loc-sub-stores'){
            return response([ 'data' => $this->loc_sub_stores_list($request->fields) ]);
        }else {
            $active = $request->active;
            $fields = $request->fields;
            return response([
                'data' => $this->list($active, $fields)
            ]);
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
      if($this->authorize->hasPermission('SUB_STORE_MANAGE'))//check permission
      {
        $subStore = new SubStore();
        if ($subStore->validate($request->all())) {
            $subStore->fill($request->all());
            $subStore->status = 1;
            $capitalizeAllFields=CapitalizeAllFields::setCapitalAll($subStore);
            $subStore->save();
            //create new quarantine bin  when create a new sub store
            $quarantine_bin=new StoreBin();
            $quarantine_bin->store_id=$subStore->store_id;
            $quarantine_bin->substore_id=$subStore->substore_id;
            $quarantine_bin->store_bin_name="QUARANTINE";
            $quarantine_bin->store_bin_description="QUARANTINE BIN";
            $quarantine_bin->status=1;
            $quarantine_bin->quarantine=1;
            $quarantine_bin->save();

            return response(['data' => [
                    'message' => 'Sub Store saved successfully',
                    'subStore' => $subStore,
                    'status'=>1
                ]
                    ], Response::HTTP_CREATED);
        } else {
          $errors = $subStore->errors();// failure, get errors
          $errors_str = $subStore->errors_tostring();
          return response(['errors' => ['validationErrors' => $errors, 'validationErrorsText' => $errors_str]], Response::HTTP_UNPROCESSABLE_ENTITY);
        }
      }
      else{
        return response($this->authorize->error_response(), 401);
      }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
      if($this->authorize->hasPermission('SUB_STORE_MANAGE'))//check permission
      {
        $subStore = SubStore::join('org_store','org_substore.store_id','=','org_store.store_id')
        ->select('org_substore.*','org_store.store_name')
        ->find($id);
        if ($subStore == null)
            throw new ModelNotFoundException("Requested sub store not found", 1);
        else
            return response(['data' => $subStore]);
      }
      else{
        return response($this->authorize->error_response(), 401);
      }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
      if($this->authorize->hasPermission('SUB_STORE_MANAGE'))//check permission
      {
        $subStore = SubStore::find($id);
        if ($subStore->validate($request->all())) {
            $subStore->fill($request->except('store_name'));
            $capitalizeAllFields=CapitalizeAllFields::setCapitalAll($subStore);
            $subStore->save();

            return response(['data' => [
                    'message' => 'Sub Store is updated successfully',
                    'subStore' => $subStore,
                    'status'=>1
            ]]);
        } else {
          $errors = $subStore->errors();// failure, get errors
          $errors_str = $subStore->errors_tostring();
          return response(['errors' => ['validationErrors' => $errors, 'validationErrorsText' => $errors_str]], Response::HTTP_UNPROCESSABLE_ENTITY);
        }
      }
      else{
        return response($this->authorize->error_response(), 401);
      }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
      if($this->authorize->hasPermission('SUB_STORE_DELETE'))//check permission
      {
        $is_exixts_bin=DB::table('org_store_bin')->where('substore_id',$id)->exists();
        $is_exists_grn=DB::table('store_grn_header')->where('sub_store',$id)->exists();
        if($is_exixts_bin==true||$is_exists_grn==true){
          return response([
              'data' => [
                  'message' => 'Sub Store Already in Use',
                  'status'=>0,
              ]
            ]);
        }
        $subStore = SubStore::where('substore_id', $id)->update(['status' => 0]);
        return response([
            'data' => [
                'message' => 'Sub Store is deactivated successfully.',
                'store' => $subStore,
                'status'=>1
            ]
          ]);
      }
      else{
        return response($this->authorize->error_response(), 401);
      }
    }

    //get filtered fields only
    private function list($active = 1, $fields = null) {
      //dd("xadadad");
        $query = null;
        if ($fields == null || $fields == '') {
            $query = SubStore::select('*')->where('status',$active);
        } else {

            $fields = explode(',', $fields);
            $query = SubStore::select($fields)->where('status','=',1);
            if ($active != null && $active != '') {

                $query->where([['status', '=', $active]]);
            }
        }
        return $query->get();
    }

    private function loc_sub_stores_list($store)
    { 
      $query = SubStore::select('substore_id','substore_name')
      ->where('org_substore.store_id' ,'=', $store)
      ->get();
      return $query;
    }

    //search goods types for autocomplete
    private function autocomplete_search($search,$storeId) {
      //dd($storeId);
        $bin_list = SubStore::select('substore_id', 'substore_name')
                        ->where([['substore_name', 'like', '%' . $search . '%'],])
                        ->where('store_id','=',$storeId)
                        ->where('status','=',1)
                        ->get();
        return $bin_list;
    }

    //get searched goods types for datatable plugin format
    private function datatable_search($data)
    {
      if($this->authorize->hasPermission('SUB_STORE_MANAGE'))//check permission
      {
        $start = $data['start'];
        $length = $data['length'];
        $draw = $data['draw'];
        $search = $data['search']['value'];
        $order = $data['order'][0];
        $order_column = $data['columns'][$order['column']]['data'];
        $order_type = $order['dir'];

        $bin_list = SubStore::join('org_store','org_substore.store_id','=','org_store.store_id')
                        ->select('org_substore.*','org_store.store_name')
                        ->where('substore_name', 'like', $search . '%')
                        ->orWhere('store_name', 'like', $search . '%')
                        ->orderBy($order_column, $order_type)
                        ->offset($start)->limit($length)->get();

        $bin_count =SubStore:: join('org_store','org_substore.store_id','=','org_store.store_id')
                        ->select('org_substore.*','org_store.store_name')
                        ->where('substore_name', 'like', $search . '%')
                        ->orWhere('store_name', 'like', $search . '%')
                ->count();

        return [
            "draw" => $draw,
            "recordsTotal" => $bin_count,
            "recordsFiltered" => $bin_count,
            "data" => $bin_list
        ];
      }
      else{
        return response($this->authorize->error_response(), 401);
      }
    }

    //validate anything based on requirements
    public function validate_data(Request $request) {
      //echo "im here";
        $for = $request->for;
        if ($for == 'duplicate') {
            return response($this->validate_duplicate_substore($request->substore_id,$request->store_name, $request->substore_name));
        }
    }

    //check shipment cterm code code already exists
    private function validate_duplicate_substore($id,$store_name,$substore_name) {
        $substore_name=strtoupper($substore_name);
        $subStore = SubStore::where('substore_name', '=', $substore_name)
                             ->where('store_id','=',$store_name)
                          ->first();
        //  dd($store_name);
        if ($subStore == null) {
            return ['status' => 'success'];
        } else if ($subStore->substore_id == $id) {
            return ['status' => 'success'];
        } else {
            return ['status' => 'error', 'message' => 'Record already exists'];
        }

    }

    public function getSubStoreList(){
        return SubStore::select('substore_id', 'substore_name')
            ->where([['status', '=', 1],])->get();
    }

    public function getSubStoreBinList(){
        return StoreBin::select('store_bin_id', 'store_bin_name')
            ->where([['status', '=', 1],])->get();
    }
}
