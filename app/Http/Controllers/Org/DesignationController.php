<?php

namespace App\Http\Controllers\Org;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Database\Eloquent\ModelNotFoundException;

use App\Http\Controllers\Controller;
use App\Models\Org\Designation;
use Exception;
use App\Libraries\AppAuthorize;
use Illuminate\Support\Facades\DB;
use App\Libraries\CapitalizeAllFields;

class DesignationController extends Controller
{
    var $authorize = null;

    public function __construct()
    {
      //add functions names to 'except' paramert to skip authentication
      $this->middleware('jwt.verify', ['except' => ['index']]);
      $this->authorize = new AppAuthorize();
    }

    //get Department list
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


    //create a Department
    public function store(Request $request)
    {
      if($this->authorize->hasPermission('DESIGNATION_MANAGE'))//check permission
      {
        $designation = new Designation();
        if($designation->validate($request->all()))
        {
          $designation->fill($request->all());
          $designation->status = 1;
          //$capitalizeAllFields=CapitalizeAllFields::setCapitalAll($designation);
          $designation->save();

          return response([ 'data' => [
            'message' => 'Designation saved successfully',
            'designation' => $designation,
            'status'=>1
            ]
          ], Response::HTTP_CREATED );
        }
        else
        {
          $errors = $designation->errors();// failure, get errors
          $errors_str = $designation->errors_tostring();
          return response(['errors' => ['validationErrors' => $errors, 'validationErrorsText' => $errors_str]], Response::HTTP_UNPROCESSABLE_ENTITY);
        }
      }
      else{
        return response($this->authorize->error_response(), 401);
      }
    }


    //get a Department
    public function show($id)
    {
      if($this->authorize->hasPermission('DESIGNATION_MANAGE'))//check permission
      {
        $designation = Designation::find($id);
        if($designation == null)
          throw new ModelNotFoundException("Requested Designation not found", 1);
        else
          return response([ 'data' => $designation ]);
      }
      else{
        return response($this->authorize->error_response(), 401);
      }
    }


    //update a Designation
    public function update(Request $request, $id)
    {
      if($this->authorize->hasPermission('DESIGNATION_MANAGE'))//check permission
      {
        $designation = Designation::find($id);
        if($designation->validate($request->all()))
        {
          $is_exsits=DB::table('usr_profile')->where('desig_id',$id)->exists();
          if($is_exsits==true){
            return response([ 'data' => [
              'message' => 'Designation Already In use',
              'status'=>0
            ]]);
          }
          else{
          $designation->fill($request->except('des_code'));
          //$capitalizeAllFields=CapitalizeAllFields::setCapitalAll($designation);
          $designation->save();

          return response([ 'data' => [
            'message' => 'Designation updated successfully',
            'designation' => $designation,
            'status'=>1
          ]]);
        }
        }
        else
        {
          $errors = $designation->errors();// failure, get errors
          $errors_str = $designation->errors_tostring();
          return response(['errors' => ['validationErrors' => $errors, 'validationErrorsText' => $errors_str]], Response::HTTP_UNPROCESSABLE_ENTITY);
        }
      }
      else{
        return response($this->authorize->error_response(), 401);
      }
    }


    //deactivate a Designation
    public function destroy($id)
    {
      if($this->authorize->hasPermission('DESIGNATION_DELETE'))//check permission
      {
        $is_exsits=DB::table('usr_profile')->where('desig_id',$id)->exists();
        if($is_exsits==true){
          return response([
            'data' => [
              'message' => 'Designation Already in Use.',
              'status' =>0
            ]
          ]);
        }
        else{
        $designation = Designation::where('des_id', $id)->update(['status' => 0]);
        return response([
          'data' => [
            'message' => 'Designation  deactivated successfully.',
            'department' => $designation,
            'status'=>1
          ]
        ]);
      }
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
        return response($this->validate_duplicate_code($request->des_id , $request->des_code));
      }
    }


    //check Designation code already exists
    private function validate_duplicate_code($id , $code)
    {
      $designation = Designation::where('des_code','=',$code)->first();
      if($designation == null){
        return ['status' => 'success'];
      }
      else if($designation->des_id == $id){
        return ['status' => 'success'];
      }
      else {
        return ['status' => 'error','message' => 'Designation code already exists'];
      }
    }


    //get filtered fields only
    private function list($active = 0 , $fields = null)
    {
      $query = null;
      if($fields == null || $fields == '') {
        $query = Designation::select('*');
      }
      else{
        $fields = explode(',', $fields);
        $query = Designation::select($fields);
        if($active != null && $active != ''){
          $query->where([['status', '=', $active]]);
        }
      }
      return $query->get();
    }

    //search Designation for autocomplete
    private function autocomplete_search($search)
  	{
  		$designation_lists = Designation::select('des_id','des_name')
  		->where([['des_name', 'like', '%' . $search . '%'],])
      ->where('status','=',1)->get();
  		return $designation_lists;
  	}


    //get searched Designations for datatable plugin format
    private function datatable_search($data)
    {
      if($this->authorize->hasPermission('DESIGNATION_MANAGE'))//check permission
      {
        $start = $data['start'];
        $length = $data['length'];
        $draw = $data['draw'];
        $search = $data['search']['value'];
        $order = $data['order'][0];
        $order_column = $data['columns'][$order['column']]['data'];
        $order_type = $order['dir'];

        $des_list = Designation::select('*')
        ->where('des_code'  , 'like', $search.'%' )
        ->orWhere('des_name','like',$search.'%')
        ->orderBy($order_column, $order_type)
        ->offset($start)->limit($length)->get();

        $des_count = Designation::where('des_code'  , 'like', $search.'%' )
        ->orWhere('des_name','like',$search.'%')
        ->count();

        return [
            "draw" => $draw,
            "recordsTotal" => $des_count,
            "recordsFiltered" => $des_count,
            "data" => $des_list
        ];
      }
      else{
        return response($this->authorize->error_response(), 401);
      }
    }

}
