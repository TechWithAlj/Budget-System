			
			<div class="col-lg-12" id="content">
				<div id="breadcrumb-div">
					<ul class="breadcrumb">
					    <li><a href="<?=base_url('unit')?>"><span class="glyphicon glyphicon-home"></span>&nbsp;Dashboard</a></li>
					    <li><a href="<?=base_url('unit/opex-info/' . $year)?>">OPEX Info</a></li>
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
						<a href="<?=base_url('unit/add-opex-item/' . $id)?>" class="btn btn-success btn-xs">+ Add Opex Item</a>
					</div>

				<?php endif;?>

				<div class="row">
					<div class="col-lg-3">
						<input type="hidden" id="id" name="id" value="<?=$id?>">
						<input type="hidden" id="cost-center" name="cost_center" value="<?=$cost_center?>">
						<label class="data-info">GL Group: <?=$gl_group?></label>
					</div>

					<div class="col-lg-3">
						<label class="data-info">Budget Year: <?=$year?></label>
					</div>

					<div class="col-lg-3">
						<label class="data-info">Grand Total: <strong><span id="grand-total"></span></strong></label>
					</div>
				</div>

				<div class="row">
					<div class="col-lg-12">
						<div class="table-responsive">
							<table class="table table-bordered" id="tbl-view-capex">
								<thead>
									<tr>
										<th class="text-center" width="60px"></th>
										<th width="7%">GL</th>
										<th width="13%">Cost Center</th>
										<th class="text-center" width="5%">Total</th>
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
									</tr>
								</thead>
								<tbody>

									<?php
										$grand_total = 0;
										foreach($gl_details as $row):
											$grand_total += $row->total_qty;
									?>

										<tr>
											<td>
												<?php if($budget_status == 1):?>
													<a href="" class="remove-opex-item remove" data-id="<?=encode($row->gl_trans_item_id)?>"><span class="fa fa-remove"></span></a>&nbsp;&nbsp;&nbsp;<a href="" class="update update-opex-item" data-id="<?=encode($row->gl_trans_item_id)?>"><span class="fa fa-pencil"></span></a>
												<?php endif;?>
											</td>
											<td><?=$row->gl_sub_name?></td>
											<td><?=$row->cost_center_desc?></td>
											<td class="text-right"><strong><?=number_format($row->total_qty)?></strong></td>
											<td class="text-right"><?=number_format($row->jan)?></td>
											<td class="text-right"><?=number_format($row->feb)?></td>
											<td class="text-right"><?=number_format($row->mar)?></td>
											<td class="text-right"><?=number_format($row->apr)?></td>
											<td class="text-right"><?=number_format($row->may)?></td>
											<td class="text-right"><?=number_format($row->jun)?></td>
											<td class="text-right"><?=number_format($row->jul)?></td>
											<td class="text-right"><?=number_format($row->aug)?></td>
											<td class="text-right"><?=number_format($row->sep)?></td>
											<td class="text-right"><?=number_format($row->oct)?></td>
											<td class="text-right"><?=number_format($row->nov)?></td>
											<td class="text-right"><?=number_format($row->december)?></td>

										</tr>

									<?php endforeach;?>

								</tbody>
							</table>

							<script type="text/javascript">
								var grand_total = '<?=number_format($grand_total)?>';
								$('#grand-total').empty();
								$('#grand-total').text(grand_total);
							</script>

							<div id="modal-remove-opex-item" class="modal fade modal-confirm" role="dialog">
								<div class="modal-dialog modal-sm">
								    <div class="modal-content">
								    	<div class="modal-header">
								        	<button type="button" class="close" data-dismiss="modal">&times;</button>
								       		<strong>Remove Item</strong>
								      	</div>
								      	<form method="POST" action="<?=base_url('unit/remove-opex-item')?>" id="remove-opex-item">
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

							<div id="modal-update-opex-item" class="modal fade modal-confirm" role="dialog">
								<div class="modal-dialog" style="width:1250px;">
								    <div class="modal-content">
								    	<div class="modal-header">
								        	<button type="button" class="close" data-dismiss="modal">&times;</button>
								       		<strong>Update Item</strong>
								      	</div>
								      	<form method="POST" action="<?=base_url('unit/update-opex-item')?>" id="update-opex-item">
								      		<input type="hidden" id="id" name="id[]">
									      	<div class="modal-body">
									      		<div class="row">
										      		<label id="label-gl" class="col-lg-3">GL Group: <span class="val"></span></label>
										      		
										      		<label id="label-total-amount" class="col-lg-3">Total Amount: <span class="val"></span></label>
										      		

										      		<br /><br /><br /><label class="col-lg-1">Cost Center: <span class="val"></span></label>
										      		<div class="col-lg-3">
										      			<select class="form-control input-sm" id="item-cost-center" name="cost_center">
										      				<option>Select..</option>
										      			</select>
										      		</div><br /><br /><br />
										      		
									      		</div>

									      		<div class="row">
									      			<div class="col-lg-12">
														<div class="table-responsive">
												      		<table class="table table-bordered" id="tbl-update-opex">
												      			<thead>
																	<tr>
																		<!-- <th class="text-center"></th> -->
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
																		<!-- <td class=""><a href="#" class="slider-item slider-opex"><span class="fa fa-sliders"></span></a></td> -->
																		<td class=""><input type="text" class="jan-qty update-capex-qty" name="opex[jan][]"></td>
																		<td class=""><input type="text" class="update-capex-qty feb-qty" name="opex[feb][]"></td>
																		<td class=""><input type="text" class="update-capex-qty mar-qty" name="opex[mar][]"></td>
																		<td class=""><input type="text" class="update-capex-qty apr-qty" name="opex[apr][]"></td>
																		<td class=""><input type="text" class="update-capex-qty may-qty" name="opex[may][]"></td>
																		<td class=""><input type="text" class="update-capex-qty jun-qty" name="opex[jun][]"></td>
																		<td class=""><input type="text" class="update-capex-qty jul-qty" name="opex[jul][]"></td>
																		<td class=""><input type="text" class="update-capex-qty aug-qty" name="opex[aug][]"></td>
																		<td class=""><input type="text" class="update-capex-qty sep-qty" name="opex[sep][]"></td>
																		<td class=""><input type="text" class="update-capex-qty oct-qty" name="opex[oct][]"></td>
																		<td class=""><input type="text" class="update-capex-qty nov-qty" name="opex[nov][]"></td>
																		<td class=""><input type="text" class="update-capex-qty dec-qty" name="opex[dec][]"></td>
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
			</div>