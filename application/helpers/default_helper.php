<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

if ( ! function_exists('encode')){

	function encode($token){
		$cipher_method = 'aes-128-ctr';
	  	$enc_key = 'jonel';
	  	$enc_iv = openssl_random_pseudo_bytes(openssl_cipher_iv_length($cipher_method));
	  	$crypted_token = openssl_encrypt($token, $cipher_method, $enc_key, 0, $enc_iv) . "::" . bin2hex($enc_iv);
	  	unset($token, $cipher_method, $enc_key, $enc_iv);
		
		$encode_id= strtr(
		    $crypted_token,
		    array(
	            '+' => '.',
	            '=' => '-',
		        '/' => '~'
		    )
	    );

	   	return $encode_id;
	}

	function decode($token){
		if(!empty($token)){
			$enc_key = 'jonel';
			$decode_id= strtr(
			    $token,
			    array(
		            '.' => '+',
		            '-' => '=',
			        '~' => '/'
			    )
		    );

			if(count(explode("::", $decode_id)) > 1){
				list($crypted_token, $enc_iv) = explode("::", $decode_id);
			}else{
				list($crypted_token, $enc_iv) = array($decode_id, '');
			}

		  	$crypted_token = $crypted_token;
			$cipher_method = 'aes-128-ctr';
			$token = openssl_decrypt($crypted_token, $cipher_method, $enc_key, 0, hex2bin($enc_iv));
			unset($crypted_token, $cipher_method, $enc_key, $enc_iv);
		}
		
		return $token;
	}

	function clean_data($data){
		$instanceName =& get_instance();
		$instanceName->load->helper('security');
		$clean = $instanceName->security->xss_clean($instanceName->db->escape_str($data));
		return $clean;
	}

	function generate_random($count){
		$random = substr(str_shuffle("0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ"), 0, $count);
		return $random;
	}

	function create_id($format, $count){
		
		if($count > 0 && $count < 10){
			$id = $format . '00000' . $count;
		}elseif($count >= 10 && $count <= 99){
			$id = $format . '0000' . $count;
		}elseif($count >= 100 && $count <= 999){
			$id = $format . '000' . $count;
		}elseif($count >= 1000 && $count <= 9999){
			$id = $format . '00' . $count;
		}elseif($count >= 10000 && $count <= 99999){
			$id = $format . '0' . $count;
		}else{
			$id = $format . $count;
		}

		return $id;
	}

	function date_now(){
		$date = date('Y-m-d H:i:s');
		return $date;
	}

	function check_num($num){
		if(!is_null($num) || !empty($num)){
			return floatval($num);
		}else{
			return 0;
		}
	}

	function check_null($num){
		if(!is_null($num) || $num == 0){
			return $num;
		}else{
			return null;
		}
	}

	function check_array($var){
			return $var;
		if(isset($var)){
		}else{
			return 0;
		}
	}

	function convert_num($value){
		if($value >= 0){
			if($value >= 1000000000){
				$value = $value/1000000000;
				$value = number_format($value, 2) . 'B';
			}else if($value >= 1000000 && $value < 1000000000){
				$value = $value/1000000;
				$value = number_format($value, 2) . 'M';
			}else if($value > 1000 && $value < 1000000){
				$value = $value/1000;
				$value = number_format($value) . 'K';
			}else if($value > 99 && $value < 999){
				$value = $value/1000;
				$value = number_format($value, 2) . 'K';
			}else{
				 $value = '0';
			}
		}else{
			$value = $value * -1;
			if($value >= 1000000000){
				$value = ($value/1000000000) * -1;
				$value = number_format($value, 2) . ' B';
			}else if($value >= 1000000 && $value < 1000000000){
				$value = ($value/1000000) * -1;
				$value = number_format($value, 2) . ' M';
			}else if($value > 1000 && $value < 1000000){
				$value = ($value/1000) * -1;
				$value = number_format($value) . ' K';
			}else if($value > 99 && $value < 999){
				$value = ($value/1000) * -1;
				$value = number_format($value, 2) . ' K';
			}else{
				 $value = '0';
			}
		}
		return $value;
	}

	function check_count($value){
		if(empty($value)){
			$value = [];
		}

		return $value;
	}

	function check_zero($value){
		if(empty($value) || $value == 0){
			$value = '';
		}

		return $value;
	}

	//my code start here
	
	function get_data($tbl, $where=null, $row=FALSE, $select=null, $order=FALSE, $string = false){
		$ci =& get_instance();
		$ci->load->database();

		if($where != null){
			$ci->db->where($where);
		}

		if($select != null){
			$ci->db->select($select);
		}

		if($order != FALSE){
			$ci->db->order_by($order);
		}

		$query = $ci->db->get($tbl);

		$result_row = $query->num_rows();
		if($result_row > 0){
			if($row == TRUE){
				$result_data = $query->row();	
			}else{
				$result_data= $query->result();
			}
		} else {
			$result = array(
				'broiler_budget_qty' => 0,
				'cost' => 0,
				'rate' => 0,
				'ave_wgt' => 0
			);
			$result_data = (object) $result;
		}
		
		return $result_data;
	}

	function get_bulk_yearly_data($tbl, $where=null, $row=FALSE, $select=null, $order=FALSE, $string = false){
		$ci =& get_instance();
		$ci->load->database();

		if($where != null){
			$ci->db->where($where);
		}

		if($select != null){
			$ci->db->select($select);
		}

		if($order != FALSE){
			$ci->db->order_by($order);
		}

		$query = $ci->db->get($tbl);

		$result_row = $query->num_rows();
		if($result_row > 0){
			
			$result_data= $query->result();
		}

		$result_array = array();
		if(!empty($result_data)){
			$i=1;
			foreach($result_data as $r){
				$result_array[$i] = $r->$select;
				$i++;
			}
		}
		
		return $result_array;
	}

	function update_data($tbl, $set, $where){
		$ci =& get_instance();
		$ci->load->database();

		$ci->db->trans_start();

		$ci->db->set($set);
		$ci->db->where($where);
		$ci->db->update($tbl);

		//return $this->db->last_query();
		if($ci->db->trans_status() === FALSE){
			$ci->db->trans_rollback();
			return FALSE;
		}else{
			$ci->db->trans_commit();
			return TRUE;
		}
	}

	function dec_places(){
		return 3;
	}

	function dec_places_dis(){
		return 2;
	}

	function amount_type($qty, $id){
		if($id == 1){
			$qty = $qty / 100;
		} else if($id == 2){
			$qty = $qty;
		} else {
			$qty = 0;
		}
		return $qty;
	}

	function get_broiler_budget_qty($broiler_subgroup_id, $bc_id, $trans_year, $doctype){
		$ci =& get_instance();
		$ci->load->model('admin_model', 'admin');

		$doctype = decode($doctype);
		if($doctype == 'trans'){
			$status = ' AND a.broiler_trans_status != 5';
		} else if($doctype == 'report') {
			$status = ' AND a.broiler_trans_status = 4';
		} else {
			$status = ' AND a.broiler_trans_status = 1';
		}

		$join_broiler_trans = array(
			'broiler_trans_dtl_tbl b' => 'a.broiler_trans_id = b.broiler_trans_id AND a.broiler_subgroup_id = '.$broiler_subgroup_id.$status.' and a.bc_id = '.$bc_id.' and YEAR(b.broiler_trans_date) = '.$trans_year
		);
		$check_broiler_trans = $ci->admin->get_join('broiler_trans_tbl a', $join_broiler_trans, false, FALSE, FALSE, 'b.broiler_budget_qty, b.amount_type_id, MONTH(b.broiler_trans_date)', false);
		return $check_broiler_trans;

		if($check_broiler_trans){
			return $check_broiler_trans;
		} else {
			return false;
		}
	}

	function get_broiler_cost_detail($broiler_group_id, $bc_id, $trans_year, $trans_month,  $doctype){
		$ci =& get_instance();
		$ci->load->model('admin_model', 'admin');

		$doctype = decode($doctype);
		if($doctype == 'trans'){
			$status = ' AND a.broiler_trans_status != 5';
		} else if($doctype == 'report') {
			$status = ' AND a.broiler_trans_status = 4';
		} else {
			$status = ' AND a.broiler_trans_status = 1';
		}

		$check_broiler_trans = $ci->admin->check_data('broiler_cost_summary_tbl', array('bc_id'	=> decode($bc_id),	'YEAR(trans_date)' =>	decode($trans_year), 'MONTH(trans_date)' => $trans_month, 'broiler_group_id' => $broiler_group_id), true);

		if($check_broiler_trans['result']){
			return $check_broiler_trans['info']->trans_qty;
		} else {
			return 0;
		}
	}

	function get_broiler_cost($bc_id, $trans_year, $trans_month, $doctype){
		$ci =& get_instance();
		$ci->load->model('admin_model', 'admin');

		$doctype = decode($doctype);
		if($doctype == 'trans'){
			$status = ' AND a.broiler_trans_status != 5';
		} else if($doctype == 'report') {
			$status = ' AND a.broiler_trans_status = 4';
		} else {
			$status = ' AND a.broiler_trans_status = 1';
		}

		if(decode($bc_id) == -1){
			$sql = "SELECT
				AVG(trans_qty) AS trans_qty
			FROM
				(
					SELECT
						SUM(trans_qty) AS trans_qty
					FROM
						`broiler_cost_summary_tbl` a
					INNER JOIN broiler_group_tbl b ON a.broiler_group_id = b.broiler_group_id
					WHERE
						YEAR (trans_date) = '".decode($trans_year)."'
					AND MONTH (trans_date) = ".$trans_month."
					GROUP BY
						b.broiler_group_status,
						a.bc_id
				) a";
			$check_broiler_trans = $ci->admin->get_query($sql, TRUE);

			if(!empty($check_broiler_trans)){
				$result = $check_broiler_trans->trans_qty;
				return round($result, dec_places());
			} else {
				return 0;
			}
		} else {
			$check_broiler_trans = $ci->admin->check_data('broiler_cost_summary_tbl', 'bc_id IN  (' . decode($bc_id) . ') AND YEAR(trans_date) = ' . decode($trans_year) . ' AND MONTH(trans_date) = ' . $trans_month, true, 'SUM(trans_qty) as trans_qty');
			
			if($check_broiler_trans['result']){
				$result = $check_broiler_trans['info']->trans_qty;
				return round($result, dec_places());
			} else {
				return 0;
			}
		}


	}
	
	function get_broiler_config_qty($broiler_subgroup_name, $bc_id, $trans_year){
		$ci =& get_instance();
		$ci->load->model('admin_model', 'admin');

		$check_broiler_config = $ci->admin->get_data('broiler_config_tbl', array('broiler_subgroup_name' => $broiler_subgroup_name, 'bc_id'	=>	$bc_id, 'YEAR(config_date)'	=>	$trans_year,	'broiler_config_status'	=>	1), FALSE, 'config_qty, amount_type_id');
		if($check_broiler_config){
			return $check_broiler_config;
		} else {
			return false;
		}
	}

	function get_broiler_config_qty_bulk($broiler_subgroup_name, $bc_id, $trans_year){
		$ci =& get_instance();
		$ci->load->model('admin_model', 'admin');

		$sql = "SELECT
					a.broiler_subgroup_name,
					MONTH (a.config_date) AS `month`,
					a.config_qty,
					a.amount_type_id
				FROM
					broiler_config_tbl a
				WHERE
					broiler_subgroup_name LIKE '%".$broiler_subgroup_name."%'
				AND a.bc_id = ".$bc_id."
				AND YEAR (a.config_date) = ".$trans_year."
				AND a.broiler_config_status = 1";
		
		$check_broiler_config = $ci->admin->get_query($sql);
		/*echo $check_broiler_config;
		exit();*/
		if($check_broiler_config){
			return $check_broiler_config;
		} else {
			return false;
		}
	}

	function get_live_sales($bc_id, $year){
		$ci =& get_instance();
		$ci->load->model('admin_model', 'admin');

		$sql = "SELECT
				IFNULL(SUM(c.sales_det_qty), 0) as live_sales_qty, 

				IFNULL((
				SELECT y.sales_live_alw_det_value
				FROM sales_live_alw_tbl x, sales_live_alw_detail_tbl y
				WHERE x.sales_live_alw_id = y.sales_live_alw_id AND x.sales_live_alw_status = 1 AND y.sales_live_alw_det_status = 1
				AND x.bc_id = f.bc_id AND y.sales_live_alw_det_date = c.sales_det_date),0
				) as alw_live

				FROM sales_tbl a, sales_item_tbl b, sales_details_tbl c, material_tbl d, outlet_tbl e, bc_tbl f
				WHERE a.sales_id = b.sales_id AND b.sales_item_id = c.sales_item_id AND b.material_id = d.material_id AND a.outlet_id = e.outlet_id AND e.bc_id = f.bc_id AND a.sales_status = 1 AND b.sales_item_status = 1 AND c.sales_det_status = 1 AND YEAR(c.sales_det_date) = ".$year." AND f.bc_id = ".$bc_id." AND d.material_desc = 'LIVE BROILER' GROUP BY YEAR(c.sales_det_date), MONTH(c.sales_det_date) ORDER BY MONTH(c.sales_det_date)";

		return $ci->admin->get_query($sql);
	}

	

	function harvested_heads($bc_id, $trans_year, $doctype){

		$doc_placement = get_broiler_budget_qty(2, decode($bc_id), decode($trans_year), $doctype) != false ? get_broiler_budget_qty(2, decode($bc_id), decode($trans_year), $doctype) : 0;
		$pfd = get_broiler_budget_qty(5, decode($bc_id), decode($trans_year), $doctype) != false ? get_broiler_budget_qty(5, decode($bc_id), decode($trans_year), $doctype) : 0;
		$hr = get_broiler_budget_qty(3, decode($bc_id), decode($trans_year), $doctype) != false ? get_broiler_budget_qty(3, decode($bc_id), decode($trans_year), $doctype) : 0;
		
		if($doc_placement && $pfd && $hr){
			$i = 1;
			foreach ($doc_placement as $row) {
				$doc_placement_val[$i] = $row->broiler_budget_qty;
				$i++;
			}
			$i = 1;
			foreach ($pfd as $row) {
				$pfd_val[$i] = $row->broiler_budget_qty;
				$pfd_val[$i] = $pfd_val[$i]/100;
				$pfd_val[$i] = 1 - $pfd_val[$i];
				$i++;
			}
			$i = 1;
			foreach ($hr as $row) {
				$hr_val[$i] = $row->broiler_budget_qty;
				$hr_val[$i] = $hr_val[$i]/100;
				$i++;
			}
			for($month = 1; $month <= 12; $month++){
				$result[$month] = $doc_placement_val[$month] * $pfd_val[$month];
				$result[$month] = $result[$month] * $hr_val[$month];
				$result[$month] = round($result[$month], 3);


			}
			return $result;
		} else {
			return $result=0;
		}

	}

	function harvested_kilo($bc_id, $trans_year, $doctype){

		$alw = get_broiler_budget_qty(4, decode($bc_id), decode($trans_year), $doctype) != false ? get_broiler_budget_qty(4, decode($bc_id), decode($trans_year), $doctype) : 0;
		
		
		$i = 1;
		if(!empty($alw)){
			foreach ($alw as $row) {
				$alw_val[$i] = $row->broiler_budget_qty;
				$i++;
			}
			$harvested_heads = harvested_heads($bc_id, $trans_year, $doctype);
			for($month = 1; $month <= 12; $month++){
				$result[$month] = $alw_val[$month] * $harvested_heads[$month];
				$result[$month] = round($result[$month], 3);

				$broiler_line_item_id = 6;
				$ci =& get_instance();
				$ci->load->model('admin_model', 'admin');
				$check = $ci->admin->check_data('broiler_amount_summary_tbl', array('bc_id'	=>	decode($bc_id),	'broiler_line_item_id'	=> $broiler_line_item_id, 'YEAR(trans_date)' => decode($trans_year), 'MONTH(trans_date)' => $month, 'trans_type_id' =>	1), true);

				if($check['result']){
					$set = array('trans_qty' =>	round($result[$month], dec_places()), 'trans_type_id' => 1);
					$where = array('bc_id'	=>	decode($bc_id),	'broiler_line_item_id'	=> $broiler_line_item_id, 'YEAR(trans_date)' => decode($trans_year), 'MONTH(trans_date)' => $month, 'trans_type_id' =>	1);
					$ci->admin->update_data('broiler_amount_summary_tbl', $set, $where);
				} else {
					$set = array('trans_qty' =>	round($result[$month], dec_places()), 'bc_id'	=>	decode($bc_id),	'broiler_line_item_id'	=> $broiler_line_item_id, 'trans_date' => decode($trans_year).'-'.$month.'-01', 'trans_type_id' => 1);
					$ci->admin->insert_data('broiler_amount_summary_tbl', $set);
				}
			}
			return $result;
		} else {
			return $result = 0;
		}
	}

	function doc_cost_amount($bc_id, $trans_year, $doctype){
		$doc_input = get_broiler_budget_qty(1, decode($bc_id), decode($trans_year), $doctype) != false ? get_broiler_budget_qty(1, decode($bc_id), decode($trans_year), $doctype) : 0;
		$doc_placement = get_broiler_budget_qty(2, decode($bc_id), decode($trans_year), $doctype) != false ? get_broiler_budget_qty(2, decode($bc_id), decode($trans_year), $doctype) : 0;
		
		if($doc_input && $doc_placement){
			$i = 1;
			foreach ($doc_placement as $row) {
				$doc_placement_val[$i] = $row->broiler_budget_qty;
				$i++;
			}

			$i = 1;
			foreach ($doc_input as $row) {
				$doc_input_val[$i] = $row->broiler_budget_qty;
				$i++;
			}
			for($month = 1; $month <= 12; $month++){
				$result[$month] = $doc_input_val[$month] * $doc_placement_val[$month];
				$result[$month] = round($result[$month], 3);

				$broiler_line_item_id = 1;
				$ci =& get_instance();
				$ci->load->model('admin_model', 'admin');
				$check = $ci->admin->check_data('broiler_amount_summary_tbl', array('bc_id'	=>	decode($bc_id),	'broiler_line_item_id'	=> $broiler_line_item_id, 'YEAR(trans_date)' => decode($trans_year), 'MONTH(trans_date)' => $month, 'trans_type_id' =>	1), true);
				if($check['result']){
					$set = array('trans_qty' =>	round($result[$month], dec_places()), 'trans_type_id' => 1);
					$where = array('bc_id'	=>	decode($bc_id),	'broiler_line_item_id'	=> $broiler_line_item_id, 'YEAR(trans_date)' => decode($trans_year), 'MONTH(trans_date)' => $month, 'trans_type_id' =>	1);
					$ci->admin->update_data('broiler_amount_summary_tbl', $set, $where);
				} else {
					$set = array('trans_qty' =>	round($result[$month], dec_places()), 'bc_id'	=>	decode($bc_id),	'broiler_line_item_id'	=> $broiler_line_item_id, 'trans_date' => decode($trans_year).'-'.$month.'-01', 'trans_type_id' => 1);
					$ci->admin->insert_data('broiler_amount_summary_tbl', $set);
				}
			}
			
			return $result;
		} else {
			return $result = 0;
		}
			
	}

	function doc ($bc_id, $trans_year, $doctype){
		$ci =& get_instance();
		$ci->load->model('admin_model', 'admin');

		$doc_cost_amount = doc_cost_amount($bc_id, $trans_year, $doctype);
		$harvested_kilo = harvested_kilo($bc_id, $trans_year, $doctype);
		$harvested_heads = harvested_heads($bc_id, $trans_year, $doctype);

		for ($broiler_summary_item_id = 33; $broiler_summary_item_id <= 36 ; $broiler_summary_item_id++) { 
			$set = array('broiler_summary_status' =>	2);
			$where = array('bc_id'	=>	decode($bc_id),	'broiler_summary_item_id'	=> $broiler_summary_item_id, 'YEAR(trans_date)' => decode($trans_year), 'trans_type_id' =>	1);
			$ci->admin->update_data('broiler_summary_tbl', $set, $where);	
		}

		for($month = 1; $month <= 12; $month++){
			if($harvested_kilo[$month] == 0){
				$result[$month] = 0;
			} else {
				
				$result[$month] = $doc_cost_amount[$month]/$harvested_kilo[$month];
				$result[$month] = round($result[$month], 3);
				

				//return round($result, dec_places());
			}
			$broiler_group_id = 1;
			$check = $ci->admin->check_data('broiler_cost_summary_tbl', array('bc_id'	=>	decode($bc_id),	'broiler_group_id'	=> $broiler_group_id, 'YEAR(trans_date)' => decode($trans_year), 'MONTH(trans_date)' => $month), true);
			if($check['result']){
				$set = array('trans_qty' =>	round($result[$month], dec_places()), 'trans_type_id' => 1);
				$where = array('bc_id'	=>	decode($bc_id),	'broiler_group_id'	=> $broiler_group_id, 'YEAR(trans_date)' => decode($trans_year), 'MONTH(trans_date)' => $month);
				$ci->admin->update_data('broiler_cost_summary_tbl', $set, $where);
			} else {
				$set = array('trans_qty' =>	round($result[$month], dec_places()), 'bc_id'	=>	decode($bc_id),	'broiler_group_id'	=> $broiler_group_id, 'trans_date' => decode($trans_year).'-'.$month.'-01', 'trans_type_id' => 1);
				$ci->admin->insert_data('broiler_cost_summary_tbl', $set);
			}

			$broiler_summary_item_id = 33; //HARVESTED HEADS
			$set = array('trans_qty' =>	round($harvested_heads[$month], dec_places()), 'bc_id'	=>	decode($bc_id),	'broiler_summary_item_id'	=> $broiler_summary_item_id, 'trans_date' => decode($trans_year).'-'.$month.'-01', 'trans_type_id' => 1, 'broiler_summary_status'	=> 1);
			$ci->admin->insert_data('broiler_summary_tbl', $set);

			$broiler_summary_item_id = 34; //HARVESTED KILO
			$set = array('trans_qty' =>	round($harvested_kilo[$month], dec_places()), 'bc_id'	=>	decode($bc_id),	'broiler_summary_item_id'	=> $broiler_summary_item_id, 'trans_date' => decode($trans_year).'-'.$month.'-01', 'trans_type_id' => 1, 'broiler_summary_status'	=> 1);
			$ci->admin->insert_data('broiler_summary_tbl', $set);

			$broiler_summary_item_id = 35; //DOC COST AMOUNT
			$set = array('trans_qty' =>	round($doc_cost_amount[$month], dec_places()), 'bc_id'	=>	decode($bc_id),	'broiler_summary_item_id'	=> $broiler_summary_item_id, 'trans_date' => decode($trans_year).'-'.$month.'-01', 'trans_type_id' => 1, 'broiler_summary_status'	=> 1);
			$ci->admin->insert_data('broiler_summary_tbl', $set);

			$broiler_summary_item_id = 36; //DOC
			$set = array('trans_qty' =>	round($result[$month], dec_places()), 'bc_id'	=>	decode($bc_id),	'broiler_summary_item_id'	=> $broiler_summary_item_id, 'trans_date' => decode($trans_year).'-'.$month.'-01', 'trans_type_id' => 1, 'broiler_summary_status'	=> 1);
			$ci->admin->insert_data('broiler_summary_tbl', $set);
		}
		return $result;
	}

	function vaccines_amount($bc_id, $trans_year, $doctype){
		$doc_placement = get_broiler_budget_qty(2, decode($bc_id), decode($trans_year), $doctype) != false ? get_broiler_budget_qty(2, decode($bc_id), decode($trans_year), $doctype) : 0;

		$vaccines_input = get_broiler_budget_qty(26, decode($bc_id), decode($trans_year), $doctype) != false ? get_broiler_budget_qty(26, decode($bc_id), decode($trans_year), $doctype) : 0;

		$i = 1;
		if($doc_placement && $vaccines_input){
			foreach ($doc_placement as $row) {
				$doc_placement_val[$i] = $row->broiler_budget_qty;
				$i++;
			}

			$i = 1;
			foreach ($vaccines_input as $row) {
				$vaccines_input_val[$i] = $row->broiler_budget_qty;
				$i++;
			}
			
			for($month = 1; $month <= 12; $month++){
				$result[$month] = $vaccines_input_val[$month] * $doc_placement_val[$month];
				$result[$month] = round($result[$month], 3);

				$broiler_line_item_id = 2;
				$ci =& get_instance();
				$ci->load->model('admin_model', 'admin');
				$check = $ci->admin->check_data('broiler_amount_summary_tbl', array('bc_id'	=>	decode($bc_id),	'broiler_line_item_id'	=> $broiler_line_item_id, 'YEAR(trans_date)' => decode($trans_year), 'MONTH(trans_date)' => $month, 'trans_type_id' =>	1), true);
				if($check['result']){
					$set = array('trans_qty' =>	round($result[$month], dec_places()), 'trans_type_id' => 1);
					$where = array('bc_id'	=>	decode($bc_id),	'broiler_line_item_id'	=> $broiler_line_item_id, 'YEAR(trans_date)' => decode($trans_year), 'MONTH(trans_date)' => $month, 'trans_type_id' =>	1);
					$ci->admin->update_data('broiler_amount_summary_tbl', $set, $where);
				} else {
					$set = array('trans_qty' =>	round($result[$month], dec_places()), 'bc_id'	=>	decode($bc_id),	'broiler_line_item_id'	=> $broiler_line_item_id, 'trans_date' => decode($trans_year).'-'.$month.'-01', 'trans_type_id' => 1);
					$ci->admin->insert_data('broiler_amount_summary_tbl', $set);
				}
			}
			return $result;
		} else {
			return $result = 0;
		}
	}

	function vaccines($bc_id, $trans_year, $doctype){
		$ci =& get_instance();
		$ci->load->model('admin_model', 'admin');

		$vaccine_amount = vaccines_amount($bc_id, $trans_year, $doctype);
		$harvested_kilo = harvested_kilo($bc_id, $trans_year, $doctype);

		for ($broiler_summary_item_id = 45; $broiler_summary_item_id <= 46 ; $broiler_summary_item_id++) { 
			$set = array('broiler_summary_status' =>	2);
			$where = array('bc_id'	=>	decode($bc_id),	'broiler_summary_item_id'	=> $broiler_summary_item_id, 'YEAR(trans_date)' => decode($trans_year), 'trans_type_id' =>	1);
			$ci->admin->update_data('broiler_summary_tbl', $set, $where);	
		}

		for($month = 1; $month <= 12; $month++){
			if($harvested_kilo[$month] == 0){
				$result[$month] = 0;
			} else {
				
				$result[$month] = $vaccine_amount[$month]/$harvested_kilo[$month];
				$result[$month] = round($result[$month], 3);
				

				//return round($result, dec_places());
			}
			$broiler_group_id = 4;
				
			$check = $ci->admin->check_data('broiler_cost_summary_tbl', array('bc_id'	=>	decode($bc_id),	'broiler_group_id'	=> $broiler_group_id, 'YEAR(trans_date)' => decode($trans_year), 'MONTH(trans_date)' => $month, 'trans_type_id' =>	1), true);
			if($check['result']){
				$set = array('trans_qty' =>	round($result[$month], dec_places()), 'trans_type_id' => 1);
				$where = array('bc_id'	=>	decode($bc_id),	'broiler_group_id'	=> $broiler_group_id, 'YEAR(trans_date)' => decode($trans_year), 'MONTH(trans_date)' => $month, 'trans_type_id' =>	1);
				$ci->admin->update_data('broiler_cost_summary_tbl', $set, $where);
			} else {
				$set = array('trans_qty' =>	round($result[$month], dec_places()), 'bc_id'	=>	decode($bc_id),	'broiler_group_id'	=> $broiler_group_id, 'trans_date' => decode($trans_year).'-'.$month.'-01', 'trans_type_id' => 1);
				$ci->admin->insert_data('broiler_cost_summary_tbl', $set);
			}

			$broiler_summary_item_id = 45; //VACCINES AMOUNT
			$set = array('trans_qty' =>	round($vaccine_amount[$month], dec_places()), 'bc_id'	=>	decode($bc_id),	'broiler_summary_item_id'	=> $broiler_summary_item_id, 'trans_date' => decode($trans_year).'-'.$month.'-01', 'trans_type_id' => 1, 'broiler_summary_status'	=> 1);
			$ci->admin->insert_data('broiler_summary_tbl', $set);

			$broiler_summary_item_id = 46; //VACCINES
			$set = array('trans_qty' =>	round($result[$month], dec_places()), 'bc_id'	=>	decode($bc_id),	'broiler_summary_item_id'	=> $broiler_summary_item_id, 'trans_date' => decode($trans_year).'-'.$month.'-01', 'trans_type_id' => 1, 'broiler_summary_status'	=> 1);
			$ci->admin->insert_data('broiler_summary_tbl', $set);
		}
		return $result;
	}

	function medicine_amount($bc_id, $trans_year,  $doctype){
		$doc_placement = get_broiler_budget_qty(2, decode($bc_id), decode($trans_year), $doctype) != false ? get_broiler_budget_qty(2, decode($bc_id), decode($trans_year), $doctype) : 0;

		$medicine_input = get_broiler_budget_qty(27, decode($bc_id), decode($trans_year),  $doctype) != false ? get_broiler_budget_qty(27, decode($bc_id), decode($trans_year),  $doctype) : 0;

		if($doc_placement && $medicine_input){

			$i = 1;
			foreach ($doc_placement as $row) {
				$doc_placement_val[$i] = $row->broiler_budget_qty;
				$i++;
			}

			$i = 1;
			foreach ($medicine_input as $row) {
				$medicine_input_val[$i] = $row->broiler_budget_qty;
				$i++;
			}
			for($month = 1; $month <= 12; $month++){
				$result[$month] = $medicine_input_val[$month] * $doc_placement_val[$month];
				$result[$month] = round($result[$month], 3);
			}
			
			return $result;
		} else {
			return $result = 0;
		}
	}

	function disinfectant_amount($bc_id, $trans_year, $doctype){
		$doc_placement = get_broiler_budget_qty(2, decode($bc_id), decode($trans_year), $doctype) != false ? get_broiler_budget_qty(2, decode($bc_id), decode($trans_year), $doctype) : 0;

		$disinfectant_input = get_broiler_budget_qty(28, decode($bc_id), decode($trans_year), $doctype) != false ? get_broiler_budget_qty(28, decode($bc_id), decode($trans_year), $doctype) : 0;
		$medicine_amount = medicine_amount($bc_id, $trans_year, $doctype);

		if($doc_placement && $disinfectant_input){
			$i = 1;
			foreach ($doc_placement as $row) {
				$doc_placement_val[$i] = $row->broiler_budget_qty;
				$i++;
			}

			$i = 1;
			foreach ($disinfectant_input as $row) {
				$disinfectant_input_val[$i] = $row->broiler_budget_qty;
				$i++;
			}
			for($month = 1; $month <= 12; $month++){
				$result[$month] = $disinfectant_input_val[$month] * $doc_placement_val[$month];
				$result[$month] = round($result[$month], 3);

				$total[$month] = $result[$month] + $medicine_amount[$month];
				$broiler_line_item_id = 3;
				$ci =& get_instance();
				$ci->load->model('admin_model', 'admin');
				$check = $ci->admin->check_data('broiler_amount_summary_tbl', array('bc_id'	=>	decode($bc_id),	'broiler_line_item_id'	=> $broiler_line_item_id, 'YEAR(trans_date)' => decode($trans_year), 'MONTH(trans_date)' => $month, 'trans_type_id' =>	1), true);
				if($check['result']){
					$set = array('trans_qty' =>	round($total[$month], dec_places()), 'trans_type_id' => 1);
					$where = array('bc_id'	=>	decode($bc_id),	'broiler_line_item_id'	=> $broiler_line_item_id, 'YEAR(trans_date)' => decode($trans_year), 'MONTH(trans_date)' => $month, 'trans_type_id' =>	1);
					$ci->admin->update_data('broiler_amount_summary_tbl', $set, $where);
				} else {
					$set = array('trans_qty' =>	round($total[$month], dec_places()), 'bc_id'	=>	decode($bc_id),	'broiler_line_item_id'	=> $broiler_line_item_id, 'trans_date' => decode($trans_year).'-'.$month.'-01', 'trans_type_id' => 1);
					$ci->admin->insert_data('broiler_amount_summary_tbl', $set);
				}
			}
			
			return $result;
		} else {
			return $result = 0;
		}
	}

	function medicines ($bc_id, $trans_year, $doctype){
		$ci =& get_instance();
		$ci->load->model('admin_model', 'admin');

		$disinfectant_amount = disinfectant_amount($bc_id, $trans_year, $doctype);
		$medicine_amount = medicine_amount($bc_id, $trans_year, $doctype);

		//$total = $disinfectant_amount + $medicine_amount;
		$harvested_kilo = harvested_kilo($bc_id, $trans_year, $doctype);

		for ($broiler_summary_item_id = 42; $broiler_summary_item_id <= 44 ; $broiler_summary_item_id++) { 
			$set = array('broiler_summary_status' =>	2);
			$where = array('bc_id'	=>	decode($bc_id),	'broiler_summary_item_id'	=> $broiler_summary_item_id, 'YEAR(trans_date)' => decode($trans_year), 'trans_type_id' =>	1);
			$ci->admin->update_data('broiler_summary_tbl', $set, $where);	
		}
		for($month = 1; $month <= 12; $month++){
			$total[$month] = $disinfectant_amount[$month]+ $medicine_amount[$month];
			if($harvested_kilo[$month] == 0){
				$result[$month] = 0;
			} else {
				$result[$month] = $total[$month]/$harvested_kilo[$month];
				$result[$month] = round($result[$month], 3);
				
				//return round($result[$month], dec_places());
			}
			$broiler_group_id = 5;
				
			$check = $ci->admin->check_data('broiler_cost_summary_tbl', array('bc_id'	=>	decode($bc_id),	'broiler_group_id'	=> $broiler_group_id, 'YEAR(trans_date)' => decode($trans_year), 'MONTH(trans_date)' => $month), true);
			if($check['result']){
				$set = array('trans_qty' =>	round($result[$month], dec_places()), 'trans_type_id' => 1);
				$where = array('bc_id'	=>	decode($bc_id),	'broiler_group_id'	=> $broiler_group_id, 'YEAR(trans_date)' => decode($trans_year), 'MONTH(trans_date)' => $month);
				$ci->admin->update_data('broiler_cost_summary_tbl', $set, $where);
			} else {
				$set = array('trans_qty' =>	round($result[$month], dec_places()), 'bc_id'	=>	decode($bc_id),	'broiler_group_id'	=> $broiler_group_id, 'trans_date' => decode($trans_year).'-'.$month.'-01', 'trans_type_id' => 1);
				$ci->admin->insert_data('broiler_cost_summary_tbl', $set);
			}


			$broiler_summary_item_id = 42; //MEDICINE AMOUNT
			$set = array('trans_qty' =>	round($medicine_amount[$month], dec_places()), 'bc_id'	=>	decode($bc_id),	'broiler_summary_item_id'	=> $broiler_summary_item_id, 'trans_date' => decode($trans_year).'-'.$month.'-01', 'trans_type_id' => 1, 'broiler_summary_status'	=> 1);
			$ci->admin->insert_data('broiler_summary_tbl', $set);

			$broiler_summary_item_id = 43; //disinfectant_amount
			$set = array('trans_qty' =>	round($disinfectant_amount[$month], dec_places()), 'bc_id'	=>	decode($bc_id),	'broiler_summary_item_id'	=> $broiler_summary_item_id, 'trans_date' => decode($trans_year).'-'.$month.'-01', 'trans_type_id' => 1, 'broiler_summary_status'	=> 1);
			$ci->admin->insert_data('broiler_summary_tbl', $set);

			$broiler_summary_item_id = 44; //MEDICINES
			$set = array('trans_qty' =>	round($result[$month], dec_places()), 'bc_id'	=>	decode($bc_id),	'broiler_summary_item_id'	=> $broiler_summary_item_id, 'trans_date' => decode($trans_year).'-'.$month.'-01', 'trans_type_id' => 1, 'broiler_summary_status'	=> 1);
			$ci->admin->insert_data('broiler_summary_tbl', $set);
		}
		return $result;
	}

	function feed_cost_kg_reg($bc_id, $trans_year, $doctype){
		$fcr_input = get_broiler_budget_qty(23, decode($bc_id), decode($trans_year),  $doctype) != false ? get_broiler_budget_qty(23, decode($bc_id), decode($trans_year),  $doctype) : 0;

		$feed_cost_input = get_broiler_budget_qty(22, decode($bc_id), decode($trans_year),  $doctype) != false ? get_broiler_budget_qty(22, decode($bc_id), decode($trans_year),  $doctype) : 0;

		if($fcr_input && $feed_cost_input){
			$i = 1;
			foreach ($fcr_input as $row) {
				$fcr_input_val[$i] = $row->broiler_budget_qty;
				$i++;
			}

			$i = 1;
			foreach ($feed_cost_input as $row) {
				$feed_cost_input_val[$i] = $row->broiler_budget_qty;
				$i++;
			}
			for($month = 1; $month <= 12; $month++){
				$result[$month] = $fcr_input_val[$month] * $feed_cost_input_val[$month];
				$result[$month] = round($result[$month], 3);
			}

			return $result;
		} else {
			return $result = 0;
		}
	}

	function feed_cost_kg_nae($bc_id, $trans_year,  $doctype){
		$fcr_input = get_broiler_budget_qty(23, decode($bc_id), decode($trans_year),  $doctype) != false ? get_broiler_budget_qty(23, decode($bc_id), decode($trans_year),  $doctype) : 0;

		$feed_cost_input = get_broiler_budget_qty(21, decode($bc_id), decode($trans_year),  $doctype) != false ? get_broiler_budget_qty(21, decode($bc_id), decode($trans_year),  $doctype) : 0;

		if($fcr_input && $feed_cost_input){
			$i = 1;
			foreach ($fcr_input as $row) {
				$fcr_input_val[$i] = $row->broiler_budget_qty;
				$i++;
			}

			$i = 1;
			foreach ($feed_cost_input as $row) {
				$feed_cost_input_val[$i] = $row->broiler_budget_qty;
				$i++;
			}
			for($month = 1; $month <= 12; $month++){
				$result[$month] = $fcr_input_val[$month] * $feed_cost_input_val[$month];
				$result[$month] = round($result[$month], 3);
			}
			return $result;
		} else {
			return $result = 0;
		}
	}

	function feed_cost_ibc($bc_id, $trans_year,  $doctype){
		$fcr_input = get_broiler_budget_qty(23, decode($bc_id), decode($trans_year),  $doctype) != false ? get_broiler_budget_qty(23, decode($bc_id), decode($trans_year),  $doctype) : 0;

		$feed_cost_input = get_broiler_budget_qty(116, decode($bc_id), decode($trans_year),  $doctype) != false ? get_broiler_budget_qty(116, decode($bc_id), decode($trans_year),  $doctype) : 0;

		if($fcr_input && $feed_cost_input){
			$i = 1;
			foreach ($fcr_input as $row) {
				$fcr_input_val[$i] = $row->broiler_budget_qty;
				$i++;
			}

			$i = 1;
			foreach ($feed_cost_input as $row) {
				$feed_cost_input_val[$i] = $row->broiler_budget_qty;
				$i++;
			}
			for($month = 1; $month <= 12; $month++){
				$result[$month] = $fcr_input_val[$month] * $feed_cost_input_val[$month];
				$result[$month] = round($result[$month], 3);
			}
			return $result;
		} else {
			return $result = 0;
		}
	}

	function feed_freight_cost($bc_id, $trans_year,  $doctype){
		$fcr_input = get_broiler_budget_qty(23, decode($bc_id), decode($trans_year),  $doctype) != false ? get_broiler_budget_qty(23, decode($bc_id), decode($trans_year),  $doctype) : 0;

		$feed_freight_input = get_broiler_budget_qty(25, decode($bc_id), decode($trans_year),  $doctype) != false ? get_broiler_budget_qty(25, decode($bc_id), decode($trans_year),  $doctype) : 0;

		if($fcr_input && $feed_freight_input){
			$i = 1;
			foreach ($fcr_input as $row) {
				$fcr_input_val[$i] = $row->broiler_budget_qty;
				$i++;
			}

			$i = 1;
			foreach ($feed_freight_input as $row) {
				$feed_freight_input_val[$i] = $row->broiler_budget_qty;
				$i++;
			}

			for($month = 1; $month <= 12; $month++){
				$result[$month] = $fcr_input_val[$month] * $feed_freight_input_val[$month];
				$result[$month] = round($result[$month], 3);
			}

			return $result;
		} else {
			return $result = 0;
		}
	}

	function feed_cost_amount($bc_id, $trans_year, $doctype){

		$nae_percentage = get_broiler_budget_qty(24, decode($bc_id), decode($trans_year),  $doctype) != false ? get_broiler_budget_qty(24, decode($bc_id), decode($trans_year),  $doctype) : 0;
		$ibc_percentage = get_broiler_budget_qty(115, decode($bc_id), decode($trans_year),  $doctype) != false ? get_broiler_budget_qty(115, decode($bc_id), decode($trans_year),  $doctype) : 0;

		if($nae_percentage && $ibc_percentage){
			$i = 1;
			foreach ($nae_percentage as $row) {
				$nae_percentage_val[$i] = $row->broiler_budget_qty;
				$nae_percentage_val[$i] = $nae_percentage_val[$i]/100;
				
				$i++;
			}

			$i = 1;
			foreach ($ibc_percentage as $row) {
				$ibc_percentage_val[$i] = $row->broiler_budget_qty;
				$ibc_percentage_val[$i] = $ibc_percentage_val[$i]/100;
				
				$i++;
			}

			$harvested_kilo = harvested_kilo($bc_id, $trans_year,  $doctype);

			$feed_freight_cost = feed_freight_cost($bc_id, $trans_year,  $doctype);
			$feed_cost_kg_reg = feed_cost_kg_reg($bc_id, $trans_year,  $doctype);
			$feed_cost_kg_nae = feed_cost_kg_nae($bc_id, $trans_year,  $doctype);
			$feed_cost_kg_ibc = feed_cost_ibc($bc_id, $trans_year,  $doctype);

			for($month = 1; $month <= 12; $month++){

				$regular_percentage[$month] = 1 - $nae_percentage_val[$month] - $ibc_percentage_val[$month];

				$total_regular1[$month] = $feed_freight_cost[$month] + $feed_cost_kg_reg[$month];
				$total_regular2[$month] = $harvested_kilo[$month] * $regular_percentage[$month];
				$result_regular[$month] = $total_regular1[$month] * $total_regular2[$month];

				$total_nae1[$month] = $feed_freight_cost[$month] + $feed_cost_kg_nae[$month];
				$total_nae2[$month] = $harvested_kilo[$month] * $nae_percentage_val[$month];
				$result_nae[$month] = $total_nae1[$month] * $total_nae2[$month];

				$total_ibc1[$month] = $feed_freight_cost[$month] + $feed_cost_kg_ibc[$month];
				$total_ibc2[$month] = $harvested_kilo[$month] * $ibc_percentage_val[$month];
				$result_ibc[$month] = $total_ibc1[$month] * $total_ibc2[$month];

				$result[$month] = $result_regular[$month] + $result_nae[$month] + $result_ibc[$month];
				$result[$month] = round($result[$month], 3);

				$broiler_line_item_id = 5;
				$ci =& get_instance();
				$ci->load->model('admin_model', 'admin');
				$check = $ci->admin->check_data('broiler_amount_summary_tbl', array('bc_id'	=>	decode($bc_id),	'broiler_line_item_id'	=> $broiler_line_item_id, 'YEAR(trans_date)' => decode($trans_year), 'MONTH(trans_date)' => $month), true);
				if($check['result']){
					$set = array('trans_qty' =>	round($result[$month], dec_places()), 'trans_type_id' => 1);
					$where = array('bc_id'	=>	decode($bc_id),	'broiler_line_item_id'	=> $broiler_line_item_id, 'YEAR(trans_date)' => decode($trans_year), 'MONTH(trans_date)' => $month);
					$ci->admin->update_data('broiler_amount_summary_tbl', $set, $where);
				} else {
					$set = array('trans_qty' =>	round($result[$month], dec_places()), 'bc_id'	=>	decode($bc_id),	'broiler_line_item_id'	=> $broiler_line_item_id, 'trans_date' => decode($trans_year).'-'.$month.'-01', 'trans_type_id' => 1);
					$ci->admin->insert_data('broiler_amount_summary_tbl', $set);
				}
			}
			return $result;
		} else {
			return $result = 0;
		}
	}

	function feed_cost($bc_id, $trans_year,  $doctype){
		$ci =& get_instance();
		$ci->load->model('admin_model', 'admin');

		$feed_cost_kg_reg = feed_cost_kg_reg($bc_id, $trans_year,  $doctype);
		$feed_cost_kg_nae = feed_cost_kg_nae($bc_id, $trans_year,  $doctype);
		$feed_freight_cost = feed_freight_cost($bc_id, $trans_year,  $doctype);
		$feed_cost_amount = feed_cost_amount($bc_id, $trans_year,  $doctype);
		$harvested_kilo = harvested_kilo($bc_id, $trans_year,  $doctype);

		for ($broiler_summary_item_id = 37; $broiler_summary_item_id <= 41 ; $broiler_summary_item_id++) { 
			$set = array('broiler_summary_status' =>	2);
			$where = array('bc_id'	=>	decode($bc_id),	'broiler_summary_item_id'	=> $broiler_summary_item_id, 'YEAR(trans_date)' => decode($trans_year), 'trans_type_id' =>	1);
			$ci->admin->update_data('broiler_summary_tbl', $set, $where);	
		}

		for($month = 1; $month <= 12; $month++){
			if($harvested_kilo[$month] == 0){
				$result[$month] = 0;
			} else {
				$result[$month] = $feed_cost_amount[$month]/$harvested_kilo[$month];
				$result[$month] = round($result[$month], 3);

			}
			$broiler_group_id = 3;
				
			$check = $ci->admin->check_data('broiler_cost_summary_tbl', array('bc_id'	=>	decode($bc_id),	'broiler_group_id'	=> $broiler_group_id, 'YEAR(trans_date)' => decode($trans_year), 'MONTH(trans_date)' => $month), true);
			if($check['result']){
				$set = array('trans_qty' =>	round($result[$month], dec_places()), 'trans_type_id' => 1);
				$where = array('bc_id'	=>	decode($bc_id),	'broiler_group_id'	=> $broiler_group_id, 'YEAR(trans_date)' => decode($trans_year), 'MONTH(trans_date)' => $month);
				$ci->admin->update_data('broiler_cost_summary_tbl', $set, $where);
			} else {
				$set = array('trans_qty' =>	round($result[$month], dec_places()), 'bc_id'	=>	decode($bc_id),	'broiler_group_id'	=> $broiler_group_id, 'trans_date' => decode($trans_year).'-'.$month.'-01', 'trans_type_id' => 1);
				$ci->admin->insert_data('broiler_cost_summary_tbl', $set);
			}

			$broiler_summary_item_id = 37; //Feed Cost Amount
			$set = array('trans_qty' =>	round($feed_cost_amount[$month], dec_places()), 'bc_id'	=>	decode($bc_id),	'broiler_summary_item_id'	=> $broiler_summary_item_id, 'trans_date' => decode($trans_year).'-'.$month.'-01', 'trans_type_id' => 1, 'broiler_summary_status'	=> 1);
			$ci->admin->insert_data('broiler_summary_tbl', $set);

			$broiler_summary_item_id = 38; //Feed Cost/kg Broiler (Reg)
			$set = array('trans_qty' =>	round($feed_cost_kg_reg[$month], dec_places()), 'bc_id'	=>	decode($bc_id),	'broiler_summary_item_id'	=> $broiler_summary_item_id, 'trans_date' => decode($trans_year).'-'.$month.'-01', 'trans_type_id' => 1, 'broiler_summary_status'	=> 1);
			$ci->admin->insert_data('broiler_summary_tbl', $set);

			$broiler_summary_item_id = 39; //Feed Cost/kg Broiler (NAE)
			$set = array('trans_qty' =>	round($feed_cost_kg_nae[$month], dec_places()), 'bc_id'	=>	decode($bc_id),	'broiler_summary_item_id'	=> $broiler_summary_item_id, 'trans_date' => decode($trans_year).'-'.$month.'-01', 'trans_type_id' => 1, 'broiler_summary_status'	=> 1);
			$ci->admin->insert_data('broiler_summary_tbl', $set);

			$broiler_summary_item_id = 40; //Feeds Freight Cost
			$set = array('trans_qty' =>	round($feed_freight_cost[$month], dec_places()), 'bc_id'	=>	decode($bc_id),	'broiler_summary_item_id'	=> $broiler_summary_item_id, 'trans_date' => decode($trans_year).'-'.$month.'-01', 'trans_type_id' => 1, 'broiler_summary_status'	=> 1);
			$ci->admin->insert_data('broiler_summary_tbl', $set);

			$broiler_summary_item_id = 41; //FEEDS
			$set = array('trans_qty' =>	round($result[$month], dec_places()), 'bc_id'	=>	decode($bc_id),	'broiler_summary_item_id'	=> $broiler_summary_item_id, 'trans_date' => decode($trans_year).'-'.$month.'-01', 'trans_type_id' => 1, 'broiler_summary_status'	=> 1);
			$ci->admin->insert_data('broiler_summary_tbl', $set);
		}
			
		return $result;
	}

	function basic_fee($bc_id, $trans_year,  $doctype){

		$basic_fee_input = get_broiler_budget_qty(6, decode($bc_id), decode($trans_year),  $doctype) != false ? get_broiler_budget_qty(6, decode($bc_id), decode($trans_year),  $doctype) : 0;
		$nae_percentage = get_broiler_budget_qty(24, decode($bc_id), decode($trans_year),  $doctype) != false ? get_broiler_budget_qty(24, decode($bc_id), decode($trans_year),  $doctype) : 0;

		$harvested_heads = harvested_heads($bc_id, $trans_year,  $doctype);
		if($basic_fee_input && $nae_percentage){
			$i = 1;
			foreach ($basic_fee_input as $row) {
				$basic_fee_input_val[$i] = $row->broiler_budget_qty;
				$i++;
			}

			$i = 1;
			foreach ($nae_percentage as $row) {
				$nae_percentage_val[$i] = $row->broiler_budget_qty;
				$nae_percentage_amount_type_val[$i] = $row->amount_type_id;
				$i++;
			}

			for($month = 1; $month <= 12; $month++){
				$nae_percentage_val[$month] = amount_type($nae_percentage_val[$month], $nae_percentage_amount_type_val[$month]);
				$regular_percentage[$month] = 1 - $nae_percentage_val[$month];
				$result[$month] = $harvested_heads[$month] * $regular_percentage[$month] * $basic_fee_input_val[$month];
				$result[$month] = round($result[$month], 3);

				$broiler_line_item_id = 7;
				$ci =& get_instance();
				$ci->load->model('admin_model', 'admin');
				$check = $ci->admin->check_data('broiler_amount_summary_tbl', array('bc_id'	=>	decode($bc_id),	'broiler_line_item_id'	=> $broiler_line_item_id, 'YEAR(trans_date)' => decode($trans_year), 'MONTH(trans_date)' => $month), true);
				if($check['result']){
					$set = array('trans_qty' =>	round($harvested_heads[$month], dec_places()), 'trans_type_id' => 1);
					$where = array('bc_id'	=>	decode($bc_id),	'broiler_line_item_id'	=> $broiler_line_item_id, 'YEAR(trans_date)' => decode($trans_year), 'MONTH(trans_date)' => $month);
					$ci->admin->update_data('broiler_amount_summary_tbl', $set, $where);
				} else {
					$set = array('trans_qty' =>	round($harvested_heads[$month], dec_places()), 'bc_id'	=>	decode($bc_id),	'broiler_line_item_id'	=> $broiler_line_item_id, 'trans_date' => decode($trans_year).'-'.$month.'-01', 'trans_type_id' => 1);
					$ci->admin->insert_data('broiler_amount_summary_tbl', $set);
				}
			}

			return $result;
		} else {
			return $result = 0;
		}
	}

	function basic_fee_nae($bc_id, $trans_year,  $doctype){

		$basic_fee_input_nae = get_broiler_budget_qty(46, decode($bc_id), decode($trans_year),  $doctype) != false ? get_broiler_budget_qty(46, decode($bc_id), decode($trans_year),  $doctype) : 0;
		$nae_percentage = get_broiler_budget_qty(24, decode($bc_id), decode($trans_year),  $doctype) != false ? get_broiler_budget_qty(24, decode($bc_id), decode($trans_year),  $doctype) : 0;

		$harvested_heads = harvested_heads($bc_id, $trans_year,  $doctype);
		if($basic_fee_input_nae && $nae_percentage){
			$i = 1;
			foreach ($basic_fee_input_nae as $row) {
				$basic_fee_input_val[$i] = $row->broiler_budget_qty;
				$i++;
			}

			$i = 1;
			foreach ($nae_percentage as $row) {
				$nae_percentage_val[$i] = $row->broiler_budget_qty;
				$nae_percentage_amount_type_val[$i] = $row->amount_type_id;
				$i++;
			}

			for($month = 1; $month <= 12; $month++){

				$nae_percentage_val[$month] = amount_type($nae_percentage_val[$month], $nae_percentage_amount_type_val[$month]);

				$result[$month] = $harvested_heads[$month] * $nae_percentage_val[$month] * $basic_fee_input_val[$month];
				$result[$month] = round($result[$month], 3);
			}

			return $result;
		} else {
			return $result = 0;
		}
	}

	function hr_incentive($bc_id, $trans_year,  $doctype){
		$hr = get_broiler_budget_qty(3, decode($bc_id), decode($trans_year),  $doctype) != false ? get_broiler_budget_qty(3, decode($bc_id), decode($trans_year),  $doctype) : 0;
		
		$hr_incentive = get_broiler_budget_qty(7, decode($bc_id), decode($trans_year),  $doctype) != false ? get_broiler_budget_qty(7, decode($bc_id), decode($trans_year),  $doctype) : 0;

		$hr_config = get_broiler_config_qty('HR - Reg', decode($bc_id), decode($trans_year),  $doctype) != false ? get_broiler_config_qty('HR - Reg', decode($bc_id), decode($trans_year),  $doctype) : 0;
		$nae_percentage = get_broiler_budget_qty(24, decode($bc_id), decode($trans_year),  $doctype) != false ? get_broiler_budget_qty(24, decode($bc_id), decode($trans_year),  $doctype) : 0;

		if($hr_config && $hr_incentive && $hr && $nae_percentage){
			$i = 1;
			foreach ($hr as $row) {
				$hr_val[$i] = $row->broiler_budget_qty;
				$hr_amount_type_val[$i] = $row->amount_type_id;
				$i++;
			}
			
			$i = 1;
			foreach ($hr_config as $row) {
				$hr_config_val[$i] = $row->config_qty;
				$hr_config_amount_type_val[$i] = $row->amount_type_id;
				$i++;
			}

			$i = 1;
			foreach ($hr_incentive as $row) {
				$hr_incentive_val[$i] = $row->broiler_budget_qty;
				$i++;
			}

			$i = 1;
			foreach ($nae_percentage as $row) {
				$nae_percentage_val[$i] = $row->broiler_budget_qty;
				$nae_percentage_amount_type_val[$i] = $row->amount_type_id;
				$i++;
			}

			$harvested_heads = harvested_heads($bc_id, $trans_year,  $doctype);

			for($month = 1; $month <= 12; $month++){
				$nae_percentage_val[$month] = amount_type($nae_percentage_val[$month], $nae_percentage_amount_type_val[$month]);
				$regular_percentage[$month] = 1 - $nae_percentage_val[$month];

				$hr_config_val[$month] = amount_type($hr_config_val[$month], $hr_config_amount_type_val[$month]);
				$hr_val[$month] = amount_type($hr_val[$month], $hr_amount_type_val[$month]);

				$hr_difference[$month] = $hr_val[$month] - $hr_config_val[$month];
				$perc_diff[$month] = (round($hr_difference[$month], 6) /.5) * 100;

				$result[$month] = $perc_diff[$month] * $hr_incentive_val[$month] * $harvested_heads[$month] * $regular_percentage[$month];
				$result[$month] = round($result[$month], 3);
			}
			return $result;
		} else {
			return $result = 0;
		}
	}

	function hr_incentive_nae($bc_id, $trans_year,  $doctype){
		$hr = get_broiler_budget_qty(3, decode($bc_id), decode($trans_year),  $doctype) != false ? get_broiler_budget_qty(3, decode($bc_id), decode($trans_year),  $doctype) : 0;
		
		//POINT TO HR-Incentive/Penalty - NAE
		$hr_incentive = get_broiler_budget_qty(48, decode($bc_id), decode($trans_year),  $doctype) != false ? get_broiler_budget_qty(48, decode($bc_id), decode($trans_year),  $doctype) : 0;

		$hr_config = get_broiler_config_qty('HR - NAE', decode($bc_id), decode($trans_year),  $doctype) != false ? get_broiler_config_qty('HR - NAE', decode($bc_id), decode($trans_year),  $doctype) : 0;
		$nae_percentage = get_broiler_budget_qty(24, decode($bc_id), decode($trans_year),  $doctype) != false ? get_broiler_budget_qty(24, decode($bc_id), decode($trans_year),  $doctype) : 0;

		if($hr_config && $hr_incentive && $hr && $nae_percentage){
			$i = 1;
			foreach ($hr as $row) {
				$hr_val[$i] = $row->broiler_budget_qty;
				$hr_amount_type_val[$i] = $row->amount_type_id;
				$i++;
			}
			
			$i = 1;
			foreach ($hr_config as $row) {
				$hr_config_val[$i] = $row->config_qty;
				$hr_config_amount_type_val[$i] = $row->amount_type_id;
				$i++;
			}

			$i = 1;
			foreach ($hr_incentive as $row) {
				$hr_incentive_val[$i] = $row->broiler_budget_qty;
				$i++;
			}

			$i = 1;
			foreach ($nae_percentage as $row) {
				$nae_percentage_val[$i] = $row->broiler_budget_qty;
				$nae_percentage_amount_type_val[$i] = $row->amount_type_id;
				$i++;
			}
			$harvested_heads = harvested_heads($bc_id, $trans_year,  $doctype);

			for($month = 1; $month <= 12; $month++){
				$nae_percentage_val[$month] = amount_type($nae_percentage_val[$month], $nae_percentage_amount_type_val[$month]);

				$hr_config_val[$month] = amount_type($hr_config_val[$month], $hr_config_amount_type_val[$month]);
				$hr_val[$month] = amount_type($hr_val[$month], $hr_amount_type_val[$month]);

				$hr_difference[$month] = $hr_val[$month] - $hr_config_val[$month];
				$perc_diff[$month] = round($hr_difference[$month], 6) /.5 * 100;

				$result[$month] = $perc_diff[$month] * $hr_incentive_val[$month] * $harvested_heads[$month] * $nae_percentage_val[$month];
				$result[$month] = round($result[$month], 3);
			}
			return $result;
		} else {
			return $result = 0;
		}
	}

	function fcr_incentive($bc_id, $trans_year,  $doctype){
		$fcr_input = get_broiler_budget_qty(23, decode($bc_id), decode($trans_year),  $doctype) != false ? get_broiler_budget_qty(23, decode($bc_id), decode($trans_year),  $doctype) : 0;
		$fcr_incentive = get_broiler_budget_qty(8, decode($bc_id), decode($trans_year),  $doctype) != false ? get_broiler_budget_qty(8, decode($bc_id), decode($trans_year),  $doctype) : 0;
		$fcr_config = get_broiler_config_qty('FCR', decode($bc_id), decode($trans_year),  $doctype) != false ? get_broiler_config_qty('FCR', decode($bc_id), decode($trans_year),  $doctype) : 0;
		
		if($fcr_config && $fcr_incentive && $fcr_input){
			$i = 1;
			foreach ($fcr_input as $row) {
				$fcr_input_val[$i] = $row->broiler_budget_qty;
				$fcr_amount_type_val[$i] = $row->amount_type_id;
				$i++;
			}
			
			$i = 1;
			foreach ($fcr_config as $row) {
				$fcr_config_val[$i] = $row->config_qty;
				$fcr_config_amount_type_val[$i] = $row->amount_type_id;
				$i++;
			}
			
			$i = 1;
			foreach ($fcr_incentive as $row) {
				$fcr_incentive_val[$i] = $row->broiler_budget_qty;
				$i++;
			}
			$harvested_heads = harvested_heads($bc_id, $trans_year,  $doctype);
			for($month = 1; $month <= 12; $month++){
				$fcr_input_val[$month] = amount_type($fcr_input_val[$month], $fcr_amount_type_val[$month]);
				$fcr_config_val[$month] = amount_type($fcr_config_val[$month], $fcr_config_amount_type_val[$month]);
				
				$fcr_difference[$month] = $fcr_config_val[$month] - $fcr_input_val[$month];
				$perc_diff[$month] = round($fcr_difference[$month], 3) * 100;
				$result[$month] = $perc_diff[$month] * $fcr_incentive_val[$month] * $harvested_heads[$month];
				$result[$month] = round($result[$month], 3);
			}
				
			return $result;
		} else {
			return $result = 0;
		}
	}

	function fcr_incentive_new($bc_id, $trans_year,  $doctype){
		$fcr_input = get_broiler_budget_qty(23, decode($bc_id), decode($trans_year),  $doctype) != false ? get_broiler_budget_qty(23, decode($bc_id), decode($trans_year),  $doctype) : 0;
		
		$fcr_config = get_broiler_config_qty('FCR', decode($bc_id), decode($trans_year),  $doctype) != false ? get_broiler_config_qty('FCR', decode($bc_id), decode($trans_year),  $doctype) : 0;


		$fcr_new_range = get_broiler_config_qty_bulk('FCR New Range', decode($bc_id), decode($trans_year)) != false ? get_broiler_config_qty_bulk('FCR New Range', decode($bc_id), decode($trans_year)) : 0;
		
		$fcr_new_rate = get_broiler_config_qty_bulk('FCR New Rate', decode($bc_id), decode($trans_year)) != false ? get_broiler_config_qty_bulk('FCR New Rate', decode($bc_id), decode($trans_year)) : 0;
		
		
		if($fcr_config && $fcr_input && $fcr_new_range && $fcr_new_rate){
			$i = 1;
			foreach ($fcr_input as $row) {
				$fcr_input_val[$i] = $row->broiler_budget_qty;
				$fcr_amount_type_val[$i] = $row->amount_type_id;
				$i++;
			}
			
			$i = 1;
			foreach ($fcr_config as $row) {
				$fcr_config_val[$i] = $row->config_qty;
				$fcr_config_amount_type_val[$i] = $row->amount_type_id;
				$i++;
			}

			$range_1 = 1;
			$range_2 = 1;
			$range_3 = 1;
			$range_4 = 1;
			$range_5 = 1;
			foreach ($fcr_new_range as $row) {
				
				if($row->broiler_subgroup_name == 'FCR New Range 1'){
					$fcr_new_range_1_val[$range_1] = $row->config_qty;
					$range_1++;
				}
				if($row->broiler_subgroup_name == 'FCR New Range 2'){
					$fcr_new_range_2_val[$range_2] = $row->config_qty;
					$range_2++;
				}
				if($row->broiler_subgroup_name == 'FCR New Range 3'){
					$fcr_new_range_3_val[$range_3] = $row->config_qty;
					$range_3++;
				}
				if($row->broiler_subgroup_name == 'FCR New Range 4'){
					$fcr_new_range_4_val[$range_4] = $row->config_qty;
					$range_4++;
				}
				if($row->broiler_subgroup_name == 'FCR New Range 5'){
					$fcr_new_range_5_val[$range_5] = $row->config_qty;
					$range_5++;
				}
				
			}

			$rate_1 = 1;
			$rate_2 = 1;
			$rate_3 = 1;
			$rate_4 = 1;
			$rate_5 = 1;
			$rate_6 = 1;
			foreach ($fcr_new_rate as $row) {
				if($row->broiler_subgroup_name == 'FCR New Rate 1'){
					$fcr_new_rate_1_val[$rate_1] = $row->config_qty;
					$rate_1++;
				}
				if($row->broiler_subgroup_name == 'FCR New Rate 2'){
					$fcr_new_rate_2_val[$rate_2] = $row->config_qty;
					$rate_2++;
				}
				if($row->broiler_subgroup_name == 'FCR New Rate 3'){
					$fcr_new_rate_3_val[$rate_3] = $row->config_qty;
					$rate_3++;
				}
				if($row->broiler_subgroup_name == 'FCR New Rate 4'){
					$fcr_new_rate_4_val[$rate_4] = $row->config_qty;
					$rate_4++;
				}
				if($row->broiler_subgroup_name == 'FCR New Rate 5'){
					$fcr_new_rate_5_val[$rate_5] = $row->config_qty;
					$rate_5++;
				}
				if($row->broiler_subgroup_name == 'FCR New Rate 6'){
					$fcr_new_rate_6_val[$rate_6] = $row->config_qty;
					$rate_6++;
				}
			}
			
			
			$harvested_heads = harvested_heads($bc_id, $trans_year,  $doctype);
			for($month = 1; $month <= 12; $month++){
				
				$fcr_input_val[$month] = amount_type($fcr_input_val[$month], $fcr_amount_type_val[$month]);
				$fcr_config_val[$month] = amount_type($fcr_config_val[$month], $fcr_config_amount_type_val[$month]);

				if($fcr_input_val[$month] <= $fcr_new_range_1_val[$month]){
					
					$dynamic_fcr_incentive[$month] = $fcr_new_rate_1_val[$month];

				} else if($fcr_input_val[$month] > $fcr_new_range_1_val[$month] && $fcr_input_val[$month] <= $fcr_new_range_2_val[$month]){
					
					$dynamic_fcr_incentive[$month] = $fcr_new_rate_2_val[$month];

				} else if($fcr_input_val[$month] > $fcr_new_range_2_val[$month] && $fcr_input_val[$month] <= $fcr_new_range_3_val[$month]){

					$dynamic_fcr_incentive[$month] = $fcr_new_rate_3_val[$month];

				} else if($fcr_input_val[$month] > $fcr_new_range_3_val[$month] && $fcr_input_val[$month] <= $fcr_new_range_4_val[$month]){

					$dynamic_fcr_incentive[$month] = $fcr_new_rate_4_val[$month];

				} else if($fcr_input_val[$month] > $fcr_new_range_4_val[$month] && $fcr_input_val[$month] <= $fcr_new_range_5_val[$month]){

					$dynamic_fcr_incentive[$month] = $fcr_new_rate_5_val[$month];

				} else {

					$dynamic_fcr_incentive[$month] = $fcr_new_rate_6_val[$month];

				}

				if($fcr_input_val[$month] > $fcr_config_val[$month]){
					$result[$month] = 0;
				} else {
					$fcr_difference[$month] = $fcr_config_val[$month] - $fcr_input_val[$month];
					$perc_diff[$month] = round($fcr_difference[$month], 3) * 100;
					$result[$month] = $perc_diff[$month] * $dynamic_fcr_incentive[$month] * $harvested_heads[$month];
					$result[$month] = round($result[$month], 3);
				}
			}
				
			return $result;
		} else {
			return $result = 0;
		}
	}

	function uniformity_bonus($bc_id, $trans_year,  $doctype){
		$harvested_heads = harvested_heads($bc_id, $trans_year,  $doctype);

		$harvest_live_sales = get_broiler_budget_qty(31, decode($bc_id), decode($trans_year),  $doctype) != false ? get_broiler_budget_qty(31, decode($bc_id), decode($trans_year),  $doctype) : 0;
		$uniformity_bonus = get_broiler_budget_qty(9, decode($bc_id), decode($trans_year),  $doctype) != false ? get_broiler_budget_qty(9, decode($bc_id), decode($trans_year),  $doctype) : 0;
		
		if($harvest_live_sales && $uniformity_bonus){
			$i = 1;
			foreach ($harvest_live_sales as $row) {
				$harvest_live_sales_val[$i] = $row->broiler_budget_qty;
				$harvest_live_sales_amount_type_val[$i] = $row->amount_type_id;
				$i++;
			}

			$i = 1;
			foreach ($uniformity_bonus as $row) {
				$uniformity_bonus_val[$i] = $row->broiler_budget_qty;
				$i++;
			}

			for($month = 1; $month <= 12; $month++){
				$harvest_live_sales_val[$month] = amount_type($harvest_live_sales_val[$month], $harvest_live_sales_amount_type_val[$month]);
				$perc[$month] = 1 - $harvest_live_sales_val[$month];
				$result[$month] = $harvested_heads[$month] * $perc[$month] * $uniformity_bonus_val[$month];
				$result[$month] = round($result[$month], 3);
			}
			return $result;
		} else {
			return $result = 0;
		}
	}

	function right_size_bonus($bc_id, $trans_year,  $doctype){
		$right_size_bonus = get_broiler_budget_qty(10, decode($bc_id), decode($trans_year),  $doctype) != false ? get_broiler_budget_qty(10, decode($bc_id), decode($trans_year),  $doctype) : 0;
		
		$harvest_live_sales = get_broiler_budget_qty(31, decode($bc_id), decode($trans_year),  $doctype) != false ? get_broiler_budget_qty(31, decode($bc_id), decode($trans_year),  $doctype) : 0;

		$right_size_perc_availment = get_broiler_config_qty('Right Size Bonus % Availment', decode($bc_id), decode($trans_year),  $doctype) != false ? get_broiler_config_qty('Right Size Bonus % Availment', decode($bc_id), decode($trans_year),  $doctype) : 0;

		if($right_size_bonus && $right_size_perc_availment && $harvest_live_sales){
			$i = 1;
			foreach ($harvest_live_sales as $row) {
				$harvest_live_sales_val[$i] = $row->broiler_budget_qty;
				$harvest_live_sales_amount_type_val[$i] = $row->amount_type_id;
				$i++;
			}

			$i = 1;
			foreach ($right_size_bonus as $row) {
				$right_size_bonus_val[$i] = $row->broiler_budget_qty;
				$i++;
			}
			$i = 1;
			foreach ($right_size_perc_availment as $row) {
				$right_size_perc_availment_val[$i] = $row->config_qty;
				$right_size_perc_availment_amount_type_val[$i] = $row->amount_type_id;
				$i++;
			}
			$harvested_heads = harvested_heads($bc_id, $trans_year,  $doctype);
			for($month = 1; $month <= 12; $month++){

				$harvest_live_sales_val[$month] = amount_type($harvest_live_sales_val[$month], $harvest_live_sales_amount_type_val[$month]);

				$right_size_perc_availment_val[$month] = amount_type($right_size_perc_availment_val[$month], $right_size_perc_availment_amount_type_val[$month]);

				$total_percent[$month] = 1 - $harvest_live_sales_val[$month];
				$total_heads[$month] = $harvested_heads[$month] * $total_percent[$month];

				$result[$month] = $right_size_bonus_val[$month] * $total_heads[$month] * $right_size_perc_availment_val[$month];

			}
			return $result;
		} else {
			return $result = 0;
		}
	}

	function performance_bonus($bc_id, $trans_year,  $doctype){

		//add the availment
		$fcr_input = get_broiler_budget_qty(23, decode($bc_id), decode($trans_year),  $doctype) != false ? get_broiler_budget_qty(23, decode($bc_id), decode($trans_year),  $doctype) : 0;
		$hr = get_broiler_budget_qty(3, decode($bc_id), decode($trans_year),  $doctype) != false ? get_broiler_budget_qty(3, decode($bc_id), decode($trans_year),  $doctype) : 0;
		$alw = get_broiler_budget_qty(4, decode($bc_id), decode($trans_year),  $doctype) != false ? get_broiler_budget_qty(4, decode($bc_id), decode($trans_year),  $doctype) : 0;

		$old_performance_rate = get_broiler_config_qty_bulk('Old Performance Rate', decode($bc_id), decode($trans_year)) != false ? get_broiler_config_qty_bulk('Old Performance Rate', decode($bc_id), decode($trans_year)) : 0;

		$old_performance_base = get_broiler_config_qty_bulk('Old Performance Base', decode($bc_id), decode($trans_year)) != false ? get_broiler_config_qty_bulk('Old Performance Base', decode($bc_id), decode($trans_year)) : 0;

		$performance_perc_availment = get_broiler_config_qty('Old Performance % Availment', decode($bc_id), decode($trans_year),  $doctype) != false ? get_broiler_config_qty('Old Performance % Availment', decode($bc_id), decode($trans_year),  $doctype) : 0;
		
		if($fcr_input && $hr && $alw && $old_performance_rate && $old_performance_base && $performance_perc_availment){
			$i = 1;
			foreach ($fcr_input as $row) {
				$fcr_input_val[$i] = $row->broiler_budget_qty;
				$fcr_amount_type_val[$i] = $row->amount_type_id;
				$i++;
			}

			$i = 1;
			foreach ($hr as $row) {
				$hr_val[$i] = $row->broiler_budget_qty;
				$hr_amount_type_val[$i] = $row->amount_type_id;
				$i++;
			}
			
			$i = 1;
			foreach ($alw as $row) {
				$alw_val[$i] = $row->broiler_budget_qty;
				$i++;
			}

			$base_1 = 1;
			$base_2 = 1;
			$base_3 = 1;
			$base_4 = 1;
			foreach ($old_performance_base as $row) {
				
				if($row->broiler_subgroup_name == 'ALW Old Performance Base'){
					$alw_old_performance_base_val[$base_1] = $row->config_qty;
					$base_1++;
				}
				if($row->broiler_subgroup_name == 'FCR Old Performance Base'){
					$fcr_old_performance_base_val[$base_2] = $row->config_qty;
					$base_2++;
				}
				if($row->broiler_subgroup_name == 'HR Old Performance Base 1'){
					$hr_old_performance_base_1_val[$base_3] = $row->config_qty;
					$base_3++;
				}
				if($row->broiler_subgroup_name == 'HR Old Performance Base 2'){
					$hr_old_performance_base_2_val[$base_4] = $row->config_qty;
					$base_4++;
				}
			}

			$rate_1 = 1;
			$rate_2 = 1;
			foreach ($old_performance_rate as $row) {
				
				if($row->broiler_subgroup_name == 'Old Performance Rate 1'){
					$old_performance_rate_1_val[$rate_1] = $row->config_qty;
					$rate_1++;
				}
				if($row->broiler_subgroup_name == 'Old Performance Rate 2'){
					$old_performance_rate_2_val[$rate_2] = $row->config_qty;
					$rate_2++;
				}
			}

			$i = 1;
			foreach ($performance_perc_availment as $row) {
				$performance_perc_availment_val[$i] = $row->config_qty;
				$performance_perc_availment_amount_type_val[$i] = $row->amount_type_id;
				$i++;
			}

			$harvested_heads = harvested_heads($bc_id, $trans_year,  $doctype);

			for($month = 1; $month <= 12; $month++){
				//return $alw_old_performance_base_val[$month];
				$fcr_input_val[$month] = amount_type($fcr_input_val[$month], $fcr_amount_type_val[$month]);
				$hr_val[$month] = amount_type($hr_val[$month], $hr_amount_type_val[$month]);
				$performance_perc_availment_val[$month] = amount_type($performance_perc_availment_val[$month], $performance_perc_availment_amount_type_val[$month]);

				if($alw_val[$month] >= $alw_old_performance_base_val[$month] && $fcr_input_val[$month] <= $fcr_old_performance_base_val[$month] && $hr_val[$month] >= $hr_old_performance_base_1_val[$month]) {

					$performance_bonus_base[$month] = $old_performance_rate_1_val[$month];

				} else if (($alw_val[$month] >= $alw_old_performance_base_val[$month] && $hr_val[$month] >= $hr_old_performance_base_2_val[$month]) || ($alw_val[$month] >= $alw_old_performance_base_val[$month] && $fcr_input_val[$month] <= $fcr_old_performance_base_val[$month])){

					$performance_bonus_base[$month] = $old_performance_rate_2_val[$month];

				} else {
					$performance_bonus_base[$month] = 0;
				}
				$result[$month] = $performance_bonus_base[$month] * $harvested_heads[$month] * $performance_perc_availment_val[$month];
				$result[$month] = round($result[$month], 3);
			}
				
			return $result;
		} else {
			return $result = 0;
		}
	}

	function performance_bonus_new($bc_id, $trans_year,  $doctype){

		$harvested_heads = harvested_heads($bc_id, $trans_year,  $doctype);
		$fcr_input = get_broiler_budget_qty(23, decode($bc_id), decode($trans_year),  $doctype) != false ? get_broiler_budget_qty(23, decode($bc_id), decode($trans_year),  $doctype) : 0;
		$hr = get_broiler_budget_qty(3, decode($bc_id), decode($trans_year),  $doctype) != false ? get_broiler_budget_qty(3, decode($bc_id), decode($trans_year),  $doctype) : 0;
		$alw = get_broiler_budget_qty(4, decode($bc_id), decode($trans_year),  $doctype) != false ? get_broiler_budget_qty(4, decode($bc_id), decode($trans_year),  $doctype) : 0;
		$performance_perc_availment = get_broiler_config_qty('New Performance % Availment', decode($bc_id), decode($trans_year),  $doctype) != false ? get_broiler_config_qty('New Performance % Availment', decode($bc_id), decode($trans_year),  $doctype) : 0;

		$new_performance_base = get_broiler_config_qty_bulk('New Performance Base', decode($bc_id), decode($trans_year)) != false ? get_broiler_config_qty_bulk('New Performance Base', decode($bc_id), decode($trans_year)) : 0;
		$new_performance_rate = get_broiler_config_qty_bulk('New Performance Rate', decode($bc_id), decode($trans_year)) != false ? get_broiler_config_qty_bulk('New Performance Rate', decode($bc_id), decode($trans_year)) : 0;
		
		$fcr_performance_margin = get_broiler_config_qty_bulk('FCR Performance Margin', decode($bc_id), decode($trans_year)) != false ? get_broiler_config_qty_bulk('FCR Performance Margin', decode($bc_id), decode($trans_year)) : 0;
		

		if($fcr_input && $hr && $alw && $new_performance_base && $new_performance_rate && $fcr_performance_margin && $performance_perc_availment){
			$i = 1;
			foreach ($fcr_input as $row) {
				$fcr_input_val[$i] = $row->broiler_budget_qty;
				$fcr_amount_type_val[$i] = $row->amount_type_id;
				$i++;
			}

			$i = 1;
			foreach ($hr as $row) {
				$hr_val[$i] = $row->broiler_budget_qty;
				$hr_amount_type_val[$i] = $row->amount_type_id;
				$i++;
			}
			
			$i = 1;
			foreach ($alw as $row) {
				$alw_val[$i] = $row->broiler_budget_qty;
				$i++;
			}

			$i = 1;
			foreach ($performance_perc_availment as $row) {
				$performance_perc_availment_val[$i] = $row->config_qty;
				$performance_perc_availment_amount_type_val[$i] = $row->amount_type_id;
				$i++;
			}

			$base_1 = 1;
			$base_2 = 1;
			$base_3 = 1;
			foreach ($new_performance_base as $row) {
				
				if($row->broiler_subgroup_name == 'ALW New Performance Base'){
					$alw_new_performance_base_val[$base_1] = $row->config_qty;
					$base_1++;
				}
				if($row->broiler_subgroup_name == 'FCR New Performance Base'){
					$fcr_new_performance_base_val[$base_2] = $row->config_qty;
					$base_2++;
				}
				if($row->broiler_subgroup_name == 'HR New Performance Base'){
					$hr_new_performance_base_val[$base_3] = $row->config_qty;
					$base_3++;
				}
			}

			$rate_1 = 1;
			$rate_2 = 1;
			foreach ($new_performance_rate as $row) {
				
				if($row->broiler_subgroup_name == 'New Performance Rate 1'){
					$new_performance_rate_1_val[$rate_1] = $row->config_qty;
					$rate_1++;
				}
				if($row->broiler_subgroup_name == 'New Performance Rate 2'){
					$new_performance_rate_2_val[$rate_2] = $row->config_qty;
					$rate_2++;
				}
			}

			$margin_1 = 1;
			$margin_2 = 1;
			foreach ($fcr_performance_margin as $row) {
				
				if($row->broiler_subgroup_name == 'FCR Performance Margin'){
					$fcr_performance_margin_val[$margin_1] = $row->config_qty;
					$margin_1++;
				}
				if($row->broiler_subgroup_name == 'FCR Performance Margin Rate'){
					$fcr_performance_margin_rate_val[$margin_2] = $row->config_qty;
					$margin_2++;
				}
			}

			for($month = 1; $month <= 12; $month++){

				if( $alw_val[$month] >= $alw_new_performance_base_val[$month] &&
					$hr_val[$month] >= $hr_new_performance_base_val[$month] &&
					$fcr_input_val[$month] <= $fcr_new_performance_base_val[$month] ){
					
					$dynamic_performance_rate[$month] = $new_performance_rate_1_val[$month];

				} else if( $alw_val[$month] >= $alw_new_performance_base_val[$month] &&
					$hr_val[$month] >= $hr_new_performance_base_val[$month] ){
					
					$dynamic_performance_rate[$month] = $new_performance_rate_2_val[$month];

				} else if( $alw_val[$month] >= $alw_new_performance_base_val[$month] &&
					$fcr_input[$month] <= $fcr_new_performance_base_val[$month] ){

					$dynamic_performance_rate[$month] = $new_performance_rate_2_val[$month];

				} else {
					$dynamic_performance_rate[$month] = 0;
				}

				$fcr_difference[$month] = $fcr_new_performance_base_val[$month] - $fcr_input_val[$month];
				if($fcr_difference[$month] >= $fcr_performance_margin_val[$month]){
					if($dynamic_performance_rate[$month] > 0){
						$dynamic_performance_rate[$month] = $dynamic_performance_rate[$month] + $fcr_performance_margin_rate_val[$month];
					}
				}

				$performance_perc_availment_val[$month] = amount_type($performance_perc_availment_val[$month], $performance_perc_availment_amount_type_val[$month]);

				$initial[$month] = $harvested_heads[$month] * $dynamic_performance_rate[$month];
				$result[$month] = $initial[$month] * $performance_perc_availment_val[$month];
				$result[$month] = round($result[$month], 3);

			}
			return $result;
		} else {
			return $result = 0;
		}
	}

	function brooding_incentive($bc_id, $trans_year,  $doctype){
		$brooding_incentive_input = get_broiler_budget_qty(12, decode($bc_id), decode($trans_year),  $doctype) != false ? get_broiler_budget_qty(12, decode($bc_id), decode($trans_year),  $doctype) : 0;

		$brooding_perc_availment = get_broiler_config_qty('Brooding Incentive % Availment', decode($bc_id), decode($trans_year),  $doctype) != false ? get_broiler_config_qty('Brooding Incentive % Availment', decode($bc_id), decode($trans_year),  $doctype) : 0;

		if($brooding_incentive_input && $brooding_perc_availment){
			$i = 1;
			foreach ($brooding_incentive_input as $row) {
				$brooding_incentive_input_val[$i] = $row->broiler_budget_qty;
				$i++;
			}
			$i = 1;
			foreach ($brooding_perc_availment as $row) {
				$brooding_perc_availment_val[$i] = $row->config_qty;
				$brooding_perc_availment_amount_type_val[$i] = $row->amount_type_id;
				$i++;
			}
			$harvested_heads = harvested_heads($bc_id, $trans_year,  $doctype);
			for($month = 1; $month <= 12; $month++){
				$brooding_perc_availment_val[$month] = amount_type($brooding_perc_availment_val[$month], $brooding_perc_availment_amount_type_val[$month]);

				$result[$month] = $harvested_heads[$month] * $brooding_incentive_input_val[$month] * $brooding_perc_availment_val[$month];
				$result[$month] = round($result[$month], 3);
			}
			
			return $result;
		} else {
			return $result = 0;
		}
	}

	function feeds_efficiency_bonus($bc_id, $trans_year,  $doctype){

		//add the rate for 3 range;
		$fcr_input = get_broiler_budget_qty(23, decode($bc_id), decode($trans_year),  $doctype) != false ? get_broiler_budget_qty(23, decode($bc_id), decode($trans_year),  $doctype) : 0;
		$fcr_efficiency_config_1 = get_broiler_config_qty('FCR Efficiency Bonus 1', decode($bc_id), decode($trans_year),  $doctype) != false ? get_broiler_config_qty('FCR Efficiency Bonus 1', decode($bc_id), decode($trans_year),  $doctype) : 0;
		$fcr_efficiency_config_2 = get_broiler_config_qty('FCR Efficiency Bonus 2', decode($bc_id), decode($trans_year),  $doctype) != false ? get_broiler_config_qty('FCR Efficiency Bonus 2', decode($bc_id), decode($trans_year),  $doctype) : 0;
		$fcr_efficiency_config_3 = get_broiler_config_qty('FCR Efficiency Bonus 3', decode($bc_id), decode($trans_year),  $doctype) != false ? get_broiler_config_qty('FCR Efficiency Bonus 3', decode($bc_id), decode($trans_year),  $doctype) : 0;

		$fcr_efficiency_rate = get_broiler_config_qty_bulk('FCR Efficiency Rate', decode($bc_id), decode($trans_year)) != false ? get_broiler_config_qty_bulk('FCR Efficiency Rate', decode($bc_id), decode($trans_year)) : 0;

		$feeds_perc_availment = get_broiler_config_qty('Feeds Efficiency % Availment', decode($bc_id), decode($trans_year),  $doctype) != false ? get_broiler_config_qty('Feeds Efficiency % Availment', decode($bc_id), decode($trans_year),  $doctype) : 0;
		
		if($fcr_input && $fcr_efficiency_config_1 && $fcr_efficiency_config_2 && $fcr_efficiency_config_3 && $fcr_efficiency_rate && $feeds_perc_availment){
			$i = 1;
			foreach ($fcr_input as $row) {
				$fcr_input_val[$i] = $row->broiler_budget_qty;
				$fcr_amount_type_val[$i] = $row->amount_type_id;
				$i++;
			}

			$i = 1;
			foreach ($fcr_efficiency_config_1 as $row) {
				$fcr_efficiency_config_1_val[$i] = $row->config_qty;
				$i++;
			}
			
			$i = 1;
			foreach ($fcr_efficiency_config_2 as $row) {
				$fcr_efficiency_config_2_val[$i] = $row->config_qty;
				$i++;
			}
			
			$i = 1;
			foreach ($fcr_efficiency_config_3 as $row) {
				$fcr_efficiency_config_3_val[$i] = $row->config_qty;
				$i++;
			}

			$i = 1;
			foreach ($feeds_perc_availment as $row) {
				$feeds_perc_availment_val[$i] = $row->config_qty;
				$feeds_perc_availment_amount_type_val[$i] = $row->amount_type_id;
				$i++;
			}

			$rate_1 = 1;
			$rate_2 = 1;
			$rate_3 = 1;
			foreach ($fcr_efficiency_rate as $row) {
				
				if($row->broiler_subgroup_name == 'FCR Efficiency Rate 1'){
					$fcr_efficiency_rate_1_val[$rate_1] = $row->config_qty;
					$rate_1++;
				}
				if($row->broiler_subgroup_name == 'FCR Efficiency Rate 2'){
					$fcr_efficiency_rate_2_val[$rate_2] = $row->config_qty;
					$rate_2++;
				}
				if($row->broiler_subgroup_name == 'FCR Efficiency Rate 3'){
					$fcr_efficiency_rate_3_val[$rate_3] = $row->config_qty;
					$rate_3++;
				}
			}

			$harvested_heads = harvested_heads($bc_id, $trans_year,  $doctype);

			for($month = 1; $month <= 12; $month++){
				$fcr_input_val[$month] = amount_type($fcr_input_val[$month], $fcr_amount_type_val[$month]);

				$feeds_perc_availment_val[$month] = amount_type($feeds_perc_availment_val[$month], $feeds_perc_availment_amount_type_val[$month]);

				if($fcr_input_val[$month] <= $fcr_efficiency_config_1_val[$month]){
					$feeds_efficiency_base[$month] = $fcr_efficiency_rate_1_val[$month];
				} else if($fcr_input_val[$month] <= $fcr_efficiency_config_2_val[$month] &&  $fcr_input_val[$month] > $fcr_efficiency_config_1_val[$month]){
					$feeds_efficiency_base[$month] = $fcr_efficiency_rate_2_val[$month];
				} else if($fcr_input_val[$month] <= $fcr_efficiency_config_3_val[$month] &&  $fcr_input_val[$month] > $fcr_efficiency_config_2_val[$month]){
					$feeds_efficiency_base[$month] = $fcr_efficiency_rate_3_val[$month];
				} else {
					$feeds_efficiency_base[$month] = 0;
				}
				$result[$month] = $feeds_efficiency_base[$month] * $harvested_heads[$month] * $feeds_perc_availment_val[$month];
				$result[$month] = round($result[$month], 3);
			}
				
			return $result;
		} else {
			return $result = 0;
		}
	}

	function bpi_incentive($bc_id, $trans_year,  $doctype){
		$bpi_perc_availment = get_broiler_config_qty('BPI % Availment', decode($bc_id), decode($trans_year),  $doctype) != false ? get_broiler_config_qty('BPI % Availment', decode($bc_id), decode($trans_year),  $doctype) : 0;
		$bpi_incentive_input = get_broiler_budget_qty(14, decode($bc_id), decode($trans_year),  $doctype) != false ? get_broiler_budget_qty(14, decode($bc_id), decode($trans_year),  $doctype) : 0;

		if($bpi_perc_availment && $bpi_incentive_input){
			$i = 1;
			foreach ($bpi_perc_availment as $row) {
				$bpi_perc_availment_val[$i] = $row->config_qty;
				$bpi_perc_amount_type_val[$i] = $row->amount_type_id;
				$i++;
			}
			
			$i = 1;
			foreach ($bpi_incentive_input as $row) {
				$bpi_incentive_input_val[$i] = $row->broiler_budget_qty;
				$i++;
			}
			$harvested_heads = harvested_heads($bc_id, $trans_year,  $doctype);
			for($month = 1; $month <= 12; $month++){
				$bpi_perc_availment_val[$month] = amount_type($bpi_perc_availment_val[$month], $bpi_perc_amount_type_val[$month]);

				$initial[$month] = $harvested_heads[$month] * $bpi_perc_availment_val[$month];
				$result[$month] = $initial[$month] * $bpi_incentive_input_val[$month];
				$result[$month] = round($result[$month], 3);
			}
				
			return $result;
		} else {
			return $result = 0;
		}
	}

	function bei_value($bc_id, $trans_year,  $doctype){
		$fcr_input = get_broiler_budget_qty(23, decode($bc_id), decode($trans_year),  $doctype) != false ? get_broiler_budget_qty(23, decode($bc_id), decode($trans_year),  $doctype) : 0;
		$hr = get_broiler_budget_qty(3, decode($bc_id), decode($trans_year),  $doctype) != false ? get_broiler_budget_qty(3, decode($bc_id), decode($trans_year),  $doctype) : 0;
		$alw = get_broiler_budget_qty(4, decode($bc_id), decode($trans_year),  $doctype) != false ? get_broiler_budget_qty(4, decode($bc_id), decode($trans_year),  $doctype) : 0;
		if($fcr_input && $hr && $alw){
			$i = 1;
			foreach ($fcr_input as $row) {
				$fcr_input_val[$i] = $row->broiler_budget_qty;
				$fcr_amount_type_val[$i] = $row->amount_type_id;
				$i++;
			}

			$i = 1;
			foreach ($hr as $row) {
				$hr_val[$i] = $row->broiler_budget_qty;
				$hr_amount_type_val[$i] = $row->amount_type_id;
				$i++;
			}
			
			$i = 1;
			foreach ($alw as $row) {
				$alw_val[$i] = $row->broiler_budget_qty;
				$i++;
			}
			for($month = 1; $month <= 12; $month++){
				$initial[$month] = $alw_val[$month] * $hr_val[$month] / $fcr_input_val[$month];
				$result[$month] = round($initial[$month], 3);
			}
			return $result;
		} else {
			return $result = 0;
		}
	}

	function bei_incentive($bc_id, $trans_year,  $doctype){

		$bei_perc_availment = get_broiler_config_qty('BEI % Availment', decode($bc_id), decode($trans_year),  $doctype) != false ? get_broiler_config_qty('BEI % Availment', decode($bc_id), decode($trans_year),  $doctype) : 0;
		
		$bei_range = get_broiler_config_qty_bulk('BEI Range', decode($bc_id), decode($trans_year)) != false ? get_broiler_config_qty_bulk('BEI Range', decode($bc_id), decode($trans_year)) : 0;
		
		$bei_rate = get_broiler_config_qty_bulk('BEI Rate', decode($bc_id), decode($trans_year)) != false ? get_broiler_config_qty_bulk('BEI Rate', decode($bc_id), decode($trans_year)) : 0;

		if($bei_range && $bei_rate && $bei_perc_availment){
			$i = 1;
			foreach ($bei_perc_availment as $row) {
				$bei_perc_availment_val[$i] = $row->config_qty;
				$bei_perc_amount_type_val[$i] = $row->amount_type_id;
				$i++;
			}

			$range_1 = 1;
			$range_2 = 1;
			$range_3 = 1;
			$range_4 = 1;
			$range_5 = 1;
			foreach ($bei_range as $row) {
				
				if($row->broiler_subgroup_name == 'BEI Range 1'){
					$bei_range_1_val[$range_1] = $row->config_qty;
					$range_1++;
				}
				if($row->broiler_subgroup_name == 'BEI Range 2'){
					$bei_range_2_val[$range_2] = $row->config_qty;
					$range_2++;
				}
				if($row->broiler_subgroup_name == 'BEI Range 3'){
					$bei_range_3_val[$range_3] = $row->config_qty;
					$range_3++;
				}
				if($row->broiler_subgroup_name == 'BEI Range 4'){
					$bei_range_4_val[$range_4] = $row->config_qty;
					$range_4++;
				}
				if($row->broiler_subgroup_name == 'BEI Range 5'){
					$bei_range_5_val[$range_5] = $row->config_qty;
					$range_5++;
				}
			}

			$rate_1 = 1;
			$rate_2 = 1;
			$rate_3 = 1;
			$rate_4 = 1;
			$rate_5 = 1;
			$rate_6 = 1;
			foreach ($bei_rate as $row) {
				if($row->broiler_subgroup_name == 'BEI Rate 1'){
					$bei_rate_1_val[$rate_1] = $row->config_qty;
					$rate_1++;
				}
				if($row->broiler_subgroup_name == 'BEI Rate 2'){
					$bei_rate_2_val[$rate_2] = $row->config_qty;
					$rate_2++;
				}
				if($row->broiler_subgroup_name == 'BEI Rate 3'){
					$bei_rate_3_val[$rate_3] = $row->config_qty;
					$rate_3++;
				}
				if($row->broiler_subgroup_name == 'BEI Rate 4'){
					$bei_rate_4_val[$rate_4] = $row->config_qty;
					$rate_4++;
				}
				if($row->broiler_subgroup_name == 'BEI Rate 5'){
					$bei_rate_5_val[$rate_5] = $row->config_qty;
					$rate_5++;
				}
				
			}

			$harvested_heads = harvested_heads($bc_id, $trans_year,  $doctype);
			$bei_value = bei_value($bc_id, $trans_year,  $doctype);

			for($month = 1; $month <= 12; $month++){

				if($bei_value[$month] >= $bei_range_1_val[$month]){
					
					$dynamic_bei_incentive[$month] = $bei_rate_1_val[$month];

				} else if($bei_value[$month] <= $bei_range_2_val[$month] && $bei_value[$month] > $bei_range_3_val[$month]){
					
					$dynamic_bei_incentive[$month] = $bei_rate_2_val[$month];

				} else if($bei_value[$month] <= $bei_range_3_val[$month] && $bei_value[$month] > $bei_range_4_val[$month]){

					$dynamic_bei_incentive[$month] = $bei_rate_3_val[$month];

				} else if($bei_value[$month] <= $bei_range_4_val[$month] && $bei_value[$month] > $bei_range_5_val[$month]){

					$dynamic_bei_incentive[$month] = $bei_rate_4_val[$month];

				} else if($bei_value[$month] <= $bei_range_5_val[$month]){

					$dynamic_bei_incentive[$month] = $bei_rate_5_val[$month];

				}
				$bei_perc_availment_val[$month] = amount_type($bei_perc_availment_val[$month], $bei_perc_amount_type_val[$month]);
				$initial[$month] = $harvested_heads[$month] * $dynamic_bei_incentive[$month];
				$result[$month] = $initial[$month] * $bei_perc_availment_val[$month];
				//$result[$month] = $harvested_heads[$month] .' * '. $bei_value[$month];
				$result[$month] = round($result[$month], 3);
			}
				
			return $result;
		} else {
			return $result = 0;
		}
	}

	function special_loyalty_incentive($bc_id, $trans_year,  $doctype){
		
		$spcl_loyalty_perc_availment = get_broiler_config_qty('SPCL Loyalty % Availment', decode($bc_id), decode($trans_year),  $doctype) != false ? get_broiler_config_qty('SPCL Loyalty % Availment', decode($bc_id), decode($trans_year),  $doctype) : 0;
		
		$spcl_loyalty_range = get_broiler_config_qty_bulk('SPCL Loyalty Range', decode($bc_id), decode($trans_year)) != false ? get_broiler_config_qty_bulk('SPCL Loyalty Range', decode($bc_id), decode($trans_year)) : 0;
		
		$spcl_loyalty_rate = get_broiler_config_qty_bulk('SPCL Loyalty Rate', decode($bc_id), decode($trans_year)) != false ? get_broiler_config_qty_bulk('SPCL Loyalty Rate', decode($bc_id), decode($trans_year)) : 0;

		if($spcl_loyalty_range && $spcl_loyalty_rate && $spcl_loyalty_perc_availment){
			$i = 1;
			foreach ($spcl_loyalty_perc_availment as $row) {
				$spcl_loyalty_perc_availment_val[$i] = $row->config_qty;
				$spcl_loyalty_perc_amount_type_val[$i] = $row->amount_type_id;
				$i++;
			}

			$range_1 = 1;
			$range_2 = 1;
			$range_3 = 1;
			$range_4 = 1;
			$range_5 = 1;
			foreach ($spcl_loyalty_range as $row) {
				
				if($row->broiler_subgroup_name == 'SPCL Loyalty Range 1'){
					$spcl_loyalty_range_1_val[$range_1] = $row->config_qty;
					$range_1++;
				}
				if($row->broiler_subgroup_name == 'SPCL Loyalty Range 2'){
					$spcl_loyalty_range_2_val[$range_2] = $row->config_qty;
					$range_2++;
				}
				if($row->broiler_subgroup_name == 'SPCL Loyalty Range 3'){
					$spcl_loyalty_range_3_val[$range_3] = $row->config_qty;
					$range_3++;
				}
				if($row->broiler_subgroup_name == 'SPCL Loyalty Range 4'){
					$spcl_loyalty_range_4_val[$range_4] = $row->config_qty;
					$range_4++;
				}
				if($row->broiler_subgroup_name == 'SPCL Loyalty Range 5'){
					$spcl_loyalty_range_5_val[$range_5] = $row->config_qty;
					$range_5++;
				}
			}

			$rate_1 = 1;
			$rate_2 = 1;
			$rate_3 = 1;
			$rate_4 = 1;
			$rate_5 = 1;
			$rate_6 = 1;
			foreach ($spcl_loyalty_rate as $row) {
				if($row->broiler_subgroup_name == 'SPCL Loyalty Rate 1'){
					$spcl_loyalty_rate_1_val[$rate_1] = $row->config_qty;
					$rate_1++;
				}
				if($row->broiler_subgroup_name == 'SPCL Loyalty Rate 2'){
					$spcl_loyalty_rate_2_val[$rate_2] = $row->config_qty;
					$rate_2++;
				}
				if($row->broiler_subgroup_name == 'SPCL Loyalty Rate 3'){
					$spcl_loyalty_rate_3_val[$rate_3] = $row->config_qty;
					$rate_3++;
				}
				if($row->broiler_subgroup_name == 'SPCL Loyalty Rate 4'){
					$spcl_loyalty_rate_4_val[$rate_4] = $row->config_qty;
					$rate_4++;
				}
				if($row->broiler_subgroup_name == 'SPCL Loyalty Rate 5'){
					$spcl_loyalty_rate_5_val[$rate_5] = $row->config_qty;
					$rate_5++;
				}
				if($row->broiler_subgroup_name == 'SPCL Loyalty Rate 6'){
					$spcl_loyalty_rate_6_val[$rate_6] = $row->config_qty;
					$rate_6++;
				}
				
			}

			$harvested_heads = harvested_heads($bc_id, $trans_year,  $doctype);
			$bei_value = bei_value($bc_id, $trans_year,  $doctype);

			for($month = 1; $month <= 12; $month++){

				if($bei_value[$month] >= $spcl_loyalty_range_1_val[$month]){
					
					$dynamic_bei_incentive[$month] = $spcl_loyalty_rate_1_val[$month];

				} else if($bei_value[$month] <= $spcl_loyalty_range_2_val[$month] && $bei_value[$month] > $spcl_loyalty_range_3_val[$month]){
					
					$dynamic_bei_incentive[$month] = $spcl_loyalty_rate_2_val[$month];

				} else if($bei_value[$month] <= $spcl_loyalty_range_3_val[$month] && $bei_value[$month] > $spcl_loyalty_range_4_val[$month]){

					$dynamic_bei_incentive[$month] = $spcl_loyalty_rate_3_val[$month];

				} else if($bei_value[$month] <= $spcl_loyalty_range_4_val[$month] && $bei_value[$month] > $spcl_loyalty_range_5_val[$month]){

					$dynamic_bei_incentive[$month] = $spcl_loyalty_rate_4_val[$month];

				} else if($bei_value[$month] <= $spcl_loyalty_range_5_val[$month]){

					$dynamic_bei_incentive[$month] = $spcl_loyalty_rate_5_val[$month];

				} else {
					
					$dynamic_bei_incentive[$month] = $spcl_loyalty_rate_6_val[$month];

				}
				$spcl_loyalty_perc_availment_val[$month] = amount_type($spcl_loyalty_perc_availment_val[$month], $spcl_loyalty_perc_amount_type_val[$month]);
				$initial[$month] = $harvested_heads[$month] * $dynamic_bei_incentive[$month];
				$result[$month] = $initial[$month] * $spcl_loyalty_perc_availment_val[$month];
				$result[$month] = round($result[$month], 3);
			}
				
			return $result;
		} else {
			return $result = 0;
		}
	}

	function weight_incentive($bc_id, $trans_year,  $doctype){

		$harvested_heads = harvested_heads($bc_id, $trans_year,  $doctype);
		$harvested_kilo = harvested_kilo($bc_id, $trans_year,  $doctype);

		$weight_incentive_base_1 = get_broiler_config_qty('Weight Incentive Base 1', decode($bc_id), decode($trans_year),  $doctype) != false ? get_broiler_config_qty('Weight Incentive Base 1', decode($bc_id), decode($trans_year),  $doctype) : 0;
		$weight_incentive_base_2 = get_broiler_config_qty('Weight Incentive Base 2', decode($bc_id), decode($trans_year),  $doctype) != false ? get_broiler_config_qty('Weight Incentive Base 2', decode($bc_id), decode($trans_year),  $doctype) : 0;
		$weight_incentive_input = get_broiler_budget_qty(15, decode($bc_id), decode($trans_year),  $doctype) != false ? get_broiler_budget_qty(15, decode($bc_id), decode($trans_year),  $doctype) : 0;

		$alw = get_broiler_budget_qty(4, decode($bc_id), decode($trans_year),  $doctype) != false ? get_broiler_budget_qty(4, decode($bc_id), decode($trans_year),  $doctype) : 0;
		
		if($weight_incentive_base_1 && $weight_incentive_base_2 && $weight_incentive_input && $alw){
			
			
			$i = 1;
			foreach ($weight_incentive_base_1 as $row) {
				$weight_incentive_base_1_val[$i] = $row->config_qty;
				$i++;
			}
			
			$i = 1;
			foreach ($weight_incentive_base_2 as $row) {
				$weight_incentive_base_2_val[$i] = $row->config_qty;
				$i++;
			}
			
			$i = 1;
			foreach ($weight_incentive_input as $row) {
				$weight_incentive_input_val[$i] = $row->broiler_budget_qty;
				$i++;
			}

			$i = 1;
			foreach ($alw as $row) {
				$alw_val[$i] = $row->broiler_budget_qty;
				$i++;
			}

			for($month = 1; $month <= 12; $month++){
				if($alw_val[$month] > $weight_incentive_base_2_val[$month]){

					$initial1[$month] = $harvested_heads[$month] * $weight_incentive_base_1_val[$month];
					$initial2[$month] = $harvested_heads[$month] * $weight_incentive_base_2_val[$month];

					$excess_weight[$month] = $initial2[$month] - $initial1[$month];
					
				} else if ($alw_val[$month] > $weight_incentive_base_1_val[$month]){
					$initial[$month] = $harvested_heads[$month] * $weight_incentive_base_1_val[$month];
					$excess_weight[$month] = $harvested_kilo[$month] - $initial[$month];
				}
				$result[$month] = $excess_weight[$month] * $weight_incentive_input_val[$month];
				$result[$month] = round($result[$month], 3);
			}
			return $result;
		} else {
			return $result = 0;
		}
	}

	function tunnel_vent_incentive($bc_id, $trans_year,  $doctype){
		$harvested_heads = harvested_heads($bc_id, $trans_year,  $doctype);

		$tunnel_vent_perc = get_broiler_budget_qty(29, decode($bc_id), decode($trans_year),  $doctype) != false ? get_broiler_budget_qty(29, decode($bc_id), decode($trans_year),  $doctype) : 0;
		$tunnel_vent_input = get_broiler_budget_qty(16, decode($bc_id), decode($trans_year),  $doctype) != false ? get_broiler_budget_qty(16, decode($bc_id), decode($trans_year),  $doctype) : 0;
		
		if($tunnel_vent_perc && $tunnel_vent_input){
			$i = 1;
			foreach ($tunnel_vent_perc as $row) {
				$tunnel_vent_perc_val[$i] = $row->broiler_budget_qty;
				$tunnel_vent_perc_amount_type_val[$i] = $row->amount_type_id;
				$i++;
			}
			
			$i = 1;
			foreach ($tunnel_vent_input as $row) {
				$tunnel_vent_input_val[$i] = $row->broiler_budget_qty;
				$i++;
			}

			for($month = 1; $month <= 12; $month++){
				$tunnel_vent_perc_val[$month] = amount_type($tunnel_vent_perc_val[$month], $tunnel_vent_perc_amount_type_val[$month]);
				$initial[$month] = $harvested_heads[$month] * $tunnel_vent_perc_val[$month];
				$result[$month] = $initial[$month] * $tunnel_vent_input_val[$month];
				$result[$month] = round($result[$month], 3);
			}
			return $result;
		} else {
			return $result = 0;
		}
	}

	function construction_incentive($bc_id, $trans_year,  $doctype){
		$harvested_heads = harvested_heads($bc_id, $trans_year,  $doctype);

		$new_bldg_perc = get_broiler_budget_qty(30, decode($bc_id), decode($trans_year),  $doctype) != false ? get_broiler_budget_qty(30, decode($bc_id), decode($trans_year),  $doctype) : 0;

		$construction_incentive_input = get_broiler_budget_qty(17, decode($bc_id), decode($trans_year),  $doctype) != false ? get_broiler_budget_qty(17, decode($bc_id), decode($trans_year),  $doctype) : 0;
		
		if($new_bldg_perc && $construction_incentive_input){
			$i = 1;
			foreach ($new_bldg_perc as $row) {
				$new_bldg_perc_val[$i] = $row->broiler_budget_qty;
				$new_bldg_perc_amount_type_val[$i] = $row->amount_type_id;
				$i++;
			}
			
			$i = 1;
			foreach ($construction_incentive_input as $row) {
				$construction_incentive_input_val[$i] = $row->broiler_budget_qty;
				$i++;
			}

			for($month = 1; $month <= 12; $month++){
				$new_bldg_perc_val[$month] = amount_type($new_bldg_perc_val[$month], $new_bldg_perc_amount_type_val[$month]);
				$initial[$month] = $harvested_heads[$month] * $new_bldg_perc_val[$month];
				$result[$month] = $initial[$month] * $construction_incentive_input_val[$month];
				$result[$month] = round($result[$month], 3);
			}
			
			return $result;
		} else {
			return $result = 0;
		}
	}

	function cleaning_incentive($bc_id, $trans_year,  $doctype){
		$harvested_heads = harvested_heads($bc_id, $trans_year,  $doctype);

		$nae_percentage = get_broiler_budget_qty(24, decode($bc_id), decode($trans_year),  $doctype) != false ? get_broiler_budget_qty(24, decode($bc_id), decode($trans_year),  $doctype) : 0;
		$cleaning_incentive_input = get_broiler_budget_qty(18, decode($bc_id), decode($trans_year),  $doctype) != false ? get_broiler_budget_qty(18, decode($bc_id), decode($trans_year),  $doctype) : 0;
		
		if($nae_percentage && $cleaning_incentive_input){
			$i = 1;
			foreach ($nae_percentage as $row) {
				$nae_percentage_val[$i] = $row->broiler_budget_qty;
				$nae_percentage_amount_type_val[$i] = $row->amount_type_id;
				$i++;
			}

			$i = 1;
			foreach ($cleaning_incentive_input as $row) {
				$cleaning_incentive_input_val[$i] = $row->broiler_budget_qty;
				$i++;
			}

			for($month = 1; $month <= 12; $month++){
				$nae_percentage_val[$month] = amount_type($nae_percentage_val[$month], $nae_percentage_amount_type_val[$month]);
				$initial[$month] = $harvested_heads[$month] * $nae_percentage_val[$month];
				$result[$month] = $initial[$month] * $cleaning_incentive_input_val[$month];
				$result[$month] = round($result[$month], 3);
			}
				
			return $result;
		} else {
			return $result = 0;
		}
	}

	function recruitment_incentive($bc_id, $trans_year,  $doctype){
		$harvested_heads = harvested_heads($bc_id, $trans_year,  $doctype);
		$recruitment_incentive_input = get_broiler_budget_qty(19, decode($bc_id), decode($trans_year),  $doctype) != false ? get_broiler_budget_qty(19, decode($bc_id), decode($trans_year),  $doctype) : 0;

		if($recruitment_incentive_input){
			$i = 1;
			foreach ($recruitment_incentive_input as $row) {
				$recruitment_incentive_input_val[$i] = $row->broiler_budget_qty;
				$i++;
			}
			for($month = 1; $month <= 12; $month++){
				$result[$month] = $harvested_heads[$month] * $recruitment_incentive_input_val[$month];
				$result[$month] = round($result[$month], 3);
			}
			
			return $result;
		} else {
			return $result = 0;
		}
	}

	function estimated_cg_fee_adj($bc_id, $trans_year,  $doctype){
		$estimated_cg_fee_adj_input = get_broiler_budget_qty(20, decode($bc_id), decode($trans_year),  $doctype) != false ? get_broiler_budget_qty(20, decode($bc_id), decode($trans_year),  $doctype) : 0;

		if($estimated_cg_fee_adj_input){
			$i = 1;
			foreach ($estimated_cg_fee_adj_input as $row) {
				$estimated_cg_fee_adj_input_val[$i] = $row->broiler_budget_qty;
				$i++;
			}
			for($month = 1; $month <= 12; $month++){
				$result[$month] = $estimated_cg_fee_adj_input_val[$month];
				$result[$month] = round($result[$month], 3);
			}
			return $result;
		} else {
			return $result = 0;
		}
	}

	function farm_management_incentive($bc_id, $trans_year,  $doctype){
		$harvested_heads = harvested_heads($bc_id, $trans_year,  $doctype);
		$farm_management_incentive_input = get_broiler_budget_qty(49, decode($bc_id), decode($trans_year),  $doctype) != false ? get_broiler_budget_qty(49, decode($bc_id), decode($trans_year),  $doctype) : 0;

		if($farm_management_incentive_input){
			$i = 1;
			foreach ($farm_management_incentive_input as $row) {
				$farm_management_incentive_input_val[$i] = $row->broiler_budget_qty;
				$i++;
			}
			for($month = 1; $month <= 12; $month++){
				$result[$month] = $harvested_heads[$month] * $farm_management_incentive_input_val[$month];
				$result[$month] = round($result[$month], 3);
			}
			
			return $result;
		} else {
			return $result = 0;
		}
	}

	function nae_incentive($bc_id, $trans_year,  $doctype){
		$harvested_heads = harvested_heads($bc_id, $trans_year,  $doctype);
		$nae_incentive_input = get_broiler_budget_qty(50, decode($bc_id), decode($trans_year),  $doctype) != false ? get_broiler_budget_qty(50, decode($bc_id), decode($trans_year),  $doctype) : 0;
		$nae_percentage = get_broiler_budget_qty(24, decode($bc_id), decode($trans_year),  $doctype) != false ? get_broiler_budget_qty(24, decode($bc_id), decode($trans_year),  $doctype) : 0;

		if($nae_incentive_input && $nae_percentage){
			$i = 1;
			foreach ($nae_incentive_input as $row) {
				$nae_incentive_input_val[$i] = $row->broiler_budget_qty;
				$i++;
			}
			$i = 1;
			foreach ($nae_percentage as $row) {
				$nae_percentage_val[$i] = $row->broiler_budget_qty;
				$nae_percentage_amount_type_val[$i] = $row->amount_type_id;
				$i++;
			}
			for($month = 1; $month <= 12; $month++){
				$nae_percentage_val[$month] = amount_type($nae_percentage_val[$month], $nae_percentage_amount_type_val[$month]);
				$result[$month] = $harvested_heads[$month] * $nae_percentage_val[$month] * $nae_incentive_input_val[$month];
				$result[$month] = round($result[$month], 3);
				
			}
			
			return $result;
		} else {
			return $result = 0;
		}
	}

	function nae_plus_incentive($bc_id, $trans_year,  $doctype){
		$harvested_heads = harvested_heads($bc_id, $trans_year,  $doctype);
		$nae_plus_incentive_input = get_broiler_budget_qty(51, decode($bc_id), decode($trans_year),  $doctype) != false ? get_broiler_budget_qty(51, decode($bc_id), decode($trans_year),  $doctype) : 0;
		$nae_percentage = get_broiler_budget_qty(24, decode($bc_id), decode($trans_year),  $doctype) != false ? get_broiler_budget_qty(24, decode($bc_id), decode($trans_year),  $doctype) : 0;

		$nae_plus_perc_availment = get_broiler_config_qty('NAE Plus % Availment', decode($bc_id), decode($trans_year),  $doctype) != false ? get_broiler_config_qty('NAE Plus % Availment', decode($bc_id), decode($trans_year),  $doctype) : 0;

		if($nae_plus_incentive_input && $nae_percentage && $nae_plus_perc_availment){
			$i = 1;
			foreach ($nae_plus_incentive_input as $row) {
				$nae_plus_incentive_input_val[$i] = $row->broiler_budget_qty;
				$i++;
			}
			$i = 1;
			foreach ($nae_percentage as $row) {
				$nae_percentage_val[$i] = $row->broiler_budget_qty;
				$nae_percentage_amount_type_val[$i] = $row->amount_type_id;
				$i++;
			}

			$i = 1;
			foreach ($nae_plus_perc_availment as $row) {
				$nae_plus_perc_availment_val[$i] = $row->config_qty;
				$nae_plus_perc_availment_amount_type_val[$i] = $row->amount_type_id;
				$i++;
			}

			for($month = 1; $month <= 12; $month++){
				$nae_percentage_val[$month] = amount_type($nae_percentage_val[$month], $nae_percentage_amount_type_val[$month]);
				$nae_plus_perc_availment_val[$month] = amount_type($nae_plus_perc_availment_val[$month], $nae_plus_perc_availment_amount_type_val[$month]);
				$result[$month] = $harvested_heads[$month] * $nae_percentage_val[$month] * $nae_plus_incentive_input_val[$month] * $nae_plus_perc_availment_val[$month];
				$result[$month] = round($result[$month], 3);
			}
			return $result;
		} else {
			return $result = 0;
		}
	}

	function loyalty_incentive($bc_id, $trans_year,  $doctype){
		$harvested_heads = harvested_heads($bc_id, $trans_year,  $doctype);
		$loyalty_incentive_input = get_broiler_budget_qty(52, decode($bc_id), decode($trans_year),  $doctype) != false ? get_broiler_budget_qty(52, decode($bc_id), decode($trans_year),  $doctype) : 0;

		if($loyalty_incentive_input){
			$i = 1;
			foreach ($loyalty_incentive_input as $row) {
				$loyalty_incentive_input_val[$i] = $row->broiler_budget_qty;
				$i++;
			}
			for($month = 1; $month <= 12; $month++){
				$result[$month] = $harvested_heads[$month] * $loyalty_incentive_input_val[$month];
				$result[$month] = round($result[$month], 3);
			}
			
			return $result;
		} else {
			return $result = 0;
		}
	}

	function alw_adjustment($bc_id, $trans_year,  $doctype){
		$harvested_heads = harvested_heads($bc_id, $trans_year,  $doctype);
		$alw_adjustment_input = get_broiler_budget_qty(110, decode($bc_id), decode($trans_year),  $doctype) != false ? get_broiler_budget_qty(110, decode($bc_id), decode($trans_year),  $doctype) : 0;
		if($alw_adjustment_input){
			$i = 1;
			foreach ($alw_adjustment_input as $row) {
				$alw_adjustment_input_val[$i] = $row->broiler_budget_qty;
				$i++;
			}
			for($month = 1; $month <= 12; $month++){
				$result[$month] = $harvested_heads[$month] * $alw_adjustment_input_val[$month];
				$result[$month] = round($result[$month], 3);
			}
			return $result;
		} else {
			return $result = 0;
		}
	}

	function weight_incentive_2($bc_id, $trans_year,  $doctype){

		$harvested_heads = harvested_heads($bc_id, $trans_year,  $doctype);
		$weight_incentive_2 = get_broiler_config_qty_bulk('Weight Incentive 2', decode($bc_id), decode($trans_year)) != false ? get_broiler_config_qty_bulk('Weight Incentive 2', decode($bc_id), decode($trans_year)) : 0;

		$live_sales_alw = get_broiler_config_qty('Live Sales ALW', decode($bc_id), decode($trans_year),  $doctype) != false ? get_broiler_config_qty('Live Sales ALW', decode($bc_id), decode($trans_year),  $doctype) : 0;
		$weight_incentive_2_input = get_broiler_budget_qty(114, decode($bc_id), decode($trans_year),  $doctype) != false ? get_broiler_budget_qty(114, decode($bc_id), decode($trans_year),  $doctype) : 0;
		$alw = get_broiler_budget_qty(4, decode($bc_id), decode($trans_year),  $doctype) != false ? get_broiler_budget_qty(4, decode($bc_id), decode($trans_year),  $doctype) : 0;

		$fcr_input = get_broiler_budget_qty(23, decode($bc_id), decode($trans_year),  $doctype) != false ? get_broiler_budget_qty(23, decode($bc_id), decode($trans_year),  $doctype) : 0;
		$harvest_live_sales = get_broiler_budget_qty(31, decode($bc_id), decode($trans_year),  $doctype) != false ? get_broiler_budget_qty(31, decode($bc_id), decode($trans_year),  $doctype) : 0;

		if($weight_incentive_2 && $live_sales_alw && $weight_incentive_2_input && $alw && $fcr_input && $harvest_live_sales){
			$i = 1;
			foreach ($alw as $row) {
				$alw_val[$i] = $row->broiler_budget_qty;
				$i++;
			}

			$i = 1;
			foreach ($weight_incentive_2_input as $row) {
				$weight_incentive_2_input_val[$i] = $row->broiler_budget_qty;
				$i++;
			}

			$i = 1;
			foreach ($live_sales_alw as $row) {
				$live_sales_alw_val[$i] = $row->config_qty;
				$i++;
			}

			$rate_1 = 1;
			$rate_2 = 1;
			foreach ($weight_incentive_2 as $row) {
				
				if($row->broiler_subgroup_name == 'Weight Incentive 2 ALW'){
					$weight_incentive_2_alw_val[$rate_1] = $row->config_qty;
					$rate_1++;
				}
				if($row->broiler_subgroup_name == 'Weight Incentive 2 FCR'){
					$weight_incentive_2_fcr_val[$rate_2] = $row->config_qty;
					$rate_2++;
				}
			}

			$i = 1;
			foreach ($fcr_input as $row) {
				$fcr_input_val[$i] = $row->broiler_budget_qty;
				$fcr_amount_type_val[$i] = $row->amount_type_id;
				$i++;
			}

			$i = 1;
			foreach ($harvest_live_sales as $row) {
				$harvest_live_sales_val[$i] = $row->broiler_budget_qty;
				$harvest_live_sales_amount_type_val[$i] = $row->amount_type_id;
				$i++;
			}

			for($month = 1; $month <= 12; $month++){
				$harvest_live_sales_val[$month] = amount_type($harvest_live_sales_val[$month], $harvest_live_sales_amount_type_val[$month]);

				if($live_sales_alw_val[$month] > 0){
					$dynamic_live_sales_alw[$month] = $live_sales_alw_val[$month];
				} else {
					$dynamic_live_sales_alw[$month] = $alw_val[$month];
				}

				if($fcr_input_val[$month] <=  $weight_incentive_2_fcr_val[$month] && $dynamic_live_sales_alw[$month] >= $weight_incentive_2_alw_val[$month]){
					$initial1[$month] = $harvested_heads[$month] * $harvest_live_sales_val[$month];
					$initial2[$month] = $dynamic_live_sales_alw[$month] - $weight_incentive_2_alw_val[$month];
					$result[$month] = $initial1[$month] * $initial2[$month];
					$result[$month] = $result[$month] * $weight_incentive_2_input_val[$month];
					$result[$month] = round($result[$month], 3);
				} else {
					$result[$month] = 0;
				}
			}
			return $result;
		} else {
			return $result = 0;
		}
	}

	function nae_special_incentive($bc_id, $trans_year,  $doctype){
		$harvested_heads = harvested_heads($bc_id, $trans_year,  $doctype);

		$nae_percentage = get_broiler_budget_qty(24, decode($bc_id), decode($trans_year),  $doctype) != false ? get_broiler_budget_qty(24, decode($bc_id), decode($trans_year),  $doctype) : 0;
		$nae_special_range = get_broiler_config_qty_bulk('NAE Special Range', decode($bc_id), decode($trans_year)) != false ? get_broiler_config_qty_bulk('NAE Special Range', decode($bc_id), decode($trans_year)) : 0;
		$nae_special_rate = get_broiler_config_qty_bulk('NAE Special Rate', decode($bc_id), decode($trans_year)) != false ? get_broiler_config_qty_bulk('NAE Special Rate', decode($bc_id), decode($trans_year)) : 0;
		$nae_special_perc_availment = get_broiler_config_qty('NAE Special % Availment', decode($bc_id), decode($trans_year),  $doctype) != false ? get_broiler_config_qty('NAE Special % Availment', decode($bc_id), decode($trans_year),  $doctype) : 0;
		$fcr_input = get_broiler_budget_qty(23, decode($bc_id), decode($trans_year),  $doctype) != false ? get_broiler_budget_qty(23, decode($bc_id), decode($trans_year),  $doctype) : 0;

		if($nae_percentage && $nae_special_range && $nae_special_rate && $nae_special_perc_availment && $fcr_input){

			$range_1 = 1;
			$range_2 = 1;
			$range_3 = 1;
			$range_4 = 1;
			$range_5 = 1;
			$range_6 = 1;
			foreach ($nae_special_range as $row) {
				
				if($row->broiler_subgroup_name == 'NAE Special Range 1'){
					$nae_special_range_1_val[$range_1] = $row->config_qty;
					$range_1++;
				}
				if($row->broiler_subgroup_name == 'NAE Special Range 2'){
					$nae_special_range_2_val[$range_2] = $row->config_qty;
					$range_2++;
				}
				if($row->broiler_subgroup_name == 'NAE Special Range 3'){
					$nae_special_range_3_val[$range_3] = $row->config_qty;
					$range_3++;
				}
				if($row->broiler_subgroup_name == 'NAE Special Range 4'){
					$nae_special_range_4_val[$range_4] = $row->config_qty;
					$range_4++;
				}
				if($row->broiler_subgroup_name == 'NAE Special Range 5'){
					$nae_special_range_5_val[$range_5] = $row->config_qty;
					$range_5++;
				}
				if($row->broiler_subgroup_name == 'NAE Special Range 6'){
					$nae_special_range_6_val[$range_6] = $row->config_qty;
					$range_6++;
				}
			}

			$rate_1 = 1;
			$rate_2 = 1;
			$rate_3 = 1;
			$rate_4 = 1;
			$rate_5 = 1;
			$rate_6 = 1;
			foreach ($nae_special_rate as $row) {
				if($row->broiler_subgroup_name == 'NAE Special Rate 1'){
					$nae_special_rate_1_val[$rate_1] = $row->config_qty;
					$rate_1++;
				}
				if($row->broiler_subgroup_name == 'NAE Special Rate 2'){
					$nae_special_rate_2_val[$rate_2] = $row->config_qty;
					$rate_2++;
				}
				if($row->broiler_subgroup_name == 'NAE Special Rate 3'){
					$nae_special_rate_3_val[$rate_3] = $row->config_qty;
					$rate_3++;
				}
				if($row->broiler_subgroup_name == 'NAE Special Rate 4'){
					$nae_special_rate_4_val[$rate_4] = $row->config_qty;
					$rate_4++;
				}
				if($row->broiler_subgroup_name == 'NAE Special Rate 5'){
					$nae_special_rate_5_val[$rate_5] = $row->config_qty;
					$rate_5++;
				}
				if($row->broiler_subgroup_name == 'NAE Special Rate 6'){
					$nae_special_rate_6_val[$rate_5] = $row->config_qty;
					$rate_6++;
				}
			}

			$i = 1;
			foreach ($nae_percentage as $row) {
				$nae_percentage_val[$i] = $row->broiler_budget_qty;
				$nae_percentage_amount_type_val[$i] = $row->amount_type_id;
				$i++;
			}

			$i = 1;
			foreach ($nae_special_perc_availment as $row) {
				$nae_special_perc_availment_val[$i] = $row->config_qty;
				$nae_special_perc_availment_amount_type_val[$i] = $row->amount_type_id;
				$i++;
			}

			$i = 1;
			foreach ($fcr_input as $row) {
				$fcr_input_val[$i] = $row->broiler_budget_qty;
				$i++;
			}

			for($month = 1; $month <= 12; $month++){
				$nae_special_perc_availment_val[$month] = amount_type($nae_special_perc_availment_val[$month], $nae_special_perc_availment_amount_type_val[$month]);
				$nae_percentage_val[$month] = amount_type($nae_percentage_val[$month], $nae_percentage_amount_type_val[$month]);
				if($fcr_input_val[$month] <= $nae_special_range_1_val[$month]){
					
					$dynamic_nae_special_rate[$month] = $nae_special_rate_1_val[$month];

				} else if($fcr_input_val[$month] > $nae_special_range_1_val[$month] && $fcr_input_val[$month] <= $nae_special_range_2_val[$month]){

					$dynamic_nae_special_rate[$month] = $nae_special_rate_2_val[$month];

				} else if($fcr_input_val[$month] > $nae_special_range_2_val[$month] && $fcr_input_val[$month] <= $nae_special_range_3_val[$month]){

					$dynamic_nae_special_rate[$month] = $nae_special_rate_3_val[$month];

				} else if($fcr_input_val[$month] > $nae_special_range_3_val[$month] && $fcr_input_val[$month] <= $nae_special_range_4_val[$month]){

					$dynamic_nae_special_rate[$month] = $nae_special_rate_4_val[$month];

				} else if($fcr_input_val[$month] > $nae_special_range_4_val[$month] && $fcr_input_val[$month] <= $nae_special_range_5_val[$month]){

					$dynamic_nae_special_rate[$month] = $nae_special_rate_5_val[$month];

				} else if($fcr_input_val[$month] > $nae_special_range_5_val[$month] && $fcr_input_val[$month] <= $nae_special_range_6_val[$month]){

					$dynamic_nae_special_rate[$month] = $nae_special_rate_6_val[$month];

				} else {
					$dynamic_nae_special_rate[$month] = 0;
				}

				$initial[$month] = $harvested_heads[$month] * $nae_percentage_val[$month];
				$result[$month] = $initial[$month] * $dynamic_nae_special_rate[$month] * $nae_special_perc_availment_val[$month];
				$result[$month] = round($result[$month], 3);
			}
			return $result;
		} else {
			return $result = 0;
		}
	}

	function laki_sa_alaga($bc_id, $trans_year, $doctype){
		$doc_placement = get_broiler_budget_qty(2, decode($bc_id), decode($trans_year), $doctype) != false ? get_broiler_budget_qty(2, decode($bc_id), decode($trans_year), $doctype) : 0;
		$hr = get_broiler_budget_qty(3, decode($bc_id), decode($trans_year), $doctype) != false ? get_broiler_budget_qty(3, decode($bc_id), decode($trans_year), $doctype) : 0;
		$pfd = get_broiler_budget_qty(5, decode($bc_id), decode($trans_year), $doctype) != false ? get_broiler_budget_qty(5, decode($bc_id), decode($trans_year), $doctype) : 0;

		$laki_sa_alaga_rate = get_broiler_config_qty('LAKI SA ALAGA RATE', decode($bc_id), decode($trans_year),  $doctype) != false ? get_broiler_config_qty('LAKI SA ALAGA RATE', decode($bc_id), decode($trans_year),  $doctype) : 0;

		$nae_perc_harvest = get_broiler_config_qty('NAE % HARVEST', decode($bc_id), decode($trans_year),  $doctype) != false ? get_broiler_config_qty('NAE % HARVEST', decode($bc_id), decode($trans_year),  $doctype) : 0;

		if(!empty($doc_placement) && !empty($hr) && !empty($laki_sa_alaga_rate) && !empty($nae_perc_harvest)){
			$i = 1;
			foreach ($doc_placement as $row) {
				$doc_placement_val[$i] = $row->broiler_budget_qty;
				$i++;
			}

			$i = 1;
			foreach ($hr as $row) {
				$hr_val[$i] = $row->broiler_budget_qty;
				$hr_amount_type_val[$i] = $row->amount_type_id;
				$i++;
			}

			$i = 1;
			foreach ($pfd as $row) {
				$pfd_val[$i] = $row->broiler_budget_qty;
				$pfd_amount_type_val[$i] = $row->amount_type_id;
				$i++;
			}

			$i = 1;
			foreach ($laki_sa_alaga_rate as $row) {
				$laki_sa_alaga_val[$i] = $row->config_qty;
				$laki_sa_alaga_amount_type_val[$i] = $row->amount_type_id;
				$i++;
			}

			$i = 1;
			foreach ($nae_perc_harvest as $row) {
				$nae_perc_harvest_val[$i] = $row->config_qty;
				$nae_perc_harvest_amount_type_val[$i] = $row->amount_type_id;
				$i++;
			}

			for($month = 1; $month <= 12; $month++){
				$laki_sa_alaga_val[$month] = amount_type($laki_sa_alaga_val[$month], $laki_sa_alaga_amount_type_val[$month]);

				$nae_perc_harvest_val[$month] = amount_type($nae_perc_harvest_val[$month], $nae_perc_harvest_amount_type_val[$month]);

				$hr_val[$month] = amount_type($hr_val[$month], $hr_amount_type_val[$month]);

				$pfd_val[$month] = amount_type($pfd_val[$month], $pfd_amount_type_val[$month]);
				
				$pfd_val[$month] = 1-$pfd_val[$month];

				$result[$month] = $doc_placement_val[$month] * $pfd_val[$month] * $hr_val[$month] * $nae_perc_harvest_val[$month] * $laki_sa_alaga_val[$month];
			}
			return $result;
		} else {
			return $result = 0;
		}
	}

	function electricity_incentive($bc_id, $trans_year, $doctype){
		$doc_placement = get_broiler_budget_qty(2, decode($bc_id), decode($trans_year), $doctype) != false ? get_broiler_budget_qty(2, decode($bc_id), decode($trans_year), $doctype) : 0;
		$hr = get_broiler_budget_qty(3, decode($bc_id), decode($trans_year), $doctype) != false ? get_broiler_budget_qty(3, decode($bc_id), decode($trans_year), $doctype) : 0;
		$pfd = get_broiler_budget_qty(5, decode($bc_id), decode($trans_year), $doctype) != false ? get_broiler_budget_qty(5, decode($bc_id), decode($trans_year), $doctype) : 0;

		$electricity_incentive_rate = get_broiler_config_qty('ELECTRICITY INCENTIVE RATE', decode($bc_id), decode($trans_year),  $doctype) != false ? get_broiler_config_qty('ELECTRICITY INCENTIVE RATE', decode($bc_id), decode($trans_year),  $doctype) : 0;

		$tunnel_vent_perc = get_broiler_budget_qty(29, decode($bc_id), decode($trans_year),  $doctype) != false ? get_broiler_budget_qty(29, decode($bc_id), decode($trans_year),  $doctype) : 0;

		if(!empty($doc_placement) && !empty($hr) && !empty($electricity_incentive_rate) && !empty($tunnel_vent_perc) ){
			$i = 1;
			foreach ($doc_placement as $row) {
				$doc_placement_val[$i] = $row->broiler_budget_qty;
				$i++;
			}

			$i = 1;
			foreach ($hr as $row) {
				$hr_val[$i] = $row->broiler_budget_qty;
				$hr_amount_type_val[$i] = $row->amount_type_id;
				$i++;
			}

			$i = 1;
			foreach ($pfd as $row) {
				$pfd_val[$i] = $row->broiler_budget_qty;
				$pfd_amount_type_val[$i] = $row->amount_type_id;
				$i++;
			}

			$i = 1;
			foreach ($electricity_incentive_rate as $row) {
				$electricity_incentive_rate_val[$i] = $row->config_qty;
				$electricity_incentive_rate_amount_type_val[$i] = $row->amount_type_id;
				$i++;
			}

			$i = 1;
			foreach ($tunnel_vent_perc as $row) {
				$tunnel_vent_perc_val[$i] = $row->broiler_budget_qty;
				$tunnel_vent_perc_amount_type_val[$i] = $row->amount_type_id;
				$i++;
			}

			for($month = 1; $month <= 12; $month++){
				$electricity_incentive_rate_val[$month] = amount_type($electricity_incentive_rate_val[$month], $electricity_incentive_rate_amount_type_val[$month]);
				$tunnel_vent_perc_val[$month] = amount_type($tunnel_vent_perc_val[$month], $tunnel_vent_perc_amount_type_val[$month]);

				$hr_val[$month] = amount_type($hr_val[$month], $hr_amount_type_val[$month]);

				$pfd_val[$month] = amount_type($pfd_val[$month], $pfd_amount_type_val[$month]);
				$pfd_val[$month] = 1-$pfd_val[$month];

				$result[$month] = $doc_placement_val[$month] * $pfd_val[$month] * $hr_val[$month] * $tunnel_vent_perc_val[$month] * $electricity_incentive_rate_val[$month];
			}
			return $result;
		} else {
			return $result = 0;
		}
	}

	function gawa_incentive($bc_id, $trans_year, $doctype){
		$doc_placement = get_broiler_budget_qty(2, decode($bc_id), decode($trans_year), $doctype) != false ? get_broiler_budget_qty(2, decode($bc_id), decode($trans_year), $doctype) : 0;
		$hr = get_broiler_budget_qty(3, decode($bc_id), decode($trans_year), $doctype) != false ? get_broiler_budget_qty(3, decode($bc_id), decode($trans_year), $doctype) : 0;
		$pfd = get_broiler_budget_qty(5, decode($bc_id), decode($trans_year), $doctype) != false ? get_broiler_budget_qty(5, decode($bc_id), decode($trans_year), $doctype) : 0;

		$gawa_incentive_rate = get_broiler_config_qty('GAWA INCENTIVE RATE', decode($bc_id), decode($trans_year),  $doctype) != false ? get_broiler_config_qty('GAWA INCENTIVE RATE', decode($bc_id), decode($trans_year),  $doctype) : 0;

		if(!empty($doc_placement) && !empty($hr) && !empty($gawa_incentive_rate) ){
			$i = 1;
			foreach ($doc_placement as $row) {
				$doc_placement_val[$i] = $row->broiler_budget_qty;
				$i++;
			}

			$i = 1;
			foreach ($hr as $row) {
				$hr_val[$i] = $row->broiler_budget_qty;
				$hr_amount_type_val[$i] = $row->amount_type_id;
				$i++;
			}

			$i = 1;
			foreach ($pfd as $row) {
				$pfd_val[$i] = $row->broiler_budget_qty;
				$pfd_amount_type_val[$i] = $row->amount_type_id;
				$i++;
			}

			$i = 1;
			foreach ($gawa_incentive_rate as $row) {
				$gawa_incentive_rate_val[$i] = $row->config_qty;
				$gawa_incentive_rate_amount_type_val[$i] = $row->amount_type_id;
				$i++;
			}

			for($month = 1; $month <= 12; $month++){
				$gawa_incentive_rate_val[$month] = amount_type($gawa_incentive_rate_val[$month], $gawa_incentive_rate_amount_type_val[$month]);

				$hr_val[$month] = amount_type($hr_val[$month], $hr_amount_type_val[$month]);

				$pfd_val[$month] = amount_type($pfd_val[$month], $pfd_amount_type_val[$month]);
				$pfd_val[$month] = 1-$pfd_val[$month];

				$result[$month] = $doc_placement_val[$month] * $pfd_val[$month] * $hr_val[$month] * $gawa_incentive_rate_val[$month];
			}
			return $result;
		} else {
			return $result = 0;
		}
	}

	function kaagapay_incentive($bc_id, $trans_year, $doctype){
		$doc_placement = get_broiler_budget_qty(2, decode($bc_id), decode($trans_year), $doctype) != false ? get_broiler_budget_qty(2, decode($bc_id), decode($trans_year), $doctype) : 0;
		$hr = get_broiler_budget_qty(3, decode($bc_id), decode($trans_year), $doctype) != false ? get_broiler_budget_qty(3, decode($bc_id), decode($trans_year), $doctype) : 0;
		$pfd = get_broiler_budget_qty(5, decode($bc_id), decode($trans_year), $doctype) != false ? get_broiler_budget_qty(5, decode($bc_id), decode($trans_year), $doctype) : 0;

		$kaagapay_incentive_rate = get_broiler_config_qty('KAAGAPAY INCENTIVE RATE', decode($bc_id), decode($trans_year),  $doctype) != false ? get_broiler_config_qty('KAAGAPAY INCENTIVE RATE', decode($bc_id), decode($trans_year),  $doctype) : 0;

		if(!empty($doc_placement) && !empty($hr) && !empty($kaagapay_incentive_rate) ){
			$i = 1;
			foreach ($doc_placement as $row) {
				$doc_placement_val[$i] = $row->broiler_budget_qty;
				$i++;
			}

			$i = 1;
			foreach ($hr as $row) {
				$hr_val[$i] = $row->broiler_budget_qty;
				$hr_amount_type_val[$i] = $row->amount_type_id;
				$i++;
			}

			$i = 1;
			foreach ($pfd as $row) {
				$pfd_val[$i] = $row->broiler_budget_qty;
				$pfd_amount_type_val[$i] = $row->amount_type_id;
				$i++;
			}

			$i = 1;
			foreach ($kaagapay_incentive_rate as $row) {
				$kaagapay_incentive_rate_val[$i] = $row->config_qty;
				$kaagapay_incentive_rate_amount_type_val[$i] = $row->amount_type_id;
				$i++;
			}

			for($month = 1; $month <= 12; $month++){
				$kaagapay_incentive_rate_val[$month] = amount_type($kaagapay_incentive_rate_val[$month], $kaagapay_incentive_rate_amount_type_val[$month]);
				$hr_val[$month] = amount_type($hr_val[$month], $hr_amount_type_val[$month]);

				$pfd_val[$month] = amount_type($pfd_val[$month], $pfd_amount_type_val[$month]);
				$pfd_val[$month] = 1-$pfd_val[$month];

				$result[$month] = $doc_placement_val[$month] * $pfd_val[$month] * $hr_val[$month] * $kaagapay_incentive_rate_val[$month];
			}
			return $result;
		} else {
			return $result = 0;
		}
	}

	function cash_on_catch_incentive($bc_id, $trans_year, $doctype){
		$doc_placement = get_broiler_budget_qty(2, decode($bc_id), decode($trans_year), $doctype) != false ? get_broiler_budget_qty(2, decode($bc_id), decode($trans_year), $doctype) : 0;
		$hr = get_broiler_budget_qty(3, decode($bc_id), decode($trans_year), $doctype) != false ? get_broiler_budget_qty(3, decode($bc_id), decode($trans_year), $doctype) : 0;
		$pfd = get_broiler_budget_qty(5, decode($bc_id), decode($trans_year), $doctype) != false ? get_broiler_budget_qty(5, decode($bc_id), decode($trans_year), $doctype) : 0;

		$cash_on_catch_incentive_rate = get_broiler_config_qty('CASH ON CATCH INCENTIVE RATE', decode($bc_id), decode($trans_year),  $doctype) != false ? get_broiler_config_qty('CASH ON CATCH INCENTIVE RATE', decode($bc_id), decode($trans_year),  $doctype) : 0;

		if(!empty($doc_placement) && !empty($hr) && !empty($cash_on_catch_incentive_rate) ){
			$i = 1;
			foreach ($doc_placement as $row) {
				$doc_placement_val[$i] = $row->broiler_budget_qty;
				$i++;
			}

			$i = 1;
			foreach ($hr as $row) {
				$hr_val[$i] = $row->broiler_budget_qty;
				$hr_amount_type_val[$i] = $row->amount_type_id;
				$i++;
			}

			$i = 1;
			foreach ($pfd as $row) {
				$pfd_val[$i] = $row->broiler_budget_qty;
				$pfd_amount_type_val[$i] = $row->amount_type_id;
				$i++;
			}

			$i = 1;
			foreach ($cash_on_catch_incentive_rate as $row) {
				$cash_on_catch_incentive_rate_val[$i] = $row->config_qty;
				$cash_on_catch_incentive_rate_amount_type_val[$i] = $row->amount_type_id;
				$i++;
			}

			for($month = 1; $month <= 12; $month++){
				$cash_on_catch_incentive_rate_val[$month] = amount_type($cash_on_catch_incentive_rate_val[$month], $cash_on_catch_incentive_rate_amount_type_val[$month]);
				$hr_val[$month] = amount_type($hr_val[$month], $hr_amount_type_val[$month]);

				$pfd_val[$month] = amount_type($pfd_val[$month], $pfd_amount_type_val[$month]);
				$pfd_val[$month] = 1-$pfd_val[$month];

				$result[$month] = $doc_placement_val[$month] * $pfd_val[$month] * $hr_val[$month] * $cash_on_catch_incentive_rate_val[$month];
			}
			return $result;
		} else {
			return $result = 0;
		}
	}

	function alw_forecasting_support($bc_id, $trans_year, $doctype){
		$doc_placement = get_broiler_budget_qty(2, decode($bc_id), decode($trans_year), $doctype) != false ? get_broiler_budget_qty(2, decode($bc_id), decode($trans_year), $doctype) : 0;
		$hr = get_broiler_budget_qty(3, decode($bc_id), decode($trans_year), $doctype) != false ? get_broiler_budget_qty(3, decode($bc_id), decode($trans_year), $doctype) : 0;
		$pfd = get_broiler_budget_qty(5, decode($bc_id), decode($trans_year), $doctype) != false ? get_broiler_budget_qty(5, decode($bc_id), decode($trans_year), $doctype) : 0;

		$alw_forecasting_support_rate = get_broiler_config_qty('ALW FORECASTING SUPPORT RATE', decode($bc_id), decode($trans_year),  $doctype) != false ? get_broiler_config_qty('ALW FORECASTING SUPPORT RATE', decode($bc_id), decode($trans_year),  $doctype) : 0;

		if(!empty($doc_placement) && !empty($hr) && !empty($alw_forecasting_support_rate) ){
			$i = 1;
			foreach ($doc_placement as $row) {
				$doc_placement_val[$i] = $row->broiler_budget_qty;
				$i++;
			}

			$i = 1;
			foreach ($hr as $row) {
				$hr_val[$i] = $row->broiler_budget_qty;
				$hr_amount_type_val[$i] = $row->amount_type_id;
				$i++;
			}

			$i = 1;
			foreach ($pfd as $row) {
				$pfd_val[$i] = $row->broiler_budget_qty;
				$pfd_amount_type_val[$i] = $row->amount_type_id;
				$i++;
			}

			$i = 1;
			foreach ($alw_forecasting_support_rate as $row) {
				$alw_forecasting_support_rate_val[$i] = $row->config_qty;
				$alw_forecasting_support_rate_amount_type_val[$i] = $row->amount_type_id;
				$i++;
			}

			for($month = 1; $month <= 12; $month++){
				$alw_forecasting_support_rate_val[$month] = amount_type($alw_forecasting_support_rate_val[$month], $alw_forecasting_support_rate_amount_type_val[$month]);

				$hr_val[$month] = amount_type($hr_val[$month], $hr_amount_type_val[$month]);

				$pfd_val[$month] = amount_type($pfd_val[$month], $pfd_amount_type_val[$month]);
				$pfd_val[$month] = 1-$pfd_val[$month];

				$result[$month] = $doc_placement_val[$month] * $pfd_val[$month] * $hr_val[$month] * $alw_forecasting_support_rate_val[$month];
			}
			return $result;
		} else {
			return $result = 0;
		}
	}

	function kkk_incentive($bc_id, $trans_year, $doctype){
		$doc_placement = get_broiler_budget_qty(2, decode($bc_id), decode($trans_year), $doctype) != false ? get_broiler_budget_qty(2, decode($bc_id), decode($trans_year), $doctype) : 0;
		$hr = get_broiler_budget_qty(3, decode($bc_id), decode($trans_year), $doctype) != false ? get_broiler_budget_qty(3, decode($bc_id), decode($trans_year), $doctype) : 0;
		$pfd = get_broiler_budget_qty(5, decode($bc_id), decode($trans_year), $doctype) != false ? get_broiler_budget_qty(5, decode($bc_id), decode($trans_year), $doctype) : 0;

		$kkk_incentive_rate = get_broiler_config_qty('KKK INCENTIVE RATE', decode($bc_id), decode($trans_year),  $doctype) != false ? get_broiler_config_qty('KKK INCENTIVE RATE', decode($bc_id), decode($trans_year),  $doctype) : 0;

		if(!empty($doc_placement) && !empty($hr) && !empty($kkk_incentive_rate) ){
			$i = 1;
			foreach ($doc_placement as $row) {
				$doc_placement_val[$i] = $row->broiler_budget_qty;
				$i++;
			}

			$i = 1;
			foreach ($hr as $row) {
				$hr_val[$i] = $row->broiler_budget_qty;
				$hr_amount_type_val[$i] = $row->amount_type_id;
				$i++;
			}

			$i = 1;
			foreach ($pfd as $row) {
				$pfd_val[$i] = $row->broiler_budget_qty;
				$pfd_amount_type_val[$i] = $row->amount_type_id;
				$i++;
			}

			$i = 1;
			foreach ($kkk_incentive_rate as $row) {
				$kkk_incentive_rate_val[$i] = $row->config_qty;
				$kkk_incentive_rate_amount_type_val[$i] = $row->amount_type_id;
				$i++;
			}

			for($month = 1; $month <= 12; $month++){
				$kkk_incentive_rate_val[$month] = amount_type($kkk_incentive_rate_val[$month], $kkk_incentive_rate_amount_type_val[$month]);
				$hr_val[$month] = amount_type($hr_val[$month], $hr_amount_type_val[$month]);

				$pfd_val[$month] = amount_type($pfd_val[$month], $pfd_amount_type_val[$month]);
				$pfd_val[$month] = 1-$pfd_val[$month];

				$result[$month] = $doc_placement_val[$month] * $pfd_val[$month] * $hr_val[$month] * $kkk_incentive_rate_val[$month];
			}
			return $result;
		} else {
			return $result = 0;
		}
	}

	function zero_antibiotic($bc_id, $trans_year, $doctype){
		$doc_placement = get_broiler_budget_qty(2, decode($bc_id), decode($trans_year), $doctype) != false ? get_broiler_budget_qty(2, decode($bc_id), decode($trans_year), $doctype) : 0;
		$hr = get_broiler_budget_qty(3, decode($bc_id), decode($trans_year), $doctype) != false ? get_broiler_budget_qty(3, decode($bc_id), decode($trans_year), $doctype) : 0;
		$pfd = get_broiler_budget_qty(5, decode($bc_id), decode($trans_year), $doctype) != false ? get_broiler_budget_qty(5, decode($bc_id), decode($trans_year), $doctype) : 0;

		$zero_antibiotic_rate = get_broiler_config_qty('ZERO ANTIBIOTIC RATE', decode($bc_id), decode($trans_year),  $doctype) != false ? get_broiler_config_qty('ZERO ANTIBIOTIC RATE', decode($bc_id), decode($trans_year),  $doctype) : 0;

		if(!empty($doc_placement) && !empty($hr) && !empty($zero_antibiotic_rate) ){
			$i = 1;
			foreach ($doc_placement as $row) {
				$doc_placement_val[$i] = $row->broiler_budget_qty;
				$i++;
			}

			$i = 1;
			foreach ($hr as $row) {
				$hr_val[$i] = $row->broiler_budget_qty;
				$hr_amount_type_val[$i] = $row->amount_type_id;
				$i++;
			}

			$i = 1;
			foreach ($pfd as $row) {
				$pfd_val[$i] = $row->broiler_budget_qty;
				$pfd_amount_type_val[$i] = $row->amount_type_id;
				$i++;
			}

			$i = 1;
			foreach ($zero_antibiotic_rate as $row) {
				$zero_antibiotic_rate_val[$i] = $row->config_qty;
				$zero_antibiotic_rate_amount_type_val[$i] = $row->amount_type_id;
				$i++;
			}

			for($month = 1; $month <= 12; $month++){
				$zero_antibiotic_rate_val[$month] = amount_type($zero_antibiotic_rate_val[$month], $zero_antibiotic_rate_amount_type_val[$month]);
				$hr_val[$month] = amount_type($hr_val[$month], $hr_amount_type_val[$month]);

				$pfd_val[$month] = amount_type($pfd_val[$month], $pfd_amount_type_val[$month]);
				$pfd_val[$month] = 1-$pfd_val[$month];

				$result[$month] = $doc_placement_val[$month] * $pfd_val[$month] * $hr_val[$month] * $zero_antibiotic_rate_val[$month];
			}
			return $result;
		} else {
			return $result = 0;
		}
	}

	function sdg_incentive($bc_id, $trans_year, $doctype){
		$doc_placement = get_broiler_budget_qty(2, decode($bc_id), decode($trans_year), $doctype) != false ? get_broiler_budget_qty(2, decode($bc_id), decode($trans_year), $doctype) : 0;
		$hr = get_broiler_budget_qty(3, decode($bc_id), decode($trans_year), $doctype) != false ? get_broiler_budget_qty(3, decode($bc_id), decode($trans_year), $doctype) : 0;
		$pfd = get_broiler_budget_qty(5, decode($bc_id), decode($trans_year), $doctype) != false ? get_broiler_budget_qty(5, decode($bc_id), decode($trans_year), $doctype) : 0;

		$sdg_incentive_rate = get_broiler_config_qty('SDG INCENTIVE RATE', decode($bc_id), decode($trans_year),  $doctype) != false ? get_broiler_config_qty('SDG INCENTIVE RATE', decode($bc_id), decode($trans_year),  $doctype) : 0;

		if(!empty($doc_placement) && !empty($hr) && !empty($sdg_incentive_rate) ){
			$i = 1;
			foreach ($doc_placement as $row) {
				$doc_placement_val[$i] = $row->broiler_budget_qty;
				$i++;
			}

			$i = 1;
			foreach ($hr as $row) {
				$hr_val[$i] = $row->broiler_budget_qty;
				$hr_amount_type_val[$i] = $row->amount_type_id;
				$i++;
			}

			$i = 1;
			foreach ($pfd as $row) {
				$pfd_val[$i] = $row->broiler_budget_qty;
				$pfd_amount_type_val[$i] = $row->amount_type_id;
				$i++;
			}

			$i = 1;
			foreach ($sdg_incentive_rate as $row) {
				$sdg_incentive_rate_val[$i] = $row->config_qty;
				$sdg_incentive_rate_amount_type_val[$i] = $row->amount_type_id;
				$i++;
			}

			for($month = 1; $month <= 12; $month++){
				$sdg_incentive_rate_val[$month] = amount_type($sdg_incentive_rate_val[$month], $sdg_incentive_rate_amount_type_val[$month]);
				$hr_val[$month] = amount_type($hr_val[$month], $hr_amount_type_val[$month]);

				$pfd_val[$month] = amount_type($pfd_val[$month], $pfd_amount_type_val[$month]);
				$pfd_val[$month] = 1-$pfd_val[$month];

				$result[$month] = $doc_placement_val[$month] * $pfd_val[$month] * $hr_val[$month] * $sdg_incentive_rate_val[$month];
			}
			return $result;
		} else {
			return $result = 0;
		}
	}

	function ibc_opex($bc_id, $trans_year,  $doctype){
		$harvested_heads = harvested_heads($bc_id, $trans_year,  $doctype);
		$ibc_percentage = get_broiler_budget_qty(115, decode($bc_id), decode($trans_year),  $doctype) != false ? get_broiler_budget_qty(115, decode($bc_id), decode($trans_year),  $doctype) : 0;
		$ibc_opex = get_broiler_budget_qty(117, decode($bc_id), decode($trans_year),  $doctype) != false ? get_broiler_budget_qty(117, decode($bc_id), decode($trans_year),  $doctype) : 0;
		if(!empty($ibc_percentage) && !empty($ibc_opex)){
			$i = 1;
			foreach ($ibc_percentage as $row) {
				$ibc_percentage_val[$i] = $row->broiler_budget_qty;
				$ibc_percentage_amount_type_val[$i] = $row->amount_type_id;
				$i++;
			}

			$i = 1;
			foreach ($ibc_opex as $row) {
				$ibc_opex_val[$i] = $row->broiler_budget_qty;
				$i++;
			}

			for($month = 1; $month <= 12; $month++){
				if(!empty($ibc_percentage_val[$month]) && !empty($ibc_opex_val[$month])){
					$ibc_percentage_val[$month] = amount_type($ibc_percentage_val[$month], $ibc_percentage_amount_type_val[$month]);
					$result[$month] = $harvested_heads[$month] * $ibc_percentage_val[$month] * $ibc_opex_val[$month];
					$result[$month] = round($result[$month], 3);
				} else {
					$result[$month] = 0;
				}
			}
			return $result;
		} else {
			return $result = 0;
		}
	}

	function right_size_bonus_new($bc_id, $trans_year, $doctype){
		$harvested_heads = harvested_heads($bc_id, $trans_year,  $doctype);
		$right_size_perc_availment = get_broiler_config_qty('Right Size Bonus % Availment', decode($bc_id), decode($trans_year),  $doctype) != false ? get_broiler_config_qty('Right Size Bonus % Availment', decode($bc_id), decode($trans_year),  $doctype) : 0;
		$right_size_new_rate = get_broiler_config_qty_bulk('Right Size New Rate', decode($bc_id), decode($trans_year)) != false ? get_broiler_config_qty_bulk('Right Size New Rate', decode($bc_id), decode($trans_year)) : 0;
		$right_size_new_range = get_broiler_config_qty_bulk('Right Size New Range', decode($bc_id), decode($trans_year)) != false ? get_broiler_config_qty_bulk('Right Size New Range', decode($bc_id), decode($trans_year)) : 0;
		$alw = get_broiler_budget_qty(4, decode($bc_id), decode($trans_year),  $doctype) != false ? get_broiler_budget_qty(4, decode($bc_id), decode($trans_year),  $doctype) : 0;


		if(!empty($right_size_perc_availment) && !empty($right_size_new_rate) && !empty($right_size_new_range) ){
			$i = 1;
			foreach ($right_size_perc_availment as $row) {
				$right_size_perc_availment_val[$i] = $row->config_qty;
				$right_size_perc_availment_amount_type_val[$i] = $row->amount_type_id;
				$i++;
			}

			$i = 1;
            foreach ($alw as $row) {
                $alw_val[$i] = $row->broiler_budget_qty;
                $i++;
            }

			$range_1 = 1;
            $range_2 = 1;
            $range_3 = 1;
            $range_4 = 1;
            $range_5 = 1;
            foreach ($right_size_new_range as $row) {
                
                if($row->broiler_subgroup_name == 'Right Size New Range 1'){
                    $right_size_new_range_1_val[$range_1] = $row->config_qty;
                    $range_1++;
                }
                if($row->broiler_subgroup_name == 'Right Size New Range 2'){
                    $right_size_new_range_2_val[$range_2] = $row->config_qty;
                    $range_2++;
                }
                if($row->broiler_subgroup_name == 'Right Size New Range 3'){
                    $right_size_new_range_3_val[$range_3] = $row->config_qty;
                    $range_3++;
                }
                if($row->broiler_subgroup_name == 'Right Size New Range 4'){
                    $right_size_new_range_4_val[$range_4] = $row->config_qty;
                    $range_4++;
                }
                if($row->broiler_subgroup_name == 'Right Size New Range 5'){
                    $right_size_new_range_5_val[$range_5] = $row->config_qty;
                    $range_5++;
                }
                
            }


            $rate_1 = 1;
            $rate_2 = 1;
            $rate_3 = 1;
            $rate_4 = 1;
            $rate_5 = 1;
            $rate_6 = 1;
            foreach ($right_size_new_rate as $row) {
                if($row->broiler_subgroup_name == 'Right Size New Rate 1'){
                    $right_size_new_rate_1_val[$rate_1] = $row->config_qty;
                    $rate_1++;
                }
                if($row->broiler_subgroup_name == 'Right Size New Rate 2'){
                    $right_size_new_rate_2_val[$rate_2] = $row->config_qty;
                    $rate_2++;
                }
                if($row->broiler_subgroup_name == 'Right Size New Rate 3'){
                    $right_size_new_rate_3_val[$rate_3] = $row->config_qty;
                    $rate_3++;
                }
                if($row->broiler_subgroup_name == 'Right Size New Rate 4'){
                    $right_size_new_rate_4_val[$rate_4] = $row->config_qty;
                    $rate_4++;
                }
                if($row->broiler_subgroup_name == 'Right Size New Rate 5'){
                    $right_size_new_rate_5_val[$rate_5] = $row->config_qty;
                    $rate_5++;
                }
                if($row->broiler_subgroup_name == 'Right Size New Rate 6'){
                    $right_size_new_rate_6_val[$rate_6] = $row->config_qty;
                    $rate_6++;
                }
            }

            for($month = 1; $month <= 12; $month++){
                
                $right_size_perc_availment_val[$month] = amount_type($right_size_perc_availment_val[$month], $right_size_perc_availment_amount_type_val[$month]);
                

                if($alw_val[$month] >= $right_size_new_range_1_val[$month]){
                    
                    $dynamic_right_size_rate[$month] = $right_size_new_rate_1_val[$month];

                } else if($alw_val[$month] > $right_size_new_range_2_val[$month] && $alw_val[$month] <= $right_size_new_range_1_val[$month]){
                    
                    $dynamic_right_size_rate[$month] = $right_size_new_rate_2_val[$month];

                } else if($alw_val[$month] > $right_size_new_range_3_val[$month] && $alw_val[$month] <= $right_size_new_range_2_val[$month]){

                    $dynamic_right_size_rate[$month] = $right_size_new_rate_3_val[$month];

                } else if($alw_val[$month] > $right_size_new_range_4_val[$month] && $alw_val[$month] <= $right_size_new_range_3_val[$month]){

                    $dynamic_right_size_rate[$month] = $right_size_new_rate_4_val[$month];

                } else if($alw_val[$month] > $right_size_new_range_5_val[$month] && $alw_val[$month] <= $right_size_new_range_4_val[$month]){

                    $dynamic_right_size_rate[$month] = $right_size_new_rate_5_val[$month];

                } else {

                    $dynamic_right_size_rate[$month] = $right_size_new_rate_6_val[$month];

                }
                
                $result[$month] = $harvested_heads[$month] * $dynamic_right_size_rate[$month] * $right_size_perc_availment_val[$month];
                $result[$month] = round($result[$month], 3);
            }

            return $result;
		} else {
            return $result = 0;
        }

	}

	function uniformity_bonus_new($bc_id, $trans_year, $doctype){
		$harvested_heads = harvested_heads($bc_id, $trans_year,  $doctype);
		$uniformity_perc_availment = get_broiler_budget_qty(166, decode($bc_id), decode($trans_year),  $doctype) != false ? get_broiler_budget_qty(166, decode($bc_id), decode($trans_year),  $doctype) : 0;
		$uniformity_new_rate = get_broiler_config_qty_bulk('Uniformity New Rate', decode($bc_id), decode($trans_year)) != false ? get_broiler_config_qty_bulk('Uniformity New Rate', decode($bc_id), decode($trans_year)) : 0;
		$uniformity_new_range = get_broiler_config_qty_bulk('Uniformity New Range', decode($bc_id), decode($trans_year)) != false ? get_broiler_config_qty_bulk('Uniformity New Range', decode($bc_id), decode($trans_year)) : 0;
		$harvest_live_sales = get_broiler_budget_qty(31, decode($bc_id), decode($trans_year),  $doctype) != false ? get_broiler_budget_qty(31, decode($bc_id), decode($trans_year),  $doctype) : 0;


		if(!empty($uniformity_perc_availment) && !empty($uniformity_new_rate) && !empty($uniformity_new_range) ){
			$i = 1;
			foreach ($harvest_live_sales as $row) {
                $harvest_live_sales_val[$i] = $row->broiler_budget_qty;
                $harvest_live_sales_amount_type_val[$i] = $row->amount_type_id;
                $i++;
            }

			$i = 1;
			foreach ($uniformity_perc_availment as $row) {
				$uniformity_perc_availment_val[$i] = $row->broiler_budget_qty;
				$uniformity_perc_availment_amount_type_val[$i] = $row->amount_type_id;
				$i++;
			}

			$range_1 = 1;
            $range_2 = 1;
            $range_3 = 1;
            $range_4 = 1;
            $range_5 = 1;
            foreach ($uniformity_new_range as $row) {
                
                if($row->broiler_subgroup_name == 'Uniformity New Range 1'){
                    $uniformity_new_range_1_val[$range_1] = $row->config_qty;
                    $range_1++;
                }
                if($row->broiler_subgroup_name == 'Uniformity New Range 2'){
                    $uniformity_new_range_2_val[$range_2] = $row->config_qty;
                    $range_2++;
                }
                if($row->broiler_subgroup_name == 'Uniformity New Range 3'){
                    $uniformity_new_range_3_val[$range_3] = $row->config_qty;
                    $range_3++;
                }
                if($row->broiler_subgroup_name == 'Uniformity New Range 4'){
                    $uniformity_new_range_4_val[$range_4] = $row->config_qty;
                    $range_4++;
                }
            }


            $rate_1 = 1;
            $rate_2 = 1;
            $rate_3 = 1;
            $rate_4 = 1;
            $rate_5 = 1;
            $rate_6 = 1;
            foreach ($uniformity_new_rate as $row) {
                if($row->broiler_subgroup_name == 'Uniformity New Rate 1'){
                    $uniformity_new_rate_1_val[$rate_1] = $row->config_qty;
                    $rate_1++;
                }
                if($row->broiler_subgroup_name == 'Uniformity New Rate 2'){
                    $uniformity_new_rate_2_val[$rate_2] = $row->config_qty;
                    $rate_2++;
                }
                if($row->broiler_subgroup_name == 'Uniformity New Rate 3'){
                    $uniformity_new_rate_3_val[$rate_3] = $row->config_qty;
                    $rate_3++;
                }
                if($row->broiler_subgroup_name == 'Uniformity New Rate 4'){
                    $uniformity_new_rate_4_val[$rate_4] = $row->config_qty;
                    $rate_4++;
                }
                if($row->broiler_subgroup_name == 'Uniformity New Rate 5'){
                    $uniformity_new_rate_5_val[$rate_5] = $row->config_qty;
                    $rate_5++;
                }
            }

            for($month = 1; $month <= 12; $month++){
                
                $uniformity_perc_availment_val[$month] = amount_type($uniformity_perc_availment_val[$month], $uniformity_perc_availment_amount_type_val[$month]);
                $harvest_live_sales_val[$month] = amount_type($harvest_live_sales_val[$month], $harvest_live_sales_amount_type_val[$month]);

                if($uniformity_perc_availment_val[$month] >= $uniformity_new_range_1_val[$month]){
                    
                    $dynamic_uniformity_rate[$month] = $uniformity_new_rate_1_val[$month];

                } else if($uniformity_perc_availment_val[$month] >= $uniformity_new_range_2_val[$month] && $uniformity_perc_availment_val[$month] < $uniformity_new_range_1_val[$month]){
                    
                    $dynamic_uniformity_rate[$month] = $uniformity_new_rate_2_val[$month];

                } else if($uniformity_perc_availment_val[$month] >= $uniformity_new_range_3_val[$month] && $uniformity_perc_availment_val[$month] < $uniformity_new_range_2_val[$month]){

                    $dynamic_uniformity_rate[$month] = $uniformity_new_rate_3_val[$month];

                } else if($uniformity_perc_availment_val[$month] >= $uniformity_new_range_4_val[$month] && $uniformity_perc_availment_val[$month] < $uniformity_new_range_3_val[$month]){

                    $dynamic_uniformity_rate[$month] = $uniformity_new_rate_4_val[$month];

                } else {

                    $dynamic_uniformity_rate[$month] = $uniformity_new_rate_5_val[$month];

                }
                
                $result[$month] = $harvested_heads[$month] * $dynamic_uniformity_rate[$month] * $harvest_live_sales_val[$month];
                $result[$month] = round($result[$month], 3);
            }

            return $result;
		} else {
            return $result = 0;
        }

	}

	function external_growers_fee($bc_id, $trans_year,  $doctype){
		$ci =& get_instance();
		$ci->load->model('admin_model', 'admin');

		$basic_fee = basic_fee($bc_id, $trans_year,  $doctype);
		$hr_incentive = hr_incentive($bc_id, $trans_year,  $doctype);
		
		$fcr_incentive = fcr_incentive($bc_id, $trans_year,  $doctype);
		$uniformity_bonus = uniformity_bonus($bc_id, $trans_year,  $doctype);
		$right_size_bonus = right_size_bonus($bc_id, $trans_year,  $doctype);
		$performance_bonus = performance_bonus($bc_id, $trans_year,  $doctype);
		$brooding_incentive = brooding_incentive($bc_id, $trans_year,  $doctype);
		
		$feeds_efficiency_bonus = feeds_efficiency_bonus($bc_id, $trans_year,  $doctype);
		$bpi_incentive = bpi_incentive($bc_id, $trans_year,  $doctype);
		$weight_incentive = weight_incentive($bc_id, $trans_year,  $doctype);
		$tunnel_vent_incentive = tunnel_vent_incentive($bc_id, $trans_year,  $doctype);
		$construction_incentive = construction_incentive($bc_id, $trans_year,  $doctype);
		
		$cleaning_incentive = cleaning_incentive($bc_id, $trans_year,  $doctype);
		$recruitment_incentive = recruitment_incentive($bc_id, $trans_year,  $doctype);
		$estimated_cg_fee_adj = estimated_cg_fee_adj($bc_id, $trans_year,  $doctype);
		$basic_fee_nae = basic_fee_nae($bc_id, $trans_year,  $doctype);
		$hr_incentive_nae = hr_incentive_nae($bc_id, $trans_year,  $doctype);

		$fcr_incentive_new = fcr_incentive_new($bc_id, $trans_year,  $doctype);
		$performance_bonus_new = performance_bonus_new($bc_id, $trans_year,  $doctype);
		$bei_incentive = bei_incentive($bc_id, $trans_year,  $doctype);
		$special_loyalty_incentive = special_loyalty_incentive($bc_id, $trans_year,  $doctype);
		$farm_management_incentive = farm_management_incentive($bc_id, $trans_year,  $doctype);
		
		$nae_incentive = nae_incentive($bc_id, $trans_year,  $doctype);
		$nae_plus_incentive = nae_plus_incentive($bc_id, $trans_year,  $doctype);
		$loyalty_incentive = loyalty_incentive($bc_id, $trans_year,  $doctype);
		$alw_adjustment = alw_adjustment($bc_id, $trans_year,  $doctype);
		$weight_incentive_2 = weight_incentive_2($bc_id, $trans_year,  $doctype);
		$nae_special_incentive = nae_special_incentive($bc_id, $trans_year,  $doctype);

		$laki_sa_alaga = laki_sa_alaga($bc_id, $trans_year,  $doctype);
		$electricity_incentive = electricity_incentive($bc_id, $trans_year,  $doctype);
		$gawa_incentive = gawa_incentive($bc_id, $trans_year,  $doctype);
		$kaagapay_incentive = kaagapay_incentive($bc_id, $trans_year,  $doctype);

		$cash_on_catch_incentive = cash_on_catch_incentive($bc_id, $trans_year,  $doctype);
		$alw_forecasting_support = alw_forecasting_support($bc_id, $trans_year,  $doctype);
		$kkk_incentive = kkk_incentive($bc_id, $trans_year,  $doctype);
		$zero_antibiotic = zero_antibiotic($bc_id, $trans_year,  $doctype);
		$sdg_incentive = sdg_incentive($bc_id, $trans_year,  $doctype);
		
		$right_size_bonus_new = right_size_bonus_new($bc_id, $trans_year,  $doctype);
		$uniformity_bonus_new = uniformity_bonus_new($bc_id, $trans_year,  $doctype);

		//$laki_sa_alaga = laki_sa_alaga($bc_id, $trans_year,  $doctype);

		for ($broiler_summary_item_id = 3; $broiler_summary_item_id <= 31 ; $broiler_summary_item_id++) { 
			$set = array('broiler_summary_status' =>	2);
			$where = array('bc_id'	=>	decode($bc_id),	'broiler_summary_item_id'	=> $broiler_summary_item_id, 'YEAR(trans_date)' => decode($trans_year), 'trans_type_id' =>	1);
			$ci->admin->update_data('broiler_summary_tbl', $set, $where);

			$total[4] = $basic_fee;
			$total[5] = $basic_fee_nae;
			$total[6] = $hr_incentive;
			$total[7] = $hr_incentive_nae;
			$total[8] = $fcr_incentive;
			$total[9] = $fcr_incentive_new;
			$total[10] = $uniformity_bonus;
			$total[11] = $right_size_bonus;
			$total[12] = $performance_bonus;
			$total[13] = $performance_bonus_new;
			$total[14] = $brooding_incentive;
			$total[15] = $feeds_efficiency_bonus;
			$total[16] = $bpi_incentive;
			$total[17] = $bei_incentive;
			$total[18] = $weight_incentive;
			$total[19] = $weight_incentive_2;
			$total[20] = $tunnel_vent_incentive;
			$total[21] = $construction_incentive;
			$total[22] = $cleaning_incentive;
			$total[23] = $recruitment_incentive;
			$total[24] = $estimated_cg_fee_adj;
			$total[25] = $farm_management_incentive;
			$total[26] = $nae_incentive;
			$total[27] = $nae_plus_incentive;
			$total[28] = $loyalty_incentive;
			$total[29] = $special_loyalty_incentive;
			$total[30] = $alw_adjustment;
			$total[31] = $nae_special_incentive;

			if($broiler_summary_item_id >= 4){
				for($month = 1; $month <= 12; $month++){
					$set = array('trans_qty' =>	round($total[$broiler_summary_item_id][$month], dec_places()), 'bc_id'	=>	decode($bc_id),	'broiler_summary_item_id'	=> $broiler_summary_item_id, 'trans_date' => decode($trans_year).'-'.$month.'-01', 'trans_type_id' => 1, 'broiler_summary_status'	=> 1);
					$ci->admin->insert_data('broiler_summary_tbl', $set);
				}
			}
		}

		for ($broiler_summary_item_id = 47; $broiler_summary_item_id <= 57 ; $broiler_summary_item_id++) {$set = array('broiler_summary_status' =>	2);
			$where = array('bc_id'	=>	decode($bc_id),	'broiler_summary_item_id'	=> $broiler_summary_item_id, 'YEAR(trans_date)' => decode($trans_year), 'trans_type_id' =>	1);
			$ci->admin->update_data('broiler_summary_tbl', $set, $where);
			
			$total[47] = $laki_sa_alaga;
			$total[48] = $electricity_incentive;
			$total[49] = $gawa_incentive;
			$total[50] = $kaagapay_incentive;

			$total[51] = $cash_on_catch_incentive;
			$total[52] = $alw_forecasting_support;
			$total[53] = $kkk_incentive;
			$total[54] = $zero_antibiotic;
			$total[55] = $sdg_incentive;
			$total[56] = $right_size_bonus_new;
			$total[57] = $uniformity_bonus_new;

			if($broiler_summary_item_id >= 47){
				for($month = 1; $month <= 12; $month++){
					$set = array('trans_qty' =>	round($total[$broiler_summary_item_id][$month], dec_places()), 'bc_id'	=>	decode($bc_id),	'broiler_summary_item_id'	=> $broiler_summary_item_id, 'trans_date' => decode($trans_year).'-'.$month.'-01', 'trans_type_id' => 1, 'broiler_summary_status'	=> 1);
					$ci->admin->insert_data('broiler_summary_tbl', $set);
				}
			}
		}

		for($month = 1; $month <= 12; $month++){
			$result[$month] =
				$basic_fee[$month] +
				$basic_fee_nae[$month] +
				$hr_incentive[$month] +
				$hr_incentive_nae[$month] +
				$fcr_incentive[$month] +
				
				$uniformity_bonus[$month] +
				$right_size_bonus[$month] +
				$performance_bonus[$month] +
				$brooding_incentive[$month] +
				$feeds_efficiency_bonus[$month] +
				
				$bpi_incentive[$month] +
				$weight_incentive[$month] +
				$tunnel_vent_incentive[$month] +
				$construction_incentive[$month] +
				$cleaning_incentive[$month] +
				
				$recruitment_incentive[$month] +
				$estimated_cg_fee_adj[$month] +
				$fcr_incentive_new[$month] +
				$performance_bonus_new[$month] +
				$bei_incentive[$month] +

				$special_loyalty_incentive[$month] +
				$farm_management_incentive[$month] +
				$nae_incentive[$month] +
				$nae_plus_incentive[$month] +
				$loyalty_incentive[$month] +

				$alw_adjustment[$month] +
				$weight_incentive_2[$month] +
				$nae_special_incentive[$month] +
				$laki_sa_alaga[$month] +
				$electricity_incentive[$month] +
				$gawa_incentive[$month] +
				$kaagapay_incentive[$month] +
				$cash_on_catch_incentive[$month] +
				$alw_forecasting_support[$month] +
				$kkk_incentive[$month] +
				$zero_antibiotic[$month] +
				$sdg_incentive[$month] +
				$right_size_bonus_new[$month] +
				$uniformity_bonus_new[$month];
			
		}

		return $result;
	}

	function growers_fee_amount($bc_id, $trans_year,  $doctype){
		$ibc_percentage = get_broiler_budget_qty(115, decode($bc_id), decode($trans_year),  $doctype) != false ? get_broiler_budget_qty(115, decode($bc_id), decode($trans_year),  $doctype) : 0;
		$external_growers_fee = external_growers_fee($bc_id, $trans_year,  $doctype);
		$ibc_opex = ibc_opex($bc_id, $trans_year,  $doctype);
		if($ibc_percentage){
			$i = 1;
			foreach ($ibc_percentage as $row) {
				$ibc_percentage_val[$i] = $row->broiler_budget_qty;
				$ibc_percentage_amount_type_val[$i] = $row->amount_type_id;
				$i++;
			}
			for($month = 1; $month <= 12; $month++){
				$ibc_percentage_val[$month] = amount_type($ibc_percentage_val[$month], $ibc_percentage_amount_type_val[$month]);
				$external_percentage[$month] = 1 - $ibc_percentage_val[$month];

				$external_growers_fee[$month] = $external_growers_fee[$month] * $external_percentage[$month];
				$result[$month] = $external_growers_fee[$month] + $ibc_opex[$month];
				$result[$month] = round($result[$month], 3);

				$broiler_line_item_id = 4;
				$ci =& get_instance();
				$ci->load->model('admin_model', 'admin');
				$check = $ci->admin->check_data('broiler_amount_summary_tbl', array('bc_id'	=>	decode($bc_id),	'broiler_line_item_id'	=> $broiler_line_item_id, 'YEAR(trans_date)' => decode($trans_year), 'MONTH(trans_date)' => $month, 'trans_type_id' =>	1), true);
				if($check['result']){
					$set = array('trans_qty' =>	round($result[$month], dec_places()), 'trans_type_id' => 1);
					$where = array('bc_id'	=>	decode($bc_id),	'broiler_line_item_id'	=> $broiler_line_item_id, 'YEAR(trans_date)' => decode($trans_year), 'MONTH(trans_date)' => $month, 'trans_type_id' =>	1);
					$ci->admin->update_data('broiler_amount_summary_tbl', $set, $where);
				} else {
					$set = array('trans_qty' =>	round($result[$month], dec_places()), 'bc_id'	=>	decode($bc_id),	'broiler_line_item_id'	=> $broiler_line_item_id, 'trans_date' => decode($trans_year).'-'.$month.'-01', 'trans_type_id' => 1);
					$ci->admin->insert_data('broiler_amount_summary_tbl', $set);
				}
			}
			return $result;
		} else {
			return $result = 0;
		}
		
	}

	function growers_fee($bc_id, $trans_year,  $doctype){
		$ci =& get_instance();
		$ci->load->model('admin_model', 'admin');

		$growers_fee_amount = growers_fee_amount($bc_id, $trans_year,  $doctype);
		$ibc_opex = ibc_opex($bc_id, $trans_year,  $doctype);
		$harvested_kilo = harvested_kilo($bc_id, $trans_year,  $doctype);
		$external_growers_fee = external_growers_fee($bc_id, $trans_year,  $doctype);

		
		$broiler_summary_item_id = 2; //IBC OPEX
		$set = array('broiler_summary_status' =>	2);
		$where = array('bc_id'	=>	decode($bc_id),	'broiler_summary_item_id'	=> $broiler_summary_item_id, 'YEAR(trans_date)' => decode($trans_year), 'trans_type_id' =>	1);
		$ci->admin->update_data('broiler_summary_tbl', $set, $where);
		

		$broiler_summary_item_id = 1; //GROWERS FEE AMOUNT
		$set = array('broiler_summary_status' =>	2);
		$where = array('bc_id'	=>	decode($bc_id),	'broiler_summary_item_id'	=> $broiler_summary_item_id, 'YEAR(trans_date)' => decode($trans_year), 'trans_type_id' =>	1);
		$ci->admin->update_data('broiler_summary_tbl', $set, $where);

		$broiler_summary_item_id = 32; //GROWERS FEE
		$set = array('broiler_summary_status' =>	2);
		$where = array('bc_id'	=>	decode($bc_id),	'broiler_summary_item_id'	=> $broiler_summary_item_id, 'YEAR(trans_date)' => decode($trans_year), 'trans_type_id' =>	1);
		$ci->admin->update_data('broiler_summary_tbl', $set, $where);

		for($month = 1; $month <= 12; $month++){
			if($harvested_kilo[$month] == 0){
				$result[$month] = 0;
			} else {
				$result[$month] = $growers_fee_amount[$month]/$harvested_kilo[$month];
			}
			$broiler_group_id = 2;
			$check = $ci->admin->check_data('broiler_cost_summary_tbl', array('bc_id'	=>	decode($bc_id),	'broiler_group_id'	=> $broiler_group_id, 'YEAR(trans_date)' => decode($trans_year), 'MONTH(trans_date)' => $month), true);
			if($check['result']){
				$set = array('trans_qty' =>	round($result[$month], dec_places()), 'trans_type_id' => 1);
				$where = array('bc_id'	=>	decode($bc_id),	'broiler_group_id'	=> $broiler_group_id, 'YEAR(trans_date)' => decode($trans_year), 'MONTH(trans_date)' => $month);
				$ci->admin->update_data('broiler_cost_summary_tbl', $set, $where);
			} else {
				$set = array('trans_qty' =>	round($result[$month], dec_places()), 'bc_id'	=>	decode($bc_id),	'broiler_group_id'	=> $broiler_group_id, 'trans_date' => decode($trans_year).'-'.$month.'-01', 'trans_type_id' => 1);
				$ci->admin->insert_data('broiler_cost_summary_tbl', $set);
			}

			if(!empty($ibc_opex)){
				$broiler_summary_item_id = 2; //IBC OPEX
				$set = array('trans_qty' =>	round($ibc_opex[$month], dec_places()), 'bc_id'	=>	decode($bc_id),	'broiler_summary_item_id'	=> $broiler_summary_item_id, 'trans_date' => decode($trans_year).'-'.$month.'-01', 'trans_type_id' => 1, 'broiler_summary_status'	=> 1);
				$ci->admin->insert_data('broiler_summary_tbl', $set);

				$broiler_summary_item_id = 3; //Growers Fee Amount
				$set = array('trans_qty' =>	round($external_growers_fee[$month], dec_places()), 'bc_id'	=>	decode($bc_id),	'broiler_summary_item_id'	=> $broiler_summary_item_id, 'trans_date' => decode($trans_year).'-'.$month.'-01', 'trans_type_id' => 1, 'broiler_summary_status'	=> 1);
				$ci->admin->insert_data('broiler_summary_tbl', $set);
			}

			$broiler_summary_item_id = 1; //Growers Fee Amount
			$set = array('trans_qty' =>	round($growers_fee_amount[$month], dec_places()), 'bc_id'	=>	decode($bc_id),	'broiler_summary_item_id'	=> $broiler_summary_item_id, 'trans_date' => decode($trans_year).'-'.$month.'-01', 'trans_type_id' => 1, 'broiler_summary_status'	=> 1);
			$ci->admin->insert_data('broiler_summary_tbl', $set);

			$broiler_summary_item_id = 32; //GROWERS FEE
			$set = array('trans_qty' =>	round($result[$month], dec_places()), 'bc_id'	=>	decode($bc_id),	'broiler_summary_item_id'	=> $broiler_summary_item_id, 'trans_date' => decode($trans_year).'-'.$month.'-01', 'trans_type_id' => 1, 'broiler_summary_status'	=> 1);
			$ci->admin->insert_data('broiler_summary_tbl', $set);
		}
		return $result;
	}

	function broiler_cost($bc_id, $trans_year,  $doctype){

		//replaced by querrying in a broiler_cost_summary_tbl table, reduced loading time.
		$growers_fee = growers_fee($bc_id, $trans_year,  $doctype);
		$feed_cost = feed_cost($bc_id, $trans_year,  $doctype);
		$vaccines = vaccines($bc_id, $trans_year,  $doctype);
		$medicines = medicines($bc_id, $trans_year,  $doctype);
		$doc = doc($bc_id, $trans_year,  $doctype);
		$result = $growers_fee + $feed_cost + $vaccines + $medicines + $doc;
		return round($result, dec_places());
	}

	function get_basic_processing_details($prod_id, $bc_id, $process_type_id, $year){
		$ci =& get_instance();
		$ci->load->model('admin_model', 'admin');

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
						LEFT JOIN `unit_tbl` `g` ON `a`.`unit_id` = `g`.`unit_id`
						JOIN `prod_trans_tbl` `h` ON `a`.`prod_trans_id` = `h`.`prod_trans_id`
						AND `h`.`prod_id` = ".$prod_id." and `h`.`bc_id` = ".$bc_id." AND YEAR(a.prod_trans_dtl_date) = ".decode($year)." AND `h`.`process_type_id` = ".$process_type_id."
						JOIN `process_type_tbl` `i` ON `h`.`process_type_id` = `i`.`process_type_id`
						UNION ALL
							SELECT
								a.*, b.service_id,
								b.service_desc,
								b.service_code,
								d.component_type,
								d.order_base,
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
							JOIN `prod_trans_tbl` `h` ON `a`.`prod_trans_id` = `h`.`prod_trans_id`
							AND `h`.`prod_id` = ".$prod_id." and `h`.`bc_id` = ".$bc_id." AND YEAR(a.prod_trans_dtl_date) = ".decode($year)." AND `h`.`process_type_id` = ".$process_type_id."
							JOIN `process_type_tbl` `i` ON `h`.`process_type_id` = `i`.`process_type_id`
					) z
				ORDER BY
					z.order_base, z.material_desc";

		$prod_trans = $ci->admin->get_query($sql);


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
				AND `a`.`prod_trans_dtl_status` <> 5 and a.component_type_id = 7
				inner JOIN `prod_trans_tbl` `h` ON `a`.`prod_trans_id` = `h`.`prod_trans_id`
				AND `h`.`prod_id` = ".$prod_id." and `h`.`bc_id` = ".$bc_id." AND YEAR(a.prod_trans_dtl_date) = ".decode($year)." AND `h`.`process_type_id` = ".$process_type_id;
		//return $sql;
		$config_rate = $ci->admin->get_query($sql);

		$result = array();

		$live_sales = get_live_sales($bc_id, decode($year));
		$live_sales_counter = 1;
		$alw_live_counter = 1;
		if(!empty($live_sales)){
			foreach($live_sales as $r){
				
	 			$budget_live_sales_array[$live_sales_counter] = $r->live_sales_qty;
	 			$budget_live_alw_array[$alw_live_counter] = $r->alw_live;
	 			$budget_live_sales_kg_array[$alw_live_counter] =  $budget_live_sales_array[$live_sales_counter] * $budget_live_alw_array[$alw_live_counter];
	 			$alw_live_counter++;
	 			$live_sales_counter++;
			}
		} else {
			$budget_live_sales_array = 0;
			$budget_live_alw_array = 0;
			$budget_live_sales_kg_array = 0;
		}

		$harvested_data_heads = get_data('broiler_amount_summary_tbl a', array('a.broiler_line_item_id' => 7, 'a.bc_id' => $bc_id, 'YEAR(a.trans_date)' => decode($year), 'trans_type_id' => 1));
		$heads_counter = 1;
		if(!empty($harvested_data_heads)){
			foreach($harvested_data_heads as $r){
				if($r->broiler_line_item_id == 7){
		 			$budget_harvested_heads[$heads_counter] = $r->trans_qty;
		 			$heads_counter++;
	 			} else {
	 				$budget_harvested_heads = 0;
	 			}
			}
		} else {
			$budget_harvested_heads = 0;
		}

		$harvested_data_kilo = get_data('broiler_amount_summary_tbl a', array('a.broiler_line_item_id' => 6, 'a.bc_id' => $bc_id, 'YEAR(a.trans_date)' => decode($year), 'trans_type_id' => 1));
		

		$kilo_counter = 1;
		if(!empty($harvested_data_kilo)){
			foreach($harvested_data_kilo as $r){
	 			if($r->broiler_line_item_id == 6){
	 				$budget_harvested_kilo[$kilo_counter] = $r->trans_qty;
		 			$kilo_counter++;
	 			} else {
	 				$budget_harvested_kilo = 0;
	 			}
			}
		} else {
			$budget_harvested_kilo = 0;
		}
		
		for ($i=1; $i <= 12 ; $i++) {
			foreach($config_rate as $row){
				
				if($row->trans_month == $i){
					
					$rate = amount_type($row->rate, $row->amount_type_id);
					$doctype = encode('trans');

					$live_sales_val = $budget_live_sales_array[$row->trans_month];
					$live_alw_val = $budget_live_alw_array[$row->trans_month];
					$live_sales_kg_val = $budget_live_sales_kg_array[$row->trans_month];

					$harvested_heads =  $budget_harvested_heads == 0 ? 0 : $budget_harvested_heads[$i];
					$harvested_heads = $harvested_heads - $live_sales_val;

					$harvested_kilo =  $budget_harvested_kilo == 0 ? 0 : $budget_harvested_kilo[$i];
					$harvested_kilo = $harvested_kilo - $live_sales_kg_val;
					$broiler_alw = $harvested_heads == 0 ? 0 : $harvested_kilo/$harvested_heads;
					$broiler_cost = get_broiler_cost(encode($bc_id), $year, $i, $doctype);
					
					
					if($row->article_id == 10 && $row->article_type_id == 2){
						$doa_qty = $rate * $harvested_heads * -1;
						$doa_qty = round($doa_qty, 3);
					}
					//EMACIATED BIRDS
					if($row->article_id == 11 && $row->article_type_id == 2){
						$ema_qty = $rate * $harvested_heads * -1;
						$ema_qty = round($ema_qty, 3);
					}
					if($row->article_id == 9 && $row->article_type_id == 2){
						$kg_value = $rate * $harvested_kilo;
						$kg_value = round($kg_value, 3);
					}
					/*$harvested_heads = harvested_heads($bc_id, $year, $row->trans_month, encode('trans'));
					$harvested_kilo = harvested_kilo($bc_id, $year, $row->trans_month, encode('trans'));*/
					//DOA
					if($row->article_id == 10 && $row->article_type_id == 2){
						$doa_qty = $rate * $harvested_heads * -1;
						$doa_qty = round($doa_qty, 3);
					}
					//EMACIATED BIRDS
					if($row->article_id == 11 && $row->article_type_id == 2){
						$ema_qty = $rate * $harvested_heads * -1;
						$ema_qty = round($ema_qty, 3);
					}
					if($row->article_id == 9 && $row->article_type_id == 2){
						$kg_value = $rate * $harvested_kilo;
						$kg_value = round($kg_value, 3);

					}
				}
			}
			$total_amount = 0;
			$amount = 0;
			
			//LOOP FOR THE DISPLAY ON PER MONTH BASIS
			foreach($prod_trans as $row):
				
				if($row->trans_month == $i):
					
					if($row->process_type_id == 1){
						$rate = amount_type($row->rate, $row->amount_type_id);
						$cost = $row->cost;

						$qty = 0;
						
						$wgt = 0;
						$alw = 0;

						$total_loss = $ema_qty + $doa_qty;
						$qty_value = $total_loss + $harvested_heads;
						$qty_value = round($qty_value, 3);

						//COST RECOVERY
						if($row->component_type_id == 4){
							$qty = $rate * $harvested_kilo;
							$amount = round($qty, 3) * $row->cost * -1;
							$qty = round($qty,3);
							
							if($row->material_desc=='BOUNTY LIVER 1KG - FRESH'){
								$liver_fresh = round($rate * $harvested_kilo, 3);
							}
							if($row->material_desc=='BOUNTY GIZZARD'){
								$gizzard = round($rate * $harvested_kilo, 3);
							}

						}

						//PACKAGING
						if($row->component_type_id == 5){
							if($row->amount_type_id == 1){
								$qty = $rate * $qty_value;
								$amount = round($qty, 3) * $row->cost;
								$qty = round($qty,3);
								
							} else {
								$qty = $rate == 0 ? 0 : $qty_value/$rate;
								$amount = round($qty, 3) * $row->cost;
								$qty = round($qty,3);
								
							}
							if($row->material_desc=='LIVER PLASTIC'){
								$qty = $rate * $liver_fresh;
								$amount = round($qty, 3) * $row->cost;
								$qty = round($qty,3);
								
							}
							if($row->material_desc=='GIZZARD PLASTIC'){
								$qty = $rate * $gizzard;
								$amount = round($qty, 3) * $row->cost;
								$qty = round($qty,3);
								
							}
						}
						

						if($row->component_type_id == 3){ //CONVERSION COST
							//nmic hds
							if($row->article_id == 4 && $row->article_type_id == 2){
								$qty = $harvested_heads * $rate;
							} else if ($row->article_id == 5 && $row->article_type_id == 2){
								$qty = $kg_value * $rate;
							} else {
								$qty = $qty_value * $rate;
							}
							$amount = round($qty,3);
							$qty = 0;
							$cost = 0;
						} else if($row->component_type_id == 7){ //CONFIG BASE
							$qty = 0;
							$cost = 0;
							$amount = 0;
						} else if($row->component_type_id == 2){ //RAW MATERIALS
							$qty = $harvested_heads;
							$cost = round($broiler_cost, 3);
							$wgt = $harvested_kilo;
							$alw = round($broiler_alw,3);
							$amount = round($cost*$wgt,3);
							$rate = 0;
						} else if($row->component_type_id == 1){ //FINISHED GOODS
							$qty = $qty_value;
							$alw = $qty_value == 0 ? 0 : round($kg_value/$qty_value,3);
							$amount = $total_amount;
							$rate = '';
							$cost = $kg_value > 0 ? $total_amount/$kg_value : 0;
							$result['cost-'.$i] = round($cost, 2);
							$result['alw-'.$i] = $alw;
							$result['qty-'.$i] = $qty;
							$result['wgt-'.$i] = $kg_value;
							$result['amount-'.$i] = $amount;
						}

						$total_amount = $total_amount + $amount;
						if($row->amount_type_id == 1){
							$rate = $row->rate.'%';
							if($row->rate == 0){
								$rate = 0;
							}
						} else {
							$rate = $row->rate;
							if($row->rate == 0){
								$rate = 0;
							}
						}
					} else if($row->process_type_id == 2){
						
					}
				endif;
			endforeach;
		}
		return $result;	
	}

	function get_classification_details($prod_id, $bc_id, $process_type_id, $year){
		$ci =& get_instance();
		$ci->load->model('admin_model', 'admin');
		$result = array();

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
						LEFT JOIN `unit_tbl` `g` ON `a`.`unit_id` = `g`.`unit_id`
						JOIN `prod_trans_tbl` `h` ON `a`.`prod_trans_id` = `h`.`prod_trans_id`
						AND `h`.`prod_id` = ".$prod_id." and `h`.`bc_id` = ".$bc_id." AND YEAR(a.prod_trans_dtl_date) = ".decode($year)." AND `h`.`process_type_id` = ".$process_type_id."
						JOIN `process_type_tbl` `i` ON `h`.`process_type_id` = `i`.`process_type_id`
						UNION ALL
							SELECT
								a.*, b.service_id,
								b.service_desc,
								b.service_code,
								d.component_type,
								d.order_base,
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
							JOIN `prod_trans_tbl` `h` ON `a`.`prod_trans_id` = `h`.`prod_trans_id`
							AND `h`.`prod_id` = ".$prod_id." and `h`.`bc_id` = ".$bc_id." AND YEAR(a.prod_trans_dtl_date) = ".decode($year)." AND `h`.`process_type_id` = ".$process_type_id."
							JOIN `process_type_tbl` `i` ON `h`.`process_type_id` = `i`.`process_type_id`
					) z
				ORDER BY
					z.order_base, z.material_desc";
		$prod_trans = $ci->admin->get_query($sql);

		$check_fdc_mat = $ci->admin->check_data('material_tbl', array('material_code'	=> '1000124', 'material_status' => 1), true);

		if($check_fdc_mat['result']){
			$prod_id = $check_fdc_mat['info']->material_id;
		} else {
			$prod_id = 0;
		}
		$fdc_details = get_basic_processing_details($prod_id, $bc_id, 1, $year);
		
		//LOOP PER MONTH
		for ($i=1; $i <= 12 ; $i++) {
			
			$total_amount = 0;
			$amount = 0;
			
			//LOOP FOR THE DISPLAY ON PER MONTH BASIS
			foreach($prod_trans as $row):
				
				if($row->trans_month == $i):
					
					if($row->process_type_id == 2){ //CHECK IF CLASSIFICATION PROCESS
						if(!empty($fdc_details)){
							$rate = amount_type($row->rate, $row->amount_type_id);
							$cost = 0;
							$qty = 0;
							$wgt= 0;
							$alw = $row->cost; //MUST BE ALW IF CHANGED
							$amount = 0;
							if($row->component_type_id == 2){ //RAW MATERIALS
								$qty = $fdc_details['qty-'.$i];
								$wgt = $fdc_details['wgt-'.$i];
								$alw = $fdc_details['alw-'.$i];
								$cost = $fdc_details['cost-'.$i];
								
								$qty = number_format($qty,0,'.',',');
								$wgt = number_format($wgt,2,'.',',');
								$alw = number_format($alw,2,'.',',');
								$cost = number_format($cost,2,'.',',');
								$amount = $fdc_details['amount-'.$i];
							} else if($row->component_type_id == 1){ //FINISHED GOODS

								$qty = round($rate * $fdc_details['qty-'.$i], 3);
								$wgt = round($qty * $alw, 3);
								$cost = round($fdc_details['cost-'.$i], 3);
								$amount = round($wgt * $cost, 3);
								//$wgt = $fdc_details['wgt-'.$i];
								
								$result[$row->material_code.'-alw-'.$i] = $alw; 
								$result[$row->material_code.'-qty-'.$i] = $qty; 
								$result[$row->material_code.'-wgt-'.$i] = $wgt; 
								$result[$row->material_code.'-cost-'.$i] = $cost;
								$result[$row->material_code.'-amount-'.$i] = $amount; 
								
							}
						} else {
							$rate = amount_type($row->rate, $row->amount_type_id);
							$cost = 0;
							$qty = 0;
							$wgt= 0;
							$alw = $row->cost;
							$amount = 0;
						}
					}
				endif;
			endforeach;
		}
		return $result;	
	}

	function get_further_process_neckless_details($prod_id, $bc_id, $process_type_id, $year){
		$ci =& get_instance();
		$ci->load->model('admin_model', 'admin');
		$result = array();

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
						LEFT JOIN `unit_tbl` `g` ON `a`.`unit_id` = `g`.`unit_id`
						JOIN `prod_trans_tbl` `h` ON `a`.`prod_trans_id` = `h`.`prod_trans_id`
						AND `h`.`prod_id` = ".$prod_id." and `h`.`bc_id` = ".$bc_id." AND YEAR(a.prod_trans_dtl_date) = ".decode($year)." AND `h`.`process_type_id` IN (3, 6, 7, 8)
						JOIN `process_type_tbl` `i` ON `h`.`process_type_id` = `i`.`process_type_id`
						UNION ALL
							SELECT
								a.*, b.service_id,
								b.service_desc,
								b.service_code,
								d.component_type,
								d.order_base,
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
							JOIN `prod_trans_tbl` `h` ON `a`.`prod_trans_id` = `h`.`prod_trans_id`
							AND `h`.`prod_id` = ".$prod_id." and `h`.`bc_id` = ".$bc_id." AND YEAR(a.prod_trans_dtl_date) = ".decode($year)." AND `h`.`process_type_id` IN (3, 6, 7, 8)
							JOIN `process_type_tbl` `i` ON `h`.`process_type_id` = `i`.`process_type_id`
					) z
				ORDER BY
					z.order_base, z.material_desc";

		$prod_trans = $ci->admin->get_query($sql);

		//LOOP PER MONTH
		for ($i=1; $i <= 12 ; $i++) {
			
			$total_amount = 0;
			$amount = 0;
			$produced_qty = 0;
			$produced_wgt = 0;
			$lossed_wgt = 0;
			
			//LOOP FOR THE DISPLAY ON PER MONTH BASIS
			foreach($prod_trans as $row):
				
				if($row->trans_month == $i):
					
					if($row->process_type_id == 3 || $row->process_type_id == 6 || $row->process_type_id == 7 || $row->process_type_id == 8){ //FURTHER PROCESSING
						$rate = amount_type($row->rate, $row->amount_type_id);
						
						if($row->component_type_id == 2){
							//fixed fdc - classification bom
							//$classification_bom = get_data('material_tbl', array('material_code'	=>	'1000124', 'material_status'	=> 1), true);

							//$prod_id = !$classification_bom ? 0 : $classification_bom->material_id;
							$prod_id = $row->material_id;
							if($prod_id){
								$classificaion_details = get_classification_details($prod_id, $bc_id, 2, $year);
							} else {
								$classificaion_details = 0;
							}

							if(!empty($classificaion_details) && $classificaion_details != 0){
								$qty = $classificaion_details[$row->material_code.'-qty-'.$i];
								$wgt = $classificaion_details[$row->material_code.'-wgt-'.$i];
								$alw = $classificaion_details[$row->material_code.'-alw-'.$i];
								$cost = $classificaion_details[$row->material_code.'-cost-'.$i];
								$amount = $classificaion_details[$row->material_code.'-amount-'.$i];
								$produced_wgt = $wgt;
								$produced_qty = $qty;
							} else {
								$qty = 0;
								$wgt = 0;
								$cost = 0;
								$amount = 0;
								$alw = 0;
							}
						} else if($row->component_type_id == 4){ //COST RECOVERY
							
							$qty = round($rate*$produced_wgt*-1, 3);
							$wgt = '';
							$alw = '';
							$cost = $row->cost;
							$amount = $cost*$qty;
							$lossed_wgt = round($lossed_wgt + $qty, 3);

						} else if($row->component_type_id == 3){
							$wgt = '';
							$qty = '';
							$alw = '';
							$cost = '';
							$amount = $produced_qty*$rate;
						} else if($row->component_type_id == 1){ //FINISHED GOODS
							$qty = $produced_qty;
							$wgt = $produced_wgt + $lossed_wgt;
							$alw = $qty <= 0 ? 0 : $wgt/$qty;
							$cost = $wgt <= 0 ? 0 : round($total_amount/$wgt, 3);
							$amount = $total_amount;

							$result[$row->material_code.'-alw-'.$i] = $alw; 
							$result[$row->material_code.'-qty-'.$i] = $qty; 
							$result[$row->material_code.'-wgt-'.$i] = $wgt; 
							$result[$row->material_code.'-cost-'.$i] = $cost;
							$result[$row->material_code.'-amount-'.$i] = $amount;
							
						}
						$total_amount = $total_amount + $amount;
					}
				endif;
			endforeach;
		}
		return $result;
	}

	function get_further_process_marination_details($prod_id, $bc_id, $process_type_id, $year){
		$ci =& get_instance();
		$ci->load->model('admin_model', 'admin');
		$result = array();
		
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
						LEFT JOIN `unit_tbl` `g` ON `a`.`unit_id` = `g`.`unit_id`
						JOIN `prod_trans_tbl` `h` ON `a`.`prod_trans_id` = `h`.`prod_trans_id`
						AND `h`.`prod_id` = ".$prod_id." and `h`.`bc_id` = ".$bc_id." AND YEAR(a.prod_trans_dtl_date) = ".decode($year)." AND `h`.`process_type_id` = ".$process_type_id."
						JOIN `process_type_tbl` `i` ON `h`.`process_type_id` = `i`.`process_type_id`
						UNION ALL
							SELECT
								a.*, b.service_id,
								b.service_desc,
								b.service_code,
								d.component_type,
								d.order_base,
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
							JOIN `prod_trans_tbl` `h` ON `a`.`prod_trans_id` = `h`.`prod_trans_id`
							AND `h`.`prod_id` = ".$prod_id." and `h`.`bc_id` = ".$bc_id." AND YEAR(a.prod_trans_dtl_date) = ".decode($year)." AND `h`.`process_type_id` = ".$process_type_id."
							JOIN `process_type_tbl` `i` ON `h`.`process_type_id` = `i`.`process_type_id`
					) z
				ORDER BY
					z.order_base, z.material_desc";


		
		$prod_trans = $ci->admin->get_query($sql);

		//LOOP PER MONTH
		for ($i=1; $i <= 12 ; $i++) {
			
			$total_amount = 0;
			$amount = 0;
			$produced_qty = 0;
			$produced_wgt = 0;
			$lossed_wgt = 0;
			$rawmats_rate = 0;
			
			//LOOP FOR THE DISPLAY ON PER MONTH BASIS
			foreach($prod_trans as $row):
				
				if($row->trans_month == $i):
					
					if($row->process_type_id == 4){ //FURTHER PROCESSING MARINATION
						$rate = amount_type($row->rate, $row->amount_type_id);
						if($row->component_type_id == 2){ //RAWMATS

							$neckless_details = get_further_process_neckless_details($row->material_id, $bc_id, 3, $year);
							$rawmats_rate = $rate;
							$qty = round($neckless_details[$row->material_code.'-qty-'.$i], 0);
							$alw = $neckless_details[$row->material_code.'-alw-'.$i];
							$cost = $neckless_details[$row->material_code.'-cost-'.$i];
							$wgt = $qty*$alw;
							$amount = $wgt*$cost;
							//$wgt = $neckless_details[$row->material_code.'-wgt-'.$i];
							//$amount = $neckless_details[$row->material_code.'-amount-'.$i];
							$produced_wgt = $wgt;
							$produced_qty = $qty;
							
						} else if($row->component_type_id == 6) { //MARINADE
							$qty = $rate * $produced_qty;
							$wgt = '';
							$alw = '';
							$cost = $row->cost;
							$amount = $qty*$cost;

						} else if($row->component_type_id == 5) { //PACKAGING
							$qty = $rate <= 0 || $rate == '' ? 0 : $produced_qty/$rate;
							$wgt = '';
							$alw = '';
							$cost = $row->cost;
							$amount = $qty*$cost;

						} else if($row->component_type_id == 3) { //conversion cost
							$qty = '';
							$wgt = '';
							$alw = '';
							$cost = '';
							$amount = $rate*$produced_qty;
						} else if($row->component_type_id == 1){ //FINISHED GOODS
							$qty = $rawmats_rate > 0 ? $produced_wgt/$rawmats_rate : 0;
							$wgt = '';
							$alw = '';
							$cost = $qty <= 0 ? 0 : $total_amount/$qty;
							$amount = $total_amount;

							$result[$row->material_code.'-alw-'.$i] = $alw; 
							$result[$row->material_code.'-qty-'.$i] = $qty; 
							$result[$row->material_code.'-wgt-'.$i] = $wgt; 
							$result[$row->material_code.'-cost-'.$i] = $cost;
							$result[$row->material_code.'-amount-'.$i] = $amount;

						} else {
							$qty = '';
							$wgt = '';
							$alw = '';
							$cost = '';
							$amount = '';
						}
						$total_amount = $total_amount + $amount;
					}
				endif;
			endforeach;
		}
		return $result;
	}

	function decimal_format($num, $dec_places, $prefix_sign = null){
			
		if($num == '' || $num <= 0){
			if($num < 0){
				return '('.$prefix_sign.number_format(-$num,$dec_places,'.',',').')';
			} else {
				return '';
			}
		}else{
			return $prefix_sign.number_format($num,$dec_places,'.',',');
		}
	}

	function time_stamp_display($myDate){
		if($myDate){
			$ts = date("m/d/Y h:i A",strtotime($myDate));
		} else {
			$ts = NULL;
		}
			
		return $ts;
	}

	function date_display($myTS, $format = 'Y-m-d H:i'){
        if($myTS){
            $sd = date($format,strtotime($myTS));
        } else {
            $sd = NULL;
        }
            
        return $sd;
        
    }


	function standard_date($myTS){
		if($myTS){
			$sd = date("Y-m-d H:i:s",strtotime($myTS));
		} else {
			$sd = NULL;
		}
			
		return $sd;	
	}

	function get_average($arr_value){
		$count = 0;
		$total = 0;
		foreach($arr_value as $row){
			$value = $row;
			if($value > 0){
				$total += $value;
				$count++;
			}
		}

		$avg_value = 0;
		if($count > 0 && $total > 0){
			$avg_value = $total/$count;
		}

		return $avg_value;
	}

	function get_min($arr_value){
		
		$arr_zero = array('0', '0.00', '0.000', '0.0000', '0.00000', '0.000000');
		$min = 0;

		$value = array_values(array_diff($arr_value, $arr_zero));
		if(!empty($value)){
			$min = min($value);
		}

		return $min;
	}

	function get_max($arr_value){
		
		$arr_zero = array('0', '0.00');
		$max = max(array_values(array_diff($arr_value, $arr_zero)));
		return $max;
	}
}