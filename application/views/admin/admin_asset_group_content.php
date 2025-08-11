			
			<div class="col-lg-12" id="content">
				<div id="breadcrumb-div">
					<ul class="breadcrumb">
					    <li><a href="<?=base_url('admin')?>"><span class="glyphicon glyphicon-home"></span>&nbsp;Dashboard</a></li>
					    <li class="active">Asset Group</li>
					</ul>
				</div>
				<?php
					if($this->session->flashdata('message') != "" ){
						echo $this->session->flashdata('message');
					}
				?>

				<div id="add-btn">
					<a href="" class="btn btn-info btn-xs" data-toggle="modal" data-backdrop="static" data-target="#modal-upload-asset">+ Upload Assets</a>

					<div id="modal-upload-asset" class="modal fade" role="dialog">
						<div class="modal-dialog modal-sm">
						    <div class="modal-content">
						    	<div class="modal-header">
						        	<button type="button" class="close" data-dismiss="modal">&times;</button>
						       		<strong>Upload Asset</strong>
						      	</div>
						      	<div class="modal-body">
						        	<form method="POST" action="<?=base_url('admin/upload-asset-group')?>" enctype="multipart/form-data" id="upload-asset-group">

						        		<div class="form-group">
						        			<label>Choose file:</label>
						        			<input type="file" name="asset_file">
						        		</div><br /><br />

						        		<div class="text-right">
						        			<a href="<?=base_url('assets/Asset subgroup/Budgeting - Asset Upload Templates.xlsx')?>" target="_blank">Download Templates</a>
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

				<ul class="nav nav-tabs">
					<li class="active"><a data-toggle="tab" href="#asset-subgroup-tab" class="tab-letter">Asset Sub Group</a></li>
   					<li><a data-toggle="tab" class="tab-letter" href="#asset-group-tab">Asset Group</a></li>
  				</ul>
  				<div class="tab-content">

  					<div id="asset-subgroup-tab" class="tab-pane fade in active">
    					<br>

    					<div class="row">
	    					<div class="col-md-5">
								<div id="add-btn">
									<a href="" class="btn btn-success btn-xs" data-toggle="modal" data-target="#modal-sub-group">+ Add Asset Sub Group</a>

									<div id="modal-sub-group" class="modal fade modal-confirm" role="dialog">
										<div class="modal-dialog modal-sm">
										    <div class="modal-content">
										    	<div class="modal-header">
										        	<button type="button" class="close" data-dismiss="modal">&times;</button>
										       		<strong>Add Asset Subgroup</strong>
										      	</div>
										      	<div class="modal-body">
										        	<form method="POST" action="<?=base_url('admin/add-asset-subgroup')?>" enctype="multipart/form-data" id="add-asset-group">

										        		<div class="form-group">
										        			<label>Asset Group:</label>
										        			<select name="asset_group" class="form-control">
										        				<option value="">Select...</option>

											        			<?php foreach($asset_group as $row):?>

											        				<option value="<?=encode($row->ag_id)?>"><?=$row->ag_name?></option>

											        			<?php endforeach;?>

										        			</select>
										        		</div>

										        		<div class="form-group">
										        			<label>Asset Subgroup Name:</label>
										        			<input type="text" class="form-control input-sm" name="asset_subgroup">
										        		</div>

										        		<div class="form-group">
										        			<label>Asset Subgroup Price:</label>
										        			<input type="text" class="form-control input-sm" name="asset_subgroup_price">
										        		</div>

										        		<div class="form-group">
										        			<label>Asset Useful Life (Year):</label>
										        			<input type="text" class="form-control input-sm" name="asset_useful_life">
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

							</div>
							<div class="col-md-7">
								<div class="text-right">
									<a href="<?=base_url('admin/download-assets/')?>" target="_blank"><span class="glyphicon glyphicon-download-alt"></span>&nbsp;&nbsp; Download Assets</a>
								</div>
							</div>
						</div>

						<table class="table table-hover" id="tbl-asset-subgroup">
							<thead>
								<tr>
									<th>Asset Subgroup</th>
									<th>Asset Group</th>
									<th>Price</th>
									<th>Useful life</th>
									<th>Status</th>
									<th>Action</th>
								</tr>
							</thead>
							<tbody>

								<?php
									foreach($sub_group as $row_sub):
								?>
								
								<tr>
									<td><?=$row_sub->asg_name?></td>
									<td><?=$row_sub->ag_name?></td>
									<td class="text-right"><?=number_format($row_sub->asg_price, 2)?></td>
									<td class="text-center"><?=$row_sub->asg_lifespan?></td>
									<td class="text-center"><?=$row_sub->asg_status == 0 ? '<span class="badge badge-info">INACTIVE</span>' : '<span class="badge badge-warning">ACTIVE</span>'?></td>
									<td><a href="" class="btn btn-success btn-xs edit-asset-subgroup" data-id="<?=encode($row_sub->asg_id)?>">View</a></td>
								</tr>

								<?php endforeach;?>

							</tbody>
						</table>

						<div id="modal-edit-asset-subgroup" class="modal fade" role="dialog">
							<div class="modal-dialog modal-sm">
							    <div class="modal-content">
							    	<div class="modal-header">
							        	<button type="button" class="close" data-dismiss="modal">&times;</button>
							       		<strong>Update Asset Group</strong>
							      	</div>
							      	<div class="modal-body">
							        	<form method="POST" action="<?=base_url('admin/update-asset-subgroup')?>" enctype="multipart/form-data" id="update-asset-subgroup">
							        		<input type="hidden" id="id" name="id">

							        		<div class="form-group">
							        			<label>Asset Group:</label>
							        			<select name="asset-group" class="form-control" id="edit-ag">
							        				<option value="">Select...</option>
							        			</select>
							        		</div>

							        		<div class="form-group">
							        			<label>Asset Subgroup Name:</label>
							        			<input type="text" class="form-control input-sm" name="asset-subgroup" id="edit-asset-subgroup">
							        		</div>

							        		<div class="form-group">
							        			<label>Asset Subgroup Price:</label>
							        			<input type="text" class="form-control input-sm" name="asset-subgroup-price" id="edit-asset-subgroup-price">
							        		</div>

							        		<div class="form-group">
							        			<label>Asset Useful Life (Year):</label>
							        			<input type="text" class="form-control input-sm" name="asset-useful-life" id="edit-asset-subgroup-useful-life">
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

    				<div id="asset-group-tab" class="tab-pane fade">
    					<br>
						<div id="add-btn">
							<a href="" class="btn btn-success btn-xs" data-toggle="modal" data-target="#modal-asset-group">+ Add Asset Group</a>

							<div id="modal-asset-group" class="modal fade modal-confirm" role="dialog">
								<div class="modal-dialog modal-sm">
								    <div class="modal-content">
								    	<div class="modal-header">
								        	<button type="button" class="close" data-dismiss="modal">&times;</button>
								       		<strong>Add Asset Group</strong>
								      	</div>
								      	<div class="modal-body">
								        	<form method="POST" action="<?=base_url('admin/add-asset-group')?>" enctype="multipart/form-data" id="add-asset-group">
								        		<div class="form-group">
								        			<label>Asset Group:</label>
								        			<input type="text" class="form-control input-sm" name="asset_group" id="asset-group">
								        		</div>

								        		<div class="form-group">
								        			<label>Code:</label>
								        			<input type="text" class="form-control input-sm" name="asset_code" id="asset-code">
								        		</div>

								        		<div class="form-group">
								        			<label>Asset Color:</label>
								        			<input type="text" class="form-control input-sm" name="asset_color" id="asset-color">
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
				
						<table class="table table-hover" id="tbl-asset-group">
							<thead>
								<tr>
									<th>Asset Group</th>
									<th>Code</th>
									<th>Asset Color</th>
									<th>Action</th>
								</tr>
							</thead>
							<tbody>

								<?php
									foreach($asset_group as $row):
								?>
								
								<tr>
									<td><?=$row->ag_name?></td>
									<td><?=$row->ag_gl_code?></td>
									<td><?=$row->ag_color?></td>
									<td><a href="" class="btn btn-success btn-xs edit-asset-group" data-id="<?=encode($row->ag_id)?>">View</a></td>
								</tr>

								<?php endforeach;?>

							</tbody>
						</table>

						<div id="modal-edit-asset-group" class="modal fade" role="dialog">
							<div class="modal-dialog modal-sm">
							    <div class="modal-content">
							    	<div class="modal-header">
							        	<button type="button" class="close" data-dismiss="modal">&times;</button>
							       		<strong>Update Asset Group</strong>
							      	</div>
							      	<div class="modal-body">
							        	<form method="POST" action="<?=base_url('admin/update-asset-group')?>" enctype="multipart/form-data" id="update-asset-group">
							        		<input type="hidden" id="id" name="id">

							        		<div class="form-group">
							        			<label>Asset Group:</label>
							        			<input type="text" class="form-control input-sm" name="asset_group" id="edit-asset-group">
							        		</div>

							        		<div class="form-group">
							        			<label>Code:</label>
							        			<input type="text" class="form-control input-sm" name="asset_code" id="edit-asset-code">
							        		</div>

							        		<div class="form-group">
							        			<label>Asset Color:</label>
							        			<input type="text" class="form-control input-sm" name="asset_color" id="edit-asset-color">
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
				</div>
			</div>