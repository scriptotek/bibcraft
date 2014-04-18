<!DOCTYPE html>
<html lang="nb">
<head>
  <title>BIBCRAFT</title>

  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0">

  <!-- Complete CSS (Responsive, With Icons) -->
  <link rel="stylesheet" href="//netdna.bootstrapcdn.com/bootstrap/3.1.0/css/bootstrap.min.css">

  <!-- Optional theme -->
  <link rel="stylesheet" href="//netdna.bootstrapcdn.com/bootstrap/3.1.0/css/bootstrap-theme.min.css">

  <link rel="stylesheet" type="text/css" href="/site.css">
  <link href='//fonts.googleapis.com/css?family=Open+Sans&subset=latin,latin-ext' rel='stylesheet' type='text/css'>
  <link rel="stylesheet" type="text/css" href="/halflings.css">
  <style type="text/css">
  html,body {
    height: 100%;
  }
  body {
    /* Thanks to http://www.colorzilla.com/gradient-editor/ :) */

background: rgb(210,255,82); /* Old browsers */
background: -moz-linear-gradient(-45deg, rgba(210,255,82,1) 0%, rgba(145,232,66,1) 100%); /* FF3.6+ */
background: -webkit-gradient(linear, left top, right bottom, color-stop(0%,rgba(210,255,82,1)), color-stop(100%,rgba(145,232,66,1))); /* Chrome,Safari4+ */
background: -webkit-linear-gradient(-45deg, rgba(210,255,82,1) 0%,rgba(145,232,66,1) 100%); /* Chrome10+,Safari5.1+ */
background: -o-linear-gradient(-45deg, rgba(210,255,82,1) 0%,rgba(145,232,66,1) 100%); /* Opera 11.10+ */
background: -ms-linear-gradient(-45deg, rgba(210,255,82,1) 0%,rgba(145,232,66,1) 100%); /* IE10+ */
background: linear-gradient(135deg, rgba(210,255,82,1) 0%,rgba(145,232,66,1) 100%); /* W3C */
filter: progid:DXImageTransform.Microsoft.gradient( startColorstr='#d2ff52', endColorstr='#91e842',GradientType=1 ); /* IE6-9 fallback on horizontal gradient */


}
.well {
  display: inline-block;
  vertical-align: middle;
  width: 300px;
}

.block {
  text-align: center;
  height: 100%;
}

/* The ghost, nudged to maintain perfect centering */
.block:before {
  content: '';
  display: inline-block;
  height: 100%;
  vertical-align: middle;
  margin-right: -0.25em; /* Adjusts for spacing */
}

input {
    margin: 1em 0;
}

  </style>
</head>
<body>

    <div class="block">

        <div class="well">
            <i class="halflings-icon lock"></i>
            <p>
              Logg inn til BibCraft
            </p>
            @if (Session::has('status'))
            <p style="color:#228822;">
              {{ $status }}
            </p>
            @endif
            @if (Session::has('loginfailed'))
            <p style="color:red;">
              Brukernavnet eller passordet var feil.
            </p>
            @endif
            <form method="POST" action="/librarians/login">
              <div class="form-group">
                <label class="sr-only" for="username">Brukernavn</label>
                <input type="username" id="username" name="username" class="form-control" placeholder="Epost" value="{{ Input::old('username') }}">
              </div>
              <div class="form-group">
                <label class="sr-only" for="password">Passord</label>
                <input type="password" id="password" name="password" class="form-control" placeholder="Passord">
              </div>
              <button type="submit" class="btn btn-success btn-lg">Logg inn</button
            </form>
        </div>

    </div>


  <script type="text/javascript" src="//cdnjs.cloudflare.com/ajax/libs/jquery/1.10.1/jquery.min.js"></script>
  <script type="text/javascript" src="/components/bootstrap/dist/js/bootstrap.min.js"></script>
  <script type="text/javascript">

    $(document).ready(function() {

    });
  </script>

</body>
</html>
