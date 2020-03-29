<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Country;
use Mockery\CountValidator\Exception;

class CountryController extends Controller {

    public function index() {
        return view('country.country_page');
    }

    public function insertCountry(Request $request) {
        try {

            $country = new Country();
            if ($request->country_id) {
                $country = Country::find($request->country_id);
            }
            $country->country_code = $request->country_code;
            $country->country_description = $request->country_description;
            $country->save();
            return 'true';
        } catch (Exception $e) {
            return 'false';
        }
    }

    /* public function show() {
      try {
      return Country::get();
      } catch (Exception $e) {
      return 'false';
      }
      } */

    public function loaddata(Request $request) {
      $data = $request->all();
      $start = $data['start'];
      $length = $data['length'];
      $draw = $data['draw'];
      $search = $data['search']['value'];
      $order = $data['order'][0];
      $order_column = $data['columns'][$order['column']]['data'];
      $order_type = $order['dir'];

      $country_list = Country::select('*')
      ->where('country_code'  , 'like', $search.'%' )
      ->orWhere('country_description'  , 'like', $search.'%' )
      ->orderBy($order_column, $order_type)
      ->offset($start)->limit($length)->get();

      $country_count = Country::where('country_code'  , 'like', $search.'%' )
      ->orWhere('country_description'  , 'like', $search.'%' )
      ->count();

      echo json_encode(array(
          "draw" => $draw,
          "recordsTotal" => $country_count,
          "recordsFiltered" => $country_count,
          "data" => $country_list
      ));
    }

    public function check_code(Request $request)
    {
      $country = Country::where('country_code','=',$request->country_code)->first();
      if($country == null){
        echo json_encode(array('status' => 'success'));
      }
      else if($country->country_id == $request->country_id){
        echo json_encode(array('status' => 'success'));
      }
      else {
        echo json_encode(array('status' => 'error','message' => 'Country code already exists'));
      }
    }

    public function saveCountry(Request $request) {
        $country = new Country();
        if ($country->validate($request->all())) {
            if ($request->country_id > 0) {
                $country = Country::find($request->country_id);
                $country->country_description = $request->country_description;
            } else {
                $country->fill($request->all());
                $country->status = 1;
                $country->created_by = 1;
            }

            $result = $country->saveOrFail();
            // echo json_encode(array('Saved'));
            echo json_encode(array('status' => 'success', 'message' => 'Source details saved successfully.'));
        } else {
            // failure, get errors
            $errors = $country->errors();
            echo json_encode(array('status' => 'error', 'message' => $errors));
        }
    }

    public function edit(Request $request) {
        $country_id = $request->country_id;
        $country = Country::find($country_id);
        echo json_encode($country);
    }

    public function delete(Request $request) {
        $country_id = $request->country_id;
        //$source = Main_Source::find($source_id);
        //$source->delete();
        $country = Country::where('country_id', $country_id)->update(['status' => 0]);
        echo json_encode(array(
          'status' => 'success',
          'message' => 'Country was deactivated successfully.'
        ));
    }

    /* public function delete($id) {
      try {
      Country::destroy($id);
      return 'true';
      } catch (Exception $e) {
      return 'false';
      }
      } */

    /* public function edit($id) {
      try {
      return Country::find($id);
      } catch (Exception source_hid) {
      return 'false';
      }
      } */

    public function update(Request $request, $id) {
        try {
            $country = Country::find($id);
            $country->country_code = $request->country_code;
            $country->country_description = $request->country_description;
            $country->update();
            return 'true';
        } catch (Exception $e) {
            return 'false';
        }
    }

    public function get_active_list(Request $request)
  	{
  		$search_c = $request->search;
  		$country_lists = Country::select('country_id','country_code','country_description')
  		->where([['country_description', 'like', '%' . $search_c . '%'],]) ->get();
  		return response()->json($country_lists);
  	}

}
