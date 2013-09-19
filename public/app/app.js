'use strict';

angular.module("ngLocale", [], ["$provide", function($provide) {
var PLURAL_CATEGORY = {ZERO: "zero", ONE: "one", TWO: "two", FEW: "few", MANY: "many", OTHER: "other"};
$provide.value("$locale", {"DATETIME_FORMATS":{"MONTH":["januar","februar","mars","april","mai","juni","juli","august","september","oktober","november","desember"],"SHORTMONTH":["jan.","feb.","mars","apr.","mai","juni","juli","aug.","sep.","okt.","nov.","des."],"DAY":["søndag","mandag","tirsdag","onsdag","torsdag","fredag","lørdag"],"SHORTDAY":["søn.","man.","tir.","ons.","tor.","fre.","lør."],"AMPMS":["AM","PM"],"medium":"d. MMM y HH:mm:ss","short":"dd.MM.yy HH:mm","fullDate":"EEEE d. MMMM y","longDate":"d. MMMM y","mediumDate":"d. MMM y","shortDate":"dd.MM.yy","mediumTime":"HH:mm:ss","shortTime":"HH:mm"},"NUMBER_FORMATS":{"DECIMAL_SEP":",","GROUP_SEP":" ","PATTERNS":[{"minInt":1,"minFrac":0,"macFrac":0,"posPre":"","posSuf":"","negPre":"-","negSuf":"","gSize":3,"lgSize":3,"maxFrac":3},{"minInt":1,"minFrac":2,"macFrac":0,"posPre":"\u00A4 ","posSuf":"","negPre":"\u00A4 -","negSuf":"","gSize":3,"lgSize":3,"maxFrac":2}],"CURRENCY_SYM":"kr"},"pluralCat":function (n) {  if (n == 1) {    return PLURAL_CATEGORY.ONE;  }  return PLURAL_CATEGORY.OTHER;},"id":"no"});
}]);


// Declare app level module which depends on filters, and services
angular.module('bibcraft', ['ngRoute', 'ngAnimate',
                            'bibcraft.collection',
                            'bibcraft.selfservice',
                            'bibcraft.selfservice.cart',
                            'bibcraft.selfservice.user',
                            'bibcraft.selfservice.checkout']) // 'wizard.filters', 'wizard.services', 'wizard.directives',

// Setup routes
.config(['$routeProvider', '$locationProvider', function($routeProvider, $locationProvider) {
	$routeProvider
    .when('/browse', {templateUrl: '/app/collection/browse.tpl.html', controller: 'CollectionController'})
    .when('/browse/:page', {templateUrl: '/app/collection/browse.tpl.html', controller: 'CollectionController'})
		.when('/index', {templateUrl: '/app/selfservice/index/index.tpl.html', controller: 'SelfServiceIndexController'})
		.when('/cart', {templateUrl: '/app/selfservice/cart/cart.tpl.html', controller: 'CartController'})
    .when('/checkout', {templateUrl: '/app/selfservice/checkout/checkout.tpl.html', controller: 'CheckoutController'})
    .when('/users/:user', {templateUrl: '/app/selfservice/user/user.tpl.html', controller: 'UserController'})
		.otherwise({redirectTo: '/index'});
	//$locationProvider
  	//	.html5Mode(false);
}])

.controller('AppController', ['$scope', 'LogService', function(sc, LogService) {

	sc.toggleLog = function() {
  		LogService.toggleLog();
  	};

}])

/*******************************************************************************************
 * LogService provides a central logging facility
 *******************************************************************************************/
.service('LogService', ['$rootScope', function(rs) {

  	this.connection = null;

  	this.log = function(msg, level) {
  		level = level ? level : 'info';
        console.log(level + ' : ' + msg);

        // Enough angularjs, let's have some good ol' jQuery :D
        var s = '<br />';
        if (level == 'error') {
            s += '<span style="color: red;">FEIL:</span> ';
        } else if (level == 'warn') {
            s += '<span style="color: orange;">MERK:</span> ';
        } else if (level == 'info') {
            s += '<span style="color: green;">INFO:</span> ';
        }
        $('.credit').append(s + msg);
        $('footer .container').stop().animate({ scrollTop: $("footer .container")[0].scrollHeight }, 800);

  	};

  	this.toggleLog = function() {
	    if ($('footer').css('height') == '28px') {
	        $('#wrap').css('margin', '0 auto -150px');
	        $('footer').css('height', '150px');
	    } else {
	        $('#wrap').css('margin', '0 auto -28px');
	        $('footer').css('height', '28px');
	    }
	    $('footer .container').scrollTop($("footer .container")[0].scrollHeight);
  	};

}]);
