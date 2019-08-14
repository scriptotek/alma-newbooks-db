<?php

namespace App\Http\Controllers\Auth;

use App\ActivationService;
use App\Http\Controllers\Controller;
use Illuminate\Auth\Events\Registered;
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

    protected $activationService;

    /**
     * Where to redirect users after login / registration.
     *
     * @var string
     */
    protected $redirectTo = '/';

    /**
     * Create a new controller instance.
     */
    public function __construct(ActivationService $activationService)
    {
        $this->middleware('guest', ['except' => ['logout', 'samlLogout']]);
        $this->activationService = $activationService;
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
            'saml_error' => session()->get('saml2_error_detail', []),
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
            return redirect()->route('saml2_logout', 'uio');
        }

        return $this->logout($request);
    }

    public function register(Request $request)
    {
        $this->validator($request->all())->validate();

        event(new Registered($user = $this->create($request->all())));

        //$this->guard()->login($user);
        $this->activationService->sendActivationMail($user);

        //return redirect($this->redirectPath());
        return redirect('/login')->with('status', 'We sent you an activation code. Check your email.');
    }

    public function authenticated(Request $request, $user)
    {
        if (!$user->activated) {
            $this->activationService->sendActivationMail($user);
            auth()->logout();
            return back()->with('error', 'You need to confirm your account. We have sent you an activation code, please check your email.');
        }
        return redirect()->intended($this->redirectPath());
    }

    public function activateUser($token)
    {
        if ($user = $this->activationService->activateUser($token)) {
            auth()->login($user);
            return redirect($this->redirectPath());
        }
        abort(404);
    }
}
