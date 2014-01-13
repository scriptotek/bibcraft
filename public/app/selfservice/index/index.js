(function() {

	'use strict';

	/*******************************************************************************************
	 * SelfServiceIndexController controls the index view
	 *******************************************************************************************/

	var SelfServiceIndexController = function($scope, $location, $timeout, WebService, LogService, CartService) {

		WebService.connect('ws://labs.biblionaut.net:8080');

		function found_book(item) {
			console.log("-[index]---------------------------------------");
			console.log("Found book: " + item.id);
			CartService.add(item);
			console.log(" -> Goto cart");
			$timeout(function() {
				$location.path('/cart');
			}, 50); // to prevent Cannot read property '$$nextSibling' of null on onmessage
		}

		$scope.dummy = function() {
			console.log('Oi!');
			CartService.add({id:'039970NAA'});
			$timeout(function() {
				$location.path('/cart');
			}, 50);
		}

		function onWebSocketMessageReceived(e, msg) {

			//console.log("New Message: " + msg);

			var data = JSON.parse(msg);

			/* Example response: {
				"msg": "new-tag",
				"item": {
					"uid": "75FE9C16500104E0",
					"library": "1030310",
					"nparts": 1,
					"usage_type": "for-circulation",
					"id": "044955NA0",
					"is_blank": false,
					"crc_ok": true,
					"partno": 1,
					"country": "NO",
					"crc": "1971",
					"error": ""
				}, "rcpt": "frontend", "uid": "75FE9C16500104E0"}
			 */

			if (data.msg == 'new-tag') {
				//found_tag(data.item);

				 if (data.item.is_empty) {

					LogService.log('Fant tomt kort', 'error');

				} else if (data.item.usage_type == 'for-circulation') {

					LogService.log('Fant bok');
					$scope.$apply(found_book(data.item));

				} else if (data.item.usage_type == 'patron-card') {

					LogService.log('Fant lånekort for bruker ' + data.item.id);
					$scope.$apply($location.path('/users/' + data.item.id));

				}
			}

		}

		// controller receiving broadcast event from WebService (bubbles up to $scope from $rootScope)
		console.log("SelfServiceIndexController : setup listeners");
		var messageListener = $scope.$on("messageReceived", onWebSocketMessageReceived)

	}

	SelfServiceIndexController.$inject = ['$scope', '$location', '$timeout', 'WebService', 'LogService', 'CartService'];

	angular.module('bibcraft.selfservice', ['bibcraft.selfservice.services','bibcraft.selfservice.cart'])
	  .controller('SelfServiceIndexController', SelfServiceIndexController);

}());
