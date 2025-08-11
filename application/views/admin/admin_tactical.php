			
			<div class="col-lg-12" id="content">
				<div id="breadcrumb-div">
					<ul class="breadcrumb">
					    <li><a href="<?=base_url('admin')?>"><span class="glyphicon glyphicon-home"></span>&nbsp;Dashboard</a></li>
					    <li class="active">Tactical Price</li>
					</ul>
				</div>
				<?php
					if($this->session->flashdata('message') != "" ){
						echo $this->session->flashdata('message');
					}
				?>
				

				<ul class="nav nav-tabs">
   					<li class="active"><a data-toggle="tab" class="tab-letter" href="#bc-tab">Business Center</a></li>
				    <li><a data-toggle="tab" href="#unit-tab">Dashboard</a></li>
  				</ul>

  				<div class="tab-content">
    				<div id="bc-tab" class="tab-pane fade in active"><br/ >
						<table class="table table-hover" id="tbl-opex">
							<thead>
								<tr>
									<th>Business Center</th>
									<th>Cost Center</th>
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
									<td><a href="<?=base_url('admin/tactical-info/' . encode($row->cost_center_code))?>" class="btn btn-success btn-xs">View</a></td>
								</tr>

								<?php endforeach;?>

							</tbody>
						</table>
					</div>
				</div>
			</div>