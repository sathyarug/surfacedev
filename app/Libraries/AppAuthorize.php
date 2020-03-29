<?php

namespace App\Libraries;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class AppAuthorize
{

  public function hasPermission($permission)
  {     
      $count = DB::table('usr_login_permission')
      ->where('user_id', '=', auth()->user()->user_id)
      ->where('permission_code', '=', $permission)
      ->count();

      if($count > 0){
        return true;
      }
      else{
        return false;
      }
  }


  public function error_response(){
    return [
      'data' => 'Not authorized'
    ];
  }

}
