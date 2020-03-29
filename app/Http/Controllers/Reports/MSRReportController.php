<?php

namespace App\Http\Controllers\Reports;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

class MSRReportController extends Controller
{
    public function index(Request $request)
    {
        $type = $request->type;
        if ($type == 'auto') {
            $search = $request->search;
            return response($this->load_shop_order($search));
        } elseif ($type == 'datatable') {
            $data = $request->all();
            $this->datatable_search($data);
        }
    }

    //search customer for autocomplete
    private function load_shop_order($search)
    {
        $shop_order_lists = DB::table('merc_shop_order_header')
            ->select('shop_order_id')
            ->where([['shop_order_id', 'like', '%' . $search . '%'],])
            ->get();
        return $shop_order_lists;
    }

    private function datatable_search($data)
    {
        $rm_in_date = $data['rm_date'];
        $shop_order_id = $data['data']['shop_order_id']['shop_order_id'];
        $cus_po = $data['data']['po_no']['po_no'];

        $query = DB::table('merc_shop_order_detail')
            ->join('merc_shop_order_header', 'merc_shop_order_header.shop_order_id', '=', 'merc_shop_order_detail.shop_order_id')
            ->join('merc_shop_order_delivery', 'merc_shop_order_delivery.shop_order_id', '=', 'merc_shop_order_header.shop_order_id')
            ->join('merc_customer_order_details', 'merc_customer_order_details.details_id', '=', 'merc_shop_order_delivery.delivery_id')
            ->join('merc_customer_order_header', 'merc_customer_order_header.order_id', '=', 'merc_customer_order_details.order_id')
            ->join('cust_customer', 'cust_customer.customer_id', '=', 'merc_customer_order_header.order_customer')
            ->join('cust_division', 'cust_division.division_id', '=', 'merc_customer_order_header.order_division')
            ->join('item_master AS ITEM', 'ITEM.master_id', '=', 'merc_shop_order_detail.inventory_part_id')
            ->join('item_master AS FNG', 'FNG.master_id', '=', 'merc_customer_order_details.fng_id')
            ->join('product_component', 'product_component.product_component_id', '=', 'merc_shop_order_detail.component_id')
            ->join('bom_header', 'bom_header.bom_id', '=', 'merc_shop_order_detail.bom_id')
            ->join('bom_details', 'bom_details.bom_id', '=', 'bom_header.bom_id')
            ->join('product_silhouette', 'product_silhouette.product_silhouette_id', '=', 'bom_details.product_silhouette_id')
            ->join('org_location', 'org_location.loc_id', '=', 'merc_customer_order_details.user_loc_id')
            ->join('merc_po_order_details', 'merc_po_order_details.shop_order_detail_id', '=', 'merc_shop_order_detail.shop_order_detail_id')
            ->join('merc_po_order_header', 'merc_po_order_header.po_id', '=', 'merc_po_order_details.po_header_id')
            ->leftJoin('store_stock', 'store_stock.shop_order_detail_id', '=', 'merc_shop_order_detail.shop_order_detail_id')
            ->leftJoin('org_size', 'org_size.size_id', '=', 'merc_po_order_details.size')
            ->join('usr_profile', 'usr_profile.user_id', '=', 'merc_po_order_header.created_by')
            ->select(
                'cust_customer.customer_name',
                'cust_division.division_description',
                'ITEM.master_code AS item_code',
                'ITEM.master_description AS item_des',
                'FNG.master_code AS fng_code',
                'FNG.master_description AS fng_des',
                'product_component.product_component_description',
                'product_silhouette.product_silhouette_description',
                'merc_shop_order_header.shop_order_id',
                DB::raw('(SELECT DATE(bom_header.created_date) AS bom_create_date FROM bom_header AS BOH
                WHERE BOH.bom_id = merc_shop_order_detail.bom_id) AS bom_create_date'),
                'org_size.size_name',
                'org_location.loc_name',
                'merc_shop_order_detail.gross_consumption',
                'merc_shop_order_detail.required_qty',
                'merc_customer_order_details.order_qty',
                'usr_profile.first_name',
                'merc_customer_order_details.pcd',
                'merc_customer_order_details.po_no',
                'merc_po_order_header.po_number',
                'merc_po_order_header.po_date',
                'merc_shop_order_detail.po_qty AS sup_po_order_qty',
                'merc_shop_order_detail.asign_qty',
                DB::raw('(SELECT DATE(store_grn_detail.created_date) AS grn_date FROM store_grn_detail
                WHERE store_grn_detail.shop_order_detail_id = merc_shop_order_detail.shop_order_detail_id) AS grn_date'),
                'merc_shop_order_detail.issue_qty',
                'store_stock.qty AS stock'
            );

        if ($rm_in_date != null || $rm_in_date != "") {
            $query->where('merc_customer_order_details.rm_in_date', $rm_in_date);
        }

        if ($shop_order_id != null || $shop_order_id != "") {
            $query->where('merc_shop_order_header.shop_order_id', $shop_order_id);
        }

        if ($cus_po != null || $cus_po != "") {
            $query->where('merc_customer_order_details.po_no', $cus_po);
        }

        $load_list = $query->distinct()->get();

        echo json_encode([
            "data" => $load_list
        ]);
    }
}
