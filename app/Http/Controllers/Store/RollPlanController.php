<?php

namespace App\Http\Controllers\Store;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use App\Http\Controllers\Controller;
use App\Models\stores\PoOrderDetails;
use App\Models\stores\PoOrderHeader;
use App\Models\stores\PoOrderType;
use App\Models\stores\RollPlan;
use Illuminate\Support\Facades\DB;
use Exception;
use App\Libraries\AppAuthorize;
use App\Libraries\CapitalizeAllFields;
class RollPlanController extends Controller
{

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
      $rollPlan = new RollPlan();
      //dd($request->invoiceNo);
    if($rollPlan->validate($request->all()))
      {
        for($i=0;$i<count($request->dataset);$i++)
        {
        $rollPlan = new RollPlan();
        $data=$request->dataset[$i];
        $data=(object)$data;
        $binID=DB::table('org_store_bin')->where('store_bin_name','=',$data->bin)->select('store_bin_id')->first();
        //dd();
        $rollPlan->lot_no=$data->lot_no;
        $rollPlan->batch_no=$data->batch_no;
        $rollPlan->roll_no=$data->roll_no;
        $rollPlan->qty=$data->qty;
        $rollPlan->received_qty=$data->received_qty;
        $rollPlan->bin=$binID->store_bin_id;
        ///dd($binID);
        $rollPlan->width=$data->width;
        $rollPlan->shade=$data->shade;
        $rollPlan->comment=$data->comment;
        //$rollPlan->barcode=$data->barcode;
        $rollPlan->invoice_no=$request->invoiceNo;
        $rollPlan->grn_detail_id=$request->grn_detail_id;
        $rollPlan->status = 1;
        $rollPlan->save();
        }
        return response([ 'data' => [
          'message' => 'Roll Plan Updated successfully',
          'rollPlan' => $rollPlan
          ]
        ], Response::HTTP_CREATED );
     }
      else
      {
          $errors = $store->errors();// failure, get errors
          return response(['errors' => ['validationErrors' => $errors]], Response::HTTP_UNPROCESSABLE_ENTITY);
      }


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

        $roll_plan_list = RollPlan::join('store_grn_detail','store_roll_plan.grn_detail_id','=','store_grn_detail.grn_detail_id')
        ->join('store_grn_header','store_grn_detail.grn_id','=','store_grn_header.grn_id')
        ->join('item_master','store_grn_detail.item_code','=','item_master.master_id')
        ->join('org_color','store_grn_detail.color','=','org_color.color_id')
        ->select('store_roll_plan.*','store_grn_detail.status','store_grn_header.grn_number','org_color.color_name','item_master.master_description','store_grn_detail.grn_qty')
        ->where('store_grn_header.grn_number'  , 'like', $search.'%' )
        ->orWhere('org_color.color_name'  , 'like', $search.'%' )
        ->orWhere('item_master.master_description','like',$search.'%')
        ->orWhere('store_grn_detail.grn_qty','like',$search.'%')
        ->groupBy('store_roll_plan.grn_detail_id')
        ->orderBy($order_column, $order_type)
        ->offset($start)->limit($length)->get();

        $roll_plan_count = RollPlan::join('store_grn_detail','store_roll_plan.grn_detail_id','=','store_grn_detail.grn_detail_id')
        ->join('store_grn_header','store_grn_detail.grn_id','=','store_grn_header.grn_id')
        ->join('item_master','store_grn_detail.item_code','=','item_master.master_id')
        ->join('org_color','store_grn_detail.color','=','org_color.color_id')
        ->select('store_roll_plan.*','store_grn_detail.status','store_grn_header.grn_number','org_color.color_name','item_master.master_description','store_grn_detail.grn_qty')
        ->where('store_grn_header.grn_number'  , 'like', $search.'%' )
        ->orWhere('org_color.color_name'  , 'like', $search.'%' )
        ->orWhere('item_master.master_description','like',$search.'%')
        ->orWhere('store_grn_detail.grn_qty','like',$search.'%')
        ->groupBy('store_roll_plan.grn_detail_id')
        ->count();

        echo json_encode([
            "draw" => $draw,
            "recordsTotal" => $roll_plan_count,
            "recordsFiltered" => $roll_plan_count,
            "data" => $roll_plan_list
        ]);

  }

 public function show($id){

   $roll_plan_data= RollPlan::select('store_roll_plan.*','org_store_bin.store_bin_name')
   ->join('org_store_bin','store_roll_plan.bin','=','org_store_bin.store_bin_id')
   ->where('grn_detail_id','=',$id)->get();
   return response([ 'data' =>[
                      'data' => $roll_plan_data,
                      'status'=>1
                    ]]);

}

//update a Color
public function update(Request $request, $id){


  for($i=0;$i<count($request->dataset);$i++)
  {
    $dataset=$request->dataset;
    $rollPlan = RollPlan::find($dataset[$i]['roll_plan_id']);
    //dd($rollPlan);
    $data=$dataset[$i];
    $data=(object)$data;
    $rollPlan->lot_no=$data->lot_no;
    $rollPlan->batch_no=$data->batch_no;
    $rollPlan->roll_no=$data->roll_no;
    $rollPlan->qty=$data->qty;
    $rollPlan->received_qty=$data->received_qty;
    $rollPlan->bin=$data->bin;
    ///dd($binID);
    $rollPlan->width=$data->width;
    $rollPlan->shade=$data->shade;
    $rollPlan->comment=$data->comment;
    //$rollPlan->barcode=$data->barcode;
    $rollPlan->invoice_no=$data->invoice_no;
    $rollPlan->grn_detail_id=$data->grn_detail_id;
    $rollPlan->status = 1;
    $rollPlan->save();

  //  dd($rollPlan);

  }

  return response([ 'data' => [
    'message' => 'Roll Plan Updated successfully',
    ]
  ], Response::HTTP_CREATED );


}



}
