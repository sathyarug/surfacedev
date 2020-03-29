<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Tymon\JWTAuth\Facades\JWTAuth;
use Illuminate\Support\Facades\Hash;

use App\UsrProfile;
use App\User;
use App\Models\Admin\ResetPassword;

use Illuminate\Support\Facades\Mail;
use App\Mail\MailSenderMailable;

class AuthController extends Controller
{
    /**
     * Create a new AuthController instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth:api', ['except' => ['login', 'logout','validate_mail','send_confirmation','save_new_password','confirm_link']]);
    }

    /**
     * Get a JWT via given credentials.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function login()
    {
        $credentials = request(['user_name', 'password']);

        $customData = $this->get_user_from_username($credentials['user_name']);

        if (!$token = auth()->claims($customData)->setTTL(720)->attempt($credentials)) {
            //return response()->json(['error' => 'Unauthorized'], 401);
              return response()->json(['error' => 'Unauthorized' , 'message' => 'Incorrect username or password'], 401);
        }
        else
        {

          $days = date_diff(date_create(date('Y-m-d H:i:s')), date_create($customData['password_reset_date'])); 
          
          if($customData['reset_status']!="RESET"){
            return response()->json(['error' => 'PENDING' , 'message' => 'Please reset your password'], 401);
          }
          else if($days->format('%a')>90){
            return response()->json(['error' => 'PENDING' , 'message' => 'Your password has expired'], 401);
          }
          else
          {
            if($customData['loc_id'] != 0){
              //save token to database
              $user = User::find(auth()->user()->user_id);
              $user->token = auth()->payload()->get('jti');
              $user->save(); //update token in user_login table

              $this->store_load_permissions($customData['user_id'], $customData['loc_id']);
              return $this->respondWithToken($token, $customData['loc_id']);
            }
            else{
              return response()->json(['error' => 'Unauthorized' , 'message' => 'No location assigned for user'], 401);
            }
          }

        }

    }

    /**
    * Get the authenticated User.
    *
    * @return \Illuminate\Http\JsonResponse
    */
   public function me()
   {
       return response()->json(auth()->user());
   }

   /**
    * Log the user out (Invalidate the token).
    *
    * @return \Illuminate\Http\JsonResponse
    */
   public function logout()
   {
       $user_id = auth()->user()->user_id;
       $user = User::find($user_id);
       $user->token = null;
       $user->save(); //update token in user_login table

       auth()->logout();
       DB::table('usr_login_permission')->where('user_id', '=', $user_id)->delete();
       return response()->json(['message' => 'Successfully logged out']);
   }

   /**
    * Refresh a token.
    *
    * @return \Illuminate\Http\JsonResponse
    */
   public function refresh(Request $request)
   {
     $loc_id = $request->loc_id;
     $user = UsrProfile::find(auth()->user()->user_id);
     $user->loc_id = $loc_id;
     $user = $user->toArray();
     // $customData = $this->get_user_from_username($credentials['user_name']);
     $this->store_load_permissions($user['user_id'], $loc_id);
     return $this->respondWithTokenRefresh(auth()->claims($user)->setTTL(720)->refresh(false, true), $loc_id);
   }

   /**
    * Get the token array structure.
    *
    * @param  string $token
    *
    * @return \Illuminate\Http\JsonResponse
    */
   protected function respondWithToken($token, $location)
   {
       $user_id = auth()->user()->user_id;
       $user = UsrProfile::find($user_id);
       $user_data = [
         'user_id' => $user->user_id,
         'location' => $location,
         'first_name' => $user->first_name,
         'last_name' => $user->last_name,
         'd2d_epf' => $user->d2d_epf
       ];

       $permissions = DB::table('usr_login_permission')->where('user_id' , '=', $user_id)->pluck('permission_code');

       return response()->json([
           'access_token' => $token,
           'token_type' => 'bearer',
           'expires_in' => auth()->factory()->getTTL(),
           'user' => $user_data,//auth()->user()
           'permissions' => $permissions
       ]);
   }


   protected function respondWithTokenRefresh($token, $loc_id)
   {
       $user_id = auth()->user()->user_id;
       $user = UsrProfile::find($user_id);
       $user_data = [
         'user_id' => $user->user_id,
         'location' => $loc_id,
         'first_name' => $user->first_name,
         'last_name' => $user->last_name,
         'd2d_epf' => $user->d2d_epf
       ];

       //$token2 = JWTAuth::getToken();
      //$apy = JWTAuth::getPayload($token)->toArray();
      /* $token2 = JWTAuth::getToken();
       //$token = JWTAuth::getToken();
       $apy = JWTAuth::getPayload($token2)->toArray();
       echo json_encode($apy);die();
       $user2 = User::find(auth()->user()->user_id);
       $user2->token = $apy['jti'];//auth()->payload()->get('jti');
       $user2->save(); //update token in user_login table*/

       $permissions = DB::table('usr_login_permission')->where('user_id' , '=', $user_id)->pluck('permission_code');

       return response()->json([
           'access_token' => $token,
           'token_type' => 'bearer',
           'expires_in' => auth()->factory()->getTTL(),
           'user' => $user_data,//auth()->user()
           'permissions' => $permissions
       ]);
   }


   private function get_user_from_username($username){
     $customData = UsrProfile::select('usr_profile.user_id', 'usr_profile.dept_id', 'usr_profile.d2d_epf','usr_login.reset_status','usr_login.password_reset_date')
     ->join('usr_login','usr_login.user_id','=','usr_profile.user_id')
     ->where('usr_login.user_name','=',$username)
     ->first();
     $customData = ($customData == null) ? [] : $customData->toArray();

     $default_location = DB::table('user_locations')->where('user_id' , '=', $customData['user_id'])->first();
     if($default_location != null){
      $customData['loc_id'] = $default_location->loc_id;
     }
     else{
         $customData['loc_id'] = 0;
     }
     return $customData;
   }


   private function store_load_permissions($user_id, $location){
     DB::table('usr_login_permission')->where('user_id', '=', $user_id)->delete();
     DB::insert("INSERT INTO usr_login_permission(user_id, permission_category, permission_code)
      SELECT user_roles.user_id, permission.category, permission_role_assign.permission FROM permission_role_assign
      INNER JOIN user_roles ON user_roles.role_id = permission_role_assign.role
      INNER JOIN permission ON permission_role_assign.permission = permission.code
      WHERE user_roles.user_id = ? AND user_roles.loc_id = ? GROUP BY permission_role_assign.permission", [$user_id, $location]);
  }


  public function validate_mail(Request $request)
  {
    $data = UsrProfile::where('usr_profile.email','=',$request->email)
    ->where('usr_profile.status','=',1)
    ->first();
    
    if($data == null){
      return ['status' => 'error','message' => 'The email address you entered could not be found.'];
    }
    else {
      return ['status' => 'success', 'data' => $data ]; 
    }
  }


  public function send_confirmation(Request $request)
  {

    $user=UsrProfile::join('usr_login','usr_profile.user_id','=','usr_login.user_id')
    ->where('email',$request['data']['email'])
    ->where('status','=',1)
    ->first();

    $token = date('YmdHis').substr(md5(uniqid(mt_rand(), true)) , 0, 20);
    $delete = ResetPassword::where('user_id','=',$user->user_id)->delete();

    $insert = array(
      "user_id"=> $user->user_id,
      "token"=> $token,
      "status" => "PENDING"
    );

    $ResetPassword = new ResetPassword();
    if($ResetPassword->validate($insert))
    {
      $ResetPassword->fill($insert);
      $ResetPassword->save();

      if($ResetPassword){

        $data = [
          'receiver_name' => $user->user_name,
          'path' => $request['baseUrl'].$token,
          'mail_data' => [
            'title' => "",
          ]
        ];

        $mail = Mail::to($user->email)->send(new MailSenderMailable("RESET_ACCOUNT", $data, "Password reset confirmation"));
        
        return response([ 'data' => [
          'result' => $mail,
          'status' => 'success',
          'message' => 'We just emailed a link to your mail. Please check your inbox and click the link to log in.'
         ]
        ], Response::HTTP_CREATED );

      }
      else
      {
        return response([ 'data' => [
          'result' => "",
          'status' => 'fail',
          'message' => 'Mail sending fail'
         ]
        ], Response::HTTP_CREATED );
      }

    }
    else
    {
      $errors = $ResetPassword->errors();
      $errors_str = $ResetPassword->errors_tostring();
      return response(['errors' => ['validationErrors' => $errors, 'validationErrorsText' => $errors_str]], Response::HTTP_UNPROCESSABLE_ENTITY);
    }

  }

  public function save_new_password(Request $request){

    $get_token=ResetPassword::where('token',$request['token'])
    ->where('status','=','PENDING')
    ->first();

    $timestamp = date("Y-m-d H:i:s");
    $update_password = User::where('user_id', $get_token->user_id)
    ->update([ 'password' => Hash::make($request['data']['password']), 'reset_status' => 'RESET', 'password_reset_date' => $timestamp ]);

    $delete = ResetPassword::where('user_id','=',$get_token->user_id)->delete();

    if($update_password){      
      return response([
        'data' => [
          'message' => 'Password was reset successfully.',
          'status'=>'success'
        ]
      ]);
    }else{
      return response([
        'data' => [
          'message' => 'Password was reset fail.',
          'status'=>'fail'
        ]
      ]);
    }

  }

  public function confirm_link(Request $request){

    $count=ResetPassword::where('token',$request['token'])
    ->where('status','=','PENDING')
    ->count('user_id');

    return response([
        'count' => $count
    ]);

  }




 }
