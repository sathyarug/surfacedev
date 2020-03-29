<?php

namespace App\Http\Controllers\Finance\Item;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use Illuminate\Support\Facades\DB;

use App\Models\Finance\Item\SubCategory;
use App\Models\Finance\Item\Category;
use Exception;
use App\Libraries\AppAuthorize;

class ItemSubCategoryController extends Controller
{
    var $authorize = null;

    public function __construct()
    {
      //add functions names to 'except' paramert to skip authentication
      $this->middleware('jwt.verify', ['except' => ['index']]);
      $this->authorize = new AppAuthorize();
    }

    public function new(){
        $data = array(
          'categories' => Category::all()
        );
        return view('finance.item.item',$data);
    }

    public function save(Request $request){
      //print_r($request->all());die();

        $IsInspectionAllowed = 0;
        $IsDisplay = 0;
        $sub_category = new SubCategory();
        if ($sub_category->validate($request->all()))
        {
            if(is_null($request->is_inspectiion_allowed) || $request->is_inspectiion_allowed == false || $request->is_inspectiion_allowed == 0){
                $IsInspectionAllowed = 0;
            }else{
                $IsInspectionAllowed = 1;
            }

            if(is_null($request->is_display) || $request->is_display == false || $request->is_display == 0){
                $IsDisplay = 0;
            }else{
                $IsDisplay = 1;
            }


            if($request->subcategory_id > 0){
                $sub_category = SubCategory::find($request->subcategory_id);
                $is_exsits_in_item_property_assign=DB::table('item_property_assign')->where('subcategory_id','=',$request->subcategory_id)->exists();
                if($is_exsits_in_item_property_assign==true){
                return json_encode(array('status' => 'error' , 'message' => 'Sub category Already in Use'));
                }
                $sub_category->category_id = $request->category_code;
                $sub_category->subcategory_code = strtoupper($request->subcategory_code);
                $sub_category->subcategory_name = strtoupper($request->subcategory_name);
                $sub_category->is_inspectiion_allowed = $IsInspectionAllowed;
                $sub_category->is_display = $IsDisplay;
                $result = $sub_category->saveOrFail();

                echo json_encode(array('status' => 'success' , 'message' => 'Sub category details updated successfully.'));
            }
            else{

              if($sub_category->validate($request->all()))
              {
                $sub_category->fill($request->all());
                $sub_category->subcategory_code = strtoupper($request->subcategory_code);
                $sub_category->subcategory_name = strtoupper($request->subcategory_name);
                $sub_category->category_id = $request->category_code;
                $sub_category->is_inspectiion_allowed = $IsInspectionAllowed;
                $sub_category->is_display = $IsDisplay;
                $sub_category->status = 1;
                $sub_category->created_by = 1;
                $result = $sub_category->saveOrFail();

                echo json_encode(array('status' => 'success' , 'message' => 'Sub category details saved successfully.'));
              }
              else
              {
                $errors = $sub_category->errors();// failure, get errors
                $errors_str = $sub_category->errors_tostring();                
                echo json_encode(array('status' => 'error' , 'message' => $errors));
              }
            }

        }
        else
        {
            // failure, get errors
            $errors = $sub_category->errors_tostring();
            echo json_encode(array('status' => 'error' , 'message' => $errors));
        }


      /*}
      else{
        return response($this->authorize->error_response(), 401);
      }*/
    }

    public function get_sub_category_list(){
        //$sub_category_list = SubCategory::all();
        $sub_category_list = SubCategory::GetSubCategoryList();
        echo json_encode($sub_category_list);
    }


    public function get(Request $request){
      if($this->authorize->hasPermission('SUB_CAT_MANAGE'))//check permission
      {
        $sub_category_id = $request->subcategory_id;
        $sub_category = SubCategory::select("*")->where("subcategory_id","=",$sub_category_id)->get();
        echo json_encode($sub_category);
      }
      else{
        return response($this->authorize->error_response(), 401);
      }
    }


    public function check_sub_category_code(Request $request)
    {
        $sub_category = SubCategory::where('subcategory_code','=',$request->subcategory_code)->first();
        if($sub_category == null){
            $msg = ['status' => 'success'];
        }
        else if($sub_category->subcategory_id == $request->subcategory_id){
          $msg = ['status' => 'success'];
        }
        else{
          $msg = ['status' => 'error','message' => 'Record already exists'];
        }
        return response($msg);
    }


      public function change_status(Request $request){
        $count = DB::table('item_property_assign')->where('subcategory_id', '=', $request->subcategory_id)->count();
        if($count > 0){
          return response([
            'status' => 'error',
            'message' => 'Cannot deactivate. Has assigned properties.'
          ]);
        }
        else{
          $sub_category = SubCategory::find($request->subcategory_id);
          $sub_category->status = $request->status;
          $result = $sub_category->saveOrFail();
          return response([
            'status' => 'success'
          ]);
        }
    }

    public function get_category_list(){
        $category_list = Category::where('status','=','1')->get();
        echo json_encode($category_list);
    }

    public function get_subcat_list_by_maincat(Request $request){

        //$sub_category = SubCategory::where('category_id','=',$request->category_id)->pluck('subcategory_id', 'subcategory_name');
        //$sub_category = SubCategory::where('category_id','=',$request->category_id)->get();
        $sub_category = SubCategory::where('category_id','=',$request->category_id)->where('status','=','1')->get();
        echo json_encode($sub_category);
    }

    public function LoadSubCategoryList(Request $request)
    {
      if($this->authorize->hasPermission('SUB_CAT_MANAGE'))//check permission
      {
        $data = $request->all();
        $start = $data['start'];
        $length = $data['length'];
        $draw = $data['draw'];
        $search = $data['search']['value'];
        $order = $data['order'][0];
        $order_column = $data['columns'][$order['column']]['data'];
        $order_type = $order['dir'];

        //$sub_category_list = SubCategory::GetSubCategoryList();
        $sub_category_list = DB::table('item_subcategory')
              ->join('item_category','item_category.category_id','=','item_subcategory.category_id')
              ->select('item_subcategory.*','item_category.category_name')
              ->where('subcategory_code','like',$search.'%')
              ->orWhere('item_category.category_name'  , 'like', $search.'%' )
              ->orderBy($order_column, $order_type)
              ->offset($start)->limit($length)->get();

        $subCategoryCount = DB::table('item_subcategory')
              ->join('item_category','item_category.category_id','=','item_subcategory.category_id')
              ->select('item_subcategory.*','item_category.category_name')
              ->where('subcategory_code','like',$search.'%')
              ->orWhere('item_category.category_name'  , 'like', $search.'%' )
              ->count();

        echo json_encode(array(
            "draw" => $draw,
            "recordsTotal" => $subCategoryCount,
            "recordsFiltered" => $subCategoryCount,
            "data" => $sub_category_list
        ));
        //echo json_encode($sub_category_list);
      }
      else{
        return response($this->authorize->error_response(), 401);
      }
    }

}

?>
