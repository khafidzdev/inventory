<!doctype html>
<html lang="en">
  <head>
    <base href="{{ url('/') }}/">
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">
    <link rel="icon" href="{{ asset('favicon.ico') }}">
    <title>@yield('title')</title>
    <link rel="stylesheet" href="{{ asset('css/simplebar.css') }}">
    <link href="https://fonts.googleapis.com/css2?family=Overpass:ital,wght@0,100;0,200;0,300;0,400;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/feather.css') }}">
    <link rel="stylesheet" href="{{ asset('css/daterangepicker.css') }}">
    <link rel="stylesheet" href="{{ asset('css/app-light.css') }}" id="lightTheme">
    <link rel="stylesheet" href="{{ asset('css/app-dark.css') }}" id="darkTheme" disabled>
    <style>
      html, body {
        height: 100vh;
        width: 100vw;
        overflow: hidden !important;
        position: fixed;
        top: 0;
        left: 0;
        margin: 0;
        padding: 0;
      }
      .wrapper {
        height: 100vh;
        width: 100vw;
        overflow: hidden !important;
      }
    </style>
  </head>
  <body class="light overflow-hidden">
    <div class="wrapper vh-100 overflow-hidden">
      <div class="row align-items-center h-100 m-0">
        @yield('content')
      </div>
    </div>
    <script src="{{ asset('js/jquery.min.js') }}"></script>
    <script src="{{ asset('js/popper.min.js') }}"></script>
    <script src="{{ asset('js/moment.min.js') }}"></script>
    <script src="{{ asset('js/bootstrap.min.js') }}"></script>
    <script src="{{ asset('js/simplebar.min.js') }}"></script>
    <script src="{{ asset('js/daterangepicker.js') }}"></script>
    <script src="{{ asset('js/jquery.stickOnScroll.js') }}"></script>
    <script src="{{ asset('js/tinycolor-min.js') }}"></script>
    <script src="{{ asset('js/config.js') }}"></script>
    <script src="{{ asset('js/apps.js') }}"></script>
    <script async src="https://www.googletagmanager.com/gtag/js?id=UA-56159088-1"></script>
    <script>
      window.dataLayer = window.dataLayer || [];

      function gtag()
      {
        dataLayer.push(arguments);
      }
      gtag('js', new Date());
      gtag('config', 'UA-56159088-1');
    </script>
  </body>
</html>
