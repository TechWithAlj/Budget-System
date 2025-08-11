			
			<div class="col-lg-12" id="content">
				<div id="breadcrumb-div">
					<ul class="breadcrumb">
					    <li><a href="<?=base_url('admin')?>"><span class="glyphicon glyphicon-home"></span>&nbsp;Dashboard</a></li>
					    <li class="active">Users</li>
					</ul>
				</div>
				<?php
					if($this->session->flashdata('message') != "" ){
						echo $this->session->flashdata('message');
					}
				?>


				<ul class="nav nav-tabs">
   					<li class="active"><a data-toggle="tab" class="tab-letter" href="#admin-user-tab">Admin</a></li>
   					<li><a data-toggle="tab" href="#national-user-tab" class="capex-graph-letter">National</a></li>
   					<li><a data-toggle="tab" href="#region-user-tab" class="capex-graph-letter">Regional Head</a></li>
				    <li><a data-toggle="tab" href="#bc-user-tab" class="capex-graph-letter">BC</a></li>
				    <li><a data-toggle="tab" href="#unit-user-tab" class="capex-graph-letter">Unit</a></li>
				    <li><a data-toggle="tab" href="#ahg-user-tab" class="capex-graph-letter">AHG User</a></li>
				    <li><a data-toggle="tab" href="#prod-user-tab" class="capex-graph-letter">Production User</a></li>
				    <li><a data-toggle="tab" href="#unit-user-tab" class="capex-graph-letter">Sales BOM User</a></li>
  				</ul>

  				<div class="tab-content">
    				<div id="admin-user-tab" class="tab-pane fade in active">
    					<br />
						<div id="add-btn">
							<a href="" class="btn btn-success btn-xs" data-toggle="modal" data-target="#modal-user">+ Add Admin</a>
						</div>
						<div id="modal-user" class="modal fade" role="dialog">
							<div class="modal-dialog modal-sm">
							    <div class="modal-content">
							    	<div class="modal-header">
							        	<button type="button" class="close" data-dismiss="modal">&times;</button>
							       		<strong>Add user</strong>
							      	</div>
							      	<div class="modal-body">
							        	<form method="POST" action="<?=base_url('admin/add-admin-user')?>" enctype="multipart/form-data" id="add-user">
							        		<div class="form-group">
							        			<label>First name</label>
							        			<input type="text" class="form-control input-sm" name="fname">
							        		</div>

							        		<div class="form-group">
							        			<label>Last name</label>
							        			<input type="text" class="form-control input-sm" name="lname">
							        		</div>

							        		<div class="form-group">
							        			<label>Employee ID.</label>
							        			<input type="text" class="form-control input-sm" name="emp_id">
							        		</div>

							        		<div class="form-group">
							        			<label>Email</label>
							        			<input type="text" class="form-control input-sm" name="email">
							        		</div>

							        		<div class="form-group">
							        			<label>Password</label>
							        			<input type="password" class="form-control input-sm" name="password">
							        		</div>

							        		<div class="btn-update">
							        			<button type="submit" class="btn btn-info btn-sm pull-right">Add</button><br>
							        		</div>
							        	</form>
							      	</div>
							    </div>
							</div>
						</div>

						<table class="table table-hover" id="tbl-users">
							<thead>
								<tr>
									<th>First name</th>
									<th>Last name</th>
									<th>Email</th>
									<th>Employee ID</th>
									<th>Action</th>
								</tr>
							</thead>
							<tbody>

								<?php
									foreach($users as $row):
								?>
								
								<tr>
									<td><?=$row->user_fname?></td>
									<td><?=$row->user_lname?></td>
									<td><?=$row->user_email?></td>
									<td><?=$row->employee_no?></td>
									<td><a href="" class="btn btn-warning btn-xs edit-user-admin" data-id="<?=encode($row->user_id)?>"><span class="glyphicon glyphicon-pencil"></span></a>&nbsp;&nbsp;&nbsp;<a href="" class="btn btn-danger btn-xs reset-user" data-id="<?=encode($row->user_id)?>">Reset</a></td>
								</tr>

								<?php endforeach;?>

							</tbody>
						</table>
					</div>

					<div id="national-user-tab" class="tab-pane fade in active">
    					<br />
						<div id="add-btn">
							<a href="" class="btn btn-success btn-xs" data-toggle="modal" data-target="#modal-national-user">+ Add National</a>
						</div>
						<div id="modal-national-user" class="modal fade" role="dialog">
							<div class="modal-dialog modal-sm">
							    <div class="modal-content">
							    	<div class="modal-header">
							        	<button type="button" class="close" data-dismiss="modal">&times;</button>
							       		<strong>Add National User</strong>
							      	</div>
							      	<div class="modal-body">
							        	<form method="POST" action="<?=base_url('admin/add-national-user')?>" enctype="multipart/form-data" id="add-user">
							        		<div class="form-group">
							        			<label>First name</label>
							        			<input type="text" class="form-control input-sm" name="fname">
							        		</div>

							        		<div class="form-group">
							        			<label>Last name</label>
							        			<input type="text" class="form-control input-sm" name="lname">
							        		</div>

							        		<div class="form-group">
							        			<label>Employee ID.</label>
							        			<input type="text" class="form-control input-sm" name="emp_id">
							        		</div>

							        		<div class="form-group">
							        			<label>Email</label>
							        			<input type="text" class="form-control input-sm" name="email">
							        		</div>

							        		<div class="form-group">
							        			<label>Password</label>
							        			<input type="password" class="form-control input-sm" name="password">
							        		</div>

							        		<div class="btn-update">
							        			<button type="submit" class="btn btn-info btn-sm pull-right">Add</button><br>
							        		</div>
							        	</form>
							      	</div>
							    </div>
							</div>
						</div>

						<table class="table table-hover" id="tbl-users-national">
							<thead>
								<tr>
									<th>First name</th>
									<th>Last name</th>
									<th>Email</th>
									<th>Employee ID</th>
									<th>Action</th>
								</tr>
							</thead>
							<tbody>

								<?php
									foreach($user_national as $row):
								?>
								
								<tr>
									<td><?=$row->user_fname?></td>
									<td><?=$row->user_lname?></td>
									<td><?=$row->user_email?></td>
									<td><?=$row->employee_no?></td>
									<td><a href="" class="btn btn-warning btn-xs edit-user-admin" data-id="<?=encode($row->user_id)?>"><span class="glyphicon glyphicon-pencil"></span></a>&nbsp;&nbsp;&nbsp;<a href="" class="btn btn-danger btn-xs reset-user" data-id="<?=encode($row->user_id)?>">Reset</a></td>
								</tr>

								<?php endforeach;?>

							</tbody>
						</table>
					</div>

					<div id="region-user-tab" class="tab-pane fade">
    					<br />
						<div id="add-btn">
							<a href="" class="btn btn-success btn-xs" data-toggle="modal" data-target="#modal-region-user">+ Add Regional Head</a>
						</div>
						<div id="modal-region-user" class="modal fade" role="dialog">
							<div class="modal-dialog modal-sm">
							    <div class="modal-content">
							    	<div class="modal-header">
							        	<button type="button" class="close" data-dismiss="modal">&times;</button>
							       		<strong>Add Regional Head User</strong>
							      	</div>
							      	<div class="modal-body">
							        	<form method="POST" action="<?=base_url('admin/add-regional-user')?>" enctype="multipart/form-data" id="add-regional-user">
							        		<div class="form-group">
							        			<label>First name</label>
							        			<input type="text" class="form-control input-sm" name="fname">
							        		</div>

							        		<div class="form-group">
							        			<label>Last name</label>
							        			<input type="text" class="form-control input-sm" name="lname">
							        		</div>

							        		<div class="form-group">
							        			<label>Employee ID.</label>
							        			<input type="text" class="form-control input-sm" name="emp_id">
							        		</div>

							        		<div class="form-group">
							        			<label>Email</label>
							        			<input type="text" class="form-control input-sm" name="email">
							        		</div>

							        		<div class="form-group">
							        			<label>Password</label>
							        			<input type="password" class="form-control input-sm" name="password">
							        		</div>

							        		<div class="form-group">
							        			<label>Region</label>
							        			<select class="form-control" name="region">
							        				<option value="">Select...</option>

							        				<?php
							        					foreach($region as $row):
							        				?>

							        				<option value="<?=encode($row->region_id)?>"><?=$row->region_name?></option>

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

						<table class="table table-hover" id="tbl-users-region">
							<thead>
								<tr>
									<th>First name</th>
									<th>Last name</th>
									<th>Email</th>
									<th>Employee ID</th>
									<th>Region</th>
									<th>Action</th>
								</tr>
							</thead>
							<tbody>

								<?php
									foreach($user_region as $row):
								?>
								
								<tr>
									<td><?=$row->user_fname?></td>
									<td><?=$row->user_lname?></td>
									<td><?=$row->user_email?></td>
									<td><?=$row->employee_no?></td>
									<td><?=$row->region_name?></td>
									<td><a href="" class="btn btn-warning btn-xs edit-user-region" data-id="<?=encode($row->user_id)?>"><span class="glyphicon glyphicon-pencil"></span></a>&nbsp;&nbsp;&nbsp;<a href="" class="btn btn-danger btn-xs reset-user" data-id="<?=encode($row->user_id)?>">Reset</a></td>
								</tr>

								<?php endforeach;?>

							</tbody>
						</table>
					</div>

					<div id="bc-user-tab" class="tab-pane fade">
    					<br />
						<div id="add-btn">
							<a href="" class="btn btn-success btn-xs" data-toggle="modal" data-target="#modal-bc-user">+ Add BC</a>
						</div>
						<div id="modal-bc-user" class="modal fade" role="dialog">
							<div class="modal-dialog modal-sm">
							    <div class="modal-content">
							    	<div class="modal-header">
							        	<button type="button" class="close" data-dismiss="modal">&times;</button>
							       		<strong>Add Business Center User</strong>
							      	</div>
							      	<div class="modal-body">
							        	<form method="POST" action="<?=base_url('admin/add-bc-user')?>" enctype="multipart/form-data" id="add-bc-user">
							        		<div class="form-group">
							        			<label>First name</label>
							        			<input type="text" class="form-control input-sm" name="fname">
							        		</div>

							        		<div class="form-group">
							        			<label>Last name</label>
							        			<input type="text" class="form-control input-sm" name="lname">
							        		</div>

							        		<div class="form-group">
							        			<label>Employee ID.</label>
							        			<input type="text" class="form-control input-sm" name="emp_id">
							        		</div>

							        		<div class="form-group">
							        			<label>Email</label>
							        			<input type="text" class="form-control input-sm" name="email">
							        		</div>

							        		<div class="form-group">
							        			<label>Password</label>
							        			<input type="password" class="form-control input-sm" name="password">
							        		</div>

							        		<div class="form-group">
							        			<label>Business center</label>
							        			<select class="form-control" name="business">
							        				<option value="">Select...</option>

							        				<?php
							        					foreach($business as $row):
							        				?>

							        				<option value="<?=encode($row->bc_id)?>"><?=$row->bc_name?></option>

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

						<table class="table table-hover" id="tbl-user-bc">
							<thead>
								<tr>
									<th>First name</th>
									<th>Last name</th>
									<th>Email</th>
									<th>Employee ID</th>
									<th>Business Center</th>
									<th>Action</th>
								</tr>
							</thead>
							<tbody>

								<?php
									foreach($user_bc as $row):
								?>
								
								<tr>
									<td><?=$row->user_fname?></td>
									<td><?=$row->user_lname?></td>
									<td><?=$row->user_email?></td>
									<td><?=$row->employee_no?></td>
									<td><?=$row->bc_name?></td>
									<td><a href="" class="btn btn-warning btn-xs edit-user-bc" data-id="<?=encode($row->user_id)?>"><span class="glyphicon glyphicon-pencil"></span></a>&nbsp;&nbsp;&nbsp;<a href="" class="btn btn-danger btn-xs reset-user" data-id="<?=encode($row->user_id)?>">Reset</a></td>
								</tr>

								<?php endforeach;?>

							</tbody>
						</table>
					</div>

					<div id="unit-user-tab" class="tab-pane fade">
    					<br />
						<div id="add-btn">
							<a href="" class="btn btn-success btn-xs" data-toggle="modal" data-target="#modal-unit-user">+ Add Unit</a>
						</div>
						<div id="modal-unit-user" class="modal fade" role="dialog">
							<div class="modal-dialog modal-sm">
							    <div class="modal-content">
							    	<div class="modal-header">
							        	<button type="button" class="close" data-dismiss="modal">&times;</button>
							       		<strong>Add Unit User</strong>
							      	</div>
							      	<div class="modal-body">
							        	<form method="POST" action="<?=base_url('admin/add-unit-user')?>" enctype="multipart/form-data" id="add-user">
							        		<div class="form-group">
							        			<label>First name</label>
							        			<input type="text" class="form-control input-sm" name="fname">
							        		</div>

							        		<div class="form-group">
							        			<label>Last name</label>
							        			<input type="text" class="form-control input-sm" name="lname">
							        		</div>

							        		<div class="form-group">
							        			<label>Employee ID.</label>
							        			<input type="text" class="form-control input-sm" name="emp_id">
							        		</div>

							        		<div class="form-group">
							        			<label>Email</label>
							        			<input type="text" class="form-control input-sm" name="email">
							        		</div>

							        		<div class="form-group">
							        			<label>Password</label>
							        			<input type="password" class="form-control input-sm" name="password">
							        		</div>

							        		<div class="form-group">
							        			<label>Unit</label>
							        			<select class="form-control" name="unit">
							        				<option value="">Select...</option>

							        				<?php
							        					foreach($unit as $row_unit):
							        				?>

							        				<option value="<?=encode($row_unit->company_unit_id)?>"><?=$row_unit->company_unit_name?></option>

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

						<table class="table table-hover" id="tbl-user-unit">
							<thead>
								<tr>
									<th>First name</th>
									<th>Last name</th>
									<th>Email</th>
									<th>Employee ID</th>
									<th>Unit</th>
									<th>Action</th>
								</tr>
							</thead>
							<tbody>

								<?php
									foreach($user_unit as $row):
								?>
								
								<tr>
									<td><?=$row->user_fname?></td>
									<td><?=$row->user_lname?></td>
									<td><?=$row->user_email?></td>
									<td><?=$row->employee_no?></td>
									<td><?=$row->company_unit_name?></td>
									<td><a href="" class="btn btn-warning btn-xs edit-user-unit" data-id="<?=encode($row->user_id)?>"><span class="glyphicon glyphicon-pencil"></span></a>&nbsp;&nbsp;&nbsp;<a href="" class="btn btn-danger btn-xs reset-user" data-id="<?=encode($row->user_id)?>">Reset</a></td>
								</tr>

								<?php endforeach;?>

							</tbody>
						</table>
					</div>

					<div id="ahg-user-tab" class="tab-pane fade">
    					<br />
						<div id="add-btn">
							<a href="" class="btn btn-success btn-xs" data-toggle="modal" data-target="#modal-ahg-user">+ Add AHG</a>
						</div>
						<div id="modal-ahg-user" class="modal fade" role="dialog">
							<div class="modal-dialog modal-sm">
							    <div class="modal-content">
							    	<div class="modal-header">
							        	<button type="button" class="close" data-dismiss="modal">&times;</button>
							       		<strong>Add AHG User</strong>
							      	</div>
							      	<div class="modal-body">
							        	<form method="POST" action="<?=base_url('admin/add-ahg-user')?>" enctype="multipart/form-data" id="add-user">
							        		<div class="form-group">
							        			<label>First name</label>
							        			<input type="text" class="form-control input-sm" name="fname">
							        		</div>

							        		<div class="form-group">
							        			<label>Last name</label>
							        			<input type="text" class="form-control input-sm" name="lname">
							        		</div>

							        		<div class="form-group">
							        			<label>Employee ID.</label>
							        			<input type="text" class="form-control input-sm" name="emp_id">
							        		</div>

							        		<div class="form-group">
							        			<label>Email</label>
							        			<input type="text" class="form-control input-sm" name="email">
							        		</div>

							        		<div class="form-group">
							        			<label>Password</label>
							        			<input type="password" class="form-control input-sm" name="password">
							        		</div>

							        		
							        		<div class="btn-update">
							        			<button type="submit" class="btn btn-info btn-sm pull-right">Add</button><br>
							        		</div>
							        	</form>
							      	</div>
							    </div>
							</div>
						</div>

						<table class="table table-hover" id="tbl-user-ahg">
							<thead>
								<tr>
									<th>First name</th>
									<th>Last name</th>
									<th>Email</th>
									<th>Employee ID</th>
									<th>Action</th>
								</tr>
							</thead>
							<tbody>

								<?php
									foreach($ahg as $row):
								?>
								
								<tr>
									<td><?=$row->user_fname?></td>
									<td><?=$row->user_lname?></td>
									<td><?=$row->user_email?></td>
									<td><?=$row->employee_no?></td>
									<td><a href="" class="btn btn-warning btn-xs edit-user" data-id="<?=encode($row->user_id)?>"><span class="glyphicon glyphicon-pencil"></span></a>&nbsp;&nbsp;&nbsp;<a href="" class="btn btn-danger btn-xs reset-user" data-id="<?=encode($row->user_id)?>">Reset</a></td>
								</tr>

								<?php endforeach;?>

							</tbody>
						</table>
					</div>

					<div id="prod-user-tab" class="tab-pane fade">
    					<br />
						<div id="add-btn">
							<a href="" class="btn btn-success btn-xs" data-toggle="modal" data-target="#modal-prod-user">+ Add Production</a>
						</div>
						<div id="modal-prod-user" class="modal fade" role="dialog">
							<div class="modal-dialog modal-sm">
							    <div class="modal-content">
							    	<div class="modal-header">
							        	<button type="button" class="close" data-dismiss="modal">&times;</button>
							       		<strong>Add Production User</strong>
							      	</div>
							      	<div class="modal-body">
							        	<form method="POST" action="<?=base_url('admin/add-prod-user')?>" enctype="multipart/form-data" id="add-prod-user">
							        		<div class="form-group">
							        			<label>First name</label>
							        			<input type="text" class="form-control input-sm" name="fname">
							        		</div>

							        		<div class="form-group">
							        			<label>Last name</label>
							        			<input type="text" class="form-control input-sm" name="lname">
							        		</div>

							        		<div class="form-group">
							        			<label>Employee ID.</label>
							        			<input type="text" class="form-control input-sm" name="emp_id">
							        		</div>

							        		<div class="form-group">
							        			<label>Email</label>
							        			<input type="text" class="form-control input-sm" name="email">
							        		</div>

							        		<div class="form-group">
							        			<label>Password</label>
							        			<input type="password" class="form-control input-sm" name="password">
							        		</div>

							        		
							        		<div class="btn-update">
							        			<button type="submit" class="btn btn-info btn-sm pull-right">Add</button><br>
							        		</div>
							        	</form>
							      	</div>
							    </div>
							</div>
						</div>

						<table class="table table-hover" id="tbl-user-prod">
							<thead>
								<tr>
									<th>First name</th>
									<th>Last name</th>
									<th>Email</th>
									<th>Employee ID</th>
									<th>Action</th>
								</tr>
							</thead>
							<tbody>

								<?php
									foreach($prod as $row):
								?>
								
								<tr>
									<td><?=$row->user_fname?></td>
									<td><?=$row->user_lname?></td>
									<td><?=$row->user_email?></td>
									<td><?=$row->employee_no?></td>
									<td><a href="" class="btn btn-warning btn-xs edit-user" data-id="<?=encode($row->user_id)?>"><span class="glyphicon glyphicon-pencil"></span></a>&nbsp;&nbsp;&nbsp;<a href="" class="btn btn-danger btn-xs reset-user" data-id="<?=encode($row->user_id)?>">Reset</a></td>
								</tr>

								<?php endforeach;?>

							</tbody>
						</table>
					</div>
				</div>

					

				<div id="modal-edit-user-admin" class="modal fade" role="dialog">
					<div class="modal-dialog modal-sm">
					    <div class="modal-content">
					    	<div class="modal-header">
					        	<button type="button" class="close" data-dismiss="modal">&times;</button>
					       		<strong>Update User</strong>
					      	</div>
					      	<div class="modal-body">
					        	<form method="POST" action="<?=base_url('admin/update-user-admin')?>" enctype="multipart/form-data" id="update-user-admin">
					        		<input type="hidden" id="id" name="id">
					        		<div class="form-group">
					        			<label>First name</label>
					        			<input type="text" class="form-control input-sm" name="fname" id="fname">
					        		</div>

					        		<div class="form-group">
					        			<label>Last name</label>
					        			<input type="text" class="form-control input-sm" name="lname" id="lname">
					        		</div>

					        		<div class="form-group">
					        			<label>Employee ID.</label>
					        			<input type="text" class="form-control input-sm" name="emp_id" id="emp_id">
					        		</div>

					        		<div class="form-group">
					        			<label>Email</label>
					        			<input type="text" class="form-control input-sm" name="email" id="email">
					        		</div>

					        		<div class="btn-update">
					        			<button type="submit" class="btn btn-info btn-sm pull-right">Update</button><br>
					        		</div>
					        	</form>
					      	</div>
					    </div>
					</div>
				</div>

				<div id="modal-edit-user-bc" class="modal fade" role="dialog">
					<div class="modal-dialog modal-sm">
					    <div class="modal-content">
					    	<div class="modal-header">
					        	<button type="button" class="close" data-dismiss="modal">&times;</button>
					       		<strong>Update User</strong>
					      	</div>
					      	<div class="modal-body">
					        	<form method="POST" action="<?=base_url('admin/update-user-bc')?>" enctype="multipart/form-data" id="update-user-bc">
					        		<input type="hidden" id="id" name="id">
					        		<div class="form-group">
					        			<label>First name</label>
					        			<input type="text" class="form-control input-sm" name="fname" id="fname">
					        		</div>

					        		<div class="form-group">
					        			<label>Last name</label>
					        			<input type="text" class="form-control input-sm" name="lname" id="lname">
					        		</div>

					        		<div class="form-group">
					        			<label>Employee ID.</label>
					        			<input type="text" class="form-control input-sm" name="emp_id" id="emp_id">
					        		</div>

					        		<div class="form-group">
					        			<label>Email</label>
					        			<input type="text" class="form-control input-sm" name="email" id="email">
					        		</div>

					        		<div class="form-group">
					        			<label>Business Center</label>
					        			<select class="form-control input-sm" name="bc" id="bc">
					        				
					        			</select>
					        		</div>



					        		<div class="btn-update">
					        			<button type="submit" class="btn btn-info btn-sm pull-right">Update</button><br>
					        		</div>
					        	</form>
					      	</div>
					    </div>
					</div>
				</div>

				<div id="modal-edit-user-region" class="modal fade" role="dialog">
					<div class="modal-dialog modal-sm">
					    <div class="modal-content">
					    	<div class="modal-header">
					        	<button type="button" class="close" data-dismiss="modal">&times;</button>
					       		<strong>Update User</strong>
					      	</div>
					      	<div class="modal-body">
					        	<form method="POST" action="<?=base_url('admin/update-user-region')?>" enctype="multipart/form-data" id="update-user-region">
					        		<input type="hidden" id="id" name="id">
					        		<div class="form-group">
					        			<label>First name</label>
					        			<input type="text" class="form-control input-sm" name="fname" id="fname">
					        		</div>

					        		<div class="form-group">
					        			<label>Last name</label>
					        			<input type="text" class="form-control input-sm" name="lname" id="lname">
					        		</div>

					        		<div class="form-group">
					        			<label>Employee ID.</label>
					        			<input type="text" class="form-control input-sm" name="emp_id" id="emp_id">
					        		</div>

					        		<div class="form-group">
					        			<label>Email</label>
					        			<input type="text" class="form-control input-sm" name="email" id="email">
					        		</div>

					        		<div class="form-group">
					        			<label>Region</label>
					        			<select class="form-control input-sm" name="region" id="region">
					        				
					        			</select>
					        		</div>
					        		
					        		<div class="btn-update">
					        			<button type="submit" class="btn btn-info btn-sm pull-right">Update</button><br>
					        		</div>
					        	</form>
					      	</div>
					    </div>
					</div>
				</div>

				<div id="modal-edit-user-unit" class="modal fade" role="dialog">
					<div class="modal-dialog modal-sm">
					    <div class="modal-content">
					    	<div class="modal-header">
					        	<button type="button" class="close" data-dismiss="modal">&times;</button>
					       		<strong>Update User</strong>
					      	</div>
					      	<div class="modal-body">
					        	<form method="POST" action="<?=base_url('admin/update-user-unit')?>" enctype="multipart/form-data" id="update-user-unit">
					        		<input type="hidden" id="id" name="id">
					        		<div class="form-group">
					        			<label>First name</label>
					        			<input type="text" class="form-control input-sm" name="fname" id="fname">
					        		</div>

					        		<div class="form-group">
					        			<label>Last name</label>
					        			<input type="text" class="form-control input-sm" name="lname" id="lname">
					        		</div>

					        		<div class="form-group">
					        			<label>Employee ID.</label>
					        			<input type="text" class="form-control input-sm" name="emp_id" id="emp_id">
					        		</div>

					        		<div class="form-group">
					        			<label>Email</label>
					        			<input type="text" class="form-control input-sm" name="email" id="email">
					        		</div>

					        		<div class="form-group">
					        			<label>Unit</label>
					        			<select class="form-control input-sm" name="unit" id="unit">
					        				
					        			</select>
					        		</div>

					        		<div class="btn-update">
					        			<button type="submit" class="btn btn-info btn-sm pull-right">Update</button><br>
					        		</div>
					        	</form>
					      	</div>
					    </div>
					</div>
				</div>

				<div id="modal-reset-user" class="modal fade" role="dialog">
					<div class="modal-dialog modal-sm">
					    <div class="modal-content">
					    	<div class="modal-header">
					        	<button type="button" class="close" data-dismiss="modal">&times;</button>
					       		<strong>Reset password</strong>
					      	</div>
					      	<div class="modal-body">
					        	<form method="POST" action="<?=base_url('admin/update-password')?>" enctype="multipart/form-data" id="update-password">
					        		<input type="hidden" id="id" name="id">
					       
					        		<div class="form-group">
					        			<label>Temporary pasword</label>
					        			<input type="password" class="form-control" name="password">
					        		</div>

					        		<div class="form-group">
					        			<label>Retype pasword</label>
					        			<input type="password" class="form-control" name="password2">
					        		</div>

					        		<div class="btn-update">
					        			<button type="submit" class="btn btn-info btn-sm pull-right">Reset</button><br>
					        		</div>
					        	</form>
					      	</div>
					    </div>
					</div>
				</div>
			</div>