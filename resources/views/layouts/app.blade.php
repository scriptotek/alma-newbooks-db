<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Tilvekstsdatabase - @yield('title')</title>

    <!-- Styles -->
    <link href="/css/app.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.11.0/css/bootstrap-select.min.css">

    <link href="/css/uio-app-top-bottom.css" type="text/css" rel="stylesheet" media="screen, print" />
    <link href="/css/uio-app-top-bottom-responsive.css" type="text/css" rel="stylesheet" media="screen and (max-width: 15.5cm) and (orientation : portrait), screen and (max-width: 17.5cm) and (orientation : landscape)"/>

    <!-- Scripts -->
    <script>
        window.Laravel = <?php echo json_encode([
            'csrfToken' => csrf_token(),
        ]); ?>
    </script>
</head>
<body>
    <nav class="navbar navbar-inverse navbar-static-top">
        <div class="container">
            <div class="navbar-header">

                <!-- Collapsed Hamburger -->
                <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#app-navbar-collapse">
                    <span class="sr-only">Toggle Navigation</span>
                    <span class="icon-bar"></span>

                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                </button>

                <!-- Branding Image -->
                <a class="navbar-brand" href="{{ url('/') }}">
                    <!--<img src="/images/uio-app-small-black-eng-responsive.png" alt="">-->
                    Tilvekstsdatabase for UBO
                </a>
            </div>

            <div class="collapse navbar-collapse" id="app-navbar-collapse">
                <!-- Left Side Of Navbar -->
                <ul class="nav navbar-nav">
                    <li class="{{ Ekko::isActiveRoute('docs.*') }}"><a href="{{ action('DocumentsController@index') }}">{{ trans('documents.header') }}</a></li>
                    <li class="{{ Ekko::isActiveRoute('reports.*') }}"><a href="{{ action('ReportsController@index') }}">{{ trans('reports.header') }}</a></li>                    &nbsp;
                    <li class="{{ Ekko::isActiveRoute('users.*') }}"><a href="{{ action('UsersController@index') }}">{{ trans('users.header') }}</a></li>                    &nbsp;
                </ul>

                <!-- Right Side Of Navbar -->
                <ul class="nav navbar-nav navbar-right">
                    <!-- Authentication Links -->
                    @if (Auth::guest())
                        <li><a href="{{ url('/saml2/login') }}">Login</a></li>
                    @else
                        <li class="dropdown">
                            <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false">
                                {{ Auth::user()->name }} <span class="caret"></span>
                            </a>

                            <ul class="dropdown-menu" role="menu">
                                <li>
                                    <a href="{{ url('/logout') }}"
                                        onclick="event.preventDefault();
                                                 document.getElementById('logout-form').submit();">
                                        Logout
                                    </a>

                                    <form id="logout-form" action="{{ url('/logout') }}" method="POST" style="display: none;">
                                        {{ csrf_field() }}
                                    </form>
                                </li>
                            </ul>
                        </li>
                    @endif
                </ul>
            </div>
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
            <span class="email">E-mail: someone@ub.uio.no</span>
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


    <!-- Scripts -->
    <script src="/js/app.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.11.0/js/bootstrap-select.min.js"></script>
<!--    <script src="https://cdnjs.cloudflare.com/ajax/libs/ace/1.2.3/ace.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/ace/1.2.3/mode-mysql.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/ace/1.2.3/mode-html.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/ace/1.2.3/theme-iplastic.js"></script>
    -->
    @yield('scripts')
</body>
</html>
