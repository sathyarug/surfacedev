<?php
/**
 * Created by PhpStorm.
 * User: shanilad
 * Date: 9/4/2018
 * Time: 3:11 PM
 */

namespace App\Http\Controllers\Merchandising;

use Illuminate\Http\Request;
use App\Models\Merchandising\ProductType;
use App\Http\Controllers\Controller;
use App\Http\Resources\ProductCategoryResource;


class ProductTypeController extends Controller
{

    public function loadProductType(Request $request) {
        try{
//            echo json_encode(ProductCategory::all());
            echo json_encode(productType::where('pack_type_description', 'LIKE', '%'.$request->search.'%')->where('status',1)->get());
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