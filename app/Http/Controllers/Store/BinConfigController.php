<?php

namespace App\Http\Controllers\Store;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Http\Controllers\Controller;
use App\Models\Store\StoreBinAllocation;
use App\Models\Finance\Item\Category;

class BinConfigController extends Controller {

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request) {
        $type = $request->type;

        if ($type == 'getBinData') {
            $binId = $request->input('bin_id');
            return response($this->getBinData($binId));
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request) {

        //if ($binConfig->validate($request->all())) {

        $items = $request->all()['items'];
        $binConfig = null;
        foreach ($items as $item) {

            $existsData = StoreBinAllocation::getExistRecords($request->input('bin_id'), $request->input('category_name'), $item['subCategoryId']);
           // print_r(isset($allocatedBin[0]));
            if (isset($allocatedBin[0])) {
                foreach ($existsData as $binConfig) {

                    $binConfig->fill($request->all());
                    $binConfig->status = ($item['itemCheckedbox'] == true) ? 1 : 0;
                    $binConfig->max_capacity = $item['capacity'];
                    $binConfig->height = $item['height'];
                    $binConfig->length = $item['length'];
                    $binConfig->width = $item['width'];
                    $binConfig->save();
                }
            } else {
                if ($item['itemCheckedbox'] == true) {
                    $binConfig = new StoreBinAllocation();

                    $binConfig->store_bin_id = $request->input('bin_id');
                    $binConfig->item_category_id = $request->input('category_name');
                    $binConfig->fill($request->all());
                    $binConfig->status = 1;
                    $binConfig->max_capacity = $item['capacity'];
                    $binConfig->height = $item['height'];
                    $binConfig->length = $item['length'];
                    $binConfig->item_subcategory_id = $item['subCategoryId'];
                    $binConfig->width = $item['width'];
                    $binConfig->save();
                }
            }
        }


        return response(['data' => [
                'message' => ' Bin Config Saved successfully',
                'storeBin' => $binConfig
            ]
                ], Response::HTTP_CREATED);

    }

    private function getBinData($binId) {
        $allocatedArray = array();
        $allocatedBin = StoreBinAllocation::getAllocatedItemByBin($binId);
        $configured = false;

        if (isset($allocatedBin[0])) {
            $configured = true;
            $categoryId = $allocatedBin[0]->item_category_id;
            $itemList = Category::getItemListByCategory($categoryId);

            foreach ($itemList as $item) {
                $allocatedArray[$item->subcategory_id]['allocation_id'] = 0;
                $allocatedArray[$item->subcategory_id]['item_name'] = $item->subcategory_name;
                $allocatedArray[$item->subcategory_id]['item_category_name'] = $item->category_name;
                $allocatedArray[$item->subcategory_id]['max_capacity'] = 0;
                $allocatedArray[$item->subcategory_id]['width'] = 0;
                $allocatedArray[$item->subcategory_id]['height'] = 0;
                $allocatedArray[$item->subcategory_id]['length'] = 0;
                $allocatedArray[$item->subcategory_id]['item_category_id'] = $item->category_id;
                $allocatedArray[$item->subcategory_id]['item_subcategory_id'] = $item->subcategory_id;
            }

            foreach ($allocatedBin as $aBin) {
                $allocatedArray[$aBin->item_subcategory_id]['allocation_id'] = $aBin->allocation_id;
                $allocatedArray[$aBin->item_subcategory_id]['max_capacity'] = $aBin->max_capacity;
                $allocatedArray[$aBin->item_subcategory_id]['width'] = $aBin->width;
                $allocatedArray[$aBin->item_subcategory_id]['height'] = $aBin->height;
                $allocatedArray[$aBin->item_subcategory_id]['length'] = $aBin->length;
                $allocatedArray[$aBin->item_subcategory_id]['item_category_id'] = $aBin->item_category_id;
                $allocatedArray[$aBin->item_subcategory_id]['item_subcategory_id'] = $aBin->item_subcategory_id;
            }
        }

        return [
            'data' => [
                'configured' => $configured,
                'allocatedArray' => array_values($allocatedArray)
            ]
        ];
    }

}
