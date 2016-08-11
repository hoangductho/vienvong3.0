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
  .module('profileMod', [])
  .config(function ($stateProvider) {
    // init default module path
    var modulePath = 'modules/profile/';
    // Now set up the states
    $stateProvider
      .state('app.main.profile', {
        abstract: true,
        url: "profile",
        templateUrl: modulePath + "views/profile.html",
        controller: 'ProfileCtrl'
      })
      .state('app.main.profile.info', {
        url: "/info",
        templateUrl: modulePath + "views/info.html",
        controller: 'InfoCtrl'
      })
      .state('app.main.profile.password', {
        url: "/password",
        templateUrl: modulePath + "views/password.html",
        controller: 'PasswordCtrl'
      })
  });
