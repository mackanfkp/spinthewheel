<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * A simple wallet class
 *
 * Extends My custom model see (application/core/MY_Model)
 */
class Wallet_model extends MY_Model {
	/**
	 * Data holder
	 */
	protected $data = array();

	/**
	 * Setup database table
	 */
	protected $dbtable = 'wallet';

	public function __construct () {
		parent::__construct();
	}

	/**
	 * Save the wallet
	 */
	public function save ($values = null) {
		$retval = false;

		$cols = $this->cols();

		if (null === $values) {
			$values = $this->getArray();

			if (isset($values['date_create'])) {
				unset($values['date_create']);
			}
			if (isset($values['date_update'])) {
				unset($values['date_update']);
			}
		}

		if ($values && ($data = array_intersect_key($values, $cols))) {
			if (! ($id = $this->get('id'))) {
				$this->db->set($data);

				if ($this->db->insert('wallet')) {
					$this->setArray($data)->set('id', $this->db->insert_id());
					$retval = $this;
				}
			} else {
				$this->db->set($data);
				$this->db->where('id', $id);

				if ($this->db->update('wallet')) {
					$this->setArray($data, true);
					$retval = $this;
				}
			}
		}

		return $retval; 
	}

	/**
	 * Create a wallet connected to a user
	 */
	public function createUserWallet ($user_id, $bonus_id = 0) {
		$retval = false;

		$data = array(
			'user_id' => (int) $user_id,
			'bonus_id' => (int) $bonus_id
		);

		$this->db->set($data);

		if ($this->db->insert('wallet')) {
			if ($this->loadByID($this->db->insert_id())) {
				$retval = $this;
			}
		}

		return $retval;
	}
}