			
			<div class="col-lg-12" id="content">
				<div id="breadcrumb-div">
					<ul class="breadcrumb">
					    <li><a href="<?=base_url('admin')?>"><span class="glyphicon glyphicon-home"></span>&nbsp;Dashboard</a></li>
					    <li><a href="<?=base_url('admin/opex')?>">OPEX</a></li>
					    <li><a href="<?=base_url('admin/opex-info/' . $cost_center_code)?>">Info</a></li>
					    <li><a href="<?=base_url('admin/sw-view/' . $id)?>">View</a></li>
					    <li class="active">Add Item</li>
					</ul>
				</div>
				<?php
					if($this->session->flashdata('message') != "" ){
						echo $this->session->flashdata('message');
					}
				?>

				<div class="row">
					<div class="col-lg-3">
						<input type="hidden" id="cost-center" name="cost_center" value="<?=$cost_center_code?>">
						<label class="data-info">GL Group: <?=$gl_group?></label>
					</div>

				</div>

				<div class="row">
					<div class="col-lg-12">
						<form method="POST" action="<?=base_url('admin/add-trans-sw-item')?>" id="add-trans-opex-item">
							<input type="hidden" id="id" name="emp_salary_trans_id" value="<?=$id?>">
							<div class="table-responsive">
								<table class="table table-bordered" id="tbl-opex-item">
									<thead>
										<tr>
											<th class="text-center" width="60px"></th>
												<th width="7%">GL</th>
												<th width="13%">Cost Center</th>
												<th class="text-center" width="5%">Total</th>
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
										<?=$gl?>
									</tbody>
								</table>

								<div class="text-right" id="expenditures-add-btn">
									<button type="submit" class="btn btn-success btn-sm btn-save-opex">Add Item</button>
								</div>
							</div>

							<div id="modal-confirm-opex" class="modal fade modal-confirm" role="dialog">
								<div class="modal-dialog modal-sm">
								    <div class="modal-content">
								    	<div class="modal-header">
								        	<button type="button" class="close" data-dismiss="modal">&times;</button>
								       		<strong>Add Salaries & Wages</strong>
								      	</div>
								      	<div class="modal-body">
							        		<div class="text-center">
							        			<strong>Are you sure to save this Salaries & Wages?</strong>
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

						<div id="modal-slider-opex" class="modal fade" role="dialog">
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
							      				<label>Budget:</label>
							      				<input type="number" class="form-control input-sm" id="slider-opex-val">
							      			</div>

								        	<input type="range" min="1" max="500000" step="999" value="1" class="slider" id="slider-opex">
								        </div>
								        <div class="slider-div">
								        	<div class="form-group">
									        	<label>Month Start:&nbsp;&nbsp;<span id="slider-opex-start-val">1</span></label>
									        	<input type="range" min="1" max="12" value="1" class="slider" id="slider-opex-start">
									        </div>
								        </div>

								        <div class="slider-div">
								        	<div class="form-group">
									        	<label>Month End:&nbsp;&nbsp;<span id="slider-opex-end-val">12</span></label>
									        	<input type="range" min="1" max="12" value="12" class="slider" id="slider-opex-end">
									        </div>
								        </div>
								       
								        <div class="text-right">
								        	<a href="" class="btn btn-info btn-sm slider-opex-item-btn">Apply</a>
								        </div>
							      	</div>
							    </div>
							</div>
						</div>
					</div>
				</div>
			</div>