<!doctype html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>BIBCRAFT : {{$title}}</title>
  <!--<link rel="stylesheet" type="text/css" href="{{ URL::to('/lib/glyphicons/css/halflings.css') }}" />-->
  <link rel="stylesheet" type="text/css" href="//cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/2.3.2/css/bootstrap.min.css" />
  <link rel="stylesheet" type="text/css" href="//cdnjs.cloudflare.com/ajax/libs/select2/3.4.1/select2.min.css" />
  <link rel="stylesheet" type="text/css" href="{{ URL::to('site.css') }}" />
  <link rel="stylesheet" type="text/css" href="{{ URL::to('/vendor/select2-bootstrap.css') }}" />

  <script type="text/javascript" src="//cdnjs.cloudflare.com/ajax/libs/jquery/1.10.2/jquery.min.js"></script>
  <script type="text/javascript" src="//cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/2.3.2/js/bootstrap.min.js"></script>
  <script type="text/javascript" src="//cdnjs.cloudflare.com/ajax/libs/select2/3.4.1/select2.min.js"></script>
</head>
<body>

<div class="navbar">
  <div class="navbar-inner">
    <a class="brand" href="#">Bibcraft</a>
    <ul class="nav">
      <li><a href="/documents">Dokumenter</a></li>
      <li><a href="/collections">Samlinger</a></li>
      <li><a href="/users">Brukere</a></li>
      <li><a href="/selfservice">Selvbetjening</a></li>
    </ul>
  </div>
</div>

  <div class="container">
    <div class="main">
    <div class="inner">

      <header>
          <h1>Bibcraft</h1>


            @if (!empty($status))
              <div class="alert alert-info" style="display:none;">
                <button type="button" class="close" data-dismiss="alert">&times;</button>
                {{$status}}
              </div>
            @endif

            @if ($e = $errors->all('<li>:message</li>'))
              <div class="alert alert-danger" style="display:none;">
                <button type="button" class="close" data-dismiss="alert">&times;</button>
                Kunne ikke lagre fordi:
                <ul>
                @foreach ($e as $msg)
                  {{$msg}}
                @endforeach
                </ul>
              </div>
            @endif

        @yield('header')

      </header>

      @yield('container')

    <div>
    <div>
  </div>
  @yield('scripts')

  <script type="text/javascript">

    $(document).ready(function() {

      if ($('.alert').length != 0) {
        $('.alert').hide().slideDown();
      }

      //parent.postMessage("Hello","*");

    });
  </script>

</body>
</html>