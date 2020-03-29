<?php
namespace App\Http\Controllers\Stores;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Database\Eloquent\ModelNotFoundException;

use App\Http\Controllers\Controller;

use App\Models\Store\Stock;
use App\Models\Store\SubStore;
use App\Models\stores\TransferLocationUpdate;
use App\Models\stores\GatePassHeader;
use App\Models\stores\GatePassDetails;
use App\Models\Store\StockTransaction;
use App\Models\Store\Store;
use App\Models\Store\StoreBin;
use App\Models\Org\UOM;
use App\Models\Stores\RollPlan;
use Illuminate\Support\Facades\DB;

/**
 *
 */
class MaterialTransferController extends Controller
{

  function __construct()
  {
    //add functions names to 'except' paramert to skip authentication
    $this->middleware('jwt.verify', ['except' => ['index','getStores']]);
  }


  public function index(Request $request)
  {

    $type = $request->type;

    if($type == 'datatable')   {
      $data = $request->all();
      return response($this->datatable_search($data));
    }
    else if($type == 'auto')    {
      $search = $request->search;
      //dd($search);
      return response($this->gate_pass_autocomplete_search($search));
    }
    else if ($type == 'getStores'){
      $search = $request->query;
      $location=$request->location;
      //dd($request->token);
      return response($this->getStores($search,$location));
        //echo"im here in get Stores";
    }
    else if($type=='getSubStores'){
      $search=$request->query;
      $store=$request->store;
      return response($this->getSubStores($search,$store));
    }
    else if($type=='getBins'){
      $search=$request->query;
      $store=$request->store;
      $subStore=$request->substore;
      return response($this->getBins($search,$store,$subStore));
    }

    else if($type=='loadDetails'){
      $gatepassNo=$request->gatePassNo;
      return response(['data'=>$this->tabaleLoad($gatepassNo)]);
    }

      else{
      $active = $request->active;
      $fields = $request->fields;
      return response([
        'data' => $this->list($active , $fields)
      ]);
    }
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



    $gatePassDetails_list= GatePassHeader::join('org_company as t', 't.company_id', '=', 'store_gate_pass_header.transfer_location')
    ->join('org_company as r', 'r.company_id', '=', 'store_gate_pass_header.receiver_location')
    ->join('usr_login','store_gate_pass_header.updated_by','=','usr_login.user_id')
    ->select('store_gate_pass_header.*','t.company_name as loc_transfer','r.company_name as loc_receiver','usr_login.user_name')
    ->where('gate_pass_no','like',$search.'%')
    ->orWhere('r.company_name', 'like', $search.'%')
    ->orWhere('t.company_name', 'like', $search.'%')
    ->orWhere('store_gate_pass_header.created_date', 'like', $search.'%')
    ->orderBy($order_column, $order_type)
    ->offset($start)->limit($length)->get();

     $gatePassDetails_list_count= GatePassHeader::join('org_company as t', 't.company_id', '=', 'store_gate_pass_header.transfer_location')
     ->join('org_company as r', 'r.company_id', '=', 'store_gate_pass_header.receiver_location')
     ->join('usr_login','store_gate_pass_header.updated_by','=','usr_login.user_id')
     ->select('store_gate_pass_header.*','t.company_name as loc_transfer','r.company_name as loc_receiver','usr_login.user_name')
     ->where('gate_pass_no','like',$search.'%')
     ->orWhere('r.company_name', 'like', $search.'%')
     ->orWhere('t.company_name', 'like', $search.'%')
     ->orWhere('store_gate_pass_header.created_date', 'like', $search.'%')
    ->count();
    return [
        "draw" => $draw,
        "recordsTotal" =>  $gatePassDetails_list_count,
        "recordsFiltered" => $gatePassDetails_list_count,
        "data" =>$gatePassDetails_list
    ];


  }
  private function gate_pass_autocomplete_search($search){

    $active="PLANED";
    $gate_pass_list = GatePassHeader::select('gate_pass_id','gate_pass_no')
    ->where([['gate_pass_no', 'like', '%' . $search . '%'],])
    ->where('status','=',$active)
    ->get();
    return $gate_pass_list;
  }

  private function tabaleLoad($gatepassNo){
//dd($gatepassNo);
$status="PLANED";
$user = auth()->payload();
$user_location=$user['loc_id'];

                $detailsTrimPacking=GatePassHeader::join('store_gate_pass_details','store_gate_pass_header.gate_pass_id','=','store_gate_pass_details.gate_pass_id')
                                    ->join('merc_shop_order_header','store_gate_pass_details.shop_order_id','=','merc_shop_order_header.shop_order_id')
                                    ->join('merc_shop_order_detail','store_gate_pass_details.shop_order_detail_id','=','merc_shop_order_detail.shop_order_detail_id')
                                    ->join('merc_shop_order_delivery','merc_shop_order_header.shop_order_id','=','merc_shop_order_delivery.shop_order_id')
                                    ->join('merc_customer_order_details','merc_shop_order_delivery.delivery_id','=','merc_customer_order_details.details_id')
                                    ->join('style_creation','store_gate_pass_details.style_id','=','style_creation.style_id')
                                    ->join('item_master','item_master.master_id','=','store_gate_pass_details.item_id')
                                    ->leftjoin('store_stock','item_master.master_id','=','store_stock.item_id')
                                    ->join('store_trim_packing_detail','store_gate_pass_details.detail_level_id','=','store_trim_packing_detail.trim_packing_id')
                                    ->leftJoin('org_color','org_color.color_id','=','store_gate_pass_details.color_id')
                                    ->leftJoin('org_size','org_size.size_id','=','store_gate_pass_details.size_id')
                                    ->leftJoin('org_store_bin','org_store_bin.store_bin_id','=','store_gate_pass_details.bin_id')
                                    ->leftJoin('org_uom','org_uom.uom_id','=','store_gate_pass_details.uom_id')
                                     ->select('item_master.master_code','item_master.inventory_uom','item_master.master_id','item_master.master_description','style_creation.style_no','style_creation.style_id','store_gate_pass_details.trns_qty','org_color.color_name','org_color.color_id','org_size.size_name','org_size.size_id','org_uom.uom_code','org_uom.uom_id','store_gate_pass_header.gate_pass_id','store_gate_pass_details.item_id','store_gate_pass_details.shop_order_id','store_gate_pass_details.shop_order_detail_id','store_trim_packing_detail.shade','store_gate_pass_details.detail_level_id','store_trim_packing_detail.width','store_trim_packing_detail.batch_no','store_trim_packing_detail.lot_no','merc_shop_order_detail.purchase_price','merc_customer_order_details.details_id as cus_po_details_id','merc_shop_order_detail.purchase_price','item_master.standard_price')
                                      /*DB::raw("sum(qty)ppp from store_stock where store_stock.location=$user_location")*/
                                  //  ->where('store_gate_pass_header.status','=',$status)
                                    ->where('store_gate_pass_header.gate_pass_id','=',$gatepassNo)
                                    ->where('store_gate_pass_header.status','=',$status);

                                        $detailsRollPlan=GatePassHeader::join('store_gate_pass_details','store_gate_pass_header.gate_pass_id','=','store_gate_pass_details.gate_pass_id')
                                                        ->join('merc_shop_order_header','store_gate_pass_details.shop_order_id','=','merc_shop_order_header.shop_order_id')
                                                        ->join('merc_shop_order_detail','store_gate_pass_details.shop_order_detail_id','=','merc_shop_order_detail.shop_order_detail_id')
                                                        ->join('merc_shop_order_delivery','merc_shop_order_header.shop_order_id','=','merc_shop_order_delivery.shop_order_id')
                                                        ->join('merc_customer_order_details','merc_shop_order_delivery.delivery_id','=','merc_customer_order_details.details_id')
                                                        ->join('style_creation','store_gate_pass_details.style_id','=','style_creation.style_id')
                                                        ->join('item_master','item_master.master_id','=','store_gate_pass_details.item_id')
                                                        ->join('store_roll_plan','store_gate_pass_details.detail_level_id','=','store_roll_plan.roll_plan_id')
                                                        ->leftjoin('store_stock','item_master.master_id','=','store_stock.item_id')
                                                        ->leftJoin('org_color','org_color.color_id','=','store_gate_pass_details.color_id')
                                                        ->leftJoin('org_size','org_size.size_id','=','store_gate_pass_details.size_id')
                                                        ->leftJoin('org_store_bin','org_store_bin.store_bin_id','=','store_gate_pass_details.bin_id')
                                                        ->leftJoin('org_uom','org_uom.uom_id','=','store_gate_pass_details.uom_id')
                                                         ->select('item_master.master_code','item_master.inventory_uom','item_master.master_id','item_master.master_description','style_creation.style_no','style_creation.style_id','store_gate_pass_details.trns_qty','org_color.color_name','org_color.color_id','org_size.size_name','org_size.size_id','org_uom.uom_code','org_uom.uom_id','store_gate_pass_header.gate_pass_id','store_gate_pass_details.item_id','store_gate_pass_details.shop_order_id','store_gate_pass_details.shop_order_detail_id','store_roll_plan.shade','store_gate_pass_details.detail_level_id','store_roll_plan.width','store_roll_plan.batch_no','store_roll_plan.lot_no' ,'merc_shop_order_detail.purchase_price','merc_customer_order_details.details_id as cus_po_details_id','merc_shop_order_detail.purchase_price','item_master.standard_price')
                                                        /*DB::raw("sum(qty)ppp from store_stock where store_stock.location=$user_location")*/

                                                        //->where('store_gate_pass_header.status','=',$status)
                                                        ->where('store_gate_pass_header.gate_pass_id','=',$gatepassNo)
                                                        ->where('store_gate_pass_header.status','=',$status)
                                    ->union($detailsTrimPacking)
                    //
                    //->where('store_stock.status','=',1)
                    //echo $details->toSql();

   /*$stockBalanceInLoction=DB::table('store_stock')
                          ->rightJoinSub($detailsRollPlan,'store_gate_pass_details',function($join){
                           $user = auth()->payload();
                           $user_location=$user['loc_id'];
                           //user location hardcode since db dont have real values
                        //$user_location=3;
                         $join->on('store_stock.item_id','=','store_gate_pass_details.item_id')
                             ->on('store_stock.style_id','=','store_gate_pass_details.style_id')
                             //->on('store_stock.size','=','gatepass_details.size_id')
                             ->on('store_stock.shop_order_id','=','store_gate_pass_details.shop_order_id')
                             ->on('store_stock.shop_order_detail_id','=','store_gate_pass_details.shop_order_detail_id')
                             //->on('store_stock.color','=','gatepass_details.color_id')
                             ->select(DB::raw('sum(store_stock.qty) qty'))

                             ->where('store_stock.location','=',$user_location);
                             //->sum('qty');

                        })*/
                    ->get();
                    return ['dataset'=>$detailsRollPlan,
                            'user_location'=>$user_location];
                 //$this->setStatuszero($details);


  }

  public function getStores($search,$location){
    //dd(ddadaa);
      //dd($user_location);
    //$user_location=auth()->payload()['loc_id'];
    //dd($user_location);
   $store_list = Store::where('status',1)
                      ->where('loc_id',$location)
                      //->where('status',1)
                    //  ->where('store_name', 'like', '%' . $search . '%')
                      ->pluck('store_name')
                      ->toArray();
            return json_encode($store_list);
      //return $store_list;
  }

  public function getSubStores($search,$store){
    $store=Store::where('store_name','=',$store)->first();
    //dd($storeId);
    $sub_store_list=SubStore::where('status',1)
                            ->where('store_id',$store->store_id)
                          ->pluck('substore_name')
                          ->toArray();
                        return json_encode($sub_store_list);

  }

  public function getBins($search,$store,$subStore){
  $Store=Store::where('store_name','=',$store)->first();
  $subStore=SubStore::Where('substore_name','=',$subStore)->first();
    //$user_location=$user['loc_id'];
    //$user_location=3;
    $store_bin_list=StoreBin::where('status',1)
                           ->where('store_id',$Store->store_id)
                           ->where('substore_id',$subStore->substore_id)
                          ->pluck('store_bin_name')
                          ->toArray();
                        return json_encode($store_bin_list);

  }



public function storedetails (Request $request){
  $user = auth()->payload();
  $transer_location=$user['loc_id'];
  $receiver_location=$request->receiver_location;
  $gate_pass_id=$request->gate_pass_id;
  //dd($request);
  $details= $request->data;
    //print_r($details);
      //print_r($gate_pass_id);*/
    for($i=0;$i<count($details);$i++){
        //save data in stock transaction table
        //get store i related to store name
         $storeName=$details[$i]["store_name"];
          $subStoreName=$details[$i]["substore_name"];
          $subStoreBin=$details[$i]["store_bin_name"];
            //$storeName="test1";
        $getStoreId=Store::where('store_name','=',$storeName)
                          ->where('loc_id','=',$transer_location)
                          ->where('status','=',1)
                          ->pluck('store_id');
                          ///$getStoreId[0];
                          //die();
                          //return $getStoreId;

        $getSubStoreId=SubStore::where('substore_name','=',$subStoreName)
                                ->where('store_id','=',$getStoreId)
                                ->where('status','=',1)
                                ->pluck('substore_id');
                                  //echo $getSubStoreId;

        $getBinId=StoreBin::where('store_id','=',$getStoreId)
                          ->where('substore_id','=',$getSubStoreId)
                          ->where('store_bin_name','=',$subStoreBin)
                          ->where('status','=',1)
                          ->pluck('store_bin_id');
                            //echo $getBinId;
           $stockTransaction=new StockTransaction();
          //convert qty in to inventory uom
          if($details[$i]['inventory_uom']!=$details[$i]['uom_id']){
            $_uom_unit_code=UOM::where('uom_id','=',$dataset[$i]['inventory_uom'])->pluck('uom_code');
            $_uom_base_unit_code=UOM::where('uom_id','=',$dataset[$i]['uom_id'])->pluck('uom_code');
            //get convertion equatiojn details
            //dd($_uom_unit_code);
            $ConversionFactor=ConversionFactor::select('*')
                                                ->where('unit_code','=',$_uom_unit_code[0])
                                                ->where('base_unit','=',$_uom_base_unit_code[0])
                                                ->first();
          $stockTransaction->qty = (double)( $details[$i]["received_qty"]*$ConversionFactor->present_factor);
          }
          else if($details[$i]['inventory_uom']==$details[$i]['uom_id']){
            $stockTransaction->qty= $details[$i]["received_qty"];
          }
      $stockTransaction->doc_num=$gate_pass_id;
      $stockTransaction->doc_type="GATE_PASS";
      $stockTransaction->style_id=$details[$i]["style_id"];
      $stockTransaction->size=$details[$i]["size_id"];
      $stockTransaction->shop_order_id=$details[$i]["shop_order_id"];
      $stockTransaction->shop_order_detail_id=$details[$i]['shop_order_detail_id'];
      $stockTransaction->customer_po_id=$details[$i]['cus_po_details_id'];
      $stockTransaction->purchase_price=$details[$i]['purchase_price'];
      $stockTransaction->standard_price=$details[$i]['standard_price'];
      $stockTransaction->item_code=$details[$i]["item_id"];
      $stockTransaction->color=$details[$i]["color_id"];
      $stockTransaction->main_store=$getStoreId[0];
      $stockTransaction->sub_store=$getSubStoreId[0];
      $stockTransaction->bin=$getBinId[0];
      $stockTransaction->uom=$details[$i]["inventory_uom"];
      //$stockTransaction->material_code=$details[$i]["master_id"];
      $stockTransaction->location=$transer_location;
      $stockTransaction->status="RECEIVED";
      $stockTransaction->location = auth()->payload()['loc_id'];
      $stockTransaction->created_by = auth()->payload()['user_id'];
      $stockTransaction->direction="+";
      $stockTransaction->save();
      //update gatepass header table as RECEIVED
      $gatePassHeader= GatePassHeader::find($gate_pass_id);
      $gatePassHeader->status="RECEIVED";
      $gatePassHeader->save();

      $findRollPlanLine=RollPlan::find($details[$i]['detail_level_id']);

      $newRollPlanLine=new RollPlan();
      $newRollPlanLine->invoice_no=$findRollPlanLine->invoice_no;
      $newRollPlanLine->grn_detail_id=$findRollPlanLine->grn_detail_id;
      $newRollPlanLine->lot_no=$findRollPlanLine->lot_no;
      $newRollPlanLine->batch_no=$findRollPlanLine->batch_no;
      //get max roll no
      $max_roll_no=RollPlan::where('grn_detail_id','=',$findRollPlanLine->grn_detail_id)->max('roll_no');
      //dd($max_roll_no);
      $newRollPlanLine->roll_no=$max_roll_no++;
      $newRollPlanLine->qty=$stockTransaction->qty;
      $newRollPlanLine->received_qty=$stockTransaction->qty;
      $newRollPlanLine->bin=$findRollPlanLine->bin;
      ///dd($binID);
      $newRollPlanLine->width=$findRollPlanLine->width;
      $newRollPlanLine->shade=$findRollPlanLine->shade;
      $newRollPlanLine->comment=$findRollPlanLine->comment;
      //$rollPlan->barcode=$data->barcode;
      $newRollPlanLine->invoice_no=$request->invoiceNo;
      $newRollPlanLine->grn_detail_id=$request->grn_detail_id;
      $newRollPlanLine->status = 1;
      $newRollPlanLine->save();

      //check current style,current size avalabale in db
      $findStoreStockLine=DB::SELECT ("SELECT * FROM store_stock
                                                                 where item_id=$stockTransaction->item_code
                                                                 AND shop_order_id=$stockTransaction->shop_order_id
                                                                 AND style_id=$stockTransaction->style_id
                                                                 AND shop_order_detail_id=$stockTransaction->shop_order_detail_id
                                                                 AND bin=$stockTransaction->bin
                                                                 AND store=$stockTransaction->main_store
                                                                 AND sub_store=$stockTransaction->sub_store
                                                                 AND location=$stockTransaction->location");
        if($findStoreStockLine!=null){
          $stockUpdate= Stock::find($findStoreStockLine[0]->id);
          $stockUpdate->qty=$stockUpdate->qty+$stockTransaction->qty;
          $stockUpdate->save();
        }
        else if($findStoreStockLine==null){
        //enter the details of gate pass to the stock table
      $stockUpdate=new Stock();
      $stockUpdate->shop_order_id=$details[$i]["shop_order_id"];
      $stockUpdate->shop_order_detail_id=$details[$i]['shop_order_detail_id'];
      $stockUpdate->style_id=$details[$i]["style_id"];
      $stockUpdate->item_id=$details[$i]["item_id"];
      $stockUpdate->size=$details[$i]["size_id"];
      $stockUpdate->color=$details[$i]["color_id"];
      $stockUpdate->location=$transer_location;
      $stockUpdate->store=$getStoreId[0];
      $stockUpdate->sub_store=$getSubStoreId[0];
      $stockUpdate->bin=$stockTransaction->bin;
      $stockUpdate->uom=$details[$i]["inventory_uom"];
      //$stockUpdate->material_code=$details[$i]["master_id"];
      $stockUpdate->qty=$stockTransaction->qty;
      $stockUpdate->purchase_price=$details[$i]['purchase_price'];
      $stockUpdate->standard_price=$details[$i]['standard_price'];
      $stockUpdate->status=1;
      $stockUpdate->save();
      //$stockTransaction->doc_num=$gate_pass_id;
      //$stockTransaction->doc_type="GATE_PASS";

      //$stockTransaction->status="RECEIVED";
      //$stockTransaction->qty= $details[$i]["received_qty"];
      //$stockTransaction->save();


 }


}


   return response(['data'=>[
     'message'=>'Items Transferd in Successfully',

   ]

    ]

);



}



}
