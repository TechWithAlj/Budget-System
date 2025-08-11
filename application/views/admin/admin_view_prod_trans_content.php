			
			<div class="col-lg-12" id="content">
				<div id="breadcrumb-div">
					<ul class="breadcrumb">
					    <li><a href="<?=base_url('admin')?>"><span class="glyphicon glyphicon-home"></span>&nbsp;Dashboard</a></li>
					    <?php if(decode($process_type_id) == 5){ ?>
					    <li><a href="<?=base_url('admin/sales-bom-trans/'.$bc_id)?>">Sales BOM Transaction</a></li>
					    <li class="active">View Sales BOM Transaction (<?=$bc->bc_name?>)</li>
					    <?php } else { ?>
					    <li><a href="<?=base_url('admin/prod-trans/'.$bc_id)?>">Production Transaction</a></li>
					    <li class="active">View Production Transaction (<?=$bc->bc_name?>)</li>
					    <?php } ?>
					</ul>
				</div>

				<?php
					if($this->session->flashdata('message') != "" ){
						echo $this->session->flashdata('message');
					}
				?>
				<?php foreach($prod_trans as $row){
					
					echo '<div class="row"><div class="col-12">';
					if(decode($process_type_id) == 5){
						echo '
						<div class="col-md-6">
							<label>Budget Year : '.$year.' | '.decode($prod_trans_id).'</label><br>
							<label>Group Name : '.$row->prod_group_code.' - '.$row->prod_group_name.'</label><br>
							<label>Brand Name : '.$row->brand_name.'</label><br>
						</div>';
					} else {
						echo '
						<div class="col-md-6">
							<label>Budget Year : '.$year.'</label><br>
							<label>Group Name : '.$row->prod_group_code.' - '.$row->prod_group_name.'</label><br>
						</div>';
					}
					echo '
					<div class="col-md-6 add-btn text-right">
						<a href="#" data-toggle="modal" data-target="#modal-production-subgroup" class="btn btn-success btn-xs">+ Add Config Item (Material)</a>&nbsp;&nbsp;<a href="#" data-toggle="modal" data-target="#modal-production-subgroup-services" class="btn btn-success btn-xs">+ Add Config Item (Services)</a>&nbsp;&nbsp;<a href="'.base_url('admin/resync_prod_trans_dtl/' .encode($row->config_prod_id).'/'.$prod_trans_id.'/'.encode($year)).'/'.$bc_id.'/'.$process_type_id.'" class="btn btn-primary btn-xs">Resync Config Items</a>
					</div>';
					echo '</div></div>';
					break;
				}?>
				<div id="modal-production-subgroup" class="modal fade modal-confirm" role="dialog">
					<div class="modal-dialog modal-sm">
						<div class="modal-content">
							<div class="modal-header">
								<button type="button" class="close" data-dismiss="modal">&times;</button>
								<strong>Add Config Item (Material)</strong>
							</div>
							<div class="modal-body">
								<form method="POST" action="<?=base_url('admin/add-config-prod-dtl')?>" enctype="multipart/form-data" id="add-material">
									<input type="hidden" name="config_prod_id" value="<?=encode($config_prod_id)?>">
									<input type="hidden" name="article_type_id" value="<?=encode(1)?>">
									<input type="hidden" name="process_type_id" id="process_type_id" value="<?=$process_type_id?>">
									<input type="hidden" name="for_sales_bom_auto_add" value="<?=encode('auto_add')?>">
									<input type="hidden" name="prod_trans_id" value="<?=$prod_trans_id?>">
									<input type="hidden" name="year" value="<?=encode($year)?>">
									<input type="hidden" name="bc_id" value="<?=$bc_id?>">
									
									
									<div class="form-group">
										<label>Component Type:</label>
										<select class="form-control" name="component_type_id" id="component_type_id">
											<option value="">Select...</option>

											<?php
											foreach($component_type as $row):
												if($row->component_classification == 1 && decode($process_type_id) != 5 && $row->component_type_id <= 6 || $row->component_type_id == 22){
													
											?>
													<option value="<?=encode($row->component_type_id)?>"><?=$row->component_type?></option>
											<?php
													
												} else if($row->component_classification == 1 && decode($process_type_id) == 5 && $row->component_type_id > 6){
											?>
													<option value="<?=encode($row->component_type_id)?>"><?=$row->component_type?></option>
											<?php
												}
											endforeach;
											?>
										</select>
									</div>

									<div class="form-group">
										<label>Item:</label>
										<select class="form-control" name="article_id[]" id="article_id">
											<option value="">Select...</option>

										</select>
									</div>

									<div class="form-group">
										<label>Amount Type:</label>
										<select class="form-control" name="amount_type_id" id="amount_type_id">
											<option value="">Select...</option>

											<?php foreach($amount_type as $row):?>

											<option value="<?=encode($row->amount_type_id)?>"><?=$row->amount_type_name?></option>

											<?php endforeach;?>

										</select>
									</div>

									<div class="form-group">
										<label>Show on Trans:</label>
										<select class="form-control" name="show_on_trans" id="show_on_trans">
											<option value="">Select...</option>
											<option value="<?=encode(1)?>">YES</option>
											<option value="<?=encode(2)?>">NO</option>

										</select>
									</div>
									
									<div class="btn-update">
										<button type="submit" class="btn btn-info btn-sm pull-right">Add</button><br>
									</div>
								</form>
							</div>
						</div>
					</div>
				</div>

				<div id="modal-production-subgroup-services" class="modal fade modal-confirm" role="dialog">
					<div class="modal-dialog modal-sm">
						<div class="modal-content">
							<div class="modal-header">
								<button type="button" class="close" data-dismiss="modal">&times;</button>
								<strong>Add Config Item (Services)</strong>
							</div>
							<div class="modal-body">
								<form method="POST" action="<?=base_url('admin/add-config-prod-dtl')?>" enctype="multipart/form-data" id="add-material">
									<input type="hidden" name="config_prod_id" value="<?=encode($config_prod_id)?>">
									<input type="hidden" name="article_type_id" value="<?=encode(2)?>">
									<input type="hidden" name="process_type_id" id="process_type_id" value="<?=$process_type_id?>">
									<input type="hidden" name="for_sales_bom_auto_add" value="<?=encode('auto_add')?>">
									<input type="hidden" name="prod_trans_id" value="<?=$prod_trans_id?>">
									<input type="hidden" name="year" value="<?=encode($year)?>">
									<input type="hidden" name="bc_id" value="<?=$bc_id?>">

									<div class="form-group">
										<label>Component Type:</label>
										<select class="form-control" name="component_type_id" id="component_type_id_svc">
											<option value="">Select...</option>
											<?php
											foreach($component_type as $row):
												if($row->component_classification == 2 && decode($process_type_id) != 5 && $row->component_type_id <= 7){
													
											?>
													<option value="<?=encode($row->component_type_id)?>"><?=$row->component_type?></option>
											<?php
													
												} else if($row->component_classification == 2 && decode($process_type_id) == 5 && $row->component_type_id > 7){
											?>
													<option value="<?=encode($row->component_type_id)?>"><?=$row->component_type?></option>
											<?php
												}
											endforeach;
											?>
										</select>
									</div>

									<div class="form-group">
										<label>Item:</label>
										<select class="form-control" name="article_id[]" id="article_id_svc">
											<option value="">Select...</option>

										</select>
									</div>

									<div class="form-group">
										<label>Amount Type:</label>
										<select class="form-control" name="amount_type_id" id="amount_type_id">
											<option value="">Select...</option>

											<?php foreach($amount_type as $row):?>

											<option value="<?=encode($row->amount_type_id)?>"><?=$row->amount_type_name?></option>

											<?php endforeach;?>

										</select>
									</div>

									<div class="form-group">
										<label>Show on Trans:</label>
										<select class="form-control" name="show_on_trans" id="show_on_trans">
											<option value="">Select...</option>
											<option value="<?=encode(1)?>">YES</option>
											<option value="<?=encode(2)?>">NO</option>

										</select>
									</div>
									
									<div class="btn-update">
										<button type="submit" class="btn btn-info btn-sm pull-right">Add</button><br>
									</div>
								</form>
							</div>
						</div>
					</div>
				</div>
				<div class="table-responsive">
					<input type="hidden" id="lock_status" value="<?=$pending_lock_status;?>">
					<table class="table table-hover table-bordered table-stripe nowrap" id="tbl-new-prod-transaction">
						<thead id="here">
							<tr>
								<th rowspan="2"></th>
								<th rowspan="2" width="30%">Item Name</th>
								<th rowspan="2" width="30%">Item Code</th>
								<th rowspan="2" width="30%">Val. Unit</th>
								<th rowspan="2" width="10%">Component</th>
								<?php for ($i=1; $i <= 12 ; $i++){ ?>
								<th class="text-center" colspan="2"><?=date('M', strtotime($year.'-'.$i.'-01'))?></th>
								<?php } ?>
							</tr>
							<tr>
								
								<?php
								foreach($prod_trans as $row){
									for ($i=1; $i <= 12 ; $i++){
										if($row->process_type_id == 2){
								?>
											<th class="text-center">Rate</th>
											<th class="text-center">Ave. Wgt</th>
								<?php
										} else {
								?>
											<th class="text-center">Rate</th>
											<th class="text-center">Cost/Price</th>
								<?php
										}
									}
									break;
								}
								?>
							</tr>
						</thead>
						<tbody>
							
							<?php
							$prod_trans_output = '';
							$count = 1;
							foreach($prod_trans as $row){
								$prod_trans_output .= '<tr><input type="hidden" name="prod_trans_output_dtl_id[]" value="' . encode($row->prod_trans_dtl_id) . '">';
								if($row->prod_trans_dtl_status == 1 && $pending_lock_status){
									$prod_trans_output .= "<td class='text-center'><a href='".base_url('admin/edit-prod-trans/' . encode($row->prod_trans_id).'/'.encode($row->config_prod_dtl_id).'/'.$bc_id.'/'.encode($year).'/'.$process_type_id)."'><i class='fa fa-pencil'></i></a></td>";
								} else {
									$prod_trans_output .= '<td>' .$count .'</td>';
								}
								
								$prod_trans_output .= '<td>' .$row->material_desc .'</td>';
								$prod_trans_output .= '<td>' .$row->material_code .'</td>';
								$prod_trans_output .= '<td>' .$row->unit_name .'</td>';
								$prod_trans_output .= '<td>' . $row->component_type .'</td>';

								$rate_array = get_bulk_yearly_data('prod_trans_dtl_tbl a', array('a.config_prod_dtl_id' => $row->config_prod_dtl_id, 'a.prod_trans_id' => $row->prod_trans_id, 'YEAR(a.prod_trans_dtl_date)' => $year), true, 'rate');
                                $cost_array = get_bulk_yearly_data('prod_trans_dtl_tbl a', array('a.config_prod_dtl_id' => $row->config_prod_dtl_id, 'a.prod_trans_id' => $row->prod_trans_id, 'YEAR(a.prod_trans_dtl_date)' => $year), true, 'cost');
								$ave_wgt_array = get_bulk_yearly_data('prod_trans_dtl_tbl a', array('a.config_prod_dtl_id' => $row->config_prod_dtl_id, 'a.prod_trans_id' => $row->prod_trans_id, 'YEAR(a.prod_trans_dtl_date)' => $year), true, 'ave_wgt');

								for ($i=1; $i <=12 ; $i++) {
									
									$rate = @$rate_array[$i] ? ($rate_array[$i] > 0 ? number_format($rate_array[$i], 6, '.', ',') : '' ) : '';
                                    $cost = @$cost_array[$i] ? ($cost_array[$i] > 0 ? number_format($cost_array[$i], 3, '.', ',') : '' ) : '';
									$ave_wgt = @$ave_wgt_array[$i] ? number_format($ave_wgt_array[$i], 3, '.', ',') : '';

									$month = date('M', strtotime($year.'-'.$i.'-01'));
									$prod_trans_output .= "<td class='text-center'>".$rate."</td>";
									if ($row->process_type_id == 2) { //CLASSIFICATION
										$prod_trans_output .= "<td class='text-center'>".$ave_wgt."</td>";
									} else {
										$prod_trans_output .= "<td class='text-center'>".$cost."</td>";
									}
								}
								
								$prod_trans_output .= '</tr>';
								$count++;
							}
							?>
							<?=$prod_trans_output?>
						</tbody>
					</table>
				</div>
				<br><br>
			</div>