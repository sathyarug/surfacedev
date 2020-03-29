<?php

namespace App\Http\Controllers\Finance;

use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Database\Eloquent\ModelNotFoundException;

use App\Http\Controllers\Controller;
use App\Models\Finance\ShipmentTerm;
use App\Libraries\AppAuthorize;
use Illuminate\Support\Facades\DB;
use App\Libraries\CapitalizeAllFields;

class ShipmentTermController extends Controller
{
    var $authorize = null;

    public function __construct()
    {
      //add functions names to 'except' paramert to skip authentication
      $this->middleware('jwt.verify', ['except' => ['index']]);
      $this->authorize = new AppAuthorize();
    }

    //get shipment term list
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
      else if($type == 'handsontable')    {
        $search = $request->search;
        return response([
          'data' => $this->handsontable_search($search)
        ]);
      }
      else{
        $active = $request->active;
        $fields = $request->fields;
        return response([
          'data' => $this->list($active , $fields)
        ]);
      }
    }

    //create a shipment term
    public function store(Request $request)
    {
      if($this->authorize->hasPermission('SHIP_TERM_MANAGE'))//check permission
      {
        $shipTerm = new ShipmentTerm();
        if($shipTerm->validate($request->all()))
        {
          $shipTerm->fill($request->all());
          $capitalizeAllFields=CapitalizeAllFields::setCapitalAll($shipTerm);
          $shipTerm->status = 1;
          $shipTerm->save();

          return response([ 'data' => [
            'message' => 'Shipment term saved successfully',
            'shipTerm' => $shipTerm,
            'status'=>'1'
            ]
          ], Response::HTTP_CREATED );
        }
        else {
          $errors = $shipTerm->errors();// failure, get errors
          $errors_str = $shipTerm->errors_tostring();
          return response(['errors' => ['validationErrors' => $errors, 'validationErrorsText' => $errors_str]], Response::HTTP_UNPROCESSABLE_ENTITY);
        }
      }
      else{
        return response($this->authorize->error_response(), 401);
      }
    }

    //get shipment term
    public function show($id)
    {
      if($this->authorize->hasPermission('SHIP_TERM_MANAGE'))//check permission
      {
        $shipTerm = ShipmentTerm::find($id);
        if($shipTerm == null)
          throw new ModelNotFoundException("Requested shipment term not found", 1);
        else
          return response([ 'data' => $shipTerm ]);
      }
      else{
        return response($this->authorize->error_response(), 401);
      }
    }


    //update a shipment term
    public function update(Request $request, $id)
    {
      if($this->authorize->hasPermission('SHIP_TERM_MANAGE'))//check permission
      {
        $is_exsits_in_supplier=DB::table('org_supplier')->where('ship_terms_agreed',$id)->exists();
        $is_exsits_in_customer=DB::table('cust_customer')->where('ship_terms_agreed',$id)->exists();
        if($is_exsits_in_supplier||$is_exsits_in_customer){
          return response([ 'data' => [
            'message' => 'Shipment term Already in Use',
            'status' => '0'
          ]]);

        }
        else {
          $shipTerm = ShipmentTerm::find($id);
          if($shipTerm->validate($request->all()))
          {
            $shipTerm->fill($request->except('ship_term_code'));
            $capitalizeAllFields=CapitalizeAllFields::setCapitalAll($shipTerm);
            $shipTerm->save();

            return response([ 'data' => [
              'message' => 'Shipment term updated successfully',
              'shipTerm' => $shipTerm,
              'status'=>'1'
            ]]);
          }
          else {
            $errors = $shipTerm->errors();// failure, get errors
            $errors_str = $shipTerm->errors_tostring();
            return response(['errors' => ['validationErrors' => $errors, 'validationErrorsText' => $errors_str]], Response::HTTP_UNPROCESSABLE_ENTITY);
          }
        }
      }
      else{
        return response($this->authorize->error_response(), 401);
      }
    }

    //deactivate a ship term
    public function destroy($id)
    {
      if($this->authorize->hasPermission('SHIP_TERM_DELETE'))//check permission
      {
        $is_exsits_in_supplier=DB::table('org_supplier')->where('ship_terms_agreed',$id)->exists();
        $is_exsits_in_customer=DB::table('cust_customer')->where('ship_terms_agreed',$id)->exists();
        if($is_exsits_in_supplier||$is_exsits_in_customer){
          return response([ 'data' => [
            'message' => 'Shipment term Already in Use',
            'status' => '0'
          ]]);
        }

        else{
        $shipTerm = ShipmentTerm::where('ship_term_id', $id)->update(['status' => 0]);
        return response([
          'data' => [
            'message' => 'Shipment term deactivated successfully.',
            'shipTerm' => $shipTerm,
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
        return response($this->validate_duplicate_code($request->ship_term_id , $request->ship_term_code));
      }
    }


    //check shipment cterm code code already exists
    private function validate_duplicate_code($id , $code)
    {
      $shipTerm = ShipmentTerm::where('ship_term_code','=',$code)->first();
      if($shipTerm == null){
        return ['status' => 'success'];
      }
      else if($shipTerm->ship_term_id == $id){
        return ['status' => 'success'];
      }
      else {
        return ['status' => 'error','message' => 'Ship term code already exists'];
      }
    }


    //get filtered fields only
    private function list($active = 0 , $fields = null)
    {
      $query = null;
      if($fields == null || $fields == '') {
        $query = ShipmentTerm::select('*');
      }
      else{
        $fields = explode(',', $fields);
        $query = ShipmentTerm::select($fields);
        if($active != null && $active != ''){
          $query->where([['status', '=', $active]]);
        }
      }
      return $query->get();
    }


    //search shipment terms for autocomplete
    private function autocomplete_search($search)
  	{
  		$ship_term_lists = ShipmentTerm::select('ship_term_id','ship_term_code')
  		->where([['ship_term_code', 'like', '%' . $search . '%'],]) ->get();
  		return $ship_term_lists;
  	}


    //get searched ship terms for datatable plugin format
    private function datatable_search($data)
    {
      if($this->authorize->hasPermission('SHIP_TERM_MANAGE'))//check permission
      {
        $start = $data['start'];
        $length = $data['length'];
        $draw = $data['draw'];
        $search = $data['search']['value'];
        $order = $data['order'][0];
        $order_column = $data['columns'][$order['column']]['data'];
        $order_type = $order['dir'];

        $ship_term_list = ShipmentTerm::select('*')
        ->where('ship_term_code'  , 'like', $search.'%' )
        ->orWhere('ship_term_description'  , 'like', $search.'%' )
        ->orderBy($order_column, $order_type)
        ->offset($start)->limit($length)->get();

        $ship_term_count = ShipmentTerm::where('ship_term_code'  , 'like', $search.'%' )
        ->orWhere('ship_term_description'  , 'like', $search.'%' )
        ->count();

        return [
            "draw" => $draw,
            "recordsTotal" => $ship_term_count,
            "recordsFiltered" => $ship_term_count,
            "data" => $ship_term_list
        ];
      }
      else{
        return response($this->authorize->error_response(), 401);
      }
    }


    private function handsontable_search($search){
      $list = ShipmentTerm::where('ship_term_description'  , 'like', $search.'%' )
      ->where('status', '=', 1 )->get()->pluck('ship_term_description');
      return $list;
    }


}
