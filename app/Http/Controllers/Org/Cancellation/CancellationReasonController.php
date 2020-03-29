<?php

namespace App\Http\Controllers\Org\Cancellation;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Database\Eloquent\ModelNotFoundException;

use App\Http\Controllers\Controller;
use App\Models\Org\Cancellation\CancellationReason;
use App\Libraries\AppAuthorize;
//use App\Libraries\CapitalizeAllFields;

class CancellationReasonController extends Controller
{
    var $authorize = null;

    public function __construct()
    {
      //add functions names to 'except' paramert to skip authentication
      $this->middleware('jwt.verify', ['except' => ['index']]);
      $this->authorize = new AppAuthorize();
    }

    //get cancellation reason list
    public function index(Request $request)
    {
      $type = $request->type;
      if($type == 'datatable')   {
        $data = $request->all();
        return response($this->datatable_search($data));
      }
      else if($type == 'auto')    {
        $search = $request->search;
        $category_code = $request->category_code;
        return response($this->autocomplete_search($search, $category_code));
      }
      else if($type=='reasonforsmv'){
        $search = $request->search;
        return response($this->autocompleteSmvChange_search($search));
      }
      else {
        $active = $request->active;
        $fields = $request->fields;
        return response([
          'data' => $this->list($active , $fields)
        ]);
      }
    }


    //create a cancellation reason
    public function store(Request $request)
    {
      if($this->authorize->hasPermission('CANCEL_REASON_MANAGE'))//check permission
      {
        $cluster = new CancellationReason();
        if($cluster->validate($request->all()))
        {
          $cluster->fill($request->all());
          //$cluster->reason_code=strtoupper($cluster->reason_code);
          $cluster->status = 1;
          //$capitalizeAllFields=CapitalizeAllFields::setCapitalAll($cluster);
          $cluster->save();

          return response([ 'data' => [
            'message' => 'Cancellation reason saved successfully',
            'cluster' => $cluster
            ]
          ], Response::HTTP_CREATED );
        }
        else
        {
          $errors = $cluster->errors();// failure, get errors
          $errors_str = $cluster->errors_tostring();
          return response(['errors' => ['validationErrors' => $errors, 'validationErrorsText' => $errors_str]], Response::HTTP_UNPROCESSABLE_ENTITY);
        }
      }
      else{
        return response($this->authorize->error_response(), 401);
      }
    }


    //get a cancellation reason
    public function show($id)
    {
      if($this->authorize->hasPermission('CANCEL_REASON_MANAGE'))//check permission
      {
        $cluster = CancellationReason::find($id);
        if($cluster == null)
          throw new ModelNotFoundException("Requested cancellation reason not found", 1);
        else
          return response([ 'data' => $cluster ]);
      }
      else{
        return response($this->authorize->error_response(), 401);
      }
    }


    //update a cancellation reason
    public function update(Request $request, $id)
    {
      if($this->authorize->hasPermission('CANCEL_REASON_MANAGE'))//check permission
      {
        $cluster = CancellationReason::find($id);
        if($cluster->validate($request->all()))
        {
          $cluster->fill($request->except('group_code'));
          //$capitalizeAllFields=CapitalizeAllFields::setCapitalAll($cluster);
          $cluster->save();

          return response([ 'data' => [
            'message' => 'Cancellation reason updated successfully',
            'cluster' => $cluster
          ]]);
        }
        else
        {
          $errors = $cluster->errors();// failure, get errors
          $errors_str = $cluster->errors_tostring();
          return response(['errors' => ['validationErrors' => $errors, 'validationErrorsText' => $errors_str]], Response::HTTP_UNPROCESSABLE_ENTITY);
        }
      }
      else{
        return response($this->authorize->error_response(), 401);
      }
    }


    //deactivate a Cluster
    public function destroy($id)
    {
      if($this->authorize->hasPermission('CANCEL_REASON_DELETE'))//check permission
      {
        $reason = CancellationReason::where('reason_id', $id)->update(['status' => 0]);
        return response([
          'data' => [
            'message' => 'Cancellation reason was deactivated successfully.',
            'cluster' => $reason
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
        return response($this->validate_duplicate_code($request->reason_id , $request->reason_code));
      }
    }


    //check Cluster code already exists
    private function validate_duplicate_code($id , $code)
    {
      $reason = CancellationReason::where('reason_code','=',$code)->first();
      if($reason == null){
        return ['status' => 'success'];
      }
      else if($reason->reason_id == $id){
        return ['status' => 'success'];
      }
      else {
        return ['status' => 'error','message' => 'Reason code already exists'];
      }
    }


    //get filtered fields only
    private function list($active = 0 , $fields = null)
    {
      $query = null;
      if($fields == null || $fields == '') {
        $query = CancellationReason::select('*');
      }
      else{
        $fields = explode(',', $fields);
        $query = CancellationReason::select($fields);
        if($active != null && $active != ''){
          $query->where([['status', '=', $active]]);
        }
      }
      return $query->get();
    }

    //search Cluster for autocomplete
    private function autocomplete_search($search, $category_code = null)
  	{
      $reasons = null;
      if($category_code == null || $category_code == false){
        $reasons = CancellationReason::select('reason_id','reason_description')
    		->where([['reason_description', 'like', '%' . $search . '%'],]) ->get();
      }
  		else{
        $reasons = CancellationReason::select('org_cancellation_reason.reason_id','org_cancellation_reason.reason_description')
        ->join('org_cancellation_category', 'org_cancellation_category.category_id', '=', 'org_cancellation_reason.reason_category')
        ->where('org_cancellation_category.category_code', '=', $category_code)
    		->where('reason_description', 'like', '%' . $search . '%')->get();
      }
  		return $reasons;
  	}

    //change reasons for smv change;
    private function autocompleteSmvChange_search($search){

      $reasons_list = CancellationReason::join('org_cancellation_category','org_cancellation_reason.reason_category','=','org_cancellation_category.category_id')
      ->where('org_cancellation_category.category_code','=','SMV_CAN')
      ->where([['org_cancellation_reason.reason_description', 'like', '%' . $search . '%'],])->get();
      return $reasons_list;
    }


    //get searched Clusters for datatable plugin format
    private function datatable_search($data)
    {
      if($this->authorize->hasPermission('CANCEL_REASON_MANAGE'))//check permission
      {
        $start = $data['start'];
        $length = $data['length'];
        $draw = $data['draw'];
        $search = $data['search']['value'];
        $order = $data['order'][0];
        $order_column = $data['columns'][$order['column']]['data'];
        $order_type = $order['dir'];

        $reason_list = CancellationReason::join('org_cancellation_category', 'org_cancellation_category.category_id', '=', 'org_cancellation_reason.reason_category')
    		->select('org_cancellation_reason.*', 'org_cancellation_category.category_description')
    		->where('reason_code','like',$search.'%')
    		->orWhere('reason_description', 'like', $search.'%')
    		->orWhere('category_description', 'like', $search.'%')
    		->orderBy($order_column, $order_type)
    		->offset($start)->limit($length)->get();

    		$reason_count = CancellationReason::join('org_cancellation_category', 'org_cancellation_category.category_id', '=', 'org_cancellation_reason.reason_category')
    		->where('reason_code','like',$search.'%')
    		->orWhere('reason_description', 'like', $search.'%')
    		->orWhere('category_description', 'like', $search.'%')
    		->count();

        return [
            "draw" => $draw,
            "recordsTotal" => $reason_count,
            "recordsFiltered" => $reason_count,
            "data" => $reason_list
        ];
      }
      else{
        return response($this->authorize->error_response(), 401);
      }
    }

}
