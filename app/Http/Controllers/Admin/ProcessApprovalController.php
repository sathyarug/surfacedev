<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
//use Spatie\Permission\Models\Role;
use App\Models\Admin\ProcessApproval;
use App\Models\Admin\UsrProfile;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Auth;

class ProcessApprovalController extends Controller {


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
        if($type == 'process') {
          return response([
            'data' => $this->get_process_list()
          ]);
        }
        else if($type == 'approval_stages')   {
          return response([
            'data' => $this->get_approval_stages()
            ]);
        }
        else if($type == 'process_levels')   {
          $process = $request->process;
          return response([
            'data' => $this->get_process_levels($process)
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
      $process = $request->process;
      $approval_levels = $request->levels;

      DB::table('app_process_approvals')->where('process', '=', $process)->delete();

      for($x = 0 ; $x < sizeof($approval_levels) ; $x++) {
        DB::table('app_process_approvals')->insert([
          'process' => $process,
          'level' => ($x + 1),
          'approval_stage' => $approval_levels[$x]['stage_id']
        ]);
      }

      return response([ 'data' => [
        'message' => 'Process levels saved successfully'
        ]
      ], Response::HTTP_CREATED );
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     *
     * @return \Illuminate\View\View
     */
    /*public function show($id) {
        $spproval_stage = ApprovalStage::findOrFail($id);
        $users = [];
        if($spproval_stage == null)
          throw new ModelNotFoundException("Requested permission not found", 1);
        else{
          $users = UsrProfile::select('usr_profile.user_id','usr_profile.first_name','usr_profile.last_name','usr_profile.email','app_approval_stage_users.approval_order')
          ->join('app_approval_stage_users','app_approval_stage_users.user_id','=','usr_profile.user_id')
          ->where('app_approval_stage_users.stage_id' , '=', $id )
          ->orderBy('app_approval_stage_users.approval_order')
          ->get();

          return response([ 'approval_stage' => $spproval_stage , 'users'=> $users ]);
        }
        //return view('admin.role.show', compact('role', 'permissions'));
    }*/

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param  int  $id
     *
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    /*public function update(Request $request, $id) {

      $approval_stage = ApprovalStage::find($id);
      if($approval_stage->validate($request->formData))
      {
        $approval_stage->fill($request->formData);
        $approval_stage->save();

        DB::table('app_approval_stage_users')->where('stage_id', '=', $approval_stage->stage_id)->delete();

        $users = $request->approvalUsers;
        for($x = 0 ; $x < sizeof($users) ; $x++) {
          DB::table('app_approval_stage_users')->insert([
            'stage_id' =>$approval_stage->stage_id,
            'user_id' => $users[$x]['user_id'],
            'approval_order' => ($x + 1)
          ]);
        }

        return response([ 'data' => [
          'message' => 'Approval stage was updated successfully',
          'approval_stage' => $approval_stage
        ]]);
      }
      else
      {
        $errors = $approval_stage->errors();// failure, get errors
        return response(['errors' => ['validationErrors' => $errors]], Response::HTTP_UNPROCESSABLE_ENTITY);
      }
    }*/

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     *
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    /*public function destroy($id) {
      $stage = ApprovalStage::where('stage_id', $id)->update(['status' => 0]);
      return response([
        'data' => [
          'message' => 'Approval stage was deactivated successfully.',
          'role' => $stage
        ]
      ] , Response::HTTP_NO_CONTENT);
    }*/



      //get filtered fields only
    /*private function list($active = 0 , $fields = null)
    {
      $query = null;
      if($fields == null || $fields == '') {
        $query = Role::select('*');
      }
      else{
        $fields = explode(',', $fields);
        $query = Role::select($fields);

      }
      return $query->get();
    }*/


    //search goods types for autocomplete
    /*private function autocomplete_search($search)
  	{
  		$role_list = Role::select('id','name')
  		->where([['name', 'like', '%' . $search . '%'],]) ->get();
  		return $role_list;
  	}*/


    //get searched goods types for datatable plugin format
    /*private function datatable_search($data)
    {
      $start = $data['start'];
      $length = $data['length'];
      $draw = $data['draw'];
      $search = $data['search']['value'];
      $order = $data['order'][0];
      $order_column = $data['columns'][$order['column']]['data'];
      $order_type = $order['dir'];

      $stage_list = ApprovalStage::select('*')
      ->where('stage_name'  , 'like', $search.'%' )
      ->orderBy($order_column, $order_type)
      ->offset($start)->limit($length)->get();

      $stage_count = ApprovalStage::where('stage_name'  , 'like', $search.'%' )
      ->count();

      return [
          "draw" => $draw,
          "recordsTotal" => $stage_count,
          "recordsFiltered" => $stage_count,
          "data" => $stage_list
      ];
    }*/

    //validate anything based on requirements
    /*public function validate_data(Request $request){
      $for = $request->for;
      if($for == 'duplicate')
      {
        return response($this->validate_duplicate_role($request->stage_id , $request->stage_name));
      }
    }*/


    //check shipment cterm code code already exists
    /*private function validate_duplicate_role($stage_id , $stage_name)
    {
      $spproval_stage = ApprovalStage::where('stage_name','=',$stage_name)->first();
      if($spproval_stage == null){
        return ['status' => 'success'];
      }
      else if($spproval_stage->stage_id == $stage_id){
        return ['status' => 'success'];
      }
      else {
        return ['status' => 'error','message' => 'Stage name already exists'];
      }
    }*/


    private function get_process_list(){
      $process_list = DB::table('app_process')->get();
      return $process_list;
    }


    private function get_approval_stages(){
      $stage_list = DB::table('app_approval_stage')->get();
      $stage_list = $stage_list;

      for($x = 0 ; $x < sizeof($stage_list) ; $x++) {
        $users = UsrProfile::select('usr_profile.user_id','usr_profile.first_name','usr_profile.last_name','usr_profile.email','app_approval_stage_users.approval_order')
        ->join('app_approval_stage_users','app_approval_stage_users.user_id','=','usr_profile.user_id')
        ->where('app_approval_stage_users.stage_id' , '=', $stage_list[$x]->stage_id )
        ->orderBy('app_approval_stage_users.approval_order')
        ->get();
        $stage_list[$x]->approval_users = $users;
      }
      return $stage_list;
    }


    private function get_process_levels($process){
      $stage_list = DB::table('app_process_approvals')
      ->join('app_approval_stage', 'app_approval_stage.stage_id', '=', 'app_process_approvals.approval_stage')
      ->select('app_approval_stage.*')
      ->where('app_process_approvals.process', '=', $process)
      ->orderByRaw('app_process_approvals.level ASC')
      ->get();
      $stage_list = $stage_list;

      for($x = 0 ; $x < sizeof($stage_list) ; $x++) {
        $users = UsrProfile::select('usr_profile.user_id','usr_profile.first_name','usr_profile.last_name','usr_profile.email','app_approval_stage_users.approval_order')
        ->join('app_approval_stage_users','app_approval_stage_users.user_id','=','usr_profile.user_id')
        ->where('app_approval_stage_users.stage_id' , '=', $stage_list[$x]->stage_id )
        ->orderBy('app_approval_stage_users.approval_order')
        ->get();
        $stage_list[$x]->approval_users = $users;
      }
      return $stage_list;
    }

}
