<?php

namespace App\Http\Controllers\Reports;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

class DailyRecReportController extends Controller
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
        $date_from = $data['date_from'];
        $date_to = $data['date_to'];
        $location = $data['data']['loc_name']['loc_id'];

        $query = DB::table('store_grn_detail')
            ->join('store_grn_header', 'store_grn_header.grn_id', '=', 'store_grn_detail.grn_id')
            ->join('merc_po_order_header', 'merc_po_order_header.po_id', '=', 'store_grn_header.po_number')
            ->join('org_location', 'org_location.loc_id', '=', 'store_grn_header.location')
            ->join('org_supplier', 'org_supplier.supplier_id', '=', 'store_grn_header.sup_id')
            ->join('merc_customer_order_details', 'merc_customer_order_details.details_id', '=', 'store_grn_detail.customer_po_id')
            ->join('merc_customer_order_header', 'merc_customer_order_header.order_id', '=', 'merc_customer_order_details.order_id')
            ->join('cust_customer', 'cust_customer.customer_id', '=', 'merc_customer_order_header.order_customer')
            ->join('item_master', 'item_master.master_id', '=', 'store_grn_detail.item_code')
            ->join('style_creation', 'style_creation.style_id', '=', 'store_grn_detail.style_id')
            ->join('usr_profile', 'usr_profile.user_id', '=', 'store_grn_header.created_by')
            ->select(
                DB::raw("(DATE_FORMAT(store_grn_detail.created_date,'%d-%b-%Y')) AS grn_date"),
                'org_location.loc_name',
                'org_supplier.supplier_name',
                'cust_customer.customer_name',
                'usr_profile.first_name',
                'store_grn_header.inv_number',
                'merc_po_order_header.po_number',
                'item_master.master_code',
                'item_master.master_description',
                'store_grn_header.batch_no',
                'store_grn_detail.po_qty',
                'style_creation.style_no'
            );

        if (($date_from != null || $date_from != "") && ($date_to != null || $date_to != "")) {
            $query->whereBetween('store_grn_header.created_date', [date("Y-m-d",strtotime($date_from)), date("Y-m-d",strtotime($date_to))]);
        }

        if ($location != null || $location != "") {
            $query->where('store_grn_header.location', $location);
        }

        $load_list = $query->distinct()->get();

        echo json_encode([
            "data" => $load_list
        ]);
    }
}
