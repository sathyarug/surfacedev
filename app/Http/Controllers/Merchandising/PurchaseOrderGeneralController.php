<?php

namespace App\Http\Controllers\Merchandising;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\DB;

use App\Http\Controllers\Controller;
use App\Models\Merchandising\PurchaseOrderGeneral;
//use App\Libraries\UniqueIdGenerator;
//use App\Models\Merchandising\StyleCreation;

class PurchaseOrderGeneralController extends Controller
{
    public function __construct()
    {
      //add functions names to 'except' paramert to skip authentication
      $this->middleware('jwt.verify', ['except' => ['index']]);
    }

    //get customer list
    public function index(Request $request)
    {
      //$id_generator = new UniqueIdGenerator();
      //echo $id_generator->generateCustomerOrderId('CUSTOMER_ORDER' , 1);
      //echo UniqueIdGenerator::generateUniqueId('CUSTOMER_ORDER' , 2 , 'FDN');
      $type = $request->type;
      if($type == 'datatable') {
        $data = $request->all();
        return response($this->datatable_search($data));
      }
      else if($type == 'auto')    {
        $search = $request->search;
        return response($this->autocomplete_search($search));
      }
      else if($type == 'style')    {
        $search = $request->search;
        return response($this->style_search($search));
      }
      else{
        return response([]);
      }
    }


    //create a customer
    public function store(Request $request)
    {
      $order = new PurchaseOrderGeneral();
      if($order->validate($request->all()))
      {
        $order->fill($request->all());
        $order->po_status = 'PLANNED';
        $order->save();

        return response([ 'data' => [
          'message' => 'Purchase order was saved successfully',
          'savepo' => $order,
          'status' => 'PLANNED'
          ]
        ], Response::HTTP_CREATED );
      }
      else
      {
          $errors = $order->errors();// failure, get errors
          return response(['errors' => ['validationErrors' => $errors]], Response::HTTP_UNPROCESSABLE_ENTITY);
      }
    }


    //get a customer
    public function show($id)
    {
      $customer = PurchaseOrderGeneral::with(['currency','location','supplier'])->find($id);
      if($customer == null)
        throw new ModelNotFoundException("Requested PO not found", 1);
      else
        return response([ 'data' => $customer ]);
    }


    //update a customer
   public function update(Request $request, $id)
    {
      $pOrder = PurchaseOrderGeneral::find($id);
      if($pOrder->validate($request->all()))
      {
        $pOrder->fill($request->except('customer_code'));
        $pOrder->save();

        return response([ 'data' => [
          'message' => 'Purchase order was updated successfully',
          'customer' => $pOrder
        ]]);
      }
      else
      {
        $errors = $pOrder->errors();// failure, get errors
        return response(['errors' => ['validationErrors' => $errors]], Response::HTTP_UNPROCESSABLE_ENTITY);
      }
    }


    //deactivate a customer
    public function destroy($id)
    {
      /*$customer = Customer::where('customer_id', $id)->update(['status' => 0]);
      return response([
        'data' => [
          'message' => 'Customer was deactivated successfully.',
          'customer' => $customer
        ]
      ] , Response::HTTP_NO_CONTENT);*/
    }


    //validate anything based on requirements
    public function validate_data(Request $request){
      /*$for = $request->for;
      if($for == 'duplicate')
      {
        return response($this->validate_duplicate_code($request->customer_id , $request->customer_code));
      }*/
    }


    public function customer_divisions(Request $request) {
        /*$type = $request->type;
        $customer_id = $request->customer_id;

        if($type == 'selected')
        {
          $selected = Division::select('division_id','division_description')
          ->whereIn('division_id' , function($selected) use ($customer_id){
              $selected->select('division_id')
              ->from('org_customer_divisions')
              ->where('customer_id', $customer_id);
          })->get();
          return response([ 'data' => $selected]);
        }
        else
        {
          $notSelected = Division::select('division_id','division_description')
          ->whereNotIn('division_id' , function($notSelected) use ($customer_id){
              $notSelected->select('division_id')
              ->from('org_customer_divisions')
              ->where('customer_id', $customer_id);
          })->get();
          return response([ 'data' => $notSelected]);
        }*/

    }

    public function save_customer_divisions(Request $request)
    {
      /*$customer_id = $request->get('customer_id');
      $divisions = $request->get('divisions');
      if($customer_id != '')
      {
        DB::table('org_customer_divisions')->where('customer_id', '=', $customer_id)->delete();
        $customer = Customer::find($customer_id);
        $save_divisions = array();

        foreach($divisions as $devision)		{
          array_push($save_divisions,Division::find($devision['division_id']));
        }

        $customer->divisions()->saveMany($save_divisions);
        return response([
          'data' => [
            'customer_id' => $customer_id
          ]
        ]);
      }
      else {
        throw new ModelNotFoundException("Requested customer not found", 1);
      }*/
    }


    //check customer code already exists
    private function validate_duplicate_code($id , $code)
    {
      /*$customer = Customer::where('customer_code','=',$code)->first();
      if($customer == null){
        return ['status' => 'success'];
      }
      else if($customer->customer_id == $id){
        return ['status' => 'success'];
      }
      else {
        return ['status' => 'error','message' => 'Customer code already exists'];
      }*/
    }


    //search customer for autocomplete
    private function autocomplete_search($search)
  	{
  		/*$customer_lists = Customer::select('customer_id','customer_name')
  		->where([['customer_name', 'like', '%' . $search . '%'],]) ->get();
  		return $customer_lists;*/
  	}


    //search customer for autocomplete
    private function style_search($search)
  	{
  	/*	$style_lists = StyleCreation::select('style_id','style_no','customer_id')
  		->where([['style_no', 'like', '%' . $search . '%'],]) ->get();
  		return $style_lists;*/
  	}


    //get searched customers for datatable plugin format
    private function datatable_search($data)
    {
      $start = $data['start'];
      $length = $data['length'];
      $draw = $data['draw'];
      $search = $data['search']['value'];
      $order = $data['order'][0];
      $order_column = $data['columns'][$order['column']]['data'];
      $order_type = $order['dir'];

      $customer_list = PurchaseOrderGeneral::join('org_location', 'org_location.loc_id', '=', 'merc_po_order_general_header.po_deli_loc')
	  ->join('org_supplier', 'org_supplier.supplier_id', '=', 'merc_po_order_general_header.po_sup_code')
      ->join('fin_currency', 'fin_currency.currency_id', '=', 'merc_po_order_general_header.po_def_cur')
	  ->select('merc_po_order_general_header.*','org_location.loc_name','org_supplier.supplier_name',
          'fin_currency.currency_code')
      ->where('po_number'  , 'like', $search.'%' )
      ->orWhere('supplier_name'  , 'like', $search.'%' )
      
	  ->orWhere('loc_name'  , 'like', $search.'%' )
      ->orderBy($order_column, $order_type)
      ->offset($start)->limit($length)->get();

      $customer_count = PurchaseOrderGeneral::join('org_location', 'org_location.loc_id', '=', 'merc_po_order_general_header.po_deli_loc')
	  ->join('org_supplier', 'org_supplier.supplier_id', '=', 'merc_po_order_general_header.po_sup_code')
      ->join('fin_currency', 'fin_currency.currency_id', '=', 'merc_po_order_general_header.po_def_cur')
      ->where('po_number'  , 'like', $search.'%' )
      ->orWhere('supplier_name'  , 'like', $search.'%' )

	  ->orWhere('loc_name'  , 'like', $search.'%' )
      ->count();

      return [
          "draw" => $draw,
          "recordsTotal" => $customer_count,
          "recordsFiltered" => $customer_count,
          "data" => $customer_list
      ];
    }

}
