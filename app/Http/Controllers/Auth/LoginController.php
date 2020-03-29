<?php

namespace App\Http\Controllers\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Cookie;
use Session;
use App\UsrLocMap;
use App\UsrProfile;
use App\OrgLocation;


//use Html;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = '/home';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
//        $this->middleware('guest')->except('logout');
    }


    public function showLogin()
    {
        // show the form
        return view('login');
    }

    public function doLogin(Request $request)
    {
        // validate the info, create rules for the inputs
        $rules = array(
      //   'user-name'    => 'required|email', // make sure the email is an actual email
            'password' => 'required|alphaNum|min:3' // password can only be alphanumeric and has to be greater than 3 characters
        );

        // run the validation rules on the inputs from the form
        $validator = Validator::make(Input::all(), $rules);

        // if the validator fails, redirect back to the form
        if ($validator->fails()) {
            return Redirect::to('/')
                ->withErrors($validator) // send back all errors to the login form
                ->withInput(Input::except('password')); // send back the input (not the password) so that we can repopulate the form
        } else {

            // create our user data for the authentication
            $userdata = array(
                'user_name' 	=> Input::get('user-name'),
                'password' 	=> Input::get('password')
            );

            $remember = (Input::has('remember')) ? true : false;
//            print_r(Auth::attempt($userdata,$remember));exit;
            // attempt to do the login
            if (Auth::attempt($userdata,$remember)) {
                $userAuth = Auth::User();

                if($remember){
                    Cookie::queue("user-name", Input::get('user-name'), 3600);
                    Cookie::queue("password", Input::get('password'), 3600);
                }else{
                    Cookie::queue(Cookie::forget('user-name'));
                    Cookie::queue(Cookie::forget('password'));
                }

                $user = UsrProfile::find($userAuth->user_id);

                $userLoc = UsrLocMap::where('user_id',$userAuth->user_id)->count();
                $request->session()->put('user', $user);
                if($userLoc>1 ){
                   // $userLoc = UsrLocMap::where('user_id',$userAuth->user_id)->get();
                    return Redirect::to('/select-location');

                }else{
                    return Redirect::to('/home');
                }


            } else {
                // validation not successful, send back to form
                $request->session()->flash('loginError', trans('auth.failed'));
                return Redirect::to('/');

            }

        }
    }

    public function selectLocation(Request $request) {

        $user = $request->session()->get('user');
        $userLoc = UsrLocMap::where('user_id',$user->user_id)->get();
        $locationMain=array();
        foreach ($userLoc AS $loc ){
            $location = OrgLocation::find($loc->loc_code);
            $locationMain[$loc->loc_code]=$location->loc_name;
        }
        return view('selectLocation', ['loc' =>$locationMain]);
    }

    public function loginWithLoc(Request $request) {
        $request->session()->get('user')->loc_code=10;
        return Redirect::to('/home');
    }


    public function logout(Request $request) {
        Auth::logout();
        $request->session()->flush();
        return redirect('/');
    }

}
