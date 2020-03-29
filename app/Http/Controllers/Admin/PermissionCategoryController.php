<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests;
use App\Http\Controllers\Controller;
//use App\Permission;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Auth;
use App\Models\Admin\PermissionCategory;
//use Spatie\Permission\Models\PermissionCa;

class PermissionCategoryController extends Controller {

    public function __construct() {
        //add functions names to 'except' paramert to skip authentication
        $this->middleware('jwt.verify', ['except' => ['index']]);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\View\View
     */

    public function index(Request $request)
    {
      $type = $request->type;
      if($type == 'datatable')   {
        $data = $request->all();
        return response($this->datatable_search($data));
      }
      else if($type == 'all'){
        $fields = $request->fields;
        return response([
          'data' => $this->list()
        ]);
      }
    }

      //get filtered fields only
    private function list()
    {
      //  return PermissionCategory::with('permissions')->get();
      return PermissionCategory::all();
    }



    //get searched goods types for datatable plugin format
    private function datatable_search($data)
    {
      /*$start = $data['start'];
      $length = $data['length'];
      $draw = $data['draw'];
      $search = $data['search']['value'];
      $order = $data['order'][0];
      $order_column = $data['columns'][$order['column']]['data'];
      $order_type = $order['dir'];

      $permission_list = Permission::select('*')
      ->where('name'  , 'like', $search.'%' )
      ->orderBy($order_column, $order_type)
      ->offset($start)->limit($length)->get();

      $permission_count = Permission::where('name'  , 'like', $search.'%' )
      ->count();

      return [
          "draw" => $draw,
          "recordsTotal" => $permission_count,
          "recordsFiltered" => $permission_count,
          "data" => $permission_list
      ];*/
    }


    public function store(Request $request) {

    }


    public function show($id) {

    }


    public function edit($id) {

    }

    public function update(Request $request, $id) {

    }


    public function destroy($id) {

    }


}
