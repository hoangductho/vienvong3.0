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
	.factory('FConnect', function ($resource, $rootScope, $filter) {

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
        var aesKeyInit = function() {
            // Create AES Key and AES Initilization Vector
            var key = CryptoJS.lib.WordArray.random(256 / 8).toString(); 
            var iv = CryptoJS.lib.WordArray.random(128 / 8).toString();
            // Init aeskey in this page
            return {
                key: key,
                iv: iv,
            }
        }
        var aeskey = aesKeyInit();
        // ------------------------------------------------------------
        // --------------------------------------------------------------------
		/**
		 * ==============================================
		 * RSA Encrypt
		 * ==============================================
		 * 
		 * @param string data
		 * @param key RSA pulbic hexa key
		 *
		 * @return string
		 */
		var rsaEncrypt = function(data, key) {
		    if(data.length > 245) {
		        console.log('RSA data lenght limited');
		        return false;
		    }

		    if(key) {
		        var encrypt = new RSAKey();
		        encrypt.setPublic(key, '10001');
		        var encrypted = encrypt.encrypt(data);
		        
		        return encrypted;
		    }else {
		        console.log('RSA Key not existed');
		        return false;
		    }
		}
        // ------------------------------------------------------------
        /**
         * ======================================
         * RSA Encrypt Data
         * ======================================
         *
         * @todo Encrypt data by rsakey storaged in rootScope
         *
         * @param data
         *
         * @return void
         */
        var rsaEncryptData = function(data){
        	// Create AES Key Package, using rsa encrypt
			if(!$rootScope.rsakey) {
				console.log('RSA Key not existed!');
				// $rootScope.rsaKeyInit();
			}
			return rsaEncrypt(data, $rootScope.rsakey.publicHex);
        }
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
            if(aeskey) {
            	// create Hexa format for initialization vector
				var iv = CryptoJS.enc.Hex.parse(aeskey.iv);
				// create Hexa format for key to encrypt
	            var key = CryptoJS.enc.Hex.parse(aeskey.key);
	            // setup initializtion vector for AES Method
				var options = {iv: iv};
				// Encrypt data input
				var encrypted = CryptoJS.AES.encrypt(angular.toJson(data), key, options); 
				// Get Base64 string of data encrypted
				var text64 = encrypted.ciphertext.toString(CryptoJS.enc.Base64);
                // Return string of json request data
                return angular.toJson({encrypted: text64});
            }else {
            	console.log('AES Key not existed');
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
			if(aeskey) {
				// create Hexa format for initialization vector
				var iv = CryptoJS.enc.Hex.parse(aeskey.iv);
				// create Hexa format for key to encrypt
	            var key = CryptoJS.enc.Hex.parse(aeskey.key);
	            // setup initializtion vector for AES Medtho
				var options = {iv: iv};
            	// Decrypt data response
				var decrypted = CryptoJS.AES.decrypt(data, key, options);
				// return string of data responsed
				return angular.fromJson(CryptoJS.enc.Utf8.stringify(decrypted));
			}else {
				conosle.log('AES Key not existed');
				return false;
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
	  					return rsaEncryptData(aeskey.key+'/'+aeskey.iv);
	  				}
	  			},
	  			transformRequest: function(data, headers){
	                return data;
	            },
	            transformResponse: function(data, headers){
	                return data;
	            }
	  		},
	  		posts: {
	  			method: 'POST',
	  			headers: { 
	  				'Content-Header': function() { 
	  					// AES key package
	  					return rsaEncryptData(aeskey.key+'/'+aeskey.iv);
	  				}
	  			},
	  			transformRequest: function(data, headers) {
	  				return requestSecurity(data, headers);
	  			},
	            transformResponse: function(data, headers){
	            	// convert response data to Json
	            	var dataGeted = angular.fromJson(data);

	            	return responseSecurity(dataGeted.response);
	            }
	  		},
	  	};

	  	return function(url, params) {
	  		return $resource(url, params, actions);
	  	};
  	});