			
			<div class="col-lg-12" id="content">
				<div id="breadcrumb-div">
					<ul class="breadcrumb">
					    <li><a href="<?=base_url('unit')?>"><span class="glyphicon glyphicon-home"></span>&nbsp;Dashboard</a></li>
					    <li><a href="<?=base_url('unit/capex-info/')?>">CAPEX Info</a></li>
					    <li class="active">Transaction</li>
					</ul>
				</div>
				<?php
					if($this->session->flashdata('message') != "" ){
						echo $this->session->flashdata('message');
					}
				?>
				<form method="POST" action="<?=base_url('unit/add-capex')?>" id="add-capex-form">
					<input type="hidden" name="year" id="capex-year" value="<?=$year?>">
					<div class="row">
						<div class="col-lg-3">
							<label class="data-info">Cost Center: <?=$cost_center_name?></label><br /><br />
						</div>

						<div class="col-lg-3">
							<label class="data-info">Budget Year: <?=$year?></label><br /><br />
						</div>

						<div class="col-lg-3">
							<label class="data-info">Grand Total: <span class="capex-grand-total"></span></label>
						</div>
					</div>
					<div class="row">
						<div class="col-lg-2">
							<input type="hidden" id="cost-center" name="bc_cost_center" value="<?=$id?>">
							
							<label class="data-info">Asset Group</label>
							<select name="asset_group" id="capex-ag" class="form-control">
								<option value="">Select...</option>

								<?php foreach($asset_group as $row_ag):?>

									<option value="<?=encode($row_ag->ag_id)?>"><?=$row_ag->ag_name?></option>

								<?php endforeach;?>
							</select>
						</div>
					</div>

					<div class="row">
						<div class="col-lg-12">
							<div class="table-responsive">
								<table class="table table-striped table-bordered nowrap" id="tbl-transac-capex">
									<thead>
										<tr>
											<th class="text-center" style="background: #03A9F4;color: #fff;"></th>
											<th style="background: #03A9F4;color: #fff; width:20px;">Asset</th>
											<th width="7%" style="background: #03A9F4;color: #fff;">Cost Center</th>
											<th width="7%" style="background: #03A9F4;color: #fff;">Type of CAPEX</th>
											<th width="7%" style="background: #03A9F4;color: #fff;">Maintenance Category</th>
											<th class="text-center" width="3%" style="background: #03A9F4;color: #fff;">Price</th>
											<th class="text-center" width="3%">Total Price</th>
											<th class="text-center" width="3%">Total Qty</th>
											<th class="text-center" width="">Jan</th>
											<th class="text-center" width="">Feb</th>
											<th class="text-center" width="">Mar</th>
											<th class="text-center" width="">Apr</th>
											<th class="text-center" width="">May</th>
											<th class="text-center" width="">Jun</th>
											<th class="text-center" width="">Jul</th>
											<th class="text-center" width="">Aug</th>
											<th class="text-center" width="">Sep</th>
											<th class="text-center" width="">Oct</th>
											<th class="text-center" width="">Nov</th>
											<th class="text-center" width="">Dec</th>
										</tr>
									</thead>
									<tbody>
									</tbody>
								</table>
							</div>
						</div>
					</div>

					<div class="text-right" id="expenditures-add-btn">
						<button type="submit" class="btn btn-success btn-sm btn-save-capex">Save CAPEX</button>
					</div>

					<div id="modal-confirm-capex" class="modal fade modal-confirm" role="dialog">
						<div class="modal-dialog modal-sm">
						    <div class="modal-content">
						    	<div class="modal-header">
						        	<button type="button" class="close" data-dismiss="modal">&times;</button>
						       		<strong>Add CAPEX</strong>
						      	</div>
						      	<div class="modal-body">
					        		<div class="text-center">
					        			<strong>Are you sure to save this CAPEX?</strong>
					        		</div><br />

					        		<div class="text-center">
					        			<button type=submit class="btn btn-sm btn-success" id="save-capex">Yes</button>&nbsp;&nbsp;<a href="" class="btn btn-sm btn-danger" data-dismiss="modal">No</a>
					        		</div>
						      	</div>
						    </div>
						</div>
					</div>
				</form>

				<div id="modal-confirm-error" class="modal fade modal-confirm" role="dialog">
					<div class="modal-dialog modal-sm">
					    <div class="modal-content">
					    	<div class="modal-header">
					        	<button type="button" class="close" data-dismiss="modal">&times;</button>
					       		<strong>Error</strong>
					      	</div>
					      	<div class="modal-body">
				        		<div class="text-center">
				        			<strong class="error-msg">Make sure all Cost Center is fill in?</strong>

				        		</div>
					      	</div>
					    </div>
					</div>
				</div>

				<div id="modal-slider-capex" class="modal fade" role="dialog">
					<div class="modal-dialog modal-sm">
					    <div class="modal-content">
					    	<div class="modal-header">
					        	<button type="button" class="close" data-dismiss="modal">&times;</button>
					       		<strong>Budgeting Slider Tool</strong>
					      	</div>
					      	<div class="modal-body">
				      			<input type="hidden" name="id" id="id">
				      			<div class="slider-div">
				      				<div class="form-group">
					      				<label>QTY:</label>
					      				<input type="number" class="form-control input-sm" id="slider-capex-val">
					      			</div>

						        	<input type="range" min="1" max="20" step="1" value="1" class="slider" id="slider-capex">
						        </div>
						        <div class="slider-div">
						        	<div class="form-group">
							        	<label>Month Start:&nbsp;&nbsp;<span id="slider-capex-start-val">1</span></label>
							        	<input type="range" min="1" max="12" value="1" class="slider" id="slider-capex-start">
							        </div>
						        </div>

						        <div class="slider-div">
						        	<div class="form-group">
							        	<label>Month End:&nbsp;&nbsp;<span id="slider-capex-end-val">12</span></label>
							        	<input type="range" min="1" max="12" value="12" class="slider" id="slider-capex-end">
							        </div>
						        </div>
						       
						        <div class="text-right">
						        	<a href="" class="btn btn-info btn-sm slider-capex-btn">Apply</a>
						        </div>
					      	</div>
					    </div>
					</div>
				</div>
			</div>
