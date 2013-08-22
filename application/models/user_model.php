<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * A simple user class
 *
 * Extends My custom model see (application/core/MY_Model)
 */
class User_model extends MY_Model {
	/**
	 * Data holder
	 */
	protected $data = array();

	/**
	 * Setup database table
	 */
	protected $dbtable = 'user';

	/**
	 * Construct the object
	 */
	public function __construct () {
		parent::__construct();
	}

	/**
	 * Save the user
	 *
	 * @param mixed $values
	 * @return object|false
	 */
	public function save ($values = null) {
		$retval = false;

		$cols = $this->cols();

		if (null === $values) {
			$values = $this->getArray();
		}

		if ($values && ($data = array_intersect_key($values, $cols))) {

			// Hash the password (should be more safe, using salt or something...)
			if (! empty($values['password'])) {
				$data['password'] = hash('sha1', $values['password']);
			}

			// If this is a new user - insert
			if (! ($id = $this->get('id'))) {
				$this->db->set($data);

				if ($this->db->insert('user')) {
					if ($id = (int) $this->db->insert_id()) {
						$this->setArray($data)->set('id', $id);
	
						// Add a wallet for this user
						if ($this->Wallet_model->createUserWallet($id)) {
							$retval = $this;
						} else {

							// Cound not add the wallet, so we delete the user...
							$this->db->delete('user', array('id' => $id));
						}
					}
				}

			// If update - not really tested yet - as we don't have any update funtionality...
			} else {
				$this->db->set($data);
				$this->db->where('id', $id);

				if ($this->db->update('user')) {
					$this->setArray($data, true);
					$retval = $this;
				}
			}
		}

		return $retval; 
	}

	/**
	 * WORKFLOW ON Spin the Wheel

	if bet > realmoney + bonus
		- Nothing to bet with
	elseif bet <= realmoney
		- calculate new current value for real money wallet

		if have bonus
			if bonus wagering is done
				- update bonus status
				- "reset" the bonus wallet
				- transfer the rest of money to realmoney wallet
			else
				- update bonus wagering amount
			endif
		endif

		- set new current value to real money wallet

	elsif bet <= realmoney + bonusmoney

		if have bonus : which we do here...
			- calculate new current value for bonus wallet
			- set new current value on bonus wallet up til initial bonus value

			if there is a rest amount
				- add it to the current value of the real money wallet
			endif

			if bonus wagering is done
				- update bonus status
				- "reset" the bonus wallet
				- transfer the rest of money to realmoney wallet
			else
				- update bonus wagering amount
			endif
		endif;

		- set new current value to real money wallet
	endif
	*/

	/**
	 * Spin the wheel
	 *
	 * @param double $bet
	 * @return array
	 */
	public function spin ($bet) {
		$retval = array();

		// Calc win/lose
		$success = rand(0, 1);
		$winning = $success ? ($bet * rand(1, 3)) : 0;

		$rm = $this->getRealmoneyWallet();
		$bm = $this->getCurrentBonusWallet();

		$sum_rm = $rm->get('current_value');
		$sum_bm = $bm ? $bm->get('current_value') : 0;

		$retval['msg'][] = sprintf('You bet &euro;%.2f.', $bet);
		$retval['msg'][] = $success
			? sprintf('You won &euro;%s.', $winning)
			: sprintf('You lost.');

		// Not enough funds
		if ($bet > $sum_rm + $sum_bm) {

			$retval['msg'] = array('You have not enough money to bet.');

		// Bet is smaller than the real money wallet...
		} elseif ($bet <= $sum_rm) {
			// Set realmoney amount
			$new_rm = $sum_rm - $bet + $winning;

			// If an active bonus - Update it
			if ($bm) {
				$cur_wg = $bm->get('wagered_value');
				$new_wg = max(0, $cur_wg - $bet);

				// Is bonus wagering done ...
				// ...change status and add the rest to the realmoney wallet
				if (! $new_wg) {
					$retval['msg'][] = 'You cleared your bonus.';

					$bm->set('status', 'DEPLETED');
					if ($rest = $bm->get('current_value')) {
						$retval['msg'][] = sprintf('Bonus money %.2f was added to your realmoney wallet.',
								$rest);
						$new_rm += $rest;

						$bm->set('current_value', 0);
					}
				}

				$bm->set('wagered_value', $new_wg);
				$bm->save();

				$retval['bonus'] = $bm->getArray();
			}

			// Now save the real money wallet
			$rm->set('current_value', $new_rm);
			$rm->save();

			$retval['realmoney'] = $rm->getArray();


		// Bet is smaller than the real money wallet + the bonus wallet...
		} elseif ($bet <= $sum_rm + $sum_bm) {
			// Use realmoney + bonusmoney

			$retval['msg'][] = 'Should do something...';

			$new_rm = 0;
			$del_bm = $bet - $sum_rm;

			if ($bm) {
				$cur_wg = $bm->get('wagered_value');
				$new_wg = max(0, $cur_wg - $bet);

				$ini_bm = $bm->get('initial_value');
				$cur_bm = $bm->get('current_value');

				$bm->set('current_value', $cur_bm - $del_bm);

				if (($cur_bm - $del_bm) + $winning > $ini_bm) {
					$bm->set('current_value', $ini_bm);
					$new_rm += (($cur_bm - $del_bm) + $winning) - $ini_bm;
				}

				// Is bonus wagering done ...
				// ...change status and add the rest to the realmoney wallet
				if (! $new_wg) {
					$retval['msg'][] = 'You cleared your bonus.';

					$bm->set('status', 'DEPLETED');
					if ($cur_bm) {
						$retval['msg'][] = sprintf('Bonus money %.2f was added to your realmoney wallet.',
								$rest);
						$new_rm += $rest;

						$bm->set('current_value', 0);
					}
				}

				$bm->set('wagered_value', $new_wg);
				$bm->save();

				$retval['bonus'] = $bm->getArray();
			}

			// Now save the real money wallet
			$rm->set('current_value', $new_rm);
			$rm->save();

			$retval['realmoney'] = $rm->getArray();
		} else {

			$retval['msg'] = array('You have not enough money to bet.');
		}

		return $retval;
	}

	/**
	 * Get the realmoney wallet
	 *
	 * @return object
	 */
	public function getRealmoneyWallet () {
		$this->loadWallets();

		if ($wallets = $this->get('__wallets', array())) {
			return array_shift($wallets);
		}

		return array();
	}

	/**
	 * Get the bonus wallets
	 *
	 * @return array
	 */
	public function getBonusWallets () {
		$this->loadWallets();

		if ($wallets = $this->get('__wallets', array())) {
			foreach ($wallets as $key => $w) {
				if ($w->get('bonus_id') == 0) {
					unset($wallets[$key]);
				}
			}

			return $wallets;
		}

		return array();
	}

	/**
	 * Get the current realmoney wallet
	 *
	 * @return object
	 */
	public function getCurrentBonusWallet () {
		if ($wallets = $this->getBonusWallets()) {
			foreach ($wallets as $w) {
				if ($w->get('status') == 'ACTIVE') {
					return $w;
				}
			}
		}

		return false;
	}

	/**
	 * Load wallets - internally cached
	 */
	protected function loadWallets () {
		if ($this->has('__wallets') || ! ($id = (int) $this->get('id'))) {
			return;
		}

		$this->set('__wallets', $this->Wallet_model->loadCollection(array('user_id' => $id)));
	}


	/**
	 * WORKFLOW ON Add bonus

	if trigger == DEPOSIT
		if amount <= 0
			FAIL: No money deposited
		else
			- Add a new wallet connected to user and bonus_id

			if value_of_reward_type == PERCENT
				- Add calculated amount to the wallet
			elseif value_of_reward_type == EURO
				- Add amount to wallet
			endif

			if reward_wallet_type == REALMONEY
				- move wallet amount to my realmoney wallet
				- set wallet depleted
			elseif reward_wallet_type == BONUS
				Do noting more...
			endif
		endif

	elseif trigger == LOGIN
		if value_of_reward_type == PERCENT
			FAIL: Cannot add PERCENT on LOGIN
		elseif value_of_reward_type == EURO
			- Add a new wallet connected to user and bonus_id
			- Add amount to the wallet

			if reward_wallet_type == REALMONEY
				- move wallet amount to my realmoney wallet
				- set wallet depleted
			elseif reward_wallet_type == BONUS
				Do nothing more...
			endif
		endif
	endif
	*/

	/**
	 * Add a login bonus
	 *
	 * @param object $bonus
	 * @return bool
	 */
	public function addLoginBonus ($bonus) {
		$retval = false;

		if (! is_object($bonus)) {
			return false;
		}

		if ($bonus->get('trigger') != 'LOGIN') {
			return false;
		}

		if ($bonus->get('value_of_reward_type') == 'PERCENT') {
			return false;
		}

		return $this->addBonus($bonus, $bonus->get('value_of_reward'));
	}

	/**
	 * Add a deposit bonus
	 *
	 * @param object $bonus
	 * @param double $amount
	 * @return bool
	 */
	public function addDepositBonus ($bonus, $amount) {
		if (! is_object($bonus)) {
			return false;
		}

		if ($bonus->get('trigger') != 'DEPOSIT') {
			return false;
		}

		if ($amount < 0 || ! is_numeric($amount)) {
			return false;
		}

		$saveamount = $amount;
		if ($bonus->get('value_of_reward_type') == 'PERCENT') {
			$saveamount = $amount * ((double) $bonus->get('value_of_reward') / 100);
		}

		return $this->addBonus($bonus, $saveamount);
	}

	/**
	 * Really add the bonus to database
	 *
	 * @param object $bonus
	 * @param double $amount
	 * @return bool
	 */
	protected function addBonus ($bonus, $amount) {
		$retval = false;

		if (! ($wallet = $this->Wallet_model->createUserWallet($this->get('id'), $bonus->get('id')))) {
			return $retval;
		}

		$save = array(
			'initial_value' => $amount,
			'current_value' => $amount,
			'wagered_value' => 0
		);

		if ($bonus->get('reward_wallet_type') == 'BONUS') {
			$save['wagered_value'] = $amount * (int) $bonus->get('multiplier');

			if ($wallet->save($save)) {
				$this->del('__wallets');

				$retval = true;
			}
		} else {
			$realmoney = $this->User_model->getRealmoneyWallet();
			$realmoney->set('current_value',
					$realmoney->get('current_value') + $save['current_value']);
			if ($realmoney->save()) {

				$save['current_value'] = 0;
				$save['status'] = 'DEPLETED';

				if ($wallet->save($save)) {
					$this->del('__wallets');

					$retval = true;
				}
			}
		}

		return $retval;
	}

	/**
	 * Check if user is logged in
	 *
	 * @return int
	 */
	public function isLoggedIn () {
		return $this->session->userdata('id');
	}

	/**
	 * Login the user
	 *
	 * @param string $username
	 * @param string $password
	 * @return bool
	 */
	public function login ($username, $password) {
		$query = $this->db->get_where('user', array(
				'username' => $username,
				'password' => hash('sha1', $password)),
				1);

		if ($data = $query->row()) {
			$this->loadByObject($data);
			$this->session->set_userdata('id', $this->get('id'));
			$this->session->set_userdata('username', $this->get('username'));
			return true;
		}

		return false;
	}

	/**
	 * Logout the user
	 *
	 * @return bool
	 */
	public function logout () {
		$this->session->unset_userdata('id');
		$this->session->unset_userdata('username');
		$this->session->sess_destroy();
		return true;
	}
}