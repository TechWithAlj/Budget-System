
            <div class="col-lg-12" id="content">
				<div id="breadcrumb-div">
					<ul class="breadcrumb">
					    <li><a href="<?=base_url('admin')?>"><span class="glyphicon glyphicon-home"></span>&nbsp;Dashboard</a></li>
					    <li class="active">Commissary Prod Cost</li>
					</ul>
				</div>

				<?php
					if($this->session->flashdata('message') != "" ){
						echo $this->session->flashdata('message');
					}
				?>

				<ul class="nav nav-tabs">
				    <li class="active">
                        <a data-toggle="tab"
                        href="#prod-config-tab"
                        class="tab-letter">
                        Commissary & Liempo Production
                        </a>
                    </li>
   					<li>
                        <a data-toggle="tab"
                        class="tab-letter"
                        href="#prod-group-tab">
                        Production Group
                        </a>
                    </li>
   					<li>
                        <a data-toggle="tab"
                        class="tab-letter"
                        href="#mat-cost-tab">
                        Material Cost Config
                        </a>
                    </li>
   					<li>
                        <a data-toggle="tab"
                        class="tab-letter"
                        href="#commi-tab">
                        Commissary Config
                        </a>
                    </li>
  				</ul>

  				<div class="tab-content">

					<div id="prod-group-tab" class="tab-pane fade in">
    					<br>
						<div id="add-btn">
							<a href="#" data-toggle="modal" data-target="#modal-production-group" class="btn btn-success btn-xs" data-toggle="modal">+ Add Production Group</a>
						</div>
						<div id="modal-production-group" class="modal fade modal-confirm" role="dialog">
							<div class="modal-dialog modal-sm">
							    <div class="modal-content">
							    	<div class="modal-header">
							        	<button type="button" class="close" data-dismiss="modal">&times;</button>
							       		<strong>Add Production Group</strong>
							      	</div>
							      	<div class="modal-body">
							        	<form method="POST" action="<?=base_url('admin/add-config-prod')?>" enctype="multipart/form-data" id="add-material">

							        		<div class="form-group">
							        			<label>Production Group:</label>
							        			<select class="form-control" name="prod_id[]" id="prod_id">
							        				<option value="">Select...</option>

							        				<?php foreach($material as $row_type):
							        				?>
							        				<option value="<?=encode($row_type->material_id)?>"><?=$row_type->material_code.' - '.$row_type->material_desc?></option>
							        				<?php endforeach;?>

							        			</select>
							        		</div>

							        		<div class="form-group">
							        			<label>Process Type:</label>
							        			<select class="form-control" name="process_type_id" id="process_type_id">
							        				<option value="">Select...</option>

							        				<?php foreach($process_type as $row):?>

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

						<div id="modal-confirm" class="modal fade" role="dialog">
							<div class="modal-dialog modal-sm">
							    <div class="modal-content">
							    	<div class="modal-header">
							        	<button type="button" class="close" data-dismiss="modal">&times;</button>
							       		<strong>Confirmation message</strong>
							      	</div>
							      	<div class="modal-body">
							      		<form method="POST" action="<?=base_url('admin/cancel-config-prod')?>" enctype="multipart/form-data" id="cancel-config-prod">
							      			<input type="hidden" name="config_prod_id" id="config_prod_id">
							      			<input type="hidden" name="trans_status" id="trans_status">
							      			<input type="hidden" name="process_type_id" value="<?=encode(9)?>">

								        	<div id="modal-msg" class="text-center">
								        		
								        	</div>
								        	<div id="modal-btn" class="text-center">
								        		<button type="submit" class="btn btn-info btn-sm">Yes</button>&nbsp;&nbsp;
								        		<button data-dismiss="modal" class="btn btn-danger btn-sm">No</button>
								        	</div>
								        </form>
							      	</div>
							    </div>
							</div>
						</div>

						<table class="table table-hover" id="tbl-commi-mat-cost">
							<thead>
								<tr>
									<th width="auto"></th>
									<th width="auto">Processing Type</th>
									<th width="30%">Production Group</th>
									<th width="auto">Created By</th>
									<th width="auto">Date Created</th>
									<th width="20%" class="text-center">Action</th>
								</tr>
							</thead>
							<tbody>
								<?php foreach($config_prod as $row):?>

								<tr>
									<td class="text-center"><a href="" class="cancel-config-prod" data-id="<?=encode($row->config_prod_id)?>"><i class="fa fa-remove"></i></td>
									<td><?=$row->process_type_name?></td>
									<td><?=$row->material_code.' - '.$row->material_desc?></td>
									<td><?=$row->user_fname.' '.$row->user_lname?></td>
									<td><?=date( 'm/d/Y', strtotime($row->created_ts))?></td>
									<td class="text-center"><a href="<?=base_url('admin/view-commi-config-prod/' . encode($row->config_prod_id).'/'.encode($row->process_type_id))?>" class="btn btn-xs btn-success edit-broiler-group" data-id="<?=encode($row->config_prod_id)?>">View</a></td>
									

								</tr>

								<?php endforeach;?>

							</tbody>
						</table>

					</div>
					
					<div id="mat-cost-tab" class="tab-pane fade in">
    					<br>
						<table class="table table-hover" id="tbl-broiler-config">
							<thead>
								<tr>
									<th>Commissary</th>
									<th width="30%">Location</th>
									<th width="20%">Unit</th>
									<th width="20%" class="text-center">Action</th>
								</tr>
							</thead>
							<tbody>

								<?php
									foreach($commissary as $row):
								?>
								<tr>
									<td><?=$row->commissary_name?></td>
									<td><?=$row->commissary_location?></td>
									<td><?=$row->company_unit_name?></td>
									<td class="text-center"><a href="<?=base_url('admin/commi-mat-cost/' . encode($row->commissary_id))?>" class="btn btn-success btn-xs">view</a></td>
								</tr>
								<?php endforeach;?>

							</tbody>
						</table>

					</div>

					<div id="prod-config-tab" class="tab-pane fade in active">
    					<br>
						<table class="table table-hover" id="tbl-broiler-group">
							<thead>
								<tr>
									<th>Commissary</th>
									<th width="30%">Location</th>
									<th width="20%">Unit</th>
									<th width="20%" class="text-center">Action</th>
								</tr>
							</thead>
							<tbody>

								<?php
									foreach($commissary as $row):
								?>
								<tr>
									<td><?=$row->commissary_name?></td>
									<td><?=$row->commissary_location?></td>
									<td><?=$row->company_unit_name?></td>
									<td class="text-center"><a href="<?=base_url('admin/commi-prod-trans/' . encode($row->commissary_id))?>" class="btn btn-success btn-xs">view</a></td>
								</tr>
								<?php endforeach;?>

							</tbody>
						</table>

					</div>

					<div id="commi-tab" class="tab-pane fade in">
    					<br>
						<div id="add-btn">
							<a href="#" data-toggle="modal" data-target="#modal-commissary" class="btn btn-success btn-xs" data-toggle="modal">+ Add Commissary</a>
						</div>
						<div id="modal-commissary" class="modal fade modal-confirm" role="dialog">
							<div class="modal-dialog modal-lg">
							    <div class="modal-content">
							    	<div class="modal-header">
							        	<button type="button" class="close" data-dismiss="modal">&times;</button>
							       		<strong>Add Commissary</strong>
							      	</div>
							      	<div class="modal-body">
							        	<form method="POST" action="<?=base_url('admin/add-commissary')?>" enctype="multipart/form-data" id="add-commissary">

											<input type="hidden" name="commissary_id" id="commissary_id">
							        		<div class="form-group">
							        			<label>Commissary Name:</label>
							        			<input type="text" required name="commissary_name" id="commissary_name" class="form-control">
							        		</div>

											<div class="form-group">
							        			<label>Commissary Location:</label>
							        			<input type="text" required name="commissary_location" id="commissary_location" class="form-control">
							        		</div>
											
											
											
											<div class="form-group">
							        			<label>Commissary Cost Center:</label>
							        			<input type="text" step="any" required name="commissary_cost_center_code" id="commissary_cost_center_code" class="form-control">
							        		</div>
							        		
											<div class="form-group">
							        			<label>Commissary Address:</label>
							        			
												<textarea class="form-control" required name="commissary_address" id="commissary_address" rows="5"></textarea>
							        		</div>
											
											<div class="form-group">
							        			<label>BC To Serve:</label>
							        			<select class="form-control" name="bc_id[]" id="bc_id_to_serve" multiple aria-placeholder="Select..." required>
							        				

							        				<?php foreach($bc as $row_type):?>
							        				<option value="<?=encode($row_type->bc_id)?>"><?=$row_type->bc_name?></option>
							        				<?php endforeach;?>

							        			</select>
							        		</div>

											<div class="form-group row" id="commi-cap-to-replace">
											<?php for($i=1; $i <= 12; $i++){
												$date = $year.'-'.$i.'-01';
											?>
												<div class="col-sm-3">
													<label>Capacity (<?=date_display($date, 'M')?>):</label>
													<input type="number" step="any" required name="commissary_capacity[]" class="form-control text-right">
												</div>
												

											<?php } ?>
							        		</div>

							        		
							        		
							        		<div class="btn-update">
							        			<button type="submit" class="btn btn-info btn-sm pull-right commi-form-btn">Add</button><br>
							        		</div>
							        	</form>
							      	</div>
							    </div>
							</div>
						</div>

						<div id="modal-confirm-commissary" class="modal fade" role="dialog">
							<div class="modal-dialog modal-sm">
							    <div class="modal-content">
							    	<div class="modal-header">
							        	<button type="button" class="close" data-dismiss="modal">&times;</button>
							       		<strong>Confirmation message</strong>
							      	</div>
							      	<div class="modal-body">
							      		<form method="POST" action="<?=base_url('admin/cancel-commissary')?>" enctype="multipart/form-data" id="cancel-commissary">
							      			<input type="hidden" name="commissary_id" id="commissary_id">
							      			<input type="hidden" name="trans_status" id="trans_status">
							      			

								        	<div id="modal-msg" class="text-center">
								        		
								        	</div>
								        	<div id="modal-btn" class="text-center">
								        		<button type="submit" class="btn btn-info btn-sm">Yes</button>&nbsp;&nbsp;
								        		<button data-dismiss="modal" class="btn btn-danger btn-sm">No</button>
								        	</div>
								        </form>
							      	</div>
							    </div>
							</div>
						</div>

						<table class="table table-hover" id="tbl-commissary">
							<thead>
								<tr>
									
									<th width="auto">Commissary</th>
									<th width="auto">Commissary Location</th>
									<th width="auto">Unit</th>
									<th width="auto">Commissary Capacity</th>
									<th width="auto">Cost Center Code</th>
									<th width="auto">BC To Serve</th>
									<th width="auto">Created By</th>
									<th width="auto">Date Created</th>
									<th width="auto" class="text-center">Action</th>
								</tr>
							</thead>
							<tbody>
								<?php foreach($commissary_mnt as $row):?>

								<tr>
									
									<td><?=$row->commissary_name?></td>
									<td><?=$row->commissary_location?></td>
									<td><?=$row->company_unit_name?></td>
									<td><?=$row->commi_capacities?></td>
									<td><?=$row->commissary_cost_center_code?></td>
									<td><?=$row->bc_names?></td>
									<td><?=$row->creator?></td>
									<td><?=date( 'm/d/Y', strtotime($row->commissary_created_ts))?></td>

									<?php if($row->commissary_status == 1):?>
									<td class="text-center">
										<a title="Edit" href="#" class="edit-commissary" data-id="<?=encode($row->commissary_id)?>"><i class="fa fa-pencil"></i></a>
										&nbsp;
										<a title="Deactivate" href="#" class="cancel-commissary text-danger" data-id="<?=encode($row->commissary_id)?>"><i class="fa fa-remove"></i></a>
									</td>
									<?php else:?>
									<td class="text-center">
										
										<a title="Activate" href="#" class="activate-commissary text-success" data-id="<?=encode($row->commissary_id)?>"><i class="fa fa-check"></i></a>
									</td>
									<?php endif;?>

								</tr>

								<?php endforeach;?>

							</tbody>
						</table>

					</div>

				</div>
			</div>


            <script>
            var url = document.location.toString();
            if (url.match('#')) {
                $('.nav-tabs a[href="#' + url.split('#')[1] + '"]').tab('show');
            } 

            // Change hash for page-reload
            $('.nav-tabs a').on('shown.bs.tab', function (e) {
                window.location.hash = e.target.hash;
            })
            </script>