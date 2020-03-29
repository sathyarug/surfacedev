<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Main_Cluster;
use App\Http\Controllers\Controller;

class MainClusterController extends Controller
{

	public function loaddata()
	{

		$cluster_list = Main_Cluster::join('org_source', 'org_group.source_id', '=', 'org_source.source_id')
		->select('org_group.*', 'org_source.source_code')
		->get();
		echo json_encode($cluster_list);

	}

	


}