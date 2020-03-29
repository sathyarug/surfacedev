<?php

namespace App\Http\Controllers\Store;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Http\Controllers\Controller;
use App\Models\stores\StoRollDescription;
use App\Models\stores\PoOrderDetails;
use App\Models\store\StoRollFabricinSpection;
use App\Models\Stores\RollPlan;
use App\Models\Store\GrnHeader;
use App\Models\Store\GrnDetail;
use App\Models\Store\FabricInspection;
use App\Models\Finance\Transaction;
use App\Models\Store\StockTransaction;
use App\Models\Store\Stock;
use App\Models\Org\ConversionFactor;
use App\Models\Org\UOM;
use App\Models\Finance\PriceVariance;
use App\Models\Merchandising\ShopOrderHeader;
use App\Models\Merchandising\ShopOrderDetail;
use App\Models\Merchandising\Item\Item;
use Exception;
use Illuminate\Support\Facades\DB;


class FabricInspectionController extends Controller
{
  public function index(Request $request)
  {
    $type = $request->type;
    if($type == 'datatable')   {
      $data = $request->all();
      return response($this->datatable_search($data));
    }
    else if($type == 'autoInvoice')    {
      $search = $request->search;
      return response($this->autocomplete_search_invoice($search));
    }

    else if($type == 'autoBatchNO'){
      $search = $request->search;
      return response($this->autocomplete_search_batchNo($search));
    }
    else if($type == 'autoBatchNoFilter'){
      $inv_no = $request->inv_no;
      $batch_no=$request->batch_no;
      return ($this->autocomplete_search_bacth_filter($inv_no,$batch_no));
    }
    else if($type == 'autoItemCodeFilter'){
      $inv_no = $request->inv_no;
      $batch_no=$request->batch_no;
      $item_code = $request->search;
      //dd($item_code);
      return ($this->autocomplete_search_item_code_filter($item_code,$inv_no,$batch_no));
    }
    else if($type=='autoStatusTypes'){
      $search = $request->search;
      return response($this->autocomplete_search_inspection_status($search));
    }
      else {
      $active = $request->active;
      $fields = $request->fields;
      return response([
        'data' => $this->list($active , $fields)
      ]);
    }
  }


  //get a Color
  public function show($id)
  {
      $status=1;
      $grnDetails = GrnDetail::join('store_roll_plan','store_grn_detail.grn_detail_id','=','store_roll_plan.grn_detail_id')
      //->join('store_grn_header','store_grn_detail.grn_id','=','store_grn_header.grn_id')
      ->join('store_fabric_inspection','store_roll_plan.roll_plan_id','=','store_fabric_inspection.roll_plan_id')
      ->join('org_store_bin','store_fabric_inspection.bin','=','org_store_bin.store_bin_id')
      ->join('item_master','store_grn_detail.item_code','=','item_master.master_id')
      ->where('store_grn_detail.grn_detail_id','=',$id)
      ->where('store_grn_detail.status','=',$status)
      ->select('store_fabric_inspection.*','store_fabric_inspection.inspection_status as status_name','org_store_bin.store_bin_name','store_fabric_inspection.qty as previous_received_qty','store_roll_plan.grn_detail_id','store_fabric_inspection.inspection_status as previous_status_name','store_grn_detail.grn_qty','store_grn_detail.item_code','item_master.master_code')
      ->get();
      //$dp=json_encode($grnDetails[0]);
    //  dd($grnDetails[0]['item_code']);

      $itemData=Item::select("*")->where('master_id','=',$grnDetails[0]['item_code'])->first();
      $invoice = GrnHeader::select('*')->where('inv_number','=',$grnDetails[0]['invoice_no'])->first();
      $batch= GrnHeader::select('*')->where('batch_no','=',$grnDetails[0]['batch_no'])->first();
      $grn_detail_id=$grnDetails[0]['grn_detail_id'];
      if($grnDetails == null)
        throw new ModelNotFoundException("Requested color not found", 1);
      else
        return response([ 'data'  => ['data'=>$grnDetails,
                                    'item'=>$itemData,
                                      'invoiceNo'=>$invoice,
                                      'batchNo'=>$batch,
                                      'grn_detail_id'=>$grn_detail_id
                                    ]
                            ]);

  }





  public function store(Request $request)
  {
//dd($request);

      //  dd(sizeof($request->data));

      $data=$request->data;
      //loop through the data set
        $this->setGrnQtytemparlyZero($data);
        for($i=0;$i<sizeof($data);$i++){
          //save data on fabric inspection table
        $fabricInspection = new FabricInspection();
        $fabricInspection->roll_plan_id=$data[$i]['roll_plan_id'];

        $fabricInspection->lot_no=$data[$i]['lot_no'];
        $fabricInspection->invoice_no=$data[$i]['invoice_no'];
        $fabricInspection->batch_no=$data[$i]['batch_no'];
        $fabricInspection->roll_no=$data[$i]['roll_no'];
        $fabricInspection->qty=$data[$i]['qty'];
        $fabricInspection->received_qty=$data[$i]['received_qty'];
        $fabricInspection->bin=$data[$i]['bin'];
        $fabricInspection->width=$data[$i]['width'];
        $fabricInspection->shade=$data[$i]['shade'];
        $fabricInspection->inspection_status=$data[$i]['status_name'];
        $fabricInspection->lab_comment=$data[$i]['lab_comment'];
        $fabricInspection->comment=$data[$i]['comment'];
        $fabricInspection->status=1;

        $fabricInspection->save();
        //get status pass data line only
        if($fabricInspection->inspection_status=='PASS'){
          //get transaction code
          $transaction = Transaction::where('trans_description', 'STOCKUPDATE')->first();
          //get roll plan details related to inspection line
          $rollplanDetail=DB::SELECT("SELECT
                   store_roll_plan.grn_detail_id,
                    store_fabric_inspection.roll_plan_id,
                     store_grn_header.main_store,
                     store_grn_header.sub_store,
                     store_grn_detail.grn_detail_id,
                      store_grn_detail.grn_id,
                      store_grn_detail.grn_line_no,
                     store_grn_detail.style_id,
                    store_grn_detail.combine_id,
                    store_grn_detail.color,
                    store_grn_detail.size,
                    store_grn_detail.uom,
                    store_grn_detail.item_code,
                   store_grn_detail.po_qty,
                   store_grn_detail.standard_price,
                   store_grn_detail.purchase_price,
store_grn_detail.grn_qty,
store_grn_detail.bal_qty,
store_grn_detail.original_bal_qty,
store_grn_detail.po_details_id,
store_grn_detail.po_number,
store_grn_detail.maximum_tolarance,
store_grn_detail.customer_po_id,
item_master.standard_price,
store_grn_detail.purchase_price,
item_master.inventory_uom,
store_grn_detail.shop_order_id,
store_grn_detail.shop_order_detail_id,
store_roll_plan.bin
FROM
store_fabric_inspection
INNER JOIN store_roll_plan ON store_roll_plan.roll_plan_id = store_fabric_inspection.roll_plan_id
INNER JOIN store_grn_detail ON store_grn_detail.grn_detail_id = store_roll_plan.grn_detail_id
#INNER JOIN item_master on item_master.master_id=store_grn_detail.item_code
INNER JOIN item_master ON store_grn_detail.item_code=item_master.master_id
INNER JOIN store_grn_header ON store_grn_header.grn_id = store_grn_detail.grn_id
WHERE store_fabric_inspection.roll_plan_id=$fabricInspection->roll_plan_id");
            //save stock transaction table line
//dd($rollplanDetail);            //dd($rollplanDetail[0]);
          $st = new StockTransaction;
          $st->status = 'PASS';
          $st->doc_type = $transaction->trans_code;
          $st->doc_num = $rollplanDetail[0]->grn_id;
          $st->style_id = $rollplanDetail[0]->style_id;
          $st->main_store = $rollplanDetail[0]->main_store;
          $st->sub_store = $rollplanDetail[0]->sub_store;
          $st->item_code = $rollplanDetail[0]->item_code;
          $st->size = $rollplanDetail[0]->size;
          $st->color = $rollplanDetail[0]->color;
          $st->uom = $rollplanDetail[0]->uom;
          $st->customer_po_id=$rollplanDetail[0]->customer_po_id;
          $st->qty = $fabricInspection->qty;
          $st->location = auth()->payload()['loc_id'];
          $st->bin = $rollplanDetail[0]->bin;
          $st->created_by = auth()->payload()['user_id'];
          $st->shop_order_id=$rollplanDetail[0]->shop_order_id;
          $st->shop_order_detail_id=$rollplanDetail[0]->shop_order_detail_id;
          $st->standard_price = $rollplanDetail[0]->standard_price;
          $st->purchase_price = $rollplanDetail[0]->purchase_price;
          $st->direction="+";
          $st->save();
          $po_detail_id=$rollplanDetail[0]->po_details_id;
          $loc= auth()->payload()['loc_id'];

          $grnDetails=GrnDetail::find($data[$i]['grn_detail_id']);
          $grnDetails->grn_qty=$grnDetails->grn_qty+$fabricInspection->qty;
          $grnDetails->save();
          //*balance qty suould be get from shop order detail level*
          $balanceQty=DB::SELECT("SELECT min(bal_qty)
                        from store_grn_detail
                        where po_details_id=$po_detail_id");

          //find exact line of stock(old way)
          $cus_po=$rollplanDetail[0]->customer_po_id;
          $style_id=$rollplanDetail[0]->style_id;
          $item_code=$rollplanDetail[0]->item_code;
          $size=$rollplanDetail[0]->size;

        //  $size=1;
          $color=$rollplanDetail[0]->color;
          $main_store=$rollplanDetail[0]->main_store;
          $sub_store=$rollplanDetail[0]->sub_store;
          $bin=$rollplanDetail[0]->bin;
        /*  if($size==null){
            $size_serach=0;
          }
          else {
            $size_serach=$size;
          }*/
          //find exact line related to item code
          $findStoreStockLine=DB::SELECT ("SELECT * FROM store_stock
                                           where item_id= $item_code
                                           AND shop_order_id=$st->shop_order_id
                                           AND style_id=$style_id
                                           AND shop_order_detail_id=$st->shop_order_detail_id
                                           AND bin=$bin
                                           AND store=$main_store
                                           AND sub_store=$sub_store
                                           AND location=$st->location");
                    //dd($findStoreStockLine);

            //if relted item is'nt avalabe in the stock
        if($findStoreStockLine==null){
          //enter as a new line

          $storeUpdate=new Stock();
          $storeUpdate->shop_order_id=$rollplanDetail[0]->shop_order_id;
          $storeUpdate->shop_order_detail_id=$rollplanDetail[0]->shop_order_detail_id;
          $storeUpdate->style_id = $rollplanDetail[0]->style_id;
          $storeUpdate->item_id= $rollplanDetail[0]->item_code;
          $storeUpdate->size = $rollplanDetail[0]->size;
          $storeUpdate->color = $rollplanDetail[0]->color;
          $storeUpdate->location = auth()->payload()['loc_id'];
          $storeUpdate->store = $rollplanDetail[0]->main_store;
          $storeUpdate->sub_store =$rollplanDetail[0]->sub_store;
          $storeUpdate->bin = $rollplanDetail[0]->bin;
          $storeUpdate->standard_price = $rollplanDetail[0]->standard_price;
          $storeUpdate->purchase_price = $rollplanDetail[0]->purchase_price;
          //check staned price and purchase price is varied
          if($storeUpdate->standard_price!=$rollplanDetail[0]->purchase_price){
            //save data on price variation table
            $priceVariance= new PriceVariance;
            $priceVariance->item_id=$rollplanDetail[0]->item_code;
            $priceVariance->standard_price=$rollplanDetail[0]->standard_price;
            $priceVariance->purchase_price =$rollplanDetail[0]->purchase_price;
            $priceVariance->shop_order_id =$rollplanDetail[0]->shop_order_id;
            $priceVariance->shop_order_detail_id =$rollplanDetail[0]->shop_order_detail_id;
            $priceVariance->status =1;
            $priceVariance->save();
          }
              //check inventory uom and purchase uom varied each other
            if($rollplanDetail[0]->uom!=$rollplanDetail[0]->inventory_uom){
              $storeUpdate->uom = $rollplanDetail[0]->inventory_uom;
              $_uom_unit_code=UOM::where('uom_id','=',$rollplanDetail[0]->inventory_uom)->pluck('uom_code');
              $_uom_base_unit_code=UOM::where('uom_id','=',$rollplanDetail[0]->uom)->pluck('uom_code');
              //get convertion equatiojn details
              $ConversionFactor=ConversionFactor::select('*')
                                                  ->where('unit_code','=',$_uom_unit_code[0])
                                                  ->where('base_unit','=',$_uom_base_unit_code[0])
                                                  ->first();
                                                    // convert values according to the convertion rate
                                                  $storeUpdate->qty =(double)($fabricInspection->qty*$ConversionFactor->present_factor);
                                                  //$storeUpdate->total_qty = (double)($fabricInspection->qty*$ConversionFactor->present_factor);
                                                  //$storeUpdate->tolerance_qty = (double)($rollplanDetail[0]->maximum_tolarance*$ConversionFactor->present_factor);
          }
          //if inventory uom and purchase uom are the same
          if($rollplanDetail[0]->uom==$rollplanDetail[0]->inventory_uom){
            $storeUpdate->uom = $rollplanDetail[0]->inventory_uom;
            $storeUpdate->qty =(double)($fabricInspection->qty);
            //$storeUpdate->total_qty = (double)($fabricInspection->qty);
            //$storeUpdate->tolerance_qty = $rollplanDetail[0]->maximum_tolarance;
          }


          //$storeUpdate->transfer_status="STOCKUPDATE";
          $storeUpdate->status=1;
          $shopOrder=ShopOrderDetail::find($rollplanDetail[0]->shop_order_detail_id);
          $shopOrder->asign_qty=$fabricInspection->qty+$shopOrder->asign_qty;
          $shopOrder->save();
          $storeUpdate->save();
        }
        //if related item already available in the stock
          else if($findStoreStockLine!=null){
            //find exaxt line in stock
            $stock=Stock::find($findStoreStockLine[0]->id);

            //if previous standerd price and new price is same

            if($stock->standard_price!=$rollplanDetail[0]->purchase_price){
              $priceVariance= new PriceVariance;
              $priceVariance->item_id=$rollplanDetail[0]->item_code;
              $priceVariance->standard_price=$rollplanDetail[0]->standard_price;
              $priceVariance->purchase_price =$rollplanDetail[0]->purchase_price;
              $priceVariance->shop_order_id =$rollplanDetail[0]->shop_order_id;
              $priceVariance->shop_order_detail_id =$rollplanDetail[0]->shop_order_detail_id;
              $priceVariance->status =1;
              $priceVariance->save();
            }

            //$stock->standard_price = $rollplanDetail[0]->standard_price;
            $stock->purchase_price = $rollplanDetail[0]->purchase_price;
            //if inventory uom and purchase uom is changed
            if($rollplanDetail[0]->uom!=$rollplanDetail[0]->inventory_uom){

                //$stock->uom = $rollplanDetail[0]->inventory_uom;

                $_uom_unit_code=UOM::where('uom_id','=',$rollplanDetail[0]->inventory_uom)->pluck('uom_code');
                $_uom_base_unit_code=UOM::where('uom_id','=',$rollplanDetail[0]->uom)->pluck('uom_code');
                //dd($rollplanDetail[0]);
                //get convertion equation details
                  $ConversionFactor=ConversionFactor::select('*')
                                                    ->where('unit_code','=',$_uom_unit_code[0])
                                                    ->where('base_unit','=',$_uom_base_unit_code[0])
                                                    ->first();
                                                    //update stock qty with convertion qty
                                                    $stock->qty =(double)$stock->qty+(double)($fabricInspection->qty*$ConversionFactor->present_factor);
                                                    //$stock->total_qty = (double)$stock->total_qty+(double)($fabricInspection->qty*$ConversionFactor->present_factor);
                                                    //$stock->tolerance_qty = (double)($rollplanDetail[0]->maximum_tolarance*$ConversionFactor->present_factor);
                                                  //  if($i==1)
                                                    //dd($stock->inv_qty);

            }
            //if inventory uom and purchase uom is same
            if($rollplanDetail[0]->uom==$rollplanDetail[0]->inventory_uom){

              $stock->qty = (double)$stock->qty+(double)($fabricInspection->qty);
              //$stock->total_qty=(double)$stock->total_qty+(double)($fabricInspection->qty);
              //$stock->tolerance_qty = $rollplanDetail[0]->maximum_tolarance;


            }

            $shopOrder=ShopOrderDetail::find($rollplanDetail[0]->shop_order_detail_id);
            $shopOrder->asign_qty=$fabricInspection->qty+$shopOrder->asign_qty;
            $shopOrder->save();
            //$stock->total_qty=$stock->total_qty+$fabricInspection->received_qty;
           //$stock->inv_qty = $stock->inv_qty+$fabricInspection->received_qty;
           $stock->save();
          }


      }


      }

        return response([ 'data' => [
          'message' => 'Fabric Inspection Saved Saved',
          'status' => 1
          ]
        ], Response::HTTP_CREATED );


  }

  private function setGrnQtytemparlyZero($data){
    for($i=0;$i<sizeof($data);$i++){
      $grnDetails=GrnDetail::find($data[$i]['grn_detail_id']);
    $grnDetails->grn_qty=0;
    $grnDetails->save();
    }
    //return true;
  }

  private function autocomplete_search_invoice($search){

    $invoice_list = GrnHeader::select('inv_number')->distinct('inv_number')
    ->where([['inv_number', 'like', '%' . $search . '%'],]) ->get();
    return $invoice_list;



  }
  private function autocomplete_search_batchNo($search){
    $invoice_list = GrnHeader::select('batch_no')->distinct('batch_no')
    ->where([['batch_no', 'like', '%' . $search . '%'],]) ->get();
    return $invoice_list;

  }
  private function autocomplete_search_inspection_status($search){
    //dd($search);
    $inspectionStatus=DB::table('store_inspec_status')->where('status_name','like','%'.$search.'%')->pluck('status_name');
    return $inspectionStatus;

  }

  public function autocomplete_search_bacth_filter($inv_no,$batch_no){
    //dd("dadad");
    $batch_list = GrnHeader::select('batch_no')->distinct('batch_no')
    ->where([['batch_no', 'like', '%' . $batch_no . '%'],])
    ->where('inv_number', '=', $inv_no) ->get();
//dd($batch_list);
   return $batch_list;

  }

public function autocomplete_search_item_code_filter($item_code,$inv_no,$batch_no){
    $item_list = GrnHeader::join('store_grn_detail','store_grn_detail.grn_id','=','store_grn_header.grn_id')
                            ->join('item_master','store_grn_detail.item_code','=','item_master.master_id')
    ->select('item_master.master_code','item_master.master_id','item_master.master_description')
    ->where('store_grn_header.batch_no','=', $batch_no)
    ->where('inv_number','=', $inv_no)
    ->where([['item_master.master_code', 'like', '%' . $item_code . '%'],])
    ->get();
    return $item_list;

  }
/*    public function store(Request $request)
    {


        foreach ($request->all()['roll_info'] as $key => $value)
        {
            $fabricinSpection= new StoRollFabricinSpection();

//            print_r($value);exit;
//            $fabricinSpection->item_code=$value['roll_no'];
            $fabricinSpection->roll_no=$value['roll_no'];
            $fabricinSpection->purchase_weight=$value['purchase_weight'];
            $fabricinSpection->save();

        }exit;

    }
*/
    public function search_rollPlan_details(Request $request){
      $batch_no=$request->batchNo;
      $invoice_no=$request->invoiceNo;
      $item_code=$request->itemCode;
      $status=1;
      $locId=auth()->payload()['loc_id'];
      $checkInspection=FabricInspection::select('store_grn_detail.grn_detail_id')
      ->join('store_roll_plan','store_fabric_inspection.roll_plan_id','=','store_roll_plan.roll_plan_id')
      ->join('store_grn_detail','store_roll_plan.grn_detail_id','=','store_grn_detail.grn_detail_id')
      ->join('store_grn_header','store_grn_detail.grn_id','=','store_grn_header.grn_id')
      ->where('store_fabric_inspection.invoice_no','=',$invoice_no)
      ->where('store_fabric_inspection.batch_no','=',$batch_no)
      ->where('store_grn_detail.item_code','=',$item_code)->first();
      if($checkInspection['grn_detail_id']!=null){
          return $this->show($checkInspection['grn_detail_id']);
      }
      $rollPlanDetails=DB::SELECT("SELECT store_roll_plan.*,org_store_bin.store_bin_name,store_grn_detail.grn_qty
          From store_roll_plan
          INNER JOIN store_grn_detail on store_roll_plan.grn_detail_id=store_grn_detail.grn_detail_id
          INNER JOIN store_grn_header on store_grn_detail.grn_id=store_grn_header.grn_id
          INNER JOIN org_store_bin on store_roll_plan.bin=org_store_bin.store_bin_id
          WHERE store_roll_plan.invoice_no='".$invoice_no."'
          AND store_roll_plan.batch_no='".$batch_no."'
          AND store_grn_detail.item_code='".$item_code."'
          AND store_grn_detail.status='".$status."'
          AND store_grn_header.location='".$locId."'
          "
          );
        $grn_detail_id=0;
          return response([ 'data'  => ['data'=>$rollPlanDetails,
                                      'item'=>$item_code,
                                        'invoiceNo'=>$invoice_no,
                                        'batchNo'=>$batch_no,
                                        'grn_detail_id'=>$grn_detail_id
                                      ]
                              ]);
    }

    //get searched Colors for datatable plugin format
    private function datatable_search($data)
    {

          $start = $data['start'];
          $length = $data['length'];
          $draw = $data['draw'];
          $search = $data['search']['value'];
          $order = $data['order'][0];
          $order_column = $data['columns'][$order['column']]['data'];
          $order_type = $order['dir'];
          //dd( $order_column);

          $inspection_list = FabricInspection::join('store_roll_plan','store_fabric_inspection.roll_plan_id','=','store_roll_plan.roll_plan_id')
          ->join('store_grn_detail','store_roll_plan.grn_detail_id','=','store_grn_detail.grn_detail_id')
          ->join('store_grn_header','store_grn_detail.grn_id','=','store_grn_header.grn_id')
          ->join('merc_po_order_header','store_grn_header.po_number','=','merc_po_order_header.po_id')
          ->join('item_master','store_grn_detail.item_code','=','item_master.master_id')
          ->select('store_fabric_inspection.*','store_grn_header.grn_number','merc_po_order_header.po_number','item_master.master_code','store_grn_detail.grn_qty','store_grn_detail.grn_detail_id')
          ->where('store_grn_header.grn_number'  , 'like', $search.'%' )
          ->orWhere('merc_po_order_header.po_number'  , 'like', $search.'%' )
          ->orWhere('item_master.master_code','like',$search.'%')
          ->orWhere('store_fabric_inspection.invoice_no','like',$search.'%')
          ->groupBy('store_roll_plan.grn_detail_id')
          ->orderBy($order_column, $order_type)
          ->offset($start)->limit($length)->get();

          $inspection_count = FabricInspection::join('store_roll_plan','store_fabric_inspection.roll_plan_id','=','store_roll_plan.roll_plan_id')
          ->join('store_grn_detail','store_roll_plan.grn_detail_id','=','store_grn_detail.grn_detail_id')
          ->join('store_grn_header','store_grn_detail.grn_id','=','store_grn_header.grn_id')
          ->join('merc_po_order_header','store_grn_header.po_number','=','merc_po_order_header.po_id')
          ->join('item_master','store_grn_detail.item_code','=','item_master.master_id')
          ->select('store_fabric_inspection.*','store_grn_header.grn_number','merc_po_order_header.po_number','item_master.master_code','store_grn_detail.grn_qty','store_grn_detail.grn_detail_id')
          ->where('store_grn_header.grn_number'  , 'like', $search.'%' )
          ->orWhere('merc_po_order_header.po_number'  , 'like', $search.'%' )
          ->orWhere('item_master.master_description','like',$search.'%')
          ->orWhere('store_fabric_inspection.invoice_no','like',$search.'%')
          //->groupBy('store_roll_plan.grn_detail_id')
          ->count();
          //dd($inspection_count);
          echo json_encode([
              "draw" => $draw,
              "recordsTotal" => $inspection_count,
              "recordsFiltered" => $inspection_count,
              "data" => $inspection_list
          ]);

    }


    //update a Color
    public function update(Request $request, $id)
    {        //
          $data=$request->data;
          $this->setGrnQtytemparlyZero($data);
          //loop through data set
        for($i=0;$i<sizeof($data);$i++){
          //get related line
          $fabricInspection = FabricInspection::find($data[$i]['fab_inspection_id']);
          $fabricInspection->roll_plan_id=$data[$i]['roll_plan_id'];
          $fabricInspection->lot_no=$data[$i]['lot_no'];
          $fabricInspection->invoice_no=$data[$i]['invoice_no'];
          $fabricInspection->batch_no=$data[$i]['batch_no'];
          $fabricInspection->roll_no=$data[$i]['roll_no'];
          $fabricInspection->qty=$data[$i]['qty'];
          //get updated qty
          $updated_st_qty=$data[$i]['previous_received_qty']-$data[$i]['qty'];
          //dd($fabricInspection->qty);
          $fabricInspection->received_qty=$data[$i]['received_qty'];
          $fabricInspection->bin=$data[$i]['bin'];
          $fabricInspection->width=$data[$i]['width'];
          $fabricInspection->shade=$data[$i]['shade'];
          $fabricInspection->inspection_status=$data[$i]['status_name'];
          $fabricInspection->lab_comment=$data[$i]['lab_comment'];
          $fabricInspection->comment=$data[$i]['comment'];
          $fabricInspection->status=1;
          //update inspection table
          $fabricInspection->save();
          //get inspection status pass line to update stock
          if($fabricInspection->inspection_status=='PASS'){
            //get transaction code
            $transaction = Transaction::where('trans_description', 'STOCKUPDATE')->first();
            //get roll Plan details for stock update
            $rollplanDetail=DB::SELECT("SELECT
                     store_roll_plan.grn_detail_id,
                      store_fabric_inspection.roll_plan_id,
                       store_grn_header.main_store,
                       store_grn_header.sub_store,
                       store_grn_detail.grn_detail_id,
                        store_grn_detail.grn_id,
                        store_grn_detail.grn_line_no,
                       store_grn_detail.style_id,
                      store_grn_detail.combine_id,
                      store_grn_detail.color,
                      store_grn_detail.size,
                      store_grn_detail.uom,
                      store_grn_detail.item_code,
                     store_grn_detail.po_qty,
                     store_grn_detail.standard_price,
                     store_grn_detail.purchase_price,
  store_grn_detail.grn_qty,
  store_grn_detail.bal_qty,
  store_grn_detail.original_bal_qty,
  store_grn_detail.po_details_id,
  store_grn_detail.po_number,
  store_grn_detail.maximum_tolarance,
  store_grn_detail.customer_po_id,
  item_master.standard_price,
  store_grn_detail.purchase_price,
  store_grn_detail.inventory_uom,
  store_grn_detail.shop_order_id,
  store_grn_detail.shop_order_detail_id,
  store_roll_plan.bin
  FROM
  store_fabric_inspection
  INNER JOIN store_roll_plan ON store_roll_plan.roll_plan_id = store_fabric_inspection.roll_plan_id
  INNER JOIN store_grn_detail ON store_grn_detail.grn_detail_id = store_roll_plan.grn_detail_id
  INNER JOIN item_master ON store_grn_detail.item_code=item_master.master_id
  INNER JOIN store_grn_header ON store_grn_header.grn_id = store_grn_detail.grn_id
  WHERE store_fabric_inspection.roll_plan_id=$fabricInspection->roll_plan_id");
              //update stock transaction table
            $st = new StockTransaction;
            $st->status = 'PASS';
            $st->doc_type = $transaction->trans_code;
            $st->doc_num = $rollplanDetail[0]->grn_id;
            $st->style_id = $rollplanDetail[0]->style_id;
            $st->main_store = $rollplanDetail[0]->main_store;
            $st->sub_store = $rollplanDetail[0]->sub_store;
            $st->item_code = $rollplanDetail[0]->item_code;
            $st->size = $rollplanDetail[0]->size;
            $st->color = $rollplanDetail[0]->color;
            $st->uom = $rollplanDetail[0]->uom;
            $st->customer_po_id=$rollplanDetail[0]->customer_po_id;
            $st->qty = $fabricInspection->$updated_st_qty;
            $st->location = auth()->payload()['loc_id'];
            $st->bin = $rollplanDetail[0]->bin;
            $st->created_by = auth()->payload()['user_id'];
            $st->shop_order_id=$rollplanDetail[0]->shop_order_id;
            $st->shop_order_detail_id=$rollplanDetail[0]->shop_order_detail_id;
            $st->standard_price = $rollplanDetail[0]->standard_price;
            $st->purchase_price = $rollplanDetail[0]->purchase_price;
            $st->direction="+";
            $st->save();
            $po_detail_id=$rollplanDetail[0]->po_details_id;
            $loc= auth()->payload()['loc_id'];
            $grnDetails=GrnDetail::find($data[$i]['grn_detail_id']);
            $grnDetails->grn_qty=$grnDetails->grn_qty+$fabricInspection->qty;
            $grnDetails->save();

            //*balance qty suould be get from shop order detail level*
            $balanceQty=DB::SELECT("SELECT min(bal_qty)
                          from store_grn_detail
                          where po_details_id=$po_detail_id");
            //find exact line of stock(old way)
            $cus_po=$rollplanDetail[0]->customer_po_id;
            $style_id=$rollplanDetail[0]->style_id;
            $item_code=$rollplanDetail[0]->item_code;
            $size=$rollplanDetail[0]->size;
          //  $size=1;
            $color=$rollplanDetail[0]->color;
            $main_store=$rollplanDetail[0]->main_store;
            $sub_store=$rollplanDetail[0]->sub_store;
            $bin=$rollplanDetail[0]->bin;
          /*  if($size==null){
              $size_serach=0;
            }
            else {
              $size_serach=$size;
            }*/
            //find exact line on stock table
            $findStoreStockLine=DB::SELECT ("SELECT * FROM store_stock
                                             where item_id=$st->item_code
                                             AND shop_order_id=$st->shop_order_id
                                             AND style_id=$st->style_id
                                             AND shop_order_detail_id=$st->shop_order_detail_id
                                             AND bin=$bin
                                             AND store=$main_store
                                             AND sub_store=$sub_store
                                             AND location=$st->location");
            //if related line is not available
          if($findStoreStockLine==null){
            //update the stock table
            $storeUpdate=new Stock();
            $storeUpdate->shop_order_id=$rollplanDetail[0]->shop_order_id;
            $storeUpdate->shop_order_detail_id=$rollplanDetail[0]->shop_order_detail_id;
            $storeUpdate->style_id = $rollplanDetail[0]->style_id;
            $storeUpdate->item_id= $rollplanDetail[0]->item_code;
            $storeUpdate->size = $rollplanDetail[0]->size;
            $storeUpdate->color = $rollplanDetail[0]->color;
            $storeUpdate->location = auth()->payload()['loc_id'];
            $storeUpdate->store = $rollplanDetail[0]->main_store;
            $storeUpdate->sub_store =$rollplanDetail[0]->sub_store;
            $storeUpdate->bin = $rollplanDetail[0]->bin;
            $storeUpdate->standard_price = $rollplanDetail[0]->standard_price;
            $storeUpdate->purchase_price = $rollplanDetail[0]->purchase_price;
            //check price variance
            if($storeUpdate->standard_price!=$storeUpdate->purchase_price){
              $priceVariance= new PriceVariance;
              $priceVariance->item_id=$rollplanDetail[0]->item_code;
              $priceVariance->standard_price=$rollplanDetail[0]->standard_price;
              $priceVariance->purchase_price =$rollplanDetail[0]->purchase_price;
              $priceVariance->shop_order_id =$rollplanDetail[0]->shop_order_id;
              $priceVariance->shop_order_detail_id =$rollplanDetail[0]->shop_order_detail_id;
              $priceVariance->status =1;
              //save price variance
              $priceVariance->save();
            }
            //if inventory uom varid
              if($rollplanDetail[0]->uom!=$rollplanDetail[0]->inventory_uom){
                $storeUpdate->uom = $rollplanDetail[0]->inventory_uom;
                $_uom_unit_code=UOM::where('uom_id','=',$rollplanDetail[0]->inventory_uom)->pluck('uom_code');
                $_uom_base_unit_code=UOM::where('uom_id','=',$rollplanDetail[0]->uom)->pluck('uom_code');
                $ConversionFactor=ConversionFactor::select('*')
                                                    ->where('unit_code','=',$_uom_unit_code[0])
                                                    ->where('base_unit','=',$_uom_base_unit_code[0])
                                                    ->first();

                                                    $storeUpdate->qty =(double)($fabricInspection->qty*$ConversionFactor->present_factor);
                                                    //$storeUpdate->total_qty = (double)($fabricInspection->qty*$ConversionFactor->present_factor);
                                                    //$storeUpdate->tolerance_qty = (double)($rollplanDetail[0]->maximum_tolarance*$ConversionFactor->present_factor);
            }
            //if inventory uom and purchase varid
            if($rollplanDetail[0]->uom==$rollplanDetail[0]->inventory_uom){
              $storeUpdate->uom = $rollplanDetail[0]->inventory_uom;
              $storeUpdate->qty =(double)($fabricInspection->qty);
              //$storeUpdate->total_qty = (double)($fabricInspection->qty);
              //$storeUpdate->tolerance_qty = $rollplanDetail[0]->maximum_tolarance;
            }


            //$storeUpdate->transfer_status="STOCKUPDATE";
            $storeUpdate->status=1;
            $shopOrder=ShopOrderDetail::find($rollplanDetail[0]->shop_order_detail_id);
            //if previous status pass
            if($data[$i]['previous_status_name']=="PASS"){
              $shopOrder->asign_qty=$fabricInspection->qty-(double)$data[$i]['previous_received_qty']+$shopOrder->asign_qty;
            }
            //if not
            else if ($data[$i]['previous_status_name']!="PASS"){

              $shopOrder->asign_qty=$fabricInspection->qty+$shopOrder->asign_qty;
            }



            $shopOrder->save();
            $storeUpdate->save();
          }

          //dd($findStoreStockLine);
          if($findStoreStockLine!=null){
              //dd($findStoreStockLine[0]->id);
              $stock=Stock::find($findStoreStockLine[0]->id);
              //if previous standerd price and new price is same
              if($stock->standard_price!=$rollplanDetail[0]->standard_price){
                $priceVariance= new PriceVariance;
                $priceVariance->item_id=$rollplanDetail[0]->item_code;
                $priceVariance->standard_price=$rollplanDetail[0]->standard_price;
                $priceVariance->purchase_price =$rollplanDetail[0]->purchase_price;
                $priceVariance->shop_order_id =$rollplanDetail[0]->shop_order_id;
                $priceVariance->shop_order_detail_id =$rollplanDetail[0]->shop_order_detail_id;
                $priceVariance->status =1;
                $priceVariance->save();
              }

              $stock->standard_price = $rollplanDetail[0]->standard_price;
              $stock->purchase_price = $rollplanDetail[0]->purchase_price;
              $shopOrder=ShopOrderDetail::find($rollplanDetail[0]->shop_order_detail_id);
              if($rollplanDetail[0]->uom!=$rollplanDetail[0]->inventory_uom){

                  //$stock->uom = $rollplanDetail[0]->inventory_uom;
                  $_uom_unit_code=UOM::where('uom_id','=',$rollplanDetail[0]->inventory_uom)->pluck('uom_code');
                  $_uom_base_unit_code=UOM::where('uom_id','=',$rollplanDetail[0]->uom)->pluck('uom_code');
                  $ConversionFactor=ConversionFactor::select('*')
                                                      ->where('unit_code','=',$_uom_unit_code[0])
                                                      ->where('base_unit','=',$_uom_base_unit_code[0])
                                                      ->first();
                                                      //dd((double)$stock->inv_qty-(double)$data[$i]['previous_received_qty']);
                                                      if($data[$i]['previous_status_name']=="PASS"){
                                                      $stock->qty =(double)$stock->qty-(double)$data[$i]['previous_received_qty']+(double)($fabricInspection->qty*$ConversionFactor->present_factor);
                                                      //$stock->total_qty = (double)$stock->total_qty-(double)$data[$i]['previous_received_qty']+(double)($fabricInspection->qty*$ConversionFactor->present_factor);
                                                      $shopOrder->asign_qty=$fabricInspection->qty-(double)$data[$i]['previous_received_qty']+$shopOrder->asign_qty;
                                                    }
                                                  else if ($data[$i]['previous_status_name']!="PASS"){

                                                      $stock->qty =(double)$stock->qty+(double)($fabricInspection->qty*$ConversionFactor->present_factor);
                                                      //$stock->total_qty = (double)$stock->total_qty+(double)($fabricInspection->qty*$ConversionFactor->present_factor);
                                                      $shopOrder->asign_qty=$fabricInspection->qty+$shopOrder->asign_qty;
                                                    }


                                                      //$stock->tolerance_qty = (double)($rollplanDetail[0]->maximum_tolarance*$ConversionFactor->present_factor);
                                                    //  if($i==1)


              }
              if($rollplanDetail[0]->uom==$rollplanDetail[0]->inventory_uom){
                //dd($fabricInspection->qty);
                if($data[$i]['previous_status_name']=="PASS"){
                $stock->qty = (double)$stock->qty-(double)$data[$i]['previous_received_qty']+(double)($fabricInspection->qty);
                //$stock->total_qty=(double)$stock->total_qty-(double)$data[$i]['previous_received_qty']+(double)($fabricInspection->qty);
                $shopOrder->asign_qty=$fabricInspection->qty-(double)$data[$i]['previous_received_qty']+$shopOrder->asign_qty;
              }
              else if ($data[$i]['previous_status_name']!="PASS"){
                $stock->qty = (double)$stock->inv_qty+(double)($fabricInspection->qty);
                //$stock->total_qty=(double)$stock->total_qty+(double)($fabricInspection->qty);
                $shopOrder->asign_qty=$fabricInspection->qty+$shopOrder->asign_qty;
              }
                //$stock->tolerance_qty = $rollplanDetail[0]->maximum_tolarance;


              }



              $shopOrder->save();
              //$stock->total_qty=$stock->total_qty+$fabricInspection->received_qty;
             //$stock->inv_qty = $stock->inv_qty+$fabricInspection->received_qty;
             $stock->save();
            }


        }


        }

        return response([ 'data' => [
          'message' => 'Fabric Inspection Updated  sucessfully',
          'status' => 1
          ]
        ], Response::HTTP_CREATED );
    }

}
