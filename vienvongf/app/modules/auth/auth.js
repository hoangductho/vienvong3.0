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
  .module('authMod', [])
  .config(function ($stateProvider) {
    // init default module path
    var modulePath = 'modules/auth/';
    // Now set up the states
    $stateProvider
      .state('app.auth', {
        abstract: true,
        url: "/auth",
        templateUrl: modulePath + "views/auth.html",
        controller: 'AuthCtrl',
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
              
              if($rootScope.auth) {
                $state.go('app.main');
              }
            }
            /**
             * ====================================
             * Catch Auth URLs Change
             * ====================================
             */
            $rootScope.$on( '$locationChangeStart', function(){
              if($state.includes('app.auth') && !$state.includes('app.auth.signout')) {
                loggedIn();
              }
            });
            /**
             * ====================================
             * Check Logged In 
             * ====================================
             */
            $rootScope.$on( '$stateChangeSuccess', function(){
              if($state.includes('app.auth') && !$state.includes('app.auth.signout')) {
                loggedIn();
              }
            });
          }
        }
      })
      .state('app.auth.signin', {
        url: "/signin",
        templateUrl: modulePath + "views/signin.html",
        controller: 'SigninCtrl'
      })
      .state('app.auth.active', {
        url: "/active/:uid/:code",
        templateUrl: modulePath + "views/active.html",
        controller: 'ActiveCtrl'
      })
      .state('app.auth.resend', {
        url: "/resend",
        templateUrl: modulePath + "views/resend.html",
        controller: 'ResendCtrl'
      })
      .state('app.auth.forgot', {
        url: "/forgot",
        templateUrl: modulePath + "views/forgot.html",
        controller: 'ForgotCtrl'
      })
      .state('app.auth.reset', {
        url: "/reset/:id/:code",
        templateUrl: modulePath + "views/reset.html",
        controller: 'ResetCtrl'
      })
      .state('app.auth.signup', {
        url: "/signup",
        templateUrl: modulePath + "views/signup.html",
        controller: 'SignupCtrl'
      })
      .state('app.auth.signout', {
        url: "/signout",
        templateUrl: modulePath + "views/signout.html",
        controller: 'SignoutCtrl'
      });
  });
