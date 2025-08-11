            <div class="col-lg-12" id="content">
				<div id="breadcrumb-div">
					<ul class="breadcrumb">
					    <li><a href="<?=base_url('admin')?>"><span class="glyphicon glyphicon-home"></span>&nbsp;Dashboard</a></li>
					    
						<li><a href="<?=base_url('admin/commi-prod-trans/'.$commissary_id)?>">Commissary Production Transaction</a></li>
					    <li class="active">View Commi Cost Sheet (<?=$commissary->commissary_name?>)</li>
					</ul>
				</div>

				<?php
					if($this->session->flashdata('message') != "" ){
						echo $this->session->flashdata('message');
					}
				?>
				
				<div class="row">					
					<?=$cost_sheet?>
				</div>
			</div>