			
			<div class="col-lg-12" id="content">
				<div id="breadcrumb-div">
					<ul class="breadcrumb">
					    <li><a href="<?=base_url('admin')?>"><span class="glyphicon glyphicon-home"></span>&nbsp;Dashboard</a></li>
					    <li><a href="<?=base_url('admin/production-sales')?>">Production Sales</a></li>
					    <li><a href="<?=base_url('admin/production-sales-info/' . $bc_id)?>">Info</a></li>
					    <li class="active">Material</li>
					</ul>
				</div>
				<?php
					if($this->session->flashdata('message') != "" ){
						echo $this->session->flashdata('message');
					}
				?>
				<div id="add-btn">
					<a href="" class="btn btn-success btn-xs" data-toggle="modal" data-target="#modal-prod-sales-material">+ Add Process</a>

					<div id="modal-prod-sales-material" class="modal fade modal-confirm" role="dialog">
						<div class="modal-dialog modal-sm">
						    <div class="modal-content">
						    	<div class="modal-header">
						        	<button type="button" class="close" data-dismiss="modal">&times;</button>
						       		<strong>Add Process</strong>
						      	</div>
						      	<div class="modal-body">
						        	<form method="POST" action="<?=base_url('admin/add-prod-sales-process')?>" enctype="multipart/form-data" id="add-prod-sales-material">
						        		<input type="hidden" id="id" name="id" value="<?=$id?>">
						        		<div class="form-group">
						        			<label>Material</label>
						        			<select name="process" id="prod-sales-process" class="form-control">
						        				<option value="">Select...</option>

							        			<?php foreach($process as $row):?>

							        				<option value="<?=encode($row->process_type_id)?>"><?=$row->process_type_name?></option>

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

					<div id="modal-cancel-emp" class="modal fade modal-confirm" role="dialog">
						<div class="modal-dialog modal-sm">
						    <div class="modal-content">
						    	<div class="modal-header">
						        	<button type="button" class="close" data-dismiss="modal">&times;</button>
						       		<strong>Cancel Employee</strong>
						      	</div>
						      	<div class="modal-body">
						      		<form method="POST" id="cancel-employee" action="<?=base_url('admin/cancel-employee/')?>">
						      			<input type="hidden" name="id" id="id">
						        		<div class="text-center">
						        			<strong>Are you sure to cancel this employee?</strong>
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
							<th>Process</th>
							<th>Status</th>
							<th>Action</th>
						</tr>
					</thead>
					<tbody>

						<?php
							foreach($prod_sales_process as $row):
						?>
						
						<tr>
							<td><?=$row->process_type_name . ' - ' . $row->material_desc?></td>
							<td><?=$row->config > 0 ? 'Configured' : 'Unconfigured' ?></td>
							<td><a class="btn btn-success btn-xs view-prod-sales" href="<?=base_url('admin/production-sales-material/' . encode($row->prod_trans_id))?>">View</a>&nbsp;&nbsp;<a href="" class="btn btn-danger btn-xs cancel-prod-sales" data-id="<?=encode($row->prod_trans_id)?>">Cancel</a></td>
						</tr>

						<?php endforeach;?>

					</tbody>
				</table>
			</div>