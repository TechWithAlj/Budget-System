<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Production extends CI_Controller {


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
					redirect('ahg');
				}elseif($user_type == "5"){
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
		$this->production_cost();
	}

	public function get_brand(){
		$info = $this->_require_login();
		if($_SERVER['REQUEST_METHOD'] == 'POST'){
			$brand_type_id = decode($this->input->post('id'));
			$check_brand_type = $this->admin->check_data('brand_type_tbl', array('brand_type_id' => $brand_type_id, 'brand_type_status' => 1));
			if($check_brand_type == TRUE){

				$brand = $this->admin->get_data('brand_tbl', array('brand_type_id' => $brand_type_id, 'brand_status' => 1));	
				$bc_brand = '';
				foreach($brand as $row):
					$bc_brand .= '<option value="' . encode($row->brand_id) . '">' . $row->brand_name . '</option>';
				endforeach;
				$data['result'] = 1;
				$data['info'] = $bc_brand;
			}else{
				$data['result'] = 0;
			}
			echo json_encode($data);
		}
	}

	public function production_cost(){
		$info = $this->_require_login();
		$join = array(
			'material_tbl b' => 'a.prod_id = b.material_id',
			'user_tbl c' => 'a.created_by = c.user_id',
			'component_type_tbl d' => 'a.component_type_id = d.component_type_id',
			'process_type_tbl e' => 'a.process_type_id = e.process_type_id and e.process_type_id != 5'
		);
		$data['config_prod'] = $this->admin->get_join('config_prod_tbl a', $join, FALSE,'e.process_type_id, b.material_desc', FALSE);

		$join = array(
			'material_group_tbl b' => 'a.material_group_id = b.material_group_id and a.material_status = 1'
		);
		$data['material'] = $this->admin->get_join('material_tbl a', $join, FALSE,'a.material_desc ASC','a.material_code');

		$data['process_type'] = $this->admin->get_data('process_type_tbl', array('process_type_status'	=>	1, 'process_type_id !='	=>	5));

		$data['component_type'] = $this->admin->get_data('component_type_tbl', array('component_type_status'	=>	1));

		$data['bc'] = $this->admin->get_data('bc_tbl', 'bc_status=1');
		$data['title'] = 'Production Cost';
		$data['content'] = $this->load->view('prod/production_cost_content', $data , TRUE);
		$this->load->view('prod/templates', $data);
	}

	public function add_config_prod(){
		
		$info = $this->_require_login();
		$user_id = decode($info['user_id']);
		if($_SERVER['REQUEST_METHOD'] == 'POST'){

			$prod_id = decode($this->input->post('prod_id'));
			$process_type_id = decode($this->input->post('process_type_id'));
			$component_type_id = 1;


			if(!empty($prod_id) && !empty($process_type_id) && !empty($component_type_id)){
				$this->db->trans_start();
				$check_config = $this->admin->check_data('config_prod_tbl', array('prod_id' =>  $prod_id,	'process_type_id'	=>	$process_type_id,	'config_prod_status'	=>	1));

				
				if($check_config == FALSE){
					$set = array(
						'prod_id' => $prod_id,
						'process_type_id' => $process_type_id,
						'component_type_id' => $component_type_id,
						'config_prod_status' => 1,
						'created_by' => $user_id
					);

					$result = $this->admin->insert_data('config_prod_tbl', $set, TRUE);

				}else{
					$msg = '<div class="alert alert-danger">Error! Config item already exist!</div>';
					$this->session->set_flashdata('message', $msg);
					redirect('production/production-cost');
				}
			}else{
				$msg = '<div class="alert alert-danger">Error please try again!</div>';
				$this->session->set_flashdata('message', $msg);
				redirect('production/production-cost');
			}

			if($this->db->trans_status() === FALSE){
				$this->db->trans_rollback();
				$msg = '<div class="alert alert-danger">Error please try again!</div>';
			}else{
				$this->db->trans_commit();
				$msg = '<div class="alert alert-success"><strong>Config successfully added.</strong></div>';
			}
			if($process_type_id != 5){
				$this->session->set_flashdata('message', $msg);
				redirect('production/production-cost');
			} else {
				$this->session->set_flashdata('message', $msg);
				redirect('production/sales-bom');
			}
				
		}else{
			redirect('production/production-cost');
		}
	}

	public function view_config_prod($id, $process_type_id){

		$info = $this->_require_login();
		$data['config_prod_id'] = $id;
		$config_prod_id = decode($id);
		$data['process_type_id'] = $process_type_id;
		$sql = 'SELECT
					*
				FROM
					(
						SELECT
							a.*, b.material_code,
							b.material_desc,
							b.material_id,
							c.user_id,
							c.user_fname,
							c.user_lname,
							d.component_type,
							d.order_base,
							f.amount_type_name,
							g.unit_name
						FROM
							`config_prod_dtl_tbl` `a`
						JOIN `material_tbl` `b` ON `a`.`article_id` = `b`.`material_id`
						AND `a`.`article_type_id` = 1
						JOIN `user_tbl` `c` ON `a`.`created_by` = `c`.`user_id`
						JOIN `component_type_tbl` `d` ON `a`.`component_type_id` = `d`.`component_type_id`
						JOIN `amount_type_tbl` `f` ON `a`.`amount_type_id` = `f`.`amount_type_id`
						AND `a`.`config_prod_id` = '.$config_prod_id.'
						left join `unit_tbl` `g` ON `a`.`unit_id` = `g`.`unit_id`
						UNION ALL
							SELECT
								a.*, b.service_id,
								b.service_desc,
								b.service_code,
								c.user_id,
								c.user_fname,
								c.user_lname,
								d.component_type,
								d.order_base,
								f.amount_type_name,
								null as unit_name
							FROM
								`config_prod_dtl_tbl` `a`
							JOIN `services_tbl` `b` ON `a`.`article_id` = `b`.`service_id`
							AND `a`.`article_type_id` = 2
							JOIN `user_tbl` `c` ON `a`.`created_by` = `c`.`user_id`
							JOIN `component_type_tbl` `d` ON `a`.`component_type_id` = `d`.`component_type_id`
							JOIN `amount_type_tbl` `f` ON `a`.`amount_type_id` = `f`.`amount_type_id`
							AND `a`.`config_prod_id` = '.$config_prod_id.'
					) z
				where z.config_prod_dtl_status != 5
				order by z.order_base, z.material_desc';
		$data['config_prod'] = $this->admin->get_query($sql);

		$join = array(
			'material_group_tbl b' => 'a.material_group_id = b.material_group_id and a.material_status = 1'
		);
		$data['material'] = $this->admin->get_join('material_tbl a', $join, FALSE,'a.material_desc ASC','a.material_code');

		$data['article_type'] = $this->admin->get_data('article_type_tbl', 'article_type_status=1');

		$data['component_type'] = $this->admin->get_data('component_type_tbl', array('component_type_status'	=>	1));
		$data['unit'] = $this->admin->get_data('unit_tbl', 'unit_status=1');
		$data['services'] = $this->admin->get_data('services_tbl', 'service_status=1');
		$data['amount_type'] = $this->admin->get_data('amount_type_tbl', 'amount_type_status=1');
		$data['title'] = 'Production Cost';
		$data['content'] = $this->load->view('prod/view_config_prod_content', $data , TRUE);
		$this->load->view('prod/templates', $data);
	}

	public function add_config_prod_dtl(){
		$info = $this->_require_login();
		$user_id = decode($info['user_id']);
		if($_SERVER['REQUEST_METHOD'] == 'POST'){

			$config_prod_id = decode($this->input->post('config_prod_id'));
			$article = $this->input->post('article_id');
			$article_type_id = decode($this->input->post('article_type_id'));
			$component_type_id = decode($this->input->post('component_type_id'));
			$unit_id = decode($this->input->post('unit_id'));
			$amount_type_id = decode($this->input->post('amount_type_id'));
			$show_on_trans = decode($this->input->post('show_on_trans'));
			$process_type_id = decode($this->input->post('process_type_id'));

			if(!empty($config_prod_id) && !empty($article) && !empty($article_type_id) && !empty($component_type_id) && !empty($amount_type_id)){
				$this->db->trans_start();
				foreach ($article as $key) {
					$article_id = clean_data(decode($key));
					if($article_type_id == 1){
						$join = array(
							'material_unit_tbl b' => 'a.material_id = b.material_id and a.material_id ='.$article_id.' and a.material_status = 1',
						);
						$material_unit_checking = $this->admin->check_join('material_tbl a', $join, TRUE);
						if($material_unit_checking['result']){
							$valuation_unit = $material_unit_checking['info']->valuation_unit;
						} else {
							$valuation_unit = NULL;
						}
					} else {
						$valuation_unit = NULL;
					}
					
					$check_config_dtl = $this->admin->check_data('config_prod_dtl_tbl', array('article_id' =>  $article_id,	'article_type_id'	=>	$article_type_id,	'config_prod_dtl_status !='	=>	5,	'config_prod_id'	=> $config_prod_id));

					
					if($check_config_dtl == FALSE){
						$set = array(
							
							'config_prod_id' => $config_prod_id,
							'article_id' => $article_id,
							'article_type_id' => $article_type_id,
							'component_type_id' => strtoupper($component_type_id),
							'unit_id' => $valuation_unit,// serves now the valuation unit
							'amount_type_id' => $amount_type_id,
							'config_prod_dtl_status' => 1,
							'show_on_trans' => $show_on_trans,
							'created_by' => $user_id
						);

						$result = $this->admin->insert_data('config_prod_dtl_tbl', $set, TRUE);

					}else{
						$msg = '<div class="alert alert-danger">Error! Config item already exist!</div>';
						$this->session->set_flashdata('message', $msg);
						redirect('production/view-config-prod/' . encode($config_prod_id));
					}
				}
			}else{
				$msg = '<div class="alert alert-danger">Error please try again!</div>';
				$this->session->set_flashdata('message', $msg);
				redirect('production/view-config-prod/' . encode($config_prod_id));
			}

			if($this->db->trans_status() === FALSE){
				$this->db->trans_rollback();
				$msg = '<div class="alert alert-danger">Error please try again!</div>';
			}else{
				$this->db->trans_commit();
				$msg = '<div class="alert alert-success"><strong>Config successfully added.</strong></div>';
			}			
			$this->session->set_flashdata('message', $msg);
			redirect('production/view-config-prod/' . encode($config_prod_id).'/'. encode($process_type_id));
		}else{
			redirect('production/production-cost');
		}
	}

	public function prod_trans($id){
		$info = $this->_require_login();
		$data['bc_id'] = $id;
		$bc_id = decode($id);
		$data['title'] = 'Production Cost';
		$data['bc'] = $this->admin->get_data('bc_tbl', array('bc_id' => $bc_id), true, 'bc_name');
		$data['year'] = $this->_active_year();
		$join = array(
			'bc_tbl b' => 'a.bc_id = b.bc_id and a.bc_id ='.$bc_id,
			'user_tbl c' => 'a.created_by = c.user_id',
			'status_tbl d' => 'd.status_id = a.prod_trans_status and a.prod_trans_status != 5',
			'prod_trans_dtl_tbl e' => 'a.prod_trans_id = e.prod_trans_id and YEAR(e.prod_trans_dtl_date)= '.$this->_active_year(),
			'material_tbl f' => 'a.prod_id = f.material_id',
			'process_type_tbl g' => 'a.process_type_id = g.process_type_id and g.process_type_id != 5'
		);
		$data['prod_trans'] = $this->admin->get_join('prod_trans_tbl a', $join, FALSE,'f.material_desc ASC', 'a.prod_trans_id');

		$join = array(
			'material_group_tbl b' => 'a.material_group_id = b.material_group_id and a.material_status = 1 and a.material_group_id <= 10'
		);
		$data['material'] = $this->admin->get_join('material_tbl a', $join, FALSE,'a.material_desc ASC','a.material_code');

		$join = array(
			'ext_prod_trans_dtl_tbl b' => 'a.ext_prod_trans_id = b.ext_prod_trans_id and a.bc_id ='.$bc_id.' and YEAR(b.trans_dtl_date)= '.$this->_active_year(),
			'user_tbl c' => 'a.created_by = c.user_id',
			'status_tbl d' => 'd.status_id = a.ext_prod_trans_status and a.ext_prod_trans_status != 5',
			'material_tbl f' => 'a.material_id = f.material_id'
		);
		$data['ext_prod_trans'] = $this->admin->get_join('ext_prod_trans_tbl a', $join, FALSE,'f.material_desc ASC', 'a.ext_prod_trans_id');

		$data['content'] = $this->load->view('prod/prod_trans_content', $data , TRUE);
		$this->load->view('prod/templates', $data);
	}

	public function get_prod_trans(){
		
		$info = $this->_require_login();
		if($_SERVER['REQUEST_METHOD'] == 'POST'){
			$prod_trans_dtl_date =clean_data($this->input->post('prod_trans_date'));
			$bc_id = clean_data(decode($this->input->post('bc_id')));
			
			if($prod_trans_dtl_date){
				$join = array(
					'bc_tbl b' => 'a.bc_id = b.bc_id and a.bc_id ='.$bc_id,
					'user_tbl c' => 'a.created_by = c.user_id',
					'status_tbl d' => 'd.status_id = a.prod_trans_status and a.prod_trans_status != 5',
					'prod_trans_dtl_tbl e' => 'a.prod_trans_id = e.prod_trans_id and YEAR(e.prod_trans_dtl_date)= '.$prod_trans_dtl_date,
					'material_tbl f' => 'a.prod_id = f.material_id',
					'process_type_tbl g' => 'a.process_type_id = g.process_type_id and g.process_type_id != 5'
				);
				$get_prod_trans_yearly = $this->admin->get_join('prod_trans_tbl a', $join, FALSE,'f.material_desc ASC', 'a.prod_trans_id');
				$prod_trans = '';
				if($get_prod_trans_yearly){
					foreach($get_prod_trans_yearly as $row){
						if($row->prod_trans_status != 5){
							$prod_trans .= '<tr><td class="text-center"><a href="" class="remove-prod-trans" data-id="'.encode($row->prod_trans_id).'" data-bc_id="'.$bc_id.'" data-mat_desc="'.encode($row->material_desc).'"><i class="fa fa-remove"></i></a></td>';
						}
						$prod_trans .= '<td>' . $row->process_type_name .'</td>';
						$prod_trans .= '<td>' . $row->material_desc .'</td>';
						$prod_trans .= '<td>' . $row->user_fname.' '.$row->user_lname .'</td>';
						$prod_trans .= '<td>' . date( 'm/d/Y', strtotime($row->created_ts)) .'</td>';
						$prod_trans .= '<td class="text-center"><a href="'.base_url('admin/view-prod-trans/' . encode($row->prod_trans_id).'/'.encode($bc_id).'/'.encode($row->process_type_id)).'" class="btn btn-xs btn-success">View</a>&nbsp;&nbsp;<a href="'.base_url('admin/view-cost-sheet/' . encode($row->prod_trans_id).'/'.encode($bc_id).'/'.encode(date('Y', strtotime($row->prod_trans_dtl_date))).'/'.encode($row->process_type_id)).'" class="btn btn-xs btn-primary">Cost Sheet</a></td>';
						$prod_trans .= '</tr>';
					}
					$data['prod_trans'] = $prod_trans;
					$data['result'] = 1;
				} else {
					$data['result'] = 1;
				}
			} else {
				$data['result'] = 0;
			}
		}
		echo json_encode($data);
	}

	public function new_prod_trans($id){
		$info = $this->_require_login();
		$data['bc_id'] = $id;
		$data['year'] = $this->_active_year();
		$bc_id = decode($id);
		$data['bc'] = $this->admin->get_data('bc_tbl', array('bc_id' => $bc_id), true, 'bc_name');
		$data['title'] = 'Production Cost';

		$join = array(
			'material_tbl b' => 'a.prod_id = b.material_id',
			'process_type_tbl c' => 'a.process_type_id = c.process_type_id and c.process_type_id != 5'
		);
		$data['config_prod'] = $this->admin->get_join('config_prod_tbl a', $join, FALSE,'c. process_type_id, b.material_desc ASC');
		$data['type'] = $this->admin->get_data('brand_type_tbl', array('brand_type_status' => 1));
		$data['brand'] = $this->admin->get_data('brand_tbl', array('brand_status' => 1));
		$data['content'] = $this->load->view('prod/new_prod_trans_content', $data , TRUE);
		$this->load->view('prod/templates', $data);
	}

	public function get_config_prod(){
		$info = $this->_require_login();
		$year = $this->_active_year();
		/*echo $this->input->post('process_type_name');
		exit();*/

		if($_SERVER['REQUEST_METHOD'] == 'POST'){
			$config_prod_id =clean_data(decode($this->input->post('id')));
			$bc_id = clean_data(decode($this->input->post('bc_id')));

			if($config_prod_id){
				$sql = 'SELECT
							*
						FROM
							(
								SELECT
									a.*, b.material_code,
									b.material_desc,
									b.material_id,
									c.user_id,
									c.user_fname,
									c.user_lname,
									d.component_type,
									d.order_base,
									f.amount_type_name,
									g.unit_name
								FROM
									`config_prod_dtl_tbl` `a`
								JOIN `material_tbl` `b` ON `a`.`article_id` = `b`.`material_id`
								AND `a`.`article_type_id` = 1
								JOIN `user_tbl` `c` ON `a`.`created_by` = `c`.`user_id`
								JOIN `component_type_tbl` `d` ON `a`.`component_type_id` = `d`.`component_type_id`
								JOIN `amount_type_tbl` `f` ON `a`.`amount_type_id` = `f`.`amount_type_id`
								AND `a`.`config_prod_id` = '.$config_prod_id.'
								and `a`.`show_on_trans` = 1
								and `a`.`config_prod_dtl_status` != 5
								AND a.config_prod_dtl_id NOT IN (
									SELECT
										a.config_prod_dtl_id
									FROM
										prod_trans_dtl_tbl a
									JOIN prod_trans_tbl b ON a.prod_trans_id = b.prod_trans_id
									WHERE
										prod_trans_dtl_status != 5
									AND YEAR (`a`.`prod_trans_dtl_date`) = '.$year.'
									AND b.bc_id = '.$bc_id.'
									AND b.config_prod_id = '.$config_prod_id.'
									GROUP BY
										a.config_prod_dtl_id
								)
								LEFT JOIN `unit_tbl` `g` ON `a`.`unit_id` = `g`.`unit_id`
								UNION ALL
									SELECT
										a.*, b.service_code,
										b.service_desc,
										b.service_code,
										c.user_id,
										c.user_fname,
										c.user_lname,
										d.component_type,
										d.order_base,
										f.amount_type_name,
										NULL AS unit_name
									FROM
										`config_prod_dtl_tbl` `a`
									JOIN `services_tbl` `b` ON `a`.`article_id` = `b`.`service_id`
									AND `a`.`article_type_id` = 2
									JOIN `user_tbl` `c` ON `a`.`created_by` = `c`.`user_id`
									JOIN `component_type_tbl` `d` ON `a`.`component_type_id` = `d`.`component_type_id`
									JOIN `amount_type_tbl` `f` ON `a`.`amount_type_id` = `f`.`amount_type_id`
									AND `a`.`config_prod_id` = '.$config_prod_id.'
									and `a`.`show_on_trans` = 1
									and `a`.`config_prod_dtl_status` != 5
									AND a.config_prod_dtl_id NOT IN (
										SELECT
											a.config_prod_dtl_id
										FROM
											prod_trans_dtl_tbl a
										JOIN prod_trans_tbl b ON a.prod_trans_id = b.prod_trans_id
										WHERE
											prod_trans_dtl_status != 5
										AND YEAR (`a`.`prod_trans_dtl_date`) = '.$year.'
										AND b.bc_id = '.$bc_id.'
										AND b.config_prod_id = '.$config_prod_id.'
										GROUP BY
											a.config_prod_dtl_id
									)
							) z
						ORDER BY
							z.order_base asc';

				$get_config_prod = $this->admin->get_query($sql);
				//$value = trim($this->input->post('process_type_name')) == 'CLASSIFICATION' ? 'value="0"' : '';
				$config_prod = '';
				if($get_config_prod){
					$count = 1;
					foreach($get_config_prod as $row){
						$config_prod .= '<tr><input type="hidden" name="config_prod_dtl_id[]" value="' . encode($row->config_prod_dtl_id) . '">';
						$config_prod .= '<td class="text-center">' .$count .'</td>';
						$config_prod .= '<td>' .$row->material_desc.'</td>';
						$config_prod .= '<td>' .$row->material_code.'</td>';
						$config_prod .= '<td>' .$row->unit_name.'</td>';
						$config_prod .= '<td>' . $row->component_type .'</td>';
						for ($i=1; $i <=12 ; $i++) {
							$month = date('M', strtotime($year.'-'.$i.'-01'));
							$config_prod .= '<td><input type="text" name="rate['.$month.'][]" class="txt form-control input-sm" id="rate-'.$month.'" size="6"></td>';
							$config_prod .= '<td><input type="text" name="cost['.$month.'][]" class="form-control input-sm" size="6"></td>';
						}
						
						
						$config_prod .= '</tr>';
						$count++;
					}
					if(trim($this->input->post('process_type_name')) == 'CLASSIFICATION'){
						$config_prod .= '<tr class="span7">
			                          <th class="total"></th>
			                          <th class="total"></th>
			                          <th class="total"></th>
			                          <th class="total"></th>
			                          <th class="total text-right">Total%</th>';
		                for ($i=1; $i <=12 ; $i++) {
		                	$config_prod .= '<th class="total text-right" id="total-<?=$i?>"></th>';
		                	$config_prod .= '<th class="total"></th>';
		                }
		                $config_prod .= '</tr>';
					}
					
					$data['config_prod'] = $config_prod;
					$data['result'] = 1;
				} else {
					$data['config_prod'] = 'No data';
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

	public function view_prod_trans($id, $bc_id, $process_type_id){
		$info = $this->_require_login();
		$data['prod_trans_id'] = $id;
		$data['process_type_id'] = $process_type_id;
		$data['bc_id'] = $bc_id;
		$data['bc'] = $this->admin->get_data('bc_tbl', array('bc_id' => decode($bc_id)), true, 'bc_name');
		$data['title'] = 'Production Cost';
		$prod_trans_id = decode($id);
		$data['year'] = $this->_active_year();
		$sql = 'SELECT
					*
				FROM
					(
						SELECT
							a.*, b.material_code,
							b.material_desc,
							b.material_id,
							d.component_type,
							d.order_base,
							f.amount_type_name,
							g.unit_name,
							h.process_type_id
						FROM
							`prod_trans_dtl_tbl` `a`
						JOIN `material_tbl` `b` ON `a`.`article_id` = `b`.`material_id`
						AND `a`.`article_type_id` = 1
						JOIN `component_type_tbl` `d` ON `a`.`component_type_id` = `d`.`component_type_id`
						JOIN `amount_type_tbl` `f` ON `a`.`amount_type_id` = `f`.`amount_type_id`
						AND `a`.`prod_trans_dtl_status` <> 5 AND `a`.`prod_trans_id` = '.$prod_trans_id.'
						AND `a`.`show_on_trans` = 1
						left join `unit_tbl` `g` ON `a`.`unit_id` = `g`.`unit_id`
						JOIN `prod_trans_tbl` `h` ON `a`.`prod_trans_id` = `h`.`prod_trans_id`
						UNION ALL
							SELECT
								a.*, b.service_code,
								b.service_desc,
								b.service_code,
								d.component_type,
								d.order_base,
								f.amount_type_name,
								null as unit_name,
								h.process_type_id
							FROM
								`prod_trans_dtl_tbl` `a`
							JOIN `services_tbl` `b` ON `a`.`article_id` = `b`.`service_id`
							AND `a`.`article_type_id` = 2
							JOIN `component_type_tbl` `d` ON `a`.`component_type_id` = `d`.`component_type_id`
							JOIN `amount_type_tbl` `f` ON `a`.`amount_type_id` = `f`.`amount_type_id`
							AND `a`.`prod_trans_dtl_status` <> 5 AND `a`.`prod_trans_id` = '.$prod_trans_id.'
							AND `a`.`show_on_trans` = 1
							JOIN `prod_trans_tbl` `h` ON `a`.`prod_trans_id` = `h`.`prod_trans_id`
					) z
				group by z.config_prod_dtl_id
				order by z.order_base';
		$data['prod_trans'] = $this->admin->get_query($sql);
		$data['content'] = $this->load->view('prod/view_prod_trans_content', $data , TRUE);
		$this->load->view('prod/templates', $data);
	}

	public function edit_prod_trans($prod_trans_id, $config_prod_dtl_id, $bc_id, $year, $process_type_id){
		$info = $this->_require_login();
		$data['prod_trans_id'] = $prod_trans_id;
		$data['config_prod_dtl_id'] = $config_prod_dtl_id;
		$data['bc_id'] = $bc_id;
		$data['bc'] = $this->admin->get_data('bc_tbl', array('bc_id' => decode($bc_id)), true, 'bc_name');
		$data['year'] = $year;
		$data['process_type_id'] = $process_type_id;

		$prod_trans_id = decode($prod_trans_id);
		$config_prod_dtl_id = decode($config_prod_dtl_id);
		
		$sql = 'SELECT
					*
				FROM
					(
						SELECT
							a.*, b.material_code,
							b.material_desc,
							b.material_id,
							d.component_type,
							f.amount_type_name,
							g.unit_name,
							h.process_type_id
						FROM
							`prod_trans_dtl_tbl` `a`
						JOIN `material_tbl` `b` ON `a`.`article_id` = `b`.`material_id`
						AND `a`.`article_type_id` = 1
						JOIN `component_type_tbl` `d` ON `a`.`component_type_id` = `d`.`component_type_id`
						JOIN `amount_type_tbl` `f` ON `a`.`amount_type_id` = `f`.`amount_type_id`
						AND `a`.`prod_trans_dtl_status` <> 5 AND `a`.`prod_trans_id` = '.$prod_trans_id.' and `a`.`config_prod_dtl_id` = '.$config_prod_dtl_id.'
						left join `unit_tbl` `g` ON `a`.`unit_id` = `g`.`unit_id`
						JOIN `prod_trans_tbl` `h` ON `a`.`prod_trans_id` = `h`.`prod_trans_id`
						UNION ALL
							SELECT
								a.*, b.service_code,
								b.service_desc,
								b.service_code,
								d.component_type,
								f.amount_type_name,
								null as unit_name,
								h.process_type_id
							FROM
								`prod_trans_dtl_tbl` `a`
							JOIN `services_tbl` `b` ON `a`.`article_id` = `b`.`service_id`
							AND `a`.`article_type_id` = 2
							JOIN `component_type_tbl` `d` ON `a`.`component_type_id` = `d`.`component_type_id`
							JOIN `amount_type_tbl` `f` ON `a`.`amount_type_id` = `f`.`amount_type_id`
							AND `a`.`prod_trans_dtl_status` <> 5 AND `a`.`prod_trans_id` = '.$prod_trans_id.' and `a`.`config_prod_dtl_id` = '.$config_prod_dtl_id.'
							JOIN `prod_trans_tbl` `h` ON `a`.`prod_trans_id` = `h`.`prod_trans_id`
					) z
				group by z.config_prod_dtl_id';

		$data['prod_trans'] = $this->admin->get_query($sql);

		$data['title'] = 'Production Cost';
		$data['content'] = $this->load->view('prod/edit_prod_trans_content', $data , TRUE);
		$this->load->view('prod/templates', $data);
	}

	public function add_prod_trans(){
		$info = $this->_require_login();
		$user_id = decode($info['user_id']);
		$status = 3;
		/*echo '<pre>';
		print_r($_POST);
		echo '</pre>';
		exit();*/
		if($_SERVER['REQUEST_METHOD'] == 'POST'){
			$bc_id = clean_data(decode($this->input->post('bc_id')));
			$year = clean_data(decode($this->input->post('year')));
			$config_prod = clean_data(decode($this->input->post('config_prod')));
			$process_type_name = clean_data($this->input->post('process_type_name'));
			$brand_id = clean_data(decode($this->input->post('brand_id')));
			if(!empty($bc_id)){
				$id = clean_data($this->input->post('config_prod_dtl_id'));
				$rate = clean_data($this->input->post('rate'));
				$cost = clean_data($this->input->post('cost'));

				if($id == ''){
					$msg = '<div class="alert alert-danger">Error! Cannot process transaction!</div>';
					$this->session->set_flashdata('message', $msg);
					redirect('production/prod-trans/' . encode($bc_id));
				}
				
				$count = 0;
				$config_tbl_data = $this->admin->get_data('config_prod_tbl', array('config_prod_id' => $config_prod), true);
				$this->db->trans_start();
				$insert_prod_trans = array(
					'config_prod_id' => $config_prod,
					'prod_id' => $config_tbl_data->prod_id,
					'process_type_id' => $config_tbl_data->process_type_id,
					'component_type_id' => $config_tbl_data->component_type_id,
					'brand_id' => $brand_id,
					'bc_id' =>$bc_id,
					'prod_trans_status' => $status,
					'created_by' => $user_id,
					'created_ts' => date_now(),
					'trans_type_id' => 1
				);
				$insert_trans = $this->admin->insert_data('prod_trans_tbl', $insert_prod_trans, true);
				//find the dtl that was not display on transaction
				$config_dtl_tbl_data_hidden = $this->admin->get_data('config_prod_dtl_tbl', array('config_prod_id' => $config_prod, 'show_on_trans'	=>	2, 'config_prod_dtl_status' =>	1));
				//LOOP THE RESULT
				foreach ($config_dtl_tbl_data_hidden as $row) {
					//LOOP TO INSERT PER MONTH
					for ($i=1; $i <= 12 ; $i++) {
						$date = $year . '-' .$i.'-01';
						$insert_prod_trans_dtl = array(
							'prod_trans_id' => $insert_trans['id'],
							'config_prod_dtl_id' => $row->config_prod_dtl_id,
							'article_id' => $row->article_id,
							'article_type_id' => $row->article_type_id,
							'component_type_id' => $row->component_type_id,
							'unit_id' => $row->unit_id,
							'amount_type_id' => $row->amount_type_id,
							'show_on_trans' => $row->show_on_trans,
							'prod_trans_dtl_date' =>$date,
							'prod_trans_dtl_status' => 1
						);
						$this->admin->insert_data('prod_trans_dtl_tbl', $insert_prod_trans_dtl, true);
					}
				}
				foreach($id as $row){

					$config_prod_dtl_id = decode($row);

					$sql = 'SELECT
								a.config_prod_dtl_id
							FROM
								prod_trans_dtl_tbl a
							JOIN prod_trans_tbl b ON a.prod_trans_id = b.prod_trans_id
							WHERE
								prod_trans_dtl_status != 5
							AND YEAR (`a`.`prod_trans_dtl_date`) = '.$year.'
							AND b.bc_id = '.$bc_id.'
							AND a.config_prod_dtl_id = '.$config_prod_dtl_id.'
							GROUP BY
								a.config_prod_dtl_id';
					$get_config_prod = $this->admin->check_query($sql);
					if(!$get_config_prod){
						
						//FIND THE DATA IN THE CONFIG THAT WAS DISPLAY IN THE TRANSSACTION
						$config_dtl_tbl_data = $this->admin->get_data('config_prod_dtl_tbl', array('config_prod_dtl_id' => $config_prod_dtl_id), true);
						for ($i=1; $i <= 12 ; $i++) {
							$month = date('M', strtotime($year.'-'.$i.'-01'));
							$rate_amount = $rate[$month][$count];
							$cost_amount = $cost[$month][$count];
							$date = $year . '-' .$i.'-01';
							
							$insert_prod_trans_dtl = array(
								'prod_trans_id' => $insert_trans['id'],
								'config_prod_dtl_id' => $config_prod_dtl_id,
								'article_id' => $config_dtl_tbl_data->article_id,
								'article_type_id' => $config_dtl_tbl_data->article_type_id,
								'component_type_id' => $config_dtl_tbl_data->component_type_id,
								'unit_id' => $config_dtl_tbl_data->unit_id,
								'amount_type_id' => $config_dtl_tbl_data->amount_type_id,
								'show_on_trans' => $config_dtl_tbl_data->show_on_trans,
								'rate' =>$rate_amount,
								'cost' =>$cost_amount,
								'prod_trans_dtl_date' =>$date,
								'prod_trans_dtl_status' => 1
							);
							$this->admin->insert_data('prod_trans_dtl_tbl', $insert_prod_trans_dtl, true);
						}
						$count++;
					} else {
						$msg = '<div class="alert alert-danger">Notice! Already exist production transaction!</div>';
						$this->session->set_flashdata('message', $msg);
						redirect('production/prod-trans/' . encode($bc_id));
					}
				}

				if($this->db->trans_status() === FALSE){
					$this->db->trans_rollback();
					$msg = '<div class="alert alert-danger">Error please try again!</div>';
				}else{
					$this->db->trans_commit();
					$msg = '<div class="alert alert-success"><strong>Production Transaction successfully added.</strong></div>';
				}
				if(trim($process_type_name) == 'SALES BOM'){
					$this->session->set_flashdata('message', $msg);
					redirect('production/sales-bom-trans/' . encode($bc_id));
				} else {
					$this->session->set_flashdata('message', $msg);
					redirect('production/prod-trans/' . encode($bc_id));
				}
					
			}else{
				echo 'Something wrong';
			}
		}
	}

	public function update_prod_trans(){
		$info = $this->_require_login();
		$user_id = decode($info['user_id']);
		if($_SERVER['REQUEST_METHOD'] == 'POST'){
			$bc_id = clean_data(decode($this->input->post('bc_id')));
			$year = clean_data(decode($this->input->post('year')));
			$process_type_id = clean_data(decode($this->input->post('process_type_id')));
			$prod_trans_id = clean_data(decode($this->input->post('prod_trans_id')));
			if(!empty($bc_id) && !empty($prod_trans_id)){
				$id = clean_data($this->input->post('config_prod_dtl_id'));
				$rate = clean_data($this->input->post('rate'));
				$cost = clean_data($this->input->post('cost'));
				$count = 0;
				$this->db->trans_start();
				foreach($id as $row){

					$config_prod_dtl_id = decode($row);

					//update header transaction
					$update_prod_trans = array(
						'modified_by' => $user_id,
						'modified_ts' => date_now()
					);
					$where = array(
						'prod_trans_id' => $prod_trans_id
					);
					$this->admin->update_data('prod_trans_tbl', $update_prod_trans, $where);

					for ($i=1; $i <= 12 ; $i++) {
						
						$rate_amount = $rate[$i][$count];
						$cost_amount = $cost[$i][$count];
						
						$update_prod_trans_dtl = array(
							'rate' =>$rate_amount,
							'cost' =>$cost_amount
						);
						$where = array(
							'config_prod_dtl_id' => $config_prod_dtl_id,
							'prod_trans_id' => $prod_trans_id,
							'MONTH(prod_trans_dtl_date)' => $i,
							'YEAR(prod_trans_dtl_date)' => $year,
						);
						$this->admin->update_data('prod_trans_dtl_tbl', $update_prod_trans_dtl, $where);
					}
					$count++;
				}
				if($this->db->trans_status() === FALSE){
					$this->db->trans_rollback();
					$msg = '<div class="alert alert-danger">Error please try again!</div>';
				}else{
					$this->db->trans_commit();
					$msg = '<div class="alert alert-success"><strong>Production Transaction successfully updated.</strong></div>';
				}
				
				$this->session->set_flashdata('message', $msg);
				redirect('production/view-prod-trans/' . encode($prod_trans_id) .'/'. encode($bc_id) .'/'. encode($process_type_id));
			} else {
				echo 'Something wrong on bc';
			}
		} else{
			echo 'Something wrong';
		}
	}

	public function view_cost_sheet($prod_trans_id, $bc_id, $year, $process_type_id){
		$info = $this->_require_login();
		$data['prod_trans_id'] = $prod_trans_id;
		$data['process_type_id'] = $process_type_id;
		$prod_trans_id = decode($prod_trans_id);
		$data['bc_id'] = $bc_id;
		$data['year'] = $year;
		$process_type_id = decode($process_type_id);
		$bc_id = decode($bc_id);
		
		if($process_type_id == 5){
			$order_by = " z.order_base_sales, z.material_desc";
		} else {
			$order_by = " z.order_base, z.material_desc";
		}
		$sql = "SELECT
					*
				FROM
					(
						SELECT
							a.*, b.material_code,
							b.material_desc,
							b.material_id,
							d.component_type,
							d.order_base,
							d.order_base_sales,
							f.amount_type_name,
							g.unit_name,
							i.process_type_id,
							i.process_type_name,
							MONTH (a.prod_trans_dtl_date) AS trans_month
						FROM
							`prod_trans_dtl_tbl` `a`
						JOIN `material_tbl` `b` ON `a`.`article_id` = `b`.`material_id`
						AND `a`.`article_type_id` = 1
						JOIN `component_type_tbl` `d` ON `a`.`component_type_id` = `d`.`component_type_id`
						JOIN `amount_type_tbl` `f` ON `a`.`amount_type_id` = `f`.`amount_type_id`
						AND `a`.`prod_trans_dtl_status` <> 5
						AND `a`.`prod_trans_id` = ".$prod_trans_id."
						LEFT JOIN `unit_tbl` `g` ON `a`.`unit_id` = `g`.`unit_id`
						JOIN `prod_trans_tbl` `h` ON `a`.`prod_trans_id` = `h`.`prod_trans_id`
						JOIN `process_type_tbl` `i` ON `h`.`process_type_id` = `i`.`process_type_id`
						UNION ALL
							SELECT
								a.*, b.service_id,
								b.service_desc,
								b.service_code,
								d.component_type,
								d.order_base,
								d.order_base_sales,
								f.amount_type_name,
								NULL AS unit_name,
								i.process_type_id,
								i.process_type_name,
								MONTH (a.prod_trans_dtl_date) AS trans_month
							FROM
								`prod_trans_dtl_tbl` `a`
							JOIN `services_tbl` `b` ON `a`.`article_id` = `b`.`service_id`
							AND `a`.`article_type_id` = 2
							JOIN `component_type_tbl` `d` ON `a`.`component_type_id` = `d`.`component_type_id`
							JOIN `amount_type_tbl` `f` ON `a`.`amount_type_id` = `f`.`amount_type_id`
							AND `a`.`prod_trans_dtl_status` <> 5
							AND `a`.`prod_trans_id` = ".$prod_trans_id."
							JOIN `prod_trans_tbl` `h` ON `a`.`prod_trans_id` = `h`.`prod_trans_id`
							JOIN `process_type_tbl` `i` ON `h`.`process_type_id` = `i`.`process_type_id`
					) z
				ORDER BY".$order_by;
		$data['prod_trans'] = $this->admin->get_query($sql);

		$sql = "SELECT
					a.*, b.service_id,
					b.service_desc,
					b.service_code,
					d.component_type,
					d.order_base,
					f.amount_type_name,
					NULL AS unit_name,
					MONTH (a.prod_trans_dtl_date) AS trans_month
				FROM
					`prod_trans_dtl_tbl` `a`
				JOIN `services_tbl` `b` ON `a`.`article_id` = `b`.`service_id`
				AND `a`.`article_type_id` = 2
				JOIN `component_type_tbl` `d` ON `a`.`component_type_id` = `d`.`component_type_id`
				JOIN `amount_type_tbl` `f` ON `a`.`amount_type_id` = `f`.`amount_type_id`
				AND `a`.`prod_trans_dtl_status` <> 5
				AND `a`.`prod_trans_id` = ".$prod_trans_id." and a.component_type_id = 7";
		$data['config_rate'] = $this->admin->get_query($sql);


		foreach($data['prod_trans'] as $row){
			//GET THE RAWMATS FOR THE CLASSIFICATION PROCESS TYPE
			if($row->process_type_id == 2){
				
				if($row->component_type_id == 2){
					$prod_id = $row->article_id;
				}
				$data['fdc_details'] = get_basic_processing_details($prod_id, $bc_id, 1, $year);
			}
		}
		
		/*echo '<pre>';
		print_r($result);
		echo '</pre>';
		exit();*/

		$data['content'] = $this->load->view('prod/view_cost_sheet_content', $data , TRUE);
		$this->load->view('prod/templates', $data);
	}

	public function remove_config_prod_dtl (){
		/*echo "<pre>";
		print_r($_POST);
		echo "</pre>";
		exit();*/
		$info = $this->_require_login();
		$user_id = decode($info['user_id']);

		$config_prod_dtl_id = clean_data(decode($this->input->post('config_prod_dtl_id')));
		$material_desc = clean_data(decode($this->input->post('material_desc')));
		$config_prod_id = clean_data(decode($this->input->post('config_prod_id')));
		$trans_status = clean_data($this->input->post('trans_status'));
		$process_type_id = clean_data(decode($this->input->post('process_type_id')));
		
		if($trans_status == 'remove'){
			$config_prod_dtl_status = 5;
			$status = 'removed';
		}

		
		$this->db->trans_start();
		$update_data = array(
			'config_prod_dtl_status' => $config_prod_dtl_status,
			'modified_by' => $user_id,
			'modified_ts' => date_now()
		);
		$where = array(
			'config_prod_dtl_id' => $config_prod_dtl_id,
		);
		$this->admin->update_data('config_prod_dtl_tbl', $update_data, $where);

		if($this->db->trans_status() === FALSE){
			$this->db->trans_rollback();
			$msg = '<div class="alert alert-danger">Error please try again!</div>';
		}else{
			$this->db->trans_commit();
			$msg = '<div class="alert alert-success"><strong>Config Detail successfully '.$status.'.</strong></div>';
		}
		$this->session->set_flashdata('message', $msg);
		redirect('production/view-config-prod/' . encode($config_prod_id).'/'. encode($process_type_id));
	}

	public function cancel_prod_trans (){
		/*echo "<pre>";
		print_r($_POST);
		echo "</pre>";
		exit();*/
		$info = $this->_require_login();
		$user_id = decode($info['user_id']);

		$prod_trans_id = clean_data(decode($this->input->post('prod_trans_id')));
		$material_desc = clean_data(decode($this->input->post('material_desc')));
		$bc_id = clean_data($this->input->post('bc_id'));
		$trans_status = clean_data($this->input->post('trans_status'));
		
		if($trans_status == 'cancel'){
			$prod_trans_status = 5;
			$status = 'cancelled';
		}

		
		$this->db->trans_start();
		$update_data = array(
			'prod_trans_status' => $prod_trans_status,
			'modified_by' => $user_id,
			'modified_ts' => date_now()
		);
		$where = array(
			'prod_trans_id' => $prod_trans_id,
		);
		$this->admin->update_data('prod_trans_tbl', $update_data, $where);

		$update_data = array(
			'prod_trans_dtl_status' => $prod_trans_status
		);
		$where = array(
			'prod_trans_id' => $prod_trans_id,
		);
		$this->admin->update_data('prod_trans_dtl_tbl', $update_data, $where);

		if($this->db->trans_status() === FALSE){
			$this->db->trans_rollback();
			$msg = '<div class="alert alert-danger">Error please try again!</div>';
		}else{
			$this->db->trans_commit();
			$msg = '<div class="alert alert-success"><strong>Transaction successfully '.$status.'.</strong></div>';
		}
		$this->session->set_flashdata('message', $msg);
		redirect('production/prod-trans/' . $bc_id);
	}

	public function get_material(){
		$info = $this->_require_login();
		if($_SERVER['REQUEST_METHOD'] == 'POST'){
			$component_type_id = decode($this->input->post('id'));
			$check_component_type = $this->admin->check_data('component_type_tbl', array('component_type_id' => $component_type_id, 'component_type_status' => 1));
			if($check_component_type){
				if($component_type_id == 1 || $component_type_id == 2){
					$where = 'a.material_group_id NOT IN (14, 16,17)'; // RAW MATS OR FINISHED GOODS
				} else if($component_type_id == 4){
					$where = 'a.material_group_id IN (11, 12)'; //COST RECOVERY
				} else if($component_type_id == 5){
					$where = 'a.material_group_id = 16'; //PACKAGING
				} else if($component_type_id == 6){
					$where = 'a.material_group_id = 17'; //MARINADES
				} else if($component_type_id == 21){
					$where = ''; //COST OF SALES
				} else {
					$where = 'a.material_group_id = '.$component_type_id; //MARINADES
				}
				$material = $this->admin->get_data('material_tbl a', $where);

				$list_materials = '';
				foreach($material as $row):
					$list_materials .= '<option value="' . encode($row->material_id) . '">' . $row->material_desc . '</option>';
				endforeach;
				$data['result'] = 1;
				$data['info'] = $list_materials;
			}else{
				$data['result'] = 0;
			}
			echo json_encode($data);
		}
	}

	public function get_services(){
		$info = $this->_require_login();
		if($_SERVER['REQUEST_METHOD'] == 'POST'){
			$component_type_id = decode($this->input->post('id'));
			$check_component_type = $this->admin->check_data('component_type_tbl', array('component_type_id' => $component_type_id, 'component_type_status' => 1));
			if($check_component_type){
				if($component_type_id == 3){
					$where = 'a.service_group_id = 1'; //CONVERSION COST
				} else if($component_type_id == 7){
					$where = 'a.service_group_id = 2'; //CONFIG BASE
				} else if($component_type_id == 8){
					$where = 'a.service_group_id = 3'; //EXPENSES OF STORE
				} else if($component_type_id == 9){
					$where = 'a.service_group_id = 4'; //EXPENSES OF STORE
				} else if($component_type_id == 10){
					$where = 'a.service_group_id = 5'; //EXPENSES OF STORE
				}
				$services = $this->admin->get_data('services_tbl a', $where);
				
				$list_materials = '';
				foreach($services as $row):
					$list_materials .= '<option value="' . encode($row->service_id) . '">' . $row->service_desc . '</option>';
				endforeach;
				$data['result'] = 1;
				$data['info'] = $list_materials;
			}else{
				$data['result'] = 0;
			}
			echo json_encode($data);
		}
	}

	public function new_ext_prod_trans($id){

		$info = $this->_require_login();
		$data['bc_id'] = $id;
		$data['year'] = $this->_active_year();
 		$year = $this->_active_year();
		$bc_id = decode($id);
		$data['title'] = 'Production Cost';
		$sql = 'SELECT
					a.*,
					b.material_desc
				FROM
					ext_prod_trans_tbl a
				INNER JOIN material_tbl b ON a.material_id = b.material_id
				AND b.material_status = 1
				AND a.ext_prod_trans_status = 1
				and a.bc_id = '.$bc_id.'
				WHERE
					ext_prod_trans_id NOT IN (
						SELECT
							ext_prod_trans_id
						FROM
							ext_prod_trans_dtl_tbl
						WHERE
							ext_prod_trans_dtl_status = 1
						
						AND YEAR (trans_dtl_date) = '.$year.'
						GROUP BY
							ext_prod_trans_id
					)';
		
		$data['ext_prod_trans'] = $this->admin->get_query($sql);
		$data['content'] = $this->load->view('prod/new_ext_prod_content', $data , TRUE);
		$this->load->view('prod/templates', $data);
	}

	public function edit_ext_prod_trans($id, $bc_id){

		$info = $this->_require_login();
		$data['ext_prod_trans_id'] = $id;
		$data['bc_id'] = $bc_id;
		$bc_id = decode($bc_id);
		$ext_prod_trans_id = decode($id);
		$data['year'] = $this->_active_year();
		$join = array(
			'ext_prod_trans_dtl_tbl b' => 'a.ext_prod_trans_id = b.ext_prod_trans_id and a.ext_prod_trans_id ='.$ext_prod_trans_id,
			'material_tbl f' => 'a.material_id = f.material_id'
		);
		$data['ext_prod_trans'] = $this->admin->get_join('ext_prod_trans_tbl a', $join, FALSE,'f.material_desc ASC', 'a.ext_prod_trans_id');

		$data['title'] = 'Production Cost';
		$data['content'] = $this->load->view('prod/edit_ext_prod_trans_content', $data , TRUE);
		$this->load->view('prod/templates', $data);
	}

	public function add_ext_prod(){
		/*echo '<pre>';
		print_r($_POST);
		exit();*/
		$info = $this->_require_login();
		$user_id = decode($info['user_id']);
		if($_SERVER['REQUEST_METHOD'] == 'POST'){
			$bc_id = decode($this->input->post('bc_id'));
			$material = $this->input->post('material_id');
			if(!empty($bc_id)){
				$this->db->trans_start();
				foreach ($material as $key) {
					$material_id = clean_data(decode($key));

					$check_ext_prod = $this->admin->check_data('ext_prod_trans_tbl', array('material_id' =>  $material_id,	'bc_id'	=>	$bc_id,	'ext_prod_trans_status !='	=>	5));
					if($check_ext_prod == FALSE){
						$set = array(
							'material_id'	=>	$material_id,
							'bc_id'	=>	$bc_id,
							'trans_type_id'	=>	1,
							'ext_prod_trans_status'	=>	1,
							'created_by' => $user_id,
							'created_ts'	=>	date_now()
						);
						$result = $this->admin->insert_data('ext_prod_trans_tbl', $set, TRUE);

					} else {
						$msg = '<div class="alert alert-danger">Error! Material(s) already exist!</div>';
						$this->session->set_flashdata('message', $msg);
						redirect('production/prod-trans/' . encode($bc_id));
					}
				}

			} else {
				$msg = '<div class="alert alert-danger">Error please try again!</div>';
				$this->session->set_flashdata('message', $msg);
				redirect('production/prod-trans/' . encode($bc_id));
			}

			if($this->db->trans_status() === FALSE){
				$this->db->trans_rollback();
				$msg = '<div class="alert alert-danger">Error please try again!</div>';
			}else{
				$this->db->trans_commit();
				$msg = '<div class="alert alert-success"><strong>Material(s) successfully added. You can view it by Adding Transaction</strong></div>';
			}
			$this->session->set_flashdata('message', $msg);
			redirect('production/prod-trans/' . encode($bc_id));
		} else {
			redirect('production/prod-trans/' . encode($bc_id));
		}
	}

	public function add_ext_prod_trans_dtl(){
		/*echo '<pre>';
		print_r($_POST);
		echo '</pre>';
		exit();*/
		$info = $this->_require_login();
		$user_id = decode($info['user_id']);
		if($_SERVER['REQUEST_METHOD'] == 'POST'){
			$ext_prod_trans_id_array = $this->input->post('ext_prod_trans_id');
			$year = decode($this->input->post('year'));
			$bc_id = decode($this->input->post('bc_id'));
			$ave_wgt = clean_data($this->input->post('ave_wgt'));
			$cost = clean_data($this->input->post('cost'));

			if(count($ext_prod_trans_id_array) && !empty($year) && !empty($bc_id)){
				$this->db->trans_start();
				foreach ($ext_prod_trans_id_array as $key) {

					$ext_prod_trans_id = clean_data(decode($key));

					$check_ext_prod = $this->admin->check_data('ext_prod_trans_dtl_tbl', array('ext_prod_trans_id' =>  $ext_prod_trans_id,	'ext_prod_trans_dtl_status !='	=>	5));
					if($check_ext_prod == FALSE){
						$count = 0;
						for ($i=1; $i <= 12 ; $i++) {
							$month = date('M', strtotime($year.'-'.$i.'-01'));
							$date = $year . '-' .$i.'-01';
							$ave_wgt_amount = $ave_wgt[$month][$count];
							$cost_amount = $cost[$month][$count];

							$insert_trans_dtl = array(
								'ext_prod_trans_id' => $ext_prod_trans_id,
								'trans_dtl_date' =>$date,
								'ave_wgt' => $ave_wgt_amount,
								'cost' => $cost_amount,
								'ext_prod_trans_dtl_status' => 1
							);
							$this->admin->insert_data('ext_prod_trans_dtl_tbl', $insert_trans_dtl, true);
						}
						$count++;
					} else {
						$msg = '<div class="alert alert-danger">Error! Material(s) already exist!</div>';
						$this->session->set_flashdata('message', $msg);
						redirect('production/prod-trans/' . encode($bc_id));
					}
				}

			} else {
				$msg = '<div class="alert alert-danger">Error please try again!</div>';
				$this->session->set_flashdata('message', $msg);
				redirect('production/prod-trans/' . encode($bc_id));
			}

			if($this->db->trans_status() === FALSE){
				$this->db->trans_rollback();
				$msg = '<div class="alert alert-danger">Error please try again!</div>';
			}else{
				$this->db->trans_commit();
				$msg = '<div class="alert alert-success"><strong>Transaction successfully added.</strong></div>';
			}
			$this->session->set_flashdata('message', $msg);
			redirect('production/prod-trans/' . encode($bc_id));
		} else {
			redirect('production/prod-trans/' . encode($bc_id));
		}
	}

	public function update_ext_prod_trans(){
		$info = $this->_require_login();
		$user_id = decode($info['user_id']);
		if($_SERVER['REQUEST_METHOD'] == 'POST'){
			$bc_id = clean_data(decode($this->input->post('bc_id')));
			$year = clean_data($this->input->post('year'));
			
			if(!empty($bc_id) && !empty($year)){
				$id = clean_data($this->input->post('ext_prod_trans_id'));
				$ave_wgt = clean_data($this->input->post('ave_wgt'));
				$cost = clean_data($this->input->post('cost'));
				$count = 0;
				$this->db->trans_start();
				foreach($id as $row){

					$ext_prod_trans_id = decode($row);

					//update header transaction
					$update_prod_trans = array(
						'modified_by' => $user_id,
						'modified_ts' => date_now()
					);
					$where = array(
						'ext_prod_trans_id' => $ext_prod_trans_id
					);
					$this->admin->update_data('ext_prod_trans_tbl', $update_prod_trans, $where);

					for ($i=1; $i <= 12 ; $i++) {
						
						$ave_wgt_amount = $ave_wgt[$i][$count];
						$cost_amount = $cost[$i][$count];
						
						$update_trans_dtl = array(
							'ave_wgt' =>$ave_wgt_amount,
							'cost' =>$cost_amount
						);
						$where = array(
							'ext_prod_trans_id' => $ext_prod_trans_id,
							'MONTH(trans_dtl_date)' => $i
						);
						$this->admin->update_data('ext_prod_trans_dtl_tbl', $update_trans_dtl, $where);
					}
					$count++;
				}
				if($this->db->trans_status() === FALSE){
					$this->db->trans_rollback();
					$msg = '<div class="alert alert-danger">Error please try again!</div>';
				}else{
					$this->db->trans_commit();
					$msg = '<div class="alert alert-success"><strong>Transaction successfully updated.</strong></div>';
				}
				
				$this->session->set_flashdata('message', $msg);
				redirect('production/prod-trans/'.encode($bc_id));
			} else {
				echo 'Something wrong on bc';
			}
		} else{
			echo 'Something wrong';
		}
	}

	public function cancel_ext_prod_trans(){
		/*echo "<pre>";
		print_r($_POST);
		echo "</pre>";
		exit();*/
		$info = $this->_require_login();
		$user_id = decode($info['user_id']);

		$ext_prod_trans_id = clean_data(decode($this->input->post('ext_prod_trans_id')));
		$bc_id = clean_data($this->input->post('bc_id'));
		$trans_status = clean_data($this->input->post('trans_status'));
		
		if($trans_status == 'cancel'){
			$ext_prod_trans_status = 5;
			$status = 'cancelled';
		}

		
		$this->db->trans_start();
		$update_data = array(
			'ext_prod_trans_status' => $ext_prod_trans_status,
			'modified_by' => $user_id,
			'modified_ts' => date_now()
		);
		$where = array(
			'ext_prod_trans_id' => $ext_prod_trans_id,
		);
		$this->admin->update_data('ext_prod_trans_tbl', $update_data, $where);

		$update_data = array(
			'ext_prod_trans_dtl_status' => $ext_prod_trans_status
		);
		$where = array(
			'ext_prod_trans_id' => $ext_prod_trans_id,
		);
		$this->admin->update_data('ext_prod_trans_dtl_tbl', $update_data, $where);

		if($this->db->trans_status() === FALSE){
			$this->db->trans_rollback();
			$msg = '<div class="alert alert-danger">Error please try again!</div>';
		}else{
			$this->db->trans_commit();
			$msg = '<div class="alert alert-success"><strong>Transaction successfully '.$status.'.</strong></div>';
		}
		$this->session->set_flashdata('message', $msg);
		redirect('productionuction/prod-trans/' . $bc_id);
	}
}