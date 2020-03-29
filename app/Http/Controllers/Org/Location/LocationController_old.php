<?php

namespace App\Http\Controllers\Org\Location;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

use App\Models\Org\Location\Location;
use App\Models\Finance\Accounting\CostCenter;
use App\Http\Controllers\Controller;

class LocationController extends Controller
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

		$location_list = Location::join('org_company', 'org_location.company_id', '=', 'org_company.company_id')
		->select('org_location.*', 'org_company.company_name')
		->where('loc_code','like',$search.'%')
		->orWhere('loc_name', 'like', $search.'%')
		->orWhere('company_name', 'like', $search.'%')
		->orderBy($order_column, $order_type)
		->offset($start)->limit($length)->get();

		$location_count = Location::join('org_company', 'org_location.company_id', '=', 'org_company.company_id')
		->select('org_location.*', 'org_company.company_name')
		->where('loc_code','like',$search.'%')
		->orWhere('loc_name', 'like', $search.'%')
		->orWhere('company_name', 'like', $search.'%')
		->count();

		echo json_encode(array(
				"draw" => $draw,
				"recordsTotal" => $location_count,
				"recordsFiltered" => $location_count,
				"data" => $location_list
		));

	}


	public function check_code(Request $request)
	{
		$location = Location::where('loc_code','=',$request->loc_code)->first();
		if($location == null){
			echo json_encode(array('status' => 'success'));
		}
		else if($location->loc_id == $request->loc_id){
			echo json_encode(array('status' => 'success'));
		}
		else {
			echo json_encode(array('status' => 'error','message' => 'Location code already exists'));
		}
	}


	public function save(Request $request)
	{
		$location = new Location();
		if ($location->validate($request->all()))
		{
				if($request->loc_id > 0){
					$location = Location::find($request->loc_id);
				}
				$location->fill($request->all());
				$location->status = 1;
				$location->created_by = 1;
				$result = $location->saveOrFail();
				$insertedId = $location->loc_id;

				DB::table('org_location_cost_centers')->where('loc_id', '=', $insertedId)->delete();
				$cost_centers = $request->get('cost_centers');
				$save_cost_centers = array();
				if($cost_centers != '') {
		  		foreach($cost_centers as $cost_center)		{
						array_push($save_cost_centers,CostCenter::find($cost_center['cost_center_id']));
					}
				}
				$location->costCenters()->saveMany($save_cost_centers);
				echo json_encode(array('status' => 'success' , 'message' => 'Location details saved successfully.') );
			}
			else
			{
        // failure, get errors
				$errors = $location->errors_tostring();
				echo json_encode(array('status' => 'error' , 'message' => $errors));
			}
		}


		public function get_location(Request $request)
		{
			$location = Location::with(['currency','country','costCenters'])->find($request->loc_id);
			echo json_encode($location);
		}


		public function change_status(Request $request)
		{
			$loc_id = $request->loc_id;
			Location::where('loc_id', $loc_id)->update(['status' => 0]);
			echo json_encode(array(
				'status' => 'success',
				'message' => 'Location deactivated successfully'
			));
		}

	}
