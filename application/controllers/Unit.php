<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Unit extends CI_Controller {

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
					return $login;
				}elseif($user_type == "4"){
					redirect('admin/broiler-cost');
				}elseif($user_type == "5"){
					redirect('admin/production-cost');
				}elseif($user_type == "6"){
					redirect('region');
				}elseif($user_type == "7"){
					redirect('national');
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
		$join_cost = array(
			'user_unit_tbl b' => 'a.user_id = b.user_id AND a.user_status_id = 1 AND b.user_unit_status = 1 AND a.user_id = ' . $user_id,
			'company_unit_tbl c' => 'b.company_unit_id = c.company_unit_id',
			'cost_center_tbl d' => 'c.cost_center = d.cost_center_code AND d.cost_center_status = 1'
		);
		$select = '*, c.company_unit_id as company_unit';

		$check_cost = $this->admin->get_join('user_tbl a', $join_cost, TRUE, FALSE, FALSE, $select);
		$data['cost_center_id'] = @$check_cost->cost_center_id;
		$data['cost_center_code'] = @$check_cost->cost_center_code;
		$data['company_unit_id'] = @$check_cost->company_unit;
		$data['cost_center_desc'] = @$check_cost->cost_center_desc;

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

	public function dashboard(){
		$info = $this->_require_login();
		$data['title'] = 'Dashboard';

		$user_info = $this->get_user_info();
		$company_unit_id = !$user_info['company_unit_id'] ? 0 : $user_info['company_unit_id'];
		$data['unit_id'] = $company_unit_id;

		$year = $this->_active_year();

		$data['year'] = $year;
 		$join_unit_trans = array(
			'cost_center_tbl b' => 'a.cost_center_id = b.cost_center_id AND a.dashboard_unit_trans_year = ' . $year . ' AND a.company_unit_id = ' . $company_unit_id,
			'dashboard_transaction_status_tbl c' => 'a.dashboard_trans_status_id = c.dashboard_trans_status_id',
			'user_tbl d' => 'a.user_id = d.user_id'
		);

		$data['trans_unit'] = $this->admin->get_join('dashboard_unit_transaction_tbl a', $join_unit_trans, FALSE,'a.dashboard_unit_trans_added DESC');

		$data['content'] = $this->load->view('unit/unit_dashboard_transaction_content', $data , TRUE);
		$this->load->view('unit/templates', $data);
	}

	public function add_unit_trans(){
		$info = $this->_require_login();
		$user_id = decode($info['user_id']);

		$user_info = $this->get_user_info();
		$cost_center_id = $user_info['cost_center_id'];
		$company_unit = $user_info['company_unit_id'];

		if($_SERVER['REQUEST_METHOD'] == 'POST'){
			$year = clean_data($this->input->post('year'));

			if(!empty($company_unit) && !empty($year)){
				$check_trans = $this->admin->check_data('dashboard_unit_transaction_tbl', 'company_unit_id = ' . $company_unit . ' AND dashboard_unit_trans_year = ' . $year . ' AND dashboard_trans_status_id IN (1, 2, 4)');
				if($check_trans == FALSE){
					$set = array(
						'company_unit_id' => $company_unit,
						'cost_center_id' => $cost_center_id,
						'user_id' => $user_id,
						'dashboard_unit_trans_year' => $year,
						'dashboard_unit_trans_added' => date_now(),
						'dashboard_trans_status_id' => 1
					);

					$result = $this->admin->insert_data('dashboard_unit_transaction_tbl', $set, TRUE);

					if($result == TRUE){	
						$msg = '<div class="alert alert-success">Report is now in Queue.</div>';
						$this->session->set_flashdata('message', $msg);
						redirect($_SERVER['HTTP_REFERER']);
					}else{
						$msg = '<div class="alert alert-danger">Error while inserting data. Please try again!</div>';
					}
				}else{
					$msg = '<div class="alert alert-danger">Error Report Cron on going. Please try again later!</div>';
				}
				
			}else{
				$msg = '<div class="alert alert-danger">Error empty data. Please try again!</div>';
			}
		}else{
			redirect('admin');
		}

		$this->session->set_flashdata('message', $msg);
		redirect($_SERVER['HTTP_REFERER']);
	}

	public function index(){
		$this->capex_info();
		// $this->materials();
	}

	public function materials(){
		$this->index();
	}

	public function check_module($module, $year){
		$info = $this->_require_login();
		$user_id = decode($info['user_id']);
		$user_info = $this->get_user_info();

		$unit = !$user_info['company_unit_id'] ? 0 : $user_info['company_unit_id'];
		$join_lock = array(
			'module_tbl b' => 'a.module_id = b.module_id AND b.module_name ="' . $module . '" AND a.lock_year = ' . $year,
			'lock_type_tbl c' => "a.lock_type_id = c.lock_type_id AND c.lock_type_name = 'Unit'",
			'lock_status_tbl d' => 'a.lock_status_id = d.lock_status_id AND d.lock_status_name != "Cancel"',
			'company_unit_tbl e' => 'a.lock_location_id = e.company_unit_id AND e.company_unit_id = ' . $unit
		);

		$check_lock = $this->admin->check_join('lock_tbl a', $join_lock, TRUE);
		
		if($check_lock['result'] == TRUE){
			$lock_status = $check_lock['info']->lock_status_name;
			if($lock_status == 'Unlocked'){
				$status = 1;
			}elseif($lock_status == 'Locked'){
				$status = 0;
			}
		}else{
			$status = 0;
		}

		return $status;
	}

	public function _get_designated_tbl($company_unit_id=NULL, $cost_center_id=NULL){
		
		if($company_unit_id===NULL){
			$user_info = $this->get_user_info();
			$company_unit_id = $user_info['company_unit_id'];
			$cost_center_id = $user_info['cost_center_id'];
		}

		$gl_transaction_tbl									= 'gl_transaction_tbl';
		$gl_transaction_item_tbl							= 'gl_transaction_item_tbl';
		$gl_transaction_details_tbl							= 'gl_transaction_details_tbl';
		$asset_group_transaction_tbl						= 'asset_group_transaction_tbl';
		$asset_group_transaction_item_tbl					= 'asset_group_transaction_item_tbl';
		$asset_group_transaction_details_tbl				= 'asset_group_transaction_details_tbl';
		$asset_group_transaction_rank_tbl					= 'asset_group_transaction_rank_tbl';
		$depreciation_bc_tbl								= 'depreciation_bc_tbl';
		$depreciation_unit_tbl								= 'depreciation_unit_tbl';
		if($company_unit_id){
			// if($company_unit_id == 17 || $company_unit_id == 22){ //COMMISSARY OR LIEMPO PRODUCTION
			if($company_unit_id == 19){ //COMMISSARY OR LIEMPO PRODUCTION
				$gl_transaction_tbl							= 'rep_gl_transaction_tbl';
				$gl_transaction_item_tbl					= 'rep_gl_transaction_item_tbl';
				$gl_transaction_details_tbl					= 'rep_gl_transaction_details_tbl';
				$asset_group_transaction_tbl				= 'rep_asset_group_transaction_tbl';
				$asset_group_transaction_item_tbl			= 'rep_asset_group_transaction_item_tbl';
				$asset_group_transaction_details_tbl		= 'rep_asset_group_transaction_details_tbl';
				$asset_group_transaction_rank_tbl			= 'rep_asset_group_transaction_rank_tbl';
				$depreciation_bc_tbl						= 'rep_depreciation_bc_tbl';
				$depreciation_unit_tbl						= 'rep_depreciation_unit_tbl';
			}
		}

		$data['gl_transaction_tbl'] 						= $gl_transaction_tbl;
		$data['gl_transaction_item_tbl'] 					= $gl_transaction_item_tbl;
		$data['gl_transaction_details_tbl'] 				= $gl_transaction_details_tbl;
		$data['asset_group_transaction_tbl'] 				= $asset_group_transaction_tbl;
		$data['asset_group_transaction_item_tbl'] 			= $asset_group_transaction_item_tbl;
		$data['asset_group_transaction_details_tbl'] 		= $asset_group_transaction_details_tbl;
		$data['asset_group_transaction_rank_tbl'] 			= $asset_group_transaction_rank_tbl;
		$data['depreciation_bc_tbl'] 						= $depreciation_bc_tbl;
		$data['depreciation_unit_tbl'] 						= $depreciation_unit_tbl;

		$object = (object) $data;
		return $object;
	}

	public function opex_info($year = null){

		
		
		$info = $this->_require_login();
		$user_id = decode($info['user_id']);
		$user_info = $this->get_user_info();
		$company_unit_id = $user_info['company_unit_id'];
		$data['unit_id'] = $company_unit_id;

		$designated_tbl = $this->_get_designated_tbl();
		

		$module = 'OPEX';
		if($year == null){
			$year = $this->_active_year();
		}

		$cost_center_id = $user_info['cost_center_id'];
		$cost_center_code = $user_info['cost_center_code'];
		$cost_center_desc = $user_info['cost_center_desc'];
		$data['id'] = $cost_center_code;
		$data['cost_center_desc'] =  $cost_center_desc;
		$data['title'] = 'OPEX Info';

		$unit = $user_info['company_unit_id'];
		$data['budget_status'] = $this->check_module($module, $year);

		$join_cost = array(
			'cost_center_tbl b' => 'a.cost_center_id=b.cost_center_id AND a.gl_trans_status=1 AND b.cost_center_id=' . $cost_center_id . ' AND a.gl_year = ' . $year,
			'gl_group_tbl c' => 'a.gl_group_id=c.gl_group_id',
			'user_tbl d' => 'a.user_id=d.user_id'
		);

		$data['cost_center'] = encode($cost_center_id);
		$sw_join = array(
			'cost_center_tbl b' => 'a.cost_center_id = b.cost_center_id AND a.emp_salary_trans_status = 1 AND b.cost_center_id = ' . $cost_center_id . ' AND a.emp_salary_trans_year = ' . $year
		);

		$get_sw_group = $this->admin->get_join('employee_salary_trans_tbl a', $sw_join);
		$data['sw'] = $get_sw_group;
		$data['gl_group'] = $this->admin->get_join($designated_tbl->gl_transaction_tbl.' a', $join_cost);

		/*$join_opex = array(
			'gl_transaction_item_tbl b' => 'a.gl_trans_item_id = b.gl_trans_item_id AND b.gl_trans_item_status = 1 AND a.gl_trans_det_status=1',
			'gl_transaction_tbl c' => 'b.gl_trans_id=c.gl_trans_id AND c.gl_trans_status=1 AND c.gl_year=' . $year,
			'gl_subgroup_tbl d' => 'b.gl_sub_id=d.gl_sub_id',
			'gl_group_tbl e' => 'd.gl_group_id = e.gl_group_id AND e.gl_group_show = 1',
			'cost_center_tbl f' => 'b.cost_center_id = f.cost_center_id AND f.parent_id=' . $cost_center_id
		);

		$opex_gl = $this->admin->get_join('gl_transaction_details_tbl a', $join_opex, FALSE, 'e.gl_group_id, d.gl_sub_name, total_amount DESC', 'b.gl_sub_id', 'e.gl_group_name, d.gl_sub_name, SUM(a.opex_amount) total_amount
			');*/


		$sql = '

			SELECT gl_group_name, gl_sub_name, (opex) as opex, (opex1) as opex1, (opex2) as opex2

			FROM 
			(
				(SELECT `e`.`gl_group_name`, `d`.`gl_sub_name`, SUM(a.opex_amount) opex, 0 as opex1, 0 opex2 FROM `'.$designated_tbl->gl_transaction_details_tbl.'` `a` JOIN `'.$designated_tbl->gl_transaction_item_tbl.'` `b` ON `a`.`gl_trans_item_id` = `b`.`gl_trans_item_id` AND `b`.`gl_trans_item_status` = 1 AND `a`.`gl_trans_det_status`=1 JOIN `'.$designated_tbl->gl_transaction_tbl.'` `c` ON `b`.`gl_trans_id`=`c`.`gl_trans_id` AND `c`.`gl_trans_status`=1 AND `c`.`gl_year`= ' . $year . ' JOIN `gl_subgroup_tbl` `d` ON `b`.`gl_sub_id`=`d`.`gl_sub_id` JOIN `gl_group_tbl` `e` ON `d`.`gl_group_id` = `e`.`gl_group_id` AND `e`.`gl_group_show` = 1 JOIN `cost_center_tbl` `f` ON `b`.`cost_center_id` = `f`.`cost_center_id` AND `f`.`parent_id`= '. $cost_center_id . ' GROUP BY `b`.`gl_sub_id` ORDER BY `e`.`gl_group_id`, `d`.`gl_sub_name`, `opex` DESC
				)

				UNION

				(SELECT z.gl_group_name, y.gl_sub_name, 0 as opex, SUM(x.cost) as opex1, 0 as opex2 FROM comparative_opex_dept_tbl x, gl_subgroup_tbl y, gl_group_tbl z WHERE x.gl_sub_id = y.gl_sub_id AND y.gl_group_id = z.gl_group_id AND x.company_unit_id = ' . $company_unit_id . ' AND x.comp_opex_dept_status = 1 AND YEAR(x.trans_year) = ' . ($year - 1) . ' AND z.gl_group_name != "DEPRECIATION EXPENSES" GROUP BY z.gl_group_id, y.gl_sub_id
				)

				UNION

				(SELECT z.gl_group_name, y.gl_sub_name, 0 as opex, 0 as opex1, SUM(x.cost) as opex2 FROM comparative_opex_dept_tbl x, gl_subgroup_tbl y, gl_group_tbl z WHERE x.gl_sub_id = y.gl_sub_id AND y.gl_group_id = z.gl_group_id AND x.company_unit_id = ' . $company_unit_id . ' AND x.comp_opex_dept_status = 1 AND YEAR(x.trans_year) = ' . ($year - 2) . ' AND z.gl_group_name != "DEPRECIATION EXPENSES" GROUP BY z.gl_group_id, y.gl_sub_id
				)
			) as opex_data


			ORDER BY
				gl_group_name, gl_sub_name

		';
		// echo $sql;
		// exit;
		$opex_gl = $this->admin->get_query($sql);

		


		//Depreciation per asset subgroup

		$depre_sub = $this->get_depreciation_subgroup($cost_center_id, $year, $company_unit_id);
		
		$depre = "DEPRECIATION EXPENSES";
		foreach($depre_sub as $row_sub){
			$gl_sub_name = $row_sub->gl_sub_name;
			$depre_amount = $row_sub->total;
			$depre_amount1 = $row_sub->total1;
			$depre_amount2 = $row_sub->total2;
			$arr_depre = new stdClass;
			$arr_depre->gl_group_name = $depre;
			$arr_depre->gl_sub_name = $gl_sub_name;
			$arr_depre->opex = $depre_amount;
			$arr_depre->opex1 = $depre_amount1;
			$arr_depre->opex2 = $depre_amount2;
			array_push($opex_gl, $arr_depre);			
		}
		// $opex_gl = ksort($opex_gl);

		// $custom_order = $opex_gl;
		// $arr = array();
		// uksort($arr, function($a, $b) use($custom_order){
			// return array_search($a,$custom_order) - array_search($b,$custom_order);
		// });
		// $opex_gl = $custom_order;
		// echo '<pre>';
		// print_r($opex_gl);
		// echo '</pre>';
		// exit;

		$get_depreciation_yee = $this->admin->get_query('SELECT SUM(a.cost) as depreciation_yee FROM comparative_opex_dept_tbl a, gl_subgroup_tbl b, gl_group_tbl c WHERE a.gl_sub_id = b.gl_sub_id AND b.gl_group_id = c.gl_group_id AND c.gl_group_name = "DEPRECIATION EXPENSES" AND a.cost_center_id = ' . $cost_center_id, TRUE);
		$depreciation_yee = 0;

		$data['opex_gl'] = $opex_gl;
		$data['year'] = $year;

		$this->_opex_summary($year);

		$data['content'] = $this->load->view('unit/unit_opex_info_content', $data , TRUE);
		$this->load->view('unit/templates', $data);
		
	}

	public function _opex_summary($year){
		$info = $this->_require_login();
		$user_id = decode($info['user_id']);
		$user_info = $this->get_user_info();
		$company_unit_id = $user_info['company_unit_id'];
		$cost_center = $user_info['cost_center_id'];

		$designated_tbl = $this->_get_designated_tbl();

		$bar = $this->admin->get_query('SELECT DATE_FORMAT(c.opex_budget_date, "%b %Y") as budget_date, e.gl_group_name as gl_group, SUM(c.opex_amount) as amount, MONTH(c.opex_budget_date) as month, e.gl_color as color, d.gl_sub_id, f.cost_center_id, f.cost_center_group_id FROM '.$designated_tbl->gl_transaction_tbl.' a, '.$designated_tbl->gl_transaction_item_tbl.' b, '.$designated_tbl->gl_transaction_details_tbl.' c, gl_subgroup_tbl d, gl_group_tbl e, cost_center_tbl f, transaction_type_tbl g WHERE a.gl_trans_id=b.gl_trans_id AND b.gl_trans_item_id=c.gl_trans_item_id AND b.gl_sub_id=d.gl_sub_id AND d.gl_group_id=e.gl_group_id AND b.cost_center_id=f.cost_center_id AND a.trans_type_id=g.trans_type_id AND f.parent_id=' . $cost_center . ' AND a.gl_trans_status=1 AND b.gl_trans_item_status=1 AND c.gl_trans_det_status=1 AND e.gl_group_show=1 AND g.trans_type_id=1 AND YEAR(c.opex_budget_date)=' . $year . ' GROUP BY d.gl_sub_id, f.cost_center_id, c.opex_budget_date ORDER BY d.gl_sub_id, f.cost_center_id, c.opex_budget_date ASC');
		$arr_budget_date = array();
		$arr_group = array();
		$arr_gl = array();
		$count = 0;

		$this->db->trans_start();

		foreach($bar as $row){
			$budget_date = date('Y-m-d', strtotime($row->budget_date));
			$cost_center_id = $row->cost_center_id;
			$cost_center_group_id = $row->cost_center_group_id;
			$gl_sub_id = $row->gl_sub_id;
			$amount = $row->amount;

			$where = array(
				'company_unit_id' => $company_unit_id,
				'gl_sub_id' => $gl_sub_id,
				'cost_center_id' => $cost_center_id,
				'dashboard_opex_unit_date = ' => $budget_date,
				'dashboard_opex_unit_status' => 1
			);
			$check_summary = $this->admin->check_data('dashboard_opex_unit_tbl', $where, TRUE);
			if($check_summary['result'] == TRUE){
				$id = $check_summary['info']->dashboard_opex_unit_id;
				$set_summary = array('dashboard_opex_unit_amount' => $amount);
				$where_summary = array('dashboard_opex_unit_id' => $id);
				$update_summary = $this->admin->update_data('dashboard_opex_unit_tbl', $set_summary, $where_summary);
			}else{
				$set_summary = array(
					'company_unit_id' => $company_unit_id,
					'gl_sub_id' => $gl_sub_id,
					'cost_center_group_id' => $cost_center_group_id,
					'cost_center_id' => $cost_center_id,
					'dashboard_opex_unit_amount' => $amount,
					'dashboard_opex_unit_date' => $budget_date,
					'dashboard_opex_unit_added' => date_now(),
					'dashboard_opex_unit_status' => 1
				);

				$insert_summary = $this->admin->insert_data('dashboard_opex_unit_tbl', $set_summary);
			}
		}



		$get_depreciation = $this->opex_line_capex(encode($cost_center), TRUE, $year, $company_unit_id);

		$depre = 'DEPRECIATION EXPENSES';
		$arr_group[$depre] = $depre;

		$arr_gl[$depre]['asset'] = $depre;
		$arr_gl[$depre]['color'] = '#f0cee4';
		$arr_gl[$depre]['amount'] = array();

		for($a = 0; $a < 12; $a++){
			$total_depreciation = 0;
			if (isset($get_depreciation['info'][$a+1]) && is_array($get_depreciation['info'][$a+1]) && isset($get_depreciation['info'][$a+1]['amount'])) {
				$total_depreciation = $get_depreciation['info'][$a+1]['amount'];
			}
			$arr_gl[$depre]['amount'][] = $total_depreciation;
		}

		$data['result'] = 1;
		$data['month'] = $arr_budget_date;
		$data['group'] = $arr_group;
		$data['group_amount'] = $arr_gl;


		$line = $this->admin->get_query('SELECT e.ag_name as asset_group, (b.capex_price / b.capex_lifespan) as avg_opex, (13-MONTH(c.capex_budget_date)) as remaining_month, MONTH(c.capex_budget_date) as budget_month, c.capex_budget_date as budget_date, c.capex_qty, f.cost_center_id, f.cost_center_group_id, h.gl_sub_id FROM '.$designated_tbl->asset_group_transaction_tbl.' a, '.$designated_tbl->asset_group_transaction_item_tbl.' b, '.$designated_tbl->asset_group_transaction_details_tbl.' c, asset_subgroup_tbl d, asset_group_tbl e, cost_center_tbl f, transaction_type_tbl g, gl_subgroup_tbl h WHERE a.ag_trans_id=b.ag_trans_id AND b.ag_trans_item_id=c.ag_trans_item_id AND b.asg_id=d.asg_id AND d.ag_id=e.ag_id AND b.cost_center_id=f.cost_center_id AND a.trans_type_id=g.trans_type_id AND e.ag_gl_code = h.gl_code AND a.ag_trans_status=1 AND b.ag_trans_item_status=1 AND c.ag_trans_det_status=1 AND g.trans_type_id=1 AND f.parent_id=' . $cost_center . ' AND YEAR(c.capex_budget_date)=' . $year);

		$line_actual = $this->admin->get_query('(SELECT "DEPRECIATION EXPENSES", SUM(x.depreciation_unit_amount) as avg_opex, 1 as remaining_month, MONTH(x.depreciation_unit_date) as budget_month, x.depreciation_unit_date as budget_date, x.depreciation_unit_date, 1 as capex_qty, "DEPRECIATION ACTUAL" as depre_type, y.gl_sub_id, y1.cost_center_id, y1.cost_center_group_id FROM '.$designated_tbl->depreciation_unit_tbl.' x, gl_subgroup_tbl y, gl_group_tbl x1, cost_center_tbl y1 WHERE x.gl_sub_id = y.gl_sub_id AND y.gl_group_id = x1.gl_group_id AND x.cost_center_id = y1.cost_center_id AND YEAR(x.depreciation_unit_date) = ' . $year . ' AND x.company_unit_id = ' . $company_unit_id . ' AND x.depreciation_unit_status = 1 GROUP BY y.gl_sub_id, y1.cost_center_id, x.depreciation_unit_date)');

		$month = array(
			'1' => 'Jan ' . $year,
			'2' => 'Feb ' . $year,
			'3' => 'Mar ' . $year,
			'4' => 'Apr ' . $year,
			'5' => 'May ' . $year,
			'6' => 'Jun ' . $year,
			'7' => 'Jul ' . $year,
			'8' => 'Aug ' . $year,
			'9' => 'Sep ' . $year,
			'10' => 'Oct ' . $year,
			'11' => 'Nov ' . $year,
			'12' => 'Dec ' . $year,
		);

		$line_data = array(
			'1' => array('month' => 'Jan ' . $year, 'amount' => 0),
			'2' => array('month' => 'Feb ' . $year, 'amount' => 0),
			'3' => array('month' => 'Mar ' . $year, 'amount' => 0),
			'4' => array('month' => 'Apr ' . $year, 'amount' => 0),
			'5' => array('month' => 'May ' . $year, 'amount' => 0),
			'6' => array('month' => 'Jun ' . $year, 'amount' => 0),
			'7' => array('month' => 'Jul ' . $year, 'amount' => 0),
			'8' => array('month' => 'Aug ' . $year, 'amount' => 0),
			'9' => array('month' => 'Sep ' . $year, 'amount' => 0),
			'10' => array('month' => 'Oct ' . $year, 'amount' => 0),
			'11' => array('month' => 'Nov ' . $year, 'amount' => 0),
			'12' => array('month' => 'Dec ' . $year, 'amount' => 0)
		);
		$total = 0;
		$line_arr = array();
		foreach($line as $row){
			$budget_date = $row->budget_date;
			$cost_center_id = $row->cost_center_id;
			$cost_center_group_id = $row->cost_center_group_id;
			$gl_sub_id = $row->gl_sub_id;
			$avg_opex = $row->avg_opex;
			$remaining_month = $row->remaining_month;
			$qty = $row->capex_qty;

			$budget_month = $row->budget_month;
			$a = 0;
			for($a = $budget_month; $a <= 12; $a++){
				$total_amount = $avg_opex * $row->capex_qty;
				if(array_key_exists($budget_date . $cost_center_id . $gl_sub_id, $line_arr)){
					$line_arr[$budget_date . $cost_center_id . $gl_sub_id]['amount'] += $total_amount;
				}else{
					$line_arr[$budget_date . $cost_center_id . $gl_sub_id]['amount'] = $total_amount;
					$line_arr[$budget_date . $cost_center_id . $gl_sub_id]['budget_date'] = $budget_date;
					$line_arr[$budget_date . $cost_center_id . $gl_sub_id]['budget_month'] = $budget_month;
					$line_arr[$budget_date . $cost_center_id . $gl_sub_id]['cost_center_id'] = $cost_center_id;
					$line_arr[$budget_date . $cost_center_id . $gl_sub_id]['cost_center_group_id'] = $cost_center_group_id;
					$line_arr[$budget_date . $cost_center_id . $gl_sub_id]['gl_sub_id'] = $gl_sub_id;
				}
			}
		}

		foreach($line_arr as $row){
			$budget_date = date('Y-m-d', strtotime($row['budget_date']));
			$budget_month = $row['budget_month'];
			$avg_opex = $row['amount'];

			$cost_center_id = $row['cost_center_id'];
			$cost_center_group_id = $row['cost_center_group_id'];
			$gl_sub_id = $row['gl_sub_id'];

			$where = array(
				'company_unit_id' => $company_unit_id,
				'gl_sub_id' => $gl_sub_id,
				'cost_center_id' => $cost_center_id,
				'dashboard_opex_unit_date = ' => $budget_date,
				'dashboard_opex_unit_status' => 1
			);
			$check_summary = $this->admin->check_data('dashboard_opex_unit_tbl', $where, TRUE);
			if($check_summary['result'] == TRUE){
				$id = $check_summary['info']->dashboard_opex_unit_id;
				$set_summary = array('dashboard_opex_unit_amount' => $avg_opex);
				$where_summary = array('dashboard_opex_unit_id' => $id);
				$update_summary = $this->admin->update_data('dashboard_opex_unit_tbl', $set_summary, $where_summary);
			}else{
				$set_summary = array(
					'company_unit_id' => $company_unit_id,
					'gl_sub_id' => $gl_sub_id,
					'cost_center_group_id' => $cost_center_group_id,
					'cost_center_id' => $cost_center_id,
					'dashboard_opex_unit_amount' => $avg_opex,
					'dashboard_opex_unit_date' => $budget_date,
					'dashboard_opex_unit_added' => date_now(),
					'dashboard_opex_unit_status' => 1
				);

				$insert_summary = $this->admin->insert_data('dashboard_opex_unit_tbl', $set_summary);
			}

		}

		foreach($line_actual as $row){
			$budget_date = $row->budget_date;
			$avg_opex = $row->avg_opex;

			$budget_date = $row->budget_date;
			$cost_center_id = $row->cost_center_id;
			$cost_center_group_id = $row->cost_center_group_id;
			$gl_sub_id = $row->gl_sub_id;

			$where = array(
				'company_unit_id' => $company_unit_id,
				'gl_sub_id' => $gl_sub_id,
				'cost_center_id' => $cost_center_id,
				'YEAR(dashboard_opex_unit_date) = ' => $year,
				'dashboard_opex_unit_status' => 1
			);
			$check_summary = $this->admin->check_data('dashboard_opex_unit_tbl', $where, TRUE);
			if($check_summary['result'] == TRUE){
				$id = $check_summary['info']->dashboard_opex_unit_id;
				$set_summary = array('dashboard_opex_unit_amount' => $avg_opex);
				$where_summary = array('dashboard_opex_unit_id' => $id);
				$update_summary = $this->admin->update_data('dashboard_opex_unit_tbl', $set_summary, $where_summary);
			}else{
				$set_summary = array(
					'company_unit_id' => $company_unit_id,
					'gl_sub_id' => $gl_sub_id,
					'cost_center_group_id' => $cost_center_group_id,
					'cost_center_id' => $cost_center_id,
					'dashboard_opex_unit_amount' => $avg_opex,
					'dashboard_opex_unit_date' => date('Y-m-d', strtotime($budget_date)),
					'dashboard_opex_unit_added' => date_now(),
					'dashboard_opex_unit_status' => 1
				);

				$insert_summary = $this->admin->insert_data('dashboard_opex_unit_tbl', $set_summary);
			}
		}

		if($this->db->trans_status() === FALSE){
			$this->db->trans_rollback();
		}else{
			$this->db->trans_commit();
		}
	}

	public function cancel_opex(){
		$info = $this->_require_login();
		if($_SERVER['REQUEST_METHOD'] == 'POST'){
			$designated_tbl = $this->_get_designated_tbl();
			$id = decode($this->input->post('id'));
			$where = array('gl_trans_id' => $id);
			$join_id = array(
				'cost_center_tbl b' => 'a.cost_center_id = b.cost_center_id AND a.gl_trans_id = ' . $id				
			);
			$check_id = $this->admin->check_join($designated_tbl->gl_transaction_tbl.' a', $join_id, TRUE);
			if($check_id['result'] == TRUE){
				$cost_center_code = $check_id['info']->cost_center_code;
				$set = array('gl_trans_status' => 0);
				$remove_opex = $this->admin->update_data($designated_tbl->gl_transaction_tbl, $set, $where);
				
				if($remove_opex == TRUE){
					$msg = '<div class="alert alert-success">CAPEX successfully removed.</strong></div>';
				}else{
					$msg = '<div class="alert alert-danger">Error please try again!</div>';
				}

				$this->session->set_flashdata('message', $msg);
				redirect('unit/opex-info/');
			}else{
				$msg = '<div class="alert alert-danger">Error please try again!</div>';
				$this->session->set_flashdata('message', $msg);
				redirect('unit/opex-info/');
			}
		}
	}

	public function cancel_sw_opex(){
		$info = $this->_require_login();
		if($_SERVER['REQUEST_METHOD'] == 'POST'){
			$id = decode($this->input->post('id'));
			$where = array('emp_salary_trans_id' => $id);
			$check_id = $this->admin->check_data('employee_salary_trans_tbl', $where);
			if($check_id == TRUE){
				$set = array('emp_salary_trans_status' => 0);
				$remove_opex = $this->admin->update_data('employee_salary_trans_tbl', $set, $where);
				
				if($remove_opex == TRUE){
					$msg = '<div class="alert alert-success">OPEX successfully removed.</strong></div>';
				}else{
					$msg = '<div class="alert alert-danger">Error please try again!</div>';
				}

				$this->session->set_flashdata('message', $msg);
				redirect($_SERVER['HTTP_REFERER']);
			}else{
				$msg = '<div class="alert alert-danger">Error please try again!</div>';
				$this->session->set_flashdata('message', $msg);
				redirect($_SERVER['HTTP_REFERER']);
			}
		}
	}

	public function get_salary($id, $month = FALSE, $year){
		$info = $this->_require_login();

		$year_active = $year;
		if($month == FALSE){
			$join_salary = array(
				'employee_salary_item_tbl b' => 'a.emp_salary_item_id = b.emp_salary_item_id AND a.emp_salary_trans_dtl_status = 1 AND b.emp_salary_item_status = 1',
				'employee_salary_trans_tbl c' => 'b.emp_salary_trans_id = c.emp_salary_trans_id AND c.emp_salary_trans_status = 1 AND YEAR(a.emp_salary_date)=' . $year_active,
				'employee_tbl d' => 'b.emp_id = d.emp_id',
				'cost_center_tbl e' => 'b.cost_center_id = e.cost_center_id AND e.parent_id =' . $id
			);
			$get_salary = $this->admin->get_join('employee_salary_trans_dtl_tbl a', $join_salary);
			$grand_total = 0;
			foreach($get_salary as $row_salary){
				$basic_salary = $row_salary->emp_salary_budget;
				$grand_total += $basic_salary;
			}
			return $grand_total;
		}else{
			$join_salary = array(
				'employee_salary_item_tbl b' => 'a.emp_salary_item_id = b.emp_salary_item_id AND a.emp_salary_trans_dtl_status = 1 AND b.emp_salary_item_status = 1',
				'employee_salary_trans_tbl c' => 'b.emp_salary_trans_id = c.emp_salary_trans_id AND c.emp_salary_trans_status = 1 AND YEAR(a.emp_salary_date)=' . $year_active,
				'employee_tbl d' => 'b.emp_id = d.emp_id',
				'cost_center_tbl e' => 'b.cost_center_id = e.cost_center_id AND e.parent_id =' . $id
			);
			$get_salary = $this->admin->get_join('employee_salary_trans_dtl_tbl a', $join_salary, FALSE, 'MONTH(a.emp_salary_date) ASC', 'MONTH(a.emp_salary_date)', 'SUM(a.emp_salary_budget) as total_salary');

			return $get_salary;
		}
	}

	public function get_salary_hmo($id, $month=FALSE, $year){
		$info = $this->_require_login();

		$year_active = $year;
		if($month == FALSE){
			$join_salary = array(
				'employee_salary_item_tbl b' => 'a.emp_salary_item_id = b.emp_salary_item_id AND a.emp_salary_trans_dtl_status = 1 AND b.emp_salary_item_status = 1',
				'employee_salary_trans_tbl c' => 'b.emp_salary_trans_id = c.emp_salary_trans_id AND c.emp_salary_trans_status = 1 AND YEAR(a.emp_salary_date)=' . $year_active,
				'employee_tbl d' => 'b.emp_id = d.emp_id',
				'cost_center_tbl e' => 'b.cost_center_id = e.cost_center_id AND e.parent_id =' . $id,
				'rank_tbl f' => 'b.rank_id = f.rank_id',
			);
			$get_salary = $this->admin->get_join('employee_salary_trans_dtl_tbl a', $join_salary);
			$grand_total = 0;
			foreach($get_salary as $row_salary){
				$basic_salary = $row_salary->emp_salary_budget;
				$join_contribution = array(
					'employee_contribution_tbl b' => "a.emp_cont_id = b.emp_cont_id AND emp_config_status = 1 AND b.emp_cont_name='HMO'",
					'employee_contribution_type_tbl c' => 'a.emp_cont_type_id = c.emp_cont_type_id',
					'employee_config_rank_tbl d' => 'a.emp_config_id = d.emp_config_id AND d.rank_id=' . $row_salary->rank_id . '  AND a.emp_config_year = ' . $year
				);
				$check_contribution = $this->admin->check_join('employee_config_tbl a', $join_contribution, TRUE);
				if($check_contribution['result'] == TRUE){
					$contribution = $check_contribution['info']->emp_config_value;
					$contribution_type = $check_contribution['info']->emp_cont_type_name;
					$total_contribution = 0;
					if($contribution_type == 'FIXED'){
						$total_contribution = $contribution;
						$grand_total += $total_contribution;
					}elseif($contribution_type == 'PERCENTAGE'){
						$total_contribution = $basic_salary * $contribution;
						$grand_total += $total_contribution;
					}elseif($contribution_type == 'RANK'){
						$contribution = $check_contribution['info']->emp_config_rank_value;
						$total_contribution = $contribution / 12;
						$grand_total += $total_contribution;
					}
				}
			}

			return $grand_total;	
		}else{
			$join_salary = array(
				'employee_salary_item_tbl b' => 'a.emp_salary_item_id = b.emp_salary_item_id AND a.emp_salary_trans_dtl_status = 1 AND b.emp_salary_item_status = 1',
				'employee_salary_trans_tbl c' => 'b.emp_salary_trans_id = c.emp_salary_trans_id AND c.emp_salary_trans_status = 1 AND YEAR(a.emp_salary_date)=' . $year_active,
				'employee_tbl d' => 'b.emp_id = d.emp_id',
				'cost_center_tbl e' => 'b.cost_center_id = e.cost_center_id AND e.parent_id =' . $id,
				'rank_tbl f' => 'b.rank_id = f.rank_id',
				'employee_config_rank_tbl g' => 'b.rank_id = g.rank_id'
			);

			$get_salary = $this->admin->get_join('employee_salary_trans_dtl_tbl a', $join_salary, FALSE, 'MONTH(a.emp_salary_date) ASC', 'MONTH(a.emp_salary_date)', '*, SUM(g.emp_config_rank_value / 12) total_hmo');
			return $get_salary;
		}
		
	}

	public function get_salary_pagibig($id, $month = FALSE, $year){
		$info = $this->_require_login();

		$year_active = $year;
		if($month == FALSE){
			$join_salary = array(
				'employee_salary_item_tbl b' => 'a.emp_salary_item_id = b.emp_salary_item_id AND a.emp_salary_trans_dtl_status = 1 AND b.emp_salary_item_status = 1',
				'employee_salary_trans_tbl c' => 'b.emp_salary_trans_id = c.emp_salary_trans_id AND c.emp_salary_trans_status = 1 AND YEAR(a.emp_salary_date)=' . $year_active,
				'employee_tbl d' => 'b.emp_id = d.emp_id',
				'cost_center_tbl e' => 'b.cost_center_id = e.cost_center_id AND e.parent_id =' . $id
			);
			$get_salary = $this->admin->get_join('employee_salary_trans_dtl_tbl a', $join_salary);
			$grand_total = 0;
			foreach($get_salary as $row_salary){
				$basic_salary = $row_salary->emp_salary_budget;
				$join_contribution = array(
					'employee_contribution_tbl b' => "a.emp_cont_id = b.emp_cont_id AND emp_config_status = 1 AND b.emp_cont_name='PAGIBIG' AND '$basic_salary' BETWEEN a.emp_config_from AND a.emp_config_to",
					'employee_contribution_type_tbl c' => 'a.emp_cont_type_id = c.emp_cont_type_id AND a.emp_config_year = ' . $year
				);
				$check_contribution = $this->admin->check_join('employee_config_tbl a', $join_contribution, TRUE);
				if($check_contribution['result'] == TRUE){
					$contribution = $check_contribution['info']->emp_config_value;
					$contribution_type = $check_contribution['info']->emp_cont_type_name;
					$total_contribution = 0;
					if($contribution_type == 'FIXED'){
						$total_contribution = $contribution;
						$total_contribution;
						$grand_total += $total_contribution;
					}elseif($contribution_type == 'PERCENTAGE'){
						$total_contribution = $basic_salary * $contribution;
						$grand_total += $total_contribution;
					}
				}
			}
			return $grand_total;
		}else{
			$join_salary = array(
				'employee_salary_item_tbl b' => 'a.emp_salary_item_id = b.emp_salary_item_id AND a.emp_salary_trans_dtl_status = 1 AND b.emp_salary_item_status = 1',
				'employee_salary_trans_tbl c' => 'b.emp_salary_trans_id = c.emp_salary_trans_id AND c.emp_salary_trans_status = 1 AND YEAR(a.emp_salary_date)=' . $year_active,
				'employee_tbl d' => 'b.emp_id = d.emp_id',
				'cost_center_tbl e' => 'b.cost_center_id = e.cost_center_id AND e.parent_id =' . $id,
				'employee_config_tbl f' => "a.emp_salary_budget BETWEEN f.emp_config_from AND f.emp_config_to AND f.emp_config_status = 1",
				'employee_contribution_tbl g' => 'f.emp_cont_id = g.emp_cont_id AND g.emp_cont_status=1 AND g.emp_cont_name="PAGIBIG"',
				'employee_contribution_type_tbl h' => 'f.emp_cont_type_id = h.emp_cont_type_id'
			);

			$get_salary = $this->admin->get_join('employee_salary_trans_dtl_tbl a', $join_salary, FALSE, 'MONTH(a.emp_salary_date) ASC', FALSE,'(CASE WHEN h.emp_cont_type_name = "FIXED" THEN f.emp_config_value WHEN h.emp_cont_type_name = "PERCENTAGE" THEN a.emp_salary_budget * f.emp_config_value ELSE 0 END) as total_pagibig');
			return $get_salary;
			//echo $this->db->last_query();
		}
	}

	public function get_salary_accident($id, $month = FALSE, $year){
		$info = $this->_require_login();

		$year_active = $year;
		if($month == FALSE){
			$join_salary = array(
				'employee_salary_item_tbl b' => 'a.emp_salary_item_id = b.emp_salary_item_id AND a.emp_salary_trans_dtl_status = 1 AND b.emp_salary_item_status = 1',
				'employee_salary_trans_tbl c' => 'b.emp_salary_trans_id = c.emp_salary_trans_id AND c.emp_salary_trans_status = 1 AND YEAR(a.emp_salary_date)=' . $year_active,
				'employee_tbl d' => 'b.emp_id = d.emp_id',
				'cost_center_tbl e' => 'b.cost_center_id = e.cost_center_id AND e.parent_id =' . $id
			);
			$get_salary = $this->admin->get_join('employee_salary_trans_dtl_tbl a', $join_salary);
			$grand_total = 0;
			foreach($get_salary as $row_salary){
				$basic_salary = $row_salary->emp_salary_budget;
				$join_contribution = array(
					'employee_contribution_tbl b' => "a.emp_cont_id = b.emp_cont_id AND emp_config_status = 1 AND b.emp_cont_name='ACCIDENT INSURANCE' AND '$basic_salary' BETWEEN a.emp_config_from AND a.emp_config_to",
					'employee_contribution_type_tbl c' => 'a.emp_cont_type_id = c.emp_cont_type_id AND a.emp_config_year = ' . $year
				);
				$check_contribution = $this->admin->check_join('employee_config_tbl a', $join_contribution, TRUE);
				if($check_contribution['result'] == TRUE){
					$contribution = $check_contribution['info']->emp_config_value;
					$contribution_type = $check_contribution['info']->emp_cont_type_name;
					$total_contribution = 0;
					if($contribution_type == 'FIXED'){
						$total_contribution = $contribution;
						$total_contribution;
						
						$grand_total += $total_contribution;
					}elseif($contribution_type == 'PERCENTAGE'){
						$total_contribution = $basic_salary * $contribution;
						$grand_total += $total_contribution;
					}
				}
			}
			return $grand_total;
		}else{
			$join_salary = array(
				'employee_salary_item_tbl b' => 'a.emp_salary_item_id = b.emp_salary_item_id AND a.emp_salary_trans_dtl_status = 1 AND b.emp_salary_item_status = 1',
				'employee_salary_trans_tbl c' => 'b.emp_salary_trans_id = c.emp_salary_trans_id AND c.emp_salary_trans_status = 1 AND YEAR(a.emp_salary_date)=' . $year_active,
				'employee_tbl d' => 'b.emp_id = d.emp_id',
				'cost_center_tbl e' => 'b.cost_center_id = e.cost_center_id AND e.parent_id =' . $id,
				'employee_config_tbl f' => "a.emp_salary_budget BETWEEN f.emp_config_from AND f.emp_config_to AND f.emp_config_status = 1",
				'employee_contribution_tbl g' => "f.emp_cont_id = g.emp_cont_id AND g.emp_cont_status=1 AND g.emp_cont_name='ACCIDENT INSURANCE'",
				'employee_contribution_type_tbl h' => 'f.emp_cont_type_id = h.emp_cont_type_id'
			);

			$get_salary = $this->admin->get_join('employee_salary_trans_dtl_tbl a', $join_salary, FALSE, 'MONTH(a.emp_salary_date) ASC', FALSE,'(CASE WHEN h.emp_cont_type_name = "FIXED" THEN f.emp_config_value WHEN h.emp_cont_type_name = "PERCENTAGE" THEN a.emp_salary_budget * f.emp_config_value ELSE 0 END) as total_accident');
			return $get_salary;
		}
	}

	public function get_salary_life($id, $month = FALSE, $year){
		$info = $this->_require_login();

		$year_active = $year;
		if($month == FALSE){
			$join_salary = array(
				'employee_salary_item_tbl b' => 'a.emp_salary_item_id = b.emp_salary_item_id AND a.emp_salary_trans_dtl_status = 1 AND b.emp_salary_item_status = 1',
				'employee_salary_trans_tbl c' => 'b.emp_salary_trans_id = c.emp_salary_trans_id AND c.emp_salary_trans_status = 1 AND YEAR(a.emp_salary_date)=' . $year_active,
				'employee_tbl d' => 'b.emp_id = d.emp_id',
				'cost_center_tbl e' => 'b.cost_center_id = e.cost_center_id AND e.parent_id =' . $id
			);
			$get_salary = $this->admin->get_join('employee_salary_trans_dtl_tbl a', $join_salary);
			$grand_total = 0;
			foreach($get_salary as $row_salary){
				$basic_salary = $row_salary->emp_salary_budget;
				$join_contribution = array(
					'employee_contribution_tbl b' => "a.emp_cont_id = b.emp_cont_id AND emp_config_status = 1 AND b.emp_cont_name='LIFE INSURANCE' AND '$basic_salary' BETWEEN a.emp_config_from AND a.emp_config_to",
					'employee_contribution_type_tbl c' => 'a.emp_cont_type_id = c.emp_cont_type_id AND a.emp_config_year = ' . $year
				);
				$check_contribution = $this->admin->check_join('employee_config_tbl a', $join_contribution, TRUE);
				if($check_contribution['result'] == TRUE){
					$contribution = $check_contribution['info']->emp_config_value;
					$contribution_type = $check_contribution['info']->emp_cont_type_name;
					$total_contribution = 0;
					if($contribution_type == 'FIXED'){
						$total_contribution = $contribution;
						$total_contribution;
						
						$grand_total += $total_contribution;
					}elseif($contribution_type == 'PERCENTAGE'){
						$total_contribution = $basic_salary * $contribution;
						$grand_total += $total_contribution;
					}
				}
			}
			return $grand_total;
		}else{
			$join_salary = array(
				'employee_salary_item_tbl b' => 'a.emp_salary_item_id = b.emp_salary_item_id AND a.emp_salary_trans_dtl_status = 1 AND b.emp_salary_item_status = 1',
				'employee_salary_trans_tbl c' => 'b.emp_salary_trans_id = c.emp_salary_trans_id AND c.emp_salary_trans_status = 1 AND YEAR(a.emp_salary_date)=' . $year_active,
				'employee_tbl d' => 'b.emp_id = d.emp_id',
				'cost_center_tbl e' => 'b.cost_center_id = e.cost_center_id AND e.parent_id =' . $id,
				'employee_config_tbl f' => "a.emp_salary_budget BETWEEN f.emp_config_from AND f.emp_config_to AND f.emp_config_status = 1",
				'employee_contribution_tbl g' => "f.emp_cont_id = g.emp_cont_id AND g.emp_cont_status=1 AND g.emp_cont_name='LIFE INSURANCE'",
				'employee_contribution_type_tbl h' => 'f.emp_cont_type_id = h.emp_cont_type_id'
			);

			$get_salary = $this->admin->get_join('employee_salary_trans_dtl_tbl a', $join_salary, FALSE, 'MONTH(a.emp_salary_date) ASC', FALSE,'(CASE WHEN h.emp_cont_type_name = "FIXED" THEN f.emp_config_value WHEN h.emp_cont_type_name = "PERCENTAGE" THEN a.emp_salary_budget * f.emp_config_value ELSE 0 END) as total_life');
			return $get_salary;
		}
	}

	public function get_salary_philhealth($id, $month = FALSE, $year){
		$info = $this->_require_login();

		$year_active = $year;
		if($month == FALSE){
			$join_salary = array(
				'employee_salary_item_tbl b' => 'a.emp_salary_item_id = b.emp_salary_item_id AND a.emp_salary_trans_dtl_status = 1 AND b.emp_salary_item_status = 1',
				'employee_salary_trans_tbl c' => 'b.emp_salary_trans_id = c.emp_salary_trans_id AND c.emp_salary_trans_status = 1 AND YEAR(a.emp_salary_date)=' . $year_active,
				'employee_tbl d' => 'b.emp_id = d.emp_id',
				'cost_center_tbl e' => 'b.cost_center_id = e.cost_center_id AND e.parent_id =' . $id
			);
			$get_salary = $this->admin->get_join('employee_salary_trans_dtl_tbl a', $join_salary);
			$grand_total = 0;
			foreach($get_salary as $row_salary){
				$basic_salary = $row_salary->emp_salary_budget;
				$join_contribution = array(
					'employee_contribution_tbl b' => "a.emp_cont_id = b.emp_cont_id AND emp_config_status = 1 AND b.emp_cont_name='PHILHEALTH' AND '$basic_salary' BETWEEN a.emp_config_from AND a.emp_config_to",
					'employee_contribution_type_tbl c' => 'a.emp_cont_type_id = c.emp_cont_type_id AND a.emp_config_year = ' . $year
				);
				$check_contribution = $this->admin->check_join('employee_config_tbl a', $join_contribution, TRUE);
				if($check_contribution['result'] == TRUE){
					$contribution = $check_contribution['info']->emp_config_value;
					$contribution_type = $check_contribution['info']->emp_cont_type_name;
					$total_contribution = 0;
					if($contribution_type == 'FIXED'){
						$total_contribution = $contribution;
						$total_contribution;

						$grand_total += $total_contribution;
					}elseif($contribution_type == 'PERCENTAGE'){
						$total_contribution = $basic_salary * $contribution;
						$grand_total += $total_contribution;
					}
				}
			}
			return $grand_total;
		}else{
			$join_salary = array(
				'employee_salary_item_tbl b' => 'a.emp_salary_item_id = b.emp_salary_item_id AND a.emp_salary_trans_dtl_status = 1 AND b.emp_salary_item_status = 1',
				'employee_salary_trans_tbl c' => 'b.emp_salary_trans_id = c.emp_salary_trans_id AND c.emp_salary_trans_status = 1 AND YEAR(a.emp_salary_date)=' . $year_active,
				'employee_tbl d' => 'b.emp_id = d.emp_id',
				'cost_center_tbl e' => 'b.cost_center_id = e.cost_center_id AND e.parent_id =' . $id,
				'employee_config_tbl f' => "a.emp_salary_budget BETWEEN f.emp_config_from AND f.emp_config_to AND f.emp_config_status = 1",
				'employee_contribution_tbl g' => "f.emp_cont_id = g.emp_cont_id AND g.emp_cont_status=1 AND g.emp_cont_name='PHILHEALTH'",
				'employee_contribution_type_tbl h' => 'f.emp_cont_type_id = h.emp_cont_type_id'
			);

			$get_salary = $this->admin->get_join('employee_salary_trans_dtl_tbl a', $join_salary, FALSE, 'MONTH(a.emp_salary_date) ASC', FALSE,'(CASE WHEN h.emp_cont_type_name = "FIXED" THEN f.emp_config_value WHEN h.emp_cont_type_name = "PERCENTAGE" THEN a.emp_salary_budget * f.emp_config_value ELSE 0 END) as total_philhealth');
			return $get_salary;
		}
	}

	public function get_salary_sss($id, $month = FALSE, $year){
		$info = $this->_require_login();

		$year_active = $year;
		if($month == FALSE){
			$join_salary = array(
				'employee_salary_item_tbl b' => 'a.emp_salary_item_id = b.emp_salary_item_id AND a.emp_salary_trans_dtl_status = 1 AND b.emp_salary_item_status = 1',
				'employee_salary_trans_tbl c' => 'b.emp_salary_trans_id = c.emp_salary_trans_id AND c.emp_salary_trans_status = 1 AND YEAR(a.emp_salary_date)=' . $year_active,
				'employee_tbl d' => 'b.emp_id = d.emp_id',
				'cost_center_tbl e' => 'b.cost_center_id = e.cost_center_id AND e.parent_id =' . $id
			);

			$get_salary = $this->admin->get_join('employee_salary_trans_dtl_tbl a', $join_salary);
			$grand_total = 0;
			foreach($get_salary as $row_salary){
				$basic_salary = $row_salary->emp_salary_budget;
				$join_sss = array(
					'employee_contribution_tbl b' => "a.emp_cont_id = b.emp_cont_id AND emp_config_status = 1 AND '$basic_salary' BETWEEN a.emp_config_from AND a.emp_config_to",
					'employee_contribution_type_tbl c' => 'a.emp_cont_type_id = c.emp_cont_type_id AND a.emp_config_year = ' . $year
				);
				$check_sss = $this->admin->check_join('employee_config_tbl a', $join_sss, TRUE);
				if($check_sss['result'] == TRUE){
					$contribution = $check_sss['info']->emp_config_value;
					$contribution_type = $check_sss['info']->emp_cont_type_name;
					$total_contribution = 0;
					if($contribution_type == 'FIXED'){
						$total_contribution = $contribution;
						$total_contribution;
						$grand_total += $total_contribution;
					}elseif($contribution_type == 'PERCENTAGE'){
						$total_contribution = $basic_salary * $contribution;
						$grand_total += $total_contribution;
					}
				}
			}
			return $grand_total;
		}else{
			$join_salary = array(
				'employee_salary_item_tbl b' => 'a.emp_salary_item_id = b.emp_salary_item_id AND a.emp_salary_trans_dtl_status = 1 AND b.emp_salary_item_status = 1',
				'employee_salary_trans_tbl c' => 'b.emp_salary_trans_id = c.emp_salary_trans_id AND c.emp_salary_trans_status = 1 AND YEAR(a.emp_salary_date)=' . $year_active,
				'employee_tbl d' => 'b.emp_id = d.emp_id',
				'cost_center_tbl e' => 'b.cost_center_id = e.cost_center_id AND e.parent_id =' . $id,
				'employee_config_tbl f' => "a.emp_salary_budget BETWEEN f.emp_config_from AND f.emp_config_to AND f.emp_config_status = 1",
				'employee_contribution_tbl g' => "f.emp_cont_id = g.emp_cont_id AND g.emp_cont_status=1 AND g.emp_cont_name='SSS'",
				'employee_contribution_type_tbl h' => 'f.emp_cont_type_id = h.emp_cont_type_id'
			);

			$get_salary = $this->admin->get_join('employee_salary_trans_dtl_tbl a', $join_salary, FALSE, 'MONTH(a.emp_salary_date) ASC', FALSE,'(CASE WHEN h.emp_cont_type_name = "FIXED" THEN f.emp_config_value WHEN h.emp_cont_type_name = "PERCENTAGE" THEN a.emp_salary_budget * f.emp_config_value ELSE 0 END) as total_sss');
			return $get_salary;
		}
	}

	public function opex_donut($id, $year){
		$info = $this->_require_login();

		$user_info = $this->get_user_info();
		$company_unit_id = $user_info['company_unit_id'];

		$cost_center = decode($id);
		$check_id = $this->admin->check_data('cost_center_tbl', array('cost_center_id' => $cost_center));

		$designated_tbl = $this->_get_designated_tbl();

		if($check_id == TRUE){
			$line = $this->admin->get_query('SELECT e.gl_group_name as gl_group, SUM(c.opex_amount) as amount, e.gl_color as color FROM '.$designated_tbl->gl_transaction_tbl.' a, '.$designated_tbl->gl_transaction_item_tbl.' b, '.$designated_tbl->gl_transaction_details_tbl.' c, gl_subgroup_tbl d, gl_group_tbl e, cost_center_tbl f, transaction_type_tbl g WHERE a.gl_trans_id=b.gl_trans_id AND  b.gl_trans_item_id=c.gl_trans_item_id AND b.gl_sub_id=d.gl_sub_id AND d.gl_group_id=e.gl_group_id AND b.cost_center_id=f.cost_center_id AND a.trans_type_id=g.trans_type_id AND f.parent_id=' . $cost_center . ' AND a.gl_trans_status=1 AND b.gl_trans_item_status=1 AND c.gl_trans_det_status=1 AND g.trans_type_id=1 AND e.gl_group_show=1 AND YEAR(c.opex_budget_date)=' . $year . ' GROUP BY e.gl_group_id');
			$data['result'] = 1;
			
			/*$contribution_sss = $this->get_salary_sss($cost_center, FALSE, $year);
			$contribution_pagibig = $this->get_salary_pagibig($cost_center, FALSE, $year);
			$contribution_philhealth = $this->get_salary_philhealth($cost_center, FALSE, $year);
			$contribution_life = $this->get_salary_life($cost_center, FALSE, $year);
			$contribution_accident = $this->get_salary_accident($cost_center, FALSE, $year);
			$contribution_hmo = $this->get_salary_hmo($cost_center, FALSE, $year);
			$contribution_salary = $this->get_salary($cost_center, FALSE, $year);
			$contribution_13month = $this->get_salary($cost_center, FALSE, $year) / 12;

			$total_sw = $contribution_sss + $contribution_pagibig + $contribution_philhealth + $contribution_life + $contribution_accident + $contribution_hmo + $contribution_salary + $contribution_13month;
			$append_sw = array('gl_group' => 'SALARIES & WAGES', 'amount' => $total_sw, 'color' => '#e25a53');*/

			$get_depreciation = $this->opex_line_capex(encode($cost_center), TRUE, $year, $company_unit_id);
			$depreciation_amount = 0;

			foreach($get_depreciation['info'] as $row_dep){
				$amount = $row_dep['amount'];
				$depreciation_amount += $amount;
			}

			$append_depreciation = array('gl_group' => 'DEPRECIATION EXPENSES', 'amount' => $depreciation_amount, 'color' => '#f0cee4');


			//array_push($line, $append_sw);
			array_push($line, $append_depreciation);
			$data['info'] = $line;
		}else{
			$data['result'] = 0;
		}

		echo json_encode($data);
		exit();
	}

	public function opex_line($id, $year){
		$info = $this->_require_login();
		$user_info = $this->get_user_info();
		$company_unit_id = $user_info['company_unit_id'];
		$designated_tbl = $this->_get_designated_tbl();

		$cost_center = decode($id);
		$check_id = $this->admin->check_data('cost_center_tbl', array('cost_center_id' => $cost_center));
		if($check_id == TRUE){
			$line = $this->admin->get_query('SELECT  DATE_FORMAT(c.opex_budget_date, "%b %Y") as budget_date, SUM(c.opex_amount) as amount FROM '.$designated_tbl->gl_transaction_tbl.' a, '.$designated_tbl->gl_transaction_item_tbl.' b, '.$designated_tbl->gl_transaction_details_tbl.' c, gl_subgroup_tbl d, gl_group_tbl e, cost_center_tbl f, transaction_type_tbl g WHERE a.gl_trans_id=b.gl_trans_id AND b.gl_trans_item_id=c.gl_trans_item_id AND b.gl_sub_id=d.gl_sub_id AND d.gl_group_id=e.gl_group_id AND b.cost_center_id=f.cost_center_id AND a.trans_type_id=g.trans_type_id AND f.parent_id=' . $cost_center . ' AND a.gl_trans_status=1 AND b.gl_trans_item_status=1 AND g.trans_type_id=1 AND c.gl_trans_det_status=1 AND e.gl_group_show=1 AND YEAR(c.opex_budget_date)=' . $year . ' GROUP BY YEAR(c.opex_budget_date), MONTH(c.opex_budget_date)');
			
			
			/*$get_salary = $this->get_salary($cost_center, TRUE, $year);
			$get_hmo = $this->get_salary_hmo($cost_center, TRUE, $year);
			$get_pagibig = $this->get_salary_pagibig($cost_center, TRUE, $year);
			$get_accident = $this->get_salary_accident($cost_center, TRUE, $year);
			$get_life = $this->get_salary_life($cost_center, TRUE, $year);
			$get_philhealth = $this->get_salary_philhealth($cost_center, TRUE, $year);
			$get_sss = $this->get_salary_sss($cost_center, TRUE, $year);

			if(isset($get_salary[0])){

				for($a = 0; $a < 12; $a++){
					$line[$a]->amount += $get_salary[$a]->total_salary + $get_hmo[$a]->total_hmo + $get_pagibig[$a]->total_pagibig + $get_accident[$a]->total_accident + $get_life[$a]->total_life + $get_philhealth[$a]->total_philhealth + $get_sss[$a]->total_sss + ($get_salary[$a]->total_salary / 12);
				}
			}*/

			$get_depreciation = $this->opex_line_capex(encode($cost_center), TRUE, $year, $company_unit_id);
			if(isset($get_depreciation['info'][1])){

				for($a = 0; $a < 12; $a++){
					$line[$a]->amount += $get_depreciation['info'][$a+1]['amount'];
				}
			}

			$data['result'] = 1;
			$data['info'] = $line;
		}else{
			$data['result'] = 0;
		}

		echo json_encode($data);
		exit();
	}

	public function opex_bar($id, $year){
		$info = $this->_require_login();
		$user_info = $this->get_user_info();
		$company_unit_id = $user_info['company_unit_id'];
		$designated_tbl = $this->_get_designated_tbl();

		$cost_center = decode($id);
		$check_id = $this->admin->check_data('cost_center_tbl', array('cost_center_id' => $cost_center));
		if($check_id == TRUE){
			$bar = $this->admin->get_query('SELECT DATE_FORMAT(c.opex_budget_date, "%b %Y") as budget_date, e.gl_group_name as gl_group, SUM(c.opex_amount) as amount, MONTH(c.opex_budget_date) as month, e.gl_color as color FROM '.$designated_tbl->gl_transaction_tbl.' a, '.$designated_tbl->gl_transaction_item_tbl.' b, '.$designated_tbl->gl_transaction_details_tbl.' c, gl_subgroup_tbl d, gl_group_tbl e, cost_center_tbl f, transaction_type_tbl g WHERE a.gl_trans_id=b.gl_trans_id AND b.gl_trans_item_id=c.gl_trans_item_id AND b.gl_sub_id=d.gl_sub_id AND d.gl_group_id=e.gl_group_id AND b.cost_center_id=f.cost_center_id AND a.trans_type_id=g.trans_type_id AND f.parent_id=' . $cost_center . ' AND a.gl_trans_status=1 AND b.gl_trans_item_status=1 AND c.gl_trans_det_status=1 AND e.gl_group_show=1 AND g.trans_type_id=1 AND YEAR(c.opex_budget_date)=' . $year . ' GROUP BY YEAR(c.opex_budget_date), MONTH(c.opex_budget_date), e.gl_group_id ORDER BY c.opex_budget_date ASC');
			$arr_budget_date = array();
			$arr_group = array();
			$arr_gl = array();
			$count = 0;
			foreach($bar as $row){
				$budget_date = $row->budget_date;
				$month = $row->month;
				$amount = $row->amount;
				$gl_group = $row->gl_group;
				$gl_color = $row->color;
				if(!array_key_exists($month, $arr_budget_date)){
					$arr_budget_date[$month] = $budget_date;
				}

				if(!array_key_exists($gl_group, $arr_gl)){
					$arr_gl[$gl_group]['asset'] = $gl_group;
					$arr_gl[$gl_group]['color'] = $gl_color;
					$arr_gl[$gl_group]['amount'] = array();

					$arr_group[$gl_group] = $gl_group;
					$count++;
				}

				array_push($arr_gl[$gl_group]['amount'], $amount);
			}



			$get_depreciation = $this->opex_line_capex(encode($cost_center), TRUE, $year, $company_unit_id);

			$depre = 'DEPRECIATION EXPENSES';
			$arr_group[$depre] = $depre;

			$arr_gl[$depre]['asset'] = $depre;
			$arr_gl[$depre]['color'] = '#f0cee4';
			$arr_gl[$depre]['amount'] = array();

			for($a = 0; $a < 12; $a++){

				$total_depreciation = $get_depreciation['info'][$a+1]['amount'];
				array_push($arr_gl[$depre]['amount'], $total_depreciation);
			}

			$data['result'] = 1;
			$data['month'] = $arr_budget_date;
			$data['group'] = $arr_group;
			$data['group_amount'] = $arr_gl;
		}else{
			$data['result'] = 0;
		}
		echo json_encode($data);
		exit();
	}

	public function opex_donut_capex($id, $year){
		$info = $this->_require_login();
		$designated_tbl = $this->_get_designated_tbl();

		$cost_center = decode($id);
		$check_id = $this->admin->check_data('cost_center_tbl', array('cost_center_id' => $cost_center));
		if($check_id == TRUE){
			$donut = $this->admin->get_query('SELECT d.ag_name as asset_group, SUM(((c.asg_price / c.asg_lifespan) * b.capex_qty) * (13-MONTH(b.capex_budget_date))) as opex_ny FROM '.$designated_tbl->asset_group_transaction_tbl.' a, '.$designated_tbl->asset_group_transaction_details_tbl.' b, asset_subgroup_tbl c, asset_group_tbl d, cost_center_tbl e WHERE a.ag_trans_id=b.ag_trans_id AND b.asg_id=c.asg_id AND c.ag_id=d.ag_id AND b.cost_center_id=e.cost_center_id AND a.ag_trans_status=1 AND b.ag_trans_det_status=1 AND e.parent_id=' . $cost_center . ' AND YEAR(b.capex_budget_date)=' . $year . ' GROUP BY d.ag_id');
			$data['result'] = 1;
			$data['info'] = $donut;
		}else{
			$data['result'] = 0;
		}		
		echo json_encode($data);
		exit();
	}

	public function opex_line_capex($id, $return_type=FALSE, $year, $company_unit_id){
		$info = $this->_require_login();
		$designated_tbl = $this->_get_designated_tbl();

		$cost_center = decode($id);
		$check_id = $this->admin->check_data('cost_center_tbl', array('cost_center_id' => $cost_center));
		if($check_id == TRUE){
			$line = $this->admin->get_query('SELECT e.ag_name as asset_group, (b.capex_price / b.capex_lifespan) as avg_opex, (13-MONTH(c.capex_budget_date)) as remaining_month, MONTH(c.capex_budget_date) as budget_date, c.capex_qty FROM '.$designated_tbl->asset_group_transaction_tbl.' a, '.$designated_tbl->asset_group_transaction_item_tbl.' b, '.$designated_tbl->asset_group_transaction_details_tbl.' c, asset_subgroup_tbl d, asset_group_tbl e, cost_center_tbl f, transaction_type_tbl g WHERE a.ag_trans_id=b.ag_trans_id AND b.ag_trans_item_id=c.ag_trans_item_id AND b.asg_id=d.asg_id AND d.ag_id=e.ag_id AND b.cost_center_id=f.cost_center_id AND a.trans_type_id=g.trans_type_id AND a.ag_trans_status=1 AND b.ag_trans_item_status=1 AND c.ag_trans_det_status=1 AND g.trans_type_id=1 AND f.parent_id=' . $cost_center . ' AND YEAR(c.capex_budget_date)=' . $year);

			$line_actual = $this->admin->get_query('(SELECT "DEPRECIATION EXPENSES", SUM(x.depreciation_unit_amount) as avg_opex, 1 as remaining_month, MONTH(x.depreciation_unit_date) as budget_date, 1 as capex_qty, "DEPRECIATION ACTUAL" as depre_type FROM '.$designated_tbl->depreciation_unit_tbl.' x, gl_subgroup_tbl y, gl_group_tbl x1 WHERE x.gl_sub_id = y.gl_sub_id AND y.gl_group_id = x1.gl_group_id AND YEAR(x.depreciation_unit_date) = ' . $year . ' AND x.company_unit_id = ' . $company_unit_id . ' AND x.depreciation_unit_status = 1 GROUP BY MONTH(x.depreciation_unit_date))');

			$month = array(
				'1' => 'Jan ' . $year,
				'2' => 'Feb ' . $year,
				'3' => 'Mar ' . $year,
				'4' => 'Apr ' . $year,
				'5' => 'May ' . $year,
				'6' => 'Jun ' . $year,
				'7' => 'Jul ' . $year,
				'8' => 'Aug ' . $year,
				'9' => 'Sep ' . $year,
				'10' => 'Oct ' . $year,
				'11' => 'Nov ' . $year,
				'12' => 'Dec ' . $year,
			);

			$line_data = array(
				'1' => array('month' => 'Jan ' . $year, 'amount' => 0),
				'2' => array('month' => 'Feb ' . $year, 'amount' => 0),
				'3' => array('month' => 'Mar ' . $year, 'amount' => 0),
				'4' => array('month' => 'Apr ' . $year, 'amount' => 0),
				'5' => array('month' => 'May ' . $year, 'amount' => 0),
				'6' => array('month' => 'Jun ' . $year, 'amount' => 0),
				'7' => array('month' => 'Jul ' . $year, 'amount' => 0),
				'8' => array('month' => 'Aug ' . $year, 'amount' => 0),
				'9' => array('month' => 'Sep ' . $year, 'amount' => 0),
				'10' => array('month' => 'Oct ' . $year, 'amount' => 0),
				'11' => array('month' => 'Nov ' . $year, 'amount' => 0),
				'12' => array('month' => 'Dec ' . $year, 'amount' => 0)
			);
			$total = 0;
			foreach($line as $row){
				$budget_date = $row->budget_date;
				$avg_opex = $row->avg_opex;
				$remaining_month = $row->remaining_month;
				$qty = $row->capex_qty;
				$a = 0;
				for($a = $budget_date; $a <= 12; $a++){
					$line_data[$a]['amount'] += $avg_opex * $row->capex_qty;
				}
			}

			foreach($line_actual as $row){
				$budget_date = $row->budget_date;
				$avg_opex = $row->avg_opex;
				$line_data[$budget_date]['amount'] += $avg_opex;
			}

			$data['result'] = 1;
			$data['info'] = $line_data;
			$data['total'] = $total;
		}else{
			$data['result'] = 0;
		}

		if($return_type == FALSE){
			echo json_encode($data);
			exit();
		}else{
			return $data;
		}
	}

	/*public function get_depreciation_subgroup($cost_center, $year, $company_unit_id){
		$info = $this->_require_login();

		$get_depreciation = $this->admin->get_query('
			SELECT 
				ag_name, asset_group, ag_gl_code, SUM(total) as total, budget_date, gl_sub_name, 

				(SELECT SUM(x.cost) FROM comparative_opex_dept_tbl x, gl_subgroup_tbl y, gl_group_tbl z WHERE x.gl_sub_id = y.gl_sub_id AND y.gl_group_id = z.gl_group_id AND depreciation_bc_tbl.ag_gl_code = y.gl_code AND x.company_unit_id = ' . $company_unit_id . ' AND x.comp_opex_dept_status = 1 AND YEAR(x.trans_year) = ' . ($year - 1) . ') as total1,

				(SELECT SUM(x.cost) FROM comparative_opex_dept_tbl x, gl_subgroup_tbl y, gl_group_tbl z WHERE x.gl_sub_id = y.gl_sub_id AND y.gl_group_id = z.gl_group_id AND depreciation_bc_tbl.ag_gl_code = y.gl_code AND x.company_unit_id = ' . $company_unit_id . ' AND x.comp_opex_dept_status = 1 AND YEAR(x.trans_year) = ' . ($year - 2) . ') as total2
			FROM
				(SELECT e.ag_name, d.asg_name as asset_group, e.ag_gl_code, SUM((b.capex_price / b.capex_lifespan) * (13-MONTH(c.capex_budget_date)) * c.capex_qty) as total, MONTH(c.capex_budget_date) as budget_date, h.gl_sub_name FROM asset_group_transaction_tbl a, asset_group_transaction_item_tbl b, asset_group_transaction_details_tbl c, asset_subgroup_tbl d, asset_group_tbl e, cost_center_tbl f, transaction_type_tbl g, gl_subgroup_tbl h WHERE a.ag_trans_id=b.ag_trans_id AND b.ag_trans_item_id=c.ag_trans_item_id AND b.asg_id=d.asg_id AND d.ag_id=e.ag_id AND b.cost_center_id=f.cost_center_id AND a.trans_type_id=g.trans_type_id AND e.ag_gl_code = h.gl_code AND g.trans_type_id = 1 AND a.ag_trans_status = 1 AND b.ag_trans_item_status = 1 AND c.ag_trans_det_status=1 AND f.parent_id=' . $cost_center . ' AND YEAR(c.capex_budget_date)=' . $year . ' GROUP BY h.gl_sub_name

				UNION

				SELECT "", "", y.gl_code, SUM(x.depreciation_unit_amount) as total, x.depreciation_unit_date, y.gl_sub_name FROM depreciation_unit_tbl x, gl_subgroup_tbl y WHERE x.gl_sub_id = y.gl_sub_id  AND YEAR(x.depreciation_unit_date) = ' . $year . ' AND x.company_unit_id = "' . $company_unit_id . '" AND x.depreciation_unit_status = 1 GROUP BY y.gl_sub_name)

				depreciation_bc_tbl

			GROUP BY gl_sub_name
		');

		return $get_depreciation;
	}*/


	public function get_depreciation_subgroup($cost_center, $year, $company_unit_id){
		$info = $this->_require_login();
		$designated_tbl = $this->_get_designated_tbl($company_unit_id);

		$sql = '
			SELECT 
				ag_name, asset_group, ag_gl_code, SUM(total) as total, SUM(total1) as total1, SUM(total2) as total2, budget_date, gl_sub_name

				
			FROM
			(

				(
					SELECT e.ag_name, d.asg_name as asset_group, e.ag_gl_code, SUM((b.capex_price / b.capex_lifespan) * (13-MONTH(c.capex_budget_date)) * c.capex_qty) as total, 0 as total1, 0 as total2, MONTH(c.capex_budget_date) as budget_date, h.gl_sub_name FROM '.$designated_tbl->asset_group_transaction_tbl.' a, '.$designated_tbl->asset_group_transaction_item_tbl.' b, '.$designated_tbl->asset_group_transaction_details_tbl.' c, asset_subgroup_tbl d, asset_group_tbl e, cost_center_tbl f, transaction_type_tbl g, gl_subgroup_tbl h WHERE a.ag_trans_id=b.ag_trans_id AND b.ag_trans_item_id=c.ag_trans_item_id AND b.asg_id=d.asg_id AND d.ag_id=e.ag_id AND b.cost_center_id=f.cost_center_id AND a.trans_type_id=g.trans_type_id AND e.ag_gl_code = h.gl_code AND g.trans_type_id = 1 AND a.ag_trans_status = 1 AND b.ag_trans_item_status = 1 AND c.ag_trans_det_status=1 AND f.parent_id=' . $cost_center . ' AND YEAR(c.capex_budget_date)=' . $year . ' GROUP BY h.gl_sub_id
				)

				UNION

				(
					SELECT "", "", y.gl_code, SUM(x.depreciation_unit_amount) as total, 0 as total1, 0 as total2, x.depreciation_unit_date, y.gl_sub_name FROM '.$designated_tbl->depreciation_unit_tbl.' x, gl_subgroup_tbl y WHERE x.gl_sub_id = y.gl_sub_id  AND YEAR(x.depreciation_unit_date) = ' . $year . ' AND x.company_unit_id = "' . $company_unit_id . '" AND x.depreciation_unit_status = 1 GROUP BY y.gl_sub_id
				)

				UNION


				(
					SELECT "", "", y.gl_code, 0 as total, SUM(x.cost) as total1, 0 as total2, "",  y.gl_sub_name FROM comparative_opex_dept_tbl x, gl_subgroup_tbl y, gl_group_tbl z WHERE x.gl_sub_id = y.gl_sub_id AND y.gl_group_id = z.gl_group_id AND x.company_unit_id = ' . $company_unit_id . ' AND x.comp_opex_dept_status = 1 AND YEAR(x.trans_year) = ' . ($year - 1) . ' AND z.gl_group_name = "DEPRECIATION EXPENSES" GROUP BY y.gl_sub_id
				)

				UNION

				(
					SELECT "", "", y.gl_code, 0 as total, 0 as total1, SUM(x.cost) as total2, "",  y.gl_sub_name FROM comparative_opex_dept_tbl x, gl_subgroup_tbl y, gl_group_tbl z WHERE x.gl_sub_id = y.gl_sub_id AND y.gl_group_id = z.gl_group_id AND x.company_unit_id = ' . $company_unit_id . ' AND x.comp_opex_dept_status = 1 AND YEAR(x.trans_year) = ' . ($year - 2) . ' AND z.gl_group_name = "DEPRECIATION EXPENSES" GROUP BY y.gl_sub_id
				)

			)depreciation_bc_tbl


			GROUP BY gl_sub_name
		';
		// return $sql;
		$get_depreciation = $this->admin->get_query($sql);

		return $get_depreciation;
	}

	public function get_depreciation_cc($cost_center, $year, $company_unit_id){
		$info = $this->_require_login();
		$designated_tbl = $this->_get_designated_tbl($company_unit_id);

		$get_depreciation = $this->admin->get_query('
			SELECT 
				ag_name, asset_group, ag_gl_code, SUM(total) as total, budget_date, gl_sub_name, cost_center_id, gl_sub_id, 

			FROM
				(SELECT e.ag_name, d.asg_name as asset_group, e.ag_gl_code, SUM((b.capex_price / b.capex_lifespan) * (13-MONTH(c.capex_budget_date)) * c.capex_qty) as total, MONTH(c.capex_budget_date) as budget_date, h.gl_sub_name, f.cost_center_id, h.gl_sub_id FROM '.$designated_tbl->asset_group_transaction_tbl.' a, '.$designated_tbl->asset_group_transaction_item_tbl.' b, '.$designated_tbl->asset_group_transaction_details_tbl.' c, asset_subgroup_tbl d, asset_group_tbl e, cost_center_tbl f, transaction_type_tbl g, gl_subgroup_tbl h WHERE a.ag_trans_id=b.ag_trans_id AND b.ag_trans_item_id=c.ag_trans_item_id AND b.asg_id=d.asg_id AND d.ag_id=e.ag_id AND b.cost_center_id=f.cost_center_id AND a.trans_type_id=g.trans_type_id AND e.ag_gl_code = h.gl_code AND g.trans_type_id = 1 AND a.ag_trans_status = 1 AND b.ag_trans_item_status = 1 AND c.ag_trans_det_status=1 AND f.parent_id=' . $cost_center . ' AND YEAR(c.capex_budget_date)=' . $year . ' GROUP BY h.gl_sub_name, f.cost_center_id, 

				UNION

				SELECT "", "", y.gl_code, SUM(x.depreciation_unit_amount) as total, x.depreciation_unit_date, y.gl_sub_name, x.cost_center_id, y.gl_sub_id FROM '.$designated_tbl->depreciation_unit_tbl.' x, gl_subgroup_tbl y WHERE x.gl_sub_id = y.gl_sub_id  AND YEAR(x.depreciation_unit_date) = ' . $year . ' AND x.company_unit_id = "' . $company_unit_id . '" AND x.depreciation_unit_status = 1 GROUP BY y.gl_sub_name, x.cost_center_id)

				depreciation_bc_tbl

			GROUP BY gl_sub_name, cost_center_id
		');

		return $get_depreciation;
	}

	public function transac_opex($year = null){
		$info = $this->_require_login();
		$data['title'] = 'Add Opex';
		$user_info = $this->get_user_info();
		$id = $user_info['cost_center_code'];
		$unit = $user_info['company_unit_id'];
		$data['id'] = encode($id);

		$designated_tbl = $this->_get_designated_tbl();

		$module = 'OPEX';
		if($year == null){
			$year = $this->_active_year();
		}

		$data['year'] = $year;

		$budget_status = $this->check_module($module, $year);
		if($budget_status == 1){
			$check_id = $this->admin->check_data('cost_center_tbl', array('cost_center_code' => $id, 'cost_center_status' => 1), TRUE);
			if($check_id['result'] == TRUE){
				$cost_center_id = $check_id['info']->cost_center_id;
				$data['cost_center_name'] = $check_id['info']->cost_center_desc;
				$data['gl_group'] = $this->admin->get_query('SELECT * FROM gl_group_tbl as a WHERE a.gl_group_id NOT IN (SELECT b.gl_group_id FROM '.$designated_tbl->gl_transaction_tbl.' b, cost_center_tbl c WHERE b.cost_center_id=c.cost_center_id AND b.gl_trans_status=1 AND c.cost_center_id=' . $cost_center_id . ' AND b.gl_year = ' . $year . ') AND a.gl_group_name !="DEPRECIATION EXPENSES" OR a.gl_group_name = "STORE EXPENSES"');

				$sw_join = array(
					'cost_center_tbl b' => 'a.cost_center_id = b.cost_center_id AND a.emp_salary_trans_status = 1 AND b.cost_center_id = ' . $cost_center_id . ' AND a.emp_salary_trans_year = ' . $year
				);

				$get_sw_group = $this->admin->check_join('employee_salary_trans_tbl a', $sw_join);
				$data['sw'] = $get_sw_group;
				$data['brand'] = $this->admin->get_data('brand_tbl', 'brand_status = 1');
				$data['content'] = $this->load->view('unit/unit_transac_opex', $data , TRUE);
				$this->load->view('unit/templates', $data);
			}
		}else{
			redirect('unit/opex-info/');
		}
	}

	public function get_stores(){
		$info = $this->_require_login();
		$year = $this->_active_year();
		if($_SERVER['REQUEST_METHOD'] == 'POST'){
			$designated_tbl = $this->_get_designated_tbl();
			$brand_id = decode($this->input->post('id'));
			$cost_center = decode($this->input->post('cost_center'));
			$year = clean_data($this->input->post('year'));
			$check_brand = $this->admin->check_data('brand_tbl', array('brand_id' => $brand_id, 'brand_status' => 1));
			if($check_brand == TRUE){
				$check_cc = $this->admin->check_data('cost_center_tbl', array('cost_center_code' => $cost_center, 'cost_center_status' => 1), TRUE);
				if($check_cc['result'] == TRUE){
					$cost_center_id = $check_cc['info']->cost_center_id;
					$join_outlet = array(
						'outlet_tbl b' => 'a.outlet_id = b.outlet_id AND a.outlet_brand_status = 1 AND a.brand_id = ' . $brand_id,
						'cost_center_tbl c' => 'b.ifs_code = c.cost_center_code AND c.cost_center_status = 1 AND c.cost_center_type_id = 8 AND c.parent_id = ' . $cost_center_id
					);
					$where = 'c.cost_center_id NOT IN (SELECT y.cost_center_id FROM '.$designated_tbl->gl_transaction_tbl.' w, '.$designated_tbl->gl_transaction_item_tbl.' x, cost_center_tbl y, outlet_tbl z, outlet_brand_tbl m  WHERE w.gl_trans_id = x.gl_trans_id AND x.cost_center_id = y.cost_center_id AND y.cost_center_code = z.ifs_code AND z.outlet_id = m.outlet_id AND m.outlet_brand_status = 1 AND m.brand_id=' . $brand_id . ' AND w.gl_year = ' . $year . ' AND w.gl_trans_status = 1 AND x.gl_trans_item_status = 1)';
					$get_outlet = $this->admin->get_join('outlet_brand_tbl a', $join_outlet, FALSE, FALSE, FALSE, FALSE, $where);
					$outlet = '';
					foreach($get_outlet as $row):
						$outlet .= '<option value="' . encode($row->cost_center_id) . '">' . $row->cost_center_desc . '</option>';
					endforeach;
					$data['result'] = 1;
					$data['info'] = $outlet;
				}else{

				}
			}else{
				$data['result'] = 0;
			}
			echo json_encode($data);
		}
	}

	public function get_gl(){
		$info = $this->_require_login();
		if($_SERVER['REQUEST_METHOD'] == 'POST'){
			$gl_group_id =clean_data(decode($this->input->post('id')));
			$cost_center =clean_data(decode($this->input->post('cost_center')));
			$check_gl = $this->admin->check_data('gl_group_tbl', array('gl_group_id' => $gl_group_id), TRUE);
			if($check_gl['result'] == TRUE){
				$data['name'] = $check_gl['info']->gl_group_name;
				$gl_name = $check_gl['info']->gl_group_name;
				$check_cost_center = $this->admin->check_data('cost_center_tbl', array('cost_center_code' => $cost_center, 'cost_center_status' => 1), TRUE);

				if($check_cost_center['result'] == TRUE){
					$cost_center_id = $check_cost_center['info']->cost_center_id;

					/*if($gl_name == 'SALARIES & WAGES'){
						$gl = $this->_salary_data($cost_center_id);
					}else{
						$gl = $this->_gl_data($cost_center_id, $gl_group_id, $gl_name);
					}*/

					$gl = $this->_gl_data($cost_center_id, $gl_group_id, $gl_name);

					$data['gl'] = $gl;
					$data['result'] = 1;
				}else{
					$data[''] = 0;
				}
				
			}else{
				$data['result'] = 0;
			}
		}else{
			$data['result'] = 0;
		}

		echo json_encode($data);
	}

	public function get_gl_sub(){
		$info = $this->_require_login();
		if($_SERVER['REQUEST_METHOD'] == 'POST'){
			$gl_sub_id =clean_data(decode($this->input->post('id')));
			$cost_center =clean_data(decode($this->input->post('cost_center')));
			$join_gl = array('gl_group_tbl b' => 'a.gl_group_id = b.gl_group_id AND gl_sub_status = 1 AND a.gl_sub_id = ' . $gl_sub_id);
			$check_gl = $this->admin->check_join('gl_subgroup_tbl a', $join_gl, TRUE);
			if($check_gl['result'] == TRUE){
				$data['name'] = $check_gl['info']->gl_group_name;
				$gl_name = $check_gl['info']->gl_group_name;
				$check_cost_center = $this->admin->check_data('cost_center_tbl', array('cost_center_code' => $cost_center, 'cost_center_status' => 1), TRUE);

				if($check_cost_center['result'] == TRUE){
					$cost_center_id = $check_cost_center['info']->cost_center_id;
					/*if($gl_name == 'SALARIES & WAGES'){
						$gl = $this->_salary_data($cost_center_id);
					}else{*/
					
					$gl = $this->_gl_data($cost_center_id, null, $gl_name, $gl_sub_id);
					
					$data['gl'] = $gl;
					$data['result'] = 1;
				}else{
					$data[''] = 0;
				}
				
			}else{
				$data['result'] = 0;
			}
		}else{
			$data['result'] = 0;
		}

		echo json_encode($data);
	}

	public function _gl_data($cost_center_id, $gl_group_id, $gl_name, $gl_sub_id=null){
		if($gl_name == 'STORE EXPENSES'){
			$get_cost_center = $this->admin->get_data('cost_center_tbl', array('parent_id' => $cost_center_id, 'cost_center_type_id' => 8 , 'cost_center_status' => 1));
		}else{
			$get_cost_center = $this->admin->get_data('cost_center_tbl', array('parent_id' => $cost_center_id, 'cost_center_type_id !=' => 8, 'cost_center_status' => 1));
		}

		$cost_center_data = '<select name="cost_center[]" class="form-control input-sm opex-cost-center">';
		$cost_center_data .= '<option value="">Select...</option>';
		foreach($get_cost_center as $row){
			$cost_center_data .= '<option value="' . encode($row->cost_center_id) . '">' . $row->cost_center_code . ' - ' . $row->cost_center_desc . '</option>';
		}

		$cost_center_data .= '</select>';

		if($gl_sub_id == null){
			$get_gl = $this->admin->get_data('gl_subgroup_tbl', array('gl_group_id' => $gl_group_id, 'gl_sub_status' => 1));
		}else{
			$get_gl = $this->admin->get_data('gl_subgroup_tbl', array('gl_sub_id' => $gl_sub_id, 'gl_sub_status' => 1));
		}
		$gl = '';
		$row_count = 1;
		foreach($get_gl as $row){
			$gl .= '<tr class="row-' . $row_count . '"><input type="hidden" name="id[]" value="' . encode($row->gl_sub_id) . '">';
			$gl .= '<td width="60px;" class="text-center"><a href="#" class="remove-gl-sub remove"><i class="fa fa-remove"></i></a>';

			if($gl_name != 'STORE EXPENSES'){
				$gl .= '&nbsp;&nbsp;&nbsp;<a href="" class="add-gl-sub add" data-id="' . encode($row->gl_sub_id) . '"><i class="fa fa-plus"></i></a>';
			}

			$gl .= '&nbsp;&nbsp;<a href="#" class="slider-add-item slider-opex" data-count="' . $row_count . '"><span class="fa fa-sliders"></span></a></td>';


			$gl .= '<td width="7%">' . $row->gl_sub_name .'</td>';
			$gl .= '<td width="13%">' . $cost_center_data . '</td>';
			$gl .= '<td class="text-right" width="5%"><label class="opex-total-qty">0</label></td>';
			$gl .= '<td class="text-center" width=""><input type="text" class="opex-qty jan-qty" name="opex[jan][]" class="form-control input-sm"></td>';
			$gl .= '<td class="text-center" width=""><input type="text" class="opex-qty feb-qty" name="opex[feb][]" class="form-control input-sm"></td>';
			$gl .= '<td class="text-center" width=""><input type="text" class="opex-qty mar-qty" name="opex[mar][]" class="form-control input-sm"></td>';
			$gl .= '<td class="text-center" width=""><input type="text" class="opex-qty apr-qty" name="opex[apr][]" class="form-control input-sm"></td>';
			$gl .= '<td class="text-center" width=""><input type="text" class="opex-qty may-qty" name="opex[may][]" class="form-control input-sm"></td>';
			$gl .= '<td class="text-center" width=""><input type="text" class="opex-qty jun-qty" name="opex[jun][]" class="form-control input-sm"></td>';
			$gl .= '<td class="text-center" width=""><input type="text" class="opex-qty jul-qty" name="opex[jul][]" class="form-control input-sm"></td>';
			$gl .= '<td class="text-center" width=""><input type="text" class="opex-qty aug-qty" name="opex[aug][]" class="form-control input-sm"></td>';
			$gl .= '<td class="text-center" width=""><input type="text" class="opex-qty sep-qty" name="opex[sep][]" class="form-control input-sm"></td>';
			$gl .= '<td class="text-center" width=""><input type="text" class="opex-qty oct-qty" name="opex[oct][]" class="form-control input-sm"></td>';
			$gl .= '<td class="text-center" width=""><input type="text" class="opex-qty nov-qty" name="opex[nov][]" class="form-control input-sm"></td>';
			$gl .= '<td class="text-center" width=""><input type="text" class="opex-qty dec-qty" name="opex[dec][]" class="form-control input-sm"></td>';
			$gl .= '</tr>';

			$row_count++;
		}

		return $gl;
	}

	public function _salary_data($cost_center, $exist = FALSE, $year = FALSE){

		if($exist == FALSE){
			$join_emp = array(
				'employee_year_tbl b' => 'a.emp_id = b.emp_id AND b.emp_year_status = 1',
				'cost_center_tbl c' =>  'b.cost_center_id = c.cost_center_id AND c.parent_id=' . $cost_center
			);
			$get_emp = $this->admin->get_join('employee_tbl a', $join_emp);
		}else{
			$join_emp = array(
				'employee_year_tbl b' => 'a.emp_id = b.emp_id',
				'cost_center_tbl c' =>  'b.cost_center_id = c.cost_center_id AND c.parent_id=' . $cost_center
			);

			$where = 'b.emp_year_status = 1 AND a.emp_id NOT IN (SELECT x.emp_id FROM employee_salary_item_tbl x, employee_salary_trans_tbl y WHERE x.emp_salary_trans_id = y.emp_salary_trans_id AND x.emp_salary_item_status = 1 AND y.emp_salary_trans_year = ' . $year . ')';
			$get_emp = $this->admin->get_join('employee_tbl a', $join_emp, FALSE, FALSE, FALSE, FALSE, $where);
		}
		
		$gl = '';
		foreach($get_emp as $row_emp){
			$total_salary = number_format($row_emp->emp_year_salary * 12, 2);
			$gl .= '<tr><input type="hidden" name="id[]" value="' . encode($row_emp->emp_id) . '">';
			$gl .= '<td class="text-center"><a href="#" class="remove-asg"><i class="fa fa-remove"></i></a></td>';
			$gl .= '<td width="7%">' . $row_emp->emp_lname . ', ' . $row_emp->emp_fname . '</td>';
			$gl .= '<td width="7%">' . $row_emp->cost_center_code . '</td>';
			$gl .= '<td class="text-right" width="5%"><label class="total-salary">' . $total_salary . '</label></td>';
			$gl .= '<td class="text-center" width=""><input type="text" class="basic-salary jan-salary" name="salary[jan][]" class="form-control input-sm" value="' . $row_emp->emp_year_salary . '"></td>';
			$gl .= '<td class="text-center" width=""><input type="text" class="opex-qty feb-salary" name="salary[feb][]" class="form-control input-sm" value="' . $row_emp->emp_year_salary . '"></td>';
			$gl .= '<td class="text-center" width=""><input type="text" class="opex-qty mar-salary" name="salary[mar][]" class="form-control input-sm" value="' . $row_emp->emp_year_salary . '"></td>';
			$gl .= '<td class="text-center" width=""><input type="text" class="opex-qty apr-salary" name="salary[apr][]" class="form-control input-sm" value="' . $row_emp->emp_year_salary . '"></td>';
			$gl .= '<td class="text-center" width=""><input type="text" class="opex-qty may-salary" name="salary[may][]" class="form-control input-sm" value="' . $row_emp->emp_year_salary . '"></td>';
			$gl .= '<td class="text-center" width=""><input type="text" class="opex-qty jun-salary" name="salary[jun][]" class="form-control input-sm" value="' . $row_emp->emp_year_salary . '"></td>';
			$gl .= '<td class="text-center" width=""><input type="text" class="opex-qty jul-salary" name="salary[jul][]" class="form-control input-sm" value="' . $row_emp->emp_year_salary . '"></td>';
			$gl .= '<td class="text-center" width=""><input type="text" class="opex-qty aug-salary" name="salary[aug][]" class="form-control input-sm" value="' . $row_emp->emp_year_salary . '"></td>';
			$gl .= '<td class="text-center" width=""><input type="text" class="opex-qty sep-salary" name="salary[sep][]" class="form-control input-sm" value="' . $row_emp->emp_year_salary . '"></td>';
			$gl .= '<td class="text-center" width=""><input type="text" class="opex-qty oct-salary" name="salary[oct][]" class="form-control input-sm" value="' . $row_emp->emp_year_salary . '"></td>';
			$gl .= '<td class="text-center" width=""><input type="text" class="opex-qty nov-salary" name="salary[nov][]" class="form-control input-sm" value="' . $row_emp->emp_year_salary . '"></td>';
			$gl .= '<td class="text-center" width=""><input type="text" class="opex-qty dec-salary" name="salary[dec][]" class="form-control input-sm" value="' . $row_emp->emp_year_salary . '"></td>';
			$gl .= '</tr>';
		}

		return $gl;
	}

	public function add_opex(){
		$info = $this->_require_login();
		$user_id = decode($info['user_id']);
		if($_SERVER['REQUEST_METHOD'] == 'POST'){
			$gl_group = clean_data(decode($this->input->post('gl_group')));
			$cost_center = clean_data(decode($this->input->post('bc_cost_center')));
			$id = clean_data($this->input->post('id'));
			$year = clean_data($this->input->post('year'));

			$designated_tbl = $this->_get_designated_tbl();

			$module = 'OPEX';
			if(!empty($gl_group) && !empty($cost_center) && !empty($year)){

				$budget_status = $this->check_module($module, $year);
				if($budget_status == 1){

					$check_gl = $this->admin->check_data('gl_group_tbl', array('gl_group_id' => $gl_group, 'gl_group_status' => 1), TRUE);
					if($check_gl['result'] == TRUE){
						$gl_name = $check_gl['info']->gl_group_name;
						$check_cc = $this->admin->check_data('cost_center_tbl', array('cost_center_code' => $cost_center, 'cost_center_status' => 1), TRUE);
						if($check_cc['result'] == TRUE){
							$cost_center_id = $check_cc['info']->cost_center_id;
							$this->db->trans_start();
							/*if($gl_name == 'SALARIES & WAGES'){
								$salary = clean_data($this->input->post('salary'));

								$set_salary = array(
									'cost_center_id' => $cost_center_id,
									'user_id' => $user_id,
									'trans_type_id' => 1,
									'emp_salary_trans_year' => $year,
									'emp_salary_trans_added' => date_now(),
									'emp_salary_trans_status' => 1
								);
								
								$insert_emp = $this->admin->insert_data('employee_salary_trans_tbl', $set_salary, TRUE);
								if($insert_emp['result'] == TRUE){
									$emp_salary_trans_id = $insert_emp['id'];
									$count = 0;
									foreach($id as $emp){
										$emp_id = decode($emp);
										$check_emp = $this->admin->check_data('employee_year_tbl', array('emp_id' => $emp_id, 'emp_year_status' => 1, 'emp_year' => $year), TRUE);

										if($check_emp['result'] == TRUE){

											$emp_cost_center = $check_emp['info']->cost_center_id;
											$rank_id = $check_emp['info']->rank_id;

											$set_item = array(
												'emp_salary_trans_id' => $emp_salary_trans_id,
												'emp_id' => $emp_id,
												'cost_center_id' => $emp_cost_center,
												'rank_id' => $rank_id,
												'emp_salary_item_added' => date_now(),
												'emp_salary_item_status' => 1
											);

											$insert_item = $this->admin->insert_data('employee_salary_item_tbl', $set_item, TRUE);
											$emp_salary_item_id = $insert_item['id'];


											$date = $year . '-01-01';
											$amount_jan = check_num($salary['jan'][$count]);
											$set_emp_jan = array(
												'emp_salary_item_id' => $emp_salary_item_id,
												'emp_salary_budget' => $amount_jan,
												'emp_salary_date' => $date,
												'emp_salary_trans_added' => date_now(),
												'emp_salary_trans_dtl_status' => 1
											);
											$insert_emp_dtl = $this->admin->insert_data('employee_salary_trans_dtl_tbl', $set_emp_jan);

											$date = $year . '-02-01';
											$amount_feb = check_num($salary['feb'][$count]);
											$set_emp_feb = array(
												'emp_salary_item_id' => $emp_salary_item_id,
												'emp_salary_budget' => $amount_feb,
												'emp_salary_date' => $date,
												'emp_salary_trans_added' => date_now(),
												'emp_salary_trans_dtl_status' => 1
											);
											$insert_emp_dtl = $this->admin->insert_data('employee_salary_trans_dtl_tbl', $set_emp_feb);

											$date = $year . '-03-01';
											$amount_mar = check_num($salary['mar'][$count]);
											$set_emp_mar = array(
												'emp_salary_item_id' => $emp_salary_item_id,
												'emp_salary_budget' => $amount_mar,
												'emp_salary_date' => $date,
												'emp_salary_trans_added' => date_now(),
												'emp_salary_trans_dtl_status' => 1
											);
											$insert_emp_dtl = $this->admin->insert_data('employee_salary_trans_dtl_tbl', $set_emp_mar);

											$date = $year . '-04-01';
											$amount_apr = check_num($salary['apr'][$count]);
											$set_emp_apr = array(
												'emp_salary_item_id' => $emp_salary_item_id,
												'emp_salary_budget' => $amount_apr,
												'emp_salary_date' => $date,
												'emp_salary_trans_added' => date_now(),
												'emp_salary_trans_dtl_status' => 1
											);
											$insert_emp_dtl = $this->admin->insert_data('employee_salary_trans_dtl_tbl', $set_emp_apr);

											$date = $year . '-05-01';
											$amount_may = check_num($salary['may'][$count]);
											$set_emp_may = array(
												'emp_salary_item_id' => $emp_salary_item_id,
												'emp_salary_budget' => $amount_may,
												'emp_salary_date' => $date,
												'emp_salary_trans_added' => date_now(),
												'emp_salary_trans_dtl_status' => 1
											);
											$insert_emp_dtl = $this->admin->insert_data('employee_salary_trans_dtl_tbl', $set_emp_may);

											$date = $year . '-06-01';
											$amount_jun = check_num($salary['jun'][$count]);
											$set_emp_jun = array(
												'emp_salary_item_id' => $emp_salary_item_id,
												'emp_salary_budget' => $amount_jun,
												'emp_salary_date' => $date,
												'emp_salary_trans_added' => date_now(),
												'emp_salary_trans_dtl_status' => 1
											);
											$insert_emp_dtl = $this->admin->insert_data('employee_salary_trans_dtl_tbl', $set_emp_jun);

											$date = $year . '-07-01';
											$amount_jul = check_num($salary['jul'][$count]);
											$set_emp_jul = array(
												'emp_salary_item_id' => $emp_salary_item_id,
												'emp_salary_budget' => $amount_jul,
												'emp_salary_date' => $date,
												'emp_salary_trans_added' => date_now(),
												'emp_salary_trans_dtl_status' => 1
											);
											$insert_emp_dtl = $this->admin->insert_data('employee_salary_trans_dtl_tbl', $set_emp_jul);

											$date = $year . '-08-01';
											$amount_aug = check_num($salary['aug'][$count]);
											$set_emp_aug = array(
												'emp_salary_item_id' => $emp_salary_item_id,
												'emp_salary_budget' => $amount_aug,
												'emp_salary_date' => $date,
												'emp_salary_trans_added' => date_now(),
												'emp_salary_trans_dtl_status' => 1
											);
											$insert_emp_dtl = $this->admin->insert_data('employee_salary_trans_dtl_tbl', $set_emp_aug);

											$date = $year . '-09-01';
											$amount_sep = check_num($salary['sep'][$count]);
											$set_emp_sep = array(
												'emp_salary_item_id' => $emp_salary_item_id,
												'emp_salary_budget' => $amount_sep,
												'emp_salary_date' => $date,
												'emp_salary_trans_added' => date_now(),
												'emp_salary_trans_dtl_status' => 1
											);
											$insert_emp_dtl = $this->admin->insert_data('employee_salary_trans_dtl_tbl', $set_emp_sep);

											$date = $year . '-10-01';
											$amount_oct = check_num($salary['oct'][$count]);
											$set_emp_oct = array(
												'emp_salary_item_id' => $emp_salary_item_id,
												'emp_salary_budget' => $amount_oct,
												'emp_salary_date' => $date,
												'emp_salary_trans_added' => date_now(),
												'emp_salary_trans_dtl_status' => 1
											);
											$insert_emp_dtl = $this->admin->insert_data('employee_salary_trans_dtl_tbl', $set_emp_oct);

											$date = $year . '-11-01';
											$amount_nov = check_num($salary['nov'][$count]);
											$set_emp_nov = array(
												'emp_salary_item_id' => $emp_salary_item_id,
												'emp_salary_budget' => $amount_nov,
												'emp_salary_date' => $date,
												'emp_salary_trans_added' => date_now(),
												'emp_salary_trans_dtl_status' => 1
											);
											$insert_emp_dtl = $this->admin->insert_data('employee_salary_trans_dtl_tbl', $set_emp_nov);

											$date = $year . '-12-01';
											$amount_dec = check_num($salary['dec'][$count]);
											$set_emp_dec = array(
												'emp_salary_item_id' => $emp_salary_item_id,
												'emp_salary_budget' => $amount_dec,
												'emp_salary_date' => $date,
												'emp_salary_trans_added' => date_now(),
												'emp_salary_trans_dtl_status' => 1
											);
											$insert_emp_dtl = $this->admin->insert_data('employee_salary_trans_dtl_tbl', $set_emp_dec);
										}else{
											$this->db->trans_rollback();
											$msg = '<div class="alert alert-danger">Error while inserting transaction, please try again!</div>';
											$this->session->set_flashdata('message', $msg);
											redirect('unit/transac-opex/');
										}

										$count++;
									}
								}else{
									$this->db->trans_rollback();
									$msg = '<div class="alert alert-danger">Error while inserting transaction, please try again!</div>';
									$this->session->set_flashdata('message', $msg);
									redirect('unit/transac-opex/');
								}

								if($this->db->trans_status() === FALSE){
									$this->db->trans_rollback();
									$msg = '<div class="alert alert-danger">Error please try again!</div>';
								}else{
									$this->db->trans_commit();
									$msg = '<div class="alert alert-success">OPEX successfully added.</strong></div>';
								}
							}else{*/


							$set = array(
								'gl_group_id' => $gl_group,
								'trans_type_id' => 1,
								'cost_center_id' => $cost_center_id,
								'user_id' => $user_id,
								'gl_year' => $year,
								'gl_trans_added' => date_now(),
								'gl_trans_status' => 1
							);
							
							$insert_opex_trans = $this->admin->insert_data($designated_tbl->gl_transaction_tbl, $set, TRUE);
							$gl_trans_id = $insert_opex_trans['id'];
							if($insert_opex_trans['result'] == TRUE){
								
								$gl_cost_center = clean_data($this->input->post('cost_center'));
								$opex = clean_data($this->input->post('opex'));
								$count = 0;
								foreach($id as $row){
									$gl_sub_id = decode($row);
									$check_gl_sub_id = $this->admin->check_data('gl_subgroup_tbl', array('gl_sub_id' => $gl_sub_id, 'gl_sub_status' => 1), TRUE);
									
									if($check_gl_sub_id['result'] == TRUE){
										$gl_cost_center_id = decode($gl_cost_center[$count]);

										$amount_jan = check_num($opex['jan'][$count]);
										$amount_feb = check_num($opex['feb'][$count]);
										$amount_mar = check_num($opex['mar'][$count]);
										$amount_apr = check_num($opex['apr'][$count]);
										$amount_may = check_num($opex['may'][$count]);
										$amount_jun = check_num($opex['jun'][$count]);
										$amount_jul = check_num($opex['jul'][$count]);
										$amount_aug = check_num($opex['aug'][$count]);
										$amount_sep = check_num($opex['sep'][$count]);
										$amount_oct = check_num($opex['oct'][$count]);
										$amount_nov = check_num($opex['nov'][$count]);
										$amount_dec = check_num($opex['dec'][$count]);

										$total_amount = $amount_jan + $amount_feb + $amount_mar + $amount_apr + $amount_may + $amount_jun + $amount_jul + $amount_aug + $amount_sep + $amount_oct + $amount_nov + $amount_dec;

										if($total_amount > 0){

											$set_item = array(
												'gl_trans_id' => $gl_trans_id,
												'gl_sub_id' => $gl_sub_id,
												'cost_center_id' => $gl_cost_center_id,
												'gl_transaction_type_id' => 2,
												'user_id' => $user_id,
												'gl_trans_item_added' => date_now(),
												'gl_trans_item_status' =>	1
											);
											$insert_item = $this->admin->insert_data($designated_tbl->gl_transaction_item_tbl, $set_item, TRUE);

											if($insert_item['result'] == TRUE){
												$gl_trans_item_id = $insert_item['id'];
												
												$date = $year . '-' . '01-01';
												$set_gl_trans = array(
													'gl_trans_item_id' => $gl_trans_item_id,
													'opex_amount' => $amount_jan,
													'opex_budget_date' => $date,
													'gl_trans_det_added' => date_now(),
													'gl_trans_det_status' => 1
												);
												$this->admin->insert_data($designated_tbl->gl_transaction_details_tbl, $set_gl_trans);

												
												$date = $year . '-' . '02-01';
												$set_gl_det_trans = array(
													'gl_trans_item_id' => $gl_trans_item_id,
													'opex_amount' => $amount_feb,
													'opex_budget_date' => $date,
													'gl_trans_det_added' => date_now(),
													'gl_trans_det_status' => 1
												);
												$this->admin->insert_data($designated_tbl->gl_transaction_details_tbl, $set_gl_det_trans);

												
												$date = $year . '-' . '03-01';
												$set_gl_det_trans = array(
													'gl_trans_item_id' => $gl_trans_item_id,
													'opex_amount' => $amount_mar,
													'opex_budget_date' => $date,
													'gl_trans_det_added' => date_now(),
													'gl_trans_det_status' => 1
												);
												$this->admin->insert_data($designated_tbl->gl_transaction_details_tbl, $set_gl_det_trans);

												
												$date = $year . '-' . '04-01';
												$set_gl_det_trans = array(
													'gl_trans_item_id' => $gl_trans_item_id,
													'opex_amount' => $amount_apr,
													'opex_budget_date' => $date,
													'gl_trans_det_added' => date_now(),
													'gl_trans_det_status' => 1
												);
												$this->admin->insert_data($designated_tbl->gl_transaction_details_tbl, $set_gl_det_trans);
												
												
												$date = $year . '-' . '05-01';
												$set_gl_det_trans = array(
													'gl_trans_item_id' => $gl_trans_item_id,
													'opex_amount' => $amount_may,
													'opex_budget_date' => $date,
													'gl_trans_det_added' => date_now(),
													'gl_trans_det_status' => 1
												);
												$this->admin->insert_data($designated_tbl->gl_transaction_details_tbl, $set_gl_det_trans);

												
												$date = $year . '-' . '06-01';
												$set_gl_det_trans = array(
													'gl_trans_item_id' => $gl_trans_item_id,
													'opex_amount' => $amount_jun,
													'opex_budget_date' => $date,
													'gl_trans_det_added' => date_now(),
													'gl_trans_det_status' => 1
												);
												$this->admin->insert_data($designated_tbl->gl_transaction_details_tbl, $set_gl_det_trans);

												$date = $year . '-' . '07-01';
												$set_gl_det_trans = array(
													'gl_trans_item_id' => $gl_trans_item_id,
													'opex_amount' => $amount_jul,
													'opex_budget_date' => $date,
													'gl_trans_det_added' => date_now(),
													'gl_trans_det_status' => 1
												);
												$this->admin->insert_data($designated_tbl->gl_transaction_details_tbl, $set_gl_det_trans);

											
												$date = $year . '-' . '08-01';
												$set_gl_det_trans = array(
													'gl_trans_item_id' => $gl_trans_item_id,
													'opex_amount' => $amount_aug,
													'opex_budget_date' => $date,
													'gl_trans_det_added' => date_now(),
													'gl_trans_det_status' => 1
												);
												$this->admin->insert_data($designated_tbl->gl_transaction_details_tbl, $set_gl_det_trans);

												
												$date = $year . '-' . '09-01';
												$set_gl_det_trans = array(
													'gl_trans_item_id' => $gl_trans_item_id,
													'opex_amount' => $amount_sep,
													'opex_budget_date' => $date,
													'gl_trans_det_added' => date_now(),
													'gl_trans_det_status' => 1
												);
												$this->admin->insert_data($designated_tbl->gl_transaction_details_tbl, $set_gl_det_trans);

												
												$date = $year . '-' . '10-01';
												$set_gl_det_trans = array(
													'gl_trans_item_id' => $gl_trans_item_id,
													'opex_amount' => $amount_oct,
													'opex_budget_date' => $date,
													'gl_trans_det_added' => date_now(),
													'gl_trans_det_status' => 1
												);
												$this->admin->insert_data($designated_tbl->gl_transaction_details_tbl, $set_gl_det_trans);

												
												$date = $year . '-' . '11-01';
												$set_gl_det_trans = array(
													'gl_trans_item_id' => $gl_trans_item_id,
													'opex_amount' => $amount_nov,
													'opex_budget_date' => $date,
													'gl_trans_det_added' => date_now(),
													'gl_trans_det_status' => 1
												);
												$this->admin->insert_data($designated_tbl->gl_transaction_details_tbl, $set_gl_det_trans);

												
												$date = $year . '-' . '12-01';
												$set_gl_det_trans = array(
													'gl_trans_item_id' => $gl_trans_item_id,
													'opex_amount' => $amount_dec,
													'opex_budget_date' => $date,
													'gl_trans_det_added' => date_now(),
													'gl_trans_det_status' => 1
												);
												$this->admin->insert_data($designated_tbl->gl_transaction_details_tbl, $set_gl_det_trans);
											}else{
												$this->db->trans_rollback();
												$msg = '<div class="alert alert-danger">Error while inserting transaction, please try again!</div>';
												$this->session->set_flashdata('message', $msg);
												redirect('unit/transac-opex/');	
											}
										}
									}else{
										$this->db->trans_rollback();
										$msg = '<div class="alert alert-danger">Error while inserting transaction, please try again!</div>';
										$this->session->set_flashdata('message', $msg);
										redirect('unit/transac-opex/');
									} 
									$count++;
								}
							}else{
								$this->db->trans_rollback();
								$msg = '<div class="alert alert-danger">Error while inserting transaction, please try again!</div>';
								$this->session->set_flashdata('message', $msg);
								redirect('unit/transac-opex/');
							}

							if($this->db->trans_status() === FALSE){
								$this->db->trans_rollback();
								$msg = '<div class="alert alert-danger">Error please try again!</div>';
							}else{
								$this->db->trans_commit();
								$msg = '<div class="alert alert-success">OPEX successfully added.</strong></div>';
							}

							$this->session->set_flashdata('message', $msg);
							redirect('unit/transac-opex/');
						}else{
							$msg = '<div class="alert alert-danger">Error please try again!</div>';	
							$this->session->set_flashdata('message', $msg);
							redirect('unit/transac-opex/');
						}
					}else{
						$msg = '<div class="alert alert-danger">Error please try again!</div>';	
						$this->session->set_flashdata('message', $msg);
						redirect('unit/transac-opex/');
					}
				}else{
					echo 'This transaction is locked. Please contact your administrator!';
				}
			}else{
				echo 'Error please try again!';
			}
		}
	}

	public function view_opex($id){
		$info = $this->_require_login();
		$user_info = $this->get_user_info();
		$cost_center_id = $user_info['cost_center_id'];
		$designated_tbl = $this->_get_designated_tbl();
		$data['title'] = 'View OPEX';
		$data['id'] = $id;
		$gl_trans_id = decode($id);
		$join_id = array(
			'gl_group_tbl b' => 'a.gl_group_id = b.gl_group_id AND a.gl_trans_status=1 AND a.gl_trans_id = ' . $gl_trans_id,
			'cost_center_tbl c' => 'a.cost_center_id = c.cost_center_id AND c.cost_center_id = ' . $cost_center_id
		);
		$check_id = $this->admin->check_join($designated_tbl->gl_transaction_tbl.' a', $join_id, TRUE);

		if($check_id['result'] == TRUE){
			$data['cost_center'] = encode($check_id['info']->cost_center_id);
			$data['cost_center_code'] = encode($check_id['info']->cost_center_code);
			$data['gl_group'] = $check_id['info']->gl_group_name;
			$year = $check_id['info']->gl_year;
			$data['year'] = $year;
			$module = 'OPEX';
			$data['budget_status'] = $this->check_module($module, $year);

			$join_det = array(
				$designated_tbl->gl_transaction_item_tbl.' b' => 'a.gl_trans_item_id = b.gl_trans_item_id AND b.gl_trans_item_status = 1 AND a.gl_trans_det_status = 1 AND b.gl_trans_id=' . $gl_trans_id,
				$designated_tbl->gl_transaction_tbl.' c' => 'b.gl_trans_id = c.gl_trans_id AND c.gl_year = ' . $year,
				'cost_center_tbl d' => 'b.cost_center_id = d.cost_center_id',
				'gl_subgroup_tbl e' => 'b.gl_sub_id=e.gl_sub_id'
				
			);


			$data['gl_details'] = $this->admin->get_join($designated_tbl->gl_transaction_details_tbl.' a', $join_det, FALSE, FALSE, 'b.gl_trans_item_id', '*, (SELECT SUM(x.opex_amount) FROM '.$designated_tbl->gl_transaction_details_tbl.' x WHERE b.gl_trans_item_id=x.gl_trans_item_id AND YEAR(x.opex_budget_date)=' . $year . ') as total_qty, (SELECT SUM(x.opex_amount) FROM '.$designated_tbl->gl_transaction_details_tbl.' x WHERE b.gl_trans_item_id=x.gl_trans_item_id AND MONTH(x.opex_budget_date)=1 AND YEAR(x.opex_budget_date)=' . $year . ') as jan, (SELECT SUM(x.opex_amount) FROM '.$designated_tbl->gl_transaction_details_tbl.' x WHERE b.gl_trans_item_id=x.gl_trans_item_id AND MONTH(opex_budget_date)=2 AND YEAR(x.opex_budget_date)=' . $year . ') as feb, (SELECT SUM(x.opex_amount) FROM '.$designated_tbl->gl_transaction_details_tbl.' x WHERE b.gl_trans_item_id=x.gl_trans_item_id AND MONTH(x.opex_budget_date)=3 AND YEAR(x.opex_budget_date)=' . $year . ') as mar, (SELECT SUM(x.opex_amount) FROM '.$designated_tbl->gl_transaction_details_tbl.' x WHERE b.gl_trans_item_id=x.gl_trans_item_id AND MONTH(x.opex_budget_date)=4 AND YEAR(x.opex_budget_date)=' . $year . ') as apr, (SELECT SUM(x.opex_amount) FROM '.$designated_tbl->gl_transaction_details_tbl.' x WHERE b.gl_trans_item_id=x.gl_trans_item_id AND MONTH(x.opex_budget_date)=5 AND YEAR(x.opex_budget_date)=' . $year . ') as may, (SELECT SUM(x.opex_amount) FROM '.$designated_tbl->gl_transaction_details_tbl.' x WHERE b.gl_trans_item_id=x.gl_trans_item_id AND MONTH(x.opex_budget_date)=6 AND YEAR(x.opex_budget_date)=' . $year . ') as jun, (SELECT SUM(x.opex_amount) FROM '.$designated_tbl->gl_transaction_details_tbl.' x WHERE b.gl_trans_item_id=x.gl_trans_item_id AND MONTH(x.opex_budget_date)=7 AND YEAR(x.opex_budget_date)=' . $year . ') as jul, (SELECT SUM(x.opex_amount) FROM '.$designated_tbl->gl_transaction_details_tbl.' x WHERE b.gl_trans_item_id=x.gl_trans_item_id AND MONTH(x.opex_budget_date)=8 AND YEAR(x.opex_budget_date)=' . $year . ') as aug, (SELECT SUM(x.opex_amount) FROM '.$designated_tbl->gl_transaction_details_tbl.' x WHERE b.gl_trans_item_id=x.gl_trans_item_id AND MONTH(x.opex_budget_date)=9 AND YEAR(x.opex_budget_date)=' . $year . ') as sep, (SELECT SUM(x.opex_amount) FROM '.$designated_tbl->gl_transaction_details_tbl.' x WHERE b.gl_trans_item_id=x.gl_trans_item_id AND MONTH(x.opex_budget_date)=10 AND YEAR(x.opex_budget_date)=' . $year . ') as oct, (SELECT SUM(x.opex_amount) FROM '.$designated_tbl->gl_transaction_details_tbl.' x WHERE b.gl_trans_item_id=x.gl_trans_item_id AND MONTH(x.opex_budget_date)=11 AND YEAR(x.opex_budget_date)=' . $year . ') as nov, (SELECT SUM(x.opex_amount) FROM '.$designated_tbl->gl_transaction_details_tbl.' x WHERE b.gl_trans_item_id=x.gl_trans_item_id AND MONTH(x.opex_budget_date)=12 AND YEAR(x.opex_budget_date)=' . $year . ') as december');
			$data['content'] = $this->load->view('unit/unit_opex_view', $data , TRUE);
			$this->load->view('unit/templates', $data);
		}else{

		}
	}

	public function sw_view($id){
		$info = $this->_require_login();
		$user_info = $this->get_user_info();
		$cost_center_id = $user_info['cost_center_id'];
		$data['title'] = 'View (Salaries & Wages)';
		$data['id'] = $id;
		$trans_id = decode($id);
		$join_id = array(
			'cost_center_tbl b' => 'a.cost_center_id = b.cost_center_id AND a.emp_salary_trans_status = 1 AND a.emp_salary_trans_id = ' . $trans_id . ' AND b.cost_center_id = ' . $cost_center_id
		);
		$check_id = $this->admin->check_join('employee_salary_trans_tbl a', $join_id, TRUE);

		if($check_id['result'] == TRUE){
			$data['cost_center'] = encode($check_id['info']->cost_center_id);
			$data['cost_center_code'] = encode($check_id['info']->cost_center_code);
			$data['cost_desc'] = encode($check_id['info']->cost_center_desc);
			$data['gl_group'] = 'SALARIES & WAGES';
			$join_det = array(
				'employee_salary_item_tbl b' => 'a.emp_salary_item_id = b.emp_salary_item_id AND a.emp_salary_trans_dtl_status = 1 AND b.emp_salary_item_status = 1',
				'employee_salary_trans_tbl c' => 'b.emp_salary_trans_id = c.emp_salary_trans_id AND c.emp_salary_trans_status = 1 AND c.emp_salary_trans_id = ' . $trans_id,
				'cost_center_tbl d' => 'b.cost_center_id = d.cost_center_id',
				'employee_tbl e' => 'b.emp_id = e.emp_id'
			);

			$year = $check_id['info']->emp_salary_trans_year;
			$data['year'] = $year;
			$module = 'OPEX';
			$data['budget_status'] = $this->check_module($module, $year);
			$data['sw_details'] = $this->admin->get_join('employee_salary_trans_dtl_tbl a', $join_det, FALSE, FALSE, 'b.emp_salary_item_id', '*, (SELECT SUM(x.emp_salary_budget) FROM employee_salary_trans_dtl_tbl x WHERE b.emp_salary_item_id=x.emp_salary_item_id AND YEAR(x.emp_salary_date)=' . $year . ') as total_qty, (SELECT SUM(x.emp_salary_budget) FROM employee_salary_trans_dtl_tbl x WHERE b.emp_salary_item_id=x.emp_salary_item_id AND MONTH(x.emp_salary_date)=1 AND YEAR(x.emp_salary_date)=' . $year . ') as jan, (SELECT SUM(x.emp_salary_budget) FROM employee_salary_trans_dtl_tbl x WHERE b.emp_salary_item_id=x.emp_salary_item_id AND MONTH(emp_salary_date)=2 AND YEAR(x.emp_salary_date)=' . $year . ') as feb, (SELECT SUM(x.emp_salary_budget) FROM employee_salary_trans_dtl_tbl x WHERE b.emp_salary_item_id=x.emp_salary_item_id AND MONTH(x.emp_salary_date)=3 AND YEAR(x.emp_salary_date)=' . $year . ') as mar, (SELECT SUM(x.emp_salary_budget) FROM employee_salary_trans_dtl_tbl x WHERE b.emp_salary_item_id=x.emp_salary_item_id AND MONTH(x.emp_salary_date)=4 AND YEAR(x.emp_salary_date)=' . $year . ') as apr, (SELECT SUM(x.emp_salary_budget) FROM employee_salary_trans_dtl_tbl x WHERE b.emp_salary_item_id=x.emp_salary_item_id AND MONTH(x.emp_salary_date)=5 AND YEAR(x.emp_salary_date)=' . $year . ') as may, (SELECT SUM(x.emp_salary_budget) FROM employee_salary_trans_dtl_tbl x WHERE b.emp_salary_item_id=x.emp_salary_item_id AND MONTH(x.emp_salary_date)=6 AND YEAR(x.emp_salary_date)=' . $year . ') as jun, (SELECT SUM(x.emp_salary_budget) FROM employee_salary_trans_dtl_tbl x WHERE b.emp_salary_item_id=x.emp_salary_item_id AND MONTH(x.emp_salary_date)=7 AND YEAR(x.emp_salary_date)=' . $year . ') as jul, (SELECT SUM(x.emp_salary_budget) FROM employee_salary_trans_dtl_tbl x WHERE b.emp_salary_item_id=x.emp_salary_item_id AND MONTH(x.emp_salary_date)=8 AND YEAR(x.emp_salary_date)=' . $year . ') as aug, (SELECT SUM(x.emp_salary_budget) FROM employee_salary_trans_dtl_tbl x WHERE b.emp_salary_item_id=x.emp_salary_item_id AND MONTH(x.emp_salary_date)=9 AND YEAR(x.emp_salary_date)=' . $year . ') as sep, (SELECT SUM(x.emp_salary_budget) FROM employee_salary_trans_dtl_tbl x WHERE b.emp_salary_item_id=x.emp_salary_item_id AND MONTH(x.emp_salary_date)=10 AND YEAR(x.emp_salary_date)=' . $year . ') as oct, (SELECT SUM(x.emp_salary_budget) FROM employee_salary_trans_dtl_tbl x WHERE b.emp_salary_item_id=x.emp_salary_item_id AND MONTH(x.emp_salary_date)=11 AND YEAR(x.emp_salary_date)=' . $year . ') as nov, (SELECT SUM(x.emp_salary_budget) FROM employee_salary_trans_dtl_tbl x WHERE b.emp_salary_item_id=x.emp_salary_item_id AND MONTH(x.emp_salary_date)=12 AND YEAR(x.emp_salary_date)=' . $year . ') as december');
			$data['content'] = $this->load->view('unit/unit_sw_content', $data , TRUE);
			$this->load->view('unit/templates', $data);
		}else{

		}
	}

	public function remove_sw_item(){
		$info = $this->_require_login();
		if($_SERVER['REQUEST_METHOD'] == 'POST'){
			$id = decode($this->input->post('id'));
			$where = array('emp_salary_item_id' => $id);
			$check_id = $this->admin->check_data('employee_salary_item_tbl', $where, TRUE);
			if($check_id['result'] == TRUE){
				$emp_salary_trans_id = $check_id['info']->emp_salary_trans_id;
				$set = array('emp_salary_item_status' => 0);
				$remove_item = $this->admin->update_data('employee_salary_item_tbl', $set, $where);

				if($remove_item == TRUE){
					$msg = '<div class="alert alert-success">Item successfully removed.</strong></div>';
				}else{
					$msg = '<div class="alert alert-danger">Error please try again!</div>';
				}

				$this->session->set_flashdata('message', $msg);
				redirect('unit/sw-view/' . encode($emp_salary_trans_id));
			}else{
				$msg = '<div class="alert alert-danger">Error please try again!</div>';
				$this->session->set_flashdata('message', $msg);
				redirect('unit/opex-info/');
			}
		}
	}

	public function remove_opex_item(){
		$info = $this->_require_login();
		$designated_tbl = $this->_get_designated_tbl();
		if($_SERVER['REQUEST_METHOD'] == 'POST'){
			$id = decode($this->input->post('id'));
			$where = array('gl_trans_item_id' => $id);
			$check_id = $this->admin->check_data($designated_tbl->gl_transaction_item_tbl, $where, TRUE);
			if($check_id['result'] == TRUE){
				$gl_trans_id = $check_id['info']->gl_trans_id;
				$set = array('gl_trans_item_status' => 0);
				$remove_item = $this->admin->update_data($designated_tbl->gl_transaction_item_tbl, $set, $where);

				if($remove_item == TRUE){
					$msg = '<div class="alert alert-success">Item successfully removed.</strong></div>';
				}else{
					$msg = '<div class="alert alert-danger">Error please try again!</div>';
				}

				$this->session->set_flashdata('message', $msg);
				redirect('unit/view-opex/' . encode($gl_trans_id));
			}else{
				$msg = '<div class="alert alert-danger">Error please try again!</div>';
				$this->session->set_flashdata('message', $msg);
				redirect('unit/opex-info/');
			}
		}
	}

	public function get_sw_item(){
		$info = $this->_require_login();
		if($_SERVER['REQUEST_METHOD'] == 'POST'){
			$gl_name = 'SALARIES & WAGES';
			$emp_salary_item_id =clean_data(decode($this->input->post('id')));
			$join_sw = array(
				'employee_salary_trans_tbl b' => 'a.emp_salary_trans_id = b.emp_salary_trans_id AND b.emp_salary_trans_status = 1 AND a.emp_salary_item_status = 1 AND a.emp_salary_item_id = ' . $emp_salary_item_id,
				'employee_tbl c' => 'a.emp_id = c.emp_id'
			);
			$check_sw = $this->admin->check_join('employee_salary_item_tbl a', $join_sw, TRUE);
			if($check_sw['result'] == TRUE){
				$emp_name = $check_sw['info']->emp_lname . ', ' . $check_sw['info']->emp_fname;
				$join_get_item = array(
					'employee_salary_item_tbl b' => 'b.emp_salary_item_id = a.emp_salary_item_id AND b.emp_salary_item_status = 1 AND a.emp_salary_trans_dtl_status = 1 AND b.emp_salary_item_id = ' . $emp_salary_item_id,
				);
				$get_item = $this->admin->get_join('employee_salary_trans_dtl_tbl a', $join_get_item, FALSE, 'a.emp_salary_date ASC', FALSE, 'a.emp_salary_budget, MONTHNAME(a.emp_salary_date) as sw_budget_date');

				$month = array(
					'January' => 0,
					'February' => 0,
					'March' => 0,
					'April' => 0,
					'May' => 0, 
					'June' => 0,
					'July' => 0,
					'August' => 0,
					'September' => 0,
					'October' => 0,
					'November' => 0,
					'December' => 0
				);

				$total_qty = 0;
				foreach($get_item as $row_item){
					$month[$row_item->sw_budget_date] += $row_item->emp_salary_budget;
					$total_qty += $row_item->emp_salary_budget;
				}

				$details = array(
					'name' => $emp_name,
					'gl_group' => $gl_name,
					'total' => $total_qty,
					'month' => $month
				);

				$data['result'] = 1;
				$data['info'] = $details;
			}else{
				echo $this->db->last_query();
				$data['result'] = 0;
			}
		}else{
			$data['result'] = 0;
		}

		echo json_encode($data);
	}

	public function update_sw_item(){
		$info = $this->_require_login();
		if($_SERVER['REQUEST_METHOD'] == 'POST'){
			$id = $this->input->post('id');
			$opex = clean_data($this->input->post('opex'));
			$count = 0;
			$this->db->trans_start();
			foreach($id as $row_id){
				$item_id = decode($row_id);
				$check_item = $this->admin->check_data('employee_salary_item_tbl', array('emp_salary_item_id' => $item_id, 'emp_salary_item_status' => 1), TRUE);

				$emp_salary_trans_id = '';
				if($check_item['result'] == TRUE){
					$emp_salary_trans_id = $check_item['info']->emp_salary_trans_id;

					$amount = $opex['jan'][$count];
					$update_item = $this->admin->update_data('employee_salary_trans_dtl_tbl', array('emp_salary_budget' => $amount), array('emp_salary_item_id' => $item_id, 'MONTH(emp_salary_date)' => 1, 'emp_salary_trans_dtl_status' => 1));
					
					$amount = $opex['feb'][$count];
					$update_item = $this->admin->update_data('employee_salary_trans_dtl_tbl', array('emp_salary_budget' => $amount), array('emp_salary_item_id' => $item_id, 'MONTH(emp_salary_date)' => 2, 'emp_salary_trans_dtl_status' => 1));


					$amount = $opex['mar'][$count];
					$update_item = $this->admin->update_data('employee_salary_trans_dtl_tbl', array('emp_salary_budget' => $amount), array('emp_salary_item_id' => $item_id, 'MONTH(emp_salary_date)' => 3, 'emp_salary_trans_dtl_status' => 1));


					$amount = $opex['apr'][$count];
					$update_item = $this->admin->update_data('employee_salary_trans_dtl_tbl', array('emp_salary_budget' => $amount), array('emp_salary_item_id' => $item_id, 'MONTH(emp_salary_date)' => 4, 'emp_salary_trans_dtl_status' => 1));

					$amount = $opex['may'][$count];
					$update_item = $this->admin->update_data('employee_salary_trans_dtl_tbl', array('emp_salary_budget' => $amount), array('emp_salary_item_id' => $item_id, 'MONTH(emp_salary_date)' => 5, 'emp_salary_trans_dtl_status' => 1));
					
					$amount = $opex['jun'][$count];
					$update_item = $this->admin->update_data('employee_salary_trans_dtl_tbl', array('emp_salary_budget' => $amount), array('emp_salary_item_id' => $item_id,'MONTH(emp_salary_date)' => 6, 'emp_salary_trans_dtl_status' => 1));

					$amount = $opex['jul'][$count];
					$update_item = $this->admin->update_data('employee_salary_trans_dtl_tbl', array('emp_salary_budget' => $amount), array('emp_salary_item_id' => $item_id, 'MONTH(emp_salary_date)' => 7, 'emp_salary_trans_dtl_status' => 1));

					$amount = $opex['aug'][$count];
					$update_item = $this->admin->update_data('employee_salary_trans_dtl_tbl', array('emp_salary_budget' => $amount), array('emp_salary_item_id' => $item_id, 'MONTH(emp_salary_date)' => 8, 'emp_salary_trans_dtl_status' => 1));

					$amount = $opex['sep'][$count];
					$update_item = $this->admin->update_data('employee_salary_trans_dtl_tbl', array('emp_salary_budget' => $amount), array('emp_salary_item_id' => $item_id, 'MONTH(emp_salary_date)' => 9, 'emp_salary_trans_dtl_status' => 1));
					
					$amount = $opex['oct'][$count];
					$update_item = $this->admin->update_data('employee_salary_trans_dtl_tbl', array('emp_salary_budget' => $amount), array('emp_salary_item_id' => $item_id, 'MONTH(emp_salary_date)' => 10, 'emp_salary_trans_dtl_status' => 1));

					$amount = $opex['nov'][$count];
					$update_item = $this->admin->update_data('employee_salary_trans_dtl_tbl', array('emp_salary_budget' => $amount), array('emp_salary_item_id' => $item_id, 'MONTH(emp_salary_date)' => 11, 'emp_salary_trans_dtl_status' => 1));

					$amount = $opex['dec'][$count];
					$update_item = $this->admin->update_data('employee_salary_trans_dtl_tbl', array('emp_salary_budget' => $amount), array('emp_salary_item_id' => $item_id, 'MONTH(emp_salary_date)' => 12, 'emp_salary_trans_dtl_status' => 1));
				}else{
					$msg = '<div class="alert alert-danger">Error please try again!</div>';
					$this->session->set_flashdata('message', $msg);
					redirect('unit/sw-view/' . encode($emp_salary_trans_id));
				}

				$count++;
			}

			if($this->db->trans_status() === FALSE){
				$this->db->trans_rollback();
				$msg = '<div class="alert alert-danger">Error please try again!</div>';
			}else{
				$this->db->trans_commit();
				$msg = '<div class="alert alert-success">CAPEX successfully updated.</strong></div>';
			}

			$this->session->set_flashdata('message', $msg);
			redirect('unit/sw-view/' . encode($emp_salary_trans_id));
		}
	}

	public function add_sw_item($id){
		$info = $this->_require_login();
		$user_info = $this->get_user_info();
		$cost_center_id = $user_info['cost_center_id'];
		$data['title'] = 'Add Salaries & Wages';

		$data['id'] = $id;
		$emp_salary_trans_id = decode($id);
		$join_id = array(
			'cost_center_tbl b' => 'a.cost_center_id = b.cost_center_id AND a.emp_salary_trans_id = ' . $emp_salary_trans_id . ' AND b.cost_center_id = ' . $cost_center_id
		);

		$check_id = $this->admin->check_join('employee_salary_trans_tbl a', $join_id, TRUE);
		if($check_id['result'] == TRUE){
			$cost_center_id = $check_id['info']->cost_center_id;
			$cost_center_code = $check_id['info']->cost_center_code;
			$gl_name = 'SALARIES & WAGES';

			$data['cost_center'] = encode($cost_center_id);
			$data['cost_center_code'] = encode($cost_center_code);
			$data['gl_group'] = $gl_name;
			$year = $check_id['info']->emp_salary_trans_year;
			$data['year'] = $year;

			$module = 'OPEX';
			$budget_status = $this->check_module($module, $year);
			if($budget_status == 1){
			
				$data['gl'] = $this->_salary_data($cost_center_id, TRUE, $year);
				$data['content'] = $this->load->view('unit/unit_sw_add_item', $data , TRUE);
				$this->load->view('unit/templates', $data);
			}else{

			}
		}else{

		}
	}

	public function add_trans_sw_item(){
		$info = $this->_require_login();
		$user_id = decode($info['user_id']);
		
		if($_SERVER['REQUEST_METHOD'] == 'POST'){
			$id = clean_data($this->input->post('id'));
			$emp_salary_trans_id = clean_data(decode($this->input->post('emp_salary_trans_id')));
			if(!empty($emp_salary_trans_id) && !empty($id)){
				$check_id = $this->admin->check_data('employee_salary_trans_tbl', array('emp_salary_trans_id' => $emp_salary_trans_id), TRUE);
				if($check_id['result'] == TRUE){
					$year = $check_id['info']->emp_salary_trans_year;

					$gl_name = 'SALARIES & WAGES';
					$this->db->trans_start();
					$salary = clean_data($this->input->post('salary'));
					$count = 0;
					foreach($id as $emp){
						$emp_id = decode($emp);

						$check_emp = $this->admin->check_data('employee_year_tbl', array('emp_id' => $emp_id, 'emp_year_status' => 1, 'emp_year' => $year), TRUE);

						if($check_emp['result'] == TRUE){
							$emp_cost_center = $check_emp['info']->cost_center_id;
							$rank_id = $check_emp['info']->rank_id;

							$set_item = array(
								'emp_salary_trans_id' => $emp_salary_trans_id,
								'emp_id' => $emp_id,
								'cost_center_id' => $emp_cost_center,
								'rank_id' => $rank_id,
								'emp_salary_item_added' => date_now(),
								'emp_salary_item_status' => 1
							);

							$insert_item = $this->admin->insert_data('employee_salary_item_tbl', $set_item, TRUE);
							$emp_salary_item_id = $insert_item['id'];


							$date = $year . '-01-01';
							$amount_jan = $salary['jan'][$count];
							$set_emp_jan = array(
								'emp_salary_item_id' => $emp_salary_item_id,
								'emp_salary_budget' => $amount_jan,
								'emp_salary_date' => $date,
								'emp_salary_trans_added' => date_now(),
								'emp_salary_trans_dtl_status' => 1
							);
							$insert_emp_dtl = $this->admin->insert_data('employee_salary_trans_dtl_tbl', $set_emp_jan);

							$date = $year . '-02-01';
							$amount_feb = $salary['feb'][$count];
							$set_emp_feb = array(
								'emp_salary_item_id' => $emp_salary_item_id,
								'emp_salary_budget' => $amount_feb,
								'emp_salary_date' => $date,
								'emp_salary_trans_added' => date_now(),
								'emp_salary_trans_dtl_status' => 1
							);
							$insert_emp_dtl = $this->admin->insert_data('employee_salary_trans_dtl_tbl', $set_emp_feb);

							$date = $year . '-03-01';
							$amount_mar = $salary['mar'][$count];
							$set_emp_mar = array(
								'emp_salary_item_id' => $emp_salary_item_id,
								'emp_salary_budget' => $amount_mar,
								'emp_salary_date' => $date,
								'emp_salary_trans_added' => date_now(),
								'emp_salary_trans_dtl_status' => 1
							);
							$insert_emp_dtl = $this->admin->insert_data('employee_salary_trans_dtl_tbl', $set_emp_mar);

							$date = $year . '-04-01';
							$amount_apr = $salary['apr'][$count];
							$set_emp_apr = array(
								'emp_salary_item_id' => $emp_salary_item_id,
								'emp_salary_budget' => $amount_apr,
								'emp_salary_date' => $date,
								'emp_salary_trans_added' => date_now(),
								'emp_salary_trans_dtl_status' => 1
							);
							$insert_emp_dtl = $this->admin->insert_data('employee_salary_trans_dtl_tbl', $set_emp_apr);

							$date = $year . '-05-01';
							$amount_may = $salary['may'][$count];
							$set_emp_may = array(
								'emp_salary_item_id' => $emp_salary_item_id,
								'emp_salary_budget' => $amount_may,
								'emp_salary_date' => $date,
								'emp_salary_trans_added' => date_now(),
								'emp_salary_trans_dtl_status' => 1
							);
							$insert_emp_dtl = $this->admin->insert_data('employee_salary_trans_dtl_tbl', $set_emp_may);

							$date = $year . '-06-01';
							$amount_jun = $salary['jun'][$count];
							$set_emp_jun = array(
								'emp_salary_item_id' => $emp_salary_item_id,
								'emp_salary_budget' => $amount_jun,
								'emp_salary_date' => $date,
								'emp_salary_trans_added' => date_now(),
								'emp_salary_trans_dtl_status' => 1
							);
							$insert_emp_dtl = $this->admin->insert_data('employee_salary_trans_dtl_tbl', $set_emp_jun);

							$date = $year . '-07-01';
							$amount_jul = $salary['jul'][$count];
							$set_emp_jul = array(
								'emp_salary_item_id' => $emp_salary_item_id,
								'emp_salary_budget' => $amount_jul,
								'emp_salary_date' => $date,
								'emp_salary_trans_added' => date_now(),
								'emp_salary_trans_dtl_status' => 1
							);
							$insert_emp_dtl = $this->admin->insert_data('employee_salary_trans_dtl_tbl', $set_emp_jul);

							$date = $year . '-08-01';
							$amount_aug = $salary['aug'][$count];
							$set_emp_aug = array(
								'emp_salary_item_id' => $emp_salary_item_id,
								'emp_salary_budget' => $amount_aug,
								'emp_salary_date' => $date,
								'emp_salary_trans_added' => date_now(),
								'emp_salary_trans_dtl_status' => 1
							);
							$insert_emp_dtl = $this->admin->insert_data('employee_salary_trans_dtl_tbl', $set_emp_aug);

							$date = $year . '-09-01';
							$amount_sep = $salary['sep'][$count];
							$set_emp_sep = array(
								'emp_salary_item_id' => $emp_salary_item_id,
								'emp_salary_budget' => $amount_sep,
								'emp_salary_date' => $date,
								'emp_salary_trans_added' => date_now(),
								'emp_salary_trans_dtl_status' => 1
							);
							$insert_emp_dtl = $this->admin->insert_data('employee_salary_trans_dtl_tbl', $set_emp_sep);

							$date = $year . '-10-01';
							$amount_oct = $salary['oct'][$count];
							$set_emp_oct = array(
								'emp_salary_item_id' => $emp_salary_item_id,
								'emp_salary_budget' => $amount_oct,
								'emp_salary_date' => $date,
								'emp_salary_trans_added' => date_now(),
								'emp_salary_trans_dtl_status' => 1
							);
							$insert_emp_dtl = $this->admin->insert_data('employee_salary_trans_dtl_tbl', $set_emp_oct);

							$date = $year . '-11-01';
							$amount_nov = $salary['nov'][$count];
							$set_emp_nov = array(
								'emp_salary_item_id' => $emp_salary_item_id,
								'emp_salary_budget' => $amount_nov,
								'emp_salary_date' => $date,
								'emp_salary_trans_added' => date_now(),
								'emp_salary_trans_dtl_status' => 1
							);
							$insert_emp_dtl = $this->admin->insert_data('employee_salary_trans_dtl_tbl', $set_emp_nov);

							$date = $year . '-12-01';
							$amount_dec = $salary['dec'][$count];
							$set_emp_dec = array(
								'emp_salary_item_id' => $emp_salary_item_id,
								'emp_salary_budget' => $amount_dec,
								'emp_salary_date' => $date,
								'emp_salary_trans_added' => date_now(),
								'emp_salary_trans_dtl_status' => 1
							);
							$insert_emp_dtl = $this->admin->insert_data('employee_salary_trans_dtl_tbl', $set_emp_dec);
						}else{
							$this->db->trans_rollback();
							$msg = '<div class="alert alert-danger">Error while inserting transaction, please try again!</div>';
							$this->session->set_flashdata('message', $msg);
							redirect('unit/sw-view/' . encode($emp_salary_trans_id));
						}

						$count++;
					}

					if($this->db->trans_status() === FALSE){
						$this->db->trans_rollback();
						$msg = '<div class="alert alert-danger">Error please try again!</div>';
					}else{
						$this->db->trans_commit();
						$msg = '<div class="alert alert-success">OPEX successfully added.</strong></div>';
					}
					
					$this->session->set_flashdata('message', $msg);
					redirect('unit/sw-view/' . encode($emp_salary_trans_id));
				}else{

				}
			}else{
				echo 'Error please try again!';
			}
		}
	}

	public function add_opex_item($id){
		$info = $this->_require_login();
		$data['title'] = 'Add OPEX Item';

		$data['id'] = $id;
		$gl_trans_id = decode($id);
		$user_info = $this->get_user_info();
		$designated_tbl = $this->_get_designated_tbl();

		$cost_center_id = $user_info['cost_center_id'];
		$join_id = array(
			'gl_group_tbl b' => 'a.gl_group_id = b.gl_group_id AND a.gl_trans_status=1 AND a.gl_trans_id = ' . $gl_trans_id,
			'cost_center_tbl c' => 'a.cost_center_id = c.cost_center_id AND c.cost_center_id = ' . $cost_center_id
		);
		$check_id = $this->admin->check_join($designated_tbl->gl_transaction_tbl.' a', $join_id, TRUE);

		if($check_id['result'] == TRUE){
			$cost_center_id = $check_id['info']->cost_center_id;
			$cost_center_code = $check_id['info']->cost_center_code;
			$gl_name = $check_id['info']->gl_group_name;
			$gl_group_id = $check_id['info']->gl_group_id;
			$year = $check_id['info']->gl_year;
			$data['year'] = $year;
			$module = 'OPEX';
			$budget_status = $this->check_module($module, $year);
			if($budget_status == 1){

				$data['gl_group'] = $check_id['info']->gl_group_name;
				$data['cost_center'] = encode($cost_center_id);
				$data['cost_center_code'] = encode($cost_center_code);
				$data['gl_group'] = $gl_name;
				
				$data['gl'] = $this->_gl_data($cost_center_id, $gl_group_id, $gl_name);	
				$data['content'] = $this->load->view('unit/unit_opex_add_item', $data , TRUE);
				$this->load->view('unit/templates', $data);
			}else{
				echo 'This transaction is locked. Please contact your administrator!';
			}
		}else{
			echo 'Error please try again!';
		}
	}

	public function add_trans_opex_item(){
		$info = $this->_require_login();
		$user_id = decode($info['user_id']);
		$designated_tbl = $this->_get_designated_tbl();
		
		if($_SERVER['REQUEST_METHOD'] == 'POST'){
			$gl_trans_id = clean_data(decode($this->input->post('gl_trans_id')));
			$id = clean_data($this->input->post('id'));
			if(!empty($gl_trans_id)){
				$join_trans = array(
					'gl_group_tbl b' => 'a.gl_group_id = b.gl_group_id AND a.gl_trans_status = 1 AND a.gl_trans_id =' . $gl_trans_id
				);
				$check_trans = $this->admin->check_join($designated_tbl->gl_transaction_tbl.' a', $join_trans, TRUE);
				if($check_trans['result'] == TRUE){
					$gl_name = $check_trans['info']->gl_group_name;
					$year = $check_trans['info']->gl_year;
					
					$this->db->trans_start();
					/*if($gl_name == 'SALARIES & WAGES'){
						$salary = clean_data($this->input->post('salary'));

						$set_salary = array(
							'cost_center_id' => $cost_center_id,
							'user_id' => $user_id,
							'trans_type_id' => 1,
							'emp_salary_trans_year' => $year,
							'emp_salary_trans_added' => date_now(),
							'emp_salary_trans_status' => 1
						);
						
						$insert_emp = $this->admin->insert_data('employee_salary_trans_tbl', $set_salary, TRUE);
						if($insert_emp['result'] == TRUE){
							$emp_salary_trans_id = $insert_emp['id'];
							$count = 0;
							foreach($id as $emp){
								$emp_id = decode($emp);
								
								$check_emp = $this->admin->check_data('employee_year_tbl', array('emp_id' => $emp_id, 'emp_year_status' => 1, 'emp_year' => $year), TRUE);

								if($check_emp['result'] == TRUE){

									$emp_cost_center = $check_emp['info']->cost_center_id;
									$rank_id = $check_emp['info']->rank_id;

									$set_item = array(
										'emp_salary_trans_id' => $emp_salary_trans_id,
										'emp_id' => $emp_id,
										'cost_center_id' => $emp_cost_center,
										'rank_id' => $rank_id,
										'emp_salary_item_added' => date_now(),
										'emp_salary_item_status' => 1
									);

									$insert_item = $this->admin->insert_data('employee_salary_item_tbl', $set_item, TRUE);
									$emp_salary_item_id = $insert_item['id'];


									$date = $year . '-01-01';
									$amount_jan = $salary['jan'][$count];
									$set_emp_jan = array(
										'emp_salary_item_id' => $emp_salary_item_id,
										'emp_salary_budget' => $amount_jan,
										'emp_salary_date' => $date,
										'emp_salary_trans_added' => date_now(),
										'emp_salary_trans_dtl_status' => 1
									);
									$insert_emp_dtl = $this->admin->insert_data('employee_salary_trans_dtl_tbl', $set_emp_jan);

									$date = $year . '-02-01';
									$amount_feb = $salary['feb'][$count];
									$set_emp_feb = array(
										'emp_salary_item_id' => $emp_salary_item_id,
										'emp_salary_budget' => $amount_feb,
										'emp_salary_date' => $date,
										'emp_salary_trans_added' => date_now(),
										'emp_salary_trans_dtl_status' => 1
									);
									$insert_emp_dtl = $this->admin->insert_data('employee_salary_trans_dtl_tbl', $set_emp_feb);

									$date = $year . '-03-01';
									$amount_mar = $salary['mar'][$count];
									$set_emp_mar = array(
										'emp_salary_item_id' => $emp_salary_item_id,
										'emp_salary_budget' => $amount_mar,
										'emp_salary_date' => $date,
										'emp_salary_trans_added' => date_now(),
										'emp_salary_trans_dtl_status' => 1
									);
									$insert_emp_dtl = $this->admin->insert_data('employee_salary_trans_dtl_tbl', $set_emp_mar);

									$date = $year . '-04-01';
									$amount_apr = $salary['apr'][$count];
									$set_emp_apr = array(
										'emp_salary_item_id' => $emp_salary_item_id,
										'emp_salary_budget' => $amount_apr,
										'emp_salary_date' => $date,
										'emp_salary_trans_added' => date_now(),
										'emp_salary_trans_dtl_status' => 1
									);
									$insert_emp_dtl = $this->admin->insert_data('employee_salary_trans_dtl_tbl', $set_emp_apr);

									$date = $year . '-05-01';
									$amount_may = $salary['may'][$count];
									$set_emp_may = array(
										'emp_salary_item_id' => $emp_salary_item_id,
										'emp_salary_budget' => $amount_may,
										'emp_salary_date' => $date,
										'emp_salary_trans_added' => date_now(),
										'emp_salary_trans_dtl_status' => 1
									);
									$insert_emp_dtl = $this->admin->insert_data('employee_salary_trans_dtl_tbl', $set_emp_may);

									$date = $year . '-06-01';
									$amount_jun = $salary['jun'][$count];
									$set_emp_jun = array(
										'emp_salary_item_id' => $emp_salary_item_id,
										'emp_salary_budget' => $amount_jun,
										'emp_salary_date' => $date,
										'emp_salary_trans_added' => date_now(),
										'emp_salary_trans_dtl_status' => 1
									);
									$insert_emp_dtl = $this->admin->insert_data('employee_salary_trans_dtl_tbl', $set_emp_jun);

									$date = $year . '-07-01';
									$amount_jul = $salary['jul'][$count];
									$set_emp_jul = array(
										'emp_salary_item_id' => $emp_salary_item_id,
										'emp_salary_budget' => $amount_jul,
										'emp_salary_date' => $date,
										'emp_salary_trans_added' => date_now(),
										'emp_salary_trans_dtl_status' => 1
									);
									$insert_emp_dtl = $this->admin->insert_data('employee_salary_trans_dtl_tbl', $set_emp_jul);

									$date = $year . '-08-01';
									$amount_aug = $salary['aug'][$count];
									$set_emp_aug = array(
										'emp_salary_item_id' => $emp_salary_item_id,
										'emp_salary_budget' => $amount_aug,
										'emp_salary_date' => $date,
										'emp_salary_trans_added' => date_now(),
										'emp_salary_trans_dtl_status' => 1
									);
									$insert_emp_dtl = $this->admin->insert_data('employee_salary_trans_dtl_tbl', $set_emp_aug);

									$date = $year . '-09-01';
									$amount_sep = $salary['sep'][$count];
									$set_emp_sep = array(
										'emp_salary_item_id' => $emp_salary_item_id,
										'emp_salary_budget' => $amount_sep,
										'emp_salary_date' => $date,
										'emp_salary_trans_added' => date_now(),
										'emp_salary_trans_dtl_status' => 1
									);
									$insert_emp_dtl = $this->admin->insert_data('employee_salary_trans_dtl_tbl', $set_emp_sep);

									$date = $year . '-10-01';
									$amount_oct = $salary['oct'][$count];
									$set_emp_oct = array(
										'emp_salary_item_id' => $emp_salary_item_id,
										'emp_salary_budget' => $amount_oct,
										'emp_salary_date' => $date,
										'emp_salary_trans_added' => date_now(),
										'emp_salary_trans_dtl_status' => 1
									);
									$insert_emp_dtl = $this->admin->insert_data('employee_salary_trans_dtl_tbl', $set_emp_oct);

									$date = $year . '-11-01';
									$amount_nov = $salary['nov'][$count];
									$set_emp_nov = array(
										'emp_salary_item_id' => $emp_salary_item_id,
										'emp_salary_budget' => $amount_nov,
										'emp_salary_date' => $date,
										'emp_salary_trans_added' => date_now(),
										'emp_salary_trans_dtl_status' => 1
									);
									$insert_emp_dtl = $this->admin->insert_data('employee_salary_trans_dtl_tbl', $set_emp_nov);

									$date = $year . '-12-01';
									$amount_dec = $salary['dec'][$count];
									$set_emp_dec = array(
										'emp_salary_item_id' => $emp_salary_item_id,
										'emp_salary_budget' => $amount_dec,
										'emp_salary_date' => $date,
										'emp_salary_trans_added' => date_now(),
										'emp_salary_trans_dtl_status' => 1
									);
									$insert_emp_dtl = $this->admin->insert_data('employee_salary_trans_dtl_tbl', $set_emp_dec);
								}else{
									$this->db->trans_rollback();
									$msg = '<div class="alert alert-danger">Error while inserting transaction, please try again!</div>';
									$this->session->set_flashdata('message', $msg);
									redirect('unit/transac-opex/');
								}

								$count++;
							}
						}else{
							$this->db->trans_rollback();
							$msg = '<div class="alert alert-danger">Error while inserting transaction, please try again!</div>';
							$this->session->set_flashdata('message', $msg);
							redirect('unit/transac-opex/');
						}

						if($this->db->trans_status() === FALSE){
							$this->db->trans_rollback();
							$msg = '<div class="alert alert-danger">Error please try again!</div>';
						}else{
							$this->db->trans_commit();
							$msg = '<div class="alert alert-success">OPEX successfully added.</strong></div>';
						}
					}else{*/
					

					$gl_cost_center = clean_data($this->input->post('cost_center'));
					$id = clean_data($this->input->post('id'));
					$opex = clean_data($this->input->post('opex'));
					$count = 0;
					foreach($id as $row){
						$gl_sub_id = decode($row);
						$check_gl_sub_id = $this->admin->check_data('gl_subgroup_tbl', array('gl_sub_id' => $gl_sub_id, 'gl_sub_status' => 1), TRUE);
						
						if($check_gl_sub_id['result'] == TRUE){
							$gl_cost_center_id = decode($gl_cost_center[$count]);

							$amount_jan = floatval(check_num($opex['jan'][$count]));
							$amount_feb = floatval(check_num($opex['feb'][$count]));
							$amount_mar = floatval(check_num($opex['mar'][$count]));
							$amount_apr = floatval(check_num($opex['apr'][$count]));
							$amount_may = floatval(check_num($opex['may'][$count]));
							$amount_jun = floatval(check_num($opex['jun'][$count]));
							$amount_jul = floatval(check_num($opex['jul'][$count]));
							$amount_aug = floatval(check_num($opex['aug'][$count]));
							$amount_sep = floatval(check_num($opex['sep'][$count]));
							$amount_oct = floatval(check_num($opex['oct'][$count]));
							$amount_nov = floatval(check_num($opex['nov'][$count]));
							$amount_dec = floatval(check_num($opex['dec'][$count]));

							$total_amount = $amount_jan + $amount_feb + $amount_mar + $amount_apr + $amount_may + $amount_jun + $amount_jul + $amount_aug + $amount_sep + $amount_oct + $amount_nov + $amount_dec;

							if($total_amount > 0){

								$set_item = array(
									'gl_trans_id' => $gl_trans_id,
									'gl_sub_id' => $gl_sub_id,
									'cost_center_id' => $gl_cost_center_id,
									'gl_transaction_type_id' => 2,
									'user_id' => $user_id,
									'gl_trans_item_added' => date_now(),
									'gl_trans_item_status' =>	1
								);
								$insert_item = $this->admin->insert_data($designated_tbl->gl_transaction_item_tbl, $set_item, TRUE);

								if($insert_item['result'] == TRUE){
									$gl_trans_item_id = $insert_item['id'];
									$date = $year . '-' . '01-01';
									$set_gl_trans = array(
										'gl_trans_item_id' => $gl_trans_item_id,
										'opex_amount' => $amount_jan,
										'opex_budget_date' => $date,
										'gl_trans_det_added' => date_now(),
										'gl_trans_det_status' => 1
									);
									$this->admin->insert_data($designated_tbl->gl_transaction_details_tbl, $set_gl_trans);

									
									$date = $year . '-' . '02-01';
									$set_gl_det_trans = array(
										'gl_trans_item_id' => $gl_trans_item_id,
										'opex_amount' => $amount_feb,
										'opex_budget_date' => $date,
										'gl_trans_det_added' => date_now(),
										'gl_trans_det_status' => 1
									);
									$this->admin->insert_data($designated_tbl->gl_transaction_details_tbl, $set_gl_det_trans);

									$date = $year . '-' . '03-01';
									$set_gl_det_trans = array(
										'gl_trans_item_id' => $gl_trans_item_id,
										'opex_amount' => $amount_mar,
										'opex_budget_date' => $date,
										'gl_trans_det_added' => date_now(),
										'gl_trans_det_status' => 1
									);
									$this->admin->insert_data($designated_tbl->gl_transaction_details_tbl, $set_gl_det_trans);

									
									$date = $year . '-' . '04-01';
									$set_gl_det_trans = array(
										'gl_trans_item_id' => $gl_trans_item_id,
										'opex_amount' => $amount_apr,
										'opex_budget_date' => $date,
										'gl_trans_det_added' => date_now(),
										'gl_trans_det_status' => 1
									);
									$this->admin->insert_data($designated_tbl->gl_transaction_details_tbl, $set_gl_det_trans);
									
									
									$date = $year . '-' . '05-01';
									$set_gl_det_trans = array(
										'gl_trans_item_id' => $gl_trans_item_id,
										'opex_amount' => $amount_may,
										'opex_budget_date' => $date,
										'gl_trans_det_added' => date_now(),
										'gl_trans_det_status' => 1
									);
									$this->admin->insert_data($designated_tbl->gl_transaction_details_tbl, $set_gl_det_trans);

									
									$date = $year . '-' . '06-01';
									$set_gl_det_trans = array(
										'gl_trans_item_id' => $gl_trans_item_id,
										'opex_amount' => $amount_jun,
										'opex_budget_date' => $date,
										'gl_trans_det_added' => date_now(),
										'gl_trans_det_status' => 1
									);
									$this->admin->insert_data($designated_tbl->gl_transaction_details_tbl, $set_gl_det_trans);

									$date = $year . '-' . '07-01';
									$set_gl_det_trans = array(
										'gl_trans_item_id' => $gl_trans_item_id,
										'opex_amount' => $amount_jul,
										'opex_budget_date' => $date,
										'gl_trans_det_added' => date_now(),
										'gl_trans_det_status' => 1
									);
									$this->admin->insert_data($designated_tbl->gl_transaction_details_tbl, $set_gl_det_trans);

									
									$date = $year . '-' . '08-01';
									$set_gl_det_trans = array(
										'gl_trans_item_id' => $gl_trans_item_id,
										'opex_amount' => $amount_aug,
										'opex_budget_date' => $date,
										'gl_trans_det_added' => date_now(),
										'gl_trans_det_status' => 1
									);
									$this->admin->insert_data($designated_tbl->gl_transaction_details_tbl, $set_gl_det_trans);

									
									$date = $year . '-' . '09-01';
									$set_gl_det_trans = array(
										'gl_trans_item_id' => $gl_trans_item_id,
										'opex_amount' => $amount_sep,
										'opex_budget_date' => $date,
										'gl_trans_det_added' => date_now(),
										'gl_trans_det_status' => 1
									);
									$this->admin->insert_data($designated_tbl->gl_transaction_details_tbl, $set_gl_det_trans);

									
									$date = $year . '-' . '10-01';
									$set_gl_det_trans = array(
										'gl_trans_item_id' => $gl_trans_item_id,
										'opex_amount' => $amount_oct,
										'opex_budget_date' => $date,
										'gl_trans_det_added' => date_now(),
										'gl_trans_det_status' => 1
									);
									$this->admin->insert_data($designated_tbl->gl_transaction_details_tbl, $set_gl_det_trans);

									
									$date = $year . '-' . '11-01';
									$set_gl_det_trans = array(
										'gl_trans_item_id' => $gl_trans_item_id,
										'opex_amount' => $amount_nov,
										'opex_budget_date' => $date,
										'gl_trans_det_added' => date_now(),
										'gl_trans_det_status' => 1
									);
									$this->admin->insert_data($designated_tbl->gl_transaction_details_tbl, $set_gl_det_trans);

									
									$date = $year . '-' . '12-01';
									$set_gl_det_trans = array(
										'gl_trans_item_id' => $gl_trans_item_id,
										'opex_amount' => $amount_dec,
										'opex_budget_date' => $date,
										'gl_trans_det_added' => date_now(),
										'gl_trans_det_status' => 1
									);
									$this->admin->insert_data($designated_tbl->gl_transaction_details_tbl, $set_gl_det_trans);
									$count++;
								}else{
									$this->db->trans_rollback();
									$msg = '<div class="alert alert-danger">Error while inserting transaction, please try again!</div>';
									$this->session->set_flashdata('message', $msg);
									redirect('unit/transac-opex/');	
								}
							}
						}else{
							$this->db->trans_rollback();
							$msg = '<div class="alert alert-danger">Error while inserting transaction, please try again!</div>';
							$this->session->set_flashdata('message', $msg);
							redirect('unit/transac-opex/');
						} 
					}
					

					if($this->db->trans_status() === FALSE){
						$this->db->trans_rollback();
						$msg = '<div class="alert alert-danger">Error please try again!</div>';
					}else{
						$this->db->trans_commit();
						$msg = '<div class="alert alert-success">OPEX successfully added.</strong></div>';
					}

					$this->session->set_flashdata('message', $msg);
					redirect('unit/view-opex/' . encode($gl_trans_id));
					
				}else{
					$msg = '<div class="alert alert-danger">Error please try again!</div>';	
					$this->session->set_flashdata('message', $msg);
					redirect('unit/view-opex/' . encode($gl_trans_id));
				}
			}else{
				echo 'Error please try again!';
			}
		}
	}
	public function get_opex_item(){
		$info = $this->_require_login();
		$designated_tbl = $this->_get_designated_tbl();
		if($_SERVER['REQUEST_METHOD'] == 'POST'){
			$gl_item_id =clean_data(decode($this->input->post('id')));
			$cost_center =clean_data(decode($this->input->post('cost_center')));
			$join_gl = array(
				$designated_tbl->gl_transaction_item_tbl.' b' => 'a.gl_sub_id = b.gl_sub_id AND b.gl_trans_item_status = 1 AND b.gl_trans_item_id = ' . $gl_item_id,
				'gl_group_tbl c' => 'a.gl_group_id = c.gl_group_id AND a.gl_sub_status = 1',
			);
			$check_gl = $this->admin->check_join('gl_subgroup_tbl a', $join_gl, TRUE);
			if($check_gl['result'] == TRUE){
				$gl_name = $check_gl['info']->gl_group_name;
				$selected_cost_center = $check_gl['info']->cost_center_id;
				$check_cost_center = $this->admin->check_data('cost_center_tbl', array('cost_center_id' => $cost_center, 'cost_center_status' => 1), TRUE);
				
				if($check_cost_center['result'] == TRUE){
					$cost_center_id = $check_cost_center['info']->cost_center_id;

					if($gl_name == 'STORE EXPENSES'){
						$get_cost_center = $this->admin->get_data('cost_center_tbl', array('parent_id' => $cost_center, 'cost_center_type_id' => 8 , 'cost_center_status' => 1));
					}else{
						$get_cost_center = $this->admin->get_data('cost_center_tbl', array('parent_id' => $cost_center, 'cost_center_type_id !=' => 8, 'cost_center_status' => 1));
					}

					$cost_center_data = '';
					foreach($get_cost_center as $row){
						$selected = '';
						if($selected_cost_center == $row->cost_center_id){
							$selected = ' selected';
						}
						$cost_center_data .= '<option value="' . encode($row->cost_center_id) . '"' . $selected . '>' . $row->cost_center_desc . '</option>';
					}
					
					$join_get_item = array(
						$designated_tbl->gl_transaction_item_tbl.' b' => 'a.gl_trans_item_id = b.gl_trans_item_id AND b.gl_trans_item_status = 1 AND a.gl_trans_det_status = 1 AND b.gl_trans_item_id = ' . $gl_item_id,
					);
					$get_item = $this->admin->get_join($designated_tbl->gl_transaction_details_tbl.' a', $join_get_item, FALSE, 'a.opex_budget_date ASC', FALSE, 'a.opex_amount, MONTHNAME(a.opex_budget_date) as opex_budget_date');

					$month = array(
						'January' => 0,
						'February' => 0,
						'March' => 0,
						'April' => 0,
						'May' => 0, 
						'June' => 0,
						'July' => 0,
						'August' => 0,
						'September' => 0,
						'October' => 0,
						'November' => 0,
						'December' => 0
					);

					$total_qty = 0;
					foreach($get_item as $row_item){
						$month[$row_item->opex_budget_date] += $row_item->opex_amount;
						$total_qty += $row_item->opex_amount;
					}


					$details = array(
						'gl_group' => $gl_name,
						'total' => $total_qty,
						'cost_center' => $cost_center_data,
						'month' => $month
					);

					$data['result'] = 1;
					$data['info'] = $details;					
				}else{
					$data[''] = 0;
				}
				
			}else{
				$data['result'] = 0;
			}
		}else{
			$data['result'] = 0;
		}

		echo json_encode($data);
	}

	public function update_opex_item(){
		$info = $this->_require_login();
		$designated_tbl = $this->_get_designated_tbl();
		if($_SERVER['REQUEST_METHOD'] == 'POST'){
			$id = $this->input->post('id');
			$cost_center_id = decode(clean_data($this->input->post('cost_center')));
			$opex = clean_data($this->input->post('opex'));
			$count = 0;
			$this->db->trans_start();
			foreach($id as $row_id){
				$item_id = decode($row_id);
				$check_item = $this->admin->check_data($designated_tbl->gl_transaction_item_tbl, array('gl_trans_item_id' => $item_id), TRUE);

				if($check_item['result'] == TRUE){
					$cost_center_db = $check_item['info']->cost_center_id;
					$gl_trans_id = $check_item['info']->gl_trans_id;
					if($cost_center_db != $cost_center_id){
						$update_cost_center = $this->admin->update_data($designated_tbl->gl_transaction_item_tbl, array('cost_center_id' => $cost_center_id), array('gl_trans_item_id' => $item_id));
					}

					$amount = check_num($opex['jan'][$count]);
					$update_item = $this->admin->update_data($designated_tbl->gl_transaction_details_tbl, array('opex_amount' => $amount), array('gl_trans_item_id' => $item_id, 'MONTH(opex_budget_date)' => 1, 'gl_trans_det_status' => 1));
					
					$amount = check_num($opex['feb'][$count]);
					$update_item = $this->admin->update_data($designated_tbl->gl_transaction_details_tbl, array('opex_amount' => $amount), array('gl_trans_item_id' => $item_id, 'MONTH(opex_budget_date)' => 2, 'gl_trans_det_status' => 1));


					$amount = check_num($opex['mar'][$count]);
					$update_item = $this->admin->update_data($designated_tbl->gl_transaction_details_tbl, array('opex_amount' => $amount), array('gl_trans_item_id' => $item_id, 'MONTH(opex_budget_date)' => 3, 'gl_trans_det_status' => 1));


					$amount = check_num($opex['apr'][$count]);
					$update_item = $this->admin->update_data($designated_tbl->gl_transaction_details_tbl, array('opex_amount' => $amount), array('gl_trans_item_id' => $item_id, 'MONTH(opex_budget_date)' => 4, 'gl_trans_det_status' => 1));

					$amount = check_num($opex['may'][$count]);
					$update_item = $this->admin->update_data($designated_tbl->gl_transaction_details_tbl, array('opex_amount' => $amount), array('gl_trans_item_id' => $item_id, 'MONTH(opex_budget_date)' => 5, 'gl_trans_det_status' => 1));
					
					$amount = check_num($opex['jun'][$count]);
					$update_item = $this->admin->update_data($designated_tbl->gl_transaction_details_tbl, array('opex_amount' => $amount), array('gl_trans_item_id' => $item_id, 'MONTH(opex_budget_date)' => 6, 'gl_trans_det_status' => 1));

					$amount = check_num($opex['jul'][$count]);
					$update_item = $this->admin->update_data($designated_tbl->gl_transaction_details_tbl, array('opex_amount' => $amount), array('gl_trans_item_id' => $item_id, 'MONTH(opex_budget_date)' => 7, 'gl_trans_det_status' => 1));

					$amount = check_num($opex['aug'][$count]);
					$update_item = $this->admin->update_data($designated_tbl->gl_transaction_details_tbl, array('opex_amount' => $amount), array('gl_trans_item_id' => $item_id, 'MONTH(opex_budget_date)' => 8, 'gl_trans_det_status' => 1));

					$amount = check_num($opex['sep'][$count]);
					$update_item = $this->admin->update_data($designated_tbl->gl_transaction_details_tbl, array('opex_amount' => $amount), array('gl_trans_item_id' => $item_id, 'MONTH(opex_budget_date)' => 9, 'gl_trans_det_status' => 1));
					
					$amount = check_num($opex['oct'][$count]);
					$update_item = $this->admin->update_data($designated_tbl->gl_transaction_details_tbl, array('opex_amount' => $amount), array('gl_trans_item_id' => $item_id, 'MONTH(opex_budget_date)' => 10, 'gl_trans_det_status' => 1));

					$amount = check_num($opex['nov'][$count]);
					$update_item = $this->admin->update_data($designated_tbl->gl_transaction_details_tbl, array('opex_amount' => $amount), array('gl_trans_item_id' => $item_id, 'MONTH(opex_budget_date)' => 11, 'gl_trans_det_status' => 1));

					$amount = check_num($opex['dec'][$count]);
					$update_item = $this->admin->update_data($designated_tbl->gl_transaction_details_tbl, array('opex_amount' => $amount), array('gl_trans_item_id' => $item_id, 'MONTH(opex_budget_date)' => 12, 'gl_trans_det_status' => 1));
				}else{
					$msg = '<div class="alert alert-danger">Error please try again!</div>';
					$this->session->set_flashdata('message', $msg);
					redirect('unit/opex-info/');
				}

				$count++;
			}

			if($this->db->trans_status() === FALSE){
				$this->db->trans_rollback();
				$msg = '<div class="alert alert-danger">Error please try again!</div>';
			}else{
				$this->db->trans_commit();
				$msg = '<div class="alert alert-success">OPEX successfully updated.</strong></div>';
			}

			$this->session->set_flashdata('message', $msg);
			redirect('unit/view-opex/' . encode($gl_trans_id));
		}
	}

	/*public function download_opex($year){
		$info = $this->_require_login();

		$user_info = $this->get_user_info();
		$cost_center_id = $user_info['cost_center_id'];
		$company_unit_id = $user_info['company_unit_id'];

		ini_set('max_execution_time', 0); 
		ini_set('memory_limit','2048M');

		$check_cost_center = $this->admin->check_data('cost_center_tbl', array('cost_center_id' => $cost_center_id, 'cost_center_status' => 1), TRUE);
		if($check_cost_center['result'] == TRUE){
			$cost_center_name = $check_cost_center['info']->cost_center_desc;

			$join_det = array(
				'gl_transaction_item_tbl b' => 'a.gl_trans_item_id = b.gl_trans_item_id AND b.gl_trans_item_status = 1 AND a.gl_trans_det_status = 1',
				'gl_transaction_tbl c' => 'b.gl_trans_id = c.gl_trans_id AND c.gl_trans_status = 1 AND c.gl_year = ' . $year,
				'cost_center_tbl d' => 'b.cost_center_id = d.cost_center_id AND (d.parent_id = ' . $cost_center_id . ' OR d.cost_center_id = ' . $cost_center_id . ')',
				'gl_subgroup_tbl e' => 'b.gl_sub_id=e.gl_sub_id',
				'gl_group_tbl f' => 'e.gl_group_id = f.gl_group_id'	
			);
				
			$gl_details = $this->admin->get_join('gl_transaction_details_tbl a', $join_det, FALSE, FALSE, 'b.gl_trans_item_id', '*, (SELECT SUM(x.opex_amount) FROM gl_transaction_details_tbl x WHERE b.gl_trans_item_id=x.gl_trans_item_id AND YEAR(x.opex_budget_date)=' . $year . ') as total_qty, (SELECT SUM(x.opex_amount) FROM gl_transaction_details_tbl x WHERE b.gl_trans_item_id=x.gl_trans_item_id AND MONTH(x.opex_budget_date)=1 AND YEAR(x.opex_budget_date)=' . $year . ') as jan, (SELECT SUM(x.opex_amount) FROM gl_transaction_details_tbl x WHERE b.gl_trans_item_id=x.gl_trans_item_id AND MONTH(opex_budget_date)=2 AND YEAR(x.opex_budget_date)=' . $year . ') as feb, (SELECT SUM(x.opex_amount) FROM gl_transaction_details_tbl x WHERE b.gl_trans_item_id=x.gl_trans_item_id AND MONTH(x.opex_budget_date)=3 AND YEAR(x.opex_budget_date)=' . $year . ') as mar, (SELECT SUM(x.opex_amount) FROM gl_transaction_details_tbl x WHERE b.gl_trans_item_id=x.gl_trans_item_id AND MONTH(x.opex_budget_date)=4 AND YEAR(x.opex_budget_date)=' . $year . ') as apr, (SELECT SUM(x.opex_amount) FROM gl_transaction_details_tbl x WHERE b.gl_trans_item_id=x.gl_trans_item_id AND MONTH(x.opex_budget_date)=5 AND YEAR(x.opex_budget_date)=' . $year . ') as may, (SELECT SUM(x.opex_amount) FROM gl_transaction_details_tbl x WHERE b.gl_trans_item_id=x.gl_trans_item_id AND MONTH(x.opex_budget_date)=6 AND YEAR(x.opex_budget_date)=' . $year . ') as jun, (SELECT SUM(x.opex_amount) FROM gl_transaction_details_tbl x WHERE b.gl_trans_item_id=x.gl_trans_item_id AND MONTH(x.opex_budget_date)=7 AND YEAR(x.opex_budget_date)=' . $year . ') as jul, (SELECT SUM(x.opex_amount) FROM gl_transaction_details_tbl x WHERE b.gl_trans_item_id=x.gl_trans_item_id AND MONTH(x.opex_budget_date)=8 AND YEAR(x.opex_budget_date)=' . $year . ') as aug, (SELECT SUM(x.opex_amount) FROM gl_transaction_details_tbl x WHERE b.gl_trans_item_id=x.gl_trans_item_id AND MONTH(x.opex_budget_date)=9 AND YEAR(x.opex_budget_date)=' . $year . ') as sep, (SELECT SUM(x.opex_amount) FROM gl_transaction_details_tbl x WHERE b.gl_trans_item_id=x.gl_trans_item_id AND MONTH(x.opex_budget_date)=10 AND YEAR(x.opex_budget_date)=' . $year . ') as oct, (SELECT SUM(x.opex_amount) FROM gl_transaction_details_tbl x WHERE b.gl_trans_item_id=x.gl_trans_item_id AND MONTH(x.opex_budget_date)=11 AND YEAR(x.opex_budget_date)=' . $year . ') as nov, (SELECT SUM(x.opex_amount) FROM gl_transaction_details_tbl x WHERE b.gl_trans_item_id=x.gl_trans_item_id AND MONTH(x.opex_budget_date)=12 AND YEAR(x.opex_budget_date)=' . $year . ') as december');

			$get_depreciation = $this->get_depreciation_monthly($cost_center_id, $year);

			$get_depreciation2 = $this->admin->get_query('SELECT *, YEAR(a.depreciation_unit_date) as gl_year,

				(SELECT SUM(x.depreciation_unit_amount) FROM depreciation_unit_tbl x WHERE b.gl_sub_id = x.gl_sub_id AND a.cost_center_id = x.cost_center_id AND x.depreciation_unit_status = 1 AND MONTH(x.depreciation_unit_date) = 1 AND YEAR(x.depreciation_unit_date) = ' . $year . ' AND x.company_unit_id = ' . $company_unit_id . ') as jan,

				(SELECT SUM(x.depreciation_unit_amount) FROM depreciation_unit_tbl x WHERE b.gl_sub_id = x.gl_sub_id AND a.cost_center_id = x.cost_center_id AND x.depreciation_unit_status = 1 AND MONTH(x.depreciation_unit_date) = 2 AND YEAR(x.depreciation_unit_date) = ' . $year . ' AND x.company_unit_id = ' . $company_unit_id . ') as feb,


				(SELECT SUM(x.depreciation_unit_amount) FROM depreciation_unit_tbl x WHERE b.gl_sub_id = x.gl_sub_id AND a.cost_center_id = x.cost_center_id AND x.depreciation_unit_status = 1 AND MONTH(x.depreciation_unit_date) = 3 AND YEAR(x.depreciation_unit_date) = ' . $year . ' AND x.company_unit_id = ' . $company_unit_id . ') as mar,


				(SELECT SUM(x.depreciation_unit_amount) FROM depreciation_unit_tbl x WHERE b.gl_sub_id = x.gl_sub_id AND a.cost_center_id = x.cost_center_id AND x.depreciation_unit_status = 1 AND MONTH(x.depreciation_unit_date) = 4 AND YEAR(x.depreciation_unit_date) = ' . $year . ' AND x.company_unit_id = ' . $company_unit_id . ') as apr,


				(SELECT SUM(x.depreciation_unit_amount) FROM depreciation_unit_tbl x WHERE b.gl_sub_id = x.gl_sub_id AND a.cost_center_id = x.cost_center_id AND x.depreciation_unit_status = 1 AND MONTH(x.depreciation_unit_date) = 5 AND YEAR(x.depreciation_unit_date) = ' . $year . ' AND x.company_unit_id = ' . $company_unit_id . ') as may,


				(SELECT SUM(x.depreciation_unit_amount) FROM depreciation_unit_tbl x WHERE b.gl_sub_id = x.gl_sub_id AND a.cost_center_id = x.cost_center_id AND x.depreciation_unit_status = 1 AND MONTH(x.depreciation_unit_date) = 6 AND YEAR(x.depreciation_unit_date) = ' . $year . ' AND x.company_unit_id = ' . $company_unit_id . ') as jun,


				(SELECT SUM(x.depreciation_unit_amount) FROM depreciation_unit_tbl x WHERE b.gl_sub_id = x.gl_sub_id AND a.cost_center_id = x.cost_center_id AND x.depreciation_unit_status = 1 AND MONTH(x.depreciation_unit_date) = 7 AND YEAR(x.depreciation_unit_date) = ' . $year . ' AND x.company_unit_id = ' . $company_unit_id . ') as jul,

				(SELECT SUM(x.depreciation_unit_amount) FROM depreciation_unit_tbl x WHERE b.gl_sub_id = x.gl_sub_id AND a.cost_center_id = x.cost_center_id AND x.depreciation_unit_status = 1 AND MONTH(x.depreciation_unit_date) = 8 AND YEAR(x.depreciation_unit_date) = ' . $year . ' AND x.company_unit_id = ' . $company_unit_id . ') as aug,

				(SELECT SUM(x.depreciation_unit_amount) FROM depreciation_unit_tbl x WHERE b.gl_sub_id = x.gl_sub_id AND a.cost_center_id = x.cost_center_id AND x.depreciation_unit_status = 1 AND MONTH(x.depreciation_unit_date) = 9 AND YEAR(x.depreciation_unit_date) = ' . $year . ' AND x.company_unit_id = ' . $company_unit_id . ') as sep,

				(SELECT SUM(x.depreciation_unit_amount) FROM depreciation_unit_tbl x WHERE b.gl_sub_id = x.gl_sub_id AND a.cost_center_id = x.cost_center_id AND x.depreciation_unit_status = 1 AND MONTH(x.depreciation_unit_date) = 10 AND YEAR(x.depreciation_unit_date) = ' . $year . ' AND x.company_unit_id = ' . $company_unit_id . ') as oct,

				(SELECT SUM(x.depreciation_unit_amount) FROM depreciation_unit_tbl x WHERE b.gl_sub_id = x.gl_sub_id AND a.cost_center_id = x.cost_center_id AND x.depreciation_unit_status = 1 AND MONTH(x.depreciation_unit_date) = 11 AND YEAR(x.depreciation_unit_date) = ' . $year . ' AND x.company_unit_id = ' . $company_unit_id . ') as nov,

				(SELECT SUM(x.depreciation_unit_amount) FROM depreciation_unit_tbl x WHERE b.gl_sub_id = x.gl_sub_id AND a.cost_center_id = x.cost_center_id AND x.depreciation_unit_status = 1 AND MONTH(x.depreciation_unit_date) = 12 AND YEAR(x.depreciation_unit_date) = ' . $year . ' AND x.company_unit_id = ' . $company_unit_id . ') as december

			  FROM depreciation_unit_tbl a, gl_subgroup_tbl b, gl_group_tbl c, cost_center_tbl d WHERE a.gl_sub_id = b.gl_sub_id AND b.gl_group_id = c.gl_group_id AND a.cost_center_id = d.cost_center_id AND c.gl_group_name = "DEPRECIATION EXPENSES" AND depreciation_unit_status = 1 AND a.cost_center_id = ' . $cost_center_id . ' GROUP BY a.gl_sub_id, a.cost_center_id');  


			$this->load->library('excel');

			$spreadsheet = $this->excel;
			$spreadsheet->getProperties()->setCreator('BAVI')
					->setLastModifiedBy('Budgeting System')
					->setTitle('Employees')
					->setSubject('List of Employees')
					->setDescription('List of Employees');

			
			$styleArray = array(
					'font' 	=> array(
							'bold' => true,
					),
					'alignment' => array(
							'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
							'vertical' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
					),
					'borders' => array(
							'top' => array(
									'style' => PHPExcel_Style_Border::BORDER_THIN,
							),
					),
					'fill' => array(
							'type' => \PHPExcel_Style_Fill::FILL_GRADIENT_LINEAR,
							'rotation' => 90,
							'startcolor' => array(
									'argb' => 'FFA0A0A0',
							),
							'endcolor' => array(
									'argb' => 'FFFFFFFF',
							),
					),
			);


			foreach(range('A','H') as $columnID) {
				$spreadsheet->getActiveSheet()->getColumnDimension($columnID)
						->setAutoSize(true);
			}

			$style_center = array(
				'font' => array(
					'bold' => true,
					'size' => 20
				),
				'alignment' => array(
					'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
				)
			);

			$style_info =  array(
				'font' => array(
					'bold' => true
				)
			);

			$style_border = array(
				'font' => array(
					'bold' => true
				),
				'alignment' => array(
					'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER
				),
				'borders' => array(
					'allborders' => array(
						'style' => PHPExcel_Style_Border::BORDER_THIN
					)
				)
			);

			$style_data = array(
				'borders' => array(
					'allborders' => array(
						'style' => PHPExcel_Style_Border::BORDER_THIN
					)
				)
			);

			$style_out = array(
				'font' => array(
					'bold' => true,
					'color' => array('rgb' => 'FF0000')
				),
				'borders' => array(
					'allborders' => array(
						'style' => PHPExcel_Style_Border::BORDER_THIN
					)
				),
				'alignment' => array(
					'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER
				),
			);


			$spreadsheet->getActiveSheet()->getStyle("A1:T1")->applyFromArray($style_border);
			$spreadsheet->getActiveSheet()->getStyle("A1:T1")->applyFromArray($style_info);
			$spreadsheet->setActiveSheetIndex(0)
				->setCellValue("A1", 'Location')
				->setCellValue("B1", 'GL Code')
				->setCellValue("C1", "GL Group")
				->setCellValue("D1", "GL Subgroup")
				->setCellValue("E1", "Cost Center Code")
				->setCellValue("F1", "Cost Center Name")
				->setCellValue("G1", "Year")
				->setCellValue("H1", "Jan")
				->setCellValue("I1", "Feb")
				->setCellValue("J1", "Mar")
				->setCellValue("K1", "Apr")
				->setCellValue("L1", "May")
				->setCellValue("M1", "Jun")
				->setCellValue("N1", "Jul")
				->setCellValue("O1", "Aug")
				->setCellValue("P1", "Sep")
				->setCellValue("Q1", "Oct")
				->setCellValue("R1", "Nov")
				->setCellValue("S1", "Dec")
				->setCellValue("T1", "Total")
				;
			// Add some data
			$x= 2;
			$count = 0;
			foreach($gl_details as $row){
				$jan = $row->jan;
				$feb = $row->feb;
				$mar = $row->mar;
				$apr = $row->apr;
				$may = $row->may;
				$jun = $row->jun;
				$jul = $row->jul;
				$aug = $row->aug;
				$sep = $row->sep;
				$oct = $row->oct;
				$nov = $row->nov;
				$december = $row->december;
				$total = $jan + $feb + $mar + $apr + $may + $jun + $jul + $aug + $sep + $oct + $nov + $december;
				$spreadsheet->setActiveSheetIndex(0)
						->setCellValue("A$x",$cost_center_name)
						->setCellValue("B$x",$row->gl_code)
						->setCellValue("C$x",$row->gl_group_name)
						->setCellValue("D$x",$row->gl_sub_name)
						->setCellValue("E$x",$row->cost_center_code)
						->setCellValue("F$x",$row->cost_center_desc)
						->setCellValue("G$x",$row->gl_year)
						->setCellValue("H$x",$jan)
						->setCellValue("I$x",$feb)
						->setCellValue("J$x",$mar)
						->setCellValue("K$x",$apr)
						->setCellValue("L$x",$may)
						->setCellValue("M$x",$jun)
						->setCellValue("N$x",$jul)
						->setCellValue("O$x",$aug)
						->setCellValue("P$x",$sep)
						->setCellValue("Q$x",$oct)
						->setCellValue("R$x",$nov)
						->setCellValue("S$x",$december)
						->setCellValue("T$x",$total)
						;

				$spreadsheet->getActiveSheet()->getStyle("A$x:T$x")->applyFromArray($style_data);
				$x++;
			}

			foreach($get_depreciation as $row){
				$jan = $row->jan;
				$feb = $row->feb;
				$mar = $row->mar;
				$apr = $row->apr;
				$may = $row->may;
				$jun = $row->jun;
				$jul = $row->jul;
				$aug = $row->aug;
				$sep = $row->sep;
				$oct = $row->oct;
				$nov = $row->nov;
				$december = $row->december;
				$amount = $row->amount;

				$month = array(0 => 0, 1 => 0, 2 => 0, 3 => 0, 4 => 0, 5 => 0, 6 => 0, 7 => 0, 8 => 0, 9 => 0, 10 => 0, 11 => 0);

				for($a = 0; $a < 12; $a++){
					$month[$a] += $jan * $amount;
				}

				for($a = 1; $a < 12; $a++){
					$month[$a] += $feb * $amount;
				}

				for($a = 2; $a < 12; $a++){
					$month[$a] += $mar * $amount;
				}

				for($a = 3; $a < 12; $a++){
					$month[$a] += $apr * $amount;
				}

				for($a = 4; $a < 12; $a++){
					$month[$a] += $may * $amount;
				}

				for($a = 5; $a < 12; $a++){
					$month[$a] += $jun * $amount;
				}

				for($a = 6; $a < 12; $a++){
					$month[$a] += $jul * $amount;
				}

				for($a = 7; $a < 12; $a++){
					$month[$a] += $aug * $amount;
				}

				for($a = 8; $a < 12; $a++){
					$month[$a] += $sep * $amount;
				}

				for($a = 9; $a < 12; $a++){
					$month[$a] += $oct * $amount;
				}

				for($a = 10; $a < 12; $a++){
					$month[$a] += $nov * $amount;
				}

				for($a = 11; $a < 12; $a++){
					$month[$a] += $december * $amount;
				}

				$total = $month[0] + $month[1] + $month[2] + $month[3] + $month[4] + $month[5] + $month[6] + $month[7] + $month[8] + $month[9] + $month[10] + $month[11];
				$spreadsheet->setActiveSheetIndex(0)
						->setCellValue("A$x",$cost_center_name)
						->setCellValue("B$x", '')
						->setCellValue("C$x", 'DEPRECIATION EXPENSES')
						->setCellValue("D$x", $row->ag_name)
						->setCellValue("E$x", $row->cost_center_code)
						->setCellValue("F$x", $row->cost_center_desc)
						->setCellValue("G$x", $row->ag_trans_budget_year)
						->setCellValue("H$x", $month[0])
						->setCellValue("I$x", $month[1])
						->setCellValue("J$x", $month[2])
						->setCellValue("K$x", $month[3])
						->setCellValue("L$x", $month[4])
						->setCellValue("M$x", $month[5])
						->setCellValue("N$x", $month[6])
						->setCellValue("O$x", $month[7])
						->setCellValue("P$x", $month[8])
						->setCellValue("Q$x", $month[9])
						->setCellValue("R$x", $month[10])
						->setCellValue("S$x", $month[11])
						->setCellValue("T$x",$total)
						;

				$spreadsheet->getActiveSheet()->getStyle("A$x:T$x")->applyFromArray($style_data);
				$x++;
			}

			foreach($get_depreciation2 as $row){
				$jan = $row->jan;
				$feb = $row->feb;
				$mar = $row->mar;
				$apr = $row->apr;
				$may = $row->may;
				$jun = $row->jun;
				$jul = $row->jul;
				$aug = $row->aug;
				$sep = $row->sep;
				$oct = $row->oct;
				$nov = $row->nov;
				$december = $row->december;
				$total = $jan + $feb + $mar + $apr + $may + $jun + $jul + $aug + $sep + $oct + $nov + $december;
				$spreadsheet->setActiveSheetIndex(0)
						->setCellValue("A$x",$cost_center_name)
						->setCellValue("B$x",$row->gl_code)
						->setCellValue("C$x", 'DEPRECIATION 2')
						->setCellValue("D$x",$row->gl_sub_name)
						->setCellValue("E$x",$row->cost_center_code)
						->setCellValue("F$x",$row->cost_center_desc)
						->setCellValue("G$x",$row->gl_year)
						->setCellValue("H$x",$jan)
						->setCellValue("I$x",$feb)
						->setCellValue("J$x",$mar)
						->setCellValue("K$x",$apr)
						->setCellValue("L$x",$may)
						->setCellValue("M$x",$jun)
						->setCellValue("N$x",$jul)
						->setCellValue("O$x",$aug)
						->setCellValue("P$x",$sep)
						->setCellValue("Q$x",$oct)
						->setCellValue("R$x",$nov)
						->setCellValue("S$x",$december)
						->setCellValue("T$x",$total)
						;

				$spreadsheet->getActiveSheet()->getStyle("A$x:T$x")->applyFromArray($style_data);
				$x++;
			}


			foreach(range('A','T') as $columnID) {
				$spreadsheet->getActiveSheet()->getColumnDimension($columnID)
						->setAutoSize(true);
			}

			$spreadsheet->getActiveSheet()->getStyle('H2:T' . ($x - 1))->getNumberFormat()->setFormatCode('#,##0.00');
			
			// Rename worksheet
			$spreadsheet->getActiveSheet()->setTitle('OPEX Data - ' . $year);

			// set right to left direction
			//		$spreadsheet->getActiveSheet()->setRightToLeft(true);

			// Set active sheet index to the first sheet, so Excel opens this as the first sheet
			$spreadsheet->setActiveSheetIndex(0);

			// Redirect output to a client’s web browser (Excel2007)
			header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
			header('Content-Disposition: attachment;filename="Budgeting - OPEX ' . $year . '.xlsx"');
			header('Cache-Control: max-age=0');
			// If you're serving to IE 9, then the following may be needed
			header('Cache-Control: max-age=1');

			// If you're serving to IE over SSL, then the following may be needed
			header('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
			header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT'); // always modified
			header('Cache-Control: cache, must-revalidate'); // HTTP/1.1
			header('Pragma: public'); // HTTP/1.0

			$writer = PHPExcel_IOFactory::createWriter($spreadsheet, 'Excel2007');
			$writer->save('php://output');
			exit;
		}else{
			echo 'Error Cost Center not exist. Please try again!';
		}
	}*/

	public function download_opex($year){
		$info = $this->_require_login();

		$user_info = $this->get_user_info();
		$cost_center_id = $user_info['cost_center_id'];
		$company_unit_id = $user_info['company_unit_id'];

		$designated_tbl = $this->_get_designated_tbl();

		ini_set('max_execution_time', 0); 
		ini_set('memory_limit','2048M');

		$check_cost_center = $this->admin->check_data('cost_center_tbl', array('cost_center_id' => $cost_center_id, 'cost_center_status' => 1), TRUE);
		if($check_cost_center['result'] == TRUE){
			$cost_center_name = $check_cost_center['info']->cost_center_desc;

			$join_det = array(
				$designated_tbl->gl_transaction_item_tbl.' b' => 'a.gl_trans_item_id = b.gl_trans_item_id AND b.gl_trans_item_status = 1 AND a.gl_trans_det_status = 1',
				$designated_tbl->gl_transaction_tbl.' c' => 'b.gl_trans_id = c.gl_trans_id AND c.gl_trans_status = 1 AND c.gl_year = ' . $year,
				'cost_center_tbl d' => 'b.cost_center_id = d.cost_center_id AND (d.parent_id = ' . $cost_center_id . ' OR d.cost_center_id = ' . $cost_center_id . ')',
				'gl_subgroup_tbl e' => 'b.gl_sub_id=e.gl_sub_id',
				'gl_group_tbl f' => 'e.gl_group_id = f.gl_group_id'	
			);
				
			$gl_details = $this->admin->get_join($designated_tbl->gl_transaction_details_tbl.' a', $join_det, FALSE, FALSE, 'b.gl_trans_item_id', '*, (SELECT SUM(x.opex_amount) FROM '.$designated_tbl->gl_transaction_details_tbl.' x WHERE b.gl_trans_item_id=x.gl_trans_item_id AND YEAR(x.opex_budget_date)=' . $year . ') as total_qty, (SELECT SUM(x.opex_amount) FROM '.$designated_tbl->gl_transaction_details_tbl.' x WHERE b.gl_trans_item_id=x.gl_trans_item_id AND MONTH(x.opex_budget_date)=1 AND YEAR(x.opex_budget_date)=' . $year . ') as jan, (SELECT SUM(x.opex_amount) FROM '.$designated_tbl->gl_transaction_details_tbl.' x WHERE b.gl_trans_item_id=x.gl_trans_item_id AND MONTH(opex_budget_date)=2 AND YEAR(x.opex_budget_date)=' . $year . ') as feb, (SELECT SUM(x.opex_amount) FROM '.$designated_tbl->gl_transaction_details_tbl.' x WHERE b.gl_trans_item_id=x.gl_trans_item_id AND MONTH(x.opex_budget_date)=3 AND YEAR(x.opex_budget_date)=' . $year . ') as mar, (SELECT SUM(x.opex_amount) FROM '.$designated_tbl->gl_transaction_details_tbl.' x WHERE b.gl_trans_item_id=x.gl_trans_item_id AND MONTH(x.opex_budget_date)=4 AND YEAR(x.opex_budget_date)=' . $year . ') as apr, (SELECT SUM(x.opex_amount) FROM '.$designated_tbl->gl_transaction_details_tbl.' x WHERE b.gl_trans_item_id=x.gl_trans_item_id AND MONTH(x.opex_budget_date)=5 AND YEAR(x.opex_budget_date)=' . $year . ') as may, (SELECT SUM(x.opex_amount) FROM '.$designated_tbl->gl_transaction_details_tbl.' x WHERE b.gl_trans_item_id=x.gl_trans_item_id AND MONTH(x.opex_budget_date)=6 AND YEAR(x.opex_budget_date)=' . $year . ') as jun, (SELECT SUM(x.opex_amount) FROM '.$designated_tbl->gl_transaction_details_tbl.' x WHERE b.gl_trans_item_id=x.gl_trans_item_id AND MONTH(x.opex_budget_date)=7 AND YEAR(x.opex_budget_date)=' . $year . ') as jul, (SELECT SUM(x.opex_amount) FROM '.$designated_tbl->gl_transaction_details_tbl.' x WHERE b.gl_trans_item_id=x.gl_trans_item_id AND MONTH(x.opex_budget_date)=8 AND YEAR(x.opex_budget_date)=' . $year . ') as aug, (SELECT SUM(x.opex_amount) FROM '.$designated_tbl->gl_transaction_details_tbl.' x WHERE b.gl_trans_item_id=x.gl_trans_item_id AND MONTH(x.opex_budget_date)=9 AND YEAR(x.opex_budget_date)=' . $year . ') as sep, (SELECT SUM(x.opex_amount) FROM '.$designated_tbl->gl_transaction_details_tbl.' x WHERE b.gl_trans_item_id=x.gl_trans_item_id AND MONTH(x.opex_budget_date)=10 AND YEAR(x.opex_budget_date)=' . $year . ') as oct, (SELECT SUM(x.opex_amount) FROM '.$designated_tbl->gl_transaction_details_tbl.' x WHERE b.gl_trans_item_id=x.gl_trans_item_id AND MONTH(x.opex_budget_date)=11 AND YEAR(x.opex_budget_date)=' . $year . ') as nov, (SELECT SUM(x.opex_amount) FROM '.$designated_tbl->gl_transaction_details_tbl.' x WHERE b.gl_trans_item_id=x.gl_trans_item_id AND MONTH(x.opex_budget_date)=12 AND YEAR(x.opex_budget_date)=' . $year . ') as december');

			$get_depreciation = $this->get_depreciation_monthly($cost_center_id, $year);
			$get_depreciation2 = $this->get_depreciation_monthly2($company_unit_id, $year);

			$this->load->library('excel');

			$spreadsheet = $this->excel;
			$spreadsheet->getProperties()->setCreator('BAVI')
					->setLastModifiedBy('Budgeting System')
					->setTitle('Employees')
					->setSubject('List of Employees')
					->setDescription('List of Employees');

			
			$styleArray = array(
					'font' 	=> array(
							'bold' => true,
					),
					'alignment' => array(
							'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
							'vertical' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
					),
					'borders' => array(
							'top' => array(
									'style' => PHPExcel_Style_Border::BORDER_THIN,
							),
					),
					'fill' => array(
							'type' => \PHPExcel_Style_Fill::FILL_GRADIENT_LINEAR,
							'rotation' => 90,
							'startcolor' => array(
									'argb' => 'FFA0A0A0',
							),
							'endcolor' => array(
									'argb' => 'FFFFFFFF',
							),
					),
			);


			foreach(range('A','H') as $columnID) {
				$spreadsheet->getActiveSheet()->getColumnDimension($columnID)
						->setAutoSize(true);
			}

			$style_center = array(
				'font' => array(
					'bold' => true,
					'size' => 20
				),
				'alignment' => array(
					'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
				)
			);

			$style_info =  array(
				'font' => array(
					'bold' => true
				)
			);

			$style_border = array(
				'font' => array(
					'bold' => true
				),
				'alignment' => array(
					'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER
				),
				'borders' => array(
					'allborders' => array(
						'style' => PHPExcel_Style_Border::BORDER_THIN
					)
				)
			);

			$style_data = array(
				'borders' => array(
					'allborders' => array(
						'style' => PHPExcel_Style_Border::BORDER_THIN
					)
				)
			);

			$style_out = array(
				'font' => array(
					'bold' => true,
					'color' => array('rgb' => 'FF0000')
				),
				'borders' => array(
					'allborders' => array(
						'style' => PHPExcel_Style_Border::BORDER_THIN
					)
				),
				'alignment' => array(
					'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER
				),
			);


			$spreadsheet->getActiveSheet()->getStyle("A1:T1")->applyFromArray($style_border);
			$spreadsheet->getActiveSheet()->getStyle("A1:T1")->applyFromArray($style_info);
			$spreadsheet->setActiveSheetIndex(0)
				->setCellValue("A1", 'Location')
				->setCellValue("B1", 'GL Code')
				->setCellValue("C1", "GL Group")
				->setCellValue("D1", "GL Subgroup")
				->setCellValue("E1", "Cost Center Code")
				->setCellValue("F1", "Cost Center Name")
				->setCellValue("G1", "Year")
				->setCellValue("H1", "Jan")
				->setCellValue("I1", "Feb")
				->setCellValue("J1", "Mar")
				->setCellValue("K1", "Apr")
				->setCellValue("L1", "May")
				->setCellValue("M1", "Jun")
				->setCellValue("N1", "Jul")
				->setCellValue("O1", "Aug")
				->setCellValue("P1", "Sep")
				->setCellValue("Q1", "Oct")
				->setCellValue("R1", "Nov")
				->setCellValue("S1", "Dec")
				->setCellValue("T1", "Total")
				;
			// Add some data
			$x= 2;
			$count = 0;
			foreach($gl_details as $row){
				$jan = $row->jan;
				$feb = $row->feb;
				$mar = $row->mar;
				$apr = $row->apr;
				$may = $row->may;
				$jun = $row->jun;
				$jul = $row->jul;
				$aug = $row->aug;
				$sep = $row->sep;
				$oct = $row->oct;
				$nov = $row->nov;
				$december = $row->december;
				$total = $jan + $feb + $mar + $apr + $may + $jun + $jul + $aug + $sep + $oct + $nov + $december;
				$spreadsheet->setActiveSheetIndex(0)
						->setCellValue("A$x",$cost_center_name)
						->setCellValue("B$x",$row->gl_code)
						->setCellValue("C$x",$row->gl_group_name)
						->setCellValue("D$x",$row->gl_sub_name)
						->setCellValue("E$x",$row->cost_center_code)
						->setCellValue("F$x",$row->cost_center_desc)
						->setCellValue("G$x",$row->gl_year)
						->setCellValue("H$x",$jan)
						->setCellValue("I$x",$feb)
						->setCellValue("J$x",$mar)
						->setCellValue("K$x",$apr)
						->setCellValue("L$x",$may)
						->setCellValue("M$x",$jun)
						->setCellValue("N$x",$jul)
						->setCellValue("O$x",$aug)
						->setCellValue("P$x",$sep)
						->setCellValue("Q$x",$oct)
						->setCellValue("R$x",$nov)
						->setCellValue("S$x",$december)
						->setCellValue("T$x",$total)
						;

				$spreadsheet->getActiveSheet()->getStyle("A$x:T$x")->applyFromArray($style_data);
				$x++;
			}

			foreach($get_depreciation as $row){
				$jan = $row->jan;
				$feb = $row->feb;
				$mar = $row->mar;
				$apr = $row->apr;
				$may = $row->may;
				$jun = $row->jun;
				$jul = $row->jul;
				$aug = $row->aug;
				$sep = $row->sep;
				$oct = $row->oct;
				$nov = $row->nov;
				$december = $row->december;
				$amount = $row->amount;

				$month = array(0 => 0, 1 => 0, 2 => 0, 3 => 0, 4 => 0, 5 => 0, 6 => 0, 7 => 0, 8 => 0, 9 => 0, 10 => 0, 11 => 0);

				for($a = 0; $a < 12; $a++){
					$month[$a] += $jan * $amount;
				}

				for($a = 1; $a < 12; $a++){
					$month[$a] += $feb * $amount;
				}

				for($a = 2; $a < 12; $a++){
					$month[$a] += $mar * $amount;
				}

				for($a = 3; $a < 12; $a++){
					$month[$a] += $apr * $amount;
				}

				for($a = 4; $a < 12; $a++){
					$month[$a] += $may * $amount;
				}

				for($a = 5; $a < 12; $a++){
					$month[$a] += $jun * $amount;
				}

				for($a = 6; $a < 12; $a++){
					$month[$a] += $jul * $amount;
				}

				for($a = 7; $a < 12; $a++){
					$month[$a] += $aug * $amount;
				}

				for($a = 8; $a < 12; $a++){
					$month[$a] += $sep * $amount;
				}

				for($a = 9; $a < 12; $a++){
					$month[$a] += $oct * $amount;
				}

				for($a = 10; $a < 12; $a++){
					$month[$a] += $nov * $amount;
				}

				for($a = 11; $a < 12; $a++){
					$month[$a] += $december * $amount;
				}

				$total = $month[0] + $month[1] + $month[2] + $month[3] + $month[4] + $month[5] + $month[6] + $month[7] + $month[8] + $month[9] + $month[10] + $month[11];
				$spreadsheet->setActiveSheetIndex(0)
						->setCellValue("A$x",$cost_center_name)
						->setCellValue("B$x", $row->ag_gl_code)
						->setCellValue("C$x", 'DEPRECIATION EXPENSES')
						->setCellValue("D$x", $row->ag_gl_name)
						->setCellValue("E$x", $row->cost_center_code)
						->setCellValue("F$x", $row->cost_center_desc)
						->setCellValue("G$x", $row->ag_trans_budget_year)
						->setCellValue("H$x", $month[0])
						->setCellValue("I$x", $month[1])
						->setCellValue("J$x", $month[2])
						->setCellValue("K$x", $month[3])
						->setCellValue("L$x", $month[4])
						->setCellValue("M$x", $month[5])
						->setCellValue("N$x", $month[6])
						->setCellValue("O$x", $month[7])
						->setCellValue("P$x", $month[8])
						->setCellValue("Q$x", $month[9])
						->setCellValue("R$x", $month[10])
						->setCellValue("S$x", $month[11])
						->setCellValue("T$x",$total)
						;

				$spreadsheet->getActiveSheet()->getStyle("A$x:T$x")->applyFromArray($style_data);
				$x++;
			}

			/*foreach($get_depreciation2 as $row){
				$jan = $row->jan;
				$feb = $row->feb;
				$mar = $row->mar;
				$apr = $row->apr;
				$may = $row->may;
				$jun = $row->jun;
				$jul = $row->jul;
				$aug = $row->aug;
				$sep = $row->sep;
				$oct = $row->oct;
				$nov = $row->nov;
				$december = $row->december;
				$total = $jan + $feb + $mar + $apr + $may + $jun + $jul + $aug + $sep + $oct + $nov + $december;
				$spreadsheet->setActiveSheetIndex(0)
						->setCellValue("A$x",$cost_center_name)
						->setCellValue("B$x",$row->gl_code)
						->setCellValue("C$x", 'DEPRECIATION 2')
						->setCellValue("D$x",$row->gl_sub_name)
						->setCellValue("E$x",$row->cost_center_code)
						->setCellValue("F$x",$row->cost_center_desc)
						->setCellValue("G$x",$row->gl_year)
						->setCellValue("H$x",$jan)
						->setCellValue("I$x",$feb)
						->setCellValue("J$x",$mar)
						->setCellValue("K$x",$apr)
						->setCellValue("L$x",$may)
						->setCellValue("M$x",$jun)
						->setCellValue("N$x",$jul)
						->setCellValue("O$x",$aug)
						->setCellValue("P$x",$sep)
						->setCellValue("Q$x",$oct)
						->setCellValue("R$x",$nov)
						->setCellValue("S$x",$december)
						->setCellValue("T$x",$total)
						;

				$spreadsheet->getActiveSheet()->getStyle("A$x:T$x")->applyFromArray($style_data);
				$x++;
			}*/

			$depre_counter = 0;
			$depre_prev_identifier = '';
			$month_counter = 0;
			$month_amount = array();
			$depre_total = 0;
			foreach($get_depreciation2 as $row){
				$depre_amount = $row->depreciation_unit_amount;
				$depre_cost_center = $row->cost_center_desc;
				$depre_cost_center_code = $row->cost_center_code;
				$depre_gl_sub_name = $row->gl_sub_name;
				$depre_gl_code = $row->gl_code;
				$depre_gl_year = $row->gl_year;
				$depre_identifier = $depre_gl_sub_name . $depre_cost_center_code;
				
				//echo $depre_amount . '|' . $depre_cost_center . '|' . $depre_gl_sub_name . '<br />';

				if($depre_counter > 0){
					$depre_total += $depre_amount;
					$month_amount[$month_counter] = $depre_amount;
					if($depre_identifier == $depre_prev_identifier){	
						if($month_counter == 11){
							$spreadsheet->setActiveSheetIndex(0)
									->setCellValue("A$x",$cost_center_name)
									->setCellValue("B$x",$depre_gl_code)
									->setCellValue("C$x", 'DEPRECIATION 2')
									->setCellValue("D$x",$depre_gl_sub_name)
									->setCellValue("E$x",$depre_cost_center_code)
									->setCellValue("F$x",$depre_cost_center)
									->setCellValue("G$x",$depre_gl_year)
									->setCellValue("H$x",$month_amount[0])
									->setCellValue("I$x",$month_amount[1])
									->setCellValue("J$x",$month_amount[2])
									->setCellValue("K$x",$month_amount[3])
									->setCellValue("L$x",$month_amount[4])
									->setCellValue("M$x",$month_amount[5])
									->setCellValue("N$x",$month_amount[6])
									->setCellValue("O$x",$month_amount[7])
									->setCellValue("P$x",$month_amount[8])
									->setCellValue("Q$x",$month_amount[9])
									->setCellValue("R$x",$month_amount[10])
									->setCellValue("S$x",$month_amount[11])
									->setCellValue("T$x",$depre_total)
									;

							$spreadsheet->getActiveSheet()->getStyle("A$x:T$x")->applyFromArray($style_data);
							$x++;

							$month_counter = 0;
							$depre_total = 0;
						}else{
							$month_counter++;
						}
					}else{
						$month_counter++;
					}

					
					$depre_prev_identifier = $depre_identifier;

				}else{
					$depre_prev_identifier = $depre_identifier;
					$depre_total += $depre_amount;
					$month_amount[$month_counter] = $depre_amount;
					$month_counter++;
				}

				$depre_counter++;
			}

			foreach(range('A','T') as $columnID) {
				$spreadsheet->getActiveSheet()->getColumnDimension($columnID)
						->setAutoSize(true);
			}

			$spreadsheet->getActiveSheet()->getStyle('H2:T' . ($x - 1))->getNumberFormat()->setFormatCode('#,##0.00');
			
			// Rename worksheet
			$spreadsheet->getActiveSheet()->setTitle('OPEX Data - ' . $year);

			// set right to left direction
			//		$spreadsheet->getActiveSheet()->setRightToLeft(true);

			// Set active sheet index to the first sheet, so Excel opens this as the first sheet
			$spreadsheet->setActiveSheetIndex(0);

			// Redirect output to a client’s web browser (Excel2007)
			header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
			header('Content-Disposition: attachment;filename="Budgeting - OPEX ' . $year . '.xlsx"');
			header('Cache-Control: max-age=0');
			// If you're serving to IE 9, then the following may be needed
			header('Cache-Control: max-age=1');

			// If you're serving to IE over SSL, then the following may be needed
			header('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
			header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT'); // always modified
			header('Cache-Control: cache, must-revalidate'); // HTTP/1.1
			header('Pragma: public'); // HTTP/1.0

			$writer = PHPExcel_IOFactory::createWriter($spreadsheet, 'Excel2007');
			$writer->save('php://output');
			exit;
		}else{
			echo 'Error Cost Center not exist. Please try again!';
		}
	}

	public function get_depreciation_monthly($cost_center, $year){
		$info = $this->_require_login();
		$designated_tbl = $this->_get_designated_tbl();

		$get_depreciation = $this->admin->get_query('SELECT e.ag_name, d.asg_name as asset_group, f.cost_center_code, f.cost_center_desc, a.ag_trans_budget_year, (b.capex_price / b.capex_lifespan) amount,
			(SELECT x.gl_sub_name FROM gl_subgroup_tbl x WHERE x.gl_code = e.ag_gl_code LIMIT 1) as ag_gl_name,

			(SELECT x.gl_code FROM gl_subgroup_tbl x WHERE x.gl_code = e.ag_gl_code LIMIT 1) as ag_gl_code,

			(SELECT x.capex_qty FROM '.$designated_tbl->asset_group_transaction_details_tbl.' x WHERE b.ag_trans_item_id = x.ag_trans_item_id AND x.ag_trans_det_status = 1 AND MONTH(x.capex_budget_date) = 1) as jan,

			(SELECT x.capex_qty FROM '.$designated_tbl->asset_group_transaction_details_tbl.' x WHERE b.ag_trans_item_id = x.ag_trans_item_id AND x.ag_trans_det_status = 1 AND MONTH(x.capex_budget_date) = 2) as feb,

			(SELECT x.capex_qty FROM '.$designated_tbl->asset_group_transaction_details_tbl.' x WHERE b.ag_trans_item_id = x.ag_trans_item_id AND x.ag_trans_det_status = 1 AND MONTH(x.capex_budget_date) = 3) as mar,

			(SELECT x.capex_qty FROM '.$designated_tbl->asset_group_transaction_details_tbl.' x WHERE b.ag_trans_item_id = x.ag_trans_item_id AND x.ag_trans_det_status = 1 AND MONTH(x.capex_budget_date) = 4) as apr,

			(SELECT x.capex_qty FROM '.$designated_tbl->asset_group_transaction_details_tbl.' x WHERE b.ag_trans_item_id = x.ag_trans_item_id AND x.ag_trans_det_status = 1 AND MONTH(x.capex_budget_date) = 5) as may,

			(SELECT x.capex_qty FROM '.$designated_tbl->asset_group_transaction_details_tbl.' x WHERE b.ag_trans_item_id = x.ag_trans_item_id AND x.ag_trans_det_status = 1 AND MONTH(x.capex_budget_date) = 6) as jun,

			(SELECT x.capex_qty FROM '.$designated_tbl->asset_group_transaction_details_tbl.' x WHERE b.ag_trans_item_id = x.ag_trans_item_id AND x.ag_trans_det_status = 1 AND MONTH(x.capex_budget_date) = 7) as jul,

			(SELECT x.capex_qty FROM '.$designated_tbl->asset_group_transaction_details_tbl.' x WHERE b.ag_trans_item_id = x.ag_trans_item_id AND x.ag_trans_det_status = 1 AND MONTH(x.capex_budget_date) = 8) as aug,

			(SELECT x.capex_qty FROM '.$designated_tbl->asset_group_transaction_details_tbl.' x WHERE b.ag_trans_item_id = x.ag_trans_item_id AND x.ag_trans_det_status = 1 AND MONTH(x.capex_budget_date) = 9) as sep,

			(SELECT x.capex_qty FROM '.$designated_tbl->asset_group_transaction_details_tbl.' x WHERE b.ag_trans_item_id = x.ag_trans_item_id AND x.ag_trans_det_status = 1 AND MONTH(x.capex_budget_date) = 10) as oct,

			(SELECT x.capex_qty FROM '.$designated_tbl->asset_group_transaction_details_tbl.' x WHERE b.ag_trans_item_id = x.ag_trans_item_id AND x.ag_trans_det_status = 1 AND MONTH(x.capex_budget_date) = 11) as nov,

			(SELECT x.capex_qty FROM '.$designated_tbl->asset_group_transaction_details_tbl.' x WHERE b.ag_trans_item_id = x.ag_trans_item_id AND x.ag_trans_det_status = 1 AND MONTH(x.capex_budget_date) = 12) as december

		 FROM '.$designated_tbl->asset_group_transaction_tbl.' a, '.$designated_tbl->asset_group_transaction_item_tbl.' b, asset_subgroup_tbl d, asset_group_tbl e, cost_center_tbl f, transaction_type_tbl g WHERE a.ag_trans_id=b.ag_trans_id AND b.asg_id=d.asg_id AND d.ag_id=e.ag_id AND b.cost_center_id=f.cost_center_id AND a.trans_type_id=g.trans_type_id AND g.trans_type_id=1 AND a.ag_trans_status=1 AND b.ag_trans_item_status = 1 AND (f.parent_id=' . $cost_center . ' OR f.cost_center_id = ' . $cost_center . ') AND a.ag_trans_budget_year=' . $year);

		return $get_depreciation;
	}

	public function get_depreciation_monthly2($company_unit_id, $year){
		$info = $this->_require_login();
		$designated_tbl = $this->_get_designated_tbl($company_unit_id);

		
		$get_depreciation = $this->admin->get_query('SELECT *, YEAR(a.depreciation_unit_date) as gl_year, SUM(a.depreciation_unit_amount) as depreciation_unit_amount

			  FROM '.$designated_tbl->depreciation_unit_tbl.' a, gl_subgroup_tbl b, gl_group_tbl c, cost_center_tbl d WHERE a.gl_sub_id = b.gl_sub_id AND b.gl_group_id = c.gl_group_id AND a.cost_center_id = d.cost_center_id AND c.gl_group_name = "DEPRECIATION EXPENSES" AND depreciation_unit_status = 1 AND a.company_unit_id = ' . $company_unit_id . ' AND YEAR(a.depreciation_unit_date) = ' . $year . ' GROUP BY a.cost_center_id, a.gl_sub_id, a.depreciation_unit_date ORDER BY a.cost_center_id, a.gl_sub_id, a.depreciation_unit_date ASC'
		);

		return $get_depreciation;
	}


	//CAPEX

	public function capex_info($year = null){
		$info = $this->_require_login();

		$user_info = $this->get_user_info();
		$bc_id = $user_info['cost_center_id'];
		$cost_center_code = $user_info['cost_center_code'];
		$unit_id = $user_info['company_unit_id'];
		$data['unit_id'] = $unit_id;
		$data['id'] = encode($cost_center_code);
		$data['title'] = 'CAPEX Info';

		$designated_tbl = $this->_get_designated_tbl();

		if($year == null){
			$year = $this->_active_year();
		}

		$module = 'CAPEX';
		$data['budget_status'] = $this->check_module($module, $year);

		$check_cost = $this->admin->check_data('cost_center_tbl', array('cost_center_code' => $cost_center_code, 'cost_center_status' => 1), TRUE);
		if($check_cost['result'] == TRUE){
			$cost_center_id = $check_cost['info']->cost_center_id;
			$cost_center_desc = $check_cost['info']->cost_center_desc;
			$data['cost_center_desc'] = $cost_center_desc;
			$join_cost = array(
				'cost_center_tbl b' => 'a.cost_center_id=b.cost_center_id AND a.ag_trans_status=1 AND b.cost_center_id=' . $cost_center_id . ' AND a.ag_trans_budget_year = ' . $year,
				'asset_group_tbl c' => 'a.ag_id=c.ag_id',
				'user_tbl d' => 'a.user_id=d.user_id'
			);
			$data['asset_group'] = $this->admin->get_join($designated_tbl->asset_group_transaction_tbl.' a', $join_cost);


			$data['capex_asset'] = $this->admin->get_query('

				SELECT ag_name, SUM(capex) as capex, SUM(capex1) as capex1, SUM(capex2) as capex2
				
				FROM
				(
					(SELECT `d`.`ag_name`, `c`.`asg_name`, SUM(a.capex_qty * `f`.`capex_price`) as capex, 0 as capex1, 0 as capex2  FROM `'.$designated_tbl->asset_group_transaction_details_tbl.'` `a` JOIN `'.$designated_tbl->asset_group_transaction_item_tbl.'` `f` ON `a`.`ag_trans_item_id` = `f`.`ag_trans_item_id` AND `f`.`ag_trans_item_status` = 1 JOIN `'.$designated_tbl->asset_group_transaction_tbl.'` `b` ON `f`.`ag_trans_id`=`b`.`ag_trans_id` AND `b`.`ag_trans_status`=1 AND `a`.`ag_trans_det_status`=1 AND `b`.`ag_trans_budget_year` = ' . $year . ' JOIN `asset_subgroup_tbl` `c` ON `f`.`asg_id`=`c`.`asg_id` JOIN `asset_group_tbl` `d` ON `c`.`ag_id` = `d`.`ag_id` JOIN `cost_center_tbl` `e` ON `f`.`cost_center_id` = `e`.`cost_center_id` AND `e`.`cost_center_id` AND `e`.`parent_id`= ' . $cost_center_id . ' GROUP BY `c`.`asg_id` ORDER BY `d`.`ag_name`)

					UNION

					(SELECT y.ag_name, "", 0 as capex, SUM(x.comp_capex_unit_val) as capex1, 0 as capex2 FROM comparative_capex_unit_tbl x, asset_group_tbl y WHERE x.ag_id = y.ag_id AND x.company_unit_id = ' . $unit_id .' AND x.comp_capex_unit_status = 1 AND x.comp_capex_unit_year = ' . ($year - 1) . ' GROUP BY y.ag_name) 

					UNION

					(SELECT y.ag_name, "", 0 as capex, 0 as capex1, SUM(x.comp_capex_unit_val) as capex2 FROM comparative_capex_unit_tbl x, asset_group_tbl y WHERE x.ag_id = y.ag_id AND x.company_unit_id = ' . $unit_id .' AND x.comp_capex_unit_status = 1 AND x.comp_capex_unit_year = ' . ($year - 2) . ' GROUP BY y.ag_name) 
				) as capex_data

				GROUP BY ag_name;
			');

			$join_capex = array(
				$designated_tbl->asset_group_transaction_item_tbl.' f' => 'a.ag_trans_item_id = f.ag_trans_item_id AND f.ag_trans_item_status = 1',
				$designated_tbl->asset_group_transaction_tbl.' b' => 'f.ag_trans_id=b.ag_trans_id AND b.ag_trans_status=1 AND a.ag_trans_det_status=1 AND b.ag_trans_budget_year = ' . $year,
				'asset_subgroup_tbl c' => 'f.asg_id=c.asg_id',
				'asset_group_tbl d' => 'c.ag_id = d.ag_id',
				'cost_center_tbl e' => 'f.cost_center_id = e.cost_center_id AND e.cost_center_id AND e.parent_id=' . $cost_center_id
			);

			$data['capex_details'] = $this->admin->get_join($designated_tbl->asset_group_transaction_details_tbl.' a', $join_capex, FALSE, 'd.ag_name', 'c.asg_id', 'd.ag_name, c.asg_name, SUM(a.capex_qty) total_qty, f.capex_price
			');

			$data['cost_center'] = encode($cost_center_id);
			$data['year'] = $year;
			$data['content'] = $this->load->view('unit/unit_capex_info_content', $data , TRUE);
			$this->load->view('unit/templates', $data);
		} else {
			$this->dashboard();

		}
	}

	public function cancel_capex(){
		$info = $this->_require_login();
		$user_info = $this->get_user_info();
		$bc_id = $user_info['cost_center_id'];
		$cost_center_code = $user_info['cost_center_code'];
		$designated_tbl = $this->_get_designated_tbl();

		if($_SERVER['REQUEST_METHOD'] == 'POST'){
			$id = decode($this->input->post('id'));
			$where = array('ag_trans_id' => $id);
			$join_id = array(
				'cost_center_tbl b' => 'a.cost_center_id = b.cost_center_id AND ag_trans_id = ' . $id
			);
			$check_id = $this->admin->check_join($designated_tbl->asset_group_transaction_tbl.' a', $join_id, TRUE);
			if($check_id['result'] == TRUE){
				$cost_center_code = $check_id['info']->cost_center_code;
				$set = array('ag_trans_status' => 0);
				$remove_capex = $this->admin->update_data($designated_tbl->asset_group_transaction_tbl, $set, $where);
				
				if($remove_capex == TRUE){
					$msg = '<div class="alert alert-success">CAPEX successfully removed.</strong></div>';
				}else{
					$msg = '<div class="alert alert-danger">Error please try again!</div>';
				}

				$this->session->set_flashdata('message', $msg);
				redirect('unit/capex-info/');
			}else{
				$msg = '<div class="alert alert-danger">Error please try again!</div>';
				$this->session->set_flashdata('message', $msg);
				redirect('unit/capex/');
			}
		}
	}

	public function add_capex_item($id){
		$info = $this->_require_login();
		$data['title'] = 'Add CAPEX Item';
		$designated_tbl = $this->_get_designated_tbl();

		$data['id'] = $id;
		$ag_trans_id = decode($id);
		$join_id = array(
			'asset_group_tbl b' => 'a.ag_id = b.ag_id AND a.ag_trans_status=1 AND a.ag_trans_id = ' . $ag_trans_id,
			'cost_center_tbl c' => 'a.cost_center_id = c.cost_center_id'
		);
		$check_id = $this->admin->check_join($designated_tbl->asset_group_transaction_tbl.' a', $join_id, TRUE);

		if($check_id['result'] == TRUE){
			$cost_center_id = $check_id['info']->cost_center_id;
			$cost_center_code = $check_id['info']->cost_center_code;
			$ag_name = $check_id['info']->ag_name;
			$ag_id = $check_id['info']->ag_id;

			$year = $check_id['info']->ag_trans_budget_year;
			$data['year'] = $year;
			$budget_status = $this->check_module('CAPEX', $year);

			if($budget_status == 1){

				$data['ag_name'] = $check_id['info']->ag_name;
				$data['cost_center'] = encode($cost_center_id);
				$data['cost_center_code'] = encode($cost_center_code);
				$data['cost_center_name'] = $check_id['info']->cost_center_desc;
				
				if($ag_name == 'TRANSPORTATION EQUIPMENT'){
					$get_asset = $this->_get_transpo_equip_data($cost_center_id, $ag_name, $ag_id, $year);
				}else{
					$get_asset = $this->_get_ag_data($cost_center_id, $ag_name, $ag_id, $year);
				}

				$data['ag'] = $get_asset['asset'];
				$data['header'] = $get_asset['header'];
				$data['content'] = $this->load->view('unit/unit_capex_add_item', $data , TRUE);
				$this->load->view('unit/templates', $data);
			}else{
				redirect('unit/capex-info');
			}
		}else{
			redirect('unit/capex-info');
		}
	}

	public function add_trans_capex_item(){
		$info = $this->_require_login();
		$user_id = decode($info['user_id']);
		$year = $this->_active_year();
		$designated_tbl = $this->_get_designated_tbl();
		if($_SERVER['REQUEST_METHOD'] == 'POST'){
			$ag_trans_id = clean_data(decode($this->input->post('ag_trans_id')));
			$bc_cost_center = clean_data(decode($this->input->post('bc_cost_center')));
			$remarks = clean_data($this->input->post('remarks'));
			$rank = clean_data($this->input->post('rank'));

			if(!empty($ag_trans_id) && !empty($bc_cost_center)){
				$check_cc = $this->admin->check_data('cost_center_tbl', array('cost_center_code' => $bc_cost_center, 'cost_center_status' => 1), TRUE);
				if($check_cc['result'] == TRUE){
					$cost_center_id = $check_cc['info']->cost_center_id;

					$join_trans = array(
						'asset_group_tbl b' => 'a.ag_id = b.ag_id AND a.ag_trans_status = 1 AND a.ag_trans_id =' . $ag_trans_id
					);
					$this->db->trans_start();
					$check_trans = $this->admin->check_join($designated_tbl->asset_group_transaction_tbl.' a', $join_trans, TRUE);
					if($check_trans['result'] == TRUE){
						
						$id = clean_data($this->input->post('id'));
						$asg_cost_center = clean_data($this->input->post('cost_center'));
						$capex_type = clean_data($this->input->post('capex_type'));
						$capex_category = clean_data($this->input->post('capex_category'));
						$capex = clean_data($this->input->post('capex'));
						$count = 0;
						foreach($id as $row){
							$asg_id = decode($row);
							$capex_remarks = '';
							if(is_array($remarks)){
								$capex_remarks = $remarks[$count];
								$rank_id = decode($rank[$count]);
							}

							$check_asg_id = $this->admin->check_data('asset_subgroup_tbl', array('asg_id' => $asg_id, 'asg_status' => 1), TRUE);
							if($check_asg_id == TRUE){
								$asset_price = $check_asg_id['info']->asg_price;
								$asset_lifespan = $check_asg_id['info']->asg_lifespan;
								$capex_type_id = decode($capex_type[$count]);
								$capex_category_id = decode($capex_category[$count]);
								$asg_cost_center_id = decode($asg_cost_center[$count]);
								$check_asg_cc = $this->admin->check_data('cost_center_tbl', array('cost_center_id' => $asg_cost_center_id));
								if($check_asg_cc == TRUE){

									$qty_jan = check_num($capex['jan'][$count]);
									$qty_feb = check_num($capex['feb'][$count]);
									$qty_mar = check_num($capex['mar'][$count]);
									$qty_apr = check_num($capex['apr'][$count]);
									$qty_may = check_num($capex['may'][$count]);
									$qty_jun = check_num($capex['jun'][$count]);
									$qty_jul = check_num($capex['jul'][$count]);
									$qty_aug = check_num($capex['aug'][$count]);
									$qty_sep = check_num($capex['sep'][$count]);
									$qty_oct = check_num($capex['oct'][$count]);
									$qty_nov = check_num($capex['nov'][$count]);
									$qty_dec = check_num($capex['dec'][$count]);

									$total_amount = $qty_jan + $qty_feb + $qty_mar + $qty_apr + $qty_may + $qty_jun + $qty_jul + $qty_aug + $qty_sep + $qty_oct + $qty_nov + $qty_dec;

									if($total_amount > 0){
										$set_item = array(
											'ag_trans_id' => $ag_trans_id,
											'asg_id' => $asg_id,
											'cost_center_id' => $asg_cost_center_id,
											'capex_type_id' => $capex_type_id,
											'capex_category_id' => $capex_category_id,
											'user_id' => $user_id,
											'capex_price' => $asset_price,
											'capex_lifespan' => $asset_lifespan,
											'capex_remarks' => $capex_remarks,
											'ag_trans_item_added' => date_now(),
											'ag_trans_item_status' => 1
										);

										$insert_item = $this->admin->insert_data($designated_tbl->asset_group_transaction_item_tbl, $set_item, TRUE);

										if($insert_item['result'] == TRUE){
											$ag_trans_item_id = $insert_item['id'];
											$date = $year . '-' . '01-01';
											$set_asg_trans = array(
												'ag_trans_item_id' => $ag_trans_item_id,
												'capex_qty' => $qty_jan,
												'capex_budget_date' => $date,
												'ag_trans_det_added' => date_now(),
												'ag_trans_det_status' => 1
											);
											$this->admin->insert_data($designated_tbl->asset_group_transaction_details_tbl, $set_asg_trans);

											
											$date = $year . '-' . '02-01';
											$set_asg_trans = array(
												'ag_trans_item_id' => $ag_trans_item_id,
												'capex_qty' => $qty_feb,
												'capex_budget_date' => $date,
												'ag_trans_det_added' => date_now(),
												'ag_trans_det_status' => 1
											);
											$this->admin->insert_data($designated_tbl->asset_group_transaction_details_tbl, $set_asg_trans);

											
											$date = $year . '-' . '03-01';
											$set_asg_trans = array(
												'ag_trans_item_id' => $ag_trans_item_id,
												'capex_qty' => $qty_mar,
												'capex_budget_date' => $date,
												'ag_trans_det_added' => date_now(),
												'ag_trans_det_status' => 1
											);
											$this->admin->insert_data($designated_tbl->asset_group_transaction_details_tbl, $set_asg_trans);

											
											$date = $year . '-' . '04-01';
											$set_asg_trans = array(
												'ag_trans_item_id' => $ag_trans_item_id,
												'capex_qty' => $qty_apr,
												'capex_budget_date' => $date,
												'ag_trans_det_added' => date_now(),
												'ag_trans_det_status' => 1
											);
											$this->admin->insert_data($designated_tbl->asset_group_transaction_details_tbl, $set_asg_trans);
											
											
											$date = $year . '-' . '05-01';
											$set_asg_trans = array(
												'ag_trans_item_id' => $ag_trans_item_id,
												'capex_qty' => $qty_may,
												'capex_budget_date' => $date,
												'ag_trans_det_added' => date_now(),
												'ag_trans_det_status' => 1
											);
											$this->admin->insert_data($designated_tbl->asset_group_transaction_details_tbl, $set_asg_trans);

											
											$date = $year . '-' . '06-01';
											$set_asg_trans = array(
												'ag_trans_item_id' => $ag_trans_item_id,
												'capex_qty' => $qty_jun,
												'capex_budget_date' => $date,
												'ag_trans_det_added' => date_now(),
												'ag_trans_det_status' => 1
											);
											$this->admin->insert_data($designated_tbl->asset_group_transaction_details_tbl, $set_asg_trans);

											$date = $year . '-' . '07-01';
											$set_asg_trans = array(
												'ag_trans_item_id' => $ag_trans_item_id,
												'capex_qty' => $qty_jul,
												'capex_budget_date' => $date,
												'ag_trans_det_added' => date_now(),
												'ag_trans_det_status' => 1
											);
											$this->admin->insert_data($designated_tbl->asset_group_transaction_details_tbl, $set_asg_trans);

											
											$date = $year . '-' . '08-01';
											$set_asg_trans = array(
												'ag_trans_item_id' => $ag_trans_item_id,
												'capex_qty' => $qty_aug,
												'capex_budget_date' => $date,
												'ag_trans_det_added' => date_now(),
												'ag_trans_det_status' => 1
											);
											$this->admin->insert_data($designated_tbl->asset_group_transaction_details_tbl, $set_asg_trans);

											
											$date = $year . '-' . '09-01';
											$set_asg_trans = array(
												'ag_trans_item_id' => $ag_trans_item_id,
												'capex_qty' => $qty_sep,
												'capex_budget_date' => $date,
												'ag_trans_det_added' => date_now(),
												'ag_trans_det_status' => 1
											);
											$this->admin->insert_data($designated_tbl->asset_group_transaction_details_tbl, $set_asg_trans);

											
											$date = $year . '-' . '10-01';
											$set_asg_trans = array(
												'ag_trans_item_id' => $ag_trans_item_id,
												'capex_qty' => $qty_oct,
												'capex_budget_date' => $date,
												'ag_trans_det_added' => date_now(),
												'ag_trans_det_status' => 1
											);
											$this->admin->insert_data($designated_tbl->asset_group_transaction_details_tbl, $set_asg_trans);

											
											$date = $year . '-' . '11-01';
											$set_asg_trans = array(
												'ag_trans_item_id' => $ag_trans_item_id,
												'capex_qty' => $qty_nov,
												'capex_budget_date' => $date,
												'ag_trans_det_added' => date_now(),
												'ag_trans_det_status' => 1
											);
											$this->admin->insert_data($designated_tbl->asset_group_transaction_details_tbl, $set_asg_trans);

											
											$date = $year . '-' . '12-01';
											$set_asg_trans = array(
												'ag_trans_item_id' => $ag_trans_item_id,
												'capex_qty' => $qty_dec,
												'capex_budget_date' => $date,
												'ag_trans_det_added' => date_now(),
												'ag_trans_det_status' => 1
											);
											$this->admin->insert_data($designated_tbl->asset_group_transaction_details_tbl, $set_asg_trans);

											if(!empty($rank_id)){
												$set_rank = array(
													'ag_trans_item_id' => $ag_trans_item_id,
													'rank_id' => $rank_id,
													'ag_trans_rank_added' => date_now(),
													'ag_trans_rank_status' => 1
												);

												$insert_rank = $this->admin->insert_data($designated_tbl->asset_group_transaction_rank_tbl, $set_rank);
											}

											$count++;
										}else{
											$msg = '<div class="alert alert-danger">Error while inserting item transaction!</div>';
											$this->session->set_flashdata('message', $msg);
											redirect('unit/view-capex/' . encode($ag_trans_id));
										}
									}
								}else{
									$this->db->trans_rollback();
									$msg = '<div class="alert alert-danger">Error empty cost center!</div>';
									$this->session->set_flashdata('message', $msg);
									redirect('unit/view-capex/' . encode($ag_trans_id));
								}
							}else{
								$msg = '<div class="alert alert-danger">Error asset subgroup not existing!</div>';
								$this->session->set_flashdata('message', $msg);
								redirect('unit/view-capex/' . encode($ag_trans_id));
							}
						}

						if($this->db->trans_status() === FALSE){
							$this->db->trans_rollback();
							$msg = '<div class="alert alert-danger">Error please try again!</div>';
						}else{
							$this->db->trans_commit();
							$msg = '<div class="alert alert-success">CAPEX successfully added.</strong></div>';
						}

						$this->session->set_flashdata('message', $msg);
						redirect('unit/view-capex/' . encode($ag_trans_id));
					}else{
						$msg = '<div class="alert alert-danger">Error transaction not exist please try again!</div>';
						$this->session->set_flashdata('message', $msg);
						redirect('unit/view-capex/' . encode($ag_trans_id));
					}
				}else{
					$msg = '<div class="alert alert-danger">Error invalid cost center!</div>';	
					$this->session->set_flashdata('message', $msg);
					redirect('unit/view-capex/' . encode($ag_trans_id));
				}
			}else{

			}
		}
	}

	public function capex_donut($id, $year){
		$cost_center = decode($id);
		$designated_tbl = $this->_get_designated_tbl();
		$check_id = $this->admin->check_data('cost_center_tbl', array('cost_center_id' => $cost_center));
		if($check_id == TRUE){
			$donut = $this->admin->get_query('SELECT e.ag_name as asset_group, SUM(b.capex_price * c.capex_qty) as amount, e.ag_color as color FROM '.$designated_tbl->asset_group_transaction_tbl.' a, '.$designated_tbl->asset_group_transaction_item_tbl.' b, '.$designated_tbl->asset_group_transaction_details_tbl.' c, asset_subgroup_tbl d, asset_group_tbl e, cost_center_tbl f, transaction_type_tbl g WHERE a.ag_trans_id=b.ag_trans_id AND b.ag_trans_item_id=c.ag_trans_item_id AND b.asg_id=d.asg_id AND d.ag_id=e.ag_id AND b.cost_center_id=f.cost_center_id AND a.trans_type_id=g.trans_type_id AND f.parent_id=' . $cost_center . ' AND a.ag_trans_status=1 AND b.ag_trans_item_status=1 AND c.ag_trans_det_status=1 AND g.trans_type_id=1 AND a.ag_trans_budget_year = ' . $year . ' GROUP BY d.ag_id ORDER BY amount DESC');
			$data['result'] = 1;
			$data['info'] = $donut;
			
		}else{
			$data['result'] = 0;
		}

		echo json_encode($data);
		exit();
	}

	public function capex_line($id, $year){
		
		$cost_center = decode($id);
		$designated_tbl = $this->_get_designated_tbl();
		$check_id = $this->admin->check_data('cost_center_tbl', array('cost_center_id' => $cost_center));
		if($check_id == TRUE){
			$line = $this->admin->get_query('SELECT DATE_FORMAT(c.capex_budget_date, "%b %Y") as budget_date, SUM(b.capex_price * c.capex_qty) as amount FROM '.$designated_tbl->asset_group_transaction_tbl.' a, '.$designated_tbl->asset_group_transaction_item_tbl.' b, '.$designated_tbl->asset_group_transaction_details_tbl.' c, asset_subgroup_tbl d, asset_group_tbl e, cost_center_tbl f, transaction_type_tbl g WHERE a.ag_trans_id=b.ag_trans_id AND b.ag_trans_item_id=c.ag_trans_item_id AND b.asg_id=d.asg_id AND d.ag_id=e.ag_id AND b.cost_center_id=f.cost_center_id AND a.trans_type_id=g.trans_type_id AND f.parent_id=' . $cost_center . ' AND a.ag_trans_status=1 AND b.ag_trans_item_status=1 AND c.ag_trans_det_status=1 AND g.trans_type_id = 1 AND a.ag_trans_budget_year = ' . $year . ' GROUP BY YEAR(c.capex_budget_date), MONTH(c.capex_budget_date)');

			/*$line2 = $this->admin->get_query('SELECT DATE_FORMAT(c.capex_budget_date, "%b %Y") as budget_date, SUM(b.capex_price * c.capex_qty) as amount FROM asset_group_transaction_tbl a, asset_group_transaction_item_tbl b, asset_group_transaction_details_tbl c, asset_subgroup_tbl d, asset_group_tbl e, cost_center_tbl f, transaction_type_tbl g WHERE a.ag_trans_id=b.ag_trans_id AND b.ag_trans_item_id=c.ag_trans_item_id AND b.asg_id=d.asg_id AND d.ag_id=e.ag_id AND b.cost_center_id=f.cost_center_id AND a.trans_type_id=g.trans_type_id AND f.parent_id=' . $cost_center . ' AND a.ag_trans_status=1 AND b.ag_trans_item_status=1 AND c.ag_trans_det_status=1 AND g.trans_type_id=2 AND YEAR(c.capex_budget_date)=' . $previous_year . ' GROUP BY YEAR(c.capex_budget_date), MONTH(c.capex_budget_date)');*/
			$line2 = '';
			$capex['first_data'] = $line;
			$capex['second_data'] = $line2;

			$data['result'] = 1;
			$data['info'] = $capex;
		}else{
			$data['result'] = 0;
		}

		echo json_encode($data);
		exit();
	}

	public function capex_bar($id, $year){
		
		$cost_center = decode($id);
		$designated_tbl = $this->_get_designated_tbl();
		$check_id = $this->admin->check_data('cost_center_tbl', array('cost_center_id' => $cost_center));
		if($check_id == TRUE){
			$bar = $this->admin->get_query('SELECT DATE_FORMAT(c.capex_budget_date, "%b %Y") as budget_date, e.ag_name as asset_group, SUM(b.capex_price * c.capex_qty) as amount, MONTH(c.capex_budget_date) as month, e.ag_color as color FROM '.$designated_tbl->asset_group_transaction_tbl.' a, '.$designated_tbl->asset_group_transaction_item_tbl.' b, '.$designated_tbl->asset_group_transaction_details_tbl.' c, asset_subgroup_tbl d, asset_group_tbl e, cost_center_tbl f, transaction_type_tbl g WHERE a.ag_trans_id=b.ag_trans_id AND b.ag_trans_item_id=c.ag_trans_item_id AND b.asg_id=d.asg_id AND d.ag_id=e.ag_id AND b.cost_center_id=f.cost_center_id AND a.trans_type_id=g.trans_type_id AND f.parent_id=' . $cost_center . ' AND a.ag_trans_status=1 AND b.ag_trans_item_status=1 AND c.ag_trans_det_status=1 AND a.ag_trans_budget_year = ' . $year . ' GROUP BY YEAR(c.capex_budget_date), MONTH(c.capex_budget_date), e.ag_id ORDER BY c.capex_budget_date ASC, amount DESC');
			$arr_budget_date = array();
			$arr_group = array();
			$arr_asset = array();
			$arr_color = array();
			$count = 0;
			foreach($bar as $row){
				$budget_date = $row->budget_date;
				$month = $row->month;
				$amount = $row->amount;
				$asset_group = $row->asset_group;
				$color = $row->color;
				if(!array_key_exists($month, $arr_budget_date)){
					$arr_budget_date[$month] = $budget_date;
				}

				if(!array_key_exists($asset_group, $arr_asset)){
					$arr_asset[$asset_group]['asset'] = $asset_group;
					$arr_asset[$asset_group]['amount'] = array();

					$arr_group[$asset_group] = $asset_group;
					$count++;
				}

				array_push($arr_asset[$asset_group]['amount'], $amount);

				if(!array_key_exists($color, $arr_color)){
					$arr_color[$color] = $color;
				}
			}

			$data['result'] = 1;
			$data['month'] = $arr_budget_date;
			$data['group'] = $arr_group;
			$data['group_amount'] = $arr_asset;
			$data['color'] = $arr_color;
		}else{
			$data['result'] = 0;
		}
		echo json_encode($data);
		exit();
	}

	public function transac_capex($year){
		$info = $this->_require_login();
		$data['title'] = 'Add capex';
		$user_info = $this->get_user_info();
		$bc_id = $user_info['cost_center_id'];
		$cost_center_code = $user_info['cost_center_code'];
		$designated_tbl = $this->_get_designated_tbl();

		$module = 'CAPEX';
		if($year == null){
			$year = $this->_active_year();
		}

		$data['year'] = $year;

		$budget_status = $this->check_module($module, $year);
		if($budget_status == 1){

			$check_id = $this->admin->check_data('cost_center_tbl', array('cost_center_code' => $cost_center_code, 'cost_center_status' => 1), TRUE);
			if($check_id['result'] == TRUE){
				$data['id'] = encode($cost_center_code);
				$cost_center_id = $check_id['info']->cost_center_id;
				
				$data['cost_center_name'] = $check_id['info']->cost_center_desc;
				$data['asset_group'] = $this->admin->get_query('SELECT * FROM asset_group_tbl as a WHERE a.ag_id NOT IN (SELECT b.ag_id FROM '.$designated_tbl->asset_group_transaction_tbl.' b, cost_center_tbl c WHERE b.cost_center_id=c.cost_center_id AND b.ag_trans_status=1 AND c.cost_center_id = ' . $cost_center_id . ' AND b.ag_trans_budget_year = ' . $year . ') AND a.ag_name != "STORE EQUIPMENT"');
				$data['content'] = $this->load->view('unit/unit_transac_capex', $data , TRUE);
				$this->load->view('unit/templates', $data);
			}
		}else{
			redirect($_SERVER['HTTP_REFERER']);
		}
	}

	public function get_subgroup(){
		$info = $this->_require_login();
		if($_SERVER['REQUEST_METHOD'] == 'POST'){
			$ag_id =clean_data(decode($this->input->post('id')));
			$cost_center =clean_data(decode($this->input->post('cost_center')));
			$check_ag = $this->admin->check_data('asset_group_tbl', array('ag_id' => $ag_id), TRUE);
			if($check_ag['result'] == TRUE){
				$data['name'] = $check_ag['info']->ag_name;
				$ag_name = strtoupper($check_ag['info']->ag_name);
				$check_cost_center = $this->admin->check_data('cost_center_tbl', array('cost_center_code' => $cost_center, 'cost_center_status' => 1), TRUE);

				if($check_cost_center['result'] == TRUE){
					$cost_center_id = $check_cost_center['info']->cost_center_id;

					$asset = $this->_get_ag_data($cost_center_id, $ag_name, $ag_id);

					if($ag_name == 'TRANSPORTATION EQUIPMENT'){
						$get_asset = $this->_get_transpo_equip_data($cost_center_id, $ag_name, $ag_id);

						$data['assets'] = $get_asset['asset'];
						$data['header'] = $get_asset['header'];
					}else{
						$get_asset = $this->_get_ag_data($cost_center_id, $ag_name, $ag_id);

						$data['assets'] = $get_asset['asset'];
						$data['header'] = $get_asset['header'];
					}

					$data['result'] = 1;
				}else{
					$data[''] = 0;
				}
				
			}else{
				$data['result'] = 0;
			}
		}else{
			$data['result'] = 0;
		}

		echo json_encode($data);
	}

	public function _get_ag_data($cost_center_id, $ag_name, $ag_id){
		if($ag_name == 'STORE EQUIPMENT' || $ag_name == 'KITCHEN EQUIPMENT'){
			$get_cost_center = $this->admin->get_data('cost_center_tbl', array('parent_id' => $cost_center_id, 'cost_center_type_id' => 8 , 'cost_center_status' => 1));
		}elseif($ag_name == 'LEASEHOLD IMPROVEMENTS' || $ag_name == 'COMPUTER EQUIPMENT & PARAPHERNALIA'){
			$get_cost_center = $this->admin->get_data('cost_center_tbl', array('parent_id' => $cost_center_id, 'cost_center_status' => 1));
		}else{
			$get_cost_center = $this->admin->get_data('cost_center_tbl', array('parent_id' => $cost_center_id, 'cost_center_type_id !=' => 8, 'cost_center_status' => 1));
		}
		$cost_center_data = '<select name="cost_center[]" class="form-control input-sm capex-cost-center" style="width: 200px;">';
		$cost_center_data .= '<option value="">Select cost center...</option>';
		foreach($get_cost_center as $row){
			$cost_center_data .= '<option value="' . encode($row->cost_center_id) . '">' . $row->cost_center_desc . '</option>';
		}

		$cost_center_data .= '</select>';
				
		$get_capex_type = $this->admin->get_data('capex_type_tbl', array('capex_type_status' => 1));
		$get_capex_category = $this->admin->get_data('capex_category_tbl', array('capex_category_status' => 1));

		$capex_type_data = '<select name="capex_type[]" class="form-control input-sm capex-type" style="width: 200px;">';
		$capex_type_data .= '<option value="">Select capex type...</option>';
		foreach($get_capex_type as $row){
			$capex_type_data .= '<option value="' . encode($row->capex_type_id) . '">' . $row->capex_type_name . '</option>';
		}
		$capex_type_data .= '</select>';

		$capex_category_data = '<select name="capex_category[]" class="form-control input-sm capex-category" style="width: 200px;">';
		$capex_category_data .= '<option value="">Select capex category...</option>';
		foreach($get_capex_category as $row){
			$capex_category_data .= '<option value="' . encode($row->capex_category_id) . '">' . $row->capex_category_name . '</option>';
		}
		$capex_category_data .= '</select>';

		$get_assets = $this->admin->get_data('asset_subgroup_tbl', array('ag_id' => $ag_id, 'asg_status' => 1));
		$asset = '';
		foreach($get_assets as $row){
			$asset .= '<tr><input type="hidden" name="id[]" value="' . encode($row->asg_id) . '"> <input type="hidden" class="asg-price" value="' . $row->asg_price . '">';
			$asset .= '<td width=""><a href="#" class="remove-asg"><i class="fa fa-remove"></i></a>&nbsp;&nbsp;&nbsp;<a href="" class="add-asset-sub" data-id="' . encode($row->asg_id) . '"><i class="fa fa-plus"></i></a>&nbsp;&nbsp;<a href="#" class="show-slider-capex"><span class="fa fa-sliders"></span></a></td>';
			$asset .= '<td style="width:20px;">' . $row->asg_name .'</td>';
			$asset .= '<td width="7%"><div class="form-group">' . $cost_center_data . '</div></td>';
			$asset .= '<td width="7%"><div class="form-group">' . $capex_type_data . '</div></td>';
			$asset .= '<td width="7%"><div class="form-group">' . $capex_category_data . '</div></td>';
			$asset .= '<td class="text-right" width="3%">' . number_format($row->asg_price, 2) . '</td>';
			$asset .= '<td class="text-right" width="3%"><label class="capex-total-price">0</label></td>';
			$asset .= '<td class="text-right" width="3%"><label class="capex-total-qty">0</label></td>';
			$asset .= '<td class="text-center" width=""><input type="text" class="capex-qty jan-qty" name="capex[jan][]" class="form-control input-sm"></td>';
			$asset .= '<td class="text-center" width=""><input type="text" class="capex-qty feb-qty" name="capex[feb][]" class="form-control input-sm"></td>';
			$asset .= '<td class="text-center" width=""><input type="text" class="capex-qty mar-qty" name="capex[mar][]" class="form-control input-sm"></td>';
			$asset .= '<td class="text-center" width=""><input type="text" class="capex-qty apr-qty" name="capex[apr][]" class="form-control input-sm"></td>';
			$asset .= '<td class="text-center" width=""><input type="text" class="capex-qty may-qty" name="capex[may][]" class="form-control input-sm"></td>';
			$asset .= '<td class="text-center" width=""><input type="text" class="capex-qty jun-qty" name="capex[jun][]" class="form-control input-sm"></td>';
			$asset .= '<td class="text-center" width=""><input type="text" class="capex-qty jul-qty" name="capex[jul][]" class="form-control input-sm"></td>';
			$asset .= '<td class="text-center" width=""><input type="text" class="capex-qty aug-qty" name="capex[aug][]" class="form-control input-sm"></td>';
			$asset .= '<td class="text-center" width=""><input type="text" class="capex-qty sep-qty" name="capex[sep][]" class="form-control input-sm"></td>';
			$asset .= '<td class="text-center" width=""><input type="text" class="capex-qty oct-qty" name="capex[oct][]" class="form-control input-sm"></td>';
			$asset .= '<td class="text-center" width=""><input type="text" class="capex-qty nov-qty" name="capex[nov][]" class="form-control input-sm"></td>';
			$asset .= '<td class="text-center" width=""><input type="text" class="capex-qty dec-qty" name="capex[dec][]" class="form-control input-sm"></td>';
			$asset .= '</tr>';
		}

		$table_head = '
			<tr>
				<th class="text-center" style="background: #03A9F4;color: #fff;"></th>
				<th style="background: #03A9F4;color: #fff; width:20px;">Asset</th>
				<th width="7%" style="background: #03A9F4;color: #fff;">Cost Center</th>
				<th width="7%" style="background: #03A9F4;color: #fff;">Type of CAPEX</th>
				<th width="7%" style="background: #03A9F4;color: #fff;">Maintenance Category</th>
				<th class="text-center" width="3%" style="background: #03A9F4;color: #fff;">Price</th>
				<th class="text-center" width="3%">Total Price</th>
				<th class="text-center" width="3%">Total Qty</th>
				<th class="text-center" width="">Jan</th>
				<th class="text-center" width="">Feb</th>
				<th class="text-center" width="">Mar</th>
				<th class="text-center" width="">Apr</th>
				<th class="text-center" width="">May</th>
				<th class="text-center" width="">Jun</th>
				<th class="text-center" width="">Jul</th>
				<th class="text-center" width="">Aug</th>
				<th class="text-center" width="">Sep</th>
				<th class="text-center" width="">Oct</th>
				<th class="text-center" width="">Nov</th>
				<th class="text-center" width="">Dec</th>
			</tr>';

		$data['asset'] = $asset;
		$data['header'] = $table_head;
		return $data;
	}

	public function _get_transpo_equip_data($cost_center_id, $ag_name, $ag_id){
		
		$get_cost_center = $this->admin->get_data('cost_center_tbl', array('parent_id' => $cost_center_id, 'cost_center_type_id !=' => 8, 'cost_center_status' => 1));
		$cost_center_data = '<select name="cost_center[]" class="form-control input-sm capex-cost-center" style="width: 200px;">';
		$cost_center_data .= '<option value="">Select cost center...</option>';
		foreach($get_cost_center as $row){
			$cost_center_data .= '<option value="' . encode($row->cost_center_id) . '">' . $row->cost_center_desc . '</option>';
		}
		$cost_center_data .= '</select>';
		
		$get_capex_type = $this->admin->get_data('capex_type_tbl', array('capex_type_status' => 1));
		$get_capex_category = $this->admin->get_data('capex_category_tbl', array('capex_category_status' => 1));

		$capex_type_data = '<select name="capex_type[]" class="form-control input-sm capex-type" style="width: 200px;">';
		$capex_type_data .= '<option value="">Select capex type...</option>';
		foreach($get_capex_type as $row){
			$capex_type_data .= '<option value="' . encode($row->capex_type_id) . '">' . $row->capex_type_name . '</option>';
		}
		$capex_type_data .= '</select>';

		$capex_category_data = '<select name="capex_category[]" class="form-control input-sm capex-category" style="width: 200px;">';
		$capex_category_data .= '<option value="">Select capex category...</option>';
		foreach($get_capex_category as $row){
			$capex_category_data .= '<option value="' . encode($row->capex_category_id) . '">' . $row->capex_category_name . '</option>';
		}
		$capex_category_data .= '</select>';

		$get_rank = $this->admin->get_data('rank_tbl', array('rank_status' => 1));
		$rank_data = '<select name="rank[]" class="form-control input-sm capex-rank" required>';
		$rank_data .= '<option value="">Select Rank...</option>';
		foreach($get_rank as $row_rank){
			$rank_data .= '<option value="' . encode($row_rank->rank_id) . '">' . $row_rank->rank_name . '</option>';
		}

		$rank_data .= '</select>';		

		$get_assets = $this->admin->get_data('asset_subgroup_tbl', array('ag_id' => $ag_id, 'asg_status' => 1));
		$asset = '';
		foreach($get_assets as $row){
			$asset .= '<tr><input type="hidden" name="id[]" value="' . encode($row->asg_id) . '"> <input type="hidden" class="asg-price" value="' . $row->asg_price . '">';
			$asset .= '<td width=""><a href="#" class="remove-asg"><i class="fa fa-remove"></i></a>&nbsp;&nbsp;&nbsp;<a href="" class="add-asset-sub" data-id="' . encode($row->asg_id) . '"><i class="fa fa-plus"></i></a>&nbsp;&nbsp;<a href="#" class="show-slider-capex"><span class="fa fa-sliders"></span></a></td>';
			$asset .= '<td style="width:20px;">' . $row->asg_name .'</td>';
			$asset .= '<td width="7%"><div class="form-group">' . $cost_center_data . '</div></td>';
			$asset .= '<td width="7%"><div class="form-group">' . $capex_type_data . '</div></td>';
			$asset .= '<td width="7%"><div class="form-group">' . $capex_category_data . '</div></td>';
			$asset .= '<td class="text-right" width="3%">' . number_format($row->asg_price, 2) . '</td>';
			$asset .= '<td class="text-right" width="3%"><label class="capex-total-price">0</label></td>';
			$asset .= '<td class="text-right" width="3%"><label class="capex-total-qty">0</label></td>';
			$asset .= '<td class="text-center" width=""><input type="text" class="capex-qty jan-qty" name="capex[jan][]" class="form-control input-sm"></td>';
			$asset .= '<td class="text-center" width=""><input type="text" class="capex-qty feb-qty" name="capex[feb][]" class="form-control input-sm"></td>';
			$asset .= '<td class="text-center" width=""><input type="text" class="capex-qty mar-qty" name="capex[mar][]" class="form-control input-sm"></td>';
			$asset .= '<td class="text-center" width=""><input type="text" class="capex-qty apr-qty" name="capex[apr][]" class="form-control input-sm"></td>';
			$asset .= '<td class="text-center" width=""><input type="text" class="capex-qty may-qty" name="capex[may][]" class="form-control input-sm"></td>';
			$asset .= '<td class="text-center" width=""><input type="text" class="capex-qty jun-qty" name="capex[jun][]" class="form-control input-sm"></td>';
			$asset .= '<td class="text-center" width=""><input type="text" class="capex-qty jul-qty" name="capex[jul][]" class="form-control input-sm"></td>';
			$asset .= '<td class="text-center" width=""><input type="text" class="capex-qty aug-qty" name="capex[aug][]" class="form-control input-sm"></td>';
			$asset .= '<td class="text-center" width=""><input type="text" class="capex-qty sep-qty" name="capex[sep][]" class="form-control input-sm"></td>';
			$asset .= '<td class="text-center" width=""><input type="text" class="capex-qty oct-qty" name="capex[oct][]" class="form-control input-sm"></td>';
			$asset .= '<td class="text-center" width=""><input type="text" class="capex-qty nov-qty" name="capex[nov][]" class="form-control input-sm"></td>';
			$asset .= '<td class="text-center" width=""><input type="text" class="capex-qty dec-qty" name="capex[dec][]" class="form-control input-sm"></td>';
			$asset .= '<td class="text-center" width="">' . $rank_data . '</td>';
			$asset .= '<td class="text-center" style="width:200px;"><input type="text" class="capex-remarks" name="remarks[]" class="form-control input-sm" style="width:200px;"></td>';
			$asset .= '</tr>';
		}

		$table_head = '
			<tr>
				<th class="text-center" style="background: #03A9F4;color: #fff;"></th>
				<th style="background: #03A9F4;color: #fff; width:20px;">Asset</th>
				<th width="7%" style="background: #03A9F4;color: #fff;">Cost Center</th>
				<th width="7%" style="background: #03A9F4;color: #fff;">Type of CAPEX</th>
				<th width="7%" style="background: #03A9F4;color: #fff;">Maintenance Category</th>
				<th class="text-center" width="3%" style="background: #03A9F4;color: #fff;">Price</th>
				<th class="text-center" width="3%">Total Price</th>
				<th class="text-center" width="3%">Total Qty</th>
				<th class="text-center" width="">Jan</th>
				<th class="text-center" width="">Feb</th>
				<th class="text-center" width="">Mar</th>
				<th class="text-center" width="">Apr</th>
				<th class="text-center" width="">May</th>
				<th class="text-center" width="">Jun</th>
				<th class="text-center" width="">Jul</th>
				<th class="text-center" width="">Aug</th>
				<th class="text-center" width="">Sep</th>
				<th class="text-center" width="">Oct</th>
				<th class="text-center" width="">Nov</th>
				<th class="text-center" width="">Dec</th>
				<th class="text-center" width="">Rank</th>
				<th class="text-center" width="">Remarks</th>
			</tr>'
		;
		$data['asset'] = $asset;
		$data['header'] = $table_head;
		return $data;
	}

	public function get_asset_subgroup(){
		$info = $this->_require_login();
		$user_id = decode($info['user_id']);
		$asg_id =clean_data(decode($this->input->post('id')));
		$cost_center =clean_data(decode($this->input->post('cost_center')));

		$check_cost_center = $this->admin->check_data('cost_center_tbl', array('cost_center_code' => $cost_center, 'cost_center_status' => 1), TRUE);
		$join_ag = array('asset_group_tbl b' => 'a.ag_id = b.ag_id AND b.ag_status = 1 AND a.asg_status = 1 AND a.asg_id = ' . $asg_id);
		$check_ag = $this->admin->check_join('asset_subgroup_tbl a', $join_ag, TRUE);
		if($check_ag['result'] == TRUE){
			
			$ag_name = strtoupper($check_ag['info']->ag_name);
			if($check_cost_center['result'] == TRUE){
				$cost_center_id = $check_cost_center['info']->cost_center_id;
				if($ag_name == 'STORE EQUIPMENT' || $ag_name == 'KITCHEN EQUIPMENT' || $ag_name == 'LEASEHOLD IMPROVEMENTS'){
					$get_cost_center = $this->admin->get_data('cost_center_tbl', array('parent_id' => $cost_center_id, 'cost_center_type_id' => 8 , 'cost_center_status' => 1));
				}else{
					$get_cost_center = $this->admin->get_data('cost_center_tbl', array('parent_id' => $cost_center_id, 'cost_center_type_id !=' => 8, 'cost_center_status' => 1));
				}

				$cost_center_data = '<select name="cost_center[]" class="form-control input-sm capex-cost-center" style="width: 200px;">';
				$cost_center_data .= '<option value="">Select cost center...</option>';
				foreach($get_cost_center as $row){
					$cost_center_data .= '<option value="' . encode($row->cost_center_id) . '">' . $row->cost_center_desc . '</option>';
				}
				$cost_center_data .= '</select>';
				
				$get_capex_type = $this->admin->get_data('capex_type_tbl', array('capex_type_status' => 1));
				$get_capex_category = $this->admin->get_data('capex_category_tbl', array('capex_category_status' => 1));

				$capex_type_data = '<select name="capex_type[]" class="form-control input-sm capex-type" style="width: 200px;">';
				$capex_type_data .= '<option value="">Select capex type...</option>';
				foreach($get_capex_type as $row){
					$capex_type_data .= '<option value="' . encode($row->capex_type_id) . '">' . $row->capex_type_name . '</option>';
				}
				$capex_type_data .= '</select>';

				$capex_category_data = '<select name="capex_category[]" class="form-control input-sm capex-category" style="width: 200px;">';
				$capex_category_data .= '<option value="">Select capex category...</option>';
				foreach($get_capex_category as $row){
					$capex_category_data .= '<option value="' . encode($row->capex_category_id) . '">' . $row->capex_category_name . '</option>';
				}
				$capex_category_data .= '</select>';

				$get_assets = $this->admin->get_data('asset_subgroup_tbl', array('asg_id' => $asg_id, 'asg_status' => 1));
				$asset = '';
				
				if($ag_name == 'TRANSPORTATION EQUIPMENT'){
					$get_rank = $this->admin->get_data('rank_tbl', array('rank_status' => 1));
					$rank_data = '<select name="rank[]" class="form-control input-sm capex-rank" required>';
					$rank_data .= '<option value="">Select Rank...</option>';
					foreach($get_rank as $row_rank){
						$rank_data .= '<option value="' . encode($row_rank->rank_id) . '">' . $row_rank->rank_name . '</option>';
					}

					$rank_data .= '</select>';	
					foreach($get_assets as $row){
						$asset .= '<tr><input type="hidden" name="id[]" value="' . encode($row->asg_id) . '"> <input type="hidden" class="asg-price" value="' . $row->asg_price . '">';
						$asset .= '<td><a href="#" class="remove-asg"><i class="fa fa-remove"></i></a>&nbsp;&nbsp;&nbsp;<a href="" class="add-asset-sub" data-id="' . encode($row->asg_id) . '"><i class="fa fa-plus"></i></a>&nbsp;&nbsp;<a href="#" class="show-slider-capex"><span class="fa fa-sliders"></span></a></td>';
						$asset .= '<td style="width:20px;">' . $row->asg_name .'</td>';
						$asset .= '<td width="7%">' . $cost_center_data  . '</td>';
						$asset .= '<td width="7%"><div class="form-group">' . $capex_type_data . '</div></td>';
						$asset .= '<td width="7%"><div class="form-group">' . $capex_category_data . '</div></td>';
						$asset .= '<td class="text-right" width="3%">' . number_format($row->asg_price, 2) . '</td>';
						$asset .= '<td class="text-right" width="3%"><label class="capex-total-price">0</label></td>';
						$asset .= '<td class="text-right" width="3%"><label class="capex-total-qty">0</label></td>';
						$asset .= '<td  class="text-center" width=""><input type="text" class="capex-qty jan-qty" name="capex[jan][]" class="form-control input-sm"></td>';
						$asset .= '<td class="text-center" width=""><input type="text" class="capex-qty feb-qty" name="capex[feb][]" class="form-control input-sm"></td>';
						$asset .= '<td class="text-center" width=""><input type="text" class="capex-qty mar-qty" name="capex[mar][]" class="form-control input-sm"></td>';
						$asset .= '<td class="text-center" width=""><input type="text" class="capex-qty apr-qty" name="capex[apr][]" class="form-control input-sm"></td>';
						$asset .= '<td class="text-center" width=""><input type="text" class="capex-qty may-qty" name="capex[may][]" class="form-control input-sm"></td>';
						$asset .= '<td class="text-center" width=""><input type="text" class="capex-qty jun-qty" name="capex[jun][]" class="form-control input-sm"></td>';
						$asset .= '<td class="text-center" width=""><input type="text" class="capex-qty jul-qty" name="capex[jul][]" class="form-control input-sm"></td>';
						$asset .= '<td class="text-center" width=""><input type="text" class="capex-qty aug-qty" name="capex[aug][]" class="form-control input-sm"></td>';
						$asset .= '<td class="text-center" width=""><input type="text" class="capex-qty sep-qty" name="capex[sep][]" class="form-control input-sm"></td>';
						$asset .= '<td class="text-center" width=""><input type="text" class="capex-qty oct-qty" name="capex[oct][]" class="form-control input-sm"></td>';
						$asset .= '<td class="text-center" width=""><input type="text" class="capex-qty nov-qty" name="capex[nov][]" class="form-control input-sm"></td>';
						$asset .= '<td class="text-center" width=""><input type="text" class="capex-qty dec-qty" name="capex[dec][]" class="form-control input-sm"></td>';
						$asset .= '<td class="text-center" width="">' . $rank_data . '</td>';
						$asset .= '<td class="text-center" style="width:200px;"><input type="text" class="capex-remarks" name="remarks[]" class="form-control input-sm" style="width:200px;"></td>';
						$asset .= '</tr>';
					}
				}else{

					foreach($get_assets as $row){
						$asset .= '<tr><input type="hidden" name="id[]" value="' . encode($row->asg_id) . '"> <input type="hidden" class="asg-price" value="' . $row->asg_price . '">';
						$asset .= '<td><a href="#" class="remove-asg"><i class="fa fa-remove"></i></a>&nbsp;&nbsp;&nbsp;<a href="" class="add-asset-sub" data-id="' . encode($row->asg_id) . '"><i class="fa fa-plus"></i></a>&nbsp;&nbsp;<a href="#" class="show-slider-capex"><span class="fa fa-sliders"></span></a></td>';
						$asset .= '<td style="width:20px;">' . $row->asg_name .'</td>';
						$asset .= '<td width="7%">' . $cost_center_data  . '</td>';
						$asset .= '<td width="7%"><div class="form-group">' . $capex_type_data . '</div></td>';
						$asset .= '<td width="7%"><div class="form-group">' . $capex_category_data . '</div></td>';
						$asset .= '<td class="text-right" width="3%">' . number_format($row->asg_price, 2) . '</td>';
						$asset .= '<td class="text-right" width="3%"><label class="capex-total-price">0</label></td>';
						$asset .= '<td class="text-right" width="3%"><label class="capex-total-qty">0</label></td>';
						$asset .= '<td  class="text-center" width=""><input type="text" class="capex-qty jan-qty" name="capex[jan][]" class="form-control input-sm"></td>';
						$asset .= '<td class="text-center" width=""><input type="text" class="capex-qty feb-qty" name="capex[feb][]" class="form-control input-sm"></td>';
						$asset .= '<td class="text-center" width=""><input type="text" class="capex-qty mar-qty" name="capex[mar][]" class="form-control input-sm"></td>';
						$asset .= '<td class="text-center" width=""><input type="text" class="capex-qty apr-qty" name="capex[apr][]" class="form-control input-sm"></td>';
						$asset .= '<td class="text-center" width=""><input type="text" class="capex-qty may-qty" name="capex[may][]" class="form-control input-sm"></td>';
						$asset .= '<td class="text-center" width=""><input type="text" class="capex-qty jun-qty" name="capex[jun][]" class="form-control input-sm"></td>';
						$asset .= '<td class="text-center" width=""><input type="text" class="capex-qty jul-qty" name="capex[jul][]" class="form-control input-sm"></td>';
						$asset .= '<td class="text-center" width=""><input type="text" class="capex-qty aug-qty" name="capex[aug][]" class="form-control input-sm"></td>';
						$asset .= '<td class="text-center" width=""><input type="text" class="capex-qty sep-qty" name="capex[sep][]" class="form-control input-sm"></td>';
						$asset .= '<td class="text-center" width=""><input type="text" class="capex-qty oct-qty" name="capex[oct][]" class="form-control input-sm"></td>';
						$asset .= '<td class="text-center" width=""><input type="text" class="capex-qty nov-qty" name="capex[nov][]" class="form-control input-sm"></td>';
						$asset .= '<td class="text-center" width=""><input type="text" class="capex-qty dec-qty" name="capex[dec][]" class="form-control input-sm"></td>';
						$asset .= '</tr>';
					}
				}

				$table_head = '
					<tr>
						<th class="text-center" style="background: #03A9F4;color: #fff;"></th>
						<th style="background: #03A9F4;color: #fff; width:20px;">Asset</th>
						<th width="7%" style="background: #03A9F4;color: #fff;">Cost Center</th>
						<th width="7%" style="background: #03A9F4;color: #fff;">Type of CAPEX</th>
						<th width="7%" style="background: #03A9F4;color: #fff;">Maintenance Category</th>
						<th class="text-center" width="3%" style="background: #03A9F4;color: #fff;">Price</th>
						<th class="text-center" width="3%">Total Price</th>
						<th class="text-center" width="3%">Total Qty</th>
						<th class="text-center" width="">Jan</th>
						<th class="text-center" width="">Feb</th>
						<th class="text-center" width="">Mar</th>
						<th class="text-center" width="">Apr</th>
						<th class="text-center" width="">May</th>
						<th class="text-center" width="">Jun</th>
						<th class="text-center" width="">Jul</th>
						<th class="text-center" width="">Aug</th>
						<th class="text-center" width="">Sep</th>
						<th class="text-center" width="">Oct</th>
						<th class="text-center" width="">Nov</th>
						<th class="text-center" width="">Dec</th>
					</tr>';

				$data['asset'] = $asset;
				$data['header'] = $table_head;
				$data['result'] = 1;
				return $data;
				
			}else{
				$data['result'] = 0;
			}
		}else{
			$data['result'] = 0;
		}
		
		echo json_encode($data);
	}

	public function add_capex(){
		$info = $this->_require_login();
		$user_id = decode($info['user_id']);

		$user_info = $this->get_user_info();
		$bc_id = $user_info['cost_center_id'];
		$cost_center_code = $user_info['cost_center_code'];
		$designated_tbl = $this->_get_designated_tbl();


		if($_SERVER['REQUEST_METHOD'] == 'POST'){
			$asset_group = clean_data(decode($this->input->post('asset_group')));
			$year = clean_data($this->input->post('year'));
			$remarks = clean_data($this->input->post('remarks'));
			$rank = clean_data($this->input->post('rank'));

			if(!empty($asset_group) && !empty($cost_center_code) && !empty($year)){
				$check_cc = $this->admin->check_data('cost_center_tbl', array('cost_center_code' => $cost_center_code, 'cost_center_status' => 1), TRUE);
				if($check_cc['result'] == TRUE){
					$cost_center_id = $check_cc['info']->cost_center_id;
					$check_duplicate = $this->admin->check_data($designated_tbl->asset_group_transaction_tbl, array('ag_id' => $asset_group, 'cost_center_id' => $cost_center_id, 'ag_trans_status' => 1, 'trans_type_id' => 1, 'ag_trans_budget_year' => $year));
					
					if($check_duplicate == FALSE){
						$check_ag = $this->admin->check_data('asset_group_tbl', array('ag_id' => $asset_group, 'ag_status' => 1));
						if($check_ag == TRUE){
							$set = array(
								'ag_id' => $asset_group,
								'cost_center_id' => $cost_center_id,
								'user_id' => $user_id,
								'trans_type_id' => 1,
								'ag_trans_budget_year' => $year,
								'ag_trans_added' => date_now(),
								'ag_trans_status' => 1
							);
							$this->db->trans_start();
							$insert_capex_trans = $this->admin->insert_data($designated_tbl->asset_group_transaction_tbl, $set, TRUE);
							$ag_trans_id = $insert_capex_trans['id'];
							if($insert_capex_trans == TRUE){
								$id = clean_data($this->input->post('id'));
								$asg_cost_center = clean_data($this->input->post('cost_center'));
								$capex_type = clean_data($this->input->post('capex_type'));
								$capex_category = clean_data($this->input->post('capex_category'));
								$capex = clean_data($this->input->post('capex'));
								$count = 0;
								foreach($id as $row){
									$asg_id = decode($row);
									$capex_remarks = '';
									if(is_array($remarks)){
										$capex_remarks = $remarks[$count];
										$rank_id = decode($rank[$count]);
									}

									$check_asg_id = $this->admin->check_data('asset_subgroup_tbl', array('asg_id' => $asg_id, 'asg_status' => 1), TRUE);
									if($check_asg_id == TRUE){
										$asset_price = $check_asg_id['info']->asg_price;
										$asset_lifespan = $check_asg_id['info']->asg_lifespan;
										$capex_type_id = decode($capex_type[$count]);
										$capex_category_id = decode($capex_category[$count]);
										$asg_cost_center_id = decode($asg_cost_center[$count]);
										$check_asg_cc = $this->admin->check_data('cost_center_tbl', array('cost_center_id' => $asg_cost_center_id));
										if($check_asg_cc == TRUE){

											$qty_jan = check_num($capex['jan'][$count]);
											$qty_feb = check_num($capex['feb'][$count]);
											$qty_mar = check_num($capex['mar'][$count]);
											$qty_apr = check_num($capex['apr'][$count]);
											$qty_may = check_num($capex['may'][$count]);
											$qty_jun = check_num($capex['jun'][$count]);
											$qty_jul = check_num($capex['jul'][$count]);
											$qty_aug = check_num($capex['aug'][$count]);
											$qty_sep = check_num($capex['sep'][$count]);
											$qty_oct = check_num($capex['oct'][$count]);
											$qty_nov = check_num($capex['nov'][$count]);
											$qty_dec = check_num($capex['dec'][$count]);

											$total_amount = $qty_jan + $qty_feb + $qty_mar + $qty_apr + $qty_may + $qty_jun + $qty_jul + $qty_aug + $qty_sep + $qty_oct + $qty_nov + $qty_dec;

											if($total_amount > 0){
												$set_item = array(
													'ag_trans_id' => $ag_trans_id,
													'asg_id' => $asg_id,
													'cost_center_id' => $asg_cost_center_id,
													'capex_type_id' => $capex_type_id,
													'capex_category_id' => $capex_category_id,
													'user_id' => $user_id,
													'capex_price' => $asset_price,
													'capex_lifespan' => $asset_lifespan,
													'capex_remarks' => $capex_remarks,
													'ag_trans_item_added' => date_now(),
													'ag_trans_item_status' => 1
												);

												$insert_item = $this->admin->insert_data($designated_tbl->asset_group_transaction_item_tbl, $set_item, TRUE);

												if($insert_item['result'] == TRUE){
													$ag_trans_item_id = $insert_item['id'];

													
													$date = $year . '-' . '01-01';
													$set_asg_trans = array(
														'ag_trans_item_id' => $ag_trans_item_id,
														'capex_qty' => $qty_jan,
														'capex_budget_date' => $date,
														'ag_trans_det_added' => date_now(),
														'ag_trans_det_status' => 1
													);
													$this->admin->insert_data($designated_tbl->asset_group_transaction_details_tbl, $set_asg_trans);

													
													$date = $year . '-' . '02-01';
													$set_asg_trans = array(
														'ag_trans_item_id' => $ag_trans_item_id,
														'capex_qty' => $qty_feb,
														'capex_budget_date' => $date,
														'ag_trans_det_added' => date_now(),
														'ag_trans_det_status' => 1
													);
													$this->admin->insert_data($designated_tbl->asset_group_transaction_details_tbl, $set_asg_trans);

													
													$date = $year . '-' . '03-01';
													$set_asg_trans = array(
														'ag_trans_item_id' => $ag_trans_item_id,
														'capex_qty' => $qty_mar,
														'capex_budget_date' => $date,
														'ag_trans_det_added' => date_now(),
														'ag_trans_det_status' => 1
													);
													$this->admin->insert_data($designated_tbl->asset_group_transaction_details_tbl, $set_asg_trans);

													
													$date = $year . '-' . '04-01';
													$set_asg_trans = array(
														'ag_trans_item_id' => $ag_trans_item_id,
														'capex_qty' => $qty_apr,
														'capex_budget_date' => $date,
														'ag_trans_det_added' => date_now(),
														'ag_trans_det_status' => 1
													);
													$this->admin->insert_data($designated_tbl->asset_group_transaction_details_tbl, $set_asg_trans);
													
													
													$date = $year . '-' . '05-01';
													$set_asg_trans = array(
														'ag_trans_item_id' => $ag_trans_item_id,
														'capex_qty' => $qty_may,
														'capex_budget_date' => $date,
														'ag_trans_det_added' => date_now(),
														'ag_trans_det_status' => 1
													);
													$this->admin->insert_data($designated_tbl->asset_group_transaction_details_tbl, $set_asg_trans);

													
													$date = $year . '-' . '06-01';
													$set_asg_trans = array(
														'ag_trans_item_id' => $ag_trans_item_id,
														'capex_qty' => $qty_jun,
														'capex_budget_date' => $date,
														'ag_trans_det_added' => date_now(),
														'ag_trans_det_status' => 1
													);
													$this->admin->insert_data($designated_tbl->asset_group_transaction_details_tbl, $set_asg_trans);

													$date = $year . '-' . '07-01';
													$set_asg_trans = array(
														'ag_trans_item_id' => $ag_trans_item_id,
														'capex_qty' => $qty_jul,
														'capex_budget_date' => $date,
														'ag_trans_det_added' => date_now(),
														'ag_trans_det_status' => 1
													);
													$this->admin->insert_data($designated_tbl->asset_group_transaction_details_tbl, $set_asg_trans);

													
													$date = $year . '-' . '08-01';
													$set_asg_trans = array(
														'ag_trans_item_id' => $ag_trans_item_id,
														'capex_qty' => $qty_aug,
														'capex_budget_date' => $date,
														'ag_trans_det_added' => date_now(),
														'ag_trans_det_status' => 1
													);
													$this->admin->insert_data($designated_tbl->asset_group_transaction_details_tbl, $set_asg_trans);

													
													$date = $year . '-' . '09-01';
													$set_asg_trans = array(
														'ag_trans_item_id' => $ag_trans_item_id,
														'capex_qty' => $qty_sep,
														'capex_budget_date' => $date,
														'ag_trans_det_added' => date_now(),
														'ag_trans_det_status' => 1
													);
													$this->admin->insert_data($designated_tbl->asset_group_transaction_details_tbl, $set_asg_trans);

													
													$date = $year . '-' . '10-01';
													$set_asg_trans = array(
														'ag_trans_item_id' => $ag_trans_item_id,
														'capex_qty' => $qty_oct,
														'capex_budget_date' => $date,
														'ag_trans_det_added' => date_now(),
														'ag_trans_det_status' => 1
													);
													$this->admin->insert_data($designated_tbl->asset_group_transaction_details_tbl, $set_asg_trans);

													
													$date = $year . '-' . '11-01';
													$set_asg_trans = array(
														'ag_trans_item_id' => $ag_trans_item_id,
														'capex_qty' => $qty_nov,
														'capex_budget_date' => $date,
														'ag_trans_det_added' => date_now(),
														'ag_trans_det_status' => 1
													);
													$this->admin->insert_data($designated_tbl->asset_group_transaction_details_tbl, $set_asg_trans);

													
													$date = $year . '-' . '12-01';
													$set_asg_trans = array(
														'ag_trans_item_id' => $ag_trans_item_id,
														'capex_qty' => $qty_dec,
														'capex_budget_date' => $date,
														'ag_trans_det_added' => date_now(),
														'ag_trans_det_status' => 1
													);
													$this->admin->insert_data($designated_tbl->asset_group_transaction_details_tbl, $set_asg_trans);

													if(!empty($rank_id)){
														$set_rank = array(
															'ag_trans_item_id' => $ag_trans_item_id,
															'rank_id' => $rank_id,
															'ag_trans_rank_added' => date_now(),
															'ag_trans_rank_status' => 1
														);

														$insert_rank = $this->admin->insert_data($designated_tbl->asset_group_transaction_rank_tbl, $set_rank);
													}

													$count++;
												}else{
													$msg = '<div class="alert alert-danger">Error while inserting item transaction!</div>';
													$this->session->set_flashdata('message', $msg);
													redirect($_SERVER['HTTP_REFERER']);
												}
											}
										}else{
											$this->db->trans_rollback();
											$msg = '<div class="alert alert-danger">Error empty cost center!</div>';
											$this->session->set_flashdata('message', $msg);
											redirect($_SERVER['HTTP_REFERER']);
										}
									}else{
										$msg = '<div class="alert alert-danger">Error asset subgroup not existing!</div>';
										$this->session->set_flashdata('message', $msg);
										redirect($_SERVER['HTTP_REFERER']);
									}
								}
							}else{
								$msg = '<div class="alert alert-danger">Error while inserting transaction, please try again!</div>';
							}

							if($this->db->trans_status() === FALSE){
								$this->db->trans_rollback();
								$msg = '<div class="alert alert-danger">Error please try again!</div>';
							}else{
								$this->db->trans_commit();
								$msg = '<div class="alert alert-success">CAPEX successfully added.</strong></div>';
							}

							$this->session->set_flashdata('message', $msg);
							redirect($_SERVER['HTTP_REFERER']);
						}else{
							$msg = '<div class="alert alert-danger">Error while checking Asset Group!</div>';
							$this->session->set_flashdata('message', $msg);
						}
					}else{
						$msg = '<div class="alert alert-danger">Error transaction already exist!</div>';	
						$this->session->set_flashdata('message', $msg);
						redirect($_SERVER['HTTP_REFERER']);
					}
				}else{
					$msg = '<div class="alert alert-danger">Error invalid cost center!</div>';	
					$this->session->set_flashdata('message', $msg);
					redirect($_SERVER['HTTP_REFERER']);
				}
			}else{
				
			}
		}
	}

	public function view_capex($id){
		$info = $this->_require_login();
		$data['title'] = 'View CAPEX';
		$data['id'] = $id;

		$user_info = $this->get_user_info();
		$bc_id = $user_info['cost_center_id'];
		$cost_center_id = $user_info['cost_center_id'];
		$designated_tbl = $this->_get_designated_tbl();

		$ag_trans_id = decode($id);
		$join_id = array(
			'asset_group_tbl b' => 'a.ag_id = b.ag_id AND a.ag_trans_status=1 AND a.ag_trans_id = ' . $ag_trans_id,
			'cost_center_tbl c' => 'a.cost_center_id = c.cost_center_id AND c.cost_center_id = ' . $cost_center_id
		);
		$check_id = $this->admin->check_join($designated_tbl->asset_group_transaction_tbl.' a', $join_id, TRUE);
		$year = $this->_active_year();
		if($check_id['result'] == TRUE){
			$data['parent_id']  = $check_id['info']->cost_center_id;
			$data['asset_group'] = $check_id['info']->ag_name;
			$data['cost_center_desc'] = $check_id['info']->cost_center_desc;
			$data['cost_center_code'] = encode($check_id['info']->cost_center_code);

			$module = 'CAPEX';
			$year  = $check_id['info']->ag_trans_budget_year;
			$data['year'] = $year;
			$data['budget_status'] = $this->check_module($module, $year);

			$join_det = array(
				'cost_center_tbl b' => 'a.cost_center_id = b.cost_center_id AND a.ag_trans_det_status = 1',
				'asset_subgroup_tbl c' => 'a.asg_id=c.asg_id AND a.ag_trans_id=' . $ag_trans_id,
				$designated_tbl->asset_group_transaction_item_tbl.' d' => 'a.ag_trans_item_id = d.ag_trans_item_id AND d.ag_trans_item_status = 1'
			);
			
			$data['asset_details'] = $this->admin->get_query('SELECT c.ag_trans_item_id, d.asg_name, g.cost_center_desc, h.capex_type_name, i.capex_category_name, b.capex_price,(SELECT SUM(x.capex_qty) FROM '.$designated_tbl->asset_group_transaction_details_tbl.' x WHERE b.ag_trans_item_id=x.ag_trans_item_id) as total_qty, b.capex_remarks, 

				(SELECT SUM(x.capex_qty) FROM '.$designated_tbl->asset_group_transaction_details_tbl.' x WHERE b.ag_trans_item_id=x.ag_trans_item_id) as total_qty, b.capex_remarks, 
				
				(SELECT y.rank_name FROM '.$designated_tbl->asset_group_transaction_rank_tbl.' x, rank_tbl y WHERE b.ag_trans_item_id = x.ag_trans_item_id AND x.rank_id = y.rank_id AND x.ag_trans_rank_status = 1) as rank,

				(SELECT SUM(x.capex_qty) FROM '.$designated_tbl->asset_group_transaction_details_tbl.' x WHERE b.ag_trans_item_id=x.ag_trans_item_id AND MONTH(x.capex_budget_date)=1 AND x.ag_trans_det_status=1) as jan,
				(SELECT SUM(x.capex_qty) FROM '.$designated_tbl->asset_group_transaction_details_tbl.' x WHERE b.ag_trans_item_id=x.ag_trans_item_id AND MONTH(x.capex_budget_date)=2 AND x.ag_trans_det_status=1) as feb,
				(SELECT SUM(x.capex_qty) FROM '.$designated_tbl->asset_group_transaction_details_tbl.' x WHERE b.ag_trans_item_id=x.ag_trans_item_id AND MONTH(x.capex_budget_date)=3 AND x.ag_trans_det_status=1) as mar,
				(SELECT SUM(x.capex_qty) FROM '.$designated_tbl->asset_group_transaction_details_tbl.' x WHERE b.ag_trans_item_id=x.ag_trans_item_id AND MONTH(x.capex_budget_date)=4 AND x.ag_trans_det_status=1) as apr,
				(SELECT SUM(x.capex_qty) FROM '.$designated_tbl->asset_group_transaction_details_tbl.' x WHERE b.ag_trans_item_id=x.ag_trans_item_id AND MONTH(x.capex_budget_date)=5 AND x.ag_trans_det_status=1) as may,
				(SELECT SUM(x.capex_qty) FROM '.$designated_tbl->asset_group_transaction_details_tbl.' x WHERE b.ag_trans_item_id=x.ag_trans_item_id AND MONTH(x.capex_budget_date)=6 AND x.ag_trans_det_status=1) as jun,
				(SELECT SUM(x.capex_qty) FROM '.$designated_tbl->asset_group_transaction_details_tbl.' x WHERE b.ag_trans_item_id=x.ag_trans_item_id AND MONTH(x.capex_budget_date)=7 AND x.ag_trans_det_status=1) as jul,
				(SELECT SUM(x.capex_qty) FROM '.$designated_tbl->asset_group_transaction_details_tbl.' x WHERE b.ag_trans_item_id=x.ag_trans_item_id AND MONTH(x.capex_budget_date)=8 AND x.ag_trans_det_status=1) as aug,
				(SELECT SUM(x.capex_qty) FROM '.$designated_tbl->asset_group_transaction_details_tbl.' x WHERE b.ag_trans_item_id=x.ag_trans_item_id AND MONTH(x.capex_budget_date)=9 AND x.ag_trans_det_status=1) as sep,
				(SELECT SUM(x.capex_qty) FROM '.$designated_tbl->asset_group_transaction_details_tbl.' x WHERE b.ag_trans_item_id=x.ag_trans_item_id AND MONTH(x.capex_budget_date)=10 AND x.ag_trans_det_status=1) as oct,
				(SELECT SUM(x.capex_qty) FROM '.$designated_tbl->asset_group_transaction_details_tbl.' x WHERE b.ag_trans_item_id=x.ag_trans_item_id AND MONTH(x.capex_budget_date)=11 AND x.ag_trans_det_status=1) as nov,
				(SELECT SUM(x.capex_qty) FROM '.$designated_tbl->asset_group_transaction_details_tbl.' x WHERE b.ag_trans_item_id=x.ag_trans_item_id AND MONTH(x.capex_budget_date)=12 AND x.ag_trans_det_status=1) as december

				FROM '.$designated_tbl->asset_group_transaction_tbl.' a, '.$designated_tbl->asset_group_transaction_item_tbl.' b, '.$designated_tbl->asset_group_transaction_details_tbl.' c, asset_subgroup_tbl d, asset_group_tbl e, transaction_type_tbl f, cost_center_tbl g, capex_type_tbl h, capex_category_tbl i  WHERE a.ag_trans_id=b.ag_trans_id AND b.ag_trans_item_id=c.ag_trans_item_id AND b.asg_id=d.asg_id AND d.ag_id=e.ag_id AND b.cost_center_id=g.cost_center_id AND a.trans_type_id=f.trans_type_id AND b.cost_center_id=g.cost_center_id AND a.ag_trans_status=1 AND b.ag_trans_item_status=1 AND c.ag_trans_det_status=1 AND b.capex_type_id = h.capex_type_id AND b.capex_category_id = i.capex_category_id AND f.trans_type_name="BUDGET" AND a.ag_trans_budget_year = ' . $year . ' AND a.ag_trans_id=' . $ag_trans_id . ' GROUP BY b.ag_trans_item_id');

			$data['content'] = $this->load->view('unit/unit_capex_view', $data , TRUE);
			$this->load->view('unit/templates', $data);
		}else{

		}
	}

	public function remove_capex_item(){
		$info = $this->_require_login();
		if($_SERVER['REQUEST_METHOD'] == 'POST'){
			$id = decode($this->input->post('id'));
			$where = array('ag_trans_item_id' => $id);
			$designated_tbl = $this->_get_designated_tbl();
			$check_id = $this->admin->check_data($designated_tbl->asset_group_transaction_item_tbl, $where, TRUE);
			if($check_id['result'] == TRUE){
				$ag_trans_id = $check_id['info']->ag_trans_id;
				$set = array('ag_trans_item_status' => 0);
				$remove_item = $this->admin->update_data($designated_tbl->asset_group_transaction_item_tbl, $set, $where);

				if($remove_item == TRUE){
					$msg = '<div class="alert alert-success">Item successfully removed.</strong></div>';
				}else{
					$msg = '<div class="alert alert-danger">Error please try again!</div>';
				}

				$this->session->set_flashdata('message', $msg);
				redirect($_SERVER['HTTP_REFERER']);
			}else{
				$msg = '<div class="alert alert-danger">Error please try again!</div>';
				$this->session->set_flashdata('message', $msg);
				redirect($_SERVER['HTTP_REFERER']);
			}
		}

	}

	public function get_capex_item(){
		$info = $this->_require_login();
		if($_SERVER['REQUEST_METHOD'] == 'POST'){
			$item_id = decode($this->input->post('id'));
			$designated_tbl = $this->_get_designated_tbl();

			$join_item = array(
				$designated_tbl->asset_group_transaction_tbl.' b' => 'a.ag_trans_id = b.ag_trans_id AND b.ag_trans_status = 1',
				'cost_center_tbl c' => 'b.cost_center_id = c.cost_center_id AND c.cost_center_status = 1 AND a.ag_trans_item_id = ' . $item_id,
				'asset_subgroup_tbl d' => 'a.asg_id = d.asg_id',
				'asset_group_tbl e' => 'd.ag_id = e.ag_id',
				'capex_type_tbl f' => 'a.capex_type_id = f.capex_type_id',
				'capex_category_tbl g' => 'a.capex_category_id = g.capex_category_id'
			);

			$check_id = $this->admin->check_join($designated_tbl->asset_group_transaction_item_tbl.' a', $join_item, TRUE, FALSE, FALSE, '*, c.cost_center_id as cost_center_main, a.cost_center_id cost_center_item, (SELECT x.rank_id FROM '.$designated_tbl->asset_group_transaction_rank_tbl.' x WHERE a.ag_trans_item_id = x.ag_trans_item_id AND x.ag_trans_rank_status = 1) as rank, f.capex_type_id, g.capex_category_id');

			if($check_id['result'] == TRUE){
				$cost_center_main = $check_id['info']->cost_center_main;
				$cost_center_item = $check_id['info']->cost_center_item;
				$price = $check_id['info']->capex_price;
				$asg_name = $check_id['info']->asg_name;
				$ag_name = $check_id['info']->ag_name;
				$remarks = $check_id['info']->capex_remarks;
				$rank = $check_id['info']->rank;
				$capex_type_id = $check_id['info']->capex_type_id;
				$capex_category_id = $check_id['info']->capex_category_id;

				if($ag_name == 'STORE EQUIPMENT' || $ag_name == 'KITCHEN EQUIPMENT' || $ag_name == 'LEASEHOLD IMPROVEMENTS'){
					$get_cost_center = $this->admin->get_data('cost_center_tbl', array('parent_id' => $cost_center_main, 'cost_center_type_id' => 8 , 'cost_center_status' => 1));
				}else{
					$get_cost_center = $this->admin->get_data('cost_center_tbl', array('parent_id' => $cost_center_main, 'cost_center_type_id !=' => 8, 'cost_center_status' => 1));
				}

				$get_capex_type = $this->admin->get_data('capex_type_tbl', array('capex_type_status' => 1));
				$get_capex_category = $this->admin->get_data('capex_category_tbl', array('capex_category_status' => 1));

				$cost_center_data = '';
				foreach($get_cost_center as $row){
					$selected = '';
					if($row->cost_center_id == $cost_center_item){
						$select = ' selected';
					}	
					$cost_center_data .= '<option value="' . encode($row->cost_center_id) . '"' . $selected . '>' . $row->cost_center_desc . '</option>';
				}
				
				$capex_type_data = '';
				foreach($get_capex_type as $row){
					$selected = '';
					if($row->capex_type_id == $capex_type_id){
						$selected = ' selected';
					}
					$capex_type_data .= '<option value="' . encode($row->capex_type_id) . '"' . $selected . '>' . $row->capex_type_name . '</option>';
				}

				$capex_category_data = '';
				foreach($get_capex_category as $row){
					$selected = '';
					if($row->capex_category_id == $capex_category_id){
						$selected = ' selected';
					}
					$capex_category_data .= '<option value="' . encode($row->capex_category_id) . '"' . $selected . '>' . $row->capex_category_name . '</option>';
				}

				$join_get_item = array(
					$designated_tbl->asset_group_transaction_item_tbl.' b' => 'a.ag_trans_item_id = b.ag_trans_item_id AND b.ag_trans_item_status = 1 AND a.ag_trans_det_status = 1 AND b.ag_trans_item_id = ' . $item_id,
				);
				$get_item = $this->admin->get_join($designated_tbl->asset_group_transaction_details_tbl.' a', $join_get_item, FALSE, 'a.capex_budget_date ASC', FALSE, 'a.capex_qty, MONTHNAME(a.capex_budget_date) as capex_budget_date');
				$month = array(
					'January' => 0,
					'February' => 0,
					'March' => 0,
					'April' => 0,
					'May' => 0, 
					'June' => 0,
					'July' => 0,
					'August' => 0,
					'September' => 0,
					'October' => 0,
					'November' => 0,
					'December' => 0
				);

				$total_qty = 0;
				foreach($get_item as $row_item){
					$month[$row_item->capex_budget_date] += $row_item->capex_qty;
					$total_qty += $row_item->capex_qty;
				}

				$get_rank = $this->admin->get_data('rank_tbl', array('rank_status' => 1));

				$rank_data = '';
				if(count($get_rank) > 1){
					$rank_data = '<select name="rank" class="form-control input-sm" required>';
					foreach($get_rank as $row_rank){
						if($rank == $row_rank->rank_id){
							$rank_data .= '<option value="' . encode($row_rank->rank_id) . '" selected>' . $row_rank->rank_name . '</option>';
						}else{
							$rank_data .= '<option value="' . encode($row_rank->rank_id) . '">' . $row_rank->rank_name . '</option>';
						}
					}

					$rank_data .= '</select>';
				}

				$details = array(
					'asset_group' => $ag_name,
					'asset_name' => $asg_name,
					'price' => $price,
					'total' => $total_qty,
					'cost_center' => $cost_center_data,
					'capex_type' => $capex_type_data,
					'capex_category' => $capex_category_data,
					'month' => $month,
					'rank' => $rank_data,
					'remarks' => $remarks,
				);

				$data['result'] = 1;
				$data['info'] = $details;
			}else{
				$data['result'] = 0;
			}

			echo json_encode($data);
			exit();
		}
	}

	public function update_capex_item(){
		$info = $this->_require_login();
		if($_SERVER['REQUEST_METHOD'] == 'POST'){
			$id = $this->input->post('id');
			$cost_center_id = decode(clean_data($this->input->post('cost_center')));
			$capex_type_id = decode(clean_data($this->input->post('capex_type')));
			$capex_category_id = decode(clean_data($this->input->post('capex_category')));
			$capex = clean_data($this->input->post('capex'));
			$rank = decode(clean_data($this->input->post('rank')));
			$remarks = clean_data($this->input->post('remarks'));
			$designated_tbl = $this->_get_designated_tbl();

			$count = 0;
			$this->db->trans_start();
			foreach($id as $row_id){
				$item_id = decode($row_id);

				$join_item = array(
					$designated_tbl->asset_group_transaction_tbl.' b' => 'a.ag_trans_id = b.ag_trans_id AND a.ag_trans_item_status = 1 AND a.ag_trans_item_id = ' . $item_id,
					'asset_group_tbl c' => 'b.ag_id = c.ag_id'
				);
				$check_item = $this->admin->check_join($designated_tbl->asset_group_transaction_item_tbl.' a', $join_item, TRUE);

				if($check_item['result'] == TRUE){
					$cost_center_db = $check_item['info']->cost_center_id;
					$capex_type_db = $check_item['info']->capex_type_id;
					$capex_category_db = $check_item['info']->capex_category_id;
					$ag_trans_id = $check_item['info']->ag_trans_id;
					$ag_name = $check_item['info']->ag_name;

					if($cost_center_db != $cost_center_id){
						$update_cost_center = $this->admin->update_data($designated_tbl->asset_group_transaction_item_tbl, array('cost_center_id' => $cost_center_id, 'capex_remarks' => $remarks), array('ag_trans_item_id' => $item_id));
					}
					
					if($capex_type_db != $capex_type_id){
						$update_cost_center = $this->admin->update_data('asset_group_transaction_item_tbl', array('capex_type_id' => $capex_type_id, 'capex_remarks' => $remarks), array('ag_trans_item_id' => $item_id));
					}

					if($capex_category_db != $capex_category_id){
						$update_cost_center = $this->admin->update_data('asset_group_transaction_item_tbl', array('capex_category_id' => $capex_category_id, 'capex_remarks' => $remarks), array('ag_trans_item_id' => $item_id));
					}

					if($ag_name == 'TRANSPORTATION EQUIPMENT'){
						$check_rank = $this->admin->check_data($designated_tbl->asset_group_transaction_rank_tbl, array('ag_trans_item_id' => $item_id, 'ag_trans_rank_status' => 1), TRUE);
						if($check_rank['result'] == TRUE){

							$ag_trans_rank_id = $check_rank['info']->ag_trans_rank_id;
							$update_rank = $this->admin->update_data($designated_tbl->asset_group_transaction_rank_tbl, array('rank_id' => $rank), array('ag_trans_rank_id' => $ag_trans_rank_id));	

						}else{
							$set_rank = array(
								'ag_trans_item_id' => $item_id,
								'rank_id' => $rank,
								'ag_trans_rank_added' => date_now(),
								'ag_trans_rank_status' => 1
							);
							$insert_rank = $this->admin->insert_data($designated_tbl->asset_group_transaction_rank_tbl, $set_rank);
						}
					}

					$qty = check_num($capex['jan'][$count]);
					$update_item = $this->admin->update_data($designated_tbl->asset_group_transaction_details_tbl, array('capex_qty' => $qty), array('ag_trans_item_id' => $item_id, 'MONTH(capex_budget_date)' => 1, 'ag_trans_det_status' => 1));
					
					$qty = check_num($capex['feb'][$count]);
					$update_item = $this->admin->update_data($designated_tbl->asset_group_transaction_details_tbl, array('capex_qty' => $qty), array('ag_trans_item_id' => $item_id, 'MONTH(capex_budget_date)' => 2, 'ag_trans_det_status' => 1));


					$qty = check_num($capex['mar'][$count]);
					$update_item = $this->admin->update_data($designated_tbl->asset_group_transaction_details_tbl, array('capex_qty' => $qty), array('ag_trans_item_id' => $item_id, 'MONTH(capex_budget_date)' => 3, 'ag_trans_det_status' => 1));


					$qty = check_num($capex['apr'][$count]);
					$update_item = $this->admin->update_data($designated_tbl->asset_group_transaction_details_tbl, array('capex_qty' => $qty), array('ag_trans_item_id' => $item_id, 'MONTH(capex_budget_date)' => 4, 'ag_trans_det_status' => 1));

					$qty = check_num($capex['may'][$count]);
					$update_item = $this->admin->update_data($designated_tbl->asset_group_transaction_details_tbl, array('capex_qty' => $qty), array('ag_trans_item_id' => $item_id, 'MONTH(capex_budget_date)' => 5, 'ag_trans_det_status' => 1));
					
					$qty = check_num($capex['jun'][$count]);
					$update_item = $this->admin->update_data($designated_tbl->asset_group_transaction_details_tbl, array('capex_qty' => $qty), array('ag_trans_item_id' => $item_id, 'MONTH(capex_budget_date)' => 6, 'ag_trans_det_status' => 1));

					$qty = check_num($capex['jul'][$count]);
					$update_item = $this->admin->update_data($designated_tbl->asset_group_transaction_details_tbl, array('capex_qty' => $qty), array('ag_trans_item_id' => $item_id, 'MONTH(capex_budget_date)' => 7, 'ag_trans_det_status' => 1));

					$qty = check_num($capex['aug'][$count]);
					$update_item = $this->admin->update_data($designated_tbl->asset_group_transaction_details_tbl, array('capex_qty' => $qty), array('ag_trans_item_id' => $item_id, 'MONTH(capex_budget_date)' => 8, 'ag_trans_det_status' => 1));

					$qty = check_num($capex['sep'][$count]);
					$update_item = $this->admin->update_data($designated_tbl->asset_group_transaction_details_tbl, array('capex_qty' => $qty), array('ag_trans_item_id' => $item_id, 'MONTH(capex_budget_date)' => 9, 'ag_trans_det_status' => 1));
					
					$qty = $capex['oct'][$count];
					$update_item = $this->admin->update_data($designated_tbl->asset_group_transaction_details_tbl, array('capex_qty' => $qty), array('ag_trans_item_id' => $item_id, 'MONTH(capex_budget_date)' => 10, 'ag_trans_det_status' => 1));

					$qty = check_num($capex['nov'][$count]);
					$update_item = $this->admin->update_data($designated_tbl->asset_group_transaction_details_tbl, array('capex_qty' => $qty), array('ag_trans_item_id' => $item_id, 'MONTH(capex_budget_date)' => 11, 'ag_trans_det_status' => 1));

					$qty = check_num($capex['dec'][$count]);
					$update_item = $this->admin->update_data($designated_tbl->asset_group_transaction_details_tbl, array('capex_qty' => $qty), array('ag_trans_item_id' => $item_id, 'MONTH(capex_budget_date)' => 12, 'ag_trans_det_status' => 1));
				}else{
					$msg = '<div class="alert alert-danger">Error please try again!</div>';
					$this->session->set_flashdata('message', $msg);
					redirect($_SERVER['HTTP_REFERER']);
				}

				$count++;
			}

			if($this->db->trans_status() === FALSE){
				$this->db->trans_rollback();
				$msg = '<div class="alert alert-danger">Error please try again!</div>';
			}else{
				$this->db->trans_commit();
				$msg = '<div class="alert alert-success">CAPEX successfully updated.</strong></div>';
			}

			$this->session->set_flashdata('message', $msg);
			redirect($_SERVER['HTTP_REFERER']);
		}
	}

	public function download_capex($year){
		$info = $this->_require_login();

		$user_info = $this->get_user_info();
		$cost_center_id = $user_info['cost_center_id'];
		$cost_center_name = $user_info['cost_center_desc'];
		$designated_tbl = $this->_get_designated_tbl();


		ini_set('max_execution_time', 0); 
		ini_set('memory_limit','2048M');

		$check_cost_center = $this->admin->check_data('cost_center_tbl', array('cost_center_id' => $cost_center_id, 'cost_center_status' => 1), TRUE);
		if($check_cost_center['result'] == TRUE){
			$cost_center_name = $check_cost_center['info']->cost_center_desc;


			$asset_details = $this->admin->get_query('SELECT c.ag_trans_item_id, d.asg_name, g.cost_center_desc, g.cost_center_code, b.capex_price, a.ag_trans_budget_year, b.capex_lifespan, e.ag_name,

				(SELECT SUM(x.capex_qty) FROM '.$designated_tbl->asset_group_transaction_details_tbl.' x WHERE b.ag_trans_item_id=x.ag_trans_item_id) as total_qty, b.capex_remarks, 
				
				(SELECT y.rank_name FROM '.$designated_tbl->asset_group_transaction_rank_tbl.' x, rank_tbl y WHERE b.ag_trans_item_id = x.ag_trans_item_id AND x.rank_id = y.rank_id AND x.ag_trans_rank_status = 1) as rank,

				(SELECT SUM(x.capex_qty) FROM '.$designated_tbl->asset_group_transaction_details_tbl.' x WHERE b.ag_trans_item_id=x.ag_trans_item_id AND MONTH(x.capex_budget_date)=1 AND x.ag_trans_det_status=1) as jan,
				(SELECT SUM(x.capex_qty) FROM '.$designated_tbl->asset_group_transaction_details_tbl.' x WHERE b.ag_trans_item_id=x.ag_trans_item_id AND MONTH(x.capex_budget_date)=2 AND x.ag_trans_det_status=1) as feb,
				(SELECT SUM(x.capex_qty) FROM '.$designated_tbl->asset_group_transaction_details_tbl.' x WHERE b.ag_trans_item_id=x.ag_trans_item_id AND MONTH(x.capex_budget_date)=3 AND x.ag_trans_det_status=1) as mar,
				(SELECT SUM(x.capex_qty) FROM '.$designated_tbl->asset_group_transaction_details_tbl.' x WHERE b.ag_trans_item_id=x.ag_trans_item_id AND MONTH(x.capex_budget_date)=4 AND x.ag_trans_det_status=1) as apr,
				(SELECT SUM(x.capex_qty) FROM '.$designated_tbl->asset_group_transaction_details_tbl.' x WHERE b.ag_trans_item_id=x.ag_trans_item_id AND MONTH(x.capex_budget_date)=5 AND x.ag_trans_det_status=1) as may,
				(SELECT SUM(x.capex_qty) FROM '.$designated_tbl->asset_group_transaction_details_tbl.' x WHERE b.ag_trans_item_id=x.ag_trans_item_id AND MONTH(x.capex_budget_date)=6 AND x.ag_trans_det_status=1) as jun,
				(SELECT SUM(x.capex_qty) FROM '.$designated_tbl->asset_group_transaction_details_tbl.' x WHERE b.ag_trans_item_id=x.ag_trans_item_id AND MONTH(x.capex_budget_date)=7 AND x.ag_trans_det_status=1) as jul,
				(SELECT SUM(x.capex_qty) FROM '.$designated_tbl->asset_group_transaction_details_tbl.' x WHERE b.ag_trans_item_id=x.ag_trans_item_id AND MONTH(x.capex_budget_date)=8 AND x.ag_trans_det_status=1) as aug,
				(SELECT SUM(x.capex_qty) FROM '.$designated_tbl->asset_group_transaction_details_tbl.' x WHERE b.ag_trans_item_id=x.ag_trans_item_id AND MONTH(x.capex_budget_date)=9 AND x.ag_trans_det_status=1) as sep,
				(SELECT SUM(x.capex_qty) FROM '.$designated_tbl->asset_group_transaction_details_tbl.' x WHERE b.ag_trans_item_id=x.ag_trans_item_id AND MONTH(x.capex_budget_date)=10 AND x.ag_trans_det_status=1) as oct,
				(SELECT SUM(x.capex_qty) FROM '.$designated_tbl->asset_group_transaction_details_tbl.' x WHERE b.ag_trans_item_id=x.ag_trans_item_id AND MONTH(x.capex_budget_date)=11 AND x.ag_trans_det_status=1) as nov,
				(SELECT SUM(x.capex_qty) FROM '.$designated_tbl->asset_group_transaction_details_tbl.' x WHERE b.ag_trans_item_id=x.ag_trans_item_id AND MONTH(x.capex_budget_date)=12 AND x.ag_trans_det_status=1) as december

				FROM '.$designated_tbl->asset_group_transaction_tbl.' a, '.$designated_tbl->asset_group_transaction_item_tbl.' b, '.$designated_tbl->asset_group_transaction_details_tbl.' c, asset_subgroup_tbl d, asset_group_tbl e, transaction_type_tbl f, cost_center_tbl g  WHERE a.ag_trans_id=b.ag_trans_id AND b.ag_trans_item_id=c.ag_trans_item_id AND b.asg_id=d.asg_id AND d.ag_id=e.ag_id AND b.cost_center_id=g.cost_center_id AND a.trans_type_id=f.trans_type_id AND b.cost_center_id=g.cost_center_id AND a.ag_trans_status=1 AND b.ag_trans_item_status=1 AND c.ag_trans_det_status=1 AND f.trans_type_name="BUDGET" AND a.ag_trans_budget_year=' . $year . ' AND g.parent_id=' . $cost_center_id . ' GROUP BY b.ag_trans_item_id'
			);

			$this->load->library('excel');

			$spreadsheet = $this->excel;
			$spreadsheet->getProperties()->setCreator('BAVI')
					->setLastModifiedBy('Budgeting System')
					->setTitle('Employees')
					->setSubject('List of Employees')
					->setDescription('List of Employees');

			
			$styleArray = array(
					'font' 	=> array(
							'bold' => true,
					),
					'alignment' => array(
							'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
							'vertical' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
					),
					'borders' => array(
							'top' => array(
									'style' => PHPExcel_Style_Border::BORDER_THIN,
							),
					),
					'fill' => array(
							'type' => \PHPExcel_Style_Fill::FILL_GRADIENT_LINEAR,
							'rotation' => 90,
							'startcolor' => array(
									'argb' => 'FFA0A0A0',
							),
							'endcolor' => array(
									'argb' => 'FFFFFFFF',
							),
					),
			);


			foreach(range('A','H') as $columnID) {
				$spreadsheet->getActiveSheet()->getColumnDimension($columnID)
						->setAutoSize(true);
			}

			$style_center = array(
				'font' => array(
					'bold' => true,
					'size' => 20
				),
				'alignment' => array(
					'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
				)
			);

			$style_info =  array(
				'font' => array(
					'bold' => true
				)
			);

			$style_border = array(
				'font' => array(
					'bold' => true
				),
				'alignment' => array(
					'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER
				),
				'borders' => array(
					'allborders' => array(
						'style' => PHPExcel_Style_Border::BORDER_THIN
					)
				)
			);

			$style_data = array(
				'borders' => array(
					'allborders' => array(
						'style' => PHPExcel_Style_Border::BORDER_THIN
					)
				)
			);

			$style_out = array(
				'font' => array(
					'bold' => true,
					'color' => array('rgb' => 'FF0000')
				),
				'borders' => array(
					'allborders' => array(
						'style' => PHPExcel_Style_Border::BORDER_THIN
					)
				),
				'alignment' => array(
					'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER
				),
			);


			$spreadsheet->getActiveSheet()->getStyle("A1:V1")->applyFromArray($style_border);
			$spreadsheet->getActiveSheet()->getStyle("A1:V1")->applyFromArray($style_info);
			$spreadsheet->setActiveSheetIndex(0)
				->setCellValue("A1", 'Location')
				->setCellValue("B1", 'Asset Group')
				->setCellValue("C1", 'Asset')
				->setCellValue("D1", "Cost Center Code")
				->setCellValue("E1", "Cost Center Name")
				->setCellValue("F1", "Year")
				->setCellValue("G1", "Useful Life (Month)")
				->setCellValue("H1", "CAPEX Amount")
				->setCellValue("I1", "Jan")
				->setCellValue("J1", "Feb")
				->setCellValue("K1", "Mar")
				->setCellValue("L1", "Apr")
				->setCellValue("M1", "May")
				->setCellValue("N1", "Jun")
				->setCellValue("O1", "Jul")
				->setCellValue("P1", "Aug")
				->setCellValue("Q1", "Sep")
				->setCellValue("R1", "Oct")
				->setCellValue("S1", "Nov")
				->setCellValue("T1", "Dec")
				->setCellValue("U1", "Total QTY")
				->setCellValue("V1", "Total Amount")
				;
			// Add some data
			$x= 2;
			$count = 0;
			foreach($asset_details as $row){

				$jan = $row->jan;
				$feb = $row->feb;
				$mar = $row->mar;
				$apr = $row->apr;
				$may = $row->may;
				$jun = $row->jun;
				$jul = $row->jul;
				$aug = $row->aug;
				$sep = $row->sep;
				$oct = $row->oct;
				$nov = $row->nov;
				$december = $row->december;
				$total = $jan + $feb + $mar + $apr + $may + $jun + $jul + $aug + $sep + $oct + $nov + $december;
				$capex_amount = $row->capex_price;
				$capex_total  = $total * $capex_amount;

				$spreadsheet->setActiveSheetIndex(0)
						->setCellValue("A$x",$cost_center_name)
						->setCellValue("B$x",$row->ag_name)
						->setCellValue("C$x",$row->asg_name)
						->setCellValue("D$x",$row->cost_center_code)
						->setCellValue("E$x",$row->cost_center_desc)
						->setCellValue("F$x",$row->ag_trans_budget_year)
						->setCellValue("G$x",$row->capex_lifespan)
						->setCellValue("H$x",$row->capex_price)
						->setCellValue("I$x",$jan)
						->setCellValue("J$x",$feb)
						->setCellValue("K$x",$mar)
						->setCellValue("L$x",$apr)
						->setCellValue("M$x",$may)
						->setCellValue("N$x",$jun)
						->setCellValue("O$x",$jul)
						->setCellValue("P$x",$aug)
						->setCellValue("Q$x",$sep)
						->setCellValue("R$x",$oct)
						->setCellValue("S$x",$nov)
						->setCellValue("T$x",$december)
						->setCellValue("U$x",$total)
						->setCellValue("V$x",$capex_total)
						;

				$spreadsheet->getActiveSheet()->getStyle("A$x:V$x")->applyFromArray($style_data);
				$x++;
			}

			foreach(range('A','V') as $columnID) {
				$spreadsheet->getActiveSheet()->getColumnDimension($columnID)
						->setAutoSize(true);
			}

			$spreadsheet->getActiveSheet()->getStyle('H2:V' . ($x - 1))->getNumberFormat()->setFormatCode('#,##0.00');
			
			// Rename worksheet
			$spreadsheet->getActiveSheet()->setTitle('CAPEX Data - ' . $year);

			// set right to left direction
			//		$spreadsheet->getActiveSheet()->setRightToLeft(true);

			// Set active sheet index to the first sheet, so Excel opens this as the first sheet
			$spreadsheet->setActiveSheetIndex(0);

			// Redirect output to a client’s web browser (Excel2007)
			header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
			$random = generate_random(5);
			header('Content-Disposition: attachment;filename="Budgeting - CAPEX ' . $cost_center_name . ' '  . $year . '_' . $random . '.xlsx"');
			header('Cache-Control: max-age=0');
			// If you're serving to IE 9, then the following may be needed
			header('Cache-Control: max-age=1');

			// If you're serving to IE over SSL, then the following may be needed
			header('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
			header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT'); // always modified
			header('Cache-Control: cache, must-revalidate'); // HTTP/1.1
			header('Pragma: public'); // HTTP/1.0

			$writer = PHPExcel_IOFactory::createWriter($spreadsheet, 'Excel2007');
			$writer->save('php://output');
			exit;
		}else{
			echo 'Error Cost Center not exist. Please try again!';
		}
	}

	
	/*Employee*/

	public function employees($year = null){
		$info = $this->_require_login();
		$user_info = $this->get_user_info();
		$cost_center_id = $user_info['cost_center_id'];
		$data['title'] = 'Employees';

		$module = 'MANPOWER';
		if($year == null){
			$year = $this->_active_year();
		}

		$data['year'] = $year;
		$data['budget_status'] = $this->check_module($module, $year);

		$join_emp = array(
			'employee_year_tbl b' => 'a.emp_id = b.emp_id AND b.emp_year = ' . $year,
			'company_unit_tbl c' => 'b.company_unit_id = c.company_unit_id',
			'cost_center_tbl d' => 'b.cost_center_id = d.cost_center_id AND d.parent_id =' . $cost_center_id,
			'employee_type_tbl e' => 'b.emp_type_id = e.emp_type_id',
			'rank_tbl f' => 'b.rank_id = f.rank_id',
		);

		$data['employee'] = $this->admin->get_join('employee_tbl a', $join_emp);
		$data['type'] = $this->admin->get_data('employee_type_tbl', array('emp_type_status' => 1));
		$data['rank'] = $this->admin->get_data('rank_tbl', array('rank_status' => 1));
		$data['unit'] = $this->admin->get_data('company_unit_tbl', array('company_unit_status' => 1));

		$where = array('cost_center_type_id != ' => 2, 'cost_center_status' => 1, 'parent_id' => $cost_center_id);
		$data['cost_center'] = $this->admin->get_data('cost_center_tbl', $where);

		$data['content'] = $this->load->view('unit/unit_employee_content', $data , TRUE);
		$this->load->view('unit/templates', $data);
	}

	public function get_new_emp(){
		$where = array('emp_type_id' => 2 , 'emp_status' => 1);
		$count = $this->admin->get_count('employee_tbl', $where) + 1;
		$format = 'NEW-EMP-';
		if($count > 0 && $count < 10){
			$format .= '00' . $count;
		}else if($count >= 10 && $count < 100){
			$format = $format . '0' . $count;
		}else{
			$format = $format . $count;
		}
		$data['result'] = 1;
		$data['info'] = $format;
		
		echo json_encode($data);
	}

	public function get_emp_cost_center(){
		$unit = decode($this->input->post('unit'));
		$user_info = $this->get_user_info();

		$cost_center_id = $user_info['cost_center_id'];
		$where = array('cost_center_type_id != ' => 2, 'cost_center_status' => 1, 'parent_id' => $cost_center_id);
		$get_cost_center = $this->admin->get_data('cost_center_tbl', $where);
		$cost_center_data = '<option value="">Select...</option>';
		foreach($get_cost_center as $row){
			$cost_center_data .= '<option value="' . encode($row->cost_center_id) . '">' . $row->cost_center_desc . '</option>';
		}

		$data['result'] = 1;
		$data['info'] = $cost_center_data;
		
		echo json_encode($data);
	}

	public function add_employee(){
		$info = $this->_require_login();
		$user_id = decode($info['user_id']);
		$user_info = $this->get_user_info();

		if($_SERVER['REQUEST_METHOD'] == 'POST'){
			$fname = clean_data($this->input->post('fname'));
			$lname = clean_data($this->input->post('lname'));
			$type = decode(clean_data($this->input->post('type')));
			$emp_no = clean_data($this->input->post('emp_no'));
			$salary = clean_data($this->input->post('salary'));
			$rank = decode(clean_data($this->input->post('rank')));
			$unit = $user_info['company_unit_id'];
			$cost_center = decode(clean_data($this->input->post('cost_center')));
			$year = clean_data($this->input->post('year'));

			if(!empty($fname) && !empty($fname) && !empty($type) && !empty($emp_no) && !empty($salary) && !empty($rank) && !empty($unit) && !empty($cost_center) && !empty($year)){
				$check_emp_no = $this->admin->check_data('employee_tbl', array('emp_no' => $emp_no));
				if($check_emp_no == FALSE){
					$check_code = $this->admin->check_data('cost_center_tbl', array('cost_center_id' =>  $cost_center));
					if($check_code == TRUE){
						$check_unit = $this->admin->check_data('company_unit_tbl', array('company_unit_id' =>  $unit, 'company_unit_status' => 1));
						if($check_unit == TRUE){
							$check_rank = $this->admin->check_data('rank_tbl', array('rank_id' =>  $rank, 'rank_status' => 1));
							if($check_rank == TRUE){
								$check_type = $this->admin->check_data('employee_type_tbl', array('emp_type_id' =>  $type, 'emp_type_status' => 1));
								if($check_type == TRUE){
									$set = array(
										'rank_id' => $rank,
										'company_unit_id' => $unit,
										'cost_center_id' => $cost_center,
										'emp_type_id' => $type,
										'emp_fname' => $fname,
										'emp_lname' => $lname,
										'emp_no' => $emp_no,
										'basic_salary' => $salary,
										'emp_added' => date_now(),
										'emp_status' => 1
									);
									$result = $this->admin->insert_data('employee_tbl', $set, TRUE);
									
									if($result == TRUE){
										$emp_id = $result['id'];

										$set_year = array(
											'emp_id' => $emp_id,
											'rank_id' => $rank,
											'company_unit_id' => $unit,
											'cost_center_id' => $cost_center,
											'emp_type_id' => $type,
											'emp_year' => $year,
											'emp_year_salary' => $salary,
											'emp_year_added' => date_now(),
											'emp_year_status' => 1
										);

										$insert_year = $this->admin->insert_data('employee_year_tbl', $set_year);

										if($this->db->trans_status() === FALSE){
											$this->db->trans_rollback();
											$msg = '<div class="alert alert-danger">Error please try again!</div>';
											$this->session->set_flashdata('message', $msg);
											redirect($_SERVER['HTTP_REFERER']);
										}else{
											$this->db->trans_commit();
											$msg = '<div class="alert alert-success">Employee successfully added.</div>';
											$this->session->set_flashdata('message', $msg);
											redirect($_SERVER['HTTP_REFERER']);
										}
									}else{
										$msg = '<div class="alert alert-danger">Error please try again!</div>';
									}
								}else{
									$msg = '<div class="alert alert-danger">Error in type please try again!</div>';	
								}
							}else{
								$msg = '<div class="alert alert-danger">Error in rank please try again!</div>';
							}
						}else{
							$msg = '<div class="alert alert-danger">Error in unit try again!</div>';
						}
					}else{
						$msg = '<div class="alert alert-danger">Error in cost center.</div>';
					}
				}else{
					$msg = '<div class="alert alert-danger">Error Employee no. already exist.</div>';
				}
			}else{
				$msg = '<div class="alert alert-danger">Error make sure all field need are fill up!</div>';
			}

			$this->session->set_flashdata('message', $msg);
			redirect($_SERVER['HTTP_REFERER']);
		}else{
			redirect($_SERVER['HTTP_REFERER']);
		}
	}

	public function modal_employee(){
		$info = $this->_require_login();
		$user_info = $this->get_user_info();
		$cost_center_id = $user_info['cost_center_id'];

		if($_SERVER['REQUEST_METHOD'] == 'POST'){
			$emp_year_id = decode(clean_data($this->input->post('id')));

			$join = array(
				'employee_tbl b' => 'a.emp_id = b.emp_id AND a.emp_year_id = ' . $emp_year_id,
				'company_unit_tbl c' => 'a.company_unit_id = c.company_unit_id',
				'cost_center_tbl d' => 'a.cost_center_id = d.cost_center_id AND d.parent_id =' . $cost_center_id,
				'employee_type_tbl e' => 'a.emp_type_id = e.emp_type_id',
				'rank_tbl f' => 'a.rank_id = f.rank_id',
			);

			$check_emp = $this->admin->check_join('employee_year_tbl a', $join, TRUE);


			if($check_emp['result'] == TRUE){
				$fname = $check_emp['info']->emp_fname;
				$lname = $check_emp['info']->emp_lname;
				$emp_no = $check_emp['info']->emp_no;
				$type_name = $check_emp['info']->emp_type_name;
				$salary = $check_emp['info']->emp_year_salary;

				$company_unit_id = $check_emp['info']->company_unit_id;
				$cost_center_id = $check_emp['info']->cost_center_id;
				$emp_type_id = $check_emp['info']->emp_type_id;
				$rank_id = $check_emp['info']->rank_id;

				$get_unit = $this->admin->get_data('company_unit_tbl', array('company_unit_status' => 1));	
				$unit_data = '<option value="">Select...</option>';

				foreach($get_unit as $row_unit):

					if($row_unit->company_unit_id == $company_unit_id){
						$unit_data .= '<option value="' . encode($row_unit->company_unit_id) . '" selected>' . $row_unit->company_unit_name . '</option>';
					}else{
						$unit_data .= '<option value="' . encode($row_unit->company_unit_id) . '">' . $row_unit->company_unit_name . '</option>';
					}

				endforeach;


				$get_cost = $this->admin->get_data('cost_center_tbl', array('cost_center_status' => 1));	
				$cost_data = '<option value="">Select...</option>';

				foreach($get_cost as $row_cost):

					if($row_cost->cost_center_id == $cost_center_id){
						$cost_data .= '<option value="' . encode($row_cost->cost_center_id) . '" selected>' . $row_cost->cost_center_desc . '</option>';
					}else{
						$cost_data .= '<option value="' . encode($row_cost->cost_center_id) . '">' . $row_cost->cost_center_desc . '</option>';
					}

				endforeach;

				$get_rank = $this->admin->get_data('rank_tbl', array('rank_status' => 1));	
				$rank_data = '<option valu="">Select...</option>';

				foreach($get_rank as $row_rank):

					if($row_rank->rank_id == $rank_id){
						$rank_data .= '<option value="' . encode($row_rank->rank_id) . '" selected>' . $row_rank->rank_name . '</option>';
					}else{
						$rank_data .= '<option value="' . encode($row_rank->rank_id) . '">' . $row_rank->rank_name . '</option>';
					}

				endforeach;

				$data['result'] = 1;
				$data['info'] = array(
					'fname' => $fname,
					'lname' => $lname,
					'emp_no' => $emp_no,
					'salary' => $salary,
					'type' => $type_name,
					'unit' => $unit_data,
					'cost_center' => $cost_data,
					'rank' => $rank_data
				);

				
			}else{
				$data['result'] = 0;
			}

			echo json_encode($data);
		}
	}

	public function update_employee(){
		$info = $this->_require_login();
		$user_id = decode($info['user_id']);
		$user_info = $this->get_user_info();
		$cost_center_id = $user_info['cost_center_id'];
		if($_SERVER['REQUEST_METHOD'] == 'POST'){
			$id = decode(clean_data($this->input->post('id')));
			$fname = clean_data($this->input->post('fname'));
			$lname = clean_data($this->input->post('lname'));
			$emp_no = clean_data($this->input->post('emp_no'));
			$rank = decode(clean_data($this->input->post('rank')));
			$unit = decode(clean_data($this->input->post('unit')));
			$cost_center = decode(clean_data($this->input->post('cost_center')));
			$salary = clean_data($this->input->post('salary'));

			if(!empty($id) && !empty($fname) && !empty($lname) && !empty($emp_no) && !empty($rank) && !empty($unit) && !empty($cost_center) && !empty($salary)){
				$check_id = $this->admin->check_data('employee_year_tbl', array('emp_year_id' => $id), TRUE);
				if($check_id['result'] == TRUE){
					$emp_id = $check_id['info']->emp_id;
					$check_emp_no = $this->admin->check_data('employee_tbl', array('emp_no' => $emp_no, 'emp_id !=' => $emp_id));
					if($check_emp_no == FALSE){
						$check_code = $this->admin->check_data('cost_center_tbl', array('cost_center_id' =>  $cost_center, 'parent_id ' => $cost_center_id));
						if($check_code == TRUE){
							$check_unit = $this->admin->check_data('company_unit_tbl', array('company_unit_id' =>  $unit, 'company_unit_status' => 1));
							if($check_unit == TRUE){
								$check_rank = $this->admin->check_data('rank_tbl', array('rank_id' =>  $rank, 'rank_status' => 1));
								if($check_rank == TRUE){
									
									$set = array(
										'emp_fname' => $fname,
										'emp_lname' => $lname,
										'emp_no' => $emp_no
									);
									$where = array('emp_id' => $emp_id);
									$update_emp = $this->admin->update_data('employee_tbl', $set, $where);
									

									$set_year = array(
										'rank_id' => $rank,
										'company_unit_id' => $unit,
										'cost_center_id' => $cost_center,
										'emp_year_salary' => $salary,
										'emp_year_added' => date_now(),
										'emp_year_status' => 1
									);

									$where_year = array('emp_year_id' => $id);

									$update_emp_year = $this->admin->update_data('employee_year_tbl', $set_year, $where_year);

									$msg = '<div class="alert alert-success">Employee updated.</div>';
									
								}else{
									$msg = '<div class="alert alert-danger">Error in rank please try again!</div>';
									$this->session->set_flashdata('message', $msg);
									redirect($_SERVER['HTTP_REFERER']);
								}
							}else{
								$msg = '<div class="alert alert-danger">Error in unit try again!</div>';
								$this->session->set_flashdata('message', $msg);
								redirect($_SERVER['HTTP_REFERER']);
							}
						}else{
							$msg = '<div class="alert alert-danger">Error in cost center.</div>';
							$this->session->set_flashdata('message', $msg);
							redirect($_SERVER['HTTP_REFERER']);
						}
					}else{
						$msg = '<div class="alert alert-danger">Error employee no. already exist.</div>';
						$this->session->set_flashdata('message', $msg);
						redirect($_SERVER['HTTP_REFERER']);
					}
				}else{
					$msg = '<div class="alert alert-danger">Error Employee not exist.</div>';
					$this->session->set_flashdata('message', $msg);
					redirect($_SERVER['HTTP_REFERER']);
				}
			}else{
				$msg = '<div class="alert alert-danger">Error make sure all field need are fill up!</div>';
				$this->session->set_flashdata('message', $msg);
				redirect($_SERVER['HTTP_REFERER']);
			}

			if($this->db->trans_status() === FALSE){
				$this->db->trans_rollback();
				$msg = '<div class="alert alert-danger">Error please try again!</div>';
				$this->session->set_flashdata('message', $msg);
				redirect($_SERVER['HTTP_REFERER']);
			}else{
				$this->db->trans_commit();
				$this->session->set_flashdata('message', $msg);
				redirect($_SERVER['HTTP_REFERER']);
			}

		}else{
			redirect($_SERVER['HTTP_REFERER']);
		}
	}

	public function deactivate_employee(){
		$info = $this->_require_login();

		if($_SERVER['REQUEST_METHOD'] == 'POST'){
			$emp_year_id = decode(clean_data($this->input->post('id')));

			$check_emp = $this->admin->check_data('employee_year_tbl a', array('emp_year_id' => $emp_year_id, 'emp_year_status' => 1), TRUE);
			if($check_emp['result'] == TRUE){
				$set_emp = array('emp_year_status' => 0);
				$where_emp = array('emp_year_id' => $emp_year_id);

				$this->admin->update_data('employee_year_tbl', $set_emp, $where_emp);

				$msg = '<div class="alert alert-success">Employee deactivated.</div>';
				$this->session->set_flashdata('message', $msg);
				redirect($_SERVER['HTTP_REFERER']);	
			}else{
				$msg = '<div class="alert alert-danger">Error Employee not exist. Please try again!</div>';
				$this->session->set_flashdata('message', $msg);
				redirect($_SERVER['HTTP_REFERER']);
			}

			// echo json_encode($data);
		}
	}

	public function activate_employee(){
		$info = $this->_require_login();

		if($_SERVER['REQUEST_METHOD'] == 'POST'){
			$emp_year_id = decode(clean_data($this->input->post('id')));

			$check_emp = $this->admin->check_data('employee_year_tbl a', array('emp_year_id' => $emp_year_id, 'emp_year_status' => 0), TRUE);
			if($check_emp['result'] == TRUE){
				$set_emp = array('emp_year_status' => 1);
				$where_emp = array('emp_year_id' => $emp_year_id);

				$this->admin->update_data('employee_year_tbl', $set_emp, $where_emp);

				$msg = '<div class="alert alert-success">Employee activated.</div>';
				$this->session->set_flashdata('message', $msg);
				redirect($_SERVER['HTTP_REFERER']);	
			}else{
				$msg = '<div class="alert alert-danger">Error Employee not exist. Please try again!</div>';
				$this->session->set_flashdata('message', $msg);
				redirect($_SERVER['HTTP_REFERER']);
			}

			// echo json_encode($data);
		}
	}

	public function upload_employees(){
		$info = $this->_require_login();
		$user_id = decode($info['user_id']);
		$user_info = $this->get_user_info();
		$parent_id = $user_info['cost_center_id'];

		if($_SERVER['REQUEST_METHOD'] == 'POST'){
			$year = clean_data($this->input->post('year'));
			$this->load->library('excel');
			ini_set('max_execution_time', 0); 
			ini_set('memory_limit','2048M');
			
			$temp_code = substr(str_shuffle("0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ"), 0, 10);

			$config['upload_path']   = 'assets/employee';
	        $config['allowed_types'] = 'xls|xlsx|xlsb';
	        $config['file_name'] = 'tmp_' . $temp_code;
	        $this->load->library('upload', $config);
			
	        if (! $this->upload->do_upload('employee_file')) {
	            $error = array('error' => $this->upload->display_errors());
	            $msg = $this->upload->display_errors();
	            $this->session->set_flashdata('message', $msg);
	            redirect($_SERVER['HTTP_REFERER']);
	        }else{
				$data = array('upload_data' => $this->upload->data());
		        $file_name = $data['upload_data']['file_name'];
				$file = 'assets/employee/' . $file_name;	   
			   
		    	$this->db->trans_start();
		       
				$objPHPExcel = PHPExcel_IOFactory::load($file);
				//get only the Cell Collection
				$cell_collection = $objPHPExcel->getActiveSheet()->getCellCollection();
			
				$high = $objPHPExcel->getActiveSheet()->getHighestRow();
				for($a = 2; $a <= $high; $a++){
					$fname = $objPHPExcel->getActiveSheet()->getCell('A' . $a)->getValue();
					$lname = $objPHPExcel->getActiveSheet()->getCell('B' . $a)->getValue();
					$type = strtoupper($objPHPExcel->getActiveSheet()->getCell('C' . $a)->getValue());
					$employee_no = $objPHPExcel->getActiveSheet()->getCell('D' . $a)->getValue();
					$salary = $objPHPExcel->getActiveSheet()->getCell('E' . $a)->getValue();
					$rank = strtoupper($objPHPExcel->getActiveSheet()->getCell('F' . $a)->getValue());
					$unit = strtoupper($objPHPExcel->getActiveSheet()->getCell('G' . $a)->getValue());
					$cost_center = strtoupper($objPHPExcel->getActiveSheet()->getCell('H' . $a)->getValue());
					
					$check_type = $this->admin->check_data('employee_type_tbl', array('emp_type_name' => $type, 'emp_type_status' => 1), TRUE);
					if($check_type['result'] == TRUE){
						$emp_type_id = $check_type['info']->emp_type_id;
						$check_rank = $this->admin->check_data('rank_tbl', array('rank_name' => $rank), TRUE);
						if($check_rank['result'] == TRUE){
							$rank_id = $check_rank['info']->rank_id;
							$check_unit = $this->admin->check_data('company_unit_tbl', array('company_unit_name' => $unit), TRUE);
							if($check_unit['result'] == TRUE){
								$company_unit_id = $check_unit['info']->company_unit_id;
								$check_cost_center = $this->admin->check_data('cost_center_tbl', array('cost_center_code' => $cost_center, 'company_unit_id' => $company_unit_id, 'parent_id' => $parent_id), TRUE);
								if($check_cost_center['result'] == TRUE){
									$cost_center_id = $check_cost_center['info']->cost_center_id;
									$check_employee = $this->admin->check_data('employee_tbl', array('emp_no' => $employee_no), TRUE);
									if($check_employee['result'] == TRUE){
										$emp_id = $check_employee['info']->emp_id;
									}else{
										$set_emp = array(
											'rank_id' => $rank_id,
											'company_unit_id' => $company_unit_id,
											'cost_center_id' => $cost_center_id,
											'emp_type_id' => $emp_type_id,
											'emp_fname' => $fname,
											'emp_lname' => $lname,
											'emp_no' => $employee_no,
											'basic_salary' => $salary,
											'emp_added' => date_now(),
											'emp_status' => 1
										);

										$insert_emp = $this->admin->insert_data('employee_tbl', $set_emp, TRUE);
										if($insert_emp['result'] == TRUE){
											$emp_id = $insert_emp['id'];
										}else{
											$msg = '<div class="alert alert-danger">Error while inserting data line ' . $a . '!</div>';
											$this->session->set_flashdata('message', $msg);
											redirect($_SERVER['HTTP_REFERER']);
										}
									}

									$where_emp_year = array(
										'emp_id' => $emp_id,
										'emp_year' => $year,
										'emp_year_status' => 1
									);
									$check_emp_year = $this->admin->check_data('employee_year_tbl', $where_emp_year, TRUE);
									if($check_emp_year['result'] == TRUE){
										$emp_year_id = $check_emp_year['info']->emp_year_id;
										$set_emp_year = array(
											'rank_id' => $rank_id,
											'company_unit_id' => $company_unit_id,
											'cost_center_id' => $cost_center_id,
											'emp_type_id' => $emp_type_id,
											'emp_year_salary' => $salary,
										);

										$where_emp_year = array('emp_year_id' => $emp_year_id);
										$this->admin->update_data('employee_year_tbl', $set_emp_year, $where_emp_year);
									}else{
										$set_emp_year = array(
											'emp_id' => $emp_id,
											'rank_id' => $rank_id,
											'company_unit_id' => $company_unit_id,
											'cost_center_id' => $cost_center_id,
											'emp_type_id' => $emp_type_id,
											'emp_year' => $year,
											'emp_year_salary' => $salary,
											'emp_year_added' => date_now(),
											'emp_year_status' => 1
										);

										$this->admin->insert_data('employee_year_tbl', $set_emp_year);
									}
								}else{
									$msg = '<div class="alert alert-danger">Error invalid Cost Center line ' . $a . '!</div>';
									$this->session->set_flashdata('message', $msg);
									redirect($_SERVER['HTTP_REFERER']);
								}
							}else{
								$msg = '<div class="alert alert-danger">Error invalid Unit line ' . $a . '!</div>';
								$this->session->set_flashdata('message', $msg);
								redirect($_SERVER['HTTP_REFERER']);
							}
						}else{
							$msg = '<div class="alert alert-danger">Error invalid Rank line ' . $a . '!</div>';
							$this->session->set_flashdata('message', $msg);
							redirect($_SERVER['HTTP_REFERER']);
						}
					}else{
						$msg = '<div class="alert alert-danger">Error invalid Type line ' . $a . '!</div>';
						$this->session->set_flashdata('message', $msg);
						redirect($_SERVER['HTTP_REFERER']);
					}
				}

				if($this->db->trans_status() === FALSE){
					$this->db->trans_rollback();
					$msg = '<div class="alert alert-danger">Error please try again!</div>';
					$this->session->set_flashdata('message', $msg);
					redirect($_SERVER['HTTP_REFERER']);
				}else{
					$this->db->trans_commit();
					$msg = '<div class="alert alert-success">Employees uploaded!</div>';
					$this->session->set_flashdata('message', $msg);
					redirect($_SERVER['HTTP_REFERER']);
				}
			}
		}else{
			redirect($_SERVER['HTTP_REFERER']);
		}
	}

	public function download_employees($year){
		$info = $this->_require_login();
		$user_info = $this->get_user_info();
		$cost_center_id = $user_info['cost_center_id'];

		$join_emp = array(
			'employee_year_tbl b' => 'a.emp_id = b.emp_id AND b.emp_year = ' . $year,
			'company_unit_tbl c' => 'b.company_unit_id = c.company_unit_id',
			'cost_center_tbl d' => 'b.cost_center_id = d.cost_center_id AND d.parent_id = ' . $cost_center_id,
			'employee_type_tbl e' => 'b.emp_type_id = e.emp_type_id',
			'rank_tbl f' => 'b.rank_id = f.rank_id',
		);

		$employees = $this->admin->get_join('employee_tbl a', $join_emp);

		$this->load->library('excel');

		$spreadsheet = $this->excel;
		$spreadsheet->getProperties()->setCreator('BAVI')
				->setLastModifiedBy('Budgeting System')
				->setTitle('Employees')
				->setSubject('List of Employees')
				->setDescription('List of Employees');

		
		$styleArray = array(
				'font' 	=> array(
						'bold' => true,
				),
				'alignment' => array(
						'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
						'vertical' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
				),
				'borders' => array(
						'top' => array(
								'style' => PHPExcel_Style_Border::BORDER_THIN,
						),
				),
				'fill' => array(
						'type' => \PHPExcel_Style_Fill::FILL_GRADIENT_LINEAR,
						'rotation' => 90,
						'startcolor' => array(
								'argb' => 'FFA0A0A0',
						),
						'endcolor' => array(
								'argb' => 'FFFFFFFF',
						),
				),
		);


		foreach(range('A','H') as $columnID) {
			$spreadsheet->getActiveSheet()->getColumnDimension($columnID)
					->setAutoSize(true);
		}

		$style_center = array(
			'font' => array(
				'bold' => true,
				'size' => 20
			),
			'alignment' => array(
				'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
			)
		);

		$style_info =  array(
			'font' => array(
				'bold' => true
			)
		);

		$style_border = array(
			'font' => array(
				'bold' => true
			),
			'alignment' => array(
				'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER
			),
			'borders' => array(
				'allborders' => array(
					'style' => PHPExcel_Style_Border::BORDER_THIN
				)
			)
		);

		$style_data = array(
			'borders' => array(
				'allborders' => array(
					'style' => PHPExcel_Style_Border::BORDER_THIN
				)
			)
		);

		$style_out = array(
			'font' => array(
				'bold' => true,
				'color' => array('rgb' => 'FF0000')
			),
			'borders' => array(
				'allborders' => array(
					'style' => PHPExcel_Style_Border::BORDER_THIN
				)
			),
			'alignment' => array(
				'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER
			),
		);


		$spreadsheet->getActiveSheet()->getStyle("A1:H1")->applyFromArray($style_border);
		$spreadsheet->getActiveSheet()->getStyle("A1:H1")->applyFromArray($style_info);
		$spreadsheet->setActiveSheetIndex(0)
				->setCellValue("A1", 'First Name')
				->setCellValue("B1", "Last Name")
				->setCellValue("C1", "Type (NEW OR OLD)")
				->setCellValue("D1", "Salary")
				->setCellValue("E1", "Employee No.")
				->setCellValue("F1", "Rank")
				->setCellValue("G1", "Unit")
				->setCellValue("H1", "Cost Center");
		// Add some data
		$x= 2;
		$count = 0;
		foreach($employees as $row){
			$spreadsheet->setActiveSheetIndex(0)
					->setCellValue("A$x",$row->emp_fname)
					->setCellValue("B$x",$row->emp_lname)
					->setCellValue("C$x",$row->emp_type_name)
					->setCellValue("D$x",$row->emp_year_salary)
					->setCellValue("E$x",$row->emp_no)
					->setCellValue("F$x",$row->rank_name)
					->setCellValue("G$x",$row->company_unit_name)
					->setCellValue("H$x",$row->cost_center_code);

			$spreadsheet->getActiveSheet()->getStyle("A$x:H$x")->applyFromArray($style_data);
			$x++;
		}
		
		// Rename worksheet
		$spreadsheet->getActiveSheet()->setTitle('List of Employees ' . $year);

		// set right to left direction
		//		$spreadsheet->getActiveSheet()->setRightToLeft(true);

		// Set active sheet index to the first sheet, so Excel opens this as the first sheet
		$spreadsheet->setActiveSheetIndex(0);

		// Redirect output to a client’s web browser (Excel2007)
		header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
		header('Content-Disposition: attachment;filename="Budgeting - List of Employees ' . $year . '.xlsx"');
		header('Cache-Control: max-age=0');
		// If you're serving to IE 9, then the following may be needed
		header('Cache-Control: max-age=1');

		// If you're serving to IE over SSL, then the following may be needed
		header('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
		header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT'); // always modified
		header('Cache-Control: cache, must-revalidate'); // HTTP/1.1
		header('Pragma: public'); // HTTP/1.0

		$writer = PHPExcel_IOFactory::createWriter($spreadsheet, 'Excel2007');
		$writer->save('php://output');
		exit;
	}


	/*Manpower*/

	public function manpower($year = null){
		$info = $this->_require_login();

		$user_info = $this->get_user_info();
		$cost_center_id = $user_info['cost_center_id'];

		if($year == null){
			$year = $this->_active_year();
		}

		$module = 'MANPOWER';
		if($year == null){
			$year = $this->_active_year();
		}

		$data['budget_status'] = $this->check_module($module, $year);

		$data['year'] = $year;
		$data['title'] = 'Manpower';

		$join_manpower = array(
			'cost_center_tbl b' => 'a.cost_center_id = b.cost_center_id AND b.cost_center_type_id IN (2, 9) AND a.manpower_status = 1 AND a.manpower_year = ' . $year . ' AND (b.cost_center_id = ' . $cost_center_id . ' OR b.parent_id = ' . $cost_center_id . ')',
			'company_unit_tbl c' => 'b.company_unit_id = c.company_unit_id',
			'rank_tbl d' => 'a.rank_id = d.rank_id'
		);

		$data['manpower'] = $this->admin->get_join('manpower_tbl a', $join_manpower);
		
		$data['cost_center'] = $this->_get_manpower_sc_cost_center($cost_center_id, $year);
		$data['rank'] = $this->admin->get_data('rank_tbl', array('rank_status' => 1));
		$data['content'] = $this->load->view('unit/unit_manpower_content', $data , TRUE);
		$this->load->view('unit/templates', $data);
	}

	public function _get_manpower_sc_cost_center($cost_center_id, $year){
		$info = $this->_require_login();
		$user_id = decode($info['user_id']);

			$unit = decode($this->input->post('unit'));

			$where = '(cost_center_id =' . $cost_center_id . ' OR parent_id = ' . $cost_center_id . ') AND cost_center_type_id IN (2, 9) AND cost_center_status = 1';
			$get_cost_center = $this->admin->get_data('cost_center_tbl', $where);

			return $get_cost_center;
	}

	public function add_manpower(){
		$info = $this->_require_login();
		$user_id = decode($info['user_id']);
		if($_SERVER['REQUEST_METHOD'] == 'POST'){
			$cost_center = decode(clean_data($this->input->post('cost_center')));
			$old = clean_data($this->input->post('old'));
			$new = clean_data($this->input->post('new'));
			$bc_old = clean_data($this->input->post('bc-old'));
			$bc_new = clean_data($this->input->post('bc-new'));
			$year = clean_data($this->input->post('year'));
			$rank = decode(clean_data($this->input->post('rank')));
			$position = clean_data($this->input->post('position'));

			if(!empty($cost_center) && !empty($year) && !empty($rank) && !empty($position)){

				$check_code = $this->admin->check_data('cost_center_tbl', array('cost_center_id' =>  $cost_center));
				if($check_code == TRUE){
					$check_rank = $this->admin->check_data('rank_tbl', array('rank_id' => $rank, 'rank_status' => 1));
					if($check_rank == TRUE){
						$set = array(
							'cost_center_id' => $cost_center,
							'rank_id' => $rank,
							'user_id' => $user_id,
							'manpower_position' => $position,
							'manpower_year' => $year,
							'manpower_old' => $old,
							'manpower_new' => $new,
							'manpower_bc_old' => $bc_old,
							'manpower_bc_new' => $bc_new,
							'manpower_added' => date_now(),
							'manpower_status' => 1
						);
						$result = $this->admin->insert_data('manpower_tbl', $set);
						
						if($result == TRUE){
							$msg = '<div class="alert alert-success">Manpower successfully added.</div>';
						}else{
							$msg = '<div class="alert alert-danger">Error please try again!</div>';
						}
					}else{
						$msg = '<div class="alert alert-danger">Error Rank not exist.</div>';
					}
				}else{
					$msg = '<div class="alert alert-danger">Error Cost Center not exist.</div>';
				}
			}else{
				$msg = '<div class="alert alert-danger">Error make sure all field need are fill up!</div>';
			}

			$this->session->set_flashdata('message', $msg);
			redirect($_SERVER['HTTP_REFERER']);
		}else{
			redirect('unit');
		}
	}

	public function modal_manpower(){
		$info = $this->_require_login();

		if($_SERVER['REQUEST_METHOD'] == 'POST'){
			$manpower_id = decode(clean_data($this->input->post('id')));

			$join = array(
				'cost_center_tbl b' => 'a.cost_center_id = b.cost_center_id AND a.manpower_id = ' . $manpower_id
			);

			$check_manpower = $this->admin->check_join('manpower_tbl a', $join, TRUE);


			if($check_manpower['result'] == TRUE){
				$cost_center = $check_manpower['info']->cost_center_desc;
				$manpower_position = $check_manpower['info']->manpower_position;
				$manpower_old = $check_manpower['info']->manpower_old;
				$manpower_new = $check_manpower['info']->manpower_new;
				$manpower_bc_old = $check_manpower['info']->manpower_bc_old;
				$manpower_bc_new = $check_manpower['info']->manpower_bc_new;
				$rank_id = $check_manpower['info']->rank_id;

				$get_rank = $this->admin->get_data('rank_tbl', array('rank_status' => 1));
				$rank_data = '<option value="">Select Rank...</option>';

				foreach($get_rank as $row_rank){

					if($rank_id == $row_rank->rank_id){
						$rank_data .= '<option value="' . encode($row_rank->rank_id) . '" selected>' . $row_rank->rank_name . '</option>';
					}else{
						$rank_data .= '<option value="' . encode($row_rank->rank_id) . '">' . $row_rank->rank_name . '</option>';
					}
				}

				$data['result'] = 1;
				$data['info'] = array(
					'cost_center' => $cost_center,
					'manpower_position' => $manpower_position,
					'manpower_old' => $manpower_old,
					'manpower_new' => $manpower_new,
					'manpower_bc_old' => $manpower_bc_old,
					'manpower_bc_new' => $manpower_bc_new,
					'rank' => $rank_data
				);

				
			}else{
				$data['result'] = 0;
			}

			echo json_encode($data);
		}
	}

	public function update_manpower(){
		$info = $this->_require_login();
		$user_id = decode($info['user_id']);
		if($_SERVER['REQUEST_METHOD'] == 'POST'){
			$id = decode(clean_data($this->input->post('id')));
			$rank = decode(clean_data($this->input->post('rank')));
			$old = clean_data($this->input->post('old'));
			$new = clean_data($this->input->post('new'));
			$bc_old = clean_data($this->input->post('bc-old'));
			$bc_new = clean_data($this->input->post('bc-new'));
			$position = clean_data($this->input->post('position'));

			if(!empty($id) && !empty($rank) && !empty($position)){
				$check_manpower = $this->admin->check_data('manpower_tbl', array('manpower_id' => $id, 'manpower_status' => 1));
				if($check_manpower == TRUE){

					$set = array(
						'rank_id' => $rank,
						'manpower_position' => $position,
						'manpower_old' => $old,
						'manpower_new' => $new,
						'manpower_bc_old' => $bc_old,
						'manpower_bc_new' => $bc_new
					);
					$where = array('manpower_id' => $id);
					$result = $this->admin->update_data('manpower_tbl', $set, $where);
					
					if($result == TRUE){
						$msg = '<div class="alert alert-success">Manpower successfully updated.</div>';
					}else{
						$msg = '<div class="alert alert-danger">Error please try again!</div>';
					}
					
				}else{
					$msg = '<div class="alert alert-danger">Error Manpower not exist.</div>';
				}
			}else{
				$msg = '<div class="alert alert-danger">Error make sure all field need are fill up!</div>';
			}

			$this->session->set_flashdata('message', $msg);
			redirect($_SERVER['HTTP_REFERER']);
		}else{
			redirect('unit');
		}
	}

	public function remove_manpower(){
		$info = $this->_require_login();
		$user_id = decode($info['user_id']);
		if($_SERVER['REQUEST_METHOD'] == 'POST'){
			$id = decode(clean_data($this->input->post('id')));

			if(!empty($id)){
				$check_manpower = $this->admin->check_data('manpower_tbl', array('manpower_id' => $id, 'manpower_status' => 1));
				if($check_manpower == TRUE){

					$set = array('manpower_status' => 0);
					$where = array('manpower_id' => $id);
					$result = $this->admin->update_data('manpower_tbl', $set, $where);
					
					if($result == TRUE){
						$msg = '<div class="alert alert-success">Manpower successfully removed.</div>';
					}else{
						$msg = '<div class="alert alert-danger">Error please try again!</div>';
					}
					
				}else{
					$msg = '<div class="alert alert-danger">Error Manpower not exist.</div>';
				}
			}else{
				$msg = '<div class="alert alert-danger">Error make sure all field need are fill up!</div>';
			}

			$this->session->set_flashdata('message', $msg);
			redirect($_SERVER['HTTP_REFERER']);
		}else{
			redirect('unit');
		}
	}

	/*PDF Report*/

	public function header($pdf) {
		$image_file = 'assets/img/bavi-logo.png';
        $pdf->setJPEGQuality(100);
	    $pdf->SetLineStyle(array('width' => 1, 'cap' => 'butt', 'join' => 'miter', 'dash' => 0, 'color' => array(255,255,255)));
	    $pdf->Image($image_file, 145, 10, 50, 14.31, 'PNG', '', '', true, 150, '', false, false, 1, false, false, false);
        // Set font
        
        // Page number
        
    }

    public function header_landscape($pdf) {
		$image_file = 'assets/img/bavi-logo.png';
        $pdf->setJPEGQuality(100);
	    $pdf->SetLineStyle(array('width' => 1, 'cap' => 'butt', 'join' => 'miter', 'dash' => 0, 'color' => array(255,255,255)));
	    $pdf->Image($image_file, 230, 10, 50, 14.31, 'PNG', '', '', true, 150, '', false, false, 1, false, false, false);
        // Set font
        
        // Page number
        
    }

    public function footer($pdf) {
        // Position at 15 mm from bottom
        // Set font
    }

    public function watermark($pdf){
    	// get the current page break margin
		$bMargin = $pdf->getBreakMargin();
		// get current auto-page-break mode
		$auto_page_break = $pdf->getAutoPageBreak();
		// disable auto-page-break
		$pdf->SetAutoPageBreak(false, 0);
		// set bacground image
		$img_file = 'assets/img/confidential.png';
		$pdf->Image($img_file, 10, 50, '', '', '', '', '', false, 300, '', false, false, 0);
		// restore auto-page-break status
		$pdf->SetAutoPageBreak($auto_page_break, $bMargin);
		// set the starting point for the page content
		$pdf->setPageMark();
    }

    public function watermark_landscape($pdf){
    	// get the current page break margin
		$bMargin = $pdf->getBreakMargin();
		// get current auto-page-break mode
		$auto_page_break = $pdf->getAutoPageBreak();
		// disable auto-page-break
		$pdf->SetAutoPageBreak(false, 0);
		// set bacground image
		$img_file = 'assets/img/confidential.png';
		$pdf->Image($img_file, 50, 50, '', '', '', '', '', false, 300, '', false, false, 0);
		// restore auto-page-break status
		$pdf->SetAutoPageBreak($auto_page_break, $bMargin);
		// set the starting point for the page content
		$pdf->setPageMark();
    }

	public function download_pdf($year = null){
		$info = $this->_require_login();
		$user_info = $this->get_user_info();
 		$bc_name = $user_info['cost_center_desc'];
 		$cost_center_id = $user_info['cost_center_id'];
 		$company_unit_id = $user_info['company_unit_id'];

		if($year == null){
			$year = $this->_active_year();
		}
		
		$this->load->library("Pdf");
			  
	    // create new PDF document
	    $pdf = new TCPDF('P', PDF_UNIT, 'A4', true, 'UTF-8', false);
	  
	    // set document information

	    $pdf->SetCreator(PDF_CREATOR);
	    $pdf->SetAuthor('Bounty Agro Venture, Inc.');
	    $pdf->SetTitle('BAVI Budgeting Report');
	    $pdf->SetSubject('BAVI Budgeting Report');
	    $pdf->SetKeywords('BAVI, Budgeting, Report');   
	  
	    // set default header data

	    $title = "Bounty Agro Venture, Inc.";

	    $pdf->setPrintHeader(false);
	    $pdf->setPrintFooter(false);

	    $pdf->SetAutoPageBreak(true);
	  
	    // set some language-dependent strings (optional)
	    if (@file_exists(dirname(__FILE__).'/lang/eng.php')) {
	        require_once(dirname(__FILE__).'/lang/eng.php');
	        $pdf->setLanguageArray($l);
	    }
	    
	  
	    // Add a page
	    // This method has several options, check the source code documentation for more information.
	    $pdf->AddPage();
	    $this->header($pdf);

	    $pdf->SetFont('arial', 'B', 42);
	    $pdf->SetTextColor(0,0,0);

	    $support_name = $bc_name;
	    if($support_name == 'SUPPLY CHAIN MANAGEMENT'){
		    $pdf->Text(20, 100, 'SUPPLY CHAIN');
		    $pdf->Text(20, 120, 'MANAGEMENT');
		    $pdf->Text(20, 140, 'BUDGET');
		    $pdf->SetTextColor(255,51,0);
		    $pdf->Text(20, 160, $year);
		}elseif($support_name == 'FINANCIAL PLANNING & ANALYSIS'){
		    $pdf->Text(20, 100, 'FINANCIAL PLANNING');
		    $pdf->Text(20, 120, '& ANALYSIS');
		    $pdf->Text(20, 140, 'BUDGET');
		    $pdf->SetTextColor(255,51,0);
		    $pdf->Text(20, 160, $year);
		}elseif($support_name == 'OFFICE OF THE PRESIDENT'){
		    $pdf->Text(20, 100, 'OFFICE OF THE');
		    $pdf->Text(20, 120, 'PRESIDENT');
		    $pdf->Text(20, 140, 'BUDGET');
		    $pdf->SetTextColor(255,51,0);
		    $pdf->Text(20, 160, $year);
		}else{
			$pdf->Text(20, 100, $support_name);
		    $pdf->Text(20, 120, 'BUDGET');
		    $pdf->SetTextColor(255,51,0);
		    $pdf->Text(20, 140, $year);
		}

	    $pdf->SetFont('arial', 'B', 8);
	    $pdf->SetTextColor(0,0,0);
	    $pdf->Text(15, 280, 'BOUNTY AGRO VENTURES INC. | Unit 1008, The Taipan Place Condo, F. Ortigas Jr. Ave, Ortigas');
	    $pdf->Text(15, 285, 'Center, Pasig City');


	    //Table of Contents
	    $pdf->AddPage();
	    $this->header($pdf);

	    $pdf->SetFont('arial', 'B', 16);
	    $pdf->SetTextColor(0,0,0);

	    $pdf->setFont('arialb', '', 12);
	    $pdf->Text(20, 40, $bc_name . ' SUPPORT CENTER');
	    $pdf->Text(20, 46, 'EXECUTIVE SUMMARY');
	    $pdf->Text(20, 52, 'Budget ' . $year);

	    /*OPEX Report*/
	    $get_opex = $this->_get_opex_report($year, $cost_center_id, $company_unit_id);
	    $opex = $get_opex['opex'];
	    $opex1 = $get_opex['opex1'];
	    $opex2 = $get_opex['opex2'];
	    $opex_dif1 = $get_opex['opex_dif1'];
	    $opex_dif2 = $get_opex['opex_dif2'];
	    $opex_condition1 = $get_opex['opex_condition1'];
	    $opex_condition2 = $get_opex['opex_condition2'];


	    $opex_report = 'OPEX is P' . convert_num($opex) . ', ' . $opex_condition1 . ' than by P' . convert_num($opex_dif1) . ' vs ' . ($year - 1) . ' and ' . $opex_condition2 . ' than by P' . convert_num($opex_dif2) . ' vs ' . ($year - 2);
	    $pdf->SetFont('arial', 'I', 10);
	    $pdf->Text(20, 80, $opex_report);

	    $per_unit = 0;
	    $per_unit1 = 0;
	    $per_unit2 = 0;

	    $get_unit = $this->_get_unit_report($year, $cost_center_id, $get_opex);
	    $per_unit = $get_unit['per_unit'];
	    $per_unit1 = $get_unit['per_unit1'];
	    $per_unit2 = $get_unit['per_unit2'];
	    $per_unit_dif1 = $get_unit['per_unit_dif1'];
	    $per_unit_dif2 = $get_unit['per_unit_dif2'];
	    $per_unit_condition1 = $get_unit['per_unit_condition1'];
	    $per_unit_condition2 = $get_unit['per_unit_condition2'];
	    $per_unit_percent1 = abs($get_unit['per_unit_percent1']);
	    $per_unit_percent2 = abs($get_unit['per_unit_percent2']);

	    $unit_report = 'This is equivalent to P' . number_format($per_unit, 3) . ' or ' . number_format($per_unit_percent1) . '% ' . $per_unit_condition1 . ' than ' . ($year - 1) . '\'s P' . number_format($per_unit1, 3) . ' AND ' . number_format($per_unit_percent2)  . '% ' . $per_unit_condition2 . ' than ' . ($year - 2)  . '\'s P' . number_format($per_unit2, 3);

	    $pdf->SetFont('arial', 'I', 10);
	    $pdf->Text(20, 90, $unit_report);


	    /*Manpower Report*/

	    $get_manpower = $this->_get_manpower_report($year, $cost_center_id);
	    $manpower_old = $get_manpower['manpower_old'];
	    $manpower_new = $get_manpower['manpower_new'];
	    $manpower_total = $manpower_old + $manpower_new;

	    $manpower_report = 'Manpower will increase by ' . $manpower_new . ' from ' . $manpower_old . ' to ' . $manpower_total . ' in ' . $year;
	    $pdf->SetFont('arial', 'I', 10);
	    $pdf->Text(20, 100, $manpower_report);

	    
	    /*CAPEX Report*/
	    $get_capex = $this->_get_capex_report($year, $cost_center_id, $company_unit_id);
	    $capex = $get_capex['capex'];
	    $capex1 = $get_capex['capex1'];
	    $capex2 = $get_capex['capex2'];
	    $capex_dif1 = $get_capex['capex_dif1'];
	    $capex_dif2 = $get_capex['capex_dif2'];
	    $capex_condition1 = $get_capex['capex_condition1'];
	    $capex_condition2 = $get_capex['capex_condition2'];

	    $capex_report = 'CAPEX is P' . convert_num($capex) . ', ' . $capex_condition1 . ' than by P' . convert_num($capex_dif1) . ' vs ' . ($year - 1) . ' AND ' . $capex_condition2 . ' than by P' . convert_num($capex_dif2) . ' vs ' . ($year - 2);
	    $pdf->SetFont('arial', 'I', 10);
	    $pdf->Text(20, 110, $capex_report);


	     // get the current page break margin
		$bMargin = $pdf->getBreakMargin();
		// get current auto-page-break mode
		$auto_page_break = $pdf->getAutoPageBreak();
		// disable auto-page-break
		$pdf->SetAutoPageBreak(false, 0);
		// set bacground image
		$img_file = 'assets/img/confidential.png';
		$pdf->Image($img_file, 10, 50, '', '', '', '', '', false, 300, '', false, false, 0);
		// restore auto-page-break status
		$pdf->SetAutoPageBreak($auto_page_break, $bMargin);
		// set the starting point for the page content
		$pdf->setPageMark();

		$this->header($pdf);

		/*COMPARATIVE OPERATING EXPENSES PER ACCOUNT Page*/

		$pdf->AddPage('L');

		$pdf->SetTextColor(0,0,0);

	    $pdf->setFont('arialb', '', 12);
	    $pdf->Text(20, 40, $bc_name . ' SUPPORT CENTER');
	    $pdf->Text(20, 46, 'COMPARATIVE OPERATING EXPENSES PER ACCOUNT');
	    $pdf->Text(20, 52, 'Budget ' . $year);

	    $pdf->SetFont('arial', '', 8);

	    $get_opex_account = $this->_get_opex_per_account($year, $cost_center_id, $company_unit_id);
	    /*echo '<pre>';
	    print_r($get_opex_account);
	    echo '</pre>';*/
	    $opex_sort_col = array_column($get_opex_account, 'total');
		array_multisort($opex_sort_col, SORT_DESC, $get_opex_account);

	    $opex_per_account_tbl = '<br /><br /><br /><br />
	    	<table border="1" cellpadding="3">
	    		<tr>
	    			<th rowspan="2" align="center" width="30%"><strong>GL DESCRIPTION</strong></th>
	    			<th align="center" width="10%"><strong>BUDGET</strong></th>
	    			<th colspan="2" align="center" width="20%"><strong>ACTUAL</strong></th>
	    			<th colspan="4" align="center" width="40%"><strong>VARIANCE</strong></th>
	    		</tr>

	    		<tr>
	    			<td align="center"><strong>' . $year . '</strong></td>
	    			<td align="center"><strong>' . ($year - 1) . '</strong></td>
	    			<td align="center"><strong>' . ($year - 2) . '</strong></td>
	    			<td align="center"><strong>' . $year  . ' vs ' . ($year - 1) . '</strong></td>
	    			<td align="center"><strong>%</strong></td>
	    			<td align="center"><strong>' . ($year - 1) . ' vs ' . ($year - 2) . '</strong></td>
	    			<td align="center"><strong>%</strong></td>
	    		</tr>
	    ';

	    /*echo '<pre>';
	    print_r($get_opex_account);
	    echo '</pre>';
	    exit;*/

	    $count_opex_account = 1;
	    $opex_acc_overall = 0;
	    $opex_acc_overall1 = 0;
	    $opex_acc_overall2 = 0;
	    foreach($get_opex_account as $row_opex_acc){
	    	$opex_acc_total = $row_opex_acc->total;
	    	$opex_acc_total1 = $row_opex_acc->total1;
	    	$opex_acc_total2 = $row_opex_acc->total2;

	    	$opex_acc_overall += $opex_acc_total;
	    	$opex_acc_overall1 += $opex_acc_total1;
	    	$opex_acc_overall2 += $opex_acc_total2;

	    	$opex_acc_dif1 = ($opex_acc_total - $opex_acc_total1);
	    	$opex_acc_dif2 = ($opex_acc_total - $opex_acc_total2);
	    	$opex_acc_per1 = $opex_acc_total1 != 0 ? ($opex_acc_dif1/$opex_acc_total1) * 100 : 100;
	    	$opex_acc_per2 = $opex_acc_total2 != 0 ? ($opex_acc_dif2/$opex_acc_total2) * 100 : 100;

	    	$opex_per_account_tbl .= '
	    		<tr>
	    			<td>' . $row_opex_acc->gl_sub_name . '</td>
	    			<td align="right">' . number_format($opex_acc_total/1000) . '</td>
	    			<td align="right">' . number_format($opex_acc_total1/1000) . '</td>
	    			<td align="right">' . number_format($opex_acc_total2/1000) . '</td>
	    			<td align="right">' . number_format(($opex_acc_dif1/1000) * -1) . '</td>
	    			<td align="right">' . number_format($opex_acc_per1 * -1) . '%</td>
	    			<td align="right">' . number_format(($opex_acc_dif2/1000) * -1) . '</td>
	    			<td align="right">' . number_format($opex_acc_per2 * -1) . '%</td>
	    		</tr>

	    	';

	    	if($count_opex_account%19 == 0 && count($get_opex_account) != $count_opex_account){
	    		$opex_per_account_tbl .= '</table>';
	    		$pdf->writeHTML($opex_per_account_tbl, true, false, true, false, '');

	    		$this->watermark_landscape($pdf);
				$this->header_landscape($pdf);

				$pdf->AddPage('L');
				$pdf->SetTextColor(0,0,0);

			    $pdf->setFont('arialb', '', 12);
			    $pdf->Text(20, 40, $bc_name . ' SUPPORT CENTER');
			    $pdf->Text(20, 46, 'COMPARATIVE OPERATING EXPENSES PER ACCOUNT');
			    $pdf->Text(20, 52, 'Budget ' . $year);

			    $pdf->SetFont('arial', '', 8);

		    	$opex_per_account_tbl = '<br /><br /><br /><br />
			    	<table border="1" cellpadding="3">
			    		<tr>
			    			<th rowspan="2" align="center" width="30%"><strong>GL DESCRIPTION</strong></th>
			    			<th align="center" width="10%"><strong>BUDGET</strong></th>
			    			<th colspan="2" align="center" width="20%"><strong>ACTUAL</strong></th>
			    			<th colspan="4" align="center" width="40%"><strong>VARIANCE</strong></th>
			    		</tr>

			    		<tr>
			    			<td align="center"><strong>' . $year . '</strong></td>
			    			<td align="center"><strong>' . ($year - 1) . '</strong></td>
			    			<td align="center"><strong>' . ($year - 2) . '</strong></td>
			    			<td align="center"><strong>' . $year  . ' vs ' . ($year - 1) . '</strong></td>
			    			<td align="center"><strong>%</strong></td>
			    			<td align="center"><strong>' . ($year - 1) . ' vs ' . ($year - 2) . '</strong></td>
			    			<td align="center"><strong>%</strong></td>
			    		</tr>
			    ';
		    }elseif(count($get_opex_account) == $count_opex_account){

		    	$opex_acc_overall_dif1 = $opex_acc_overall - $opex_acc_overall1;
		    	$opex_acc_overall_dif2 = $opex_acc_overall - $opex_acc_overall2;
		    	$opex_acc_overall_per1 = $opex_acc_overall1 != 0 ? ($opex_acc_overall_dif1/$opex_acc_overall1) * 100 : 100;
		    	$opex_acc_overall_per2 = $opex_acc_overall2 != 0 ? ($opex_acc_overall_dif2/$opex_acc_overall2) * 100 : 100;
		    	$opex_per_account_tbl .= '
	    		<tr>
	    			<td><strong>Total:</strong></td>
	    			<td align="right"><strong>' . number_format($opex_acc_overall/1000) . '</strong></td>
	    			<td align="right"><strong>' . number_format($opex_acc_overall1/1000) . '</strong></td>
	    			<td align="right"><strong>' . number_format($opex_acc_overall2/1000) . '</strong></td>
	    			<td align="right"><strong>' . number_format(($opex_acc_overall_dif1/1000) * -1) . '</strong></td>
	    			<td align="right"><strong>' . number_format($opex_acc_overall_per1 * -1) . '%</strong></td>
	    			<td align="right"><strong>' . number_format(($opex_acc_overall_dif2/1000) * -1) . '</strong></td>
	    			<td align="right"><strong>' . number_format($opex_acc_overall_per2 * -1) . '%</strong></td>
	    		</tr>

	    	';

		    	$opex_per_account_tbl .= '</table>';
	    		$pdf->writeHTML($opex_per_account_tbl, true, false, true, false, '');

	    		$this->watermark_landscape($pdf);
				$this->header_landscape($pdf);
		    }

		    $count_opex_account++;
	    }

	    /*$opex_per_account_tbl .= '</table>';
	    $pdf->writeHTML($opex_per_account_tbl, true, false, true, false, '');*/




	    $this->watermark_landscape($pdf);
		$this->header_landscape($pdf);


		/*CAPITAL EXPENDITURES SUMMARY PER CATEGORY Page*/

		$pdf->AddPage('P');

		$pdf->SetTextColor(0,0,0);

	    $pdf->setFont('arialb', '', 12);


	    $pdf->Text(20, 40, $bc_name . ' SUPPORT CENTER');
	    $pdf->Text(20, 46, 'CAPITAL EXPENDITURES SUMMARY PER CATEGORY');
	    $pdf->Text(20, 52, 'Budget ' . $year);

	    $pdf->SetFont('arial', '', 8);

	    $get_capex_category = $this->_get_capex_category_report($year, $cost_center_id, $company_unit_id);

	    $capex_category_tbl = '<br /><br /><br /><br />
	    	<table border="1" cellpadding="3">
	    		<tr>
	    			<th align="center" width="30%"><strong>ASSET TYPE</strong></th>
	    			<th align="center" width="10%"><strong>' . $year .'</strong></th>
	    			<th align="center" width="10%"><strong>' . ($year - 1) . '</strong></th>
	    			<th align="center" width="10%"><strong>' . ($year - 2) . '</strong></th>
	    			<th align="center" width="10%"><strong>Fav/(UnFav)</strong></th>
	    			<th align="center" width="10%"><strong>%</strong></th>
	    			<th align="center" width="10%"><strong>Fav/(UnFav)</strong></th>
	    			<th align="center" width="10%"><strong>%</strong></th>
	    		</tr>
	    ';
	    $capex_grand_total = 0;
	    $capex_grand_total1 = 0;
	    $capex_grand_total2 = 0;

	    foreach($get_capex_category as $row_capex_cat){
	    	$capex_total = $row_capex_cat->capex_total;
	    	$capex_total1 = $row_capex_cat->capex_total1;
	    	$capex_total2 = $row_capex_cat->capex_total2;

	    	$capex_grand_total += $capex_total;
	    	$capex_grand_total1 += $capex_total1;
	    	$capex_grand_total2 += $capex_total2;

	    	$capex_dif1 = $capex_total - $capex_total1;
	    	$capex_dif2 = $capex_total - $capex_total2;
	    	$capex_per1 = $capex_total1 != 0 ? ($capex_dif1/$capex_total1) * 100 : 0;
	    	$capex_per2 = $capex_total2 != 0 ? ($capex_dif2/$capex_total2) * 100 : 0;

	    	$capex_category_tbl .= '
	    		<tr>
	    			<td>' . $row_capex_cat->ag_name . '</td>
	    			<td align="right">' . number_format($capex_total/1000) . '</td>
	    			<td align="right">' . number_format($capex_total1/1000) . '</td>
	    			<td align="right">' . number_format($capex_total2/1000) . '</td>
	    			<td align="right">' . number_format($capex_dif1/1000) . '</td>
	    			<td align="right">' . number_format($capex_per1) . '%</td>
	    			<td align="right">' . number_format($capex_dif2/1000) . '</td>
	    			<td align="right">' . number_format($capex_per2) . '%</td>
	    		</tr>

	    	';
	    }

	    $capex_total_dif1 = $capex_grand_total - $capex_grand_total1;
	    $capex_total_dif2 = $capex_grand_total - $capex_grand_total2;
	    $capex_total_per1 = $capex_grand_total1 != 0 ? ($capex_total_dif1/$capex_grand_total1) * 100 : 0;
	    $capex_total_per2 = $capex_grand_total2 != 0 ? ($capex_total_dif2/$capex_grand_total2) * 100 : 0;

	    $capex_category_tbl .= '
	    	<tr>
	    		<td><strong>TOTAL</strong></td>
	    		<td align="right"><strong>' . number_format($capex_grand_total/1000) . '</strong></td>
	    		<td align="right"><strong>' . number_format($capex_grand_total1/1000) . '</strong></td>
	    		<td align="right"><strong>' . number_format($capex_grand_total2/1000) . '</strong></td>
	    		<td align="right"><strong>' . number_format($capex_total_dif1/1000) . '</strong></td>
	    		<td align="right"><strong>' . number_format($capex_total_per1) . '%</strong></td>
	    		<td align="right"><strong>' . number_format($capex_total_dif2/1000) . '</strong></td>
	    		<td align="right"><strong>' . number_format($capex_total_per2) . '%</strong></td>
	    	</tr>
	    ';

	    $capex_category_tbl .= '</table>';


	    $pdf->writeHTML($capex_category_tbl, true, false, true, false, '');


	    $this->watermark($pdf);
		$this->header($pdf);


		/*CAPITAL EXPENDITURES Page*/

		$pdf->AddPage('P');

		$pdf->SetTextColor(0,0,0);

	    $pdf->setFont('arialb', '', 12);
	    $pdf->Text(20, 40, $bc_name . ' SUPPORT CENTER');
	    $pdf->Text(20, 46, 'CAPITAL EXPENDITURES');
	    $pdf->Text(20, 52, 'Budget ' . $year);

	    $pdf->SetFont('arial', '', 10);

	    $get_capex = $this->_get_capex_item_report($year, $cost_center_id, $company_unit_id);

	    $capex_tbl = '<br /><br /><br /><br />
	    	<table border="1" cellpadding="3">
	    		<tr>
	    			<th align="center" width="30%"><strong>ASSET GROUP</strong></th>
	    			<th align="center" width="30%"><strong>ITEM DESCRIPTION</strong></th>
	    			<th align="center" width="12%"><strong>QTY</strong></th>
	    			<th align="center" width="13%"><strong>COST</strong></th>
	    			<th align="center" width="15%"><strong>AMOUNT</strong></th>
	    		</tr>
	    ';
	    $count_capex = 1;
	    $total_capex = 0;
	    foreach($get_capex as $row_capex){

	    	$total_capex += $row_capex->capex_total;

	    	$capex_tbl .= '
	    		<tr>
	    			<td>' . $row_capex->ag_name . '</td>
	    			<td>' . $row_capex->asg_name . '</td>
	    			<td align="right">' . number_format($row_capex->total_qty) . '</td>
	    			<td align="right">' . number_format($row_capex->capex_price) . '</td>
	    			<td align="right">' . number_format($row_capex->capex_total) . '</td>
	    		</tr>
	    	';

	    	if($count_capex%16 == 0 && count($get_capex) != $count_capex){
	    		$capex_tbl .= '</table>';
	    		$pdf->writeHTML($capex_tbl, true, false, true, false, '');

	    		$this->watermark($pdf);
				$this->header($pdf);

				$pdf->AddPage('P');

				$pdf->SetTextColor(0,0,0);

			    $pdf->setFont('arialb', '', 12);
			    $pdf->Text(20, 40, $bc_name . ' SUPPORT CENTER');
			    $pdf->Text(20, 46, 'CAPITAL EXPENDITURES');
			    $pdf->Text(20, 52, 'Budget ' . $year);

			    $pdf->SetFont('arial', '', 10);

			    $capex_tbl = '<br /><br /><br /><br />
			    	<table border="1" cellpadding="3">
			    		<tr>
			    			<th align="center" width="30%"><strong>ASSET GROUP</strong></th>
			    			<th align="center" width="30%"><strong>ITEM DESCRIPTION</strong></th>
			    			<th align="center" width="12%"><strong>QTY</strong></th>
			    			<th align="center" width="13%"><strong>COST</strong></th>
			    			<th align="center" width="15%"><strong>AMOUNT</strong></th>
			    		</tr>
			    ';
		    }elseif(count($get_capex) == $count_capex){
		    	$capex_tbl .= '<tr><td align="right" colspan="5"><strong>' . number_format($total_capex) .'</strong></td></tr>';
		    	$capex_tbl .= '</table>';
	    		$pdf->writeHTML($capex_tbl, true, false, true, false, '');

	    		$this->watermark($pdf);
				$this->header($pdf);
		    }

		    $count_capex++;
	    }

		/*MANPOWER Page*/

		$pdf->AddPage();

		$pdf->SetTextColor(0,0,0);

	    $pdf->setFont('arialb', '', 12);
	    $pdf->Text(20, 40, $bc_name . ' SUPPORT CENTER');
	    $pdf->Text(20, 46, 'MANPOWER SUMMARY');
	    $pdf->Text(20, 52, 'Budget ' . $year);

	    $pdf->SetFont('arial', '', 8);

	    $manpower_tbl = '<br /><br /><br /><br />
	    	<table border="1" cellpadding="3">
	    		<tr>
	    			<th align="center"><strong>DEPARTMENT</strong></th>
	    			<th align="center"><strong>POSITION</strong></th>
	    			<th align="center"><strong>RANK</strong></th>
	    			<th align="center"><strong>' . ($year - 1) . '</strong></th>
	    			<th align="center"><strong>ADDL</strong></th>
	    			<th align="center"><strong>' . $year . '</strong></th>
	    		</tr>
	    ';

	     $join_manpower = array(
			'cost_center_tbl b' => 'a.cost_center_id = b.cost_center_id AND a.manpower_status = 1 AND a.manpower_year = ' . $year . ' AND (b.parent_id = ' . $cost_center_id . ' OR b.cost_center_id = ' . $cost_center_id . ')',
			'company_unit_tbl c' => 'b.company_unit_id = c.company_unit_id',
			'rank_tbl d' => 'a.rank_id = d.rank_id'
		);

	    $get_manpower = $this->admin->get_join('manpower_tbl a', $join_manpower);
	    $manpower_grandtotal = 0;
	    $manpower_old_total = 0;
	    $manpower_new_total = 0;
	    foreach($get_manpower as $row_manpower){
	    	$manpower_old = $row_manpower->manpower_old;
	    	$manpower_new = $row_manpower->manpower_new;
	    	$manpower_total = $manpower_old + $manpower_new;

	    	$manpower_old_total += $manpower_old;
	    	$manpower_new_total += $manpower_new;
	    	$manpower_grandtotal += $manpower_total;
	    	$manpower_tbl .= '
	    		<tr>
	    			<td>' . $row_manpower->company_unit_name . '</td>
	    			<td>' . $row_manpower->manpower_position . '</td>
	    			<td>' . $row_manpower->rank_name . '</td>
	    			<td align="center">' . $manpower_old . '</td>
	    			<td align="center">' . $manpower_new . '</td>
	    			<td align="center">' . $manpower_total . '</td>
	    		</tr>
	    	';
	    }
	    $manpower_tbl .= '
	    	<tr>
	    		<td></td>
	    		<td></td>
	    		<td></td>
	    		<td align="center"><strong>' . $manpower_old_total . '</strong></td>
	    		<td align="center"><strong>' . $manpower_new_total . '</strong></td>
	    		<td align="center"><strong>' . $manpower_grandtotal . '</strong></td>
	    	</tr>
	    ';

	    $manpower_tbl .= '</table>';
	    $pdf->writeHTML($manpower_tbl, true, false, true, false, '');

	    $this->watermark($pdf);
		$this->header($pdf);

	    //$this->footer($pdf);

	    

	    //$this->header($pdf);
	    //$this->footer($pdf);

   		$pdf->Output('BAVI Budgeting ' . $year . ' - ' . date('Y/m/d'). '.pdf', 'I');   	
	}

	public function _get_opex_report($year, $cost_center_id, $company_unit_id){
		$designated_tbl = $this->_get_designated_tbl($company_unit_id);

		$opex_gl = $this->admin->get_query('

			SELECT opex.gl_group_name, opex.gl_sub_name, opex.gl_code, SUM(total) as total, SUM(total1) as total1, SUM(total2) as total2 
			FROM(

				(SELECT `e`.`gl_group_name`, `d`.`gl_sub_name`, d.gl_code, SUM(a.opex_amount) total, 0 as total1, 0 as total2

				FROM `'.$designated_tbl->gl_transaction_details_tbl.'` `a` JOIN `'.$designated_tbl->gl_transaction_item_tbl.'` `b` ON `a`.`gl_trans_item_id` = `b`.`gl_trans_item_id` AND `b`.`gl_trans_item_status` = 1 AND `a`.`gl_trans_det_status`=1 JOIN `'.$designated_tbl->gl_transaction_tbl.'` `c` ON `b`.`gl_trans_id`=`c`.`gl_trans_id` AND `c`.`gl_trans_status`=1 AND `c`.`gl_year`= ' . $year . ' JOIN `gl_subgroup_tbl` `d` ON `b`.`gl_sub_id`=`d`.`gl_sub_id` JOIN `gl_group_tbl` `e` ON `d`.`gl_group_id` = `e`.`gl_group_id` AND `e`.`gl_group_show` = 1 JOIN `cost_center_tbl` `f` ON `b`.`cost_center_id` = `f`.`cost_center_id` AND `f`.`cost_center_id` AND (`f`.`parent_id`=' . $cost_center_id . ' OR f.cost_center_id = ' . $cost_center_id . ') GROUP BY `d`.`gl_code` ORDER BY `e`.`gl_group_name`
				)

				UNION

				(SELECT comp_tbl.gl_group_name, comp_tbl.gl_sub_name, comp_tbl.gl_code, SUM(comp_tbl.total) as total, SUM(comp_tbl.total1) as total1, SUM(comp_tbl.total2) as total2

				FROM
					(SELECT z.gl_group_name, y.gl_sub_name, y.gl_code, 0 as total, SUM(x.cost) as total1, 0 as total2 FROM comparative_opex_dept_tbl x, gl_subgroup_tbl y, gl_group_tbl z WHERE x.gl_sub_id = y.gl_sub_id AND y.gl_group_id = z.gl_group_id AND x.company_unit_id = ' . $company_unit_id . ' AND x.comp_opex_dept_status = 1 AND YEAR(x.trans_year) = '  . ($year - 1) . ' GROUP BY y.gl_code

					UNION


					SELECT z.gl_group_name, y.gl_sub_name, y.gl_code, 0 as total, 0 as total1, SUM(x.cost) as total2 FROM comparative_opex_dept_tbl x, gl_subgroup_tbl y, gl_group_tbl z WHERE x.gl_sub_id = y.gl_sub_id AND y.gl_group_id = z.gl_group_id AND x.company_unit_id = ' . $company_unit_id . ' AND x.comp_opex_dept_status = 1 AND YEAR(x.trans_year) = '  . ($year - 2) . ' GROUP BY y.gl_code
					) as comp_tbl

				GROUP BY comp_tbl.gl_code
				)
				) as opex
			

		', TRUE);

		$opex_total = 0;
		$opex_total1 = 0;
		$opex_total2 = 0;
		if(!empty(check_count($opex_gl))){
			$opex_total += $opex_gl->total;
			$opex_total1 += $opex_gl->total1;
			$opex_total2 += $opex_gl->total2;
		}

		$depre_sub = $this->get_depreciation_subgroup($cost_center_id, $year, $company_unit_id);
		$depre = "DEPRECIATION EXPENSES";
		foreach($depre_sub as $row_sub){
			$gl_sub_name = $row_sub->gl_sub_name;
			$depre_amount = $row_sub->total;
			$arr_depre = new stdClass;
			$arr_depre->gl_group_name = $depre;
			$arr_depre->gl_sub_name = $gl_sub_name;
			$arr_depre->total = $depre_amount;
			$arr_depre->total1 = 0;
			$arr_depre->total2 = 0;

			$opex_total += $depre_amount;
		}

		$opex_condition1 = '';
 		$opex_indicator1 = '';
 		if($opex_total >= $opex_total1){
 			$opex_condition1 = 'higher';
 			$opex_indicator1 = 'fa fa-long-arrow-up';
 			$opex_dif1 = $opex_total - $opex_total1;
 		}elseif($opex_total < $opex_total1){
 			$opex_condition1 = 'lower';
 			$opex_indicator1 = 'fa fa-long-arrow-down';
 			$opex_dif1 = $opex_total1 - $opex_total;
 		}

 		$opex_condition2 = '';
 		$opex_indicator2 = '';
 		if($opex_total >= $opex_total2){
 			$opex_condition2 = 'higher';
 			$opex_indicator2 = 'fa fa-long-arrow-up';
 			$opex_dif2 = $opex_total - $opex_total2;
 		}elseif($opex_total < $opex_total2){
 			$opex_condition2 = 'lower';
 			$opex_indicator2 = 'fa fa-long-arrow-down';
 			$opex_dif2 = $opex_total2 - $opex_total;
 		}


		$data['opex'] = $opex_total;
		$data['opex1'] = $opex_total1;
		$data['opex2'] = $opex_total2;
		$data['opex_condition1'] = $opex_condition1;
		$data['opex_condition2'] = $opex_condition2;
		$data['opex_indicator1'] = $opex_indicator1;
		$data['opex_indicator2'] = $opex_condition1;
		$data['opex_dif1'] = $opex_dif1;
		$data['opex_dif2'] = $opex_dif2;

		return $data;
	}

	public function _get_sales_unit_report($year, $bc_id){
		/*Sales Unit*/
 		
	}

	public function _get_unit_report($year, $cost_center_id, $opex_data){

		
		$join = array(
			'sales_item_tbl b' => 'a.sales_id = b.sales_id AND a.sales_status = 1 AND b. sales_item_status',
			'sales_details_tbl c' => 'b.sales_item_id = c.sales_item_id AND c.sales_det_status = 1',
			'material_tbl d' => 'b.material_id = d.material_id',
			'material_unit_tbl e' => 'd.material_id = e.material_id AND e.material_unit_status = 1',
			'outlet_tbl f' => 'a.outlet_id = f.outlet_id',
			'bc_tbl g' => 'f.bc_id = g.bc_id AND a.sales_year = ' . $year 
		);

		$get_unit = $this->admin->get_join('sales_tbl a', $join, TRUE, FALSE, FALSE, 'd.material_code, d.material_desc, SUM(c.sales_det_qty), e.sales_unit_equivalent as sales_unit, SUM(c.sales_det_qty) / e.sales_unit_equivalent as sales_unit,

			(SELECT SUM(comp_pnl_sales_volume) FROM comparative_pnl_tbl WHERE comp_pnl_status = 1 AND comp_pnl_year = ' . ($year - 1) . ') as sales_unit1,

			(SELECT SUM(comp_pnl_sales_volume) FROM comparative_pnl_tbl WHERE comp_pnl_status = 1 AND comp_pnl_year = ' . ($year - 2) . ') as sales_unit2
		');

		$sales_unit = 0;
		$sales_unit1 = 0;
		$sales_unit2 = 0;
		if(!empty(check_count($get_unit))){
			$sales_unit = $get_unit->sales_unit;
			$sales_unit1 = $get_unit->sales_unit1;
			$sales_unit2 = $get_unit->sales_unit2;
		}

		$opex = $opex_data['opex'];
		$opex1 = $opex_data['opex1'];
		$opex2 = $opex_data['opex2'];

		$per_unit = $sales_unit != 0 ? $opex / $sales_unit : 0;
		$per_unit1 = $sales_unit1 != 0 ? $opex1 / $sales_unit1 : 0;
		$per_unit2 = $sales_unit2 != 0 ? $opex2 / $sales_unit2 : 0;


		$per_unit_condition1 = 0;
 		$per_unit_indicator1 = 0;
 		$per_unit_dif1 = 0;
 		$per_unit_percent1 = 0;
 		if($per_unit >= $per_unit1){
 			$per_unit_condition1 = 'higher';
 			$per_unit_indicator1 = 'fa fa-long-arrow-up';
 			$per_unit_dif1 = $per_unit - $per_unit1;
 			$per_unit_percent1 = $per_unit1 != 0 ? ($per_unit_dif1 / $per_unit1) * 100 : 0;

 		}elseif($per_unit < $per_unit1){
 			$per_unit_condition1 = 'lower';
 			$per_unit_indicator1 = 'fa fa-long-arrow-down';
 			$per_unit_dif1 = $per_unit - $per_unit1;
 			$per_unit_percent1 = $per_unit1 != 0 ? ($per_unit_dif1 / $per_unit1) * 100 : 0;
 		}

 		$per_unit_condition2 = 0;
 		$per_unit_indicator2 = 0;
 		$per_unit_dif2 = 0;
 		$per_unit_percent2 = 0;
 		if($per_unit >= $per_unit2){
 			$per_unit_condition2 = 'higher';
 			$per_unit_indicator2 = 'fa fa-long-arrow-up';
 			$per_unit_dif2 = $per_unit - $per_unit2;
 			$per_unit_percent2 = $per_unit2 != 0 ? ($per_unit_dif2 / $per_unit2) * 100 : 0;

 		}elseif($per_unit < $per_unit2){
 			$per_unit_condition2 = 'lower';
 			$per_unit_indicator2 = 'fa fa-long-arrow-down';
 			$per_unit_dif2 = $per_unit - $per_unit2;
 			$per_unit_percent2 = $per_unit2 != 0 ? ($per_unit_dif2 / $per_unit2) * 100 : 0;
 		}

		$data['per_unit'] = $per_unit;
		$data['per_unit1'] = $per_unit1;
		$data['per_unit2'] = $per_unit2;
		$data['per_unit_condition1'] = $per_unit_condition1;
		$data['per_unit_condition2'] = $per_unit_condition2;
		$data['per_unit_indicator1'] = $per_unit_indicator1;
		$data['per_unit_indicator2'] = $per_unit_condition1;
		$data['per_unit_dif1'] = $per_unit_dif1;
		$data['per_unit_dif2'] = $per_unit_dif2;
		$data['per_unit_percent1'] = $per_unit_percent1;
		$data['per_unit_percent2'] = $per_unit_percent2;

		return $data;
	}

	public function _get_manpower_report($year, $cost_center_id){
		$join_manpower = array('cost_center_tbl b' => 'a.cost_center_id = b.cost_center_id');
		$where = '(b.parent_id = ' .  $cost_center_id . ' OR b.cost_center_id = ' . $cost_center_id . ') AND a.manpower_status = 1 AND a.manpower_year = ' . $year;
		$select = 'SUM(a.manpower_old) as manpower_old, SUM(a.manpower_new) as manpower_new';
		$get_manpower = $this->admin->get_join('manpower_tbl a', $join_manpower, TRUE, FALSE, FALSE, $select, $where);
		$manpower_old = $get_manpower->manpower_old;
		$manpower_new = $get_manpower->manpower_new;

		$data['manpower_old'] = $manpower_old;
		$data['manpower_new'] = $manpower_new;

		return $data;
	}


	public function _get_capex_report($year, $cost_center_id, $company_unit_id){

		$capex = $this->capex_report($cost_center_id, $year, $company_unit_id);
 		$capex_data = $capex->total_capex;
 		$capex_data1 = $capex->total_capex1;
 		$capex_data2 = $capex->total_capex2;
 		$last_year = $year - 1;

 		$capex_condition1 = '';
 		$capex_indicator1 = '';
 		if($capex_data >= $capex_data1){
 			$capex_condition1 = 'higher';
 			$capex_indicator1 = 'fa fa-long-arrow-up';
 			$capex_dif1 = $capex_data - $capex_data1;
 		}elseif($capex_data < $capex_data1){
 			$capex_condition1 = 'lower';
 			$capex_indicator1 = 'fa fa-long-arrow-down';
 			$capex_dif1 = $capex_data1 - $capex_data;
 		}

 		$capex_condition2 = '';
 		$capex_indicator2 = '';
 		if($capex_data >= $capex_data2){
 			$capex_condition2 = 'higher';
 			$capex_indicator2 = 'fa fa-long-arrow-up';
 			$capex_dif2 = $capex_data - $capex_data2;
 		}elseif($capex_data < $capex_data2){
 			$capex_condition2 = 'lower';
 			$capex_indicator2 = 'fa fa-long-arrow-down';
 			$capex_dif2 = $capex_data2 - $capex_data;
 		}

 		$data['capex'] = $capex_data;
 		$data['capex1'] = $capex_data1;
 		$data['capex2'] = $capex_data2;
 		$data['capex_condition1'] = $capex_condition1;
 		$data['capex_condition2'] = $capex_condition2;
 		$data['capex_indicator1'] = $capex_indicator1;
 		$data['capex_indicator2'] = $capex_indicator2;
 		$data['capex_dif1'] = $capex_dif1;
 		$data['capex_dif2'] = $capex_dif2;

 		return $data;
	}

	public function capex_report($cost_center_id, $year, $company_unit_id){
		$info = $this->_require_login();
		$designated_tbl = $this->_get_designated_tbl($company_unit_id);

		$join = array(
			$designated_tbl->asset_group_transaction_item_tbl.' b' => 'a.ag_trans_id = b.ag_trans_id AND a.ag_trans_status = 1 AND b.ag_trans_item_status = 1 AND a.cost_center_id = ' . $cost_center_id . ' AND a.ag_trans_budget_year = ' . $year,
			$designated_tbl->asset_group_transaction_details_tbl.' c' => 'b.ag_trans_item_id = c.ag_trans_item_id AND c.ag_trans_det_status = 1',
			'asset_subgroup_tbl d' => 'b.asg_id = d.asg_id',
			'asset_group_tbl e' => 'd.ag_id = e.ag_id',
		);

		$capex = $this->admin->get_join($designated_tbl->asset_group_transaction_tbl.' a', $join, TRUE, FALSE, FALSE, '

			SUM(b.capex_price * c.capex_qty) as total_capex,

			(SELECT SUM(x.comp_capex_unit_val) FROM comparative_capex_unit_tbl x WHERE x.company_unit_id = ' . $company_unit_id .' AND x.comp_capex_unit_status = 1 AND x.comp_capex_unit_year = ' . ($year - 1) . ') as total_capex1, 

			(SELECT SUM(x.comp_capex_unit_val) FROM comparative_capex_unit_tbl x WHERE x.company_unit_id = ' . $company_unit_id .' AND x.comp_capex_unit_status = 1 AND x.comp_capex_unit_year = ' . ($year - 2) . ') as total_capex2

			');
		return $capex;
	}

	public function _get_opex_per_account($year, $cost_center_id, $company_unit_id){
		$designated_tbl = $this->_get_designated_tbl($company_unit_id);

		$opex_gl = $this->admin->get_query('

			SELECT opex.gl_group_name, opex.gl_sub_name, opex.gl_code, SUM(total) as total, SUM(total1) as total1, SUM(total2) as total2 
			FROM(

				(SELECT `e`.`gl_group_name`, `d`.`gl_sub_name`, d.gl_code, SUM(a.opex_amount) total, 0 as total1, 0 as total2

				FROM `'.$designated_tbl->gl_transaction_details_tbl.'` `a` JOIN `'.$designated_tbl->gl_transaction_item_tbl.'` `b` ON `a`.`gl_trans_item_id` = `b`.`gl_trans_item_id` AND `b`.`gl_trans_item_status` = 1 AND `a`.`gl_trans_det_status`=1 JOIN `'.$designated_tbl->gl_transaction_tbl.'` `c` ON `b`.`gl_trans_id`=`c`.`gl_trans_id` AND `c`.`gl_trans_status`=1 AND `c`.`gl_year`= ' . $year . ' JOIN `gl_subgroup_tbl` `d` ON `b`.`gl_sub_id`=`d`.`gl_sub_id` JOIN `gl_group_tbl` `e` ON `d`.`gl_group_id` = `e`.`gl_group_id` AND `e`.`gl_group_show` = 1 JOIN `cost_center_tbl` `f` ON `b`.`cost_center_id` = `f`.`cost_center_id` AND `f`.`cost_center_id` AND (`f`.`parent_id`=' . $cost_center_id . ' OR f.cost_center_id = ' . $cost_center_id . ') GROUP BY `d`.`gl_code` ORDER BY `e`.`gl_group_name`
				)

				UNION

				(SELECT comp_tbl.gl_group_name, comp_tbl.gl_sub_name, comp_tbl.gl_code, SUM(comp_tbl.total) as total, SUM(comp_tbl.total1) as total1, SUM(comp_tbl.total2) as total2

				FROM
					(SELECT z.gl_group_name, y.gl_sub_name, y.gl_code, 0 as total, SUM(x.cost) as total1, 0 as total2 FROM comparative_opex_dept_tbl x, gl_subgroup_tbl y, gl_group_tbl z WHERE x.gl_sub_id = y.gl_sub_id AND y.gl_group_id = z.gl_group_id AND x.company_unit_id = ' . $company_unit_id . ' AND x.comp_opex_dept_status = 1 AND YEAR(x.trans_year) = '  . ($year - 1) . ' GROUP BY y.gl_code

					UNION


					SELECT z.gl_group_name, y.gl_sub_name, y.gl_code, 0 as total, 0 as total1, SUM(x.cost) as total2 FROM comparative_opex_dept_tbl x, gl_subgroup_tbl y, gl_group_tbl z WHERE x.gl_sub_id = y.gl_sub_id AND y.gl_group_id = z.gl_group_id AND x.company_unit_id = ' . $company_unit_id . ' AND x.comp_opex_dept_status = 1 AND YEAR(x.trans_year) = '  . ($year - 2) . ' GROUP BY y.gl_code
					) as comp_tbl

				GROUP BY comp_tbl.gl_code
				)
				) as opex
			GROUP BY opex.gl_code

		');

		$depre_sub = $this->get_depreciation_subgroup($cost_center_id, $year, $company_unit_id);
		$depre = "DEPRECIATION EXPENSES";
		foreach($depre_sub as $row_sub){
			$gl_sub_name = $row_sub->gl_sub_name;
			$depre_amount = $row_sub->total;
			$arr_depre = new stdClass;
			$arr_depre->gl_group_name = $depre;
			$arr_depre->gl_sub_name = $gl_sub_name;
			$arr_depre->total = $depre_amount;
			$arr_depre->total1 = 0;
			$arr_depre->total2 = 0;

			$key = array_search($gl_sub_name, array_column($opex_gl, 'gl_sub_name'));
			if(!empty($key)){
				$opex_gl[$key]->total += $depre_amount;
			}else{
				array_push($opex_gl, $arr_depre);	
			}
		}

		return $opex_gl;
	}

	public function _get_capex_category_report($year, $cost_center_id, $company_unit_id){
		$designated_tbl = $this->_get_designated_tbl($company_unit_id);

		$capex_asset = $this->admin->get_query('

			SELECT capex.ag_id, capex.ag_name, SUM(capex.capex_total) as capex_total, SUM(capex.capex_total1) as capex_total1, SUM(capex.capex_total2) as capex_total2  

			FROM
			(
				(
					SELECT `d`.`ag_id`, `d`.`ag_name`, SUM(a.capex_qty * f.capex_price) as capex_total, 0 as capex_total1, 0 as capex_total2 FROM `'.$designated_tbl->asset_group_transaction_details_tbl.'` `a` JOIN `'.$designated_tbl->asset_group_transaction_item_tbl.'` `f` ON `a`.`ag_trans_item_id` = `f`.`ag_trans_item_id` AND `f`.`ag_trans_item_status` = 1 JOIN `'.$designated_tbl->asset_group_transaction_tbl.'` `b` ON `f`.`ag_trans_id`=`b`.`ag_trans_id` AND `b`.`ag_trans_status`=1 AND `a`.`ag_trans_det_status`=1 AND `b`.`ag_trans_budget_year` = ' . $year . ' JOIN `asset_subgroup_tbl` `c` ON `f`.`asg_id`=`c`.`asg_id` JOIN `asset_group_tbl` `d` ON `c`.`ag_id` = `d`.`ag_id` JOIN `cost_center_tbl` `e` ON `f`.`cost_center_id` = `e`.`cost_center_id` AND `e`.`cost_center_id` AND (`e`.`parent_id`= ' . $cost_center_id . ' OR e.cost_center_id = ' . $cost_center_id . ') GROUP BY `d`.`ag_id` ORDER BY `d`.`ag_name`
				)

				UNION

				(
					SELECT y.ag_id, y.ag_name, 0 as capex_total, SUM(x.comp_capex_unit_val) as capex_total1, 0 as capex_total2 FROM comparative_capex_unit_tbl x, asset_group_tbl y WHERE y.ag_id = x.ag_id AND x.company_unit_id = ' . $company_unit_id .' AND x.comp_capex_unit_status = 1 AND x.comp_capex_unit_year = ' . ($year - 1) . ' GROUP BY y.ag_name
				)

				UNION 

				(
					SELECT y.ag_id, y.ag_name, 0 as capex_total, 0 as capex_total1, SUM(x.comp_capex_unit_val)  as capex_total2 FROM comparative_capex_unit_tbl x, asset_group_tbl y WHERE y.ag_id = x.ag_id AND x.company_unit_id = ' . $company_unit_id .' AND x.comp_capex_unit_status = 1 AND x.comp_capex_unit_year = ' . ($year - 2) . ' GROUP BY y.ag_name
				)
			)as capex

			GROUP BY capex.ag_name
		');

		return $capex_asset;
	}

	public function _get_capex_item_report($year, $cost_center_id, $company_unit_id){
		$designated_tbl = $this->_get_designated_tbl($company_unit_id);
		$join_capex = array(
			$designated_tbl->asset_group_transaction_item_tbl.' f' => 'a.ag_trans_item_id = f.ag_trans_item_id AND f.ag_trans_item_status = 1',
			$designated_tbl->asset_group_transaction_tbl.' b' => 'f.ag_trans_id=b.ag_trans_id AND b.ag_trans_status=1 AND a.ag_trans_det_status=1 AND b.ag_trans_budget_year = ' . $year,
			'asset_subgroup_tbl c' => 'f.asg_id=c.asg_id',
			'asset_group_tbl d' => 'c.ag_id = d.ag_id',
			'cost_center_tbl e' => 'f.cost_center_id = e.cost_center_id AND e.cost_center_id AND (e.parent_id=' . $cost_center_id . ' OR e.cost_center_id = ' . $cost_center_id . ')'
		);

		$capex_asset = $this->admin->get_join($designated_tbl->asset_group_transaction_details_tbl.' a', $join_capex, FALSE, 'd.ag_name', 'c.asg_id', 'd.ag_id , d.ag_name, c.asg_name, SUM(a.capex_qty) total_qty, f.capex_price, SUM(a.capex_qty * f.capex_price) as capex_total
		'
		);
		return $capex_asset;
	}

	public function asset_group(){
		$info = $this->_require_login();
		$data['title'] = 'Asset Group';

		$data['asset_group'] = $this->admin->get_data('asset_group_tbl', 'ag_status=1');

		$join_sub = array('asset_group_tbl b' => 'a.ag_id=b.ag_id');
		$data['sub_group'] = $this->admin->get_join('asset_subgroup_tbl a', $join_sub, FALSE, 'asg_status ASC, ag_name, asg_name');
		$data['content'] = $this->load->view('unit/unit_asset_group_content', $data , TRUE);
		$this->load->view('unit/templates', $data);
	}

	public function download_assets(){
		$info = $this->_require_login();

		$join_asset = array('asset_group_tbl b' => 'a.ag_id = b.ag_id');
		$assets = $this->admin->get_join('asset_subgroup_tbl a', $join_asset, FALSE, 'asg_status DESC, b.ag_name, a.asg_name');

		$this->load->library('excel');

		$spreadsheet = $this->excel;
		$spreadsheet->getProperties()->setCreator('BAVI')
				->setLastModifiedBy('Budgeting System')
				->setTitle('List of fixed materials ')
				->setSubject('Fixed Materials')
				->setDescription('List of materials');

		
		$styleArray = array(
				'font' 	=> array(
						'bold' => true,
				),
				'alignment' => array(
						'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
						'vertical' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
				),
				'borders' => array(
						'top' => array(
								'style' => PHPExcel_Style_Border::BORDER_THIN,
						),
				),
				'fill' => array(
						'type' => \PHPExcel_Style_Fill::FILL_GRADIENT_LINEAR,
						'rotation' => 90,
						'startcolor' => array(
								'argb' => 'FFA0A0A0',
						),
						'endcolor' => array(
								'argb' => 'FFFFFFFF',
						),
				),
		);


		foreach(range('A','E') as $columnID) {
			$spreadsheet->getActiveSheet()->getColumnDimension($columnID)
					->setAutoSize(true);
		}

		$style_center = array(
			'font' => array(
				'bold' => true,
				'size' => 20
			),
			'alignment' => array(
				'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
			)
		);

		$style_info =  array(
			'font' => array(
				'bold' => true
			)
		);

		$style_border = array(
			'font' => array(
				'bold' => true
			),
			'alignment' => array(
				'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER
			),
			'borders' => array(
				'allborders' => array(
					'style' => PHPExcel_Style_Border::BORDER_THIN
				)
			)
		);

		$style_data = array(
			'font' => array(
				'bold' => false
			),
			'borders' => array(
				'allborders' => array(
					'style' => PHPExcel_Style_Border::BORDER_THIN
				)
			),
		);

		$style_out = array(
			'font' => array(
				'bold' => true,
				'color' => array('rgb' => 'FF0000')
			),
			'borders' => array(
				'allborders' => array(
					'style' => PHPExcel_Style_Border::BORDER_THIN
				)
			),
			'alignment' => array(
				'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER
			),
		);


		$spreadsheet->getActiveSheet()->getStyle("A1:E1")->applyFromArray($style_border);
		$spreadsheet->getActiveSheet()->getStyle("A1:E1")->applyFromArray($style_info);
		$spreadsheet->setActiveSheetIndex(0)
				->setCellValue("A1", 'Asset Group')
				->setCellValue("B1", "Asset Name")
				->setCellValue("C1", "Useful Life (In Month)")
				->setCellValue("D1", "Cost")
				->setCellValue("E1", "Status")
				;


		// Add some data
		$x= 2;
		$count = 0;
		foreach($assets as $row){

			if($row->asg_status == 0){
				$status = 'INACTIVE';
			}elseif($row->asg_status == 1){
				$status = 'ACTIVE';
			}

			$spreadsheet->setActiveSheetIndex(0)
				->setCellValue("A$x",$row->ag_name)
				->setCellValue("B$x",$row->asg_name)
				->setCellValue("C$x",$row->asg_lifespan)
				->setCellValue("D$x",$row->asg_price)
				->setCellValue("E$x",$status)
			;

			$spreadsheet->getActiveSheet()->getStyle("A$x:E$x")->applyFromArray($style_data);
			$x++;
		}

		$spreadsheet->getActiveSheet()->getStyle('D2:D' . ($x - 1))->getNumberFormat()->setFormatCode('#,##0.00');	
		
		// Rename worksheet
		$spreadsheet->getActiveSheet()->setTitle('List of Assets');

		// set right to left direction
		//		$spreadsheet->getActiveSheet()->setRightToLeft(true);

		// Set active sheet index to the first sheet, so Excel opens this as the first sheet
		$spreadsheet->setActiveSheetIndex(0);

		// Redirect output to a client’s web browser (Excel2007)

		$random = generate_random(5);
		header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
		header('Content-Disposition: attachment;filename="Budgeting - List of Assets_' . $random . '.xlsx"');
		header('Cache-Control: max-age=0');
		// If you're serving to IE 9, then the following may be needed
		header('Cache-Control: max-age=1');

		// If you're serving to IE over SSL, then the following may be needed
		header('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
		header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT'); // always modified
		header('Cache-Control: cache, must-revalidate'); // HTTP/1.1
		header('Pragma: public'); // HTTP/1.0

		$writer = PHPExcel_IOFactory::createWriter($spreadsheet, 'Excel2007');
		$writer->save('php://output');
		exit;
	}

	public function gl_group(){
		$info = $this->_require_login();
		$data['title'] = 'GL Group';

		$data['gl_group'] = $this->admin->get_data('gl_group_tbl');

		$join_sub = array(
			'gl_group_tbl b' => 'a.gl_group_id=b.gl_group_id',
			'gl_class_tbl c' => 'a.gl_class_id = c.gl_class_id'
		);
		$data['sub_group'] = $this->admin->get_join('gl_subgroup_tbl a', $join_sub);
		$data['content'] = $this->load->view('unit/unit_gl_group_content', $data , TRUE);
		$this->load->view('unit/templates', $data);
	}

	public function download_gl(){
		$info = $this->_require_login();

		$join_sub = array(
			'gl_group_tbl b' => 'a.gl_group_id=b.gl_group_id',
			'gl_class_tbl c' => 'a.gl_class_id = c.gl_class_id'
		);
		$sub_group = $this->admin->get_join('gl_subgroup_tbl a', $join_sub, FALSE, 'b.gl_group_name, a.gl_sub_name');

		$this->load->library('excel');

		$spreadsheet = $this->excel;
		$spreadsheet->getProperties()->setCreator('BAVI')
				->setLastModifiedBy('Budgeting System')
				->setTitle('List of fixed materials ')
				->setSubject('Fixed Materials')
				->setDescription('List of materials');

		
		$styleArray = array(
				'font' 	=> array(
						'bold' => true,
				),
				'alignment' => array(
						'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
						'vertical' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
				),
				'borders' => array(
						'top' => array(
								'style' => PHPExcel_Style_Border::BORDER_THIN,
						),
				),
				'fill' => array(
						'type' => \PHPExcel_Style_Fill::FILL_GRADIENT_LINEAR,
						'rotation' => 90,
						'startcolor' => array(
								'argb' => 'FFA0A0A0',
						),
						'endcolor' => array(
								'argb' => 'FFFFFFFF',
						),
				),
		);


		foreach(range('A','D') as $columnID) {
			$spreadsheet->getActiveSheet()->getColumnDimension($columnID)
					->setAutoSize(true);
		}

		$style_center = array(
			'font' => array(
				'bold' => true,
				'size' => 20
			),
			'alignment' => array(
				'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
			)
		);

		$style_info =  array(
			'font' => array(
				'bold' => true
			)
		);

		$style_border = array(
			'font' => array(
				'bold' => true
			),
			'alignment' => array(
				'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER
			),
			'borders' => array(
				'allborders' => array(
					'style' => PHPExcel_Style_Border::BORDER_THIN
				)
			)
		);

		$style_data = array(
			'font' => array(
				'bold' => false
			),
			'borders' => array(
				'allborders' => array(
					'style' => PHPExcel_Style_Border::BORDER_THIN
				)
			),
		);

		$style_out = array(
			'font' => array(
				'bold' => true,
				'color' => array('rgb' => 'FF0000')
			),
			'borders' => array(
				'allborders' => array(
					'style' => PHPExcel_Style_Border::BORDER_THIN
				)
			),
			'alignment' => array(
				'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER
			),
		);


		$spreadsheet->getActiveSheet()->getStyle("A1:E1")->applyFromArray($style_border);
		$spreadsheet->getActiveSheet()->getStyle("A1:E1")->applyFromArray($style_info);
		$spreadsheet->setActiveSheetIndex(0)
				->setCellValue("A1", 'GL Code')
				->setCellValue("B1", "GL Subgroup")
				->setCellValue("C1", "GL Group")
				->setCellValue("D1", "Status")
				->setCellValue("E1", "Classification")
				;


		// Add some data
		$x= 2;
		$count = 0;
		foreach($sub_group as $row){

			if($row->gl_sub_status == 0){
				$status = 'INACTIVE';
			}elseif($row->gl_sub_status == 1){
				$status = 'ACTIVE';
			}

			$spreadsheet->setActiveSheetIndex(0)
				->setCellValue("A$x",$row->gl_code)
				->setCellValue("B$x",$row->gl_sub_name)
				->setCellValue("C$x",$row->gl_group_name)
				->setCellValue("D$x",$status)
				->setCellValue("E$x",$row->gl_class_name)
			;

			$spreadsheet->getActiveSheet()->getStyle("A$x:E$x")->applyFromArray($style_data);
			$x++;
		}
		
		// Rename worksheet
		$spreadsheet->getActiveSheet()->setTitle('List of GL');

		// set right to left direction
		//		$spreadsheet->getActiveSheet()->setRightToLeft(true);

		// Set active sheet index to the first sheet, so Excel opens this as the first sheet
		$spreadsheet->setActiveSheetIndex(0);

		// Redirect output to a client’s web browser (Excel2007)
		$random = generate_random(5);
		header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
		header('Content-Disposition: attachment;filename="Budgeting - List of GL_' . $random . '.xlsx"');
		header('Cache-Control: max-age=0');
		// If you're serving to IE 9, then the following may be needed
		header('Cache-Control: max-age=1');

		// If you're serving to IE over SSL, then the following may be needed
		header('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
		header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT'); // always modified
		header('Cache-Control: cache, must-revalidate'); // HTTP/1.1
		header('Pragma: public'); // HTTP/1.0

		$writer = PHPExcel_IOFactory::createWriter($spreadsheet, 'Excel2007');
		$writer->save('php://output');
		exit;
	}

	/* codes to update start here*/

	public function comparative_data_upload(){
		$info = $this->_require_login();
		$user_info = $this->get_user_info();
		$company_unit_id = $user_info['company_unit_id'];
		$designated_tbl = $this->_get_designated_tbl();
		

		$data['bc'] = $this->admin->get_data('bc_tbl', 'bc_status=1');
		$data['title'] = 'Comparative Data';


		$join = array(
			'company_unit_tbl b' => 'a.company_unit_id = b.company_unit_id and b.company_unit_id = '.$company_unit_id,
			'user_tbl c' => 'a.created_by = c.user_id'
		);
		$order = 'a.comp_capex_unit_year DESC, b.company_unit_name';
		$group = 'a.comp_capex_unit_year, a.company_unit_id';
		$select = 'a.*, b.company_unit_name, CONCAT(c.user_fname," ",c.user_lname) as creator, a.comp_capex_unit_year as trans_year, COUNT(a.comp_capex_unit_id) as trans_count';
		$where = array('a.comp_capex_unit_status' => 1);
		//$where = false;
		$data['comparative_capex_unit'] = $this->admin->get_join('comparative_capex_unit_tbl a', $join, false, $order, $group, $select, $where);


		$join = array(
			'company_unit_tbl b' => 'a.company_unit_id = b.company_unit_id and b.company_unit_id = '.$company_unit_id,
			'user_tbl c' => 'a.created_by = c.user_id'
		);
		$order = 'YEAR(a.trans_year) DESC, b.company_unit_name';
		$group = 'YEAR(a.trans_year), a.company_unit_id';
		$select = 'a.*, b.company_unit_name, CONCAT(c.user_fname," ",c.user_lname) as creator, YEAR(a.trans_year) as trans_year, COUNT(a.comp_opex_dept_id) as trans_count';
		$where = array('a.comp_opex_dept_status' => 1);
		//$where = false;
		$data['comparative_opex_dept'] = $this->admin->get_join('comparative_opex_dept_tbl a', $join, false, $order, $group, $select, $where);



		$join = array(
			'company_unit_tbl b' => 'a.company_unit_id = b.company_unit_id and b.company_unit_id = '.$company_unit_id,
			'user_tbl c' => 'a.user_id = c.user_id'
		);
		$order = 'YEAR(a.depreciation_unit_date) DESC, b.company_unit_name';
		$group = 'YEAR(a.depreciation_unit_date), a.company_unit_id';
		$select = 'a.*, b.company_unit_name, CONCAT(c.user_fname," ",c.user_lname) as creator, YEAR(a.depreciation_unit_date) as trans_year, FORMAT(COUNT(a.depreciation_unit_id)/12, 0) as trans_count';
		$where = array('a.depreciation_unit_status' => 1);
		//$where = false;
		$data['depreciation_unit'] = $this->admin->get_join($designated_tbl->depreciation_unit_tbl.' a', $join, false, $order, $group, $select, $where);

		$data['content'] = $this->load->view('unit/unit_comparative_data_upload_content', $data , TRUE);
		$this->load->view('unit/templates', $data);
	}

	public function download_comparative_capex_unit_temp(){
		$info = $this->_require_login();
		$user_info = $this->get_user_info();
		$company_unit_id = $user_info['company_unit_id'];

		$company_unit = $this->admin->get_data('company_unit_tbl', array('company_unit_status' => 1, 'company_unit_id' => $company_unit_id), false);
		$bc = $this->admin->get_data('bc_tbl', array('bc_status' => 1), false);
		$join = array(
			'company_unit_tbl b' => 'a.company_unit_id = b.company_unit_id and b.company_unit_id = '.$company_unit_id,
			//'cost_center_group_tbl c' => 'a.cost_center_group_id = c.cost_center_group_id',
			'bc_tbl d' => 'a.bc_id = d.bc_id',
			'cost_center_type_tbl e' => 'a.cost_center_type_id = e.cost_center_type_id and e.cost_center_type_id = 2'
		);
		$row_type=FALSE;
		$order=FALSE;
		$group=FALSE;
		$select='b.company_unit_name, d.bc_name, e.*, a.*,';
		$cost_center = $this->admin->get_join('cost_center_tbl a', $join, $row_type, $order, $group, $select, array('cost_center_status' => 1), false);
		
		$asset_group = $this->admin->get_data('asset_group_tbl', array('ag_status' => 1), false);
		
		$this->load->library('excel');
		$spreadsheet = $this->excel;
		$spreadsheet->getProperties()->setCreator('BAVI')
				->setLastModifiedBy('Budgeting System')
				->setSubject('Comparative CAPEX (Unit) Temp')
				->setDescription('Comparative CAPEX (Unit) Temp');
		
		$style_hdr = array(
				'font' 	=> array(
						'bold' => true,
						'color' => array('rgb' => 'ffffff')
				),
				'alignment' => array(
						'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
						'vertical' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
				),
				'borders' => array(
						'allborders' => array(
							'style' => PHPExcel_Style_Border::BORDER_THIN,
							'color' => array('rgb' => 'ffffff')
						)
				),
				'fill' => array(
						'type' => PHPExcel_Style_Fill::FILL_SOLID,
						'startcolor' => array(
							'rgb' => '0066cc'
				    	)
				),
		);

		$style_highlight_row = array(

				'fill' => array(
						'type' => PHPExcel_Style_Fill::FILL_SOLID,
						'startcolor' => array(
							'rgb' => 'cce6ff'
				    	)
				),
		);

		$style_center = array(
			'font' => array(
				'bold' => true,
				'size' => 20
			),
			'alignment' => array(
				'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
			)
		);

		$style_left = array(
			
			'alignment' => array(
				'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_LEFT,
			)
		);

		$style_info =  array(
			'font' => array(
				'bold' => true
			)
		);

		$style_border = array(
			'font' => array(
				'bold' => true
			),
			'alignment' => array(
				'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER
			),
			'borders' => array(
				'allborders' => array(
					'style' => PHPExcel_Style_Border::BORDER_THIN
				)
			)
		);

		$style_border_bold = array(
			'font' => array(
				'bold' => true
			),
			
			'borders' => array(
				'allborders' => array(
					'style' => PHPExcel_Style_Border::BORDER_THIN
				)
			)
		);

		$style_border_normal = array(
			
			'borders' => array(
				'allborders' => array(
					'style' => PHPExcel_Style_Border::BORDER_THIN
				)
			)
		);

		$style_data = array(
			'font' => array(
				'bold' => true
			),
			'borders' => array(
				'allborders' => array(
					'style' => PHPExcel_Style_Border::BORDER_THIN
				)
			),
			'alignment' => array(
				'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER
			),
		);

		$style_data_right = array(
			'font' => array(
				'bold' => false
			),
			'borders' => array(
				'allborders' => array(
					'style' => PHPExcel_Style_Border::BORDER_THIN
				)
			),
			'alignment' => array(
				'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_RIGHT
			),
		);

		$style_out = array(
			'font' => array(
				'bold' => true,
				'color' => array('rgb' => 'FF0000')
			),
			'borders' => array(
				'allborders' => array(
					'style' => PHPExcel_Style_Border::BORDER_THIN
				)
			),
			'alignment' => array(
				'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER
			),
		);

		
		$spreadsheet->setActiveSheetIndex(0)
				->setTitle("Comparative CAPEX (Unit)")
				->getProtection()
				->setPassword('qwertyxxxx')
				->setSheet(false);

		$reportTitle = 'Comparative CAPEX (Unit) Template';
		$reportTitle .= "\nRun Date : ".date_now();

		$spreadsheet->getActiveSheet()->getStyle("A2:G2")->applyFromArray($style_border);
		$spreadsheet->getActiveSheet()->getStyle("A2:G2")->applyFromArray($style_info);
		
		$table_head = array(
			'Unit Name',
			'Cost Center',
			'Cost Center Name',
			'Class Code',
			'Class Name',
			'Acq. Date',
			'Acq. Cost'
		);

		$spreadsheet->setActiveSheetIndex(0)
				->setCellValue('A1', $reportTitle);

		$head = 0;
		foreach($table_head as $value)
		{
			$spreadsheet->getActiveSheet()->setCellValueByColumnAndRow($head, 2, $value);
			$head++;
		}

		$spreadsheet->getActiveSheet()->getStyle('A1')->getAlignment()->setWrapText(true);
		$spreadsheet->getActiveSheet()->getStyle("A2:G2")->applyFromArray($style_hdr);

		foreach(range('A','S') as $columnID) {
			$spreadsheet->getActiveSheet()->getColumnDimension($columnID)
					->setAutoSize(true);
		}
		$spreadsheet->getActiveSheet()->getComment('A3')->getText()->createTextRun('Put only one Unit here and leave it blank on the succeeding fields');



		//SHEET 2
		$spreadsheet->createSheet();
		$spreadsheet->setActiveSheetIndex(1)
				->setTitle("Company Unit")
				->getProtection()
				->setPassword('qwertyxxxx')
				->setSheet(true);

		$table_head = array(
			'Company Unit Name',
			'Cost Center',
			'Status'
        );
        $head = 0;
		foreach($table_head as $value)
		{
			$spreadsheet->getActiveSheet()->setCellValueByColumnAndRow($head, 1, $value);
			$head++;
		}

		$body = 2;//Add some data, row #
		
		foreach($company_unit as $row)
		{
			$spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(0 ,$body, $row->company_unit_name);
			$spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(1 ,$body, $row->cost_center);
			$spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(2 ,$body, 'ACTIVE');

			$body++;
		}
		$cell_hdr = "A1:C1";
		$spreadsheet->getActiveSheet()->getStyle($cell_hdr)->applyFromArray($style_hdr);
		for ($i = 'A'; $i <=  $spreadsheet->getActiveSheet()->getHighestColumn(); $i++) {
		    $spreadsheet->getActiveSheet()->getColumnDimension($i)->setAutoSize( true );
		}


		
		


		

		//SHEET 3
		$spreadsheet->createSheet();
		$spreadsheet->setActiveSheetIndex(2)
				->setTitle("Cost Center")
				->getProtection()
				->setPassword('qwertyxxxx')
				->setSheet(true);

		$table_head = array(
			'Cost Center Description',
			'Cost Center Code',
			'Unit',
			'BC',
			'Type',
			'Group',
			'Status'
        );
        $head = 0;
		foreach($table_head as $value)
		{
			$spreadsheet->getActiveSheet()->setCellValueByColumnAndRow($head, 1, $value);
			$head++;
		}

		$body = 2;//Add some data, row #
		
		foreach($cost_center as $row)
		{
			$spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(0 ,$body, $row->cost_center_desc);
			$spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(1 ,$body, $row->cost_center_code);
			$spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(2 ,$body, $row->company_unit_name);
			$spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(3 ,$body, $row->bc_name);
			$spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(4 ,$body, $row->cost_center_type_name);
			$spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(5 ,$body, '');
			$spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(6 ,$body, 'ACTIVE');

			$body++;
		}
		$cell_hdr = "A1:G1";
		$spreadsheet->getActiveSheet()->getStyle($cell_hdr)->applyFromArray($style_hdr);
		foreach(range('A','G') as $columnID) {
			$spreadsheet->getActiveSheet()->getColumnDimension($columnID)
					->setAutoSize(true);
		}
		$spreadsheet->getActiveSheet()->getStyle('A2:G'.$body)->applyFromArray($style_left);



		//SHEET 4
		$spreadsheet->createSheet();
		$spreadsheet->setActiveSheetIndex(3)
				->setTitle("Asset Group")
				->getProtection()
				->setPassword('qwertyxxxx')
				->setSheet(true);

		$table_head = array(
			'Asset Group Name',
			'Status'
        );
        $head = 0;
		foreach($table_head as $value)
		{
			$spreadsheet->getActiveSheet()->setCellValueByColumnAndRow($head, 1, $value);
			$head++;
		}

		$body = 2;//Add some data, row #
		
		foreach($asset_group as $row)
		{
			$spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(0 ,$body, $row->ag_name);
			
			$spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(1 ,$body, 'ACTIVE');

			$body++;
		}
		$cell_hdr = "A1:B1";
		$spreadsheet->getActiveSheet()->getStyle($cell_hdr)->applyFromArray($style_hdr);
		foreach(range('A','B') as $columnID) {
			$spreadsheet->getActiveSheet()->getColumnDimension($columnID)
					->setAutoSize(true);
		}

		$spreadsheet->setActiveSheetIndex(0);
		ob_end_clean();
		ob_start();
		// Redirect output to a client’s web browser (Excel2007)
		header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
		header('Content-Disposition: attachment;filename="Comparative CAPEX (Unit).xlsx"');
		header('Cache-Control: max-age=0');

		// If you're serving to IE over SSL, then the following may be needed
		header('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
		header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT'); // always modified
		header('Cache-Control: cache, must-revalidate'); // HTTP/1.1
		header('Pragma: public'); // HTTP/1.0

		$writer = PHPExcel_IOFactory::createWriter($spreadsheet, 'Excel2007');
		$writer->save('php://output');
		exit;
	}

	public function download_comparative_opex_dept_temp(){
		$info = $this->_require_login();
		$user_info = $this->get_user_info();
		$company_unit_id = $user_info['company_unit_id'];
		$company_unit = $this->admin->get_data('company_unit_tbl', array('company_unit_status' => 1, 'company_unit_id' => $company_unit_id), false);
		$bc = $this->admin->get_data('bc_tbl', array('bc_status' => 1), false);

		$join = array(
			'gl_group_tbl b' => 'a.gl_group_id = b.gl_group_id'
		);
		$gl_subgroup = $this->admin->get_join($tbl='gl_subgroup_tbl a', $join, $row_type=FALSE, $order=FALSE, $group=FALSE, $select=FALSE, $where=array('gl_sub_status' => 1));

		$join = array(
			'company_unit_tbl b' => 'a.company_unit_id = b.company_unit_id and b.company_unit_id = '.$company_unit_id,
			'cost_center_group_tbl c' => 'a.cost_center_group_id = c.cost_center_group_id',
			'bc_tbl d' => 'a.bc_id = d.bc_id',
			'cost_center_type_tbl e' => 'a.cost_center_type_id = e.cost_center_type_id'
		);
		$row_type=FALSE;
		$order=FALSE;
		$group=FALSE;
		$select=FALSE;
		$cost_center = $this->admin->get_join('cost_center_tbl a', $join, $row_type, $order, $group, $select, array('cost_center_status' => 1), false);

		$asset_group = $this->admin->get_data('asset_group_tbl', array('ag_status' => 1), false);
		
		$this->load->library('excel');
		$spreadsheet = $this->excel;
		$spreadsheet->getProperties()->setCreator('BAVI')
				->setLastModifiedBy('Budgeting System')
				->setSubject('Comparative CAPEX Temp')
				->setDescription('Comparative CAPEX Temp');
		
		$style_hdr = array(
				'font' 	=> array(
						'bold' => true,
						'color' => array('rgb' => 'ffffff')
				),
				'alignment' => array(
						'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
						'vertical' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
				),
				'borders' => array(
						'allborders' => array(
							'style' => PHPExcel_Style_Border::BORDER_THIN,
							'color' => array('rgb' => 'ffffff')
						)
				),
				'fill' => array(
						'type' => PHPExcel_Style_Fill::FILL_SOLID,
						'startcolor' => array(
							'rgb' => '0066cc'
				    	)
				),
		);

		$style_highlight_row = array(

				'fill' => array(
						'type' => PHPExcel_Style_Fill::FILL_SOLID,
						'startcolor' => array(
							'rgb' => 'cce6ff'
				    	)
				),
		);

		$style_center = array(
			'font' => array(
				'bold' => true,
				'size' => 20
			),
			'alignment' => array(
				'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
			)
		);

		$style_left = array(
			
			'alignment' => array(
				'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_LEFT,
			)
		);

		$style_info =  array(
			'font' => array(
				'bold' => true
			)
		);

		$style_border = array(
			'font' => array(
				'bold' => true
			),
			'alignment' => array(
				'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER
			),
			'borders' => array(
				'allborders' => array(
					'style' => PHPExcel_Style_Border::BORDER_THIN
				)
			)
		);

		$style_border_bold = array(
			'font' => array(
				'bold' => true
			),
			
			'borders' => array(
				'allborders' => array(
					'style' => PHPExcel_Style_Border::BORDER_THIN
				)
			)
		);

		$style_border_normal = array(
			
			'borders' => array(
				'allborders' => array(
					'style' => PHPExcel_Style_Border::BORDER_THIN
				)
			)
		);

		$style_data = array(
			'font' => array(
				'bold' => true
			),
			'borders' => array(
				'allborders' => array(
					'style' => PHPExcel_Style_Border::BORDER_THIN
				)
			),
			'alignment' => array(
				'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER
			),
		);

		$style_data_right = array(
			'font' => array(
				'bold' => false
			),
			'borders' => array(
				'allborders' => array(
					'style' => PHPExcel_Style_Border::BORDER_THIN
				)
			),
			'alignment' => array(
				'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_RIGHT
			),
		);

		$style_out = array(
			'font' => array(
				'bold' => true,
				'color' => array('rgb' => 'FF0000')
			),
			'borders' => array(
				'allborders' => array(
					'style' => PHPExcel_Style_Border::BORDER_THIN
				)
			),
			'alignment' => array(
				'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER
			),
		);

		
		$spreadsheet->setActiveSheetIndex(0)
				->setTitle("Comparative OPEX per Unit")
				->getProtection()
				->setPassword('qwertyxxxx')
				->setSheet(false);

		$reportTitle = 'Comparative OPEX per Unit Template';
		$reportTitle .= "\nRun Date : ".date_now();

		
		
		$table_head = array(
			
			'Unit/Department',
			'Cost Center Code',
			'Cost Center Name',
			'GL Account',
			'GL Description',
			'GL Group',
			'Cost'
		);

		$spreadsheet->setActiveSheetIndex(0)
				->setCellValue('A1', $reportTitle);

		$head = 0;
		foreach($table_head as $value)
		{
			$spreadsheet->getActiveSheet()->setCellValueByColumnAndRow($head, 2, $value);
			$head++;
		}

		$spreadsheet->getActiveSheet()->getStyle('A1')->getAlignment()->setWrapText(true);
		$spreadsheet->getActiveSheet()->getStyle("A2:G2")->applyFromArray($style_hdr);

		foreach(range('A','I') as $columnID) {
			$spreadsheet->getActiveSheet()->getColumnDimension($columnID)
					->setAutoSize(true);
		}
		$spreadsheet->getActiveSheet()->getComment('A3')->getText()->createTextRun('Put only one unit per upload template');
		//$spreadsheet->getActiveSheet()->getComment('B3')->getText()->createTextRun('Put only one BC here and leave it blank on the succeeding fields');



		


		//SHEET 2
		$spreadsheet->createSheet();
		$spreadsheet->setActiveSheetIndex(1)
				->setTitle("Company Unit")
				->getProtection()
				->setPassword('qwertyxxxx')
				->setSheet(true);

		$table_head = array(
			'Company Unit Name',
			'Cost Center',
			'Status'
        );
        $head = 0;
		foreach($table_head as $value)
		{
			$spreadsheet->getActiveSheet()->setCellValueByColumnAndRow($head, 1, $value);
			$head++;
		}

		$body = 2;//Add some data, row #
		
		foreach($company_unit as $row)
		{
			$spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(0 ,$body, $row->company_unit_name);
			$spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(1 ,$body, $row->cost_center);
			$spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(2 ,$body, 'ACTIVE');

			$body++;
		}
		$cell_hdr = "A1:C1";
		$spreadsheet->getActiveSheet()->getStyle($cell_hdr)->applyFromArray($style_hdr);
		foreach(range('A','B') as $columnID) {
			$spreadsheet->getActiveSheet()->getColumnDimension($columnID)
					->setAutoSize(true);
		}
		


		

		//SHEET 3
		$spreadsheet->createSheet();
		$spreadsheet->setActiveSheetIndex(2)
				->setTitle("Cost Center")
				->getProtection()
				->setPassword('qwertyxxxx')
				->setSheet(true);

		$table_head = array(
			'Cost Center Description',
			'Cost Center Code',
			'Unit',
			'BC',
			'Type',
			'Group',
			'Status'
        );
        $head = 0;
		foreach($table_head as $value)
		{
			$spreadsheet->getActiveSheet()->setCellValueByColumnAndRow($head, 1, $value);
			$head++;
		}

		$body = 2;//Add some data, row #
		
		foreach($cost_center as $row)
		{
			$spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(0 ,$body, $row->cost_center_desc);
			$spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(1 ,$body, $row->cost_center_code);
			$spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(2 ,$body, $row->company_unit_name);
			$spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(3 ,$body, $row->bc_name);
			$spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(4 ,$body, $row->cost_center_type_name);
			$spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(5 ,$body, $row->cost_center_group_name);
			$spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(6 ,$body, 'ACTIVE');

			$body++;
		}
		$cell_hdr = "A1:G1";
		$spreadsheet->getActiveSheet()->getStyle($cell_hdr)->applyFromArray($style_hdr);
		foreach(range('A','G') as $columnID) {
			$spreadsheet->getActiveSheet()->getColumnDimension($columnID)
					->setAutoSize(true);
		}
		$spreadsheet->getActiveSheet()->getStyle('A2:G'.$body)->applyFromArray($style_left);



		//SHEET 4
		$spreadsheet->createSheet();
		$spreadsheet->setActiveSheetIndex(3)
				->setTitle("GL")
				->getProtection()
				->setPassword('qwertyxxxx')
				->setSheet(true);

		$table_head = array(
			'GL Code',
			'GL Description',
			'GL Group',
			'Status'
        );
        $head = 0;
		foreach($table_head as $value)
		{
			$spreadsheet->getActiveSheet()->setCellValueByColumnAndRow($head, 1, $value);
			$head++;
		}

		$body = 2;//Add some data, row #
		
		foreach($gl_subgroup as $row)
		{
			$spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(0 ,$body, $row->gl_code);
			$spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(1 ,$body, $row->gl_sub_name);
			$spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(2 ,$body, $row->gl_group_name);
			$spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(3 ,$body, 'ACTIVE');

			$body++;
		}
		$cell_hdr = "A1:D1";
		$spreadsheet->getActiveSheet()->getStyle($cell_hdr)->applyFromArray($style_hdr);
		foreach(range('A','D') as $columnID) {
			$spreadsheet->getActiveSheet()->getColumnDimension($columnID)
					->setAutoSize(true);
		}



		$spreadsheet->setActiveSheetIndex(0);
		ob_end_clean();
		ob_start();
		// Redirect output to a client’s web browser (Excel2007)
		header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
		header('Content-Disposition: attachment;filename="Comparative OPEX per unit.xlsx"');
		header('Cache-Control: max-age=0');

		// If you're serving to IE over SSL, then the following may be needed
		header('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
		header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT'); // always modified
		header('Cache-Control: cache, must-revalidate'); // HTTP/1.1
		header('Pragma: public'); // HTTP/1.0

		$writer = PHPExcel_IOFactory::createWriter($spreadsheet, 'Excel2007');
		$writer->save('php://output');
		exit;
	}

	public function download_depreciation_unit_temp(){
		$info = $this->_require_login();
		$user_info = $this->get_user_info();
		$company_unit_id = $user_info['company_unit_id'];

		$company_unit = $this->admin->get_data('company_unit_tbl', array('company_unit_status' => 1, 'company_unit_id' => $company_unit_id), false);
		$bc = $this->admin->get_data('bc_tbl', array('bc_status' => 1), false);
		
		$join = array(
			'gl_group_tbl b' => 'a.gl_group_id = b.gl_group_id'
		);
		$gl_subgroup = $this->admin->get_join($tbl='gl_subgroup_tbl a', $join, $row_type=FALSE, $order=FALSE, $group=FALSE, $select=FALSE, $where=array('gl_sub_status' => 1));

		$join = array(
			'company_unit_tbl b' => 'a.company_unit_id = b.company_unit_id and b.company_unit_id = '.$company_unit_id,
			//'cost_center_group_tbl c' => 'a.cost_center_group_id = c.cost_center_group_id',
			'bc_tbl d, LEFT' => 'a.bc_id = d.bc_id',
			'cost_center_type_tbl e' => 'a.cost_center_type_id = e.cost_center_type_id and e.cost_center_type_id IN (2, 9)'
		);
		$row_type=FALSE;
		$order=FALSE;
		$group=FALSE;
		$select='b.company_unit_name, d.bc_name, e.*, a.*,';
		$cost_center = $this->admin->get_join('cost_center_tbl a', $join, $row_type, $order, $group, $select, array('cost_center_status' => 1), false);
		
		//$asset_group = $this->admin->get_data('asset_group_tbl', array('ag_status' => 1), false);
		
		//$this->load->library('excel');
		//$spreadsheet = $this->excel;
		require_once( APPPATH . "/third_party/PHPExcel-1.8/Classes/PHPExcel.php" );
		$spreadsheet = new PHPExcel();

		$spreadsheet->getProperties()->setCreator('BAVI')
				->setLastModifiedBy('Budgeting System')
				->setSubject('Depreciatin Unit Temp')
				->setDescription('Depreciatin Unit Temp');
		
		$style_hdr = array(
				'font' 	=> array(
						'bold' => true,
						'color' => array('rgb' => 'ffffff')
				),
				'alignment' => array(
						'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
						'vertical' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
				),
				'borders' => array(
						'allborders' => array(
							'style' => PHPExcel_Style_Border::BORDER_THIN,
							'color' => array('rgb' => 'ffffff')
						)
				),
				'fill' => array(
						'type' => PHPExcel_Style_Fill::FILL_SOLID,
						'startcolor' => array(
							'rgb' => '0066cc'
				    	)
				),
		);

		$style_highlight_row = array(

				'fill' => array(
						'type' => PHPExcel_Style_Fill::FILL_SOLID,
						'startcolor' => array(
							'rgb' => 'cce6ff'
				    	)
				),
		);

		$style_center = array(
			'font' => array(
				'bold' => true,
				'size' => 20
			),
			'alignment' => array(
				'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
			)
		);

		$style_left = array(
			
			'alignment' => array(
				'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_LEFT,
			)
		);

		$style_info =  array(
			'font' => array(
				'bold' => true
			)
		);

		$style_border = array(
			'font' => array(
				'bold' => true
			),
			'alignment' => array(
				'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER
			),
			'borders' => array(
				'allborders' => array(
					'style' => PHPExcel_Style_Border::BORDER_THIN
				)
			)
		);

		$style_border_bold = array(
			'font' => array(
				'bold' => true
			),
			
			'borders' => array(
				'allborders' => array(
					'style' => PHPExcel_Style_Border::BORDER_THIN
				)
			)
		);

		$style_border_normal = array(
			
			'borders' => array(
				'allborders' => array(
					'style' => PHPExcel_Style_Border::BORDER_THIN
				)
			)
		);

		$style_data = array(
			'font' => array(
				'bold' => true
			),
			'borders' => array(
				'allborders' => array(
					'style' => PHPExcel_Style_Border::BORDER_THIN
				)
			),
			'alignment' => array(
				'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER
			),
		);

		$style_data_right = array(
			'font' => array(
				'bold' => false
			),
			'borders' => array(
				'allborders' => array(
					'style' => PHPExcel_Style_Border::BORDER_THIN
				)
			),
			'alignment' => array(
				'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_RIGHT
			),
		);

		$style_out = array(
			'font' => array(
				'bold' => true,
				'color' => array('rgb' => 'FF0000')
			),
			'borders' => array(
				'allborders' => array(
					'style' => PHPExcel_Style_Border::BORDER_THIN
				)
			),
			'alignment' => array(
				'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER
			),
		);

		
		$spreadsheet->setActiveSheetIndex(0)
				->setTitle("Depreciation Unit")
				->getProtection()
				->setPassword('qwertyxxxx')
				->setSheet(false);

		$reportTitle = 'Depreciation Unit Template';
		$reportTitle .= "\nRun Date : ".date_now();

		$spreadsheet->getActiveSheet()->getStyle("A2:AF2")->applyFromArray($style_border);
		$spreadsheet->getActiveSheet()->getStyle("A2:AF2")->applyFromArray($style_info);
		
		$table_head = array(
			'Unit Name',
			'Cost Center',
			'Cost Center Name',
			'GL Code',
			'GL Description',
			'GL Group',
			'Asset Code',
			'Asset Name',
			'Quantity',
			'Useful Life',
			'Acq. Date',
			'Acq. Cost',
			'Accum. Depr.',
			'Net Book Value',
			'Currency',
			'Monthly Depr.',
			'Ord. Depr.',
			'Jan',
			'Feb',
			'Mar',
			'Apr',
			'May',
			'Jun',
			'Jul',
			'Aug',
			'Sep',
			'Oct',
			'Nov',
			'Dec'
		);

		$spreadsheet->setActiveSheetIndex(0)
				->setCellValue('A1', $reportTitle);

		$head = 0;
		foreach($table_head as $value)
		{
			$spreadsheet->getActiveSheet()->setCellValueByColumnAndRow($head, 2, $value);
			$head++;
		}

		$spreadsheet->getActiveSheet()->getStyle('A1')->getAlignment()->setWrapText(true);
		$spreadsheet->getActiveSheet()->getStyle("A2:AC2")->applyFromArray($style_hdr);

		foreach(range('A','P') as $columnID) {
			$spreadsheet->getActiveSheet()->getColumnDimension($columnID)
					->setAutoSize(true);
		}
		
		$spreadsheet->getActiveSheet()->getComment('A3')->getText()->createTextRun('Put only one Unit here and leave it blank on the succeeding fields');


		


		//SHEET 2
		$spreadsheet->createSheet();
		$spreadsheet->setActiveSheetIndex(1)
				->setTitle("Company Unit")
				->getProtection()
				->setPassword('qwertyxxxx')
				->setSheet(true);

		$table_head = array(
			'Company Unit Name',
			'Cost Center',
			'Status'
        );
        $head = 0;
		foreach($table_head as $value)
		{
			$spreadsheet->getActiveSheet()->setCellValueByColumnAndRow($head, 1, $value);
			$head++;
		}

		$body = 2;//Add some data, row #
		
		foreach($company_unit as $row)
		{
			$spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(0 ,$body, $row->company_unit_name);
			$spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(1 ,$body, $row->cost_center);
			$spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(2 ,$body, 'ACTIVE');

			$body++;
		}
		$cell_hdr = "A1:C1";
		$spreadsheet->getActiveSheet()->getStyle($cell_hdr)->applyFromArray($style_hdr);
		for ($i = 'A'; $i <=  $spreadsheet->getActiveSheet()->getHighestColumn(); $i++) {
		    $spreadsheet->getActiveSheet()->getColumnDimension($i)->setAutoSize( true );
		}
		


		

		//SHEET 3
		$spreadsheet->createSheet();
		$spreadsheet->setActiveSheetIndex(2)
				->setTitle("Cost Center")
				->getProtection()
				->setPassword('qwertyxxxx')
				->setSheet(true);

		$table_head = array(
			'Cost Center Description',
			'Cost Center Code',
			'Unit',
			'BC',
			'Type',
			'Group',
			'Status'
        );
        $head = 0;
		foreach($table_head as $value)
		{
			$spreadsheet->getActiveSheet()->setCellValueByColumnAndRow($head, 1, $value);
			$head++;
		}

		$body = 2;//Add some data, row #
		
		foreach($cost_center as $row)
		{
			$spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(0 ,$body, $row->cost_center_desc);
			$spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(1 ,$body, $row->cost_center_code);
			$spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(2 ,$body, $row->company_unit_name);
			$spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(3 ,$body, $row->bc_name);
			$spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(4 ,$body, $row->cost_center_type_name);
			$spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(5 ,$body, '');
			$spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(6 ,$body, 'ACTIVE');

			$body++;
		}
		$cell_hdr = "A1:G1";
		$spreadsheet->getActiveSheet()->getStyle($cell_hdr)->applyFromArray($style_hdr);
		for ($i = 'A'; $i <=  $spreadsheet->getActiveSheet()->getHighestColumn(); $i++) {
		    $spreadsheet->getActiveSheet()->getColumnDimension($i)->setAutoSize( true );
		}
		$spreadsheet->getActiveSheet()->getStyle('A2:G'.$body)->applyFromArray($style_left);



		//SHEET 4
		$spreadsheet->createSheet();
		$spreadsheet->setActiveSheetIndex(3)
				->setTitle("GL")
				->getProtection()
				->setPassword('qwertyxxxx')
				->setSheet(true);

		$table_head = array(
			'GL Code',
			'GL Description',
			'GL Group',
			'Status'
        );
        $head = 0;
		foreach($table_head as $value)
		{
			$spreadsheet->getActiveSheet()->setCellValueByColumnAndRow($head, 1, $value);
			$head++;
		}

		$body = 2;//Add some data, row #
		
		foreach($gl_subgroup as $row)
		{
			$spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(0 ,$body, $row->gl_code);
			$spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(1 ,$body, $row->gl_sub_name);
			$spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(2 ,$body, $row->gl_group_name);
			$spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(3 ,$body, 'ACTIVE');

			$body++;
		}
		$cell_hdr = "A1:D1";
		$spreadsheet->getActiveSheet()->getStyle($cell_hdr)->applyFromArray($style_hdr);
		foreach(range('A','D') as $columnID) {
			$spreadsheet->getActiveSheet()->getColumnDimension($columnID)
					->setAutoSize(true);
		}

		$spreadsheet->setActiveSheetIndex(0);
		ob_end_clean();
		ob_start();
		// Redirect output to a client’s web browser (Excel2007)
		header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
		header('Content-Disposition: attachment;filename="Depreciaton Unit.xlsx"');
		header('Cache-Control: max-age=0');

		// If you're serving to IE over SSL, then the following may be needed
		header('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
		header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT'); // always modified
		header('Cache-Control: cache, must-revalidate'); // HTTP/1.1
		header('Pragma: public'); // HTTP/1.0

		$writer = PHPExcel_IOFactory::createWriter($spreadsheet, 'Excel2007');
		$writer->save('php://output');
		exit;
	}


	// UPDATE MULA DITO

	public function upload_comp_capex_unit(){
		$info = $this->_require_login();
		$user_info = $this->get_user_info();
		$company_unit_id_logged = $user_info['company_unit_id'];

		if($_SERVER['REQUEST_METHOD'] == 'POST'){

			$user_id = decode($info['user_id']);
			//$this->load->library('excel');
			$this->load->library('excel_v2');
			ini_set('max_execution_time', 0);
			ini_set('memory_limit','2048M');

			$path = 'assets/comparative-capex-unit/';
			if (!is_dir($path)) {
			    mkdir($path, 0777, TRUE);
			}


			$trans_year = clean_data($this->input->post('comp-capex-unit-year'));

			$config['upload_path'] = $path;
			$config['allowed_types'] = 'xlsx|xls';
			$config['remove_spaces'] = TRUE;
			$this->load->library('upload', $config);
			$this->upload->initialize($config);            
			if (!$this->upload->do_upload('comp-capex-unit-file')) {
				$error = array('error' => $this->upload->display_errors());
			} else {
				$data = array('upload_data' => $this->upload->data());
			}

			if(empty($error)){
				if (!empty($data['upload_data']['file_name'])) {
					$import_xls_file = $data['upload_data']['file_name'];
				} else {
					$import_xls_file = 0;
				}

				$inputFileName = $path . $import_xls_file;
				try {
					$inputFileType = PHPExcel_IOFactory::identify($inputFileName);
					$objReader = PHPExcel_IOFactory::createReader($inputFileType);
					$objPHPExcel = $objReader->load($inputFileName);
					$allDataInSheet = $objPHPExcel->getActiveSheet()->toArray(null, true, false, true);
					$flag = true;
					$i=2; //dating 4
					
					$added = 0;
					
					
		        	$totalKgs = 0;
		        	$overAllTotal = 0;
		        	$msg = '';
		        	$this->db->trans_start();

		        	//echo '<pre>';
		        	//print_r($allDataInSheet);
		        	//echo '</pre>';
		        	//exit();
		        	foreach ($allDataInSheet as $value) {
	        			if($added < 2){ //dating 3
	        				if($added == 1){
				        		if(
					        		empty($value['A']) ||
									empty($value['B']) ||
									empty($value['C']) ||
									empty($value['D']) ||
									empty($value['E']) ||
									empty($value['F']) ||
									empty($value['G']) ||
									!empty($value['H'])
				        		){
				        			$msg = '<div class="alert alert-danger">Error, Invalid Template! Please download the latest one.</div>';
									$this->session->set_flashdata('message', $msg);
									redirect('unit/comparative-data-upload');
				        		}
				        	}
		                  	goto end_capex_unit_here;
		                }

		                $company_unit_name		= strtoupper(clean_data(trim(@$value['A'])));

			        	$cost_center	= clean_data(trim(@$value['B']));
			        	$cost_center_name	= clean_data(trim(@$value['C']));
			        	$class_name	= clean_data(trim(@$value['E']));
			        	//$acq_date = standard_date(clean_data(trim(@$value['F'])));
						$acq_date = $value['F'] ? date('Y-m-d', PHPExcel_Shared_Date::ExcelToPHP( $value['F'] )) : NULL;
						$acq_cost = clean_data(trim(@$value['G']));

						//$acq_cost = $acq_cost * 1;

			        	$i++;

			        	if($added == 2){
				        	$company_unit_id = '';
							$check_company_unit = $this->admin->check_data('company_unit_tbl', array('company_unit_name' => strtoupper($company_unit_name)), TRUE, 'company_unit_id');
							if($check_company_unit['result'] == TRUE){
								$company_unit_id = $check_company_unit['info']->company_unit_id;
								if($company_unit_id_logged != $company_unit_id){
									$msg .= '<div class="alert alert-danger">Unit ('.$company_unit_name.') does not match with your current Unit access! Line number '.$i.'.</div>';
									$this->session->set_flashdata('message', $msg);
									redirect('unit/comparative-data-upload');
								}
								$set = array(
									'comp_capex_unit_status' 	=> 2
								);
								$result =  $this->admin->update_data('comparative_capex_unit_tbl', $set, array('company_unit_id' => $company_unit_id, 'comp_capex_unit_year' => $trans_year));
							}else{
								$msg .= '<div class="alert alert-danger">Company Unit ('.$company_unit_name.') does not exist! Line number '.$i.'.</div>';
								redirect('unit/comparative-data-upload');
							}
			        	}

						$check_asset_group = $this->admin->check_data('asset_group_tbl', array('ag_name' => strtoupper($class_name)), TRUE, 'ag_id');
			        	if($check_asset_group['result'] == TRUE){
							$ag_id = $check_asset_group['info']->ag_id;
						}else{
							
							$msg .= '<div class="alert alert-danger">Class Name ('.$class_name.') does not exist! Line number '.$i.'.</div>';
							goto end_capex_unit_here;
							
						}

						$check_cost_center = $this->admin->check_data('cost_center_tbl', array('cost_center_code' => strtoupper($cost_center), 'company_unit_id' => $company_unit_id), TRUE, 'cost_center_id');
			        	if($check_cost_center['result'] == TRUE){
							$cost_center_id = $check_cost_center['info']->cost_center_id;
						}else{
							$msg .= '<div class="alert alert-danger">Cost Center ('.$cost_center.') does not exist! Line number '.$i.'.</div>';
							goto end_capex_unit_here;
						}

						if(empty($acq_date) || $acq_date==''){
							$msg .= '<div class="alert alert-danger">Acq. Date ('.$acq_date.') is not valid! Line number '.$i.'.</div>';
							goto end_capex_unit_here;
						}


			        	if(!empty($company_unit_id)){

							$set = array(
								'ag_id' => $ag_id,
								'company_unit_id' 		=> @$company_unit_id,
								'cost_center_id' => $cost_center_id,
								'comp_capex_unit_val' => $acq_cost,
								'comp_capex_unit_date'	=> $acq_date,
								'comp_capex_unit_year'	=> $trans_year,
								'comp_capex_unit_status' => 1,
								'filename'		=> $inputFileName,
								'created_by'	=> $user_id,
								'created_ts'	=> date_now()
								
							);
							$result =  $this->admin->insert_data('comparative_capex_unit_tbl', $set, TRUE);
							
						}
		                
						end_capex_unit_here:
		                $added++;
		            }
		            if($this->db->trans_status() === FALSE){
						$this->db->trans_rollback();
						$msg .= '<div class="alert alert-danger">Error please try again!</div>';
						$this->session->set_flashdata('message', $msg);
						redirect('unit/comparative-data-upload');
					}else{
						$this->db->trans_commit();
						$msg .= '<div class="alert alert-success">Data uploaded successfully!</div>';
						$this->session->set_flashdata('message', $msg);
						redirect('unit/comparative-data-upload');
					}

		        } catch (Exception $e) {
					die('Error loading file "' . pathinfo($inputFileName, PATHINFO_BASENAME)
		                  . '": ' .$e->getMessage());

				}
					
			} else {
				$msg = '<div class="alert alert-danger">'.$this->upload->display_errors().'</div>';
				$this->session->set_flashdata('message', $msg);
				redirect('unit/comparative-data-upload');
			}
		} else {
			$msg = '<div class="alert alert-danger">Please contact system administrator!</div>';
			$this->session->set_flashdata('message', $msg);
			redirect('unit/comparative-data-upload');
		}
	}

	public function upload_depreciation_unit(){
		$info = $this->_require_login();
		$user_info = $this->get_user_info();
		$company_unit_id_logged = $user_info['company_unit_id'];
		if($_SERVER['REQUEST_METHOD'] == 'POST'){

			$user_id = decode($info['user_id']);
			//$this->load->library('excel');
			$this->load->library('excel_v2');
			ini_set('max_execution_time', 0);
			ini_set('memory_limit','2048M');

			$path = 'assets/depreciation-unit/';
			if (!is_dir($path)) {
			    mkdir($path, 0777, TRUE);
			}


			$trans_year = clean_data($this->input->post('comp-depreciation-unit-year'));

			$config['upload_path'] = $path;
			$config['allowed_types'] = 'xlsx|xls';
			$config['remove_spaces'] = TRUE;
			$this->load->library('upload', $config);
			$this->upload->initialize($config);            
			if (!$this->upload->do_upload('depreciation-unit-file')) {
				$error = array('error' => $this->upload->display_errors());
			} else {
				$data = array('upload_data' => $this->upload->data());
			}

			if(empty($error)){
				if (!empty($data['upload_data']['file_name'])) {
					$import_xls_file = $data['upload_data']['file_name'];
				} else {
					$import_xls_file = 0;
				}

				$inputFileName = $path . $import_xls_file;
				try {
					$inputFileType = PHPExcel_IOFactory::identify($inputFileName);
					$objReader = PHPExcel_IOFactory::createReader($inputFileType);
					$objPHPExcel = $objReader->load($inputFileName);
					$allDataInSheet = $objPHPExcel->getActiveSheet()->toArray(null, true, false, true);
					$flag = true;
					$i=2; //dating 4
					
					$added = 0;
					
					
		        	$totalKgs = 0;
		        	$overAllTotal = 0;
		        	$msg = '';
		        	$this->db->trans_start();
					$designated_tbl = $this->_get_designated_tbl();

		        	//echo '<pre>';
		        	//print_r($allDataInSheet);
		        	//echo '</pre>';
		        	//exit();
		        	foreach ($allDataInSheet as $value) {
	        			if($added < 2){ //dating 3
	        				if($added == 1){
				        		if(
					        		empty($value['A']) ||
									empty($value['B']) ||
									empty($value['C']) ||
									empty($value['D']) ||
									empty($value['E']) ||
									empty($value['F']) ||
									empty($value['G']) ||
									empty($value['H']) ||
									empty($value['I']) ||
									empty($value['J']) ||
									empty($value['K']) ||
									empty($value['L']) ||
									empty($value['M']) ||
									empty($value['N']) ||
									empty($value['O']) ||
									empty($value['P']) ||
									empty($value['Q']) ||
									empty($value['R']) ||
									empty($value['S']) ||
									empty($value['T']) ||
									empty($value['U']) ||
									empty($value['V']) ||
									empty($value['W']) ||
									empty($value['X']) ||
									empty($value['Y']) ||
									empty($value['Z']) ||
									empty($value['AA']) ||
									empty($value['AB']) ||
									empty($value['AC']) ||
									!empty($value['AD'])
				        		){
				        			$msg = '<div class="alert alert-danger">Error, Invalid Template! Please download the latest one.</div>';
									$this->session->set_flashdata('message', $msg);
									redirect('unit/comparative-data-upload');
				        		}
				        	}
		                  	goto end_depreciation_unit_here;
		                }

		                //$plant			= clean_data(trim(@$value['A']));
			        	$company_unit_name		= strtoupper(clean_data(trim(@$value['A'])));
			        	$cost_center		= clean_data(trim(@$value['B']));
			        	$cost_center_name		= clean_data(trim(@$value['C']));
			        	
			        	$class_code	= clean_data(trim(@$value['D']));
			        	$class_name	= clean_data(trim(@$value['E']));
			        	$gl_group_name	= clean_data(trim(@$value['F']));
			        	$asset_code	= clean_data(trim(@$value['G']));
			        	$asset_name	= clean_data(trim(@$value['H']));
			        	$quantity	= clean_data(trim(@$value['I']));
			        	$useful_life	= clean_data(trim(@$value['J']));
						//$acq_date = standard_date(clean_data(trim(@$value['J'])));
						$acq_date = $value['K'] ? date('Y-m-d', PHPExcel_Shared_Date::ExcelToPHP( $value['K'] )) : NULL;
			        	$acq_cost = clean_data(trim(@$value['L']));
			        	$accum_depr = clean_data(trim(@$value['M']));
			        	$net_book_value = clean_data(trim(@$value['N']));
						$currency = clean_data(trim(@$value['O']));
			        	$monthly_depr = clean_data(trim(@$value['P']));
			        	$ord_depr = clean_data(trim(@$value['Q']));

						$qty = array(
							'1' => clean_data(trim(@$value['R'])),
							'2' => clean_data(trim(@$value['S'])),
							'3' => clean_data(trim(@$value['T'])),
							'4' => clean_data(trim(@$value['U'])),
							'5' => clean_data(trim(@$value['V'])),
							'6' => clean_data(trim(@$value['W'])),
							'7' => clean_data(trim(@$value['X'])),
							'8' => clean_data(trim(@$value['Y'])),
							'9' => clean_data(trim(@$value['Z'])),
							'10' => clean_data(trim(@$value['AA'])),
							'11' => clean_data(trim(@$value['AB'])),
							'12' => clean_data(trim(@$value['AC']))
						);
						


			        	$i++;

			        	if($added == 2){
				        	

							$company_unit_id = '';
							$check_company_unit = $this->admin->check_data('company_unit_tbl', array('company_unit_name' => strtoupper($company_unit_name)), TRUE, 'company_unit_id');
							if($check_company_unit['result'] == TRUE){
								$company_unit_id = $check_company_unit['info']->company_unit_id;
								if($company_unit_id_logged != $company_unit_id){
									$msg .= '<div class="alert alert-danger">Unit ('.$company_unit_name.') does not match with your current Unit access! Line number '.$i.'.</div>';
									$this->session->set_flashdata('message', $msg);
									redirect('unit/comparative-data-upload');
								}
								$set = array(
									'depreciation_unit_status' 	=> 2
								);
								$result =  $this->admin->update_data($designated_tbl->depreciation_unit_tbl, $set, array('company_unit_id' => $company_unit_id, 'YEAR(depreciation_unit_date)' => $trans_year));
							}else{
								$msg .= '<div class="alert alert-danger">Company Unit ('.$company_unit_name.') does not exist! Line number '.$i.'.</div>';
								redirect('unit/comparative-data-upload');
							}
			        	}

			        	$check_cost_center = $this->admin->check_data('cost_center_tbl', array('cost_center_code' => strtoupper($cost_center), 'company_unit_id' => $company_unit_id), TRUE, 'cost_center_id');
			        	if($check_cost_center['result'] == TRUE){
							$cost_center_id = $check_cost_center['info']->cost_center_id;
						}else{
							$msg .= '<div class="alert alert-danger">Cost Center ('.$cost_center.') does not exist! Line number '.$i.'.</div>';
							goto end_depreciation_unit_here;
						}

						
						$filter = array(
							'gl_group_name' => strtoupper($gl_group_name),
							'gl_group_status' => 1
						);
						$check_gl_group = $this->admin->check_data('gl_group_tbl', $filter, TRUE, 'gl_group_id');
			        	if($check_gl_group['result'] == TRUE){
							$gl_group_id = $check_gl_group['info']->gl_group_id;
						}else{
							
							$msg .= '<div class="alert alert-danger">GL Group ('.$gl_group_name.') does not exist! Line number '.$i.'.</div>';
							goto end_depreciation_unit_here;
							
						}


						$filter = array(
							'gl_code' => strtoupper($class_code),
							'gl_group_id' => $gl_group_id,
							'gl_sub_status' => 1
						);
						$check_gl_sub_id = $this->admin->check_data('gl_subgroup_tbl', $filter, TRUE, 'gl_sub_id');
			        	if($check_gl_sub_id['result'] == TRUE){
							$gl_sub_id = $check_gl_sub_id['info']->gl_sub_id;
						}else{
							$gl_sub_id = '';
							
							$msg .= '<div class="alert alert-danger">Class Name ('.$class_name.') does not exist in GL subgroup! Line number '.$i.'.</div>';
							goto end_depreciation_unit_here;
							
						}


			        	if(!empty($company_unit_id)){
							for($month = 1; $month <= 12; $month++){
								//$qty_val = $qty[$month] * 1;
								// $qty_val = str_replace(',', '', $qty[$month]);
								$set = array(
									'company_unit_id' 		=> @$company_unit_id,
									'cost_center_id' => $cost_center_id,
									'gl_sub_id' => $gl_sub_id,
									'depreciation_unit_date'	=> $trans_year.'-'.$month.'-01',
									'depreciation_unit_amount' => $qty[$month],
									'depreciation_unit_added'	=> date_now(),
									'user_id'	=> $user_id,
									'depreciation_unit_status' => 1,
									'filename'		=> $inputFileName
									
								);
								//echo $qty[$month].'<br>';

								$result =  $this->admin->insert_data($designated_tbl->depreciation_unit_tbl, $set, TRUE);
							}
						}
		                
						end_depreciation_unit_here:
		                $added++;
		            }
		            if($this->db->trans_status() === FALSE){
						$this->db->trans_rollback();
						$msg .= '<div class="alert alert-danger">Error please try again!</div>';
						$this->session->set_flashdata('message', $msg);
						redirect('unit/comparative-data-upload');
					}else{
						$this->db->trans_commit();
						$msg .= '<div class="alert alert-success">Data uploaded successfully!</div>';
						$this->session->set_flashdata('message', $msg);
						redirect('unit/comparative-data-upload');
					}

		        } catch (Exception $e) {
					die('Error loading file "' . pathinfo($inputFileName, PATHINFO_BASENAME)
		                  . '": ' .$e->getMessage());

				}
					
			} else {
				$msg = '<div class="alert alert-danger">'.$this->upload->display_errors().'</div>';
				$this->session->set_flashdata('message', $msg);
				redirect('unit/comparative-data-upload');
			}
		} else {
			$msg = '<div class="alert alert-danger">Please contact system administrator!</div>';
			$this->session->set_flashdata('message', $msg);
			redirect('unit/comparative-data-upload');
		}
	}


	public function upload_comp_opex_dept(){
		$info = $this->_require_login();
		$user_info = $this->get_user_info();
		//$company_unit_id_logged = $user_info['company_unit_id'];

		if($_SERVER['REQUEST_METHOD'] == 'POST'){

			$user_id = decode($info['user_id']);
			$this->load->library('excel_v2');
			ini_set('max_execution_time', 0);
			ini_set('memory_limit','2048M');

			$path = 'assets/comparative-opex-dept/';
			if (!is_dir($path)) {
			    mkdir($path, 0777, TRUE);
			}

			$trans_year = clean_data($this->input->post('comp-opex-dept-year'));

			$config['upload_path'] = $path;
			$config['allowed_types'] = 'xlsx|xls';
			$config['remove_spaces'] = TRUE;
			$this->load->library('upload', $config);
			$this->upload->initialize($config);            
			if (!$this->upload->do_upload('opex-dept-file')) {
				$error = array('error' => $this->upload->display_errors());
			} else {
				$data = array('upload_data' => $this->upload->data());
			}

			if(empty($error)){
				if (!empty($data['upload_data']['file_name'])) {
					$import_xls_file = $data['upload_data']['file_name'];
				} else {
					$import_xls_file = 0;
				}

				$inputFileName = $path . $import_xls_file;
				try {
					$inputFileType = PHPExcel_IOFactory::identify($inputFileName);
					$objReader = PHPExcel_IOFactory::createReader($inputFileType);
					$objPHPExcel = $objReader->load($inputFileName);
					$allDataInSheet = $objPHPExcel->getActiveSheet()->toArray(null, true, false, true);
					$flag = true;
					$i=2; //dating 4
					
					$added = 0;

					
					
		        	$totalKgs = 0;
		        	$overAllTotal = 0;
		        	$msg = '';
		        	$this->db->trans_start();

					//echo '<pre>';
		        	//print_r($allDataInSheet);
		        	//echo '</pre>';
		        	//exit();
		        	foreach ($allDataInSheet as $value) {
	        			if($added < 2){ //dating 3
	        				if($added == 1){
				        		if(
					        		empty(@$value['A']) ||
									empty(@$value['B']) ||
									empty(@$value['C']) ||
									empty(@$value['D']) ||
									empty(@$value['E']) ||
									empty(@$value['F']) ||
									empty(@$value['G']) ||
									!empty(@$value['H'])
				        		){
				        			$msg = '<div class="alert alert-danger">Error, Invalid Template! Please download the latest one.</div>';
									$this->session->set_flashdata('message', $msg);
									redirect('unit/comparative-data-upload');
				        		}
				        	}
		                  	goto end_opex_dept_here;
		                }

		                $company_unit_name	= clean_data(trim(@$value['A']));
			        	$cost_center		= clean_data(trim(@$value['B']));
			        	$cost_center_name		= clean_data(trim(@$value['C']));
			        	$gl_code		= clean_data(trim(@$value['D']));
			        	$gl_desc		= strtoupper( clean_data(trim(@$value['E'])));
			        	$gl_group_name = strtoupper( clean_data(trim(@$value['F'])));
						$cost		= clean_data(trim(@$value['G']));
						
						

			        	$i++;

			        	if($added == 2){
				        	

							$company_unit_id = '';
							$check_company_unit = $this->admin->check_data('company_unit_tbl', array('company_unit_name' => strtoupper($company_unit_name)), TRUE, 'company_unit_id');
							if($check_company_unit['result'] == TRUE){
								$company_unit_id = $check_company_unit['info']->company_unit_id;
								$set = array(
									'comp_opex_dept_status' 	=> 2
								);
								$result =  $this->admin->update_data('comparative_opex_dept_tbl', $set, array('company_unit_id' => $company_unit_id, 'YEAR(trans_year)' => $trans_year));
							}else{
								$msg .= '<div class="alert alert-danger">Company Unit ('.$company_unit_name.') does not exist! Line number '.$i.'.</div>';
								redirect('unit/comparative-data-upload');
							}
			        	}


			        	$check_cost_center = $this->admin->check_data('cost_center_tbl', array('cost_center_code' => strtoupper($cost_center)), TRUE, 'cost_center_id');
			        	if($check_cost_center['result'] == TRUE){
							$cost_center_id = $check_cost_center['info']->cost_center_id;
						}else{
							
							$msg .= '<div class="alert alert-danger">Cost Center ('.$cost_center.') does not exist! Line number '.$i.'.</div>';
							goto end_opex_dept_here;
							
						}
						

						$filter = array(
							'gl_group_name' => strtoupper($gl_group_name),
							'gl_group_status' => 1
						);
						$check_gl_group = $this->admin->check_data('gl_group_tbl', $filter, TRUE, 'gl_group_id');
			        	if($check_gl_group['result'] == TRUE){
							$gl_group_id = $check_gl_group['info']->gl_group_id;
						}else{
							
							$msg .= '<div class="alert alert-danger">GL Group ('.$gl_group_name.') does not exist! Line number '.$i.'.</div>';
							goto end_opex_dept_here;
							
						}

						
						$filter = array(
							'gl_code' => strtoupper($gl_code),
							'gl_group_id' => $gl_group_id,
							'gl_sub_status' => 1
						);
						$check_gl_subgroup = $this->admin->check_data('gl_subgroup_tbl', $filter, TRUE, 'gl_sub_id');
			        	if($check_gl_subgroup['result'] == TRUE){
							$gl_sub_id = $check_gl_subgroup['info']->gl_sub_id;
						}else{
							
							$msg .= '<div class="alert alert-danger">GL Account ('.$gl_code.') does not exist! Line number '.$i.'.</div>';
							goto end_opex_dept_here;
							
						}

						


			        	if(!empty($company_unit_id)){

							$set = array(
								'bc_id' 		=> NULL,
								'cost_center_id' => $cost_center_id,
								'company_unit_id' => $company_unit_id,
								'cost' => $cost=='' ? 0:$cost,
								'gl_sub_id' => $gl_sub_id,
								'gl_desc' => $gl_desc,
								'trans_year' => @$trans_year.'-01-01',
								'comp_opex_dept_status' => 1,
								'filename'		=> $inputFileName,
								'created_by'	=> $user_id,
								'created_ts'	=> date_now()
							);

							$result =  $this->admin->insert_data('comparative_opex_dept_tbl', $set, TRUE);
						}
		                
						end_opex_dept_here:
		                $added++;
		            }
		            if($this->db->trans_status() === FALSE){
						$this->db->trans_rollback();
						$msg .= '<div class="alert alert-danger">Error please try again!</div>';
						$this->session->set_flashdata('message', $msg);
						redirect('unit/comparative-data-upload');
					}else{
						$this->db->trans_commit();
						$msg .= '<div class="alert alert-success">Data uploaded successfully!</div>';
						$this->session->set_flashdata('message', $msg);
						redirect('unit/comparative-data-upload');
					}

		        } catch (Exception $e) {
					die('Error loading file "' . pathinfo($inputFileName, PATHINFO_BASENAME)
		                  . '": ' .$e->getMessage());

				}
					
			} else {
				$msg = '<div class="alert alert-danger">'.$this->upload->display_errors().'</div>';
				$this->session->set_flashdata('message', $msg);
				redirect('unit/comparative-data-upload');
			}
		} else {
			$msg = '<div class="alert alert-danger">Please contact system administrator!</div>';
			$this->session->set_flashdata('message', $msg);
			redirect('unit/comparative-data-upload');
		}
	}

	public function view_uploaded_file($filename){
		$info = $this->_require_login();
		$user_id = decode($info['user_id']);
		$this->load->library('excel_v2');
		ini_set('max_execution_time', 0);
		ini_set('memory_limit','2048M');

		
		
		$inputFileType = 'Excel2007';
		$inputFileName = decode($filename);

		$objReader = PHPExcel_IOFactory::createReader($inputFileType);
		$objPHPExcel = $objReader->load($inputFileName);

		
		$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'HTML');
		$objWriter->save('php://output');

		exit;
	}

	public function cancel_uploaded_data(){
		$info = $this->_require_login();
		$user_id = decode($info['user_id']);

		$year = clean_data($this->input->post('trans_year'));
		$id = clean_data(decode($this->input->post('bc_id')));
		$table = clean_data(decode($this->input->post('table')));
		$set = array();
		$where = array();
		$designated_tbl = $this->_get_designated_tbl();

		

		if($table == 'comparative_capex_unit_tbl'){
			$prefix = 'Comparative Capex (Unit) ';
			$table = $table.' a';
			$where = array(
				'a.company_unit_id' => $id,
				'a.comp_capex_unit_year' => decode($year),
				'a.comp_capex_unit_status' => 1
			);
	
			$set = array(
				'a.comp_capex_unit_status' => 2
			);

		}


		if($table == 'comparative_opex_dept_tbl'){
			$prefix = 'Comparative OpEx (Unit) ';
			$table = $table.' a';
			$where = array(
				'a.company_unit_id' => $id,
				'YEAR(a.trans_year)' => decode($year),
				'a.comp_opex_dept_status' => 1
			);

			$set = array(
				'a.comp_opex_dept_status' => 2
			);
		}

		if($table == 'depreciation_unit_tbl'){
			$prefix = 'Comparative Depreciation (Unit) ';
			$table = $designated_tbl->depreciation_unit_tbl.' a';
			$where = array(
				'a.company_unit_id' => $id,
				'YEAR(a.depreciation_unit_date)' => decode($year),
				'a.depreciation_unit_status' => 1
			);
			
			$set = array(
				'a.depreciation_unit_status' => 2
			);
		}

		if(!empty($set) && $table && !empty($where)){
			$result =  $this->admin->update_data($table, $set, $where);
		}

		if(@$result){
			$msg = '<div class="alert alert-success">'.$prefix.'Data cancelled successfully!</div>';
			$this->session->set_flashdata('message', $msg);
			redirect('unit/comparative-data-upload');
		} else {
			$msg = '<div class="alert alert-danger">Error! Please try again!</div>';
			$this->session->set_flashdata('message', $msg);
			redirect('unit/comparative-data-upload');
		}
	}

	public function view_uploaded_data($table, $id, $year){
		$info = $this->_require_login();
		$user_id = decode($info['user_id']);
		$designated_tbl = $this->_get_designated_tbl();
		
		
		$data['id'] = $id;
		
		$id = decode($id);
		
		$data['title'] = 'View Uploaded Data';

		$table =  decode($table);
		
		if($table == 'comparative_capex_unit_tbl'){
			$data['table_title'] = 'CAPEX (Unit)';
			$table = 'comparative_capex_unit_tbl a';
			$join = array(
				'company_unit_tbl b' => 'a.company_unit_id = b.company_unit_id',
				'cost_center_tbl c' => 'a.cost_center_id = c.cost_center_id',
				'asset_group_tbl d' => 'a.ag_id = d.ag_id'
			);
			$where = array(
				'a.company_unit_id' => $id,
				'a.comp_capex_unit_year' => decode($year),
				'a.comp_capex_unit_status' => 1
			);
			$order_by = 'a.comp_capex_unit_id';
			$group_by = 'a.comp_capex_unit_id';
			
			$uploaded_data = $this->admin->get_join($table, $join, FALSE, $order_by, $group_by, '*', $where);

			$table = '
			<div class="col-lg-12">
				<div class="table-responsive">
					<table class="table table-hover table-stripe nowrap tbl-comparative">
						<thead>
							<tr>
								<th class="text-center">Unit Name</th>
								<th class="text-center">Cost Center</th>
								<th class="text-center">Cost Center Name</th>
								<th class="text-center">Class Code</th>
								<th class="text-center">Class Name</th>
								<th class="text-center">Acq. Date</th>
								<th class="text-center">Acq. Cost</th>
								<th class="text-center">Year</th>
							</tr>
						</thead>
						<tbody>';
			if(!empty($uploaded_data)){
								
				foreach($uploaded_data as $r){
					$table .= '<tr>';
					$table .= '<td>'.$r->company_unit_name.'</td>';
					$table .= '<td>'.$r->cost_center_code.'</td>';
					$table .= '<td>'.$r->cost_center_desc.'</td>';
					$table .= '<td>'.$r->ag_gl_code.'</td>';
					$table .= '<td>'.$r->ag_name.'</td>';
					$table .= '<td class="text-center">'.date('m/d/Y', strtotime($r->comp_capex_unit_date)).'</td>';
					$table .= '<td class="text-right">'.number_format($r->comp_capex_unit_val, 2, '.', ',').'</td>';
					$table .= '<td class="text-right">'.$r->comp_capex_unit_year.'</td>';
					$table .= '</tr>';
				}

				
			}
			$table .= '</tbody>';
			$table .= '</table></div></div>';
			
			$data['table_view'] = $table;
		}

		if($table == 'comparative_opex_dept_tbl'){
			$data['table_title'] = 'OPEX (Unit)';
			$table = 'comparative_opex_dept_tbl a';
			$join = array(
				'company_unit_tbl b' => 'a.company_unit_id = b.company_unit_id',
				'cost_center_tbl c' => 'a.cost_center_id = c.cost_center_id',
				'gl_subgroup_tbl d' => 'a.gl_sub_id = d.gl_sub_id',
				'gl_group_tbl e' => 'd.gl_group_id = e.gl_group_id'
			);
			$where = array(
				'a.company_unit_id' => $id,
				'YEAR(a.trans_year)' => decode($year),
				'a.comp_opex_dept_status' => 1
			);
			$order_by = 'a.comp_opex_dept_id';
			$group_by = 'a.comp_opex_dept_id';
			
			$uploaded_data = $this->admin->get_join($table, $join, FALSE, $order_by, $group_by, '*', $where);
			//exit;
			$table = '
			<div class="col-lg-12">
				<div class="table-responsive">
					<table class="table table-hover table-stripe nowrap tbl-comparative">
						<thead>
							<tr>
								<th class="text-center">Unit Name</th>
								<th class="text-center">Cost Center</th>
								<th class="text-center">Cost Center Name</th>
								<th class="text-center">GL Account</th>
								<th class="text-center">GL Description</th>
								<th class="text-center">GL Group</th>
								<th class="text-center">Cost</th>
								<th class="text-center">Year</th>
							</tr>
						</thead>
						<tbody>';
			if(!empty($uploaded_data)){
								
				foreach($uploaded_data as $r){
					$table .= '<tr>';
					$table .= '<td>'.$r->company_unit_name.'</td>';
					$table .= '<td>'.$r->cost_center_code.'</td>';
					$table .= '<td>'.$r->cost_center_desc.'</td>';
					$table .= '<td>'.$r->gl_code.'</td>';
					$table .= '<td>'.$r->gl_sub_name.'</td>';
					$table .= '<td>'.$r->gl_group_name.'</td>';
					$table .= '<td class="text-right">'.number_format($r->cost, 2, '.', ',').'</td>';
					$table .= '<td class="text-right">'.decode($year).'</td>';
					$table .= '</tr>';
				}

				
			}
			$table .= '</tbody>';
			$table .= '</table></div></div>';
			
			$data['table_view'] = $table;
		}


		if($table == 'depreciation_unit_tbl'){
			$data['table_title'] = 'Depreciation (Unit)';
			$table = $designated_tbl->depreciation_unit_tbl.' a';
			$join = array(
				'company_unit_tbl b' => 'a.company_unit_id = b.company_unit_id',
				'cost_center_tbl c' => 'a.cost_center_id = c.cost_center_id',
				'gl_subgroup_tbl d' => 'a.gl_sub_id = d.gl_sub_id',
				'gl_group_tbl e' => 'd.gl_group_id = e.gl_group_id'
			);

			$where = array(
				'a.company_unit_id' => $id,
				'YEAR(a.depreciation_unit_date)' => decode($year),
				'a.depreciation_unit_status' => 1
			);
			$order_by = 'a.depreciation_unit_id';
			$group_by = 'a.depreciation_unit_id';
			
			$uploaded_data = $this->admin->get_join($table, $join, FALSE, $order_by, $group_by, '*, MONTH(a.depreciation_unit_date) as trans_month', $where);

			$table = '
			<div class="col-lg-12">
				<div class="table-responsive">
					<table class="table table-hover table-stripe nowrap tbl-comparative">
						<thead>
							<tr>
								<th class="text-center">Unit Name</th>
								<th class="text-center">Cost Center</th>
								<th class="text-center">Cost Center Name</th>
								<th class="text-center">GL Code</th>
								<th class="text-center">GL Description</th>
								<th class="text-center">GL Group</th>
								<th class="text-center">Jan</th>
								<th class="text-center">Feb</th>
								<th class="text-center">Mar</th>
								<th class="text-center">Apr</th>
								<th class="text-center">May</th>
								<th class="text-center">Jun</th>
								<th class="text-center">Jul</th>
								<th class="text-center">Aug</th>
								<th class="text-center">Sep</th>
								<th class="text-center">Oct</th>
								<th class="text-center">Nov</th>
								<th class="text-center">Dec</th>
								<th class="text-center">Year</th>
							</tr>
						</thead>
						<tbody>';
			if(!empty($uploaded_data)){
								
				foreach($uploaded_data as $r){
					if($r->trans_month == 1){
						$table .= '<tr>';
						$table .= '<td>'.$r->company_unit_name.'</td>';
						$table .= '<td>'.$r->cost_center_code.'</td>';
						$table .= '<td>'.$r->cost_center_desc.'</td>';
						$table .= '<td>'.$r->gl_code.'</td>';
						$table .= '<td>'.$r->gl_sub_name.'</td>';
						$table .= '<td>'.$r->gl_group_name.'</td>';

					}
					$table .= '<td class="text-right">'.number_format($r->depreciation_unit_amount, 2, '.', ',').'</td>';


					if($r->trans_month == 12){
						$table .= '<td class="text-right">'.decode($year).'</td>';
						$table.= '</tr>';
					}
				}

				
			}
			$table .= '</tbody>';
			$table .= '</table></div></div>';
			
			$data['table_view'] = $table;
		}

		$view = 'unit/unit_view_upload_data_content';

		$data['content'] = $this->load->view($view, $data , TRUE);
		$this->load->view('unit/templates', $data);
		
	}

	/* codes to update end here*/
}
