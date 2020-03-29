<?php

namespace App\Http\Controllers\Reports;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Mpdf\Tag\Select;
use PDF;

class PODetailsController extends Controller
{
    public function index(Request $request)
    {
        $type = $request->type;
        if ($type == 'datatable') {
            $data = $request->all();
            $this->datatable_search($data);
        } elseif ($type == 'excel') {
            $data = $request->all();
            $this->excel_search($data);
        }
    }

    private function datatable_search($data)
    {
        $po_no = $data['data']['po_no']['po_number'];

        // $query = DB::table('merc_po_order_details')
        //     ->join('merc_po_order_header', 'merc_po_order_details.po_header_id', '=', 'merc_po_order_header.po_id')
        //     ->join('merc_shop_order_detail', 'merc_shop_order_detail.shop_order_detail_id', '=', 'merc_po_order_details.shop_order_detail_id')
        //     ->join('merc_shop_order_header', 'merc_shop_order_header.shop_order_id', '=', 'merc_po_order_details.shop_order_id')
        //     ->leftJoin('merc_customer_order_details', 'merc_customer_order_details.shop_order_id', '=', 'merc_shop_order_header.shop_order_id')
        //     ->join('merc_customer_order_header', 'merc_customer_order_header.order_id', '=', 'merc_customer_order_details.order_id')
        //     ->leftJoin('item_master AS ITEM', 'ITEM.master_id', '=', 'merc_po_order_details.item_code')
        //     ->leftJoin('org_uom AS Inv_UOM', 'Inv_UOM.uom_id', '=', 'ITEM.inventory_uom')
        //     ->leftJoin('org_uom AS Pur_UOM', 'Pur_UOM.uom_id', '=', 'merc_shop_order_detail.purchase_uom')
        //     ->join('costing', 'costing.id', '=', 'merc_shop_order_detail.costing_id')
        //     ->join('bom_header', 'bom_header.bom_id', '=', 'merc_po_order_details.bom_id')
        //     ->join('bom_details', 'bom_details.bom_id', '=', 'bom_header.bom_id')
        //     ->leftJoin('item_master AS FNG', 'FNG.master_id', '=', 'bom_header.fng_id')
        //     ->leftJoin('style_creation', 'style_creation.style_id', '=', 'merc_po_order_details.style')
        //     ->select(
        //         'merc_po_order_details.id AS po_det_id',
        //         'merc_po_order_header.po_id',
        //         'merc_po_order_details.line_no',
        //         'merc_customer_order_details.rm_in_date',
        //         'merc_customer_order_details.pcd',
        //         'merc_customer_order_header.order_code',
        //         'merc_customer_order_details.line_no',
        //         'merc_customer_order_details.po_no',
        //         'ITEM.master_code AS item_code',
        //         'ITEM.master_description AS item_description',
        //         'ITEM.standard_price',
        //         'merc_po_order_details.purchase_price',
        //         'Inv_UOM.uom_code AS Inventory_UOM',
        //         'Pur_UOM.uom_code AS purchase_UOM',
        //         'merc_shop_order_detail.po_qty',
        //         'costing.id AS costing_ID',
        //         'costing.epm AS cos_epm',
        //         'costing.np_margine AS cos_np',
        //         'bom_header.bom_id',
        //         'FNG.master_code AS FNG_NO',
        //         'style_creation.style_no',
        //         'bom_header.epm AS bom_epm',
        //         'bom_header.np_margin AS bom_np',
        //         'bom_details.sfg_code AS SFG_NO'
        //     );

        $query = DB::table('merc_po_order_header')
            ->join('usr_profile', 'usr_profile.user_id', '=', 'merc_po_order_header.created_by')
            ->select(
                'merc_po_order_header.po_id',
                'merc_po_order_header.po_number',
                'usr_profile.first_name'
            );

        if ($po_no != null || $po_no != "") {
            $query->where('merc_po_order_header.po_number', $po_no);
        }

        $load_list = $query->distinct()->get();

        echo json_encode([
            "data" => $load_list
        ]);
    }

    private function excel_search($data)
    {
        $po_no = $data['data']['po_no']['po_number'];

        $query = DB::table('merc_po_order_details')
            ->join('merc_po_order_header', 'merc_po_order_details.po_header_id', '=', 'merc_po_order_header.po_id')
            ->join('merc_shop_order_detail', 'merc_shop_order_detail.shop_order_detail_id', '=', 'merc_po_order_details.shop_order_detail_id')
            ->join('merc_shop_order_header', 'merc_shop_order_header.shop_order_id', '=', 'merc_po_order_details.shop_order_id')
            ->leftJoin('merc_customer_order_details', 'merc_customer_order_details.shop_order_id', '=', 'merc_shop_order_header.shop_order_id')
            ->join('merc_customer_order_header', 'merc_customer_order_header.order_id', '=', 'merc_customer_order_details.order_id')
            ->leftJoin('item_master AS ITEM', 'ITEM.master_id', '=', 'merc_po_order_details.item_code')
            ->leftJoin('org_uom AS Inv_UOM', 'Inv_UOM.uom_id', '=', 'ITEM.inventory_uom')
            ->leftJoin('org_uom AS Pur_UOM', 'Pur_UOM.uom_id', '=', 'merc_shop_order_detail.purchase_uom')
            ->join('costing', 'costing.id', '=', 'merc_shop_order_detail.costing_id')
            ->join('bom_header', 'bom_header.bom_id', '=', 'merc_po_order_details.bom_id')
            ->join('bom_details', 'bom_details.bom_id', '=', 'bom_header.bom_id')
            ->leftJoin('item_master AS FNG', 'FNG.master_id', '=', 'bom_header.fng_id')
            ->leftJoin('style_creation', 'style_creation.style_id', '=', 'merc_po_order_details.style')
            ->select(
                'merc_po_order_header.po_number',
                'merc_po_order_details.id AS po_det_id',
                'merc_po_order_header.po_id',
                'merc_po_order_details.line_no',
                'merc_customer_order_details.rm_in_date',
                'merc_customer_order_details.pcd',
                'merc_customer_order_header.order_code',
                'merc_customer_order_details.line_no',
                'merc_customer_order_details.po_no',
                'ITEM.master_code AS item_code',
                'ITEM.master_description AS item_description',
                'ITEM.standard_price',
                'merc_po_order_details.purchase_price',
                'Inv_UOM.uom_code AS Inventory_UOM',
                'Pur_UOM.uom_code AS purchase_UOM',
                'merc_shop_order_detail.po_qty',
                DB::raw("ROUND(merc_shop_order_detail.po_qty * ITEM.standard_price, 4) AS value"),
                'costing.id AS costing_ID',
                'costing.epm AS cos_epm',
                'costing.np_margine AS cos_np',
                'bom_header.bom_id',
                'FNG.master_code AS FNG_NO',
                'style_creation.style_no',
                'bom_header.epm AS bom_epm',
                'bom_header.np_margin AS bom_np',
                'bom_details.sfg_code AS SFG_NO'
            );

        if ($po_no != null || $po_no != "") {
            $query->where('merc_po_order_header.po_number', $po_no);
        }

        $load_list = $query->distinct()->get();

        echo json_encode([
            "data" => $load_list
        ]);
    }

    public function viewPODetails(Request $request)
    {
        $po_id = $request->ci;
        // $po_id = $request->di;

        $data['company'] = DB::table('merc_po_order_header')
            ->join('org_location', 'merc_po_order_header.po_deli_loc', '=', 'org_location.loc_id')
            ->join('org_company', 'org_location.company_id', '=', 'org_company.company_id')
            ->join('org_country', 'org_location.country_code', '=', 'org_country.country_id')
            ->select('org_location.*', 'org_company.company_name', 'org_country.country_description')
            ->where('merc_po_order_header.po_id', '=', $po_id)
            ->get();

        $data['headers'] = DB::table('merc_po_order_header')
            ->join('merc_po_order_details', 'merc_po_order_details.po_header_id', '=', 'merc_po_order_header.po_id')
            ->join('merc_shop_order_detail', 'merc_shop_order_detail.shop_order_detail_id', '=', 'merc_po_order_details.shop_order_detail_id')
            ->join('merc_shop_order_header', 'merc_shop_order_header.shop_order_id', '=', 'merc_po_order_details.shop_order_id')
            ->leftJoin('merc_customer_order_details', 'merc_customer_order_details.shop_order_id', '=', 'merc_shop_order_header.shop_order_id')
            ->join('merc_customer_order_header', 'merc_customer_order_header.order_id', '=', 'merc_customer_order_details.order_id')
            ->join('cust_division', 'cust_division.division_id', '=', 'merc_customer_order_header.order_division')
            ->join('fin_currency', 'fin_currency.currency_id', '=', 'merc_po_order_header.po_def_cur')
            ->join('org_supplier', 'org_supplier.supplier_id', '=', 'merc_po_order_header.po_sup_code')
            ->join('org_location', 'org_location.loc_id', '=', 'merc_po_order_header.po_deli_loc')
            ->join('org_company', 'org_company.company_id', '=', 'merc_po_order_header.invoice_to')
            ->join('usr_profile', 'usr_profile.user_id', '=', 'merc_po_order_header.created_by')
            ->select(
                'merc_po_order_header.po_number',
                DB::raw("DATE_FORMAT(merc_po_order_header.created_date, '%d-%b-%Y') 'created_date'"),
                'cust_division.division_description',
                'usr_profile.first_name',
                'fin_currency.currency_code',
                'org_supplier.supplier_name',
                'org_location.loc_name',
                'org_company.company_name',
                'merc_po_order_header.po_status'
            )
            ->where('merc_po_order_header.po_id', $po_id)
            ->distinct()
            ->get();


        $data['details'] = DB::table('merc_po_order_details')
            ->join('merc_shop_order_detail', 'merc_shop_order_detail.shop_order_detail_id', '=', 'merc_po_order_details.shop_order_detail_id')
            ->join('merc_shop_order_header', 'merc_shop_order_header.shop_order_id', '=', 'merc_po_order_details.shop_order_id')
            ->leftJoin('merc_customer_order_details', 'merc_customer_order_details.shop_order_id', '=', 'merc_shop_order_header.shop_order_id')
            ->join('merc_customer_order_header', 'merc_customer_order_header.order_id', '=', 'merc_customer_order_details.order_id')
            ->leftJoin('item_master AS ITEM', 'ITEM.master_id', '=', 'merc_po_order_details.item_code')
            ->leftJoin('org_uom AS Inv_UOM', 'Inv_UOM.uom_id', '=', 'ITEM.inventory_uom')
            ->leftJoin('org_uom AS Pur_UOM', 'Pur_UOM.uom_id', '=', 'merc_shop_order_detail.purchase_uom')
            ->join('costing', 'costing.id', '=', 'merc_shop_order_detail.costing_id')
            ->join('bom_header', 'bom_header.bom_id', '=', 'merc_po_order_details.bom_id')
            ->join('bom_details', 'bom_details.bom_id', '=', 'bom_header.bom_id')
            ->leftJoin('item_master AS FNG', 'FNG.master_id', '=', 'bom_header.fng_id')
            ->leftJoin('style_creation', 'style_creation.style_id', '=', 'merc_po_order_details.style')
            ->select(
                'merc_po_order_details.id AS po_id',
                'merc_po_order_details.line_no',
                'merc_customer_order_details.rm_in_date',
                'merc_customer_order_details.pcd',
                'merc_customer_order_header.order_code',
                'merc_customer_order_details.line_no',
                'merc_customer_order_details.po_no',
                'ITEM.master_code AS item_code',
                'ITEM.master_description AS item_description',
                'ITEM.standard_price',
                'merc_po_order_details.purchase_price',
                'Inv_UOM.uom_code AS Inventory_UOM',
                'Pur_UOM.uom_code AS purchase_UOM',
                'merc_shop_order_detail.po_qty',
                DB::raw("ROUND(merc_shop_order_detail.po_qty * ITEM.standard_price, 4) AS value"),
                'costing.id AS costing_ID',
                'costing.epm AS cos_epm',
                'costing.np_margine AS cos_np',
                'bom_header.bom_id',
                'FNG.master_code AS FNG_NO',
                'style_creation.style_no',
                'bom_header.epm AS bom_epm',
                'bom_header.np_margin AS bom_np',
                'bom_details.sfg_code AS SFG_NO'
            )
            ->where('merc_po_order_details.po_header_id', $po_id)
            ->distinct()
            ->get();

        // dd($data);
        $pdf = PDF::loadView('reports/po-details', $data)
            //->download('Cost Sheet - CI'.$request->costing_id.'.pdf');
            ->stream('PO Details PO No-' . $request->ci . '.pdf');
        return $pdf;

        // $excel = Excel::loadView('reports.po-details', array('data' => $data))->export('xls');
        // return view('reports.po-details', compact('data'));
    }
}
