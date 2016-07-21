'use strict';

/**
 * @ngdoc overview
 * @name Securities factory
 * @description
 * # factories connect to security api of gigidoApp
 *
 */

angular
	.module('vienvongApp')
	.factory('FConnect', function ($resource, $rootScope, localStorageService, $state, FSecurity) {

        // --------------------------------------------------------
		/**
         * ======================================
         * AES KEY Init
         * ======================================
         * 
         * @todo create aes key package (key + init_vector + keypack_rsa_encrypt)
         *
         * @return void
         */
        var aeskey = FSecurity.aesKeyInit();
        // ------------------------------------------------------------
		/**
		 * ======================================
		 * Encrypt Resquest Data
		 * ======================================
		 *
		 * @todo Encrypt Resquest data post to server using AES Encrypt Method
		 *
		 * @param string data
		 *
		 * @return string
		 */
		var requestSecurity = function(data){
			var encrypted = FSecurity.aesEncrypt(data, aeskey);
            if(encrypted) {
                return angular.toJson({encrypted: encrypted});
            }else {
            	return false;
            }
            
        };
        // ------------------------------------------------------------
        /**
		 * ======================================
		 * Decrypt Response Data
		 * ======================================
		 *
		 * @todo Decrypt Response data recived from server using AES Decrypt Method
		 *
		 * @param Base64 data base64 string of data encrypted
		 *
		 * @return JSON
		 */
		var responseSecurity = function(data) {
			// convert response data to Json
        	var dataGeted = angular.fromJson(data);
        	if(dataGeted.response) {
        		return FSecurity.aesDecrypt(dataGeted.response, aeskey);
        	}else {
        		if(dataGeted.ok == 0 && dataGeted.err == 498) {
        			alert('Phiên làm việc hết hiệu lực!');
        			$state.go('app.auth.signout');
        		}
        		return dataGeted;
        	}
			
		};
		// ------------------------------------------------------------
		/**
		 * ======================================
		 * Actions Config 
		 * ======================================
		 * 
		 * @todo Define actions of the factory, we can using them to comunicate with server
		 */
	  	var actions = {
	  		get: {
	  			method: 'GET'
	  		},
	  		post: {
	  			method: 'POST'
	  		},
	  		gets: {
	  			method: 'GET',
	  			headers: { 
	  				'Content-Header': function() { 
	  					// AES key package
	  					return FSecurity.rsaEncryptData(aeskey.key+'/'+aeskey.iv);
	  				}
	  			},
	  			transformRequest: function(data, headers){
	                return data;
	            },
	            transformResponse: function(data, headers){
	            	return responseSecurity(data);
	            }
	  		},
	  		posts: {
	  			method: 'POST',
	  			headers: { 
	  				'Content-Header': function() { 
	  					// AES key package
	  					return FSecurity.rsaEncryptData(aeskey.key+'/'+aeskey.iv);
	  				}
	  			},
	  			transformRequest: function(data, headers) {
	  				return requestSecurity(data, headers);
	  			},
	            transformResponse: function(data, headers){
	            	return responseSecurity(data);
	            }
	  		},
	  		getauth: {
	  			method: 'GET',
	  			headers: { 
	  				'Content-Header': function() { 
	  					// AES key package
	  					return FSecurity.rsaEncryptData(aeskey.key+'/'+aeskey.iv);
	  				},
	  				'Authenticate': function() {
	  					var auth = localStorageService.get('auth');
	  					return FSecurity.aesEncrypt(auth, aeskey);
	  				}
	  			},
	  			transformRequest: function(data, headers){
	                return data;
	            },
	            transformResponse: function(data, headers){
	            	return responseSecurity(data);
	            }
	  		},
	  		postauth: {
	  			method: 'POST',
	  			headers: { 
	  				'Content-Header': function() { 
	  					// AES key package
	  					return FSecurity.rsaEncryptData(aeskey.key+'/'+aeskey.iv);
	  				},
	  				'Authenticate': function() {
	  					var auth = localStorageService.get('auth');
	  					return FSecurity.aesEncrypt(auth, aeskey);
	  				}
	  			},
	  			transformRequest: function(data, headers) {
	  				return requestSecurity(data, headers);
	  			},
	            transformResponse: function(data, headers){
	            	return responseSecurity(data);
	            }
	  		},
	  	};

	  	return function(url, params) {
	  		return $resource(url, params, actions);
	  	};
  	});