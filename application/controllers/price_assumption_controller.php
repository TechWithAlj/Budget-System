public function price_assumption_report(){
		$year = $this->_active_year();
		$user_info = $this->get_user_info();
 		$cost_center_id = $user_info['cost_center_id'];
 		$bc_id = $user_info['bc_id'];

		$join_live = array(
			'sales_item_tbl b' => 'a.sales_id = b.sales_id AND a.sales_status = 1 AND b.sales_item_status = 1 AND a.sales_year = ' . $year,
			'sales_details_tbl c' => 'b.sales_item_id = c.sales_item_id AND c.sales_det_status = 1',
			'material_tbl d' => 'b.material_id = d.material_id',
			'material_group_tbl e' => 'd.material_group_id = e.material_group_id',
			'outlet_tbl f' => 'a.outlet_id = f.outlet_id AND f.bc_id = ' . $bc_id,
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

		$join_tds = array(
			'sales_item_tbl b' => 'a.sales_id = b.sales_id AND a.sales_status = 1 AND b.sales_item_status = 1 AND a.sales_year = ' . $year,
			'sales_details_tbl c' => 'b.sales_item_id = c.sales_item_id AND c.sales_det_status = 1',
			'material_tbl d' => 'b.material_id = d.material_id',
			'material_group_tbl e' => 'd.material_group_id = e.material_group_id AND e.material_group_name = "DRESSED"',
			'outlet_tbl f' => 'a.outlet_id = f.outlet_id AND f.bc_id = ' . $bc_id,
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

		$join_smkt = array(
			'sales_item_tbl b' => 'a.sales_id = b.sales_id AND a.sales_status = 1 AND b.sales_item_status = 1 AND a.sales_year = ' . $year,
			'sales_details_tbl c' => 'b.sales_item_id = c.sales_item_id AND c.sales_det_status = 1',
			'material_tbl d' => 'b.material_id = d.material_id',
			'material_group_tbl e' => 'd.material_group_id = e.material_group_id AND e.material_group_name = "DRESSED"',
			'outlet_tbl f' => 'a.outlet_id = f.outlet_id AND f.bc_id = ' . $bc_id,
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

		$get_ctg_orc = $this->admin->get_query("SELECT p.size_name, AVG(asp_tbl.jan_asp) as orc_jan, AVG(asp_tbl.feb_asp) as orc_feb, AVG(asp_tbl.mar_asp) as orc_mar, AVG(asp_tbl.apr_asp) as orc_apr, AVG(asp_tbl.may_asp) as orc_may, AVG(asp_tbl.jun_asp) as orc_jun, AVG(asp_tbl.jul_asp) as orc_jul, AVG(asp_tbl.aug_asp) as orc_aug, AVG(asp_tbl.sep_asp) as orc_sep, AVG(asp_tbl.oct_asp) as orc_oct, AVG(asp_tbl.nov_asp) as orc_nov, AVG(asp_tbl.dec_asp) as orc_dec FROM material_tbl o, size_tbl p,

		(SELECT m.material_id, m.size_id,

		(SELECT AVG(c.sales_det_asp) FROM `sales_tbl` `a` JOIN `sales_item_tbl` `b` ON `a`.`sales_id` = `b`.`sales_id` AND `a`.`sales_status` = 1 AND `b`.`sales_item_status` = 1 AND `a`.`sales_year` = 2020 JOIN `sales_details_tbl` `c` ON `b`.`sales_item_id` = `c`.`sales_item_id` AND `c`.`sales_det_status` = 1 JOIN `material_tbl` `d` ON `b`.`material_id` = `d`.`material_id` JOIN `material_group_tbl` `e` ON `d`.`material_group_id` = `e`.`material_group_id` JOIN `outlet_tbl` `f` ON `a`.`outlet_id` = `f`.`outlet_id` JOIN `outlet_brand_tbl` `g` ON `f`.`outlet_id` = `g`.`outlet_id` AND `g`.`outlet_brand_status` = 1 JOIN `brand_tbl` `h` ON `g`.`brand_id` = `h`.`brand_id` AND `h`.`brand_name` = 'CHOOKS-TO-GO' AND c.sales_det_date = '" . $year . "-01-01' WHERE d.material_id = m.material_id AND f.bc_id = " . $bc_id . ") as jan_asp,
    
    	(SELECT AVG(c.sales_det_asp) FROM `sales_tbl` `a` JOIN `sales_item_tbl` `b` ON `a`.`sales_id` = `b`.`sales_id` AND `a`.`sales_status` = 1 AND `b`.`sales_item_status` = 1 AND `a`.`sales_year` = 2020 JOIN `sales_details_tbl` `c` ON `b`.`sales_item_id` = `c`.`sales_item_id` AND `c`.`sales_det_status` = 1 JOIN `material_tbl` `d` ON `b`.`material_id` = `d`.`material_id` JOIN `material_group_tbl` `e` ON `d`.`material_group_id` = `e`.`material_group_id` JOIN `outlet_tbl` `f` ON `a`.`outlet_id` = `f`.`outlet_id` JOIN `outlet_brand_tbl` `g` ON `f`.`outlet_id` = `g`.`outlet_id` AND `g`.`outlet_brand_status` = 1 JOIN `brand_tbl` `h` ON `g`.`brand_id` = `h`.`brand_id` AND `h`.`brand_name` = 'CHOOKS-TO-GO' AND c.sales_det_date = '" . $year . "-02-01' WHERE d.material_id = m.material_id AND f.bc_id = " . $bc_id . ") as feb_asp,
    
    	(SELECT AVG(c.sales_det_asp) FROM `sales_tbl` `a` JOIN `sales_item_tbl` `b` ON `a`.`sales_id` = `b`.`sales_id` AND `a`.`sales_status` = 1 AND `b`.`sales_item_status` = 1 AND `a`.`sales_year` = 2020 JOIN `sales_details_tbl` `c` ON `b`.`sales_item_id` = `c`.`sales_item_id` AND `c`.`sales_det_status` = 1 JOIN `material_tbl` `d` ON `b`.`material_id` = `d`.`material_id` JOIN `material_group_tbl` `e` ON `d`.`material_group_id` = `e`.`material_group_id` JOIN `outlet_tbl` `f` ON `a`.`outlet_id` = `f`.`outlet_id` JOIN `outlet_brand_tbl` `g` ON `f`.`outlet_id` = `g`.`outlet_id` AND `g`.`outlet_brand_status` = 1 JOIN `brand_tbl` `h` ON `g`.`brand_id` = `h`.`brand_id` AND `h`.`brand_name` = 'CHOOKS-TO-GO' AND c.sales_det_date = '" . $year . "-03-01' WHERE d.material_id = m.material_id AND f.bc_id = " . $bc_id . ") as mar_asp,
    
   		(SELECT AVG(c.sales_det_asp) FROM `sales_tbl` `a` JOIN `sales_item_tbl` `b` ON `a`.`sales_id` = `b`.`sales_id` AND `a`.`sales_status` = 1 AND `b`.`sales_item_status` = 1 AND `a`.`sales_year` = 2020 JOIN `sales_details_tbl` `c` ON `b`.`sales_item_id` = `c`.`sales_item_id` AND `c`.`sales_det_status` = 1 JOIN `material_tbl` `d` ON `b`.`material_id` = `d`.`material_id` JOIN `material_group_tbl` `e` ON `d`.`material_group_id` = `e`.`material_group_id` JOIN `outlet_tbl` `f` ON `a`.`outlet_id` = `f`.`outlet_id` JOIN `outlet_brand_tbl` `g` ON `f`.`outlet_id` = `g`.`outlet_id` AND `g`.`outlet_brand_status` = 1 JOIN `brand_tbl` `h` ON `g`.`brand_id` = `h`.`brand_id` AND `h`.`brand_name` = 'CHOOKS-TO-GO' AND c.sales_det_date = '" . $year . "-04-01' WHERE d.material_id = m.material_id AND f.bc_id = " . $bc_id . ") as apr_asp,
    
    	(SELECT AVG(c.sales_det_asp) FROM `sales_tbl` `a` JOIN `sales_item_tbl` `b` ON `a`.`sales_id` = `b`.`sales_id` AND `a`.`sales_status` = 1 AND `b`.`sales_item_status` = 1 AND `a`.`sales_year` = 2020 JOIN `sales_details_tbl` `c` ON `b`.`sales_item_id` = `c`.`sales_item_id` AND `c`.`sales_det_status` = 1 JOIN `material_tbl` `d` ON `b`.`material_id` = `d`.`material_id` JOIN `material_group_tbl` `e` ON `d`.`material_group_id` = `e`.`material_group_id` JOIN `outlet_tbl` `f` ON `a`.`outlet_id` = `f`.`outlet_id` JOIN `outlet_brand_tbl` `g` ON `f`.`outlet_id` = `g`.`outlet_id` AND `g`.`outlet_brand_status` = 1 JOIN `brand_tbl` `h` ON `g`.`brand_id` = `h`.`brand_id` AND `h`.`brand_name` = 'CHOOKS-TO-GO' AND c.sales_det_date = '" . $year . "-05-01' WHERE d.material_id = m.material_id AND f.bc_id = " . $bc_id . ") as may_asp,
    
    	(SELECT AVG(c.sales_det_asp) FROM `sales_tbl` `a` JOIN `sales_item_tbl` `b` ON `a`.`sales_id` = `b`.`sales_id` AND `a`.`sales_status` = 1 AND `b`.`sales_item_status` = 1 AND `a`.`sales_year` = 2020 JOIN `sales_details_tbl` `c` ON `b`.`sales_item_id` = `c`.`sales_item_id` AND `c`.`sales_det_status` = 1 JOIN `material_tbl` `d` ON `b`.`material_id` = `d`.`material_id` JOIN `material_group_tbl` `e` ON `d`.`material_group_id` = `e`.`material_group_id` JOIN `outlet_tbl` `f` ON `a`.`outlet_id` = `f`.`outlet_id` JOIN `outlet_brand_tbl` `g` ON `f`.`outlet_id` = `g`.`outlet_id` AND `g`.`outlet_brand_status` = 1 JOIN `brand_tbl` `h` ON `g`.`brand_id` = `h`.`brand_id` AND `h`.`brand_name` = 'CHOOKS-TO-GO' AND c.sales_det_date = '" . $year . "-06-01' WHERE d.material_id = m.material_id AND f.bc_id = " . $bc_id . ") as jun_asp,
    
    	(SELECT AVG(c.sales_det_asp) FROM `sales_tbl` `a` JOIN `sales_item_tbl` `b` ON `a`.`sales_id` = `b`.`sales_id` AND `a`.`sales_status` = 1 AND `b`.`sales_item_status` = 1 AND `a`.`sales_year` = 2020 JOIN `sales_details_tbl` `c` ON `b`.`sales_item_id` = `c`.`sales_item_id` AND `c`.`sales_det_status` = 1 JOIN `material_tbl` `d` ON `b`.`material_id` = `d`.`material_id` JOIN `material_group_tbl` `e` ON `d`.`material_group_id` = `e`.`material_group_id` JOIN `outlet_tbl` `f` ON `a`.`outlet_id` = `f`.`outlet_id` JOIN `outlet_brand_tbl` `g` ON `f`.`outlet_id` = `g`.`outlet_id` AND `g`.`outlet_brand_status` = 1 JOIN `brand_tbl` `h` ON `g`.`brand_id` = `h`.`brand_id` AND `h`.`brand_name` = 'CHOOKS-TO-GO' AND c.sales_det_date = '" . $year . "-07-01' WHERE d.material_id = m.material_id AND f.bc_id = " . $bc_id . ") as jul_asp,
    
    	(SELECT AVG(c.sales_det_asp) FROM `sales_tbl` `a` JOIN `sales_item_tbl` `b` ON `a`.`sales_id` = `b`.`sales_id` AND `a`.`sales_status` = 1 AND `b`.`sales_item_status` = 1 AND `a`.`sales_year` = 2020 JOIN `sales_details_tbl` `c` ON `b`.`sales_item_id` = `c`.`sales_item_id` AND `c`.`sales_det_status` = 1 JOIN `material_tbl` `d` ON `b`.`material_id` = `d`.`material_id` JOIN `material_group_tbl` `e` ON `d`.`material_group_id` = `e`.`material_group_id` JOIN `outlet_tbl` `f` ON `a`.`outlet_id` = `f`.`outlet_id` JOIN `outlet_brand_tbl` `g` ON `f`.`outlet_id` = `g`.`outlet_id` AND `g`.`outlet_brand_status` = 1 JOIN `brand_tbl` `h` ON `g`.`brand_id` = `h`.`brand_id` AND `h`.`brand_name` = 'CHOOKS-TO-GO' AND c.sales_det_date = '" . $year . "-08-01' WHERE d.material_id = m.material_id AND f.bc_id = " . $bc_id . ") as aug_asp,
    
    	(SELECT AVG(c.sales_det_asp) FROM `sales_tbl` `a` JOIN `sales_item_tbl` `b` ON `a`.`sales_id` = `b`.`sales_id` AND `a`.`sales_status` = 1 AND `b`.`sales_item_status` = 1 AND `a`.`sales_year` = 2020 JOIN `sales_details_tbl` `c` ON `b`.`sales_item_id` = `c`.`sales_item_id` AND `c`.`sales_det_status` = 1 JOIN `material_tbl` `d` ON `b`.`material_id` = `d`.`material_id` JOIN `material_group_tbl` `e` ON `d`.`material_group_id` = `e`.`material_group_id` JOIN `outlet_tbl` `f` ON `a`.`outlet_id` = `f`.`outlet_id` JOIN `outlet_brand_tbl` `g` ON `f`.`outlet_id` = `g`.`outlet_id` AND `g`.`outlet_brand_status` = 1 JOIN `brand_tbl` `h` ON `g`.`brand_id` = `h`.`brand_id` AND `h`.`brand_name` = 'CHOOKS-TO-GO' AND c.sales_det_date = '" . $year . "-09-01' WHERE d.material_id = m.material_id AND f.bc_id = " . $bc_id . ") as sep_asp,
    
    	(SELECT AVG(c.sales_det_asp) FROM `sales_tbl` `a` JOIN `sales_item_tbl` `b` ON `a`.`sales_id` = `b`.`sales_id` AND `a`.`sales_status` = 1 AND `b`.`sales_item_status` = 1 AND `a`.`sales_year` = 2020 JOIN `sales_details_tbl` `c` ON `b`.`sales_item_id` = `c`.`sales_item_id` AND `c`.`sales_det_status` = 1 JOIN `material_tbl` `d` ON `b`.`material_id` = `d`.`material_id` JOIN `material_group_tbl` `e` ON `d`.`material_group_id` = `e`.`material_group_id` JOIN `outlet_tbl` `f` ON `a`.`outlet_id` = `f`.`outlet_id` JOIN `outlet_brand_tbl` `g` ON `f`.`outlet_id` = `g`.`outlet_id` AND `g`.`outlet_brand_status` = 1 JOIN `brand_tbl` `h` ON `g`.`brand_id` = `h`.`brand_id` AND `h`.`brand_name` = 'CHOOKS-TO-GO' AND c.sales_det_date = '" . $year . "-10-01' WHERE d.material_id = m.material_id AND f.bc_id = " . $bc_id . ") as oct_asp,
    
    	(SELECT AVG(c.sales_det_asp) FROM `sales_tbl` `a` JOIN `sales_item_tbl` `b` ON `a`.`sales_id` = `b`.`sales_id` AND `a`.`sales_status` = 1 AND `b`.`sales_item_status` = 1 AND `a`.`sales_year` = 2020 JOIN `sales_details_tbl` `c` ON `b`.`sales_item_id` = `c`.`sales_item_id` AND `c`.`sales_det_status` = 1 JOIN `material_tbl` `d` ON `b`.`material_id` = `d`.`material_id` JOIN `material_group_tbl` `e` ON `d`.`material_group_id` = `e`.`material_group_id` JOIN `outlet_tbl` `f` ON `a`.`outlet_id` = `f`.`outlet_id` JOIN `outlet_brand_tbl` `g` ON `f`.`outlet_id` = `g`.`outlet_id` AND `g`.`outlet_brand_status` = 1 JOIN `brand_tbl` `h` ON `g`.`brand_id` = `h`.`brand_id` AND `h`.`brand_name` = 'CHOOKS-TO-GO' AND c.sales_det_date = '" . $year . "-11-01' WHERE d.material_id = m.material_id AND f.bc_id = " . $bc_id . ") as nov_asp,
    
    	(SELECT AVG(c.sales_det_asp) FROM `sales_tbl` `a` JOIN `sales_item_tbl` `b` ON `a`.`sales_id` = `b`.`sales_id` AND `a`.`sales_status` = 1 AND `b`.`sales_item_status` = 1 AND `a`.`sales_year` = 2020 JOIN `sales_details_tbl` `c` ON `b`.`sales_item_id` = `c`.`sales_item_id` AND `c`.`sales_det_status` = 1 JOIN `material_tbl` `d` ON `b`.`material_id` = `d`.`material_id` JOIN `material_group_tbl` `e` ON `d`.`material_group_id` = `e`.`material_group_id` JOIN `outlet_tbl` `f` ON `a`.`outlet_id` = `f`.`outlet_id` JOIN `outlet_brand_tbl` `g` ON `f`.`outlet_id` = `g`.`outlet_id` AND `g`.`outlet_brand_status` = 1 JOIN `brand_tbl` `h` ON `g`.`brand_id` = `h`.`brand_id` AND `h`.`brand_name` = 'CHOOKS-TO-GO' AND c.sales_det_date = '" . $year . "-12-01' WHERE d.material_id =  m.material_id AND f.bc_id = " . $bc_id . ") as dec_asp
    
		FROM material_size_tbl m, brand_tbl n WHERE m.brand_id = n.brand_id AND m.material_size_status = 1 AND n.brand_name = 'CHOOKS-TO-GO') as asp_tbl

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


		/*Uling Roasters*/

		/*UR Chicken*/

		$get_ur_orc = $this->admin->get_query("SELECT p.size_name, AVG(asp_tbl.jan_asp) as orc_jan, AVG(asp_tbl.feb_asp) as orc_feb, AVG(asp_tbl.mar_asp) as orc_mar, AVG(asp_tbl.apr_asp) as orc_apr, AVG(asp_tbl.may_asp) as orc_may, AVG(asp_tbl.jun_asp) as orc_jun, AVG(asp_tbl.jul_asp) as orc_jul, AVG(asp_tbl.aug_asp) as orc_aug, AVG(asp_tbl.sep_asp) as orc_sep, AVG(asp_tbl.oct_asp) as orc_oct, AVG(asp_tbl.nov_asp) as orc_nov, AVG(asp_tbl.dec_asp) as orc_dec FROM material_tbl o, size_tbl p,

		(SELECT m.material_id, m.size_id,

		(SELECT AVG(c.sales_det_asp) FROM `sales_tbl` `a` JOIN `sales_item_tbl` `b` ON `a`.`sales_id` = `b`.`sales_id` AND `a`.`sales_status` = 1 AND `b`.`sales_item_status` = 1 AND `a`.`sales_year` = 2020 JOIN `sales_details_tbl` `c` ON `b`.`sales_item_id` = `c`.`sales_item_id` AND `c`.`sales_det_status` = 1 JOIN `material_tbl` `d` ON `b`.`material_id` = `d`.`material_id` JOIN `material_group_tbl` `e` ON `d`.`material_group_id` = `e`.`material_group_id` JOIN `outlet_tbl` `f` ON `a`.`outlet_id` = `f`.`outlet_id` JOIN `outlet_brand_tbl` `g` ON `f`.`outlet_id` = `g`.`outlet_id` AND `g`.`outlet_brand_status` = 1 JOIN `brand_tbl` `h` ON `g`.`brand_id` = `h`.`brand_id` AND `h`.`brand_name` = 'ULING ROASTER' AND c.sales_det_date = '" . $year . "-01-01' WHERE d.material_id = m.material_id AND f.bc_id = " . $bc_id . ") as jan_asp,
    
    	(SELECT AVG(c.sales_det_asp) FROM `sales_tbl` `a` JOIN `sales_item_tbl` `b` ON `a`.`sales_id` = `b`.`sales_id` AND `a`.`sales_status` = 1 AND `b`.`sales_item_status` = 1 AND `a`.`sales_year` = 2020 JOIN `sales_details_tbl` `c` ON `b`.`sales_item_id` = `c`.`sales_item_id` AND `c`.`sales_det_status` = 1 JOIN `material_tbl` `d` ON `b`.`material_id` = `d`.`material_id` JOIN `material_group_tbl` `e` ON `d`.`material_group_id` = `e`.`material_group_id` JOIN `outlet_tbl` `f` ON `a`.`outlet_id` = `f`.`outlet_id` JOIN `outlet_brand_tbl` `g` ON `f`.`outlet_id` = `g`.`outlet_id` AND `g`.`outlet_brand_status` = 1 JOIN `brand_tbl` `h` ON `g`.`brand_id` = `h`.`brand_id` AND `h`.`brand_name` = 'ULING ROASTER' AND c.sales_det_date = '" . $year . "-02-01' WHERE d.material_id = m.material_id AND f.bc_id = " . $bc_id . ") as feb_asp,
    
    	(SELECT AVG(c.sales_det_asp) FROM `sales_tbl` `a` JOIN `sales_item_tbl` `b` ON `a`.`sales_id` = `b`.`sales_id` AND `a`.`sales_status` = 1 AND `b`.`sales_item_status` = 1 AND `a`.`sales_year` = 2020 JOIN `sales_details_tbl` `c` ON `b`.`sales_item_id` = `c`.`sales_item_id` AND `c`.`sales_det_status` = 1 JOIN `material_tbl` `d` ON `b`.`material_id` = `d`.`material_id` JOIN `material_group_tbl` `e` ON `d`.`material_group_id` = `e`.`material_group_id` JOIN `outlet_tbl` `f` ON `a`.`outlet_id` = `f`.`outlet_id` JOIN `outlet_brand_tbl` `g` ON `f`.`outlet_id` = `g`.`outlet_id` AND `g`.`outlet_brand_status` = 1 JOIN `brand_tbl` `h` ON `g`.`brand_id` = `h`.`brand_id` AND `h`.`brand_name` = 'ULING ROASTER' AND c.sales_det_date = '" . $year . "-03-01' WHERE d.material_id = m.material_id AND f.bc_id = " . $bc_id . ") as mar_asp,
    
   		(SELECT AVG(c.sales_det_asp) FROM `sales_tbl` `a` JOIN `sales_item_tbl` `b` ON `a`.`sales_id` = `b`.`sales_id` AND `a`.`sales_status` = 1 AND `b`.`sales_item_status` = 1 AND `a`.`sales_year` = 2020 JOIN `sales_details_tbl` `c` ON `b`.`sales_item_id` = `c`.`sales_item_id` AND `c`.`sales_det_status` = 1 JOIN `material_tbl` `d` ON `b`.`material_id` = `d`.`material_id` JOIN `material_group_tbl` `e` ON `d`.`material_group_id` = `e`.`material_group_id` JOIN `outlet_tbl` `f` ON `a`.`outlet_id` = `f`.`outlet_id` JOIN `outlet_brand_tbl` `g` ON `f`.`outlet_id` = `g`.`outlet_id` AND `g`.`outlet_brand_status` = 1 JOIN `brand_tbl` `h` ON `g`.`brand_id` = `h`.`brand_id` AND `h`.`brand_name` = 'ULING ROASTER' AND c.sales_det_date = '" . $year . "-04-01' WHERE d.material_id = m.material_id AND f.bc_id = " . $bc_id . ") as apr_asp,
    
    	(SELECT AVG(c.sales_det_asp) FROM `sales_tbl` `a` JOIN `sales_item_tbl` `b` ON `a`.`sales_id` = `b`.`sales_id` AND `a`.`sales_status` = 1 AND `b`.`sales_item_status` = 1 AND `a`.`sales_year` = 2020 JOIN `sales_details_tbl` `c` ON `b`.`sales_item_id` = `c`.`sales_item_id` AND `c`.`sales_det_status` = 1 JOIN `material_tbl` `d` ON `b`.`material_id` = `d`.`material_id` JOIN `material_group_tbl` `e` ON `d`.`material_group_id` = `e`.`material_group_id` JOIN `outlet_tbl` `f` ON `a`.`outlet_id` = `f`.`outlet_id` JOIN `outlet_brand_tbl` `g` ON `f`.`outlet_id` = `g`.`outlet_id` AND `g`.`outlet_brand_status` = 1 JOIN `brand_tbl` `h` ON `g`.`brand_id` = `h`.`brand_id` AND `h`.`brand_name` = 'ULING ROASTER' AND c.sales_det_date = '" . $year . "-05-01' WHERE d.material_id = m.material_id AND f.bc_id = " . $bc_id . ") as may_asp,
    
    	(SELECT AVG(c.sales_det_asp) FROM `sales_tbl` `a` JOIN `sales_item_tbl` `b` ON `a`.`sales_id` = `b`.`sales_id` AND `a`.`sales_status` = 1 AND `b`.`sales_item_status` = 1 AND `a`.`sales_year` = 2020 JOIN `sales_details_tbl` `c` ON `b`.`sales_item_id` = `c`.`sales_item_id` AND `c`.`sales_det_status` = 1 JOIN `material_tbl` `d` ON `b`.`material_id` = `d`.`material_id` JOIN `material_group_tbl` `e` ON `d`.`material_group_id` = `e`.`material_group_id` JOIN `outlet_tbl` `f` ON `a`.`outlet_id` = `f`.`outlet_id` JOIN `outlet_brand_tbl` `g` ON `f`.`outlet_id` = `g`.`outlet_id` AND `g`.`outlet_brand_status` = 1 JOIN `brand_tbl` `h` ON `g`.`brand_id` = `h`.`brand_id` AND `h`.`brand_name` = 'ULING ROASTER' AND c.sales_det_date = '" . $year . "-06-01' WHERE d.material_id = m.material_id AND f.bc_id = " . $bc_id . ") as jun_asp,
    
    	(SELECT AVG(c.sales_det_asp) FROM `sales_tbl` `a` JOIN `sales_item_tbl` `b` ON `a`.`sales_id` = `b`.`sales_id` AND `a`.`sales_status` = 1 AND `b`.`sales_item_status` = 1 AND `a`.`sales_year` = 2020 JOIN `sales_details_tbl` `c` ON `b`.`sales_item_id` = `c`.`sales_item_id` AND `c`.`sales_det_status` = 1 JOIN `material_tbl` `d` ON `b`.`material_id` = `d`.`material_id` JOIN `material_group_tbl` `e` ON `d`.`material_group_id` = `e`.`material_group_id` JOIN `outlet_tbl` `f` ON `a`.`outlet_id` = `f`.`outlet_id` JOIN `outlet_brand_tbl` `g` ON `f`.`outlet_id` = `g`.`outlet_id` AND `g`.`outlet_brand_status` = 1 JOIN `brand_tbl` `h` ON `g`.`brand_id` = `h`.`brand_id` AND `h`.`brand_name` = 'ULING ROASTER' AND c.sales_det_date = '" . $year . "-07-01' WHERE d.material_id = m.material_id AND f.bc_id = " . $bc_id . ") as jul_asp,
    
    	(SELECT AVG(c.sales_det_asp) FROM `sales_tbl` `a` JOIN `sales_item_tbl` `b` ON `a`.`sales_id` = `b`.`sales_id` AND `a`.`sales_status` = 1 AND `b`.`sales_item_status` = 1 AND `a`.`sales_year` = 2020 JOIN `sales_details_tbl` `c` ON `b`.`sales_item_id` = `c`.`sales_item_id` AND `c`.`sales_det_status` = 1 JOIN `material_tbl` `d` ON `b`.`material_id` = `d`.`material_id` JOIN `material_group_tbl` `e` ON `d`.`material_group_id` = `e`.`material_group_id` JOIN `outlet_tbl` `f` ON `a`.`outlet_id` = `f`.`outlet_id` JOIN `outlet_brand_tbl` `g` ON `f`.`outlet_id` = `g`.`outlet_id` AND `g`.`outlet_brand_status` = 1 JOIN `brand_tbl` `h` ON `g`.`brand_id` = `h`.`brand_id` AND `h`.`brand_name` = 'ULING ROASTER' AND c.sales_det_date = '" . $year . "-08-01' WHERE d.material_id = m.material_id AND f.bc_id = " . $bc_id . ") as aug_asp,
    
    	(SELECT AVG(c.sales_det_asp) FROM `sales_tbl` `a` JOIN `sales_item_tbl` `b` ON `a`.`sales_id` = `b`.`sales_id` AND `a`.`sales_status` = 1 AND `b`.`sales_item_status` = 1 AND `a`.`sales_year` = 2020 JOIN `sales_details_tbl` `c` ON `b`.`sales_item_id` = `c`.`sales_item_id` AND `c`.`sales_det_status` = 1 JOIN `material_tbl` `d` ON `b`.`material_id` = `d`.`material_id` JOIN `material_group_tbl` `e` ON `d`.`material_group_id` = `e`.`material_group_id` JOIN `outlet_tbl` `f` ON `a`.`outlet_id` = `f`.`outlet_id` JOIN `outlet_brand_tbl` `g` ON `f`.`outlet_id` = `g`.`outlet_id` AND `g`.`outlet_brand_status` = 1 JOIN `brand_tbl` `h` ON `g`.`brand_id` = `h`.`brand_id` AND `h`.`brand_name` = 'ULING ROASTER' AND c.sales_det_date = '" . $year . "-09-01' WHERE d.material_id = m.material_id AND f.bc_id = " . $bc_id . ") as sep_asp,
    
    	(SELECT AVG(c.sales_det_asp) FROM `sales_tbl` `a` JOIN `sales_item_tbl` `b` ON `a`.`sales_id` = `b`.`sales_id` AND `a`.`sales_status` = 1 AND `b`.`sales_item_status` = 1 AND `a`.`sales_year` = 2020 JOIN `sales_details_tbl` `c` ON `b`.`sales_item_id` = `c`.`sales_item_id` AND `c`.`sales_det_status` = 1 JOIN `material_tbl` `d` ON `b`.`material_id` = `d`.`material_id` JOIN `material_group_tbl` `e` ON `d`.`material_group_id` = `e`.`material_group_id` JOIN `outlet_tbl` `f` ON `a`.`outlet_id` = `f`.`outlet_id` JOIN `outlet_brand_tbl` `g` ON `f`.`outlet_id` = `g`.`outlet_id` AND `g`.`outlet_brand_status` = 1 JOIN `brand_tbl` `h` ON `g`.`brand_id` = `h`.`brand_id` AND `h`.`brand_name` = 'ULING ROASTER' AND c.sales_det_date = '" . $year . "-10-01' WHERE d.material_id = m.material_id AND f.bc_id = " . $bc_id . ") as oct_asp,
    
    	(SELECT AVG(c.sales_det_asp) FROM `sales_tbl` `a` JOIN `sales_item_tbl` `b` ON `a`.`sales_id` = `b`.`sales_id` AND `a`.`sales_status` = 1 AND `b`.`sales_item_status` = 1 AND `a`.`sales_year` = 2020 JOIN `sales_details_tbl` `c` ON `b`.`sales_item_id` = `c`.`sales_item_id` AND `c`.`sales_det_status` = 1 JOIN `material_tbl` `d` ON `b`.`material_id` = `d`.`material_id` JOIN `material_group_tbl` `e` ON `d`.`material_group_id` = `e`.`material_group_id` JOIN `outlet_tbl` `f` ON `a`.`outlet_id` = `f`.`outlet_id` JOIN `outlet_brand_tbl` `g` ON `f`.`outlet_id` = `g`.`outlet_id` AND `g`.`outlet_brand_status` = 1 JOIN `brand_tbl` `h` ON `g`.`brand_id` = `h`.`brand_id` AND `h`.`brand_name` = 'ULING ROASTER' AND c.sales_det_date = '" . $year . "-11-01' WHERE d.material_id = m.material_id AND f.bc_id = " . $bc_id . ") as nov_asp,
    
    	(SELECT AVG(c.sales_det_asp) FROM `sales_tbl` `a` JOIN `sales_item_tbl` `b` ON `a`.`sales_id` = `b`.`sales_id` AND `a`.`sales_status` = 1 AND `b`.`sales_item_status` = 1 AND `a`.`sales_year` = 2020 JOIN `sales_details_tbl` `c` ON `b`.`sales_item_id` = `c`.`sales_item_id` AND `c`.`sales_det_status` = 1 JOIN `material_tbl` `d` ON `b`.`material_id` = `d`.`material_id` JOIN `material_group_tbl` `e` ON `d`.`material_group_id` = `e`.`material_group_id` JOIN `outlet_tbl` `f` ON `a`.`outlet_id` = `f`.`outlet_id` JOIN `outlet_brand_tbl` `g` ON `f`.`outlet_id` = `g`.`outlet_id` AND `g`.`outlet_brand_status` = 1 JOIN `brand_tbl` `h` ON `g`.`brand_id` = `h`.`brand_id` AND `h`.`brand_name` = 'ULING ROASTER' AND c.sales_det_date = '" . $year . "-01-01' WHERE d.material_id =  m.material_id AND f.bc_id = " . $bc_id . ") as dec_asp
    
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
			}
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

		/*11 PCS*/
		$join_ur_11pcs= array(
			'sales_item_tbl b' => 'a.sales_id = b.sales_id AND a.sales_status = 1 AND b.sales_item_status = 1 AND a.sales_year = ' . $year,
			'sales_details_tbl c' => 'b.sales_item_id = c.sales_item_id AND c.sales_det_status = 1',
			'material_tbl d' => 'b.material_id = d.material_id AND d.material_code = 10400170',
			'material_group_tbl e' => 'd.material_group_id = e.material_group_id AND e.material_group_name = \'OTHER SPECIALTY PRODUCTS\'',
			'outlet_tbl f' => 'a.outlet_id = f.outlet_id AND f.bc_id = ' . $bc_id,
			'outlet_brand_tbl g' => 'f.outlet_id = g.outlet_id AND g.outlet_brand_status = 1',
			'brand_tbl h' => 'g.brand_id  = h.brand_id AND h.brand_name = \'ULING ROASTERS\''
		);

		$get_ur_11pcs = $this->admin->get_join('sales_tbl a', $join_ur_11pcs, FALSE, FALSE, 'YEAR(c.sales_det_date), MONTH(c.sales_det_date)', 'AVG(c.sales_det_asp) as asp, MONTH(c.sales_det_date) asp_date');

		$data['ur_11pcs_1'] = 0;
		$data['ur_11pcs_2'] = 0;
		$data['ur_11pcs_3'] = 0;
		$data['ur_11pcs_4'] = 0;
		$data['ur_11pcs_5'] = 0;
		$data['ur_11pcs_6'] = 0;
		$data['ur_11pcs_7'] = 0;
		$data['ur_11pcs_8'] = 0;
		$data['ur_11pcs_9'] = 0;
		$data['ur_11pcs_10'] = 0;
		$data['ur_11pcs_11'] = 0;
		$data['ur_11pcs_12'] = 0;
		$data['ur_11pcs_total'] = 0;
		$data['ur_11pcs_count'] = 0;
		$data['ur_11pcs_avg'] = 0;
		$data['ur_11pcs_min'] = 0;
		$data['ur_11pcs_max'] = 0;

		foreach($get_ur_11pcs as $row){
			$asp = $row->asp;
			$month = $row->asp_date;
			$data['ur_11pcs_' . $month] = $asp;
			$data['ur_11pcs_total'] += $asp;

			if($asp < $data['ur_11pcs_min'] || $data['ur_11pcs_count'] == 0){
				$data['ur_11pcs_min'] = $asp;
			}

			if($asp > $data['ur_11pcs_max'] || $data['ur_11pcs_count'] == 0){
				$data['ur_11pcs_max'] = $asp;
			}

			$data['ur_11pcs_count']++;
		}

		$data['ur_11pcs_avg'] = $data['ur_11pcs_total'] != 0 ? $data['ur_11pcs_total'] / $data['ur_11pcs_count'] : 0;

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


		/*REYAL*/

		/*REYAL Chicken*/

		$get_improved_sweet_and_spicy_orc = $this->admin->get_query("SELECT p.size_name, AVG(asp_tbl.jan_asp) as orc_jan, AVG(asp_tbl.feb_asp) as orc_feb, AVG(asp_tbl.mar_asp) as orc_mar, AVG(asp_tbl.apr_asp) as orc_apr, AVG(asp_tbl.may_asp) as orc_may, AVG(asp_tbl.jun_asp) as orc_jun, AVG(asp_tbl.jul_asp) as orc_jul, AVG(asp_tbl.aug_asp) as orc_aug, AVG(asp_tbl.sep_asp) as orc_sep, AVG(asp_tbl.oct_asp) as orc_oct, AVG(asp_tbl.nov_asp) as orc_nov, AVG(asp_tbl.dec_asp) as orc_dec FROM material_tbl o, size_tbl p,

		(SELECT m.material_id, m.size_id,

		(SELECT AVG(c.sales_det_asp) FROM `sales_tbl` `a` JOIN `sales_item_tbl` `b` ON `a`.`sales_id` = `b`.`sales_id` AND `a`.`sales_status` = 1 AND `b`.`sales_item_status` = 1 AND `a`.`sales_year` = 2020 JOIN `sales_details_tbl` `c` ON `b`.`sales_item_id` = `c`.`sales_item_id` AND `c`.`sales_det_status` = 1 JOIN `material_tbl` `d` ON `b`.`material_id` = `d`.`material_id` JOIN `material_group_tbl` `e` ON `d`.`material_group_id` = `e`.`material_group_id` JOIN `outlet_tbl` `f` ON `a`.`outlet_id` = `f`.`outlet_id` JOIN `outlet_brand_tbl` `g` ON `f`.`outlet_id` = `g`.`outlet_id` AND `g`.`outlet_brand_status` = 1 JOIN `brand_tbl` `h` ON `g`.`brand_id` = `h`.`brand_id` AND `h`.`brand_name` = 'REYAL' AND c.sales_det_date = '" . $year . "-01-01' WHERE d.material_id = m.material_id AND f.bc_id = " . $bc_id . ") as jan_asp,
    
    	(SELECT AVG(c.sales_det_asp) FROM `sales_tbl` `a` JOIN `sales_item_tbl` `b` ON `a`.`sales_id` = `b`.`sales_id` AND `a`.`sales_status` = 1 AND `b`.`sales_item_status` = 1 AND `a`.`sales_year` = 2020 JOIN `sales_details_tbl` `c` ON `b`.`sales_item_id` = `c`.`sales_item_id` AND `c`.`sales_det_status` = 1 JOIN `material_tbl` `d` ON `b`.`material_id` = `d`.`material_id` JOIN `material_group_tbl` `e` ON `d`.`material_group_id` = `e`.`material_group_id` JOIN `outlet_tbl` `f` ON `a`.`outlet_id` = `f`.`outlet_id` JOIN `outlet_brand_tbl` `g` ON `f`.`outlet_id` = `g`.`outlet_id` AND `g`.`outlet_brand_status` = 1 JOIN `brand_tbl` `h` ON `g`.`brand_id` = `h`.`brand_id` AND `h`.`brand_name` = 'REYAL' AND c.sales_det_date = '" . $year . "-02-01' WHERE d.material_id = m.material_id AND f.bc_id = " . $bc_id . ") as feb_asp,
    
    	(SELECT AVG(c.sales_det_asp) FROM `sales_tbl` `a` JOIN `sales_item_tbl` `b` ON `a`.`sales_id` = `b`.`sales_id` AND `a`.`sales_status` = 1 AND `b`.`sales_item_status` = 1 AND `a`.`sales_year` = 2020 JOIN `sales_details_tbl` `c` ON `b`.`sales_item_id` = `c`.`sales_item_id` AND `c`.`sales_det_status` = 1 JOIN `material_tbl` `d` ON `b`.`material_id` = `d`.`material_id` JOIN `material_group_tbl` `e` ON `d`.`material_group_id` = `e`.`material_group_id` JOIN `outlet_tbl` `f` ON `a`.`outlet_id` = `f`.`outlet_id` JOIN `outlet_brand_tbl` `g` ON `f`.`outlet_id` = `g`.`outlet_id` AND `g`.`outlet_brand_status` = 1 JOIN `brand_tbl` `h` ON `g`.`brand_id` = `h`.`brand_id` AND `h`.`brand_name` = 'REYAL' AND c.sales_det_date = '" . $year . "-03-01' WHERE d.material_id = m.material_id AND f.bc_id = " . $bc_id . ") as mar_asp,
    
   		(SELECT AVG(c.sales_det_asp) FROM `sales_tbl` `a` JOIN `sales_item_tbl` `b` ON `a`.`sales_id` = `b`.`sales_id` AND `a`.`sales_status` = 1 AND `b`.`sales_item_status` = 1 AND `a`.`sales_year` = 2020 JOIN `sales_details_tbl` `c` ON `b`.`sales_item_id` = `c`.`sales_item_id` AND `c`.`sales_det_status` = 1 JOIN `material_tbl` `d` ON `b`.`material_id` = `d`.`material_id` JOIN `material_group_tbl` `e` ON `d`.`material_group_id` = `e`.`material_group_id` JOIN `outlet_tbl` `f` ON `a`.`outlet_id` = `f`.`outlet_id` JOIN `outlet_brand_tbl` `g` ON `f`.`outlet_id` = `g`.`outlet_id` AND `g`.`outlet_brand_status` = 1 JOIN `brand_tbl` `h` ON `g`.`brand_id` = `h`.`brand_id` AND `h`.`brand_name` = 'REYAL' AND c.sales_det_date = '" . $year . "-04-01' WHERE d.material_id = m.material_id AND f.bc_id = " . $bc_id . ") as apr_asp,
    
    	(SELECT AVG(c.sales_det_asp) FROM `sales_tbl` `a` JOIN `sales_item_tbl` `b` ON `a`.`sales_id` = `b`.`sales_id` AND `a`.`sales_status` = 1 AND `b`.`sales_item_status` = 1 AND `a`.`sales_year` = 2020 JOIN `sales_details_tbl` `c` ON `b`.`sales_item_id` = `c`.`sales_item_id` AND `c`.`sales_det_status` = 1 JOIN `material_tbl` `d` ON `b`.`material_id` = `d`.`material_id` JOIN `material_group_tbl` `e` ON `d`.`material_group_id` = `e`.`material_group_id` JOIN `outlet_tbl` `f` ON `a`.`outlet_id` = `f`.`outlet_id` JOIN `outlet_brand_tbl` `g` ON `f`.`outlet_id` = `g`.`outlet_id` AND `g`.`outlet_brand_status` = 1 JOIN `brand_tbl` `h` ON `g`.`brand_id` = `h`.`brand_id` AND `h`.`brand_name` = 'REYAL' AND c.sales_det_date = '" . $year . "-05-01' WHERE d.material_id = m.material_id AND f.bc_id = " . $bc_id . ") as may_asp,
    
    	(SELECT AVG(c.sales_det_asp) FROM `sales_tbl` `a` JOIN `sales_item_tbl` `b` ON `a`.`sales_id` = `b`.`sales_id` AND `a`.`sales_status` = 1 AND `b`.`sales_item_status` = 1 AND `a`.`sales_year` = 2020 JOIN `sales_details_tbl` `c` ON `b`.`sales_item_id` = `c`.`sales_item_id` AND `c`.`sales_det_status` = 1 JOIN `material_tbl` `d` ON `b`.`material_id` = `d`.`material_id` JOIN `material_group_tbl` `e` ON `d`.`material_group_id` = `e`.`material_group_id` JOIN `outlet_tbl` `f` ON `a`.`outlet_id` = `f`.`outlet_id` JOIN `outlet_brand_tbl` `g` ON `f`.`outlet_id` = `g`.`outlet_id` AND `g`.`outlet_brand_status` = 1 JOIN `brand_tbl` `h` ON `g`.`brand_id` = `h`.`brand_id` AND `h`.`brand_name` = 'REYAL' AND c.sales_det_date = '" . $year . "-06-01' WHERE d.material_id = m.material_id AND f.bc_id = " . $bc_id . ") as jun_asp,
    
    	(SELECT AVG(c.sales_det_asp) FROM `sales_tbl` `a` JOIN `sales_item_tbl` `b` ON `a`.`sales_id` = `b`.`sales_id` AND `a`.`sales_status` = 1 AND `b`.`sales_item_status` = 1 AND `a`.`sales_year` = 2020 JOIN `sales_details_tbl` `c` ON `b`.`sales_item_id` = `c`.`sales_item_id` AND `c`.`sales_det_status` = 1 JOIN `material_tbl` `d` ON `b`.`material_id` = `d`.`material_id` JOIN `material_group_tbl` `e` ON `d`.`material_group_id` = `e`.`material_group_id` JOIN `outlet_tbl` `f` ON `a`.`outlet_id` = `f`.`outlet_id` JOIN `outlet_brand_tbl` `g` ON `f`.`outlet_id` = `g`.`outlet_id` AND `g`.`outlet_brand_status` = 1 JOIN `brand_tbl` `h` ON `g`.`brand_id` = `h`.`brand_id` AND `h`.`brand_name` = 'REYAL' AND c.sales_det_date = '" . $year . "-07-01' WHERE d.material_id = m.material_id AND f.bc_id = " . $bc_id . ") as jul_asp,
    
    	(SELECT AVG(c.sales_det_asp) FROM `sales_tbl` `a` JOIN `sales_item_tbl` `b` ON `a`.`sales_id` = `b`.`sales_id` AND `a`.`sales_status` = 1 AND `b`.`sales_item_status` = 1 AND `a`.`sales_year` = 2020 JOIN `sales_details_tbl` `c` ON `b`.`sales_item_id` = `c`.`sales_item_id` AND `c`.`sales_det_status` = 1 JOIN `material_tbl` `d` ON `b`.`material_id` = `d`.`material_id` JOIN `material_group_tbl` `e` ON `d`.`material_group_id` = `e`.`material_group_id` JOIN `outlet_tbl` `f` ON `a`.`outlet_id` = `f`.`outlet_id` JOIN `outlet_brand_tbl` `g` ON `f`.`outlet_id` = `g`.`outlet_id` AND `g`.`outlet_brand_status` = 1 JOIN `brand_tbl` `h` ON `g`.`brand_id` = `h`.`brand_id` AND `h`.`brand_name` = 'REYAL' AND c.sales_det_date = '" . $year . "-08-01' WHERE d.material_id = m.material_id AND f.bc_id = " . $bc_id . ") as aug_asp,
    
    	(SELECT AVG(c.sales_det_asp) FROM `sales_tbl` `a` JOIN `sales_item_tbl` `b` ON `a`.`sales_id` = `b`.`sales_id` AND `a`.`sales_status` = 1 AND `b`.`sales_item_status` = 1 AND `a`.`sales_year` = 2020 JOIN `sales_details_tbl` `c` ON `b`.`sales_item_id` = `c`.`sales_item_id` AND `c`.`sales_det_status` = 1 JOIN `material_tbl` `d` ON `b`.`material_id` = `d`.`material_id` JOIN `material_group_tbl` `e` ON `d`.`material_group_id` = `e`.`material_group_id` JOIN `outlet_tbl` `f` ON `a`.`outlet_id` = `f`.`outlet_id` JOIN `outlet_brand_tbl` `g` ON `f`.`outlet_id` = `g`.`outlet_id` AND `g`.`outlet_brand_status` = 1 JOIN `brand_tbl` `h` ON `g`.`brand_id` = `h`.`brand_id` AND `h`.`brand_name` = 'REYAL' AND c.sales_det_date = '" . $year . "-09-01' WHERE d.material_id = m.material_id AND f.bc_id = " . $bc_id . ") as sep_asp,
    
    	(SELECT AVG(c.sales_det_asp) FROM `sales_tbl` `a` JOIN `sales_item_tbl` `b` ON `a`.`sales_id` = `b`.`sales_id` AND `a`.`sales_status` = 1 AND `b`.`sales_item_status` = 1 AND `a`.`sales_year` = 2020 JOIN `sales_details_tbl` `c` ON `b`.`sales_item_id` = `c`.`sales_item_id` AND `c`.`sales_det_status` = 1 JOIN `material_tbl` `d` ON `b`.`material_id` = `d`.`material_id` JOIN `material_group_tbl` `e` ON `d`.`material_group_id` = `e`.`material_group_id` JOIN `outlet_tbl` `f` ON `a`.`outlet_id` = `f`.`outlet_id` JOIN `outlet_brand_tbl` `g` ON `f`.`outlet_id` = `g`.`outlet_id` AND `g`.`outlet_brand_status` = 1 JOIN `brand_tbl` `h` ON `g`.`brand_id` = `h`.`brand_id` AND `h`.`brand_name` = 'REYAL' AND c.sales_det_date = '" . $year . "-10-01' WHERE d.material_id = m.material_id AND f.bc_id = " . $bc_id . ") as oct_asp,
    
    	(SELECT AVG(c.sales_det_asp) FROM `sales_tbl` `a` JOIN `sales_item_tbl` `b` ON `a`.`sales_id` = `b`.`sales_id` AND `a`.`sales_status` = 1 AND `b`.`sales_item_status` = 1 AND `a`.`sales_year` = 2020 JOIN `sales_details_tbl` `c` ON `b`.`sales_item_id` = `c`.`sales_item_id` AND `c`.`sales_det_status` = 1 JOIN `material_tbl` `d` ON `b`.`material_id` = `d`.`material_id` JOIN `material_group_tbl` `e` ON `d`.`material_group_id` = `e`.`material_group_id` JOIN `outlet_tbl` `f` ON `a`.`outlet_id` = `f`.`outlet_id` JOIN `outlet_brand_tbl` `g` ON `f`.`outlet_id` = `g`.`outlet_id` AND `g`.`outlet_brand_status` = 1 JOIN `brand_tbl` `h` ON `g`.`brand_id` = `h`.`brand_id` AND `h`.`brand_name` = 'REYAL' AND c.sales_det_date = '" . $year . "-11-01' WHERE d.material_id = m.material_id AND f.bc_id = " . $bc_id . ") as nov_asp,
    
    	(SELECT AVG(c.sales_det_asp) FROM `sales_tbl` `a` JOIN `sales_item_tbl` `b` ON `a`.`sales_id` = `b`.`sales_id` AND `a`.`sales_status` = 1 AND `b`.`sales_item_status` = 1 AND `a`.`sales_year` = 2020 JOIN `sales_details_tbl` `c` ON `b`.`sales_item_id` = `c`.`sales_item_id` AND `c`.`sales_det_status` = 1 JOIN `material_tbl` `d` ON `b`.`material_id` = `d`.`material_id` JOIN `material_group_tbl` `e` ON `d`.`material_group_id` = `e`.`material_group_id` JOIN `outlet_tbl` `f` ON `a`.`outlet_id` = `f`.`outlet_id` JOIN `outlet_brand_tbl` `g` ON `f`.`outlet_id` = `g`.`outlet_id` AND `g`.`outlet_brand_status` = 1 JOIN `brand_tbl` `h` ON `g`.`brand_id` = `h`.`brand_id` AND `h`.`brand_name` = 'REYAL' AND c.sales_det_date = '" . $year . "-12-01' WHERE d.material_id =  m.material_id AND f.bc_id = " . $bc_id . ") as dec_asp
    
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
			$month = $row->ur_date;
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

			if($asp > $data['smkt_live_max'] || $data['smkt_liver_count'] == 0){
				$data['smkt_liver_max'] = $asp;
			}

			$data['smkt_liver_count']++;
		}

		$data['smkt_liver_avg'] = $data['smkt_liver_total'] != 0 ? $data['smkt_liver_total'] / $data['smkt_liver_count'] : 0;

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

		return $data;
	}