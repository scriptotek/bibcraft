'use strict';

/* Services */

angular.module('bibcraft.selfservice.services', [])
	.service('WebService', ['$rootScope', 'LogService', function(rs, LogService) {

		this.connection = null;

		this.send = function(args) {
			console.log('> send 1');
			var s = JSON.stringify(args);
			console.log('>>> send 2');
			LogService.log('SEND: ' + s);
			console.log('>>> send 3');
			this.connection.send(s);
		};

		this._reconnect = function(url) {
			var that = this;

			this.connection = new WebSocket(url);
			LogService.log('Connecting to ' + this.connection.url);

			this.connection.onopen = function(e) {
				LogService.log('Connected to ' + e.target.url + ', identifying as frontend client');
				that.send({'msg': 'hello', 'role': 'frontend'});
			};
			this.connection.onclose = function(e) {
					LogService.log('Connection closed. Retrying in 5 seconds.', 'error');
				setTimeout(function() {
						that._reconnect(url);
				}, 5000);
			};
			this.connection.onerror = function(e) {
					LogService.log('Oh noes, webservice error: ' + e.data, 'error');
			};
			this.connection.onmessage = function(e) {
					LogService.log('RECV: ' + e.data);
					rs.$broadcast('messageReceived', e.data);
			};
		};

		this.connect = function(url) {
			if (this.connection == null) {
				this._reconnect(url);
			} else {
				//console.log('Already connected');
			}
		}

	}]);