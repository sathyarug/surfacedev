<?php

namespace App\Http\Controllers\Reports;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use App\Libraries\CapitalizeAllFields;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use App\Models\Merchandising\BOMDetails;
use App\Models\Core\Status;
use App\Models\Merchandising\PoOrderHeader;

class POReportController extends Controller
{ 

    public function index(Request $request)
    {
      $type = $request->type;
      if($type == 'datatable') {
          $data = $request->all();
          $this->datatable_search($data);
      }else if($type == 'header') {
          $data = $request->all();
          $this->load_po_header($data);
      }else if($type == 'auto')    {
        $search = $request->search;
        return response($this->autocomplete_search($search));
      }else {
        $active = $request->active;
        $fields = $request->fields;
        return response([
          'data' => $this->list($active , $fields)
        ]);
      }
    }

    private function list($active = 0 , $fields = null)
    {
      $query = null;
      if($fields == null || $fields == '') {
        $query = Status::select('*');
      }
      else{
        $fields = explode(',', $fields);
        $query = Status::select('*')
        ->where('type'  , 'like', '%'.$fields[1].'%' );
      }
      return $query->get();
    }

    private function autocomplete_search($search)
    {
      $po_lists = PoOrderHeader::select('po_id','po_number')
      ->where([['po_number', 'like', '%' . $search . '%'],]) ->get();
      return $po_lists;
    }

    private function datatable_search($data)
    {
      $customer = $data['data']['customer_name']['customer_id'];
      $bom_stage = $data['data']['bom_stage']['bom_stage_id'];
      $style = $data['data']['style_name']['style_no'];
      $item = $data['data']['item_name']['master_id'];
      $supplier = $data['data']['supplier_name']['supplier_id'];
      $cus_po = $data['data']['cuspo_no']['po_no'];
      $sales_order = $data['data']['salesorder_id']['order_code'];
      $pcd_from = $data['pcd_from'];
      $pcd_to = $data['pcd_to'];
      $status = $data['data']['po_status']['status'];

      $query = DB::table('merc_shop_order_header')
      ->join('merc_shop_order_detail','merc_shop_order_header.shop_order_id','=','merc_shop_order_detail.shop_order_id')
      ->join('merc_shop_order_delivery','merc_shop_order_header.shop_order_id','=','merc_shop_order_delivery.shop_order_id')
      ->join("merc_customer_order_details",function($join){
         $join->on("merc_shop_order_delivery.delivery_id","=","merc_customer_order_details.details_id")
              ->on("merc_shop_order_header.fg_id","=","merc_customer_order_details.fng_id");
      })
      ->join('merc_customer_order_header','merc_customer_order_details.order_id','=','merc_customer_order_header.order_id')
      ->join('cust_customer','merc_customer_order_header.order_customer','=','cust_customer.customer_id')
      ->join('style_creation','merc_customer_order_header.order_style','=','style_creation.style_id')
      ->join('item_master AS MAT','merc_shop_order_detail.inventory_part_id','=','MAT.master_id')
      ->join('item_master AS FNG','merc_customer_order_details.fng_id','=','FNG.master_id')
      ->leftJoin('org_color AS STY_COLOUR','FNG.color_id','=','STY_COLOUR.color_id')
      ->leftJoin('org_color AS MAT_COL','MAT.color_id','=','MAT_COL.color_id')
      ->leftJoin('org_size AS MAT_SIZE','MAT.size_id','=','MAT_SIZE.size_id')
      ->leftJoin('org_uom','merc_shop_order_detail.purchase_uom','=','org_uom.uom_id')
      ->leftJoin('org_supplier','merc_shop_order_detail.supplier','=','org_supplier.supplier_id')
      ->join('org_location','merc_customer_order_details.projection_location','=','org_location.loc_id')
      ->join('org_origin_type','merc_shop_order_detail.orign_type_id','=','org_origin_type.origin_type_id')
      ->join('item_category','MAT.category_id','=','item_category.category_id')
      ->join('merc_bom_stage','merc_customer_order_header.order_stage','=','merc_bom_stage.bom_stage_id')
      ->select('merc_shop_order_header.shop_order_id',
      'merc_shop_order_detail.shop_order_detail_id',
      'merc_shop_order_detail.bom_id',
      'merc_shop_order_delivery.delivery_id',
      'merc_customer_order_header.order_stage',
      'merc_bom_stage.bom_stage_description',
      'cust_customer.customer_name',
      'style_creation.style_no',
      'merc_customer_order_header.order_code',
      'merc_customer_order_details.po_no',
      'merc_customer_order_details.pcd',
      'merc_customer_order_details.fng_id',
      'FNG.master_code AS fng_number',
      'MAT.master_code AS mat_code',
      'MAT.master_description',
      'STY_COLOUR.color_id',
      'STY_COLOUR.color_name',
      'MAT_COL.color_id AS material_color_id',
      'MAT_COL.color_name AS material_color',
      'MAT_SIZE.size_id',
      'MAT_SIZE.size_name',
      'org_uom.uom_id',
      'org_uom.uom_code',
      'org_supplier.supplier_id',
      'org_supplier.supplier_name',
      'merc_shop_order_detail.unit_price',
      'merc_customer_order_details.order_qty',
      'merc_shop_order_detail.gross_consumption',
      'MAT.moq',
      'MAT.mcq',
      'org_location.loc_id',
      'org_location.loc_name',
      'merc_shop_order_detail.inventory_part_id',
      'merc_customer_order_details.ship_mode',
      'org_origin_type.origin_type_id',
      'org_origin_type.origin_type',
      'item_category.category_id',
      'item_category.category_name',
      'merc_customer_order_header.order_status',
      DB::raw("(ROUND((merc_customer_order_details.order_qty * merc_shop_order_detail.gross_consumption),4)) AS total_qty"),
      DB::raw("(DATE_FORMAT(merc_customer_order_details.pcd,'%d-%b-%Y')) AS pcd_01"),
      DB::raw("(SELECT COUNT(EX.currency)AS ex_rate FROM org_exchange_rate AS EX WHERE EX.currency = org_supplier.currency) AS ex_rate"),
      DB::raw("(IFNULL(merc_shop_order_detail.po_qty,0)) AS req_qty"),
      DB::raw("(IFNULL(merc_shop_order_detail.po_balance_qty,0)) AS po_balance_qty")
      );
      if($customer!=null || $customer!=""){
        $query->where('cust_customer.customer_id', $customer);
      }
      if($bom_stage!=null || $bom_stage!=""){
        $query->where('merc_customer_order_header.order_stage', $bom_stage);
      }
      if($style!=null || $style!=""){
        $query->where('style_creation.style_no', $style);
      }
      if($supplier!=null || $supplier!=""){
        $query->where('merc_shop_order_detail.supplier', $supplier);
      }
      if($cus_po!=null || $cus_po!=""){
        $query->where('merc_customer_order_details.po_no', $cus_po);
      }
      if($sales_order!=null || $sales_order!=""){
        $query->where('merc_customer_order_header.order_code', $sales_order);
      }
      if($item!=null || $item!=""){
        $query->where('merc_shop_order_detail.inventory_part_id', $item);
      }
      if($pcd_from!=null || $pcd_from!=""){
        $query->whereBetween('merc_customer_order_details.pcd', [date("Y-m-d",strtotime($pcd_from)), date("Y-m-d",strtotime($pcd_to))]);
      }
      if($status!=null || $status!=""){
        $query->where('merc_customer_order_header.order_status', $status);
      }
      $load_list = $query->get();

      echo json_encode([
          "recordsTotal" => "",
          "recordsFiltered" => "",
          "data" => $load_list
      ]);

  }


  private function load_po_header($data)
  {
      $po_no = $data['data']['po_no']['po_number'];
      $supplier = $data['data']['supplier_name']['supplier_id'];
      $po_from = $data['po_from'];
      $po_to = $data['po_to'];
      $status = $data['data']['po_status']['status'];

      $query = DB::table('merc_po_order_header')
      ->join('org_supplier','merc_po_order_header.po_sup_code','=','org_supplier.supplier_id')
      ->join('usr_login','merc_po_order_header.created_by','=','usr_login.user_id')
      ->join('org_location','merc_po_order_header.user_loc_id','=','org_location.loc_id')
      ->select('merc_po_order_header.*',
      'org_supplier.supplier_name',
      'usr_login.user_name',
      DB::raw("(SELECT
        FORMAT(SUM(merc_po_order_details.tot_qty),2)
        FROM
        merc_po_order_details
        WHERE
        merc_po_order_details.po_header_id = merc_po_order_header.po_id) AS total_amount"),
      'org_location.loc_name'
      );

      if($po_no!=null || $po_no!=""){
        $query->where('merc_po_order_header.po_number', $po_no);
      }
      if($supplier!=null || $supplier!=""){
        $query->where('merc_po_order_header.po_sup_code', $supplier);
      }
      if($po_from!=null || $po_from!=""){
        $query->whereBetween('merc_po_order_header.po_date', [date("Y-m-d",strtotime($po_from)), date("Y-m-d",strtotime($po_to))]);        
      }
      if($status!=null || $status!=""){
        $query->where('merc_po_order_header.po_status', $status);
      }
      $load_list = $query->get();

      echo json_encode([
          "recordsTotal" => "",
          "recordsFiltered" => "",
          "data" => $load_list
      ]);

  }


}
