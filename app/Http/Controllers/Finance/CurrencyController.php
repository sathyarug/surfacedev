<?php

namespace App\Http\Controllers\Finance;

use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use App\Http\Controllers\Controller;
use App\Models\Finance\Currency;
use App\Models\Org\Supplier;
use App\Libraries\AppAuthorize;
use App\Libraries\CapitalizeAllFields;
use Illuminate\Support\Facades\DB;

class CurrencyController extends Controller
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

      if($type == 'datatable') {
        $data = $request->all();
        return response($this->datatable_search($data));
      }
      else if($type == 'auto') {
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

    //create a shipment term
    public function store(Request $request)
    {
      if($this->authorize->hasPermission('CURRENCY_MANAGE'))//check permission
      {
        $currency = new Currency();
        if ($currency->validate($request->all()))
        {
          $currency->fill($request->all());
          $capitalizeAllFields=CapitalizeAllFields::setCapitalAll($currency);
          $currency->status = 1;
          $currency->save();

          return response([ 'data' => [
            'message' => 'Currency saved successfully',
            'currency' => $currency,
            'status'=>'1'
            ]
          ], Response::HTTP_CREATED );
        }
        else
        {
          $errors = $currency->errors();// failure, get errors
          $errors_str = $currency->errors_tostring();
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
      if($this->authorize->hasPermission('CURRENCY_MANAGE'))//check permission
      {
        $currency = Currency::find($id);
        if($currency == null)
          throw new ModelNotFoundException("Requested currency not found", 1);
        else
          return response([ 'data' => $currency ]);
      }
      else{
        return response($this->authorize->error_response(), 401);
      }
    }


    //update a shipment term
    public function update(Request $request, $id)
    {
      if($this->authorize->hasPermission('CURRENCY_MANAGE'))//check permission
      {
        //$is_exsits_in_company=DB::table('org_company')->where('country_code',$id)->exists();
        $is_exsits_in_supplier=DB::table('org_supplier')->where('currency',$id)->exists();
        $is_exsits_in_company=DB::table('org_company')->where('default_currency',$id)->exists();
        if($is_exsits_in_supplier==true||$is_exsits_in_company==true){
          return response([
            'data' => [
              'status'=>'0',
              'message'=>'Currency already in use.'
              ]
          ]);
        }
        else {
        $currency = Currency::find($id);
        if ($currency->validate($request->all()))
        {

          $currency->fill($request->except('currency_code'));
          $capitalizeAllFields=CapitalizeAllFields::setCapitalAll($currency);
          $currency->save();

          return response([ 'data' => [
            'message' => 'Currency updated successfully',
            'currency' => $currency,
              'status'=>'1'
          ]]);
        }
        else
        {
          $errors = $currency->errors();// failure, get errors
          $errors_str = $currency->errors_tostring();
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
      if($this->authorize->hasPermission('CURRENCY_DELETE'))//check permission
      {
        $is_exsits_in_supplier=DB::table('org_supplier')->where('currency',$id)->exists();
        $is_exsits_in_company=DB::table('org_company')->where('default_currency',$id)->exists();
            if($is_exsits_in_supplier==true||$is_exsits_in_company==true){
            return response([
              'data' => [
                'message' => 'Currency already in use.',
                'status'=>'0'
              ]
            ]);
          }
          else {
        $currency = Currency::where('currency_id', $id)->update(['status' => 0]);
        return response([
          'data' => [
            'message' => 'Currency deactivated successfully.',
            'shipTerm' => $currency,
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
        return response($this->validate_duplicate_code($request->currency_id , $request->currency_code));
      }
    }


    //check shipment cterm code code already exists
    private function validate_duplicate_code($id , $code)
    {
      $currency = Currency::where('currency_code','=',$code)->first();
      if($currency == null){
        return ['status' => 'success'];
      }
      else if($currency->currency_id == $id){
        return ['status' => 'success'];
      }
      else {
        return ['status' => 'error','message' => 'Currency code already exists'];
      }
    }


    //get filtered fields only
    private function list($active = 0 , $fields = null)
    {
      $query = null;
      if($fields == null || $fields == '') {
        $query = Currency::select('*');
      }
      else{
        $fields = explode(',', $fields);
        $query = Currency::select($fields);
        if($active != null && $active != ''){
          $query->where([['status', '=', $active]]);
        }
      }
      return $query->get();
    }


    //search shipment terms for autocomplete
    private function autocomplete_search($search)
  	{
  		$ship_term_lists = Currency::select('currency_id','currency_code')
  		->where([['currency_code', 'like', '%' . $search . '%'],['status','<>','0']]) ->get();
  		return $ship_term_lists;
  	}


    //get searched ship terms for datatable plugin format
    private function datatable_search($data)
    {
      if($this->authorize->hasPermission('CURRENCY_MANAGE'))//check permission
      {
        $start = $data['start'];
        $length = $data['length'];
        $draw = $data['draw'];
        $search = $data['search']['value'];
        $order = $data['order'][0];
        $order_column = $data['columns'][$order['column']]['data'];
        $order_type = $order['dir'];

        $currency_list = Currency::select('*')
        ->where('currency_code'  , 'like', $search.'%' )
        ->orWhere('currency_description'  , 'like', $search.'%' )
        ->orderBy($order_column, $order_type)
        ->offset($start)->limit($length)->get();

        $currency_count = Currency::where('currency_code'  , 'like', $search.'%' )
        ->orWhere('currency_description'  , 'like', $search.'%' )
        ->count();

        return [
            "draw" => $draw,
            "recordsTotal" => $currency_count,
            "recordsFiltered" => $currency_count,
            "data" => $currency_list
        ];
      }
      else{
        return response($this->authorize->error_response(), 401);
      }
    }

}
