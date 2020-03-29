<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Response;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use App;
use App\Models\Finance\Accounting\CostCenter;
use App\Models\Admin\UsrProfile;
use App\Models\Admin\User;
use App\Models\Admin\UsrDepartment;
use App\Models\Admin\UsrDesignation;
use App\Models\Admin\Role;
use App\Models\Org\Location\Location;
use App\Libraries\CapitalizeAllFields;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{

  public function __construct()
  {
    //add functions names to 'except' paramert to skip authentication
    $this->middleware('jwt.verify', ['except' => ['index']]);
  }

  //get user list
  public function index(Request $request)
  {
    $type = $request->type;
    if ($type == 'datatable') {
      $data = $request->all();
      return response($this->datatable_search($data));
    } else if ($type == 'auto') {
      $search = $request->search;
      return response($this->autocomplete_search($search));
    } else if ($type == 'auto_has_login') {
      $search = $request->search;
      return response($this->autocomplete_search_has_login($search));
    } else if ($type == 'autoimmidiateReport') {
      $search = $request->search;
      return response($this->autocomplete_load_all_users($search));
    } else if ($type == 'autocomplete_hods') {
      $search = $request->search;
      return response($this->autocomplete_hods($search));
    } else {
      $active = $request->active;
      $fields = $request->fields;
      return response([
        'data' => $this->list($active, $fields)
      ]);
    }
  }

  //validate anything based on requirements
  public function validate_data(Request $request)
  {
    //dd("dadaa");
    $for = $request->for;
    if ($for == 'validateEmpNo') {
      return response($this->validate_duplicate_code($request->emp_number, $request->user_id));
    }
    else if($for == 'validateUsername'){
      return response($this->validate_duplicate_username($request->user_name, $request->user_id));
    }
  }

  public function validate_duplicate_code($emp_number, $user_id)
  {
    //$emp_number=strval( $emp_number ) ;
    $user = UsrProfile::where('emp_number', '=', $emp_number)->first();
    //dd($user);
    if ($user == null) {
      return ['status' => 'success'];
    } else if ($user->user_id == $user_id) {
      return ['status' => 'success'];
    } else {
      return ['status' => 'error', 'message' => 'Employee Number already exists'];
    }
  }

  public function validate_duplicate_username($user_name, $user_id)
  {
    //$emp_number=strval( $emp_number ) ;
    $user = User::where('user_name', '=', $user_name)->first();
    //dd($user);
    if ($user == null) {
      return ['status' => 'success'];
    } else if ($user->user_id == $user_id) {
      return ['status' => 'success'];
    } else {
      return ['status' => 'error', 'message' => 'Username already exists'];
    }
  }




  public function store(Request $request)
  {
    //  $data = request()->except(['_token']);
    $profile = new UsrProfile;
    $login = new User;

    if($profile->validate($request->except(['username', 'password'])))
    {
      $profile->fill($request->except(['username', 'password']));
      $profile->status = 1;
      $profile->reporting_level_1 = $request->reporting_level_1['user_id'];
      $profile->reporting_level_2 = $request->reporting_level_2['user_id'];
      $capitalizeAllFields = CapitalizeAllFields::setCapitalAll($profile);
      //$profile->email = $request->email;
      $profile->save();

      if($profile->user_id > 0 && $request->user_name != null && $request->password != null) {
        $login->user_id = $profile->user_id;
        $login->user_name = strtoupper($request->user_name);
        $login->reset_status = "PENDING";
        $login->password_reset_date = date('Y-m-d H:i:s');
        $login->password = Hash::make($request->password);
        $login->save();
      }

      return response([
        'data' => [
          'message' => 'User profile saved successfully',
          'user_profile' => $profile,
          'user' => $login
        ]
      ], Response::HTTP_CREATED);
    }
    else{
      $errors = $profile->errors();// failure, get errors
      $errors_str = $profile->errors_tostring();
      return response(['errors' => ['validationErrors' => $errors, 'validationErrorsText' => $errors_str]], Response::HTTP_UNPROCESSABLE_ENTITY);
    }
  }

  //deactivate a Designation
  public function destroy($id)
  {
    $user = UsrProfile::where('user_id', $id)->update(['status' => 0]);
    return response([
      'data' => [
        'message' => 'User deactivated successfully.',
        'department' => $user,
        'status' => '1'
      ]
    ]);
  }

  //get a Company
  public function show($id)
  {
    // dd($id);
    $user = UsrProfile::join('org_designation', 'usr_profile.desig_id', '=', 'org_designation.des_id')
      ->join('usr_login', 'usr_login.user_id', '=', 'usr_profile.user_id')
      ->leftJoin('usr_login as for_reporting_level_1', 'usr_profile.reporting_level_1', '=', 'for_reporting_level_1.user_id')
      ->leftJoin('usr_login as for_reporting_level_2', 'usr_profile.reporting_level_2', '=', 'for_reporting_level_2.user_id')
      ->select('usr_profile.*', 'org_designation.des_name', 'org_designation.des_id', 'usr_login.user_name', 'usr_login.password', 'for_reporting_level_1.user_name as reporting_level_1', 'for_reporting_level_2.user_name as reporting_level_2')
      ->where('usr_profile.user_id', '=', $id)
      ->first();

    if ($user == null)
      throw new ModelNotFoundException("Requested user not found", 1);
    else
      return response(['data' => $user]);
  }


  public function validateUserName(Request $request)
  {
    $user = User::where('user_name', Input::get('user_name'))->first();
    if (is_null($user))
      echo json_encode(true);
    else
      echo json_encode(false);
  }

  public function validateEmpNo(Request $request)
  {
    //dd($request->emp_number);
    $emp = UsrProfile::where('emp_number', Input::get('emp_number'))->first();
    if (is_null($emp))
      echo json_encode(true);
    else
      echo json_encode(false);
  }

  public function loadReportLevels(Request $request)
  {
    //dd($request);
    //exit;
    //echo response()->json($posts);
    $query = $request->get('q', '');

    $posts = UsrProfile::where('first_name', 'LIKE', '%' . $query . '%')->limit(5)->get();

    echo json_encode($posts);
  }

  public function getUserList()
  {
    //UsrProfile::all()->sortByDesc("created_at")->sortByDesc("status")

    return datatables()->of(DB::table('usr_profile as t1')
      ->select("t1.user_id", "t1.first_name", "t1.last_name", "t1.emp_number", "t1.email", "t2.dept_name", "t3.desig_name", "t4.loc_name")
      ->join("usr_department AS t2", "t1.dept_id", "=", "t2.dept_id")
      ->join("org_designation AS t3", "t1.desig_id", "=", "t3.des_id")
      ->join("org_location AS t4", "t1.loc_id", "=", "t4.loc_id")
      ->get())->toJson();

    //return datatables()->query(DB::table('users'))->toJson();


  }
  //update user profile
  public function update(Request $request, $id)
  {
    //dd($request);
    $user = UsrProfile::find($id);
    $user->fill($request->all());
    $date_of_birth = date_create($request->date_of_birth_);
    $user->date_of_birth = date_format($date_of_birth, "Y-m-d"); //change pcd date format to save in database
    $joined_date = date_create($request->joined_date_);
    $user->joined_date = date_format($joined_date, "Y-m-d"); //change pcd date format to save in database
    $resign_date = date_create($request->resign_date_);
    $user->resign_date = date_format($resign_date, "Y-m-d"); //change pcd date format to save in database
    $user->reporting_level_1 = $request->reporting_level_1['user_id'];
    $user->reporting_level_2 = $request->reporting_level_2['user_id'];


    $user->save();

    return response([
      'data' => [
        'message' => 'User Updated Successfully',
        'country' => $user
      ]
    ]);
  }


  public function roles(Request $request)
  {
    $type = $request->type;
    $user_id = $request->user_id;
    $location = $request->location;

    if ($type == 'assigned') {
      $assigned = Role::select('role_id', 'role_name')
        ->whereIn('role_id', function ($selected) use ($user_id, $location) {
          $selected->select('role_id')
            ->from('user_roles')
            ->where('user_id', $user_id)
            ->where('loc_id', $location);
        })->get();
      return response(['data' => $assigned]);
    } else if ($type == 'not_assigned') {
      $notAssigned = Role::select('role_id', 'role_name')
        ->whereNotIn('role_id', function ($notSelected) use ($user_id, $location) {
          $notSelected->select('role_id')
            ->from('user_roles')
            ->where('user_id', $user_id)
            ->where('loc_id', $location);
        })->get();
      return response(['data' => $notAssigned]);
    }
  }


  public function save_roles(Request $request)
  {
    $user_id = $request->get('user_id');
    $loc_id = $request->get('loc_id');
    $roles = $request->get('roles');
    if ($user_id != '') {
      DB::table('user_roles')
        ->where('user_id', '=', $user_id)
        ->where('loc_id', '=', $loc_id)
        ->delete();
      $user = UsrProfile::find($user_id);
      $save_roles = array();

      foreach ($roles as $role) {
        //array_push($save_roles, Role::find($role['role_id']));
        $user->roles()->save(Role::find($role['role_id']), ['loc_id' => $loc_id]);
      }

      //$user->roles()->saveMany($save_roles,['loc_id' => $loc_id]);
      return response([
        'data' => [
          'user_id' => $user_id
        ]
      ]);
    } else {
      throw new ModelNotFoundException("Requested user not found", 1);
    }
  }


  public function locations(Request $request)
  {
    $type = $request->type;
    $user_id = $request->user_id;
    $logUserId = auth()->user()->user_id;

    if ($type == 'assigned') {
      $assigned = Location::select('loc_id', 'loc_name')
        ->whereIn('loc_id', function ($selected) use ($user_id) {
          $selected->select('loc_id')
            ->from('user_locations')
            ->where('user_id', $user_id);
        })->get();
      return response(['data' => $assigned]);
    } else if ($type == 'not_assigned') {
      // $notAssigned = Location::select('loc_id', 'loc_name')
      //   ->where('status', '=', 1)
      //   ->whereNotIn('loc_id', function ($notSelected) use ($user_id) {
      //     $notSelected->select('loc_id')
      //       ->from('user_locations')
      //       ->where('user_id', $user_id);
      //   })->get();

      $notAssigned = DB::table('org_location')
      //  ->join('user_locations', 'user_locations.loc_id', '=', 'org_location.loc_id')
        ->select(
          'org_location.loc_id',
          'org_location.loc_name'
        )
        //->where('user_locations.user_id', $logUserId)
        ->where('org_location.status','=',1)
        ->whereNotIn('org_location.loc_id', function ($notSelected) use ($user_id) {
          $notSelected->select('user_locations.loc_id')
            ->from('user_locations')
            ->where('user_locations.user_id', $user_id);
        })->get();
      return response(['data' => $notAssigned]);
    }
  }


  public function save_locations(Request $request)
  {
    $user_id = $request->get('user_id');
    $locations = $request->get('locations');
    if ($user_id != '') {
      DB::table('user_locations')->where('user_id', '=', $user_id)->delete();
      $user = UsrProfile::find($user_id);
      $save_locations = array();

      foreach ($locations as $location) {
        array_push($save_locations, Location::find($location['loc_id']));
      }

      $user->locations()->saveMany($save_locations);
      return response([
        'data' => [
          'user_id' => $user_id
        ]
      ]);
    } else {
      throw new ModelNotFoundException("Requested user not found", 1);
    }
  }


  public function user_assigned_locations()
  {
    $user_id = auth()->user()->user_id;
    $locations = DB::select("SELECT user_locations.*, org_location.loc_code, org_location.loc_name FROM user_locations
      INNER JOIN org_location ON org_location.loc_id = user_locations.loc_id WHERE user_locations.user_id = ?", [$user_id]);
    return response([
      'data' => $locations
    ]);
  }


  //search Color for autocomplete
  private function autocomplete_search_has_login($search)
  {
    $user_lists = UsrProfile::select(
      'usr_profile.user_id',
      'usr_profile.first_name',
      'usr_profile.last_name',
      'usr_profile.email',
      DB::raw('CONCAT(usr_profile.first_name,usr_profile.last_name) AS full_name')
    )
      ->join('usr_login', 'usr_login.user_id', '=', 'usr_profile.user_id')
      ->where([['usr_profile.first_name', 'like', '%' . $search . '%'],])->get();
    return $user_lists;
  }

  private function autocomplete_load_all_users($search)
  {
    //dd($search);
    $active = 1;
    $user_lists = UsrProfile::select('usr_profile.user_id', 'user_name')
      ->join('usr_login', 'usr_profile.user_id', '=', 'usr_login.user_id')
      ->where([['usr_login.user_name', 'like', '%' . $search . '%']])
      ->where('status', '=', $active)
      ->get();
    return $user_lists;
  }


  private function autocomplete_hods($search)
  {
    //dd($search);
    $active = 1;
    $user_lists = UsrProfile::select('usr_profile.user_id', 'user_name')
      ->join('org_designation', 'usr_profile.desig_id', '=', 'org_designation.des_id')
      ->join('usr_login', 'usr_profile.user_id', '=', 'usr_login.user_id')
      ->where([['usr_login.user_name', 'like', '%' . $search . '%']])
      ->where('usr_profile.status', '=', $active)
      ->where('org_designation.des_name', '=', 'HOD')
      ->get();
    return $user_lists;
  }

  //get searched Colors for datatable plugin format
  private function datatable_search($data)
  {
    $start = $data['start'];
    $length = $data['length'];
    $draw = $data['draw'];
    $search = $data['search']['value'];
    $order = $data['order'][0];
    $order_column = $data['columns'][$order['column']]['data'];
    $order_type = $order['dir'];

    $user_list = UsrProfile::leftjoin('org_location', 'usr_profile.loc_id', '=', 'org_location.loc_id')
      ->join('org_departments', 'usr_profile.dept_id', '=', 'org_departments.dep_id')
      ->join('org_designation', 'usr_profile.desig_id', '=', 'org_designation.des_id')
      //->join('org_location', 'usr_profile.loc_id', '=', 'org_location.loc_id')
      ->select('usr_profile.*', 'org_location.loc_name', 'org_departments.dep_name', 'org_designation.des_name')
      ->where('first_name', 'like', $search . '%')
      ->orWhere('last_name', 'like', $search . '%')
      ->orWhere('emp_number', 'like', $search . '%')
      ->orWhere('org_designation.des_name', 'like', $search . '%')
      ->orWhere('org_departments.dep_name', 'like', $search . '%')
      ->orderBy($order_column, $order_type)
      ->offset($start)->limit($length)->get();

    $user_count = UsrProfile::leftjoin('org_location', 'usr_profile.loc_id', '=', 'org_location.loc_id')
      ->join('org_departments', 'usr_profile.dept_id', '=', 'org_departments.dep_id')
      ->join('org_designation', 'usr_profile.desig_id', '=', 'org_designation.des_id')
      ->where('first_name', 'like', $search . '%')
      ->orWhere('last_name', 'like', $search . '%')
      ->orWhere('emp_number', 'like', $search . '%')
      ->orWhere('org_designation.des_name', 'like', $search . '%')
      ->orWhere('org_departments.dep_name', 'like', $search . '%')
      ->count();

    return [
      "draw" => $draw,
      "recordsTotal" => $user_count,
      "recordsFiltered" => $user_count,
      "data" => $user_list
    ];
  }
}
