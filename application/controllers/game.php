<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Game controller
 */
class Game extends CI_Controller {

	/**
	 * Construct controller
	 * Need to be logged in
	 * Loading needed models and seting up the logged in user
	 */
	public function __construct () {
		parent::__construct();

		if (! ($id = (int) $this->session->userdata('id'))) {
			redirect('main/login');
		}

		$this->load->model('User_model');
		$this->load->model('Bonus_model');
		$this->load->model('Wallet_model');
		$this->User_model->loadByID($id);
	}

	/**
	 * Game page - Attach bonus to logged in user
	 */
	public function index () {
		// Get all bonuses
		$bonuses = $this->Bonus_model->loadCollection(array('status' => 'ACTIVE'));

		// Get my bonus wallets
		$wallets = $this->User_model->getBonusWallets();

		// Get my real money wallet
		$realmoney = $this->User_model->getRealmoneyWallet();

		// Get possible current bonus wallet
		$bonus = $this->User_model->getCurrentBonusWallet();

		if ($bonus) {
			redirect('game/play');
		}

		// Create arrays for bonus selectboxes
		$d_opts = array(0 => '- Choose Deposit Bonus -');
		$l_opts = array(0 => '- Choose Login Bonus -');

		foreach ($bonuses as $o) {
			$_bid = $o->get('id');

			if ($o->get('trigger') == 'DEPOSIT') {
				$d_opts[$_bid] = $o->get('name');
			} else {
				$l_opts[$_bid] = $o->get('name');
			}
		}

		// Remove used bonuses
		foreach ($wallets as $w) {
			$_bid = $w->get('bonus_id');
			if (isset($d_opts[$_bid])) {
				unset($d_opts[$_bid]);
			}
			if (isset($l_opts[$_bid])) {
				unset($l_opts[$_bid]);
			}
		}

		// Create inputs
		$inputs = array(
			'login_bonus_id' => array(
				'name' => 'login_bonus_id',
				'options' => $l_opts,
				'selected' => $this->input->post('login_bonus_id'),
				'disabled' => 0,
				'extra' => 'id="login_bonus_id" class="input-large"'),
			'deposit_bonus_id' => array(
				'name' => 'deposit_bonus_id',
				'options' => $d_opts,
				'selected' => $this->input->post('deposit_bonus_id'),
				'disabled' => 0,
				'extra' => 'id="deposit_bonus_id" class="input-large"'),
			'deposit_amount' => array(
				'name' => 'deposit_amount',
				'id' => 'deposit_amount',
				'value' => $this->input->post('deposit_amount'),
				'class' => 'input-large'),
		);

		// Add some form validation
		$this->form_validation->set_rules('login_bonus_id', 'Login bonus', 'trim|xss_clean');
		$this->form_validation->set_rules('deposit_bonus_id', 'Deposit bonus', 'trim|xss_clean');
		$this->form_validation->set_rules('deposit_amount', 'Deposit amount', 'trim|numeric|xss_clean');

		// If posted and run validation...
		if (false !== $this->form_validation->run()) {

			// Add login bonus
			if ($this->input->post('addLoginBonus')) {
				$id = $this->input->post('login_bonus_id');

				if (! $id || ! isset($l_opts[$id])) {
					$this->form_validation->set_post_validation_error('login_bonus_id',
							'Login bonus does not exist');
				} else {

					// Attach login bonus to user
					if ($this->User_model->addLoginBonus($bonuses[$id])) {
						$this->session->set_flashdata('success', 'Login bonus was successfully attached');
						redirect('game', 'refresh');
					} else {
						$this->form_validation->set_post_validation_error('login_bonus_id',
								'Login bonus could not be created');
					}
				}

			// Add deposit bonus
			} elseif ($this->input->post('addDepositBonus')) {
				$id = $this->input->post('deposit_bonus_id');
				$amount = $this->input->post('deposit_amount');

				if (! $id || ! isset($d_opts[$id])) {
					$this->form_validation->set_post_validation_error('deposit_bonus_id',
							'Deposit bonus does not exist');
				} elseif (! $amount || ! is_numeric($amount)) {
					$this->form_validation->set_post_validation_error('deposit_amount',
							'Enter a valid deposit amount');
				} else {

					// Attach login bonus to user
					if ($this->User_model->addDepositBonus($bonuses[$id], $amount)) {
						$this->session->set_flashdata('success', 'Deposit bonus was successfully attached');
						redirect('game', 'refresh');
					} else {
						$this->form_validation->set_post_validation_error('deposit_bonus_id',
								'Deposit bonus could not be created');
					}
				}
			}
		}

		// Load view
		$dvars = array('inputs' => $inputs, 'realmoney' => $realmoney, 'bonus' => $bonus);
		$page = $this->load->view('game', $dvars, true);
		$this->load->view('layout', array('__content' => $page));
	}

	/**
	 * Play game page - Spin the wheel
	 */
	public function play () {
		// Get my real money wallet
		$realmoney = $this->User_model->getRealmoneyWallet();

		// Get possible current bonus wallet
		$bonus = $this->User_model->getCurrentBonusWallet();

		// Do we have any money at all...
		$tot = $realmoney->get('current_value') + ($bonus ? $bonus->get('current_value') : 0);

		if ($tot <= 0) {
			$this->session->set_flashdata('error', 'You have no money to play for...');
			redirect('game', 'refresh');
		}

		// Load view
		$dvars = array('realmoney' => $realmoney, 'bonus' => $bonus);
		$page = $this->load->view('game_play', $dvars, true);
		$this->load->view('layout', array('__content' => $page));
	}

	/**
	 * Spins page - Spins the wheel, ajax calls this.
	 */
	public function spin () {
		$amount = (int) $this->input->post('amount');
		$retval = $this->User_model->spin($amount);

		echo json_encode($retval);
		exit;
	}

	/**
	 * Forfeit bonus page - Removes the current bonus
	 */
	public function forfeit () {
		// Check for the current bonus and remove it...
		if ($bonus = $this->User_model->getCurrentBonusWallet()) {
			$bonus->set('status', 'DEPLETED');
			$bonus->set('current_value', 0);
			if ($bonus->save()) {
				$this->session->set_flashdata('success', 'Bonus was forfeited');
			}
		}

		redirect('game/play', 'refresh');
	}
}

/* End of file game.php */
/* Location: ./application/controllers/game.php */
