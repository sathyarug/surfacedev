<?php

namespace App\Http\Controllers\Reports;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

class InventoryPartInStockController extends Controller
{
    public function index(Request $request)
    {
        $type = $request->type;
        if ($type == 'datatable') {
            $data = $request->all();
            $this->datatable_search($data);
        }
    }

    private function datatable_search($data)
    {
        $location = $data['data']['loc_name']['loc_id'];

        $query = DB::table('store_stock')
            ->join('item_master', 'item_master.master_id', '=', 'store_stock.item_id')
            ->join('org_location', 'org_location.loc_id', '=', 'store_stock.location')
            ->join('org_company', 'org_company.company_id', '=', 'org_location.company_id')
            ->join('merc_shop_order_detail', 'merc_shop_order_detail.shop_order_detail_id', '=', 'store_stock.shop_order_detail_id')
            ->join('store_grn_detail', 'store_grn_detail.shop_order_detail_id', '=', 'store_stock.shop_order_detail_id')
            ->join('store_roll_plan', 'store_roll_plan.grn_detail_id', '=', 'store_grn_detail.grn_detail_id')
            ->join('org_substore', 'org_substore.substore_id', '=', 'store_stock.sub_store')
            ->join('org_store_bin', 'org_store_bin.store_bin_id', '=', 'store_stock.bin')
            ->leftJoin('org_size', 'org_size.size_id', '=', 'store_stock.size')
            ->leftJoin('org_uom', 'org_uom.uom_id', '=', 'store_stock.uom')
            ->leftJoin('merc_customer_order_details', 'merc_customer_order_details.shop_order_id', '=', 'merc_shop_order_detail.shop_order_id')
            ->join('merc_customer_order_header', 'merc_customer_order_header.order_id', '=', 'merc_customer_order_details.order_id')
            ->join('style_creation', 'style_creation.style_id', '=', 'store_stock.style_id')
            ->leftJoin('cust_division', 'cust_division.division_id', '=', 'merc_customer_order_header.order_division')
            ->select(
                'item_master.master_code',
                'item_master.master_description',
                'org_uom.uom_code',
                'org_company.company_name',
                'org_location.loc_name',
                'store_roll_plan.lot_no',
                'store_roll_plan.batch_no',
                'store_stock.qty AS avble_stock',
                'merc_shop_order_detail.asign_qty',
                'org_size.size_name',
                'merc_shop_order_detail.shop_order_id',
                'org_substore.substore_name',
                'org_store_bin.store_bin_name',
                'store_stock.standard_price',
                'style_creation.style_no',
                DB::raw("(DATE_FORMAT(store_grn_detail.created_date,'%d-%b-%Y')) AS grn_date"),
                'cust_division.division_description'
            );

        if ($location != null || $location != "") {
            $query->where('store_stock.location', $location);
        }

        $load_list = $query->distinct()->get();

        echo json_encode([
            "data" => $load_list
        ]);
    }
}
