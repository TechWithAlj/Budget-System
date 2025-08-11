			
			<div class="col-lg-12" id="content">
				<div id="breadcrumb-div">
					<ul class="breadcrumb">
					    <li><a href="<?=base_url('admin')?>"><span class="glyphicon glyphicon-home"></span>&nbsp;Dashboard</a></li>
					    <li class="active">Measurement</li>
					</ul>
				</div>
				<?php
					if($this->session->flashdata('message') != "" ){
						echo $this->session->flashdata('message');
					}
				?>
				<div id="add-btn">
					<a href="" class="btn btn-success btn-xs" data-toggle="modal" data-target="#modal-measurement">+ Add Measurement</a>

					<div id="modal-measurement" class="modal fade" role="dialog">
						<div class="modal-dialog modal-sm">
						    <div class="modal-content">
						    	<div class="modal-header">
						        	<button type="button" class="close" data-dismiss="modal">&times;</button>
						       		<strong>Add Measurement</strong>
						      	</div>
						      	<div class="modal-body">
						        	<form method="POST" action="<?=base_url('admin/add-measurement')?>" enctype="multipart/form-data" id="add-measurement">
						        		<div class="form-group">
						        			<label>Measurement</label>
						        			<input type="text" class="form-control input-sm" name="measurement">
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
				
				<table class="table table-hover" id="tbl-users">
					<thead>
						<tr>
							<th>Measurement</th>
							<th>Action</th>
						</tr>
					</thead>
					<tbody>

						<?php
							foreach($measurement as $row):
						?>
						
						<tr>
							<td><?=$row->um_name?></td>
							<td><a href="" class="btn btn-success btn-xs edit-measurement" data-id="<?=encode($row->um_id)?>">View</a></td>
						</tr>

						<?php endforeach;?>

					</tbody>
				</table>

				<div id="modal-edit-measurement" class="modal fade" role="dialog">
					<div class="modal-dialog modal-sm">
					    <div class="modal-content">
					    	<div class="modal-header">
					        	<button type="button" class="close" data-dismiss="modal">&times;</button>
					       		<strong>Update measurement</strong>
					      	</div>
					      	<div class="modal-body">
					        	<form method="POST" action="<?=base_url('admin/update-measurement')?>" enctype="multipart/form-data" id="update-measurement">
					        		<input type="hidden" id="id" name="id">

					        		<div class="form-group">
					        			<label>Measurement</label>
					        			<input type="text" class="form-control input-sm" name="measurement" id="measurement">
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