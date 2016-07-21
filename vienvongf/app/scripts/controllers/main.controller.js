'use strict';

/**
 * @ngdoc function
 * @name vienvongApp.controller:MainCtrl
 * @description
 * # MainCtrl
 * Controller of the vienvongApp
 */
angular.module('vienvongApp')
	.controller('MainCtrl', function ($rootScope, $scope, $state, localStorageService, FConnect) {
		// setup user info
		if($rootScope.auth) {
			$scope.user = $rootScope.auth.email;
		}
		// setup user info when info changed
		$rootScope.$watch('auth', function(data){
			if(!angular.isUndefined(data) && data) {
				$scope.user = data.email;
			} else {
				$scope.user = null;
			}
		});
		// logout
		$rootScope.logout = function() {
			var check = confirm('Bạn có thực sự muốn đăng xuất khỏi tài khoản này không?');
			if(check) {
				$state.go('app.auth.signout');
			}
		}
	});
