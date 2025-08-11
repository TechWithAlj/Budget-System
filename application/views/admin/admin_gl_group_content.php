			
			<div class="col-lg-12" id="content">
				<div id="breadcrumb-div">
					<ul class="breadcrumb">
					    <li><a href="<?=base_url('admin')?>"><span class="glyphicon glyphicon-home"></span>&nbsp;Dashboard</a></li>
					    <li class="active">GL Group</li>
					</ul>
				</div>
				<?php
					if($this->session->flashdata('message') != "" ){
						echo $this->session->flashdata('message');
					}
				?>

				<div class="row">
					<div class="col-lg-3">
						<div id="add-btn">
							<a href="" class="btn btn-info btn-xs" data-toggle="modal" data-backdrop="static" data-target="#modal-upload-gl-subgroup">+ Upload GL Subgroup</a>

							<div id="modal-upload-gl-subgroup" class="modal fade" role="dialog">
								<div class="modal-dialog modal-sm">
								    <div class="modal-content">
								    	<div class="modal-header">
								        	<button type="button" class="close" data-dismiss="modal">&times;</button>
								       		<strong>Upload GL Subgroup</strong>
								      	</div>
								      	<div class="modal-body">
								        	<form method="POST" action="<?=base_url('admin/upload-gl-subgroup')?>" enctype="multipart/form-data" id="upload-gl-subgroup">

								        		<div class="form-group">
								        			<label>Choose file:</label>
								        			<input type="file" name="gl_file">
								        		</div><br /><br />

								        		<div class="text-right">
								        			<a href="<?=base_url('assets/GL subgroup/Budgeting - GL Subgroup Upload Templates 2.xlsx')?>" target="_blank"><span class="glyphicon glyphicon-download-alt"></span>&nbsp;&nbsp;Download GL Subgroup Templates</a>
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
					</div>

					<div class="col-lg-9">
						<div class="text-right">
							<a href="<?=base_url('admin/download-gl/')?>" target="_blank"><span class="glyphicon glyphicon-download-alt"></span>&nbsp;&nbsp; Download GL</a>
						</div>
					</div>
				</div>

				<ul class="nav nav-tabs">
					<li class="active"><a data-toggle="tab" href="#gl-subgroup-tab" class="tab-letter">GL Subgroup</a></li>
   					<li><a data-toggle="tab" class="tab-letter" href="#gl-group-tab">GL Group</a></li>
  				</ul>
  				<div class="tab-content">

  					<div id="gl-subgroup-tab" class="tab-pane fade in active">
    					<br>
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
								        	<form method="POST" action="<?=base_url('admin/add-gl-subgroup')?>" enctype="multipart/form-data" id="add-gl-subgroup">

								        		<div class="form-group">
								        			<label>Asset Group:</label>
								        			<select name="gl_group" class="form-control">
								        				<option value="">Select...</option>

									        			<?php foreach($gl_group as $row):?>

									        				<option value="<?=encode($row->gl_group_id)?>"><?=$row->gl_group_name?></option>

									        			<?php endforeach;?>

								        			</select>
								        		</div>

								        		<div class="form-group">
								        			<label>GL Subgroup Name:</label>
								        			<input type="text" class="form-control input-sm" name="gl_subgroup">
								        		</div>

								        		<div class="form-group">
								        			<label>GL Subgroup code:</label>
								        			<input type="text" class="form-control input-sm" name="gl_subgroup_code">
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
				
						<table class="table table-hover" id="tbl-gl-subgroup">
							<thead>
								<tr>
									<th>GL Subgroup</th>
									<th>GL Group</th>
									<th>GL Subgroup Code</th>
									<th>GL Classification</th>
									<th>Status</th>
									<th>Action</th>
								</tr>
							</thead>
							<tbody>

								<?php
									foreach($sub_group as $row_sub):

										if($row_sub->gl_sub_status == 1){
			                                $badge_gl_sub = '<span class="badge badge-info">Active</span>';
			                                $toggle_gl_sub = '<a href="" class="btn btn-danger btn-xs deactivate-gl-sub" data-id="' . encode($row_sub->gl_sub_id) . '">Deactivate</a>';
			                            }elseif($row_sub->gl_sub_status == 0){
			                                $badge_gl_sub = '<span class="badge badge-warning">Inactive</span>';
			                                $toggle_gl_sub = '<a href="#" class="btn btn-info btn-xs activate-gl-sub" data-id="' . encode($row_sub->gl_sub_id) . '">Activate</a>';
			                            }
								?>
								
								<tr>
									<td><?=$row_sub->gl_sub_name?></td>
									<td><?=$row_sub->gl_group_name?></td>
									<td><?=$row_sub->gl_code?></td>
									<td><?=$row_sub->gl_class_name?></td>
									<td class="text-center"><?=$badge_gl_sub?></td>
									<td><a href="" class="btn btn-success btn-xs edit-gl-subgroup" data-id="<?=encode($row_sub->gl_sub_id)?>">View</a>&nbsp;&nbsp;<?=$toggle_gl_sub?></td>
								</tr>

								<?php endforeach;?>

							</tbody>
						</table>

						<div id="modal-edit-gl-subgroup" class="modal fade" role="dialog">
							<div class="modal-dialog modal-sm">
							    <div class="modal-content">
							    	<div class="modal-header">
							        	<button type="button" class="close" data-dismiss="modal">&times;</button>
							       		<strong>Update GL  Subgroup</strong>
							      	</div>
							      	<div class="modal-body">
							        	<form method="POST" action="<?=base_url('admin/update-gl-subgroup')?>" enctype="multipart/form-data" id="update-gl-subgroup">
							        		<input type="hidden" id="id" name="id">

							        		<div class="form-group">
							        			<label>Asset Group:</label>
							        			<select name="gl-group" class="form-control" id="edit-gl-group-select">
							        				<option value="">Select...</option>
							        			</select>
							        		</div>

							        		<div class="form-group">
								        			<label>GL Subgroup Name:</label>
								        			<input type="text" class="form-control input-sm" name="gl-subgroup" id="edit-gl-subgroup">
								        		</div>

								        		<div class="form-group">
								        			<label>GL Subgroup code:</label>
								        			<input type="text" class="form-control input-sm" name="gl-subgroup-code" id="edit-gl-subgroup-code">
								        		</div>

							        		<div class="btn-update">
							        			<button type="submit" class="btn btn-info btn-sm pull-right">Update</button><br>
							        		</div>
							        	</form>
							      	</div>
							    </div>
							</div>
						</div>

						<div id="modal-deactivate-gl-sub" class="modal fade modal-confirm" role="dialog">
							<div class="modal-dialog modal-sm">
							    <div class="modal-content">
							    	<div class="modal-header">
							        	<button type="button" class="close" data-dismiss="modal">&times;</button>
							       		<strong>Deactivate GL Subgroup</strong>
							      	</div>
							      	<div class="modal-body">
							        	<form method="POST" action="<?=base_url('admin/deactivate-gl-subgroup')?>" enctype="multipart/form-data">
							        		<input type="hidden" name="id" value="" id="id">
							        		
							        		<div class="text-center">
							        			<label>Are you sure you want to deactivate this GL Subgroup?</label><br /><br /><br />
							        		</div>

							        		<div class="text-center">
							        			<button type="submit" class="btn btn-info btn-sm">Yes</button>
							        			&nbsp;&nbsp;
							        			<button data-dismiss="modal" class="btn btn-danger btn-sm">No</button>
							        			<br /><br />
							        		</div>
							        	</form>
							      	</div>
							    </div>
							</div>
						</div>

						<div id="modal-activate-gl-sub" class="modal fade modal-confirm" role="dialog">
							<div class="modal-dialog modal-sm">
							    <div class="modal-content">
							    	<div class="modal-header">
							        	<button type="button" class="close" data-dismiss="modal">&times;</button>
							       		<strong>Activate GL Subgroup</strong>
							      	</div>
							      	<div class="modal-body">
							        	<form method="POST" action="<?=base_url('admin/activate-gl-subgroup')?>" enctype="multipart/form-data">
							        		<input type="hidden" name="id" value="" id="id">
							        		
							        		<div class="text-center">
							        			<label>Are you sure you want to activate this GL Subgroup?</label><br /><br /><br />
							        		</div>

							        		<div class="text-center">
							        			<button type="submit" class="btn btn-info btn-sm">Yes</button>
							        			&nbsp;&nbsp;
							        			<button data-dismiss="modal" class="btn btn-danger btn-sm">No</button>
							        			<br /><br />
							        		</div>
							        	</form>
							      	</div>
							    </div>
							</div>
						</div>
					</div>

    				<div id="gl-group-tab" class="tab-pane fade">
    					<br>
						<div id="add-btn">
							<a href="" class="btn btn-success btn-xs" data-toggle="modal" data-target="#modal-gl-group">+ Add GL Group</a>

							<div id="modal-gl-group" class="modal fade modal-confirm" role="dialog">
								<div class="modal-dialog modal-sm">
								    <div class="modal-content">
								    	<div class="modal-header">
								        	<button type="button" class="close" data-dismiss="modal">&times;</button>
								       		<strong>Add GL Group</strong>
								      	</div>
								      	<div class="modal-body">
								        	<form method="POST" action="<?=base_url('admin/add-gl-group')?>" enctype="multipart/form-data" id="add-gl-group">
								        		<div class="form-group">
								        			<label>GL Group:</label>
								        			<input type="text" class="form-control input-sm" name="gl_group">
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
				
						<table class="table table-hover" id="tbl-gl-group">
							<thead>
								<tr>
									<th>GL Group</th>
									<th class="text-center">Status</th>
									<th>Action</th>
								</tr>
							</thead>
							<tbody>

								<?php
									foreach($gl_group as $row):
										if($row->gl_group_status == 1){
			                                $badge_gl_group = '<span class="badge badge-info">Active</span>';
			                                $toggle_gl_group = '<a href="" class="btn btn-danger btn-xs deactivate-gl-group" data-id="' . encode($row->gl_group_id) . '">Deactivate</a>';
			                            }elseif($row->gl_group_status == 0){
			                                $badge_gl_group = '<span class="badge badge-warning">Inactive</span>';
			                                $toggle_gl_group = '<a href="#" class="btn btn-info btn-xs activate-gl-group" data-id="' . encode($row->gl_group_id) . '">Activate</a>';
			                            }
								?>
								
								<tr>
									<td><?=$row->gl_group_name?></td>
									<td class="text-center"><?=$badge_gl_group?></td>
									<td><a href="" class="btn btn-success btn-xs edit-gl-group" data-id="<?=encode($row->gl_group_id)?>">View</a>&nbsp;&nbsp;<?=$toggle_gl_group?></td>
								</tr>

								<?php endforeach;?>

							</tbody>
						</table>

						<div id="modal-edit-gl-group" class="modal fade" role="dialog">
							<div class="modal-dialog modal-sm">
							    <div class="modal-content">
							    	<div class="modal-header">
							        	<button type="button" class="close" data-dismiss="modal">&times;</button>
							       		<strong>Update GL Group</strong>
							      	</div>
							      	<div class="modal-body">
							        	<form method="POST" action="<?=base_url('admin/update-gl-group')?>" enctype="multipart/form-data" id="update-gl-group">
							        		<input type="hidden" id="id" name="id">

							        		<div class="form-group">
							        			<label>GL Group:</label>
							        			<input type="text" class="form-control input-sm" name="gl-group" id="edit-gl-group">
							        		</div>

							        		<div class="btn-update">
							        			<button type="submit" class="btn btn-info btn-sm pull-right">Update</button><br>
							        		</div>
							        	</form>
							      	</div>
							    </div>
							</div>
						</div>

						<div id="modal-deactivate-gl-group" class="modal fade modal-confirm" role="dialog">
							<div class="modal-dialog modal-sm">
							    <div class="modal-content">
							    	<div class="modal-header">
							        	<button type="button" class="close" data-dismiss="modal">&times;</button>
							       		<strong>Deactivate GL Group</strong>
							      	</div>
							      	<div class="modal-body">
							        	<form method="POST" action="<?=base_url('admin/deactivate-gl-group')?>" enctype="multipart/form-data">
							        		<input type="hidden" name="id" value="" id="id">
							        		
							        		<div class="text-center">
							        			<label>Are you sure you want to deactivate this GL Group?</label><br /><br /><br />
							        		</div>

							        		<div class="text-center">
							        			<button type="submit" class="btn btn-info btn-sm">Yes</button>
							        			&nbsp;&nbsp;
							        			<button data-dismiss="modal" class="btn btn-danger btn-sm">No</button>
							        			<br /><br />
							        		</div>
							        	</form>
							      	</div>
							    </div>
							</div>
						</div>

						<div id="modal-activate-gl-group" class="modal fade modal-confirm" role="dialog">
							<div class="modal-dialog modal-sm">
							    <div class="modal-content">
							    	<div class="modal-header">
							        	<button type="button" class="close" data-dismiss="modal">&times;</button>
							       		<strong>Activate GL Group</strong>
							      	</div>
							      	<div class="modal-body">
							        	<form method="POST" action="<?=base_url('admin/activate-gl-group')?>" enctype="multipart/form-data">
							        		<input type="hidden" name="id" value="" id="id">
							        		
							        		<div class="text-center">
							        			<label>Are you sure you want to activate this GL Group?</label><br /><br /><br />
							        		</div>

							        		<div class="text-center">
							        			<button type="submit" class="btn btn-info btn-sm">Yes</button>
							        			&nbsp;&nbsp;
							        			<button data-dismiss="modal" class="btn btn-danger btn-sm">No</button>
							        			<br /><br />
							        		</div>
							        	</form>
							      	</div>
							    </div>
							</div>
						</div>
					</div>
				</div>
			</div>