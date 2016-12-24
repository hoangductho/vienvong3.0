'use strict';

/**
 * @ngdoc function
 * @name vienvongApp.controller:MainCtrl
 * @description
 * # MainCtrl
 * Controller of the vienvongApp
 */
angular.module('groupsMod')
	.controller('EditGroupsCtrl', function ($rootScope, $scope, $state, $interval, FConnect) {
		// submit status
		$scope.submitting = false;
		// error message of server
		$scope.errmsg = null;
		// sign up success status
		$scope.success = false;

		$scope.edit = function () {
			if(this.ProfileForm.$invalid){
				return false;
			}else {
				// submitting
				$scope.submitting = true;
				// get login url of api
				var url = $rootScope.apiUrl.groups.edit;
				// get data of group
				var group = {'id': parseInt($state.params.gid), 'name': this.ProfileForm.groupname.$viewValue, 'status': parseInt(this.ProfileForm.groupstatus.$viewValue), 'introduce': this.ProfileForm.introduce.$viewValue || null};
				console.log(group);
				// login from api
				FConnect(url, {}).postauth(group, function(data){
					if(data.ok){
						console.log(data);
					}else {
						// show error message
						$scope.errmsg = data.errmsg;
					}
					// end submitting
					$scope.submitting = false;
				}, function(data) {
					if(data.ok == 0) {
						$scope.errmsg = data.errmsg;
					}else {
						$scope.errmsg = "Server xảy ra sự cố, xin vui lòng thử lại sau!";
					}
					// end submitting
					$scope.submitting = false;
				});
			}
		}
	});
