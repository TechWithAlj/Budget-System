			
			<div class="col-lg-12" id="content">
				<div id="breadcrumb-div">
					<ul class="breadcrumb">
					    <li><a href="<?=base_url('admin')?>"><span class="glyphicon glyphicon-home"></span>&nbsp;Dashboard</a></li>
					    <li class="active">Employees</li>
					</ul>
				</div>
				<?php
					if($this->session->flashdata('message') != "" ){
						echo $this->session->flashdata('message');
					}
				?>

				<div id="add-btn">
					<div class="row">
						<div class="col-lg-3">
							<a href="" class="btn btn-success btn-xs" data-toggle="modal" data-target="#modal-manpower">+ Add Manpower</a>

							<div id="modal-manpower" class="modal fade modal-confirm" role="dialog">
								<div class="modal-dialog modal-sm">
								    <div class="modal-content">
								    	<div class="modal-header">
								        	<button type="button" class="close" data-dismiss="modal">&times;</button>
								       		<strong>Add Manpower</strong>
								      	</div>
								      	<div class="modal-body">
								        	<form method="POST" action="<?=base_url('admin/add-manpower')?>" enctype="multipart/form-data" id="add-manpower">
								        		<input type="hidden" name="year" value="<?=$year?>">

								        		<div class="form-group">
								        			<label>Type:</label>
								        			<select name="type" id="manpower-type" class="form-control">
								        				<option value="">Select...</option>
								        				<option value="bc">Business Center</option>
									        			<option value="sc">Support Center</option>
								        			</select>
								        		</div>

								        		<div class="form-group hide" id="div-manpower-bc">
								        			<label>Business Center:</label>
								        			<select name="bc" id="manpower-bc" class="form-control">
								        				<option value="">Select...</option>

								        				<?php foreach($bc as $row_bc):?>

									        				<option value="<?=encode($row_bc->bc_id)?>"><?=$row_bc->bc_name?></option>

									        			<?php endforeach;?>
								        			</select>
								        		</div>

								        		<div class="form-group hide" id="div-manpower-unit">
								        			<label>Unit:</label>
								        			<select name="sc" id="manpower-unit" class="form-control">
								        				<option value="">Select...</option>

								        				<?php foreach($unit as $row_unit):?>

									        				<option value="<?=encode($row_unit->company_unit_id)?>"><?=$row_unit->company_unit_name?></option>

									        			<?php endforeach;?>
								        			</select>
								        		</div>

								        		<div class="form-group">
								        			<label>Cost Center</label>
								        			<select name="cost_center" id="manpower-cost-center" class="form-control">
								        				
								        				<option value="">Select...</option>

								        			</select>
								        		</div>

								        		<div class="form-group">
								        			<label>Rank</label>
								        			<select name="rank" id="manpower-rank" class="form-control">
								        				
								        				<option value="">Select...</option>

								        				<?php foreach($rank as $row_rank):?>

									        				<option value="<?=encode($row_rank->rank_id)?>"><?=$row_rank->rank_name?></option>

									        			<?php endforeach;?>

								        			</select>
								        		</div>

								        		<div class="form-group">
								        			<label>Position</label>
								        			<input type="text" class="form-control input-sm" name="position">
								        		</div>

								        		<div class="form-group">
								        			<label>Old Manpower</label>
								        			<input type="text" class="form-control input-sm" name="old">
								        		</div>

								        		<div class="form-group">
								        			<label>New Manpower</label>
								        			<input type="text" class="form-control input-sm" name="new">
								        		</div>
								        		<div class="form-group">
								        			<label>Remarks</label>
								        			<textarea class="form-control input-sm" name="remarks" rows="3"></textarea>
								        		</div>
								        		<div class="form-group hide" id="bc-old">
								        			<label>BC Old Manpower</label>
								        			<input type="text" class="form-control input-sm" name="bc-old">
								        		</div>

								        		<div class="form-group hide" id="bc-new">
								        			<label>BC New Manpower</label>
								        			<input type="text" class="form-control input-sm" name="bc-new">
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
			                        <div class="input-group input-append date" id="manpower-trans-year">
			                            <input type="text" name="month" id="manpower-year" class="form-control input-sm" placeholder="Pick year" value="<?=$year?>">
			                            <span class="input-group-addon add-on"><span class="glyphicon glyphicon-calendar"></span></span>
			                        </div>
			                    </div>
							</div>
						</div>

						<div class="col-lg-7">
							<div class="text-right">
								<a href="<?=base_url('admin/download-manpower/' . $year)?>" target="_blank"><span class="glyphicon glyphicon-download-alt"></span>&nbsp;&nbsp; Download Manpower</a>
							</div>

						</div>
					</div>

					<div id="modal-cancel-emp" class="modal fade modal-confirm" role="dialog">
						<div class="modal-dialog modal-sm">
						    <div class="modal-content">
						    	<div class="modal-header">
						        	<button type="button" class="close" data-dismiss="modal">&times;</button>
						       		<strong>Cancel Manpower</strong>
						      	</div>
						      	<div class="modal-body">
						      		<form method="POST" id="cancel-employee" action="<?=base_url('admin/cancel-manpower/')?>">
						      			<input type="hidden" name="id" id="id">
						        		<div class="text-center">
						        			<strong>Are you sure to cancel this manpower?</strong>
						        		</div><br />

						        		<div class="text-center">
						        			<button type=submit class="btn btn-sm btn-success" id="save-opex">Yes</button>&nbsp;&nbsp;<button class="btn btn-sm btn-danger" data-dismiss="modal">No</button>
						        		</div>
						        	</form>
						      	</div>
						    </div>
						</div>
					</div>
				</div>
				
				<table class="table table-hover" id="tbl-employee">
					<thead>
						<tr>
							<th>Business Center</th>
							<th>Unit</th>
							<th>Cost Center Code</th>
							<th>Cost Center Name</th>
							<th>Rank</th>
							<th>Position</th>
							<th>Old</th>
							<th>New</th>
							<th>BC Old</th>
							<th>BC New</th>
							<th>Year</th>
							<th>Remarks</th>
							<th>Action</th>
						</tr>
					</thead>
					<tbody>

						<?php
							foreach($manpower as $row):
						?>
						
						<tr>
							<td><?=$row->bc?></td>
							<td><?=$row->company_unit_name?></td>
							<td><?=$row->cost_center_code?></td>
							<td><?=$row->cost_center_desc?></td>
							<td><?=$row->rank_name?></td>
							<td><?=$row->manpower_position?></td>
							<td><?=$row->manpower_old?></td>
							<td><?=$row->manpower_new?></td>
							<td><?=$row->manpower_bc_old?></td>
							<td><?=$row->manpower_bc_new?></td>
							<td><?=$row->manpower_year?></td>
							<td><?=$row->manpower_remarks?></td>
							<td><a href="" class="btn btn-success btn-xs edit-manpower" data-id="<?=encode($row->manpower_id)?>">View</a>&nbsp;&nbsp;<a href="" class="btn btn-danger btn-xs remove-manpower" data-id="<?=encode($row->manpower_id)?>">Cancel</a>&nbsp;&nbsp;</td>
						</tr>

						<?php endforeach;?>

					</tbody>
				</table>

				<div id="modal-edit-manpower" class="modal fade modal-confirm" role="dialog">
					<div class="modal-dialog modal-sm">
					    <div class="modal-content">
					    	<div class="modal-header">
					        	<button type="button" class="close" data-dismiss="modal">&times;</button>
					       		<strong>Update Manpower</strong>
					      	</div>
					      	<div class="modal-body">
					        	<form method="POST" action="<?=base_url('admin/update-manpower')?>" enctype="multipart/form-data" id="update-manpower">
					        		<input type="hidden" name="id" id="id">
					        		
					        		<div class="form-group">
					        			<label>Cost Center: <span id="edit-manpower-cost-center"></span></label>
					        		</div>

					        		<div class="form-group">
					        			<label>Rank:</label>
					        			<select name="rank" id="edit-manpower-rank" class="form-control">
					        				<option value="">Select Rank...</option>
					        			</select>
					        		</div>

					        		<div class="form-group">
					        			<label>Position</label>
					        			<input type="text" class="form-control input-sm" name="position" id="edit-manpower-position">
					        		</div>

					        		<div class="form-group">
					        			<label>Old Manpower</label>
					        			<input type="text" class="form-control input-sm" name="old" id="edit-manpower-old">
					        		</div>

					        		<div class="form-group">
					        			<label>New Manpower</label>
					        			<input type="text" class="form-control input-sm" name="new" id="edit-manpower-new">
					        		</div>
					        		<div class="form-group">
					        			<label>Remarks</label>
					        			<textarea class="form-control input-sm" name="remarks" id="edit-manpower-remarks" rows="3"></textarea>
					        		</div>
					        		<div class="form-group">
					        			<label>BC Old Manpower</label>
					        			<input type="text" class="form-control input-sm" name="bc-old" id="edit-manpower-bc-old">
					        		</div>

					        		<div class="form-group">
					        			<label>BC New Manpower</label>
					        			<input type="text" class="form-control input-sm" name="bc-new" id="edit-manpower-bc-new">
					        		</div>

					        		<div class="btn-update">
					        			<button type="submit" class="btn btn-info btn-sm pull-right">Update</button><br>
					        		</div>
					        	</form>
					      	</div>
					    </div>
					</div>
				</div>

				<div id="modal-remove-manpower" class="modal fade modal-confirm" role="dialog">
					<div class="modal-dialog modal-sm">
					    <div class="modal-content">
					    	<div class="modal-header">
					        	<button type="button" class="close" data-dismiss="modal">&times;</button>
					       		<strong>Remove Manpower</strong>
					      	</div>
					      	<div class="modal-body">
					        	<form method="POST" action="<?=base_url('admin/remove-manpower')?>" enctype="multipart/form-data">
					        		<input type="hidden" name="id" value="" id="id">
					        		
					        		<div class="text-center">
					        			<label>Are you sure you want to remove this Manpower?</label><br /><br /><br />
					        		</div>

					        		<div class="text-center">
					        			<button type="submit" class="btn btn-info btn-sm">Yes</button>
					        			&nbsp;&nbsp;
					        			<button data-dismiss="modal" class="btn btn-danger btn-sm">No</button>
					        			<br /><br />
					        		</div>
					        	</form>
					      	</div>
					    </div>
					</div>
				</div>
			</div>
