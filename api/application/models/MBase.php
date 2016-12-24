<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class MBase extends CI_Model {
	/**
	 * Table name
	 */
	public $table;
	// ----------------------------------------------------------------
	/**
	 * --------------------------------------------
	 * Constructor
	 * --------------------------------------------
	 */
	function __construct() {
		parent::__construct();
		$this->load->database();
	}
	// ----------------------------------------------------------------
	/**
	 * --------------------------------------------
	 * Insert Row
	 * --------------------------------------------
	 *
	 * @param array $data data needed insert
	 *
	 * @return insert result
	 */
	public function insert($data) {
		return $this->db->insert($this->table, $data);
	}
	// ----------------------------------------------------------------
	/**
	 * --------------------------------------------
	 * Get Rows
	 * --------------------------------------------
	 *
	 * @param array $filter filter's conditional
	 * @param mixed $select fields needed get
	 *
	 * @return rows result
	 */
	public function get($filter, $select = '*') {
		if(is_array($select)) {
			$select = implode(',', $select);
		}
		// echo $this->db->select($select)->where($filter)->from($this->table)->get_compiled_select();
		return $this->db->select($select)->where($filter)->from($this->table)->get();
	}
	// ----------------------------------------------------------------
	/**
	 * --------------------------------------------
	 * Exists row
	 * --------------------------------------------
	 *
	 * @param array $filter filter's conditional
	 * @param mixed $select fields needed get (array/string)
	 *
	 * @return row result
	 */
	public function exists($filter, $select = null) {
		if(is_array($select)) {
			$select = implode(',', $select);
		}
		// echo $this->db->select($select)->where($filter)->from($this->table)->get_compiled_select();
		$exists = (object) $this->db->select($select)->where($filter)->from($this->table)->get();

		if($exists->ok && !empty($exists->result))  {
			if(!empty($select)) {
				return $exists->result;
			}else {
				return true;
			}
		}else {
			return false;
		}
	}
	// ----------------------------------------------------------------
	/**
	 * --------------------------------------------
	 * Update Data
	 * --------------------------------------------
	 *
	 * @param array $data data set 
	 * @param array $filter where filter
	 * @param bool  $default 
	 *
	 * @return update result
	 */
	public function update($set, $filter, $default = false) {
		try {
			$update = $this->db->update($this->table, $set, $filter);

			if($default) {
				return $update;
			}

			if($update['ok']) {
				return true;
			}else {
				return false;
			}
		} catch (Exception $e) {
			return false;
		}
	}
	// ----------------------------------------------------------------
	/**
	 * --------------------------------------------
	 * Select Max
	 * --------------------------------------------
	 *
	 * @param string $field
	 * @param string $alias
	 * @param array  $filter
	 *
	 * @return select max result
	 */
	public function select_max($field, $alias = '', $filter = null) {
		return $this->db->select_max($field)->where($filter)->from($this->table)->get();
	}

}