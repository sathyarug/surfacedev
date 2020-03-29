<?php

namespace App\Http\Controllers\Org;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Database\Eloquent\ModelNotFoundException;

use App\Http\Controllers\Controller;
use App\Models\Org\ShipMode;
use Exception;

class ShipModeController extends Controller
{
    public function __construct()
    {
      //add functions names to 'except' paramert to skip authentication
      $this->middleware('jwt.verify', ['except' => ['index']]);
    }

    //get Season list
    public function index(Request $request)
    {
      $type = $request->type;
      if($type == 'datatable')   {
        $data = $request->all();
        return response($this->datatable_search($data));
      }
      else if($type == 'auto')    {
        $search = $request->search;
        return response($this->autocomplete_search($search));
      }
      else if($type == 'handsontable')    {
        $search = $request->search;
        return response([
          'data' => $this->handsontable_search($search)
        ]);
      }
      else {
        $active = $request->active;
        $fields = $request->fields;
        return response([
          'data' => $this->list($active , $fields)
        ]);
      //}
    }

  }


    //create a Season
    public function store(Request $request)
    {
    }


    //get a Season
    public function show($id)
    {
    }


    //update a Season
    public function update(Request $request, $id)
    {
    }

    private function autocomplete_search($search)
    {
      $company_lists = ShipMode::select('ship_mode')
      ->where([['ship_mode', 'like', '%' . $search . '%'],]) ->get();
      return $company_lists;
    }


    //deactivate a Season
    public function destroy($id)
    {
    }


    //get filtered fields only
    private function list($active = 0 , $fields = null)
    {
      $query = null;
      if($fields == null || $fields == '') {
        $query = ShipMode::select('*');
      }
      else{
        $fields = explode(',', $fields);
        $query = ShipMode::select($fields);
        if($active != null && $active != ''){
          $query->where([['status', '=', $active]]);
        }
      }
      return $query->get();
    }

    private function handsontable_search($search){
      $list = ShipMode::where('ship_mode', 'like', $search.'%')
      ->where('status', '=', 1)->get()->pluck('ship_mode');
      return $list;
    }

}
