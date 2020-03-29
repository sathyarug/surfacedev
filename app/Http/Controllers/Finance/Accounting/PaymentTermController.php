<?php

namespace App\Http\Controllers\Finance\Accounting;

use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Database\Eloquent\ModelNotFoundException;

use App\Http\Controllers\Controller;
use App\Models\Finance\Accounting\PaymentTerm;
use App\Libraries\AppAuthorize;
use App\Libraries\CapitalizeAllFields;
use Illuminate\Support\Facades\DB;

class PaymentTermController extends Controller
{
    var $authorize = null;

    public function __construct()
    {
      //add functions names to 'except' paramert to skip authentication
      $this->middleware('jwt.verify', ['except' => ['index']]);
      $this->authorize = new AppAuthorize();
    }

    //get Payment Term list
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

    //create a Payment Term
    public function store(Request $request)
    {
      if($this->authorize->hasPermission('PAYMENT_TERM_MANAGE'))//check permission
      {
        $paymentTerm = new PaymentTerm();
        if($paymentTerm->validate($request->all()))
        {
          $paymentTerm->fill($request->all());
          $capitalizeAllFields=CapitalizeAllFields::setCapitalAll($paymentTerm);
          $paymentTerm->status = 1;
          $paymentTerm->save();

          return response([ 'data' => [
            'message' => 'Payment term saved successfully',
            'PaymentTerm' => $paymentTerm,
            'status'=>'1'
            ]
          ], Response::HTTP_CREATED );
        }
        else {
          $errors = $paymentTerm->errors();// failure, get errors
          $errors_str = $paymentTerm->errors_tostring();
          return response(['errors' => ['validationErrors' => $errors, 'validationErrorsText' => $errors_str]], Response::HTTP_UNPROCESSABLE_ENTITY);
        }
      }
      else{
        return response($this->authorize->error_response(), 401);
      }
    }

    //get a Payment Term
    public function show($id)
    {
      if($this->authorize->hasPermission('PAYMENT_TERM_MANAGE'))//check permission
      {
        $paymentTerm = PaymentTerm::find($id);
        if($paymentTerm == null)
          throw new ModelNotFoundException("Requested payment term not found", 1);
        else
          return response( ['data' => $paymentTerm] );
      }
      else{
        return response($this->authorize->error_response(), 401);
      }
    }


    //update a Payment Term
    public function update(Request $request, $id)
    {
      if($this->authorize->hasPermission('PAYMENT_TERM_MANAGE'))//check permission
      {
        $is_exsits_in_supplier=DB::table('org_supplier')->where('payemnt_terms',$id)->exists();
        $is_exsits_in_customer=DB::table('cust_customer')->where('payemnt_terms',$id)->exists();
        if($is_exsits_in_supplier||$is_exsits_in_customer){
          return response([ 'data' => [
            'message' => 'Payment term Already in Use',
            'status' => '0',
          ]]);
        }
        else {
        $paymentTerm = PaymentTerm::find($id);
        if($paymentTerm->validate($request->all()))
        {
          $paymentTerm->fill( $request->except('payment_code'));
          $capitalizeAllFields=CapitalizeAllFields::setCapitalAll($paymentTerm);
          $paymentTerm->save();

          return response([ 'data' => [
            'message' => 'Payment term updated successfully',
            'PaymentTerm' => $paymentTerm,
            'status'=>'1'
          ]]);
        }
        else
        {
          $errors = $paymentTerm->errors();// failure, get errors
          $errors_str = $paymentTerm->errors_tostring();
          return response(['errors' => ['validationErrors' => $errors, 'validationErrorsText' => $errors_str]], Response::HTTP_UNPROCESSABLE_ENTITY);
        }
       }
      }
      else{
        return response($this->authorize->error_response(), 401);
      }
    }

    //deactivate a Payment Term
    public function destroy($id)
    {
      if($this->authorize->hasPermission('PAYMENT_TERM_DELETE'))//check permission
      {
        $is_exsits_in_supplier=DB::table('org_supplier')->where('payemnt_terms',$id)->exists();
        $is_exsits_in_customer=DB::table('cust_customer')->where('payemnt_terms',$id)->exists();
        if($is_exsits_in_supplier||$is_exsits_in_customer){
          return response([
            'data' => [
              'message' => 'Payment term Already in Use',
              'status' => '0'
            ]
          ]);
        }
        else{
        $paymentTerm = PaymentTerm::where('payment_term_id', $id)->update(['status' => 0]);
        return response([
          'data' => [
            'message' => 'Payment term deactivated successfully.',
            'PaymentTerm' => $paymentTerm,
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
        return response($this->validate_duplicate_code($request->payment_term_id , $request->payment_code));
      }
    }


    //check Payment Term code already exists
    private function validate_duplicate_code($id , $code)
    {
      $paymentTerm = PaymentTerm::where('payment_code','=',$code)->first();
      if($paymentTerm == null){
        return ['status' => 'success'];
      }
      else if($paymentTerm->payment_term_id == $id){
        return ['status' => 'success'];
      }
      else {
        return ['status' => 'error','message' => 'Payment term code already exists'];
      }
    }


    //get filtered fields only
    private function list($active = 0 , $fields = null)
    {
      $query = null;
      if($fields == null || $fields == '') {
        $query = PaymentTerm::select('*');
      }
      else{
        $fields = explode(',', $fields);
        $query = PaymentTerm::select($fields);
        if($active != null && $active != ''){
          $query->where([['status', '=', $active]]);
        }
      }
      return $query->get();
    }


    //search Payment Terms for autocomplete
    private function autocomplete_search($search)
  	{
  		$payment_method_list = PaymentTerm::select('payment_term_id','payment_code')
  		->where([['payment_code', 'like', '%' . $search . '%'],]) ->get();
  		return $payment_method_list;
  	}


    //get searched Payment Terms for datatable plugin format
    private function datatable_search($data)
    {
      if($this->authorize->hasPermission('PAYMENT_TERM_MANAGE'))//check permission
      {
        $start = $data['start'];
        $length = $data['length'];
        $draw = $data['draw'];
        $search = $data['search']['value'];
        $order = $data['order'][0];
        $order_column = $data['columns'][$order['column']]['data'];
        $order_type = $order['dir'];

        $payment_method_list = PaymentTerm::select('*')
        ->where('payment_code'  , 'like', $search.'%' )
        ->orWhere('payment_description'  , 'like', $search.'%' )
        ->orderBy($order_column, $order_type)
        ->offset($start)->limit($length)->get();

        $payment_method_count = PaymentTerm::where('payment_code'  , 'like', $search.'%' )
        ->orWhere('payment_description'  , 'like', $search.'%' )
        ->count();

        return [
            "draw" => $draw,
            "recordsTotal" => $payment_method_count,
            "recordsFiltered" => $payment_method_count,
            "data" => $payment_method_list
        ];
      }
      else{
        return response($this->authorize->error_response(), 401);
      }
    }

}
