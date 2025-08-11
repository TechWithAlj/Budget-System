			
			<div class="col-lg-12" id="content">
				<div id="breadcrumb-div">
					<ul class="breadcrumb">
					    <li><a href="<?=base_url('business-center')?>"><span class="glyphicon glyphicon-home"></span>&nbsp;Dashboard</a></li>
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
								        	<form method="POST" action="<?=base_url('business-center/add-bc-trans')?>" enctype="multipart/form-data" id="upload-materials">

								        		<input type="hidden" name="year" value="<?=$year?>">

								        		<div class="text-center">
								        			<h1>Are you sure you want to Queue Report?</h1>
								        		</div>

								        		<div class="text-center">
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
									<td><?=$row_trans_bc->dashboard_bc_trans_end?></td>
									<td><?=$badge?></td>
									<td><?=$action?></td>
								</tr>

								<?php endforeach;?>
							</tbody>
						</table>

					</div>
				</div>
			</div>