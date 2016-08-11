'use strict';

angular
	.module('vienvongApp')
	.directive("mainMenu", function() {
	    return {
	    	restrict: 'A',
	        //scope: {},
	        templateUrl: "views/components/main.menu.html"
	    };
	});