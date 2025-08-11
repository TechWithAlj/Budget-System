			
			<div class="col-lg-12" id="content">
				<div id="breadcrumb-div">
					<ul class="breadcrumb">
					    <li><a href="<?=base_url('business-center')?>"><span class="glyphicon glyphicon-home"></span>&nbsp;Dashboard</a></li>
					    <?php if(decode($process_type_id) == 5){ ?>
					    <li><a href="<?=base_url('business-center/sales-bom-trans/'.$bc_id)?>">Sales BOM Transaction</a></li>
					    <?php } else { ?>
					    <li><a href="<?=base_url('business-center/prod-trans/'.$bc_id)?>">Production Transaction</a></li>
					    <?php } ?>
					    <li class="active">View Cost Sheet (<?=$bc->bc_name?>)</li>
					</ul>
				</div>

				<?php
					if($this->session->flashdata('message') != "" ){
						echo $this->session->flashdata('message');
					}
				?>
				
				<div class="row">					
					<?php for ($i=1; $i <= 12 ; $i++) { ?>
					<div class="col-lg-12">
						<div class="table-responsive">
							<table class="table table-hover table-bordered table-stripe" id="tbl-view-cost-sheet-<?=$i?>">
								<thead>
									<tr>
										<th colspan="10" class="text-center">
											<?=date('F Y', strtotime(decode($year).'-'.$i.'-'.'01'))?>
										</th>
									</tr>
									<tr>
										<th  width="20%">Item Name</th>
										<th  width="auto">Base Unit</th>
										<th  width="auto">Val. Unit</th>
										<th  width="auto">Component Type</th>
										<th  width="auto">Rate</th>
										<th  width="auto">Qty</th>
										<th  width="auto">Wgt</th>
										<th  width="auto">Ave. Wgt</th>
										<th  width="auto">Cost/Price</th>
										<th  width="auto">Amount</th>
									</tr>
								</thead>
								<tbody>
									<?php
									$harvested_heads_array = harvested_heads($bc_id, $year, encode('trans'));
									$harvested_kilo_array = harvested_kilo($bc_id, $year, encode('trans'));
									$live_sales_counter = 1;
									$alw_live_counter = 1;
									if(count($live_sales)){
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

									//LOOP OF THE CONFIGURATION TO FIND PRODUCED WGT AND QTY
									foreach($config_rate as $row){

										if($row->trans_month == $i){
											$rate = amount_type($row->rate, $row->amount_type_id);
											/*$harvested_heads =  40105;
											$harvested_kilo =  56845.07;
											$broiler_alw = 1.42;
											$broiler_cost = 70.16;*/

											$live_sales_val = $budget_live_sales_array[$row->trans_month];
											$live_alw_val = $budget_live_alw_array[$row->trans_month];
											$live_sales_kg_val = $budget_live_sales_kg_array[$row->trans_month];

											$harvested_heads=$harvested_heads_array[$row->trans_month];
											$harvested_heads = $harvested_heads - $live_sales_val;

											$harvested_kilo=$harvested_kilo_array[$row->trans_month];
											$harvested_kilo = $harvested_kilo - $live_sales_kg_val;
											$broiler_alw = $harvested_heads <= 0 ? 0 : $harvested_kilo / $harvested_heads;
											$broiler_cost = round(get_broiler_cost($bc_id, $year, $row->trans_month, encode('trans')), 2);
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
											//PRODUCTION YIELD
											if($row->article_id == 9 && $row->article_type_id == 2){
												$kg_value = $rate * $harvested_kilo;
												$kg_value = round($kg_value, 3);
											}
										}
									}
									$total_amount = 0;
									$amount = 0;
									$produced_qty = 0;
									$produced_wgt = 0;
									$lossed_wgt = 0;
									//LOOP FOR THE DISPLAY ON PER MONTH BASIS
									foreach($prod_trans as $row):

										if($row->trans_month == $i):
											if($row->process_type_id == 1){ //BASIC PROCESSING
												$rate = amount_type($row->rate, $row->amount_type_id);
												$cost = $row->cost;
												$qty = 0;
												
												$wgt = '';
												$alw = '';

												$total_loss = $ema_qty + $doa_qty;
												$qty_value = $total_loss + $harvested_heads;
												$qty_value = round($qty_value, 3);

												//COST RECOVERY
												if($row->component_type_id == 4){
													$qty = $rate * $harvested_kilo;
													$amount = round($qty, 3) * $row->cost * -1;
													//$qty = number_format($qty,2,'.',',');
													
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
														//$qty = number_format($qty,0,'.',',');
														
													} else {
														$qty = $rate == 0 ? 0 : $qty_value/$rate;
														$amount = round($qty, 3) * $row->cost;
														//$qty = number_format($qty,0,'.',',');
														
													}
													if($row->material_desc=='LIVER PLASTIC'){
														$qty = $rate * $liver_fresh;
														$amount = round($qty, 3) * $row->cost;
														//$qty = number_format($qty,0,'.',',');
														
													}
													if($row->material_desc=='GIZZARD PLASTIC'){
														$qty = $rate * $gizzard;
														$amount = round($qty, 3) * $row->cost;
														//$qty = number_format($qty,0,'.',',');
														
													}
												}

												//CONVERSION COST
												if($row->component_type_id == 3){
													//nmic hds
													if($row->article_id == 4 && $row->article_type_id == 2){
														$qty = $harvested_heads * $rate;
													} else if ($row->article_id == 5 && $row->article_type_id == 2){
														$qty = $kg_value * $rate;
													} else {
														$qty = $qty_value * $rate;
													}
													$amount = round($qty,3);
													$qty = '';
													$cost = '';
												} else if($row->component_type_id == 7){
													$qty = '';
													$cost = '';
													$amount = '';
												} else if($row->component_type_id == 2){
													$qty = $harvested_heads;
													$cost = round($broiler_cost, 3);
													$wgt = $harvested_kilo;
													$alw = $broiler_alw;
													$amount = round($cost*$wgt,3);
													$wgt = $harvested_kilo;
													$rate = '';
												} else if($row->component_type_id == 1){
													$qty = $qty_value;
													
													$alw = $qty_value > 0 ? $kg_value/$qty_value : 0;
													$amount = $total_amount;
													$rate = '';
													$cost = $kg_value > 0 ? $total_amount/$kg_value : 0;
													$wgt = $kg_value;
													$cost = $cost;
												}

												$total_amount = $total_amount + $amount;
												if($row->amount_type_id == 1){
													$rate = $row->rate.'%';
													if($row->rate == 0){
														$rate = '';
													}
												} else {
													$rate = $row->rate;
													if($row->rate == 0){
														$rate = '';
													}
												}
											} else if($row->process_type_id == 2){ //CLASSIFICATION

												if(count($fdc_details)){
													$rate = amount_type($row->rate, $row->amount_type_id);
													$cost = 0;
													$qty = 0;
													$wgt= 0;
													$alw = $row->ave_wgt;
													$amount = 0;
													if($row->component_type_id == 2){ //RAW MATERIALS
														$qty = $fdc_details['qty-'.$i];
														$wgt = $fdc_details['wgt-'.$i];
														$alw = $fdc_details['alw-'.$i];
														$cost = $fdc_details['cost-'.$i];
														
														
														$amount = $fdc_details['amount-'.$i];
													} else if($row->component_type_id == 1){ //FINISHED GOODS
														$qty = round($rate * $fdc_details['qty-'.$i], 3);
														$wgt = round($qty * $alw, 3);
														$cost = $fdc_details['cost-'.$i];
														$amount = $wgt * $cost;
														//$wgt = $fdc_details['wgt-'.$i];
														
													}
												} else {
													$rate = amount_type($row->rate, $row->amount_type_id);
													$cost = 0;
													$qty = 0;
													$wgt= 0;
													$alw = $row->ave_wgt;
													$amount = 0;
												}
												

												if($row->amount_type_id == 1){
													$rate = $row->rate.'%';
													if($row->rate == 0){
														$rate = '';
													}
												} else {
													$rate = $row->rate;
													if($row->rate == 0){
														$rate = '';
													}
												}
											} else if($row->process_type_id == 5){ //sales bom
												$rate = amount_type($row->rate, $row->amount_type_id);
												if($row->component_type_id == 20){
													$qty = 1000;
													$alw = 0;
													$cost = 225.50;
													$wgt = $qty*$alw;
													$amount = $qty*$cost;

													$produced_qty = $qty;
												} else if($row->component_type_id == 21){ //COST OF SALES
													$result = get_further_process_marination_details($row->material_id, decode($bc_id), 4, $year);
													if(count($result)){
														$rate = 1;
														$qty = $produced_qty/$rate;//round($result[$row->material_code.'-qty-'.$i], 0);
														$alw = 0;//$result[$row->material_code.'-alw-'.$i];
														$cost = $result[$row->material_code.'-cost-'.$i];

														$wgt = $qty*$alw;
														$amount = $qty*$cost;
													} else {
														$rate = 1;
														$qty = 0;
														$alw = 0;
														$cost = 0;
														$wgt = 0;
														$amount = 0;
													}
													
												} else if($row->component_type_id == 18 || $row->component_type_id == 19) { //ALL SERVICES
													$qty = $rate == 0 ? 0 : $produced_qty/$rate;
													$wgt = '';
													$alw = '';
													$cost = $row->cost;
													$amount = $qty*$cost;

													//$qty = number_format($qty,2,'.',',');
													//$cost = number_format($cost,2,'.',',');
												} else if($row->article_type_id == 2) {
													$qty = '';
													$wgt = '';
													$alw = '';
													$cost = '';
													$amount = $produced_qty*$rate;

													//$qty = number_format($qty,2,'.',',');
													//$cost = number_format($cost,2,'.',',');
												} else {
													$qty = '';
													$wgt = '';
													$alw = '';
													$cost = '';
													$amount = '';
												}
											}

											if($i == 1){
												$update_data = array(
													
													'cost_sheet_stat' => 1
												);
												$where = array(
													'prod_trans_id' => $row->prod_trans_id
												);
												update_data('prod_trans_tbl', $update_data, $where);
											}

											$update_data = array(
												'qty' => $qty,
												'Wgt' => $wgt,
												'ave_wgt' => $alw,
												'cost' => $cost,
												'total_cost' => $amount

											);
											$where = array(
												'prod_trans_dtl_id' => $row->prod_trans_dtl_id
											);
											update_data('prod_trans_dtl_tbl', $update_data, $where);
										?>
										
											<tr>
												<td ><?=$row->article_type_id == 2 ? $row->material_desc : $row->material_code.' - '.$row->material_desc?></td>
												<td ><?=$row->buom_unit?></td>
												<td ><?=$row->unit_name?></td>
												<td ><?=$row->component_type?></td>
												<td ><?=$rate?></td>
												<?php if ($row->process_type_id == 2){ ?>
													<td ><?=$qty > 0 || $qty != '' ? number_format(intval($qty),0,'.',',') : '' ?></td>
													<td ><?=$wgt > 0 || $wgt != '' ? number_format(intval($wgt),0,'.',',') : '' ?></td>
												<?php }else{ ?>
													<td ><?=$qty > 0 || $qty != '' ? number_format($qty,2,'.',',') : '' ?></td>
													<td ><?=$wgt > 0 || $wgt != '' ? number_format($wgt,2,'.',',') : '' ?></td>
												<?php } ?>
												<td ><?=$alw?></td>
												<td ><?=$cost > 0 || $cost != '' ? number_format($cost,2,'.',',') : '' ?></td>
												<td ><?=$amount > 0 || $amount != '' ? number_format($amount,2,'.',',') : '' ?></td>
											</tr>
										
										<?php
										endif; ?>
									<?php endforeach; ?>
								</tbody>
							</table>
							<br><br>
						</div>
					</div>
					<?php } ?>
				</div>
				<br><br>
			</div>