<?php

namespace App\Http\Controllers\Org;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Database\Eloquent\ModelNotFoundException;

use App\Http\Controllers\Controller;
use App\Models\Org\GarmentOptions;
use Exception;
use App\Libraries\AppAuthorize;
use App\Libraries\CapitalizeAllFields;

class GarmentOptionsController extends Controller
{
    var $authorize = null;

    public function __construct()
    {
      //add functions names to 'except' paramert to skip authentication
      $this->middleware('jwt.verify', ['except' => ['index']]);
      $this->authorize = new AppAuthorize();
    }

    //get GarmentOptions list
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
      else if($type == 'handsontable')    {
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


    //create a GarmentOptions
    public function store(Request $request)
    {
      if($this->authorize->hasPermission('GARMT_OPTION_MANAGE'))//check permission
      {
        $garmentoptions = new GarmentOptions();
        if($garmentoptions->validate($request->all()))
        {
          $garmentoptions->fill($request->all());
          $garmentoptions->status = 1;
          $capitalizeAllFields = CapitalizeAllFields::setCapitalAll($garmentoptions);
          $garmentoptions->save();

          return response([ 'data' => [
            'message' => 'Garment Option saved successfully',
            'garmentoptions' => $garmentoptions
            ]
          ], Response::HTTP_CREATED );
        }
        else
        {
          $errors = $garmentoptions->errors();// failure, get errors
          $errors_str = $garmentoptions->errors_tostring();
          return response(['errors' => ['validationErrors' => $errors, 'validationErrorsText' => $errors_str]], Response::HTTP_UNPROCESSABLE_ENTITY);
        }
      }
      else{
        return response($this->authorize->error_response(), 401);
      }
    }


    //get a GarmentOptions
    public function show($id)
    {
      if($this->authorize->hasPermission('GARMT_OPTION_MANAGE'))//check permission
      {
        $garmentoptions = GarmentOptions::find($id);
        if($garmentoptions == null)
          throw new ModelNotFoundException("Requested garment option not found", 1);
        else
          return response([ 'data' => $garmentoptions ]);
      }
      else{
        return response($this->authorize->error_response(), 401);
      }
    }


    //update a GarmentOptions
    public function update(Request $request, $id)
    {
      if($this->authorize->hasPermission('GARMT_OPTION_MANAGE'))//check permission
      {
        $garmentoptions = GarmentOptions::find($id);
        if($garmentoptions->validate($request->all()))
        {
          $garmentoptions->fill($request->all());
          $capitalizeAllFields=CapitalizeAllFields::setCapitalAll($garmentoptions);
          $garmentoptions->save();

          return response([ 'data' => [
            'message' => 'Garment option updated successfully',
            'garmentoptions' => $garmentoptions
          ]]);
        }
        else
        {
          $errors = $garmentoptions->errors();// failure, get errors
          return response(['errors' => ['validationErrors' => $errors]], Response::HTTP_UNPROCESSABLE_ENTITY);
        }
      }
      else{
        return response($this->authorize->error_response(), 401);
      }
    }


    //deactivate a GarmentOptions
    public function destroy($id)
    {
      if($this->authorize->hasPermission('GARMT_OPTION_DELETE'))//check permission
      {
        $garmentoptions = GarmentOptions::where('garment_options_id', $id)->update(['status' => 0]);
        return response([
          'data' => [
            'message' => 'Garment Option was deactivated successfully.',
            'garnentoptions' => $garmentoptions
          ]
        ] , Response::HTTP_NO_CONTENT);
      }
      else{
        return response($this->authorize->error_response(), 401);
      }
    }


    //validate anything based on requirements
    public function validate_data(Request $request){
      $for = $request->for;
      if($for == 'duplicate')
      {
        return response($this->validate_duplicate_code($request->garment_options_id , $request->garment_options_description));
      }
    }


    //check GarmentOptions code already exists
    private function validate_duplicate_code($id , $code)
    {
      $garmentoptions = GarmentOptions::where('garment_options_description','=',$code)->first();
      if($garmentoptions == null){
        return ['status' => 'success'];
      }
      else if($garmentoptions->garment_options_id == $id){
        return ['status' => 'success'];
      }
      else {
        return ['status' => 'error','message' => 'Garment option code already exists'];
      }
    }


    //get filtered fields only
    private function list($active = 0 , $fields = null)
    {
      $query = null;
      if($fields == null || $fields == '') {
        $query = GarmentOptions::select('*');
      }
      else{
        $fields = explode(',', $fields);
        $query = GarmentOptions::select($fields);
        if($active != null && $active != ''){
          $query->where([['status', '=', $active]]);
        }
      }
      return $query->get();
    }

    //search GarmentOptions for autocomplete
    private function autocomplete_search($search)
  	{
  		$garmentoptions_lists = GarmentOptions::select('garment_options_id','garment_options_description')
  		->where([['garment_options_description', 'like', '%' . $search . '%']]) ->get();
  		return $garmentoptions_lists;
  	}


    //get searched GarmentOptions for datatable plugin format
    private function datatable_search($data)
    {
      if($this->authorize->hasPermission('GARMT_OPTION_MANAGE'))//check permission
      {
        $start = $data['start'];
        $length = $data['length'];
        $draw = $data['draw'];
        $search = $data['search']['value'];
        $order = $data['order'][0];
        $order_column = $data['columns'][$order['column']]['data'];
        $order_type = $order['dir'];

        $garmentoptions_list = GarmentOptions::select('*')
        ->where('garment_options_description'  , 'like', $search.'%' )
        ->orderBy($order_column, $order_type)
        ->offset($start)->limit($length)->get();

        $garmentoptions_count = GarmentOptions::where('garment_options_description'  , 'like', $search.'%' )
        ->count();

        return [
            "draw" => $draw,
            "recordsTotal" => $garmentoptions_count,
            "recordsFiltered" => $garmentoptions_count,
            "data" => $garmentoptions_list
        ];
      }
      else{
        return response($this->authorize->error_response(), 401);
      }
    }

    private function handsontable_search($search){
      $list = GarmentOptions::where('garment_options_description'  , 'like', $search.'%' )
      ->where('status', '=', 1)->get()->pluck('garment_options_description');
      return $list;
    }

}
