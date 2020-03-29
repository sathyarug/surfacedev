<?php
namespace App\Http\Controllers\D2d;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Http\Controllers\Controller;
use App\UsrProfile;
use DB;


class D2DController extends Controller
{

  public function load_d2d_user(Request $request)
  {
    $epf = $request->epf;
    $load_list = UsrProfile::join('d2d_userlogin', 'usr_profile.d2d_epf', '=', 'd2d_userlogin.uname')
     ->select('d2d_userlogin.*')
     ->where('d2d_epf'  , '=', $epf )
     ->get();

    //dd($load_list);
     return response([ 'data' => [
       'load_list' => $load_list
       ]
     ], Response::HTTP_CREATED );

  }



}
