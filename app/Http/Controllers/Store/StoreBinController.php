<?php

namespace App\Http\Controllers\Store;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Http\Controllers\Controller;
use App\Models\Store\StoreBin;
use App\Models\Finance\Item\Category;
use Illuminate\Support\Facades\DB;
use App\Libraries\CapitalizeAllFields;
use App\Libraries\AppAuthorize;

class StoreBinController extends Controller {

    var $authorize = null;

    public function __construct()
    {
      //add functions names to 'except' paramert to skip authentication
      $this->middleware('jwt.verify', ['except' => ['index']]);
      $this->authorize = new AppAuthorize();
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request) {
        $type = $request->type;

        if ($type == 'datatable') {
            $data = $request->all();
            return response($this->datatable_search($data));
        } else if ($type == 'auto') {
            $search = $request->search;
            return response($this->autocomplete_search($search));
        } else if ($type == 'getBins') {
            $data = $request->all();
            return response($this->getActiveBins($data));
        }
        else if ($type == 'autoStoreWiseBin') {
            $search = $request->search;
            $subStoreBin=$request->substore_id;
            return response($this->autocomplete_substore_wise_bin_search($search,$subStoreBin));
        }
        else if ($type == 'getCategory') {
            $data = $request->all();
            return response($this->getCategoryList($data));
        } else if ($type == 'getItemCategory') {
            $data = $request->all();
            return response($this->getItemCategory($data['category_id']));
        } else if($type == 'sub-store-bin'){
            return response([ 'data' => $this->loc_stores_bin_list($request->fields) ]);
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
      if($this->authorize->hasPermission('BIN_MANAGE'))//check permission
      {
        $storeBin = new StoreBin();
        if ($storeBin->validate($request->all())) {
            $storeBin->fill($request->all());
            $storeBin->status = 1;
            $capitalizeAllFields=CapitalizeAllFields::setCapitalAll($storeBin);
            $storeBin->save();

            return response(['data' => [
                    'message' => 'Store bin saved successfully',
                    'storeBin' => $storeBin
                ]
                    ], Response::HTTP_CREATED);
      } else {
            $errors = $store->errors(); // failure, get errors
            return response(['errors' => ['validationErrors' => $errors]], Response::HTTP_UNPROCESSABLE_ENTITY);
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
      if($this->authorize->hasPermission('BIN_MANAGE'))//check permission
      {
        $storeBin = StoreBin::find($id);
        $storeBin->store;
        $storeBin->substore;
        if ($storeBin == null)
            throw new ModelNotFoundException("Requested store not found", 1);
        else
            return response(['data' => $storeBin]);
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
      if($this->authorize->hasPermission('BIN_MANAGE'))//check permission
      {
        $storeBin = StoreBin::find($id);
        if ($storeBin->validate($request->all())) {
          $is_exsits_in_bin_alocation=DB::table('org_store_bin_allocation')->where('store_bin_id','=',$id)->exists();
          $is_exists_in_roll_plan=DB::table('store_roll_plan')->where('bin','=',$id)->exists();
          if($is_exsits_in_bin_alocation==true||$is_exists_in_roll_plan==true){
            return response(['data' => [
                    'message' => 'Store Bin Alreday in Use',
                    'status'=>0,
                      ]]);
          }
            $storeBin->fill($request->except('store_bin_name'));
            $capitalizeAllFields=CapitalizeAllFields::setCapitalAll($storeBin);
            $storeBin->save();

            return response(['data' => [
                    'message' => 'Store Bin successfully',
                    'storeBin' => $storeBin,
                    'status'=>1
            ]]);
        } else {
            $errors = $storeBin->errors(); // failure, get errors
            return response(['errors' => ['validationErrors' => $errors]], Response::HTTP_UNPROCESSABLE_ENTITY);
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
      if($this->authorize->hasPermission('BIN_DELETE'))//check permission
      {
        $is_exsits_in_bin_alocation=DB::table('org_store_bin_allocation')->where('store_bin_id','=',$id);
        $is_exists_in_roll_plan=DB::table('store_roll_plan')->where('bin','=',$id);
        if($is_exsits_in_bin_alocation==true||$is_exists_in_roll_plan==true){
          return response([
              'data' => [
                  'message' => 'Store Bin Alreday in Use',
                  'status'=>0,
              ]
          ]);
        }
        $storeBin = StoreBin::where('store_bin_id', $id)->update(['status' => 0]);
        return response([
            'data' => [
                'message' => 'Store Bin is deactivated successfully.',
                'store' => $storeBin,
                'status'=>1,
            ]
        ]);
      }
      else{
        return response($this->authorize->error_response(), 401);
      }
    }

    //get filtered fields only
    private function list($active = 0, $fields = null) {
        $query = null;
        if ($fields == null || $fields == '') {
            $query = StoreBin::select('*');
        } else {
            $fields = explode(',', $fields);
            $query = StoreBin::select($fields);
            if ($active != null && $active != '') {
                $query->where([['status', '=', $active]]);
            }
        }
        return $query->get();
    }

    private function loc_stores_bin_list($fields)
    { 
      $fields = explode(',', $fields);
      $query = StoreBin::select('store_bin_id','store_bin_name')
      ->where('org_store_bin.store_id' ,'=', $fields[0])
      ->where('org_store_bin.substore_id' ,'=', $fields[1])
      ->get();
      return $query;
    }

    //search goods types for autocomplete
    private function autocomplete_search($search) {
        $bin_list = StoreBin::select('store_bin_id', 'store_bin_name')
        ->where([['store_bin_name', 'like', '%' . $search . '%'],])->get();
        return $bin_list;
    }


    //search bin  related to the subStoreBin auto complete
    private function autocomplete_substore_wise_bin_search($search,$subStoreBin) {
      //dd("dadadada");
        $bin_list = DB::table('org_store_bin')->select('*')
                        ->where([['store_bin_name', 'like', '%' . $search . '%'],])
                        ->where('substore_id','=',$subStoreBin)
                        ->where('status','=',1)->get();
        return $bin_list;
    }
    //get searched goods types for datatable plugin format
    private function datatable_search($data)
    {
      if($this->authorize->hasPermission('BIN_MANAGE'))//check permission
      {
        $start = $data['start'];
        $length = $data['length'];
        $draw = $data['draw'];
        $search = $data['search']['value'];
        $order = $data['order'][0];
        $order_column = $data['columns'][$order['column']]['data'];
        $order_type = $order['dir'];

        $payload = auth()->payload();
        $locId = $payload->get('loc_id');
        //print_r($payload); exit;
        $bin_list = StoreBin::select('org_store_bin.*', 'org_substore.substore_name', 'org_store.store_name')
                        ->join('org_substore', 'org_substore.substore_id', '=', 'org_store_bin.substore_id')
                        ->join('org_store',function($join) use ($locId)
                        {
                            $join->on('org_store.store_id', '=', 'org_store_bin.store_id');
                            $join->on('org_store.loc_id', '=', DB::raw($locId) );
                        })

                        ->where([['store_bin_name', 'like', "%$search%"]])
                        ->orWhere([['store_bin_description', 'like', "%$search%"]])
                        ->orWhere([['org_substore.substore_name', 'like', "%$search%"]])
                        ->orderBy($order_column, $order_type)
                        ->offset($start)->limit($length)->get();

        //$bin_count = StoreBin::where('store_bin_name', 'like', $search . '%')
                //->count();
        $bin_count =  StoreBin::select('org_store_bin.*', 'org_substore.substore_name', 'org_store.store_name')
                        ->join('org_substore', 'org_substore.substore_id', '=', 'org_store_bin.substore_id')
                        ->join('org_store',function($join) use ($locId)
                        {
                            $join->on('org_store.store_id', '=', 'org_store_bin.store_id');
                            $join->on('org_store.loc_id', '=', DB::raw($locId) );
                        })

                        ->where([['store_bin_name', 'like', "%$search%"]])
                        ->orWhere([['store_bin_description', 'like', "%$search%"]])
                        ->orWhere([['org_substore.substore_name', 'like', "%$search%"]])
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
        $for = $request->for;
        if ($for == 'duplicate') {
            return response($this->validate_duplicate_bin($request->id, $request->store_name,$request->substore_name,$request->store_bin_name));
        }
    }

    //check shipment cterm code code already exists
    private function validate_duplicate_bin($id, $store_name,$substore_name,$store_bin_name) {


        $bin = StoreBin::where([['store_id', '=', $store_name],['substore_id','=',$substore_name],['store_bin_name','=',$store_bin_name]])->first();
        if( $bin == null){
          echo json_encode(array('status' => 'success'));
        }
        else if($bin->store_bin_id == $id){
          echo json_encode(array('status' => 'success'));
        }
        else {
          echo json_encode(array('status' => 'error','message' => 'Record already exists'));
        }


    }

    public function getBinListByLoc(Request $request){
        $bin_list = StoreBin::select('store_bin_id', 'store_bin_name')->where('store_id', $request->id)->get();
        //$bin_list = StoreBin::select('store_bin_id','store_bin_name')->where([['store_id', '=',  $request->id],])->get();
        $bins = $bin_list->toArray();
        return response([
            'data' => $bins
        ]);
    }

    private function getActiveBins($data) {
        $bin_list = StoreBin::select('org_store_bin.*', 'org_substore.substore_name', 'org_store.store_name','org_store_bin_allocation.allocation_id')
            ->join('org_substore', 'org_substore.substore_id', '=', 'org_store_bin.substore_id')
            ->join('org_store', 'org_store.store_id', '=', 'org_store_bin.store_id')
            ->leftJoin('org_store_bin_allocation', 'org_store_bin_allocation.store_bin_id', '=', 'org_store_bin.store_bin_id')
            ->where(
            [
                ['org_store_bin.status', '=', '1'],
                ['org_substore.substore_id', '=', $data['substoreId']],
                ['org_store.store_id', '=', $data['storeId']],
            ])->get();

        $binArray= array();
        foreach($bin_list as $bin) {
            $binArray[$bin->store_bin_id] = $bin;
        }
        return [
            "data" => array_values($binArray)
        ];
    }


    private function getCategoryList() {
        return Category::select('item_category.*')
                ->where('item_category.status', '=', '1')
                ->orderBy('item_category.category_name', 'ASC')->get();
    }

    private function getItemCategory($categoryId) {
        return [
            "data" => Category::getItemListByCategory($categoryId)
        ];
    }

}
