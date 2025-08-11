			
			<div class="col-lg-12" id="content">
				<div id="breadcrumb-div">
					<ul class="breadcrumb">
					    <li><a href="<?=base_url('admin')?>"><span class="glyphicon glyphicon-home"></span>&nbsp;Dashboard</a></li>
					    <li class="active">Brand BC</li>
					</ul>
				</div>
				<?php
					if($this->session->flashdata('message') != "" ){
						echo $this->session->flashdata('message');
					}
				?>
								
				<table class="table table-hover" id="tbl-brand-bc">
					<thead>
						<tr>
							<th>Business Center</th>
							<th>Cost Center</th>
							<th>Total Capex</th>
							<th>Action</th>
						</tr>
					</thead>
					<tbody>

						<?php
							foreach($bc as $row):
						?>
						
						<tr>
							<td><?=$row->bc_name?></td>
							<td><?=$row->cost_center_code?></td>
							<td></td>
							<td><a href="<?=base_url('admin/brand-bc-info/' . encode($row->bc_id) . '/')?>" class="btn btn-success btn-xs">View</a></td>
						</tr>

						<?php endforeach;?>

					</tbody>
				</table>
			</div>