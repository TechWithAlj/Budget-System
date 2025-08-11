			
			<div class="col-lg-12" id="content">
				<div id="breadcrumb-div">
					<ul class="breadcrumb">
					    <li><a href="<?=base_url('admin')?>"><span class="glyphicon glyphicon-home"></span>&nbsp;Dashboard</a></li>
					    <li class="active">&nbsp;Remarks</li>
					</ul>
				</div>
				<?php
					if($this->session->flashdata('message') != "" ){
						echo $this->session->flashdata('message');
					}
				?>
				<div id="add-btn">
					<a href="" class="btn btn-success btn-xs" data-toggle="modal" data-target="#modal-remarks">+ Add Remarks</a>

					<div id="modal-remarks" class="modal fade" role="dialog">
						<div class="modal-dialog modal-sm">
						    <div class="modal-content">
						    	<div class="modal-header">
						        	<button type="button" class="close" data-dismiss="modal">&times;</button>
						       		<strong>Add Remarks</strong>
						      	</div>
						      	<div class="modal-body">
						        	<form method="POST" action="<?=base_url('admin/add-remarks')?>" enctype="multipart/form-data" id="add-remarks">
						        		<div class="form-group">
						        			<label>Remarks</label>
						        			<input type="text" class="form-control input-sm" name="remarks">
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
				
				<table class="table table-hover" id="tbl-remarks">
					<thead>
						<tr>
							<th>Remarks</th>
							<th>Action</th>
						</tr>
					</thead>
					<tbody>

						<?php
							foreach($remarks as $row):
						?>
						
						<tr>
							<td><?=$row->po_remarks?></td>
							<td><a href="" class="btn btn-success btn-xs edit-remarks" data-id="<?=encode($row->po_remarks_id)?>">View</a></td>
						</tr>

						<?php endforeach;?>

					</tbody>
				</table>

				<div id="modal-edit-remarks" class="modal fade" role="dialog">
					<div class="modal-dialog modal-sm">
					    <div class="modal-content">
					    	<div class="modal-header">
					        	<button type="button" class="close" data-dismiss="modal">&times;</button>
					       		<strong>Update Remarks</strong>
					      	</div>
					      	<div class="modal-body">
					        	<form method="POST" action="<?=base_url('admin/update-po-remarks')?>" enctype="multipart/form-data" id="update-remarks">
					        		<input type="hidden" id="id" name="id">

					        		<div class="form-group">
					        			<label>Remarks</label>
					        			<input type="text" class="form-control input-sm" name="remarks" id="remarks">
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