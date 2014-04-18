<!doctype html>
<html lang="nb" ng-app="bibcraft">
<head>
  <meta charset="utf-8" />
  <title>BIBCRAFT - Selvbetjening</title>
  <base href="/selfservice">
  <link href='//fonts.googleapis.com/css?family=Noto+Sans|Share+Tech+Mono' rel='stylesheet' type='text/css'>
  <link href='//fonts.googleapis.com/css?family=Noto+Sans' rel='stylesheet' type='text/css'>

  <link href="/vendor/bootstrap/css/bootstrap.min.css" media="screen" type="text/css" rel="stylesheet" />
  <link href="/components/font-awesome/css/font-awesome.min.css" media="screen" type="text/css" rel="stylesheet" />
  <link href="/app/selfservice/selfservice.css" type="text/css" rel="stylesheet" />
  <link rel="stylesheet" href="/components/animate.css/animate.css">
</head>
<body ng-cloak ng-controller="AppController">

  <div class="container" ng-class="{blurred: modalVisible}">

    <div class="centralnotice" ng-show="network_error"><div style="padding:20px;">
      Nettverksforbindelsen er ustabil. Bibcraft fortsetter å prøve å få kontakt
    </div></div>
<!--
    <h2 style="padding-left:20px;">
      BibCraft : Realfagsbibliotekets eksperimentelle og mobile utlånssystem
    </h2>
-->
    <div class="view" ng-view></div>

    <footer ng-controller="AppController" ng-click="toggleLog()">
      <div class="container">
        <p class="muted credit">
          &copy; Universitetsbiblioteket i Oslo 2013
        </p>
      </div>
    </footer>

  </div>

  <div class="modal2"
    ng-controller="ModalController"
    ng-include="template"
    ng-click="close()"
  ></div>

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

  <script src="/components/jquery/dist/jquery.js"></script>
  <script src="/vendor/bootstrap/js/bootstrap.min.js"></script>

  <link href="/components/font-awesome/css/font-awesome.min.css" rel="stylesheet">

  <script src="//ajax.googleapis.com/ajax/libs/angularjs/1.2.8/angular.min.js"></script>
  <script src="//ajax.googleapis.com/ajax/libs/angularjs/1.2.8/angular-route.min.js"></script>
  <script src="//ajax.googleapis.com/ajax/libs/angularjs/1.2.8/angular-touch.min.js"></script>
  <script src="//ajax.googleapis.com/ajax/libs/angularjs/1.2.8/angular-animate.min.js"></script>

  <script src="/app/app.js"></script>
  <script src="/app/collection/collection.js"></script>
  <script src="/app/selfservice/services.js"></script>
  <script src="/app/selfservice/index/index.js"></script>
  <script src="/app/selfservice/cart/cart.js"></script>
  <script src="/app/selfservice/checkout/checkout.js"></script>
  <script src="/app/selfservice/user/user.js"></script>

  <!--
  <script type='text/javascript'>

      $('body').on('dragstart', function (e) {
        event.preventDefault();
      });

      $('body').on('click touchend', function (e) {
        $('#sfx-click')[0].play();
      });

    });

  </script>
  -->

</body>
</html>
