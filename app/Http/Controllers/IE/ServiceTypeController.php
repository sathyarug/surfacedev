<?php

namespace App\Http\Controllers\IE;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Database\Eloquent\ModelNotFoundException;

use App\Http\Controllers\Controller;
use App\Models\IE\ServiceType;
use App\Models\Merchandising\BulkCostingDetails;
use Exception;
use App\Libraries\AppAuthorize;
use App\Libraries\CapitalizeAllFields;

class ServiceTypeController extends Controller
{
    var $authorize = null;

    public function __construct()
    {
      //add functions names to 'except' paramert to skip authentication
      $this->middleware('jwt.verify', ['except' => ['index']]);
      $this->authorize = new AppAuthorize();
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
      if($this->authorize->hasPermission('SERVICE_TYPE_MANAGE'))//check permission
      {
        $servicetype = new ServiceType();
        if($servicetype->validate($request->all()))
        {
          $servicetype->fill($request->all());
          $capitalizeAllFields=CapitalizeAllFields::setCapitalAll($servicetype);
          $servicetype->status = 1;
          $servicetype->save();

          return response([ 'data' => [
            'message' => 'Service Type saved successfully',
            'servicetype' => $servicetype,
            'status'=>'1'
            ]
          ], Response::HTTP_CREATED );
        }
        else
        {
          $errors = $servicetype->errors();// failure, get errors
          $errors_str = $servicetype->errors_tostring();
          return response(['errors' => ['validationErrors' => $errors, 'validationErrorsText' => $errors_str]], Response::HTTP_UNPROCESSABLE_ENTITY);
        }
      }
      else{

      }
    }


    //get a Service Type
    public function show($id)
    {
      if($this->authorize->hasPermission('SERVICE_TYPE_MANAGE'))//check permission
      {
        $servicetype = ServiceType::find($id);
        if($servicetype == null)
          throw new ModelNotFoundException("Requested service type not found", 1);
        else
          return response([ 'data' => $servicetype ]);
      }
      else{

      }
    }


    //update a Service Type
    public function update(Request $request, $id)
    {
      if($this->authorize->hasPermission('SERVICE_TYPE_MANAGE'))//check permission
      {
        $bulkCostingFeatureDetails=BulkCostingDetails::where([['process_option','=',$id]])->first();
        if($bulkCostingFeatureDetails!=null){
          return response([
            'data'=>[
              'status'=>'0',
              'message'=>'Service Type Already in use'
            ]
          ]);
        }

        $servicetype = ServiceType::find($id);
        if($servicetype->validate($request->all()))
        {
          $servicetype->fill($request->except('service_type_code'));
          $capitalizeAllFields=CapitalizeAllFields::setCapitalAll($servicetype);
          $servicetype->save();

          return response([ 'data' => [
            'message' => 'Service Type updated successfully',
            'servicetype' => $servicetype,
            'status'=>'1',
          ]]);
        }
        else
        {
          $errors = $servicetype->errors();// failure, get errors
          $errors_str = $servicetype->errors_tostring();
          return response(['errors' => ['validationErrors' => $errors, 'validationErrorsText' => $errors_str]], Response::HTTP_UNPROCESSABLE_ENTITY);
        }
      }
      else{
        return response($this->authorize->error_response(), 401);
      }
    }


    //deactivate a Service Type
    public function destroy($id)
    {
      if($this->authorize->hasPermission('SERVICE_TYPE_DELETE'))//check permission
      {
        $bulkCostingFeatureDetails=BulkCostingDetails::where([['process_option','=',$id]])->first();
        if($bulkCostingFeatureDetails!=null){
          return response([
            'data'=>[
              'status'=>'0',
              'message'=>'Service Type Already in use'
            ]
          ]);
        }

        $servicetype = ServiceType::where('service_type_id', $id)->update(['status' => 0]);
        return response([
          'data' => [
            'message' => 'Service Type was deactivated successfully.',
            'servicetype' => $servicetype
          ]
        ]);
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
        return response($this->validate_duplicate_code($request->service_type_id , $request->service_type_code));
      }
    }


    //check Service Type code already exists
    private function validate_duplicate_code($id , $code)
    {
      $servicetype = ServiceType::where('service_type_code','=',$code)->first();
      if($servicetype == null){
        return ['status' => 'success'];
      }
      else if($servicetype->service_type_id == $id){
        return ['status' => 'success'];
      }
      else {
        return ['status' => 'error','message' => 'Service Type code already exists'];
      }
    }


    //get filtered fields only
    private function list($active = 0 , $fields = null)
    {
      $query = null;
      if($fields == null || $fields == '') {
        $query = ServiceType::select('*');
      }
      else{
        $fields = explode(',', $fields);
        $query = ServiceType::select($fields);
        if($active != null && $active != ''){
          $query->where([['status', '=', $active]]);
        }
      }
      return $query->get();
    }

    //search Service Type for autocomplete
    private function autocomplete_search($search)
  	{
  		$service_type_lists = ServiceType::select('service_type_id','service_type_code')
  		->where([['service_type_code', 'like', '%' . $search . '%'],]) ->get();
  		return $service_type_lists;
  	}


    //get searched Service Types for datatable plugin format
    private function datatable_search($data)
    {
      if($this->authorize->hasPermission('SERVICE_TYPE_MANAGE'))//check permission
      {
        $start = $data['start'];
        $length = $data['length'];
        $draw = $data['draw'];
        $search = $data['search']['value'];
        $order = $data['order'][0];
        $order_column = $data['columns'][$order['column']]['data'];
        $order_type = $order['dir'];

        $service_type_list = ServiceType::select('*')
        ->where('service_type_code'  , 'like', $search.'%' )
        ->orWhere('service_type_description'  , 'like', $search.'%' )
        ->orderBy($order_column, $order_type)
        ->offset($start)->limit($length)->get();

        $service_type_count = ServiceType::where('service_type_code'  , 'like', $search.'%' )
        ->orWhere('service_type_description'  , 'like', $search.'%' )
        ->count();

        return [
            "draw" => $draw,
            "recordsTotal" => $service_type_count,
            "recordsFiltered" => $service_type_count,
            "data" => $service_type_list
        ];
      }
      else{
        return response($this->authorize->error_response(), 401);
      }
    }

}
