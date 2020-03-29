<?php

namespace App\Exports;

use App\Models\Org\UOM;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithStrictNullComparison;
use DB;

class FrOrderDownload implements FromArray, WithHeadings, WithMapping, WithStrictNullComparison
{
    use Exportable;

    public function array(): array
    {
        //return UOM::all();
        $load_list = DB::select("SELECT
                      merc_customer_order_details.order_id AS CO,
                      merc_customer_order_details.line_no AS LI,
                      costing_finish_goods.fg_code,
                      costing_finish_good_components.sfg_code,
                      style_creation.style_no,
                      product_component.product_component_description AS P_Type,
                      merc_color_options.color_option AS FAB_Type,
                      cust_customer.customer_name,
                      cust_division.division_description,
                      IF(merc_customer_order_details.delivery_status='CONNECTED','F','D') AS Deli_ST,
                      DATE_FORMAT(merc_customer_order_details.planned_delivery_date, '%d/%m/%Y') AS PDD,
                      merc_customer_order_details.order_qty,
                      merc_customer_order_details.po_no,
                      CC.color_code,
                      WIPC.color_name,
                      DATE_FORMAT(merc_customer_order_details.planned_delivery_date, '%Y') AS Sales_Y,
                      org_season.season_name,
                      IF(merc_customer_order_details.delivery_status!='CONNECTED',null,null) AS shipped,
                      merc_customer_order_details.ship_mode,
                      (merc_customer_order_details.fob / product_feature.count) AS SPRICE,
                      DATE_FORMAT(merc_customer_order_details.created_date, '%d/%m/%Y') AS CRE_DATE,
                      merc_customer_order_header.order_buy_name,
                      org_location.loc_name,
                      DATE_FORMAT(merc_customer_order_details.pcd, '%d/%m/%Y') AS PCD,
                      costing.id AS costingID,
                      org_country.country_description

                      FROM
                      merc_customer_order_details
                      INNER JOIN costing_finish_goods ON merc_customer_order_details.fg_id = costing_finish_goods.fg_id
                      INNER JOIN costing_finish_good_components ON costing_finish_goods.fg_id = costing_finish_good_components.fg_id
                      INNER JOIN product_component ON costing_finish_good_components.product_component_id = product_component.product_component_id
                      INNER JOIN merc_color_options ON merc_customer_order_details.colour_type = merc_color_options.col_opt_id
                      INNER JOIN merc_customer_order_header ON merc_customer_order_details.order_id = merc_customer_order_header.order_id
                      INNER JOIN style_creation ON merc_customer_order_header.order_style = style_creation.style_id
                      INNER JOIN cust_customer ON style_creation.customer_id = cust_customer.customer_id
                      INNER JOIN cust_division ON style_creation.division_id = cust_division.division_id
                      LEFT JOIN org_color AS CC ON costing_finish_goods.combo_color_id = CC.color_id
                      LEFT JOIN org_color AS WIPC ON costing_finish_good_components.color_id = WIPC.color_id
                      INNER JOIN org_season ON merc_customer_order_header.order_season = org_season.season_id
                      INNER JOIN product_feature ON style_creation.product_feature_id = product_feature.product_feature_id
                      INNER JOIN org_location ON merc_customer_order_details.projection_location = org_location.loc_id
                      INNER JOIN costing ON costing_finish_goods.costing_id = costing.id
                      INNER JOIN org_country ON merc_customer_order_details.country = org_country.country_id
                      WHERE
                      merc_customer_order_details.active_status = 'ACTIVE' and
                      merc_customer_order_details.delivery_status='CONNECTED'

        ");

        return $load_list;

    }

    public function map($fr): array
    {
        return [
            $fr->CO.'::'.$fr->LI.'::'.$fr->fg_code.'_'.$fr->sfg_code,
            $fr->style_no.'::'.$fr->P_Type.'::'.$fr->FAB_Type,
            $fr->customer_name.'::'.$fr->division_description,
            $fr->Deli_ST,
            $fr->PDD,
            $fr->order_qty,
            $fr->po_no,
            $fr->color_code.'::'.$fr->color_name,
            $fr->Sales_Y,
            $fr->season_name,
            $fr->order_qty,
            $fr->shipped,
            $fr->ship_mode,
            $fr->SPRICE,
            $fr->CRE_DATE,
            $fr->order_buy_name,
            $fr->loc_name,
            $fr->PCD,
            $fr->costingID,
            $fr->country_description,
            'END',

        ];
    }

    public function headings(): array
    {
        return [
            'O.CODE','O.PROD','O.CUST','O.STATUS','O^DD:1','O^DQ:1','O^DR:1','O.DESCRIP','O.SALES_YEAR',
            'O.SALES_SEASON','O.CONTRACT_QTY','O.COMPLETE','O.TRANS_OVERRIDE','O.SPRICE','O.EVBASE',
            'O.UdLot Number','O.UdFactory','O.UdInitial PCD','O.UdDOP','O.UdCountry','END'
        ];
    }
}
