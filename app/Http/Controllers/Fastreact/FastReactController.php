<?php

namespace App\Http\Controllers\Fastreact;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Http\Controllers\Controller;
use App\Models\Merchandising\StyleCreation;
use App\Models\IE\ComponentSMVHeader;
use App\Models\IE\ComponentSMVDetails;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\FrCsvDownload;
use App\Exports\FrOrderDownload;
use DB;


class FastReactController extends Controller
{

    public function index(Request $request)
    {
        $type = $request->type;

        if($type == 'datatable') {
            $data = $request->all();
            return response($this->datatable_search($data));
        }
        else{ }

    }

    private function datatable_search($data)
    {

      $start = $data['start'];
      $length = $data['length'];
      $draw = $data['draw'];
      $search = $data['search']['value'];
      $order = $data['order'][0];
      $order_column = $data['columns'][$order['column']]['data'];
      $order_type = $order['dir'];

      $cluster_list = StyleCreation::select('*')
      ->where('style_no','like',$search.'%')
      ->orWhere('style_description'  , 'like', $search.'%' )
      ->orderBy($order_column, $order_type)
      ->offset($start)->limit($length)->get();

      $cluster_count = StyleCreation::select('*')
      ->where('style_no','like',$search.'%')
      ->orWhere('style_description'  , 'like', $search.'%' )
      ->count();

      return [
          "draw" => $draw,
          "recordsTotal" => $cluster_count,
          "recordsFiltered" => $cluster_count,
          "data" => $cluster_list
      ];

    }




    public function load_fr_Details(Request $request)
  	{

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
    Sum(PFC.emblishment) AS emblishment
    FROM
    product_feature_component AS PFC
    WHERE
    PFC.product_feature_id = style_creation.product_feature_id AND
    PFC.product_component_id = ie_component_smv_details.product_component_id AND
    PFC.product_silhouette_id = ie_component_smv_details.product_silhouette_id
    ) AS emblishment,

    (
    SELECT
    Sum(PFC.washing) AS washing
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




       //return $customer_list;
       return response([ 'data' => [
         'load_list' => $load_list,
         'count' => sizeof($load_list)
         ]
       ], Response::HTTP_CREATED );

  	}


    public function export_csv(Request $request)
    {

      return (new FrCsvDownload)->download('PRODUCTS.CSV', \Maatwebsite\Excel\Excel::CSV,
      ['Content-Type' => 'text/csv']);

    }

    public function export_csv_orders(Request $request)
    {

      return (new FrOrderDownload)->download('ORDERS.CSV', \Maatwebsite\Excel\Excel::CSV,
      ['Content-Type' => 'text/csv']);

    }





}
