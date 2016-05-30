'use strict';

/**
 * @ngdoc function
 * @name vienvongApp.controller:MainCtrl
 * @description
 * # MainCtrl
 * Controller of the vienvongApp
 */
angular.module('authMod')
	.controller('ActiveCtrl', function ($rootScope, $scope, $state, $interval, FConnect) {
		// submit status
		$scope.submitting = false;
		// error message of server
		$scope.errmsg = null;
		// status active
		$scope.ok = -1;
		// count down
		$scope.countdown = 5;
  		// ------------------------------------------------------------
  		/**
  		 * ========================================
  		 * Active Account
  		 * ========================================
  		 */
  		var active = function() {
  			// submit status
  			$scope.submitting = true;
  			// active url
  			var url = $rootScope.apiUrl.auth.active;
  			// server process
  			FConnect(url).posts($state.params, function(data){
  				$scope.ok = data.ok;
  				if(data.ok) {
  					$interval(function(){
  						if($scope.countdown) {
  							$scope.countdown --;
  						}
  						if(!$scope.countdown) {
  							$state.go('app.auth.signin');
  						}
  					}, 1000, $scope.countdown)
  				}else {
  					$scope.err = data.err;
  					$scope.errmsg = data.errmsg;
  					if(data.err === 11001) {
  						$interval(function(){
	  						if($scope.countdown) {
	  							$scope.countdown --;
	  						}
	  						if(!$scope.countdown) {
	  							$state.go('app.auth.resend');
	  						}
	  					}, 1000, $scope.countdown)
  					}
  				}
  				$scope.submitting = false;
  			}, function(){
  				$scope.errmsg = 'Quá trình xử lý xảy ra sự cố. Mong bạn vui lòng thử lại sau.';
  				$scope.submitting = false;
  			});

  		}
  		active();
	});
