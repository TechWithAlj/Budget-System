			
			<div class="col-lg-12" id="content">
				<div id="breadcrumb-div">
					<ul class="breadcrumb">
					    <li><a href="<?=base_url('business-center')?>"><span class="glyphicon glyphicon-home"></span>&nbsp;Dashboard</a></li>
					    <li><a href="<?=base_url('business-center/sales-info/' . $year)?>">Sales Info</a></li>
					    <li class="active">View</li>
					</ul>
				</div>
				<?php
					if($this->session->flashdata('message') != "" ){
						echo $this->session->flashdata('message');
					}
				?>

				<div id="add-btn">
					<?php if($budget_status == 1):?>
					
					<a href="<?=base_url('business-center/add-sales-item/' . encode($id))?>" class="btn btn-success btn-xs">+ Add Sales Item</a>

					<?php endif;?>
				</div>

				<div class="row">
					<div class="col-lg-3">
						<label id="data-info">Outlet Name: <?=$outlet_name . ' (' . $brand_code . ')'?></label>
					</div>
					<div class="col-lg-3">
						<label id="data-info">Business Center: <?=$bc_name?></label>
					</div>

					<div class="col-lg-2">
						<label id="data-info">Budget Year: <?=$year?></label>
					</div>

					<div class="col-lg-2">
						<label id="data-info">Remaining: <span class="sales-remaining"></span></label>
					</div>

					<div class="col-lg-2">
						<label id="data-info">Total: <span class="sales-total"></span></label>
					</div>

				</div>

				<div class="row">
					<input type="hidden" name="id" value="<?=$id?>">
				</div>
				<div class="table-responsive">
					<table class="table table-bordered table-stripe nowrap" id="tbl-sales-view" style="width:100%">
						<thead>
							<tr>
								<th rowspan="2"></th>
								<th rowspan="2">Material Code</th>
								<th rowspan="2">Material Desc</th>
								<th colspan="2" style="width: 10px;" class="text-center">Jan</th>
								<th colspan="2" style="width: 10px;" class="text-center">Feb</th>
								<th colspan="2" style="width: 10px;" class="text-center">Mar</th>
								<th colspan="2" style="width: 10px;" class="text-center">Apr</th>
								<th colspan="2" style="width: 10px;" class="text-center">May</th>
								<th colspan="2" style="width: 10px;" class="text-center">Jun</th>
								<th colspan="2" style="width: 10px;" class="text-center">Jul</th>
								<th colspan="2" style="width: 10px;" class="text-center">Aug</th>
								<th colspan="2" style="width: 10px;" class="text-center">Sep</th>
								<th colspan="2" style="width: 10px;" class="text-center">Oct</th>
								<th colspan="2" style="width: 10px;" class="text-center">Nov</th>
								<th colspan="2" style="width: 10px;" class="text-center">Dec</th>
							</tr>

							<tr>
								<!-- January -->
								<th>QTY</th>
								<th>ASP</th>
								
								<!-- February -->
								<th>QTY</th>
								<th>ASP</th>

								<!-- March -->
								<th>QTY</th>
								<th>ASP</th>

								<!-- April -->
								<th>QTY</th>
								<th>ASP</th>

								<!-- May -->
								<th>QTY</th>
								<th>ASP</th>

								<!-- June -->
								<th>QTY</th>
								<th>ASP</th>

								<!-- July -->
								<th>QTY</th>
								<th>ASP</th>

								<!-- August -->
								<th>QTY</th>
								<th>ASP</th>

								<!-- September -->
								<th>QTY</th>
								<th>ASP</th>

								<!-- October -->
								<th>QTY</th>
								<th>ASP</th>

								<!-- November -->
								<th>QTY</th>
								<th>ASP</th>

								<!-- December -->
								<th>QTY</th>
								<th>ASP</th>										
							</tr>
						</thead>
						
						<tbody>

							<?php
								foreach($material as $row):
									$data_item = explode(',', $row->jan);
									$jan_qty = $data_item[0];
									$jan_asp = $data_item[1];

									$data_item = explode(',', $row->feb);
									$feb_qty = $data_item[0];
									$feb_asp = $data_item[1];

									$data_item = explode(',', $row->mar);
									$mar_qty = $data_item[0];
									$mar_asp = $data_item[1];

									$data_item = explode(',', $row->apr);
									$apr_qty = $data_item[0];
									$apr_asp = $data_item[1];


									$data_item = explode(',', $row->may);
									$may_qty = $data_item[0];
									$may_asp = $data_item[1];

									$data_item = explode(',', $row->jun);
									$jun_qty = $data_item[0];
									$jun_asp = $data_item[1];

									$data_item = explode(',', $row->jul);
									$jul_qty = $data_item[0];
									$jul_asp = $data_item[1];

									$data_item = explode(',', $row->aug);
									$aug_qty = $data_item[0];
									$aug_asp = $data_item[1];

									$data_item = explode(',', $row->sep);
									$sep_qty = $data_item[0];
									$sep_asp = $data_item[1];

									$data_item = explode(',', $row->oct);
									$oct_qty = $data_item[0];
									$oct_asp = $data_item[1];

									$data_item = explode(',', $row->nov);
									$nov_qty = $data_item[0];
									$nov_asp = $data_item[1];

									$data_item = explode(',', $row->december);
									$dec_qty = $data_item[0];
									$dec_asp = $data_item[1];
							?>
							
							<tr>
								<td>
									<?php if($budget_status == 1):?>

									<a href="" class="remove-sales-item remove" data-id="<?=encode($row->sales_item_id)?>"><span class="fa fa-remove"></span></a>&nbsp;&nbsp;&nbsp;<a href="" class="update update-sales-item" data-id="<?=encode($row->sales_item_id)?>"><span class="fa fa-pencil"></span></a>
									
									<?php endif;?>
								</td>

								<td><?=$row->material_code?><input type="hidden" name="material[]" value="<?=encode($row->material_id)?>"></td>
								<td><?=$row->material_desc?></td>

								<!-- January -->
								<td class="budget-td text-right"><?=number_format($jan_qty, 2)?></td>
								<td class="budget-td text-right"><?=number_format($jan_asp, 2)?></td>

								<!-- February -->
								<td class="budget-td text-right"><?=number_format($feb_qty, 2)?></td>
								<td class="budget-td text-right"><?=number_format($feb_asp, 2)?></td>
								

								<!-- March -->
								<td class="budget-td text-right"><?=number_format($mar_qty, 2)?></td>
								<td class="budget-td text-right"><?=number_format($mar_asp, 2)?></td>

								<!-- April -->
								<td class="budget-td text-right"><?=number_format($apr_qty, 2)?></td>
								<td class="budget-td text-right"><?=number_format($apr_asp, 2)?></td>

								<!-- May -->
								<td class="budget-td text-right"><?=number_format($may_qty, 2)?></td>
								<td class="budget-td text-right"><?=number_format($may_asp, 2)?></td>
								
								<!-- June -->
								<td class="budget-td text-right"><?=number_format($jun_qty, 2)?></td>
								<td class="budget-td text-right"><?=number_format($jun_asp, 2)?></td>

								<!-- July -->
								<td class="budget-td text-right"><?=number_format($jul_qty, 2)?></td>
								<td class="budget-td text-right"><?=number_format($jul_asp, 2)?></td>

								<!-- August -->
								<td class="budget-td text-right"><?=number_format($aug_qty, 2)?></td>
								<td class="budget-td text-right"><?=number_format($aug_asp, 2)?></td>

								<!-- September -->
								<td class="budget-td text-right"><?=number_format($sep_qty, 2)?></td>
								<td class="budget-td text-right"><?=number_format($sep_asp, 2)?></td>
								

								<!-- October -->
								<td class="budget-td text-right"><?=number_format($oct_qty, 2)?></td>
								<td class="budget-td text-right"><?=number_format($oct_asp, 2)?></td>
								

								<!-- November -->
								<td class="budget-td text-right"><?=number_format($nov_qty, 2)?></td>
								<td class="budget-td text-right"><?=number_format($nov_asp, 2)?></td>
								

								<!-- December -->
								<td class="budget-td text-right"><?=number_format($dec_qty, 2)?></td>
								<td class="budget-td text-right"><?=number_format($dec_asp, 2)?></td>
							</tr>

							<?php endforeach;?>

						</tbody>
					</table>
				</div>
				<div class="row">
					<!-- <div class="col-lg-12 text-right">
						<button class="btn-budget btn btn-success btn-sm">Submit</button>
					</div> -->
				</div><br /><br />

				<div id="modal-remove-sales-item" class="modal fade" role="dialog">
					<div class="modal-dialog modal-sm">
					    <div class="modal-content">
					    	<div class="modal-header">
					        	<button type="button" class="close" data-dismiss="modal">&times;</button>
					       		<strong>Remove Item</strong>
					      	</div>
					      	<div class="modal-body">
					      		<form method="POST" action="<?=base_url('business-center/remove-sales-item')?>" enctype="multipart/form-data" id="remove-sales-item">
					      			<input type="hidden" name="id" id="id">
						        	<div id="modal-msg" class="text-center">
						        		<label>Are you sure to remove sales item?</label>
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

				<div id="modal-update-sales-item" class="modal fade" role="dialog">
					<div class="modal-dialog" style="width:1250px;">
					    <div class="modal-content">
					    	<div class="modal-header">
					        	<button type="button" class="close" data-dismiss="modal">&times;</button>
					       		<strong>Update Item</strong>
					      	</div>
					      	<form method="POST" action="<?=base_url('business-center/update-sales-item')?>" id="update-sales-item">
					      		<input type="hidden" id="id" name="id[]">
						      	<div class="modal-body">
						      		<div class="row">
							      		<label id="label-code" class="col-lg-3">Material Code: <span class="val"></span></label>
							      		<label id="label-desc" class="col-lg-3">Material Desc: <span class="val"></span></label><br /> <br />
							      		
						      		</div>

						      		<div class="row">
						      			<div class="col-lg-12">
											<div class="table-responsive">
									      		<table class="table table-bordered" id="tbl-update-opex">
									      			<thead>
														<tr>
															<th colspan="2" class="text-center">Jan</th>
															<th colspan="2" class="text-center">Feb</th>
															<th colspan="2" class="text-center">Mar</th>
															<th colspan="2" class="text-center">Apr</th>
															<th colspan="2" class="text-center">May</th>
															<th colspan="2" class="text-center">Jun</th>
															<th colspan="2" class="text-center">Jul</th>
															<th colspan="2" class="text-center">Aug</th>
															<th colspan="2" class="text-center">Sep</th>
															<th colspan="2" class="text-center">Oct</th>
															<th colspan="2" class="text-center">Nov</th>
															<th colspan="2" class="text-center">Dec</th>
														</tr>
														<tr>
															<td>QTY</td>
															<td>ASP</td>

															<td>QTY</td>
															<td>ASP</td>

															<td>QTY</td>
															<td>ASP</td>

															<td>QTY</td>
															<td>ASP</td>

															<td>QTY</td>
															<td>ASP</td>

															<td>QTY</td>
															<td>ASP</td>

															<td>QTY</td>
															<td>ASP</td>

															<td>QTY</td>
															<td>ASP</td>

															<td>QTY</td>
															<td>ASP</td>

															<td>QTY</td>
															<td>ASP</td>

															<td>QTY</td>
															<td>ASP</td>

															<td>QTY</td>
															<td>ASP</td>
														</tr>

													</thead>

													<tbody>
														<tr>
															<td class=""><input type="text" class="update-item-qty jan-qty" name="qty[jan][]"></td>
															<td class=""><input type="text" class="update-item-asp jan-asp" name="asp[jan][]"></td>

															<td class=""><input type="text" class="update-item-qty feb-qty" name="qty[feb][]"></td>
															<td class=""><input type="text" class="update-item-asp feb-asp" name="asp[feb][]"></td>

															<td class=""><input type="text" class="update-item-qty mar-qty" name="qty[mar][]"></td>
															<td class=""><input type="text" class="update-item-asp mar-asp" name="asp[mar][]"></td>

															<td class=""><input type="text" class="update-item-qty apr-qty" name="qty[apr][]"></td>
															<td class=""><input type="text" class="update-item-asp apr-asp" name="asp[apr][]"></td>

															<td class=""><input type="text" class="update-item-qty may-qty" name="qty[may][]"></td>
															<td class=""><input type="text" class="update-item-asp may-asp" name="asp[may][]"></td>

															<td class=""><input type="text" class="update-item-qty jun-qty" name="qty[jun][]"></td>
															<td class=""><input type="text" class="update-item-asp jun-asp" name="asp[jun][]"></td>

															<td class=""><input type="text" class="update-item-qty jul-qty" name="qty[jul][]"></td>
															<td class=""><input type="text" class="update-item-asp jul-asp" name="asp[jul][]"></td>

															<td class=""><input type="text" class="update-item-qty aug-qty" name="qty[aug][]"></td>
															<td class=""><input type="text" class="update-item-asp aug-asp" name="asp[aug][]"></td>


															<td class=""><input type="text" class="update-item-qty sep-qty" name="qty[sep][]"></td>
															<td class=""><input type="text" class="update-item-asp sep-asp" name="asp[sep][]"></td>

															<td class=""><input type="text" class="update-item-qty oct-qty" name="qty[oct][]"></td>
															<td class=""><input type="text" class="update-item-asp oct-asp" name="asp[oct][]"></td>

															<td class=""><input type="text" class="update-item-qty nov-qty" name="qty[nov][]"></td>
															<td class=""><input type="text" class="update-item-asp nov-asp" name="asp[nov][]"></td>

															<td class=""><input type="text" class="update-item-qty dec-qty" name="qty[dec][]"></td>
															<td class=""><input type="text" class="update-item-asp dec-asp" name="asp[dec][]"></td>
														</tr>
													</tbody>
												</table>
											</div>

											<div class="text-right">
									      		<button type="submit" class="btn btn-success btn-update-sales">Update</button>
									      	</div>
										</div>
									</div>
						      	</div>
					      	</form>
					    </div>
					</div>
				</div>
			</div>