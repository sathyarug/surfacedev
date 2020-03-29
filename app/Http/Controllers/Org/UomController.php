<?php

namespace App\Http\Controllers\Org;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\DB;
use App\Libraries\CapitalizeAllFields;

use App\Http\Controllers\Controller;
use App\Models\Org\UOM;

use App\Libraries\AppAuthorize;

class UomController extends Controller
{
    var $authorize = null;

    public function __construct()
    {
      //add functions names to 'except' paramert to skip authentication
      $this->middleware('jwt.verify', ['except' => ['index']]);
      $this->authorize = new AppAuthorize();
    }

    //get UOM list
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

      else if($type == 'auto_2')    {
        $master_id = $request->master_id;
        $category = $request->category;
        return response([
          'data' => $this->mat_uom_list($master_id,$category)
        ]);
      }

      else if($type == 'auto_3')    {
        //$master_id = $request->master_id;
        return response([
          'data' => $this->Cuttable_uom_list()
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


    //create a UOM
    public function store(Request $request)
    {
      if($this->authorize->hasPermission('UOM_MANAGE'))//check permission
      {
        $uom = new UOM();
        if($uom->validate($request->all()))
        {
          $uom->fill($request->all());
          //$capitalizeAllFields=CapitalizeAllFields::setCapitalAll($uom);
          $uom->uom_code=$request->uom_code;
          $uom->status = 1;
          $uom->save();

          return response([ 'data' => [
            'message' => 'UOM saved successfully',
            'uom' => $uom
            ]
          ], Response::HTTP_CREATED );
        }
        else
        {
          $errors = $uom->errors();// failure, get errors
          $errors_str = $uom->errors_tostring();
          return response(['errors' => ['validationErrors' => $errors, 'validationErrorsText' => $errors_str]], Response::HTTP_UNPROCESSABLE_ENTITY);
        }
      }
      else{
        return response($this->authorize->error_response(), 401);
      }
    }


    //get a UOM
    public function show($id)
    {
      if($this->authorize->hasPermission('UOM_MANAGE'))//check permission
      {
        $uom = UOM::find($id);
        if($uom == null)
          throw new ModelNotFoundException("Requested UOM not found", 1);
        else
          return response([ 'data' => $uom ]);
      }
      else{
        return response($this->authorize->error_response(), 401);
      }
    }


    //update a UOM
    public function update(Request $request, $id)
    {
      if($this->authorize->hasPermission('UOM_MANAGE'))//check permission
      {
        $is_exists = DB::table('item_uom')->where('uom_id', $id)->exists();
        if($is_exists){ // uom already used in item master table
          return response([
            'data' => [
              'status' => 'error',
              'message' => 'Cannot deactivate UOM. Already use in item creation.'
            ]
          ] , 200);
        }
        else{
          $uom = UOM::find($id);
          if($uom->validate($request->all()))
          {
            $uom->fill($request->except('uom_code'));
            //$capitalizeAllFields=CapitalizeAllFields::setCapitalAll($uom);
            $uom->uom_code=$request->uom_code;
            $uom->save();

            return response([ 'data' => [
              'status' => 'success',
              'message' => 'UOM updated successfully',
              'uom' => $uom
            ]]);
          }
          else
          {
            $errors = $uom->errors();// failure, get errors
            $errors_str = $uom->errors_tostring();
            return response(['errors' => ['validationErrors' => $errors, 'validationErrorsText' => $errors_str]], Response::HTTP_UNPROCESSABLE_ENTITY);
          }
        }
      }
      else{
        return response($this->authorize->error_response(), 401);
      }
    }


    //deactivate a UOM
    public function destroy($id)
    {
      if($this->authorize->hasPermission('UOM_DELETE'))//check permission
      {
        $is_exists = DB::table('item_uom')->where('uom_id', $id)->exists();
        if($is_exists){ // uom already used in item master table
          return response([
            'data' => [
              'status' => 'error',
              'message' => 'Cannot deactivate UOM. Already use in item creation.'
            ]
          ] , 200);
        }
        else{
          $uom = UOM::where('uom_id', $id)->update(['status' => 0]);
          return response([
            'data' => [
              'status' => 'success',
              'message' => 'UOM was deactivated successfully.',
              'uom' => $uom
            ]
          ] , Response::HTTP_NO_CONTENT);
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
        return response($this->validate_duplicate_code($request->uom_id , $request->uom_code));
      }
    }


    //check UOM code already exists
    private function validate_duplicate_code($id , $code)
    {
      $uom = UOM::where('uom_code','=',$code)->first();
      if($uom == null){
        return ['status' => 'success'];
      }
      else if($uom->uom_id == $id){
        return ['status' => 'success'];
      }
      else {
        return ['status' => 'error','message' => 'UOM code already exists'];
      }
    }


    public function mat_uom_list($master_id,$category){
      //dd($category);

    $uom_lists =  DB::table('item_uom')
                      ->select('org_uom.uom_id','org_uom.uom_code')
                      ->join('org_uom', 'item_uom.uom_id', '=', 'org_uom.uom_id')
                      ->where('item_uom.master_id' , '=', $master_id )
                      ->where('org_uom.status' , '<>', 0 );
    if ($category == "yd")
    {
        $uom_lists->where('org_uom.uom_code' , '=', "yd" );
    }

      $load_list = $uom_lists->get();

      return $load_list;
    }

    public function Cuttable_uom_list(){

    $uom_lists =  DB::table('org_uom')
                      ->select('org_uom.uom_id','org_uom.uom_code')
                      ->where('org_uom.cuttable' , '=', 1 )
                      ->where('org_uom.status' , '<>', 0 )
                      ->get();

      return $uom_lists;
    }



    //get filtered fields only
    private function list($active = 0 , $fields = null)
    {
      $query = null;
      if($fields == null || $fields == '') {
        $query = UOM::select('*');
      }
      else{
        $fields = explode(',', $fields);
        $query = UOM::select($fields);
        if($active != null && $active != ''){
          $query->where([['status', '=', $active]]);
        }
      }
      return $query->get();
    }

    //search UOM for autocomplete
    private function autocomplete_search($search)
  	{
  		$uom_lists = UOM::select('uom_id','uom_code')
      ->where('status', '=', 1)
  		->where([['uom_code', 'like', '%' . $search . '%'],]) ->get();
  		return $uom_lists;
  	}




    //get searched UOMs for datatable plugin format
    private function datatable_search($data)
    {
      if($this->authorize->hasPermission('UOM_MANAGE'))//check permission
      {
        $start = $data['start'];
        $length = $data['length'];
        $draw = $data['draw'];
        $search = $data['search']['value'];
        $order = $data['order'][0];
        $order_column = $data['columns'][$order['column']]['data'];
        $order_type = $order['dir'];

        $uom_list = UOM::select('*')
        ->where('uom_code'  , 'like', $search.'%' )
        ->orWhere('uom_description'  , 'like', $search.'%' )
        ->orderBy($order_column, $order_type)
        ->offset($start)->limit($length)->get();

        $uom_count = UOM::where('uom_code'  , 'like', $search.'%' )
        ->orWhere('uom_description'  , 'like', $search.'%' )
        ->count();

        return [
            "draw" => $draw,
            "recordsTotal" => $uom_count,
            "recordsFiltered" => $uom_count,
            "data" => $uom_list
        ];
      }
      else{
        return response($this->authorize->error_response(), 401);
      }
    }

}
