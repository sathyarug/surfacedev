<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Main_Location;
use App\Http\Controllers\Controller;

class MainLocationController extends Controller
{

	public function loaddata()
   		{

       	$location_list = Main_Location::join('org_group', 'org_company.group_id', '=', 'org_group.group_id')
            				->select('org_company.*', 'org_group.group_code')
            				->get();
        echo json_encode($location_list);

   		}


}