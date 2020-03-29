<?php

namespace App\Http\Controllers\Merchandising;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\Models\Merchandising\CustomerOrderType;
use Exception;

class CustomerOrderTypeController extends Controller
{

  public function __construct()
  {
    //add functions names to 'except' paramert to skip authentication
    $this->middleware('jwt.verify', ['except' => ['index']]);
  }

  //
  public function index(Request $request)
  {
    /*$type = $request->type;
    if($type == 'datatable')   {
      $data = $request->all();
      return response($this->datatable_search($data));
    }
    else if($type == 'auto')    {
      $search = $request->search;
      return response($this->autocomplete_search($search));
    }
    else {*/
      $active = $request->active;
      $fields = $request->fields;
      return response([
        'data' => $this->list($active , $fields)
      ]);
    //}
  }

  public function store(Request $request)
  {
  }


  public function show($id)
  {
  }

  public function update(Request $request, $id)
  {
  }


  //get filtered fields only
  private function list($active = 0 , $fields = null)
  {
    $query = null;
    if($fields == null || $fields == '') {
      $query = CustomerOrderType::select('*');
    }
    else{
      $fields = explode(',', $fields);
      $query = CustomerOrderType::select($fields);
      if($active != null && $active != ''){
        $query->where([['status', '=', $active]]);
      }
    }
    return $query->get();
  }

}
