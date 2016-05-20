<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * ================================================
 * Visitor Table Queries
 * ================================================
 *
 * @Description 
 *
 * Log visitor in day
 *
 * --------------------------------------
 * 
 * @Properties
 *
 * array(
 * 		'_id' => 'Visitor ID',
 *		'ip' => 'Client IP',
 *		'client_string' => 'Client random string',
 *		'browser' => 'Client browser',
 *		'browser_version' => 'Client browser version',
 *		'mobile' => 'Mobile Device',
 *		'platform' => 'Client OS',
 *		'referrer' => 'Client domain referrer',
 *		'agent_string' => 'Client agent string',
 *		'languages' => 'Client languages',
 *		'charsets' => 'Charsets',
 * )
 */
class MLog_visitor extends MBase {
	/**
	 * Table name
	 */
	public $table = 'log_visitor';
	// ----------------------------------------------------------------
	/**
	 * --------------------------------------------
	 * Constructor
	 * --------------------------------------------
	 */
	function __construct() {
		parent::__construct();
	}
}