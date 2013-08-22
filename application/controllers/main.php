<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Game controller
 */
class Main extends CI_Controller {

	/**
	 * Construct controller
	 * Loading needed models
	 */
	public function __construct () {
		parent::__construct();

		$this->load->model('User_model');
		$this->load->model('Bonus_model');
		$this->load->model('Wallet_model');
	}

	/**
	 * Startpage - Just som info...
	 */
	public function index () {
		$page = $this->load->view('index', array(), true);
		$this->load->view('layout', array('__content' => $page));
	}

	/**
	 * Bonus Page - Add bonuses and list them
	 */
	public function bonuses () {
		// Add some form validation
		$this->form_validation->set_rules('name', 'Name',
				'trim|required|min_length[2]|max_length[255]|xss_clean');
		$this->form_validation->set_rules('trigger', 'Trigger',
				'trim|required|xss_clean');
		$this->form_validation->set_rules('reward_wallet_type', 'Reward wallet type',
				'trim|required|xss_clean');
		$this->form_validation->set_rules('value_of_reward', 'Value of reward',
				'trim|required|integer|xss_clean');
		$this->form_validation->set_rules('value_of_reward_type', 'Type of reward',
				'trim|required|xss_clean');
				$this->form_validation->set_rules('multiplier', 'Wagering multiplier',
				'trim|required|integer|xss_clean');
		$this->form_validation->set_rules('status', 'Age',
				'trim|required|xss_clean');

		// Create inputs
		$defaults = array(
			'name' => '',
			'trigger' => '',
			'reward_wallet_type' => 'BONUS',
			'value_of_reward' => '',
			'value_of_reward_type' => 'PERCENT',
			'multiplier' => 10,
			'status' => 'ACTIVE',
		);

		$values = array_merge($defaults, (array) $this->input->post(null, true));
		$inputs = array(
			'name' => array('name' => 'name', 'id' => 'name', 'value' => $values['name']),
			'trigger' => array(
					'name' => 'trigger',
					'options' => array('DEPOSIT' => 'DEPOSIT', 'LOGIN' => 'LOGIN'),
					'selected' => $values['trigger'],
					'extra' => 'id="trigger"'),
			'reward_wallet_type' => array(
					'name' => 'reward_wallet_type',
					'options' => array('BONUS' => 'BONUS', 'REALMONEY' => 'REALMONEY'),
					'selected' => $values['reward_wallet_type'],
					'extra' => 'id="reward_wallet_type"'),
			'value_of_reward' => array(
					'name' => 'value_of_reward',
					'id' => 'value_of_reward',
					'value' => $values['value_of_reward'],
					'class' => 'input-small'),
			'value_of_reward_type' => array(
					'name' => 'value_of_reward_type',
					'options' => array('PERCENT' => '%', 'EURO' => '€'),
					'selected' => $values['value_of_reward_type'],
					'extra' => 'id="value_of_reward_type" class="input-small"'),
			'multiplier' => array('name' => 'multiplier', 'id' => 'multiplier', 'value' => $values['multiplier']),
			'status' => array(
					'name' => 'status',
					'options' => array('ACTIVE' => 'ACTIVE', 'WAGERED' => 'WAGERED', 'DEPLETED' => 'DEPLETED'),
					'selected' => $values['status'],
					'extra' => 'id="status"'),
		);

		// If posted and run validation...
		if (false !== $this->form_validation->run()) {
			if ($values['trigger'] == 'LOGIN' && $values['value_of_reward_type'] == 'PERCENT') {
				$this->form_validation->set_post_validation_error('value_of_reward_type',
						'Cannot set value of reward to % when trigger is LOGIN');

			// Save bonus
			} elseif ($this->Bonus_model->save($values)) {
				$this->session->set_flashdata('success', 'Bonus was successfully added');
				redirect('main/bonuses', 'refresh');
			} else {
				$this->form_validation->set_post_validation_error('save_failed', 'Unable to save bonus');
			}
		}

		// Load view
		$bonuslist = $this->Bonus_model->loadCollection(null, array('name', 'ASC'));
		$page = $this->load->view('bonuses', array('inputs' => $inputs, 'bonuslist' => $bonuslist), true);
		$this->load->view('layout', array('__content' => $page));
	}

	/**
	 * Players Page for this controller.
	 */
	public function players () {
		// Add some form validation
		$this->form_validation->set_rules('username', 'Email',
				'trim|required|min_length[5]|max_length[255]|valid_email|xss_clean|is_unique[user.username]');
		$this->form_validation->set_rules('password', 'Password',
				'trim|required|min_length[5]|max_length[16]|xss_clean');
		$this->form_validation->set_rules('firstname', 'Firstname',
				'trim|required|min_length[2]|max_length[50]|xss_clean');
		$this->form_validation->set_rules('lastname', 'Lastname',
				'trim|required|min_length[2]|max_length[50]|xss_clean');
		$this->form_validation->set_rules('age', 'Age',
				'trim|integer|xss_clean');

		// Create inputs
		$defaults = array(
			'username' => '',
			'password' => '',
			'firstname' => '',
			'lastname' => '',
			'age' => 20,
			'gender' => 'm',
		);

		$values = array_merge($defaults, (array) $this->input->post(null, true));
		$inputs = array(
			'username' => array('name' => 'username', 'id' => 'username', 'value' => $values['username']),
			'password' => array('name' => 'password', 'id' => 'password', 'value' => $values['password']),
			'firstname' => array('name' => 'firstname', 'id' => 'firstname', 'value' => $values['firstname']),
			'lastname' => array('name' => 'lastname', 'id' => 'lastname', 'value' => $values['lastname']),
			'age' => array(
					'name' => 'age',
					'options' => array_combine(($r = range(18, 99)), $r),
					'selected' => $values['age'],
					'extra' => 'id="age"'),
			'gender_m' => array('name' => 'gender', 'id' => 'gender_m', 'value' => 'M'),
			'gender_f' => array('name' => 'gender', 'id' => 'gender_f', 'value' => 'F')
		);

		if ($values['gender'] == 'M') {
			$inputs['gender_f']['checked'] = true;
		} else {
			$inputs['gender_m']['checked'] = true;
		}

		// If posted and run validation...
		if (false !== $this->form_validation->run()) {

			// Save the new user
			if ($this->User_model->save($values)) {
				$this->session->set_flashdata('success', 'Player was successfully added');
				redirect('main/players', 'refresh');
			} else {
				$this->form_validation->set_post_validation_error('save_failed', 'Unable to save player');
			}
		}

		// Load view
		$userlist = $this->User_model->loadCollection(null, array('lastname', 'DESC'));
		$page = $this->load->view('players', array('inputs' => $inputs, 'userlist' => $userlist), true);
		$this->load->view('layout', array('__content' => $page));
	}

	/**
	 * Show player page - View a player's info and his/her wallets
	 */
	public function player_show ($id) {
		$id = (int) $id;

		// Check access
		if (! $id || ! $this->User_model->loadByID($id)) {
			show_404();
		} else {

			// Load the wallets
			$bonuswallets = $this->User_model->getBonusWallets();
			$realmoneywallet = $this->User_model->getRealmoneyWallet();

			// Load view
			$page = $this->load->view('player_show', array(
					'player' => $this->User_model,
					'realmoneywallet' => $realmoneywallet,
					'bonuswallets' => $bonuswallets),
					true);
			$this->load->view('layout', array('__content' => $page));
		}
	}

	/**
	 * Login page
	 */
	public function login () {
		// Already logged in
		if ($this->User_model->isLoggedIn()) {
			redirect('game/');
		}

		// Add some form validation
		$this->form_validation->set_rules('username', 'Email',
				'trim|required|valid_email|xss_clean');
		$this->form_validation->set_rules('password', 'Password',
				'trim|required|xss_clean');

		// Create inputs
		$defaults = array('username' => '', 'password' => '');
		$values = array_merge($defaults, (array) $this->input->post(null, true));
		$inputs = array(
			'username' => array('name' => 'username', 'id' => 'username', 'value' => $values['username']),
			'password' => array('name' => 'password', 'id' => 'password', 'value' => $values['password'])
		);

		// If posted and run validation...
		if (false !== $this->form_validation->run()) {

			// Login the user
			if ($this->User_model->login($values['username'], $values['password'])) {
				redirect('game/', 'refresh');
			} else {
				$this->form_validation->set_post_validation_error('login_failed', 'Unable to login');
			}
		}

		$page = $this->load->view('login', array('inputs' => $inputs, 'values' => $values), true);
		$this->load->view('layout', array('__content' => $page));
	}

	/**
	 * Logout page
	 */
	public function logout () {
		$this->User_model->logout();
		redirect('/');
	}
}

/* End of file main.php */
/* Location: ./application/controllers/main.php */