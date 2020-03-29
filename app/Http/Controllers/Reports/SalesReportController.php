<?php

namespace App\Http\Controllers\Reports;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Collection;
use App\Models\Report\DbLog;

class SalesReportController extends Controller
{
  public function index(Request $request)
  {
    $type = $request->type;
    if ($type == 'datatable') {
      $cus = $request->cus;
      $pcd_from = $request->from;
      $pcd_to = $request->to;
      $status = $request->status;
      $this->datatable_search($cus, $pcd_from, $pcd_to, $status);
    } else if ($type == 'headers') {
      $this->load_sizes();
    }
  }

  public function db2()
  {
    $employee_attendance =
      DB::Connection('mysql2')
      ->table('d2d_ord_detail')
      // ->leftJoin('USERINFO', 'CHECKINOUT.USERID', '=', 'USERINFO.USERID')
      // ->leftjoin(DB::Connection('mysql'))
      ->table('EMPLOYEE', 'USERINFO.CardNo', '=', 'EMPLOYEE.CardID')
      ->where('d2d_ord_detail.scNumber', '=', 2)
      ->take(1)
      ->get();

    // $response = DB::table($database1 . '.table1 as t1')
    //   ->leftJoin($database2 . '.table2 as t2', 't2.t1_id', '=', 't2.id')
    //   ->get();

    // DB::Connection('sqlsrv')
    //               ->table('CHECKINOUT')
    //               ->leftJoin('USERINFO', 'CHECKINOUT.USERID', '=', 'USERINFO.USERID')
    //               ->leftjoin(DB::Connection('sqlsrv2'))                       
    //               ->table('EMPLOYEE', 'USERINFO.CardNo', '=', 'EMPLOYEE.CardID')
    //               ->get();

    echo "<pre>";
    print_r($employee_attendance);
    echo "</pre>";
  }

  private function datatable_search($customer, $pcd_from, $pcd_to, $status)
  {
    $query = DB::table('merc_customer_order_details')
      ->join('merc_customer_order_header', 'merc_customer_order_header.order_id', '=', 'merc_customer_order_details.order_id')
      ->join('cust_customer', 'cust_customer.customer_id', '=', 'merc_customer_order_header.order_customer')
      ->join('cust_division', 'cust_division.division_id', '=', 'merc_customer_order_header.order_division')
      ->join('org_country', 'org_country.country_id', '=', 'merc_customer_order_details.country')
      ->join('style_creation', 'style_creation.style_id', '=', 'merc_customer_order_header.order_style')
      ->join('org_color', 'org_color.color_id', '=', 'merc_customer_order_details.style_color')
      ->join('merc_shop_order_header', 'merc_shop_order_header.shop_order_id', '=', 'merc_customer_order_details.shop_order_id')
      ->join('merc_shop_order_detail', 'merc_shop_order_detail.shop_order_id', '=', 'merc_shop_order_header.shop_order_id')
      ->join('item_master', 'item_master.master_id', '=', 'merc_customer_order_details.fng_id')
      // ->join('costing', 'costing.id', '=', 'merc_customer_order_details.costing_id')
      ->join('org_season', 'org_season.season_id', '=', 'merc_customer_order_header.order_season')
      ->join('org_location', 'org_location.loc_id', '=', 'merc_customer_order_details.user_loc_id')
      // ->join('merc_customer_order_size', 'merc_customer_order_size.details_id', '=', 'merc_customer_order_details.details_id')
      ->select(
        'merc_customer_order_details.details_id',
        'cust_customer.customer_name',
        'cust_division.division_description',
        'merc_customer_order_details.planned_delivery_date',
        'merc_customer_order_header.order_buy_name',
        'merc_customer_order_header.order_type',
        'org_country.country_description',
        'style_creation.remark_style',
        'style_creation.style_no',
        'style_creation.style_description',
        'org_color.color_code',
        'org_color.color_name',
        DB::raw('(SELECT DATE(merc_customer_order_details.created_date)) AS order_date'),
        'merc_customer_order_details.ac_date',
        'merc_customer_order_details.revised_delivery_date',
        'merc_customer_order_details.ship_mode',
        'merc_customer_order_details.excess_presentage',
        'merc_customer_order_details.po_no',
        'merc_customer_order_details.order_qty',
        'merc_customer_order_details.fob',
        'merc_shop_order_header.shop_order_id',
        'item_master.master_code',
        'merc_customer_order_details.active_status',
        'org_season.season_name',
        'org_location.loc_name',
        'merc_customer_order_details.details_id'
        // DB::raw('(SELECT sum(merc_customer_order_size.order_qty) as total1 FROM merc_customer_order_size 
        //         WHERE merc_customer_order_size.details_id = merc_customer_order_details.details_id GROUP BY merc_customer_order_details.details_id) AS total1'),
        // DB::raw('(SELECT sum(merc_customer_order_size.planned_qty) as total2 FROM merc_customer_order_size 
        //         WHERE merc_customer_order_size.details_id = merc_customer_order_details.details_id GROUP BY merc_customer_order_details.details_id) AS total2')
        // DB::raw('sum(merc_customer_order_size.planned_qty) as total2')
      )
      ->where('merc_customer_order_details.active_status', 'ACTIVE');

    if ($customer != null || $customer != "") {
      $query->where('cust_customer.customer_id', $customer);
    }

    if ($status != null || $status != "") {
      $query->where('merc_customer_order_header.order_status', $status);
    }

    if (($pcd_from != null || $pcd_from != "") && ($pcd_to != null || $pcd_to != "")) {
      $query->whereBetween('merc_customer_order_details.pcd', [$pcd_from, $pcd_to]);
    }

    $load_list = $query->distinct()->get();

    $sizeA = [];
    $sizeLast = [];
    $sumQua = [];
    $loadCount = $load_list->count();

    for ($i = 0; $i < $loadCount; $i++) {
      array_push($sizeA, $load_list[$i]->details_id);
    }

    $sizeUniq = array_unique($sizeA);

    foreach ($sizeUniq as $p) {

      $query2 = DB::table('merc_customer_order_size')
        ->join('merc_customer_order_details', 'merc_customer_order_details.details_id', '=', 'merc_customer_order_size.details_id')
        ->join('org_size', 'org_size.size_id', '=', 'merc_customer_order_size.size_id')
        ->select(
          'merc_customer_order_details.details_id',
          'org_size.size_name AS size',
          'merc_customer_order_size.order_qty AS quantity'
        )
        ->where('merc_customer_order_details.details_id', $p)
        ->distinct()
        ->get();

      // $query3 = DB::table('merc_customer_order_size')
      //   ->join('merc_customer_order_details', 'merc_customer_order_details.details_id', '=', 'merc_customer_order_size.details_id')
      //   ->join('org_size', 'org_size.size_id', '=', 'merc_customer_order_size.size_id')
      //   ->select(
      //     'merc_customer_order_details.details_id',
      //     DB::raw('sum(merc_customer_order_size.order_qty) as total1'),
      //     DB::raw('sum(merc_customer_order_size.planned_qty) as total2')
      //   )
      //   ->where('merc_customer_order_details.details_id', $p)
      //   ->groupBy('merc_customer_order_details.details_id')
      //   ->distinct()
      //   ->get();

      foreach ($query2 as $p) {
        array_push($sizeLast, $query2);
      }

      // foreach ($query3 as $p) {
      //   array_push($sumQua, $query3);
      // }
    }

    $merged_collection = new Collection();
    $merged_collection_total = new Collection();

    foreach ($sizeLast as $collection) {
      foreach ($collection as $item) {
        $merged_collection->push($item);
      }
    }

    // foreach ($sumQua as $collection) {
    //   foreach ($collection as $item) {
    //     $merged_collection_total->push($item);
    //   }
    // }

    $arr = [];
    $arrFinal = [];
    $arraysx = [];
    $sizeLastC = count($merged_collection);

    for ($x = 0; $x < $sizeLastC; $x++) {
      $arr[$x]['details_id'] = $merged_collection[$x]->details_id;
      $arr[$x]['size'] = $merged_collection[$x]->size;
      $arr[$x]['qua'] = $merged_collection[$x]->quantity;
      // $arr[$x]['' . $merged_collection[$x]->size . ''] = $merged_collection[$x]->quantity;
    }

    $arrFinal = array_map('unserialize', array_unique(array_map('serialize', $arr)));

    foreach ($arrFinal as $object) {
      $arraysx[] = (array) $object;
    }

    echo json_encode([
      "recordsTotal" => "",
      "recordsFiltered" => "",
      "sizes" => $arraysx,
      // "total" => $merged_collection_total,
      "data" => $load_list
    ]);
  }
}
