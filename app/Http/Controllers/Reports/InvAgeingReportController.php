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

class InvAgeingReportController extends Controller
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

    // public function datatable_search($data)
    // {

    //   $location = $data['data']['loc_name']['loc_id'];

    //   $query = DB::table('store_stock_transaction')
    //   ->join('item_master','store_stock_transaction.item_code','=','item_master.master_id')
    //   ->join('item_category','item_master.category_id','=','item_category.category_id')
    //   ->join('item_subcategory','item_master.subcategory_id','=','item_subcategory.subcategory_id')
    //   ->join('org_location','store_stock_transaction.location','=','org_location.loc_id')
    //   ->join('org_substore','store_stock_transaction.sub_store','=','org_substore.substore_id') 
    //   ->join('org_uom','store_stock_transaction.uom','=','org_uom.uom_id')
    //   ->leftJoin('store_grn_header','store_stock_transaction.doc_num','=','store_grn_header.grn_number')
    //   ->select('store_stock_transaction.transaction_id',
    //     'item_category.category_name',
    //     'item_subcategory.subcategory_name',
    //     'item_master.master_description',
    //     DB::raw('SUM(store_stock_transaction.qty) AS total_qty'),
    //     DB::raw('SUM(store_stock_transaction.standard_price) AS total_amount'),
    //     'org_location.loc_name',
    //     'org_substore.substore_name',
    //     'store_stock_transaction.item_code',
    //     'org_uom.uom_description'
    //   );

    //   $query->where('store_stock_transaction.location', $location);
    //   $query->groupBy('store_stock_transaction.item_code');
    //   $query->havingRaw('total_qty > ?', [0]);
    //   $query->orderBy('item_master.master_description', 'ASC');

    //   $rows = array();
    //   foreach($query->get() as $row)
    //   {  
    //       $row->zeroTOthirty = $this->get_item_stock_balance($row->item_code,0,30);
    //       $row->thirtyTOsixty = $this->get_item_stock_balance($row->item_code,30,60);
    //       $row->sixtyTOninety = $this->get_item_stock_balance($row->item_code,60,90);
    //       $row->ninetyTOhuntwenty = $this->get_item_stock_balance($row->item_code,90,120);
    //       $row->huntwentyPlus = $this->get_item_stock_balance($row->item_code,120,120);
    //       $rows[] = $row;
    //   }

    //   echo json_encode([
    //     "recordsTotal" => "",
    //     "recordsFiltered" => "",
    //     "data" => $rows
    //   ]);

    // }

    // public function get_item_stock_balance($item, $date_from, $date_to)
    // {
    //   $today = date('Y-m-d');
    //   $query = DB::table('store_stock_transaction')
    //   ->join('store_grn_header','store_stock_transaction.doc_num','=','store_grn_header.grn_number')
    //   ->select(DB::raw('SUM(store_stock_transaction.qty) AS total_qty'),DB::raw('SUM(store_stock_transaction.standard_price) AS total_value'));
    //   $query->where('store_stock_transaction.item_code', $item);
    //   if($date_from==$date_to){
    //     $query->where(DB::raw('(TO_DAYS("'.$today.'")-TO_DAYS(store_grn_header.created_date))'),'>',120);
    //   }else{
    //     $query->whereBetween(DB::raw('(TO_DAYS("'.$today.'")-TO_DAYS(store_grn_header.created_date))'),[$date_from,$date_to]);
    //   }
    //   $query->groupBy('store_stock_transaction.item_code');

    //   if($query->count()==0){
    //     return "";
    //   }else{
    //     return $query->first();
    //   }
    // }


    public function datatable_search($data)
    {

      $location = $data['data']['loc_name']['loc_id'];

      $query = DB::table('store_stock AS st')
      ->join('item_master','st.item_id','=','item_master.master_id')
      ->join('item_category','item_master.category_id','=','item_category.category_id')
      ->join('item_subcategory','item_master.subcategory_id','=','item_subcategory.subcategory_id')
      ->join('org_uom','st.uom','=','org_uom.uom_id')
      ->join('org_location','st.location','=','org_location.loc_id')
      ->join('org_store','st.store','=','org_store.store_id')
      ->select('st.item_id',
        'item_master.master_code',
        'item_master.master_description',
        'item_category.category_name',
        'item_subcategory.subcategory_name',
        'st.location',
        'st.store',
        'st.sub_store',
        'st.bin',
        'st.uom',
        'org_uom.uom_description',
        'org_location.loc_name',
        'org_store.store_name',
        DB::raw('FORMAT(SUM(st.qty),4) AS total_qty'),
        DB::raw('FORMAT(SUM(st.qty*st.standard_price),4) AS total_std_price'),
        DB::raw('FORMAT(SUM(st.qty*st.purchase_price),4) AS total_pur_price')
      );
      $query->where('st.location', $location);
      $query->where('st.qty','>', 0);    
      $query->groupBy('st.location','st.item_id');
      $query->orderBy('item_master.master_description', 'ASC');
      $rows = array();
      foreach($query->get() as $row)
      {  
        $row->zeroTOthirty = $this->get_item_stock_balance($row->location,$row->store,$row->item_id,0,30);
        $row->thirtyTOsixty = $this->get_item_stock_balance($row->location,$row->store,$row->item_id,30,60);
        $row->sixtyTOninety = $this->get_item_stock_balance($row->location,$row->store,$row->item_id,60,90);
        $row->ninetyTOhuntwenty = $this->get_item_stock_balance($row->location,$row->store,$row->item_id,90,120);
        $row->huntwentyPlus = $this->get_item_stock_balance($row->location,$row->store,$row->item_id,120,120);
        $rows[] = $row;
      }

      echo json_encode([
        "recordsTotal" => "",
        "recordsFiltered" => "",
        "data" => $rows
      ]);

    }

    //return array("total_qty" => $item, "total_value" => $date_from);

    public function get_item_stock_balance($location,$store,$item,$date_from,$date_to)
    {
      $today = date('Y-m-d');
      $query = DB::table('store_stock_transaction AS sst');
      $query->select(
        DB::raw('FORMAT(SUM(sst.qty),4) AS total_qty'),
        DB::raw('FORMAT(SUM(sst.qty*sst.standard_price),4) AS total_value'),
        DB::raw('FORMAT(SUM(sst.qty*sst.purchase_price),4) AS total_pur_value')
      );
      $query->whereIn('sst.direction', ['+','-']);
      $query->where('sst.location', $location);
      $query->where('sst.item_id', $item);

      if($date_from==$date_to){
        $query->where(DB::raw('(TO_DAYS("'.$today.'")-TO_DAYS(sst.created_date))'),'>',120);
      }else{
        $query->whereBetween(DB::raw('(TO_DAYS("'.$today.'")-TO_DAYS(sst.created_date))'),[$date_from,$date_to]);
      }

      $query->groupBy('sst.location','sst.item_id');
      if($query->count()==0){
        return "";
      }else{
        return $query->first();
      }
    }


}
