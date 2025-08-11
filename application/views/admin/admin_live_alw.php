			
			<div class="col-lg-12" id="content">
				<div id="breadcrumb-div">
					<ul class="breadcrumb">
					    <li><a href="<?=base_url('admin')?>"><span class="glyphicon glyphicon-home"></span>&nbsp;Dashboard</a></li>
					    <li class="active">ALW for Live Sales</li>
					</ul>
				</div>
				<?php
					if($this->session->flashdata('message') != "" ){
						echo $this->session->flashdata('message');
					}
				?>
				<div id="add-btn">

					<div class="row">
						<div class="col-lg-2">
							<a href="" class="btn btn-success btn-xs" data-toggle="modal" data-target="#modal-alw">+ Add ALW for Live Sales</a>

							<div id="modal-alw" class="modal fade modal-confirm" role="dialog">
								<div class="modal-dialog" style="width:1250px;">
								    <div class="modal-content">
								    	<div class="modal-header">
								        	<button type="button" class="close" data-dismiss="modal">&times;</button>
								       		<strong>Add ALW for Live Sales</strong>
								      	</div>
								      	<div class="modal-body">
								        	<form method="POST" action="<?=base_url('admin/add-live-alw')?>" enctype="multipart/form-data" id="add-live-alw">

								        		<div class="row">
								        			<div class="col-lg-4">
										        		<div class="form-group">
										        			<label>Business Center</label>
										        			<select name="bc" id="bc" class="form-control">
										        				<option value="">Select...</option>

											        			<?php foreach($bc as $row):?>

											        				<option value="<?=encode($row->bc_id)?>"><?=$row->bc_name?></option>

											        			<?php endforeach;?>

										        			</select>
										        		</div>
										        	</div>
										        </div>
								        		
								        		<div class="row">
									        		<div class="col-lg-12">
									        			<div class="table-responsive">
											        		<table class="table table-border" id="tbl-alw">
											        			<thead>
											        				<tr>
											        					<!-- <th></th> -->
												        				<th>Jan</th>
												        				<th>Feb</th>
												        				<th>Mar</th>
												        				<th>Apr</th>
												        				<th>May</th>
												        				<th>Jun</th>
												        				<th>Jul</th>
												        				<th>Aug</th>
												        				<th>Sep</th>
												        				<th>Oct</th>
												        				<th>Nov</th>
												        				<th>Dec</th>
												        			</tr>
											        			</thead>
											        			<tbody>
											        				<tr>
											        					<!-- <td><a href="#" class="slider-opex" data-count="2"><span class="fa fa-sliders"></span></a></td> -->
											        					<td><div class="form-group"><input type="text" name="jan_qty"></div></td>

											        					<td><div class="form-group"><input type="text" class="alw-qty" name="feb_qty"></div></td>

											        					<td><div class="form-group"><input type="text" class="alw-qty" name="mar_qty"></div></td>

											        					<td><div class="form-group"><input type="text" class="alw-qty" name="apr_qty"></div></td>

											        					<td><div class="form-group"><input type="text" class="alw-qty" name="may_qty"></div></td>

											        					<td><div class="form-group"><input type="text" class="alw-qty" name="jun_qty"></div></td>

											        					<td><div class="form-group"><input type="text" class="alw-qty" name="jul_qty"></div></td>

											        					<td><div class="form-group"><input type="text" class="alw-qty" name="aug_qty"></div></td>

											        					<td><div class="form-group"><input type="text" class="alw-qty" name="sep_qty"></div></td>

											        					<td><div class="form-group"><input type="text" class="alw-qty" name="oct_qty"></div></td>

											        					<td><div class="form-group"><input type="text" class="alw-qty" name="nov_qty"></div></td>

											        					<td><div class="form-group"><input type="text" class="alw-qty" name="dec_qty"></div></td>

											        				</tr>
											        			</tbody>
											        		</table>
											        	</div>
										        	</div>
										        </div>


								        		<div class="btn-update">
								        			<button type="submit" class="btn btn-info btn-sm pull-right">Add</button><br>
								        		</div>
								        	</form>
								      	</div>
								    </div>
								</div>
							</div>
						</div>

						<div class="col-lg-2">
							<div class="date">
		                        <div class="input-group input-append date" id="alw-trans-year">
		                            <input type="text" name="month" id="alw-year" class="form-control input-sm" placeholder="Pick year" value="<?=$year?>">
		                            <span class="input-group-addon add-on"><span class="glyphicon glyphicon-calendar"></span></span>
		                        </div>
		                    </div>
						</div>

						<div class="col-lg-8">
							<div class="text-right">
								<a href="<?=base_url('admin/download-alw/' . $year)?>" target="_blank"><span class="glyphicon glyphicon-download-alt"></span>&nbsp;&nbsp; Download ALW</a>
							</div>
						</div>
					</div>
				</div>
				
				<table class="table table-hover" id="tbl-view-alw">
					<thead>
						<tr>
							<th>BC</th>
							<th>Jan</th>
							<th>Feb</th>
							<th>Mar</th>
							<th>Apr</th>
							<th>May</th>
							<th>Jun</th>
							<th>Jul</th>
							<th>Aug</th>
							<th>Sep</th>
							<th>Oct</th>
							<th>Nov</th>
							<th>Dec</th>
							<th>Action</th>
						</tr>
					</thead>
					<tbody>

						<?php
							foreach($alw as $row):
						?>
						
						<tr>
							<td><?=$row->bc_name?></td>
							<td><?=$row->alw_jan?></td>
							<td><?=$row->alw_feb?></td>
							<td><?=$row->alw_mar?></td>
							<td><?=$row->alw_apr?></td>
							<td><?=$row->alw_may?></td>
							<td><?=$row->alw_jun?></td>
							<td><?=$row->alw_jul?></td>
							<td><?=$row->alw_aug?></td>
							<td><?=$row->alw_sep?></td>
							<td><?=$row->alw_oct?></td>
							<td><?=$row->alw_nov?></td>
							<td><?=$row->alw_dec?></td>
							<td><a href="" class="btn btn-success btn-xs edit-alw" data-id="<?=encode($row->sales_live_alw_id)?>">View</a>&nbsp;&nbsp;<a href="" class="btn btn-danger btn-xs cancel-alw" data-id="<?=encode($row->sales_live_alw_id)?>">Cancel</a></td>
						</tr>

						<?php endforeach;?>

					</tbody>
				</table>


				<div id="modal-update-alw" class="modal fade modal-confirm" role="dialog">
					<div class="modal-dialog" style="width:1250px;">
					    <div class="modal-content">
					    	<div class="modal-header">
					        	<button type="button" class="close" data-dismiss="modal">&times;</button>
					       		<strong>Update ALW for Live Sales</strong>
					      	</div>
					      	<div class="modal-body">
					        	<form method="POST" action="<?=base_url('admin/update-live-alw')?>" enctype="multipart/form-data" id="update-live-alw">
					        		<input type="hidden" name="id" value="" id="id">
					        		<div class="row">
						        		<div class="col-lg-12">
						        			<div class="table-responsive">
								        		<table class="table table-border" id="tbl-alw">
								        			<thead>
								        				<tr>
								        					<!-- <th></th> -->
									        				<th>Jan</th>
									        				<th>Feb</th>
									        				<th>Mar</th>
									        				<th>Apr</th>
									        				<th>May</th>
									        				<th>Jun</th>
									        				<th>Jul</th>
									        				<th>Aug</th>
									        				<th>Sep</th>
									        				<th>Oct</th>
									        				<th>Nov</th>
									        				<th>Dec</th>
									        			</tr>
								        			</thead>
								        			<tbody>
								        				<tr>
								        					<!-- <td><a href="#" class="slider-opex" data-count="2"><span class="fa fa-sliders"></span></a></td> -->
								        					<td><div class="form-group"><input type="text" name="jan_qty" id="jan_qty"></div></td>

								        					<td><div class="form-group"><input type="text" class="alw-qty" name="feb_qty" id="feb_qty"></div></td>

								        					<td><div class="form-group"><input type="text" class="alw-qty" name="mar_qty" id="mar_qty"></div></td>

								        					<td><div class="form-group"><input type="text" class="alw-qty" name="apr_qty" id="apr_qty"></div></td>

								        					<td><div class="form-group"><input type="text" class="alw-qty" name="may_qty" id="may_qty"></div></td>

								        					<td><div class="form-group"><input type="text" class="alw-qty" name="jun_qty" id="jun_qty"></div></td>

								        					<td><div class="form-group"><input type="text" class="alw-qty" name="jul_qty" id="jul_qty"></div></td>

								        					<td><div class="form-group"><input type="text" class="alw-qty" name="aug_qty" id="aug_qty"></div></td>

								        					<td><div class="form-group"><input type="text" class="alw-qty" name="sep_qty" id="sep_qty"></div></td>

								        					<td><div class="form-group"><input type="text" class="alw-qty" name="oct_qty" id="oct_qty"></div></td>

								        					<td><div class="form-group"><input type="text" class="alw-qty" name="nov_qty" id="nov_qty"></div></td>

								        					<td><div class="form-group"><input type="text" class="alw-qty" name="dec_qty" id="dec_qty"></div></td>

								        				</tr>
								        			</tbody>
								        		</table>
								        	</div>
							        	</div>
							        </div>


					        		<div class="btn-update">
					        			<button type="submit" class="btn btn-info btn-sm pull-right">Add</button><br>
					        		</div>
					        	</form>
					      	</div>
					    </div>
					</div>
				</div>

				<div id="modal-confirm-alw" class="modal fade modal-confirm" role="dialog">
					<div class="modal-dialog modal-sm">
					    <div class="modal-content">
					    	<div class="modal-header">
					        	<button type="button" class="close" data-dismiss="modal">&times;</button>
					       		<strong>Cancel ALW</strong>
					      	</div>
					      	<div class="modal-body">
					      		<form method="POST" action="<?=base_url('admin/cancel-live-alw')?>" enctype="multipart/form-data" id="cancel-live-alw">
					      			<input type="hidden" name="id" id="id">
					        		<div class="text-center">
					        			<strong>Are you sure to cancel this ALW?</strong>
					        		</div><br />

					        		<div class="text-center">
					        			<button type=submit class="btn btn-sm btn-success" id="save-capex">Yes</button>&nbsp;&nbsp;<a href="" class="btn btn-sm btn-danger" data-dismiss="modal">No</a>
					        		</div>
					        	</form>
					      	</div>
					    </div>
					</div>
				</div>

			</div>