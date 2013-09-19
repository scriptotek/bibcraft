(function() {

	'use strict';

	/*******************************************************************************************
	 * CartController controls the cart view
	 *******************************************************************************************/

	var CartController = function($scope, $location, WebService, LogService, CartService) {

		WebService.connect('ws://labs.biblionaut.net:8080');

		function found_book(item) {
			console.log("-[cart]---------------------------------------");
			console.log("Found book: " + item.id);
			CartService.add(item);
		}

		function onWebSocketMessageReceived(e, msg) {

			console.log("New Message: " + msg);

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
				if (data.item.usage_type == 'for-circulation') {

					$scope.$apply(found_book(data.item));

				}
			}

		}

		function onCartChanged(e) {

			$scope.books = CartService.contents;

			console.log('> on Cart changed, cart contains ' + $scope.books.length + ' items');

			if ($scope.books.length > 0) {
				$('#borrowing-cart .item:last').slideDown('fast', function() {
					var $t = $('#borrowing-cart');
					$t.animate({"scrollTop": $('#borrowing-cart')[0].scrollHeight}, "slow");
				});
			}
		}

		$scope.continue = function() {
			$location.path('/checkout');
		}

		$scope.cancel = function() {
			CartService.clear();
			$location.path('/');
		}
		// controller receiving broadcast event from WebService (bubbles up to $scope from $rootScope)

		var messageListener = $scope.$on("messageReceived", onWebSocketMessageReceived),
			cartChangedListener = $scope.$on("cartChanged", onCartChanged);

		// In case there is already something in the cart
		onCartChanged();

		console.log("CartController initialized");

	};

	/*******************************************************************************************
	 * CartService keeps books until checkout is complete
	 *******************************************************************************************/

	var CartService = function($rootScope, $q, $http, LogService) {

		// Keeps the contents of the cart
		this.contents = [];

		// Clears the cart
		this.clear = function() {
			this.contents = [];
		};

		// Check if an item is present in cart
		this.has = function(id) {
			for (var i = this.contents.length - 1; i >= 0; i--) {
				if (this.contents[i].cardData.id == id) return true;
			};
			return false;
		};

		// Add an item using RFID card data
		this.add = function(cardData) {
			var that = this;
			if (this.has(cardData.id)) {
				console.log('Already in cart: ' + cardData.id);
				return;
			}
			console.log('Added to cart : ' + cardData.id);
			this.fetchMetadata(cardData.id).then(function(d) {
				console.log('Got some metadata back');
				console.log(d);
				if (d.error) {

					LogService.log('Boka ble ikke funnet i BibCraft-systemet: ' + d.error, 'error');

				} else if (d.loans.length !== 0) {

					LogService.log('Boka er allerede utlånt!', 'error');

					// TODO: Gå til ny skjerm og vis utlånsfrist :)

				} else {

					that.contents.push({
						cardData: cardData,
						catalogueData: d
					});

					// Broadcast an event
					console.log(':: Broadcast : Cart changed')
					$rootScope.$broadcast('cartChanged');

				}

			});

		};

		// Fetches metadata for an item
		this.fetchMetadata = function(dokid) {
			console.log('Fetching metadata for : ' + dokid);

			var promise = $http.get('/documents/show/' + dokid).then(function (response) {
				// The then function here is an opportunity to modify the response
				//console.log('got response');
				//console.log(response);
				return response.data;
			}, function(response) {
				console.log('request failed!');
				LogService.log('Http request failed!', 'error');
				return $q.reject(response);
			});

			return promise;
		};

		console.log('Created CartService');

	};

	CartController.$inject = ['$scope', '$location', 'WebService', 'LogService', 'CartService'];
	CartService.$inject = ['$rootScope', '$q', '$http', 'LogService'];

	angular.module('bibcraft.selfservice.cart', ['bibcraft.selfservice.services', 'bibcraft.selfservice.user'])
	  .controller('CartController', CartController)
	  .service('CartService', CartService);

}());
