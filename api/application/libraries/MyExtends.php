<?php
/**
 * ================================================
 * Extends Functions Class
 * ================================================
 * 
 * Some functions extend using in system
 */

if(!class_exists('MyExtends')) {
	class MyExtends {
		/**
		 * ------------------------------------------------
		 * Random String
		 * ------------------------------------------------
		 *
		 * @todo Generate ramdom string with length inputed
		 *
		 * @param int $length length of string needed
		 */  
		public function RandomString($length = 16) {
		    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
		    $charactersLength = strlen($characters);
		    $randomString = '';
		    for ($i = 0; $i < $length; $i++) {
		        $randomString .= $characters[rand(0, $charactersLength - 1)];
		    }
		    return $randomString;
		}
	}
}