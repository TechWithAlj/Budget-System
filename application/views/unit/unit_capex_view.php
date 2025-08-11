			
			<div class="col-lg-12" id="content">
				<div id="breadcrumb-div">
					<ul class="breadcrumb">
					    <li><a href="<?=base_url('unit')?>"><span class="glyphicon glyphicon-home"></span>&nbsp;Dashboard</a></li>
					    <li><a href="<?=base_url('unit/capex-info/')?>">CAPEX Info</a></li>
					    <li class="active">View</li>
					</ul>
				</div>
				<?php
					if($this->session->flashdata('message') != "" ){
						echo $this->session->flashdata('message');
					}
				?>

				<?php if($budget_status == 1):?>

					<div id="add-btn">
						<a href="<?=base_url('unit/add-capex-item/' . $id)?>" class="btn btn-success btn-xs">+ Add Capex Item</a>
					</div>

				<?php endif;?>

				<div class="row">
					<div class="col-lg-3">
						<input type="hidden" id="cost-center" name="bc_cost_center" value="<?=$id?>">
						<label class="data-info">Cost Center: <?=$cost_center_desc?></label>
					</div>

					<div class="col-lg-3">
						<input type="hidden" id="cost-center" name="bc_cost_center" value="<?=$id?>">
						<label class="data-info">Budget Year: <?=$year?></label>
					</div>

					<div class="col-lg-3">
						<label class="data-info">Asset Group: <?=$asset_group?></label>
					</div>

					<div class="col-lg-3">
						<label class="data-info">Grand Total: <span id="opex-grand-total"></span></label>
					</div>

					<br /><br />
				</div>

				<div class="row">
					<div class="col-lg-12">
						<div class="table-responsive">
							<table class="table table-bordered" id="tbl-view-capex">
								<thead>
									<tr>
										<th class="text-center" width=""></th>
										<th width="7%">Asset</th>
										<th width="7%">Cost Center</th>
										<th class="text-center" width="3%">Price</th>
										<th class="text-center" width="3%">Total Price</th>
										<th class="text-center" width="3%">Total Qty</th>
										<th class="text-center" width="">Jan</th>
										<th class="text-center" width="">Feb</th>
										<th class="text-center" width="">Mar</th>
										<th class="text-center" width="">Apr</th>
										<th class="text-center" width="">May</th>
										<th class="text-center" width="">Jun</th>
										<th class="text-center" width="">Jul</th>
										<th class="text-center" width="">Aug</th>
										<th class="text-center" width="">Sep</th>
										<th class="text-center" width="">Oct</th>
										<th class="text-center" width="">Nov</th>
										<th class="text-center" width="">Dec</th>

										<?php if($asset_group == 'TRANSPORTATION EQUIPMENT'):?>
											<th class="text-center" width="">Rank</th>
											<th class="text-center" width="">Remarks</th>
										<?php endif;?>
									</tr>
								</thead>
								<tbody>

									<?php
										$grand_total = 0;
										foreach($asset_details as $row):
											$grand_total += $row->capex_price * $row->total_qty;
									?>

										<tr>
											<td>

												<?php if($budget_status == 1):?>

													<a href="" class="remove remove-trans-item" data-id="<?=encode($row->ag_trans_item_id)?>"><span class="fa fa-remove"></span></a>&nbsp;&nbsp;&nbsp;<a href="" class="update update-trans-item" data-id="<?=encode($row->ag_trans_item_id)?>"><span class="fa fa-pencil"></span></a>

												<?php endif;?>

											</td>
											<td><?=$row->asg_name?></td>
											<td><?=$row->cost_center_desc?></td>
											<td><?=number_format($row->capex_price)?></td>
											<td><?=number_format($row->capex_price * $row->total_qty)?></td>
											<td><?=number_format($row->total_qty)?></td>
											<td><?=number_format($row->jan)?></td>
											<td><?=number_format($row->feb)?></td>
											<td><?=number_format($row->mar)?></td>
											<td><?=number_format($row->apr)?></td>
											<td><?=number_format($row->may)?></td>
											<td><?=number_format($row->jun)?></td>
											<td><?=number_format($row->jul)?></td>
											<td><?=number_format($row->aug)?></td>
											<td><?=number_format($row->sep)?></td>
											<td><?=number_format($row->oct)?></td>
											<td><?=number_format($row->nov)?></td>
											<td><?=number_format($row->december)?></td>

											<?php if($asset_group == 'TRANSPORTATION EQUIPMENT'):?>
												<td><?=$row->rank?></td>
												<td><?=$row->capex_remarks?></td>
											<?php endif;?>

										</tr>

									<?php endforeach;?>

								</tbody>
							</table>
							<script type="text/javascript">
								$('#opex-grand-total').empty();
								$('#opex-grand-total').text('<?=number_format($grand_total, 2)?>');
							</script>
							<div id="modal-remove-trans-item" class="modal fade modal-confirm" role="dialog">
								<div class="modal-dialog modal-sm">
								    <div class="modal-content">
								    	<div class="modal-header">
								        	<button type="button" class="close" data-dismiss="modal">&times;</button>
								       		<strong>Remove Item</strong>
								      	</div>
								      	<form method="POST" action="<?=base_url('unit/remove-capex-item')?>" id="remove-capex-item">
								      		<input type="hidden" id="id" name="id">
									      	<div class="modal-body">
								        		<div class="text-center">
								        			<strong>Are you sure to remove this item?</strong>
								        		</div><br />

								        		<div class="text-center">
								        			<button type=submit class="btn btn-sm btn-success">Yes</button>&nbsp;&nbsp;<a href="" class="btn btn-sm btn-danger" data-dismiss="modal">No</a>
								        		</div>
									      	</div>
								      	</form>
								    </div>
								</div>
							</div>
						</div>

						<div id="modal-update-trans-item" class="modal fade modal-confirm" role="dialog">
								<div class="modal-dialog" style="width:1250px;">
								    <div class="modal-content">
								    	<div class="modal-header">
								        	<button type="button" class="close" data-dismiss="modal">&times;</button>
								       		<strong>Update Item</strong>
								      	</div>
								      	<form method="POST" action="<?=base_url('unit/update-capex-item')?>" id="update-capex-item">
								      		<input type="hidden" id="id" name="id[]">
									      	<div class="modal-body">
									      		<div class="row">
										      		<label id="label-asset" class="col-lg-3">Asset: <span class="val"></span></label>
										      		<label id="label-price" class="col-lg-3">Price: <span class="val"></span></label>
										      		<label id="label-total-qty" class="col-lg-3">Total QTY: <span class="val"></span></label>
										      		<label id="label-total-amount" class="col-lg-3">Total Amount: <span class="val"></span></label>

										      		<br /><br /><br />
										      		
										      		<div class="col-lg-3">
										      			<label>Cost Center: </label>
										      			<select class="form-control input-sm" id="item-cost-center" name="cost_center">
										      				<option value="">Select..</option>
										      			</select>
										      		</div>

										      		<div class="col-lg-3" id="edit-capex-rank">
										      			
										      		</div>

										      		<div class="col-lg-3" id="edit-capex-remarks">
										      			
										      		</div>

										      		<br /><br /><br /><br /><br />
									      		</div>

									      		<div class="row">
									      			<div class="col-lg-12">
														<div class="table-responsive">
												      		<table class="table" id="tbl-update-capex">
												      			<thead>
																	<tr>
																		<th class="text-center">Jan</th>
																		<th class="text-center">Feb</th>
																		<th class="text-center">Mar</th>
																		<th class="text-center">Apr</th>
																		<th class="text-center">May</th>
																		<th class="text-center">Jun</th>
																		<th class="text-center">Jul</th>
																		<th class="text-center">Aug</th>
																		<th class="text-center">Sep</th>
																		<th class="text-center">Oct</th>
																		<th class="text-center">Nov</th>
																		<th class="text-center">Dec</th>
																	</tr>
																</thead>

																<tbody>
																	<tr>
																		<td class=""><input type="text" class="jan-qty update-capex-qty" name="capex[jan][]"></td>
																		<td class=""><input type="text" class="update-capex-qty feb-qty" name="capex[feb][]"></td>
																		<td class=""><input type="text" class="update-capex-qty mar-qty" name="capex[mar][]"></td>
																		<td class=""><input type="text" class="update-capex-qty apr-qty" name="capex[apr][]"></td>
																		<td class=""><input type="text" class="update-capex-qty may-qty" name="capex[may][]"></td>
																		<td class=""><input type="text" class="update-capex-qty jun-qty" name="capex[jun][]"></td>
																		<td class=""><input type="text" class="update-capex-qty jul-qty" name="capex[jul][]"></td>
																		<td class=""><input type="text" class="update-capex-qty aug-qty" name="capex[aug][]"></td>
																		<td class=""><input type="text" class="update-capex-qty sep-qty" name="capex[sep][]"></td>
																		<td class=""><input type="text" class="update-capex-qty oct-qty" name="capex[oct][]"></td>
																		<td class=""><input type="text" class="update-capex-qty nov-qty" name="capex[nov][]"></td>
																		<td class=""><input type="text" class="update-capex-qty dec-qty" name="capex[dec][]"></td>
																	</tr>
																</tbody>
															</table>
														</div>

														<div class="text-right">
												      		<button type="submit" class="btn btn-success">Update</button>
												      	</div>
													</div>
												</div>
									      	</div>
								      	</form>
								    </div>
								</div>
							</div>
						</div>
					</div>
				</div>	
			</div>