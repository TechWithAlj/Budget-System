			<div class="col-lg-12" id="content">
				<div id="breadcrumb-div">
					<ul class="breadcrumb">
					    <li><a href="<?=base_url('admin')?>"><span class="glyphicon glyphicon-home"></span>&nbsp;Dashboard</a></li>
					    <li><a href="<?=base_url('admin/broiler-cost')?>">Broiler Cost</a></li>
					    <li class="active">Broiler Config Info (<?=$bc->bc_name?>)</li>
					</ul>
				</div>

				<?php
					if($this->session->flashdata('message') != "" ){
						echo $this->session->flashdata('message');
					}
				?>

				<div class="row">
					<div class="col-lg-2">
						<label>Pick Year:</label>
						<div class="form-group">
							<div class="date">
		                        <div class="input-group input-append date" id="broiler-config-year">
		                            <input type="text" name="month" id="broiler-config-date-pick-year" class="form-control input-sm" placeholder="Pick year" value="<?=$year?>">
		                            <span class="input-group-addon add-on"><span class="glyphicon glyphicon-calendar"></span></span>
		                        </div>
		                    </div>
						</div>
					</div>
					<input type="hidden" name="bc_id" id="bc_id" value="<?=encode($bc_id)?>">
				</div>

				
				<input type="hidden" id="lock_status" value="<?=$pending_lock_status;?>">
				<div id="add-btn">
					<a href="<?=base_url('admin/new-broiler-config/' . encode($bc_id).'/'.$year)?>" class="btn btn-success btn-xs" id="add_broiler_config_button" >+ Add Broiler Config</a>
				</div>
				

				<div class="table-responsive">
					<table class="table table-hover table-bordered table-stripe nowrap" id="tbl-view-broiler-config">
						<thead>
							<tr>
								<th>Broiler Config Name</th>
								<th>Business Center</th>
								<th>Status</th>
								<th class="text-center">Action</th>
							</tr>
						</thead>
						<tbody>
							<?php foreach($broiler_config as $row): ?>
							<tr>
								<th><?=$row->broiler_subgroup_name?></th>
								<th><?=$row->bc_name?></th>
								<th><?=$row->status_name?></th>
								<td class="text-center"><a href="<?=base_url('admin/edit-broiler-config/' . encode($row->broiler_subgroup_name).'/'.encode($row->bc_id).'/'.$year)?>" class="btn-xs glyphicon glyphicon-pencil edit-broiler-config"></a></td>
								<!-- <td>&nbsp;&nbsp;<a href="<?=base_url('admin/remove-broiler-config/' . encode($row->broiler_group_id))?>" class="btn btn-xs glyphicon glyphicon-remove remove-broiler-config" data-id="<?=encode($row->broiler_group_id)?>"></a></td> -->
							</tr>
							<?php endforeach; ?>
						</tbody>
					</table>
				</div>
			</div>