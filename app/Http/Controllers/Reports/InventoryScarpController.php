<?php

namespace App\Http\Controllers\Reports;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use App\Libraries\CapitalizeAllFields;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use App\Models\Org\BlockStatus;
use App\Models\stores\StoreScarpHeader;
use App\Models\stores\StoreScarpDetails;
use App\Models\stores\RollPlan;
use App\Models\Store\TrimPacking;
use App\Models\Store\StockTransaction;

class InventoryScarpController extends Controller
{ 

    public function index(Request $request)
    {
      $type = $request->type;
      if($type == 'header') {
        $data = $request->all();
        $this->header_search($data);
      }
    }

    public function header_search($data)
    {
      $from_store = $data['search']['from_store']['store_id'];
      $to_store = $data['search']['to_store']['store_id'];

      $query = DB::table('store_inv_scarp_header')
      ->join('org_store','store_inv_scarp_header.from_store','=','org_store.store_id')
      ->join('org_store AS store_to','store_inv_scarp_header.to_store','=','store_to.store_id')
      ->join('usr_login','store_inv_scarp_header.created_by','=','usr_login.user_id')
      ->select('store_inv_scarp_header.*',
        'org_store.store_name AS store_from',
        'store_to.store_name AS store_to',
        'usr_login.user_name',
        DB::raw("(DATE_FORMAT(store_inv_scarp_header.created_date,'%d-%b-%Y %H:%i:%s')) AS create_date")
      );
      if($from_store!=null || $from_store!=""){
        $query->where('store_inv_scarp_header.from_store', $from_store);
      }
      if($to_store!=null || $to_store!=""){
        $query->where('store_inv_scarp_header.to_store', $to_store);
      }
      $query->orderBy('store_inv_scarp_header.report_no','DESC');
      $data = $query->get();

      echo json_encode([
        "recordsTotal" => "",
        "recordsFiltered" => "",
        "data" => $data
      ]);

    }

    public function load_inventory(Request $request)
    {
      $data = $request->all();
      $from_store = $data['search']['from_store']['store_id'];
      $to_store = $data['search']['to_store']['store_id'];
      $category = $data['search']['item_category']['category_id'];
      $code_from = $data['search']['item_code']['master_code'];
      $code_to = $data['search']['item_code_to']['master_code'];
      $paramsArr = $data['options'];
      $storeArr = array($from_store,$to_store);

      // Block stock related transations
      if(in_array("FPWP", $paramsArr)) {
        
        foreach($storeArr as $value) {

          $is_exists = BlockStatus::where('store', $value)->exists();
          if($is_exists)
          {     
            $update = BlockStatus::where('store', $value)->update(['status' => "BLOCK"]); 
          }
          else
          {
            $insert = new BlockStatus();
            $insert->status_description = "BLOCK_STOCK";
            $insert->status = "BLOCK";
            $insert->store = $value;
            $insert->save();
          }

        } 

      }
      // Block stock related transations end

      $fabric = DB::table('store_grn_detail')
      ->join('style_creation','store_grn_detail.style_id','=','style_creation.style_id')
      ->join('cust_customer','style_creation.customer_id','=','cust_customer.customer_id')
      ->join('item_master','store_grn_detail.item_code','=','item_master.master_id')
      ->join('item_category','item_master.category_id','=','item_category.category_id')
      ->join('org_uom','store_grn_detail.inventory_uom','=','org_uom.uom_id')
      ->join('merc_shop_order_detail','store_grn_detail.shop_order_detail_id','=','merc_shop_order_detail.shop_order_detail_id')
      ->join('store_grn_header','store_grn_detail.grn_id','=','store_grn_header.grn_id')
      ->join('store_roll_plan','store_grn_detail.grn_detail_id','=','store_roll_plan.grn_detail_id')
      ->join('org_store_bin','store_roll_plan.bin','=','org_store_bin.store_bin_id')
      ->select('store_grn_detail.grn_detail_id',
        'store_grn_header.main_store',
        'store_grn_header.sub_store',
        'store_grn_header.location',
        'store_grn_detail.item_code',
        'store_grn_detail.style_id',
        'style_creation.style_no',
        'store_grn_detail.shop_order_id',
        'store_grn_detail.shop_order_detail_id',
        'cust_customer.customer_name',
        'item_master.master_description',
        'org_uom.uom_description',
        'store_grn_detail.grn_qty',
        'store_grn_detail.standard_price',
        'store_grn_detail.purchase_price',
        DB::raw("(store_grn_detail.grn_qty*store_grn_detail.standard_price) AS total_value"),
        'item_master.category_id',
        'store_roll_plan.roll_plan_id',
        'store_roll_plan.lot_no',
        'store_roll_plan.batch_no',
        'store_roll_plan.roll_no',
        'store_roll_plan.qty',
        'store_roll_plan.bin',
        'store_roll_plan.shade',
        'org_store_bin.store_bin_name',
        DB::raw("(SELECT
        SUM(store_issue_detail.qty)-store_roll_plan.qty
        FROM
        store_issue_detail
        WHERE
        store_issue_detail.item_detail_id = store_roll_plan.roll_plan_id
        AND store_issue_detail.item_id = store_grn_detail.item_code) AS bin_wise_balance_qty"),
        DB::raw("(SELECT
        SUM(store_issue_detail.qty)-store_roll_plan.qty
        FROM
        store_issue_detail
        WHERE
        store_issue_detail.item_detail_id = store_roll_plan.roll_plan_id
        AND store_issue_detail.item_id = store_grn_detail.item_code)*store_grn_detail.standard_price AS bin_wise_balance_amount"),
        DB::raw("(NULL) AS scarp_qty"),
        DB::raw("(NULL) AS comments"),
        'store_grn_detail.style_id',
        'store_grn_detail.inventory_uom',
        'item_master.color_id',
        'item_master.size_id',
        'store_grn_detail.po_number',
        'item_master.master_code',
        'item_category.category_code',
        'store_grn_header.inv_number'
      )
      ->where('merc_shop_order_detail.po_balance_qty','>', 0)
      ->whereIn('item_category.category_code', ['FAB'])
      ->whereIn('store_grn_header.main_store', $storeArr);
      if($category != null){
        $fabric->where('item_master.category_id', $category);
      }
      if($code_from != null && $code_to != null) {
        $fabric->whereBetween('item_master.master_code', [$code_from, $code_to]);
      }

      $trims = DB::table('store_grn_detail')
      ->join('style_creation','store_grn_detail.style_id','=','style_creation.style_id')
      ->join('cust_customer','style_creation.customer_id','=','cust_customer.customer_id')
      ->join('item_master','store_grn_detail.item_code','=','item_master.master_id')
      ->join('item_category','item_master.category_id','=','item_category.category_id')
      ->join('org_uom','store_grn_detail.inventory_uom','=','org_uom.uom_id')
      ->join('merc_shop_order_detail','store_grn_detail.shop_order_detail_id','=','merc_shop_order_detail.shop_order_detail_id')
      ->leftJoin('store_grn_header','store_grn_detail.grn_id','=','store_grn_header.grn_id')
      ->join('store_trim_packing_detail','store_grn_detail.grn_detail_id','=','store_trim_packing_detail.grn_detail_id')
      ->join('org_store_bin','store_trim_packing_detail.bin','=','org_store_bin.store_bin_id')
      ->select('store_grn_detail.grn_detail_id',
        'store_grn_header.main_store',
        'store_grn_header.sub_store',
        'store_grn_header.location',
        'store_grn_detail.item_code',
        'store_grn_detail.style_id',
        'style_creation.style_no',
        'store_grn_detail.shop_order_id',
        'store_grn_detail.shop_order_detail_id',
        'cust_customer.customer_name',
        'item_master.master_description',
        'org_uom.uom_description',
        'store_grn_detail.grn_qty',
        'store_grn_detail.standard_price',
        'store_grn_detail.purchase_price',
        DB::raw("(store_grn_detail.grn_qty*store_grn_detail.standard_price) AS total_value"),
        'item_master.category_id',
        'store_trim_packing_detail.trim_packing_id AS roll_plan_id',
        'store_trim_packing_detail.lot_no',
        'store_trim_packing_detail.batch_no',
        'store_trim_packing_detail.box_no AS roll_no',
        'store_trim_packing_detail.qty',
        'store_trim_packing_detail.bin',
        'store_trim_packing_detail.shade',
        'org_store_bin.store_bin_name',
        DB::raw("(SELECT
        SUM(store_issue_detail.qty)-store_trim_packing_detail.qty
        FROM
        store_issue_detail
        WHERE
        store_issue_detail.item_detail_id = store_trim_packing_detail.trim_packing_id
        AND store_issue_detail.item_id = store_grn_detail.item_code) AS bin_wise_balance_qty"),
        DB::raw("(SELECT
        SUM(store_issue_detail.qty)-store_trim_packing_detail.qty
        FROM
        store_issue_detail
        WHERE
        store_issue_detail.item_detail_id = store_trim_packing_detail.trim_packing_id
        AND store_issue_detail.item_id = store_grn_detail.item_code)*store_grn_detail.standard_price AS bin_wise_balance_amount"),
        DB::raw("(NULL) AS scarp_qty"),
        DB::raw("(NULL) AS comments"),
        'store_grn_detail.style_id',
        'store_grn_detail.inventory_uom',
        'item_master.color_id',
        'item_master.size_id',
        'store_grn_detail.po_number',
        'item_master.master_code',
        'item_category.category_code',
        'store_grn_header.inv_number'
      )
      ->where('merc_shop_order_detail.po_balance_qty','>', 0)
      ->whereNotIn('item_category.category_code', ['FAB'])
      ->whereIn('store_grn_header.main_store', $storeArr);
      if($category != null){
        $trims->where('item_master.category_id', $category);
      }
      if($code_from != null && $code_to != null) {
        $trims->whereBetween('item_master.master_code', [$code_from, $code_to]);
      }
      $trims->unionAll($fabric)
      ->orderBy('style_no','ASC')
      ->orderBy('grn_detail_id','ASC')
      ->orderBy('location','ASC')
      ->orderBy('main_store','ASC')
      ->orderBy('sub_store','ASC')
      ->orderBy('category_id','ASC');
      $data = $trims->get();

      echo json_encode([
        "recordsTotal" => "",
        "recordsFiltered" => "",
        "data" => $data
      ]);
      
    }

    public function store(Request $request)
    {

      $storeArr = array($request['header']['from_store']['store_id'],$request['header']['to_store']['store_id']);

      $saveHeader = new StoreScarpHeader();
      $saveHeader->from_store = $request['header']['from_store']['store_id'];
      $saveHeader->to_store = $request['header']['to_store']['store_id'];
      $saveHeader->status = 1;
      $saveHeader->save();

      $report_no = $saveHeader->report_no;

      if($saveHeader){
        
        $scarp = $this->save_scarp_details($request['details'],$report_no);
        $stock_transaction = $this->save_stock_transaction($request['details'],$report_no);
        $bin_balance = $this->update_bin_balance($request['details'],$report_no);
        $release_store = $this->release_store_status($storeArr);

        return response([ 'data' => [
          'result' => 'success',
          'message' => 'Data saved successfully'
         ]
        ], Response::HTTP_CREATED );

      } else {

        $errors = $saveHeader->errors();
        return response([ 'data' => [
            'result' => $errors,
            'message' => 'Data save fail'
          ]
        ], Response::HTTP_CREATED );

      }

    }

    public function save_scarp_details($scarpData,$report_no){
      
      foreach($scarpData as $row){
            
        if($row['scarp_qty']!="" || $row['scarp_qty']!=null){
          
            if(!isset($row['comments'])){
              $comments = "";
            }else{
              $comments = $row['comments'];
            }

            $saveDetails = new StoreScarpDetails();
            $saveDetails->report_no = $report_no;
            $saveDetails->status = 1;
            $saveDetails->style = $row['style_id'];
            $saveDetails->shop_order_id = $row['shop_order_detail_id'];
            $saveDetails->master_id = $row['item_code'];
            $saveDetails->bin_no = $row['bin'];
            $saveDetails->roll_box_no = $row['roll_no'];
            $saveDetails->batch = $row['batch_no'];
            $saveDetails->inv_qty = $row['bin_wise_balance_qty'];
            $saveDetails->scarp_qty = $row['scarp_qty'];
            $saveDetails->standard_price = $row['standard_price'];
            $saveDetails->purchase_price = $row['purchase_price'];
            $saveDetails->comments = $comments;
            $saveDetails->uom = $row['inventory_uom'];
            $saveDetails->save();

        }

      }

    }

    public function save_stock_transaction($scarpData,$report_no){
      
      foreach($scarpData as $row){
            
        if($row['scarp_qty']!="" || $row['scarp_qty']!=null){
          
            if(!isset($row['comments'])){
              $comments = "";
            }else{
              $comments = $row['comments'];
            }

            $st = new StockTransaction;
            $st->status = 1;
            $st->doc_type = 'SCARP';
            $st->doc_num = $report_no;
            $st->location = $row['location'];
            $st->main_store = $row['main_store'];
            $st->sub_store = $row['sub_store'];
            $st->bin = $row['bin'];
            $st->style_id = $row['style_id'];
            $st->item_code = $row['item_code'];
            $st->material_code = $row['master_code'];
            $st->uom = $row['inventory_uom'];
            $st->qty = (double)$row['scarp_qty']*-1;
            $st->standard_price = (double)$row['standard_price'];
            $st->purchase_price = (double)$row['purchase_price'];
            $st->direction = '-';
            $st->size = $row['size_id'];
            $st->color = $row['color_id'];        
            $st->customer_po_id = $row['po_number'];    
            $st->shop_order_id = $row['shop_order_id'];
            $st->shop_order_detail_id = $row['shop_order_detail_id'];
            $st->created_by = auth()->payload()['user_id'];
            $st->user_loc_id = auth()->payload()['loc_id'];
            $st->save();

        }

      }

    }

    public function update_bin_balance($scarpData,$report_no){
      
      foreach($scarpData as $row){
            
        if($row['scarp_qty']!="" || $row['scarp_qty']!=null){
          
            if($row['category_code']=='FAB'){  
              $update = RollPlan::where('roll_plan_id', $row['roll_plan_id'])->update(['qty' => ($row['bin_wise_balance_amount']-$row['scarp_qty'])]); 
            }
            else{
              $update = TrimPacking::where('trim_packing_id', $row['trim_packing_id'])->update(['qty' => ($row['bin_wise_balance_amount']-$row['scarp_qty'])]); 
            }

        }

      }

    }

    public function release_store_status($storeArr){
      
      foreach($storeArr as $value) {
        $update = BlockStatus::where('store', $value)->update(['status' => "RELEASE"]);
      }

    }


}
