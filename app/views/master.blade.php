<!doctype html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Utvalgte e-bøker : {{$title}}</title>
  <!--<link rel="stylesheet" type="text/css" href="{{ URL::to('/lib/glyphicons/css/halflings.css') }}" />-->
  <link rel="stylesheet" type="text/css" href="{{ URL::to('/lib/bootstrap/css/bootstrap.min.css') }}" />
  <link rel="stylesheet" type="text/css" href="{{ URL::to('site.css') }}" />
  <script type="text/javascript" src="{{ URL::to('/lib/jquery-1.10.1.min.js') }}"></script>
  <script type="text/javascript" src="{{ URL::to('/lib/bootstrap/js/bootstrap.min.js') }}"></script>
</head>
<body>
  <div class="container-narrow">
    <div class="main">
    <div class="inner">
      
      <header>
        @yield('header')
      </header>

      @yield('container')
      
    <div>
    <div>
  </div>
  @yield('scripts')
</body>
</html>