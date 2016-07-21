'use strict';

/**
 * @ngdoc function
 * @name vienvongApp.controller:MainCtrl
 * @description
 * # MainCtrl
 * Controller of the vienvongApp
 */
angular.module('authMod')
	.controller('ResendCtrl', function ($rootScope, $scope, $state, $interval, FConnect) {
		// submit status
		$scope.submitting = false;
		// error message of server
		$scope.errmsg = null;
		// status active
		$scope.ok = 0;
		// count down
		$scope.countdown = 5;
  		// ------------------------------------------------------------
  		/**
		 * ========================================
		 * Auth Submit Form
		 * ========================================
		 */
		$scope.resend = function() {
			if(this.authForm.$invalid){
				return false;
			}else {
				// submitting
				$scope.submitting = true;
				// get login url of api
				var url = $rootScope.apiUrl.auth.resend;
				// get data of user
				var resend = {'email': this.authForm.email.$viewValue};
				// login from api
				FConnect(url, {}).posts(resend, function(data){
					if(data.ok){
						$scope.ok = data.ok;
						$interval(function(){
	  						if($scope.countdown) {
	  							$scope.countdown --;
	  						}
	  						if(!$scope.countdown) {
	  							$state.go('app.auth.signin');
	  						}
	  					}, 1000, $scope.countdown)
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
