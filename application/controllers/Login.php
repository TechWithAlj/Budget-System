<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Login extends CI_Controller {

	public function __construct() {
    	parent::__construct();
    	$this->load->model('login_model', 'login');
    	$this->load->model('admin_model', 'admin');
	}

	public function _require_login(){
		$login = $this->session->userdata('bavi_purchasing');

		if(isset($login)){
			$user_type = decode($login['user_type_id']);
			if(decode($login['user_reset']) != 1){
				if($user_type == "1"){
					redirect('admin/materials');
				}elseif($user_type == "2"){
					redirect('business-center/materials');
				}elseif($user_type == "3"){
					redirect('unit');
				}elseif($user_type == "4"){
					redirect('admin/broiler-cost');
				}elseif($user_type == "5"){
					redirect('admin/production-cost');
				}elseif($user_type == "6"){
					redirect('region/');
				}elseif($user_type == "7"){
					redirect('national/');
				}else{
					$this->session->unset_userdata('bavi_purchasing');
					$this->session->sess_destroy();
					redirect();
				}
			}else{
				$this->session->unset_userdata('bavi_purchasing');
				$this->session->sess_destroy();
				redirect('login/change-password/' . $this->_encode($login['user_id']));
			}
		}
	}

	public function _active_year(){
		$get_budget = $this->admin->check_data('budget_active_tbl', array('budget_active_status' => 1), TRUE);
		$budget_year = $get_budget['info']->budget_active_year;
		return $budget_year;
	}

	public function index(){
		$this->_require_login();

		$data['year'] = $this->_active_year();
		$this->load->view('login/login_view', $data);
	}

	public function login_process(){

		if($_SERVER['REQUEST_METHOD'] == 'POST'){
			$email = clean_data($this->input->post('email'));
			$password = clean_data($this->input->post('password'));
			$where = array('user_email' => $email);
			$redirect = '';
			$check_login = $this->login->check_data('user_tbl', $where, TRUE);
			if($check_login['result'] == TRUE){
				$check_login['info']->user_password;
				if(decode($check_login['info']->user_password) == $password){

					$session = array(
						'user_id' => encode($check_login['info']->user_id),
						'user_type_id' => encode($check_login['info']->user_type_id),
						'user_reset' => encode($check_login['info']->user_reset)
					);
				
					if($check_login['info']->user_status_id == 1){
						$this->session->set_userdata('bavi_purchasing', $session);
						if($redirect == ''){
							$set_logs = array(
								'user_id' => $check_login['info']->user_id,
								'user_login_date' => date_now(),
								'user_login_status' => 1
							);

							$this->admin->insert_data('user_login_tbl', $set_logs);

							if($check_login['info']->user_type_id == 1){
								redirect('admin/materials');
							}elseif($check_login['info']->user_type_id == 2){
								redirect('business-center/materials');
							}elseif($check_login['info']->user_type_id == 3){
								redirect('unit');
							}elseif($check_login['info']->user_type_id == 4){
								redirect('admin/broiler-cost');
							}elseif($check_login['info']->user_type_id == 5){
								redirect('admin/commi-production-cost');
							}elseif($check_login['info']->user_type_id == 6){
								redirect('region/');
							}elseif($check_login['info']->user_type_id == 7){
								redirect('national/');
							}
						}else{
							redirect($redirect);
						}
					}elseif($check_login['info']->user_status == 2){
						$msg = '<div class="alert alert-danger">Your account has been deactivated contact your administrator.</div>';
						$this->session->set_flashdata('message', $msg);
						redirect('');
					}else{
						$msg = '<div class="alert alert-danger">Error please contact your administrator.</div>';
						$this->session->set_flashdata('message', $msg);
						redirect('');
					}
				}else{
					$msg = '<div class="alert alert-danger">Invalid email and password.</div>';
					$this->session->set_flashdata('message', $msg);
					redirect('');
				}
			
			}else{
				$msg = '<div class="alert alert-danger">Invalid email and password.</div>';
				$this->session->set_flashdata('message', $msg);
				redirect('');
			}
		}else{
			redirect('');
		}
	}

	public function change_password($id){
		$this->_require_login();
		$check_user = $this->login->check_data('user_tbl', array('user_id' => decode($id), 'user_reset' => 1));
		if($check_user == TRUE){
			$data['id'] = $id;
			$this->load->view('login/change_password_view', $data);
		}else{
			redirect();
		}
	}

	public function change_process(){
		if($_SERVER['REQUEST_METHOD'] == 'POST'){
			$user_id = clean_data(decode($this->input->post('id')));
			$password = clean_data($this->input->post('password'));
			$r_password = clean_data($this->input->post('r_password'));
			
			if($password == $r_password){

				$set_password = array(
					'user_password' => encode($password),
					'user_reset' => 0
				);
				
				$update_pass = $this->login->update_data('user_tbl', $set_password, array('user_id' => $user_id));

				if($update_pass == TRUE){
					$msg = '<div class="alert alert-success">Login now with your new password.</div>';
					
				}else{
					$msg = '<div class="alert alert-danger">Error please try again.</div>';
				}

				$this->session->set_flashdata('message', $msg);
				redirect();
			}
		}else{
			redirect();
		}
	}

	public function logout(){
		$this->session->unset_userdata('bavi_purchasing');
		$this->session->sess_destroy();
		redirect();
	}

	/*public function login_ldap(){
		$username = 'LD-BUDGETING';
		$password = "h9%9BPp";


		$ldapconfig['host'] = '192.168.64.10';//CHANGE THIS TO THE CORRECT LDAP SERVER
		$ldapconfig['port'] = '389';
		$ldapconfig['basedn'] = 'DC=bountyagro,DC=com,DC=ph';//CHANGE THIS TO THE CORRECT BASE DN
		$ldapconfig['usersdn'] = 'cn=users';//CHANGE THIS TO THE CORRECT USER OU/CN
		$ds = ldap_connect('ldap://' . $ldapconfig['host'], $ldapconfig['port']);

		ldap_set_option($ds, LDAP_OPT_PROTOCOL_VERSION, 3);
		ldap_set_option($ds, LDAP_OPT_REFERRALS, 0);
		ldap_set_option($ds, LDAP_OPT_NETWORK_TIMEOUT, 10);

		$dn = "uid=" . $username . "," . $ldapconfig['basedn'];
		
		if ($bind=ldap_bind($ds, $dn, $password)) {
		  echo("Login correct");//REPLACE THIS WITH THE CORRECT FUNCTION LIKE A REDIRECT;
		} else {

	 		echo "Login Failed: Please check your username or password";
		}
		
	}*/

	public function login_ldap(){
		$username = 'LD-BUDGETING';
		$password = 'h9%9BPp';

		/*$username = 'jtbelandres';
		$password = 'B0unty@gr0';*/

		$server = "192.168.64.10";  //this is the LDAP server you're connecting with
		$port = '389';
		$ds = ldap_connect("ldap://" . $server, $port); //always connect securely via LDAPS when possible

		ldap_set_option($ds, LDAP_OPT_PROTOCOL_VERSION, 3);
		ldap_set_option($ds, LDAP_OPT_REFERRALS, 0);

		//$dn = "uid=" . $username . ",cn=users,dc=bountyagro,dc=com,dc=ph";
		
		if ($ldapbind = @ldap_bind($ds, $username, $password)) {
		  echo("Login correct");//REPLACE THIS WITH THE CORRECT FUNCTION LIKE A REDIRECT;

		  $dn = "cn=users,dc=bountyagro,dc=com,dc=ph";
		  $filter = "mail=*";

		  $sr = ldap_search($ds, $dn, $filter);

		  
		  $results = ldap_get_entries($ds, $sr);

		  foreach($results as $row){
		  	
		  	echo $username = clean_data($row['samaccountname'][0]);
		  	echo '<br />';
		  	echo $email = $row['userprincipalname'][0];
			echo '<br />';
			

			$check_user = $this->admin->check_data('user_tbl', array('user_email' => $email), TRUE);
			echo $this->db->last_query();
			echo '<br />';
			if($check_user['result'] == TRUE){
				$user_id = $check_user['info']->user_id;

				$set = array('username_ldap' => utf8_encode($username));
				$where = array('user_id' => $user_id);

				$update_user = $this->admin->update_data('user_tbl', $set, $where);
				echo 'update user info';
			}

			echo '<br />';
		  }
		} else {

	 		echo "Login Failed: Please check your username or password";
		}
		
	}


}