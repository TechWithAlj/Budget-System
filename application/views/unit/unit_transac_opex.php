			
			<div class="col-lg-12" id="content">
				<div id="breadcrumb-div">
					<ul class="breadcrumb">
					    <li><a href="<?=base_url('unit')?>"><span class="glyphicon glyphicon-home"></span>&nbsp;Dashboard</a></li>
					    <li><a href="<?=base_url('unit/opex-info/')?>">OPEX Info</a></li>
					    <li class="active">Transaction</li>
					</ul>
				</div>
				<?php
					if($this->session->flashdata('message') != "" ){
						echo $this->session->flashdata('message');
					}
				?>

				<form method="POST" id="add-opex-form">
					<input type="hidden" name="year" id="opex-year" value="<?=$year?>">
					<div class="row">
						<div class="col-lg-4">
							<label class="data-info">Cost Center: <?=$cost_center_name?></label><br /><br />
						</div>

						<div class="col-lg-4">
							<label class="data-info">Budget Year: <?=$year?></label><br /><br />
						</div>

						<div class="col-lg-4">
							<label class="data-info">Grand Total: <span class="opex-grand-total"></span></label>
						</div>
					</div>

					<div class="row">
						<div class="col-lg-3">
							<input type="hidden" id="cost-center" name="bc_cost_center" value="<?=$id?>">
							<label class="data-info">GL Group:</label>
							<select name="gl_group" id="opex-gl" class="form-control">
								<option value="">Select...</option>

								<?php foreach($gl_group as $row_gl):?>

									<option value="<?=encode($row_gl->gl_group_id)?>"><?=$row_gl->gl_group_name?></option>

								<?php endforeach;?>
							</select>
						</div>
					</div>

					<div class="row">
						<div class="col-lg-12">
							<div class="table-responsive">
								<table class="table table-bordered" id="tbl-transac-opex">
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
									</tbody>
								</table>
							</div>
						</div>
					</div>

					<div class="text-right" id="expenditures-add-btn">
						<button type="submit" class="btn btn-success btn-sm btn-save-opex">Save OPEX</button>
					</div>

					<div id="modal-confirm-opex" class="modal fade modal-confirm" role="dialog">
						<div class="modal-dialog modal-sm">
						    <div class="modal-content">
						    	<div class="modal-header">
						        	<button type="button" class="close" data-dismiss="modal">&times;</button>
						       		<strong>Add OPEX</strong>
						      	</div>
						      	<div class="modal-body">
					        		<div class="text-center">
					        			<strong>Are you sure to save this OPEX?</strong>
					        		</div><br />

					        		<div class="text-center">
					        			<button type=submit class="btn btn-sm btn-success" id="save-opex">Yes</button>&nbsp;&nbsp;<a href="" class="btn btn-sm btn-danger" data-dismiss="modal">No</a>
					        		</div>
						      	</div>
						    </div>
						</div>
					</div>

					<div id="modal-confirm-error" class="modal fade modal-confirm" role="dialog">
						<div class="modal-dialog modal-sm">
						    <div class="modal-content">
						    	<div class="modal-header">
						        	<button type="button" class="close" data-dismiss="modal">&times;</button>
						       		<strong>Error</strong>
						      	</div>
						      	<div class="modal-body">
					        		<div class="text-center">
					        			<strong class="error-msg">Make sure all Cost Center is fill in.</strong>

					        		</div>
						      	</div>
						    </div>
						</div>
					</div>
				</form>

				

				<div id="modal-slider-opex" class="modal fade" role="dialog">
					<div class="modal-dialog modal-sm">
					    <div class="modal-content">
					    	<div class="modal-header">
					        	<button type="button" class="close" data-dismiss="modal">&times;</button>
					       		<strong>Budgeting Slider Tool</strong>
					      	</div>
					      	<div class="modal-body">
				      			<input type="hidden" id="id">
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
						        	<a href="" class="btn btn-info btn-sm slider-opex-btn">Apply</a>
						        </div>
					        	
					      	</div>
					    </div>
					</div>
				</div>
			</div>

			<script type="text/javascript">
				$(document).find('input.opex-qty').bind('paste', null, function(e){
					 $this = $(this);

				    setTimeout(function(){
				        var columns = $this.val().split(/\s+/);
				        var i;
				      	var input =  $this;

				        for(i=0; i < columns.length; i++){

				            input.val(columns[i]);
				            input = input.parent().next().find('.opex-qty');
				            console.log(input);
				            console.log(columns[i]);
				        }
				    }, 0);
				});
			</script>