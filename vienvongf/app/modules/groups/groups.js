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
  .module('groupsMod', [])
  .config(function ($stateProvider) {
    // init default module path
    var modulePath = 'modules/groups/';
    // Now set up the states
    $stateProvider
      .state('app.main.groups', {
        abstract: true,
        url: "groups",
        templateUrl: modulePath + "views/groups.html",
        // controller: 'GroupsCtrl'
      })
      .state('app.main.groups.detail', {
        url: "/detail/:gid",
        templateUrl: modulePath + "views/groups.detail.html",
        // controller: 'GroupsCreateCtrl'
      })
      .state('app.main.groups.list', {
        url: "/list",
        templateUrl: modulePath + "views/groups.list.html",
        // controller: 'GroupsListCtrl'
      })
  });
