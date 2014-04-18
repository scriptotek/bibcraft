(function() {

	'use strict';

	var CollectionService = function($q, $http, LogService) {

		var activeSubCollection = -1;

		this.setSubCollection = function(id) {
			activeSubCollection = id;
			console.log('set subcollection: ' + id);
		};

		this.getActiveCollection = function() {
			console.log('active subcollection: ' + activeSubCollection);

			var promise = $http({method: 'GET', cache: true, url: '/config.json'})
			.then(function (response) {
				if (response.data.activeCollection) {
					//LogService.log('Viser samling: ' + response.data.activeCollection, 'info');
					return [response.data.activeCollection, activeSubCollection];
				}
				return [-1, -1];
			}, function(response) {
				LogService.log('Http request failed!', 'error');
				return $q.reject(response);
			});

			return promise;
		};

		this.getBooks = function(collections, page, itemsPerPage) {

			var collections = collections.join(',');

			var promise = $http({
				method: 'GET',
				cache: true,
				url: '/documents?collection=' + collections + '&itemsPerPage=' + itemsPerPage + '&page=' + page
			})
			.then(function (response) {
				// The then function here is an opportunity to modify the response
				console.log(response);
				// The return value gets picked up by the then in the controller.
				if (response.data.documents) {
					for (var i = 0; i < response.data.documents.length; i++) {
						response.data.documents[i].available = (response.data.documents[i].loans.length === 0);
					}
				}
				LogService.log('Samlingen inneholder ' + response.data.total + ' bøker. Viser side ' + response.data.currentPage + ' av ' + response.data.lastPage + '.', 'info');
				return response.data;
			}, function(response) {
				LogService.log('Http request failed!', 'error');
				return $q.reject(response);
			});

			return promise;
		};

	};

	var CollectionController = function($scope, $location, $routeParams, CollectionService, ModalService) {

		var collection = -1,
			page = 1,
			lastPage = 1,
			itemsPerPage = 6;

		var init = function() {
			console.log('View: collection=' + collection + ', page=' + page + ', itemsPerPage=' + itemsPerPage);

			$scope.books = [];

			$scope.subcollections = [{id: -1, caption:'Alt'}, {id:2, caption:'Noe'}, {id:3, caption:'Intet'}];
			//$scope.selectedSubCollection = 1;

			var collections = [collection];
			if ($scope.selectedSubCollection != -1) collections.push($scope.selectedSubCollection);
			console.log('getBooks for collections: ' + collections.join(', '));

			CollectionService.getBooks(collections, page, itemsPerPage).then(function(d) {

				if (d.error) {
					$scope.error = d.error;
				} else {
					console.log('Got ' + d.documents.length + ' books');
					$scope.books = d.documents;
					lastPage = d.lastPage;
				}

			});

			$scope.prevPage = function() {
				console.log('goto prev page');
				var prevPage = page - 1;
				if (prevPage === 0) {
					prevPage = lastPage;
				}
				console.log('Goto prev page: ' + prevPage);
				$location.path('/browse/' + prevPage);
			};

			$scope.nextPage = function() {
				var nextPage = page + 1;
				if (nextPage > lastPage) {
					nextPage = 1;
				}
				console.log('Goto next page: ' + nextPage);
				$location.path('/browse/' + nextPage);
			};

			$scope.open = function(book) {
				ModalService.show('collection/openbook', book);
			};

			$scope.selectSubCollection = function(id) {
				$scope.selectedSubCollection = id;
				CollectionService.setSubCollection(id);
			};

		};

		if ($routeParams.page !== undefined) {
			console.log("HAVE PAGE");
			page = parseInt($routeParams.page, 10);
		}

		if ($routeParams.itemsPerPage !== undefined) {
			itemsPerPage = parseInt($routeParams.itemsPerPage, 10);
		}

		if ($routeParams.collection !== undefined) {
			collection = parseInt($routeParams.collection, 10);
			init();
		} else {
			CollectionService.getActiveCollection().then(function(c) {
				collection = c[0];
				$scope.selectedSubCollection = c[1];
				console.log('$scope.selectedSubCollection: ' + $scope.selectedSubCollection);
				init();
			});
		}

	};

	CollectionService.$inject = ['$q', '$http', 'LogService'];
	CollectionController.$inject = ['$scope', '$location', '$routeParams', 'CollectionService', 'ModalService'];

	angular.module('bibcraft.collection', ['ngRoute','ngTouch'])
		.service('CollectionService', CollectionService)
		.controller('CollectionController', CollectionController);

}());
