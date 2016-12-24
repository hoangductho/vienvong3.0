'use strict';

/**
 * @ngdoc function
 * @name vienvongApp.controller:MainCtrl
 * @description
 * # MainCtrl
 * Controller of the vienvongApp
 */
angular.module('groupsMod')
	.controller('ListGroupsCtrl', function ($rootScope, $scope, $state, $interval, FConnect) {
		$('#data-table').DataTable();
	});
