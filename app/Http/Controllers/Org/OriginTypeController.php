<?php

namespace App\Http\Controllers\Org;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Database\Eloquent\ModelNotFoundException;

use App\Http\Controllers\Controller;
use App\Models\Org\OriginType;
use Exception;
use Illuminate\Support\Facades\DB;

class OriginTypeController extends Controller
{
    public function __construct()
    {
      //add functions names to 'except' paramert to skip authentication
      $this->middleware('jwt.verify', ['except' => ['index']]);
    }

    //get Origin Type list
    public function index(Request $request)
    {
      $type = $request->type;
      if($type == 'datatable'){
        $data = $request->all();
        return response($this->datatable_search($data));
      }
      else if($type == 'auto'){
        $search = $request->search;
        return response($this->autocomplete_search($search));
      }
      else if($type == 'handsontable'){
        $search = $request->search;
        return response([
          'data' => $this->handsontable_search($search)
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


    //create a Origin Type
    public function store(Request $request)
    {
      $originType = new OriginType();
      if($originType->validate($request->all()))
      {
        $originType->fill($request->all());
        $originType->status = 1;
        //$originType->origin_type=strtoupper($originType->origin_type);
        $originType->save();

        return response([ 'data' => [
          'message' => 'Origin type saved successfully',
          'originType' => $originType,
          'status'=>1
          ]
        ], Response::HTTP_CREATED );
      }
      else
      {
        $errors = $originType->errors();// failure, get errors
        $errors_str = $originType->errors_tostring();
        return response(['errors' => ['validationErrors' => $errors, 'validationErrorsText' => $errors_str]], Response::HTTP_UNPROCESSABLE_ENTITY);
      }
    }


    //get a Origin Type
    public function show($id)
    {
      $originType = OriginType::find($id);
      if($originType == null)
        throw new ModelNotFoundException("Requested origin type not found", 1);
      else
        return response([ 'data' => $originType ]);
    }


    //update a Origin Type
    public function update(Request $request, $id)
    {
      $originType = OriginType::find($id);
      $costing=DB::table('costing_finish_good_component_items')->where('origin_type_id','=',$id)->exists();
      if($originType->validate($request->all()))
      {
        if($costing==true){
          return response([ 'data' => [
            'message' => 'Origin type already in use',
            'originType' => $originType,
            'status'=>0
          ]]);
        }
        else if($costing==false){
          $originType->fill($request->except('origin_type'));
        //$originType->origin_type=strtoupper($originType->origin_type);
        $originType->save();
      return response([ 'data' => [
          'message' => 'Origin type updated successfully',
          'originType' => $originType,
          'status'=>1
        ]]);
      }
      }
      else
      {
        $errors = $originType->errors();// failure, get errors
        $errors_str = $originType->errors_tostring();
        return response(['errors' => ['validationErrors' => $errors, 'validationErrorsText' => $errors_str]], Response::HTTP_UNPROCESSABLE_ENTITY);
      }
    }


    //deactivate a Origin Type
    public function destroy($id)
    {
      $costing=DB::table('costing_finish_good_component_items')->where('origin_type_id','=',$id)->exists();

      if($costing==true){
        return response([
          'data' => [
            'message' => 'Origin type already in Use',
            'originType' => $costing,
            'status'=>0
          ]
        ]);
      }
        else if($costing==false){
        $originType = OriginType::where('origin_type_id', $id)->update(['status' => 0]);
      return response([
        'data' => [
          'message' => 'Origin type was deactivated successfully.',
          'originType' => $originType,
          'status'=>1
        ]
      ]);
    }
    }


    //validate anything based on requirements
    public function validate_data(Request $request){
      $for = $request->for;
      if($for == 'duplicate')
      {
        return response($this->validate_duplicate_code($request->origin_type_id , $request->origin_type));
      }
    }


    //check OriginType code already exists
    private function validate_duplicate_code($id , $code)
    {
      $originType = OriginType::where('origin_type','=',$code)->first();
      if($originType == null){
        return ['status' => 'success'];
      }
      else if($originType->origin_type_id == $id){
        return ['status' => 'success'];
      }
      else {
        return ['status' => 'error','message' => 'Origin type code already exists'];
      }
    }


    //get filtered fields only
    private function list($active = 0 , $fields = null)
    {
      $query = null;
      if($fields == null || $fields == '') {
        $query = OriginType::select('*');
      }
      else{
        $fields = explode(',', $fields);
        $query = OriginType::select($fields);
        if($active != null && $active != ''){
          $query->where([['status', '=', $active]]);
        }
      }
      return $query->get();
    }

    //search Origin Type for autocomplete
    private function autocomplete_search($search)
  	{
  		$origin_type_lists = OriginType::select('origin_type_id','origin_type')
  		->where([['origin_type', 'like', '%' . $search . '%'],]) ->get();
  		return $origin_type_lists;
  	}


    //get searched OriginTypes for datatable plugin format
    private function datatable_search($data)
    {
      $start = $data['start'];
      $length = $data['length'];
      $draw = $data['draw'];
      $search = $data['search']['value'];
      $order = $data['order'][0];
      $order_column = $data['columns'][$order['column']]['data'];
      $order_type = $order['dir'];

      $origin_type_list = OriginType::select('*')
      ->where('origin_type'  , 'like', $search.'%' )
      ->orderBy($order_column, $order_type)
      ->offset($start)->limit($length)->get();

      $origin_type_count = OriginType::where('origin_type'  , 'like', $search.'%' )->count();

      return [
          "draw" => $draw,
          "recordsTotal" => $origin_type_count,
          "recordsFiltered" => $origin_type_count,
          "data" => $origin_type_list
      ];
    }


    private function handsontable_search($search){
      $list = OriginType::where('origin_type', 'like', $search.'%')
      ->where('status', '=', 1)->get()->pluck('origin_type');
      return $list;
    }

}
