<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">

  <!-- CSRF Token -->
  <meta name="csrf-token" content="{{ csrf_token() }}">

  <title>DTR Control Panel</title>

  <!-- Scripts -->

  <!-- Fonts -->
  <link rel="dns-prefetch" href="//fonts.gstatic.com">
  <link href="https://fonts.googleapis.com/css?family=Open+Sans:400,700&display=swap" rel="stylesheet">

  <!-- Styles -->
  <link rel="shortcut icon" type="image/x-icon" href="{{ asset('images/clock.ico') }}" />
  <link rel="stylesheet" href="{{ asset('css/bootstrap.min.css') }}">
  <link rel="stylesheet" href="{{ asset('css/gridSystem.css') }}">
  <link rel="stylesheet" href="{{ asset('css/dataTables.bootstrap4.min.css') }}">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
</head>

<body class="bg-light">
  <!------------------------------------------------------------------------>
  <!------------------------ HEADER & NAV CONTAINER ------------------------>
  <!------------------------------------------------------------------------>

  <!-- Navigation Section -->
  <nav class="navbar navbar-expand-lg navbar-light default-primary-color shadow-lg">
    <div class="container">
      <a class="navbar-brand" href="/"><img src="images/Clock.ico" width="45" height="45"
          class="d-inline-block align-top" alt=""></a>
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
  <main>
    @yield('content')
  </main>

  <footer>
  <div class="container pt-4 pb-5" style="margin-top:100px;">
    <div class="row text-center mt-3">
      <div class="col">
        <img src="{{ asset('images/^AB131B9BFFCE272C922BFD7FA1DFB26C48FCDB5C789CD78658^pimgpsh_fullsize_distr (2).png') }}"
          style="max-height: 75%;max-width: 75%;">
      </div>
      <div class="col">
        <img src="{{ asset('images/bmg_logo.png') }}" style="max-height: 75%;max-width: 75%;">
      </div>
      <div class="col">
        <img src="{{ asset('images/MAPI LOGO.png') }}" style="max-height: 75%;max-width: 75%;">
      </div>
    </div>
  </div>
  </footer>

  <script type="text/javascript" src="{{ asset('js/jquery.min.js') }}"></script>
  <script type="text/javascript" src="{{ asset('js/popper.min.js') }}"></script>
  <script type="text/javascript" src="{{ asset('js/bootstrap.min.js') }}"></script>
  <script type="text/javascript" src="{{ asset('js/jquery.dataTables.min.js') }}"></script>
  <script type="text/javascript" src="{{ asset('js/dataTables.bootstrap4.min.js') }}"></script>
  <script type="text/javascript" src="{{ asset('js/moment.min.js') }}"></script>
  <script type="text/javascript" src="{{ asset('js/app.js') }}"></script>

</body>

</html>