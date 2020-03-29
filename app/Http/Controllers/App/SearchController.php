<?php

namespace App\Http\Controllers\App;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\DB;

use App\Http\Controllers\Controller;
use Exception;

class SearchController extends Controller
{
    public function __construct()
    {
      //add functions names to 'except' paramert to skip authentication
    //  $this->middleware('jwt.verify', ['except' => ['index']]);
    }

    //get Color list
    public function index(Request $request)
    {
        auth()->payload()->get('loc_id');
      /*  $menus = DB::select('select * from app_menu where level = 1');
        $level = 2;
        foreach ($menus as $row) {
          $menus2 = "select * from app_menu where level = 1"
        }*/
        $fields = $request->fields;
        return response([
          'data' => $this->search($fields)
        ]);
    }

    private function search($fields = null) {

    return $fields;
  }


}
