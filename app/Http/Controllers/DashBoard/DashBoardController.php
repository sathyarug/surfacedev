<?php

namespace App\Http\Controllers\DashBoard;

use App\Models\Merchandising\StyleCreation;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Merchandising\CustomerOrder;
use App\Models\Merchandising\CustomerOrderDetails;
use App\Models\Org\Customer;
use App\Models\Org\Division;
use App\Models\Admin\ProcessApproval;
use App\Models\Merchandising\Costing\Costing;
use App\Models\Merchandising\PoOrderHeader;
use App\Models\Merchandising\Item\Item;
use App\Models\Store\GrnHeader;
use DB;

class DashBoardController extends Controller
{
    public function index(Request $request)
    {
       if($request->type == 'customer-order-data'){
            //$this->loadCustomerOrderData();
           return response([
               'cus_data' => $this->loadCustomerOrderData()
           ]);
       }if($request->type == 'customer-order-data-value'){
            //$this->loadCustomerOrderData();
           return $this->loadCustomerOrderDataValue();

       }elseif($request->type == 'customer-order-detail'){

           return response([
               'cus_data' => $this->loadCustomerOrderDetails($request->customer)
           ]);
       }elseif($request->type == 'pending-costing-detail'){

           return response([
               'cus_data' => $this->loadPendingCostingDetails()
           ]);
       }elseif($request->type == 'edit-mode-data'){

           return response([
               'cus_data' => $this->loadEditModeDetails()
           ]);
       }elseif($request->type == 'load-po-approval'){

           return response([
               'cus_data' => $this->loadPoApprovalData()
           ]);
       }elseif($request->type == 'load-smv-update'){
           return response([
               'cus_data' => $this->loadPendingSmvData($request->customer)
           ]);
       }elseif($request->type == 'load-order-status'){

           return response([
               'cus_data' => $this->loadOrderStatus()
           ]);
       }elseif($request->type == 'load-pending-grn'){

           return response([
               'cus_data' => $this->loadPendingGrnData()
           ]);
       }elseif($request->type == 'load-item-creation'){

           return response([
               'cus_data' => $this->loadItemApprovalData()
           ]);
       } elseif($request->type == 'load-quarter-month-wise'){

           return $this->loadQuaterMonthWise();

       }

       elseif($request->type == 'load-quarterby-quarter'){

           return $this->loadq1CurrentYear();

       }
       elseif($request->type == 'load-quarter-1'){

           return $this->loadQ1();

       }
       elseif($request->type == 'load-quarter-1-last'){

           return $this->loadQ1Last();

       }elseif($request->type == 'load-quarter-1-lasttwo'){

           return $this->loadQ1LastTwo();

       }elseif($request->type == 'load-quarter-2'){

           return $this->loadQ2();

       }elseif($request->type == 'load-quarter-2-last'){

           return $this->loadQ2Last();

       }elseif($request->type == 'load-quarter-2-lasttwo'){

           return $this->loadQ2LastTwo();

       }elseif($request->type == 'load-quarter-3'){

           return $this->loadQ3();

       }elseif($request->type == 'load-quarter-3-last'){

           return $this->loadQ3Last();

       }elseif($request->type == 'load-quarter-3-lasttwo'){

           return $this->loadQ3LastTwo();

       }elseif($request->type == 'load-quarter-4'){

           return $this->loadQ4();

       }elseif($request->type == 'load-quarter-4-last'){

           return $this->loadQ4Last();

       }elseif($request->type == 'load-quarter-4-lasttwo'){

           return $this->loadQ4LastTwo();

       }elseif($request->type == 'load-quarter-wise'){

           return $this->loadQuarterWise();
       }
       elseif($request->type == 'load-quarter-wise-lastyear'){

           return $this->loadQuarterLastYear();
       }elseif($request->type == 'load-quarter-wise-lasttwoyear'){

           return $this->loadQuarterTwoLastYear();
       }


       elseif($request->type == 'load-quarter'){

           return $this->datatable_search();
       } elseif($request->type == 'load-otd'){

           return $this->loadOTD();
       }
       elseif($request->type == 'load-otd-lastyear'){

          return $this->loadOTDLastYear();
      }
      elseif($request->type == 'load-otd-lasttwoyear'){

         return $this->loadOTDLastTwoYear();
     }

  //OTD
       elseif($request->type == 'load-otd-ld-thihariya'){

          return $this->PlantWiseOtdThihariya();
      }
      elseif($request->type == 'load-otd-ld-kelaniya'){

         return $this->PlantWiseOtdKelaniya();
     }
     elseif($request->type == 'load-otd-ld-mawathagama'){

        return $this->PlantWiseOtdMawathagama();
    }
    elseif($request->type == 'load-otd-ld-naula'){

       return $this->PlantWiseOtdNaula();
   }
   elseif($request->type == 'load-otd-ld-uhumeeya'){

      return $this->PlantWiseOtdUhumeeya();
  }
  elseif($request->type == 'load-otd-ld-narammala'){

     return $this->PlantWiseOtdNarammala();
 }

 //RM
 elseif($request->type == 'load-rm-otd-ld-thihariya'){

    return $this->PlantWiseRMThihariya();
}
elseif($request->type == 'load-rm-otd-ld-kelaniya'){

   return $this->PlantWiseRMKelaniya();
}
elseif($request->type == 'load-rm-otd-ld-mawathagama'){

  return $this->PlantWiseRMMawathagama();
}
elseif($request->type == 'load-rm-otd-ld-naula'){

 return $this->PlantWiseRMNaula();
}
elseif($request->type == 'load-rm-otd-ld-uhumeeya'){

return $this->PlantWiseRMUhumeeya();
}
elseif($request->type == 'load-rm-otd-ld-narammala'){

return $this->PlantWiseRMNarammala();
}
elseif($request->type == 'load-rm-lastyear'){
     return $this->loadRmDataLastYear();
}
elseif($request->type == 'load-rm-last-twoyear'){
     return $this->loadRmDataLastTwoYear();
}


       elseif($request->type == 'load-RM'){

           return $this->loadRM();
       }elseif($request->type == 'load-RM-Data'){

           return $this->loadRmData();
       }elseif($request->type == 'load-Otd-Plant'){

           return response([
               'cus_data' => $this->PlantWiseOtd()
           ]);
       } elseif($request->type == 'load-inentorys'){

          return $this->loadInventoryval();
      }elseif($request->type == 'load-inv-Plant'){

          return response([
              'cus_data' => $this->InventoryPlantWise()
          ]);
      }
      elseif($request->type == 'load-inv-Plant-lastyear'){

          return response([
              'cus_data' => $this->InventoryPlantWiseLastYear()
          ]);
      }elseif($request->type == 'load-inv-Plant-lasttwoyear'){

          return response([
              'cus_data' => $this->InventoryPlantWiseLastTwoYear()
          ]);
      }
      //inventory plant-wise today and yesterday
      elseif($request->type == 'load-inv-Plant-thihariya'){

          return response([
              'cus_data' => $this->InventoryPlantWiseThihariya()
          ]);
      }
      elseif($request->type == 'load-inv-Plant-kelaniya'){

          return response([
              'cus_data' => $this->InventoryPlantWiseKelaniya()
          ]);
      }
      elseif($request->type == 'load-inv-Plant-mawathagama'){

          return response([
              'cus_data' => $this->InventoryPlantWiseMawathagama()
          ]);
      }
      elseif($request->type == 'load-inv-Plant-naula'){

          return response([
              'cus_data' => $this->InventoryPlantWiseNaula()
          ]);
      }
      elseif($request->type == 'load-inv-Plant-uhumeeya'){

          return response([
              'cus_data' => $this->InventoryPlantWiseUhumeeya()
          ]);
      }
      elseif($request->type == 'load-inv-Plant-narammala'){

          return response([
              'cus_data' => $this->InventoryPlantWiseNarammala()
          ]);
      }
      //load inventory Yesterday
      elseif($request->type == 'load-inv-Plant-thihariya-yesterday'){

          return response([
              'cus_data' => $this->InventoryPlantWiseThihariyaYesterday()
          ]);
      }
      elseif($request->type == 'load-inv-Plant-kelaniya-yesterday'){

          return response([
              'cus_data' => $this->InventoryPlantWiseKelaniyaYesterday()
          ]);
      }
      elseif($request->type == 'load-inv-Plant-mawathagama-yesterday'){

          return response([
              'cus_data' => $this->InventoryPlantWiseMawathagamaYesterday()
          ]);
      }
      elseif($request->type == 'load-inv-Plant-naula-yesterday'){

          return response([
              'cus_data' => $this->InventoryPlantWiseNaulaYesterday()
          ]);
      }
      elseif($request->type == 'load-inv-Plant-uhumeeya-yesterday'){

          return response([
              'cus_data' => $this->InventoryPlantWiseUhumeeyaYesterday()
          ]);
      }
      elseif($request->type == 'load-inv-Plant-narammala-yesterday'){

          return response([
              'cus_data' => $this->InventoryPlantWiseNarammalaYesterday()
          ]);
      }
      //inventorylastyear
      elseif($request->type == 'load-inv-Plant-thihariya-lastyear'){

          return response([
              'cus_data' => $this->InventoryPlantWiseThihariyaLastYear()
          ]);
      }
      elseif($request->type == 'load-inv-Plant-kelaniya-lastyear'){

          return response([
              'cus_data' => $this->InventoryPlantWiseKelaniyaLastYear()
          ]);
      }
      elseif($request->type == 'load-inv-Plant-mawathagama-lastyear'){

          return response([
              'cus_data' => $this->InventoryPlantWiseMawathagamaLastYear()
          ]);
      }
      elseif($request->type == 'load-inv-Plant-naula-lastyear'){

          return response([
              'cus_data' => $this->InventoryPlantWiseNaulaLastYear()
          ]);
      }
      elseif($request->type == 'load-inv-Plant-uhumeeya-lastyear'){

          return response([
              'cus_data' => $this->InventoryPlantWiseUhumeeyaLastYear()
          ]);
      }
      elseif($request->type == 'load-inv-Plant-narammala-lastyear'){

          return response([
              'cus_data' => $this->InventoryPlantWiseNarammalaLastYear()
          ]);
      }
      //
      elseif($request->type == 'load-Pending-cie'){

          return response([
              'cus_data' => $this->PendingCIIE()
          ]);
      }elseif($request->type == 'load-Approved-cie'){

          return response([
              'cus_data' => $this->ApprovedCIIE()
          ]);
      }elseif($request->type == 'load-Rejected-cie'){

          return response([
              'cus_data' => $this->RejectedCIIE()
          ]);
      }

    }

    public function loadItemApprovalData(){
        $itemApp = Item::select('master_id')
            ->where('status', '=', 1)
            ->whereNull('approval_status')
            ->where('created_by', '=', auth()->user()->user_id)
            ->get()
            ->toArray();

        return count($itemApp);

    }

    public function loadPendingGrnData(){
        $grn = DB::select('SELECT
                    `merc_customer_order_details`.`order_id`,
                    `store_grn_detail`.`grn_id`
                FROM
                    `merc_customer_order_details`
                INNER JOIN `store_grn_detail` ON `store_grn_detail`.`shop_order_id` = `merc_customer_order_details`.`shop_order_id`
                INNER JOIN merc_customer_order_header ON `merc_customer_order_header`.`order_id` = `merc_customer_order_details`.`order_id`
                WHERE
                    `merc_customer_order_details`.`created_by` = '.auth()->user()->user_id.'
                    AND merc_customer_order_details.rm_in_date < NOW()
                    AND merc_customer_order_header.order_status = "PLANNED"
                GROUP BY
                    `merc_customer_order_details`.`order_id`
                HAVING
                    `store_grn_detail`.`grn_id` IS NOT NULL');

        $response['grn'] = count($grn);

        $pendGrn = DB::select('SELECT
                    `merc_customer_order_details`.`order_id`
                FROM
                    `merc_customer_order_details`
                WHERE
                    `merc_customer_order_details`.`created_by` = '.auth()->user()->user_id.'
                    AND merc_customer_order_details.rm_in_date < NOW()
                GROUP BY
	                `merc_customer_order_details`.`order_id`');

        $response['non_grn'] = count($pendGrn);

        return $response;
    }

    public function loadOrderStatus(){
        //$customer['customers'] = Customer::pluck('customer_name')->toArray();
        $style = StyleCreation::select('style_creation.customer_id', 'cust_customer.customer_name','cust_customer.customer_short_name',DB::raw('COUNT(style_creation.style_id) as count'))
            ->join('cust_customer', 'cust_customer.customer_id', '=', 'style_creation.customer_id')
            ->join('ie_component_smv_header', 'style_creation.style_id', '=', 'ie_component_smv_header.style_id')
            ->where('style_creation.status', '=', 1)
            ->whereNotNull('ie_component_smv_header.total_smv')
            ->groupBy('style_creation.customer_id')
            ->get()
            ->toArray();

        $pending = StyleCreation::select('style_creation.customer_id', 'cust_customer.customer_name','cust_customer.customer_short_name',DB::raw('COUNT(*) as count'))
            ->join('cust_customer', 'cust_customer.customer_id', '=', 'style_creation.customer_id')
            ->leftjoin('ie_component_smv_header', 'style_creation.style_id', '=', 'ie_component_smv_header.style_id')
            ->where('style_creation.status', '=', 1)
            ->whereNull('ie_component_smv_header.total_smv')
            ->groupBy('style_creation.customer_id')
            ->groupBy('ie_component_smv_header.style_id')
            ->get()
            ->toArray();

        return $pending;
    }

    public function loadOrderStatusStyle(){
      $style = StyleCreation::select('style_creation.customer_id', 'cust_customer.customer_name','cust_customer.customer_short_name',DB::raw('COUNT(style_creation.style_id) as count'))
          ->join('cust_customer', 'cust_customer.customer_id', '=', 'style_creation.customer_id')
          ->join('ie_component_smv_header', 'style_creation.style_id', '=', 'ie_component_smv_header.style_id')
          ->where('style_creation.status', '=', 1)
          ->whereNotNull('ie_component_smv_header.total_smv')
          ->groupBy('style_creation.customer_id')
          ->get()
          ->toArray();

          return $style;

    }

    public function loadPendingSmvData($custId){
        $custDivisions = Customer::select('cust_customer.customer_code', 'cust_division.division_description')
            ->join('cust_division', 'cust_division.customer_code', '=', 'cust_customer.customer_code')
            ->where('cust_customer.customer_id', '=', $custId)
            ->where('cust_division.status', '=', 1)
            ->groupBy('cust_customer.customer_id')
            ->get()
            ->toArray();


        /*$custDivisions = Division::select('cust_division.division_description', 'cust_division.division_id', 'cust_division.customer_code')
            ->join('cust_customer','cust_division.customer_code', '=', 'cust_division.customer_code' )
            ->where('cust_customer.customer_id', '=', $custId)
            ->where('cust_division.status', '=', 1)
            //->get()
            ->toSql();*/

        //dd($custDivisions);
        $output = array();

        foreach($custDivisions as $division ){

            $upCount = StyleCreation::select('ie_component_smv_header.style_id', "cust_division.division_description")
                ->join('ie_component_smv_header','ie_component_smv_header.style_id', '=', 'style_creation.style_id' )
                ->join('cust_division','cust_division.division_id', '=', 'style_creation.division_id' )
                ->where('style_creation.status', '=', 1)
                ->where('style_creation.customer_id', '=', $custId)
                ->where('cust_division.customer_code', '=', $division['customer_code'])
                ->groupBy('style_creation.division_id')
                ->groupBy('ie_component_smv_header.style_id')
                ->get();

            $output['updated'][$division['division_description']]= count($upCount);


            $pendCount = DB::select('SELECT
                                    `style_creation`.style_id,
                                    `cust_division`.`division_description`,
                                    ie_component_smv_header.total_smv
                                FROM
                                    `style_creation`
                                LEFT JOIN `ie_component_smv_header` ON `ie_component_smv_header`.`style_id` = `style_creation`.`style_id`
                                INNER JOIN `cust_division` ON `cust_division`.`division_id` = `style_creation`.`division_id`
                                WHERE
                                    `style_creation`.`status` = 1
                                AND `style_creation`.`customer_id` = '.$custId.'
                                AND  `cust_division`.`customer_code` = "'.$division['customer_code'].'"
                                GROUP BY
                                    `style_creation`.`division_id`
                                HAVING total_smv IS NULL');

            $output['pending'][$division['division_description']]= count($pendCount);;

        }
        $output['divisions'] = $custDivisions;

        return $output;
    }

    public function loadPoApprovalData(){
        $approvalData['pending'] = PoOrderHeader::select(DB::raw("count(merc_po_order_header.po_id) as pending"))
            ->where('merc_po_order_header.created_by', '=', auth()->user()->user_id)
            ->where('merc_po_order_header.po_status', '=', 'PLANNED')
            ->get()
            ->toArray();



        $approvalData['approved'] = PoOrderHeader::select(DB::raw("count(merc_po_order_header.po_id) as approved"))
            ->where('merc_po_order_header.created_by', '=', auth()->user()->user_id)
            //->where('merc_po_order_header.created_by', '=', '27')
            ->where('merc_po_order_header.po_status', '=', 'CONFIRMED')
            ->get()
            ->toArray();

        $approvalData['rejected'] = PoOrderHeader::select(DB::raw("count(merc_po_order_header.po_id) as rejected"))
            ->where('merc_po_order_header.created_by', '=', auth()->user()->user_id)//auth()->user()->user_id
            ->where('merc_po_order_header.po_status', '=', 'REJECTED')
            ->get()
            ->toArray();

        return $approvalData;
    }

    public function loadEditModeDetails(){
        $editabaledata['po'] = PoOrderHeader::select(DB::raw("count(merc_po_order_header.po_id) as poCount"))
            ->where('merc_po_order_header.created_by', '=', auth()->user()->user_id)
            ->get()
            ->toArray();

        $editabaledata['costing'] = Costing::select(DB::raw("count(id) as costingCount"))
            ->where('costing.created_by', '=', auth()->user()->user_id)
            ->get()
            ->toArray();

       return $editabaledata;
    }

    public function loadPendingCostingDetails(){
        //$customer['users'] = Customer::pluck('customer_name')->toArray();

        $pendCosting['costing'] = ProcessApproval::select('app_process_approval.status',DB::raw("count(app_process_approval.id) as count"))
            ->join('usr_profile', 'usr_profile.user_id', '=', 'app_process_approval.document_created_by')
            ->where('app_process_approval.document_created_by', '=', auth()->user()->user_id)
            ->where('app_process_approval.process', '=', 'COSTING' )
            ->groupBy('app_process_approval.status')
            ->get()
            ->toArray();

        return $pendCosting;
    }

    public function loadCustomerOrderDataValue(){

        $customer = CustomerOrder::select(DB::raw("FORMAT(ROUND(SUM(merc_customer_order_details.fob*merc_customer_order_details.order_qty), 2),2) as total"))
            ->join('cust_customer', 'cust_customer.customer_id', '=', 'merc_customer_order_header.order_customer')
            ->join('merc_customer_order_details', 'merc_customer_order_details.order_id', '=', 'merc_customer_order_header.order_id')
            ->first();

        return $customer;
    }

    public function loadCustomerOrderData(){
        //$customer = Customer::select('customer_name')->get()->toArray();
        $customer['customers'] = Customer::pluck('customer_name')->toArray();
        $customer['customers'] = CustomerOrder::select('cust_customer.customer_name', 'cust_customer.customer_id','customer_short_name',DB::raw("ROUND(merc_customer_order_details.fob*merc_customer_order_details.order_qty, 2) as total"))
            ->join('cust_customer', 'cust_customer.customer_id', '=', 'merc_customer_order_header.order_customer')
            ->join('merc_customer_order_details', 'merc_customer_order_details.order_id', '=', 'merc_customer_order_header.order_id')
            ->groupBy('cust_customer.customer_name')
            ->get()
            ->toArray();

        return $customer;
    }



    public function loadCustomerOrderDetails($customer){
        $so['divisions'] = Division::pluck('division_description')->toArray();

        $so['div_data'] = CustomerOrder::select('cust_division.division_description', 'cust_division.division_id',DB::raw("ROUND(merc_customer_order_details.fob*merc_customer_order_details.order_qty, 2) as total"))
            ->join('cust_customer', 'cust_customer.customer_id', '=', 'merc_customer_order_header.order_customer')
            ->join('cust_division', 'cust_customer.customer_code', '=', 'cust_division.customer_code')
            ->join('merc_customer_order_details', 'merc_customer_order_details.order_id', '=', 'merc_customer_order_header.order_id')
            ->where('cust_customer.customer_id', '=', $customer)
            ->groupBy('cust_customer.customer_name')
            ->get()
            ->toArray();

        return $so;
    }

    public function loadQuarterWise(){

      $from = date('Y-01-01');
      $to = date('Y-m-d');
       // $pendCount = DB::select("SELECT ROUND (merc_customer_order_details.planned_qty*merc_customer_order_details.fob,2) AS value,
       //            DATE_FORMAT( planned_delivery_date,  '%Y-%M-%d' ) AS DATE,
       //            CASE WHEN quarter(planned_delivery_date)=1
       //            THEN CONCAT('Q',QUARTER(planned_delivery_date)+3)
       //            ELSE CONCAT('Q',QUARTER(planned_delivery_date)-1)
       //            END AS qt_year
       //            FROM merc_customer_order_details
       //            WHERE DATE(planned_delivery_date) BETWEEN '2020-01-01' AND '2020-03-31'
       //            GROUP BY
       //            qt_year");
       //
       //            return(json_encode(array('data'=>$pendCount)));

       $pendCount = DB::select("SELECT SUM(ROUND (merc_customer_order_details.planned_qty*merc_customer_order_details.fob,2)) AS value,
                  DATE_FORMAT( planned_delivery_date,  '%Y-%M-%d' ) AS DATE,
                  CASE WHEN quarter(planned_delivery_date)=1
                  THEN CONCAT('Q',QUARTER(planned_delivery_date)+3)
                  ELSE CONCAT('Q',QUARTER(planned_delivery_date)-1)
                  END AS qt_year
                  FROM merc_customer_order_details
                  WHERE DATE(planned_delivery_date) BETWEEN (DATE_FORMAT(CURRENT_DATE(),'%Y-01-01')) AND (DATE_FORMAT(CURRENT_DATE(),'%Y-%m-%d'))
                  GROUP BY
                  qt_year");

                  return(json_encode(array('data'=>$pendCount)));

    }

    public function loadQuarterLastYear(){

       $pendCount = DB::select("SELECT SUM(ROUND (merc_customer_order_details.planned_qty*merc_customer_order_details.fob,2)) AS value,
                  DATE_FORMAT( planned_delivery_date,  '%Y-%M-%d' ) AS DATE,
                  CASE WHEN quarter(planned_delivery_date)=1
                  THEN CONCAT('Q',QUARTER(planned_delivery_date)+3)
                  ELSE CONCAT('Q',QUARTER(planned_delivery_date)-1)
                  END AS qt_year
                  FROM merc_customer_order_details
                  WHERE DATE(planned_delivery_date) BETWEEN (DATE_FORMAT(CURRENT_DATE(),'%Y-01-01')-INTERVAL 1 YEAR) AND (DATE_FORMAT(CURRENT_DATE(),'%Y-12-31')-INTERVAL 1 YEAR)
                  GROUP BY
                  qt_year");

                  return(json_encode(array('data'=>$pendCount)));

    }
    public function loadQuarterTwoLastYear(){



       $pendCount = DB::select("SELECT SUM(ROUND (merc_customer_order_details.planned_qty*merc_customer_order_details.fob,2)) AS value,
                  DATE_FORMAT( planned_delivery_date,  '%Y-%M-%d' ) AS DATE,
                  CASE WHEN quarter(planned_delivery_date)=1
                  THEN CONCAT('Q',QUARTER(planned_delivery_date)+3)
                  ELSE CONCAT('Q',QUARTER(planned_delivery_date)-1)
                  END AS qt_year
                  FROM merc_customer_order_details
                  WHERE DATE(planned_delivery_date) BETWEEN (DATE_FORMAT(CURRENT_DATE(),'%Y-01-01')-INTERVAL 2 YEAR) AND (DATE_FORMAT(CURRENT_DATE(),'%Y-12-31')-INTERVAL 2 YEAR)
                  GROUP BY
                  qt_year");

                  return(json_encode(array('data'=>$pendCount)));

    }

    public function loadQuaterMonthWise (){
        $monthly = DB::select  ("SELECT CONCAT( YEAR( planned_delivery_date ) , '-', MONTHNAME( planned_delivery_date )) AS ym, ROUND (merc_customer_order_details.planned_qty*merc_customer_order_details.fob,2) as value
              FROM merc_customer_order_details
              WHERE planned_delivery_date BETWEEN '2019-04-01' AND CURRENT_DATE()
              GROUP BY MONTH(planned_delivery_date) ASC ");

              return(json_encode(array('data'=>$monthly)));

    }

    public function loadQ1 (){
        $monthly = DB::select  ("SELECT CONCAT( YEAR( planned_delivery_date ) , '-', MONTHNAME( planned_delivery_date )) AS ym, ROUND (merc_customer_order_details.planned_qty*merc_customer_order_details.fob,2) as value
              FROM merc_customer_order_details
              WHERE DATE(planned_delivery_date) BETWEEN (DATE_FORMAT(CURRENT_DATE(),'%Y-04-01')) AND (DATE_FORMAT(CURRENT_DATE(),'%Y-06-31'))
              GROUP BY MONTH(planned_delivery_date) ASC ");

              return(json_encode(array('data'=>$monthly)));
    }

    public function loadq1CurrentYear(){

      $q1from ='2019-04-01';
      $q1to = '2019-06-31';

      $q2from = date('2019-07-01');
      $q2to = date('2019-09-31');

      $q3from = date('2019-10-01');
      $q3to = date('2019-12-31');

      $q4from = date('2020-01-01');
      $q4to = date('2020-03-31');


      $query = DB::table('merc_customer_order_details')
      ->select(DB::raw("SUM(ROUND (merc_customer_order_details.planned_qty*merc_customer_order_details.fob,2)) as q1"))
      ->whereBetween('planned_delivery_date',[$q1from,$q1to])
      ->first();

      $query2 = DB::table('merc_customer_order_details')
      ->select(DB::raw("SUM(ROUND (merc_customer_order_details.planned_qty*merc_customer_order_details.fob,2)) as q2"))
      ->whereBetween('planned_delivery_date',[$q2from,$q2to])
      ->first();

      $query3 = DB::table('merc_customer_order_details')
      ->select(DB::raw("SUM(ROUND (merc_customer_order_details.planned_qty*merc_customer_order_details.fob,2)) as q3"))
      ->whereBetween('planned_delivery_date',[$q3from,$q3to])
      ->first();

      $query4 = DB::table('merc_customer_order_details')
      ->select(DB::raw("SUM(ROUND (merc_customer_order_details.planned_qty*merc_customer_order_details.fob,2)) as q4"))
      ->whereBetween('planned_delivery_date',[$q4from,$q4to])
      ->first();

      return array('q'=>$query,'qq'=>$query2,'qqq'=>$query3,'qqqq'=>$query4);
    }

    public function loadQ1Last (){
        $monthly = DB::select  ("SELECT CONCAT( YEAR( planned_delivery_date ) , '-', MONTHNAME( planned_delivery_date )) AS ym, ROUND (merc_customer_order_details.planned_qty*merc_customer_order_details.fob,2) as value
              FROM merc_customer_order_details
              WHERE DATE(planned_delivery_date) BETWEEN (DATE_FORMAT(CURRENT_DATE(),'%Y-04-01')-INTERVAL 1 YEAR) AND (DATE_FORMAT(CURRENT_DATE(),'%Y-04-31')-INTERVAL 1 YEAR)
              GROUP BY MONTH(planned_delivery_date) ASC ");

              return(json_encode(array('data'=>$monthly)));
    }

    public function loadQ1LastTwo (){
        $monthly = DB::select  ("SELECT CONCAT( YEAR( planned_delivery_date ) , '-', MONTHNAME( planned_delivery_date )) AS ym, ROUND (merc_customer_order_details.planned_qty*merc_customer_order_details.fob,2) as value
              FROM merc_customer_order_details
              WHERE DATE(planned_delivery_date) BETWEEN (DATE_FORMAT(CURRENT_DATE(),'%Y-04-01')-INTERVAL 2 YEAR) AND (DATE_FORMAT(CURRENT_DATE(),'%Y-06-31')-INTERVAL 2 YEAR)
              GROUP BY MONTH(planned_delivery_date) ASC ");

              return(json_encode(array('data'=>$monthly)));
    }

    public function loadQ2 (){
        $monthly = DB::select  ("SELECT CONCAT( YEAR( planned_delivery_date ) , '-', MONTHNAME( planned_delivery_date )) AS ym, ROUND (merc_customer_order_details.planned_qty*merc_customer_order_details.fob,2) as value
              FROM merc_customer_order_details
              WHERE DATE(planned_delivery_date) BETWEEN (DATE_FORMAT(CURRENT_DATE(),'%Y-07-01')) AND (DATE_FORMAT(CURRENT_DATE(),'%Y-09-31'))
              GROUP BY MONTH(planned_delivery_date) ASC ");

              return(json_encode(array('data'=>$monthly)));
    }

    public function loadQ2Last (){
        $monthly = DB::select  ("SELECT CONCAT( YEAR( planned_delivery_date ) , '-', MONTHNAME( planned_delivery_date )) AS ym, ROUND (merc_customer_order_details.planned_qty*merc_customer_order_details.fob,2) as value
              FROM merc_customer_order_details
              WHERE DATE(planned_delivery_date) BETWEEN (DATE_FORMAT(CURRENT_DATE(),'%Y-07-01')-INTERVAL 1 YEAR) AND (DATE_FORMAT(CURRENT_DATE(),'%Y-09-31')-INTERVAL 1 YEAR)
              GROUP BY MONTH(planned_delivery_date) ASC ");

              return(json_encode(array('data'=>$monthly)));
    }

    public function loadQ2LastTwo (){
        $monthly = DB::select  ("SELECT CONCAT( YEAR( planned_delivery_date ) , '-', MONTHNAME( planned_delivery_date )) AS ym, ROUND (merc_customer_order_details.planned_qty*merc_customer_order_details.fob,2) as value
              FROM merc_customer_order_details
              WHERE DATE(planned_delivery_date) BETWEEN (DATE_FORMAT(CURRENT_DATE(),'%Y-07-01')-INTERVAL 2 YEAR) AND (DATE_FORMAT(CURRENT_DATE(),'%Y-09-31')-INTERVAL 2 YEAR)
              GROUP BY MONTH(planned_delivery_date) ASC ");

              return(json_encode(array('data'=>$monthly)));
    }

    public function loadQ3 (){
        $monthly = DB::select  ("SELECT CONCAT( YEAR( planned_delivery_date ) , '-', MONTHNAME( planned_delivery_date )) AS ym, ROUND (merc_customer_order_details.planned_qty*merc_customer_order_details.fob,2) as value
              FROM merc_customer_order_details
              WHERE DATE(planned_delivery_date) BETWEEN (DATE_FORMAT(CURRENT_DATE(),'%Y-10-01')) AND (DATE_FORMAT(CURRENT_DATE(),'%Y-12-31'))
              GROUP BY MONTH(planned_delivery_date) ASC ");

              return(json_encode(array('data'=>$monthly)));
    }

    public function loadQ3Last (){
        $monthly = DB::select  ("SELECT CONCAT( YEAR( planned_delivery_date ) , '-', MONTHNAME( planned_delivery_date )) AS ym, ROUND (merc_customer_order_details.planned_qty*merc_customer_order_details.fob,2) as value
              FROM merc_customer_order_details
              WHERE DATE(planned_delivery_date) BETWEEN (DATE_FORMAT(CURRENT_DATE(),'%Y-10-01')-INTERVAL 1 YEAR) AND (DATE_FORMAT(CURRENT_DATE(),'%Y-12-31')-INTERVAL 1 YEAR)
              GROUP BY MONTH(planned_delivery_date) ASC ");

              return(json_encode(array('data'=>$monthly)));
    }

    public function loadQ3LastTwo (){
        $monthly = DB::select  ("SELECT CONCAT( YEAR( planned_delivery_date ) , '-', MONTHNAME( planned_delivery_date )) AS ym, ROUND (merc_customer_order_details.planned_qty*merc_customer_order_details.fob,2) as value
              FROM merc_customer_order_details
              WHERE DATE(planned_delivery_date) BETWEEN (DATE_FORMAT(CURRENT_DATE(),'%Y-10-01')-INTERVAL 2 YEAR) AND (DATE_FORMAT(CURRENT_DATE(),'%Y-12-31')-INTERVAL 2 YEAR)
              GROUP BY MONTH(planned_delivery_date) ASC ");

              return(json_encode(array('data'=>$monthly)));
    }

    public function loadQ4 (){
      $monthly = DB::select  ("SELECT CONCAT( YEAR( planned_delivery_date ) , '-', MONTHNAME( planned_delivery_date )) AS ym, SUM(ROUND (merc_customer_order_details.planned_qty*merc_customer_order_details.fob,2)) as value
            FROM merc_customer_order_details
            WHERE DATE(planned_delivery_date) BETWEEN (DATE_FORMAT(CURRENT_DATE(),'%Y-01-01')) AND (DATE_FORMAT(CURRENT_DATE(),'%Y-03-31'))
            GROUP BY MONTH(planned_delivery_date) ASC ");

            return(json_encode(array('data'=>$monthly)));
    }

    public function loadQ4Last (){
      $monthly = DB::select  ("SELECT CONCAT( YEAR( planned_delivery_date ) , '-', MONTHNAME( planned_delivery_date )) AS ym, ROUND (merc_customer_order_details.planned_qty*merc_customer_order_details.fob,2) as value
            FROM merc_customer_order_details
            WHERE DATE(planned_delivery_date) BETWEEN (DATE_FORMAT(CURRENT_DATE(),'%Y-01-01')-INTERVAL 1 YEAR) AND (DATE_FORMAT(CURRENT_DATE(),'%Y-03-31')-INTERVAL 2 YEAR)
            GROUP BY MONTH(planned_delivery_date) ASC ");

            return(json_encode(array('data'=>$monthly)));
    }

    public function loadQ4LastTwo (){
      $monthly = DB::select  ("SELECT CONCAT( YEAR( planned_delivery_date ) , '-', MONTHNAME( planned_delivery_date )) AS ym, ROUND (merc_customer_order_details.planned_qty*merc_customer_order_details.fob,2) as value
            FROM merc_customer_order_details
            WHERE planned_delivery_date BETWEEN (DATE_FORMAT(CURRENT_DATE(),'%Y-01-01')-INTERVAL 2 YEAR) AND (DATE_FORMAT(CURRENT_DATE(),'%Y-01-01')-INTERVAL 3 YEAR)
            GROUP BY MONTH(planned_delivery_date) ASC ");

            return(json_encode(array('data'=>$monthly)));
    }

    public function loadOTD(){

            // $query = DB::table('merc_customer_order_details')
            // ->select(DB::raw("COUNT(planned_delivery_date) AS count"))
            // ->where('planned_delivery_date','CURRENT_DATE()')
            // ->first();
            // return json_encode($query);

            // $monthly = DB::select  ("SELECT COUNT(item_master.master_code) as OTD
            //            from merc_po_order_header
            //            inner join merc_po_order_details on merc_po_order_header.po_number=merc_po_order_details.po_no
            //            inner join item_master ON merc_po_order_details.item_code=item_master.master_id
            //            INNER JOIN store_grn_detail on item_master.master_id=store_grn_detail.item_code
            //            where DATE(merc_po_order_header.delivery_date) = '2020-01-31'
            //            and store_grn_detail.created_date<merc_po_order_header.delivery_date ");

            $query = DB::table('merc_po_order_header')
            ->select('item_master.master_code',DB::raw("COUNT(item_master.master_code) as OTD"))
            ->join('merc_po_order_details', 'merc_po_order_header.po_number', '=', 'merc_po_order_details.po_no')
            ->join('item_master', 'merc_po_order_details.item_code', '=', 'item_master.master_id')
            ->join('store_grn_detail', 'item_master.master_id', '=', 'store_grn_detail.item_code')
            ->where('merc_po_order_header.delivery_date',date("Y-m-d"))
            ->where('store_grn_detail.created_date','<=','merc_po_order_header.delivery_date ')
            ->first();

            $query2 = DB::table('merc_po_order_header')
            ->select('item_master.master_code',DB::raw("COUNT(item_master.master_code) as OTD"))
            ->join('merc_po_order_details', 'merc_po_order_header.po_number', '=', 'merc_po_order_details.po_no')
            ->join('item_master', 'merc_po_order_details.item_code', '=', 'item_master.master_id')
            ->join('store_grn_detail', 'item_master.master_id', '=', 'store_grn_detail.item_code')
            ->where('merc_po_order_header.delivery_date',date("Y-m-d"))
            ->where('store_grn_detail.created_date','>=','merc_po_order_header.delivery_date ')
            ->first();

            return array('pass'=>$query,'fail'=>$query2);

    }

    public function loadOTDLastYear(){//current year

            $current_year = DB::select("SELECT YEAR(CURDATE()) as cur_year");
            $current_year=$current_year["0"];

            $from = date('Y-01-01');
            $to = date('Y-m-d',strtotime("-1 days"));

            $query = DB::table('merc_po_order_header')
            ->select('item_master.master_code',DB::raw("COUNT(item_master.master_code) as OTD"))
            ->join('merc_po_order_details', 'merc_po_order_header.po_number', '=', 'merc_po_order_details.po_no')
            ->join('item_master', 'merc_po_order_details.item_code', '=', 'item_master.master_id')
            ->join('store_grn_detail', 'item_master.master_id', '=', 'store_grn_detail.item_code')
            ->whereBetween(DB::raw('(DATE_FORMAT(merc_po_order_header.delivery_date,"%Y-%m-%d"))'),[$from,$to])
            //->where(DB::raw('(DATE_FORMAT(merc_po_order_header.delivery_date,"%Y"))'),'=',date("Y"))//YEAR(CURDATE())
            //->where('merc_po_order_header.delivery_date','2020-01-24')
            ->where('store_grn_detail.created_date','<=','merc_po_order_header.delivery_date ')
            ->first();

            $query2 = DB::table('merc_po_order_header')
            ->select('item_master.master_code',DB::raw("COUNT(item_master.master_code) as OTD"))
            ->join('merc_po_order_details', 'merc_po_order_header.po_number', '=', 'merc_po_order_details.po_no')
            ->join('item_master', 'merc_po_order_details.item_code', '=', 'item_master.master_id')
            ->join('store_grn_detail', 'item_master.master_id', '=', 'store_grn_detail.item_code')
            ->whereBetween(DB::raw('(DATE_FORMAT(merc_po_order_header.delivery_date,"%Y-%m-%d"))'),[$from,$to])
            //->where(DB::raw('(DATE_FORMAT(merc_po_order_header.delivery_date,"%Y"))'), date("Y"))//YEAR(CURDATE())
            //->where('merc_po_order_header.delivery_date','2020-01-24')
            ->where('store_grn_detail.created_date','>=','merc_po_order_header.delivery_date ')
            ->first();

            return array('pass'=>$query,'fail'=>$query2);

    }

    public function loadOTDLastTwoYear(){//lastyear

            $query = DB::table('merc_po_order_header')
            ->select('item_master.master_code',DB::raw("COUNT(item_master.master_code) as OTD"))
            ->join('merc_po_order_details', 'merc_po_order_header.po_number', '=', 'merc_po_order_details.po_no')
            ->join('item_master', 'merc_po_order_details.item_code', '=', 'item_master.master_id')
            ->join('store_grn_detail', 'item_master.master_id', '=', 'store_grn_detail.item_code')
            ->where(DB::raw('(DATE_FORMAT(merc_po_order_header.delivery_date,"%Y"))'), date("Y")-1)//YEAR(CURDATE())-1
            //->where('merc_po_order_header.delivery_date','2020-01-24')
            ->where('store_grn_detail.created_date','<=','merc_po_order_header.delivery_date ')
            ->first();

            $query2 = DB::table('merc_po_order_header')
            ->select('item_master.master_code',DB::raw("COUNT(item_master.master_code) as OTD"))
            ->join('merc_po_order_details', 'merc_po_order_header.po_number', '=', 'merc_po_order_details.po_no')
            ->join('item_master', 'merc_po_order_details.item_code', '=', 'item_master.master_id')
            ->join('store_grn_detail', 'item_master.master_id', '=', 'store_grn_detail.item_code')
            ->where(DB::raw('(DATE_FORMAT(merc_po_order_header.delivery_date,"%Y"))'),date("Y")-1)//YEAR(CURDATE())-1
            //->where('merc_po_order_header.delivery_date','2020-01-24')
            ->where('store_grn_detail.created_date','>=','merc_po_order_header.delivery_date ')
            ->first();

            return array('pass'=>$query,'fail'=>$query2);

    }

    public function PlantWiseOtd(){
    //$query2['divisions'] = Customer::pluck('org_location.loc_name')->toArray();
    $query2 = DB::table('merc_po_order_header')
      ->select('org_location.loc_name',DB::raw("COUNT(item_master.master_code) as OTD"))
      ->join('merc_po_order_details', 'merc_po_order_header.po_number', '=', 'merc_po_order_details.po_no')
      ->join('item_master', 'merc_po_order_details.item_code', '=', 'item_master.master_id')
      ->join('store_grn_detail', 'item_master.master_id', '=', 'store_grn_detail.item_code')
      ->join('org_location', 'merc_po_order_header.po_deli_loc', '=', 'org_location.loc_id')
      ->where(DB::raw('(DATE_FORMAT(merc_po_order_header.delivery_date,"%Y-%m-%d"))'),'2020-01-24' )//date("Y-m-d")
      //->where('merc_po_order_header.delivery_date','2020-01-24')
      ->where('store_grn_detail.created_date','<','merc_po_order_header.delivery_date ')
      ->groupBy('org_location.loc_name')
      ->get()
      ->toArray();
      //->first();
        return $query2;

    }

    public function PlantWiseOtdThihariya(){
    //$query2['divisions'] = Customer::pluck('org_location.loc_name')->toArray();
    $query = DB::table('merc_po_order_header')
      ->select('org_location.loc_name',DB::raw("COUNT(item_master.master_code) as OTD"))
      ->join('merc_po_order_details', 'merc_po_order_header.po_number', '=', 'merc_po_order_details.po_no')
      ->join('item_master', 'merc_po_order_details.item_code', '=', 'item_master.master_id')
      ->join('store_grn_detail', 'item_master.master_id', '=', 'store_grn_detail.item_code')
      ->join('org_location', 'merc_po_order_header.po_deli_loc', '=', 'org_location.loc_id')
      ->where('org_location.loc_id','24')
      ->where(DB::raw('(DATE_FORMAT(merc_po_order_header.delivery_date,"%Y-%m-%d"))'), date("Y-m-d"))
      //->where('merc_po_order_header.delivery_date','CURRENT_DATE()')
      ->where('store_grn_detail.created_date','<','merc_po_order_header.delivery_date ')
      ->first();

      $query2 = DB::table('merc_po_order_header')
        ->select('org_location.loc_name',DB::raw("COUNT(item_master.master_code) as OTD"))
        ->join('merc_po_order_details', 'merc_po_order_header.po_number', '=', 'merc_po_order_details.po_no')
        ->join('item_master', 'merc_po_order_details.item_code', '=', 'item_master.master_id')
        ->join('store_grn_detail', 'item_master.master_id', '=', 'store_grn_detail.item_code')
        ->join('org_location', 'merc_po_order_header.po_deli_loc', '=', 'org_location.loc_id')
        ->where('org_location.loc_id','24')
        ->where(DB::raw('(DATE_FORMAT(merc_po_order_header.delivery_date,"%Y-%m-%d"))'), date("Y-m-d"))
        //->where('merc_po_order_header.delivery_date','CURRENT_DATE()')
        ->where('store_grn_detail.created_date','>','merc_po_order_header.delivery_date ')
        ->first();

      return array('pass'=>$query,'fail'=>$query2);

    }

    public function PlantWiseOtdKelaniya(){
    //$query2['divisions'] = Customer::pluck('org_location.loc_name')->toArray();
    $query = DB::table('merc_po_order_header')
      ->select('org_location.loc_name',DB::raw("COUNT(item_master.master_code) as OTD"))
      ->join('merc_po_order_details', 'merc_po_order_header.po_number', '=', 'merc_po_order_details.po_no')
      ->join('item_master', 'merc_po_order_details.item_code', '=', 'item_master.master_id')
      ->join('store_grn_detail', 'item_master.master_id', '=', 'store_grn_detail.item_code')
      ->join('org_location', 'merc_po_order_header.po_deli_loc', '=', 'org_location.loc_id')
      ->where('org_location.loc_id','26')
      ->where(DB::raw('(DATE_FORMAT(merc_po_order_header.delivery_date,"%Y-%m-%d"))'), date("Y-m-d"))
      //->where('merc_po_order_header.delivery_date','CURRENT_DATE()')
      ->where('store_grn_detail.created_date','<','merc_po_order_header.delivery_date ')
      ->first();

      $query2 = DB::table('merc_po_order_header')
        ->select('org_location.loc_name',DB::raw("COUNT(item_master.master_code) as OTD"))
        ->join('merc_po_order_details', 'merc_po_order_header.po_number', '=', 'merc_po_order_details.po_no')
        ->join('item_master', 'merc_po_order_details.item_code', '=', 'item_master.master_id')
        ->join('store_grn_detail', 'item_master.master_id', '=', 'store_grn_detail.item_code')
        ->join('org_location', 'merc_po_order_header.po_deli_loc', '=', 'org_location.loc_id')
        ->where('org_location.loc_id','26')
        ->where(DB::raw('(DATE_FORMAT(merc_po_order_header.delivery_date,"%Y-%m-%d"))'), date("Y-m-d"))
        //->where('merc_po_order_header.delivery_date','CURRENT_DATE()')
        ->where('store_grn_detail.created_date','>','merc_po_order_header.delivery_date ')
        ->first();

      return array('pass'=>$query,'fail'=>$query2);
    }

    public function PlantWiseOtdMawathagama(){
    //$query2['divisions'] = Customer::pluck('org_location.loc_name')->toArray();
    $query = DB::table('merc_po_order_header')
      ->select('org_location.loc_name',DB::raw("COUNT(item_master.master_code) as OTD"))
      ->join('merc_po_order_details', 'merc_po_order_header.po_number', '=', 'merc_po_order_details.po_no')
      ->join('item_master', 'merc_po_order_details.item_code', '=', 'item_master.master_id')
      ->join('store_grn_detail', 'item_master.master_id', '=', 'store_grn_detail.item_code')
      ->join('org_location', 'merc_po_order_header.po_deli_loc', '=', 'org_location.loc_id')
      ->where('org_location.loc_id','26')
        ->where(DB::raw('(DATE_FORMAT(merc_po_order_header.delivery_date,"%Y-%m-%d"))'), date("Y-m-d"))
      //->where('merc_po_order_header.delivery_date','CURRENT_DATE()')
      ->where('store_grn_detail.created_date','<','merc_po_order_header.delivery_date ')
      ->first();

      $query2 = DB::table('merc_po_order_header')
        ->select('org_location.loc_name',DB::raw("COUNT(item_master.master_code) as OTD"))
        ->join('merc_po_order_details', 'merc_po_order_header.po_number', '=', 'merc_po_order_details.po_no')
        ->join('item_master', 'merc_po_order_details.item_code', '=', 'item_master.master_id')
        ->join('store_grn_detail', 'item_master.master_id', '=', 'store_grn_detail.item_code')
        ->join('org_location', 'merc_po_order_header.po_deli_loc', '=', 'org_location.loc_id')
        ->where('org_location.loc_id','26')
          ->where(DB::raw('(DATE_FORMAT(merc_po_order_header.delivery_date,"%Y-%m-%d"))'), date("Y-m-d"))
        //->where('merc_po_order_header.delivery_date','CURRENT_DATE()')
        ->where('store_grn_detail.created_date','>','merc_po_order_header.delivery_date ')
        ->first();

      return array('pass'=>$query,'fail'=>$query2);

    }

    public function PlantWiseOtdNaula(){
    //$query2['divisions'] = Customer::pluck('org_location.loc_name')->toArray();
    $query = DB::table('merc_po_order_header')
      ->select('org_location.loc_name',DB::raw("COUNT(item_master.master_code) as OTD"))
      ->join('merc_po_order_details', 'merc_po_order_header.po_number', '=', 'merc_po_order_details.po_no')
      ->join('item_master', 'merc_po_order_details.item_code', '=', 'item_master.master_id')
      ->join('store_grn_detail', 'item_master.master_id', '=', 'store_grn_detail.item_code')
      ->join('org_location', 'merc_po_order_header.po_deli_loc', '=', 'org_location.loc_id')
      ->where('org_location.loc_id','26')
      ->where(DB::raw('(DATE_FORMAT(merc_po_order_header.delivery_date,"%Y-%m-%d"))'), date("Y-m-d"))
      //->where('merc_po_order_header.delivery_date','CURRENT_DATE()')
      ->where('store_grn_detail.created_date','<','merc_po_order_header.delivery_date ')
      ->first();

      $query2 = DB::table('merc_po_order_header')
        ->select('org_location.loc_name',DB::raw("COUNT(item_master.master_code) as OTD"))
        ->join('merc_po_order_details', 'merc_po_order_header.po_number', '=', 'merc_po_order_details.po_no')
        ->join('item_master', 'merc_po_order_details.item_code', '=', 'item_master.master_id')
        ->join('store_grn_detail', 'item_master.master_id', '=', 'store_grn_detail.item_code')
        ->join('org_location', 'merc_po_order_header.po_deli_loc', '=', 'org_location.loc_id')
        ->where('org_location.loc_id','26')
        ->where(DB::raw('(DATE_FORMAT(merc_po_order_header.delivery_date,"%Y-%m-%d"))'), date("Y-m-d"))
        //->where('merc_po_order_header.delivery_date','CURRENT_DATE()')
        ->where('store_grn_detail.created_date','>','merc_po_order_header.delivery_date ')
        ->first();

      return array('pass'=>$query,'fail'=>$query2);

    }

    public function PlantWiseOtdUhumeeya(){

    //$query2['divisions'] = Customer::pluck('org_location.loc_name')->toArray();
    $query = DB::table('merc_po_order_header')
      ->select('org_location.loc_name',DB::raw("COUNT(item_master.master_code) as OTD"))
      ->join('merc_po_order_details', 'merc_po_order_header.po_number', '=', 'merc_po_order_details.po_no')
      ->join('item_master', 'merc_po_order_details.item_code', '=', 'item_master.master_id')
      ->join('store_grn_detail', 'item_master.master_id', '=', 'store_grn_detail.item_code')
      ->join('org_location', 'merc_po_order_header.po_deli_loc', '=', 'org_location.loc_id')
      ->where('org_location.loc_id','26')
      ->where(DB::raw('(DATE_FORMAT(merc_po_order_header.delivery_date,"%Y-%m-%d"))'), date("Y-m-d"))
      //->where('merc_po_order_header.delivery_date','CURRENT_DATE()')
      ->where('store_grn_detail.created_date','<','merc_po_order_header.delivery_date ')
      ->first();

      $query2 = DB::table('merc_po_order_header')
        ->select('org_location.loc_name',DB::raw("COUNT(item_master.master_code) as OTD"))
        ->join('merc_po_order_details', 'merc_po_order_header.po_number', '=', 'merc_po_order_details.po_no')
        ->join('item_master', 'merc_po_order_details.item_code', '=', 'item_master.master_id')
        ->join('store_grn_detail', 'item_master.master_id', '=', 'store_grn_detail.item_code')
        ->join('org_location', 'merc_po_order_header.po_deli_loc', '=', 'org_location.loc_id')
        ->where('org_location.loc_id','26')
        ->where(DB::raw('(DATE_FORMAT(merc_po_order_header.delivery_date,"%Y-%m-%d"))'), date("Y-m-d"))
        //->where('merc_po_order_header.delivery_date','CURRENT_DATE()')
        ->where('store_grn_detail.created_date','>','merc_po_order_header.delivery_date ')
        ->first();

      return array('pass'=>$query,'fail'=>$query2);

    }

    public function PlantWiseOtdNarammala(){

    //$query2['divisions'] = Customer::pluck('org_location.loc_name')->toArray();
    $query = DB::table('merc_po_order_header')
      ->select('org_location.loc_name',DB::raw("COUNT(item_master.master_code) as OTD"))
      ->join('merc_po_order_details', 'merc_po_order_header.po_number', '=', 'merc_po_order_details.po_no')
      ->join('item_master', 'merc_po_order_details.item_code', '=', 'item_master.master_id')
      ->join('store_grn_detail', 'item_master.master_id', '=', 'store_grn_detail.item_code')
      ->join('org_location', 'merc_po_order_header.po_deli_loc', '=', 'org_location.loc_id')
      ->where('org_location.loc_id','26')
      ->where(DB::raw('(DATE_FORMAT(merc_po_order_header.delivery_date,"%Y-%m-%d"))'), date("Y-m-d"))
      //->where('merc_po_order_header.delivery_date','CURRENT_DATE()')
      ->where('store_grn_detail.created_date','<','merc_po_order_header.delivery_date ')
      ->first();

      $query2 = DB::table('merc_po_order_header')
        ->select('org_location.loc_name',DB::raw("COUNT(item_master.master_code) as OTD"))
        ->join('merc_po_order_details', 'merc_po_order_header.po_number', '=', 'merc_po_order_details.po_no')
        ->join('item_master', 'merc_po_order_details.item_code', '=', 'item_master.master_id')
        ->join('store_grn_detail', 'item_master.master_id', '=', 'store_grn_detail.item_code')
        ->join('org_location', 'merc_po_order_header.po_deli_loc', '=', 'org_location.loc_id')
        ->where('org_location.loc_id','26')
        ->where(DB::raw('(DATE_FORMAT(merc_po_order_header.delivery_date,"%Y-%m-%d"))'), date("Y-m-d"))
        //->where('merc_po_order_header.delivery_date','CURRENT_DATE()')
        ->where('store_grn_detail.created_date','>','merc_po_order_header.delivery_date ')
        ->first();

      return array('pass'=>$query,'fail'=>$query2);

    }

    public function loadRM(){

            $query = DB::table('merc_po_order_header')
            ->select('item_master.master_code',DB::raw("COUNT(item_master.master_code) as OTD"))
            ->join('merc_po_order_details', 'merc_po_order_header.po_number', '=', 'merc_po_order_details.po_no')
            ->join('merc_shop_order_detail', 'merc_po_order_details.shop_order_detail_id', '=', 'merc_shop_order_detail.shop_order_detail_id')
            ->join('merc_shop_order_header', 'merc_shop_order_detail.shop_order_id', '=', 'merc_shop_order_header.shop_order_id')
            ->join('merc_shop_order_delivery', 'merc_shop_order_header.shop_order_id', '=', 'merc_shop_order_delivery.shop_order_id')
            ->join('merc_customer_order_details', 'merc_shop_order_delivery.delivery_id', '=', 'merc_customer_order_details.details_id')
            ->join('item_master', 'merc_po_order_details.item_code', '=', 'item_master.master_id')
            ->join('store_grn_detail', 'item_master.master_id', '=', 'store_grn_detail.item_code')
            ->where(DB::raw('(DATE_FORMAT(merc_customer_order_details.rm_in_date,"%Y-%m-%d"))'), date("Y-m-d"))
            //->where('merc_customer_order_details.rm_in_date','2020-01-24')
            ->where('merc_customer_order_details.rm_in_date','<=','merc_customer_order_details.planned_delivery_date ')
            ->first();

            $query2 = DB::table('merc_po_order_header')
            ->select('item_master.master_code',DB::raw("COUNT(item_master.master_code) as OTD"))
            ->join('merc_po_order_details', 'merc_po_order_header.po_number', '=', 'merc_po_order_details.po_no')
            ->join('merc_shop_order_detail', 'merc_po_order_details.shop_order_detail_id', '=', 'merc_shop_order_detail.shop_order_detail_id')
            ->join('merc_shop_order_header', 'merc_shop_order_detail.shop_order_id', '=', 'merc_shop_order_header.shop_order_id')
            ->join('merc_shop_order_delivery', 'merc_shop_order_header.shop_order_id', '=', 'merc_shop_order_delivery.shop_order_id')
            ->join('merc_customer_order_details', 'merc_shop_order_delivery.delivery_id', '=', 'merc_customer_order_details.details_id')
            ->join('item_master', 'merc_po_order_details.item_code', '=', 'item_master.master_id')
            ->join('store_grn_detail', 'item_master.master_id', '=', 'store_grn_detail.item_code')
            ->where(DB::raw('(DATE_FORMAT(merc_customer_order_details.rm_in_date,"%Y-%m-%d"))'), date("Y-m-d"))
            //->where('merc_customer_order_details.rm_in_date','2020-01-24')
            ->where('merc_customer_order_details.rm_in_date','>=','merc_customer_order_details.planned_delivery_date ')
            ->first();

            return array('pass'=>$query,'fail'=>$query2);

    }

    public function PlantWiseRMThihariya(){

            $query = DB::table('merc_po_order_header')
            ->select('item_master.master_code',DB::raw("COUNT(item_master.master_code) as OTD"))
            ->join('merc_po_order_details', 'merc_po_order_header.po_number', '=', 'merc_po_order_details.po_no')
            ->join('merc_shop_order_detail', 'merc_po_order_details.shop_order_detail_id', '=', 'merc_shop_order_detail.shop_order_detail_id')
            ->join('merc_shop_order_header', 'merc_shop_order_detail.shop_order_id', '=', 'merc_shop_order_header.shop_order_id')
            ->join('merc_shop_order_delivery', 'merc_shop_order_header.shop_order_id', '=', 'merc_shop_order_delivery.shop_order_id')
            ->join('merc_customer_order_details', 'merc_shop_order_delivery.delivery_id', '=', 'merc_customer_order_details.details_id')
            ->join('item_master', 'merc_po_order_details.item_code', '=', 'item_master.master_id')
            ->join('store_grn_detail', 'item_master.master_id', '=', 'store_grn_detail.item_code')
            ->join('org_location', 'merc_po_order_header.po_deli_loc', '=', 'org_location.loc_id')
            ->where('org_location.loc_id','24')
            ->where(DB::raw('(DATE_FORMAT(merc_customer_order_details.rm_in_date,"%Y-%m-%d"))'), date("Y-m-d"))
            //->where('merc_customer_order_details.rm_in_date','2020-01-24')
            ->where('merc_customer_order_details.rm_in_date','<=','merc_customer_order_details.planned_delivery_date ')
            ->first();

            $query2 = DB::table('merc_po_order_header')
            ->select('item_master.master_code',DB::raw("COUNT(item_master.master_code) as OTD"))
            ->join('merc_po_order_details', 'merc_po_order_header.po_number', '=', 'merc_po_order_details.po_no')
            ->join('merc_shop_order_detail', 'merc_po_order_details.shop_order_detail_id', '=', 'merc_shop_order_detail.shop_order_detail_id')
            ->join('merc_shop_order_header', 'merc_shop_order_detail.shop_order_id', '=', 'merc_shop_order_header.shop_order_id')
            ->join('merc_shop_order_delivery', 'merc_shop_order_header.shop_order_id', '=', 'merc_shop_order_delivery.shop_order_id')
            ->join('merc_customer_order_details', 'merc_shop_order_delivery.delivery_id', '=', 'merc_customer_order_details.details_id')
            ->join('item_master', 'merc_po_order_details.item_code', '=', 'item_master.master_id')
            ->join('store_grn_detail', 'item_master.master_id', '=', 'store_grn_detail.item_code')
            ->join('org_location', 'merc_po_order_header.po_deli_loc', '=', 'org_location.loc_id')
            ->where('org_location.loc_id','24')
            ->where(DB::raw('(DATE_FORMAT(merc_customer_order_details.rm_in_date,"%Y-%m-%d"))'), date("Y-m-d"))
            //->where('merc_customer_order_details.rm_in_date','2020-01-24')
            ->where('merc_customer_order_details.rm_in_date','>=','merc_customer_order_details.planned_delivery_date ')
            ->first();

            $from = date('Y-01-01');
            $to = date('Y-m-d',strtotime("-1 days"));
            $lastyear = date('Y',strtotime("-1 year"));

            $query3 = DB::table('merc_po_order_header')
            ->select('item_master.master_code',DB::raw("COUNT(item_master.master_code) as OTD"))
            ->join('merc_po_order_details', 'merc_po_order_header.po_number', '=', 'merc_po_order_details.po_no')
            ->join('merc_shop_order_detail', 'merc_po_order_details.shop_order_detail_id', '=', 'merc_shop_order_detail.shop_order_detail_id')
            ->join('merc_shop_order_header', 'merc_shop_order_detail.shop_order_id', '=', 'merc_shop_order_header.shop_order_id')
            ->join('merc_shop_order_delivery', 'merc_shop_order_header.shop_order_id', '=', 'merc_shop_order_delivery.shop_order_id')
            ->join('merc_customer_order_details', 'merc_shop_order_delivery.delivery_id', '=', 'merc_customer_order_details.details_id')
            ->join('item_master', 'merc_po_order_details.item_code', '=', 'item_master.master_id')
            ->join('store_grn_detail', 'item_master.master_id', '=', 'store_grn_detail.item_code')
            ->join('org_location', 'merc_po_order_header.po_deli_loc', '=', 'org_location.loc_id')
            ->where('org_location.loc_id','24')
            ->whereBetween(DB::raw('(DATE_FORMAT(merc_customer_order_details.rm_in_date,"%Y-%m-%d"))'),[$from,$to])
            //->where(DB::raw('(DATE_FORMAT(merc_customer_order_details.rm_in_date,"%Y-%m-%d"))'), date("Y-m-d"))
            //->where('merc_customer_order_details.rm_in_date','2020-01-24')
            ->where('merc_customer_order_details.rm_in_date','<=','merc_customer_order_details.planned_delivery_date ')
            ->first();

            $query4 = DB::table('merc_po_order_header')
            ->select('item_master.master_code',DB::raw("COUNT(item_master.master_code) as OTD"))
            ->join('merc_po_order_details', 'merc_po_order_header.po_number', '=', 'merc_po_order_details.po_no')
            ->join('merc_shop_order_detail', 'merc_po_order_details.shop_order_detail_id', '=', 'merc_shop_order_detail.shop_order_detail_id')
            ->join('merc_shop_order_header', 'merc_shop_order_detail.shop_order_id', '=', 'merc_shop_order_header.shop_order_id')
            ->join('merc_shop_order_delivery', 'merc_shop_order_header.shop_order_id', '=', 'merc_shop_order_delivery.shop_order_id')
            ->join('merc_customer_order_details', 'merc_shop_order_delivery.delivery_id', '=', 'merc_customer_order_details.details_id')
            ->join('item_master', 'merc_po_order_details.item_code', '=', 'item_master.master_id')
            ->join('store_grn_detail', 'item_master.master_id', '=', 'store_grn_detail.item_code')
            ->join('org_location', 'merc_po_order_header.po_deli_loc', '=', 'org_location.loc_id')
            ->where('org_location.loc_id','24')
            ->whereBetween(DB::raw('(DATE_FORMAT(merc_customer_order_details.rm_in_date,"%Y-%m-%d"))'),[$from,$to])
            //->where(DB::raw('(DATE_FORMAT(merc_customer_order_details.rm_in_date,"%Y-%m-%d"))'), date("Y-m-d"))
            //->where('merc_customer_order_details.rm_in_date','2020-01-24')
            ->where('merc_customer_order_details.rm_in_date','>=','merc_customer_order_details.planned_delivery_date ')
            ->first();

            $query5 = DB::table('merc_po_order_header')
            ->select('item_master.master_code',DB::raw("COUNT(item_master.master_code) as OTD"))
            ->join('merc_po_order_details', 'merc_po_order_header.po_number', '=', 'merc_po_order_details.po_no')
            ->join('merc_shop_order_detail', 'merc_po_order_details.shop_order_detail_id', '=', 'merc_shop_order_detail.shop_order_detail_id')
            ->join('merc_shop_order_header', 'merc_shop_order_detail.shop_order_id', '=', 'merc_shop_order_header.shop_order_id')
            ->join('merc_shop_order_delivery', 'merc_shop_order_header.shop_order_id', '=', 'merc_shop_order_delivery.shop_order_id')
            ->join('merc_customer_order_details', 'merc_shop_order_delivery.delivery_id', '=', 'merc_customer_order_details.details_id')
            ->join('item_master', 'merc_po_order_details.item_code', '=', 'item_master.master_id')
            ->join('store_grn_detail', 'item_master.master_id', '=', 'store_grn_detail.item_code')
            ->join('org_location', 'merc_po_order_header.po_deli_loc', '=', 'org_location.loc_id')
            ->where('org_location.loc_id','24')
            //->whereBetween(DB::raw('(DATE_FORMAT(merc_customer_order_details.rm_in_date,"%Y-%m-%d"))'),[$from,$to])
            ->where(DB::raw('(DATE_FORMAT(merc_customer_order_details.rm_in_date,"%Y"))'), $lastyear)
            //->where('merc_customer_order_details.rm_in_date','2020-01-24')
            ->where('merc_customer_order_details.rm_in_date','<=','merc_customer_order_details.planned_delivery_date ')
            ->first();

            $query6 = DB::table('merc_po_order_header')
            ->select('item_master.master_code',DB::raw("COUNT(item_master.master_code) as OTD"))
            ->join('merc_po_order_details', 'merc_po_order_header.po_number', '=', 'merc_po_order_details.po_no')
            ->join('merc_shop_order_detail', 'merc_po_order_details.shop_order_detail_id', '=', 'merc_shop_order_detail.shop_order_detail_id')
            ->join('merc_shop_order_header', 'merc_shop_order_detail.shop_order_id', '=', 'merc_shop_order_header.shop_order_id')
            ->join('merc_shop_order_delivery', 'merc_shop_order_header.shop_order_id', '=', 'merc_shop_order_delivery.shop_order_id')
            ->join('merc_customer_order_details', 'merc_shop_order_delivery.delivery_id', '=', 'merc_customer_order_details.details_id')
            ->join('item_master', 'merc_po_order_details.item_code', '=', 'item_master.master_id')
            ->join('store_grn_detail', 'item_master.master_id', '=', 'store_grn_detail.item_code')
            ->join('org_location', 'merc_po_order_header.po_deli_loc', '=', 'org_location.loc_id')
            ->where('org_location.loc_id','24')
            //->whereBetween(DB::raw('(DATE_FORMAT(merc_customer_order_details.rm_in_date,"%Y-%m-%d"))'),[$from,$to])
            ->where(DB::raw('(DATE_FORMAT(merc_customer_order_details.rm_in_date,"%Y"))'), $lastyear)
            //->where('merc_customer_order_details.rm_in_date','2020-01-24')
            ->where('merc_customer_order_details.rm_in_date','>=','merc_customer_order_details.planned_delivery_date ')
            ->first();

            return array('pass'=>$query,'fail'=>$query2,'currentpass'=>$query3,'currentfail'=>$query4,'lastpass'=>$query5,'lastfail'=>$query6);

    }

    public function PlantWiseRMKelaniya(){

            $query = DB::table('merc_po_order_header')
            ->select('item_master.master_code',DB::raw("COUNT(item_master.master_code) as OTD"))
            ->join('merc_po_order_details', 'merc_po_order_header.po_number', '=', 'merc_po_order_details.po_no')
            ->join('merc_shop_order_detail', 'merc_po_order_details.shop_order_detail_id', '=', 'merc_shop_order_detail.shop_order_detail_id')
            ->join('merc_shop_order_header', 'merc_shop_order_detail.shop_order_id', '=', 'merc_shop_order_header.shop_order_id')
            ->join('merc_shop_order_delivery', 'merc_shop_order_header.shop_order_id', '=', 'merc_shop_order_delivery.shop_order_id')
            ->join('merc_customer_order_details', 'merc_shop_order_delivery.delivery_id', '=', 'merc_customer_order_details.details_id')
            ->join('item_master', 'merc_po_order_details.item_code', '=', 'item_master.master_id')
            ->join('store_grn_detail', 'item_master.master_id', '=', 'store_grn_detail.item_code')
            ->join('org_location', 'merc_po_order_header.po_deli_loc', '=', 'org_location.loc_id')
            ->where('org_location.loc_id','26')
            ->where(DB::raw('(DATE_FORMAT(merc_customer_order_details.rm_in_date,"%Y-%m-%d"))'), date("Y-m-d"))
            //->where('merc_customer_order_details.rm_in_date','2020-01-24')
            ->where('merc_customer_order_details.rm_in_date','<=','merc_customer_order_details.planned_delivery_date ')
            ->first();

            $query2 = DB::table('merc_po_order_header')
            ->select('item_master.master_code',DB::raw("COUNT(item_master.master_code) as OTD"))
            ->join('merc_po_order_details', 'merc_po_order_header.po_number', '=', 'merc_po_order_details.po_no')
            ->join('merc_shop_order_detail', 'merc_po_order_details.shop_order_detail_id', '=', 'merc_shop_order_detail.shop_order_detail_id')
            ->join('merc_shop_order_header', 'merc_shop_order_detail.shop_order_id', '=', 'merc_shop_order_header.shop_order_id')
            ->join('merc_shop_order_delivery', 'merc_shop_order_header.shop_order_id', '=', 'merc_shop_order_delivery.shop_order_id')
            ->join('merc_customer_order_details', 'merc_shop_order_delivery.delivery_id', '=', 'merc_customer_order_details.details_id')
            ->join('item_master', 'merc_po_order_details.item_code', '=', 'item_master.master_id')
            ->join('store_grn_detail', 'item_master.master_id', '=', 'store_grn_detail.item_code')
            ->join('org_location', 'merc_po_order_header.po_deli_loc', '=', 'org_location.loc_id')
            ->where('org_location.loc_id','26')
            ->where(DB::raw('(DATE_FORMAT(merc_customer_order_details.rm_in_date,"%Y-%m-%d"))'), date("Y-m-d"))
            //->where('merc_customer_order_details.rm_in_date','2020-01-24')
            ->where('merc_customer_order_details.rm_in_date','>=','merc_customer_order_details.planned_delivery_date ')
            ->first();

            $from = date('Y-01-01');
            $to = date('Y-m-d',strtotime("-1 days"));
            $lastyear = date('Y',strtotime("-1 year"));

            $query3 = DB::table('merc_po_order_header')
            ->select('item_master.master_code',DB::raw("COUNT(item_master.master_code) as OTD"))
            ->join('merc_po_order_details', 'merc_po_order_header.po_number', '=', 'merc_po_order_details.po_no')
            ->join('merc_shop_order_detail', 'merc_po_order_details.shop_order_detail_id', '=', 'merc_shop_order_detail.shop_order_detail_id')
            ->join('merc_shop_order_header', 'merc_shop_order_detail.shop_order_id', '=', 'merc_shop_order_header.shop_order_id')
            ->join('merc_shop_order_delivery', 'merc_shop_order_header.shop_order_id', '=', 'merc_shop_order_delivery.shop_order_id')
            ->join('merc_customer_order_details', 'merc_shop_order_delivery.delivery_id', '=', 'merc_customer_order_details.details_id')
            ->join('item_master', 'merc_po_order_details.item_code', '=', 'item_master.master_id')
            ->join('store_grn_detail', 'item_master.master_id', '=', 'store_grn_detail.item_code')
            ->join('org_location', 'merc_po_order_header.po_deli_loc', '=', 'org_location.loc_id')
            ->where('org_location.loc_id','26')
            ->whereBetween(DB::raw('(DATE_FORMAT(merc_customer_order_details.rm_in_date,"%Y-%m-%d"))'),[$from,$to])
            //->where(DB::raw('(DATE_FORMAT(merc_customer_order_details.rm_in_date,"%Y-%m-%d"))'), date("Y-m-d"))
            //->where('merc_customer_order_details.rm_in_date','2020-01-24')
            ->where('merc_customer_order_details.rm_in_date','<=','merc_customer_order_details.planned_delivery_date ')
            ->first();

            $query4 = DB::table('merc_po_order_header')
            ->select('item_master.master_code',DB::raw("COUNT(item_master.master_code) as OTD"))
            ->join('merc_po_order_details', 'merc_po_order_header.po_number', '=', 'merc_po_order_details.po_no')
            ->join('merc_shop_order_detail', 'merc_po_order_details.shop_order_detail_id', '=', 'merc_shop_order_detail.shop_order_detail_id')
            ->join('merc_shop_order_header', 'merc_shop_order_detail.shop_order_id', '=', 'merc_shop_order_header.shop_order_id')
            ->join('merc_shop_order_delivery', 'merc_shop_order_header.shop_order_id', '=', 'merc_shop_order_delivery.shop_order_id')
            ->join('merc_customer_order_details', 'merc_shop_order_delivery.delivery_id', '=', 'merc_customer_order_details.details_id')
            ->join('item_master', 'merc_po_order_details.item_code', '=', 'item_master.master_id')
            ->join('store_grn_detail', 'item_master.master_id', '=', 'store_grn_detail.item_code')
            ->join('org_location', 'merc_po_order_header.po_deli_loc', '=', 'org_location.loc_id')
            ->where('org_location.loc_id','26')
            ->whereBetween(DB::raw('(DATE_FORMAT(merc_customer_order_details.rm_in_date,"%Y-%m-%d"))'),[$from,$to])
            //->where(DB::raw('(DATE_FORMAT(merc_customer_order_details.rm_in_date,"%Y-%m-%d"))'), date("Y-m-d"))
            //->where('merc_customer_order_details.rm_in_date','2020-01-24')
            ->where('merc_customer_order_details.rm_in_date','>=','merc_customer_order_details.planned_delivery_date ')
            ->first();

            $query5 = DB::table('merc_po_order_header')
            ->select('item_master.master_code',DB::raw("COUNT(item_master.master_code) as OTD"))
            ->join('merc_po_order_details', 'merc_po_order_header.po_number', '=', 'merc_po_order_details.po_no')
            ->join('merc_shop_order_detail', 'merc_po_order_details.shop_order_detail_id', '=', 'merc_shop_order_detail.shop_order_detail_id')
            ->join('merc_shop_order_header', 'merc_shop_order_detail.shop_order_id', '=', 'merc_shop_order_header.shop_order_id')
            ->join('merc_shop_order_delivery', 'merc_shop_order_header.shop_order_id', '=', 'merc_shop_order_delivery.shop_order_id')
            ->join('merc_customer_order_details', 'merc_shop_order_delivery.delivery_id', '=', 'merc_customer_order_details.details_id')
            ->join('item_master', 'merc_po_order_details.item_code', '=', 'item_master.master_id')
            ->join('store_grn_detail', 'item_master.master_id', '=', 'store_grn_detail.item_code')
            ->join('org_location', 'merc_po_order_header.po_deli_loc', '=', 'org_location.loc_id')
            ->where('org_location.loc_id','26')
            //->whereBetween(DB::raw('(DATE_FORMAT(merc_customer_order_details.rm_in_date,"%Y-%m-%d"))'),[$from,$to])
            ->where(DB::raw('(DATE_FORMAT(merc_customer_order_details.rm_in_date,"%Y"))'), $lastyear)
            //->where('merc_customer_order_details.rm_in_date','2020-01-24')
            ->where('merc_customer_order_details.rm_in_date','<=','merc_customer_order_details.planned_delivery_date ')
            ->first();

            $query6 = DB::table('merc_po_order_header')
            ->select('item_master.master_code',DB::raw("COUNT(item_master.master_code) as OTD"))
            ->join('merc_po_order_details', 'merc_po_order_header.po_number', '=', 'merc_po_order_details.po_no')
            ->join('merc_shop_order_detail', 'merc_po_order_details.shop_order_detail_id', '=', 'merc_shop_order_detail.shop_order_detail_id')
            ->join('merc_shop_order_header', 'merc_shop_order_detail.shop_order_id', '=', 'merc_shop_order_header.shop_order_id')
            ->join('merc_shop_order_delivery', 'merc_shop_order_header.shop_order_id', '=', 'merc_shop_order_delivery.shop_order_id')
            ->join('merc_customer_order_details', 'merc_shop_order_delivery.delivery_id', '=', 'merc_customer_order_details.details_id')
            ->join('item_master', 'merc_po_order_details.item_code', '=', 'item_master.master_id')
            ->join('store_grn_detail', 'item_master.master_id', '=', 'store_grn_detail.item_code')
            ->join('org_location', 'merc_po_order_header.po_deli_loc', '=', 'org_location.loc_id')
            ->where('org_location.loc_id','26')
            //->whereBetween(DB::raw('(DATE_FORMAT(merc_customer_order_details.rm_in_date,"%Y-%m-%d"))'),[$from,$to])
            ->where(DB::raw('(DATE_FORMAT(merc_customer_order_details.rm_in_date,"%Y"))'), $lastyear)
            //->where('merc_customer_order_details.rm_in_date','2020-01-24')
            ->where('merc_customer_order_details.rm_in_date','>=','merc_customer_order_details.planned_delivery_date ')
            ->first();

            return array('pass'=>$query,'fail'=>$query2,'currentpass'=>$query3,'currentfail'=>$query4,'lastpass'=>$query5,'lastfail'=>$query6);

    }

    public function PlantWiseRMMawathagama(){

            $query = DB::table('merc_po_order_header')
            ->select('item_master.master_code',DB::raw("COUNT(item_master.master_code) as OTD"))
            ->join('merc_po_order_details', 'merc_po_order_header.po_number', '=', 'merc_po_order_details.po_no')
            ->join('merc_shop_order_detail', 'merc_po_order_details.shop_order_detail_id', '=', 'merc_shop_order_detail.shop_order_detail_id')
            ->join('merc_shop_order_header', 'merc_shop_order_detail.shop_order_id', '=', 'merc_shop_order_header.shop_order_id')
            ->join('merc_shop_order_delivery', 'merc_shop_order_header.shop_order_id', '=', 'merc_shop_order_delivery.shop_order_id')
            ->join('merc_customer_order_details', 'merc_shop_order_delivery.delivery_id', '=', 'merc_customer_order_details.details_id')
            ->join('item_master', 'merc_po_order_details.item_code', '=', 'item_master.master_id')
            ->join('store_grn_detail', 'item_master.master_id', '=', 'store_grn_detail.item_code')
            ->join('org_location', 'merc_po_order_header.po_deli_loc', '=', 'org_location.loc_id')
            ->where('org_location.loc_id','26')
            ->where(DB::raw('(DATE_FORMAT(merc_customer_order_details.rm_in_date,"%Y-%m-%d"))'), date("Y-m-d"))
            //->where('merc_customer_order_details.rm_in_date','2020-01-24')
            ->where('merc_customer_order_details.rm_in_date','<=','merc_customer_order_details.planned_delivery_date ')
            ->first();

            $query2 = DB::table('merc_po_order_header')
            ->select('item_master.master_code',DB::raw("COUNT(item_master.master_code) as OTD"))
            ->join('merc_po_order_details', 'merc_po_order_header.po_number', '=', 'merc_po_order_details.po_no')
            ->join('merc_shop_order_detail', 'merc_po_order_details.shop_order_detail_id', '=', 'merc_shop_order_detail.shop_order_detail_id')
            ->join('merc_shop_order_header', 'merc_shop_order_detail.shop_order_id', '=', 'merc_shop_order_header.shop_order_id')
            ->join('merc_shop_order_delivery', 'merc_shop_order_header.shop_order_id', '=', 'merc_shop_order_delivery.shop_order_id')
            ->join('merc_customer_order_details', 'merc_shop_order_delivery.delivery_id', '=', 'merc_customer_order_details.details_id')
            ->join('item_master', 'merc_po_order_details.item_code', '=', 'item_master.master_id')
            ->join('store_grn_detail', 'item_master.master_id', '=', 'store_grn_detail.item_code')
            ->join('org_location', 'merc_po_order_header.po_deli_loc', '=', 'org_location.loc_id')
            ->where('org_location.loc_id','26')
            ->where(DB::raw('(DATE_FORMAT(merc_customer_order_details.rm_in_date,"%Y-%m-%d"))'), date("Y-m-d"))
            //->where('merc_customer_order_details.rm_in_date','2020-01-24')
            ->where('merc_customer_order_details.rm_in_date','>=','merc_customer_order_details.planned_delivery_date ')
            ->first();

            $from = date('Y-01-01');
            $to = date('Y-m-d',strtotime("-1 days"));
            $lastyear = date('Y',strtotime("-1 year"));

            $query3 = DB::table('merc_po_order_header')
            ->select('item_master.master_code',DB::raw("COUNT(item_master.master_code) as OTD"))
            ->join('merc_po_order_details', 'merc_po_order_header.po_number', '=', 'merc_po_order_details.po_no')
            ->join('merc_shop_order_detail', 'merc_po_order_details.shop_order_detail_id', '=', 'merc_shop_order_detail.shop_order_detail_id')
            ->join('merc_shop_order_header', 'merc_shop_order_detail.shop_order_id', '=', 'merc_shop_order_header.shop_order_id')
            ->join('merc_shop_order_delivery', 'merc_shop_order_header.shop_order_id', '=', 'merc_shop_order_delivery.shop_order_id')
            ->join('merc_customer_order_details', 'merc_shop_order_delivery.delivery_id', '=', 'merc_customer_order_details.details_id')
            ->join('item_master', 'merc_po_order_details.item_code', '=', 'item_master.master_id')
            ->join('store_grn_detail', 'item_master.master_id', '=', 'store_grn_detail.item_code')
            ->join('org_location', 'merc_po_order_header.po_deli_loc', '=', 'org_location.loc_id')
            ->where('org_location.loc_id','26')
            ->whereBetween(DB::raw('(DATE_FORMAT(merc_customer_order_details.rm_in_date,"%Y-%m-%d"))'),[$from,$to])
            //->where(DB::raw('(DATE_FORMAT(merc_customer_order_details.rm_in_date,"%Y-%m-%d"))'), date("Y-m-d"))
            //->where('merc_customer_order_details.rm_in_date','2020-01-24')
            ->where('merc_customer_order_details.rm_in_date','<=','merc_customer_order_details.planned_delivery_date ')
            ->first();

            $query4 = DB::table('merc_po_order_header')
            ->select('item_master.master_code',DB::raw("COUNT(item_master.master_code) as OTD"))
            ->join('merc_po_order_details', 'merc_po_order_header.po_number', '=', 'merc_po_order_details.po_no')
            ->join('merc_shop_order_detail', 'merc_po_order_details.shop_order_detail_id', '=', 'merc_shop_order_detail.shop_order_detail_id')
            ->join('merc_shop_order_header', 'merc_shop_order_detail.shop_order_id', '=', 'merc_shop_order_header.shop_order_id')
            ->join('merc_shop_order_delivery', 'merc_shop_order_header.shop_order_id', '=', 'merc_shop_order_delivery.shop_order_id')
            ->join('merc_customer_order_details', 'merc_shop_order_delivery.delivery_id', '=', 'merc_customer_order_details.details_id')
            ->join('item_master', 'merc_po_order_details.item_code', '=', 'item_master.master_id')
            ->join('store_grn_detail', 'item_master.master_id', '=', 'store_grn_detail.item_code')
            ->join('org_location', 'merc_po_order_header.po_deli_loc', '=', 'org_location.loc_id')
            ->where('org_location.loc_id','26')
            ->whereBetween(DB::raw('(DATE_FORMAT(merc_customer_order_details.rm_in_date,"%Y-%m-%d"))'),[$from,$to])
            //->where(DB::raw('(DATE_FORMAT(merc_customer_order_details.rm_in_date,"%Y-%m-%d"))'), date("Y-m-d"))
            //->where('merc_customer_order_details.rm_in_date','2020-01-24')
            ->where('merc_customer_order_details.rm_in_date','>=','merc_customer_order_details.planned_delivery_date ')
            ->first();

            $query5 = DB::table('merc_po_order_header')
            ->select('item_master.master_code',DB::raw("COUNT(item_master.master_code) as OTD"))
            ->join('merc_po_order_details', 'merc_po_order_header.po_number', '=', 'merc_po_order_details.po_no')
            ->join('merc_shop_order_detail', 'merc_po_order_details.shop_order_detail_id', '=', 'merc_shop_order_detail.shop_order_detail_id')
            ->join('merc_shop_order_header', 'merc_shop_order_detail.shop_order_id', '=', 'merc_shop_order_header.shop_order_id')
            ->join('merc_shop_order_delivery', 'merc_shop_order_header.shop_order_id', '=', 'merc_shop_order_delivery.shop_order_id')
            ->join('merc_customer_order_details', 'merc_shop_order_delivery.delivery_id', '=', 'merc_customer_order_details.details_id')
            ->join('item_master', 'merc_po_order_details.item_code', '=', 'item_master.master_id')
            ->join('store_grn_detail', 'item_master.master_id', '=', 'store_grn_detail.item_code')
            ->join('org_location', 'merc_po_order_header.po_deli_loc', '=', 'org_location.loc_id')
            ->where('org_location.loc_id','26')
            //->whereBetween(DB::raw('(DATE_FORMAT(merc_customer_order_details.rm_in_date,"%Y-%m-%d"))'),[$from,$to])
            ->where(DB::raw('(DATE_FORMAT(merc_customer_order_details.rm_in_date,"%Y"))'), $lastyear)
            //->where('merc_customer_order_details.rm_in_date','2020-01-24')
            ->where('merc_customer_order_details.rm_in_date','<=','merc_customer_order_details.planned_delivery_date ')
            ->first();

            $query6 = DB::table('merc_po_order_header')
            ->select('item_master.master_code',DB::raw("COUNT(item_master.master_code) as OTD"))
            ->join('merc_po_order_details', 'merc_po_order_header.po_number', '=', 'merc_po_order_details.po_no')
            ->join('merc_shop_order_detail', 'merc_po_order_details.shop_order_detail_id', '=', 'merc_shop_order_detail.shop_order_detail_id')
            ->join('merc_shop_order_header', 'merc_shop_order_detail.shop_order_id', '=', 'merc_shop_order_header.shop_order_id')
            ->join('merc_shop_order_delivery', 'merc_shop_order_header.shop_order_id', '=', 'merc_shop_order_delivery.shop_order_id')
            ->join('merc_customer_order_details', 'merc_shop_order_delivery.delivery_id', '=', 'merc_customer_order_details.details_id')
            ->join('item_master', 'merc_po_order_details.item_code', '=', 'item_master.master_id')
            ->join('store_grn_detail', 'item_master.master_id', '=', 'store_grn_detail.item_code')
            ->join('org_location', 'merc_po_order_header.po_deli_loc', '=', 'org_location.loc_id')
            ->where('org_location.loc_id','26')
            //->whereBetween(DB::raw('(DATE_FORMAT(merc_customer_order_details.rm_in_date,"%Y-%m-%d"))'),[$from,$to])
            ->where(DB::raw('(DATE_FORMAT(merc_customer_order_details.rm_in_date,"%Y"))'), $lastyear)
            //->where('merc_customer_order_details.rm_in_date','2020-01-24')
            ->where('merc_customer_order_details.rm_in_date','>=','merc_customer_order_details.planned_delivery_date ')
            ->first();

            return array('pass'=>$query,'fail'=>$query2,'currentpass'=>$query3,'currentfail'=>$query4,'lastpass'=>$query5,'lastfail'=>$query6);

    }

    public function PlantWiseRMNaula(){

            $query = DB::table('merc_po_order_header')
            ->select('item_master.master_code',DB::raw("COUNT(item_master.master_code) as OTD"))
            ->join('merc_po_order_details', 'merc_po_order_header.po_number', '=', 'merc_po_order_details.po_no')
            ->join('merc_shop_order_detail', 'merc_po_order_details.shop_order_detail_id', '=', 'merc_shop_order_detail.shop_order_detail_id')
            ->join('merc_shop_order_header', 'merc_shop_order_detail.shop_order_id', '=', 'merc_shop_order_header.shop_order_id')
            ->join('merc_shop_order_delivery', 'merc_shop_order_header.shop_order_id', '=', 'merc_shop_order_delivery.shop_order_id')
            ->join('merc_customer_order_details', 'merc_shop_order_delivery.delivery_id', '=', 'merc_customer_order_details.details_id')
            ->join('item_master', 'merc_po_order_details.item_code', '=', 'item_master.master_id')
            ->join('store_grn_detail', 'item_master.master_id', '=', 'store_grn_detail.item_code')
            ->join('org_location', 'merc_po_order_header.po_deli_loc', '=', 'org_location.loc_id')
            ->where('org_location.loc_id','26')
            ->where(DB::raw('(DATE_FORMAT(merc_customer_order_details.rm_in_date,"%Y-%m-%d"))'), date("Y-m-d"))
            //->where('merc_customer_order_details.rm_in_date','2020-01-24')
            ->where('merc_customer_order_details.rm_in_date','<=','merc_customer_order_details.planned_delivery_date ')
            ->first();

            $query2 = DB::table('merc_po_order_header')
            ->select('item_master.master_code',DB::raw("COUNT(item_master.master_code) as OTD"))
            ->join('merc_po_order_details', 'merc_po_order_header.po_number', '=', 'merc_po_order_details.po_no')
            ->join('merc_shop_order_detail', 'merc_po_order_details.shop_order_detail_id', '=', 'merc_shop_order_detail.shop_order_detail_id')
            ->join('merc_shop_order_header', 'merc_shop_order_detail.shop_order_id', '=', 'merc_shop_order_header.shop_order_id')
            ->join('merc_shop_order_delivery', 'merc_shop_order_header.shop_order_id', '=', 'merc_shop_order_delivery.shop_order_id')
            ->join('merc_customer_order_details', 'merc_shop_order_delivery.delivery_id', '=', 'merc_customer_order_details.details_id')
            ->join('item_master', 'merc_po_order_details.item_code', '=', 'item_master.master_id')
            ->join('store_grn_detail', 'item_master.master_id', '=', 'store_grn_detail.item_code')
            ->join('org_location', 'merc_po_order_header.po_deli_loc', '=', 'org_location.loc_id')
            ->where('org_location.loc_id','26')
            ->where(DB::raw('(DATE_FORMAT(merc_customer_order_details.rm_in_date,"%Y-%m-%d"))'), date("Y-m-d"))
            //->where('merc_customer_order_details.rm_in_date','2020-01-24')
            ->where('merc_customer_order_details.rm_in_date','>=','merc_customer_order_details.planned_delivery_date ')
            ->first();

            $from = date('Y-01-01');
            $to = date('Y-m-d',strtotime("-1 days"));
            $lastyear = date('Y',strtotime("-1 year"));

            $query3 = DB::table('merc_po_order_header')
            ->select('item_master.master_code',DB::raw("COUNT(item_master.master_code) as OTD"))
            ->join('merc_po_order_details', 'merc_po_order_header.po_number', '=', 'merc_po_order_details.po_no')
            ->join('merc_shop_order_detail', 'merc_po_order_details.shop_order_detail_id', '=', 'merc_shop_order_detail.shop_order_detail_id')
            ->join('merc_shop_order_header', 'merc_shop_order_detail.shop_order_id', '=', 'merc_shop_order_header.shop_order_id')
            ->join('merc_shop_order_delivery', 'merc_shop_order_header.shop_order_id', '=', 'merc_shop_order_delivery.shop_order_id')
            ->join('merc_customer_order_details', 'merc_shop_order_delivery.delivery_id', '=', 'merc_customer_order_details.details_id')
            ->join('item_master', 'merc_po_order_details.item_code', '=', 'item_master.master_id')
            ->join('store_grn_detail', 'item_master.master_id', '=', 'store_grn_detail.item_code')
            ->join('org_location', 'merc_po_order_header.po_deli_loc', '=', 'org_location.loc_id')
            ->where('org_location.loc_id','26')
            ->whereBetween(DB::raw('(DATE_FORMAT(merc_customer_order_details.rm_in_date,"%Y-%m-%d"))'),[$from,$to])
            //->where(DB::raw('(DATE_FORMAT(merc_customer_order_details.rm_in_date,"%Y-%m-%d"))'), date("Y-m-d"))
            //->where('merc_customer_order_details.rm_in_date','2020-01-24')
            ->where('merc_customer_order_details.rm_in_date','<=','merc_customer_order_details.planned_delivery_date ')
            ->first();

            $query4 = DB::table('merc_po_order_header')
            ->select('item_master.master_code',DB::raw("COUNT(item_master.master_code) as OTD"))
            ->join('merc_po_order_details', 'merc_po_order_header.po_number', '=', 'merc_po_order_details.po_no')
            ->join('merc_shop_order_detail', 'merc_po_order_details.shop_order_detail_id', '=', 'merc_shop_order_detail.shop_order_detail_id')
            ->join('merc_shop_order_header', 'merc_shop_order_detail.shop_order_id', '=', 'merc_shop_order_header.shop_order_id')
            ->join('merc_shop_order_delivery', 'merc_shop_order_header.shop_order_id', '=', 'merc_shop_order_delivery.shop_order_id')
            ->join('merc_customer_order_details', 'merc_shop_order_delivery.delivery_id', '=', 'merc_customer_order_details.details_id')
            ->join('item_master', 'merc_po_order_details.item_code', '=', 'item_master.master_id')
            ->join('store_grn_detail', 'item_master.master_id', '=', 'store_grn_detail.item_code')
            ->join('org_location', 'merc_po_order_header.po_deli_loc', '=', 'org_location.loc_id')
            ->where('org_location.loc_id','26')
            ->whereBetween(DB::raw('(DATE_FORMAT(merc_customer_order_details.rm_in_date,"%Y-%m-%d"))'),[$from,$to])
            //->where(DB::raw('(DATE_FORMAT(merc_customer_order_details.rm_in_date,"%Y-%m-%d"))'), date("Y-m-d"))
            //->where('merc_customer_order_details.rm_in_date','2020-01-24')
            ->where('merc_customer_order_details.rm_in_date','>=','merc_customer_order_details.planned_delivery_date ')
            ->first();

            $query5 = DB::table('merc_po_order_header')
            ->select('item_master.master_code',DB::raw("COUNT(item_master.master_code) as OTD"))
            ->join('merc_po_order_details', 'merc_po_order_header.po_number', '=', 'merc_po_order_details.po_no')
            ->join('merc_shop_order_detail', 'merc_po_order_details.shop_order_detail_id', '=', 'merc_shop_order_detail.shop_order_detail_id')
            ->join('merc_shop_order_header', 'merc_shop_order_detail.shop_order_id', '=', 'merc_shop_order_header.shop_order_id')
            ->join('merc_shop_order_delivery', 'merc_shop_order_header.shop_order_id', '=', 'merc_shop_order_delivery.shop_order_id')
            ->join('merc_customer_order_details', 'merc_shop_order_delivery.delivery_id', '=', 'merc_customer_order_details.details_id')
            ->join('item_master', 'merc_po_order_details.item_code', '=', 'item_master.master_id')
            ->join('store_grn_detail', 'item_master.master_id', '=', 'store_grn_detail.item_code')
            ->join('org_location', 'merc_po_order_header.po_deli_loc', '=', 'org_location.loc_id')
            ->where('org_location.loc_id','26')
            //->whereBetween(DB::raw('(DATE_FORMAT(merc_customer_order_details.rm_in_date,"%Y-%m-%d"))'),[$from,$to])
            ->where(DB::raw('(DATE_FORMAT(merc_customer_order_details.rm_in_date,"%Y"))'), $lastyear)
            //->where('merc_customer_order_details.rm_in_date','2020-01-24')
            ->where('merc_customer_order_details.rm_in_date','<=','merc_customer_order_details.planned_delivery_date ')
            ->first();

            $query6 = DB::table('merc_po_order_header')
            ->select('item_master.master_code',DB::raw("COUNT(item_master.master_code) as OTD"))
            ->join('merc_po_order_details', 'merc_po_order_header.po_number', '=', 'merc_po_order_details.po_no')
            ->join('merc_shop_order_detail', 'merc_po_order_details.shop_order_detail_id', '=', 'merc_shop_order_detail.shop_order_detail_id')
            ->join('merc_shop_order_header', 'merc_shop_order_detail.shop_order_id', '=', 'merc_shop_order_header.shop_order_id')
            ->join('merc_shop_order_delivery', 'merc_shop_order_header.shop_order_id', '=', 'merc_shop_order_delivery.shop_order_id')
            ->join('merc_customer_order_details', 'merc_shop_order_delivery.delivery_id', '=', 'merc_customer_order_details.details_id')
            ->join('item_master', 'merc_po_order_details.item_code', '=', 'item_master.master_id')
            ->join('store_grn_detail', 'item_master.master_id', '=', 'store_grn_detail.item_code')
            ->join('org_location', 'merc_po_order_header.po_deli_loc', '=', 'org_location.loc_id')
            ->where('org_location.loc_id','26')
            //->whereBetween(DB::raw('(DATE_FORMAT(merc_customer_order_details.rm_in_date,"%Y-%m-%d"))'),[$from,$to])
            ->where(DB::raw('(DATE_FORMAT(merc_customer_order_details.rm_in_date,"%Y"))'), $lastyear)
            //->where('merc_customer_order_details.rm_in_date','2020-01-24')
            ->where('merc_customer_order_details.rm_in_date','>=','merc_customer_order_details.planned_delivery_date ')
            ->first();

            return array('pass'=>$query,'fail'=>$query2,'currentpass'=>$query3,'currentfail'=>$query4,'lastpass'=>$query5,'lastfail'=>$query6);

    }

    public function PlantWiseRMUhumeeya(){

            $query = DB::table('merc_po_order_header')
            ->select('item_master.master_code',DB::raw("COUNT(item_master.master_code) as OTD"))
            ->join('merc_po_order_details', 'merc_po_order_header.po_number', '=', 'merc_po_order_details.po_no')
            ->join('merc_shop_order_detail', 'merc_po_order_details.shop_order_detail_id', '=', 'merc_shop_order_detail.shop_order_detail_id')
            ->join('merc_shop_order_header', 'merc_shop_order_detail.shop_order_id', '=', 'merc_shop_order_header.shop_order_id')
            ->join('merc_shop_order_delivery', 'merc_shop_order_header.shop_order_id', '=', 'merc_shop_order_delivery.shop_order_id')
            ->join('merc_customer_order_details', 'merc_shop_order_delivery.delivery_id', '=', 'merc_customer_order_details.details_id')
            ->join('item_master', 'merc_po_order_details.item_code', '=', 'item_master.master_id')
            ->join('store_grn_detail', 'item_master.master_id', '=', 'store_grn_detail.item_code')
            ->join('org_location', 'merc_po_order_header.po_deli_loc', '=', 'org_location.loc_id')
            ->where('org_location.loc_id','26')
            ->where(DB::raw('(DATE_FORMAT(merc_customer_order_details.rm_in_date,"%Y-%m-%d"))'), date("Y-m-d"))
            //->where('merc_customer_order_details.rm_in_date','2020-01-24')
            ->where('merc_customer_order_details.rm_in_date','<=','merc_customer_order_details.planned_delivery_date ')
            ->first();

            $query2 = DB::table('merc_po_order_header')
            ->select('item_master.master_code',DB::raw("COUNT(item_master.master_code) as OTD"))
            ->join('merc_po_order_details', 'merc_po_order_header.po_number', '=', 'merc_po_order_details.po_no')
            ->join('merc_shop_order_detail', 'merc_po_order_details.shop_order_detail_id', '=', 'merc_shop_order_detail.shop_order_detail_id')
            ->join('merc_shop_order_header', 'merc_shop_order_detail.shop_order_id', '=', 'merc_shop_order_header.shop_order_id')
            ->join('merc_shop_order_delivery', 'merc_shop_order_header.shop_order_id', '=', 'merc_shop_order_delivery.shop_order_id')
            ->join('merc_customer_order_details', 'merc_shop_order_delivery.delivery_id', '=', 'merc_customer_order_details.details_id')
            ->join('item_master', 'merc_po_order_details.item_code', '=', 'item_master.master_id')
            ->join('store_grn_detail', 'item_master.master_id', '=', 'store_grn_detail.item_code')
            ->join('org_location', 'merc_po_order_header.po_deli_loc', '=', 'org_location.loc_id')
            ->where('org_location.loc_id','26')
            ->where(DB::raw('(DATE_FORMAT(merc_customer_order_details.rm_in_date,"%Y-%m-%d"))'), date("Y-m-d"))
            //->where('merc_customer_order_details.rm_in_date','2020-01-24')
            ->where('merc_customer_order_details.rm_in_date','>=','merc_customer_order_details.planned_delivery_date ')
            ->first();

            $from = date('Y-01-01');
            $to = date('Y-m-d',strtotime("-1 days"));
            $lastyear = date('Y',strtotime("-1 year"));

            $query3 = DB::table('merc_po_order_header')
            ->select('item_master.master_code',DB::raw("COUNT(item_master.master_code) as OTD"))
            ->join('merc_po_order_details', 'merc_po_order_header.po_number', '=', 'merc_po_order_details.po_no')
            ->join('merc_shop_order_detail', 'merc_po_order_details.shop_order_detail_id', '=', 'merc_shop_order_detail.shop_order_detail_id')
            ->join('merc_shop_order_header', 'merc_shop_order_detail.shop_order_id', '=', 'merc_shop_order_header.shop_order_id')
            ->join('merc_shop_order_delivery', 'merc_shop_order_header.shop_order_id', '=', 'merc_shop_order_delivery.shop_order_id')
            ->join('merc_customer_order_details', 'merc_shop_order_delivery.delivery_id', '=', 'merc_customer_order_details.details_id')
            ->join('item_master', 'merc_po_order_details.item_code', '=', 'item_master.master_id')
            ->join('store_grn_detail', 'item_master.master_id', '=', 'store_grn_detail.item_code')
            ->join('org_location', 'merc_po_order_header.po_deli_loc', '=', 'org_location.loc_id')
            ->where('org_location.loc_id','26')
            ->whereBetween(DB::raw('(DATE_FORMAT(merc_customer_order_details.rm_in_date,"%Y-%m-%d"))'),[$from,$to])
            //->where(DB::raw('(DATE_FORMAT(merc_customer_order_details.rm_in_date,"%Y-%m-%d"))'), date("Y-m-d"))
            //->where('merc_customer_order_details.rm_in_date','2020-01-24')
            ->where('merc_customer_order_details.rm_in_date','<=','merc_customer_order_details.planned_delivery_date ')
            ->first();

            $query4 = DB::table('merc_po_order_header')
            ->select('item_master.master_code',DB::raw("COUNT(item_master.master_code) as OTD"))
            ->join('merc_po_order_details', 'merc_po_order_header.po_number', '=', 'merc_po_order_details.po_no')
            ->join('merc_shop_order_detail', 'merc_po_order_details.shop_order_detail_id', '=', 'merc_shop_order_detail.shop_order_detail_id')
            ->join('merc_shop_order_header', 'merc_shop_order_detail.shop_order_id', '=', 'merc_shop_order_header.shop_order_id')
            ->join('merc_shop_order_delivery', 'merc_shop_order_header.shop_order_id', '=', 'merc_shop_order_delivery.shop_order_id')
            ->join('merc_customer_order_details', 'merc_shop_order_delivery.delivery_id', '=', 'merc_customer_order_details.details_id')
            ->join('item_master', 'merc_po_order_details.item_code', '=', 'item_master.master_id')
            ->join('store_grn_detail', 'item_master.master_id', '=', 'store_grn_detail.item_code')
            ->join('org_location', 'merc_po_order_header.po_deli_loc', '=', 'org_location.loc_id')
            ->where('org_location.loc_id','26')
            ->whereBetween(DB::raw('(DATE_FORMAT(merc_customer_order_details.rm_in_date,"%Y-%m-%d"))'),[$from,$to])
            //->where(DB::raw('(DATE_FORMAT(merc_customer_order_details.rm_in_date,"%Y-%m-%d"))'), date("Y-m-d"))
            //->where('merc_customer_order_details.rm_in_date','2020-01-24')
            ->where('merc_customer_order_details.rm_in_date','>=','merc_customer_order_details.planned_delivery_date ')
            ->first();

            $query5 = DB::table('merc_po_order_header')
            ->select('item_master.master_code',DB::raw("COUNT(item_master.master_code) as OTD"))
            ->join('merc_po_order_details', 'merc_po_order_header.po_number', '=', 'merc_po_order_details.po_no')
            ->join('merc_shop_order_detail', 'merc_po_order_details.shop_order_detail_id', '=', 'merc_shop_order_detail.shop_order_detail_id')
            ->join('merc_shop_order_header', 'merc_shop_order_detail.shop_order_id', '=', 'merc_shop_order_header.shop_order_id')
            ->join('merc_shop_order_delivery', 'merc_shop_order_header.shop_order_id', '=', 'merc_shop_order_delivery.shop_order_id')
            ->join('merc_customer_order_details', 'merc_shop_order_delivery.delivery_id', '=', 'merc_customer_order_details.details_id')
            ->join('item_master', 'merc_po_order_details.item_code', '=', 'item_master.master_id')
            ->join('store_grn_detail', 'item_master.master_id', '=', 'store_grn_detail.item_code')
            ->join('org_location', 'merc_po_order_header.po_deli_loc', '=', 'org_location.loc_id')
            ->where('org_location.loc_id','26')
            //->whereBetween(DB::raw('(DATE_FORMAT(merc_customer_order_details.rm_in_date,"%Y-%m-%d"))'),[$from,$to])
            ->where(DB::raw('(DATE_FORMAT(merc_customer_order_details.rm_in_date,"%Y"))'), $lastyear)
            //->where('merc_customer_order_details.rm_in_date','2020-01-24')
            ->where('merc_customer_order_details.rm_in_date','<=','merc_customer_order_details.planned_delivery_date ')
            ->first();

            $query6 = DB::table('merc_po_order_header')
            ->select('item_master.master_code',DB::raw("COUNT(item_master.master_code) as OTD"))
            ->join('merc_po_order_details', 'merc_po_order_header.po_number', '=', 'merc_po_order_details.po_no')
            ->join('merc_shop_order_detail', 'merc_po_order_details.shop_order_detail_id', '=', 'merc_shop_order_detail.shop_order_detail_id')
            ->join('merc_shop_order_header', 'merc_shop_order_detail.shop_order_id', '=', 'merc_shop_order_header.shop_order_id')
            ->join('merc_shop_order_delivery', 'merc_shop_order_header.shop_order_id', '=', 'merc_shop_order_delivery.shop_order_id')
            ->join('merc_customer_order_details', 'merc_shop_order_delivery.delivery_id', '=', 'merc_customer_order_details.details_id')
            ->join('item_master', 'merc_po_order_details.item_code', '=', 'item_master.master_id')
            ->join('store_grn_detail', 'item_master.master_id', '=', 'store_grn_detail.item_code')
            ->join('org_location', 'merc_po_order_header.po_deli_loc', '=', 'org_location.loc_id')
            ->where('org_location.loc_id','26')
            //->whereBetween(DB::raw('(DATE_FORMAT(merc_customer_order_details.rm_in_date,"%Y-%m-%d"))'),[$from,$to])
            ->where(DB::raw('(DATE_FORMAT(merc_customer_order_details.rm_in_date,"%Y"))'), $lastyear)
            //->where('merc_customer_order_details.rm_in_date','2020-01-24')
            ->where('merc_customer_order_details.rm_in_date','>=','merc_customer_order_details.planned_delivery_date ')
            ->first();

            return array('pass'=>$query,'fail'=>$query2,'currentpass'=>$query3,'currentfail'=>$query4,'lastpass'=>$query5,'lastfail'=>$query6);

    }

    public function PlantWiseRMNarammala(){

          $query = DB::table('merc_po_order_header')
            ->select('item_master.master_code',DB::raw("COUNT(item_master.master_code) as OTD"))
            ->join('merc_po_order_details', 'merc_po_order_header.po_number', '=', 'merc_po_order_details.po_no')
            ->join('merc_shop_order_detail', 'merc_po_order_details.shop_order_detail_id', '=', 'merc_shop_order_detail.shop_order_detail_id')
            ->join('merc_shop_order_header', 'merc_shop_order_detail.shop_order_id', '=', 'merc_shop_order_header.shop_order_id')
            ->join('merc_shop_order_delivery', 'merc_shop_order_header.shop_order_id', '=', 'merc_shop_order_delivery.shop_order_id')
            ->join('merc_customer_order_details', 'merc_shop_order_delivery.delivery_id', '=', 'merc_customer_order_details.details_id')
            ->join('item_master', 'merc_po_order_details.item_code', '=', 'item_master.master_id')
            ->join('store_grn_detail', 'item_master.master_id', '=', 'store_grn_detail.item_code')
            ->join('org_location', 'merc_po_order_header.po_deli_loc', '=', 'org_location.loc_id')
            ->where('org_location.loc_id','26')
            ->where(DB::raw('(DATE_FORMAT(merc_customer_order_details.rm_in_date,"%Y-%m-%d"))'), date("Y-m-d"))
            //->where('merc_customer_order_details.rm_in_date','2020-01-24')
            ->where('merc_customer_order_details.rm_in_date','<=','merc_customer_order_details.planned_delivery_date ')
            ->first();

            $query2 = DB::table('merc_po_order_header')
            ->select('item_master.master_code',DB::raw("COUNT(item_master.master_code) as OTD"))
            ->join('merc_po_order_details', 'merc_po_order_header.po_number', '=', 'merc_po_order_details.po_no')
            ->join('merc_shop_order_detail', 'merc_po_order_details.shop_order_detail_id', '=', 'merc_shop_order_detail.shop_order_detail_id')
            ->join('merc_shop_order_header', 'merc_shop_order_detail.shop_order_id', '=', 'merc_shop_order_header.shop_order_id')
            ->join('merc_shop_order_delivery', 'merc_shop_order_header.shop_order_id', '=', 'merc_shop_order_delivery.shop_order_id')
            ->join('merc_customer_order_details', 'merc_shop_order_delivery.delivery_id', '=', 'merc_customer_order_details.details_id')
            ->join('item_master', 'merc_po_order_details.item_code', '=', 'item_master.master_id')
            ->join('store_grn_detail', 'item_master.master_id', '=', 'store_grn_detail.item_code')
            ->join('org_location', 'merc_po_order_header.po_deli_loc', '=', 'org_location.loc_id')
            ->where('org_location.loc_id','26')
            ->where(DB::raw('(DATE_FORMAT(merc_customer_order_details.rm_in_date,"%Y-%m-%d"))'), date("Y-m-d"))
            //->where('merc_customer_order_details.rm_in_date','2020-01-24')
            ->where('merc_customer_order_details.rm_in_date','>=','merc_customer_order_details.planned_delivery_date ')
            ->first();

            $from = date('Y-01-01');
            $to = date('Y-m-d',strtotime("-1 days"));
            $lastyear = date('Y',strtotime("-1 year"));

            $query3 = DB::table('merc_po_order_header')
            ->select('item_master.master_code',DB::raw("COUNT(item_master.master_code) as OTD"))
            ->join('merc_po_order_details', 'merc_po_order_header.po_number', '=', 'merc_po_order_details.po_no')
            ->join('merc_shop_order_detail', 'merc_po_order_details.shop_order_detail_id', '=', 'merc_shop_order_detail.shop_order_detail_id')
            ->join('merc_shop_order_header', 'merc_shop_order_detail.shop_order_id', '=', 'merc_shop_order_header.shop_order_id')
            ->join('merc_shop_order_delivery', 'merc_shop_order_header.shop_order_id', '=', 'merc_shop_order_delivery.shop_order_id')
            ->join('merc_customer_order_details', 'merc_shop_order_delivery.delivery_id', '=', 'merc_customer_order_details.details_id')
            ->join('item_master', 'merc_po_order_details.item_code', '=', 'item_master.master_id')
            ->join('store_grn_detail', 'item_master.master_id', '=', 'store_grn_detail.item_code')
            ->join('org_location', 'merc_po_order_header.po_deli_loc', '=', 'org_location.loc_id')
            ->where('org_location.loc_id','26')
            ->whereBetween(DB::raw('(DATE_FORMAT(merc_customer_order_details.rm_in_date,"%Y-%m-%d"))'),[$from,$to])
            //->where(DB::raw('(DATE_FORMAT(merc_customer_order_details.rm_in_date,"%Y-%m-%d"))'), date("Y-m-d"))
            //->where('merc_customer_order_details.rm_in_date','2020-01-24')
            ->where('merc_customer_order_details.rm_in_date','<=','merc_customer_order_details.planned_delivery_date ')
            ->first();

            $query4 = DB::table('merc_po_order_header')
            ->select('item_master.master_code',DB::raw("COUNT(item_master.master_code) as OTD"))
            ->join('merc_po_order_details', 'merc_po_order_header.po_number', '=', 'merc_po_order_details.po_no')
            ->join('merc_shop_order_detail', 'merc_po_order_details.shop_order_detail_id', '=', 'merc_shop_order_detail.shop_order_detail_id')
            ->join('merc_shop_order_header', 'merc_shop_order_detail.shop_order_id', '=', 'merc_shop_order_header.shop_order_id')
            ->join('merc_shop_order_delivery', 'merc_shop_order_header.shop_order_id', '=', 'merc_shop_order_delivery.shop_order_id')
            ->join('merc_customer_order_details', 'merc_shop_order_delivery.delivery_id', '=', 'merc_customer_order_details.details_id')
            ->join('item_master', 'merc_po_order_details.item_code', '=', 'item_master.master_id')
            ->join('store_grn_detail', 'item_master.master_id', '=', 'store_grn_detail.item_code')
            ->join('org_location', 'merc_po_order_header.po_deli_loc', '=', 'org_location.loc_id')
            ->where('org_location.loc_id','26')
            ->whereBetween(DB::raw('(DATE_FORMAT(merc_customer_order_details.rm_in_date,"%Y-%m-%d"))'),[$from,$to])
            //->where(DB::raw('(DATE_FORMAT(merc_customer_order_details.rm_in_date,"%Y-%m-%d"))'), date("Y-m-d"))
            //->where('merc_customer_order_details.rm_in_date','2020-01-24')
            ->where('merc_customer_order_details.rm_in_date','>=','merc_customer_order_details.planned_delivery_date ')
            ->first();

            $query5 = DB::table('merc_po_order_header')
            ->select('item_master.master_code',DB::raw("COUNT(item_master.master_code) as OTD"))
            ->join('merc_po_order_details', 'merc_po_order_header.po_number', '=', 'merc_po_order_details.po_no')
            ->join('merc_shop_order_detail', 'merc_po_order_details.shop_order_detail_id', '=', 'merc_shop_order_detail.shop_order_detail_id')
            ->join('merc_shop_order_header', 'merc_shop_order_detail.shop_order_id', '=', 'merc_shop_order_header.shop_order_id')
            ->join('merc_shop_order_delivery', 'merc_shop_order_header.shop_order_id', '=', 'merc_shop_order_delivery.shop_order_id')
            ->join('merc_customer_order_details', 'merc_shop_order_delivery.delivery_id', '=', 'merc_customer_order_details.details_id')
            ->join('item_master', 'merc_po_order_details.item_code', '=', 'item_master.master_id')
            ->join('store_grn_detail', 'item_master.master_id', '=', 'store_grn_detail.item_code')
            ->join('org_location', 'merc_po_order_header.po_deli_loc', '=', 'org_location.loc_id')
            ->where('org_location.loc_id','26')
            //->whereBetween(DB::raw('(DATE_FORMAT(merc_customer_order_details.rm_in_date,"%Y-%m-%d"))'),[$from,$to])
            ->where(DB::raw('(DATE_FORMAT(merc_customer_order_details.rm_in_date,"%Y"))'), $lastyear)
            //->where('merc_customer_order_details.rm_in_date','2020-01-24')
            ->where('merc_customer_order_details.rm_in_date','<=','merc_customer_order_details.planned_delivery_date ')
            ->first();

            $query6 = DB::table('merc_po_order_header')
            ->select('item_master.master_code',DB::raw("COUNT(item_master.master_code) as OTD"))
            ->join('merc_po_order_details', 'merc_po_order_header.po_number', '=', 'merc_po_order_details.po_no')
            ->join('merc_shop_order_detail', 'merc_po_order_details.shop_order_detail_id', '=', 'merc_shop_order_detail.shop_order_detail_id')
            ->join('merc_shop_order_header', 'merc_shop_order_detail.shop_order_id', '=', 'merc_shop_order_header.shop_order_id')
            ->join('merc_shop_order_delivery', 'merc_shop_order_header.shop_order_id', '=', 'merc_shop_order_delivery.shop_order_id')
            ->join('merc_customer_order_details', 'merc_shop_order_delivery.delivery_id', '=', 'merc_customer_order_details.details_id')
            ->join('item_master', 'merc_po_order_details.item_code', '=', 'item_master.master_id')
            ->join('store_grn_detail', 'item_master.master_id', '=', 'store_grn_detail.item_code')
            ->join('org_location', 'merc_po_order_header.po_deli_loc', '=', 'org_location.loc_id')
            ->where('org_location.loc_id','26')
            //->whereBetween(DB::raw('(DATE_FORMAT(merc_customer_order_details.rm_in_date,"%Y-%m-%d"))'),[$from,$to])
            ->where(DB::raw('(DATE_FORMAT(merc_customer_order_details.rm_in_date,"%Y"))'), $lastyear)
            //->where('merc_customer_order_details.rm_in_date','2020-01-24')
            ->where('merc_customer_order_details.rm_in_date','>=','merc_customer_order_details.planned_delivery_date ')
            ->first();

            return array('pass'=>$query,'fail'=>$query2,'currentpass'=>$query3,'currentfail'=>$query4,'lastpass'=>$query5,'lastfail'=>$query6);

    }

    public function loadRmData(){

      $query = DB::table('merc_po_order_header')
      ->select('item_master.master_code',DB::raw("COUNT(item_master.master_code) as OTD"))
      ->join('merc_po_order_details', 'merc_po_order_header.po_number', '=', 'merc_po_order_details.po_no')
      ->join('merc_shop_order_detail', 'merc_po_order_details.shop_order_detail_id', '=', 'merc_shop_order_detail.shop_order_detail_id')
      ->join('merc_shop_order_header', 'merc_shop_order_detail.shop_order_id', '=', 'merc_shop_order_header.shop_order_id')
      ->join('merc_shop_order_delivery', 'merc_shop_order_header.shop_order_id', '=', 'merc_shop_order_delivery.shop_order_id')
      ->join('merc_customer_order_details', 'merc_shop_order_delivery.delivery_id', '=', 'merc_customer_order_details.details_id')
      ->join('item_master', 'merc_po_order_details.item_code', '=', 'item_master.master_id')
      ->join('store_grn_detail', 'item_master.master_id', '=', 'store_grn_detail.item_code')
      ->where(DB::raw('(DATE_FORMAT(merc_customer_order_details.rm_in_date,"%Y-%m-%d"))'), date("Y-m-d"))
      //->where('merc_customer_order_details.rm_in_date','2020-01-24')
      ->where('merc_customer_order_details.rm_in_date','<=','merc_customer_order_details.planned_delivery_date ')
      ->first();

      $query2 = DB::table('merc_po_order_header')
      ->select('item_master.master_code',DB::raw("COUNT(item_master.master_code) as OTD"))
      ->join('merc_po_order_details', 'merc_po_order_header.po_number', '=', 'merc_po_order_details.po_no')
      ->join('merc_shop_order_detail', 'merc_po_order_details.shop_order_detail_id', '=', 'merc_shop_order_detail.shop_order_detail_id')
      ->join('merc_shop_order_header', 'merc_shop_order_detail.shop_order_id', '=', 'merc_shop_order_header.shop_order_id')
      ->join('merc_shop_order_delivery', 'merc_shop_order_header.shop_order_id', '=', 'merc_shop_order_delivery.shop_order_id')
      ->join('merc_customer_order_details', 'merc_shop_order_delivery.delivery_id', '=', 'merc_customer_order_details.details_id')
      ->join('item_master', 'merc_po_order_details.item_code', '=', 'item_master.master_id')
      ->join('store_grn_detail', 'item_master.master_id', '=', 'store_grn_detail.item_code')
      ->where(DB::raw('(DATE_FORMAT(merc_customer_order_details.rm_in_date,"%Y-%m-%d"))'), date("Y-m-d"))
      //->where('merc_customer_order_details.rm_in_date','2020-01-24')
      ->where('merc_customer_order_details.rm_in_date','>=','merc_customer_order_details.planned_delivery_date ')
      ->first();

      return array('pass'=>$query,'fail'=>$query2);

    }

    public function loadRmDataLastYear(){

      $from =  date('Y-01-01');
      $to = date('Y-m-d',strtotime("-1 days"));

      $query = DB::table('merc_po_order_header')
      ->select('item_master.master_code',DB::raw("COUNT(item_master.master_code) as OTD"))
      ->join('merc_po_order_details', 'merc_po_order_header.po_number', '=', 'merc_po_order_details.po_no')
      ->join('merc_shop_order_detail', 'merc_po_order_details.shop_order_detail_id', '=', 'merc_shop_order_detail.shop_order_detail_id')
      ->join('merc_shop_order_header', 'merc_shop_order_detail.shop_order_id', '=', 'merc_shop_order_header.shop_order_id')
      ->join('merc_shop_order_delivery', 'merc_shop_order_header.shop_order_id', '=', 'merc_shop_order_delivery.shop_order_id')
      ->join('merc_customer_order_details', 'merc_shop_order_delivery.delivery_id', '=', 'merc_customer_order_details.details_id')
      ->join('item_master', 'merc_po_order_details.item_code', '=', 'item_master.master_id')
      ->join('store_grn_detail', 'item_master.master_id', '=', 'store_grn_detail.item_code')
      //->where(DB::raw('(DATE_FORMAT(merc_customer_order_details.rm_in_date,"%Y-%m-%d"))'), date("Y-m-d"))
      ->whereBetween(DB::raw('(DATE_FORMAT(merc_customer_order_details.rm_in_date,"%Y-%m-%d"))'),[$from,$to])
      ->where('merc_customer_order_details.rm_in_date','<=','merc_customer_order_details.planned_delivery_date ')
      ->first();

      $query2 = DB::table('merc_po_order_header')
      ->select('item_master.master_code',DB::raw("COUNT(item_master.master_code) as OTD"))
      ->join('merc_po_order_details', 'merc_po_order_header.po_number', '=', 'merc_po_order_details.po_no')
      ->join('merc_shop_order_detail', 'merc_po_order_details.shop_order_detail_id', '=', 'merc_shop_order_detail.shop_order_detail_id')
      ->join('merc_shop_order_header', 'merc_shop_order_detail.shop_order_id', '=', 'merc_shop_order_header.shop_order_id')
      ->join('merc_shop_order_delivery', 'merc_shop_order_header.shop_order_id', '=', 'merc_shop_order_delivery.shop_order_id')
      ->join('merc_customer_order_details', 'merc_shop_order_delivery.delivery_id', '=', 'merc_customer_order_details.details_id')
      ->join('item_master', 'merc_po_order_details.item_code', '=', 'item_master.master_id')
      ->join('store_grn_detail', 'item_master.master_id', '=', 'store_grn_detail.item_code')
      //->where(DB::raw('(DATE_FORMAT(merc_customer_order_details.rm_in_date,"%Y-%m-%d"))'), date("Y-m-d"))
      ->whereBetween(DB::raw('(DATE_FORMAT(merc_customer_order_details.rm_in_date,"%Y-%m-%d"))'),[$from,$to])
      ->where('merc_customer_order_details.rm_in_date','>=','merc_customer_order_details.planned_delivery_date ')
      ->first();

      return array('pass'=>$query,'fail'=>$query2);

    }


    public function loadRmDataLastTwoYear(){

      // $from =  date('Y-01-01');
      //$to = date('Y-m-d',strtotime("-1 days"));

      $query = DB::table('merc_po_order_header')
      ->select('item_master.master_code',DB::raw("COUNT(item_master.master_code) as OTD"))
      ->join('merc_po_order_details', 'merc_po_order_header.po_number', '=', 'merc_po_order_details.po_no')
      ->join('merc_shop_order_detail', 'merc_po_order_details.shop_order_detail_id', '=', 'merc_shop_order_detail.shop_order_detail_id')
      ->join('merc_shop_order_header', 'merc_shop_order_detail.shop_order_id', '=', 'merc_shop_order_header.shop_order_id')
      ->join('merc_shop_order_delivery', 'merc_shop_order_header.shop_order_id', '=', 'merc_shop_order_delivery.shop_order_id')
      ->join('merc_customer_order_details', 'merc_shop_order_delivery.delivery_id', '=', 'merc_customer_order_details.details_id')
      ->join('item_master', 'merc_po_order_details.item_code', '=', 'item_master.master_id')
      ->join('store_grn_detail', 'item_master.master_id', '=', 'store_grn_detail.item_code')
      ->where(DB::raw('(DATE_FORMAT(merc_customer_order_details.rm_in_date,"%Y"))'), date("Y")-1)
      ->where('merc_customer_order_details.rm_in_date','<=','merc_customer_order_details.planned_delivery_date ')
      ->first();

      $query2 = DB::table('merc_po_order_header')
      ->select('item_master.master_code',DB::raw("COUNT(item_master.master_code) as OTD"))
      ->join('merc_po_order_details', 'merc_po_order_header.po_number', '=', 'merc_po_order_details.po_no')
      ->join('merc_shop_order_detail', 'merc_po_order_details.shop_order_detail_id', '=', 'merc_shop_order_detail.shop_order_detail_id')
      ->join('merc_shop_order_header', 'merc_shop_order_detail.shop_order_id', '=', 'merc_shop_order_header.shop_order_id')
      ->join('merc_shop_order_delivery', 'merc_shop_order_header.shop_order_id', '=', 'merc_shop_order_delivery.shop_order_id')
      ->join('merc_customer_order_details', 'merc_shop_order_delivery.delivery_id', '=', 'merc_customer_order_details.details_id')
      ->join('item_master', 'merc_po_order_details.item_code', '=', 'item_master.master_id')
      ->join('store_grn_detail', 'item_master.master_id', '=', 'store_grn_detail.item_code')
      ->where(DB::raw('(DATE_FORMAT(merc_customer_order_details.rm_in_date,"%Y"))'), date("Y")-1)
      ->where('merc_customer_order_details.rm_in_date','>=','merc_customer_order_details.planned_delivery_date ')
      ->first();

      return array('pass'=>$query,'fail'=>$query2);

    }
    public function loadInventoryval(){

     $query = DB::table('store_stock')
              ->select(DB::raw("ROUND(SUM(store_stock.qty*store_stock.standard_price),2) as account_total"))
              ->where(DB::raw('(DATE_FORMAT(store_stock.created_date,"%Y-%m-%d"))'), date('Y-m-d')) // CURRENT_DATE()
              //->where(DB::raw('(DATE_FORMAT(store_stock.created_date,"%Y-%m-%d"))'), date("Y-m-d"))
              ->first();

    return array('pass'=>$query);

    }

    public function InventoryPlantWise(){

    $query2 = DB::table('store_stock')
      ->select('org_location.loc_name',DB::raw("ROUND(SUM(store_stock.qty*store_stock.standard_price),2) as account_total"),DB::raw('YEAR(store_stock.created_date)','=','2020'))
      ->join('org_location', 'store_stock.location', '=', 'org_location.loc_id')
      ->where(DB::raw('(DATE_FORMAT(store_stock.created_date,"%Y-%m-%d"))'), '2020-01-14')
      //->where(DB::raw('(DATE_FORMAT(store_stock.created_date,"%Y-%m-%d"))'), date("Y-m-d"))
      ->groupBy('org_location.loc_name')
      ->get()
      ->toArray();

      return $query2;

    }

    public function InventoryPlantWiseLastYear(){

      $from =  date('Y-01-01');
      $to = date('Y-m-d',strtotime("-1 days"));

    $query2 = DB::table('store_stock')
      ->select('org_location.loc_name',DB::raw("ROUND(SUM(store_stock.qty*store_stock.standard_price),2) as account_total"),DB::raw('YEAR(store_stock.created_date)','=','2019'))
      ->join('org_location', 'store_stock.location', '=', 'org_location.loc_id')
      ->whereBetween(DB::raw('(DATE_FORMAT(store_stock.created_date,"%Y-%m-%d"))'), [$from,$to])
      ->groupBy('org_location.loc_name')
      ->get()
      ->toArray();

      return $query2;

    }

    public function InventoryPlantWiseLastTwoYear(){ //current year

    $query2 = DB::table('store_stock')
      ->select('org_location.loc_name',DB::raw("ROUND(SUM(store_stock.qty*store_stock.standard_price),2) as account_total"),DB::raw('YEAR(store_stock.created_date)','=','2018'))
      ->join('org_location', 'store_stock.location', '=', 'org_location.loc_id')
      ->where(DB::raw('(DATE_FORMAT(store_stock.created_date,"%Y"))'), date("Y")-1)
      ->groupBy('org_location.loc_name')
      ->get()
      ->toArray();

      return $query2;

    }

    public function InventoryPlantWiseThihariya(){

    $query2 = DB::table('store_stock')
      ->select('org_location.loc_name','item_category.category_name',DB::raw("ROUND(SUM(store_stock.qty*store_stock.standard_price),2) as account_total"),DB::raw('YEAR(store_stock.created_date)','=','2018'))
      ->join('item_master','item_master.master_id','=','store_stock.item_id')
      ->join('item_category','item_category.category_id','=','item_master.category_id')
      ->join('org_location','org_location.loc_id','=','store_stock.location')
      ->where('org_location.loc_id','=','24')
      ->where(DB::raw('(DATE_FORMAT(store_stock.created_date,"%Y-%m-%d"))'), date("Y-m-d"))//%Y-%M-%d CURRENT_DATE()
      ->groupBy('item_category.category_id')
      ->get()
      ->toArray();

      return $query2;
    }
    public function InventoryPlantWiseThihariyaYesterday(){

      $from =  date('Y-01-01');
      $to = date('Y-m-d',strtotime("-1 days"));

      $query3 = DB::table('store_stock')
        ->select('org_location.loc_name','item_category.category_name',DB::raw("ROUND(SUM(store_stock.qty*store_stock.standard_price),2) as account_total"),DB::raw('YEAR(store_stock.created_date)','=','2018'))
        ->join('item_master','item_master.master_id','=','store_stock.item_id')
        ->join('item_category','item_category.category_id','=','item_master.category_id')
        ->join('org_location','org_location.loc_id','=','store_stock.location')
        ->where('org_location.loc_id','=','24')
        ->whereBetween(DB::raw('(DATE_FORMAT(store_stock.created_date,"%Y-%m-%d"))'), [$from,$to])//%Y-%M-%d CURRENT_DATE()
        ->groupBy('item_category.category_id')
        ->get()
        ->toArray();

      //return $query2;
      return $query3;

    }
    public function InventoryPlantWiseKelaniya(){

    $query2 = DB::table('store_stock')
      ->select('org_location.loc_name','item_category.category_name',DB::raw("ROUND(SUM(store_stock.qty*store_stock.standard_price),2) as account_total"),DB::raw('YEAR(store_stock.created_date)','=','2018'))
      ->join('item_master','item_master.master_id','=','store_stock.item_id')
      ->join('item_category','item_category.category_id','=','item_master.category_id')
      ->join('org_location','org_location.loc_id','=','store_stock.location')
      ->where('org_location.loc_id','=','26')
      ->where(DB::raw('(DATE_FORMAT(store_stock.created_date,"%Y-%m-%d"))'), date("Y-m-d"))//%Y-%M-%d CURRENT_DATE()
      ->groupBy('item_category.category_id')
      ->get()
      ->toArray();

      return $query2;
    }

      public function InventoryPlantWiseKelaniyaYesterday(){

      $from =  date('Y-01-01');
      $to = date('Y-m-d',strtotime("-1 days"));

      $query3 = DB::table('store_stock')
        ->select('org_location.loc_name','item_category.category_name',DB::raw("ROUND(SUM(store_stock.qty*store_stock.standard_price),2) as account_total"),DB::raw('YEAR(store_stock.created_date)','=','2018'))
        ->join('item_master','item_master.master_id','=','store_stock.item_id')
        ->join('item_category','item_category.category_id','=','item_master.category_id')
        ->join('org_location','org_location.loc_id','=','store_stock.location')
        ->where('org_location.loc_id','=','26')
        ->whereBetween(DB::raw('(DATE_FORMAT(store_stock.created_date,"%Y-%m-%d"))'), [$from,$to])//%Y-%M-%d CURRENT_DATE()
        ->groupBy('item_category.category_id')
        ->get()
        ->toArray();

      //return $query2;
      return $query3;


    }
    public function InventoryPlantWiseMawathagama(){

    $query2 = DB::table('store_stock')
      ->select('org_location.loc_name','item_category.category_name',DB::raw("ROUND(SUM(store_stock.qty*store_stock.standard_price),2) as account_total"),DB::raw('YEAR(store_stock.created_date)','=','2018'))
      ->join('item_master','item_master.master_id','=','store_stock.item_id')
      ->join('item_category','item_category.category_id','=','item_master.category_id')
      ->join('org_location','org_location.loc_id','=','store_stock.location')
      ->where('org_location.loc_id','=','24')
      ->where(DB::raw('(DATE_FORMAT(store_stock.created_date,"%Y-%m-%d"))'), date("Y-m-d"))//%Y-%M-%d CURRENT_DATE()
      ->groupBy('item_category.category_id')
      ->get()
      ->toArray();

      return $query2;
    }

      public function InventoryPlantWiseMawathagamaYesterday(){
      $from =  date('Y-01-01');
      $to = date('Y-m-d',strtotime("-1 days"));

      $query3 = DB::table('store_stock')
        ->select('org_location.loc_name','item_category.category_name',DB::raw("ROUND(SUM(store_stock.qty*store_stock.standard_price),2) as account_total"),DB::raw('YEAR(store_stock.created_date)','=','2018'))
        ->join('item_master','item_master.master_id','=','store_stock.item_id')
        ->join('item_category','item_category.category_id','=','item_master.category_id')
        ->join('org_location','org_location.loc_id','=','store_stock.location')
        ->where('org_location.loc_id','=','24')
        ->whereBetween(DB::raw('(DATE_FORMAT(store_stock.created_date,"%Y-%m-%d"))'), [$from,$to])//%Y-%M-%d CURRENT_DATE()
        ->groupBy('item_category.category_id')
        ->get()
        ->toArray();

      //return $query2;
      return $query3;

    }
    public function InventoryPlantWiseNaula(){

    $query2 = DB::table('store_stock')
      ->select('org_location.loc_name','item_category.category_name',DB::raw("ROUND(SUM(store_stock.qty*store_stock.standard_price),2) as account_total"),DB::raw('YEAR(store_stock.created_date)','=','2018'))
      ->join('item_master','item_master.master_id','=','store_stock.item_id')
      ->join('item_category','item_category.category_id','=','item_master.category_id')
      ->join('org_location','org_location.loc_id','=','store_stock.location')
      ->where('org_location.loc_id','=','24')
      ->where(DB::raw('(DATE_FORMAT(store_stock.created_date,"%Y-%m-%d"))'), date("Y-m-d"))//%Y-%M-%d CURRENT_DATE()
      ->groupBy('item_category.category_id')
      ->get()
      ->toArray();

      return $query2;

    }

      public function InventoryPlantWiseNaulaYesterday(){

      $from =  date('Y-01-01');
      $to = date('Y-m-d',strtotime("-1 days"));

      $query3 = DB::table('store_stock')
        ->select('org_location.loc_name','item_category.category_name',DB::raw("ROUND(SUM(store_stock.qty*store_stock.standard_price),2) as account_total"),DB::raw('YEAR(store_stock.created_date)','=','2018'))
        ->join('item_master','item_master.master_id','=','store_stock.item_id')
        ->join('item_category','item_category.category_id','=','item_master.category_id')
        ->join('org_location','org_location.loc_id','=','store_stock.location')
        ->where('org_location.loc_id','=','24')
        ->whereBetween(DB::raw('(DATE_FORMAT(store_stock.created_date,"%Y-%m-%d"))'), [$from,$to])//%Y-%M-%d CURRENT_DATE()
        ->groupBy('item_category.category_id')
        ->get()
        ->toArray();

      //return $query2;
      return $query3;

    }
    public function InventoryPlantWiseUhumeeya(){

    $query2 = DB::table('store_stock')
      ->select('org_location.loc_name','item_category.category_name',DB::raw("ROUND(SUM(store_stock.qty*store_stock.standard_price),2) as account_total"),DB::raw('YEAR(store_stock.created_date)','=','2018'))
      ->join('item_master','item_master.master_id','=','store_stock.item_id')
      ->join('item_category','item_category.category_id','=','item_master.category_id')
      ->join('org_location','org_location.loc_id','=','store_stock.location')
      ->where('org_location.loc_id','=','24')
      ->where(DB::raw('(DATE_FORMAT(store_stock.created_date,"%Y-%m-%d"))'), date("Y-m-d"))//%Y-%M-%d CURRENT_DATE()
      ->groupBy('item_category.category_id')
      ->get()
      ->toArray();

      return $query2;

    }

      public function InventoryPlantWiseUhumeeyaYesterday(){

      $from =  date('Y-01-01');
      $to = date('Y-m-d',strtotime("-1 days"));

      $query3 = DB::table('store_stock')
        ->select('org_location.loc_name','item_category.category_name',DB::raw("ROUND(SUM(store_stock.qty*store_stock.standard_price),2) as account_total"),DB::raw('YEAR(store_stock.created_date)','=','2018'))
        ->join('item_master','item_master.master_id','=','store_stock.item_id')
        ->join('item_category','item_category.category_id','=','item_master.category_id')
        ->join('org_location','org_location.loc_id','=','store_stock.location')
        ->where('org_location.loc_id','=','24')
        ->whereBetween(DB::raw('(DATE_FORMAT(store_stock.created_date,"%Y-%m-%d"))'), [$from,$to])//%Y-%M-%d CURRENT_DATE()
        ->groupBy('item_category.category_id')
        ->get()
        ->toArray();

      //return $query2;
      return $query3;

    }
    public function InventoryPlantWiseNarammalaYesterday(){

      $from =  date('Y-01-01');
      $to = date('Y-m-d',strtotime("-1 days"));

    $query2 = DB::table('store_stock')
      ->select('org_location.loc_name','item_category.category_name',DB::raw("ROUND(SUM(store_stock.qty*store_stock.standard_price),2) as account_total"),DB::raw('YEAR(store_stock.created_date)','=','2018'))
      ->join('item_master','item_master.master_id','=','store_stock.item_id')
      ->join('item_category','item_category.category_id','=','item_master.category_id')
      ->join('org_location','org_location.loc_id','=','store_stock.location')
      ->where('org_location.loc_id','=','24')
      ->whereBetween(DB::raw('(DATE_FORMAT(store_stock.created_date,"%Y-%m-%d"))'), [$from,$to])//%Y-%M-%d CURRENT_DATE()
      ->groupBy('item_category.category_id')
      ->get()
      ->toArray();

      return $query2;
    }

    public function InventoryPlantWiseThihariyaLastYear(){

    $from =  date('Y',strtotime("-1 year"));
    $to = date('Y-m-d',strtotime("-1 days"));

    $query3 = DB::table('store_stock')
      ->select('org_location.loc_name','item_category.category_name',DB::raw("ROUND(SUM(store_stock.qty*store_stock.standard_price),2) as account_total"),DB::raw('YEAR(store_stock.created_date)','=','2018'))
      ->join('item_master','item_master.master_id','=','store_stock.item_id')
      ->join('item_category','item_category.category_id','=','item_master.category_id')
      ->join('org_location','org_location.loc_id','=','store_stock.location')
      ->where('org_location.loc_id','=','24')
      ->where(DB::raw('(DATE_FORMAT(store_stock.created_date,"%Y"))'), $from)//%Y-%M-%d CURRENT_DATE()
      ->groupBy('item_category.category_id')
      ->get()
      ->toArray();

    //return $query2;
    return $query3;

  }

  public function InventoryPlantWiseKelaniyaLastYear(){

  $from =  date('Y-01-01');
  $to = date('Y-m-d',strtotime("-1 days"));

  $query3 = DB::table('store_stock')
    ->select('org_location.loc_name','item_category.category_name',DB::raw("ROUND(SUM(store_stock.qty*store_stock.standard_price),2) as account_total"),DB::raw('YEAR(store_stock.created_date)','=','2018'))
    ->join('item_master','item_master.master_id','=','store_stock.item_id')
    ->join('item_category','item_category.category_id','=','item_master.category_id')
    ->join('org_location','org_location.loc_id','=','store_stock.location')
    ->where('org_location.loc_id','=','24')
    ->where(DB::raw('(DATE_FORMAT(store_stock.created_date,"%Y"))'), $from)//%Y-%M-%d CURRENT_DATE()
    ->groupBy('item_category.category_id')
    ->get()
    ->toArray();

  //return $query2;
  return $query3;

}

public function InventoryPlantWiseMawathagamaLastYear(){

$from =  date('Y-01-01');
$to = date('Y-m-d',strtotime("-1 days"));

$query3 = DB::table('store_stock')
  ->select('org_location.loc_name','item_category.category_name',DB::raw("ROUND(SUM(store_stock.qty*store_stock.standard_price),2) as account_total"),DB::raw('YEAR(store_stock.created_date)','=','2018'))
  ->join('item_master','item_master.master_id','=','store_stock.item_id')
  ->join('item_category','item_category.category_id','=','item_master.category_id')
  ->join('org_location','org_location.loc_id','=','store_stock.location')
  ->where('org_location.loc_id','=','24')
  ->where(DB::raw('(DATE_FORMAT(store_stock.created_date,"%Y"))'), $from)//%Y-%M-%d CURRENT_DATE()
  ->groupBy('item_category.category_id')
  ->get()
  ->toArray();

//return $query2;
return $query3;

}

public function InventoryPlantWiseNaulaLastYear(){

$from =  date('Y-01-01');
$to = date('Y-m-d',strtotime("-1 days"));

$query3 = DB::table('store_stock')
  ->select('org_location.loc_name','item_category.category_name',DB::raw("ROUND(SUM(store_stock.qty*store_stock.standard_price),2) as account_total"),DB::raw('YEAR(store_stock.created_date)','=','2018'))
  ->join('item_master','item_master.master_id','=','store_stock.item_id')
  ->join('item_category','item_category.category_id','=','item_master.category_id')
  ->join('org_location','org_location.loc_id','=','store_stock.location')
  ->where('org_location.loc_id','=','24')
  ->where(DB::raw('(DATE_FORMAT(store_stock.created_date,"%Y"))'), $from)//%Y-%M-%d CURRENT_DATE()
  ->groupBy('item_category.category_id')
  ->get()
  ->toArray();

//return $query2;
return $query3;

}

public function InventoryPlantWiseUhumeeyaLastYear(){

$from =  date('Y-01-01');
$to = date('Y-m-d',strtotime("-1 days"));

$query3 = DB::table('store_stock')
  ->select('org_location.loc_name','item_category.category_name',DB::raw("ROUND(SUM(store_stock.qty*store_stock.standard_price),2) as account_total"),DB::raw('YEAR(store_stock.created_date)','=','2018'))
  ->join('item_master','item_master.master_id','=','store_stock.item_id')
  ->join('item_category','item_category.category_id','=','item_master.category_id')
  ->join('org_location','org_location.loc_id','=','store_stock.location')
  ->where('org_location.loc_id','=','24')
  ->where(DB::raw('(DATE_FORMAT(store_stock.created_date,"%Y"))'), $from)//%Y-%M-%d CURRENT_DATE()
  ->groupBy('item_category.category_id')
  ->get()
  ->toArray();

//return $query2;
return $query3;

}

public function InventoryPlantWiseNarammalaLastYear(){

$from =  date('Y-01-01');
$to = date('Y-m-d',strtotime("-1 days"));

$query3 = DB::table('store_stock')
  ->select('org_location.loc_name','item_category.category_name',DB::raw("ROUND(SUM(store_stock.qty*store_stock.standard_price),2) as account_total"),DB::raw('YEAR(store_stock.created_date)','=','2018'))
  ->join('item_master','item_master.master_id','=','store_stock.item_id')
  ->join('item_category','item_category.category_id','=','item_master.category_id')
  ->join('org_location','org_location.loc_id','=','store_stock.location')
  ->where('org_location.loc_id','=','24')
  ->where(DB::raw('(DATE_FORMAT(store_stock.created_date,"%Y"))'), $from)//%Y-%M-%d CURRENT_DATE()
  ->groupBy('item_category.category_id')
  ->get()
  ->toArray();

//return $query2;
return $query3;

}

    public function PendingCIIE(){
      $pending= DB::table('app_process_approval')
          ->select('app_process_approval.process',DB::raw("count(app_process_approval.id) as count"))
          ->join('usr_profile', 'usr_profile.user_id', '=', 'app_process_approval.document_created_by')
          ->where('app_process_approval.document_created_by', '=', auth()->user()->user_id)//auth()->user()->user_id
          ->where('app_process_approval.status', '=', 'PENDING' )
          ->groupBy('app_process_approval.process')
          ->get()
          ->toArray();

      return $pending;
    }

    public function ApprovedCIIE(){
      $pending= DB::table('app_process_approval')
          ->select('app_process_approval.process',DB::raw("count(app_process_approval.id) as count"))
          ->join('usr_profile', 'usr_profile.user_id', '=', 'app_process_approval.document_created_by')
          ->where('app_process_approval.document_created_by', '=', auth()->user()->user_id)//auth()->user()->user_id
          ->where('app_process_approval.status', '=', 'APPROVED' )
          ->groupBy('app_process_approval.process')
          ->get()
          ->toArray();

      return $pending;
    }

    public function RejectedCIIE(){
      $pending= DB::table('app_process_approval')
          ->select('app_process_approval.process',DB::raw("count(app_process_approval.id) as count"))
          ->join('usr_profile', 'usr_profile.user_id', '=', 'app_process_approval.document_created_by')
          ->where('app_process_approval.document_created_by', '=', auth()->user()->user_id)//auth()->user()->user_id-80
          ->where('app_process_approval.status', '=', 'PENDING' )
          ->groupBy('app_process_approval.process')
          ->get()
          ->toArray();

      return $pending;
    }

    // public function loadRM(){
    //       $query = DB::table('merc_customer_order_details')
    //       ->select(DB::raw("COUNT(rm_in_date) AS count"))
    //       ->where('rm_in_date','CURRENT_DATE()')
    //       ->first();
    //       return json_encode($query);
    // }

    private function datatable_search()
    {
      //$quarter = $data['data']['customer_name']['customer_id'];

      $query = DB::table('merc_customer_order_details')
      ->select(
      DB::raw(" CONCAT( YEAR( planned_delivery_date ) , '-', MONTHNAME( planned_delivery_date )) AS ym"),
      DB::raw(" ROUND (merc_customer_order_details.planned_qty*merc_customer_order_details.fob,2)) AS total_qty")
      );
      //
      // if($quarter = "Q1"){
      //   $query->whereIn('MONTH(planned_delivery_date)', [4,5,6]);
      // }
      // if($quarter = Q2){
      //   $query->whereIn('MONTH(planned_delivery_date)', [7,8,9]);
      // }
      // if($quarter = Q3){
      //   $query->whereIn('MONTH(planned_delivery_date)', [10,11,12]);
      // }
      // if($quarter = Q4){
      //   $query->whereIn('MONTH(planned_delivery_date)', [1,2,3]);
      // }
      // else{
      // $query->whereBetween('planned_delivery_date','2019-04-01','2020-03-31');
      // }
      $query->whereBetween('planned_delivery_date','2019-04-01','2020-03-31');
      $query->groupBy('MONTH(planned_delivery_date)');

      return(json_encode(array('data'=>$monthly)));

   }

 }
