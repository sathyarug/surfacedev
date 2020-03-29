<?php

namespace App\Http\Controllers\Store;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Exception;
use App\Libraries\AppAuthorize;
use App\Libraries\CapitalizeAllFields;
use App\Models\Store\TrimPacking;
use App\Models\Store\GrnHeader;
use App\Models\Store\GrnDetail;
use App\Models\Store\Stock;
use App\Models\Store\StoreBin;
use App\Models\Org\UOM;
use App\Models\Merchandising\Item\Item;
use App\Models\Org\ConversionFactor;
class TrimPackingController extends Controller{


  var $authorize = null;

  public function __construct()
  {
    //add functions names to 'except' paramert to skip authentication
    $this->middleware('jwt.verify', ['except' => ['index']]);
    $this->authorize = new AppAuthorize();
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
      return response($this->autocomplete_search($search));
    }

    else {
    $this->store($request);
    }
  }


  //create roll plan
    public function store(Request $request)
    {
      $trimPacking=new TrimPacking();
      if($trimPacking->validate($request->all()))
        {
          $grnDetail_id=$request->grn_detail_id;

          for($i=0;$i<count($request->dataset);$i++){
            $trimPacking=new TrimPacking();
            $data=$request->dataset[$i];
            $data=(object)$data;
            $_tempQty=0;
            $findGrnDetailLine=GrnHeader::join('store_grn_detail','store_grn_header.grn_id','=','store_grn_detail.grn_id')
                                      ->where('store_grn_detail.grn_detail_id','=',$grnDetail_id)
                                      ->first();
            $location=auth()->payload()['loc_id'];
            //find the grned qurantine bin
            $bin = StoreBin::where('substore_id', $findGrnDetailLine->sub_store)
                                          ->where('quarantine','=',1)
                                          ->first();
            //find trim packing updated bin
            $binID=DB::table('org_store_bin')->where('store_bin_name','=',$data->bin)->select('store_bin_id')->first();
            if($bin->store_bin_id!=$binID->store_bin_id){
              //find grned stock line
            $findStoreStockLine=DB::SELECT ("SELECT * FROM store_stock
                                             where item_id= $findGrnDetailLine->item_code
                                             AND shop_order_id=$findGrnDetailLine->shop_order_id
                                             AND style_id=$findGrnDetailLine->style_id
                                             AND shop_order_detail_id=$findGrnDetailLine->shop_order_detail_id
                                             AND bin=$bin->store_bin_id
                                             AND store=$findGrnDetailLine->main_store
                                             AND sub_store=$findGrnDetailLine->sub_store
                                             AND location=$location");
              //dd($findGrnDetailLine->item_code);
            $stock=Stock::find($findStoreStockLine[0]->id);
            $item=Item::find($findGrnDetailLine->item_code);
            if($item->inventory_uom!=$findGrnDetailLine->uom){
              $_uom_unit_code=UOM::where('uom_id','=',$item->inventory_uom)->pluck('uom_code');
              $_uom_base_unit_code=UOM::where('uom_id','=',$findGrnDetailLine->uom)->pluck('uom_code');
              //get convertion equatiojn details
              $item=Item::find($findGrnDetailLine->item_code);
              $ConversionFactor=ConversionFactor::select('*')
                                                  ->where('unit_code','=',$_uom_unit_code[0])
                                                  ->where('base_unit','=',$_uom_base_unit_code[0])
                                                  ->first();
            $_tempQty=(double)($data->received_qty*$ConversionFactor->present_factor);
            $stock->qty =(double)$stock->qty-(double)($data->received_qty*$ConversionFactor->present_factor);

            }
            else if($item->inventory_uom==$findGrnDetailLine->item_code){
            $_tempQty=(double)$data->received_qty;
            $stock->qty=(double)$stock->qty-(double)$data->received_qty;
           }
                  $stock->save();
                //find updated bin is already have same item
                $findStoreStockLineforNewBin=DB::SELECT ("SELECT * FROM store_stock
                                                 where item_id= $findGrnDetailLine->item_code
                                                 AND shop_order_id=$findGrnDetailLine->shop_order_id
                                                 AND style_id=$findGrnDetailLine->style_id
                                                 AND shop_order_detail_id=$findGrnDetailLine->shop_order_detail_id
                                                 AND bin=$binID->store_bin_id
                                                 AND store=$findGrnDetailLine->main_store
                                                 AND sub_store=$findGrnDetailLine->sub_store
                                                 AND location=$location");
                if($findStoreStockLineforNewBin!=null){
                   $stock=Stock::find($findStoreStockLineforNewBin[0]->id);
                   $stock->qty=(double)$stock->qty+$_tempQty;
                   $stock->save();
                }
                else if($findStoreStockLineforNewBin==null){
                $stockUpdate=new Stock();
                $stockUpdate->shop_order_id=$stock->shop_order_id;
                $stockUpdate->shop_order_detail_id=$stock->shop_order_detail_id;
                $stockUpdate->style_id=$stock->style_id;
                $stockUpdate->item_id=$stock->item_id;
                $stockUpdate->size=$stock->size;
                $stockUpdate->item_id=$stock->item_id;
                $stockUpdate->size=$stock->size;
                $stockUpdate->color=$stock->color;
                $stockUpdate->uom=$stock->uom;
                $stockUpdate->location=$location;
                $stockUpdate->store=$findGrnDetailLine->main_store;
                $stockUpdate->sub_store=$findGrnDetailLine->sub_store;
                $stockUpdate->bin=$binID->store_bin_id;
                $stockUpdate->status=1;
                $stockUpdate->standard_price=$findGrnDetailLine->standard_price;
                $stockUpdate->purchase_price=$findGrnDetailLine->purchase_price;
                $stockUpdate->qty=$_tempQty;
                $stockUpdate->save();
              }
            }



            $trimPacking->lot_no=$data->lot_no;
            $trimPacking->batch_no=$data->batch_no;
            $trimPacking->box_no=$data->box_no;
            $trimPacking->received_qty=$data->received_qty;
            $trimPacking->qty=$data->received_qty;
            $trimPacking->bin=$binID->store_bin_id;
            $trimPacking->shade=$data->shade;
            $trimPacking->comment=$data->comment;
            $trimPacking->invoice_no=$request->invoiceNo;
            $trimPacking->grn_detail_id=$request->grn_detail_id;
            $trimPacking->status=1;
            $trimPacking->save();


          }

          return response([ 'data' => [
            'message' => 'Trim Packing Detail Saved successfully',
            'trimPacking' => $trimPacking
            ]
          ], Response::HTTP_CREATED );


        }

        else
        {
            $errors = $store->errors();// failure, get errors
            return response(['errors' => ['validationErrors' => $errors]], Response::HTTP_UNPROCESSABLE_ENTITY);
        }




    }





}
