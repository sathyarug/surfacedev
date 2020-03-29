<?php

namespace App\Http\Controllers\Merchandising;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\DB;

use App\Http\Controllers\Controller;
use App\Models\Merchandising\CustomerOrder;
use App\Models\Merchandising\Costing\Costing;
//use App\Libraries\UniqueIdGenerator;
use App\Models\Merchandising\StyleCreation;
use App\Models\Merchandising\BuyMaster;
use App\Libraries\SearchQueryBuilder;

class CustomerOrderController extends Controller
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
      else if($type == 'lot')    {
        $search = $request->search;
        return response($this->lot_search($search));
      }
      else if($type == 'buyname')    {
        $search = $request->search;
        return response($this->buyname_search($search));
      }
      else if($type == 'search_fields'){
        return response([
          'data' => $this->get_search_fields()
        ]);
      }
      else if($type == 'customer_orders_for_style'){
          return response([
              'data' => $this->getCustomerOrdersForStyle($request)
          ]);
      }
      elseif($type == 'select') {
          $active = $request->active;
          $fields = $request->fields;
          return response([
              'data' => $this->list($active, $fields)
          ]);
      }
      else{
        return response([]);
      }
    }


    //create a customer
    public function store(Request $request)
    {

      $style = $request->order_style;
      $season = $request->order_season;
      $stage = $request->order_stage;

      $check_stage = CustomerOrder::select(DB::raw('count(*) as po_count'))
                     ->where('order_style', '=', $style)
                     ->where('order_stage', '=', $stage)
                     ->where('order_season', '=', $season)
                     ->where('order_status', '=', 'PLANNED')
                     ->get();
      if($check_stage[0]['po_count'] >=1 ){

        return response([ 'data' => ['status' => 'error','message' => '( Style -> Season -> Stage ) already exists !']]);

      }else{

        $order = new CustomerOrder();
        if($order->validate($request->all()))
        {
          $order->fill($request->except(['order_status']));
          $order->order_status = 'PLANNED';
          $order->lot_number =strtoupper($request->lot_number);

          $order->save();

          return response([ 'data' => [
            'message' => 'Sales order saved successfully',
            'customerOrder' => $order
            ]
          ], Response::HTTP_CREATED );
        }
        else
        {
            $errors = $order->errors();// failure, get errors
            return response(['errors' => ['validationErrors' => $errors]], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

      }

    }


    //get a customer
    public function show($id)
    {
      $customerOrder = CustomerOrder::with(['style','customer','division','buyname'])->find($id);

      //dd($customerOrder->order_style);

      $season = CustomerOrder::select('org_season.season_id', 'org_season.season_name')
                   //->join('costing', 'merc_customer_order_header.order_style', '=', 'costing.style_id')
                   ->join('org_season', 'merc_customer_order_header.order_season', '=', 'org_season.season_id')
                   ->where('merc_customer_order_header.order_id', '=', $id)

                   ->get();

      $customerOrder['season1'] = $season;

      $bom_stage = CustomerOrder::select('merc_bom_stage.bom_stage_id', 'merc_bom_stage.bom_stage_description')
                   //->join('costing', 'merc_customer_order_header.order_style', '=', 'costing.style_id')
                   ->join('merc_bom_stage', 'merc_customer_order_header.order_stage', '=', 'merc_bom_stage.bom_stage_id')
                   ->where('merc_customer_order_header.order_id', '=', $id)

                   ->get();

      $customerOrder['bom_stage1']  = $bom_stage;

      $buy_name = CustomerOrder::select('buy_master.buy_id', 'buy_master.buy_name')
                  ->join('buy_master', 'merc_customer_order_header.order_buy_name', '=', 'buy_master.buy_id')
                  ->where('merc_customer_order_header.order_id', '=', $id)
                  ->get();
      $customerOrder['buy_name']  = $buy_name;

      $style_pack_count   = StyleCreation::select('product_feature.count')
                          ->join('product_feature', 'product_feature.product_feature_id', '=','style_creation.product_feature_id')
                          ->where('style_id', '=', $customerOrder->order_style)
                          ->get();

      $customerOrder['pack_count']  = $style_pack_count;


      if($customerOrder == null)
        throw new ModelNotFoundException("Requested customer order not found", 1);
      else
        return response([ 'data' => $customerOrder ]);
    }


    //update a customer
    public function update(Request $request, $id)
    {
      $customerOrder = CustomerOrder::find($id);
      if($customerOrder->validate($request->all()))
      {
        $customerOrder->fill($request->except(['customer_code','order_status']));
        $customerOrder->save();

        return response([ 'data' => [
          'message' => 'Sales order updated successfully',
          'customerOrder' => $customerOrder
        ]]);
      }
      else
      {
        $errors = $customerOrder->errors();// failure, get errors
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
  		$customer_lists = CustomerOrder::select('order_id','order_code')
  		->where([['order_code', 'like', '%' . $search . '%'],])
      //->where('status','=',1)
      ->get();
  		return $customer_lists;
  	}


    //search customer for autocomplete
    private function style_search($search)
  	{
  		$style_lists = StyleCreation::select('style_creation.*', 'cust_customer.customer_code','cust_customer.customer_name','cust_division.division_description')
      ->join('cust_customer', 'style_creation.customer_id', '=', 'cust_customer.customer_id')
      ->join('cust_division', 'style_creation.division_id', '=', 'cust_division.division_id')
  		->where([['style_no', 'like', '%' . $search . '%'],]) ->get();
  		return $style_lists;
  	}

    private function lot_search($search)
  	{
  		$lot_lists = CustomerOrder::select('merc_customer_order_header.lot_number')
  		->where([['lot_number', 'like', '%' . $search . '%'],])
      ->groupBy('merc_customer_order_header.lot_number')
      ->get();
  		return $lot_lists;
  	}

    private function buyname_search($search)
  	{
  		$style_lists = BuyMaster::select('buy_master.buy_id', 'buy_master.buy_name')
  		->where([['buy_name', 'like', '%' . $search . '%'],]) ->get();
  		return $style_lists;
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
      $fields = json_decode($data['query_data']);
      $user = auth()->user();


      $customer_list = CustomerOrder::join('style_creation', 'style_creation.style_id', '=', 'merc_customer_order_header.order_style')
      ->join('cust_customer', 'cust_customer.customer_id', '=', 'merc_customer_order_header.order_customer')
      ->join('cust_division', 'cust_division.division_id', '=', 'merc_customer_order_header.order_division')
      //->join('merc_customer_order_type', 'merc_customer_order_type.order_type_id', '=', 'merc_customer_order_header.order_type')
      ->join('core_status', 'core_status.status', '=', 'merc_customer_order_header.order_status')
      ->select('merc_customer_order_header.*','style_creation.style_no','cust_customer.customer_name',
          'cust_division.division_description',/*'merc_customer_order_type.order_type as order_type_name',*/'core_status.color');

      $customer_count = CustomerOrder::join('style_creation', 'style_creation.style_id', '=', 'merc_customer_order_header.order_style')
      ->join('cust_customer', 'cust_customer.customer_id', '=', 'merc_customer_order_header.order_customer')
      //->join('merc_customer_order_type', 'merc_customer_order_type.order_type_id', '=', 'merc_customer_order_header.order_type')
      ->join('cust_division', 'cust_division.division_id', '=', 'merc_customer_order_header.order_division');

      if(sizeof($fields) > 0)  {
        $searchQueryBuilder = new SearchQueryBuilder();
        $customer_list = $searchQueryBuilder->generateQuery($customer_list, $fields);
        $customer_count = $searchQueryBuilder->generateQuery($customer_count, $fields);
      }
      else{
          $customer_list = $customer_list->
          where('order_code' , 'like', $search.'%' )
          ->orWhere('style_no'  , 'like', $search.'%' )
          ->orWhere('customer_name'  , 'like', $search.'%' )
          ->orWhere('division_description'  , 'like', $search.'%' );

          $customer_count = $customer_count->where('order_code'  , 'like', $search.'%' )
          ->orWhere('style_no'  , 'like', $search.'%' )
          ->orWhere('customer_name'  , 'like', $search.'%' )
          ->orWhere('division_description'  , 'like', $search.'%' );
      }

      $customer_list = $customer_list->orderBy($order_column, $order_type)
          ->offset($start)->limit($length)->get();

      $customer_count =  $customer_count->count();

      for($x = 0 ; $x < sizeof($customer_list) ; $x++){

        IF($customer_list[$x]["created_by"] == $user->user_id)
        {
          $customer_list[$x]['usr_vali'] = 1;
        }else{
          $customer_list[$x]['usr_vali'] = 0;
        }
      }

        //dd($customer_list);
      return [
          "draw" => $draw,
          "recordsTotal" => $customer_count,
          "recordsFiltered" => $customer_count,
          "data" => $customer_list
      ];
    }


    private function get_search_fields(){

      /*$arr = [
        'col1' => 'merc_customer_order_header.order_id',
        'col2' => 'merc_customer_order_header.order_code',
        'col3' => 'merc_customer_order_header.order_code',
        'col4' => 'merc_customer_order_header.order_style'
      ];*/

      return [
        [
          'field' => 'merc_customer_order_header.order_id',
          'field_description' => 'Order ID',
          'type' => 'number'
        ],
        [
          'field' => 'merc_customer_order_header.order_code',
          'field_description' => 'Order code',
          'type' => 'string'
        ],
        [
          'field' => 'merc_customer_order_header.order_code',
          'field_description' => 'Order company',
          'foreign_key' => 'org_company.company_id',
          'type' => 'string'
        ],
        [
          'field' => 'merc_customer_order_header.order_style',
          'field_description' => 'Order style',
          'type' => 'string'
        ]
      ];

    }

    //get filtered fields only
    private function list($active = 0 , $fields = null)
    {
        $query = null;
        if($fields == null || $fields == '') {
            $query = CustomerOrder::select('*');
        }
        else{
            $fields = explode(',', $fields);
            $query = CustomerOrder::select($fields);
            if($active != null && $active != ''){
                $payload = auth()->payload();
                $query->where([['order_status', '=', $active]]);
            }
        }
        return $query->get();
    }

    private function getCustomerOrdersForStyle(Request $request){
        $cusOrder = CustomerOrder::join('merc_customer_order_details', 'merc_customer_order_details.order_id', '=', 'merc_customer_order_header.order_id')
            ->join('org_color', 'org_color.color_id', '=', 'merc_customer_order_details.style_color')
            ->leftJoin('org_country', 'org_country.country_id', '=', 'merc_customer_order_details.country')
            ->select('merc_customer_order_details.details_id','org_color.color_id', 'org_color.color_name','org_country.country_description','merc_customer_order_details.order_qty', 'org_color.color_id', 'merc_customer_order_header.order_code' )
            ->where('merc_customer_order_header.order_style', '=', $request->style_id)
            ->where('merc_customer_order_details.style_color', '=', $request->color)
            ->where('merc_customer_order_details.delivery_status', '=', 'PLANNED')
            ->get()
            ->toArray();

        return $cusOrder;
    }

    public function load_header_season(Request $request){

      $style_id = $request->style_id;

      $season = Costing::select('org_season.season_id', 'org_season.season_name')
                   ->join('org_season', 'costing.season_id', '=', 'org_season.season_id')
                   ->where('style_id', '=', $style_id)
                   ->groupBy('costing.season_id')
                   ->get();

      $arr['season']      = $season;

      if($arr == null)
          throw new ModelNotFoundException("Requested section not found", 1);
      else
          return response([ 'data' => $arr ]);

    }

    public function load_header_stage(Request $request){
    $style_id = $request->style_id;
    $season_id= $request->season_id;

    $bom_stage = Costing::select('merc_bom_stage.bom_stage_id', 'merc_bom_stage.bom_stage_description')
                 ->join('merc_bom_stage', 'costing.bom_stage_id', '=', 'merc_bom_stage.bom_stage_id')
                 ->where('style_id', '=', $style_id)
                 ->where('season_id', '=', $season_id)
                 ->where('costing.status', '<>', 'CANCELLED')
                 ->groupBy('costing.bom_stage_id')
                 ->get();

    $arr['bom_stage']  = $bom_stage;

    if($arr == null)
      throw new ModelNotFoundException("Requested section not found", 1);
    else
      return response([ 'data' => $arr ]);
    }


    public function load_header_buy_name(Request $request){
    $style_id = $request->style_id;
    $season_id= $request->season_id;
    $order_id = $request->order_id;

    $buy_name= Costing::select('buy_master.buy_id', 'buy_master.buy_name')
                 ->leftjoin('buy_master', 'costing.buy_id', '=', 'buy_master.buy_id')
                 ->where('style_id', '=', $style_id)
                 ->where('season_id', '=', $season_id)
                 ->where('bom_stage_id', '=', $order_id)
                 ->where('costing.status', '<>', 'CANCELLED')
                 ->get();

    $arr['buy_name']  = $buy_name;

    $style_pack_count   = StyleCreation::select('product_feature.count')
                        ->join('product_feature', 'product_feature.product_feature_id', '=','style_creation.product_feature_id')
                        ->where('style_id', '=', $style_id)
                        ->get();

    $arr['pack_count']  = $style_pack_count;

    if($arr == null)
      throw new ModelNotFoundException("Requested section not found", 1);
    else
      return response([ 'data' => $arr ]);
    }


}
