<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\UnitOfMeasure;

class UomController extends Controller {

    public function index() {
        return view('uom.uom');
    }

    public function loadData(Request $request)
    {
      $data = $request->all();
      $start = $data['start'];
      $length = $data['length'];
      $draw = $data['draw'];
      $search = $data['search']['value'];
      $order = $data['order'][0];
      $order_column = $data['columns'][$order['column']]['data'];
      $order_type = $order['dir'];

      $uom_list = UnitOfMeasure::select('*')
      ->where('uom_code'  , 'like', $search.'%' )
      ->orWhere('uom_description'  , 'like', $search.'%' )
      ->orderBy($order_column, $order_type)
      ->offset($start)->limit($length)->get();

      $uom_count = UnitOfMeasure::where('uom_code'  , 'like', $search.'%' )
      ->orWhere('uom_description'  , 'like', $search.'%' )
      ->count();

      echo json_encode(array(
          "draw" => $draw,
          "recordsTotal" => $uom_count,
          "recordsFiltered" => $uom_count,
          "data" => $uom_list
      ));
    }

    public function check_code(Request $request)
    {
      $uom = UnitOfMeasure::where('uom_code','=',$request->uom_code)->first();
      if($uom == null){
        echo json_encode(array('status' => 'success'));
      }
      else if($uom->uom_id == $request->uom_id){
        echo json_encode(array('status' => 'success'));
      }
      else {
        echo json_encode(array('status' => 'error','message' => 'UOM code already exists'));
      }
    }

    public function saveUom(Request $request) {
        $uom = new UnitOfMeasure();
        if ($uom->validate($request->all())) {
            if ($request->uom_id > 0) {
                $uom = UnitOfMeasure::find($request->uom_id);
                $uom->uom_description = $request->uom_description;
                $uom->uom_factor = $request->uom_factor;
                $uom->uom_base_unit = $request->uom_base_unit;
                $uom->unit_type = $request->unit_type;
            } else {
                $uom->fill($request->all());
                $uom->status = 1;
                $uom->created_by = 1;
            }

            $uom = $uom->saveOrFail();
            // echo json_encode(array('Saved'));
            echo json_encode(array('status' => 'success', 'message' => 'UOM details saved successfully.'));
        } else {
            // failure, get errors
            $errors = $uom->errors();
            echo json_encode(array('status' => 'error', 'message' => $errors));
        }
    }

    public function edit(Request $request) {
        $uom_id = $request->uom_id;
        $uom = UnitOfMeasure::find($uom_id);
        echo json_encode($uom);
    }

    public function delete(Request $request) {
        $uom_id = $request->uom_id;
        //$source = Main_Source::find($source_id);
        //$source->delete();
        $uom = UnitOfMeasure::where('uom_id', $uom_id)->update(['status' => 0]);
        echo json_encode(array(
          'status' => 'success',
          'message' => 'UOM was deactivated successfully.'
        ));
    }

    public function LoadUOM(){
        $objUOM = UnitOfMeasure::where('status','=',1)
        ->select("*")->get();
        echo json_encode($objUOM);
    }
}
