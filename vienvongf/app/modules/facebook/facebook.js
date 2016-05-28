'use strict';

/**
 * @ngdoc overview
 * @name PublicModule
 * @description
 * # Public content module of gigidoApp
 *
 * sub module of the application.
 */

angular
  .module('facebookMod', [])
  .run(['$rootScope', '$window', 
    function($rootScope, $window){
      $window.fbAsyncInit = function () {
          FB.init({
            appId      : '649217441856181',
            xfbml      : true,
            version    : 'v2.6'
          });
      };

      (function(d, s, id) {
          var js, fjs = d.getElementsByTagName(s)[0];
          if (d.getElementById(id)) return;
          js = d.createElement(s); js.id = id;
          js.src = "//connect.facebook.net/en_US/sdk.js";
          fjs.parentNode.insertBefore(js, fjs);
      }(document, 'script', 'facebook-jssdk'));

      $(document).ajaxComplete(function(){
          try{
              FB.XFBML.parse();
          }catch(ex){}
      });

      $rootScope.facebookAppId = '649217441856181';
    }
  ]);
