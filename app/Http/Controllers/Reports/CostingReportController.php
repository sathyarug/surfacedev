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
use App\Models\Merchandising\Costing\Costing;
use App\Models\Merchandising\Costing\CostingHistory;
use PDF;

class CostingReportController extends Controller
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
        $query = Status::select($fields);
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
      $style = $data['data']['style_name']['style_id'];
      $status = $data['data']['costing_status']['status'];
      $date_from = $data['date_from'];
      $date_to = $data['date_to'];
      $location = $data['data']['loc_name']['loc_id'];
      $costing_id = $data['data']['costing_id']['id'];
      
      $query = DB::table('costing')
      ->join('merc_bom_stage','costing.bom_stage_id','=','merc_bom_stage.bom_stage_id')
      ->join('style_creation','costing.style_id','=','style_creation.style_id')
      ->join('org_season','costing.season_id','=','org_season.season_id')
      ->join('org_location','costing.user_loc_id','=','org_location.loc_id')
      ->join('usr_login','costing.created_by','=','usr_login.user_id')
      ->join('cust_customer','style_creation.customer_id','=','cust_customer.customer_id')
      ->join('prod_category','style_creation.product_category_id','=','prod_category.prod_cat_id')
      ->select('costing.id',
        'costing.revision_no',
        'merc_bom_stage.bom_stage_description',
        'style_creation.style_no',
        'costing.sc_no',
        'org_season.season_name',
        'costing.status AS costing_status',
        'total_order_qty',
        'total_smv',
        'org_location.loc_name',
        DB::raw("(SELECT
        CONCAT(usr_login.user_name,' | ',approval_date) AS app_by_date
        FROM
        app_process_approval
        INNER JOIN app_process_approval_stage_users ON app_process_approval.id = app_process_approval_stage_users.id
        INNER JOIN usr_login ON app_process_approval_stage_users.user_id = usr_login.user_id
        WHERE
        app_process_approval.process = 'COSTING'
        AND app_process_approval.document_id = costing.id
        AND app_process_approval_stage_users.approval_stage_id = 
        (
        SELECT
        Count(app_process_approval.template_id) AS stage_count
        FROM
        app_process_approval
        INNER JOIN app_approval_template_stages ON app_process_approval.template_id = app_approval_template_stages.template_id
        WHERE
        app_approval_template_stages.template_id = app_process_approval.template_id
        AND app_process_approval.process = 'COSTING'
        AND app_process_approval.document_id = costing.id
        )) AS app_by_date"),
        'usr_login.user_name',
        'cust_customer.customer_name',
        'prod_category.prod_cat_description',
         DB::raw("DATE_FORMAT(costing.created_date, '%d-%b-%Y') 'created_date'")
      );
      if($style!=null || $style!=""){
        $query->where('style_creation.style_id', $style);
      }
      if($customer!=null || $customer!=""){
        $query->where('cust_customer.customer_id', $customer);
      }
      if($location!=null || $location!=""){
        $query->where('costing.user_loc_id', $location);
      }
      if($status!=null || $status!=""){
        $query->where('costing.status', $status);
      }
      if($costing_id!=null || $costing_id!=""){
        $query->where('costing.id', $costing_id);
      }
      if($date_from!=null || $date_from!=""){
        $query->whereBetween(DB::raw('(DATE_FORMAT(costing.created_date,"%Y-%m-%d"))'),[date("Y-m-d",strtotime($date_from)), date("Y-m-d",strtotime($date_to))]);
      }

      $load_list = $query->get();

      echo json_encode([
          "recordsTotal" => "",
          "recordsFiltered" => "",
          "data" => $load_list
      ]);

  }

  public function viewCostingDetails(Request $request)
  {
      $costing_id=$request->ci;
      
      $data['company'] = Costing::join('org_location', 'costing.user_loc_id', '=', 'org_location.loc_id')
      ->join('org_company', 'org_location.company_id', '=', 'org_company.company_id')
      ->join('org_country', 'org_location.country_code', '=', 'org_country.country_id')
      ->select('org_location.*','org_company.company_name','org_country.country_description')
      ->where('costing.id','=',$costing_id)
      ->get();

      $data['headers'] = Costing::join('style_creation', 'costing.style_id', '=', 'style_creation.style_id')
      ->join('cust_customer', 'style_creation.customer_id', '=', 'cust_customer.customer_id')
      ->join('cust_division', 'style_creation.division_id', '=', 'cust_division.division_id')
      ->join('org_season', 'costing.season_id', '=', 'org_season.season_id')
      ->join('usr_login', 'costing.created_by', '=', 'usr_login.user_id')
      ->join('merc_bom_stage', 'costing.bom_stage_id', '=' ,'merc_bom_stage.bom_stage_id')
      ->join('merc_color_options', 'costing.color_type_id', '=' ,'merc_color_options.col_opt_id')
      ->leftJoin('buy_master', 'costing.buy_id', '=' ,'buy_master.buy_id')
      ->leftJoin('costing_design_source', 'costing.design_source_id', '=' ,'costing_design_source.design_source_id')
      ->select('costing.*',
          'style_creation.style_no',
          'cust_customer.customer_name',
          'cust_division.division_description',
          'org_season.season_name',
          'usr_login.user_name',
          'merc_bom_stage.bom_stage_description',
          'merc_color_options.color_option',
          'buy_master.buy_name',
          'costing_design_source.design_source_name',
          'style_creation.style_description',
          'style_creation.image',
          DB::raw("DATE_FORMAT(costing.created_date, '%d-%b-%Y') 'created_date2'"),
          DB::raw("(SELECT
          org_cancellation_category.category_description
          FROM
          org_cancellation_category
          WHERE
          org_cancellation_category.category_id = costing.upcharge_reason
          AND org_cancellation_category.category_code = 'COSTING') AS upcharge_reason"),
          DB::raw("(SELECT
          DATE_FORMAT(app_process_approval_stage_users.approval_date,'%d-%b-%Y')
          FROM
          app_process_approval
          INNER JOIN app_process_approval_stage_users ON app_process_approval.id = app_process_approval_stage_users.id
          INNER JOIN usr_login ON app_process_approval_stage_users.user_id = usr_login.user_id
          WHERE
          app_process_approval.process = 'COSTING'
          AND app_process_approval.document_id = 1
          ORDER BY approval_date DESC
          LIMIT 1) AS last_app_by")
        )
      ->where('costing.id','=',$costing_id)
      ->get();

      $data['categories'] = Costing::join('costing_items', 'costing.id', '=' ,'costing_items.costing_id')
      ->join('item_master', 'costing_items.inventory_part_id', '=' ,'item_master.master_id')
      ->join('item_category', 'item_master.category_id', '=' ,'item_category.category_id')
      ->select('item_master.category_id','item_category.category_name')
      ->where('costing.id','=',$costing_id)
      ->groupBy('item_master.category_id')
      ->get();

      $data['details'] = Costing::join('costing_items', 'costing.id', '=' ,'costing_items.costing_id')
      ->join('org_uom', 'costing_items.purchase_uom_id', '=' ,'org_uom.uom_id')
      ->join('org_origin_type', 'costing_items.origin_type_id', '=' ,'org_origin_type.origin_type_id')
      ->join('item_master', 'costing_items.inventory_part_id', '=' ,'item_master.master_id')
      ->join('product_component', 'costing_items.product_component_id', '=' ,'product_component.product_component_id')
      ->select('costing.id',
        'costing_items.inventory_part_id AS item_id',
        'item_master.category_id',
        'item_master.master_description',
        'costing_items.unit_price',
        'costing_items.net_consumption',
        'costing_items.wastage',
        'costing_items.gross_consumption',
        'costing_items.meterial_type',
        'costing_items.freight_charges',
        'costing_items.surcharge',
        'costing_items.total_cost',
        'costing_items.lead_time',
        'org_uom.uom_description',
        'org_origin_type.origin_type',
        'product_component.product_component_description'
        )
      ->where('costing.id','=',$costing_id)
      ->orderBy('item_master.category_id','ASC')
      ->get();

      $data['ratios'] = Costing::join('costing_items', 'costing.id', '=' ,'costing_items.costing_id')
      ->join('item_master', 'costing_items.inventory_part_id', '=' ,'item_master.master_id')
      ->join('item_category', 'item_master.category_id', '=' ,'item_category.category_id')
      ->select('item_category.category_name',
        DB::raw("(SUM(costing_items.total_cost)) AS cat_sum"),
        DB::raw("(SELECT
        Sum(costing_items.total_cost)
        FROM
        costing_items
        INNER JOIN item_master ON costing_items.inventory_part_id = item_master.master_id
        INNER JOIN item_category ON item_master.category_id = item_category.category_id
        WHERE
        costing_items.costing_id = costing_items.costing_id) AS tot_sum")
        )
      ->where('costing.id','=',$costing_id)
      ->groupBy('item_master.category_id')
      ->get();


      if(sizeof($data['headers'])>0 && sizeof($data['details'])>0){
        $pdf = PDF::loadView('reports/costing',$data)
        ->stream('Cost Sheet CI-'.$request->costing_id.'.pdf');
        return $pdf;
      }else{
        return View('reports/error');
      }

      //->download('Cost Sheet - CI'.$request->costing_id.'.pdf');
      //return View('reports/costing',$data);
      
  }


  public function viewCostingVersionDetails(Request $request)
  {
      $costing_id=$request->ci;
      $version=$request->version;

      $data['company'] = Costing::join('org_location', 'costing.user_loc_id', '=', 'org_location.loc_id')
      ->join('org_company', 'org_location.company_id', '=', 'org_company.company_id')
      ->join('org_country', 'org_location.country_code', '=', 'org_country.country_id')
      ->select('org_location.*','org_company.company_name','org_country.country_description')
      ->where('costing.id','=',$costing_id)
      ->get();

      $data['headers'] = Costing::join('style_creation', 'costing.style_id', '=', 'style_creation.style_id')
      ->join('cust_customer', 'style_creation.customer_id', '=', 'cust_customer.customer_id')
      ->join('cust_division', 'style_creation.division_id', '=', 'cust_division.division_id')
      ->join('org_season', 'costing.season_id', '=', 'org_season.season_id')
      ->join('usr_login', 'costing.created_by', '=', 'usr_login.user_id')
      ->join('merc_bom_stage', 'costing.bom_stage_id', '=' ,'merc_bom_stage.bom_stage_id')
      ->join('merc_color_options', 'costing.color_type_id', '=' ,'merc_color_options.col_opt_id')
      ->leftJoin('buy_master', 'costing.buy_id', '=' ,'buy_master.buy_id')
      ->leftJoin('costing_design_source', 'costing.design_source_id', '=' ,'costing_design_source.design_source_id')
      ->select('costing.*',
          'style_creation.style_no',
          'cust_customer.customer_name',
          'cust_division.division_description',
          'org_season.season_name',
          'usr_login.user_name',
          'merc_bom_stage.bom_stage_description',
          'merc_color_options.color_option',
          'buy_master.buy_name',
          'costing_design_source.design_source_name',
          'style_creation.style_description',
          'style_creation.image',
          DB::raw("DATE_FORMAT(costing.created_date, '%d-%b-%Y') 'created_date2'"),
          DB::raw("(SELECT
          org_cancellation_category.category_description
          FROM
          org_cancellation_category
          WHERE
          org_cancellation_category.category_id = costing.upcharge_reason
          AND org_cancellation_category.category_code = 'COSTING') AS upcharge_reason"),
          DB::raw("(SELECT
          DATE_FORMAT(app_process_approval_stage_users.approval_date,'%d-%b-%Y')
          FROM
          app_process_approval
          INNER JOIN app_process_approval_stage_users ON app_process_approval.id = app_process_approval_stage_users.id
          INNER JOIN usr_login ON app_process_approval_stage_users.user_id = usr_login.user_id
          WHERE
          app_process_approval.process = 'COSTING'
          AND app_process_approval.document_id = 1
          ORDER BY approval_date DESC
          LIMIT 1) AS last_app_by")
        )
      ->where('costing.id','=',$costing_id)
      ->get();

      $data['details'] = Costing::join('costing_items', 'costing.id', '=' ,'costing_items.costing_id')
      ->join('org_uom', 'costing_items.purchase_uom_id', '=' ,'org_uom.uom_id')
      ->join('org_origin_type', 'costing_items.origin_type_id', '=' ,'org_origin_type.origin_type_id')
      ->join('item_master', 'costing_items.inventory_part_id', '=' ,'item_master.master_id')
      ->join('product_component', 'costing_items.product_component_id', '=' ,'product_component.product_component_id')
      ->select('costing.id',
        'costing.revision_no',
        'costing_items.inventory_part_id AS item_id',
        'item_master.category_id',
        'item_master.master_description',
        'costing_items.unit_price',
        'costing_items.net_consumption',
        'costing_items.wastage',
        'costing_items.gross_consumption',
        'costing_items.meterial_type',
        'costing_items.freight_charges',
        'costing_items.surcharge',
        'costing_items.total_cost',
        'costing_items.lead_time',
        'org_uom.uom_description',
        'org_origin_type.origin_type',
        'product_component.product_component_description'
        )
      ->where('costing.id','=',$costing_id)
      ->orderBy('item_master.category_id','ASC')
      ->get();

      $data['categories'] = Costing::join('costing_items', 'costing.id', '=' ,'costing_items.costing_id')
      ->join('item_master', 'costing_items.inventory_part_id', '=' ,'item_master.master_id')
      ->join('item_category', 'item_master.category_id', '=' ,'item_category.category_id')
      ->select('item_master.category_id','item_category.category_name')
      ->where('costing.id','=',$costing_id)
      ->groupBy('item_master.category_id')
      ->get();

      $data['ratios'] = Costing::join('costing_items', 'costing.id', '=' ,'costing_items.costing_id')
      ->join('item_master', 'costing_items.inventory_part_id', '=' ,'item_master.master_id')
      ->join('item_category', 'item_master.category_id', '=' ,'item_category.category_id')
      ->select('item_category.category_name',
        DB::raw("(SUM(costing_items.total_cost)) AS cat_sum"),
        DB::raw("(SELECT
        Sum(costing_items.total_cost)
        FROM
        costing_items
        INNER JOIN item_master ON costing_items.inventory_part_id = item_master.master_id
        INNER JOIN item_category ON item_master.category_id = item_category.category_id
        WHERE
        costing_items.costing_id = costing_items.costing_id) AS tot_sum")
        )
      ->where('costing.id','=',$costing_id)
      ->groupBy('item_master.category_id')
      ->get();

      ///////////////////  Version query //////////////////////////
      $data['pre_headers'] = CostingHistory::join('style_creation', 'costing_history.style_id', '=', 'style_creation.style_id')
      ->join('cust_customer', 'style_creation.customer_id', '=', 'cust_customer.customer_id')
      ->join('cust_division', 'style_creation.division_id', '=', 'cust_division.division_id')
      ->join('org_season', 'costing_history.season_id', '=', 'org_season.season_id')
      ->join('usr_login', 'costing_history.created_by', '=', 'usr_login.user_id')
      ->join('merc_bom_stage', 'costing_history.bom_stage_id', '=' ,'merc_bom_stage.bom_stage_id')
      ->join('merc_color_options', 'costing_history.color_type_id', '=' ,'merc_color_options.col_opt_id')
      ->leftJoin('buy_master', 'costing_history.buy_id', '=' ,'buy_master.buy_id')
      ->leftJoin('costing_design_source', 'costing_history.design_source_id', '=' ,'costing_design_source.design_source_id')
      ->select('costing_history.*',
          'style_creation.style_no',
          'cust_customer.customer_name',
          'cust_division.division_description',
          'org_season.season_name',
          'usr_login.user_name',
          'merc_bom_stage.bom_stage_description',
          'merc_color_options.color_option',
          'buy_master.buy_name',
          'costing_design_source.design_source_name',
          'style_creation.style_description',
          'style_creation.image',
          DB::raw("DATE_FORMAT(costing_history.created_date, '%d-%b-%Y') 'created_date2'"),
          DB::raw("(SELECT
          org_cancellation_category.category_description
          FROM
          org_cancellation_category
          WHERE
          org_cancellation_category.category_id = costing_history.upcharge_reason
          AND org_cancellation_category.category_code = 'COSTING') AS upcharge_reason"),
          DB::raw("(SELECT
          DATE_FORMAT(app_process_approval_stage_users.approval_date,'%d-%b-%Y')
          FROM
          app_process_approval
          INNER JOIN app_process_approval_stage_users ON app_process_approval.id = app_process_approval_stage_users.id
          INNER JOIN usr_login ON app_process_approval_stage_users.user_id = usr_login.user_id
          WHERE
          app_process_approval.process = 'COSTING'
          AND app_process_approval.document_id = 1
          ORDER BY approval_date DESC
          LIMIT 1) AS last_app_by")
        )
      ->where('costing_history.id','=',$costing_id)
      ->where('costing_history.revision_no','=',$version)
      ->get();

      $data['pre_details'] = CostingHistory::join('costing_items_history', 'costing_history.id', '=' ,'costing_items_history.costing_id')
      ->join('org_uom', 'costing_items_history.purchase_uom_id', '=' ,'org_uom.uom_id')
      ->join('org_origin_type', 'costing_items_history.origin_type_id', '=' ,'org_origin_type.origin_type_id')
      ->join('item_master', 'costing_items_history.inventory_part_id', '=' ,'item_master.master_id')
      ->join('product_component', 'costing_items_history.product_component_id', '=' ,'product_component.product_component_id')
      ->select('costing_history.id',
        'costing_history.revision_no',
        'costing_items_history.inventory_part_id AS item_id',
        'item_master.category_id',
        'item_master.master_description',
        'costing_items_history.unit_price',
        'costing_items_history.net_consumption',
        'costing_items_history.wastage',
        'costing_items_history.gross_consumption',
        'costing_items_history.meterial_type',
        'costing_items_history.freight_charges',
        'costing_items_history.surcharge',
        'costing_items_history.total_cost',
        'costing_items_history.lead_time',
        'org_uom.uom_description',
        'org_origin_type.origin_type',
        'product_component.product_component_description'
      )
      ->where('costing_history.id','=',$costing_id)
      ->where('costing_history.revision_no','=',$version)
      ->orderBy('item_master.category_id','ASC')
      ->get();

      $data['removed_rows'] = CostingHistory::join('costing_items_history', 'costing_history.id', '=' ,'costing_items_history.costing_id')
      ->join('org_uom', 'costing_items_history.purchase_uom_id', '=' ,'org_uom.uom_id')
      ->join('org_origin_type', 'costing_items_history.origin_type_id', '=' ,'org_origin_type.origin_type_id')
      ->join('item_master', 'costing_items_history.inventory_part_id', '=' ,'item_master.master_id')
      ->select('costing_history.id',
        'costing_history.revision_no',
        'costing_items_history.inventory_part_id AS item_id',
        'item_master.category_id',
        'item_master.master_description',
        'costing_items_history.unit_price',
        'costing_items_history.net_consumption',
        'costing_items_history.wastage',
        'costing_items_history.gross_consumption',
        'costing_items_history.meterial_type',
        'costing_items_history.freight_charges',
        'costing_items_history.surcharge',
        'costing_items_history.total_cost',
        'costing_items_history.lead_time',
        'org_uom.uom_description',
        'org_origin_type.origin_type'
      )
      ->where('costing_history.id','=',$costing_id)
      ->where('costing_history.revision_no','=',$version)
      ->whereNotIn('costing_items_history.inventory_part_id' , function($notIn) use ($costing_id){
        $notIn->select('costing_items.inventory_part_id')
        ->from('costing_items')
        ->where('costing_items.costing_id', $costing_id);
      })
      ->orderBy('item_master.category_id','ASC')
      ->get();

      $data['pre_ratios'] = CostingHistory::join('costing_items_history', 'costing_history.id', '=' ,'costing_items_history.costing_id')
      ->join('item_master', 'costing_items_history.inventory_part_id', '=' ,'item_master.master_id')
      ->join('item_category', 'item_master.category_id', '=' ,'item_category.category_id')
      ->select('item_category.category_name',
        DB::raw("(SUM(costing_items_history.total_cost)) AS cat_sum"),
        DB::raw("(SELECT
        Sum(costing_items_history.total_cost)
        FROM
        costing_items_history
        INNER JOIN item_master ON costing_items_history.inventory_part_id = item_master.master_id
        INNER JOIN item_category ON item_master.category_id = item_category.category_id
        WHERE
        costing_items_history.costing_id = costing_items_history.costing_id) AS tot_sum")
        )
      ->where('costing_history.id','=',$costing_id)
      ->where('costing_history.revision_no','=',$version)
      ->groupBy('item_master.category_id')
      ->get();

      if(sizeof($data['headers'])>0 && sizeof($data['pre_headers'])>0 && sizeof($data['details'])>0 && sizeof($data['pre_details'])>0){
        $pdf = PDF::loadView('reports/costing-variance',$data)
        ->stream('Cost Variance Sheet CI-'.$request->costing_id.'.pdf');
        return $pdf;
      }else{
        return View('reports/error');
      }

     
  }


}




