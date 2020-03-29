<?php

namespace App\Http\Controllers\Org\Location;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\DB;

use App\Http\Controllers\Controller;
use App\Models\Org\Location\Company;
use App\Models\Org\Location\Location;
use App\Models\Org\Department;
use App\Models\Org\Section;
use App\Libraries\AppAuthorize;
use App\Libraries\CapitalizeAllFields;

class CompanyController extends Controller
{
    var $authorize = null;

    public function __construct()
    {
      //add functions names to 'except' paramert to skip authentication
      $this->middleware('jwt.verify', ['except' => ['index']]);
      $this->authorize = new AppAuthorize();
    }

    //get Company list
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


    //create a Company
    public function store(Request $request)
    {
      if($this->authorize->hasPermission('COMPANY_MANAGE'))//check permission
      {
        $company = new Company();
        if($company->validate($request->all()))
        {
          $company->fill($request->all());
          //$capitalizeAllFields=CapitalizeAllFields::setCapitalAll($company);
          $company->company_email=$request->company_email;
          $company->company_web=$request->company_web;
          $company->status = 1;
          $company->created_by = 1;
          $result = $company->saveOrFail();
          $insertedId = $company->company_id;

          DB::table('org_company_departments')->where('company_id', '=', $insertedId)->delete();
    			$departments = $request->get('departments');
    			$save_departments = array();
    			if($departments != '') {
    	  		foreach($departments as $dep)		{
    					array_push($save_departments,Department::find($dep['dep_id']));
    				}
    			}
    			$company->departments()->saveMany($save_departments);

          DB::table('org_company_sections')->where('company_id', '=', $insertedId)->delete();
    			$sections = $request->get('sections');
    			$save_sections = array();
    			if($sections != '') {
    	  		foreach($sections as $sec)		{
    					array_push($save_sections,Section::find($sec['section_id']));
    				}
    			}
    			$company->sections()->saveMany($save_sections);

          $processes = DB::table('app_process')->get();
          $insert_procees_list = [];
          foreach($processes as $process){
            array_push($insert_procees_list, [
              'unque_id' => $process->initial_id,
              'process_type' => $process->process_name,
              'company' => $insertedId
            ]);
          }
          //echo json_encode($insert_procees_list);
          DB::table('unique_id_generator')->insert($insert_procees_list);

          return response([ 'data' => [
            'message' => 'Company saved successfully',
            'company' => $company
            ]
          ], Response::HTTP_CREATED );
        }
        else
        {
          $errors = $company->errors();// failure, get errors
          $errors_str = $company->errors_tostring();
          return response(['errors' => ['validationErrors' => $errors, 'validationErrorsText' => $errors_str]], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

      }
      else{
        return response($this->authorize->error_response(), 401);
      }
    }


    //get a Company
    public function show($id)
    {
      if($this->authorize->hasPermission('COMPANY_MANAGE'))//check permission
      {
        $company = Company::with(['currency','country','sections','departments'])->find($id);
        if($company == null)
          throw new ModelNotFoundException("Requested company not found", 1);
        else
          return response([ 'data' => $company ]);
      }
      else{
        return response($this->authorize->error_response(), 401);
      }
    }


    //update a Company
    public function update(Request $request, $id)
    {
      if($this->authorize->hasPermission('COMPANY_MANAGE'))//check permission
      {
        $check_location = Location::where([['status', '=', '1'],['company_id','=',$id]])->first();
        if($check_location != null)
        {
          return response([
            'data'=>[
              'status'=>'0',
            ]
          ]);
        }else{


        $company = Company::find($id);
        if($company->validate($request->all()))
        {
          $company->fill($request->except('company_code'));
          //$capitalizeAllFields=CapitalizeAllFields::setCapitalAll($company);
          $company->company_email=$request->company_email;
          $company->company_web=$request->company_web;
          $company->save();

          DB::table('org_company_departments')->where('company_id', '=', $id)->delete();
    			$departments = $request->get('departments');
    			$save_departments = array();
    			if($departments != '') {
    	  		foreach($departments as $dep)		{
    					array_push($save_departments,Department::find($dep['dep_id']));
    				}
    			}
    			$company->departments()->saveMany($save_departments);

          DB::table('org_company_sections')->where('company_id', '=', $id)->delete();
    			$sections = $request->get('sections');
    			$save_sections = array();
    			if($sections != '') {
    	  		foreach($sections as $sec)		{
    					array_push($save_sections,Section::find($sec['section_id']));
    				}
    			}
    			$company->sections()->saveMany($save_sections);

          return response([ 'data' => [
            'message' => 'Company updated successfully',
            'company' => $company
          ]]);
        }
        else
        {
          $errors = $company->errors();// failure, get errors
          $errors_str = $company->errors_tostring();
          return response(['errors' => ['validationErrors' => $errors, 'validationErrorsText' => $errors_str]], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

      }

      }
      else{
        return response($this->authorize->error_response(), 401);
      }
    }


    //deactivate a Company
    public function destroy($id)
    {
      if($this->authorize->hasPermission('COMPANY_DELETE'))//check permission
      {

        $check_location = Location::where([['status', '=', '1'],['company_id','=',$id]])->first();

        if($check_location != null)
        {
          return response([
            'data'=>[
              'status'=>'0',
            ]
          ]);
        }else{

        $company = Company::where('company_id', $id)->update(['status' => 0]);
        return response([
          'data' => [
            'message' => 'Company deactivated successfully.',
            'company' => $company
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
        return response($this->validate_duplicate_code($request->company_id , $request->company_code));
      }
    }


    //check Company code already exists
    private function validate_duplicate_code($id , $code)
    {
      $company = Company::where('company_code','=',$code)->first();
      if($company == null){
        return ['status' => 'success'];
      }
      else if($company->company_id == $id){
        return ['status' => 'success'];
      }
      else {
        return ['status' => 'error','message' => 'Company code already exists'];
      }
    }


    //get filtered fields only
    private function list($active = 0 , $fields = null)
    {
      $query = null;
      if($fields == null || $fields == '') {
        $query = Company::select('*');
      }
      else{
        $fields = explode(',', $fields);
        $query = Company::select($fields);
        if($active != null && $active != ''){
          $query->where([['status', '=', $active]]);
        }
      }
      return $query->get();
    }

    //search Company for autocomplete
    private function autocomplete_search($search)
  	{
  		$company_lists = Company::select('company_id','company_name')
  		->where([['company_name', 'like', '%' . $search . '%'],]) ->get();
  		return $company_lists;
  	}


    //get searched Companys for datatable plugin format
    private function datatable_search($data)
    {
      if($this->authorize->hasPermission('COMPANY_MANAGE'))//check permission
      {
        $start = $data['start'];
        $length = $data['length'];
        $draw = $data['draw'];
        $search = $data['search']['value'];
        $order = $data['order'][0];
        $order_column = $data['columns'][$order['column']]['data'];
        $order_type = $order['dir'];

        $company_list = Company::join('org_group', 'org_company.group_id', '=', 'org_group.group_id')
        ->join('org_country', 'org_company.country_code', '=', 'org_country.country_id')
        ->join('fin_currency', 'org_company.default_currency', '=', 'fin_currency.currency_id')
    		->select('org_company.*', 'org_group.group_name','org_country.country_description','fin_currency.currency_code')
    		->where('company_code','like',$search.'%')
    		->orWhere('company_name', 'like', $search.'%')
    		->orWhere('group_name', 'like', $search.'%')
    		->orderBy($order_column, $order_type)
    		->offset($start)->limit($length)->get();

    		$company_count = Company::join('org_group', 'org_company.group_id', '=', 'org_group.group_id')
        ->join('org_country', 'org_company.country_code', '=', 'org_country.country_id')
        ->join('fin_currency', 'org_company.default_currency', '=', 'fin_currency.currency_id')
    		->select('org_company.*', 'org_group.group_name')
    		->where('company_code','like',$search.'%')
    		->orWhere('company_name', 'like', $search.'%')
    		->orWhere('group_name', 'like', $search.'%')
    		->count();

        return [
            "draw" => $draw,
            "recordsTotal" => $company_count,
            "recordsFiltered" => $company_count,
            "data" => $company_list
        ];
      }
      else{
        return response($this->authorize->error_response(), 401);
      }
    }

}
