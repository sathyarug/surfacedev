<?php

namespace App\Http\Controllers\Merchandising\BulkCosting;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Http\Controllers\Controller;
use App\Models\Merchandising\BulkCosting;
use App\Models\Merchandising\BulkCostingDetails;
use Illuminate\Support\Facades\DB;
use App\Models\Finance\Cost\FinanceCost;
use App\Models\Merchandising\Position;

class BulkDetailsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request) {
        $type = $request->type;

        if ($type == 'getItemData'){
            $item = $request->item;
            $serialblk = $request->serialblk;
            return response($this->getItemDetails($item,$serialblk));
        }elseif ($type == 'item'){
            $search = $request->search;
            return response($this->getItemList($search));
        }elseif ($type == 'loadItem'){
            $bulkheader_id = $request->serialblk;
            $head_id = $request->head;
            return response($this->loadItemList($bulkheader_id,$head_id));
        }elseif ($type == 'getSupplier'){
            return response($this->getSupplier());
        }elseif ($type == 'getProcessOptions'){
            return response($this->getProcessOptions());
        }elseif ($type == 'getUmo'){
            return response($this->getUom());
        }elseif ($type == 'getColorForDivision'){
            return response($this->getColorForDivision());
        }elseif ($type == 'getContry'){
            return response($this->getContry());
        }elseif ($type == 'getShepTerms'){
            return response($this->getShepTerms());
        }elseif ($type == 'loadMainData'){
            $bulkheader_id = $request->serialblk;
            return response($this->loadMainData($bulkheader_id));
        }elseif ($type == 'getMainCategory'){
            return response($this->getMainCategory());
        }elseif ($type == 'loadItemAccordingCategory'){
            return response($this->loadItemAccordingCategory($request));
        }elseif ($type == 'getColorType'){
            return response($this->getColorType());
        }elseif ($type == 'getPosition'){
            return response($this->getPosition());
        }elseif ($type == 'addNewItem'){
            $serialblk = $request->serialblk;
            return response($this->addNewItem($serialblk));
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request) {

        $id = $request->item_id;
        $blk_id=$request->bulkheader_id;

        if($id != 0){
            $model = BulkCostingDetails::find($id);
        }else{
            $model = new BulkCostingDetails();
        }



        if ($model->validate($request->all())) {

            $master= \App\itemCreation::Where('master_description',$request->main_item_description)->get();
            $umo= \App\Models\Org\UOM::pluck('uom_code')->toArray();
                $data=$request->all();
            $supplier= \App\Models\Org\Supplier::where('supplier_name',$request->supplier )->get();
            $serviceType= \App\Models\IE\ServiceType::where('service_type_description',$request->process_options )->get();
            $umo= \App\Models\Org\UOM::where('uom_code',$request->uom )->get();
            if(isset($request->color)){
                $color=\App\Models\Org\Color::where('color_name',$request->color )->get();
            }else{
                $color[0]['color_id']=0;
            }

            $contry=\App\Models\Org\Country::where('country_description',$request->country_of_origin )->get();

            if(isset($request->color_type)){
                $colorType=\App\Models\Merchandising\ColorOption::Where('color_option',$request->color_type)->get();
            }else{
                $colorType[0]['col_opt_id']=0;
            }

            if(isset($request->position)){
                $positionId=\App\Models\Merchandising\Position::Where('position',$request->position)->get();
            }else{
                $positionId[0]['position_id']=0;
            }

            if(isset($request->shipping_terms)){
                $shipTermId=\App\Models\Finance\ShipmentTerm::Where('ship_term_description',$request->shipping_terms)->get();
            }else{
                $shipTermId[0]['ship_term_id']=0;
            }

            $orderType=0;
            if($request->OrderType == 'color wise'){
                $orderType=1;
            }elseif ($request->OrderType == 'size wise'){
                $orderType=2;
            }elseif ($request->OrderType == 'both'){
                $orderType=3;
            }



            $model->bulkheader_id=$blk_id;
            $model->article_no=$request->article_no;
            $model->color_id=$color[0]['color_id'];
            $model->color_type_id=$colorType[0]['col_opt_id'];
            $model->main_item=$request->main_item_Id;
            $model->supplier_id=$supplier[0]['supplier_id'];
            $model->Position=$positionId[0]['position_id'];
            $model->measurement=$request->measurement;
            $model->process_option=$serviceType[0]['service_type_id'];
            $model->uom_id=$umo[0]['uom_id'];
            $model->net_consumption=$request->net_consumption;
            $model->main_item=$master[0]['master_id'];
            $model->unit_price=$request->unit_price;
            $model->wastage=$request->wastage;
            $model->gross_consumption=$request->gross_consumption;
            $model->freight_charges=$request->freight_charges;
            $model->finance_charges=$request->finance_charges;
            $model->moq=$request->moq;
            $model->mcq=$request->mcq;
//            $model->main_item=$request->main_item;
            $model->calculate_by_deliverywise=$request->calculate_by_deliverywise;
            $model->surcharge=$request->surcharge;
            $model->total_cost=$request->total_cost;
            $model->shipping_terms=$shipTermId[0]['ship_term_id'];
            $model->order_type=$orderType;
            $model->lead_time=$request->lead_time;
            $model->country_of_origin=$contry[0]['country_id'];
            $model->comments=$request->comments;



            $model->save();

           // $ItemCode=\App\Models\Org\ItemCode::where('item_id',$model->main_item )->where('color_id',$model->color_id )->where('size_id',$model->color_id )->get();
//            print_r($ItemCode);exit;

            return response(['data' => [
                'message' => 'Costing is saved successfully',
                'bulkCostin' => $model,
                'return'=>1
            ]
            ], Response::HTTP_CREATED);
        } else {
            $errors = $model->errors(); // failure, get errors
            return response(['errors' => ['validationErrors' => $errors]], Response::HTTP_UNPROCESSABLE_ENTITY);
        }
    }

    public function getItemDetails($id,$serialblk){
        $master= \App\itemCreation::find($id)->toArray();
        $SubCategory= \App\Models\Finance\Item\SubCategory::find($master['subcategory_id'])->toArray();
        $category= \App\Models\Finance\Item\Category::find($SubCategory['category_id'])->toArray();
      //  $FinanceCost=\App\Models\Finance\Cost\FinanceCost();
//        $supplier= \App\Models\Org\Supplier::where('status', 1)->get()->toArray();
//        $serviceType= \App\Models\IE\ServiceType::where('status', 1)->get()->toArray();

//        print_r($category);exit;

        $item=array(
            'main_item_type'=>$category['category_code'],
            'main_item_Id'=>$id,
            'main_item_code'=>$master['master_code'],
            'main_item_description'=>$master['master_description'],
            'item_id'=>0,
            'bulkheader_id'=>$serialblk,
            'color'=>'',
            'article_no'=>'',
            'supplier'=>'',
            'position'=>'',
            'measurement'=>'',
            'process_options'=>'',
            'uom'=>'',
            'net_consumption'=>'',
            'unit_price'=>'',
            'wastage'=>'',
            'gross_consumption'=>'',
            'freight_charges'=>'',
            'finance_charges'=>'',
            'moq'=>'',
            'mcq'=>'',
            'calculate_by_deliverywise'=>false,
            'surcharge'=>'',
            'total_cost'=>'',
            'shipping_terms'=>'',
            'lead_time'=>'',
            'country_of_origin'=>'',
            'comments'=>'',
            'success'=>'<a  style="min-height: 12px !important;padding: 1px 10px;font-size: 6px; line-height: 1; border-radius: 2px;margin: 1px;"  class="btn bg-success-400 btn-rounded  btn-icon btn-xs-new"><i class="letter-icon">save</i> </a>',
            'primary'=>'<a  style="min-height: 12px !important;padding: 1px 10px;font-size: 6px; line-height: 1; border-radius: 2px;margin: 1px;"  class="btn bg-primary-400 btn-rounded  btn-icon btn-xs-new"><i class="letter-icon">Copy</i> </a>',
            'danger'=>'<a  style="min-height: 12px !important;padding: 1px 10px;font-size: 6px; line-height: 1; border-radius: 2px;margin: 1px;"  class="btn bg-danger-400 btn-rounded  btn-icon btn-xs-new"><i class="letter-icon">Delete</i> </a>'


        );
        return json_encode($item);
//        return array('master_id'=>$master['master_id'],'category_code'=>$category['category_code']);

    }

    public function getItemList($search){
        return \App\itemCreation::select('master_id', 'master_description')
            ->where([['master_description', 'like', '%' . $search . '%'],])->get();
    }
//
//    public function getItemList($search){
//        return \App\itemCreation::select('master_id', 'master_description')
//            ->where([['master_description', 'like', '%' . $search . '%'],])->get();
//    }

    public function loadItemList($id,$headId){
        $itemList= BulkCostingDetails::select('*')
            ->where([['bulkheader_id', '=',  $id ],['status', '=',  1 ]])->get();
        $hader = \App\Models\Merchandising\BulkCosting::find($headId)->toArray();
        $SentToApproval=0;
        if(count($hader)>0){
            $SentToApproval=$hader['costing_status'];
        }
        $returnArray=array();
        foreach ($itemList AS $item){
            $master= \App\itemCreation::find($item->main_item)->toArray();
            $SubCategory= \App\Models\Finance\Item\SubCategory::find($master['subcategory_id'])->toArray();
            $category= \App\Models\Finance\Item\Category::find($SubCategory['category_id'])->toArray();

            $supplie=\App\Models\Org\Supplier::find($item->supplier_id);
            $process_options=\App\Models\IE\ServiceType::find($item->process_option);
            $umo=\App\Models\Org\UOM::find($item->uom_id);
            $color=\App\Models\Org\Color::find($item->color_id);
            $contry=\App\Models\Org\Country::find($item->country_of_origin);
            $colorType=\App\Models\Merchandising\ColorOption::find($item->color_type_id);
            $position=\App\Models\Merchandising\Position::find($item->position);
            $shipTerm=\App\Models\Finance\ShipmentTerm::find($item->shipping_terms);

             $calculate_by_deliverywise=false;
            if($item->calculate_by_deliverywise ==1){
                $calculate_by_deliverywise=true;
            }

            $oderType='';
            if($item->order_type ==1){
                $oderType='COLOR WISE';
            }elseif ($item->order_type ==2) {
                $oderType='SIZE WISE';
            }elseif ($item->order_type == 3){
                $oderType='BOTH';
            }else{
                $oderType='NONE';
            }

            $item=array(
                'main_item_type'=>$category['category_name'],
                'main_item_Id'=>$item->main_item,
                'main_item_code'=>$master['master_code'],
                'main_item_description'=>$master['master_description'],
                'bulkheader_id'=>$item->bulkheader_id,
                'item_id'=>$item->item_id,
                'color'=>$color['color_name'],
                'color_type'=>$colorType['color_option'],
                'article_no'=>$item->article_no,
                'supplier'=>$supplie->supplier_name,
                'position'=>$position->position,
                'measurement'=>$item->measurement,
                'process_options'=>$process_options->service_type_description,
                'uom'=>$umo->uom_code,
                'net_consumption'=>$item->net_consumption,
                'unit_price'=>$item->unit_price,
                'wastage'=>$item->wastage,
                'gross_consumption'=>$item->gross_consumption,
                'freight_charges'=>$item->freight_charges,
                'finance_charges'=>$item->finance_charges,
                'moq'=>$item->moq,
                'mcq'=>$item->mcq,
                'OrderType'=>$oderType,
                'calculate_by_deliverywise'=>$calculate_by_deliverywise,
                'surcharge'=>$item->surcharge,
                'total_cost'=>$item->total_cost,
                'shipping_terms'=>$shipTerm['ship_term_description'],
                'lead_time'=>$item->lead_time,
                'country_of_origin'=>$contry['country_description'],
                'comments'=>$item->comments,
                'success'=>'<a  style="min-height: 12px !important;padding: 1px 10px;font-size: 6px; line-height: 1; border-radius: 2px;margin: 1px;"  class="btn bg-success-400 btn-rounded  btn-icon btn-xs-new"><i class="letter-icon">save</i> </a>',
                'primary'=>'<a  style="min-height: 12px !important;padding: 1px 10px;font-size: 6px; line-height: 1; border-radius: 2px;margin: 1px;"  class="btn bg-primary-400 btn-rounded  btn-icon btn-xs-new"><i class="letter-icon">Copy</i> </a>',
                'danger'=>'<a  style="min-height: 12px !important;padding: 1px 10px;font-size: 6px; line-height: 1; border-radius: 2px;margin: 1px;"  class="btn bg-danger-400 btn-rounded  btn-icon btn-xs-new"><i class="letter-icon">Delete</i> </a>'


            );

            $returnArray[]=$item;
        }

        return json_encode(array('data'=>$returnArray,'SentToApproval'=>$SentToApproval));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id) {
        $model = BulkCostingDetails::find($id);
        $model->status=0;
        $model->save();
        $blkFeature=\App\Models\Merchandising\BulkCostingFeatureDetails::find($model->bulkheader_id);
        return ($this->loadItemList($model->bulkheader_id,$blkFeature->bulkheader_id));
//        return 1;
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id) {

        $modelOld = BulkCostingDetails::find($id);
        $model = new BulkCostingDetails();

        $model->bulkheader_id=$modelOld->bulkheader_id;
        $model->article_no=$modelOld->article_no;
        $model->color_id=$modelOld->color_id;
        $model->color_type_id=$modelOld->color_type_id;
        $model->main_item=$modelOld->main_item;
        $model->supplier_id=$modelOld->supplier_id;
        $model->position=$modelOld->position;
        $model->measurement=$modelOld->measurement;
        $model->process_option=$modelOld->process_option;
        $model->uom_id=$modelOld->uom_id;
        $model->net_consumption=$modelOld->net_consumption;
        $model->unit_price=$modelOld->unit_price;
        $model->wastage=$modelOld->wastage;
        $model->gross_consumption=$modelOld->gross_consumption;
        $model->freight_charges=$modelOld->freight_charges;
        $model->finance_charges=$modelOld->finance_charges;
        $model->moq=$modelOld->moq;
        $model->mcq=$modelOld->mcq;
        $model->calculate_by_deliverywise=$modelOld->calculate_by_deliverywise;
        $model->surcharge=$modelOld->surcharge;
        $model->total_cost=$modelOld->total_cost;
        $model->order_type=$modelOld->order_type;
        $model->shipping_terms=$modelOld->shipping_terms;
        $model->lead_time=$modelOld->lead_time;
        $model->country_of_origin=$modelOld->country_of_origin;
        $model->comments=$modelOld->comments;

        $model->save();
//        App\Models\Merchandising\BulkCostingFeatureDetails
//        $BulkCosting = BulkCosting::find($request->bulk_header);
        $blkFeature=\App\Models\Merchandising\BulkCostingFeatureDetails::find($modelOld->bulkheader_id);

//print_r($modelOld->bulkheader_id.','.$blkFeature->bulkheader_id);exit;
        return ($this->loadItemList($modelOld->bulkheader_id,$blkFeature->bulkheader_id));
//        return response(['data' => [
//            'message' => 'Costing is saved successfully',
//            'bulkCostin' => $model,
//            'return'=>1
//        ]
//        ], Response::HTTP_CREATED);

    }

    public  function getSupplier(){
        $supplier= \App\Models\Org\Supplier::where('status', 1)->pluck('supplier_name')->toArray();
        return json_encode($supplier);
    }

    public  function getProcessOptions(){
        $serviceType= \App\Models\IE\ServiceType::where('status', 1)->pluck('service_type_description')->toArray();
        return json_encode($serviceType);
    }
    public  function getUom(){
        $umo= \App\Models\Org\UOM::pluck('uom_code')->toArray();
        return json_encode($umo);
    }
    public  function getColorForDivision(){
        $color=\App\Models\Org\Color::pluck('color_name')->toArray();
        return json_encode($color);
    }

    public  function getColorType(){
        $colorType=\App\Models\Merchandising\ColorOption::Where('status',1)->pluck('color_option')->toArray();
        return json_encode($colorType);
    }
    public  function getPosition(){
        $colorType=\App\Models\Merchandising\Position::Where('status',1)->pluck('position')->toArray();
        return json_encode($colorType);
    }
    public  function getMainCategory(){
        $category=\App\Models\Finance\Item\Category::pluck('category_name')->toArray();
        return json_encode($category);
    }

    public  function getContry(){
        $contry=\App\Models\Org\Country::pluck('country_description')->toArray();
        return json_encode($contry);
    }

    public  function getShepTerms(){
        $shepTerms=\App\Models\Finance\ShipmentTerm::pluck('ship_term_description')->toArray();
        return json_encode($shepTerms);
    }

    public function loadMainData($id){
        $blkFeature=\App\Models\Merchandising\BulkCostingFeatureDetails::find($id);
        $hader = \App\Models\Merchandising\BulkCosting::find($blkFeature->bulkheader_id);
        $style = \App\Models\Merchandising\StyleCreation::find($hader->style_id);
        $color=\App\Models\Org\Color::find($blkFeature->color_ID);
        $component=\App\Models\Org\Component::find($blkFeature->component_id);
        //$sumStyleSmv=\App\Models\ie\StyleSMV::where('style_id', $hader->style_id)->sum('smv_value');
       // $sumStyleSmvComp=\App\Models\ie\StyleSMV::where('style_id', $hader->style_id)->where('feature_id', $blkFeature->component_id)->sum('smv_value');
        $financeCost=\App\Models\Finance\Cost\FinanceCost::first();
//        $cpmData=\App\Models\ie\ProductCMP::where('style_id', $hader->style_id)->pluck('cpm')->toArray();
//        $cpm=0;
//        if(isset($cpmData[0])){
//        $cpm= $cpmData[0];
//        }
//        print_r($cpm);exit;
        $data['blkNo']=$blkFeature->bulkheader_id;
        $data['bom']=$blkFeature->bom_stage;
        $data['season']=$blkFeature->season_id;
        $data['colType']=$blkFeature->col_opt_id;
        $epmNnp = \App\Models\Merchandising\BulkCostingFeatureDetails::getEmpNp($blkFeature->style_feature_id,$data);
//print_r($epmNnp);exit;
       // $epmNnp = \App\Models\Merchandising\BulkCostingFeatureDetails::getEmpNp($productFeature['id'],$data);
       return (array('style'=>$style->style_no,
           'component'=>$component->product_component_description,
           'color'=>$color->color_name,
          // 'styleSmv'=>$sumStyleSmv,
            'styleSmv'=>$blkFeature->smv,
          // 'styleSmvComp'=>$sumStyleSmvComp,
           'finance_cost'=>$financeCost->finance_cost,
           'cpmfront_end'=>$financeCost->cpmfront_end,
           'cpum'=>$financeCost->cpum,
           'cpm'=>(($financeCost->cpum)*($hader->plan_efficiency)),
           'fob'=>(float)$epmNnp['fob'],
           'totalCostWithoutRm'=>$epmNnp['totalCostWithoutRm']
       ));
    }

    public  function loadItemAccordingCategory($tem){

        $item_list = DB::table('item_master')
            ->join('item_subcategory', 'item_master.subcategory_id', '=', 'item_subcategory.subcategory_id')
            ->join('item_category', 'item_subcategory.category_id', '=', 'item_category.category_id')
            ->select(DB::raw('item_master.master_description'))
            ->where('item_category.category_name',$tem['query'])
            ->get()
            ->toArray();

        $returnArray=array();
        foreach ($item_list AS $item_data){
            array_push($returnArray,$item_data->master_description);
        }

        return ($returnArray);
    }

    public function addNewItem($serialblk){
        $finCost = FinanceCost::all();
        $item=array(
            'main_item_type'=>'',
            'main_item_Id'=>'',
            'main_item_code'=>'',
            'main_item_description'=>'',
            'bulkheader_id'=>$serialblk,
            'item_id'=>'',
            'color'=>'',
            'color_type'=>'',
            'article_no'=>'',
            'supplier'=>'',
            'position'=>'',
            'measurement'=>'',
            'process_options'=>'',
            'uom'=>'',
            'net_consumption'=>0,
            'unit_price'=>0,
            'wastage'=>'',
            'gross_consumption'=>0,
            'freight_charges'=>0,
            'finance_charges'=>$finCost[0]['finance_cost'],
            'moq'=>'',
            'mcq'=>'',
            'OrderType'=>'',
            'calculate_by_deliverywise'=>false,
            'surcharge'=>'',
            'total_cost'=>'',
            'shipping_terms'=>'',
            'lead_time'=>'',
            'country_of_origin'=>'',
            'comments'=>'',
            'success'=>'<a  style="min-height: 12px !important;padding: 1px 10px;font-size: 6px; line-height: 1; border-radius: 2px;margin: 1px;"  class="btn bg-success-400 btn-rounded  btn-icon btn-xs-new"><i class="letter-icon">save</i> </a>',
            'primary'=>'<a  style="min-height: 12px !important;padding: 1px 10px;font-size: 6px; line-height: 1; border-radius: 2px;margin: 1px;"  class="btn bg-primary-400 btn-rounded  btn-icon btn-xs-new"><i class="letter-icon">Copy</i> </a>',
            'danger'=>'<a  style="min-height: 12px !important;padding: 1px 10px;font-size: 6px; line-height: 1; border-radius: 2px;margin: 1px;"  class="btn bg-danger-400 btn-rounded  btn-icon btn-xs-new"><i class="letter-icon">Delete</i> </a>'


        );
        return json_encode($item);
    }
}
