'use strict';

/**
 * @ngdoc function
 * @name vienvongApp.controller:MainCtrl
 * @description
 * # MainCtrl
 * Controller of the vienvongApp
 */
angular.module('authMod')
	.controller('SignoutCtrl', function ($rootScope, $scope, $state, localStorageService, FConnect) {
		localStorageService.remove('auth');
	});
