<?php

namespace App\Http\Controllers\App;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\DB;

use App\Http\Controllers\Controller;
use Exception;
use Mockery\Undefined;

class MenuController extends Controller
{
  public $x = 0;

  public function __construct()
  {
    //add functions names to 'except' paramert to skip authentication
    //  $this->middleware('jwt.verify', ['except' => ['index']]);
  }

  //get Color list
  public function index(Request $request)
  {
    auth()->payload()->get('loc_id');
    $user_id = auth()->user()->user_id;
    /*  $menus = DB::select('select * from app_menu where level = 1');
        $level = 2;
        foreach ($menus as $row) {
          $menus2 = "select * from app_menu where level = 1"
        }*/
    return response([
      'data' => $this->get_menus(null, $user_id)
    ]);
  }

  function get_menus($menu = null, $user_id)
  {
    if ($menu == null) {
      $menus = DB::select('select * from app_menu where level = ? ORDER BY app_menu.order ASC', [1]);
    } else {
      $menus = DB::select(
        'select app_menu.* from app_menu where app_menu.parent_menu = ? AND
            IF(app_menu.permission IS NULL , 1 ,
              (SELECT COUNT(usr_login_permission.permission_code) FROM usr_login_permission WHERE usr_login_permission.user_id = ? AND
              usr_login_permission.permission_code=app_menu.permission)) >= 1 ORDER BY app_menu.order ASC',
        [$menu->code, $user_id]
      );
      $menu->sub_menus = $menus;
    }

    if (sizeof($menus) <= 0) {
      // end the recursion
      return;
    } else {
      // continue the recursion
      foreach ($menus as $row) {
        $this->get_menus($row, $user_id);
      }
    }
    return $menus;
  }

  public function getSearchMenu(Request $request)
  {
    $search = $request->search;
    if ($search != null) {
      $user_id = auth()->user()->user_id;

      return response([
        'data' => $this->get_search_menus($user_id, $search)
      ]);
    } else {
      $user_id = auth()->user()->user_id;

      return response([
        'data' => $this->get_menus(null, $user_id)
      ]);
    }
  }

  //get search menu
  function get_search_menus($user_id, $search)
  {
    $menus3 = DB::select(
      'select app_menu.* from app_menu where app_menu.name LIKE "%' . $search . '%" AND app_menu.level = 3 AND
              IF(app_menu.permission IS NULL , 1 ,
                (SELECT COUNT(usr_login_permission.permission_code) FROM usr_login_permission WHERE usr_login_permission.user_id = ? AND
                usr_login_permission.permission_code=app_menu.permission)) >= 1 ORDER BY app_menu.order ASC',
      [$user_id]
    );

    $loadCount = COUNT($menus3);
    foreach ($menus3 as $row) {
      $row->sub_menus = [];
    }

    for ($i = 0; $i < $loadCount; $i++) {
      // $arr1 = [];
      $menus2 = DB::select('select app_menu.* from app_menu where app_menu.code = "' . $menus3[$i]->parent_menu . '"');
      foreach ($menus2 as $row) {
        $arr1[] = (array) $menus3[$i];
        $row->sub_menus = $arr1;
      }
    }

    $loadCount1 = COUNT($menus2);

    for ($j = 0; $j < $loadCount1; $j++) {
      // $arr2 = [];
      $menus = DB::select('select app_menu.* from app_menu where app_menu.code = "' . $menus2[$j]->parent_menu . '"');
      foreach ($menus as $row) {
        $arr2[] = (array) $menus2[$j];
        $row->sub_menus = $arr2;
      }
    }
    // dd($menus);

    return $menus;
  }



  //get search menu
  // function get_search_menus($menu = null, $user_id, $search)
  // {

  //   if ($menu == null) {
  //     $this->x++;
  //     $menus = DB::select('select * from app_menu where level = ? ORDER BY app_menu.order ASC', [1]);
  //   } else {
  //     $this->x++;
  //     if ($this->x == 2) {

  //       $menus = DB::select(
  //         'select app_menu.* from app_menu where app_menu.parent_menu = ? AND
  //             IF(app_menu.permission IS NULL , 1 ,
  //               (SELECT COUNT(usr_login_permission.permission_code) FROM usr_login_permission WHERE usr_login_permission.user_id = ? AND
  //               usr_login_permission.permission_code=app_menu.permission)) >= 1 ORDER BY app_menu.order ASC',
  //         [$menu->code, $user_id]
  //       );
  //       $menu->sub_menus = $menus;
  //     } elseif ($this->x == 3) {
  //       $menus = DB::select(
  //         'select app_menu.* from app_menu where app_menu.parent_menu = ? AND app_menu.name LIKE "%' . $search . '%" AND
  //             IF(app_menu.permission IS NULL , 1 ,
  //               (SELECT COUNT(usr_login_permission.permission_code) FROM usr_login_permission WHERE usr_login_permission.user_id = ? AND
  //               usr_login_permission.permission_code=app_menu.permission)) >= 1 ORDER BY app_menu.order ASC',
  //         [$menu->code, $user_id]
  //       );
  //       $menu->sub_menus = $menus;
  //     }
  //   }


  //   if (sizeof($menus) <= 0) {
  //     // end the recursion
  //     return;
  //   } else {
  //     // continue the recursion
  //     foreach ($menus as $row) {
  //       $this->get_search_menus($row, $user_id, $search);
  //     }
  //   }

  //   return $menus;
  // }
}
