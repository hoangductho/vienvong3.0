'use strict';

/**
 * @ngdoc function
 * @name vienvongApp.controller:MainCtrl
 * @description
 * # MainCtrl
 * Controller of the vienvongApp
 */
angular.module('authMod')
  	.controller('ForgotCtrl', function ($rootScope, $scope, FConnect) {
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
				// get login url of api
				var url = $rootScope.apiUrl.auth.forgot;
				// get data of user
				var user = {'email': this.authForm.email.$viewValue};
				// login from api
				FConnect(url, {}).posts(user, function(data){
					if(data.ok){

					}else {
						$scope.errmsg = data.errmsg;
					}

					$scope.submitting = false;
				}, function() {
					if(data.ok == 0) {
						$scope.errmsg = data.errmsg;
					}else {
						$scope.errmsg = "Server xảy ra sự cố, xin vui lòng thử lại sau!";
					}
					$scope.submitting = false;
				});
			}
		}
  	});
