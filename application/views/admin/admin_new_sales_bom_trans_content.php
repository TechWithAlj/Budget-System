			
			<div class="col-lg-12" id="content">
				<div id="breadcrumb-div">
					<ul class="breadcrumb">
					    <li><a href="<?=base_url('admin')?>"><span class="glyphicon glyphicon-home"></span>&nbsp;Dashboard</a></li>
					    <li><a href="<?=base_url('admin/sales-bom-trans/'.$bc_id)?>">Sales BOM Transaction</a></li>
					    <li class="active">Add Sales BOM Transaction (<?=$bc->bc_name?>) </li>
					</ul>
				</div>

				<?php
					if($this->session->flashdata('message') != "" ){
						echo $this->session->flashdata('message');
					}
				?>
				<form method="post" action="<?=base_url('admin/add-prod-trans')?>" enctype="multipart/form-data" id="add-material">
					<input type="hidden" name="bc_id" id="bc_id" value="<?=$bc_id?>">
					<input type="hidden" name="year" id="year" value="<?=encode($year)?>">
					<input type="hidden" id="pick_year" value="<?=$year?>">
					<input type="hidden" name="process_type_name" id="process_type_name" value="">
					<div class="row">
						<div class="col-lg-2">
							
							<label>Brand Type</label>
							<select name="type_id" id="type_id" class="form-control" required="true">
								<option value="">Select...</option>

								<?php foreach($type as $row):?>

									<option value="<?=encode($row->brand_type_id)?>"><?=$row->brand_type_name?></option>

								<?php endforeach;?>
							</select>
						</div>
						<div class="col-lg-2">
							
							<label>Brand</label>
							<select name="brand_id" id="brand_id" class="form-control" required="true">
								<option value="">Select...</option>
							</select>
						</div>
						<div class="col-lg-3">
							
							<label>Sales BOM Group</label>
							<select name="config_prod" id="config_prod" class="form-control">
								<option value="">Select...</option>

								<?php foreach($config_prod as $row):?>

									<option value="<?=encode($row->config_prod_id)?>"><?=$row->material_code.' - '.$row->material_desc.' ~ '.$row->process_type_name?></option>

								<?php endforeach;?>
							</select>
						</div>
					</div>
					

					<div class="table-responsive">
						<table class="table table-hover table-bordered table-stripe nowrap" id="tbl-new-prod-transaction">
							<thead id="here">
								<tr>
									<th class="text-center" rowspan="2"></th>
									<th rowspan="2" width="30%">Sales BOM Item Name</th>
									<th rowspan="2" width="30%">Item Code</th>
									<th rowspan="2" width="30%">Val. Unit</th>
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

				<div id="modal-slider-prod" class="modal fade" role="dialog">
					<div class="modal-dialog modal-sm">
					    <div class="modal-content">
					    	<div class="modal-header">
					        	<button type="button" class="close" data-dismiss="modal">&times;</button>
					       		<strong>Budgeting Slider Tool</strong>
					      	</div>
					      	<div class="modal-body">
				      			<input type="hidden" name="id" id="id">
				      			<div class="slider-div">
				      				
					      			<label>Rate:</label><input type="number" class="form-control input-sm" id="slider-qty-val"><br />
						        	<input type="range" min="1" max="1000" value="0" class="slider" id="slider-qty">
						        </div>
						        <div class="slider-div">
						        	<label>Month Start:&nbsp;&nbsp;<span id="slider-qty-start-val"></span></label>
						        	<input type="range" min="1" max="12" value="1" class="slider" id="slider-qty-start">
						        </div>

						        <div class="slider-div">
						        	<label>Month End:&nbsp;&nbsp;<span id="slider-qty-end-val"></span></label>
						        	<input type="range" min="1" max="12" value="12" class="slider" id="slider-qty-end">
						        </div>

						        <hr />
						        <div class="slider-div">
					      			<label id="dynamic-label">:&nbsp;&nbsp; </label><input type="number" class="form-control input-sm" id="slider-cost-val"><br />
						        	<input type="range" min="1" max="5000" value="0" class="slider" id="slider-cost">
						        </div>

						        <div class="slider-div">
						        	<label>Month Start:&nbsp;&nbsp;<span id="slider-cost-start-val"></span></label>
						        	<input type="range" min="1" max="12" value="1" class="slider" id="slider-cost-start">
						        </div>

						        <div class="slider-div">
						        	<label>Month End:&nbsp;&nbsp;<span id="slider-cost-end-val"></span></label>
						        	<input type="range" min="1" max="12" value="12" class="slider" id="slider-cost-end">
						        </div>

						        <div class="text-right">
						        	<a href="" class="btn btn-info btn-sm slider-prod-btn">Apply</a>
						        </div>
					        	
					      	</div>
					    </div>
					</div>
				</div>
			</div>