<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Presytem {
	/**
	 * --------------------------------------------
	 * Setup Cross Domain Header Setting
	 * --------------------------------------------
	 */
	public function crossdomain()
	{
		// setup Cross Domain Origin
		header("Access-Control-Allow-Origin: *");
		// setup Header's fields
		header("Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept, Content-Header, Authenticate");
		// setup credentials
	    header('Access-Control-Allow-Credentials: true');
	    // setup method support
	    header('Access-Control-Allow-Methods: GET, POST, PUT, OPTIONS');
	}
	// ----------------------------------------------------------------
	/**
	 * --------------------------------------------
	 * Setup Default Timezone
	 * --------------------------------------------
	 */
	public function timezone() {
		date_default_timezone_set('UTC');
	}
	// ----------------------------------------------------------------
	/**
	 * --------------------------------------------
	 * Setup Default Frontend Site
	 * --------------------------------------------
	 */
	public function domain() {
		define('PUBLICDOMAIN', 'http://0.0.0.0:9000/');
	}
	/**
	 * --------------------------------------------
	 * Init Presystem Setup
	 * --------------------------------------------
	 */
	public function init() {
		// setup timezone
		$this->timezone();
		// setup cross domain
		$this->crossdomain();
		// setup public domain
		$this->domain();
	}
	// ----------------------------------------------------------------
}
