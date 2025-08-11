			
			<div class="col-lg-12" id="content">
				<div id="breadcrumb-div">
					<ul class="breadcrumb">
					    <li><a href="<?=base_url('admin')?>"><span class="glyphicon glyphicon-home"></span>&nbsp;Dashboard</a></li>
					    <li><a href="<?=base_url('admin/tactical-price')?>">&nbsp;Tactical Price</a></li>
					    <li class="active">Info</li>
					</ul>
				</div>
				<?php
					if($this->session->flashdata('message') != "" ){
						echo $this->session->flashdata('message');
					}
				?>

				<input type="hidden" value="<?=$id?>" id="cost-center">
				<div id="add-btn">
					<div class="row">
						<div class="col-lg-2">
							<a href="" class="btn btn-success btn-xs" data-toggle="modal" data-target="#modal-add-tactical">+ Add Tactical Price</a>

							<div id="modal-add-tactical" class="modal fade modal-confirm" role="dialog">
								<div class="modal-dialog" style="width:1250px;">
								    <div class="modal-content">
								    	<div class="modal-header">
								        	<button type="button" class="close" data-dismiss="modal">&times;</button>
								       		<strong>Add Tactical Price</strong>
								      	</div>
								      	<div class="modal-body">
								        	<form method="POST" action="<?=base_url('admin/add-tactical-price')?>" enctype="multipart/form-data" id="add-tactical-price">
								        		<input type="hidden" id="id" value="<?=$bc_id?>">
								        		<input type="hidden" name="year" value="<?=$year?>">
								        		<div class="row">
								        			<div class="col-lg-3">
										        		<div class="form-group">
										        			<label>Brand</label>
										        			<select name="brand" id="brand" class="form-control">
										        				<option value="">Select...</option>

											        			<?php foreach($brand as $row):?>

											        				<option value="<?=encode($row->brand_id)?>"><?=$row->brand_name?></option>

											        			<?php endforeach;?>

										        			</select>
										        		</div>
										        	</div>

										        	<div class="col-lg-4">
										        		<div class="form-group">
										        			<label>Outlet</label><br />
										        			<select name="outlet" id="outlet" class="form-control">
										        				<option value="">Select...</option>
										        			</select>
										        		</div>
										        	</div>
										        </div>
								        		
								        		<div class="row">
									        		<div class="col-lg-12">
									        			<div class="table-responsive">
											        		<table class="table table-border" id="tbl-add-tactical">
											        			<thead>
											        				<tr>
											        					<th></th>
											        					<th>Material</th>
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
											        				<!-- <tr>
											        					<td><a href="#" class="slider-tactical" data-count="2"><span class="fa fa-sliders"></span></a></td>
											        					<td><div class="form-group"><input type="text" name="jan_qty" class="tactical-qty"></div></td>

											        					<td><div class="form-group"><input type="text" class="tactical-qty" name="feb_qty"></div></td>

											        					<td><div class="form-group"><input type="text" class="tactical-qty" name="mar_qty"></div></td>

											        					<td><div class="form-group"><input type="text" class="tactical-qty" name="apr_qty"></div></td>

											        					<td><div class="form-group"><input type="text" class="tactical-qty" name="may_qty"></div></td>

											        					<td><div class="form-group"><input type="text" class="tactical-qty" name="jun_qty"></div></td>

											        					<td><div class="form-group"><input type="text" class="tactical-qty" name="jul_qty"></div></td>

											        					<td><div class="form-group"><input type="text" class="tactical-qty" name="aug_qty"></div></td>

											        					<td><div class="form-group"><input type="text" class="tactical-qty" name="sep_qty"></div></td>

											        					<td><div class="form-group"><input type="text" class="tactical-qty" name="oct_qty"></div></td>

											        					<td><div class="form-group"><input type="text" class="tactical-qty" name="nov_qty"></div></td>

											        					<td><div class="form-group"><input type="text" class="tactical-qty" name="dec_qty"></div></td>

											        				</tr> -->
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
							
							<div class="form-group">
								<div class="date">
			                        <div class="input-group input-append date" id="tactical-trans-year">
			                            <input type="text" name="month" id="tactical-year" class="form-control input-sm" placeholder="Pick year" value="<?=$year?>">
			                            <span class="input-group-addon add-on"><span class="glyphicon glyphicon-calendar"></span></span>
			                        </div>
			                    </div>
							</div>
						</div>

						<div class="col-lg-8">
							<div class="text-right">
								<a href="<?=base_url('admin/download-tactical/' . $bc_id . '/' . $year)?>" target="_blank"><span class="glyphicon glyphicon-download-alt"></span>&nbsp;&nbsp; Download Tactical</a>
							</div>
						</div>
					</div>
				</div>

				<label class="data-info">Business Center: <?=$bc_name?></label><br /><br />
				
				<table class="table table-hover" id="tbl-employee">
					<thead>
						<tr>
							<th>Outlet</th>
							<th>Outlet Code</th>
							<th>Brand</th>
							<th>Action</th>
						</tr>
					</thead>
					<tbody>

						<?php
							foreach($tactical as $row):
						?>
						
						<tr>
							<td><?=$row->outlet_name?></td>
							<td><?=$row->ifs_code?></td>
							<td><?=$row->brand_name?></td>
							<td><a href="<?=base_url('admin/tactical-view/' . encode($row->sales_tactical_id))?>" class="btn btn-success btn-xs" data-id="<?=encode($row->sales_tactical_id)?>">View</a>&nbsp;&nbsp;<a href="" class="btn btn-danger btn-xs cancel-tactical" data-id="<?=encode($row->sales_tactical_id)?>">Cancel</a></td>
						</tr>

						<?php endforeach;?>

					</tbody>
				</table>

				<div id="modal-slider-tactical" class="modal fade" role="dialog">
					<div class="modal-dialog modal-sm">
					    <div class="modal-content">
					    	<div class="modal-header">
					        	<button type="button" class="close" data-dismiss="modal">&times;</button>
					       		<strong>Budgeting Slider Tool</strong>
					      	</div>
					      	<div class="modal-body">
				      			<input type="hidden" id="id">
				      			<div class="slider-div">
				      				<div class="form-group">
					      				<label>Tactical Price:</label>
					      				<input type="number" class="form-control input-sm" id="slider-tactical-val">
					      			</div>

						        	<input type="range" min="1" max="300" step="1" value="1" class="slider" id="slider-tactical">
						        </div>
						        <div class="slider-div">
						        	<div class="form-group">
							        	<label>Month Start:&nbsp;&nbsp;<span id="slider-tactical-start-val">1</span></label>
							        	<input type="range" min="1" max="12" value="1" class="slider" id="slider-tactical-start">
							        </div>
						        </div>

						        <div class="slider-div">
						        	<div class="form-group">
							        	<label>Month End:&nbsp;&nbsp;<span id="slider-tactical-end-val">12</span></label>
							        	<input type="range" min="1" max="12" value="12" class="slider" id="slider-tactical-end">
							        </div>
						        </div>
						       
						        <div class="text-right">
						        	<a href="" class="btn btn-info btn-sm slider-tactical-btn">Apply</a>
						        </div>
					        	
					      	</div>
					    </div>
					</div>
				</div>

				<div id="modal-update-tactical" class="modal fade modal-confirm" role="dialog">
					<div class="modal-dialog" style="width:1250px;">
					    <div class="modal-content">
					    	<div class="modal-header">
					        	<button type="button" class="close" data-dismiss="modal">&times;</button>
					       		<strong>Update Tactical Price</strong>
					      	</div>
					      	<div class="modal-body">
					        	<form method="POST" action="<?=base_url('admin/update-tactical-price')?>" enctype="multipart/form-data" id="update-tactical-price">
					        		<input type="hidden" name="id" value="" id="id">

					        		<div class="row">
					        			<div class="col-lg-4">
					        				<label class="data-info">Business Center: <span class="bc-name"></span></label>
					        			</div>
					        			
					        			<div class="col-lg-4">
					        				<label class="data-info">Outlet: <span class="outlet-name"></span></label>
					        			</div>

					        			<div class="col-lg-4">
					        				<label class="data-info">SKU: <span class="sku-name"></span></label>
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

				<div id="modal-confirm-tactical" class="modal fade modal-confirm" role="dialog">
					<div class="modal-dialog modal-sm">
					    <div class="modal-content">
					    	<div class="modal-header">
					        	<button type="button" class="close" data-dismiss="modal">&times;</button>
					       		<strong>Cancel Tactical Price</strong>
					      	</div>
					      	<div class="modal-body">
					      		<form method="POST" action="<?=base_url('admin/cancel-tactical-price')?>" enctype="multipart/form-data" id="cancel-tactical-price">
					      			<input type="hidden" name="id" id="id">
					        		<div class="text-center">
					        			<strong>Are you sure to cancel this tactical price?</strong>
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