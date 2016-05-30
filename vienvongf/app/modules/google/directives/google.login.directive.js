/**
 * Created by hoanggia on 4/8/15.
 */

'use strict';

angular
    .module('googleMod')
    .directive('googleLogin', ['$rootScope', '$state','localStorageService', 'FConnect', 
        function ($rootScope, $state, localStorageService, FConnect, FSecurity) {
        return {
            restrict: 'A',
            scope: {},
            template: '<a href="" ng-click="gpLogin()"><i class="fa fa-3x fa-google-plus-square google-text"></i></a>',
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
                var login = function(gpauth) {
                    console.log(gpauth);
                    var auth = {
                        access_token: gpauth.access_token,
                        expires_at: gpauth.expires_at,
                        expires_in: gpauth.expires_in,
                    }
                    // Logged into your app and Facebook.
                    // send code to server
                    var url = $rootScope.apiUrl.auth.google;
                    // connect to server
                    FConnect(url).posts(auth, function(response){
                        console.log(response);
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
                scope.gpLogin = function() {
                    // param to init gapi
                    var myParams = {
                        'client_id': '1082471688155-kao66flq0pn2f1ceo006q1prsrrp2r4r.apps.googleusercontent.com',
                        // 'cookiepolicy': 'single_host_origin',
                        // 'callback': scope.login,
                        'scope': 'https://www.googleapis.com/auth/plus.login https://www.googleapis.com/auth/userinfo.email',
                        // 'requestvisibleactions': 'http://schema.org/AddAction'
                    };
                    // google authenticate param
                    gapi.auth.init(function(){
                        gapi.auth.authorize(myParams, login);
                    });
                }
            }
        }
    }]);