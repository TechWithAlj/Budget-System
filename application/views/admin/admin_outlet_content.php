			
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
					<div class="row">
						<div class="col-lg-3">
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
								        		<input type="hidden" name="year" value="<?=$year?>">
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
								        			<label>Business Center:</label>
								        			<select class="form-control" name="bc" id="bc">
								        				<option value="">Select...</option>

								        				<?php foreach($bc as $row_bc):?>

								        				<option value="<?=encode($row_bc->bc_id)?>"><?=$row_bc->bc_name?></option>

								        				<?php endforeach;?>

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

								        		<div class="form-group">
								        			<label>Outlet Name</label>
								        			<input type="text" class="form-control input-sm" name="outlet" id="outlet">
								        		</div>

								        		<div class="form-group">
								        			<label>IFS Code</label>
								        			<input type="text" class="form-control input-sm" name="ifs" id="ifs">
								        		</div>

								        		<!-- <div class="form-group">
								        			<label>Region:</label>
								        			<select class="form-control" name="region" id="region">
								        				<option value="">Select...</option>

								        				<?php foreach($region as $row_region):?>

								        				<option value="<?=encode($row_region->region_id)?>"><?=$row_region->region_name?></option>

								        				<?php endforeach;?>

								        			</select>
								        		</div> -->

								        		

								        		

								        		<div class="btn-update">
								        			<button type="submit" class="btn btn-info btn-sm pull-right">Add</button><br>
								        		</div>
								        	</form>
								      	</div>
								    </div>
								</div>
							</div>

							&nbsp;&nbsp;&nbsp;&nbsp;
							<a href="" class="btn btn-info btn-xs" data-toggle="modal" data-backdrop="static" data-target="#modal-upload-outlets">+ Upload Outlets</a>

							<div id="modal-upload-outlets" class="modal fade" role="dialog">
								<div class="modal-dialog modal-sm">
								    <div class="modal-content">
								    	<div class="modal-header">
								        	<button type="button" class="close" data-dismiss="modal">&times;</button>
								       		<strong>Upload Outlet</strong>
								      	</div>
								      	<div class="modal-body">
								        	<form method="POST" action="<?=base_url('admin/upload-outlets')?>" enctype="multipart/form-data" id="upload-outlets">
								        		<input type="hidden" name="year" value="<?=$year?>">
								        		<div class="form-group">
								        			<label>Choose file:</label>
								        			<input type="file" name="outlet_file">
								        		</div><br /><br />

								        		<div class="text-right">
								        			<a href="<?=base_url('assets/outlet/Budgeting - Outlets Upload Templates.xlsx')?>" target="_blank"><span class="glyphicon glyphicon-download-alt"></span>&nbsp;&nbsp;Download Outlets Templates</a>
								        		</div><br /><br />						        		

								        		<div class="btn-update">
								        			<button type="submit" class="btn btn-info btn-sm pull-right">Upload</button><br>
								        		</div>
								        	</form>
								      	</div>
								    </div>
								</div>
							</div>
						</div>

						<div class="col-lg-2">
							<div class="form-group">
								<div class="date">
			                        <div class="input-group input-append date" id="outlet-trans-year">
			                            <input type="text" name="month" id="outlet-year" class="form-control input-sm" placeholder="Pick year" value="<?=$year?>">
			                            <span class="input-group-addon add-on"><span class="glyphicon glyphicon-calendar"></span></span>
			                        </div>
			                    </div>
							</div>
						</div>

						<div class="col-lg-7">
							<div class="text-right">
								<a href="<?=base_url('admin/download-outlets/' . $year)?>" target="_blank"><span class="glyphicon glyphicon-download-alt"></span>&nbsp;&nbsp; Download outlets</a>
							</div>
						</div>
					</div>
				</div>
				
				<table class="table table-hover" id="tbl-sales">
					<thead>
						<tr>
							<th>Outlet Code</th>
							<th>Outlet Name</th>
							<th>Brand</th>
							<th>Region</th>
							<th>BC</th>
							<th>Type</th>
							<th>Action</th>
						</tr>
					</thead>
					<tbody>

						<?php
							foreach($outlets as $row):
						?>
						
						<tr>
							<td><?=$row->ifs_code?></td>
							<td><?=$row->outlet_name?></td>
							<td><?=$row->brand_name?></td>
							<td><?=$row->region_name?></td>
							<td><?=$row->bc_name?></td>
							<td><?=$row->outlet_type_name?></td>
							<td>
								<a href="#" class="glyphicon glyphicon-pencil edit-outlet" data-id="<?=encode($row->outlet_id)?>"></a>
								&nbsp;&nbsp;

								<a href="#" class="glyphicon glyphicon-remove remove-outlet" data-id="<?=encode($row->outlet_id)?>"></a>
								&nbsp;&nbsp;

								<a href="<?=base_url('admin/outlet-brand/' . encode($row->outlet_id))?>" class="btn btn-xs btn-info">Brands</a></td>
						</tr>

						<?php endforeach;?>

					</tbody>
				</table>

				<div id="modal-edit-outlet" class="modal fade modal-confirm" role="dialog">
					<div class="modal-dialog modal-sm">
					    <div class="modal-content">
					    	<div class="modal-header">
					        	<button type="button" class="close" data-dismiss="modal">&times;</button>
					       		<strong>Update Outlet</strong>
					      	</div>
					      	<div class="modal-body">
					        	<form method="POST" action="<?=base_url('admin/update-outlet')?>" enctype="multipart/form-data" id="update-outlet">
					        		<input type="hidden" id="id" name="id">
					        		<div class="form-group">
					        			<label>Status:</label>
					        			<select class="form-control" name="status" id="edit-outlet-status">
					        				<option value="">Select...</option>
					        			</select>
					        		</div>

					        		<div class="form-group">
					        			<label>Business Center:</label>
					        			<select class="form-control" name="bc" id="edit-outlet-bc">
					        				<option value="">Select...</option>
					        			</select>
					        		</div>

					        		<div class="form-group">
					        			<label>Outlet Name</label>
					        			<input type="text" class="form-control input-sm" name="outlet" id="edit-outlet-name">
					        		</div>

					        		<div class="form-group">
					        			<label>IFS Code</label>
					        			<input type="text" class="form-control input-sm" name="ifs" id="edit-ifs-code">
					        		</div>					        		

					        		<div class="btn-update">
					        			<button type="submit" class="btn btn-info btn-sm pull-right">Update</button><br>
					        		</div>
					        	</form>
					      	</div>
					    </div>
					</div>
				</div>

				<div id="modal-remove-outlet" class="modal fade modal-confirm" role="dialog">
					<div class="modal-dialog modal-sm">
					    <div class="modal-content">
					    	<div class="modal-header">
					        	<button type="button" class="close" data-dismiss="modal">&times;</button>
					       		<strong>Remove Outlet</strong>
					      	</div>
					      	<div class="modal-body">
					        	<form method="POST" action="<?=base_url('admin/remove-outlet')?>" enctype="multipart/form-data" id="remove-outlet">

					        		<input type="hidden" name="id" id="id">
					        		<input type="hidden" name="year" id="remove-outlet-year">

					        		<div class="text-center">
					        			<strong><h5>Are you sure to remove this outlet?</h5></strong>
					        		</div><br /><br />

					        		<div class="btn-update text-center">
					        			<button type="submit" class="btn btn-info btn-sm">Yes</button>&nbsp;&nbsp;
					        			<button type="button" class="btn btn-danger btn-sm" data-dismiss="modal">No</button><br>
					        		</div>
					        	</form>
					      	</div>
					    </div>
					</div>
				</div>
			</div>