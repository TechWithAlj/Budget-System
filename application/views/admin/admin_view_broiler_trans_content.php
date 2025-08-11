			

			<div class="col-lg-12" id="content">
				<div id="breadcrumb-div">
					<ul class="breadcrumb">
					    <li><a href="<?=base_url('admin')?>"><span class="glyphicon glyphicon-home"></span>&nbsp;Dashboard</a></li>
					    <li><a href="<?=base_url('admin/broiler-trans/' . $bc_id.'/'.decode($trans_year))?>">Broiler Transaction</a></li>
					    <li class="active">Broiler Transaction Details (<?=$broiler_group_name?>) - (<?=$bc->bc_name?>)</li>
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
					      		<form method="POST" action="<?=base_url('admin/post-broiler-trans')?>" enctype="multipart/form-data" id="post-broiler-trans">
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
								<?php if($pending_lock_status): ?>
									<td width="3%"></td>
								<?php endif; ?>
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
									<?php if($pending_lock_status): ?>
								<td class="text-center">
									<a href="<?=base_url('admin/edit-broiler-trans/' . encode($row->broiler_trans_id).'/'.encode($row->broiler_subgroup_name).'/'. encode($row->bc_id).'/'. encode($row->broiler_group_id).'/'. encode($row->broiler_group_name).'/'.$trans_year)?>"><i class="fa fa-pencil"></i></a>&nbsp;&nbsp;<a href="" class="cancel-broiler-trans" data-id="<?=encode($row->broiler_trans_id)?>" data-bc="<?=encode($row->bc_id)?>" data-bgid="<?=encode($row->broiler_group_id)?>" data-bgname="<?=encode($row->broiler_group_name)?>" data-transyear="<?=$trans_year?>"><i class="fa fa-remove"></i></a>
								</td>
									<?php endif; ?>
								<?php } else { ?>
								<td class="text-center">
									<a href="" class="post-broiler-trans" data-id="<?=encode($row->broiler_trans_id)?>" data-bc="<?=encode($row->bc_id)?>" data-bgid="<?=encode($row->broiler_group_id)?>" data-bgname="<?=encode($row->broiler_group_name)?>" data-transyear="<?=$trans_year?>"><i class="fa fa-lock"></i></td>
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
					<a href="<?=base_url('admin/view-broiler-summary/' . $bc_id .'/'. $trans_year)?>" class="btn btn-info btn-xs">VIEW BROILER COST SUMMARY</a>
					<a href="<?=base_url('admin/compute-broiler-summary/' . $bc_id .'/'. $trans_year.'/'.$broiler_group_id.'/'.$broiler_group_name)?>" class="btn btn-primary btn-xs">COMPUTE <?=$broiler_group_name?></a>
				</div>
				
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
							
							<?php
							$x = 0;
							$harvested_kilo = 0;
							$harvested_heads = 0;
							$doc_cost_amount = 0;
							$vaccine_amount = 0;
							$medicine_amount = 0;
							$disinfectant_amount = 0;
							$feed_cost_amount = 0;
							$growers_fee_amount = 0;
							$ibc_opex = 0;
							$ext_growes_fee = 0;
							$total = 0;
							
							foreach ($broiler_summary as $summary_row) {

								if($summary_row->broiler_summary_item_id == 34){
									$harvested_kilo = $harvested_kilo + $summary_row->trans_qty;
									$total = $harvested_kilo;
								} else if($summary_row->broiler_summary_item_id == 33){
									$harvested_heads = $harvested_heads + $summary_row->trans_qty;
									$total = $harvested_heads;
								} else if ($summary_row->broiler_summary_item_id == 35){
									$doc_cost_amount = $doc_cost_amount + $summary_row->trans_qty;
									$total = $doc_cost_amount;
								} else if ($summary_row->broiler_summary_item_id == 45){
									$vaccine_amount = $vaccine_amount + $summary_row->trans_qty;
									$total = $vaccine_amount;
								} else if ($summary_row->broiler_summary_item_id == 42){
									$medicine_amount = $medicine_amount + $summary_row->trans_qty;
									$total = $medicine_amount;
								} else if ($summary_row->broiler_summary_item_id == 43){
									$disinfectant_amount = $disinfectant_amount + $summary_row->trans_qty;
									$total = $disinfectant_amount;
								} else if ($summary_row->broiler_summary_item_id == 37){
									$feed_cost_amount = $feed_cost_amount + $summary_row->trans_qty;
									$total = $feed_cost_amount;
								} else if ($summary_row->broiler_summary_item_id == 1){
									$growers_fee_amount = $growers_fee_amount + $summary_row->trans_qty;
									$total = $growers_fee_amount;
								} else if ($summary_row->broiler_summary_item_id == 2){
									$ibc_opex = $ibc_opex + $summary_row->trans_qty;
									$total = $ibc_opex;
								} else if ($summary_row->broiler_summary_item_id == 3){
									$ext_growes_fee = $ext_growes_fee + $summary_row->trans_qty;
									$total = $ext_growes_fee;
								} else {
									$total = $total + $summary_row->trans_qty;
								}

								if($summary_row	->heading_indent == 3){
									$text_indent = 'style="text-indent:10%"';
								} else if($summary_row->heading_indent == 2){
									$text_indent = 'style="text-indent:5%"';
								} else if($summary_row->heading_indent == 4){
									$text_indent = 'style="text-indent:15%"';
								} else {
									$text_indent = '';
								}


								if($x == 0){
									//row header 
									
									if($summary_row->broiler_group_id == $broiler_group_id || $summary_row->broiler_summary_item_id == 34){
										echo '<tr><td '.$text_indent.'>'.$summary_row->broiler_summary_item.'</td>';
										echo '<td class="text-right">'.number_format($summary_row->trans_qty,dec_places_dis(),'.',',').'</td>';
									}


								} else {

									//quantity display
									
									if($summary_row->broiler_group_id == $broiler_group_id || $summary_row->broiler_summary_item_id == 34){
										echo '<td class="text-right">'.number_format($summary_row->trans_qty,dec_places_dis(),'.',',').'</td>';
									}
									
								}
								$x++;
								
								

								
								if($x == 12){
									if($summary_row->broiler_summary_item_id == 36){
										$total = $harvested_kilo == 0 ? 0 : $doc_cost_amount / $harvested_kilo;
									} else if($summary_row->broiler_summary_item_id == 46) {
										$total = $harvested_kilo == 0 ? 0 : $vaccine_amount / $harvested_kilo;
									} else if($summary_row->broiler_summary_item_id == 44) {
										$total = $harvested_kilo == 0 ? 0 : ($medicine_amount + $disinfectant_amount) / $harvested_kilo;
									} else if($summary_row->broiler_summary_item_id == 41) {
										$total = $harvested_kilo == 0 ? 0 : $feed_cost_amount / $harvested_kilo;
									} else if($summary_row->broiler_summary_item_id == 32) {
										$total = $harvested_kilo == 0 ? 0 : $growers_fee_amount / $harvested_kilo;
									} else {
										echo '';
									}
									//total display
									if($summary_row->broiler_group_id == $broiler_group_id || $summary_row->broiler_summary_item_id == 34){
										echo '<td class="text-right">'.number_format($total,dec_places_dis(),'.',',').'</td></tr>';
									}
									$x = 0;
									$total = 0;
									
								}
								
							}
							?>
							</tr>
						</tbody>
					</table>
					<br>
					<hr>
				</div>			
			</div>