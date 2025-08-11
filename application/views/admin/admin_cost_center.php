			
			<div class="col-lg-12" id="content">
				<div id="breadcrumb-div">
					<ul class="breadcrumb">
					    <li><a href="<?=base_url('admin')?>"><span class="glyphicon glyphicon-home"></span>&nbsp;Dashboard</a></li>
					    <li class="active">Cost Center</li>
					</ul>
				</div>
				<?php
					if($this->session->flashdata('message') != "" ){
						echo $this->session->flashdata('message');
					}
				?>
				<div id="add-btn">
					<a href="" class="btn btn-success btn-xs" data-toggle="modal" data-backdrop="static" data-target="#modal-category">+ Add Cost/Profit Center</a>&nbsp;&nbsp;&nbsp;
					<a href="" class="btn btn-success btn-xs" data-toggle="modal" data-backdrop="static" data-target="#modal-upload-cost-center">+ Upload Cost Center</a>&nbsp;&nbsp;&nbsp;

					<a href="" class="btn btn-info btn-xs" data-toggle="modal" data-backdrop="static" data-target="#modal-cost-center-allocation">Upload Allocation</a>

					<div id="modal-upload-cost-center" class="modal fade" role="dialog">
						<div class="modal-dialog modal-md">
						    <div class="modal-content">
						    	<div class="modal-header">
						        	<button type="button" class="close" data-dismiss="modal">&times;</button>
						       		<strong>Upload Cost Center</strong>
						      	</div>
						      	<div class="modal-body">
						        	<form method="POST" action="<?=base_url('admin/upload-cost-center')?>" enctype="multipart/form-data" id="upload-materials">

						        		<div class="form-group">
						        			<label>Choose file:</label>
						        			<input type="file" name="prod_config_file">
						        		</div><br /><br />

						        		<div class="text-right">
						        			<a href="<?=base_url('assets/cost-center/Cost Center Template.xlsx')?>" target="_blank"><span class="glyphicon glyphicon-download-alt"></span>&nbsp;&nbsp;Download Templates</a>
						        		</div><br /><br />
						        		
						        		<div class="btn-update">
						        			<button type="submit" class="btn btn-info btn-sm pull-right">Upload</button><br>
						        		</div>
						        	</form>
						      	</div>
						    </div>
						</div>
					</div>

					<div id="modal-category" class="modal fade modal-confirm" role="dialog">
						<div class="modal-dialog modal-sm">
						    <div class="modal-content">
						    	<div class="modal-header">
						        	<button type="button" class="close" data-dismiss="modal">&times;</button>
						       		<strong>Add Cost/Profit Center</strong>
						      	</div>
						      	<div class="modal-body">
						        	<form method="POST" action="<?=base_url('admin/add-cost-center')?>" enctype="multipart/form-data" id="add-cost-center">
						        		<div class="form-group">

						        			<label></label>
						        			
						        		</div>

						        		<div class="form-group">
						        			<label>Cost/Profit Center Code:</label>
						        			<input type="text" class="form-control input-sm" name="cost_code">
						        		</div>

						        		<div class="form-group">
						        			<label>Cost/Profit Center Name:</label>
						        			<input type="text" class="form-control input-sm" name="cost_name">
						        		</div>

						        		<div class="form-group">
						        			<label>Business Center:</label>
						        			<select class="form-control" name="bc">
						        				<option value="">Select...</option>

						        				<?php foreach($bc as $row_bc):?>
						        				
						        					<option value="<?=encode($row_bc->bc_id)?>"><?=$row_bc->bc_name?></option>

						        				<?php endforeach;?>
						        			</select>
						        		</div>

						        		<div class="form-group">
						        			<label>Unit:</label>
						        			<select class="form-control" name="unit">
						        				<option value="">Select...</option>
						        				<?php foreach($unit as $row_unit):?>
						        				
						        				<option value="<?=encode($row_unit->company_unit_id)?>"><?=$row_unit->company_unit_name?></option>

						        				<?php endforeach;?>
						        			</select>
						        		</div>

						        		<div class="form-group">
						        			<label>Type:</label>
						        			<select class="form-control" name="type">
						        				<option value="">Select...</option>

						        				<?php foreach($type as $row_type):?>
						        				<option value="<?=encode($row_type->cost_center_type_id)?>"><?=$row_type->cost_center_type_name?></option>

						        				<?php endforeach;?>

						        			</select>
						        		</div>

						        		<div class="form-group">
						        			<label>Group:</label>
						        			<select class="form-control" name="group">
						        				<option value="">Select...</option>

						        				<?php foreach($cc_group as $row_group):?>
						        				<option value="<?=encode($row_group->cost_center_group_id)?>"><?=$row_group->cost_center_group_name?></option>

						        				<?php endforeach;?>

						        			</select>
						        		</div>

						        		<div class="form-group">
						        			<label>Allocation Type:</label>
						        			<select class="form-control" name="allocation[]" id="cost-center-allocation" multiple="multiple">
						        				<option value="">Select...</option>

						        				<?php foreach($allocation as $row_allocation):?>
						        				
						        				<option value="<?=encode($row_allocation->allocation_type_id)?>"><?=$row_allocation->allocation_type_name?></option>

						        				<?php endforeach;?>

						        			</select>
						        		</div>

						        		<div class="form-group">
						        			<label>Parent</label>
						        			<select class="form-control" name="parent">
						        				<option value="">Select...</option>

						        				<?php foreach($cc as $row_cost):?>
						        				<option value="<?=encode($row_cost->cost_center_id)?>"><?=$row_cost->cost_center_desc . ' - ' . $row_cost->cost_center_code?></option>

						        				<?php endforeach;?>

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
				</div>

				<div id="modal-cost-center-allocation" class="modal fade modal-confirm" role="dialog">
					<div class="modal-dialog modal-sm">
					    <div class="modal-content">
					    	<div class="modal-header">
					        	<button type="button" class="close" data-dismiss="modal">&times;</button>
					       		<strong>Upload Cost Center Allocation</strong>
					      	</div>
					      	<div class="modal-body">
					        	<form method="POST" action="<?=base_url('admin/upload-cost-center-allocation')?>" enctype="multipart/form-data" id="">

					        		<div class="form-group">
					        			<label>Choose file:</label>
					        			<input type="file" name="allocation_file">
					        		</div>

					        		<div class="btn-update">
					        			<button type="submit" class="btn btn-info btn-sm pull-right">Upload</button><br>
					        		</div>
					        	</form>
					      	</div>
					    </div>
					</div>
				</div>
				
				<table class="table table-hover" id="tbl-cost-center">
					<thead>
						<tr>
							<th>Name</th>
							<th>Code</th>
							<th>Unit</th>
							<th>BC</th>
							<th>Type</th>
							<th>Group</th>
							<th>Allocation Type</th>
							<!-- <th>Parent</th> -->
							<th>Action</th>
						</tr>
					</thead>
					<tbody>

						<?php
							foreach($cost_center as $row):
						?>
						
						<tr>
							<td><?=$row->cost_center_desc?></td>
							<td><?=$row->cost_center?></td>
							<td><?=$row->company_unit_name?></td>
							<td><?=$row->bc_name?></td>
							<td><?=$row->cost_center_type_name?></td>
							<td><?=$row->cost_center_group_name?></td>
							<td><?=$row->allocation?></td>
							<!-- <td><?=$row->cost_center_type_name?></td> -->
							<td><a href="" class="btn btn-success btn-xs edit-cost-center" data-id="<?=encode($row->id)?>">View</a></td>
						</tr>

						<?php endforeach;?>

					</tbody>
				</table>

				<div id="modal-edit-cost-center" class="modal fade modal-confirm" role="dialog">
					<div class="modal-dialog modal-sm">
					    <div class="modal-content">
					    	<div class="modal-header">
					        	<button type="button" class="close" data-dismiss="modal">&times;</button>
					       		<strong>Update Cost/Profit Center</strong>
					      	</div>
					      	<div class="modal-body">
					        	<form method="POST" action="<?=base_url('admin/update-cost-center')?>" enctype="multipart/form-data" id="update-cost-center">

					        		<input type="hidden" name="id" id="id">

					        		<div class="form-group">
					        			<label>Cost/Profit Center Code:</label>
					        			<input type="text" class="form-control input-sm" name="cost_code" id="edit-cost-center-code">
					        		</div>

					        		<div class="form-group">
					        			<label>Cost/Profit Center Name:</label>
					        			<input type="text" class="form-control input-sm" name="cost_name" id="edit-cost-center-name">
					        		</div>

					        		<div class="form-group">
					        			<label>Business Center:</label>
					        			<select class="form-control" name="bc" id="edit-cost-center-bc">
					        			</select>
					        		</div>

					        		<div class="form-group">
					        			<label>Unit:</label>
					        			<select class="form-control" name="unit" id="edit-cost-center-unit">
					        				
					        			</select>
					        		</div>

					        		<div class="form-group">
					        			<label>Type:</label>
					        			<select class="form-control" name="type" id="edit-cost-center-type">

					        			</select>
					        		</div>

					        		<div class="form-group">
					        			<label>Group:</label>
					        			<select class="form-control" name="group" id="edit-cost-center-group">

					        			</select>
					        		</div>

					        		<div class="form-group">
					        			<label>Allocation Type:</label>
					        			<select class="form-control" name="allocation[]" id="edit-cost-center-allocation">
					        				
					        			</select>
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