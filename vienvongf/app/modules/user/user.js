'use strict';

/**
 * @ngdoc overview
 * @name authMod
 * @description
 * # vienvongApp
 *
 * authenticate module of the application.
 */
angular
  .module('userMod', [])
  .config(function ($stateProvider) {
    // init default module path
    var modulePath = 'modules/user/';
    // Now set up the states
    $stateProvider
      .state('app.main.user', {
        abstract: true,
        url: "user",
        templateUrl: modulePath + "views/user.html",
        //controller: 'AuthCtrl',
        resolve: {
          online: function($rootScope, $state, $location, localStorageService) {
            /**
             * ====================================
             * Check Loged In
             * ====================================
             */
            var loggedIn = function() {
              if(!$rootScope.auth) {
                $rootScope.auth = localStorageService.get('auth');  
              }
              
              if(!$rootScope.auth || $rootScope.auth == null) {
                $state.go('app.main');
              }
            }
          }
        },
        controller: 'UserCtrl'
      })
      .state('app.main.user.profile', {
        url: "/profile",
        templateUrl: modulePath + "views/profile.html",
        controller: 'ProfileCtrl'
      })
      .state('app.main.user.password', {
        url: "/password",
        templateUrl: modulePath + "views/password.html",
        controller: 'PasswordCtrl'
      })
  });
