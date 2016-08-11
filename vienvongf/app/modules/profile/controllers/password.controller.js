'use strict';

/**
 * @ngdoc function
 * @name vienvongApp.controller:MainCtrl
 * @description
 * # MainCtrl
 * Controller of the vienvongApp
 */
angular.module('profileMod')
	.controller('PasswordCtrl', function ($rootScope, $scope, $state, localStorageService, FConnect) {
		// submit status
		$scope.submitting = false;
		// error message of server
		$scope.errmsg = null;
		// password object
		$scope.password = {};
		// ------------------------------------------------------------
  		/**
		 * ========================================
		 * Update profile of user
		 * ========================================
		 */
		$scope.update = function() {
			if(this.PasswordForm.$invalid){
				return false;
			}
			// submitting
			$scope.submitting = true;
			$scope.errmsg = null;
			// get login url of api
			var url = $rootScope.apiUrl.user.password;
			// login from api
			FConnect(url, {}).postauth($scope.password, function(data){
				console.log(data);
				if(data.ok){
					$state.go('app.auth.signout');
				}else {
					$scope.errmsg = data.errmsg;
				}

				$scope.submitting = false;
			}, function(error) {
				var data = error.data;
				if(data.ok == 0) {
					$scope.errmsg = data.errmsg;
				}else {
					$scope.errmsg = "Server xảy ra sự cố, xin vui lòng thử lại sau!";
				}
				$scope.submitting = false;
			});
			return false;
		}
	});
