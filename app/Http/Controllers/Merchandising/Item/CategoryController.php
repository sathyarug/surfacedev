<?php

namespace App\Http\Controllers\Merchandising\Item;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Http\Controllers\Controller;

use App\Models\Merchandising\Item\Category;


class CategoryController extends Controller
{
    public function __construct()
    {
      //add functions names to 'except' paramert to skip authentication
      $this->middleware('jwt.verify', ['except' => []]);
    }

    public function index(Request $request)
    {
        //$active = $request->active;
        //$fields = $request->fields;
        $type = $request->type;
        if($type == 'handsontable'){
          return response([
            'data' => $this->handsontable_list()
          ]);
        }
        else{
          return response([
            'data' => $this->list()
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

     private function list(){
       $category_list = Category::where('status','=','1')->get();
       return $category_list;
     }

     private function handsontable_list(){
       $category_list = Category::where('status','=','1')->get()->pluck('category_name');
       return $category_list;
     }

}
