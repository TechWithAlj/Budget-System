			
			<div class="col-lg-12" id="content">
				<div id="breadcrumb-div">
					<ul class="breadcrumb">
					    <li><a href="<?=base_url('ahg')?>"><span class="glyphicon glyphicon-home"></span>&nbsp;Dashboard</a></li>
					    <li><a href="<?=base_url('ahg/prod-trans/'.$bc_id)?>">Production Transaction</a></li>
					    <li class="active">Add Production Transaction</li>
					</ul>
				</div>

				<?php
					if($this->session->flashdata('message') != "" ){
						echo $this->session->flashdata('message');
					}
				?>
				<form method="post" action="<?=base_url('ahg/add-prod-trans')?>" enctype="multipart/form-data" id="add-material">
					<input type="hidden" name="bc_id" id="bc_id" value="<?=$bc_id?>">
					<input type="hidden" name="year" id="year" value="<?=encode($year)?>">
					<input type="hidden" name="process_type_name" id="process_type_name" value="">
					<div class="row">
						<div class="col-lg-2">
							
							<label>Brand Type</label>
							<select name="type_id" id="type_id" class="form-control">
								<option value="">Select...</option>

								<?php foreach($type as $row):?>

									<option value="<?=encode($row->brand_type_id)?>"><?=$row->brand_type_name?></option>

								<?php endforeach;?>
							</select>
						</div>
						<div class="col-lg-2">
							
							<label>Brand</label>
							<select name="brand_id" id="brand_id" class="form-control">
								<option value="">Select...</option>
							</select>
						</div>
						<div class="col-lg-3">
							
							<label>Production Group</label>
							<select name="config_prod" id="config_prod" class="form-control">
								<option value="">Select...</option>

								<?php foreach($config_prod as $row):?>

									<option value="<?=encode($row->config_prod_id)?>"><?=$row->material_desc.' - '.$row->process_type_name?></option>

								<?php endforeach;?>
							</select>
						</div>
					</div>
					

					<div class="table-responsive">
						<table class="table table-hover table-bordered table-stripe nowrap" id="tbl-new-prod-transaction">
							<thead id="here">
								<tr>
									<th class="text-center" rowspan="2"></th>
									<th rowspan="2" width="30%">Production Group Name</th>
									<th rowspan="2" width="10%">Component</th>
									<?php for ($i=1; $i <= 12 ; $i++){ ?>
									<th class="text-center" colspan="2"><?=date('M', strtotime($year.'-'.$i.'-01'))?></th>
									<?php } ?>
								</tr>
								<tr>
									
									<?php for ($i=1; $i <= 12 ; $i++){ ?>
									
									<th class="text-center">Rate</th>
									
									<th class="text-center" id="dynamic_hdr-<?=$i?>">Cost/Price</th>
									<?php } ?>
								</tr>
							</thead>
							<tbody>
							</tbody>
						</table>
					</div>
						
					<div class="text-right" id="expenditures-add-btn">
						<button type="submit" class="btn btn-success btn-sm save">Save</button>
					</div>
				</form>
			</div>