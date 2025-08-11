
			<div class="col-lg-12" id="content">
				<div id="breadcrumb-div">
					<ul class="breadcrumb">
					    <li><a href="<?=base_url('ahg')?>"><span class="glyphicon glyphicon-home"></span>&nbsp;Dashboard</a></li>
					    <li class="active">Broiler Cost</li>
					</ul>
				</div>

				<?php
					if($this->session->flashdata('message') != "" ){
						echo $this->session->flashdata('message');
					}
				?>

				<ul class="nav nav-tabs">
				    <li class="active"><a data-toggle="tab" href="#broiler-config-tab" class="tab-letter">Broiler</a></li>
   					<li><a data-toggle="tab" class="tab-letter" href="#broiler-group-tab">Broiler Group</a></li>
  				</ul>

  				<div class="tab-content">

					<div id="broiler-group-tab" class="tab-pane fade in">
    					<br>
						<!-- <div id="add-btn">
							<a href="" class="btn btn-success btn-xs" data-toggle="modal" data-target="#modal-vmaterial">+ Add Broiler Group</a>

						</div> -->

						<table class="table table-hover" id="tbl-broiler-group">
							<thead>
								<tr>
									<th>Broiler Group Name</th>
									<th>Created By</th>
									<th>Date Created</th>
									<th class="text-center">Action</th>
								</tr>
							</thead>
							<tbody>
								<?php foreach($broiler_group as $row):?>

								<tr>
									<td><?=$row->broiler_group_name?></td>
									<td><?=$row->user_fname.' '.$row->user_lname?></td>
									<td><?=date( 'm/d/Y', strtotime($row->created_ts))?></td>
									<td class="text-center"><a href="<?=base_url('ahg/view-broiler-group/' . encode($row->broiler_group_id))?>" class="btn btn-xs btn-success edit-broiler-group" data-id="<?=encode($row->broiler_group_id)?>">View</a></td>
									<!--&nbsp;&nbsp;<a href="<?=base_url('admin/view-broiler-group/' . encode($row->broiler_group_id))?>" class="btn btn-danger btn-xs glyphicon glyphicon-remove cancel-broiler-group" data-id="<?=encode($row->broiler_group_id)?>"></a> -->

								</tr>

								<?php endforeach;?>

							</tbody>
						</table>

					</div>

					<div id="broiler-config-tab" class="tab-pane fade in active">
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
									<td class="text-center"><a href="<?=base_url('ahg/view-broiler-config/' . encode($row->bc_id))?>" class="btn btn-success btn-xs">Config</a>&nbsp;&nbsp;<a href="<?=base_url('ahg/broiler-trans/' . encode($row->bc_id))?>" class="btn btn-primary btn-xs">Budget</a></td>
								</tr>
								<?php endforeach;?>

							</tbody>
						</table>

					</div>
				</div>
			</div>