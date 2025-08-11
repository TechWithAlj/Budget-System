            <div class="col-lg-12" id="content">
				<div id="breadcrumb-div">
					<ul class="breadcrumb">
					    <li><a href="<?=base_url('business-center')?>"><span class="glyphicon glyphicon-home"></span>&nbsp;Dashboard</a></li>
					    <?php if(decode($process_type_id) == 5){ ?>
					    <li><a href="<?=base_url('business-center/sales-bom-trans/'.$bc_id)?>">Sales BOM Transaction</a></li>
					    <?php } else { ?>
					    <li><a href="<?=base_url('business-center/prod-trans/'.$bc_id)?>">Production Transaction</a></li>
					    <?php } ?>
					    <li class="active">View Sales BOM Listing (<?=$bc->bc_name?>)</li>
					</ul>
				</div>

				<?php
					if($this->session->flashdata('message') != "" ){
						echo $this->session->flashdata('message');
					}
				?>
				
				<div class="row">
					<div class="col-lg-12">
						<!-- <a href="<?=base_url('business-center/download-cost-sheet-report/' .$bc_id.'/'.$year)?>" class="btn btn-info btn-xs" id=""><span class="fa fa-download"></span>&nbsp;&nbsp;Download</a>	 -->		
						
                        <a href="<?=base_url('business-center/sync-sales-bom-summary/' .$bc_id.'/'.$year)?>" class="btn btn-primary btn-xs" id=""><span class="fa fa-refresh"></span>&nbsp;Sync Sales BOM Cost of Sales</a>			
					</div>
					<?=$report?>
				</div>
			</div>