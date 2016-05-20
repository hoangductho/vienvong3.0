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
        controller: 'AuthCtrl'
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
      .state('app.auth.logout', {
        url: "/logout",
        templateUrl: modulePath + "views/logout.html",
        // controller: 'LoginCtrl'
      });
  });
