<?php

namespace App\Http\Controllers\Finance\Accounting;

use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use App\Libraries\CapitalizeAllFields;
use App\Http\Controllers\Controller;
use App\Models\Finance\Accounting\CostCenter;
use App\Models\Admin\UsrProfile;
use App\Libraries\AppAuthorize;

class CostCenterController extends Controller
{
  var $authorize = null;

  public function __construct()
  {
    //add functions names to 'except' paramert to skip authentication
    $this->middleware('jwt.verify', ['except' => ['index']]);
    $this->authorize = new AppAuthorize();
  }

  //get cost center list
  public function index(Request $request)
  {
    $type = $request->type;

    if ($type == 'datatable') {
      $data = $request->all();
      return response($this->datatable_search($data));
    } else if ($type == 'auto') {
      $search = $request->search;
      return response($this->autocomplete_search($search));
    } else {
      $active = $request->active;
      $fields = $request->fields;
      return response([
        'data' => $this->list($active, $fields)
      ]);
    }
  }

  //create a cost center
  public function store(Request $request)
  {
    if ($this->authorize->hasPermission('COST_CENTER_MANAGE')) //check permission
    {
      $costCenter = new CostCenter();
      if ($costCenter->validate($request->all())) {
        $costCenter->fill($request->all());
        $capitalizeAllFields = CapitalizeAllFields::setCapitalAll($costCenter);
        $costCenter->status = 1;
        $costCenter->save();

        return response([
          'data' => [
            'message' => 'Cost center saved successfully',
            'CostCenter' => $costCenter,
            'status' => '1'
          ]
        ], Response::HTTP_CREATED);
      } else {
        $errors = $costCenter->errors(); // failure, get errors
        $errors_str = $costCenter->errors_tostring();
        return response(['errors' => ['validationErrors' => $errors, 'validationErrorsText' => $errors_str]], Response::HTTP_UNPROCESSABLE_ENTITY);
      }
    } else {
      return response($this->authorize->error_response(), 401);
    }
  }

  //get a cost center
  public function show($id)
  {
    $costCenter = CostCenter::find($id);
    if ($costCenter == null)
      throw new ModelNotFoundException("Requested cost center not found", 1);
    else
      return response(['data' => $costCenter]);
  }


  //update a cost center
  public function update(Request $request, $id)
  {
    if ($this->authorize->hasPermission('COST_CENTER_MANAGE')) //check permission
    {
      $costCenter = CostCenter::find($id);
      $is_exists_user = UsrProfile::where('cost_center_id', $id)->exists();

      if ($is_exists_user) {
        return response(['data' => [
          'message' => 'Cost center Already in Use',
          'status' => '0'
        ]]);
      } else {
        if ($costCenter->validate($request->all())) {
          $costCenter->fill($request->except('cost_center_code'));
          $capitalizeAllFields = CapitalizeAllFields::setCapitalAll($costCenter);
          $costCenter->save();

          return response(['data' => [
            'message' => 'Cost center updated successfully',
            'CostCenter' => $costCenter,
            'status' => '1'
          ]]);
        } else {
          $errors = $costCenter->errors(); // failure, get errors
          $errors_str = $costCenter->errors_tostring();
          return response(['errors' => ['validationErrors' => $errors, 'validationErrorsText' => $errors_str]], Response::HTTP_UNPROCESSABLE_ENTITY);
        }
      }
    } else {
      return response($this->authorize->error_response(), 401);
    }
  }

  //deactivate a cost center
  public function destroy($id)
  {
    if ($this->authorize->hasPermission('COST_CENTER_DELETE')) //check permission
    {
      $is_exists_user = UsrProfile::where('cost_center_id', $id)->exists();
      if ($is_exists_user == 'true') {
        return response(['data' => [
          'message' => 'Cost center Already in Use',
          'status' => '0'
        ]]);
      } else {
        $costCenter = CostCenter::where('cost_center_id', $id)->update(['status' => 0]);
        return response([
          'data' => [
            'message' => 'Cost center deactivated successfully.',
            'CostCenter' => $costCenter,
            'status' => '1'
          ]
        ]);
      }
    } else {
      return response($this->authorize->error_response(), 401);
    }
  }


  //validate anything based on requirements
  public function validate_data(Request $request)
  {
    $for = $request->for;
    if ($for == 'duplicate') {
      return response($this->validate_duplicate_code($request->cost_center_id, $request->cost_center_code));
    }
  }


  //check cost center code already exists
  private function validate_duplicate_code($id, $code)
  {
    $costCenter = CostCenter::where('cost_center_code', '=', $code)->first();
    if ($costCenter == null) {
      return ['status' => 'success'];
    } else if ($costCenter->cost_center_id == $id) {
      return ['status' => 'success'];
    } else {
      return ['status' => 'error', 'message' => 'Cost center code already exists'];
    }
  }


  //get filtered fields only
  private function list($active = 0, $fields = null)
  {
    $query = null;
    if ($fields == null || $fields == '') {
      $query = CostCenter::select('*')
        ->where('status', '=', 1);
    } else {
      $fields = explode(',', $fields);
      $query = CostCenter::select($fields);
      if ($active != null && $active != '') {
        $query->where([['status', '=', $active]]);
        //echo("djdjjdjd2");

      }
      $query = CostCenter::select('*')
        ->where('status', '=', 1);
    }

    return $query->get();
  }


  //search cost center for autocomplete
  private function autocomplete_search($search)
  {
    $cost_center_list = CostCenter::select('cost_center_id', 'cost_center_name')
      ->where([['cost_center_name', 'like', '%' . $search . '%'],])
      ->where('status', '=', 1)->get();

    //echo $cost_center_list;die();
    return $cost_center_list;
  }


  //get searched cost center for datatable plugin format
  private function datatable_search($data)
  {
    if ($this->authorize->hasPermission('COST_CENTER_MANAGE')) //check permission {
    {
      $start = $data['start'];
      $length = $data['length'];
      $draw = $data['draw'];
      $search = $data['search']['value'];
      $order = $data['order'][0];
      $order_column = $data['columns'][$order['column']]['data'];
      $order_type = $order['dir'];

      $cost_center_list = CostCenter::select('*')
        ->where('cost_center_code', 'like', $search . '%')
        ->orWhere('cost_center_name', 'like', $search . '%')
        ->orderBy($order_column, $order_type)
        ->offset($start)->limit($length)->get();

      $cost_center_count = CostCenter::where('cost_center_code', 'like', $search . '%')
        ->orWhere('cost_center_name', 'like', $search . '%')
        ->count();

      return [
        "draw" => $draw,
        "recordsTotal" => $cost_center_count,
        "recordsFiltered" => $cost_center_count,
        "data" => $cost_center_list
      ];
    } else {
      return response($this->authorize->error_response(), 401);
    }
  }
}
