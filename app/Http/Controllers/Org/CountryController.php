<?php

namespace App\Http\Controllers\Org;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;

use App\Http\Controllers\Controller;
use App\Models\Org\Country;
use App\Http\Resources\Org\CountryResource;
use App\Libraries\AppAuthorize;
use Illuminate\Support\Facades\DB;
use App\Libraries\CapitalizeAllFields;

class CountryController extends Controller
{
    var $authorize = null;

    public function __construct()
    {
      //add functions names to 'except' paramert to skip authentication
      $this->middleware('jwt.verify', ['except' => ['index']]);
      $this->authorize = new AppAuthorize();
    }

    //Display a listing of the resource.
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
         return response([
           'data' => $this->fng_country($master_id)
         ]);
       }
       else if($type == 'handsontable')    {
         $search = $request->search;
         return response([
           'data' => $this->handsontable_search($search)
         ]);
       }
       else if($type == 'country_selector'){
         $search = $request->search;
         return response([
           'data' => $this->country_selector_list($search)
         ]);
       }
       else{
         return response([]);
       }
    }


    //create new country
    public function store(Request $request)
    {
      if($this->authorize->hasPermission('COUNTRY_MANAGE'))//check permission
      {
        $country = new Country();
        if($country->validate($request->all()))
        {
          $country->fill($request->all());
          //$capitalizeAllFields=CapitalizeAllFields::setCapitalAll($country);
          $country->status = 1;
          $country->save();
          return response([
            'data' => [
              'message' => 'Country saved successfully',
              'country' => $country,
              'status'=>'1'
            ]
          ] , Response::HTTP_CREATED );

        }
        else{
          $errors = $country->errors();// failure, get errors
          $errors_str = $country->errors_tostring();
          return response(['errors' => ['validationErrors' => $errors, 'validationErrorsText' => $errors_str]], Response::HTTP_UNPROCESSABLE_ENTITY);
        }
      }
      else{
        return response($this->authorize->error_response(), 401);
      }
    }


    //get new country
    public function show($id)
    {
      if($this->authorize->hasPermission('COUNTRY_MANAGE'))//check permission
      {
        $country = Country::find($id);
        if($country == null)
          return response( ['data' => 'Requested country not found'] , Response::HTTP_NOT_FOUND );
        else
          return response( ['data' => $country] );
      }
      else{
        return response($this->authorize->error_response(), 401);
      }
    }


    //update country
    public function update(Request $request, $id)
    {
      if($this->authorize->hasPermission('COUNTRY_MANAGE'))//check permission
      {
        $country = Country::find($id);
        if($country->validate($request->all()))
        {
          $is_exsits_in_company=DB::table('org_company')->where('country_code',$id)->exists();
          $is_exsits_in_location=DB::table('org_location')->where('country_code',$id)->exists();
          $is_exsits_in_customer=DB::table('cust_customer')->where('customer_country',$id)->exists();
          $is_exsits_in_so=DB::table('merc_customer_order_details')->where('country',$id)->exists();
          $is_exsits_in_costing=DB::table('costing_finish_good_component_items')->where('country_id',$id)->exists();
          $is_exsits_in_supplier=DB::table('org_supplier')->where('supplier_country',$id)->exists();

          if($is_exsits_in_company==true||$is_exsits_in_location==true||$is_exsits_in_customer==true||$is_exsits_in_so==true||$is_exsits_in_costing==true||$is_exsits_in_supplier==true){

            return response([
              'data' => [
                'message' => 'Country Already In use',
                'status' => '0'
              ]
            ]);
          }
            else{
              $country->fill($request->except('country_code'));
              //$capitalizeAllFields=CapitalizeAllFields::setCapitalAll($country);
              $country->save();
          return response([
            'data' => [
              'message' => 'Country updated successfully',
              'country' => $country,
              'status'=>'1'
            ]
          ]);
        }
      }
        else
        {
          $errors = $country->errors();// failure, get errors
          $errors_str = $country->errors_tostring();
          return response(['errors' => ['validationErrors' => $errors, 'validationErrorsText' => $errors_str]], Response::HTTP_UNPROCESSABLE_ENTITY);
        }
      }
      else{
        return response($this->authorize->error_response(), 401);
      }
    }


    //deactivate a country
    public function destroy($id)
    {
      if($this->authorize->hasPermission('COUNTRY_DELETE'))//check permission
      {
        $is_exsits_in_company=DB::table('org_company')->where('country_code',$id)->exists();
        $is_exsits_in_location=DB::table('org_location')->where('country_code',$id)->exists();
        $is_exsits_in_customer=DB::table('cust_customer')->where('customer_country',$id)->exists();
        $is_exsits_in_so=DB::table('merc_customer_order_details')->where('country',$id)->exists();
        $is_exsits_in_costing=DB::table('costing_finish_good_component_items')->where('country_id',$id)->exists();
        $is_exsits_in_supplier=DB::table('org_supplier')->where('supplier_country',$id)->exists();

        if($is_exsits_in_company==true||$is_exsits_in_location==true||$is_exsits_in_customer==true||$is_exsits_in_so==true||$is_exsits_in_costing==true||$is_exsits_in_supplier==true){

          return response([
            'data' => [
              'message' => 'Country Already In use',
              'status' => '0'
            ]
          ]);
        }

          else{
        $country = Country::where('country_id', $id)->update(['status' => 0]);
        return response([
          'data' => [
            'message' => 'Country was deactivated successfully.',
            'country' => $country,
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
          return response($this->validate_duplicate_code($request->country_id , $request->country_code));
        }

    }


    //check country code
    public function validate_duplicate_code($country_id , $country_code)
    {
      $country = Country::where('country_code','=',$country_code)->first();
      if($country == null){
        return ['status' => 'success'];
      }
      else if($country->country_id == $country_id){
        return ['status' => 'success'];
      }
      else {
        return ['status' => 'error','message' => 'Country code already exists'];
      }
    }


    //search countries for autocomplete
    private function autocomplete_search($search)
  	{
  		$country_lists = Country::select('country_id','country_code','country_description')
  		->where([['country_description', 'like', '%' . $search . '%'],['status','<>',0]])
      ->get();
  		return $country_lists;
  	}


    public function fng_country($master_id){

      //dd($master_id);

      $fng_country_lists =  DB::table('bom_header')
                      ->select('org_country.country_id','org_country.country_description')
                      ->join('org_country', 'bom_header.country_id', '=', 'org_country.country_id')
                      ->where('bom_header.fng_id' , '=', $master_id )
                      ->get();

      

      return $fng_country_lists;
    }



    //get searched countries for datatable plugin format
    private function datatable_search($data)
    {
      if($this->authorize->hasPermission('COUNTRY_MANAGE'))//check permission
      {
        $start = $data['start'];
        $length = $data['length'];
        $draw = $data['draw'];
        $search = $data['search']['value'];
        $order = $data['order'][0];
        $order_column = $data['columns'][$order['column']]['data'];
        $order_type = $order['dir'];

        $country_list = Country::select('*')
        ->where('country_code'  , 'like', $search.'%' )
        ->orWhere('country_description'  , 'like', $search.'%' )
        ->orderBy($order_column, $order_type)
        ->offset($start)->limit($length)->get();

        $country_count = Country::where('country_code'  , 'like', $search.'%' )
        ->orWhere('country_description'  , 'like', $search.'%' )
        ->count();

        return [
            "draw" => $draw,
            "recordsTotal" => $country_count,
            "recordsFiltered" => $country_count,
            "data" => $country_list
        ];
      }
      else{
        return response($this->authorize->error_response(), 401);
      }
    }


    private function handsontable_search($search){
      $list = Country::where('country_description', 'like', $search.'%' )
      ->where('status', '=', 1)->get()->pluck('country_description');
      return $list;
    }


    private function country_selector_list($search){
      $list = Country::select('country_id', 'country_code', 'country_description')
      ->where('country_code', 'like', '%' . $search . '%')
      ->orWhere('country_description', 'like', '%' . $search . '%')
      ->get();
      return $list;
    }
}
