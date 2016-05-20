<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * ================================================
 * RSA Table Queries
 * ================================================
 * @Description 
 *
 * Storage RSA Key. RSA Key will be refresh in everyday
 *
 * --------------------------------------
 *
 * @Properties
 * 
 * array(
 * 		'_id' => 'ID',
 *		'public' => 'Public Key',
 *		'private' => 'Private Key',
 *		'PublicHex' => 'Public Hexa Key',
 *		'created_at' => 'Crated at time',
 * )
 */
class MRsakey extends MBase {
	/**
	 * Table name
	 */
	public $table = 'rsakey';
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