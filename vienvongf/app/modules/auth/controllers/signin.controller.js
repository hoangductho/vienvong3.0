'use strict';

/**
 * @ngdoc function
 * @name vienvongApp.controller:MainCtrl
 * @description
 * # MainCtrl
 * Controller of the vienvongApp
 */
angular.module('authMod')
	.controller('SigninCtrl', function ($rootScope, $scope, $state, localStorageService, FConnect) {
		// submit status
		$scope.submitting = false;
		// error message of server
		$scope.errmsg = null;

  		// ------------------------------------------------------------
  		/**
		 * ========================================
		 * Auth Submit Form
		 * ========================================
		 */
		$scope.submit = function() {
			if(this.authForm.$invalid){
				return false;
			}else {
				// submitting
				$scope.submitting = true;
				$scope.errmsg = null;
				// get login url of api
				var url = $rootScope.apiUrl.auth.signin;
				// get data of user
				var user = {'email': this.authForm.email.$viewValue, 'password': CryptoJS.SHA256(this.authForm.password.$viewValue).toString()};
				// login from api
				FConnect(url, {}).posts(user, function(data){
					if(data.ok){
						// storage profile in client
                        localStorageService.set('profile', data.result);
                        // redirect page into other page
                        $state.go('app.main');
					}else {
						$scope.errmsg = data.errmsg;
					}

					$scope.submitting = false;
				}, function() {
					$scope.submitting = false;
				});
			}
		}
	});
