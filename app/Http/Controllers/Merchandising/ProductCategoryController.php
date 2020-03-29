<?php

namespace App\Http\Controllers\Merchandising;

use Illuminate\Http\Request;
use App\Models\Merchandising\ProductCategory;
use App\Models\Org\Customer;
use App\Http\Controllers\Controller;
use App\Http\Resources\ProductCategoryResource;

class ProductCategoryController extends Controller
{
    
    
    
  //   public function postdata(Request $request)
  //  { 
       
  //       $ProCat = new Product_Category(); 
        
  //       if ($ProCat->validate($request->all()))   
  //       {
  //           if($request->prod_cat_description_hid > 0){
  //               $ProCat = sty_prod_category::find($request->prod_cat_description_hid);
  //           }     
  //           $ProCat->fill($request->all());
  //           //print_r($ProCat);
  //           $ProCat->status= 1 ;
  //          // $OrgStore->loc_id = fac_location;
  //           $ProCat->created_by = 1;  
  //           $result = $ProCat->saveOrFail();
  //          // echo json_encode(array('Saved'));
  //           if($result)
  //             {
  //               echo json_encode(["message"=>$result]);
  //                 //return response()->json(["message"=>$result]);
  //             }
  //       }
  //       else
  //       {            
  //           // failure, get errors
  //           $errors = $ProCat->errors();
  //           print_r($errors);
  //       }        


  //  }
   

        
  //      public function loaddata()
  //  {
  //  		 $category_list = Product_Category::all();
  //                //print_r($category_list);
  //      		 echo json_encode($category_list);

  //  }
        
  //        public function edit(Request $request)
  //  {

  //       $store_id= $request->store_id;
  //      // $store = OrgStore::find($store_id);  
        
  //        $store = OrgStore::join('org_location', 'org_store.loc_id', '=', 'org_location.loc_id')
		// ->select('org_store.*', 'org_store.store_id','org_location.company_code')
  //               ->where('org_store.store_id', '=', $store_id)
		// ->get();
        
        
        
  //       echo json_encode($store);
   			
  //  }
   
  //   public function delete(Request $request)
  //  {
  //       $store_id = $request->store_id;
  //       //$source = Main_Source::find($source_id);
  //       //$source->delete();
  //       $store = OrgStore::where('store_id', $store_id)->update(['status' => 0]);
  //       echo json_encode(array('delete'));
  //  }
   
   
  //   public function check_Store_Name(Request $request)
  //  {


  //  	$count = OrgStore::where('store_name','=',$request->code)->count();

  //       if($request->idcode > 0){

  //         $user = OrgStore::where('store_id', $request->idcode)->first();

  //             if($user->store_name == $request->code)
  //             {
  //                 $msg = true;

  //             }else{

  //                 $msg = 'Already exists. please try another one';

  //             }


  //       }else{

  //           if($count == 1){ 

  //                 $msg = 'Already exists. please try another one'; 

  //             }else{ 

  //                 $msg = true; 
                  
  //             }

  //       }
  //  			echo json_encode($msg);
  //  }


  //   public function load_fac_locations(Request $request){

  //           $search_c = $request->search;
  //          // print_r($search_c);
  //           $fac_lists = OrgLocation::select('loc_id','loc_code','company_code')
  //           ->where([['company_code', 'like', '%' . $search_c . '%'],]) ->get();


  //           return response()->json(['items'=>$fac_lists]);
  //           //return $select_source;

  //   }
    
  //   public function load_fac_section(Request $request){

  //           $search_c = $request->search;
  //          // print_r($search_c);
  //           $fac_lists = OrgLocation::select('loc_id','loc_code','company_code')
  //           ->where([['company_code', 'like', '%' . $search_c . '%'],]) ->get();


  //           return response()->json(['items'=>$fac_lists]);
  //           //return $select_source;

  //   }

    public function loadProductCategory(Request $request) {
        try{
//            echo json_encode(ProductCategory::all());
            echo json_encode(ProductCategory::where('prod_cat_description', 'LIKE', '%'.$request->search.'%')->where('status',1)->get());
//            return ProductCategoryResource::collection(ProductCategory::where('prod_cat_description', 'LIKE', '%'.$request->search.'%')->get() );
        }
        catch (JWTException $e) {
            // something went wrong whilst attempting to encode the token
            return response()->json(['error' => 'could_not_create_token'], 500);
        }
//        $customer_list = Customer::all();
//        echo json_encode($customer_list);
    }


}