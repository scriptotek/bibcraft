<!doctype html>
<html lang="nb" ng-app="bibcraft">
<head>
  <meta charset="utf-8" />
  <title>BIBCRAFT - Selvbetjening</title>
  <base href="/selfservice">
  <link href='//fonts.googleapis.com/css?family=Noto+Sans|Share+Tech+Mono' rel='stylesheet' type='text/css'>
  <link href="/vendor/bootstrap/css/bootstrap.min.css" media="screen" type="text/css" rel="stylesheet" />
  <link href="/app/selfservice/selfservice.css" type="text/css" rel="stylesheet" />
</head>
<body>

  <div class="view" ng-view></div>

  <footer ng-controller="AppController" ng-click="toggleLog()">
    <div class="container">
      <p class="muted credit">
        &copy; Universitetsbiblioteket i Oslo 2013
      </p>
    </div>
  </footer>

  <audio>
    <source src="assets/robot-blip.mp3" type="audio/mpeg" />
  </audio>
  <audio>
    <source src="assets/blipp2.mp3" type="audio/mpeg" />
  </audio>
  <audio id="sfx-click">
    <source src="assets/rapid_beep3.mp3" type="audio/mpeg" />
  </audio>
  <audio id="sfx-error">
    <source src="assets/error.mp3" type="audio/mpeg" />
  </audio>

  <script src="//cdnjs.cloudflare.com/ajax/libs/jquery/1.10.2/jquery.min.js"></script>
  <script src="/vendor/bootstrap/js/bootstrap.min.js"></script>
  <link href="//netdna.bootstrapcdn.com/font-awesome/3.2.1/css/font-awesome.css" rel="stylesheet">

  <script src="//ajax.googleapis.com/ajax/libs/angularjs/1.2.0rc1/angular.min.js"></script>
  <script src="//ajax.googleapis.com/ajax/libs/angularjs/1.2.0rc1/angular-route.min.js"></script>
  <script src="//ajax.googleapis.com/ajax/libs/angularjs/1.2.0rc1/angular-touch.min.js"></script>

  <script src="/app/app.js"></script>
  <script src="/app/selfservice/services.js"></script>
  <script src="/app/selfservice/index/controller.js"></script>
  <script src="/app/selfservice/cart/controller.js"></script>
  <script src="/app/collection/controller.js"></script>

  <script type='text/javascript'>
    $(document).ready(function () {
      $('header h1').on('click', function () {

        found_book('051335638');

      });

      function mppi() {
/*        $('#blinkingblock').toggle();
        $('header').fadeOut('fast', function() {
          $('header').fadeIn();
          setTimeout(mppi, 2000)

        });*/
        $('#slide1').css(' ','123px')
      }
      setTimeout(mppi,300);

      $('body').on('dragstart', function (e) {
        event.preventDefault();
      });

      $('body').on('click touchend', function (e) {
        $('#sfx-click')[0].play();
      });

      $('body').on('mousedown', function (e) {
        /*if ($(e.target).is('input')) {
          // pass
        } else {
          e.preventDefault();
        }*/
        /*console.log(e.target);
        if ($(e.target).is('a') || $(e.target).is('input') || $(e.target).is('button') || $(e.target).is('label')) {
          // pass
        } else {
          e.preventDefault();
        }*/
        //e.stopPropagation();
      });

      $('body').on('mousemove', function (e) {
        //e.preventDefault();
        //e.stopPropagation();
      });

      $('body').on('click', function (e) {
        //e.preventDefault();
        //e.stopPropagation();
      });

      $('body').on('mouseup', function (e) {
        //e.preventDefault();
        //e.stopPropagation();
      });

    });

  </script>

</body> 
</html>
