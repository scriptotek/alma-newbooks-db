<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;

class Saml2Controller extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | SAML error controller
    |--------------------------------------------------------------------------
    |
    |
    */

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function error()
    {
        return view('auth.saml2.error', [
            'errors' => session()->get('saml2_error', []),
        ]);
    }
}
