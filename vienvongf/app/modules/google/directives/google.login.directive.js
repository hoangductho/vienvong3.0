/**
 * Created by hoanggia on 4/8/15.
 */

'use strict';

angular
    .module('googleMod')
    .directive('googleLogin', ['$rootScope', '$state','localStorageService', 'FConnect', 
        function ($rootScope, $state, localStorageService, FConnect) {
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
                        if(response.ok && response.result) {
                            // get authenticate info
                            var data = JSON.stringify({userid: response.result.userid, access_token: response.result.access_token});
                            var auth = {
                                token: $rootScope.rsaEncryptData(data),
                                refresh: $rootScope.aesEncrypt(data, {key: $rootScope.rsakey.publicHex.slice(0,32), iv: $rootScope.rsakey.publicHex.slice(0,16),})
                            };
                            // storage auth info
                            localStorageService.set('auth',auth);
                            // get user profile
                            getProfile();
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
                        'client_id': '1082471688155-url1mk0kstnrnukmpvdt9humd7qbvnag.apps.googleusercontent.com',
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