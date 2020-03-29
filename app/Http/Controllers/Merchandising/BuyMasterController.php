<?php

namespace App\Http\Controllers\Merchandising;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use App\Libraries\CapitalizeAllFields;
use App\Http\Controllers\Controller;
use App\Models\Merchandising\BuyMaster;
use Exception;

class BuyMasterController extends Controller
{
  public function __construct()
  {
    //add functions names to 'except' paramert to skip authentication
    $this->middleware('jwt.verify', ['except' => ['index']]);
  }

  //get marker types list
  public function index(Request $request)
  {
    $type = $request->type;
    if ($type == 'datatable') {
      $data = $request->all();
      return response($this->datatable_search($data));
    } else {
      $active = $request->active;
      $fields = $request->fields;
      return response([
        'data' => $this->list($active, $fields)
      ]);
    }
  }

  //deactivate a gmarker types
  public function destroy($id)
  {
    $is_exists_comp_smv = DB::table('ie_component_smv_header')->where('buy_id', $id)->exists();

    if ($is_exists_comp_smv == true) {
      return response(['data' => [
        'status' => '0',
        'message' => 'Buy master Already in Use',
        'buy_name' => ''
        ]]);
      } else {
        $deactive = BuyMaster::where('buy_id', $id)->update(['status' => 0]);
        return response([
          'data' => [
            'status' => '1',
            'message' => 'Buy master was deactivated successfully.',
            'deactive' => $deactive
          ]
        ]);
      }
    }

    //validate anything based on requirements
    public function validate_data(Request $request)
    {
      $for = $request->for;
      if ($for == 'duplicate') {
        return response($this->validate_duplicate_code($request->buy_id, $request->buy_name));
      }
    }


    private function validate_duplicate_code($id , $code)
    {
      $select = BuyMaster::where('buy_name','=',$code)->first();
      if($select == null){
        return ['status' => 'success'];
      }
      else if($select->buy_id == $id){
        return ['status' => 'success'];
      }
      else {
        return ['status' => 'error','message' => 'Buy already exists'];
      }
    }

    public function store(Request $request)
    {
      $save = new BuyMaster();
      if ($save->validate($request->all())) {
        $save->fill($request->all());
        $save->status = 1;
        $capitalizeAllFields = CapitalizeAllFields::setCapitalAll($save);
        $save->save();

        return response([
          'data' => [
            'status' => '1',
            'message' => 'Buy mater saved successfully',
            'save' => $save
          ]
        ], Response::HTTP_CREATED);
      } else {
        $errors = $save->errors(); // failure, get errors
        return response(['errors' => ['validationErrors' => $errors]], Response::HTTP_UNPROCESSABLE_ENTITY);
      }
    }

    

    //Find values of to be edited row
    public function show($id)
    {
      $buy_name = BuyMaster::find($id);
      if ($buy_name == null)
      throw new ModelNotFoundException("Buy not found", 1);
      else
      return response(['data' => $buy_name]);
    }

    //update a buy
    public function update(Request $request, $id)
    {
      $buy_name = BuyMaster::find($id);
      if ($buy_name->validate($request->all())) {

        $is_exists_comp_smv = DB::table('ie_component_smv_header')->where('buy_id', $id)->exists();

        if ($is_exists_comp_smv == true) {
          return response(['data' => [
            'status' => '0',
            'message' => 'Buy master Already in Use',
            'buy_name' => ''
            ]]);
          } else {
            $buy_name->fill($request->except('created_date,created_by'));
            $capitalizeAllFields = CapitalizeAllFields::setCapitalAll($buy_name);
            $buy_name->save();

            return response(['data' => [
              'status' => '1',
              'message' => 'Buy master updated successfully',
              'buy_name' => $buy_name
              ]]);
            }
          } else {
            $errors = $buy_name->errors(); // failure, get errors
            return response(['errors' => ['validationErrors' => $errors]], Response::HTTP_UNPROCESSABLE_ENTITY);
          }
        }

        private function list($active = 0, $fields = null)
        {
          $query = null;
          if ($fields == null || $fields == '') {
            $query = BuyMaster::select('*');
          } else {
            $fields = explode(',', $fields);
            $query = BuyMaster::select($fields);
            if ($active != null && $active != '') {
              $query->where([['status', '=', $active]]);
            }
          }
          return $query->get();
        }

        //get searched buy for datatable plugin format
        private function datatable_search($data)
        {
          $start = $data['start'];
          $length = $data['length'];
          $draw = $data['draw'];
          $search = $data['search']['value'];
          $order = $data['order'][0];
          $order_column = $data['columns'][$order['column']]['data'];
          $order_type = $order['dir'];

          $list = BuyMaster::select('*')
          ->where('buy_name', 'like', $search . '%')
          ->orderBy($order_column, $order_type)
          ->offset($start)->limit($length)->get();

          $count = BuyMaster::select('*')
          ->where('buy_name', 'like', $search . '%')
          ->orderBy($order_column, $order_type)
          ->count();

          return [
            "draw" => $draw,
            "recordsTotal" => $count,
            "recordsFiltered" => $count,
            "data" => $list
          ];
        }
      }
