<?php

namespace App\Http\Controllers\Org;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Database\Eloquent\ModelNotFoundException;

use App\Http\Controllers\Controller;
use App\Models\Org\RequestType;
use Exception;
use App\Libraries\CapitalizeAllFields;

class RequestTypeController extends Controller
{
    public function __construct()
    {
      //add functions names to 'except' paramert to skip authentication
      $this->middleware('jwt.verify', ['except' => ['index']]);
    }

    //get RequestType list
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


    //create a RequestType
    public function store(Request $request)
    {
      $requestType = new RequestType();
      if($requestType->validate($request->all()))
      {
        $requestType->fill($request->all());
        $requestType->status = 1;
        //$capitalizeAllFields=CapitalizeAllFields::setCapitalAll($requestType);
        $requestType->save();

        return response([ 'data' => [
          'message' => 'Request Type saved successfully',
          'requestType' => $requestType
          ]
        ], Response::HTTP_CREATED );
      }
      else
      {
        $errors = $requestType->errors();// failure, get errors
        $errors_str = $requestType->errors_tostring();
        return response(['errors' => ['validationErrors' => $errors, 'validationErrorsText' => $errors_str]], Response::HTTP_UNPROCESSABLE_ENTITY);
      }
    }


    //get a Request Type
    public function show($id)
    {
      $requestType = RequestType::find($id);
      if($requestType == null)
        throw new ModelNotFoundException("Requested garment option not found", 1);
      else
        return response([ 'data' => $requestType ]);
    }


    //update a request Type
    public function update(Request $request, $id)
    {
      $requestType = RequestType::find($id);
      if($requestType->validate($request->all()))
      {
        $requestType->fill($request->all());
        //$capitalizeAllFields=CapitalizeAllFields::setCapitalAll($requestType);
        $requestType->save();

        return response([ 'data' => [
          'message' => 'Request Type updated successfully',
          'requestType' => $requestType
        ]]);
      }
      else
      {
        $errors = $requestType->errors();// failure, get errors
        $errors_str = $requestType->errors_tostring();
        return response(['errors' => ['validationErrors' => $errors, 'validationErrorsText' => $errors_str]], Response::HTTP_UNPROCESSABLE_ENTITY);
      }
    }


    //deactivate a Request Type
    public function destroy($id)
    {
      $requestType = RequestType::where('request_type_id', $id)->update(['status' => 0]);
      return response([
        'data' => [
          'message' => 'Request Type deactivated successfully.',
          'RequestType' => $requestType
        ]
      ] , Response::HTTP_NO_CONTENT);
    }


    //validate anything based on requirements
    public function validate_data(Request $request){
      $for = $request->for;
      if($for == 'duplicate')
      {
        return response($this->validate_duplicate_code($request->request_type_id , $request->request_type));
      }
    }


    //check Request Type already exists
    private function validate_duplicate_code($id , $code)
    {
      $requestType = RequestType::where('request_type','=',$code)->first();
      if($requestType == null){
        return ['status' => 'success'];
      }
      else if($requestType->request_type_id == $id){
        return ['status' => 'success'];
      }
      else {
        return ['status' => 'error','message' => 'Request Type already exists'];
      }
    }


    //get filtered fields only
    private function list($active = 0 , $fields = null)
    {
      $query = null;
      if($fields == null || $fields == '') {
        $query = RequestType::select('*');
      }
      else{
        $fields = explode(',', $fields);
        $query = RequestType::select($fields);
        if($active != null && $active != ''){
          $query->where([['status', '=', $active]]);
        }
      }
      return $query->get();
    }

    //search Requset Type for autocomplete
    private function autocomplete_search($search)
  	{
  		$requestType_lists =  RequestType::select('request_type_id','request_type')
  		->where([['request_type', 'like', '%' . $search . '%']])
      ->where('status','=',1)
      ->get();
  		return $requestType_lists;
  	}


    //get searched Requset Type for datatable plugin format
    private function datatable_search($data)
    {
      $start = $data['start'];
      $length = $data['length'];
      $draw = $data['draw'];
      $search = $data['search']['value'];
      $order = $data['order'][0];
      $order_column = $data['columns'][$order['column']]['data'];
      $order_type = $order['dir'];

      $requestType_list = RequestType::select('*')
      ->where('request_type'  , 'like', $search.'%' )
      ->orderBy($order_column, $order_type)
      ->offset($start)->limit($length)->get();

      $requestType_count = RequestType::where('request_type'  , 'like', $search.'%' )
      ->count();

      return [
          "draw" => $draw,
          "recordsTotal" => $requestType_count,
          "recordsFiltered" => $requestType_count,
          "data" => $requestType_list
      ];
    }

}
