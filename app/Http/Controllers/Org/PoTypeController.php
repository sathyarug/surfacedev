<?php

namespace App\Http\Controllers\Org;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Database\Eloquent\ModelNotFoundException;

use App\Http\Controllers\Controller;
use App\Models\Org\PoTypes;
use Exception;

class PoTypeController extends Controller
{
    public function __construct()
    {
      //add functions names to 'except' paramert to skip authentication
      $this->middleware('jwt.verify', ['except' => ['index']]);
    }

    //get Color list
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
      else {
        $active = $request->active;
        $fields = $request->fields;
        return response([
          'data' => $this->list($active , $fields)
        ]);
      }
    }


    //create a Color
    public function store(Request $request)
    {
      $potyp = new PoTypes();
      if($potyp->validate($request->all()))
      {
        $potyp->fill($request->all());
        $potyp->status = 1;
        $potyp->save();

        return response([ 'data' => [
          'message' => 'Color was saved successfully',
          'color' => $potyp
          ]
        ], Response::HTTP_CREATED );
      }
      else
      {
          $errors = $potyp->errors();// failure, get errors
          return response(['errors' => ['validationErrors' => $errors]], Response::HTTP_UNPROCESSABLE_ENTITY);
      }
    }


    //get a Color
    public function show($id)
    {
      $potyp = PoTypes::find($id);
      if($potyp == null)
        throw new ModelNotFoundException("Requested color not found", 1);
      else
        return response([ 'data' => $potyp ]);
    }


    //update a Color
    public function update(Request $request, $id)
    {
      $potyp = PoTypes::find($id);
      if($potyp->validate($request->all()))
      {
        $potyp->fill($request->except('color_code'));
        $potyp->save();

        return response([ 'data' => [
          'message' => 'Color was updated successfully',
          'color' => $potyp
        ]]);
      }
      else
      {
        $errors = $color->errors();// failure, get errors
        return response(['errors' => ['validationErrors' => $errors]], Response::HTTP_UNPROCESSABLE_ENTITY);
      }
    }


    //deactivate a Color
    public function destroy($id)
    {
      $potyp = PoTypes::where('color_id', $id)->update(['status' => 0]);
      return response([
        'data' => [
          'message' => 'Color was deactivated successfully.',
          'color' => $potyp
        ]
      ] , Response::HTTP_NO_CONTENT);
    }


    //validate anything based on requirements
    public function validate_data(Request $request){
      $for = $request->for;
      if($for == 'duplicate')
      {
        return response($this->validate_duplicate_code($request->color_id , $request->color_code));
      }
    }


    //check Color code already exists
    private function validate_duplicate_code($id , $code)
    {
      /*$color = PoTypes::where('color_code','=',$code)->first();
      if($color == null){
        return ['status' => 'success'];
      }
      else if($color->color_id == $id){
        return ['status' => 'success'];
      }
      else {
        return ['status' => 'error','message' => 'Color code already exists'];
      }*/
    }


    //get filtered fields only
    private function list($active = 0 , $fields = null)
    {
      $query = null;
      if($fields == null || $fields == '') {
        $query = PoTypes::select('*');
      }
      else{
        $fields = explode(',', $fields);
        $query = PoTypes::select($fields);
        if($active != null && $active != ''){
          $query->where([['status', '=', $active]]);
        }
      }
      return $query->get();
    }

    //search Color for autocomplete
    private function autocomplete_search($search)
  	{
  		$potyp = PoTypes::select('po_id','po_type','process_type')
  		->where([['po_type', 'like', '%' . $search . '%'],]) ->get();
  		return $potyp;
  	}


    //get searched Colors for datatable plugin format
    private function datatable_search($data)
    {
    /*  $start = $data['start'];
      $length = $data['length'];
      $draw = $data['draw'];
      $search = $data['search']['value'];
      $order = $data['order'][0];
      $order_column = $data['columns'][$order['column']]['data'];
      $order_type = $order['dir'];

      $color_list = Color::select('*')
      ->where('color_code'  , 'like', $search.'%' )
      ->orWhere('Color_name'  , 'like', $search.'%' )
      ->orderBy($order_column, $order_type)
      ->offset($start)->limit($length)->get();

      $color_count = Color::where('color_code'  , 'like', $search.'%' )
      ->orWhere('color_name'  , 'like', $search.'%' )
      ->count();

      return [
          "draw" => $draw,
          "recordsTotal" => $color_count,
          "recordsFiltered" => $color_count,
          "data" => $color_list
      ];*/
    }

}
