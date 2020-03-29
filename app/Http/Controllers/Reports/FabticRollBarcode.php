<?php

namespace App\Http\Controllers\Reports;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

class FabticRollBarcode extends Controller
{
    public function index(Request $request)
    {
    }

    public function getData(Request $request)
    {
        # code...
        $query = '';
        $load_list = [];
        $barcode_type = $request->type_of_barcode;
        $po_number = $request->po_number['po_number'];
        $invoice_no = $request->invoice_no;
        $barcode_from = $request->barcode_from;
        $barcode_to = $request->barcode_to;

        if ($barcode_type == 'fabric') {

            $query = DB::table('store_roll_plan')
                ->join('store_grn_detail', 'store_grn_detail.grn_detail_id', '=', 'store_roll_plan.grn_detail_id')
                ->join('store_grn_header', 'store_grn_header.grn_id', '=', 'store_grn_detail.grn_id')
                ->join('merc_po_order_header', 'merc_po_order_header.po_id', '=', 'store_grn_header.po_number')
                ->join('merc_shop_order_header', 'merc_shop_order_header.shop_order_id', '=', 'store_grn_detail.shop_order_id')
                ->join('merc_shop_order_detail', 'merc_shop_order_detail.shop_order_id', '=', 'merc_shop_order_header.shop_order_id')
                ->join('merc_shop_order_delivery', 'merc_shop_order_delivery.shop_order_id', '=', 'merc_shop_order_header.shop_order_id')
                ->join('merc_customer_order_details', 'merc_customer_order_details.details_id', '=', 'merc_shop_order_delivery.delivery_id')
                ->join('merc_customer_order_header', 'merc_customer_order_header.order_id', '=', 'merc_customer_order_details.order_id')
                ->join('item_master', 'item_master.master_id', '=', 'store_grn_detail.item_code')
                ->join('org_supplier', 'org_supplier.supplier_id', '=', 'store_grn_header.sup_id')
                ->leftJoin('org_color', 'org_color.color_id', '=', 'store_grn_detail.color')
                ->join('style_creation', 'style_creation.style_id', '=', 'store_grn_detail.style_id')
                ->leftJoin('org_season', 'org_season.season_id', '=', 'merc_customer_order_header.order_season')
                ->leftJoin('cust_division', 'cust_division.division_id', '=', 'merc_customer_order_header.order_division')
                ->select(
                    'store_roll_plan.roll_plan_id',
                    'store_roll_plan.barcode',
                    'store_roll_plan.batch_no',
                    'store_roll_plan.roll_no',
                    'store_roll_plan.lot_no',
                    'store_roll_plan.received_qty',
                    'store_roll_plan.invoice_no',
                    'store_roll_plan.created_date',
                    'item_master.master_code',
                    'item_master.master_description',
                    'org_supplier.supplier_code',
                    'org_supplier.supplier_name',
                    'merc_po_order_header.po_number',
                    'merc_customer_order_header.order_code',
                    'merc_customer_order_details.line_no',
                    'org_color.color_name',                    
                    'item_master.supplier_reference',
                    'style_creation.style_no',
                    'org_season.season_name',
                    'cust_division.division_description'
                );

            if ($po_number != null || $po_number != "") {
                $query->where('merc_po_order_header.po_number', $po_number);
            }

            if ($invoice_no != null || $invoice_no != "") {
                $query->where('store_roll_plan.invoice_no', $invoice_no);
            }

            if (($barcode_from != null || $barcode_from != "") && ($barcode_to != null || $barcode_to != "")) {
                $query->whereBetween('store_roll_plan.barcode', [$barcode_from, $barcode_to]);
            }

            if ($query) {
                $load_list = $query->distinct()->get();
            }

            foreach ($load_list as $item) {
                $po = $item->po_number;
                $batch = $item->batch_no;
                $roll = $item->roll_no;
                $updatedBarcode = $item->barcode;

                if ($updatedBarcode == '') {
                    $barcode = $po . $batch . $roll;

                    DB::table('store_roll_plan')
                        ->where('store_roll_plan.batch_no', $batch)
                        ->where('store_roll_plan.roll_no', $roll)
                        ->update(['store_roll_plan.barcode' => $barcode]);
                }
            }
        } elseif ($barcode_type == 'trim') {

            $query = DB::table('store_trim_packing_detail')
                ->join('store_grn_detail', 'store_grn_detail.grn_detail_id', '=', 'store_trim_packing_detail.grn_detail_id')
                ->join('store_grn_header', 'store_grn_header.grn_id', '=', 'store_grn_detail.grn_id')
                ->join('merc_po_order_header', 'merc_po_order_header.po_id', '=', 'store_grn_header.po_number')
                ->join('merc_shop_order_header', 'merc_shop_order_header.shop_order_id', '=', 'store_grn_detail.shop_order_id')
                ->join('merc_shop_order_detail', 'merc_shop_order_detail.shop_order_id', '=', 'merc_shop_order_header.shop_order_id')
                ->join('merc_shop_order_delivery', 'merc_shop_order_delivery.shop_order_id', '=', 'merc_shop_order_header.shop_order_id')
                ->join('merc_customer_order_details', 'merc_customer_order_details.details_id', '=', 'merc_shop_order_delivery.delivery_id')
                ->join('merc_customer_order_header', 'merc_customer_order_header.order_id', '=', 'merc_customer_order_details.order_id')
                ->join('item_master', 'item_master.master_id', '=', 'store_grn_detail.item_code')
                ->join('org_supplier', 'org_supplier.supplier_id', '=', 'store_grn_header.sup_id')
                ->leftJoin('org_color', 'org_color.color_id', '=', 'store_grn_detail.color')
                ->join('style_creation', 'style_creation.style_id', '=', 'store_grn_detail.style_id')
                ->leftJoin('org_season', 'org_season.season_id', '=', 'merc_customer_order_header.order_season')
                ->leftJoin('cust_division', 'cust_division.division_id', '=', 'merc_customer_order_header.order_division')
                ->select(
                    'store_trim_packing_detail.trim_packing_id',
                    'store_trim_packing_detail.barcode',
                    'store_trim_packing_detail.batch_no',
                    'store_trim_packing_detail.box_no',
                    'store_trim_packing_detail.received_qty',
                    'store_trim_packing_detail.invoice_no',
                    'store_trim_packing_detail.created_date',
                    'item_master.master_code',
                    'item_master.master_description',
                    'org_supplier.supplier_code',
                    'org_supplier.supplier_name',
                    'merc_po_order_header.po_number',
                    'merc_customer_order_header.order_code',
                    'merc_customer_order_details.line_no',
                    'org_color.color_name',
                    'item_master.supplier_reference',
                    'style_creation.style_no',
                    'org_season.season_name',
                    'cust_division.division_description'
                );

            if ($po_number != null || $po_number != "") {
                $query->where('merc_po_order_header.po_number', $po_number);
            }

            if ($invoice_no != null || $invoice_no != "") {
                $query->where('store_trim_packing_detail.invoice_no', $invoice_no);
            }

            if (($barcode_from != null || $barcode_from != "") && ($barcode_to != null || $barcode_to != "")) {
                $query->whereBetween('store_trim_packing_detail.barcode', [$barcode_from, $barcode_to]);
            }

            if ($query) {
                $load_list = $query->distinct()->get();
            }

            foreach ($load_list as $item) {
                $po = $item->po_number;
                $batch = $item->batch_no;
                $box = $item->box_no;
                $updatedBarcode = $item->barcode;

                if ($updatedBarcode == '') {
                    $barcode = $po . $batch . $box;

                    DB::table('store_trim_packing_detail')
                        ->where('store_trim_packing_detail.batch_no', $batch)
                        ->where('store_trim_packing_detail.box_no', $box)
                        ->update(['store_trim_packing_detail.barcode' => $barcode]);
                }
            }
        }

        echo json_encode([
            "data" => $load_list
        ]);
    }

    public function updatePrint(Request $request)
    {
        $barcodes = $request->param;
        foreach ($barcodes as $barcode) {
            DB::table('store_roll_plan')
                ->where('store_roll_plan.barcode', $barcode)
                ->update(['store_roll_plan.print_status' => 'Printed']);
        }
    }

    public function updateTrimPrint(Request $request)
    {
        $barcodes = $request->param;
        foreach ($barcodes as $barcode) {
            DB::table('store_trim_packing_detail')
                ->where('store_trim_packing_detail.barcode', $barcode)
                ->update(['store_trim_packing_detail.print_status' => 'Printed']);
        }
    }

    public function deleteBarcode(Request $request)
    {
        $roll_plan_id = $request->roll;
        $batch_no = $request->batch;

        $query = DB::table('store_roll_plan')
            ->where('store_roll_plan.roll_plan_id', $roll_plan_id)
            ->where('store_roll_plan.batch_no', $batch_no)
            ->where(function ($query) {
                $query->where('store_roll_plan.print_status', null)
                    ->orWhere('store_roll_plan.print_status', '!=', 'Printed');
            })
            ->update(['store_roll_plan.barcode' => '']);

        if ($query == 1) {
            echo json_encode([
                'data' => [
                    'message' => 'Barcode number delete successfully.',
                    'status' => 1
                ]
            ]);
        } else {
            echo json_encode([
                'data' => [
                    'message' => 'Barcode already printed.',
                    'status' => 0
                ]
            ]);
        }
    }

    public function deleteTrimBarcode(Request $request)
    {
        $trim_id = $request->trim;
        $batch_no = $request->batch;

        $query = DB::table('store_trim_packing_detail')
            ->where('store_trim_packing_detail.trim_packing_id', $trim_id)
            ->where('store_trim_packing_detail.batch_no', $batch_no)
            ->where(function ($query) {
                $query->where('store_trim_packing_detail.print_status', null)
                    ->orWhere('store_trim_packing_detail.print_status', '!=', 'Printed');
            })
            ->update(['store_trim_packing_detail.barcode' => '']);

        if ($query == 1) {
            echo json_encode([
                'data' => [
                    'message' => 'Barcode number delete successfully.',
                    'status' => 1
                ]
            ]);
        } else {
            echo json_encode([
                'data' => [
                    'message' => 'Barcode already printed.',
                    'status' => 0
                ]
            ]);
        }
    }
}
