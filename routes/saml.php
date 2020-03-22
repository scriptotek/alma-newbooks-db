<?php

use Illuminate\Support\Facades\Route;

Route::post('saml2/acs', '\Aacotroneo\Saml2\Http\Controllers\Saml2Controller@acs');
