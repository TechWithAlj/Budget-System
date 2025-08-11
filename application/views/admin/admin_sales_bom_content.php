
			<div class="col-lg-12" id="content">
				<div id="breadcrumb-div">
					<ul class="breadcrumb">
					    <li><a href="<?=base_url('admin')?>"><span class="glyphicon glyphicon-home"></span>&nbsp;Dashboard</a></li>
					    <li class="active">Sales BOM</li>
					</ul>
				</div>

				<?php
					if($this->session->flashdata('message') != "" ){
						echo $this->session->flashdata('message');
					}
				?>

				<ul class="nav nav-tabs">
				    <li class="active"><a data-toggle="tab" href="#prod-config-tab" class="tab-letter">Sales BOM</a></li>
   					<li><a data-toggle="tab" class="tab-letter" href="#prod-group-tab">Sales BOM Group</a></li>
  				</ul>

  				<div class="tab-content">

					<div id="prod-group-tab" class="tab-pane fade in">
    					<br>
						<div id="add-btn">
							<a href="#" data-toggle="modal" data-target="#modal-production-group" class="btn btn-success btn-xs" data-toggle="modal" data-target="#modal-vmaterial">+ Add Sales BOM</a>
						</div>
						<div id="modal-production-group" class="modal fade modal-confirm" role="dialog">
							<div class="modal-dialog modal-sm">
							    <div class="modal-content">
							    	<div class="modal-header">
							        	<button type="button" class="close" data-dismiss="modal">&times;</button>
							       		<strong>Add Sales BOM</strong>
							      	</div>
							      	<div class="modal-body">
							        	<form method="POST" action="<?=base_url('admin/add-config-prod')?>" enctype="multipart/form-data" id="add-material">

							        		<div class="form-group">
							        			<label>Sales BOM:</label>
							        			<select class="form-control" name="prod_id[]" id="sales_bom_id">
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
							      			<input type="hidden" name="process_type_id" value="<?=encode(5)?>">
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

						<table class="table table-hover" id="tbl-broiler-group">
							<thead>
								<tr>
									<th width="auto"></th>
									<th>Processing Type</th>
									<th>Sales BOM Group</th>
									
									<th>Created By</th>
									<th>Date Created</th>
									<th class="text-center">Action</th>
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
									<td class="text-center"><a href="<?=base_url('admin/view-config-prod/' . encode($row->config_prod_id).'/'.encode($row->process_type_id))?>" class="btn btn-xs btn-success edit-broiler-group" data-id="<?=encode($row->config_prod_id)?>">View</a></td>
									<!--&nbsp;&nbsp;<a href="<?=base_url('admin/view-broiler-group/' . encode($row->broiler_group_id))?>" class="btn btn-danger btn-xs glyphicon glyphicon-remove cancel-broiler-group" data-id="<?=encode($row->broiler_group_id)?>"></a> -->

								</tr>

								<?php endforeach;?>

							</tbody>
						</table>

					</div>

					<div id="prod-config-tab" class="tab-pane fade in active">
    					<br>
						<table class="table table-hover" id="tbl-broiler-config">
							<thead>
								<tr>
									<th>Business Center</th>
									<th width="20%" class="text-center">Action</th>
								</tr>
							</thead>
							<tbody>

								<?php
									foreach($bc as $row):
								?>
								<tr>
									<td><?=$row->bc_name?></td>
									<td class="text-center"><a href="<?=base_url('admin/sales-bom-trans/' . encode($row->bc_id))?>" class="btn btn-success btn-xs">view</a></td>
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