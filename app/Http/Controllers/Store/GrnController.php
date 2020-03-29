<?php

namespace App\Http\Controllers\Store;

use App\Libraries\UniqueIdGenerator;
use App\Models\Store\StoreBin;
use App\Models\Org\SupplierTolarance;
use App\Models\Store\Stock;
use App\Models\Store\StockTransaction;
use App\Models\Store\SubStore;
use App\Models\Finance\Transaction;
use App\Models\Store\Store;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Http\Controllers\Controller;
use App\Models\Store\GrnHeader;
use App\Models\Store\GrnDetail;
use App\Models\Merchandising\PoOrderHeader;
use App\Models\Merchandising\PoOrderDetails;
use App\Models\Finance\PriceVariance;
use App\Models\Org\ConversionFactor;
use App\Models\Merchandising\ShopOrderHeader;
use App\Models\Merchandising\ShopOrderDetail;
use App\Models\Org\UOM;
use App\Models\Merchandising\Item\Item;
use Illuminate\Support\Facades\DB;
use App\Libraries\AppAuthorize;
class GrnController extends Controller
{

    public function __construct()
    {
      //add functions names to 'except' paramert to skip authentication
      $this->middleware('jwt.verify', ['except' => ['index']]);
    }
    public function grnDetails() {
        return view('grn.grn_details');

    }

    public function index(Request $request){
        $type = $request->type;
       // $fields = $request->fields;
       // $active = $request->status;
        if($type == 'datatable') {
            $data = $request->all();
            return response($this->datatable_search($data));
        }else if($type == 'auto')    {
            $search = $request->search;
            return response($this->autocomplete_search($search));
        }

    }

    private function autocomplete_search($search)
    {
      $lists = GrnHeader::select('*')
      ->where([['grn_number', 'like', '%' . $search . '%'],]) ->get();
      return $lists;
    }

    public function store(Request $request){
           // dd($request);
            $y=0;
             if(empty($request['grn_id'])) {

                 //Update GRN Header
                  $header = new GrnHeader;
                  $locId=auth()->payload()['loc_id'];
                  $unId = UniqueIdGenerator::generateUniqueId('ARRIVAL', auth()->payload()['loc_id']);
                  $header->grn_number = $unId;
                  $header->po_number = $request->header['po_id'];

            }else{
                 $header = GrnHeader::find($request['grn_id']);
                 $header->updated_by = auth()->payload()['user_id'];

                 // Remove all added grn details
                 GrnDetail::where('grn_id', $request['grn_id'])->delete();
            }
            //Get Main store
            $store = SubStore::find($request->header['substore_id']);

            $header->batch_no = $request->header['batch_no'];
            $header->inv_number = $request->header['invoice_no'];
            $header->note = $request->header['note'];
            $header->location = auth()->payload()['loc_id'];
            $header->main_store = $store->store_id;
            $header->sub_store = $store->substore_id;
            $header->sup_id=$request->header['sup_id'];
            $header->status=1;
            $header->created_by = auth()->payload()['user_id'];

            $header->save();

             $i = 1;

             //$valTol = $this->validateSupplierTolerance($request['dataset'], $request->header['sup_id']);

             //for tempary
             $valTol=true;
             //dd($request['dataset'] );
             foreach ($request['dataset'] as $rec){

                 if($valTol) {

                     $poDetails = PoOrderDetails::find($rec['id']);

                     $grnDetails = new GrnDetail;
                     $grnDetails->grn_id = $header->grn_id;
                     $grnDetails->po_number=$request->header['po_id'];
                     $grnDetails->grn_line_no = $i;
                     $grnDetails->style_id = $poDetails->style;
                     $grnDetails->po_details_id=$rec['id'];
                     $grnDetails->combine_id = $poDetails->comb_id;
                     $grnDetails->color = $poDetails->colour;
                     $grnDetails->size = $poDetails->size;
                     $grnDetails->uom = $poDetails->uom;
                     $grnDetails->po_qty = (double)$poDetails->req_qty;
                     $grnDetails->grn_qty = $rec['qty'];
                     $grnDetails->i_rec_qty = $rec['qty'];
                     $grnDetails->bal_qty =(double)$rec['bal_qty'];
                     $grnDetails->original_bal_qty=(double)$rec['original_bal_qty'];
                     $grnDetails->maximum_tolarance =$rec['maximum_tolarance'];
                     $grnDetails->item_code = $poDetails->item_code;
                     $grnDetails->customer_po_id=$rec['cus_order_details_id'];
                     $grnDetails->excess_qty=(double)$rec['excess_qty'];
                     $grnDetails->standard_price =(double)$rec['standard_price'];
                     $grnDetails->purchase_price =(double)$rec['purchase_price'];
                     $grnDetails->shop_order_id =$rec['shop_order_id'];
                     $grnDetails->shop_order_detail_id=$rec['shop_order_detail_id'];
                     $grnDetails->inventory_uom =$rec['inventory_uom'];
                     $grnDetails->status=1;


                     $responseData[$y]=$grnDetails;
                     $y++;
                     $i++;
                     //Get Quarantine Bin
                     $bin = StoreBin::where('substore_id', $store->substore_id)
                         ->where('quarantine','=',1)
                         ->first();


                     //Update Stock Transaction
                     $transaction = Transaction::where('trans_description', 'ARRIVAL')->first();

                     if($rec['category_code']=="FAB"){
                       $rec['inspection_allowed']=1;
                     }

                     if(empty($rec['inspection_allowed'])==true|| $rec['inspection_allowed']==0){
                       $grnDetails->inspection_allowed=0;
                       $grnDetails->save();
                       $st = new StockTransaction;
                       $st->status = 'CONFIRM';
                       $st->doc_type = $transaction->trans_code;
                       $st->doc_num = $header->grn_id;
                       $st->style_id = $poDetails->style;
                       $st->main_store = $store->store_id;
                       $st->sub_store = $store->substore_id;
                       $st->item_code = $poDetails->item_code;
                       $st->size = $poDetails->size;
                       $st->color = $poDetails->colour;
                       $st->uom = $poDetails->uom;
                       $st->customer_po_id=$rec['cus_order_details_id'];
                       $st->qty = (double)$rec['qty'];
                       $st->standard_price =(double)$rec['standard_price'];
                       $st->purchase_price =(double)$rec['purchase_price'];
                       $st->shop_order_id =$rec['shop_order_id'];
                       $st->shop_order_detail_id =$rec['shop_order_detail_id'];
                       $st->location = auth()->payload()['loc_id'];
                       $st->bin = $bin->store_bin_id;
                       $st->direction="+";
                       $st->created_by = auth()->payload()['user_id'];
                       $st->save();

                       $findStoreStockLine=DB::SELECT ("SELECT * FROM store_stock
                                                        where item_id= $poDetails->item_code
                                                        AND shop_order_id=$st->shop_order_id
                                                        AND style_id=$poDetails->style
                                                        AND shop_order_detail_id=$st->shop_order_detail_id
                                                        AND bin=$bin->store_bin_id
                                                        AND store=$store->store_id
                                                        AND sub_store=$store->substore_id
                                                        AND location=$st->location");
                      if($findStoreStockLine==null){
                          $storeUpdate=new Stock();
                                    $storeUpdate=new Stock();
                                    $storeUpdate->shop_order_id=$rec['shop_order_id'];
                                    $storeUpdate->shop_order_detail_id=$rec['shop_order_detail_id'];
                                    $storeUpdate->style_id = $poDetails->style;
                                    $storeUpdate->item_id=$poDetails->item_code;
                                    $storeUpdate->size = $poDetails->size;
                                    $storeUpdate->color =  $poDetails->colour;
                                    $storeUpdate->location = auth()->payload()['loc_id'];
                                    $storeUpdate->store = $header->main_store;
                                    $storeUpdate->sub_store =$header->sub_store;
                                    $storeUpdate->bin = $bin->store_bin_id;
                                    $storeUpdate->uom=$poDetails->uom;
                                    $storeUpdate->standard_price = $rec['standard_price'];
                                    $storeUpdate->purchase_price = $rec['purchase_price'];

                                    if($rec['standard_price']!=(double)$rec['purchase_price']){
                                      //save data on price variation table
                                      $priceVariance= new PriceVariance;
                                      $priceVariance->item_id=$poDetails->item_code;
                                      $priceVariance->standard_price=$rec['standard_price'];
                                      $priceVariance->purchase_price =$rec['purchase_price'];
                                      $priceVariance->shop_order_id =$rec['shop_order_id'];
                                      $priceVariance->shop_order_detail_id =$rec['shop_order_detail_id'];
                                      $priceVariance->status =1;
                                      $priceVariance->save();
                                    }
                                    //check inventory uom and purchase uom varied each other
                                  if($poDetails->uom!=$rec['inventory_uom']){
                                    $storeUpdate->uom = $rec['inventory_uom'];
                                    $_uom_unit_code=UOM::where('uom_id','=',$rec['inventory_uom'])->pluck('uom_code');
                                    $_uom_base_unit_code=UOM::where('uom_id','=',$poDetails->uom)->pluck('uom_code');
                                    //get convertion equatiojn details
                                    //dd($_uom_unit_code);
                                    $ConversionFactor=ConversionFactor::select('*')
                                                                        ->where('unit_code','=',$_uom_unit_code[0])
                                                                        ->where('base_unit','=',$_uom_base_unit_code[0])
                                                                        ->first();
                                  //dd($ConversionFactor);
                                                                          // convert values according to the convertion rate
                                                                        //$storeUpdate->inv_qty =(double)($grnDetails->grn_qty *$ConversionFactor->present_factor);
                                                                        $storeUpdate->qty = (double)( $grnDetails->grn_qty*$ConversionFactor->present_factor);
                                                                        //$storeUpdate->tolerance_qty = (double)($grnDetails->maximum_tolarance*$ConversionFactor->present_factor);
                                }
                                //if inventory uom and purchase uom are the same
                                if($poDetails->uom==$rec['inventory_uom']){

                                  //$storeUpdate->inv_qty =(double)($grnDetails->grn_qty);
                                  $storeUpdate->qty = (double)($grnDetails->grn_qty);
                                  //$storeUpdate->tolerance_qty = (double)($grnDetails->maximum_tolarance);
                                }

                              //  $storeUpdate->transfer_status="STOCKUPDATE";
                                $storeUpdate->status=1;
                                $shopOrder=ShopOrderDetail::find($rec['shop_order_detail_id']);
                                $shopOrder->asign_qty=$storeUpdate->qty+$shopOrder->asign_qty;
                                $shopOrder->save();
                                $storeUpdate->save();

                      }
                        else if($findStoreStockLine!=null){
                          //find exaxt line in stock
                          $stock=Stock::find($findStoreStockLine[0]->id);

                          //if previous standerd price and new price is same

                          if($rec['standard_price']!=$rec['purchase_price']){
                            $priceVariance= new PriceVariance;
                            $priceVariance->item_id=$poDetails->item_code;
                            $priceVariance->standard_price=$rec['standard_price'];
                            $priceVariance->purchase_price =$rec['purchase_price'];
                            $priceVariance->shop_order_id =$rec['shop_order_id'];
                            $priceVariance->shop_order_detail_id =$rec['shop_order_detail_id'];
                            $priceVariance->status =1;
                            $priceVariance->save();
                          }

                          //check inventory uom and purchase uom varied each other
                        if($poDetails->uom!=$rec['inventory_uom']){
                          $stock->uom = $rec['inventory_uom'];
                          $_uom_unit_code=UOM::where('uom_id','=',$rec['inventory_uom'])->pluck('uom_code');
                          $_uom_base_unit_code=UOM::where('uom_id','=',$poDetails->uom)->pluck('uom_code');
                          //get convertion equatiojn details
                          $ConversionFactor=ConversionFactor::select('*')
                                                              ->where('unit_code','=',$_uom_unit_code[0])
                                                              ->where('base_unit','=',$_uom_base_unit_code[0])
                                                              ->first();
                                                                // convert values according to the convertion rate
                                                                //update stock qty with convertion qty
                                                                $stock->qty =(double)$stock->qty+(double)($grnDetails->grn_qty*$ConversionFactor->present_factor);
                                                                //$stock->total_qty = (double)$stock->total_qty+(double)($grnDetails->grn_qty*$ConversionFactor->present_factor);
                                                                //$stock->tolerance_qty = (double)($grnDetails->maximum_tolarance*$ConversionFactor->present_factor);


                      }

                      //if inventory uom and purchase uom is same
                      if($poDetails->uom==$rec['inventory_uom']){

                        $stock->qty = (double)$stock->qty+(double)($grnDetails->grn_qty);
                        //$stock->total_qty=(double)$stock->total_qty+(double)($grnDetails->grn_qty);
                        //$stock->tolerance_qty = $grnDetails->maximum_tolarance;


                      }

                      $shopOrder=ShopOrderDetail::find($rec['shop_order_detail_id']);
                      $shopOrder->asign_qty=$grnDetails->grn_qty+$shopOrder->asign_qty;
                      $shopOrder->save();
                      //$stock->total_qty=$stock->total_qty+$fabricInspection->received_qty;
                     //$stock->inv_qty = $stock->inv_qty+$fabricInspection->received_qty;
                     $stock->save();


                        }




                     }

                     else if(empty($rec['inspection_allowed'])==false|| $rec['inspection_allowed']==1){
                       $grnDetails->inspection_allowed=1;
                       $grnDetails->save();
                       $st = new StockTransaction;
                       $st->status = 'CONFIRM';
                       $st->doc_type = $transaction->trans_code;
                       $st->doc_num = $header->grn_id;
                       $st->style_id = $poDetails->style;
                       $st->main_store = $store->store_id;
                       $st->sub_store = $store->substore_id;
                       $st->item_code = $poDetails->item_code;
                       $st->size = $poDetails->size;
                       $st->color = $poDetails->colour;
                       $st->uom = $poDetails->uom;
                       $st->customer_po_id=$rec['cus_order_details_id'];
                       $st->qty = (double)$rec['qty'];
                       $st->standard_price =(double)$rec['standard_price'];
                       $st->purchase_price =(double)$rec['purchase_price'];
                       $st->shop_order_id =$rec['shop_order_id'];
                       $st->shop_order_detail_id =$rec['shop_order_detail_id'];
                       $st->location = auth()->payload()['loc_id'];
                       $st->bin = $bin->store_bin_id;
                       $st->created_by = auth()->payload()['user_id'];
                       $st->save();

                     }

                     if (!$grnDetails->save()) {
                         return response(['data' => [
                             'type' => 'error',
                             'message' => 'Not Saved',
                             'grnId' => $header->grn_id
                         ]
                         ], Response::HTTP_CREATED);
                     }
                 }else{
                     return response([ 'data' => [
                         'type' => 'error',
                         'message' => 'Not matching with supplier tolerance.',
                         'grnId' => $header->grn_id,
                         'detailData'=>$responseData
                     ]
                     ], Response::HTTP_CREATED );
                 }

             }

            return response(['data' => [
                    'type' => 'success',
                    'message' => 'Saved Successfully.',
                    'grnId' => $header->grn_id,
                    'detailData'=>$responseData
                ]
            ], Response::HTTP_CREATED);


    }

    //deactivate a Grn Header
    public function destroy($id)
    {

        $rollPlan=GrnHeader::join('store_grn_detail','store_grn_header.grn_id','=','store_grn_detail.grn_id')
        ->join('store_roll_plan','store_grn_detail.grn_detail_id','=','store_roll_plan.grn_detail_id')
        ->where('store_grn_header.grn_id','=',$id)->exists();
        if($rollPlan==true){
          return response([
            'data' => [
              'message' => 'Inward Register Already in Use.',
              'status'=>0
            ]
          ] );
        }
        else{
          $header=GrnHeader::select('store_grn_header.*')
          ->join('store_grn_detail','store_grn_header.grn_id','=','store_grn_detail.grn_id')
          ->where('store_grn_header.grn_id','=',$id)->first();
          //dd($header->sub_store);
        $grnDetailupdate=GrnDetail::where('grn_id',$id)->select('*')->get();
        $transaction = Transaction::where('trans_description', 'GRNCANCEL')->first();
        for($i=0;$i<sizeof($grnDetailupdate);$i++){
          $st = new StockTransaction;
          $grnDetailupdate[$i]->status=0;
          $grnDetailupdate[$i]->save();
          $st->status = 'CONFIRM';
          $st->doc_type = $transaction->trans_code;
          $st->doc_num = $header->grn_id;
          $st->style_id = $grnDetailupdate[$i]->style_id;
          $st->main_store =  $header->main_store;
          $st->sub_store = $header->sub_store;
          $st->item_code = $grnDetailupdate[$i]->item_code;
          $st->size = $grnDetailupdate[$i]->size;
          $st->color = $grnDetailupdate[$i]->color;
          $st->uom = $grnDetailupdate[$i]->uom;
          $st->customer_po_id=$grnDetailupdate[$i]->customer_po_id;
          $st->qty = (double)$grnDetailupdate[$i]->grn_qty;
          $st->standard_price =(double)$grnDetailupdate[$i]->standard_price;
          $st->purchase_price =(double)$grnDetailupdate[$i]->purchase_price;
          $st->shop_order_id =$grnDetailupdate[$i]->shop_order_id;
          $st->shop_order_detail_id =$grnDetailupdate[$i]->shop_order_detail_id;
          $st->location = auth()->payload()['loc_id'];
          //Get Quarantine Bin
        //  dd($st->uom);
          $bin = StoreBin::where('substore_id',$header->sub_store)
              ->where('quarantine','=',1)
              ->first();
              //dd( $bin->store_bin_id);
          $st->bin = $bin->store_bin_id;
          $st->direction="-";
          $st->created_by = auth()->payload()['user_id'];
          $st->save();
          //dd($grnDetailupdate[$i]->style_id);

       $findStoreStockLine=DB::SELECT ("SELECT * FROM store_stock
                                                                  where item_id=$st->item_code
                                                                  AND shop_order_id=$st->shop_order_id
                                                                  AND style_id=$st->style_id
                                                                  AND shop_order_detail_id=$st->shop_order_detail_id
                                                                  AND bin=$st->bin
                                                                  AND store=$st->main_store
                                                                  AND sub_store=$st->sub_store
                                                                  AND location=$st->location");

                if($grnDetailupdate[$i]['inspection_allowed']==0){
                  $shopOrder=ShopOrderDetail::find($grnDetailupdate[$i]->shop_order_detail_id);
                  $shopOrder->asign_qty=$shopOrder->asign_qty-(double)$grnDetailupdate[$i]->grn_qty;
                  $shopOrder->save();

                    $stock=Stock::find($findStoreStockLine[0]->id);

                    if($stock->uom!=$grnDetailupdate[$i]->uom){
                     $_uom_unit_code=UOM::where('uom_id','=',$stock->uom)->pluck('uom_code');
                      $_uom_base_unit_code=UOM::where('uom_id','=',$grnDetailupdate[$i]->uom)->pluck('uom_code');
                      //get convertion equatiojn details
                      $ConversionFactor=ConversionFactor::select('*')
                                                          ->where('unit_code','=',$_uom_unit_code[0])
                                                          ->where('base_unit','=',$_uom_base_unit_code[0])
                                                          ->first();
                                                            // convert values according to the convertion rate
                                                            //update stock qty with convertion qty
                                                            $stock->qty =(double)$stock->qty-(double)($grnDetailupdate[$i]->grn_qty*$ConversionFactor->present_factor);
                                                            //$stock->total_qty = (double)$stock->total_qty+(double)($grnDetails->grn_qty*$ConversionFactor->present_factor);
                                                            //$stock->tolerance_qty = (double)($grnDetails->maximum_tolarance*$ConversionFactor->present_factor);
                                                            $stock->save();


                  }
                  else if($stock[0]->uom==$grnDetailupdate[$i]->uom){
                    $stock->qty =(double)$stock->qty-(double)($grnDetailupdate[$i]->grn_qty);
                    $stock->save();

                }

        }
      //  dd($grnDetailupdate);
        $grnHeader = GrnHeader::where('grn_id', $id)->update(['status' => 0]);
        return response([
          'data' => [
            'message' => 'Inward Registery deactivate successfully.',
            'status'=>1
          ]
        ] );
      }

    }

}
    public function datatable_search($data){
        $start = $data['start'];
        $length = $data['length'];
        $draw = $data['draw'];
        $search = $data['search']['value'];
        $order = $data['order'][0];
        $order_column = $data['columns'][$order['column']]['data'];
        $order_type = $order['dir'];

        $section_list = GrnHeader::select(DB::raw("DATE_FORMAT(store_grn_header.updated_date, '%d-%b-%Y') 'updated_date_'"),'store_grn_header.grn_number','store_grn_header.status', 'store_grn_detail.grn_id','merc_po_order_header.po_number', 'org_supplier.supplier_name', 'org_store.store_name', 'org_substore.substore_name','store_grn_header.inv_number','usr_login.user_name')
                        ->join('store_grn_detail', 'store_grn_detail.grn_id', '=', 'store_grn_header.grn_id')
                        ->leftjoin('merc_po_order_header','store_grn_header.po_number','=','merc_po_order_header.po_id')
                        //->leftjoin('store_grn_header', 'store_grn_detail.grn_id', '=', 'store_grn_header.grn_id')
                        ->leftjoin('org_substore', 'store_grn_header.sub_store', '=', 'org_substore.substore_id')
                        ->leftjoin('org_store', 'org_substore.store_id', '=', 'org_store.store_id')
                        ->leftjoin('org_supplier', 'store_grn_header.sup_id', '=', 'org_supplier.supplier_id')
                        ->leftjoin('usr_login','store_grn_detail.created_by','=','usr_login.user_id')
                        ->orWhere('supplier_name', 'like', $search.'%')
                        ->orWhere('substore_name', 'like', $search.'%')
                        ->orWhere('grn_number', 'like', $search.'%')
                        ->orWhere('inv_number', 'like', $search.'%')
                        ->orWhere('user_name', 'like', $search.'%')
                        ->orWhere('merc_po_order_header.po_number', 'like', $search.'%')
                        ->orderBy($order_column, $order_type)
                        ->orderBy('store_grn_header.updated_date',$order_column.' DESC', $order_type)
                        ->groupBy('store_grn_header.grn_id')
                        ->offset($start)->limit($length)->get();
                        //->where('stock_grn_header'  , '=', $search.'%' )


        $section_count = GrnHeader::where('grn_number'  , 'like', $search.'%' )
            //->orWhere('style_description'  , 'like', $search.'%' )
            ->count();

        return [
            "draw" => $draw,
            "recordsTotal" => $section_count,
            "recordsFiltered" => $section_count,
            "data" => $section_list
        ];
    }

    public function validateSupplierTolerance($dataArr, $suppId){
    //  dd($dataArr);

        $poQty = 0;
        $qty = 0;
        foreach ($dataArr as $data){
            $qty += $data['qty'];
            $poQty += $data['req_qty'];

        }

        //Get Supplier Tolarance
        $supTol = SupplierTolarance::where('supplier_id', $suppId)->first();

        $tolQty = $poQty*($supTol->tolerance_percentage/100);
        $plusQty = $tolQty + $poQty;
        $minusQty = $poQty - $tolQty;
        if($qty >= $minusQty || $qty <= $plusQty){
            return true;
        }else{
            return false;
        }


    }

    public function addGrnLines(Request $request){
       // dd($request); exit;
        $lineCount = 0;

        //Check po lines selected
        foreach ($request['item_list'] as $rec){
            if($rec['item_select']){
                $lineCount++;
            }
        }

        if($lineCount > 0){
            if(!$request['id']){
                $grnHeader = new GrnHeader;
                $grnHeader->grn_number = 0;
                $grnHeader->po_number = $request->po_no;
                $grnHeader->save();
                $grnNo = $grnHeader->grn_id;
            }else{
                $grnNo = $request['id'];
            }

            $i = 1;
            foreach ($request['item_list'] as $rec){
                if($rec['item_select']){

                    //$poData = new PoOrderDetails;
                    $poData = PoOrderDetails::where('id', $rec['po_line_id'])->first();

                   // dd($poData);

                    $grnDetails = new GrnDetail;
                    $grnDetails->grn_id = $grnNo;
                    $grnDetails->grn_line_no = $i;
                    $grnDetails->style_id = $poData->style;
                    $grnDetails->sc_no = $poData->sc_no;
                    $grnDetails->color = $poData->colour;
                    $grnDetails->size = $poData->size;
                    $grnDetails->uom = $poData->uom;
                    $grnDetails->po_qty = $poData->req_qty;
                    $grnDetails->grn_qty = (float)$rec['qty'];
                    //$grnDetails->pre_qty = (float)$rec['qty'];
                    $grnDetails->bal_qty = $poData->bal_qty - (float)$rec['qty'];
                    $grnDetails->status = 0;
                    $grnDetails->item_code = $poData->item_code;
                    $grnDetails->save();

                }
                $i++;
            }

        }

        return response([
            'id' => $grnNo
        ]);
    }

    public function saveGrnBins(Request $request){
        dd($request);
        $grnData = GrnDetail::find($request->line_id);
       /* foreach ($request->bin_list as $bin){
            $stockTrrans = new StockTransaction;
            $stockTrrans->bin = $bin['bin'];
            $stockTrrans->qty = $bin['qty'];
            $stockTrrans->so = $grnData->sc_no;
            $stockTrrans->doc_type = 'GRN';
            $stockTrrans->doc_num = $request->id;
            $stockTrrans->item_code = $grnData->item_code;
            $stockTrrans->size = $grnData->size;
            $stockTrrans->color = $grnData->color;
            $stockTrrans->uom = $grnData->uom;
            $stockTrrans->location = 10;
            $stockTrrans->created_by = 1000;
            $stockTrrans->status = 'PENDING';
            $stockTrrans->save();
        }*/

    }

    public function update(Request $request, $id)
    {
      //save grn header
      $y=0;
      $responseData=[];
      $header=$request->header;
      $dataset=$request->dataset;
      $grnHeader=GrnHeader::find($id);
        $grnHeader['batch_no']=$header['batch_no'];
        $grnHeader['sub_store']=$header['sub_store']['substore_id'];
        $grnHeader['note']=$header['note'];

        //$store = SubStore::find($request->header['substore_id'])
        $grnHeader->save();
        //find bin
        $bin = StoreBin::where('substore_id', $grnHeader['sub_store'])
            ->where('quarantine','=',1)
            ->first();
            //loop through data set
        for($i=0;$i<sizeof($dataset);$i++){

          //$grnDetails=new GrnDetail;
          //if data set have grn id (updaated line with several new lines)
          if(isset($dataset[$i]['grn_detail_id'])==true){
            //find related grn line in detail table
            //dd($dataset[$i]['grn_detail_id']);
            $grnDetails=GrnDetail::find($dataset[$i]['grn_detail_id']);
            //update grn qtys
            $grnDetails['grn_qty']=(float)$dataset[$i]['qty'];
            //$grnDetails['pre_qty']=(float)$dataset[$i]['qty'];
            $grnDetails['i_rec_qty'] = (float)$dataset[$i]['qty'];
            $grnDetails['bal_qty']=(float)$dataset[$i]['bal_qty'];
            $grnDetails->save();

            //find related po details
            $poDetails = PoOrderDetails::find($dataset[$i]['id']);

            //Update Stock Transaction
            $transaction = Transaction::where('trans_description', 'ARRIVAL')->first();
            //create new stock tranaction table record
            $st = new StockTransaction;
            $st->status = 'CONFIRM';
            $st->doc_type = $transaction->trans_code;
            $st->doc_num = $id;
            $st->style_id = $poDetails->style;
            $st->main_store = $grnHeader->main_store;
            $st->sub_store = $grnHeader->sub_store;
            $st->item_code = $poDetails->item_code;
            $st->size = $poDetails->size;
            $st->color = $poDetails->colour;
            $st->uom = $poDetails->uom;
            $st->customer_po_id=$dataset[$i]['cus_order_details_id'];
            $st->qty = (double)($dataset[$i]['pre_qty']-$dataset[$i]['qty']);
            $st->standard_price =(double)$dataset[$i]['standard_price'];
            $st->purchase_price =(double)$dataset[$i]['purchase_price'];
            $st->shop_order_id =$dataset[$i]['shop_order_id'];
            $st->shop_order_detail_id =$dataset[$i]['shop_order_detail_id'];
            $st->location = auth()->payload()['loc_id'];
            $st->direction="+";
            //dd($bin);
            $st->bin = $bin->store_bin_id;
            $st->created_by = auth()->payload()['user_id'];
            //save stock transaction record
            $st->save();

            if($dataset[$i]['category_code']=="FAB"){
              $dataset[$i]['inspection_allowed']=1;
            }
            //check current line is alowed for inspection or not
               if(empty($dataset[$i]['inspection_allowed'])==true|| $dataset[$i]['inspection_allowed']==0){

                 //find relted item in stock
                 $findStoreStockLine=DB::SELECT ("SELECT * FROM store_stock
                                                  where item_id= $poDetails->item_code
                                                  AND shop_order_id=$st->shop_order_id
                                                  AND style_id=$poDetails->style
                                                  AND shop_order_detail_id=$st->shop_order_detail_id
                                                  AND bin=$bin->store_bin_id
                                                  AND store=$grnHeader->main_store
                                                  AND sub_store=$grnHeader->sub_store
                                                  AND location=$st->location");
                                                  //dd($findStoreStockLine);
                    $stock=Stock::find($findStoreStockLine[0]->id);
                    //check if purchase price and starned price is varied
                    if($stock->standard_price!=$dataset[$i]['purchase_price']){
                      //create price variance object
                      $priceVariance= new PriceVariance;
                      $priceVariance->item_id=$poDetails->item_code;
                      $priceVariance->standard_price=$stock->standard_price;
                      $priceVariance->purchase_price =$dataset[$i]['purchase_price'];
                      $priceVariance->shop_order_id =$dataset[$i]['shop_order_id'];
                      $priceVariance->shop_order_detail_id =$dataset[$i]['shop_order_detail_id'];
                      $priceVariance->status =1;
                      //save price variance record
                      $priceVariance->save();
                    }
                      //find shop order line for update the asign qty
                    $shopOrder=ShopOrderDetail::find($dataset[$i]['shop_order_detail_id']);
                    //check inventory uom and purchase uom is varied
                    if($poDetails->uom!=$dataset[$i]['inventory_uom']){
                        //if po uom and inventory uom is varid
                        //asign stock uom to inve uom
                        $stock->uom = $dataset[$i]['inventory_uom'];
                        //convert qty according the uom
                        $_uom_unit_code=UOM::where('uom_id','=',$dataset[$i]['inventory_uom'])->pluck('uom_code');
                        $_uom_base_unit_code=UOM::where('uom_id','=',$poDetails->uom)->pluck('uom_code');
                        $ConversionFactor=ConversionFactor::select('*')
                                                            ->where('unit_code','=',$_uom_unit_code[0])
                                                            ->where('base_unit','=',$_uom_base_unit_code[0])
                                                            ->first();
                                                            //dd((double)$stock->inv_qty-(double)$data[$i]['previous_received_qty']);

                                                            $stock->qty =(double)$stock->qty-(double)$dataset[$i]['pre_qty']+(double)($dataset[$i]['qty']*$ConversionFactor->present_factor);
                                                            //$stock->total_qty = (double)$stock->total_qty-(double)$dataset[$i]['pre_qty']+(double)($dataset[$i]['qty']*$ConversionFactor->present_factor);
                                                            $shopOrder->asign_qty=$dataset['qty']-(double)$dataset[$i]['pre_qty']+$shopOrder->asign_qty;
                                                            //$stock->tolerance_qty = (double)($dataset[$i]['maximum_tolarance']*$ConversionFactor->present_factor);


                    }

                    // if po uom and inventory uom is the same
                    if($poDetails->uom==$dataset[$i]['inventory_uom']){
                     $stock->qty = (double)$stock->qty-(double)$dataset[$i]['pre_qty']+(double)($dataset[$i]['qty']);
                      //$stock->total_qty=(double)$stock->total_qty-(double)$dataset[$i]['pre_qty']+(double)($dataset[$i]['qty']);
                      $shopOrder->asign_qty=$dataset[$i]['qty']-(double)$dataset[$i]['pre_qty']+$shopOrder->asign_qty;

                      //$stock->tolerance_qty = $dataset[$i]['maximum_tolarance'];


                    }
                    //save shop order
                   $shopOrder->save();
                   $stock->save();



              }

              //cretate responce data array
            $responseData[$y]=$grnDetails;
            }
              //if dataset line dont have grn id
            else if(isset($dataset[$i]['grn_detail_id'])==false){
              //find po details line
              $poDetails = PoOrderDetails::find($dataset[$i]['id']);
              //get next grn line no reated to the header
              $max_line_no=DB::table('store_grn_detail')->where('grn_id','=',$id)
                                                        ->max('grn_line_no');
                  //save grn details
              $grnDetails = new GrnDetail;

              $grnDetails->grn_id =$id;
              $grnDetails->po_number=$header['po_id'];
              $grnDetails->grn_line_no = $max_line_no++;
              $grnDetails->style_id = $poDetails->style;
              $grnDetails->po_details_id=$dataset[$i]['id'];
              $grnDetails->combine_id = $poDetails->comb_id;
              $grnDetails->color = $poDetails->colour;
              $grnDetails->size = $poDetails->size;
              $grnDetails->uom = $poDetails->uom;
              $grnDetails->po_qty = (double)$poDetails->req_qty;
              $grnDetails->grn_qty = $dataset[$i]['qty'];
              //$grnDetails->pre_qty = $dataset[$i]['qty'];
              $grnDetails->bal_qty =(double)$dataset[$i]['bal_qty'];
              $grnDetails->maximum_tolarance =$dataset[$i]['maximum_tolarance'];
              $grnDetails->original_bal_qty=(double)$dataset[$i]['original_bal_qty'];
              $grnDetails->item_code = $poDetails->item_code;
              $grnDetails->excess_qty=(double)$dataset[$i]['excess_qty'];
              $grnDetails->customer_po_id=$dataset[$i]['cus_order_details_id'];
              $grnDetails->standard_price =(double)$dataset[$i]['standard_price'];
              $grnDetails->purchase_price =(double)$dataset[$i]['purchase_price'];
              $grnDetails->shop_order_id =$dataset[$i]['shop_order_id'];
              $grnDetails->shop_order_detail_id =$dataset[$i]['shop_order_detail_id'];
              $grnDetails->inventory_uom =$dataset[$i]['inventory_uom'];
              $grnDetails->status=1;



              //$poDetails = PoOrderDetails::find($dataset[$i]['id']);


              //Update Stock Transaction
              $transaction = Transaction::where('trans_description', 'ARRIVAL')->first();

              $st = new StockTransaction;
              $st->status = 'CONFIRM';
              $st->doc_type = $transaction->trans_code;
              $st->doc_num = $id;
              $st->style_id = $poDetails->style;
              $st->main_store = $grnHeader->main_store;
              $st->sub_store = $grnHeader->sub_store;
              $st->item_code = $poDetails->item_code;
              $st->size = $poDetails->size;
              $st->color = $poDetails->colour;
              $st->uom = $poDetails->uom;
              $st->direction="+";
              $st->customer_po_id=$dataset[$i]['cus_order_details_id'];
              $st->qty = (double)$dataset[$i]['qty'];
              $st->shop_order_id =$dataset[$i]['shop_order_id'];
              $st->shop_order_detail_id =$dataset[$i]['shop_order_detail_id'];
              $st->location = auth()->payload()['loc_id'];
              //dd($bin);
              $st->bin = $bin->store_bin_id;
              $st->created_by = auth()->payload()['user_id'];
              $st->save();

              //add newly
                //if new line is not allowed for isnpection directly update the stock table
              if(empty($dataset[$i]['inspection_allowed'])==true|| $dataset[$i]['inspection_allowed']==0){
                  $grnDetails->inspection_allowed=0;
                  //$grnDetails->save();
                //find related stock line
                $findStoreStockLine=DB::SELECT ("SELECT * FROM store_stock
                                                 where item_id= $poDetails->item_code
                                                 AND shop_order_id=$st->shop_order_id
                                                 AND style_id=$poDetails->style
                                                 AND shop_order_detail_id=$st->shop_order_detail_id
                                                 AND bin=$bin->store_bin_id
                                                 AND store=$grnHeader->main_store
                                                 AND sub_store=$grnHeader->sub_store
                                                 AND location=$st->location");
                  //if stock line not found
               if($findStoreStockLine==null){
                //create new line

                             //$storeUpdate=new Stock();
                             $storeUpdate=new Stock();
                             $store = SubStore::find($request->header['substore_id']);
                             $bin = StoreBin::where('substore_id', $store->substore_id)
                                 ->where('quarantine','=',1)
                                 ->first();
                             $storeUpdate->shop_order_id=$dataset[$i]['shop_order_id'];
                             $storeUpdate->shop_order_detail_id=$dataset[$i]['shop_order_detail_id'];
                             $storeUpdate->style_id = $poDetails->style;
                             $storeUpdate->item_id=$poDetails->item_code;
                             $storeUpdate->size = $poDetails->size;
                             $storeUpdate->color =  $poDetails->colour;
                             $storeUpdate->location = auth()->payload()['loc_id'];
                             $storeUpdate->store =$store->store_id;
                             $storeUpdate->sub_store =$header['sub_store']['substore_id'];
                             $storeUpdate->bin = $bin->store_bin_id;
                             $storeUpdate->uom=$poDetails->uom;
                             $storeUpdate->standard_price = $dataset[$i]['standard_price'];
                             $storeUpdate->purchase_price = $dataset[$i]['purchase_price'];
                              //if dataline statned price and purchase price is varied
                             if($storeUpdate->standard_price!=$storeUpdate->purchase_price){
                               //save data on price variation table
                               $priceVariance= new PriceVariance;
                               $priceVariance->item_id=$poDetails->item_code;
                               $priceVariance->standard_price=$dataset[$i]['standard_price'];
                               $priceVariance->purchase_price =$dataset[$i]['purchase_price'];
                               $priceVariance->shop_order_id =$dataset[$i]['shop_order_id'];
                               $priceVariance->shop_order_detail_id =$dataset[$i]['shop_order_detail_id'];
                               $priceVariance->status =1;
                               $priceVariance->save();
                             }
                             //check inventory uom and purchase uom varied each other
                           if($poDetails->uom!=$dataset[$i]['inventory_uom']){
                             $storeUpdate->uom = $dataset[$i]['inventory_uom'];
                             $_uom_unit_code=UOM::where('uom_id','=',$dataset[$i]['inventory_uom'])->pluck('uom_code');
                             $_uom_base_unit_code=UOM::where('uom_id','=',$poDetails->uom)->pluck('uom_code');
                             //get convertion equatiojn details
                             //dd($_uom_unit_code);
                             $ConversionFactor=ConversionFactor::select('*')
                                                                 ->where('unit_code','=',$_uom_unit_code[0])
                                                                 ->where('base_unit','=',$_uom_base_unit_code[0])
                                                                 ->first();
                           //dd($ConversionFactor);
                                                                   // convert values according to the convertion rate
                                                                 $storeUpdate->qty =(double)($dataset[$i]['qty']*$ConversionFactor->present_factor);
                                                                 //$storeUpdate->total_qty = (double)( $dataset[$i]['qty']*$ConversionFactor->present_factor);
                                                                 //$storeUpdate->tolerance_qty = (double)($dataset[$i]['maximum_tolarance']*$ConversionFactor->present_factor);
                         }
                         //if inventory uom and purchase uom are the same
                         if($poDetails->uom==$dataset[$i]['inventory_uom']){

                           $storeUpdate->qty =(double)($dataset[$i]['qty']);
                           //$storeUpdate->total_qty = (double)($dataset[$i]['qty']);
                          //$storeUpdate->tolerance_qty = (double)($dataset[$i]['maximum_tolarance']);
                         }

                        // $storeUpdate->transfer_status="STOCKUPDATE";
                         $storeUpdate->status=1;
                         $shopOrder=ShopOrderDetail::find($dataset[$i]['shop_order_detail_id']);
                         $shopOrder->asign_qty=$storeUpdate->qty+$shopOrder->asign_qty;
                         $shopOrder->save();
                         $storeUpdate->save();
                        // c cxcxcxcx

               }
                 else if($findStoreStockLine!=null){
                   //find exaxt line in stock
                   $stock=Stock::find($findStoreStockLine[0]->id);

                   //if previous standerd price and new price is same

                   if($dataset[$i]['standard_price']!=$dataset[$i]['purchase_price']){
                     $priceVariance= new PriceVariance;
                     $priceVariance->item_id=$dataset[$i]['master_id'];
                     $priceVariance->standard_price=$dataset[$i]['standard_price'];
                     $priceVariance->purchase_price =$dataset[$i]['purchase_price'];
                     $priceVariance->shop_order_id =$dataset[$i]['shop_order_id'];
                     $priceVariance->shop_order_detail_id =$dataset[$i]['shop_order_detail_id'];
                     $priceVariance->status =1;
                     $priceVariance->save();
                   }

                   //check inventory uom and purchase uom varied each other
                 if($poDetails->uom!=$dataset[$i]['inventory_uom']){
                   //$stock->uom = $dataset[$i]['inventory_uom'];
                   $_uom_unit_code=UOM::where('uom_id','=',$dataset[$i]['inventory_uom'])->pluck('uom_code');
                   $_uom_base_unit_code=UOM::where('uom_id','=',$poDetails->uom)->pluck('uom_code');
                   //get convertion equatiojn details
                   $ConversionFactor=ConversionFactor::select('*')
                                                       ->where('unit_code','=',$_uom_unit_code[0])
                                                       ->where('base_unit','=',$_uom_base_unit_code[0])
                                                       ->first();
                                                         // convert values according to the convertion rate
                                                         //update stock qty with convertion qty
                                                         $stock->qty =(double)$stock->qty+(double)($dataset[$i]['qty']*$ConversionFactor->present_factor);
                                                         //$stock->total_qty = (double)$stock->total_qty+(double)($dataset[$i]['qty']*$ConversionFactor->present_factor);
                                                         //$stock->tolerance_qty = (double)($dataset[$i]['maximum_tolarance']*$ConversionFactor->present_factor);


               }

               //if inventory uom and purchase uom is same
               if($poDetails->uom==$dataset[$i]['inventory_uom']){

                 $stock->qty = (double)$stock->qty+(double)($dataset[$i]['qty']);
                 //$stock->total_qty=(double)$stock->total_qty+(double)($dataset[$i]['qty']);
                 //$stock->tolerance_qty =$dataset[$i]['maximum_tolarance'];


               }

               $shopOrder=ShopOrderDetail::find($dataset[$i]['shop_order_detail_id']);
               $shopOrder->asign_qty=$dataset[$i]['qty']+$shopOrder->asign_qty;
               $shopOrder->save();
               //$stock->total_qty=$stock->total_qty+$fabricInspection->received_qty;
              //$stock->inv_qty = $stock->inv_qty+$fabricInspection->received_qty;
              $stock->save();


                 }




              }
              $grnDetails->inspection_allowed=1;
              $grnDetails->save();
            //$line_no++;
              $responseData[$y]=$grnDetails;
            }
            $y++;
        }



        //dd($header['grn_id']);


        return response(['data' => [
                'type' => 'success',
                'message' => 'Updated Successfully.',
                'grnId' => $header['grn_id'],
                'detailData'=>$responseData
            ]
        ], Response::HTTP_CREATED);




    }

    public function show($id)
    {
      $status=1;
      $headerData=DB::SELECT("SELECT store_grn_header.*, merc_po_order_header.po_number,merc_po_order_header.po_id,org_supplier.supplier_name,org_substore.substore_name,org_location.loc_name
        FROM
        store_grn_header
        INNER JOIN merc_po_order_header ON store_grn_header.po_number=merc_po_order_header.po_id
        INNER JOIN org_supplier ON store_grn_header.sup_id=org_supplier.supplier_id
        INNER JOIN org_location on merc_po_order_header.po_deli_loc=org_location.loc_id
        INNER JOIN org_substore ON store_grn_header.sub_store=org_substore.substore_id
        WHERE store_grn_header.grn_id=$id"
    );
    //dd();
    $sub_store=SubStore::find($headerData[0]->sub_store);

    $detailsData=DB::SELECT("SELECT DISTINCT  store_grn_detail.*,style_creation.style_no,merc_customer_order_header.order_id,cust_customer.customer_name,org_color.color_name,store_grn_detail.po_qty as req_qty,store_grn_detail.grn_qty as qty,store_grn_detail.grn_qty as pre_qty,store_grn_detail.po_number as po_id,merc_po_order_details.id,merc_customer_order_details.details_id as cus_order_details_id,
      org_size.size_name,org_uom.uom_code,item_master.master_description,item_master.master_code,item_master.category_id,store_grn_detail.uom as inventory_uom,item_category.category_code

      from
      store_grn_header
       JOIN store_grn_detail ON store_grn_header.grn_id=store_grn_detail.grn_id
       JOIN style_creation ON store_grn_detail.style_id=style_creation.style_id
       JOIN cust_customer ON style_creation.customer_id=cust_customer.customer_id
       INNER JOIN merc_customer_order_header ON style_creation.style_id = merc_customer_order_header.order_style
       INNER JOIN merc_customer_order_details ON merc_customer_order_header.order_id = merc_customer_order_details.order_id
       LEFT JOIN org_color ON store_grn_detail.color=org_color.color_id
       LEFT JOIN org_size ON  store_grn_detail.size= org_size.size_id
       LEFT JOIN org_uom ON store_grn_detail.uom=org_uom.uom_id
       JOIN  item_master ON store_grn_detail.item_code= item_master.master_id
       INNER JOIN item_category ON item_master.category_id=item_category.category_id
       JOIN merc_po_order_header ON store_grn_detail.po_number=merc_po_order_header.po_id
       JOIN  merc_po_order_details ON store_grn_detail.po_details_id=merc_po_order_details.id
      WHERE store_grn_header.grn_id=$id
      AND store_grn_detail.status= $status
      GROUP BY(merc_po_order_details.id)
      order By(merc_customer_order_details.rm_in_date)DESC
      ");

    return response([
        'data' =>[
      'headerData'=>  $headerData[0],
      'detailsData'=>$detailsData,
      'sub_store'=>$sub_store
      ]
    ]);

    }



    //validate anything based on requirements
    public function validate_data(Request $request){

      $for = $request->for;
      if($for == 'duplicate')
      {
        return response($this->validate_duplicate_code($request->grn_id, $request->invoice_no));
      }
    }


    //check customer code already exists
    private function validate_duplicate_code($id,$code)
    {
      $grnHeader = GrnHeader::where('inv_number','=',$code)->first();
      if($grnHeader == null){
      return ['status' => 'success'];
     }
     else if($grnHeader->grn_id == $id){
     return ['status' => 'success'];
     }
   else {
    return ['status' => 'error','message' => 'Invoice Number already exists'];
   }
    }

    public function filterData(Request $request){

   $customer_id=$request['customer_name']['customer_id'];
   $customer_po=$request['customer_po']['order_id'];
   $color=$request['color']['color_name'];
   $itemDesacription=$request['item_description']['master_id'];
   $pcd=$request['pcd_date'];
   $rm_in_date=$request['rm_in_date'];
   $po_id=$request['po_id'];
   $supplier_id=$request['supplier_id'];



                          $poData=DB::Select("SELECT DISTINCT style_creation.style_no,
                                       cust_customer.customer_name,merc_po_order_header.po_id,merc_po_order_details.id,
                                       item_master.master_description,
                                       org_color.color_name,
                                      org_size.size_name,
                                      org_uom.uom_code,
                                      merc_po_order_details.req_qty,
                                    DATE_FORMAT(merc_customer_order_details.rm_in_date, '%d-%b-%Y')as rm_in_date,
                                        #merc_customer_order_details.rm_in_date,
                                    DATE_FORMAT(merc_customer_order_details.pcd, '%d-%b-%Y')as pcd,
                                        #merc_customer_order_details.pcd,
                                      merc_customer_order_details.po_no,
                                       merc_customer_order_header.order_id,
                                       item_master.master_id,
                                       item_master.category_id,
                                       merc_customer_order_details.details_id as cus_order_details_id,
                                       merc_shop_order_header.shop_order_id,
                                       merc_shop_order_detail.shop_order_detail_id,
                                       item_master.category_id,
                                       item_master.master_code,
                                       merc_po_order_details.purchase_price,
                                       item_master.standard_price,
                                       item_master.inventory_uom,
                                       (SELECT
                                      SUM(SGD.grn_qty)
                                      FROM
                                     store_grn_detail AS SGD

                                     WHERE
                                    SGD.po_details_id = merc_po_order_details.id
                                    group By(SGD.po_details_id)
                                  ) AS tot_grn_qty,

                                  (SELECT
                                                    bal_qty
                                                       FROM
                                                       store_grn_detail AS SGD2

                                                                   WHERE
                                                                   SGD2.po_details_id = merc_po_order_details.id
                                                                   group By(SGD2.po_details_id)
                                                                 ) AS bal_qty,
                                  (

                                  SELECT
                                  IFNULL(sum(for_uom.max ),0)as maximum_tolarance
                                  FROM
                                  org_supplier_tolarance AS for_uom
                                  WHERE
                                  for_uom.uom_id =  org_uom.uom_id AND
                                  for_uom.category_id = item_master.category_id AND
                                  for_uom.subcategory_id = item_master.subcategory_id
                                ) AS maximum_tolarance


                              FROM
                              merc_po_order_header
                              INNER JOIN merc_po_order_details ON merc_po_order_header.po_number = merc_po_order_details.po_no
                              INNER JOIN style_creation ON merc_po_order_details.style = style_creation.style_id
                              INNER JOIN cust_customer ON style_creation.customer_id = cust_customer.customer_id
                              #INNER JOIN merc_customer_order_header ON style_creation.style_id = merc_customer_order_header.order_style
                              #INNER JOIN merc_customer_order_details ON merc_customer_order_header.order_id = merc_customer_order_details.order_id
                              INNER JOIN merc_shop_order_detail on merc_po_order_details.shop_order_detail_id=merc_shop_order_detail.shop_order_detail_id
                              INNER JOIN merc_shop_order_header on  merc_shop_order_detail.shop_order_id=merc_shop_order_header.shop_order_id
                              INNER JOIN merc_shop_order_delivery on merc_shop_order_header.shop_order_id=merc_shop_order_delivery.shop_order_id
                              #INNER JOIN merc_shop_order_detail on merc_shop_order_header.shop_order_id=merc_shop_order_detail.shop_order_id
                              INNER JOIN merc_customer_order_details ON merc_shop_order_delivery.delivery_id = merc_customer_order_details.details_id
                              INNER JOIN merc_customer_order_header ON merc_customer_order_details.order_id = merc_customer_order_header.order_id
                              INNER JOIN item_master ON merc_po_order_details.item_code = item_master.master_id
                              LEFT JOIN org_supplier_tolarance AS for_category ON item_master.category_id = for_category.category_id
                              LEFT JOIN org_color ON merc_po_order_details.colour = org_color.color_id
                              LEFT JOIN org_size ON merc_po_order_details.size = org_size.size_id
                              LEFT JOIN org_uom ON merc_po_order_details.uom = org_uom.uom_id

                    /* INNER JOIN  store_grn_detail ON store_grn_header.grn_id=store_grn_detail.grn_id*/

                     WHERE merc_po_order_header.po_id = $po_id
                    AND merc_po_order_header.po_sup_code=$supplier_id
                    AND merc_po_order_details.po_status='CONFIRMED'
                    AND merc_customer_order_header.order_id like  '%".$customer_po."%'
                    AND cust_customer.customer_id like  '%".$customer_id."%'
                    AND item_master.master_id like '%".$itemDesacription."%'
                    AND merc_customer_order_details.pcd like '%".$pcd."%'
                    AND merc_customer_order_details.rm_in_date like '%".$rm_in_date."%'
                    AND merc_po_order_details.req_qty>(SELECT
                                                          IFNULL(SUM(SGD.grn_qty),0)
                                                          FROM
                                                         store_grn_detail AS SGD

                                                         WHERE
                                                        SGD.po_details_id = merc_po_order_details.id
                                                      )
                    AND (org_color.color_name IS NULL or  org_color.color_name like  '%".$color."%')
                    GROUP BY merc_po_order_details.id
                    /*AND store_grn_header.grn_id=*/

            ");


            return response([
                'data' => $poData
            ]);
              ///return $poData;







   }


    public function deleteLine(Request $request){
      //dd($request->line);
      $grnDetails = GrnDetail::find($request->line);
      $grnDetails->status=0;
      $grnDetails->bal_qty=$grnDetails->$grnDetails->po_qty;
      $grnDetails->save();
      return response([
          'data' => [
            'status'=>1,
            'message'=>"Selected GRN line Deleted"
          ]
      ]);
    }

    public function getPoSCList(Request $request){
        dd($request);
        //echo 'xx';
        exit;
    }

    public function getAddedBins(Request $request){
        //dd($request);
       $grnData = GrnDetail::getGrnLineDataWithBins($request->id);

        return response([
            'data' => $grnData
        ]);
        //$grnData = GrnDetail::where('id', $request->lineId)->first();
        //dd($grnData);
    }

    public function loadAddedGrnLInes(Request $request){
        $grnLines = GrnHeader::getGrnLineData($request);

        return response([
            'data' => $grnLines
        ]);
    }
    public function isreadyForTrimPackingDetails(Request $request){
      $is_type_fabric=DB::table('item_category')->select('category_code')->where('category_id','=',$request->category_id)->first();
      $substorewiseBins=DB::table('org_substore')->select('*')->where('substore_id','=',$request->substore_id)->get();
      $status=0;
      $message="";
      $is_grn_same_qty=DB::table('store_grn_header')
      ->select('*')
      ->join('store_grn_detail','store_grn_header.grn_id','=','store_grn_detail.grn_id')
      ->where('store_grn_header.inv_number','=',$request->invoice_no)
      ->where('store_grn_header.po_number','=',$request->po_id)
      ->where('store_grn_header.grn_id','=',$request->grn_id)
      ->where('store_grn_detail.po_details_id','=',$request->po_line_id)
      ->first();
      //dd($is_grn_same_qty);
      if($is_type_fabric->category_code=='FAB'){
        $status=0;
        $is_grn_same_qty=null;
        $message="Selected Item is Fabric type";
      }
      else if($is_type_fabric->category_code!='FAB'){
        //dd($is_type_fabric->category_code);
      if($is_grn_same_qty==null){
            $status=0;
        $message="Error Can't Add Trim packing Details";
      }
       else if($is_grn_same_qty!=null){
      if($is_grn_same_qty->grn_qty==$request->qty)
     {
       $is_aLLreaddy_trim_packing_details_added=DB::table('store_trim_packing_detail')->select('*')->where('grn_detail_id','=',$is_grn_same_qty->grn_detail_id)->first();
          //dd($is_aLLreaddy_roll_plned);
              if($is_aLLreaddy_trim_packing_details_added!=null){
                $status=0;
               $message="Trim Packing Details Already Added";
                }
       else{
        $status=1;
      }
     }
     else if($is_grn_same_qty->grn_qty!=$request->qty)
        {
           $status=0;
           $message="Error Can't Add Trim Packing Details";
        }
      }
    }
      return response([
          'data'=> [
            'dataModel'=>$is_grn_same_qty,
             'status'=>$status,
             'message'=>$message,
             'substoreWiseBin'=>$substorewiseBins
            ]
      ]);


    }


    public function isreadyForRollPlan(Request $request){
      $is_type_fabric=DB::table('item_category')->select('category_code')->where('category_id','=',$request->category_id)->first();
      $substorewiseBins=DB::table('org_substore')->select('*')->where('substore_id','=',$request->substore_id)->get();
      $status=0;
      $message="";
      $is_grn_same_qty=DB::table('store_grn_header')
      ->select('*')
      ->join('store_grn_detail','store_grn_header.grn_id','=','store_grn_detail.grn_id')
      ->where('store_grn_header.inv_number','=',$request->invoice_no)
      ->where('store_grn_header.po_number','=',$request->po_id)
      ->where('store_grn_header.grn_id','=',$request->grn_id)
      ->where('store_grn_detail.po_details_id','=',$request->po_line_id)
      ->first();
      //dd($is_grn_same_qty);
      if($is_type_fabric->category_code!='FAB'){
        $status=0;
        $is_grn_same_qty=null;
        $message="Selected Item not a Fabric type";
      }
      else if($is_type_fabric->category_code=='FAB'){
        //dd($is_type_fabric->category_code);
      if($is_grn_same_qty==null){
            $status=0;
        $message="Error Can't Add Roll Plan";
      }
       else if($is_grn_same_qty!=null){
      if($is_grn_same_qty->grn_qty==$request->qty)
     {
       $is_aLLreaddy_roll_plned=DB::table('store_roll_plan')->select('*')->where('grn_detail_id','=',$is_grn_same_qty->grn_detail_id)->first();
          //dd($is_aLLreaddy_roll_plned);
              if($is_aLLreaddy_roll_plned!=null){
                $status=0;
               $message="Roll Plan Already Added";
                }
       else{
        $status=1;
      }
     }
     else if($is_grn_same_qty->grn_qty!=$request->qty)
        {
           $status=0;
           $message="Error Can't Add Roll Plan";
        }
      }
    }
      return response([
          'data'=> [
            'dataModel'=>$is_grn_same_qty,
             'status'=>$status,
             'message'=>$message,
             'substoreWiseBin'=>$substorewiseBins
            ]
      ]);


    }

}
