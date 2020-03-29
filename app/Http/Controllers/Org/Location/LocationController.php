<?php

namespace App\Http\Controllers\Org\Location;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\DB;

use App\Http\Controllers\Controller;
use App\Models\Org\Location\Location;
use App\Models\Merchandising\CustomerOrderDetails;
use App\Models\Finance\Accounting\CostCenter;
use App\Libraries\AppAuthorize;
use App\Libraries\CapitalizeAllFields;

class LocationController extends Controller
{

    var $authorize = null;

    public function __construct()
    {
      //add functions names to 'except' paramert to skip authentication
      $this->middleware('jwt.verify', ['except' => ['index']]);
      $this->authorize = new AppAuthorize();
    }

    //get Location list
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


    //create a Location
    public function store(Request $request)
    {
      if($this->authorize->hasPermission('LOC_MANAGE'))//check permission
      {
        $location = new Location();
        if($location->validate($request->all()))
        {
          $location->fill($request->all());
          //$capitalizeAllFields=CapitalizeAllFields::setCapitalAll($location);
          $location->loc_email=$request->loc_email;
          $location->loc_web=$request->loc_web;
          $location->loc_google=$request->loc_google;
  				$location->status = 1;
  				$location->created_by = 1;
  				$result = $location->saveOrFail();
  				$insertedId = $location->loc_id;

  				DB::table('org_location_cost_centers')->where('loc_id', '=', $insertedId)->delete();
  				$cost_centers = $request->get('cost_centers');
  				$save_cost_centers = array();
  				if($cost_centers != '') {
  		  		foreach($cost_centers as $cost_center)		{
  						array_push($save_cost_centers,CostCenter::find($cost_center['cost_center_id']));
  					}
  				}
  				$location->costCenters()->saveMany($save_cost_centers);

          return response([ 'data' => [
            'message' => 'Location saved successfully',
            'location' => $location
            ]
          ], Response::HTTP_CREATED );
        }
        else
        {
          $errors = $location->errors();// failure, get errors
          $errors_str = $location->errors_tostring();
          return response(['errors' => ['validationErrors' => $errors, 'validationErrorsText' => $errors_str]], Response::HTTP_UNPROCESSABLE_ENTITY);
        }
      }
      else{
        return response($this->authorize->error_response(), 401);
      }
    }


    //get a Location
    public function show($id)
    {
      if($this->authorize->hasPermission('LOC_MANAGE'))//check permission
      {
        $location = Location::with(['country','currency','costCenters'])->find($id);
        if($location == null)
          throw new ModelNotFoundException("Requested location not found", 1);
        else
          return response([ 'data' => $location ]);
      }
      else{
        return response($this->authorize->error_response(), 401);
      }
    }


    //update a Location
    public function update(Request $request, $id)
    {
    //  dd($request);

      if($this->authorize->hasPermission('LOC_MANAGE'))//check permission
      {
        $location = Location::find($id);
        if($location->validate($request->all()))
        {
          $customer_order = CustomerOrderDetails::where([['delivery_status', '<>', 'CANCEL'],['projection_location','=',$id]])->first();
          if($customer_order != null)
          {
            return response([
              'data'=>[
                'status'=>'0',
              ]
            ]);
          }else{

          $location->fill($request->except('loc_code'));
          //$location->loc_name = strtoupper($location->loc_name);
          $location->opr_start_date=date("Y-m-d", strtotime($request->opr_start_date) );
          $location->save();

          DB::table('org_location_cost_centers')->where('loc_id', '=', $id)->delete();
  				$cost_centers = $request->get('cost_centers');
  				$save_cost_centers = array();
  				if($cost_centers != '') {
  		  		foreach($cost_centers as $cost_center)		{
  						array_push($save_cost_centers,CostCenter::find($cost_center['cost_center_id']));
  					}
  				}
  				$location->costCenters()->saveMany($save_cost_centers);

          return response([ 'data' => [
            'message' => 'Location updated successfully',
            'location' => $location
          ]]);
         }
        }
        else
        {
          $errors = $location->errors();// failure, get errors
          $errors_str = $location->errors_tostring();
          return response(['errors' => ['validationErrors' => $errors, 'validationErrorsText' => $errors_str]], Response::HTTP_UNPROCESSABLE_ENTITY);
        }
      }
      else{
        return response($this->authorize->error_response(), 401);
      }
    }


    //deactivate a Location
    public function destroy($id)
    {
      if($this->authorize->hasPermission('LOC_DELETE'))//check permission
      {
        $customer_order = CustomerOrderDetails::where([['delivery_status', '<>', 'CANCEL'],['projection_location','=',$id]])->first();
        if($customer_order != null)
        {
          return response([
            'data'=>[
              'status'=>'0',
            ]
          ]);
        }else{
        $location = Location::where('loc_id', $id)->update(['status' => 0]);
        return response([
          'data' => [
            'message' => 'Location deactivated successfully.',
            'location' => $location
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
        return response($this->validate_duplicate_code($request->Location_id , $request->Location_code));
      }
    }


    //check Location code already exists
    private function validate_duplicate_code($id , $code)
    {
      $location = Location::where('loc_code','=',$code)->first();
      if($location == null){
        return ['status' => 'success'];
      }
      else if($location->Location_id == $id){
        return ['status' => 'success'];
      }
      else {
        return ['status' => 'error','message' => 'Location code already exists'];
      }
    }


    //get filtered fields only
    private function list($active = 0 , $fields = null)
    {

      $query = null;
      if($fields == null || $fields == '') {
        $query = Location::select('*')
        ->where('status','=',$active);
      }
      else{
        $fields = explode(',', $fields);
        $query = Location::select($fields);
        if($active != null && $active != ''){
          $query->where([['status', '=', 1]]);
        }
        $query = Location::select('*')
        ->where('status','=',1);
      }
        //dd($query->get());
      return $query->get();
    }

    //search Location for autocomplete
    private function autocomplete_search($search)
  	{
  		$location_lists = Location::select('loc_id','loc_name')
  		->where([['loc_name', 'like', '%' . $search . '%'],])
      ->where('status','=',1) ->get();
  		return $location_lists;
  	}


    //get searched Locations for datatable plugin format
    private function datatable_search($data)
    {
      if($this->authorize->hasPermission('LOC_MANAGE'))//check permission
      {
        $start = $data['start'];
        $length = $data['length'];
        $draw = $data['draw'];
        $search = $data['search']['value'];
        $order = $data['order'][0];
        $order_column = $data['columns'][$order['column']]['data'];
        $order_type = $order['dir'];

        $location_list = Location::join('org_company', 'org_location.company_id', '=', 'org_company.company_id')
        ->join('fin_currency', 'org_location.currency_code', '=', 'fin_currency.currency_id')
        ->join('org_country', 'org_location.country_code', '=', 'org_country.country_id')
    		->select('org_location.*', 'org_company.company_name','fin_currency.currency_code','org_country.country_description')
    		->where('loc_code','like',$search.'%')
    		->orWhere('loc_name', 'like', $search.'%')
    		->orWhere('company_name', 'like', $search.'%')
    		->orderBy($order_column, $order_type)
    		->offset($start)->limit($length)->get();

    		$location_count = Location::join('org_company', 'org_location.company_id', '=', 'org_company.company_id')
        ->join('fin_currency', 'org_location.currency_code', '=', 'fin_currency.currency_id')
        ->select('org_location.*', 'org_company.company_name')
    		->where('loc_code','like',$search.'%')
    		->orWhere('loc_name', 'like', $search.'%')
    		->orWhere('company_name', 'like', $search.'%')
    		->count();
        return [
            "draw" => $draw,
            "recordsTotal" => $location_count,
            "recordsFiltered" => $location_count,
            "data" => $location_list
        ];
      }
      else{
        return response($this->authorize->error_response(), 401);
      }
    }

}
