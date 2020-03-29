<?php

namespace App\Http\Controllers\Store;

use App\Models\Store\BinTransfer;
use App\Models\Store\Stock;
use App\Models\Store\StockTransaction;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Store\BinTransferDetail;

class BinTransferController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //dd($request);
       /* $header = new BinTransfer;
        $header->sub_store = $request->sub_store;
        $header->created_by = 58814;
        $header->save();*/

        //$block = BinTransfer::with('store_bin_transfer_header.transfer_id', 'transfer_id')->where('transfer_id', 18)->get();
        //$users = $this->user->with('salutation')->all();
        //dd($block);
        $binTransferData = BinTransfer::getBinTransferDeiatls(18);


       // BinTransfer::where('item_code', '=', $request->id)->update(['status' => 0]);

        foreach($binTransferData as $trData){

            //Update minus record
            $st = new StockTransaction;
            $st->doc_num = $trData['transfer_id'];
            $st->doc_type = 'BIN_TRANSFER';
            $st->customer_po_id = $trData['customer_po_id'];
            $st->style_id = $trData['style'];
            $st->material_code = $trData['material_id'];
            $st->size = $trData['size'];
            $st->color = $trData['color'];
            $st->uom = $trData['uom'];
            $st->qty = $trData['qty']*-1;
            $st->main_store = 1;
            $st->sub_store = $trData['sub_store'];
            $st->location = 2;
            $st->bin = $trData['from_bin'];
            $st->status = 'CONFIRM';
            $st->save();

            //Update plus record
            $st2 = new StockTransaction;
            $st2->doc_num = $trData['transfer_id'];
            $st2->doc_type = 'BIN_TRANSFER';
            $st2->customer_po_id = $trData['customer_po_id'];
            $st2->style_id = $trData['style'];
            $st2->material_code = $trData['material_id'];
            $st2->size = $trData['size'];
            $st2->color = $trData['color'];
            $st2->uom = $trData['uom'];
            $st2->qty = $trData['qty'];
            $st2->main_store = 1;
            $st2->sub_store = $trData['sub_store'];
            $st2->location = 2;
            $st2->bin = $trData['to_bin'];
            $st2->status = 'CONFIRM';
            $st2->save();


            //Minus Record (Stock Update)
            $stock = Stock::where('style_id', $st->style_id)
                ->where('color', $st->color)
                ->where('size', $st->size)
                ->where('material_code', $st->material_code)
                ->where('bin', $st->bin)
                ->where('location', $st->location)
                ->where('customer_po_id', $st->customer_po_id)
                ->first();

            //Updating Stock
            $currStock = $stock->total_qty + $st->qty;

            Stock::where('id', $stock->id)
                ->update(['total_qty' => $currStock]);

            //Plus Record
            $stockUpdate = Stock::where('style_id', $st2->style_id)
                ->where('color', $st2->color)
                ->where('size', $st2->size)
                ->where('material_code', $st2->material_code)
                ->where('bin', 2)
                ->where('location', $st2->location)
                ->where('customer_po_id', $st2->customer_po_id)
                ->first();


            //Updating Stock
            if($stockUpdate){
                $currStock = $stockUpdate->total_qty + $st2->qty;
                $pp = Stock::where('id', $stockUpdate->id)
                    ->update(['total_qty' => $currStock]);
                var_dump($stockUpdate->id);
            }else{
                $newStock = new Stock;
                $newStock->customer_po_id = $trData['customer_po_id'];
                $newStock->style_id = $trData['style'];
                $newStock->size = $trData['size'];
                $newStock->color = $trData['color'];
                $newStock->location = 2;
                $newStock->store = 1;
                $newStock->sub_store = $request->sub_store;
                $newStock->bin = $trData['to_bin'];
                $newStock->uom = $trData['uom'];
                $newStock->material_code = $trData['material_id'];
                $newStock->inv_qty = $trData['qty'];
                $newStock->total_qty = $trData['qty'];
                $newStock->save();

            }


        }



    }

    public function addBinTrnsfer(Request $request){
         $header = new BinTransfer;
         $header->sub_store = $request->sub_store;
         $header->created_by = 58814;
         $header->save();

        foreach($request->binModalData as $bin){
            $modal = new BinTransferDetail;
            $modal->sub_store = 100;
            $modal->transfer_id = $header->id;
            $modal->from_bin = 100;
            $modal->to_bin = 1;
            $modal->style = $bin['style_id'];
            $modal->color = $bin['colour'];
            $modal->qty = $bin['qty'];
            $modal->color = $bin['color_id'];
            $modal->size = $bin['size_id'];
            $modal->uom = $bin['uom_id'];
            $modal->material_id = $bin['material_id'];
            $modal->save();
        }

        return $header->id;
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }

    public function loadBinQty(Request $request){
       $stock = Stock::getBinStock($request->id);

        return response([
            'data' => $stock
        ]);
    }

    public function loadAddedBinQty(Request $request){
        $stock = BinTransfer::getAddedBinStock($request->id);
        //$stock = Stock::getBinStock(1);

        return response([
            'data' => $stock
        ]);
    }




}
