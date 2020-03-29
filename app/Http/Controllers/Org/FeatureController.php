<?php

namespace App\Http\Controllers\Org;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Database\Eloquent\ModelNotFoundException;

use App\Http\Controllers\Controller;
use App\Models\Org\Feature;
use Illuminate\Support\Facades\DB;
use App\Libraries\CapitalizeAllFields;


use Exception;

class FeatureController extends Controller
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


    //create a Feature
    public function store(Request $request)
    {
      $feature = new Feature();
      if($feature->validate($request->all()))
      {
        $feature->fill($request->all());
        $feature->status = 1;
        $capitalizeAllFields=CapitalizeAllFields::setCapitalAll($feature);
        $feature->save();

        return response([ 'data' => [
          'message' => 'Feature was saved successfully',
          'feature' => $feature
          ]
        ], Response::HTTP_CREATED );
      }
      else
      {
          $errors = $feature->errors();// failure, get errors
          return response(['errors' => ['validationErrors' => $errors]], Response::HTTP_UNPROCESSABLE_ENTITY);
      }
    }


    //get a Feature
    public function show($id)
    {
      $feature = Feature::find($id);
      if($feature == null)
        throw new ModelNotFoundException("Requested feature not found", 1);
      else
        return response([ 'data' => $feature ]);
    }


    //update a Feature
    public function update(Request $request, $id)
    {
        $feature = Feature::find($id);
      if($feature->validate($request->all()))
      {
        $is_exsist=DB::table('style_creation')->where('product_feature_id',$id)->exists();
        if($is_exsist){
          return response([
            'data' => [
              'status' => 'error',
              'message' => 'Cannot deactivate Feature. Already use in Style creation.'
            ]
          ] , 200);
        }
        else{

        $feature->fill($request->all());
        $capitalizeAllFields=CapitalizeAllFields::setCapitalAll($feature);
        $feature->save();

        return response([ 'data' => [
          'message' => 'Feature was updated successfully',
          'feature' => $feature
        ]]);
      }
      }
      else
      {
        $errors = $feature->errors();// failure, get errors
        return response(['errors' => ['validationErrors' => $errors]], Response::HTTP_UNPROCESSABLE_ENTITY);
      }
    }


    //deactivate a Feature
    public function destroy($id)
    {
      //$styleCreation=StyleCreation::where(['','=',''])

      $feature = Feature::where('product_feature_id', $id)->update(['status' => 0]);
      return response([
        'data' => [
          'message' => 'Feature was deactivated successfully.',
          'feature' => $feature
        ]
      ] , Response::HTTP_NO_CONTENT);
    }


    //validate anything based on requirements
    public function validate_data(Request $request){
      $for = $request->for;
      if($for == 'duplicate')
      {
        return response($this->validate_duplicate_code($request->product_feature_id , $request->product_feature_description));
      }
    }


    //check Feature code already exists
    private function validate_duplicate_code($id , $code)
    {
      $feature = Feature::where('product_feature_description','=',$code)->first();
      if($feature == null){
        return ['status' => 'success'];
      }
      else if($feature->product_feature_id == $id){
        return ['status' => 'success'];
      }
      else {
        return ['status' => 'error','message' => 'Feature code already exists'];
      }
    }


    //get filtered fields only
    private function list($active = 0 , $fields = null)
    {
      $query = null;
      if($fields == null || $fields == '') {
        $query = Feature::select('*');
      }
      else{
        $fields = explode(',', $fields);
        $query = Feature::select($fields);
        if($active != null && $active != ''){
          $query->where([['status', '=', $active]]);
        }
      }
      return $query->get();
    }

    //search Size for autocomplete
    private function autocomplete_search($search)
  	{
  		$feature_lists = Feature::select('product_feature_id','product_feature_description')
  		->where([['product_feature_description', 'like', '%' . $search . '%']]) ->get();
  		return $feature_lists;
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

      $feature_list = Feature::select('*')
      ->where('product_feature_description'  , 'like', $search.'%' )
      ->orderBy($order_column, $order_type)
      ->offset($start)->limit($length)->get();

      $feature_count = Feature::where('product_feature_description'  , 'like', $search.'%' )
      ->count();

      return [
          "draw" => $draw,
          "recordsTotal" => $feature_count,
          "recordsFiltered" => $feature_count,
          "data" => $feature_list
      ];
    }

}
