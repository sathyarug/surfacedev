<?php

namespace App\Http\Controllers\Org;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Database\Eloquent\ModelNotFoundException;

use App\Http\Controllers\Controller;
use App\Models\Org\PropertyType;

class PropertyTypeController extends Controller
{
    public function __construct()
    {
      //add functions names to 'except' paramert to skip authentication
      $this->middleware('jwt.verify', ['except' => ['index']]);
    }

    //get Department list
    public function index(Request $request)
    {
      $type = $request->type;
      /*if($type == 'datatable')   {
        $data = $request->all();
        return response($this->datatable_search($data));
      }*/
      if($type == 'auto')    {
        $search = $request->search;
        return response($this->autocomplete_search($search));
      }
      else {
        $active = $request->active;
        $fields = $request->fields;
        return response([
          'data' => $this->list($active , $fields)
        ]);
      }
    }


    //create a Department
    public function store(Request $request)
    {

    }


    //get a Department
    public function show($id)
    {

    }


    //update a Department
    public function update(Request $request, $id)
    {

    }


    //deactivate a Department
    public function destroy($id)
    {

    }


    //get filtered fields only
    private function list($active = 0 , $fields = null)
    {
      $query = null;
      if($fields == null || $fields == '') {
        $query = PropertyType::select('*');
      }
      else{
        $fields = explode(',', $fields);
        $query = PropertyType::select($fields);
        if($active != null && $active != ''){
          $query->where([['status', '=', $active]]);
        }
      }
      return $query->get();
    }

    //search Department for autocomplete
    private function autocomplete_search($search)
  	{
  		$type_lists = LocationType::select('type_prop_id','type_property')
  		->where([['type_property', 'like', '%' . $search . '%'],]) ->get();
  		return $type_lists;
  	}

}
