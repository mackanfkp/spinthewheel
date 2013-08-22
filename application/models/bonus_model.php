<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * A simple bonus class
 *
 * Extends My custom model see (application/core/MY_Model)
 */
class Bonus_model extends MY_Model {
	/**
	 * Data holder
	 */
	protected $data = array();

	/**
	 * Setup database table
	 */
	protected $dbtable = 'bonus';

	public function __construct () {
		parent::__construct();
	}

	/**
	 * Save the bonus
	 */
	public function save ($values = null) {
		$retval = false;

		$cols = $this->cols();

		if (null === $values) {
			$values = $this->getArray();
		}

		if ($values && ($data = array_intersect_key($values, $cols))) {

			if (! ($id = $this->get('id'))) {
				$this->db->set($data);

				if ($this->db->insert('bonus')) {
					$this->setArray($data)->set('id', $this->db->insert_id());
					$retval = $this;
				}
			} else {
				$this->db->set($data);
				$this->db->where('id', $id);

				if ($this->db->update('bonus')) {
					$this->setArray($data, true);
					$retval = $this;
				}
			}
		}

		return $retval; 
	}
}