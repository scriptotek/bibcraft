(function() {

	'use strict';

	/*******************************************************************************************
	 * CheckoutController controls the user identification and checkout view
	 *******************************************************************************************/

	var CheckoutController = function($scope, $location, LogService, WebService, CartService, HttpService) {

		var user_id = -1;

		$scope.newuser = true;
		$scope.activationMessage = '';
		$scope.data = { phone: '', name: '', code: '' };
		$scope.step = 'start';

		$scope.tast = function(siffer) {
			if ($scope.step == 'start') {
				$scope.data.phone += siffer;
			} else if ($scope.step == 'confirm') {
				$scope.data.code += siffer;
			}
		};

		$scope.slettTegn = function() {
			if ($scope.step == 'start') {
				$scope.data.phone = $scope.data.phone.substr(0, $scope.data.phone.length-1);
			} else if ($scope.step == 'confirm') {
				$scope.data.code = $scope.data.code.substr(0, $scope.data.code.length-1);
			}
		};

		$scope.nextStep = function() {
			if ($scope.step == 'start') {
				$scope.step = 'wait';
				lookupUser($scope.data.phone)
			} else if ($scope.step == 'confirm') {
				checkActivationCode($scope.data);
			}
		};

		$scope.changePhoneNo = function() {
			$scope.step = 'start';
		};

		$scope.myloans = function (){
			$location.path('/users/' + user_id);
		};

		$scope.home = function (){
			$location.path('/');
		};

		/**
		 * Sjekker om det finnes en bruker med et gitt telefonnummer
		 */
		function lookupUser(tlfnr) {

			LogService.log('Sjekker om ' + tlfnr + ' allerede er registrert');

			HttpService.neverEverGiveUp({ method: 'GET', url: '/users/show?phone=' + tlfnr })
				.then(function (response) {
					if (response.id) {

						LogService.log('Det finnes allerede en registrert bruker med dette telefonnummeret!', 'warn');
						$scope.newuser = false;
						$scope.data.name = response.name;
						sendNewActivationCode(tlfnr);

					} else {

						LogService.log('Forsøker å finne navn knyttet til ' + tlfnr);
						$scope.newuser = true;
						phoneNumberLookup(tlfnr);

					}
				});
		}

		/**
		 * Sender en ny aktiveringskode til en allerede registrert bruker
		 */
		function sendNewActivationCode(tlfnr) {
			HttpService.neverEverGiveUp({ method: 'GET', url: '/users/new-activation-code?phone=' + tlfnr })
				.then(function (response) {
					LogService.log('Ny aktiveringskode sendt.');
					//$scope.header = 'Verifisér bruker';
					$scope.step = 'confirm';
				});
		}

		/**
		 * Prøver å slå opp telefonnummer i ekstern katalog
		 */
		function phoneNumberLookup(tlfnr) {
			HttpService.neverEverGiveUp({ method: 'GET', url: '//services2.biblionaut.net/phone_number_lookup.php?phone=' + tlfnr, timeout: 10000 })
				.then(function (person) {

					// decode html entities in a funny way:
					//var pname = $('<div />').html(response.personname).text();

					if (!person.found) {
						LogService.log('Klarte ikke å slå opp telefonnummeret', 'error');
						$scope.data.name = '';
					} else {
						LogService.log('Fant navn: ' + person.displayname);
						$scope.data.name = person.displayname;
					}

					storeUser($scope.data);

				});
		}

		/**
		 * Lagrer en ny bruker i databasen
		 */
		function storeUser (data) {
			HttpService.neverEverGiveUp({ method: 'POST', url: '/users/store', data: data })
				.then(function (response) {
					LogService.log('Brukeren ble opprettet');
					if ($scope.data.name == '') {
						// We got no name. Should we care?
					}
					$scope.step = 'confirm';
				});
		}

		/**
		 * Sjekker aktiveringskoden mot serveren
		 */
		function checkActivationCode(data) {
			LogService.log('Sjekker koden');

			HttpService.neverEverGiveUp({ method: 'POST', url: '/users/activate', data: data })
				.then(function (response) {
					if (response.error != "") {
						LogService.log(response.error, 'error');
						if (response.error == 'invalid_code') {
							LogService.log('Koden var ikke riktig. Prøv igjen', 'error');
							//$('#bekreftelse').closest('.control-group').addClass('error');
						}
					} else {
						LogService.log('Bruker bekreftet!', 'info');
						user_id = parseInt(response.user_id, 10);

						completeCheckout();

						/* if ($scope.newuser) {
							$scope.card_status = 'Legg et nytt lånekort på RFID-leseren. Hold det i ro til kortet er ferdigskrevet.';
							$scope.step = 'writecard';
						} else {
							completeCheckout();
						} */
					}
			});
		}

		function completeCheckout() {
			$scope.step = 'checkout';
			$scope.checkout_status = 'Et øyeblikk... Lagrer lån...';
			$scope.checkout_error = false;

			console.log('------- CHECKOUT -----------');
			if (CartService.contents.length == 0) {
				LogService.log('Du har ikke valgt noen bøker','error');
				$scope.checkout_status = 'Du har ikke valgt noen bøker';
				$scope.checkout_error = true;
				return;
			}
			console.log(CartService.contents);
			console.log(user_id);

			if (user_id == -1) {
				$scope.checkout_status = 'Du er ikke identifisert';
				return;
			}

			var document_ids = [];
			for (var i = CartService.contents.length - 1; i >= 0; i--) {
				document_ids.push(parseInt(CartService.contents[i].catalogueData.id, 10));
			};

			var postData = {
				'user_id': user_id,
				'documents': document_ids,
			};
			console.log(postData);


			HttpService.neverEverGiveUp({ method: 'POST', url: '/users/add-loans', data: postData })
				.then(function (response) {
					// The then function here is an opportunity to modify the response
					console.log('got response');
					console.log(response);
					console.log('------- CHECKOUT COMPLETE -----------');
					$scope.checkout_status = 'Utlånet var vellykket!';
					$scope.step = 'complete';
					//$location.path('/users/' + user_id);
				});

		}

		/**
		 * Skriver nytt lånekort
		 */
		function writeCard(uid) {
			LogService.log('Skriver til kort, vennligst vent...');
			WebService.send({
				rcpt: 'backend',
				msg: 'write-patron-card',
				uid: uid,
				data: { user_id: user_id }
			});
		}

		/*
		 * Fant RFID-tagg identifisert som eksisterende lånekort
		 */
		function foundPatronCard(item) {
			LogService.log('Fant bruker: ' + item.id);
			user_id = parseInt(item.id, 10);
			$scope.newuser = false;
			completeCheckout();
		}

		/*
		 * Triggered whenever we received a websocket message from the WebsocketService.
		 * The broadcasted message bubbles up to our $scope from $rootScope
		 */
		$scope.$on('messageReceived', function (e, data) {

			/* Example response: {"msg": "new-tag", "item": {
					"uid": "9ACA9C16500104E0",
					"library": "1030310",
					"nparts": 1,
					"usage_type": "patron-card",
					"id": "5",
					"is_blank": false,
					"crc_ok": true,
					"partno": 1,
					"country": "NO",
					"crc": "E0A9",
					"error": ""
				}, "rcpt": "frontend", "uid": "9ACA9C16500104E0"}
			 */

			if (data.msg == 'new-tag') {

				if (data.item.is_blank) {

					LogService.log('Kortet er blankt', 'warn');
					if ($scope.step == 'writecard') {
						$scope.card_status = 'Skriver til kortet... Ikke fjern det enda!';
						$scope.$apply(writeCard(data.uid));
					}

				} else if (data.item.usage_type == 'patron-card') {

					if ($scope.step == 'start') {

						console.log('Found patron card');
						console.log(data.item);
						$scope.$apply(foundPatronCard(data.item));

					} else if ($scope.step == 'writecard') {

						LogService.log('Kortet er ikke blankt!', 'error');

					}

				}
			} else if (data.msg == 'card-written') {

				LogService.log('Gratulerer, kortet er ferdig!');
				if ($scope.step == 'writecard') {
					$scope.card_status = 'Gratulerer, lånekortet ditt er ferdig. Du kan nå fjerne det.';
					$scope.$apply(completeCheckout());
				}

			}

		});

		WebService.connect('ws://linode2.biblionaut.net:8080');

	};

	CheckoutController.$inject = ['$scope', '$location', 'LogService', 'WebService', 'CartService', 'HttpService'];

	angular.module('bibcraft.selfservice.checkout', ['ngLocale', 'ngAnimate', 'bibcraft.selfservice.services'])
		.controller('CheckoutController', CheckoutController);


}());
