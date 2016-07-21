'use strict';

/**
 * @ngdoc function
 * @name vienvongApp.controller:MainCtrl
 * @description
 * # MainCtrl
 * Controller of the vienvongApp
 */
angular.module('authMod')
  .controller('AuthCtrl', function ($rootScope, $state) {
  	// setup user info when info changed
	$rootScope.$watch('auth', function(data){
		if(!angular.isUndefined(data) && data) {
			if($state.includes('app.auth')) {
				$state.go('app.main');
			}
		}
	});
  });
