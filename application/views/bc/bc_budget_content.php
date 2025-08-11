			
			<div class="col-lg-12" id="content">
				<div id="breadcrumb-div">
					<ul class="breadcrumb">
					    <li><a href="<?=base_url('business-center')?>"><span class="glyphicon glyphicon-home"></span>&nbsp;Dashboard</a></li>
					    <li><a href="<?=base_url('business-center/sales-info/')?>">Sales Info</a></li>
					    <li class="active">Transaction</li>
					</ul>
				</div>
				<?php
					if($this->session->flashdata('message') != "" ){
						echo $this->session->flashdata('message');
					}
				?>
				<div class="row">
					<div class="col-lg-3">
						<label id="data-info">Outlet Name: <?=$outlet_name . ' (' . $brand_code . ')' ?></label>
					</div>
					<div class="col-lg-3">
						<label id="data-info">Business Center: <?=$bc_name?></label>
					</div>

					<div class="col-lg-3">
						<label id="data-info">Budget Year: <?=$year?></label>
					</div>

					<div class="col-lg-3">
						<label id="data-info">Remaining: <span class="sales-remaining"></span></label>
					</div>

					<div class="col-lg-3">
						<label id="data-info">Total: <span class="sales-total"></span></label>
					</div>
				</div>
				
				<div class="row"><br />
					<div class="col-lg-12">
    					<label class="checkbox-inline">
							<input type="checkbox" data-toggle="toggle" id="toggle-qty" value="ASP" data-size="mini"> QTY Only
						</label>

						<label class="checkbox-inline">
							<input type="checkbox" data-toggle="toggle" id="toggle-asp" value="ASP" data-size="mini"> ASP Only
						</label>
					</div>
				</div>
				
				<!-- <label class="checkbox-inline">
						<input type="checkbox" data-toggle="toggle" id="toggle-asp" data-size="mini"> ASP only
				</label> -->

				<form action="<?=base_url('business-center/add-outlet-budget')?>" method="POST" enctype="multipart/form-data" id="add-sales">
					
					<input type="hidden" name="year" value="<?=$year?>" id="sales-year">

					<div class="row">
						<input type="hidden" name="id" value="<?=$outlet_id?>">
					</div>
					<div class="table-responsive">
						<table class="table table-bordered table-stripe nowrap" id="tbl-budget" style="width:100%">
							<thead>
								<tr>
									<th rowspan="2"></th>
									<th rowspan="2">Material Code</th>
									<th rowspan="2">Material Desc</th>
									<th rowspan="2">UOM</th>
									<th rowspan="2">Total</th>
									<th colspan="2" style="width: 10px;" class="text-center">Jan</th>
									<th colspan="2" style="width: 10px;" class="text-center">Feb</th>
									<th colspan="2" style="width: 10px;" class="text-center">Mar</th>
									<th colspan="2" style="width: 10px;" class="text-center">Apr</th>
									<th colspan="2" style="width: 10px;" class="text-center">May</th>
									<th colspan="2" style="width: 10px;" class="text-center">Jun</th>
									<th colspan="2" style="width: 10px;" class="text-center">Jul</th>
									<th colspan="2" style="width: 10px;" class="text-center">Aug</th>
									<th colspan="2" style="width: 10px;" class="text-center">Sep</th>
									<th colspan="2" style="width: 10px;" class="text-center">Oct</th>
									<th colspan="2" style="width: 10px;" class="text-center">Nov</th>
									<th colspan="2" style="width: 10px;" class="text-center">Dec</th>
								</tr>

								<tr>
									<th width="5%">QTY</th>
									<!-- <th></th>
									<th width="5%">Weight Unit</th> -->
									<th width="5%">ASP</th>
									<!-- <th width="5%">Sales</th>
									<th width="5%">Equivalent Unit</th> -->
									
									<!-- February -->
									<th>QTY</th>
									<!-- <th></th>
									<th>Weight Unit</th> -->
									<th>ASP</th><!-- 
									<th>Sales</th>
									<th>Equivalent Unit</th> -->

									<!-- March -->
									<th>QTY</th>
									<!-- <th></th>
									<th>Weight Unit</th> -->
									<th>ASP</th>
									<!-- <th>Sales</th>
									<th>Equivalent Unit</th> -->

									<!-- April -->
									<th>QTY</th>
									<!-- <th></th>
									<th>Weight Unit</th> -->
									<th>ASP</th>
									<!-- <th>Sales</th>
									<th>Equivalent Unit</th> -->

									<!-- May -->
									<th>QTY</th>
									<!-- <th></th>
									<th>Weight Unit</th> -->
									<th>ASP</th>
									<!-- <th>Sales</th>
									<th>Equivalent Unit</th> -->

									<!-- June -->
									<th>QTY</th>
									<!-- <th></th>
									<th>Weight Unit</th> -->
									<th>ASP</th>
									<!-- <th>Sales</th>
									<th>Equivalent Unit</th> -->

									<!-- July -->
									<th>QTY</th>
									<!-- <th></th>
									<th>Weight Unit</th> -->
									<th>ASP</th>
									<!-- <th>Sales</th>
									<th>Equivalent Unit</th> -->

									<!-- August -->
									<th>QTY</th>
									<!-- <th></th>
									<th>Weight Unit</th> -->
									<th>ASP</th>
									<!-- <th>Sales</th>
									<th>Equivalent Unit</th> -->

									<!-- September -->
									<th>QTY</th>
									<!-- <th></th>
									<th>Weight Unit</th> -->
									<th>ASP</th>
									<!-- <th>Sales</th>
									<th>Equivalent Unit</th> -->

									<!-- October -->
									<th>QTY</th>
									<!-- <th></th>
									<th>Weight Unit</th> -->
									<th>ASP</th>
									<!-- <th>Sales</th>
									<th>Equivalent Unit</th> -->

									<!-- November -->
									<th>QTY</th>
									<!-- <th></th>
									<th>Weight Unit</th> -->
									<th>ASP</th>
									<!-- <th>Sales</th>
									<th>Equivalent Unit</th> -->

									<!-- December -->
									<th>QTY</th>
									<!-- <th></th>
									<th>Weight Unit</th> -->
									<th>ASP</th>
									<!-- <th>Sales</th>
									<th>Equivalent Unit</th> -->
								</tr>
							</thead>
							
							<tbody>

								<?php
									$count = 0;
									foreach($material as $row):
										$count++;
								?>
								
								<tr class="row-<?=$count?>">
									<td><a href="#" class="remove remove-material-item"><span class="fa fa-remove"></span></a>&nbsp;&nbsp;<a href="#" class="slider-item" data-count="<?=$count?>"><span class="fa fa-sliders"></span></a></td>
									<td><?=$row->material_code?></td>
									<td><?=$row->material_desc?></td>
									<td><?=$row->unit_name?></td>
									<td class="text-right"><span class="sales-qty-total"></span></td>
									<!-- <td><?=$row->brand_name?></td> -->

									<!-- January -->
									<td class="budget-td">
										<input type="hidden" name="material[]" value="<?=encode($row->material_id)?>">
										<div class="form-group"><input style="width: 100px; height: 25px;" type="text" class="form-control input-sm budget-qty-jan sales-qty" data-id="<?=encode($row->material_id)?>" name="budget_qty[jan][]" data-ifs="<?=$outlet_id?>">&nbsp;&nbsp;</div>
									</td>
									<!-- <td><label><?=$row->unit_name?></label></td>
									<td class="budget-td"><label class="weight-unit-jan"></label></td> -->
									<td class="budget-td"><div class="form-group"><input type="text" style="width: 100px; height: 25px;" class="form-control input-sm budget-asp-jan sales-asp" data-id="<?=encode($row->material_id)?>" name="asp[jan][]" data-ifs="<?=$outlet_id?>"></div></td>
									<!-- <td class="budget-td"><label class="asp-jan"></label></td>
									<td class="budget-td"><label class="equivalent-unit-jan"></label></td> -->

									<!-- February -->
									<td class="budget-td"><div class="form-group"><input style="width: 100px; height: 25px;" type="text" class="form-control input-sm budget-qty-feb sales-qty" data-id="<?=encode($row->material_id)?>" name="budget_qty[feb][]" data-ifs="<?=$outlet_id?>">&nbsp;&nbsp;</div></td>
									<!-- <td><label><?=$row->unit_name?></label></td>
									<td class="budget-td"><label class="weight-unit-feb"></label></td> -->
									<td class="budget-td"><div class="form-group"><input type="text" style="width: 100px; height: 25px;" class="form-control input-sm budget-asp-feb sales-asp" data-id="<?=encode($row->material_id)?>" name="asp[feb][]" data-ifs="<?=$outlet_id?>"></div></td>
									<!-- <td class="budget-td"><label class="asp-feb"></label></td>
									<td class="budget-td"><label class="equivalent-unit-feb"></label></td> -->

									<!-- March -->
									<td class="budget-td"><div class="form-group"><input style="width: 100px; height: 25px;" type="text" class="form-control input-sm budget-qty-mar sales-qty" data-id="<?=encode($row->material_id)?>" name="budget_qty[mar][]" data-ifs="<?=$outlet_id?>">&nbsp;&nbsp;</div></td>
									<!-- <td><label><?=$row->unit_name?></label></td>
									<td class="budget-td"><label class="weight-unit-mar"></label></td> -->
									<td class="budget-td"><div class="form-group"><input type="text" style="width: 100px; height: 25px;" class="form-control input-sm budget-asp-mar sales-asp" data-id="<?=encode($row->material_id)?>" name="asp[mar][]" data-ifs="<?=$outlet_id?>"></div></td>
									<!-- <td class="budget-td"><label class="asp-mar"></label></td>
									<td class="budget-td"><label class="equivalent-unit-mar"></label></td> -->

									<!-- April -->
									<td class="budget-td"><div class="form-group"><input style="width: 100px; height: 25px;" type="text" class="form-control input-sm budget-qty-apr sales-qty" data-id="<?=encode($row->material_id)?>" name="budget_qty[apr][]" data-ifs="<?=$outlet_id?>">&nbsp;&nbsp;</div></td>
									<!-- <td><label><?=$row->unit_name?></label></td>
									<td class="budget-td"><label class="weight-unit-apr"></label></td> -->
									<td class="budget-td"><div class="form-group"><input type="text" style="width: 100px; height: 25px;" class="form-control input-sm budget-asp-apr sales-asp" data-id="<?=encode($row->material_id)?>" name="asp[apr][]" data-ifs="<?=$outlet_id?>"></div></td>
									<!-- <td class="budget-td"><label class="asp-apr"></label></td>
									<td class="budget-td"><label class="equivalent-unit-apr"></label></td> -->

									<!-- May -->
									<td class="budget-td"><div class="form-group"><input style="width: 100px; height: 25px;" type="text" class="form-control input-sm budget-qty-may sales-qty" data-id="<?=encode($row->material_id)?>" name="budget_qty[may][]" data-ifs="<?=$outlet_id?>">&nbsp;&nbsp;</div></td>
									<!-- <td><label><?=$row->unit_name?></label></td>
									<td class="budget-td"><label class="weight-unit-may"></label></td> -->
									<td class="budget-td"><div class="form-group"><input type="text" style="width: 100px; height: 25px;" class="form-control input-sm budget-asp-may sales-asp" data-id="<?=encode($row->material_id)?>" name="asp[may][]" data-ifs="<?=$outlet_id?>"></div></td>
									<!-- <td class="budget-td"><label class="asp-may"></label></td>
									<td class="budget-td"><label class="equivalent-unit-may"></label></td> -->
									
									<!-- June -->
									<td class="budget-td"><div class="form-group"><input style="width: 100px; height: 25px;" type="text" class="form-control input-sm budget-qty-jun sales-qty" data-id="<?=encode($row->material_id)?>" name="budget_qty[jun][]" data-ifs="<?=$outlet_id?>">&nbsp;&nbsp;</div></td>
									<!-- <td><label><?=$row->unit_name?></label></td>
									<td class="budget-td"><label class="weight-unit-jun"></label></td> -->
									<td class="budget-td"><div class="form-group"><input type="text" style="width: 100px; height: 25px;" class="form-control input-sm budget-asp-jun sales-asp" data-id="<?=encode($row->material_id)?>" name="asp[jun][]" data-ifs="<?=$outlet_id?>"></div></td>
									<!-- <td class="budget-td"><label class="asp-jun"></label></td>
									<td class="budget-td"><label class="equivalent-unit-jun"></label></td> -->

									<!-- July -->
									<td class="budget-td"><div class="form-group"><input style="width: 100px; height: 25px;" type="text" class="form-control input-sm budget-qty-jul sales-qty" data-id="<?=encode($row->material_id)?>" name="budget_qty[jul][]" data-ifs="<?=$outlet_id?>">&nbsp;&nbsp;</div></td>
									<!-- <td><label><?=$row->unit_name?></label></td>
									<td class="budget-td"><label class="weight-unit-jul"></label></td> -->
									<td class="budget-td"><div class="form-group"><input type="text" style="width: 100px; height: 25px;" class="form-control input-sm budget-asp-jul sales-asp" data-id="<?=encode($row->material_id)?>" name="asp[jul][]" data-ifs="<?=$outlet_id?>"></div></td>
									<!-- <td class="budget-td"><label class="asp-jul"></label></td>
									<td class="budget-td"><label class="equivalent-unit-jul"></label></td> -->

									<!-- August -->
									<td class="budget-td"><div class="form-group"><input style="width: 100px; height: 25px;" type="text" class="form-control input-sm budget-qty-aug sales-qty" data-id="<?=encode($row->material_id)?>" name="budget_qty[aug][]" data-ifs="<?=$outlet_id?>">&nbsp;&nbsp;</div></td>
									<!-- <td><label><?=$row->unit_name?></label></td>
									<td class="budget-td"><label class="weight-unit-aug"></label></td> -->
									<td class="budget-td"><div class="form-group"><input type="text" style="width: 100px; height: 25px;" class="form-control input-sm budget-asp-aug sales-asp" data-id="<?=encode($row->material_id)?>" name="asp[aug][]" data-ifs="<?=$outlet_id?>"></div></td>
									<!-- <td class="budget-td"><label class="asp-aug"></label></td>
									<td class="budget-td"><label class="equivalent-unit-aug"></label></td> -->

									<!-- September -->
									<td class="budget-td"><div class="form-group"><input style="width: 100px; height: 25px;" type="text" class="form-control input-sm budget-qty-sep sales-qty" data-id="<?=encode($row->material_id)?>" name="budget_qty[sep][]" data-ifs="<?=$outlet_id?>">&nbsp;&nbsp;</div></td>
									<!-- <td><label><?=$row->unit_name?></label></td>
									<td class="budget-td"><label class="weight-unit-sep"></label></td> -->
									<td class="budget-td"><div class="form-group"><input type="text" style="width: 100px; height: 25px;" class="form-control input-sm budget-asp-sep sales-asp" data-id="<?=encode($row->material_id)?>" name="asp[sep][]" data-ifs="<?=$outlet_id?>"></div></td>
									<!-- <td class="budget-td"><label class="asp-sep"></label></td>
									<td class="budget-td"><label class="equivalent-unit-sep"></label></td> -->

									<!-- October -->
									<td class="budget-td"><div class="form-group"><input style="width: 100px; height: 25px;" type="text" class="form-control input-sm budget-qty-oct sales-qty" data-id="<?=encode($row->material_id)?>" name="budget_qty[oct][]" data-ifs="<?=$outlet_id?>">&nbsp;&nbsp;</div></td>
									<!-- <td><label><?=$row->unit_name?></label></td>
									<td class="budget-td"><label class="weight-unit-oct"></label></td> -->
									<td class="budget-td"><div class="form-group"><input type="text" style="width: 100px; height: 25px;" class="form-control input-sm budget-asp-oct sales-asp" data-id="<?=encode($row->material_id)?>" name="asp[oct][]" data-ifs="<?=$outlet_id?>"></div></td>
									<!-- <td class="budget-td"><label class="asp-oct"></label></td>
									<td class="budget-td"><label class="equivalent-unit-oct"></label></td> -->

									<!-- November -->
									<td class="budget-td"><div class="form-group"><input style="width: 100px; height: 25px;" type="text" class="form-control input-sm budget-qty-nov sales-qty" data-id="<?=encode($row->material_id)?>" name="budget_qty[nov][]" data-ifs="<?=$outlet_id?>">&nbsp;&nbsp;</div></td>
									<!-- <td><label><?=$row->unit_name?></label></td>
									<td class="budget-td"><label class="weight-unit-nov"></label></td> -->
									<td class="budget-td"><div class="form-group"><input type="text" style="width: 100px; height: 25px;" class="form-control input-sm budget-asp-nov sales-asp" data-id="<?=encode($row->material_id)?>" name="asp[nov][]" data-ifs="<?=$outlet_id?>"></div></td>
									<!-- <td class="budget-td"><label class="asp-nov"></label></td>
									<td class="budget-td"><label class="equivalent-unit-nov"></label></td> -->

									<!-- December -->
									<td class="budget-td"><div class="form-group"><input style="width: 100px; height: 25px;" type="text" class="form-control input-sm budget-qty-dec sales-qty" data-id="<?=encode($row->material_id)?>" name="budget_qty[dec][]" data-ifs="<?=$outlet_id?>">&nbsp;&nbsp;</div></td>
									<!-- <td><label><?=$row->unit_name?></label></td>
									<td class="budget-td"><label class="weight-unit-dec"></label></td> -->
									<td class="budget-td"><div class="form-group"><input type="text" style="width: 100px; height: 25px;" class="form-control input-sm budget-asp-dec sales-asp" data-id="<?=encode($row->material_id)?>" name="asp[dec][]" data-ifs="<?=$outlet_id?>"></div></td>
									<!-- <td class="budget-td"><label class="asp-dec"></label></td>
									<td class="budget-td"><label class="equivalent-unit-dec"></label></td> -->
								</tr>

								<?php endforeach;?>

							</tbody>
						</table>
					</div>
					<div class="row">
						<div class="col-lg-12 text-right">
							<button type="submit" class="btn-add-sales btn btn-success btn-sm">Submit</button>
						</div>
					</div><br /><br />

					<div id="modal-confirm-sales" class="modal fade" role="dialog">
						<div class="modal-dialog modal-sm">
						    <div class="modal-content">
						    	<div class="modal-header">
						        	<button type="button" class="close" data-dismiss="modal">&times;</button>
						       		<strong>Confirmation message</strong>
						      	</div>
						      	<div class="modal-body">
						        	<div id="modal-msg" class="text-center">
						        		<label>Are you sure to save this sales record??</label>
						        	</div>
						        	<div id="modal-btn" class="text-center">
						        		<button type="submit" class="btn btn-info btn-sm">Yes</button>&nbsp;&nbsp;
						        		<a href="" data-dismiss="modal" class="btn btn-danger btn-sm">No</a>
						        	</div>
						      	</div>
						    </div>
						</div>
					</div>
				</form>

				

				<div id="modal-slider-sales" class="modal fade" role="dialog">
					<div class="modal-dialog modal-sm">
					    <div class="modal-content">
					    	<div class="modal-header">
					        	<button type="button" class="close" data-dismiss="modal">&times;</button>
					       		<strong>Budgeting Slider Tool</strong>
					      	</div>
					      	<div class="modal-body">
				      			<input type="hidden" name="id" id="id">
				      			<div class="slider-div">
					      			<label>QTY:&nbsp;&nbsp;</label><input type="number" class="form-control input-sm" id="slider-qty-val"><br />
						        	<input type="range" min="1" max="5000" value="0" class="slider" id="slider-qty">
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
					      			<label>ASP: </label><input type="number" class="form-control input-sm" id="slider-asp-val"><br />
						        	<input type="range" min="1" max="1000" value="0" class="slider" id="slider-asp">
						        </div>

						        <div class="slider-div">
						        	<label>Month Start:&nbsp;&nbsp;<span id="slider-asp-start-val"></span></label>
						        	<input type="range" min="1" max="12" value="1" class="slider" id="slider-asp-start">
						        </div>

						        <div class="slider-div">
						        	<label>Month End:&nbsp;&nbsp;<span id="slider-asp-end-val"></span></label>
						        	<input type="range" min="1" max="12" value="12" class="slider" id="slider-asp-end">
						        </div>

						        <div class="text-right">
						        	<a href="" class="btn btn-info btn-sm slider-sales-btn">Apply</a>
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
				        			<strong class="error-msg">Make sure all encode numbers only.</strong>

				        		</div>
					      	</div>
					    </div>
					</div>
				</div>
			</div>