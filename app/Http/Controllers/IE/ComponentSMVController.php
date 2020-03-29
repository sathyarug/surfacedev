<?php

namespace App\Http\Controllers\IE;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Database\Eloquent\ModelNotFoundException;

use App\Http\Controllers\Controller;
use App\Models\IE\ComponentSMVHeader;
use App\Models\IE\ComponentSMVHeaderHistory;
use App\Models\IE\ComponentSMVDetails;
use App\Models\IE\ComponentSMVDetailsHistory;
use App\Models\Merchandising\BulkCostingFeatureDetails;
use App\Models\IE\GarmentOperationMaster;
use App\Models\IE\SMVUpdate;
use App\Models\IE\ComponentSMVSummary;
use App\Models\IE\ComponentSMVSummaryHistory;
use App\Models\Merchandising\StyleCreation;
use App\Models\Merchandising\BuyMaster;
use App\Models\Merchandising\ProductFeatureComponent;
use Exception;
use Illuminate\Support\Facades\DB;
class ComponentSMVController extends Controller
{
    public function __construct()
    {
      //add functions names to 'except' paramert to skip authentication
      $this->middleware('jwt.verify', ['except' => ['index']]);
    }

    //get Service Type list
    public function index(Request $request)
    {
      $type = $request->type;
      if($type == 'datatable')   {
        $data = $request->all();
        return response($this->datatable_search($data));
      }
      else if($type == 'auto_buy')    {
        $search = $request->search;
        return response($this->autocomplete_search_buy_name($search));
      }
      else if($type == 'searchDetails')    {
        $styleId = $request->styleId;
        $bomStageId=$request->bomStageId;
        $colorOptionId=$request->colorOptionId;
        $buyId=$request->buyId;


        return response(['data'=>$this->details_search($styleId,$bomStageId,$colorOptionId,$buyId)]);
      }
    /*  else if($type=='checkSMVRange'){
      $data=$request->data;
        return ($this->check_smv_range($data));
      }*/
      else {
        $active = $request->active;
        $fields = $request->fields;
        return response([
          'data' => $this->list($active , $fields)
        ]);
      }
    }


    //create a Service Type
    public function storeDataset(Request $request)
    {
    if($this->validateGarmentOperations($request->data)==false){
      return response(['data'=>[
        'message'=>"Invalid Garment Operation",
        'status'=>0
        ]
      ]);
      }
      $styleId=$request->styleId;
      $prodcutFeatureId=$request->productFeatureID;
      //$stylefeature=StyleCreation::find('style_id');
      $bomStageID=$request->bomStageId;
      $totalSMV=$request->totalSMV;
      $colorOptionId=$request->colOptId;
      $buyId=$request->buyId;
      $details=$request->data;
      $detailsSummary=$request->dataSum;
      $comments=$request->comments;
      $revisionNo=$request->revisionNo;
      $headerTableId=$request->componentSmvHeaderId;
      $is_used_in_costing=DB::table('costing')->where('style_id','=',$styleId)->where('bom_stage_id','=',$bomStageID)->where('color_type_id','=',$colorOptionId)->exists();
      if($is_used_in_costing==true){
        return response(['data'=>[
          'message'=>"Component SMV Already In use",
          'status'=>0
          ]
        ]);
      }
      else{
      if($headerTableId==0){
      $smvComponentHeader=new ComponentSMVHeader();
      $smvComponentHeader->style_id=$styleId;
      $smvComponentHeader->status=1;
      $smvComponentHeader->bom_stage_id=$bomStageID;
      $smvComponentHeader->col_opt_id=$colorOptionId;
      $smvComponentHeader->buy_id=$buyId;
      $smvComponentHeader->product_feature_id=$prodcutFeatureId;
      $smvComponentHeader->total_smv=$totalSMV;
      $smvComponentHeader->revision_no=0;
      $smvComponentHeader->comments=$comments;
      $smvComponentHeader->save();
      $headerId=$smvComponentHeader->smv_component_header_id;


      for($i=0;$i<sizeof($detailsSummary);$i++){
        $smvComponentSummary=new ComponentSMVSummary();
        $smvComponentSummary->smv_component_header_id=$headerId;
        $smvComponentSummary->product_component_id=$detailsSummary[$i]["product_component_id"];
        $smvComponentSummary->product_silhouette_id=$detailsSummary[$i]['product_silhouette_id'];
        $smvComponentSummary->line_no=$detailsSummary[$i]['line_no'];
        $smvComponentSummary->total_smv=$detailsSummary[$i]['total_smv'];
        $smvComponentSummary->status=1;
        $smvComponentSummary->save();
      }

      //echo(sizeof($details));
      for($i=0;$i<sizeof($details);$i++){
        $smvComponentDetails=new ComponentSMVDetails();
        $smvComponentDetails->smv_component_header_id=$headerId;
        $garmentOperationName=$details[$i]["garment_operation_name"];
        //echo($garmentOperationName);
         $garmentOperation=GarmentOperationMaster::select('*')
         ->where('garment_operation_name','=',$garmentOperationName)
         ->first();
         //echo($garmentOperation->garment_operation_id);
         $smvComponentDetails->garment_operation_id=$garmentOperation->garment_operation_id;
         $smvComponentDetails->product_component_id=$details[$i]['product_component_id'];
         $smvComponentDetails->product_silhouette_id=$details[$i]['product_silhouette_id'];
         $smvComponentDetails->line_no=$details[$i]['line_no'];
         $smvComponentDetails->smv=$details[$i]['smv'];
         $smvComponentDetails->status=1;
         $smvComponentDetails->save();
        }


    return response(['data'=>[
      'message'=>"Component SMV Saved Sucessfully",
      'status'=>1
      ]
    ]);
  }
  //smv update part
  else {
    $smvComponentHeader=ComponentSMVHeader::find($headerTableId);
    $smvComponentHeaderHistory=new ComponentSMVHeaderHistory();
    $revisionNo=$smvComponentHeader->revision_no;
    $smvComponentHeaderHistory->smv_component_header_id=$headerTableId;
    $smvComponentHeaderHistory->style_id=$smvComponentHeader['style_id'];
    $smvComponentHeaderHistory->status=$smvComponentHeader['status'];
    $smvComponentHeaderHistory->bom_stage_id=$smvComponentHeader['bom_stage_id'];
    $smvComponentHeaderHistory->col_opt_id=$smvComponentHeader['col_opt_id'];
    $smvComponentHeaderHistory->buy_id=$smvComponentHeader['buy_id'];
    $smvComponentHeaderHistory->product_feature_id=$smvComponentHeader['product_feature_id'];
    $smvComponentHeaderHistory->total_smv=$smvComponentHeader['total_smv'];
    $num=(int)$revisionNo;
    //print_r($num);
    $smvComponentHeaderHistory->revision_no=$num+1;
    $smvComponentHeaderHistory->comments=$smvComponentHeader['comments'];
    $smvComponentHeaderHistory->save();
    $smvComponentHeader->total_smv=$totalSMV;
    //
    $smvComponentHeader->revision_no=$num+1;
    $smvComponentHeader->comments=$comments;
    $smvComponentHeader->save();

      $smvComponentSummary=ComponentSMVSummary::select('*')
      ->where('smv_component_header_id','=',$headerTableId)
      ->get();
      for($i=0;$i<sizeof($smvComponentSummary);$i++){
      $smvComponentSummaryHistory= new ComponentSMVSummaryHistory();
      $smvComponentSummaryHistory->summary_id=$smvComponentSummary[$i]["summary_id"];
      $smvComponentSummaryHistory->smv_component_header_id=$headerTableId;
      $smvComponentSummaryHistory->product_component_id=$smvComponentSummary[$i]["product_component_id"];
      $smvComponentSummaryHistory->product_silhouette_id=$smvComponentSummary[$i]['product_silhouette_id'];
      $smvComponentSummaryHistory->line_no=$smvComponentSummary[$i]['line_no'];
      $smvComponentSummaryHistory->total_smv=$smvComponentSummary[$i]['total_smv'];
      $smvComponentSummaryHistory->status=1;
      $smvComponentSummaryHistory->save();
       }
       $smvComponentSummaryDel=ComponentSMVSummary::select('*')
       ->where('smv_component_header_id','=',$headerTableId)
       ->delete();

       for($i=0;$i<sizeof($detailsSummary);$i++){
         $smvComponentSummary=new ComponentSMVSummary();
         $smvComponentSummary->smv_component_header_id=$headerTableId;
         $smvComponentSummary->product_component_id=$detailsSummary[$i]["product_component_id"];
         $smvComponentSummary->product_silhouette_id=$detailsSummary[$i]['product_silhouette_id'];
         $smvComponentSummary->line_no=$detailsSummary[$i]['line_no'];
         $smvComponentSummary->total_smv=$detailsSummary[$i]['total_smv'];
         $smvComponentSummary->status=1;
         $smvComponentSummary->save();
       }

        $smvComponentDetails= ComponentSMVDetails::select('*')
       ->where('smv_component_header_id','=',$headerTableId)
       ->get();
         for($i=0;$i<sizeof( $smvComponentDetails);$i++){
           $smvComponentDetailsHistory=new ComponentSMVDetailsHistory();
           $smvComponentDetailsHistory->details_id= $smvComponentDetails[$i]['details_id'];
           $smvComponentDetailsHistory->smv_component_header_id= $headerTableId;
           $smvComponentDetailsHistory->garment_operation_id= $smvComponentDetails[$i]['garment_operation_id'];
           $smvComponentDetailsHistory->product_component_id=$smvComponentDetails[$i]['product_component_id'];
           $smvComponentDetailsHistory->product_silhouette_id=$smvComponentDetails[$i]['product_silhouette_id'];
           $smvComponentDetailsHistory->line_no=$smvComponentDetails[$i]['line_no'];
           $smvComponentDetailsHistory->smv=$smvComponentDetails[$i]['smv'];
           $smvComponentDetailsHistory->status=1;
           $smvComponentDetailsHistory->save();

         }
         $smvComponentDetails=ComponentSMVDetails::select('*')
         ->where('smv_component_header_id','=',$headerTableId)
         ->delete();

         for($i=0;$i<sizeof($details);$i++){
           $smvComponentDetails=new ComponentSMVDetails();
           $smvComponentDetails->smv_component_header_id=$headerTableId;
           $garmentOperationName=$details[$i]["garment_operation_name"];
           //echo($garmentOperationName);
            $garmentOperation=GarmentOperationMaster::select('*')
            ->where('garment_operation_name','=',$garmentOperationName)
            ->first();
            //echo($garmentOperation->garment_operation_id);
            $smvComponentDetails->garment_operation_id=$garmentOperation->garment_operation_id;
            $smvComponentDetails->product_component_id=$details[$i]['product_component_id'];
            $smvComponentDetails->product_silhouette_id=$details[$i]['product_silhouette_id'];
            $smvComponentDetails->line_no=$details[$i]['line_no'];
            $smvComponentDetails->smv=$details[$i]['smv'];
            $smvComponentDetails->status=1;
            $smvComponentDetails->save();
           }

           return response(['data'=>[
             'message'=>"Component SMV  Revised Successfully",
             'status'=>1
             ]
           ]);
      }
  }
    }

    public function check_smv_range(Request $request){
        $data=$request->data;
          for($i=0;$i<sizeof($data);$i++){
          $styleId=$data[$i]['style_id'];
          $productSilhouetteId=$data[$i]['product_silhouette_id'];
          $smv=$data[$i]['total_smv'];
      $smv_devision=SMVUpdate::join('style_creation','smv_update.customer_id','=','style_creation.customer_id')
        ->where('style_creation.style_id','=',$styleId)
        ->select('*')
        ->first();
      $SMVUpdate=SMVUpdate::where('smv_update.product_silhouette_id','=',$productSilhouetteId)
       ->where('smv_update.status','=',1)
       //->where('style_creation.division_id','=',$smv_devision->division_id)
       ->where('smv_update.min_smv','<=',$smv)
       ->where('smv_update.max_smv','>=',$smv)
       ->orderBy('version', 'DESC')
       ->select('*')
       ->first();



      if($SMVUpdate==null){
        return response([
           'data' => [
             'message' => ' is Not in Range',
             'append'=>'Total SMV of the ',
             'status' => 0,
             'silhouette'=>$data[$i]['product_silhouette_description'],
           ]
         ]);

      }


    }
    //
       return response([
         'data' => [
           'message' => 'SMV is in the Range',
           'status' => 1,
         ]
       ]);
      //echo("pass");


    }

    //search Silhouette for autocomplete
    private function autocomplete_search_buy_name($search)
    {
      $active=1;
      $buyier_lists = BuyMaster::select('buy_id','buy_name')
      ->where([['buy_name', 'like', '%' . $search . '%']])
      ->where('status','=',$active)
      ->get();
      return $buyier_lists;
    }

    public function check_copy_status(Request $request){
      //echo("hdhhdhdhdhhdhd");
      $styleId=$request->styleId;
      $prodcutFeatureId=DB::table('style_creation')->where('style_id','=',$styleId)->first();
      $style=StyleCreation::Join('product_feature_component','style_creation.product_feature_id','=','product_feature_component.product_feature_id')
              ->SELECT('product_feature_component.*')
              ->where('style_creation.product_feature_id','=',$prodcutFeatureId->product_feature_id)
              ->get();
                if(sizeof($style)>1){
                  $prodcutComponentId=$style[0]->product_component_id;
                  $productSilhouetteId=$style[0]->product_silhouette_id;
                  $style=json_decode(json_encode($style), true);
                //  dd($style[0]['product_feature_id']!=$prodcutFeatureId);
                  for($i=0;$i<sizeof($style);$i++){
                    if($style[$i]['product_feature_id']!=$prodcutFeatureId->product_feature_id||$style[$i]['product_component_id']!=$prodcutComponentId||$style[$i]['product_silhouette_id']!=$productSilhouetteId){
                      return response([
                        'data' => [
                          'message' => 'This Function can be use Only for Same type of Components',
                          'status' => '0',
                        ]
                      ]);
                    }

                  }
                  return response([
                    'data' => [
                      'message' => 'success',
                      'status' => '1',
                    ]
                  ]);

                }
              /*->groupBy('product_feature_component.product_feature_id')
              ->groupBy('product_feature_component.product_component_id')
              ->groupBy('product_feature_component.product_silhouette_id')*/

              //dd($style);
              else{
              return response([
                'data' => [
                  'message' => 'This Function can be use Only for Same type of Components',
                  'status' => '0',
                ]
              ]);
              //dd($style);
            }


    }

    //get a Service Type
    public function show($id)
    {


$component_smv_header_details = ComponentSMVHeader::join('style_creation','ie_component_smv_header.style_id','=','style_creation.style_id')
->join('merc_bom_stage','ie_component_smv_header.bom_stage_id','=','merc_bom_stage.bom_stage_id')
->join('merc_color_options','ie_component_smv_header.col_opt_id','=','merc_color_options.col_opt_id')
->leftjoin('buy_master','ie_component_smv_header.buy_id','=','buy_master.buy_id')
->select('ie_component_smv_header.*','merc_bom_stage.bom_stage_description','style_creation.style_no','merc_color_options.color_option','buy_master.buy_name')
->where('ie_component_smv_header.smv_component_header_id'  , '=', $id )
->get();
$componet_smv_details_list=ComponentSMVDetails::join('product_component','ie_component_smv_details.product_component_id','=','product_component.product_component_id')
->join('ie_garment_operation_master','ie_component_smv_details.garment_operation_id','=','ie_garment_operation_master.garment_operation_id')
->join('product_silhouette','ie_component_smv_details.product_silhouette_id','=','product_silhouette.product_silhouette_id')
->select('product_component.product_component_description','ie_garment_operation_master.garment_operation_name','product_silhouette.product_silhouette_description','ie_component_smv_details.*')
->where('ie_component_smv_details.smv_component_header_id','=',$id)
->orderBy('ie_component_smv_details.line_no')
//->orderBy('product_component.product_component_id')
//->orderBy('product_silhouette.product_silhouette_id')

->get();
$component_smv_summary=ComponentSMVSummary::join('product_component','ie_component_smv_summary.product_component_id','=','product_component.product_component_id')
->join('product_silhouette','ie_component_smv_summary.product_silhouette_id','=','product_silhouette.product_silhouette_id')
->join('ie_component_smv_header','ie_component_smv_summary.smv_component_header_id','=','ie_component_smv_header.smv_component_header_id')
->join('style_creation','ie_component_smv_header.style_id','=','style_creation.style_id')
->select('product_component.product_component_description','product_silhouette.product_silhouette_description','ie_component_smv_summary.*','style_creation.style_no','style_creation.style_id')
->where('ie_component_smv_summary.smv_component_header_id','=',$id)
->get();

$data=array($component_smv_header_details,$componet_smv_details_list,$component_smv_summary);

$styleId=$component_smv_header_details[0]->style_id;
$style=StyleCreation::select('*')
->where('style_id','=',$styleId)
->first();
$pFeatureID=$style->product_feature_id;
$cus=$style->customer_id;
$devision=$style->division_id;
$silhouetteList=ProductFeatureComponent::select('*')
->where('product_feature_id','=',$pFeatureID)
->where('status','=',1)
->get();
for($i=0;$i<sizeof($silhouetteList);$i++){
  $checkRange=SMVUpdate::select('*')
  //->where('customer_id','=',$cus)
  //->where('division_id','=',$devision)
  ->where('product_silhouette_id','=',$silhouetteList[$i]['product_silhouette_id'])
  ->where('status','=',1)
  ->first();
if($checkRange==null){
  return response(['data'=>"Error"]);

}

}
if($component_smv_header_details == null)
  throw new ModelNotFoundException("Requested Component SMV not found", 1);

else
  return response([ 'data' => $data]);




  }






    //deactivate a Service Type
    public function destroy($id)
    {
      $componetSmv = ComponentSMVHeader::where('smv_component_header_id', $id)->update(['status' => 0]);
      $componentSmvDetails=ComponentSMVDetails::where('smv_component_header_id',$id)->update(['status' => 0]);
      $componentSmvSummary=ComponentSMVSummary::where('smv_component_header_id',$id)->update(['status' => 0]);

      return response([
        'data' => [
          'message' => 'Component SMV deactivated successfully.',
          'componentSmv' => $componetSmv
        ]
      ]);
    }


    //validate anything based on requirements
    public function validate_data(Request $request){
      $for = $request->for;
      if($for == 'duplicate')
      {
        return response($this->validate_duplicate_code($request->garment_operation_id , $request->garment_operation_name));
      }
    }


    //check Service Type code already exists
    private function validate_duplicate_code($id , $code)
    {
      $garmentOperation = GarmentOperationMaster::where('garment_operation_name','=',$code)->first();
      if($garmentOperation == null){
        return ['status' => 'success'];
      }
      else if($garmentOperation->garment_operation_id == $id){
        return ['status' => 'success'];
      }
      else {
        return ['status' => 'error','message' => 'Garment Operation already exists'];
      }
    }


    //get filtered fields only
    private function list($active = 0 , $fields = null)
    {
      $query = null;
      if($fields == null || $fields == '') {
        $query = GarmentOperationMaster::select('*');
      }
      else{
        $fields = explode(',', $fields);
        $query = GarmentOperationMaster::select($fields);
        if($active != null && $active != ''){
          $query->where([['status', '=', $active]]);
        }
      }
      return $query->get();
    }

    //search Service Type for autocomplete
    private function autocomplete_search($search)
  	{
  		$garment_operation_lists = GarmentOperationMaster::select('garment_operation_id','garment_operation_name')
  		->where([['garment_operation_name', 'like', '%' . $search . '%'],]) ->get();
  		return $garment_operation_lists;
  	}

    private function details_search($styleId,$bomStageID,$colorOptionId,$buyId){

      $style=StyleCreation::select('*')
      ->where('style_id','=',$styleId)
      ->first();
      $pFeatureID=$style->product_feature_id;
      $cus=$style->customer_id;
      $devision=$style->division_id;
      $silhouetteList=ProductFeatureComponent::select('*')
      ->where('product_feature_id','=',$pFeatureID)
      ->where('status','=',1)
      ->get();
      for($i=0;$i<sizeof($silhouetteList);$i++){
        $checkRange=SMVUpdate::select('*')
        //->where('customer_id','=',$cus)
        //->where('division_id','=',$devision)
        ->where('status','=',1)
        ->where('product_silhouette_id','=',$silhouetteList[$i]['product_silhouette_id'])
        ->first();

        if($checkRange==null){
          $data=array('','','Please Set SMV Range To All Silhouettes','1');
          return $data;
        }

      }
      $component_smv=null;
    //  dd($buyId);
      if($buyId=="null"){
        //dd("in  null");
        $component_smv = ComponentSMVHeader::where('style_id','=',$styleId)
        ->where('bom_stage_id','=',$bomStageID)
        ->where('col_opt_id','=',$colorOptionId)
        ->where('status','=',1)
        ->select('*')
      //  ->toSql();
        ->first();
      }

      else if($buyId!="null"){
          //dd("in not null");
        $component_smv = ComponentSMVHeader::where('style_id','=',$styleId)
        ->where('bom_stage_id','=',$bomStageID)
        ->where('col_opt_id','=',$colorOptionId)
        ->where('buy_id','=',$buyId)
        ->where('status','=',1)
        ->select('*')
        //->toSql();
        ->first();
      }
        //echo("dfffffff");
        //echo($component_smv->smv_component_header_id);
      //  dd($component_smv);

        if($component_smv!=null){
          $id=$component_smv->smv_component_header_id;
            //echo($id);
            //echo("gdhdhdhdh");
          $component_smv_header_details = ComponentSMVHeader::join('style_creation','ie_component_smv_header.style_id','=','style_creation.style_id')
          ->join('merc_bom_stage','ie_component_smv_header.bom_stage_id','=','merc_bom_stage.bom_stage_id')
          ->join('merc_color_options','ie_component_smv_header.col_opt_id','=','merc_color_options.col_opt_id')
          ->leftjoin('buy_master','ie_component_smv_header.buy_id','=','buy_master.buy_id')
          ->select('ie_component_smv_header.*','merc_bom_stage.bom_stage_description','style_creation.style_no','merc_color_options.color_option','buy_master.buy_name')
          ->where('ie_component_smv_header.smv_component_header_id'  , '=', $id )
          ->get();
          $componet_smv_details_list=ComponentSMVDetails::join('product_component','ie_component_smv_details.product_component_id','=','product_component.product_component_id')
          ->join('ie_garment_operation_master','ie_component_smv_details.garment_operation_id','=','ie_garment_operation_master.garment_operation_id')
          ->join('product_silhouette','ie_component_smv_details.product_silhouette_id','=','product_silhouette.product_silhouette_id')
          ->select('product_component.product_component_description','ie_garment_operation_master.garment_operation_name','product_silhouette.product_silhouette_description','ie_component_smv_details.*')
          ->where('ie_component_smv_details.smv_component_header_id','=',$id)
          //->orderBy('product_component.product_component_id')
          //->orderBy('product_silhouette.product_silhouette_id')
          ->orderBy('ie_component_smv_details.line_no')
          ->get();
          //->toSql();
          //echo($componet_smv_details_list);
          //dd();
          $component_smv_summary=ComponentSMVSummary::join('product_component','ie_component_smv_summary.product_component_id','=','product_component.product_component_id')
          ->join('product_silhouette','ie_component_smv_summary.product_silhouette_id','=','product_silhouette.product_silhouette_id')
          ->join('ie_component_smv_header','ie_component_smv_summary.smv_component_header_id','=','ie_component_smv_header.smv_component_header_id')
          ->join('style_creation','ie_component_smv_header.style_id','=','style_creation.style_id')
          ->select('product_component.product_component_description','product_silhouette.product_silhouette_description','ie_component_smv_summary.*','style_creation.style_no','style_creation.style_id')
          ->where('ie_component_smv_summary.smv_component_header_id','=',$id)
          ->get();

          $data=array($component_smv_header_details,$componet_smv_details_list,$component_smv_summary,'0');
          return $data;

        }
        else if($component_smv==null){
      $details=ProductFeatureComponent::join('style_creation','style_creation.product_feature_id','=','product_feature_component.product_feature_id')
      ->join('product_feature as pf1','pf1.product_feature_id','=','product_feature_component.product_feature_id')
      ->join('product_silhouette','product_silhouette.product_silhouette_id','=','product_feature_component.product_silhouette_id')
      ->join('product_component','product_component.product_component_id','=','product_feature_component.product_component_id')
      ->select('product_feature_component.line_no','pf1.product_feature_description','pf1.product_feature_id','product_silhouette.product_silhouette_description','product_component.product_component_description','product_component.product_component_id','product_silhouette.product_silhouette_id')
      ->where('style_creation.style_id','=',$styleId)
      ->where('product_feature_component.status','=',1)
      //->toSql();
      //echo $details;
      ->get();
      return $details;
    }

    }

    //get searched Service Types for datatable plugin format
    private function datatable_search($data)
    {
      $start = $data['start'];
      $length = $data['length'];
      $draw = $data['draw'];
      $search = $data['search']['value'];
      $order = $data['order'][0];
      $order_column = $data['columns'][$order['column']]['data'];
      $order_type = $order['dir'];

      $component_smv_list = ComponentSMVHeader::join('style_creation','ie_component_smv_header.style_id','=','style_creation.style_id')
      ->join('merc_bom_stage','ie_component_smv_header.bom_stage_id','=','merc_bom_stage.bom_stage_id')
      ->join('merc_color_options','ie_component_smv_header.col_opt_id','=','merc_color_options.col_opt_id')
      ->leftjoin('buy_master','ie_component_smv_header.buy_id','=','buy_master.buy_id')
      ->select('ie_component_smv_header.smv_component_header_id','ie_component_smv_header.total_smv','merc_bom_stage.bom_stage_description','ie_component_smv_header.revision_no','ie_component_smv_header.comments','style_creation.style_no','merc_color_options.color_option','ie_component_smv_header.status','buy_master.buy_name')
      ->where('ie_component_smv_header.comments'  , 'like', $search.'%' )
      ->orWhere('style_creation.style_no'  , 'like', $search.'%' )
      ->orWhere('merc_color_options.color_option'  , 'like', $search.'%' )
      ->orWhere('merc_bom_stage.bom_stage_description'  , 'like', $search.'%' )
      ->orWhere('ie_component_smv_header.total_smv'  , 'like', $search.'%' )
      ->orWhere('buy_master.buy_name','like',$search.'%' )

      ->orderBy($order_column, $order_type)
      ->offset($start)->limit($length)->get();

      $component_smv_count = ComponentSMVHeader::join('style_creation','ie_component_smv_header.style_id','=','style_creation.style_id')
      ->join('merc_bom_stage','ie_component_smv_header.bom_stage_id','=','merc_bom_stage.bom_stage_id')
      ->join('merc_color_options','ie_component_smv_header.col_opt_id','=','merc_color_options.col_opt_id')
      ->leftjoin('buy_master','ie_component_smv_header.buy_id','=','buy_master.buy_id')
      ->select('ie_component_smv_header.smv_component_header_id','ie_component_smv_header.total_smv','merc_bom_stage.bom_stage_description','ie_component_smv_header.revision_no','ie_component_smv_header.comments','style_creation.style_no','merc_color_options.color_option','ie_component_smv_header.status')
      ->where('ie_component_smv_header.comments'  , 'like', $search.'%' )
      ->orWhere('style_creation.style_no'  , 'like', $search.'%' )
      ->orWhere('merc_color_options.color_option'  , 'like', $search.'%' )
      ->orWhere('merc_bom_stage.bom_stage_description'  , 'like', $search.'%' )
      ->orWhere('ie_component_smv_header.total_smv'  , 'like', $search.'%' )
      ->orWhere('buy_master.buy_name','like',$search.'%' )

      ->count();

      return [
          "draw" => $draw,
          "recordsTotal" => $component_smv_count,
          "recordsFiltered" => $component_smv_count,
          "data" => $component_smv_list
      ];
    }

public function validateGarmentOperations($dataSet){
//dd($value);
  foreach ($dataSet as $key => $value) {
  //  dd($value);
    $garmentOperation=GarmentOperationMaster::select('*')
    ->where('garment_operation_name','=',$value['garment_operation_name'])
    ->first();
    //dd($garmentOperation);
    if($garmentOperation==null){
      //dd($garmentOperation);
      return false;
    }
  }
  return true;
}

}
