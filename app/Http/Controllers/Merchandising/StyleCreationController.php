<?php

namespace App\Http\Controllers\Merchandising;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Http\Controllers\Controller;
use App\Models\Merchandising\StyleCreation;
use App\Models\Org\Customer;
use App\Models\Org\Division;
use App\Models\Merchandising\ProductFeature;
use App\Models\Merchandising\ProductSilhouette;
use App\Models\Org\SilhouetteClassification;
use App\Models\Merchandising\ProductCategory;
use App\Models\Merchandising\ProductType;
use App\Models\Merchandising\StyleProductFeature;
use App\Models\Merchandising\Costing\Costing;
use App\Models\Merchandising\ProductComponent;
use App\Models\IE\ComponentSMVHeader;
use DB;

//use Illuminate\Http\Response;

class StyleCreationController extends Controller
{
    public function __construct()
    {
        //add functions names to 'except' paramert to skip authentication
        $this->middleware('jwt.verify', ['except' => ['index', 'loadStyles','GetStyleDetails']]);
    }

    //get customer list
    public function index(Request $request)
    {
        $type = $request->type;

        if($type == 'datatable') {
            $data = $request->all();
            return response($this->datatable_search($data));
        }elseif($type == 'select')   {
            $active = $request->active;
            $fields = $request->fields;
            return response([
                'data' => $this->list($active , $fields)
            ]);
        }elseif($type == 'checkStyle')   {
            $id = $request->styleId;
            $code = $request->styleNo;
            return response($this->validate_duplicate_code($id , $code));

        }elseif($type == 'style_customer'){
            return response([
                'data' => $this->getCustomerForStyle($request->style)
            ]);
        }
        else if($type == 'auto') {
          $search = $request->search;
          return response($this->getStyleDetailsForSMV($search));
        }
        else{

            try{
                echo json_encode(StyleCreation::where('style_no', 'LIKE', '%'.$request->search.'%')->get());
            }
            catch (JWTException $e) {
                // something went wrong whilst attempting to encode the token
                return response()->json(['error' => 'could_not_create_token'], 500);
            }

        }

    }

    private function datatable_search($data)
    {
      $start = $data['start'];
      $length = $data['length'];
      $draw = $data['draw'];
      $search = $data['search']['value'];
      $order = $data['order'][0];
      $order_column = $data['columns'][$order['column']]['data'];
      $order_type = $order['dir'];
      $user = auth()->user();

      $cluster_list = StyleCreation::select('*')
      ->Where('created_by','=', $user->user_id)
  	  ->Where(function ($query) use ($search) {
  			$query->orWhere('style_no', 'like', $search.'%')
  				    ->orWhere('style_description', 'like', $search.'%')
  				    ->orWhere('remark_style', 'like', $search.'%');
  		        })
      ->orderBy($order_column, $order_type)
      ->offset($start)->limit($length)->get();

      $cluster_count = StyleCreation::select('*')
      ->Where('created_by', $user->user_id)
      ->Where(function ($query) use ($search) {
			$query->orWhere('style_no', 'like', $search.'%')
				    ->orWhere('style_description', 'like', $search.'%')
				    ->orWhere('remark_style', 'like', $search.'%');
		          })
      ->count();

      return [
          "draw" => $draw,
          "recordsTotal" => $cluster_count,
          "recordsFiltered" => $cluster_count,
          "data" => $cluster_list
      ];


    }

    public function saveStyleCreation(Request $request) {

        if($request->style_id != null){

          //$check_style = Costing::where([['status', '!=', 'CANCELED'],['style_id','=',$request->style_id]])->first();
          $check_style = ComponentSMVHeader::where([['status', '=', '1'],['style_id','=',$request->style_id]])->first();
          
          if($check_style != null)
          {
            return response(['data'=>['status'=>'0',]]);
          }else{
            $styleCreation = StyleCreation::find($request->style_id);
          }
          
        }else{

            $dataArr = array(
              'style_id' => null,
              'style_no' => $request->style_no,
              'customer_id' => $request->customer['customer_id'],
              'division_id' => $request->division,
              'product_feature_id' => $request->ProductFeature,
              'style_description' => $request->style_description,
              'product_silhouette_id' => $request->ProductSilhouette['product_silhouette_id'],
            );

            $styleCreation = new StyleCreation();

            if(!$styleCreation->validate($dataArr)) {
              $errors = $styleCreation->errors();// failure, get errors
              $errors_str = $styleCreation->errors_tostring();
              return response(['errors' => ['validationErrors' => $errors, 'validationErrorsText' => $errors_str]], Response::HTTP_UNPROCESSABLE_ENTITY);
            }
        }

        $styleCreation->style_no =strtoupper($request->style_no);
        $styleCreation->product_feature_id =$request->ProductFeature;
        $styleCreation->product_category_id =$request->ProductCategory['prod_cat_id'];
        $styleCreation->product_silhouette_id =$request->ProductSilhouette['product_silhouette_id'];
        $styleCreation->customer_id =$request->customer['customer_id'];
        //$styleCreation->pack_type_id =$request->ProductType['pack_type_id'];
        $styleCreation->division_id =$request->division;
        $styleCreation->style_description =strtoupper($request->style_description);
        $styleCreation->remark_style =strtoupper($request->Remarks);
        $styleCreation->remarks_pack =strtoupper($request->Remarks_pack);
        //$capitalizeAllFields=CapitalizeAllFields::setCapitalAll($styleCreation);
        $styleCreation->saveOrFail();

        $styleCreationUpdate = StyleCreation::find($styleCreation->style_id);
        $styleCreationUpdate->image =$styleCreation->style_id.'.png';
        $styleCreationUpdate->save();

        if($request->avatarHidden !=null){
            $this->saveImage($request->avatar['value'],$styleCreation->style_id);
        }

        if($request->style_id != null)
        {
          return response([ 'data' => [
            'message' => 'Style details updated successfully.',
            'image' =>$styleCreation->style_id.'.png'
          ]]);

        }else{

          return response([ 'data' => [
            'message' => 'Style details saved successfully.',
            'image' =>$styleCreation->style_id.'.png'
          ]]);

        }

       
    }


    private function saveImage($image,$id){

        // your base64 encoded
        if (!file_exists(public_path().'/assets/styleImage')) {
            mkdir(public_path().'/assets/styleImage', 0777, true);
        }

        if (file_exists(public_path().'/assets/styleImage/'.$id.'.png')) {
//            dd(public_path().'/assets/styleImage/'.$image);
            rename(public_path().'/assets/styleImage/'.$id.'.png', public_path().'/assets/styleImage/'.strtotime("now").'_'.$id.'.png');
        }
//dd($id);
        $image = str_replace('data:image/png;base64,', '', $image);
        $image = str_replace(' ', '+', $image);
        $imageName = $id.'.'.'png';

        DB::table('style_creation')
            ->where('style_id', $id)
            ->update(['upload_status' => '1']);

        \File::put(public_path().'/assets/styleImage/'.$imageName, base64_decode($image));
        return true;
    }

    public function loadStyles(){
        $style_list = StyleCreation::all();
        echo json_encode($style_list);
    }

    public function GetStyleDetails(Request $request){

        $style_details = new styleCreation();
        $result = $style_details->GetStyleDetailsByCode($request->style_id);

        echo json_encode($result);


    }

    //get a Section
    public function show($id)
    {

        $style = StyleCreation::with(['productFeature'])->find($id);

        $customer = Customer::find($style['customer_id']);
        $productFeature = ProductFeature::find($style['product_feature_id']);
        $ProductSilhouette = SilhouetteClassification::select('org_silhouette_classification.sil_class_id AS product_silhouette_id', 'org_silhouette_classification.sil_class_description AS product_silhouette_description')->find($style['product_silhouette_id']);
        $ProductCategory = ProductCategory::find($style['product_category_id']);
        $productType = ProductType::find($style['pack_type_id']);
        $divisions=DB::table('org_customer_divisions')
                  ->join('cust_division', 'org_customer_divisions.division_id', '=', 'cust_division.division_id')
                  ->select('org_customer_divisions.division_id AS division_id','cust_division.division_code','cust_division.division_description')
                  ->where('org_customer_divisions.division_id','=',$style['division_id'])
                  ->get();

                  //echo $divisions;
        // $avatarHidden = null;

        if($productFeature['count'] == 0){$pack_type = null;}else{$pack_type = $productFeature['count'].'-PACK';}
// dd($productFeature);
        $style['customer']=$customer;
        $style['product_f']=$productFeature;
        $style['product_f_pack_c']=$pack_type;
        $style['ProductSilhouette']=$ProductSilhouette;
        $style['ProductCategory']=$ProductCategory;
        $style['productType']=$productType;
        $style['division']=$divisions;
        $style['error']=1;
        // $style['image']=$avatarHidden;



//        dd($style);
//
//        foreach ($section AS $key=>$val){
//            dd($val);
//            //Customer::where('customer_id', '=', $request->search)->get()
//        }
        if($style == null)
            throw new ModelNotFoundException("Requested section not found", 1);
        else
            return response([ 'data' => $style ]);
    }

    //get filtered fields only
    private function list($active = 0 , $fields = null)
    {
        $query = null;
        if($fields == null || $fields == '') {
            $query = StyleCreation::select('*');
        }
        else{
            $fields = explode(',', $fields);
            $query = StyleCreation::select($fields);
            if($active != null && $active != ''){
                $payload = auth()->payload();
                $query->where([['status', '=', $active]]);
            }
        }
        return $query->get();
    }

    //deactivate a style
    public function destroy($id)
    {
      $check_style = Costing::where([['status', '!=', 'CANCELED'],['style_id','=',$id]])->first();
      if($check_style != null)
      {
        return response([
          'data'=>[
            'status'=>'0',
          ]
        ]);
        }else{
        $style = StyleCreation::where('style_id', $id)->update(['status' => 0]);
        return response([
            'data' => [
                'message' => 'Style was deactivated successfully.',
                'style' => $style
            ]
        ]);

      }
    }

    public function getCustomerForStyle($style){
        $cust = DB::table('style_creation')
            ->join('cust_customer', 'cust_customer.customer_id', '=', 'style_creation.customer_id')
            ->select('cust_customer.customer_id AS id','cust_customer.customer_name')
            ->where('style_creation.style_id','=',$style)
            ->first();

        return $cust;

    }

public function getStyleDetailsForSMV($search){
  $active=1;
  $style_lists = StyleCreation::select('style_id','style_no')
  ->where([['style_no', 'like', '%' . $search . '%'],])
  ->where('status','=',1)
  ->get();
  return $style_lists;


}



    //validate anything based on requirements
    public function validate_data(Request $request){
      $for = $request->for;
      if($for == 'duplicate')
      {
        return response($this->validate_duplicate_code($request->style_id , $request->style_no));
      }
    }


    //check Cluster code already exists
    private function validate_duplicate_code($id , $code)
    {

        $style = StyleCreation::where('style_no','=',$code)->where('status','=',1)->first();
        //echo $style;

        if($style == null){
            return ['status' => 'success'];
        }
        else if($style->style_id == $id){
            return ['status' => 'success'];
        }
        else {
            return ['status' => 'error','message' => 'Style no already exists'];
        }
    }



    public function pro_listload(Request $request){
      //$subCatCode2 = $request->subCatCode2;
      $subCat = ProductComponent::select('*')
         ->where('status' , '<>', 0 )
         ->get();

         return response([ 'count' => sizeof($subCat), 'subCat'=> $subCat ]);

    }





}
