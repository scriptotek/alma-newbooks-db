<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;

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
     * Where to redirect users after login / registration.
     *
     * @var string
     */
    protected $redirectTo = '/';

    /**
     * Create a new controller instance.
     */
    public function __construct()
    {
        $this->middleware('guest', ['except' => 'samlLogout']);
    }

    /**
     * Display SAML errors.
     *
     * @return \Illuminate\Http\Response
     */
    public function error()
    {
        return view('auth.error', [
            'errors' => session()->get('saml2_error', []),
        ]);
    }

    /**
     * Log the user out of the application.
     *
     * @param  Request  $request
     * @return \Illuminate\Http\Response
     */
    public function samlLogout(Request $request)
    {
        $user = \Auth::user();

        if (!is_null($user->saml_session)) {
            return \Saml2::logout('/', $user->saml_id, $user->saml_session);
        }

        return $this->logout($request);
    }
}
