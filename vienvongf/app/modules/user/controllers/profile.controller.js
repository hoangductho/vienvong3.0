'use strict';

/**
 * @ngdoc function
 * @name vienvongApp.controller:MainCtrl
 * @description
 * # MainCtrl
 * Controller of the vienvongApp
 */
angular.module('userMod')
	.controller('ProfileCtrl', function ($rootScope, $scope, $state, localStorageService, FConnect) {
		// submit status
		$scope.submitting = false;
		// error message of server
		$scope.errmsg = null;
		// profile info
		$scope.profile = null;

  		// ------------------------------------------------------------
  		/**
		 * ========================================
		 * Load profile of user
		 * ========================================
		 */
		$scope.loading = function() {
			// submitting
			$scope.submitting = true;
			$scope.errmsg = null;
			// get login url of api
			var url = $rootScope.apiUrl.user.profile.index;
			// login from api
			FConnect(url, {}).getauth({}, function(data){
				if(data.ok){
					$scope.profile = data.data;
				}else {
					$scope.errmsg = data.errmsg;
				}

				$scope.submitting = false;
			}, function(e) {
				var data = e.data;
				if(data.ok == 0) {
					$scope.errmsg = data.errmsg;
				}else {
					$scope.errmsg = "Server xảy ra sự cố, xin vui lòng thử lại sau!";
				}
				$scope.submitting = false;
			});
		}
		$scope.loading();
		// ------------------------------------------------------------
  		/**
		 * ========================================
		 * Update profile of user
		 * ========================================
		 */
		$scope.update = function() {
			if(this.ProfileForm.$invalid){
				return false;
			}
			// submitting
			$scope.submitting = true;
			$scope.errmsg = null;
			// get login url of api
			var url = $rootScope.apiUrl.user.profile.update;
			// login from api
			FConnect(url, {}).postauth($scope.profile, function(data){
				if(data.ok){
					// $scope.profile = data.data;
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
