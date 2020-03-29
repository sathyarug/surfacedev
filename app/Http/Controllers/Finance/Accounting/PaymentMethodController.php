<?php

namespace App\Http\Controllers\Finance\Accounting;

use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Database\Eloquent\ModelNotFoundException;

use App\Http\Controllers\Controller;
use App\Models\Finance\Accounting\PaymentMethod;
use App\Libraries\AppAuthorize;
use App\Libraries\CapitalizeAllFields;
use Illuminate\Support\Facades\DB;
class PaymentMethodController extends Controller
{
    var $authorize = null;

    public function __construct()
    {
      //add functions names to 'except' paramert to skip authentication
      $this->middleware('jwt.verify', ['except' => ['index']]);
      $this->authorize = new AppAuthorize();
    }

    //get payment method list
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

    //create a payment method
    public function store(Request $request)
    {
      if($this->authorize->hasPermission('PAYMENT_METHOD_MANAGE'))//check permission
      {
        $paymentMethod = new PaymentMethod();
        if($paymentMethod->validate($request->all()))
        {
          $paymentMethod->fill($request->all());
          $capitalizeAllFields=CapitalizeAllFields::setCapitalAll($paymentMethod);
          $paymentMethod->status = 1;
          $paymentMethod->save();

          return response([ 'data' => [
            'message' => 'Payment Method saved successfully',
            'paymentMethod' => $paymentMethod,
            'status'=>'1'
            ]
          ], Response::HTTP_CREATED );
        }
        else {
          $errors = $paymentMethod->errors();// failure, get errors
          $errors_str = $paymentMethod->errors_tostring();
          return response(['errors' => ['validationErrors' => $errors, 'validationErrorsText' => $errors_str]], Response::HTTP_UNPROCESSABLE_ENTITY);
        }
      }
      else{
        return response($this->authorize->error_response(), 401);
      }
    }

    //get a payment method
    public function show($id)
    {
      if($this->authorize->hasPermission('PAYMENT_METHOD_MANAGE'))//check permission
      {
        $paymentMethod = PaymentMethod::find($id);
        if($paymentMethod == null)
          throw new ModelNotFoundException("Requested payment method not found", 1);
        else
          return response( ['data' => $paymentMethod] );
      }
      else{
        return response($this->authorize->error_response(), 401);
      }
    }


    //update a payment method
    public function update(Request $request, $id)
    {
      if($this->authorize->hasPermission('PAYMENT_METHOD_MANAGE'))//check permission
      {
        $is_exsits_in_supplier=DB::table('org_supplier')->where('payment_mode',$id)->exists();
        $is_exsits_in_customer=DB::table('cust_customer')->where('payment_mode',$id)->exists();
        if($is_exsits_in_supplier||$is_exsits_in_customer){
          return response([ 'data' => [
            'message' => 'Payment Method Already in Use',
            'status' => '0',
          ]]);
        }

        else {
          $paymentMethod = PaymentMethod::find($id);

          if($paymentMethod->validate($request->all()))
          {
            $paymentMethod->fill( $request->except('payment_method_code'));
            $capitalizeAllFields=CapitalizeAllFields::setCapitalAll($paymentMethod);
            $paymentMethod->save();

            return response([ 'data' => [
              'message' => 'Payment method updated successfully',
              'paymentMethod' => $paymentMethod,
              'status'=>'1',
            ]]);
          }
          else {
            $errors = $paymentMethod->errors();// failure, get errors
            $errors_str = $paymentMethod->errors_tostring();
            return response(['errors' => ['validationErrors' => $errors, 'validationErrorsText' => $errors_str]], Response::HTTP_UNPROCESSABLE_ENTITY);
          }
        }
      }
      else{
        return response($this->authorize->error_response(), 401);
      }
    }

    //deactivate a payment method
    public function destroy($id)
    {
      if($this->authorize->hasPermission('PAYMENT_METHOD_DELETE'))//check permission
      {
        $is_exsits_in_supplier=DB::table('org_supplier')->where('payment_mode',$id)->exists();
        $is_exsits_in_customer=DB::table('cust_customer')->where('payment_mode',$id)->exists();
        if($is_exsits_in_supplier||$is_exsits_in_customer){
          return response([ 'data' => [
            'message' => 'Payment Method Already in Use',
            'status' => '0',
          ]]);
        }
        else{
        $paymentMethod = PaymentMethod::where('payment_method_id', $id)->update(['status' => 0]);
        return response([
          'data' => [
            'message' => 'Payment method deactivated successfully.',
            'paymentMethod' => $paymentMethod,
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
        return response($this->validate_duplicate_code($request->payment_method_id , $request->payment_method_code));
      }
    }


    //check payment method code already exists
    private function validate_duplicate_code($id , $code)
    {
      $paymentMethod = PaymentMethod::where('payment_method_code','=',$code)->first();
      if($paymentMethod == null){
        return ['status' => 'success'];
      }
      else if($paymentMethod->payment_method_id == $id){
        return ['status' => 'success'];
      }
      else {
        return ['status' => 'error','message' => 'Payment method code already exists'];
      }
    }


    //get filtered fields only
    private function list($active = 0 , $fields = null)
    {
      $query = null;
      if($fields == null || $fields == '') {
        $query = PaymentMethod::select('*');
      }
      else{
        $fields = explode(',', $fields);
        $query = PaymentMethod::select($fields);
        if($active != null && $active != ''){
          $query->where([['status', '=', $active]]);
        }
      }
      return $query->get();
    }


    //search payment methods for autocomplete
    private function autocomplete_search($search)
  	{
  		$payment_method_list = PaymentMethod::select('payment_method_id','payment_method_code')
  		->where([['payment_method_code', 'like', '%' . $search . '%'],]) ->get();
  		return $payment_method_list;
  	}


    //get searched payment methods for datatable plugin format
    private function datatable_search($data)
    {
      if($this->authorize->hasPermission('PAYMENT_METHOD_MANAGE'))//check permission
      {
        $start = $data['start'];
        $length = $data['length'];
        $draw = $data['draw'];
        $search = $data['search']['value'];
        $order = $data['order'][0];
        $order_column = $data['columns'][$order['column']]['data'];
        $order_type = $order['dir'];

        $payment_method_list = PaymentMethod::select('*')
        ->where('payment_method_code'  , 'like', $search.'%' )
        ->orWhere('payment_method_description'  , 'like', $search.'%' )
        ->orderBy($order_column, $order_type)
        ->offset($start)->limit($length)->get();

        $payment_method_count = PaymentMethod::where('payment_method_code'  , 'like', $search.'%' )
        ->orWhere('payment_method_description'  , 'like', $search.'%' )
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
