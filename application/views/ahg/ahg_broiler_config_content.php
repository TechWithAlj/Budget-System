			<div class="col-lg-12" id="content">
				<div id="breadcrumb-div">
					<ul class="breadcrumb">
					    <li><a href="<?=base_url('ahg')?>"><span class="glyphicon glyphicon-home"></span>&nbsp;Dashboard</a></li>
					    <li><a href="<?=base_url('ahg/broiler-cost')?>">Broiler Cost</a></li>
					    <li class="active">Broiler Config Info</li>
					</ul>
				</div>

				<?php
					if($this->session->flashdata('message') != "" ){
						echo $this->session->flashdata('message');
					}
				?>

				<div id="add-btn">
					<a href="<?=base_url('ahg/new-broiler-config/' . encode($bc_id))?>" class="btn btn-success btn-xs">+ Add Broiler Config</a>
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
								<td class="text-center"><a href="<?=base_url('ahg/edit-broiler-config/' . encode($row->broiler_subgroup_name).'/'.encode($row->bc_id))?>" class="btn-xs glyphicon glyphicon-pencil edit-broiler-config"></a></td>
								<!-- <td>&nbsp;&nbsp;<a href="<?=base_url('admin/remove-broiler-config/' . encode($row->broiler_group_id))?>" class="btn btn-xs glyphicon glyphicon-remove remove-broiler-config" data-id="<?=encode($row->broiler_group_id)?>"></a></td> -->
							</tr>
							<?php endforeach; ?>
						</tbody>
					</table>
				</div>
			</div>