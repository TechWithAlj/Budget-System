			
			<div class="col-lg-12" id="content">
				<div id="breadcrumb-div">
					<ul class="breadcrumb">
					    <li><a href="<?=base_url('admin')?>"><span class="glyphicon glyphicon-home"></span>&nbsp;Dashboard</a></li>
					    <li class="active">Business Center</li>
					</ul>
				</div>
				<?php
					if($this->session->flashdata('message') != "" ){
						echo $this->session->flashdata('message');
					}
				?>
				<div id="add-btn">
					<a href="" class="btn btn-success btn-xs" data-toggle="modal" data-target="#modal-category">+ Add Business Center</a>

					<div id="modal-category" class="modal fade modal-confirm" role="dialog">
						<div class="modal-dialog modal-sm">
						    <div class="modal-content">
						    	<div class="modal-header">
						        	<button type="button" class="close" data-dismiss="modal">&times;</button>
						       		<strong>Add business center</strong>
						      	</div>
						      	<div class="modal-body">
						        	<form method="POST" action="<?=base_url('admin/add-business')?>" enctype="multipart/form-data" id="add-business">
						        		<div class="form-group">
						        			<label>Region:</label>
						        			<select name="region" class="form-control">
						        				<option>Select...</option>
						        				<?php foreach($region as $row_region):?>
						        					<option value="<?=encode($row_region->region_id)?>"><?=$row_region->region_name?></option>
						        				<?php endforeach;?>
						        			</select>
						        		</div>

						        		<div class="form-group">
						        			<label>Business center code:</label>
						        			<input type="text" class="form-control input-sm" name="bc_code" id="bc-code">
						        		</div>
						        		<div class="form-group">
						        			<label>Cost Center code:</label>
						        			<input type="text" class="form-control input-sm" name="cost_center" id="bc-code">
						        		</div>
						        		<div class="form-group">
						        			<label>Business center</label>
						        			<input type="text" class="form-control input-sm" name="business" id="business">
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
				
				<table class="table table-hover" id="tbl-business">
					<thead>
						<tr>
							<th>Business center code</th>
							<th>Cost center</th>
							<th>Business center</th>
							<th>Business center</th>
							<th>Action</th>
						</tr>
					</thead>
					<tbody>

						<?php
							foreach($business as $row):
						?>
						
						<tr>
							<td><?=$row->cost_center?></td>
							<td><?=$row->bc_code?></td>
							<td><?=$row->bc_name?></td>
							<td><?=$row->region_name?></td>
							<td><a href="" class="btn btn-success btn-xs edit-business" data-id="<?=encode($row->bc_id)?>">View</a></td>
						</tr>

						<?php endforeach;?>

					</tbody>
				</table>

				<div id="modal-edit-business" class="modal fade" role="dialog">
					<div class="modal-dialog modal-sm">
					    <div class="modal-content">
					    	<div class="modal-header">
					        	<button type="button" class="close" data-dismiss="modal">&times;</button>
					       		<strong>Update business</strong>
					      	</div>
					      	<div class="modal-body">
					        	<form method="POST" action="<?=base_url('admin/update-business')?>" enctype="multipart/form-data" id="update-business">
					        		<input type="hidden" id="id" name="id">

					        		<div class="form-group">
					        			<label>Business center code</label>
					        			<input type="text" class="form-control input-sm" name="bc_code" id="bc-code">
					        		</div>

					        		<div class="form-group">
					        			<label>Business center</label>
					        			<input type="text" class="form-control input-sm" name="business" id="business">
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