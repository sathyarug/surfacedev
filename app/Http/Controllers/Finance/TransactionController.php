<?php

namespace App\Http\Controllers\Finance;

use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Database\Eloquent\ModelNotFoundException;

use App\Http\Controllers\Controller;
use App\Models\Finance\Transaction ;
use App\Libraries\AppAuthorize;
use App\Libraries\CapitalizeAllFields;

class TransactionController extends Controller
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
      if($this->authorize->hasPermission('TRANSACTION_MANAGE'))//check permission
      {
        $transaction = new Transaction();
        if($transaction->validate($request->all()))
        {
          $transaction->fill($request->all());
          $capitalizeAllFields=CapitalizeAllFields::setCapitalAll($transaction);
          $transaction->status = 1;
          $transaction->save();

          return response([ 'data' => [
            'message' => ' Transaction saved successfully',
            'transaction' => $transaction
            ]
          ], Response::HTTP_CREATED );
        }
        else {
          $errors = $transaction->errors();// failure, get errors
          $errors_str = $transaction->errors_tostring();
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
      if($this->authorize->hasPermission('TRANSACTION_MANAGE'))//check permission
      {
        $transaction = Transaction::find($id);
        if($transaction == null)
          throw new ModelNotFoundException("Requested shipment term not found", 1);
        else
          return response([ 'data' => $transaction ]);
      }
      else{
        return response($this->authorize->error_response(), 401);
      }
    }


    //update a shipment term
    public function update(Request $request, $id)
    {
      if($this->authorize->hasPermission('TRANSACTION_MANAGE'))//check permission
      {
        $transaction = Transaction::find($id);
        if($transaction->validate($request->all()))
        {
          $transaction->fill($request->except('trans_code'));
          $capitalizeAllFields=CapitalizeAllFields::setCapitalAll($transaction);
          $transaction->save();

          return response([ 'data' => [
            'message' => 'Transaction updated successfully',
            'transaction' => $transaction
          ]]);
        }
        else {
          $errors = $transaction->errors();// failure, get errors
          $errors_str = $transaction->errors_tostring();
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
      if($this->authorize->hasPermission('TRANSACTION_DELETE'))//check permission
      {
        $transaction = Transaction::where('trans_id', $id)->update(['status' => 0]);
        return response([
          'data' => [
            'message' => 'Shipment term was deactivated successfully.',
            'transaction' => $transaction
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

        return response($this->validate_duplicate_code($request->trans_id , $request->trans_code));
      }
    }


    //check shipment cterm code code already exists

      private function validate_duplicate_code($id , $code){
        //echo $id;
        //echo $code;
        //echo $transDescription;
       $transaction =Transaction::where('trans_code','=',$code)->first();
       //dd($transaction);

      if( $transaction == null){
      echo json_encode(array('status' => 'success'));
      }
      else if( $transaction->trans_id == $id){
      echo json_encode(array('status' => 'success'));
      }
      else {
      echo json_encode(array('status' => 'error','message' => 'Record already exists'));
      }
    }


    //get filtered fields only
    private function list($active = 0 , $fields = null)
    {
      $query = null;
      if($fields == null || $fields == '') {
        $query = Transaction::select('*');
      }
      else{
        $fields = explode(',', $fields);
        $query = Transaction::select($fields);
        if($active != null && $active != ''){
          $query->where([['status', '=', $active]]);
        }
      }
      return $query->get();
    }


    //search shipment terms for autocomplete
    private function autocomplete_search($search)
  	{
  		$transaction_lists = Transaction::select('trans_id','trans_code')
  		->where([['trans_code', 'like', '%' . $search . '%'],]) ->get();
  		return $transaction_lists;
  	}


    //get searched ship terms for datatable plugin format
    private function datatable_search($data)
    {
      if($this->authorize->hasPermission('TRANSACTION_MANAGE'))//check permission
      {
        $start = $data['start'];
        $length = $data['length'];
        $draw = $data['draw'];
        $search = $data['search']['value'];
        $order = $data['order'][0];
        $order_column = $data['columns'][$order['column']]['data'];
        $order_type = $order['dir'];

        $transaction_list = Transaction::select('*')
        ->where('trans_code'  , 'like', $search.'%' )
        ->orWhere('trans_description'  , 'like', $search.'%' )
        ->orderBy($order_column, $order_type)
        ->offset($start)->limit($length)->get();

        $transaction_count = Transaction::where('trans_code'  , 'like', $search.'%' )
        ->orWhere('trans_description'  , 'like', $search.'%' )
        ->count();

        return [
            "draw" => $draw,
            "recordsTotal" => $transaction_count,
            "recordsFiltered" => $transaction_count,
            "data" => $transaction_list
        ];
      }
      else{
        return response($this->authorize->error_response(), 401);
      }
    }

}
