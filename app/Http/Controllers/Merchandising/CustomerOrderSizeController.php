<?php

namespace App\Http\Controllers\Merchandising;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\DB;

use App\Http\Controllers\Controller;
use App\Models\Merchandising\CustomerOrderSize;
//use App\Libraries\UniqueIdGenerator;
use App\Models\Merchandising\StyleCreation;

class CustomerOrderSizeController extends Controller
{
    public function __construct()
    {
      //add functions names to 'except' paramert to skip authentication
      $this->middleware('jwt.verify', ['except' => ['index','show']]);
    }

    //get customer list
    public function index(Request $request)
    {
        $details_id = $request->details_id;
        return response(['data' => $this->list($details_id)]);
    }


    //create a customer
    public function store(Request $request)
    {
      $size = new CustomerOrderSize();
      if($size->validate($request->all()))
      {
        $size_count = CustomerOrderSize::where('details_id', '=', $request->details_id)
        ->where('size_id', '=', $request->size_id)
        ->where('status','<>',0)
        ->count();

        if($size_count > 0){
          return response([ 'data' => [
            'message' => 'Size already exists'
            ]
          ], Response::HTTP_UNPROCESSABLE_ENTITY );
        }
        else{
          $size->fill($request->all());
          $size->version_no = 0;//$this->get_next_version_no($request->details_id);
          $size->line_no = $this->get_next_line_no($request->details_id);
          $size->save();
          $size = CustomerOrderSize::with(['size'])->find($size->id);

          return response([ 'data' => [
            'message' => 'Customer order size was saved successfully',
            'customerOrderSize' => $size
            ]
          ], Response::HTTP_CREATED );
        }
      }
      else
      {
          $errors = $size->errors();// failure, get errors
          return response(['errors' => ['validationErrors' => $errors]], Response::HTTP_UNPROCESSABLE_ENTITY);
      }
    }


    //get a customer
    public function show($id)
    {
      $size = CustomerOrderSize::with(['size'])->find($id);
      if($size == null)
        throw new ModelNotFoundException("Requested order size not found", 1);
      else
        return response([ 'data' => $size ]);
    }


    //update a customer
    public function update(Request $request, $id)
    {
      $new_size = new CustomerOrderSize();
      if($new_size->validate($request->all()))
      {
        $size = CustomerOrderSize::find($id);

        $next_version_no = $this->get_next_version_no($size->details_id);
        $new_size->version_no = $next_version_no;
        $new_size->line_no = $size->line_no;
        $new_size->fill($request->all());
        $new_size->save();
        $new_size = CustomerOrderSize::with(['size'])->find($new_size->id);

        return response([ 'data' => [
          'message' => 'Customer order size was saved successfully',
          'customerOrderSize' => $new_size
          ]
        ], Response::HTTP_CREATED );
      }
      else
      {
          $errors = $new_size->errors();// failure, get errors
          return response(['errors' => ['validationErrors' => $errors]], Response::HTTP_UNPROCESSABLE_ENTITY);
      }
    }


    //deactivate a customer order size
    public function destroy($id)
    {
      CustomerOrderSize::where('id', $id)->update(['status' => 0]);
      return response([
        'data' => [
          'message' => 'Size was removed successfully.',
          'size' => null
        ]
      ] , Response::HTTP_NO_CONTENT);
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
  		$style_lists = StyleCreation::select('style_id','style_no','customer_id')
  		->where([['style_no', 'like', '%' . $search . '%'],]) ->get();
  		return $style_lists;
  	}


    private function list($details_id){
    /*  $order_sizes = CustomerOrderSize::join('org_size','merc_customer_order_size.size_id','=','org_size.size_id')
      ->select('merc_customer_order_size.*','org_size.size_name')
      ->where('merc_customer_order_size.details_id','=',$details_id)
      ->where('')
      ->get();*/
      $results = DB::select('select a.*,s.size_name from merc_customer_order_size a
      INNER JOIN org_size s ON a.size_id = s.size_id where
      a.details_id = ? and
      a.status = 1 and
      a.version_no = (select MAX(b.version_no) from merc_customer_order_size b where b.details_id=? and b.size_id=a.size_id GROUP BY b.size_id)
      order by a.line_no',
      array($details_id , $details_id));
      //$order_sizes = CustomerOrderSize::with(['size'])->where('details_id','=',$details_id)->get();
      return $results;
    }


    //get searched customers for datatable plugin format
    private function get_next_version_no($details_id)
    {
      $max_id = CustomerOrderSize::where('details_id','=',$details_id)->max('version_no');
      return ($max_id + 1);
    }

    //get searched customers for datatable plugin format
    private function get_next_line_no($details_id)
    {
      $max_no = CustomerOrderSize::where('details_id','=',$details_id)->max('line_no');
      return ($max_no + 1);
    }

}
