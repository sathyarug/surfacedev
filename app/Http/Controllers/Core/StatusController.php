<?php

namespace App\Http\Controllers\Core;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Database\Eloquent\ModelNotFoundException;

use App\Http\Controllers\Controller;
use App\Models\Core\Status;
use Exception;

class StatusController extends Controller
{
    public function __construct()
    {
      //add functions names to 'except' paramert to skip authentication
      $this->middleware('jwt.verify', ['except' => ['index']]);
    }

    //get Color list
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
        $type = $request->type;
        return response([
          'data' => $this->list($type)
        ]);
    //  }
    }


    //create a Color
    public function store(Request $request)
    {
    }


    //get a Color
    public function show($id)
    {
    }


    //update a Color
    public function update(Request $request, $id)
    {
    }


    //deactivate a Color
    public function destroy($id)
    {
    }


    //get filtered fields only
    private function list($type)
    {
        $query = Status::where('type', '=' , $type)->get();
        return $query;
    }



}
