			
			<div class="col-lg-12" id="content">
				<div id="breadcrumb-div">
					<ul class="breadcrumb">
					    <li><a href="<?=base_url('admin')?>"><span class="glyphicon glyphicon-home"></span>&nbsp;Dashboard</a></li>
					    <li class="active">Materials</li>
					</ul>
				</div>

				<?php
					if($this->session->flashdata('message') != "" ){
						echo $this->session->flashdata('message');
					}
				?>

				<ul class="nav nav-tabs">
   					<li class="active"><a data-toggle="tab" class="tab-letter" href="#bc-tab">BC</a></li>
   					<li><a data-toggle="tab" href="#region-tab" class="tab-letter">Regional</a></li>
  				</ul>

  				<div class="tab-content">
    				<div id="bc-tab" class="tab-pane fade in active">
    					<br>

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
			                                $action = '<a href="" class="text-danger terminate-bc-trans" data-id="' . encode($row_trans_bc->dashboard_bc_trans_id) . '"><i class="glyphicon glyphicon-remove"></i></a>';
			                            }elseif($row_trans_bc->dashboard_trans_status_id == 2){
			                                $badge = '<span class="badge badge-info">PROCESSING</span>';
			                                $action = '<a href="" class="text-danger terminate-bc-trans" data-id="' . encode($row_trans_bc->dashboard_bc_trans_id) . '"><i class="glyphicon glyphicon-remove"></i></a>';
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
						

						<table class="table table-hover" id="tbl-dashboard-unit">
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
				</div>
			</div>