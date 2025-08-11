<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class National extends CI_Controller {

	public function __construct() {
    	parent::__construct();
    	$this->load->model('admin_model', 'admin');
	}

	public function _require_login(){
		
		$login = $this->session->userdata('bavi_purchasing');

		if(isset($login)){
			$user_type = decode($login['user_type_id']);
			if(decode($login['user_reset']) != 1){
				if($user_type == "1"){
					redirect('admin');
				}elseif($user_type == "2"){
					redirect('business-center');
				}elseif($user_type == "3"){
					redirect('unit');
				}elseif($user_type == "4"){
					redirect('admin/broiler-cost');
				}elseif($user_type == "5"){
					redirect('admin/production-cost');
				}elseif($user_type == "6"){
					redirect('region');
				}elseif($user_type == "7"){
					return $login;
				}else{
					$this->session->unset_userdata('bavi_purchasing');
					$this->session->sess_destroy();
					redirect();
				}
			}else{
				$this->session->unset_userdata('bavi_purchasing');
				$this->session->sess_destroy();
				redirect('login/change-password/' . $login['user_id']);
			}
		}else{
			$this->session->unset_userdata('bavi_purchasing');
			$this->session->sess_destroy();
			redirect();
		}
	}

	public function get_user_info(){
		$info = $this->_require_login();
		$user_id = decode($info['user_id']);
		$join_info = array(
			'user_region_tbl b' => 'a.user_id = b.user_id AND a.user_status_id = 1 AND b.user_region_status = 1 AND a.user_id = ' . $user_id,
			'region_tbl c' => 'b.region_id = c.region_id',
			'bc_tbl d' => 'c.region_id = d.region_id',
			'cost_center_tbl e' => 'd.cost_center_code = e.cost_center_code AND e.cost_center_status = 1'
		);

		$get_info = $this->admin->get_join('user_tbl a', $join_info, FALSE, FALSE, FALSE, '*, d.bc_id as bc');
		$bc_data = '';
		$cost_center_data = '';
		$count = 1;
		foreach($get_info as $row){
			$data['cost_center_id'] = $row->cost_center_id;
			$data['region_id'] = $row->region_id;

			if(count($get_info) == $count){
				$bc_data .= $row->bc;
				$cost_center_data .= $row->cost_center_id;
			}else{
				$bc_data .= $row->bc . ',';
				$cost_center_data .= $row->cost_center_id . ',';
			}

			$count++;
		}

		$data['cost_center_data'] = $cost_center_data;
		$data['bc_data'] = $bc_data;

		return $data;
	}

	public function _active_year(){
		$get_budget = $this->admin->check_data('budget_active_tbl', array('budget_active_status' => 1), TRUE);
		$budget_year = $get_budget['info']->budget_active_year;
		return $budget_year;
	}

	public function logout(){
		$this->session->unset_userdata('bavi_purchasing');
		$this->session->sess_destroy();
		redirect();
	}

	public function index($year = null){
		$info = $this->_require_login();
		$data['title'] = 'Dashboard';

		if($year == null){
			$year = $this->_active_year();
		}

		$data['year'] = $year;
 		$join_bc_trans = array(
			'bc_tbl b' => 'a.bc_id = b.bc_id AND a.dashboard_bc_trans_year = ' . $year,
			'dashboard_transaction_status_tbl c' => 'a.dashboard_trans_status_id = c.dashboard_trans_status_id',
			'user_tbl d' => 'a.user_id = d.user_id'
		);

		$data['trans_bc'] = $this->admin->get_join('dashboard_bc_transaction_tbl a', $join_bc_trans, FALSE,'a.dashboard_bc_trans_added DESC');

		$join_unit_trans = array(
			'cost_center_tbl b' => 'a.cost_center_id = b.cost_center_id AND a.dashboard_unit_trans_year = ' . $year,
			'dashboard_transaction_status_tbl c' => 'a.dashboard_trans_status_id = c.dashboard_trans_status_id',
			'user_tbl d' => 'a.user_id = d.user_id'
		);

		$data['trans_unit'] = $this->admin->get_join('dashboard_unit_transaction_tbl a', $join_unit_trans, FALSE,'a.dashboard_unit_trans_added DESC');

		$select_region = '
			a.region_name, a.region_id,
			(SELECT COUNT(DISTINCT(y.bc_id)) FROM dashboard_bc_transaction_tbl x, bc_tbl y WHERE x.bc_id = y.bc_id AND a.region_id = y.region_id AND x.dashboard_trans_status_id = 3 AND x.dashboard_bc_trans_year = ' . $year . ') as count_completed

		';
		$data['trans_region'] = $this->admin->get_data('region_tbl a', array('region_status' => 1), FALSE, $select_region);

		$data['bc'] = $this->admin->get_data('bc_tbl', array('bc_status' => 1));
		$data['unit'] = $this->admin->get_data('company_unit_tbl', array('company_unit_status' => 1));

		$data['content'] = $this->load->view('national/national_dashboard_transaction_content', $data , TRUE);		
		$this->load->view('national/templates', $data);
	}
}