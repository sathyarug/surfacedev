<?php
namespace App\Http\Controllers\Org;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\DB;

use App\Http\Controllers\Controller;
use App\Models\Org\CustomerSizeGrid;
use App\Models\Org\Customer;
use App\Models\Org\Size;
use App\Models\Org\Silhouette;

class CustomerSizeGridController extends Controller
{


  public function __construct()
  {
    //add functions names to 'except' paramert to skip authentication
    $this->middleware('jwt.verify', ['except' => ['index']]);
  }

  //get customer size list
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


      //create a customer Size Grid
      public function store(Request $request)
      {
          $customerSizeGrid= new  CustomerSizeGrid ();
          $customerSizeGrid->fill($request->all());
          $customerSizeGrid->status = 1;

          $customerSizeGrid->save();

          return response([ 'data' => [
            'message' => 'Customer Size Grid Saved successfully',
            'customerSizeGrid' => $customerSizeGrid
            ]
          ], Response::HTTP_CREATED );
      }

    /*  public function store(Request $request)
      {
        $customerSizeGrid= new  CustomerSizeGrid ();
        //if($location->validate($request->all()))
        {
          $customerSizeGrid->fill($request->all());
          $customerSizeGrid->status = 1;
          $customerSizeGrid->created_by = 1;
          $result = $customerSizeGrid->saveOrFail();
          $insertedId = $customerSizeGrid->id;

          DB::table('cust_size_grid')->where('id', '=', $insertedId)->delete();
          $cost_centers = $request->get('cost_centers');
          $save_cost_centers = array();
          if($cost_centers != '') {
            foreach($cost_centers as $cost_center)		{
              array_push($save_cost_centers,CostCenter::find($cost_center['cost_center_id']));
            }
          }
          $location->costCenters()->saveMany($save_cost_centers);

          return response([ 'data' => [
            'message' => 'Location was saved successfully',
            'location' => $location
            ]
          ], Response::HTTP_CREATED );
        }
        else
        {
            $errors = $location->errors();// failure, get errors
            return response(['errors' => ['validationErrors' => $errors]], Response::HTTP_UNPROCESSABLE_ENTITY);
        }
      }
*/



      //get customer size grid
      public function show($id)
      {

          $customerSizeGrid = CustomerSizeGrid::find($id);
          if($customerSizeGrid == null)
            throw new ModelNotFoundException("Requested Customer Size not found", 1);
          else
            return response([ 'data' => $customerSizeGrid ]);
      }

      //deactivate a ship term
      public function destroy($id)
      {
          $customerSizeGrid =CustomerSizeGrid::where('id', $id)->update(['status' => 0]);
          return response([
            'data' => [
              'message' => ' Customer Size Grid Deactivated successfully.',
              'customerSizeGrid' => $customerSizeGrid
            ]
          ] , Response::HTTP_NO_CONTENT);
      }

      //validate anything based on requirements
      public function validate_data(Request $request){
        $for = $request->for;

        if($for == 'duplicate')
        {
          //print_r( $request->all());
          return response($this->validate_duplicate_code($request->id , $request->customer_name,
          $request->product_silhouette_description,$request->size_name));
        }
      }

      //customer name refeerd as customer id others also like that
      private function validate_duplicate_code($id, $customer_name, $product_silhouette_description, $size_name)
    {


      $customerSizeGrid =CustomerSizeGrid :: where([['customer_id','=',$customer_name],['product_silhouette_id','=',$product_silhouette_description],['size_id','=',$size_name]])->first();


      if($customerSizeGrid == null){
        echo json_encode(array('status' => 'success'));
      }
      else if($customerSizeGrid->id == $id){
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
          $query = CustomerSizeGrid::select('*');
        }
        else{
          $fields = explode(',', $fields);
          $query = CustomerSizeGrid::select($fields);
          if($active != null && $active != ''){
            $query->where([['status', '=', $active]]);
          }
        }
        return $query->get();
      }

      //search Customer Size Grid for autocomplete
      private function autocomplete_search($search)
    	{
    		$customersizegrid_lists = CustomerSizeGrid::select('customer_id')
    		->where([['customer_id', 'like', '%' . $search . '%'],]) ->get();
    		return $customersizegrid_lists;
    	}

      //get searched ship terms for datatable plugin format
      private function datatable_search($data)
      {
        $start = $data['start'];
        $length = $data['length'];
        $draw = $data['draw'];
        $search = $data['search']['value'];
        $order = $data['order'][0];
        $order_column = $data['columns'][$order['column']]['data'];
        $order_type = $order['dir'];

        /*$customerSizeGrid_list = CustomerSizeGrid::select('*')
        ->where('customer_id'  , 'like', $search.'%' )
        ->orderBy($order_column, $order_type)
        ->offset($start)->limit($length)->get();

        $customerSizeGrid_count = CustomerSizeGrid::where('customer_id'  , 'like', $search.'%' )
        ->count();

        return [
            "draw" => $draw,
            "recordsTotal" => $customerSizeGrid_count,
            "recordsFiltered" => $customerSizeGrid_count,
            "data" =>   $customerSizeGrid_list
        ];*/

        $customerSizeGrid_list= CustomerSizeGrid::join('cust_customer', 'cust_size_grid.customer_id', '=', 'cust_customer.customer_id')
        ->join('product_silhouette', 'cust_size_grid.product_silhouette_id', '=', 'product_silhouette.product_silhouette_id')
        ->join('org_size', 'cust_size_grid.size_id', '=', 'org_size.size_id')
        ->select('cust_size_grid.*', 'cust_customer.customer_name','product_silhouette.product_silhouette_description','org_size.size_name')
        ->where('size_name','like',$search.'%')
        ->orWhere('product_silhouette_description', 'like', $search.'%')
        ->orWhere('customer_name', 'like', $search.'%')
        ->orderBy($order_column, $order_type)
        ->offset($start)->limit($length)->get();

        $customerSizeGrid_list_count= CustomerSizeGrid::join('cust_customer', 'cust_size_grid.customer_id', '=', 'cust_customer.customer_id')
        ->join('product_silhouette', 'cust_size_grid.product_silhouette_id', '=', 'product_silhouette.product_silhouette_id')
        ->join('org_size', 'cust_size_grid.size_id', '=', 'org_size.size_id')
        ->select('cust_size_grid.*', 'cust_customer.customer_name','product_silhouette.product_silhouette_description','org_size.size_name')
        ->where('size_name','like',$search.'%')
        ->orWhere('product_silhouette_description', 'like', $search.'%')
        ->orWhere('customer_name', 'like', $search.'%')
        ->count();
        return [
            "draw" => $draw,
            "recordsTotal" => $customerSizeGrid_list_count,
            "recordsFiltered" => $customerSizeGrid_list_count,
            "data" =>$customerSizeGrid_list
        ];


      }



}
