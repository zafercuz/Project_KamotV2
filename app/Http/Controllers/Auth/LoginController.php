<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\UserLog;
use Carbon\Carbon;
use Session;

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
    protected $redirectTo = '/';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }

    /**
     * The user has been authenticated.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  mixed  $user
     * @return mixed
     */
    protected function authenticated(Request $request)
    {
        $current_date_time = Carbon::now()->toDateTimeString();
        UserLog::create([
            'hrisid' => $request['hrisid'],
            'login_at' => $current_date_time,
        ]);
        $request->session()->put('hrisid', $request['hrisid']);
        Auth::logoutOtherDevices(request('password'));
    }

    public function login(Request $request)
    {
        $this->validate($request, [
            'login'    => 'required',
            'password' => 'required',
        ]);

        $login_type = filter_var($request->input('login'), FILTER_VALIDATE_EMAIL ) 
            ? 'email' 
            : 'hrisid';

        $request->merge([
            $login_type => $request->input('login')
        ]);

        if (Auth::attempt($request->only($login_type, 'password'))) {
            $this->authenticated($request);

            return redirect()->intended($this->redirectPath());
        }

        return redirect()->back()
            ->withInput()
            ->withErrors([
                'login' => 'These credentials do not match our records.',
            ]);
    }

    public function logout(Request $request)
    {
        // ****** Set up variable to session 'HRIS ID' ****** //
        $hrisid = $request->session()->get('hrisid');

        $this->guard()->logout(); // Log outs User

        $request->session()->invalidate(); // Flush the session data and regenerate the ID.

        $request->session()->put('hrisid', $hrisid); // Put hris id to session

        return $this->loggedOut($request) ?: redirect('/');
    }

    /**
     * The user has logged out of the application.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return mixed
     */
    protected function loggedOut(Request $request)
    {
        $hrisid = $request->session()->pull('hrisid');
        $request->session()->invalidate(); // Flush the session data and regenerate the ID.
        $current_date_time = Carbon::now()->toDateTimeString();
        $query = UserLog::where('hrisid', $hrisid)
                ->latest('login_at')
                ->first()
                ->update(['logout_at' => $current_date_time]);
    }

}
