
			<div class="col-lg-12" id="content">
				<div id="breadcrumb-div">
					<ul class="breadcrumb">
					    <li><a href="<?=base_url('ahg')?>"><span class="glyphicon glyphicon-home"></span>&nbsp;Dashboard</a></li>
					    <li class="active">Production Cost</li>
					</ul>
				</div>

				<?php
					if($this->session->flashdata('message') != "" ){
						echo $this->session->flashdata('message');
					}
				?>

				<ul class="nav nav-tabs">
				    <li class="active"><a data-toggle="tab" href="#prod-config-tab" class="tab-letter">Production</a></li>
   					<li><a data-toggle="tab" class="tab-letter" href="#prod-group-tab">Production Group</a></li>
  				</ul>

  				<div class="tab-content">

					<div id="prod-group-tab" class="tab-pane fade in">
    					<br>
						<div id="add-btn">
							<a href="#" data-toggle="modal" data-target="#modal-production-group" class="btn btn-success btn-xs" data-toggle="modal" data-target="#modal-vmaterial">+ Add Production Group</a>
						</div>
						<div id="modal-production-group" class="modal fade modal-confirm" role="dialog">
							<div class="modal-dialog modal-sm">
							    <div class="modal-content">
							    	<div class="modal-header">
							        	<button type="button" class="close" data-dismiss="modal">&times;</button>
							       		<strong>Add Production Group</strong>
							      	</div>
							      	<div class="modal-body">
							        	<form method="POST" action="<?=base_url('ahg/add-config-prod')?>" enctype="multipart/form-data" id="add-material">

							        		<div class="form-group">
							        			<label>Production Group:</label>
							        			<select class="form-control" name="prod_id" id="prod_id">
							        				<option value="">Select...</option>

							        				<?php foreach($material as $row_type):
							        				?>
							        				<option value="<?=encode($row_type->material_id)?>"><?=$row_type->material_desc?></option>
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

						<table class="table table-hover" id="tbl-broiler-group">
							<thead>
								<tr>
									<th>Processing Type</th>
									<th>Production Group</th>
									
									<th>Created By</th>
									<th>Date Created</th>
									<th class="text-center">Action</th>
								</tr>
							</thead>
							<tbody>
								<?php foreach($config_prod as $row):?>

								<tr>
									<td><?=$row->process_type_name?></td>
									<td><?=$row->material_desc?></td>
									<td><?=$row->user_fname.' '.$row->user_lname?></td>
									<td><?=date( 'm/d/Y', strtotime($row->created_ts))?></td>
									<td class="text-center"><a href="<?=base_url('ahg/view-config-prod/' . encode($row->config_prod_id))?>" class="btn btn-xs btn-success edit-broiler-group" data-id="<?=encode($row->config_prod_id)?>">View</a></td>
									<!--&nbsp;&nbsp;<a href="<?=base_url('ahg/view-broiler-group/' . encode($row->broiler_group_id))?>" class="btn btn-danger btn-xs glyphicon glyphicon-remove cancel-broiler-group" data-id="<?=encode($row->broiler_group_id)?>"></a> -->

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
									<td class="text-center"><a href="<?=base_url('ahg/prod-trans/' . encode($row->bc_id))?>" class="btn btn-success btn-xs">view</a></td>
								</tr>
								<?php endforeach;?>

							</tbody>
						</table>

					</div>
				</div>
			</div>