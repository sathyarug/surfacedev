<?php

namespace App\Http\Controllers\Merchandising\BulkCosting;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use App\Models\Merchandising\BulkCosting;
use App\Models\Merchandising\BulkCostingDetails;
use App\Models\Merchandising\BulkCostingFeatureDetails;
use App\Models\Merchandising\StyleProductFeature;

class BulkCostingController extends Controller {

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request) {
        $type = $request->type;

        if ($type == 'getSeasonList') {
            return response($this->getSeasonList());
        } elseif ($type == 'getColorType') {
            return response($this->getColorType());
        }elseif ($type == 'getBomStage') {
            return response($this->getBomStage());
        } elseif ($type == 'auto') {
            $search = $request->search;
            return response($this->getStyleList($search));
        } elseif ($type == 'getStyleData') {
            return response($this->getStyleData($request->style_id));
        } elseif ($type == 'getFinishGood') {
            $data=array('blkNo'=>$request->blk,'bom'=>$request->bom,'season'=>$request->sea,'colType'=>$request->col);
            return response($this->getFinishGood($request->style_id,$data));
        }elseif ($type == 'item'){
            $search = $request->search;
            return response($this->getItemList($search));
        }elseif ($type == 'getItemData'){
            $item = $request->item;
            return response($this->getItemDetails($item));
        }elseif ($type == 'getColorForDivision'){
            $division_id = $request->division_id;
            $query = $request->query;
            return response($this->getColorForDivision($division_id,$query));
        }
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create() {
        echo 'Create';
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request) {
        if($request->bulk_header !=0){
            $model = BulkCosting::find($request->bulk_header);
        }else{
             $model = new BulkCosting();
        }

        $type = $request->type;

        if($type =='lineHeader'){
            $data=array('blkNo'=>$request->hader,'bom'=>$request->bom,'season'=>$request->sea,'colType'=>$request->col);
            return response($this->saveLineHeader($request,$data));
        }

        if ($model->validate($request->all())) {

            $model->style_id=$request->Style['style_id'];

            $date=date_create($request->pcd);

            $model->pcd=date_format($date,"Y-m-d");
            $model->fob=$request->fob;
            $model->plan_efficiency=$request->plan_efficiency;
            $model->finance_charges=$request->finance_charges;

            $model->status = 1;

//            print_r($request->all());exit;
            $model->save();
            return response(['data' => [
                    'message' => 'Costing is saved successfully',
                    'bulkCostin' => $model
                ]
                    ], Response::HTTP_CREATED);
        } else {
            $errors = $model->errors(); // failure, get errors
            return response(['errors' => ['validationErrors' => $errors]], Response::HTTP_UNPROCESSABLE_ENTITY);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id) {
        echo 'Show';
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id) {
        echo 'Edit';
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id) {

        $modelOld = BulkCostingFeatureDetails::find($id);


        $styleFeatures=BulkCostingFeatureDetails::where('style_feature_id',$modelOld->style_feature_id)->where('feature_id',$modelOld->feature_id)->where('status',1)->get()->toArray();

        $featureList=\App\Models\Org\FeatureComponent::where('product_feature_id',$modelOld->feature_id)->where('status',1)->get()->toArray();

        foreach ($featureList AS $feature ){
        $chrck=BulkCostingFeatureDetails::where('style_feature_id',$modelOld->style_feature_id)->where('feature_id',$feature['product_feature_id'])->where('component_id',$feature['product_component_id'])->get()->toArray();

            if( count($chrck)== 0){
                return 0;
            }

       }

        $productFeatureList = new StyleProductFeature();
        $productFeatureList->style_id = $request->style_id;
        $productFeatureList->product_feature_id = $modelOld->feature_id;
        $productFeatureList->save();

        foreach ($styleFeatures AS $styleFeature) {

           $model = new BulkCostingFeatureDetails();

           $model->style_feature_id = $productFeatureList->id;
           $model->feature_id = $styleFeature['feature_id'];
           $model->component_id = $styleFeature['component_id'];
           $model->bulkheader_id = $styleFeature['bulkheader_id'];
           $model->surcharge = $styleFeature['surcharge'];
           $model->color_ID = $styleFeature['color_ID'];
           $model->season_id = $styleFeature['season_id'];
           $model->col_opt_id = $styleFeature['col_opt_id'];
           $model->bom_stage = $styleFeature['bom_stage'];
           $model->mcq = $styleFeature['mcq'];
           $model->combo_code = $styleFeature['combo_code'];
           $model->save();

           $itemList = BulkCostingDetails::select('*')
               ->where([['bulkheader_id', '=', $styleFeature['blk_feature_id']], ['status', '=', 1]])->get();

           foreach ($itemList AS $item) {
               $BulkCostingDetails = new BulkCostingDetails();

               $BulkCostingDetails->bulkheader_id = $model->blk_feature_id;
               $BulkCostingDetails->article_no = $item->article_no;
               $BulkCostingDetails->color_id = $item->color_id;
               $BulkCostingDetails->color_type_id = $item->color_type_id;
               $BulkCostingDetails->code = $item->code;
               $BulkCostingDetails->main_item = $item->main_item;
               $BulkCostingDetails->supplier_id = $item->supplier_id;
               $BulkCostingDetails->position = $item->position;
               $BulkCostingDetails->measurement = $item->measurement;
               $BulkCostingDetails->process_option = $item->process_option;
               $BulkCostingDetails->uom_id = $item->uom_id;
               $BulkCostingDetails->net_consumption = $item->net_consumption;
               $BulkCostingDetails->unit_price = $item->unit_price;
               $BulkCostingDetails->wastage = $item->wastage;
               $BulkCostingDetails->gross_consumption = $item->gross_consumption;
               $BulkCostingDetails->freight_charges = $item->freight_charges;
               $BulkCostingDetails->finance_charges = $item->finance_charges;
               $BulkCostingDetails->mcq = $item->mcq;
               $BulkCostingDetails->moq = $item->moq;
               $BulkCostingDetails->calculate_by_deliverywise = $item->calculate_by_deliverywise;
               $BulkCostingDetails->order_type = $item->order_type;
               $BulkCostingDetails->surcharge = $item->surcharge;
               $BulkCostingDetails->total_cost = $item->total_cost;
               $BulkCostingDetails->shipping_terms = $item->shipping_terms;
               $BulkCostingDetails->lead_time = $item->lead_time;
               $BulkCostingDetails->country_of_origin = $item->country_of_origin;
               $BulkCostingDetails->comments = $item->comments;

               $BulkCostingDetails->save();

           }
       }
        $data=array('blkNo'=>$model->bulkheader_id,'bom'=>$model->bom_stage,'season'=>$model->season_id,'colType'=>$model->col_opt_id);
         return $this->getFinishGood($request->style_id,$data);

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id) {
        echo 'Destroy';
    }

    private function getSeasonList() {
        //return \App\Models\Org\Customer::getActiveCustomerList();
        return \App\Models\Org\Season::select('season_id', 'season_name')
                        ->where([['status', '=', 1]])->get();
    }

    private function getColorType() {
        return \App\Models\Merchandising\ColorOption::select('col_opt_id', 'color_option')
                        ->where([['status', '=', 1]])->get();
    }

    private function getBomStage() {
        return \App\Models\Merchandising\BOMStage::select('bom_stage_id', 'bom_stage_description')
            ->where([['status', '=', 1]])->get();
    }

    private function getStyleList($search) {
        return \App\Models\Merchandising\StyleCreation::select('style_id', 'style_no')
                        ->where([['style_no', 'like', '%' . $search . '%'],])->get();
    }

    private function getStyleData($style_id) {
        $dataArr = array();
        $styleData = \App\Models\Merchandising\StyleCreation::find($style_id);
        $hader = \App\Models\Merchandising\BulkCosting::where('style_id', $style_id)->get()->toArray();
        $country = \App\Models\Org\Country::find($styleData->customer->customer_county);
       

        $dataArr['style_remark'] = $styleData->remark;
        $dataArr['division_name'] = $styleData->division->division_description;
        $dataArr['division_id'] = $styleData->division->division_id;
        $dataArr['style_desc'] = $styleData->style_description;
        $dataArr['cust_name'] = $styleData->customer->customer_name;


        $dataArr['style_desc'] = $styleData->style_description;
        $dataArr['style_id'] = $styleData->style_id;
        $dataArr['style_no'] = $styleData->style_no;
        $dataArr['image'] = $styleData->image;

        $dataArr['cust_id'] = $styleData->customer->customer_id;
        $dataArr['division_name'] = $styleData->division->division_description;
        $dataArr['division_id'] = $styleData->division->division_id;
        $dataArr['country'] = $country->country_description;
//        $dataArr['stage'] = '';

        $sumStyleSmvComp=\App\Models\ie\StyleSMV::where('style_id', $styleData->style_id)->orderBy('smv_comp_id', 'desc')->first();
//        dd($sumStyleSmvComp->created_date);exit;

        if(count($hader)>0){
            $hader[0]['pcd']=date_format(date_create($hader[0]['pcd']),"m/d/Y");
            $dataArr['blk_hader'] = $hader[0];
            $dataArr['blk_hader']['smv_received']=date_format(date_create($sumStyleSmvComp->created_date),"m/d/Y");
            $dataArr['blk_hader']['costed_smv_id']=$sumStyleSmvComp->smv_value;

        }else{
            $financeCost=\App\Models\finance\Cost\FinanceCost::first();

            $dataArr['blk_hader']['updated_date']='';
            $dataArr['blk_hader']['total_cost']='';
            $dataArr['blk_hader']['season_id']='';
            $dataArr['blk_hader']['color_type_id']='';
            $dataArr['blk_hader']['created_date']='';
            $dataArr['blk_hader']['cost_per_std_min']=$financeCost->cpmfront_end;
            $dataArr['blk_hader']['epm']='';
            $dataArr['blk_hader']['np_margin']='';
            $dataArr['blk_hader']['plan_efficiency']='';
            $dataArr['blk_hader']['bulk_costing_id']='';
            $dataArr['blk_hader']['pcd']='';
            $dataArr['blk_hader']['finance_charges']=$financeCost->finance_cost;
            $dataArr['blk_hader']['cost_per_min']=$financeCost->cpum;
            $dataArr['blk_hader']['costed_smv_id']=$sumStyleSmvComp->smv_value;

        }


//dd($dataArr);
        return $dataArr;
    }

    private function getFinishGood($style_id,$data) {


        $productFeatureList = \App\Models\Merchandising\StyleProductFeature::where('style_id', $style_id)->get()->toArray();

        $count=1;
        $lineNo=0;
        foreach ($productFeatureList AS $productFeature){

            $featureList=\App\Models\Org\FeatureComponent::where('product_feature_id', $productFeature['product_feature_id'])->where('status',1)->get()->toArray();


            foreach ($featureList As $feature){
                $surcharge=false;
                $mcq=false;
                $colordata='';$blkHeadId=0;
                $featureData=\App\Models\Org\Feature::find($feature['product_feature_id']);
                $component=\App\Models\Org\Component::find($feature['product_component_id']);

                $blk=$data['blkNo'];
                $bom=$data['bom'];
                $season=$data['season'];
                $colType=$data['colType'];

                $blkCostFea = \App\Models\Merchandising\BulkCostingFeatureDetails::where('style_feature_id', $productFeature['id'])->where('feature_id', $featureData->product_feature_id)->where('component_id',$component->product_component_id)->where('bulkheader_id',$blk)->where('bom_stage',$bom)->where('season_id',$season)->where('col_opt_id',$colType)->where('status',1)->first();
                if(isset($blkCostFea->mcq) && $blkCostFea->mcq==1){
                        $mcq=true;
                    }else{
                        $mcq=false;
                    }

                if(isset($blkCostFea->surcharge) && $blkCostFea->surcharge==1){
                    $surcharge=true;
                }else{
                    $surcharge=false;
                }


                if(isset($blkCostFea->color_ID)){

                    $color=\App\Models\Org\Color::find($blkCostFea->color_ID)->first();
                    $colordata=$color->color_name;

                }
                if(isset($blkCostFea->blk_feature_id)){
                    $blkHeadId=$blkCostFea->blk_feature_id;
                }


                $productFeatureArray[]=array(
                    'pack_name'=>'PACK-'.$count,
                    'id'=>$component->product_component_id,
                    'style_feature_id'=>$productFeature['id'],
                    'description'=>$component->product_component_description,
                    'main_featureName'=>$featureData->product_feature_description,
                    'mcq'=>$mcq,
                    'surcharge'=>$surcharge,
                    'color'=>$colordata,
                    'blkHead'=>$blkHeadId,
                    'main_featureName_id'=>$featureData->product_feature_id,
                    'success'=>'<a  style="min-height: 12px !important;padding: 1px 10px;font-size: 6px; line-height: 1; border-radius: 2px;margin: 1px;"  class="btn bg-success-400 btn-rounded  btn-icon btn-xs-new"><i class="letter-icon">save</i> </a>',
                    'primary'=>'<a  style="min-height: 12px !important;padding: 1px 10px;font-size: 6px; line-height: 1; border-radius: 2px;margin: 1px;"  class="btn bg-primary-400 btn-rounded  btn-icon btn-xs-new"><i class="letter-icon">Copy</i> </a>',
                    'danger'=>'<a  style="min-height: 12px !important;padding: 1px 10px;font-size: 6px; line-height: 1; border-radius: 2px;margin: 1px;"  class="btn bg-warning-400 btn-rounded  btn-icon btn-xs-new"><i class="letter-icon">Open</i> </a>'

                );$lineNo++;
            }


$count++;
        }

        return json_encode($productFeatureArray);
    }

    private function saveLineHeader($request,$data){
        $color=\App\Models\Org\Color::where('color_name', $request->color)->first();

 if($request->blkHead != 0){
     $bulkCostingDetails = BulkCostingFeatureDetails::find($request->blkHead);
 }else{
     $bulkCostingDetails = new BulkCostingFeatureDetails();
 }

        $bulkCostingDetails->bulkheader_id=$request->blkHead;
        $bulkCostingDetails->color_id=$color->color_id;
        $bulkCostingDetails->feature_id=$request->main_featureName_id;
        $bulkCostingDetails->component_id=$request->id;
        $bulkCostingDetails->mcq=$request->mcq;
        $bulkCostingDetails->surcharge=$request->surcharge;
        $bulkCostingDetails->style_feature_id=$request->style_feature_id;
        $bulkCostingDetails->bulkheader_id=$data['blkNo'];
        $bulkCostingDetails->bom_stage=$data['bom'];
        $bulkCostingDetails->season_id=$data['season'];
        $bulkCostingDetails->col_opt_id=$data['colType'];
        $bulkCostingDetails->save();

        $model = BulkCosting::find($bulkCostingDetails->bulkheader_id);

        $data=array('blkNo'=>$data['blkNo'],'bom'=>$data['bom'],'season'=>$data['season'],'colType'=>$data['colType']);

        return $this->getFinishGood($model->style_id,$data);

//        return $bulkCostingDetails->blk_feature_id;

    }

    public function getItemList($search){
        return \App\itemCreation::select('master_id', 'master_description')
            ->where([['master_description', 'like', '%' . $search . '%'],])->get();
    }

    public function getItemDetails($id){
        $master= \App\itemCreation::find($id)->toArray();
        $SubCategory= \App\Models\Finance\Item\SubCategory::find($master['subcategory_id'])->toArray();
        $category= \App\Models\Finance\Item\Category::find($SubCategory['category_id'])->toArray();
        $supplier= \App\Models\Org\Supplier::where('status', 1)->get()->toArray();
        $serviceType= \App\Models\IE\ServiceType::where('status', 1)->get()->toArray();



        return array('category'=>$category,'supplier'=>$supplier,'pOptions'=>$serviceType);

    }

    public  function getColorForDivision($division_id,$query){
        $color=\App\Models\Org\Color::where([['division_id','=',$division_id]])->pluck('color_name')->toArray();
        return json_encode($color);
    }

}
