<?php

namespace App\Http\Controllers\Org;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Database\Eloquent\ModelNotFoundException;

use App\Http\Controllers\Controller;
use App\Models\Org\Section;
use App\Models\Org\CompanySection;
use App\Libraries\AppAuthorize;
use App\Libraries\CapitalizeAllFields;
class SectionController extends Controller
{
    var $authorize = null;

    public function __construct()
    {
      //add functions names to 'except' paramert to skip authentication
      $this->middleware('jwt.verify', ['except' => ['index']]);
      $this->authorize = new AppAuthorize();
    }

    //get Section list
    public function index(Request $request)
    {
      $type = $request->type;
      if($type == 'datatable')   {
        $data = $request->all();
        $this->datatable_search($data);
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


    //create a Section
    public function store(Request $request)
    {
      if($this->authorize->hasPermission('SECTION_MANAGE'))//check permission
      {
          $section = new Section();
          if($section->validate($request->all()))
          {
            //$request->section_code=strtoupper($request->section_code);
            //echo($request->section_code);
            $section->fill($request->all());
            //$capitalizeAllFields=CapitalizeAllFields::setCapitalAll($section);
            $section->status = 1;
            $section->save();

            return response([ 'data' => [
              'message' => 'Section saved successfully',
              'section' => $section
              ]
            ], Response::HTTP_CREATED );
          }
          else
          {
            $errors = $section->errors();// failure, get errors
            $errors_str = $section->errors_tostring();
            return response(['errors' => ['validationErrors' => $errors, 'validationErrorsText' => $errors_str]], Response::HTTP_UNPROCESSABLE_ENTITY);
          }
      }
      else {
        return response($this->authorize->error_response(), 401);
      }
    }


    //get a Section
    public function show($id)
    {
      if($this->authorize->hasPermission('SECTION_MANAGE'))//check permission
      {
        $section = Section::find($id);
        if($section == null)
          throw new ModelNotFoundException("Requested section not found", 1);
        else
          return response([ 'data' => $section ]);
      }
      else {
        return response($this->authorize->error_response(), 401);
      }
    }


    //update a Section
    public function update(Request $request, $id)
    {
      if($this->authorize->hasPermission('SECTION_MANAGE'))//check permission
      {
        $companySection=CompanySection::where([['section_id','=',$id]])->first();

        if($companySection!=null){
          return response([
            'data' => [
              'message' => 'Section is Already in Use',
              'status'=>'0'
            ]
          ]);
        }

        if($companySection==null){
          $section = Section::find($id);
        if($section->validate($request->all()))
        {
          $section->fill($request->except('section_code'));
          //$capitalizeAllFields=CapitalizeAllFields::setCapitalAll($section);
          $section->save();

          return response([ 'data' => [
            'message' => 'Section updated successfully',
            'section' => $section,
            'status'=>'1'
          ]]);
        }
      }
        else
        {
          $errors = $section->errors();// failure, get errors
          $errors_str = $section->errors_tostring();
          return response(['errors' => ['validationErrors' => $errors, 'validationErrorsText' => $errors_str]], Response::HTTP_UNPROCESSABLE_ENTITY);
        }
      }
      else {
        return response($this->authorize->error_response(), 401);
      }
    }


    //deactivate a Section
    public function destroy($id)
    {
      if($this->authorize->hasPermission('SECTION_DELETE'))//check permission
      {
        $companySection=CompanySection::where([['section_id','=',$id]])->first();

        if($companySection!=null){
          return response([
            'data' => [
              'message' => 'Section is Already in Use',
              'status'=>'0'
            ]
          ]);
        }
            else if($companySection==null){
              $section = Section::where('section_id', $id)->update(['status' => 0]);
        return response([
          'data' => [
            'message' => 'Section deactivated successfully.',
            'section' => $section,
            'status'=>'1'
          ]
        ]);
      }
    }
      else {
        return response($this->authorize->error_response(), 401);
      }
    }


    //validate anything based on requirements
    public function validate_data(Request $request){
      $for = $request->for;
      if($for == 'duplicate')
      {
        return response($this->validate_duplicate_code($request->section_id , $request->section_code));
      }
    }


    //check Section code already exists
    private function validate_duplicate_code($id , $code)
    {
      $section = Section::where('section_code','=',$code)->first();
      if($section == null){
        return ['status' => 'success'];
      }
      else if($section->section_id == $id){
        return ['status' => 'success'];
      }
      else {
        return ['status' => 'error','message' => 'Section code already exists'];
      }
    }


    //get filtered fields only
    private function list($active = 0 , $fields = null)
    {
        $fields = "section_name,section_id";
        $query = null;
        if($fields == null || $fields == '') {
          $query = Section::select('*');
        }
        else{
          $fields = explode(',', $fields);
          $query = Section::select($fields);
          if($active != null && $active != ''){
            $query->where([['status', '=', $active]]);
          }
        }
        return $query->get();
    }

    //search Section for autocomplete
    private function autocomplete_search($search)
  	{
  		$section_lists = Section::select('section_id','section_name')
  		->where([['section_name', 'like', '%' . $search . '%'],['status','<>','0']]) ->get();
  		return $section_lists;
  	}


    //get searched Sections for datatable plugin format
    private function datatable_search($data)
    {
      if($this->authorize->hasPermission('SECTION_MANAGE'))//check permission
      {
        $start = $data['start'];
        $length = $data['length'];
        $draw = $data['draw'];
        $search = $data['search']['value'];
        $order = $data['order'][0];
        $order_column = $data['columns'][$order['column']]['data'];
        $order_type = $order['dir'];

        $section_list = Section::select('*')
        ->where('section_code'  , 'like', $search.'%' )
        ->orWhere('section_name'  , 'like', $search.'%' )
        ->orderBy($order_column, $order_type)
        ->offset($start)->limit($length)->get();

        $section_count = Section::where('section_code'  , 'like', $search.'%' )
        ->orWhere('section_name'  , 'like', $search.'%' )
        ->count();

        echo json_encode([
            "draw" => $draw,
            "recordsTotal" => $section_count,
            "recordsFiltered" => $section_count,
            "data" => $section_list
        ]);
      }
      else{
        return response($this->authorize->error_response(), 401);
      }
  }

}
