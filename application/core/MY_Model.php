<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Override the default CI_Model
 */
class MY_Model extends CI_Model {
	/**
	 * Data holder
	 */
	protected $data = array();
	protected $dbtable;

	public function __construct() {
		parent::__construct();
	}

	/**
	 * Load a user by id
	 */
	public function loadByID ($id) {
		$retval = false;

		if ($query = $this->db->get_where($this->dbtable, array('id' => (int) $id))) {
			foreach ($query->result() as $obj) {
				$this->loadByObject($obj);
				$retval = true;
			}
		}

		return $retval;
	}

	/**
	 * Load a user by array
	 */
	public function loadByData ($array) {
		$this->setArray($array);
	}

	/**
	 * Load a user by object
	 */
	public function loadByObject ($object) {
		$this->setArray(get_object_vars($object));
	}

	/**
	 * Load a collection of users
	 */
	public function loadCollection ($where = array(), $order = null, $offset = 0, $limit = 100) {
		$retval = array();

		if ($order) {
			$this->db->order_by($order[0], $order[1]);
		}

		if (! $where) {
			$query = $this->db->get($this->dbtable, $limit, $offset);
		} else {
			$query = $this->db->get_where($this->dbtable, $where, $limit, $offset);
		}


		if ($query) {
			$cls = get_class($this);

			foreach ($query->result() as $obj) {
				$retval[$obj->id] = new $cls();
				$retval[$obj->id]->loadByObject($obj);
			}
		}

		return $retval;
	}


	/**
	 * Delete key from data
	 */
	public function del ($key) {
		if ($this->has($key)) {
			unset($this->data[$key]);
		}
		return $this;
	}

	/**
	 * Add key/value to data
	 */
	public function set ($key, $val) {
		$this->data[$key] = $val;
		return $this;
	}

	/**
	 * Get key from data
	 */
	public function get ($key, $default = '') {
		return array_key_exists($key, $this->data) ? $this->data[$key] : $default;
	}

	/**
	 * Check if key is set
	 */
	public function has ($key) {
		return array_key_exists($key, $this->data);
	}

	/**
	 * Set an array to data
	 */
	public function setArray ($array, $merge = false) {
		if ($merge) {
			$this->data = array_merge($this->data, $array);
		} else {
			$this->data = $array;
		}
		return $this;
	}

	/**
	 * Get the data array
	 */
	public function getArray () {
		return $this->data;
	}

	/**
	 * Load available columns from database
	 */
	protected function cols () {
		static $cols = null;

		if (null == $cols) {
			$cols = array_flip($this->db->list_fields($this->dbtable));
		}

		return $cols;
	}
}