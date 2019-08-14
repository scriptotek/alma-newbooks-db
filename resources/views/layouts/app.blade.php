<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Tilvekstsdatabase - @yield('title')</title>
    <link rel="shortcut icon" href="/images/favicon.ico" >

    <!-- Styles -->
    <link href="{{ mix('/css/app.css') }}" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.13.10/css/bootstrap-select.min.css">
    <link href="/css/uio-app-top-bottom.css" type="text/css" rel="stylesheet" media="screen, print" />
    <link href="/css/uio-app-top-bottom-responsive.css" type="text/css" rel="stylesheet" media="screen and (max-width: 15.5cm) and (orientation : portrait), screen and (max-width: 17.5cm) and (orientation : landscape)"/>

    <!-- Scripts -->
    <script>
        window.Laravel = <?php 
            $lang_files = Storage::disk('resources')->files('lang/' . App::getLocale());
            $translations = [];
            foreach ($lang_files as $f) {
                $filename = pathinfo($f)['filename'];
                $translations[$filename] = trans($filename);
            }
            echo json_encode([
                'csrfToken' => csrf_token(),
                'translations' => $translations,
            ]);
        ?>

    </script>
</head>
<body>
    <div id="app">



    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
      <a class="navbar-brand" href="{{ url('/') }}">Tilvekstsdatabase for UBO</a>
      <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
      </button>

      <div class="collapse navbar-collapse" id="navbarSupportedContent">
        <ul class="navbar-nav mr-auto">
          <li class="nav-item " class="{{ Ekko::isActiveRoute('reports.*') }}">
            <a class="nav-link" href="{{ action('ReportsController@index') }}">{{ trans('reports.header') }}</a>
          </li>
          @if (Auth::user())
              <li class="nav-item " class="{{ Ekko::isActiveRoute('templates.*') }}">
                <a class="nav-link" href="{{ action('TemplatesController@index') }}">{{ trans('templates.header') }}</a>
              </li>
              <li class="nav-item " class="{{ Ekko::isActiveRoute('documents.*') }}">
                <a class="nav-link" href="{{ action('DocumentsController@index') }}">{{ trans('documents.header') }}</a>
              </li>
              <li class="nav-item " class="{{ Ekko::isActiveRoute('users.*') }}">
                <a class="nav-link" href="{{ action('UsersController@index') }}">{{ trans('users.header') }}</a>
              </li>
              <li class="nav-item " class="{{ Ekko::isActiveRoute('my-orders') }}">
                <a class="nav-link" href="{{ action('MyOrdersController@index') }}">{{ trans('my-orders.header') }}</a>
              </li>
          @endif
        </ul>

        <ul class="navbar-nav ml-auto">
            @if (Auth::guest())
                @if (config('auth.use_saml'))
                  <li class="nav-item ">
                    <a class="nav-link" href="{{ route('saml2_login', 'uio') }}">Login</a>
                  </li>
                @else
                  <li class="nav-item ">
                    <a class="nav-link" href="{{ url('/register') }}">Register</a>
                  </li>
                  <li class="nav-item ">
                    <a class="nav-link" href="{{ url('/login') }}">Login</a>
                  </li>
                @endif
            @else
              <li class="nav-item dropdown">
                <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                  {{ Auth::user()->name }} <span class="caret"></span>
                </a>
                <div class="dropdown-menu" aria-labelledby="navbarDropdown">
                  <a class="dropdown-item" href="{{ url('/logout') }}"
                     onclick="event.preventDefault(); document.getElementById('logout-form').submit();"
                  >Logout</a>
                 <form id="logout-form" action="{{ url('/logout') }}" method="POST" style="display: none;">
                    {{ csrf_field() }}
                 </form>
                </div>
              </li>
            @endif
        </ul>
      </div>
    </nav>

    @if (Session::has('status'))
    <div class="container">
        <div class="alert alert-info">
            <p>
                {{ Session::get('status') }}
            </p>
        </div>
    </div>
    @endif

    @if (Session::has('error'))
    <div class="container">
        <div class="alert alert-danger">
            <p>
                {{ Session::get('error') }}
            </p>
        </div>
    </div>
    @endif

    <div id="content">
        @yield('content')
    </div>

    <!-- Page footer starts -->
    <div id="app-footer-wrapper">
      <div id="app-footer">
        <div id="contact-info">
          <div class="phone-fax-email">
            <span class="vrtx-label">Contact information</span>
            <span class="email">E-mail: d.m.heggo@ub.uio.no</span>
            <!-- <span class="tel">Phone: 99 99 99 99</span>-->
          </div>
        </div>
        <div id="app-responsible">
          <span class="vrtx-label">Responsible for this service</span>
          <span><a href="http://ub.uio.no/">University of Oslo Library</a></span>
        </div>
      </div>
    </div>
    <!-- Page footer end -->
    </div>

    <!-- Scripts -->
    <script src="{{ mix('/js/app.js') }}"></script>
    @yield('scripts')
</body>
</html>
