/**
 * Created by hoanggia on 4/8/15.
 */

'use strict';

angular
    .module('facebookMod')
    .directive('facebookLogin', ['$rootScope', '$state','localStorageService', 'FConnect', 
        function ($rootScope, $state, localStorageService, FConnect) {
        return {
            restrict: 'A',
            scope: {},
            template: '<a href="" ng-click="fbLogin()""><i class="fa fa-3x fa-facebook-square facebook-text"></i></a>',
            link: function(scope, element, attrs) {
                /**
                 * ========================================
                 * Get User's Profile
                 * ========================================
                 */
                var getProfile = function() {
                    // get api url
                    var url = $rootScope.apiUrl.user.profile.detail;
                    // get profile from server
                    FConnect(url).posts({}, function(response){
                        if(response.ok && response.result) {
                            // storage profile in client
                            localStorageService.set('profile', response.result);
                            // redirect page into other page
                            $state.go('app.user.profile');
                        }else {
                            $state.go('app.user.profile');
                        }
                    })
                }
                // ----------------------------------------------------
                /**
                 * ========================================
                 * Login Using Facebook
                 * ========================================
                 */
                var login = function(fbauth) {
                    // Logged into your app and Facebook.
                    // send code to server
                    var url = $rootScope.apiUrl.auth.facebook;
                    FConnect(url).posts({access_token: fbauth.accessToken}, function(response){
                        if(response.ok && response.result) {
                            // storage authenticate data
                            localStorageService.set('auth', response.result);
                            // redirect page
                            $state.go('app.main');
                        }else{
                            alert(response.err);
                        }
                    })
                }
                // ----------------------------------------------------
                /**
                 * ========================================
                 * Login Facebook Action
                 * ========================================
                 */
                scope.fbLogin = function() {
                    FB.getLoginStatus(function(current){
                        if (current.status === 'connected') {
                            // the user is logged in and has authenticated your
                            // app, and response.authResponse supplies
                            // the user's ID, a valid access token, a signed
                            // request, and the time the access token 
                            // and signed request each expire
                            // var uid = response.authResponse.userID;
                            // var accessToken = response.authResponse.accessToken;
                            login(current.authResponse);
                        } else if (current.status === 'not_authorized') {
                            // the user is logged in to Facebook, 
                            // but has not authenticated your app
                        } else {
                            // the user isn't logged in to Facebook.
                            FB.login(function(response){
                                if (response.status === 'connected') {
                                    login(response.authResponse)
                                }
                            },{
                                //auth_type: 'rerequest'
                            });
                        }
                    });
                }
            }
        }
    }]);