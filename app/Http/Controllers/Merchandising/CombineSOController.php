<?php

namespace App\Http\Controllers\Merchandising;

use App\Models\Merchandising\CustomerOrderDetails;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Merchandising\CostingSOCombine;
use App\Models\Merchandising\BulkCosting;
use App\Models\Merchandising\BulkCostingDetails;
use DB;
use Illuminate\Http\Response;

class CombineSOController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $type = $request->type;
        if ($type == 'getCostingsForCombine'){
            $styleId = $request->style_id;
            $fields = $request->fields;
            return response([
                'data' => $this->getCostingDataByStyle($styleId, $fields)
            ]);
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $maxCmb = DB::table('merc_costing_so_combine')->where('costing_id', $request->costing_id)->max('comb_id');
        $comId = $maxCmb +1;

        $errors = '';
        $saveStatus = false;

        foreach ($request->soList as $item) {
            // Check SO already combined

            $chkCmb = DB::table('merc_costing_so_combine')
                ->where('feature_id', $request->feature_id)
                ->where('details_id', $item['details_id'])
                ->where('color', $item['color_id'])
                ->first();

            if ($chkCmb === null) {
                if($item['item_select'] == true) {
                    $modal = new CostingSOCombine;
                    $modal->costing_id = $request->costing_id;
                    $modal->feature_id = $request->feature_id;
                    $modal->color = $item['color_id'];
                    $modal->details_id = $item['details_id'];
                    $modal->qty = $item['qty'];
                    $modal->comb_id = $comId;
                    $modal->created_by = auth()->payload()['user_id'];
                    $saveStatus = $modal->save();

                    //Update customer order detail status
                    $cusOrder = CustomerOrderDetails::where('details_id', '=', $item['details_id'])->first();
                    $cusOrder->delivery_status = 'RELEASED';
                    $cusOrder->save();
                    //dd($cusOrder);
                }
            }else{
                return response(['response' => ['type' => 'error'],['validationErrors' => 'Already Combined']], 200);
            }

        }

        if($saveStatus){
            return response(['response' => ['type' => 'success'], ['message' => 'Successfully Added']], 200);
        }



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

    //get filtered fields only
    private function getCostingDataByStyle($styleId , $fields = null)
    {
        $fields = explode(',', $fields);
        return BulkCosting::getCostingCombineData($styleId);

    }
}
