<?php

namespace App\Http\Controllers\Merchandising\Item;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Http\Controllers\Controller;

use App\Models\Merchandising\Item\PropertyValueAssign;


class ItemPropertyValueController extends Controller
{
    public function __construct()
    {
      //add functions names to 'except' paramert to skip authentication
      $this->middleware('jwt.verify', ['except' => []]);
    }

    public function index(Request $request)
    {
        $type = $request->type;
        //$active = $request->active;
        //$fields = $request->fields;
        /*if($type == 'sub_category_by_category'){
          $category_id = $request->category_id;
          return response([
            'data' => $this->list($category_id)
          ]);
        }*/

    }


    public function store(Request $request)
    {
      $property_value = new PropertyValueAssign();
      if($property_value->validate($request->all()))
      {
        $count = PropertyValueAssign::where('property_id','=',$request->property_id)
        ->where('assign_value','=',$request->assign_value)->count();
        if($count > 0){
          return response([
            'data' => [
              'status' => 'error',
              'message' => 'Property value already exists'
            ]
          ]);
        }
        else{
          $property_value->property_id = $request->property_id;
          $property_value->assign_value = $request->assign_value;
          $property_value->status = 1;
          $property_value->saveOrFail();

          return response([
            'data' => [
              'status' => 'success',
              'message' => 'Property value saved successfully'
            ]
          ]);
        }
      }
      else {
          $errors = $property_value->errors();// failure, get errors
          return response(['errors' => ['validationErrors' => $errors]], Response::HTTP_UNPROCESSABLE_ENTITY);
      }
    }


    public function show($id)
    {
    }

    public function edit($id)
    {
    }

    public function update(Request $request, $id)
    {
    }

     //deactivate a item
     public function destroy($id)
     {
     }

     public function list($category_id){
       $sub_category = SubCategory::where('category_id', '=', $category_id)->where('status','=','1')->get();
       return $sub_category;
     }

}
