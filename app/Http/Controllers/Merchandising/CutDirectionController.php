<?php
namespace App\Http\Controllers\Merchandising;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use App\Http\Controllers\Controller;
use App\Libraries\CapitalizeAllFields;

use App\Models\Merchandising\CutDirection;
use Exception;
use App\Libraries\AppAuthorize;


class CutDirectionController extends Controller{

  var $authorize = null;

  public function __construct()
  {
    //add functions names to 'except' paramert to skip authentication
    $this->middleware('jwt.verify', ['except' => ['index']]);
    $this->authorize = new AppAuthorize();
  }

  //get shipment term list
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
    else{
      $active = $request->active;
      $fields = $request->fields;
      return response([
        'data' => $this->list($active , $fields)
      ]);
    }
  }

  //create a shipment term
  public function store(Request $request)
  {
    if($this->authorize->hasPermission('CUT_DIRECTION_MANAGE'))//check permission
    {
      $cutDirection = new CutDirection();
      if($cutDirection->validate($request->all()))
      {
        $cutDirection->fill($request->all());
        $capitalizeAllFields=CapitalizeAllFields::setCapitalAll($cutDirection);
        $cutDirection->status = 1;
        $cutDirection->save();

        return response([ 'data' => [
          'message' => ' Cut Direction saved successfully',
          'cutDirection' => $cutDirection
          ]
        ], Response::HTTP_CREATED );
      }
      else
      {
        $errors = $cutDirection->errors();// failure, get errors
        $errors_str = $cutDirection->errors_tostring();
        return response(['errors' => ['validationErrors' => $errors, 'validationErrorsText' => $errors_str]], Response::HTTP_UNPROCESSABLE_ENTITY);
      }

    }
    else{
      return response($this->authorize->error_response(), 401);
    }
  }

  //get shipment term
  public function show($id)
  {
    if($this->authorize->hasPermission('CUT_DIRECTION_MANAGE'))//check permission
    {
      $cutDirection =CutDirection::find($id);
      if($cutDirection == null)
        throw new ModelNotFoundException("Requested Cut Direction not found", 1);
      else
        return response([ 'data' => $cutDirection ]);
    }
    else{
      return response($this->authorize->error_response(), 401);
    }
  }


  //update a shipment term
  public function update(Request $request, $id)
  {
    if($this->authorize->hasPermission('CUT_DIRECTION_MANAGE'))//check permission
    {
      $cutDirection = CutDirection::find($id);
      $cutDirection->fill($request->all());
      $capitalizeAllFields=CapitalizeAllFields::setCapitalAll($cutDirection);
      $cutDirection->save();

      return response([ 'data' => [
        'message' => 'Cut Direction updated successfully',
        'cutDirection' => $cutDirection
      ]]);
    }
    else{
      return response($this->authorize->error_response(), 401);
    }
  }

  //deactivate a ship term
  public function destroy($id)
  {
    if($this->authorize->hasPermission('CUT_DIRECTION_DELETE'))//check permission
    {
      $cutDirection = CutDirection::where('cut_dir_id', $id)->update(['status' => 0]);
      return response([
        'data' => [
          'message' => 'Cut Direction was deactivated successfully.',
          'cutDirection' => $cutDirection
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
      return response($this->validate_duplicate_code($request->cut_dir_id , $request->cut_dir_description));
    }
  }


  //check shipment cterm code code already exists
  private function validate_duplicate_code($id ,$description)
  {
     $cutDirection =  CutDirection::where('cut_dir_description','=',$description)->first();
                                    //>where('cd_acronyms','=',$code)->first();

    if( $cutDirection == null){
      return ['status' => 'success'];
    }
    else if( $cutDirection->cut_dir_id == $id){
      return ['status' => 'success'];
    }
    else {
      return ['status' => 'error','message' => 'Cut Direction already exists'];
    }
  }


  //get filtered fields only
  private function list($active = 0 , $fields = null)
  {
    $query = null;
    if($fields == null || $fields == '') {
      $query = CutDirection::select('*');
    }
    else{
      $fields = explode(',', $fields);
      $query =CutDirection::select($fields);
      if($active != null && $active != ''){
        $query->where([['status', '=', $active]]);
      }
    }
    return $query->get();
  }


  //search shipment terms for autocomplete
  private function autocomplete_search($search)
  {
    $cutDirection_lists = CutDirection::select('cut_dir_id','cut_dir_description','cd_acronyms')
    ->where([['cut_dir_description', 'like', '%' . $search . '%'],]) ->get();
    return $cutDirection_lists;
  }


  //get searched ship terms for datatable plugin format
  private function datatable_search($data)
  {
    if($this->authorize->hasPermission('CUT_DIRECTION_MANAGE'))//check permission
    {
      $start = $data['start'];
      $length = $data['length'];
      $draw = $data['draw'];
      $search = $data['search']['value'];
      $order = $data['order'][0];
      $order_column = $data['columns'][$order['column']]['data'];
      $order_type = $order['dir'];

      $cutDirection_list = CutDirection::select('*')
      ->where('cut_dir_description'  , 'like', $search.'%' )
      ->orWhere('cd_acronyms'  , 'like', $search.'%' )
      ->orderBy($order_column, $order_type)
      ->offset($start)->limit($length)->get();

        $cutDirection_count = CutDirection::where('cut_dir_description'  , 'like', $search.'%' )
      ->orWhere('cd_acronyms'  , 'like', $search.'%' )
      ->count();

      return [
          "draw" => $draw,
          "recordsTotal" => $cutDirection_count ,
          "recordsFiltered" => $cutDirection_count,
          "data" => $cutDirection_list
      ];
    }
    else{
      return response($this->authorize->error_response(), 401);
    }

  }







}
