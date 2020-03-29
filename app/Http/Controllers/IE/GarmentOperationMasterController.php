<?php

namespace App\Http\Controllers\IE;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Database\Eloquent\ModelNotFoundException;

use App\Http\Controllers\Controller;
use App\Models\IE\GarmentOperationMaster;
use Exception;
use App\Libraries\CapitalizeAllFields;
use Illuminate\Support\Facades\DB;

class GarmentOperationMasterController extends Controller
{
    public function __construct()
    {
      //add functions names to 'except' paramert to skip authentication
      $this->middleware('jwt.verify', ['except' => ['index']]);
    }

    //get Service Type list
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


    //create a Service Type
    public function store(Request $request)
    {
      $garmentOperation = new GarmentOperationMaster();
      if($garmentOperation->validate($request->all()))
      {
        $garmentOperation->fill($request->all());
        $capitalizeAllFields=CapitalizeAllFields::setCapitalAll($garmentOperation);
        $garmentOperation->status = 1;
        $garmentOperation->save();

        return response([ 'data' => [
          'message' => 'Garment Operation Saved successfully',
          'garmentOperation' => $garmentOperation,
          'status'=>'1'
          ]
        ], Response::HTTP_CREATED );
      }
      else
      {
          $errors = $garmentOperation->errors();// failure, get errors
          $errors_str = $garmentOperation->errors_tostring();
          return response(['errors' => ['validationErrors' => $errors, 'validationErrorsText' => $errors_str]], Response::HTTP_UNPROCESSABLE_ENTITY);
      }
    }


    //get a Service Type
    public function show($id)
    {

      $garmentOperation = GarmentOperationMaster::find($id);
      if($garmentOperation == null)
        throw new ModelNotFoundException("Requested Garment Operation Not Found", 1);
      else
        return response([ 'data' => $garmentOperation ]);
    }






    //deactivate a Service Type
    public function destroy($id)
    {
      $is_exsits=DB::table('ie_component_smv_details')->where('garment_operation_id','=',$id)->exists();
      if($is_exsits==true){
        return response([
          'data' => [
            'message' => 'Garment Operation is Already In use.',
            'status'=>'0'
          ]
        ]);
      }
      $garmentOperation = GarmentOperationMaster::where('garment_operation_id', $id)->update(['status' => 0]);
      return response([
        'data' => [
          'message' => 'Garment Operation Was Deactivated Successfully.',
          'garmentOperation' => $garmentOperation,
          'status'=>'1'
        ]
      ]);
    }


    //validate anything based on requirements
    public function validate_data(Request $request){
      $for = $request->for;
      if($for == 'duplicate')
      {
        return response($this->validate_duplicate_code($request->garment_operation_id , $request->garment_operation_name));
      }
    }


    //check Service Type code already exists
    private function validate_duplicate_code($id , $code)
    {
      $garmentOperation = GarmentOperationMaster::where('garment_operation_name','=',$code)->first();
      if($garmentOperation == null){
        return ['status' => 'success'];
      }
      else if($garmentOperation->garment_operation_id == $id){
        return ['status' => 'success'];
      }
      else {
        return ['status' => 'error','message' => 'Garment Operation Already Exists'];
      }
    }


    //get filtered fields only
    private function list($active = 0 , $fields = null)
    {
      $query = null;
      if($fields == null || $fields == '') {
        $query = GarmentOperationMaster::select('*');
      }
      else{
        $fields = explode(',', $fields);
        $query = GarmentOperationMaster::select($fields);
        if($active != null && $active != ''){
          $query->where([['status', '=', $active]]);
        }
      }
      return $query->get();
    }

    //search Service Type for autocomplete
    private function autocomplete_search($search)
  	{
  		$garment_operation_lists = GarmentOperationMaster::where([['garment_operation_name', 'like', '%' . $search . '%'],])
       ->where('status','1')
      ->pluck('garment_operation_name')
      ->toArray();
  		return  json_encode($garment_operation_lists);
  	}


    //get searched Service Types for datatable plugin format
    private function datatable_search($data)
    {
      $start = $data['start'];
      $length = $data['length'];
      $draw = $data['draw'];
      $search = $data['search']['value'];
      $order = $data['order'][0];
      $order_column = $data['columns'][$order['column']]['data'];
      $order_type = $order['dir'];

      $garment_operation_list = GarmentOperationMaster::select('*')
      ->where('garment_operation_name'  , 'like', $search.'%' )
      ->orderBy($order_column, $order_type)
      ->offset($start)->limit($length)->get();

      $garment_operation_count = GarmentOperationMaster::where('garment_operation_name'  , 'like', $search.'%' )
      ->count();

      return [
          "draw" => $draw,
          "recordsTotal" => $garment_operation_count,
          "recordsFiltered" => $garment_operation_count,
          "data" => $garment_operation_list
      ];
    }

}
