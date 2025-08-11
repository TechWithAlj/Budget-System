			
			<div class="col-lg-12" id="content">
				<div id="breadcrumb-div">
					<ul class="breadcrumb">
					    <li><a href="<?=base_url('unit')?>"><span class="glyphicon glyphicon-home"></span>&nbsp;Dashboard</a></li>
					    <li class="active">Employees</li>
					</ul>
				</div>
				<?php
					if($this->session->flashdata('message') != "" ){
						echo $this->session->flashdata('message');
					}
				?>
				<div id="add-btn">

					<div class="row">
						<?php if($budget_status == 1):?>
							<div class="col-lg-1">
								<a href="" class="btn btn-success btn-xs" data-toggle="modal" data-target="#modal-employee">+ Add Employee</a>

								<div id="modal-employee" class="modal fade modal-confirm" role="dialog">
									<div class="modal-dialog modal-sm">
									    <div class="modal-content">
									    	<div class="modal-header">
									        	<button type="button" class="close" data-dismiss="modal">&times;</button>
									       		<strong>Add Employee</strong>
									      	</div>
									      	<div class="modal-body">
									        	<form method="POST" action="<?=base_url('unit/add-employee')?>" enctype="multipart/form-data" id="add-employee">

									        		<input type="hidden" name="year" value="<?=$year?>">

									        		<div class="form-group">
									        			<label>First Name</label>
									        			<input type="text" class="form-control input-sm" name="fname" id="group">
									        		</div>

									        		<div class="form-group">
									        			<label>Last Name</label>
									        			<input type="text" class="form-control input-sm" name="lname" id="group">
									        		</div>


									        		<div class="form-group">
									        			<label>Type</label>
									        			<select name="type" id="emp-type" class="form-control">
									        				<option value="">Select...</option>

										        			<?php foreach($type as $row):?>

										        				<option value="<?=encode($row->emp_type_id)?>"><?=$row->emp_type_name?></option>

										        			<?php endforeach;?>

									        			</select>
									        		</div>

									        		<div class="form-group">
									        			<label>Employee No.</label>
									        			<input type="text" class="form-control input-sm" name="emp_no" id="emp-no">
									        		</div>

									        		<div class="form-group">
									        			<label>Basic Salary</label>
									        			<input type="text" class="form-control input-sm" name="salary" id="group">
									        		</div>

									        		<div class="form-group">
									        			<label>Rank</label>
									        			<select name="rank" class="form-control">
									        				<option value="">Select...</option>

										        			<?php foreach($rank as $row):?>

										        				<option value="<?=encode($row->rank_id)?>"><?=$row->rank_name?></option>

										        			<?php endforeach;?>

									        			</select>
									        		</div>

									        		<div class="form-group">
									        			<label>Cost Center</label>
									        			<select name="cost_center" id="emp-cost-center" class="form-control">
									        				<option value="">Select...</option>

										        			<?php foreach($cost_center as $row):?>

										        				<option value="<?=encode($row->cost_center_id)?>"><?=$row->cost_center_desc?></option>

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
							</div>

							<div class="col-lg-2">
								<a href="" class="btn btn-info btn-xs" data-toggle="modal" data-backdrop="static" data-target="#modal-upload-employees">+ Upload Employees</a>

								<div id="modal-upload-employees" class="modal fade" role="dialog">
									<div class="modal-dialog modal-sm">
									    <div class="modal-content">
									    	<div class="modal-header">
									        	<button type="button" class="close" data-dismiss="modal">&times;</button>
									       		<strong>Upload Employees</strong>
									      	</div>
									      	<div class="modal-body">
									        	<form method="POST" action="<?=base_url('unit/upload-employees')?>" enctype="multipart/form-data" id="upload-employees">
									        		<input type="hidden" name="year" value="<?=$year?>">
									        		<div class="form-group">
									        			<label>Choose file:</label>
									        			<input type="file" name="employee_file">
									        		</div><br /><br />

									        		<div class="text-right">
									        			<a href="<?=base_url('assets/employee/Budgeting - Employee Templates.xlsx')?>" target="_blank"><span class="glyphicon glyphicon-download-alt"></span>&nbsp;&nbsp;Download Employee Templates</a>
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
				                        <div class="input-group input-append date" id="emp-trans-year">
				                            <input type="text" name="month" id="emp-year" class="form-control input-sm" placeholder="Pick year" value="<?=$year?>">
				                            <span class="input-group-addon add-on"><span class="glyphicon glyphicon-calendar"></span></span>
				                        </div>
				                    </div>
								</div>
							</div>

							<div class="col-lg-7">
								<div class="text-right">
									<a href="<?=base_url('unit/download-employees/' . $year)?>" target="_blank"><span class="glyphicon glyphicon-download-alt"></span>&nbsp;&nbsp; Download Employees</a>
								</div>
							</div>
						<?php else:?>
							
							<div class="col-lg-2">
								<div class="form-group">
									<div class="date">
				                        <div class="input-group input-append date" id="emp-trans-year">
				                            <input type="text" name="month" id="emp-year" class="form-control input-sm" placeholder="Pick year" value="<?=$year?>">
				                            <span class="input-group-addon add-on"><span class="glyphicon glyphicon-calendar"></span></span>
				                        </div>
				                    </div>
								</div>
							</div>

							<div class="col-lg-10">
								<div class="text-right">
									<a href="<?=base_url('unit/download-employees/' . $year)?>" target="_blank"><span class="glyphicon glyphicon-download-alt"></span>&nbsp;&nbsp; Download Employees</a>
								</div>
							</div>

						<?php endif;?>
					</div>

					<div id="modal-cancel-emp" class="modal fade modal-confirm" role="dialog">
						<div class="modal-dialog modal-sm">
						    <div class="modal-content">
						    	<div class="modal-header">
						        	<button type="button" class="close" data-dismiss="modal">&times;</button>
						       		<strong>Cancel Employee</strong>
						      	</div>
						      	<div class="modal-body">
						      		<form method="POST" id="cancel-employee" action="<?=base_url('unit/cancel-employee/')?>">
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
							<th>First Name</th>
							<th>Last Name</th>
							<th>Employee No.</th>
							<th class="text-right">Basic Salary</th>
							<th>Unit</th>
							<th>Cost Center</th>
							<th>Type</th>
							<th>Status</th>
							<th>Action</th>
						</tr>
					</thead>
					<tbody>

						<?php
							foreach($employee as $row):

								if($row->emp_year_status == 1){
	                                $badge = '<span class="badge badge-info">Active</span>';
	                                $toggle = '<a href="" class="btn btn-danger btn-xs deactivate-emp" data-id="' . encode($row->emp_year_id) . '">Deactivate</a>';
	                            }elseif($row->emp_year_status == 0){
	                                $badge = '<span class="badge badge-warning">Inactive</span>';
	                                $toggle = '<a href="#" class="btn btn-info btn-xs activate-emp" data-id="' . encode($row->emp_year_id) . '">Activate</a>';
	                            }
						?>
						
						<tr>
							<td><?=$row->emp_fname?></td>
							<td><?=$row->emp_lname?></td>
							<td><?=$row->emp_no?></td>
							<td class="text-right"><?=number_format($row->basic_salary)?></td>
							<td><?=$row->company_unit_name?></td>
							<td><?=$row->cost_center_desc?></td>
							<td><?=$row->emp_type_name?></td>
							<td class="text-center"><?=$badge?></td>
							<td>
								<?php if($budget_status == 1):?>
									
									<a href="" class="btn btn-success btn-xs edit-emp" data-id="<?=encode($row->emp_year_id)?>">View</a>&nbsp;&nbsp;<?=$toggle?>

								<?php endif;?>
							</td>
						</tr>

						<?php endforeach;?>

					</tbody>
				</table>

				<div id="modal-edit-employee" class="modal fade modal-confirm" role="dialog">
					<div class="modal-dialog modal-sm">
					    <div class="modal-content">
					    	<div class="modal-header">
					        	<button type="button" class="close" data-dismiss="modal">&times;</button>
					       		<strong>Update Employee</strong>
					      	</div>
					      	<div class="modal-body">
					        	<form method="POST" action="<?=base_url('unit/update-employee')?>" enctype="multipart/form-data" id="update-employee">
					        		<input type="hidden" name="id" id="id">
					        		<div class="form-group">
					        			<label>First Name</label>
					        			<input type="text" class="form-control input-sm" name="fname" id="edit-emp-fname">
					        		</div>

					        		<div class="form-group">
					        			<label>Last Name</label>
					        			<input type="text" class="form-control input-sm" name="lname" id="edit-emp-lname">
					        		</div>


					        		<div class="form-group">
					        			<label>Type: <span id="edit-emp-type"></span></label>
					        		</div>

					        		<div class="form-group">
					        			<label>Employee No.</label>
					        			<input type="text" class="form-control input-sm" name="emp_no" id="edit-emp-no">
					        		</div>

					        		<div class="form-group">
					        			<label>Basic Salary</label>
					        			<input type="text" class="form-control input-sm" name="salary" id="edit-emp-salary">
					        		</div>

					        		<div class="form-group">
					        			<label>Rank</label>
					        			<select name="rank" class="form-control" id="edit-emp-rank">
					        				<option value="">Select...</option>
					        			</select>
					        		</div>

					        		<div class="form-group">
					        			<label>Cost Center</label>
					        			<select name="cost_center" id="edit-emp-cost-center" class="form-control">
					        				<option value="">Select...</option>
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

				<div id="modal-deactivate-employee" class="modal fade modal-confirm" role="dialog">
					<div class="modal-dialog modal-sm">
					    <div class="modal-content">
					    	<div class="modal-header">
					        	<button type="button" class="close" data-dismiss="modal">&times;</button>
					       		<strong>Deactivate Employee</strong>
					      	</div>
					      	<div class="modal-body">
					        	<form method="POST" action="<?=base_url('unit/deactivate-employee')?>" enctype="multipart/form-data">
					        		<input type="hidden" name="id" value="" id="id">
					        		
					        		<div class="text-center">
					        			<label>Are you sure you want to deactivate this employee?</label><br /><br /><br />
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

				<div id="modal-activate-employee" class="modal fade modal-confirm" role="dialog">
					<div class="modal-dialog modal-sm">
					    <div class="modal-content">
					    	<div class="modal-header">
					        	<button type="button" class="close" data-dismiss="modal">&times;</button>
					       		<strong>Activate Employee</strong>
					      	</div>
					      	<div class="modal-body">
					        	<form method="POST" action="<?=base_url('unit/activate-employee')?>" enctype="multipart/form-data">
					        		<input type="hidden" name="id" value="" id="id">
					        		
					        		<div class="text-center">
					        			<label>Are you sure you want to activate this employee?</label><br /><br /><br />
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