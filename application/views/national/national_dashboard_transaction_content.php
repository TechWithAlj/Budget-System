			
			<div class="col-lg-12" id="content">
				<div id="breadcrumb-div">
					<ul class="breadcrumb">
					    <li><a href="<?=base_url('national')?>"><span class="glyphicon glyphicon-home"></span>&nbsp;Dashboard</a></li>
					</ul>
				</div>

				<?php
					if($this->session->flashdata('message') != "" ){
						echo $this->session->flashdata('message');
					}
				?>

				<div class="pull-right">
					<strong><a href="<?=base_url('dashboard/view-dashboard/' . encode('NATIONAL') . '/' . encode($year))?>" target="_blank" class="text-success"><i class="glyphicon glyphicon-stats"></i> NATIONAL DASHBOARD</a></strong>&nbsp;&nbsp;&nbsp;&nbsp;

					<strong><a href="<?=base_url('dashboard/view-pdf/' . encode('NATIONAL') . '/' . encode($year))?>" target="_blank" class="text-info"><i class="glyphicon glyphicon-file"></i> NATIONAL PDF</a></strong>
				</div>

				<ul class="nav nav-tabs">
   					<li class="active"><a data-toggle="tab" class="tab-letter" href="#bc-tab">BC</a></li>
   					<li><a data-toggle="tab" href="#region-tab" class="tab-letter">Regional</a></li>
				    <li><a data-toggle="tab" href="#unit-tab" class="tab-letter">Unit</a></li>
  				</ul>

  				<div class="tab-content">
    				<div id="bc-tab" class="tab-pane fade in active">
    					<br>

    					<div id="add-btn">
							<a href="" class="btn btn-info btn-xs" data-toggle="modal" data-backdrop="static" data-target="#modal-dashboard-bc-trans">+ Process BC Dashboard</a><br /><br /><br />

							<div id="modal-dashboard-bc-trans" class="modal fade" role="dialog">
								<div class="modal-dialog modal-sm">
								    <div class="modal-content">
								    	<div class="modal-header">
								        	<button type="button" class="close" data-dismiss="modal">&times;</button>
								       		<strong>Process BC Dashboard</strong>
								      	</div>

								      	<div class="modal-body">
								        	<form method="POST" action="<?=base_url('dashboard/add-bc-trans')?>" enctype="multipart/form-data" id="upload-materials">

								        		<input type="hidden" name="year" value="<?=$year?>">

								        		<div class="form-group">
								        			<label>Business Center:</label>
								        			<select class="form-control" name="bc">
								        				<option value="">Select BC...</option>
								        				
								        				<?php foreach($bc as $row_bc):?>

								        					<option value="<?=encode($row_bc->bc_id)?>"><?=$row_bc->bc_name?></option>

								        				<?php endforeach;?>

								        			</select><br />
								        		</div>

								        		<div class="btn-update">
								        			<button type="submit" class="btn btn-info btn-sm pull-right">Submit</button><br>
								        		</div>
								        	</form>
								      	</div>
								    </div>
								</div>
							</div>
						</div>

						<table class="table table-hover" id="tbl-dashboard-bc">
							<thead>
								<tr>
									<th>Business Center</th>
									<th>Year</th>
									<th>Process By</th>
									<th>Started</th>
									<th>Processed</th>
									<th>End</th>
									<th>Status</th>
									<th>Action</th>
								</tr>
							</thead>
							<tbody>

								<?php
									foreach($trans_bc as $row_trans_bc):
										$action = '';
										if($row_trans_bc->dashboard_trans_status_id == 1){
			                                $badge = '<span class="badge badge-warning">QUEUE</span>';
			                                /*$action = '<a href="" class="text-danger terminate-bc-trans" data-id="' . encode($row_trans_bc->dashboard_bc_trans_id) . '"><i class="glyphicon glyphicon-remove"></i></a>';*/
			                            }elseif($row_trans_bc->dashboard_trans_status_id == 2){
			                                $badge = '<span class="badge badge-info">PROCESSING</span>';
			                                /*$action = '<a href="" class="text-danger terminate-bc-trans" data-id="' . encode($row_trans_bc->dashboard_bc_trans_id) . '"><i class="glyphicon glyphicon-remove"></i></a>';*/
			                            }elseif($row_trans_bc->dashboard_trans_status_id == 3){
			                                $badge = '<span class="badge badge-success">COMPLETED</span>';
			                                $action = '<a href="' . base_url('dashboard/view-dashboard/' . encode('BC') . '/' . encode($row_trans_bc->dashboard_bc_trans_year) . '/' . encode($row_trans_bc->dashboard_bc_trans_id)) . '" target="_blank" class="text-success"><i class="glyphicon glyphicon-stats"></i></a>&nbsp;&nbsp;<a href="' . base_url('dashboard/view-pdf/' . encode('BC') . '/' . encode($row_trans_bc->dashboard_bc_trans_year) . '/' . encode($row_trans_bc->dashboard_bc_trans_id)) . '" target="_blank" class="text-info"><i class="glyphicon glyphicon-file"></i></a>';
			                            }elseif($row_trans_bc->dashboard_trans_status_id == 4){
			                                $badge = '<span class="badge badge-warning">TERMINATING</span>';
			                                $action = '';
			                            }elseif($row_trans_bc->dashboard_trans_status_id == 5){
			                                $badge = '<span class="badge badge-danger">TERMINATED</span>';
			                                $action = '';
			                            }

								?>

								<tr>
									<td><?=$row_trans_bc->bc_name?></td>
									<td><?=$row_trans_bc->dashboard_bc_trans_year?></td>
									<td><?=$row_trans_bc->user_lname . ', ' . $row_trans_bc->user_fname?></td>
									<td><?=$row_trans_bc->dashboard_bc_trans_added?></td>
									<td><?=$row_trans_bc->dashboard_bc_trans_process?></td>
									<td><?=$row_trans_bc->dashboard_bc_trans_end?></td>
									<td><?=$badge?></td>
									<td><?=$action?></td>
								</tr>

								<?php endforeach;?>
							</tbody>
						</table>

					</div>

					<div id="region-tab" class="tab-pane fade">
    					<br>
						

						<table class="table table-hover" id="tbl-dashboard-region">
							<thead>
								<tr>
									<th>Region</th>
									<th>Year</th>
									<th>Completed</th>
									<th>Action</th>
								</tr>
							</thead>
							<tbody>

								<?php
									foreach($trans_region as $row_trans_region):
										$count_completed = $row_trans_region->count_completed;

										$action = '';
										if($count_completed > 0){
											$action = '<a href="' . base_url('dashboard/view-dashboard/' . encode('REGIONAL') . '/' . encode($year) . '/' . encode($row_trans_region->region_id)) . '" target="_blank" class="text-success"><i class="glyphicon glyphicon-stats"></i></a>&nbsp;&nbsp;<a href="' . base_url('dashboard/view-pdf/' . encode('REGIONAL') . '/' . encode($year) . '/' . encode($row_trans_region->region_id)) . '" target="_blank" class="text-info"><i class="glyphicon glyphicon-file"></i></a>';
										}
										

								?>

									<tr>
										<td><?=$row_trans_region->region_name?></td>
										<td><?=$year?></td>
										<td><?=$count_completed?></td>
										<td><?=$action?></td>
									</tr>

								<?php endforeach;?>
							</tbody>
						</table>
					</div>

					<div id="unit-tab" class="tab-pane fade">
    					<br>

    					<div id="add-btn">
							<a href="" class="btn btn-info btn-xs" data-toggle="modal" data-backdrop="static" data-target="#modal-dashboard-unit-trans">+ Process Unit Dashboard</a><br />

							<div id="modal-dashboard-unit-trans" class="modal fade" role="dialog">
								<div class="modal-dialog modal-sm">
								    <div class="modal-content">
								    	<div class="modal-header">
								        	<button type="button" class="close" data-dismiss="modal">&times;</button>
								       		<strong>Process Unit Dashboard</strong>
								      	</div>

								      	<div class="modal-body">
								        	<form method="POST" action="<?=base_url('dashboard/add-unit-trans')?>" enctype="multipart/form-data" id="upload-materials">
								        		
								        		<input type="hidden" name="year" value="<?=$year?>">

								        		<div class="form-group">
								        			<label>Unit:</label>
								        			<select class="form-control" name="unit">
								        				<option value="">Select Unit...</option>
								        				
								        				<?php foreach($unit as $row_unit):?>

								        					<option value="<?=encode($row_unit->company_unit_id)?>"><?=$row_unit->company_unit_name?></option>

								        				<?php endforeach;?>

								        			</select>
								        		</div>

								        		<div class="btn-update">
								        			<button type="submit" class="btn btn-info btn-sm pull-right">Submit</button><br>
								        		</div>
								        	</form>
								      	</div>
								    </div>
								</div>
							</div>
						</div>

						<table class="table table-hover" id="tbl-dashboard-unit">
							<thead>
								<tr>
									<th>Unit</th>
									<th>Year</th>
									<th>Process By</th>
									<th>Started</th>
									<th>End</th>
									<th>Status</th>
									<th>Action</th>
								</tr>
							</thead>
							<tbody>

								<?php
									foreach($trans_unit as $row_trans_unit):
										$action = '';
										if($row_trans_unit->dashboard_trans_status_id == 1){
			                                $badge = '<span class="badge badge-warning">QUEUE</span>';
			                                /*$action = '<a href="" class="text-danger terminate-bc-trans" data-id="' . encode($row_trans_unit->dashboard_unit_trans_id) . '"><i class="glyphicon glyphicon-remove"></i></a>';*/
			                            }elseif($row_trans_unit->dashboard_trans_status_id == 2){
			                                $badge = '<span class="badge badge-info">PROCESSING</span>';
			                                /*$action = '<a href="" class="text-danger terminate-bc-trans" data-id="' . encode($row_trans_unit->dashboard_unit_trans_id) . '"><i class="glyphicon glyphicon-remove"></i></a>';*/
			                            }elseif($row_trans_unit->dashboard_trans_status_id == 3){
			                                $badge = '<span class="badge badge-success">COMPLETED</span>';
			                                $action = '<a href="' . base_url('dashboard/view-unit-pdf/' . encode($row_trans_unit->dashboard_unit_trans_id)) . '" target="_blank" class="text-info"><i class="glyphicon glyphicon-file"></i></a>';
			                            }elseif($row_trans_unit->dashboard_trans_status_id == 4){
			                                $badge = '<span class="badge badge-warning">TERMINATING</span>';
			                                $action = '';
			                            }elseif($row_trans_unit->dashboard_trans_status_id == 5){
			                                $badge = '<span class="badge badge-danger">TERMINATED</span>';
			                                $action = '';
			                            }
								?>

									<tr>
										<td><?=$row_trans_unit->cost_center_desc?></td>
										<td><?=$row_trans_unit->dashboard_unit_trans_year?></td>
										<td><?=$row_trans_unit->user_lname . ', ' . $row_trans_unit->user_fname?></td>
										<td><?=$row_trans_unit->dashboard_unit_trans_added?></td>
										<td><?=$row_trans_unit->dashboard_unit_trans_end?></td>
										<td><?=$badge?></td>
										<td><?=$action?></td>
									</tr>

								<?php endforeach;?>
							</tbody>
						</table>
					</div>
				</div>
			</div>