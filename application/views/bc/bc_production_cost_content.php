
			<div class="col-lg-12" id="content">
				<div id="breadcrumb-div">
					<ul class="breadcrumb">
					    <li><a href="<?=base_url('business-center')?>"><span class="glyphicon glyphicon-home"></span>&nbsp;Dashboard</a></li>
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
   					
  				</ul>

  				<div class="tab-content">

					

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
									<td class="text-center"><a href="<?=base_url('business-center/prod-trans/' . encode($row->bc_id))?>" class="btn btn-success btn-xs">view</a></td>
								</tr>
								<?php endforeach;?>

							</tbody>
						</table>

					</div>
				</div>
			</div>