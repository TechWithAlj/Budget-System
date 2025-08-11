			
			<div class="col-lg-12" id="content">
				<div id="breadcrumb-div">
					<ul class="breadcrumb">
					    <li><a href="<?=base_url('admin')?>"><span class="glyphicon glyphicon-home"></span>&nbsp;Dashboard</a></li>
					    <li><a href="<?=base_url('admin/production-sales')?>">Production Sales</a></li>
					    <li class="active">Info</li>

					</ul>
				</div>
				<?php
					if($this->session->flashdata('message') != "" ){
						echo $this->session->flashdata('message');
					}
				?>

				<label class="data-info">Business Center: <?=$bc_name?></label><br /><br />

				<ul class="nav nav-tabs">
				    <li class="active"><a data-toggle="tab" href="#prod-sale-info-tab" class="tab-letter">Material Config</a></li>
				    <li><a data-toggle="tab" href="#prod-basic-tab" class="tab-letter">Basic Processing</a></li>
				    <li><a data-toggle="tab" href="#prod-classification-tab" class="tab-letter">Classification</a></li>
  				</ul>

  				<div class="tab-content">
					<div id="prod-sale-info-tab" class="tab-pane fade in active">
    					<br>
						<div id="add-btn">
							<a href="" class="btn btn-success btn-xs" data-toggle="modal" data-target="#modal-prod-sales-material">+ Add Material</a>

							<div id="modal-prod-sales-material" class="modal fade modal-confirm" role="dialog">
								<div class="modal-dialog modal-sm">
								    <div class="modal-content">
								    	<div class="modal-header">
								        	<button type="button" class="close" data-dismiss="modal">&times;</button>
								       		<strong>Add Material</strong>
								      	</div>
								      	<div class="modal-body">
								        	<form method="POST" action="<?=base_url('admin/add-prod-sales-material')?>" enctype="multipart/form-data" id="add-prod-sales-material">
								        		<input type="hidden" name="bc" value="<?=$bc?>">
								        		<div class="form-group">
								        			<label>Material</label>
								        			<select name="material[]" id="prod-sales-material" class="form-control">
								        				
								        				<option value="">Select...</option>

									        			<?php foreach($material as $row):?>

									        				<option value="<?=encode($row->material_id)?>"><?=$row->material_code . ' - ' . $row->material_desc?></option>

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
									<th>Material Code</th>
									<th>Material</th>
									<th>Action</th>
								</tr>
							</thead>
							<tbody>

								<?php
									foreach($prod_sales_mat as $row):
								?>
								
								<tr>
									<td><?=$row->material_code?></td>
									<td><?=$row->material_desc?></td>
									<td><a class="btn btn-success btn-xs view-prod-sales" href="<?=base_url('admin/production-sales-material/' . encode($row->prod_sales_id))?>">View</a>&nbsp;&nbsp;<a href="" class="btn btn-danger btn-xs cancel-prod-sales" data-id="<?=encode($row->prod_sales_id)?>">Cancel</a></td>
								</tr>

								<?php endforeach;?>

							</tbody>
						</table>
					</div>

	
					<div id="prod-basic-tab" class="tab-pane fade">
    					<br>
						<div id="add-btn">
							<a href=">?=base_url('admin/basic-processing')?>" class="btn btn-success btn-xs">+ Add Basic Processing</a>
						</div>
				
						<table class="table table-hover" id="tbl-employee">
							<thead>
								<tr>
									<th>Material</th>
									<th>Action</th>
								</tr>
							</thead>
							<tbody>

								<?php
									foreach($prod_sales_mat as $row):
								?>
								
								<tr>
									<td><?=$row->material_desc?></td>
									<td><a class="btn btn-success btn-xs view-prod-sales" href="<?=base_url('admin/production-sales-material/' . encode($row->prod_sales_id))?>">View</a>&nbsp;&nbsp;<a href="" class="btn btn-danger btn-xs cancel-prod-sales" data-id="<?=encode($row->prod_sales_id)?>">Cancel</a></td>
								</tr>

								<?php endforeach;?>

							</tbody>
						</table>
					</div>					

					<div id="prod-classification-tab" class="tab-pane fade">
    					<br>
						<div id="add-btn">
							<a href="" class="btn btn-success btn-xs" data-toggle="modal" data-target="#modal-prod-sales-material">+ Add Classification</a>

							<div id="modal-prod-sales-material" class="modal fade modal-confirm" role="dialog">
								<div class="modal-dialog modal-sm">
								    <div class="modal-content">
								    	<div class="modal-header">
								        	<button type="button" class="close" data-dismiss="modal">&times;</button>
								       		<strong>Add Material</strong>
								      	</div>
								      	<div class="modal-body">
								        	<form method="POST" action="<?=base_url('admin/add-prod-sales-material')?>" enctype="multipart/form-data" id="add-prod-sales-material">
								        		<input type="hidden" name="bc" value="<?=$bc?>">
								        		<div class="form-group">
								        			<label>Material</label>
								        			<select name="material[]" id="prod-sales-material" class="form-control">
								        				

									        			<?php foreach($material as $row):?>

									        				<option value="<?=encode($row->material_id)?>"><?=$row->material_desc?></option>

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
									<th>Material</th>
									<th>Action</th>
								</tr>
							</thead>
							<tbody>

								<?php
									foreach($prod_sales_mat as $row):
								?>
								
								<tr>
									<td><?=$row->material_desc?></td>
									<td><a class="btn btn-success btn-xs view-prod-sales" href="<?=base_url('admin/production-sales-material/' . encode($row->prod_sales_id))?>">View</a>&nbsp;&nbsp;<a href="" class="btn btn-danger btn-xs cancel-prod-sales" data-id="<?=encode($row->prod_sales_id)?>">Cancel</a></td>
								</tr>

								<?php endforeach;?>

							</tbody>
						</table>
					</div>
				</div>
			</div>