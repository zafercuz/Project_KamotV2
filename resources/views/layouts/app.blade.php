<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">

  <!-- CSRF Token -->
  <meta name="csrf-token" content="{{ csrf_token() }}">

  <title>DTR Portal</title>

  <!-- Scripts -->

  <!-- Fonts -->
  <link rel="dns-prefetch" href="//fonts.gstatic.com">
  <link href="https://fonts.googleapis.com/css?family=Open+Sans:400,700&display=swap" rel="stylesheet">

  <!-- Styles -->
  <link rel="shortcut icon" type="image/x-icon" href="{{ asset('images/Clock.ico') }}" />
  <link rel="stylesheet" href="{{ asset('css/bootstrap.min.css') }}">
  <link rel="stylesheet" href="{{ asset('css/gridSystem.css') }}">
  <link rel="stylesheet" href="{{ asset('css/dataTables.bootstrap4.min.css') }}">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
</head>

<body class="bg-light">
  <div class="d-flex flex-column sticky-footer-wrapper">
    <!------------------------------------------------------------------------>
    <!------------------------ HEADER & NAV CONTAINER ------------------------>
    <!------------------------------------------------------------------------>

    <!-- Navigation Section -->
    <nav class="navbar navbar-expand-lg navbar-light default-primary-color shadow-lg">
      <div class="container">
        <a class="navbar-brand" href="/"><img src="images/Clock.ico" width="45" height="45"
          class="d-inline-block align-top" alt=""></a>

        <div class="collapse navbar-collapse" id="navbarSupportedContent">
          <!-- Left Side Of Navbar -->
          <ul class="navbar-nav mr-auto">

          </ul>

          <!-- Right Side Of Navbar -->
          <ul class="navbar-nav ml-auto">
            <!-- Authentication Links -->
            @guest
            <li class="nav-item">
              <a class="nav-link" href="{{ route('login') }}">{{ __('Login') }}</a>
            </li>
            @if (Route::has('register'))
            <li class="nav-item">
              <a class="nav-link" href="{{ route('register') }}">{{ __('Register') }}</a>
            </li>
            @endif
            @else
            <li class="nav-item dropdown">
              <a id="navbarDropdown" class="nav-link dropdown-toggle" href="#" role="button" data-toggle="dropdown"
                aria-haspopup="true" aria-expanded="false" v-pre>
                {{ Auth::user()->name }} <span class="caret"></span>
              </a>

              <div class="dropdown-menu" aria-labelledby="navbarDropdown">
                <a class="dropdown-item" href="{{ route('logout') }}" onclick="event.preventDefault();
                                                     document.getElementById('logout-form').submit();">
                  {{ __('Logout') }}
                </a>

                <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                  @csrf
                </form>
              </div>
            </li>
            @endguest
          </ul>
        </div>
      </div>
    </nav>

    <!-- Top section -->
    <header>
      <div class="container text-center mt-4">
        <h1 class="display-4" id="headerDisplay">Daily Time Record</h2>
      </div>
    </header>


    <!---------------------------------------------------------------->
    <!------------------------ BODY CONTAINER ------------------------>
    <!---------------------------------------------------------------->
    <main class="flex-fill">
      @yield('content')
    </main>

    <footer>
      <div class="container-fluid w-100">
        <div class="row text-center">
          <div class="col">
            <img
              src="{{ asset('images/^AB131B9BFFCE272C922BFD7FA1DFB26C48FCDB5C789CD78658^pimgpsh_fullsize_distr (2).png') }}"
              style="max-height: 70%;max-width: 70%;">
          </div>
          <div class="col">
            <img src="{{ asset('images/bmg_logo.png') }}" style="max-height: 70%;max-width: 70%;">
          </div>
          <div class="col">
            <img src="{{ asset('images/MAPI LOGO-min.png') }}" style="max-height: 80%;max-width: 80%;">
          </div>
        </div>
      </div>
    </footer>

  </div>

  <script type="text/javascript" src="{{ asset('js/jquery.min.js') }}"></script>
  <script type="text/javascript" src="{{ asset('js/popper.min.js') }}"></script>
  <script type="text/javascript" src="{{ asset('js/bootstrap.min.js') }}"></script>
  <script type="text/javascript" src="{{ asset('js/jquery.dataTables.min.js') }}"></script>
  <script type="text/javascript" src="{{ asset('js/dataTables.bootstrap4.min.js') }}"></script>
  <script type="text/javascript" src="{{ asset('js/moment.min.js') }}"></script>
  <script type="text/javascript" src="{{ asset('js/app.js') }}"></script>

</body>

</html>