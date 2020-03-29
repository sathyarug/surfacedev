<?php

namespace App\Http\Controllers\Reports;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use App\Libraries\CapitalizeAllFields;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use App\Models\Core\Status;
use App\Models\Store\IssueHeader;
use PDF;

class PickListController extends Controller
{ 

    public function index(Request $request)
    {
      $type = $request->type;
      if($type == 'datatable') {
          $data = $request->all();
          $this->datatable_search($data);
      }else {
        $active = $request->active;
        $fields = $request->fields;
        return response([
          'data' => $this->list($active , $fields)
        ]);
      }
    }

    public function datatable_search($data)
    {
      $mrn = $data['data']['mrn_no']['mrn_id'];
      $issue = $data['data']['issue_no']['issue_no'];
      $customer = $data['data']['customer_name']['customer_id'];
      $style = $data['data']['style_name']['style_id'];
      $location = $data['data']['loc_name']['loc_id'];
      $date_from = $data['date_from'];
      $date_to = $data['date_to'];

      // $catArr = array();
      // if(isset($data['data']['category'])){
      //   foreach($data['data']['category'] as $row){
      //     array_push($catArr,$row['category_id']);
      //   }
      // }

      $query = DB::table('store_issue_header')
      ->join('store_mrn_header','store_issue_header.mrn_id','=','store_mrn_header.mrn_id')
      ->join('style_creation','store_mrn_header.style_id','=','style_creation.style_id')
      ->join('cust_customer','style_creation.customer_id','=','cust_customer.customer_id')
      ->join('usr_login','store_issue_header.created_by','=','usr_login.user_id')
      ->join('org_location','store_issue_header.user_loc_id','=','org_location.loc_id')
      ->select('store_issue_header.issue_no',
        'store_mrn_header.mrn_id',
        'store_mrn_header.mrn_no',
        'store_mrn_header.cut_qty',
        DB::raw("(DATE_FORMAT(store_issue_header.created_date,'%d-%b-%Y')) AS created_date"),
        'cust_customer.customer_name',
        'style_creation.style_no',
        'usr_login.user_name',
        'org_location.loc_name'
      );
      if($mrn!=null || $mrn!=""){
        $query->where('store_mrn_header.mrn_id', $mrn);
      }
      if($customer!=null || $customer!=""){
        $query->where('cust_customer.customer_id', $customer);
      }
      if($style!=null || $style!=""){
        $query->where('style_creation.style_id', $style);
      }
      if($issue!=null || $issue!=""){
        $query->where('store_issue_header.issue_no', $issue);
      }
      if($location!=null || $location!=""){
        $query->where('store_mrn_header.user_loc_id', $location);
      }
      if($date_from!=null || $date_from!=""){
        $query->whereBetween(DB::raw('(DATE_FORMAT(store_mrn_header.created_date,"%Y-%m-%d"))'),[date("Y-m-d",strtotime($date_from)), date("Y-m-d",strtotime($date_to))]);
      }

      $query->orderBy('store_mrn_header.mrn_id','DESC' , 'store_issue_header.issue_no','DESC');
      $data = $query->get();

      echo json_encode([
        "recordsTotal" => "",
        "recordsFiltered" => "",
        "data" => $data
      ]);

    }

    public function update_issue_status(Request $request){

      $mrn=$request->mrn;
      $issue_no=$request->issue;

      $is_exists=IssueHeader::where('mrn_id', $mrn)
      ->where('issue_no', $issue_no)
      ->where('print_status', 'PRINTED')
      ->exists();

      if($is_exists==false){
        $update = IssueHeader::where('mrn_id', $mrn)
        ->where('issue_no', $issue_no)
        ->update(['print_status' => 'PRINTED','print_by' => auth()->payload()['user_id'],'print_date' => date("Y-m-d H:i:s")]);
      }

      echo json_encode([
        "print_status" => $is_exists
      ]);
    }

    public function viewPickList(Request $request)
    {
      $mrn=$request->mrn;
      $issue_no=$request->issue;
      $category=$request->category;
      if($request->print_status==1){
        $is_exists=true;
      }else{
        $is_exists=false;
      }

      $data['company'] = $query = DB::table('store_mrn_header')
      ->join('org_location','store_mrn_header.user_loc_id','=','org_location.loc_id')
      ->join('org_company', 'org_location.company_id', '=', 'org_company.company_id')
      ->join('org_country', 'org_location.country_code', '=', 'org_country.country_id')
      ->select('org_location.*','org_company.company_name','org_country.country_description')
      ->where('store_mrn_header.mrn_id','=',$mrn)
      ->get();

      $data['headers'] = $query = DB::table('store_issue_header')
      ->join('store_mrn_header','store_issue_header.mrn_id','=','store_mrn_header.mrn_id')
      ->join('style_creation','store_mrn_header.style_id','=','style_creation.style_id')
      ->select('store_mrn_header.line_no',
        'style_creation.style_no',
        'store_mrn_header.cut_qty',
        'store_mrn_header.mrn_id',
        'store_mrn_header.mrn_no',
        DB::raw("(SELECT
        item_master.master_code
        FROM
        store_mrn_detail
        INNER JOIN merc_shop_order_header ON store_mrn_detail.shop_order_id = merc_shop_order_header.shop_order_id
        INNER JOIN item_master ON merc_shop_order_header.fg_id = item_master.master_id
        WHERE
        store_mrn_detail.mrn_id = store_mrn_header.mrn_id
        GROUP BY
        item_master.master_code) AS fg_code"),
        DB::raw("(SELECT
        GROUP_CONCAT(DISTINCT merc_customer_order_header.order_code SEPARATOR ' / ')
        FROM
        store_mrn_detail
        INNER JOIN merc_customer_order_details ON store_mrn_detail.cust_order_detail_id = merc_customer_order_details.details_id
        INNER JOIN merc_customer_order_header ON merc_customer_order_details.order_id = merc_customer_order_header.order_id
        WHERE
        store_mrn_detail.mrn_id = store_mrn_header.mrn_id) AS cust_order"),     
        DB::raw("(SELECT
        GROUP_CONCAT(DISTINCT merc_po_order_details.po_no SEPARATOR ' / ') AS po_nos
        FROM
        store_mrn_detail
        INNER JOIN merc_po_order_details ON store_mrn_detail.shop_order_detail_id = merc_po_order_details.shop_order_detail_id
        WHERE
        store_mrn_detail.mrn_id = store_mrn_header.mrn_id
        ) AS sup_po_no"),
        'store_issue_header.issue_no AS issue_id',
        DB::raw("(DATE_FORMAT(store_issue_header.created_date,'%d-%b-%Y')) AS created_date")
      )
      ->where('store_issue_header.mrn_id','=',$mrn)
      ->where('store_issue_header.issue_no','=',$issue_no)
      ->get();

      $fabric = DB::table('store_issue_header')
      ->join('store_issue_detail','store_issue_header.issue_id','=','store_issue_detail.issue_id')
      ->join('org_location','store_issue_detail.location_id','=','org_location.loc_id')    
      ->join('org_store','store_issue_detail.store_id','=','org_store.store_id')
      ->join('org_substore','store_issue_detail.sub_store_id','=','org_substore.substore_id')
      ->join('store_roll_plan','store_issue_detail.item_detail_id','=','store_roll_plan.roll_plan_id')
      ->join('store_fabric_inspection','store_roll_plan.roll_plan_id','=','store_fabric_inspection.roll_plan_id')  
      ->join('item_master','store_issue_detail.item_id','=','item_master.master_id')
      ->join('item_category','item_master.category_id','=','item_category.category_id')
      ->join('org_store_bin','store_roll_plan.bin','=','org_store_bin.store_bin_id')
      ->join('store_mrn_detail','store_issue_detail.mrn_detail_id','=','store_mrn_detail.mrn_detail_id')
      ->join('org_uom','store_mrn_detail.uom','=','org_uom.uom_id')
      ->leftJoin('org_size','item_master.size_id','=','org_size.size_id')
      ->leftJoin('org_color','item_master.color_id','=','org_color.color_id')
      ->select('store_issue_header.issue_id',
      'store_issue_header.issue_no',
      'store_issue_header.mrn_id',
      'store_issue_detail.issue_detail_id',
      'store_issue_detail.mrn_detail_id',
      'store_issue_detail.item_id',
      'store_issue_detail.qty AS issue_qty',
      'store_mrn_detail.requested_qty',
      'org_location.loc_name',
      'org_store.store_name',
      'org_substore.substore_name',
      'item_category.category_code',
      'item_master.master_code',
      'item_master.master_description',
      'store_issue_detail.item_detail_id',
      'item_master.size_id',
      'org_size.size_name',
      'org_color.color_name',
      'org_store_bin.store_bin_name',
      'org_uom.uom_description',
      'store_roll_plan.lot_no',
      'store_roll_plan.batch_no',
      'store_roll_plan.roll_no AS roll_box',
      'store_fabric_inspection.width AS yardage',
      'store_fabric_inspection.shade',
      'store_fabric_inspection.lab_comment'
      )
      ->where('item_category.category_code','=','FAB')
      ->where('store_issue_header.mrn_id','=',$mrn)
      ->where('store_issue_header.issue_no','=',$issue_no);
      if($category!=0){
        $fabric->where('item_master.category_id', $category);
      }

      $trims = DB::table('store_issue_header')
      ->join('store_issue_detail','store_issue_header.issue_id','=','store_issue_detail.issue_id')
      ->join('org_location','store_issue_detail.location_id','=','org_location.loc_id')    
      ->join('org_store','store_issue_detail.store_id','=','org_store.store_id')
      ->join('org_substore','store_issue_detail.sub_store_id','=','org_substore.substore_id')
      ->join('store_trim_packing_detail','store_issue_detail.item_detail_id','=','store_trim_packing_detail.trim_packing_id')
      ->join('item_master','store_issue_detail.item_id','=','item_master.master_id')
      ->join('item_category','item_master.category_id','=','item_category.category_id')
      ->join('org_store_bin','store_trim_packing_detail.bin','=','org_store_bin.store_bin_id')
      ->join('store_mrn_detail','store_issue_detail.mrn_detail_id','=','store_mrn_detail.mrn_detail_id')
      ->join('org_uom','store_mrn_detail.uom','=','org_uom.uom_id')
      ->leftJoin('org_size','item_master.size_id','=','org_size.size_id')
      ->leftJoin('org_color','item_master.color_id','=','org_color.color_id')
      ->select('store_issue_header.issue_id',
      'store_issue_header.issue_no',
      'store_issue_header.mrn_id',
      'store_issue_detail.issue_detail_id',
      'store_issue_detail.mrn_detail_id',
      'store_issue_detail.item_id',
      'store_issue_detail.qty AS issue_qty',
      'store_mrn_detail.requested_qty',
      'org_location.loc_name',
      'org_store.store_name',
      'org_substore.substore_name',
      'item_category.category_code',
      'item_master.master_code',
      'item_master.master_description',
      'store_issue_detail.item_detail_id',
      'item_master.size_id',
      'org_size.size_name',
      'org_color.color_name',
      'org_store_bin.store_bin_name',
      'org_uom.uom_description',
      'store_trim_packing_detail.lot_no',
      'store_trim_packing_detail.batch_no',
      'store_trim_packing_detail.box_no AS roll_box',
      DB::raw("(NULL) AS yardage"),
      DB::raw("(NULL) AS shade"),
      DB::raw("(NULL) AS lab_comment")
      )
      ->where('item_category.category_code','<>','FAB')
      ->where('store_issue_header.mrn_id','=',$mrn)
      ->where('store_issue_header.issue_no','=',$issue_no);
      if($category!=0){
        $trims->where('item_master.category_id', $category);
      }
      $trims->unionAll($fabric);

      $data['details'] = $trims->get();

      $config = [
        // 'format' => 'A4',
        'orientation' => 'L', //L-landscape
        'watermark' => 'Duplicate',
        'show_watermark' => $is_exists,
      ];

      if(sizeof($data['headers'])>0 && sizeof($data['details'])>0){
        $pdf = PDF::loadView('reports/pick-list', $data, [], $config)
        ->stream('Pick List -'.$request->mrn.'.pdf');
        return $pdf;
      }else{
        return View('reports/error');
      }

    }
    

}
