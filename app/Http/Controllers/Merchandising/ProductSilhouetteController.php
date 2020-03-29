<?php
/**
 * Created by PhpStorm.
 * User: shanilad
 * Date: 9/6/2018
 * Time: 3:09 PM
 */

namespace App\Http\Controllers\Merchandising;

use Illuminate\Http\Request;
use App\Models\Merchandising\ProductSilhouette;
use App\Models\Org\SilhouetteClassification;
use App\Http\Controllers\Controller;
use App\Http\Resources\ProductCategoryResource;




class ProductSilhouetteController extends Controller
{

    public function __construct()
    {
      //add functions names to 'except' paramert to skip authentication
      $this->middleware('jwt.verify', ['except' => ['index']]);
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
      else if($type == 'auto_2') {
        $search = $request->search;
        $comp_id = $request->comp_id;
        return response([
          'data' => $this->handsontable_list($comp_id, $search)
        ]);
      }
      else {
        $active = $request->active;
        $fields = $request->fields;
        return response([
          'data' => $this->list($active , $fields)
        ]);
      }
    }

    private function handsontable_list($comp_id, $search){
      $list = ProductSilhouette::where('status','1')
      ->where('product_component', '=', $comp_id)
      ->where('product_silhouette_description', 'like', '%' . $search . '%')
      ->get()
      ->pluck('product_silhouette_description');
      return $list;
    }

    private function autocomplete_search($search)
  	{
  		$silhouette_lists = ProductSilhouette::where([['product_silhouette_description', 'like', '%' . $search . '%'],])
       ->where('status','1')
       ->pluck('product_silhouette_description')
       ->toArray();
  		return  json_encode($silhouette_lists);
  	}


    public function loadProductSilhouette(Request $request) {
        try{
            echo json_encode(ProductSilhouette::where('product_silhouette_description', 'LIKE', '%'.$request->search.'%')->where('status',1)->get());
        }
        catch (JWTException $e) {

            return response()->json(['error' => 'could_not_create_token'], 500);
        }
    }

    public function loadProductSilhouetteHome(Request $request) {
        try{
            echo json_encode(SilhouetteClassification::select('org_silhouette_classification.sil_class_id AS product_silhouette_id', 'org_silhouette_classification.sil_class_description AS product_silhouette_description')
            ->where('sil_class_description', 'LIKE', '%'.$request->search.'%')->where('status',1)->get());
        }
        catch (JWTException $e) {

            return response()->json(['error' => 'could_not_create_token'], 500);
        }
    }
}
