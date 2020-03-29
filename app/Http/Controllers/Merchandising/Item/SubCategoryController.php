<?php

namespace App\Http\Controllers\Merchandising\Item;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Http\Controllers\Controller;

use App\Models\Merchandising\Item\SubCategory;


class SubCategoryController extends Controller
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
        if($type == 'sub_category_by_category'){
          $category_id = $request->category_id;
          return response([
            'data' => $this->list($category_id)
          ]);
        }

    }

    public function store(Request $request)
    {

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
