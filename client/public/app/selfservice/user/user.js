(function() {

	'use strict';

	/*******************************************************************************************
	 * UserController controls the user view
	 *******************************************************************************************/

	var UserController = function($scope, $location, $routeParams, $http, $filter, $locale, LogService) {

		$scope.user = { name: 'du ukjente', loans: [] };

		if ($routeParams.user === undefined) {
			LogService.log('Ingen bruker angitt', 'error');
			return;
		}

		var user = parseInt($routeParams.user, 10);

		LogService.log('Viser bruker ' + user);

		$http.get('/users/show/' + user)
			.success(function (response) {
				$filter('postProcessUserResponse')(response);
				$scope.user = response;
			})
			.error(function(response) {
				LogService.log('Http request failed!', 'error');
			});

	};

	UserController.$inject = ['$scope', '$location', '$routeParams', '$http', '$filter', '$locale', 'LogService'];

	angular.module('bibcraft.selfservice.user', ['ngLocale', 'ngAnimate', 'bibcraft.selfservice.services'])
	  .controller('UserController', UserController)
	  .filter('postProcessUserResponse', function() {
		return function(input) {
			for (var i = input.loans.length - 1; i >= 0; i--) {
				var dt = new Date(input.loans[i].created_at);
				input.loans[i].created_at = dt.getTime();

				dt = new Date(input.loans[i].due_at);
				input.loans[i].due_at = dt.getTime();
			};
			return input;
		}
	  });

}());
