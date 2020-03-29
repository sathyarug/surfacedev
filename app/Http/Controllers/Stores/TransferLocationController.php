<?php
namespace App\Http\Controllers\Stores;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Database\Eloquent\ModelNotFoundException;

use App\Http\Controllers\Controller;
use App\Models\Merchandising\CustomerOrder;
use App\Models\Merchandising\CustomerOrderDetails;
use App\Models\Merchandising\StyleCreation;
use App\Models\Finance\Item\SubCategory;
use App\Models\Merchandising\Item\Item;
use App\Models\Org\ConversionFactor;
use App\Models\stores\RollPlan;
use App\Models\Store\Stock;
use App\Models\stores\TransferLocationUpdate;
use App\models\stores\GatePassHeader;
use App\models\stores\GatePassDetails;
use App\models\Store\StockTransaction;
use App\Models\Store\GrnHeader;
use App\Models\Merchandising\ShopOrderHeader;
use App\Models\Merchandising\ShopOrderDetail;
use App\Models\Store\GrnDetail;
use App\Models\Org\UOM;
use Illuminate\Support\Facades\DB;
use App\Libraries\UniqueIdGenerator;
use App\Libraries\Approval;
 class TransferLocationController extends Controller{



   public function __construct()
   {
     //add functions names to 'except' paramert to skip authentication
     $this->middleware('jwt.verify', ['except' => ['index']]);
   }


   //get customer size list
   public function index(Request $request)
   {
     $type = $request->type;

     if($type == 'style')   {
       $searchFrom = $request->searchFrom;
       $searchTo=$request->searchTo;
       return response($this->styleFromSearch($searchFrom, $searchTo));
     }
/*     else if($type=='saveDetails'){
       $details=$request->details;
       print_r($details);


     }*/
    else if($type=='loadDetails'){
       $style=$request->searchFrom;
       //print_r($request->searchFrom);
       return response(['data'=>$this->tabaleLoad($style)]);

     }


     else if ($type == 'auto')    {
       $search = $request->search;
       return response($this->autocomplete_search($search));
     }

   else{
       $active = $request->active;
       $fields = $request->fields;
       return null;
     }
   }


    private function styleFromSearch($searchFrom, $searchTo){

   $stylefrom=ShopOrderHeader::select('style_creation.*')
                            ->join('merc_shop_order_detail','merc_shop_order_header.shop_order_id','=','merc_shop_order_detail.shop_order_id')
                            ->join('bom_header','merc_shop_order_detail.bom_id','=','bom_header.bom_id')
                           ->join('costing','merc_shop_order_detail.costing_id','=','costing.id')
                          ->join('style_creation','costing.style_id','=','style_creation.style_id')
                          ->select('style_creation.style_no')
                          ->where('merc_shop_order_detail.shop_order_id','=',$searchFrom)
                          ->where('style_creation.status','=',1)
                          ->first();
  $styleTo=ShopOrderHeader::select('style_creation.*')
                           ->join('merc_shop_order_detail','merc_shop_order_header.shop_order_id','=','merc_shop_order_detail.shop_order_id')
                          ->join('bom_header','merc_shop_order_detail.bom_id','=','bom_header.bom_id')
                          ->join('costing','merc_shop_order_detail.costing_id','=','costing.id')
                          ->join('style_creation','costing.style_id','=','style_creation.style_id')
                         ->select('style_creation.style_no')
                         ->where('merc_shop_order_detail.shop_order_id','=',$searchTo)
                         ->where('style_creation.status','=',1)
                         ->first();
                //dd($stylefrom);
            if($stylefrom!=$styleTo){
              return [
                "styleFrom"=>$stylefrom,
                'message'=>"Diffrent Styles",
                'status'=>0

                ];
              }
              if($stylefrom==$styleTo){
                return [
                  "styleFrom"=>$stylefrom,
                  'message'=>"Same Style",
                  'status'=>1

                  ];
                }





                            }


                      private function tabaleLoad($style){

                        $user = auth()->payload();
                        $user_location=$user['loc_id'];



                                        $detailsTrimPacking=GrnHeader::join('store_grn_detail','store_grn_header.grn_id','=','store_grn_detail.grn_id')
                                                       ->Join('style_creation','store_grn_detail.style_id','=','style_creation.style_id')
                                                       ->Join('store_trim_packing_detail','store_grn_detail.grn_detail_id','=','store_trim_packing_detail.grn_detail_id')
                                                      ->join('item_master','store_grn_detail.item_code','=','item_master.master_id')
                                                      ->join('org_store_bin','store_trim_packing_detail.bin','=','org_store_bin.store_bin_id')
                                                      ->select('store_trim_packing_detail.*','item_master.master_code','org_store_bin.store_bin_name','store_grn_header.main_store','store_grn_header.sub_store','store_grn_detail.style_id','store_grn_detail.size','store_grn_detail.uom','store_grn_detail.color','store_grn_detail.shop_order_id','store_grn_detail.shop_order_detail_id','store_grn_detail.item_code','store_grn_detail.customer_po_id','item_master.inventory_uom')
                                                      ->where('style_creation.style_no','=',$style)
                                                      ->where('store_trim_packing_detail.qty','>',0)
                                                      ->where('store_trim_packing_detail.user_loc_id','=',$user_location);

                                                  $detailsRollPlan=GrnHeader::join('store_grn_detail','store_grn_header.grn_id','=','store_grn_detail.grn_id')
                                                                ->join('style_creation','store_grn_detail.style_id','=','style_creation.style_id')
                                                                ->join('store_roll_plan','store_grn_detail.grn_detail_id','=','store_roll_plan.grn_detail_id')
                                                                ->join('store_fabric_inspection','store_roll_plan.roll_plan_id','=','store_fabric_inspection.roll_plan_id')
                                                                ->join('item_master','store_grn_detail.item_code','=','item_master.master_id')
                                                                ->join('org_store_bin','store_roll_plan.bin','=','org_store_bin.store_bin_id')
                                                                ->select('store_roll_plan.*','item_master.master_code','org_store_bin.store_bin_name','store_grn_header.main_store','store_grn_header.sub_store','store_grn_detail.style_id','store_grn_detail.size','store_grn_detail.uom','store_grn_detail.color','store_grn_detail.shop_order_id','store_grn_detail.shop_order_detail_id','store_grn_detail.item_code','store_grn_detail.customer_po_id','item_master.inventory_uom')
                                                                ->where('style_creation.style_no','=',$style)
                                                                ->where('store_roll_plan.qty','>',0)
                                                                ->where('store_roll_plan.user_loc_id','=',$user_location)
                                                                ->where('store_fabric_inspection.inspection_status','=','PASS')
                                                                ->union($detailsTrimPacking)
                                                               ->get();

                                                        return $detailsRollPlan;

                      }

                      private function setStatuszero($details){
                        for($i=0;$i<count($details);$i++){
                          $id=$details[$i]["id"];
                          //$setStatusZero=TransferLocationUpdate::find($id);
                          $setStatusZero->status=0;
                          $setStatusZero->save();


                        }



                      }

                      public function send_to_approval(Request $request) {
                        $gate_pass_id=$request->formData['gate_pass_id'];
                        //dd($gate_pass_id);
                        $approval = new Approval();
                        $user_id=auth()->payload()['user_id'];
                        $approval->start('GATE_PASS',$gate_pass_id,$user_id);//start costing approval process

                        return response([
                          'data' => [
                            'status' => 'success',
                            'message' => 'Gate Pass sent for approval',
                            'costing' => $request
                          ]
                        ]);

                      }
                      public function storedetails (Request $request){
                        $user = auth()->payload();
                        $transer_location=$user['loc_id'];
                        $receiver_location=$request->receiver_location;
                        //print_r($receiver_location);
                          $id;
                          $qty;
                        $details= $request->data;
                       for($i=0;$i<count($details);$i++){
                         if(empty($details[$i]['isEdited'])==false&&$details[$i]['isEdited']==1){
                              $status="";
                              ////$id=$details[$i]["id"];
                              //

                              $stockUpdateDetails= TransferLocationUpdate::select('*')
                              ->where('style_id','=',$details[$i]['style_id'])
                              ->where('item_id','=',$details[$i]['item_code'])
                              ->where('store','=',$details[$i]['main_store'])
                              ->where('style_id','=',$details[$i]['style_id'])
                              ->where('shop_order_id','=',$details[$i]['shop_order_id'])
                              ->where('shop_order_detail_id','=',$details[$i]['shop_order_detail_id'])
                              ->where('sub_store','=',$details[$i]['sub_store'])
                              ->where('bin','=',$details[$i]['bin'])
                              ->where('location','=',$transer_location)
                              ->first();

                              /*$findStoreStockLine=DB::SELECT ("SELECT * FROM store_stock
                                                               where item_id= $poDetails->item_code
                                                               AND shop_order_id=$st->shop_order_id
                                                               AND style_id=$poDetails->style
                                                               AND shop_order_detail_id=$st->shop_order_detail_id
                                                               AND bin=$bin->store_bin_id
                                                               AND store=$store->store_id
                                                               AND sub_store=$store->substore_id
                                                               AND location=$st->location");*/



                              //dd((double)$details[$i]['trans_qty']);
                              $itemType=Item::join('item_category','item_master.category_id','=','item_category.category_id')
                                             ->select('item_category.category_code')
                                             ->where('item_master.master_id','=',$details[$i]['item_code'])
                                             ->first();
                                               if($itemType->category_code=="FAB"){
                                                 $rollPlan=RollPlan::find($details[$i]['roll_plan_id']);
                                                 $rollPlan->qty=$rollPlan->qty-(double)$details[$i]['trans_qty'];
                                                 $rollPlan->save();
                                                 /*$updateTrasferedRollplan= new RollPlan();
                                                 $updateTrasferedRollplan->invoice_no=$rollPlan->invoice_no;
                                                 $updateTrasferedRollplan->lot_no=$rollPlan->lot_no;
                                                 $updateTrasferedRollplan->batch_no=$rollPlan->batch_no;
                                                 $updateTrasferedRollplan->roll_no=(double)$details[$i]['trans_qty'];
                                                 $updateTrasferedRollplan->received_qty=(double)$details[$i]['trans_qty'];

                                                 */


                                               }
                                                 else if($itemType->category_code=!"FAB") {
                                                  $trimPacking=TrimPacking::find($details[$i]['roll_plan_id']);
                                                  $trimPacking->qty=$trimPacking->qty-(double)$details[$i]['trans_qty'];
                                                  $trimPacking->save();

                                                 }

                                                 if($details[$i]['uom']!=$details[$i]['inventory_uom']){
                                                   //$storeUpdate->uom = $rec['inventory_uom'];
                                                   $_uom_unit_code=UOM::where('uom_id','=',$details[$i]['inventory_uom'])->pluck('uom_code');
                                                   $_uom_base_unit_code=UOM::where('uom_id','=',$details[$i]['uom'])->pluck('uom_code');
                                                   //get convertion equatiojn details
                                                   //dd($_uom_unit_code);
                                                   $ConversionFactor=ConversionFactor::select('*')
                                                                                       ->where('unit_code','=',$_uom_unit_code[0])
                                                                                       ->where('base_unit','=',$_uom_base_unit_code[0])
                                                                                       ->first();
                                                 //dd($ConversionFactor);
                                                                                         // convert values according to the convertion rate
                                                                                     $stockUpdateDetails->qty =$stockUpdateDetails->qty-(double)( $details[$i]['trans_qty']*$ConversionFactor->present_factor);

                                               }
                                               //if inventory uom and purchase uom are the same
                                               if($details[$i]['uom']==$details[$i]['inventory_uom']){

                                              $stockUpdateDetails->qty=$stockUpdateDetails->qty-$details[$i]['trans_qty'];
                                               }




                           //$stockUpdateDetails->status=1;
                            $stockUpdateDetails->save();
                          }
                          }
                            $unId = UniqueIdGenerator::generateUniqueId('GATE_PASS', auth()->payload()['loc_id']);
                            //dd($unId);
                            $gatePassHeader=new GatePassHeader();
                            $gatePassHeader->gate_pass_no=$unId;
                            //dd($gatePassHeader->gate_pass_no);
                            $gatePassHeader->transfer_location=$transer_location;
                            $gatePassHeader->receiver_location=$receiver_location;
                            $gatePassHeader->status="PLANED";
                            $gatePassHeader->save();
                            //dd($gatePassHeader);
                            $gate_pass_id=$gatePassHeader->gate_pass_id;
                            //print_r($gate_pass_id);*/
                            for($i=0;$i<count($details);$i++){
                            if(empty($details[$i]['isEdited'])==false&&$details[$i]['isEdited']==1){
                              //dd($details[$i]["trans_qty"]);
                            $itemType=Item::join('item_category','item_master.category_id','=','item_category.category_id')
                                           ->select('item_category.category_code')
                                           ->where('item_master.master_id','=',$details[$i]['item_code'])
                                           ->first();
                            $gatePassDetails= new GatePassDetails();
                            $stockTransaction=new StockTransaction();
                            //if($stockUpdateDetails->transfer_status=="transfer"){
                            $gatePassDetails->gate_pass_id=$gate_pass_id;
                            $gatePassDetails->size_id=$details[$i]['size'];
                            $gatePassDetails->shop_order_id=$details[$i]['shop_order_id'];
                            $gatePassDetails->shop_order_detail_id=$details[$i]['shop_order_detail_id'];
                            $gatePassDetails->style_id=$details[$i]['style_id'];
                            $gatePassDetails->item_id=$details[$i]['item_code'];
                            $gatePassDetails->color_id=$details[$i]['color'];
                            $gatePassDetails->store_id=$details[$i]['main_store'];
                            $gatePassDetails->sub_store_id=$details[$i]['sub_store'];
                            $gatePassDetails->bin_id=$details[$i]['bin'];
                            $gatePassDetails->uom_id=$details[$i]['uom'];
                            $gatePassDetails->detail_level_id=$details[$i]['roll_plan_id'];
                            //$gatePassDetails->material_code_id=$stockUpdateDetails->material_code;
                            $qty=$details[$i]["trans_qty"];
                            $gatePassDetails->trns_qty=$qty;
                            $gatePassDetails->save();
                            $stockTransaction->doc_num=$gate_pass_id;
                            $stockTransaction->doc_type="GATE_PASS";
                            $stockTransaction->style_id=$details[$i]['style_id'];
                            $stockTransaction->size=$details[$i]['size'];
                            $stockTransaction->customer_po_id=$details[$i]['customer_po_id'];
                            $stockTransaction->item_id=$details[$i]['item_code'];
                            $stockTransaction->color=$details[$i]['color'];
                            $stockTransaction->main_store=$details[$i]['main_store'];
                            $stockTransaction->sub_store=$details[$i]['sub_store'];
                            $stockTransaction->bin=$details[$i]['bin'];
                            $stockTransaction->uom=$details[$i]['uom'];
                            $stockTransaction->shop_order_id=$details[$i]['shop_order_id'];
                            $stockTransaction->shop_order_detail_id=$details[$i]['shop_order_detail_id'];
                            $stockTransaction->direction="";
                            $stockTransaction->location=$transer_location;
                            $stockTransaction->status="PLANED";
                            $stockTransaction->qty= $qty;
                            $stockTransaction->created_by = auth()->payload()['user_id'];
                            $stockTransaction->save();
                          }
                          //}
                            }


                           return response(['data'=>[
                           'message'=>'Items Transferd Successfully',
                            'gate_pass_id'=>$gatePassHeader->gate_pass_id,
                         ]

                          ]

                     );




                    }



}
