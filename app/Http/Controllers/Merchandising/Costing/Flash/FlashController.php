<?php

namespace App\Http\Controllers\Merchandising\Costing\Flash;

use App\Models\Merchandising\Costing\Flash\cost_flash_header;
use App\Models\Merchandising\Costing\Flash\cost_flash_details;
use App\Models\Merchandising\Costing\Flash\his_cost_flash_header;
use App\Models\Merchandising\Costing\Flash\his_cost_flash_details;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class FlashController extends Controller
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
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
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
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Merchandising\Costing\Flash\cost_flash_header  $cost_flash_header
     * @return \Illuminate\Http\Response
     */
    public function show(cost_flash_header $cost_flash_header)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Merchandising\Costing\Flash\cost_flash_header  $cost_flash_header
     * @return \Illuminate\Http\Response
     */
    public function edit(cost_flash_header $cost_flash_header)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Merchandising\Costing\Flash\cost_flash_header  $cost_flash_header
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, cost_flash_header $cost_flash_header)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Merchandising\Costing\Flash\cost_flash_header  $cost_flash_header
     * @return \Illuminate\Http\Response
     */
    public function destroy(cost_flash_header $cost_flash_header)
    {
        //
    }
    
    public function saveCostingHeader(Request $request){
        
        try{
            
            if(cost_flash_header::where("costing_id",$request->costing_id)->exists()){
                
                /*$costHeaderUpdate = cost_flash_header::where("costing_id",$request->costing_id)->first();
                $costHeaderUpdate->update(["order_qty"=>$request->order_qty, "season_id"=>$request->season_id, "order_smv"=>$request->sewing_smv, 
                                           "order_fob"=>$request->order_fob, "order_eff"=>$request->order_eff,"packing_smv"=>$request->packing_smv, 
                                           "labour_sub_cost"=>$request->labour_cost, "finance_cost"=>$request->finance_cost, "corporate_cost"=>$request->corporate_cost,
                                           "epm_rate"=>$request->epm, "netprofit"=>$request->np, "factory_cpm"=>$request->fac_cpm, "frontend_cpm"=>$request->front_cpm, "finance_rate"=>$request->fin_rate,
                                           "sewing_smv"=>$request->sewing_smv]);*/
                
                cost_flash_header::where("costing_id",$request->costing_id)
                                    ->update(['order_qty'=>$request->order_qty,'season_id'=>$request->season_id,'order_smv'=>$request->order_smv,'order_fob'=>$request->order_fob,'order_eff'=>$request->order_eff,
                                              'packing_smv'=>$request->packing_smv,'labour_sub_cost'=>$request->labour_cost,'finance_cost'=>$request->finance_cost,'corporate_cost'=>$request->corporate_cost,
                                              'epm_rate'=>$request->epm,'netprofit'=>$request->np,'factory_cpm'=>$request->fac_cpm,'frontend_cpm'=>$request->front_cpm,'finance_rate'=>$request->fin_rate,
                                              'sewing_smv'=>$request->sewing_smv]);
                $status = $request->costing_id;
            }else{
                
                $costingHeader = new cost_flash_header();        
                $costingHeader->style_id = $request->style_code;        
                $costingHeader->order_qty = $request->order_qty;        
                $costingHeader->season_id = $request->season_id;        
                $costingHeader->order_smv = $request->order_smv;        
                $costingHeader->order_fob = $request->order_fob;        
                $costingHeader->order_eff = $request->order_eff;        
                $costingHeader->sewing_smv = $request->sewing_smv;        
                $costingHeader->packing_smv = $request->packing_smv; 
                $costingHeader->labour_sub_cost = $request->labour_cost; 
                $costingHeader->finance_cost = $request->finance_cost; 
                $costingHeader->corporate_cost = $request->corporate_cost; 
                $costingHeader->epm_rate = $request->epm; 
                $costingHeader->netprofit = $request->np; 
                $costingHeader->factory_cpm = $request->fac_cpm; 
                $costingHeader->frontend_cpm = $request->front_cpm; 
                $costingHeader->finance_rate = $request->fin_rate; 
                $costingHeader->approval_no = 0;             
                $costingHeader->order_status = 0;

                $costingHeader->saveOrFail();
                
                $status = $costingHeader->costing_id;                
            }
                      
        } catch ( \Exception $ex) {
            $status = "fail";    
        }
        echo json_encode(array('status' => $status));          
    }
    
    public function setItemInactive(Request $request){
        
        try{
            
            /* Check whether costing id exist or not */
            if(cost_flash_details::where("costing_id",$request->costing_id)->exists()){
                
                /* If exist set status to zero for all items */
                $setInactiveStatus = cost_flash_details::where("costing_id",$request->costing_id)->first();
                $setInactiveStatus->update(["status"=>0]);                
            }
            
            $status = "success";
            
        } catch ( \Exception $ex) {
            $status = "fail";
        }
        echo json_encode(array('status' => $status));
        
    }
    
    public function saveCostingDetails(Request $request){
        
        try{
            
            if(cost_flash_details::where("costing_id",$request->costing_id)->where("master_id",$request->item_code)->exists()){
                
                $costDetailsUpdate = cost_flash_details::where("costing_id",$request->costing_id)->where("master_id",$request->item_code)->first();
                $costDetailsUpdate->where("costing_id",$request->costing_id)->where("master_id",$request->item_code)->update(["uom_id"=>$request->uom_id, "conpc"=>$request->con_pc, "unitprice"=>$request->unit_price, "wastage"=>$request->wastage,
                                            "total_required_qty"=>$request->total_required_qty, "total_value"=>$request->total_value, 
                                            "supplier_id"=>$request->supplier_id, "status"=>1]);
                
            }else{
                
                $costingDetails = new cost_flash_details();
                $costingDetails->costing_id = $request->costing_id;
                $costingDetails->style_id = $request->style_code;
                $costingDetails->master_id = $request->item_code;
                $costingDetails->uom_id = $request->uom_id;
                $costingDetails->conpc = $request->con_pc;
                $costingDetails->unitprice = $request->unit_price;
                $costingDetails->wastage = $request->wastage;
                $costingDetails->total_required_qty = $request->total_required_qty;
                $costingDetails->total_value = $request->total_value;
                $costingDetails->supplier_id = $request->supplier_id;
                $costingDetails->status = 1;
                $costingDetails->saveOrFail();
                
            }
             $status = "success";
            
            
        } catch ( \Exception $ex) {
            
            $status = "fail"; 
        }
        echo json_encode(array('status' => $status));        
    }
    
    public function confirmCostSheet(Request $request){
        
        try{
            
            $costHeaderConfirm = cost_flash_header::where("costing_id",$request->costing_id)->first();
            $revision_no = $costHeaderConfirm->approval_no;
            $revision_no++;
            $costHeaderConfirm->update(["order_status"=>3, "confirm_at" => date("Y-m-d"),"approval_no"=>$revision_no]);
            
            //DB::table('cost_flash_header')->where()->update(["confirm_at" => date("Y-m-d")]); 
            //echo $request->costing_id;
            
            $status = "success";
            
        } catch ( \Exception $ex) {
            $status = "fail";
        }
        
        echo json_encode(array('status' => $status)); 
        
    }
    
    public function reviseCostSheet(Request $request){
        
        try{              
            
            //Before revise the costsheet save existing details to history table
            // =================================================================          
            $this->saveCostSheetDetailsToHistory($request->costing_id);
            $costHeaderRevise = cost_flash_header::where("costing_id",$request->costing_id)->first();           
            $costHeaderRevise->update(["order_status"=>0, "revised_on" => date("Y-m-d")]);
            
            $status = "success";
            
        } catch ( \Exception $ex) {
            $status = "fail";
        }        
        echo json_encode(array('status' => $status));         
    }
    
    private function saveCostSheetDetailsToHistory($cost_id){
        
        try{
            
            $historyCostHeader = new his_cost_flash_header();
            
            $costRevisionId = 0;
            
            $rsGetHistroyRevisionId = his_cost_flash_header::where('costing_id','=',$cost_id)->orderBy('history_id','desc')->first();
            
            if($rsGetHistroyRevisionId === null){
                $costRevisionId = 1;
            }else{
                $costRevisionId = $rsGetHistroyRevisionId->revision_id;
                $costRevisionId++;
            } 
            $costingHeaderDetails = cost_flash_header::where('costing_id','=',$cost_id)->get();
            
            foreach($costingHeaderDetails as $costHeader){
                
                $historyCostHeader->costing_id = $cost_id;
                $historyCostHeader->style_id = $costHeader->style_id;
                $historyCostHeader->order_qty = $costHeader->order_qty;
                $historyCostHeader->order_status = $costHeader->order_status;
                $historyCostHeader->approval_no = $costHeader->approval_no;
                $historyCostHeader->revision_id = $costRevisionId;
                $historyCostHeader->order_smv = $costHeader->order_smv;
                $historyCostHeader->order_fob = $costHeader->order_fob;
                $historyCostHeader->order_eff = $costHeader->order_eff;
                $historyCostHeader->revised_by = $costHeader->revised_by;
                $historyCostHeader->revised_on = $costHeader->revised_on;
                $historyCostHeader->season_id = $costHeader->season_id;
                $historyCostHeader->created_at = $costHeader->created_at;
                $historyCostHeader->created_by = $costHeader->created_by;
                $historyCostHeader->sewing_smv = $costHeader->sewing_smv;
                $historyCostHeader->packing_smv = $costHeader->packing_smv;
                $historyCostHeader->labour_sub_cost = $costHeader->labour_sub_cost;
                $historyCostHeader->finance_cost = $costHeader->finance_cost;
                $historyCostHeader->corporate_cost = $costHeader->corporate_cost;
                $historyCostHeader->epm_rate = $costHeader->epm_rate;
                $historyCostHeader->netprofit = $costHeader->netprofit;
                $historyCostHeader->factory_cpm = $costHeader->factory_cpm;
                $historyCostHeader->frontend_cpm = $costHeader->frontend_cpm;
                $historyCostHeader->finance_rate = $costHeader->finance_rate;
                $historyCostHeader->confirm_by = $costHeader->confirm_by;
                $historyCostHeader->confirm_at = $costHeader->confirm_at;
                $historyCostHeader->saveOrFail();
            }
            
            $hisCostingId =  $historyCostHeader->history_id;
            
            // Save cost sheer history details
            // ===============================
            $costingLineDetails = cost_flash_details::where('costing_id','=',$cost_id)->get();            
            
            foreach($costingLineDetails as $costDetails){                
                
                $historyCostDetails = new his_cost_flash_details();                
                $historyCostDetails->history_id = $hisCostingId;
                $historyCostDetails->costing_id = $costDetails->costing_id;
                $historyCostDetails->style_id = $costDetails->style_id;
                $historyCostDetails->master_id = $costDetails->master_id;                
                $historyCostDetails->uom_id = $costDetails->uom_id;                
                $historyCostDetails->conpc = $costDetails->conpc;                
                $historyCostDetails->unitprice = $costDetails->unitprice;                
                $historyCostDetails->wastage = $costDetails->wastage;                
                $historyCostDetails->required_qty = $costDetails->required_qty;                
                $historyCostDetails->total_required_qty = $costDetails->total_required_qty;                
                $historyCostDetails->total_value = $costDetails->total_value;                
                $historyCostDetails->supplier_id = $costDetails->supplier_id;                
                
                $historyCostDetails->saveOrFail();
            }          
        } catch ( \Exception $ex) {
           echo $ex;
        }
        
    }
    
    public function listingCostings(Request $request){
        
        //$costListings = cost_flash_header::select("costing_id", "LPAD(costing_id,6,0)")->get();
        $costHeader = new cost_flash_header();
        $costListings = $costHeader->ListCostingId($request->style_id);        
        
        echo json_encode($costListings);
        
    }
    
    public function getCostingHeader(Request $request){
        
        //$costingHeaderDetails = cost_flash_header::find($request->costing_id);
        $costingHeaderDetails = cost_flash_header::where('costing_id','=',$request->costing_id)->get();
        echo json_encode($costingHeaderDetails);
    }
    
    public function getCostingLines(Request $request){
        
        //$costingLineDetails = cost_flash_details::where('costing_id','=',$request->costing_id)->get();
        $costingLineDetails = new cost_flash_details();
        $rsCostingDetails = $costingLineDetails->getCostingLineDetails($request->costing_id);
        echo json_encode($rsCostingDetails);
    }
    
    public function getCostingItems(Request $request){   
        
        
        //$costingLineDetails = cost_flash_details::with(['supplier'])->where('costing_id',$request->costing_id)->where('cost_flash_details.master_id','=',$request->item_code)->get();
        //echo json_encode($costingLineDetails);
        
        $costingLineDetails = new cost_flash_details();
        $rsCostingDetails = $costingLineDetails->getCostingLineItems($request->costing_id, $request->item_code);
        echo json_encode($rsCostingDetails);
       
        
    }
}
