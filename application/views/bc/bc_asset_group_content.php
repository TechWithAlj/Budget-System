			
			<div class="col-lg-12" id="content">
				<div id="breadcrumb-div">
					<ul class="breadcrumb">
					    <li><a href="<?=base_url('business-center')?>"><span class="glyphicon glyphicon-home"></span>&nbsp;Dashboard</a></li>
					    <li class="active">Assets</li>
					</ul>
				</div>
				<?php
					if($this->session->flashdata('message') != "" ){
						echo $this->session->flashdata('message');
					}
				?>

				<ul class="nav nav-tabs">
					<li class="active"><a data-toggle="tab" href="#asset-subgroup-tab" class="tab-letter">Asset Sub Group</a></li>
   					<li><a data-toggle="tab" class="tab-letter" href="#asset-group-tab">Asset Group</a></li>
  				</ul>
  				<div class="tab-content">

  					<div id="asset-subgroup-tab" class="tab-pane fade in active">
    					<br>

    					<div class="row">
	    					<div class="col-md-5">

							</div>
							<div class="col-md-7">
								<div class="text-right">
									<a href="<?=base_url('business-center/download-assets/')?>" target="_blank"><span class="glyphicon glyphicon-download-alt"></span>&nbsp;&nbsp; Download Assets</a>
								</div>
							</div>
						</div><br /><br />

						<table class="table table-hover" id="tbl-asset-subgroup">
							<thead>
								<tr>
									<th>Asset Subgroup</th>
									<th>Asset Group</th>
									<th>Price</th>
									<th>Useful life</th>
									<th>Status</th>
								</tr>
							</thead>
							<tbody>

								<?php
									foreach($sub_group as $row_sub):
								?>
								
								<tr>
									<td><?=$row_sub->asg_name?></td>
									<td><?=$row_sub->ag_name?></td>
									<td class="text-right"><?=number_format($row_sub->asg_price, 2)?></td>
									<td class="text-center"><?=$row_sub->asg_lifespan?></td>
									<td class="text-center"><?=$row_sub->asg_status == 0 ? '<span class="badge badge-info">INACTIVE</span>' : '<span class="badge badge-warning">ACTIVE</span>'?></td>
								</tr>

								<?php endforeach;?>

							</tbody>
						</table>
					</div>

    				<div id="asset-group-tab" class="tab-pane fade">
    					<br>
				
						<table class="table table-hover" id="tbl-asset-group">
							<thead>
								<tr>
									<th>Asset Group</th>
									<th>Code</th>
									<th>Asset Color</th>
								</tr>
							</thead>
							<tbody>

								<?php
									foreach($asset_group as $row):
								?>
								
								<tr>
									<td><?=$row->ag_name?></td>
									<td><?=$row->ag_gl_code?></td>
									<td><?=$row->ag_color?></td>
								</tr>

								<?php endforeach;?>

							</tbody>
						</table>
					</div>
				</div>
			</div>