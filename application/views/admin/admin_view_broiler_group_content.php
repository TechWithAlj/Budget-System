			<div class="col-lg-12" id="content">
				<div id="breadcrumb-div">
					<ul class="breadcrumb">
					    <li><a href="<?=base_url('admin')?>"><span class="glyphicon glyphicon-home"></span>&nbsp;Dashboard</a></li>
					    <li><a href="<?=base_url('admin/broiler-cost')?>">Broiler Cost</a></li>
					    <li class="active">Broiler Group</li>
					</ul>
				</div>
			
				<?php
					if($this->session->flashdata('message') != "" ){
						echo $this->session->flashdata('message');
					}
				?>
				<div class="table-responsive">
					<table class="table table-hover table-bordered table-stripe nowrap" id="tbl-broiler-group">
						<thead>
							<tr>
								<th>Broiler Subgroup Name</th>
								<th>Show on Transaction Display</th>
								<th>Show on Config</th>
								<th>Amount Type</th>
							</tr>
						</thead>
						<tbody>
						<?php foreach($broiler_subgroup as $row): ?>
							<?php $show = $row->show == 1 ? 'YES' : 'NO'; ?>
							<?php $config_show = $row->config_show == 1 ? 'YES' : 'NO'; ?>
							<tr>
								<th><?=$row->broiler_subgroup_name?></th>
								<th><?=$show?></th>
								<th><?=$config_show?></th>
								<th><?=$row->amount_type_name?></th>
							</tr>
						<?php endforeach; ?>
						</tbody>
					</table>
				</div>
				<br><br>
			</div>