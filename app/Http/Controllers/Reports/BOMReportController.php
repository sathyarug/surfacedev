<?php

namespace App\Http\Controllers\Reports;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use App\Libraries\CapitalizeAllFields;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use App\Models\Merchandising\BOMHeader;
use PDF;

class BOMReportController extends Controller
{ 

    public function index(Request $request)
    {
      $type = $request->type;
      if($type == 'datatable') {
          $data = $request->all();
          $this->datatable_search($data);
      }
    }

  private function datatable_search($data){

    $cost_id = $data['data']['costing_id']['id'];
    $style = $data['data']['style_name']['style_id'];
    $fng = $data['data']['fng_code']['fng_id'];
    $stage = $data['data']['bom_stage']['bom_stage_id'];
    $status = $data['data']['bom_status']['status'];

    $query = DB::table('bom_header')
    ->join('item_master', 'bom_header.fng_id','=','item_master.master_id')
    ->join('org_country', 'bom_header.country_id','=','org_country.country_id')
    ->join('costing', 'bom_header.costing_id','=','costing.id')
    ->join('style_creation', 'costing.style_id','=','style_creation.style_id')
    ->join('merc_bom_stage', 'costing.bom_stage_id','=','merc_bom_stage.bom_stage_id')
    ->join('org_season', 'costing.season_id','=','org_season.season_id')
    ->join('merc_color_options', 'costing.color_type_id','=','merc_color_options.col_opt_id')
    ->join('usr_login', 'bom_header.created_by','=','usr_login.user_id')
    ->select('bom_header.*',
      'costing.sc_no',
      'item_master.master_code',
      'org_country.country_description',
      'costing.style_id',
      'style_creation.style_no',
      'costing.bom_stage_id',
      'merc_bom_stage.bom_stage_description',
      'costing.season_id',
      'org_season.season_name',
      'costing.color_type_id',
      'merc_color_options.color_option',
      'usr_login.user_name');
    if($cost_id!=null || $cost_id!=""){
      $query->where('bom_header.costing_id', $cost_id);
    }
    if($style!=null || $style!=""){
      $query->where('costing.style_id', $style);
    }
    if($fng!=null || $fng!=""){
      $query->where('bom_header.fng_id', $fng);
    }
    if($stage!=null || $stage!=""){
      $query->where('costing.bom_stage_id', $stage);
    }
    if($status!=null || $status!=""){
      $query->where('bom_header.status', $status);
    }
    $load_list = $query->get();

    echo json_encode([
      "recordsTotal" => "",
      "recordsFiltered" => "",
      "data" => $load_list
    ]);
  
  }

  public function view_bom(Request $request)
  {
    $bom=$request->bom;

    $data['company'] = BOMHeader::join('org_location', 'bom_header.user_loc_id', '=', 'org_location.loc_id')
    ->join('org_company', 'org_location.company_id', '=', 'org_company.company_id')
    ->join('org_country', 'org_location.country_code', '=', 'org_country.country_id')
    ->select('org_location.*','org_company.company_name','org_country.country_description')
    ->where('bom_header.bom_id','=',$bom)
    ->get();

    $data['headers'] = BOMHeader::join('item_master', 'bom_header.fng_id','=','item_master.master_id')
    ->join('org_country', 'bom_header.country_id','=','org_country.country_id')
    ->join('costing', 'bom_header.costing_id','=','costing.id')
    ->join('style_creation', 'costing.style_id','=','style_creation.style_id')
    ->join('merc_bom_stage', 'costing.bom_stage_id','=','merc_bom_stage.bom_stage_id')
    ->join('org_season', 'costing.season_id','=','org_season.season_id')
    ->join('merc_color_options', 'costing.color_type_id','=','merc_color_options.col_opt_id')
    ->join('usr_login', 'bom_header.created_by','=','usr_login.user_id')
    ->leftJoin('buy_master', 'costing.buy_id','=','buy_master.buy_id')
    ->leftJoin('org_color', 'item_master.color_id','=','org_color.color_id')
    ->select('bom_header.*',
      'costing.sc_no',
      'costing.style_type',
      'item_master.master_code',
      'org_country.country_description',
      'costing.style_id',
      'style_creation.style_no',
      'costing.bom_stage_id',
      'merc_bom_stage.bom_stage_description',
      'costing.season_id',
      'org_season.season_name',
      'costing.color_type_id',
      'merc_color_options.color_option',
      'usr_login.user_name',
      'buy_master.buy_name',
      'org_color.color_name',
      DB::raw("(SELECT
      COUNT(product_feature_component.product_feature_id)
      FROM
      style_creation
      INNER JOIN product_feature ON style_creation.product_feature_id = product_feature.product_feature_id
      INNER JOIN product_feature_component ON product_feature.product_feature_id = product_feature_component.product_feature_id
      WHERE
      style_creation.style_id = costing.style_id) AS comp_count")
    )
    ->where('bom_header.bom_id','=',$bom)
    ->get();

    $data['details'] = BOMHeader::join('bom_details', 'bom_header.bom_id','=','bom_details.bom_id')
    ->join('product_component', 'bom_details.product_component_id','=','product_component.product_component_id')
    ->join('product_silhouette', 'bom_details.product_silhouette_id','=','product_silhouette.product_silhouette_id')
    ->join('item_master', 'bom_details.inventory_part_id','=','item_master.master_id')
    ->leftJoin('merc_position', 'bom_details.position_id','=','merc_position.position_id')
    ->leftJoin('org_uom', 'bom_details.purchase_uom_id','=','org_uom.uom_id')
    ->leftJoin('org_supplier', 'bom_details.supplier_id','=','org_supplier.supplier_id')
    ->leftJoin('org_origin_type', 'bom_details.origin_type_id','=','org_origin_type.origin_type_id')
    ->leftJoin('org_garment_options', 'bom_details.garment_options_id','=','org_garment_options.garment_options_id')
    ->leftJoin('org_ship_mode', 'bom_details.ship_mode','=','org_ship_mode.ship_mode')
    ->leftJoin('fin_shipment_term', 'bom_details.ship_term_id','=','fin_shipment_term.ship_term_id')
    ->leftJoin('org_country', 'bom_details.country_id','=','org_country.country_id')
    ->select('bom_details.*',
    'bom_header.bom_id',
    'product_component.product_component_description',
    'product_silhouette.product_silhouette_description',
    'item_master.master_description',
    'item_master.master_code',
    'item_master.category_id',
    'merc_position.position',
    'org_uom.uom_code',
    'org_supplier.supplier_code',
    'org_supplier.supplier_name',
    'org_origin_type.origin_type',
    'org_garment_options.garment_options_description',
    'org_ship_mode.ship_mode',
    'fin_shipment_term.ship_term_description',
    'org_country.country_description')
    ->where('bom_header.bom_id','=',$bom)
    ->get();

    $data['sfgs'] = BOMHeader::join('bom_details', 'bom_header.bom_id', '=' ,'bom_details.bom_id')
    ->join('item_master', 'bom_details.sfg_id', '=' ,'item_master.master_id')
    ->join('costing_items', 'bom_details.costing_item_id', '=' ,'costing_items.costing_item_id')
    ->join('product_component', 'costing_items.product_component_id', '=' ,'product_component.product_component_id')
    ->select('bom_header.bom_id','bom_details.sfg_id','item_master.category_id','bom_details.sfg_code','product_component.product_component_description')
    ->where('bom_header.bom_id','=',$bom)
    ->groupBy('bom_details.sfg_id')
    ->get();

    $data['categories'] = BOMHeader::join('bom_details', 'bom_header.bom_id', '=' ,'bom_details.bom_id')
    ->join('item_master', 'bom_details.inventory_part_id', '=' ,'item_master.master_id')
    ->join('item_category', 'item_master.category_id', '=' ,'item_category.category_id')
    ->select('bom_header.bom_id','bom_details.bom_detail_id','bom_details.inventory_part_id','item_master.category_id','item_category.category_name','bom_details.sfg_id')
    ->where('bom_header.bom_id','=',$bom)
    ->orderBy('item_category.category_id')
    ->groupBy('item_master.category_id','bom_details.sfg_id')
    ->get();

    $config = [
        // 'format' => 'A4',
        'orientation' => 'L', //L-landscape
        'watermark' => 'Duplicate',
        'show_watermark' => false,
      ];

    if(sizeof($data['headers'])>0 && sizeof($data['details'])>0 && sizeof($data['categories'])>0){
      $pdf = PDF::loadView('reports/bom', $data, [], $config)
      ->stream('BOM report-'.$bom.'.pdf');
      return $pdf;
    }else{
      return View('reports/error');
    }
    //return View('reports/bom',$data);

  }

  

}
