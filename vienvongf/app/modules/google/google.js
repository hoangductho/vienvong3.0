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
  .module('googleMod', [])
  .run(['$rootScope', '$window', 
    function($rootScope, $window){
      (function() {
        var po = document.createElement('script'); po.type = 'text/javascript'; po.async = true;
        po.src = 'https://apis.google.com/js/client:plusone.js';
        var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(po, s);
      })();
    }
  ]);
