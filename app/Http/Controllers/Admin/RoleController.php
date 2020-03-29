<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
//use Spatie\Permission\Models\Role;
use App\Models\Admin\Role;
use App\Models\Admin\Permission;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Auth;

class RoleController extends Controller {


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
        if($type == 'datatable') {
          $data = $request->all();
          return response($this->datatable_search($data));
        }
        else if($type == 'category_permissions')   {
          $role = $request->role;
          $category = $request->category;
          return response([
            'data' => $this->category_permissions($role, $category)
            ]);
        }
        else {
        /*  $active = $request->active;
          $fields = $request->fields;
          return response([
            'data' => $this->list($active , $fields)
          ]);*/
        }
    }


    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function store(Request $request) {
      $role = new Role();
      if($role->validate($request->all()))
      {
        $role->fill($request->all());
        $role->status = 1;
        $role->save();

        return response([ 'data' => [
          'message' => 'Permission role saved successfully',
          'role' => $role
          ]
        ], Response::HTTP_CREATED );
      }
      else
      {
        $errors = $role->errors();// failure, get errors
        $errors_str = $role->errors_tostring();
        return response(['errors' => ['validationErrors' => $errors, 'validationErrorsText' => $errors_str]], Response::HTTP_UNPROCESSABLE_ENTITY);
      }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     *
     * @return \Illuminate\View\View
     */
    public function show($id) {
        $role = Role::findOrFail($id);
        $permissions = $role->permissions->pluck('name');

        if($role == null)
          throw new ModelNotFoundException("Requested permission not found", 1);
        else
          return response([ 'data' => $role , 'permissions'=> $permissions ]);
        //return view('admin.role.show', compact('role', 'permissions'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param  int  $id
     *
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function update(Request $request, $id) {

      $role = Role::find($id);
      if($role->validate($request->all()))
      {
        $role->fill($request->all());
        $role->save();

        return response([ 'data' => [
          'message' => 'Role updated successfully',
          'role' => $role
        ]]);
      }
      else
      {
        $errors = $role->errors();// failure, get errors
        return response(['errors' => ['validationErrors' => $errors]], Response::HTTP_UNPROCESSABLE_ENTITY);
      }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     *
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function destroy($id) {
      $role = Role::where('role_id', $id)->update(['status' => 0]);
      return response([
        'data' => [
          'message' => 'Role deactivated successfully.',
          'role' => $role
        ]
      ] , Response::HTTP_NO_CONTENT);
    }


    private function category_permissions($role, $category){
      if($role != 0){
        return DB::select('select permission.*,(case when (permission_role_assign.role IS NULL) THEN  0 ELSE 1 END) as status from
        permission left join permission_role_assign on permission.code = permission_role_assign.permission and permission_role_assign.role = ?
        where permission.category = ?',
        [$role , $category]);
      }
      else{
        return Permission::where('category', '=' , $category)->get();
      }
    }


    public function change_role_permission(Request $request) {
      $role_id = $request->role_id;
      $permission_code = $request->permission;
      $status = $request->status;
      $response_message = '';

      if($status == false){
        DB::table('permission_role_assign')
        ->where('role', '=', $role_id)
        ->where('permission', '=', $permission_code)
        ->delete();
        $response_message = 'Permission ('.$permission_code.') was removed';
      }
      else if($status == true){
        DB::table('permission_role_assign')->insert([
            ['role' => $role_id, 'permission' => $permission_code]
        ]);
        $response_message = 'Permission ('.$permission_code.') was assigned';
      }

      return response([ 'data' => [
       'message' => $response_message,
       'status' => $status
      ]]);
    }



      //get filtered fields only
    private function list($active = 0 , $fields = null)
    {
      $query = null;
      if($fields == null || $fields == '') {
        $query = Role::select('*');
      }
      else{
        $fields = explode(',', $fields);
        $query = Role::select($fields);
        /*if($active != null && $active != ''){
          $query->where([['status', '=', $active]]);
        }*/
      }
      return $query->get();
    }


    //search goods types for autocomplete
    private function autocomplete_search($search)
  	{
  		$role_list = Role::select('id','name')
  		->where([['name', 'like', '%' . $search . '%'],]) ->get();
  		return $role_list;
  	}


    //get searched goods types for datatable plugin format
    private function datatable_search($data)
    {
      $start = $data['start'];
      $length = $data['length'];
      $draw = $data['draw'];
      $search = $data['search']['value'];
      $order = $data['order'][0];
      $order_column = $data['columns'][$order['column']]['data'];
      $order_type = $order['dir'];

      $role_list = Role::select('*')
      ->where('role_name'  , 'like', $search.'%' )
      ->orderBy($order_column, $order_type)
      ->offset($start)->limit($length)->get();

      $role_count = Role::where('role_name'  , 'like', $search.'%' )
      ->count();

      return [
          "draw" => $draw,
          "recordsTotal" => $role_count,
          "recordsFiltered" => $role_count,
          "data" => $role_list
      ];
    }

    //validate anything based on requirements
    public function validate_data(Request $request){
      $for = $request->for;
      if($for == 'duplicate')
      {
        return response($this->validate_duplicate_role($request->role_id , $request->role_name));
      }
    }


    //check shipment cterm code code already exists
    private function validate_duplicate_role($role_id , $role_name)
    {
      $role = Role::where('role_name','=',$role_name)->first();
      if($role == null){
        return ['status' => 'success'];
      }
      else if($role->role_id == $role_id){
        return ['status' => 'success'];
      }
      else {
        return ['status' => 'error','message' => 'Role name already exists'];
      }
    }


}
