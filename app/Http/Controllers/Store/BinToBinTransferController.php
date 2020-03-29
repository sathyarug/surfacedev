<?php

namespace App\Http\Controllers\Store;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Libraries\AppAuthorize;
use App\Libraries\CapitalizeAllFields;
use App\Libraries\UniqueIdGenerator;
use Exception;
use App\Models\Store\StoreBin;

use App\Models\stores\RollPlan;
use App\Models\Store\TrimPacking;

use App\Models\Store\StockTransaction;
use App\Models\Org\ConversionFactor;
use App\Models\Store\Stock;

use App\Models\Store\ReturnToSupplierHeader;
use App\Models\Store\ReturnToSupplierDetails;
use App\Models\Store\GrnHeader;
use App\Models\Store\GrnDetail;
use App\Models\Merchandising\ShopOrderDetail;

class BinToBinTransferController extends Controller
{ 

    public function index(Request $request)
    {
      $type = $request->type;
      if($type == 'load_details') {
        $data = $request->all();
        return $this->load_details($data);
      }else if($type == 'datatable'){
        $data = $request->all();
        return $this->datatable_search($data);
      }
    }

    public function load_sub_store_bin(Request $request)
    {
        $bin_lists = StoreBin::where('substore_id',$request['search']['sub_store']['substore_id'])
        ->pluck('store_bin_name')
        ->toArray();
        return json_encode([ "data" => $bin_lists ]);
    }

    public function load_bin_items(Request $request)
    {
        $store = $request['search']['store']['store_id'];
        $sub_store = $request['search']['sub_store']['substore_id'];
        $bin = $request['search']['store_bin']['store_bin_id'];
        $item_id = $request['details']['item_code']['master_id'];

        $query = DB::table('store_roll_plan')
        ->join('store_grn_detail','store_roll_plan.grn_detail_id','=','store_grn_detail.grn_detail_id')
        ->join('style_creation','store_grn_detail.style_id','=','style_creation.style_id')
        ->join('item_master','store_grn_detail.item_code','=','item_master.master_id')
        ->join('item_category','item_master.category_id','=','item_category.category_id')
        ->join('org_uom','store_grn_detail.uom','=','org_uom.uom_id')
        ->join('org_store_bin','store_roll_plan.bin','=','org_store_bin.store_bin_id')
        ->join('store_grn_header','store_grn_detail.grn_id','=','store_grn_header.grn_id')
        ->select('store_stock_details.*',
        'store_rm_plan.grn_detail_id',
        'store_grn_detail.grn_id',
        'store_grn_header.grn_number',
        'store_rm_plan.lot_no',
        'store_rm_plan.batch_no',
        'store_rm_plan.roll_or_box_no',
        'store_rm_plan.received_qty',
        'store_rm_plan.actual_qty',
        'store_rm_plan.shade',
        'store_grn_detail.style_id',
        'store_grn_detail.purchase_price',
        'store_grn_detail.inventory_uom',
        'store_grn_detail.uom',
        'org_uom.uom_code',
        'item_master.category_id',
        'item_master.master_description',
        'store_grn_detail.shop_order_id',
        'store_grn_detail.shop_order_detail_id',
        'store_grn_detail.po_number',
        'store_grn_detail.po_details_id',
        'item_category.category_code',
        'item_master.master_code',
        'org_store_bin.store_bin_name');
        $query->where('store_roll_plan.bin', $bin);
        $query->where('item_category.category_code', 'FAB');
        $query->where('store_roll_plan.qty','>',0);
        if($item_id!=null || $item_id!=""){
            $query->where('store_grn_detail.item_code', $item_id);
        }
        $data = $query->get();

        echo json_encode([
            "recordsTotal" => "",
            "recordsFiltered" => "",
            "data" => $data
        ]);

    }

    public function store(Request $request)
    {
        
    }

}
