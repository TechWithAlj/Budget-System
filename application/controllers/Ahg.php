<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Ahg extends CI_Controller {

	public function __construct() {
    	parent::__construct();
    	$this->load->model('admin_model', 'admin');
	}

	public function _active_year(){
		$get_budget = $this->admin->check_data('budget_active_tbl', array('budget_active_status' => 1), TRUE);
		$budget_year = $get_budget['info']->budget_active_year;
		return $budget_year;
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

	public function logout(){
		$this->session->unset_userdata('bavi_purchasing');
		$this->session->sess_destroy();
		redirect();
	}

	public function index(){
		$this->broiler_cost();
	}

	public function broiler_cost(){
		$info = $this->_require_login();
		$join_broiler_group = array(
			'user_tbl c' => 'a.created_by = c.user_id'
		);
		$data['broiler_group'] = $this->admin->get_join('broiler_group_tbl a', $join_broiler_group, FALSE,'a.broiler_group_name ASC', FALSE);

		$join_broiler_config = array(
			'bc_tbl b' => 'a.bc_id = b.bc_id',
			'user_tbl c' => 'a.created_by = c.user_id'
		);
		$data['broiler_config'] = $this->admin->get_join('broiler_config_tbl a', $join_broiler_config, FALSE,'a.broiler_subgroup_name ASC', FALSE);

		$data['bc'] = $this->admin->get_data('bc_tbl', 'bc_status=1');
		$data['title'] = 'Broiler Cost';
		$data['content'] = $this->load->view('ahg/ahg_broiler_cost_content', $data , TRUE);
		$this->load->view('ahg/templates', $data);
	}

	public function view_broiler_group($id){
		$info = $this->_require_login();
		$data['broiler_group_id'] = decode($id);
		$broiler_group_id = decode($id);
		$data['title'] = 'Broiler Cost';

		$join_broiler_group = array(
			'broiler_subgroup_tbl b' => 'a.broiler_group_id = b.broiler_group_id and b.broiler_group_id ='.$broiler_group_id,
			'amount_type_tbl c' => 'c.amount_type_id = b.amount_type_id',
			'status_tbl d' => 'd.status_id = a.broiler_group_status and a.broiler_group_status = 1'
		);
		$data['broiler_subgroup'] = $this->admin->get_join('broiler_group_tbl a', $join_broiler_group, FALSE,'b.broiler_subgroup_name ASC');

		$data['content'] = $this->load->view('ahg/ahg_view_broiler_group_content', $data , TRUE);
		$this->load->view('ahg/templates', $data);
	}

	public function view_broiler_config($id){
		$info = $this->_require_login();
		$data['bc_id'] = decode($id);
		$bc_id = decode($id);
		$data['title'] = 'Broiler Cost';

		$join_broiler_config = array(
			'bc_tbl b' => 'a.bc_id = b.bc_id and a.bc_id ='.$bc_id,
			'user_tbl c' => 'a.created_by = c.user_id',
			'status_tbl d' => 'd.status_id = a.broiler_config_status and a.broiler_config_status = 1'
		);
		$data['broiler_config'] = $this->admin->get_join('broiler_config_tbl a', $join_broiler_config, FALSE,'a.broiler_subgroup_name ASC', 'a.broiler_subgroup_name');

		$data['content'] = $this->load->view('ahg/ahg_broiler_config_content', $data , TRUE);
		$this->load->view('ahg/templates', $data);
	}

	public function new_broiler_config($id){
		$info = $this->_require_login();
		$data['bc_id'] = $id;
		$year = $this->_active_year();
		$bc_id = decode($id);
		$data['title'] = 'Broiler Cost';
		$sql = 'SELECT
					*
				FROM
					broiler_subgroup_tbl a
				INNER JOIN broiler_group_tbl b ON a.broiler_group_id = b.broiler_group_id
				AND b.broiler_group_status = 1
				AND a.config_show = 1
				WHERE
					broiler_subgroup_name NOT IN (
						SELECT
							broiler_subgroup_name
						FROM
							broiler_config_tbl
						WHERE
							broiler_config_status = 1
						AND bc_id = '.$bc_id.'
						AND YEAR (config_date) = '.$year.'
						GROUP BY
							broiler_subgroup_name
					)';
		
		$data['config_subgroup'] = $this->admin->get_query($sql);
		$data['content'] = $this->load->view('ahg/ahg_add_broiler_config_content', $data , TRUE);
		$this->load->view('ahg/templates', $data);
	}

	public function edit_broiler_config($item_name, $bc_id){
		$info = $this->_require_login();
		$broiler_subgroup_name = decode($item_name);
		$data['bc_id'] = $bc_id;
		$bc_id = decode($bc_id);
		$data['title'] = 'Broiler Cost';
		$year = $this->_active_year();
		$data['config_subgroup'] = $this->admin->get_query("SELECT
											broiler_config_id,
											broiler_subgroup_name,
											(
												SELECT
													a.config_qty
												FROM
													broiler_config_tbl a
												WHERE
													a.broiler_subgroup_name = '$broiler_subgroup_name'
												AND a.bc_id = ".$bc_id."
												AND MONTH (a.config_date) = 1
												AND YEAR (a.config_date) = ".$year."
												AND a.broiler_config_status = 1
											) AS jan,
											(
												SELECT
													a.config_qty
												FROM
													broiler_config_tbl a
												WHERE
													a.broiler_subgroup_name = '$broiler_subgroup_name'
												AND a.bc_id = ".$bc_id."
												AND MONTH (a.config_date) = 2
												AND YEAR (a.config_date) = ".$year."
												AND a.broiler_config_status = 1
											) AS feb,
											(
												SELECT
													a.config_qty
												FROM
													broiler_config_tbl a
												WHERE
													a.broiler_subgroup_name = '$broiler_subgroup_name'
												AND a.bc_id = ".$bc_id."
												AND MONTH (a.config_date) = 3
												AND YEAR (a.config_date) = ".$year."
												AND a.broiler_config_status = 1
											) AS mar,
											(
												SELECT
													a.config_qty
												FROM
													broiler_config_tbl a
												WHERE
													a.broiler_subgroup_name = '$broiler_subgroup_name'
												AND a.bc_id = ".$bc_id."
												AND MONTH (a.config_date) = 4
												AND YEAR (a.config_date) = ".$year."
												AND a.broiler_config_status = 1
											) AS apr,
											(
												SELECT
													a.config_qty
												FROM
													broiler_config_tbl a
												WHERE
													a.broiler_subgroup_name = '$broiler_subgroup_name'
												AND a.bc_id = ".$bc_id."
												AND MONTH (a.config_date) = 5
												AND YEAR (a.config_date) = ".$year."
												AND a.broiler_config_status = 1
											) AS may,
											(
												SELECT
													a.config_qty
												FROM
													broiler_config_tbl a
												WHERE
													a.broiler_subgroup_name = '$broiler_subgroup_name'
												AND a.bc_id = ".$bc_id."
												AND MONTH (a.config_date) = 6
												AND YEAR (a.config_date) = ".$year."
												AND a.broiler_config_status = 1
											) AS jun,
											(
												SELECT
													a.config_qty
												FROM
													broiler_config_tbl a
												WHERE
													a.broiler_subgroup_name = '$broiler_subgroup_name'
												AND a.bc_id = ".$bc_id."
												AND MONTH (a.config_date) = 7
												AND YEAR (a.config_date) = ".$year."
												AND a.broiler_config_status = 1
											) AS jul,
											(
												SELECT
													a.config_qty
												FROM
													broiler_config_tbl a
												WHERE
													a.broiler_subgroup_name = '$broiler_subgroup_name'
												AND a.bc_id = ".$bc_id."
												AND MONTH (a.config_date) = 8
												AND YEAR (a.config_date) = ".$year."
												AND a.broiler_config_status = 1
											) AS aug,
											(
												SELECT
													a.config_qty
												FROM
													broiler_config_tbl a
												WHERE
													a.broiler_subgroup_name = '$broiler_subgroup_name'
												AND a.bc_id = ".$bc_id."
												AND MONTH (a.config_date) = 9
												AND YEAR (a.config_date) = ".$year."
												AND a.broiler_config_status = 1
											) AS sep,
											(
												SELECT
													a.config_qty
												FROM
													broiler_config_tbl a
												WHERE
													a.broiler_subgroup_name = '$broiler_subgroup_name'
												AND a.bc_id = ".$bc_id."
												AND MONTH (a.config_date) = 10
												AND YEAR (a.config_date) = ".$year."
												AND a.broiler_config_status = 1
											) AS oct,
											(
												SELECT
													a.config_qty
												FROM
													broiler_config_tbl a
												WHERE
													a.broiler_subgroup_name = '$broiler_subgroup_name'
												AND a.bc_id = ".$bc_id."
												AND MONTH (a.config_date) = 11
												AND YEAR (a.config_date) = ".$year."
												AND a.broiler_config_status = 1
											) AS nov,
											(
												SELECT
													a.config_qty
												FROM
													broiler_config_tbl a
												WHERE
													a.broiler_subgroup_name = '$broiler_subgroup_name'
												AND a.bc_id = ".$bc_id."
												AND MONTH (a.config_date) = 12
												AND YEAR (a.config_date) = ".$year."
												AND a.broiler_config_status = 1
											) AS decem
										FROM
											`broiler_config_tbl` `a`
										where a. broiler_subgroup_name = '$broiler_subgroup_name'
										GROUP BY
											broiler_subgroup_name");
		$data['content'] = $this->load->view('ahg/ahg_edit_broiler_config_content', $data , TRUE);
		$this->load->view('ahg/templates', $data);
	}

	public function add_broiler_config(){
		$info = $this->_require_login();
		$user_id = decode($info['user_id']);
		if($_SERVER['REQUEST_METHOD'] == 'POST'){
			$bc_id = clean_data(decode($this->input->post('bc_id')));
			if(!empty($bc_id)){
				$id = clean_data($this->input->post('broiler_subgroup_id'));
				$config_qty = clean_data($this->input->post('config_qty'));
				$count = 0;
				$this->db->trans_start();
				foreach($id as $row){

					$broiler_subgroup_id = decode($row);
					$check_broiler_subgroup_info = $this->admin->check_data('broiler_subgroup_tbl', array('broiler_subgroup_id' => $broiler_subgroup_id), TRUE);
					if($check_broiler_subgroup_info['result'] == TRUE){
						$broiler_subgroup_name = $check_broiler_subgroup_info['info']->broiler_subgroup_name;
						$amount_type_id = $check_broiler_subgroup_info['info']->amount_type_id;
					} else {
						$msg = '<div class="alert alert-danger">Error! Please try again!</div>';	
						$this->session->set_flashdata('message', $msg);
						redirect('ahg/view-broiler-config/' . encode($bc_id));
					}

					$check_broiler_subgroup_name = $this->admin->check_data('broiler_config_tbl', array('broiler_subgroup_name' => $broiler_subgroup_name, 'broiler_config_status' => 1,	'bc_id'	=>	$bc_id), TRUE);
					if($check_broiler_subgroup_name['result'] != TRUE){
						
						$year = $this->_active_year();
						$qty_jan = $config_qty['jan'][$count];
						$date = $year . '-' . '01-01';
						$insert_broiler_config = array(
							'broiler_subgroup_name' => $broiler_subgroup_name,
							'bc_id' =>$bc_id,
							'config_date' => $date,
							'config_qty' => $qty_jan,
							'broiler_config_status' => 1,
							'amount_type_id' => $amount_type_id,
							'created_by' => $user_id,
							'created_ts' => date_now(),
							'trans_type_id' => 1
						);
						$this->admin->insert_data('broiler_config_tbl', $insert_broiler_config);

						$year = $this->_active_year();
						$qty_feb = $config_qty['feb'][$count];
						$date = $year . '-' . '02-01';
						$insert_broiler_config = array(
							'broiler_subgroup_name' => $broiler_subgroup_name,
							'bc_id' =>$bc_id,
							'config_date' => $date,
							'config_qty' => $qty_feb,
							'broiler_config_status' => 1,
							'amount_type_id' => $amount_type_id,
							'created_by' => $user_id,
							'created_ts' => date_now(),
							'trans_type_id' => 1
						);
						$this->admin->insert_data('broiler_config_tbl', $insert_broiler_config);

						$year = $this->_active_year();
						$qty_mar = $config_qty['mar'][$count];
						$date = $year . '-' . '03-01';
						$insert_broiler_config = array(
							'broiler_subgroup_name' => $broiler_subgroup_name,
							'bc_id' =>$bc_id,
							'config_date' => $date,
							'config_qty' => $qty_mar,
							'broiler_config_status' => 1,
							'amount_type_id' => $amount_type_id,
							'created_by' => $user_id,
							'created_ts' => date_now(),
							'trans_type_id' => 1
						);
						$this->admin->insert_data('broiler_config_tbl', $insert_broiler_config);

						$year = $this->_active_year();
						$qty_apr = $config_qty['apr'][$count];
						$date = $year . '-' . '04-01';
						$insert_broiler_config = array(
							'broiler_subgroup_name' => $broiler_subgroup_name,
							'bc_id' =>$bc_id,
							'config_date' => $date,
							'config_qty' => $qty_apr,
							'broiler_config_status' => 1,
							'amount_type_id' => $amount_type_id,
							'created_by' => $user_id,
							'created_ts' => date_now(),
							'trans_type_id' => 1
						);
						$this->admin->insert_data('broiler_config_tbl', $insert_broiler_config);

						$year = $this->_active_year();
						$qty_may = $config_qty['may'][$count];
						$date = $year . '-' . '05-01';
						$insert_broiler_config = array(
							'broiler_subgroup_name' => $broiler_subgroup_name,
							'bc_id' =>$bc_id,
							'config_date' => $date,
							'config_qty' => $qty_may,
							'broiler_config_status' => 1,
							'amount_type_id' => $amount_type_id,
							'created_by' => $user_id,
							'created_ts' => date_now(),
							'trans_type_id' => 1
						);
						$this->admin->insert_data('broiler_config_tbl', $insert_broiler_config);

						$year = $this->_active_year();
						$qty_jun = $config_qty['jun'][$count];
						$date = $year . '-' . '06-01';
						$insert_broiler_config = array(
							'broiler_subgroup_name' => $broiler_subgroup_name,
							'bc_id' =>$bc_id,
							'config_date' => $date,
							'config_qty' => $qty_jun,
							'broiler_config_status' => 1,
							'amount_type_id' => $amount_type_id,
							'created_by' => $user_id,
							'created_ts' => date_now(),
							'trans_type_id' => 1
						);
						$this->admin->insert_data('broiler_config_tbl', $insert_broiler_config);

						$year = $this->_active_year();
						$qty_jul = $config_qty['jul'][$count];
						$date = $year . '-' . '07-01';
						$insert_broiler_config = array(
							'broiler_subgroup_name' => $broiler_subgroup_name,
							'bc_id' =>$bc_id,
							'config_date' => $date,
							'config_qty' => $qty_jul,
							'broiler_config_status' => 1,
							'amount_type_id' => $amount_type_id,
							'created_by' => $user_id,
							'created_ts' => date_now(),
							'trans_type_id' => 1
						);
						$this->admin->insert_data('broiler_config_tbl', $insert_broiler_config);

						$year = $this->_active_year();
						$qty_aug = $config_qty['may'][$count];
						$date = $year . '-' . '08-01';
						$insert_broiler_config = array(
							'broiler_subgroup_name' => $broiler_subgroup_name,
							'bc_id' =>$bc_id,
							'config_date' => $date,
							'config_qty' => $qty_aug,
							'broiler_config_status' => 1,
							'amount_type_id' => $amount_type_id,
							'created_by' => $user_id,
							'created_ts' => date_now(),
							'trans_type_id' => 1
						);
						$this->admin->insert_data('broiler_config_tbl', $insert_broiler_config);

						$year = $this->_active_year();
						$qty_sep = $config_qty['sep'][$count];
						$date = $year . '-' . '09-01';
						$insert_broiler_config = array(
							'broiler_subgroup_name' => $broiler_subgroup_name,
							'bc_id' =>$bc_id,
							'config_date' => $date,
							'config_qty' => $qty_sep,
							'broiler_config_status' => 1,
							'amount_type_id' => $amount_type_id,
							'created_by' => $user_id,
							'created_ts' => date_now(),
							'trans_type_id' => 1
						);
						$this->admin->insert_data('broiler_config_tbl', $insert_broiler_config);

						$year = $this->_active_year();
						$qty_oct = $config_qty['oct'][$count];
						$date = $year . '-' . '10-01';
						$insert_broiler_config = array(
							'broiler_subgroup_name' => $broiler_subgroup_name,
							'bc_id' =>$bc_id,
							'config_date' => $date,
							'config_qty' => $qty_oct,
							'broiler_config_status' => 1,
							'amount_type_id' => $amount_type_id,
							'created_by' => $user_id,
							'created_ts' => date_now(),
							'trans_type_id' => 1
						);
						$this->admin->insert_data('broiler_config_tbl', $insert_broiler_config);

						$year = $this->_active_year();
						$qty_nov = $config_qty['nov'][$count];
						$date = $year . '-' . '11-01';
						$insert_broiler_config = array(
							'broiler_subgroup_name' => $broiler_subgroup_name,
							'bc_id' =>$bc_id,
							'config_date' => $date,
							'config_qty' => $qty_nov,
							'broiler_config_status' => 1,
							'amount_type_id' => $amount_type_id,
							'created_by' => $user_id,
							'created_ts' => date_now(),
							'trans_type_id' => 1
						);
						$this->admin->insert_data('broiler_config_tbl', $insert_broiler_config);

						$year = $this->_active_year();
						$qty_dec = $config_qty['dec'][$count];
						$date = $year . '-' . '12-01';
						$insert_broiler_config = array(
							'broiler_subgroup_name' => $broiler_subgroup_name,
							'bc_id' =>$bc_id,
							'config_date' => $date,
							'config_qty' => $qty_dec,
							'broiler_config_status' => 1,
							'amount_type_id' => $amount_type_id,
							'created_by' => $user_id,
							'created_ts' => date_now(),
							'trans_type_id' => 1
						);
						$this->admin->insert_data('broiler_config_tbl', $insert_broiler_config);
						$count++;
					} else {
						/*$msg = '<div class="alert alert-danger">Notice! Config already exists!</div>';	
						$this->session->set_flashdata('message', $msg);
						redirect('admin/view-broiler-config/' . encode($bc_id));*/
						continue;
					}
				}
				if($this->db->trans_status() === FALSE){
					$this->db->trans_rollback();
					$msg = '<div class="alert alert-danger">Error please try again!</div>';
				}else{
					$this->db->trans_commit();
					$msg = '<div class="alert alert-success"><strong>Broiler Config successfully added.</strong></div>';
				}
				$this->session->set_flashdata('message', $msg);
				redirect('ahg/view-broiler-config/' . encode($bc_id));
			}else{
				echo 'Something wrong';
			}
		}
	}

	public function update_broiler_config(){
		$info = $this->_require_login();
		$user_id = decode($info['user_id']);
		if($_SERVER['REQUEST_METHOD'] == 'POST'){
			$bc_id = clean_data(decode($this->input->post('bc_id')));
			if(!empty($bc_id)){
				$id = clean_data($this->input->post('broiler_subgroup_name'));
				$config_qty = clean_data($this->input->post('config_qty'));
				$count = 0;
				$this->db->trans_start();
				foreach($id as $row){

					$broiler_subgroup_name = decode($row);
					$year = $this->_active_year();

					$qty_jan = $config_qty['jan'][$count];
					$update_broiler_config = array(
						'config_qty' => $qty_jan,
						'modified_by' => $user_id,
						'modified_ts' => date_now()
					);
					$where = array(
						'broiler_subgroup_name' => $broiler_subgroup_name,
						'bc_id' =>$bc_id,
						'MONTH(config_date)' => 1,
						'YEAR(config_date)' => $year,
						'broiler_config_status' => 1
					);
					$this->admin->update_data('broiler_config_tbl', $update_broiler_config, $where);

					$qty_feb = $config_qty['feb'][$count];
					$update_broiler_config = array(
						'config_qty' => $qty_feb,
						'modified_by' => $user_id,
						'modified_ts' => date_now()
					);
					$where = array(
						'broiler_subgroup_name' => $broiler_subgroup_name,
						'bc_id' =>$bc_id,
						'MONTH(config_date)' => 2,
						'YEAR(config_date)' => $year,
						'broiler_config_status' => 1
					);
					$this->admin->update_data('broiler_config_tbl', $update_broiler_config, $where);

					$qty_mar = $config_qty['mar'][$count];
					$update_broiler_config = array(
						'config_qty' => $qty_mar,
						'modified_by' => $user_id,
						'modified_ts' => date_now()
					);
					$where = array(
						'broiler_subgroup_name' => $broiler_subgroup_name,
						'bc_id' =>$bc_id,
						'MONTH(config_date)' => 3,
						'YEAR(config_date)' => $year,
						'broiler_config_status' => 1
					);
					$this->admin->update_data('broiler_config_tbl', $update_broiler_config, $where);

					$qty_apr = $config_qty['apr'][$count];
					$update_broiler_config = array(
						'config_qty' => $qty_apr,
						'modified_by' => $user_id,
						'modified_ts' => date_now()
					);
					$where = array(
						'broiler_subgroup_name' => $broiler_subgroup_name,
						'bc_id' =>$bc_id,
						'MONTH(config_date)' => 4,
						'YEAR(config_date)' => $year,
						'broiler_config_status' => 1
					);
					$this->admin->update_data('broiler_config_tbl', $update_broiler_config, $where);

					$qty_may = $config_qty['may'][$count];
					$update_broiler_config = array(
						'config_qty' => $qty_may,
						'modified_by' => $user_id,
						'modified_ts' => date_now()
					);
					$where = array(
						'broiler_subgroup_name' => $broiler_subgroup_name,
						'bc_id' =>$bc_id,
						'MONTH(config_date)' => 5,
						'YEAR(config_date)' => $year,
						'broiler_config_status' => 1
					);
					$this->admin->update_data('broiler_config_tbl', $update_broiler_config, $where);

					$qty_jun = $config_qty['jun'][$count];
					$update_broiler_config = array(
						'config_qty' => $qty_jun,
						'modified_by' => $user_id,
						'modified_ts' => date_now()
					);
					$where = array(
						'broiler_subgroup_name' => $broiler_subgroup_name,
						'bc_id' =>$bc_id,
						'MONTH(config_date)' => 6,
						'YEAR(config_date)' => $year,
						'broiler_config_status' => 1
					);
					$this->admin->update_data('broiler_config_tbl', $update_broiler_config, $where);

					$qty_jul = $config_qty['jul'][$count];
					$update_broiler_config = array(
						'config_qty' => $qty_jul,
						'modified_by' => $user_id,
						'modified_ts' => date_now()
					);
					$where = array(
						'broiler_subgroup_name' => $broiler_subgroup_name,
						'bc_id' =>$bc_id,
						'MONTH(config_date)' => 7,
						'YEAR(config_date)' => $year,
						'broiler_config_status' => 1
					);
					$this->admin->update_data('broiler_config_tbl', $update_broiler_config, $where);

					$qty_aug = $config_qty['aug'][$count];
					$update_broiler_config = array(
						'config_qty' => $qty_aug,
						'modified_by' => $user_id,
						'modified_ts' => date_now()
					);
					$where = array(
						'broiler_subgroup_name' => $broiler_subgroup_name,
						'bc_id' =>$bc_id,
						'MONTH(config_date)' => 8,
						'YEAR(config_date)' => $year,
						'broiler_config_status' => 1
					);
					$this->admin->update_data('broiler_config_tbl', $update_broiler_config, $where);

					$qty_sep = $config_qty['sep'][$count];
					$update_broiler_config = array(
						'config_qty' => $qty_sep,
						'modified_by' => $user_id,
						'modified_ts' => date_now()
					);
					$where = array(
						'broiler_subgroup_name' => $broiler_subgroup_name,
						'bc_id' =>$bc_id,
						'MONTH(config_date)' => 9,
						'YEAR(config_date)' => $year,
						'broiler_config_status' => 1
					);
					$this->admin->update_data('broiler_config_tbl', $update_broiler_config, $where);

					$qty_oct = $config_qty['oct'][$count];
					$update_broiler_config = array(
						'config_qty' => $qty_oct,
						'modified_by' => $user_id,
						'modified_ts' => date_now()
					);
					$where = array(
						'broiler_subgroup_name' => $broiler_subgroup_name,
						'bc_id' =>$bc_id,
						'MONTH(config_date)' => 10,
						'YEAR(config_date)' => $year,
						'broiler_config_status' => 1
					);
					$this->admin->update_data('broiler_config_tbl', $update_broiler_config, $where);

					$qty_nov = $config_qty['nov'][$count];
					$update_broiler_config = array(
						'config_qty' => $qty_nov,
						'modified_by' => $user_id,
						'modified_ts' => date_now()
					);
					$where = array(
						'broiler_subgroup_name' => $broiler_subgroup_name,
						'bc_id' =>$bc_id,
						'MONTH(config_date)' => 11,
						'YEAR(config_date)' => $year,
						'broiler_config_status' => 1
					);
					$this->admin->update_data('broiler_config_tbl', $update_broiler_config, $where);

					$qty_dec = $config_qty['dec'][$count];
					$update_broiler_config = array(
						'config_qty' => $qty_dec,
						'modified_by' => $user_id,
						'modified_ts' => date_now()
					);
					$where = array(
						'broiler_subgroup_name' => $broiler_subgroup_name,
						'bc_id' =>$bc_id,
						'MONTH(config_date)' => 12,
						'YEAR(config_date)' => $year,
						'broiler_config_status' => 1
					);
					$this->admin->update_data('broiler_config_tbl', $update_broiler_config, $where);
					$count++;
				}
				if($this->db->trans_status() === FALSE){
					$this->db->trans_rollback();
					$msg = '<div class="alert alert-danger">Error please try again!</div>';
				}else{
					$this->db->trans_commit();
					$msg = '<div class="alert alert-success"><strong>Broiler Config successfully updated.</strong></div>';
				}
				$this->session->set_flashdata('message', $msg);
				redirect('ahg/view-broiler-config/' . encode($bc_id));
			}else{
				echo 'Something wrong';
			}
		}
	}

	public function broiler_trans($id){
		$info = $this->_require_login();
		$data['bc_id'] = decode($id);
		$bc_id = decode($id);
		$data['title'] = 'Broiler Cost';
		$year = $this->_active_year();
		$data['trans_year'] = encode($year);
		$join_broiler_trans = array(
			'bc_tbl b' => 'a.bc_id = b.bc_id and a.bc_id ='.$bc_id,
			'user_tbl c' => 'a.created_by = c.user_id',
			'status_tbl d' => 'd.status_id = a.broiler_trans_status and a.broiler_trans_status != 5',
			'broiler_subgroup_tbl e' => 'a.broiler_subgroup_id = e.broiler_subgroup_id',
			'broiler_group_tbl f' => 'e.broiler_group_id = f.broiler_group_id',
			'broiler_trans_dtl_tbl g' => 'a.broiler_trans_id = g.broiler_trans_id and YEAR(g.broiler_trans_date)='.$year,
		);
		$data['broiler_trans'] = $this->admin->get_join('broiler_trans_tbl a', $join_broiler_trans, FALSE,'f.broiler_group_name ASC', 'f.broiler_group_id, YEAR(g.broiler_trans_date)');

		$data['content'] = $this->load->view('ahg/ahg_broiler_trans_content', $data , TRUE);
		$this->load->view('ahg/templates', $data);
	}

	public function new_broiler_trans($id){
		$info = $this->_require_login();
		$data['bc_id'] = decode($id);
		$bc_id = decode($id);
		$data['title'] = 'Broiler Cost';

		$join_broiler_subgroup = array(
			'broiler_group_tbl b' => 'a.broiler_group_id = b.broiler_group_id AND b.broiler_group_status = 1 AND a.config_show = 1',
		);
		$data['broiler_subgroup'] = $this->admin->get_join('broiler_subgroup_tbl a', $join_broiler_subgroup, FALSE,'a.broiler_subgroup_id ASC', FALSE);

		$data['broiler_group'] = $this->admin->get_data('broiler_group_tbl a', 'broiler_group_status=1');
		$data['content'] = $this->load->view('ahg/ahg_new_broiler_trans_content', $data , TRUE);
		$this->load->view('ahg/templates', $data);
	}

	public function get_broiler_subgroup(){
		$info = $this->_require_login();
		$year = $this->_active_year();
		if($_SERVER['REQUEST_METHOD'] == 'POST'){
			$broiler_group_id =clean_data(decode($this->input->post('id')));
			$bc_id =clean_data(decode($this->input->post('bc_id')));
			if($broiler_group_id){
				$sql = 'SELECT
							*
						FROM
							broiler_subgroup_tbl
						WHERE
							broiler_group_id = '.$broiler_group_id.'
						AND broiler_subgroup_tbl.show = 1
						AND broiler_subgroup_id NOT IN (
							SELECT
								a.broiler_subgroup_id
							FROM
								broiler_trans_tbl a
							INNER JOIN broiler_trans_dtl_tbl b ON a.broiler_trans_id = b.broiler_trans_id
							INNER JOIN broiler_subgroup_tbl c ON a.broiler_subgroup_id = c.broiler_subgroup_id
							WHERE
								broiler_trans_status != 5
							AND YEAR (b.broiler_trans_date) = '.$year.'
							AND a.bc_id = '.$bc_id.'
							AND c.broiler_group_id = '.$broiler_group_id.'
							GROUP BY
								broiler_subgroup_id
						)';
				$get_broiler_subgroup = $this->admin->get_query($sql);
				$broiler_group = '';
				if($get_broiler_subgroup){
					foreach($get_broiler_subgroup as $row){
						$broiler_group .= '<tr><input type="hidden" name="broiler_subgroup_id[]" value="' . encode($row->broiler_subgroup_id) . '">';
						$broiler_group .= '<td>' . $row->broiler_subgroup_name .'</td>';
						$broiler_group .= '<td class="text-center"><input type="text" name="broiler_budget_qty[jan][]" class="form-control input-sm broiler-cost-input" size="6"></td>';
						$broiler_group .= '<td class="text-center"><input type="text" name="broiler_budget_qty[feb][]" class="form-control input-sm broiler-cost-input" size="6"></td>';
						$broiler_group .= '<td class="text-center"><input type="text" name="broiler_budget_qty[mar][]" class="form-control input-sm broiler-cost-input" size="6"></td>';
						$broiler_group .= '<td class="text-center"><input type="text" name="broiler_budget_qty[apr][]" class="form-control input-sm broiler-cost-input" size="6"></td>';
						$broiler_group .= '<td class="text-center"><input type="text" name="broiler_budget_qty[may][]" class="form-control input-sm broiler-cost-input" size="6"></td>';
						$broiler_group .= '<td class="text-center"><input type="text" name="broiler_budget_qty[jun][]" class="form-control input-sm broiler-cost-input" size="6"></td>';
						$broiler_group .= '<td class="text-center"><input type="text" name="broiler_budget_qty[jul][]" class="form-control input-sm broiler-cost-input" size="6"></td>';
						$broiler_group .= '<td class="text-center"><input type="text" name="broiler_budget_qty[aug][]" class="form-control input-sm broiler-cost-input" size="6"></td>';
						$broiler_group .= '<td class="text-center"><input type="text" name="broiler_budget_qty[sep][]" class="form-control input-sm broiler-cost-input" size="6"></td>';
						$broiler_group .= '<td class="text-center"><input type="text" name="broiler_budget_qty[oct][]" class="form-control input-sm broiler-cost-input" size="6"></td>';
						$broiler_group .= '<td class="text-center"><input type="text" name="broiler_budget_qty[nov][]" class="form-control input-sm broiler-cost-input" size="6"></td>';
						$broiler_group .= '<td class="text-center"><input type="text" name="broiler_budget_qty[dec][]" class="form-control input-sm broiler-cost-input" size="6"></td>';
						$broiler_group .= '</tr>';
					}
					$data['broiler_group'] = $broiler_group;
					$data['result'] = 1;
				} else {
					$data['broiler_group'] = 'No data';
					$data['result'] = 1;
				}
			} else {
				$data['result'] = 0;
			}
			
				
		} else{
			$data['result'] = 0;
		}

		echo json_encode($data);
	}

	public function add_broiler_trans(){
		$info = $this->_require_login();
		$user_id = decode($info['user_id']);
		if($_SERVER['REQUEST_METHOD'] == 'POST'){
			$bc_id = clean_data(decode($this->input->post('bc_id')));
			if(!empty($bc_id)){
				$id = clean_data($this->input->post('broiler_subgroup_id'));
				$broiler_budget_qty = clean_data($this->input->post('broiler_budget_qty'));
				$count = 0;
				$this->db->trans_start();
				foreach($id as $row){

					$broiler_subgroup_id = decode($row);
					$check_broiler_subgroup_info = $this->admin->check_data('broiler_subgroup_tbl', array('broiler_subgroup_id' => $broiler_subgroup_id), TRUE);
					if($check_broiler_subgroup_info['result'] == TRUE){
						$broiler_subgroup_name = $check_broiler_subgroup_info['info']->broiler_subgroup_name;
						$amount_type_id = $check_broiler_subgroup_info['info']->amount_type_id;
					} else {
						$msg = '<div class="alert alert-danger">Error! Please try again!</div>';	
						$this->session->set_flashdata('message', $msg);
						redirect('ahg/broiler-trans/' . encode($bc_id));
					}
					
					$year = $this->_active_year();
					$join_broiler_trans = array(
						'broiler_trans_dtl_tbl b' => 'a.broiler_trans_id = b.broiler_trans_id AND a.broiler_subgroup_id = '.$broiler_subgroup_id.' AND a.broiler_trans_status != 5 and a.bc_id = '.$bc_id.' and YEAR(b.broiler_trans_date) = ' . $year
					);
					$check_broiler_trans = $this->admin->check_join('broiler_trans_tbl a', $join_broiler_trans, TRUE);
					if($check_broiler_trans['result'] == TRUE){
						$broiler_trans_id = $check_broiler_trans['info']->broiler_trans_id;
						$check_broiler_trans_dtl = $this->admin->check_data('broiler_trans_dtl_tbl a', array('a.broiler_trans_id' => $broiler_trans_id, 'YEAR(a.broiler_trans_date) !=' => $year), TRUE);
					}

					if($check_broiler_trans['result'] != TRUE){
						
						$qty_jan = $broiler_budget_qty['jan'][$count];
						$date = $year . '-' . '01-01';
						$insert_broiler_trans = array(
							'broiler_subgroup_id' => $broiler_subgroup_id,
							'bc_id' =>$bc_id,
							'broiler_trans_status' => 3,
							'created_by' => $user_id,
							'created_ts' => date_now(),
							'trans_type_id' => 1
						);
						$insert_trans = $this->admin->insert_data('broiler_trans_tbl', $insert_broiler_trans, true);

						$insert_broiler_trans_dtl = array(
							'broiler_trans_id' => $insert_trans['id'],
							'broiler_trans_date' =>$date,
							'broiler_budget_qty' => $qty_jan,
							'amount_type_id' => $amount_type_id
						);
						$this->admin->insert_data('broiler_trans_dtl_tbl', $insert_broiler_trans_dtl, true);

						$year = $this->_active_year();
						$qty_feb = $broiler_budget_qty['feb'][$count];
						$date = $year . '-' . '02-01';
						$insert_broiler_trans_dtl = array(
							'broiler_trans_id' => $insert_trans['id'],
							'broiler_trans_date' =>$date,
							'broiler_budget_qty' => $qty_feb,
							'amount_type_id' => $amount_type_id
						);
						$this->admin->insert_data('broiler_trans_dtl_tbl', $insert_broiler_trans_dtl, true);

						$year = $this->_active_year();
						$qty_mar = $broiler_budget_qty['mar'][$count];
						$date = $year . '-' . '03-01';
						$insert_broiler_trans_dtl = array(
							'broiler_trans_id' => $insert_trans['id'],
							'broiler_trans_date' =>$date,
							'broiler_budget_qty' => $qty_mar,
							'amount_type_id' => $amount_type_id
						);
						$this->admin->insert_data('broiler_trans_dtl_tbl', $insert_broiler_trans_dtl, true);

						$year = $this->_active_year();
						$qty_apr = $broiler_budget_qty['apr'][$count] == '' ? 0 : $broiler_budget_qty['apr'][$count];
						$date = $year . '-' . '04-01';
						$insert_broiler_trans_dtl = array(
							'broiler_trans_id' => $insert_trans['id'],
							'broiler_trans_date' =>$date,
							'broiler_budget_qty' => $qty_apr,
							'amount_type_id' => $amount_type_id
						);
						$this->admin->insert_data('broiler_trans_dtl_tbl', $insert_broiler_trans_dtl, true);

						$year = $this->_active_year();
						$qty_may = $broiler_budget_qty['may'][$count];
						$date = $year . '-' . '05-01';
						$insert_broiler_trans_dtl = array(
							'broiler_trans_id' => $insert_trans['id'],
							'broiler_trans_date' =>$date,
							'broiler_budget_qty' => $qty_may,
							'amount_type_id' => $amount_type_id
						);
						$this->admin->insert_data('broiler_trans_dtl_tbl', $insert_broiler_trans_dtl, true);

						$year = $this->_active_year();
						$qty_jun = $broiler_budget_qty['jun'][$count];
						$date = $year . '-' . '06-01';
						$insert_broiler_trans_dtl = array(
							'broiler_trans_id' => $insert_trans['id'],
							'broiler_trans_date' =>$date,
							'broiler_budget_qty' => $qty_jun,
							'amount_type_id' => $amount_type_id
						);
						$this->admin->insert_data('broiler_trans_dtl_tbl', $insert_broiler_trans_dtl, true);

						$year = $this->_active_year();
						$qty_jul = $broiler_budget_qty['jul'][$count];
						$date = $year . '-' . '07-01';
						$insert_broiler_trans_dtl = array(
							'broiler_trans_id' => $insert_trans['id'],
							'broiler_trans_date' =>$date,
							'broiler_budget_qty' => $qty_jul,
							'amount_type_id' => $amount_type_id
						);
						$this->admin->insert_data('broiler_trans_dtl_tbl', $insert_broiler_trans_dtl, true);

						$year = $this->_active_year();
						$qty_aug = $broiler_budget_qty['aug'][$count];
						$date = $year . '-' . '08-01';
						$insert_broiler_trans_dtl = array(
							'broiler_trans_id' => $insert_trans['id'],
							'broiler_trans_date' =>$date,
							'broiler_budget_qty' => $qty_aug,
							'amount_type_id' => $amount_type_id
						);
						$this->admin->insert_data('broiler_trans_dtl_tbl', $insert_broiler_trans_dtl, true);

						$year = $this->_active_year();
						$qty_sep = $broiler_budget_qty['sep'][$count];
						$date = $year . '-' . '09-01';
						$insert_broiler_trans_dtl = array(
							'broiler_trans_id' => $insert_trans['id'],
							'broiler_trans_date' =>$date,
							'broiler_budget_qty' => $qty_sep,
							'amount_type_id' => $amount_type_id
						);
						$this->admin->insert_data('broiler_trans_dtl_tbl', $insert_broiler_trans_dtl, true);

						$year = $this->_active_year();
						$qty_oct = $broiler_budget_qty['oct'][$count];
						$date = $year . '-' . '10-01';
						$insert_broiler_trans_dtl = array(
							'broiler_trans_id' => $insert_trans['id'],
							'broiler_trans_date' =>$date,
							'broiler_budget_qty' => $qty_oct,
							'amount_type_id' => $amount_type_id
						);
						$this->admin->insert_data('broiler_trans_dtl_tbl', $insert_broiler_trans_dtl, true);

						$year = $this->_active_year();
						$qty_nov = $broiler_budget_qty['nov'][$count];
						$date = $year . '-' . '11-01';
						$insert_broiler_trans_dtl = array(
							'broiler_trans_id' => $insert_trans['id'],
							'broiler_trans_date' =>$date,
							'broiler_budget_qty' => $qty_nov,
							'amount_type_id' => $amount_type_id
						);
						$this->admin->insert_data('broiler_trans_dtl_tbl', $insert_broiler_trans_dtl, true);

						$year = $this->_active_year();
						$qty_dec = $broiler_budget_qty['dec'][$count];
						$date = $year . '-' . '12-01';
						$insert_broiler_trans_dtl = array(
							'broiler_trans_id' => $insert_trans['id'],
							'broiler_trans_date' =>$date,
							'broiler_budget_qty' => $qty_dec,
							'amount_type_id' => $amount_type_id
						);
						$this->admin->insert_data('broiler_trans_dtl_tbl', $insert_broiler_trans_dtl, true);
						$count++;
					} else {
						$msg = '<div class="alert alert-danger">Notice! Broiler Transaction already exists!</div>';	
						$this->session->set_flashdata('message', $msg);
						redirect('ahg/broiler-trans/' . encode($bc_id));
						
					}
				}

				if($this->db->trans_status() === FALSE){
					$this->db->trans_rollback();
					$msg = '<div class="alert alert-danger">Error please try again!</div>';
				}else{
					$this->db->trans_commit();
					$msg = '<div class="alert alert-success"><strong>Broiler Transaction successfully added.</strong></div>';
				}
				$this->session->set_flashdata('message', $msg);
				redirect('ahg/broiler-trans/' . encode($bc_id));
			}else{
				echo 'Something wrong';
			}
		}
	}

	public function view_broiler_trans($bc_id, $broiler_group_id, $broiler_group_name, $trans_year){
		$info = $this->_require_login();
		$data['bc_id'] = $bc_id;
		$data['trans_year'] = $trans_year;
		$data['broiler_group_name'] = decode($broiler_group_name);
		$data['broiler_group_id'] = decode($broiler_group_id);
		$bc_id = decode($bc_id);
		$broiler_group_id = decode($broiler_group_id);
		$trans_year = decode($trans_year);

		$join_broiler_trans = array(
			'bc_tbl b' => 'a.bc_id = b.bc_id and a.bc_id ='.$bc_id,
			'status_tbl d' => 'd.status_id = a.broiler_trans_status and a.broiler_trans_status != 5',
			'broiler_subgroup_tbl e' => 'a.broiler_subgroup_id = e.broiler_subgroup_id',
			'broiler_group_tbl f' => 'e.broiler_group_id = f.broiler_group_id and f.broiler_group_id = '.$broiler_group_id,
			'broiler_trans_dtl_tbl g'	=>	'a.broiler_trans_id = g.broiler_trans_id and YEAR(g.broiler_trans_date) = '.$trans_year
		);
		$data['broiler_trans'] = $this->admin->get_join('broiler_trans_tbl a', $join_broiler_trans, FALSE,'f.broiler_group_name ASC', 'a.broiler_trans_id', 'a.broiler_trans_id, e.broiler_subgroup_name, g.broiler_trans_dtl_id, b.bc_id, f.broiler_group_id, f.broiler_group_name, d.status_id');

		$data['title'] = 'Broiler Cost';
		$data['content'] = $this->load->view('ahg/ahg_view_broiler_trans_content', $data , TRUE);
		$this->load->view('ahg/templates', $data);
	}

	public function edit_broiler_trans($id, $broiler_subgroup_name, $bc_id, $broiler_group_id, $broiler_group_name){
		$info = $this->_require_login();
		$data['broiler_trans_id'] = $id;
		$data['bc_id'] = $bc_id;
		$data['broiler_group_id'] = $broiler_group_id;
		$data['broiler_group_name'] = $broiler_group_name;
		$data['broiler_subgroup_name'] = decode($broiler_subgroup_name);
		$broiler_trans_id = decode($id);
		
		$join_broiler_trans = array(
			'broiler_subgroup_tbl e' => 'a.broiler_subgroup_id = e.broiler_subgroup_id',
			'broiler_group_tbl f' => 'e.broiler_group_id = f.broiler_group_id',
			'broiler_trans_dtl_tbl g'	=>	'a.broiler_trans_id = g.broiler_trans_id and a.broiler_trans_id = '.$broiler_trans_id,
		);
		$data['broiler_trans'] = $this->admin->get_join('broiler_trans_tbl a', $join_broiler_trans, FALSE, false, 'a.broiler_trans_id');

		$data['title'] = 'Broiler Cost';
		$data['content'] = $this->load->view('ahg/ahg_edit_broiler_trans_content', $data , TRUE);
		$this->load->view('ahg/templates', $data);
	}

	public function update_broiler_trans(){
		$info = $this->_require_login();
		$user_id = decode($info['user_id']);
		if($_SERVER['REQUEST_METHOD'] == 'POST'){
			$bc_id = clean_data(decode($this->input->post('bc_id')));
			$year = clean_data(decode($this->input->post('broiler_trans_year')));
			$broiler_group_id = clean_data($this->input->post('broiler_group_id'));
			$broiler_group_name = clean_data($this->input->post('broiler_group_name'));
			if(!empty($bc_id)){
				$id = clean_data($this->input->post('broiler_trans_id'));
				$broiler_budget_qty = clean_data($this->input->post('broiler_budget_qty'));
				$count = 0;
				$this->db->trans_start();
				foreach($id as $row){

					$broiler_trans_id = decode($row);

					//update header transaction
					$update_broiler_trans = array(
						'modified_by' => $user_id,
						'modified_ts' => date_now()
					);
					$where = array(
						'broiler_trans_id' => $broiler_trans_id
					);
					$this->admin->update_data('broiler_trans_tbl', $update_broiler_trans, $where);

					//update dtl trans
					$qty_jan = $broiler_budget_qty['jan'][$count];
					$update_broiler_trans = array(
						'broiler_budget_qty' => $qty_jan
					);
					$where = array(
						'broiler_trans_id' => $broiler_trans_id,
						'MONTH(broiler_trans_date)' => 1,
						'YEAR(broiler_trans_date)' => $year,
					);
					$this->admin->update_data('broiler_trans_dtl_tbl', $update_broiler_trans, $where);

					$qty_feb = $broiler_budget_qty['feb'][$count];
					$update_broiler_trans = array(
						'broiler_budget_qty' => $qty_feb
					);
					$where = array(
						'broiler_trans_id' => $broiler_trans_id,
						'MONTH(broiler_trans_date)' => 2,
						'YEAR(broiler_trans_date)' => $year,
					);
					$this->admin->update_data('broiler_trans_dtl_tbl', $update_broiler_trans, $where);

					$qty_mar = $broiler_budget_qty['mar'][$count];
					$update_broiler_trans = array(
						'broiler_budget_qty' => $qty_mar
					);
					$where = array(
						'broiler_trans_id' => $broiler_trans_id,
						'MONTH(broiler_trans_date)' => 3,
						'YEAR(broiler_trans_date)' => $year,
					);
					$this->admin->update_data('broiler_trans_dtl_tbl', $update_broiler_trans, $where);

					$qty_apr = $broiler_budget_qty['apr'][$count];
					$update_broiler_trans = array(
						'broiler_budget_qty' => $qty_apr
					);
					$where = array(
						'broiler_trans_id' => $broiler_trans_id,
						'MONTH(broiler_trans_date)' => 4,
						'YEAR(broiler_trans_date)' => $year,
					);
					$this->admin->update_data('broiler_trans_dtl_tbl', $update_broiler_trans, $where);

					$qty_may = $broiler_budget_qty['may'][$count];
					$update_broiler_trans = array(
						'broiler_budget_qty' => $qty_may
					);
					$where = array(
						'broiler_trans_id' => $broiler_trans_id,
						'MONTH(broiler_trans_date)' => 5,
						'YEAR(broiler_trans_date)' => $year,
					);
					$this->admin->update_data('broiler_trans_dtl_tbl', $update_broiler_trans, $where);

					$qty_jun = $broiler_budget_qty['jun'][$count];
					$update_broiler_trans = array(
						'broiler_budget_qty' => $qty_jun
					);
					$where = array(
						'broiler_trans_id' => $broiler_trans_id,
						'MONTH(broiler_trans_date)' => 6,
						'YEAR(broiler_trans_date)' => $year,
					);
					$this->admin->update_data('broiler_trans_dtl_tbl', $update_broiler_trans, $where);

					$qty_jul = $broiler_budget_qty['jul'][$count];
					$update_broiler_trans = array(
						'broiler_budget_qty' => $qty_jul
					);
					$where = array(
						'broiler_trans_id' => $broiler_trans_id,
						'MONTH(broiler_trans_date)' => 7,
						'YEAR(broiler_trans_date)' => $year,
					);
					$this->admin->update_data('broiler_trans_dtl_tbl', $update_broiler_trans, $where);

					$qty_aug = $broiler_budget_qty['aug'][$count];
					$update_broiler_trans = array(
						'broiler_budget_qty' => $qty_aug
					);
					$where = array(
						'broiler_trans_id' => $broiler_trans_id,
						'MONTH(broiler_trans_date)' => 8,
						'YEAR(broiler_trans_date)' => $year,
					);
					$this->admin->update_data('broiler_trans_dtl_tbl', $update_broiler_trans, $where);

					$qty_sep = $broiler_budget_qty['sep'][$count];
					$update_broiler_trans = array(
						'broiler_budget_qty' => $qty_sep
					);
					$where = array(
						'broiler_trans_id' => $broiler_trans_id,
						'MONTH(broiler_trans_date)' => 9,
						'YEAR(broiler_trans_date)' => $year,
					);
					$this->admin->update_data('broiler_trans_dtl_tbl', $update_broiler_trans, $where);

					$qty_oct = $broiler_budget_qty['oct'][$count];
					$update_broiler_trans = array(
						'broiler_budget_qty' => $qty_oct
					);
					$where = array(
						'broiler_trans_id' => $broiler_trans_id,
						'MONTH(broiler_trans_date)' => 10,
						'YEAR(broiler_trans_date)' => $year,
					);
					$this->admin->update_data('broiler_trans_dtl_tbl', $update_broiler_trans, $where);

					$qty_nov = $broiler_budget_qty['nov'][$count];
					$update_broiler_trans = array(
						'broiler_budget_qty' => $qty_nov
					);
					$where = array(
						'broiler_trans_id' => $broiler_trans_id,
						'MONTH(broiler_trans_date)' => 11,
						'YEAR(broiler_trans_date)' => $year,
					);
					$this->admin->update_data('broiler_trans_dtl_tbl', $update_broiler_trans, $where);

					$qty_dec = $broiler_budget_qty['dec'][$count];
					$update_broiler_trans = array(
						'broiler_budget_qty' => $qty_dec
					);
					$where = array(
						'broiler_trans_id' => $broiler_trans_id,
						'MONTH(broiler_trans_date)' => 12,
						'YEAR(broiler_trans_date)' => $year,
					);
					$this->admin->update_data('broiler_trans_dtl_tbl', $update_broiler_trans, $where);
					$count++;
				}
				if($this->db->trans_status() === FALSE){
					$this->db->trans_rollback();
					$msg = '<div class="alert alert-danger">Error please try again!</div>';
				}else{
					$this->db->trans_commit();
					$msg = '<div class="alert alert-success"><strong>Broiler Transaction successfully updated.</strong></div>';
				}
				$this->session->set_flashdata('message', $msg);
				redirect('ahg/view-broiler-trans/' . encode($bc_id) .'/'. $broiler_group_id .'/'. $broiler_group_name .'/'. encode($year));
			} else {
				echo 'Something wrong on bc';
			}
		} else{
			echo 'Something wrong';
		}
	}

	public function post_broiler_trans(){
		
		$info = $this->_require_login();
		$user_id = decode($info['user_id']);

		$id = clean_data($this->input->post('broiler_trans_id'));
		$bc_id = clean_data($this->input->post('bc_id'));
		$broiler_group_id = clean_data($this->input->post('broiler_group_id'));
		$broiler_group_name = clean_data($this->input->post('broiler_group_name'));
		$trans_year = clean_data($this->input->post('trans_year'));
		$trans_status = clean_data($this->input->post('broiler_trans_status'));
		if($trans_status == 'post'){
			$broiler_trans_status = 4;
			$status = 'posted';
		} else {
			$broiler_trans_status = 5;
			$status = 'cancelled';
		}

		$data['broiler_trans_id'] = $id;
		$broiler_trans_id = decode($id);
		$this->db->trans_start();
		$update_broiler_trans = array(
			'broiler_trans_status' => $broiler_trans_status,
			'modified_by' => $user_id,
			'modified_ts' => date_now()
		);
		$where = array(
			'broiler_trans_id' => $broiler_trans_id,
		);
		echo $this->admin->update_data('broiler_trans_tbl', $update_broiler_trans, $where);

		if($this->db->trans_status() === FALSE){
			$this->db->trans_rollback();
			$msg = '<div class="alert alert-danger">Error please try again!</div>';
		}else{
			$this->db->trans_commit();
			$msg = '<div class="alert alert-success"><strong>Broiler Transaction Detail successfully '.$status.'.</strong></div>';
		}
		$this->session->set_flashdata('message', $msg);
		redirect('ahg/view-broiler-trans/' . $bc_id .'/'. $broiler_group_id .'/'. $broiler_group_name .'/'. $trans_year);
	}

	public function cancel_broiler_trans($id, $bc_id, $broiler_group_id, $broiler_group_name, $trans_year){
		$info = $this->_require_login();
		$data['broiler_trans_id'] = $id;
		$broiler_trans_id = decode($id);
		$this->db->trans_start();
		$update_broiler_trans = array(
			'broiler_trans_status' => 5
		);
		$where = array(
			'broiler_trans_id' => $broiler_trans_id,
		);
		$this->admin->update_data('broiler_trans_tbl', $update_broiler_trans, $where);

		if($this->db->trans_status() === FALSE){
			$this->db->trans_rollback();
			$msg = '<div class="alert alert-danger">Error please try again!</div>';
		}else{
			$this->db->trans_commit();
			$msg = '<div class="alert alert-success"><strong>Broiler Transaction Detail successfully removed.</strong></div>';
		}
		$this->session->set_flashdata('message', $msg);
		redirect('ahg/view-broiler-trans/' . $bc_id .'/'. $broiler_group_id .'/'. $broiler_group_name .'/'. $trans_year);
	}

	public function get_broiler_trans(){
		$info = $this->_require_login();
		if($_SERVER['REQUEST_METHOD'] == 'POST'){
			$broiler_trans_date =clean_data($this->input->post('broiler_trans_date'));
			$bc_id =clean_data(decode($this->input->post('bc_id')));
			if($broiler_trans_date){

				$join_broiler_trans = array(
					'bc_tbl b' => 'a.bc_id = b.bc_id and a.bc_id ='.$bc_id,
					'user_tbl c' => 'a.created_by = c.user_id',
					'status_tbl d' => 'd.status_id = a.broiler_trans_status and a.broiler_trans_status != 5',
					'broiler_subgroup_tbl e' => 'a.broiler_subgroup_id = e.broiler_subgroup_id',
					'broiler_group_tbl f' => 'e.broiler_group_id = f.broiler_group_id',
					'broiler_trans_dtl_tbl g' => 'a.broiler_trans_id = g.broiler_trans_id and YEAR(g.broiler_trans_date)='.$broiler_trans_date
				);
				$get_broiler_trans_yearly = $this->admin->get_join('broiler_trans_tbl a', $join_broiler_trans, FALSE,'f.broiler_group_name ASC', 'f.broiler_group_id, YEAR(g.broiler_trans_date)');
				$broiler_trans = '';
				if($get_broiler_trans_yearly){
					foreach($get_broiler_trans_yearly as $row){
					
						$broiler_trans .= '<tr><td>' . $row->broiler_group_name .'</td>';
						$broiler_trans .= '<td>' . $row->bc_name .'</td>';
						$broiler_trans .= '<td>' . $row->user_fname.' '.$row->user_lname .'</td>';
						if($row->status_id == 1){
							$broiler_trans .= '<td class="text-center"><a href="'.base_url('ahg/view-broiler-trans/' . encode($row->bc_id).'/'. encode($row->broiler_group_id).'/'.encode($row->broiler_group_name) . '/' . encode(date( 'Y', strtotime($row->broiler_trans_date)))).'" class="brn btn-xs glyphicon glyphicon-file edit-broiler-config" title="View"></a>&nbsp;&nbsp;<a href="'.base_url('ahg/post-broiler-trans/' . encode($row->broiler_trans_id).'/'.encode($row->bc_id).'/'.encode($row->broiler_group_id).'/'.encode($row->broiler_group_name)).'" class="brn btn-xs btn-xs glyphicon glyphicon-lock edit-broiler-config" title="Post"></a></td>';
						} else {
							$broiler_trans .= '<td class="text-center"><a href="'.base_url('ahg/view-broiler-trans/' . encode($row->bc_id).'/'. encode($row->broiler_group_id).'/'.encode($row->broiler_group_name) . '/' . encode(date( 'Y', strtotime($row->broiler_trans_date)))).'" class="brn btn-xs btn-success edit-broiler-config">View</a></td>';
						}
						$broiler_trans .= '</tr>';
					}
					$data['broiler_trans'] = $broiler_trans;
					$data['result'] = 1;
				} else {
					$data['result'] = 1;
				}
				
			} else {
				$data['result'] = 0;
			}
			
				
		} else{
			$data['result'] = 0;
		}

		echo json_encode($data);
	}

	public function view_broiler_summary($bc_id, $trans_year){
		$info = $this->_require_login();
		$data['bc_id'] = $bc_id;
		$data['trans_year'] = $trans_year;
		$id = decode($bc_id);
		$year = decode($trans_year);
		$join_broiler_cost_summary = array(
			'broiler_cost_summary_tbl b' => 'a.broiler_group_id = b.broiler_group_id and b.bc_id ='.$id.' and YEAR(b.trans_date) = '.$year
		);
		$data['broiler_group'] = $this->admin->get_join('broiler_group_tbl a', $join_broiler_cost_summary, FALSE,'a.broiler_group_name ASC', 'a.broiler_group_id');

		$data['title'] = 'Broiler Cost';
		$data['content'] = $this->load->view('ahg/ahg_view_broiler_cost_summary_content', $data , TRUE);
		$this->load->view('ahg/templates', $data);
	}
}
?>