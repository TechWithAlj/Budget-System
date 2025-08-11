			
			<div class="col-lg-12" id="content">
				<div id="breadcrumb-div">
					<ul class="breadcrumb">
					    <li><a href="<?=base_url('ahg')?>"><span class="glyphicon glyphicon-home"></span>&nbsp;Dashboard</a></li>
					    <li class="active">Live Broiler</li>
					</ul>
				</div>
				<?php
					if($this->session->flashdata('message') != "" ){
						echo $this->session->flashdata('message');
					}
				?>
				<div id="add-btn">
					<a href="" class="btn btn-success btn-xs" data-toggle="modal" data-backdrop="static" data-target="#modal-category">+ Add Live Broiler</a>

					<div id="modal-category" class="modal fade modal-confirm" role="dialog">
						<div class="modal-dialog modal-sm">
						    <div class="modal-content">
						    	<div class="modal-header">
						        	<button type="button" class="close" data-dismiss="modal">&times;</button>
						       		<strong>Add Live Broiler</strong>
						      	</div>
						      	<div class="modal-body">
						        	<form method="POST" action="<?=base_url('ahg/add-broiler')?>" enctype="multipart/form-data" id="add-broiler">
						        		<div class="form-group">

						        			<label>Business Center:</label>
						        			<select class="form-control" name="bc" id="bc">
						        				<option value="">Select...</option>

						        				<?php foreach($bc as $row_bc):?>
						        				<option value="<?=encode($row_bc->bc_id)?>"><?=$row_bc->bc_name?></option>

						        				<?php endforeach;?>

						        			</select>
						        		</div>

						        		<div class="form-group">
						        			<label>Live Broiler</label>
						        			<input type="text" class="form-control input-sm" name="broiler" id="broiler">
						        		</div>

						        		<div class="form-group">
						        			<label>Carcass Yield</label>
						        			<input type="text" class="form-control input-sm" name="carcass" id="carcass">
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
				
				<table class="table table-hover" id="tbl-category">
					<thead>
						<tr>
							<th>Business Center</th>
							<th>Live Broiler</th>
							<th>Carcass Yield</th>
							<th>Total</th>
							<th>Action</th>
						</tr>
					</thead>
					<tbody>

						<?php
							foreach($broiler as $row):
						?>
						
						<tr>
							<td><?=$row->bc_name?></td>
							<td><?=$row->live_broiler?></td>
							<td><?=$row->carcass?></td>
							<td><?=$row->live_broiler*$row->carcass?></td>
							<td><a href="" class="btn btn-success btn-xs edit-broiler" data-id="<?=encode($row->broiler_id)?>">View</a></td>
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
					        	<form method="POST" action="<?=base_url('ahg/update-category')?>" enctype="multipart/form-data" id="update-category">
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