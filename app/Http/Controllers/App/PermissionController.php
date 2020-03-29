<?php

namespace App\Http\Controllers\App;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\DB;

use App\Http\Controllers\Controller;
use Exception;

class PermissionController extends Controller
{
    public function __construct()
    {
      $this->middleware('jwt.verify', ['except' => []]);
      //add functions names to 'except' paramert to skip authentication
    }

    public function index(Request $request) {
      $type = $request->type;

      if($type == 'module_permission'){
        $permission_category = $request->permission_category;
        return response([
          'data' => $this->get_module_permission($permission_category)
        ]);
      }

    }


    public function get_required_permissions(Request $request)
    {
        $result = DB::table('usr_login_permission')
        ->where('user_id', '=', auth()->user()->user_id)
        ->whereIn('permission_code', $request->permissions)
        ->get();
        $permissions = [];
        foreach ($result as $row) {
        //  echo json_encode($row);
          $permissions[$row->permission_code] = true;
        }
        return response([
          'data' => $permissions
        ]);
    }


    private function get_module_permission($permission_category){
      $result = DB::table('usr_login_permission')
      ->where('user_id', '=', auth()->user()->user_id)
      ->where('permission_category', '=', $permission_category)
      ->whereIn('permission_code', $request->permissions)
      ->get();
      $permissions = [];
      foreach ($result as $row) {
      //  echo json_encode($row);
        $permissions[$row->permission_code] = true;
      }
      return response([
        'data' => $permissions
      ]);
    }

}
