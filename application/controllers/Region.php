<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Region extends CI_Controller {

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

	public function _get_cost_center_bc($cost_center_id){
		$join_cost_center = array('bc_tbl b' => 'a.cost_center_code = b.cost_center_code AND a.cost_center_status = 1 AND a.cost_center_id = ' . $cost_center_id);
		$check_cost_center = $this->admin->check_join('cost_center_tbl a', $join_cost_center, TRUE);
		if($check_cost_center['result'] == TRUE){
			$bc_id = $check_cost_center['info']->bc_id;
			return $bc_id;
		}else{
			echo 'Error cost center code not exist. Please try again!';
			exit;
		}
	}

	public function _get_cost_center_name($cost_center_id){
		$join_cost_center = array('bc_tbl b' => 'a.cost_center_code = b.cost_center_code AND a.cost_center_status = 1 AND a.cost_center_id = ' . $cost_center_id);
		$check_cost_center = $this->admin->check_join('cost_center_tbl a', $join_cost_center, TRUE);
		if($check_cost_center['result'] == TRUE){
			$bc_name = $check_cost_center['info']->bc_name;
			return $bc_name;
		}else{
			echo 'Error cost center code not exist. Please try again!';
			exit;
		}
	}

	public function logout(){
		$this->session->unset_userdata('bavi_purchasing');
		$this->session->sess_destroy();
		redirect();
	}

	public function index($year = null){
		$info = $this->_require_login();
		$user_info = $this->get_user_info();
		$region_id = $user_info['region_id'];
		$data['title'] = 'Dashboard';

		if($year == null){
			$year = $this->_active_year();
		}

		$data['year'] = $year;

		$join_bc_trans = array(
			'bc_tbl b' => 'a.bc_id = b.bc_id AND a.dashboard_bc_trans_year = ' . $year . ' AND b.region_id = ' . $region_id,
			'dashboard_transaction_status_tbl c' => 'a.dashboard_trans_status_id = c.dashboard_trans_status_id',
			'user_tbl d' => 'a.user_id = d.user_id'
		);

		$data['trans_bc'] = $this->admin->get_join('dashboard_bc_transaction_tbl a', $join_bc_trans, FALSE,'a.dashboard_bc_trans_added DESC');

		$select_region = '
			a.region_name, a.region_id,
			(SELECT COUNT(DISTINCT(y.bc_id)) FROM dashboard_bc_transaction_tbl x, bc_tbl y WHERE x.bc_id = y.bc_id AND a.region_id = y.region_id AND x.dashboard_trans_status_id = 3 AND x.dashboard_bc_trans_year = ' . $year . ') as count_completed

		';
		$data['trans_region'] = $this->admin->get_data('region_tbl a', array('region_status' => 1, 'region_id' => $region_id), FALSE, $select_region);

		$data['content'] = $this->load->view('region/dashboard_transaction_content', $data , TRUE);

		$user_type = decode($info['user_type_id']);
		if($user_type == 1){
			$this->load->view('admin/templates', $data);
		}elseif($user_type == 2){
			$this->load->view('bc/templates', $data);
		}elseif($user_type == 3){
			$this->load->view('unit/templates', $data);
		}elseif($user_type == 6){
			$this->load->view('region/templates', $data);
		}
	}


	/*CAPEX*/

	public function capex(){
		$info = $this->_require_login();
		$user_info = $this->get_user_info();
		$region_id = $user_info['region_id'];

		$data['title'] = 'CAPEX';
		$data['year'] = $this->_active_year();

		$data['bc'] = $this->admin->get_data('bc_tbl', 'bc_status=1 AND region_id = ' . $region_id);
		$data['content'] = $this->load->view('region/region_capex', $data , TRUE);
		$this->load->view('region/templates', $data);
	}

	public function capex_info($id, $year = null){
		$info = $this->_require_login();
		$cost_center = decode($id);
		$data['title'] = 'CAPEX Info';

		$join_cost_center = array('cost_center_tbl b' => "a.cost_center_code = b.cost_center_code AND a.cost_center_code ='" . $cost_center . "'");
		$check_bc = $this->admin->check_join('bc_tbl a', $join_cost_center, TRUE, FALSE, FALSE, '*, a.bc_id as bc');
		if($check_bc['result'] == TRUE){
			$bc_id = $check_bc['info']->bc;

			if($year == null){
				$year = $this->_active_year();
			}

			$data['year'] = $year;
			$module = 'CAPEX';

			$check_cost = $this->admin->check_data('cost_center_tbl', array('cost_center_code' => $cost_center, 'cost_center_status' => 1), TRUE);
			$data['id'] = $id;
			if($check_cost['result'] == TRUE){
				$cost_center_id = $check_cost['info']->cost_center_id;
				$cost_center_desc = $check_cost['info']->cost_center_desc;
				$data['cost_center_desc'] = $cost_center_desc;
				$join_cost = array(
					'cost_center_tbl b' => 'a.cost_center_id=b.cost_center_id AND a.ag_trans_status=1 AND b.cost_center_id=' . $cost_center_id . ' AND a.ag_trans_budget_year = ' . $year,
					'asset_group_tbl c' => 'a.ag_id=c.ag_id',
					'user_tbl d' => 'a.user_id=d.user_id'
				);
				$data['asset_group'] = $this->admin->get_join('asset_group_transaction_tbl a', $join_cost);

				$data['capex_asset'] = $this->admin->get_query('

					SELECT ag_name, SUM(capex) as capex, SUM(capex1) as capex1, SUM(capex2) as capex2
					
					FROM
					(
						(SELECT `d`.`ag_name`, `c`.`asg_name`, SUM(a.capex_qty * `f`.`capex_price`) as capex, 0 as capex1, 0 as capex2  FROM `asset_group_transaction_details_tbl` `a` JOIN `asset_group_transaction_item_tbl` `f` ON `a`.`ag_trans_item_id` = `f`.`ag_trans_item_id` AND `f`.`ag_trans_item_status` = 1 JOIN `asset_group_transaction_tbl` `b` ON `f`.`ag_trans_id`=`b`.`ag_trans_id` AND `b`.`ag_trans_status`=1 AND `a`.`ag_trans_det_status`=1 AND `b`.`ag_trans_budget_year` = ' . $year . ' JOIN `asset_subgroup_tbl` `c` ON `f`.`asg_id`=`c`.`asg_id` JOIN `asset_group_tbl` `d` ON `c`.`ag_id` = `d`.`ag_id` JOIN `cost_center_tbl` `e` ON `f`.`cost_center_id` = `e`.`cost_center_id` AND `e`.`cost_center_id` AND `e`.`parent_id`= ' . $cost_center_id . ' GROUP BY `c`.`asg_id` ORDER BY `d`.`ag_name`)

						UNION

						(SELECT y.ag_name, "", 0 as capex, SUM(x.comp_capex_val) as capex1, 0 as capex2 FROM comparative_capex_tbl x, asset_group_tbl y WHERE x.ag_id = y.ag_id AND x.bc_id = ' . $bc_id .' AND x.comp_capex_status = 1 AND x.comp_capex_year = ' . ($year - 1) . ' GROUP BY y.ag_name) 

						UNION

						(SELECT y.ag_name, "", 0 as capex, 0 as capex1, SUM(x.comp_capex_val) as capex2 FROM comparative_capex_tbl x, asset_group_tbl y WHERE x.ag_id = y.ag_id AND x.bc_id = ' . $bc_id .' AND x.comp_capex_status = 1 AND x.comp_capex_year = ' . ($year - 2) . ' GROUP BY y.ag_name) 
					) as capex_data

					GROUP BY ag_name;
				');

				$join_capex = array(
					'asset_group_transaction_item_tbl f' => 'a.ag_trans_item_id = f.ag_trans_item_id AND f.ag_trans_item_status = 1',
					'asset_group_transaction_tbl b' => 'f.ag_trans_id=b.ag_trans_id AND b.ag_trans_status=1 AND a.ag_trans_det_status=1 AND b.ag_trans_budget_year = ' . $year,
					'asset_subgroup_tbl c' => 'f.asg_id=c.asg_id',
					'asset_group_tbl d' => 'c.ag_id = d.ag_id',
					'cost_center_tbl e' => 'f.cost_center_id = e.cost_center_id AND e.cost_center_id AND e.parent_id=' . $cost_center_id
				);

				$data['capex_details'] = $this->admin->get_join('asset_group_transaction_details_tbl a', $join_capex, FALSE, 'd.ag_name', 'c.asg_id', 'd.ag_name, c.asg_name, SUM(a.capex_qty) total_qty, f.capex_price
				');

				$data['cost_center'] = encode($cost_center_id);
				$data['year'] = $year;
				$data['content'] = $this->load->view('region/region_capex_info_content', $data , TRUE);
				$this->load->view('region/templates', $data);
			}	
		}else{

		}
	}

	public function capex_donut($id, $year){
		$info = $this->_require_login();

		$cost_center = decode($id);
		$check_id = $this->admin->check_data('cost_center_tbl', array('cost_center_id' => $cost_center));
		if($check_id == TRUE){
			$donut = $this->admin->get_query('SELECT e.ag_name as asset_group, SUM(b.capex_price * c.capex_qty) as amount, e.ag_color as color FROM asset_group_transaction_tbl a, asset_group_transaction_item_tbl b, asset_group_transaction_details_tbl c, asset_subgroup_tbl d, asset_group_tbl e, cost_center_tbl f, transaction_type_tbl g WHERE a.ag_trans_id=b.ag_trans_id AND b.ag_trans_item_id=c.ag_trans_item_id AND b.asg_id=d.asg_id AND d.ag_id=e.ag_id AND b.cost_center_id=f.cost_center_id AND a.trans_type_id=g.trans_type_id AND f.parent_id=' . $cost_center . ' AND a.ag_trans_status=1 AND b.ag_trans_item_status=1 AND c.ag_trans_det_status=1 AND g.trans_type_id=1 AND a.ag_trans_budget_year = ' . $year . ' GROUP BY d.ag_id ORDER BY amount DESC');
			$data['result'] = 1;
			$data['info'] = $donut;
			
		}else{
			$data['result'] = 0;
		}

		echo json_encode($data);
		exit();
	}

	public function capex_line($id, $year){
		$info = $this->_require_login();
		
		$get_year = $this->admin->check_data('budget_active_tbl', array('budget_active_status' => 1), TRUE);

		if($get_year['result'] == TRUE){
			$trans_type = $get_year['info']->trans_type_id;
			$previous_year = $year - 1;
			$cost_center = decode($id);
			$check_id = $this->admin->check_data('cost_center_tbl', array('cost_center_id' => $cost_center));
			if($check_id == TRUE){
				$line = $this->admin->get_query('SELECT DATE_FORMAT(c.capex_budget_date, "%b %Y") as budget_date, SUM(b.capex_price * c.capex_qty) as amount FROM asset_group_transaction_tbl a, asset_group_transaction_item_tbl b, asset_group_transaction_details_tbl c, asset_subgroup_tbl d, asset_group_tbl e, cost_center_tbl f, transaction_type_tbl g WHERE a.ag_trans_id=b.ag_trans_id AND b.ag_trans_item_id=c.ag_trans_item_id AND b.asg_id=d.asg_id AND d.ag_id=e.ag_id AND b.cost_center_id=f.cost_center_id AND a.trans_type_id=g.trans_type_id AND f.parent_id=' . $cost_center . ' AND a.ag_trans_status=1 AND b.ag_trans_item_status=1 AND c.ag_trans_det_status=1 AND g.trans_type_id=1 AND a.ag_trans_budget_year = ' . $year . ' GROUP BY YEAR(c.capex_budget_date), MONTH(c.capex_budget_date)');

				/*$line2 = $this->admin->get_query('SELECT DATE_FORMAT(c.capex_budget_date, "%b %Y") as budget_date, SUM(b.capex_price * c.capex_qty) as amount FROM asset_group_transaction_tbl a, asset_group_transaction_item_tbl b, asset_group_transaction_details_tbl c, asset_subgroup_tbl d, asset_group_tbl e, cost_center_tbl f, transaction_type_tbl g WHERE a.ag_trans_id=b.ag_trans_id AND b.ag_trans_item_id=c.ag_trans_item_id AND b.asg_id=d.asg_id AND d.ag_id=e.ag_id AND b.cost_center_id=f.cost_center_id AND a.trans_type_id=g.trans_type_id AND f.parent_id=' . $cost_center . ' AND a.ag_trans_status=1 AND b.ag_trans_item_status=1 AND c.ag_trans_det_status=1 AND g.trans_type_id=2 AND YEAR(c.capex_budget_date)=' . $previous_year . ' GROUP BY YEAR(c.capex_budget_date), MONTH(c.capex_budget_date)');*/

				$line2 = '';

				$capex['first_data'] = $line;
				$capex['second_data'] = $line2;

				$data['result'] = 1;
				$data['info'] = $capex;
			}else{
				$data['result'] = 0;
			}
		}else{
			$data['result'] = 0;
		}

		echo json_encode($data);
		exit();
	}

	public function capex_bar($id, $year){
		$info = $this->_require_login();

		$cost_center = decode($id);
		$check_id = $this->admin->check_data('cost_center_tbl', array('cost_center_id' => $cost_center));
		if($check_id == TRUE){
			$bar = $this->admin->get_query('SELECT DATE_FORMAT(c.capex_budget_date, "%b %Y") as budget_date, e.ag_name as asset_group, SUM(b.capex_price * c.capex_qty) as amount, MONTH(c.capex_budget_date) as month, e.ag_color as color FROM asset_group_transaction_tbl a, asset_group_transaction_item_tbl b, asset_group_transaction_details_tbl c, asset_subgroup_tbl d, asset_group_tbl e, cost_center_tbl f, transaction_type_tbl g WHERE a.ag_trans_id=b.ag_trans_id AND b.ag_trans_item_id=c.ag_trans_item_id AND b.asg_id=d.asg_id AND d.ag_id=e.ag_id AND b.cost_center_id=f.cost_center_id AND a.trans_type_id=g.trans_type_id AND f.parent_id=' . $cost_center . ' AND a.ag_trans_status=1 AND b.ag_trans_item_status=1 AND c.ag_trans_det_status=1 AND a.ag_trans_budget_year = ' . $year . ' GROUP BY YEAR(c.capex_budget_date), MONTH(c.capex_budget_date), e.ag_id ORDER BY c.capex_budget_date ASC, amount DESC');
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

	public function download_capex($cost_center_id, $year){
		$info = $this->_require_login();

		ini_set('max_execution_time', 0); 
		ini_set('memory_limit','2048M');

		$cost_center_id = decode($cost_center_id);
		$check_cost_center = $this->admin->check_data('cost_center_tbl', array('cost_center_id' => $cost_center_id, 'cost_center_status' => 1), TRUE);
		if($check_cost_center['result'] == TRUE){
			$cost_center_name = $check_cost_center['info']->cost_center_desc;


			$asset_details = $this->admin->get_query('SELECT c.ag_trans_item_id, d.asg_name, g.cost_center_desc, g.cost_center_code, b.capex_price, a.ag_trans_budget_year, b.capex_lifespan, e.ag_name,

				(SELECT SUM(x.capex_qty) FROM asset_group_transaction_details_tbl x WHERE b.ag_trans_item_id=x.ag_trans_item_id) as total_qty, b.capex_remarks, 
				
				(SELECT y.rank_name FROM asset_group_transaction_rank_tbl x, rank_tbl y WHERE b.ag_trans_item_id = x.ag_trans_item_id AND x.rank_id = y.rank_id AND x.ag_trans_rank_status = 1) as rank,

				(SELECT SUM(x.capex_qty) FROM asset_group_transaction_details_tbl x WHERE b.ag_trans_item_id=x.ag_trans_item_id AND MONTH(x.capex_budget_date)=1 AND x.ag_trans_det_status=1) as jan,
				(SELECT SUM(x.capex_qty) FROM asset_group_transaction_details_tbl x WHERE b.ag_trans_item_id=x.ag_trans_item_id AND MONTH(x.capex_budget_date)=2 AND x.ag_trans_det_status=1) as feb,
				(SELECT SUM(x.capex_qty) FROM asset_group_transaction_details_tbl x WHERE b.ag_trans_item_id=x.ag_trans_item_id AND MONTH(x.capex_budget_date)=3 AND x.ag_trans_det_status=1) as mar,
				(SELECT SUM(x.capex_qty) FROM asset_group_transaction_details_tbl x WHERE b.ag_trans_item_id=x.ag_trans_item_id AND MONTH(x.capex_budget_date)=4 AND x.ag_trans_det_status=1) as apr,
				(SELECT SUM(x.capex_qty) FROM asset_group_transaction_details_tbl x WHERE b.ag_trans_item_id=x.ag_trans_item_id AND MONTH(x.capex_budget_date)=5 AND x.ag_trans_det_status=1) as may,
				(SELECT SUM(x.capex_qty) FROM asset_group_transaction_details_tbl x WHERE b.ag_trans_item_id=x.ag_trans_item_id AND MONTH(x.capex_budget_date)=6 AND x.ag_trans_det_status=1) as jun,
				(SELECT SUM(x.capex_qty) FROM asset_group_transaction_details_tbl x WHERE b.ag_trans_item_id=x.ag_trans_item_id AND MONTH(x.capex_budget_date)=7 AND x.ag_trans_det_status=1) as jul,
				(SELECT SUM(x.capex_qty) FROM asset_group_transaction_details_tbl x WHERE b.ag_trans_item_id=x.ag_trans_item_id AND MONTH(x.capex_budget_date)=8 AND x.ag_trans_det_status=1) as aug,
				(SELECT SUM(x.capex_qty) FROM asset_group_transaction_details_tbl x WHERE b.ag_trans_item_id=x.ag_trans_item_id AND MONTH(x.capex_budget_date)=9 AND x.ag_trans_det_status=1) as sep,
				(SELECT SUM(x.capex_qty) FROM asset_group_transaction_details_tbl x WHERE b.ag_trans_item_id=x.ag_trans_item_id AND MONTH(x.capex_budget_date)=10 AND x.ag_trans_det_status=1) as oct,
				(SELECT SUM(x.capex_qty) FROM asset_group_transaction_details_tbl x WHERE b.ag_trans_item_id=x.ag_trans_item_id AND MONTH(x.capex_budget_date)=11 AND x.ag_trans_det_status=1) as nov,
				(SELECT SUM(x.capex_qty) FROM asset_group_transaction_details_tbl x WHERE b.ag_trans_item_id=x.ag_trans_item_id AND MONTH(x.capex_budget_date)=12 AND x.ag_trans_det_status=1) as december

				FROM asset_group_transaction_tbl a, asset_group_transaction_item_tbl b, asset_group_transaction_details_tbl c, asset_subgroup_tbl d, asset_group_tbl e, transaction_type_tbl f, cost_center_tbl g  WHERE a.ag_trans_id=b.ag_trans_id AND b.ag_trans_item_id=c.ag_trans_item_id AND b.asg_id=d.asg_id AND d.ag_id=e.ag_id AND b.cost_center_id=g.cost_center_id AND a.trans_type_id=f.trans_type_id AND b.cost_center_id=g.cost_center_id AND a.ag_trans_status=1 AND b.ag_trans_item_status=1 AND c.ag_trans_det_status=1 AND f.trans_type_name="BUDGET" AND a.ag_trans_budget_year=' . $year . ' AND g.parent_id=' . $cost_center_id . ' GROUP BY b.ag_trans_item_id'
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
			$spreadsheet->getActiveSheet()->setTitle('OPEX Data - ' . $year);

			// set right to left direction
			//		$spreadsheet->getActiveSheet()->setRightToLeft(true);

			// Set active sheet index to the first sheet, so Excel opens this as the first sheet
			$spreadsheet->setActiveSheetIndex(0);

			// Redirect output to a client’s web browser (Excel2007)
			header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
			$random = generate_random(5);
			header('Content-Disposition: attachment;filename="Budgeting - CAPEX ' . $year . '_' . $random . '.xlsx"');
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

	public function view_capex($id){
		$info = $this->_require_login();
		$data['title'] = 'View CAPEX';
		$data['id'] = $id;

		$ag_trans_id = decode($id);
		$join_id = array(
			'asset_group_tbl b' => 'a.ag_id = b.ag_id AND a.ag_trans_status=1 AND a.ag_trans_id = ' . $ag_trans_id,
			'cost_center_tbl c' => 'a.cost_center_id = c.cost_center_id'
		);
		$check_id = $this->admin->check_join('asset_group_transaction_tbl a', $join_id, TRUE);
		
		if($check_id['result'] == TRUE){
			$data['parent_id']  = $check_id['info']->cost_center_id;
			$data['asset_group'] = $check_id['info']->ag_name;
			$data['cost_center_desc'] = $check_id['info']->cost_center_desc;
			$data['cost_center_code'] = encode($check_id['info']->cost_center_code);
			$year = $check_id['info']->ag_trans_budget_year;
			$data['year'] = $year;

			$data['asset_details'] = $this->admin->get_query('SELECT c.ag_trans_item_id, d.asg_name, g.cost_center_desc, b.capex_price,

				(SELECT SUM(x.capex_qty) FROM asset_group_transaction_details_tbl x WHERE b.ag_trans_item_id=x.ag_trans_item_id) as total_qty, b.capex_remarks, 
				
				(SELECT y.rank_name FROM asset_group_transaction_rank_tbl x, rank_tbl y WHERE b.ag_trans_item_id = x.ag_trans_item_id AND x.rank_id = y.rank_id AND x.ag_trans_rank_status = 1) as rank,

				(SELECT SUM(x.capex_qty) FROM asset_group_transaction_details_tbl x WHERE b.ag_trans_item_id=x.ag_trans_item_id AND MONTH(x.capex_budget_date)=1 AND x.ag_trans_det_status=1) as jan,
				(SELECT SUM(x.capex_qty) FROM asset_group_transaction_details_tbl x WHERE b.ag_trans_item_id=x.ag_trans_item_id AND MONTH(x.capex_budget_date)=2 AND x.ag_trans_det_status=1) as feb,
				(SELECT SUM(x.capex_qty) FROM asset_group_transaction_details_tbl x WHERE b.ag_trans_item_id=x.ag_trans_item_id AND MONTH(x.capex_budget_date)=3 AND x.ag_trans_det_status=1) as mar,
				(SELECT SUM(x.capex_qty) FROM asset_group_transaction_details_tbl x WHERE b.ag_trans_item_id=x.ag_trans_item_id AND MONTH(x.capex_budget_date)=4 AND x.ag_trans_det_status=1) as apr,
				(SELECT SUM(x.capex_qty) FROM asset_group_transaction_details_tbl x WHERE b.ag_trans_item_id=x.ag_trans_item_id AND MONTH(x.capex_budget_date)=5 AND x.ag_trans_det_status=1) as may,
				(SELECT SUM(x.capex_qty) FROM asset_group_transaction_details_tbl x WHERE b.ag_trans_item_id=x.ag_trans_item_id AND MONTH(x.capex_budget_date)=6 AND x.ag_trans_det_status=1) as jun,
				(SELECT SUM(x.capex_qty) FROM asset_group_transaction_details_tbl x WHERE b.ag_trans_item_id=x.ag_trans_item_id AND MONTH(x.capex_budget_date)=7 AND x.ag_trans_det_status=1) as jul,
				(SELECT SUM(x.capex_qty) FROM asset_group_transaction_details_tbl x WHERE b.ag_trans_item_id=x.ag_trans_item_id AND MONTH(x.capex_budget_date)=8 AND x.ag_trans_det_status=1) as aug,
				(SELECT SUM(x.capex_qty) FROM asset_group_transaction_details_tbl x WHERE b.ag_trans_item_id=x.ag_trans_item_id AND MONTH(x.capex_budget_date)=9 AND x.ag_trans_det_status=1) as sep,
				(SELECT SUM(x.capex_qty) FROM asset_group_transaction_details_tbl x WHERE b.ag_trans_item_id=x.ag_trans_item_id AND MONTH(x.capex_budget_date)=10 AND x.ag_trans_det_status=1) as oct,
				(SELECT SUM(x.capex_qty) FROM asset_group_transaction_details_tbl x WHERE b.ag_trans_item_id=x.ag_trans_item_id AND MONTH(x.capex_budget_date)=11 AND x.ag_trans_det_status=1) as nov,
				(SELECT SUM(x.capex_qty) FROM asset_group_transaction_details_tbl x WHERE b.ag_trans_item_id=x.ag_trans_item_id AND MONTH(x.capex_budget_date)=12 AND x.ag_trans_det_status=1) as december

				FROM asset_group_transaction_tbl a, asset_group_transaction_item_tbl b, asset_group_transaction_details_tbl c, asset_subgroup_tbl d, asset_group_tbl e, transaction_type_tbl f, cost_center_tbl g  WHERE a.ag_trans_id=b.ag_trans_id AND b.ag_trans_item_id=c.ag_trans_item_id AND b.asg_id=d.asg_id AND d.ag_id=e.ag_id AND b.cost_center_id=g.cost_center_id AND a.trans_type_id=f.trans_type_id AND b.cost_center_id=g.cost_center_id AND a.ag_trans_status=1 AND b.ag_trans_item_status=1 AND c.ag_trans_det_status=1 AND f.trans_type_name="BUDGET" AND a.ag_trans_budget_year=' . $year . ' AND a.ag_trans_id=' . $ag_trans_id . ' GROUP BY b.ag_trans_item_id');

			$data['content'] = $this->load->view('region/region_capex_view', $data , TRUE);
			$this->load->view('region/templates', $data);
		}else{

		}
	}

	
	/*OPEX*/

	public function opex(){
		$info = $this->_require_login();
		$user_info = $this->get_user_info();
		$region_id = $user_info['region_id'];

		$data['title'] = 'OPEX';
		$data['active_year'] = $this->_active_year();

		$data['bc'] = $this->admin->get_data('bc_tbl', 'bc_status = 1 AND region_id = ' . $region_id);
		$data['unit'] = $this->admin->get_data('company_unit_tbl', 'company_unit_status=1');
		$data['content'] = $this->load->view('region/region_opex', $data , TRUE);
		$this->load->view('region/templates', $data);
	}

	public function opex_info($id, $year = null){
		$info = $this->_require_login();
		$cost_center = decode($id);
		$data['title'] = 'OPEX Info';
		$data['bc_id'] = $id;

		if($year == null){
			$year = $this->_active_year();
		}

		$data['active_year'] = $this->_active_year();
		$data['year'] = $year;

		$join_cost = array(
			'cost_center_type_tbl b' => 'a.cost_center_type_id = b.cost_center_type_id AND a.cost_center_code = "' . $cost_center . '" AND a.cost_center_status = 1',
			'bc_tbl c' => 'a.cost_center_code = c.cost_center_code'
		);
		$check_cost = $this->admin->check_join('cost_center_tbl a', $join_cost, TRUE);
		$data['id'] = $id;
		if($check_cost['result'] == TRUE){
			$cost_center_id = $check_cost['info']->cost_center_id;
			$cost_center_desc = $check_cost['info']->cost_center_desc;
			$bc_id = $check_cost['info']->bc_id;

			$data['cost_center_desc'] =  $cost_center_desc;
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
			$data['gl_group'] = $this->admin->get_join('gl_transaction_tbl a', $join_cost);

			$opex_gl = $this->admin->get_query('

				SELECT gl_group_name, gl_sub_name, (opex) as opex, (opex1) as opex1, (opex2) as opex2

				FROM 
				(
					(SELECT `e`.`gl_group_name`, `d`.`gl_sub_name`, SUM(a.opex_amount) opex, 0 as opex1, 0 opex2 FROM `gl_transaction_details_tbl` `a` JOIN `gl_transaction_item_tbl` `b` ON `a`.`gl_trans_item_id` = `b`.`gl_trans_item_id` AND `b`.`gl_trans_item_status` = 1 AND `a`.`gl_trans_det_status`=1 JOIN `gl_transaction_tbl` `c` ON `b`.`gl_trans_id`=`c`.`gl_trans_id` AND `c`.`gl_trans_status`=1 AND `c`.`gl_year`= ' . $year . ' JOIN `gl_subgroup_tbl` `d` ON `b`.`gl_sub_id`=`d`.`gl_sub_id` JOIN `gl_group_tbl` `e` ON `d`.`gl_group_id` = `e`.`gl_group_id` AND `e`.`gl_group_show` = 1 JOIN `cost_center_tbl` `f` ON `b`.`cost_center_id` = `f`.`cost_center_id` AND `f`.`parent_id`= '. $cost_center_id . ' GROUP BY `b`.`gl_sub_id` ORDER BY `e`.`gl_group_id`, `d`.`gl_sub_name`, `opex` DESC
					)

					UNION

					(SELECT z.gl_group_name, y.gl_sub_name, 0 as opex, SUM(x.cost) as opex1, 0 as opex2 FROM comparative_opex_gl_tbl x, gl_subgroup_tbl y, gl_group_tbl z WHERE x.gl_sub_id = y.gl_sub_id AND y.gl_group_id = z.gl_group_id AND x.bc_id = ' . $bc_id . ' AND x.comp_opex_gl_status = 1 AND YEAR(x.trans_year) = ' . ($year - 1) . ' AND z.gl_group_name != "DEPRECIATION EXPENSES" GROUP BY z.gl_group_id, y.gl_sub_id
					)

					UNION

					(SELECT z.gl_group_name, y.gl_sub_name, 0 as opex, 0 as opex1, SUM(x.cost) as opex2 FROM comparative_opex_gl_tbl x, gl_subgroup_tbl y, gl_group_tbl z WHERE x.gl_sub_id = y.gl_sub_id AND y.gl_group_id = z.gl_group_id AND x.bc_id = ' . $bc_id . ' AND x.comp_opex_gl_status = 1 AND YEAR(x.trans_year) = ' . ($year - 2) . ' AND z.gl_group_name != "DEPRECIATION EXPENSES" GROUP BY z.gl_group_id, y.gl_sub_id
					)
				) as opex_data


				ORDER BY
					gl_group_name, gl_sub_name

			');


			//Depreciation per asset subgroup

			$depre_sub = $this->get_depreciation_subgroup($cost_center_id, $year, $bc_id);
			/*echo '<pre>';
			print_r($depre_sub);
			echo '</pre>';
			exit;*/
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


			$data['opex_gl'] = $opex_gl;
			$data['year'] = $year;
			$data['content'] = $this->load->view('region/region_opex_info_content', $data , TRUE);
			$this->load->view('region/templates', $data);
		}
	}

	public function get_depreciation_subgroup($cost_center, $year, $bc_id){
		$info = $this->_require_login();

		$get_depreciation = $this->admin->get_query('
			SELECT 
				ag_name, asset_group, ag_gl_code, SUM(total) as total, SUM(total1) as total1, SUM(total2) as total2, budget_date, gl_sub_name

				
			FROM
			(

				(
					SELECT e.ag_name, d.asg_name as asset_group, e.ag_gl_code, SUM((b.capex_price / b.capex_lifespan) * (13-MONTH(c.capex_budget_date)) * c.capex_qty) as total, 0 as total1, 0 as total2, MONTH(c.capex_budget_date) as budget_date, h.gl_sub_name FROM asset_group_transaction_tbl a, asset_group_transaction_item_tbl b, asset_group_transaction_details_tbl c, asset_subgroup_tbl d, asset_group_tbl e, cost_center_tbl f, transaction_type_tbl g, gl_subgroup_tbl h WHERE a.ag_trans_id=b.ag_trans_id AND b.ag_trans_item_id=c.ag_trans_item_id AND b.asg_id=d.asg_id AND d.ag_id=e.ag_id AND b.cost_center_id=f.cost_center_id AND a.trans_type_id=g.trans_type_id AND e.ag_gl_code = h.gl_code AND g.trans_type_id = 1 AND a.ag_trans_status = 1 AND b.ag_trans_item_status = 1 AND c.ag_trans_det_status=1 AND f.parent_id=' . $cost_center . ' AND YEAR(c.capex_budget_date)=' . $year . ' GROUP BY h.gl_sub_id
				)

				UNION

				(
					SELECT "", "", y.gl_code, SUM(x.depreciation_bc_amount) as total, 0 as total1, 0 as total2, x.depreciation_bc_date, y.gl_sub_name FROM depreciation_bc_tbl x, gl_subgroup_tbl y WHERE x.gl_sub_id = y.gl_sub_id  AND YEAR(x.depreciation_bc_date) = ' . $year . ' AND x.bc_id = "' . $bc_id . '" AND x.depreciation_bc_status = 1 GROUP BY y.gl_sub_id
				)

				UNION


				(
					SELECT "", "", y.gl_code, 0 as total, SUM(x.cost) as total1, 0 as total2, "",  y.gl_sub_name FROM comparative_opex_gl_tbl x, gl_subgroup_tbl y, gl_group_tbl z WHERE x.gl_sub_id = y.gl_sub_id AND y.gl_group_id = z.gl_group_id AND x.bc_id = ' . $bc_id . ' AND x.comp_opex_gl_status = 1 AND YEAR(x.trans_year) = ' . ($year - 1) . ' AND z.gl_group_name = "DEPRECIATION EXPENSES" GROUP BY y.gl_sub_id
				)

				UNION

				(
					SELECT "", "", y.gl_code, 0 as total1, 0 as total1, SUM(x.cost) as total2, "",  y.gl_sub_name FROM comparative_opex_gl_tbl x, gl_subgroup_tbl y, gl_group_tbl z WHERE x.gl_sub_id = y.gl_sub_id AND y.gl_group_id = z.gl_group_id AND x.bc_id = ' . $bc_id . ' AND x.comp_opex_gl_status = 1 AND YEAR(x.trans_year) = ' . ($year - 2) . ' AND z.gl_group_name = "DEPRECIATION EXPENSES" GROUP BY y.gl_sub_id
				)

			)depreciation_bc_tbl


			GROUP BY gl_sub_name
		');

		return $get_depreciation;
	}

	public function opex_donut($id, $year){
		$info = $this->_require_login();

		$cost_center = decode($id);
		$check_id = $this->admin->check_data('cost_center_tbl', array('cost_center_id' => $cost_center));

		if($check_id == TRUE){
			$line = $this->admin->get_query('SELECT e.gl_group_name as gl_group, SUM(c.opex_amount) as amount, e.gl_color as color FROM gl_transaction_tbl a, gl_transaction_item_tbl b, gl_transaction_details_tbl c, gl_subgroup_tbl d, gl_group_tbl e, cost_center_tbl f, transaction_type_tbl g WHERE a.gl_trans_id=b.gl_trans_id AND  b.gl_trans_item_id=c.gl_trans_item_id AND b.gl_sub_id=d.gl_sub_id AND d.gl_group_id=e.gl_group_id AND b.cost_center_id=f.cost_center_id AND a.trans_type_id=g.trans_type_id AND f.parent_id=' . $cost_center . ' AND a.gl_trans_status=1 AND b.gl_trans_item_status=1 AND c.gl_trans_det_status=1 AND g.trans_type_id=1 AND e.gl_group_show=1 AND YEAR(c.opex_budget_date)=' . $year . ' GROUP BY e.gl_group_id');
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

			$get_depreciation = $this->opex_line_capex(encode($cost_center), TRUE, $year);
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
		$cost_center = decode($id);
		$check_id = $this->admin->check_data('cost_center_tbl', array('cost_center_id' => $cost_center));
		if($check_id == TRUE){
			$line = $this->admin->get_query('SELECT  DATE_FORMAT(c.opex_budget_date, "%b %Y") as budget_date, SUM(c.opex_amount) as amount FROM gl_transaction_tbl a, gl_transaction_item_tbl b, gl_transaction_details_tbl c, gl_subgroup_tbl d, gl_group_tbl e, cost_center_tbl f, transaction_type_tbl g WHERE a.gl_trans_id=b.gl_trans_id AND b.gl_trans_item_id=c.gl_trans_item_id AND b.gl_sub_id=d.gl_sub_id AND d.gl_group_id=e.gl_group_id AND b.cost_center_id=f.cost_center_id AND a.trans_type_id=g.trans_type_id AND f.parent_id=' . $cost_center . ' AND a.gl_trans_status=1 AND b.gl_trans_item_status=1 AND g.trans_type_id=1 AND c.gl_trans_det_status=1 AND e.gl_group_show=1 AND YEAR(c.opex_budget_date)=' . $year . ' GROUP BY YEAR(c.opex_budget_date), MONTH(c.opex_budget_date)');
			
			
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

			$get_depreciation = $this->opex_line_capex(encode($cost_center), TRUE, $year);
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

		$cost_center = decode($id);
		$join_id = array('bc_tbl b' => 'a.cost_center_code = b.cost_center_code AND a.cost_center_id = ' . $cost_center);
		$check_id = $this->admin->check_join('cost_center_tbl a', $join_id, TRUE);
		if($check_id['result'] == TRUE){
			$bar = $this->admin->get_query('SELECT DATE_FORMAT(c.opex_budget_date, "%b %Y") as budget_date, e.gl_group_name as gl_group, SUM(c.opex_amount) as amount, MONTH(c.opex_budget_date) as month, e.gl_color as color FROM gl_transaction_tbl a, gl_transaction_item_tbl b, gl_transaction_details_tbl c, gl_subgroup_tbl d, gl_group_tbl e, cost_center_tbl f, transaction_type_tbl g WHERE a.gl_trans_id=b.gl_trans_id AND b.gl_trans_item_id=c.gl_trans_item_id AND b.gl_sub_id=d.gl_sub_id AND d.gl_group_id=e.gl_group_id AND b.cost_center_id=f.cost_center_id AND a.trans_type_id=g.trans_type_id AND f.parent_id=' . $cost_center . ' AND a.gl_trans_status=1 AND b.gl_trans_item_status=1 AND c.gl_trans_det_status=1 AND e.gl_group_show=1 AND g.trans_type_id=1 AND YEAR(c.opex_budget_date)=' . $year . ' GROUP BY YEAR(c.opex_budget_date), MONTH(c.opex_budget_date), e.gl_group_id ORDER BY c.opex_budget_date ASC');

			$bc_id = $check_id['info']->bc_id;

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

			$get_depreciation = $this->opex_line_capex(encode($cost_center), TRUE, $year);
			$depre = 'DEPRECIATION EXPENSES';

			$arr_group[$depre] = $depre;

			$arr_gl[$depre]['asset'] = $depre;
			$arr_gl[$depre]['color'] = '#f0cee4';

			//$arr_gl[$sw]['amount'] = array();
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

	public function opex_line_capex($id, $return_type=FALSE, $year){
		$info = $this->_require_login();

		$cost_center = decode($id);
		$join_id = array('bc_tbl b' => 'a.cost_center_code = b.cost_center_code AND a.cost_center_id = ' . $cost_center);
		$check_id = $this->admin->check_join('cost_center_tbl a', $join_id, TRUE);
		if($check_id['result'] == TRUE){
			$bc_id = $check_id['info']->bc_id;

			$line = $this->admin->get_query('
				SELECT e.ag_name as asset_group, (b.capex_price / b.capex_lifespan) as avg_opex, (13-MONTH(c.capex_budget_date)) as remaining_month, MONTH(c.capex_budget_date) as budget_date, c.capex_qty, "DEPRECIATION BUDGET" as depre_type FROM asset_group_transaction_tbl a, asset_group_transaction_item_tbl b, asset_group_transaction_details_tbl c, asset_subgroup_tbl d, asset_group_tbl e, cost_center_tbl f, transaction_type_tbl g WHERE a.ag_trans_id=b.ag_trans_id AND b.ag_trans_item_id=c.ag_trans_item_id AND b.asg_id=d.asg_id AND d.ag_id=e.ag_id AND b.cost_center_id=f.cost_center_id AND a.trans_type_id=g.trans_type_id AND a.ag_trans_status=1 AND b.ag_trans_item_status=1 AND c.ag_trans_det_status=1 AND g.trans_type_id=1 AND f.parent_id=' . $cost_center . ' AND YEAR(c.capex_budget_date)=' . $year);

			$line_actual = $this->admin->get_query('(SELECT "DEPRECIATION EXPENSES", SUM(x.depreciation_bc_amount) as avg_opex, 1 as remaining_month, MONTH(x.depreciation_bc_date) as budget_date, 1 as capex_qty, "DEPRECIATION ACTUAL" as depre_type FROM depreciation_bc_tbl x, gl_subgroup_tbl y, bc_tbl z, gl_group_tbl x1 WHERE x.gl_sub_id = y.gl_sub_id AND x.bc_id = z.bc_id AND y.gl_group_id = x1.gl_group_id AND YEAR(x.depreciation_bc_date) = ' . $year . ' AND z.bc_id = ' . $bc_id . ' AND x.depreciation_bc_status = 1 GROUP BY MONTH(x.depreciation_bc_date))');


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

	public function view_opex($id){
		$info = $this->_require_login();
		$data['title'] = 'View OPEX';
		$data['id'] = $id;
		$gl_trans_id = decode($id);
		$join_id = array(
			'gl_group_tbl b' => 'a.gl_group_id = b.gl_group_id AND a.gl_trans_status=1 AND a.gl_trans_id = ' . $gl_trans_id,
			'cost_center_tbl c' => 'a.cost_center_id = c.cost_center_id'
		);
		$check_id = $this->admin->check_join('gl_transaction_tbl a', $join_id, TRUE);
		$year = $this->_active_year();

		if($check_id['result'] == TRUE){
			$data['cost_center'] = encode($check_id['info']->cost_center_id);
			$data['cost_center_code'] = encode($check_id['info']->cost_center_code);
			$data['gl_group'] = $check_id['info']->gl_group_name;
			$data['year'] = $check_id['info']->gl_year;
			$join_det = array(
				'gl_transaction_item_tbl b' => 'a.gl_trans_item_id = b.gl_trans_item_id AND b.gl_trans_item_status = 1 AND a.gl_trans_det_status = 1 AND b.gl_trans_id=' . $gl_trans_id,
				'gl_transaction_tbl c' => 'b.gl_trans_id = c.gl_trans_id AND c.gl_year = ' . $year,
				'cost_center_tbl d' => 'b.cost_center_id = d.cost_center_id',
				'gl_subgroup_tbl e' => 'b.gl_sub_id=e.gl_sub_id'
				
			);
			
			$data['gl_details'] = $this->admin->get_join('gl_transaction_details_tbl a', $join_det, FALSE, FALSE, 'b.gl_trans_item_id', '*, (SELECT SUM(x.opex_amount) FROM gl_transaction_details_tbl x WHERE b.gl_trans_item_id=x.gl_trans_item_id AND YEAR(x.opex_budget_date)=' . $year . ') as total_qty, (SELECT SUM(x.opex_amount) FROM gl_transaction_details_tbl x WHERE b.gl_trans_item_id=x.gl_trans_item_id AND MONTH(x.opex_budget_date)=1 AND YEAR(x.opex_budget_date)=' . $year . ') as jan, (SELECT SUM(x.opex_amount) FROM gl_transaction_details_tbl x WHERE b.gl_trans_item_id=x.gl_trans_item_id AND MONTH(opex_budget_date)=2 AND YEAR(x.opex_budget_date)=' . $year . ') as feb, (SELECT SUM(x.opex_amount) FROM gl_transaction_details_tbl x WHERE b.gl_trans_item_id=x.gl_trans_item_id AND MONTH(x.opex_budget_date)=3 AND YEAR(x.opex_budget_date)=' . $year . ') as mar, (SELECT SUM(x.opex_amount) FROM gl_transaction_details_tbl x WHERE b.gl_trans_item_id=x.gl_trans_item_id AND MONTH(x.opex_budget_date)=4 AND YEAR(x.opex_budget_date)=' . $year . ') as apr, (SELECT SUM(x.opex_amount) FROM gl_transaction_details_tbl x WHERE b.gl_trans_item_id=x.gl_trans_item_id AND MONTH(x.opex_budget_date)=5 AND YEAR(x.opex_budget_date)=' . $year . ') as may, (SELECT SUM(x.opex_amount) FROM gl_transaction_details_tbl x WHERE b.gl_trans_item_id=x.gl_trans_item_id AND MONTH(x.opex_budget_date)=6 AND YEAR(x.opex_budget_date)=' . $year . ') as jun, (SELECT SUM(x.opex_amount) FROM gl_transaction_details_tbl x WHERE b.gl_trans_item_id=x.gl_trans_item_id AND MONTH(x.opex_budget_date)=7 AND YEAR(x.opex_budget_date)=' . $year . ') as jul, (SELECT SUM(x.opex_amount) FROM gl_transaction_details_tbl x WHERE b.gl_trans_item_id=x.gl_trans_item_id AND MONTH(x.opex_budget_date)=8 AND YEAR(x.opex_budget_date)=' . $year . ') as aug, (SELECT SUM(x.opex_amount) FROM gl_transaction_details_tbl x WHERE b.gl_trans_item_id=x.gl_trans_item_id AND MONTH(x.opex_budget_date)=9 AND YEAR(x.opex_budget_date)=' . $year . ') as sep, (SELECT SUM(x.opex_amount) FROM gl_transaction_details_tbl x WHERE b.gl_trans_item_id=x.gl_trans_item_id AND MONTH(x.opex_budget_date)=10 AND YEAR(x.opex_budget_date)=' . $year . ') as oct, (SELECT SUM(x.opex_amount) FROM gl_transaction_details_tbl x WHERE b.gl_trans_item_id=x.gl_trans_item_id AND MONTH(x.opex_budget_date)=11 AND YEAR(x.opex_budget_date)=' . $year . ') as nov, (SELECT SUM(x.opex_amount) FROM gl_transaction_details_tbl x WHERE b.gl_trans_item_id=x.gl_trans_item_id AND MONTH(x.opex_budget_date)=12 AND YEAR(x.opex_budget_date)=' . $year . ') as december');
			$data['content'] = $this->load->view('region/region_opex_view', $data , TRUE);
			$this->load->view('region/templates', $data);
		}else{

		}
	}

	public function view_store_expense($id){
		$info = $this->_require_login();

		ini_set('max_execution_time', 0);
		ini_set('memory_limit','2048M');

		$data['title'] = 'View Store Expenses';
		$data['id'] = $id;
		$gl_trans_id = decode($id);
		$join_id = array(
			'gl_group_tbl b' => "a.gl_group_id = b.gl_group_id AND a.gl_trans_status=1  AND a.gl_trans_id = " . $gl_trans_id,
			'gl_transaction_item_tbl c' => 'a.gl_trans_id = c.gl_trans_id AND c.gl_trans_item_status = 1 AND c.gl_transaction_type_id = 1',
			'cost_center_tbl d' => 'c.cost_center_id = d.cost_center_id',
			'cost_center_tbl e' => 'a.cost_center_id = e.cost_center_id'
		);
		$check_id = $this->admin->check_join('gl_transaction_tbl a', $join_id, TRUE, FALSE, FALSE, '*, e.cost_center_code as parent_code');

		if($check_id['result'] == TRUE){
			$year = $check_id['info']->gl_year;
			$data['cost_center'] = encode($check_id['info']->cost_center_id);
			$data['parent'] = encode($check_id['info']->parent_code);
			$data['cost_center_code'] = encode($check_id['info']->cost_center_code);
			$data['bc'] = $check_id['info']->cost_center_desc;
			$data['gl_group'] = $check_id['info']->gl_group_name;
			$join_outlet = array(
				'cost_center_tbl b' => 'a.ifs_code = b.cost_center_code',
				'gl_transaction_item_tbl c' => 'b.cost_center_id = c.cost_center_id AND c.gl_trans_item_status = 1',
				'gl_transaction_tbl d' => 'c.gl_trans_id = d.gl_trans_id AND d.gl_trans_status = 1 AND d.gl_year = ' . $year . ' AND d.gl_trans_id = ' . $gl_trans_id,
			);

			$data['store_expense'] = $this->admin->get_join('outlet_tbl a', $join_outlet, FALSE, FALSE, 'a.outlet_id', '*, c.cost_center_id as cost_center');
			
			$data['year'] = $year;
			
			$data['content'] = $this->load->view('region/region_store_expense_content', $data , TRUE);
			$this->load->view('region/templates', $data);
		}else{
		}
	}

	public function view_store_expense_item($id, $year){
		$info = $this->_require_login();
		$data['title'] = 'View Store Expense Item';
		$data['id'] = $id;
		$cost_center_id = decode($id);
		$join_id = array(
			'gl_group_tbl b' => "a.gl_group_id = b.gl_group_id AND a.gl_trans_status=1",
			'gl_transaction_item_tbl c' => 'a.gl_trans_id = c.gl_trans_id AND a.gl_trans_status = 1 AND gl_trans_item_status = 1 AND c.gl_transaction_type_id = 1',
			'cost_center_tbl d' => 'c.cost_center_id = d.cost_center_id AND d.cost_center_id = ' . $cost_center_id . ' AND a.gl_year = ' . $year,
			'cost_center_tbl e' => 'a.cost_center_id = e.cost_center_id'
		);
		$check_id = $this->admin->check_join('gl_transaction_tbl a', $join_id, TRUE, FALSE, FALSE, '*, e.cost_center_code as parent');

		if($check_id['result'] == TRUE){
			$data['cost_center'] = encode($check_id['info']->cost_center_id);
			$data['cost_center_code'] = encode($check_id['info']->cost_center_code);
			$data['parent'] = encode($check_id['info']->parent);
			$data['gl_group'] = $check_id['info']->gl_group_name;
			$data['gl_trans_id'] = encode($check_id['info']->gl_trans_id);
			$join_det = array(
				'gl_transaction_item_tbl b' => 'a.gl_trans_item_id = b.gl_trans_item_id AND b.gl_trans_item_status = 1 AND a.gl_trans_det_status = 1 AND b.gl_transaction_type_id = 1',
				'gl_transaction_tbl c' => 'b.gl_trans_id = c.gl_trans_id AND c.gl_trans_status = 1 AND c.gl_year = ' . $year,
				'cost_center_tbl d' => 'b.cost_center_id = d.cost_center_id AND d.cost_center_id = ' . $cost_center_id,
				'gl_subgroup_tbl e' => 'b.gl_sub_id=e.gl_sub_id',
				'gl_group_tbl f' => "e.gl_group_id = f.gl_group_id",
			);

			$data['year'] = $year;
			$data['gl_details'] = $this->admin->get_join('gl_transaction_details_tbl a', $join_det, FALSE, FALSE, 'b.gl_trans_item_id', '*, (SELECT SUM(x.opex_amount) FROM gl_transaction_details_tbl x WHERE b.gl_trans_item_id=x.gl_trans_item_id AND YEAR(x.opex_budget_date)=' . $year . ') as total_qty, (SELECT SUM(x.opex_amount) FROM gl_transaction_details_tbl x WHERE b.gl_trans_item_id=x.gl_trans_item_id AND MONTH(x.opex_budget_date)=1 AND YEAR(x.opex_budget_date)=' . $year . ') as jan, (SELECT SUM(x.opex_amount) FROM gl_transaction_details_tbl x WHERE b.gl_trans_item_id=x.gl_trans_item_id AND MONTH(opex_budget_date)=2 AND YEAR(x.opex_budget_date)=' . $year . ') as feb, (SELECT SUM(x.opex_amount) FROM gl_transaction_details_tbl x WHERE b.gl_trans_item_id=x.gl_trans_item_id AND MONTH(x.opex_budget_date)=3 AND YEAR(x.opex_budget_date)=' . $year . ') as mar, (SELECT SUM(x.opex_amount) FROM gl_transaction_details_tbl x WHERE b.gl_trans_item_id=x.gl_trans_item_id AND MONTH(x.opex_budget_date)=4 AND YEAR(x.opex_budget_date)=' . $year . ') as apr, (SELECT SUM(x.opex_amount) FROM gl_transaction_details_tbl x WHERE b.gl_trans_item_id=x.gl_trans_item_id AND MONTH(x.opex_budget_date)=5 AND YEAR(x.opex_budget_date)=' . $year . ') as may, (SELECT SUM(x.opex_amount) FROM gl_transaction_details_tbl x WHERE b.gl_trans_item_id=x.gl_trans_item_id AND MONTH(x.opex_budget_date)=6 AND YEAR(x.opex_budget_date)=' . $year . ') as jun, (SELECT SUM(x.opex_amount) FROM gl_transaction_details_tbl x WHERE b.gl_trans_item_id=x.gl_trans_item_id AND MONTH(x.opex_budget_date)=7 AND YEAR(x.opex_budget_date)=' . $year . ') as jul, (SELECT SUM(x.opex_amount) FROM gl_transaction_details_tbl x WHERE b.gl_trans_item_id=x.gl_trans_item_id AND MONTH(x.opex_budget_date)=8 AND YEAR(x.opex_budget_date)=' . $year . ') as aug, (SELECT SUM(x.opex_amount) FROM gl_transaction_details_tbl x WHERE b.gl_trans_item_id=x.gl_trans_item_id AND MONTH(x.opex_budget_date)=9 AND YEAR(x.opex_budget_date)=' . $year . ') as sep, (SELECT SUM(x.opex_amount) FROM gl_transaction_details_tbl x WHERE b.gl_trans_item_id=x.gl_trans_item_id AND MONTH(x.opex_budget_date)=10 AND YEAR(x.opex_budget_date)=' . $year . ') as oct, (SELECT SUM(x.opex_amount) FROM gl_transaction_details_tbl x WHERE b.gl_trans_item_id=x.gl_trans_item_id AND MONTH(x.opex_budget_date)=11 AND YEAR(x.opex_budget_date)=' . $year . ') as nov, (SELECT SUM(x.opex_amount) FROM gl_transaction_details_tbl x WHERE b.gl_trans_item_id=x.gl_trans_item_id AND MONTH(x.opex_budget_date)=12 AND YEAR(x.opex_budget_date)=' . $year . ') as december');
			$data['content'] = $this->load->view('region/region_store_expense_item', $data , TRUE);
			$this->load->view('region/templates', $data);
		}else{

		}
	}

	public function download_opex($cost_center_id, $year){
		$info = $this->_require_login();
		$cost_center_id = decode($cost_center_id);
		$bc_id = $this->_get_cost_center_bc($cost_center_id);
		$bc_name = $this->_get_cost_center_name($cost_center_id);

		ini_set('max_execution_time', 0); 
		ini_set('memory_limit','6048M');

		$check_cost_center = $this->admin->check_data('cost_center_tbl', array('cost_center_id' => $cost_center_id, 'cost_center_status' => 1), TRUE);
		if($check_cost_center['result'] == TRUE){
			$cost_center_name = $check_cost_center['info']->cost_center_desc;

			$join_det = array(
				'gl_transaction_item_tbl b' => 'a.gl_trans_item_id = b.gl_trans_item_id AND b.gl_trans_item_status = 1 AND a.gl_trans_det_status = 1',
				'gl_transaction_tbl c' => 'b.gl_trans_id = c.gl_trans_id AND c.gl_trans_status = 1 AND c.gl_year = ' . $year,
				'cost_center_tbl d' => 'b.cost_center_id = d.cost_center_id AND d.parent_id = ' . $cost_center_id,
				'gl_subgroup_tbl e' => 'b.gl_sub_id=e.gl_sub_id',
				'gl_group_tbl f' => 'e.gl_group_id = f.gl_group_id'
			);
				
			$gl_details = $this->admin->get_join('gl_transaction_details_tbl a', $join_det, FALSE, FALSE, 'b.gl_trans_item_id', '*, (SELECT SUM(x.opex_amount) FROM gl_transaction_details_tbl x WHERE b.gl_trans_item_id=x.gl_trans_item_id AND YEAR(x.opex_budget_date)=' . $year . ') as total_qty, (SELECT SUM(x.opex_amount) FROM gl_transaction_details_tbl x WHERE b.gl_trans_item_id=x.gl_trans_item_id AND MONTH(x.opex_budget_date)=1 AND YEAR(x.opex_budget_date)=' . $year . ') as jan, (SELECT SUM(x.opex_amount) FROM gl_transaction_details_tbl x WHERE b.gl_trans_item_id=x.gl_trans_item_id AND MONTH(opex_budget_date)=2 AND YEAR(x.opex_budget_date)=' . $year . ') as feb, (SELECT SUM(x.opex_amount) FROM gl_transaction_details_tbl x WHERE b.gl_trans_item_id=x.gl_trans_item_id AND MONTH(x.opex_budget_date)=3 AND YEAR(x.opex_budget_date)=' . $year . ') as mar, (SELECT SUM(x.opex_amount) FROM gl_transaction_details_tbl x WHERE b.gl_trans_item_id=x.gl_trans_item_id AND MONTH(x.opex_budget_date)=4 AND YEAR(x.opex_budget_date)=' . $year . ') as apr, (SELECT SUM(x.opex_amount) FROM gl_transaction_details_tbl x WHERE b.gl_trans_item_id=x.gl_trans_item_id AND MONTH(x.opex_budget_date)=5 AND YEAR(x.opex_budget_date)=' . $year . ') as may, (SELECT SUM(x.opex_amount) FROM gl_transaction_details_tbl x WHERE b.gl_trans_item_id=x.gl_trans_item_id AND MONTH(x.opex_budget_date)=6 AND YEAR(x.opex_budget_date)=' . $year . ') as jun, (SELECT SUM(x.opex_amount) FROM gl_transaction_details_tbl x WHERE b.gl_trans_item_id=x.gl_trans_item_id AND MONTH(x.opex_budget_date)=7 AND YEAR(x.opex_budget_date)=' . $year . ') as jul, (SELECT SUM(x.opex_amount) FROM gl_transaction_details_tbl x WHERE b.gl_trans_item_id=x.gl_trans_item_id AND MONTH(x.opex_budget_date)=8 AND YEAR(x.opex_budget_date)=' . $year . ') as aug, (SELECT SUM(x.opex_amount) FROM gl_transaction_details_tbl x WHERE b.gl_trans_item_id=x.gl_trans_item_id AND MONTH(x.opex_budget_date)=9 AND YEAR(x.opex_budget_date)=' . $year . ') as sep, (SELECT SUM(x.opex_amount) FROM gl_transaction_details_tbl x WHERE b.gl_trans_item_id=x.gl_trans_item_id AND MONTH(x.opex_budget_date)=10 AND YEAR(x.opex_budget_date)=' . $year . ') as oct, (SELECT SUM(x.opex_amount) FROM gl_transaction_details_tbl x WHERE b.gl_trans_item_id=x.gl_trans_item_id AND MONTH(x.opex_budget_date)=11 AND YEAR(x.opex_budget_date)=' . $year . ') as nov, (SELECT SUM(x.opex_amount) FROM gl_transaction_details_tbl x WHERE b.gl_trans_item_id=x.gl_trans_item_id AND MONTH(x.opex_budget_date)=12 AND YEAR(x.opex_budget_date)=' . $year . ') as december,

				(SELECT z.brand_name FROM outlet_tbl x, outlet_brand_tbl y, brand_tbl z WHERE d.cost_center_code = x.ifs_code AND x.outlet_id = y.outlet_id AND y.brand_id = z.brand_id AND y.outlet_brand_status = 1) as brand,

				(SELECT x.cost_center_group_name FROM cost_center_group_tbl x WHERE d.cost_center_group_id = x.cost_center_group_id) as cost_center_group
				');



			$get_depreciation = $this->get_depreciation_monthly($cost_center_id, $year, $bc_id);

			$get_depreciation2 = $this->get_depreciation_monthly2($cost_center_id, $year, $bc_id);

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
				->setCellValue("E1", "Brand")
				->setCellValue("F1", "Cost Center Code")
				->setCellValue("G1", "Cost Center Name")
				->setCellValue("H1", "Cost Center Group")
				->setCellValue("I1", "Year")
				->setCellValue("J1", "Jan")
				->setCellValue("K1", "Feb")
				->setCellValue("L1", "Mar")
				->setCellValue("M1", "Apr")
				->setCellValue("N1", "May")
				->setCellValue("O1", "Jun")
				->setCellValue("P1", "Jul")
				->setCellValue("Q1", "Aug")
				->setCellValue("R1", "Sep")
				->setCellValue("S1", "Oct")
				->setCellValue("T1", "Nov")
				->setCellValue("U1", "Dec")
				->setCellValue("V1", "Total")
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
						->setCellValue("E$x",$row->brand)
						->setCellValue("F$x",$row->cost_center_code)
						->setCellValue("G$x",$row->cost_center_desc)
						->setCellValue("H$x",$row->cost_center_group)
						->setCellValue("I$x",$row->gl_year)
						->setCellValue("J$x",$jan)
						->setCellValue("K$x",$feb)
						->setCellValue("L$x",$mar)
						->setCellValue("M$x",$apr)
						->setCellValue("N$x",$may)
						->setCellValue("O$x",$jun)
						->setCellValue("P$x",$jul)
						->setCellValue("Q$x",$aug)
						->setCellValue("R$x",$sep)
						->setCellValue("S$x",$oct)
						->setCellValue("T$x",$nov)
						->setCellValue("U$x",$december)
						->setCellValue("V$x",$total)
						;

				$spreadsheet->getActiveSheet()->getStyle("A$x:V$x")->applyFromArray($style_data);
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
						->setCellValue("D$x", $row->gl_sub_name)
						->setCellValue("E$x",$row->brand)
						->setCellValue("F$x", $row->cost_center_code)
						->setCellValue("G$x", $row->cost_center_desc)
						->setCellValue("H$x", $row->cost_center_group)
						->setCellValue("I$x", $row->ag_trans_budget_year)
						->setCellValue("J$x", $month[0])
						->setCellValue("K$x", $month[1])
						->setCellValue("L$x", $month[2])
						->setCellValue("M$x", $month[3])
						->setCellValue("N$x", $month[4])
						->setCellValue("O$x", $month[5])
						->setCellValue("P$x", $month[6])
						->setCellValue("Q$x", $month[7])
						->setCellValue("R$x", $month[8])
						->setCellValue("S$x", $month[9])
						->setCellValue("T$x", $month[10])
						->setCellValue("U$x", $month[11])
						->setCellValue("V$x",$total)
						;

				$spreadsheet->getActiveSheet()->getStyle("A$x:V$x")->applyFromArray($style_data);
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
						->setCellValue("C$x","DEPRECIATION - 2")
						->setCellValue("D$x",$row->gl_sub_name)
						->setCellValue("E$x",$row->brand)
						->setCellValue("F$x",$row->cost_center_code)
						->setCellValue("G$x",$row->cost_center_desc)
						->setCellValue("H$x",$row->cost_center_group)
						->setCellValue("I$x",$row->gl_year)
						->setCellValue("J$x",$jan)
						->setCellValue("K$x",$feb)
						->setCellValue("L$x",$mar)
						->setCellValue("M$x",$apr)
						->setCellValue("N$x",$may)
						->setCellValue("O$x",$jun)
						->setCellValue("P$x",$jul)
						->setCellValue("Q$x",$aug)
						->setCellValue("R$x",$sep)
						->setCellValue("S$x",$oct)
						->setCellValue("T$x",$nov)
						->setCellValue("U$x",$december)
						->setCellValue("V$x",$total)
						;

				$spreadsheet->getActiveSheet()->getStyle("A$x:V$x")->applyFromArray($style_data);
				$x++;
			}

			foreach(range('A','V') as $columnID) {
				$spreadsheet->getActiveSheet()->getColumnDimension($columnID)
						->setAutoSize(true);
			}

			$spreadsheet->getActiveSheet()->getStyle('J2:V' . ($x - 1))->getNumberFormat()->setFormatCode('#,##0.00');
			
			// Rename worksheet
			$spreadsheet->getActiveSheet()->setTitle('OPEX Data - ' . $year);

			// set right to left direction
			//		$spreadsheet->getActiveSheet()->setRightToLeft(true);

			// Set active sheet index to the first sheet, so Excel opens this as the first sheet
			$spreadsheet->setActiveSheetIndex(0);

			// Redirect output to a client’s web browser (Excel2007)
			header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
			$random = generate_random(5);
			header('Content-Disposition: attachment;filename="Budgeting - ' . $bc_name . ' OPEX ' . $year . '_' . $random . '.xlsx"');
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

	public function get_depreciation_monthly($cost_center, $year, $bc_id){
		$info = $this->_require_login();

		
		$get_depreciation = $this->admin->get_query('SELECT d.asg_name as asset_group, f.cost_center_code, f.cost_center_desc, a.ag_trans_budget_year, (b.capex_price / b.capex_lifespan) amount,

			(SELECT z.brand_name FROM outlet_tbl x, outlet_brand_tbl y, brand_tbl z WHERE f.cost_center_code = x.ifs_code AND x.outlet_id = y.outlet_id AND y.brand_id = z.brand_id AND y.outlet_brand_status = 1) as brand,

			(SELECT m.gl_sub_name FROM gl_subgroup_tbl m WHERE e.ag_gl_code = m.gl_code LIMIT 1) as gl_sub_name,

			(SELECT x.capex_qty FROM asset_group_transaction_details_tbl x WHERE b.ag_trans_item_id = x.ag_trans_item_id AND x.ag_trans_det_status = 1 AND MONTH(x.capex_budget_date) = 1) as jan,

			(SELECT x.capex_qty FROM asset_group_transaction_details_tbl x WHERE b.ag_trans_item_id = x.ag_trans_item_id AND x.ag_trans_det_status = 1 AND MONTH(x.capex_budget_date) = 2) as feb,

			(SELECT x.capex_qty FROM asset_group_transaction_details_tbl x WHERE b.ag_trans_item_id = x.ag_trans_item_id AND x.ag_trans_det_status = 1 AND MONTH(x.capex_budget_date) = 3) as mar,

			(SELECT x.capex_qty FROM asset_group_transaction_details_tbl x WHERE b.ag_trans_item_id = x.ag_trans_item_id AND x.ag_trans_det_status = 1 AND MONTH(x.capex_budget_date) = 4) as apr,

			(SELECT x.capex_qty FROM asset_group_transaction_details_tbl x WHERE b.ag_trans_item_id = x.ag_trans_item_id AND x.ag_trans_det_status = 1 AND MONTH(x.capex_budget_date) = 5) as may,

			(SELECT x.capex_qty FROM asset_group_transaction_details_tbl x WHERE b.ag_trans_item_id = x.ag_trans_item_id AND x.ag_trans_det_status = 1 AND MONTH(x.capex_budget_date) = 6) as jun,

			(SELECT x.capex_qty FROM asset_group_transaction_details_tbl x WHERE b.ag_trans_item_id = x.ag_trans_item_id AND x.ag_trans_det_status = 1 AND MONTH(x.capex_budget_date) = 7) as jul,

			(SELECT x.capex_qty FROM asset_group_transaction_details_tbl x WHERE b.ag_trans_item_id = x.ag_trans_item_id AND x.ag_trans_det_status = 1 AND MONTH(x.capex_budget_date) = 8) as aug,

			(SELECT x.capex_qty FROM asset_group_transaction_details_tbl x WHERE b.ag_trans_item_id = x.ag_trans_item_id AND x.ag_trans_det_status = 1 AND MONTH(x.capex_budget_date) = 9) as sep,

			(SELECT x.capex_qty FROM asset_group_transaction_details_tbl x WHERE b.ag_trans_item_id = x.ag_trans_item_id AND x.ag_trans_det_status = 1 AND MONTH(x.capex_budget_date) = 10) as oct,

			(SELECT x.capex_qty FROM asset_group_transaction_details_tbl x WHERE b.ag_trans_item_id = x.ag_trans_item_id AND x.ag_trans_det_status = 1 AND MONTH(x.capex_budget_date) = 11) as nov,

			(SELECT x.capex_qty FROM asset_group_transaction_details_tbl x WHERE b.ag_trans_item_id = x.ag_trans_item_id AND x.ag_trans_det_status = 1 AND MONTH(x.capex_budget_date) = 12) as december,

			(SELECT x.cost_center_group_name FROM cost_center_group_tbl x WHERE f.cost_center_group_id = x.cost_center_group_id) as cost_center_group

			FROM asset_group_transaction_tbl a, asset_group_transaction_item_tbl b, asset_subgroup_tbl d, asset_group_tbl e, cost_center_tbl f, transaction_type_tbl g WHERE a.ag_trans_id=b.ag_trans_id AND b.asg_id=d.asg_id AND d.ag_id=e.ag_id AND b.cost_center_id=f.cost_center_id AND a.trans_type_id=g.trans_type_id AND g.trans_type_id=1 AND a.ag_trans_status=1 AND b.ag_trans_item_status = 1 AND f.parent_id=' . $cost_center . ' AND a.ag_trans_budget_year=' . $year . '
		');

		return $get_depreciation;
	}

	public function get_depreciation_monthly2($cost_center, $year, $bc_id){
		$info = $this->_require_login();

		
		$get_depreciation = $this->admin->get_query("

			SELECT
			n.gl_sub_name,
			n.gl_code,
			'DEPRECIATION(OLD)' AS depreciation_type,
			o.cost_center_code,
			o.cost_center_desc,
			'" . $year . "' AS gl_year,
			1 AS amount,
			q.cost_center_type_name,
			b.brand,
			SUM(CASE WHEN MONTH(d.depreciation_bc_date) = 1 THEN d.depreciation_bc_amount ELSE 0 END) AS jan,
			SUM(CASE WHEN MONTH(d.depreciation_bc_date) = 2 THEN d.depreciation_bc_amount ELSE 0 END) AS feb,
			SUM(CASE WHEN MONTH(d.depreciation_bc_date) = 3 THEN d.depreciation_bc_amount ELSE 0 END) AS mar,
			SUM(CASE WHEN MONTH(d.depreciation_bc_date) = 4 THEN d.depreciation_bc_amount ELSE 0 END) AS apr,
			SUM(CASE WHEN MONTH(d.depreciation_bc_date) = 5 THEN d.depreciation_bc_amount ELSE 0 END) AS may,
			SUM(CASE WHEN MONTH(d.depreciation_bc_date) = 6 THEN d.depreciation_bc_amount ELSE 0 END) AS jun,
			SUM(CASE WHEN MONTH(d.depreciation_bc_date) = 7 THEN d.depreciation_bc_amount ELSE 0 END) AS jul,
			SUM(CASE WHEN MONTH(d.depreciation_bc_date) = 8 THEN d.depreciation_bc_amount ELSE 0 END) AS aug,
			SUM(CASE WHEN MONTH(d.depreciation_bc_date) = 9 THEN d.depreciation_bc_amount ELSE 0 END) AS sep,
			SUM(CASE WHEN MONTH(d.depreciation_bc_date) = 10 THEN d.depreciation_bc_amount ELSE 0 END) AS oct,
			SUM(CASE WHEN MONTH(d.depreciation_bc_date) = 11 THEN d.depreciation_bc_amount ELSE 0 END) AS nov,
			SUM(CASE WHEN MONTH(d.depreciation_bc_date) = 12 THEN d.depreciation_bc_amount ELSE 0 END) AS december,
			ccg.cost_center_group_name
			FROM
			depreciation_bc_tbl d
			INNER JOIN gl_subgroup_tbl n ON d.gl_sub_id = n.gl_sub_id
			INNER JOIN cost_center_tbl o ON d.cost_center_id = o.cost_center_id
			INNER JOIN asset_group_tbl p ON n.gl_code = p.ag_gl_code
			INNER JOIN cost_center_type_tbl q ON o.cost_center_type_id = q.cost_center_type_id
			LEFT JOIN (
				SELECT x.ifs_code, z.brand_name as brand
				FROM outlet_tbl x
				JOIN outlet_brand_tbl y ON x.outlet_id = y.outlet_id AND y.outlet_brand_status = 1
				JOIN brand_tbl z ON y.brand_id = z.brand_id
			) b ON o.cost_center_code = b.ifs_code
			LEFT JOIN cost_center_group_tbl ccg ON o.cost_center_group_id = ccg.cost_center_group_id
			WHERE
			d.depreciation_bc_status = 1
			AND YEAR(d.depreciation_bc_date) = '" . $year . "'
			AND d.bc_id = " . $bc_id . "
			GROUP BY
			o.cost_center_id, n.gl_sub_id
		");

		return $get_depreciation;
	}
	
	public function get_depreciation_monthly2_old($cost_center, $year, $bc_id){
		$info = $this->_require_login();

		
		$get_depreciation = $this->admin->get_query('

			SELECT n.gl_sub_name, n.gl_code, "DEPRECIATION(OLD)", o.cost_center_code, o.cost_center_desc, "' . $year . '" as gl_year, 1 as amount,

				(SELECT z.brand_name FROM outlet_tbl x, outlet_brand_tbl y, brand_tbl z WHERE o.cost_center_code = x.ifs_code AND x.outlet_id = y.outlet_id AND y.brand_id = z.brand_id AND y.outlet_brand_status = 1) as brand,

				(SELECT SUM(x.depreciation_bc_amount) FROM depreciation_bc_tbl x WHERE x.cost_center_id = o.cost_center_id AND x.gl_sub_id = n.gl_sub_id AND MONTH(x.depreciation_bc_date) = 1 AND x.depreciation_bc_status = 1 AND YEAR(x.depreciation_bc_date) = ' . $year . ' AND x.bc_id = ' . $bc_id . ') as  jan,

				(SELECT SUM(x.depreciation_bc_amount) FROM depreciation_bc_tbl x WHERE x.cost_center_id = o.cost_center_id AND x.gl_sub_id = n.gl_sub_id AND MONTH(x.depreciation_bc_date) = 2 AND x.depreciation_bc_status = 1 AND YEAR(x.depreciation_bc_date) = ' . $year . ' AND x.bc_id = ' . $bc_id . ') as  feb,


				(SELECT SUM(x.depreciation_bc_amount) FROM depreciation_bc_tbl x WHERE x.cost_center_id = o.cost_center_id AND x.gl_sub_id = n.gl_sub_id AND MONTH(x.depreciation_bc_date) = 3 AND x.depreciation_bc_status = 1 AND YEAR(x.depreciation_bc_date) = ' . $year . ' AND x.bc_id = ' . $bc_id . ') as  mar,


				(SELECT SUM(x.depreciation_bc_amount) FROM depreciation_bc_tbl x WHERE x.cost_center_id = o.cost_center_id AND x.gl_sub_id = n.gl_sub_id AND MONTH(x.depreciation_bc_date) = 4 AND x.depreciation_bc_status = 1 AND YEAR(x.depreciation_bc_date) = ' . $year . ' AND x.bc_id = ' . $bc_id . ') as  apr,

				(SELECT SUM(x.depreciation_bc_amount) FROM depreciation_bc_tbl x WHERE x.cost_center_id = o.cost_center_id AND x.gl_sub_id = n.gl_sub_id AND MONTH(x.depreciation_bc_date) = 5 AND x.depreciation_bc_status = 1 AND YEAR(x.depreciation_bc_date) = ' . $year . ' AND x.bc_id = ' . $bc_id . ') as  may,

				(SELECT SUM(x.depreciation_bc_amount) FROM depreciation_bc_tbl x WHERE x.cost_center_id = o.cost_center_id AND x.gl_sub_id = n.gl_sub_id AND MONTH(x.depreciation_bc_date) = 6 AND x.depreciation_bc_status = 1 AND YEAR(x.depreciation_bc_date) = ' . $year . ' AND x.bc_id = ' . $bc_id . ') as  jun,

				(SELECT SUM(x.depreciation_bc_amount) FROM depreciation_bc_tbl x WHERE x.cost_center_id = o.cost_center_id AND x.gl_sub_id = n.gl_sub_id AND MONTH(x.depreciation_bc_date) = 7 AND x.depreciation_bc_status = 1 AND YEAR(x.depreciation_bc_date) = ' . $year . ' AND x.bc_id = ' . $bc_id . ') as  jul,

				(SELECT SUM(x.depreciation_bc_amount) FROM depreciation_bc_tbl x WHERE x.cost_center_id = o.cost_center_id AND x.gl_sub_id = n.gl_sub_id AND MONTH(x.depreciation_bc_date) = 8 AND x.depreciation_bc_status = 1 AND YEAR(x.depreciation_bc_date) = ' . $year . ' AND x.bc_id = ' . $bc_id . ') as  aug,

				(SELECT SUM(x.depreciation_bc_amount) FROM depreciation_bc_tbl x WHERE x.cost_center_id = o.cost_center_id AND x.gl_sub_id = n.gl_sub_id AND MONTH(x.depreciation_bc_date) = 9 AND x.depreciation_bc_status = 1 AND YEAR(x.depreciation_bc_date) = ' . $year . ' AND x.bc_id = ' . $bc_id . ') as  sep,

				(SELECT SUM(x.depreciation_bc_amount) FROM depreciation_bc_tbl x WHERE x.cost_center_id = o.cost_center_id AND x.gl_sub_id = n.gl_sub_id AND MONTH(x.depreciation_bc_date) = 10 AND x.depreciation_bc_status = 1 AND YEAR(x.depreciation_bc_date) = ' . $year . ' AND x.bc_id = ' . $bc_id . ') as  oct,

				(SELECT SUM(x.depreciation_bc_amount) FROM depreciation_bc_tbl x WHERE x.cost_center_id = o.cost_center_id AND x.gl_sub_id = n.gl_sub_id AND MONTH(x.depreciation_bc_date) = 11 AND x.depreciation_bc_status = 1 AND YEAR(x.depreciation_bc_date) = ' . $year . ' AND x.bc_id = ' . $bc_id . ') as  nov,

				(SELECT SUM(x.depreciation_bc_amount) FROM depreciation_bc_tbl x WHERE x.cost_center_id = o.cost_center_id AND x.gl_sub_id = n.gl_sub_id AND MONTH(x.depreciation_bc_date) = 12 AND x.depreciation_bc_status = 1 AND YEAR(x.depreciation_bc_date) = ' . $year . ' AND x.bc_id = ' . $bc_id . ') as  december,

				(SELECT x.cost_center_group_name FROM cost_center_group_tbl x WHERE o.cost_center_group_id = x.cost_center_group_id) as cost_center_group
			
			FROM depreciation_bc_tbl m, gl_subgroup_tbl n, cost_center_tbl o, asset_group_tbl p WHERE m.gl_sub_id = n.gl_sub_id AND m.cost_center_id = o.cost_center_id AND  n.gl_code = p.ag_gl_code AND m.depreciation_bc_status = 1 AND YEAR(m.depreciation_bc_date) = ' . $year . ' AND m.bc_id = ' . $bc_id . ' GROUP BY o.cost_center_id, n.gl_sub_id


		');

		return $get_depreciation;
	}


	/*Sales*/

	public function sales(){
		$info = $this->_require_login();
		$user_info = $this->get_user_info();
		$region_id = $user_info['region_id'];

		$data['year'] = $this->_active_year();
		$data['title'] = 'Sales';
		$data['bc'] = $this->admin->get_data('bc_tbl', array('bc_status' => 1, 'region_id' => $region_id));
		$data['content'] = $this->load->view('region/region_sales_content', $data , TRUE);
		$this->load->view('region/templates', $data);
	}

	public function sales_info($id, $year = null){
		$info = $this->_require_login();
		$bc_id = decode($id);
		$data['id'] = $id;
		$data['title'] = 'Sales Info';

		if($year == null){
			$year = $this->_active_year();
		}

		$data['year'] = $year;

		$check_bc = $this->admin->check_data('bc_tbl', array('bc_id' => $bc_id, 'bc_status' => 1), TRUE);
		if($check_bc['result'] == TRUE){
			$bc_name = $check_bc['info']->bc_name;
			$data['bc_name'] = $bc_name;
			$join_brand = array(
				'outlet_brand_tbl b' => 'a.brand_id = b.brand_id AND b.outlet_brand_status = 1',
				'outlet_tbl c' => 'b.outlet_id = c.outlet_id AND c.outlet_status_id = 1',
				'bc_tbl d' => 'c.bc_id = d.bc_id AND d.bc_id = ' . $bc_id
			);
			$data['brand'] = $this->admin->get_join('brand_tbl a', $join_brand, FALSE, FALSE, 'a.brand_id');

			$join_budgeted_outlet = array(
				'outlet_type_tbl b' => 'a.outlet_type_id = b.outlet_type_id',
				'bc_tbl c' => 'a.bc_id = c.bc_id AND c.bc_id = ' . $bc_id,
				'region_tbl d' => 'c.region_id=d.region_id',
				'outlet_status_tbl e' => 'a.outlet_status_id=e.outlet_status_id',
				'outlet_brand_tbl f' => 'a.outlet_id = f.outlet_id AND f.outlet_brand_status = 1',
				'brand_tbl g' => 'f.brand_id = g.brand_id',
				'brand_type_tbl h' => 'g.brand_type_id = h.brand_type_id',
				'sales_tbl i' => 'a.outlet_id = i.outlet_id AND i.sales_year=' . $year . ' AND i.sales_status=1 AND i.trans_type_id = 1'
			);
			
			$data['budgeted_outlet'] = $this->admin->get_join('outlet_tbl a', $join_budgeted_outlet, FALSE,'a.ifs_code ASC');
			$join_unbudgeted_outlet = array(
				'outlet_type_tbl b' => 'a.outlet_type_id = b.outlet_type_id',
				'bc_tbl c' => 'a.bc_id = c.bc_id AND c.bc_id = ' . $bc_id,
				'region_tbl d' => 'c.region_id=d.region_id',
				'outlet_status_tbl e' => 'a.outlet_status_id=e.outlet_status_id',
				'outlet_brand_tbl f' => 'a.outlet_id = f.outlet_id AND f.outlet_brand_status = 1',
				'brand_tbl g' => 'f.brand_id = g.brand_id',
				'brand_type_tbl h' => 'g.brand_type_id = h.brand_type_id',
				'outlet_year_tbl i' => 'a.outlet_id = i.outlet_id AND i.outlet_year_status = 1 AND i.outlet_year = ' . $year
			);
			$where_budgeted = 'a.outlet_id NOT IN(SELECT outlet_id FROM sales_tbl x WHERE x.outlet_id=a.outlet_id AND x.sales_year=' . $year . ' AND x.sales_status=1 AND x.trans_type_id = 1)';

			$data['unbudgeted_outlet'] = $this->admin->get_join('outlet_tbl a', $join_unbudgeted_outlet, FALSE,'a.ifs_code ASC', FALSE, FALSE, $where_budgeted);

			$data['region'] = $this->admin->get_data('region_tbl', array('region_status' => 1));
			$data['type'] = $this->admin->get_data('brand_type_tbl', array('brand_type_status' => 1));
			$data['status'] = $this->admin->get_data('outlet_status_tbl');
			$data['content'] = $this->load->view('region/region_sales_info_content', $data , TRUE);
			$this->load->view('region/templates', $data);
		}
	}

	public function get_sales_info_net_sales(){
		$info = $this->_require_login();

		ini_set('max_execution_time', 0); 
		ini_set('memory_limit','2048M');

		$bc_id = decode($this->input->post('id'));
		$year = $this->input->post('year');

		$get_sales = $this->admin->get_query('
 			
				SELECT i.vat_type_name, h.sales_det_qty, j.material_group_name, h.sales_det_date, c.material_id, k.sales_unit_equivalent,

				(h.sales_det_asp - IFNULL((SELECT z.sales_tactical_det_price FROM sales_tactical_tbl x, sales_tactical_item_tbl y, sales_tactical_details_tbl z WHERE d.outlet_id = x.outlet_id AND x.sales_tactical_id = y.sales_tactical_id AND b.material_id = y.material_id AND y.sales_tactical_item_id = z.sales_tactical_item_id AND h.sales_det_date = z.sales_tactical_det_date AND x.sales_tactical_status = 1 AND y.sales_tactical_item_status = 1 AND z.sales_tactical_det_status = 1), 0)
					
				) as price

			FROM `sales_tbl` `a` JOIN `sales_item_tbl` `b` ON `a`.`sales_id` = `b`.`sales_id` AND `a`.`sales_status` = 1 AND `b`.`sales_item_status` = 1 JOIN `material_tbl` `c` ON `b`.`material_id` = `c`.`material_id` JOIN `outlet_tbl` `d` ON `a`.`outlet_id` = `d`.`outlet_id` JOIN `outlet_brand_tbl` `e` ON `d`.`outlet_id` = `e`.`outlet_id` JOIN `brand_tbl` `f` ON `e`.`brand_id` = `f`.`brand_id` JOIN `bc_tbl` `g` ON `d`.`bc_id` = `g`.`bc_id` JOIN `sales_details_tbl` `h` ON `b`.`sales_item_id` = `h`.`sales_item_id` JOIN vat_type_tbl i ON c.vat_type_id = i.vat_type_id JOIN material_group_tbl j ON c.material_group_id = j.material_group_id AND g.bc_id = ' . $bc_id . ' AND a.sales_year = ' . $year . ' JOIN material_unit_tbl k ON c.material_id = k.material_id AND k.material_unit_status = 1 WHERE e.outlet_brand_status = 1
		');


		$net_sales = 0;
		$net_sales1 = 0;
		$net_sales2 = 0;

		$sales_unit = 0;
		$sales_unit1 = 0;
		$sales_unit2 = 0;

		$weight_arr = array();
		foreach($get_sales as $row){

			$sales_det_qty = $row->sales_det_qty;
			$material_group_name = $row->material_group_name;
			$sales_date = $row->sales_det_date;
			$material_id = $row->material_id;
			$unit = $row->sales_unit_equivalent;

			$sales_unit += $unit > 0 ? $sales_det_qty / $unit : 0;

			$sales_weight = 1;
			if($material_group_name == 'DRESSED'){

				if(!array_key_exists($material_id . $sales_date, $weight_arr)){

					$get_dressed_wt = $this->admin->get_query(
						'SELECT g1.ext_prod_trans_dtl_id, g1.ave_wgt FROM prod_trans_tbl a1, prod_trans_dtl_tbl b1, component_type_tbl c1, material_tbl d1, ext_prod_trans_tbl f1, ext_prod_trans_dtl_tbl g1

						WHERE a1.prod_trans_id = b1.prod_trans_id AND b1.component_type_id = c1.component_type_id AND a1.prod_id = ' . $material_id . ' AND b1.article_id = d1.material_id AND b1.article_type_id = 1 AND a1.prod_trans_status = 3 AND a1.bc_id = ' . $bc_id . ' AND b1.prod_trans_dtl_date = "' . $sales_date . '" AND a1.process_type_id = 5 AND c1.component_type = "COST OF SALES" AND f1.bc_id = a1.bc_id AND f1.ext_prod_trans_id = g1.ext_prod_trans_id AND b1.prod_trans_dtl_date = g1.trans_dtl_date AND d1.material_id = f1.material_id AND f1.ext_prod_trans_status = 1 AND g1.ext_prod_trans_dtl_status = 1
					', TRUE);
					
					if(!empty($get_dressed_wt)){
						$sales_weight = $get_dressed_wt->ave_wgt;
						if($sales_weight > 0){
							$sales_det_qty = $sales_det_qty * $sales_weight;
						}else{
							$sales_weight = 1;
						}
					}

					$weight_arr[$material_id . $sales_date] = $sales_weight;
				}else{
					$sales_weight = $weight_arr[$material_id . $sales_date];
					if($sales_weight > 0){
						$sales_det_qty = $sales_det_qty * $sales_weight;
					}else{
						$sales_weight = 1;
					}
				}
			}

	

			$gross_sales = $sales_det_qty * $row->price;

			$vat_total = 0;
			if($row->vat_type_name == 'VAT ITEM'){
				$vat_total = $gross_sales / 1.12 * 0.12;
			}

			$net_sales += $gross_sales - $vat_total;
		}

 		$check_net_sales1 = $this->admin->check_data('comparative_pnl_tbl', array('bc_id' => $bc_id, 'comp_pnl_year' => ($year - 1), 'comp_pnl_status' => 1), TRUE);

	    $check_net_sales2 = $this->admin->check_data('comparative_pnl_tbl', array('bc_id' => $bc_id, 'comp_pnl_year' => ($year - 2), 'comp_pnl_status' => 1), TRUE);

	    if($check_net_sales1['result'] == TRUE){
 			$net_sales1 = $check_net_sales1['info']->comp_pnl_net_sales;
 			$sales_unit1 = $check_net_sales1['info']->comp_pnl_sales_volume;
 		}

 		if($check_net_sales2['result'] == TRUE){
 			$net_sales2 = $check_net_sales2['info']->comp_pnl_net_sales;
 			$sales_unit2 = $check_net_sales2['info']->comp_pnl_sales_volume;
 		}

 		$data['net_sales'] = $net_sales;
 		$data['net_sales1'] = $net_sales1;
 		$data['net_sales2'] = $net_sales2;

 		$data['sales_unit'] = $sales_unit;
 		$data['sales_unit1'] = $sales_unit1;
 		$data['sales_unit2'] = $sales_unit2;

 		/*Sale Volume*/
 		$get_sales_volume = $this->_get_sales_info_volume($bc_id, $year);
 		$volume_tbl = $this->_get_sales_info_volume_tbl($get_sales_volume);
 		$data['volume_tbl'] = $volume_tbl;

 		echo json_encode($data);
	}

	public function _get_sales_info_volume($bc_id, $year){


		$join_live = array(
			'sales_item_tbl b' => 'a.sales_id = b.sales_id AND a.sales_status = 1 AND b.sales_item_status = 1 AND a.sales_year = ' . $year,
			'sales_details_tbl c' => 'b.sales_item_id = c.sales_item_id AND c.sales_det_status = 1',
			'material_tbl d' => 'b.material_id = d.material_id',
			'material_group_tbl e' => 'd.material_group_id = e.material_group_id AND e.material_group_name = \'LIVE SALES\'',
			'material_unit_tbl f' => 'd.material_id = f.material_id AND f.material_unit_status = 1',
			'outlet_tbl g' => 'a.outlet_id = g.outlet_id AND g.bc_id = ' . $bc_id
		);

		$get_live = $this->admin->get_join('sales_tbl a', $join_live, TRUE, FALSE, FALSE, 'SUM(c.sales_det_qty / f.sales_unit_equivalent) as live_sales_unit, 

			(SELECT SUM(x.sales_unit) FROM comparative_volume_tbl x WHERE g.bc_id = x.bc_id AND x.sales_status = 1 AND x.sales_vol = "LIVE SALES" AND YEAR(x.trans_year) = ' . ($year - 1) . ') as live_sales_unit1,

			(SELECT SUM(x.sales_unit) FROM comparative_volume_tbl x WHERE g.bc_id = x.bc_id AND x.sales_status = 1 AND x.sales_vol = "LIVE SALES" AND YEAR(x.trans_year) = ' . ($year - 2) . ') as live_sales_unit2
		');

		$data['live'] = $get_live->live_sales_unit;
		$data['live1'] = $get_live->live_sales_unit1;
		$data['live2'] = $get_live->live_sales_unit2;

		$join_dressed_others = array(
			'sales_item_tbl b' => 'a.sales_id = b.sales_id AND a.sales_status = 1 AND b.sales_item_status = 1 AND a.sales_year = ' . $year,
			'sales_details_tbl c' => 'b.sales_item_id = c.sales_item_id AND c.sales_det_status = 1',
			'material_tbl d' => 'b.material_id = d.material_id',
			'material_group_tbl e' => 'd.material_group_id = e.material_group_id AND e.material_group_name = \'DRESSED\'',
			'material_unit_tbl f' => 'd.material_id = f.material_id AND f.material_unit_status = 1',
			'outlet_tbl g' => 'a.outlet_id = g.outlet_id AND g.bc_id = ' . $bc_id,
			'outlet_brand_tbl h' => 'g.outlet_id = h.outlet_id AND h.outlet_brand_status = 1',
			'brand_tbl i' => 'h.brand_id = i.brand_id AND i.brand_name = \'TRADE DISTRIBUTOR\''
		);

		$get_dressed_others = $this->admin->get_join('sales_tbl a', $join_dressed_others, TRUE, FALSE, FALSE, 'SUM(c.sales_det_qty / f.sales_unit_equivalent) as dressed_distributor_sales_unit
		');
		$data['dressed_distributor'] = $get_dressed_others->dressed_distributor_sales_unit;

		$get_dressed_others1 = $this->admin->check_query('SELECT SUM(x.sales_unit) as dressed_distributor_sales_unit1 FROM comparative_volume_tbl x WHERE x.sales_status = 1 AND x.sales_vol = "DRESSED - DISTRIBUTOR" AND YEAR(x.trans_year) = ' . ($year - 1) . ' AND x.bc_id = ' . $bc_id, TRUE);

		$data['dressed_distributor1'] = 0;
		if($get_dressed_others1['result'] == TRUE){
			$data['dressed_distributor1'] = $get_dressed_others1['info']->dressed_distributor_sales_unit1;
		}

		$get_dressed_others2 = $this->admin->check_query('SELECT SUM(x.sales_unit) as dressed_distributor_sales_unit2 FROM comparative_volume_tbl x WHERE x.sales_status = 1 AND x.sales_vol = "DRESSED - DISTRIBUTOR" AND YEAR(x.trans_year) = ' . ($year - 2) . ' AND x.bc_id = ' . $bc_id, TRUE);

		$data['dressed_distributor2'] = 0;
		if($get_dressed_others2['result'] == TRUE){
			$data['dressed_distributor2'] = $get_dressed_others2['info']->dressed_distributor_sales_unit2;
		}

		
		/*Dressed Dealer*/

		$join_dressed_dealer = array(
			'sales_item_tbl b' => 'a.sales_id = b.sales_id AND a.sales_status = 1 AND b.sales_item_status = 1 AND a.sales_year = ' . $year,
			'sales_details_tbl c' => 'b.sales_item_id = c.sales_item_id AND c.sales_det_status = 1',
			'material_tbl d' => 'b.material_id = d.material_id',
			'material_group_tbl e' => 'd.material_group_id = e.material_group_id AND e.material_group_name = \'DRESSED\'',
			'material_unit_tbl f' => 'd.material_id = f.material_id AND f.material_unit_status = 1',
			'outlet_tbl g' => 'a.outlet_id = g.outlet_id AND g.bc_id = ' . $bc_id,
			'outlet_brand_tbl h' => 'g.outlet_id = h.outlet_id AND h.outlet_brand_status = 1',
			'brand_tbl i' => 'h.brand_id = i.brand_id AND i.brand_name = \'DEALER\''
		);

		$get_dressed_dealer = $this->admin->get_join('sales_tbl a', $join_dressed_dealer, TRUE, FALSE, FALSE, 'SUM(c.sales_det_qty / f.sales_unit_equivalent) as dressed_dealer_sales_unit

		');
		$data['dressed_dealer'] = $get_dressed_dealer->dressed_dealer_sales_unit;


		$get_dressed_dealer1 = $this->admin->check_query('SELECT SUM(x.sales_unit) as dressed_dealer_sales_unit1 FROM comparative_volume_tbl x WHERE x.sales_status = 1 AND x.sales_vol = "DRESSED - DEALER" AND YEAR(x.trans_year) = ' . ($year - 1) . ' AND x.bc_id = ' . $bc_id, TRUE);

		$data['dressed_dealer1'] = 0;
		if($get_dressed_dealer1['result'] == TRUE){
			$data['dressed_dealer1'] = $get_dressed_dealer1['info']->dressed_dealer_sales_unit1;	
		}


		$get_dressed_dealer2 = $this->admin->check_query('SELECT SUM(x.sales_unit) as dressed_dealer_sales_unit2 FROM comparative_volume_tbl x WHERE x.sales_status = 1 AND x.sales_vol = "DRESSED - DEALER" AND YEAR(x.trans_year) = ' . ($year - 2) . ' AND x.bc_id = ' . $bc_id, TRUE);	
			
		$data['dressed_dealer2'] = 0;
		if($get_dressed_dealer2['result'] == TRUE){
			$data['dressed_dealer2'] = $get_dressed_dealer2['info']->dressed_dealer_sales_unit2;	
		}

		


		/*Dressed HRI*/

		$join_dressed_hri = array(
			'sales_item_tbl b' => 'a.sales_id = b.sales_id AND a.sales_status = 1 AND b.sales_item_status = 1 AND a.sales_year = ' . $year,
			'sales_details_tbl c' => 'b.sales_item_id = c.sales_item_id AND c.sales_det_status = 1',
			'material_tbl d' => 'b.material_id = d.material_id',
			'material_group_tbl e' => 'd.material_group_id = e.material_group_id AND e.material_group_name = \'DRESSED\'',
			'material_unit_tbl f' => 'd.material_id = f.material_id AND f.material_unit_status = 1',
			'outlet_tbl g' => 'a.outlet_id = g.outlet_id AND g.bc_id = ' . $bc_id,
			'outlet_brand_tbl h' => 'g.outlet_id = h.outlet_id AND h.outlet_brand_status = 1',
			'brand_tbl i' => 'h.brand_id = i.brand_id AND i.brand_name = \'HRI\''
		);

		$get_dressed_hri = $this->admin->get_join('sales_tbl a', $join_dressed_hri, TRUE, FALSE, FALSE, 'SUM(c.sales_det_qty / f.sales_unit_equivalent) as dressed_hri_sales_unit,

			(SELECT SUM(x.sales_unit) FROM comparative_volume_tbl x WHERE g.bc_id = x.bc_id AND x.sales_status = 1 AND x.sales_vol = "DRESSED - HRI" AND YEAR(x.trans_year) = ' . ($year - 1) . ') as dressed_hri_sales_unit1,

			(SELECT SUM(x.sales_unit) FROM comparative_volume_tbl x WHERE g.bc_id = x.bc_id AND x.sales_status = 1 AND x.sales_vol = "DRESSED - HRI" AND YEAR(x.trans_year) = ' . ($year - 2) . ') as dressed_hri_sales_unit2

		');
		$data['dressed_hri'] = $get_dressed_hri->dressed_hri_sales_unit;
		$data['dressed_hri1'] = $get_dressed_hri->dressed_hri_sales_unit1;
		$data['dressed_hri2'] = $get_dressed_hri->dressed_hri_sales_unit2;

		$join_dressed_sup = array(
			'sales_item_tbl b' => 'a.sales_id = b.sales_id AND a.sales_status = 1 AND b.sales_item_status = 1 AND a.sales_year = ' . $year,
			'sales_details_tbl c' => 'b.sales_item_id = c.sales_item_id AND c.sales_det_status = 1',
			'material_tbl d' => 'b.material_id = d.material_id',
			'material_group_tbl e' => 'd.material_group_id = e.material_group_id AND e.material_group_name = \'DRESSED\'',
			'material_unit_tbl f' => 'd.material_id = f.material_id AND f.material_unit_status = 1',
			'outlet_tbl g' => 'a.outlet_id = g.outlet_id AND g.bc_id = ' . $bc_id,
			'outlet_brand_tbl h' => 'g.outlet_id = h.outlet_id AND h.outlet_brand_status = 1',
			'brand_tbl i' => 'h.brand_id = i.brand_id AND i.brand_name = \'SUPERMARKET\''
		);

		$get_dressed_sup = $this->admin->get_join('sales_tbl a', $join_dressed_sup, TRUE, FALSE, FALSE, 'SUM(c.sales_det_qty / f.sales_unit_equivalent) as dressed_sup_sales_unit,

			(SELECT SUM(x.sales_unit) FROM comparative_volume_tbl x WHERE g.bc_id = x.bc_id AND x.sales_status = 1 AND x.sales_vol = "DRESSED - SUPERMARKET" AND YEAR(x.trans_year) = ' . ($year - 1) . ') as dressed_sup_sales_unit1,

			(SELECT SUM(x.sales_unit) FROM comparative_volume_tbl x WHERE g.bc_id = x.bc_id AND x.sales_status = 1 AND x.sales_vol = "DRESSED - SUPERMARKET" AND YEAR(x.trans_year) = ' . ($year - 2) . ') as dressed_sup_sales_unit2
		');
		$data['dressed_sup'] = $get_dressed_sup->dressed_sup_sales_unit;
		$data['dressed_sup1'] = $get_dressed_sup->dressed_sup_sales_unit1;
		$data['dressed_sup2'] = $get_dressed_sup->dressed_sup_sales_unit2;


		$join_dressed_retail = array(
			'sales_item_tbl b' => 'a.sales_id = b.sales_id AND a.sales_status = 1 AND b.sales_item_status = 1 AND a.sales_year = ' . $year,
			'sales_details_tbl c' => 'b.sales_item_id = c.sales_item_id AND c.sales_det_status = 1',
			'material_tbl d' => 'b.material_id = d.material_id',
			'material_group_tbl e' => 'd.material_group_id = e.material_group_id AND e.material_group_name = \'DRESSED\'',
			'material_unit_tbl f' => 'd.material_id = f.material_id AND f.material_unit_status = 1',
			'outlet_tbl g' => 'a.outlet_id = g.outlet_id AND g.bc_id = ' . $bc_id,
			'outlet_brand_tbl h' => 'g.outlet_id = h.outlet_id AND h.outlet_brand_status = 1',
			'brand_tbl i' => 'h.brand_id = i.brand_id',
			'brand_type_tbl j' => 'i.brand_type_id = j.brand_type_id AND j.brand_type_name = \'RETAIL\''
		);

		$get_dressed_retail = $this->admin->get_join('sales_tbl a', $join_dressed_retail, TRUE, FALSE, FALSE, 'SUM(c.sales_det_qty / f.sales_unit_equivalent) as dressed_retail_sales_unit,

			(SELECT SUM(x.sales_unit) FROM comparative_volume_tbl x WHERE g.bc_id = x.bc_id AND x.sales_status = 1 AND x.sales_vol = "DRESSED" AND YEAR(x.trans_year) = ' . ($year - 1) . ') as dressed_retail_sales_unit1,

			(SELECT SUM(x.sales_unit) FROM comparative_volume_tbl x WHERE g.bc_id = x.bc_id AND x.sales_status = 1 AND x.sales_vol = "DRESSED" AND YEAR(x.trans_year) = ' . ($year - 2) . ') as dressed_retail_sales_unit2
		');
		$data['dressed_retail'] = $get_dressed_retail->dressed_retail_sales_unit;
		$data['dressed_retail1'] = $get_dressed_retail->dressed_retail_sales_unit1;
		$data['dressed_retail2'] = $get_dressed_retail->dressed_retail_sales_unit2;

		$join_dressed_vansales = array(
			'sales_item_tbl b' => 'a.sales_id = b.sales_id AND a.sales_status = 1 AND b.sales_item_status = 1 AND a.sales_year = ' . $year,
			'sales_details_tbl c' => 'b.sales_item_id = c.sales_item_id AND c.sales_det_status = 1',
			'material_tbl d' => 'b.material_id = d.material_id',
			'material_group_tbl e' => 'd.material_group_id = e.material_group_id AND e.material_group_name = \'DRESSED\'',
			'material_unit_tbl f' => 'd.material_id = f.material_id AND f.material_unit_status = 1',
			'outlet_tbl g' => 'a.outlet_id = g.outlet_id AND g.bc_id = ' . $bc_id,
			'outlet_brand_tbl h' => 'g.outlet_id = h.outlet_id AND h.outlet_brand_status = 1',
			'brand_tbl i' => 'h.brand_id = i.brand_id',
			'brand_type_tbl j' => 'i.brand_type_id = j.brand_type_id AND i.brand_name = \'VAN SALES\''
		);

		$get_dressed_vansales = $this->admin->get_join('sales_tbl a', $join_dressed_vansales, TRUE, FALSE, FALSE, 'SUM(c.sales_det_qty / f.sales_unit_equivalent) as dressed_vansales_unit,

			(SELECT SUM(x.sales_unit) FROM comparative_volume_tbl x WHERE g.bc_id = x.bc_id AND x.sales_status = 1 AND x.sales_vol = "DRESSED - VANSALES" AND YEAR(x.trans_year) = ' . ($year - 1) . ') as dressed_vansales_unit1,

			(SELECT SUM(x.sales_unit) FROM comparative_volume_tbl x WHERE g.bc_id = x.bc_id AND x.sales_status = 1 AND x.sales_vol = "DRESSED - VANSALES" AND YEAR(x.trans_year) = ' . ($year - 2) . ') as dressed_vansales_unit2
		');

		$data['dressed_vansales'] = $get_dressed_vansales->dressed_vansales_unit;
		$data['dressed_vansales1'] = $get_dressed_vansales->dressed_vansales_unit1;
		$data['dressed_vansales2'] = $get_dressed_vansales->dressed_vansales_unit2;

		$join_non_marinated = array(
			'sales_item_tbl b' => 'a.sales_id = b.sales_id AND a.sales_status = 1 AND b.sales_item_status = 1 AND a.sales_year = ' . $year,
			'sales_details_tbl c' => 'b.sales_item_id = c.sales_item_id AND c.sales_det_status = 1',
			'material_tbl d' => 'b.material_id = d.material_id',
			'material_group_tbl e' => 'd.material_group_id = e.material_group_id AND e.material_group_name = \'NON MARINATED CUT UPS\'',
			'material_unit_tbl f' => 'd.material_id = f.material_id AND f.material_unit_status = 1',
			'outlet_tbl g' => 'a.outlet_id = g.outlet_id AND g.bc_id = ' . $bc_id,
			'outlet_brand_tbl h' => 'g.outlet_id = h.outlet_id AND h.outlet_brand_status = 1',
			'brand_tbl i' => 'h.brand_id = i.brand_id',
			'brand_type_tbl j' => 'i.brand_type_id = j.brand_type_id'
			/*'brand_type_tbl j' => 'i.brand_type_id = j.brand_type_id AND j.brand_type_name = \'RETAIL\''*/
		);

		$get_non_marinated = $this->admin->get_join('sales_tbl a', $join_non_marinated, TRUE, FALSE, FALSE, 'SUM(c.sales_det_qty / f.sales_unit_equivalent) as non_marinated_sales_unit,

			(SELECT SUM(x.sales_unit) FROM comparative_volume_tbl x WHERE g.bc_id = x.bc_id AND x.sales_status = 1 AND x.sales_vol = "NON MARINATED CUT UPS" AND YEAR(x.trans_year) = ' . ($year - 1) . ') as non_marinated_sales_unit1,

			(SELECT SUM(x.sales_unit) FROM comparative_volume_tbl x WHERE g.bc_id = x.bc_id AND x.sales_status = 1 AND x.sales_vol = "NON MARINATED CUT UPS" AND YEAR(x.trans_year) = ' . ($year - 2) . ') as non_marinated_sales_unit2

			');
		$data['non_marinated'] = $get_non_marinated->non_marinated_sales_unit;
		$data['non_marinated1'] = $get_non_marinated->non_marinated_sales_unit1;
		$data['non_marinated2'] = $get_non_marinated->non_marinated_sales_unit2;

		$join_marinated = array(
			'sales_item_tbl b' => 'a.sales_id = b.sales_id AND a.sales_status = 1 AND b.sales_item_status = 1 AND a.sales_year = ' . $year,
			'sales_details_tbl c' => 'b.sales_item_id = c.sales_item_id AND c.sales_det_status = 1',
			'material_tbl d' => 'b.material_id = d.material_id AND d.material_code NOT IN ("1000090", "10200090", "1000401", "1000402")',
			'material_group_tbl e' => 'd.material_group_id = e.material_group_id AND e.material_group_name = \'MARINATED CUT UPS\'',
			'material_unit_tbl f' => 'd.material_id = f.material_id AND f.material_unit_status = 1',
			'outlet_tbl g' => 'a.outlet_id = g.outlet_id AND g.bc_id = ' . $bc_id,
			'outlet_brand_tbl h' => 'g.outlet_id = h.outlet_id AND h.outlet_brand_status = 1',
			'brand_tbl i' => 'h.brand_id = i.brand_id',
			'brand_type_tbl j' => 'i.brand_type_id = j.brand_type_id'
			/*'brand_type_tbl j' => 'i.brand_type_id = j.brand_type_id AND j.brand_type_name = \'RETAIL\''*/
		);

		$get_marinated = $this->admin->get_join('sales_tbl a', $join_marinated, TRUE, FALSE, FALSE, 'SUM(c.sales_det_qty / f.sales_unit_equivalent) as marinated_sales_unit,

			(SELECT SUM(x.sales_unit) FROM comparative_volume_tbl x WHERE g.bc_id = x.bc_id AND x.sales_status = 1 AND x.sales_vol = "MARINATED CUT UPS" AND YEAR(x.trans_year) = ' . ($year - 1) . ') as marinated_sales_unit1,

			(SELECT SUM(x.sales_unit) FROM comparative_volume_tbl x WHERE g.bc_id = x.bc_id AND x.sales_status = 1 AND x.sales_vol = "MARINATED CUT UPS" AND YEAR(x.trans_year) = ' . ($year - 2) . ') as marinated_sales_unit2


			');
		$data['marinated'] = $get_marinated->marinated_sales_unit;
		$data['marinated1'] = $get_marinated->marinated_sales_unit1;
		$data['marinated2'] = $get_marinated->marinated_sales_unit2;

		$join_spicy_neck = array(
			'sales_item_tbl b' => 'a.sales_id = b.sales_id AND a.sales_status = 1 AND b.sales_item_status = 1 AND a.sales_year = ' . $year,
			'sales_details_tbl c' => 'b.sales_item_id = c.sales_item_id AND c.sales_det_status = 1',
			'material_tbl d' => 'b.material_id = d.material_id AND d.material_code = "10200090"',
			'material_group_tbl e' => 'd.material_group_id = e.material_group_id AND e.material_group_name = \'MARINATED CUT UPS\'',
			'material_unit_tbl f' => 'd.material_id = f.material_id AND f.material_unit_status = 1',
			'outlet_tbl g' => 'a.outlet_id = g.outlet_id AND g.bc_id = ' . $bc_id,
			'outlet_brand_tbl h' => 'g.outlet_id = h.outlet_id AND h.outlet_brand_status = 1',
			'brand_tbl i' => 'h.brand_id = i.brand_id',
			'brand_type_tbl j' => 'i.brand_type_id = j.brand_type_id AND j.brand_type_name = \'RETAIL\''
		);

		$get_spicy_neck = $this->admin->get_join('sales_tbl a', $join_spicy_neck, TRUE, FALSE, FALSE, 'SUM(c.sales_det_qty / f.sales_unit_equivalent) as spicy_neck_sales_unit,

			(SELECT SUM(x.sales_unit) FROM comparative_volume_tbl x WHERE g.bc_id = x.bc_id AND x.sales_status = 1 AND x.sales_vol = "SPICY NECK" AND YEAR(x.trans_year) = ' . ($year - 1) . ') as spicy_neck_sales_unit1,

			(SELECT SUM(x.sales_unit) FROM comparative_volume_tbl x WHERE g.bc_id = x.bc_id AND x.sales_status = 1 AND x.sales_vol = "SPICY NECK" AND YEAR(x.trans_year) = ' . ($year - 2) . ') as spicy_neck_sales_unit2
		');
		$data['spicy_neck'] = $get_spicy_neck->spicy_neck_sales_unit;
		$data['spicy_neck1'] = $get_spicy_neck->spicy_neck_sales_unit1;
		$data['spicy_neck2'] = $get_spicy_neck->spicy_neck_sales_unit2;

		$join_roasted_cutups = array(
			'sales_item_tbl b' => 'a.sales_id = b.sales_id AND a.sales_status = 1 AND b.sales_item_status = 1 AND a.sales_year = ' . $year,
			'sales_details_tbl c' => 'b.sales_item_id = c.sales_item_id AND c.sales_det_status = 1',
			'material_tbl d' => 'b.material_id = d.material_id',
			'material_group_tbl e' => 'd.material_group_id = e.material_group_id AND e.material_group_name = \'ROASTED CUT UPS\'',
			'material_unit_tbl f' => 'd.material_id = f.material_id AND f.material_unit_status = 1',
			'outlet_tbl g' => 'a.outlet_id = g.outlet_id AND g.bc_id = ' . $bc_id,
			'outlet_brand_tbl h' => 'g.outlet_id = h.outlet_id AND h.outlet_brand_status = 1',
			'brand_tbl i' => 'h.brand_id = i.brand_id',
			'brand_type_tbl j' => 'i.brand_type_id = j.brand_type_id AND j.brand_type_name = \'RETAIL\''
		);

		$get_roasted_cutups = $this->admin->get_join('sales_tbl a', $join_roasted_cutups, TRUE, FALSE, FALSE, 'SUM(c.sales_det_qty / f.sales_unit_equivalent) as roasted_cutups_sales_unit,

			(SELECT SUM(x.sales_unit) FROM comparative_volume_tbl x WHERE g.bc_id = x.bc_id AND x.sales_status = 1 AND x.sales_vol = "ROASTED CUT UPS" AND YEAR(x.trans_year) = ' . ($year - 1) . ') as roasted_cutups_sales_unit1,

			(SELECT SUM(x.sales_unit) FROM comparative_volume_tbl x WHERE g.bc_id = x.bc_id AND x.sales_status = 1 AND x.sales_vol = "ROASTED CUT UPS" AND YEAR(x.trans_year) = ' . ($year - 2) . ') as roasted_cutups_sales_unit2

			');
		$data['roasted_cutups'] = $get_roasted_cutups->roasted_cutups_sales_unit;
		$data['roasted_cutups1'] = $get_roasted_cutups->roasted_cutups_sales_unit1;
		$data['roasted_cutups2'] = $get_roasted_cutups->roasted_cutups_sales_unit2;

		$join_roasted_chicken = array(
			'sales_item_tbl b' => 'a.sales_id = b.sales_id AND a.sales_status = 1 AND b.sales_item_status = 1 AND a.sales_year = ' . $year,
			'sales_details_tbl c' => 'b.sales_item_id = c.sales_item_id AND c.sales_det_status = 1',
			'material_tbl d' => 'b.material_id = d.material_id',
			'material_group_tbl e' => 'd.material_group_id = e.material_group_id AND e.material_group_name = \'ROASTED CHICKEN\'',
			'material_unit_tbl f' => 'd.material_id = f.material_id AND f.material_unit_status = 1',
			'outlet_tbl g' => 'a.outlet_id = g.outlet_id AND g.bc_id = ' . $bc_id,
			'outlet_brand_tbl h' => 'g.outlet_id = h.outlet_id AND h.outlet_brand_status = 1',
			'brand_tbl i' => 'h.brand_id = i.brand_id',
			'brand_type_tbl j' => 'i.brand_type_id = j.brand_type_id AND j.brand_type_name = \'RETAIL\''
		);

		$get_roasted_chicken = $this->admin->get_join('sales_tbl a', $join_roasted_chicken, TRUE, FALSE, FALSE, 'SUM(c.sales_det_qty / f.sales_unit_equivalent) as roasted_chicken_sales_unit,

			(SELECT SUM(x.sales_unit) FROM comparative_volume_tbl x WHERE g.bc_id = x.bc_id AND x.sales_status = 1 AND x.sales_vol = "ROASTED CHICKEN" AND YEAR(x.trans_year) = ' . ($year - 1) . ') as roasted_chicken_sales_unit1,

			(SELECT SUM(x.sales_unit) FROM comparative_volume_tbl x WHERE g.bc_id = x.bc_id AND x.sales_status = 1 AND x.sales_vol = "ROASTED CHICKEN" AND YEAR(x.trans_year) = ' . ($year - 2) . ') as roasted_chicken_sales_unit2
		');
		$data['roasted_chicken'] = $get_roasted_chicken->roasted_chicken_sales_unit;
		$data['roasted_chicken1'] = $get_roasted_chicken->roasted_chicken_sales_unit1;
		$data['roasted_chicken2'] = $get_roasted_chicken->roasted_chicken_sales_unit2;

		$join_marinated_chicken_raw = array(
			'sales_item_tbl b' => 'a.sales_id = b.sales_id AND a.sales_status = 1 AND b.sales_item_status = 1 AND a.sales_year = ' . $year,
			'sales_details_tbl c' => 'b.sales_item_id = c.sales_item_id AND c.sales_det_status = 1',
			'material_tbl d' => 'b.material_id = d.material_id',
			'material_group_tbl e' => 'd.material_group_id = e.material_group_id AND e.material_group_name = \'MARINATED CHICKEN (RAW)\'',
			'material_unit_tbl f' => 'd.material_id = f.material_id AND f.material_unit_status = 1',
			'outlet_tbl g' => 'a.outlet_id = g.outlet_id AND g.bc_id = ' . $bc_id,
			'outlet_brand_tbl h' => 'g.outlet_id = h.outlet_id AND h.outlet_brand_status = 1',
			'brand_tbl i' => 'h.brand_id = i.brand_id',
			'brand_type_tbl j' => 'i.brand_type_id = j.brand_type_id'
			/*'brand_type_tbl j' => 'i.brand_type_id = j.brand_type_id AND j.brand_type_name = \'RETAIL\''*/
		);

		$get_marinated_chicken_raw = $this->admin->get_join('sales_tbl a', $join_marinated_chicken_raw, TRUE, FALSE, FALSE, 'SUM(c.sales_det_qty / f.sales_unit_equivalent) as marinated_chicken_raw_sales_unit,

			(SELECT SUM(x.sales_unit) FROM comparative_volume_tbl x WHERE g.bc_id = x.bc_id AND x.sales_status = 1 AND x.sales_vol = "MARINATED CHICKEN (RAW)" AND YEAR(x.trans_year) = ' . ($year - 1) . ') as marinated_chicken_raw_sales_unit1,

			(SELECT SUM(x.sales_unit) FROM comparative_volume_tbl x WHERE g.bc_id = x.bc_id AND x.sales_status = 1 AND x.sales_vol = "MARINATED CHICKEN (RAW)" AND YEAR(x.trans_year) = ' . ($year - 2) . ') as marinated_chicken_raw_sales_unit2

		');

		$data['marinated_chicken_raw'] = $get_marinated_chicken_raw->marinated_chicken_raw_sales_unit;


		$get_marinated_chicken_raw1 = $this->admin->check_query('SELECT SUM(x.sales_unit) as marinated_chicken_raw_sales_unit1 FROM comparative_volume_tbl x WHERE x.sales_status = 1 AND x.sales_vol = "MARINATED CHICKEN (RAW)" AND YEAR(x.trans_year) = ' . ($year - 1) . ' AND x.bc_id = ' . $bc_id, TRUE);

		$data['marinated_chicken_raw1'] = 0;
		if($get_marinated_chicken_raw1['result'] == TRUE){
			$data['marinated_chicken_raw1'] = $get_marinated_chicken_raw1['info']->marinated_chicken_raw_sales_unit1;	
		}

		$get_marinated_chicken_raw2 = $this->admin->check_query('SELECT SUM(x.sales_unit) as marinated_chicken_raw_sales_unit2 FROM comparative_volume_tbl x WHERE x.sales_status = 1 AND x.sales_vol = "MARINATED CHICKEN (RAW)" AND YEAR(x.trans_year) = ' . ($year - 2) . ' AND x.bc_id = ' . $bc_id, TRUE);

		$data['marinated_chicken_raw2'] = 0;
		if($get_marinated_chicken_raw2['result'] == TRUE){
			$data['marinated_chicken_raw2'] = $get_marinated_chicken_raw2['info']->marinated_chicken_raw_sales_unit2;	
		}

		

		$join_other = array(
			'sales_item_tbl b' => 'a.sales_id = b.sales_id AND a.sales_status = 1 AND b.sales_item_status = 1 AND a.sales_year = ' . $year,
			'sales_details_tbl c' => 'b.sales_item_id = c.sales_item_id AND c.sales_det_status = 1',
			'material_tbl d' => 'b.material_id = d.material_id',
			'material_group_tbl e' => 'd.material_group_id = e.material_group_id AND e.material_group_name = \'OTHER SPECIALTY PRODUCTS\'',
			'material_unit_tbl f' => 'd.material_id = f.material_id AND f.material_unit_status = 1',
			'outlet_tbl g' => 'a.outlet_id = g.outlet_id AND g.bc_id = ' . $bc_id,
			'outlet_brand_tbl h' => 'g.outlet_id = h.outlet_id AND h.outlet_brand_status = 1',
			'brand_tbl i' => 'h.brand_id = i.brand_id',
			'brand_type_tbl j' => 'i.brand_type_id = j.brand_type_id AND j.brand_type_name = \'RETAIL\''
		);

		$get_other = $this->admin->get_join('sales_tbl a', $join_other, TRUE, FALSE, FALSE, 'SUM(c.sales_det_qty / f.sales_unit_equivalent) as other_sales_unit,

			(SELECT SUM(x.sales_unit) FROM comparative_volume_tbl x WHERE g.bc_id = x.bc_id AND x.sales_status = 1 AND x.sales_vol = "OTHER SPECIALTY PRODUCTS" AND YEAR(x.trans_year) = ' . ($year - 1) . ') as other_sales_unit1,

			(SELECT SUM(x.sales_unit) FROM comparative_volume_tbl x WHERE g.bc_id = x.bc_id AND x.sales_status = 1 AND x.sales_vol = "OTHER SPECIALTY PRODUCTS" AND YEAR(x.trans_year) = ' . ($year - 2) . ') as other_sales_unit2
		');
		$data['others'] = $get_other->other_sales_unit;
		$data['others1'] = $get_other->other_sales_unit1;
		$data['others2'] = $get_other->other_sales_unit2;

		$join_liempo = array(
			'sales_item_tbl b' => 'a.sales_id = b.sales_id AND a.sales_status = 1 AND b.sales_item_status = 1 AND a.sales_year = ' . $year,
			'sales_details_tbl c' => 'b.sales_item_id = c.sales_item_id AND c.sales_det_status = 1',
			'material_tbl d' => 'b.material_id = d.material_id',
			'material_group_tbl e' => 'd.material_group_id = e.material_group_id AND e.material_group_name = \'LIEMPO\'',
			'material_unit_tbl f' => 'd.material_id = f.material_id AND f.material_unit_status = 1',
			'outlet_tbl g' => 'a.outlet_id = g.outlet_id AND g.bc_id = ' . $bc_id,
			'outlet_brand_tbl h' => 'g.outlet_id = h.outlet_id AND h.outlet_brand_status = 1',
			'brand_tbl i' => 'h.brand_id = i.brand_id',
			'brand_type_tbl j' => 'i.brand_type_id = j.brand_type_id AND j.brand_type_name = \'RETAIL\''
		);

		$get_liempo = $this->admin->get_join('sales_tbl a', $join_liempo, TRUE, FALSE, FALSE, 'SUM(c.sales_det_qty / f.sales_unit_equivalent) as liempo_sales_unit
		');

		$data['liempo'] = $get_liempo->liempo_sales_unit;

		$get_liempo1 = $this->admin->check_query('SELECT SUM(x.sales_unit) as liempo_sales_unit1 FROM comparative_volume_tbl x WHERE x.sales_status = 1 AND x.sales_vol = "LIEMPO" AND YEAR(x.trans_year) = ' . ($year - 1) . ' AND x.bc_id = ' . $bc_id, TRUE);

		$data['liempo1'] = 0;
		if($get_liempo1['result'] == 1){
			$data['liempo1'] = $get_liempo1['info']->liempo_sales_unit1;
		}

		$get_liempo2 = $this->admin->check_query('SELECT SUM(x.sales_unit) as liempo_sales_unit2 FROM comparative_volume_tbl x WHERE x.sales_status = 1 AND x.sales_vol = "LIEMPO" AND YEAR(x.trans_year) = ' . ($year - 2) . ' AND x.bc_id = ' . $bc_id, TRUE);

		$data['liempo2'] = 0;
		if($get_liempo2['result'] == 1){
			$data['liempo2'] = $get_liempo2['info']->liempo_sales_unit2;
		}
		

		$join_vap = array(
			'sales_item_tbl b' => 'a.sales_id = b.sales_id AND a.sales_status = 1 AND b.sales_item_status = 1 AND a.sales_year = ' . $year,
			'sales_details_tbl c' => 'b.sales_item_id = c.sales_item_id AND c.sales_det_status = 1',
			'material_tbl d' => 'b.material_id = d.material_id',
			'material_group_tbl e' => 'd.material_group_id = e.material_group_id AND e.material_group_name = \'OTHER VAP PRODUCTS\'',
			'material_unit_tbl f' => 'd.material_id = f.material_id AND f.material_unit_status = 1',
			'outlet_tbl g' => 'a.outlet_id = g.outlet_id AND g.bc_id = ' . $bc_id,
			'outlet_brand_tbl h' => 'g.outlet_id = h.outlet_id AND h.outlet_brand_status = 1',
			'brand_tbl i' => 'h.brand_id = i.brand_id',
			'brand_type_tbl j' => 'i.brand_type_id = j.brand_type_id AND j.brand_type_name = \'RETAIL\''
		);

		$get_vap = $this->admin->get_join('sales_tbl a', $join_vap, TRUE, FALSE, FALSE, 'SUM(c.sales_det_qty / f.sales_unit_equivalent) as vap_sales_unit	

		');

		$data['vap'] = $get_vap->vap_sales_unit;

		$get_vap1 = $this->admin->check_query('SELECT SUM(x.sales_unit) as vap_sales_unit1 FROM comparative_volume_tbl x WHERE x.sales_status = 1 AND x.sales_vol = "VAP" AND YEAR(x.trans_year) = ' . ($year - 1) . ' AND x.bc_id = ' . $bc_id, TRUE);

		$data['vap1'] = 0;
		if($get_vap1['result'] == TRUE){
			$data['vap1'] = $get_vap1['info']->vap_sales_unit1;	
		}

		$get_vap2 = $this->admin->check_query('SELECT SUM(x.sales_unit) as vap_sales_unit2 FROM comparative_volume_tbl x WHERE x.sales_status = 1 AND x.sales_vol = "VAP" AND YEAR(x.trans_year) = ' . ($year - 2) . ' AND x.bc_id = ' . $bc_id, TRUE);
		
		$data['vap2'] = 0;
		if($get_vap2['result'] == TRUE){
			$data['vap2'] = $get_vap2['info']->vap_sales_unit2;	
		}

		$get_transfer = $this->admin->check_data('volume_others_tbl', array('bc_id' => $bc_id, 'volume_others_year' => $year, 'volume_others_status' => 1), TRUE);
		$data['transfer'] = 0;
		$data['transfer1'] = 0;
		$data['transfer2'] = 0;
		if($get_transfer['result'] == TRUE){
			$data['transfer'] = $get_transfer['info']->volume_others_unit;
		}
	}

	public function _get_sales_info_volume_tbl($volume){
		$info = $this->_require_login();

		$live = $volume['live'];
		$live1 = $volume['live1'];
		$live2 = $volume['live2'];
		$live_dif1 = $live - $live1;
		$live_dif2 = $live - $live2;
		$live_per1 = $live1 > 0 ? ($live_dif1 / $live1) * 100 : 0;
		$live_per2 = $live2 > 0 ? ($live_dif2 / $live2) * 100 : 0;

		$dressed_distributor = $volume['dressed_distributor'];
		$dressed_distributor1 = $volume['dressed_distributor1'];
		$dressed_distributor2 = $volume['dressed_distributor2'];
		$dressed_distributor_dif1 = $dressed_distributor - $dressed_distributor1;
		$dressed_distributor_dif2 = $dressed_distributor - $dressed_distributor2;
		$dressed_distributor_per1 = $dressed_distributor1 > 0 ? ($dressed_distributor_dif1 / $dressed_distributor1) * 100 : 0;
		$dressed_distributor_per2 = $dressed_distributor2 > 0 ? ($dressed_distributor_dif2 / $dressed_distributor2) * 100 : 0;

		$dressed_dealer = $volume['dressed_dealer'];
		$dressed_dealer1 = $volume['dressed_dealer1'];
		$dressed_dealer2 = $volume['dressed_dealer2'];
		$dressed_dealer_dif1 = $dressed_dealer - $dressed_dealer1;
		$dressed_dealer_dif2 = $dressed_dealer - $dressed_dealer2;
		$dressed_dealer_per1 = $dressed_dealer1 > 0 ? ($dressed_dealer_dif1 / $dressed_dealer1) * 100 : 0;
		$dressed_dealer_per2 = $dressed_dealer2 > 0 ? ($dressed_dealer_dif2 / $dressed_dealer2) * 100 : 0;

		$dressed_hri = $volume['dressed_hri'];
		$dressed_hri1 = $volume['dressed_hri1'];
		$dressed_hri2 = $volume['dressed_hri2'];
		$dressed_hri_dif1 = $dressed_hri - $dressed_hri1;
		$dressed_hri_dif2 = $dressed_hri - $dressed_hri2;
		$dressed_hri_per1 = $dressed_hri1 > 0 ? ($dressed_hri_dif1 / $dressed_hri1) * 100 : 0;
		$dressed_hri_per2 = $dressed_hri2 > 0 ? ($dressed_hri_dif2 / $dressed_hri2) * 100 : 0;


		$dressed_sup = $volume['dressed_sup'];
		$dressed_sup1 = $volume['dressed_sup1'];
		$dressed_sup2 = $volume['dressed_sup2'];
		$dressed_sup_dif1 = $dressed_sup - $dressed_sup1;
		$dressed_sup_dif2 = $dressed_sup - $dressed_sup2;
		$dressed_sup_per1 = $dressed_sup1 > 0 ? ($dressed_sup_dif1 / $dressed_sup1) * 100 : 0;
		$dressed_sup_per2 = $dressed_sup2 > 0 ? ($dressed_sup_dif2 / $dressed_sup2) * 100 : 0;

		$dressed_vansales = $volume['dressed_vansales'];
		$dressed_vansales1 = $volume['dressed_vansales1'];
		$dressed_vansales2 = $volume['dressed_vansales2'];
		$dressed_vansales_dif1 = $dressed_vansales - $dressed_vansales1;
		$dressed_vansales_dif2 = $dressed_vansales - $dressed_vansales2;
		$dressed_vansales_per1 = $dressed_vansales1 > 0 ? ($dressed_vansales_dif1 / $dressed_vansales1) * 100 : 0;
		$dressed_vansales_per2 = $dressed_vansales2 > 0 ? ($dressed_vansales_dif2 / $dressed_vansales2) * 100 : 0;


		$dressed_retail = $volume['dressed_retail'];
		$dressed_retail1 = $volume['dressed_retail1'];
		$dressed_retail2 = $volume['dressed_retail2'];
		$dressed_retail_dif1 = $dressed_retail - $dressed_retail1;
		$dressed_retail_dif2 = $dressed_retail - $dressed_retail2;
		$dressed_retail_per1 = $dressed_retail1 > 0 ? ($dressed_retail_dif1 / $dressed_retail1) * 100 : 0;
		$dressed_retail_per2 = $dressed_retail2 > 0 ? ($dressed_retail_dif2 / $dressed_retail2) * 100 : 0;
		

		$non_marinated = $volume['non_marinated'];
		$non_marinated1 = $volume['non_marinated1'];
		$non_marinated2 = $volume['non_marinated2'];
		$non_marinated_dif1 = $non_marinated - $non_marinated1;
		$non_marinated_dif2 = $non_marinated - $non_marinated2;
		$non_marinated_per1 = $non_marinated1 > 0 ? ($non_marinated_dif1 / $non_marinated1) * 100 : 0;
		$non_marinated_per2 = $non_marinated2 > 0 ? ($non_marinated_dif2 / $non_marinated2) * 100 : 0;


		$marinated = $volume['marinated'];
		$marinated1 = $volume['marinated1'];
		$marinated2 = $volume['marinated2'];
		$marinated_dif1 = $marinated - $marinated1;
		$marinated_dif2 = $marinated - $marinated2;
		$marinated_per1 = $marinated1 > 0 ? ($marinated_dif1 / $marinated1) * 100 : 0;
		$marinated_per2 = $marinated2 > 0 ? ($marinated_dif2 / $marinated2) * 100 : 0;


		$spicy_neck = $volume['spicy_neck'];
		$spicy_neck1 = $volume['spicy_neck1'];
		$spicy_neck2 = $volume['spicy_neck2'];
		$spicy_neck_dif1 = $spicy_neck - $spicy_neck1;
		$spicy_neck_dif2 = $spicy_neck - $spicy_neck2;
		$spicy_neck_per1 = $spicy_neck1 > 0 ? ($spicy_neck_dif1 / $spicy_neck1) * 100 : 0;
		$spicy_neck_per2 = $spicy_neck2 > 0 ? ($spicy_neck_dif2 / $spicy_neck2) * 100 : 0;


		$roasted_cutups = $volume['roasted_cutups'];
		$roasted_cutups1 = $volume['roasted_cutups1'];
		$roasted_cutups2 = $volume['roasted_cutups2'];
		$roasted_cutups_dif1 = $roasted_cutups - $roasted_cutups1;
		$roasted_cutups_dif2 = $roasted_cutups - $roasted_cutups2;
		$roasted_cutups_per1 = $roasted_cutups1 > 0 ? ($roasted_cutups_dif1 / $roasted_cutups1) * 100 : 0;
		$roasted_cutups_per2 = $roasted_cutups2 > 0 ? ($roasted_cutups_dif2 / $roasted_cutups2) * 100 : 0;


		$roasted_chicken = $volume['roasted_chicken'];
		$roasted_chicken1 = $volume['roasted_chicken1'];
		$roasted_chicken2 = $volume['roasted_chicken2'];
		$roasted_chicken_dif1 = $roasted_chicken - $roasted_chicken1;
		$roasted_chicken_dif2 = $roasted_chicken - $roasted_chicken2;
		$roasted_chicken_per1 = $roasted_chicken1 > 0 ? ($roasted_chicken_dif1 / $roasted_chicken1) * 100 : 0;
		$roasted_chicken_per2 = $roasted_chicken2 > 0 ? ($roasted_chicken_dif2 / $roasted_chicken2) * 100 : 0;

		
		$marinated_chicken_raw = $volume['marinated_chicken_raw'];
		$marinated_chicken_raw1 = $volume['marinated_chicken_raw1'];
		$marinated_chicken_raw2 = $volume['marinated_chicken_raw2'];
		$marinated_chicken_raw_dif1 = $marinated_chicken_raw - $marinated_chicken_raw1;
		$marinated_chicken_raw_dif2 = $marinated_chicken_raw - $marinated_chicken_raw2;
		$marinated_chicken_raw_per1 = $marinated_chicken_raw1 > 0 ? ($marinated_chicken_raw_dif1 / $marinated_chicken_raw1) * 100 : 0;
		$marinated_chicken_raw_per2 = $marinated_chicken_raw2 > 0 ? ($marinated_chicken_raw_dif2 / $marinated_chicken_raw2) * 100 : 0;

		
		$others = $volume['others'];
		$others1 = $volume['others1'];
		$others2 = $volume['others2'];
		$others_dif1 = $others - $others1;
		$others_dif2 = $others - $others2;
		$others_per1 = $others1 > 0 ? ($others_dif1 / $others1) * 100 : 0;
		$others_per2 = $others2 > 0 ? ($others_dif2 / $others2) * 100 : 0;


		$sub_total = $live + $dressed_distributor + $dressed_dealer + $dressed_hri + $dressed_sup + $dressed_retail + $dressed_vansales + $non_marinated + $marinated + $spicy_neck + $roasted_cutups + $roasted_chicken + $marinated_chicken_raw + $others;

		$liempo = $volume['liempo'];
		$liempo1 = $volume['liempo1'];
		$liempo2 = $volume['liempo2'];
		$liempo_dif1 = $liempo - $liempo1;
		$liempo_dif2 = $liempo - $liempo2;
		$liempo_per1 = $liempo1 > 0 ? ($liempo_dif1 / $liempo1) * 100 : 0;
		$liempo_per2 = $liempo2 > 0 ? ($liempo_dif2 / $liempo2) * 100 : 0;


		$vap = $volume['vap'];
		$vap1 = $volume['vap1'];
		$vap2 = $volume['vap2'];
		$vap_dif1 = $vap - $vap1;
		$vap_dif2 = $vap - $vap2;
		$vap_per1 = $vap1 > 0 ? ($vap_dif1 / $vap1) * 100 : 0;
		$vap_per2 = $vap2 > 0 ? ($vap_dif2 / $vap2) * 100 : 0;

		$transfer = $volume['transfer'];
		$transfer1 = $volume['transfer1'];
		$transfer2 = $volume['transfer2'];
		$transfer_dif1 = $transfer - $transfer1;
		$transfer_dif2 = $transfer - $transfer2;
		$transfer_per1 = $transfer1 > 0 ? ($transfer_dif1 / $transfer1) * 100 : 0;
		$transfer_per2 = $transfer2 > 0 ? ($transfer_dif2 / $transfer2) * 100 : 0;



		$total = $live + $dressed_distributor + $dressed_dealer + $dressed_hri + $dressed_sup + $dressed_retail + $dressed_vansales + $non_marinated + $marinated + $spicy_neck + $roasted_cutups + $roasted_chicken + $marinated_chicken_raw + $others + $liempo + $vap + $transfer;

		$total1 = $live1 + $dressed_distributor1 + $dressed_dealer1 + $dressed_hri1 + $dressed_sup1 + $dressed_retail1 + $dressed_vansales1 + $non_marinated1 + $marinated1 + $spicy_neck1 + $roasted_cutups1 + $roasted_chicken1 + $marinated_chicken_raw1 + $others1 + $liempo1 + $vap1 + $transfer1;

		$total2 = $live2 + $dressed_distributor2 + $dressed_dealer2 + $dressed_hri2 + $dressed_sup2 + $dressed_retail2 + $dressed_vansales2 + $non_marinated2 + $marinated2 + $spicy_neck2 + $roasted_cutups2 + $roasted_chicken2 + $marinated_chicken_raw2 + $others2 + $liempo2 + $vap2 + $transfer2;

		$variance1 = $total - $total1;
		$variance2 = $total - $total2;
		$percent1 = $total1 > 0 ? ($variance1 / $total1) * 100 : 0;
		$percent2 = $total2 > 0 ? ($variance2 / $total2) * 100 : 0;

		$total_heads = $live + $dressed_distributor + $dressed_dealer + $dressed_hri + $dressed_sup + $dressed_retail + $dressed_vansales + $non_marinated + $marinated + $roasted_cutups + $roasted_chicken + $marinated_chicken_raw + $others;


		$volume_tbl = '


	        <tr>
	            <td class="text-left">DRESSED</td>
	            <td class="text-right">' . number_format($dressed_retail) . '</td>
	            <td class="text-right">' . number_format($dressed_retail1) . '</td>
	            <td class="text-right">' . number_format($dressed_retail2) . '</td>
	            <td class="text-right">' . number_format($dressed_retail_dif1) . '</td>
	            <td class="text-right">' . number_format($dressed_retail_per1) . '%</td>
	            <td class="text-right">' . number_format($dressed_retail_dif2) . '</td>
	            <td class="text-right">' . number_format($dressed_retail_per2) . '%</td>

	            <td class="text-right">' . number_format($dressed_retail) . '</td>
	            <td class="text-right">' . number_format($dressed_retail1) . '</td>
	            <td class="text-right">' . number_format($dressed_retail2) . '</td>
	            <td class="text-right">' . number_format($dressed_retail_dif1) . '</td>
	            <td class="text-right">' . number_format($dressed_retail_per1) . '%</td>
	            <td class="text-right">' . number_format($dressed_retail_dif2) . '</td>
	            <td class="text-right">' . number_format($dressed_retail_per2) . '%</td>
	        </tr>

	        <tr>
	            <td class="text-left">NON MARINATED CUT UPS</td>
	            <td class="text-right">' . number_format($non_marinated) . '</td>
	            <td class="text-right">' . number_format($non_marinated1) . '</td>
	            <td class="text-right">' . number_format($non_marinated2) . '</td>
	            <td class="text-right">' . number_format($non_marinated_dif1) . '</td>
	            <td class="text-right">' . number_format($non_marinated_per1) . '%</td>
	            <td class="text-right">' . number_format($non_marinated_dif2) . '</td>
	            <td class="text-right">' . number_format($non_marinated_per2) . '%</td>

	            <td class="text-right">' . number_format($non_marinated) . '</td>
	            <td class="text-right">' . number_format($non_marinated1) . '</td>
	            <td class="text-right">' . number_format($non_marinated2) . '</td>
	            <td class="text-right">' . number_format($non_marinated_dif1) . '</td>
	            <td class="text-right">' . number_format($non_marinated_per1) . '%</td>
	            <td class="text-right">' . number_format($non_marinated_dif2) . '</td>
	            <td class="text-right">' . number_format($non_marinated_per2) . '%</td>
	        </tr>

	        <tr>
	            <td class="text-left">MARINATED CUT UPS</td>
	            <td class="text-right">' . number_format($marinated) . '</td>
	            <td class="text-right">' . number_format($marinated1) . '</td>
	            <td class="text-right">' . number_format($marinated2) . '</td>
	            <td class="text-right">' . number_format($marinated_dif1) . '</td>
	            <td class="text-right">' . number_format($marinated_per1) . '%</td>
	            <td class="text-right">' . number_format($marinated_dif2) . '</td>
	            <td class="text-right">' . number_format($marinated_per2) . '%</td>

	            <td class="text-right">' . number_format($marinated) . '</td>
	            <td class="text-right">' . number_format($marinated1) . '</td>
	            <td class="text-right">' . number_format($marinated2) . '</td>
	            <td class="text-right">' . number_format($marinated_dif1) . '</td>
	            <td class="text-right">' . number_format($marinated_per1) . '%</td>
	            <td class="text-right">' . number_format($marinated_dif2) . '</td>
	            <td class="text-right">' . number_format($marinated_per2) . '%</td>
	        </tr>

	        <tr>
	            <td class="text-left">SPICY NECK</td>
	            <td class="text-right">' . number_format($spicy_neck) . '</td>
	            <td class="text-right">' . number_format($spicy_neck1) . '</td>
	            <td class="text-right">' . number_format($spicy_neck2) . '</td>
	            <td class="text-right">' . number_format($spicy_neck_dif1) . '</td>
	            <td class="text-right">' . number_format($spicy_neck_per1) . '%</td>
	            <td class="text-right">' . number_format($spicy_neck_dif2) . '</td>
	            <td class="text-right">' . number_format($spicy_neck_per2) . '%</td>

	            <td class="text-right"></td>
	            <td class="text-right"></td>
	            <td class="text-right"></td>
	            <td class="text-right"></td>
	            <td class="text-right"></td>
	            <td class="text-right"></td>
	            <td class="text-right"></td>
	        </tr>

	        <tr>
	            <td class="text-left">ROASTED CUT UPS</td>
	            <td class="text-right">' . number_format($roasted_cutups) . '</td>
	            <td class="text-right">' . number_format($roasted_cutups1) . '</td>
	            <td class="text-right">' . number_format($roasted_cutups2) . '</td>
	            <td class="text-right">' . number_format($roasted_cutups_dif1) . '</td>
	            <td class="text-right">' . number_format($roasted_cutups_per1) . '%</td>
	            <td class="text-right">' . number_format($roasted_cutups_dif2) . '</td>
	            <td class="text-right">' . number_format($roasted_cutups_per2) . '%</td>

	            <td class="text-right">' . number_format($roasted_cutups) . '</td>
	            <td class="text-right">' . number_format($roasted_cutups1) . '</td>
	            <td class="text-right">' . number_format($roasted_cutups2) . '</td>
	            <td class="text-right">' . number_format($roasted_cutups_dif1) . '</td>
	            <td class="text-right">' . number_format($roasted_cutups_per1) . '%</td>
	            <td class="text-right">' . number_format($roasted_cutups_dif2) . '</td>
	            <td class="text-right">' . number_format($roasted_cutups_per2) . '%</td>
	        </tr>

	        <tr>
	            <td class="text-left">ROASTED CHICKEN</td>
	            <td class="text-right">' . number_format($roasted_chicken) . '</td>
	            <td class="text-right">' . number_format($roasted_chicken1) . '</td>
	            <td class="text-right">' . number_format($roasted_chicken2) . '</td>
	            <td class="text-right">' . number_format($roasted_chicken_dif1) . '</td>
	            <td class="text-right">' . number_format($roasted_chicken_per1) . '%</td>
	            <td class="text-right">' . number_format($roasted_chicken_dif2) . '</td>
	            <td class="text-right">' . number_format($roasted_chicken_per2) . '%</td>

	            <td class="text-right">' . number_format($roasted_chicken) . '</td>
	            <td class="text-right">' . number_format($roasted_chicken1) . '</td>
	            <td class="text-right">' . number_format($roasted_chicken2) . '</td>
	            <td class="text-right">' . number_format($roasted_chicken_dif1) . '</td>
	            <td class="text-right">' . number_format($roasted_chicken_per1) . '%</td>
	            <td class="text-right">' . number_format($roasted_chicken_dif2) . '</td>
	            <td class="text-right">' . number_format($roasted_chicken_per2) . '%</td>
	        </tr>

	        <tr>
	            <td class="text-left">MARINATED CHICKEN (RAW)</td>
	            <td class="text-right">' . number_format($marinated_chicken_raw) . '</td>
	            <td class="text-right">' . number_format($marinated_chicken_raw1) . '</td>
	            <td class="text-right">' . number_format($marinated_chicken_raw2) . '</td>
	            <td class="text-right">' . number_format($marinated_chicken_raw_dif1) . '</td>
	            <td class="text-right">' . number_format($marinated_chicken_raw_per1) . '%</td>
	            <td class="text-right">' . number_format($marinated_chicken_raw_dif2) . '</td>
	            <td class="text-right">' . number_format($marinated_chicken_raw_per2) . '%</td>

	            <td class="text-right">' . number_format($marinated_chicken_raw) . '</td>
	            <td class="text-right">' . number_format($marinated_chicken_raw1) . '</td>
	            <td class="text-right">' . number_format($marinated_chicken_raw2) . '</td>
	            <td class="text-right">' . number_format($marinated_chicken_raw_dif1) . '</td>
	            <td class="text-right">' . number_format($marinated_chicken_raw_per1) . '%</td>
	            <td class="text-right">' . number_format($marinated_chicken_raw_dif2) . '</td>
	            <td class="text-right">' . number_format($marinated_chicken_raw_per2) . '%</td>
	        </tr>

	        <tr>
	            <td class="text-left">OTHER SPECIALTY PRODUCTS</td>
	            <td class="text-right">' . number_format($others) . '</td>
	            <td class="text-right">' . number_format($others1) . '</td>
	            <td class="text-right">' . number_format($others2) . '</td>
	            <td class="text-right">' . number_format($others_dif1) . '</td>
	            <td class="text-right">' . number_format($others_per1) . '%</td>
	            <td class="text-right">' . number_format($others_dif2) . '</td>
	            <td class="text-right">' . number_format($others_per2) . '%</td>

	            <td class="text-right">' . number_format($others) . '</td>
	            <td class="text-right">' . number_format($others1) . '</td>
	            <td class="text-right">' . number_format($others2) . '</td>
	            <td class="text-right">' . number_format($others_dif1) . '</td>
	            <td class="text-right">' . number_format($others_per1) . '%</td>
	            <td class="text-right">' . number_format($others_dif2) . '</td>
	            <td class="text-right">' . number_format($others_per2) . '%</td>
	        </tr>

	        <tr>
	            <td></td>
	            <td></td>
	            <td></td>
	            <td></td>
	            <td></td>
	            <td></td>
	            <td></td>
	            <td></td>
	            <td></td>
	            <td></td>
	            <td></td>
	            <td></td>
	            <td></td>
	            <td></td>
	            <td></td>
	        </tr>

	        <tr style="border-top: 2px solid black; border-bottom: 2px solid black">
	            <td class="text-left"><strong>Sub Total</strong></td>
	            <td class="text-right">' . number_format($sub_total) . '</td>
	            <td></td>
	            <td></td>
	            <td></td>
	            <td></td>
	            <td></td>
	            <td></td>
	            <td></td>
	            <td></td>
	            <td></td>
	            <td></td>
	            <td></td>
	            <td></td>
	            <td></td>
	        </tr>

	        <tr>
	            <td class="text-left">LIEMPO</td>
	            <td class="text-right">' . number_format($liempo) . '</td>
	            <td class="text-right">' . number_format($liempo1) . '</td>
	            <td class="text-right">' . number_format($liempo2) . '</td>
	            <td class="text-right">' . number_format($liempo_dif1) . '</td>
	            <td class="text-right">' . number_format($liempo_per1) . '%</td>
	            <td class="text-right">' . number_format($liempo_dif2) . '</td>
	            <td class="text-right">' . number_format($liempo_per2) . '%</td>

	            <td class="text-right">' . number_format($liempo) . '</td>
	            <td class="text-right">' . number_format($liempo1) . '</td>
	            <td class="text-right">' . number_format($liempo2) . '</td>
	            <td class="text-right">' . number_format($liempo_dif1) . '</td>
	            <td class="text-right">' . number_format($liempo_per1) . '%</td>
	            <td class="text-right">' . number_format($liempo_dif2) . '</td>
	            <td class="text-right">' . number_format($liempo_per2) . '%</td>
	        </tr>

	        <tr>
	            <td class="text-left">VAP</td>
	            <td class="text-right">' . number_format($vap) . '</td>
	            <td class="text-right">' . number_format($vap1) . '</td>
	            <td class="text-right">' . number_format($vap2) . '</td>
	            <td class="text-right">' . number_format($vap_dif1) . '</td>
	            <td class="text-right">' . number_format($vap_per1) . '%</td>
	            <td class="text-right">' . number_format($vap_dif2) . '</td>
	            <td class="text-right">' . number_format($vap_per2) . '%</td>

	            <td class="text-right">' . number_format($vap) . '</td>
	            <td class="text-right">' . number_format($vap1) . '</td>
	            <td class="text-right">' . number_format($vap2) . '</td>
	            <td class="text-right">' . number_format($vap_dif1) . '</td>
	            <td class="text-right">' . number_format($vap_per1) . '%</td>
	            <td class="text-right">' . number_format($vap_dif2) . '</td>
	            <td class="text-right">' . number_format($vap_per2) . '%</td>
	        </tr>

	        <tr>
	            <td class="text-left">DP/DOA/DAA</td>
	            <td class="text-right"></td>
	            <td></td>
	            <td></td>
	            <td></td>
	            <td></td>
	            <td></td>
	            <td></td>
	            <td></td>
	            <td></td>
	            <td></td>
	            <td></td>
	            <td></td>
	            <td></td>
	            <td></td>
	        </tr>

	        <tr>
	            <td class="text-left">Transfer (In) / Out</td>
	            <td class="text-right">' . number_format($transfer) . '</td>
	            <td></td>
	            <td></td>
	            <td></td>
	            <td></td>
	            <td></td>
	            <td></td>
	            <td class="text-right">' . number_format($transfer) . '</td>
	            <td></td>
	            <td></td>
	            <td></td>
	            <td></td>
	            <td></td>
	            <td></td>
	        </tr>

	        <tr style="border-top: 2px solid black; border-bottom: 2px solid black">
	            <td class="text-left"><strong>Total</strong></td>
	            <td class="text-right">' . number_format($total) . '</td>
	            <td class="text-right">' . number_format($total1) . '</td>
	            <td class="text-right">' . number_format($total2) . '</td>
	            <td class="text-right">' . number_format($variance1) . '</td>
	            <td class="text-right">' . number_format($percent1) . '</td>
	            <td class="text-right">' . number_format($variance2) . '</td>
	            <td class="text-right">' . number_format($percent1) . '</td>

	            <td class="text-right">' . number_format($total_heads) . '</td>
	            <td class="text-right">' . number_format($total1) . '</td>
	            <td class="text-right">' . number_format($total2) . '</td>
	            <td class="text-right">' . number_format($variance1) . '</td>
	            <td class="text-right">' . number_format($percent1) . '%</td>
	            <td class="text-right">' . number_format($variance2) . '</td>
	            <td class="text-right">' . number_format($percent1) . '%</td>
	        </tr>
	    ';

	    return $volume_tbl;
	}

	public function _get_broiler_dashboard_tbl($year, $bc_data){ //SAMPLE BICOL BC

		$trans_year = encode($year);
		$doctype = encode('trans');
		$join = array('bc_tbl b' => 'a.bc_id = b.bc_id');
 		$harvested_actual_data = $this->admin->get_join('broiler_amount_summary_tbl a', $join, false, false, 'a.trans_date', 'a.broiler_line_item_id, SUM(trans_qty)', 'trans_type_id = 2 AND YEAR(a.trans_date) = ' . ($year - 1) .	' AND a.bc_id IN (' . $bc_data . ') AND a.trans_status != 5');

 		$i = 1;
 		$kilo = 1;
 		$feeds = 1;
 		$cg_fee = 1;
 		$medicines = 1;
 		$vaccines = 1;
 		$doc = 1;
 		if(!empty($harvested_actual_data)){
	 		foreach ($harvested_actual_data as $r) {
	 			if($r->broiler_line_item_id == 7){
		 			$actual_harvested_heads[$i] = $r->trans_qty;
		 			$i++;
	 			}
	 			if($r->broiler_line_item_id == 6){
	 				$actual_harvested_kilo[$kilo] = $r->trans_qty;
		 			$kilo++;
	 			}
	 			if($r->broiler_line_item_id == 5){
	 				$actual_feeds_amount[$feeds] = $r->trans_qty;
		 			$feeds++;
	 			}
	 			if($r->broiler_line_item_id == 4){
	 				$actual_cg_fee_amount[$cg_fee] = $r->trans_qty;
		 			$cg_fee++;
	 			}
	 			if($r->broiler_line_item_id == 3){
	 				$actual_medicines_amount[$medicines] = $r->trans_qty;
		 			$medicines++;
	 			}
	 			if($r->broiler_line_item_id == 2){
	 				$actual_vaccines_amount[$vaccines] = $r->trans_qty;
		 			$vaccines++;
	 			}
	 			if($r->broiler_line_item_id == 1){
	 				$actual_doc_amount[$doc] = $r->trans_qty;
		 			$doc++;
	 			}
	 		}

	 		$actual_harvested_heads = $actual_harvested_heads;
	 		$actual_harvested_kilo = $actual_harvested_kilo;
	 		$actual_feeds_amount = $actual_feeds_amount;
	 		$actual_cg_fee_amount = $actual_cg_fee_amount;
	 		$actual_medicines_amount = $actual_medicines_amount;
	 		$actual_vaccines_amount = $actual_vaccines_amount;
	 		$actual_doc_amount = $actual_doc_amount;
 		} else {
 			
 			$actual_harvested_heads = 0;
 			$actual_harvested_kilo = 0;
	 		$actual_feeds_amount = 0;
	 		$actual_cg_fee_amount = 0;
	 		$actual_medicines_amount = 0;
	 		$actual_vaccines_amount = 0;
	 		$actual_doc_amount = 0;
 		}

 		$join = array('bc_tbl b' => 'a.bc_id = b.bc_id');
 		$previous_harvested_actual_data = $this->admin->get_join('broiler_amount_summary_tbl a', $join, false, false, 'a.trans_date', 'a.broiler_line_item_id, SUM(trans_qty)', 'trans_type_id = 2 AND YEAR(a.trans_date) = ' . ($year - 2) . ' AND a.bc_id IN (' . $bc_data . ') AND a.trans_status != 5');

 		$i = 1;
 		$kilo = 1;
 		$feeds = 1;
 		$cg_fee = 1;
 		$medicines = 1;
 		$vaccines = 1;
 		$doc = 1;
 		if(!empty($previous_harvested_actual_data)){
 			foreach ($previous_harvested_actual_data as $r) {
	 			
	 			if($r->broiler_line_item_id == 7){
		 			$previous_actual_harvested_heads[$i] = $r->trans_qty;
		 			$i++;
	 			}
	 			if($r->broiler_line_item_id == 6){
	 				$previous_actual_harvested_kilo[$kilo] = $r->trans_qty;
		 			$kilo++;
	 			}
	 			if($r->broiler_line_item_id == 5){
	 				$previous_actual_feeds_amount[$feeds] = $r->trans_qty;
		 			$feeds++;
	 			}
	 			if($r->broiler_line_item_id == 4){
	 				$previous_actual_cg_fee_amount[$cg_fee] = $r->trans_qty;
		 			$cg_fee++;
	 			}
	 			if($r->broiler_line_item_id == 3){
	 				$previous_actual_medicines_amount[$medicines] = $r->trans_qty;
		 			$medicines++;
	 			}
	 			if($r->broiler_line_item_id == 2){
	 				$previous_actual_vaccines_amount[$vaccines] = $r->trans_qty;
		 			$vaccines++;
	 			}
	 			if($r->broiler_line_item_id == 1){
	 				$previous_actual_doc_amount[$doc] = $r->trans_qty;
		 			$doc++;
	 			}
	 		}
	 		$previous_actual_harvested_heads = $previous_actual_harvested_heads;
	 		$previous_actual_harvested_kilo = $previous_actual_harvested_kilo;
	 		$previous_actual_feeds_amount = $previous_actual_feeds_amount;
	 		$previous_actual_cg_fee_amount = $previous_actual_cg_fee_amount;
	 		$previous_actual_medicines_amount = $previous_actual_medicines_amount;
	 		$previous_actual_vaccines_amount = $previous_actual_vaccines_amount;
	 		$previous_actual_doc_amount = $previous_actual_doc_amount;
 		} else {
 			$previous_actual_harvested_heads = 0;
 			$previous_actual_harvested_kilo = 0;
	 		$previous_actual_feeds_amount = 0;
	 		$previous_actual_cg_fee_amount = 0;
	 		$previous_actual_medicines_amount = 0;
	 		$previous_actual_vaccines_amount = 0;
	 		$previous_actual_doc_amount = 0;
	 	}

	 	$join = array(
			'broiler_summary_item_tbl b' => 'a.broiler_summary_item_id = b.broiler_summary_item_id and a.bc_id IN (' . $bc_data . ') and YEAR(a.trans_date) = '.$year.' and a.trans_type_id = 1 and a.broiler_summary_status = 1'
		);
		$broiler_summary = $this->admin->get_join('broiler_summary_tbl a', $join, false, 'b.order_count, MONTH(a.trans_date)', 'trans_date', 'SUM(a.trans_qty)', false, false);

		$i = 1;
		if(!empty($broiler_summary)){
			foreach ($broiler_summary as $broiler_summary_row) {
				if($broiler_summary_row->broiler_summary_item_id == 33){
					$budgeted_harvested_heads[$i] = $broiler_summary_row->trans_qty;
		 			$i++;

		 			if($i == 13) $i = 1;
				} else if($broiler_summary_row->broiler_summary_item_id == 34){
					$budgeted_harvested_kilo[$i] = $broiler_summary_row->trans_qty;
		 			$i++;
		 			if($i == 13) $i = 1;
				} else if($broiler_summary_row->broiler_summary_item_id == 35){
					$budgeted_doc_cost_amount[$i] = $broiler_summary_row->trans_qty;
		 			$i++;
		 			if($i == 13) $i = 1;
				} else if($broiler_summary_row->broiler_summary_item_id == 1){
					$budgeted_growers_fee_amount[$i] = $broiler_summary_row->trans_qty;
		 			$i++;
		 			if($i == 13) $i = 1;
				} else if($broiler_summary_row->broiler_summary_item_id == 37){
					$budgeted_feed_cost_amount[$i] = $broiler_summary_row->trans_qty;
		 			$i++;
		 			if($i == 13) $i = 1;
				} else if($broiler_summary_row->broiler_summary_item_id == 45){
					$budgeted_vaccines_amount[$i] = $broiler_summary_row->trans_qty;
		 			$i++;
		 			if($i == 13) $i = 1;
				} else if($broiler_summary_row->broiler_summary_item_id == 42){
					$budgeted_medicine_amount[$i] = $broiler_summary_row->trans_qty;
		 			$i++;
		 			if($i == 13) $i = 1;
				} else if($broiler_summary_row->broiler_summary_item_id == 43){
					$budgeted_disinfectant_amount[$i] = $broiler_summary_row->trans_qty;
		 			$i++;
		 			if($i == 13) $i = 1;
				}
			}
			$budgeted_harvested_heads = @$budgeted_harvested_heads;
			$budgeted_harvested_kilo = @$budgeted_harvested_kilo;
			$budgeted_doc_cost_amount = @$budgeted_doc_cost_amount;
			$budgeted_growers_fee_amount = @$budgeted_growers_fee_amount;
			$budgeted_feed_cost_amount = @$budgeted_feed_cost_amount;
			$budgeted_vaccines_amount = @$budgeted_vaccines_amount;
			$budgeted_medicine_amount = @$budgeted_medicine_amount;
			$budgeted_disinfectant_amount = @$budgeted_disinfectant_amount;

		} else {
			$budgeted_harvested_heads = 0;
			$budgeted_harvested_kilo = 0;
			$budgeted_doc_cost_amount = 0;
			$budgeted_growers_fee_amount = 0;
			$budgeted_feed_cost_amount = 0;
			$budgeted_vaccines_amount = 0;
			$budgeted_medicine_amount = 0;
			$budgeted_disinfectant_amount = 0;
		}


		$total_actual_harvested_heads = 0;
		$total_actual_doc = 0;
		$total_actual_feeds = 0;
		$total_actual_cg = 0;
		$total_actual_medicines = 0;
		$total_actual_vaccines = 0;
		$total_actual_harvested_kilo = 0;

		$total_previous_actual_harvested_heads = 0;
		$total_previous_actual_doc = 0;
		$total_previous_actual_feeds = 0;
		$total_previous_actual_cg = 0;
		$total_previous_actual_medicines = 0;
		$total_previous_actual_vaccines = 0;
		$total_previous_actual_harvested_kilo = 0;

		$harvested_heads = 0;
		$harvested_kilo = 0;
		$growers_fee_amount = 0;
		$feed_cost_amount = 0;
		$vaccines_amount = 0;
		$medicine_amount = 0;
		$disinfectant_amount = 0;
		$doc_cost_amount = 0;

		$table = '';
		for ($i=1; $i <=12 ; $i++) {
			//BUDGET BROILER
			$harvested_heads = $harvested_heads + $budgeted_harvested_heads[$i];
			$harvested_kilo = $harvested_kilo + $budgeted_harvested_kilo[$i];
			$doc_cost_amount = $doc_cost_amount + $budgeted_doc_cost_amount[$i];
			$growers_fee_amount = $growers_fee_amount + $budgeted_growers_fee_amount[$i];
			$feed_cost_amount = $feed_cost_amount + $budgeted_feed_cost_amount[$i];
			$vaccines_amount = $vaccines_amount + $budgeted_vaccines_amount[$i];
			$medicine_amount = $medicine_amount + $budgeted_medicine_amount[$i];
			$disinfectant_amount = $disinfectant_amount + $budgeted_disinfectant_amount[$i];

			$doc_ave = $harvested_kilo == 0 ? 0 : $doc_cost_amount/$harvested_kilo;
			$growers_fee_ave = $harvested_kilo == 0 ? 0 : $growers_fee_amount/$harvested_kilo;
			$feed_cost_ave = $harvested_kilo == 0 ? 0 : $feed_cost_amount/$harvested_kilo;
			$vaccines_ave = $harvested_kilo == 0 ? 0 : $vaccines_amount/$harvested_kilo;
			$total = $disinfectant_amount + $medicine_amount;
			$medicine_ave = $harvested_kilo == 0 ? 0 : $total/$harvested_kilo;
			$month = date('M', strtotime($year.'-'.$i.'-01'));

			$total_actual_harvested_heads = $total_actual_harvested_heads + $actual_harvested_heads[$i];

			$total_previous_actual_harvested_heads = $total_previous_actual_harvested_heads + $previous_actual_harvested_heads[$i];


			$actual_doc[$i] = $actual_harvested_kilo[$i] <= 0 ? 0 : $actual_doc_amount[$i]/$actual_harvested_kilo[$i];
			$actual_feeds[$i] = $actual_harvested_kilo[$i] <= 0 ? 0 : $actual_feeds_amount[$i]/$actual_harvested_kilo[$i];
			$actual_cg[$i] = $actual_harvested_kilo[$i] <= 0 ? 0 : $actual_cg_fee_amount[$i]/$actual_harvested_kilo[$i];
			$actual_medicines[$i] = $actual_harvested_kilo[$i] <= 0 ? 0 : $actual_medicines_amount[$i]/$actual_harvested_kilo[$i];
			$actual_vaccines[$i] = $actual_harvested_kilo[$i] <= 0 ? 0 : $actual_vaccines_amount[$i]/$actual_harvested_kilo[$i];
			$actual_broiler_cost[$i] = $actual_doc[$i] + $actual_feeds[$i] + $actual_cg[$i] + $actual_medicines[$i] + $actual_vaccines[$i];

			$total_actual_doc = $total_actual_doc + $actual_doc_amount[$i];
			$total_actual_feeds = $total_actual_feeds + $actual_feeds_amount[$i];
			$total_actual_cg = $total_actual_cg + $actual_cg_fee_amount[$i];
			$total_actual_medicines = $total_actual_medicines + $actual_medicines_amount[$i];
			$total_actual_vaccines = $total_actual_vaccines + $actual_vaccines_amount[$i];
			$total_actual_harvested_kilo = $total_actual_harvested_kilo + $actual_harvested_kilo[$i];

			$previous_actual_doc[$i] = $previous_actual_harvested_kilo[$i] <= 0 ? 0 : $previous_actual_doc_amount[$i]/$previous_actual_harvested_kilo[$i];
			$previous_actual_feeds[$i] = $previous_actual_harvested_kilo[$i] <= 0 ? 0 : $previous_actual_feeds_amount[$i]/$previous_actual_harvested_kilo[$i];
			$previous_actual_cg[$i] = $previous_actual_harvested_kilo[$i] <= 0 ? 0 : $previous_actual_cg_fee_amount[$i]/$previous_actual_harvested_kilo[$i];
			$previous_actual_medicines[$i] = $previous_actual_harvested_kilo[$i] <= 0 ? 0 : $previous_actual_medicines_amount[$i]/$previous_actual_harvested_kilo[$i];
			$previous_actual_vaccines[$i] = $previous_actual_harvested_kilo[$i] <= 0 ? 0 : $previous_actual_vaccines_amount[$i]/$previous_actual_harvested_kilo[$i];
			$previous_actual_broiler_cost[$i] = $previous_actual_doc[$i] + $previous_actual_feeds[$i] + $previous_actual_cg[$i] + $previous_actual_medicines[$i] + $previous_actual_vaccines[$i];

			$total_previous_actual_doc = $total_previous_actual_doc + $previous_actual_doc_amount[$i];
			$total_previous_actual_feeds = $total_previous_actual_feeds + $previous_actual_feeds_amount[$i];
			$total_previous_actual_cg = $total_previous_actual_cg + $previous_actual_cg_fee_amount[$i];
			$total_previous_actual_medicines = $total_previous_actual_medicines + $previous_actual_medicines_amount[$i];
			$total_previous_actual_vaccines = $total_previous_actual_vaccines + $previous_actual_vaccines_amount[$i];
			$total_previous_actual_harvested_kilo = $total_previous_actual_harvested_kilo + $previous_actual_harvested_kilo[$i];

			$table .= '<tr>
				<td>'.$month.'</td>
				<td align="right">'.number_format($budgeted_harvested_heads[$i],0,'.',',').'</td>
				<td align="right">'.number_format($actual_harvested_heads[$i],0,'.',',').'</td>
				<td align="right">'.number_format($previous_actual_harvested_heads[$i],0,'.',',').'</td>

				<td align="right">'.number_format(get_broiler_cost(encode($bc_data), $trans_year, $i, $doctype),dec_places_dis(),'.',',').'</td>
				<td align="right">'.number_format($actual_broiler_cost[$i],dec_places_dis(),'.',',').'</td>
				<td align="right">'.number_format($previous_actual_broiler_cost[$i],dec_places_dis(),'.',',').'</td>
			</tr>';
		}

		$broiler_cost_ave = $medicine_ave + $doc_ave + $growers_fee_ave + $feed_cost_ave + $vaccines_ave;
		$total_actual_broiler_cost = $total_actual_doc + $total_actual_feeds + $total_actual_cg + $total_actual_vaccines + $total_actual_medicines;
		$total_actual_broiler_cost = $total_actual_harvested_kilo <= 0 ? 0 : $total_actual_broiler_cost/$total_actual_harvested_kilo;

		$total_previous_actual_broiler_cost = $total_previous_actual_doc + $total_previous_actual_feeds + $total_previous_actual_cg + $total_previous_actual_vaccines + $total_previous_actual_medicines;
		$total_previous_actual_broiler_cost = $total_previous_actual_harvested_kilo <= 0 ? 0 : $total_previous_actual_broiler_cost/$total_previous_actual_harvested_kilo;
		$table .= '<tr>
				<td align="right">TOTAL</td>
				<td align="right">'.number_format($harvested_heads,0,'.',',').'</td>
				<td align="right">'.number_format($total_actual_harvested_heads,0,'.',',').'</td>
				<td align="right">'.number_format($total_previous_actual_harvested_heads,0,'.',',').'</td>
				<td align="right">'.number_format($broiler_cost_ave,dec_places_dis(),'.',',').'</td>
				<td align="right">'.number_format($total_actual_broiler_cost,dec_places_dis(),'.',',').'</td>
				<td align="right">'.number_format($total_previous_actual_broiler_cost,dec_places_dis(),'.',',').'</td>
			</tr>';
		
		$harvested_dif1 = $harvested_heads - $total_actual_harvested_heads;
		$harvested_dif2 = $harvested_heads - $total_previous_actual_harvested_heads;
		$harvested_percent1 = $total_actual_harvested_heads > 0 ? ($harvested_dif1 / $total_actual_harvested_heads) * 100 : 0;
		$harvested_percent2 = $total_previous_actual_harvested_heads > 0 ? ($harvested_dif2 / $total_previous_actual_harvested_heads) * 100 : 0;

 		$harvested_condition1 = '';
 		$harvested_indicator1 = '';
 		if($harvested_heads > $total_actual_harvested_heads){
 			$harvested_condition1 = 'higher';
 			$harvested_indicator1 = 'fa fa-long-arrow-up';
 		}elseif($harvested_heads < $total_actual_harvested_heads){
 			$harvested_condition1 = 'lower';
 			$harvested_indicator1 = 'fa fa-long-arrow-down';
 		}

 		$harvested_condition2 = '';
 		$harvested_indicator2 = '';
 		if($harvested_heads > $total_previous_actual_harvested_heads){
 			$harvested_condition2 = 'higher';
 			$harvested_indicator2 = 'fa fa-long-arrow-up';
 		}elseif($harvested_heads < $total_previous_actual_harvested_heads){
 			$harvested_condition2 = 'lower';
 			$harvested_indicator2 = 'fa fa-long-arrow-down';
 		}

		$broiler_cost_dif1 = round($broiler_cost_ave) - round($total_actual_broiler_cost);
		$broiler_cost_dif2 = round($broiler_cost_ave) - round($total_previous_actual_broiler_cost);
		$broilder_cost_percent1 = $broiler_cost_ave > 0 ? ($broiler_cost_dif1 / $broiler_cost_ave) * 100 : 0;
		$broilder_cost_percent2 = $broiler_cost_ave > 0 ? ($broiler_cost_dif1 / $broiler_cost_ave) * 100 : 0;

		$broiler_cost_condition1 = '';
 		$broiler_cost_indicator1 = '';
 		if($broiler_cost_ave > $total_actual_broiler_cost){
 			$broiler_cost_condition1 = 'higher';
 			$broiler_cost_indicator1 = 'fa fa-long-arrow-up';
 		}elseif($broiler_cost_ave < $total_actual_broiler_cost){
 			$broiler_cost_condition1 = 'lower';
 			$broiler_cost_indicator1 = 'fa fa-long-arrow-down';
 		}

 		$broiler_cost_condition2 = '';
 		$broiler_cost_indicator2 = '';
 		if($broiler_cost_ave > $total_previous_actual_broiler_cost){
 			$broiler_cost_condition2 = 'higher';
 			$broiler_cost_indicator2 = 'fa fa-long-arrow-up';
 		}elseif($broiler_cost_ave < $total_previous_actual_broiler_cost){
 			$broiler_cost_condition2 = 'lower';
 			$broiler_cost_indicator2 = 'fa fa-long-arrow-down';
 		}

		$data['harvested_heads'] = $harvested_heads;
		$data['harvested_heads1'] = $total_actual_harvested_heads;
		$data['harvested_heads2'] = $total_previous_actual_harvested_heads;
		$data['harvested_dif1'] = $harvested_dif1;
		$data['harvested_dif2'] = $harvested_dif2;
		$data['harvested_percent1'] = $harvested_percent1;
		$data['harvested_percent2'] = $harvested_percent2;
		$data['harvested_condition1'] = $harvested_condition1;
		$data['harvested_condition2'] = $harvested_condition2;
		$data['harvested_indicator1'] = $harvested_indicator1;
		$data['harvested_indicator2'] = $harvested_indicator2;


		$data['broiler_cost'] = $broiler_cost_ave;
		$data['broiler_cost1'] = $total_actual_broiler_cost;
		$data['broiler_cost2'] = $total_previous_actual_broiler_cost;
		$data['broiler_cost_dif1'] = $broiler_cost_dif1;
		$data['broiler_cost_dif2'] = $broiler_cost_dif2;
		$data['broilder_cost_percent1'] = $broilder_cost_percent1;
		$data['broilder_cost_percent2'] = $broilder_cost_percent2;
		$data['broiler_cost_condition1'] = $broiler_cost_condition1;
		$data['broiler_cost_condition2'] = $broiler_cost_condition2;
		$data['broiler_cost_indicator1'] = $broiler_cost_indicator1;
		$data['broiler_cost_indicator2'] = $broiler_cost_indicator2;

		$data['tbl'] = $table;
		return $data;
	}

	public function sales_unit($bc_id, $year){
		$join = array(
			'sales_item_tbl b' => 'a.sales_id = b.sales_id AND a.sales_status = 1 AND b. sales_item_status',
			'sales_details_tbl c' => 'b.sales_item_id = c.sales_item_id AND c.sales_det_status = 1',
			'material_tbl d' => 'b.material_id = d.material_id',
			'material_unit_tbl e' => 'd.material_id = e.material_id AND e.material_unit_status = 1',
			'outlet_tbl f' => 'a.outlet_id = f.outlet_id',
			'bc_tbl g' => 'f.bc_id = g.bc_id AND g.bc_id IN (' . $bc_id . ') AND a.sales_year = ' . $year 
		);

		$sales_unit = $this->admin->get_join('sales_tbl a', $join, FALSE, FALSE, 'd.material_id', 'd.material_code, d.material_desc, SUM(c.sales_det_qty), e.sales_unit_equivalent as sales_unit, SUM(c.sales_det_qty) / e.sales_unit_equivalent as total_sales_unit');
		return $sales_unit;
	}

	public function get_comparative_net_sales($bc_id, $year){
		$info = $this->_require_login();
		$user_info = $this->get_user_info();

		$join1 = array(
			'bc_tbl b' => 'a.bc_id = b.bc_id AND b.bc_id IN (' . $bc_id . ') AND YEAR(a.trans_year) = ' . ($year - 1),
		);
		$comparative1 = $this->admin->check_join('comparative_net_sales_tbl a', $join1, TRUE,'b.bc_name ASC', false, 'b.bc_name, a.*, SUM(net_sales) as net_sales, SUM(a.sales_unit) as sales_unit');
		$net_sales1 = 0;
		$sales_unit1 = 0;
		if($comparative1['result'] == TRUE){
			$net_sales1 = $comparative1['info']->net_sales;
			$sales_unit1 = $comparative1['info']->sales_unit;
		}

		$join2 = array(
			'bc_tbl b' => 'a.bc_id = b.bc_id AND b.bc_id IN (' . $bc_id . ') AND YEAR(a.trans_year) = ' . ($year - 2),
		);
		$comparative2 = $this->admin->check_join('comparative_net_sales_tbl a', $join2, FALSE,'b.bc_name ASC', false, 'b.bc_name, a.*, SUM(net_sales) as net_sales, SUM(a.sales_unit) as sales_unit');

		$net_sales2 = 0;
		$sales_unit2 = 0;
		if($comparative2['result'] == TRUE){
			$net_sales2 = $comparative2['info']->net_sales;
			$sales_unit2 = $comparative2['info']->sales_unit;
		}

		$data['net_sales1'] = $net_sales1;
		$data['net_sales2'] = $net_sales2;
		$data['sales_unit1'] = $sales_unit1;
		$data['sales_unit2'] = $sales_unit2;
		return $data;
	}

	public function capex_report($cost_center_id, $year, $bc_id){
		$info = $this->_require_login();

		$join = array(
			'asset_group_transaction_item_tbl b' => 'a.ag_trans_id = b.ag_trans_id AND a.ag_trans_status = 1 AND b.ag_trans_item_status = 1 AND a.cost_center_id IN (' . $cost_center_id . ') AND a.ag_trans_budget_year = ' . $year,
			'asset_group_transaction_details_tbl c' => 'b.ag_trans_item_id = c.ag_trans_item_id AND c.ag_trans_det_status = 1',
			'asset_subgroup_tbl d' => 'b.asg_id = d.asg_id',
			'asset_group_tbl e' => 'd.ag_id = e.ag_id',
		);

		$capex = $this->admin->get_join('asset_group_transaction_tbl a', $join, TRUE, FALSE, FALSE, '

			SUM(b.capex_price * c.capex_qty) as total_capex,

			(SELECT SUM(x.comp_capex_val) FROM comparative_capex_tbl x WHERE e.ag_id = x.ag_id AND x.bc_id IN (' . $bc_id .') AND x.comp_capex_status = 1 AND x.comp_capex_year = ' . ($year - 1) . ') as total_capex1, 

			(SELECT SUM(x.comp_capex_val) FROM comparative_capex_tbl x WHERE d.ag_id = x.ag_id AND x.bc_id IN (' . $bc_id .') AND x.comp_capex_status = 1 AND x.comp_capex_year = ' . ($year - 2) . ') as total_capex2

			');
		return $capex;
	}

	public function volume_report($bc_id, $year){
		$info = $this->_require_login();

		$join_live = array(
			'sales_item_tbl b' => 'a.sales_id = b.sales_id AND a.sales_status = 1 AND b.sales_item_status = 1 AND a.sales_year = ' . $year,
			'sales_details_tbl c' => 'b.sales_item_id = c.sales_item_id AND c.sales_det_status = 1',
			'material_tbl d' => 'b.material_id = d.material_id',
			'material_group_tbl e' => 'd.material_group_id = e.material_group_id AND e.material_group_name = \'LIVE SALES\'',
			'material_unit_tbl f' => 'd.material_id = f.material_id AND f.material_unit_status = 1',
			'outlet_tbl g' => 'a.outlet_id = g.outlet_id AND g.bc_id IN (' . $bc_id . ')'
		);

		$get_live = $this->admin->get_join('sales_tbl a', $join_live, TRUE, FALSE, FALSE, 'SUM(c.sales_det_qty / f.sales_unit_equivalent) as live_sales_unit, 

			(SELECT SUM(x.sales_unit) FROM comparative_volume_tbl x WHERE g.bc_id = x.bc_id AND x.sales_status = 1 AND x.sales_vol = "LIVE SALES" AND YEAR(x.trans_year) = ' . ($year - 1) . ') as live_sales_unit1,

			(SELECT SUM(x.sales_unit) FROM comparative_volume_tbl x WHERE g.bc_id = x.bc_id AND x.sales_status = 1 AND x.sales_vol = "LIVE SALES" AND YEAR(x.trans_year) = ' . ($year - 2) . ') as live_sales_unit2
		');

		$data['live'] = $get_live->live_sales_unit;
		$data['live1'] = $get_live->live_sales_unit1;
		$data['live2'] = $get_live->live_sales_unit2;

		$join_dressed_others = array(
			'sales_item_tbl b' => 'a.sales_id = b.sales_id AND a.sales_status = 1 AND b.sales_item_status = 1 AND a.sales_year = ' . $year,
			'sales_details_tbl c' => 'b.sales_item_id = c.sales_item_id AND c.sales_det_status = 1',
			'material_tbl d' => 'b.material_id = d.material_id',
			'material_group_tbl e' => 'd.material_group_id = e.material_group_id AND e.material_group_name = \'DRESSED\'',
			'material_unit_tbl f' => 'd.material_id = f.material_id AND f.material_unit_status = 1',
			'outlet_tbl g' => 'a.outlet_id = g.outlet_id AND g.bc_id IN (' . $bc_id . ')',
			'outlet_brand_tbl h' => 'g.outlet_id = h.outlet_id AND h.outlet_brand_status = 1',
			'brand_tbl i' => 'h.brand_id = i.brand_id AND i.brand_name = \'TRADE DISTRIBUTOR\''
		);

		$get_dressed_others = $this->admin->get_join('sales_tbl a', $join_dressed_others, TRUE, FALSE, FALSE, 'SUM(c.sales_det_qty / f.sales_unit_equivalent) as dressed_others_sales_unit,

			(SELECT SUM(x.sales_unit) FROM comparative_volume_tbl x WHERE g.bc_id = x.bc_id AND x.sales_status = 1 AND x.sales_vol = "DRESSED - OTHERS" AND YEAR(x.trans_year) = ' . ($year - 1) . ') as dressed_others_sales_unit1,

			(SELECT SUM(x.sales_unit) FROM comparative_volume_tbl x WHERE g.bc_id = x.bc_id AND x.sales_status = 1 AND x.sales_vol = "DRESSED - OTHERS" AND YEAR(x.trans_year) = ' . ($year - 2) . ') as dressed_others_sales_unit2

		');
		$data['dressed_others'] = $get_dressed_others->dressed_others_sales_unit;
		$data['dressed_others1'] = $get_dressed_others->dressed_others_sales_unit1;
		$data['dressed_others2'] = $get_dressed_others->dressed_others_sales_unit2;

		$join_dressed_sup = array(
			'sales_item_tbl b' => 'a.sales_id = b.sales_id AND a.sales_status = 1 AND b.sales_item_status = 1 AND a.sales_year = ' . $year,
			'sales_details_tbl c' => 'b.sales_item_id = c.sales_item_id AND c.sales_det_status = 1',
			'material_tbl d' => 'b.material_id = d.material_id',
			'material_group_tbl e' => 'd.material_group_id = e.material_group_id AND e.material_group_name = \'DRESSED\'',
			'material_unit_tbl f' => 'd.material_id = f.material_id AND f.material_unit_status = 1',
			'outlet_tbl g' => 'a.outlet_id = g.outlet_id AND g.bc_id IN (' . $bc_id . ')',
			'outlet_brand_tbl h' => 'g.outlet_id = h.outlet_id AND h.outlet_brand_status = 1',
			'brand_tbl i' => 'h.brand_id = i.brand_id AND i.brand_name = \'SUPERMARKET\''
		);

		$get_dressed_sup = $this->admin->get_join('sales_tbl a', $join_dressed_sup, TRUE, FALSE, FALSE, 'SUM(c.sales_det_qty / f.sales_unit_equivalent) as dressed_sup_sales_unit,

			(SELECT SUM(x.sales_unit) FROM comparative_volume_tbl x WHERE g.bc_id = x.bc_id AND x.sales_status = 1 AND x.sales_vol = "DRESSED - SUPERMARKET" AND YEAR(x.trans_year) = ' . ($year - 1) . ') as dressed_sup_sales_unit1,

			(SELECT SUM(x.sales_unit) FROM comparative_volume_tbl x WHERE g.bc_id = x.bc_id AND x.sales_status = 1 AND x.sales_vol = "DRESSED - SUPERMARKET" AND YEAR(x.trans_year) = ' . ($year - 2) . ') as dressed_sup_sales_unit2
		');
		$data['dressed_sup'] = $get_dressed_sup->dressed_sup_sales_unit;
		$data['dressed_sup1'] = $get_dressed_sup->dressed_sup_sales_unit1;
		$data['dressed_sup2'] = $get_dressed_sup->dressed_sup_sales_unit2;


		$join_dressed_retail = array(
			'sales_item_tbl b' => 'a.sales_id = b.sales_id AND a.sales_status = 1 AND b.sales_item_status = 1 AND a.sales_year = ' . $year,
			'sales_details_tbl c' => 'b.sales_item_id = c.sales_item_id AND c.sales_det_status = 1',
			'material_tbl d' => 'b.material_id = d.material_id',
			'material_group_tbl e' => 'd.material_group_id = e.material_group_id AND e.material_group_name = \'DRESSED\'',
			'material_unit_tbl f' => 'd.material_id = f.material_id AND f.material_unit_status = 1',
			'outlet_tbl g' => 'a.outlet_id = g.outlet_id AND g.bc_id IN (' . $bc_id . ')',
			'outlet_brand_tbl h' => 'g.outlet_id = h.outlet_id AND h.outlet_brand_status = 1',
			'brand_tbl i' => 'h.brand_id = i.brand_id',
			'brand_type_tbl j' => 'i.brand_type_id = j.brand_type_id AND j.brand_type_name = \'RETAIL\''
		);

		$get_dressed_retail = $this->admin->get_join('sales_tbl a', $join_dressed_retail, TRUE, FALSE, FALSE, 'SUM(c.sales_det_qty / f.sales_unit_equivalent) as dressed_retail_sales_unit,

			(SELECT SUM(x.sales_unit) FROM comparative_volume_tbl x WHERE g.bc_id = x.bc_id AND x.sales_status = 1 AND x.sales_vol = "DRESSED - RETAIL" AND YEAR(x.trans_year) = ' . ($year - 1) . ') as dressed_retail_sales_unit1,

			(SELECT SUM(x.sales_unit) FROM comparative_volume_tbl x WHERE g.bc_id = x.bc_id AND x.sales_status = 1 AND x.sales_vol = "DRESSED - RETAIL" AND YEAR(x.trans_year) = ' . ($year - 2) . ') as dressed_retail_sales_unit2
		');
		$data['dressed_retail'] = $get_dressed_retail->dressed_retail_sales_unit;
		$data['dressed_retail1'] = $get_dressed_retail->dressed_retail_sales_unit1;
		$data['dressed_retail2'] = $get_dressed_retail->dressed_retail_sales_unit2;

		$join_dressed_vansales = array(
			'sales_item_tbl b' => 'a.sales_id = b.sales_id AND a.sales_status = 1 AND b.sales_item_status = 1 AND a.sales_year = ' . $year,
			'sales_details_tbl c' => 'b.sales_item_id = c.sales_item_id AND c.sales_det_status = 1',
			'material_tbl d' => 'b.material_id = d.material_id',
			'material_group_tbl e' => 'd.material_group_id = e.material_group_id AND e.material_group_name = \'DRESSED\'',
			'material_unit_tbl f' => 'd.material_id = f.material_id AND f.material_unit_status = 1',
			'outlet_tbl g' => 'a.outlet_id = g.outlet_id AND g.bc_id IN (' . $bc_id . ')',
			'outlet_brand_tbl h' => 'g.outlet_id = h.outlet_id AND h.outlet_brand_status = 1',
			'brand_tbl i' => 'h.brand_id = i.brand_id',
			'brand_type_tbl j' => 'i.brand_type_id = j.brand_type_id AND i.brand_name = \'VAN SALES\''
		);

		$get_dressed_vansales = $this->admin->get_join('sales_tbl a', $join_dressed_vansales, TRUE, FALSE, FALSE, 'SUM(c.sales_det_qty / f.sales_unit_equivalent) as dressed_vansales_unit,

			(SELECT SUM(x.sales_unit) FROM comparative_volume_tbl x WHERE g.bc_id = x.bc_id AND x.sales_status = 1 AND x.sales_vol = "DRESSED - VANSALES" AND YEAR(x.trans_year) = ' . ($year - 1) . ') as dressed_vansales_unit1,

			(SELECT SUM(x.sales_unit) FROM comparative_volume_tbl x WHERE g.bc_id = x.bc_id AND x.sales_status = 1 AND x.sales_vol = "DRESSED - VANSALES" AND YEAR(x.trans_year) = ' . ($year - 2) . ') as dressed_vansales_unit2
		');

		$data['dressed_vansales'] = $get_dressed_vansales->dressed_vansales_unit;
		$data['dressed_vansales1'] = $get_dressed_vansales->dressed_vansales_unit1;
		$data['dressed_vansales2'] = $get_dressed_vansales->dressed_vansales_unit2;

		$join_non_marinated = array(
			'sales_item_tbl b' => 'a.sales_id = b.sales_id AND a.sales_status = 1 AND b.sales_item_status = 1 AND a.sales_year = ' . $year,
			'sales_details_tbl c' => 'b.sales_item_id = c.sales_item_id AND c.sales_det_status = 1',
			'material_tbl d' => 'b.material_id = d.material_id',
			'material_group_tbl e' => 'd.material_group_id = e.material_group_id AND e.material_group_name = \'NON MARINATED CUT UPS\'',
			'material_unit_tbl f' => 'd.material_id = f.material_id AND f.material_unit_status = 1',
			'outlet_tbl g' => 'a.outlet_id = g.outlet_id AND g.bc_id IN (' . $bc_id . ')',
			'outlet_brand_tbl h' => 'g.outlet_id = h.outlet_id AND h.outlet_brand_status = 1',
			'brand_tbl i' => 'h.brand_id = i.brand_id',
			'brand_type_tbl j' => 'i.brand_type_id = j.brand_type_id'
			/*'brand_type_tbl j' => 'i.brand_type_id = j.brand_type_id AND j.brand_type_name = \'RETAIL\''*/
		);

		$get_non_marinated = $this->admin->get_join('sales_tbl a', $join_non_marinated, TRUE, FALSE, FALSE, 'SUM(c.sales_det_qty / f.sales_unit_equivalent) as non_marinated_sales_unit,

			(SELECT SUM(x.sales_unit) FROM comparative_volume_tbl x WHERE g.bc_id = x.bc_id AND x.sales_status = 1 AND x.sales_vol = "NON MARINATED CUT UPS" AND YEAR(x.trans_year) = ' . ($year - 1) . ') as non_marinated_sales_unit1,

			(SELECT SUM(x.sales_unit) FROM comparative_volume_tbl x WHERE g.bc_id = x.bc_id AND x.sales_status = 1 AND x.sales_vol = "NON MARINATED CUT UPS" AND YEAR(x.trans_year) = ' . ($year - 2) . ') as non_marinated_sales_unit2

			');
		$data['non_marinated'] = $get_non_marinated->non_marinated_sales_unit;
		$data['non_marinated1'] = $get_non_marinated->non_marinated_sales_unit1;
		$data['non_marinated2'] = $get_non_marinated->non_marinated_sales_unit2;

		$join_marinated = array(
			'sales_item_tbl b' => 'a.sales_id = b.sales_id AND a.sales_status = 1 AND b.sales_item_status = 1 AND a.sales_year = ' . $year,
			'sales_details_tbl c' => 'b.sales_item_id = c.sales_item_id AND c.sales_det_status = 1',
			'material_tbl d' => 'b.material_id = d.material_id AND d.material_code NOT IN ("1000090", "10200090", "1000401", "1000402")',
			'material_group_tbl e' => 'd.material_group_id = e.material_group_id AND e.material_group_name = \'MARINATED CUT UPS\'',
			'material_unit_tbl f' => 'd.material_id = f.material_id AND f.material_unit_status = 1',
			'outlet_tbl g' => 'a.outlet_id = g.outlet_id AND g.bc_id IN (' . $bc_id . ')',
			'outlet_brand_tbl h' => 'g.outlet_id = h.outlet_id AND h.outlet_brand_status = 1',
			'brand_tbl i' => 'h.brand_id = i.brand_id',
			'brand_type_tbl j' => 'i.brand_type_id = j.brand_type_id'
			/*'brand_type_tbl j' => 'i.brand_type_id = j.brand_type_id AND j.brand_type_name = \'RETAIL\''*/
		);

		$get_marinated = $this->admin->get_join('sales_tbl a', $join_marinated, TRUE, FALSE, FALSE, 'SUM(c.sales_det_qty / f.sales_unit_equivalent) as marinated_sales_unit,

			(SELECT SUM(x.sales_unit) FROM comparative_volume_tbl x WHERE g.bc_id = x.bc_id AND x.sales_status = 1 AND x.sales_vol = "MARINATED CUT UPS" AND YEAR(x.trans_year) = ' . ($year - 1) . ') as marinated_sales_unit1,

			(SELECT SUM(x.sales_unit) FROM comparative_volume_tbl x WHERE g.bc_id = x.bc_id AND x.sales_status = 1 AND x.sales_vol = "MARINATED CUT UPS" AND YEAR(x.trans_year) = ' . ($year - 2) . ') as marinated_sales_unit2


			');
		$data['marinated'] = $get_marinated->marinated_sales_unit;
		$data['marinated1'] = $get_marinated->marinated_sales_unit1;
		$data['marinated2'] = $get_marinated->marinated_sales_unit2;

		$join_spicy_neck = array(
			'sales_item_tbl b' => 'a.sales_id = b.sales_id AND a.sales_status = 1 AND b.sales_item_status = 1 AND a.sales_year = ' . $year,
			'sales_details_tbl c' => 'b.sales_item_id = c.sales_item_id AND c.sales_det_status = 1',
			'material_tbl d' => 'b.material_id = d.material_id AND d.material_code = "10200090"',
			'material_group_tbl e' => 'd.material_group_id = e.material_group_id AND e.material_group_name = \'MARINATED CUT UPS\'',
			'material_unit_tbl f' => 'd.material_id = f.material_id AND f.material_unit_status = 1',
			'outlet_tbl g' => 'a.outlet_id = g.outlet_id AND g.bc_id IN (' . $bc_id . ')',
			'outlet_brand_tbl h' => 'g.outlet_id = h.outlet_id AND h.outlet_brand_status = 1',
			'brand_tbl i' => 'h.brand_id = i.brand_id',
			'brand_type_tbl j' => 'i.brand_type_id = j.brand_type_id AND j.brand_type_name = \'RETAIL\''
		);

		$get_spicy_neck = $this->admin->get_join('sales_tbl a', $join_spicy_neck, TRUE, FALSE, FALSE, 'SUM(c.sales_det_qty / f.sales_unit_equivalent) as spicy_neck_sales_unit,

			(SELECT SUM(x.sales_unit) FROM comparative_volume_tbl x WHERE g.bc_id = x.bc_id AND x.sales_status = 1 AND x.sales_vol = "SPICY NECK" AND YEAR(x.trans_year) = ' . ($year - 1) . ') as spicy_neck_sales_unit1,

			(SELECT SUM(x.sales_unit) FROM comparative_volume_tbl x WHERE g.bc_id = x.bc_id AND x.sales_status = 1 AND x.sales_vol = "SPICY NECK" AND YEAR(x.trans_year) = ' . ($year - 2) . ') as spicy_neck_sales_unit2
		');
		$data['spicy_neck'] = $get_spicy_neck->spicy_neck_sales_unit;
		$data['spicy_neck1'] = $get_spicy_neck->spicy_neck_sales_unit1;
		$data['spicy_neck2'] = $get_spicy_neck->spicy_neck_sales_unit2;

		$join_roasted_cutups = array(
			'sales_item_tbl b' => 'a.sales_id = b.sales_id AND a.sales_status = 1 AND b.sales_item_status = 1 AND a.sales_year = ' . $year,
			'sales_details_tbl c' => 'b.sales_item_id = c.sales_item_id AND c.sales_det_status = 1',
			'material_tbl d' => 'b.material_id = d.material_id',
			'material_group_tbl e' => 'd.material_group_id = e.material_group_id AND e.material_group_name = \'ROASTED CUT UPS\'',
			'material_unit_tbl f' => 'd.material_id = f.material_id AND f.material_unit_status = 1',
			'outlet_tbl g' => 'a.outlet_id = g.outlet_id AND g.bc_id IN (' . $bc_id . ')',
			'outlet_brand_tbl h' => 'g.outlet_id = h.outlet_id AND h.outlet_brand_status = 1',
			'brand_tbl i' => 'h.brand_id = i.brand_id',
			'brand_type_tbl j' => 'i.brand_type_id = j.brand_type_id AND j.brand_type_name = \'RETAIL\''
		);

		$get_roasted_cutups = $this->admin->get_join('sales_tbl a', $join_roasted_cutups, TRUE, FALSE, FALSE, 'SUM(c.sales_det_qty / f.sales_unit_equivalent) as roasted_cutups_sales_unit,

			(SELECT SUM(x.sales_unit) FROM comparative_volume_tbl x WHERE g.bc_id = x.bc_id AND x.sales_status = 1 AND x.sales_vol = "ROASTED CUT UPS" AND YEAR(x.trans_year) = ' . ($year - 1) . ') as roasted_cutups_sales_unit1,

			(SELECT SUM(x.sales_unit) FROM comparative_volume_tbl x WHERE g.bc_id = x.bc_id AND x.sales_status = 1 AND x.sales_vol = "ROASTED CUT UPS" AND YEAR(x.trans_year) = ' . ($year - 2) . ') as roasted_cutups_sales_unit2

			');
		$data['roasted_cutups'] = $get_roasted_cutups->roasted_cutups_sales_unit;
		$data['roasted_cutups1'] = $get_roasted_cutups->roasted_cutups_sales_unit1;
		$data['roasted_cutups2'] = $get_roasted_cutups->roasted_cutups_sales_unit2;

		$join_roasted_chicken = array(
			'sales_item_tbl b' => 'a.sales_id = b.sales_id AND a.sales_status = 1 AND b.sales_item_status = 1 AND a.sales_year = ' . $year,
			'sales_details_tbl c' => 'b.sales_item_id = c.sales_item_id AND c.sales_det_status = 1',
			'material_tbl d' => 'b.material_id = d.material_id',
			'material_group_tbl e' => 'd.material_group_id = e.material_group_id AND e.material_group_name = \'ROASTED CHICKEN\'',
			'material_unit_tbl f' => 'd.material_id = f.material_id AND f.material_unit_status = 1',
			'outlet_tbl g' => 'a.outlet_id = g.outlet_id AND g.bc_id IN (' . $bc_id . ')',
			'outlet_brand_tbl h' => 'g.outlet_id = h.outlet_id AND h.outlet_brand_status = 1',
			'brand_tbl i' => 'h.brand_id = i.brand_id',
			'brand_type_tbl j' => 'i.brand_type_id = j.brand_type_id AND j.brand_type_name = \'RETAIL\''
		);

		$get_roasted_chicken = $this->admin->get_join('sales_tbl a', $join_roasted_chicken, TRUE, FALSE, FALSE, 'SUM(c.sales_det_qty / f.sales_unit_equivalent) as roasted_chicken_sales_unit,

			(SELECT SUM(x.sales_unit) FROM comparative_volume_tbl x WHERE g.bc_id = x.bc_id AND x.sales_status = 1 AND x.sales_vol = "ROASTED CHICKEN" AND YEAR(x.trans_year) = ' . ($year - 1) . ') as roasted_chicken_sales_unit1,

			(SELECT SUM(x.sales_unit) FROM comparative_volume_tbl x WHERE g.bc_id = x.bc_id AND x.sales_status = 1 AND x.sales_vol = "ROASTED CHICKEN" AND YEAR(x.trans_year) = ' . ($year - 2) . ') as roasted_chicken_sales_unit2
		');
		$data['roasted_chicken'] = $get_roasted_chicken->roasted_chicken_sales_unit;
		$data['roasted_chicken1'] = $get_roasted_chicken->roasted_chicken_sales_unit1;
		$data['roasted_chicken2'] = $get_roasted_chicken->roasted_chicken_sales_unit2;

		$join_marinated_chicken_raw = array(
			'sales_item_tbl b' => 'a.sales_id = b.sales_id AND a.sales_status = 1 AND b.sales_item_status = 1 AND a.sales_year = ' . $year,
			'sales_details_tbl c' => 'b.sales_item_id = c.sales_item_id AND c.sales_det_status = 1',
			'material_tbl d' => 'b.material_id = d.material_id',
			'material_group_tbl e' => 'd.material_group_id = e.material_group_id AND e.material_group_name = \'MARINATED CHICKEN (RAW)\'',
			'material_unit_tbl f' => 'd.material_id = f.material_id AND f.material_unit_status = 1',
			'outlet_tbl g' => 'a.outlet_id = g.outlet_id AND g.bc_id IN (' . $bc_id . ')',
			'outlet_brand_tbl h' => 'g.outlet_id = h.outlet_id AND h.outlet_brand_status = 1',
			'brand_tbl i' => 'h.brand_id = i.brand_id',
			'brand_type_tbl j' => 'i.brand_type_id = j.brand_type_id'
			/*'brand_type_tbl j' => 'i.brand_type_id = j.brand_type_id AND j.brand_type_name = \'RETAIL\''*/
		);

		$get_marinated_chicken_raw = $this->admin->get_join('sales_tbl a', $join_marinated_chicken_raw, TRUE, FALSE, FALSE, 'SUM(c.sales_det_qty / f.sales_unit_equivalent) as marinated_chicken_raw_sales_unit,

			(SELECT SUM(x.sales_unit) FROM comparative_volume_tbl x WHERE g.bc_id = x.bc_id AND x.sales_status = 1 AND x.sales_vol = "MARINATED CHICKEN (RAW)" AND YEAR(x.trans_year) = ' . ($year - 1) . ') as marinated_chicken_raw_sales_unit1,

			(SELECT SUM(x.sales_unit) FROM comparative_volume_tbl x WHERE g.bc_id = x.bc_id AND x.sales_status = 1 AND x.sales_vol = "MARINATED CHICKEN (RAW)" AND YEAR(x.trans_year) = ' . ($year - 2) . ') as marinated_chicken_raw_sales_unit2

		');
		$data['marinated_chicken_raw'] = $get_marinated_chicken_raw->marinated_chicken_raw_sales_unit;
		$data['marinated_chicken_raw1'] = $get_marinated_chicken_raw->marinated_chicken_raw_sales_unit1;
		$data['marinated_chicken_raw2'] = $get_marinated_chicken_raw->marinated_chicken_raw_sales_unit2;

		$join_other = array(
			'sales_item_tbl b' => 'a.sales_id = b.sales_id AND a.sales_status = 1 AND b.sales_item_status = 1 AND a.sales_year = ' . $year,
			'sales_details_tbl c' => 'b.sales_item_id = c.sales_item_id AND c.sales_det_status = 1',
			'material_tbl d' => 'b.material_id = d.material_id',
			'material_group_tbl e' => 'd.material_group_id = e.material_group_id AND e.material_group_name = \'OTHER SPECIALTY PRODUCTS\'',
			'material_unit_tbl f' => 'd.material_id = f.material_id AND f.material_unit_status = 1',
			'outlet_tbl g' => 'a.outlet_id = g.outlet_id AND g.bc_id IN (' . $bc_id . ')',
			'outlet_brand_tbl h' => 'g.outlet_id = h.outlet_id AND h.outlet_brand_status = 1',
			'brand_tbl i' => 'h.brand_id = i.brand_id',
			'brand_type_tbl j' => 'i.brand_type_id = j.brand_type_id AND j.brand_type_name = \'RETAIL\''
		);

		$get_other = $this->admin->get_join('sales_tbl a', $join_other, TRUE, FALSE, FALSE, 'SUM(c.sales_det_qty / f.sales_unit_equivalent) as other_sales_unit,

			(SELECT SUM(x.sales_unit) FROM comparative_volume_tbl x WHERE g.bc_id = x.bc_id AND x.sales_status = 1 AND x.sales_vol = "OTHER SPECIALTY PRODUCTS" AND YEAR(x.trans_year) = ' . ($year - 1) . ') as other_sales_unit1,

			(SELECT SUM(x.sales_unit) FROM comparative_volume_tbl x WHERE g.bc_id = x.bc_id AND x.sales_status = 1 AND x.sales_vol = "OTHER SPECIALTY PRODUCTS" AND YEAR(x.trans_year) = ' . ($year - 2) . ') as other_sales_unit2
		');
		$data['others'] = $get_other->other_sales_unit;
		$data['others1'] = $get_other->other_sales_unit1;
		$data['others2'] = $get_other->other_sales_unit2;

		$join_liempo = array(
			'sales_item_tbl b' => 'a.sales_id = b.sales_id AND a.sales_status = 1 AND b.sales_item_status = 1 AND a.sales_year = ' . $year,
			'sales_details_tbl c' => 'b.sales_item_id = c.sales_item_id AND c.sales_det_status = 1',
			'material_tbl d' => 'b.material_id = d.material_id',
			'material_group_tbl e' => 'd.material_group_id = e.material_group_id AND e.material_group_name = \'LIEMPO\'',
			'material_unit_tbl f' => 'd.material_id = f.material_id AND f.material_unit_status = 1',
			'outlet_tbl g' => 'a.outlet_id = g.outlet_id AND g.bc_id IN (' . $bc_id . ')',
			'outlet_brand_tbl h' => 'g.outlet_id = h.outlet_id AND h.outlet_brand_status = 1',
			'brand_tbl i' => 'h.brand_id = i.brand_id',
			'brand_type_tbl j' => 'i.brand_type_id = j.brand_type_id AND j.brand_type_name = \'RETAIL\''
		);

		$get_liempo = $this->admin->get_join('sales_tbl a', $join_liempo, TRUE, FALSE, FALSE, 'SUM(c.sales_det_qty / f.sales_unit_equivalent) as liempo_sales_unit,

			(SELECT SUM(x.sales_unit) FROM comparative_volume_tbl x WHERE g.bc_id = x.bc_id AND x.sales_status = 1 AND x.sales_vol = "LIEMPO" AND YEAR(x.trans_year) = ' . ($year - 1) . ') as liempo_sales_unit1,

			(SELECT SUM(x.sales_unit) FROM comparative_volume_tbl x WHERE g.bc_id = x.bc_id AND x.sales_status = 1 AND x.sales_vol = "LIEMPO" AND YEAR(x.trans_year) = ' . ($year - 2) . ') as liempo_sales_unit2
		');
		$data['liempo'] = $get_liempo->liempo_sales_unit;
		$data['liempo1'] = $get_liempo->liempo_sales_unit1;
		$data['liempo2'] = $get_liempo->liempo_sales_unit2;

		$join_vap = array(
			'sales_item_tbl b' => 'a.sales_id = b.sales_id AND a.sales_status = 1 AND b.sales_item_status = 1 AND a.sales_year = ' . $year,
			'sales_details_tbl c' => 'b.sales_item_id = c.sales_item_id AND c.sales_det_status = 1',
			'material_tbl d' => 'b.material_id = d.material_id',
			'material_group_tbl e' => 'd.material_group_id = e.material_group_id AND e.material_group_name = \'OTHER VAP PRODUCTS\'',
			'material_unit_tbl f' => 'd.material_id = f.material_id AND f.material_unit_status = 1',
			'outlet_tbl g' => 'a.outlet_id = g.outlet_id AND g.bc_id IN (' . $bc_id . ')',
			'outlet_brand_tbl h' => 'g.outlet_id = h.outlet_id AND h.outlet_brand_status = 1',
			'brand_tbl i' => 'h.brand_id = i.brand_id',
			'brand_type_tbl j' => 'i.brand_type_id = j.brand_type_id AND j.brand_type_name = \'RETAIL\''
		);

		$get_vap = $this->admin->get_join('sales_tbl a', $join_vap, TRUE, FALSE, FALSE, 'SUM(c.sales_det_qty / f.sales_unit_equivalent) as vap_sales_unit,

			(SELECT SUM(x.sales_unit) FROM comparative_volume_tbl x WHERE g.bc_id = x.bc_id AND x.sales_status = 1 AND x.sales_vol = "VAP" AND YEAR(x.trans_year) = ' . ($year - 1) . ') as vap_sales_unit1,

			(SELECT SUM(x.sales_unit) FROM comparative_volume_tbl x WHERE g.bc_id = x.bc_id AND x.sales_status = 1 AND x.sales_vol = "VAP" AND YEAR(x.trans_year) = ' . ($year - 2) . ') as vap_sales_unit2

		');
		$data['vap'] = $get_vap->vap_sales_unit;
		$data['vap1'] = $get_vap->vap_sales_unit1;
		$data['vap2'] = $get_vap->vap_sales_unit2;

		return $data;
	}

	public function price_assumption_report($bc_id, $year){
		$info = $this->_require_login();

		$count = 0;
		$newline_count = 15;
		$join_live = array(
			'sales_item_tbl b' => 'a.sales_id = b.sales_id AND a.sales_status = 1 AND b.sales_item_status = 1 AND a.sales_year = ' . $year,
			'sales_details_tbl c' => 'b.sales_item_id = c.sales_item_id AND c.sales_det_status = 1',
			'material_tbl d' => 'b.material_id = d.material_id',
			'material_group_tbl e' => 'd.material_group_id = e.material_group_id',
			'outlet_tbl f' => 'a.outlet_id = f.outlet_id AND f.bc_id IN (' . $bc_id . ')',
			'outlet_brand_tbl g' => 'f.outlet_id = g.outlet_id AND g.outlet_brand_status = 1',
			'brand_tbl h' => 'g.brand_id  = h.brand_id AND h.brand_name = "LIVE"'
		);

		$get_live = $this->admin->get_join('sales_tbl a', $join_live, FALSE, FALSE, 'YEAR(c.sales_det_date), MONTH(c.sales_det_date)', 'AVG(c.sales_det_asp) as asp, MONTH(c.sales_det_date) asp_date');

		$data['live1'] = 0;
		$data['live2'] = 0;
		$data['live3'] = 0;
		$data['live4'] = 0;
		$data['live5'] = 0;
		$data['live6'] = 0;
		$data['live7'] = 0;
		$data['live8'] = 0;
		$data['live9'] = 0;
		$data['live10'] = 0;
		$data['live11'] = 0;
		$data['live12'] = 0;
		$data['live_total'] = 0;
		$data['live_count'] = 0;
		$data['live_avg'] = 0;
		$data['live_min'] = 0;
		$data['live_max'] = 0;

		foreach($get_live as $row){
			$asp = $row->asp;
			$month = $row->asp_date;
			$data['live' . $month] = $asp;

			$data['live_total'] += $asp;

			if($asp < $data['live_min'] || $data['live_count'] == 0){
				$data['live_min'] = $asp;
			}

			if($asp > $data['live_max'] || $data['live_count'] == 0){
				$data['live_max'] = $asp;
			}

			$data['live_count']++;
		}

		$data['live_avg'] = $data['live_total'] != 0 ? $data['live_total'] / $data['live_count'] : 0;


		
		$data['live_prev'] = 0;
		$data['live_prev2'] = 0;

		$live_prev_year2 = $this->admin->get_data('comparative_price_tbl', 'comp_price_product = "LIVE" AND comp_price_segment = "COM" AND comp_price_year = ' . $year - 2 . ' AND bc_id IN (' . $bc_id . ')', 
			FALSE, 'AVG(comp_price_jan) as jan_price, AVG(comp_price_feb) as feb_price, AVG(comp_price_mar) as mar_price, AVG(comp_price_apr) as apr_price, AVG(comp_price_may) as may_price, AVG(comp_price_jun) as jun_price, AVG(comp_price_jul) as jul_price, AVG(comp_price_aug) as aug_price, AVG(comp_price_sep) as sep_price, AVG(comp_price_oct) as oct_price, AVG(comp_price_nov) as nov_price, AVG(comp_price_dec) as dec_price');
		
		$data['live_prev_year2_jan'] = 0;
		$data['live_prev_year2_feb'] = 0;
		$data['live_prev_year2_mar'] = 0;
		$data['live_prev_year2_apr'] = 0;
		$data['live_prev_year2_may'] = 0;
		$data['live_prev_year2_jun'] = 0;
		$data['live_prev_year2_jul'] = 0;
		$data['live_prev_year2_aug'] = 0;
		$data['live_prev_year2_sep'] = 0;
		$data['live_prev_year2_oct'] = 0;
		$data['live_prev_year2_nov'] = 0;
		$data['live_prev_year2_dec'] = 0;
		$data['live_prev_year2_avg'] = 0;
		$data['live_prev_year2_min'] = 0;
		$data['live_prev_year2_max'] = 0;

		foreach($live_prev_year2 as $row){
			$data['live_prev_year2_jan'] = $row->jan_price;
			$data['live_prev_year2_feb'] = $row->feb_price;
			$data['live_prev_year2_mar'] = $row->mar_price;
			$data['live_prev_year2_apr'] = $row->apr_price;
			$data['live_prev_year2_may'] = $row->may_price;
			$data['live_prev_year2_jun'] = $row->jun_price;
			$data['live_prev_year2_jul'] = $row->jul_price;
			$data['live_prev_year2_aug'] = $row->aug_price;
			$data['live_prev_year2_sep'] = $row->sep_price;
			$data['live_prev_year2_oct'] = $row->oct_price;
			$data['live_prev_year2_nov'] = $row->nov_price;
			$data['live_prev_year2_dec'] = $row->dec_price;
			$data['live_prev_year2_avg'] = ($row->jan_price + $row->feb_price + $row->mar_price + $row->apr_price + $row->may_price + $row->jun_price + $row->jul_price + $row->aug_price + $row->sep_price + $row->oct_price + $row->nov_price + $row->dec_price) / 12;

			$data['live_prev_year2_min'] = min($row->jan_price, $row->feb_price, $row->mar_price, $row->apr_price, $row->may_price, $row->jun_price, $row->jul_price, $row->aug_price, $row->sep_price, $row->oct_price, $row->nov_price, $row->dec_price);

			$data['live_prev_year2_max'] = max($row->jan_price, $row->feb_price, $row->mar_price, $row->apr_price, $row->may_price, $row->jun_price, $row->jul_price, $row->aug_price, $row->sep_price, $row->oct_price, $row->nov_price, $row->dec_price);

		}


		$live_prev_year1 = $this->admin->get_data('comparative_price_tbl', 'comp_price_product = "LIVE" AND comp_price_segment = "COM" AND comp_price_year = ' . ($year - 1) . ' AND bc_id IN (' . $bc_id . ')', 
			FALSE, 'AVG(comp_price_jan) as jan_price, AVG(comp_price_feb) as feb_price, AVG(comp_price_mar) as mar_price, AVG(comp_price_apr) as apr_price, AVG(comp_price_may) as may_price, AVG(comp_price_jun) as jun_price, AVG(comp_price_jul) as jul_price, AVG(comp_price_aug) as aug_price, AVG(comp_price_sep) as sep_price, AVG(comp_price_oct) as oct_price, AVG(comp_price_nov) as nov_price, AVG(comp_price_dec) as dec_price');
		
		$data['live_prev_year1_jan'] = 0;
		$data['live_prev_year1_feb'] = 0;
		$data['live_prev_year1_mar'] = 0;
		$data['live_prev_year1_apr'] = 0;
		$data['live_prev_year1_may'] = 0;
		$data['live_prev_year1_jun'] = 0;
		$data['live_prev_year1_jul'] = 0;
		$data['live_prev_year1_aug'] = 0;
		$data['live_prev_year1_sep'] = 0;
		$data['live_prev_year1_oct'] = 0;
		$data['live_prev_year1_nov'] = 0;
		$data['live_prev_year1_dec'] = 0;
		$data['live_prev_year1_avg'] = 0;
		$data['live_prev_year1_min'] = 0;
		$data['live_prev_year1_max'] = 0;

		foreach($live_prev_year1 as $row){
			$data['live_prev_year1_jan'] = $row->jan_price;
			$data['live_prev_year1_feb'] = $row->feb_price;
			$data['live_prev_year1_mar'] = $row->mar_price;
			$data['live_prev_year1_apr'] = $row->apr_price;
			$data['live_prev_year1_may'] = $row->may_price;
			$data['live_prev_year1_jun'] = $row->jun_price;
			$data['live_prev_year1_jul'] = $row->jul_price;
			$data['live_prev_year1_aug'] = $row->aug_price;
			$data['live_prev_year1_sep'] = $row->sep_price;
			$data['live_prev_year1_oct'] = $row->oct_price;
			$data['live_prev_year1_nov'] = $row->nov_price;
			$data['live_prev_year1_dec'] = $row->dec_price;
			$data['live_prev_year1_avg'] = ($row->jan_price + $row->feb_price + $row->mar_price + $row->apr_price + $row->may_price + $row->jun_price + $row->jul_price + $row->aug_price + $row->sep_price + $row->oct_price + $row->nov_price + $row->dec_price) / 12;

			$data['live_prev_year1_min'] = min($row->jan_price, $row->feb_price, $row->mar_price, $row->apr_price, $row->may_price, $row->jun_price, $row->jul_price, $row->aug_price, $row->sep_price, $row->oct_price, $row->nov_price, $row->dec_price);

			$data['live_prev_year1_max'] = max($row->jan_price, $row->feb_price, $row->mar_price, $row->apr_price, $row->may_price, $row->jun_price, $row->jul_price, $row->aug_price, $row->sep_price, $row->oct_price, $row->nov_price, $row->dec_price);
		}

		/*Van Sales*/

		$join_vansales = array(
			'sales_item_tbl b' => 'a.sales_id = b.sales_id AND a.sales_status = 1 AND b.sales_item_status = 1 AND a.sales_year = ' . $year,
			'sales_details_tbl c' => 'b.sales_item_id = c.sales_item_id AND c.sales_det_status = 1',
			'material_tbl d' => 'b.material_id = d.material_id',
			'material_group_tbl e' => 'd.material_group_id = e.material_group_id AND e.material_group_name = "DRESSED"',
			'outlet_tbl f' => 'a.outlet_id = f.outlet_id AND f.bc_id IN (' . $bc_id . ')',
			'outlet_brand_tbl g' => 'f.outlet_id = g.outlet_id AND g.outlet_brand_status = 1',
			'brand_tbl h' => 'g.brand_id  = h.brand_id AND h.brand_name = \'VAN SALES\''
		);

		$get_vansales = $this->admin->get_join('sales_tbl a', $join_vansales, FALSE, FALSE, 'YEAR(c.sales_det_date), MONTH(c.sales_det_date)', 'AVG(c.sales_det_asp) as asp, MONTH(c.sales_det_date) asp_date');

		$data['vansales1'] = 0;
		$data['vansales2'] = 0;
		$data['vansales3'] = 0;
		$data['vansales4'] = 0;
		$data['vansales5'] = 0;
		$data['vansales6'] = 0;
		$data['vansales7'] = 0;
		$data['vansales8'] = 0;
		$data['vansales9'] = 0;
		$data['vansales10'] = 0;
		$data['vansales11'] = 0;
		$data['vansales12'] = 0;
		$data['vansales_total'] = 0;
		$data['vansales_count'] = 0;
		$data['vansales_avg'] = 0;
		$data['vansales_min'] = 0;
		$data['vansales_max'] = 0;

		foreach($get_vansales as $row){
			$asp = $row->asp;
			$month = $row->asp_date;
			$data['vansales' . $month] = $asp;
			$data['vansales_total'] += $asp;

			if($asp < $data['vansales_min'] || $data['vansales_count'] == 0){
				$data['vansales_min'] = $asp;
			}

			if($asp > $data['vansales_max'] || $data['vansales_count'] == 0){
				$data['vansales_max'] = $asp;
			}

			$data['vansales_count']++;
		}

		$data['vansales_avg'] = $data['vansales_total'] != 0 ? $data['vansales_total'] / $data['vansales_count'] : 0;

		$vansales_prev_year2 = $this->admin->get_data('comparative_price_tbl', 'comp_price_product = "VAN SALES" AND comp_price_segment = "COM" AND comp_price_year = ' . ($year - 2) . ' AND bc_id IN (' . $bc_id . ')', 
			FALSE, 'AVG(comp_price_jan) as jan_price, AVG(comp_price_feb) as feb_price, AVG(comp_price_mar) as mar_price, AVG(comp_price_apr) as apr_price, AVG(comp_price_may) as may_price, AVG(comp_price_jun) as jun_price, AVG(comp_price_jul) as jul_price, AVG(comp_price_aug) as aug_price, AVG(comp_price_sep) as sep_price, AVG(comp_price_oct) as oct_price, AVG(comp_price_nov) as nov_price, AVG(comp_price_dec) as dec_price');
		
		$data['vansales_prev_year2_jan'] = 0;
		$data['vansales_prev_year2_feb'] = 0;
		$data['vansales_prev_year2_mar'] = 0;
		$data['vansales_prev_year2_apr'] = 0;
		$data['vansales_prev_year2_may'] = 0;
		$data['vansales_prev_year2_jun'] = 0;
		$data['vansales_prev_year2_jul'] = 0;
		$data['vansales_prev_year2_aug'] = 0;
		$data['vansales_prev_year2_sep'] = 0;
		$data['vansales_prev_year2_oct'] = 0;
		$data['vansales_prev_year2_nov'] = 0;
		$data['vansales_prev_year2_dec'] = 0;
		$data['vansales_prev_year2_avg'] = 0;
		$data['vansales_prev_year2_min'] = 0;
		$data['vansales_prev_year2_max'] = 0;

		foreach($vansales_prev_year2 as $row){
			$data['vansales_prev_year2_jan'] = $row->jan_price;
			$data['vansales_prev_year2_feb'] = $row->feb_price;
			$data['vansales_prev_year2_mar'] = $row->mar_price;
			$data['vansales_prev_year2_apr'] = $row->apr_price;
			$data['vansales_prev_year2_may'] = $row->may_price;
			$data['vansales_prev_year2_jun'] = $row->jun_price;
			$data['vansales_prev_year2_jul'] = $row->jul_price;
			$data['vansales_prev_year2_aug'] = $row->aug_price;
			$data['vansales_prev_year2_sep'] = $row->sep_price;
			$data['vansales_prev_year2_oct'] = $row->oct_price;
			$data['vansales_prev_year2_nov'] = $row->nov_price;
			$data['vansales_prev_year2_dec'] = $row->dec_price;
			$data['vansales_prev_year2_avg'] = ($row->jan_price + $row->feb_price + $row->mar_price + $row->apr_price + $row->may_price + $row->jun_price + $row->jul_price + $row->aug_price + $row->sep_price + $row->oct_price + $row->nov_price + $row->dec_price) / 12;

			$data['vansales_prev_year2_min'] = min($row->jan_price, $row->feb_price, $row->mar_price, $row->apr_price, $row->may_price, $row->jun_price, $row->jul_price, $row->aug_price, $row->sep_price, $row->oct_price, $row->nov_price, $row->dec_price);

			$data['vansales_prev_year2_max'] = max($row->jan_price, $row->feb_price, $row->mar_price, $row->apr_price, $row->may_price, $row->jun_price, $row->jul_price, $row->aug_price, $row->sep_price, $row->oct_price, $row->nov_price, $row->dec_price);
		}

		$vansales_prev_year1 = $this->admin->get_data('comparative_price_tbl', 'comp_price_product = "VAN SALES" AND comp_price_segment = "COM" AND comp_price_year = ' . ($year - 1) . ' AND bc_id IN (' . $bc_id . ')', 
			FALSE, 'AVG(comp_price_jan) as jan_price, AVG(comp_price_feb) as feb_price, AVG(comp_price_mar) as mar_price, AVG(comp_price_apr) as apr_price, AVG(comp_price_may) as may_price, AVG(comp_price_jun) as jun_price, AVG(comp_price_jul) as jul_price, AVG(comp_price_aug) as aug_price, AVG(comp_price_sep) as sep_price, AVG(comp_price_oct) as oct_price, AVG(comp_price_nov) as nov_price, AVG(comp_price_dec) as dec_price');
		
		$data['vansales_prev_year1_jan'] = 0;
		$data['vansales_prev_year1_feb'] = 0;
		$data['vansales_prev_year1_mar'] = 0;
		$data['vansales_prev_year1_apr'] = 0;
		$data['vansales_prev_year1_may'] = 0;
		$data['vansales_prev_year1_jun'] = 0;
		$data['vansales_prev_year1_jul'] = 0;
		$data['vansales_prev_year1_aug'] = 0;
		$data['vansales_prev_year1_sep'] = 0;
		$data['vansales_prev_year1_oct'] = 0;
		$data['vansales_prev_year1_nov'] = 0;
		$data['vansales_prev_year1_dec'] = 0;
		$data['vansales_prev_year1_avg'] = 0;
		$data['vansales_prev_year1_min'] = 0;
		$data['vansales_prev_year1_max'] = 0;

		foreach($vansales_prev_year1 as $row){
			$data['vansales_prev_year1_jan'] = $row->jan_price;
			$data['vansales_prev_year1_feb'] = $row->feb_price;
			$data['vansales_prev_year1_mar'] = $row->mar_price;
			$data['vansales_prev_year1_apr'] = $row->apr_price;
			$data['vansales_prev_year1_may'] = $row->may_price;
			$data['vansales_prev_year1_jun'] = $row->jun_price;
			$data['vansales_prev_year1_jul'] = $row->jul_price;
			$data['vansales_prev_year1_aug'] = $row->aug_price;
			$data['vansales_prev_year1_sep'] = $row->sep_price;
			$data['vansales_prev_year1_oct'] = $row->oct_price;
			$data['vansales_prev_year1_nov'] = $row->nov_price;
			$data['vansales_prev_year1_dec'] = $row->dec_price;
			$data['vansales_prev_year1_avg'] = ($row->jan_price + $row->feb_price + $row->mar_price + $row->apr_price + $row->may_price + $row->jun_price + $row->jul_price + $row->aug_price + $row->sep_price + $row->oct_price + $row->nov_price + $row->dec_price) / 12;

			$data['vansales_prev_year1_min'] = min($row->jan_price, $row->feb_price, $row->mar_price, $row->apr_price, $row->may_price, $row->jun_price, $row->jul_price, $row->aug_price, $row->sep_price, $row->oct_price, $row->nov_price, $row->dec_price);

			$data['vansales_prev_year1_max'] = max($row->jan_price, $row->feb_price, $row->mar_price, $row->apr_price, $row->may_price, $row->jun_price, $row->jul_price, $row->aug_price, $row->sep_price, $row->oct_price, $row->nov_price, $row->dec_price);
		}

		$join_smkt = array(
			'sales_item_tbl b' => 'a.sales_id = b.sales_id AND a.sales_status = 1 AND b.sales_item_status = 1 AND a.sales_year = ' . $year,
			'sales_details_tbl c' => 'b.sales_item_id = c.sales_item_id AND c.sales_det_status = 1',
			'material_tbl d' => 'b.material_id = d.material_id',
			'material_group_tbl e' => 'd.material_group_id = e.material_group_id AND e.material_group_name = "DRESSED"',
			'outlet_tbl f' => 'a.outlet_id = f.outlet_id AND f.bc_id IN (' . $bc_id . ')',
			'outlet_brand_tbl g' => 'f.outlet_id = g.outlet_id AND g.outlet_brand_status = 1',
			'brand_tbl h' => 'g.brand_id  = h.brand_id AND h.brand_name = \'SUPERMARKET\''
		);

		$join_tds = array(
			'sales_item_tbl b' => 'a.sales_id = b.sales_id AND a.sales_status = 1 AND b.sales_item_status = 1 AND a.sales_year = ' . $year,
			'sales_details_tbl c' => 'b.sales_item_id = c.sales_item_id AND c.sales_det_status = 1',
			'material_tbl d' => 'b.material_id = d.material_id',
			'material_group_tbl e' => 'd.material_group_id = e.material_group_id AND e.material_group_name = "DRESSED"',
			'outlet_tbl f' => 'a.outlet_id = f.outlet_id AND f.bc_id IN (' . $bc_id . ')',
			'outlet_brand_tbl g' => 'f.outlet_id = g.outlet_id AND g.outlet_brand_status = 1',
			'brand_tbl h' => 'g.brand_id  = h.brand_id AND h.brand_name = \'TRADE DISTRIBUTOR\''
		);

		$get_tds = $this->admin->get_join('sales_tbl a', $join_tds, FALSE, FALSE, 'YEAR(c.sales_det_date), MONTH(c.sales_det_date)', 'AVG(c.sales_det_asp) as asp, MONTH(c.sales_det_date) asp_date');

		$data['tds1'] = 0;
		$data['tds2'] = 0;
		$data['tds3'] = 0;
		$data['tds4'] = 0;
		$data['tds5'] = 0;
		$data['tds6'] = 0;
		$data['tds7'] = 0;
		$data['tds8'] = 0;
		$data['tds9'] = 0;
		$data['tds10'] = 0;
		$data['tds11'] = 0;
		$data['tds12'] = 0;
		$data['tds_total'] = 0;
		$data['tds_count'] = 0;
		$data['tds_avg'] = 0;
		$data['tds_min'] = 0;
		$data['tds_max'] = 0;

		foreach($get_tds as $row){
			$asp = $row->asp;
			$month = $row->asp_date;
			$data['tds' . $month] = $asp;
			$data['tds_total'] += $asp;

			if($asp < $data['tds_min'] || $data['tds_count'] == 0){
				$data['tds_min'] = $asp;
			}

			if($asp > $data['tds_max'] || $data['tds_count'] == 0){
				$data['tds_max'] = $asp;
			}

			$data['tds_count']++;
		}

		$data['tds_avg'] = $data['tds_total'] != 0 ? $data['tds_total'] / $data['tds_count'] : 0;

		$tds_prev_year2 = $this->admin->get_data('comparative_price_tbl', 'comp_price_product = "TDs" AND comp_price_segment = "COM" AND comp_price_year = ' . ($year - 2) . ' AND bc_id IN (' . $bc_id . ')', 
			FALSE, 'AVG(comp_price_jan) as jan_price, AVG(comp_price_feb) as feb_price, AVG(comp_price_mar) as mar_price, AVG(comp_price_apr) as apr_price, AVG(comp_price_may) as may_price, AVG(comp_price_jun) as jun_price, AVG(comp_price_jul) as jul_price, AVG(comp_price_aug) as aug_price, AVG(comp_price_sep) as sep_price, AVG(comp_price_oct) as oct_price, AVG(comp_price_nov) as nov_price, AVG(comp_price_dec) as dec_price');
		
		$data['tds_prev_year2_jan'] = 0;
		$data['tds_prev_year2_feb'] = 0;
		$data['tds_prev_year2_mar'] = 0;
		$data['tds_prev_year2_apr'] = 0;
		$data['tds_prev_year2_may'] = 0;
		$data['tds_prev_year2_jun'] = 0;
		$data['tds_prev_year2_jul'] = 0;
		$data['tds_prev_year2_aug'] = 0;
		$data['tds_prev_year2_sep'] = 0;
		$data['tds_prev_year2_oct'] = 0;
		$data['tds_prev_year2_nov'] = 0;
		$data['tds_prev_year2_dec'] = 0;
		$data['tds_prev_year2_avg'] = 0;
		$data['tds_prev_year2_min'] = 0;
		$data['tds_prev_year2_max'] = 0;

		foreach($tds_prev_year2 as $row){
			$data['tds_prev_year2_jan'] = $row->jan_price;
			$data['tds_prev_year2_feb'] = $row->feb_price;
			$data['tds_prev_year2_mar'] = $row->mar_price;
			$data['tds_prev_year2_apr'] = $row->apr_price;
			$data['tds_prev_year2_may'] = $row->may_price;
			$data['tds_prev_year2_jun'] = $row->jun_price;
			$data['tds_prev_year2_jul'] = $row->jul_price;
			$data['tds_prev_year2_aug'] = $row->aug_price;
			$data['tds_prev_year2_sep'] = $row->sep_price;
			$data['tds_prev_year2_oct'] = $row->oct_price;
			$data['tds_prev_year2_nov'] = $row->nov_price;
			$data['tds_prev_year2_dec'] = $row->dec_price;
			$data['tds_prev_year2_avg'] = ($row->jan_price + $row->feb_price + $row->mar_price + $row->apr_price + $row->may_price + $row->jun_price + $row->jul_price + $row->aug_price + $row->sep_price + $row->oct_price + $row->nov_price + $row->dec_price) / 12;

			$data['tds_prev_year2_min'] = min($row->jan_price, $row->feb_price, $row->mar_price, $row->apr_price, $row->may_price, $row->jun_price, $row->jul_price, $row->aug_price, $row->sep_price, $row->oct_price, $row->nov_price, $row->dec_price);

			$data['tds_prev_year2_max'] = max($row->jan_price, $row->feb_price, $row->mar_price, $row->apr_price, $row->may_price, $row->jun_price, $row->jul_price, $row->aug_price, $row->sep_price, $row->oct_price, $row->nov_price, $row->dec_price);
		}

		$tds_prev_year1 = $this->admin->get_data('comparative_price_tbl', 'comp_price_product = "TDs" AND comp_price_segment = "COM" AND comp_price_year = ' . ($year - 1) . ' AND bc_id IN (' . $bc_id . ')', 
			FALSE, 'AVG(comp_price_jan) as jan_price, AVG(comp_price_feb) as feb_price, AVG(comp_price_mar) as mar_price, AVG(comp_price_apr) as apr_price, AVG(comp_price_may) as may_price, AVG(comp_price_jun) as jun_price, AVG(comp_price_jul) as jul_price, AVG(comp_price_aug) as aug_price, AVG(comp_price_sep) as sep_price, AVG(comp_price_oct) as oct_price, AVG(comp_price_nov) as nov_price, AVG(comp_price_dec) as dec_price');
		
		$data['tds_prev_year1_jan'] = 0;
		$data['tds_prev_year1_feb'] = 0;
		$data['tds_prev_year1_mar'] = 0;
		$data['tds_prev_year1_apr'] = 0;
		$data['tds_prev_year1_may'] = 0;
		$data['tds_prev_year1_jun'] = 0;
		$data['tds_prev_year1_jul'] = 0;
		$data['tds_prev_year1_aug'] = 0;
		$data['tds_prev_year1_sep'] = 0;
		$data['tds_prev_year1_oct'] = 0;
		$data['tds_prev_year1_nov'] = 0;
		$data['tds_prev_year1_dec'] = 0;
		$data['tds_prev_year1_avg'] = 0;
		$data['tds_prev_year1_min'] = 0;
		$data['tds_prev_year1_max'] = 0;

		foreach($tds_prev_year1 as $row){
			$data['tds_prev_year1_jan'] = $row->jan_price;
			$data['tds_prev_year1_feb'] = $row->feb_price;
			$data['tds_prev_year1_mar'] = $row->mar_price;
			$data['tds_prev_year1_apr'] = $row->apr_price;
			$data['tds_prev_year1_may'] = $row->may_price;
			$data['tds_prev_year1_jun'] = $row->jun_price;
			$data['tds_prev_year1_jul'] = $row->jul_price;
			$data['tds_prev_year1_aug'] = $row->aug_price;
			$data['tds_prev_year1_sep'] = $row->sep_price;
			$data['tds_prev_year1_oct'] = $row->oct_price;
			$data['tds_prev_year1_nov'] = $row->nov_price;
			$data['tds_prev_year1_dec'] = $row->dec_price;
			$data['tds_prev_year1_avg'] = ($row->jan_price + $row->feb_price + $row->mar_price + $row->apr_price + $row->may_price + $row->jun_price + $row->jul_price + $row->aug_price + $row->sep_price + $row->oct_price + $row->nov_price + $row->dec_price) / 12;

			$data['tds_prev_year1_min'] = min($row->jan_price, $row->feb_price, $row->mar_price, $row->apr_price, $row->may_price, $row->jun_price, $row->jul_price, $row->aug_price, $row->sep_price, $row->oct_price, $row->nov_price, $row->dec_price);

			$data['tds_prev_year1_max'] = max($row->jan_price, $row->feb_price, $row->mar_price, $row->apr_price, $row->may_price, $row->jun_price, $row->jul_price, $row->aug_price, $row->sep_price, $row->oct_price, $row->nov_price, $row->dec_price);
		}

		$join_smkt = array(
			'sales_item_tbl b' => 'a.sales_id = b.sales_id AND a.sales_status = 1 AND b.sales_item_status = 1 AND a.sales_year = ' . $year,
			'sales_details_tbl c' => 'b.sales_item_id = c.sales_item_id AND c.sales_det_status = 1',
			'material_tbl d' => 'b.material_id = d.material_id',
			'material_group_tbl e' => 'd.material_group_id = e.material_group_id AND e.material_group_name = "DRESSED"',
			'outlet_tbl f' => 'a.outlet_id = f.outlet_id AND f.bc_id IN (' . $bc_id . ')',
			'outlet_brand_tbl g' => 'f.outlet_id = g.outlet_id AND g.outlet_brand_status = 1',
			'brand_tbl h' => 'g.brand_id  = h.brand_id AND h.brand_name = \'SUPERMARKET\''
		);

		$get_smkt = $this->admin->get_join('sales_tbl a', $join_smkt, FALSE, FALSE, 'YEAR(c.sales_det_date), MONTH(c.sales_det_date)', 'AVG(c.sales_det_asp) as asp, MONTH(c.sales_det_date) asp_date');

		$data['smkt1'] = 0;
		$data['smkt2'] = 0;
		$data['smkt3'] = 0;
		$data['smkt4'] = 0;
		$data['smkt5'] = 0;
		$data['smkt6'] = 0;
		$data['smkt7'] = 0;
		$data['smkt8'] = 0;
		$data['smkt9'] = 0;
		$data['smkt10'] = 0;
		$data['smkt11'] = 0;
		$data['smkt12'] = 0;
		$data['smkt_total'] = 0;
		$data['smkt_count'] = 0;
		$data['smkt_avg'] = 0;
		$data['smkt_min'] = 0;
		$data['smkt_max'] = 0;

		foreach($get_smkt as $row){
			$asp = $row->asp;
			$month = $row->asp_date;
			$data['smkt' . $month] = $asp;
			$data['smkt_total'] += $asp;

			if($asp < $data['smkt_min'] || $data['smkt_count'] == 0){
				$data['smkt_min'] = $asp;
			}

			if($asp > $data['smkt_max'] || $data['smkt_count'] == 0){
				$data['smkt_max'] = $asp;
			}

			$data['smkt_count']++;
		}

		$data['smkt_avg'] = $data['smkt_total'] != 0 ? $data['smkt_total'] / $data['smkt_count'] : 0;

		$smkt_prev_year2 = $this->admin->get_data('comparative_price_tbl', 'comp_price_product = "TDs" AND comp_price_segment = "COM" AND comp_price_year = ' . ($year - 2) . ' AND bc_id IN (' . $bc_id . ')', 
			FALSE, 'AVG(comp_price_jan) as jan_price, AVG(comp_price_feb) as feb_price, AVG(comp_price_mar) as mar_price, AVG(comp_price_apr) as apr_price, AVG(comp_price_may) as may_price, AVG(comp_price_jun) as jun_price, AVG(comp_price_jul) as jul_price, AVG(comp_price_aug) as aug_price, AVG(comp_price_sep) as sep_price, AVG(comp_price_oct) as oct_price, AVG(comp_price_nov) as nov_price, AVG(comp_price_dec) as dec_price');
		
		$data['smkt_prev_year2_jan'] = 0;
		$data['smkt_prev_year2_feb'] = 0;
		$data['smkt_prev_year2_mar'] = 0;
		$data['smkt_prev_year2_apr'] = 0;
		$data['smkt_prev_year2_may'] = 0;
		$data['smkt_prev_year2_jun'] = 0;
		$data['smkt_prev_year2_jul'] = 0;
		$data['smkt_prev_year2_aug'] = 0;
		$data['smkt_prev_year2_sep'] = 0;
		$data['smkt_prev_year2_oct'] = 0;
		$data['smkt_prev_year2_nov'] = 0;
		$data['smkt_prev_year2_dec'] = 0;
		$data['smkt_prev_year2_avg'] = 0;
		$data['smkt_prev_year2_min'] = 0;
		$data['smkt_prev_year2_max'] = 0;

		foreach($smkt_prev_year2 as $row){
			$data['smkt_prev_year2_jan'] = $row->jan_price;
			$data['smkt_prev_year2_feb'] = $row->feb_price;
			$data['smkt_prev_year2_mar'] = $row->mar_price;
			$data['smkt_prev_year2_apr'] = $row->apr_price;
			$data['smkt_prev_year2_may'] = $row->may_price;
			$data['smkt_prev_year2_jun'] = $row->jun_price;
			$data['smkt_prev_year2_jul'] = $row->jul_price;
			$data['smkt_prev_year2_aug'] = $row->aug_price;
			$data['smkt_prev_year2_sep'] = $row->sep_price;
			$data['smkt_prev_year2_oct'] = $row->oct_price;
			$data['smkt_prev_year2_nov'] = $row->nov_price;
			$data['smkt_prev_year2_dec'] = $row->dec_price;
			$data['smkt_prev_year2_avg'] = ($row->jan_price + $row->feb_price + $row->mar_price + $row->apr_price + $row->may_price + $row->jun_price + $row->jul_price + $row->aug_price + $row->sep_price + $row->oct_price + $row->nov_price + $row->dec_price) / 12;

			$data['smkt_prev_year2_min'] = min($row->jan_price, $row->feb_price, $row->mar_price, $row->apr_price, $row->may_price, $row->jun_price, $row->jul_price, $row->aug_price, $row->sep_price, $row->oct_price, $row->nov_price, $row->dec_price);

			$data['smkt_prev_year2_max'] = max($row->jan_price, $row->feb_price, $row->mar_price, $row->apr_price, $row->may_price, $row->jun_price, $row->jul_price, $row->aug_price, $row->sep_price, $row->oct_price, $row->nov_price, $row->dec_price);
		}

		$smkt_prev_year1 = $this->admin->get_data('comparative_price_tbl', 'comp_price_product = "SUPERMARKET" AND comp_price_segment = "COM" AND comp_price_year = ' . ($year - 1) . ' AND bc_id IN (' . $bc_id . ')', 
			FALSE, 'AVG(comp_price_jan) as jan_price, AVG(comp_price_feb) as feb_price, AVG(comp_price_mar) as mar_price, AVG(comp_price_apr) as apr_price, AVG(comp_price_may) as may_price, AVG(comp_price_jun) as jun_price, AVG(comp_price_jul) as jul_price, AVG(comp_price_aug) as aug_price, AVG(comp_price_sep) as sep_price, AVG(comp_price_oct) as oct_price, AVG(comp_price_nov) as nov_price, AVG(comp_price_dec) as dec_price');
		
		$data['smkt_prev_year1_jan'] = 0;
		$data['smkt_prev_year1_feb'] = 0;
		$data['smkt_prev_year1_mar'] = 0;
		$data['smkt_prev_year1_apr'] = 0;
		$data['smkt_prev_year1_may'] = 0;
		$data['smkt_prev_year1_jun'] = 0;
		$data['smkt_prev_year1_jul'] = 0;
		$data['smkt_prev_year1_aug'] = 0;
		$data['smkt_prev_year1_sep'] = 0;
		$data['smkt_prev_year1_oct'] = 0;
		$data['smkt_prev_year1_nov'] = 0;
		$data['smkt_prev_year1_dec'] = 0;
		$data['smkt_prev_year1_avg'] = 0;
		$data['smkt_prev_year1_min'] = 0;
		$data['smkt_prev_year1_max'] = 0;

		foreach($smkt_prev_year1 as $row){
			$data['smkt_prev_year1_jan'] = $row->jan_price;
			$data['smkt_prev_year1_feb'] = $row->feb_price;
			$data['smkt_prev_year1_mar'] = $row->mar_price;
			$data['smkt_prev_year1_apr'] = $row->apr_price;
			$data['smkt_prev_year1_may'] = $row->may_price;
			$data['smkt_prev_year1_jun'] = $row->jun_price;
			$data['smkt_prev_year1_jul'] = $row->jul_price;
			$data['smkt_prev_year1_aug'] = $row->aug_price;
			$data['smkt_prev_year1_sep'] = $row->sep_price;
			$data['smkt_prev_year1_oct'] = $row->oct_price;
			$data['smkt_prev_year1_nov'] = $row->nov_price;
			$data['smkt_prev_year1_dec'] = $row->dec_price;
			$data['smkt_prev_year1_avg'] = ($row->jan_price + $row->feb_price + $row->mar_price + $row->apr_price + $row->may_price + $row->jun_price + $row->jul_price + $row->aug_price + $row->sep_price + $row->oct_price + $row->nov_price + $row->dec_price) / 12;

			$data['smkt_prev_year1_min'] = min($row->jan_price, $row->feb_price, $row->mar_price, $row->apr_price, $row->may_price, $row->jun_price, $row->jul_price, $row->aug_price, $row->sep_price, $row->oct_price, $row->nov_price, $row->dec_price);

			$data['smkt_prev_year1_max'] = max($row->jan_price, $row->feb_price, $row->mar_price, $row->apr_price, $row->may_price, $row->jun_price, $row->jul_price, $row->aug_price, $row->sep_price, $row->oct_price, $row->nov_price, $row->dec_price);
		}


		/*Chooks-to-Go*/

		$get_ctg_orc = $this->admin->get_query("SELECT p.size_name, AVG(asp_tbl.jan_asp) as orc_jan, AVG(asp_tbl.feb_asp) as orc_feb, AVG(asp_tbl.mar_asp) as orc_mar, AVG(asp_tbl.apr_asp) as orc_apr, AVG(asp_tbl.may_asp) as orc_may, AVG(asp_tbl.jun_asp) as orc_jun, AVG(asp_tbl.jul_asp) as orc_jul, AVG(asp_tbl.aug_asp) as orc_aug, AVG(asp_tbl.sep_asp) as orc_sep, AVG(asp_tbl.oct_asp) as orc_oct, AVG(asp_tbl.nov_asp) as orc_nov, AVG(asp_tbl.dec_asp) as orc_dec FROM material_tbl o, size_tbl p,

		(SELECT m.material_id, m.size_id,

		(SELECT AVG(c.sales_det_asp) FROM `sales_tbl` `a` JOIN `sales_item_tbl` `b` ON `a`.`sales_id` = `b`.`sales_id` AND `a`.`sales_status` = 1 AND `b`.`sales_item_status` = 1 AND `a`.`sales_year` = " . $year . " JOIN `sales_details_tbl` `c` ON `b`.`sales_item_id` = `c`.`sales_item_id` AND `c`.`sales_det_status` = 1 JOIN `material_tbl` `d` ON `b`.`material_id` = `d`.`material_id` JOIN `material_group_tbl` `e` ON `d`.`material_group_id` = `e`.`material_group_id` JOIN `outlet_tbl` `f` ON `a`.`outlet_id` = `f`.`outlet_id` JOIN `outlet_brand_tbl` `g` ON `f`.`outlet_id` = `g`.`outlet_id` AND `g`.`outlet_brand_status` = 1 JOIN `brand_tbl` `h` ON `g`.`brand_id` = `h`.`brand_id` AND `h`.`brand_name` = 'CHOOKS-TO-GO' AND c.sales_det_date = '" . $year . "-01-01' WHERE d.material_id = m.material_id AND c.sales_det_asp > 0 AND f.bc_id IN (" . $bc_id . ")) as jan_asp,
    
    	(SELECT AVG(c.sales_det_asp) FROM `sales_tbl` `a` JOIN `sales_item_tbl` `b` ON `a`.`sales_id` = `b`.`sales_id` AND `a`.`sales_status` = 1 AND `b`.`sales_item_status` = 1 AND `a`.`sales_year` = " . $year . " JOIN `sales_details_tbl` `c` ON `b`.`sales_item_id` = `c`.`sales_item_id` AND `c`.`sales_det_status` = 1 JOIN `material_tbl` `d` ON `b`.`material_id` = `d`.`material_id` JOIN `material_group_tbl` `e` ON `d`.`material_group_id` = `e`.`material_group_id` JOIN `outlet_tbl` `f` ON `a`.`outlet_id` = `f`.`outlet_id` JOIN `outlet_brand_tbl` `g` ON `f`.`outlet_id` = `g`.`outlet_id` AND `g`.`outlet_brand_status` = 1 JOIN `brand_tbl` `h` ON `g`.`brand_id` = `h`.`brand_id` AND `h`.`brand_name` = 'CHOOKS-TO-GO' AND c.sales_det_date = '" . $year . "-02-01' WHERE d.material_id = m.material_id AND c.sales_det_asp > 0 AND f.bc_id IN (" . $bc_id . ")) as feb_asp,
    
    	(SELECT AVG(c.sales_det_asp) FROM `sales_tbl` `a` JOIN `sales_item_tbl` `b` ON `a`.`sales_id` = `b`.`sales_id` AND `a`.`sales_status` = 1 AND `b`.`sales_item_status` = 1 AND `a`.`sales_year` = " . $year . " JOIN `sales_details_tbl` `c` ON `b`.`sales_item_id` = `c`.`sales_item_id` AND `c`.`sales_det_status` = 1 JOIN `material_tbl` `d` ON `b`.`material_id` = `d`.`material_id` JOIN `material_group_tbl` `e` ON `d`.`material_group_id` = `e`.`material_group_id` JOIN `outlet_tbl` `f` ON `a`.`outlet_id` = `f`.`outlet_id` JOIN `outlet_brand_tbl` `g` ON `f`.`outlet_id` = `g`.`outlet_id` AND `g`.`outlet_brand_status` = 1 JOIN `brand_tbl` `h` ON `g`.`brand_id` = `h`.`brand_id` AND `h`.`brand_name` = 'CHOOKS-TO-GO' AND c.sales_det_date = '" . $year . "-03-01' WHERE d.material_id = m.material_id AND c.sales_det_asp > 0 AND f.bc_id IN (" . $bc_id . ")) as mar_asp,
    
   		(SELECT AVG(c.sales_det_asp) FROM `sales_tbl` `a` JOIN `sales_item_tbl` `b` ON `a`.`sales_id` = `b`.`sales_id` AND `a`.`sales_status` = 1 AND `b`.`sales_item_status` = 1 AND `a`.`sales_year` = " . $year . " JOIN `sales_details_tbl` `c` ON `b`.`sales_item_id` = `c`.`sales_item_id` AND `c`.`sales_det_status` = 1 JOIN `material_tbl` `d` ON `b`.`material_id` = `d`.`material_id` JOIN `material_group_tbl` `e` ON `d`.`material_group_id` = `e`.`material_group_id` JOIN `outlet_tbl` `f` ON `a`.`outlet_id` = `f`.`outlet_id` JOIN `outlet_brand_tbl` `g` ON `f`.`outlet_id` = `g`.`outlet_id` AND `g`.`outlet_brand_status` = 1 JOIN `brand_tbl` `h` ON `g`.`brand_id` = `h`.`brand_id` AND `h`.`brand_name` = 'CHOOKS-TO-GO' AND c.sales_det_date = '" . $year . "-04-01' WHERE d.material_id = m.material_id AND c.sales_det_asp > 0 AND f.bc_id IN (" . $bc_id . ")) as apr_asp,
    
    	(SELECT AVG(c.sales_det_asp) FROM `sales_tbl` `a` JOIN `sales_item_tbl` `b` ON `a`.`sales_id` = `b`.`sales_id` AND `a`.`sales_status` = 1 AND `b`.`sales_item_status` = 1 AND `a`.`sales_year` = " . $year . " JOIN `sales_details_tbl` `c` ON `b`.`sales_item_id` = `c`.`sales_item_id` AND `c`.`sales_det_status` = 1 JOIN `material_tbl` `d` ON `b`.`material_id` = `d`.`material_id` JOIN `material_group_tbl` `e` ON `d`.`material_group_id` = `e`.`material_group_id` JOIN `outlet_tbl` `f` ON `a`.`outlet_id` = `f`.`outlet_id` JOIN `outlet_brand_tbl` `g` ON `f`.`outlet_id` = `g`.`outlet_id` AND `g`.`outlet_brand_status` = 1 JOIN `brand_tbl` `h` ON `g`.`brand_id` = `h`.`brand_id` AND `h`.`brand_name` = 'CHOOKS-TO-GO' AND c.sales_det_date = '" . $year . "-05-01' WHERE d.material_id = m.material_id AND c.sales_det_asp > 0 AND f.bc_id IN (" . $bc_id . ")) as may_asp,
    
    	(SELECT AVG(c.sales_det_asp) FROM `sales_tbl` `a` JOIN `sales_item_tbl` `b` ON `a`.`sales_id` = `b`.`sales_id` AND `a`.`sales_status` = 1 AND `b`.`sales_item_status` = 1 AND `a`.`sales_year` = " . $year . " JOIN `sales_details_tbl` `c` ON `b`.`sales_item_id` = `c`.`sales_item_id` AND `c`.`sales_det_status` = 1 JOIN `material_tbl` `d` ON `b`.`material_id` = `d`.`material_id` JOIN `material_group_tbl` `e` ON `d`.`material_group_id` = `e`.`material_group_id` JOIN `outlet_tbl` `f` ON `a`.`outlet_id` = `f`.`outlet_id` JOIN `outlet_brand_tbl` `g` ON `f`.`outlet_id` = `g`.`outlet_id` AND `g`.`outlet_brand_status` = 1 JOIN `brand_tbl` `h` ON `g`.`brand_id` = `h`.`brand_id` AND `h`.`brand_name` = 'CHOOKS-TO-GO' AND c.sales_det_date = '" . $year . "-06-01' WHERE d.material_id = m.material_id AND c.sales_det_asp > 0 AND f.bc_id IN (" . $bc_id . ")) as jun_asp,
    
    	(SELECT AVG(c.sales_det_asp) FROM `sales_tbl` `a` JOIN `sales_item_tbl` `b` ON `a`.`sales_id` = `b`.`sales_id` AND `a`.`sales_status` = 1 AND `b`.`sales_item_status` = 1 AND `a`.`sales_year` = " . $year . " JOIN `sales_details_tbl` `c` ON `b`.`sales_item_id` = `c`.`sales_item_id` AND `c`.`sales_det_status` = 1 JOIN `material_tbl` `d` ON `b`.`material_id` = `d`.`material_id` JOIN `material_group_tbl` `e` ON `d`.`material_group_id` = `e`.`material_group_id` JOIN `outlet_tbl` `f` ON `a`.`outlet_id` = `f`.`outlet_id` JOIN `outlet_brand_tbl` `g` ON `f`.`outlet_id` = `g`.`outlet_id` AND `g`.`outlet_brand_status` = 1 JOIN `brand_tbl` `h` ON `g`.`brand_id` = `h`.`brand_id` AND `h`.`brand_name` = 'CHOOKS-TO-GO' AND c.sales_det_date = '" . $year . "-07-01' WHERE d.material_id = m.material_id AND c.sales_det_asp > 0 AND f.bc_id IN (" . $bc_id . ")) as jul_asp,
    
    	(SELECT AVG(c.sales_det_asp) FROM `sales_tbl` `a` JOIN `sales_item_tbl` `b` ON `a`.`sales_id` = `b`.`sales_id` AND `a`.`sales_status` = 1 AND `b`.`sales_item_status` = 1 AND `a`.`sales_year` = " . $year . " JOIN `sales_details_tbl` `c` ON `b`.`sales_item_id` = `c`.`sales_item_id` AND `c`.`sales_det_status` = 1 JOIN `material_tbl` `d` ON `b`.`material_id` = `d`.`material_id` JOIN `material_group_tbl` `e` ON `d`.`material_group_id` = `e`.`material_group_id` JOIN `outlet_tbl` `f` ON `a`.`outlet_id` = `f`.`outlet_id` JOIN `outlet_brand_tbl` `g` ON `f`.`outlet_id` = `g`.`outlet_id` AND `g`.`outlet_brand_status` = 1 JOIN `brand_tbl` `h` ON `g`.`brand_id` = `h`.`brand_id` AND `h`.`brand_name` = 'CHOOKS-TO-GO' AND c.sales_det_date = '" . $year . "-08-01' WHERE d.material_id = m.material_id AND c.sales_det_asp > 0 AND f.bc_id IN (" . $bc_id . ")) as aug_asp,
    
    	(SELECT AVG(c.sales_det_asp) FROM `sales_tbl` `a` JOIN `sales_item_tbl` `b` ON `a`.`sales_id` = `b`.`sales_id` AND `a`.`sales_status` = 1 AND `b`.`sales_item_status` = 1 AND `a`.`sales_year` = " . $year . " JOIN `sales_details_tbl` `c` ON `b`.`sales_item_id` = `c`.`sales_item_id` AND `c`.`sales_det_status` = 1 JOIN `material_tbl` `d` ON `b`.`material_id` = `d`.`material_id` JOIN `material_group_tbl` `e` ON `d`.`material_group_id` = `e`.`material_group_id` JOIN `outlet_tbl` `f` ON `a`.`outlet_id` = `f`.`outlet_id` JOIN `outlet_brand_tbl` `g` ON `f`.`outlet_id` = `g`.`outlet_id` AND `g`.`outlet_brand_status` = 1 JOIN `brand_tbl` `h` ON `g`.`brand_id` = `h`.`brand_id` AND `h`.`brand_name` = 'CHOOKS-TO-GO' AND c.sales_det_date = '" . $year . "-09-01' WHERE d.material_id = m.material_id AND c.sales_det_asp > 0 AND f.bc_id IN (" . $bc_id . ")) as sep_asp,
    
    	(SELECT AVG(c.sales_det_asp) FROM `sales_tbl` `a` JOIN `sales_item_tbl` `b` ON `a`.`sales_id` = `b`.`sales_id` AND `a`.`sales_status` = 1 AND `b`.`sales_item_status` = 1 AND `a`.`sales_year` = " . $year . " JOIN `sales_details_tbl` `c` ON `b`.`sales_item_id` = `c`.`sales_item_id` AND `c`.`sales_det_status` = 1 JOIN `material_tbl` `d` ON `b`.`material_id` = `d`.`material_id` JOIN `material_group_tbl` `e` ON `d`.`material_group_id` = `e`.`material_group_id` JOIN `outlet_tbl` `f` ON `a`.`outlet_id` = `f`.`outlet_id` JOIN `outlet_brand_tbl` `g` ON `f`.`outlet_id` = `g`.`outlet_id` AND `g`.`outlet_brand_status` = 1 JOIN `brand_tbl` `h` ON `g`.`brand_id` = `h`.`brand_id` AND `h`.`brand_name` = 'CHOOKS-TO-GO' AND c.sales_det_date = '" . $year . "-10-01' WHERE d.material_id = m.material_id AND c.sales_det_asp > 0 AND f.bc_id IN (" . $bc_id . ")) as oct_asp,
    
    	(SELECT AVG(c.sales_det_asp) FROM `sales_tbl` `a` JOIN `sales_item_tbl` `b` ON `a`.`sales_id` = `b`.`sales_id` AND `a`.`sales_status` = 1 AND `b`.`sales_item_status` = 1 AND `a`.`sales_year` = " . $year . " JOIN `sales_details_tbl` `c` ON `b`.`sales_item_id` = `c`.`sales_item_id` AND `c`.`sales_det_status` = 1 JOIN `material_tbl` `d` ON `b`.`material_id` = `d`.`material_id` JOIN `material_group_tbl` `e` ON `d`.`material_group_id` = `e`.`material_group_id` JOIN `outlet_tbl` `f` ON `a`.`outlet_id` = `f`.`outlet_id` JOIN `outlet_brand_tbl` `g` ON `f`.`outlet_id` = `g`.`outlet_id` AND `g`.`outlet_brand_status` = 1 JOIN `brand_tbl` `h` ON `g`.`brand_id` = `h`.`brand_id` AND `h`.`brand_name` = 'CHOOKS-TO-GO' AND c.sales_det_date = '" . $year . "-11-01' WHERE d.material_id = m.material_id AND c.sales_det_asp > 0 AND f.bc_id IN (" . $bc_id . ")) as nov_asp,
    
    	(SELECT AVG(c.sales_det_asp) FROM `sales_tbl` `a` JOIN `sales_item_tbl` `b` ON `a`.`sales_id` = `b`.`sales_id` AND `a`.`sales_status` = 1 AND `b`.`sales_item_status` = 1 AND `a`.`sales_year` = " . $year . " JOIN `sales_details_tbl` `c` ON `b`.`sales_item_id` = `c`.`sales_item_id` AND `c`.`sales_det_status` = 1 JOIN `material_tbl` `d` ON `b`.`material_id` = `d`.`material_id` JOIN `material_group_tbl` `e` ON `d`.`material_group_id` = `e`.`material_group_id` JOIN `outlet_tbl` `f` ON `a`.`outlet_id` = `f`.`outlet_id` JOIN `outlet_brand_tbl` `g` ON `f`.`outlet_id` = `g`.`outlet_id` AND `g`.`outlet_brand_status` = 1 JOIN `brand_tbl` `h` ON `g`.`brand_id` = `h`.`brand_id` AND `h`.`brand_name` = 'CHOOKS-TO-GO' AND c.sales_det_date = '" . $year . "-12-01' WHERE d.material_id = m.material_id AND c.sales_det_asp > 0 AND f.bc_id IN (" . $bc_id . ")) as dec_asp
    
		FROM material_size_tbl m, brand_tbl n WHERE m.brand_id = n.brand_id AND m.material_size_status = 1 AND n.brand_name = 'CHOOKS-TO-GO' AND m.is_orc = 1) as asp_tbl

		WHERE o.material_id = asp_tbl.material_id AND p.size_id = asp_tbl.size_id GROUP BY p.size_id");

		$data['ctg_reg1'] = $data['ctg_reg2'] = $data['ctg_reg3'] = $data['ctg_reg4'] = $data['ctg_reg5'] = $data['ctg_reg6'] = $data['ctg_reg7'] = $data['ctg_reg8'] = $data['ctg_reg9'] = $data['ctg_reg10'] = $data['ctg_reg11'] = $data['ctg_reg12'] = $data['ctg_reg_total'] = $data['ctg_reg_count'] = $data['ctg_reg_avg'] = $data['ctg_reg_min'] = $data['ctg_reg_max'] = 0;

		$data['ctg_jbo1'] = $data['ctg_jbo2'] = $data['ctg_jbo3'] = $data['ctg_jbo4'] = $data['ctg_jbo5'] = $data['ctg_jbo6'] = $data['ctg_jbo7'] = $data['ctg_jbo8'] = $data['ctg_jbo9'] = $data['ctg_jbo10'] = $data['ctg_jbo11'] = $data['ctg_jbo12'] = $data['ctg_jbo_total'] = $data['ctg_jbo_count'] = $data['ctg_jbo_avg'] = $data['ctg_jbo_min'] = $data['ctg_jbo_max'] = 0;

		$data['ctg_ss1'] = $data['ctg_ss2'] = $data['ctg_ss3'] = $data['ctg_ss4'] = $data['ctg_ss5'] = $data['ctg_ss6'] = $data['ctg_ss7'] = $data['ctg_ss8'] = $data['ctg_ss9'] = $data['ctg_ss10'] = $data['ctg_ss11'] = $data['ctg_ss12'] = $data['ctg_ss_total'] = $data['ctg_ss_count'] = $data['ctg_ss_avg'] = $data['ctg_ss_min'] = $data['ctg_ss_max'] = 0;

		$data['ctg_bt1'] = $data['ctg_bt2'] = $data['ctg_bt3'] = $data['ctg_bt4'] = $data['ctg_bt5'] = $data['ctg_bt6'] = $data['ctg_bt7'] = $data['ctg_bt8'] = $data['ctg_bt9'] = $data['ctg_bt10'] = $data['ctg_bt11'] = $data['ctg_bt12'] = $data['ctg_bt_total'] = $data['ctg_bt_count'] = $data['ctg_bt_avg'] = $data['ctg_bt_min'] = $data['ctg_bt_max'] = 0;

		$data['ctg_half1'] = $data['ctg_half2'] = $data['ctg_half3'] = $data['ctg_half4'] = $data['ctg_half5'] = $data['ctg_half6'] = $data['ctg_half7'] = $data['ctg_half8'] = $data['ctg_half9'] = $data['ctg_half10'] = $data['ctg_half11'] = $data['ctg_half12'] = $data['ctg_half_total'] = $data['ctg_half_count'] = $data['ctg_half_avg'] = $data['ctg_half_min'] = $data['ctg_half_max'] = 0;

		foreach($get_ctg_orc as $row){

			$orc_size = $row->size_name;
			$orc_jan = $row->orc_jan;
			$orc_feb = $row->orc_feb;
			$orc_mar = $row->orc_mar;
			$orc_apr = $row->orc_apr;
			$orc_may = $row->orc_may;
			$orc_jun = $row->orc_jun;
			$orc_jul = $row->orc_jul;
			$orc_aug = $row->orc_aug;
			$orc_sep = $row->orc_sep;
			$orc_oct = $row->orc_oct;
			$orc_nov = $row->orc_nov;
			$orc_dec = $row->orc_dec;
			$orc_min = min($orc_jan, $orc_feb, $orc_mar, $orc_apr, $orc_may, $orc_jun, $orc_jul, $orc_aug, $orc_sep, $orc_oct, $orc_nov, $orc_dec);
			$orc_max = max($orc_jan, $orc_feb, $orc_mar, $orc_apr, $orc_may, $orc_jun, $orc_jul, $orc_aug, $orc_sep, $orc_oct, $orc_nov, $orc_dec);
			$orc_avg = ($orc_jan + $orc_feb + $orc_mar + $orc_apr + $orc_may + $orc_jun + $orc_jul + $orc_aug + $orc_sep + $orc_oct + $orc_nov + $orc_dec) / 12;

			if($orc_size == "REGULAR"){
				$data['ctg_reg1'] = $orc_jan;
				$data['ctg_reg2'] = $orc_feb;
				$data['ctg_reg3'] = $orc_mar;
				$data['ctg_reg4'] = $orc_apr;
				$data['ctg_reg5'] = $orc_may;
				$data['ctg_reg6'] = $orc_jun;
				$data['ctg_reg7'] = $orc_jul;
				$data['ctg_reg8'] = $orc_aug;
				$data['ctg_reg9'] = $orc_sep;
				$data['ctg_reg10'] = $orc_oct;
				$data['ctg_reg11'] = $orc_nov;
				$data['ctg_reg12'] = $orc_dec;
				$data['ctg_reg_avg'] = $orc_avg;
				$data['ctg_reg_min'] = $orc_min;
				$data['ctg_reg_max'] = $orc_max;

				$reg_prev_year2 = $this->admin->get_data('comparative_price_tbl', 'comp_price_product = "ORC - REGULAR" AND comp_price_segment = "CTG" AND comp_price_year = ' . ($year - 2) . ' AND bc_id IN (' . $bc_id . ')', 
			FALSE, 'AVG(comp_price_jan) as jan_price, AVG(comp_price_feb) as feb_price, AVG(comp_price_mar) as mar_price, AVG(comp_price_apr) as apr_price, AVG(comp_price_may) as may_price, AVG(comp_price_jun) as jun_price, AVG(comp_price_jul) as jul_price, AVG(comp_price_aug) as aug_price, AVG(comp_price_sep) as sep_price, AVG(comp_price_oct) as oct_price, AVG(comp_price_nov) as nov_price, AVG(comp_price_dec) as dec_price');
				foreach($reg_prev_year2 as $row){
					$data['ctg_reg1_year2'] = $row->jan_price;
					$data['ctg_reg2_year2'] = $row->feb_price;
					$data['ctg_reg3_year2'] = $row->mar_price;
					$data['ctg_reg4_year2'] = $row->apr_price;
					$data['ctg_reg5_year2'] = $row->may_price;
					$data['ctg_reg6_year2'] = $row->jun_price;
					$data['ctg_reg7_year2'] = $row->jul_price;
					$data['ctg_reg8_year2'] = $row->aug_price;
					$data['ctg_reg9_year2'] = $row->sep_price;
					$data['ctg_reg10_year2'] = $row->oct_price;
					$data['ctg_reg11_year2'] = $row->nov_price;
					$data['ctg_reg12_year2'] = $row->dec_price;
					$data['ctg_reg_avg_year2'] = ($row->jan_price + $row->feb_price + $row->mar_price + $row->apr_price + $row->may_price + $row->jun_price + $row->jul_price + $row->aug_price + $row->sep_price + $row->oct_price + $row->nov_price + $row->dec_price) / 12;

					$data['ctg_reg_min_year2'] = min($row->jan_price, $row->feb_price, $row->mar_price, $row->apr_price, $row->may_price, $row->jun_price, $row->jul_price, $row->aug_price, $row->sep_price, $row->oct_price, $row->nov_price, $row->dec_price);

					$data['ctg_reg_max_year2'] = max($row->jan_price, $row->feb_price, $row->mar_price, $row->apr_price, $row->may_price, $row->jun_price, $row->jul_price, $row->aug_price, $row->sep_price, $row->oct_price, $row->nov_price, $row->dec_price);
				}

				$reg_prev_year1 = $this->admin->get_data('comparative_price_tbl', 'comp_price_product = "ORC - REGULAR" AND comp_price_segment = "CTG" AND comp_price_year = ' . ($year - 1) . ' AND bc_id IN (' . $bc_id . ')', 
			FALSE, 'AVG(comp_price_jan) as jan_price, AVG(comp_price_feb) as feb_price, AVG(comp_price_mar) as mar_price, AVG(comp_price_apr) as apr_price, AVG(comp_price_may) as may_price, AVG(comp_price_jun) as jun_price, AVG(comp_price_jul) as jul_price, AVG(comp_price_aug) as aug_price, AVG(comp_price_sep) as sep_price, AVG(comp_price_oct) as oct_price, AVG(comp_price_nov) as nov_price, AVG(comp_price_dec) as dec_price');

				foreach($reg_prev_year1 as $row){
					$data['ctg_reg1_year1'] = $row->jan_price;
					$data['ctg_reg2_year1'] = $row->feb_price;
					$data['ctg_reg3_year1'] = $row->mar_price;
					$data['ctg_reg4_year1'] = $row->apr_price;
					$data['ctg_reg5_year1'] = $row->may_price;
					$data['ctg_reg6_year1'] = $row->jun_price;
					$data['ctg_reg7_year1'] = $row->jul_price;
					$data['ctg_reg8_year1'] = $row->aug_price;
					$data['ctg_reg9_year1'] = $row->sep_price;
					$data['ctg_reg10_year1'] = $row->oct_price;
					$data['ctg_reg11_year1'] = $row->nov_price;
					$data['ctg_reg12_year1'] = $row->dec_price;
					$data['ctg_reg_avg_year1'] = ($row->jan_price + $row->feb_price + $row->mar_price + $row->apr_price + $row->may_price + $row->jun_price + $row->jul_price + $row->aug_price + $row->sep_price + $row->oct_price + $row->nov_price + $row->dec_price) / 12;

					$data['ctg_reg_min_year1'] = min($row->jan_price, $row->feb_price, $row->mar_price, $row->apr_price, $row->may_price, $row->jun_price, $row->jul_price, $row->aug_price, $row->sep_price, $row->oct_price, $row->nov_price, $row->dec_price);

					$data['ctg_reg_max_year1'] = max($row->jan_price, $row->feb_price, $row->mar_price, $row->apr_price, $row->may_price, $row->jun_price, $row->jul_price, $row->aug_price, $row->sep_price, $row->oct_price, $row->nov_price, $row->dec_price);
				}

			}elseif($orc_size == "JUMBO"){
				$data['ctg_jbo1'] = $orc_jan;
				$data['ctg_jbo2'] = $orc_feb;
				$data['ctg_jbo3'] = $orc_mar;
				$data['ctg_jbo4'] = $orc_apr;
				$data['ctg_jbo5'] = $orc_may;
				$data['ctg_jbo6'] = $orc_jun;
				$data['ctg_jbo7'] = $orc_jul;
				$data['ctg_jbo8'] = $orc_aug;
				$data['ctg_jbo9'] = $orc_sep;
				$data['ctg_jbo10'] = $orc_oct;
				$data['ctg_jbo11'] = $orc_nov;
				$data['ctg_jbo12'] = $orc_dec;
				$data['ctg_jbo_avg'] = $orc_avg;
				$data['ctg_jbo_min'] = $orc_min;
				$data['ctg_jbo_max'] = $orc_max;

				$jbo_prev_year2 = $this->admin->get_data('comparative_price_tbl', 'comp_price_product = "ORC - JUMBO" AND comp_price_segment = "CTG" AND comp_price_year = ' . ($year - 2) . ' AND bc_id IN (' . $bc_id . ')', 
			FALSE, 'AVG(comp_price_jan) as jan_price, AVG(comp_price_feb) as feb_price, AVG(comp_price_mar) as mar_price, AVG(comp_price_apr) as apr_price, AVG(comp_price_may) as may_price, AVG(comp_price_jun) as jun_price, AVG(comp_price_jul) as jul_price, AVG(comp_price_aug) as aug_price, AVG(comp_price_sep) as sep_price, AVG(comp_price_oct) as oct_price, AVG(comp_price_nov) as nov_price, AVG(comp_price_dec) as dec_price');

				foreach($jbo_prev_year2 as $row){
					$data['ctg_jbo1_year2'] = $row->jan_price;
					$data['ctg_jbo2_year2'] = $row->feb_price;
					$data['ctg_jbo3_year2'] = $row->mar_price;
					$data['ctg_jbo4_year2'] = $row->apr_price;
					$data['ctg_jbo5_year2'] = $row->may_price;
					$data['ctg_jbo6_year2'] = $row->jun_price;
					$data['ctg_jbo7_year2'] = $row->jul_price;
					$data['ctg_jbo8_year2'] = $row->aug_price;
					$data['ctg_jbo9_year2'] = $row->sep_price;
					$data['ctg_jbo10_year2'] = $row->oct_price;
					$data['ctg_jbo11_year2'] = $row->nov_price;
					$data['ctg_jbo12_year2'] = $row->dec_price;
					$data['ctg_jbo_year2_avg'] = ($row->jan_price + $row->feb_price + $row->mar_price + $row->apr_price + $row->may_price + $row->jun_price + $row->jul_price + $row->aug_price + $row->sep_price + $row->oct_price + $row->nov_price + $row->dec_price) / 12;

					$data['ctg_jbo_year2_min'] = min($row->jan_price, $row->feb_price, $row->mar_price, $row->apr_price, $row->may_price, $row->jun_price, $row->jul_price, $row->aug_price, $row->sep_price, $row->oct_price, $row->nov_price, $row->dec_price);

					$data['ctg_jbo_year2_max'] = max($row->jan_price, $row->feb_price, $row->mar_price, $row->apr_price, $row->may_price, $row->jun_price, $row->jul_price, $row->aug_price, $row->sep_price, $row->oct_price, $row->nov_price, $row->dec_price);
				}

				$jbo_prev_year1 = $this->admin->get_data('comparative_price_tbl', 'comp_price_product = "ORC - JUMBO" AND comp_price_segment = "CTG" AND  comp_price_year = ' . ($year - 1) . ' AND bc_id IN (' . $bc_id . ')', 
			FALSE, 'AVG(comp_price_jan) as jan_price, AVG(comp_price_feb) as feb_price, AVG(comp_price_mar) as mar_price, AVG(comp_price_apr) as apr_price, AVG(comp_price_may) as may_price, AVG(comp_price_jun) as jun_price, AVG(comp_price_jul) as jul_price, AVG(comp_price_aug) as aug_price, AVG(comp_price_sep) as sep_price, AVG(comp_price_oct) as oct_price, AVG(comp_price_nov) as nov_price, AVG(comp_price_dec) as dec_price');

				foreach($jbo_prev_year1 as $row){
					$data['ctg_jbo1_year1'] = $row->jan_price;
					$data['ctg_jbo2_year1'] = $row->feb_price;
					$data['ctg_jbo3_year1'] = $row->mar_price;
					$data['ctg_jbo4_year1'] = $row->apr_price;
					$data['ctg_jbo5_year1'] = $row->may_price;
					$data['ctg_jbo6_year1'] = $row->jun_price;
					$data['ctg_jbo7_year1'] = $row->jul_price;
					$data['ctg_jbo8_year1'] = $row->aug_price;
					$data['ctg_jbo9_year1'] = $row->sep_price;
					$data['ctg_jbo10_year1'] = $row->oct_price;
					$data['ctg_jbo11_year1'] = $row->nov_price;
					$data['ctg_jbo12_year1'] = $row->dec_price;
					$data['ctg_jbo_year1_avg'] = ($row->jan_price + $row->feb_price + $row->mar_price + $row->apr_price + $row->may_price + $row->jun_price + $row->jul_price + $row->aug_price + $row->sep_price + $row->oct_price + $row->nov_price + $row->dec_price) / 12;

					$data['ctg_jbo_year1_min'] = min($row->jan_price, $row->feb_price, $row->mar_price, $row->apr_price, $row->may_price, $row->jun_price, $row->jul_price, $row->aug_price, $row->sep_price, $row->oct_price, $row->nov_price, $row->dec_price);

					$data['ctg_jbo_year1_max'] = max($row->jan_price, $row->feb_price, $row->mar_price, $row->apr_price, $row->may_price, $row->jun_price, $row->jul_price, $row->aug_price, $row->sep_price, $row->oct_price, $row->nov_price, $row->dec_price);
				}

			}elseif($orc_size == "SUPERSIZE"){
				$data['ctg_ss1'] = $orc_jan;
				$data['ctg_ss2'] = $orc_feb;
				$data['ctg_ss3'] = $orc_mar;
				$data['ctg_ss4'] = $orc_apr;
				$data['ctg_ss5'] = $orc_may;
				$data['ctg_ss6'] = $orc_jun;
				$data['ctg_ss7'] = $orc_jul;
				$data['ctg_ss8'] = $orc_aug;
				$data['ctg_ss9'] = $orc_sep;
				$data['ctg_ss10'] = $orc_oct;
				$data['ctg_ss11'] = $orc_nov;
				$data['ctg_ss12'] = $orc_dec;
				$data['ctg_ss_avg'] = $orc_avg;
				$data['ctg_ss_min'] = $orc_min;
				$data['ctg_ss_max'] = $orc_max;

				$ss_prev_year2 = $this->admin->get_data('comparative_price_tbl', 'comp_price_product = "ORC - SUPERSIZE" AND comp_price_segment = "CTG" AND comp_price_year = ' . ($year - 2) . ' AND bc_id IN (' . $bc_id . ')', 
			FALSE, 'AVG(comp_price_jan) as jan_price, AVG(comp_price_feb) as feb_price, AVG(comp_price_mar) as mar_price, AVG(comp_price_apr) as apr_price, AVG(comp_price_may) as may_price, AVG(comp_price_jun) as jun_price, AVG(comp_price_jul) as jul_price, AVG(comp_price_aug) as aug_price, AVG(comp_price_sep) as sep_price, AVG(comp_price_oct) as oct_price, AVG(comp_price_nov) as nov_price, AVG(comp_price_dec) as dec_price');

				foreach($ss_prev_year2 as $row){
					$data['ctg_ss1_year2'] = $row->jan_price;
					$data['ctg_ss2_year2'] = $row->feb_price;
					$data['ctg_ss3_year2'] = $row->mar_price;
					$data['ctg_ss4_year2'] = $row->apr_price;
					$data['ctg_ss5_year2'] = $row->may_price;
					$data['ctg_ss6_year2'] = $row->jun_price;
					$data['ctg_ss7_year2'] = $row->jul_price;
					$data['ctg_ss8_year2'] = $row->aug_price;
					$data['ctg_ss9_year2'] = $row->sep_price;
					$data['ctg_ss10_year2'] = $row->oct_price;
					$data['ctg_ss11_year2'] = $row->nov_price;
					$data['ctg_ss12_year2'] = $row->dec_price;
					$data['ctg_ss_avg_year2'] = ($row->jan_price + $row->feb_price + $row->mar_price + $row->apr_price + $row->may_price + $row->jun_price + $row->jul_price + $row->aug_price + $row->sep_price + $row->oct_price + $row->nov_price + $row->dec_price) / 12;

					$data['ctg_ss_min_year2'] = min($row->jan_price, $row->feb_price, $row->mar_price, $row->apr_price, $row->may_price, $row->jun_price, $row->jul_price, $row->aug_price, $row->sep_price, $row->oct_price, $row->nov_price, $row->dec_price);

					$data['ctg_ss_max_year2'] = max($row->jan_price, $row->feb_price, $row->mar_price, $row->apr_price, $row->may_price, $row->jun_price, $row->jul_price, $row->aug_price, $row->sep_price, $row->oct_price, $row->nov_price, $row->dec_price);
				}

				$ss_prev_year1 = $this->admin->get_data('comparative_price_tbl', 'comp_price_product = "ORC - SUPERSIZE" AND comp_price_segment = "CTG" AND comp_price_year = ' . ($year - 1) . ' AND bc_id IN (' .	$bc_id . ')', 
			FALSE, 'AVG(comp_price_jan) as jan_price, AVG(comp_price_feb) as feb_price, AVG(comp_price_mar) as mar_price, AVG(comp_price_apr) as apr_price, AVG(comp_price_may) as may_price, AVG(comp_price_jun) as jun_price, AVG(comp_price_jul) as jul_price, AVG(comp_price_aug) as aug_price, AVG(comp_price_sep) as sep_price, AVG(comp_price_oct) as oct_price, AVG(comp_price_nov) as nov_price, AVG(comp_price_dec) as dec_price');

				foreach($ss_prev_year1 as $row){
					$data['ctg_ss1_year1'] = $row->jan_price;
					$data['ctg_ss2_year1'] = $row->feb_price;
					$data['ctg_ss3_year1'] = $row->mar_price;
					$data['ctg_ss4_year1'] = $row->apr_price;
					$data['ctg_ss5_year1'] = $row->may_price;
					$data['ctg_ss6_year1'] = $row->jun_price;
					$data['ctg_ss7_year1'] = $row->jul_price;
					$data['ctg_ss8_year1'] = $row->aug_price;
					$data['ctg_ss9_year1'] = $row->sep_price;
					$data['ctg_ss10_year1'] = $row->oct_price;
					$data['ctg_ss11_year1'] = $row->nov_price;
					$data['ctg_ss12_year1'] = $row->dec_price;
					$data['ctg_ss_avg_year1'] = ($row->jan_price + $row->feb_price + $row->mar_price + $row->apr_price + $row->may_price + $row->jun_price + $row->jul_price + $row->aug_price + $row->sep_price + $row->oct_price + $row->nov_price + $row->dec_price) / 12;

					$data['ctg_ss_min_year1'] = min($row->jan_price, $row->feb_price, $row->mar_price, $row->apr_price, $row->may_price, $row->jun_price, $row->jul_price, $row->aug_price, $row->sep_price, $row->oct_price, $row->nov_price, $row->dec_price);

					$data['ctg_ss_max_year1'] = max($row->jan_price, $row->feb_price, $row->mar_price, $row->apr_price, $row->may_price, $row->jun_price, $row->jul_price, $row->aug_price, $row->sep_price, $row->oct_price, $row->nov_price, $row->dec_price);
				}

			}elseif($orc_size == "BIGTIME"){
				$data['ctg_bt1'] = $orc_jan;
				$data['ctg_bt2'] = $orc_feb;
				$data['ctg_bt3'] = $orc_mar;
				$data['ctg_bt4'] = $orc_apr;
				$data['ctg_bt5'] = $orc_may;
				$data['ctg_bt6'] = $orc_jun;
				$data['ctg_bt7'] = $orc_jul;
				$data['ctg_bt8'] = $orc_aug;
				$data['ctg_bt9'] = $orc_sep;
				$data['ctg_bt10'] = $orc_oct;
				$data['ctg_bt11'] = $orc_nov;
				$data['ctg_bt12'] = $orc_dec;
				$data['ctg_bt_avg'] = $orc_avg;
				$data['cctg_bt_min'] = $orc_min;
				$data['ctg_bt_max'] = $orc_max;


				$bt_prev_year2 = $this->admin->get_data('comparative_price_tbl', 'comp_price_product = "ORC - BIGTIME" AND comp_price_segment = "CTG" AND comp_price_year = ' . ($year - 2) . ' AND bc_id IN (' . $bc_id . ')',
			FALSE, 'AVG(comp_price_jan) as jan_price, AVG(comp_price_feb) as feb_price, AVG(comp_price_mar) as mar_price, AVG(comp_price_apr) as apr_price, AVG(comp_price_may) as may_price, AVG(comp_price_jun) as jun_price, AVG(comp_price_jul) as jul_price, AVG(comp_price_aug) as aug_price, AVG(comp_price_sep) as sep_price, AVG(comp_price_oct) as oct_price, AVG(comp_price_nov) as nov_price, AVG(comp_price_dec) as dec_price');

				foreach($bt_prev_year2 as $row){
					$data['ctg_bt1_year2'] = $row->jan_price;
					$data['ctg_bt2_year2'] = $row->feb_price;
					$data['ctg_bt3_year2'] = $row->mar_price;
					$data['ctg_bt4_year2'] = $row->apr_price;
					$data['ctg_bt5_year2'] = $row->may_price;
					$data['ctg_bt6_year2'] = $row->jun_price;
					$data['ctg_bt7_year2'] = $row->jul_price;
					$data['ctg_bt8_year2'] = $row->aug_price;
					$data['ctg_bt9_year2'] = $row->sep_price;
					$data['ctg_bt10_year2'] = $row->oct_price;
					$data['ctg_bt11_year2'] = $row->nov_price;
					$data['ctg_bt12_year2'] = $row->dec_price;
					$data['ctg_bt_avg_year2'] = ($row->jan_price + $row->feb_price + $row->mar_price + $row->apr_price + $row->may_price + $row->jun_price + $row->jul_price + $row->aug_price + $row->sep_price + $row->oct_price + $row->nov_price + $row->dec_price) / 12;

					$data['ctg_bt_min_year2'] = min($row->jan_price, $row->feb_price, $row->mar_price, $row->apr_price, $row->may_price, $row->jun_price, $row->jul_price, $row->aug_price, $row->sep_price, $row->oct_price, $row->nov_price, $row->dec_price);

					$data['ctg_bt_max_year2'] = max($row->jan_price, $row->feb_price, $row->mar_price, $row->apr_price, $row->may_price, $row->jun_price, $row->jul_price, $row->aug_price, $row->sep_price, $row->oct_price, $row->nov_price, $row->dec_price);
				}

				$bt_prev_year1 = $this->admin->get_data('comparative_price_tbl', 'comp_price_product = "ORC - BIGTIME" AND comp_price_segment = "CTG" AND comp_price_year = ' . ($year - 1) . ' AND bc_id IN (' . $bc_id . ')', 
			FALSE, 'AVG(comp_price_jan) as jan_price, AVG(comp_price_feb) as feb_price, AVG(comp_price_mar) as mar_price, AVG(comp_price_apr) as apr_price, AVG(comp_price_may) as may_price, AVG(comp_price_jun) as jun_price, AVG(comp_price_jul) as jul_price, AVG(comp_price_aug) as aug_price, AVG(comp_price_sep) as sep_price, AVG(comp_price_oct) as oct_price, AVG(comp_price_nov) as nov_price, AVG(comp_price_dec) as dec_price');

				foreach($bt_prev_year1 as $row){
					$data['ctg_bt1_year1'] = $row->jan_price;
					$data['ctg_bt2_year1'] = $row->feb_price;
					$data['ctg_bt3_year1'] = $row->mar_price;
					$data['ctg_bt4_year1'] = $row->apr_price;
					$data['ctg_bt5_year1'] = $row->may_price;
					$data['ctg_bt6_year1'] = $row->jun_price;
					$data['ctg_bt7_year1'] = $row->jul_price;
					$data['ctg_bt8_year1'] = $row->aug_price;
					$data['ctg_bt9_year1'] = $row->sep_price;
					$data['ctg_bt10_year1'] = $row->oct_price;
					$data['ctg_bt11_year1'] = $row->nov_price;
					$data['ctg_bt12_year1'] = $row->dec_price;
					$data['ctg_bt_avg_year1'] = ($row->jan_price + $row->feb_price + $row->mar_price + $row->apr_price + $row->may_price + $row->jun_price + $row->jul_price + $row->aug_price + $row->sep_price + $row->oct_price + $row->nov_price + $row->dec_price) / 12;

					$data['ctg_bt_min_year1'] = min($row->jan_price, $row->feb_price, $row->mar_price, $row->apr_price, $row->may_price, $row->jun_price, $row->jul_price, $row->aug_price, $row->sep_price, $row->oct_price, $row->nov_price, $row->dec_price);

					$data['ctg_bt_max_year1'] = max($row->jan_price, $row->feb_price, $row->mar_price, $row->apr_price, $row->may_price, $row->jun_price, $row->jul_price, $row->aug_price, $row->sep_price, $row->oct_price, $row->nov_price, $row->dec_price);
				}

			}elseif($orc_size == "HALF"){
				$data['ctg_half1'] = $orc_jan;
				$data['ctg_half2'] = $orc_feb;
				$data['ctg_half3'] = $orc_mar;
				$data['ctg_half4'] = $orc_apr;
				$data['ctg_half5'] = $orc_may;
				$data['ctg_half6'] = $orc_jun;
				$data['ctg_half7'] = $orc_jul;
				$data['ctg_half8'] = $orc_aug;
				$data['ctg_half9'] = $orc_sep;
				$data['ctg_half10'] = $orc_oct;
				$data['ctg_half11'] = $orc_nov;
				$data['ctg_half12'] = $orc_dec;
				$data['ctg_half_avg'] = $orc_avg;
				$data['ctg_half_min'] = $orc_min;
				$data['ctg_half_max'] = $orc_max;

				$half_prev_year2 = $this->admin->get_data('comparative_price_tbl', 'comp_price_product = "ORC - HALF" AND comp_price_segment = "CTG" AND comp_price_year = ' . ($year - 2) . ' AND bc_id IN (' . $bc_id . ')',
			FALSE, 'AVG(comp_price_jan) as jan_price, AVG(comp_price_feb) as feb_price, AVG(comp_price_mar) as mar_price, AVG(comp_price_apr) as apr_price, AVG(comp_price_may) as may_price, AVG(comp_price_jun) as jun_price, AVG(comp_price_jul) as jul_price, AVG(comp_price_aug) as aug_price, AVG(comp_price_sep) as sep_price, AVG(comp_price_oct) as oct_price, AVG(comp_price_nov) as nov_price, AVG(comp_price_dec) as dec_price');

				foreach($half_prev_year2 as $row){
					$data['ctg_half1_year2'] = $row->jan_price;
					$data['ctg_half2_year2'] = $row->feb_price;
					$data['ctg_half3_year2'] = $row->mar_price;
					$data['ctg_half4_year2'] = $row->apr_price;
					$data['ctg_half5_year2'] = $row->may_price;
					$data['ctg_half6_year2'] = $row->jun_price;
					$data['ctg_half7_year2'] = $row->jul_price;
					$data['ctg_half8_year2'] = $row->aug_price;
					$data['ctg_half9_year2'] = $row->sep_price;
					$data['ctg_half10_year2'] = $row->oct_price;
					$data['ctg_half11_year2'] = $row->nov_price;
					$data['ctg_half12_year2'] = $row->dec_price;
					$data['ctg_half_avg_year2'] = ($row->jan_price + $row->feb_price + $row->mar_price + $row->apr_price + $row->may_price + $row->jun_price + $row->jul_price + $row->aug_price + $row->sep_price + $row->oct_price + $row->nov_price + $row->dec_price) / 12;

					$data['ctg_half_min_year2'] = min($row->jan_price, $row->feb_price, $row->mar_price, $row->apr_price, $row->may_price, $row->jun_price, $row->jul_price, $row->aug_price, $row->sep_price, $row->oct_price, $row->nov_price, $row->dec_price);

					$data['ctg_half_max_year2'] = max($row->jan_price, $row->feb_price, $row->mar_price, $row->apr_price, $row->may_price, $row->jun_price, $row->jul_price, $row->aug_price, $row->sep_price, $row->oct_price, $row->nov_price, $row->dec_price);
				}

				$half_prev_year1 = $this->admin->get_data('comparative_price_tbl', 'comp_price_product = "ORC - HALF" AND comp_price_segment = "CTG" AND comp_price_year = ' . ($year - 1) . ' AND bc_id IN (' . $bc_id . ')', 
			FALSE, 'AVG(comp_price_jan) as jan_price, AVG(comp_price_feb) as feb_price, AVG(comp_price_mar) as mar_price, AVG(comp_price_apr) as apr_price, AVG(comp_price_may) as may_price, AVG(comp_price_jun) as jun_price, AVG(comp_price_jul) as jul_price, AVG(comp_price_aug) as aug_price, AVG(comp_price_sep) as sep_price, AVG(comp_price_oct) as oct_price, AVG(comp_price_nov) as nov_price, AVG(comp_price_dec) as dec_price');

				foreach($half_prev_year1 as $row){
					$data['ctg_half1_year1'] = $row->jan_price;
					$data['ctg_half2_year1'] = $row->feb_price;
					$data['ctg_half3_year1'] = $row->mar_price;
					$data['ctg_half4_year1'] = $row->apr_price;
					$data['ctg_half5_year1'] = $row->may_price;
					$data['ctg_half6_year1'] = $row->jun_price;
					$data['ctg_half7_year1'] = $row->jul_price;
					$data['ctg_half8_year1'] = $row->aug_price;
					$data['ctg_half9_year1'] = $row->sep_price;
					$data['ctg_half10_year1'] = $row->oct_price;
					$data['ctg_half11_year1'] = $row->nov_price;
					$data['ctg_half12_year1'] = $row->dec_price;
					$data['ctg_half_avg_year1'] = ($row->jan_price + $row->feb_price + $row->mar_price + $row->apr_price + $row->may_price + $row->jun_price + $row->jul_price + $row->aug_price + $row->sep_price + $row->oct_price + $row->nov_price + $row->dec_price) / 12;

					$data['ctg_half_min_year1'] = min($row->jan_price, $row->feb_price, $row->mar_price, $row->apr_price, $row->may_price, $row->jun_price, $row->jul_price, $row->aug_price, $row->sep_price, $row->oct_price, $row->nov_price, $row->dec_price);

					$data['ctg_half_max_year1'] = max($row->jan_price, $row->feb_price, $row->mar_price, $row->apr_price, $row->may_price, $row->jun_price, $row->jul_price, $row->aug_price, $row->sep_price, $row->oct_price, $row->nov_price, $row->dec_price);
				}
			}
		}

		/*CTG Liempo*/
		$join_liempo = array(
			'sales_item_tbl b' => 'a.sales_id = b.sales_id AND a.sales_status = 1 AND b.sales_item_status = 1 AND a.sales_year = ' . $year,
			'sales_details_tbl c' => 'b.sales_item_id = c.sales_item_id AND c.sales_det_status = 1',
			'material_tbl d' => 'b.material_id = d.material_id',
			'material_group_tbl e' => 'd.material_group_id = e.material_group_id AND e.material_group_name = "LIEMPO"',
			'outlet_tbl f' => 'a.outlet_id = f.outlet_id AND f.bc_id = ' . $bc_id,
			'outlet_brand_tbl g' => 'f.outlet_id = g.outlet_id AND g.outlet_brand_status = 1',
			'brand_tbl h' => 'g.brand_id  = h.brand_id AND h.brand_name = \'CHOOKS-TO-GO\''
		);

		$get_liempo = $this->admin->get_join('sales_tbl a', $join_liempo, FALSE, FALSE, 'YEAR(c.sales_det_date), MONTH(c.sales_det_date)', 'AVG(c.sales_det_asp) as asp, MONTH(c.sales_det_date) asp_date');

		$data['liempo1'] = 0;
		$data['liempo2'] = 0;
		$data['liempo3'] = 0;
		$data['liempo4'] = 0;
		$data['liempo5'] = 0;
		$data['liempo6'] = 0;
		$data['liempo7'] = 0;
		$data['liempo8'] = 0;
		$data['liempo9'] = 0;
		$data['liempo10'] = 0;
		$data['liempo11'] = 0;
		$data['liempo12'] = 0;
		$data['liempo_total'] = 0;
		$data['liempo_count'] = 0;
		$data['liempo_avg'] = 0;
		$data['liempo_min'] = 0;
		$data['liempo_max'] = 0;

		foreach($get_liempo as $row){
			$asp = $row->asp;
			$month = $row->asp_date;
			$data['liempo' . $month] = $asp;
			$data['liempo_total'] += $asp;

			if($asp < $data['liempo_min'] || $data['liempo_count'] == 0){
				$data['liempo_min'] = $asp;
			}

			if($asp > $data['liempo_max'] || $data['liempo_count'] == 0){
				$data['liempo_max'] = $asp;
			}

			$data['liempo_count']++;
		}

		$data['liempo_avg'] = $data['liempo_total'] != 0 ? $data['liempo_total'] / $data['liempo_count'] : 0;

		$ctg_liempo_prev_year2 = $this->admin->get_data('comparative_price_tbl', array('comp_price_product' => 'LIEMPO', 'comp_price_segment' => 'CTG', 'comp_price_year' => $year - 2,	'bc_id'	=>	$bc_id), 
			FALSE, 'AVG(comp_price_jan) as jan_price, AVG(comp_price_feb) as feb_price, AVG(comp_price_mar) as mar_price, AVG(comp_price_apr) as apr_price, AVG(comp_price_may) as may_price, AVG(comp_price_jun) as jun_price, AVG(comp_price_jul) as jul_price, AVG(comp_price_aug) as aug_price, AVG(comp_price_sep) as sep_price, AVG(comp_price_oct) as oct_price, AVG(comp_price_nov) as nov_price, AVG(comp_price_dec) as dec_price');
		
		$data['ctg_liempo_prev_year2_jan'] = 0;
		$data['ctg_liempo_prev_year2_feb'] = 0;
		$data['ctg_liempo_prev_year2_mar'] = 0;
		$data['ctg_liempo_prev_year2_apr'] = 0;
		$data['ctg_liempo_prev_year2_may'] = 0;
		$data['ctg_liempo_prev_year2_jun'] = 0;
		$data['ctg_liempo_prev_year2_jul'] = 0;
		$data['ctg_liempo_prev_year2_aug'] = 0;
		$data['ctg_liempo_prev_year2_sep'] = 0;
		$data['ctg_liempo_prev_year2_oct'] = 0;
		$data['ctg_liempo_prev_year2_nov'] = 0;
		$data['ctg_liempo_prev_year2_dec'] = 0;
		$data['ctg_liempo_prev_year2_avg'] = 0;
		$data['ctg_liempo_prev_year2_min'] = 0;
		$data['ctg_liempo_prev_year2_max'] = 0;

		foreach($ctg_liempo_prev_year2 as $row){
			$data['ctg_liempo_prev_year2_jan'] = $row->jan_price;
			$data['ctg_liempo_prev_year2_feb'] = $row->feb_price;
			$data['ctg_liempo_prev_year2_mar'] = $row->mar_price;
			$data['ctg_liempo_prev_year2_apr'] = $row->apr_price;
			$data['ctg_liempo_prev_year2_may'] = $row->may_price;
			$data['ctg_liempo_prev_year2_jun'] = $row->jun_price;
			$data['ctg_liempo_prev_year2_jul'] = $row->jul_price;
			$data['ctg_liempo_prev_year2_aug'] = $row->aug_price;
			$data['ctg_liempo_prev_year2_sep'] = $row->sep_price;
			$data['ctg_liempo_prev_year2_oct'] = $row->oct_price;
			$data['ctg_liempo_prev_year2_nov'] = $row->nov_price;
			$data['ctg_liempo_prev_year2_dec'] = $row->dec_price;
			$data['ctg_liempo_prev_year2_avg'] = ($row->jan_price + $row->feb_price + $row->mar_price + $row->apr_price + $row->may_price + $row->jun_price + $row->jul_price + $row->aug_price + $row->sep_price + $row->oct_price + $row->nov_price + $row->dec_price) / 12;

			$data['ctg_liempo_prev_year2_min'] = min($row->jan_price, $row->feb_price, $row->mar_price, $row->apr_price, $row->may_price, $row->jun_price, $row->jul_price, $row->aug_price, $row->sep_price, $row->oct_price, $row->nov_price, $row->dec_price);

			$data['ctg_liempo_prev_year2_max'] = max($row->jan_price, $row->feb_price, $row->mar_price, $row->apr_price, $row->may_price, $row->jun_price, $row->jul_price, $row->aug_price, $row->sep_price, $row->oct_price, $row->nov_price, $row->dec_price);
		}


		$ctg_liempo_prev_year1 = $this->admin->get_data('comparative_price_tbl', array('comp_price_product' => 'LIEMPO', 'comp_price_segment' => 'CTG', 'comp_price_year' => $year - 1,	'bc_id'	=>	$bc_id), 
			FALSE, 'AVG(comp_price_jan) as jan_price, AVG(comp_price_feb) as feb_price, AVG(comp_price_mar) as mar_price, AVG(comp_price_apr) as apr_price, AVG(comp_price_may) as may_price, AVG(comp_price_jun) as jun_price, AVG(comp_price_jul) as jul_price, AVG(comp_price_aug) as aug_price, AVG(comp_price_sep) as sep_price, AVG(comp_price_oct) as oct_price, AVG(comp_price_nov) as nov_price, AVG(comp_price_dec) as dec_price');
		
		$data['ctg_liempo_prev_year1_jan'] = 0;
		$data['ctg_liempo_prev_year1_feb'] = 0;
		$data['ctg_liempo_prev_year1_mar'] = 0;
		$data['ctg_liempo_prev_year1_apr'] = 0;
		$data['ctg_liempo_prev_year1_may'] = 0;
		$data['ctg_liempo_prev_year1_jun'] = 0;
		$data['ctg_liempo_prev_year1_jul'] = 0;
		$data['ctg_liempo_prev_year1_aug'] = 0;
		$data['ctg_liempo_prev_year1_sep'] = 0;
		$data['ctg_liempo_prev_year1_oct'] = 0;
		$data['ctg_liempo_prev_year1_nov'] = 0;
		$data['ctg_liempo_prev_year1_dec'] = 0;
		$data['ctg_liempo_prev_year1_avg'] = 0;
		$data['ctg_liempo_prev_year1_min'] = 0;
		$data['ctg_liempo_prev_year1_max'] = 0;

		foreach($ctg_liempo_prev_year1 as $row){
			$data['ctg_liempo_prev_year1_jan'] = $row->jan_price;
			$data['ctg_liempo_prev_year1_feb'] = $row->feb_price;
			$data['ctg_liempo_prev_year1_mar'] = $row->mar_price;
			$data['ctg_liempo_prev_year1_apr'] = $row->apr_price;
			$data['ctg_liempo_prev_year1_may'] = $row->may_price;
			$data['ctg_liempo_prev_year1_jun'] = $row->jun_price;
			$data['ctg_liempo_prev_year1_jul'] = $row->jul_price;
			$data['ctg_liempo_prev_year1_aug'] = $row->aug_price;
			$data['ctg_liempo_prev_year1_sep'] = $row->sep_price;
			$data['ctg_liempo_prev_year1_oct'] = $row->oct_price;
			$data['ctg_liempo_prev_year1_nov'] = $row->nov_price;
			$data['ctg_liempo_prev_year1_dec'] = $row->dec_price;
			$data['ctg_liempo_prev_year1_avg'] = ($row->jan_price + $row->feb_price + $row->mar_price + $row->apr_price + $row->may_price + $row->jun_price + $row->jul_price + $row->aug_price + $row->sep_price + $row->oct_price + $row->nov_price + $row->dec_price) / 12;

			$data['ctg_liempo_prev_year1_min'] = min($row->jan_price, $row->feb_price, $row->mar_price, $row->apr_price, $row->may_price, $row->jun_price, $row->jul_price, $row->aug_price, $row->sep_price, $row->oct_price, $row->nov_price, $row->dec_price);

			$data['ctg_liempo_prev_year1_max'] = max($row->jan_price, $row->feb_price, $row->mar_price, $row->apr_price, $row->may_price, $row->jun_price, $row->jul_price, $row->aug_price, $row->sep_price, $row->oct_price, $row->nov_price, $row->dec_price);
		}


		/*CTG Dressed Chicken*/
		$join_ctg_dressed = array(
			'sales_item_tbl b' => 'a.sales_id = b.sales_id AND a.sales_status = 1 AND b.sales_item_status = 1 AND a.sales_year = ' . $year,
			'sales_details_tbl c' => 'b.sales_item_id = c.sales_item_id AND c.sales_det_status = 1',
			'material_tbl d' => 'b.material_id = d.material_id',
			'material_group_tbl e' => 'd.material_group_id = e.material_group_id AND e.material_group_name = "DRESSED"',
			'outlet_tbl f' => 'a.outlet_id = f.outlet_id AND f.bc_id =' . $bc_id,
			'outlet_brand_tbl g' => 'f.outlet_id = g.outlet_id AND g.outlet_brand_status = 1',
			'brand_tbl h' => 'g.brand_id  = h.brand_id AND h.brand_name = \'CHOOKS-TO-GO\''
		);

		$get_ctg_dressed = $this->admin->get_join('sales_tbl a', $join_ctg_dressed, FALSE, FALSE, 'YEAR(c.sales_det_date), MONTH(c.sales_det_date)', 'AVG(c.sales_det_asp) as asp, MONTH(c.sales_det_date) asp_date');

		$data['ctg_dressed1'] = 0;
		$data['ctg_dressed2'] = 0;
		$data['ctg_dressed3'] = 0;
		$data['ctg_dressed4'] = 0;
		$data['ctg_dressed5'] = 0;
		$data['ctg_dressed6'] = 0;
		$data['ctg_dressed7'] = 0;
		$data['ctg_dressed8'] = 0;
		$data['ctg_dressed9'] = 0;
		$data['ctg_dressed10'] = 0;
		$data['ctg_dressed11'] = 0;
		$data['ctg_dressed12'] = 0;
		$data['ctg_dressed_total'] = 0;
		$data['ctg_dressed_count'] = 0;
		$data['ctg_dressed_avg'] = 0;
		$data['ctg_dressed_min'] = 0;
		$data['ctg_dressed_max'] = 0;

		foreach($get_ctg_dressed as $row){
			$asp = $row->asp;
			$month = $row->asp_date;
			$data['ctg_dressed' . $month] = $asp;
			$data['ctg_dressed_total'] += $asp;

			if($asp < $data['ctg_dressed_min'] || $data['ctg_dressed_count'] == 0){
				$data['ctg_dressed_min'] = $asp;
			}

			if($asp > $data['ctg_dressed_max'] || $data['ctg_dressed_count'] == 0){
				$data['ctg_dressed_max'] = $asp;
			}

			$data['ctg_dressed_count']++;
		}

		$data['ctg_dressed_avg'] = $data['ctg_dressed_total'] != 0 ? $data['ctg_dressed_total'] / $data['ctg_dressed_count'] : 0;

		$ctg_dressed_prev_year2 = $this->admin->get_data('comparative_price_tbl', array('comp_price_product' => 'DRESSED', 'comp_price_segment' => 'CTG', 'comp_price_year' => $year - 2,	'bc_id'	=>	$bc_id), 
			FALSE, 'AVG(comp_price_jan) as jan_price, AVG(comp_price_feb) as feb_price, AVG(comp_price_mar) as mar_price, AVG(comp_price_apr) as apr_price, AVG(comp_price_may) as may_price, AVG(comp_price_jun) as jun_price, AVG(comp_price_jul) as jul_price, AVG(comp_price_aug) as aug_price, AVG(comp_price_sep) as sep_price, AVG(comp_price_oct) as oct_price, AVG(comp_price_nov) as nov_price, AVG(comp_price_dec) as dec_price');
		
		$data['ctg_dressed_prev_year2_jan'] = 0;
		$data['ctg_dressed_prev_year2_feb'] = 0;
		$data['ctg_dressed_prev_year2_mar'] = 0;
		$data['ctg_dressed_prev_year2_apr'] = 0;
		$data['ctg_dressed_prev_year2_may'] = 0;
		$data['ctg_dressed_prev_year2_jun'] = 0;
		$data['ctg_dressed_prev_year2_jul'] = 0;
		$data['ctg_dressed_prev_year2_aug'] = 0;
		$data['ctg_dressed_prev_year2_sep'] = 0;
		$data['ctg_dressed_prev_year2_oct'] = 0;
		$data['ctg_dressed_prev_year2_nov'] = 0;
		$data['ctg_dressed_prev_year2_dec'] = 0;
		$data['ctg_dressed_prev_year2_avg'] = 0;
		$data['ctg_dressed_prev_year2_min'] = 0;
		$data['ctg_dressed_prev_year2_max'] = 0;

		foreach($ctg_dressed_prev_year2 as $row){
			$data['ctg_dressed_prev_year2_jan'] = $row->jan_price;
			$data['ctg_dressed_prev_year2_feb'] = $row->feb_price;
			$data['ctg_dressed_prev_year2_mar'] = $row->mar_price;
			$data['ctg_dressed_prev_year2_apr'] = $row->apr_price;
			$data['ctg_dressed_prev_year2_may'] = $row->may_price;
			$data['ctg_dressed_prev_year2_jun'] = $row->jun_price;
			$data['ctg_dressed_prev_year2_jul'] = $row->jul_price;
			$data['ctg_dressed_prev_year2_aug'] = $row->aug_price;
			$data['ctg_dressed_prev_year2_sep'] = $row->sep_price;
			$data['ctg_dressed_prev_year2_oct'] = $row->oct_price;
			$data['ctg_dressed_prev_year2_nov'] = $row->nov_price;
			$data['ctg_dressed_prev_year2_dec'] = $row->dec_price;
			$data['ctg_dressed_prev_year2_avg'] = ($row->jan_price + $row->feb_price + $row->mar_price + $row->apr_price + $row->may_price + $row->jun_price + $row->jul_price + $row->aug_price + $row->sep_price + $row->oct_price + $row->nov_price + $row->dec_price) / 12;

			$data['ctg_dressed_prev_year2_min'] = min($row->jan_price, $row->feb_price, $row->mar_price, $row->apr_price, $row->may_price, $row->jun_price, $row->jul_price, $row->aug_price, $row->sep_price, $row->oct_price, $row->nov_price, $row->dec_price);

			$data['ctg_dressed_prev_year2_max'] = max($row->jan_price, $row->feb_price, $row->mar_price, $row->apr_price, $row->may_price, $row->jun_price, $row->jul_price, $row->aug_price, $row->sep_price, $row->oct_price, $row->nov_price, $row->dec_price);
		}


		$ctg_dressed_prev_year1 = $this->admin->get_data('comparative_price_tbl', array('comp_price_product' => 'DRESSED', 'comp_price_segment' => 'CTG', 'comp_price_year' => $year - 1,	'bc_id'	=>	$bc_id), 
			FALSE, 'AVG(comp_price_jan) as jan_price, AVG(comp_price_feb) as feb_price, AVG(comp_price_mar) as mar_price, AVG(comp_price_apr) as apr_price, AVG(comp_price_may) as may_price, AVG(comp_price_jun) as jun_price, AVG(comp_price_jul) as jul_price, AVG(comp_price_aug) as aug_price, AVG(comp_price_sep) as sep_price, AVG(comp_price_oct) as oct_price, AVG(comp_price_nov) as nov_price, AVG(comp_price_dec) as dec_price');
		
		$data['ctg_dressed_prev_year1_jan'] = 0;
		$data['ctg_dressed_prev_year1_feb'] = 0;
		$data['ctg_dressed_prev_year1_mar'] = 0;
		$data['ctg_dressed_prev_year1_apr'] = 0;
		$data['ctg_dressed_prev_year1_may'] = 0;
		$data['ctg_dressed_prev_year1_jun'] = 0;
		$data['ctg_dressed_prev_year1_jul'] = 0;
		$data['ctg_dressed_prev_year1_aug'] = 0;
		$data['ctg_dressed_prev_year1_sep'] = 0;
		$data['ctg_dressed_prev_year1_oct'] = 0;
		$data['ctg_dressed_prev_year1_nov'] = 0;
		$data['ctg_dressed_prev_year1_dec'] = 0;
		$data['ctg_dressed_prev_year1_avg'] = 0;
		$data['ctg_dressed_prev_year1_min'] = 0;
		$data['ctg_dressed_prev_year1_max'] = 0;

		foreach($ctg_dressed_prev_year1 as $row){
			$data['ctg_dressed_prev_year1_jan'] = $row->jan_price;
			$data['ctg_dressed_prev_year1_feb'] = $row->feb_price;
			$data['ctg_dressed_prev_year1_mar'] = $row->mar_price;
			$data['ctg_dressed_prev_year1_apr'] = $row->apr_price;
			$data['ctg_dressed_prev_year1_may'] = $row->may_price;
			$data['ctg_dressed_prev_year1_jun'] = $row->jun_price;
			$data['ctg_dressed_prev_year1_jul'] = $row->jul_price;
			$data['ctg_dressed_prev_year1_aug'] = $row->aug_price;
			$data['ctg_dressed_prev_year1_sep'] = $row->sep_price;
			$data['ctg_dressed_prev_year1_oct'] = $row->oct_price;
			$data['ctg_dressed_prev_year1_nov'] = $row->nov_price;
			$data['ctg_dressed_prev_year1_dec'] = $row->dec_price;
			$data['ctg_dressed_prev_year1_avg'] = ($row->jan_price + $row->feb_price + $row->mar_price + $row->apr_price + $row->may_price + $row->jun_price + $row->jul_price + $row->aug_price + $row->sep_price + $row->oct_price + $row->nov_price + $row->dec_price) / 12;

			$data['ctg_dressed_prev_year1_min'] = min($row->jan_price, $row->feb_price, $row->mar_price, $row->apr_price, $row->may_price, $row->jun_price, $row->jul_price, $row->aug_price, $row->sep_price, $row->oct_price, $row->nov_price, $row->dec_price);

			$data['ctg_dressed_prev_year1_max'] = max($row->jan_price, $row->feb_price, $row->mar_price, $row->apr_price, $row->may_price, $row->jun_price, $row->jul_price, $row->aug_price, $row->sep_price, $row->oct_price, $row->nov_price, $row->dec_price);
		}


		/*CTG Chooksies*/
		$join_ctg_chooksies = array(
			'sales_item_tbl b' => 'a.sales_id = b.sales_id AND a.sales_status = 1 AND b.sales_item_status = 1 AND a.sales_year = ' . $year,
			'sales_details_tbl c' => 'b.sales_item_id = c.sales_item_id AND c.sales_det_status = 1',
			'material_tbl d' => 'b.material_id = d.material_id AND (d.material_code = 10200095 OR d.material_code = 10200096 OR d.material_code = 10200097)',
			'material_group_tbl e' => 'd.material_group_id = e.material_group_id AND e.material_group_name = \'MARINATED CUT UPS\'',
			'outlet_tbl f' => 'a.outlet_id = f.outlet_id AND f.bc_id =' . $bc_id,
			'outlet_brand_tbl g' => 'f.outlet_id = g.outlet_id AND g.outlet_brand_status = 1',
			'brand_tbl h' => 'g.brand_id  = h.brand_id AND h.brand_name = \'CHOOKS-TO-GO\''
		);

		$get_ctg_chooksies = $this->admin->get_join('sales_tbl a', $join_ctg_chooksies, FALSE, FALSE, 'YEAR(c.sales_det_date), MONTH(c.sales_det_date)', 'AVG(c.sales_det_asp) as asp, MONTH(c.sales_det_date) asp_date');

		$data['ctg_chooksies1'] = 0;
		$data['ctg_chooksies2'] = 0;
		$data['ctg_chooksies3'] = 0;
		$data['ctg_chooksies4'] = 0;
		$data['ctg_chooksies5'] = 0;
		$data['ctg_chooksies6'] = 0;
		$data['ctg_chooksies7'] = 0;
		$data['ctg_chooksies8'] = 0;
		$data['ctg_chooksies9'] = 0;
		$data['ctg_chooksies10'] = 0;
		$data['ctg_chooksies11'] = 0;
		$data['ctg_chooksies12'] = 0;
		$data['ctg_chooksies_total'] = 0;
		$data['ctg_chooksies_count'] = 0;
		$data['ctg_chooksies_avg'] = 0;
		$data['ctg_chooksies_min'] = 0;
		$data['ctg_chooksies_max'] = 0;

		foreach($get_ctg_chooksies as $row){
			$asp = $row->asp;
			$month = $row->asp_date;
			$data['ctg_chooksies' . $month] = $asp;
			$data['ctg_chooksies_total'] += $asp;

			if($asp < $data['ctg_chooksies_min'] || $data['ctg_chooksies_count'] == 0){
				$data['ctg_chooksies_min'] = $asp;
			}

			if($asp > $data['ctg_chooksies_max'] || $data['ctg_chooksies_count'] == 0){
				$data['ctg_chooksies_max'] = $asp;
			}

			$data['ctg_chooksies_count']++;
		}

		$data['ctg_chooksies_avg'] = $data['ctg_chooksies_total'] != 0 ? $data['ctg_chooksies_total'] / $data['ctg_chooksies_count'] : 0;


		$ctg_chooksies_prev_year2 = $this->admin->get_data('comparative_price_tbl', array('comp_price_product' => 'CHOOKSIES MARINADO', 'comp_price_segment' => 'CTG', 'comp_price_year' => $year - 2,	'bc_id'	=>	$bc_id), 
			FALSE, 'AVG(comp_price_jan) as jan_price, AVG(comp_price_feb) as feb_price, AVG(comp_price_mar) as mar_price, AVG(comp_price_apr) as apr_price, AVG(comp_price_may) as may_price, AVG(comp_price_jun) as jun_price, AVG(comp_price_jul) as jul_price, AVG(comp_price_aug) as aug_price, AVG(comp_price_sep) as sep_price, AVG(comp_price_oct) as oct_price, AVG(comp_price_nov) as nov_price, AVG(comp_price_dec) as dec_price');
		
		$data['ctg_chooksies_prev_year2_jan'] = 0;
		$data['ctg_chooksies_prev_year2_feb'] = 0;
		$data['ctg_chooksies_prev_year2_mar'] = 0;
		$data['ctg_chooksies_prev_year2_apr'] = 0;
		$data['ctg_chooksies_prev_year2_may'] = 0;
		$data['ctg_chooksies_prev_year2_jun'] = 0;
		$data['ctg_chooksies_prev_year2_jul'] = 0;
		$data['ctg_chooksies_prev_year2_aug'] = 0;
		$data['ctg_chooksies_prev_year2_sep'] = 0;
		$data['ctg_chooksies_prev_year2_oct'] = 0;
		$data['ctg_chooksies_prev_year2_nov'] = 0;
		$data['ctg_chooksies_prev_year2_dec'] = 0;
		$data['ctg_chooksies_prev_year2_avg'] = 0;
		$data['ctg_chooksies_prev_year2_min'] = 0;
		$data['ctg_chooksies_prev_year2_max'] = 0;

		foreach($ctg_chooksies_prev_year2 as $row){
			$data['ctg_chooksies_prev_year2_jan'] = $row->jan_price;
			$data['ctg_chooksies_prev_year2_feb'] = $row->feb_price;
			$data['ctg_chooksies_prev_year2_mar'] = $row->mar_price;
			$data['ctg_chooksies_prev_year2_apr'] = $row->apr_price;
			$data['ctg_chooksies_prev_year2_may'] = $row->may_price;
			$data['ctg_chooksies_prev_year2_jun'] = $row->jun_price;
			$data['ctg_chooksies_prev_year2_jul'] = $row->jul_price;
			$data['ctg_chooksies_prev_year2_aug'] = $row->aug_price;
			$data['ctg_chooksies_prev_year2_sep'] = $row->sep_price;
			$data['ctg_chooksies_prev_year2_oct'] = $row->oct_price;
			$data['ctg_chooksies_prev_year2_nov'] = $row->nov_price;
			$data['ctg_chooksies_prev_year2_dec'] = $row->dec_price;
			$data['ctg_chooksies_prev_year2_avg'] = ($row->jan_price + $row->feb_price + $row->mar_price + $row->apr_price + $row->may_price + $row->jun_price + $row->jul_price + $row->aug_price + $row->sep_price + $row->oct_price + $row->nov_price + $row->dec_price) / 12;

			$data['ctg_chooksies_prev_year2_min'] = min($row->jan_price, $row->feb_price, $row->mar_price, $row->apr_price, $row->may_price, $row->jun_price, $row->jul_price, $row->aug_price, $row->sep_price, $row->oct_price, $row->nov_price, $row->dec_price);

			$data['ctg_chooksies_prev_year2_max'] = max($row->jan_price, $row->feb_price, $row->mar_price, $row->apr_price, $row->may_price, $row->jun_price, $row->jul_price, $row->aug_price, $row->sep_price, $row->oct_price, $row->nov_price, $row->dec_price);
		}


		$ctg_chooksies_prev_year1 = $this->admin->get_data('comparative_price_tbl', array('comp_price_product' => 'CHOOKSIES MARINADO', 'comp_price_segment' => 'CTG', 'comp_price_year' => $year - 1,	'bc_id'	=>	$bc_id), 
			FALSE, 'AVG(comp_price_jan) as jan_price, AVG(comp_price_feb) as feb_price, AVG(comp_price_mar) as mar_price, AVG(comp_price_apr) as apr_price, AVG(comp_price_may) as may_price, AVG(comp_price_jun) as jun_price, AVG(comp_price_jul) as jul_price, AVG(comp_price_aug) as aug_price, AVG(comp_price_sep) as sep_price, AVG(comp_price_oct) as oct_price, AVG(comp_price_nov) as nov_price, AVG(comp_price_dec) as dec_price');
		
		$data['ctg_chooksies_prev_year1_jan'] = 0;
		$data['ctg_chooksies_prev_year1_feb'] = 0;
		$data['ctg_chooksies_prev_year1_mar'] = 0;
		$data['ctg_chooksies_prev_year1_apr'] = 0;
		$data['ctg_chooksies_prev_year1_may'] = 0;
		$data['ctg_chooksies_prev_year1_jun'] = 0;
		$data['ctg_chooksies_prev_year1_jul'] = 0;
		$data['ctg_chooksies_prev_year1_aug'] = 0;
		$data['ctg_chooksies_prev_year1_sep'] = 0;
		$data['ctg_chooksies_prev_year1_oct'] = 0;
		$data['ctg_chooksies_prev_year1_nov'] = 0;
		$data['ctg_chooksies_prev_year1_dec'] = 0;
		$data['ctg_chooksies_prev_year1_avg'] = 0;
		$data['ctg_chooksies_prev_year1_min'] = 0;
		$data['ctg_chooksies_prev_year1_max'] = 0;

		foreach($ctg_chooksies_prev_year1 as $row){
			$data['ctg_chooksies_prev_year1_jan'] = $row->jan_price;
			$data['ctg_chooksies_prev_year1_feb'] = $row->feb_price;
			$data['ctg_chooksies_prev_year1_mar'] = $row->mar_price;
			$data['ctg_chooksies_prev_year1_apr'] = $row->apr_price;
			$data['ctg_chooksies_prev_year1_may'] = $row->may_price;
			$data['ctg_chooksies_prev_year1_jun'] = $row->jun_price;
			$data['ctg_chooksies_prev_year1_jul'] = $row->jul_price;
			$data['ctg_chooksies_prev_year1_aug'] = $row->aug_price;
			$data['ctg_chooksies_prev_year1_sep'] = $row->sep_price;
			$data['ctg_chooksies_prev_year1_oct'] = $row->oct_price;
			$data['ctg_chooksies_prev_year1_nov'] = $row->nov_price;
			$data['ctg_chooksies_prev_year1_dec'] = $row->dec_price;
			$data['ctg_chooksies_prev_year1_avg'] = ($row->jan_price + $row->feb_price + $row->mar_price + $row->apr_price + $row->may_price + $row->jun_price + $row->jul_price + $row->aug_price + $row->sep_price + $row->oct_price + $row->nov_price + $row->dec_price) / 12;

			$data['ctg_chooksies_prev_year1_min'] = min($row->jan_price, $row->feb_price, $row->mar_price, $row->apr_price, $row->may_price, $row->jun_price, $row->jul_price, $row->aug_price, $row->sep_price, $row->oct_price, $row->nov_price, $row->dec_price);

			$data['ctg_chooksies_prev_year1_max'] = max($row->jan_price, $row->feb_price, $row->mar_price, $row->apr_price, $row->may_price, $row->jun_price, $row->jul_price, $row->aug_price, $row->sep_price, $row->oct_price, $row->nov_price, $row->dec_price);
		}


		$join_ctg_marinado = array(
			'sales_item_tbl b' => 'a.sales_id = b.sales_id AND a.sales_status = 1 AND b.sales_item_status = 1 AND a.sales_year = ' . $year,
			'sales_details_tbl c' => 'b.sales_item_id = c.sales_item_id AND c.sales_det_status = 1',
			'material_tbl d' => 'b.material_id = d.material_id AND d.material_code = 1000638',
			'material_group_tbl e' => 'd.material_group_id = e.material_group_id AND e.material_group_name = \'MARINATED CUT UPS\'',
			'outlet_tbl f' => 'a.outlet_id = f.outlet_id AND f.bc_id =' . $bc_id,
			'outlet_brand_tbl g' => 'f.outlet_id = g.outlet_id AND g.outlet_brand_status = 1',
			'brand_tbl h' => 'g.brand_id  = h.brand_id AND h.brand_name = \'CHOOKS-TO-GO\''
		);

		$get_ctg_marinado = $this->admin->get_join('sales_tbl a', $join_ctg_marinado, FALSE, FALSE, 'YEAR(c.sales_det_date), MONTH(c.sales_det_date)', 'AVG(c.sales_det_asp) as asp, MONTH(c.sales_det_date) asp_date');

		$data['ctg_marinado1'] = 0;
		$data['ctg_marinado2'] = 0;
		$data['ctg_marinado3'] = 0;
		$data['ctg_marinado4'] = 0;
		$data['ctg_marinado5'] = 0;
		$data['ctg_marinado6'] = 0;
		$data['ctg_marinado7'] = 0;
		$data['ctg_marinado8'] = 0;
		$data['ctg_marinado9'] = 0;
		$data['ctg_marinado10'] = 0;
		$data['ctg_marinado11'] = 0;
		$data['ctg_marinado12'] = 0;
		$data['ctg_marinado_total'] = 0;
		$data['ctg_marinado_count'] = 0;
		$data['ctg_marinado_avg'] = 0;
		$data['ctg_marinado_min'] = 0;
		$data['ctg_marinado_max'] = 0;

		foreach($get_ctg_marinado as $row){
			$asp = $row->asp;
			$month = $row->asp_date;
			$data['ctg_marinado' . $month] = $asp;
			$data['ctg_marinado_total'] += $asp;

			if($asp < $data['ctg_marinado_min'] || $data['ctg_marinado_count'] == 0){
				$data['ctg_marinado_min'] = $asp;
			}

			if($asp > $data['ctg_marinado_max'] || $data['ctg_marinado_count'] == 0){
				$data['ctg_marinado_max'] = $asp;
			}

			$data['ctg_marinado_count']++;
		}

		$data['ctg_marinado_avg'] = $data['ctg_marinado_total'] != 0 ? $data['ctg_marinado_total'] / $data['ctg_marinado_count'] : 0;

		$ctg_marinado_prev_year2 = $this->admin->get_data('comparative_price_tbl', array('comp_price_product' => 'MARINADO FRIED', 'comp_price_segment' => 'CTG', 'comp_price_year' => $year - 2,	'bc_id'	=>	$bc_id), 
			FALSE, 'AVG(comp_price_jan) as jan_price, AVG(comp_price_feb) as feb_price, AVG(comp_price_mar) as mar_price, AVG(comp_price_apr) as apr_price, AVG(comp_price_may) as may_price, AVG(comp_price_jun) as jun_price, AVG(comp_price_jul) as jul_price, AVG(comp_price_aug) as aug_price, AVG(comp_price_sep) as sep_price, AVG(comp_price_oct) as oct_price, AVG(comp_price_nov) as nov_price, AVG(comp_price_dec) as dec_price');
		
		$data['ctg_marinado_prev_year2_jan'] = 0;
		$data['ctg_marinado_prev_year2_feb'] = 0;
		$data['ctg_marinado_prev_year2_mar'] = 0;
		$data['ctg_marinado_prev_year2_apr'] = 0;
		$data['ctg_marinado_prev_year2_may'] = 0;
		$data['ctg_marinado_prev_year2_jun'] = 0;
		$data['ctg_marinado_prev_year2_jul'] = 0;
		$data['ctg_marinado_prev_year2_aug'] = 0;
		$data['ctg_marinado_prev_year2_sep'] = 0;
		$data['ctg_marinado_prev_year2_oct'] = 0;
		$data['ctg_marinado_prev_year2_nov'] = 0;
		$data['ctg_marinado_prev_year2_dec'] = 0;
		$data['ctg_marinado_prev_year2_avg'] = 0;
		$data['ctg_marinado_prev_year2_min'] = 0;
		$data['ctg_marinado_prev_year2_max'] = 0;

		foreach($ctg_marinado_prev_year2 as $row){
			$data['ctg_marinado_prev_year2_jan'] = $row->jan_price;
			$data['ctg_marinado_prev_year2_feb'] = $row->feb_price;
			$data['ctg_marinado_prev_year2_mar'] = $row->mar_price;
			$data['ctg_marinado_prev_year2_apr'] = $row->apr_price;
			$data['ctg_marinado_prev_year2_may'] = $row->may_price;
			$data['ctg_marinado_prev_year2_jun'] = $row->jun_price;
			$data['ctg_marinado_prev_year2_jul'] = $row->jul_price;
			$data['ctg_marinado_prev_year2_aug'] = $row->aug_price;
			$data['ctg_marinado_prev_year2_sep'] = $row->sep_price;
			$data['ctg_marinado_prev_year2_oct'] = $row->oct_price;
			$data['ctg_marinado_prev_year2_nov'] = $row->nov_price;
			$data['ctg_marinado_prev_year2_dec'] = $row->dec_price;
			$data['ctg_marinado_prev_year2_avg'] = ($row->jan_price + $row->feb_price + $row->mar_price + $row->apr_price + $row->may_price + $row->jun_price + $row->jul_price + $row->aug_price + $row->sep_price + $row->oct_price + $row->nov_price + $row->dec_price) / 12;

			$data['ctg_marinado_prev_year2_min'] = min($row->jan_price, $row->feb_price, $row->mar_price, $row->apr_price, $row->may_price, $row->jun_price, $row->jul_price, $row->aug_price, $row->sep_price, $row->oct_price, $row->nov_price, $row->dec_price);

			$data['ctg_marinado_prev_year2_max'] = max($row->jan_price, $row->feb_price, $row->mar_price, $row->apr_price, $row->may_price, $row->jun_price, $row->jul_price, $row->aug_price, $row->sep_price, $row->oct_price, $row->nov_price, $row->dec_price);
		}


		$ctg_marinado_prev_year1 = $this->admin->get_data('comparative_price_tbl', array('comp_price_product' => 'MARINADO FRIED', 'comp_price_segment' => 'CTG', 'comp_price_year' => $year - 1,	'bc_id'	=>	$bc_id), 
			FALSE, 'AVG(comp_price_jan) as jan_price, AVG(comp_price_feb) as feb_price, AVG(comp_price_mar) as mar_price, AVG(comp_price_apr) as apr_price, AVG(comp_price_may) as may_price, AVG(comp_price_jun) as jun_price, AVG(comp_price_jul) as jul_price, AVG(comp_price_aug) as aug_price, AVG(comp_price_sep) as sep_price, AVG(comp_price_oct) as oct_price, AVG(comp_price_nov) as nov_price, AVG(comp_price_dec) as dec_price');
		
		$data['ctg_marinado_prev_year1_jan'] = 0;
		$data['ctg_marinado_prev_year1_feb'] = 0;
		$data['ctg_marinado_prev_year1_mar'] = 0;
		$data['ctg_marinado_prev_year1_apr'] = 0;
		$data['ctg_marinado_prev_year1_may'] = 0;
		$data['ctg_marinado_prev_year1_jun'] = 0;
		$data['ctg_marinado_prev_year1_jul'] = 0;
		$data['ctg_marinado_prev_year1_aug'] = 0;
		$data['ctg_marinado_prev_year1_sep'] = 0;
		$data['ctg_marinado_prev_year1_oct'] = 0;
		$data['ctg_marinado_prev_year1_nov'] = 0;
		$data['ctg_marinado_prev_year1_dec'] = 0;
		$data['ctg_marinado_prev_year1_avg'] = 0;
		$data['ctg_marinado_prev_year1_min'] = 0;
		$data['ctg_marinado_prev_year1_max'] = 0;

		foreach($ctg_marinado_prev_year1 as $row){
			$data['ctg_marinado_prev_year1_jan'] = $row->jan_price;
			$data['ctg_marinado_prev_year1_feb'] = $row->feb_price;
			$data['ctg_marinado_prev_year1_mar'] = $row->mar_price;
			$data['ctg_marinado_prev_year1_apr'] = $row->apr_price;
			$data['ctg_marinado_prev_year1_may'] = $row->may_price;
			$data['ctg_marinado_prev_year1_jun'] = $row->jun_price;
			$data['ctg_marinado_prev_year1_jul'] = $row->jul_price;
			$data['ctg_marinado_prev_year1_aug'] = $row->aug_price;
			$data['ctg_marinado_prev_year1_sep'] = $row->sep_price;
			$data['ctg_marinado_prev_year1_oct'] = $row->oct_price;
			$data['ctg_marinado_prev_year1_nov'] = $row->nov_price;
			$data['ctg_marinado_prev_year1_dec'] = $row->dec_price;
			$data['ctg_marinado_prev_year1_avg'] = ($row->jan_price + $row->feb_price + $row->mar_price + $row->apr_price + $row->may_price + $row->jun_price + $row->jul_price + $row->aug_price + $row->sep_price + $row->oct_price + $row->nov_price + $row->dec_price) / 12;

			$data['ctg_marinado_prev_year1_min'] = min($row->jan_price, $row->feb_price, $row->mar_price, $row->apr_price, $row->may_price, $row->jun_price, $row->jul_price, $row->aug_price, $row->sep_price, $row->oct_price, $row->nov_price, $row->dec_price);

			$data['ctg_marinado_prev_year1_max'] = max($row->jan_price, $row->feb_price, $row->mar_price, $row->apr_price, $row->may_price, $row->jun_price, $row->jul_price, $row->aug_price, $row->sep_price, $row->oct_price, $row->nov_price, $row->dec_price);
		}


		$join_ctg_spicy = array(
			'sales_item_tbl b' => 'a.sales_id = b.sales_id AND a.sales_status = 1 AND b.sales_item_status = 1 AND a.sales_year = ' . $year,
			'sales_details_tbl c' => 'b.sales_item_id = c.sales_item_id AND c.sales_det_status = 1',
			'material_tbl d' => 'b.material_id = d.material_id AND (d.material_code = 10200090 OR d.material_code = 1000401 OR d.material_code = 1000402 OR d.material_code = 1000617)',
			'material_group_tbl e' => 'd.material_group_id = e.material_group_id AND e.material_group_name = \'MARINATED CUT UPS\'',
			'outlet_tbl f' => 'a.outlet_id = f.outlet_id AND f.bc_id =' . $bc_id,
			'outlet_brand_tbl g' => 'f.outlet_id = g.outlet_id AND g.outlet_brand_status = 1',
			'brand_tbl h' => 'g.brand_id  = h.brand_id AND h.brand_name = \'CHOOKS-TO-GO\''
		);

		$get_ctg_spicy = $this->admin->get_join('sales_tbl a', $join_ctg_spicy, FALSE, FALSE, 'YEAR(c.sales_det_date), MONTH(c.sales_det_date)', 'AVG(c.sales_det_asp) as asp, MONTH(c.sales_det_date) asp_date');

		$data['ctg_spicy1'] = 0;
		$data['ctg_spicy2'] = 0;
		$data['ctg_spicy3'] = 0;
		$data['ctg_spicy4'] = 0;
		$data['ctg_spicy5'] = 0;
		$data['ctg_spicy6'] = 0;
		$data['ctg_spicy7'] = 0;
		$data['ctg_spicy8'] = 0;
		$data['ctg_spicy9'] = 0;
		$data['ctg_spicy10'] = 0;
		$data['ctg_spicy11'] = 0;
		$data['ctg_spicy12'] = 0;
		$data['ctg_spicy_total'] = 0;
		$data['ctg_spicy_count'] = 0;
		$data['ctg_spicy_avg'] = 0;
		$data['ctg_spicy_min'] = 0;
		$data['ctg_spicy_max'] = 0;

		foreach($get_ctg_spicy as $row){
			$asp = $row->asp;
			$month = $row->asp_date;
			$data['ctg_spicy' . $month] = $asp;
			$data['ctg_spicy_total'] += $asp;

			if($asp < $data['ctg_spicy_min'] || $data['ctg_spicy_count'] == 0){
				$data['ctg_spicy_min'] = $asp;
			}

			if($asp > $data['ctg_spicy_max'] || $data['ctg_spicy_count'] == 0){
				$data['ctg_spicy_max'] = $asp;
			}

			$data['ctg_spicy_count']++;
		}

		$data['ctg_spicy_avg'] = $data['ctg_spicy_total'] != 0 ? $data['ctg_spicy_total'] / $data['ctg_spicy_count'] : 0;

		$ctg_spicy_prev_year2 = $this->admin->get_data('comparative_price_tbl', array('comp_price_product' => 'SPICY NECK', 'comp_price_segment' => 'CTG', 'comp_price_year' => $year - 2,	'bc_id'	=>	$bc_id), 
			FALSE, 'AVG(comp_price_jan) as jan_price, AVG(comp_price_feb) as feb_price, AVG(comp_price_mar) as mar_price, AVG(comp_price_apr) as apr_price, AVG(comp_price_may) as may_price, AVG(comp_price_jun) as jun_price, AVG(comp_price_jul) as jul_price, AVG(comp_price_aug) as aug_price, AVG(comp_price_sep) as sep_price, AVG(comp_price_oct) as oct_price, AVG(comp_price_nov) as nov_price, AVG(comp_price_dec) as dec_price');
		
		$data['ctg_spicy_prev_year2_jan'] = 0;
		$data['ctg_spicy_prev_year2_feb'] = 0;
		$data['ctg_spicy_prev_year2_mar'] = 0;
		$data['ctg_spicy_prev_year2_apr'] = 0;
		$data['ctg_spicy_prev_year2_may'] = 0;
		$data['ctg_spicy_prev_year2_jun'] = 0;
		$data['ctg_spicy_prev_year2_jul'] = 0;
		$data['ctg_spicy_prev_year2_aug'] = 0;
		$data['ctg_spicy_prev_year2_sep'] = 0;
		$data['ctg_spicy_prev_year2_oct'] = 0;
		$data['ctg_spicy_prev_year2_nov'] = 0;
		$data['ctg_spicy_prev_year2_dec'] = 0;
		$data['ctg_spicy_prev_year2_avg'] = 0;
		$data['ctg_spicy_prev_year2_min'] = 0;
		$data['ctg_spicy_prev_year2_max'] = 0;

		foreach($ctg_spicy_prev_year2 as $row){
			$data['ctg_spicy_prev_year2_jan'] = $row->jan_price;
			$data['ctg_spicy_prev_year2_feb'] = $row->feb_price;
			$data['ctg_spicy_prev_year2_mar'] = $row->mar_price;
			$data['ctg_spicy_prev_year2_apr'] = $row->apr_price;
			$data['ctg_spicy_prev_year2_may'] = $row->may_price;
			$data['ctg_spicy_prev_year2_jun'] = $row->jun_price;
			$data['ctg_spicy_prev_year2_jul'] = $row->jul_price;
			$data['ctg_spicy_prev_year2_aug'] = $row->aug_price;
			$data['ctg_spicy_prev_year2_sep'] = $row->sep_price;
			$data['ctg_spicy_prev_year2_oct'] = $row->oct_price;
			$data['ctg_spicy_prev_year2_nov'] = $row->nov_price;
			$data['ctg_spicy_prev_year2_dec'] = $row->dec_price;
			$data['ctg_spicy_prev_year2_avg'] = ($row->jan_price + $row->feb_price + $row->mar_price + $row->apr_price + $row->may_price + $row->jun_price + $row->jul_price + $row->aug_price + $row->sep_price + $row->oct_price + $row->nov_price + $row->dec_price) / 12;

			$data['ctg_spicy_prev_year2_min'] = min($row->jan_price, $row->feb_price, $row->mar_price, $row->apr_price, $row->may_price, $row->jun_price, $row->jul_price, $row->aug_price, $row->sep_price, $row->oct_price, $row->nov_price, $row->dec_price);

			$data['ctg_spicy_prev_year2_max'] = max($row->jan_price, $row->feb_price, $row->mar_price, $row->apr_price, $row->may_price, $row->jun_price, $row->jul_price, $row->aug_price, $row->sep_price, $row->oct_price, $row->nov_price, $row->dec_price);
		}


		$ctg_spicy_prev_year1 = $this->admin->get_data('comparative_price_tbl', array('comp_price_product' => 'SPICY NECK', 'comp_price_segment' => 'CTG', 'comp_price_year' => $year - 1,	'bc_id'	=>	$bc_id), 
			FALSE, 'AVG(comp_price_jan) as jan_price, AVG(comp_price_feb) as feb_price, AVG(comp_price_mar) as mar_price, AVG(comp_price_apr) as apr_price, AVG(comp_price_may) as may_price, AVG(comp_price_jun) as jun_price, AVG(comp_price_jul) as jul_price, AVG(comp_price_aug) as aug_price, AVG(comp_price_sep) as sep_price, AVG(comp_price_oct) as oct_price, AVG(comp_price_nov) as nov_price, AVG(comp_price_dec) as dec_price');
		
		$data['ctg_spicy_prev_year1_jan'] = 0;
		$data['ctg_spicy_prev_year1_feb'] = 0;
		$data['ctg_spicy_prev_year1_mar'] = 0;
		$data['ctg_spicy_prev_year1_apr'] = 0;
		$data['ctg_spicy_prev_year1_may'] = 0;
		$data['ctg_spicy_prev_year1_jun'] = 0;
		$data['ctg_spicy_prev_year1_jul'] = 0;
		$data['ctg_spicy_prev_year1_aug'] = 0;
		$data['ctg_spicy_prev_year1_sep'] = 0;
		$data['ctg_spicy_prev_year1_oct'] = 0;
		$data['ctg_spicy_prev_year1_nov'] = 0;
		$data['ctg_spicy_prev_year1_dec'] = 0;
		$data['ctg_spicy_prev_year1_avg'] = 0;
		$data['ctg_spicy_prev_year1_min'] = 0;
		$data['ctg_spicy_prev_year1_max'] = 0;

		foreach($ctg_spicy_prev_year1 as $row){
			$data['ctg_spicy_prev_year1_jan'] = $row->jan_price;
			$data['ctg_spicy_prev_year1_feb'] = $row->feb_price;
			$data['ctg_spicy_prev_year1_mar'] = $row->mar_price;
			$data['ctg_spicy_prev_year1_apr'] = $row->apr_price;
			$data['ctg_spicy_prev_year1_may'] = $row->may_price;
			$data['ctg_spicy_prev_year1_jun'] = $row->jun_price;
			$data['ctg_spicy_prev_year1_jul'] = $row->jul_price;
			$data['ctg_spicy_prev_year1_aug'] = $row->aug_price;
			$data['ctg_spicy_prev_year1_sep'] = $row->sep_price;
			$data['ctg_spicy_prev_year1_oct'] = $row->oct_price;
			$data['ctg_spicy_prev_year1_nov'] = $row->nov_price;
			$data['ctg_spicy_prev_year1_dec'] = $row->dec_price;
			$data['ctg_spicy_prev_year1_avg'] = ($row->jan_price + $row->feb_price + $row->mar_price + $row->apr_price + $row->may_price + $row->jun_price + $row->jul_price + $row->aug_price + $row->sep_price + $row->oct_price + $row->nov_price + $row->dec_price) / 12;

			$data['ctg_spicy_prev_year1_min'] = min($row->jan_price, $row->feb_price, $row->mar_price, $row->apr_price, $row->may_price, $row->jun_price, $row->jul_price, $row->aug_price, $row->sep_price, $row->oct_price, $row->nov_price, $row->dec_price);

			$data['ctg_spicy_prev_year1_max'] = max($row->jan_price, $row->feb_price, $row->mar_price, $row->apr_price, $row->may_price, $row->jun_price, $row->jul_price, $row->aug_price, $row->sep_price, $row->oct_price, $row->nov_price, $row->dec_price);
		}

		/*Barbeque*/
		$join_ctg_barbeque = array(
			'sales_item_tbl b' => 'a.sales_id = b.sales_id AND a.sales_status = 1 AND b.sales_item_status = 1 AND a.sales_year = ' . $year,
			'sales_details_tbl c' => 'b.sales_item_id = c.sales_item_id AND c.sales_det_status = 1',
			'material_tbl d' => 'b.material_id = d.material_id AND d.material_code = 1000548',
			'material_group_tbl e' => 'd.material_group_id = e.material_group_id AND e.material_group_name = \'OTHER SPECIALTY PRODUCTS\'',
			'outlet_tbl f' => 'a.outlet_id = f.outlet_id AND f.bc_id = ' . $bc_id,
			'outlet_brand_tbl g' => 'f.outlet_id = g.outlet_id AND g.outlet_brand_status = 1',
			'brand_tbl h' => 'g.brand_id  = h.brand_id AND h.brand_name = \'CHOOKS-TO-GO\''
		);

		$get_ctg_barbeque = $this->admin->get_join('sales_tbl a', $join_ctg_barbeque, FALSE, FALSE, 'YEAR(c.sales_det_date), MONTH(c.sales_det_date)', 'AVG(c.sales_det_asp) as asp, MONTH(c.sales_det_date) asp_date');

		$data['ctg_barbeque1'] = 0;
		$data['ctg_barbeque2'] = 0;
		$data['ctg_barbeque3'] = 0;
		$data['ctg_barbeque4'] = 0;
		$data['ctg_barbeque5'] = 0;
		$data['ctg_barbeque6'] = 0;
		$data['ctg_barbeque7'] = 0;
		$data['ctg_barbeque8'] = 0;
		$data['ctg_barbeque9'] = 0;
		$data['ctg_barbeque10'] = 0;
		$data['ctg_barbeque11'] = 0;
		$data['ctg_barbeque12'] = 0;
		$data['ctg_barbeque_total'] = 0;
		$data['ctg_barbeque_count'] = 0;
		$data['ctg_barbeque_avg'] = 0;
		$data['ctg_barbeque_min'] = 0;
		$data['ctg_barbeque_max'] = 0;

		foreach($get_ctg_barbeque as $row){
			$asp = $row->asp;
			$month = $row->asp_date;
			$data['ctg_barbeque' . $month] = $asp;
			$data['ctg_barbeque_total'] += $asp;

			if($asp < $data['ctg_barbeque_min'] || $data['ctg_barbeque_count'] == 0){
				$data['ctg_barbeque_min'] = $asp;
			}

			if($asp > $data['ctg_barbeque_max'] || $data['ctg_barbeque_count'] == 0){
				$data['ctg_barbeque_max'] = $asp;
			}

			$data['ctg_barbeque_count']++;
		}

		$data['ctg_barbeque_avg'] = $data['ctg_barbeque_total'] != 0 ? $data['ctg_barbeque_total'] / $data['ctg_barbeque_count'] : 0;

		$ctg_barbecue_prev_year2 = $this->admin->get_data('comparative_price_tbl', array('comp_price_product' => 'BBQ', 'comp_price_segment' => 'CTG', 'comp_price_year' => $year - 2,	'bc_id'	=>	$bc_id), 
			FALSE, 'AVG(comp_price_jan) as jan_price, AVG(comp_price_feb) as feb_price, AVG(comp_price_mar) as mar_price, AVG(comp_price_apr) as apr_price, AVG(comp_price_may) as may_price, AVG(comp_price_jun) as jun_price, AVG(comp_price_jul) as jul_price, AVG(comp_price_aug) as aug_price, AVG(comp_price_sep) as sep_price, AVG(comp_price_oct) as oct_price, AVG(comp_price_nov) as nov_price, AVG(comp_price_dec) as dec_price');
		
		$data['ctg_barbecue_prev_year2_jan'] = 0;
		$data['ctg_barbecue_prev_year2_feb'] = 0;
		$data['ctg_barbecue_prev_year2_mar'] = 0;
		$data['ctg_barbecue_prev_year2_apr'] = 0;
		$data['ctg_barbecue_prev_year2_may'] = 0;
		$data['ctg_barbecue_prev_year2_jun'] = 0;
		$data['ctg_barbecue_prev_year2_jul'] = 0;
		$data['ctg_barbecue_prev_year2_aug'] = 0;
		$data['ctg_barbecue_prev_year2_sep'] = 0;
		$data['ctg_barbecue_prev_year2_oct'] = 0;
		$data['ctg_barbecue_prev_year2_nov'] = 0;
		$data['ctg_barbecue_prev_year2_dec'] = 0;
		$data['ctg_barbecue_prev_year2_avg'] = 0;
		$data['ctg_barbecue_prev_year2_min'] = 0;
		$data['ctg_barbecue_prev_year2_max'] = 0;

		foreach($ctg_barbecue_prev_year2 as $row){
			$data['ctg_barbecue_prev_year2_jan'] = $row->jan_price;
			$data['ctg_barbecue_prev_year2_feb'] = $row->feb_price;
			$data['ctg_barbecue_prev_year2_mar'] = $row->mar_price;
			$data['ctg_barbecue_prev_year2_apr'] = $row->apr_price;
			$data['ctg_barbecue_prev_year2_may'] = $row->may_price;
			$data['ctg_barbecue_prev_year2_jun'] = $row->jun_price;
			$data['ctg_barbecue_prev_year2_jul'] = $row->jul_price;
			$data['ctg_barbecue_prev_year2_aug'] = $row->aug_price;
			$data['ctg_barbecue_prev_year2_sep'] = $row->sep_price;
			$data['ctg_barbecue_prev_year2_oct'] = $row->oct_price;
			$data['ctg_barbecue_prev_year2_nov'] = $row->nov_price;
			$data['ctg_barbecue_prev_year2_dec'] = $row->dec_price;
			$data['ctg_barbecue_prev_year2_avg'] = ($row->jan_price + $row->feb_price + $row->mar_price + $row->apr_price + $row->may_price + $row->jun_price + $row->jul_price + $row->aug_price + $row->sep_price + $row->oct_price + $row->nov_price + $row->dec_price) / 12;

			$data['ctg_barbecue_prev_year2_min'] = min($row->jan_price, $row->feb_price, $row->mar_price, $row->apr_price, $row->may_price, $row->jun_price, $row->jul_price, $row->aug_price, $row->sep_price, $row->oct_price, $row->nov_price, $row->dec_price);

			$data['ctg_barbecue_prev_year2_max'] = max($row->jan_price, $row->feb_price, $row->mar_price, $row->apr_price, $row->may_price, $row->jun_price, $row->jul_price, $row->aug_price, $row->sep_price, $row->oct_price, $row->nov_price, $row->dec_price);
		}


		$ctg_barbecue_prev_year1 = $this->admin->get_data('comparative_price_tbl', array('comp_price_product' => 'BBQ', 'comp_price_segment' => 'CTG', 'comp_price_year' => $year - 1,	'bc_id'	=>	$bc_id), 
			FALSE, 'AVG(comp_price_jan) as jan_price, AVG(comp_price_feb) as feb_price, AVG(comp_price_mar) as mar_price, AVG(comp_price_apr) as apr_price, AVG(comp_price_may) as may_price, AVG(comp_price_jun) as jun_price, AVG(comp_price_jul) as jul_price, AVG(comp_price_aug) as aug_price, AVG(comp_price_sep) as sep_price, AVG(comp_price_oct) as oct_price, AVG(comp_price_nov) as nov_price, AVG(comp_price_dec) as dec_price');
		
		$data['ctg_barbecue_prev_year1_jan'] = 0;
		$data['ctg_barbecue_prev_year1_feb'] = 0;
		$data['ctg_barbecue_prev_year1_mar'] = 0;
		$data['ctg_barbecue_prev_year1_apr'] = 0;
		$data['ctg_barbecue_prev_year1_may'] = 0;
		$data['ctg_barbecue_prev_year1_jun'] = 0;
		$data['ctg_barbecue_prev_year1_jul'] = 0;
		$data['ctg_barbecue_prev_year1_aug'] = 0;
		$data['ctg_barbecue_prev_year1_sep'] = 0;
		$data['ctg_barbecue_prev_year1_oct'] = 0;
		$data['ctg_barbecue_prev_year1_nov'] = 0;
		$data['ctg_barbecue_prev_year1_dec'] = 0;
		$data['ctg_barbecue_prev_year1_avg'] = 0;
		$data['ctg_barbecue_prev_year1_min'] = 0;
		$data['ctg_barbecue_prev_year1_max'] = 0;

		foreach($ctg_barbecue_prev_year1 as $row){
			$data['ctg_barbecue_prev_year1_jan'] = $row->jan_price;
			$data['ctg_barbecue_prev_year1_feb'] = $row->feb_price;
			$data['ctg_barbecue_prev_year1_mar'] = $row->mar_price;
			$data['ctg_barbecue_prev_year1_apr'] = $row->apr_price;
			$data['ctg_barbecue_prev_year1_may'] = $row->may_price;
			$data['ctg_barbecue_prev_year1_jun'] = $row->jun_price;
			$data['ctg_barbecue_prev_year1_jul'] = $row->jul_price;
			$data['ctg_barbecue_prev_year1_aug'] = $row->aug_price;
			$data['ctg_barbecue_prev_year1_sep'] = $row->sep_price;
			$data['ctg_barbecue_prev_year1_oct'] = $row->oct_price;
			$data['ctg_barbecue_prev_year1_nov'] = $row->nov_price;
			$data['ctg_barbecue_prev_year1_dec'] = $row->dec_price;
			$data['ctg_barbecue_prev_year1_avg'] = ($row->jan_price + $row->feb_price + $row->mar_price + $row->apr_price + $row->may_price + $row->jun_price + $row->jul_price + $row->aug_price + $row->sep_price + $row->oct_price + $row->nov_price + $row->dec_price) / 12;

			$data['ctg_barbecue_prev_year1_min'] = min($row->jan_price, $row->feb_price, $row->mar_price, $row->apr_price, $row->may_price, $row->jun_price, $row->jul_price, $row->aug_price, $row->sep_price, $row->oct_price, $row->nov_price, $row->dec_price);

			$data['ctg_barbecue_prev_year1_max'] = max($row->jan_price, $row->feb_price, $row->mar_price, $row->apr_price, $row->may_price, $row->jun_price, $row->jul_price, $row->aug_price, $row->sep_price, $row->oct_price, $row->nov_price, $row->dec_price);
		}

		/*Nuggets*/
		$join_ctg_nuggets = array(
			'sales_item_tbl b' => 'a.sales_id = b.sales_id AND a.sales_status = 1 AND b.sales_item_status = 1 AND a.sales_year = ' . $year,
			'sales_details_tbl c' => 'b.sales_item_id = c.sales_item_id AND c.sales_det_status = 1',
			'material_tbl d' => 'b.material_id = d.material_id AND (d.material_code = 40000016 OR d.material_code = 40000019)',
			'material_group_tbl e' => 'd.material_group_id = e.material_group_id AND e.material_group_name = \'OTHER VAP PRODUCTS\'',
			'outlet_tbl f' => 'a.outlet_id = f.outlet_id AND f.bc_id = ' . $bc_id,
			'outlet_brand_tbl g' => 'f.outlet_id = g.outlet_id AND g.outlet_brand_status = 1',
			'brand_tbl h' => 'g.brand_id  = h.brand_id AND h.brand_name = \'CHOOKS-TO-GO\''
		);

		$get_ctg_nuggets = $this->admin->get_join('sales_tbl a', $join_ctg_nuggets, FALSE, FALSE, 'YEAR(c.sales_det_date), MONTH(c.sales_det_date)', 'AVG(c.sales_det_asp) as asp, MONTH(c.sales_det_date) asp_date');

		$data['ctg_nuggets1'] = 0;
		$data['ctg_nuggets2'] = 0;
		$data['ctg_nuggets3'] = 0;
		$data['ctg_nuggets4'] = 0;
		$data['ctg_nuggets5'] = 0;
		$data['ctg_nuggets6'] = 0;
		$data['ctg_nuggets7'] = 0;
		$data['ctg_nuggets8'] = 0;
		$data['ctg_nuggets9'] = 0;
		$data['ctg_nuggets10'] = 0;
		$data['ctg_nuggets11'] = 0;
		$data['ctg_nuggets12'] = 0;
		$data['ctg_nuggets_total'] = 0;
		$data['ctg_nuggets_count'] = 0;
		$data['ctg_nuggets_avg'] = 0;
		$data['ctg_nuggets_min'] = 0;
		$data['ctg_nuggets_max'] = 0;

		foreach($get_ctg_nuggets as $row){
			$asp = $row->asp;
			$month = $row->asp_date;
			$data['ctg_nuggets' . $month] = $asp;
			$data['ctg_nuggets_total'] += $asp;

			if($asp < $data['ctg_nuggets_min'] || $data['ctg_nuggets_count'] == 0){
				$data['ctg_nuggets_min'] = $asp;
			}

			if($asp > $data['ctg_nuggets_max'] || $data['ctg_nuggets_count'] == 0){
				$data['ctg_nuggets_max'] = $asp;
			}

			$data['ctg_nuggets_count']++;
		}

		$data['ctg_nuggets_avg'] = $data['ctg_nuggets_total'] != 0 ? $data['ctg_nuggets_total'] / $data['ctg_nuggets_count'] : 0;


		$ctg_nuggets_prev_year2 = $this->admin->get_data('comparative_price_tbl', array('comp_price_product' => 'VAP-Nuggets', 'comp_price_segment' => 'CTG', 'comp_price_year' => $year - 2,	'bc_id'	=>	$bc_id), 
			FALSE, 'AVG(comp_price_jan) as jan_price, AVG(comp_price_feb) as feb_price, AVG(comp_price_mar) as mar_price, AVG(comp_price_apr) as apr_price, AVG(comp_price_may) as may_price, AVG(comp_price_jun) as jun_price, AVG(comp_price_jul) as jul_price, AVG(comp_price_aug) as aug_price, AVG(comp_price_sep) as sep_price, AVG(comp_price_oct) as oct_price, AVG(comp_price_nov) as nov_price, AVG(comp_price_dec) as dec_price');
		
		$data['ctg_nuggets_prev_year2_jan'] = 0;
		$data['ctg_nuggets_prev_year2_feb'] = 0;
		$data['ctg_nuggets_prev_year2_mar'] = 0;
		$data['ctg_nuggets_prev_year2_apr'] = 0;
		$data['ctg_nuggets_prev_year2_may'] = 0;
		$data['ctg_nuggets_prev_year2_jun'] = 0;
		$data['ctg_nuggets_prev_year2_jul'] = 0;
		$data['ctg_nuggets_prev_year2_aug'] = 0;
		$data['ctg_nuggets_prev_year2_sep'] = 0;
		$data['ctg_nuggets_prev_year2_oct'] = 0;
		$data['ctg_nuggets_prev_year2_nov'] = 0;
		$data['ctg_nuggets_prev_year2_dec'] = 0;
		$data['ctg_nuggets_prev_year2_avg'] = 0;
		$data['ctg_nuggets_prev_year2_min'] = 0;
		$data['ctg_nuggets_prev_year2_max'] = 0;

		foreach($ctg_nuggets_prev_year2 as $row){
			$data['ctg_nuggets_prev_year2_jan'] = $row->jan_price;
			$data['ctg_nuggets_prev_year2_feb'] = $row->feb_price;
			$data['ctg_nuggets_prev_year2_mar'] = $row->mar_price;
			$data['ctg_nuggets_prev_year2_apr'] = $row->apr_price;
			$data['ctg_nuggets_prev_year2_may'] = $row->may_price;
			$data['ctg_nuggets_prev_year2_jun'] = $row->jun_price;
			$data['ctg_nuggets_prev_year2_jul'] = $row->jul_price;
			$data['ctg_nuggets_prev_year2_aug'] = $row->aug_price;
			$data['ctg_nuggets_prev_year2_sep'] = $row->sep_price;
			$data['ctg_nuggets_prev_year2_oct'] = $row->oct_price;
			$data['ctg_nuggets_prev_year2_nov'] = $row->nov_price;
			$data['ctg_nuggets_prev_year2_dec'] = $row->dec_price;
			$data['ctg_nuggets_prev_year2_avg'] = ($row->jan_price + $row->feb_price + $row->mar_price + $row->apr_price + $row->may_price + $row->jun_price + $row->jul_price + $row->aug_price + $row->sep_price + $row->oct_price + $row->nov_price + $row->dec_price) / 12;

			$data['ctg_nuggets_prev_year2_min'] = min($row->jan_price, $row->feb_price, $row->mar_price, $row->apr_price, $row->may_price, $row->jun_price, $row->jul_price, $row->aug_price, $row->sep_price, $row->oct_price, $row->nov_price, $row->dec_price);

			$data['ctg_nuggets_prev_year2_max'] = max($row->jan_price, $row->feb_price, $row->mar_price, $row->apr_price, $row->may_price, $row->jun_price, $row->jul_price, $row->aug_price, $row->sep_price, $row->oct_price, $row->nov_price, $row->dec_price);
		}


		$ctg_nuggets_prev_year1 = $this->admin->get_data('comparative_price_tbl', array('comp_price_product' => 'VAP-Nuggets', 'comp_price_segment' => 'CTG', 'comp_price_year' => $year - 1,	'bc_id'	=>	$bc_id), 
			FALSE, 'AVG(comp_price_jan) as jan_price, AVG(comp_price_feb) as feb_price, AVG(comp_price_mar) as mar_price, AVG(comp_price_apr) as apr_price, AVG(comp_price_may) as may_price, AVG(comp_price_jun) as jun_price, AVG(comp_price_jul) as jul_price, AVG(comp_price_aug) as aug_price, AVG(comp_price_sep) as sep_price, AVG(comp_price_oct) as oct_price, AVG(comp_price_nov) as nov_price, AVG(comp_price_dec) as dec_price');
		
		$data['ctg_nuggets_prev_year1_jan'] = 0;
		$data['ctg_nuggets_prev_year1_feb'] = 0;
		$data['ctg_nuggets_prev_year1_mar'] = 0;
		$data['ctg_nuggets_prev_year1_apr'] = 0;
		$data['ctg_nuggets_prev_year1_may'] = 0;
		$data['ctg_nuggets_prev_year1_jun'] = 0;
		$data['ctg_nuggets_prev_year1_jul'] = 0;
		$data['ctg_nuggets_prev_year1_aug'] = 0;
		$data['ctg_nuggets_prev_year1_sep'] = 0;
		$data['ctg_nuggets_prev_year1_oct'] = 0;
		$data['ctg_nuggets_prev_year1_nov'] = 0;
		$data['ctg_nuggets_prev_year1_dec'] = 0;
		$data['ctg_nuggets_prev_year1_avg'] = 0;
		$data['ctg_nuggets_prev_year1_min'] = 0;
		$data['ctg_nuggets_prev_year1_max'] = 0;

		foreach($ctg_nuggets_prev_year1 as $row){
			$data['ctg_nuggets_prev_year1_jan'] = $row->jan_price;
			$data['ctg_nuggets_prev_year1_feb'] = $row->feb_price;
			$data['ctg_nuggets_prev_year1_mar'] = $row->mar_price;
			$data['ctg_nuggets_prev_year1_apr'] = $row->apr_price;
			$data['ctg_nuggets_prev_year1_may'] = $row->may_price;
			$data['ctg_nuggets_prev_year1_jun'] = $row->jun_price;
			$data['ctg_nuggets_prev_year1_jul'] = $row->jul_price;
			$data['ctg_nuggets_prev_year1_aug'] = $row->aug_price;
			$data['ctg_nuggets_prev_year1_sep'] = $row->sep_price;
			$data['ctg_nuggets_prev_year1_oct'] = $row->oct_price;
			$data['ctg_nuggets_prev_year1_nov'] = $row->nov_price;
			$data['ctg_nuggets_prev_year1_dec'] = $row->dec_price;
			$data['ctg_nuggets_prev_year1_avg'] = ($row->jan_price + $row->feb_price + $row->mar_price + $row->apr_price + $row->may_price + $row->jun_price + $row->jul_price + $row->aug_price + $row->sep_price + $row->oct_price + $row->nov_price + $row->dec_price) / 12;

			$data['ctg_nuggets_prev_year1_min'] = min($row->jan_price, $row->feb_price, $row->mar_price, $row->apr_price, $row->may_price, $row->jun_price, $row->jul_price, $row->aug_price, $row->sep_price, $row->oct_price, $row->nov_price, $row->dec_price);

			$data['ctg_nuggets_prev_year1_max'] = max($row->jan_price, $row->feb_price, $row->mar_price, $row->apr_price, $row->may_price, $row->jun_price, $row->jul_price, $row->aug_price, $row->sep_price, $row->oct_price, $row->nov_price, $row->dec_price);
		}


		/*Marinated Raw*/
		$join_ctg_raw = array(
			'sales_item_tbl b' => 'a.sales_id = b.sales_id AND a.sales_status = 1 AND b.sales_item_status = 1 AND a.sales_year = ' . $year,
			'sales_details_tbl c' => 'b.sales_item_id = c.sales_item_id AND c.sales_det_status = 1',
			'material_tbl d' => 'b.material_id = d.material_id',
			'material_group_tbl e' => 'd.material_group_id = e.material_group_id AND e.material_group_name = \'MARINATED CHICKEN (RAW)\'',
			'outlet_tbl f' => 'a.outlet_id = f.outlet_id AND f.bc_id = ' . $bc_id,
			'outlet_brand_tbl g' => 'f.outlet_id = g.outlet_id AND g.outlet_brand_status = 1',
			'brand_tbl h' => 'g.brand_id  = h.brand_id AND h.brand_name = \'CHOOKS-TO-GO\''
		);

		$get_ctg_raw = $this->admin->get_join('sales_tbl a', $join_ctg_raw, FALSE, FALSE, 'YEAR(c.sales_det_date), MONTH(c.sales_det_date)', 'AVG(c.sales_det_asp) as asp, MONTH(c.sales_det_date) asp_date');

		$data['ctg_raw1'] = 0;
		$data['ctg_raw2'] = 0;
		$data['ctg_raw3'] = 0;
		$data['ctg_raw4'] = 0;
		$data['ctg_raw5'] = 0;
		$data['ctg_raw6'] = 0;
		$data['ctg_raw7'] = 0;
		$data['ctg_raw8'] = 0;
		$data['ctg_raw9'] = 0;
		$data['ctg_raw10'] = 0;
		$data['ctg_raw11'] = 0;
		$data['ctg_raw12'] = 0;
		$data['ctg_raw_total'] = 0;
		$data['ctg_raw_count'] = 0;
		$data['ctg_raw_avg'] = 0;
		$data['ctg_raw_min'] = 0;
		$data['ctg_raw_max'] = 0;

		foreach($get_ctg_raw as $row){
			$asp = $row->asp;
			$month = $row->asp_date;
			$data['ctg_raw' . $month] = $asp;
			$data['ctg_raw_total'] += $asp;

			if($asp < $data['ctg_raw_min'] || $data['ctg_raw_count'] == 0){
				$data['ctg_raw_min'] = $asp;
			}

			if($asp > $data['ctg_raw_max'] || $data['ctg_raw_count'] == 0){
				$data['ctg_raw_max'] = $asp;
			}

			$data['ctg_raw_count']++;
		}

		$data['ctg_raw_avg'] = $data['ctg_raw_total'] != 0 ? $data['ctg_raw_total'] / $data['ctg_raw_count'] : 0;


		$ctg_raw_prev_year2 = $this->admin->get_data('comparative_price_tbl', array('comp_price_product' => 'MARINATED CHICKEN RAW', 'comp_price_segment' => 'CTG', 'comp_price_year' => $year - 2,	'bc_id'	=>	$bc_id), 
			FALSE, 'AVG(comp_price_jan) as jan_price, AVG(comp_price_feb) as feb_price, AVG(comp_price_mar) as mar_price, AVG(comp_price_apr) as apr_price, AVG(comp_price_may) as may_price, AVG(comp_price_jun) as jun_price, AVG(comp_price_jul) as jul_price, AVG(comp_price_aug) as aug_price, AVG(comp_price_sep) as sep_price, AVG(comp_price_oct) as oct_price, AVG(comp_price_nov) as nov_price, AVG(comp_price_dec) as dec_price');
		
		$data['ctg_raw_prev_year2_jan'] = 0;
		$data['ctg_raw_prev_year2_feb'] = 0;
		$data['ctg_raw_prev_year2_mar'] = 0;
		$data['ctg_raw_prev_year2_apr'] = 0;
		$data['ctg_raw_prev_year2_may'] = 0;
		$data['ctg_raw_prev_year2_jun'] = 0;
		$data['ctg_raw_prev_year2_jul'] = 0;
		$data['ctg_raw_prev_year2_aug'] = 0;
		$data['ctg_raw_prev_year2_sep'] = 0;
		$data['ctg_raw_prev_year2_oct'] = 0;
		$data['ctg_raw_prev_year2_nov'] = 0;
		$data['ctg_raw_prev_year2_dec'] = 0;
		$data['ctg_raw_prev_year2_avg'] = 0;
		$data['ctg_raw_prev_year2_min'] = 0;
		$data['ctg_raw_prev_year2_max'] = 0;

		foreach($ctg_raw_prev_year2 as $row){
			$data['ctg_raw_prev_year2_jan'] = $row->jan_price;
			$data['ctg_raw_prev_year2_feb'] = $row->feb_price;
			$data['ctg_raw_prev_year2_mar'] = $row->mar_price;
			$data['ctg_raw_prev_year2_apr'] = $row->apr_price;
			$data['ctg_raw_prev_year2_may'] = $row->may_price;
			$data['ctg_raw_prev_year2_jun'] = $row->jun_price;
			$data['ctg_raw_prev_year2_jul'] = $row->jul_price;
			$data['ctg_raw_prev_year2_aug'] = $row->aug_price;
			$data['ctg_raw_prev_year2_sep'] = $row->sep_price;
			$data['ctg_raw_prev_year2_oct'] = $row->oct_price;
			$data['ctg_raw_prev_year2_nov'] = $row->nov_price;
			$data['ctg_raw_prev_year2_dec'] = $row->dec_price;
			$data['ctg_raw_prev_year2_avg'] = ($row->jan_price + $row->feb_price + $row->mar_price + $row->apr_price + $row->may_price + $row->jun_price + $row->jul_price + $row->aug_price + $row->sep_price + $row->oct_price + $row->nov_price + $row->dec_price) / 12;

			$data['ctg_raw_prev_year2_min'] = min($row->jan_price, $row->feb_price, $row->mar_price, $row->apr_price, $row->may_price, $row->jun_price, $row->jul_price, $row->aug_price, $row->sep_price, $row->oct_price, $row->nov_price, $row->dec_price);

			$data['ctg_raw_prev_year2_max'] = max($row->jan_price, $row->feb_price, $row->mar_price, $row->apr_price, $row->may_price, $row->jun_price, $row->jul_price, $row->aug_price, $row->sep_price, $row->oct_price, $row->nov_price, $row->dec_price);
		}


		$ctg_raw_prev_year1 = $this->admin->get_data('comparative_price_tbl', array('comp_price_product' => 'MARINATED CHICKEN RAW', 'comp_price_segment' => 'CTG', 'comp_price_year' => $year - 1,	'bc_id'	=>	$bc_id), 
			FALSE, 'AVG(comp_price_jan) as jan_price, AVG(comp_price_feb) as feb_price, AVG(comp_price_mar) as mar_price, AVG(comp_price_apr) as apr_price, AVG(comp_price_may) as may_price, AVG(comp_price_jun) as jun_price, AVG(comp_price_jul) as jul_price, AVG(comp_price_aug) as aug_price, AVG(comp_price_sep) as sep_price, AVG(comp_price_oct) as oct_price, AVG(comp_price_nov) as nov_price, AVG(comp_price_dec) as dec_price');
		
		$data['ctg_raw_prev_year1_jan'] = 0;
		$data['ctg_raw_prev_year1_feb'] = 0;
		$data['ctg_raw_prev_year1_mar'] = 0;
		$data['ctg_raw_prev_year1_apr'] = 0;
		$data['ctg_raw_prev_year1_may'] = 0;
		$data['ctg_raw_prev_year1_jun'] = 0;
		$data['ctg_raw_prev_year1_jul'] = 0;
		$data['ctg_raw_prev_year1_aug'] = 0;
		$data['ctg_raw_prev_year1_sep'] = 0;
		$data['ctg_raw_prev_year1_oct'] = 0;
		$data['ctg_raw_prev_year1_nov'] = 0;
		$data['ctg_raw_prev_year1_dec'] = 0;
		$data['ctg_raw_prev_year1_avg'] = 0;
		$data['ctg_raw_prev_year1_min'] = 0;
		$data['ctg_raw_prev_year1_max'] = 0;

		foreach($ctg_raw_prev_year1 as $row){
			$data['ctg_raw_prev_year1_jan'] = $row->jan_price;
			$data['ctg_raw_prev_year1_feb'] = $row->feb_price;
			$data['ctg_raw_prev_year1_mar'] = $row->mar_price;
			$data['ctg_raw_prev_year1_apr'] = $row->apr_price;
			$data['ctg_raw_prev_year1_may'] = $row->may_price;
			$data['ctg_raw_prev_year1_jun'] = $row->jun_price;
			$data['ctg_raw_prev_year1_jul'] = $row->jul_price;
			$data['ctg_raw_prev_year1_aug'] = $row->aug_price;
			$data['ctg_raw_prev_year1_sep'] = $row->sep_price;
			$data['ctg_raw_prev_year1_oct'] = $row->oct_price;
			$data['ctg_raw_prev_year1_nov'] = $row->nov_price;
			$data['ctg_raw_prev_year1_dec'] = $row->dec_price;
			$data['ctg_raw_prev_year1_avg'] = ($row->jan_price + $row->feb_price + $row->mar_price + $row->apr_price + $row->may_price + $row->jun_price + $row->jul_price + $row->aug_price + $row->sep_price + $row->oct_price + $row->nov_price + $row->dec_price) / 12;

			$data['ctg_raw_prev_year1_min'] = min($row->jan_price, $row->feb_price, $row->mar_price, $row->apr_price, $row->may_price, $row->jun_price, $row->jul_price, $row->aug_price, $row->sep_price, $row->oct_price, $row->nov_price, $row->dec_price);

			$data['ctg_raw_prev_year1_max'] = max($row->jan_price, $row->feb_price, $row->mar_price, $row->apr_price, $row->may_price, $row->jun_price, $row->jul_price, $row->aug_price, $row->sep_price, $row->oct_price, $row->nov_price, $row->dec_price);
		}



		/*CTG Chooksies Cut ups*/
		$join_ctg_cutups = array(
			'sales_item_tbl b' => 'a.sales_id = b.sales_id AND a.sales_status = 1 AND b.sales_item_status = 1 AND a.sales_year = ' . $year,
			'sales_details_tbl c' => 'b.sales_item_id = c.sales_item_id AND c.sales_det_status = 1',
			'material_tbl d' => 'b.material_id = d.material_id AND d.material_code = 10000150',
			'material_group_tbl e' => 'd.material_group_id = e.material_group_id AND e.material_group_name = \'NON MARINATED CUT UPS\'',
			'outlet_tbl f' => 'a.outlet_id = f.outlet_id AND f.bc_id = ' . $bc_id,
			'outlet_brand_tbl g' => 'f.outlet_id = g.outlet_id AND g.outlet_brand_status = 1',
			'brand_tbl h' => 'g.brand_id  = h.brand_id AND h.brand_name = \'CHOOKS-TO-GO\''
		);

		$get_ctg_cutups = $this->admin->get_join('sales_tbl a', $join_ctg_cutups, FALSE, FALSE, 'YEAR(c.sales_det_date), MONTH(c.sales_det_date)', 'AVG(c.sales_det_asp) as asp, MONTH(c.sales_det_date) asp_date');

		$data['ctg_cutups1'] = 0;
		$data['ctg_cutups2'] = 0;
		$data['ctg_cutups3'] = 0;
		$data['ctg_cutups4'] = 0;
		$data['ctg_cutups5'] = 0;
		$data['ctg_cutups6'] = 0;
		$data['ctg_cutups7'] = 0;
		$data['ctg_cutups8'] = 0;
		$data['ctg_cutups9'] = 0;
		$data['ctg_cutups10'] = 0;
		$data['ctg_cutups11'] = 0;
		$data['ctg_cutups12'] = 0;
		$data['ctg_cutups_total'] = 0;
		$data['ctg_cutups_count'] = 0;
		$data['ctg_cutups_avg'] = 0;
		$data['ctg_cutups_min'] = 0;
		$data['ctg_cutups_max'] = 0;

		foreach($get_ctg_cutups as $row){
			$asp = $row->asp;
			$month = $row->asp_date;
			$data['ctg_cutups' . $month] = $asp;
			$data['ctg_cutups_total'] += $asp;

			if($asp < $data['ctg_cutups_min'] || $data['ctg_cutups_count'] == 0){
				$data['ctg_cutups_min'] = $asp;
			}

			if($asp > $data['ctg_cutups_max'] || $data['ctg_cutups_count'] == 0){
				$data['ctg_cutups_max'] = $asp;
			}

			$data['ctg_cutups_count']++;
		}

		$data['ctg_cutups_avg'] = $data['ctg_cutups_total'] != 0 ? $data['ctg_cutups_total'] / $data['ctg_cutups_count'] : 0;

		$ctg_cutups_prev_year2 = $this->admin->get_data('comparative_price_tbl', array('comp_price_product' => 'CHOOKSIES CUT UPS', 'comp_price_segment' => 'CTG', 'comp_price_year' => $year - 2,	'bc_id'	=>	$bc_id), 
			FALSE, 'AVG(comp_price_jan) as jan_price, AVG(comp_price_feb) as feb_price, AVG(comp_price_mar) as mar_price, AVG(comp_price_apr) as apr_price, AVG(comp_price_may) as may_price, AVG(comp_price_jun) as jun_price, AVG(comp_price_jul) as jul_price, AVG(comp_price_aug) as aug_price, AVG(comp_price_sep) as sep_price, AVG(comp_price_oct) as oct_price, AVG(comp_price_nov) as nov_price, AVG(comp_price_dec) as dec_price');
		
		$data['ctg_cutups_prev_year2_jan'] = 0;
		$data['ctg_cutups_prev_year2_feb'] = 0;
		$data['ctg_cutups_prev_year2_mar'] = 0;
		$data['ctg_cutups_prev_year2_apr'] = 0;
		$data['ctg_cutups_prev_year2_may'] = 0;
		$data['ctg_cutups_prev_year2_jun'] = 0;
		$data['ctg_cutups_prev_year2_jul'] = 0;
		$data['ctg_cutups_prev_year2_aug'] = 0;
		$data['ctg_cutups_prev_year2_sep'] = 0;
		$data['ctg_cutups_prev_year2_oct'] = 0;
		$data['ctg_cutups_prev_year2_nov'] = 0;
		$data['ctg_cutups_prev_year2_dec'] = 0;
		$data['ctg_cutups_prev_year2_avg'] = 0;
		$data['ctg_cutups_prev_year2_min'] = 0;
		$data['ctg_cutups_prev_year2_max'] = 0;

		foreach($ctg_cutups_prev_year2 as $row){
			$data['ctg_cutups_prev_year2_jan'] = $row->jan_price;
			$data['ctg_cutups_prev_year2_feb'] = $row->feb_price;
			$data['ctg_cutups_prev_year2_mar'] = $row->mar_price;
			$data['ctg_cutups_prev_year2_apr'] = $row->apr_price;
			$data['ctg_cutups_prev_year2_may'] = $row->may_price;
			$data['ctg_cutups_prev_year2_jun'] = $row->jun_price;
			$data['ctg_cutups_prev_year2_jul'] = $row->jul_price;
			$data['ctg_cutups_prev_year2_aug'] = $row->aug_price;
			$data['ctg_cutups_prev_year2_sep'] = $row->sep_price;
			$data['ctg_cutups_prev_year2_oct'] = $row->oct_price;
			$data['ctg_cutups_prev_year2_nov'] = $row->nov_price;
			$data['ctg_cutups_prev_year2_dec'] = $row->dec_price;
			$data['ctg_cutups_prev_year2_avg'] = ($row->jan_price + $row->feb_price + $row->mar_price + $row->apr_price + $row->may_price + $row->jun_price + $row->jul_price + $row->aug_price + $row->sep_price + $row->oct_price + $row->nov_price + $row->dec_price) / 12;

			$data['ctg_cutups_prev_year2_min'] = min($row->jan_price, $row->feb_price, $row->mar_price, $row->apr_price, $row->may_price, $row->jun_price, $row->jul_price, $row->aug_price, $row->sep_price, $row->oct_price, $row->nov_price, $row->dec_price);

			$data['ctg_cutups_prev_year2_max'] = max($row->jan_price, $row->feb_price, $row->mar_price, $row->apr_price, $row->may_price, $row->jun_price, $row->jul_price, $row->aug_price, $row->sep_price, $row->oct_price, $row->nov_price, $row->dec_price);
		}


		$ctg_cutups_prev_year1 = $this->admin->get_data('comparative_price_tbl', array('comp_price_product' => 'CHOOKSIES CUT UPS', 'comp_price_segment' => 'CTG', 'comp_price_year' => $year - 1,	'bc_id'	=>	$bc_id), 
			FALSE, 'AVG(comp_price_jan) as jan_price, AVG(comp_price_feb) as feb_price, AVG(comp_price_mar) as mar_price, AVG(comp_price_apr) as apr_price, AVG(comp_price_may) as may_price, AVG(comp_price_jun) as jun_price, AVG(comp_price_jul) as jul_price, AVG(comp_price_aug) as aug_price, AVG(comp_price_sep) as sep_price, AVG(comp_price_oct) as oct_price, AVG(comp_price_nov) as nov_price, AVG(comp_price_dec) as dec_price');
		
		$data['ctg_cutups_prev_year1_jan'] = 0;
		$data['ctg_cutups_prev_year1_feb'] = 0;
		$data['ctg_cutups_prev_year1_mar'] = 0;
		$data['ctg_cutups_prev_year1_apr'] = 0;
		$data['ctg_cutups_prev_year1_may'] = 0;
		$data['ctg_cutups_prev_year1_jun'] = 0;
		$data['ctg_cutups_prev_year1_jul'] = 0;
		$data['ctg_cutups_prev_year1_aug'] = 0;
		$data['ctg_cutups_prev_year1_sep'] = 0;
		$data['ctg_cutups_prev_year1_oct'] = 0;
		$data['ctg_cutups_prev_year1_nov'] = 0;
		$data['ctg_cutups_prev_year1_dec'] = 0;
		$data['ctg_cutups_prev_year1_avg'] = 0;
		$data['ctg_cutups_prev_year1_min'] = 0;
		$data['ctg_cutups_prev_year1_max'] = 0;

		foreach($ctg_cutups_prev_year1 as $row){
			$data['ctg_cutups_prev_year1_jan'] = $row->jan_price;
			$data['ctg_cutups_prev_year1_feb'] = $row->feb_price;
			$data['ctg_cutups_prev_year1_mar'] = $row->mar_price;
			$data['ctg_cutups_prev_year1_apr'] = $row->apr_price;
			$data['ctg_cutups_prev_year1_may'] = $row->may_price;
			$data['ctg_cutups_prev_year1_jun'] = $row->jun_price;
			$data['ctg_cutups_prev_year1_jul'] = $row->jul_price;
			$data['ctg_cutups_prev_year1_aug'] = $row->aug_price;
			$data['ctg_cutups_prev_year1_sep'] = $row->sep_price;
			$data['ctg_cutups_prev_year1_oct'] = $row->oct_price;
			$data['ctg_cutups_prev_year1_nov'] = $row->nov_price;
			$data['ctg_cutups_prev_year1_dec'] = $row->dec_price;
			$data['ctg_cutups_prev_year1_avg'] = ($row->jan_price + $row->feb_price + $row->mar_price + $row->apr_price + $row->may_price + $row->jun_price + $row->jul_price + $row->aug_price + $row->sep_price + $row->oct_price + $row->nov_price + $row->dec_price) / 12;

			$data['ctg_cutups_prev_year1_min'] = min($row->jan_price, $row->feb_price, $row->mar_price, $row->apr_price, $row->may_price, $row->jun_price, $row->jul_price, $row->aug_price, $row->sep_price, $row->oct_price, $row->nov_price, $row->dec_price);

			$data['ctg_cutups_prev_year1_max'] = max($row->jan_price, $row->feb_price, $row->mar_price, $row->apr_price, $row->may_price, $row->jun_price, $row->jul_price, $row->aug_price, $row->sep_price, $row->oct_price, $row->nov_price, $row->dec_price);
		}


		/*CTG Liver / Gizzard*/
		$join_ctg_liver = array(
			'sales_item_tbl b' => 'a.sales_id = b.sales_id AND a.sales_status = 1 AND b.sales_item_status = 1 AND a.sales_year = ' . $year,
			'sales_details_tbl c' => 'b.sales_item_id = c.sales_item_id AND c.sales_det_status = 1',
			'material_tbl d' => 'b.material_id = d.material_id',
			'material_group_tbl e' => 'd.material_group_id = e.material_group_id AND e.material_group_name = \'LIVER / GIZZARD\'',
			'outlet_tbl f' => 'a.outlet_id = f.outlet_id AND f.bc_id = ' . $bc_id,
			'outlet_brand_tbl g' => 'f.outlet_id = g.outlet_id AND g.outlet_brand_status = 1',
			'brand_tbl h' => 'g.brand_id  = h.brand_id AND h.brand_name = \'CHOOKS-TO-GO\''
		);

		$get_ctg_liver = $this->admin->get_join('sales_tbl a', $join_ctg_liver, FALSE, FALSE, 'YEAR(c.sales_det_date), MONTH(c.sales_det_date)', 'AVG(c.sales_det_asp) as asp, MONTH(c.sales_det_date) asp_date');

		$data['ctg_liver1'] = 0;
		$data['ctg_liver2'] = 0;
		$data['ctg_liver3'] = 0;
		$data['ctg_liver4'] = 0;
		$data['ctg_liver5'] = 0;
		$data['ctg_liver6'] = 0;
		$data['ctg_liver7'] = 0;
		$data['ctg_liver8'] = 0;
		$data['ctg_liver9'] = 0;
		$data['ctg_liver10'] = 0;
		$data['ctg_liver11'] = 0;
		$data['ctg_liver12'] = 0;
		$data['ctg_liver_total'] = 0;
		$data['ctg_liver_count'] = 0;
		$data['ctg_liver_avg'] = 0;
		$data['ctg_liver_min'] = 0;
		$data['ctg_liver_max'] = 0;

		foreach($get_ctg_liver as $row){
			$asp = $row->asp;
			$month = $row->asp_date;
			$data['ctg_liver' . $month] = $asp;
			$data['ctg_liver_total'] += $asp;

			if($asp < $data['ctg_liver_min'] || $data['ctg_liver_count'] == 0){
				$data['ctg_cutups_min'] = $asp;
			}

			if($asp > $data['ctg_liver_max'] || $data['ctg_liver_count'] == 0){
				$data['ctg_liver_max'] = $asp;
			}

			$data['ctg_liver_count']++;
		}

		$data['ctg_liver_avg'] = $data['ctg_liver_total'] != 0 ? $data['ctg_liver_total'] / $data['ctg_liver_count'] : 0;


		$ctg_liver_prev_year2 = $this->admin->get_data('comparative_price_tbl', array('comp_price_product' => 'GIZZARD / LIVER', 'comp_price_segment' => 'CTG', 'comp_price_year' => $year - 2,	'bc_id'	=>	$bc_id), 
			FALSE, 'AVG(comp_price_jan) as jan_price, AVG(comp_price_feb) as feb_price, AVG(comp_price_mar) as mar_price, AVG(comp_price_apr) as apr_price, AVG(comp_price_may) as may_price, AVG(comp_price_jun) as jun_price, AVG(comp_price_jul) as jul_price, AVG(comp_price_aug) as aug_price, AVG(comp_price_sep) as sep_price, AVG(comp_price_oct) as oct_price, AVG(comp_price_nov) as nov_price, AVG(comp_price_dec) as dec_price');
		
		$data['ctg_liver_prev_year2_jan'] = 0;
		$data['ctg_liver_prev_year2_feb'] = 0;
		$data['ctg_liver_prev_year2_mar'] = 0;
		$data['ctg_liver_prev_year2_apr'] = 0;
		$data['ctg_liver_prev_year2_may'] = 0;
		$data['ctg_liver_prev_year2_jun'] = 0;
		$data['ctg_liver_prev_year2_jul'] = 0;
		$data['ctg_liver_prev_year2_aug'] = 0;
		$data['ctg_liver_prev_year2_sep'] = 0;
		$data['ctg_liver_prev_year2_oct'] = 0;
		$data['ctg_liver_prev_year2_nov'] = 0;
		$data['ctg_liver_prev_year2_dec'] = 0;
		$data['ctg_liver_prev_year2_avg'] = 0;
		$data['ctg_liver_prev_year2_min'] = 0;
		$data['ctg_liver_prev_year2_max'] = 0;

		foreach($ctg_liver_prev_year2 as $row){
			$data['ctg_liver_prev_year2_jan'] = $row->jan_price;
			$data['ctg_liver_prev_year2_feb'] = $row->feb_price;
			$data['ctg_liver_prev_year2_mar'] = $row->mar_price;
			$data['ctg_liver_prev_year2_apr'] = $row->apr_price;
			$data['ctg_liver_prev_year2_may'] = $row->may_price;
			$data['ctg_liver_prev_year2_jun'] = $row->jun_price;
			$data['ctg_liver_prev_year2_jul'] = $row->jul_price;
			$data['ctg_liver_prev_year2_aug'] = $row->aug_price;
			$data['ctg_liver_prev_year2_sep'] = $row->sep_price;
			$data['ctg_liver_prev_year2_oct'] = $row->oct_price;
			$data['ctg_liver_prev_year2_nov'] = $row->nov_price;
			$data['ctg_liver_prev_year2_dec'] = $row->dec_price;
			$data['ctg_liver_prev_year2_avg'] = ($row->jan_price + $row->feb_price + $row->mar_price + $row->apr_price + $row->may_price + $row->jun_price + $row->jul_price + $row->aug_price + $row->sep_price + $row->oct_price + $row->nov_price + $row->dec_price) / 12;

			$data['ctg_liver_prev_year2_min'] = min($row->jan_price, $row->feb_price, $row->mar_price, $row->apr_price, $row->may_price, $row->jun_price, $row->jul_price, $row->aug_price, $row->sep_price, $row->oct_price, $row->nov_price, $row->dec_price);

			$data['ctg_liver_prev_year2_max'] = max($row->jan_price, $row->feb_price, $row->mar_price, $row->apr_price, $row->may_price, $row->jun_price, $row->jul_price, $row->aug_price, $row->sep_price, $row->oct_price, $row->nov_price, $row->dec_price);
		}


		$ctg_liver_prev_year1 = $this->admin->get_data('comparative_price_tbl', array('comp_price_product' => 'GIZZARD / LIVER', 'comp_price_segment' => 'CTG', 'comp_price_year' => $year - 1,	'bc_id'	=>	$bc_id), 
			FALSE, 'AVG(comp_price_jan) as jan_price, AVG(comp_price_feb) as feb_price, AVG(comp_price_mar) as mar_price, AVG(comp_price_apr) as apr_price, AVG(comp_price_may) as may_price, AVG(comp_price_jun) as jun_price, AVG(comp_price_jul) as jul_price, AVG(comp_price_aug) as aug_price, AVG(comp_price_sep) as sep_price, AVG(comp_price_oct) as oct_price, AVG(comp_price_nov) as nov_price, AVG(comp_price_dec) as dec_price');
		
		$data['ctg_liver_prev_year1_jan'] = 0;
		$data['ctg_liver_prev_year1_feb'] = 0;
		$data['ctg_liver_prev_year1_mar'] = 0;
		$data['ctg_liver_prev_year1_apr'] = 0;
		$data['ctg_liver_prev_year1_may'] = 0;
		$data['ctg_liver_prev_year1_jun'] = 0;
		$data['ctg_liver_prev_year1_jul'] = 0;
		$data['ctg_liver_prev_year1_aug'] = 0;
		$data['ctg_liver_prev_year1_sep'] = 0;
		$data['ctg_liver_prev_year1_oct'] = 0;
		$data['ctg_liver_prev_year1_nov'] = 0;
		$data['ctg_liver_prev_year1_dec'] = 0;
		$data['ctg_liver_prev_year1_avg'] = 0;
		$data['ctg_liver_prev_year1_min'] = 0;
		$data['ctg_liver_prev_year1_max'] = 0;

		foreach($ctg_liver_prev_year1 as $row){
			$data['ctg_liver_prev_year1_jan'] = $row->jan_price;
			$data['ctg_liver_prev_year1_feb'] = $row->feb_price;
			$data['ctg_liver_prev_year1_mar'] = $row->mar_price;
			$data['ctg_liver_prev_year1_apr'] = $row->apr_price;
			$data['ctg_liver_prev_year1_may'] = $row->may_price;
			$data['ctg_liver_prev_year1_jun'] = $row->jun_price;
			$data['ctg_liver_prev_year1_jul'] = $row->jul_price;
			$data['ctg_liver_prev_year1_aug'] = $row->aug_price;
			$data['ctg_liver_prev_year1_sep'] = $row->sep_price;
			$data['ctg_liver_prev_year1_oct'] = $row->oct_price;
			$data['ctg_liver_prev_year1_nov'] = $row->nov_price;
			$data['ctg_liver_prev_year1_dec'] = $row->dec_price;
			$data['ctg_liver_prev_year1_avg'] = ($row->jan_price + $row->feb_price + $row->mar_price + $row->apr_price + $row->may_price + $row->jun_price + $row->jul_price + $row->aug_price + $row->sep_price + $row->oct_price + $row->nov_price + $row->dec_price) / 12;

			$data['ctg_liver_prev_year1_min'] = min($row->jan_price, $row->feb_price, $row->mar_price, $row->apr_price, $row->may_price, $row->jun_price, $row->jul_price, $row->aug_price, $row->sep_price, $row->oct_price, $row->nov_price, $row->dec_price);

			$data['ctg_liver_prev_year1_max'] = max($row->jan_price, $row->feb_price, $row->mar_price, $row->apr_price, $row->may_price, $row->jun_price, $row->jul_price, $row->aug_price, $row->sep_price, $row->oct_price, $row->nov_price, $row->dec_price);
		}

		/*11 PC PICA PICA CUTS*/

		$join_ctg_pica = array(
			'sales_item_tbl b' => 'a.sales_id = b.sales_id AND a.sales_status = 1 AND b.sales_item_status = 1 AND a.sales_year = ' . $year,
			'sales_details_tbl c' => 'b.sales_item_id = c.sales_item_id AND c.sales_det_status = 1',
			'material_tbl d' => 'b.material_id = d.material_id AND d.material_code = 10400170',
			'material_group_tbl e' => 'd.material_group_id = e.material_group_id',
			'outlet_tbl f' => 'a.outlet_id = f.outlet_id AND f.bc_id = ' . $bc_id,
			'outlet_brand_tbl g' => 'f.outlet_id = g.outlet_id AND g.outlet_brand_status = 1',
			'brand_tbl h' => 'g.brand_id  = h.brand_id AND h.brand_name = \'CHOOKS-TO-GO\''
		);

		$get_ctg_pica = $this->admin->get_join('sales_tbl a', $join_ctg_pica, FALSE, FALSE, 'YEAR(c.sales_det_date), MONTH(c.sales_det_date)', 'AVG(c.sales_det_asp) as asp, MONTH(c.sales_det_date) asp_date');

		$data['ctg_pica1'] = 0;
		$data['ctg_pica2'] = 0;
		$data['ctg_pica3'] = 0;
		$data['ctg_pica4'] = 0;
		$data['ctg_pica5'] = 0;
		$data['ctg_pica6'] = 0;
		$data['ctg_pica7'] = 0;
		$data['ctg_pica8'] = 0;
		$data['ctg_pica9'] = 0;
		$data['ctg_pica10'] = 0;
		$data['ctg_pica11'] = 0;
		$data['ctg_pica12'] = 0;
		$data['ctg_pica_total'] = 0;
		$data['ctg_pica_count'] = 0;
		$data['ctg_pica_avg'] = 0;
		$data['ctg_pica_min'] = 0;
		$data['ctg_pica_max'] = 0;

		foreach($get_ctg_pica as $row){
			$asp = $row->asp;
			$month = $row->asp_date;
			$data['ctg_pica' . $month] = $asp;
			$data['ctg_pica_total'] += $asp;

			if($asp < $data['ctg_pica_min'] || $data['ctg_pica_count'] == 0){
				$data['ctg_pica_min'] = $asp;
			}

			if($asp > $data['ctg_pica_max'] || $data['ctg_pica_count'] == 0){
				$data['ctg_pica_max'] = $asp;
			}

			$data['ctg_pica_count']++;
		}

		$data['ctg_pica_avg'] = $data['ctg_pica_total'] != 0 ? $data['ctg_pica_total'] / $data['ctg_pica_count'] : 0;


		$ctg_pica_prev_year2 = $this->admin->get_data('comparative_price_tbl', array('comp_price_product' => '11 PC', 'comp_price_segment' => 'CTG', 'comp_price_year' => $year - 2,	'bc_id'	=>	$bc_id), 
			FALSE, 'AVG(comp_price_jan) as jan_price, AVG(comp_price_feb) as feb_price, AVG(comp_price_mar) as mar_price, AVG(comp_price_apr) as apr_price, AVG(comp_price_may) as may_price, AVG(comp_price_jun) as jun_price, AVG(comp_price_jul) as jul_price, AVG(comp_price_aug) as aug_price, AVG(comp_price_sep) as sep_price, AVG(comp_price_oct) as oct_price, AVG(comp_price_nov) as nov_price, AVG(comp_price_dec) as dec_price');
		
		$data['ctg_pica_prev_year2_jan'] = 0;
		$data['ctg_pica_prev_year2_feb'] = 0;
		$data['ctg_pica_prev_year2_mar'] = 0;
		$data['ctg_pica_prev_year2_apr'] = 0;
		$data['ctg_pica_prev_year2_may'] = 0;
		$data['ctg_pica_prev_year2_jun'] = 0;
		$data['ctg_pica_prev_year2_jul'] = 0;
		$data['ctg_pica_prev_year2_aug'] = 0;
		$data['ctg_pica_prev_year2_sep'] = 0;
		$data['ctg_pica_prev_year2_oct'] = 0;
		$data['ctg_pica_prev_year2_nov'] = 0;
		$data['ctg_pica_prev_year2_dec'] = 0;
		$data['ctg_pica_prev_year2_avg'] = 0;
		$data['ctg_pica_prev_year2_min'] = 0;
		$data['ctg_pica_prev_year2_max'] = 0;

		foreach($ctg_pica_prev_year2 as $row){
			$data['ctg_pica_prev_year2_jan'] = $row->jan_price;
			$data['ctg_pica_prev_year2_feb'] = $row->feb_price;
			$data['ctg_pica_prev_year2_mar'] = $row->mar_price;
			$data['ctg_pica_prev_year2_apr'] = $row->apr_price;
			$data['ctg_pica_prev_year2_may'] = $row->may_price;
			$data['ctg_pica_prev_year2_jun'] = $row->jun_price;
			$data['ctg_pica_prev_year2_jul'] = $row->jul_price;
			$data['ctg_pica_prev_year2_aug'] = $row->aug_price;
			$data['ctg_pica_prev_year2_sep'] = $row->sep_price;
			$data['ctg_pica_prev_year2_oct'] = $row->oct_price;
			$data['ctg_pica_prev_year2_nov'] = $row->nov_price;
			$data['ctg_pica_prev_year2_dec'] = $row->dec_price;
			$data['ctg_pica_prev_year2_avg'] = ($row->jan_price + $row->feb_price + $row->mar_price + $row->apr_price + $row->may_price + $row->jun_price + $row->jul_price + $row->aug_price + $row->sep_price + $row->oct_price + $row->nov_price + $row->dec_price) / 12;

			$data['ctg_pica_prev_year2_min'] = min($row->jan_price, $row->feb_price, $row->mar_price, $row->apr_price, $row->may_price, $row->jun_price, $row->jul_price, $row->aug_price, $row->sep_price, $row->oct_price, $row->nov_price, $row->dec_price);

			$data['ctg_pica_prev_year2_max'] = max($row->jan_price, $row->feb_price, $row->mar_price, $row->apr_price, $row->may_price, $row->jun_price, $row->jul_price, $row->aug_price, $row->sep_price, $row->oct_price, $row->nov_price, $row->dec_price);
		}


		$ctg_pica_prev_year1 = $this->admin->get_data('comparative_price_tbl', array('comp_price_product' => '11 PC', 'comp_price_segment' => 'CTG', 'comp_price_year' => $year - 1,	'bc_id'	=>	$bc_id), 
			FALSE, 'AVG(comp_price_jan) as jan_price, AVG(comp_price_feb) as feb_price, AVG(comp_price_mar) as mar_price, AVG(comp_price_apr) as apr_price, AVG(comp_price_may) as may_price, AVG(comp_price_jun) as jun_price, AVG(comp_price_jul) as jul_price, AVG(comp_price_aug) as aug_price, AVG(comp_price_sep) as sep_price, AVG(comp_price_oct) as oct_price, AVG(comp_price_nov) as nov_price, AVG(comp_price_dec) as dec_price');
		
		$data['ctg_pica_prev_year1_jan'] = 0;
		$data['ctg_pica_prev_year1_feb'] = 0;
		$data['ctg_pica_prev_year1_mar'] = 0;
		$data['ctg_pica_prev_year1_apr'] = 0;
		$data['ctg_pica_prev_year1_may'] = 0;
		$data['ctg_pica_prev_year1_jun'] = 0;
		$data['ctg_pica_prev_year1_jul'] = 0;
		$data['ctg_pica_prev_year1_aug'] = 0;
		$data['ctg_pica_prev_year1_sep'] = 0;
		$data['ctg_pica_prev_year1_oct'] = 0;
		$data['ctg_pica_prev_year1_nov'] = 0;
		$data['ctg_pica_prev_year1_dec'] = 0;
		$data['ctg_pica_prev_year1_avg'] = 0;
		$data['ctg_pica_prev_year1_min'] = 0;
		$data['ctg_pica_prev_year1_max'] = 0;

		foreach($ctg_pica_prev_year1 as $row){
			$data['ctg_pica_prev_year1_jan'] = $row->jan_price;
			$data['ctg_pica_prev_year1_feb'] = $row->feb_price;
			$data['ctg_pica_prev_year1_mar'] = $row->mar_price;
			$data['ctg_pica_prev_year1_apr'] = $row->apr_price;
			$data['ctg_pica_prev_year1_may'] = $row->may_price;
			$data['ctg_pica_prev_year1_jun'] = $row->jun_price;
			$data['ctg_pica_prev_year1_jul'] = $row->jul_price;
			$data['ctg_pica_prev_year1_aug'] = $row->aug_price;
			$data['ctg_pica_prev_year1_sep'] = $row->sep_price;
			$data['ctg_pica_prev_year1_oct'] = $row->oct_price;
			$data['ctg_pica_prev_year1_nov'] = $row->nov_price;
			$data['ctg_pica_prev_year1_dec'] = $row->dec_price;
			$data['ctg_pica_prev_year1_avg'] = ($row->jan_price + $row->feb_price + $row->mar_price + $row->apr_price + $row->may_price + $row->jun_price + $row->jul_price + $row->aug_price + $row->sep_price + $row->oct_price + $row->nov_price + $row->dec_price) / 12;

			$data['ctg_pica_prev_year1_min'] = min($row->jan_price, $row->feb_price, $row->mar_price, $row->apr_price, $row->may_price, $row->jun_price, $row->jul_price, $row->aug_price, $row->sep_price, $row->oct_price, $row->nov_price, $row->dec_price);

			$data['ctg_pica_prev_year1_max'] = max($row->jan_price, $row->feb_price, $row->mar_price, $row->apr_price, $row->may_price, $row->jun_price, $row->jul_price, $row->aug_price, $row->sep_price, $row->oct_price, $row->nov_price, $row->dec_price);
		}

		/*1 PC BOSSING CUTS */
		
		$join_ctg_bossing = array(
			'sales_item_tbl b' => 'a.sales_id = b.sales_id AND a.sales_status = 1 AND b.sales_item_status = 1 AND a.sales_year = ' . $year,
			'sales_details_tbl c' => 'b.sales_item_id = c.sales_item_id AND c.sales_det_status = 1',
			'material_tbl d' => 'b.material_id = d.material_id AND d.material_code = 10400184',
			'material_group_tbl e' => 'd.material_group_id = e.material_group_id',
			'outlet_tbl f' => 'a.outlet_id = f.outlet_id AND f.bc_id = ' . $bc_id,
			'outlet_brand_tbl g' => 'f.outlet_id = g.outlet_id AND g.outlet_brand_status = 1',
			'brand_tbl h' => 'g.brand_id  = h.brand_id AND h.brand_name = \'CHOOKS-TO-GO\''
		);

		$get_ctg_bossing = $this->admin->get_join('sales_tbl a', $join_ctg_bossing, FALSE, FALSE, 'YEAR(c.sales_det_date), MONTH(c.sales_det_date)', 'AVG(c.sales_det_asp) as asp, MONTH(c.sales_det_date) asp_date');

		$data['ctg_bossing1'] = 0;
		$data['ctg_bossing2'] = 0;
		$data['ctg_bossing3'] = 0;
		$data['ctg_bossing4'] = 0;
		$data['ctg_bossing5'] = 0;
		$data['ctg_bossing6'] = 0;
		$data['ctg_bossing7'] = 0;
		$data['ctg_bossing8'] = 0;
		$data['ctg_bossing9'] = 0;
		$data['ctg_bossing10'] = 0;
		$data['ctg_bossing11'] = 0;
		$data['ctg_bossing12'] = 0;
		$data['ctg_bossing_total'] = 0;
		$data['ctg_bossing_count'] = 0;
		$data['ctg_bossing_avg'] = 0;
		$data['ctg_bossing_min'] = 0;
		$data['ctg_bossing_max'] = 0;

		foreach($get_ctg_bossing as $row){
			$asp = $row->asp;
			$month = $row->asp_date;
			$data['ctg_bossing' . $month] = $asp;
			$data['ctg_bossing_total'] += $asp;

			if($asp < $data['ctg_bossing_min'] || $data['ctg_bossing_count'] == 0){
				$data['ctg_bossing_min'] = $asp;
			}

			if($asp > $data['ctg_bossing_max'] || $data['ctg_bossing_count'] == 0){
				$data['ctg_bossing_max'] = $asp;
			}

			$data['ctg_bossing_count']++;
		}

		$data['ctg_bossing_avg'] = $data['ctg_bossing_total'] != 0 ? $data['ctg_bossing_total'] / $data['ctg_bossing_count'] : 0;


		$ctg_bossing_prev_year2 = $this->admin->get_data('comparative_price_tbl', array('comp_price_product' => '1 PC', 'comp_price_segment' => 'CTG', 'comp_price_year' => $year - 2,	'bc_id'	=>	$bc_id), 
			FALSE, 'AVG(comp_price_jan) as jan_price, AVG(comp_price_feb) as feb_price, AVG(comp_price_mar) as mar_price, AVG(comp_price_apr) as apr_price, AVG(comp_price_may) as may_price, AVG(comp_price_jun) as jun_price, AVG(comp_price_jul) as jul_price, AVG(comp_price_aug) as aug_price, AVG(comp_price_sep) as sep_price, AVG(comp_price_oct) as oct_price, AVG(comp_price_nov) as nov_price, AVG(comp_price_dec) as dec_price');
		
		$data['ctg_bossing_prev_year2_jan'] = 0;
		$data['ctg_bossing_prev_year2_feb'] = 0;
		$data['ctg_bossing_prev_year2_mar'] = 0;
		$data['ctg_bossing_prev_year2_apr'] = 0;
		$data['ctg_bossing_prev_year2_may'] = 0;
		$data['ctg_bossing_prev_year2_jun'] = 0;
		$data['ctg_bossing_prev_year2_jul'] = 0;
		$data['ctg_bossing_prev_year2_aug'] = 0;
		$data['ctg_bossing_prev_year2_sep'] = 0;
		$data['ctg_bossing_prev_year2_oct'] = 0;
		$data['ctg_bossing_prev_year2_nov'] = 0;
		$data['ctg_bossing_prev_year2_dec'] = 0;
		$data['ctg_bossing_prev_year2_avg'] = 0;
		$data['ctg_bossing_prev_year2_min'] = 0;
		$data['ctg_bossing_prev_year2_max'] = 0;

		foreach($ctg_bossing_prev_year2 as $row){
			$data['ctg_bossing_prev_year2_jan'] = $row->jan_price;
			$data['ctg_bossing_prev_year2_feb'] = $row->feb_price;
			$data['ctg_bossing_prev_year2_mar'] = $row->mar_price;
			$data['ctg_bossing_prev_year2_apr'] = $row->apr_price;
			$data['ctg_bossing_prev_year2_may'] = $row->may_price;
			$data['ctg_bossing_prev_year2_jun'] = $row->jun_price;
			$data['ctg_bossing_prev_year2_jul'] = $row->jul_price;
			$data['ctg_bossing_prev_year2_aug'] = $row->aug_price;
			$data['ctg_bossing_prev_year2_sep'] = $row->sep_price;
			$data['ctg_bossing_prev_year2_oct'] = $row->oct_price;
			$data['ctg_bossing_prev_year2_nov'] = $row->nov_price;
			$data['ctg_bossing_prev_year2_dec'] = $row->dec_price;
			$data['ctg_bossing_prev_year2_avg'] = ($row->jan_price + $row->feb_price + $row->mar_price + $row->apr_price + $row->may_price + $row->jun_price + $row->jul_price + $row->aug_price + $row->sep_price + $row->oct_price + $row->nov_price + $row->dec_price) / 12;

			$data['ctg_bossing_prev_year2_min'] = min($row->jan_price, $row->feb_price, $row->mar_price, $row->apr_price, $row->may_price, $row->jun_price, $row->jul_price, $row->aug_price, $row->sep_price, $row->oct_price, $row->nov_price, $row->dec_price);

			$data['ctg_bossing_prev_year2_max'] = max($row->jan_price, $row->feb_price, $row->mar_price, $row->apr_price, $row->may_price, $row->jun_price, $row->jul_price, $row->aug_price, $row->sep_price, $row->oct_price, $row->nov_price, $row->dec_price);
		}


		$ctg_bossing_prev_year1 = $this->admin->get_data('comparative_price_tbl', array('comp_price_product' => '11 PC', 'comp_price_segment' => 'CTG', 'comp_price_year' => $year - 1,	'bc_id'	=>	$bc_id), 
			FALSE, 'AVG(comp_price_jan) as jan_price, AVG(comp_price_feb) as feb_price, AVG(comp_price_mar) as mar_price, AVG(comp_price_apr) as apr_price, AVG(comp_price_may) as may_price, AVG(comp_price_jun) as jun_price, AVG(comp_price_jul) as jul_price, AVG(comp_price_aug) as aug_price, AVG(comp_price_sep) as sep_price, AVG(comp_price_oct) as oct_price, AVG(comp_price_nov) as nov_price, AVG(comp_price_dec) as dec_price');
		
		$data['ctg_bossing_prev_year1_jan'] = 0;
		$data['ctg_bossing_prev_year1_feb'] = 0;
		$data['ctg_bossing_prev_year1_mar'] = 0;
		$data['ctg_bossing_prev_year1_apr'] = 0;
		$data['ctg_bossing_prev_year1_may'] = 0;
		$data['ctg_bossing_prev_year1_jun'] = 0;
		$data['ctg_bossing_prev_year1_jul'] = 0;
		$data['ctg_bossing_prev_year1_aug'] = 0;
		$data['ctg_bossing_prev_year1_sep'] = 0;
		$data['ctg_bossing_prev_year1_oct'] = 0;
		$data['ctg_bossing_prev_year1_nov'] = 0;
		$data['ctg_bossing_prev_year1_dec'] = 0;
		$data['ctg_bossing_prev_year1_avg'] = 0;
		$data['ctg_bossing_prev_year1_min'] = 0;
		$data['ctg_bossing_prev_year1_max'] = 0;

		foreach($ctg_bossing_prev_year1 as $row){
			$data['ctg_bossing_prev_year1_jan'] = $row->jan_price;
			$data['ctg_bossing_prev_year1_feb'] = $row->feb_price;
			$data['ctg_bossing_prev_year1_mar'] = $row->mar_price;
			$data['ctg_bossing_prev_year1_apr'] = $row->apr_price;
			$data['ctg_bossing_prev_year1_may'] = $row->may_price;
			$data['ctg_bossing_prev_year1_jun'] = $row->jun_price;
			$data['ctg_bossing_prev_year1_jul'] = $row->jul_price;
			$data['ctg_bossing_prev_year1_aug'] = $row->aug_price;
			$data['ctg_bossing_prev_year1_sep'] = $row->sep_price;
			$data['ctg_bossing_prev_year1_oct'] = $row->oct_price;
			$data['ctg_bossing_prev_year1_nov'] = $row->nov_price;
			$data['ctg_bossing_prev_year1_dec'] = $row->dec_price;
			$data['ctg_bossing_prev_year1_avg'] = ($row->jan_price + $row->feb_price + $row->mar_price + $row->apr_price + $row->may_price + $row->jun_price + $row->jul_price + $row->aug_price + $row->sep_price + $row->oct_price + $row->nov_price + $row->dec_price) / 12;

			$data['ctg_bossing_prev_year1_min'] = min($row->jan_price, $row->feb_price, $row->mar_price, $row->apr_price, $row->may_price, $row->jun_price, $row->jul_price, $row->aug_price, $row->sep_price, $row->oct_price, $row->nov_price, $row->dec_price);

			$data['ctg_bossing_prev_year1_max'] = max($row->jan_price, $row->feb_price, $row->mar_price, $row->apr_price, $row->may_price, $row->jun_price, $row->jul_price, $row->aug_price, $row->sep_price, $row->oct_price, $row->nov_price, $row->dec_price);
		}

		/*Uling Roasters*/

		/*UR Chicken*/

		$get_ur_orc = $this->admin->get_query("SELECT p.size_name, AVG(asp_tbl.jan_asp) as orc_jan, AVG(asp_tbl.feb_asp) as orc_feb, AVG(asp_tbl.mar_asp) as orc_mar, AVG(asp_tbl.apr_asp) as orc_apr, AVG(asp_tbl.may_asp) as orc_may, AVG(asp_tbl.jun_asp) as orc_jun, AVG(asp_tbl.jul_asp) as orc_jul, AVG(asp_tbl.aug_asp) as orc_aug, AVG(asp_tbl.sep_asp) as orc_sep, AVG(asp_tbl.oct_asp) as orc_oct, AVG(asp_tbl.nov_asp) as orc_nov, AVG(asp_tbl.dec_asp) as orc_dec FROM material_tbl o, size_tbl p,

		(SELECT m.material_id, m.size_id,

		(SELECT AVG(c.sales_det_asp) FROM `sales_tbl` `a` JOIN `sales_item_tbl` `b` ON `a`.`sales_id` = `b`.`sales_id` AND `a`.`sales_status` = 1 AND `b`.`sales_item_status` = 1 AND `a`.`sales_year` = " . $year . " JOIN `sales_details_tbl` `c` ON `b`.`sales_item_id` = `c`.`sales_item_id` AND `c`.`sales_det_status` = 1 JOIN `material_tbl` `d` ON `b`.`material_id` = `d`.`material_id` JOIN `material_group_tbl` `e` ON `d`.`material_group_id` = `e`.`material_group_id` JOIN `outlet_tbl` `f` ON `a`.`outlet_id` = `f`.`outlet_id` JOIN `outlet_brand_tbl` `g` ON `f`.`outlet_id` = `g`.`outlet_id` AND `g`.`outlet_brand_status` = 1 JOIN `brand_tbl` `h` ON `g`.`brand_id` = `h`.`brand_id` AND `h`.`brand_name` = 'ULING ROASTER' AND c.sales_det_date = '" . $year . "-01-01' WHERE d.material_id = m.material_id AND f.bc_id = " . $bc_id . ") as jan_asp,
    
    	(SELECT AVG(c.sales_det_asp) FROM `sales_tbl` `a` JOIN `sales_item_tbl` `b` ON `a`.`sales_id` = `b`.`sales_id` AND `a`.`sales_status` = 1 AND `b`.`sales_item_status` = 1 AND `a`.`sales_year` = " . $year . " JOIN `sales_details_tbl` `c` ON `b`.`sales_item_id` = `c`.`sales_item_id` AND `c`.`sales_det_status` = 1 JOIN `material_tbl` `d` ON `b`.`material_id` = `d`.`material_id` JOIN `material_group_tbl` `e` ON `d`.`material_group_id` = `e`.`material_group_id` JOIN `outlet_tbl` `f` ON `a`.`outlet_id` = `f`.`outlet_id` JOIN `outlet_brand_tbl` `g` ON `f`.`outlet_id` = `g`.`outlet_id` AND `g`.`outlet_brand_status` = 1 JOIN `brand_tbl` `h` ON `g`.`brand_id` = `h`.`brand_id` AND `h`.`brand_name` = 'ULING ROASTER' AND c.sales_det_date = '" . $year . "-02-01' WHERE d.material_id = m.material_id AND f.bc_id = " . $bc_id . ") as feb_asp,
    
    	(SELECT AVG(c.sales_det_asp) FROM `sales_tbl` `a` JOIN `sales_item_tbl` `b` ON `a`.`sales_id` = `b`.`sales_id` AND `a`.`sales_status` = 1 AND `b`.`sales_item_status` = 1 AND `a`.`sales_year` = " . $year . " JOIN `sales_details_tbl` `c` ON `b`.`sales_item_id` = `c`.`sales_item_id` AND `c`.`sales_det_status` = 1 JOIN `material_tbl` `d` ON `b`.`material_id` = `d`.`material_id` JOIN `material_group_tbl` `e` ON `d`.`material_group_id` = `e`.`material_group_id` JOIN `outlet_tbl` `f` ON `a`.`outlet_id` = `f`.`outlet_id` JOIN `outlet_brand_tbl` `g` ON `f`.`outlet_id` = `g`.`outlet_id` AND `g`.`outlet_brand_status` = 1 JOIN `brand_tbl` `h` ON `g`.`brand_id` = `h`.`brand_id` AND `h`.`brand_name` = 'ULING ROASTER' AND c.sales_det_date = '" . $year . "-03-01' WHERE d.material_id = m.material_id AND f.bc_id = " . $bc_id . ") as mar_asp,
    
   		(SELECT AVG(c.sales_det_asp) FROM `sales_tbl` `a` JOIN `sales_item_tbl` `b` ON `a`.`sales_id` = `b`.`sales_id` AND `a`.`sales_status` = 1 AND `b`.`sales_item_status` = 1 AND `a`.`sales_year` = " . $year . " JOIN `sales_details_tbl` `c` ON `b`.`sales_item_id` = `c`.`sales_item_id` AND `c`.`sales_det_status` = 1 JOIN `material_tbl` `d` ON `b`.`material_id` = `d`.`material_id` JOIN `material_group_tbl` `e` ON `d`.`material_group_id` = `e`.`material_group_id` JOIN `outlet_tbl` `f` ON `a`.`outlet_id` = `f`.`outlet_id` JOIN `outlet_brand_tbl` `g` ON `f`.`outlet_id` = `g`.`outlet_id` AND `g`.`outlet_brand_status` = 1 JOIN `brand_tbl` `h` ON `g`.`brand_id` = `h`.`brand_id` AND `h`.`brand_name` = 'ULING ROASTER' AND c.sales_det_date = '" . $year . "-04-01' WHERE d.material_id = m.material_id AND f.bc_id = " . $bc_id . ") as apr_asp,
    
    	(SELECT AVG(c.sales_det_asp) FROM `sales_tbl` `a` JOIN `sales_item_tbl` `b` ON `a`.`sales_id` = `b`.`sales_id` AND `a`.`sales_status` = 1 AND `b`.`sales_item_status` = 1 AND `a`.`sales_year` = " . $year . " JOIN `sales_details_tbl` `c` ON `b`.`sales_item_id` = `c`.`sales_item_id` AND `c`.`sales_det_status` = 1 JOIN `material_tbl` `d` ON `b`.`material_id` = `d`.`material_id` JOIN `material_group_tbl` `e` ON `d`.`material_group_id` = `e`.`material_group_id` JOIN `outlet_tbl` `f` ON `a`.`outlet_id` = `f`.`outlet_id` JOIN `outlet_brand_tbl` `g` ON `f`.`outlet_id` = `g`.`outlet_id` AND `g`.`outlet_brand_status` = 1 JOIN `brand_tbl` `h` ON `g`.`brand_id` = `h`.`brand_id` AND `h`.`brand_name` = 'ULING ROASTER' AND c.sales_det_date = '" . $year . "-05-01' WHERE d.material_id = m.material_id AND f.bc_id = " . $bc_id . ") as may_asp,
    
    	(SELECT AVG(c.sales_det_asp) FROM `sales_tbl` `a` JOIN `sales_item_tbl` `b` ON `a`.`sales_id` = `b`.`sales_id` AND `a`.`sales_status` = 1 AND `b`.`sales_item_status` = 1 AND `a`.`sales_year` = " . $year . " JOIN `sales_details_tbl` `c` ON `b`.`sales_item_id` = `c`.`sales_item_id` AND `c`.`sales_det_status` = 1 JOIN `material_tbl` `d` ON `b`.`material_id` = `d`.`material_id` JOIN `material_group_tbl` `e` ON `d`.`material_group_id` = `e`.`material_group_id` JOIN `outlet_tbl` `f` ON `a`.`outlet_id` = `f`.`outlet_id` JOIN `outlet_brand_tbl` `g` ON `f`.`outlet_id` = `g`.`outlet_id` AND `g`.`outlet_brand_status` = 1 JOIN `brand_tbl` `h` ON `g`.`brand_id` = `h`.`brand_id` AND `h`.`brand_name` = 'ULING ROASTER' AND c.sales_det_date = '" . $year . "-06-01' WHERE d.material_id = m.material_id AND f.bc_id = " . $bc_id . ") as jun_asp,
    
    	(SELECT AVG(c.sales_det_asp) FROM `sales_tbl` `a` JOIN `sales_item_tbl` `b` ON `a`.`sales_id` = `b`.`sales_id` AND `a`.`sales_status` = 1 AND `b`.`sales_item_status` = 1 AND `a`.`sales_year` = " . $year . " JOIN `sales_details_tbl` `c` ON `b`.`sales_item_id` = `c`.`sales_item_id` AND `c`.`sales_det_status` = 1 JOIN `material_tbl` `d` ON `b`.`material_id` = `d`.`material_id` JOIN `material_group_tbl` `e` ON `d`.`material_group_id` = `e`.`material_group_id` JOIN `outlet_tbl` `f` ON `a`.`outlet_id` = `f`.`outlet_id` JOIN `outlet_brand_tbl` `g` ON `f`.`outlet_id` = `g`.`outlet_id` AND `g`.`outlet_brand_status` = 1 JOIN `brand_tbl` `h` ON `g`.`brand_id` = `h`.`brand_id` AND `h`.`brand_name` = 'ULING ROASTER' AND c.sales_det_date = '" . $year . "-07-01' WHERE d.material_id = m.material_id AND f.bc_id = " . $bc_id . ") as jul_asp,
    
    	(SELECT AVG(c.sales_det_asp) FROM `sales_tbl` `a` JOIN `sales_item_tbl` `b` ON `a`.`sales_id` = `b`.`sales_id` AND `a`.`sales_status` = 1 AND `b`.`sales_item_status` = 1 AND `a`.`sales_year` = " . $year . " JOIN `sales_details_tbl` `c` ON `b`.`sales_item_id` = `c`.`sales_item_id` AND `c`.`sales_det_status` = 1 JOIN `material_tbl` `d` ON `b`.`material_id` = `d`.`material_id` JOIN `material_group_tbl` `e` ON `d`.`material_group_id` = `e`.`material_group_id` JOIN `outlet_tbl` `f` ON `a`.`outlet_id` = `f`.`outlet_id` JOIN `outlet_brand_tbl` `g` ON `f`.`outlet_id` = `g`.`outlet_id` AND `g`.`outlet_brand_status` = 1 JOIN `brand_tbl` `h` ON `g`.`brand_id` = `h`.`brand_id` AND `h`.`brand_name` = 'ULING ROASTER' AND c.sales_det_date = '" . $year . "-08-01' WHERE d.material_id = m.material_id AND f.bc_id = " . $bc_id . ") as aug_asp,
    
    	(SELECT AVG(c.sales_det_asp) FROM `sales_tbl` `a` JOIN `sales_item_tbl` `b` ON `a`.`sales_id` = `b`.`sales_id` AND `a`.`sales_status` = 1 AND `b`.`sales_item_status` = 1 AND `a`.`sales_year` = " . $year . " JOIN `sales_details_tbl` `c` ON `b`.`sales_item_id` = `c`.`sales_item_id` AND `c`.`sales_det_status` = 1 JOIN `material_tbl` `d` ON `b`.`material_id` = `d`.`material_id` JOIN `material_group_tbl` `e` ON `d`.`material_group_id` = `e`.`material_group_id` JOIN `outlet_tbl` `f` ON `a`.`outlet_id` = `f`.`outlet_id` JOIN `outlet_brand_tbl` `g` ON `f`.`outlet_id` = `g`.`outlet_id` AND `g`.`outlet_brand_status` = 1 JOIN `brand_tbl` `h` ON `g`.`brand_id` = `h`.`brand_id` AND `h`.`brand_name` = 'ULING ROASTER' AND c.sales_det_date = '" . $year . "-09-01' WHERE d.material_id = m.material_id AND f.bc_id = " . $bc_id . ") as sep_asp,
    
    	(SELECT AVG(c.sales_det_asp) FROM `sales_tbl` `a` JOIN `sales_item_tbl` `b` ON `a`.`sales_id` = `b`.`sales_id` AND `a`.`sales_status` = 1 AND `b`.`sales_item_status` = 1 AND `a`.`sales_year` = " . $year . " JOIN `sales_details_tbl` `c` ON `b`.`sales_item_id` = `c`.`sales_item_id` AND `c`.`sales_det_status` = 1 JOIN `material_tbl` `d` ON `b`.`material_id` = `d`.`material_id` JOIN `material_group_tbl` `e` ON `d`.`material_group_id` = `e`.`material_group_id` JOIN `outlet_tbl` `f` ON `a`.`outlet_id` = `f`.`outlet_id` JOIN `outlet_brand_tbl` `g` ON `f`.`outlet_id` = `g`.`outlet_id` AND `g`.`outlet_brand_status` = 1 JOIN `brand_tbl` `h` ON `g`.`brand_id` = `h`.`brand_id` AND `h`.`brand_name` = 'ULING ROASTER' AND c.sales_det_date = '" . $year . "-10-01' WHERE d.material_id = m.material_id AND f.bc_id = " . $bc_id . ") as oct_asp,
    
    	(SELECT AVG(c.sales_det_asp) FROM `sales_tbl` `a` JOIN `sales_item_tbl` `b` ON `a`.`sales_id` = `b`.`sales_id` AND `a`.`sales_status` = 1 AND `b`.`sales_item_status` = 1 AND `a`.`sales_year` = " . $year . " JOIN `sales_details_tbl` `c` ON `b`.`sales_item_id` = `c`.`sales_item_id` AND `c`.`sales_det_status` = 1 JOIN `material_tbl` `d` ON `b`.`material_id` = `d`.`material_id` JOIN `material_group_tbl` `e` ON `d`.`material_group_id` = `e`.`material_group_id` JOIN `outlet_tbl` `f` ON `a`.`outlet_id` = `f`.`outlet_id` JOIN `outlet_brand_tbl` `g` ON `f`.`outlet_id` = `g`.`outlet_id` AND `g`.`outlet_brand_status` = 1 JOIN `brand_tbl` `h` ON `g`.`brand_id` = `h`.`brand_id` AND `h`.`brand_name` = 'ULING ROASTER' AND c.sales_det_date = '" . $year . "-11-01' WHERE d.material_id = m.material_id AND f.bc_id = " . $bc_id . ") as nov_asp,
    
    	(SELECT AVG(c.sales_det_asp) FROM `sales_tbl` `a` JOIN `sales_item_tbl` `b` ON `a`.`sales_id` = `b`.`sales_id` AND `a`.`sales_status` = 1 AND `b`.`sales_item_status` = 1 AND `a`.`sales_year` = " . $year . " JOIN `sales_details_tbl` `c` ON `b`.`sales_item_id` = `c`.`sales_item_id` AND `c`.`sales_det_status` = 1 JOIN `material_tbl` `d` ON `b`.`material_id` = `d`.`material_id` JOIN `material_group_tbl` `e` ON `d`.`material_group_id` = `e`.`material_group_id` JOIN `outlet_tbl` `f` ON `a`.`outlet_id` = `f`.`outlet_id` JOIN `outlet_brand_tbl` `g` ON `f`.`outlet_id` = `g`.`outlet_id` AND `g`.`outlet_brand_status` = 1 JOIN `brand_tbl` `h` ON `g`.`brand_id` = `h`.`brand_id` AND `h`.`brand_name` = 'ULING ROASTER' AND c.sales_det_date = '" . $year . "-01-01' WHERE d.material_id =  m.material_id AND f.bc_id = " . $bc_id . ") as dec_asp
    
		FROM material_size_tbl m, brand_tbl n WHERE m.brand_id = n.brand_id AND m.material_size_status = 1 AND n.brand_name = 'ULING ROASTER') as asp_tbl

		WHERE o.material_id = asp_tbl.material_id AND p.size_id = asp_tbl.size_id GROUP BY p.size_id");
		
		$data['ur_largo1'] = $data['ur_largo2'] = $data['ur_largo3'] = $data['ur_largo4'] = $data['ur_largo5'] = $data['ur_largo6'] = $data['ur_largo7'] = $data['ur_largo8'] = $data['ur_largo9'] = $data['ur_largo10'] = $data['ur_largo11'] = $data['ur_largo12'] = $data['ur_largo_total'] = $data['ur_largo_count'] = $data['ur_largo_avg'] = $data['ur_largo_min'] = $data['ur_largo_max'] = 0;

		$data['ur_plm1'] = $data['ur_plm2'] = $data['ur_plm3'] = $data['ur_plm4'] = $data['ur_plm5'] = $data['ur_plm6'] = $data['ur_plm7'] = $data['ur_plm8'] = $data['ur_plm9'] = $data['ur_plm10'] = $data['ur_plm11'] = $data['ur_plm12'] = $data['ur_plm_total'] = $data['ur_plm_count'] = $data['ur_plm_avg'] = $data['ur_plm_min'] = $data['ur_plm_max'] = 0;

		$data['ur_half1'] = $data['ur_half2'] = $data['ur_half3'] = $data['ur_half4'] = $data['ur_half5'] = $data['ur_half6'] = $data['ur_half7'] = $data['ur_half8'] = $data['ur_half9'] = $data['ur_half10'] = $data['ur_half11'] = $data['ur_half12'] = $data['ur_half_total'] = $data['ur_half_count'] = $data['ur_half_avg'] = $data['ur_half_min'] = $data['ur_half_max'] = 0;

		$data['ur_pequeno1'] = $data['ur_pequeno2'] = $data['ur_pequeno3'] = $data['ur_pequeno4'] = $data['ur_pequeno5'] = $data['ur_pequeno6'] = $data['ur_pequeno7'] = $data['ur_pequeno8'] = $data['ur_pequeno9'] = $data['ur_pequeno10'] = $data['ur_pequeno11'] = $data['ur_pequeno12'] = $data['ur_pequeno_total'] = $data['ur_pequeno_count'] = $data['ur_pequeno_avg'] = $data['ur_pequeno_min'] = $data['ur_pequeno_max'] = 0;

		foreach($get_ur_orc as $row){

			$orc_size = $row->size_name;
			$orc_jan = $row->orc_jan;
			$orc_feb = $row->orc_feb;
			$orc_mar = $row->orc_mar;
			$orc_apr = $row->orc_apr;
			$orc_may = $row->orc_may;
			$orc_jun = $row->orc_jun;
			$orc_jul = $row->orc_jul;
			$orc_aug = $row->orc_aug;
			$orc_sep = $row->orc_sep;
			$orc_oct = $row->orc_oct;
			$orc_nov = $row->orc_nov;
			$orc_dec = $row->orc_dec;
			$orc_min = min($orc_jan, $orc_feb, $orc_mar, $orc_apr, $orc_may, $orc_jun, $orc_jul, $orc_aug, $orc_sep, $orc_oct, $orc_nov, $orc_dec);
			$orc_max = max($orc_jan, $orc_feb, $orc_mar, $orc_apr, $orc_may, $orc_jun, $orc_jul, $orc_aug, $orc_sep, $orc_oct, $orc_nov, $orc_dec);
			$orc_avg = ($orc_jan + $orc_feb + $orc_mar + $orc_apr + $orc_may + $orc_jun + $orc_jul + $orc_aug + $orc_sep + $orc_oct + $orc_nov + $orc_dec) / 12;

			if($orc_size == "LARGO"){
				$data['ur_largo1'] = $orc_jan;
				$data['ur_largo2'] = $orc_feb;
				$data['ur_largo3'] = $orc_mar;
				$data['ur_largo4'] = $orc_apr;
				$data['ur_largo5'] = $orc_may;
				$data['ur_largo6'] = $orc_jun;
				$data['ur_largo7'] = $orc_jul;
				$data['ur_largo8'] = $orc_aug;
				$data['ur_largo9'] = $orc_sep;
				$data['ur_largo10'] = $orc_oct;
				$data['ur_largo11'] = $orc_nov;
				$data['ur_largo12'] = $orc_dec;
				$data['ur_largo_avg'] = $orc_avg;
				$data['ur_largo_min'] = $orc_min;
				$data['ur_largo_max'] = $orc_max;

				$ur_largo_prev_year2 = $this->admin->get_data('comparative_price_tbl', array('comp_price_product' => 'LARGO', 'comp_price_segment' => 'UR', 'comp_price_year' => $year - 2,	'bc_id'	=>	$bc_id), FALSE, 'AVG(comp_price_jan) as jan_price, AVG(comp_price_feb) as feb_price, AVG(comp_price_mar) as mar_price, AVG(comp_price_apr) as apr_price, AVG(comp_price_may) as may_price, AVG(comp_price_jun) as jun_price, AVG(comp_price_jul) as jul_price, AVG(comp_price_aug) as aug_price, AVG(comp_price_sep) as sep_price, AVG(comp_price_oct) as oct_price, AVG(comp_price_nov) as nov_price, AVG(comp_price_dec) as dec_price');
		
				$data['ur_largo_prev_year2_jan'] = 0;
				$data['ur_largo_prev_year2_feb'] = 0;
				$data['ur_largo_prev_year2_mar'] = 0;
				$data['ur_largo_prev_year2_apr'] = 0;
				$data['ur_largo_prev_year2_may'] = 0;
				$data['ur_largo_prev_year2_jun'] = 0;
				$data['ur_largo_prev_year2_jul'] = 0;
				$data['ur_largo_prev_year2_aug'] = 0;
				$data['ur_largo_prev_year2_sep'] = 0;
				$data['ur_largo_prev_year2_oct'] = 0;
				$data['ur_largo_prev_year2_nov'] = 0;
				$data['ur_largo_prev_year2_dec'] = 0;
				$data['ur_largo_prev_year2_avg'] = 0;
				$data['ur_largo_prev_year2_min'] = 0;
				$data['ur_largo_prev_year2_max'] = 0;

				foreach($ur_largo_prev_year2 as $row){
					$data['ur_largo_prev_year2_jan'] = $row->jan_price;
					$data['ur_largo_prev_year2_feb'] = $row->feb_price;
					$data['ur_largo_prev_year2_mar'] = $row->mar_price;
					$data['ur_largo_prev_year2_apr'] = $row->apr_price;
					$data['ur_largo_prev_year2_may'] = $row->may_price;
					$data['ur_largo_prev_year2_jun'] = $row->jun_price;
					$data['ur_largo_prev_year2_jul'] = $row->jul_price;
					$data['ur_largo_prev_year2_aug'] = $row->aug_price;
					$data['ur_largo_prev_year2_sep'] = $row->sep_price;
					$data['ur_largo_prev_year2_oct'] = $row->oct_price;
					$data['ur_largo_prev_year2_nov'] = $row->nov_price;
					$data['ur_largo_prev_year2_dec'] = $row->dec_price;
					$data['ur_largo_prev_year2_avg'] = ($row->jan_price + $row->feb_price + $row->mar_price + $row->apr_price + $row->may_price + $row->jun_price + $row->jul_price + $row->aug_price + $row->sep_price + $row->oct_price + $row->nov_price + $row->dec_price) / 12;

					$data['ur_largo_prev_year2_min'] = min($row->jan_price, $row->feb_price, $row->mar_price, $row->apr_price, $row->may_price, $row->jun_price, $row->jul_price, $row->aug_price, $row->sep_price, $row->oct_price, $row->nov_price, $row->dec_price);

					$data['ur_largo_prev_year2_max'] = max($row->jan_price, $row->feb_price, $row->mar_price, $row->apr_price, $row->may_price, $row->jun_price, $row->jul_price, $row->aug_price, $row->sep_price, $row->oct_price, $row->nov_price, $row->dec_price);
				}


				$ur_largo_prev_year1 = $this->admin->get_data('comparative_price_tbl', array('comp_price_product' => 'LARGO', 'comp_price_segment' => 'UR', 'comp_price_year' => $year - 1,	'bc_id'	=>	$bc_id), 
					FALSE, 'AVG(comp_price_jan) as jan_price, AVG(comp_price_feb) as feb_price, AVG(comp_price_mar) as mar_price, AVG(comp_price_apr) as apr_price, AVG(comp_price_may) as may_price, AVG(comp_price_jun) as jun_price, AVG(comp_price_jul) as jul_price, AVG(comp_price_aug) as aug_price, AVG(comp_price_sep) as sep_price, AVG(comp_price_oct) as oct_price, AVG(comp_price_nov) as nov_price, AVG(comp_price_dec) as dec_price');
				
				$data['ur_largo_prev_year1_jan'] = 0;
				$data['ur_largo_prev_year1_feb'] = 0;
				$data['ur_largo_prev_year1_mar'] = 0;
				$data['ur_largo_prev_year1_apr'] = 0;
				$data['ur_largo_prev_year1_may'] = 0;
				$data['ur_largo_prev_year1_jun'] = 0;
				$data['ur_largo_prev_year1_jul'] = 0;
				$data['ur_largo_prev_year1_aug'] = 0;
				$data['ur_largo_prev_year1_sep'] = 0;
				$data['ur_largo_prev_year1_oct'] = 0;
				$data['ur_largo_prev_year1_nov'] = 0;
				$data['ur_largo_prev_year1_dec'] = 0;
				$data['ur_largo_prev_year1_avg'] = 0;
				$data['ur_largo_prev_year1_min'] = 0;
				$data['ur_largo_prev_year1_max'] = 0;

				foreach($ur_largo_prev_year1 as $row){
					$data['ur_largo_prev_year1_jan'] = $row->jan_price;
					$data['ur_largo_prev_year1_feb'] = $row->feb_price;
					$data['ur_largo_prev_year1_mar'] = $row->mar_price;
					$data['ur_largo_prev_year1_apr'] = $row->apr_price;
					$data['ur_largo_prev_year1_may'] = $row->may_price;
					$data['ur_largo_prev_year1_jun'] = $row->jun_price;
					$data['ur_largo_prev_year1_jul'] = $row->jul_price;
					$data['ur_largo_prev_year1_aug'] = $row->aug_price;
					$data['ur_largo_prev_year1_sep'] = $row->sep_price;
					$data['ur_largo_prev_year1_oct'] = $row->oct_price;
					$data['ur_largo_prev_year1_nov'] = $row->nov_price;
					$data['ur_largo_prev_year1_dec'] = $row->dec_price;
					$data['ur_largo_prev_year1_avg'] = ($row->jan_price + $row->feb_price + $row->mar_price + $row->apr_price + $row->may_price + $row->jun_price + $row->jul_price + $row->aug_price + $row->sep_price + $row->oct_price + $row->nov_price + $row->dec_price) / 12;

					$data['ur_largo_prev_year1_min'] = min($row->jan_price, $row->feb_price, $row->mar_price, $row->apr_price, $row->may_price, $row->jun_price, $row->jul_price, $row->aug_price, $row->sep_price, $row->oct_price, $row->nov_price, $row->dec_price);

					$data['ur_largo_prev_year1_max'] = max($row->jan_price, $row->feb_price, $row->mar_price, $row->apr_price, $row->may_price, $row->jun_price, $row->jul_price, $row->aug_price, $row->sep_price, $row->oct_price, $row->nov_price, $row->dec_price);
				}

			}elseif($orc_size == "PLM"){
				$data['ur_plm1'] = $orc_jan;
				$data['ur_plm2'] = $orc_feb;
				$data['ur_plm3'] = $orc_mar;
				$data['ur_plm4'] = $orc_apr;
				$data['ur_plm5'] = $orc_may;
				$data['ur_plm6'] = $orc_jun;
				$data['ur_plm7'] = $orc_jul;
				$data['ur_plm8'] = $orc_aug;
				$data['ur_plm9'] = $orc_sep;
				$data['ur_plm10'] = $orc_oct;
				$data['ur_plm11'] = $orc_nov;
				$data['ur_plm12'] = $orc_dec;
				$data['ur_plm_avg'] = $orc_avg;
				$data['ur_plm_min'] = $orc_min;
				$data['ur_plm_max'] = $orc_max;

				$ur_plm_prev_year2 = $this->admin->get_data('comparative_price_tbl', array('comp_price_product' => 'PLM', 'comp_price_segment' => 'UR', 'comp_price_year' => $year - 2,	'bc_id'	=>	$bc_id), FALSE, 'AVG(comp_price_jan) as jan_price, AVG(comp_price_feb) as feb_price, AVG(comp_price_mar) as mar_price, AVG(comp_price_apr) as apr_price, AVG(comp_price_may) as may_price, AVG(comp_price_jun) as jun_price, AVG(comp_price_jul) as jul_price, AVG(comp_price_aug) as aug_price, AVG(comp_price_sep) as sep_price, AVG(comp_price_oct) as oct_price, AVG(comp_price_nov) as nov_price, AVG(comp_price_dec) as dec_price');
		
				$data['ur_plm_prev_year2_jan'] = 0;
				$data['ur_plm_prev_year2_feb'] = 0;
				$data['ur_plm_prev_year2_mar'] = 0;
				$data['ur_plm_prev_year2_apr'] = 0;
				$data['ur_plm_prev_year2_may'] = 0;
				$data['ur_plm_prev_year2_jun'] = 0;
				$data['ur_plm_prev_year2_jul'] = 0;
				$data['ur_plm_prev_year2_aug'] = 0;
				$data['ur_plm_prev_year2_sep'] = 0;
				$data['ur_plm_prev_year2_oct'] = 0;
				$data['ur_plm_prev_year2_nov'] = 0;
				$data['ur_plm_prev_year2_dec'] = 0;
				$data['ur_plm_prev_year2_avg'] = 0;
				$data['ur_plm_prev_year2_min'] = 0;
				$data['ur_plm_prev_year2_max'] = 0;

				foreach($ur_plm_prev_year2 as $row){
					$data['ur_plm_prev_year2_jan'] = $row->jan_price;
					$data['ur_plm_prev_year2_feb'] = $row->feb_price;
					$data['ur_plm_prev_year2_mar'] = $row->mar_price;
					$data['ur_plm_prev_year2_apr'] = $row->apr_price;
					$data['ur_plm_prev_year2_may'] = $row->may_price;
					$data['ur_plm_prev_year2_jun'] = $row->jun_price;
					$data['ur_plm_prev_year2_jul'] = $row->jul_price;
					$data['ur_plm_prev_year2_aug'] = $row->aug_price;
					$data['ur_plm_prev_year2_sep'] = $row->sep_price;
					$data['ur_plm_prev_year2_oct'] = $row->oct_price;
					$data['ur_plm_prev_year2_nov'] = $row->nov_price;
					$data['ur_plm_prev_year2_dec'] = $row->dec_price;
					$data['ur_plm_prev_year2_avg'] = ($row->jan_price + $row->feb_price + $row->mar_price + $row->apr_price + $row->may_price + $row->jun_price + $row->jul_price + $row->aug_price + $row->sep_price + $row->oct_price + $row->nov_price + $row->dec_price) / 12;

					$data['ur_plm_prev_year2_min'] = min($row->jan_price, $row->feb_price, $row->mar_price, $row->apr_price, $row->may_price, $row->jun_price, $row->jul_price, $row->aug_price, $row->sep_price, $row->oct_price, $row->nov_price, $row->dec_price);

					$data['ur_plm_prev_year2_max'] = max($row->jan_price, $row->feb_price, $row->mar_price, $row->apr_price, $row->may_price, $row->jun_price, $row->jul_price, $row->aug_price, $row->sep_price, $row->oct_price, $row->nov_price, $row->dec_price);
				}


				$ur_plm_prev_year1 = $this->admin->get_data('comparative_price_tbl', array('comp_price_product' => 'PLM', 'comp_price_segment' => 'UR', 'comp_price_year' => $year - 1,	'bc_id'	=>	$bc_id), 
					FALSE, 'AVG(comp_price_jan) as jan_price, AVG(comp_price_feb) as feb_price, AVG(comp_price_mar) as mar_price, AVG(comp_price_apr) as apr_price, AVG(comp_price_may) as may_price, AVG(comp_price_jun) as jun_price, AVG(comp_price_jul) as jul_price, AVG(comp_price_aug) as aug_price, AVG(comp_price_sep) as sep_price, AVG(comp_price_oct) as oct_price, AVG(comp_price_nov) as nov_price, AVG(comp_price_dec) as dec_price');
				
				$data['ur_plm_prev_year1_jan'] = 0;
				$data['ur_plm_prev_year1_feb'] = 0;
				$data['ur_plm_prev_year1_mar'] = 0;
				$data['ur_plm_prev_year1_apr'] = 0;
				$data['ur_plm_prev_year1_may'] = 0;
				$data['ur_plm_prev_year1_jun'] = 0;
				$data['ur_plm_prev_year1_jul'] = 0;
				$data['ur_plm_prev_year1_aug'] = 0;
				$data['ur_plm_prev_year1_sep'] = 0;
				$data['ur_plm_prev_year1_oct'] = 0;
				$data['ur_plm_prev_year1_nov'] = 0;
				$data['ur_plm_prev_year1_dec'] = 0;
				$data['ur_plm_prev_year1_avg'] = 0;
				$data['ur_plm_prev_year1_min'] = 0;
				$data['ur_plm_prev_year1_max'] = 0;

				foreach($ur_plm_prev_year1 as $row){
					$data['ur_plm_prev_year1_jan'] = $row->jan_price;
					$data['ur_plm_prev_year1_feb'] = $row->feb_price;
					$data['ur_plm_prev_year1_mar'] = $row->mar_price;
					$data['ur_plm_prev_year1_apr'] = $row->apr_price;
					$data['ur_plm_prev_year1_may'] = $row->may_price;
					$data['ur_plm_prev_year1_jun'] = $row->jun_price;
					$data['ur_plm_prev_year1_jul'] = $row->jul_price;
					$data['ur_plm_prev_year1_aug'] = $row->aug_price;
					$data['ur_plm_prev_year1_sep'] = $row->sep_price;
					$data['ur_plm_prev_year1_oct'] = $row->oct_price;
					$data['ur_plm_prev_year1_nov'] = $row->nov_price;
					$data['ur_plm_prev_year1_dec'] = $row->dec_price;
					$data['ur_plm_prev_year1_avg'] = ($row->jan_price + $row->feb_price + $row->mar_price + $row->apr_price + $row->may_price + $row->jun_price + $row->jul_price + $row->aug_price + $row->sep_price + $row->oct_price + $row->nov_price + $row->dec_price) / 12;

					$data['ur_plm_prev_year1_min'] = min($row->jan_price, $row->feb_price, $row->mar_price, $row->apr_price, $row->may_price, $row->jun_price, $row->jul_price, $row->aug_price, $row->sep_price, $row->oct_price, $row->nov_price, $row->dec_price);

					$data['ur_plm_prev_year1_max'] = max($row->jan_price, $row->feb_price, $row->mar_price, $row->apr_price, $row->may_price, $row->jun_price, $row->jul_price, $row->aug_price, $row->sep_price, $row->oct_price, $row->nov_price, $row->dec_price);
				}

			}elseif($orc_size == "PEQUENO"){
				$data['ur_pequeno1'] = $orc_jan;
				$data['ur_pequeno2'] = $orc_feb;
				$data['ur_pequeno3'] = $orc_mar;
				$data['ur_pequeno4'] = $orc_apr;
				$data['ur_pequeno5'] = $orc_may;
				$data['ur_pequeno6'] = $orc_jun;
				$data['ur_pequeno7'] = $orc_jul;
				$data['ur_pequeno8'] = $orc_aug;
				$data['ur_pequeno9'] = $orc_sep;
				$data['ur_pequeno10'] = $orc_oct;
				$data['ur_pequeno11'] = $orc_nov;
				$data['ur_pequeno12'] = $orc_dec;
				$data['ur_pequeno_avg'] = $orc_avg;
				$data['ur_pequeno_min'] = $orc_min;
				$data['ur_pequeno_max'] = $orc_max;

				$ur_pequeno_prev_year2 = $this->admin->get_data('comparative_price_tbl', array('comp_price_product' => 'PEQUENO', 'comp_price_segment' => 'UR', 'comp_price_year' => $year - 2,	'bc_id'	=>	$bc_id), FALSE, 'AVG(comp_price_jan) as jan_price, AVG(comp_price_feb) as feb_price, AVG(comp_price_mar) as mar_price, AVG(comp_price_apr) as apr_price, AVG(comp_price_may) as may_price, AVG(comp_price_jun) as jun_price, AVG(comp_price_jul) as jul_price, AVG(comp_price_aug) as aug_price, AVG(comp_price_sep) as sep_price, AVG(comp_price_oct) as oct_price, AVG(comp_price_nov) as nov_price, AVG(comp_price_dec) as dec_price');
		
				$data['ur_pequeno_prev_year2_jan'] = 0;
				$data['ur_pequeno_prev_year2_feb'] = 0;
				$data['ur_pequeno_prev_year2_mar'] = 0;
				$data['ur_pequeno_prev_year2_apr'] = 0;
				$data['ur_pequeno_prev_year2_may'] = 0;
				$data['ur_pequeno_prev_year2_jun'] = 0;
				$data['ur_pequeno_prev_year2_jul'] = 0;
				$data['ur_pequeno_prev_year2_aug'] = 0;
				$data['ur_pequeno_prev_year2_sep'] = 0;
				$data['ur_pequeno_prev_year2_oct'] = 0;
				$data['ur_pequeno_prev_year2_nov'] = 0;
				$data['ur_pequeno_prev_year2_dec'] = 0;
				$data['ur_pequeno_prev_year2_avg'] = 0;
				$data['ur_pequeno_prev_year2_min'] = 0;
				$data['ur_pequeno_prev_year2_max'] = 0;

				foreach($ur_pequeno_prev_year2 as $row){
					$data['ur_pequeno_prev_year2_jan'] = $row->jan_price;
					$data['ur_pequeno_prev_year2_feb'] = $row->feb_price;
					$data['ur_pequeno_prev_year2_mar'] = $row->mar_price;
					$data['ur_pequeno_prev_year2_apr'] = $row->apr_price;
					$data['ur_pequeno_prev_year2_may'] = $row->may_price;
					$data['ur_pequeno_prev_year2_jun'] = $row->jun_price;
					$data['ur_pequeno_prev_year2_jul'] = $row->jul_price;
					$data['ur_pequeno_prev_year2_aug'] = $row->aug_price;
					$data['ur_pequeno_prev_year2_sep'] = $row->sep_price;
					$data['ur_pequeno_prev_year2_oct'] = $row->oct_price;
					$data['ur_pequeno_prev_year2_nov'] = $row->nov_price;
					$data['ur_pequeno_prev_year2_dec'] = $row->dec_price;
					$data['ur_pequeno_prev_year2_avg'] = ($row->jan_price + $row->feb_price + $row->mar_price + $row->apr_price + $row->may_price + $row->jun_price + $row->jul_price + $row->aug_price + $row->sep_price + $row->oct_price + $row->nov_price + $row->dec_price) / 12;

					$data['ur_pequeno_prev_year2_min'] = min($row->jan_price, $row->feb_price, $row->mar_price, $row->apr_price, $row->may_price, $row->jun_price, $row->jul_price, $row->aug_price, $row->sep_price, $row->oct_price, $row->nov_price, $row->dec_price);

					$data['ur_pequeno_prev_year2_max'] = max($row->jan_price, $row->feb_price, $row->mar_price, $row->apr_price, $row->may_price, $row->jun_price, $row->jul_price, $row->aug_price, $row->sep_price, $row->oct_price, $row->nov_price, $row->dec_price);
				}


				$ur_pequeno_prev_year1 = $this->admin->get_data('comparative_price_tbl', array('comp_price_product' => 'PEQUENO', 'comp_price_segment' => 'UR', 'comp_price_year' => $year - 1,	'bc_id'	=>	$bc_id), 
					FALSE, 'AVG(comp_price_jan) as jan_price, AVG(comp_price_feb) as feb_price, AVG(comp_price_mar) as mar_price, AVG(comp_price_apr) as apr_price, AVG(comp_price_may) as may_price, AVG(comp_price_jun) as jun_price, AVG(comp_price_jul) as jul_price, AVG(comp_price_aug) as aug_price, AVG(comp_price_sep) as sep_price, AVG(comp_price_oct) as oct_price, AVG(comp_price_nov) as nov_price, AVG(comp_price_dec) as dec_price');
				
				$data['ur_pequeno_prev_year1_jan'] = 0;
				$data['ur_pequeno_prev_year1_feb'] = 0;
				$data['ur_pequeno_prev_year1_mar'] = 0;
				$data['ur_pequeno_prev_year1_apr'] = 0;
				$data['ur_pequeno_prev_year1_may'] = 0;
				$data['ur_pequeno_prev_year1_jun'] = 0;
				$data['ur_pequeno_prev_year1_jul'] = 0;
				$data['ur_pequeno_prev_year1_aug'] = 0;
				$data['ur_pequeno_prev_year1_sep'] = 0;
				$data['ur_pequeno_prev_year1_oct'] = 0;
				$data['ur_pequeno_prev_year1_nov'] = 0;
				$data['ur_pequeno_prev_year1_dec'] = 0;
				$data['ur_pequeno_prev_year1_avg'] = 0;
				$data['ur_pequeno_prev_year1_min'] = 0;
				$data['ur_pequeno_prev_year1_max'] = 0;

				foreach($ur_pequeno_prev_year1 as $row){
					$data['ur_pequeno_prev_year1_jan'] = $row->jan_price;
					$data['ur_pequeno_prev_year1_feb'] = $row->feb_price;
					$data['ur_pequeno_prev_year1_mar'] = $row->mar_price;
					$data['ur_pequeno_prev_year1_apr'] = $row->apr_price;
					$data['ur_pequeno_prev_year1_may'] = $row->may_price;
					$data['ur_pequeno_prev_year1_jun'] = $row->jun_price;
					$data['ur_pequeno_prev_year1_jul'] = $row->jul_price;
					$data['ur_pequeno_prev_year1_aug'] = $row->aug_price;
					$data['ur_pequeno_prev_year1_sep'] = $row->sep_price;
					$data['ur_pequeno_prev_year1_oct'] = $row->oct_price;
					$data['ur_pequeno_prev_year1_nov'] = $row->nov_price;
					$data['ur_pequeno_prev_year1_dec'] = $row->dec_price;
					$data['ur_pequeno_prev_year1_avg'] = ($row->jan_price + $row->feb_price + $row->mar_price + $row->apr_price + $row->may_price + $row->jun_price + $row->jul_price + $row->aug_price + $row->sep_price + $row->oct_price + $row->nov_price + $row->dec_price) / 12;

					$data['ur_pequeno_prev_year1_min'] = min($row->jan_price, $row->feb_price, $row->mar_price, $row->apr_price, $row->may_price, $row->jun_price, $row->jul_price, $row->aug_price, $row->sep_price, $row->oct_price, $row->nov_price, $row->dec_price);

					$data['ur_pequeno_prev_year1_max'] = max($row->jan_price, $row->feb_price, $row->mar_price, $row->apr_price, $row->may_price, $row->jun_price, $row->jul_price, $row->aug_price, $row->sep_price, $row->oct_price, $row->nov_price, $row->dec_price);
				}

			}elseif($orc_size == "HALF"){
				$data['ur_half1'] = $orc_jan;
				$data['ur_half2'] = $orc_feb;
				$data['ur_half3'] = $orc_mar;
				$data['ur_half4'] = $orc_apr;
				$data['ur_half5'] = $orc_may;
				$data['ur_half6'] = $orc_jun;
				$data['ur_half7'] = $orc_jul;
				$data['ur_half8'] = $orc_aug;
				$data['ur_half9'] = $orc_sep;
				$data['ur_half10'] = $orc_oct;
				$data['ur_half11'] = $orc_nov;
				$data['ur_half12'] = $orc_dec;
				$data['ur_half_avg'] = $orc_avg;
				$data['ur_half_min'] = $orc_min;
				$data['ur_half_max'] = $orc_max;

				$ur_half_prev_year2 = $this->admin->get_data('comparative_price_tbl', array('comp_price_product' => 'HALF', 'comp_price_segment' => 'UR', 'comp_price_year' => $year - 2,	'bc_id'	=>	$bc_id), FALSE, 'AVG(comp_price_jan) as jan_price, AVG(comp_price_feb) as feb_price, AVG(comp_price_mar) as mar_price, AVG(comp_price_apr) as apr_price, AVG(comp_price_may) as may_price, AVG(comp_price_jun) as jun_price, AVG(comp_price_jul) as jul_price, AVG(comp_price_aug) as aug_price, AVG(comp_price_sep) as sep_price, AVG(comp_price_oct) as oct_price, AVG(comp_price_nov) as nov_price, AVG(comp_price_dec) as dec_price');
		
				$data['ur_half_prev_year2_jan'] = 0;
				$data['ur_half_prev_year2_feb'] = 0;
				$data['ur_half_prev_year2_mar'] = 0;
				$data['ur_half_prev_year2_apr'] = 0;
				$data['ur_half_prev_year2_may'] = 0;
				$data['ur_half_prev_year2_jun'] = 0;
				$data['ur_half_prev_year2_jul'] = 0;
				$data['ur_half_prev_year2_aug'] = 0;
				$data['ur_half_prev_year2_sep'] = 0;
				$data['ur_half_prev_year2_oct'] = 0;
				$data['ur_half_prev_year2_nov'] = 0;
				$data['ur_half_prev_year2_dec'] = 0;
				$data['ur_half_prev_year2_avg'] = 0;
				$data['ur_half_prev_year2_min'] = 0;
				$data['ur_half_prev_year2_max'] = 0;

				foreach($ur_half_prev_year2 as $row){
					$data['ur_half_prev_year2_jan'] = $row->jan_price;
					$data['ur_half_prev_year2_feb'] = $row->feb_price;
					$data['ur_half_prev_year2_mar'] = $row->mar_price;
					$data['ur_half_prev_year2_apr'] = $row->apr_price;
					$data['ur_half_prev_year2_may'] = $row->may_price;
					$data['ur_half_prev_year2_jun'] = $row->jun_price;
					$data['ur_half_prev_year2_jul'] = $row->jul_price;
					$data['ur_half_prev_year2_aug'] = $row->aug_price;
					$data['ur_half_prev_year2_sep'] = $row->sep_price;
					$data['ur_half_prev_year2_oct'] = $row->oct_price;
					$data['ur_half_prev_year2_nov'] = $row->nov_price;
					$data['ur_half_prev_year2_dec'] = $row->dec_price;
					$data['ur_half_prev_year2_avg'] = ($row->jan_price + $row->feb_price + $row->mar_price + $row->apr_price + $row->may_price + $row->jun_price + $row->jul_price + $row->aug_price + $row->sep_price + $row->oct_price + $row->nov_price + $row->dec_price) / 12;

					$data['ur_half_prev_year2_min'] = min($row->jan_price, $row->feb_price, $row->mar_price, $row->apr_price, $row->may_price, $row->jun_price, $row->jul_price, $row->aug_price, $row->sep_price, $row->oct_price, $row->nov_price, $row->dec_price);

					$data['ur_half_prev_year2_max'] = max($row->jan_price, $row->feb_price, $row->mar_price, $row->apr_price, $row->may_price, $row->jun_price, $row->jul_price, $row->aug_price, $row->sep_price, $row->oct_price, $row->nov_price, $row->dec_price);
				}


				$ur_half_prev_year1 = $this->admin->get_data('comparative_price_tbl', array('comp_price_product' => 'HALF', 'comp_price_segment' => 'UR', 'comp_price_year' => $year - 1,	'bc_id'	=>	$bc_id), 
					FALSE, 'AVG(comp_price_jan) as jan_price, AVG(comp_price_feb) as feb_price, AVG(comp_price_mar) as mar_price, AVG(comp_price_apr) as apr_price, AVG(comp_price_may) as may_price, AVG(comp_price_jun) as jun_price, AVG(comp_price_jul) as jul_price, AVG(comp_price_aug) as aug_price, AVG(comp_price_sep) as sep_price, AVG(comp_price_oct) as oct_price, AVG(comp_price_nov) as nov_price, AVG(comp_price_dec) as dec_price');
				
				$data['ur_half_prev_year1_jan'] = 0;
				$data['ur_half_prev_year1_feb'] = 0;
				$data['ur_half_prev_year1_mar'] = 0;
				$data['ur_half_prev_year1_apr'] = 0;
				$data['ur_half_prev_year1_may'] = 0;
				$data['ur_half_prev_year1_jun'] = 0;
				$data['ur_half_prev_year1_jul'] = 0;
				$data['ur_half_prev_year1_aug'] = 0;
				$data['ur_half_prev_year1_sep'] = 0;
				$data['ur_half_prev_year1_oct'] = 0;
				$data['ur_half_prev_year1_nov'] = 0;
				$data['ur_half_prev_year1_dec'] = 0;
				$data['ur_half_prev_year1_avg'] = 0;
				$data['ur_half_prev_year1_min'] = 0;
				$data['ur_half_prev_year1_max'] = 0;

				foreach($ur_half_prev_year1 as $row){
					$data['ur_half_prev_year1_jan'] = $row->jan_price;
					$data['ur_half_prev_year1_feb'] = $row->feb_price;
					$data['ur_half_prev_year1_mar'] = $row->mar_price;
					$data['ur_half_prev_year1_apr'] = $row->apr_price;
					$data['ur_half_prev_year1_may'] = $row->may_price;
					$data['ur_half_prev_year1_jun'] = $row->jun_price;
					$data['ur_half_prev_year1_jul'] = $row->jul_price;
					$data['ur_half_prev_year1_aug'] = $row->aug_price;
					$data['ur_half_prev_year1_sep'] = $row->sep_price;
					$data['ur_half_prev_year1_oct'] = $row->oct_price;
					$data['ur_half_prev_year1_nov'] = $row->nov_price;
					$data['ur_half_prev_year1_dec'] = $row->dec_price;
					$data['ur_half_prev_year1_avg'] = ($row->jan_price + $row->feb_price + $row->mar_price + $row->apr_price + $row->may_price + $row->jun_price + $row->jul_price + $row->aug_price + $row->sep_price + $row->oct_price + $row->nov_price + $row->dec_price) / 12;

					$data['ur_half_prev_year1_min'] = min($row->jan_price, $row->feb_price, $row->mar_price, $row->apr_price, $row->may_price, $row->jun_price, $row->jul_price, $row->aug_price, $row->sep_price, $row->oct_price, $row->nov_price, $row->dec_price);

					$data['ur_half_prev_year1_max'] = max($row->jan_price, $row->feb_price, $row->mar_price, $row->apr_price, $row->may_price, $row->jun_price, $row->jul_price, $row->aug_price, $row->sep_price, $row->oct_price, $row->nov_price, $row->dec_price);
				}
			}
		}

		/*IMPROVED INASAL CLASSIC*/
		$join_ur_improved_inasal_classic = array(
			'sales_item_tbl b' => 'a.sales_id = b.sales_id AND a.sales_status = 1 AND b.sales_item_status = 1 AND a.sales_year = ' . $year,
			'sales_details_tbl c' => 'b.sales_item_id = c.sales_item_id AND c.sales_det_status = 1',
			'material_tbl d' => 'b.material_id = d.material_id AND d.material_id IN ("1000741", "1000742")',
			'material_group_tbl e' => 'd.material_group_id = e.material_group_id',
			'outlet_tbl f' => 'a.outlet_id = f.outlet_id AND f.bc_id = ' . $bc_id,
			'outlet_brand_tbl g' => 'f.outlet_id = g.outlet_id AND g.outlet_brand_status = 1',
			'brand_tbl h' => 'g.brand_id  = h.brand_id AND h.brand_name = \'ULING ROASTER\''
		);

		$get_ur_improved_inasal_classic = $this->admin->get_join('sales_tbl a', $join_ur_improved_inasal_classic, FALSE, FALSE, 'YEAR(c.sales_det_date), MONTH(c.sales_det_date)', 'AVG(c.sales_det_asp) as asp, MONTH(c.sales_det_date) asp_date');

		$data['ur_improved_inasal_classic1'] = 0;
		$data['ur_improved_inasal_classic2'] = 0;
		$data['ur_improved_inasal_classic3'] = 0;
		$data['ur_improved_inasal_classic4'] = 0;
		$data['ur_improved_inasal_classic5'] = 0;
		$data['ur_improved_inasal_classic6'] = 0;
		$data['ur_improved_inasal_classic7'] = 0;
		$data['ur_improved_inasal_classic8'] = 0;
		$data['ur_improved_inasal_classic9'] = 0;
		$data['ur_improved_inasal_classic10'] = 0;
		$data['ur_improved_inasal_classic11'] = 0;
		$data['ur_improved_inasal_classic12'] = 0;
		$data['ur_improved_inasal_classic_total'] = 0;
		$data['ur_improved_inasal_classic_count'] = 0;
		$data['ur_improved_inasal_classic_avg'] = 0;
		$data['ur_improved_inasal_classic_min'] = 0;
		$data['ur_improved_inasal_classic_max'] = 0;

		foreach($get_ur_improved_inasal_classic as $row){
			$asp = $row->asp;
			$month = $row->asp_date;
			$data['ur_improved_inasal_classic' . $month] = $asp;
			$data['ur_improved_inasal_classic_total'] += $asp;

			if($asp < $data['ur_improved_inasal_classic_min'] || $data['ur_improved_inasal_classic_count'] == 0){
				$data['ur_improved_inasal_classic_min'] = $asp;
			}

			if($asp > $data['ur_improved_inasal_classic_max'] || $data['ur_improved_inasal_classic_count'] == 0){
				$data['ur_improved_inasal_classic_max'] = $asp;
			}

			$data['ur_improved_inasal_classic_count']++;
		}

		$data['ur_improved_inasal_classic_avg'] = $data['ur_improved_inasal_classic_total'] != 0 ? $data['ur_improved_inasal_classic_total'] / $data['ur_improved_inasal_classic_count'] : 0;

		$ur_improved_inasal_classic_prev_year2 = $this->admin->get_data('comparative_price_tbl', array('comp_price_product' => 'IMPROVED INASAL CLASSIC', 'comp_price_segment' => 'UR', 'comp_price_year' => $year - 2,	'bc_id'	=>	$bc_id), 
			FALSE, 'AVG(comp_price_jan) as jan_price, AVG(comp_price_feb) as feb_price, AVG(comp_price_mar) as mar_price, AVG(comp_price_apr) as apr_price, AVG(comp_price_may) as may_price, AVG(comp_price_jun) as jun_price, AVG(comp_price_jul) as jul_price, AVG(comp_price_aug) as aug_price, AVG(comp_price_sep) as sep_price, AVG(comp_price_oct) as oct_price, AVG(comp_price_nov) as nov_price, AVG(comp_price_dec) as dec_price');
		
		$data['ur_improved_inasal_classic_prev_year2_jan'] = 0;
		$data['ur_improved_inasal_classic_prev_year2_feb'] = 0;
		$data['ur_improved_inasal_classic_prev_year2_mar'] = 0;
		$data['ur_improved_inasal_classic_prev_year2_apr'] = 0;
		$data['ur_improved_inasal_classic_prev_year2_may'] = 0;
		$data['ur_improved_inasal_classic_prev_year2_jun'] = 0;
		$data['ur_improved_inasal_classic_prev_year2_jul'] = 0;
		$data['ur_improved_inasal_classic_prev_year2_aug'] = 0;
		$data['ur_improved_inasal_classic_prev_year2_sep'] = 0;
		$data['ur_improved_inasal_classic_prev_year2_oct'] = 0;
		$data['ur_improved_inasal_classic_prev_year2_nov'] = 0;
		$data['ur_improved_inasal_classic_prev_year2_dec'] = 0;
		$data['ur_improved_inasal_classic_prev_year2_avg'] = 0;
		$data['ur_improved_inasal_classic_prev_year2_min'] = 0;
		$data['ur_improved_inasal_classic_prev_year2_max'] = 0;

		foreach($ur_improved_inasal_classic_prev_year2 as $row){
			$data['ur_improved_inasal_classic_prev_year2_jan'] = $row->jan_price;
			$data['ur_improved_inasal_classic_prev_year2_feb'] = $row->feb_price;
			$data['ur_improved_inasal_classic_prev_year2_mar'] = $row->mar_price;
			$data['ur_improved_inasal_classic_prev_year2_apr'] = $row->apr_price;
			$data['ur_improved_inasal_classic_prev_year2_may'] = $row->may_price;
			$data['ur_improved_inasal_classic_prev_year2_jun'] = $row->jun_price;
			$data['ur_improved_inasal_classic_prev_year2_jul'] = $row->jul_price;
			$data['ur_improved_inasal_classic_prev_year2_aug'] = $row->aug_price;
			$data['ur_improved_inasal_classic_prev_year2_sep'] = $row->sep_price;
			$data['ur_improved_inasal_classic_prev_year2_oct'] = $row->oct_price;
			$data['ur_improved_inasal_classic_prev_year2_nov'] = $row->nov_price;
			$data['ur_improved_inasal_classic_prev_year2_dec'] = $row->dec_price;
			$data['ur_improved_inasal_classic_prev_year2_avg'] = ($row->jan_price + $row->feb_price + $row->mar_price + $row->apr_price + $row->may_price + $row->jun_price + $row->jul_price + $row->aug_price + $row->sep_price + $row->oct_price + $row->nov_price + $row->dec_price) / 12;

			$data['ur_improved_inasal_classic_prev_year2_min'] = min($row->jan_price, $row->feb_price, $row->mar_price, $row->apr_price, $row->may_price, $row->jun_price, $row->jul_price, $row->aug_price, $row->sep_price, $row->oct_price, $row->nov_price, $row->dec_price);

			$data['ur_improved_inasal_classic_prev_year2_max'] = max($row->jan_price, $row->feb_price, $row->mar_price, $row->apr_price, $row->may_price, $row->jun_price, $row->jul_price, $row->aug_price, $row->sep_price, $row->oct_price, $row->nov_price, $row->dec_price);
		}


		$ur_improved_inasal_classic_prev_year1 = $this->admin->get_data('comparative_price_tbl', array('comp_price_product' => 'IMPROVED INASAL CLASSIC', 'comp_price_segment' => 'UR', 'comp_price_year' => $year - 1,	'bc_id'	=>	$bc_id), 
			FALSE, 'AVG(comp_price_jan) as jan_price, AVG(comp_price_feb) as feb_price, AVG(comp_price_mar) as mar_price, AVG(comp_price_apr) as apr_price, AVG(comp_price_may) as may_price, AVG(comp_price_jun) as jun_price, AVG(comp_price_jul) as jul_price, AVG(comp_price_aug) as aug_price, AVG(comp_price_sep) as sep_price, AVG(comp_price_oct) as oct_price, AVG(comp_price_nov) as nov_price, AVG(comp_price_dec) as dec_price');
		
		$data['ur_improved_inasal_classic_prev_year1_jan'] = 0;
		$data['ur_improved_inasal_classic_prev_year1_feb'] = 0;
		$data['ur_improved_inasal_classic_prev_year1_mar'] = 0;
		$data['ur_improved_inasal_classic_prev_year1_apr'] = 0;
		$data['ur_improved_inasal_classic_prev_year1_may'] = 0;
		$data['ur_improved_inasal_classic_prev_year1_jun'] = 0;
		$data['ur_improved_inasal_classic_prev_year1_jul'] = 0;
		$data['ur_improved_inasal_classic_prev_year1_aug'] = 0;
		$data['ur_improved_inasal_classic_prev_year1_sep'] = 0;
		$data['ur_improved_inasal_classic_prev_year1_oct'] = 0;
		$data['ur_improved_inasal_classic_prev_year1_nov'] = 0;
		$data['ur_improved_inasal_classic_prev_year1_dec'] = 0;
		$data['ur_improved_inasal_classic_prev_year1_avg'] = 0;
		$data['ur_improved_inasal_classic_prev_year1_min'] = 0;
		$data['ur_improved_inasal_classic_prev_year1_max'] = 0;

		foreach($ur_improved_inasal_classic_prev_year1 as $row){
			$data['ur_improved_inasal_classic_prev_year1_jan'] = $row->jan_price;
			$data['ur_improved_inasal_classic_prev_year1_feb'] = $row->feb_price;
			$data['ur_improved_inasal_classic_prev_year1_mar'] = $row->mar_price;
			$data['ur_improved_inasal_classic_prev_year1_apr'] = $row->apr_price;
			$data['ur_improved_inasal_classic_prev_year1_may'] = $row->may_price;
			$data['ur_improved_inasal_classic_prev_year1_jun'] = $row->jun_price;
			$data['ur_improved_inasal_classic_prev_year1_jul'] = $row->jul_price;
			$data['ur_improved_inasal_classic_prev_year1_aug'] = $row->aug_price;
			$data['ur_improved_inasal_classic_prev_year1_sep'] = $row->sep_price;
			$data['ur_improved_inasal_classic_prev_year1_oct'] = $row->oct_price;
			$data['ur_improved_inasal_classic_prev_year1_nov'] = $row->nov_price;
			$data['ur_improved_inasal_classic_prev_year1_dec'] = $row->dec_price;
			$data['ur_improved_inasal_classic_prev_year1_avg'] = ($row->jan_price + $row->feb_price + $row->mar_price + $row->apr_price + $row->may_price + $row->jun_price + $row->jul_price + $row->aug_price + $row->sep_price + $row->oct_price + $row->nov_price + $row->dec_price) / 12;

			$data['ur_improved_inasal_classic_prev_year1_min'] = min($row->jan_price, $row->feb_price, $row->mar_price, $row->apr_price, $row->may_price, $row->jun_price, $row->jul_price, $row->aug_price, $row->sep_price, $row->oct_price, $row->nov_price, $row->dec_price);

			$data['ur_improved_inasal_classic_prev_year1_max'] = max($row->jan_price, $row->feb_price, $row->mar_price, $row->apr_price, $row->may_price, $row->jun_price, $row->jul_price, $row->aug_price, $row->sep_price, $row->oct_price, $row->nov_price, $row->dec_price);
		}

		
		/*IMPROVED SWEET AND SPICY*/

		$join_ur_improved_sweet_and_spicy = array(
			'sales_item_tbl b' => 'a.sales_id = b.sales_id AND a.sales_status = 1 AND b.sales_item_status = 1 AND a.sales_year = ' . $year,
			'sales_details_tbl c' => 'b.sales_item_id = c.sales_item_id AND c.sales_det_status = 1',
			'material_tbl d' => 'b.material_id = d.material_id AND d.material_id IN ("1000755", "1000757")',
			'material_group_tbl e' => 'd.material_group_id = e.material_group_id',
			'outlet_tbl f' => 'a.outlet_id = f.outlet_id AND f.bc_id = ' . $bc_id,
			'outlet_brand_tbl g' => 'f.outlet_id = g.outlet_id AND g.outlet_brand_status = 1',
			'brand_tbl h' => 'g.brand_id  = h.brand_id AND h.brand_name = \'ULING ROASTER\''
		);

		$get_ur_improved_sweet_and_spicy = $this->admin->get_join('sales_tbl a', $join_ur_improved_sweet_and_spicy, FALSE, FALSE, 'YEAR(c.sales_det_date), MONTH(c.sales_det_date)', 'AVG(c.sales_det_asp) as asp, MONTH(c.sales_det_date) asp_date');

		$data['ur_improved_sweet_and_spicy1'] = 0;
		$data['ur_improved_sweet_and_spicy2'] = 0;
		$data['ur_improved_sweet_and_spicy3'] = 0;
		$data['ur_improved_sweet_and_spicy4'] = 0;
		$data['ur_improved_sweet_and_spicy5'] = 0;
		$data['ur_improved_sweet_and_spicy6'] = 0;
		$data['ur_improved_sweet_and_spicy7'] = 0;
		$data['ur_improved_sweet_and_spicy8'] = 0;
		$data['ur_improved_sweet_and_spicy9'] = 0;
		$data['ur_improved_sweet_and_spicy10'] = 0;
		$data['ur_improved_sweet_and_spicy11'] = 0;
		$data['ur_improved_sweet_and_spicy12'] = 0;
		$data['ur_improved_sweet_and_spicy_total'] = 0;
		$data['ur_improved_sweet_and_spicy_count'] = 0;
		$data['ur_improved_sweet_and_spicy_avg'] = 0;
		$data['ur_improved_sweet_and_spicy_min'] = 0;
		$data['ur_improved_sweet_and_spicy_max'] = 0;

		foreach($get_ur_improved_sweet_and_spicy as $row){
			$asp = $row->asp;
			$month = $row->asp_date;
			$data['ur_improved_sweet_and_spicy' . $month] = $asp;
			$data['ur_improved_sweet_and_spicy_total'] += $asp;

			if($asp < $data['ur_improved_sweet_and_spicy_min'] || $data['ur_improved_sweet_and_spicy_count'] == 0){
				$data['ur_improved_sweet_and_spicy_min'] = $asp;
			}

			if($asp > $data['ur_improved_sweet_and_spicy_max'] || $data['ur_improved_sweet_and_spicy_count'] == 0){
				$data['ur_improved_sweet_and_spicy_max'] = $asp;
			}

			$data['ur_improved_sweet_and_spicy_count']++;
		}

		$data['ur_improved_sweet_and_spicy_avg'] = $data['ur_improved_sweet_and_spicy_total'] != 0 ? $data['ur_improved_sweet_and_spicy_total'] / $data['ur_improved_sweet_and_spicy_count'] : 0;

		$ur_improved_sweet_and_spicy_prev_year2 = $this->admin->get_data('comparative_price_tbl', array('comp_price_product' => 'IMPROVED SWEET AND SPICY', 'comp_price_segment' => 'UR', 'comp_price_year' => $year - 2,	'bc_id'	=>	$bc_id), 
			FALSE, 'AVG(comp_price_jan) as jan_price, AVG(comp_price_feb) as feb_price, AVG(comp_price_mar) as mar_price, AVG(comp_price_apr) as apr_price, AVG(comp_price_may) as may_price, AVG(comp_price_jun) as jun_price, AVG(comp_price_jul) as jul_price, AVG(comp_price_aug) as aug_price, AVG(comp_price_sep) as sep_price, AVG(comp_price_oct) as oct_price, AVG(comp_price_nov) as nov_price, AVG(comp_price_dec) as dec_price');
		
		$data['ur_improved_sweet_and_spicy_prev_year2_jan'] = 0;
		$data['ur_improved_sweet_and_spicy_prev_year2_feb'] = 0;
		$data['ur_improved_sweet_and_spicy_prev_year2_mar'] = 0;
		$data['ur_improved_sweet_and_spicy_prev_year2_apr'] = 0;
		$data['ur_improved_sweet_and_spicy_prev_year2_may'] = 0;
		$data['ur_improved_sweet_and_spicy_prev_year2_jun'] = 0;
		$data['ur_improved_sweet_and_spicy_prev_year2_jul'] = 0;
		$data['ur_improved_sweet_and_spicy_prev_year2_aug'] = 0;
		$data['ur_improved_sweet_and_spicy_prev_year2_sep'] = 0;
		$data['ur_improved_sweet_and_spicy_prev_year2_oct'] = 0;
		$data['ur_improved_sweet_and_spicy_prev_year2_nov'] = 0;
		$data['ur_improved_sweet_and_spicy_prev_year2_dec'] = 0;
		$data['ur_improved_sweet_and_spicy_prev_year2_avg'] = 0;
		$data['ur_improved_sweet_and_spicy_prev_year2_min'] = 0;
		$data['ur_improved_sweet_and_spicy_prev_year2_max'] = 0;

		foreach($ur_improved_sweet_and_spicy_prev_year2 as $row){
			$data['ur_improved_sweet_and_spicy_prev_year2_jan'] = $row->jan_price;
			$data['ur_improved_sweet_and_spicy_prev_year2_feb'] = $row->feb_price;
			$data['ur_improved_sweet_and_spicy_prev_year2_mar'] = $row->mar_price;
			$data['ur_improved_sweet_and_spicy_prev_year2_apr'] = $row->apr_price;
			$data['ur_improved_sweet_and_spicy_prev_year2_may'] = $row->may_price;
			$data['ur_improved_sweet_and_spicy_prev_year2_jun'] = $row->jun_price;
			$data['ur_improved_sweet_and_spicy_prev_year2_jul'] = $row->jul_price;
			$data['ur_improved_sweet_and_spicy_prev_year2_aug'] = $row->aug_price;
			$data['ur_improved_sweet_and_spicy_prev_year2_sep'] = $row->sep_price;
			$data['ur_improved_sweet_and_spicy_prev_year2_oct'] = $row->oct_price;
			$data['ur_improved_sweet_and_spicy_prev_year2_nov'] = $row->nov_price;
			$data['ur_improved_sweet_and_spicy_prev_year2_dec'] = $row->dec_price;
			$data['ur_improved_sweet_and_spicy_prev_year2_avg'] = ($row->jan_price + $row->feb_price + $row->mar_price + $row->apr_price + $row->may_price + $row->jun_price + $row->jul_price + $row->aug_price + $row->sep_price + $row->oct_price + $row->nov_price + $row->dec_price) / 12;

			$data['ur_improved_sweet_and_spicy_prev_year2_min'] = min($row->jan_price, $row->feb_price, $row->mar_price, $row->apr_price, $row->may_price, $row->jun_price, $row->jul_price, $row->aug_price, $row->sep_price, $row->oct_price, $row->nov_price, $row->dec_price);

			$data['ur_improved_sweet_and_spicy_prev_year2_max'] = max($row->jan_price, $row->feb_price, $row->mar_price, $row->apr_price, $row->may_price, $row->jun_price, $row->jul_price, $row->aug_price, $row->sep_price, $row->oct_price, $row->nov_price, $row->dec_price);
		}


		$ur_improved_sweet_and_spicy_prev_year1 = $this->admin->get_data('comparative_price_tbl', array('comp_price_product' => 'IMPROVED SWEET AND SPICY', 'comp_price_segment' => 'UR', 'comp_price_year' => $year - 1,	'bc_id'	=>	$bc_id), 
			FALSE, 'AVG(comp_price_jan) as jan_price, AVG(comp_price_feb) as feb_price, AVG(comp_price_mar) as mar_price, AVG(comp_price_apr) as apr_price, AVG(comp_price_may) as may_price, AVG(comp_price_jun) as jun_price, AVG(comp_price_jul) as jul_price, AVG(comp_price_aug) as aug_price, AVG(comp_price_sep) as sep_price, AVG(comp_price_oct) as oct_price, AVG(comp_price_nov) as nov_price, AVG(comp_price_dec) as dec_price');
		
		$data['ur_improved_sweet_and_spicy_prev_year1_jan'] = 0;
		$data['ur_improved_sweet_and_spicy_prev_year1_feb'] = 0;
		$data['ur_improved_sweet_and_spicy_prev_year1_mar'] = 0;
		$data['ur_improved_sweet_and_spicy_prev_year1_apr'] = 0;
		$data['ur_improved_sweet_and_spicy_prev_year1_may'] = 0;
		$data['ur_improved_sweet_and_spicy_prev_year1_jun'] = 0;
		$data['ur_improved_sweet_and_spicy_prev_year1_jul'] = 0;
		$data['ur_improved_sweet_and_spicy_prev_year1_aug'] = 0;
		$data['ur_improved_sweet_and_spicy_prev_year1_sep'] = 0;
		$data['ur_improved_sweet_and_spicy_prev_year1_oct'] = 0;
		$data['ur_improved_sweet_and_spicy_prev_year1_nov'] = 0;
		$data['ur_improved_sweet_and_spicy_prev_year1_dec'] = 0;
		$data['ur_improved_sweet_and_spicy_prev_year1_avg'] = 0;
		$data['ur_improved_sweet_and_spicy_prev_year1_min'] = 0;
		$data['ur_improved_sweet_and_spicy_prev_year1_max'] = 0;

		foreach($ur_improved_sweet_and_spicy_prev_year1 as $row){
			$data['ur_improved_sweet_and_spicy_prev_year1_jan'] = $row->jan_price;
			$data['ur_improved_sweet_and_spicy_prev_year1_feb'] = $row->feb_price;
			$data['ur_improved_sweet_and_spicy_prev_year1_mar'] = $row->mar_price;
			$data['ur_improved_sweet_and_spicy_prev_year1_apr'] = $row->apr_price;
			$data['ur_improved_sweet_and_spicy_prev_year1_may'] = $row->may_price;
			$data['ur_improved_sweet_and_spicy_prev_year1_jun'] = $row->jun_price;
			$data['ur_improved_sweet_and_spicy_prev_year1_jul'] = $row->jul_price;
			$data['ur_improved_sweet_and_spicy_prev_year1_aug'] = $row->aug_price;
			$data['ur_improved_sweet_and_spicy_prev_year1_sep'] = $row->sep_price;
			$data['ur_improved_sweet_and_spicy_prev_year1_oct'] = $row->oct_price;
			$data['ur_improved_sweet_and_spicy_prev_year1_nov'] = $row->nov_price;
			$data['ur_improved_sweet_and_spicy_prev_year1_dec'] = $row->dec_price;
			$data['ur_improved_sweet_and_spicy_prev_year1_avg'] = ($row->jan_price + $row->feb_price + $row->mar_price + $row->apr_price + $row->may_price + $row->jun_price + $row->jul_price + $row->aug_price + $row->sep_price + $row->oct_price + $row->nov_price + $row->dec_price) / 12;

			$data['ur_improved_sweet_and_spicy_prev_year1_min'] = min($row->jan_price, $row->feb_price, $row->mar_price, $row->apr_price, $row->may_price, $row->jun_price, $row->jul_price, $row->aug_price, $row->sep_price, $row->oct_price, $row->nov_price, $row->dec_price);

			$data['ur_improved_sweet_and_spicy_prev_year1_max'] = max($row->jan_price, $row->feb_price, $row->mar_price, $row->apr_price, $row->may_price, $row->jun_price, $row->jul_price, $row->aug_price, $row->sep_price, $row->oct_price, $row->nov_price, $row->dec_price);
		}

		/*UR Liempo*/
		$join_ur_liempo = array(
			'sales_item_tbl b' => 'a.sales_id = b.sales_id AND a.sales_status = 1 AND b.sales_item_status = 1 AND a.sales_year = ' . $year,
			'sales_details_tbl c' => 'b.sales_item_id = c.sales_item_id AND c.sales_det_status = 1',
			'material_tbl d' => 'b.material_id = d.material_id',
			'material_group_tbl e' => 'd.material_group_id = e.material_group_id AND e.material_group_name = "LIEMPO"',
			'outlet_tbl f' => 'a.outlet_id = f.outlet_id AND f.bc_id = ' . $bc_id,
			'outlet_brand_tbl g' => 'f.outlet_id = g.outlet_id AND g.outlet_brand_status = 1',
			'brand_tbl h' => 'g.brand_id  = h.brand_id AND h.brand_name = \'ULING ROASTER\''
		);

		$get_ur_liempo = $this->admin->get_join('sales_tbl a', $join_ur_liempo, FALSE, FALSE, 'YEAR(c.sales_det_date), MONTH(c.sales_det_date)', 'AVG(c.sales_det_asp) as asp, MONTH(c.sales_det_date) asp_date');

		$data['ur_liempo1'] = 0;
		$data['ur_liempo2'] = 0;
		$data['ur_liempo3'] = 0;
		$data['ur_liempo4'] = 0;
		$data['ur_liempo5'] = 0;
		$data['ur_liempo6'] = 0;
		$data['ur_liempo7'] = 0;
		$data['ur_liempo8'] = 0;
		$data['ur_liempo9'] = 0;
		$data['ur_liempo10'] = 0;
		$data['ur_liempo11'] = 0;
		$data['ur_liempo12'] = 0;
		$data['ur_liempo_total'] = 0;
		$data['ur_liempo_count'] = 0;
		$data['ur_liempo_avg'] = 0;
		$data['ur_liempo_min'] = 0;
		$data['ur_liempo_max'] = 0;

		foreach($get_ur_liempo as $row){
			$asp = $row->asp;
			$month = $row->asp_date;
			$data['ur_liempo' . $month] = $asp;
			$data['ur_liempo_total'] += $asp;

			if($asp < $data['ur_liempo_min'] || $data['ur_liempo_count'] == 0){
				$data['ur_liempo_min'] = $asp;
			}

			if($asp > $data['ur_liempo_max'] || $data['ur_liempo_count'] == 0){
				$data['ur_liempo_max'] = $asp;
			}

			$data['ur_liempo_count']++;
		}

		$data['ur_liempo_avg'] = $data['ur_liempo_total'] != 0 ? $data['ur_liempo_total'] / $data['ur_liempo_count'] : 0;

		$ur_liempo_prev_year2 = $this->admin->get_data('comparative_price_tbl', array('comp_price_product' => 'LIEMPO', 'comp_price_segment' => 'UR', 'comp_price_year' => $year - 2,	'bc_id'	=>	$bc_id), 
			FALSE, 'AVG(comp_price_jan) as jan_price, AVG(comp_price_feb) as feb_price, AVG(comp_price_mar) as mar_price, AVG(comp_price_apr) as apr_price, AVG(comp_price_may) as may_price, AVG(comp_price_jun) as jun_price, AVG(comp_price_jul) as jul_price, AVG(comp_price_aug) as aug_price, AVG(comp_price_sep) as sep_price, AVG(comp_price_oct) as oct_price, AVG(comp_price_nov) as nov_price, AVG(comp_price_dec) as dec_price');
		
		$data['ur_liempo_prev_year2_jan'] = 0;
		$data['ur_liempo_prev_year2_feb'] = 0;
		$data['ur_liempo_prev_year2_mar'] = 0;
		$data['ur_liempo_prev_year2_apr'] = 0;
		$data['ur_liempo_prev_year2_may'] = 0;
		$data['ur_liempo_prev_year2_jun'] = 0;
		$data['ur_liempo_prev_year2_jul'] = 0;
		$data['ur_liempo_prev_year2_aug'] = 0;
		$data['ur_liempo_prev_year2_sep'] = 0;
		$data['ur_liempo_prev_year2_oct'] = 0;
		$data['ur_liempo_prev_year2_nov'] = 0;
		$data['ur_liempo_prev_year2_dec'] = 0;
		$data['ur_liempo_prev_year2_avg'] = 0;
		$data['ur_liempo_prev_year2_min'] = 0;
		$data['ur_liempo_prev_year2_max'] = 0;

		foreach($ur_liempo_prev_year2 as $row){
			$data['ur_liempo_prev_year2_jan'] = $row->jan_price;
			$data['ur_liempo_prev_year2_feb'] = $row->feb_price;
			$data['ur_liempo_prev_year2_mar'] = $row->mar_price;
			$data['ur_liempo_prev_year2_apr'] = $row->apr_price;
			$data['ur_liempo_prev_year2_may'] = $row->may_price;
			$data['ur_liempo_prev_year2_jun'] = $row->jun_price;
			$data['ur_liempo_prev_year2_jul'] = $row->jul_price;
			$data['ur_liempo_prev_year2_aug'] = $row->aug_price;
			$data['ur_liempo_prev_year2_sep'] = $row->sep_price;
			$data['ur_liempo_prev_year2_oct'] = $row->oct_price;
			$data['ur_liempo_prev_year2_nov'] = $row->nov_price;
			$data['ur_liempo_prev_year2_dec'] = $row->dec_price;
			$data['ur_liempo_prev_year2_avg'] = ($row->jan_price + $row->feb_price + $row->mar_price + $row->apr_price + $row->may_price + $row->jun_price + $row->jul_price + $row->aug_price + $row->sep_price + $row->oct_price + $row->nov_price + $row->dec_price) / 12;

			$data['ur_liempo_prev_year2_min'] = min($row->jan_price, $row->feb_price, $row->mar_price, $row->apr_price, $row->may_price, $row->jun_price, $row->jul_price, $row->aug_price, $row->sep_price, $row->oct_price, $row->nov_price, $row->dec_price);

			$data['ur_liempo_prev_year2_max'] = max($row->jan_price, $row->feb_price, $row->mar_price, $row->apr_price, $row->may_price, $row->jun_price, $row->jul_price, $row->aug_price, $row->sep_price, $row->oct_price, $row->nov_price, $row->dec_price);
		}


		$ur_liempo_prev_year1 = $this->admin->get_data('comparative_price_tbl', array('comp_price_product' => 'LIEMPO', 'comp_price_segment' => 'UR', 'comp_price_year' => $year - 1,	'bc_id'	=>	$bc_id), 
			FALSE, 'AVG(comp_price_jan) as jan_price, AVG(comp_price_feb) as feb_price, AVG(comp_price_mar) as mar_price, AVG(comp_price_apr) as apr_price, AVG(comp_price_may) as may_price, AVG(comp_price_jun) as jun_price, AVG(comp_price_jul) as jul_price, AVG(comp_price_aug) as aug_price, AVG(comp_price_sep) as sep_price, AVG(comp_price_oct) as oct_price, AVG(comp_price_nov) as nov_price, AVG(comp_price_dec) as dec_price');
		
		$data['ur_liempo_prev_year1_jan'] = 0;
		$data['ur_liempo_prev_year1_feb'] = 0;
		$data['ur_liempo_prev_year1_mar'] = 0;
		$data['ur_liempo_prev_year1_apr'] = 0;
		$data['ur_liempo_prev_year1_may'] = 0;
		$data['ur_liempo_prev_year1_jun'] = 0;
		$data['ur_liempo_prev_year1_jul'] = 0;
		$data['ur_liempo_prev_year1_aug'] = 0;
		$data['ur_liempo_prev_year1_sep'] = 0;
		$data['ur_liempo_prev_year1_oct'] = 0;
		$data['ur_liempo_prev_year1_nov'] = 0;
		$data['ur_liempo_prev_year1_dec'] = 0;
		$data['ur_liempo_prev_year1_avg'] = 0;
		$data['ur_liempo_prev_year1_min'] = 0;
		$data['ur_liempo_prev_year1_max'] = 0;

		foreach($ur_liempo_prev_year1 as $row){
			$data['ur_liempo_prev_year1_jan'] = $row->jan_price;
			$data['ur_liempo_prev_year1_feb'] = $row->feb_price;
			$data['ur_liempo_prev_year1_mar'] = $row->mar_price;
			$data['ur_liempo_prev_year1_apr'] = $row->apr_price;
			$data['ur_liempo_prev_year1_may'] = $row->may_price;
			$data['ur_liempo_prev_year1_jun'] = $row->jun_price;
			$data['ur_liempo_prev_year1_jul'] = $row->jul_price;
			$data['ur_liempo_prev_year1_aug'] = $row->aug_price;
			$data['ur_liempo_prev_year1_sep'] = $row->sep_price;
			$data['ur_liempo_prev_year1_oct'] = $row->oct_price;
			$data['ur_liempo_prev_year1_nov'] = $row->nov_price;
			$data['ur_liempo_prev_year1_dec'] = $row->dec_price;
			$data['ur_liempo_prev_year1_avg'] = ($row->jan_price + $row->feb_price + $row->mar_price + $row->apr_price + $row->may_price + $row->jun_price + $row->jul_price + $row->aug_price + $row->sep_price + $row->oct_price + $row->nov_price + $row->dec_price) / 12;

			$data['ur_liempo_prev_year1_min'] = min($row->jan_price, $row->feb_price, $row->mar_price, $row->apr_price, $row->may_price, $row->jun_price, $row->jul_price, $row->aug_price, $row->sep_price, $row->oct_price, $row->nov_price, $row->dec_price);

			$data['ur_liempo_prev_year1_max'] = max($row->jan_price, $row->feb_price, $row->mar_price, $row->apr_price, $row->may_price, $row->jun_price, $row->jul_price, $row->aug_price, $row->sep_price, $row->oct_price, $row->nov_price, $row->dec_price);
		}

		
		/*UR Dressed*/
		$join_ur_dressed = array(
			'sales_item_tbl b' => 'a.sales_id = b.sales_id AND a.sales_status = 1 AND b.sales_item_status = 1 AND a.sales_year = ' . $year,
			'sales_details_tbl c' => 'b.sales_item_id = c.sales_item_id AND c.sales_det_status = 1',
			'material_tbl d' => 'b.material_id = d.material_id',
			'material_group_tbl e' => 'd.material_group_id = e.material_group_id AND e.material_group_name = "DRESSED"',
			'outlet_tbl f' => 'a.outlet_id = f.outlet_id AND f.bc_id = ' . $bc_id,
			'outlet_brand_tbl g' => 'f.outlet_id = g.outlet_id AND g.outlet_brand_status = 1',
			'brand_tbl h' => 'g.brand_id  = h.brand_id AND h.brand_name = \'ULING ROASTER\''
		);

		$get_ur_dressed = $this->admin->get_join('sales_tbl a', $join_ur_dressed, FALSE, FALSE, 'YEAR(c.sales_det_date), MONTH(c.sales_det_date)', 'AVG(c.sales_det_asp) as asp, MONTH(c.sales_det_date) asp_date');

		$data['ur_dressed1'] = 0;
		$data['ur_dressed2'] = 0;
		$data['ur_dressed3'] = 0;
		$data['ur_dressed4'] = 0;
		$data['ur_dressed5'] = 0;
		$data['ur_dressed6'] = 0;
		$data['ur_dressed7'] = 0;
		$data['ur_dressed8'] = 0;
		$data['ur_dressed9'] = 0;
		$data['ur_dressed10'] = 0;
		$data['ur_dressed11'] = 0;
		$data['ur_dressed12'] = 0;
		$data['ur_dressed_total'] = 0;
		$data['ur_dressed_count'] = 0;
		$data['ur_dressed_avg'] = 0;
		$data['ur_dressed_min'] = 0;
		$data['ur_dressed_max'] = 0;

		foreach($get_ur_dressed as $row){
			$asp = $row->asp;
			$month = $row->asp_date;
			$data['ur_dressed' . $month] = $asp;
			$data['ur_dressed_total'] += $asp;

			if($asp < $data['ur_dressed_min'] || $data['ur_dressed_count'] == 0){
				$data['ur_dressed_min'] = $asp;
			}

			if($asp > $data['ur_dressed_max'] || $data['ur_dressed_count'] == 0){
				$data['ur_dressed_max'] = $asp;
			}

			$data['ur_dressed_count']++;
		}

		$data['ur_dressed_avg'] = $data['ur_dressed_total'] != 0 ? $data['ur_dressed_total'] / $data['ur_dressed_count'] : 0;

		$ur_dressed_prev_year2 = $this->admin->get_data('comparative_price_tbl', array('comp_price_product' => 'DRESSED', 'comp_price_segment' => 'UR', 'comp_price_year' => $year - 2,	'bc_id'	=>	$bc_id), 
			FALSE, 'AVG(comp_price_jan) as jan_price, AVG(comp_price_feb) as feb_price, AVG(comp_price_mar) as mar_price, AVG(comp_price_apr) as apr_price, AVG(comp_price_may) as may_price, AVG(comp_price_jun) as jun_price, AVG(comp_price_jul) as jul_price, AVG(comp_price_aug) as aug_price, AVG(comp_price_sep) as sep_price, AVG(comp_price_oct) as oct_price, AVG(comp_price_nov) as nov_price, AVG(comp_price_dec) as dec_price');
		
		$data['ur_dressed_prev_year2_jan'] = 0;
		$data['ur_dressed_prev_year2_feb'] = 0;
		$data['ur_dressed_prev_year2_mar'] = 0;
		$data['ur_dressed_prev_year2_apr'] = 0;
		$data['ur_dressed_prev_year2_may'] = 0;
		$data['ur_dressed_prev_year2_jun'] = 0;
		$data['ur_dressed_prev_year2_jul'] = 0;
		$data['ur_dressed_prev_year2_aug'] = 0;
		$data['ur_dressed_prev_year2_sep'] = 0;
		$data['ur_dressed_prev_year2_oct'] = 0;
		$data['ur_dressed_prev_year2_nov'] = 0;
		$data['ur_dressed_prev_year2_dec'] = 0;
		$data['ur_dressed_prev_year2_avg'] = 0;
		$data['ur_dressed_prev_year2_min'] = 0;
		$data['ur_dressed_prev_year2_max'] = 0;

		foreach($ur_dressed_prev_year2 as $row){
			$data['ur_dressed_prev_year2_jan'] = $row->jan_price;
			$data['ur_dressed_prev_year2_feb'] = $row->feb_price;
			$data['ur_dressed_prev_year2_mar'] = $row->mar_price;
			$data['ur_dressed_prev_year2_apr'] = $row->apr_price;
			$data['ur_dressed_prev_year2_may'] = $row->may_price;
			$data['ur_dressed_prev_year2_jun'] = $row->jun_price;
			$data['ur_dressed_prev_year2_jul'] = $row->jul_price;
			$data['ur_dressed_prev_year2_aug'] = $row->aug_price;
			$data['ur_dressed_prev_year2_sep'] = $row->sep_price;
			$data['ur_dressed_prev_year2_oct'] = $row->oct_price;
			$data['ur_dressed_prev_year2_nov'] = $row->nov_price;
			$data['ur_dressed_prev_year2_dec'] = $row->dec_price;
			$data['ur_dressed_prev_year2_avg'] = ($row->jan_price + $row->feb_price + $row->mar_price + $row->apr_price + $row->may_price + $row->jun_price + $row->jul_price + $row->aug_price + $row->sep_price + $row->oct_price + $row->nov_price + $row->dec_price) / 12;

			$data['ur_dressed_prev_year2_min'] = min($row->jan_price, $row->feb_price, $row->mar_price, $row->apr_price, $row->may_price, $row->jun_price, $row->jul_price, $row->aug_price, $row->sep_price, $row->oct_price, $row->nov_price, $row->dec_price);

			$data['ur_dressed_prev_year2_max'] = max($row->jan_price, $row->feb_price, $row->mar_price, $row->apr_price, $row->may_price, $row->jun_price, $row->jul_price, $row->aug_price, $row->sep_price, $row->oct_price, $row->nov_price, $row->dec_price);
		}


		$ur_dressed_prev_year1 = $this->admin->get_data('comparative_price_tbl', array('comp_price_product' => 'DRESSED', 'comp_price_segment' => 'UR', 'comp_price_year' => $year - 1,	'bc_id'	=>	$bc_id), 
			FALSE, 'AVG(comp_price_jan) as jan_price, AVG(comp_price_feb) as feb_price, AVG(comp_price_mar) as mar_price, AVG(comp_price_apr) as apr_price, AVG(comp_price_may) as may_price, AVG(comp_price_jun) as jun_price, AVG(comp_price_jul) as jul_price, AVG(comp_price_aug) as aug_price, AVG(comp_price_sep) as sep_price, AVG(comp_price_oct) as oct_price, AVG(comp_price_nov) as nov_price, AVG(comp_price_dec) as dec_price');
		
		$data['ur_dressed_prev_year1_jan'] = 0;
		$data['ur_dressed_prev_year1_feb'] = 0;
		$data['ur_dressed_prev_year1_mar'] = 0;
		$data['ur_dressed_prev_year1_apr'] = 0;
		$data['ur_dressed_prev_year1_may'] = 0;
		$data['ur_dressed_prev_year1_jun'] = 0;
		$data['ur_dressed_prev_year1_jul'] = 0;
		$data['ur_dressed_prev_year1_aug'] = 0;
		$data['ur_dressed_prev_year1_sep'] = 0;
		$data['ur_dressed_prev_year1_oct'] = 0;
		$data['ur_dressed_prev_year1_nov'] = 0;
		$data['ur_dressed_prev_year1_dec'] = 0;
		$data['ur_dressed_prev_year1_avg'] = 0;
		$data['ur_dressed_prev_year1_min'] = 0;
		$data['ur_dressed_prev_year1_max'] = 0;

		foreach($ur_dressed_prev_year1 as $row){
			$data['ur_dressed_prev_year1_jan'] = $row->jan_price;
			$data['ur_dressed_prev_year1_feb'] = $row->feb_price;
			$data['ur_dressed_prev_year1_mar'] = $row->mar_price;
			$data['ur_dressed_prev_year1_apr'] = $row->apr_price;
			$data['ur_dressed_prev_year1_may'] = $row->may_price;
			$data['ur_dressed_prev_year1_jun'] = $row->jun_price;
			$data['ur_dressed_prev_year1_jul'] = $row->jul_price;
			$data['ur_dressed_prev_year1_aug'] = $row->aug_price;
			$data['ur_dressed_prev_year1_sep'] = $row->sep_price;
			$data['ur_dressed_prev_year1_oct'] = $row->oct_price;
			$data['ur_dressed_prev_year1_nov'] = $row->nov_price;
			$data['ur_dressed_prev_year1_dec'] = $row->dec_price;
			$data['ur_dressed_prev_year1_avg'] = ($row->jan_price + $row->feb_price + $row->mar_price + $row->apr_price + $row->may_price + $row->jun_price + $row->jul_price + $row->aug_price + $row->sep_price + $row->oct_price + $row->nov_price + $row->dec_price) / 12;

			$data['ur_dressed_prev_year1_min'] = min($row->jan_price, $row->feb_price, $row->mar_price, $row->apr_price, $row->may_price, $row->jun_price, $row->jul_price, $row->aug_price, $row->sep_price, $row->oct_price, $row->nov_price, $row->dec_price);

			$data['ur_dressed_prev_year1_max'] = max($row->jan_price, $row->feb_price, $row->mar_price, $row->apr_price, $row->may_price, $row->jun_price, $row->jul_price, $row->aug_price, $row->sep_price, $row->oct_price, $row->nov_price, $row->dec_price);
		}

		

		/*UR Chooksies*/
		$join_ur_chooksies = array(
			'sales_item_tbl b' => 'a.sales_id = b.sales_id AND a.sales_status = 1 AND b.sales_item_status = 1 AND a.sales_year = ' . $year,
			'sales_details_tbl c' => 'b.sales_item_id = c.sales_item_id AND c.sales_det_status = 1',
			'material_tbl d' => 'b.material_id = d.material_id AND (d.material_code = 10200095 OR d.material_code = 10200096 OR d.material_code = 10200097)',
			'material_group_tbl e' => 'd.material_group_id = e.material_group_id AND e.material_group_name = \'MARINATED CUT UPS\'',
			'outlet_tbl f' => 'a.outlet_id = f.outlet_id AND f.bc_id = ' . $bc_id,
			'outlet_brand_tbl g' => 'f.outlet_id = g.outlet_id AND g.outlet_brand_status = 1',
			'brand_tbl h' => 'g.brand_id  = h.brand_id AND h.brand_name = \'ULING ROASTER\''
		);

		$get_ur_chooksies = $this->admin->get_join('sales_tbl a', $join_ur_chooksies, FALSE, FALSE, 'YEAR(c.sales_det_date), MONTH(c.sales_det_date)', 'AVG(c.sales_det_asp) as asp, MONTH(c.sales_det_date) asp_date');

		$data['ur_chooksies1'] = 0;
		$data['ur_chooksies2'] = 0;
		$data['ur_chooksies3'] = 0;
		$data['ur_chooksies4'] = 0;
		$data['ur_chooksies5'] = 0;
		$data['ur_chooksies6'] = 0;
		$data['ur_chooksies7'] = 0;
		$data['ur_chooksies8'] = 0;
		$data['ur_chooksies9'] = 0;
		$data['ur_chooksies10'] = 0;
		$data['ur_chooksies11'] = 0;
		$data['ur_chooksies12'] = 0;
		$data['ur_chooksies_total'] = 0;
		$data['ur_chooksies_count'] = 0;
		$data['ur_chooksies_avg'] = 0;
		$data['ur_chooksies_min'] = 0;
		$data['ur_chooksies_max'] = 0;

		foreach($get_ur_chooksies as $row){
			$asp = $row->asp;
			$month = $row->asp_date;
			$data['ur_chooksies' . $month] = $asp;
			$data['ur_chooksies_total'] += $asp;

			if($asp < $data['ur_chooksies_min'] || $data['ur_chooksies_count'] == 0){
				$data['ur_chooksies_min'] = $asp;
			}

			if($asp > $data['ur_chooksies_max'] || $data['ur_chooksies_count'] == 0){
				$data['ur_chooksies_max'] = $asp;
			}

			$data['ur_chooksies_count']++;
		}

		$data['ur_chooksies_avg'] = $data['ur_chooksies_total'] != 0 ? $data['ur_chooksies_total'] / $data['ur_chooksies_count'] : 0;

		$ur_chooksies_prev_year2 = $this->admin->get_data('comparative_price_tbl', array('comp_price_product' => 'CHOOKSIES MARINADO', 'comp_price_segment' => 'UR', 'comp_price_year' => $year - 2,	'bc_id'	=>	$bc_id), 
			FALSE, 'AVG(comp_price_jan) as jan_price, AVG(comp_price_feb) as feb_price, AVG(comp_price_mar) as mar_price, AVG(comp_price_apr) as apr_price, AVG(comp_price_may) as may_price, AVG(comp_price_jun) as jun_price, AVG(comp_price_jul) as jul_price, AVG(comp_price_aug) as aug_price, AVG(comp_price_sep) as sep_price, AVG(comp_price_oct) as oct_price, AVG(comp_price_nov) as nov_price, AVG(comp_price_dec) as dec_price');
		
		$data['ur_chooksies_prev_year2_jan'] = 0;
		$data['ur_chooksies_prev_year2_feb'] = 0;
		$data['ur_chooksies_prev_year2_mar'] = 0;
		$data['ur_chooksies_prev_year2_apr'] = 0;
		$data['ur_chooksies_prev_year2_may'] = 0;
		$data['ur_chooksies_prev_year2_jun'] = 0;
		$data['ur_chooksies_prev_year2_jul'] = 0;
		$data['ur_chooksies_prev_year2_aug'] = 0;
		$data['ur_chooksies_prev_year2_sep'] = 0;
		$data['ur_chooksies_prev_year2_oct'] = 0;
		$data['ur_chooksies_prev_year2_nov'] = 0;
		$data['ur_chooksies_prev_year2_dec'] = 0;
		$data['ur_chooksies_prev_year2_avg'] = 0;
		$data['ur_chooksies_prev_year2_min'] = 0;
		$data['ur_chooksies_prev_year2_max'] = 0;

		foreach($ur_chooksies_prev_year2 as $row){
			$data['ur_chooksies_prev_year2_jan'] = $row->jan_price;
			$data['ur_chooksies_prev_year2_feb'] = $row->feb_price;
			$data['ur_chooksies_prev_year2_mar'] = $row->mar_price;
			$data['ur_chooksies_prev_year2_apr'] = $row->apr_price;
			$data['ur_chooksies_prev_year2_may'] = $row->may_price;
			$data['ur_chooksies_prev_year2_jun'] = $row->jun_price;
			$data['ur_chooksies_prev_year2_jul'] = $row->jul_price;
			$data['ur_chooksies_prev_year2_aug'] = $row->aug_price;
			$data['ur_chooksies_prev_year2_sep'] = $row->sep_price;
			$data['ur_chooksies_prev_year2_oct'] = $row->oct_price;
			$data['ur_chooksies_prev_year2_nov'] = $row->nov_price;
			$data['ur_chooksies_prev_year2_dec'] = $row->dec_price;
			$data['ur_chooksies_prev_year2_avg'] = ($row->jan_price + $row->feb_price + $row->mar_price + $row->apr_price + $row->may_price + $row->jun_price + $row->jul_price + $row->aug_price + $row->sep_price + $row->oct_price + $row->nov_price + $row->dec_price) / 12;

			$data['ur_chooksies_prev_year2_min'] = min($row->jan_price, $row->feb_price, $row->mar_price, $row->apr_price, $row->may_price, $row->jun_price, $row->jul_price, $row->aug_price, $row->sep_price, $row->oct_price, $row->nov_price, $row->dec_price);

			$data['ur_chooksies_prev_year2_max'] = max($row->jan_price, $row->feb_price, $row->mar_price, $row->apr_price, $row->may_price, $row->jun_price, $row->jul_price, $row->aug_price, $row->sep_price, $row->oct_price, $row->nov_price, $row->dec_price);
		}


		$ur_chooksies_prev_year1 = $this->admin->get_data('comparative_price_tbl', array('comp_price_product' => 'CHOOKSIES MARINADO', 'comp_price_segment' => 'UR', 'comp_price_year' => $year - 1,	'bc_id'	=>	$bc_id), 
			FALSE, 'AVG(comp_price_jan) as jan_price, AVG(comp_price_feb) as feb_price, AVG(comp_price_mar) as mar_price, AVG(comp_price_apr) as apr_price, AVG(comp_price_may) as may_price, AVG(comp_price_jun) as jun_price, AVG(comp_price_jul) as jul_price, AVG(comp_price_aug) as aug_price, AVG(comp_price_sep) as sep_price, AVG(comp_price_oct) as oct_price, AVG(comp_price_nov) as nov_price, AVG(comp_price_dec) as dec_price');
		
		$data['ur_chooksies_prev_year1_jan'] = 0;
		$data['ur_chooksies_prev_year1_feb'] = 0;
		$data['ur_chooksies_prev_year1_mar'] = 0;
		$data['ur_chooksies_prev_year1_apr'] = 0;
		$data['ur_chooksies_prev_year1_may'] = 0;
		$data['ur_chooksies_prev_year1_jun'] = 0;
		$data['ur_chooksies_prev_year1_jul'] = 0;
		$data['ur_chooksies_prev_year1_aug'] = 0;
		$data['ur_chooksies_prev_year1_sep'] = 0;
		$data['ur_chooksies_prev_year1_oct'] = 0;
		$data['ur_chooksies_prev_year1_nov'] = 0;
		$data['ur_chooksies_prev_year1_dec'] = 0;
		$data['ur_chooksies_prev_year1_avg'] = 0;
		$data['ur_chooksies_prev_year1_min'] = 0;
		$data['ur_chooksies_prev_year1_max'] = 0;

		foreach($ur_chooksies_prev_year1 as $row){
			$data['ur_chooksies_prev_year1_jan'] = $row->jan_price;
			$data['ur_chooksies_prev_year1_feb'] = $row->feb_price;
			$data['ur_chooksies_prev_year1_mar'] = $row->mar_price;
			$data['ur_chooksies_prev_year1_apr'] = $row->apr_price;
			$data['ur_chooksies_prev_year1_may'] = $row->may_price;
			$data['ur_chooksies_prev_year1_jun'] = $row->jun_price;
			$data['ur_chooksies_prev_year1_jul'] = $row->jul_price;
			$data['ur_chooksies_prev_year1_aug'] = $row->aug_price;
			$data['ur_chooksies_prev_year1_sep'] = $row->sep_price;
			$data['ur_chooksies_prev_year1_oct'] = $row->oct_price;
			$data['ur_chooksies_prev_year1_nov'] = $row->nov_price;
			$data['ur_chooksies_prev_year1_dec'] = $row->dec_price;
			$data['ur_chooksies_prev_year1_avg'] = ($row->jan_price + $row->feb_price + $row->mar_price + $row->apr_price + $row->may_price + $row->jun_price + $row->jul_price + $row->aug_price + $row->sep_price + $row->oct_price + $row->nov_price + $row->dec_price) / 12;

			$data['ur_chooksies_prev_year1_min'] = min($row->jan_price, $row->feb_price, $row->mar_price, $row->apr_price, $row->may_price, $row->jun_price, $row->jul_price, $row->aug_price, $row->sep_price, $row->oct_price, $row->nov_price, $row->dec_price);

			$data['ur_chooksies_prev_year1_max'] = max($row->jan_price, $row->feb_price, $row->mar_price, $row->apr_price, $row->may_price, $row->jun_price, $row->jul_price, $row->aug_price, $row->sep_price, $row->oct_price, $row->nov_price, $row->dec_price);
		}


		$join_ur_marinado = array(
			'sales_item_tbl b' => 'a.sales_id = b.sales_id AND a.sales_status = 1 AND b.sales_item_status = 1 AND a.sales_year = ' . $year,
			'sales_details_tbl c' => 'b.sales_item_id = c.sales_item_id AND c.sales_det_status = 1',
			'material_tbl d' => 'b.material_id = d.material_id AND d.material_code = 1000638',
			'material_group_tbl e' => 'd.material_group_id = e.material_group_id AND e.material_group_name = \'MARINATED CUT UPS\'',
			'outlet_tbl f' => 'a.outlet_id = f.outlet_id AND f.bc_id = ' . $bc_id,
			'outlet_brand_tbl g' => 'f.outlet_id = g.outlet_id AND g.outlet_brand_status = 1',
			'brand_tbl h' => 'g.brand_id  = h.brand_id AND h.brand_name = \'ULING ROASTER\''
		);

		$get_ur_marinado = $this->admin->get_join('sales_tbl a', $join_ur_marinado, FALSE, FALSE, 'YEAR(c.sales_det_date), MONTH(c.sales_det_date)', 'AVG(c.sales_det_asp) as asp, MONTH(c.sales_det_date) asp_date');

		$data['ur_marinado1'] = 0;
		$data['ur_marinado2'] = 0;
		$data['ur_marinado3'] = 0;
		$data['ur_marinado4'] = 0;
		$data['ur_marinado5'] = 0;
		$data['ur_marinado6'] = 0;
		$data['ur_marinado7'] = 0;
		$data['ur_marinado8'] = 0;
		$data['ur_marinado9'] = 0;
		$data['ur_marinado10'] = 0;
		$data['ur_marinado11'] = 0;
		$data['ur_marinado12'] = 0;
		$data['ur_marinado_total'] = 0;
		$data['ur_marinado_count'] = 0;
		$data['ur_marinado_avg'] = 0;
		$data['ur_marinado_min'] = 0;
		$data['ur_marinado_max'] = 0;

		foreach($get_ur_marinado as $row){
			$asp = $row->asp;
			$month = $row->asp_date;
			$data['ur_marinado' . $month] = $asp;
			$data['ur_marinado_total'] += $asp;

			if($asp < $data['ur_marinado_min'] || $data['ur_marinado_count'] == 0){
				$data['ur_marinado_min'] = $asp;
			}

			if($asp > $data['ur_marinado_max'] || $data['ur_marinado_count'] == 0){
				$data['ur_marinado_max'] = $asp;
			}

			$data['ur_marinado_count']++;
		}

		$data['ur_marinado_avg'] = $data['ur_marinado_total'] != 0 ? $data['ur_marinado_total'] / $data['ur_marinado_count'] : 0;

		$ur_marinado_prev_year2 = $this->admin->get_data('comparative_price_tbl', array('comp_price_product' => 'MARINADO FRIED', 'comp_price_segment' => 'UR', 'comp_price_year' => $year - 2,	'bc_id'	=>	$bc_id), 
			FALSE, 'AVG(comp_price_jan) as jan_price, AVG(comp_price_feb) as feb_price, AVG(comp_price_mar) as mar_price, AVG(comp_price_apr) as apr_price, AVG(comp_price_may) as may_price, AVG(comp_price_jun) as jun_price, AVG(comp_price_jul) as jul_price, AVG(comp_price_aug) as aug_price, AVG(comp_price_sep) as sep_price, AVG(comp_price_oct) as oct_price, AVG(comp_price_nov) as nov_price, AVG(comp_price_dec) as dec_price');
		
		$data['ur_marinado_prev_year2_jan'] = 0;
		$data['ur_marinado_prev_year2_feb'] = 0;
		$data['ur_marinado_prev_year2_mar'] = 0;
		$data['ur_marinado_prev_year2_apr'] = 0;
		$data['ur_marinado_prev_year2_may'] = 0;
		$data['ur_marinado_prev_year2_jun'] = 0;
		$data['ur_marinado_prev_year2_jul'] = 0;
		$data['ur_marinado_prev_year2_aug'] = 0;
		$data['ur_marinado_prev_year2_sep'] = 0;
		$data['ur_marinado_prev_year2_oct'] = 0;
		$data['ur_marinado_prev_year2_nov'] = 0;
		$data['ur_marinado_prev_year2_dec'] = 0;
		$data['ur_marinado_prev_year2_avg'] = 0;
		$data['ur_marinado_prev_year2_min'] = 0;
		$data['ur_marinado_prev_year2_max'] = 0;

		foreach($ur_marinado_prev_year2 as $row){
			$data['ur_marinado_prev_year2_jan'] = $row->jan_price;
			$data['ur_marinado_prev_year2_feb'] = $row->feb_price;
			$data['ur_marinado_prev_year2_mar'] = $row->mar_price;
			$data['ur_marinado_prev_year2_apr'] = $row->apr_price;
			$data['ur_marinado_prev_year2_may'] = $row->may_price;
			$data['ur_marinado_prev_year2_jun'] = $row->jun_price;
			$data['ur_marinado_prev_year2_jul'] = $row->jul_price;
			$data['ur_marinado_prev_year2_aug'] = $row->aug_price;
			$data['ur_marinado_prev_year2_sep'] = $row->sep_price;
			$data['ur_marinado_prev_year2_oct'] = $row->oct_price;
			$data['ur_marinado_prev_year2_nov'] = $row->nov_price;
			$data['ur_marinado_prev_year2_dec'] = $row->dec_price;
			$data['ur_marinado_prev_year2_avg'] = ($row->jan_price + $row->feb_price + $row->mar_price + $row->apr_price + $row->may_price + $row->jun_price + $row->jul_price + $row->aug_price + $row->sep_price + $row->oct_price + $row->nov_price + $row->dec_price) / 12;

			$data['ur_marinado_prev_year2_min'] = min($row->jan_price, $row->feb_price, $row->mar_price, $row->apr_price, $row->may_price, $row->jun_price, $row->jul_price, $row->aug_price, $row->sep_price, $row->oct_price, $row->nov_price, $row->dec_price);

			$data['ur_marinado_prev_year2_max'] = max($row->jan_price, $row->feb_price, $row->mar_price, $row->apr_price, $row->may_price, $row->jun_price, $row->jul_price, $row->aug_price, $row->sep_price, $row->oct_price, $row->nov_price, $row->dec_price);
		}


		$ur_marinado_prev_year1 = $this->admin->get_data('comparative_price_tbl', array('comp_price_product' => 'MARINADO FRIED', 'comp_price_segment' => 'UR', 'comp_price_year' => $year - 1,	'bc_id'	=>	$bc_id), 
			FALSE, 'AVG(comp_price_jan) as jan_price, AVG(comp_price_feb) as feb_price, AVG(comp_price_mar) as mar_price, AVG(comp_price_apr) as apr_price, AVG(comp_price_may) as may_price, AVG(comp_price_jun) as jun_price, AVG(comp_price_jul) as jul_price, AVG(comp_price_aug) as aug_price, AVG(comp_price_sep) as sep_price, AVG(comp_price_oct) as oct_price, AVG(comp_price_nov) as nov_price, AVG(comp_price_dec) as dec_price');
		
		$data['ur_marinado_prev_year1_jan'] = 0;
		$data['ur_marinado_prev_year1_feb'] = 0;
		$data['ur_marinado_prev_year1_mar'] = 0;
		$data['ur_marinado_prev_year1_apr'] = 0;
		$data['ur_marinado_prev_year1_may'] = 0;
		$data['ur_marinado_prev_year1_jun'] = 0;
		$data['ur_marinado_prev_year1_jul'] = 0;
		$data['ur_marinado_prev_year1_aug'] = 0;
		$data['ur_marinado_prev_year1_sep'] = 0;
		$data['ur_marinado_prev_year1_oct'] = 0;
		$data['ur_marinado_prev_year1_nov'] = 0;
		$data['ur_marinado_prev_year1_dec'] = 0;
		$data['ur_marinado_prev_year1_avg'] = 0;
		$data['ur_marinado_prev_year1_min'] = 0;
		$data['ur_marinado_prev_year1_max'] = 0;

		foreach($ur_marinado_prev_year1 as $row){
			$data['ur_marinado_prev_year1_jan'] = $row->jan_price;
			$data['ur_marinado_prev_year1_feb'] = $row->feb_price;
			$data['ur_marinado_prev_year1_mar'] = $row->mar_price;
			$data['ur_marinado_prev_year1_apr'] = $row->apr_price;
			$data['ur_marinado_prev_year1_may'] = $row->may_price;
			$data['ur_marinado_prev_year1_jun'] = $row->jun_price;
			$data['ur_marinado_prev_year1_jul'] = $row->jul_price;
			$data['ur_marinado_prev_year1_aug'] = $row->aug_price;
			$data['ur_marinado_prev_year1_sep'] = $row->sep_price;
			$data['ur_marinado_prev_year1_oct'] = $row->oct_price;
			$data['ur_marinado_prev_year1_nov'] = $row->nov_price;
			$data['ur_marinado_prev_year1_dec'] = $row->dec_price;
			$data['ur_marinado_prev_year1_avg'] = ($row->jan_price + $row->feb_price + $row->mar_price + $row->apr_price + $row->may_price + $row->jun_price + $row->jul_price + $row->aug_price + $row->sep_price + $row->oct_price + $row->nov_price + $row->dec_price) / 12;

			$data['ur_marinado_prev_year1_min'] = min($row->jan_price, $row->feb_price, $row->mar_price, $row->apr_price, $row->may_price, $row->jun_price, $row->jul_price, $row->aug_price, $row->sep_price, $row->oct_price, $row->nov_price, $row->dec_price);

			$data['ur_marinado_prev_year1_max'] = max($row->jan_price, $row->feb_price, $row->mar_price, $row->apr_price, $row->may_price, $row->jun_price, $row->jul_price, $row->aug_price, $row->sep_price, $row->oct_price, $row->nov_price, $row->dec_price);
		}

		$join_ur_spicy = array(
			'sales_item_tbl b' => 'a.sales_id = b.sales_id AND a.sales_status = 1 AND b.sales_item_status = 1 AND a.sales_year = ' . $year,
			'sales_details_tbl c' => 'b.sales_item_id = c.sales_item_id AND c.sales_det_status = 1',
			'material_tbl d' => 'b.material_id = d.material_id AND (d.material_code = 10200090 OR d.material_code = 1000401 OR d.material_code = 1000402 OR d.material_code = 1000617)',
			'material_group_tbl e' => 'd.material_group_id = e.material_group_id AND e.material_group_name = \'MARINATED CUT UPS\'',
			'outlet_tbl f' => 'a.outlet_id = f.outlet_id AND f.bc_id = ' . $bc_id,
			'outlet_brand_tbl g' => 'f.outlet_id = g.outlet_id AND g.outlet_brand_status = 1',
			'brand_tbl h' => 'g.brand_id  = h.brand_id AND h.brand_name = \'ULING ROASTER\''
		);

		$get_ur_spicy = $this->admin->get_join('sales_tbl a', $join_ur_spicy, FALSE, FALSE, 'YEAR(c.sales_det_date), MONTH(c.sales_det_date)', 'AVG(c.sales_det_asp) as asp, MONTH(c.sales_det_date) asp_date');

		$data['ur_spicy1'] = 0;
		$data['ur_spicy2'] = 0;
		$data['ur_spicy3'] = 0;
		$data['ur_spicy4'] = 0;
		$data['ur_spicy5'] = 0;
		$data['ur_spicy6'] = 0;
		$data['ur_spicy7'] = 0;
		$data['ur_spicy8'] = 0;
		$data['ur_spicy9'] = 0;
		$data['ur_spicy10'] = 0;
		$data['ur_spicy11'] = 0;
		$data['ur_spicy12'] = 0;
		$data['ur_spicy_total'] = 0;
		$data['ur_spicy_count'] = 0;
		$data['ur_spicy_avg'] = 0;
		$data['ur_spicy_min'] = 0;
		$data['ur_spicy_max'] = 0;

		foreach($get_ur_spicy as $row){
			$asp = $row->asp;
			$month = $row->asp_date;
			$data['ur_spicy' . $month] = $asp;
			$data['ur_spicy_total'] += $asp;

			if($asp < $data['ur_spicy_min'] || $data['ur_spicy_count'] == 0){
				$data['ur_spicy_min'] = $asp;
			}

			if($asp > $data['ur_spicy_max'] || $data['ur_spicy_count'] == 0){
				$data['ur_spicy_max'] = $asp;
			}

			$data['ur_spicy_count']++;
		}

		$data['ur_spicy_avg'] = $data['ur_spicy_total'] != 0 ? $data['ur_spicy_total'] / $data['ur_spicy_count'] : 0;

		$ur_spicy_prev_year2 = $this->admin->get_data('comparative_price_tbl', array('comp_price_product' => 'SPICY NECK', 'comp_price_segment' => 'UR', 'comp_price_year' => $year - 2,	'bc_id'	=>	$bc_id), 
			FALSE, 'AVG(comp_price_jan) as jan_price, AVG(comp_price_feb) as feb_price, AVG(comp_price_mar) as mar_price, AVG(comp_price_apr) as apr_price, AVG(comp_price_may) as may_price, AVG(comp_price_jun) as jun_price, AVG(comp_price_jul) as jul_price, AVG(comp_price_aug) as aug_price, AVG(comp_price_sep) as sep_price, AVG(comp_price_oct) as oct_price, AVG(comp_price_nov) as nov_price, AVG(comp_price_dec) as dec_price');
		
		$data['ur_spicy_prev_year2_jan'] = 0;
		$data['ur_spicy_prev_year2_feb'] = 0;
		$data['ur_spicy_prev_year2_mar'] = 0;
		$data['ur_spicy_prev_year2_apr'] = 0;
		$data['ur_spicy_prev_year2_may'] = 0;
		$data['ur_spicy_prev_year2_jun'] = 0;
		$data['ur_spicy_prev_year2_jul'] = 0;
		$data['ur_spicy_prev_year2_aug'] = 0;
		$data['ur_spicy_prev_year2_sep'] = 0;
		$data['ur_spicy_prev_year2_oct'] = 0;
		$data['ur_spicy_prev_year2_nov'] = 0;
		$data['ur_spicy_prev_year2_dec'] = 0;
		$data['ur_spicy_prev_year2_avg'] = 0;
		$data['ur_spicy_prev_year2_min'] = 0;
		$data['ur_spicy_prev_year2_max'] = 0;

		foreach($ur_spicy_prev_year2 as $row){
			$data['ur_spicy_prev_year2_jan'] = $row->jan_price;
			$data['ur_spicy_prev_year2_feb'] = $row->feb_price;
			$data['ur_spicy_prev_year2_mar'] = $row->mar_price;
			$data['ur_spicy_prev_year2_apr'] = $row->apr_price;
			$data['ur_spicy_prev_year2_may'] = $row->may_price;
			$data['ur_spicy_prev_year2_jun'] = $row->jun_price;
			$data['ur_spicy_prev_year2_jul'] = $row->jul_price;
			$data['ur_spicy_prev_year2_aug'] = $row->aug_price;
			$data['ur_spicy_prev_year2_sep'] = $row->sep_price;
			$data['ur_spicy_prev_year2_oct'] = $row->oct_price;
			$data['ur_spicy_prev_year2_nov'] = $row->nov_price;
			$data['ur_spicy_prev_year2_dec'] = $row->dec_price;
			$data['ur_spicy_prev_year2_avg'] = ($row->jan_price + $row->feb_price + $row->mar_price + $row->apr_price + $row->may_price + $row->jun_price + $row->jul_price + $row->aug_price + $row->sep_price + $row->oct_price + $row->nov_price + $row->dec_price) / 12;

			$data['ur_spicy_prev_year2_min'] = min($row->jan_price, $row->feb_price, $row->mar_price, $row->apr_price, $row->may_price, $row->jun_price, $row->jul_price, $row->aug_price, $row->sep_price, $row->oct_price, $row->nov_price, $row->dec_price);

			$data['ur_spicy_prev_year2_max'] = max($row->jan_price, $row->feb_price, $row->mar_price, $row->apr_price, $row->may_price, $row->jun_price, $row->jul_price, $row->aug_price, $row->sep_price, $row->oct_price, $row->nov_price, $row->dec_price);
		}


		$ur_spicy_prev_year1 = $this->admin->get_data('comparative_price_tbl', array('comp_price_product' => 'SPICY NECK', 'comp_price_segment' => 'UR', 'comp_price_year' => $year - 1,	'bc_id'	=>	$bc_id), 
			FALSE, 'AVG(comp_price_jan) as jan_price, AVG(comp_price_feb) as feb_price, AVG(comp_price_mar) as mar_price, AVG(comp_price_apr) as apr_price, AVG(comp_price_may) as may_price, AVG(comp_price_jun) as jun_price, AVG(comp_price_jul) as jul_price, AVG(comp_price_aug) as aug_price, AVG(comp_price_sep) as sep_price, AVG(comp_price_oct) as oct_price, AVG(comp_price_nov) as nov_price, AVG(comp_price_dec) as dec_price');
		
		$data['ur_spicy_prev_year1_jan'] = 0;
		$data['ur_spicy_prev_year1_feb'] = 0;
		$data['ur_spicy_prev_year1_mar'] = 0;
		$data['ur_spicy_prev_year1_apr'] = 0;
		$data['ur_spicy_prev_year1_may'] = 0;
		$data['ur_spicy_prev_year1_jun'] = 0;
		$data['ur_spicy_prev_year1_jul'] = 0;
		$data['ur_spicy_prev_year1_aug'] = 0;
		$data['ur_spicy_prev_year1_sep'] = 0;
		$data['ur_spicy_prev_year1_oct'] = 0;
		$data['ur_spicy_prev_year1_nov'] = 0;
		$data['ur_spicy_prev_year1_dec'] = 0;
		$data['ur_spicy_prev_year1_avg'] = 0;
		$data['ur_spicy_prev_year1_min'] = 0;
		$data['ur_spicy_prev_year1_max'] = 0;

		foreach($ur_spicy_prev_year1 as $row){
			$data['ur_spicy_prev_year1_jan'] = $row->jan_price;
			$data['ur_spicy_prev_year1_feb'] = $row->feb_price;
			$data['ur_spicy_prev_year1_mar'] = $row->mar_price;
			$data['ur_spicy_prev_year1_apr'] = $row->apr_price;
			$data['ur_spicy_prev_year1_may'] = $row->may_price;
			$data['ur_spicy_prev_year1_jun'] = $row->jun_price;
			$data['ur_spicy_prev_year1_jul'] = $row->jul_price;
			$data['ur_spicy_prev_year1_aug'] = $row->aug_price;
			$data['ur_spicy_prev_year1_sep'] = $row->sep_price;
			$data['ur_spicy_prev_year1_oct'] = $row->oct_price;
			$data['ur_spicy_prev_year1_nov'] = $row->nov_price;
			$data['ur_spicy_prev_year1_dec'] = $row->dec_price;
			$data['ur_spicy_prev_year1_avg'] = ($row->jan_price + $row->feb_price + $row->mar_price + $row->apr_price + $row->may_price + $row->jun_price + $row->jul_price + $row->aug_price + $row->sep_price + $row->oct_price + $row->nov_price + $row->dec_price) / 12;

			$data['ur_spicy_prev_year1_min'] = min($row->jan_price, $row->feb_price, $row->mar_price, $row->apr_price, $row->may_price, $row->jun_price, $row->jul_price, $row->aug_price, $row->sep_price, $row->oct_price, $row->nov_price, $row->dec_price);

			$data['ur_spicy_prev_year1_max'] = max($row->jan_price, $row->feb_price, $row->mar_price, $row->apr_price, $row->may_price, $row->jun_price, $row->jul_price, $row->aug_price, $row->sep_price, $row->oct_price, $row->nov_price, $row->dec_price);
		}

		/*Barbeque*/
		$join_ur_barbeque = array(
			'sales_item_tbl b' => 'a.sales_id = b.sales_id AND a.sales_status = 1 AND b.sales_item_status = 1 AND a.sales_year = ' . $year,
			'sales_details_tbl c' => 'b.sales_item_id = c.sales_item_id AND c.sales_det_status = 1',
			'material_tbl d' => 'b.material_id = d.material_id AND d.material_code = 1000548',
			'material_group_tbl e' => 'd.material_group_id = e.material_group_id AND e.material_group_name = \'OTHER SPECIALTY PRODUCTS\'',
			'outlet_tbl f' => 'a.outlet_id = f.outlet_id AND f.bc_id = ' . $bc_id,
			'outlet_brand_tbl g' => 'f.outlet_id = g.outlet_id AND g.outlet_brand_status = 1',
			'brand_tbl h' => 'g.brand_id  = h.brand_id AND h.brand_name = \'ULING ROASTER\''
		);

		$get_ur_barbeque = $this->admin->get_join('sales_tbl a', $join_ur_barbeque, FALSE, FALSE, 'YEAR(c.sales_det_date), MONTH(c.sales_det_date)', 'AVG(c.sales_det_asp) as asp, MONTH(c.sales_det_date) asp_date');

		$data['ur_barbeque1'] = 0;
		$data['ur_barbeque2'] = 0;
		$data['ur_barbeque3'] = 0;
		$data['ur_barbeque4'] = 0;
		$data['ur_barbeque5'] = 0;
		$data['ur_barbeque6'] = 0;
		$data['ur_barbeque7'] = 0;
		$data['ur_barbeque8'] = 0;
		$data['ur_barbeque9'] = 0;
		$data['ur_barbeque10'] = 0;
		$data['ur_barbeque11'] = 0;
		$data['ur_barbeque12'] = 0;
		$data['ur_barbeque_total'] = 0;
		$data['ur_barbeque_count'] = 0;
		$data['ur_barbeque_avg'] = 0;
		$data['ur_barbeque_min'] = 0;
		$data['ur_barbeque_max'] = 0;

		foreach($get_ur_barbeque as $row){
			$asp = $row->asp;
			$month = $row->asp_date;
			$data['ur_barbeque' . $month] = $asp;
			$data['ur_barbeque_total'] += $asp;

			if($asp < $data['ur_barbeque_min'] || $data['ur_barbeque_count'] == 0){
				$data['ur_barbeque_min'] = $asp;
			}

			if($asp > $data['ur_barbeque_max'] || $data['ur_barbeque_count'] == 0){
				$data['ur_barbeque_max'] = $asp;
			}

			$data['ur_barbeque_count']++;
		}

		$data['ur_barbeque_avg'] = $data['ur_barbeque_total'] != 0 ? $data['ur_barbeque_total'] / $data['ur_barbeque_count'] : 0;


		$ur_barbeque_prev_year2 = $this->admin->get_data('comparative_price_tbl', array('comp_price_product' => 'BBQ', 'comp_price_segment' => 'UR', 'comp_price_year' => $year - 2,	'bc_id'	=>	$bc_id), 
			FALSE, 'AVG(comp_price_jan) as jan_price, AVG(comp_price_feb) as feb_price, AVG(comp_price_mar) as mar_price, AVG(comp_price_apr) as apr_price, AVG(comp_price_may) as may_price, AVG(comp_price_jun) as jun_price, AVG(comp_price_jul) as jul_price, AVG(comp_price_aug) as aug_price, AVG(comp_price_sep) as sep_price, AVG(comp_price_oct) as oct_price, AVG(comp_price_nov) as nov_price, AVG(comp_price_dec) as dec_price');
		
		$data['ur_barbeque_prev_year2_jan'] = 0;
		$data['ur_barbeque_prev_year2_feb'] = 0;
		$data['ur_barbeque_prev_year2_mar'] = 0;
		$data['ur_barbeque_prev_year2_apr'] = 0;
		$data['ur_barbeque_prev_year2_may'] = 0;
		$data['ur_barbeque_prev_year2_jun'] = 0;
		$data['ur_barbeque_prev_year2_jul'] = 0;
		$data['ur_barbeque_prev_year2_aug'] = 0;
		$data['ur_barbeque_prev_year2_sep'] = 0;
		$data['ur_barbeque_prev_year2_oct'] = 0;
		$data['ur_barbeque_prev_year2_nov'] = 0;
		$data['ur_barbeque_prev_year2_dec'] = 0;
		$data['ur_barbeque_prev_year2_avg'] = 0;
		$data['ur_barbeque_prev_year2_min'] = 0;
		$data['ur_barbeque_prev_year2_max'] = 0;

		foreach($ur_barbeque_prev_year2 as $row){
			$data['ur_barbeque_prev_year2_jan'] = $row->jan_price;
			$data['ur_barbeque_prev_year2_feb'] = $row->feb_price;
			$data['ur_barbeque_prev_year2_mar'] = $row->mar_price;
			$data['ur_barbeque_prev_year2_apr'] = $row->apr_price;
			$data['ur_barbeque_prev_year2_may'] = $row->may_price;
			$data['ur_barbeque_prev_year2_jun'] = $row->jun_price;
			$data['ur_barbeque_prev_year2_jul'] = $row->jul_price;
			$data['ur_barbeque_prev_year2_aug'] = $row->aug_price;
			$data['ur_barbeque_prev_year2_sep'] = $row->sep_price;
			$data['ur_barbeque_prev_year2_oct'] = $row->oct_price;
			$data['ur_barbeque_prev_year2_nov'] = $row->nov_price;
			$data['ur_barbeque_prev_year2_dec'] = $row->dec_price;
			$data['ur_barbeque_prev_year2_avg'] = ($row->jan_price + $row->feb_price + $row->mar_price + $row->apr_price + $row->may_price + $row->jun_price + $row->jul_price + $row->aug_price + $row->sep_price + $row->oct_price + $row->nov_price + $row->dec_price) / 12;

			$data['ur_barbeque_prev_year2_min'] = min($row->jan_price, $row->feb_price, $row->mar_price, $row->apr_price, $row->may_price, $row->jun_price, $row->jul_price, $row->aug_price, $row->sep_price, $row->oct_price, $row->nov_price, $row->dec_price);

			$data['ur_barbeque_prev_year2_max'] = max($row->jan_price, $row->feb_price, $row->mar_price, $row->apr_price, $row->may_price, $row->jun_price, $row->jul_price, $row->aug_price, $row->sep_price, $row->oct_price, $row->nov_price, $row->dec_price);
		}


		$ur_barbeque_prev_year1 = $this->admin->get_data('comparative_price_tbl', array('comp_price_product' => 'BBQ', 'comp_price_segment' => 'UR', 'comp_price_year' => $year - 1,	'bc_id'	=>	$bc_id), 
			FALSE, 'AVG(comp_price_jan) as jan_price, AVG(comp_price_feb) as feb_price, AVG(comp_price_mar) as mar_price, AVG(comp_price_apr) as apr_price, AVG(comp_price_may) as may_price, AVG(comp_price_jun) as jun_price, AVG(comp_price_jul) as jul_price, AVG(comp_price_aug) as aug_price, AVG(comp_price_sep) as sep_price, AVG(comp_price_oct) as oct_price, AVG(comp_price_nov) as nov_price, AVG(comp_price_dec) as dec_price');
		
		$data['ur_barbeque_prev_year1_jan'] = 0;
		$data['ur_barbeque_prev_year1_feb'] = 0;
		$data['ur_barbeque_prev_year1_mar'] = 0;
		$data['ur_barbeque_prev_year1_apr'] = 0;
		$data['ur_barbeque_prev_year1_may'] = 0;
		$data['ur_barbeque_prev_year1_jun'] = 0;
		$data['ur_barbeque_prev_year1_jul'] = 0;
		$data['ur_barbeque_prev_year1_aug'] = 0;
		$data['ur_barbeque_prev_year1_sep'] = 0;
		$data['ur_barbeque_prev_year1_oct'] = 0;
		$data['ur_barbeque_prev_year1_nov'] = 0;
		$data['ur_barbeque_prev_year1_dec'] = 0;
		$data['ur_barbeque_prev_year1_avg'] = 0;
		$data['ur_barbeque_prev_year1_min'] = 0;
		$data['ur_barbeque_prev_year1_max'] = 0;

		foreach($ur_barbeque_prev_year1 as $row){
			$data['ur_barbeque_prev_year1_jan'] = $row->jan_price;
			$data['ur_barbeque_prev_year1_feb'] = $row->feb_price;
			$data['ur_barbeque_prev_year1_mar'] = $row->mar_price;
			$data['ur_barbeque_prev_year1_apr'] = $row->apr_price;
			$data['ur_barbeque_prev_year1_may'] = $row->may_price;
			$data['ur_barbeque_prev_year1_jun'] = $row->jun_price;
			$data['ur_barbeque_prev_year1_jul'] = $row->jul_price;
			$data['ur_barbeque_prev_year1_aug'] = $row->aug_price;
			$data['ur_barbeque_prev_year1_sep'] = $row->sep_price;
			$data['ur_barbeque_prev_year1_oct'] = $row->oct_price;
			$data['ur_barbeque_prev_year1_nov'] = $row->nov_price;
			$data['ur_barbeque_prev_year1_dec'] = $row->dec_price;
			$data['ur_barbeque_prev_year1_avg'] = ($row->jan_price + $row->feb_price + $row->mar_price + $row->apr_price + $row->may_price + $row->jun_price + $row->jul_price + $row->aug_price + $row->sep_price + $row->oct_price + $row->nov_price + $row->dec_price) / 12;

			$data['ur_barbeque_prev_year1_min'] = min($row->jan_price, $row->feb_price, $row->mar_price, $row->apr_price, $row->may_price, $row->jun_price, $row->jul_price, $row->aug_price, $row->sep_price, $row->oct_price, $row->nov_price, $row->dec_price);

			$data['ur_barbeque_prev_year1_max'] = max($row->jan_price, $row->feb_price, $row->mar_price, $row->apr_price, $row->may_price, $row->jun_price, $row->jul_price, $row->aug_price, $row->sep_price, $row->oct_price, $row->nov_price, $row->dec_price);
		}


		/*Nuggets*/
		$join_ur_nuggets = array(
			'sales_item_tbl b' => 'a.sales_id = b.sales_id AND a.sales_status = 1 AND b.sales_item_status = 1 AND a.sales_year = ' . $year,
			'sales_details_tbl c' => 'b.sales_item_id = c.sales_item_id AND c.sales_det_status = 1',
			'material_tbl d' => 'b.material_id = d.material_id AND (d.material_code = 40000016 OR d.material_code = 40000019)',
			'material_group_tbl e' => 'd.material_group_id = e.material_group_id AND e.material_group_name = \'OTHER VAP PRODUCTS\'',
			'outlet_tbl f' => 'a.outlet_id = f.outlet_id AND f.bc_id = ' . $bc_id,
			'outlet_brand_tbl g' => 'f.outlet_id = g.outlet_id AND g.outlet_brand_status = 1',
			'brand_tbl h' => 'g.brand_id  = h.brand_id AND h.brand_name = \'ULING ROASTERS\''
		);

		$get_ur_nuggets = $this->admin->get_join('sales_tbl a', $join_ur_nuggets, FALSE, FALSE, 'YEAR(c.sales_det_date), MONTH(c.sales_det_date)', 'AVG(c.sales_det_asp) as asp, MONTH(c.sales_det_date) asp_date');

		$data['ur_nuggets1'] = 0;
		$data['ur_nuggets2'] = 0;
		$data['ur_nuggets3'] = 0;
		$data['ur_nuggets4'] = 0;
		$data['ur_nuggets5'] = 0;
		$data['ur_nuggets6'] = 0;
		$data['ur_nuggets7'] = 0;
		$data['ur_nuggets8'] = 0;
		$data['ur_nuggets9'] = 0;
		$data['ur_nuggets10'] = 0;
		$data['ur_nuggets11'] = 0;
		$data['ur_nuggets12'] = 0;
		$data['ur_nuggets_total'] = 0;
		$data['ur_nuggets_count'] = 0;
		$data['ur_nuggets_avg'] = 0;
		$data['ur_nuggets_min'] = 0;
		$data['ur_nuggets_max'] = 0;

		foreach($get_ur_nuggets as $row){
			$asp = $row->asp;
			$month = $row->asp_date;
			$data['ur_nuggets' . $month] = $asp;
			$data['ur_nuggets_total'] += $asp;

			if($asp < $data['ur_nuggets_min'] || $data['ur_nuggets_count'] == 0){
				$data['ur_nuggets_min'] = $asp;
			}

			if($asp > $data['ur_nuggets_max'] || $data['ur_nuggets_count'] == 0){
				$data['ur_nuggets_max'] = $asp;
			}

			$data['ur_nuggets_count']++;
		}

		$data['ur_nuggets_avg'] = $data['ur_nuggets_total'] != 0 ? $data['ur_nuggets_total'] / $data['ur_nuggets_count'] : 0;


		$ur_nuggets_prev_year2 = $this->admin->get_data('comparative_price_tbl', array('comp_price_product' => 'VAP-NUGGETS', 'comp_price_segment' => 'UR', 'comp_price_year' => $year - 2,	'bc_id'	=>	$bc_id), 
			FALSE, 'AVG(comp_price_jan) as jan_price, AVG(comp_price_feb) as feb_price, AVG(comp_price_mar) as mar_price, AVG(comp_price_apr) as apr_price, AVG(comp_price_may) as may_price, AVG(comp_price_jun) as jun_price, AVG(comp_price_jul) as jul_price, AVG(comp_price_aug) as aug_price, AVG(comp_price_sep) as sep_price, AVG(comp_price_oct) as oct_price, AVG(comp_price_nov) as nov_price, AVG(comp_price_dec) as dec_price');
		
		$data['ur_nuggets_prev_year2_jan'] = 0;
		$data['ur_nuggets_prev_year2_feb'] = 0;
		$data['ur_nuggets_prev_year2_mar'] = 0;
		$data['ur_nuggets_prev_year2_apr'] = 0;
		$data['ur_nuggets_prev_year2_may'] = 0;
		$data['ur_nuggets_prev_year2_jun'] = 0;
		$data['ur_nuggets_prev_year2_jul'] = 0;
		$data['ur_nuggets_prev_year2_aug'] = 0;
		$data['ur_nuggets_prev_year2_sep'] = 0;
		$data['ur_nuggets_prev_year2_oct'] = 0;
		$data['ur_nuggets_prev_year2_nov'] = 0;
		$data['ur_nuggets_prev_year2_dec'] = 0;
		$data['ur_nuggets_prev_year2_avg'] = 0;
		$data['ur_nuggets_prev_year2_min'] = 0;
		$data['ur_nuggets_prev_year2_max'] = 0;

		foreach($ur_nuggets_prev_year2 as $row){
			$data['ur_nuggets_prev_year2_jan'] = $row->jan_price;
			$data['ur_nuggets_prev_year2_feb'] = $row->feb_price;
			$data['ur_nuggets_prev_year2_mar'] = $row->mar_price;
			$data['ur_nuggets_prev_year2_apr'] = $row->apr_price;
			$data['ur_nuggets_prev_year2_may'] = $row->may_price;
			$data['ur_nuggets_prev_year2_jun'] = $row->jun_price;
			$data['ur_nuggets_prev_year2_jul'] = $row->jul_price;
			$data['ur_nuggets_prev_year2_aug'] = $row->aug_price;
			$data['ur_nuggets_prev_year2_sep'] = $row->sep_price;
			$data['ur_nuggets_prev_year2_oct'] = $row->oct_price;
			$data['ur_nuggets_prev_year2_nov'] = $row->nov_price;
			$data['ur_nuggets_prev_year2_dec'] = $row->dec_price;
			$data['ur_nuggets_prev_year2_avg'] = ($row->jan_price + $row->feb_price + $row->mar_price + $row->apr_price + $row->may_price + $row->jun_price + $row->jul_price + $row->aug_price + $row->sep_price + $row->oct_price + $row->nov_price + $row->dec_price) / 12;

			$data['ur_nuggets_prev_year2_min'] = min($row->jan_price, $row->feb_price, $row->mar_price, $row->apr_price, $row->may_price, $row->jun_price, $row->jul_price, $row->aug_price, $row->sep_price, $row->oct_price, $row->nov_price, $row->dec_price);

			$data['ur_nuggets_prev_year2_max'] = max($row->jan_price, $row->feb_price, $row->mar_price, $row->apr_price, $row->may_price, $row->jun_price, $row->jul_price, $row->aug_price, $row->sep_price, $row->oct_price, $row->nov_price, $row->dec_price);
		}


		$ur_nuggets_prev_year1 = $this->admin->get_data('comparative_price_tbl', array('comp_price_product' => 'VAP-NUGGETS', 'comp_price_segment' => 'UR', 'comp_price_year' => $year - 1,	'bc_id'	=>	$bc_id), 
			FALSE, 'AVG(comp_price_jan) as jan_price, AVG(comp_price_feb) as feb_price, AVG(comp_price_mar) as mar_price, AVG(comp_price_apr) as apr_price, AVG(comp_price_may) as may_price, AVG(comp_price_jun) as jun_price, AVG(comp_price_jul) as jul_price, AVG(comp_price_aug) as aug_price, AVG(comp_price_sep) as sep_price, AVG(comp_price_oct) as oct_price, AVG(comp_price_nov) as nov_price, AVG(comp_price_dec) as dec_price');
		
		$data['ur_nuggets_prev_year1_jan'] = 0;
		$data['ur_nuggets_prev_year1_feb'] = 0;
		$data['ur_nuggets_prev_year1_mar'] = 0;
		$data['ur_nuggets_prev_year1_apr'] = 0;
		$data['ur_nuggets_prev_year1_may'] = 0;
		$data['ur_nuggets_prev_year1_jun'] = 0;
		$data['ur_nuggets_prev_year1_jul'] = 0;
		$data['ur_nuggets_prev_year1_aug'] = 0;
		$data['ur_nuggets_prev_year1_sep'] = 0;
		$data['ur_nuggets_prev_year1_oct'] = 0;
		$data['ur_nuggets_prev_year1_nov'] = 0;
		$data['ur_nuggets_prev_year1_dec'] = 0;
		$data['ur_nuggets_prev_year1_avg'] = 0;
		$data['ur_nuggets_prev_year1_min'] = 0;
		$data['ur_nuggets_prev_year1_max'] = 0;

		foreach($ur_nuggets_prev_year1 as $row){
			$data['ur_nuggets_prev_year1_jan'] = $row->jan_price;
			$data['ur_nuggets_prev_year1_feb'] = $row->feb_price;
			$data['ur_nuggets_prev_year1_mar'] = $row->mar_price;
			$data['ur_nuggets_prev_year1_apr'] = $row->apr_price;
			$data['ur_nuggets_prev_year1_may'] = $row->may_price;
			$data['ur_nuggets_prev_year1_jun'] = $row->jun_price;
			$data['ur_nuggets_prev_year1_jul'] = $row->jul_price;
			$data['ur_nuggets_prev_year1_aug'] = $row->aug_price;
			$data['ur_nuggets_prev_year1_sep'] = $row->sep_price;
			$data['ur_nuggets_prev_year1_oct'] = $row->oct_price;
			$data['ur_nuggets_prev_year1_nov'] = $row->nov_price;
			$data['ur_nuggets_prev_year1_dec'] = $row->dec_price;
			$data['ur_nuggets_prev_year1_avg'] = ($row->jan_price + $row->feb_price + $row->mar_price + $row->apr_price + $row->may_price + $row->jun_price + $row->jul_price + $row->aug_price + $row->sep_price + $row->oct_price + $row->nov_price + $row->dec_price) / 12;

			$data['ur_nuggets_prev_year1_min'] = min($row->jan_price, $row->feb_price, $row->mar_price, $row->apr_price, $row->may_price, $row->jun_price, $row->jul_price, $row->aug_price, $row->sep_price, $row->oct_price, $row->nov_price, $row->dec_price);

			$data['ur_nuggets_prev_year1_max'] = max($row->jan_price, $row->feb_price, $row->mar_price, $row->apr_price, $row->may_price, $row->jun_price, $row->jul_price, $row->aug_price, $row->sep_price, $row->oct_price, $row->nov_price, $row->dec_price);
		}

		/*UR 11 PC PICA PICA CUTS*/

		$join_ur_pica = array(
			'sales_item_tbl b' => 'a.sales_id = b.sales_id AND a.sales_status = 1 AND b.sales_item_status = 1 AND a.sales_year = ' . $year,
			'sales_details_tbl c' => 'b.sales_item_id = c.sales_item_id AND c.sales_det_status = 1',
			'material_tbl d' => 'b.material_id = d.material_id AND d.material_code = 10400170',
			'material_group_tbl e' => 'd.material_group_id = e.material_group_id',
			'outlet_tbl f' => 'a.outlet_id = f.outlet_id AND f.bc_id = ' . $bc_id,
			'outlet_brand_tbl g' => 'f.outlet_id = g.outlet_id AND g.outlet_brand_status = 1',
			'brand_tbl h' => 'g.brand_id  = h.brand_id AND h.brand_name = \'ULING ROASTER\''
		);

		$get_ur_pica = $this->admin->get_join('sales_tbl a', $join_ur_pica, FALSE, FALSE, 'YEAR(c.sales_det_date), MONTH(c.sales_det_date)', 'AVG(c.sales_det_asp) as asp, MONTH(c.sales_det_date) asp_date');

		$data['ur_pica1'] = 0;
		$data['ur_pica2'] = 0;
		$data['ur_pica3'] = 0;
		$data['ur_pica4'] = 0;
		$data['ur_pica5'] = 0;
		$data['ur_pica6'] = 0;
		$data['ur_pica7'] = 0;
		$data['ur_pica8'] = 0;
		$data['ur_pica9'] = 0;
		$data['ur_pica10'] = 0;
		$data['ur_pica11'] = 0;
		$data['ur_pica12'] = 0;
		$data['ur_pica_total'] = 0;
		$data['ur_pica_count'] = 0;
		$data['ur_pica_avg'] = 0;
		$data['ur_pica_min'] = 0;
		$data['ur_pica_max'] = 0;

		foreach($get_ur_pica as $row){
			$asp = $row->asp;
			$month = $row->asp_date;
			$data['ur_pica' . $month] = $asp;
			$data['ur_pica_total'] += $asp;

			if($asp < $data['ur_pica_min'] || $data['ur_pica_count'] == 0){
				$data['ur_pica_min'] = $asp;
			}

			if($asp > $data['ur_pica_max'] || $data['ur_pica_count'] == 0){
				$data['ur_pica_max'] = $asp;
			}

			$data['ur_pica_count']++;
		}

		$data['ur_pica_avg'] = $data['ur_pica_total'] != 0 ? $data['ur_pica_total'] / $data['ur_pica_count'] : 0;


		$ur_pica_prev_year2 = $this->admin->get_data('comparative_price_tbl', array('comp_price_product' => '11 PC', 'comp_price_segment' => 'UR', 'comp_price_year' => $year - 2,	'bc_id'	=>	$bc_id), 
			FALSE, 'AVG(comp_price_jan) as jan_price, AVG(comp_price_feb) as feb_price, AVG(comp_price_mar) as mar_price, AVG(comp_price_apr) as apr_price, AVG(comp_price_may) as may_price, AVG(comp_price_jun) as jun_price, AVG(comp_price_jul) as jul_price, AVG(comp_price_aug) as aug_price, AVG(comp_price_sep) as sep_price, AVG(comp_price_oct) as oct_price, AVG(comp_price_nov) as nov_price, AVG(comp_price_dec) as dec_price');
		
		$data['ur_pica_prev_year2_jan'] = 0;
		$data['ur_pica_prev_year2_feb'] = 0;
		$data['ur_pica_prev_year2_mar'] = 0;
		$data['ur_pica_prev_year2_apr'] = 0;
		$data['ur_pica_prev_year2_may'] = 0;
		$data['ur_pica_prev_year2_jun'] = 0;
		$data['ur_pica_prev_year2_jul'] = 0;
		$data['ur_pica_prev_year2_aug'] = 0;
		$data['ur_pica_prev_year2_sep'] = 0;
		$data['ur_pica_prev_year2_oct'] = 0;
		$data['ur_pica_prev_year2_nov'] = 0;
		$data['ur_pica_prev_year2_dec'] = 0;
		$data['ur_pica_prev_year2_avg'] = 0;
		$data['ur_pica_prev_year2_min'] = 0;
		$data['ur_pica_prev_year2_max'] = 0;

		foreach($ur_pica_prev_year2 as $row){
			$data['ur_pica_prev_year2_jan'] = $row->jan_price;
			$data['ur_pica_prev_year2_feb'] = $row->feb_price;
			$data['ur_pica_prev_year2_mar'] = $row->mar_price;
			$data['ur_pica_prev_year2_apr'] = $row->apr_price;
			$data['ur_pica_prev_year2_may'] = $row->may_price;
			$data['ur_pica_prev_year2_jun'] = $row->jun_price;
			$data['ur_pica_prev_year2_jul'] = $row->jul_price;
			$data['ur_pica_prev_year2_aug'] = $row->aug_price;
			$data['ur_pica_prev_year2_sep'] = $row->sep_price;
			$data['ur_pica_prev_year2_oct'] = $row->oct_price;
			$data['ur_pica_prev_year2_nov'] = $row->nov_price;
			$data['ur_pica_prev_year2_dec'] = $row->dec_price;
			$data['ur_pica_prev_year2_avg'] = ($row->jan_price + $row->feb_price + $row->mar_price + $row->apr_price + $row->may_price + $row->jun_price + $row->jul_price + $row->aug_price + $row->sep_price + $row->oct_price + $row->nov_price + $row->dec_price) / 12;

			$data['ur_pica_prev_year2_min'] = min($row->jan_price, $row->feb_price, $row->mar_price, $row->apr_price, $row->may_price, $row->jun_price, $row->jul_price, $row->aug_price, $row->sep_price, $row->oct_price, $row->nov_price, $row->dec_price);

			$data['ur_pica_prev_year2_max'] = max($row->jan_price, $row->feb_price, $row->mar_price, $row->apr_price, $row->may_price, $row->jun_price, $row->jul_price, $row->aug_price, $row->sep_price, $row->oct_price, $row->nov_price, $row->dec_price);
		}


		$ur_pica_prev_year1 = $this->admin->get_data('comparative_price_tbl', array('comp_price_product' => '11 PC', 'comp_price_segment' => 'UR', 'comp_price_year' => $year - 1,	'bc_id'	=>	$bc_id), 
			FALSE, 'AVG(comp_price_jan) as jan_price, AVG(comp_price_feb) as feb_price, AVG(comp_price_mar) as mar_price, AVG(comp_price_apr) as apr_price, AVG(comp_price_may) as may_price, AVG(comp_price_jun) as jun_price, AVG(comp_price_jul) as jul_price, AVG(comp_price_aug) as aug_price, AVG(comp_price_sep) as sep_price, AVG(comp_price_oct) as oct_price, AVG(comp_price_nov) as nov_price, AVG(comp_price_dec) as dec_price');
		
		$data['ur_pica_prev_year1_jan'] = 0;
		$data['ur_pica_prev_year1_feb'] = 0;
		$data['ur_pica_prev_year1_mar'] = 0;
		$data['ur_pica_prev_year1_apr'] = 0;
		$data['ur_pica_prev_year1_may'] = 0;
		$data['ur_pica_prev_year1_jun'] = 0;
		$data['ur_pica_prev_year1_jul'] = 0;
		$data['ur_pica_prev_year1_aug'] = 0;
		$data['ur_pica_prev_year1_sep'] = 0;
		$data['ur_pica_prev_year1_oct'] = 0;
		$data['ur_pica_prev_year1_nov'] = 0;
		$data['ur_pica_prev_year1_dec'] = 0;
		$data['ur_pica_prev_year1_avg'] = 0;
		$data['ur_pica_prev_year1_min'] = 0;
		$data['ur_pica_prev_year1_max'] = 0;

		foreach($ur_pica_prev_year1 as $row){
			$data['ur_pica_prev_year1_jan'] = $row->jan_price;
			$data['ur_pica_prev_year1_feb'] = $row->feb_price;
			$data['ur_pica_prev_year1_mar'] = $row->mar_price;
			$data['ur_pica_prev_year1_apr'] = $row->apr_price;
			$data['ur_pica_prev_year1_may'] = $row->may_price;
			$data['ur_pica_prev_year1_jun'] = $row->jun_price;
			$data['ur_pica_prev_year1_jul'] = $row->jul_price;
			$data['ur_pica_prev_year1_aug'] = $row->aug_price;
			$data['ur_pica_prev_year1_sep'] = $row->sep_price;
			$data['ur_pica_prev_year1_oct'] = $row->oct_price;
			$data['ur_pica_prev_year1_nov'] = $row->nov_price;
			$data['ur_pica_prev_year1_dec'] = $row->dec_price;
			$data['ur_pica_prev_year1_avg'] = ($row->jan_price + $row->feb_price + $row->mar_price + $row->apr_price + $row->may_price + $row->jun_price + $row->jul_price + $row->aug_price + $row->sep_price + $row->oct_price + $row->nov_price + $row->dec_price) / 12;

			$data['ur_pica_prev_year1_min'] = min($row->jan_price, $row->feb_price, $row->mar_price, $row->apr_price, $row->may_price, $row->jun_price, $row->jul_price, $row->aug_price, $row->sep_price, $row->oct_price, $row->nov_price, $row->dec_price);

			$data['ur_pica_prev_year1_max'] = max($row->jan_price, $row->feb_price, $row->mar_price, $row->apr_price, $row->may_price, $row->jun_price, $row->jul_price, $row->aug_price, $row->sep_price, $row->oct_price, $row->nov_price, $row->dec_price);
		}

		/*1 PC BOSSING CUTS */
		
		$join_ur_bossing = array(
			'sales_item_tbl b' => 'a.sales_id = b.sales_id AND a.sales_status = 1 AND b.sales_item_status = 1 AND a.sales_year = ' . $year,
			'sales_details_tbl c' => 'b.sales_item_id = c.sales_item_id AND c.sales_det_status = 1',
			'material_tbl d' => 'b.material_id = d.material_id AND d.material_code = 10400184',
			'material_group_tbl e' => 'd.material_group_id = e.material_group_id',
			'outlet_tbl f' => 'a.outlet_id = f.outlet_id AND f.bc_id = ' . $bc_id,
			'outlet_brand_tbl g' => 'f.outlet_id = g.outlet_id AND g.outlet_brand_status = 1',
			'brand_tbl h' => 'g.brand_id  = h.brand_id AND h.brand_name = \'ULING ROASTER\''
		);

		$get_ur_bossing = $this->admin->get_join('sales_tbl a', $join_ur_bossing, FALSE, FALSE, 'YEAR(c.sales_det_date), MONTH(c.sales_det_date)', 'AVG(c.sales_det_asp) as asp, MONTH(c.sales_det_date) asp_date');

		$data['ur_bossing1'] = 0;
		$data['ur_bossing2'] = 0;
		$data['ur_bossing3'] = 0;
		$data['ur_bossing4'] = 0;
		$data['ur_bossing5'] = 0;
		$data['ur_bossing6'] = 0;
		$data['ur_bossing7'] = 0;
		$data['ur_bossing8'] = 0;
		$data['ur_bossing9'] = 0;
		$data['ur_bossing10'] = 0;
		$data['ur_bossing11'] = 0;
		$data['ur_bossing12'] = 0;
		$data['ur_bossing_total'] = 0;
		$data['ur_bossing_count'] = 0;
		$data['ur_bossing_avg'] = 0;
		$data['ur_bossing_min'] = 0;
		$data['ur_bossing_max'] = 0;

		foreach($get_ur_bossing as $row){
			$asp = $row->asp;
			$month = $row->asp_date;
			$data['ur_bossing' . $month] = $asp;
			$data['ur_bossing_total'] += $asp;

			if($asp < $data['ur_bossing_min'] || $data['ur_bossing_count'] == 0){
				$data['ur_bossing_min'] = $asp;
			}

			if($asp > $data['ur_bossing_max'] || $data['ur_bossing_count'] == 0){
				$data['ur_bossing_max'] = $asp;
			}

			$data['ur_bossing_count']++;
		}

		$data['ur_bossing_avg'] = $data['ur_bossing_total'] != 0 ? $data['ur_bossing_total'] / $data['ur_bossing_count'] : 0;


		$ur_bossing_prev_year2 = $this->admin->get_data('comparative_price_tbl', array('comp_price_product' => '1 PC', 'comp_price_segment' => 'UR', 'comp_price_year' => $year - 2,	'bc_id'	=>	$bc_id), 
			FALSE, 'AVG(comp_price_jan) as jan_price, AVG(comp_price_feb) as feb_price, AVG(comp_price_mar) as mar_price, AVG(comp_price_apr) as apr_price, AVG(comp_price_may) as may_price, AVG(comp_price_jun) as jun_price, AVG(comp_price_jul) as jul_price, AVG(comp_price_aug) as aug_price, AVG(comp_price_sep) as sep_price, AVG(comp_price_oct) as oct_price, AVG(comp_price_nov) as nov_price, AVG(comp_price_dec) as dec_price');
		
		$data['ur_bossing_prev_year2_jan'] = 0;
		$data['ur_bossing_prev_year2_feb'] = 0;
		$data['ur_bossing_prev_year2_mar'] = 0;
		$data['ur_bossing_prev_year2_apr'] = 0;
		$data['ur_bossing_prev_year2_may'] = 0;
		$data['ur_bossing_prev_year2_jun'] = 0;
		$data['ur_bossing_prev_year2_jul'] = 0;
		$data['ur_bossing_prev_year2_aug'] = 0;
		$data['ur_bossing_prev_year2_sep'] = 0;
		$data['ur_bossing_prev_year2_oct'] = 0;
		$data['ur_bossing_prev_year2_nov'] = 0;
		$data['ur_bossing_prev_year2_dec'] = 0;
		$data['ur_bossing_prev_year2_avg'] = 0;
		$data['ur_bossing_prev_year2_min'] = 0;
		$data['ur_bossing_prev_year2_max'] = 0;

		foreach($ur_bossing_prev_year2 as $row){
			$data['ur_bossing_prev_year2_jan'] = $row->jan_price;
			$data['ur_bossing_prev_year2_feb'] = $row->feb_price;
			$data['ur_bossing_prev_year2_mar'] = $row->mar_price;
			$data['ur_bossing_prev_year2_apr'] = $row->apr_price;
			$data['ur_bossing_prev_year2_may'] = $row->may_price;
			$data['ur_bossing_prev_year2_jun'] = $row->jun_price;
			$data['ur_bossing_prev_year2_jul'] = $row->jul_price;
			$data['ur_bossing_prev_year2_aug'] = $row->aug_price;
			$data['ur_bossing_prev_year2_sep'] = $row->sep_price;
			$data['ur_bossing_prev_year2_oct'] = $row->oct_price;
			$data['ur_bossing_prev_year2_nov'] = $row->nov_price;
			$data['ur_bossing_prev_year2_dec'] = $row->dec_price;
			$data['ur_bossing_prev_year2_avg'] = ($row->jan_price + $row->feb_price + $row->mar_price + $row->apr_price + $row->may_price + $row->jun_price + $row->jul_price + $row->aug_price + $row->sep_price + $row->oct_price + $row->nov_price + $row->dec_price) / 12;

			$data['ur_bossing_prev_year2_min'] = min($row->jan_price, $row->feb_price, $row->mar_price, $row->apr_price, $row->may_price, $row->jun_price, $row->jul_price, $row->aug_price, $row->sep_price, $row->oct_price, $row->nov_price, $row->dec_price);

			$data['ur_bossing_prev_year2_max'] = max($row->jan_price, $row->feb_price, $row->mar_price, $row->apr_price, $row->may_price, $row->jun_price, $row->jul_price, $row->aug_price, $row->sep_price, $row->oct_price, $row->nov_price, $row->dec_price);
		}


		$ur_bossing_prev_year1 = $this->admin->get_data('comparative_price_tbl', array('comp_price_product' => '11 PC', 'comp_price_segment' => 'UR', 'comp_price_year' => $year - 1,	'bc_id'	=>	$bc_id), 
			FALSE, 'AVG(comp_price_jan) as jan_price, AVG(comp_price_feb) as feb_price, AVG(comp_price_mar) as mar_price, AVG(comp_price_apr) as apr_price, AVG(comp_price_may) as may_price, AVG(comp_price_jun) as jun_price, AVG(comp_price_jul) as jul_price, AVG(comp_price_aug) as aug_price, AVG(comp_price_sep) as sep_price, AVG(comp_price_oct) as oct_price, AVG(comp_price_nov) as nov_price, AVG(comp_price_dec) as dec_price');
		
		$data['ur_bossing_prev_year1_jan'] = 0;
		$data['ur_bossing_prev_year1_feb'] = 0;
		$data['ur_bossing_prev_year1_mar'] = 0;
		$data['ur_bossing_prev_year1_apr'] = 0;
		$data['ur_bossing_prev_year1_may'] = 0;
		$data['ur_bossing_prev_year1_jun'] = 0;
		$data['ur_bossing_prev_year1_jul'] = 0;
		$data['ur_bossing_prev_year1_aug'] = 0;
		$data['ur_bossing_prev_year1_sep'] = 0;
		$data['ur_bossing_prev_year1_oct'] = 0;
		$data['ur_bossing_prev_year1_nov'] = 0;
		$data['ur_bossing_prev_year1_dec'] = 0;
		$data['ur_bossing_prev_year1_avg'] = 0;
		$data['ur_bossing_prev_year1_min'] = 0;
		$data['ur_bossing_prev_year1_max'] = 0;

		foreach($ur_bossing_prev_year1 as $row){
			$data['ur_bossing_prev_year1_jan'] = $row->jan_price;
			$data['ur_bossing_prev_year1_feb'] = $row->feb_price;
			$data['ur_bossing_prev_year1_mar'] = $row->mar_price;
			$data['ur_bossing_prev_year1_apr'] = $row->apr_price;
			$data['ur_bossing_prev_year1_may'] = $row->may_price;
			$data['ur_bossing_prev_year1_jun'] = $row->jun_price;
			$data['ur_bossing_prev_year1_jul'] = $row->jul_price;
			$data['ur_bossing_prev_year1_aug'] = $row->aug_price;
			$data['ur_bossing_prev_year1_sep'] = $row->sep_price;
			$data['ur_bossing_prev_year1_oct'] = $row->oct_price;
			$data['ur_bossing_prev_year1_nov'] = $row->nov_price;
			$data['ur_bossing_prev_year1_dec'] = $row->dec_price;
			$data['ur_bossing_prev_year1_avg'] = ($row->jan_price + $row->feb_price + $row->mar_price + $row->apr_price + $row->may_price + $row->jun_price + $row->jul_price + $row->aug_price + $row->sep_price + $row->oct_price + $row->nov_price + $row->dec_price) / 12;

			$data['ur_bossing_prev_year1_min'] = min($row->jan_price, $row->feb_price, $row->mar_price, $row->apr_price, $row->may_price, $row->jun_price, $row->jul_price, $row->aug_price, $row->sep_price, $row->oct_price, $row->nov_price, $row->dec_price);

			$data['ur_bossing_prev_year1_max'] = max($row->jan_price, $row->feb_price, $row->mar_price, $row->apr_price, $row->may_price, $row->jun_price, $row->jul_price, $row->aug_price, $row->sep_price, $row->oct_price, $row->nov_price, $row->dec_price);
		}

		/*UR Chooksies Cut ups*/
		$join_ur_cutups = array(
			'sales_item_tbl b' => 'a.sales_id = b.sales_id AND a.sales_status = 1 AND b.sales_item_status = 1 AND a.sales_year = ' . $year,
			'sales_details_tbl c' => 'b.sales_item_id = c.sales_item_id AND c.sales_det_status = 1',
			'material_tbl d' => 'b.material_id = d.material_id AND d.material_code = 10000150',
			'material_group_tbl e' => 'd.material_group_id = e.material_group_id AND e.material_group_name = \'NON MARINATED CUT UPS\'',
			'outlet_tbl f' => 'a.outlet_id = f.outlet_id AND f.bc_id = ' . $bc_id,
			'outlet_brand_tbl g' => 'f.outlet_id = g.outlet_id AND g.outlet_brand_status = 1',
			'brand_tbl h' => 'g.brand_id  = h.brand_id AND h.brand_name = \'ULING ROASTER\''
		);

		$get_ur_cutups = $this->admin->get_join('sales_tbl a', $join_ur_cutups, FALSE, FALSE, 'YEAR(c.sales_det_date), MONTH(c.sales_det_date)', 'AVG(c.sales_det_asp) as asp, MONTH(c.sales_det_date) asp_date');

		$data['ur_cutups1'] = 0;
		$data['ur_cutups2'] = 0;
		$data['ur_cutups3'] = 0;
		$data['ur_cutups4'] = 0;
		$data['ur_cutups5'] = 0;
		$data['ur_cutups6'] = 0;
		$data['ur_cutups7'] = 0;
		$data['ur_cutups8'] = 0;
		$data['ur_cutups9'] = 0;
		$data['ur_cutups10'] = 0;
		$data['ur_cutups11'] = 0;
		$data['ur_cutups12'] = 0;
		$data['ur_cutups_total'] = 0;
		$data['ur_cutups_count'] = 0;
		$data['ur_cutups_avg'] = 0;
		$data['ur_cutups_min'] = 0;
		$data['ur_cutups_max'] = 0;

		foreach($get_ur_cutups as $row){
			$asp = $row->asp;
			$month = $row->asp_date;
			$data['ur_cutups' . $month] = $asp;
			$data['ur_cutups_total'] += $asp;

			if($asp < $data['ur_cutups_min'] || $data['ur_cutups_count'] == 0){
				$data['ur_cutups_min'] = $asp;
			}

			if($asp > $data['ur_cutups_max'] || $data['ur_cutups_count'] == 0){
				$data['ur_cutups_max'] = $asp;
			}

			$data['ur_cutups_count']++;
		}

		$data['ur_cutups_avg'] = $data['ur_cutups_total'] != 0 ? $data['ur_cutups_total'] / $data['ur_cutups_count'] : 0;


		$ur_cutups_prev_year2 = $this->admin->get_data('comparative_price_tbl', array('comp_price_product' => ' CHOOKSIES CUTUPS', 'comp_price_segment' => 'UR', 'comp_price_year' => $year - 2,	'bc_id'	=>	$bc_id), 
			FALSE, 'AVG(comp_price_jan) as jan_price, AVG(comp_price_feb) as feb_price, AVG(comp_price_mar) as mar_price, AVG(comp_price_apr) as apr_price, AVG(comp_price_may) as may_price, AVG(comp_price_jun) as jun_price, AVG(comp_price_jul) as jul_price, AVG(comp_price_aug) as aug_price, AVG(comp_price_sep) as sep_price, AVG(comp_price_oct) as oct_price, AVG(comp_price_nov) as nov_price, AVG(comp_price_dec) as dec_price');
		
		$data['ur_cutups_prev_year2_jan'] = 0;
		$data['ur_cutups_prev_year2_feb'] = 0;
		$data['ur_cutups_prev_year2_mar'] = 0;
		$data['ur_cutups_prev_year2_apr'] = 0;
		$data['ur_cutups_prev_year2_may'] = 0;
		$data['ur_cutups_prev_year2_jun'] = 0;
		$data['ur_cutups_prev_year2_jul'] = 0;
		$data['ur_cutups_prev_year2_aug'] = 0;
		$data['ur_cutups_prev_year2_sep'] = 0;
		$data['ur_cutups_prev_year2_oct'] = 0;
		$data['ur_cutups_prev_year2_nov'] = 0;
		$data['ur_cutups_prev_year2_dec'] = 0;
		$data['ur_cutups_prev_year2_avg'] = 0;
		$data['ur_cutups_prev_year2_min'] = 0;
		$data['ur_cutups_prev_year2_max'] = 0;

		foreach($ur_cutups_prev_year2 as $row){
			$data['ur_cutups_prev_year2_jan'] = $row->jan_price;
			$data['ur_cutups_prev_year2_feb'] = $row->feb_price;
			$data['ur_cutups_prev_year2_mar'] = $row->mar_price;
			$data['ur_cutups_prev_year2_apr'] = $row->apr_price;
			$data['ur_cutups_prev_year2_may'] = $row->may_price;
			$data['ur_cutups_prev_year2_jun'] = $row->jun_price;
			$data['ur_cutups_prev_year2_jul'] = $row->jul_price;
			$data['ur_cutups_prev_year2_aug'] = $row->aug_price;
			$data['ur_cutups_prev_year2_sep'] = $row->sep_price;
			$data['ur_cutups_prev_year2_oct'] = $row->oct_price;
			$data['ur_cutups_prev_year2_nov'] = $row->nov_price;
			$data['ur_cutups_prev_year2_dec'] = $row->dec_price;
			$data['ur_cutups_prev_year2_avg'] = ($row->jan_price + $row->feb_price + $row->mar_price + $row->apr_price + $row->may_price + $row->jun_price + $row->jul_price + $row->aug_price + $row->sep_price + $row->oct_price + $row->nov_price + $row->dec_price) / 12;

			$data['ur_cutups_prev_year2_min'] = min($row->jan_price, $row->feb_price, $row->mar_price, $row->apr_price, $row->may_price, $row->jun_price, $row->jul_price, $row->aug_price, $row->sep_price, $row->oct_price, $row->nov_price, $row->dec_price);

			$data['ur_cutups_prev_year2_max'] = max($row->jan_price, $row->feb_price, $row->mar_price, $row->apr_price, $row->may_price, $row->jun_price, $row->jul_price, $row->aug_price, $row->sep_price, $row->oct_price, $row->nov_price, $row->dec_price);
		}


		$ur_cutups_prev_year1 = $this->admin->get_data('comparative_price_tbl', array('comp_price_product' => 'CHOOKSIES CUTUPS', 'comp_price_segment' => 'UR', 'comp_price_year' => $year - 1,	'bc_id'	=>	$bc_id), 
			FALSE, 'AVG(comp_price_jan) as jan_price, AVG(comp_price_feb) as feb_price, AVG(comp_price_mar) as mar_price, AVG(comp_price_apr) as apr_price, AVG(comp_price_may) as may_price, AVG(comp_price_jun) as jun_price, AVG(comp_price_jul) as jul_price, AVG(comp_price_aug) as aug_price, AVG(comp_price_sep) as sep_price, AVG(comp_price_oct) as oct_price, AVG(comp_price_nov) as nov_price, AVG(comp_price_dec) as dec_price');
		
		$data['ur_cutups_prev_year1_jan'] = 0;
		$data['ur_cutups_prev_year1_feb'] = 0;
		$data['ur_cutups_prev_year1_mar'] = 0;
		$data['ur_cutups_prev_year1_apr'] = 0;
		$data['ur_cutups_prev_year1_may'] = 0;
		$data['ur_cutups_prev_year1_jun'] = 0;
		$data['ur_cutups_prev_year1_jul'] = 0;
		$data['ur_cutups_prev_year1_aug'] = 0;
		$data['ur_cutups_prev_year1_sep'] = 0;
		$data['ur_cutups_prev_year1_oct'] = 0;
		$data['ur_cutups_prev_year1_nov'] = 0;
		$data['ur_cutups_prev_year1_dec'] = 0;
		$data['ur_cutups_prev_year1_avg'] = 0;
		$data['ur_cutups_prev_year1_min'] = 0;
		$data['ur_cutups_prev_year1_max'] = 0;

		foreach($ur_cutups_prev_year1 as $row){
			$data['ur_cutups_prev_year1_jan'] = $row->jan_price;
			$data['ur_cutups_prev_year1_feb'] = $row->feb_price;
			$data['ur_cutups_prev_year1_mar'] = $row->mar_price;
			$data['ur_cutups_prev_year1_apr'] = $row->apr_price;
			$data['ur_cutups_prev_year1_may'] = $row->may_price;
			$data['ur_cutups_prev_year1_jun'] = $row->jun_price;
			$data['ur_cutups_prev_year1_jul'] = $row->jul_price;
			$data['ur_cutups_prev_year1_aug'] = $row->aug_price;
			$data['ur_cutups_prev_year1_sep'] = $row->sep_price;
			$data['ur_cutups_prev_year1_oct'] = $row->oct_price;
			$data['ur_cutups_prev_year1_nov'] = $row->nov_price;
			$data['ur_cutups_prev_year1_dec'] = $row->dec_price;
			$data['ur_cutups_prev_year1_avg'] = ($row->jan_price + $row->feb_price + $row->mar_price + $row->apr_price + $row->may_price + $row->jun_price + $row->jul_price + $row->aug_price + $row->sep_price + $row->oct_price + $row->nov_price + $row->dec_price) / 12;

			$data['ur_cutups_prev_year1_min'] = min($row->jan_price, $row->feb_price, $row->mar_price, $row->apr_price, $row->may_price, $row->jun_price, $row->jul_price, $row->aug_price, $row->sep_price, $row->oct_price, $row->nov_price, $row->dec_price);

			$data['ur_cutups_prev_year1_max'] = max($row->jan_price, $row->feb_price, $row->mar_price, $row->apr_price, $row->may_price, $row->jun_price, $row->jul_price, $row->aug_price, $row->sep_price, $row->oct_price, $row->nov_price, $row->dec_price);
		}

		/*UR Liver / Gizzard*/
		$join_ur_liver = array(
			'sales_item_tbl b' => 'a.sales_id = b.sales_id AND a.sales_status = 1 AND b.sales_item_status = 1 AND a.sales_year = ' . $year,
			'sales_details_tbl c' => 'b.sales_item_id = c.sales_item_id AND c.sales_det_status = 1',
			'material_tbl d' => 'b.material_id = d.material_id',
			'material_group_tbl e' => 'd.material_group_id = e.material_group_id AND e.material_group_name = \'LIVER / GIZZARD\'',
			'outlet_tbl f' => 'a.outlet_id = f.outlet_id AND f.bc_id = ' . $bc_id,
			'outlet_brand_tbl g' => 'f.outlet_id = g.outlet_id AND g.outlet_brand_status = 1',
			'brand_tbl h' => 'g.brand_id  = h.brand_id AND h.brand_name = \'ULING ROASTER\''
		);

		$get_ur_liver = $this->admin->get_join('sales_tbl a', $join_ur_liver, FALSE, FALSE, 'YEAR(c.sales_det_date), MONTH(c.sales_det_date)', 'AVG(c.sales_det_asp) as asp, MONTH(c.sales_det_date) asp_date');

		$data['ur_liver1'] = 0;
		$data['ur_liver2'] = 0;
		$data['ur_liver3'] = 0;
		$data['ur_liver4'] = 0;
		$data['ur_liver5'] = 0;
		$data['ur_liver6'] = 0;
		$data['ur_liver7'] = 0;
		$data['ur_liver8'] = 0;
		$data['ur_liver9'] = 0;
		$data['ur_liver10'] = 0;
		$data['ur_liver11'] = 0;
		$data['ur_liver12'] = 0;
		$data['ur_liver_total'] = 0;
		$data['ur_liver_count'] = 0;
		$data['ur_liver_avg'] = 0;
		$data['ur_liver_min'] = 0;
		$data['ur_liver_max'] = 0;

		foreach($get_ur_liver as $row){
			$asp = $row->asp;
			$month = $row->asp_date;
			$data['ur_liver' . $month] = $asp;
			$data['ur_liver_total'] += $asp;

			if($asp < $data['ur_liver_min'] || $data['ur_liver_count'] == 0){
				$data['ur_cutups_min'] = $asp;
			}

			if($asp > $data['ur_liver_max'] || $data['ur_liver_count'] == 0){
				$data['ur_liver_max'] = $asp;
			}

			$data['ur_liver_count']++;
		}

		$data['ur_liver_avg'] = $data['ur_liver_total'] != 0 ? $data['ur_liver_total'] / $data['ur_liver_count'] : 0;


		$ur_liver_prev_year2 = $this->admin->get_data('comparative_price_tbl', array('comp_price_product' => 'GIZZARD / LIVER', 'comp_price_segment' => 'UR', 'comp_price_year' => $year - 2,	'bc_id'	=>	$bc_id), 
			FALSE, 'AVG(comp_price_jan) as jan_price, AVG(comp_price_feb) as feb_price, AVG(comp_price_mar) as mar_price, AVG(comp_price_apr) as apr_price, AVG(comp_price_may) as may_price, AVG(comp_price_jun) as jun_price, AVG(comp_price_jul) as jul_price, AVG(comp_price_aug) as aug_price, AVG(comp_price_sep) as sep_price, AVG(comp_price_oct) as oct_price, AVG(comp_price_nov) as nov_price, AVG(comp_price_dec) as dec_price');
		
		$data['ur_liver_prev_year2_jan'] = 0;
		$data['ur_liver_prev_year2_feb'] = 0;
		$data['ur_liver_prev_year2_mar'] = 0;
		$data['ur_liver_prev_year2_apr'] = 0;
		$data['ur_liver_prev_year2_may'] = 0;
		$data['ur_liver_prev_year2_jun'] = 0;
		$data['ur_liver_prev_year2_jul'] = 0;
		$data['ur_liver_prev_year2_aug'] = 0;
		$data['ur_liver_prev_year2_sep'] = 0;
		$data['ur_liver_prev_year2_oct'] = 0;
		$data['ur_liver_prev_year2_nov'] = 0;
		$data['ur_liver_prev_year2_dec'] = 0;
		$data['ur_liver_prev_year2_avg'] = 0;
		$data['ur_liver_prev_year2_min'] = 0;
		$data['ur_liver_prev_year2_max'] = 0;

		foreach($ur_liver_prev_year2 as $row){
			$data['ur_liver_prev_year2_jan'] = $row->jan_price;
			$data['ur_liver_prev_year2_feb'] = $row->feb_price;
			$data['ur_liver_prev_year2_mar'] = $row->mar_price;
			$data['ur_liver_prev_year2_apr'] = $row->apr_price;
			$data['ur_liver_prev_year2_may'] = $row->may_price;
			$data['ur_liver_prev_year2_jun'] = $row->jun_price;
			$data['ur_liver_prev_year2_jul'] = $row->jul_price;
			$data['ur_liver_prev_year2_aug'] = $row->aug_price;
			$data['ur_liver_prev_year2_sep'] = $row->sep_price;
			$data['ur_liver_prev_year2_oct'] = $row->oct_price;
			$data['ur_liver_prev_year2_nov'] = $row->nov_price;
			$data['ur_liver_prev_year2_dec'] = $row->dec_price;
			$data['ur_liver_prev_year2_avg'] = ($row->jan_price + $row->feb_price + $row->mar_price + $row->apr_price + $row->may_price + $row->jun_price + $row->jul_price + $row->aug_price + $row->sep_price + $row->oct_price + $row->nov_price + $row->dec_price) / 12;

			$data['ur_liver_prev_year2_min'] = min($row->jan_price, $row->feb_price, $row->mar_price, $row->apr_price, $row->may_price, $row->jun_price, $row->jul_price, $row->aug_price, $row->sep_price, $row->oct_price, $row->nov_price, $row->dec_price);

			$data['ur_liver_prev_year2_max'] = max($row->jan_price, $row->feb_price, $row->mar_price, $row->apr_price, $row->may_price, $row->jun_price, $row->jul_price, $row->aug_price, $row->sep_price, $row->oct_price, $row->nov_price, $row->dec_price);
		}


		$ur_liver_prev_year1 = $this->admin->get_data('comparative_price_tbl', array('comp_price_product' => 'GIZZARD / LIVER', 'comp_price_segment' => 'UR', 'comp_price_year' => $year - 1,	'bc_id'	=>	$bc_id), 
			FALSE, 'AVG(comp_price_jan) as jan_price, AVG(comp_price_feb) as feb_price, AVG(comp_price_mar) as mar_price, AVG(comp_price_apr) as apr_price, AVG(comp_price_may) as may_price, AVG(comp_price_jun) as jun_price, AVG(comp_price_jul) as jul_price, AVG(comp_price_aug) as aug_price, AVG(comp_price_sep) as sep_price, AVG(comp_price_oct) as oct_price, AVG(comp_price_nov) as nov_price, AVG(comp_price_dec) as dec_price');
		
		$data['ur_liver_prev_year1_jan'] = 0;
		$data['ur_liver_prev_year1_feb'] = 0;
		$data['ur_liver_prev_year1_mar'] = 0;
		$data['ur_liver_prev_year1_apr'] = 0;
		$data['ur_liver_prev_year1_may'] = 0;
		$data['ur_liver_prev_year1_jun'] = 0;
		$data['ur_liver_prev_year1_jul'] = 0;
		$data['ur_liver_prev_year1_aug'] = 0;
		$data['ur_liver_prev_year1_sep'] = 0;
		$data['ur_liver_prev_year1_oct'] = 0;
		$data['ur_liver_prev_year1_nov'] = 0;
		$data['ur_liver_prev_year1_dec'] = 0;
		$data['ur_liver_prev_year1_avg'] = 0;
		$data['ur_liver_prev_year1_min'] = 0;
		$data['ur_liver_prev_year1_max'] = 0;

		foreach($ur_liver_prev_year1 as $row){
			$data['ur_liver_prev_year1_jan'] = $row->jan_price;
			$data['ur_liver_prev_year1_feb'] = $row->feb_price;
			$data['ur_liver_prev_year1_mar'] = $row->mar_price;
			$data['ur_liver_prev_year1_apr'] = $row->apr_price;
			$data['ur_liver_prev_year1_may'] = $row->may_price;
			$data['ur_liver_prev_year1_jun'] = $row->jun_price;
			$data['ur_liver_prev_year1_jul'] = $row->jul_price;
			$data['ur_liver_prev_year1_aug'] = $row->aug_price;
			$data['ur_liver_prev_year1_sep'] = $row->sep_price;
			$data['ur_liver_prev_year1_oct'] = $row->oct_price;
			$data['ur_liver_prev_year1_nov'] = $row->nov_price;
			$data['ur_liver_prev_year1_dec'] = $row->dec_price;
			$data['ur_liver_prev_year1_avg'] = ($row->jan_price + $row->feb_price + $row->mar_price + $row->apr_price + $row->may_price + $row->jun_price + $row->jul_price + $row->aug_price + $row->sep_price + $row->oct_price + $row->nov_price + $row->dec_price) / 12;

			$data['ur_liver_prev_year1_min'] = min($row->jan_price, $row->feb_price, $row->mar_price, $row->apr_price, $row->may_price, $row->jun_price, $row->jul_price, $row->aug_price, $row->sep_price, $row->oct_price, $row->nov_price, $row->dec_price);

			$data['ur_liver_prev_year1_max'] = max($row->jan_price, $row->feb_price, $row->mar_price, $row->apr_price, $row->may_price, $row->jun_price, $row->jul_price, $row->aug_price, $row->sep_price, $row->oct_price, $row->nov_price, $row->dec_price);
		}

		/*REYAL*/

		/*REYAL Chicken*/

		$get_improved_sweet_and_spicy_orc = $this->admin->get_query("SELECT p.size_name, AVG(asp_tbl.jan_asp) as orc_jan, AVG(asp_tbl.feb_asp) as orc_feb, AVG(asp_tbl.mar_asp) as orc_mar, AVG(asp_tbl.apr_asp) as orc_apr, AVG(asp_tbl.may_asp) as orc_may, AVG(asp_tbl.jun_asp) as orc_jun, AVG(asp_tbl.jul_asp) as orc_jul, AVG(asp_tbl.aug_asp) as orc_aug, AVG(asp_tbl.sep_asp) as orc_sep, AVG(asp_tbl.oct_asp) as orc_oct, AVG(asp_tbl.nov_asp) as orc_nov, AVG(asp_tbl.dec_asp) as orc_dec FROM material_tbl o, size_tbl p,

		(SELECT m.material_id, m.size_id,

		(SELECT AVG(c.sales_det_asp) FROM `sales_tbl` `a` JOIN `sales_item_tbl` `b` ON `a`.`sales_id` = `b`.`sales_id` AND `a`.`sales_status` = 1 AND `b`.`sales_item_status` = 1 AND `a`.`sales_year` = " . $year . " JOIN `sales_details_tbl` `c` ON `b`.`sales_item_id` = `c`.`sales_item_id` AND `c`.`sales_det_status` = 1 JOIN `material_tbl` `d` ON `b`.`material_id` = `d`.`material_id` JOIN `material_group_tbl` `e` ON `d`.`material_group_id` = `e`.`material_group_id` JOIN `outlet_tbl` `f` ON `a`.`outlet_id` = `f`.`outlet_id` JOIN `outlet_brand_tbl` `g` ON `f`.`outlet_id` = `g`.`outlet_id` AND `g`.`outlet_brand_status` = 1 JOIN `brand_tbl` `h` ON `g`.`brand_id` = `h`.`brand_id` AND `h`.`brand_name` = 'REYAL' AND c.sales_det_date = '" . $year . "-01-01' WHERE d.material_id = m.material_id AND f.bc_id = " . $bc_id . ") as jan_asp,
    
    	(SELECT AVG(c.sales_det_asp) FROM `sales_tbl` `a` JOIN `sales_item_tbl` `b` ON `a`.`sales_id` = `b`.`sales_id` AND `a`.`sales_status` = 1 AND `b`.`sales_item_status` = 1 AND `a`.`sales_year` = " . $year . " JOIN `sales_details_tbl` `c` ON `b`.`sales_item_id` = `c`.`sales_item_id` AND `c`.`sales_det_status` = 1 JOIN `material_tbl` `d` ON `b`.`material_id` = `d`.`material_id` JOIN `material_group_tbl` `e` ON `d`.`material_group_id` = `e`.`material_group_id` JOIN `outlet_tbl` `f` ON `a`.`outlet_id` = `f`.`outlet_id` JOIN `outlet_brand_tbl` `g` ON `f`.`outlet_id` = `g`.`outlet_id` AND `g`.`outlet_brand_status` = 1 JOIN `brand_tbl` `h` ON `g`.`brand_id` = `h`.`brand_id` AND `h`.`brand_name` = 'REYAL' AND c.sales_det_date = '" . $year . "-02-01' WHERE d.material_id = m.material_id AND f.bc_id = " . $bc_id . ") as feb_asp,
    
    	(SELECT AVG(c.sales_det_asp) FROM `sales_tbl` `a` JOIN `sales_item_tbl` `b` ON `a`.`sales_id` = `b`.`sales_id` AND `a`.`sales_status` = 1 AND `b`.`sales_item_status` = 1 AND `a`.`sales_year` = " . $year . " JOIN `sales_details_tbl` `c` ON `b`.`sales_item_id` = `c`.`sales_item_id` AND `c`.`sales_det_status` = 1 JOIN `material_tbl` `d` ON `b`.`material_id` = `d`.`material_id` JOIN `material_group_tbl` `e` ON `d`.`material_group_id` = `e`.`material_group_id` JOIN `outlet_tbl` `f` ON `a`.`outlet_id` = `f`.`outlet_id` JOIN `outlet_brand_tbl` `g` ON `f`.`outlet_id` = `g`.`outlet_id` AND `g`.`outlet_brand_status` = 1 JOIN `brand_tbl` `h` ON `g`.`brand_id` = `h`.`brand_id` AND `h`.`brand_name` = 'REYAL' AND c.sales_det_date = '" . $year . "-03-01' WHERE d.material_id = m.material_id AND f.bc_id = " . $bc_id . ") as mar_asp,
    
   		(SELECT AVG(c.sales_det_asp) FROM `sales_tbl` `a` JOIN `sales_item_tbl` `b` ON `a`.`sales_id` = `b`.`sales_id` AND `a`.`sales_status` = 1 AND `b`.`sales_item_status` = 1 AND `a`.`sales_year` = " . $year . " JOIN `sales_details_tbl` `c` ON `b`.`sales_item_id` = `c`.`sales_item_id` AND `c`.`sales_det_status` = 1 JOIN `material_tbl` `d` ON `b`.`material_id` = `d`.`material_id` JOIN `material_group_tbl` `e` ON `d`.`material_group_id` = `e`.`material_group_id` JOIN `outlet_tbl` `f` ON `a`.`outlet_id` = `f`.`outlet_id` JOIN `outlet_brand_tbl` `g` ON `f`.`outlet_id` = `g`.`outlet_id` AND `g`.`outlet_brand_status` = 1 JOIN `brand_tbl` `h` ON `g`.`brand_id` = `h`.`brand_id` AND `h`.`brand_name` = 'REYAL' AND c.sales_det_date = '" . $year . "-04-01' WHERE d.material_id = m.material_id AND f.bc_id = " . $bc_id . ") as apr_asp,
    
    	(SELECT AVG(c.sales_det_asp) FROM `sales_tbl` `a` JOIN `sales_item_tbl` `b` ON `a`.`sales_id` = `b`.`sales_id` AND `a`.`sales_status` = 1 AND `b`.`sales_item_status` = 1 AND `a`.`sales_year` = " . $year . " JOIN `sales_details_tbl` `c` ON `b`.`sales_item_id` = `c`.`sales_item_id` AND `c`.`sales_det_status` = 1 JOIN `material_tbl` `d` ON `b`.`material_id` = `d`.`material_id` JOIN `material_group_tbl` `e` ON `d`.`material_group_id` = `e`.`material_group_id` JOIN `outlet_tbl` `f` ON `a`.`outlet_id` = `f`.`outlet_id` JOIN `outlet_brand_tbl` `g` ON `f`.`outlet_id` = `g`.`outlet_id` AND `g`.`outlet_brand_status` = 1 JOIN `brand_tbl` `h` ON `g`.`brand_id` = `h`.`brand_id` AND `h`.`brand_name` = 'REYAL' AND c.sales_det_date = '" . $year . "-05-01' WHERE d.material_id = m.material_id AND f.bc_id = " . $bc_id . ") as may_asp,
    
    	(SELECT AVG(c.sales_det_asp) FROM `sales_tbl` `a` JOIN `sales_item_tbl` `b` ON `a`.`sales_id` = `b`.`sales_id` AND `a`.`sales_status` = 1 AND `b`.`sales_item_status` = 1 AND `a`.`sales_year` = " . $year . " JOIN `sales_details_tbl` `c` ON `b`.`sales_item_id` = `c`.`sales_item_id` AND `c`.`sales_det_status` = 1 JOIN `material_tbl` `d` ON `b`.`material_id` = `d`.`material_id` JOIN `material_group_tbl` `e` ON `d`.`material_group_id` = `e`.`material_group_id` JOIN `outlet_tbl` `f` ON `a`.`outlet_id` = `f`.`outlet_id` JOIN `outlet_brand_tbl` `g` ON `f`.`outlet_id` = `g`.`outlet_id` AND `g`.`outlet_brand_status` = 1 JOIN `brand_tbl` `h` ON `g`.`brand_id` = `h`.`brand_id` AND `h`.`brand_name` = 'REYAL' AND c.sales_det_date = '" . $year . "-06-01' WHERE d.material_id = m.material_id AND f.bc_id = " . $bc_id . ") as jun_asp,
    
    	(SELECT AVG(c.sales_det_asp) FROM `sales_tbl` `a` JOIN `sales_item_tbl` `b` ON `a`.`sales_id` = `b`.`sales_id` AND `a`.`sales_status` = 1 AND `b`.`sales_item_status` = 1 AND `a`.`sales_year` = " . $year . " JOIN `sales_details_tbl` `c` ON `b`.`sales_item_id` = `c`.`sales_item_id` AND `c`.`sales_det_status` = 1 JOIN `material_tbl` `d` ON `b`.`material_id` = `d`.`material_id` JOIN `material_group_tbl` `e` ON `d`.`material_group_id` = `e`.`material_group_id` JOIN `outlet_tbl` `f` ON `a`.`outlet_id` = `f`.`outlet_id` JOIN `outlet_brand_tbl` `g` ON `f`.`outlet_id` = `g`.`outlet_id` AND `g`.`outlet_brand_status` = 1 JOIN `brand_tbl` `h` ON `g`.`brand_id` = `h`.`brand_id` AND `h`.`brand_name` = 'REYAL' AND c.sales_det_date = '" . $year . "-07-01' WHERE d.material_id = m.material_id AND f.bc_id = " . $bc_id . ") as jul_asp,
    
    	(SELECT AVG(c.sales_det_asp) FROM `sales_tbl` `a` JOIN `sales_item_tbl` `b` ON `a`.`sales_id` = `b`.`sales_id` AND `a`.`sales_status` = 1 AND `b`.`sales_item_status` = 1 AND `a`.`sales_year` = " . $year . " JOIN `sales_details_tbl` `c` ON `b`.`sales_item_id` = `c`.`sales_item_id` AND `c`.`sales_det_status` = 1 JOIN `material_tbl` `d` ON `b`.`material_id` = `d`.`material_id` JOIN `material_group_tbl` `e` ON `d`.`material_group_id` = `e`.`material_group_id` JOIN `outlet_tbl` `f` ON `a`.`outlet_id` = `f`.`outlet_id` JOIN `outlet_brand_tbl` `g` ON `f`.`outlet_id` = `g`.`outlet_id` AND `g`.`outlet_brand_status` = 1 JOIN `brand_tbl` `h` ON `g`.`brand_id` = `h`.`brand_id` AND `h`.`brand_name` = 'REYAL' AND c.sales_det_date = '" . $year . "-08-01' WHERE d.material_id = m.material_id AND f.bc_id = " . $bc_id . ") as aug_asp,
    
    	(SELECT AVG(c.sales_det_asp) FROM `sales_tbl` `a` JOIN `sales_item_tbl` `b` ON `a`.`sales_id` = `b`.`sales_id` AND `a`.`sales_status` = 1 AND `b`.`sales_item_status` = 1 AND `a`.`sales_year` = " . $year . " JOIN `sales_details_tbl` `c` ON `b`.`sales_item_id` = `c`.`sales_item_id` AND `c`.`sales_det_status` = 1 JOIN `material_tbl` `d` ON `b`.`material_id` = `d`.`material_id` JOIN `material_group_tbl` `e` ON `d`.`material_group_id` = `e`.`material_group_id` JOIN `outlet_tbl` `f` ON `a`.`outlet_id` = `f`.`outlet_id` JOIN `outlet_brand_tbl` `g` ON `f`.`outlet_id` = `g`.`outlet_id` AND `g`.`outlet_brand_status` = 1 JOIN `brand_tbl` `h` ON `g`.`brand_id` = `h`.`brand_id` AND `h`.`brand_name` = 'REYAL' AND c.sales_det_date = '" . $year . "-09-01' WHERE d.material_id = m.material_id AND f.bc_id = " . $bc_id . ") as sep_asp,
    
    	(SELECT AVG(c.sales_det_asp) FROM `sales_tbl` `a` JOIN `sales_item_tbl` `b` ON `a`.`sales_id` = `b`.`sales_id` AND `a`.`sales_status` = 1 AND `b`.`sales_item_status` = 1 AND `a`.`sales_year` = " . $year . " JOIN `sales_details_tbl` `c` ON `b`.`sales_item_id` = `c`.`sales_item_id` AND `c`.`sales_det_status` = 1 JOIN `material_tbl` `d` ON `b`.`material_id` = `d`.`material_id` JOIN `material_group_tbl` `e` ON `d`.`material_group_id` = `e`.`material_group_id` JOIN `outlet_tbl` `f` ON `a`.`outlet_id` = `f`.`outlet_id` JOIN `outlet_brand_tbl` `g` ON `f`.`outlet_id` = `g`.`outlet_id` AND `g`.`outlet_brand_status` = 1 JOIN `brand_tbl` `h` ON `g`.`brand_id` = `h`.`brand_id` AND `h`.`brand_name` = 'REYAL' AND c.sales_det_date = '" . $year . "-10-01' WHERE d.material_id = m.material_id AND f.bc_id = " . $bc_id . ") as oct_asp,
    
    	(SELECT AVG(c.sales_det_asp) FROM `sales_tbl` `a` JOIN `sales_item_tbl` `b` ON `a`.`sales_id` = `b`.`sales_id` AND `a`.`sales_status` = 1 AND `b`.`sales_item_status` = 1 AND `a`.`sales_year` = " . $year . " JOIN `sales_details_tbl` `c` ON `b`.`sales_item_id` = `c`.`sales_item_id` AND `c`.`sales_det_status` = 1 JOIN `material_tbl` `d` ON `b`.`material_id` = `d`.`material_id` JOIN `material_group_tbl` `e` ON `d`.`material_group_id` = `e`.`material_group_id` JOIN `outlet_tbl` `f` ON `a`.`outlet_id` = `f`.`outlet_id` JOIN `outlet_brand_tbl` `g` ON `f`.`outlet_id` = `g`.`outlet_id` AND `g`.`outlet_brand_status` = 1 JOIN `brand_tbl` `h` ON `g`.`brand_id` = `h`.`brand_id` AND `h`.`brand_name` = 'REYAL' AND c.sales_det_date = '" . $year . "-11-01' WHERE d.material_id = m.material_id AND f.bc_id = " . $bc_id . ") as nov_asp,
    
    	(SELECT AVG(c.sales_det_asp) FROM `sales_tbl` `a` JOIN `sales_item_tbl` `b` ON `a`.`sales_id` = `b`.`sales_id` AND `a`.`sales_status` = 1 AND `b`.`sales_item_status` = 1 AND `a`.`sales_year` = " . $year . " JOIN `sales_details_tbl` `c` ON `b`.`sales_item_id` = `c`.`sales_item_id` AND `c`.`sales_det_status` = 1 JOIN `material_tbl` `d` ON `b`.`material_id` = `d`.`material_id` JOIN `material_group_tbl` `e` ON `d`.`material_group_id` = `e`.`material_group_id` JOIN `outlet_tbl` `f` ON `a`.`outlet_id` = `f`.`outlet_id` JOIN `outlet_brand_tbl` `g` ON `f`.`outlet_id` = `g`.`outlet_id` AND `g`.`outlet_brand_status` = 1 JOIN `brand_tbl` `h` ON `g`.`brand_id` = `h`.`brand_id` AND `h`.`brand_name` = 'REYAL' AND c.sales_det_date = '" . $year . "-12-01' WHERE d.material_id =  m.material_id AND f.bc_id = " . $bc_id . ") as dec_asp
    
		FROM material_size_tbl m, brand_tbl n WHERE m.brand_id = n.brand_id AND m.material_size_status = 1 AND n.brand_name = 'REYAL') as asp_tbl

		WHERE o.material_id = asp_tbl.material_id AND p.size_id = asp_tbl.size_id GROUP BY p.size_id");

		$data['improved_sweet_and_spicy_bfc1'] = $data['improved_sweet_and_spicy_bfc2'] = $data['improved_sweet_and_spicy_bfc3'] = $data['improved_sweet_and_spicy_bfc4'] = $data['improved_sweet_and_spicy_bfc5'] = $data['improved_sweet_and_spicy_bfc6'] = $data['improved_sweet_and_spicy_bfc7'] = $data['improved_sweet_and_spicy_bfc8'] = $data['improved_sweet_and_spicy_bfc9'] = $data['improved_sweet_and_spicy_bfc10'] = $data['improved_sweet_and_spicy_bfc11'] = $data['improved_sweet_and_spicy_bfc12'] = $data['improved_sweet_and_spicy_bfc_total'] = $data['improved_sweet_and_spicy_bfc_count'] = $data['improved_sweet_and_spicy_bfc_avg'] = $data['improved_sweet_and_spicy_bfc_min'] = $data['improved_sweet_and_spicy_bfc_max'] = 0;

		
		foreach($get_improved_sweet_and_spicy_orc as $row){

			$orc_size = $row->size_name;
			$orc_jan = $row->orc_jan;
			$orc_feb = $row->orc_feb;
			$orc_mar = $row->orc_mar;
			$orc_apr = $row->orc_apr;
			$orc_may = $row->orc_may;
			$orc_jun = $row->orc_jun;
			$orc_jul = $row->orc_jul;
			$orc_aug = $row->orc_aug;
			$orc_sep = $row->orc_sep;
			$orc_oct = $row->orc_oct;
			$orc_nov = $row->orc_nov;
			$orc_dec = $row->orc_dec;
			$orc_min = min($orc_jan, $orc_feb, $orc_mar, $orc_apr, $orc_may, $orc_jun, $orc_jul, $orc_aug, $orc_sep, $orc_oct, $orc_nov, $orc_dec);
			$orc_max = max($orc_jan, $orc_feb, $orc_mar, $orc_apr, $orc_may, $orc_jun, $orc_jul, $orc_aug, $orc_sep, $orc_oct, $orc_nov, $orc_dec);
			$orc_avg = ($orc_jan + $orc_feb + $orc_mar + $orc_apr + $orc_may + $orc_jun + $orc_jul + $orc_aug + $orc_sep + $orc_oct + $orc_nov + $orc_dec) / 12;

			if($orc_size == "BUTTERFLY CHICKEN"){
				$data['improved_sweet_and_spicy_bfc1'] = $orc_jan;
				$data['improved_sweet_and_spicy_bfc'] = $orc_feb;
				$data['improved_sweet_and_spicy_bfc'] = $orc_mar;
				$data['improved_sweet_and_spicy_bfc'] = $orc_apr;
				$data['improved_sweet_and_spicy_bfc'] = $orc_may;
				$data['improved_sweet_and_spicy_bfc'] = $orc_jun;
				$data['improved_sweet_and_spicy_bfc'] = $orc_jul;
				$data['improved_sweet_and_spicy_bfc'] = $orc_aug;
				$data['improved_sweet_and_spicy_bfc'] = $orc_sep;
				$data['improved_sweet_and_spicy_bfc'] = $orc_oct;
				$data['improved_sweet_and_spicy_bfc'] = $orc_nov;
				$data['improved_sweet_and_spicy_bfc'] = $orc_dec;
				$data['improved_sweet_and_spicy_bfc_avg'] = $orc_avg;
				$data['improved_sweet_and_spicy_bfc_min'] = $orc_min;
				$data['improved_sweet_and_spicy_bfc_max'] = $orc_max;

				$improved_sweet_and_spicy_bfc_prev_year2 = $this->admin->get_data('comparative_price_tbl', array('comp_price_product' => 'BUTTERFLY', 'comp_price_segment' => 'REYAL', 'comp_price_year' => $year - 2,	'bc_id'	=>	$bc_id), 
			FALSE, 'AVG(comp_price_jan) as jan_price, AVG(comp_price_feb) as feb_price, AVG(comp_price_mar) as mar_price, AVG(comp_price_apr) as apr_price, AVG(comp_price_may) as may_price, AVG(comp_price_jun) as jun_price, AVG(comp_price_jul) as jul_price, AVG(comp_price_aug) as aug_price, AVG(comp_price_sep) as sep_price, AVG(comp_price_oct) as oct_price, AVG(comp_price_nov) as nov_price, AVG(comp_price_dec) as dec_price');
		
				$data['improved_sweet_and_spicy_bfc_prev_year2_jan'] = 0;
				$data['improved_sweet_and_spicy_bfc_prev_year2_feb'] = 0;
				$data['improved_sweet_and_spicy_bfc_prev_year2_mar'] = 0;
				$data['improved_sweet_and_spicy_bfc_prev_year2_apr'] = 0;
				$data['improved_sweet_and_spicy_bfc_prev_year2_may'] = 0;
				$data['improved_sweet_and_spicy_bfc_prev_year2_jun'] = 0;
				$data['improved_sweet_and_spicy_bfc_prev_year2_jul'] = 0;
				$data['improved_sweet_and_spicy_bfc_prev_year2_aug'] = 0;
				$data['improved_sweet_and_spicy_bfc_prev_year2_sep'] = 0;
				$data['improved_sweet_and_spicy_bfc_prev_year2_oct'] = 0;
				$data['improved_sweet_and_spicy_bfc_prev_year2_nov'] = 0;
				$data['improved_sweet_and_spicy_bfc_prev_year2_dec'] = 0;
				$data['improved_sweet_and_spicy_bfc_prev_year2_avg'] = 0;
				$data['improved_sweet_and_spicy_bfc_prev_year2_min'] = 0;
				$data['improved_sweet_and_spicy_bfc_prev_year2_max'] = 0;

				foreach($improved_sweet_and_spicy_bfc_prev_year2 as $row){
					$data['improved_sweet_and_spicy_bfc_prev_year2_jan'] = $row->jan_price;
					$data['improved_sweet_and_spicy_bfc_prev_year2_feb'] = $row->feb_price;
					$data['improved_sweet_and_spicy_bfc_prev_year2_mar'] = $row->mar_price;
					$data['improved_sweet_and_spicy_bfc_prev_year2_apr'] = $row->apr_price;
					$data['improved_sweet_and_spicy_bfc_prev_year2_may'] = $row->may_price;
					$data['improved_sweet_and_spicy_bfc_prev_year2_jun'] = $row->jun_price;
					$data['improved_sweet_and_spicy_bfc_prev_year2_jul'] = $row->jul_price;
					$data['improved_sweet_and_spicy_bfc_prev_year2_aug'] = $row->aug_price;
					$data['improved_sweet_and_spicy_bfc_prev_year2_sep'] = $row->sep_price;
					$data['improved_sweet_and_spicy_bfc_prev_year2_oct'] = $row->oct_price;
					$data['improved_sweet_and_spicy_bfc_prev_year2_nov'] = $row->nov_price;
					$data['improved_sweet_and_spicy_bfc_prev_year2_dec'] = $row->dec_price;
					$data['improved_sweet_and_spicy_bfc_prev_year2_avg'] = ($row->jan_price + $row->feb_price + $row->mar_price + $row->apr_price + $row->may_price + $row->jun_price + $row->jul_price + $row->aug_price + $row->sep_price + $row->oct_price + $row->nov_price + $row->dec_price) / 12;

					$data['improved_sweet_and_spicy_bfc_prev_year2_min'] = min($row->jan_price, $row->feb_price, $row->mar_price, $row->apr_price, $row->may_price, $row->jun_price, $row->jul_price, $row->aug_price, $row->sep_price, $row->oct_price, $row->nov_price, $row->dec_price);

					$data['improved_sweet_and_spicy_bfc_prev_year2_max'] = max($row->jan_price, $row->feb_price, $row->mar_price, $row->apr_price, $row->may_price, $row->jun_price, $row->jul_price, $row->aug_price, $row->sep_price, $row->oct_price, $row->nov_price, $row->dec_price);
				}


				$improved_sweet_and_spicy_bfc_prev_year1 = $this->admin->get_data('comparative_price_tbl', array('comp_price_product' => 'BUTTERFLY', 'comp_price_segment' => 'REYAL', 'comp_price_year' => $year - 1,	'bc_id'	=>	$bc_id), 
					FALSE, 'AVG(comp_price_jan) as jan_price, AVG(comp_price_feb) as feb_price, AVG(comp_price_mar) as mar_price, AVG(comp_price_apr) as apr_price, AVG(comp_price_may) as may_price, AVG(comp_price_jun) as jun_price, AVG(comp_price_jul) as jul_price, AVG(comp_price_aug) as aug_price, AVG(comp_price_sep) as sep_price, AVG(comp_price_oct) as oct_price, AVG(comp_price_nov) as nov_price, AVG(comp_price_dec) as dec_price');
				
				$data['improved_sweet_and_spicy_bfc_prev_year1_jan'] = 0;
				$data['improved_sweet_and_spicy_bfc_prev_year1_feb'] = 0;
				$data['improved_sweet_and_spicy_bfc_prev_year1_mar'] = 0;
				$data['improved_sweet_and_spicy_bfc_prev_year1_apr'] = 0;
				$data['improved_sweet_and_spicy_bfc_prev_year1_may'] = 0;
				$data['improved_sweet_and_spicy_bfc_prev_year1_jun'] = 0;
				$data['improved_sweet_and_spicy_bfc_prev_year1_jul'] = 0;
				$data['improved_sweet_and_spicy_bfc_prev_year1_aug'] = 0;
				$data['improved_sweet_and_spicy_bfc_prev_year1_sep'] = 0;
				$data['improved_sweet_and_spicy_bfc_prev_year1_oct'] = 0;
				$data['improved_sweet_and_spicy_bfc_prev_year1_nov'] = 0;
				$data['improved_sweet_and_spicy_bfc_prev_year1_dec'] = 0;
				$data['improved_sweet_and_spicy_bfc_prev_year1_avg'] = 0;
				$data['improved_sweet_and_spicy_bfc_prev_year1_min'] = 0;
				$data['improved_sweet_and_spicy_bfc_prev_year1_max'] = 0;

				foreach($improved_sweet_and_spicy_bfc_prev_year1 as $row){
					$data['improved_sweet_and_spicy_bfc_prev_year1_jan'] = $row->jan_price;
					$data['improved_sweet_and_spicy_bfc_prev_year1_feb'] = $row->feb_price;
					$data['improved_sweet_and_spicy_bfc_prev_year1_mar'] = $row->mar_price;
					$data['improved_sweet_and_spicy_bfc_prev_year1_apr'] = $row->apr_price;
					$data['improved_sweet_and_spicy_bfc_prev_year1_may'] = $row->may_price;
					$data['improved_sweet_and_spicy_bfc_prev_year1_jun'] = $row->jun_price;
					$data['improved_sweet_and_spicy_bfc_prev_year1_jul'] = $row->jul_price;
					$data['improved_sweet_and_spicy_bfc_prev_year1_aug'] = $row->aug_price;
					$data['improved_sweet_and_spicy_bfc_prev_year1_sep'] = $row->sep_price;
					$data['improved_sweet_and_spicy_bfc_prev_year1_oct'] = $row->oct_price;
					$data['improved_sweet_and_spicy_bfc_prev_year1_nov'] = $row->nov_price;
					$data['improved_sweet_and_spicy_bfc_prev_year1_dec'] = $row->dec_price;
					$data['improved_sweet_and_spicy_bfc_prev_year1_avg'] = ($row->jan_price + $row->feb_price + $row->mar_price + $row->apr_price + $row->may_price + $row->jun_price + $row->jul_price + $row->aug_price + $row->sep_price + $row->oct_price + $row->nov_price + $row->dec_price) / 12;

					$data['improved_sweet_and_spicy_bfc_prev_year1_min'] = min($row->jan_price, $row->feb_price, $row->mar_price, $row->apr_price, $row->may_price, $row->jun_price, $row->jul_price, $row->aug_price, $row->sep_price, $row->oct_price, $row->nov_price, $row->dec_price);

					$data['improved_sweet_and_spicy_bfc_prev_year1_max'] = max($row->jan_price, $row->feb_price, $row->mar_price, $row->apr_price, $row->may_price, $row->jun_price, $row->jul_price, $row->aug_price, $row->sep_price, $row->oct_price, $row->nov_price, $row->dec_price);
				}

			}
		}

		/*REYAL Liempo*/
		$join_improved_sweet_and_spicy_liempo = array(
			'sales_item_tbl b' => 'a.sales_id = b.sales_id AND a.sales_status = 1 AND b.sales_item_status = 1 AND a.sales_year = ' . $year,
			'sales_details_tbl c' => 'b.sales_item_id = c.sales_item_id AND c.sales_det_status = 1',
			'material_tbl d' => 'b.material_id = d.material_id',
			'material_group_tbl e' => 'd.material_group_id = e.material_group_id AND e.material_group_name = "LIEMPO"',
			'outlet_tbl f' => 'a.outlet_id = f.outlet_id AND f.bc_id = ' . $bc_id,
			'outlet_brand_tbl g' => 'f.outlet_id = g.outlet_id AND g.outlet_brand_status = 1',
			'brand_tbl h' => 'g.brand_id  = h.brand_id AND h.brand_name = \'REYAL\''
		);

		$get_improved_sweet_and_spicy_liempo = $this->admin->get_join('sales_tbl a', $join_improved_sweet_and_spicy_liempo, FALSE, FALSE, 'YEAR(c.sales_det_date), MONTH(c.sales_det_date)', 'AVG(c.sales_det_asp) as asp, MONTH(c.sales_det_date) asp_date');

		$data['improved_sweet_and_spicy_liempo1'] = 0;
		$data['improved_sweet_and_spicy_liempo2'] = 0;
		$data['improved_sweet_and_spicy_liempo3'] = 0;
		$data['improved_sweet_and_spicy_liempo4'] = 0;
		$data['improved_sweet_and_spicy_liempo5'] = 0;
		$data['improved_sweet_and_spicy_liempo6'] = 0;
		$data['improved_sweet_and_spicy_liempo7'] = 0;
		$data['improved_sweet_and_spicy_liempo8'] = 0;
		$data['improved_sweet_and_spicy_liempo9'] = 0;
		$data['improved_sweet_and_spicy_liempo10'] = 0;
		$data['improved_sweet_and_spicy_liempo11'] = 0;
		$data['improved_sweet_and_spicy_liempo12'] = 0;
		$data['improved_sweet_and_spicy_liempo_total'] = 0;
		$data['improved_sweet_and_spicy_liempo_count'] = 0;
		$data['improved_sweet_and_spicy_liempo_avg'] = 0;
		$data['improved_sweet_and_spicy_liempo_min'] = 0;
		$data['improved_sweet_and_spicy_liempo_max'] = 0;

		foreach($get_improved_sweet_and_spicy_liempo as $row){
			$asp = $row->asp;
			$month = $row->asp_date;
			$data['improved_sweet_and_spicy_liempo' . $month] = $asp;
			$data['improved_sweet_and_spicy_liempo_total'] += $asp;

			if($asp < $data['improved_sweet_and_spicy_liempo_min'] || $data['improved_sweet_and_spicy_liempo_count'] == 0){
				$data['improved_sweet_and_spicy_liempo_min'] = $asp;
			}

			if($asp > $data['improved_sweet_and_spicy_liempo_max'] || $data['improved_sweet_and_spicy_liempo_count'] == 0){
				$data['improved_sweet_and_spicy_liempo_max'] = $asp;
			}

			$data['improved_sweet_and_spicy_liempo_count']++;
		}

		$data['improved_sweet_and_spicy_liempo_avg'] = $data['improved_sweet_and_spicy_liempo_total'] != 0 ? $data['improved_sweet_and_spicy_liempo_total'] / $data['improved_sweet_and_spicy_liempo_count'] : 0;

		$improved_sweet_and_spicy_liempo_prev_year2 = $this->admin->get_data('comparative_price_tbl', array('comp_price_product' => 'LIEMPO', 'comp_price_segment' => 'REYAL', 'comp_price_year' => $year - 2,	'bc_id'	=>	$bc_id), 
			FALSE, 'AVG(comp_price_jan) as jan_price, AVG(comp_price_feb) as feb_price, AVG(comp_price_mar) as mar_price, AVG(comp_price_apr) as apr_price, AVG(comp_price_may) as may_price, AVG(comp_price_jun) as jun_price, AVG(comp_price_jul) as jul_price, AVG(comp_price_aug) as aug_price, AVG(comp_price_sep) as sep_price, AVG(comp_price_oct) as oct_price, AVG(comp_price_nov) as nov_price, AVG(comp_price_dec) as dec_price');
		
		$data['improved_sweet_and_spicy_liempo_prev_year2_jan'] = 0;
		$data['improved_sweet_and_spicy_liempo_prev_year2_feb'] = 0;
		$data['improved_sweet_and_spicy_liempo_prev_year2_mar'] = 0;
		$data['improved_sweet_and_spicy_liempo_prev_year2_apr'] = 0;
		$data['improved_sweet_and_spicy_liempo_prev_year2_may'] = 0;
		$data['improved_sweet_and_spicy_liempo_prev_year2_jun'] = 0;
		$data['improved_sweet_and_spicy_liempo_prev_year2_jul'] = 0;
		$data['improved_sweet_and_spicy_liempo_prev_year2_aug'] = 0;
		$data['improved_sweet_and_spicy_liempo_prev_year2_sep'] = 0;
		$data['improved_sweet_and_spicy_liempo_prev_year2_oct'] = 0;
		$data['improved_sweet_and_spicy_liempo_prev_year2_nov'] = 0;
		$data['improved_sweet_and_spicy_liempo_prev_year2_dec'] = 0;
		$data['improved_sweet_and_spicy_liempo_prev_year2_avg'] = 0;
		$data['improved_sweet_and_spicy_liempo_prev_year2_min'] = 0;
		$data['improved_sweet_and_spicy_liempo_prev_year2_max'] = 0;

		foreach($improved_sweet_and_spicy_liempo_prev_year2 as $row){
			$data['improved_sweet_and_spicy_liempo_prev_year2_jan'] = $row->jan_price;
			$data['improved_sweet_and_spicy_liempo_prev_year2_feb'] = $row->feb_price;
			$data['improved_sweet_and_spicy_liempo_prev_year2_mar'] = $row->mar_price;
			$data['improved_sweet_and_spicy_liempo_prev_year2_apr'] = $row->apr_price;
			$data['improved_sweet_and_spicy_liempo_prev_year2_may'] = $row->may_price;
			$data['improved_sweet_and_spicy_liempo_prev_year2_jun'] = $row->jun_price;
			$data['improved_sweet_and_spicy_liempo_prev_year2_jul'] = $row->jul_price;
			$data['improved_sweet_and_spicy_liempo_prev_year2_aug'] = $row->aug_price;
			$data['improved_sweet_and_spicy_liempo_prev_year2_sep'] = $row->sep_price;
			$data['improved_sweet_and_spicy_liempo_prev_year2_oct'] = $row->oct_price;
			$data['improved_sweet_and_spicy_liempo_prev_year2_nov'] = $row->nov_price;
			$data['improved_sweet_and_spicy_liempo_prev_year2_dec'] = $row->dec_price;
			$data['improved_sweet_and_spicy_liempo_prev_year2_avg'] = ($row->jan_price + $row->feb_price + $row->mar_price + $row->apr_price + $row->may_price + $row->jun_price + $row->jul_price + $row->aug_price + $row->sep_price + $row->oct_price + $row->nov_price + $row->dec_price) / 12;

			$data['improved_sweet_and_spicy_liempo_prev_year2_min'] = min($row->jan_price, $row->feb_price, $row->mar_price, $row->apr_price, $row->may_price, $row->jun_price, $row->jul_price, $row->aug_price, $row->sep_price, $row->oct_price, $row->nov_price, $row->dec_price);

			$data['improved_sweet_and_spicy_liempo_prev_year2_max'] = max($row->jan_price, $row->feb_price, $row->mar_price, $row->apr_price, $row->may_price, $row->jun_price, $row->jul_price, $row->aug_price, $row->sep_price, $row->oct_price, $row->nov_price, $row->dec_price);
		}


		$improved_sweet_and_spicy_liempo_prev_year1 = $this->admin->get_data('comparative_price_tbl', array('comp_price_product' => 'LIEMPO', 'comp_price_segment' => 'REYAL', 'comp_price_year' => $year - 1,	'bc_id'	=>	$bc_id), 
			FALSE, 'AVG(comp_price_jan) as jan_price, AVG(comp_price_feb) as feb_price, AVG(comp_price_mar) as mar_price, AVG(comp_price_apr) as apr_price, AVG(comp_price_may) as may_price, AVG(comp_price_jun) as jun_price, AVG(comp_price_jul) as jul_price, AVG(comp_price_aug) as aug_price, AVG(comp_price_sep) as sep_price, AVG(comp_price_oct) as oct_price, AVG(comp_price_nov) as nov_price, AVG(comp_price_dec) as dec_price');
		
		$data['improved_sweet_and_spicy_liempo_prev_year1_jan'] = 0;
		$data['improved_sweet_and_spicy_liempo_prev_year1_feb'] = 0;
		$data['improved_sweet_and_spicy_liempo_prev_year1_mar'] = 0;
		$data['improved_sweet_and_spicy_liempo_prev_year1_apr'] = 0;
		$data['improved_sweet_and_spicy_liempo_prev_year1_may'] = 0;
		$data['improved_sweet_and_spicy_liempo_prev_year1_jun'] = 0;
		$data['improved_sweet_and_spicy_liempo_prev_year1_jul'] = 0;
		$data['improved_sweet_and_spicy_liempo_prev_year1_aug'] = 0;
		$data['improved_sweet_and_spicy_liempo_prev_year1_sep'] = 0;
		$data['improved_sweet_and_spicy_liempo_prev_year1_oct'] = 0;
		$data['improved_sweet_and_spicy_liempo_prev_year1_nov'] = 0;
		$data['improved_sweet_and_spicy_liempo_prev_year1_dec'] = 0;
		$data['improved_sweet_and_spicy_liempo_prev_year1_avg'] = 0;
		$data['improved_sweet_and_spicy_liempo_prev_year1_min'] = 0;
		$data['improved_sweet_and_spicy_liempo_prev_year1_max'] = 0;

		foreach($improved_sweet_and_spicy_liempo_prev_year1 as $row){
			$data['improved_sweet_and_spicy_liempo_prev_year1_jan'] = $row->jan_price;
			$data['improved_sweet_and_spicy_liempo_prev_year1_feb'] = $row->feb_price;
			$data['improved_sweet_and_spicy_liempo_prev_year1_mar'] = $row->mar_price;
			$data['improved_sweet_and_spicy_liempo_prev_year1_apr'] = $row->apr_price;
			$data['improved_sweet_and_spicy_liempo_prev_year1_may'] = $row->may_price;
			$data['improved_sweet_and_spicy_liempo_prev_year1_jun'] = $row->jun_price;
			$data['improved_sweet_and_spicy_liempo_prev_year1_jul'] = $row->jul_price;
			$data['improved_sweet_and_spicy_liempo_prev_year1_aug'] = $row->aug_price;
			$data['improved_sweet_and_spicy_liempo_prev_year1_sep'] = $row->sep_price;
			$data['improved_sweet_and_spicy_liempo_prev_year1_oct'] = $row->oct_price;
			$data['improved_sweet_and_spicy_liempo_prev_year1_nov'] = $row->nov_price;
			$data['improved_sweet_and_spicy_liempo_prev_year1_dec'] = $row->dec_price;
			$data['improved_sweet_and_spicy_liempo_prev_year1_avg'] = ($row->jan_price + $row->feb_price + $row->mar_price + $row->apr_price + $row->may_price + $row->jun_price + $row->jul_price + $row->aug_price + $row->sep_price + $row->oct_price + $row->nov_price + $row->dec_price) / 12;

			$data['improved_sweet_and_spicy_liempo_prev_year1_min'] = min($row->jan_price, $row->feb_price, $row->mar_price, $row->apr_price, $row->may_price, $row->jun_price, $row->jul_price, $row->aug_price, $row->sep_price, $row->oct_price, $row->nov_price, $row->dec_price);

			$data['improved_sweet_and_spicy_liempo_prev_year1_max'] = max($row->jan_price, $row->feb_price, $row->mar_price, $row->apr_price, $row->may_price, $row->jun_price, $row->jul_price, $row->aug_price, $row->sep_price, $row->oct_price, $row->nov_price, $row->dec_price);
		}
		
		/*REYAL Dressed*/
		$join_improved_sweet_and_spicy_dressed = array(
			'sales_item_tbl b' => 'a.sales_id = b.sales_id AND a.sales_status = 1 AND b.sales_item_status = 1 AND a.sales_year = ' . $year,
			'sales_details_tbl c' => 'b.sales_item_id = c.sales_item_id AND c.sales_det_status = 1',
			'material_tbl d' => 'b.material_id = d.material_id',
			'material_group_tbl e' => 'd.material_group_id = e.material_group_id AND e.material_group_name = "DRESSED"',
			'outlet_tbl f' => 'a.outlet_id = f.outlet_id AND f.bc_id = ' . $bc_id,
			'outlet_brand_tbl g' => 'f.outlet_id = g.outlet_id AND g.outlet_brand_status = 1',
			'brand_tbl h' => 'g.brand_id  = h.brand_id AND h.brand_name = \'REYAL\''
		);

		$get_improved_sweet_and_spicy_dressed = $this->admin->get_join('sales_tbl a', $join_improved_sweet_and_spicy_dressed, FALSE, FALSE, 'YEAR(c.sales_det_date), MONTH(c.sales_det_date)', 'AVG(c.sales_det_asp) as asp, MONTH(c.sales_det_date) asp_date');

		$data['improved_sweet_and_spicy_dressed1'] = 0;
		$data['improved_sweet_and_spicy_dressed2'] = 0;
		$data['improved_sweet_and_spicy_dressed3'] = 0;
		$data['improved_sweet_and_spicy_dressed4'] = 0;
		$data['improved_sweet_and_spicy_dressed5'] = 0;
		$data['improved_sweet_and_spicy_dressed6'] = 0;
		$data['improved_sweet_and_spicy_dressed7'] = 0;
		$data['improved_sweet_and_spicy_dressed8'] = 0;
		$data['improved_sweet_and_spicy_dressed9'] = 0;
		$data['improved_sweet_and_spicy_dressed10'] = 0;
		$data['improved_sweet_and_spicy_dressed11'] = 0;
		$data['improved_sweet_and_spicy_dressed12'] = 0;
		$data['improved_sweet_and_spicy_dressed_total'] = 0;
		$data['improved_sweet_and_spicy_dressed_count'] = 0;
		$data['improved_sweet_and_spicy_dressed_avg'] = 0;
		$data['improved_sweet_and_spicy_dressed_min'] = 0;
		$data['improved_sweet_and_spicy_dressed_max'] = 0;

		foreach($get_improved_sweet_and_spicy_dressed as $row){
			$asp = $row->asp;
			$month = $row->asp_date;
			$data['improved_sweet_and_spicy_dressed' . $month] = $asp;
			$data['improved_sweet_and_spicy_dressed_total'] += $asp;

			if($asp < $data['improved_sweet_and_spicy_dressed_min'] || $data['improved_sweet_and_spicy_dressed_count'] == 0){
				$data['improved_sweet_and_spicy_dressed_min'] = $asp;
			}

			if($asp > $data['improved_sweet_and_spicy_dressed_max'] || $data['improved_sweet_and_spicy_dressed_count'] == 0){
				$data['improved_sweet_and_spicy_dressed_max'] = $asp;
			}

			$data['improved_sweet_and_spicy_dressed_count']++;
		}

		$data['improved_sweet_and_spicy_dressed_avg'] = $data['improved_sweet_and_spicy_dressed_total'] != 0 ? $data['improved_sweet_and_spicy_dressed_total'] / $data['improved_sweet_and_spicy_dressed_count'] : 0;

		$improved_sweet_and_spicy_dressed_prev_year2 = $this->admin->get_data('comparative_price_tbl', array('comp_price_product' => 'DRESSED', 'comp_price_segment' => 'REYAL', 'comp_price_year' => $year - 2,	'bc_id'	=>	$bc_id), 
			FALSE, 'AVG(comp_price_jan) as jan_price, AVG(comp_price_feb) as feb_price, AVG(comp_price_mar) as mar_price, AVG(comp_price_apr) as apr_price, AVG(comp_price_may) as may_price, AVG(comp_price_jun) as jun_price, AVG(comp_price_jul) as jul_price, AVG(comp_price_aug) as aug_price, AVG(comp_price_sep) as sep_price, AVG(comp_price_oct) as oct_price, AVG(comp_price_nov) as nov_price, AVG(comp_price_dec) as dec_price');
		
		$data['improved_sweet_and_spicy_dressed_prev_year2_jan'] = 0;
		$data['improved_sweet_and_spicy_dressed_prev_year2_feb'] = 0;
		$data['improved_sweet_and_spicy_dressed_prev_year2_mar'] = 0;
		$data['improved_sweet_and_spicy_dressed_prev_year2_apr'] = 0;
		$data['improved_sweet_and_spicy_dressed_prev_year2_may'] = 0;
		$data['improved_sweet_and_spicy_dressed_prev_year2_jun'] = 0;
		$data['improved_sweet_and_spicy_dressed_prev_year2_jul'] = 0;
		$data['improved_sweet_and_spicy_dressed_prev_year2_aug'] = 0;
		$data['improved_sweet_and_spicy_dressed_prev_year2_sep'] = 0;
		$data['improved_sweet_and_spicy_dressed_prev_year2_oct'] = 0;
		$data['improved_sweet_and_spicy_dressed_prev_year2_nov'] = 0;
		$data['improved_sweet_and_spicy_dressed_prev_year2_dec'] = 0;
		$data['improved_sweet_and_spicy_dressed_prev_year2_avg'] = 0;
		$data['improved_sweet_and_spicy_dressed_prev_year2_min'] = 0;
		$data['improved_sweet_and_spicy_dressed_prev_year2_max'] = 0;

		foreach($improved_sweet_and_spicy_dressed_prev_year2 as $row){
			$data['improved_sweet_and_spicy_dressed_prev_year2_jan'] = $row->jan_price;
			$data['improved_sweet_and_spicy_dressed_prev_year2_feb'] = $row->feb_price;
			$data['improved_sweet_and_spicy_dressed_prev_year2_mar'] = $row->mar_price;
			$data['improved_sweet_and_spicy_dressed_prev_year2_apr'] = $row->apr_price;
			$data['improved_sweet_and_spicy_dressed_prev_year2_may'] = $row->may_price;
			$data['improved_sweet_and_spicy_dressed_prev_year2_jun'] = $row->jun_price;
			$data['improved_sweet_and_spicy_dressed_prev_year2_jul'] = $row->jul_price;
			$data['improved_sweet_and_spicy_dressed_prev_year2_aug'] = $row->aug_price;
			$data['improved_sweet_and_spicy_dressed_prev_year2_sep'] = $row->sep_price;
			$data['improved_sweet_and_spicy_dressed_prev_year2_oct'] = $row->oct_price;
			$data['improved_sweet_and_spicy_dressed_prev_year2_nov'] = $row->nov_price;
			$data['improved_sweet_and_spicy_dressed_prev_year2_dec'] = $row->dec_price;
			$data['improved_sweet_and_spicy_dressed_prev_year2_avg'] = ($row->jan_price + $row->feb_price + $row->mar_price + $row->apr_price + $row->may_price + $row->jun_price + $row->jul_price + $row->aug_price + $row->sep_price + $row->oct_price + $row->nov_price + $row->dec_price) / 12;

			$data['improved_sweet_and_spicy_dressed_prev_year2_min'] = min($row->jan_price, $row->feb_price, $row->mar_price, $row->apr_price, $row->may_price, $row->jun_price, $row->jul_price, $row->aug_price, $row->sep_price, $row->oct_price, $row->nov_price, $row->dec_price);

			$data['improved_sweet_and_spicy_dressed_prev_year2_max'] = max($row->jan_price, $row->feb_price, $row->mar_price, $row->apr_price, $row->may_price, $row->jun_price, $row->jul_price, $row->aug_price, $row->sep_price, $row->oct_price, $row->nov_price, $row->dec_price);
		}


		$improved_sweet_and_spicy_dressed_prev_year1 = $this->admin->get_data('comparative_price_tbl', array('comp_price_product' => 'DRESSED', 'comp_price_segment' => 'REYAL', 'comp_price_year' => $year - 1,	'bc_id'	=>	$bc_id), 
			FALSE, 'AVG(comp_price_jan) as jan_price, AVG(comp_price_feb) as feb_price, AVG(comp_price_mar) as mar_price, AVG(comp_price_apr) as apr_price, AVG(comp_price_may) as may_price, AVG(comp_price_jun) as jun_price, AVG(comp_price_jul) as jul_price, AVG(comp_price_aug) as aug_price, AVG(comp_price_sep) as sep_price, AVG(comp_price_oct) as oct_price, AVG(comp_price_nov) as nov_price, AVG(comp_price_dec) as dec_price');
		
		$data['improved_sweet_and_spicy_dressed_prev_year1_jan'] = 0;
		$data['improved_sweet_and_spicy_dressed_prev_year1_feb'] = 0;
		$data['improved_sweet_and_spicy_dressed_prev_year1_mar'] = 0;
		$data['improved_sweet_and_spicy_dressed_prev_year1_apr'] = 0;
		$data['improved_sweet_and_spicy_dressed_prev_year1_may'] = 0;
		$data['improved_sweet_and_spicy_dressed_prev_year1_jun'] = 0;
		$data['improved_sweet_and_spicy_dressed_prev_year1_jul'] = 0;
		$data['improved_sweet_and_spicy_dressed_prev_year1_aug'] = 0;
		$data['improved_sweet_and_spicy_dressed_prev_year1_sep'] = 0;
		$data['improved_sweet_and_spicy_dressed_prev_year1_oct'] = 0;
		$data['improved_sweet_and_spicy_dressed_prev_year1_nov'] = 0;
		$data['improved_sweet_and_spicy_dressed_prev_year1_dec'] = 0;
		$data['improved_sweet_and_spicy_dressed_prev_year1_avg'] = 0;
		$data['improved_sweet_and_spicy_dressed_prev_year1_min'] = 0;
		$data['improved_sweet_and_spicy_dressed_prev_year1_max'] = 0;

		foreach($improved_sweet_and_spicy_dressed_prev_year1 as $row){
			$data['improved_sweet_and_spicy_dressed_prev_year1_jan'] = $row->jan_price;
			$data['improved_sweet_and_spicy_dressed_prev_year1_feb'] = $row->feb_price;
			$data['improved_sweet_and_spicy_dressed_prev_year1_mar'] = $row->mar_price;
			$data['improved_sweet_and_spicy_dressed_prev_year1_apr'] = $row->apr_price;
			$data['improved_sweet_and_spicy_dressed_prev_year1_may'] = $row->may_price;
			$data['improved_sweet_and_spicy_dressed_prev_year1_jun'] = $row->jun_price;
			$data['improved_sweet_and_spicy_dressed_prev_year1_jul'] = $row->jul_price;
			$data['improved_sweet_and_spicy_dressed_prev_year1_aug'] = $row->aug_price;
			$data['improved_sweet_and_spicy_dressed_prev_year1_sep'] = $row->sep_price;
			$data['improved_sweet_and_spicy_dressed_prev_year1_oct'] = $row->oct_price;
			$data['improved_sweet_and_spicy_dressed_prev_year1_nov'] = $row->nov_price;
			$data['improved_sweet_and_spicy_dressed_prev_year1_dec'] = $row->dec_price;
			$data['improved_sweet_and_spicy_dressed_prev_year1_avg'] = ($row->jan_price + $row->feb_price + $row->mar_price + $row->apr_price + $row->may_price + $row->jun_price + $row->jul_price + $row->aug_price + $row->sep_price + $row->oct_price + $row->nov_price + $row->dec_price) / 12;

			$data['improved_sweet_and_spicy_dressed_prev_year1_min'] = min($row->jan_price, $row->feb_price, $row->mar_price, $row->apr_price, $row->may_price, $row->jun_price, $row->jul_price, $row->aug_price, $row->sep_price, $row->oct_price, $row->nov_price, $row->dec_price);

			$data['improved_sweet_and_spicy_dressed_prev_year1_max'] = max($row->jan_price, $row->feb_price, $row->mar_price, $row->apr_price, $row->may_price, $row->jun_price, $row->jul_price, $row->aug_price, $row->sep_price, $row->oct_price, $row->nov_price, $row->dec_price);
		}

		/*REYAL Chooksies*/
		$join_improved_sweet_and_spicy_chooksies = array(
			'sales_item_tbl b' => 'a.sales_id = b.sales_id AND a.sales_status = 1 AND b.sales_item_status = 1 AND a.sales_year = ' . $year,
			'sales_details_tbl c' => 'b.sales_item_id = c.sales_item_id AND c.sales_det_status = 1',
			'material_tbl d' => 'b.material_id = d.material_id AND (d.material_code = 10200095 OR d.material_code = 10200096 OR d.material_code = 10200097)',
			'material_group_tbl e' => 'd.material_group_id = e.material_group_id AND e.material_group_name = \'MARINATED CUT UPS\'',
			'outlet_tbl f' => 'a.outlet_id = f.outlet_id AND f.bc_id = ' . $bc_id,
			'outlet_brand_tbl g' => 'f.outlet_id = g.outlet_id AND g.outlet_brand_status = 1',
			'brand_tbl h' => 'g.brand_id  = h.brand_id AND h.brand_name = \'REYAL\''
		);

		$get_improved_sweet_and_spicy_chooksies = $this->admin->get_join('sales_tbl a', $join_improved_sweet_and_spicy_chooksies, FALSE, FALSE, 'YEAR(c.sales_det_date), MONTH(c.sales_det_date)', 'AVG(c.sales_det_asp) as asp, MONTH(c.sales_det_date) asp_date');

		$data['improved_sweet_and_spicy_chooksies1'] = 0;
		$data['improved_sweet_and_spicy_chooksies2'] = 0;
		$data['improved_sweet_and_spicy_chooksies3'] = 0;
		$data['improved_sweet_and_spicy_chooksies4'] = 0;
		$data['improved_sweet_and_spicy_chooksies5'] = 0;
		$data['improved_sweet_and_spicy_chooksies6'] = 0;
		$data['improved_sweet_and_spicy_chooksies7'] = 0;
		$data['improved_sweet_and_spicy_chooksies8'] = 0;
		$data['improved_sweet_and_spicy_chooksies9'] = 0;
		$data['improved_sweet_and_spicy_chooksies10'] = 0;
		$data['improved_sweet_and_spicy_chooksies11'] = 0;
		$data['improved_sweet_and_spicy_chooksies12'] = 0;
		$data['improved_sweet_and_spicy_chooksies_total'] = 0;
		$data['improved_sweet_and_spicy_chooksies_count'] = 0;
		$data['improved_sweet_and_spicy_chooksies_avg'] = 0;
		$data['improved_sweet_and_spicy_chooksies_min'] = 0;
		$data['improved_sweet_and_spicy_chooksies_max'] = 0;

		foreach($get_improved_sweet_and_spicy_chooksies as $row){
			$asp = $row->asp;
			$month = $row->asp_date;
			$data['improved_sweet_and_spicy_chooksies' . $month] = $asp;
			$data['improved_sweet_and_spicy_chooksies_total'] += $asp;

			if($asp < $data['improved_sweet_and_spicy_chooksies_min'] || $data['improved_sweet_and_spicy_chooksies_count'] == 0){
				$data['improved_sweet_and_spicy_chooksies_min'] = $asp;
			}

			if($asp > $data['improved_sweet_and_spicy_chooksies_max'] || $data['improved_sweet_and_spicy_chooksies_count'] == 0){
				$data['improved_sweet_and_spicy_chooksies_max'] = $asp;
			}

			$data['improved_sweet_and_spicy_chooksies_count']++;
		}

		$data['improved_sweet_and_spicy_chooksies_avg'] = $data['improved_sweet_and_spicy_chooksies_total'] != 0 ? $data['improved_sweet_and_spicy_chooksies_total'] / $data['improved_sweet_and_spicy_chooksies_count'] : 0;


		$improved_sweet_and_spicy_chooksies_prev_year2 = $this->admin->get_data('comparative_price_tbl', array('comp_price_product' => 'CHOOKSIES MARINADO', 'comp_price_segment' => 'REYAL', 'comp_price_year' => $year - 2,	'bc_id'	=>	$bc_id), 
			FALSE, 'AVG(comp_price_jan) as jan_price, AVG(comp_price_feb) as feb_price, AVG(comp_price_mar) as mar_price, AVG(comp_price_apr) as apr_price, AVG(comp_price_may) as may_price, AVG(comp_price_jun) as jun_price, AVG(comp_price_jul) as jul_price, AVG(comp_price_aug) as aug_price, AVG(comp_price_sep) as sep_price, AVG(comp_price_oct) as oct_price, AVG(comp_price_nov) as nov_price, AVG(comp_price_dec) as dec_price');
		
		$data['improved_sweet_and_spicy_chooksies_prev_year2_jan'] = 0;
		$data['improved_sweet_and_spicy_chooksies_prev_year2_feb'] = 0;
		$data['improved_sweet_and_spicy_chooksies_prev_year2_mar'] = 0;
		$data['improved_sweet_and_spicy_chooksies_prev_year2_apr'] = 0;
		$data['improved_sweet_and_spicy_chooksies_prev_year2_may'] = 0;
		$data['improved_sweet_and_spicy_chooksies_prev_year2_jun'] = 0;
		$data['improved_sweet_and_spicy_chooksies_prev_year2_jul'] = 0;
		$data['improved_sweet_and_spicy_chooksies_prev_year2_aug'] = 0;
		$data['improved_sweet_and_spicy_chooksies_prev_year2_sep'] = 0;
		$data['improved_sweet_and_spicy_chooksies_prev_year2_oct'] = 0;
		$data['improved_sweet_and_spicy_chooksies_prev_year2_nov'] = 0;
		$data['improved_sweet_and_spicy_chooksies_prev_year2_dec'] = 0;
		$data['improved_sweet_and_spicy_chooksies_prev_year2_avg'] = 0;
		$data['improved_sweet_and_spicy_chooksies_prev_year2_min'] = 0;
		$data['improved_sweet_and_spicy_chooksies_prev_year2_max'] = 0;

		foreach($improved_sweet_and_spicy_chooksies_prev_year2 as $row){
			$data['improved_sweet_and_spicy_chooksies_prev_year2_jan'] = $row->jan_price;
			$data['improved_sweet_and_spicy_chooksies_prev_year2_feb'] = $row->feb_price;
			$data['improved_sweet_and_spicy_chooksies_prev_year2_mar'] = $row->mar_price;
			$data['improved_sweet_and_spicy_chooksies_prev_year2_apr'] = $row->apr_price;
			$data['improved_sweet_and_spicy_chooksies_prev_year2_may'] = $row->may_price;
			$data['improved_sweet_and_spicy_chooksies_prev_year2_jun'] = $row->jun_price;
			$data['improved_sweet_and_spicy_chooksies_prev_year2_jul'] = $row->jul_price;
			$data['improved_sweet_and_spicy_chooksies_prev_year2_aug'] = $row->aug_price;
			$data['improved_sweet_and_spicy_chooksies_prev_year2_sep'] = $row->sep_price;
			$data['improved_sweet_and_spicy_chooksies_prev_year2_oct'] = $row->oct_price;
			$data['improved_sweet_and_spicy_chooksies_prev_year2_nov'] = $row->nov_price;
			$data['improved_sweet_and_spicy_chooksies_prev_year2_dec'] = $row->dec_price;
			$data['improved_sweet_and_spicy_chooksies_prev_year2_avg'] = ($row->jan_price + $row->feb_price + $row->mar_price + $row->apr_price + $row->may_price + $row->jun_price + $row->jul_price + $row->aug_price + $row->sep_price + $row->oct_price + $row->nov_price + $row->dec_price) / 12;

			$data['improved_sweet_and_spicy_chooksies_prev_year2_min'] = min($row->jan_price, $row->feb_price, $row->mar_price, $row->apr_price, $row->may_price, $row->jun_price, $row->jul_price, $row->aug_price, $row->sep_price, $row->oct_price, $row->nov_price, $row->dec_price);

			$data['improved_sweet_and_spicy_chooksies_prev_year2_max'] = max($row->jan_price, $row->feb_price, $row->mar_price, $row->apr_price, $row->may_price, $row->jun_price, $row->jul_price, $row->aug_price, $row->sep_price, $row->oct_price, $row->nov_price, $row->dec_price);
		}


		$improved_sweet_and_spicy_chooksies_prev_year1 = $this->admin->get_data('comparative_price_tbl', array('comp_price_product' => 'CHOOKSIES MARINADO', 'comp_price_segment' => 'REYAL', 'comp_price_year' => $year - 1,	'bc_id'	=>	$bc_id), 
			FALSE, 'AVG(comp_price_jan) as jan_price, AVG(comp_price_feb) as feb_price, AVG(comp_price_mar) as mar_price, AVG(comp_price_apr) as apr_price, AVG(comp_price_may) as may_price, AVG(comp_price_jun) as jun_price, AVG(comp_price_jul) as jul_price, AVG(comp_price_aug) as aug_price, AVG(comp_price_sep) as sep_price, AVG(comp_price_oct) as oct_price, AVG(comp_price_nov) as nov_price, AVG(comp_price_dec) as dec_price');
		
		$data['improved_sweet_and_spicy_chooksies_prev_year1_jan'] = 0;
		$data['improved_sweet_and_spicy_chooksies_prev_year1_feb'] = 0;
		$data['improved_sweet_and_spicy_chooksies_prev_year1_mar'] = 0;
		$data['improved_sweet_and_spicy_chooksies_prev_year1_apr'] = 0;
		$data['improved_sweet_and_spicy_chooksies_prev_year1_may'] = 0;
		$data['improved_sweet_and_spicy_chooksies_prev_year1_jun'] = 0;
		$data['improved_sweet_and_spicy_chooksies_prev_year1_jul'] = 0;
		$data['improved_sweet_and_spicy_chooksies_prev_year1_aug'] = 0;
		$data['improved_sweet_and_spicy_chooksies_prev_year1_sep'] = 0;
		$data['improved_sweet_and_spicy_chooksies_prev_year1_oct'] = 0;
		$data['improved_sweet_and_spicy_chooksies_prev_year1_nov'] = 0;
		$data['improved_sweet_and_spicy_chooksies_prev_year1_dec'] = 0;
		$data['improved_sweet_and_spicy_chooksies_prev_year1_avg'] = 0;
		$data['improved_sweet_and_spicy_chooksies_prev_year1_min'] = 0;
		$data['improved_sweet_and_spicy_chooksies_prev_year1_max'] = 0;

		foreach($improved_sweet_and_spicy_chooksies_prev_year1 as $row){
			$data['improved_sweet_and_spicy_chooksies_prev_year1_jan'] = $row->jan_price;
			$data['improved_sweet_and_spicy_chooksies_prev_year1_feb'] = $row->feb_price;
			$data['improved_sweet_and_spicy_chooksies_prev_year1_mar'] = $row->mar_price;
			$data['improved_sweet_and_spicy_chooksies_prev_year1_apr'] = $row->apr_price;
			$data['improved_sweet_and_spicy_chooksies_prev_year1_may'] = $row->may_price;
			$data['improved_sweet_and_spicy_chooksies_prev_year1_jun'] = $row->jun_price;
			$data['improved_sweet_and_spicy_chooksies_prev_year1_jul'] = $row->jul_price;
			$data['improved_sweet_and_spicy_chooksies_prev_year1_aug'] = $row->aug_price;
			$data['improved_sweet_and_spicy_chooksies_prev_year1_sep'] = $row->sep_price;
			$data['improved_sweet_and_spicy_chooksies_prev_year1_oct'] = $row->oct_price;
			$data['improved_sweet_and_spicy_chooksies_prev_year1_nov'] = $row->nov_price;
			$data['improved_sweet_and_spicy_chooksies_prev_year1_dec'] = $row->dec_price;
			$data['improved_sweet_and_spicy_chooksies_prev_year1_avg'] = ($row->jan_price + $row->feb_price + $row->mar_price + $row->apr_price + $row->may_price + $row->jun_price + $row->jul_price + $row->aug_price + $row->sep_price + $row->oct_price + $row->nov_price + $row->dec_price) / 12;

			$data['improved_sweet_and_spicy_chooksies_prev_year1_min'] = min($row->jan_price, $row->feb_price, $row->mar_price, $row->apr_price, $row->may_price, $row->jun_price, $row->jul_price, $row->aug_price, $row->sep_price, $row->oct_price, $row->nov_price, $row->dec_price);

			$data['improved_sweet_and_spicy_chooksies_prev_year1_max'] = max($row->jan_price, $row->feb_price, $row->mar_price, $row->apr_price, $row->may_price, $row->jun_price, $row->jul_price, $row->aug_price, $row->sep_price, $row->oct_price, $row->nov_price, $row->dec_price);
		}


		/*REYAL Marinado*/
		$join_improved_sweet_and_spicy_marinado = array(
			'sales_item_tbl b' => 'a.sales_id = b.sales_id AND a.sales_status = 1 AND b.sales_item_status = 1 AND a.sales_year = ' . $year,
			'sales_details_tbl c' => 'b.sales_item_id = c.sales_item_id AND c.sales_det_status = 1',
			'material_tbl d' => 'b.material_id = d.material_id AND d.material_code = 1000638',
			'material_group_tbl e' => 'd.material_group_id = e.material_group_id AND e.material_group_name = \'MARINATED CUT UPS\'',
			'outlet_tbl f' => 'a.outlet_id = f.outlet_id AND f.bc_id = ' . $bc_id,
			'outlet_brand_tbl g' => 'f.outlet_id = g.outlet_id AND g.outlet_brand_status = 1',
			'brand_tbl h' => 'g.brand_id  = h.brand_id AND h.brand_name = \'REYAL\''
		);

		$get_improved_sweet_and_spicy_marinado = $this->admin->get_join('sales_tbl a', $join_improved_sweet_and_spicy_marinado, FALSE, FALSE, 'YEAR(c.sales_det_date), MONTH(c.sales_det_date)', 'AVG(c.sales_det_asp) as asp, MONTH(c.sales_det_date) asp_date');

		$data['improved_sweet_and_spicy_marinado1'] = 0;
		$data['improved_sweet_and_spicy_marinado2'] = 0;
		$data['improved_sweet_and_spicy_marinado3'] = 0;
		$data['improved_sweet_and_spicy_marinado4'] = 0;
		$data['improved_sweet_and_spicy_marinado5'] = 0;
		$data['improved_sweet_and_spicy_marinado6'] = 0;
		$data['improved_sweet_and_spicy_marinado7'] = 0;
		$data['improved_sweet_and_spicy_marinado8'] = 0;
		$data['improved_sweet_and_spicy_marinado9'] = 0;
		$data['improved_sweet_and_spicy_marinado10'] = 0;
		$data['improved_sweet_and_spicy_marinado11'] = 0;
		$data['improved_sweet_and_spicy_marinado12'] = 0;
		$data['improved_sweet_and_spicy_marinado_total'] = 0;
		$data['improved_sweet_and_spicy_marinado_count'] = 0;
		$data['improved_sweet_and_spicy_marinado_avg'] = 0;
		$data['improved_sweet_and_spicy_marinado_min'] = 0;
		$data['improved_sweet_and_spicy_marinado_max'] = 0;

		foreach($get_improved_sweet_and_spicy_marinado as $row){
			$asp = $row->asp;
			$month = $row->asp_date;
			$data['improved_sweet_and_spicy_marinado' . $month] = $asp;
			$data['improved_sweet_and_spicy_marinado_total'] += $asp;

			if($asp < $data['improved_sweet_and_spicy_marinado_min'] || $data['improved_sweet_and_spicy_marinado_count'] == 0){
				$data['improved_sweet_and_spicy_marinado_min'] = $asp;
			}

			if($asp > $data['improved_sweet_and_spicy_marinado_max'] || $data['improved_sweet_and_spicy_marinado_count'] == 0){
				$data['improved_sweet_and_spicy_marinado_max'] = $asp;
			}

			$data['improved_sweet_and_spicy_marinado_count']++;
		}

		$data['improved_sweet_and_spicy_marinado_avg'] = $data['improved_sweet_and_spicy_marinado_total'] != 0 ? $data['improved_sweet_and_spicy_marinado_total'] / $data['improved_sweet_and_spicy_marinado_count'] : 0;

		$improved_sweet_and_spicy_marinado_prev_year2 = $this->admin->get_data('comparative_price_tbl', array('comp_price_product' => 'MARINADO FRIED', 'comp_price_segment' => 'REYAL', 'comp_price_year' => $year - 2,	'bc_id'	=>	$bc_id), 
			FALSE, 'AVG(comp_price_jan) as jan_price, AVG(comp_price_feb) as feb_price, AVG(comp_price_mar) as mar_price, AVG(comp_price_apr) as apr_price, AVG(comp_price_may) as may_price, AVG(comp_price_jun) as jun_price, AVG(comp_price_jul) as jul_price, AVG(comp_price_aug) as aug_price, AVG(comp_price_sep) as sep_price, AVG(comp_price_oct) as oct_price, AVG(comp_price_nov) as nov_price, AVG(comp_price_dec) as dec_price');
		
		$data['improved_sweet_and_spicy_marinado_prev_year2_jan'] = 0;
		$data['improved_sweet_and_spicy_marinado_prev_year2_feb'] = 0;
		$data['improved_sweet_and_spicy_marinado_prev_year2_mar'] = 0;
		$data['improved_sweet_and_spicy_marinado_prev_year2_apr'] = 0;
		$data['improved_sweet_and_spicy_marinado_prev_year2_may'] = 0;
		$data['improved_sweet_and_spicy_marinado_prev_year2_jun'] = 0;
		$data['improved_sweet_and_spicy_marinado_prev_year2_jul'] = 0;
		$data['improved_sweet_and_spicy_marinado_prev_year2_aug'] = 0;
		$data['improved_sweet_and_spicy_marinado_prev_year2_sep'] = 0;
		$data['improved_sweet_and_spicy_marinado_prev_year2_oct'] = 0;
		$data['improved_sweet_and_spicy_marinado_prev_year2_nov'] = 0;
		$data['improved_sweet_and_spicy_marinado_prev_year2_dec'] = 0;
		$data['improved_sweet_and_spicy_marinado_prev_year2_avg'] = 0;
		$data['improved_sweet_and_spicy_marinado_prev_year2_min'] = 0;
		$data['improved_sweet_and_spicy_marinado_prev_year2_max'] = 0;

		foreach($improved_sweet_and_spicy_marinado_prev_year2 as $row){
			$data['improved_sweet_and_spicy_marinado_prev_year2_jan'] = $row->jan_price;
			$data['improved_sweet_and_spicy_marinado_prev_year2_feb'] = $row->feb_price;
			$data['improved_sweet_and_spicy_marinado_prev_year2_mar'] = $row->mar_price;
			$data['improved_sweet_and_spicy_marinado_prev_year2_apr'] = $row->apr_price;
			$data['improved_sweet_and_spicy_marinado_prev_year2_may'] = $row->may_price;
			$data['improved_sweet_and_spicy_marinado_prev_year2_jun'] = $row->jun_price;
			$data['improved_sweet_and_spicy_marinado_prev_year2_jul'] = $row->jul_price;
			$data['improved_sweet_and_spicy_marinado_prev_year2_aug'] = $row->aug_price;
			$data['improved_sweet_and_spicy_marinado_prev_year2_sep'] = $row->sep_price;
			$data['improved_sweet_and_spicy_marinado_prev_year2_oct'] = $row->oct_price;
			$data['improved_sweet_and_spicy_marinado_prev_year2_nov'] = $row->nov_price;
			$data['improved_sweet_and_spicy_marinado_prev_year2_dec'] = $row->dec_price;
			$data['improved_sweet_and_spicy_marinado_prev_year2_avg'] = ($row->jan_price + $row->feb_price + $row->mar_price + $row->apr_price + $row->may_price + $row->jun_price + $row->jul_price + $row->aug_price + $row->sep_price + $row->oct_price + $row->nov_price + $row->dec_price) / 12;

			$data['improved_sweet_and_spicy_marinado_prev_year2_min'] = min($row->jan_price, $row->feb_price, $row->mar_price, $row->apr_price, $row->may_price, $row->jun_price, $row->jul_price, $row->aug_price, $row->sep_price, $row->oct_price, $row->nov_price, $row->dec_price);

			$data['improved_sweet_and_spicy_marinado_prev_year2_max'] = max($row->jan_price, $row->feb_price, $row->mar_price, $row->apr_price, $row->may_price, $row->jun_price, $row->jul_price, $row->aug_price, $row->sep_price, $row->oct_price, $row->nov_price, $row->dec_price);
		}


		$improved_sweet_and_spicy_marinado_prev_year1 = $this->admin->get_data('comparative_price_tbl', array('comp_price_product' => 'MARINADO FRIED', 'comp_price_segment' => 'REYAL', 'comp_price_year' => $year - 1,	'bc_id'	=>	$bc_id), 
			FALSE, 'AVG(comp_price_jan) as jan_price, AVG(comp_price_feb) as feb_price, AVG(comp_price_mar) as mar_price, AVG(comp_price_apr) as apr_price, AVG(comp_price_may) as may_price, AVG(comp_price_jun) as jun_price, AVG(comp_price_jul) as jul_price, AVG(comp_price_aug) as aug_price, AVG(comp_price_sep) as sep_price, AVG(comp_price_oct) as oct_price, AVG(comp_price_nov) as nov_price, AVG(comp_price_dec) as dec_price');
		
		$data['improved_sweet_and_spicy_marinado_prev_year1_jan'] = 0;
		$data['improved_sweet_and_spicy_marinado_prev_year1_feb'] = 0;
		$data['improved_sweet_and_spicy_marinado_prev_year1_mar'] = 0;
		$data['improved_sweet_and_spicy_marinado_prev_year1_apr'] = 0;
		$data['improved_sweet_and_spicy_marinado_prev_year1_may'] = 0;
		$data['improved_sweet_and_spicy_marinado_prev_year1_jun'] = 0;
		$data['improved_sweet_and_spicy_marinado_prev_year1_jul'] = 0;
		$data['improved_sweet_and_spicy_marinado_prev_year1_aug'] = 0;
		$data['improved_sweet_and_spicy_marinado_prev_year1_sep'] = 0;
		$data['improved_sweet_and_spicy_marinado_prev_year1_oct'] = 0;
		$data['improved_sweet_and_spicy_marinado_prev_year1_nov'] = 0;
		$data['improved_sweet_and_spicy_marinado_prev_year1_dec'] = 0;
		$data['improved_sweet_and_spicy_marinado_prev_year1_avg'] = 0;
		$data['improved_sweet_and_spicy_marinado_prev_year1_min'] = 0;
		$data['improved_sweet_and_spicy_marinado_prev_year1_max'] = 0;

		foreach($improved_sweet_and_spicy_marinado_prev_year1 as $row){
			$data['improved_sweet_and_spicy_marinado_prev_year1_jan'] = $row->jan_price;
			$data['improved_sweet_and_spicy_marinado_prev_year1_feb'] = $row->feb_price;
			$data['improved_sweet_and_spicy_marinado_prev_year1_mar'] = $row->mar_price;
			$data['improved_sweet_and_spicy_marinado_prev_year1_apr'] = $row->apr_price;
			$data['improved_sweet_and_spicy_marinado_prev_year1_may'] = $row->may_price;
			$data['improved_sweet_and_spicy_marinado_prev_year1_jun'] = $row->jun_price;
			$data['improved_sweet_and_spicy_marinado_prev_year1_jul'] = $row->jul_price;
			$data['improved_sweet_and_spicy_marinado_prev_year1_aug'] = $row->aug_price;
			$data['improved_sweet_and_spicy_marinado_prev_year1_sep'] = $row->sep_price;
			$data['improved_sweet_and_spicy_marinado_prev_year1_oct'] = $row->oct_price;
			$data['improved_sweet_and_spicy_marinado_prev_year1_nov'] = $row->nov_price;
			$data['improved_sweet_and_spicy_marinado_prev_year1_dec'] = $row->dec_price;
			$data['improved_sweet_and_spicy_marinado_prev_year1_avg'] = ($row->jan_price + $row->feb_price + $row->mar_price + $row->apr_price + $row->may_price + $row->jun_price + $row->jul_price + $row->aug_price + $row->sep_price + $row->oct_price + $row->nov_price + $row->dec_price) / 12;

			$data['improved_sweet_and_spicy_marinado_prev_year1_min'] = min($row->jan_price, $row->feb_price, $row->mar_price, $row->apr_price, $row->may_price, $row->jun_price, $row->jul_price, $row->aug_price, $row->sep_price, $row->oct_price, $row->nov_price, $row->dec_price);

			$data['improved_sweet_and_spicy_marinado_prev_year1_max'] = max($row->jan_price, $row->feb_price, $row->mar_price, $row->apr_price, $row->may_price, $row->jun_price, $row->jul_price, $row->aug_price, $row->sep_price, $row->oct_price, $row->nov_price, $row->dec_price);
		}


		/*REYAL Spicy Neck*/
		$join_improved_sweet_and_spicy_spicy = array(
			'sales_item_tbl b' => 'a.sales_id = b.sales_id AND a.sales_status = 1 AND b.sales_item_status = 1 AND a.sales_year = ' . $year,
			'sales_details_tbl c' => 'b.sales_item_id = c.sales_item_id AND c.sales_det_status = 1',
			'material_tbl d' => 'b.material_id = d.material_id AND (d.material_code = 10200090 OR d.material_code = 1000401 OR d.material_code = 1000402 OR d.material_code = 1000617)',
			'material_group_tbl e' => 'd.material_group_id = e.material_group_id AND e.material_group_name = \'MARINATED CUT UPS\'',
			'outlet_tbl f' => 'a.outlet_id = f.outlet_id AND f.bc_id = ' . $bc_id,
			'outlet_brand_tbl g' => 'f.outlet_id = g.outlet_id AND g.outlet_brand_status = 1',
			'brand_tbl h' => 'g.brand_id  = h.brand_id AND h.brand_name = \'REYAL\''
		);

		$get_improved_sweet_and_spicy_spicy = $this->admin->get_join('sales_tbl a', $join_improved_sweet_and_spicy_spicy, FALSE, FALSE, 'YEAR(c.sales_det_date), MONTH(c.sales_det_date)', 'AVG(c.sales_det_asp) as asp, MONTH(c.sales_det_date) asp_date');

		$data['improved_sweet_and_spicy_spicy1'] = 0;
		$data['improved_sweet_and_spicy_spicy2'] = 0;
		$data['improved_sweet_and_spicy_spicy3'] = 0;
		$data['improved_sweet_and_spicy_spicy4'] = 0;
		$data['improved_sweet_and_spicy_spicy5'] = 0;
		$data['improved_sweet_and_spicy_spicy6'] = 0;
		$data['improved_sweet_and_spicy_spicy7'] = 0;
		$data['improved_sweet_and_spicy_spicy8'] = 0;
		$data['improved_sweet_and_spicy_spicy9'] = 0;
		$data['improved_sweet_and_spicy_spicy10'] = 0;
		$data['improved_sweet_and_spicy_spicy11'] = 0;
		$data['improved_sweet_and_spicy_spicy12'] = 0;
		$data['improved_sweet_and_spicy_spicy_total'] = 0;
		$data['improved_sweet_and_spicy_spicy_count'] = 0;
		$data['improved_sweet_and_spicy_spicy_avg'] = 0;
		$data['improved_sweet_and_spicy_spicy_min'] = 0;
		$data['improved_sweet_and_spicy_spicy_max'] = 0;

		foreach($get_improved_sweet_and_spicy_spicy as $row){
			$asp = $row->asp;
			$month = $row->asp_date;
			$data['improved_sweet_and_spicy_spicy' . $month] = $asp;
			$data['improved_sweet_and_spicy_spicy_total'] += $asp;

			if($asp < $data['improved_sweet_and_spicy_spicy_min'] || $data['improved_sweet_and_spicy_spicy_count'] == 0){
				$data['improved_sweet_and_spicy_spicy_min'] = $asp;
			}

			if($asp > $data['improved_sweet_and_spicy_spicy_max'] || $data['improved_sweet_and_spicy_spicy_count'] == 0){
				$data['improved_sweet_and_spicy_spicy_max'] = $asp;
			}

			$data['improved_sweet_and_spicy_spicy_count']++;
		}

		$data['improved_sweet_and_spicy_spicy_avg'] = $data['improved_sweet_and_spicy_spicy_total'] != 0 ? $data['improved_sweet_and_spicy_spicy_total'] / $data['improved_sweet_and_spicy_spicy_count'] : 0;

		$improved_sweet_and_spicy_spicy_prev_year2 = $this->admin->get_data('comparative_price_tbl', array('comp_price_product' => 'SPICY NECK', 'comp_price_segment' => 'REYAL', 'comp_price_year' => $year - 2,	'bc_id'	=>	$bc_id), 
			FALSE, 'AVG(comp_price_jan) as jan_price, AVG(comp_price_feb) as feb_price, AVG(comp_price_mar) as mar_price, AVG(comp_price_apr) as apr_price, AVG(comp_price_may) as may_price, AVG(comp_price_jun) as jun_price, AVG(comp_price_jul) as jul_price, AVG(comp_price_aug) as aug_price, AVG(comp_price_sep) as sep_price, AVG(comp_price_oct) as oct_price, AVG(comp_price_nov) as nov_price, AVG(comp_price_dec) as dec_price');
		
		$data['improved_sweet_and_spicy_spicy_prev_year2_jan'] = 0;
		$data['improved_sweet_and_spicy_spicy_prev_year2_feb'] = 0;
		$data['improved_sweet_and_spicy_spicy_prev_year2_mar'] = 0;
		$data['improved_sweet_and_spicy_spicy_prev_year2_apr'] = 0;
		$data['improved_sweet_and_spicy_spicy_prev_year2_may'] = 0;
		$data['improved_sweet_and_spicy_spicy_prev_year2_jun'] = 0;
		$data['improved_sweet_and_spicy_spicy_prev_year2_jul'] = 0;
		$data['improved_sweet_and_spicy_spicy_prev_year2_aug'] = 0;
		$data['improved_sweet_and_spicy_spicy_prev_year2_sep'] = 0;
		$data['improved_sweet_and_spicy_spicy_prev_year2_oct'] = 0;
		$data['improved_sweet_and_spicy_spicy_prev_year2_nov'] = 0;
		$data['improved_sweet_and_spicy_spicy_prev_year2_dec'] = 0;
		$data['improved_sweet_and_spicy_spicy_prev_year2_avg'] = 0;
		$data['improved_sweet_and_spicy_spicy_prev_year2_min'] = 0;
		$data['improved_sweet_and_spicy_spicy_prev_year2_max'] = 0;

		foreach($improved_sweet_and_spicy_spicy_prev_year2 as $row){
			$data['improved_sweet_and_spicy_spicy_prev_year2_jan'] = $row->jan_price;
			$data['improved_sweet_and_spicy_spicy_prev_year2_feb'] = $row->feb_price;
			$data['improved_sweet_and_spicy_spicy_prev_year2_mar'] = $row->mar_price;
			$data['improved_sweet_and_spicy_spicy_prev_year2_apr'] = $row->apr_price;
			$data['improved_sweet_and_spicy_spicy_prev_year2_may'] = $row->may_price;
			$data['improved_sweet_and_spicy_spicy_prev_year2_jun'] = $row->jun_price;
			$data['improved_sweet_and_spicy_spicy_prev_year2_jul'] = $row->jul_price;
			$data['improved_sweet_and_spicy_spicy_prev_year2_aug'] = $row->aug_price;
			$data['improved_sweet_and_spicy_spicy_prev_year2_sep'] = $row->sep_price;
			$data['improved_sweet_and_spicy_spicy_prev_year2_oct'] = $row->oct_price;
			$data['improved_sweet_and_spicy_spicy_prev_year2_nov'] = $row->nov_price;
			$data['improved_sweet_and_spicy_spicy_prev_year2_dec'] = $row->dec_price;
			$data['improved_sweet_and_spicy_spicy_prev_year2_avg'] = ($row->jan_price + $row->feb_price + $row->mar_price + $row->apr_price + $row->may_price + $row->jun_price + $row->jul_price + $row->aug_price + $row->sep_price + $row->oct_price + $row->nov_price + $row->dec_price) / 12;

			$data['improved_sweet_and_spicy_spicy_prev_year2_min'] = min($row->jan_price, $row->feb_price, $row->mar_price, $row->apr_price, $row->may_price, $row->jun_price, $row->jul_price, $row->aug_price, $row->sep_price, $row->oct_price, $row->nov_price, $row->dec_price);

			$data['improved_sweet_and_spicy_spicy_prev_year2_max'] = max($row->jan_price, $row->feb_price, $row->mar_price, $row->apr_price, $row->may_price, $row->jun_price, $row->jul_price, $row->aug_price, $row->sep_price, $row->oct_price, $row->nov_price, $row->dec_price);
		}


		$improved_sweet_and_spicy_spicy_prev_year1 = $this->admin->get_data('comparative_price_tbl', array('comp_price_product' => 'SPICY NECK', 'comp_price_segment' => 'REYAL', 'comp_price_year' => $year - 1,	'bc_id'	=>	$bc_id), 
			FALSE, 'AVG(comp_price_jan) as jan_price, AVG(comp_price_feb) as feb_price, AVG(comp_price_mar) as mar_price, AVG(comp_price_apr) as apr_price, AVG(comp_price_may) as may_price, AVG(comp_price_jun) as jun_price, AVG(comp_price_jul) as jul_price, AVG(comp_price_aug) as aug_price, AVG(comp_price_sep) as sep_price, AVG(comp_price_oct) as oct_price, AVG(comp_price_nov) as nov_price, AVG(comp_price_dec) as dec_price');
		
		$data['improved_sweet_and_spicy_spicy_prev_year1_jan'] = 0;
		$data['improved_sweet_and_spicy_spicy_prev_year1_feb'] = 0;
		$data['improved_sweet_and_spicy_spicy_prev_year1_mar'] = 0;
		$data['improved_sweet_and_spicy_spicy_prev_year1_apr'] = 0;
		$data['improved_sweet_and_spicy_spicy_prev_year1_may'] = 0;
		$data['improved_sweet_and_spicy_spicy_prev_year1_jun'] = 0;
		$data['improved_sweet_and_spicy_spicy_prev_year1_jul'] = 0;
		$data['improved_sweet_and_spicy_spicy_prev_year1_aug'] = 0;
		$data['improved_sweet_and_spicy_spicy_prev_year1_sep'] = 0;
		$data['improved_sweet_and_spicy_spicy_prev_year1_oct'] = 0;
		$data['improved_sweet_and_spicy_spicy_prev_year1_nov'] = 0;
		$data['improved_sweet_and_spicy_spicy_prev_year1_dec'] = 0;
		$data['improved_sweet_and_spicy_spicy_prev_year1_avg'] = 0;
		$data['improved_sweet_and_spicy_spicy_prev_year1_min'] = 0;
		$data['improved_sweet_and_spicy_spicy_prev_year1_max'] = 0;

		foreach($improved_sweet_and_spicy_spicy_prev_year1 as $row){
			$data['improved_sweet_and_spicy_spicy_prev_year1_jan'] = $row->jan_price;
			$data['improved_sweet_and_spicy_spicy_prev_year1_feb'] = $row->feb_price;
			$data['improved_sweet_and_spicy_spicy_prev_year1_mar'] = $row->mar_price;
			$data['improved_sweet_and_spicy_spicy_prev_year1_apr'] = $row->apr_price;
			$data['improved_sweet_and_spicy_spicy_prev_year1_may'] = $row->may_price;
			$data['improved_sweet_and_spicy_spicy_prev_year1_jun'] = $row->jun_price;
			$data['improved_sweet_and_spicy_spicy_prev_year1_jul'] = $row->jul_price;
			$data['improved_sweet_and_spicy_spicy_prev_year1_aug'] = $row->aug_price;
			$data['improved_sweet_and_spicy_spicy_prev_year1_sep'] = $row->sep_price;
			$data['improved_sweet_and_spicy_spicy_prev_year1_oct'] = $row->oct_price;
			$data['improved_sweet_and_spicy_spicy_prev_year1_nov'] = $row->nov_price;
			$data['improved_sweet_and_spicy_spicy_prev_year1_dec'] = $row->dec_price;
			$data['improved_sweet_and_spicy_spicy_prev_year1_avg'] = ($row->jan_price + $row->feb_price + $row->mar_price + $row->apr_price + $row->may_price + $row->jun_price + $row->jul_price + $row->aug_price + $row->sep_price + $row->oct_price + $row->nov_price + $row->dec_price) / 12;

			$data['improved_sweet_and_spicy_spicy_prev_year1_min'] = min($row->jan_price, $row->feb_price, $row->mar_price, $row->apr_price, $row->may_price, $row->jun_price, $row->jul_price, $row->aug_price, $row->sep_price, $row->oct_price, $row->nov_price, $row->dec_price);

			$data['improved_sweet_and_spicy_spicy_prev_year1_max'] = max($row->jan_price, $row->feb_price, $row->mar_price, $row->apr_price, $row->may_price, $row->jun_price, $row->jul_price, $row->aug_price, $row->sep_price, $row->oct_price, $row->nov_price, $row->dec_price);
		}

		/*REYAL Barbeque*/
		$join_improved_sweet_and_spicy_barbeque = array(
			'sales_item_tbl b' => 'a.sales_id = b.sales_id AND a.sales_status = 1 AND b.sales_item_status = 1 AND a.sales_year = ' . $year,
			'sales_details_tbl c' => 'b.sales_item_id = c.sales_item_id AND c.sales_det_status = 1',
			'material_tbl d' => 'b.material_id = d.material_id AND d.material_code = 1000548',
			'material_group_tbl e' => 'd.material_group_id = e.material_group_id AND e.material_group_name = \'OTHER SPECIALTY PRODUCTS\'',
			'outlet_tbl f' => 'a.outlet_id = f.outlet_id AND f.bc_id = ' . $bc_id,
			'outlet_brand_tbl g' => 'f.outlet_id = g.outlet_id AND g.outlet_brand_status = 1',
			'brand_tbl h' => 'g.brand_id  = h.brand_id AND h.brand_name = \'REYAL\''
		);

		$get_improved_sweet_and_spicy_barbeque = $this->admin->get_join('sales_tbl a', $join_improved_sweet_and_spicy_barbeque, FALSE, FALSE, 'YEAR(c.sales_det_date), MONTH(c.sales_det_date)', 'AVG(c.sales_det_asp) as asp, MONTH(c.sales_det_date) asp_date');

		$data['improved_sweet_and_spicy_barbeque1'] = 0;
		$data['improved_sweet_and_spicy_barbeque2'] = 0;
		$data['improved_sweet_and_spicy_barbeque3'] = 0;
		$data['improved_sweet_and_spicy_barbeque4'] = 0;
		$data['improved_sweet_and_spicy_barbeque5'] = 0;
		$data['improved_sweet_and_spicy_barbeque6'] = 0;
		$data['improved_sweet_and_spicy_barbeque7'] = 0;
		$data['improved_sweet_and_spicy_barbeque8'] = 0;
		$data['improved_sweet_and_spicy_barbeque9'] = 0;
		$data['improved_sweet_and_spicy_barbeque10'] = 0;
		$data['improved_sweet_and_spicy_barbeque11'] = 0;
		$data['improved_sweet_and_spicy_barbeque12'] = 0;
		$data['improved_sweet_and_spicy_barbeque_total'] = 0;
		$data['improved_sweet_and_spicy_barbeque_count'] = 0;
		$data['improved_sweet_and_spicy_barbeque_avg'] = 0;
		$data['improved_sweet_and_spicy_barbeque_min'] = 0;
		$data['improved_sweet_and_spicy_barbeque_max'] = 0;

		foreach($get_improved_sweet_and_spicy_barbeque as $row){
			$asp = $row->asp;
			$month = $row->asp_date;
			$data['improved_sweet_and_spicy_barbeque' . $month] = $asp;
			$data['improved_sweet_and_spicy_barbeque_total'] += $asp;

			if($asp < $data['improved_sweet_and_spicy_barbeque_min'] || $data['improved_sweet_and_spicy_barbeque_count'] == 0){
				$data['improved_sweet_and_spicy_barbeque_min'] = $asp;
			}

			if($asp > $data['improved_sweet_and_spicy_barbeque_max'] || $data['improved_sweet_and_spicy_barbeque_count'] == 0){
				$data['improved_sweet_and_spicy_barbeque_max'] = $asp;
			}

			$data['improved_sweet_and_spicy_barbeque_count']++;
		}

		$data['improved_sweet_and_spicy_barbeque_avg'] = $data['improved_sweet_and_spicy_barbeque_total'] != 0 ? $data['improved_sweet_and_spicy_barbeque_total'] / $data['improved_sweet_and_spicy_barbeque_count'] : 0;

		$improved_sweet_and_spicy_barbeque_prev_year2 = $this->admin->get_data('comparative_price_tbl', array('comp_price_product' => 'BBQ', 'comp_price_segment' => 'REYAL', 'comp_price_year' => $year - 2,	'bc_id'	=>	$bc_id), 
			FALSE, 'AVG(comp_price_jan) as jan_price, AVG(comp_price_feb) as feb_price, AVG(comp_price_mar) as mar_price, AVG(comp_price_apr) as apr_price, AVG(comp_price_may) as may_price, AVG(comp_price_jun) as jun_price, AVG(comp_price_jul) as jul_price, AVG(comp_price_aug) as aug_price, AVG(comp_price_sep) as sep_price, AVG(comp_price_oct) as oct_price, AVG(comp_price_nov) as nov_price, AVG(comp_price_dec) as dec_price');
		
		$data['improved_sweet_and_spicy_barbeque_prev_year2_jan'] = 0;
		$data['improved_sweet_and_spicy_barbeque_prev_year2_feb'] = 0;
		$data['improved_sweet_and_spicy_barbeque_prev_year2_mar'] = 0;
		$data['improved_sweet_and_spicy_barbeque_prev_year2_apr'] = 0;
		$data['improved_sweet_and_spicy_barbeque_prev_year2_may'] = 0;
		$data['improved_sweet_and_spicy_barbeque_prev_year2_jun'] = 0;
		$data['improved_sweet_and_spicy_barbeque_prev_year2_jul'] = 0;
		$data['improved_sweet_and_spicy_barbeque_prev_year2_aug'] = 0;
		$data['improved_sweet_and_spicy_barbeque_prev_year2_sep'] = 0;
		$data['improved_sweet_and_spicy_barbeque_prev_year2_oct'] = 0;
		$data['improved_sweet_and_spicy_barbeque_prev_year2_nov'] = 0;
		$data['improved_sweet_and_spicy_barbeque_prev_year2_dec'] = 0;
		$data['improved_sweet_and_spicy_barbeque_prev_year2_avg'] = 0;
		$data['improved_sweet_and_spicy_barbeque_prev_year2_min'] = 0;
		$data['improved_sweet_and_spicy_barbeque_prev_year2_max'] = 0;

		foreach($improved_sweet_and_spicy_barbeque_prev_year2 as $row){
			$data['improved_sweet_and_spicy_barbeque_prev_year2_jan'] = $row->jan_price;
			$data['improved_sweet_and_spicy_barbeque_prev_year2_feb'] = $row->feb_price;
			$data['improved_sweet_and_spicy_barbeque_prev_year2_mar'] = $row->mar_price;
			$data['improved_sweet_and_spicy_barbeque_prev_year2_apr'] = $row->apr_price;
			$data['improved_sweet_and_spicy_barbeque_prev_year2_may'] = $row->may_price;
			$data['improved_sweet_and_spicy_barbeque_prev_year2_jun'] = $row->jun_price;
			$data['improved_sweet_and_spicy_barbeque_prev_year2_jul'] = $row->jul_price;
			$data['improved_sweet_and_spicy_barbeque_prev_year2_aug'] = $row->aug_price;
			$data['improved_sweet_and_spicy_barbeque_prev_year2_sep'] = $row->sep_price;
			$data['improved_sweet_and_spicy_barbeque_prev_year2_oct'] = $row->oct_price;
			$data['improved_sweet_and_spicy_barbeque_prev_year2_nov'] = $row->nov_price;
			$data['improved_sweet_and_spicy_barbeque_prev_year2_dec'] = $row->dec_price;
			$data['improved_sweet_and_spicy_barbeque_prev_year2_avg'] = ($row->jan_price + $row->feb_price + $row->mar_price + $row->apr_price + $row->may_price + $row->jun_price + $row->jul_price + $row->aug_price + $row->sep_price + $row->oct_price + $row->nov_price + $row->dec_price) / 12;

			$data['improved_sweet_and_spicy_barbeque_prev_year2_min'] = min($row->jan_price, $row->feb_price, $row->mar_price, $row->apr_price, $row->may_price, $row->jun_price, $row->jul_price, $row->aug_price, $row->sep_price, $row->oct_price, $row->nov_price, $row->dec_price);

			$data['improved_sweet_and_spicy_barbeque_prev_year2_max'] = max($row->jan_price, $row->feb_price, $row->mar_price, $row->apr_price, $row->may_price, $row->jun_price, $row->jul_price, $row->aug_price, $row->sep_price, $row->oct_price, $row->nov_price, $row->dec_price);
		}


		$improved_sweet_and_spicy_barbeque_prev_year1 = $this->admin->get_data('comparative_price_tbl', array('comp_price_product' => 'BBQ', 'comp_price_segment' => 'REYAL', 'comp_price_year' => $year - 1,	'bc_id'	=>	$bc_id), 
			FALSE, 'AVG(comp_price_jan) as jan_price, AVG(comp_price_feb) as feb_price, AVG(comp_price_mar) as mar_price, AVG(comp_price_apr) as apr_price, AVG(comp_price_may) as may_price, AVG(comp_price_jun) as jun_price, AVG(comp_price_jul) as jul_price, AVG(comp_price_aug) as aug_price, AVG(comp_price_sep) as sep_price, AVG(comp_price_oct) as oct_price, AVG(comp_price_nov) as nov_price, AVG(comp_price_dec) as dec_price');
		
		$data['improved_sweet_and_spicy_barbeque_prev_year1_jan'] = 0;
		$data['improved_sweet_and_spicy_barbeque_prev_year1_feb'] = 0;
		$data['improved_sweet_and_spicy_barbeque_prev_year1_mar'] = 0;
		$data['improved_sweet_and_spicy_barbeque_prev_year1_apr'] = 0;
		$data['improved_sweet_and_spicy_barbeque_prev_year1_may'] = 0;
		$data['improved_sweet_and_spicy_barbeque_prev_year1_jun'] = 0;
		$data['improved_sweet_and_spicy_barbeque_prev_year1_jul'] = 0;
		$data['improved_sweet_and_spicy_barbeque_prev_year1_aug'] = 0;
		$data['improved_sweet_and_spicy_barbeque_prev_year1_sep'] = 0;
		$data['improved_sweet_and_spicy_barbeque_prev_year1_oct'] = 0;
		$data['improved_sweet_and_spicy_barbeque_prev_year1_nov'] = 0;
		$data['improved_sweet_and_spicy_barbeque_prev_year1_dec'] = 0;
		$data['improved_sweet_and_spicy_barbeque_prev_year1_avg'] = 0;
		$data['improved_sweet_and_spicy_barbeque_prev_year1_min'] = 0;
		$data['improved_sweet_and_spicy_barbeque_prev_year1_max'] = 0;

		foreach($improved_sweet_and_spicy_barbeque_prev_year1 as $row){
			$data['improved_sweet_and_spicy_barbeque_prev_year1_jan'] = $row->jan_price;
			$data['improved_sweet_and_spicy_barbeque_prev_year1_feb'] = $row->feb_price;
			$data['improved_sweet_and_spicy_barbeque_prev_year1_mar'] = $row->mar_price;
			$data['improved_sweet_and_spicy_barbeque_prev_year1_apr'] = $row->apr_price;
			$data['improved_sweet_and_spicy_barbeque_prev_year1_may'] = $row->may_price;
			$data['improved_sweet_and_spicy_barbeque_prev_year1_jun'] = $row->jun_price;
			$data['improved_sweet_and_spicy_barbeque_prev_year1_jul'] = $row->jul_price;
			$data['improved_sweet_and_spicy_barbeque_prev_year1_aug'] = $row->aug_price;
			$data['improved_sweet_and_spicy_barbeque_prev_year1_sep'] = $row->sep_price;
			$data['improved_sweet_and_spicy_barbeque_prev_year1_oct'] = $row->oct_price;
			$data['improved_sweet_and_spicy_barbeque_prev_year1_nov'] = $row->nov_price;
			$data['improved_sweet_and_spicy_barbeque_prev_year1_dec'] = $row->dec_price;
			$data['improved_sweet_and_spicy_barbeque_prev_year1_avg'] = ($row->jan_price + $row->feb_price + $row->mar_price + $row->apr_price + $row->may_price + $row->jun_price + $row->jul_price + $row->aug_price + $row->sep_price + $row->oct_price + $row->nov_price + $row->dec_price) / 12;

			$data['improved_sweet_and_spicy_barbeque_prev_year1_min'] = min($row->jan_price, $row->feb_price, $row->mar_price, $row->apr_price, $row->may_price, $row->jun_price, $row->jul_price, $row->aug_price, $row->sep_price, $row->oct_price, $row->nov_price, $row->dec_price);

			$data['improved_sweet_and_spicy_barbeque_prev_year1_max'] = max($row->jan_price, $row->feb_price, $row->mar_price, $row->apr_price, $row->may_price, $row->jun_price, $row->jul_price, $row->aug_price, $row->sep_price, $row->oct_price, $row->nov_price, $row->dec_price);
		}

		/*REYAL Nuggets*/
		$join_improved_sweet_and_spicy_nuggets = array(
			'sales_item_tbl b' => 'a.sales_id = b.sales_id AND a.sales_status = 1 AND b.sales_item_status = 1 AND a.sales_year = ' . $year,
			'sales_details_tbl c' => 'b.sales_item_id = c.sales_item_id AND c.sales_det_status = 1',
			'material_tbl d' => 'b.material_id = d.material_id AND (d.material_code = 40000016 OR d.material_code = 40000019)',
			'material_group_tbl e' => 'd.material_group_id = e.material_group_id AND e.material_group_name = \'OTHER VAP PRODUCTS\'',
			'outlet_tbl f' => 'a.outlet_id = f.outlet_id AND f.bc_id = ' . $bc_id,
			'outlet_brand_tbl g' => 'f.outlet_id = g.outlet_id AND g.outlet_brand_status = 1',
			'brand_tbl h' => 'g.brand_id  = h.brand_id AND h.brand_name = \'REYAL\''
		);

		$get_improved_sweet_and_spicy_nuggets = $this->admin->get_join('sales_tbl a', $join_improved_sweet_and_spicy_nuggets, FALSE, FALSE, 'YEAR(c.sales_det_date), MONTH(c.sales_det_date)', 'AVG(c.sales_det_asp) as asp, MONTH(c.sales_det_date) asp_date');

		$data['improved_sweet_and_spicy_nuggets1'] = 0;
		$data['improved_sweet_and_spicy_nuggets2'] = 0;
		$data['improved_sweet_and_spicy_nuggets3'] = 0;
		$data['improved_sweet_and_spicy_nuggets4'] = 0;
		$data['improved_sweet_and_spicy_nuggets5'] = 0;
		$data['improved_sweet_and_spicy_nuggets6'] = 0;
		$data['improved_sweet_and_spicy_nuggets7'] = 0;
		$data['improved_sweet_and_spicy_nuggets8'] = 0;
		$data['improved_sweet_and_spicy_nuggets9'] = 0;
		$data['improved_sweet_and_spicy_nuggets10'] = 0;
		$data['improved_sweet_and_spicy_nuggets11'] = 0;
		$data['improved_sweet_and_spicy_nuggets12'] = 0;
		$data['improved_sweet_and_spicy_nuggets_total'] = 0;
		$data['improved_sweet_and_spicy_nuggets_count'] = 0;
		$data['improved_sweet_and_spicy_nuggets_avg'] = 0;
		$data['improved_sweet_and_spicy_nuggets_min'] = 0;
		$data['improved_sweet_and_spicy_nuggets_max'] = 0;

		foreach($get_improved_sweet_and_spicy_nuggets as $row){
			$asp = $row->asp;
			$month = $row->asp_date;
			$data['improved_sweet_and_spicy_nuggets' . $month] = $asp;
			$data['improved_sweet_and_spicy_nuggets_total'] += $asp;

			if($asp < $data['improved_sweet_and_spicy_nuggets_min'] || $data['improved_sweet_and_spicy_nuggets_count'] == 0){
				$data['improved_sweet_and_spicy_nuggets_min'] = $asp;
			}

			if($asp > $data['improved_sweet_and_spicy_nuggets_max'] || $data['improved_sweet_and_spicy_nuggets_count'] == 0){
				$data['improved_sweet_and_spicy_nuggets_max'] = $asp;
			}

			$data['improved_sweet_and_spicy_nuggets_count']++;
		}

		$data['improved_sweet_and_spicy_nuggets_avg'] = $data['improved_sweet_and_spicy_nuggets_total'] != 0 ? $data['improved_sweet_and_spicy_nuggets_total'] / $data['improved_sweet_and_spicy_nuggets_count'] : 0;

		$improved_sweet_and_spicy_nuggets_prev_year2 = $this->admin->get_data('comparative_price_tbl', array('comp_price_product' => 'VAP-NUGGETS', 'comp_price_segment' => 'REYAL', 'comp_price_year' => $year - 2,	'bc_id'	=>	$bc_id), 
			FALSE, 'AVG(comp_price_jan) as jan_price, AVG(comp_price_feb) as feb_price, AVG(comp_price_mar) as mar_price, AVG(comp_price_apr) as apr_price, AVG(comp_price_may) as may_price, AVG(comp_price_jun) as jun_price, AVG(comp_price_jul) as jul_price, AVG(comp_price_aug) as aug_price, AVG(comp_price_sep) as sep_price, AVG(comp_price_oct) as oct_price, AVG(comp_price_nov) as nov_price, AVG(comp_price_dec) as dec_price');
		
		$data['improved_sweet_and_spicy_nuggets_prev_year2_jan'] = 0;
		$data['improved_sweet_and_spicy_nuggets_prev_year2_feb'] = 0;
		$data['improved_sweet_and_spicy_nuggets_prev_year2_mar'] = 0;
		$data['improved_sweet_and_spicy_nuggets_prev_year2_apr'] = 0;
		$data['improved_sweet_and_spicy_nuggets_prev_year2_may'] = 0;
		$data['improved_sweet_and_spicy_nuggets_prev_year2_jun'] = 0;
		$data['improved_sweet_and_spicy_nuggets_prev_year2_jul'] = 0;
		$data['improved_sweet_and_spicy_nuggets_prev_year2_aug'] = 0;
		$data['improved_sweet_and_spicy_nuggets_prev_year2_sep'] = 0;
		$data['improved_sweet_and_spicy_nuggets_prev_year2_oct'] = 0;
		$data['improved_sweet_and_spicy_nuggets_prev_year2_nov'] = 0;
		$data['improved_sweet_and_spicy_nuggets_prev_year2_dec'] = 0;
		$data['improved_sweet_and_spicy_nuggets_prev_year2_avg'] = 0;
		$data['improved_sweet_and_spicy_nuggets_prev_year2_min'] = 0;
		$data['improved_sweet_and_spicy_nuggets_prev_year2_max'] = 0;

		foreach($improved_sweet_and_spicy_nuggets_prev_year2 as $row){
			$data['improved_sweet_and_spicy_nuggets_prev_year2_jan'] = $row->jan_price;
			$data['improved_sweet_and_spicy_nuggets_prev_year2_feb'] = $row->feb_price;
			$data['improved_sweet_and_spicy_nuggets_prev_year2_mar'] = $row->mar_price;
			$data['improved_sweet_and_spicy_nuggets_prev_year2_apr'] = $row->apr_price;
			$data['improved_sweet_and_spicy_nuggets_prev_year2_may'] = $row->may_price;
			$data['improved_sweet_and_spicy_nuggets_prev_year2_jun'] = $row->jun_price;
			$data['improved_sweet_and_spicy_nuggets_prev_year2_jul'] = $row->jul_price;
			$data['improved_sweet_and_spicy_nuggets_prev_year2_aug'] = $row->aug_price;
			$data['improved_sweet_and_spicy_nuggets_prev_year2_sep'] = $row->sep_price;
			$data['improved_sweet_and_spicy_nuggets_prev_year2_oct'] = $row->oct_price;
			$data['improved_sweet_and_spicy_nuggets_prev_year2_nov'] = $row->nov_price;
			$data['improved_sweet_and_spicy_nuggets_prev_year2_dec'] = $row->dec_price;
			$data['improved_sweet_and_spicy_nuggets_prev_year2_avg'] = ($row->jan_price + $row->feb_price + $row->mar_price + $row->apr_price + $row->may_price + $row->jun_price + $row->jul_price + $row->aug_price + $row->sep_price + $row->oct_price + $row->nov_price + $row->dec_price) / 12;

			$data['improved_sweet_and_spicy_nuggets_prev_year2_min'] = min($row->jan_price, $row->feb_price, $row->mar_price, $row->apr_price, $row->may_price, $row->jun_price, $row->jul_price, $row->aug_price, $row->sep_price, $row->oct_price, $row->nov_price, $row->dec_price);

			$data['improved_sweet_and_spicy_nuggets_prev_year2_max'] = max($row->jan_price, $row->feb_price, $row->mar_price, $row->apr_price, $row->may_price, $row->jun_price, $row->jul_price, $row->aug_price, $row->sep_price, $row->oct_price, $row->nov_price, $row->dec_price);
		}


		$improved_sweet_and_spicy_nuggets_prev_year1 = $this->admin->get_data('comparative_price_tbl', array('comp_price_product' => 'VAP-NUGGETS', 'comp_price_segment' => 'REYAL', 'comp_price_year' => $year - 1,	'bc_id'	=>	$bc_id), 
			FALSE, 'AVG(comp_price_jan) as jan_price, AVG(comp_price_feb) as feb_price, AVG(comp_price_mar) as mar_price, AVG(comp_price_apr) as apr_price, AVG(comp_price_may) as may_price, AVG(comp_price_jun) as jun_price, AVG(comp_price_jul) as jul_price, AVG(comp_price_aug) as aug_price, AVG(comp_price_sep) as sep_price, AVG(comp_price_oct) as oct_price, AVG(comp_price_nov) as nov_price, AVG(comp_price_dec) as dec_price');
		
		$data['improved_sweet_and_spicy_nuggets_prev_year1_jan'] = 0;
		$data['improved_sweet_and_spicy_nuggets_prev_year1_feb'] = 0;
		$data['improved_sweet_and_spicy_nuggets_prev_year1_mar'] = 0;
		$data['improved_sweet_and_spicy_nuggets_prev_year1_apr'] = 0;
		$data['improved_sweet_and_spicy_nuggets_prev_year1_may'] = 0;
		$data['improved_sweet_and_spicy_nuggets_prev_year1_jun'] = 0;
		$data['improved_sweet_and_spicy_nuggets_prev_year1_jul'] = 0;
		$data['improved_sweet_and_spicy_nuggets_prev_year1_aug'] = 0;
		$data['improved_sweet_and_spicy_nuggets_prev_year1_sep'] = 0;
		$data['improved_sweet_and_spicy_nuggets_prev_year1_oct'] = 0;
		$data['improved_sweet_and_spicy_nuggets_prev_year1_nov'] = 0;
		$data['improved_sweet_and_spicy_nuggets_prev_year1_dec'] = 0;
		$data['improved_sweet_and_spicy_nuggets_prev_year1_avg'] = 0;
		$data['improved_sweet_and_spicy_nuggets_prev_year1_min'] = 0;
		$data['improved_sweet_and_spicy_nuggets_prev_year1_max'] = 0;

		foreach($improved_sweet_and_spicy_nuggets_prev_year1 as $row){
			$data['improved_sweet_and_spicy_nuggets_prev_year1_jan'] = $row->jan_price;
			$data['improved_sweet_and_spicy_nuggets_prev_year1_feb'] = $row->feb_price;
			$data['improved_sweet_and_spicy_nuggets_prev_year1_mar'] = $row->mar_price;
			$data['improved_sweet_and_spicy_nuggets_prev_year1_apr'] = $row->apr_price;
			$data['improved_sweet_and_spicy_nuggets_prev_year1_may'] = $row->may_price;
			$data['improved_sweet_and_spicy_nuggets_prev_year1_jun'] = $row->jun_price;
			$data['improved_sweet_and_spicy_nuggets_prev_year1_jul'] = $row->jul_price;
			$data['improved_sweet_and_spicy_nuggets_prev_year1_aug'] = $row->aug_price;
			$data['improved_sweet_and_spicy_nuggets_prev_year1_sep'] = $row->sep_price;
			$data['improved_sweet_and_spicy_nuggets_prev_year1_oct'] = $row->oct_price;
			$data['improved_sweet_and_spicy_nuggets_prev_year1_nov'] = $row->nov_price;
			$data['improved_sweet_and_spicy_nuggets_prev_year1_dec'] = $row->dec_price;
			$data['improved_sweet_and_spicy_nuggets_prev_year1_avg'] = ($row->jan_price + $row->feb_price + $row->mar_price + $row->apr_price + $row->may_price + $row->jun_price + $row->jul_price + $row->aug_price + $row->sep_price + $row->oct_price + $row->nov_price + $row->dec_price) / 12;

			$data['improved_sweet_and_spicy_nuggets_prev_year1_min'] = min($row->jan_price, $row->feb_price, $row->mar_price, $row->apr_price, $row->may_price, $row->jun_price, $row->jul_price, $row->aug_price, $row->sep_price, $row->oct_price, $row->nov_price, $row->dec_price);

			$data['improved_sweet_and_spicy_nuggets_prev_year1_max'] = max($row->jan_price, $row->feb_price, $row->mar_price, $row->apr_price, $row->may_price, $row->jun_price, $row->jul_price, $row->aug_price, $row->sep_price, $row->oct_price, $row->nov_price, $row->dec_price);
		}

		/*REYAL 11 PCS*/
		$join_improved_sweet_and_spicy_11pcs= array(
			'sales_item_tbl b' => 'a.sales_id = b.sales_id AND a.sales_status = 1 AND b.sales_item_status = 1 AND a.sales_year = ' . $year,
			'sales_details_tbl c' => 'b.sales_item_id = c.sales_item_id AND c.sales_det_status = 1',
			'material_tbl d' => 'b.material_id = d.material_id AND d.material_code = 10400170',
			'material_group_tbl e' => 'd.material_group_id = e.material_group_id AND e.material_group_name = \'OTHER SPECIALTY PRODUCTS\'',
			'outlet_tbl f' => 'a.outlet_id = f.outlet_id AND f.bc_id = ' . $bc_id,
			'outlet_brand_tbl g' => 'f.outlet_id = g.outlet_id AND g.outlet_brand_status = 1',
			'brand_tbl h' => 'g.brand_id  = h.brand_id AND h.brand_name = \'REYAL\''
		);

		$get_improved_sweet_and_spicy_11pcs = $this->admin->get_join('sales_tbl a', $join_improved_sweet_and_spicy_11pcs, FALSE, FALSE, 'YEAR(c.sales_det_date), MONTH(c.sales_det_date)', 'AVG(c.sales_det_asp) as asp, MONTH(c.sales_det_date) asp_date');

		$data['improved_sweet_and_spicy_11pcs_1'] = 0;
		$data['improved_sweet_and_spicy_11pcs_2'] = 0;
		$data['improved_sweet_and_spicy_11pcs_3'] = 0;
		$data['improved_sweet_and_spicy_11pcs_4'] = 0;
		$data['improved_sweet_and_spicy_11pcs_5'] = 0;
		$data['improved_sweet_and_spicy_11pcs_6'] = 0;
		$data['improved_sweet_and_spicy_11pcs_7'] = 0;
		$data['improved_sweet_and_spicy_11pcs_8'] = 0;
		$data['improved_sweet_and_spicy_11pcs_9'] = 0;
		$data['improved_sweet_and_spicy_11pcs_10'] = 0;
		$data['improved_sweet_and_spicy_11pcs_11'] = 0;
		$data['improved_sweet_and_spicy_11pcs_12'] = 0;
		$data['improved_sweet_and_spicy_11pcs_total'] = 0;
		$data['improved_sweet_and_spicy_11pcs_count'] = 0;
		$data['improved_sweet_and_spicy_11pcs_avg'] = 0;
		$data['improved_sweet_and_spicy_11pcs_min'] = 0;
		$data['improved_sweet_and_spicy_11pcs_max'] = 0;

		foreach($get_improved_sweet_and_spicy_11pcs as $row){
			$asp = $row->asp;
			$month = $row->asp_date;
			$data['reyap_11pcs_' . $month] = $asp;
			$data['reyap_11pcs_total'] += $asp;

			if($asp < $data['improved_sweet_and_spicy_11pcs_min'] || $data['improved_sweet_and_spicy_11pcs_count'] == 0){
				$data['improved_sweet_and_spicy_11pcs_min'] = $asp;
			}

			if($asp > $data['improved_sweet_and_spicy_11pcs_max'] || $data['improved_sweet_and_spicy_11pcs_count'] == 0){
				$data['improved_sweet_and_spicy_11pcs_max'] = $asp;
			}

			$data['improved_sweet_and_spicy_11pcs_count']++;
		}

		$data['improved_sweet_and_spicy_11pcs_avg'] = $data['improved_sweet_and_spicy_11pcs_total'] != 0 ? $data['improved_sweet_and_spicy_11pcs_total'] / $data['improved_sweet_and_spicy_11pcs_count'] : 0;

		$improved_sweet_and_spicy_11pcs_prev_year2 = $this->admin->get_data('comparative_price_tbl', array('comp_price_product' => '11 PC', 'comp_price_segment' => 'REYAL', 'comp_price_year' => $year - 2,	'bc_id'	=>	$bc_id), 
			FALSE, 'AVG(comp_price_jan) as jan_price, AVG(comp_price_feb) as feb_price, AVG(comp_price_mar) as mar_price, AVG(comp_price_apr) as apr_price, AVG(comp_price_may) as may_price, AVG(comp_price_jun) as jun_price, AVG(comp_price_jul) as jul_price, AVG(comp_price_aug) as aug_price, AVG(comp_price_sep) as sep_price, AVG(comp_price_oct) as oct_price, AVG(comp_price_nov) as nov_price, AVG(comp_price_dec) as dec_price');
		
		$data['improved_sweet_and_spicy_11pcs_prev_year2_jan'] = 0;
		$data['improved_sweet_and_spicy_11pcs_prev_year2_feb'] = 0;
		$data['improved_sweet_and_spicy_11pcs_prev_year2_mar'] = 0;
		$data['improved_sweet_and_spicy_11pcs_prev_year2_apr'] = 0;
		$data['improved_sweet_and_spicy_11pcs_prev_year2_may'] = 0;
		$data['improved_sweet_and_spicy_11pcs_prev_year2_jun'] = 0;
		$data['improved_sweet_and_spicy_11pcs_prev_year2_jul'] = 0;
		$data['improved_sweet_and_spicy_11pcs_prev_year2_aug'] = 0;
		$data['improved_sweet_and_spicy_11pcs_prev_year2_sep'] = 0;
		$data['improved_sweet_and_spicy_11pcs_prev_year2_oct'] = 0;
		$data['improved_sweet_and_spicy_11pcs_prev_year2_nov'] = 0;
		$data['improved_sweet_and_spicy_11pcs_prev_year2_dec'] = 0;
		$data['improved_sweet_and_spicy_11pcs_prev_year2_avg'] = 0;
		$data['improved_sweet_and_spicy_11pcs_prev_year2_min'] = 0;
		$data['improved_sweet_and_spicy_11pcs_prev_year2_max'] = 0;

		foreach($improved_sweet_and_spicy_11pcs_prev_year2 as $row){
			$data['improved_sweet_and_spicy_11pcs_prev_year2_jan'] = $row->jan_price;
			$data['improved_sweet_and_spicy_11pcs_prev_year2_feb'] = $row->feb_price;
			$data['improved_sweet_and_spicy_11pcs_prev_year2_mar'] = $row->mar_price;
			$data['improved_sweet_and_spicy_11pcs_prev_year2_apr'] = $row->apr_price;
			$data['improved_sweet_and_spicy_11pcs_prev_year2_may'] = $row->may_price;
			$data['improved_sweet_and_spicy_11pcs_prev_year2_jun'] = $row->jun_price;
			$data['improved_sweet_and_spicy_11pcs_prev_year2_jul'] = $row->jul_price;
			$data['improved_sweet_and_spicy_11pcs_prev_year2_aug'] = $row->aug_price;
			$data['improved_sweet_and_spicy_11pcs_prev_year2_sep'] = $row->sep_price;
			$data['improved_sweet_and_spicy_11pcs_prev_year2_oct'] = $row->oct_price;
			$data['improved_sweet_and_spicy_11pcs_prev_year2_nov'] = $row->nov_price;
			$data['improved_sweet_and_spicy_11pcs_prev_year2_dec'] = $row->dec_price;
			$data['improved_sweet_and_spicy_11pcs_prev_year2_avg'] = ($row->jan_price + $row->feb_price + $row->mar_price + $row->apr_price + $row->may_price + $row->jun_price + $row->jul_price + $row->aug_price + $row->sep_price + $row->oct_price + $row->nov_price + $row->dec_price) / 12;

			$data['improved_sweet_and_spicy_11pcs_prev_year2_min'] = min($row->jan_price, $row->feb_price, $row->mar_price, $row->apr_price, $row->may_price, $row->jun_price, $row->jul_price, $row->aug_price, $row->sep_price, $row->oct_price, $row->nov_price, $row->dec_price);

			$data['improved_sweet_and_spicy_11pcs_prev_year2_max'] = max($row->jan_price, $row->feb_price, $row->mar_price, $row->apr_price, $row->may_price, $row->jun_price, $row->jul_price, $row->aug_price, $row->sep_price, $row->oct_price, $row->nov_price, $row->dec_price);
		}


		$improved_sweet_and_spicy_11pcs_prev_year1 = $this->admin->get_data('comparative_price_tbl', array('comp_price_product' => '11 PC', 'comp_price_segment' => 'REYAL', 'comp_price_year' => $year - 1,	'bc_id'	=>	$bc_id), 
			FALSE, 'AVG(comp_price_jan) as jan_price, AVG(comp_price_feb) as feb_price, AVG(comp_price_mar) as mar_price, AVG(comp_price_apr) as apr_price, AVG(comp_price_may) as may_price, AVG(comp_price_jun) as jun_price, AVG(comp_price_jul) as jul_price, AVG(comp_price_aug) as aug_price, AVG(comp_price_sep) as sep_price, AVG(comp_price_oct) as oct_price, AVG(comp_price_nov) as nov_price, AVG(comp_price_dec) as dec_price');
		
		$data['improved_sweet_and_spicy_11pcs_prev_year1_jan'] = 0;
		$data['improved_sweet_and_spicy_11pcs_prev_year1_feb'] = 0;
		$data['improved_sweet_and_spicy_11pcs_prev_year1_mar'] = 0;
		$data['improved_sweet_and_spicy_11pcs_prev_year1_apr'] = 0;
		$data['improved_sweet_and_spicy_11pcs_prev_year1_may'] = 0;
		$data['improved_sweet_and_spicy_11pcs_prev_year1_jun'] = 0;
		$data['improved_sweet_and_spicy_11pcs_prev_year1_jul'] = 0;
		$data['improved_sweet_and_spicy_11pcs_prev_year1_aug'] = 0;
		$data['improved_sweet_and_spicy_11pcs_prev_year1_sep'] = 0;
		$data['improved_sweet_and_spicy_11pcs_prev_year1_oct'] = 0;
		$data['improved_sweet_and_spicy_11pcs_prev_year1_nov'] = 0;
		$data['improved_sweet_and_spicy_11pcs_prev_year1_dec'] = 0;
		$data['improved_sweet_and_spicy_11pcs_prev_year1_avg'] = 0;
		$data['improved_sweet_and_spicy_11pcs_prev_year1_min'] = 0;
		$data['improved_sweet_and_spicy_11pcs_prev_year1_max'] = 0;

		foreach($improved_sweet_and_spicy_11pcs_prev_year1 as $row){
			$data['improved_sweet_and_spicy_11pcs_prev_year1_jan'] = $row->jan_price;
			$data['improved_sweet_and_spicy_11pcs_prev_year1_feb'] = $row->feb_price;
			$data['improved_sweet_and_spicy_11pcs_prev_year1_mar'] = $row->mar_price;
			$data['improved_sweet_and_spicy_11pcs_prev_year1_apr'] = $row->apr_price;
			$data['improved_sweet_and_spicy_11pcs_prev_year1_may'] = $row->may_price;
			$data['improved_sweet_and_spicy_11pcs_prev_year1_jun'] = $row->jun_price;
			$data['improved_sweet_and_spicy_11pcs_prev_year1_jul'] = $row->jul_price;
			$data['improved_sweet_and_spicy_11pcs_prev_year1_aug'] = $row->aug_price;
			$data['improved_sweet_and_spicy_11pcs_prev_year1_sep'] = $row->sep_price;
			$data['improved_sweet_and_spicy_11pcs_prev_year1_oct'] = $row->oct_price;
			$data['improved_sweet_and_spicy_11pcs_prev_year1_nov'] = $row->nov_price;
			$data['improved_sweet_and_spicy_11pcs_prev_year1_dec'] = $row->dec_price;
			$data['improved_sweet_and_spicy_11pcs_prev_year1_avg'] = ($row->jan_price + $row->feb_price + $row->mar_price + $row->apr_price + $row->may_price + $row->jun_price + $row->jul_price + $row->aug_price + $row->sep_price + $row->oct_price + $row->nov_price + $row->dec_price) / 12;

			$data['improved_sweet_and_spicy_11pcs_prev_year1_min'] = min($row->jan_price, $row->feb_price, $row->mar_price, $row->apr_price, $row->may_price, $row->jun_price, $row->jul_price, $row->aug_price, $row->sep_price, $row->oct_price, $row->nov_price, $row->dec_price);

			$data['improved_sweet_and_spicy_11pcs_prev_year1_max'] = max($row->jan_price, $row->feb_price, $row->mar_price, $row->apr_price, $row->may_price, $row->jun_price, $row->jul_price, $row->aug_price, $row->sep_price, $row->oct_price, $row->nov_price, $row->dec_price);
		}


		/*REYAL Chooksies Cut ups*/
		$join_improved_sweet_and_spicy_cutups = array(
			'sales_item_tbl b' => 'a.sales_id = b.sales_id AND a.sales_status = 1 AND b.sales_item_status = 1 AND a.sales_year = ' . $year,
			'sales_details_tbl c' => 'b.sales_item_id = c.sales_item_id AND c.sales_det_status = 1',
			'material_tbl d' => 'b.material_id = d.material_id AND d.material_code = 10000150',
			'material_group_tbl e' => 'd.material_group_id = e.material_group_id AND e.material_group_name = \'NON MARINATED CUT UPS\'',
			'outlet_tbl f' => 'a.outlet_id = f.outlet_id AND f.bc_id = ' . $bc_id,
			'outlet_brand_tbl g' => 'f.outlet_id = g.outlet_id AND g.outlet_brand_status = 1',
			'brand_tbl h' => 'g.brand_id  = h.brand_id AND h.brand_name = \'REYAL\''
		);

		$get_improved_sweet_and_spicy_cutups = $this->admin->get_join('sales_tbl a', $join_improved_sweet_and_spicy_cutups, FALSE, FALSE, 'YEAR(c.sales_det_date), MONTH(c.sales_det_date)', 'AVG(c.sales_det_asp) as asp, MONTH(c.sales_det_date) asp_date');

		$data['improved_sweet_and_spicy_cutups1'] = 0;
		$data['improved_sweet_and_spicy_cutups2'] = 0;
		$data['improved_sweet_and_spicy_cutups3'] = 0;
		$data['improved_sweet_and_spicy_cutups4'] = 0;
		$data['improved_sweet_and_spicy_cutups5'] = 0;
		$data['improved_sweet_and_spicy_cutups6'] = 0;
		$data['improved_sweet_and_spicy_cutups7'] = 0;
		$data['improved_sweet_and_spicy_cutups8'] = 0;
		$data['improved_sweet_and_spicy_cutups9'] = 0;
		$data['improved_sweet_and_spicy_cutups10'] = 0;
		$data['improved_sweet_and_spicy_cutups11'] = 0;
		$data['improved_sweet_and_spicy_cutups12'] = 0;
		$data['improved_sweet_and_spicy_cutups_total'] = 0;
		$data['improved_sweet_and_spicy_cutups_count'] = 0;
		$data['improved_sweet_and_spicy_cutups_avg'] = 0;
		$data['improved_sweet_and_spicy_cutups_min'] = 0;
		$data['improved_sweet_and_spicy_cutups_max'] = 0;

		foreach($get_improved_sweet_and_spicy_cutups as $row){
			$asp = $row->asp;
			$month = $row->asp_date;
			$data['improved_sweet_and_spicy_cutups' . $month] = $asp;
			$data['improved_sweet_and_spicy_cutups_total'] += $asp;

			if($asp < $data['improved_sweet_and_spicy_cutups_min'] || $data['improved_sweet_and_spicy_cutups_count'] == 0){
				$data['improved_sweet_and_spicy_cutups_min'] = $asp;
			}

			if($asp > $data['improved_sweet_and_spicy_cutups_max'] || $data['improved_sweet_and_spicy_cutups_count'] == 0){
				$data['improved_sweet_and_spicy_cutups_max'] = $asp;
			}

			$data['improved_sweet_and_spicy_cutups_count']++;
		}

		$data['improved_sweet_and_spicy_cutups_avg'] = $data['improved_sweet_and_spicy_cutups_total'] != 0 ? $data['improved_sweet_and_spicy_cutups_total'] / $data['improved_sweet_and_spicy_cutups_count'] : 0;


		$get_improved_sweet_and_spicy_cutups = $this->admin->get_join('sales_tbl a', $join_improved_sweet_and_spicy_cutups, FALSE, FALSE, 'YEAR(c.sales_det_date), MONTH(c.sales_det_date)', 'AVG(c.sales_det_asp) as asp, MONTH(c.sales_det_date) asp_date');

		$data['improved_sweet_and_spicy_cutups1'] = 0;
		$data['improved_sweet_and_spicy_cutups2'] = 0;
		$data['improved_sweet_and_spicy_cutups3'] = 0;
		$data['improved_sweet_and_spicy_cutups4'] = 0;
		$data['improved_sweet_and_spicy_cutups5'] = 0;
		$data['improved_sweet_and_spicy_cutups6'] = 0;
		$data['improved_sweet_and_spicy_cutups7'] = 0;
		$data['improved_sweet_and_spicy_cutups8'] = 0;
		$data['improved_sweet_and_spicy_cutups9'] = 0;
		$data['improved_sweet_and_spicy_cutups10'] = 0;
		$data['improved_sweet_and_spicy_cutups11'] = 0;
		$data['improved_sweet_and_spicy_cutups12'] = 0;
		$data['improved_sweet_and_spicy_cutups_total'] = 0;
		$data['improved_sweet_and_spicy_cutups_count'] = 0;
		$data['improved_sweet_and_spicy_cutups_avg'] = 0;
		$data['improved_sweet_and_spicy_cutups_min'] = 0;
		$data['improved_sweet_and_spicy_cutups_max'] = 0;

		foreach($get_improved_sweet_and_spicy_cutups as $row){
			$asp = $row->asp;
			$month = $row->asp_date;
			$data['improved_sweet_and_spicy_cutups' . $month] = $asp;
			$data['improved_sweet_and_spicy_cutups_total'] += $asp;

			if($asp < $data['improved_sweet_and_spicy_cutups_min'] || $data['improved_sweet_and_spicy_cutups_count'] == 0){
				$data['improved_sweet_and_spicy_cutups_min'] = $asp;
			}

			if($asp > $data['improved_sweet_and_spicy_cutups_max'] || $data['improved_sweet_and_spicy_cutups_count'] == 0){
				$data['improved_sweet_and_spicy_cutups_max'] = $asp;
			}

			$data['improved_sweet_and_spicy_cutups_count']++;
		}

		$data['improved_sweet_and_spicy_cutups_avg'] = $data['improved_sweet_and_spicy_cutups_total'] != 0 ? $data['improved_sweet_and_spicy_cutups_total'] / $data['improved_sweet_and_spicy_cutups_count'] : 0;

		$improved_sweet_and_spicy_cutups_prev_year2 = $this->admin->get_data('comparative_price_tbl', array('comp_price_product' => 'CHOOKSIES CUT UPS', 'comp_price_segment' => 'REYAL', 'comp_price_year' => $year - 2,	'bc_id'	=>	$bc_id), 
			FALSE, 'AVG(comp_price_jan) as jan_price, AVG(comp_price_feb) as feb_price, AVG(comp_price_mar) as mar_price, AVG(comp_price_apr) as apr_price, AVG(comp_price_may) as may_price, AVG(comp_price_jun) as jun_price, AVG(comp_price_jul) as jul_price, AVG(comp_price_aug) as aug_price, AVG(comp_price_sep) as sep_price, AVG(comp_price_oct) as oct_price, AVG(comp_price_nov) as nov_price, AVG(comp_price_dec) as dec_price');
		
		$data['improved_sweet_and_spicy_cutups_prev_year2_jan'] = 0;
		$data['improved_sweet_and_spicy_cutups_prev_year2_feb'] = 0;
		$data['improved_sweet_and_spicy_cutups_prev_year2_mar'] = 0;
		$data['improved_sweet_and_spicy_cutups_prev_year2_apr'] = 0;
		$data['improved_sweet_and_spicy_cutups_prev_year2_may'] = 0;
		$data['improved_sweet_and_spicy_cutups_prev_year2_jun'] = 0;
		$data['improved_sweet_and_spicy_cutups_prev_year2_jul'] = 0;
		$data['improved_sweet_and_spicy_cutups_prev_year2_aug'] = 0;
		$data['improved_sweet_and_spicy_cutups_prev_year2_sep'] = 0;
		$data['improved_sweet_and_spicy_cutups_prev_year2_oct'] = 0;
		$data['improved_sweet_and_spicy_cutups_prev_year2_nov'] = 0;
		$data['improved_sweet_and_spicy_cutups_prev_year2_dec'] = 0;
		$data['improved_sweet_and_spicy_cutups_prev_year2_avg'] = 0;
		$data['improved_sweet_and_spicy_cutups_prev_year2_min'] = 0;
		$data['improved_sweet_and_spicy_cutups_prev_year2_max'] = 0;

		foreach($improved_sweet_and_spicy_cutups_prev_year2 as $row){
			$data['improved_sweet_and_spicy_cutups_prev_year2_jan'] = $row->jan_price;
			$data['improved_sweet_and_spicy_cutups_prev_year2_feb'] = $row->feb_price;
			$data['improved_sweet_and_spicy_cutups_prev_year2_mar'] = $row->mar_price;
			$data['improved_sweet_and_spicy_cutups_prev_year2_apr'] = $row->apr_price;
			$data['improved_sweet_and_spicy_cutups_prev_year2_may'] = $row->may_price;
			$data['improved_sweet_and_spicy_cutups_prev_year2_jun'] = $row->jun_price;
			$data['improved_sweet_and_spicy_cutups_prev_year2_jul'] = $row->jul_price;
			$data['improved_sweet_and_spicy_cutups_prev_year2_aug'] = $row->aug_price;
			$data['improved_sweet_and_spicy_cutups_prev_year2_sep'] = $row->sep_price;
			$data['improved_sweet_and_spicy_cutups_prev_year2_oct'] = $row->oct_price;
			$data['improved_sweet_and_spicy_cutups_prev_year2_nov'] = $row->nov_price;
			$data['improved_sweet_and_spicy_cutups_prev_year2_dec'] = $row->dec_price;
			$data['improved_sweet_and_spicy_cutups_prev_year2_avg'] = ($row->jan_price + $row->feb_price + $row->mar_price + $row->apr_price + $row->may_price + $row->jun_price + $row->jul_price + $row->aug_price + $row->sep_price + $row->oct_price + $row->nov_price + $row->dec_price) / 12;

			$data['improved_sweet_and_spicy_cutups_prev_year2_min'] = min($row->jan_price, $row->feb_price, $row->mar_price, $row->apr_price, $row->may_price, $row->jun_price, $row->jul_price, $row->aug_price, $row->sep_price, $row->oct_price, $row->nov_price, $row->dec_price);

			$data['improved_sweet_and_spicy_cutups_prev_year2_max'] = max($row->jan_price, $row->feb_price, $row->mar_price, $row->apr_price, $row->may_price, $row->jun_price, $row->jul_price, $row->aug_price, $row->sep_price, $row->oct_price, $row->nov_price, $row->dec_price);
		}

		$improved_sweet_and_spicy_cutups_prev_year1 = $this->admin->get_data('comparative_price_tbl', array('comp_price_product' => 'CHOOKSIES CUT UPS', 'comp_price_segment' => 'REYAL', 'comp_price_year' => $year - 1,	'bc_id'	=>	$bc_id), 
			FALSE, 'AVG(comp_price_jan) as jan_price, AVG(comp_price_feb) as feb_price, AVG(comp_price_mar) as mar_price, AVG(comp_price_apr) as apr_price, AVG(comp_price_may) as may_price, AVG(comp_price_jun) as jun_price, AVG(comp_price_jul) as jul_price, AVG(comp_price_aug) as aug_price, AVG(comp_price_sep) as sep_price, AVG(comp_price_oct) as oct_price, AVG(comp_price_nov) as nov_price, AVG(comp_price_dec) as dec_price');
		
		$data['improved_sweet_and_spicy_cutups_prev_year1_jan'] = 0;
		$data['improved_sweet_and_spicy_cutups_prev_year1_feb'] = 0;
		$data['improved_sweet_and_spicy_cutups_prev_year1_mar'] = 0;
		$data['improved_sweet_and_spicy_cutups_prev_year1_apr'] = 0;
		$data['improved_sweet_and_spicy_cutups_prev_year1_may'] = 0;
		$data['improved_sweet_and_spicy_cutups_prev_year1_jun'] = 0;
		$data['improved_sweet_and_spicy_cutups_prev_year1_jul'] = 0;
		$data['improved_sweet_and_spicy_cutups_prev_year1_aug'] = 0;
		$data['improved_sweet_and_spicy_cutups_prev_year1_sep'] = 0;
		$data['improved_sweet_and_spicy_cutups_prev_year1_oct'] = 0;
		$data['improved_sweet_and_spicy_cutups_prev_year1_nov'] = 0;
		$data['improved_sweet_and_spicy_cutups_prev_year1_dec'] = 0;
		$data['improved_sweet_and_spicy_cutups_prev_year1_avg'] = 0;
		$data['improved_sweet_and_spicy_cutups_prev_year1_min'] = 0;
		$data['improved_sweet_and_spicy_cutups_prev_year1_max'] = 0;

		foreach($improved_sweet_and_spicy_cutups_prev_year1 as $row){
			$data['improved_sweet_and_spicy_cutups_prev_year1_jan'] = $row->jan_price;
			$data['improved_sweet_and_spicy_cutups_prev_year1_feb'] = $row->feb_price;
			$data['improved_sweet_and_spicy_cutups_prev_year1_mar'] = $row->mar_price;
			$data['improved_sweet_and_spicy_cutups_prev_year1_apr'] = $row->apr_price;
			$data['improved_sweet_and_spicy_cutups_prev_year1_may'] = $row->may_price;
			$data['improved_sweet_and_spicy_cutups_prev_year1_jun'] = $row->jun_price;
			$data['improved_sweet_and_spicy_cutups_prev_year1_jul'] = $row->jul_price;
			$data['improved_sweet_and_spicy_cutups_prev_year1_aug'] = $row->aug_price;
			$data['improved_sweet_and_spicy_cutups_prev_year1_sep'] = $row->sep_price;
			$data['improved_sweet_and_spicy_cutups_prev_year1_oct'] = $row->oct_price;
			$data['improved_sweet_and_spicy_cutups_prev_year1_nov'] = $row->nov_price;
			$data['improved_sweet_and_spicy_cutups_prev_year1_dec'] = $row->dec_price;
			$data['improved_sweet_and_spicy_cutups_prev_year1_avg'] = ($row->jan_price + $row->feb_price + $row->mar_price + $row->apr_price + $row->may_price + $row->jun_price + $row->jul_price + $row->aug_price + $row->sep_price + $row->oct_price + $row->nov_price + $row->dec_price) / 12;

			$data['improved_sweet_and_spicy_cutups_prev_year1_min'] = min($row->jan_price, $row->feb_price, $row->mar_price, $row->apr_price, $row->may_price, $row->jun_price, $row->jul_price, $row->aug_price, $row->sep_price, $row->oct_price, $row->nov_price, $row->dec_price);

			$data['improved_sweet_and_spicy_cutups_prev_year1_max'] = max($row->jan_price, $row->feb_price, $row->mar_price, $row->apr_price, $row->may_price, $row->jun_price, $row->jul_price, $row->aug_price, $row->sep_price, $row->oct_price, $row->nov_price, $row->dec_price);
		}

		/*SUPERMARKET Marinated Raw*/
		$join_smkt_raw = array(
			'sales_item_tbl b' => 'a.sales_id = b.sales_id AND a.sales_status = 1 AND b.sales_item_status = 1 AND a.sales_year = ' . $year,
			'sales_details_tbl c' => 'b.sales_item_id = c.sales_item_id AND c.sales_det_status = 1',
			'material_tbl d' => 'b.material_id = d.material_id',
			'material_group_tbl e' => 'd.material_group_id = e.material_group_id AND e.material_group_name = \'MARINATED CHICKEN (RAW)\'',
			'outlet_tbl f' => 'a.outlet_id = f.outlet_id AND f.bc_id = ' . $bc_id,
			'outlet_brand_tbl g' => 'f.outlet_id = g.outlet_id AND g.outlet_brand_status = 1',
			'brand_tbl h' => 'g.brand_id  = h.brand_id AND h.brand_name = \'SUPERMARKET\''
		);

		$get_smkt_raw = $this->admin->get_join('sales_tbl a', $join_smkt_raw, FALSE, FALSE, 'YEAR(c.sales_det_date), MONTH(c.sales_det_date)', 'AVG(c.sales_det_asp) as asp, MONTH(c.sales_det_date) asp_date');

		$data['smkt_raw1'] = 0;
		$data['smkt_raw2'] = 0;
		$data['smkt_raw3'] = 0;
		$data['smkt_raw4'] = 0;
		$data['smkt_raw5'] = 0;
		$data['smkt_raw6'] = 0;
		$data['smkt_raw7'] = 0;
		$data['smkt_raw8'] = 0;
		$data['smkt_raw9'] = 0;
		$data['smkt_raw10'] = 0;
		$data['smkt_raw11'] = 0;
		$data['smkt_raw12'] = 0;
		$data['smkt_raw_total'] = 0;
		$data['smkt_raw_count'] = 0;
		$data['smkt_raw_avg'] = 0;
		$data['smkt_raw_min'] = 0;
		$data['smkt_raw_max'] = 0;

		foreach($get_smkt_raw as $row){
			$asp = $row->asp;
			$month = $row->asp_date;
			$data['smkt_raw' . $month] = $asp;
			$data['smkt_raw_total'] += $asp;

			if($asp < $data['smkt_raw_min'] || $data['smkt_raw_count'] == 0){
				$data['smkt_raw_min'] = $asp;
			}

			if($asp > $data['smkt_raw_max'] || $data['smkt_raw_count'] == 0){
				$data['smkt_raw_max'] = $asp;
			}

			$data['smkt_raw_count']++;
		}

		$data['smkt_raw_avg'] = $data['smkt_raw_total'] != 0 ? $data['smkt_raw_total'] / $data['smkt_raw_count'] : 0;


		$smkt_raw_prev_year2 = $this->admin->get_data('comparative_price_tbl', array('comp_price_product' => 'SMKT - MARINATED CHICKEN RAW', 'comp_price_segment' => 'REYAL', 'comp_price_year' => $year - 2,	'bc_id'	=>	$bc_id), 
			FALSE, 'AVG(comp_price_jan) as jan_price, AVG(comp_price_feb) as feb_price, AVG(comp_price_mar) as mar_price, AVG(comp_price_apr) as apr_price, AVG(comp_price_may) as may_price, AVG(comp_price_jun) as jun_price, AVG(comp_price_jul) as jul_price, AVG(comp_price_aug) as aug_price, AVG(comp_price_sep) as sep_price, AVG(comp_price_oct) as oct_price, AVG(comp_price_nov) as nov_price, AVG(comp_price_dec) as dec_price');
		
		$data['smkt_raw_prev_year2_jan'] = 0;
		$data['smkt_raw_prev_year2_feb'] = 0;
		$data['smkt_raw_prev_year2_mar'] = 0;
		$data['smkt_raw_prev_year2_apr'] = 0;
		$data['smkt_raw_prev_year2_may'] = 0;
		$data['smkt_raw_prev_year2_jun'] = 0;
		$data['smkt_raw_prev_year2_jul'] = 0;
		$data['smkt_raw_prev_year2_aug'] = 0;
		$data['smkt_raw_prev_year2_sep'] = 0;
		$data['smkt_raw_prev_year2_oct'] = 0;
		$data['smkt_raw_prev_year2_nov'] = 0;
		$data['smkt_raw_prev_year2_dec'] = 0;
		$data['smkt_raw_prev_year2_avg'] = 0;
		$data['smkt_raw_prev_year2_min'] = 0;
		$data['smkt_raw_prev_year2_max'] = 0;

		foreach($smkt_raw_prev_year2 as $row){
			$data['smkt_raw_prev_year2_jan'] = $row->jan_price;
			$data['smkt_raw_prev_year2_feb'] = $row->feb_price;
			$data['smkt_raw_prev_year2_mar'] = $row->mar_price;
			$data['smkt_raw_prev_year2_apr'] = $row->apr_price;
			$data['smkt_raw_prev_year2_may'] = $row->may_price;
			$data['smkt_raw_prev_year2_jun'] = $row->jun_price;
			$data['smkt_raw_prev_year2_jul'] = $row->jul_price;
			$data['smkt_raw_prev_year2_aug'] = $row->aug_price;
			$data['smkt_raw_prev_year2_sep'] = $row->sep_price;
			$data['smkt_raw_prev_year2_oct'] = $row->oct_price;
			$data['smkt_raw_prev_year2_nov'] = $row->nov_price;
			$data['smkt_raw_prev_year2_dec'] = $row->dec_price;
			$data['smkt_raw_prev_year2_avg'] = ($row->jan_price + $row->feb_price + $row->mar_price + $row->apr_price + $row->may_price + $row->jun_price + $row->jul_price + $row->aug_price + $row->sep_price + $row->oct_price + $row->nov_price + $row->dec_price) / 12;

			$data['smkt_raw_prev_year2_min'] = min($row->jan_price, $row->feb_price, $row->mar_price, $row->apr_price, $row->may_price, $row->jun_price, $row->jul_price, $row->aug_price, $row->sep_price, $row->oct_price, $row->nov_price, $row->dec_price);

			$data['smkt_raw_prev_year2_max'] = max($row->jan_price, $row->feb_price, $row->mar_price, $row->apr_price, $row->may_price, $row->jun_price, $row->jul_price, $row->aug_price, $row->sep_price, $row->oct_price, $row->nov_price, $row->dec_price);
		}


		$smkt_raw_prev_year1 = $this->admin->get_data('comparative_price_tbl', array('comp_price_product' => 'SMKT - MARINATED CHICKEN RAW', 'comp_price_segment' => 'REYAL', 'comp_price_year' => $year - 1,	'bc_id'	=>	$bc_id), 
			FALSE, 'AVG(comp_price_jan) as jan_price, AVG(comp_price_feb) as feb_price, AVG(comp_price_mar) as mar_price, AVG(comp_price_apr) as apr_price, AVG(comp_price_may) as may_price, AVG(comp_price_jun) as jun_price, AVG(comp_price_jul) as jul_price, AVG(comp_price_aug) as aug_price, AVG(comp_price_sep) as sep_price, AVG(comp_price_oct) as oct_price, AVG(comp_price_nov) as nov_price, AVG(comp_price_dec) as dec_price');
		
		$data['smkt_raw_prev_year1_jan'] = 0;
		$data['smkt_raw_prev_year1_feb'] = 0;
		$data['smkt_raw_prev_year1_mar'] = 0;
		$data['smkt_raw_prev_year1_apr'] = 0;
		$data['smkt_raw_prev_year1_may'] = 0;
		$data['smkt_raw_prev_year1_jun'] = 0;
		$data['smkt_raw_prev_year1_jul'] = 0;
		$data['smkt_raw_prev_year1_aug'] = 0;
		$data['smkt_raw_prev_year1_sep'] = 0;
		$data['smkt_raw_prev_year1_oct'] = 0;
		$data['smkt_raw_prev_year1_nov'] = 0;
		$data['smkt_raw_prev_year1_dec'] = 0;
		$data['smkt_raw_prev_year1_avg'] = 0;
		$data['smkt_raw_prev_year1_min'] = 0;
		$data['smkt_raw_prev_year1_max'] = 0;

		foreach($smkt_raw_prev_year1 as $row){
			$data['smkt_raw_prev_year1_jan'] = $row->jan_price;
			$data['smkt_raw_prev_year1_feb'] = $row->feb_price;
			$data['smkt_raw_prev_year1_mar'] = $row->mar_price;
			$data['smkt_raw_prev_year1_apr'] = $row->apr_price;
			$data['smkt_raw_prev_year1_may'] = $row->may_price;
			$data['smkt_raw_prev_year1_jun'] = $row->jun_price;
			$data['smkt_raw_prev_year1_jul'] = $row->jul_price;
			$data['smkt_raw_prev_year1_aug'] = $row->aug_price;
			$data['smkt_raw_prev_year1_sep'] = $row->sep_price;
			$data['smkt_raw_prev_year1_oct'] = $row->oct_price;
			$data['smkt_raw_prev_year1_nov'] = $row->nov_price;
			$data['smkt_raw_prev_year1_dec'] = $row->dec_price;
			$data['smkt_raw_prev_year1_avg'] = ($row->jan_price + $row->feb_price + $row->mar_price + $row->apr_price + $row->may_price + $row->jun_price + $row->jul_price + $row->aug_price + $row->sep_price + $row->oct_price + $row->nov_price + $row->dec_price) / 12;

			$data['smkt_raw_prev_year1_min'] = min($row->jan_price, $row->feb_price, $row->mar_price, $row->apr_price, $row->may_price, $row->jun_price, $row->jul_price, $row->aug_price, $row->sep_price, $row->oct_price, $row->nov_price, $row->dec_price);

			$data['smkt_raw_prev_year1_max'] = max($row->jan_price, $row->feb_price, $row->mar_price, $row->apr_price, $row->may_price, $row->jun_price, $row->jul_price, $row->aug_price, $row->sep_price, $row->oct_price, $row->nov_price, $row->dec_price);
		}


		/*SUPERMARKET Liver / Gizzard*/
		$join_smkt_liver = array(
			'sales_item_tbl b' => 'a.sales_id = b.sales_id AND a.sales_status = 1 AND b.sales_item_status = 1 AND a.sales_year = ' . $year,
			'sales_details_tbl c' => 'b.sales_item_id = c.sales_item_id AND c.sales_det_status = 1',
			'material_tbl d' => 'b.material_id = d.material_id',
			'material_group_tbl e' => 'd.material_group_id = e.material_group_id AND e.material_group_name = \'LIVER / GIZZARD\'',
			'outlet_tbl f' => 'a.outlet_id = f.outlet_id AND f.bc_id = ' . $bc_id,
			'outlet_brand_tbl g' => 'f.outlet_id = g.outlet_id AND g.outlet_brand_status = 1',
			'brand_tbl h' => 'g.brand_id  = h.brand_id AND h.brand_name = \'SUPERMARKET\''
		);

		$get_smkt_liver = $this->admin->get_join('sales_tbl a', $join_smkt_liver, FALSE, FALSE, 'YEAR(c.sales_det_date), MONTH(c.sales_det_date)', 'AVG(c.sales_det_asp) as asp, MONTH(c.sales_det_date) asp_date');

		$data['smkt_liver1'] = 0;
		$data['smkt_liver2'] = 0;
		$data['smkt_liver3'] = 0;
		$data['smkt_liver4'] = 0;
		$data['smkt_liver5'] = 0;
		$data['smkt_liver6'] = 0;
		$data['smkt_liver7'] = 0;
		$data['smkt_liver8'] = 0;
		$data['smkt_liver9'] = 0;
		$data['smkt_liver10'] = 0;
		$data['smkt_liver11'] = 0;
		$data['smkt_liver12'] = 0;
		$data['smkt_liver_total'] = 0;
		$data['smkt_liver_count'] = 0;
		$data['smkt_liver_avg'] = 0;
		$data['smkt_liver_min'] = 0;
		$data['smkt_liver_max'] = 0;

		foreach($get_smkt_liver as $row){
			$asp = $row->asp;
			$month = $row->asp_date;
			$data['smkt_liver' . $month] = $asp;
			$data['smkt_liver_total'] += $asp;

			if($asp < $data['smkt_liver_min'] || $data['smkt_liver_count'] == 0){
				$data['smkt_liver_min'] = $asp;
			}

			if($asp > $data['smkt_liver_max'] || $data['smkt_liver_count'] == 0){
				$data['smkt_liver_max'] = $asp;
			}

			$data['smkt_liver_count']++;
		}

		$data['smkt_liver_avg'] = $data['smkt_liver_total'] != 0 ? $data['smkt_liver_total'] / $data['smkt_liver_count'] : 0;


		$smkt_liver_prev_year2 = $this->admin->get_data('comparative_price_tbl', array('comp_price_product' => 'SMKT - LIVER / GIZZARD', 'comp_price_segment' => 'REYAL', 'comp_price_year' => $year - 2,	'bc_id'	=>	$bc_id), 
			FALSE, 'AVG(comp_price_jan) as jan_price, AVG(comp_price_feb) as feb_price, AVG(comp_price_mar) as mar_price, AVG(comp_price_apr) as apr_price, AVG(comp_price_may) as may_price, AVG(comp_price_jun) as jun_price, AVG(comp_price_jul) as jul_price, AVG(comp_price_aug) as aug_price, AVG(comp_price_sep) as sep_price, AVG(comp_price_oct) as oct_price, AVG(comp_price_nov) as nov_price, AVG(comp_price_dec) as dec_price');
		
		$data['smkt_liver_prev_year2_jan'] = 0;
		$data['smkt_liver_prev_year2_feb'] = 0;
		$data['smkt_liver_prev_year2_mar'] = 0;
		$data['smkt_liver_prev_year2_apr'] = 0;
		$data['smkt_liver_prev_year2_may'] = 0;
		$data['smkt_liver_prev_year2_jun'] = 0;
		$data['smkt_liver_prev_year2_jul'] = 0;
		$data['smkt_liver_prev_year2_aug'] = 0;
		$data['smkt_liver_prev_year2_sep'] = 0;
		$data['smkt_liver_prev_year2_oct'] = 0;
		$data['smkt_liver_prev_year2_nov'] = 0;
		$data['smkt_liver_prev_year2_dec'] = 0;
		$data['smkt_liver_prev_year2_avg'] = 0;
		$data['smkt_liver_prev_year2_min'] = 0;
		$data['smkt_liver_prev_year2_max'] = 0;

		foreach($smkt_liver_prev_year2 as $row){
			$data['smkt_liver_prev_year2_jan'] = $row->jan_price;
			$data['smkt_liver_prev_year2_feb'] = $row->feb_price;
			$data['smkt_liver_prev_year2_mar'] = $row->mar_price;
			$data['smkt_liver_prev_year2_apr'] = $row->apr_price;
			$data['smkt_liver_prev_year2_may'] = $row->may_price;
			$data['smkt_liver_prev_year2_jun'] = $row->jun_price;
			$data['smkt_liver_prev_year2_jul'] = $row->jul_price;
			$data['smkt_liver_prev_year2_aug'] = $row->aug_price;
			$data['smkt_liver_prev_year2_sep'] = $row->sep_price;
			$data['smkt_liver_prev_year2_oct'] = $row->oct_price;
			$data['smkt_liver_prev_year2_nov'] = $row->nov_price;
			$data['smkt_liver_prev_year2_dec'] = $row->dec_price;
			$data['smkt_liver_prev_year2_avg'] = ($row->jan_price + $row->feb_price + $row->mar_price + $row->apr_price + $row->may_price + $row->jun_price + $row->jul_price + $row->aug_price + $row->sep_price + $row->oct_price + $row->nov_price + $row->dec_price) / 12;

			$data['smkt_liver_prev_year2_min'] = min($row->jan_price, $row->feb_price, $row->mar_price, $row->apr_price, $row->may_price, $row->jun_price, $row->jul_price, $row->aug_price, $row->sep_price, $row->oct_price, $row->nov_price, $row->dec_price);

			$data['smkt_liver_prev_year2_max'] = max($row->jan_price, $row->feb_price, $row->mar_price, $row->apr_price, $row->may_price, $row->jun_price, $row->jul_price, $row->aug_price, $row->sep_price, $row->oct_price, $row->nov_price, $row->dec_price);
		}


		$smkt_liver_prev_year1 = $this->admin->get_data('comparative_price_tbl', array('comp_price_product' => 'SMKT - LIVER / GIZZARD', 'comp_price_segment' => 'REYAL', 'comp_price_year' => $year - 1,	'bc_id'	=>	$bc_id), 
			FALSE, 'AVG(comp_price_jan) as jan_price, AVG(comp_price_feb) as feb_price, AVG(comp_price_mar) as mar_price, AVG(comp_price_apr) as apr_price, AVG(comp_price_may) as may_price, AVG(comp_price_jun) as jun_price, AVG(comp_price_jul) as jul_price, AVG(comp_price_aug) as aug_price, AVG(comp_price_sep) as sep_price, AVG(comp_price_oct) as oct_price, AVG(comp_price_nov) as nov_price, AVG(comp_price_dec) as dec_price');
		
		$data['smkt_liver_prev_year1_jan'] = 0;
		$data['smkt_liver_prev_year1_feb'] = 0;
		$data['smkt_liver_prev_year1_mar'] = 0;
		$data['smkt_liver_prev_year1_apr'] = 0;
		$data['smkt_liver_prev_year1_may'] = 0;
		$data['smkt_liver_prev_year1_jun'] = 0;
		$data['smkt_liver_prev_year1_jul'] = 0;
		$data['smkt_liver_prev_year1_aug'] = 0;
		$data['smkt_liver_prev_year1_sep'] = 0;
		$data['smkt_liver_prev_year1_oct'] = 0;
		$data['smkt_liver_prev_year1_nov'] = 0;
		$data['smkt_liver_prev_year1_dec'] = 0;
		$data['smkt_liver_prev_year1_avg'] = 0;
		$data['smkt_liver_prev_year1_min'] = 0;
		$data['smkt_liver_prev_year1_max'] = 0;

		foreach($smkt_liver_prev_year1 as $row){
			$data['smkt_liver_prev_year1_jan'] = $row->jan_price;
			$data['smkt_liver_prev_year1_feb'] = $row->feb_price;
			$data['smkt_liver_prev_year1_mar'] = $row->mar_price;
			$data['smkt_liver_prev_year1_apr'] = $row->apr_price;
			$data['smkt_liver_prev_year1_may'] = $row->may_price;
			$data['smkt_liver_prev_year1_jun'] = $row->jun_price;
			$data['smkt_liver_prev_year1_jul'] = $row->jul_price;
			$data['smkt_liver_prev_year1_aug'] = $row->aug_price;
			$data['smkt_liver_prev_year1_sep'] = $row->sep_price;
			$data['smkt_liver_prev_year1_oct'] = $row->oct_price;
			$data['smkt_liver_prev_year1_nov'] = $row->nov_price;
			$data['smkt_liver_prev_year1_dec'] = $row->dec_price;
			$data['smkt_liver_prev_year1_avg'] = ($row->jan_price + $row->feb_price + $row->mar_price + $row->apr_price + $row->may_price + $row->jun_price + $row->jul_price + $row->aug_price + $row->sep_price + $row->oct_price + $row->nov_price + $row->dec_price) / 12;

			$data['smkt_liver_prev_year1_min'] = min($row->jan_price, $row->feb_price, $row->mar_price, $row->apr_price, $row->may_price, $row->jun_price, $row->jul_price, $row->aug_price, $row->sep_price, $row->oct_price, $row->nov_price, $row->dec_price);

			$data['smkt_liver_prev_year1_max'] = max($row->jan_price, $row->feb_price, $row->mar_price, $row->apr_price, $row->may_price, $row->jun_price, $row->jul_price, $row->aug_price, $row->sep_price, $row->oct_price, $row->nov_price, $row->dec_price);
		}


		/*SUPERMARKET Marinated Cut ups*/
		$join_smkt_marinated = array(
			'sales_item_tbl b' => 'a.sales_id = b.sales_id AND a.sales_status = 1 AND b.sales_item_status = 1 AND a.sales_year = ' . $year,
			'sales_details_tbl c' => 'b.sales_item_id = c.sales_item_id AND c.sales_det_status = 1',
			'material_tbl d' => 'b.material_id = d.material_id',
			'material_group_tbl e' => 'd.material_group_id = e.material_group_id AND e.material_group_name = \'MARINATED CUT UPS\'',
			'outlet_tbl f' => 'a.outlet_id = f.outlet_id AND f.bc_id = ' . $bc_id,
			'outlet_brand_tbl g' => 'f.outlet_id = g.outlet_id AND g.outlet_brand_status = 1',
			'brand_tbl h' => 'g.brand_id  = h.brand_id AND h.brand_name = \'SUPERMARKET\''
		);

		$get_smkt_marinated = $this->admin->get_join('sales_tbl a', $join_smkt_marinated, FALSE, FALSE, 'YEAR(c.sales_det_date), MONTH(c.sales_det_date)', 'AVG(c.sales_det_asp) as asp, MONTH(c.sales_det_date) asp_date');

		$data['smkt_marinated1'] = 0;
		$data['smkt_marinated2'] = 0;
		$data['smkt_marinated3'] = 0;
		$data['smkt_marinated4'] = 0;
		$data['smkt_marinated5'] = 0;
		$data['smkt_marinated6'] = 0;
		$data['smkt_marinated7'] = 0;
		$data['smkt_marinated8'] = 0;
		$data['smkt_marinated9'] = 0;
		$data['smkt_marinated10'] = 0;
		$data['smkt_marinated11'] = 0;
		$data['smkt_marinated12'] = 0;
		$data['smkt_marinated_total'] = 0;
		$data['smkt_marinated_count'] = 0;
		$data['smkt_marinated_avg'] = 0;
		$data['smkt_marinated_min'] = 0;
		$data['smkt_marinated_max'] = 0;

		foreach($get_smkt_marinated as $row){
			$asp = $row->asp;
			$month = $row->asp_date;
			$data['smkt_marinated' . $month] = $asp;
			$data['smkt_marinated_total'] += $asp;

			if($asp < $data['smkt_marinated_min'] || $data['smkt_marinated_count'] == 0){
				$data['smkt_marinated_min'] = $asp;
			}

			if($asp > $data['smkt_marinated_max'] || $data['smkt_marinated_count'] == 0){
				$data['smkt_marinated_max'] = $asp;
			}

			$data['smkt_marinated_count']++;
		}

		$data['smkt_marinated_avg'] = $data['smkt_marinated_total'] != 0 ? $data['smkt_marinated_total'] / $data['smkt_marinated_count'] : 0;

		$smkt_marinated_prev_year2 = $this->admin->get_data('comparative_price_tbl', array('comp_price_product' => 'SMKT - MAR CUT UPS', 'comp_price_segment' => 'REYAL', 'comp_price_year' => $year - 2,	'bc_id'	=>	$bc_id), 
			FALSE, 'AVG(comp_price_jan) as jan_price, AVG(comp_price_feb) as feb_price, AVG(comp_price_mar) as mar_price, AVG(comp_price_apr) as apr_price, AVG(comp_price_may) as may_price, AVG(comp_price_jun) as jun_price, AVG(comp_price_jul) as jul_price, AVG(comp_price_aug) as aug_price, AVG(comp_price_sep) as sep_price, AVG(comp_price_oct) as oct_price, AVG(comp_price_nov) as nov_price, AVG(comp_price_dec) as dec_price');
		
		$data['smkt_marinated_prev_year2_jan'] = 0;
		$data['smkt_marinated_prev_year2_feb'] = 0;
		$data['smkt_marinated_prev_year2_mar'] = 0;
		$data['smkt_marinated_prev_year2_apr'] = 0;
		$data['smkt_marinated_prev_year2_may'] = 0;
		$data['smkt_marinated_prev_year2_jun'] = 0;
		$data['smkt_marinated_prev_year2_jul'] = 0;
		$data['smkt_marinated_prev_year2_aug'] = 0;
		$data['smkt_marinated_prev_year2_sep'] = 0;
		$data['smkt_marinated_prev_year2_oct'] = 0;
		$data['smkt_marinated_prev_year2_nov'] = 0;
		$data['smkt_marinated_prev_year2_dec'] = 0;
		$data['smkt_marinated_prev_year2_avg'] = 0;
		$data['smkt_marinated_prev_year2_min'] = 0;
		$data['smkt_marinated_prev_year2_max'] = 0;

		foreach($smkt_marinated_prev_year2 as $row){
			$data['smkt_marinated_prev_year2_jan'] = $row->jan_price;
			$data['smkt_marinated_prev_year2_feb'] = $row->feb_price;
			$data['smkt_marinated_prev_year2_mar'] = $row->mar_price;
			$data['smkt_marinated_prev_year2_apr'] = $row->apr_price;
			$data['smkt_marinated_prev_year2_may'] = $row->may_price;
			$data['smkt_marinated_prev_year2_jun'] = $row->jun_price;
			$data['smkt_marinated_prev_year2_jul'] = $row->jul_price;
			$data['smkt_marinated_prev_year2_aug'] = $row->aug_price;
			$data['smkt_marinated_prev_year2_sep'] = $row->sep_price;
			$data['smkt_marinated_prev_year2_oct'] = $row->oct_price;
			$data['smkt_marinated_prev_year2_nov'] = $row->nov_price;
			$data['smkt_marinated_prev_year2_dec'] = $row->dec_price;
			$data['smkt_marinated_prev_year2_avg'] = ($row->jan_price + $row->feb_price + $row->mar_price + $row->apr_price + $row->may_price + $row->jun_price + $row->jul_price + $row->aug_price + $row->sep_price + $row->oct_price + $row->nov_price + $row->dec_price) / 12;

			$data['smkt_marinated_prev_year2_min'] = min($row->jan_price, $row->feb_price, $row->mar_price, $row->apr_price, $row->may_price, $row->jun_price, $row->jul_price, $row->aug_price, $row->sep_price, $row->oct_price, $row->nov_price, $row->dec_price);

			$data['smkt_marinated_prev_year2_max'] = max($row->jan_price, $row->feb_price, $row->mar_price, $row->apr_price, $row->may_price, $row->jun_price, $row->jul_price, $row->aug_price, $row->sep_price, $row->oct_price, $row->nov_price, $row->dec_price);
		}


		$smkt_marinated_prev_year1 = $this->admin->get_data('comparative_price_tbl', array('comp_price_product' => 'SMKT - MAR CUT UPS', 'comp_price_segment' => 'REYAL', 'comp_price_year' => $year - 1,	'bc_id'	=>	$bc_id), 
			FALSE, 'AVG(comp_price_jan) as jan_price, AVG(comp_price_feb) as feb_price, AVG(comp_price_mar) as mar_price, AVG(comp_price_apr) as apr_price, AVG(comp_price_may) as may_price, AVG(comp_price_jun) as jun_price, AVG(comp_price_jul) as jul_price, AVG(comp_price_aug) as aug_price, AVG(comp_price_sep) as sep_price, AVG(comp_price_oct) as oct_price, AVG(comp_price_nov) as nov_price, AVG(comp_price_dec) as dec_price');
		
		$data['smkt_marinated_prev_year1_jan'] = 0;
		$data['smkt_marinated_prev_year1_feb'] = 0;
		$data['smkt_marinated_prev_year1_mar'] = 0;
		$data['smkt_marinated_prev_year1_apr'] = 0;
		$data['smkt_marinated_prev_year1_may'] = 0;
		$data['smkt_marinated_prev_year1_jun'] = 0;
		$data['smkt_marinated_prev_year1_jul'] = 0;
		$data['smkt_marinated_prev_year1_aug'] = 0;
		$data['smkt_marinated_prev_year1_sep'] = 0;
		$data['smkt_marinated_prev_year1_oct'] = 0;
		$data['smkt_marinated_prev_year1_nov'] = 0;
		$data['smkt_marinated_prev_year1_dec'] = 0;
		$data['smkt_marinated_prev_year1_avg'] = 0;
		$data['smkt_marinated_prev_year1_min'] = 0;
		$data['smkt_marinated_prev_year1_max'] = 0;

		foreach($smkt_marinated_prev_year1 as $row){
			$data['smkt_marinated_prev_year1_jan'] = $row->jan_price;
			$data['smkt_marinated_prev_year1_feb'] = $row->feb_price;
			$data['smkt_marinated_prev_year1_mar'] = $row->mar_price;
			$data['smkt_marinated_prev_year1_apr'] = $row->apr_price;
			$data['smkt_marinated_prev_year1_may'] = $row->may_price;
			$data['smkt_marinated_prev_year1_jun'] = $row->jun_price;
			$data['smkt_marinated_prev_year1_jul'] = $row->jul_price;
			$data['smkt_marinated_prev_year1_aug'] = $row->aug_price;
			$data['smkt_marinated_prev_year1_sep'] = $row->sep_price;
			$data['smkt_marinated_prev_year1_oct'] = $row->oct_price;
			$data['smkt_marinated_prev_year1_nov'] = $row->nov_price;
			$data['smkt_marinated_prev_year1_dec'] = $row->dec_price;
			$data['smkt_marinated_prev_year1_avg'] = ($row->jan_price + $row->feb_price + $row->mar_price + $row->apr_price + $row->may_price + $row->jun_price + $row->jul_price + $row->aug_price + $row->sep_price + $row->oct_price + $row->nov_price + $row->dec_price) / 12;

			$data['smkt_marinated_prev_year1_min'] = min($row->jan_price, $row->feb_price, $row->mar_price, $row->apr_price, $row->may_price, $row->jun_price, $row->jul_price, $row->aug_price, $row->sep_price, $row->oct_price, $row->nov_price, $row->dec_price);

			$data['smkt_marinated_prev_year1_max'] = max($row->jan_price, $row->feb_price, $row->mar_price, $row->apr_price, $row->may_price, $row->jun_price, $row->jul_price, $row->aug_price, $row->sep_price, $row->oct_price, $row->nov_price, $row->dec_price);
		}


		/*TRADE DISTRIBUTOR Marinated Raw*/
		$join_tds_raw = array(
			'sales_item_tbl b' => 'a.sales_id = b.sales_id AND a.sales_status = 1 AND b.sales_item_status = 1 AND a.sales_year = ' . $year,
			'sales_details_tbl c' => 'b.sales_item_id = c.sales_item_id AND c.sales_det_status = 1',
			'material_tbl d' => 'b.material_id = d.material_id',
			'material_group_tbl e' => 'd.material_group_id = e.material_group_id AND e.material_group_name = \'MARINATED CHICKEN (RAW)\'',
			'outlet_tbl f' => 'a.outlet_id = f.outlet_id AND f.bc_id = ' . $bc_id,
			'outlet_brand_tbl g' => 'f.outlet_id = g.outlet_id AND g.outlet_brand_status = 1',
			'brand_tbl h' => 'g.brand_id  = h.brand_id AND h.brand_name = \'TRADE DISTRIBUTOR\''
		);

		$get_tds_raw = $this->admin->get_join('sales_tbl a', $join_tds_raw, FALSE, FALSE, 'YEAR(c.sales_det_date), MONTH(c.sales_det_date)', 'AVG(c.sales_det_asp) as asp, MONTH(c.sales_det_date) asp_date');

		$data['tds_raw1'] = 0;
		$data['tds_raw2'] = 0;
		$data['tds_raw3'] = 0;
		$data['tds_raw4'] = 0;
		$data['tds_raw5'] = 0;
		$data['tds_raw6'] = 0;
		$data['tds_raw7'] = 0;
		$data['tds_raw8'] = 0;
		$data['tds_raw9'] = 0;
		$data['tds_raw10'] = 0;
		$data['tds_raw11'] = 0;
		$data['tds_raw12'] = 0;
		$data['tds_raw_total'] = 0;
		$data['tds_raw_count'] = 0;
		$data['tds_raw_avg'] = 0;
		$data['tds_raw_min'] = 0;
		$data['tds_raw_max'] = 0;

		foreach($get_tds_raw as $row){
			$asp = $row->asp;
			$month = $row->asp_date;
			$data['tds_raw' . $month] = $asp;
			$data['tds_raw_total'] += $asp;

			if($asp < $data['tds_raw_min'] || $data['tds_raw_count'] == 0){
				$data['tds_raw_min'] = $asp;
			}

			if($asp > $data['tds_raw_max'] || $data['tds_raw_count'] == 0){
				$data['tds_raw_max'] = $asp;
			}

			$data['tds_raw_count']++;
		}

		$data['tds_raw_avg'] = $data['tds_raw_total'] != 0 ? $data['tds_raw_total'] / $data['tds_raw_count'] : 0;

		$tds_raw_prev_year2 = $this->admin->get_data('comparative_price_tbl', array('comp_price_product' => 'TDs - MARINATED CHICKEN RAW', 'comp_price_segment' => 'REYAL', 'comp_price_year' => $year - 2,	'bc_id'	=>	$bc_id), 
			FALSE, 'AVG(comp_price_jan) as jan_price, AVG(comp_price_feb) as feb_price, AVG(comp_price_mar) as mar_price, AVG(comp_price_apr) as apr_price, AVG(comp_price_may) as may_price, AVG(comp_price_jun) as jun_price, AVG(comp_price_jul) as jul_price, AVG(comp_price_aug) as aug_price, AVG(comp_price_sep) as sep_price, AVG(comp_price_oct) as oct_price, AVG(comp_price_nov) as nov_price, AVG(comp_price_dec) as dec_price');
		
		$data['tds_raw_prev_year2_jan'] = 0;
		$data['tds_raw_prev_year2_feb'] = 0;
		$data['tds_raw_prev_year2_mar'] = 0;
		$data['tds_raw_prev_year2_apr'] = 0;
		$data['tds_raw_prev_year2_may'] = 0;
		$data['tds_raw_prev_year2_jun'] = 0;
		$data['tds_raw_prev_year2_jul'] = 0;
		$data['tds_raw_prev_year2_aug'] = 0;
		$data['tds_raw_prev_year2_sep'] = 0;
		$data['tds_raw_prev_year2_oct'] = 0;
		$data['tds_raw_prev_year2_nov'] = 0;
		$data['tds_raw_prev_year2_dec'] = 0;
		$data['tds_raw_prev_year2_avg'] = 0;
		$data['tds_raw_prev_year2_min'] = 0;
		$data['tds_raw_prev_year2_max'] = 0;

		foreach($tds_raw_prev_year2 as $row){
			$data['tds_raw_prev_year2_jan'] = $row->jan_price;
			$data['tds_raw_prev_year2_feb'] = $row->feb_price;
			$data['tds_raw_prev_year2_mar'] = $row->mar_price;
			$data['tds_raw_prev_year2_apr'] = $row->apr_price;
			$data['tds_raw_prev_year2_may'] = $row->may_price;
			$data['tds_raw_prev_year2_jun'] = $row->jun_price;
			$data['tds_raw_prev_year2_jul'] = $row->jul_price;
			$data['tds_raw_prev_year2_aug'] = $row->aug_price;
			$data['tds_raw_prev_year2_sep'] = $row->sep_price;
			$data['tds_raw_prev_year2_oct'] = $row->oct_price;
			$data['tds_raw_prev_year2_nov'] = $row->nov_price;
			$data['tds_raw_prev_year2_dec'] = $row->dec_price;
			$data['tds_raw_prev_year2_avg'] = ($row->jan_price + $row->feb_price + $row->mar_price + $row->apr_price + $row->may_price + $row->jun_price + $row->jul_price + $row->aug_price + $row->sep_price + $row->oct_price + $row->nov_price + $row->dec_price) / 12;

			$data['tds_raw_prev_year2_min'] = min($row->jan_price, $row->feb_price, $row->mar_price, $row->apr_price, $row->may_price, $row->jun_price, $row->jul_price, $row->aug_price, $row->sep_price, $row->oct_price, $row->nov_price, $row->dec_price);

			$data['tds_raw_prev_year2_max'] = max($row->jan_price, $row->feb_price, $row->mar_price, $row->apr_price, $row->may_price, $row->jun_price, $row->jul_price, $row->aug_price, $row->sep_price, $row->oct_price, $row->nov_price, $row->dec_price);
		}


		$tds_raw_prev_year1 = $this->admin->get_data('comparative_price_tbl', array('comp_price_product' => 'TDs - MARINATED CHICKEN RAW', 'comp_price_segment' => 'REYAL', 'comp_price_year' => $year - 1,	'bc_id'	=>	$bc_id), 
			FALSE, 'AVG(comp_price_jan) as jan_price, AVG(comp_price_feb) as feb_price, AVG(comp_price_mar) as mar_price, AVG(comp_price_apr) as apr_price, AVG(comp_price_may) as may_price, AVG(comp_price_jun) as jun_price, AVG(comp_price_jul) as jul_price, AVG(comp_price_aug) as aug_price, AVG(comp_price_sep) as sep_price, AVG(comp_price_oct) as oct_price, AVG(comp_price_nov) as nov_price, AVG(comp_price_dec) as dec_price');
		
		$data['tds_raw_prev_year1_jan'] = 0;
		$data['tds_raw_prev_year1_feb'] = 0;
		$data['tds_raw_prev_year1_mar'] = 0;
		$data['tds_raw_prev_year1_apr'] = 0;
		$data['tds_raw_prev_year1_may'] = 0;
		$data['tds_raw_prev_year1_jun'] = 0;
		$data['tds_raw_prev_year1_jul'] = 0;
		$data['tds_raw_prev_year1_aug'] = 0;
		$data['tds_raw_prev_year1_sep'] = 0;
		$data['tds_raw_prev_year1_oct'] = 0;
		$data['tds_raw_prev_year1_nov'] = 0;
		$data['tds_raw_prev_year1_dec'] = 0;
		$data['tds_raw_prev_year1_avg'] = 0;
		$data['tds_raw_prev_year1_min'] = 0;
		$data['tds_raw_prev_year1_max'] = 0;

		foreach($tds_raw_prev_year1 as $row){
			$data['tds_raw_prev_year1_jan'] = $row->jan_price;
			$data['tds_raw_prev_year1_feb'] = $row->feb_price;
			$data['tds_raw_prev_year1_mar'] = $row->mar_price;
			$data['tds_raw_prev_year1_apr'] = $row->apr_price;
			$data['tds_raw_prev_year1_may'] = $row->may_price;
			$data['tds_raw_prev_year1_jun'] = $row->jun_price;
			$data['tds_raw_prev_year1_jul'] = $row->jul_price;
			$data['tds_raw_prev_year1_aug'] = $row->aug_price;
			$data['tds_raw_prev_year1_sep'] = $row->sep_price;
			$data['tds_raw_prev_year1_oct'] = $row->oct_price;
			$data['tds_raw_prev_year1_nov'] = $row->nov_price;
			$data['tds_raw_prev_year1_dec'] = $row->dec_price;
			$data['tds_raw_prev_year1_avg'] = ($row->jan_price + $row->feb_price + $row->mar_price + $row->apr_price + $row->may_price + $row->jun_price + $row->jul_price + $row->aug_price + $row->sep_price + $row->oct_price + $row->nov_price + $row->dec_price) / 12;

			$data['tds_raw_prev_year1_min'] = min($row->jan_price, $row->feb_price, $row->mar_price, $row->apr_price, $row->may_price, $row->jun_price, $row->jul_price, $row->aug_price, $row->sep_price, $row->oct_price, $row->nov_price, $row->dec_price);

			$data['tds_raw_prev_year1_max'] = max($row->jan_price, $row->feb_price, $row->mar_price, $row->apr_price, $row->may_price, $row->jun_price, $row->jul_price, $row->aug_price, $row->sep_price, $row->oct_price, $row->nov_price, $row->dec_price);
		}

		/*TRADE DISTRIBUTOR Liver / Gizzard*/
		$join_tds_liver = array(
			'sales_item_tbl b' => 'a.sales_id = b.sales_id AND a.sales_status = 1 AND b.sales_item_status = 1 AND a.sales_year = ' . $year,
			'sales_details_tbl c' => 'b.sales_item_id = c.sales_item_id AND c.sales_det_status = 1',
			'material_tbl d' => 'b.material_id = d.material_id',
			'material_group_tbl e' => 'd.material_group_id = e.material_group_id AND e.material_group_name = \'LIVER / GIZZARD\'',
			'outlet_tbl f' => 'a.outlet_id = f.outlet_id AND f.bc_id = ' . $bc_id,
			'outlet_brand_tbl g' => 'f.outlet_id = g.outlet_id AND g.outlet_brand_status = 1',
			'brand_tbl h' => 'g.brand_id  = h.brand_id AND h.brand_name = \'TRADE DISTRIBUTOR\''
		);

		$get_tds_liver = $this->admin->get_join('sales_tbl a', $join_tds_liver, FALSE, FALSE, 'YEAR(c.sales_det_date), MONTH(c.sales_det_date)', 'AVG(c.sales_det_asp) as asp, MONTH(c.sales_det_date) asp_date');

		$data['tds_liver1'] = 0;
		$data['tds_liver2'] = 0;
		$data['tds_liver3'] = 0;
		$data['tds_liver4'] = 0;
		$data['tds_liver5'] = 0;
		$data['tds_liver6'] = 0;
		$data['tds_liver7'] = 0;
		$data['tds_liver8'] = 0;
		$data['tds_liver9'] = 0;
		$data['tds_liver10'] = 0;
		$data['tds_liver11'] = 0;
		$data['tds_liver12'] = 0;
		$data['tds_liver_total'] = 0;
		$data['tds_liver_count'] = 0;
		$data['tds_liver_avg'] = 0;
		$data['tds_liver_min'] = 0;
		$data['tds_liver_max'] = 0;

		foreach($get_tds_liver as $row){
			$asp = $row->asp;
			$month = $row->asp_date;
			$data['tds_liver' . $month] = $asp;
			$data['tds_liver_total'] += $asp;

			if($asp < $data['tds_liver_min'] || $data['tds_liver_count'] == 0){
				$data['tds_liver_min'] = $asp;
			}

			if($asp > $data['tds_liver_max'] || $data['tds_liver_count'] == 0){
				$data['tds_liver_max'] = $asp;
			}

			$data['tds_liver_count']++;
		}

		$data['tds_liver_avg'] = $data['tds_liver_total'] != 0 ? $data['tds_liver_total'] / $data['tds_liver_count'] : 0;


		$tds_liver_prev_year2 = $this->admin->get_data('comparative_price_tbl', array('comp_price_product' => 'TDs - LIVER / GIZZARD', 'comp_price_segment' => 'REYAL', 'comp_price_year' => $year - 2,	'bc_id'	=>	$bc_id), 
			FALSE, 'AVG(comp_price_jan) as jan_price, AVG(comp_price_feb) as feb_price, AVG(comp_price_mar) as mar_price, AVG(comp_price_apr) as apr_price, AVG(comp_price_may) as may_price, AVG(comp_price_jun) as jun_price, AVG(comp_price_jul) as jul_price, AVG(comp_price_aug) as aug_price, AVG(comp_price_sep) as sep_price, AVG(comp_price_oct) as oct_price, AVG(comp_price_nov) as nov_price, AVG(comp_price_dec) as dec_price');
		
		$data['tds_liver_prev_year2_jan'] = 0;
		$data['tds_liver_prev_year2_feb'] = 0;
		$data['tds_liver_prev_year2_mar'] = 0;
		$data['tds_liver_prev_year2_apr'] = 0;
		$data['tds_liver_prev_year2_may'] = 0;
		$data['tds_liver_prev_year2_jun'] = 0;
		$data['tds_liver_prev_year2_jul'] = 0;
		$data['tds_liver_prev_year2_aug'] = 0;
		$data['tds_liver_prev_year2_sep'] = 0;
		$data['tds_liver_prev_year2_oct'] = 0;
		$data['tds_liver_prev_year2_nov'] = 0;
		$data['tds_liver_prev_year2_dec'] = 0;
		$data['tds_liver_prev_year2_avg'] = 0;
		$data['tds_liver_prev_year2_min'] = 0;
		$data['tds_liver_prev_year2_max'] = 0;

		foreach($tds_liver_prev_year2 as $row){
			$data['tds_liver_prev_year2_jan'] = $row->jan_price;
			$data['tds_liver_prev_year2_feb'] = $row->feb_price;
			$data['tds_liver_prev_year2_mar'] = $row->mar_price;
			$data['tds_liver_prev_year2_apr'] = $row->apr_price;
			$data['tds_liver_prev_year2_may'] = $row->may_price;
			$data['tds_liver_prev_year2_jun'] = $row->jun_price;
			$data['tds_liver_prev_year2_jul'] = $row->jul_price;
			$data['tds_liver_prev_year2_aug'] = $row->aug_price;
			$data['tds_liver_prev_year2_sep'] = $row->sep_price;
			$data['tds_liver_prev_year2_oct'] = $row->oct_price;
			$data['tds_liver_prev_year2_nov'] = $row->nov_price;
			$data['tds_liver_prev_year2_dec'] = $row->dec_price;
			$data['tds_liver_prev_year2_avg'] = ($row->jan_price + $row->feb_price + $row->mar_price + $row->apr_price + $row->may_price + $row->jun_price + $row->jul_price + $row->aug_price + $row->sep_price + $row->oct_price + $row->nov_price + $row->dec_price) / 12;

			$data['tds_liver_prev_year2_min'] = min($row->jan_price, $row->feb_price, $row->mar_price, $row->apr_price, $row->may_price, $row->jun_price, $row->jul_price, $row->aug_price, $row->sep_price, $row->oct_price, $row->nov_price, $row->dec_price);

			$data['tds_liver_prev_year2_max'] = max($row->jan_price, $row->feb_price, $row->mar_price, $row->apr_price, $row->may_price, $row->jun_price, $row->jul_price, $row->aug_price, $row->sep_price, $row->oct_price, $row->nov_price, $row->dec_price);
		}


		$tds_liver_prev_year1 = $this->admin->get_data('comparative_price_tbl', array('comp_price_product' => 'TDs - LIVER / GIZZARD', 'comp_price_segment' => 'REYAL', 'comp_price_year' => $year - 1,	'bc_id'	=>	$bc_id), 
			FALSE, 'AVG(comp_price_jan) as jan_price, AVG(comp_price_feb) as feb_price, AVG(comp_price_mar) as mar_price, AVG(comp_price_apr) as apr_price, AVG(comp_price_may) as may_price, AVG(comp_price_jun) as jun_price, AVG(comp_price_jul) as jul_price, AVG(comp_price_aug) as aug_price, AVG(comp_price_sep) as sep_price, AVG(comp_price_oct) as oct_price, AVG(comp_price_nov) as nov_price, AVG(comp_price_dec) as dec_price');
		
		$data['tds_liver_prev_year1_jan'] = 0;
		$data['tds_liver_prev_year1_feb'] = 0;
		$data['tds_liver_prev_year1_mar'] = 0;
		$data['tds_liver_prev_year1_apr'] = 0;
		$data['tds_liver_prev_year1_may'] = 0;
		$data['tds_liver_prev_year1_jun'] = 0;
		$data['tds_liver_prev_year1_jul'] = 0;
		$data['tds_liver_prev_year1_aug'] = 0;
		$data['tds_liver_prev_year1_sep'] = 0;
		$data['tds_liver_prev_year1_oct'] = 0;
		$data['tds_liver_prev_year1_nov'] = 0;
		$data['tds_liver_prev_year1_dec'] = 0;
		$data['tds_liver_prev_year1_avg'] = 0;
		$data['tds_liver_prev_year1_min'] = 0;
		$data['tds_liver_prev_year1_max'] = 0;

		foreach($tds_liver_prev_year1 as $row){
			$data['tds_liver_prev_year1_jan'] = $row->jan_price;
			$data['tds_liver_prev_year1_feb'] = $row->feb_price;
			$data['tds_liver_prev_year1_mar'] = $row->mar_price;
			$data['tds_liver_prev_year1_apr'] = $row->apr_price;
			$data['tds_liver_prev_year1_may'] = $row->may_price;
			$data['tds_liver_prev_year1_jun'] = $row->jun_price;
			$data['tds_liver_prev_year1_jul'] = $row->jul_price;
			$data['tds_liver_prev_year1_aug'] = $row->aug_price;
			$data['tds_liver_prev_year1_sep'] = $row->sep_price;
			$data['tds_liver_prev_year1_oct'] = $row->oct_price;
			$data['tds_liver_prev_year1_nov'] = $row->nov_price;
			$data['tds_liver_prev_year1_dec'] = $row->dec_price;
			$data['tds_liver_prev_year1_avg'] = ($row->jan_price + $row->feb_price + $row->mar_price + $row->apr_price + $row->may_price + $row->jun_price + $row->jul_price + $row->aug_price + $row->sep_price + $row->oct_price + $row->nov_price + $row->dec_price) / 12;

			$data['tds_liver_prev_year1_min'] = min($row->jan_price, $row->feb_price, $row->mar_price, $row->apr_price, $row->may_price, $row->jun_price, $row->jul_price, $row->aug_price, $row->sep_price, $row->oct_price, $row->nov_price, $row->dec_price);

			$data['tds_liver_prev_year1_max'] = max($row->jan_price, $row->feb_price, $row->mar_price, $row->apr_price, $row->may_price, $row->jun_price, $row->jul_price, $row->aug_price, $row->sep_price, $row->oct_price, $row->nov_price, $row->dec_price);
		}

		/*Resellers*/
		
		$get_rsl_orc = $this->admin->get_query("SELECT p.size_name, AVG(asp_tbl.jan_asp) as orc_jan, AVG(asp_tbl.feb_asp) as orc_feb, AVG(asp_tbl.mar_asp) as orc_mar, AVG(asp_tbl.apr_asp) as orc_apr, AVG(asp_tbl.may_asp) as orc_may, AVG(asp_tbl.jun_asp) as orc_jun, AVG(asp_tbl.jul_asp) as orc_jul, AVG(asp_tbl.aug_asp) as orc_aug, AVG(asp_tbl.sep_asp) as orc_sep, AVG(asp_tbl.oct_asp) as orc_oct, AVG(asp_tbl.nov_asp) as orc_nov, AVG(asp_tbl.dec_asp) as orc_dec FROM material_tbl o, size_tbl p,

		(SELECT m.material_id, m.size_id,

		(SELECT AVG(c.sales_det_asp) FROM `sales_tbl` `a` JOIN `sales_item_tbl` `b` ON `a`.`sales_id` = `b`.`sales_id` AND `a`.`sales_status` = 1 AND `b`.`sales_item_status` = 1 AND `a`.`sales_year` = " . $year . " JOIN `sales_details_tbl` `c` ON `b`.`sales_item_id` = `c`.`sales_item_id` AND `c`.`sales_det_status` = 1 JOIN `material_tbl` `d` ON `b`.`material_id` = `d`.`material_id` JOIN `material_group_tbl` `e` ON `d`.`material_group_id` = `e`.`material_group_id` JOIN `outlet_tbl` `f` ON `a`.`outlet_id` = `f`.`outlet_id` JOIN `outlet_brand_tbl` `g` ON `f`.`outlet_id` = `g`.`outlet_id` AND `g`.`outlet_brand_status` = 1 JOIN `brand_tbl` `h` ON `g`.`brand_id` = `h`.`brand_id` AND `h`.`brand_name` = 'RESELLER' AND c.sales_det_date = '" . $year . "-01-01' WHERE d.material_id = m.material_id AND c.sales_det_asp > 0 AND f.bc_id = " . $bc_id . ") as jan_asp,
    
    	(SELECT AVG(c.sales_det_asp) FROM `sales_tbl` `a` JOIN `sales_item_tbl` `b` ON `a`.`sales_id` = `b`.`sales_id` AND `a`.`sales_status` = 1 AND `b`.`sales_item_status` = 1 AND `a`.`sales_year` = " . $year . " JOIN `sales_details_tbl` `c` ON `b`.`sales_item_id` = `c`.`sales_item_id` AND `c`.`sales_det_status` = 1 JOIN `material_tbl` `d` ON `b`.`material_id` = `d`.`material_id` JOIN `material_group_tbl` `e` ON `d`.`material_group_id` = `e`.`material_group_id` JOIN `outlet_tbl` `f` ON `a`.`outlet_id` = `f`.`outlet_id` JOIN `outlet_brand_tbl` `g` ON `f`.`outlet_id` = `g`.`outlet_id` AND `g`.`outlet_brand_status` = 1 JOIN `brand_tbl` `h` ON `g`.`brand_id` = `h`.`brand_id` AND `h`.`brand_name` = 'RESELLER' AND c.sales_det_date = '" . $year . "-02-01' WHERE d.material_id = m.material_id AND c.sales_det_asp > 0 AND f.bc_id = " . $bc_id . ") as feb_asp,
    
    	(SELECT AVG(c.sales_det_asp) FROM `sales_tbl` `a` JOIN `sales_item_tbl` `b` ON `a`.`sales_id` = `b`.`sales_id` AND `a`.`sales_status` = 1 AND `b`.`sales_item_status` = 1 AND `a`.`sales_year` = " . $year . " JOIN `sales_details_tbl` `c` ON `b`.`sales_item_id` = `c`.`sales_item_id` AND `c`.`sales_det_status` = 1 JOIN `material_tbl` `d` ON `b`.`material_id` = `d`.`material_id` JOIN `material_group_tbl` `e` ON `d`.`material_group_id` = `e`.`material_group_id` JOIN `outlet_tbl` `f` ON `a`.`outlet_id` = `f`.`outlet_id` JOIN `outlet_brand_tbl` `g` ON `f`.`outlet_id` = `g`.`outlet_id` AND `g`.`outlet_brand_status` = 1 JOIN `brand_tbl` `h` ON `g`.`brand_id` = `h`.`brand_id` AND `h`.`brand_name` = 'RESELLER' AND c.sales_det_date = '" . $year . "-03-01' WHERE d.material_id = m.material_id AND c.sales_det_asp > 0 AND f.bc_id = " . $bc_id . ") as mar_asp,
    
   		(SELECT AVG(c.sales_det_asp) FROM `sales_tbl` `a` JOIN `sales_item_tbl` `b` ON `a`.`sales_id` = `b`.`sales_id` AND `a`.`sales_status` = 1 AND `b`.`sales_item_status` = 1 AND `a`.`sales_year` = " . $year . " JOIN `sales_details_tbl` `c` ON `b`.`sales_item_id` = `c`.`sales_item_id` AND `c`.`sales_det_status` = 1 JOIN `material_tbl` `d` ON `b`.`material_id` = `d`.`material_id` JOIN `material_group_tbl` `e` ON `d`.`material_group_id` = `e`.`material_group_id` JOIN `outlet_tbl` `f` ON `a`.`outlet_id` = `f`.`outlet_id` JOIN `outlet_brand_tbl` `g` ON `f`.`outlet_id` = `g`.`outlet_id` AND `g`.`outlet_brand_status` = 1 JOIN `brand_tbl` `h` ON `g`.`brand_id` = `h`.`brand_id` AND `h`.`brand_name` = 'RESELLER' AND c.sales_det_date = '" . $year . "-04-01' WHERE d.material_id = m.material_id AND c.sales_det_asp > 0 AND f.bc_id = " . $bc_id . ") as apr_asp,
    
    	(SELECT AVG(c.sales_det_asp) FROM `sales_tbl` `a` JOIN `sales_item_tbl` `b` ON `a`.`sales_id` = `b`.`sales_id` AND `a`.`sales_status` = 1 AND `b`.`sales_item_status` = 1 AND `a`.`sales_year` = " . $year . " JOIN `sales_details_tbl` `c` ON `b`.`sales_item_id` = `c`.`sales_item_id` AND `c`.`sales_det_status` = 1 JOIN `material_tbl` `d` ON `b`.`material_id` = `d`.`material_id` JOIN `material_group_tbl` `e` ON `d`.`material_group_id` = `e`.`material_group_id` JOIN `outlet_tbl` `f` ON `a`.`outlet_id` = `f`.`outlet_id` JOIN `outlet_brand_tbl` `g` ON `f`.`outlet_id` = `g`.`outlet_id` AND `g`.`outlet_brand_status` = 1 JOIN `brand_tbl` `h` ON `g`.`brand_id` = `h`.`brand_id` AND `h`.`brand_name` = 'RESELLER' AND c.sales_det_date = '" . $year . "-05-01' WHERE d.material_id = m.material_id AND c.sales_det_asp > 0 AND f.bc_id = " . $bc_id . ") as may_asp,
    
    	(SELECT AVG(c.sales_det_asp) FROM `sales_tbl` `a` JOIN `sales_item_tbl` `b` ON `a`.`sales_id` = `b`.`sales_id` AND `a`.`sales_status` = 1 AND `b`.`sales_item_status` = 1 AND `a`.`sales_year` = " . $year . " JOIN `sales_details_tbl` `c` ON `b`.`sales_item_id` = `c`.`sales_item_id` AND `c`.`sales_det_status` = 1 JOIN `material_tbl` `d` ON `b`.`material_id` = `d`.`material_id` JOIN `material_group_tbl` `e` ON `d`.`material_group_id` = `e`.`material_group_id` JOIN `outlet_tbl` `f` ON `a`.`outlet_id` = `f`.`outlet_id` JOIN `outlet_brand_tbl` `g` ON `f`.`outlet_id` = `g`.`outlet_id` AND `g`.`outlet_brand_status` = 1 JOIN `brand_tbl` `h` ON `g`.`brand_id` = `h`.`brand_id` AND `h`.`brand_name` = 'RESELLER' AND c.sales_det_date = '" . $year . "-06-01' WHERE d.material_id = m.material_id AND c.sales_det_asp > 0 AND f.bc_id = " . $bc_id . ") as jun_asp,
    
    	(SELECT AVG(c.sales_det_asp) FROM `sales_tbl` `a` JOIN `sales_item_tbl` `b` ON `a`.`sales_id` = `b`.`sales_id` AND `a`.`sales_status` = 1 AND `b`.`sales_item_status` = 1 AND `a`.`sales_year` = " . $year . " JOIN `sales_details_tbl` `c` ON `b`.`sales_item_id` = `c`.`sales_item_id` AND `c`.`sales_det_status` = 1 JOIN `material_tbl` `d` ON `b`.`material_id` = `d`.`material_id` JOIN `material_group_tbl` `e` ON `d`.`material_group_id` = `e`.`material_group_id` JOIN `outlet_tbl` `f` ON `a`.`outlet_id` = `f`.`outlet_id` JOIN `outlet_brand_tbl` `g` ON `f`.`outlet_id` = `g`.`outlet_id` AND `g`.`outlet_brand_status` = 1 JOIN `brand_tbl` `h` ON `g`.`brand_id` = `h`.`brand_id` AND `h`.`brand_name` = 'RESELLER' AND c.sales_det_date = '" . $year . "-07-01' WHERE d.material_id = m.material_id AND c.sales_det_asp > 0 AND f.bc_id = " . $bc_id . ") as jul_asp,
    
    	(SELECT AVG(c.sales_det_asp) FROM `sales_tbl` `a` JOIN `sales_item_tbl` `b` ON `a`.`sales_id` = `b`.`sales_id` AND `a`.`sales_status` = 1 AND `b`.`sales_item_status` = 1 AND `a`.`sales_year` = " . $year . " JOIN `sales_details_tbl` `c` ON `b`.`sales_item_id` = `c`.`sales_item_id` AND `c`.`sales_det_status` = 1 JOIN `material_tbl` `d` ON `b`.`material_id` = `d`.`material_id` JOIN `material_group_tbl` `e` ON `d`.`material_group_id` = `e`.`material_group_id` JOIN `outlet_tbl` `f` ON `a`.`outlet_id` = `f`.`outlet_id` JOIN `outlet_brand_tbl` `g` ON `f`.`outlet_id` = `g`.`outlet_id` AND `g`.`outlet_brand_status` = 1 JOIN `brand_tbl` `h` ON `g`.`brand_id` = `h`.`brand_id` AND `h`.`brand_name` = 'RESELLER' AND c.sales_det_date = '" . $year . "-08-01' WHERE d.material_id = m.material_id AND c.sales_det_asp > 0 AND f.bc_id = " . $bc_id . ") as aug_asp,
    
    	(SELECT AVG(c.sales_det_asp) FROM `sales_tbl` `a` JOIN `sales_item_tbl` `b` ON `a`.`sales_id` = `b`.`sales_id` AND `a`.`sales_status` = 1 AND `b`.`sales_item_status` = 1 AND `a`.`sales_year` = " . $year . " JOIN `sales_details_tbl` `c` ON `b`.`sales_item_id` = `c`.`sales_item_id` AND `c`.`sales_det_status` = 1 JOIN `material_tbl` `d` ON `b`.`material_id` = `d`.`material_id` JOIN `material_group_tbl` `e` ON `d`.`material_group_id` = `e`.`material_group_id` JOIN `outlet_tbl` `f` ON `a`.`outlet_id` = `f`.`outlet_id` JOIN `outlet_brand_tbl` `g` ON `f`.`outlet_id` = `g`.`outlet_id` AND `g`.`outlet_brand_status` = 1 JOIN `brand_tbl` `h` ON `g`.`brand_id` = `h`.`brand_id` AND `h`.`brand_name` = 'RESELLER' AND c.sales_det_date = '" . $year . "-09-01' WHERE d.material_id = m.material_id AND c.sales_det_asp > 0 AND f.bc_id = " . $bc_id . ") as sep_asp,
    
    	(SELECT AVG(c.sales_det_asp) FROM `sales_tbl` `a` JOIN `sales_item_tbl` `b` ON `a`.`sales_id` = `b`.`sales_id` AND `a`.`sales_status` = 1 AND `b`.`sales_item_status` = 1 AND `a`.`sales_year` = " . $year . " JOIN `sales_details_tbl` `c` ON `b`.`sales_item_id` = `c`.`sales_item_id` AND `c`.`sales_det_status` = 1 JOIN `material_tbl` `d` ON `b`.`material_id` = `d`.`material_id` JOIN `material_group_tbl` `e` ON `d`.`material_group_id` = `e`.`material_group_id` JOIN `outlet_tbl` `f` ON `a`.`outlet_id` = `f`.`outlet_id` JOIN `outlet_brand_tbl` `g` ON `f`.`outlet_id` = `g`.`outlet_id` AND `g`.`outlet_brand_status` = 1 JOIN `brand_tbl` `h` ON `g`.`brand_id` = `h`.`brand_id` AND `h`.`brand_name` = 'RESELLER' AND c.sales_det_date = '" . $year . "-10-01' WHERE d.material_id = m.material_id AND c.sales_det_asp > 0 AND f.bc_id = " . $bc_id . ") as oct_asp,
    
    	(SELECT AVG(c.sales_det_asp) FROM `sales_tbl` `a` JOIN `sales_item_tbl` `b` ON `a`.`sales_id` = `b`.`sales_id` AND `a`.`sales_status` = 1 AND `b`.`sales_item_status` = 1 AND `a`.`sales_year` = " . $year . " JOIN `sales_details_tbl` `c` ON `b`.`sales_item_id` = `c`.`sales_item_id` AND `c`.`sales_det_status` = 1 JOIN `material_tbl` `d` ON `b`.`material_id` = `d`.`material_id` JOIN `material_group_tbl` `e` ON `d`.`material_group_id` = `e`.`material_group_id` JOIN `outlet_tbl` `f` ON `a`.`outlet_id` = `f`.`outlet_id` JOIN `outlet_brand_tbl` `g` ON `f`.`outlet_id` = `g`.`outlet_id` AND `g`.`outlet_brand_status` = 1 JOIN `brand_tbl` `h` ON `g`.`brand_id` = `h`.`brand_id` AND `h`.`brand_name` = 'RESELLER' AND c.sales_det_date = '" . $year . "-11-01' WHERE d.material_id = m.material_id AND c.sales_det_asp > 0 AND f.bc_id = " . $bc_id . ") as nov_asp,
    
    	(SELECT AVG(c.sales_det_asp) FROM `sales_tbl` `a` JOIN `sales_item_tbl` `b` ON `a`.`sales_id` = `b`.`sales_id` AND `a`.`sales_status` = 1 AND `b`.`sales_item_status` = 1 AND `a`.`sales_year` = " . $year . " JOIN `sales_details_tbl` `c` ON `b`.`sales_item_id` = `c`.`sales_item_id` AND `c`.`sales_det_status` = 1 JOIN `material_tbl` `d` ON `b`.`material_id` = `d`.`material_id` JOIN `material_group_tbl` `e` ON `d`.`material_group_id` = `e`.`material_group_id` JOIN `outlet_tbl` `f` ON `a`.`outlet_id` = `f`.`outlet_id` JOIN `outlet_brand_tbl` `g` ON `f`.`outlet_id` = `g`.`outlet_id` AND `g`.`outlet_brand_status` = 1 JOIN `brand_tbl` `h` ON `g`.`brand_id` = `h`.`brand_id` AND `h`.`brand_name` = 'RESELLER' AND c.sales_det_date = '" . $year . "-12-01' WHERE d.material_id = m.material_id AND c.sales_det_asp > 0 AND f.bc_id = " . $bc_id . ") as dec_asp
    
		FROM material_size_tbl m, brand_tbl n WHERE m.brand_id = n.brand_id AND m.material_size_status = 1 AND n.brand_name = 'RESELLER' AND m.is_orc = 1) as asp_tbl

		WHERE o.material_id = asp_tbl.material_id AND p.size_id = asp_tbl.size_id GROUP BY p.size_id");

		$data['rsl_reg1'] = $data['rsl_reg2'] = $data['rsl_reg3'] = $data['rsl_reg4'] = $data['rsl_reg5'] = $data['rsl_reg6'] = $data['rsl_reg7'] = $data['rsl_reg8'] = $data['rsl_reg9'] = $data['rsl_reg10'] = $data['rsl_reg11'] = $data['rsl_reg12'] = $data['rsl_reg_total'] = $data['rsl_reg_count'] = $data['rsl_reg_avg'] = $data['rsl_reg_min'] = $data['rsl_reg_max'] = 0;

		$data['rsl_jbo1'] = $data['rsl_jbo2'] = $data['rsl_jbo3'] = $data['rsl_jbo4'] = $data['rsl_jbo5'] = $data['rsl_jbo6'] = $data['rsl_jbo7'] = $data['rsl_jbo8'] = $data['rsl_jbo9'] = $data['rsl_jbo10'] = $data['rsl_jbo11'] = $data['rsl_jbo12'] = $data['rsl_jbo_total'] = $data['rsl_jbo_count'] = $data['rsl_jbo_avg'] = $data['rsl_jbo_min'] = $data['rsl_jbo_max'] = 0;

		$data['rsl_ss1'] = $data['rsl_ss2'] = $data['rsl_ss3'] = $data['rsl_ss4'] = $data['rsl_ss5'] = $data['rsl_ss6'] = $data['rsl_ss7'] = $data['rsl_ss8'] = $data['rsl_ss9'] = $data['rsl_ss10'] = $data['rsl_ss11'] = $data['rsl_ss12'] = $data['rsl_ss_total'] = $data['rsl_ss_count'] = $data['rsl_ss_avg'] = $data['rsl_ss_min'] = $data['rsl_ss_max'] = 0;

		$data['rsl_bt1'] = $data['rsl_bt2'] = $data['rsl_bt3'] = $data['rsl_bt4'] = $data['rsl_bt5'] = $data['rsl_bt6'] = $data['rsl_bt7'] = $data['rsl_bt8'] = $data['rsl_bt9'] = $data['rsl_bt10'] = $data['rsl_bt11'] = $data['rsl_bt12'] = $data['rsl_bt_total'] = $data['rsl_bt_count'] = $data['rsl_bt_avg'] = $data['rsl_bt_min'] = $data['rsl_bt_max'] = 0;

		$data['rsl_half1'] = $data['rsl_half2'] = $data['rsl_half3'] = $data['rsl_half4'] = $data['rsl_half5'] = $data['rsl_half6'] = $data['rsl_half7'] = $data['rsl_half8'] = $data['rsl_half9'] = $data['rsl_half10'] = $data['rsl_half11'] = $data['rsl_half12'] = $data['rsl_half_total'] = $data['rsl_half_count'] = $data['rsl_half_avg'] = $data['rsl_half_min'] = $data['rsl_half_max'] = 0;

		$data['rsl_reg1_year2'] = 0;
		$data['rsl_reg2_year2'] = 0;
		$data['rsl_reg3_year2'] = 0;
		$data['rsl_reg4_year2'] = 0;
		$data['rsl_reg5_year2'] = 0;
		$data['rsl_reg6_year2'] = 0;
		$data['rsl_reg7_year2'] = 0;
		$data['rsl_reg8_year2'] = 0;
		$data['rsl_reg9_year2'] = 0;
		$data['rsl_reg10_year2'] = 0;
		$data['rsl_reg11_year2'] = 0;
		$data['rsl_reg12_year2'] = 0;
		$data['rsl_reg_avg_year2'] = 0;
		$data['rsl_reg_min_year2'] = 0;
		$data['rsl_reg_max_year2'] = 0;

		$data['rsl_reg1_year1'] = 0;
		$data['rsl_reg2_year1'] = 0;
		$data['rsl_reg3_year1'] = 0;
		$data['rsl_reg4_year1'] = 0;
		$data['rsl_reg5_year1'] = 0;
		$data['rsl_reg6_year1'] = 0;
		$data['rsl_reg7_year1'] = 0;
		$data['rsl_reg8_year1'] = 0;
		$data['rsl_reg9_year1'] = 0;
		$data['rsl_reg10_year1'] = 0;
		$data['rsl_reg11_year1'] = 0;
		$data['rsl_reg12_year1'] = 0;
		$data['rsl_reg_avg_year1'] = 0;
		$data['rsl_reg_min_year1'] = 0;
		$data['rsl_reg_max_year1'] = 0;

		$data['rsl_jbo1_year2'] = 0;
		$data['rsl_jbo2_year2'] = 0;
		$data['rsl_jbo3_year2'] = 0;
		$data['rsl_jbo4_year2'] = 0;
		$data['rsl_jbo5_year2'] = 0;
		$data['rsl_jbo6_year2'] = 0;
		$data['rsl_jbo7_year2'] = 0;
		$data['rsl_jbo8_year2'] = 0;
		$data['rsl_jbo9_year2'] = 0;
		$data['rsl_jbo10_year2'] = 0;
		$data['rsl_jbo11_year2'] = 0;
		$data['rsl_jbo12_year2'] = 0;
		$data['rsl_jbo_year2_avg'] = 0;
		$data['rsl_jbo_year2_min'] = 0;
		$data['rsl_jbo_year2_max'] = 0;

		$data['rsl_jbo1_year2'] = 0;
		$data['rsl_jbo2_year2'] = 0;
		$data['rsl_jbo3_year2'] = 0;
		$data['rsl_jbo4_year2'] = 0;
		$data['rsl_jbo5_year2'] = 0;
		$data['rsl_jbo6_year2'] = 0;
		$data['rsl_jbo7_year2'] = 0;
		$data['rsl_jbo8_year2'] = 0;
		$data['rsl_jbo9_year2'] = 0;
		$data['rsl_jbo10_year2'] = 0;
		$data['rsl_jbo11_year2'] = 0;
		$data['rsl_jbo12_year2'] = 0;
		$data['rsl_jbo_year2_avg'] = 0;
		$data['rsl_jbo_year2_min'] = 0;
		$data['rsl_jbo_year2_max'] = 0;


		$data['rsl_jbo1_year1'] = 0;
		$data['rsl_jbo2_year1'] = 0;
		$data['rsl_jbo3_year1'] = 0;
		$data['rsl_jbo4_year1'] = 0;
		$data['rsl_jbo5_year1'] = 0;
		$data['rsl_jbo6_year1'] = 0;
		$data['rsl_jbo7_year1'] = 0;
		$data['rsl_jbo8_year1'] = 0;
		$data['rsl_jbo9_year1'] = 0;
		$data['rsl_jbo10_year1'] = 0;
		$data['rsl_jbo11_year1'] = 0;
		$data['rsl_jbo12_year1'] = 0;
		$data['rsl_jbo_year1_avg'] = 0;
		$data['rsl_jbo_year1_min'] = 0;
		$data['rsl_jbo_year1_max'] = 0;

		$data['rsl_ss1_year2'] = 0;
		$data['rsl_ss2_year2'] = 0;
		$data['rsl_ss3_year2'] = 0;
		$data['rsl_ss4_year2'] = 0;
		$data['rsl_ss5_year2'] = 0;
		$data['rsl_ss6_year2'] = 0;
		$data['rsl_ss7_year2'] = 0;
		$data['rsl_ss8_year2'] = 0;
		$data['rsl_ss9_year2'] = 0;
		$data['rsl_ss10_year2'] = 0;
		$data['rsl_ss11_year2'] = 0;
		$data['rsl_ss12_year2'] = 0;
		$data['rsl_ss_avg_year2'] = 0;
		$data['rsl_ss_min_year2'] = 0;
		$data['rsl_ss_max_year2'] = 0;


		$data['rsl_ss1_year1'] = 0;
		$data['rsl_ss2_year1'] = 0;
		$data['rsl_ss3_year1'] = 0;
		$data['rsl_ss4_year1'] = 0;
		$data['rsl_ss5_year1'] = 0;
		$data['rsl_ss6_year1'] = 0;
		$data['rsl_ss7_year1'] = 0;
		$data['rsl_ss8_year1'] = 0;
		$data['rsl_ss9_year1'] = 0;
		$data['rsl_ss10_year1'] = 0;
		$data['rsl_ss11_year1'] = 0;
		$data['rsl_ss12_year1'] = 0;
		$data['rsl_ss_avg_year1'] = 0;
		$data['rsl_ss_min_year1'] = 0;
		$data['rsl_ss_max_year1'] = 0;

		$data['rsl_bt1_year2'] = 0;
		$data['rsl_bt2_year2'] = 0;
		$data['rsl_bt3_year2'] = 0;
		$data['rsl_bt4_year2'] = 0;
		$data['rsl_bt5_year2'] = 0;
		$data['rsl_bt6_year2'] = 0;
		$data['rsl_bt7_year2'] = 0;
		$data['rsl_bt8_year2'] = 0;
		$data['rsl_bt9_year2'] = 0;
		$data['rsl_bt10_year2'] = 0;
		$data['rsl_bt11_year2'] = 0;
		$data['rsl_bt12_year2'] = 0;
		$data['rsl_bt_avg_year2'] = 0;
		$data['rsl_bt_min_year2'] = 0;
		$data['rsl_bt_max_year2'] = 0;

		$data['rsl_bt1_year1'] = 0;
		$data['rsl_bt2_year1'] = 0;
		$data['rsl_bt3_year1'] = 0;
		$data['rsl_bt4_year1'] = 0;
		$data['rsl_bt5_year1'] = 0;
		$data['rsl_bt6_year1'] = 0;
		$data['rsl_bt7_year1'] = 0;
		$data['rsl_bt8_year1'] = 0;
		$data['rsl_bt9_year1'] = 0;
		$data['rsl_bt10_year1'] = 0;
		$data['rsl_bt11_year1'] = 0;
		$data['rsl_bt12_year1'] = 0;
		$data['rsl_bt_avg_year1'] = 0;
		$data['rsl_bt_min_year1'] = 0;
		$data['rsl_bt_max_year1'] = 0;

		$data['rsl_half1_year2'] = 0;
		$data['rsl_half2_year2'] = 0;
		$data['rsl_half3_year2'] = 0;
		$data['rsl_half4_year2'] = 0;
		$data['rsl_half5_year2'] = 0;
		$data['rsl_half6_year2'] = 0;
		$data['rsl_half7_year2'] = 0;
		$data['rsl_half8_year2'] = 0;
		$data['rsl_half9_year2'] = 0;
		$data['rsl_half10_year2'] = 0;
		$data['rsl_half11_year2'] = 0;
		$data['rsl_half12_year2'] = 0;
		$data['rsl_half_avg_year2'] = 0;
		$data['rsl_half_min_year2'] = 0;
		$data['rsl_half_max_year2'] = 0;

		$data['rsl_half1_year1'] = 0;
		$data['rsl_half2_year1'] = 0;
		$data['rsl_half3_year1'] = 0;
		$data['rsl_half4_year1'] = 0;
		$data['rsl_half5_year1'] = 0;
		$data['rsl_half6_year1'] = 0;
		$data['rsl_half7_year1'] = 0;
		$data['rsl_half8_year1'] = 0;
		$data['rsl_half9_year1'] = 0;
		$data['rsl_half10_year1'] = 0;
		$data['rsl_half11_year1'] = 0;
		$data['rsl_half12_year1'] = 0;
		$data['rsl_half_avg_year1'] = 0;
		$data['rsl_half_min_year1'] = 0;
		$data['rsl_half_max_year1'] = 0;

		foreach($get_rsl_orc as $row){
			$orc_size = $row->size_name;
			$orc_jan = $row->orc_jan;
			$orc_feb = $row->orc_feb;
			$orc_mar = $row->orc_mar;
			$orc_apr = $row->orc_apr;
			$orc_may = $row->orc_may;
			$orc_jun = $row->orc_jun;
			$orc_jul = $row->orc_jul;
			$orc_aug = $row->orc_aug;
			$orc_sep = $row->orc_sep;
			$orc_oct = $row->orc_oct;
			$orc_nov = $row->orc_nov;
			$orc_dec = $row->orc_dec;
			$orc_min = min($orc_jan, $orc_feb, $orc_mar, $orc_apr, $orc_may, $orc_jun, $orc_jul, $orc_aug, $orc_sep, $orc_oct, $orc_nov, $orc_dec);
			$orc_max = max($orc_jan, $orc_feb, $orc_mar, $orc_apr, $orc_may, $orc_jun, $orc_jul, $orc_aug, $orc_sep, $orc_oct, $orc_nov, $orc_dec);
			$orc_avg = ($orc_jan + $orc_feb + $orc_mar + $orc_apr + $orc_may + $orc_jun + $orc_jul + $orc_aug + $orc_sep + $orc_oct + $orc_nov + $orc_dec) / 12;

			if($orc_size == "REGULAR"){
				$data['rsl_reg1'] = $orc_jan;
				$data['rsl_reg2'] = $orc_feb;
				$data['rsl_reg3'] = $orc_mar;
				$data['rsl_reg4'] = $orc_apr;
				$data['rsl_reg5'] = $orc_may;
				$data['rsl_reg6'] = $orc_jun;
				$data['rsl_reg7'] = $orc_jul;
				$data['rsl_reg8'] = $orc_aug;
				$data['rsl_reg9'] = $orc_sep;
				$data['rsl_reg10'] = $orc_oct;
				$data['rsl_reg11'] = $orc_nov;
				$data['rsl_reg12'] = $orc_dec;
				$data['rsl_reg_avg'] = $orc_avg;
				$data['rsl_reg_min'] = $orc_min;
				$data['rsl_reg_max'] = $orc_max;

				$reg_prev_year2 = $this->admin->get_data('comparative_price_tbl', array('comp_price_product' => 'ORC - REGULAR', 'comp_price_segment' => 'RSL', 'comp_price_year' => $year - 2,	'bc_id'	=>	$bc_id), 
			FALSE, 'AVG(comp_price_jan) as jan_price, AVG(comp_price_feb) as feb_price, AVG(comp_price_mar) as mar_price, AVG(comp_price_apr) as apr_price, AVG(comp_price_may) as may_price, AVG(comp_price_jun) as jun_price, AVG(comp_price_jul) as jul_price, AVG(comp_price_aug) as aug_price, AVG(comp_price_sep) as sep_price, AVG(comp_price_oct) as oct_price, AVG(comp_price_nov) as nov_price, AVG(comp_price_dec) as dec_price');

				foreach($reg_prev_year2 as $row){
					$data['rsl_reg1_year2'] = $row->jan_price;
					$data['rsl_reg2_year2'] = $row->feb_price;
					$data['rsl_reg3_year2'] = $row->mar_price;
					$data['rsl_reg4_year2'] = $row->apr_price;
					$data['rsl_reg5_year2'] = $row->may_price;
					$data['rsl_reg6_year2'] = $row->jun_price;
					$data['rsl_reg7_year2'] = $row->jul_price;
					$data['rsl_reg8_year2'] = $row->aug_price;
					$data['rsl_reg9_year2'] = $row->sep_price;
					$data['rsl_reg10_year2'] = $row->oct_price;
					$data['rsl_reg11_year2'] = $row->nov_price;
					$data['rsl_reg12_year2'] = $row->dec_price;
					$data['rsl_reg_avg_year2'] = ($row->jan_price + $row->feb_price + $row->mar_price + $row->apr_price + $row->may_price + $row->jun_price + $row->jul_price + $row->aug_price + $row->sep_price + $row->oct_price + $row->nov_price + $row->dec_price) / 12;

					$data['rsl_reg_min_year2'] = min($row->jan_price, $row->feb_price, $row->mar_price, $row->apr_price, $row->may_price, $row->jun_price, $row->jul_price, $row->aug_price, $row->sep_price, $row->oct_price, $row->nov_price, $row->dec_price);

					$data['rsl_reg_max_year2'] = max($row->jan_price, $row->feb_price, $row->mar_price, $row->apr_price, $row->may_price, $row->jun_price, $row->jul_price, $row->aug_price, $row->sep_price, $row->oct_price, $row->nov_price, $row->dec_price);
				}

				$reg_prev_year1 = $this->admin->get_data('comparative_price_tbl', array('comp_price_product' => 'ORC - REGULAR', 'comp_price_segment' => 'RSL', 'comp_price_year' => $year - 1,	'bc_id'	=>	$bc_id), 
			FALSE, 'AVG(comp_price_jan) as jan_price, AVG(comp_price_feb) as feb_price, AVG(comp_price_mar) as mar_price, AVG(comp_price_apr) as apr_price, AVG(comp_price_may) as may_price, AVG(comp_price_jun) as jun_price, AVG(comp_price_jul) as jul_price, AVG(comp_price_aug) as aug_price, AVG(comp_price_sep) as sep_price, AVG(comp_price_oct) as oct_price, AVG(comp_price_nov) as nov_price, AVG(comp_price_dec) as dec_price');

				foreach($reg_prev_year1 as $row){
					$data['rsl_reg1_year1'] = $row->jan_price;
					$data['rsl_reg2_year1'] = $row->feb_price;
					$data['rsl_reg3_year1'] = $row->mar_price;
					$data['rsl_reg4_year1'] = $row->apr_price;
					$data['rsl_reg5_year1'] = $row->may_price;
					$data['rsl_reg6_year1'] = $row->jun_price;
					$data['rsl_reg7_year1'] = $row->jul_price;
					$data['rsl_reg8_year1'] = $row->aug_price;
					$data['rsl_reg9_year1'] = $row->sep_price;
					$data['rsl_reg10_year1'] = $row->oct_price;
					$data['rsl_reg11_year1'] = $row->nov_price;
					$data['rsl_reg12_year1'] = $row->dec_price;
					$data['rsl_reg_avg_year1'] = ($row->jan_price + $row->feb_price + $row->mar_price + $row->apr_price + $row->may_price + $row->jun_price + $row->jul_price + $row->aug_price + $row->sep_price + $row->oct_price + $row->nov_price + $row->dec_price) / 12;

					$data['rsl_reg_min_year1'] = min($row->jan_price, $row->feb_price, $row->mar_price, $row->apr_price, $row->may_price, $row->jun_price, $row->jul_price, $row->aug_price, $row->sep_price, $row->oct_price, $row->nov_price, $row->dec_price);

					$data['rsl_reg_max_year1'] = max($row->jan_price, $row->feb_price, $row->mar_price, $row->apr_price, $row->may_price, $row->jun_price, $row->jul_price, $row->aug_price, $row->sep_price, $row->oct_price, $row->nov_price, $row->dec_price);
				}

			}elseif($orc_size == "JUMBO"){
				$data['rsl_jbo1'] = $orc_jan;
				$data['rsl_jbo2'] = $orc_feb;
				$data['rsl_jbo3'] = $orc_mar;
				$data['rsl_jbo4'] = $orc_apr;
				$data['rsl_jbo5'] = $orc_may;
				$data['rsl_jbo6'] = $orc_jun;
				$data['rsl_jbo7'] = $orc_jul;
				$data['rsl_jbo8'] = $orc_aug;
				$data['rsl_jbo9'] = $orc_sep;
				$data['rsl_jbo10'] = $orc_oct;
				$data['rsl_jbo11'] = $orc_nov;
				$data['rsl_jbo12'] = $orc_dec;
				$data['rsl_jbo_avg'] = $orc_avg;
				$data['rsl_jbo_min'] = $orc_min;
				$data['rsl_jbo_max'] = $orc_max;

				$jbo_prev_year2 = $this->admin->get_data('comparative_price_tbl', array('comp_price_product' => 'ORC - JUMBO', 'comp_price_segment' => 'RSL', 'comp_price_year' => $year - 2,	'bc_id'	=>	$bc_id), 
			FALSE, 'AVG(comp_price_jan) as jan_price, AVG(comp_price_feb) as feb_price, AVG(comp_price_mar) as mar_price, AVG(comp_price_apr) as apr_price, AVG(comp_price_may) as may_price, AVG(comp_price_jun) as jun_price, AVG(comp_price_jul) as jul_price, AVG(comp_price_aug) as aug_price, AVG(comp_price_sep) as sep_price, AVG(comp_price_oct) as oct_price, AVG(comp_price_nov) as nov_price, AVG(comp_price_dec) as dec_price');

				foreach($jbo_prev_year2 as $row){
					$data['rsl_jbo1_year2'] = $row->jan_price;
					$data['rsl_jbo2_year2'] = $row->feb_price;
					$data['rsl_jbo3_year2'] = $row->mar_price;
					$data['rsl_jbo4_year2'] = $row->apr_price;
					$data['rsl_jbo5_year2'] = $row->may_price;
					$data['rsl_jbo6_year2'] = $row->jun_price;
					$data['rsl_jbo7_year2'] = $row->jul_price;
					$data['rsl_jbo8_year2'] = $row->aug_price;
					$data['rsl_jbo9_year2'] = $row->sep_price;
					$data['rsl_jbo10_year2'] = $row->oct_price;
					$data['rsl_jbo11_year2'] = $row->nov_price;
					$data['rsl_jbo12_year2'] = $row->dec_price;
					$data['rsl_jbo_year2_avg'] = ($row->jan_price + $row->feb_price + $row->mar_price + $row->apr_price + $row->may_price + $row->jun_price + $row->jul_price + $row->aug_price + $row->sep_price + $row->oct_price + $row->nov_price + $row->dec_price) / 12;

					$data['rsl_jbo_year2_min'] = min($row->jan_price, $row->feb_price, $row->mar_price, $row->apr_price, $row->may_price, $row->jun_price, $row->jul_price, $row->aug_price, $row->sep_price, $row->oct_price, $row->nov_price, $row->dec_price);

					$data['rsl_jbo_year2_max'] = max($row->jan_price, $row->feb_price, $row->mar_price, $row->apr_price, $row->may_price, $row->jun_price, $row->jul_price, $row->aug_price, $row->sep_price, $row->oct_price, $row->nov_price, $row->dec_price);
				}

				$jbo_prev_year1 = $this->admin->get_data('comparative_price_tbl', array('comp_price_product' => 'ORC - JUMBO', 'comp_price_segment' => 'RSL', 'comp_price_year' => $year - 1,	'bc_id'	=>	$bc_id), 
			FALSE, 'AVG(comp_price_jan) as jan_price, AVG(comp_price_feb) as feb_price, AVG(comp_price_mar) as mar_price, AVG(comp_price_apr) as apr_price, AVG(comp_price_may) as may_price, AVG(comp_price_jun) as jun_price, AVG(comp_price_jul) as jul_price, AVG(comp_price_aug) as aug_price, AVG(comp_price_sep) as sep_price, AVG(comp_price_oct) as oct_price, AVG(comp_price_nov) as nov_price, AVG(comp_price_dec) as dec_price');

				foreach($jbo_prev_year1 as $row){
					$data['rsl_jbo1_year1'] = $row->jan_price;
					$data['rsl_jbo2_year1'] = $row->feb_price;
					$data['rsl_jbo3_year1'] = $row->mar_price;
					$data['rsl_jbo4_year1'] = $row->apr_price;
					$data['rsl_jbo5_year1'] = $row->may_price;
					$data['rsl_jbo6_year1'] = $row->jun_price;
					$data['rsl_jbo7_year1'] = $row->jul_price;
					$data['rsl_jbo8_year1'] = $row->aug_price;
					$data['rsl_jbo9_year1'] = $row->sep_price;
					$data['rsl_jbo10_year1'] = $row->oct_price;
					$data['rsl_jbo11_year1'] = $row->nov_price;
					$data['rsl_jbo12_year1'] = $row->dec_price;
					$data['rsl_jbo_year1_avg'] = ($row->jan_price + $row->feb_price + $row->mar_price + $row->apr_price + $row->may_price + $row->jun_price + $row->jul_price + $row->aug_price + $row->sep_price + $row->oct_price + $row->nov_price + $row->dec_price) / 12;

					$data['rsl_jbo_year1_min'] = min($row->jan_price, $row->feb_price, $row->mar_price, $row->apr_price, $row->may_price, $row->jun_price, $row->jul_price, $row->aug_price, $row->sep_price, $row->oct_price, $row->nov_price, $row->dec_price);

					$data['rsl_jbo_year1_max'] = max($row->jan_price, $row->feb_price, $row->mar_price, $row->apr_price, $row->may_price, $row->jun_price, $row->jul_price, $row->aug_price, $row->sep_price, $row->oct_price, $row->nov_price, $row->dec_price);
				}

			}elseif($orc_size == "SUPERSIZE"){
				$data['rsl_ss1'] = $orc_jan;
				$data['rsl_ss2'] = $orc_feb;
				$data['rsl_ss3'] = $orc_mar;
				$data['rsl_ss4'] = $orc_apr;
				$data['rsl_ss5'] = $orc_may;
				$data['rsl_ss6'] = $orc_jun;
				$data['rsl_ss7'] = $orc_jul;
				$data['rsl_ss8'] = $orc_aug;
				$data['rsl_ss9'] = $orc_sep;
				$data['rsl_ss10'] = $orc_oct;
				$data['rsl_ss11'] = $orc_nov;
				$data['rsl_ss12'] = $orc_dec;
				$data['rsl_ss_avg'] = $orc_avg;
				$data['rsl_ss_min'] = $orc_min;
				$data['rsl_ss_max'] = $orc_max;

				$ss_prev_year2 = $this->admin->get_data('comparative_price_tbl', array('comp_price_product' => 'ORC - SUPERSIZE', 'comp_price_segment' => 'RSL', 'comp_price_year' => $year - 2, 'bc_id'	=>	$bc_id), 
			FALSE, 'AVG(comp_price_jan) as jan_price, AVG(comp_price_feb) as feb_price, AVG(comp_price_mar) as mar_price, AVG(comp_price_apr) as apr_price, AVG(comp_price_may) as may_price, AVG(comp_price_jun) as jun_price, AVG(comp_price_jul) as jul_price, AVG(comp_price_aug) as aug_price, AVG(comp_price_sep) as sep_price, AVG(comp_price_oct) as oct_price, AVG(comp_price_nov) as nov_price, AVG(comp_price_dec) as dec_price');

				foreach($ss_prev_year2 as $row){
					$data['rsl_ss1_year2'] = $row->jan_price;
					$data['rsl_ss2_year2'] = $row->feb_price;
					$data['rsl_ss3_year2'] = $row->mar_price;
					$data['rsl_ss4_year2'] = $row->apr_price;
					$data['rsl_ss5_year2'] = $row->may_price;
					$data['rsl_ss6_year2'] = $row->jun_price;
					$data['rsl_ss7_year2'] = $row->jul_price;
					$data['rsl_ss8_year2'] = $row->aug_price;
					$data['rsl_ss9_year2'] = $row->sep_price;
					$data['rsl_ss10_year2'] = $row->oct_price;
					$data['rsl_ss11_year2'] = $row->nov_price;
					$data['rsl_ss12_year2'] = $row->dec_price;
					$data['rsl_ss_avg_year2'] = ($row->jan_price + $row->feb_price + $row->mar_price + $row->apr_price + $row->may_price + $row->jun_price + $row->jul_price + $row->aug_price + $row->sep_price + $row->oct_price + $row->nov_price + $row->dec_price) / 12;

					$data['rsl_ss_min_year2'] = min($row->jan_price, $row->feb_price, $row->mar_price, $row->apr_price, $row->may_price, $row->jun_price, $row->jul_price, $row->aug_price, $row->sep_price, $row->oct_price, $row->nov_price, $row->dec_price);

					$data['rsl_ss_max_year2'] = max($row->jan_price, $row->feb_price, $row->mar_price, $row->apr_price, $row->may_price, $row->jun_price, $row->jul_price, $row->aug_price, $row->sep_price, $row->oct_price, $row->nov_price, $row->dec_price);
				}

				$ss_prev_year1 = $this->admin->get_data('comparative_price_tbl', array('comp_price_product' => 'ORC - SUPERSIZE', 'comp_price_segment' => 'RSL', 'comp_price_year' => $year - 1, 'bc_id'	=>	$bc_id), 
			FALSE, 'AVG(comp_price_jan) as jan_price, AVG(comp_price_feb) as feb_price, AVG(comp_price_mar) as mar_price, AVG(comp_price_apr) as apr_price, AVG(comp_price_may) as may_price, AVG(comp_price_jun) as jun_price, AVG(comp_price_jul) as jul_price, AVG(comp_price_aug) as aug_price, AVG(comp_price_sep) as sep_price, AVG(comp_price_oct) as oct_price, AVG(comp_price_nov) as nov_price, AVG(comp_price_dec) as dec_price');

				foreach($ss_prev_year1 as $row){
					$data['rsl_ss1_year1'] = $row->jan_price;
					$data['rsl_ss2_year1'] = $row->feb_price;
					$data['rsl_ss3_year1'] = $row->mar_price;
					$data['rsl_ss4_year1'] = $row->apr_price;
					$data['rsl_ss5_year1'] = $row->may_price;
					$data['rsl_ss6_year1'] = $row->jun_price;
					$data['rsl_ss7_year1'] = $row->jul_price;
					$data['rsl_ss8_year1'] = $row->aug_price;
					$data['rsl_ss9_year1'] = $row->sep_price;
					$data['rsl_ss10_year1'] = $row->oct_price;
					$data['rsl_ss11_year1'] = $row->nov_price;
					$data['rsl_ss12_year1'] = $row->dec_price;
					$data['rsl_ss_avg_year1'] = ($row->jan_price + $row->feb_price + $row->mar_price + $row->apr_price + $row->may_price + $row->jun_price + $row->jul_price + $row->aug_price + $row->sep_price + $row->oct_price + $row->nov_price + $row->dec_price) / 12;

					$data['rsl_ss_min_year1'] = min($row->jan_price, $row->feb_price, $row->mar_price, $row->apr_price, $row->may_price, $row->jun_price, $row->jul_price, $row->aug_price, $row->sep_price, $row->oct_price, $row->nov_price, $row->dec_price);

					$data['rsl_ss_max_year1'] = max($row->jan_price, $row->feb_price, $row->mar_price, $row->apr_price, $row->may_price, $row->jun_price, $row->jul_price, $row->aug_price, $row->sep_price, $row->oct_price, $row->nov_price, $row->dec_price);
				}

			}elseif($orc_size == "BIGTIME"){
				$data['rsl_bt1'] = $orc_jan;
				$data['rsl_bt2'] = $orc_feb;
				$data['rsl_bt3'] = $orc_mar;
				$data['rsl_bt4'] = $orc_apr;
				$data['rsl_bt5'] = $orc_may;
				$data['rsl_bt6'] = $orc_jun;
				$data['rsl_bt7'] = $orc_jul;
				$data['rsl_bt8'] = $orc_aug;
				$data['rsl_bt9'] = $orc_sep;
				$data['rsl_bt10'] = $orc_oct;
				$data['rsl_bt11'] = $orc_nov;
				$data['rsl_bt12'] = $orc_dec;
				$data['rsl_bt_avg'] = $orc_avg;
				$data['crsl_bt_min'] = $orc_min;
				$data['rsl_bt_max'] = $orc_max;


				$bt_prev_year2 = $this->admin->get_data('comparative_price_tbl', array('comp_price_product' => 'ORC - BIGTIME', 'comp_price_segment' => 'RSL', 'comp_price_year' => $year - 2, 'bc_id'	=>	$bc_id),
			FALSE, 'AVG(comp_price_jan) as jan_price, AVG(comp_price_feb) as feb_price, AVG(comp_price_mar) as mar_price, AVG(comp_price_apr) as apr_price, AVG(comp_price_may) as may_price, AVG(comp_price_jun) as jun_price, AVG(comp_price_jul) as jul_price, AVG(comp_price_aug) as aug_price, AVG(comp_price_sep) as sep_price, AVG(comp_price_oct) as oct_price, AVG(comp_price_nov) as nov_price, AVG(comp_price_dec) as dec_price');

				foreach($bt_prev_year2 as $row){
					$data['rsl_bt1_year2'] = $row->jan_price;
					$data['rsl_bt2_year2'] = $row->feb_price;
					$data['rsl_bt3_year2'] = $row->mar_price;
					$data['rsl_bt4_year2'] = $row->apr_price;
					$data['rsl_bt5_year2'] = $row->may_price;
					$data['rsl_bt6_year2'] = $row->jun_price;
					$data['rsl_bt7_year2'] = $row->jul_price;
					$data['rsl_bt8_year2'] = $row->aug_price;
					$data['rsl_bt9_year2'] = $row->sep_price;
					$data['rsl_bt10_year2'] = $row->oct_price;
					$data['rsl_bt11_year2'] = $row->nov_price;
					$data['rsl_bt12_year2'] = $row->dec_price;
					$data['rsl_bt_avg_year2'] = ($row->jan_price + $row->feb_price + $row->mar_price + $row->apr_price + $row->may_price + $row->jun_price + $row->jul_price + $row->aug_price + $row->sep_price + $row->oct_price + $row->nov_price + $row->dec_price) / 12;

					$data['rsl_bt_min_year2'] = min($row->jan_price, $row->feb_price, $row->mar_price, $row->apr_price, $row->may_price, $row->jun_price, $row->jul_price, $row->aug_price, $row->sep_price, $row->oct_price, $row->nov_price, $row->dec_price);

					$data['rsl_bt_max_year2'] = max($row->jan_price, $row->feb_price, $row->mar_price, $row->apr_price, $row->may_price, $row->jun_price, $row->jul_price, $row->aug_price, $row->sep_price, $row->oct_price, $row->nov_price, $row->dec_price);
				}

				$bt_prev_year1 = $this->admin->get_data('comparative_price_tbl', array('comp_price_product' => 'ORC - BIGTIME', 'comp_price_segment' => 'RSL', 'comp_price_year' => $year - 1, 'bc_id'	=>	$bc_id), 
			FALSE, 'AVG(comp_price_jan) as jan_price, AVG(comp_price_feb) as feb_price, AVG(comp_price_mar) as mar_price, AVG(comp_price_apr) as apr_price, AVG(comp_price_may) as may_price, AVG(comp_price_jun) as jun_price, AVG(comp_price_jul) as jul_price, AVG(comp_price_aug) as aug_price, AVG(comp_price_sep) as sep_price, AVG(comp_price_oct) as oct_price, AVG(comp_price_nov) as nov_price, AVG(comp_price_dec) as dec_price');

				foreach($bt_prev_year1 as $row){
					$data['rsl_bt1_year1'] = $row->jan_price;
					$data['rsl_bt2_year1'] = $row->feb_price;
					$data['rsl_bt3_year1'] = $row->mar_price;
					$data['rsl_bt4_year1'] = $row->apr_price;
					$data['rsl_bt5_year1'] = $row->may_price;
					$data['rsl_bt6_year1'] = $row->jun_price;
					$data['rsl_bt7_year1'] = $row->jul_price;
					$data['rsl_bt8_year1'] = $row->aug_price;
					$data['rsl_bt9_year1'] = $row->sep_price;
					$data['rsl_bt10_year1'] = $row->oct_price;
					$data['rsl_bt11_year1'] = $row->nov_price;
					$data['rsl_bt12_year1'] = $row->dec_price;
					$data['rsl_bt_avg_year1'] = ($row->jan_price + $row->feb_price + $row->mar_price + $row->apr_price + $row->may_price + $row->jun_price + $row->jul_price + $row->aug_price + $row->sep_price + $row->oct_price + $row->nov_price + $row->dec_price) / 12;

					$data['rsl_bt_min_year1'] = min($row->jan_price, $row->feb_price, $row->mar_price, $row->apr_price, $row->may_price, $row->jun_price, $row->jul_price, $row->aug_price, $row->sep_price, $row->oct_price, $row->nov_price, $row->dec_price);

					$data['rsl_bt_max_year1'] = max($row->jan_price, $row->feb_price, $row->mar_price, $row->apr_price, $row->may_price, $row->jun_price, $row->jul_price, $row->aug_price, $row->sep_price, $row->oct_price, $row->nov_price, $row->dec_price);
				}

			}elseif($orc_size == "HALF"){
				$data['rsl_half1'] = $orc_jan;
				$data['rsl_half2'] = $orc_feb;
				$data['rsl_half3'] = $orc_mar;
				$data['rsl_half4'] = $orc_apr;
				$data['rsl_half5'] = $orc_may;
				$data['rsl_half6'] = $orc_jun;
				$data['rsl_half7'] = $orc_jul;
				$data['rsl_half8'] = $orc_aug;
				$data['rsl_half9'] = $orc_sep;
				$data['rsl_half10'] = $orc_oct;
				$data['rsl_half11'] = $orc_nov;
				$data['rsl_half12'] = $orc_dec;
				$data['rsl_half_avg'] = $orc_avg;
				$data['rsl_half_min'] = $orc_min;
				$data['rsl_half_max'] = $orc_max;

				$half_prev_year2 = $this->admin->get_data('comparative_price_tbl', array('comp_price_product' => 'ORC - HALF', 'comp_price_segment' => 'RSL', 'comp_price_year' => $year - 2,	'bc_id'	=>	$bc_id),
			FALSE, 'AVG(comp_price_jan) as jan_price, AVG(comp_price_feb) as feb_price, AVG(comp_price_mar) as mar_price, AVG(comp_price_apr) as apr_price, AVG(comp_price_may) as may_price, AVG(comp_price_jun) as jun_price, AVG(comp_price_jul) as jul_price, AVG(comp_price_aug) as aug_price, AVG(comp_price_sep) as sep_price, AVG(comp_price_oct) as oct_price, AVG(comp_price_nov) as nov_price, AVG(comp_price_dec) as dec_price');

				foreach($half_prev_year2 as $row){
					$data['rsl_half1_year2'] = $row->jan_price;
					$data['rsl_half2_year2'] = $row->feb_price;
					$data['rsl_half3_year2'] = $row->mar_price;
					$data['rsl_half4_year2'] = $row->apr_price;
					$data['rsl_half5_year2'] = $row->may_price;
					$data['rsl_half6_year2'] = $row->jun_price;
					$data['rsl_half7_year2'] = $row->jul_price;
					$data['rsl_half8_year2'] = $row->aug_price;
					$data['rsl_half9_year2'] = $row->sep_price;
					$data['rsl_half10_year2'] = $row->oct_price;
					$data['rsl_half11_year2'] = $row->nov_price;
					$data['rsl_half12_year2'] = $row->dec_price;
					$data['rsl_half_avg_year2'] = ($row->jan_price + $row->feb_price + $row->mar_price + $row->apr_price + $row->may_price + $row->jun_price + $row->jul_price + $row->aug_price + $row->sep_price + $row->oct_price + $row->nov_price + $row->dec_price) / 12;

					$data['rsl_half_min_year2'] = min($row->jan_price, $row->feb_price, $row->mar_price, $row->apr_price, $row->may_price, $row->jun_price, $row->jul_price, $row->aug_price, $row->sep_price, $row->oct_price, $row->nov_price, $row->dec_price);

					$data['rsl_half_max_year2'] = max($row->jan_price, $row->feb_price, $row->mar_price, $row->apr_price, $row->may_price, $row->jun_price, $row->jul_price, $row->aug_price, $row->sep_price, $row->oct_price, $row->nov_price, $row->dec_price);
				}

				$half_prev_year1 = $this->admin->get_data('comparative_price_tbl', array('comp_price_product' => 'ORC - HALF', 'comp_price_segment' => 'RSL', 'comp_price_year' => $year - 1,	'bc_id'	=>	$bc_id), 
			FALSE, 'AVG(comp_price_jan) as jan_price, AVG(comp_price_feb) as feb_price, AVG(comp_price_mar) as mar_price, AVG(comp_price_apr) as apr_price, AVG(comp_price_may) as may_price, AVG(comp_price_jun) as jun_price, AVG(comp_price_jul) as jul_price, AVG(comp_price_aug) as aug_price, AVG(comp_price_sep) as sep_price, AVG(comp_price_oct) as oct_price, AVG(comp_price_nov) as nov_price, AVG(comp_price_dec) as dec_price');

				foreach($half_prev_year1 as $row){
					$data['rsl_half1_year1'] = $row->jan_price;
					$data['rsl_half2_year1'] = $row->feb_price;
					$data['rsl_half3_year1'] = $row->mar_price;
					$data['rsl_half4_year1'] = $row->apr_price;
					$data['rsl_half5_year1'] = $row->may_price;
					$data['rsl_half6_year1'] = $row->jun_price;
					$data['rsl_half7_year1'] = $row->jul_price;
					$data['rsl_half8_year1'] = $row->aug_price;
					$data['rsl_half9_year1'] = $row->sep_price;
					$data['rsl_half10_year1'] = $row->oct_price;
					$data['rsl_half11_year1'] = $row->nov_price;
					$data['rsl_half12_year1'] = $row->dec_price;
					$data['rsl_half_avg_year1'] = ($row->jan_price + $row->feb_price + $row->mar_price + $row->apr_price + $row->may_price + $row->jun_price + $row->jul_price + $row->aug_price + $row->sep_price + $row->oct_price + $row->nov_price + $row->dec_price) / 12;

					$data['rsl_half_min_year1'] = min($row->jan_price, $row->feb_price, $row->mar_price, $row->apr_price, $row->may_price, $row->jun_price, $row->jul_price, $row->aug_price, $row->sep_price, $row->oct_price, $row->nov_price, $row->dec_price);

					$data['rsl_half_max_year1'] = max($row->jan_price, $row->feb_price, $row->mar_price, $row->apr_price, $row->may_price, $row->jun_price, $row->jul_price, $row->aug_price, $row->sep_price, $row->oct_price, $row->nov_price, $row->dec_price);
				}
			}
		}

		/*RSL Liempo*/
		$join_rsl_liempo = array(
			'sales_item_tbl b' => 'a.sales_id = b.sales_id AND a.sales_status = 1 AND b.sales_item_status = 1 AND a.sales_year = ' . $year,
			'sales_details_tbl c' => 'b.sales_item_id = c.sales_item_id AND c.sales_det_status = 1',
			'material_tbl d' => 'b.material_id = d.material_id',
			'material_group_tbl e' => 'd.material_group_id = e.material_group_id AND e.material_group_name = "LIEMPO"',
			'outlet_tbl f' => 'a.outlet_id = f.outlet_id AND f.bc_id = ' . $bc_id,
			'outlet_brand_tbl g' => 'f.outlet_id = g.outlet_id AND g.outlet_brand_status = 1',
			'brand_tbl h' => 'g.brand_id  = h.brand_id AND h.brand_name = \'RESELLER\''
		);

		$get_rsl_liempo = $this->admin->get_join('sales_tbl a', $join_rsl_liempo, FALSE, FALSE, 'YEAR(c.sales_det_date), MONTH(c.sales_det_date)', 'AVG(c.sales_det_asp) as asp, MONTH(c.sales_det_date) asp_date');

		$data['rsl_liempo1'] = 0;
		$data['rsl_liempo2'] = 0;
		$data['rsl_liempo3'] = 0;
		$data['rsl_liempo4'] = 0;
		$data['rsl_liempo5'] = 0;
		$data['rsl_liempo6'] = 0;
		$data['rsl_liempo7'] = 0;
		$data['rsl_liempo8'] = 0;
		$data['rsl_liempo9'] = 0;
		$data['rsl_liempo10'] = 0;
		$data['rsl_liempo11'] = 0;
		$data['rsl_liempo12'] = 0;
		$data['rsl_liempo_total'] = 0;
		$data['rsl_liempo_count'] = 0;
		$data['rsl_liempo_avg'] = 0;
		$data['rsl_liempo_min'] = 0;
		$data['rsl_liempo_max'] = 0;

		foreach($get_rsl_liempo as $row){
			$asp = $row->asp;
			$month = $row->asp_date;
			$data['liempo' . $month] = $asp;
			$data['liempo_total'] += $asp;

			if($asp < $data['rsl_liempo_min'] || $data['rsl_liempo_count'] == 0){
				$data['rsl_liempo_min'] = $asp;
			}

			if($asp > $data['rsl_liempo_max'] || $data['rsl_liempo_count'] == 0){
				$data['rsl_liempo_max'] = $asp;
			}

			$data['rsl_liempo_count']++;
		}

		$data['rsl_liempo_avg'] = $data['rsl_liempo_total'] != 0 ? $data['rsl_liempo_total'] / $data['rsl_liempo_count'] : 0;

		$rsl_liempo_prev_year2 = $this->admin->get_data('comparative_price_tbl', array('comp_price_product' => 'LIEMPO', 'comp_price_segment' => 'RSL', 'comp_price_year' => $year - 2,	'bc_id'	=>	$bc_id), 
			FALSE, 'AVG(comp_price_jan) as jan_price, AVG(comp_price_feb) as feb_price, AVG(comp_price_mar) as mar_price, AVG(comp_price_apr) as apr_price, AVG(comp_price_may) as may_price, AVG(comp_price_jun) as jun_price, AVG(comp_price_jul) as jul_price, AVG(comp_price_aug) as aug_price, AVG(comp_price_sep) as sep_price, AVG(comp_price_oct) as oct_price, AVG(comp_price_nov) as nov_price, AVG(comp_price_dec) as dec_price');
		
		$data['rsl_liempo_prev_year2_jan'] = 0;
		$data['rsl_liempo_prev_year2_feb'] = 0;
		$data['rsl_liempo_prev_year2_mar'] = 0;
		$data['rsl_liempo_prev_year2_apr'] = 0;
		$data['rsl_liempo_prev_year2_may'] = 0;
		$data['rsl_liempo_prev_year2_jun'] = 0;
		$data['rsl_liempo_prev_year2_jul'] = 0;
		$data['rsl_liempo_prev_year2_aug'] = 0;
		$data['rsl_liempo_prev_year2_sep'] = 0;
		$data['rsl_liempo_prev_year2_oct'] = 0;
		$data['rsl_liempo_prev_year2_nov'] = 0;
		$data['rsl_liempo_prev_year2_dec'] = 0;
		$data['rsl_liempo_prev_year2_avg'] = 0;
		$data['rsl_liempo_prev_year2_min'] = 0;
		$data['rsl_liempo_prev_year2_max'] = 0;

		foreach($rsl_liempo_prev_year2 as $row){
			$data['rsl_liempo_prev_year2_jan'] = $row->jan_price;
			$data['rsl_liempo_prev_year2_feb'] = $row->feb_price;
			$data['rsl_liempo_prev_year2_mar'] = $row->mar_price;
			$data['rsl_liempo_prev_year2_apr'] = $row->apr_price;
			$data['rsl_liempo_prev_year2_may'] = $row->may_price;
			$data['rsl_liempo_prev_year2_jun'] = $row->jun_price;
			$data['rsl_liempo_prev_year2_jul'] = $row->jul_price;
			$data['rsl_liempo_prev_year2_aug'] = $row->aug_price;
			$data['rsl_liempo_prev_year2_sep'] = $row->sep_price;
			$data['rsl_liempo_prev_year2_oct'] = $row->oct_price;
			$data['rsl_liempo_prev_year2_nov'] = $row->nov_price;
			$data['rsl_liempo_prev_year2_dec'] = $row->dec_price;
			$data['rsl_liempo_prev_year2_avg'] = ($row->jan_price + $row->feb_price + $row->mar_price + $row->apr_price + $row->may_price + $row->jun_price + $row->jul_price + $row->aug_price + $row->sep_price + $row->oct_price + $row->nov_price + $row->dec_price) / 12;

			$data['rsl_liempo_prev_year2_min'] = min($row->jan_price, $row->feb_price, $row->mar_price, $row->apr_price, $row->may_price, $row->jun_price, $row->jul_price, $row->aug_price, $row->sep_price, $row->oct_price, $row->nov_price, $row->dec_price);

			$data['rsl_liempo_prev_year2_max'] = max($row->jan_price, $row->feb_price, $row->mar_price, $row->apr_price, $row->may_price, $row->jun_price, $row->jul_price, $row->aug_price, $row->sep_price, $row->oct_price, $row->nov_price, $row->dec_price);
		}


		$rsl_liempo_prev_year1 = $this->admin->get_data('comparative_price_tbl', array('comp_price_product' => 'LIEMPO', 'comp_price_segment' => 'RSL', 'comp_price_year' => $year - 1,	'bc_id'	=>	$bc_id), 
			FALSE, 'AVG(comp_price_jan) as jan_price, AVG(comp_price_feb) as feb_price, AVG(comp_price_mar) as mar_price, AVG(comp_price_apr) as apr_price, AVG(comp_price_may) as may_price, AVG(comp_price_jun) as jun_price, AVG(comp_price_jul) as jul_price, AVG(comp_price_aug) as aug_price, AVG(comp_price_sep) as sep_price, AVG(comp_price_oct) as oct_price, AVG(comp_price_nov) as nov_price, AVG(comp_price_dec) as dec_price');
		
		$data['rsl_liempo_prev_year1_jan'] = 0;
		$data['rsl_liempo_prev_year1_feb'] = 0;
		$data['rsl_liempo_prev_year1_mar'] = 0;
		$data['rsl_liempo_prev_year1_apr'] = 0;
		$data['rsl_liempo_prev_year1_may'] = 0;
		$data['rsl_liempo_prev_year1_jun'] = 0;
		$data['rsl_liempo_prev_year1_jul'] = 0;
		$data['rsl_liempo_prev_year1_aug'] = 0;
		$data['rsl_liempo_prev_year1_sep'] = 0;
		$data['rsl_liempo_prev_year1_oct'] = 0;
		$data['rsl_liempo_prev_year1_nov'] = 0;
		$data['rsl_liempo_prev_year1_dec'] = 0;
		$data['rsl_liempo_prev_year1_avg'] = 0;
		$data['rsl_liempo_prev_year1_min'] = 0;
		$data['rsl_liempo_prev_year1_max'] = 0;

		foreach($rsl_liempo_prev_year1 as $row){
			$data['rsl_liempo_prev_year1_jan'] = $row->jan_price;
			$data['rsl_liempo_prev_year1_feb'] = $row->feb_price;
			$data['rsl_liempo_prev_year1_mar'] = $row->mar_price;
			$data['rsl_liempo_prev_year1_apr'] = $row->apr_price;
			$data['rsl_liempo_prev_year1_may'] = $row->may_price;
			$data['rsl_liempo_prev_year1_jun'] = $row->jun_price;
			$data['rsl_liempo_prev_year1_jul'] = $row->jul_price;
			$data['rsl_liempo_prev_year1_aug'] = $row->aug_price;
			$data['rsl_liempo_prev_year1_sep'] = $row->sep_price;
			$data['rsl_liempo_prev_year1_oct'] = $row->oct_price;
			$data['rsl_liempo_prev_year1_nov'] = $row->nov_price;
			$data['rsl_liempo_prev_year1_dec'] = $row->dec_price;
			$data['rsl_liempo_prev_year1_avg'] = ($row->jan_price + $row->feb_price + $row->mar_price + $row->apr_price + $row->may_price + $row->jun_price + $row->jul_price + $row->aug_price + $row->sep_price + $row->oct_price + $row->nov_price + $row->dec_price) / 12;

			$data['rsl_liempo_prev_year1_min'] = min($row->jan_price, $row->feb_price, $row->mar_price, $row->apr_price, $row->may_price, $row->jun_price, $row->jul_price, $row->aug_price, $row->sep_price, $row->oct_price, $row->nov_price, $row->dec_price);

			$data['rsl_liempo_prev_year1_max'] = max($row->jan_price, $row->feb_price, $row->mar_price, $row->apr_price, $row->may_price, $row->jun_price, $row->jul_price, $row->aug_price, $row->sep_price, $row->oct_price, $row->nov_price, $row->dec_price);
		}


		/*Reseller Dressed Chicken*/
		$join_rsl_dressed = array(
			'sales_item_tbl b' => 'a.sales_id = b.sales_id AND a.sales_status = 1 AND b.sales_item_status = 1 AND a.sales_year = ' . $year,
			'sales_details_tbl c' => 'b.sales_item_id = c.sales_item_id AND c.sales_det_status = 1',
			'material_tbl d' => 'b.material_id = d.material_id',
			'material_group_tbl e' => 'd.material_group_id = e.material_group_id AND e.material_group_name = "DRESSED"',
			'outlet_tbl f' => 'a.outlet_id = f.outlet_id AND f.bc_id =' . $bc_id,
			'outlet_brand_tbl g' => 'f.outlet_id = g.outlet_id AND g.outlet_brand_status = 1',
			'brand_tbl h' => 'g.brand_id  = h.brand_id AND h.brand_name = \'RESELLER\''
		);

		$get_rsl_dressed = $this->admin->get_join('sales_tbl a', $join_rsl_dressed, FALSE, FALSE, 'YEAR(c.sales_det_date), MONTH(c.sales_det_date)', 'AVG(c.sales_det_asp) as asp, MONTH(c.sales_det_date) asp_date');

		$data['rsl_dressed1'] = 0;
		$data['rsl_dressed2'] = 0;
		$data['rsl_dressed3'] = 0;
		$data['rsl_dressed4'] = 0;
		$data['rsl_dressed5'] = 0;
		$data['rsl_dressed6'] = 0;
		$data['rsl_dressed7'] = 0;
		$data['rsl_dressed8'] = 0;
		$data['rsl_dressed9'] = 0;
		$data['rsl_dressed10'] = 0;
		$data['rsl_dressed11'] = 0;
		$data['rsl_dressed12'] = 0;
		$data['rsl_dressed_total'] = 0;
		$data['rsl_dressed_count'] = 0;
		$data['rsl_dressed_avg'] = 0;
		$data['rsl_dressed_min'] = 0;
		$data['rsl_dressed_max'] = 0;

		foreach($get_rsl_dressed as $row){
			$asp = $row->asp;
			$month = $row->asp_date;
			$data['rsl_dressed' . $month] = $asp;
			$data['rsl_dressed_total'] += $asp;

			if($asp < $data['rsl_dressed_min'] || $data['rsl_dressed_count'] == 0){
				$data['rsl_dressed_min'] = $asp;
			}

			if($asp > $data['rsl_dressed_max'] || $data['rsl_dressed_count'] == 0){
				$data['rsl_dressed_max'] = $asp;
			}

			$data['rsl_dressed_count']++;
		}

		$data['rsl_dressed_avg'] = $data['rsl_dressed_total'] != 0 ? $data['rsl_dressed_total'] / $data['rsl_dressed_count'] : 0;

		$rsl_dressed_prev_year2 = $this->admin->get_data('comparative_price_tbl', array('comp_price_product' => 'DRESSED', 'comp_price_segment' => 'RSL', 'comp_price_year' => $year - 2,	'bc_id'	=>	$bc_id), 
			FALSE, 'AVG(comp_price_jan) as jan_price, AVG(comp_price_feb) as feb_price, AVG(comp_price_mar) as mar_price, AVG(comp_price_apr) as apr_price, AVG(comp_price_may) as may_price, AVG(comp_price_jun) as jun_price, AVG(comp_price_jul) as jul_price, AVG(comp_price_aug) as aug_price, AVG(comp_price_sep) as sep_price, AVG(comp_price_oct) as oct_price, AVG(comp_price_nov) as nov_price, AVG(comp_price_dec) as dec_price');
		
		$data['rsl_dressed_prev_year2_jan'] = 0;
		$data['rsl_dressed_prev_year2_feb'] = 0;
		$data['rsl_dressed_prev_year2_mar'] = 0;
		$data['rsl_dressed_prev_year2_apr'] = 0;
		$data['rsl_dressed_prev_year2_may'] = 0;
		$data['rsl_dressed_prev_year2_jun'] = 0;
		$data['rsl_dressed_prev_year2_jul'] = 0;
		$data['rsl_dressed_prev_year2_aug'] = 0;
		$data['rsl_dressed_prev_year2_sep'] = 0;
		$data['rsl_dressed_prev_year2_oct'] = 0;
		$data['rsl_dressed_prev_year2_nov'] = 0;
		$data['rsl_dressed_prev_year2_dec'] = 0;
		$data['rsl_dressed_prev_year2_avg'] = 0;
		$data['rsl_dressed_prev_year2_min'] = 0;
		$data['rsl_dressed_prev_year2_max'] = 0;

		foreach($rsl_dressed_prev_year2 as $row){
			$data['rsl_dressed_prev_year2_jan'] = $row->jan_price;
			$data['rsl_dressed_prev_year2_feb'] = $row->feb_price;
			$data['rsl_dressed_prev_year2_mar'] = $row->mar_price;
			$data['rsl_dressed_prev_year2_apr'] = $row->apr_price;
			$data['rsl_dressed_prev_year2_may'] = $row->may_price;
			$data['rsl_dressed_prev_year2_jun'] = $row->jun_price;
			$data['rsl_dressed_prev_year2_jul'] = $row->jul_price;
			$data['rsl_dressed_prev_year2_aug'] = $row->aug_price;
			$data['rsl_dressed_prev_year2_sep'] = $row->sep_price;
			$data['rsl_dressed_prev_year2_oct'] = $row->oct_price;
			$data['rsl_dressed_prev_year2_nov'] = $row->nov_price;
			$data['rsl_dressed_prev_year2_dec'] = $row->dec_price;
			$data['rsl_dressed_prev_year2_avg'] = ($row->jan_price + $row->feb_price + $row->mar_price + $row->apr_price + $row->may_price + $row->jun_price + $row->jul_price + $row->aug_price + $row->sep_price + $row->oct_price + $row->nov_price + $row->dec_price) / 12;

			$data['rsl_dressed_prev_year2_min'] = min($row->jan_price, $row->feb_price, $row->mar_price, $row->apr_price, $row->may_price, $row->jun_price, $row->jul_price, $row->aug_price, $row->sep_price, $row->oct_price, $row->nov_price, $row->dec_price);

			$data['rsl_dressed_prev_year2_max'] = max($row->jan_price, $row->feb_price, $row->mar_price, $row->apr_price, $row->may_price, $row->jun_price, $row->jul_price, $row->aug_price, $row->sep_price, $row->oct_price, $row->nov_price, $row->dec_price);
		}


		$rsl_dressed_prev_year1 = $this->admin->get_data('comparative_price_tbl', array('comp_price_product' => 'DRESSED', 'comp_price_segment' => 'RSL', 'comp_price_year' => $year - 1,	'bc_id'	=>	$bc_id), 
			FALSE, 'AVG(comp_price_jan) as jan_price, AVG(comp_price_feb) as feb_price, AVG(comp_price_mar) as mar_price, AVG(comp_price_apr) as apr_price, AVG(comp_price_may) as may_price, AVG(comp_price_jun) as jun_price, AVG(comp_price_jul) as jul_price, AVG(comp_price_aug) as aug_price, AVG(comp_price_sep) as sep_price, AVG(comp_price_oct) as oct_price, AVG(comp_price_nov) as nov_price, AVG(comp_price_dec) as dec_price');
		
		$data['rsl_dressed_prev_year1_jan'] = 0;
		$data['rsl_dressed_prev_year1_feb'] = 0;
		$data['rsl_dressed_prev_year1_mar'] = 0;
		$data['rsl_dressed_prev_year1_apr'] = 0;
		$data['rsl_dressed_prev_year1_may'] = 0;
		$data['rsl_dressed_prev_year1_jun'] = 0;
		$data['rsl_dressed_prev_year1_jul'] = 0;
		$data['rsl_dressed_prev_year1_aug'] = 0;
		$data['rsl_dressed_prev_year1_sep'] = 0;
		$data['rsl_dressed_prev_year1_oct'] = 0;
		$data['rsl_dressed_prev_year1_nov'] = 0;
		$data['rsl_dressed_prev_year1_dec'] = 0;
		$data['rsl_dressed_prev_year1_avg'] = 0;
		$data['rsl_dressed_prev_year1_min'] = 0;
		$data['rsl_dressed_prev_year1_max'] = 0;

		foreach($rsl_dressed_prev_year1 as $row){
			$data['rsl_dressed_prev_year1_jan'] = $row->jan_price;
			$data['rsl_dressed_prev_year1_feb'] = $row->feb_price;
			$data['rsl_dressed_prev_year1_mar'] = $row->mar_price;
			$data['rsl_dressed_prev_year1_apr'] = $row->apr_price;
			$data['rsl_dressed_prev_year1_may'] = $row->may_price;
			$data['rsl_dressed_prev_year1_jun'] = $row->jun_price;
			$data['rsl_dressed_prev_year1_jul'] = $row->jul_price;
			$data['rsl_dressed_prev_year1_aug'] = $row->aug_price;
			$data['rsl_dressed_prev_year1_sep'] = $row->sep_price;
			$data['rsl_dressed_prev_year1_oct'] = $row->oct_price;
			$data['rsl_dressed_prev_year1_nov'] = $row->nov_price;
			$data['rsl_dressed_prev_year1_dec'] = $row->dec_price;
			$data['rsl_dressed_prev_year1_avg'] = ($row->jan_price + $row->feb_price + $row->mar_price + $row->apr_price + $row->may_price + $row->jun_price + $row->jul_price + $row->aug_price + $row->sep_price + $row->oct_price + $row->nov_price + $row->dec_price) / 12;

			$data['rsl_dressed_prev_year1_min'] = min($row->jan_price, $row->feb_price, $row->mar_price, $row->apr_price, $row->may_price, $row->jun_price, $row->jul_price, $row->aug_price, $row->sep_price, $row->oct_price, $row->nov_price, $row->dec_price);

			$data['rsl_dressed_prev_year1_max'] = max($row->jan_price, $row->feb_price, $row->mar_price, $row->apr_price, $row->may_price, $row->jun_price, $row->jul_price, $row->aug_price, $row->sep_price, $row->oct_price, $row->nov_price, $row->dec_price);
		}


		/*Reseller Chooksies*/
		$join_rsl_chooksies = array(
			'sales_item_tbl b' => 'a.sales_id = b.sales_id AND a.sales_status = 1 AND b.sales_item_status = 1 AND a.sales_year = ' . $year,
			'sales_details_tbl c' => 'b.sales_item_id = c.sales_item_id AND c.sales_det_status = 1',
			'material_tbl d' => 'b.material_id = d.material_id AND (d.material_code = 10200095 OR d.material_code = 10200096 OR d.material_code = 10200097)',
			'material_group_tbl e' => 'd.material_group_id = e.material_group_id AND e.material_group_name = \'MARINATED CUT UPS\'',
			'outlet_tbl f' => 'a.outlet_id = f.outlet_id AND f.bc_id =' . $bc_id,
			'outlet_brand_tbl g' => 'f.outlet_id = g.outlet_id AND g.outlet_brand_status = 1',
			'brand_tbl h' => 'g.brand_id  = h.brand_id AND h.brand_name = \'RESELLER\''
		);

		$get_rsl_chooksies = $this->admin->get_join('sales_tbl a', $join_rsl_chooksies, FALSE, FALSE, 'YEAR(c.sales_det_date), MONTH(c.sales_det_date)', 'AVG(c.sales_det_asp) as asp, MONTH(c.sales_det_date) asp_date');

		$data['rsl_chooksies1'] = 0;
		$data['rsl_chooksies2'] = 0;
		$data['rsl_chooksies3'] = 0;
		$data['rsl_chooksies4'] = 0;
		$data['rsl_chooksies5'] = 0;
		$data['rsl_chooksies6'] = 0;
		$data['rsl_chooksies7'] = 0;
		$data['rsl_chooksies8'] = 0;
		$data['rsl_chooksies9'] = 0;
		$data['rsl_chooksies10'] = 0;
		$data['rsl_chooksies11'] = 0;
		$data['rsl_chooksies12'] = 0;
		$data['rsl_chooksies_total'] = 0;
		$data['rsl_chooksies_count'] = 0;
		$data['rsl_chooksies_avg'] = 0;
		$data['rsl_chooksies_min'] = 0;
		$data['rsl_chooksies_max'] = 0;

		foreach($get_rsl_chooksies as $row){
			$asp = $row->asp;
			$month = $row->asp_date;
			$data['rsl_chooksies' . $month] = $asp;
			$data['rsl_chooksies_total'] += $asp;

			if($asp < $data['rsl_chooksies_min'] || $data['rsl_chooksies_count'] == 0){
				$data['rsl_chooksies_min'] = $asp;
			}

			if($asp > $data['rsl_chooksies_max'] || $data['rsl_chooksies_count'] == 0){
				$data['rsl_chooksies_max'] = $asp;
			}

			$data['rsl_chooksies_count']++;
		}

		$data['rsl_chooksies_avg'] = $data['rsl_chooksies_total'] != 0 ? $data['rsl_chooksies_total'] / $data['rsl_chooksies_count'] : 0;


		$rsl_chooksies_prev_year2 = $this->admin->get_data('comparative_price_tbl', array('comp_price_product' => 'CHOOKSIES MARINADO', 'comp_price_segment' => 'RSL', 'comp_price_year' => $year - 2,	'bc_id'	=>	$bc_id), 
			FALSE, 'AVG(comp_price_jan) as jan_price, AVG(comp_price_feb) as feb_price, AVG(comp_price_mar) as mar_price, AVG(comp_price_apr) as apr_price, AVG(comp_price_may) as may_price, AVG(comp_price_jun) as jun_price, AVG(comp_price_jul) as jul_price, AVG(comp_price_aug) as aug_price, AVG(comp_price_sep) as sep_price, AVG(comp_price_oct) as oct_price, AVG(comp_price_nov) as nov_price, AVG(comp_price_dec) as dec_price');
		
		$data['rsl_chooksies_prev_year2_jan'] = 0;
		$data['rsl_chooksies_prev_year2_feb'] = 0;
		$data['rsl_chooksies_prev_year2_mar'] = 0;
		$data['rsl_chooksies_prev_year2_apr'] = 0;
		$data['rsl_chooksies_prev_year2_may'] = 0;
		$data['rsl_chooksies_prev_year2_jun'] = 0;
		$data['rsl_chooksies_prev_year2_jul'] = 0;
		$data['rsl_chooksies_prev_year2_aug'] = 0;
		$data['rsl_chooksies_prev_year2_sep'] = 0;
		$data['rsl_chooksies_prev_year2_oct'] = 0;
		$data['rsl_chooksies_prev_year2_nov'] = 0;
		$data['rsl_chooksies_prev_year2_dec'] = 0;
		$data['rsl_chooksies_prev_year2_avg'] = 0;
		$data['rsl_chooksies_prev_year2_min'] = 0;
		$data['rsl_chooksies_prev_year2_max'] = 0;

		foreach($rsl_chooksies_prev_year2 as $row){
			$data['rsl_chooksies_prev_year2_jan'] = $row->jan_price;
			$data['rsl_chooksies_prev_year2_feb'] = $row->feb_price;
			$data['rsl_chooksies_prev_year2_mar'] = $row->mar_price;
			$data['rsl_chooksies_prev_year2_apr'] = $row->apr_price;
			$data['rsl_chooksies_prev_year2_may'] = $row->may_price;
			$data['rsl_chooksies_prev_year2_jun'] = $row->jun_price;
			$data['rsl_chooksies_prev_year2_jul'] = $row->jul_price;
			$data['rsl_chooksies_prev_year2_aug'] = $row->aug_price;
			$data['rsl_chooksies_prev_year2_sep'] = $row->sep_price;
			$data['rsl_chooksies_prev_year2_oct'] = $row->oct_price;
			$data['rsl_chooksies_prev_year2_nov'] = $row->nov_price;
			$data['rsl_chooksies_prev_year2_dec'] = $row->dec_price;
			$data['rsl_chooksies_prev_year2_avg'] = ($row->jan_price + $row->feb_price + $row->mar_price + $row->apr_price + $row->may_price + $row->jun_price + $row->jul_price + $row->aug_price + $row->sep_price + $row->oct_price + $row->nov_price + $row->dec_price) / 12;

			$data['rsl_chooksies_prev_year2_min'] = min($row->jan_price, $row->feb_price, $row->mar_price, $row->apr_price, $row->may_price, $row->jun_price, $row->jul_price, $row->aug_price, $row->sep_price, $row->oct_price, $row->nov_price, $row->dec_price);

			$data['rsl_chooksies_prev_year2_max'] = max($row->jan_price, $row->feb_price, $row->mar_price, $row->apr_price, $row->may_price, $row->jun_price, $row->jul_price, $row->aug_price, $row->sep_price, $row->oct_price, $row->nov_price, $row->dec_price);
		}


		$rsl_chooksies_prev_year1 = $this->admin->get_data('comparative_price_tbl', array('comp_price_product' => 'CHOOKSIES MARINADO', 'comp_price_segment' => 'RSL', 'comp_price_year' => $year - 1,	'bc_id'	=>	$bc_id), 
			FALSE, 'AVG(comp_price_jan) as jan_price, AVG(comp_price_feb) as feb_price, AVG(comp_price_mar) as mar_price, AVG(comp_price_apr) as apr_price, AVG(comp_price_may) as may_price, AVG(comp_price_jun) as jun_price, AVG(comp_price_jul) as jul_price, AVG(comp_price_aug) as aug_price, AVG(comp_price_sep) as sep_price, AVG(comp_price_oct) as oct_price, AVG(comp_price_nov) as nov_price, AVG(comp_price_dec) as dec_price');
		
		$data['rsl_chooksies_prev_year1_jan'] = 0;
		$data['rsl_chooksies_prev_year1_feb'] = 0;
		$data['rsl_chooksies_prev_year1_mar'] = 0;
		$data['rsl_chooksies_prev_year1_apr'] = 0;
		$data['rsl_chooksies_prev_year1_may'] = 0;
		$data['rsl_chooksies_prev_year1_jun'] = 0;
		$data['rsl_chooksies_prev_year1_jul'] = 0;
		$data['rsl_chooksies_prev_year1_aug'] = 0;
		$data['rsl_chooksies_prev_year1_sep'] = 0;
		$data['rsl_chooksies_prev_year1_oct'] = 0;
		$data['rsl_chooksies_prev_year1_nov'] = 0;
		$data['rsl_chooksies_prev_year1_dec'] = 0;
		$data['rsl_chooksies_prev_year1_avg'] = 0;
		$data['rsl_chooksies_prev_year1_min'] = 0;
		$data['rsl_chooksies_prev_year1_max'] = 0;

		foreach($rsl_chooksies_prev_year1 as $row){
			$data['rsl_chooksies_prev_year1_jan'] = $row->jan_price;
			$data['rsl_chooksies_prev_year1_feb'] = $row->feb_price;
			$data['rsl_chooksies_prev_year1_mar'] = $row->mar_price;
			$data['rsl_chooksies_prev_year1_apr'] = $row->apr_price;
			$data['rsl_chooksies_prev_year1_may'] = $row->may_price;
			$data['rsl_chooksies_prev_year1_jun'] = $row->jun_price;
			$data['rsl_chooksies_prev_year1_jul'] = $row->jul_price;
			$data['rsl_chooksies_prev_year1_aug'] = $row->aug_price;
			$data['rsl_chooksies_prev_year1_sep'] = $row->sep_price;
			$data['rsl_chooksies_prev_year1_oct'] = $row->oct_price;
			$data['rsl_chooksies_prev_year1_nov'] = $row->nov_price;
			$data['rsl_chooksies_prev_year1_dec'] = $row->dec_price;
			$data['rsl_chooksies_prev_year1_avg'] = ($row->jan_price + $row->feb_price + $row->mar_price + $row->apr_price + $row->may_price + $row->jun_price + $row->jul_price + $row->aug_price + $row->sep_price + $row->oct_price + $row->nov_price + $row->dec_price) / 12;

			$data['rsl_chooksies_prev_year1_min'] = min($row->jan_price, $row->feb_price, $row->mar_price, $row->apr_price, $row->may_price, $row->jun_price, $row->jul_price, $row->aug_price, $row->sep_price, $row->oct_price, $row->nov_price, $row->dec_price);

			$data['rsl_chooksies_prev_year1_max'] = max($row->jan_price, $row->feb_price, $row->mar_price, $row->apr_price, $row->may_price, $row->jun_price, $row->jul_price, $row->aug_price, $row->sep_price, $row->oct_price, $row->nov_price, $row->dec_price);
		}


		$join_rsl_marinado = array(
			'sales_item_tbl b' => 'a.sales_id = b.sales_id AND a.sales_status = 1 AND b.sales_item_status = 1 AND a.sales_year = ' . $year,
			'sales_details_tbl c' => 'b.sales_item_id = c.sales_item_id AND c.sales_det_status = 1',
			'material_tbl d' => 'b.material_id = d.material_id AND d.material_code = 1000638',
			'material_group_tbl e' => 'd.material_group_id = e.material_group_id AND e.material_group_name = \'MARINATED CUT UPS\'',
			'outlet_tbl f' => 'a.outlet_id = f.outlet_id AND f.bc_id =' . $bc_id,
			'outlet_brand_tbl g' => 'f.outlet_id = g.outlet_id AND g.outlet_brand_status = 1',
			'brand_tbl h' => 'g.brand_id  = h.brand_id AND h.brand_name = \'RESELLER\''
		);

		$get_rsl_marinado = $this->admin->get_join('sales_tbl a', $join_rsl_marinado, FALSE, FALSE, 'YEAR(c.sales_det_date), MONTH(c.sales_det_date)', 'AVG(c.sales_det_asp) as asp, MONTH(c.sales_det_date) asp_date');

		$data['rsl_marinado1'] = 0;
		$data['rsl_marinado2'] = 0;
		$data['rsl_marinado3'] = 0;
		$data['rsl_marinado4'] = 0;
		$data['rsl_marinado5'] = 0;
		$data['rsl_marinado6'] = 0;
		$data['rsl_marinado7'] = 0;
		$data['rsl_marinado8'] = 0;
		$data['rsl_marinado9'] = 0;
		$data['rsl_marinado10'] = 0;
		$data['rsl_marinado11'] = 0;
		$data['rsl_marinado12'] = 0;
		$data['rsl_marinado_total'] = 0;
		$data['rsl_marinado_count'] = 0;
		$data['rsl_marinado_avg'] = 0;
		$data['rsl_marinado_min'] = 0;
		$data['rsl_marinado_max'] = 0;

		foreach($get_rsl_marinado as $row){
			$asp = $row->asp;
			$month = $row->asp_date;
			$data['rsl_marinado' . $month] = $asp;
			$data['rsl_marinado_total'] += $asp;

			if($asp < $data['rsl_marinado_min'] || $data['rsl_marinado_count'] == 0){
				$data['rsl_marinado_min'] = $asp;
			}

			if($asp > $data['rsl_marinado_max'] || $data['rsl_marinado_count'] == 0){
				$data['rsl_marinado_max'] = $asp;
			}

			$data['rsl_marinado_count']++;
		}

		$data['rsl_marinado_avg'] = $data['rsl_marinado_total'] != 0 ? $data['rsl_marinado_total'] / $data['rsl_marinado_count'] : 0;

		$rsl_marinado_prev_year2 = $this->admin->get_data('comparative_price_tbl', array('comp_price_product' => 'MARINADO FRIED', 'comp_price_segment' => 'RSL', 'comp_price_year' => $year - 2,	'bc_id'	=>	$bc_id), 
			FALSE, 'AVG(comp_price_jan) as jan_price, AVG(comp_price_feb) as feb_price, AVG(comp_price_mar) as mar_price, AVG(comp_price_apr) as apr_price, AVG(comp_price_may) as may_price, AVG(comp_price_jun) as jun_price, AVG(comp_price_jul) as jul_price, AVG(comp_price_aug) as aug_price, AVG(comp_price_sep) as sep_price, AVG(comp_price_oct) as oct_price, AVG(comp_price_nov) as nov_price, AVG(comp_price_dec) as dec_price');
		
		$data['rsl_marinado_prev_year2_jan'] = 0;
		$data['rsl_marinado_prev_year2_feb'] = 0;
		$data['rsl_marinado_prev_year2_mar'] = 0;
		$data['rsl_marinado_prev_year2_apr'] = 0;
		$data['rsl_marinado_prev_year2_may'] = 0;
		$data['rsl_marinado_prev_year2_jun'] = 0;
		$data['rsl_marinado_prev_year2_jul'] = 0;
		$data['rsl_marinado_prev_year2_aug'] = 0;
		$data['rsl_marinado_prev_year2_sep'] = 0;
		$data['rsl_marinado_prev_year2_oct'] = 0;
		$data['rsl_marinado_prev_year2_nov'] = 0;
		$data['rsl_marinado_prev_year2_dec'] = 0;
		$data['rsl_marinado_prev_year2_avg'] = 0;
		$data['rsl_marinado_prev_year2_min'] = 0;
		$data['rsl_marinado_prev_year2_max'] = 0;

		foreach($rsl_marinado_prev_year2 as $row){
			$data['rsl_marinado_prev_year2_jan'] = $row->jan_price;
			$data['rsl_marinado_prev_year2_feb'] = $row->feb_price;
			$data['rsl_marinado_prev_year2_mar'] = $row->mar_price;
			$data['rsl_marinado_prev_year2_apr'] = $row->apr_price;
			$data['rsl_marinado_prev_year2_may'] = $row->may_price;
			$data['rsl_marinado_prev_year2_jun'] = $row->jun_price;
			$data['rsl_marinado_prev_year2_jul'] = $row->jul_price;
			$data['rsl_marinado_prev_year2_aug'] = $row->aug_price;
			$data['rsl_marinado_prev_year2_sep'] = $row->sep_price;
			$data['rsl_marinado_prev_year2_oct'] = $row->oct_price;
			$data['rsl_marinado_prev_year2_nov'] = $row->nov_price;
			$data['rsl_marinado_prev_year2_dec'] = $row->dec_price;
			$data['rsl_marinado_prev_year2_avg'] = ($row->jan_price + $row->feb_price + $row->mar_price + $row->apr_price + $row->may_price + $row->jun_price + $row->jul_price + $row->aug_price + $row->sep_price + $row->oct_price + $row->nov_price + $row->dec_price) / 12;

			$data['rsl_marinado_prev_year2_min'] = min($row->jan_price, $row->feb_price, $row->mar_price, $row->apr_price, $row->may_price, $row->jun_price, $row->jul_price, $row->aug_price, $row->sep_price, $row->oct_price, $row->nov_price, $row->dec_price);

			$data['rsl_marinado_prev_year2_max'] = max($row->jan_price, $row->feb_price, $row->mar_price, $row->apr_price, $row->may_price, $row->jun_price, $row->jul_price, $row->aug_price, $row->sep_price, $row->oct_price, $row->nov_price, $row->dec_price);
		}


		$rsl_marinado_prev_year1 = $this->admin->get_data('comparative_price_tbl', array('comp_price_product' => 'MARINADO FRIED', 'comp_price_segment' => 'RSL', 'comp_price_year' => $year - 1,	'bc_id'	=>	$bc_id), 
			FALSE, 'AVG(comp_price_jan) as jan_price, AVG(comp_price_feb) as feb_price, AVG(comp_price_mar) as mar_price, AVG(comp_price_apr) as apr_price, AVG(comp_price_may) as may_price, AVG(comp_price_jun) as jun_price, AVG(comp_price_jul) as jul_price, AVG(comp_price_aug) as aug_price, AVG(comp_price_sep) as sep_price, AVG(comp_price_oct) as oct_price, AVG(comp_price_nov) as nov_price, AVG(comp_price_dec) as dec_price');
		
		$data['rsl_marinado_prev_year1_jan'] = 0;
		$data['rsl_marinado_prev_year1_feb'] = 0;
		$data['rsl_marinado_prev_year1_mar'] = 0;
		$data['rsl_marinado_prev_year1_apr'] = 0;
		$data['rsl_marinado_prev_year1_may'] = 0;
		$data['rsl_marinado_prev_year1_jun'] = 0;
		$data['rsl_marinado_prev_year1_jul'] = 0;
		$data['rsl_marinado_prev_year1_aug'] = 0;
		$data['rsl_marinado_prev_year1_sep'] = 0;
		$data['rsl_marinado_prev_year1_oct'] = 0;
		$data['rsl_marinado_prev_year1_nov'] = 0;
		$data['rsl_marinado_prev_year1_dec'] = 0;
		$data['rsl_marinado_prev_year1_avg'] = 0;
		$data['rsl_marinado_prev_year1_min'] = 0;
		$data['rsl_marinado_prev_year1_max'] = 0;

		foreach($rsl_marinado_prev_year1 as $row){
			$data['rsl_marinado_prev_year1_jan'] = $row->jan_price;
			$data['rsl_marinado_prev_year1_feb'] = $row->feb_price;
			$data['rsl_marinado_prev_year1_mar'] = $row->mar_price;
			$data['rsl_marinado_prev_year1_apr'] = $row->apr_price;
			$data['rsl_marinado_prev_year1_may'] = $row->may_price;
			$data['rsl_marinado_prev_year1_jun'] = $row->jun_price;
			$data['rsl_marinado_prev_year1_jul'] = $row->jul_price;
			$data['rsl_marinado_prev_year1_aug'] = $row->aug_price;
			$data['rsl_marinado_prev_year1_sep'] = $row->sep_price;
			$data['rsl_marinado_prev_year1_oct'] = $row->oct_price;
			$data['rsl_marinado_prev_year1_nov'] = $row->nov_price;
			$data['rsl_marinado_prev_year1_dec'] = $row->dec_price;
			$data['rsl_marinado_prev_year1_avg'] = ($row->jan_price + $row->feb_price + $row->mar_price + $row->apr_price + $row->may_price + $row->jun_price + $row->jul_price + $row->aug_price + $row->sep_price + $row->oct_price + $row->nov_price + $row->dec_price) / 12;

			$data['rsl_marinado_prev_year1_min'] = min($row->jan_price, $row->feb_price, $row->mar_price, $row->apr_price, $row->may_price, $row->jun_price, $row->jul_price, $row->aug_price, $row->sep_price, $row->oct_price, $row->nov_price, $row->dec_price);

			$data['rsl_marinado_prev_year1_max'] = max($row->jan_price, $row->feb_price, $row->mar_price, $row->apr_price, $row->may_price, $row->jun_price, $row->jul_price, $row->aug_price, $row->sep_price, $row->oct_price, $row->nov_price, $row->dec_price);
		}


		$join_rsl_spicy = array(
			'sales_item_tbl b' => 'a.sales_id = b.sales_id AND a.sales_status = 1 AND b.sales_item_status = 1 AND a.sales_year = ' . $year,
			'sales_details_tbl c' => 'b.sales_item_id = c.sales_item_id AND c.sales_det_status = 1',
			'material_tbl d' => 'b.material_id = d.material_id AND (d.material_code = 10200090 OR d.material_code = 1000401 OR d.material_code = 1000402 OR d.material_code = 1000617)',
			'material_group_tbl e' => 'd.material_group_id = e.material_group_id AND e.material_group_name = \'MARINATED CUT UPS\'',
			'outlet_tbl f' => 'a.outlet_id = f.outlet_id AND f.bc_id =' . $bc_id,
			'outlet_brand_tbl g' => 'f.outlet_id = g.outlet_id AND g.outlet_brand_status = 1',
			'brand_tbl h' => 'g.brand_id  = h.brand_id AND h.brand_name = \'RESELLER\''
		);

		$get_rsl_spicy = $this->admin->get_join('sales_tbl a', $join_rsl_spicy, FALSE, FALSE, 'YEAR(c.sales_det_date), MONTH(c.sales_det_date)', 'AVG(c.sales_det_asp) as asp, MONTH(c.sales_det_date) asp_date');

		$data['rsl_spicy1'] = 0;
		$data['rsl_spicy2'] = 0;
		$data['rsl_spicy3'] = 0;
		$data['rsl_spicy4'] = 0;
		$data['rsl_spicy5'] = 0;
		$data['rsl_spicy6'] = 0;
		$data['rsl_spicy7'] = 0;
		$data['rsl_spicy8'] = 0;
		$data['rsl_spicy9'] = 0;
		$data['rsl_spicy10'] = 0;
		$data['rsl_spicy11'] = 0;
		$data['rsl_spicy12'] = 0;
		$data['rsl_spicy_total'] = 0;
		$data['rsl_spicy_count'] = 0;
		$data['rsl_spicy_avg'] = 0;
		$data['rsl_spicy_min'] = 0;
		$data['rsl_spicy_max'] = 0;

		foreach($get_rsl_spicy as $row){
			$asp = $row->asp;
			$month = $row->asp_date;
			$data['rsl_spicy' . $month] = $asp;
			$data['rsl_spicy_total'] += $asp;

			if($asp < $data['rsl_spicy_min'] || $data['rsl_spicy_count'] == 0){
				$data['rsl_spicy_min'] = $asp;
			}

			if($asp > $data['rsl_spicy_max'] || $data['rsl_spicy_count'] == 0){
				$data['rsl_spicy_max'] = $asp;
			}

			$data['rsl_spicy_count']++;
		}

		$data['rsl_spicy_avg'] = $data['rsl_spicy_total'] != 0 ? $data['rsl_spicy_total'] / $data['rsl_spicy_count'] : 0;

		$rsl_spicy_prev_year2 = $this->admin->get_data('comparative_price_tbl', array('comp_price_product' => 'SPICY NECK', 'comp_price_segment' => 'RSL', 'comp_price_year' => $year - 2,	'bc_id'	=>	$bc_id), 
			FALSE, 'AVG(comp_price_jan) as jan_price, AVG(comp_price_feb) as feb_price, AVG(comp_price_mar) as mar_price, AVG(comp_price_apr) as apr_price, AVG(comp_price_may) as may_price, AVG(comp_price_jun) as jun_price, AVG(comp_price_jul) as jul_price, AVG(comp_price_aug) as aug_price, AVG(comp_price_sep) as sep_price, AVG(comp_price_oct) as oct_price, AVG(comp_price_nov) as nov_price, AVG(comp_price_dec) as dec_price');
		
		$data['rsl_spicy_prev_year2_jan'] = 0;
		$data['rsl_spicy_prev_year2_feb'] = 0;
		$data['rsl_spicy_prev_year2_mar'] = 0;
		$data['rsl_spicy_prev_year2_apr'] = 0;
		$data['rsl_spicy_prev_year2_may'] = 0;
		$data['rsl_spicy_prev_year2_jun'] = 0;
		$data['rsl_spicy_prev_year2_jul'] = 0;
		$data['rsl_spicy_prev_year2_aug'] = 0;
		$data['rsl_spicy_prev_year2_sep'] = 0;
		$data['rsl_spicy_prev_year2_oct'] = 0;
		$data['rsl_spicy_prev_year2_nov'] = 0;
		$data['rsl_spicy_prev_year2_dec'] = 0;
		$data['rsl_spicy_prev_year2_avg'] = 0;
		$data['rsl_spicy_prev_year2_min'] = 0;
		$data['rsl_spicy_prev_year2_max'] = 0;

		foreach($rsl_spicy_prev_year2 as $row){
			$data['rsl_spicy_prev_year2_jan'] = $row->jan_price;
			$data['rsl_spicy_prev_year2_feb'] = $row->feb_price;
			$data['rsl_spicy_prev_year2_mar'] = $row->mar_price;
			$data['rsl_spicy_prev_year2_apr'] = $row->apr_price;
			$data['rsl_spicy_prev_year2_may'] = $row->may_price;
			$data['rsl_spicy_prev_year2_jun'] = $row->jun_price;
			$data['rsl_spicy_prev_year2_jul'] = $row->jul_price;
			$data['rsl_spicy_prev_year2_aug'] = $row->aug_price;
			$data['rsl_spicy_prev_year2_sep'] = $row->sep_price;
			$data['rsl_spicy_prev_year2_oct'] = $row->oct_price;
			$data['rsl_spicy_prev_year2_nov'] = $row->nov_price;
			$data['rsl_spicy_prev_year2_dec'] = $row->dec_price;
			$data['rsl_spicy_prev_year2_avg'] = ($row->jan_price + $row->feb_price + $row->mar_price + $row->apr_price + $row->may_price + $row->jun_price + $row->jul_price + $row->aug_price + $row->sep_price + $row->oct_price + $row->nov_price + $row->dec_price) / 12;

			$data['rsl_spicy_prev_year2_min'] = min($row->jan_price, $row->feb_price, $row->mar_price, $row->apr_price, $row->may_price, $row->jun_price, $row->jul_price, $row->aug_price, $row->sep_price, $row->oct_price, $row->nov_price, $row->dec_price);

			$data['rsl_spicy_prev_year2_max'] = max($row->jan_price, $row->feb_price, $row->mar_price, $row->apr_price, $row->may_price, $row->jun_price, $row->jul_price, $row->aug_price, $row->sep_price, $row->oct_price, $row->nov_price, $row->dec_price);
		}


		$rsl_spicy_prev_year1 = $this->admin->get_data('comparative_price_tbl', array('comp_price_product' => 'SPICY NECK', 'comp_price_segment' => 'RSL', 'comp_price_year' => $year - 1,	'bc_id'	=>	$bc_id), 
			FALSE, 'AVG(comp_price_jan) as jan_price, AVG(comp_price_feb) as feb_price, AVG(comp_price_mar) as mar_price, AVG(comp_price_apr) as apr_price, AVG(comp_price_may) as may_price, AVG(comp_price_jun) as jun_price, AVG(comp_price_jul) as jul_price, AVG(comp_price_aug) as aug_price, AVG(comp_price_sep) as sep_price, AVG(comp_price_oct) as oct_price, AVG(comp_price_nov) as nov_price, AVG(comp_price_dec) as dec_price');
		
		$data['rsl_spicy_prev_year1_jan'] = 0;
		$data['rsl_spicy_prev_year1_feb'] = 0;
		$data['rsl_spicy_prev_year1_mar'] = 0;
		$data['rsl_spicy_prev_year1_apr'] = 0;
		$data['rsl_spicy_prev_year1_may'] = 0;
		$data['rsl_spicy_prev_year1_jun'] = 0;
		$data['rsl_spicy_prev_year1_jul'] = 0;
		$data['rsl_spicy_prev_year1_aug'] = 0;
		$data['rsl_spicy_prev_year1_sep'] = 0;
		$data['rsl_spicy_prev_year1_oct'] = 0;
		$data['rsl_spicy_prev_year1_nov'] = 0;
		$data['rsl_spicy_prev_year1_dec'] = 0;
		$data['rsl_spicy_prev_year1_avg'] = 0;
		$data['rsl_spicy_prev_year1_min'] = 0;
		$data['rsl_spicy_prev_year1_max'] = 0;

		foreach($rsl_spicy_prev_year1 as $row){
			$data['rsl_spicy_prev_year1_jan'] = $row->jan_price;
			$data['rsl_spicy_prev_year1_feb'] = $row->feb_price;
			$data['rsl_spicy_prev_year1_mar'] = $row->mar_price;
			$data['rsl_spicy_prev_year1_apr'] = $row->apr_price;
			$data['rsl_spicy_prev_year1_may'] = $row->may_price;
			$data['rsl_spicy_prev_year1_jun'] = $row->jun_price;
			$data['rsl_spicy_prev_year1_jul'] = $row->jul_price;
			$data['rsl_spicy_prev_year1_aug'] = $row->aug_price;
			$data['rsl_spicy_prev_year1_sep'] = $row->sep_price;
			$data['rsl_spicy_prev_year1_oct'] = $row->oct_price;
			$data['rsl_spicy_prev_year1_nov'] = $row->nov_price;
			$data['rsl_spicy_prev_year1_dec'] = $row->dec_price;
			$data['rsl_spicy_prev_year1_avg'] = ($row->jan_price + $row->feb_price + $row->mar_price + $row->apr_price + $row->may_price + $row->jun_price + $row->jul_price + $row->aug_price + $row->sep_price + $row->oct_price + $row->nov_price + $row->dec_price) / 12;

			$data['rsl_spicy_prev_year1_min'] = min($row->jan_price, $row->feb_price, $row->mar_price, $row->apr_price, $row->may_price, $row->jun_price, $row->jul_price, $row->aug_price, $row->sep_price, $row->oct_price, $row->nov_price, $row->dec_price);

			$data['rsl_spicy_prev_year1_max'] = max($row->jan_price, $row->feb_price, $row->mar_price, $row->apr_price, $row->may_price, $row->jun_price, $row->jul_price, $row->aug_price, $row->sep_price, $row->oct_price, $row->nov_price, $row->dec_price);
		}

		/*Barbeque*/
		$join_rsl_barbeque = array(
			'sales_item_tbl b' => 'a.sales_id = b.sales_id AND a.sales_status = 1 AND b.sales_item_status = 1 AND a.sales_year = ' . $year,
			'sales_details_tbl c' => 'b.sales_item_id = c.sales_item_id AND c.sales_det_status = 1',
			'material_tbl d' => 'b.material_id = d.material_id AND d.material_code = 1000548',
			'material_group_tbl e' => 'd.material_group_id = e.material_group_id AND e.material_group_name = \'OTHER SPECIALTY PRODUCTS\'',
			'outlet_tbl f' => 'a.outlet_id = f.outlet_id AND f.bc_id = ' . $bc_id,
			'outlet_brand_tbl g' => 'f.outlet_id = g.outlet_id AND g.outlet_brand_status = 1',
			'brand_tbl h' => 'g.brand_id  = h.brand_id AND h.brand_name = \'RESELLER\''
		);

		$get_rsl_barbeque = $this->admin->get_join('sales_tbl a', $join_rsl_barbeque, FALSE, FALSE, 'YEAR(c.sales_det_date), MONTH(c.sales_det_date)', 'AVG(c.sales_det_asp) as asp, MONTH(c.sales_det_date) asp_date');

		$data['rsl_barbeque1'] = 0;
		$data['rsl_barbeque2'] = 0;
		$data['rsl_barbeque3'] = 0;
		$data['rsl_barbeque4'] = 0;
		$data['rsl_barbeque5'] = 0;
		$data['rsl_barbeque6'] = 0;
		$data['rsl_barbeque7'] = 0;
		$data['rsl_barbeque8'] = 0;
		$data['rsl_barbeque9'] = 0;
		$data['rsl_barbeque10'] = 0;
		$data['rsl_barbeque11'] = 0;
		$data['rsl_barbeque12'] = 0;
		$data['rsl_barbeque_total'] = 0;
		$data['rsl_barbeque_count'] = 0;
		$data['rsl_barbeque_avg'] = 0;
		$data['rsl_barbeque_min'] = 0;
		$data['rsl_barbeque_max'] = 0;

		foreach($get_rsl_barbeque as $row){
			$asp = $row->asp;
			$month = $row->asp_date;
			$data['rsl_barbeque' . $month] = $asp;
			$data['rsl_barbeque_total'] += $asp;

			if($asp < $data['rsl_barbeque_min'] || $data['rsl_barbeque_count'] == 0){
				$data['rsl_barbeque_min'] = $asp;
			}

			if($asp > $data['rsl_barbeque_max'] || $data['rsl_barbeque_count'] == 0){
				$data['rsl_barbeque_max'] = $asp;
			}

			$data['rsl_barbeque_count']++;
		}

		$data['rsl_barbeque_avg'] = $data['rsl_barbeque_total'] != 0 ? $data['rsl_barbeque_total'] / $data['rsl_barbeque_count'] : 0;

		$rsl_barbecue_prev_year2 = $this->admin->get_data('comparative_price_tbl', array('comp_price_product' => 'BBQ', 'comp_price_segment' => 'RSL', 'comp_price_year' => $year - 2,	'bc_id'	=>	$bc_id), 
			FALSE, 'AVG(comp_price_jan) as jan_price, AVG(comp_price_feb) as feb_price, AVG(comp_price_mar) as mar_price, AVG(comp_price_apr) as apr_price, AVG(comp_price_may) as may_price, AVG(comp_price_jun) as jun_price, AVG(comp_price_jul) as jul_price, AVG(comp_price_aug) as aug_price, AVG(comp_price_sep) as sep_price, AVG(comp_price_oct) as oct_price, AVG(comp_price_nov) as nov_price, AVG(comp_price_dec) as dec_price');
		
		$data['rsl_barbecue_prev_year2_jan'] = 0;
		$data['rsl_barbecue_prev_year2_feb'] = 0;
		$data['rsl_barbecue_prev_year2_mar'] = 0;
		$data['rsl_barbecue_prev_year2_apr'] = 0;
		$data['rsl_barbecue_prev_year2_may'] = 0;
		$data['rsl_barbecue_prev_year2_jun'] = 0;
		$data['rsl_barbecue_prev_year2_jul'] = 0;
		$data['rsl_barbecue_prev_year2_aug'] = 0;
		$data['rsl_barbecue_prev_year2_sep'] = 0;
		$data['rsl_barbecue_prev_year2_oct'] = 0;
		$data['rsl_barbecue_prev_year2_nov'] = 0;
		$data['rsl_barbecue_prev_year2_dec'] = 0;
		$data['rsl_barbecue_prev_year2_avg'] = 0;
		$data['rsl_barbecue_prev_year2_min'] = 0;
		$data['rsl_barbecue_prev_year2_max'] = 0;

		foreach($rsl_barbecue_prev_year2 as $row){
			$data['rsl_barbecue_prev_year2_jan'] = $row->jan_price;
			$data['rsl_barbecue_prev_year2_feb'] = $row->feb_price;
			$data['rsl_barbecue_prev_year2_mar'] = $row->mar_price;
			$data['rsl_barbecue_prev_year2_apr'] = $row->apr_price;
			$data['rsl_barbecue_prev_year2_may'] = $row->may_price;
			$data['rsl_barbecue_prev_year2_jun'] = $row->jun_price;
			$data['rsl_barbecue_prev_year2_jul'] = $row->jul_price;
			$data['rsl_barbecue_prev_year2_aug'] = $row->aug_price;
			$data['rsl_barbecue_prev_year2_sep'] = $row->sep_price;
			$data['rsl_barbecue_prev_year2_oct'] = $row->oct_price;
			$data['rsl_barbecue_prev_year2_nov'] = $row->nov_price;
			$data['rsl_barbecue_prev_year2_dec'] = $row->dec_price;
			$data['rsl_barbecue_prev_year2_avg'] = ($row->jan_price + $row->feb_price + $row->mar_price + $row->apr_price + $row->may_price + $row->jun_price + $row->jul_price + $row->aug_price + $row->sep_price + $row->oct_price + $row->nov_price + $row->dec_price) / 12;

			$data['rsl_barbecue_prev_year2_min'] = min($row->jan_price, $row->feb_price, $row->mar_price, $row->apr_price, $row->may_price, $row->jun_price, $row->jul_price, $row->aug_price, $row->sep_price, $row->oct_price, $row->nov_price, $row->dec_price);

			$data['rsl_barbecue_prev_year2_max'] = max($row->jan_price, $row->feb_price, $row->mar_price, $row->apr_price, $row->may_price, $row->jun_price, $row->jul_price, $row->aug_price, $row->sep_price, $row->oct_price, $row->nov_price, $row->dec_price);
		}


		$rsl_barbecue_prev_year1 = $this->admin->get_data('comparative_price_tbl', array('comp_price_product' => 'BBQ', 'comp_price_segment' => 'RSL', 'comp_price_year' => $year - 1,	'bc_id'	=>	$bc_id), 
			FALSE, 'AVG(comp_price_jan) as jan_price, AVG(comp_price_feb) as feb_price, AVG(comp_price_mar) as mar_price, AVG(comp_price_apr) as apr_price, AVG(comp_price_may) as may_price, AVG(comp_price_jun) as jun_price, AVG(comp_price_jul) as jul_price, AVG(comp_price_aug) as aug_price, AVG(comp_price_sep) as sep_price, AVG(comp_price_oct) as oct_price, AVG(comp_price_nov) as nov_price, AVG(comp_price_dec) as dec_price');
		
		$data['rsl_barbecue_prev_year1_jan'] = 0;
		$data['rsl_barbecue_prev_year1_feb'] = 0;
		$data['rsl_barbecue_prev_year1_mar'] = 0;
		$data['rsl_barbecue_prev_year1_apr'] = 0;
		$data['rsl_barbecue_prev_year1_may'] = 0;
		$data['rsl_barbecue_prev_year1_jun'] = 0;
		$data['rsl_barbecue_prev_year1_jul'] = 0;
		$data['rsl_barbecue_prev_year1_aug'] = 0;
		$data['rsl_barbecue_prev_year1_sep'] = 0;
		$data['rsl_barbecue_prev_year1_oct'] = 0;
		$data['rsl_barbecue_prev_year1_nov'] = 0;
		$data['rsl_barbecue_prev_year1_dec'] = 0;
		$data['rsl_barbecue_prev_year1_avg'] = 0;
		$data['rsl_barbecue_prev_year1_min'] = 0;
		$data['rsl_barbecue_prev_year1_max'] = 0;

		foreach($rsl_barbecue_prev_year1 as $row){
			$data['rsl_barbecue_prev_year1_jan'] = $row->jan_price;
			$data['rsl_barbecue_prev_year1_feb'] = $row->feb_price;
			$data['rsl_barbecue_prev_year1_mar'] = $row->mar_price;
			$data['rsl_barbecue_prev_year1_apr'] = $row->apr_price;
			$data['rsl_barbecue_prev_year1_may'] = $row->may_price;
			$data['rsl_barbecue_prev_year1_jun'] = $row->jun_price;
			$data['rsl_barbecue_prev_year1_jul'] = $row->jul_price;
			$data['rsl_barbecue_prev_year1_aug'] = $row->aug_price;
			$data['rsl_barbecue_prev_year1_sep'] = $row->sep_price;
			$data['rsl_barbecue_prev_year1_oct'] = $row->oct_price;
			$data['rsl_barbecue_prev_year1_nov'] = $row->nov_price;
			$data['rsl_barbecue_prev_year1_dec'] = $row->dec_price;
			$data['rsl_barbecue_prev_year1_avg'] = ($row->jan_price + $row->feb_price + $row->mar_price + $row->apr_price + $row->may_price + $row->jun_price + $row->jul_price + $row->aug_price + $row->sep_price + $row->oct_price + $row->nov_price + $row->dec_price) / 12;

			$data['rsl_barbecue_prev_year1_min'] = min($row->jan_price, $row->feb_price, $row->mar_price, $row->apr_price, $row->may_price, $row->jun_price, $row->jul_price, $row->aug_price, $row->sep_price, $row->oct_price, $row->nov_price, $row->dec_price);

			$data['rsl_barbecue_prev_year1_max'] = max($row->jan_price, $row->feb_price, $row->mar_price, $row->apr_price, $row->may_price, $row->jun_price, $row->jul_price, $row->aug_price, $row->sep_price, $row->oct_price, $row->nov_price, $row->dec_price);
		}

		/*Reseller Nuggets*/

		$join_rsl_nuggets = array(
			'sales_item_tbl b' => 'a.sales_id = b.sales_id AND a.sales_status = 1 AND b.sales_item_status = 1 AND a.sales_year = ' . $year,
			'sales_details_tbl c' => 'b.sales_item_id = c.sales_item_id AND c.sales_det_status = 1',
			'material_tbl d' => 'b.material_id = d.material_id AND (d.material_code = 40000016 OR d.material_code = 40000019)',
			'material_group_tbl e' => 'd.material_group_id = e.material_group_id AND e.material_group_name = \'OTHER VAP PRODUCTS\'',
			'outlet_tbl f' => 'a.outlet_id = f.outlet_id AND f.bc_id = ' . $bc_id,
			'outlet_brand_tbl g' => 'f.outlet_id = g.outlet_id AND g.outlet_brand_status = 1',
			'brand_tbl h' => 'g.brand_id  = h.brand_id AND h.brand_name = \'RESELLER\''
		);

		$get_rsl_nuggets = $this->admin->get_join('sales_tbl a', $join_rsl_nuggets, FALSE, FALSE, 'YEAR(c.sales_det_date), MONTH(c.sales_det_date)', 'AVG(c.sales_det_asp) as asp, MONTH(c.sales_det_date) asp_date');

		$data['rsl_nuggets1'] = 0;
		$data['rsl_nuggets2'] = 0;
		$data['rsl_nuggets3'] = 0;
		$data['rsl_nuggets4'] = 0;
		$data['rsl_nuggets5'] = 0;
		$data['rsl_nuggets6'] = 0;
		$data['rsl_nuggets7'] = 0;
		$data['rsl_nuggets8'] = 0;
		$data['rsl_nuggets9'] = 0;
		$data['rsl_nuggets10'] = 0;
		$data['rsl_nuggets11'] = 0;
		$data['rsl_nuggets12'] = 0;
		$data['rsl_nuggets_total'] = 0;
		$data['rsl_nuggets_count'] = 0;
		$data['rsl_nuggets_avg'] = 0;
		$data['rsl_nuggets_min'] = 0;
		$data['rsl_nuggets_max'] = 0;

		foreach($get_rsl_nuggets as $row){
			$asp = $row->asp;
			$month = $row->asp_date;
			$data['rsl_nuggets' . $month] = $asp;
			$data['rsl_nuggets_total'] += $asp;

			if($asp < $data['rsl_nuggets_min'] || $data['rsl_nuggets_count'] == 0){
				$data['rsl_nuggets_min'] = $asp;
			}

			if($asp > $data['rsl_nuggets_max'] || $data['rsl_nuggets_count'] == 0){
				$data['rsl_nuggets_max'] = $asp;
			}

			$data['rsl_nuggets_count']++;
		}

		$data['rsl_nuggets_avg'] = $data['rsl_nuggets_total'] != 0 ? $data['rsl_nuggets_total'] / $data['rsl_nuggets_count'] : 0;


		$rsl_nuggets_prev_year2 = $this->admin->get_data('comparative_price_tbl', array('comp_price_product' => 'VAP-Nuggets', 'comp_price_segment' => 'RSL', 'comp_price_year' => $year - 2,	'bc_id'	=>	$bc_id), 
			FALSE, 'AVG(comp_price_jan) as jan_price, AVG(comp_price_feb) as feb_price, AVG(comp_price_mar) as mar_price, AVG(comp_price_apr) as apr_price, AVG(comp_price_may) as may_price, AVG(comp_price_jun) as jun_price, AVG(comp_price_jul) as jul_price, AVG(comp_price_aug) as aug_price, AVG(comp_price_sep) as sep_price, AVG(comp_price_oct) as oct_price, AVG(comp_price_nov) as nov_price, AVG(comp_price_dec) as dec_price');
		
		$data['rsl_nuggets_prev_year2_jan'] = 0;
		$data['rsl_nuggets_prev_year2_feb'] = 0;
		$data['rsl_nuggets_prev_year2_mar'] = 0;
		$data['rsl_nuggets_prev_year2_apr'] = 0;
		$data['rsl_nuggets_prev_year2_may'] = 0;
		$data['rsl_nuggets_prev_year2_jun'] = 0;
		$data['rsl_nuggets_prev_year2_jul'] = 0;
		$data['rsl_nuggets_prev_year2_aug'] = 0;
		$data['rsl_nuggets_prev_year2_sep'] = 0;
		$data['rsl_nuggets_prev_year2_oct'] = 0;
		$data['rsl_nuggets_prev_year2_nov'] = 0;
		$data['rsl_nuggets_prev_year2_dec'] = 0;
		$data['rsl_nuggets_prev_year2_avg'] = 0;
		$data['rsl_nuggets_prev_year2_min'] = 0;
		$data['rsl_nuggets_prev_year2_max'] = 0;

		foreach($rsl_nuggets_prev_year2 as $row){
			$data['rsl_nuggets_prev_year2_jan'] = $row->jan_price;
			$data['rsl_nuggets_prev_year2_feb'] = $row->feb_price;
			$data['rsl_nuggets_prev_year2_mar'] = $row->mar_price;
			$data['rsl_nuggets_prev_year2_apr'] = $row->apr_price;
			$data['rsl_nuggets_prev_year2_may'] = $row->may_price;
			$data['rsl_nuggets_prev_year2_jun'] = $row->jun_price;
			$data['rsl_nuggets_prev_year2_jul'] = $row->jul_price;
			$data['rsl_nuggets_prev_year2_aug'] = $row->aug_price;
			$data['rsl_nuggets_prev_year2_sep'] = $row->sep_price;
			$data['rsl_nuggets_prev_year2_oct'] = $row->oct_price;
			$data['rsl_nuggets_prev_year2_nov'] = $row->nov_price;
			$data['rsl_nuggets_prev_year2_dec'] = $row->dec_price;
			$data['rsl_nuggets_prev_year2_avg'] = ($row->jan_price + $row->feb_price + $row->mar_price + $row->apr_price + $row->may_price + $row->jun_price + $row->jul_price + $row->aug_price + $row->sep_price + $row->oct_price + $row->nov_price + $row->dec_price) / 12;

			$data['rsl_nuggets_prev_year2_min'] = min($row->jan_price, $row->feb_price, $row->mar_price, $row->apr_price, $row->may_price, $row->jun_price, $row->jul_price, $row->aug_price, $row->sep_price, $row->oct_price, $row->nov_price, $row->dec_price);

			$data['rsl_nuggets_prev_year2_max'] = max($row->jan_price, $row->feb_price, $row->mar_price, $row->apr_price, $row->may_price, $row->jun_price, $row->jul_price, $row->aug_price, $row->sep_price, $row->oct_price, $row->nov_price, $row->dec_price);
		}


		$rsl_nuggets_prev_year1 = $this->admin->get_data('comparative_price_tbl', array('comp_price_product' => 'VAP-NuggetsQ', 'comp_price_segment' => 'RSL', 'comp_price_year' => $year - 1,	'bc_id'	=>	$bc_id), 
			FALSE, 'AVG(comp_price_jan) as jan_price, AVG(comp_price_feb) as feb_price, AVG(comp_price_mar) as mar_price, AVG(comp_price_apr) as apr_price, AVG(comp_price_may) as may_price, AVG(comp_price_jun) as jun_price, AVG(comp_price_jul) as jul_price, AVG(comp_price_aug) as aug_price, AVG(comp_price_sep) as sep_price, AVG(comp_price_oct) as oct_price, AVG(comp_price_nov) as nov_price, AVG(comp_price_dec) as dec_price');
		
		$data['rsl_nuggets_prev_year1_jan'] = 0;
		$data['rsl_nuggets_prev_year1_feb'] = 0;
		$data['rsl_nuggets_prev_year1_mar'] = 0;
		$data['rsl_nuggets_prev_year1_apr'] = 0;
		$data['rsl_nuggets_prev_year1_may'] = 0;
		$data['rsl_nuggets_prev_year1_jun'] = 0;
		$data['rsl_nuggets_prev_year1_jul'] = 0;
		$data['rsl_nuggets_prev_year1_aug'] = 0;
		$data['rsl_nuggets_prev_year1_sep'] = 0;
		$data['rsl_nuggets_prev_year1_oct'] = 0;
		$data['rsl_nuggets_prev_year1_nov'] = 0;
		$data['rsl_nuggets_prev_year1_dec'] = 0;
		$data['rsl_nuggets_prev_year1_avg'] = 0;
		$data['rsl_nuggets_prev_year1_min'] = 0;
		$data['rsl_nuggets_prev_year1_max'] = 0;

		foreach($rsl_nuggets_prev_year1 as $row){
			$data['rsl_nuggets_prev_year1_jan'] = $row->jan_price;
			$data['rsl_nuggets_prev_year1_feb'] = $row->feb_price;
			$data['rsl_nuggets_prev_year1_mar'] = $row->mar_price;
			$data['rsl_nuggets_prev_year1_apr'] = $row->apr_price;
			$data['rsl_nuggets_prev_year1_may'] = $row->may_price;
			$data['rsl_nuggets_prev_year1_jun'] = $row->jun_price;
			$data['rsl_nuggets_prev_year1_jul'] = $row->jul_price;
			$data['rsl_nuggets_prev_year1_aug'] = $row->aug_price;
			$data['rsl_nuggets_prev_year1_sep'] = $row->sep_price;
			$data['rsl_nuggets_prev_year1_oct'] = $row->oct_price;
			$data['rsl_nuggets_prev_year1_nov'] = $row->nov_price;
			$data['rsl_nuggets_prev_year1_dec'] = $row->dec_price;
			$data['rsl_nuggets_prev_year1_avg'] = ($row->jan_price + $row->feb_price + $row->mar_price + $row->apr_price + $row->may_price + $row->jun_price + $row->jul_price + $row->aug_price + $row->sep_price + $row->oct_price + $row->nov_price + $row->dec_price) / 12;

			$data['rsl_nuggets_prev_year1_min'] = min($row->jan_price, $row->feb_price, $row->mar_price, $row->apr_price, $row->may_price, $row->jun_price, $row->jul_price, $row->aug_price, $row->sep_price, $row->oct_price, $row->nov_price, $row->dec_price);

			$data['rsl_nuggets_prev_year1_max'] = max($row->jan_price, $row->feb_price, $row->mar_price, $row->apr_price, $row->may_price, $row->jun_price, $row->jul_price, $row->aug_price, $row->sep_price, $row->oct_price, $row->nov_price, $row->dec_price);
		}

		/*UR 11 PC PICA PICA CUTS*/

		$join_rsl_pica = array(
			'sales_item_tbl b' => 'a.sales_id = b.sales_id AND a.sales_status = 1 AND b.sales_item_status = 1 AND a.sales_year = ' . $year,
			'sales_details_tbl c' => 'b.sales_item_id = c.sales_item_id AND c.sales_det_status = 1',
			'material_tbl d' => 'b.material_id = d.material_id AND d.material_code = 10400170',
			'material_group_tbl e' => 'd.material_group_id = e.material_group_id',
			'outlet_tbl f' => 'a.outlet_id = f.outlet_id AND f.bc_id = ' . $bc_id,
			'outlet_brand_tbl g' => 'f.outlet_id = g.outlet_id AND g.outlet_brand_status = 1',
			'brand_tbl h' => 'g.brand_id  = h.brand_id AND h.brand_name = \'RESELLER\''
		);

		$get_rsl_pica = $this->admin->get_join('sales_tbl a', $join_rsl_pica, FALSE, FALSE, 'YEAR(c.sales_det_date), MONTH(c.sales_det_date)', 'AVG(c.sales_det_asp) as asp, MONTH(c.sales_det_date) asp_date');

		$data['rsl_pica1'] = 0;
		$data['rsl_pica2'] = 0;
		$data['rsl_pica3'] = 0;
		$data['rsl_pica4'] = 0;
		$data['rsl_pica5'] = 0;
		$data['rsl_pica6'] = 0;
		$data['rsl_pica7'] = 0;
		$data['rsl_pica8'] = 0;
		$data['rsl_pica9'] = 0;
		$data['rsl_pica10'] = 0;
		$data['rsl_pica11'] = 0;
		$data['rsl_pica12'] = 0;
		$data['rsl_pica_total'] = 0;
		$data['rsl_pica_count'] = 0;
		$data['rsl_pica_avg'] = 0;
		$data['rsl_pica_min'] = 0;
		$data['rsl_pica_max'] = 0;

		foreach($get_rsl_pica as $row){
			$asp = $row->asp;
			$month = $row->asp_date;
			$data['rsl_pica' . $month] = $asp;
			$data['rsl_pica_total'] += $asp;

			if($asp < $data['rsl_pica_min'] || $data['rsl_pica_count'] == 0){
				$data['rsl_pica_min'] = $asp;
			}

			if($asp > $data['rsl_pica_max'] || $data['rsl_pica_count'] == 0){
				$data['rsl_pica_max'] = $asp;
			}

			$data['rsl_pica_count']++;
		}

		$data['rsl_pica_avg'] = $data['rsl_pica_total'] != 0 ? $data['rsl_pica_total'] / $data['rsl_pica_count'] : 0;


		$rsl_pica_prev_year2 = $this->admin->get_data('comparative_price_tbl', array('comp_price_product' => '11 PC', 'comp_price_segment' => 'RSL', 'comp_price_year' => $year - 2,	'bc_id'	=>	$bc_id), 
			FALSE, 'AVG(comp_price_jan) as jan_price, AVG(comp_price_feb) as feb_price, AVG(comp_price_mar) as mar_price, AVG(comp_price_apr) as apr_price, AVG(comp_price_may) as may_price, AVG(comp_price_jun) as jun_price, AVG(comp_price_jul) as jul_price, AVG(comp_price_aug) as aug_price, AVG(comp_price_sep) as sep_price, AVG(comp_price_oct) as oct_price, AVG(comp_price_nov) as nov_price, AVG(comp_price_dec) as dec_price');
		
		$data['rsl_pica_prev_year2_jan'] = 0;
		$data['rsl_pica_prev_year2_feb'] = 0;
		$data['rsl_pica_prev_year2_mar'] = 0;
		$data['rsl_pica_prev_year2_apr'] = 0;
		$data['rsl_pica_prev_year2_may'] = 0;
		$data['rsl_pica_prev_year2_jun'] = 0;
		$data['rsl_pica_prev_year2_jul'] = 0;
		$data['rsl_pica_prev_year2_aug'] = 0;
		$data['rsl_pica_prev_year2_sep'] = 0;
		$data['rsl_pica_prev_year2_oct'] = 0;
		$data['rsl_pica_prev_year2_nov'] = 0;
		$data['rsl_pica_prev_year2_dec'] = 0;
		$data['rsl_pica_prev_year2_avg'] = 0;
		$data['rsl_pica_prev_year2_min'] = 0;
		$data['rsl_pica_prev_year2_max'] = 0;

		foreach($rsl_pica_prev_year2 as $row){
			$data['rsl_pica_prev_year2_jan'] = $row->jan_price;
			$data['rsl_pica_prev_year2_feb'] = $row->feb_price;
			$data['rsl_pica_prev_year2_mar'] = $row->mar_price;
			$data['rsl_pica_prev_year2_apr'] = $row->apr_price;
			$data['rsl_pica_prev_year2_may'] = $row->may_price;
			$data['rsl_pica_prev_year2_jun'] = $row->jun_price;
			$data['rsl_pica_prev_year2_jul'] = $row->jul_price;
			$data['rsl_pica_prev_year2_aug'] = $row->aug_price;
			$data['rsl_pica_prev_year2_sep'] = $row->sep_price;
			$data['rsl_pica_prev_year2_oct'] = $row->oct_price;
			$data['rsl_pica_prev_year2_nov'] = $row->nov_price;
			$data['rsl_pica_prev_year2_dec'] = $row->dec_price;
			$data['rsl_pica_prev_year2_avg'] = ($row->jan_price + $row->feb_price + $row->mar_price + $row->apr_price + $row->may_price + $row->jun_price + $row->jul_price + $row->aug_price + $row->sep_price + $row->oct_price + $row->nov_price + $row->dec_price) / 12;

			$data['rsl_pica_prev_year2_min'] = min($row->jan_price, $row->feb_price, $row->mar_price, $row->apr_price, $row->may_price, $row->jun_price, $row->jul_price, $row->aug_price, $row->sep_price, $row->oct_price, $row->nov_price, $row->dec_price);

			$data['rsl_pica_prev_year2_max'] = max($row->jan_price, $row->feb_price, $row->mar_price, $row->apr_price, $row->may_price, $row->jun_price, $row->jul_price, $row->aug_price, $row->sep_price, $row->oct_price, $row->nov_price, $row->dec_price);
		}


		$rsl_pica_prev_year1 = $this->admin->get_data('comparative_price_tbl', array('comp_price_product' => '11 PC', 'comp_price_segment' => 'RSL', 'comp_price_year' => $year - 1,	'bc_id'	=>	$bc_id), 
			FALSE, 'AVG(comp_price_jan) as jan_price, AVG(comp_price_feb) as feb_price, AVG(comp_price_mar) as mar_price, AVG(comp_price_apr) as apr_price, AVG(comp_price_may) as may_price, AVG(comp_price_jun) as jun_price, AVG(comp_price_jul) as jul_price, AVG(comp_price_aug) as aug_price, AVG(comp_price_sep) as sep_price, AVG(comp_price_oct) as oct_price, AVG(comp_price_nov) as nov_price, AVG(comp_price_dec) as dec_price');
		
		$data['rsl_pica_prev_year1_jan'] = 0;
		$data['rsl_pica_prev_year1_feb'] = 0;
		$data['rsl_pica_prev_year1_mar'] = 0;
		$data['rsl_pica_prev_year1_apr'] = 0;
		$data['rsl_pica_prev_year1_may'] = 0;
		$data['rsl_pica_prev_year1_jun'] = 0;
		$data['rsl_pica_prev_year1_jul'] = 0;
		$data['rsl_pica_prev_year1_aug'] = 0;
		$data['rsl_pica_prev_year1_sep'] = 0;
		$data['rsl_pica_prev_year1_oct'] = 0;
		$data['rsl_pica_prev_year1_nov'] = 0;
		$data['rsl_pica_prev_year1_dec'] = 0;
		$data['rsl_pica_prev_year1_avg'] = 0;
		$data['rsl_pica_prev_year1_min'] = 0;
		$data['rsl_pica_prev_year1_max'] = 0;

		foreach($rsl_pica_prev_year1 as $row){
			$data['rsl_pica_prev_year1_jan'] = $row->jan_price;
			$data['rsl_pica_prev_year1_feb'] = $row->feb_price;
			$data['rsl_pica_prev_year1_mar'] = $row->mar_price;
			$data['rsl_pica_prev_year1_apr'] = $row->apr_price;
			$data['rsl_pica_prev_year1_may'] = $row->may_price;
			$data['rsl_pica_prev_year1_jun'] = $row->jun_price;
			$data['rsl_pica_prev_year1_jul'] = $row->jul_price;
			$data['rsl_pica_prev_year1_aug'] = $row->aug_price;
			$data['rsl_pica_prev_year1_sep'] = $row->sep_price;
			$data['rsl_pica_prev_year1_oct'] = $row->oct_price;
			$data['rsl_pica_prev_year1_nov'] = $row->nov_price;
			$data['rsl_pica_prev_year1_dec'] = $row->dec_price;
			$data['rsl_pica_prev_year1_avg'] = ($row->jan_price + $row->feb_price + $row->mar_price + $row->apr_price + $row->may_price + $row->jun_price + $row->jul_price + $row->aug_price + $row->sep_price + $row->oct_price + $row->nov_price + $row->dec_price) / 12;

			$data['rsl_pica_prev_year1_min'] = min($row->jan_price, $row->feb_price, $row->mar_price, $row->apr_price, $row->may_price, $row->jun_price, $row->jul_price, $row->aug_price, $row->sep_price, $row->oct_price, $row->nov_price, $row->dec_price);

			$data['rsl_pica_prev_year1_max'] = max($row->jan_price, $row->feb_price, $row->mar_price, $row->apr_price, $row->may_price, $row->jun_price, $row->jul_price, $row->aug_price, $row->sep_price, $row->oct_price, $row->nov_price, $row->dec_price);
		}

		/*RSL 1 PC BOSSING CUTS */
		
		$join_rsl_bossing = array(
			'sales_item_tbl b' => 'a.sales_id = b.sales_id AND a.sales_status = 1 AND b.sales_item_status = 1 AND a.sales_year = ' . $year,
			'sales_details_tbl c' => 'b.sales_item_id = c.sales_item_id AND c.sales_det_status = 1',
			'material_tbl d' => 'b.material_id = d.material_id AND d.material_code = 10400184',
			'material_group_tbl e' => 'd.material_group_id = e.material_group_id',
			'outlet_tbl f' => 'a.outlet_id = f.outlet_id AND f.bc_id = ' . $bc_id,
			'outlet_brand_tbl g' => 'f.outlet_id = g.outlet_id AND g.outlet_brand_status = 1',
			'brand_tbl h' => 'g.brand_id  = h.brand_id AND h.brand_name = \'RESELLER\''
		);

		$get_rsl_bossing = $this->admin->get_join('sales_tbl a', $join_rsl_bossing, FALSE, FALSE, 'YEAR(c.sales_det_date), MONTH(c.sales_det_date)', 'AVG(c.sales_det_asp) as asp, MONTH(c.sales_det_date) asp_date');

		$data['rsl_bossing1'] = 0;
		$data['rsl_bossing2'] = 0;
		$data['rsl_bossing3'] = 0;
		$data['rsl_bossing4'] = 0;
		$data['rsl_bossing5'] = 0;
		$data['rsl_bossing6'] = 0;
		$data['rsl_bossing7'] = 0;
		$data['rsl_bossing8'] = 0;
		$data['rsl_bossing9'] = 0;
		$data['rsl_bossing10'] = 0;
		$data['rsl_bossing11'] = 0;
		$data['rsl_bossing12'] = 0;
		$data['rsl_bossing_total'] = 0;
		$data['rsl_bossing_count'] = 0;
		$data['rsl_bossing_avg'] = 0;
		$data['rsl_bossing_min'] = 0;
		$data['rsl_bossing_max'] = 0;

		foreach($get_rsl_bossing as $row){
			$asp = $row->asp;
			$month = $row->asp_date;
			$data['rsl_bossing' . $month] = $asp;
			$data['rsl_bossing_total'] += $asp;

			if($asp < $data['rsl_bossing_min'] || $data['rsl_bossing_count'] == 0){
				$data['rsl_bossing_min'] = $asp;
			}

			if($asp > $data['rsl_bossing_max'] || $data['rsl_bossing_count'] == 0){
				$data['rsl_bossing_max'] = $asp;
			}

			$data['rsl_bossing_count']++;
		}

		$data['rsl_bossing_avg'] = $data['rsl_bossing_total'] != 0 ? $data['rsl_bossing_total'] / $data['rsl_bossing_count'] : 0;


		$rsl_bossing_prev_year2 = $this->admin->get_data('comparative_price_tbl', array('comp_price_product' => '1 PC', 'comp_price_segment' => 'RSL', 'comp_price_year' => $year - 2,	'bc_id'	=>	$bc_id), 
			FALSE, 'AVG(comp_price_jan) as jan_price, AVG(comp_price_feb) as feb_price, AVG(comp_price_mar) as mar_price, AVG(comp_price_apr) as apr_price, AVG(comp_price_may) as may_price, AVG(comp_price_jun) as jun_price, AVG(comp_price_jul) as jul_price, AVG(comp_price_aug) as aug_price, AVG(comp_price_sep) as sep_price, AVG(comp_price_oct) as oct_price, AVG(comp_price_nov) as nov_price, AVG(comp_price_dec) as dec_price');
		
		$data['rsl_bossing_prev_year2_jan'] = 0;
		$data['rsl_bossing_prev_year2_feb'] = 0;
		$data['rsl_bossing_prev_year2_mar'] = 0;
		$data['rsl_bossing_prev_year2_apr'] = 0;
		$data['rsl_bossing_prev_year2_may'] = 0;
		$data['rsl_bossing_prev_year2_jun'] = 0;
		$data['rsl_bossing_prev_year2_jul'] = 0;
		$data['rsl_bossing_prev_year2_aug'] = 0;
		$data['rsl_bossing_prev_year2_sep'] = 0;
		$data['rsl_bossing_prev_year2_oct'] = 0;
		$data['rsl_bossing_prev_year2_nov'] = 0;
		$data['rsl_bossing_prev_year2_dec'] = 0;
		$data['rsl_bossing_prev_year2_avg'] = 0;
		$data['rsl_bossing_prev_year2_min'] = 0;
		$data['rsl_bossing_prev_year2_max'] = 0;

		foreach($rsl_bossing_prev_year2 as $row){
			$data['rsl_bossing_prev_year2_jan'] = $row->jan_price;
			$data['rsl_bossing_prev_year2_feb'] = $row->feb_price;
			$data['rsl_bossing_prev_year2_mar'] = $row->mar_price;
			$data['rsl_bossing_prev_year2_apr'] = $row->apr_price;
			$data['rsl_bossing_prev_year2_may'] = $row->may_price;
			$data['rsl_bossing_prev_year2_jun'] = $row->jun_price;
			$data['rsl_bossing_prev_year2_jul'] = $row->jul_price;
			$data['rsl_bossing_prev_year2_aug'] = $row->aug_price;
			$data['rsl_bossing_prev_year2_sep'] = $row->sep_price;
			$data['rsl_bossing_prev_year2_oct'] = $row->oct_price;
			$data['rsl_bossing_prev_year2_nov'] = $row->nov_price;
			$data['rsl_bossing_prev_year2_dec'] = $row->dec_price;
			$data['rsl_bossing_prev_year2_avg'] = ($row->jan_price + $row->feb_price + $row->mar_price + $row->apr_price + $row->may_price + $row->jun_price + $row->jul_price + $row->aug_price + $row->sep_price + $row->oct_price + $row->nov_price + $row->dec_price) / 12;

			$data['rsl_bossing_prev_year2_min'] = min($row->jan_price, $row->feb_price, $row->mar_price, $row->apr_price, $row->may_price, $row->jun_price, $row->jul_price, $row->aug_price, $row->sep_price, $row->oct_price, $row->nov_price, $row->dec_price);

			$data['rsl_bossing_prev_year2_max'] = max($row->jan_price, $row->feb_price, $row->mar_price, $row->apr_price, $row->may_price, $row->jun_price, $row->jul_price, $row->aug_price, $row->sep_price, $row->oct_price, $row->nov_price, $row->dec_price);
		}


		$rsl_bossing_prev_year1 = $this->admin->get_data('comparative_price_tbl', array('comp_price_product' => '11 PC', 'comp_price_segment' => 'RSL', 'comp_price_year' => $year - 1,	'bc_id'	=>	$bc_id), 
			FALSE, 'AVG(comp_price_jan) as jan_price, AVG(comp_price_feb) as feb_price, AVG(comp_price_mar) as mar_price, AVG(comp_price_apr) as apr_price, AVG(comp_price_may) as may_price, AVG(comp_price_jun) as jun_price, AVG(comp_price_jul) as jul_price, AVG(comp_price_aug) as aug_price, AVG(comp_price_sep) as sep_price, AVG(comp_price_oct) as oct_price, AVG(comp_price_nov) as nov_price, AVG(comp_price_dec) as dec_price');
		
		$data['rsl_bossing_prev_year1_jan'] = 0;
		$data['rsl_bossing_prev_year1_feb'] = 0;
		$data['rsl_bossing_prev_year1_mar'] = 0;
		$data['rsl_bossing_prev_year1_apr'] = 0;
		$data['rsl_bossing_prev_year1_may'] = 0;
		$data['rsl_bossing_prev_year1_jun'] = 0;
		$data['rsl_bossing_prev_year1_jul'] = 0;
		$data['rsl_bossing_prev_year1_aug'] = 0;
		$data['rsl_bossing_prev_year1_sep'] = 0;
		$data['rsl_bossing_prev_year1_oct'] = 0;
		$data['rsl_bossing_prev_year1_nov'] = 0;
		$data['rsl_bossing_prev_year1_dec'] = 0;
		$data['rsl_bossing_prev_year1_avg'] = 0;
		$data['rsl_bossing_prev_year1_min'] = 0;
		$data['rsl_bossing_prev_year1_max'] = 0;

		foreach($rsl_bossing_prev_year1 as $row){
			$data['rsl_bossing_prev_year1_jan'] = $row->jan_price;
			$data['rsl_bossing_prev_year1_feb'] = $row->feb_price;
			$data['rsl_bossing_prev_year1_mar'] = $row->mar_price;
			$data['rsl_bossing_prev_year1_apr'] = $row->apr_price;
			$data['rsl_bossing_prev_year1_may'] = $row->may_price;
			$data['rsl_bossing_prev_year1_jun'] = $row->jun_price;
			$data['rsl_bossing_prev_year1_jul'] = $row->jul_price;
			$data['rsl_bossing_prev_year1_aug'] = $row->aug_price;
			$data['rsl_bossing_prev_year1_sep'] = $row->sep_price;
			$data['rsl_bossing_prev_year1_oct'] = $row->oct_price;
			$data['rsl_bossing_prev_year1_nov'] = $row->nov_price;
			$data['rsl_bossing_prev_year1_dec'] = $row->dec_price;
			$data['rsl_bossing_prev_year1_avg'] = ($row->jan_price + $row->feb_price + $row->mar_price + $row->apr_price + $row->may_price + $row->jun_price + $row->jul_price + $row->aug_price + $row->sep_price + $row->oct_price + $row->nov_price + $row->dec_price) / 12;

			$data['rsl_bossing_prev_year1_min'] = min($row->jan_price, $row->feb_price, $row->mar_price, $row->apr_price, $row->may_price, $row->jun_price, $row->jul_price, $row->aug_price, $row->sep_price, $row->oct_price, $row->nov_price, $row->dec_price);

			$data['rsl_bossing_prev_year1_max'] = max($row->jan_price, $row->feb_price, $row->mar_price, $row->apr_price, $row->may_price, $row->jun_price, $row->jul_price, $row->aug_price, $row->sep_price, $row->oct_price, $row->nov_price, $row->dec_price);
		}


		/*Reseller Marinated Raw*/
		$join_rsl_raw = array(
			'sales_item_tbl b' => 'a.sales_id = b.sales_id AND a.sales_status = 1 AND b.sales_item_status = 1 AND a.sales_year = ' . $year,
			'sales_details_tbl c' => 'b.sales_item_id = c.sales_item_id AND c.sales_det_status = 1',
			'material_tbl d' => 'b.material_id = d.material_id',
			'material_group_tbl e' => 'd.material_group_id = e.material_group_id AND e.material_group_name = \'MARINATED CHICKEN (RAW)\'',
			'outlet_tbl f' => 'a.outlet_id = f.outlet_id AND f.bc_id = ' . $bc_id,
			'outlet_brand_tbl g' => 'f.outlet_id = g.outlet_id AND g.outlet_brand_status = 1',
			'brand_tbl h' => 'g.brand_id  = h.brand_id AND h.brand_name = \'RESELLER\''
		);

		$get_rsl_raw = $this->admin->get_join('sales_tbl a', $join_rsl_raw, FALSE, FALSE, 'YEAR(c.sales_det_date), MONTH(c.sales_det_date)', 'AVG(c.sales_det_asp) as asp, MONTH(c.sales_det_date) asp_date');

		$data['rsl_raw1'] = 0;
		$data['rsl_raw2'] = 0;
		$data['rsl_raw3'] = 0;
		$data['rsl_raw4'] = 0;
		$data['rsl_raw5'] = 0;
		$data['rsl_raw6'] = 0;
		$data['rsl_raw7'] = 0;
		$data['rsl_raw8'] = 0;
		$data['rsl_raw9'] = 0;
		$data['rsl_raw10'] = 0;
		$data['rsl_raw11'] = 0;
		$data['rsl_raw12'] = 0;
		$data['rsl_raw_total'] = 0;
		$data['rsl_raw_count'] = 0;
		$data['rsl_raw_avg'] = 0;
		$data['rsl_raw_min'] = 0;
		$data['rsl_raw_max'] = 0;

		foreach($get_rsl_raw as $row){
			$asp = $row->asp;
			$month = $row->asp_date;
			$data['rsl_raw' . $month] = $asp;
			$data['rsl_raw_total'] += $asp;

			if($asp < $data['rsl_raw_min'] || $data['rsl_raw_count'] == 0){
				$data['rsl_raw_min'] = $asp;
			}

			if($asp > $data['rsl_raw_max'] || $data['rsl_raw_count'] == 0){
				$data['rsl_raw_max'] = $asp;
			}

			$data['rsl_raw_count']++;
		}

		$data['rsl_raw_avg'] = $data['rsl_raw_total'] != 0 ? $data['rsl_raw_total'] / $data['rsl_raw_count'] : 0;


		$rsl_raw_prev_year2 = $this->admin->get_data('comparative_price_tbl', array('comp_price_product' => 'MARINATED CHICKEN RAW', 'comp_price_segment' => 'RSL', 'comp_price_year' => $year - 2,	'bc_id'	=>	$bc_id), 
			FALSE, 'AVG(comp_price_jan) as jan_price, AVG(comp_price_feb) as feb_price, AVG(comp_price_mar) as mar_price, AVG(comp_price_apr) as apr_price, AVG(comp_price_may) as may_price, AVG(comp_price_jun) as jun_price, AVG(comp_price_jul) as jul_price, AVG(comp_price_aug) as aug_price, AVG(comp_price_sep) as sep_price, AVG(comp_price_oct) as oct_price, AVG(comp_price_nov) as nov_price, AVG(comp_price_dec) as dec_price');
		
		$data['rsl_raw_prev_year2_jan'] = 0;
		$data['rsl_raw_prev_year2_feb'] = 0;
		$data['rsl_raw_prev_year2_mar'] = 0;
		$data['rsl_raw_prev_year2_apr'] = 0;
		$data['rsl_raw_prev_year2_may'] = 0;
		$data['rsl_raw_prev_year2_jun'] = 0;
		$data['rsl_raw_prev_year2_jul'] = 0;
		$data['rsl_raw_prev_year2_aug'] = 0;
		$data['rsl_raw_prev_year2_sep'] = 0;
		$data['rsl_raw_prev_year2_oct'] = 0;
		$data['rsl_raw_prev_year2_nov'] = 0;
		$data['rsl_raw_prev_year2_dec'] = 0;
		$data['rsl_raw_prev_year2_avg'] = 0;
		$data['rsl_raw_prev_year2_min'] = 0;
		$data['rsl_raw_prev_year2_max'] = 0;

		foreach($rsl_raw_prev_year2 as $row){
			$data['rsl_raw_prev_year2_jan'] = $row->jan_price;
			$data['rsl_raw_prev_year2_feb'] = $row->feb_price;
			$data['rsl_raw_prev_year2_mar'] = $row->mar_price;
			$data['rsl_raw_prev_year2_apr'] = $row->apr_price;
			$data['rsl_raw_prev_year2_may'] = $row->may_price;
			$data['rsl_raw_prev_year2_jun'] = $row->jun_price;
			$data['rsl_raw_prev_year2_jul'] = $row->jul_price;
			$data['rsl_raw_prev_year2_aug'] = $row->aug_price;
			$data['rsl_raw_prev_year2_sep'] = $row->sep_price;
			$data['rsl_raw_prev_year2_oct'] = $row->oct_price;
			$data['rsl_raw_prev_year2_nov'] = $row->nov_price;
			$data['rsl_raw_prev_year2_dec'] = $row->dec_price;
			$data['rsl_raw_prev_year2_avg'] = ($row->jan_price + $row->feb_price + $row->mar_price + $row->apr_price + $row->may_price + $row->jun_price + $row->jul_price + $row->aug_price + $row->sep_price + $row->oct_price + $row->nov_price + $row->dec_price) / 12;

			$data['rsl_raw_prev_year2_min'] = min($row->jan_price, $row->feb_price, $row->mar_price, $row->apr_price, $row->may_price, $row->jun_price, $row->jul_price, $row->aug_price, $row->sep_price, $row->oct_price, $row->nov_price, $row->dec_price);

			$data['rsl_raw_prev_year2_max'] = max($row->jan_price, $row->feb_price, $row->mar_price, $row->apr_price, $row->may_price, $row->jun_price, $row->jul_price, $row->aug_price, $row->sep_price, $row->oct_price, $row->nov_price, $row->dec_price);
		}


		$rsl_raw_prev_year1 = $this->admin->get_data('comparative_price_tbl', array('comp_price_product' => 'MARINATED CHICKEN RAW', 'comp_price_segment' => 'RSL', 'comp_price_year' => $year - 1,	'bc_id'	=>	$bc_id), 
			FALSE, 'AVG(comp_price_jan) as jan_price, AVG(comp_price_feb) as feb_price, AVG(comp_price_mar) as mar_price, AVG(comp_price_apr) as apr_price, AVG(comp_price_may) as may_price, AVG(comp_price_jun) as jun_price, AVG(comp_price_jul) as jul_price, AVG(comp_price_aug) as aug_price, AVG(comp_price_sep) as sep_price, AVG(comp_price_oct) as oct_price, AVG(comp_price_nov) as nov_price, AVG(comp_price_dec) as dec_price');
		
		$data['rsl_raw_prev_year1_jan'] = 0;
		$data['rsl_raw_prev_year1_feb'] = 0;
		$data['rsl_raw_prev_year1_mar'] = 0;
		$data['rsl_raw_prev_year1_apr'] = 0;
		$data['rsl_raw_prev_year1_may'] = 0;
		$data['rsl_raw_prev_year1_jun'] = 0;
		$data['rsl_raw_prev_year1_jul'] = 0;
		$data['rsl_raw_prev_year1_aug'] = 0;
		$data['rsl_raw_prev_year1_sep'] = 0;
		$data['rsl_raw_prev_year1_oct'] = 0;
		$data['rsl_raw_prev_year1_nov'] = 0;
		$data['rsl_raw_prev_year1_dec'] = 0;
		$data['rsl_raw_prev_year1_avg'] = 0;
		$data['rsl_raw_prev_year1_min'] = 0;
		$data['rsl_raw_prev_year1_max'] = 0;

		foreach($rsl_raw_prev_year1 as $row){
			$data['rsl_raw_prev_year1_jan'] = $row->jan_price;
			$data['rsl_raw_prev_year1_feb'] = $row->feb_price;
			$data['rsl_raw_prev_year1_mar'] = $row->mar_price;
			$data['rsl_raw_prev_year1_apr'] = $row->apr_price;
			$data['rsl_raw_prev_year1_may'] = $row->may_price;
			$data['rsl_raw_prev_year1_jun'] = $row->jun_price;
			$data['rsl_raw_prev_year1_jul'] = $row->jul_price;
			$data['rsl_raw_prev_year1_aug'] = $row->aug_price;
			$data['rsl_raw_prev_year1_sep'] = $row->sep_price;
			$data['rsl_raw_prev_year1_oct'] = $row->oct_price;
			$data['rsl_raw_prev_year1_nov'] = $row->nov_price;
			$data['rsl_raw_prev_year1_dec'] = $row->dec_price;
			$data['rsl_raw_prev_year1_avg'] = ($row->jan_price + $row->feb_price + $row->mar_price + $row->apr_price + $row->may_price + $row->jun_price + $row->jul_price + $row->aug_price + $row->sep_price + $row->oct_price + $row->nov_price + $row->dec_price) / 12;

			$data['rsl_raw_prev_year1_min'] = min($row->jan_price, $row->feb_price, $row->mar_price, $row->apr_price, $row->may_price, $row->jun_price, $row->jul_price, $row->aug_price, $row->sep_price, $row->oct_price, $row->nov_price, $row->dec_price);

			$data['rsl_raw_prev_year1_max'] = max($row->jan_price, $row->feb_price, $row->mar_price, $row->apr_price, $row->may_price, $row->jun_price, $row->jul_price, $row->aug_price, $row->sep_price, $row->oct_price, $row->nov_price, $row->dec_price);
		}



		/*Reseller Chooksies Cut ups*/
		$join_rsl_cutups = array(
			'sales_item_tbl b' => 'a.sales_id = b.sales_id AND a.sales_status = 1 AND b.sales_item_status = 1 AND a.sales_year = ' . $year,
			'sales_details_tbl c' => 'b.sales_item_id = c.sales_item_id AND c.sales_det_status = 1',
			'material_tbl d' => 'b.material_id = d.material_id AND d.material_code = 10000150',
			'material_group_tbl e' => 'd.material_group_id = e.material_group_id AND e.material_group_name = \'NON MARINATED CUT UPS\'',
			'outlet_tbl f' => 'a.outlet_id = f.outlet_id AND f.bc_id = ' . $bc_id,
			'outlet_brand_tbl g' => 'f.outlet_id = g.outlet_id AND g.outlet_brand_status = 1',
			'brand_tbl h' => 'g.brand_id  = h.brand_id AND h.brand_name = \'RESELLER\''
		);

		$get_rsl_cutups = $this->admin->get_join('sales_tbl a', $join_rsl_cutups, FALSE, FALSE, 'YEAR(c.sales_det_date), MONTH(c.sales_det_date)', 'AVG(c.sales_det_asp) as asp, MONTH(c.sales_det_date) asp_date');

		$data['rsl_cutups1'] = 0;
		$data['rsl_cutups2'] = 0;
		$data['rsl_cutups3'] = 0;
		$data['rsl_cutups4'] = 0;
		$data['rsl_cutups5'] = 0;
		$data['rsl_cutups6'] = 0;
		$data['rsl_cutups7'] = 0;
		$data['rsl_cutups8'] = 0;
		$data['rsl_cutups9'] = 0;
		$data['rsl_cutups10'] = 0;
		$data['rsl_cutups11'] = 0;
		$data['rsl_cutups12'] = 0;
		$data['rsl_cutups_total'] = 0;
		$data['rsl_cutups_count'] = 0;
		$data['rsl_cutups_avg'] = 0;
		$data['rsl_cutups_min'] = 0;
		$data['rsl_cutups_max'] = 0;

		foreach($get_rsl_cutups as $row){
			$asp = $row->asp;
			$month = $row->asp_date;
			$data['rsl_cutups' . $month] = $asp;
			$data['rsl_cutups_total'] += $asp;

			if($asp < $data['rsl_cutups_min'] || $data['rsl_cutups_count'] == 0){
				$data['rsl_cutups_min'] = $asp;
			}

			if($asp > $data['rsl_cutups_max'] || $data['rsl_cutups_count'] == 0){
				$data['rsl_cutups_max'] = $asp;
			}

			$data['rsl_cutups_count']++;
		}

		$data['rsl_cutups_avg'] = $data['rsl_cutups_total'] != 0 ? $data['rsl_cutups_total'] / $data['rsl_cutups_count'] : 0;

		$rsl_cutups_prev_year2 = $this->admin->get_data('comparative_price_tbl', array('comp_price_product' => 'CHOOKSIES CUT UPS', 'comp_price_segment' => 'RSL', 'comp_price_year' => $year - 2,	'bc_id'	=>	$bc_id), 
			FALSE, 'AVG(comp_price_jan) as jan_price, AVG(comp_price_feb) as feb_price, AVG(comp_price_mar) as mar_price, AVG(comp_price_apr) as apr_price, AVG(comp_price_may) as may_price, AVG(comp_price_jun) as jun_price, AVG(comp_price_jul) as jul_price, AVG(comp_price_aug) as aug_price, AVG(comp_price_sep) as sep_price, AVG(comp_price_oct) as oct_price, AVG(comp_price_nov) as nov_price, AVG(comp_price_dec) as dec_price');
		
		$data['rsl_cutups_prev_year2_jan'] = 0;
		$data['rsl_cutups_prev_year2_feb'] = 0;
		$data['rsl_cutups_prev_year2_mar'] = 0;
		$data['rsl_cutups_prev_year2_apr'] = 0;
		$data['rsl_cutups_prev_year2_may'] = 0;
		$data['rsl_cutups_prev_year2_jun'] = 0;
		$data['rsl_cutups_prev_year2_jul'] = 0;
		$data['rsl_cutups_prev_year2_aug'] = 0;
		$data['rsl_cutups_prev_year2_sep'] = 0;
		$data['rsl_cutups_prev_year2_oct'] = 0;
		$data['rsl_cutups_prev_year2_nov'] = 0;
		$data['rsl_cutups_prev_year2_dec'] = 0;
		$data['rsl_cutups_prev_year2_avg'] = 0;
		$data['rsl_cutups_prev_year2_min'] = 0;
		$data['rsl_cutups_prev_year2_max'] = 0;

		foreach($rsl_cutups_prev_year2 as $row){
			$data['rsl_cutups_prev_year2_jan'] = $row->jan_price;
			$data['rsl_cutups_prev_year2_feb'] = $row->feb_price;
			$data['rsl_cutups_prev_year2_mar'] = $row->mar_price;
			$data['rsl_cutups_prev_year2_apr'] = $row->apr_price;
			$data['rsl_cutups_prev_year2_may'] = $row->may_price;
			$data['rsl_cutups_prev_year2_jun'] = $row->jun_price;
			$data['rsl_cutups_prev_year2_jul'] = $row->jul_price;
			$data['rsl_cutups_prev_year2_aug'] = $row->aug_price;
			$data['rsl_cutups_prev_year2_sep'] = $row->sep_price;
			$data['rsl_cutups_prev_year2_oct'] = $row->oct_price;
			$data['rsl_cutups_prev_year2_nov'] = $row->nov_price;
			$data['rsl_cutups_prev_year2_dec'] = $row->dec_price;
			$data['rsl_cutups_prev_year2_avg'] = ($row->jan_price + $row->feb_price + $row->mar_price + $row->apr_price + $row->may_price + $row->jun_price + $row->jul_price + $row->aug_price + $row->sep_price + $row->oct_price + $row->nov_price + $row->dec_price) / 12;

			$data['rsl_cutups_prev_year2_min'] = min($row->jan_price, $row->feb_price, $row->mar_price, $row->apr_price, $row->may_price, $row->jun_price, $row->jul_price, $row->aug_price, $row->sep_price, $row->oct_price, $row->nov_price, $row->dec_price);

			$data['rsl_cutups_prev_year2_max'] = max($row->jan_price, $row->feb_price, $row->mar_price, $row->apr_price, $row->may_price, $row->jun_price, $row->jul_price, $row->aug_price, $row->sep_price, $row->oct_price, $row->nov_price, $row->dec_price);
		}


		$rsl_cutups_prev_year1 = $this->admin->get_data('comparative_price_tbl', array('comp_price_product' => 'CHOOKSIES CUT UPS', 'comp_price_segment' => 'RSL', 'comp_price_year' => $year - 1,	'bc_id'	=>	$bc_id), 
			FALSE, 'AVG(comp_price_jan) as jan_price, AVG(comp_price_feb) as feb_price, AVG(comp_price_mar) as mar_price, AVG(comp_price_apr) as apr_price, AVG(comp_price_may) as may_price, AVG(comp_price_jun) as jun_price, AVG(comp_price_jul) as jul_price, AVG(comp_price_aug) as aug_price, AVG(comp_price_sep) as sep_price, AVG(comp_price_oct) as oct_price, AVG(comp_price_nov) as nov_price, AVG(comp_price_dec) as dec_price');
		
		$data['rsl_cutups_prev_year1_jan'] = 0;
		$data['rsl_cutups_prev_year1_feb'] = 0;
		$data['rsl_cutups_prev_year1_mar'] = 0;
		$data['rsl_cutups_prev_year1_apr'] = 0;
		$data['rsl_cutups_prev_year1_may'] = 0;
		$data['rsl_cutups_prev_year1_jun'] = 0;
		$data['rsl_cutups_prev_year1_jul'] = 0;
		$data['rsl_cutups_prev_year1_aug'] = 0;
		$data['rsl_cutups_prev_year1_sep'] = 0;
		$data['rsl_cutups_prev_year1_oct'] = 0;
		$data['rsl_cutups_prev_year1_nov'] = 0;
		$data['rsl_cutups_prev_year1_dec'] = 0;
		$data['rsl_cutups_prev_year1_avg'] = 0;
		$data['rsl_cutups_prev_year1_min'] = 0;
		$data['rsl_cutups_prev_year1_max'] = 0;

		foreach($rsl_cutups_prev_year1 as $row){
			$data['rsl_cutups_prev_year1_jan'] = $row->jan_price;
			$data['rsl_cutups_prev_year1_feb'] = $row->feb_price;
			$data['rsl_cutups_prev_year1_mar'] = $row->mar_price;
			$data['rsl_cutups_prev_year1_apr'] = $row->apr_price;
			$data['rsl_cutups_prev_year1_may'] = $row->may_price;
			$data['rsl_cutups_prev_year1_jun'] = $row->jun_price;
			$data['rsl_cutups_prev_year1_jul'] = $row->jul_price;
			$data['rsl_cutups_prev_year1_aug'] = $row->aug_price;
			$data['rsl_cutups_prev_year1_sep'] = $row->sep_price;
			$data['rsl_cutups_prev_year1_oct'] = $row->oct_price;
			$data['rsl_cutups_prev_year1_nov'] = $row->nov_price;
			$data['rsl_cutups_prev_year1_dec'] = $row->dec_price;
			$data['rsl_cutups_prev_year1_avg'] = ($row->jan_price + $row->feb_price + $row->mar_price + $row->apr_price + $row->may_price + $row->jun_price + $row->jul_price + $row->aug_price + $row->sep_price + $row->oct_price + $row->nov_price + $row->dec_price) / 12;

			$data['rsl_cutups_prev_year1_min'] = min($row->jan_price, $row->feb_price, $row->mar_price, $row->apr_price, $row->may_price, $row->jun_price, $row->jul_price, $row->aug_price, $row->sep_price, $row->oct_price, $row->nov_price, $row->dec_price);

			$data['rsl_cutups_prev_year1_max'] = max($row->jan_price, $row->feb_price, $row->mar_price, $row->apr_price, $row->may_price, $row->jun_price, $row->jul_price, $row->aug_price, $row->sep_price, $row->oct_price, $row->nov_price, $row->dec_price);
		}


		/*Reseller Liver / Gizzard*/
		$join_rsl_liver = array(
			'sales_item_tbl b' => 'a.sales_id = b.sales_id AND a.sales_status = 1 AND b.sales_item_status = 1 AND a.sales_year = ' . $year,
			'sales_details_tbl c' => 'b.sales_item_id = c.sales_item_id AND c.sales_det_status = 1',
			'material_tbl d' => 'b.material_id = d.material_id',
			'material_group_tbl e' => 'd.material_group_id = e.material_group_id AND e.material_group_name = \'LIVER / GIZZARD\'',
			'outlet_tbl f' => 'a.outlet_id = f.outlet_id AND f.bc_id = ' . $bc_id,
			'outlet_brand_tbl g' => 'f.outlet_id = g.outlet_id AND g.outlet_brand_status = 1',
			'brand_tbl h' => 'g.brand_id  = h.brand_id AND h.brand_name = \'RESELLER\''
		);

		$get_rsl_liver = $this->admin->get_join('sales_tbl a', $join_rsl_liver, FALSE, FALSE, 'YEAR(c.sales_det_date), MONTH(c.sales_det_date)', 'AVG(c.sales_det_asp) as asp, MONTH(c.sales_det_date) asp_date');

		$data['rsl_liver1'] = 0;
		$data['rsl_liver2'] = 0;
		$data['rsl_liver3'] = 0;
		$data['rsl_liver4'] = 0;
		$data['rsl_liver5'] = 0;
		$data['rsl_liver6'] = 0;
		$data['rsl_liver7'] = 0;
		$data['rsl_liver8'] = 0;
		$data['rsl_liver9'] = 0;
		$data['rsl_liver10'] = 0;
		$data['rsl_liver11'] = 0;
		$data['rsl_liver12'] = 0;
		$data['rsl_liver_total'] = 0;
		$data['rsl_liver_count'] = 0;
		$data['rsl_liver_avg'] = 0;
		$data['rsl_liver_min'] = 0;
		$data['rsl_liver_max'] = 0;

		foreach($get_rsl_liver as $row){
			$asp = $row->asp;
			$month = $row->asp_date;
			$data['rsl_liver' . $month] = $asp;
			$data['rsl_liver_total'] += $asp;

			if($asp < $data['rsl_liver_min'] || $data['rsl_liver_count'] == 0){
				$data['rsl_cutups_min'] = $asp;
			}

			if($asp > $data['rsl_liver_max'] || $data['rsl_liver_count'] == 0){
				$data['rsl_liver_max'] = $asp;
			}

			$data['rsl_liver_count']++;
		}

		$data['rsl_liver_avg'] = $data['rsl_liver_total'] != 0 ? $data['rsl_liver_total'] / $data['rsl_liver_count'] : 0;


		$rsl_liver_prev_year2 = $this->admin->get_data('comparative_price_tbl', array('comp_price_product' => 'GIZZARD / LIVER', 'comp_price_segment' => 'RESELLER', 'comp_price_year' => $year - 2,	'bc_id'	=>	$bc_id), 
			FALSE, 'AVG(comp_price_jan) as jan_price, AVG(comp_price_feb) as feb_price, AVG(comp_price_mar) as mar_price, AVG(comp_price_apr) as apr_price, AVG(comp_price_may) as may_price, AVG(comp_price_jun) as jun_price, AVG(comp_price_jul) as jul_price, AVG(comp_price_aug) as aug_price, AVG(comp_price_sep) as sep_price, AVG(comp_price_oct) as oct_price, AVG(comp_price_nov) as nov_price, AVG(comp_price_dec) as dec_price');
		
		$data['rsl_liver_prev_year2_jan'] = 0;
		$data['rsl_liver_prev_year2_feb'] = 0;
		$data['rsl_liver_prev_year2_mar'] = 0;
		$data['rsl_liver_prev_year2_apr'] = 0;
		$data['rsl_liver_prev_year2_may'] = 0;
		$data['rsl_liver_prev_year2_jun'] = 0;
		$data['rsl_liver_prev_year2_jul'] = 0;
		$data['rsl_liver_prev_year2_aug'] = 0;
		$data['rsl_liver_prev_year2_sep'] = 0;
		$data['rsl_liver_prev_year2_oct'] = 0;
		$data['rsl_liver_prev_year2_nov'] = 0;
		$data['rsl_liver_prev_year2_dec'] = 0;
		$data['rsl_liver_prev_year2_avg'] = 0;
		$data['rsl_liver_prev_year2_min'] = 0;
		$data['rsl_liver_prev_year2_max'] = 0;

		foreach($rsl_liver_prev_year2 as $row){
			$data['rsl_liver_prev_year2_jan'] = $row->jan_price;
			$data['rsl_liver_prev_year2_feb'] = $row->feb_price;
			$data['rsl_liver_prev_year2_mar'] = $row->mar_price;
			$data['rsl_liver_prev_year2_apr'] = $row->apr_price;
			$data['rsl_liver_prev_year2_may'] = $row->may_price;
			$data['rsl_liver_prev_year2_jun'] = $row->jun_price;
			$data['rsl_liver_prev_year2_jul'] = $row->jul_price;
			$data['rsl_liver_prev_year2_aug'] = $row->aug_price;
			$data['rsl_liver_prev_year2_sep'] = $row->sep_price;
			$data['rsl_liver_prev_year2_oct'] = $row->oct_price;
			$data['rsl_liver_prev_year2_nov'] = $row->nov_price;
			$data['rsl_liver_prev_year2_dec'] = $row->dec_price;
			$data['rsl_liver_prev_year2_avg'] = ($row->jan_price + $row->feb_price + $row->mar_price + $row->apr_price + $row->may_price + $row->jun_price + $row->jul_price + $row->aug_price + $row->sep_price + $row->oct_price + $row->nov_price + $row->dec_price) / 12;

			$data['rsl_liver_prev_year2_min'] = min($row->jan_price, $row->feb_price, $row->mar_price, $row->apr_price, $row->may_price, $row->jun_price, $row->jul_price, $row->aug_price, $row->sep_price, $row->oct_price, $row->nov_price, $row->dec_price);

			$data['rsl_liver_prev_year2_max'] = max($row->jan_price, $row->feb_price, $row->mar_price, $row->apr_price, $row->may_price, $row->jun_price, $row->jul_price, $row->aug_price, $row->sep_price, $row->oct_price, $row->nov_price, $row->dec_price);
		}


		$rsl_liver_prev_year1 = $this->admin->get_data('comparative_price_tbl', array('comp_price_product' => 'GIZZARD / LIVER', 'comp_price_segment' => 'RSL', 'comp_price_year' => $year - 1,	'bc_id'	=>	$bc_id), 
			FALSE, 'AVG(comp_price_jan) as jan_price, AVG(comp_price_feb) as feb_price, AVG(comp_price_mar) as mar_price, AVG(comp_price_apr) as apr_price, AVG(comp_price_may) as may_price, AVG(comp_price_jun) as jun_price, AVG(comp_price_jul) as jul_price, AVG(comp_price_aug) as aug_price, AVG(comp_price_sep) as sep_price, AVG(comp_price_oct) as oct_price, AVG(comp_price_nov) as nov_price, AVG(comp_price_dec) as dec_price');
		
		$data['rsl_liver_prev_year1_jan'] = 0;
		$data['rsl_liver_prev_year1_feb'] = 0;
		$data['rsl_liver_prev_year1_mar'] = 0;
		$data['rsl_liver_prev_year1_apr'] = 0;
		$data['rsl_liver_prev_year1_may'] = 0;
		$data['rsl_liver_prev_year1_jun'] = 0;
		$data['rsl_liver_prev_year1_jul'] = 0;
		$data['rsl_liver_prev_year1_aug'] = 0;
		$data['rsl_liver_prev_year1_sep'] = 0;
		$data['rsl_liver_prev_year1_oct'] = 0;
		$data['rsl_liver_prev_year1_nov'] = 0;
		$data['rsl_liver_prev_year1_dec'] = 0;
		$data['rsl_liver_prev_year1_avg'] = 0;
		$data['rsl_liver_prev_year1_min'] = 0;
		$data['rsl_liver_prev_year1_max'] = 0;

		foreach($rsl_liver_prev_year1 as $row){
			$data['rsl_liver_prev_year1_jan'] = $row->jan_price;
			$data['rsl_liver_prev_year1_feb'] = $row->feb_price;
			$data['rsl_liver_prev_year1_mar'] = $row->mar_price;
			$data['rsl_liver_prev_year1_apr'] = $row->apr_price;
			$data['rsl_liver_prev_year1_may'] = $row->may_price;
			$data['rsl_liver_prev_year1_jun'] = $row->jun_price;
			$data['rsl_liver_prev_year1_jul'] = $row->jul_price;
			$data['rsl_liver_prev_year1_aug'] = $row->aug_price;
			$data['rsl_liver_prev_year1_sep'] = $row->sep_price;
			$data['rsl_liver_prev_year1_oct'] = $row->oct_price;
			$data['rsl_liver_prev_year1_nov'] = $row->nov_price;
			$data['rsl_liver_prev_year1_dec'] = $row->dec_price;
			$data['rsl_liver_prev_year1_avg'] = ($row->jan_price + $row->feb_price + $row->mar_price + $row->apr_price + $row->may_price + $row->jun_price + $row->jul_price + $row->aug_price + $row->sep_price + $row->oct_price + $row->nov_price + $row->dec_price) / 12;

			$data['rsl_liver_prev_year1_min'] = min($row->jan_price, $row->feb_price, $row->mar_price, $row->apr_price, $row->may_price, $row->jun_price, $row->jul_price, $row->aug_price, $row->sep_price, $row->oct_price, $row->nov_price, $row->dec_price);

			$data['rls_liver_prev_year1_max'] = max($row->jan_price, $row->feb_price, $row->mar_price, $row->apr_price, $row->may_price, $row->jun_price, $row->jul_price, $row->aug_price, $row->sep_price, $row->oct_price, $row->nov_price, $row->dec_price);
		}

		return $data;

	}


}
