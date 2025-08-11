			<div class="col-lg-12" id="content">
				<div id="breadcrumb-div">
					<ul class="breadcrumb">
					    <li><a href="<?=base_url('ahg')?>"><span class="glyphicon glyphicon-home"></span>&nbsp;Dashboard</a></li>
					    <li><a href="<?=base_url('ahg/broiler-trans/' . $bc_id)?>">Broiler Transaction</a></li>
					    <li class="active">Broiler Transaction Details (<?=$broiler_group_name?>)</li>
					</ul>
				</div>

				<?php
					if($this->session->flashdata('message') != "" ){
						echo $this->session->flashdata('message');
					}
				?>

				<div id="modal-confirm" class="modal fade" role="dialog">
					<div class="modal-dialog modal-sm">
					    <div class="modal-content">
					    	<div class="modal-header">
					        	<button type="button" class="close" data-dismiss="modal">&times;</button>
					       		<strong>Confirmation message</strong>
					      	</div>
					      	<div class="modal-body">
					      		<form method="POST" action="<?=base_url('ahg/post-broiler-trans')?>" enctype="multipart/form-data" id="post-broiler-trans">
					      			<input type="hidden" name="broiler_trans_id" id="broiler_trans_id">
					      			<input type="hidden" name="bc_id" id="bc_id">
					      			<input type="hidden" name="broiler_group_id" id="broiler_group_id">
					      			<input type="hidden" name="broiler_group_name" id="broiler_group_name">
					      			<input type="hidden" name="trans_year" id="trans_year">
					      			<input type="hidden" name="broiler_trans_status" id="broiler_trans_status">
						        	<div id="modal-msg" class="text-center">
						        		
						        	</div>
						        	<div id="modal-btn" class="text-center">
						        		<button type="submit" class="btn btn-info btn-sm">Yes</button>&nbsp;&nbsp;
						        		<a href="" data-dismiss="modal" class="btn btn-danger btn-sm">No</a>
						        	</div>
						        </form>
					      	</div>
					    </div>
					</div>
				</div>
				<?php
				$year = decode($trans_year);
				$doctype = encode('trans');
				?>
				<div class="table-responsive">
					<table class="table table-hover table-bordered table-stripe nowrap" id="tbl-view-broiler-transaction">
						<thead>
							<tr>
								<td width="3%"></td>
								<th width="30%">Broiler Subgroup Name</th>
							<?php for ($i=1; $i <= 12 ; $i++){ ?>
								<th width="auto" class="text-center"><?=date('M', strtotime($year.'-'.$i.'-01'))?></th>
							<?php } ?>
							</tr>
						</thead>
						<tbody>

							<?php
							$count = 1;
							foreach($broiler_trans as $row):
							?>
							<tr>
								<?php if($row->status_id == 3){ ?>
								<td class="text-center"><a href="<?=base_url('ahg/edit-broiler-trans/' . encode($row->broiler_trans_id).'/'.encode($row->broiler_subgroup_name).'/'. encode($row->bc_id).'/'. encode($row->broiler_group_id).'/'. encode($row->broiler_group_name))?>"><i class="fa fa-pencil"></i></a>&nbsp;&nbsp;<a href="" class="post-broiler-trans" data-id="<?=encode($row->broiler_trans_id)?>" data-bc="<?=encode($row->bc_id)?>" data-bgid="<?=encode($row->broiler_group_id)?>" data-bgname="<?=encode($row->broiler_group_name)?>" data-transyear="<?=$trans_year?>"><i class="fa fa-lock"></i></td>
								<?php } else { ?>
								<td class="text-center"><a href="" class="cancel-broiler-trans" data-id="<?=encode($row->broiler_trans_id)?>" data-bc="<?=encode($row->bc_id)?>" data-bgid="<?=encode($row->broiler_group_id)?>" data-bgname="<?=encode($row->broiler_group_name)?>" data-transyear="<?=$trans_year?>"><i class="fa fa-remove"></i></a></td>
								<?php }
								if($row->broiler_subgroup_name == 'DOC Placement'){
									$decimal = 0;
								} else {
									$decimal = 3;
								}?>
								<th><?=$row->broiler_subgroup_name?></th>
								<td class="text-right"><?=number_format(get_data('broiler_trans_dtl_tbl a', array('a.broiler_trans_id' => $row->broiler_trans_id, 'MONTH(a.broiler_trans_date)'	=>	1, 'YEAR(a.broiler_trans_date)' => $year), true, 'a.broiler_budget_qty')->broiler_budget_qty,$decimal,'.',',')?></td>
								<td class="text-right"><?=number_format(get_data('broiler_trans_dtl_tbl a', array('a.broiler_trans_id' => $row->broiler_trans_id, 'MONTH(a.broiler_trans_date)'	=>	2, 'YEAR(a.broiler_trans_date)' => $year), true, 'a.broiler_budget_qty')->broiler_budget_qty,$decimal,'.',',')?></td>
								<td class="text-right"><?=number_format(get_data('broiler_trans_dtl_tbl a', array('a.broiler_trans_id' => $row->broiler_trans_id, 'MONTH(a.broiler_trans_date)'	=>	3, 'YEAR(a.broiler_trans_date)' => $year), true, 'a.broiler_budget_qty')->broiler_budget_qty,$decimal,'.',',')?></td>
								<td class="text-right"><?=number_format(get_data('broiler_trans_dtl_tbl a', array('a.broiler_trans_id' => $row->broiler_trans_id, 'MONTH(a.broiler_trans_date)'	=>	4, 'YEAR(a.broiler_trans_date)' => $year), true, 'a.broiler_budget_qty')->broiler_budget_qty,$decimal,'.',',')?></td>
								<td class="text-right"><?=number_format(get_data('broiler_trans_dtl_tbl a', array('a.broiler_trans_id' => $row->broiler_trans_id, 'MONTH(a.broiler_trans_date)'	=>	5, 'YEAR(a.broiler_trans_date)' => $year), true, 'a.broiler_budget_qty')->broiler_budget_qty,$decimal,'.',',')?></td>
								<td class="text-right"><?=number_format(get_data('broiler_trans_dtl_tbl a', array('a.broiler_trans_id' => $row->broiler_trans_id, 'MONTH(a.broiler_trans_date)'	=>	6, 'YEAR(a.broiler_trans_date)' => $year), true, 'a.broiler_budget_qty')->broiler_budget_qty,$decimal,'.',',')?></td>
								<td class="text-right"><?=number_format(get_data('broiler_trans_dtl_tbl a', array('a.broiler_trans_id' => $row->broiler_trans_id, 'MONTH(a.broiler_trans_date)'	=>	7, 'YEAR(a.broiler_trans_date)' => $year), true, 'a.broiler_budget_qty')->broiler_budget_qty,$decimal,'.',',')?></td>
								<td class="text-right"><?=number_format(get_data('broiler_trans_dtl_tbl a', array('a.broiler_trans_id' => $row->broiler_trans_id, 'MONTH(a.broiler_trans_date)'	=>	8, 'YEAR(a.broiler_trans_date)' => $year), true, 'a.broiler_budget_qty')->broiler_budget_qty,$decimal,'.',',')?></td>
								<td class="text-right"><?=number_format(get_data('broiler_trans_dtl_tbl a', array('a.broiler_trans_id' => $row->broiler_trans_id, 'MONTH(a.broiler_trans_date)'	=>	9, 'YEAR(a.broiler_trans_date)' => $year), true, 'a.broiler_budget_qty')->broiler_budget_qty,$decimal,'.',',')?></td>
								<td class="text-right"><?=number_format(get_data('broiler_trans_dtl_tbl a', array('a.broiler_trans_id' => $row->broiler_trans_id, 'MONTH(a.broiler_trans_date)'	=>	10, 'YEAR(a.broiler_trans_date)' => $year), true, 'a.broiler_budget_qty')->broiler_budget_qty,$decimal,'.',',')?></td>
								<td class="text-right"><?=number_format(get_data('broiler_trans_dtl_tbl a', array('a.broiler_trans_id' => $row->broiler_trans_id, 'MONTH(a.broiler_trans_date)'	=>	11, 'YEAR(a.broiler_trans_date)' => $year), true, 'a.broiler_budget_qty')->broiler_budget_qty,$decimal,'.',',')?></td>
								<td class="text-right"><?=number_format(get_data('broiler_trans_dtl_tbl a', array('a.broiler_trans_id' => $row->broiler_trans_id, 'MONTH(a.broiler_trans_date)'	=>	12, 'YEAR(a.broiler_trans_date)' => $year), true, 'a.broiler_budget_qty')->broiler_budget_qty,$decimal,'.',',')?></td>
								
								<!-- <td>&nbsp;&nbsp;<a href="<?=base_url('admin/remove-broiler-config/' . encode($row->broiler_group_id))?>" class="btn btn-xs glyphicon glyphicon-remove remove-broiler-config" data-id="<?=encode($row->broiler_group_id)?>"></a></td> -->
							</tr>
							<?php
							$count++;
							endforeach;
							?>							
						</tbody>
					</table>
					<br>
				</div>
				<div class="row">
					<div class="col-lg-12">
						<hr>
					</div>
				</div>
				<div id="add-btn">
					<a href="<?=base_url('ahg/view-broiler-summary/' . $bc_id .'/'. $trans_year)?>" class="btn btn-info btn-xs">View Broiler Cost Summary</a>
				</div>
				<?php if($broiler_group_id == 1): ?>
					<div class="table-responsive">
						<label>Computation Results</label>
						<table class="table table-hover table-bordered table-stripe nowrap" id="tbl-view-broiler-result">
							<thead>
								<tr>
									<th width="30%">Computation Name</th>
								<?php for ($i=1; $i <= 12 ; $i++){ ?>
									<th width="auto" class="text-center"><?=date('M', strtotime($year.'-'.$i.'-01'))?></th>
								<?php } ?>
									<th class="text-center">Total</th>
								</tr>
							</thead>
							<tbody>
								<tr>
									<td style="text-indent: 10%;">Harvestable Heads</td>
								<?php
									/*$get_doc_cost_amount = doc_cost_amount($bc_id, $trans_year, $doctype);
									echo $get_doc_cost_amount;
									exit();*/
									$get_harvested_heads = harvested_heads($bc_id, $trans_year, $doctype);
									$harvested_heads = 0;
									for ($i=1; $i <= 12 ; $i++){
										$harvested_heads = $harvested_heads + $get_harvested_heads[$i];
								?>
										<td class="text-right"><?=number_format($get_harvested_heads[$i],0,'.',',')?></td>
								<?php
									}
								?>
									<td class="text-right"><?=number_format($harvested_heads,0,'.',',')?></td>
								</tr>
								<tr>
									<td style="text-indent: 10%;">Harvestable Kilos</td>
								<?php
								
									$harvested_kilo = 0;
									$get_harvested_kilo = harvested_kilo($bc_id, $trans_year, $doctype);
									for ($i=1; $i <= 12 ; $i++){
										$harvested_kilo = $harvested_kilo + $get_harvested_kilo[$i];
								?>
										<td class="text-right"><?=number_format($get_harvested_kilo[$i],dec_places_dis(),'.',',')?></td>
								<?php } ?>
									<td class="text-right"><?=number_format($harvested_kilo,dec_places_dis(),'.',',')?></td>
								</tr>
								<tr>
									<td style="text-indent: 10%;">DOC Cost Amount</td>
								<?php
									$doc_cost_amount = 0;
									$get_doc_cost_amount = doc_cost_amount($bc_id, $trans_year, $doctype);
									for ($i=1; $i <= 12 ; $i++){
										$doc_cost_amount = $doc_cost_amount + $get_doc_cost_amount[$i];
								?>
										<td class="text-right"><?=number_format($get_doc_cost_amount[$i],dec_places_dis(),'.',',')?></td>
								<?php
									}
								?>
									<td class="text-right"><?=number_format($doc_cost_amount,dec_places_dis(),'.',',')?></td>
								
								</tr>
								
							</tbody>
							<tfoot>
								<tr>
									<th><?=$broiler_group_name?></th>
								<?php
								
									$doc = 0;
									$get_doc = doc($bc_id, $trans_year, $doctype);
									for ($i=1; $i <= 12 ; $i++){
										$doc = $doc + $get_doc[$i];
								?>
										<th class="text-right"><?=number_format($get_doc[$i],dec_places_dis(),'.',',')?></th>
								<?php } ?>
									<th class="text-right"><?=$harvested_kilo == 0 ? 0 : number_format($doc_cost_amount/$harvested_kilo,dec_places_dis(),'.',',')?></th>
								</tr>
							</tfoot>
						</table>
						<br>
						<hr>
					</div>
				<?php endif; ?>
				<?php if($broiler_group_id == 4): ?>
					<div class="table-responsive">
						<label>Computation Results</label>
						<table class="table table-hover table-bordered table-stripe nowrap" id="tbl-view-broiler-result">
							<thead>
								<tr>
									<th width="30%">Computation Name</th>
								<?php for ($i=1; $i <= 12 ; $i++){ ?>
									<th width="auto" class="text-center"><?=date('M', strtotime($year.'-'.$i.'-01'))?></th>
								<?php } ?>
									<th class="text-center">Total</th>
								</tr>
							</thead>
							<tbody>
								
								<tr>
									<td style="text-indent: 10%;">Vaccines Amount</td>
								<?php
									$vaccines_amount = 0;
									$get_vaccine_amount = vaccines_amount($bc_id, $trans_year, $doctype);
									for ($i=1; $i <= 12 ; $i++){
										$vaccines_amount = $vaccines_amount + $get_vaccine_amount[$i];
								?>
										<td class="text-right"><?=number_format($get_vaccine_amount[$i],dec_places_dis(),'.',',')?></td>
								<?php
									}
								?>
									<td class="text-right"><?=number_format($vaccines_amount,dec_places_dis(),'.',',')?></td>
								</tr>
							</tbody>
							<tfoot>
								<tr>
									<th><?=$broiler_group_name?></th>
								<?php
								
									$vaccines = 0;
									$harvested_kilo = 0;
									$get_harvested_kilo = harvested_kilo($bc_id, $trans_year, $doctype);

									$get_vaccines = vaccines($bc_id, $trans_year, $doctype);
									for ($i=1; $i <= 12 ; $i++){
										$harvested_kilo = $harvested_kilo + $get_harvested_kilo[$i];
										$vaccines = $vaccines + $get_vaccines[$i];
								?>
										<th class="text-right"><?=number_format($get_vaccines[$i],dec_places_dis(),'.',',')?></th>
								<?php } ?>
									<th class="text-right"><?=$harvested_kilo == 0 ? 0 : number_format($vaccines_amount/$harvested_kilo,dec_places_dis(),'.',',')?></th>
								</tr>
							</tfoot>
						</table>
						<br>
						<hr>
					</div>
				<?php endif; ?>
				<?php if($broiler_group_id == 5): ?>
					<div class="table-responsive">
						<label>Computation Results</label>
						<table class="table table-hover table-bordered table-stripe nowrap" id="tbl-view-broiler-result">
							<thead>
								<tr>
									<th width="30%">Computation Name</th>
								<?php for ($i=1; $i <= 12 ; $i++){ ?>
									<th width="auto" class="text-center"><?=date('M', strtotime($year.'-'.$i.'-01'))?></th>
								<?php } ?>
									<th class="text-center">Total</th>
								</tr>
							</thead>
							<tbody>
								
								<tr>
									<td style="text-indent: 10%;">Medicines Amount</td>
								<?php
									$medicine_amount = 0;
									$get_medicine_amount = medicine_amount($bc_id, $trans_year, $doctype);
									for ($i=1; $i <= 12 ; $i++){
										$medicine_amount = $medicine_amount + $get_medicine_amount[$i];
								?>
										<td class="text-right"><?=number_format($get_medicine_amount{$i},dec_places_dis(),'.',',')?></td>
								<?php
									}
								?>
									<td class="text-right"><?=number_format($medicine_amount,dec_places_dis(),'.',',')?></td>
								</tr>
								<tr>
									<td style="text-indent: 10%;">Disinfectants Amount</td>
								<?php

									$disinfectant_amount = 0;
									$get_disinfectant_amount = disinfectant_amount($bc_id, $trans_year, $doctype);
									for ($i=1; $i <= 12 ; $i++){
										$disinfectant_amount = $disinfectant_amount + $get_disinfectant_amount[$i];
								?>
									<td class="text-right"><?=number_format($get_disinfectant_amount[$i],dec_places_dis(),'.',',')?></td>
								<?php } ?>
									<td class="text-right"><?=number_format($disinfectant_amount,dec_places_dis(),'.',',')?></td>
								</tr>

							</tbody>
							<tfoot>
								<tr>
									<th><?=$broiler_group_name?></th>
								<?php
								
									$medicines = 0;
									$harvested_kilo = 0;
									$get_harvested_kilo = harvested_kilo($bc_id, $trans_year, $doctype);
									$get_medicines = medicines($bc_id, $trans_year, $doctype);

									for ($i=1; $i <= 12 ; $i++){
										$harvested_kilo = $harvested_kilo + $get_harvested_kilo[$i];
										$medicines = $medicines + $get_medicines[$i];
								?>
									<th class="text-right"><?=number_format($get_medicines{$i},dec_places_dis(),'.',',')?></th>
								<?php } ?>
								<?php $total =  $disinfectant_amount + $medicine_amount?>
									<th class="text-right"><?=$harvested_kilo == 0 ? 0 : number_format($total/$harvested_kilo,dec_places_dis(),'.',',')?></th>
								</tr>
							</tfoot>
						</table>
						<br>
						<hr>
					</div>
				<?php endif; ?>
				<?php if($broiler_group_id == 3): ?>
					<div class="table-responsive">
						<label>Computation Results</label>
						<table class="table table-hover table-bordered table-stripe nowrap" id="tbl-view-broiler-result">
							<thead>
								<tr>
									<th width="30%">Computation Name</th>
								<?php for ($i=1; $i <= 12 ; $i++){ ?>
									<th width="auto" class="text-center"><?=date('M', strtotime($year.'-'.$i.'-01'))?></th>
								<?php } ?>
									<th class="text-center">Total</th>
								</tr>
							</thead>
							<tbody>
								<tr>
									<td style="text-indent: 5%;">Feed Cost Amount</td>
								<?php
									$feed_cost_amount = 0;
									$get_feed_cost_amount = feed_cost_amount($bc_id, $trans_year, $doctype);
									for ($i=1; $i <= 12 ; $i++){
										$feed_cost_amount = $feed_cost_amount + $get_feed_cost_amount[$i];
								?>
										<td class="text-right"><?=number_format($get_feed_cost_amount[$i],dec_places_dis(),'.',',')?></td>
								<?php
									}
								?>
									<td class="text-right"><?=number_format($feed_cost_amount,dec_places_dis(),'.',',')?></td>
								</tr>

								<tr>
									<td style="text-indent: 10%;">Feed Cost/kg Broiler (Reg)</td>
								<?php
									$feed_cost_kg_reg = 0;
									$get_feed_cost_kg_reg = feed_cost_kg_reg($bc_id, $trans_year, $doctype);
									for ($i=1; $i <= 12 ; $i++){
										$feed_cost_kg_reg = $feed_cost_kg_reg + $get_feed_cost_kg_reg[$i]
								?>
										<td class="text-right"><?=number_format($get_feed_cost_kg_reg[$i],dec_places_dis(),'.',',')?></td>
								<?php
									}
								?>
									<td class="text-right"><?=number_format($feed_cost_kg_reg,dec_places_dis(),'.',',')?></td>
								</tr>
								<tr>
									<td style="text-indent: 10%;">Feed Cost/kg Broiler (NAE)</td>
								<?php
									$feed_cost_kg_nae = 0;
									$get_feed_cost_kg_nae = feed_cost_kg_nae($bc_id, $trans_year, $doctype);
									for ($i=1; $i <= 12 ; $i++){
										$feed_cost_kg_nae = $feed_cost_kg_nae + $get_feed_cost_kg_nae[$i];
								?>
										<td class="text-right"><?=number_format($get_feed_cost_kg_nae[$i],dec_places_dis(),'.',',')?></td>
								<?php
									}
								?>
									<td class="text-right"><?=number_format($feed_cost_kg_nae,dec_places_dis(),'.',',')?></td>
								</tr>
								<tr>
									<td style="text-indent: 10%;">Feeds Freight Cost</td>
								<?php
									$feed_freight_cost = 0;
									$get_feed_freight_cost = feed_freight_cost($bc_id, $trans_year, $doctype);
									for ($i=1; $i <= 12 ; $i++){
										$feed_freight_cost = $feed_freight_cost + $get_feed_freight_cost[$i];
								?>
										<td class="text-right"><?=number_format($get_feed_freight_cost[$i],dec_places_dis(),'.',',')?></td>
								<?php
									}
								?>
									<td class="text-right"><?=number_format($feed_freight_cost,dec_places_dis(),'.',',')?></td>
								</tr>
							</tbody>

							<tfoot>
								<tr>
									<th><?=$broiler_group_name?></th>
								<?php
									$feed_cost = 0;
									$harvested_kilo = 0;
									$get_feed_cost = feed_cost($bc_id, $trans_year, $doctype);
									$get_harvested_kilo = harvested_kilo($bc_id, $trans_year, $doctype);
									for ($i=1; $i <= 12 ; $i++){
										$feed_cost = $feed_cost + $get_feed_cost[$i];
										$harvested_kilo = $harvested_kilo + $get_harvested_kilo[$i];
								?>
									<th class="text-right"><?=number_format($get_feed_cost[$i],dec_places_dis(),'.',',')?></th>
								<?php } ?>
									<th class="text-right"><?=$harvested_kilo == 0 ? 0 : number_format($feed_cost_amount/$harvested_kilo,dec_places_dis(),'.',',')?></th>
								</tr>
							</tfoot>
						</table>
						<br>
						<hr>
					</div>
				<?php endif; ?>
				<?php if($broiler_group_id == 2): ?>
					<div class="table-responsive">
						<label>Computation Results</label>
						<table class="table table-hover table-bordered table-stripe nowrap" id="tbl-view-broiler-result">
							<thead>
								<tr>
									<th width="30%">Computation Name</th>
								<?php for ($i=1; $i <= 12 ; $i++){ ?>
									<th width="auto" class="text-center"><?=date('M', strtotime($year.'-'.$i.'-01'))?></th>
								<?php } ?>
									<th class="text-center">Total</th>
								</tr>
							</thead>
							<tbody>
								<tr>
									<td style="text-indent: 5%;">Growers Fee Amount</td>
								<?php
									$growers_fee_amount = 0;
									$get_growers_fee_amount = growers_fee_amount($bc_id, $trans_year, $doctype);
									for ($i=1; $i <= 12 ; $i++){
										$growers_fee_amount = $growers_fee_amount + $get_growers_fee_amount[$i];
								?>
										<td class="text-right"><?=number_format($get_growers_fee_amount[$i],dec_places_dis(),'.',',')?></td>
								<?php
									}
								?>
									<td class="text-right"><?=number_format($growers_fee_amount,dec_places_dis(),'.',',')?></td>
								</tr>
								<tr>
									<td style="text-indent: 10%;">Basic Fee - Regular</td>
								<?php
									$basic_fee = 0;
									$get_basic_fee = basic_fee($bc_id, $trans_year, $doctype);
									for ($i=1; $i <= 12 ; $i++){
										$basic_fee = $basic_fee + $get_basic_fee[$i];
								?>
										<td class="text-right"><?=number_format($get_basic_fee[$i],dec_places_dis(),'.',',')?></td>
								<?php
									}
								?>
									<td class="text-right"><?=number_format($basic_fee,dec_places_dis(),'.',',')?></td>
								</tr>

								<tr>
									<td style="text-indent: 10%;">Basic Fee - NAE</td>
								<?php
									$basic_fee_nae = 0;
									$get_basic_fee_nae = basic_fee_nae($bc_id, $trans_year, $doctype);
									for ($i=1; $i <= 12 ; $i++){
										$basic_fee_nae = $basic_fee_nae + $get_basic_fee_nae[$i];
								?>
										<td class="text-right"><?=number_format($get_basic_fee_nae[$i],dec_places_dis(),'.',',')?></td>
								<?php
									}
								?>
									<td class="text-right"><?=number_format($basic_fee_nae,dec_places_dis(),'.',',')?></td>
								</tr>

								<tr>
									<td style="text-indent: 10%;">HR-Incentive/Penalty - Reg</td>
								<?php
									$hr_incentive = 0;
									$get_hr_incentive = hr_incentive($bc_id, $trans_year, $doctype);
									for ($i=1; $i <= 12 ; $i++){
										$hr_incentive = $hr_incentive + $get_hr_incentive[$i];
								?>
										<td class="text-right"><?=number_format($get_hr_incentive[$i],dec_places_dis(),'.',',')?></td>
								<?php
									}
								?>
									<td class="text-right"><?=number_format($hr_incentive,dec_places_dis(),'.',',')?></td>
								</tr>

								<tr>
									<td style="text-indent: 10%;">HR-Incentive/Penalty - NAE</td>
								<?php
									$hr_incentive_nae = 0;
									$get_hr_incentive_nae = hr_incentive_nae($bc_id, $trans_year, $doctype);
									for ($i=1; $i <= 12 ; $i++){
										$hr_incentive_nae = $hr_incentive_nae + $get_hr_incentive_nae[$i];
								?>
										<td class="text-right"><?=number_format($get_hr_incentive_nae[$i],dec_places_dis(),'.',',')?></td>
								<?php
									}
								?>
									<td class="text-right"><?=number_format($hr_incentive_nae,dec_places_dis(),'.',',')?></td>
								</tr>

								<tr>
									<td style="text-indent: 10%;">FCR-Incentive/Penalty - Old</td>
								<?php
									$fcr_incentive = 0;
									$get_fcr_incentive = fcr_incentive($bc_id, $trans_year, $doctype);
									for ($i=1; $i <= 12 ; $i++){
										$fcr_incentive = $fcr_incentive + $get_fcr_incentive[$i];
								?>
										<td class="text-right"><?=number_format($get_fcr_incentive[$i],dec_places_dis(),'.',',')?></td>
								<?php
									}
								?>
									<td class="text-right"><?=number_format($fcr_incentive,dec_places_dis(),'.',',')?></td>
								</tr>

								<tr>
									<td style="text-indent: 10%;">FCR-Incentive/Penalty - New</td>
								<?php
									$fcr_incentive_new = 0;
									$get_fcr_incentive_new = fcr_incentive_new($bc_id, $trans_year, $doctype);
									for ($i=1; $i <= 12 ; $i++){
										$fcr_incentive_new = $fcr_incentive_new + $get_fcr_incentive_new[$i];
								?>
										<td class="text-right"><?=number_format($get_fcr_incentive_new[$i],dec_places_dis(),'.',',')?></td>
								<?php
									}
								?>
									<td class="text-right"><?=number_format($fcr_incentive_new,dec_places_dis(),'.',',')?></td>
								</tr>

								<tr>
									<td style="text-indent: 10%;">Uniformity Bonus</td>
								<?php
									$uniformity_bonus = 0;
									$get_uniformity_bonus = uniformity_bonus($bc_id, $trans_year, $doctype);
									for ($i=1; $i <= 12 ; $i++){
										$uniformity_bonus = $uniformity_bonus + $get_uniformity_bonus[$i];
								?>
										<td class="text-right"><?=number_format($get_uniformity_bonus[$i],dec_places_dis(),'.',',')?></td>
								<?php
									}
								?>
									<td class="text-right"><?=number_format($uniformity_bonus,dec_places_dis(),'.',',')?></td>
								</tr>
								<tr>
									<td style="text-indent: 10%;">Right Size Bonus</td>
								<?php
									$right_size_bonus = 0;
									$get_right_size_bonus = right_size_bonus($bc_id, $trans_year, $doctype);
									for ($i=1; $i <= 12 ; $i++){
										$right_size_bonus = $right_size_bonus + $get_right_size_bonus[$i];
								?>
										<td class="text-right"><?=number_format($get_right_size_bonus[$i],dec_places_dis(),'.',',')?></td>
								<?php
									}
								?>
									<td class="text-right"><?=number_format($right_size_bonus,dec_places_dis(),'.',',')?></td>
								</tr>

								<tr>
									<td style="text-indent: 10%;">Performance Bonus - Old</td>
								<?php
									$performance_bonus = 0;
									$get_performance_bonus = performance_bonus($bc_id, $trans_year, $doctype);
									for ($i=1; $i <= 12 ; $i++){
										$performance_bonus = $performance_bonus + $get_performance_bonus[$i];
								?>
										<td class="text-right"><?=number_format($get_performance_bonus[$i],dec_places_dis(),'.',',')?></td>
								<?php
									}
								?>
									<td class="text-right"><?=number_format($performance_bonus,dec_places_dis(),'.',',')?></td>
								</tr>

								<tr>
									<td style="text-indent: 10%;">Performance Bonus - New</td>
								<?php
									$performance_bonus_new = 0;
									$get_performance_bonus_new = performance_bonus_new($bc_id, $trans_year, $doctype);
									for ($i=1; $i <= 12 ; $i++){
										$performance_bonus_new = $performance_bonus_new + $get_performance_bonus_new[$i];
								?>
										<td class="text-right"><?=number_format($get_performance_bonus_new[$i],dec_places_dis(),'.',',')?></td>
								<?php
									}
								?>
									<td class="text-right"><?=number_format($performance_bonus_new,dec_places_dis(),'.',',')?></td>
								</tr>

								<tr>
									<td style="text-indent: 10%;">Brooding Incentive</td>
								<?php
									$brooding_incentive = 0;
									$get_brooding_incentive = brooding_incentive($bc_id, $trans_year, $doctype);
									for ($i=1; $i <= 12 ; $i++){
										$brooding_incentive = $brooding_incentive + $get_brooding_incentive[$i];
								?>
										<td class="text-right"><?=number_format($get_brooding_incentive[$i],dec_places_dis(),'.',',')?></td>
								<?php
									}
								?>
									<td class="text-right"><?=number_format($brooding_incentive,dec_places_dis(),'.',',')?></td>
								</tr>
								<tr>
									<td style="text-indent: 10%;">Feeds Efficiency Bonus</td>
								<?php
									$feeds_efficiency_bonus = 0;
									$get_feeds_efficiency_bonus = feeds_efficiency_bonus($bc_id, $trans_year, $doctype);
									for ($i=1; $i <= 12 ; $i++){
										$feeds_efficiency_bonus = $feeds_efficiency_bonus + $get_feeds_efficiency_bonus[$i];
								?>
										<td class="text-right"><?=number_format($get_feeds_efficiency_bonus[$i],dec_places_dis(),'.',',')?></td>
								<?php } ?>
									<td class="text-right"><?=number_format($feeds_efficiency_bonus,dec_places_dis(),'.',',')?></td>
								</tr>

								<tr>
									<td style="text-indent: 10%;">BPI Incentive</td>
								<?php
									$bpi_incentive = 0;
									$get_bpi_incentive = bpi_incentive($bc_id, $trans_year, $doctype);
									for ($i=1; $i <= 12 ; $i++){
										$bpi_incentive = $bpi_incentive + $get_bpi_incentive[$i];
								?>
										<td class="text-right"><?=number_format($get_bpi_incentive[$i],dec_places_dis(),'.',',')?></td>
								<?php } ?>
									<td class="text-right"><?=number_format($bpi_incentive,dec_places_dis(),'.',',')?></td>
								</tr>

								<tr>
									<td style="text-indent: 10%;">BEI Incentive</td>
								<?php
									$bei_incentive = 0;
									$get_bei_incentive = bei_incentive($bc_id, $trans_year, $doctype);
									for ($i=1; $i <= 12 ; $i++){
										$bei_incentive = $bei_incentive + $get_bei_incentive[$i];
								?>
										<td class="text-right"><?=number_format($get_bei_incentive[$i],dec_places_dis(),'.',',')?></td>
								<?php } ?>
									<td class="text-right"><?=number_format($bei_incentive,dec_places_dis(),'.',',')?></td>
								</tr>

								<tr>
									<td style="text-indent: 10%;">Weight Incentive</td>
								<?php
									$weight_incentive = 0;
									$get_weight_incentive = weight_incentive($bc_id, $trans_year, $doctype);
									for ($i=1; $i <= 12 ; $i++){
										$weight_incentive = $weight_incentive + $get_weight_incentive[$i];
								?>
										<td class="text-right"><?=number_format($get_weight_incentive[$i],dec_places_dis(),'.',',')?></td>
								<?php } ?>
									<td class="text-right"><?=number_format($weight_incentive,dec_places_dis(),'.',',')?></td>
								</tr>
								<tr>
									<td style="text-indent: 10%;">Tunnel Vent Incentive</td>
								<?php
									$tunnel_vent_incentive = 0;
									$get_tunnel_vent_incentive = tunnel_vent_incentive($bc_id, $trans_year, $doctype);
									for ($i=1; $i <= 12 ; $i++){
										$tunnel_vent_incentive = $tunnel_vent_incentive + $get_tunnel_vent_incentive[$i];
								?>
										<td class="text-right"><?=number_format($get_tunnel_vent_incentive[$i],dec_places_dis(),'.',',')?></td>
								<?php } ?>
									<td class="text-right"><?=number_format($tunnel_vent_incentive,dec_places_dis(),'.',',')?></td>
								</tr>
								<tr>
									<td style="text-indent: 10%;">Construction Incentive</td>
								<?php
									$construction_incentive = 0;
									$get_construction_incentive = construction_incentive($bc_id, $trans_year, $doctype);
									for ($i=1; $i <= 12 ; $i++){
										$construction_incentive = $construction_incentive + $get_construction_incentive[$i];
								?>
										<td class="text-right"><?=number_format($get_construction_incentive[$i],dec_places_dis(),'.',',')?></td>
								<?php } ?>
									<td class="text-right"><?=number_format($construction_incentive,dec_places_dis(),'.',',')?></td>
								</tr>
								<tr>
									<td style="text-indent: 10%;">Cleaning and Pest-Control Incentive</td>
								<?php
									$cleaning_incentive = 0;
									$get_cleaning_incentive = cleaning_incentive($bc_id, $trans_year, $doctype);
									for ($i=1; $i <= 12 ; $i++){
										$cleaning_incentive = $cleaning_incentive + $get_cleaning_incentive[$i];
								?>
										<td class="text-right"><?=number_format($get_cleaning_incentive[$i],dec_places_dis(),'.',',')?></td>
								<?php } ?>
									<td class="text-right"><?=number_format($cleaning_incentive,dec_places_dis(),'.',',')?></td>
								</tr>
								<tr>
									<td style="text-indent: 10%;">Recruitment Incentive</td>
								<?php
									$recruitment_incentive = 0;
									$get_recruitment_incentive = recruitment_incentive($bc_id, $trans_year, $doctype);
									for ($i=1; $i <= 12 ; $i++){
										$recruitment_incentive = $recruitment_incentive + $get_recruitment_incentive[$i];
								?>
										<td class="text-right"><?=number_format($get_recruitment_incentive[$i],dec_places_dis(),'.',',')?></td>
								<?php } ?>
									<td class="text-right"><?=number_format($recruitment_incentive,dec_places_dis(),'.',',')?></td>
								</tr>
								<tr>
									<td style="text-indent: 10%;">Estimated CGFee Adj.</td>
								<?php
									$estimated_cg_fee_adj = 0;
									$get_estimated_cg_fee_adj = estimated_cg_fee_adj($bc_id, $trans_year, $doctype);
									for ($i=1; $i <= 12 ; $i++){
										$estimated_cg_fee_adj = $estimated_cg_fee_adj + $get_estimated_cg_fee_adj[$i];
								?>
										<td class="text-right"><?=number_format($get_estimated_cg_fee_adj[$i],dec_places_dis(),'.',',')?></td>
								<?php } ?>
									<td class="text-right"><?=number_format($estimated_cg_fee_adj,dec_places_dis(),'.',',')?></td>
								</tr>

								<tr>
									<td style="text-indent: 10%;">Farm Management Incentive</td>
								<?php
									$farm_management_incentive = 0;
									$get_farm_management_incentive = farm_management_incentive($bc_id, $trans_year, $doctype);
									for ($i=1; $i <= 12 ; $i++){
										$farm_management_incentive = $farm_management_incentive + $get_farm_management_incentive[$i];
								?>
										<td class="text-right"><?=number_format($get_farm_management_incentive[$i],dec_places_dis(),'.',',')?></td>
								<?php } ?>
									<td class="text-right"><?=number_format($farm_management_incentive,dec_places_dis(),'.',',')?></td>
								</tr>

								<tr>
									<td style="text-indent: 10%;">NAE Incentive</td>
								<?php
									$nae_incentive = 0;
									$get_nae_incentive = nae_incentive($bc_id, $trans_year, $doctype);
									for ($i=1; $i <= 12 ; $i++){
										$nae_incentive = $nae_incentive + $get_nae_incentive[$i];
								?>
										<td class="text-right"><?=number_format($get_nae_incentive[$i],dec_places_dis(),'.',',')?></td>
								<?php } ?>
									<td class="text-right"><?=number_format($nae_incentive,dec_places_dis(),'.',',')?></td>
								</tr>

								<tr>
									<td style="text-indent: 10%;">NAE Plus Incentive</td>
								<?php
									$nae_plus_incentive = 0;
									$get_nae_plus_incentive = nae_plus_incentive($bc_id, $trans_year, $doctype);
									for ($i=1; $i <= 12 ; $i++){
										$nae_plus_incentive = $nae_plus_incentive + $get_nae_plus_incentive[$i];
								?>
										<td class="text-right"><?=number_format($get_nae_plus_incentive[$i],dec_places_dis(),'.',',')?></td>
								<?php } ?>
									<td class="text-right"><?=number_format($nae_plus_incentive,dec_places_dis(),'.',',')?></td>
								</tr>

								<tr>
									<td style="text-indent: 10%;">Loyalty Incentive</td>
								<?php
									$loyalty_incentive = 0;
									$get_loyalty_incentive = loyalty_incentive($bc_id, $trans_year, $doctype);
									for ($i=1; $i <= 12 ; $i++){
										$loyalty_incentive = $loyalty_incentive + $get_loyalty_incentive[$i];
								?>
										<td class="text-right"><?=number_format($get_loyalty_incentive[$i],dec_places_dis(),'.',',')?></td>
								<?php } ?>
									<td class="text-right"><?=number_format($loyalty_incentive,dec_places_dis(),'.',',')?></td>
								</tr>

								<tr>
									<td style="text-indent: 10%;">Special Loyalty Incentive</td>
								<?php
									$special_loyalty_incentive = 0;
									$get_special_loyalty_incentive = special_loyalty_incentive($bc_id, $trans_year, $doctype);
									for ($i=1; $i <= 12 ; $i++){
										$special_loyalty_incentive = $special_loyalty_incentive + $get_special_loyalty_incentive[$i];
								?>
										<td class="text-right"><?=number_format($get_special_loyalty_incentive[$i],dec_places_dis(),'.',',')?></td>
								<?php } ?>
									<td class="text-right"><?=number_format($special_loyalty_incentive,dec_places_dis(),'.',',')?></td>
								</tr>
								
							</tbody>
							<tfoot>
								<tr>
									<th><?=$broiler_group_name?></th>
								<?php
									$harvested_kilo = 0;
									$growers_fee = 0;
									$get_growers_fee = growers_fee($bc_id, $trans_year, $doctype);
									$get_harvested_kilo = harvested_kilo($bc_id, $trans_year, $doctype);
									for ($i=1; $i <= 12 ; $i++){
										$growers_fee = $growers_fee + $get_growers_fee[$i];
										$harvested_kilo = $harvested_kilo + $get_harvested_kilo[$i];
								?>
									<th class="text-right"><?=number_format($get_growers_fee[$i],dec_places_dis(),'.',',')?></th>
								<?php } ?>
									<th class="text-right"><?=$harvested_kilo == 0 ? 0 : number_format($growers_fee_amount/$harvested_kilo,dec_places_dis(),'.',',')?></th>
								</tr>
							</tfoot>
						</table>
						<br>
						<hr>
					</div>
				<?php endif; ?>
			</div>