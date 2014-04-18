<!doctype html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>BIBCRAFT : {{$title}}</title>
  <!--<link rel="stylesheet" type="text/css" href="{{ URL::to('/lib/glyphicons/css/halflings.css') }}" />-->
  
  <link rel="stylesheet" href="//netdna.bootstrapcdn.com/bootstrap/3.1.0/css/bootstrap.min.css">
  <link rel="stylesheet" href="//netdna.bootstrapcdn.com/bootstrap/3.1.0/css/bootstrap-theme.min.css">

  <link rel="stylesheet" type="text/css" href="//cdnjs.cloudflare.com/ajax/libs/select2/3.4.5/select2.min.css" />
  <link rel="stylesheet" type="text/css" href="{{ URL::to('site.css') }}" />
  <link rel="stylesheet" type="text/css" href="{{ URL::to('/vendor/select2-bootstrap.css') }}" />

  <script type="text/javascript" src="//cdnjs.cloudflare.com/ajax/libs/jquery/1.10.2/jquery.min.js"></script>
  <script type="text/javascript" src="//cdnjs.cloudflare.com/ajax/libs/select2/3.4.1/select2.min.js"></script>

  <script src="//netdna.bootstrapcdn.com/bootstrap/3.1.0/js/bootstrap.min.js"></script>
  <script src="/components/list.js/dist/list.min.js"></script>

</head>
<body>

<div class="container">

  <nav class="navbar navbar-default hidden-print" role="navigation">

    <!-- Brand and toggle get grouped for better mobile display -->
    <div class="navbar-header">
      <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-ex1-collapse">
        <span class="sr-only">Toggle navigation</span>
        <span class="icon-bar"></span>
        <span class="icon-bar"></span>
        <span class="icon-bar"></span>
      </button>
      <a class="navbar-brand" href="#">Bibcraft</a>
    </div>

    <div class="collapse navbar-collapse navbar-ex1-collapse">
    @if (Auth::check())
    <!-- Navigation links -->

      <ul class="nav navbar-nav">
        <li><a href="/collections">Samlinger</a></li>
       <!-- <li><a href="/documents">Dokumenter</a></li>-->
        <li><a href="/loans">Utlån</a></li>
        <li><a href="/librarians">Bibliotekarer</a></li>
        <li><a href="/users">Brukere</a></li>
        <li><a href="/selfservice">Selvbetjening</a></li>
      </ul>

      <ul class="nav navbar-nav navbar-right">
        <li class="dropdown">
          <a href="#" class="dropdown-toggle" data-toggle="dropdown">
            Logget inn som {{ Auth::user()->name }}
            <b class="caret"></b>
          </a>
          <ul class="dropdown-menu">
            <li>
              <a href="{{ URL::action('LibrariansController@getEdit') }}">Kontoinnstillinger</a>
            </li>
            <li>
              <a href="{{ URL::action('LibrariansController@getLogout') }}">Logg ut</a>
            </li>
          </ul>
        </li>
      </ul>

      @else
      <ul class="nav navbar-nav navbar-right">
        <li><a href="{{ URL::action('LibrariansController@getLogin') }}">Logg inn</a></li>
      </ul>
      @endif

    </div>
  </nav>
  <header>
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