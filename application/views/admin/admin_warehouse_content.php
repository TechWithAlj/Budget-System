			
			<div class="col-lg-12" id="content">
				<div id="breadcrumb-div">
					<ul class="breadcrumb">
					    <li><a href="<?=base_url('admin')?>"><span class="glyphicon glyphicon-home"></span>&nbsp;Dashboard</a></li>
					    <li class="active"><span class="fa fa-bar-chart"></span>&nbsp;Warehouse</li>
					</ul>
				</div>
				<?php
					if($this->session->flashdata('message') != "" ){
						echo $this->session->flashdata('message');
					}
				?>
				<div id="add-btn">
					<a href="" class="btn btn-success btn-xs" data-toggle="modal" data-target="#modal-warehouse">+ Add Warehouse</a>

					<div id="modal-warehouse" class="modal fade" role="dialog">
						<div class="modal-dialog modal-sm">
						    <div class="modal-content">
						    	<div class="modal-header">
						        	<button type="button" class="close" data-dismiss="modal">&times;</button>
						       		<strong>Add warehouse</strong>
						      	</div>
						      	<div class="modal-body">
						        	<form method="POST" action="<?=base_url('admin/add-warehouse')?>" enctype="multipart/form-data" id="add-warehouse">
						        		<div class="form-group">
						        			<label>Warehouse</label>
						        			<input type="text" class="form-control input-sm" name="warehouse" id="warehouse">
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
				
				<table class="table table-hover" id="tbl-warehouse">
					<thead>
						<tr>
							<th>Warehouse</th>
							<th>Action</th>
						</tr>
					</thead>
					<tbody>

						<?php
							foreach($warehouse as $row):
						?>
						
						<tr>
							<td><?=$row->wh_name?></td>
							<td><a href="" class="btn btn-success btn-xs edit-warehouse" data-id="<?=encode($row->wh_id)?>">View</a></td>
						</tr>

						<?php endforeach;?>

					</tbody>
				</table>

				<div id="modal-edit-warehouse" class="modal fade" role="dialog">
					<div class="modal-dialog modal-sm">
					    <div class="modal-content">
					    	<div class="modal-header">
					        	<button type="button" class="close" data-dismiss="modal">&times;</button>
					       		<strong>Update warehouse</strong>
					      	</div>
					      	<div class="modal-body">
					        	<form method="POST" action="<?=base_url('admin/update-warehouse')?>" enctype="multipart/form-data" id="update-warehouse">
					        		<input type="hidden" id="id" name="id">
					        		<div class="form-group">
					        			<label>Warehouse</label>
					        			<input type="text" class="form-control input-sm" name="warehouse" id="warehouse">
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