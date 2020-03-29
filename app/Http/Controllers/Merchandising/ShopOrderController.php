<?php

namespace App\Http\Controllers\Merchandising;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\DB;

use App\Http\Controllers\Controller;
use App\Models\Merchandising\CustomerOrder;
use App\Models\Merchandising\CustomerOrderDetails;

use App\Models\Merchandising\Costing\Costing;
use App\Models\Merchandising\StyleCreation;
use App\Models\Merchandising\BOMHeader;
use App\Libraries\SearchQueryBuilder;

use App\Models\Merchandising\ShopOrderHeader;
use App\Models\Merchandising\ShopOrderDetail;
use App\Models\Merchandising\ShopOrderDelivery;
use App\Models\Merchandising\ShopOrderDetailsHistory;


class ShopOrderController extends Controller
{
    public function __construct()
    {
      //add functions names to 'except' paramert to skip authentication
      $this->middleware('jwt.verify', ['except' => ['index']]);
    }

    //get customer list
    public function index(Request $request)
    {
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
      else if($type == 'search_fields'){
        return response([
          'data' => $this->get_search_fields()
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

    }


    //get a customer
    public function show($id)
    {

    }


    //update a customer
    public function update(Request $request, $id)
    {

    }


    //deactivate a customer
    public function destroy($id)
    {

    }


    //validate anything based on requirements
    public function validate_data(Request $request)
    {

    }




    //check customer code already exists
    private function validate_duplicate_code($id , $code)
    {

    }


    //search customer for autocomplete
    private function autocomplete_search($search)
  	{
      //dd($search);
      $shopOrder = ShopOrderHeader::select('shop_order_id')
      ->where([['shop_order_id', 'like', '%' . $search . '%'],]) ->get();
      return $shopOrder;
  	}


    //search customer for autocomplete
    private function style_search($search)
  	{
  		$shopOrder_lists = CustomerOrderDetails::select('item_master.master_id', 'item_master.master_code','item_master.master_description')
      ->join('item_master', 'merc_customer_order_details.fng_id', '=', 'item_master.master_id')
  		->where([['master_code', 'like', '%' . $search . '%'],])
      ->get();

  		return $shopOrder_lists;
  	}

    public function load_shop_order_header(Request $request){

      $shop_order_id  = $request->so_id;
      $so_details     = ShopOrderHeader::find($shop_order_id);
      $fng_id         = $so_details['fg_id'];

      $load_header = ShopOrderHeader::select('item_master.master_description', 'org_country.country_description', 'merc_bom_stage.bom_stage_description', 'merc_customer_order_details.order_qty', 'merc_customer_order_details.planned_qty', 'merc_shop_order_header.order_status','item_master.master_id','merc_shop_order_header.shop_order_id','item_master.master_code',
                     DB::raw("DATE_FORMAT(merc_customer_order_details.planned_delivery_date, '%d-%b-%Y') 'delivery_date'"))
                   ->join('merc_shop_order_delivery', 'merc_shop_order_header.shop_order_id', '=', 'merc_shop_order_delivery.shop_order_id')
                   ->join('merc_customer_order_details', 'merc_shop_order_delivery.delivery_id', '=', 'merc_customer_order_details.details_id')
                   ->join('item_master', 'merc_shop_order_header.fg_id', '=', 'item_master.master_id')
                   ->join('org_country', 'merc_customer_order_details.country', '=', 'org_country.country_id')
                   ->join('merc_customer_order_header', 'merc_customer_order_details.order_id', '=', 'merc_customer_order_header.order_id')
                   ->join('merc_bom_stage', 'merc_customer_order_header.order_stage', '=', 'merc_bom_stage.bom_stage_id')
                   ->where('merc_shop_order_header.fg_id', '=', $fng_id)
                   ->where('merc_shop_order_header.shop_order_id', '=', $shop_order_id)
                   ->where('merc_shop_order_header.status', '=',1)
                   ->get();

      $arr['header_data'] = $load_header;

      $load_details = ShopOrderHeader::select('merc_shop_order_detail.actual_qty','merc_shop_order_detail.actual_consumption','merc_shop_order_detail.required_qty','merc_shop_order_detail.shop_order_detail_id','merc_shop_order_detail.shop_order_id','product_component.product_component_description','item_master.master_code','item_master.master_description','IUOM.uom_code AS inv_uom','PUOM.uom_code AS pur_uom','org_supplier.supplier_name','merc_shop_order_detail.unit_price','merc_shop_order_detail.purchase_price','item_master.supplier_reference as article_no'
                      ,'merc_position.position','merc_shop_order_detail.net_consumption','merc_shop_order_detail.wastage','merc_shop_order_detail.gross_consumption','merc_shop_order_header.order_qty','merc_shop_order_detail.po_qty as po_qty','merc_shop_order_detail.asign_qty as grn_qty','merc_shop_order_detail.mrn_qty','merc_shop_order_detail.issue_qty as issued_qty','bom_details.sfg_code','sfg_colour.color_name')
                   ->join('merc_shop_order_delivery', 'merc_shop_order_header.shop_order_id', '=', 'merc_shop_order_delivery.shop_order_id')
                   ->join('merc_customer_order_details', 'merc_shop_order_delivery.delivery_id', '=', 'merc_customer_order_details.details_id')
                   ->join('merc_customer_order_header', 'merc_customer_order_details.order_id', '=', 'merc_customer_order_header.order_id')
                   ->join('merc_shop_order_detail', 'merc_shop_order_header.shop_order_id', '=', 'merc_shop_order_detail.shop_order_id')
                   ->join('bom_details', 'merc_shop_order_detail.bom_detail_id', '=', 'bom_details.bom_detail_id')
                   ->leftjoin('item_master AS sfg', 'bom_details.sfg_id', '=', 'sfg.master_id')
                   ->leftjoin('org_color AS sfg_colour', 'sfg.color_id', '=', 'sfg_colour.color_id')
                   ->join('product_component', 'merc_shop_order_detail.component_id', '=', 'product_component.product_component_id')
                   ->join('item_master', 'merc_shop_order_detail.inventory_part_id', '=', 'item_master.master_id')
                   ->join('org_uom AS IUOM', 'item_master.inventory_uom', '=', 'IUOM.uom_id')
                   ->join('org_uom AS PUOM', 'merc_shop_order_detail.purchase_uom', '=', 'PUOM.uom_id')
                   ->join('org_supplier', 'merc_shop_order_detail.supplier', '=', 'org_supplier.supplier_id')
                   ->leftjoin('merc_position', 'merc_shop_order_detail.postion_id', '=', 'merc_position.position_id')
                   ->where('merc_shop_order_header.shop_order_id', '=', $shop_order_id)
                   ->where('merc_shop_order_header.status', '=',1)
                   ->get();

      $arr['details_data'] = $load_details;
      $arr['details_count'] = sizeof($load_details);


      $load_history = ShopOrderHeader::select('item_master.master_code','item_master.master_description','IUOM.uom_code AS inv_uom','PUOM.uom_code AS pur_uom','merc_shop_order_detail_history.*','merc_shop_order_header.*',
      DB::raw("DATE_FORMAT(merc_shop_order_detail_history.created_date, '%d-%b-%Y %h:%m:%s') AS soh_date" ) )
                   ->join('merc_shop_order_detail_history', 'merc_shop_order_detail_history.shop_order_id', '=', 'merc_shop_order_header.shop_order_id')
                   ->join('merc_shop_order_detail', 'merc_shop_order_detail.shop_order_detail_id', '=', 'merc_shop_order_detail_history.shop_order_detail_id')
                   ->join('item_master', 'merc_shop_order_detail.inventory_part_id', '=', 'item_master.master_id')
                   ->join('org_uom AS IUOM', 'item_master.inventory_uom', '=', 'IUOM.uom_id')
                   ->join('org_uom AS PUOM', 'merc_shop_order_detail.purchase_uom', '=', 'PUOM.uom_id')
                   ->where('merc_shop_order_header.shop_order_id', '=', $shop_order_id)
                   ->where('merc_shop_order_header.status', '=',1)
                   ->get();

      $arr['history_data'] = $load_history;
      $arr['history_count'] = sizeof($load_history);

      $load_sales_order = CustomerOrderDetails::select('*')
                   ->join('merc_customer_order_header', 'merc_customer_order_details.order_id', '=', 'merc_customer_order_header.order_id')
                   ->where('shop_order_id', '=', $shop_order_id)
                   ->where('active_status', '=','ACTIVE')
                   ->where('delivery_status', '=','RELEASED')
                   ->where('type_created', '=','CREATE')
                   ->get();

      if(sizeof($load_sales_order)==0){

        $load_split = CustomerOrderDetails::select('*')
                     ->join('merc_customer_order_header', 'merc_customer_order_details.order_id', '=', 'merc_customer_order_header.order_id')
                     ->where('fng_id', '=', $fng_id)
                     ->where('active_status', '=','ACTIVE')
                     ->where('delivery_status', '=','RELEASED')
                     ->where('type_created', '=','GFS')
                     ->get();

        $arr['sales_order'] = $load_split;
        $arr['sales_order_count'] = sizeof($load_split);
      }else{

        $arr['sales_order'] = $load_sales_order;
        $arr['sales_order_count'] = sizeof($load_sales_order);
      }
      //echo sizeof($load_sales_order);die();
      //dd($load_sales_order) ;



      if($arr == null)
          throw new ModelNotFoundException("Requested section not found", 1);
      else
          return response([ 'data' => $arr ]);

    }


    public function load_shop_order_list(Request $request){

      $fng_id = $request->fng_id;

      $so_list = ShopOrderHeader::select('*')
                   ->where('fg_id', '=', $fng_id)
                   ->where('status', '=',1)
                   ->get();

      $arr['so_list'] = $so_list;

      if($arr == null)
          throw new ModelNotFoundException("Requested section not found", 1);
      else
          return response([ 'data' => $arr ]);

    }


    public function update_shop_order_details(Request $request){
      $lines          = $request->lines;
      $shop_order_id  = $request->so_id;
      //$so_details     = ShopOrderHeader::find($shop_order_id);
      $so_details = ShopOrderHeader::select('item_master.master_id','item_master.master_code')
                   ->join('item_master', 'merc_shop_order_header.fg_id', '=', 'item_master.master_id')
                   ->where('shop_order_id', '=', $shop_order_id)
                   ->get();
      //dd($so_details[0]['master_id']);
      $fng_id         = $so_details[0]['master_id'];
      $fng_code       = $so_details[0]['master_code'];

      $max_no = ShopOrderDetailsHistory::where('shop_order_id','=',$shop_order_id)->max('version');
	    if($max_no == NULL){ $max_no= 0;}

      if($lines != null && sizeof($lines) >= 1){
      for($y = 0 ; $y < sizeof($lines) ; $y++){

        ShopOrderDetail::where('shop_order_detail_id', $lines[$y]['shop_order_detail_id'])
        ->update(['required_qty' => $lines[$y]['required_qty'],
                  'actual_consumption' => $lines[$y]['actual_con'],
                  'actual_qty' => $lines[$y]['actul_qty'] ]);

        $so_history = new ShopOrderDetailsHistory();

        $so_history->shop_order_id        = $shop_order_id;
        $so_history->shop_order_detail_id = $lines[$y]['shop_order_detail_id'];
        $so_history->master_id            = $fng_id;
        $so_history->master_code          = $fng_code;
        $so_history->required_qty         = $lines[$y]['required_qty'];
        $so_history->gross_consumption    = $lines[$y]['gross_consumption'];
        $so_history->actual_consumption   = $lines[$y]['actual_con'];
        $so_history->actual_qty           = $lines[$y]['actul_qty'];
        $so_history->version              = $max_no + 1;
        $so_history->save();

      }

      return response([
              'data' => [
              'status' => 'success',
              'message' => 'Update successfully.'
          ]
         ] , 200);
    }


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
      //$user = auth()->user();



      $customer_list = ShopOrderHeader::join('item_master', 'merc_shop_order_header.fg_id', '=', 'item_master.master_id')
	    ->join('merc_shop_order_delivery', 'merc_shop_order_header.shop_order_id', '=', 'merc_shop_order_delivery.shop_order_del_id')
      ->join('bom_header', 'bom_header.fng_id', '=', 'merc_shop_order_header.fg_id')
      ->join('merc_customer_order_details', 'merc_shop_order_delivery.delivery_id', '=', 'merc_customer_order_details.details_id')
      ->join('merc_customer_order_header', 'merc_customer_order_details.order_id', '=', 'merc_customer_order_header.order_id')
      ->join('style_creation', 'style_creation.style_id', '=', 'merc_customer_order_header.order_style')
      ->join('merc_bom_stage', 'merc_customer_order_header.order_stage', '=', 'merc_bom_stage.bom_stage_id')
      ->join('org_country', 'merc_customer_order_details.country', '=', 'org_country.country_id')
	    ->select('merc_shop_order_header.order_status','merc_shop_order_header.shop_order_id','item_master.master_code','item_master.master_id',
          'merc_bom_stage.bom_stage_description','org_country.country_description', 'style_creation.style_no', 'bom_header.bom_id', 'bom_header.costing_id')
      //->Where('merc_po_order_header.created_by','=', $user->user_id)
      ->Where(function ($query) use ($search) {
  			$query->orWhere('merc_shop_order_header.shop_order_id', 'like', $search.'%')
  				    ->orWhere('item_master.master_code', 'like', $search.'%')
  				    ->orWhere('merc_bom_stage.bom_stage_description', 'like', $search.'%')
              ->orWhere('org_country.country_description', 'like', $search.'%')
              ->orWhere('style_creation.style_no', 'like', $search.'%')
              ->orWhere('bom_header.bom_id', 'like', $search.'%')
              ->orWhere('bom_header.costing_id', 'like', $search.'%');
  		        })
      ->orderBy($order_column, $order_type)
      ->offset($start)->limit($length)->get();

      $customer_count = ShopOrderHeader::join('item_master', 'merc_shop_order_header.fg_id', '=', 'item_master.master_id')
      ->join('merc_shop_order_delivery', 'merc_shop_order_header.shop_order_id', '=', 'merc_shop_order_delivery.shop_order_del_id')
      ->join('bom_header', 'bom_header.fng_id', '=', 'merc_shop_order_header.fg_id')
      ->join('merc_customer_order_details', 'merc_shop_order_delivery.delivery_id', '=', 'merc_customer_order_details.details_id')
      ->join('merc_customer_order_header', 'merc_customer_order_details.order_id', '=', 'merc_customer_order_header.order_id')
      ->join('style_creation', 'style_creation.style_id', '=', 'merc_customer_order_header.order_style')
      ->join('merc_bom_stage', 'merc_customer_order_header.order_stage', '=', 'merc_bom_stage.bom_stage_id')
      ->join('org_country', 'merc_customer_order_details.country', '=', 'org_country.country_id')
	    ->select('merc_shop_order_header.order_status','merc_shop_order_header.shop_order_id','item_master.master_code','item_master.master_id',
          'merc_bom_stage.bom_stage_description','org_country.country_description', 'style_creation.style_no', 'bom_header.bom_id', 'bom_header.costing_id')
      //->Where('merc_po_order_header.created_by','=', $user->user_id)
      ->Where(function ($query) use ($search) {
  			$query->orWhere('merc_shop_order_header.shop_order_id', 'like', $search.'%')
  				    ->orWhere('item_master.master_code', 'like', $search.'%')
  				    ->orWhere('merc_bom_stage.bom_stage_description', 'like', $search.'%')
              ->orWhere('org_country.country_description', 'like', $search.'%')
              ->orWhere('style_creation.style_no', 'like', $search.'%')
              ->orWhere('bom_header.bom_id', 'like', $search.'%')
              ->orWhere('bom_header.costing_id', 'like', $search.'%');
  		        })
      ->count();



      return [
          "draw" => $draw,
          "recordsTotal" => $customer_count,
          "recordsFiltered" => $customer_count,
          "data" => $customer_list
      ];
    }


    private function get_search_fields()
    {

    }

    //get filtered fields only
    private function list($active = 0 , $fields = null)
    {

    }




}
