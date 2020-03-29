<?php

namespace App\Http\Controllers\Reports;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

class IssueReportController extends Controller
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
        $from_date = $data['from_date'];
        $to_date = $data['to_date'];
        $customer = $data['data']['customer_name']['customer_id'];
        $location = $data['data']['loc_name']['loc_id'];
        $style = $data['data']['style_no']['style_id'];

        $query = DB::table('store_issue_detail')
            ->join('store_issue_header', 'store_issue_header.issue_id', '=', 'store_issue_detail.issue_id')            
            ->join('store_mrn_detail', 'store_mrn_detail.mrn_detail_id', '=', 'store_issue_detail.mrn_detail_id')
            ->join('store_mrn_header', 'store_mrn_header.mrn_id', '=', 'store_mrn_detail.mrn_id')
            ->join('style_creation', 'style_creation.style_id', '=', 'store_mrn_header.style_id')
            ->join('item_master', 'item_master.master_id', '=', 'store_issue_detail.item_id')
            ->join('item_category', 'item_category.category_id', '=', 'item_master.category_id')
            ->join('merc_shop_order_detail', 'merc_shop_order_detail.shop_order_detail_id', '=', 'store_mrn_detail.shop_order_detail_id')
            ->join('merc_customer_order_details', 'merc_customer_order_details.details_id', '=', 'store_mrn_detail.cust_order_detail_id')
            ->join('merc_customer_order_header', 'merc_customer_order_header.order_id', '=', 'merc_customer_order_details.order_id')
            ->join('cust_customer', 'cust_customer.customer_id', '=', 'merc_customer_order_header.order_customer')
            ->join('merc_po_order_details', 'merc_po_order_details.shop_order_detail_id', '=', 'merc_shop_order_detail.shop_order_detail_id')
            ->join('merc_po_order_header', 'merc_po_order_header.po_id', '=', 'merc_po_order_details.po_header_id')
            ->join('store_grn_detail', 'store_grn_detail.shop_order_detail_id', '=', 'store_mrn_detail.shop_order_detail_id')
            ->join('usr_profile', 'usr_profile.user_id', '=', 'store_issue_header.created_by')
            ->select(
                'item_master.master_code',
                'item_master.master_description',
                'item_category.category_name',
                'style_creation.style_no',
                'merc_shop_order_detail.shop_order_id',
                'store_issue_detail.issue_detail_id',
                'merc_po_order_header.po_number',
                'merc_po_order_header.po_date',
                'cust_customer.customer_name',
                'merc_customer_order_details.po_no',
                'store_grn_detail.grn_qty',
                'store_grn_detail.bal_qty',
                DB::raw("(DATE_FORMAT(store_grn_detail.created_date,'%d-%b-%Y')) AS grn_date"),
                'store_mrn_header.mrn_no',
                'store_issue_header.issue_no',
                'store_issue_detail.qty AS issue_qty',
                DB::raw("(DATE_FORMAT(store_issue_detail.created_date,'%d-%b-%Y')) AS issue_date"),
                'store_grn_detail.original_bal_qty',
                'usr_profile.first_name'
            );

        if (($from_date != null || $from_date != "") && ($to_date != null || $to_date != "")) {
            $query->whereBetween('store_issue_detail.created_date', [$from_date, $to_date]);
        }

        if ($customer != null || $customer != "") {
            $query->where('cust_customer.customer_id', $customer);
        }

        if ($location != null || $location != "") {
            $query->where('store_issue_detail.location_id', $location);
        }

        if ($style != null || $style != "") {
            $query->where('store_mrn_header.style_id', $style);
        }

        $load_list = $query->distinct()->get();

        echo json_encode([
            "data" => $load_list
        ]);
    }
}
