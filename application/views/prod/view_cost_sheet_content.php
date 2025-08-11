			
			<div class="col-lg-12" id="content">
				<div id="breadcrumb-div">
					<ul class="breadcrumb">
					    <li><a href="<?=base_url('production')?>"><span class="glyphicon glyphicon-home"></span>&nbsp;Dashboard</a></li>
					    <li><a href="<?=base_url('production/prod-trans/'.$bc_id)?>">Production Transaction</a></li>
					    <li class="active">View Cost Sheet</li>
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
										<th  width="auto">Unit</th>
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
									//LOOP OF THE CONFIGURATION TO FIND PRODUCED WGT AND QTY
									foreach($config_rate as $row){
										if($row->trans_month == $i){
											$rate = amount_type($row->rate, $row->amount_type_id);
											/*$harvested_heads =  40105;
											$harvested_kilo =  56845.07;
											$broiler_alw = 1.42;
											$broiler_cost = 70.16;*/

											$harvested_heads_array = harvested_heads($bc_id, $year, encode('trans'));
											$harvested_kilo_array = harvested_kilo($bc_id, $year, encode('trans'));

											$harvested_heads=$harvested_heads_array[$row->trans_month];
											$harvested_kilo=$harvested_kilo_array[$row->trans_month];
											$broiler_alw = $harvested_kilo / $harvested_heads;
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
													$qty = number_format($qty,2,'.',',');
													
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
														$qty = number_format($qty,0,'.',',');
														
													} else {
														$qty = $rate == 0 ? 0 : $qty_value/$rate;
														$amount = round($qty, 3) * $row->cost;
														$qty = number_format($qty,0,'.',',');
														
													}
													if($row->material_desc=='LIVER PLASTIC'){
														$qty = $rate * $liver_fresh;
														$amount = round($qty, 3) * $row->cost;
														$qty = number_format($qty,0,'.',',');
														
													}
													if($row->material_desc=='GIZZARD PLASTIC'){
														$qty = $rate * $gizzard;
														$amount = round($qty, 3) * $row->cost;
														$qty = number_format($qty,0,'.',',');
														
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
													$qty = number_format($harvested_heads,2,'.',',');
													$cost = round($broiler_cost, 3);
													$wgt = $harvested_kilo;
													$alw = number_format($broiler_alw,3,'.',',');
													$amount = round($cost*$wgt,3);
													$wgt = number_format($harvested_kilo,2,'.',',');
													$rate = '';
												} else if($row->component_type_id == 1){
													$qty = number_format($qty_value,2,'.',',');
													
													$alw = number_format($kg_value/$qty_value,3,'.',',');
													$amount = $total_amount;
													$rate = '';
													$cost = $kg_value > 0 ? $total_amount/$kg_value : 0;
													$wgt = number_format($kg_value,2,'.',',');
													$cost = number_format($cost,2,'.',',');
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
													$alw = $row->cost;
													$amount = 0;
													if($row->component_type_id == 2){ //RAW MATERIALS
														$qty = $fdc_details['qty-'.$i];
														$wgt = $fdc_details['wgt-'.$i];
														$alw = $fdc_details['alw-'.$i];
														$cost = $fdc_details['cost-'.$i];
														
														$qty = number_format($qty,0,'.',',');
														$wgt = number_format($wgt,2,'.',',');
														$alw = number_format($alw,3,'.',',');
														$cost = number_format($cost,2,'.',',');
														$amount = $fdc_details['amount-'.$i];
													} else if($row->component_type_id == 1){ //FINISHED GOODS
														$qty = round($rate * $fdc_details['qty-'.$i], 3);
														$wgt = round($qty * $alw, 3);
														$cost = $fdc_details['cost-'.$i];
														$amount = $wgt * $cost;
														//$wgt = $fdc_details['wgt-'.$i];
														$alw = number_format($alw,3,'.',',');
														$qty = number_format($qty,0,'.',',');
														$wgt = number_format($wgt,2,'.',',');
														$cost = number_format($cost,2,'.',',');
													}
												} else {
													$rate = amount_type($row->rate, $row->amount_type_id);
													$cost = 0;
													$qty = 0;
													$wgt= 0;
													$alw = $row->cost;
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
											} else if($row->process_type_id == 3){ //FURTHER PROCESSING
												$rate = amount_type($row->rate, $row->amount_type_id);
												
												if($row->component_type_id == 2){
													//fixed fdc - classification bom
													$classification_bom = get_data('material_tbl', array('material_code'	=>	'1000124', 'material_status'	=> 1), true);

													$prod_id = !$classification_bom ? 0 : $classification_bom->material_id;
													if($prod_id){
														$classificaion_details = get_classification_details($prod_id, decode($bc_id), 2, decode($year));
													} else {
														$classificaion_details = 0;
													}
													if(count($classificaion_details) && $classificaion_details != 0){
														$qty = $classificaion_details[$row->material_code.'-qty-'.$i];
														//$wgt = $classificaion_details[$row->material_code.'-wgt-'.$i];
														$alw = $classificaion_details[$row->material_code.'-alw-'.$i];
														$cost = $classificaion_details[$row->material_code.'-cost-'.$i];
														//$amount = $classificaion_details[$row->material_code.'-amount-'.$i];
														$wgt = $qty*$alw;
														$amount = $wgt*$cost;

														$produced_wgt = $wgt;
														$produced_qty = $qty;
														$qty = number_format($qty,0,'.',',');
														$wgt = number_format($wgt,2,'.',',');
														$alw = number_format($alw,3,'.',',');
														$cost = number_format($cost,2,'.',',');
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

													$cost = number_format($cost,2,'.',',');
													$qty = number_format($qty,2,'.',',');
												} else if($row->component_type_id == 3){
													$wgt = '';
													$qty = '';
													$alw = '';
													$cost = '';
													$amount = $produced_qty*$rate;
												} else if($row->component_type_id == 1){
													$qty = $produced_qty;
													$wgt = $produced_wgt + $lossed_wgt;
													
													
													$alw = $qty <= 0 ? 0 : $wgt/$qty;
													$amount = $total_amount;
													$cost = $wgt <= 0 ? 0 : round($total_amount/$wgt, 3);
													
													$cost = number_format($cost,2,'.',',');
													$qty = number_format($qty,0,'.',',');
													$wgt = number_format($wgt,2,'.',',');
													$alw = number_format($alw,3,'.',',');
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
											} else if($row->process_type_id == 4){ //FURTHER PROCESSING MARINATION
												$rate = amount_type($row->rate, $row->amount_type_id);
												if($row->component_type_id == 2){

													$result = get_further_process_neckless_details($row->material_id, decode($bc_id), 3, decode($year));
													$qty = round($result[$row->material_code.'-qty-'.$i], 0);
													$alw = $result[$row->material_code.'-alw-'.$i];
													$cost = $result[$row->material_code.'-cost-'.$i];
													$wgt = $qty*$alw;
													$amount = $wgt*$cost;
													//$wgt = $result[$row->material_code.'-wgt-'.$i];
													//$amount = $result[$row->material_code.'-amount-'.$i];

													$produced_wgt = $wgt;
													$produced_qty = $qty;
													$qty = number_format($qty,0,'.',',');
													$wgt = number_format($wgt,3,'.',',');
													$alw = number_format($alw,3,'.',',');
													$cost = number_format($cost,2,'.',',');
												} else if($row->component_type_id == 6) { //MARINADE
													$qty = $rate * $produced_qty;
													$wgt = '';
													$alw = '';
													$cost = $row->cost;
													$amount = $qty*$cost;

													$qty = number_format($qty,0,'.',',');
													$cost = number_format($cost,2,'.',',');
												} else if($row->component_type_id == 5) { //PACKAGING
													$qty = $rate <= 0 || $rate == '' ? 0 : $produced_qty/$rate;
													$wgt = '';
													$alw = '';
													$cost = $row->cost;
													$amount = $qty*$cost;

													$qty = number_format($qty,0,'.',',');
													$cost = number_format($cost,2,'.',',');
												} else if($row->component_type_id == 3) {
													$qty = '';
													$wgt = '';
													$alw = '';
													$cost = '';
													$amount = $rate*$produced_qty;
												} else if($row->component_type_id == 1){
													$qty = $produced_qty;
													$wgt = '';
													$alw = '';
													$cost = $produced_qty <= 0 ? 0 : $total_amount/$produced_qty;
													$amount = $total_amount;

													$qty = number_format($qty,0,'.',',');
													$cost = number_format($cost,2,'.',',');
												} else {
													$qty = '';
													$wgt = '';
													$alw = '';
													$cost = '';
													$amount = '';
												}
												$total_amount = $total_amount + $amount;
											} else if($row->process_type_id == 5){ //FURTHER PROCESSING MARINATION
												$rate = amount_type($row->rate, $row->amount_type_id);
												if($row->component_type_id == 1){
													$qty = 1000;
													$alw = 0;
													$cost = 225.50;
													$wgt = $qty*$alw;
													$amount = $qty*$cost;

													$produced_qty = $qty;
												} else if($row->component_type_id == 21){ //COST OF SALES
													$result = get_further_process_marination_details($row->material_id, decode($bc_id), 4, decode($year));
													$rate = 1;
													$qty = $produced_qty/$rate;//round($result[$row->material_code.'-qty-'.$i], 0);
													$alw = 0;//$result[$row->material_code.'-alw-'.$i];
													$cost = $result[$row->material_code.'-cost-'.$i];

													$wgt = $qty*$alw;
													$amount = $qty*$cost;
												} else if($row->component_type_id == 18 || $row->component_type_id == 19) { //ALL SERVICES
													$qty = $rate == 0 ? 0 : $produced_qty/$rate;
													$wgt = '';
													$alw = '';
													$cost = $row->cost;
													$amount = $qty*$cost;

													$qty = number_format($qty,2,'.',',');
													$cost = number_format($cost,2,'.',',');
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
										?>
											<tr>
												<td ><?=$row->material_desc?></td>
												<td ><?=$row->unit_name?></td>
												<td ><?=$row->component_type?></td>
												<td ><?=$rate?></td>
												<td ><?=$qty?></td>
												<td ><?=$wgt?></td>
												<td ><?=$alw?></td>
												<td ><?=$cost?></td>
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