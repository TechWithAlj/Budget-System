			
			<div class="col-lg-12" id="content">
				<div id="breadcrumb-div">
					<ul class="breadcrumb">
					    <li><a href="<?=base_url('admin')?>"><span class="glyphicon glyphicon-home"></span>&nbsp;Dashboard</a></li>
					    <li class="active">Sales</li>
					</ul>
				</div>
				<?php
					if($this->session->flashdata('message') != "" ){
						echo $this->session->flashdata('message');
					}
				?>
				<div id="add-btn">
					<a href="" class="btn btn-success btn-xs" data-toggle="modal" data-backdrop="static" data-target="#modal-add-lock">+ Lock module</a>&nbsp;&nbsp;<!-- <a href="" class="btn btn-info btn-xs" data-toggle="modal" data-backdrop="static" data-target="#modal-budget">+ Upload Budget</a> -->

					<div id="modal-add-lock" class="modal fade modal-confirm" role="dialog">
						<div class="modal-dialog modal-sm">
						    <div class="modal-content">
						    	<div class="modal-header">
						        	<button type="button" class="close" data-dismiss="modal">&times;</button>
						       		<strong>Add Lock Module</strong>
						      	</div>
						      	<div class="modal-body">
						        	<form method="POST" action="<?=base_url('admin/add-lock-module')?>" enctype="multipart/form-data" id="add-lock-module">

						        		<div class="form-group">
						        			<label>Module:</label>
						        			<select class="form-control" name="module">
						        				<option value="">Select...</option>
						        				
						        				<?php foreach($module as $row_module):?>

						        				<option value="<?=encode($row_module->module_id)?>"><?=$row_module->module_name?></option>

						        				<?php endforeach;?>

						        			</select>
						        		</div>

						        		<div class="form-group">
						        			<label>Lock type:</label>
						        			<select class="form-control" name="lock_type" id="lock-type">
						        				<option value="">Select...</option>

						        				<?php foreach($lock_type as $row_lock_type):?>

						        				<option value="<?=encode($row_lock_type->lock_type_id)?>"><?=$row_lock_type->lock_type_name?></option>

						        				<?php endforeach;?>

						        			</select>
						        		</div>

						        		<div class="form-group">
						        			<label>Lock Location:</label>
						        			<select class="form-control" name="lock_location" id="lock-location">
						        				<option value="">Select...</option>
						        			</select>
						        		</div>

						        		<div class="form-group">
						        			<label>Lock Year:</label>
						        			<div class="date">
						                        <div class="input-group input-append date" id="lock-module-date">
						                            <input type="text" name="lock_year" class="form-control input-sm" placeholder="Pick year" value="">
						                            <span class="input-group-addon add-on"><span class="glyphicon glyphicon-calendar"></span></span>
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
				
				<table class="table table-hover" id="tbl-lock-module">
					<thead>
						<tr>
							<th>Lock Type</th>
							<th>Location</th>
							<th>Module</th>
							<th>Lock Year</th>
							<th>Status</th>
							<th>Action</th>
						</tr>
					</thead>
					<tbody>

						<?php
							foreach($lock_module as $row_lock):
								$lock = '';
								if($row_lock->lock_status_name == 'Locked'){
									$lock = '<a href="" class="locked" data-id="' . encode($row_lock->lock_id) . '"><span class="fa fa-lock fa-lg"></span></a>';
								}elseif($row_lock->lock_status_name == 'Unlocked'){
									$lock = '<a href="" class="unlocked" data-id="' . encode($row_lock->lock_id) . '"><span class="fa fa-unlock fa-lg"></span></a>';
								}
						?>
						
						<tr>
							<td><?=$row_lock->lock_type_name?></td>
							<td><?=$row_lock->location_name?></td>
							<td><?=$row_lock->module_name?></td>
							<td><?=$row_lock->lock_year?></td>
							<td><?=$row_lock->lock_status_name?></td>
							<td><?=$lock?>&nbsp;&nbsp;&nbsp;&nbsp;<a href="" class="btn btn-xs btn-danger cancel-lock">Cancel</a></td>
						</tr>

						<?php endforeach;?>

					</tbody>
				</table>

				<div id="modal-lock" class="modal fade" role="dialog">
					<div class="modal-dialog modal-sm">
					    <div class="modal-content">
					    	<div class="modal-header">
					        	<button type="button" class="close" data-dismiss="modal">&times;</button>
					       		<strong>Module Lock</strong>
					      	</div>
					      	<div class="modal-body">
					        	<form method="POST" action="<?=base_url('admin/update-lock-module')?>" enctype="multipart/form-data" id="update-lock-module">
					        		<div class="text-center">
						        		<input type="hidden" id="id" name="id">
						        		<label><strong>Are you sure to lock this module?</strong></label><br /><br />
						        		<button type="submit" class="btn btn-success btn-sm">Yes</button>&nbsp;&nbsp;<button type="button" class="btn btn-danger btn-sm" data-dismiss="modal">No</button>
						        	</div>
					        	</form>
					      	</div>
					    </div>
					</div>
				</div>


				<div id="modal-unlock" class="modal fade" role="dialog">
					<div class="modal-dialog modal-sm">
					    <div class="modal-content">
					    	<div class="modal-header">
					        	<button type="button" class="close" data-dismiss="modal">&times;</button>
					       		<strong>Module Unlock</strong>
					      	</div>
					      	<div class="modal-body">
					        	<form method="POST" action="<?=base_url('admin/update-unlock-module')?>" enctype="multipart/form-data" id="update-unlock-module">
					        		<div class="text-center">
						        		<input type="hidden" id="id" name="id">
						        		<label><strong>Are you sure to unlock this module?</strong></label><br /><br />
						        		<button type="submit" class="btn btn-success btn-sm">Yes</button>&nbsp;&nbsp;<button type="button" class="btn btn-danger btn-sm" data-dismiss="modal">No</button>
						        	</div>
					        	</form>
					      	</div>
					    </div>
					</div>
				</div>
			</div>