			
			<div class="col-lg-12" id="content">
				<div id="breadcrumb-div">
					<ul class="breadcrumb">
					    <li><a href="<?=base_url('admin')?>"><span class="glyphicon glyphicon-home"></span>&nbsp;Dashboard</a></li>
					    <li class="active">Category</li>
					</ul>
				</div>
				<?php
					if($this->session->flashdata('message') != "" ){
						echo $this->session->flashdata('message');
					}
				?>
				<div id="add-btn">
					<a href="" class="btn btn-success btn-xs" data-toggle="modal" data-backdrop="static" data-target="#modal-outlet">+ Add Outlet</a>&nbsp;&nbsp;<!-- <a href="" class="btn btn-info btn-xs" data-toggle="modal" data-backdrop="static" data-target="#modal-budget">+ Upload Budget</a> -->

					<div id="modal-outlet" class="modal fade modal-confirm" role="dialog">
						<div class="modal-dialog modal-sm">
						    <div class="modal-content">
						    	<div class="modal-header">
						        	<button type="button" class="close" data-dismiss="modal">&times;</button>
						       		<strong>Add Outlet</strong>
						      	</div>
						      	<div class="modal-body">
						        	<form method="POST" action="<?=base_url('admin/add-outlet')?>" enctype="multipart/form-data" id="add-outlet">
						        		<div class="form-group">
						        			<label>Outlet Name</label>
						        			<input type="text" class="form-control input-sm" name="outlet" id="outlet">
						        		</div>

						        		<div class="form-group">
						        			<label>IFS Code</label>
						        			<input type="text" class="form-control input-sm" name="ifs" id="ifs">
						        		</div>

						        		<div class="form-group">
						        			<label>Status:</label>
						        			<select class="form-control" name="status" id="status">
						        				<option value="">Select...</option>
						        				<?php foreach($status as $row_status):?>

						        				<option value="<?=encode($row_status->outlet_status_id)?>"><?=$row_status->outlet_status_name?></option>

						        				<?php endforeach;?>
						        			</select>
						        		</div>

						        		<div class="form-group">
						        			<label>Region:</label>
						        			<select class="form-control" name="region" id="region">
						        				<option value="">Select...</option>

						        				<?php foreach($region as $row_region):?>

						        				<option value="<?=encode($row_region->region_id)?>"><?=$row_region->region_name?></option>

						        				<?php endforeach;?>

						        			</select>
						        		</div>

						        		<div class="form-group">
						        			<label>Business Center:</label>
						        			<select class="form-control" name="bc" id="bc">
						        				<option value="">Select...</option>
						        			</select>
						        		</div>

						        		<div class="form-group">
						        			<label>Type:</label>
						        			<select class="form-control" name="type" id="type">
						        				<option value="">Select...</option>
						        				<?php foreach($type as $row_type):?>

						        				<option value="<?=encode($row_type->brand_type_id)?>"><?=$row_type->brand_type_name?></option>

						        				<?php endforeach;?>
						        			</select>
						        		</div>

						        		<div class="form-group">
						        			<label>Brand:</label>
						        			<select class="form-control" name="brand[]" id="brand">
						        				<option value="">Select...</option>
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

					<div id="modal-budget" class="modal fade modal-confirm" role="dialog">
						<div class="modal-dialog modal-sm">
						    <div class="modal-content">
						    	<div class="modal-header">
						        	<button type="button" class="close" data-dismiss="modal">&times;</button>
						       		<strong>Upload budget</strong>
						      	</div>
						      	<div class="modal-body">
						        	<form method="POST" action="<?=base_url('admin/upload-budget')?>" enctype="multipart/form-data" id="add-outlet">
						        		
						        		<label>Pick Month:</label>
										<div class="form-group">
											<div class="date">
				                                <div class="input-group input-append date" id="budget-month">
				                                    <input type="text" name="month" id="date-pick-month" class="form-control input-sm" placeholder="Pick month" value="">
				                                    <span class="input-group-addon add-on"><span class="glyphicon glyphicon-calendar"></span></span>
				                                </div>
				                            </div>
										</div>

						        		<div class="form-group">
						        			<label>Choose file:</label>
						        			<input type="file" name="budget_file">
						        		</div>


						        		<div class="btn-update">
						        			<button type="submit" class="btn btn-info btn-sm pull-right">Upload</button><br>
						        		</div>
						        	</form>
						      	</div>
						    </div>
						</div>
					</div>
				</div>
				
				<table class="table table-hover" id="tbl-category">
					<thead>
						<tr>
							<th>IFS Code</th>
							<th>Outlet</th>
							<th>Brand Type</th>
							<th>Region</th>
							<th>BC</th>
							<th>Status</th>
							<th>Action</th>
						</tr>
					</thead>
					<tbody>

						<?php
							foreach($outlet as $row):
						?>
						
						<tr>
							<td><?=$row->ifs_code?></td>
							<td><?=$row->outlet_name?></td>
							<td><?=$row->brand_type_name . ' - ' . $row->bc_name?></td>
							<td><?=$row->region_name?></td>
							<td><?=$row->bc_name?></td>
							<td><?=$row->outlet_status_name?></td>
							<td><a href="" class="edit-outlet"><span class="glyphicon glyphicon-pencil"></span></a>&nbsp;&nbsp;<a href="<?=base_url('admin/outlet-brand/' . encode($row->outlet_id))?>" class="btn btn-xs btn-success edit-outlet" data-id="<?=encode($row->outlet_id)?>">Brand</a>&nbsp;&nbsp;<a href="<?=base_url('admin/outlet-budget/' . encode($row->outlet_id))?>" class="btn btn-xs btn-info edit-outlet" data-id="<?=encode($row->outlet_id)?>">Budget</a></td>
						</tr>

						<?php endforeach;?>

					</tbody>
				</table>

				<div id="modal-edit-category" class="modal fade" role="dialog">
					<div class="modal-dialog modal-sm">
					    <div class="modal-content">
					    	<div class="modal-header">
					        	<button type="button" class="close" data-dismiss="modal">&times;</button>
					       		<strong>Update Category</strong>
					      	</div>
					      	<div class="modal-body">
					        	<form method="POST" action="<?=base_url('admin/update-category')?>" enctype="multipart/form-data" id="update-category">
					        		<input type="hidden" id="id" name="id">
					        		<div class="form-group">
					        			<label>Category</label>
					        			<input type="text" class="form-control input-sm" name="category" id="category">
					        		</div>

					        		<div class="btn-update">
					        			<button type="submit" class="btn btn-info btn-sm pull-right">Update</button><br>
					        		</div>
					        	</form>
					      	</div>
					    </div>
					</div>
				</div>
			</div>