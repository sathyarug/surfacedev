<?php

namespace App\Http\Controllers\Org\Location;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

use App\Models\Org\Location\Company;
use App\Models\Org\Location\Cluster;

use App\Models\Org\CompanySection;
use App\Models\Org\OrgDepartments;
use App\Currency;
use App\Country;
use App\Section;


use App\Http\Controllers\Controller;

class CompanyController extends Controller
{

	public function get_list(Request $request)
	{
		$data = $request->all();
		$start = $data['start'];
		$length = $data['length'];
		$draw = $data['draw'];
		$search = $data['search']['value'];
		$order = $data['order'][0];
		$order_column = $data['columns'][$order['column']]['data'];
		$order_type = $order['dir'];

		$company_list = Company::join('org_group', 'org_company.group_id', '=', 'org_group.group_id')
		->select('org_company.*', 'org_group.group_name')
		->where('company_code','like',$search.'%')
		->orWhere('company_name', 'like', $search.'%')
		->orWhere('group_name', 'like', $search.'%')
		->orderBy($order_column, $order_type)
		->offset($start)->limit($length)->get();

		$company_list_count = Company::join('org_group', 'org_company.group_id', '=', 'org_group.group_id')
		->select('org_company.*', 'org_group.group_name')
		->where('company_code','like',$search.'%')
		->orWhere('company_name', 'like', $search.'%')
		->orWhere('group_name', 'like', $search.'%')
		->count();

		echo json_encode(array(
				"draw" => $draw,
				"recordsTotal" => $company_list_count,
				"recordsFiltered" => $company_list_count,
				"data" => $company_list
		));

	}


	public function get_active_list(Request $request)
	{
		$search_c = $request->search;
		$loc_lists = Company::select('company_id','company_code','company_name')
		->where([['status', '=', '1'],['company_name', 'like', '%' . $search_c . '%'],]) ->get();
		return response()->json($loc_lists);
	}


	public function load_section_list(Request $request)
	{
		$search_c = $request->search;
		$sec_lists = Section::select('section_id','section_code','section_name')
		->where([['status', '=', '1'],['section_name', 'like', '%' . $search_c . '%'],]) ->get();
		return response()->json(['items'=>$sec_lists]);
	}


	public function load_depat_list(Request $request)
	{
		$search_c = $request->search;
		$dep_lists = OrgDepartments::select('dep_id','dep_code','dep_name')
		->where([['dep_name', 'like', '%' . $search_c . '%'],]) ->get();
		return response()->json(['items'=>$dep_lists]);
	}


	public function check_code(Request $request)
	{
		$company = Company::where('company_code','=',$request->company_code)->first();
		if($company == null){
			echo json_encode(array('status' => 'success'));
		}
		else if($company->company_id == $request->company_id){
			echo json_encode(array('status' => 'success'));
		}
		else {
			echo json_encode(array('status' => 'error','message' => 'Company code already exists'));
		}
	}


	public function save(Request $request)
	{
		$company = new Company();
		if ($company->validate($request->all()))
		{
			if($request->company_id > 0){
				$company = Company::find($request->company_id);
			}
			$company->fill($request->all());
			$company->status = 1;
			$company->created_by = 1;
			$result = $company->saveOrFail();
			$insertedId = $company->company_id;

			DB::table('org_company_departments')->where('company_id', '=', $insertedId)->delete();

			$departments = $request->get('departments');
			$save_departments = array();
			if($departments != '') {
	  		foreach($departments as $dep)		{
					array_push($save_departments,OrgDepartments::find($dep['dep_id']));
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

			echo json_encode(array('status' => 'success' , 'message' => 'Location details saved successfully.') );
		}
		else
		{
      // failure, get errors
			$errors = $company->errors_tostring();
			echo json_encode(array('status' => 'error' , 'message' => $errors));
		}


	}



	public function get_company(Request $request)
	{
		$company = Company::with(['departments','sections','currency','country'])->find($request->company_id);
		echo json_encode($company);
	/*	$loc_id = $request->loc_id;
		$cluster = Company::join('org_group', 'org_company.group_id', '=', 'org_group.group_id')
		->join('org_country', 'org_company.country_code', '=', 'org_country.country_id')
		->join('fin_currency', 'org_company.default_currency', '=', 'fin_currency.currency_id')
		->select('org_company.*', 'org_group.group_code', 'org_group.group_name', 'org_country.country_description',
				'fin_currency.currency_id' ,'fin_currency.currency_description')
		->where('org_company.company_id', '=', $loc_id)->get();*/


	/*	$load_mul = [];OrgCompanySection::join('org_section', 'org_company_section.section_id', '=', 'org_section.section_id')
		 	->select('org_company_section.*', 'org_section.section_name')
		 	->where([
		 			['org_company_section.status', '=', '1'],
		 			['org_company_section.company_id', '=', $loc_id]
					])->get();*/

		/*$load_dep = [];OrgCompanyDepartments::join('org_departments', 'org_company_departments.com_dep_name', '=', 'org_departments.dep_id')
		 	->select('org_company_departments.*', 'org_departments.dep_name')
		 	->where([
		 			['org_company_departments.status', '=', '1'],
		 			['org_company_departments.company_id', '=', $loc_id]
					])->get();*/

		//echo json_encode(array('com_hed' => $cluster,'multi' => $load_mul,'dep' => $load_dep));
	}


	public function change_status(Request $request)
	{
		$loc_id = $request->loc_id;

		$cluster = Company::where('company_id', $loc_id)->update(['status' => 0]);
		echo json_encode(array('delete'));
	}


   public function test(){
		 $company = Company::with(['departments','currency','country'])->find(14);
		 echo json_encode($company);
		 /*foreach ($company->departments as $role) {
     	echo json_encode($role);
		}*/
	 }

}
