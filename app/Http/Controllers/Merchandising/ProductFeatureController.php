<?php
/**
 * Created by PhpStorm.
 * User: shanilad
 * Date: 9/5/2018
 * Time: 4:10 PM
 */

namespace App\Http\Controllers\Merchandising;

use Illuminate\Http\Request;
use App\Models\Merchandising\ProductFeature;
use App\Models\Merchandising\ProductFeatureComponent;
use App\Models\Merchandising\ProductSilhouette;
use App\Models\Merchandising\StyleCreation;
use App\Models\IE\ComponentSMVHeader;
use App\Http\Controllers\Controller;
use App\Http\Resources\ProductCategoryResource;
use App\Libraries\AppAuthorize;
use App\Models\Merchandising\ProductComponent;
use Illuminate\Support\Facades\DB;


class ProductFeatureController extends Controller
{

    var $authorize = null;

    public function loadProductFeature(Request $request) {
//        print_r('sss');exit;
        try{
//            echo json_encode(ProductCategory::all());
            echo json_encode(ProductFeature::where('product_feature_description', 'LIKE', '%'.$request->search.'%')
            ->where('status',1)->get());
//            return ProductCategoryResource::collection(ProductCategory::where('prod_cat_description', 'LIKE', '%'.$request->search.'%')->get() );
        }
        catch (JWTException $e) {
            // something went wrong whilst attempting to encode the token
            return response()->json(['error' => 'could_not_create_token'], 500);
        }
//        $customer_list = Customer::all();
//        echo json_encode($customer_list);
    }

    private function get_next_line_no($po)
      {
        $max_no = PoOrderDetails::where('po_no','=',$po)->max('line_no');
  	  if($max_no == NULL){ $max_no= 0;}
        return ($max_no + 1);
      }


    public function save_product_feature(Request $request){
      $lines = $request->lines;
      //print_r($lines);
      //die();

      if($lines != null && sizeof($lines) >= 1){

        for($r = 0 ; $r < sizeof($lines) ; $r++)
        {

            if(isset($lines[$r]['assign']) == '')
              {
                $line_id = $r+1;
                $err = 'Product Feature Line '.$line_id.' cannot be empty.';
                return response([ 'data' => ['status' => 'error','message' => $err]]);

              }

            $silhouette2 = ProductSilhouette::select('product_silhouette_id')
              ->where('product_silhouette_description','=',$lines[$r]['product_silhouette_description'])
              ->where('product_component','=',$lines[$r]['pro_com_id'])
              ->first();


             if(isset($silhouette2['product_silhouette_id']) == null)
                  {
                    $line_id = $r+1;
                    $err = 'Incorrect Product Silhouette type. Line -'.$line_id.'.';
                    return response([ 'data' => ['status' => 'error','message' => $err]]);

                  }

              if(isset($lines[$r]['product_silhouette_description']) == '')
                {
                  $line_id = $r+1;
                  $err = 'Silhouette Line '.$line_id.' cannot be empty.';
                  return response([ 'data' => ['status' => 'error','message' => $err]]);

                }

              if(isset($lines[$r]['emb']) == '')
                {
                  $line_id = $r+1;
                  $err = 'Emblishment Line '.$line_id.' cannot be empty.';
                  return response([ 'data' => ['status' => 'error','message' => $err]]);

                }

              if(isset($lines[$r]['wash']) == '')
                {
                    $line_id = $r+1;
                    $err = 'Washing Line '.$line_id.' cannot be empty.';
                    return response([ 'data' => ['status' => 'error','message' => $err]]);

                }

        }


        $PF = new ProductFeature();
        $PF ->status = 1;
        $PF ->save();

        $max_f_n = $PF['product_feature_id'];

        $a = 1;
        for($x = 0 ; $x < sizeof($lines) ; $x++) {

        if($lines[$x]['emb'] == "YES"){ $emblishment = 1; }else { $emblishment = 0; }
        if($lines[$x]['wash'] == "YES"){ $washing = 1; }else{ $washing= 0; }
        if(isset($lines[$x]['display'])== ''){$dis = '';}else{ $dis = strtoupper($lines[$x]['display']); }

        $silhouette = ProductSilhouette::select('*')
        ->where('product_silhouette_description','=',$lines[$x]['product_silhouette_description'])
        ->where('product_component','=',$lines[$x]['pro_com_id'])
        ->first();

        $PFC = new ProductFeatureComponent();
        $PFC->product_feature_id = $max_f_n;
        $PFC->product_component_id = $lines[$x]['pro_com_id'];
        $PFC->product_silhouette_id = $silhouette->product_silhouette_id;
        $PFC->line_no = $a ;
        $PFC->display_name = $dis;
        $PFC->emblishment = $emblishment;
        $PFC->washing = $washing;
        $PFC->status = 1;
        $PFC->save();
        $a++;
        }

        $pfc_list= ProductFeatureComponent::select(DB::raw('Count(product_component.product_component_description) as Count'),'product_component.product_component_description')
        ->join('product_component','product_feature_component.product_component_id','=','product_component.product_component_id')
        ->where('product_feature_component.product_feature_id','=',$max_f_n)
        ->groupBy('product_feature_component.product_component_id')
        ->get();

        $f = '';$a=array();
        for($y = 0 ; $y < sizeof($pfc_list) ; $y++) {
          $d = $pfc_list[$y]->Count;
          $e = $pfc_list[$y]->product_component_description;
          $f = $d.' '.$e;
          array_push($a,$f);
        }

        $separated = implode(" | ", $a);

        $PFU = ProductFeature::find($max_f_n);
        $PFU ->product_feature_description = strtoupper($separated);
        $PFU ->count = sizeof($lines);
        $PFU ->save();

        if(sizeof($lines) == 0){$pack_type = null;}else{$pack_type = sizeof($lines).'-PACK';}

        return response([
          'data' => [
            'status' => 'success',
            'message' => 'Saved successfully.',
            'max_f' => $max_f_n,
            'max_f_d' => strtoupper($separated),
            'max_f_c' => $pack_type
          ]
        ] , 200);

      }

    }

    public function pro_listload_edit(Request $request){
      $id = $request->id;

      $subCat = ProductFeatureComponent::select('product_component.product_component_description AS assign','product_feature_component.display_name AS display','product_silhouette.product_silhouette_description','product_feature_component.emblishment AS emb','product_feature_component.washing AS wash','product_feature_component.feature_component_id','product_feature_component.product_component_id AS pro_com_id')
         ->join('product_component','product_feature_component.product_component_id','=','product_component.product_component_id')
         ->leftjoin('product_silhouette','product_feature_component.product_silhouette_id','=','product_silhouette.product_silhouette_id')
         ->where('product_feature_id' , '=', $id )
         ->where('product_feature_component.status' , '<>', 0 )
         ->get();

      $subCat1 = ProductComponent::select('*')
         ->where('status' , '<>', 0 )
         ->get();

      return response([ 'count'   => sizeof($subCat), 'subCat'=> $subCat,
                        'count2'  => sizeof($subCat1), 'subCat2'=> $subCat1 ]);

    }

    public function destroy($id)
    {
      $fe_data = ProductFeatureComponent::select('product_feature_id')->where('feature_component_id','=',$id)->first();
      $style_id = StyleCreation::select('style_id')->where('product_feature_id','=',$fe_data['product_feature_id'])->first();
      //echo $style_id['style_id'];
      //die();

      $check_smv_table = ComponentSMVHeader::where([['status', '=', '1'],['style_id','=',$style_id['style_id']]])->first();
      if($check_smv_table != null)
      {
        return response([
          'data'=>[
            'status'=>'0',
          ]
        ]);
      }else{


      $pro_f = ProductFeatureComponent::where('feature_component_id', $id)->update(['status' => 0]);
      $lines = ProductFeatureComponent::select('*')
      ->where([['status', '=', '1'],['product_feature_id','=',$fe_data['product_feature_id']]])
      ->get();

      $pfc_list= ProductFeatureComponent::select(DB::raw('Count(product_component.product_component_description) as Count'),'product_component.product_component_description')
      ->join('product_component','product_feature_component.product_component_id','=','product_component.product_component_id')
      ->where('product_feature_component.product_feature_id','=',$fe_data['product_feature_id'])
      ->where('product_feature_component.status' , '<>', 0 )
      ->groupBy('product_feature_component.product_component_id')
      ->get();

      $f = '';$a=array();
      for($y = 0 ; $y < sizeof($pfc_list) ; $y++) {
        $d = $pfc_list[$y]->Count;
        $e = $pfc_list[$y]->product_component_description;
        $f = $d.' '.$e;
        array_push($a,$f);
      }

      $separated = implode(" | ", $a);

      //$PF = new productFeature();
      $PF = ProductFeature::find($fe_data['product_feature_id']);
      $PF ->product_feature_description = strtoupper($separated);
      $PF ->count = sizeof($lines);
      $PF ->save();

      if(sizeof($lines) == 0){$pack_type = null;}else{$pack_type = sizeof($lines).'-PACK';}

      return response([
        'data' => [
          'message' => 'Product Feature deactivated successfully.',
          'max_f' => $fe_data['product_feature_id'],
          'prod_f' => $pro_f,
          'max_f_d' => strtoupper($separated),
          'max_f_c' => $pack_type
        ]
      ]);

      }

    }

    public function update_product_feature(Request $request){
      $lines = $request->lines;
      $fe_data = $request->fe_data;

      $style_id = StyleCreation::select('style_id')->where('product_feature_id','=',$fe_data)->first();
      $check_smv_table = ComponentSMVHeader::where([['status', '=', '1'],['style_id','=',$style_id['style_id']]])->first();
      if($check_smv_table != null)
      {

        $err = "Can't Update , Product Feature already in use.";
        return response([ 'data' => ['status' => 'error','message' => $err]]);
      }else{

      if($lines != null && sizeof($lines) >= 1){

        for($r = 0 ; $r < sizeof($lines) ; $r++)
        {
            if(isset($lines[$r]['assign']) == '')
              {
                $line_id = $r+1;
                $err = 'Product Feature Line '.$line_id.' cannot be empty.';
                return response([ 'data' => ['status' => 'error','message' => $err]]);

              }

            if(isset($lines[$r]['product_silhouette_description']) == '')
              {
                $line_id = $r+1;
                $err = 'Silhouette Line '.$line_id.' cannot be empty.';
                return response([ 'data' => ['status' => 'error','message' => $err]]);

              }

            if(isset($lines[$r]['emb']) == '')
              {
                  $line_id = $r+1;
                  $err = 'Emblishment Line '.$line_id.' cannot be empty.';
                  return response([ 'data' => ['status' => 'error','message' => $err]]);

              }

            if(isset($lines[$r]['wash']) == '')
              {
                    $line_id = $r+1;
                    $err = 'Washing Line '.$line_id.' cannot be empty.';
                    return response([ 'data' => ['status' => 'error','message' => $err]]);

              }

            if(isset($lines[$r]['feature_component_id']) == '')
              {
                $style_id = StyleCreation::select('style_id')->where('product_feature_id','=',$fe_data)->first();
                $check_smv_table = ComponentSMVHeader::where([['status', '=', '1'],['style_id','=',$style_id['style_id']]])->first();
                if($check_smv_table != null)
                {
                  $line_id = $r+1;
                  $err = "Product Feature already in use Line '.$line_id.'";
                  return response([ 'data' => ['status' => 'error','message' => $err]]);
                }
              }

        }

          for($x = 0 ; $x < sizeof($lines) ; $x++) {

          if($lines[$x]['emb'] == "YES"){ $emblishment = 1; }else { $emblishment = 0; }
          if($lines[$x]['wash'] == "YES"){ $washing = 1; }else { $washing= 0; }
          if(isset($lines[$x]['display'])== ''){$dis = '';}else{ $dis = strtoupper($lines[$x]['display']); }
          if(isset($lines[$x]['feature_component_id']) == '')
          {
            $max_id_temp = ProductFeatureComponent::where('product_feature_id','=',$fe_data)->max('line_no');
            $id = ProductFeatureComponent::insertGetId(['status' => 0, 'line_no' => $max_id_temp+1]);
            $fc_id = $id;
          }else{
            $fc_id = $lines[$x]['feature_component_id'];
          }

          $silhouette = ProductSilhouette::select('*')
          ->where('product_silhouette_description','=',$lines[$x]['product_silhouette_description'])
          ->first();

          $PF = ProductFeatureComponent::find($fc_id);
          $PF->product_feature_id= $fe_data;
          $PF->product_silhouette_id = $silhouette->product_silhouette_id;
          $PF->product_component_id = $lines[$x]['pro_com_id'];
          $PF->display_name = $dis;
          $PF->emblishment = $emblishment;
          $PF->washing = $washing;
          $PF->status = 1;
          $PF->save();

          }


          $pfc_list= ProductFeatureComponent::select(DB::raw('Count(product_component.product_component_description) as Count'),'product_component.product_component_description')
          ->join('product_component','product_feature_component.product_component_id','=','product_component.product_component_id')
          ->where('product_feature_component.product_feature_id','=',$fe_data)
          ->where('product_feature_component.status' , '<>', 0 )
          ->groupBy('product_feature_component.product_component_id')
          ->get();

          $f = '';$a=array();
          for($y = 0 ; $y < sizeof($pfc_list) ; $y++) {
            $d = $pfc_list[$y]->Count;
            $e = $pfc_list[$y]->product_component_description;
            $f = $d.' '.$e;
            array_push($a,$f);
          }

          $separated = implode(" | ", $a);

          //$PF = new productFeature();
          $PF = ProductFeature::find($fe_data);
          $PF ->product_feature_description = strtoupper($separated);
          $PF ->count = sizeof($lines);
          $PF ->save();

          if(sizeof($lines) == 0){$pack_type = null;}else{$pack_type = sizeof($lines).'-PACK';}

        return response([ 'data' => [
          'message' => 'Product Feature updated successfully',
          'prod_f' => $PF,
          'max_f' => $fe_data,
          'max_f_d' => strtoupper($separated),
          'max_f_c' => $pack_type
        ]]);



      }

    }


    }

    public function save_line_fe(Request $request){

      $assign = $request->assign;
      $pro_com_id = $request->pro_com_id;
      $fe_data = $request->fe_data;

      $max_line_id = ProductFeatureComponent::where('product_feature_id','=',$fe_data)->max('line_no');

      $PF = new ProductFeatureComponent();
      $PF->product_feature_id = $fe_data;
      $PF->product_component_id = $pro_com_id;
      $PF->line_no = $max_line_id + 1;
      $PF->emblishment = 0;
      $PF->washing = 0;
      $PF->saveOrFail();

      //echo $PF->feature_component_id;


      $subCat = ProductFeatureComponent::select('product_component.product_component_description AS assign','product_feature_component.feature_component_id','product_feature_component.product_component_id AS pro_com_id','product_feature_component.emblishment AS emb','product_feature_component.washing AS wash')
         ->join('product_component','product_feature_component.product_component_id','=','product_component.product_component_id')
         ->where('feature_component_id' , '=', $PF->feature_component_id )
         ->get();

      return response([ 'count'   => sizeof($subCat), 'subCat'=> $subCat]);

    }

  /*  public function delete_feature_temp(Request $request){
        $fe_data = $request->fe_data;

        $pf = ProductFeatureComponent::select('feature_component_id')
        ->where('product_silhouette_id','=',null)
        ->where('product_feature_id','=',$fe_data)
        ->first();
        //echo $pf->feature_component_id;
        //die();
        //ProductFeatureComponent::where('feature_component_id','=',$pf->feature_component_id)->delete();
        if($pf != null){
          return response(['error' => ['status' => 'error',]], 200);

        }else{

          return response([ 'data' => [ 'status' => 'success' ] ] , 200);


        }




    }*/





}
