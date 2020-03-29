<?php

namespace App\Http\Controllers\Store;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\store\Stock;
use App\itemCreation;
class OrderTransferController extends Controller
{
    public function index()
    {
        $dataList1 = Stock::select('*')
            ->where('customer_po_id'  , '=', 1 )
            ->get()
            ->toArray();

        $dataList2 = Stock::select('*')
            ->where('customer_po_id'  , '=', 2 )
            ->get()
            ->toArray();

        $dataList=array();
        foreach ($dataList1 AS $List1){
            foreach ($dataList2 AS $List2){
              if($List1['item_code'] == $List2['item_code']){
                  $itemcreation = itemCreation::where('master_code', '=', $List2['item_code'])->get()->toArray();
                  $List2['itemCreation']=$itemcreation;
                  $dataList[]=$List2;
              }
            }
        }

        return $dataList;
    }
}
