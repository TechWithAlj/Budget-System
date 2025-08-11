public function check_price(){
		
		/*$sp = "CALL get_noi_basic(?,?,@param1,@param2,@param3,@param4,@param5 )";
		$params = array(
			'PARAM_1' => 1,
			'PARAM_2' => "2020-01-01",
		);

		$data = $this->db->query($sp, $params);
		$get_data = $this->db->query('SELECT @param1');
		print_r($get_data->result());*/
		$brand_id = 1;
		$material_sales_id = 85;
		$bc = 3;
		$check_sku = $this->admin->get_query("SELECT e.material_id as sku_material_id, a.prod_trans_id, a.process_type_id, c.component_type, d.material_desc, d.material_id, e.material_desc as mat_details
			FROM prod_trans_tbl a, prod_trans_dtl_tbl b, component_type_tbl c, material_tbl d, material_tbl e
			WHERE a.prod_trans_id = b.prod_trans_id AND b.component_type_id = c.component_type_id
			AND a.prod_id= " . $material_sales_id . " AND a.prod_id = d.material_id AND b.article_id = e.material_id AND b.article_type_id = 1 AND a.prod_trans_status = 3
			AND a.bc_id = " . $bc . " AND b.prod_trans_dtl_date = '2020-01-01' AND a.brand_id = " . $brand_id . " AND a.process_type_id = 5 AND c.component_type = 'COST OF SALES'", TRUE);
					echo $r = $check_sku->sku_material_id;


		$data = $this->db->query("
			SELECT * FROM (SELECT @r as _id,
				@mat := IFNULL(
					(SELECT d.material_desc
			    	FROM prod_trans_tbl a, prod_trans_dtl_tbl b, component_type_tbl c, material_tbl d
			   		WHERE a.prod_trans_id = b.prod_trans_id AND b.component_type_id = c.component_type_id
			        AND a.prod_id=_id AND a.prod_id = d.material_id AND (b.component_type_id = 21
					OR b.component_type_id = 2) AND a.prod_trans_status = 3
			        AND a.bc_id = @bc AND b.prod_trans_dtl_date = @prod_date AND a.process_type_id != 5
			        )
				, '') as mat_desc,

				@unit := IFNULL(
					(SELECT f.unit_name
			    	FROM prod_trans_tbl a, prod_trans_dtl_tbl b, component_type_tbl c, material_tbl d, material_unit_tbl e, unit_tbl f
			   		WHERE a.prod_trans_id = b.prod_trans_id AND b.component_type_id = c.component_type_id
			        AND a.prod_id=_id AND a.prod_id = d.material_id AND (b.component_type_id = 21
					OR b.component_type_id = 2) AND a.prod_trans_status = 3 AND d.material_id = e.material_id AND e.valuation_unit = f.unit_id AND e.material_unit_status = 1
			        AND a.bc_id = @bc AND b.prod_trans_dtl_date = @prod_date AND a.process_type_id != 5
			        )
				, '') as mat_unit,	

			    @components :=(
					SELECT c.component_type
			    	FROM prod_trans_tbl a, prod_trans_dtl_tbl b, component_type_tbl c, material_tbl d
			   		WHERE a.prod_trans_id = b.prod_trans_id AND b.component_type_id = c.component_type_id
			        AND a.prod_id=_id AND a.prod_id = d.material_id AND a.prod_trans_status = 3
			        AND (b.component_type_id = 21
					OR b.component_type_id = 2) AND a.bc_id = @bc
			        AND b.prod_trans_dtl_date = @prod_date AND a.process_type_id != 5
			        
				) as mat_comp,
			               
			    @process :=(
					SELECT b.process_type_name
			    	FROM prod_trans_tbl a, process_type_tbl b
			        WHERE a.prod_id=_id AND a.process_type_id = b.process_type_id
			        AND a.prod_trans_status = 3 AND a.bc_id = @bc AND a.process_type_id != 5
			        
				) as mat_process, 
				
				(
					SELECT @r := b.article_id
			    	FROM prod_trans_tbl a, prod_trans_dtl_tbl b, component_type_tbl c
			   		WHERE a.prod_trans_id = b.prod_trans_id
			        AND b.component_type_id = c.component_type_id
			        AND a.prod_id = _id AND (b.component_type_id = 21 OR b.component_type_id = 2)
			        AND a.prod_trans_status = 3 AND a.bc_id = @bc
			        AND b.prod_trans_dtl_date = @prod_date AND a.process_type_id != 5 LIMIT 1
				) as parent,
			    
			    @l := @l + 1 AS level
			    
			FROM
				(
			   	SELECT
			    	@r := " . $r . ",
			        @l := 1,
			        @bc := " . $bc . ",
			        @prod_date := '2020-01-01',
			        @brand := 1,
			        @cl := 0,
			        @mat := 'test',
			        @rate := 0,
			        @cost := 0,
			        @process := '',
			        @unit := ''
				) vars,
			    
			    prod_trans_tbl
			    WHERE @mat != '' HAVING @r != 225
				
				
			) as noi_data
			    
			ORDER BY level DESC

		");
		$result = $data->result();
		$fresh_dressed_cost = 0;
		$date = '2020-01-01';
		foreach($result as $row){
			
			$prod_id = $row->_id;
			$mat_unit = $row->mat_unit;
			echo $process = $row->mat_process;
			echo ' - ';
			echo $mat_desc = $row->mat_desc;
			if($process == "BASIC PROCESSING"){

				$sp = "CALL get_noi_basic(?,?,@total_qty,@total_kgs,@ave_wt,@cost_per_head,@total_cost_amount,@test)";
				$params = array(
					'PARAM_1' => $bc,
					'PARAM_2' => "2020-01-01",
				);

				$data = $this->db->query($sp, $params);
				$get_data = $this->db->query('SELECT @total_qty as total_qty, @total_kgs as total_kgs, @ave_wt as ave_wt, @cost_per_head as cost_per_head, @total_cost_amount as total_cost_amount, @test as test');
				//print_r($get_data);
				$basic =  $get_data->row();
				echo '<br />';
				echo 'Live Sales: ' . $basic->test;
				echo '<br />';
				echo  'Qty : ' . $total_qty = round($basic->total_qty,3);
				echo '<br />';
				echo 'KGS: ' . $total_kgs = round($basic->total_kgs,3);
				echo '<br />';
				echo 'AVE WT: ' . $ave_wt = round($basic->ave_wt,3);
				echo '<br />';
				echo 'Cost/Price: ' . $cost_per_head = round($basic->cost_per_head,3);
				echo '<br />';
				echo 'Amount: ' . $total_cost_amount = round($basic->total_cost_amount,3);
				echo '<br />';
			}elseif($process == "CLASSIFICATION"){
				$get_prod_det = $this->admin->get_query("SELECT a.prod_id, b.rate, b.cost, c.material_desc, b.component_type_id, d.component_type as det_component, e.component_type as trans_component FROM prod_trans_tbl a, prod_trans_dtl_tbl b, material_tbl c, component_type_tbl d, component_type_tbl e, process_type_tbl f WHERE  a.prod_id = " . $prod_id . " AND a.bc_id = " . $bc . " AND a.prod_trans_status = 3 AND a.prod_trans_id = b.prod_trans_id AND b.component_type_id = d.component_type_id AND a.component_type_id = e.component_type_id AND b.article_type_id = 1 AND b.article_id = c.material_id AND a.process_type_id = f.process_type_id AND b.prod_trans_dtl_date = '" . $date . "' AND f.process_type_name = '" . $process . "'

					UNION ALL

					SELECT a.prod_id, b.rate, b.cost, c.service_desc, b.component_type_id, d.component_type as det_component, e.component_type as trans_component FROM prod_trans_tbl a, prod_trans_dtl_tbl b, services_tbl c, component_type_tbl d, component_type_tbl e, process_type_tbl f WHERE  a.prod_id = " . $prod_id . " AND a.bc_id = " . $bc . " AND a.prod_trans_status = 3 AND a.prod_trans_id = b.prod_trans_id AND b.component_type_id = d.component_type_id AND a.component_type_id = e.component_type_id AND b.article_type_id = 2 AND b.article_id = c.service_id AND a.process_type_id = f.process_type_id AND b.prod_trans_dtl_date = '" . $date . "' AND f.process_type_name = '" . $process . "'");
				echo '<br />';
				foreach($get_prod_det as $row_det){
					$components = $row_det->det_component;
					if($components != 'RAW MATERIALS'){
						$rate = $row_det->rate;
						$cost = $row_det->cost;

						echo $total_qty = round($total_qty * ($rate / 100));
						echo '<br/>';
						echo $total_kgs = round($total_qty * ($cost),3);
						echo '<br/>';
						echo $total_cost_amount = round($total_kgs * $cost_per_head,3);
						echo '<br/>';
					}
				}

				echo '<br/ >';
				echo 'Qty : ' . $total_qty;
				echo '<br />';
				echo 'KGS: ' . $total_kgs;
				echo '<br />';
				echo 'AVE WT: ' . round($total_kgs / $total_qty,3);
				echo '<br />';
				echo 'Cost/Price: ' . round($total_cost_amount / $total_kgs,3);
				echo '<br />';
				echo 'Amount: ' . $total_cost_amount;
				echo '<br />';

			}elseif($process == "FURTHER PROCESS - NECKLESS"){
				$get_prod_det = $this->admin->get_query("SELECT a.prod_id, b.rate, b.cost, c.material_desc, b.component_type_id, d.component_type as det_component, e.component_type as trans_component FROM prod_trans_tbl a, prod_trans_dtl_tbl b, material_tbl c, component_type_tbl d, component_type_tbl e, process_type_tbl f WHERE  a.prod_id = " . $prod_id . " AND a.bc_id = " . $bc . " AND a.prod_trans_status = 3 AND a.prod_trans_id = b.prod_trans_id AND b.component_type_id = d.component_type_id AND a.component_type_id = e.component_type_id AND b.article_type_id = 1 AND b.article_id = c.material_id AND a.process_type_id = f.process_type_id AND b.prod_trans_dtl_date = '" . $date . "' AND f.process_type_name = '" . $process . "'

					UNION ALL

					SELECT a.prod_id, b.rate, b.cost, c.service_desc, b.component_type_id, d.component_type as det_component, e.component_type as trans_component FROM prod_trans_tbl a, prod_trans_dtl_tbl b, services_tbl c, component_type_tbl d, component_type_tbl e, process_type_tbl f WHERE  a.prod_id = " . $prod_id . " AND a.bc_id = " . $bc . " AND a.prod_trans_status = 3 AND a.prod_trans_id = b.prod_trans_id AND b.component_type_id = d.component_type_id AND a.component_type_id = e.component_type_id AND b.article_type_id = 2 AND b.article_id = c.service_id AND a.process_type_id = f.process_type_id AND b.prod_trans_dtl_date = '" . $date . "' AND f.process_type_name = '" . $process . "'");
				$cost_rec_kgs = 0;
				$cost_rec_amount = 0;
				foreach($get_prod_det as $row_det){
					$components = $row_det->det_component;
					$mat_desc = $row_det->material_desc;
					$rate = $row_det->rate;
					$cost = $row_det->cost;
					if($components == 'COST RECOVERY'){
						$cost_rec_kgs = round($cost_rec_kgs + ($total_kgs * ($rate / 100)), 3);
						echo '<br/>';
						echo $mat_desc . ': ' .  round(($total_kgs * ($rate / 100)) * $cost, 3);
						echo '<br/>';
						$cost_rec_amount = round($cost_rec_amount + ($total_kgs * ($rate / 100)) * $cost, 3);
						
					}elseif($components == 'CONVERSION COST'){
						$total_cost_amount = round($total_cost_amount + ($total_qty * $rate), 3);
						echo '<br />';
						echo $mat_desc . ': ' .  round(($total_qty * $rate),3);
						echo '<br />';
					}else{
						$total_cost_amount = round($total_cost_amount + ($total_qty * $rate), 3);
					}
				}

				$total_cost_amount = round($total_cost_amount  -  $cost_rec_amount, 3);
				echo '<br/ >';
				echo 'Qty : ' . round($total_qty, 3);
				echo '<br />';
				echo 'KGS: ' . $total_kgs = round($total_kgs - $cost_rec_kgs, 3);
				echo '<br />';
				echo 'AVE WT: ' . round($total_kgs / $total_qty, 3);
				echo '<br />';
				echo 'Cost/Price: ' . round($total_cost_amount / $total_kgs, 3);
				echo '<br />';
				echo 'Amount: ' . round($total_cost_amount, 3);
				echo '<br />';
			}elseif($process == "FURTHER PROCESS - MARINATION"){
				$get_prod_det = $this->admin->get_query("SELECT a.prod_id, b.rate, b.cost, c.material_desc, b.component_type_id, d.component_type as det_component, e.component_type as trans_component, g.unit_name as mat_det_unit, b.amount_type_id as amount_type FROM prod_trans_tbl a, prod_trans_dtl_tbl b, material_tbl c, component_type_tbl d, component_type_tbl e, process_type_tbl f, unit_tbl g WHERE  a.prod_id = " . $prod_id . " AND a.bc_id = " . $bc . " AND a.prod_trans_status = 3 AND a.prod_trans_id = b.prod_trans_id AND b.component_type_id = d.component_type_id AND a.component_type_id = e.component_type_id AND b.article_type_id = 1 AND b.article_id = c.material_id AND c.unit_id = g.unit_id AND a.process_type_id = f.process_type_id AND b.prod_trans_dtl_date = '" . $date . "' AND f.process_type_name = '" . $process . "'

					UNION ALL

					SELECT a.prod_id, b.rate, b.cost, c.service_desc, b.component_type_id, d.component_type as det_component, e.component_type as trans_component, 0, b.amount_type_id as amount_type FROM prod_trans_tbl a, prod_trans_dtl_tbl b, services_tbl c, component_type_tbl d, component_type_tbl e, process_type_tbl f WHERE  a.prod_id = " . $prod_id . " AND a.bc_id = " . $bc . " AND a.prod_trans_status = 3 AND a.prod_trans_id = b.prod_trans_id AND b.component_type_id = d.component_type_id AND a.component_type_id = e.component_type_id AND b.article_type_id = 2 AND b.article_id = c.service_id AND a.process_type_id = f.process_type_id AND b.prod_trans_dtl_date = '" . $date . "' AND f.process_type_name = '" . $process . "'");

					
				foreach($get_prod_det as $row_det){
					$components = $row_det->det_component;
					if($row_det->amount_type == 1){
				 		$rate = $row_det->rate / 100;
				 	}else{
				 		$rate = $row_det->rate;
				 	}

					$cost = $row_det->cost;
					$mat_details = $row_det->material_desc;
					if($components == 'MARINADES'){
						echo '<br />';
						echo 'Material/Service : ' . $mat_details;
						echo ' (' . round(($total_qty * $rate) * $cost, 3) . ')';
						echo ' | ' . $total_qty . ', ' . $rate . ', ' . $cost;
						echo '<br />';
						$total_cost_amount = round($total_cost_amount + (($total_qty * $rate) * $cost));
					}elseif($components == "PACKAGING"){
						echo '<br />';
						echo 'Material/Service : ' . $mat_details;
						if($rate > 0){
							echo ' (' . round((($total_qty / $rate) * $cost), 3) . ')';
							$total_cost_amount = round($total_cost_amount + (($total_qty / $rate) * $cost), 2);
						}
						echo '<br />';
					}elseif($components == "CONVERSION COST"){
						echo '<br />';
						echo 'Material/Service : ' . $mat_details;
						echo ' (' . round(($total_qty * $rate), 3) . ')';
						echo '<br />';
						$total_cost_amount = round($total_cost_amount + ($total_qty * $rate),3);
					}elseif($components == "RAW MATERIALS"){

						if($row_det->mat_det_unit == 'KG'){
							$multiplier = $rate > 0 ? $total_kgs / $rate : $total_kgs;
						}elseif($row_det->mat_det_unit == 'HD'){
							$multiplier = $rate > 0 ? $total_qty / $rate : $total_qty;
						}

						echo '<br />';
						echo $mat_details . ' | ' . $row_det->mat_det_unit;
						echo '<br />';
						echo $row_det->rate;
					}
				}

				echo '<br/ >';
				echo 'Qty : ' . round($total_qty, 3);
				echo '<br />';
				echo 'KGS: ' . $total_kgs = round($multiplier, 3);
				echo '<br />';
				echo 'AVE WT: ' . round($total_kgs / $total_qty, 3);
				echo '<br />';
				echo 'Cost/Price: ' . round($total_cost_amount / $multiplier, 3);
				echo '<br />';
				echo 'Amount: ' . round($total_cost_amount, 3);
				echo '<br />';
			}elseif($process == "FURTHER PROCESS - CUT UPS PACKAGING"){
				$get_prod_det = $this->admin->get_query("SELECT a.prod_id, b.rate, b.cost, c.material_desc, b.component_type_id, d.component_type as det_component, e.component_type as trans_component FROM prod_trans_tbl a, prod_trans_dtl_tbl b, material_tbl c, component_type_tbl d, component_type_tbl e, process_type_tbl f WHERE  a.prod_id = " . $prod_id . " AND a.bc_id = " . $bc . " AND a.prod_trans_status = 3 AND a.prod_trans_id = b.prod_trans_id AND b.component_type_id = d.component_type_id AND a.component_type_id = e.component_type_id AND b.article_type_id = 1 AND b.article_id = c.material_id AND a.process_type_id = f.process_type_id AND b.prod_trans_dtl_date = '" . $date . "' AND f.process_type_name = '" . $process . "'

					UNION ALL

					SELECT a.prod_id, b.rate, b.cost, c.service_desc, b.component_type_id, d.component_type as det_component, e.component_type as trans_component FROM prod_trans_tbl a, prod_trans_dtl_tbl b, services_tbl c, component_type_tbl d, component_type_tbl e, process_type_tbl f WHERE  a.prod_id = " . $prod_id . " AND a.bc_id = " . $bc . " AND a.prod_trans_status = 3 AND a.prod_trans_id = b.prod_trans_id AND b.component_type_id = d.component_type_id AND a.component_type_id = e.component_type_id AND b.article_type_id = 2 AND b.article_id = c.service_id AND a.process_type_id = f.process_type_id AND b.prod_trans_dtl_date = '" . $date . "' AND f.process_type_name = '" . $process . "'");

				$cost_rec_kgs = 0;
				$cost_rec_amount = 0;
				$raw_rate = 0;
				foreach($get_prod_det as $row_det){
					$components = $row_det->det_component;
					$mat_details = $row_det->material_desc;
					$rate = $row_det->rate;
					$cost = $row_det->cost;
					if($components == 'COST RECOVERY'){
						echo '<br/>';
						echo $mat_details . ' - ' . round(($total_kgs * ($rate / 100)) * $cost, 3) . ' | ' . number_format(($total_kgs * ($rate / 100)), 3);
						echo '<br />';
						$cost_rec_kgs = round($cost_rec_kgs + ($total_kgs * ($rate / 100)), 3);
						$cost_rec_amount = round($cost_rec_amount + ($total_kgs * ($rate / 100)) * $cost, 3);
					}elseif($components == 'CONVERSION COST'){
						echo '<br/>';
						echo $mat_details . ' - ' . round(($total_qty * $rate), 3);
						echo '<br />';
						$total_cost_amount = round($total_cost_amount + ($total_qty * $rate), 3);

					}elseif($components == "PACKAGING"){
						if(	$rate > 0){
							echo '<br/>';
							echo $mat_details . ' - ' . round((($total_qty / $rate) * $cost),3);
							echo '<br />';
							$total_cost_amount = round($total_cost_amount + (($total_qty / $rate) * $cost), 3);
						}
					}elseif($components == "RAW MATERIALS"){
						$raw_rate = $rate;

					}else{
						$total_cost_amount = round($total_cost_amount + ($total_qty * $cost), 3);
					}
				}

				$total_kgs = round($total_kgs - $cost_rec_kgs, 3);
				$total_cost_amount = round($total_cost_amount - $cost_rec_amount, 3);
				$total_qty = round($total_qty / $raw_rate, 3);
				echo '<br/ >';
				echo 'Qty : ' . round($total_qty, 3);
				echo '<br />';
				echo 'KGS: ' . round($total_kgs, 3);
				echo '<br />';
				echo 'AVE WT: ' . round($total_kgs / $total_qty, 3);
				echo '<br />';
				echo 'Cost/Price: ' . round($total_cost_amount / $total_qty, 3);
				echo '<br />';
				echo 'Amount: ' . round($total_cost_amount, 3);
				echo '<br />';
			}elseif($process == "FURTHER PROCESS - CUTTING"){
				$get_prod_det = $this->admin->get_query("SELECT a.prod_id, b.rate, b.cost, c.material_desc, b.component_type_id, d.component_type as det_component, e.component_type as trans_component FROM prod_trans_tbl a, prod_trans_dtl_tbl b, material_tbl c, component_type_tbl d, component_type_tbl e, process_type_tbl f WHERE  a.prod_id = " . $prod_id . " AND a.bc_id = " . $bc . " AND a.prod_trans_status = 3 AND a.prod_trans_id = b.prod_trans_id AND b.component_type_id = d.component_type_id AND a.component_type_id = e.component_type_id AND b.article_type_id = 1 AND b.article_id = c.material_id AND a.process_type_id = f.process_type_id AND b.prod_trans_dtl_date = '" . $date . "' AND f.process_type_name = '" . $process . "'

					UNION ALL

					SELECT a.prod_id, b.rate, b.cost, c.service_desc, b.component_type_id, d.component_type as det_component, e.component_type as trans_component FROM prod_trans_tbl a, prod_trans_dtl_tbl b, services_tbl c, component_type_tbl d, component_type_tbl e, process_type_tbl f WHERE  a.prod_id = " . $prod_id . " AND a.bc_id = " . $bc . " AND a.prod_trans_status = 3 AND a.prod_trans_id = b.prod_trans_id AND b.component_type_id = d.component_type_id AND a.component_type_id = e.component_type_id AND b.article_type_id = 2 AND b.article_id = c.service_id AND a.process_type_id = f.process_type_id AND b.prod_trans_dtl_date = '" . $date . "' AND f.process_type_name = '" . $process . "'");

				$cost_rec_kgs = 0;
				$cost_rec_amount = 0;
				$raw_rate = 0;
				echo 'Total Cost Raw: ' . $total_cost_amount;
				echo $total_cost_amount;
				echo '<br />';
				foreach($get_prod_det as $row_det){
					$components = $row_det->det_component;
					$mat_details = $row_det->material_desc;
					$rate = $row_det->rate;
					$cost = $row_det->cost;
					if($components == 'COST RECOVERY'){

						$cost_rec_kgs = round($cost_rec_kgs + ($total_kgs * ($rate / 100)), 3);
						$cost_rec_amount = round($cost_rec_amount + ($total_kgs * ($rate / 100)) * $cost, 3);

						echo '<br/>';
						echo $mat_details . ' - ' . round(($total_kgs * ($rate / 100)) * $cost, 3) . ' | ' . round(($total_kgs * ($rate / 100)), 3);
						echo '<br />';
						echo 'Total kgs: ' . round($total_kgs, 3) . ' | Rate: ' . round($rate, 3) . ' | Cost:' . number_format($cost, 3); 
					}elseif($components == 'CONVERSION COST'){
						echo '<br/>';
						echo $mat_details . ' - ' . round(($total_qty * $rate), 3);
						echo '<br />';
						$total_cost_amount = round($total_cost_amount + ($total_qty * $rate), 3);

					}elseif($components == "PACKAGING"){
						if(	$rate > 0){
							echo '<br/>';
							echo $mat_details . ' - ' . round((($total_qty / $rate) * $cost), 3);
							echo '<br />';
							$total_cost_amount = round($total_cost_amount + (($total_qty / $rate) * $cost), 3);
						}
					}elseif($components == "RAW MATERIALS"){
						$raw_rate = $rate;

					}else{
						$total_cost_amount = round($total_cost_amount + ($total_qty * $cost), 3);
					}
				}

				$total_kgs = round($total_kgs - $cost_rec_kgs, 3);
				$total_cost_amount = round($total_cost_amount - $cost_rec_amount, 3);
				$total_qty = round($total_qty / $raw_rate, 3);
				echo '<br/ >';
				echo 'Qty : ' . round($total_qty, 3);
				echo '<br />';
				echo 'KGS: ' . round($total_kgs, 3);
				echo '<br />';
				echo 'AVE WT: ' . round($total_kgs / $total_qty, 3);
				echo '<br />';
				echo 'Cost/Price: ' . round($total_cost_amount / $total_kgs	, 3);
				echo '<br />';
				echo 'Amount: ' . round($total_cost_amount, 3);
				echo '<br />';
			}elseif($process == "FURTHER PROCESS - INDIVIDUAL PACKAGING"){
				$get_prod_det = $this->admin->get_query("SELECT a.prod_id, b.rate, b.cost, c.material_desc, b.component_type_id, d.component_type as det_component, e.component_type as trans_component FROM prod_trans_tbl a, prod_trans_dtl_tbl b, material_tbl c, component_type_tbl d, component_type_tbl e, process_type_tbl f WHERE  a.prod_id = " . $prod_id . " AND a.bc_id = " . $bc . " AND a.prod_trans_status = 3 AND a.prod_trans_id = b.prod_trans_id AND b.component_type_id = d.component_type_id AND a.component_type_id = e.component_type_id AND b.article_type_id = 1 AND b.article_id = c.material_id AND a.process_type_id = f.process_type_id AND b.prod_trans_dtl_date = '" . $date . "' AND f.process_type_name = '" . $process . "'

					UNION ALL

					SELECT a.prod_id, b.rate, b.cost, c.service_desc, b.component_type_id, d.component_type as det_component, e.component_type as trans_component FROM prod_trans_tbl a, prod_trans_dtl_tbl b, services_tbl c, component_type_tbl d, component_type_tbl e, process_type_tbl f WHERE  a.prod_id = " . $prod_id . " AND a.bc_id = " . $bc . " AND a.prod_trans_status = 3 AND a.prod_trans_id = b.prod_trans_id AND b.component_type_id = d.component_type_id AND a.component_type_id = e.component_type_id AND b.article_type_id = 2 AND b.article_id = c.service_id AND a.process_type_id = f.process_type_id AND b.prod_trans_dtl_date = '" . $date . "' AND f.process_type_name = '" . $process . "'");

				$cost_rec_kgs = 0;
				$cost_rec_amount = 0;
				$raw_rate = 0;
				foreach($get_prod_det as $row_det){
					$components = $row_det->det_component;
					$mat_details = $row_det->material_desc;
					$rate = $row_det->rate;
					$cost = $row_det->cost;
					if($components == 'COST RECOVERY'){
						echo '<br/>';
						echo $mat_details . ' - ' . round(($total_kgs * ($rate / 100)) * $cost, 3) . ' | ' . number_format(($total_kgs * ($rate / 100)), 3);
						echo '<br />';
						$cost_rec_kgs = round($cost_rec_kgs + ($total_kgs * ($rate / 100)), 3);
						$cost_rec_amount = round($cost_rec_amount + ($total_kgs * ($rate / 100)) * $cost, 3);
					}elseif($components == 'CONVERSION COST'){
						echo '<br/>';
						echo $mat_details . ' - ' . round(($total_qty * $rate), 3);
						echo '<br />';
						$total_cost_amount = round($total_cost_amount + ($total_qty * $rate), 3);

					}elseif($components == "PACKAGING"){
						if(	$rate > 0){
							echo '<br/>';
							echo $mat_details . ' - ' . round((($total_qty / $rate) * $cost),3);
							echo '<br />';
							$total_cost_amount = round($total_cost_amount + (($total_qty / $rate) * $cost), 3);
						}
					}elseif($components == "RAW MATERIALS"){
						$raw_rate = $rate;

					}else{
						$total_cost_amount = round($total_cost_amount + ($total_qty * $cost), 3);
					}
				}

				$total_kgs = round($total_kgs - $cost_rec_kgs, 3);
				$total_cost_amount = round($total_cost_amount - $cost_rec_amount, 3);
				$total_qty = round($total_qty / $raw_rate, 3);
				echo '<br/ >';
				echo 'Qty : ' . round($total_qty, 3);
				echo '<br />';
				echo 'KGS: ' . round($total_kgs, 3);
				echo '<br />';
				echo 'AVE WT: ' . round($total_kgs / $total_qty, 3);
				echo '<br />';
				echo 'Cost/Price: ' . round($total_cost_amount / $total_kgs, 3);
				echo '<br />';
				echo 'Amount: ' . round($total_cost_amount, 3);
				echo '<br />';
			}
			echo '<br />';
		}
		//print_r($data);
	}