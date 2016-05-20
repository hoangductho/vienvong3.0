'use strict';

/**
 * @ngdoc function
 * @name vienvongApp.controller:MainCtrl
 * @description
 * # MainCtrl
 * Controller of the vienvongApp
 */
angular.module('authMod')
  	.controller('SignupCtrl', function ($rootScope, $scope, $timeout, $state, FConnect) {
		// submit status
		$scope.submitting = false;
		// error message of server
		$scope.errmsg = null;
		// sign up success status
		$scope.success = false;

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
				// get login url of api
				var url = $rootScope.apiUrl.auth.signup;
				// get data of user
				var user = {'email': this.authForm.email.$viewValue, 'password': this.authForm.password.$viewValue};
				// login from api
				FConnect(url, {}).posts(user, function(data){
					if(data.ok){
						// sign up success
						$scope.success = true;
						// redirect to login page
						$timeout(function() {
							$state.go('app.auth.signin');
						}, 5000);
					}else {
						// show error message
						$scope.errmsg = data.errmsg;
					}
					// end submitting
					$scope.submitting = false;
				}, function() {
					// end submitting
					$scope.submitting = false;
				});
			}
		}
  	});
