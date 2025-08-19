			
			<div class="col-lg-12" id="content">
				<div id="breadcrumb-div">
					<ul class="breadcrumb">
					    <li><a href="<?=base_url('unit')?>"><span class="glyphicon glyphicon-home"></span>&nbsp;Dashboard</a></li>
					    <li><a href="<?=base_url('unit/capex-info/' . $year)?>">CAPEX Info</a></li>
					    <li><a href="<?=base_url('unit/view-capex/' . $id)?>">View</a></li>
					    <li class="active">Add Item</li>
					</ul>
				</div>
				<?php
					if($this->session->flashdata('message') != "" ){
						echo $this->session->flashdata('message');
					}
				?>
				<form method="POST" action="<?=base_url('unit/add-trans-capex-item')?>" id="add-trans-capex-item">
					<input type="hidden" id="id" name="ag_trans_id" value="<?=$id?>">
					<input type="hidden" id="cost-center" name="bc_cost_center" value="<?=$cost_center_code?>">
					<div class="row">
						<div class="col-lg-3">
							<label class="data-info">Cost Center: <?=$cost_center_name?></label><br /><br />
						</div>

						<div class="col-lg-3">
							<label class="data-info">Asset Group: <?=$ag_name?></label><br /><br />
						</div>

						<div class="col-lg-3">
							<label class="data-info">Budget Year: <?=$year?></label><br /><br />
						</div>

						<div class="col-lg-3">
							<label class="data-info">Grand Total: <span class="capex-grand-total"></span></label>
						</div>
					</div>

					<div class="row">
						<div class="col-lg-12">
							<div class="table-responsive">
								<table class="table table-striped table-bordered nowrap" id="tbl-capex-item">
									<thead>
										<?=$header?>
									</thead>
									<tbody>
										<?=$ag?>
									</tbody>
								</table>
							</div>
						</div>
					</div>
					<script type="text/javascript">
						$('#tbl-capex-item').DataTable({
							"scrollX": true,
							"scrollY": "300px",
							"fixedHeader": true,
							"bInfo": false,
							"paging": false,
						});
					</script>
					<div class="text-right" id="expenditures-add-btn">
						<button type="submit" class="btn btn-success btn-sm btn-save-capex">Add Item</button>
					</div>

					<div id="modal-confirm-capex" class="modal fade modal-confirm" role="dialog">
						<div class="modal-dialog modal-sm">
						    <div class="modal-content">
						    	<div class="modal-header">
						        	<button type="button" class="close" data-dismiss="modal">&times;</button>
						       		<strong>Add CAPEX Item</strong>
						      	</div>
						      	<div class="modal-body">
					        		<div class="text-center">
					        			<strong>Are you sure to add this CAPEX?</strong>
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
				        			<strong class="error-msg">Make sure all Cost Center, Type of CAPEX, and Maintenance Category is fill in?</strong>

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
						        	<a href="" class="btn btn-info btn-sm slider-capex-item-btn">Apply</a>
						        </div>
					      	</div>
					    </div>
					</div>
				</div>
			</div>
