<?php

namespace App\Http\Controllers\Merchandising;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Database\Eloquent\ModelNotFoundException;

use App\Http\Controllers\Controller;
//use App\Models\Merchandising\MaterialSize;
use App\Models\Org\Size;
use App\Models\Merchandising\Item\{Category, SubCategory};
use App\Models\Merchandising\MaterialRatio;

use Exception;

class MaterialSizeController extends Controller
{
    public function __construct()
    {
      //add functions names to 'except' paramert to skip authentication
      $this->middleware('jwt.verify', ['except' => ['index']]);
    }

    //get Feature list
    public function index(Request $request)
    {
      $type = $request->type;
      if($type == 'datatable') {
        $data = $request->all();
        return response($this->datatable_search($data));
      }
      else if($type == 'auto') {
        $search = $request->search;
        return response($this->autocomplete_search($search));
      }
      else {
        $active = $request->active;
        $fields = $request->fields;
        return response([
          'data' => $this->list($active , $fields)
        ]);
      }
    }


    //create a Material Size
    public function store(Request $request)
    {
      //$matsize = new MaterialSize();
      $matsize = new Size();
      if($matsize->validate($request->all()))
      {
        $matsize->size_name = $request->size_name;// cannot use fill function, beause same model use in org/SizeController
        $matsize->category_id = $request->category_id;
        $matsize->subcategory_id = $request->subcategory_id;
        $matsize->type = 'M';
        $matsize->status = 1;
        //$matsize->po_status = 0;
        //$matsize->division_id = -1;
        $matsize->save();

        return response([ 'data' => [
          'status' => 'success',
          'message' => 'Material Size saved successfully',
          'matsize' => $matsize
          ]
        ], Response::HTTP_CREATED );
      }
      else
      {
        $errors = $productSpecification->errors();// failure, get errors
        $errors_str = $productSpecification->errors_tostring();
        return response(['errors' => ['validationErrors' => $errors, 'validationErrorsText' => $errors_str]], Response::HTTP_UNPROCESSABLE_ENTITY);
      }
    }


    //get a Feature
    public function show($id)
    {
      $matsize = Size::find($id);
      $category = Category::find($matsize->category_id, ['category_id', 'category_name']);
      $sub_category = SubCategory::find($matsize->subcategory_id, ['subcategory_id', 'subcategory_name']);
      $matsize['category'] = $category;
      $matsize['sub_category'] = $sub_category;
      if($matsize == null)
        throw new ModelNotFoundException("Requested Material Size not found", 1);
      else
        return response([ 'data' => $matsize ]);
    }


    //update a Feature
    public function update(Request $request, $id)
    {
      //chek size already used
      $count = MaterialRatio::where('size_id', '=', $id)->count();
      if($count > 0){
        return response([ 'data' => [
          'status' => 'error',
          'message' => 'Cannot update material size. Already used.'
        ]]);
      }
      else {
        $matsize = Size::find($id);
        if($matsize->validate($request->all()))
        {
          $matsize->size_name = $request->size_name;// cannot use fill function, beause same model use in org/SizeController
          $matsize->category_id = $request->category_id;
          $matsize->subcategory_id = $request->subcategory_id;
          $matsize->save();

          return response([ 'data' => [
            'status' => 'success',
            'message' => 'Material size updated successfully',
            'matsize' => $matsize
          ]]);
        }
        else
        {
          $errors = $matsize->errors();// failure, get errors
          return response(['errors' => ['validationErrors' => $errors]], Response::HTTP_UNPROCESSABLE_ENTITY);
        }
      }
    }


    //deactivate a Feature
    public function destroy($id)
    {
      $count = MaterialRatio::where('size_id', '=', $id)->count();//chek size already used
      if($count > 0){
        return response([
          'data' => [
            'status' => 'error',
            'message' => 'Cannot delete material size. Already used.'
          ]
        ] , Response::HTTP_OK);
      }
      else {
        $matsize = Size::where('size_id', $id)->update(['status' => 0]);
        return response([
          'data' => [
            'status' => 'success',
            'message' => 'Material size deactivated successfully.',
            'matsize' => $matsize
          ]
        ] , Response::HTTP_NO_CONTENT);
      }
    }


    //validate anything based on requirements
    public function validate_data(Request $request){
      $for = $request->for;
      if($for == 'duplicate')
      {
        return response($this->validate_duplicate_code($request->size_id, $request->size_name,$request->category_id,
          $request->subcategory_id));
      }
    }


    //check Feature code already exists
    private function validate_duplicate_code($size_id, $size_name, $category_id, $subcategory_id)
    {
      $matsize = Size::where([['category_id', '=', $category_id], ['subcategory_id', '=', $subcategory_id], ['size_name', '=', $size_name]])->first();
      if($matsize == null){
        echo json_encode(array('status' => 'success'));
      }
      else if($matsize->size_id == $size_id){
        echo json_encode(array('status' => 'success'));
      }
      else {
        echo json_encode(array('status' => 'error','message' => 'Material size already exists'));
      }
    }


    //get filtered fields only
    private function list($active = 0 , $fields = null)
    {
      $query = null;
      if($fields == null || $fields == '') {
        $query = Size::select('*');
      }
      else{
        $fields = explode(',', $fields);
        $query = MaterialSize::select($fields);
        if($active != null && $active != ''){
          $query->where([['status', '=', $active]]);
        }
      }
      return $query->get();
    }

    //search Size for autocomplete
    private function autocomplete_search($search)
  	{
  		/*$matsize_lists = Size::join('item_category', 'org_size.category_id', '=' , 'item_category.category_id')
              ->join('item_subcategory', 'org_size.subcategory_id', '=' , 'item_subcategory.subcategory_id')
              ->select('org_size.*','item_subcategory.subcategory_name','item_category.category_name')
              ->where([['item_category.category_name', 'like', '%' . $search . '%']]) ->get();
               return $matsize_lists;*/
  	}

    public function get_sub_cat(Request $request){
        $category = $request->category_id;
        //$sub_category = SubCategory::where('category_id','=',$request->category_id)->pluck('subcategory_id', 'subcategory_name');
        $sub_category = SubCategory::join('item_category', 'item_subcategory.category_id', '=' , 'item_category.category_id')
              ->select('item_subcategory.subcategory_name','item_subcategory.subcategory_id')
              ->where('item_category.category_id','=', $category )
              ->get();
        echo json_encode($sub_category);
    }


    //get searched Features for datatable plugin format
    private function datatable_search($data)
    {
      $start = $data['start'];
      $length = $data['length'];
      $draw = $data['draw'];
      $search = $data['search']['value'];
      $order = $data['order'][0];
      $order_column = $data['columns'][$order['column']]['data'];
      $order_type = $order['dir'];

      $matsize_list =  Size::join('item_category', 'org_size.category_id', '=' , 'item_category.category_id')
          ->join('item_subcategory', 'org_size.subcategory_id', '=' , 'item_subcategory.subcategory_id')
          ->select('org_size.*','item_subcategory.subcategory_name','item_category.category_name')
          ->where('org_size.type', '=', 'M')
          ->where('item_category.category_name'  , 'like', $search.'%' )
          ->orwhere('item_subcategory.subcategory_name', 'like', $search.'%')
          ->orwhere('org_size.size_name', 'like', $search.'%')
          ->orderBy($order_column, $order_type)
          ->offset($start)->limit($length)->get();

      $matsize_count = Size::join('item_category', 'org_size.category_id', '=' , 'item_category.category_id')
          ->join('item_subcategory', 'org_size.subcategory_id', '=' , 'item_subcategory.subcategory_id')
          ->select('org_size.*','item_subcategory.subcategory_name','item_category.category_name')
          ->where('org_size.type', '=', 'M')
          ->where('item_category.category_name'  , 'like', $search.'%' )
          ->orwhere('item_subcategory.subcategory_name', 'like', $search.'%')
          ->orwhere('org_size.size_name', 'like', $search.'%')
          ->count();

      return [
          "draw" => $draw,
          "recordsTotal" => $matsize_count,
          "recordsFiltered" => $matsize_count,
          "data" => $matsize_list
      ];
    }

}
