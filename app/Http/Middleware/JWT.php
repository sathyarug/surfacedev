<?php

namespace App\Http\Middleware;

use Closure;
use JWTAuth;
use Exception;
use Tymon\JWTAuth\Http\Middleware\BaseMiddleware;

use App\User;

class JWT extends BaseMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
      try {
              $user = JWTAuth::parseToken()->authenticate();
              $db_user = User::find($user->user_id);//check passed token with saved token. used to limit single concurrent user
              //echo json_encode($db_user);die();
              if(auth()->payload()->get('jti') != $db_user->token){
                return response()->json(['status' => 'There are currently multiple sessions logged in with this username and password'],401);
              }
          } catch (Exception $e) {
              if ($e instanceof \Tymon\JWTAuth\Exceptions\TokenInvalidException){
                  return response()->json(['status' => 'Token is Invalid'],401);
              }else if ($e instanceof \Tymon\JWTAuth\Exceptions\TokenExpiredException){
                  return response()->json(['status' => 'Token is Expired'],401);
              }else{
                  return response()->json(['status' => 'Authorization Token not found'],401);
              }
          }
          return $next($request);
    }
}
