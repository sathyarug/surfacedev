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

class FrCsvDownload implements FromArray, WithHeadings, WithMapping, WithStrictNullComparison
{
    use Exportable;

    public function array(): array
    {
        //return UOM::all();
        $load_list = DB::select("SELECT
        style_creation.style_no AS style,
        product_silhouette.product_silhouette_description AS silhouette,
        merc_color_options.color_option,
        product_component.product_component_description AS component,
        prod_category.prod_cat_description AS Product_type,
        (
        SELECT
        IF(OM.garment_operation_name='PRINT',1,0) AS print
        FROM
        ie_component_smv_details AS IEH
        INNER JOIN ie_garment_operation_master AS OM ON IEH.garment_operation_id = OM.garment_operation_id
        WHERE
        IEH.smv_component_header_id = ie_component_smv_details.smv_component_header_id AND
        IEH.product_silhouette_id = ie_component_smv_details.product_silhouette_id AND
        OM.garment_operation_name = 'PRINT'
        ) AS print,

        (
        SELECT
        IF(OM.garment_operation_name='PAD PRINTING',1,0) AS pad_print
        FROM
        ie_component_smv_details AS IEH
        INNER JOIN ie_garment_operation_master AS OM ON IEH.garment_operation_id = OM.garment_operation_id
        WHERE
        IEH.smv_component_header_id = ie_component_smv_details.smv_component_header_id AND
        IEH.product_silhouette_id = ie_component_smv_details.product_silhouette_id AND
        OM.garment_operation_name = 'PAD PRINTING'
        GROUP BY
        OM.garment_operation_name
      ) AS pad_print,

      (
        SELECT
        IF(OM.garment_operation_name='HEAT SEAL',1,0) AS heat_seal
        FROM
        ie_component_smv_details AS IEH
        INNER JOIN ie_garment_operation_master AS OM ON IEH.garment_operation_id = OM.garment_operation_id
        WHERE
        IEH.smv_component_header_id = ie_component_smv_details.smv_component_header_id AND
        IEH.product_silhouette_id = ie_component_smv_details.product_silhouette_id AND
        OM.garment_operation_name = 'HEAT SEAL'
        GROUP BY
        OM.garment_operation_name
      ) AS heat_seal,
      (
        SELECT
        CSUM.total_smv AS total_smv
        FROM
        ie_component_smv_summary AS CSUM
        WHERE
        CSUM.smv_component_header_id = ie_component_smv_details.smv_component_header_id AND
        CSUM.product_silhouette_id = ie_component_smv_details.product_silhouette_id AND
        CSUM.line_no = ie_component_smv_details.line_no

      )AS total_smv,
      (
        SELECT
        if(Sum(PFC.emblishment)>= 1,1,0) AS emblishment
        FROM
        product_feature_component AS PFC
        WHERE
        PFC.product_feature_id = style_creation.product_feature_id AND
        PFC.product_component_id = ie_component_smv_details.product_component_id AND
        PFC.product_silhouette_id = ie_component_smv_details.product_silhouette_id
        ) AS emblishment,

        (
        SELECT
        if(Sum(PFC.washing)>= 1,1,0) AS washing
        FROM
        product_feature_component AS PFC
        WHERE
        PFC.product_feature_id = style_creation.product_feature_id AND
        PFC.product_component_id = ie_component_smv_details.product_component_id AND
        PFC.product_silhouette_id = ie_component_smv_details.product_silhouette_id
        ) AS washing

        FROM
        ie_component_smv_header
        INNER JOIN ie_component_smv_details ON ie_component_smv_header.smv_component_header_id = ie_component_smv_details.smv_component_header_id
        INNER JOIN style_creation ON ie_component_smv_header.style_id = style_creation.style_id
        INNER JOIN prod_category ON style_creation.product_category_id = prod_category.prod_cat_id
        INNER JOIN merc_color_options ON ie_component_smv_header.col_opt_id = merc_color_options.col_opt_id
        INNER JOIN product_silhouette ON ie_component_smv_details.product_silhouette_id = product_silhouette.product_silhouette_id
        INNER JOIN product_component ON ie_component_smv_details.product_component_id = product_component.product_component_id

        GROUP BY
        ie_component_smv_details.product_silhouette_id,
        ie_component_smv_details.smv_component_header_id
        ");

        return $load_list;

    }

    public function map($fr): array
    {
        return [
            $fr->style.'::'.$fr->silhouette.'_'.$fr->component.'::'.$fr->color_option,
            $fr->silhouette.'_'.$fr->component,
            $fr->Product_type,
            1,
            ($fr->print == 1 ? 1 : 0),
            ($fr->pad_print == 1 ? 1 : 0),
            ($fr->heat_seal == 1 ? 1 : 0),
            ($fr->emblishment == 1 ? 1 : 0),
            1,
            ($fr->total_smv == 1 ? 1 : 0),
            ($fr->washing == 1 ? 1 : 0),
            1,
            1,
            'END',
        ];
    }

    public function headings(): array
    {
        return [
            'P.CODE','P.TYPE','P.DESCRIP','P^WC:10','P^WC:20','P^WC:30','P^WC:40','P^WC:50','P^WC:60','P^WC:70'
            ,'P^WC:80','P^WC:90','P^WC:100','END'
        ];
    }
}
