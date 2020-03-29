<?php

namespace App\Http\Controllers\Finance;

use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Database\Eloquent\ModelNotFoundException;

use App\Http\Controllers\Controller;
use App\Models\Finance\ExchangeRate;
use App\Libraries\AppAuthorize;
use Illuminate\Support\Facades\DB;
class ExchangeRateController extends Controller
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
      if($this->authorize->hasPermission('EXCHANGE_RATE_MANAGE'))//check permission
      {
        $rate = new ExchangeRate();
        if ($rate->validate($request->all()))
        {
          $rate->fill($request->all());
          $rate->status = 1;
          $rate->save();

          return response([ 'data' => [
            'message' => 'Exchange rate saved successfully',
            'exchangeRate' => $rate
            ]
          ], Response::HTTP_CREATED );
        }
        else
        {
          $errors = $rate->errors();// failure, get errors
          $errors_str = $rate->errors_tostring();
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
      if($this->authorize->hasPermission('EXCHANGE_RATE_MANAGE'))//check permission
      {
        $rate = ExchangeRate::with(['currency'])->find($id);
        if($rate == null)
          throw new ModelNotFoundException("Requested exchange rate not found", 1);
        else
          return response([ 'data' => $rate ]);
      }
      else{
        return response($this->authorize->error_response(), 401);
      }
    }


    //update a shipment term
    public function update(Request $request, $id)
    {
      if($this->authorize->hasPermission('EXCHANGE_RATE_MANAGE'))//check permission
      {
        $rate = ExchangeRate::find($id);
        if ($rate->validate($request->all()))
        {
          //echo $request->valid_from;
          //echo date("Y-m-d", strtotime($request->valid_from) );
          $rate->fill($request->all());
          $rate->valid_from=date("Y-m-d", strtotime($request->valid_from) );

          $rate->save();

          return response([ 'data' => [
            'message' => 'Exchange rate updated successfully',
            'exchangeRate' => $rate
          ]]);
        }
        else
        {
          $errors = $rate->errors();// failure, get errors
          $errors_str = $rate->errors_tostring();
          return response(['errors' => ['validationErrors' => $errors, 'validationErrorsText' => $errors_str]], Response::HTTP_UNPROCESSABLE_ENTITY);
        }
      }
      else{
        return response($this->authorize->error_response(), 401);
      }
    }

    //deactivate a ship term
    public function destroy($id)
    {
      if($this->authorize->hasPermission('EXCHANGE_RATE_DELETE'))//check permission
      {
        $rate = ExchangeRate::where('id', $id)->update(['status' => 0]);
        return response([
          'data' => [
            'message' => 'Exchange rate deactivated successfully.',
            'exchangeRate' => $rate
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
        return response($this->validate_duplicate($request->id , $request->currency , $request->valid_from));
      }
    }


    //check shipment cterm code code already exists
    private function validate_duplicate($id , $currency , $valid_from)
    {
      $valid_from = date('Y-m-d', strtotime($valid_from));
      $rate = ExchangeRate::where([['currency','=',$currency] , ['valid_from' , '=' , $valid_from]])->first();
      if($rate == null){
        return ['status' => 'success'];
      }
      else if($rate->id == $id){
        return ['status' => 'success'];
      }
      else {
        return ['status' => 'error','message' => 'Exchange rate already exists'];
      }
    }


    //get filtered fields only
    private function list($active = 0 , $fields = null)
    {
      $query = null;
      if($fields == null || $fields == '') {
        $query = ExchangeRate::select('*');
      }
      else{
        $fields = explode(',', $fields);
        $query = ExchangeRate::select($fields);
        if($active != null && $active != ''){
          $query->where([['status', '=', $active]]);
        }
      }
      return $query->get();
    }


    //search shipment terms for autocomplete
    private function autocomplete_search($search)
  	{
  		$ship_term_lists = ExchangeRate::select('ExchangeRate_id','ExchangeRate_code')
  		->where([['ExchangeRate_code', 'like', '%' . $search . '%'],]) ->get();
  		return $ship_term_lists;
  	}


    //get searched ship terms for datatable plugin format
    private function datatable_search($data)
    {
      if($this->authorize->hasPermission('EXCHANGE_RATE_MANAGE'))//check permission
      {
        $start = $data['start'];
        $length = $data['length'];
        $draw = $data['draw'];
        $search = $data['search']['value'];
        $order = $data['order'][0];
        $order_column = $data['columns'][$order['column']]['data'];
        $order_type = $order['dir'];

        $rate_list = ExchangeRate::join('fin_currency' , 'fin_currency.currency_id' , '=' , 'org_exchange_rate.currency')
        ->select(DB::raw("DATE_FORMAT(valid_from, '%d-%b-%Y') 'from_date'"),'org_exchange_rate.*','fin_currency.currency_code','fin_currency.currency_description')
        ->where('currency_code'  , 'like', $search.'%' )
        ->orWhere('valid_from'  , 'like', $search.'%' )
        ->orWhere('rate'  , 'like', $search.'%' )
        ->orderBy($order_column, $order_type)
        ->offset($start)->limit($length)->get();

        $rate_count = ExchangeRate::join('fin_currency' , 'fin_currency.currency_id' , '=' , 'org_exchange_rate.currency')
        ->where('currency_code'  , 'like', $search.'%' )
        ->orWhere('valid_from'  , 'like', $search.'%' )
        ->orWhere('rate'  , 'like', $search.'%' )
        ->count();

        return [
            "draw" => $draw,
            "recordsTotal" => $rate_count,
            "recordsFiltered" => $rate_count,
            "data" => $rate_list
        ];
      }
      else{
        return response($this->authorize->error_response(), 401);
      }
    }

}
