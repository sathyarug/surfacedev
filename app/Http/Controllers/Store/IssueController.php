<?php

namespace App\Http\Controllers\Store;
use App\Libraries\UniqueIdGenerator;
use App\Models\Store\IssueDetails;
use App\Models\Store\IssueHeader;
use App\Models\Store\MRNHeader;
use App\Models\Store\MRNDetail;
use App\Models\Store\GrnDetail;
use App\Models\stores\RollPlan;
use App\Models\Merchandising\Item\Item;
use App\Models\Merchandising\Item\Category;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Http\Controllers\Controller;
use App\Models\Org\ConversionFactor;
use App\Models\Finance\Transaction;
use App\Models\Store\StockTransaction;
use Illuminate\Support\Facades\DB;
use App\Models\Merchandising\ShopOrderDetail;
use App\Models\Store\TrimPacking;
use App\Models\Store\Stock;
class IssueController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $type = $request->type;
        $fields = $request->fields;
        $active = $request->status;
        if($type == 'datatable') {
            $data = $request->all();
            return response($this->datatable_search($data));
        }elseif($type == 'issue_details'){
            $id = $request->id;
            return response(['data' => $this->getIssueDetails($id)]);
        }else if($type == 'auto')    {
            $search = $request->search;
            return response($this->autocomplete_search($search));
        }else if($type == 'auto_batch')    {
            $search = $request->search;
            return response($this->autocomplete_batch_search($search));
        }else if($type == 'auto_ins_status')    {
            $search = $request->search;
            return response($this->autocomplete_ins_status_search($search));
        }else{
            $loc_id = $request->loc;
            return response(['data' => $this->list($active, $fields, $loc_id)]);
        }
    }

    /**
    * Store a newly created resource in storage.
    *
    *@param  \Illuminate\Http\Request  $request
    *@return \Illuminate\Http\Response
    */

    private function autocomplete_ins_status_search($search)
    {
      $query = DB::table('store_inspec_status')
      ->select('*')
      //->where([['store_inspec_status.status_name', 'like', '%' . $search . '%'],])
      ->get();
      return $query;
    }

    private function autocomplete_batch_search($search)
    {
      $trim_pack = TrimPacking::select('batch_no')
      ->where([['batch_no', 'like', '%' . $search . '%'],])
      ->groupBy('batch_no');

      $roll_plan = RollPlan::select('batch_no')
      ->where([['batch_no', 'like', '%' . $search . '%'],])
      ->groupBy('batch_no')
      ->union($trim_pack)
      ->get();

      return $roll_plan;
    }

    //search Color for autocomplete
    private function autocomplete_search($search)
    {
      $lists = IssueHeader::select('*')
      ->where([['issue_no', 'like', '%' . $search . '%'],]) ->get();
      return $lists;
    }

    public function store(Request $request)
    {
      $header=$request->header;
      $details=$request->dataset;
      $locId=auth()->payload()['loc_id'];
      if($header['issue_no']!=0){
        $issueNo=$header['issue_no'];
        $issueHeader=IssueHeader::where('issue_no','=',$issueNo)->first();
      }
      else if($header['issue_no']==0){
      $issueHeader=new IssueHeader();
      $unId = UniqueIdGenerator::generateUniqueId('ISSUE', auth()->payload()['loc_id']);
      $issueHeader->mrn_id=$header['mrn_no']['mrn_id'];
      $issueHeader->issue_no=$unId;
      $issueHeader->status=1;
      $issueHeader->issue_status="PENDING";
      $issueHeader->save();
    }
      for($i=0;$i<sizeof($details);$i++){

          if(empty($details[$i]['isEdited'])==false&&$details[$i]['isEdited']==1){

              $issueDetails=new IssueDetails();
              $mrnDetail=MRNHeader::join('store_mrn_detail','store_mrn_header.mrn_id','=','store_mrn_detail.mrn_id')
                                   ->where('store_mrn_detail.shop_order_detail_id','=',$details[$i]['shop_order_detail_id'])
                                   ->first();
                                   //dd();
              $issueDetails->issue_id=$issueHeader->issue_id;
              $issueDetails->mrn_detail_id=$mrnDetail->mrn_detail_id;
              $issueDetails->item_id=$details[$i]['item_code'];


              $issueDetails->qty=$details[$i]['issue_qty'];
              $issueDetails->location_id=$locId;
              $issueDetails->store_id=$details[$i]['main_store'];
              $issueDetails->sub_store_id=$details[$i]['sub_store'];
              $issueDetails->bin=$details[$i]['bin'];
              $issueDetails->status=1;
              $issueDetails->issue_status="PENDING";

              $itemType=Item::join('item_category','item_master.category_id','=','item_category.category_id')
                             ->select('item_category.category_code')
                             ->where('item_master.master_id','=',$details[$i]['item_code'])
                             ->first();

                if($itemType->category_code=="FAB"){
                $rollPlan =RollPlan::find($details[$i]['roll_plan_id']);
                $rollPlan->qty=$details[$i]['qty']-$details[$i]['issue_qty'];
                $rollPlan->save();
                $issueDetails->item_detail_id=$details[$i]['roll_plan_id'];
              }
              else if($itemType->category_code=!"FAB") {
               $trimPacking=TrimPacking::find($details[$i]['trim_packing_id']);
                $trimPacking->qty=$details[$i]['qty']-$details[$i]['issue_qty'];
                $$trimPacking->save();
                $issueDetails->item_detail_id=$details[$i]['trim_packing_id'];
              }
              $issueDetails->save();

                $transaction = Transaction::where('trans_description', 'ISSUE')->first();
                //$mrnDetail=MRNDetail::find($issueDetails->mrn_detail_id);
                $st = new StockTransaction;
                $st->status = 'PENDING';
                $st->doc_type = $transaction->trans_code;
                $st->doc_num = $issueHeader->issue_id;
                $st->style_id = $details[$i]['style_id'];
                $st->main_store = $details[$i]['main_store'];
                $st->sub_store =$details[$i]['sub_store'];
                $st->item_code = $details[$i]['item_code'];
                $st->size = $mrnDetail->size_id;
                $st->color = $mrnDetail->color_id;
                //$st->uom = $mrnDetail->uom;
                $st->customer_po_id=$mrnDetail->cust_order_detail_id;
                $item=Item::find($details[$i]['item_code']);
                if($item->inventory_uom!= $mrnDetail->uom){
                 $st->uom  = $item->inventory_uom;
                  $_uom_unit_code=UOM::where('uom_id','=',$item->inventory_uom)->pluck('uom_code');
                  $_uom_base_unit_code=UOM::where('uom_id','=',$mrnDetail->uom)->pluck('uom_code');
                  //get convertion equatiojn details
                  //dd($_uom_unit_code);
                  $ConversionFactor=ConversionFactor::select('*')
                                                      ->where('unit_code','=',$_uom_unit_code[0])
                                                      ->where('base_unit','=',$_uom_base_unit_code[0])
                                                      ->first();
                                                      // convert values according to the convertion rate
                                                      $st->qty =(double)($details[$i]['issue_qty'] *$ConversionFactor->present_factor);

                }
                else if($item->inventory_uom==$mrnDetail->uom){
                  $st->uom = $mrnDetail->uom;
                  $st->qty =(double)$details[$i]['issue_qty'];
                }
                $st->shop_order_id =$details[$i]['shop_order_id'];
                $st->shop_order_detail_id =$details[$i]['shop_order_detail_id'];
                $st->location = auth()->payload()['loc_id'];
                $st->bin = $details[$i]['bin'];
                $st->created_by = auth()->payload()['user_id'];
              //  dd($st);
                $st->save();



          }


      }
      return response([ 'data' => [
        'message1'=>'Issue No ',
        'message2' => ' Saved Successfully',
        'status'=>1,
        'issueNo'=>$issueHeader->issue_no
        ]
      ], Response::HTTP_CREATED );
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

    //get searched Colors for datatable plugin format
    private function datatable_search($data)
    {
      //dd(csc);

          $start = $data['start'];
          $length = $data['length'];
          $draw = $data['draw'];
          $search = $data['search']['value'];
          $order = $data['order'][0];
          $order_column = $data['columns'][$order['column']]['data'];
          $order_type = $order['dir'];

          $issue_list = IssueHeader::join('store_issue_detail','store_issue_detail.issue_id','=','store_issue_header.issue_id')
                                    ->join('item_master','store_issue_detail.item_id','=','item_master.master_id')
                                    ->join('org_store','store_issue_detail.store_id','=','org_store.store_id')
                                    ->join('org_substore','store_issue_detail.sub_store_id','=','org_substore.substore_id')
                                    ->join('org_store_bin','store_issue_detail.bin','=','org_store_bin.store_bin_id')
                                    


          ->select('store_issue_detail.*','store_issue_header.*','org_store.store_name','org_substore.substore_name','org_substore.substore_name','item_master.master_description')
          ->where('store_issue_header.issue_no'  , 'like', $search.'%' )
          ->orWhere('item_master.master_code'  , 'like', $search.'%' )
          ->orWhere('org_substore.substore_name','like',$search.'%')
          ->orWhere('org_store_bin.store_bin_description','like',$search.'%')
          ->orderBy($order_column, $order_type)
          ->offset($start)->limit($length)->get();
          //dd($issue_list);

          $issue_list_count = IssueHeader::join('store_issue_detail','store_issue_detail.issue_id','=','store_issue_header.issue_id')
                                    ->join('item_master','store_issue_detail.item_id','=','item_master.master_id')
                                    ->join('org_store','store_issue_detail.store_id','=','org_store.store_id')
                                    ->join('org_substore','store_issue_detail.sub_store_id','=','org_substore.substore_id')
                                    ->join('org_store_bin','store_issue_detail.bin','=','org_store_bin.store_bin_id')


          ->select('store_issue_detail.*','store_issue_header.*','org_store.store_name','org_substore.substore_name','org_substore.substore_name')
          ->where('store_issue_header.issue_no'  , 'like', $search.'%' )
          ->orWhere('item_master.master_code'  , 'like', $search.'%' )
          ->orWhere('org_substore.substore_name','like',$search.'%')
          ->orWhere('org_store_bin.store_bin_description','like',$search.'%')
          ->count();

          echo json_encode([
              "draw" => $draw,
              "recordsTotal" => $issue_list_count,
              "recordsFiltered" => $issue_list_count,
              "data" => $issue_list
          ]);

    }

    public function list($active, $fields, $loc){
        $query = null;
        if($fields == null || $fields == '') {
            $query = IssueHeader::select('*');
        }else{
            $fields = explode(',', $fields);
            $query = IssueHeader::select($fields);
            if($active != null && $active != ''){
                $payload = auth()->payload();
                $query->where([['status', '=', $active], ['location', '=', $loc]]);
            }

        }
        return $query->get();
    }

    public function getIssueDetails($id){
        return IssueDetails::getIssueDetailsForReturn($id);
    }

   public function loadMrnData(Request $request){

     //$issueHeader=IssueHeader::
     $mrndetails=MRNHeader::join('store_mrn_detail','store_mrn_header.mrn_id','=','store_mrn_detail.mrn_id')
                            ->join('item_master','store_mrn_detail.item_id','=','item_master.master_id')
                            ->leftjoin('org_color','store_mrn_detail.color_id','=','org_color.color_id')
                            ->leftJoin('org_size','store_mrn_detail.size_id','=','org_size.size_id')
                            ->join('merc_shop_order_detail','store_mrn_detail.shop_order_detail_id','=','merc_shop_order_detail.shop_order_detail_id')
                            ->join('merc_po_order_details','merc_shop_order_detail.shop_order_detail_id','=','merc_po_order_details.shop_order_detail_id')
                            ->join('org_uom as for_po_uom','merc_po_order_details.uom','=','for_po_uom.uom_id')
                            ->join('org_uom as for_inv_uom','item_master.inventory_uom','=','for_inv_uom.uom_id')
                            ->select('store_mrn_header.*','store_mrn_detail.*','item_master.master_code','item_master.master_description','org_color.color_name','merc_shop_order_detail.asign_qty','merc_shop_order_detail.balance_to_issue_qty','for_po_uom.uom_code','for_inv_uom.uom_code as inventory_uom')
                            ->where('store_mrn_header.mrn_id','=',$request->mrn_id)
                            ->get();

                            return response([
                                'data' => $mrndetails
                            ]);
      //dd($mrndetails);

    }


     public function loadBinDetails (Request $request){

       $pendingIssueQty=DB::SELECT("SELECT SUM(store_issue_detail.qty) as pendindg_qty

         From
         store_issue_header
         INNER JOIN store_issue_detail on store_issue_header.issue_id=store_issue_detail.issue_id
         INNER JOIN store_mrn_detail on store_issue_detail.mrn_detail_id=store_mrn_detail.mrn_detail_id
         where store_mrn_detail.shop_order_detail_id=$request->shop_order_detail_id
         AND store_mrn_detail.item_id=$request->item_id

       ");
       $locId=auth()->payload()['loc_id'];

        //dd((double)$pendingIssueQty[0]->pendindg_qty);
      /*if($request->requested_qty<=(double)$pendingIssueQty[0]->pendindg_qty){
        $grnDetails=[];

        return response([ 'data' => [
          'data' => $grnDetails,
          'status'=>0,
          'message'=>"Requested Qty Isuued",
          'pending_qty'=>$pendingIssueQty[0]->pendindg_qty
          ]
        ], Response::HTTP_CREATED );

      }*/

       $itemType=Item::join('item_category','item_master.category_id','=','item_category.category_id')
                      ->select('item_category.category_code')
                      ->where('item_master.master_id','=',$request->item_id)
                      ->first();
                    //dd($itemType);
              if($itemType->category_code=="FAB"){
                  //dd($request->shop_order_detail_id);
                  $grnDetails=GrnDetail::join('store_roll_plan','store_grn_detail.grn_detail_id','=','store_roll_plan.grn_detail_id')
                                        ->join('store_fabric_inspection','store_roll_plan.roll_plan_id','=','store_fabric_inspection.roll_plan_id')
                                        ->join('org_store_bin','store_roll_plan.bin','=','org_store_bin.store_bin_id')
                                        ->join('store_grn_header','store_grn_detail.grn_id','=','store_grn_header.grn_id')
                                        ->select('store_roll_plan.*','org_store_bin.store_bin_name','store_grn_detail.shop_order_detail_id','store_grn_detail.shop_order_id','store_grn_detail.item_code','store_grn_header.*','store_grn_detail.style_id')
                                       ->where('store_grn_detail.shop_order_detail_id','=',$request->shop_order_detail_id)
                                       ->where('store_grn_header.location','=',$locId)
                                       ->where('store_fabric_inspection.inspection_status','=',"PASS")
                                       ->where('store_roll_plan.qty','>',0)
                                       ->get();

                                      //dd($grnDetails);


              }
              else if ($itemType->category_code!="FAB"){
                $grnDetails=GrnDetail::join('store_trim_packing_detail','store_grn_detail.grn_detail_id','=','store_trim_packing_detail.grn_detail_id')
                                      ->join('org_store_bin','store_trim_packing_detail.bin','=','org_store_bin.store_bin_id')
                                      ->join('store_grn_header','store_grn_detail.grn_id','=','store_grn_header.grn_id')
                                      ->select('store_trim_packing_detail.*','org_store_bin.store_bin_name','store_grn_detail.shop_order_detail_id','store_grn_detail.shop_order_id','store_grn_detail.item_code','store_grn_header.*','store_grn_detail.style_id')
                                      ->where('store_grn_detail.shop_order_detail_id','=',$request->shop_order_detail_id)
                                      ->where('store_grn_header.location','=',$locId)
                                      ->where('store_trim_packing_detail.qty','>',0)
                                      ->get();
              }

              if($pendingIssueQty[0]->pendindg_qty==null){
               $pendingIssueQty[0]->pendindg_qty=0;
              }
              return response([ 'data' => [
                'data' => $grnDetails,
                'status'=>1,
                'pending_qty'=>$pendingIssueQty[0]->pendindg_qty
                ]
              ], Response::HTTP_CREATED );
     }

     public function confirmIssueData(Request $request) {

      $headerData=IssueHeader::where('issue_no','=',$request->header['issue_no'])
                              ->where('mrn_id','=',$request->header['mrn_id'])
                              ->first();

       $headerData->issue_status="CONFIRM";
       $headerData->save();
       $issueHeader=IssueHeader::join('store_issue_detail','store_issue_header.issue_id','=','store_issue_detail.issue_id')
                              ->join('store_mrn_detail','store_issue_detail.mrn_detail_id','=','store_mrn_detail.mrn_detail_id')
                              ->join('store_mrn_header','store_mrn_detail.mrn_id','=','store_mrn_header.mrn_id')
                              ->where('store_issue_header.issue_no','=',$request->header['issue_no'])
                              ->Where('store_issue_header.mrn_id','=',$request->header['mrn_id'])
                              ->select('store_issue_detail.*','store_issue_header.*','store_mrn_detail.uom','store_mrn_header.style_id','store_mrn_detail.shop_order_id','store_mrn_detail.shop_order_detail_id','store_mrn_detail.cust_order_detail_id','store_mrn_detail.color_id')
                              ->get();
                             //dd($issueHeader);
                    for($i=0;$i<count($issueHeader);$i++){
                          $issueDetails=new IssueDetails();
                          $issueDetails=IssueDetails::find($issueHeader[$i]['issue_detail_id']);
                          $issueDetails->issue_status="CONFIRM";
                          $issueDetails->save();
                          //dd()
                          $shopOrderDetail=ShopOrderDetail::find($issueHeader[$i]['shop_order_detail_id']);
                          //dd($issueDetails[$i]['qty']);
                          $shopOrderDetail->balance_to_issue_qty=(double)$shopOrderDetail->balance_to_issue_qty-(double)$issueDetails->qty;
                          $shopOrderDetail->issue_qty=(double)$shopOrderDetail->issue_qty+(double)$issueDetails->qty;
                          //dd($shopOrderDetail);
                          $shopOrderDetail->save();
                          $transaction = Transaction::where('trans_description', 'ISSUE')->first();
                          //$mrnDetail=MRNDetail::find($issueDetails->mrn_detail_id);
                          $st = new StockTransaction;
                          $st->status = 'CONFIRM';
                          $st->doc_type = $transaction->trans_code;
                          $st->doc_num = $issueHeader[$i]['issue_id'];
                          $st->style_id = $issueHeader[$i]['style_id'];
                          $st->main_store = $issueHeader[$i]['store_id'];
                          $st->sub_store =$issueHeader[$i]['sub_store_id'];
                          $st->item_code = $issueHeader[$i]['item_id'];
                          $st->size = $issueHeader[$i]['size_id'];
                          $st->color = $issueHeader[$i]['color_id'];
                          //$st->uom = $mrnDetail->uom;
                          $st->customer_po_id=$issueHeader[$i]['cust_order_detail_id'];



                          $item=Item::find($issueHeader[$i]['item_id']);
                          if($item->inventory_uom!=$issueHeader[$i]['uom']){
                            $st->uom  = $item->inventory_uom;
                             $_uom_unit_code=UOM::where('uom_id','=',$item->inventory_uom)->pluck('uom_code');
                             $_uom_base_unit_code=UOM::where('uom_id','=',$mrnDetail->uom)->pluck('uom_code');
                             //get convertion equatiojn details
                             //dd($_uom_unit_code);
                             $ConversionFactor=ConversionFactor::select('*')
                                                                 ->where('unit_code','=',$_uom_unit_code[0])
                                                                 ->where('base_unit','=',$_uom_base_unit_code[0])
                                                                 ->first();
                                                                 // convert values according to the convertion rate
                                                                  $st->qty =(double)($issueHeader[$i]['qty'] *$ConversionFactor->present_factor);

                                  }
                          if($item->inventory_uom==$issueHeader[$i]['uom']){
                            $st->uom=$issueHeader[$i]['uom'];
                            $st->qty =$issueHeader[$i]['qty'];
                    }
                    $st->shop_order_id =$issueHeader[$i]['shop_order_id'];
                    $st->shop_order_detail_id =$issueHeader[$i]['shop_order_detail_id'];
                    $st->location = auth()->payload()['loc_id'];
                    $st->bin = $issueHeader[$i]['bin'];
                    $st->created_by = auth()->payload()['user_id'];
                    $st->direction="-";
                  //dd($st);
                    $st->save();

                    /*$findStoreStockLine=DB::SELECT ("SELECT * FROM store_stock
                                                     where item_id=$issueDetails->item_id
                                                     AND store=$issueDetails->store_id
                                                      AND sub_store=$issueDetails->sub_store_id
                                                      AND location=$st->location
                                                      AND bin=$issueDetails->bin
                                                      ");*/
                                                      $findStoreStockLine=DB::SELECT ("SELECT * FROM store_stock
                                                                                       where item_id=$issueDetails->item_id
                                                                                       AND shop_order_id=$st->shop_order_id
                                                                                       AND style_id=$st->style_id
                                                                                       AND shop_order_detail_id=$st->shop_order_detail_id
                                                                                       AND bin=$st->bin
                                                                                       AND store=$st->main_store
                                                                                       AND sub_store=$st->sub_store
                                                                                       AND location=$st->location");
                                                                                       //dd($findStoreStockLine);

                    $stock=Stock::find($findStoreStockLine[0]->id);
                    $stock->qty=(double)$stock->qty- (double)$st->qty;
                    //$stock->total_qty=(double)$stock->total_qty- (double)$st->qty;
                    $stock->save();
}

                 return response([ 'data' => [
                      'data' => $issueHeader,
                          'status'=>1,
                           'message'=>"Issue Confirmed sucessfully!"
  ]
], Response::HTTP_CREATED );


     }


}
