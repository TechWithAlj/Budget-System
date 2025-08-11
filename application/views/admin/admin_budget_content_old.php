			
			<div class="col-lg-12" id="content">
				<div id="breadcrumb-div">
					<ul class="breadcrumb">
					    <li><a href="<?=base_url('admin')?>"><span class="glyphicon glyphicon-home"></span>&nbsp;Dashboard</a></li>
					    <li class="active">Outlet Budget</li>
					</ul>
				</div>
				<?php
					if($this->session->flashdata('message') != "" ){
						echo $this->session->flashdata('message');
					}
				?>
				<div class="row">
					<div class="col-lg-4">
						<label id="data-info">Outlet Name: <?=$outlet_name?></label>
					</div>
					<div class="col-lg-4">
						<label id="data-info">Business Center: <?=$bc_name?></label>
					</div>
				</div>
				<ul class="nav nav-tabs">
   					<li class="active"><a data-toggle="tab" class="tab-letter" href="#budget-tab">Budget</a></li>
				    <li><a data-toggle="tab" href="#history-tab" class="tab-letter">History</a></li>
  				</ul>

				<div class="tab-content">
    				<div id="budget-tab" class="tab-pane fade in active">
						<form action="<?=base_url('admin/add-outlet-budget')?>" method="POST" enctype="multipart/form-data" id="add-outlet-budget">
							<div class="row"><br>
								<div class="col-lg-2">
									<label>Pick Month:</label>
									<div class="form-group">
										<div class="date">
			                                <div class="input-group input-append date" id="budget-month">
			                                    <input type="text" name="month" id="date-pick-month" class="form-control input-sm" placeholder="Pick month" value="">
			                                    <span class="input-group-addon add-on"><span class="glyphicon glyphicon-calendar"></span></span>
			                                </div>
			                            </div>
									</div>
								</div>

								<input type="hidden" name="id" value="<?=$id?>">
							</div>
							
							<table class="table table-hover" id="tbl-budget">
								<thead>
									<tr>
										<th>Material Code</th>
										<th>Material Desc</th>
										<th>Brand</th>
																		<th style="width: 10px;">QTY</th>
										<th></th>
										<th>Weight Unit</th>
										<th>ASP</th>
										<th>Sales</th>
										<th>Equivalent Unit</th>
										<!-- <th>Action</th> -->
									</tr>
								</thead>
								<tbody>

									<?php
										foreach($material as $row):
									?>
									
									<tr>
										<td><?=$row->material_code?><input type="hidden" name="material[]" value="<?=encode($row->material_id)?>"></td>
										<td><?=$row->material_desc?></td>
										<td><?=$row->brand_name?></td>
										<td class="budget-td"><div class="form-group"><input style="width: 100px; height: 25px;" type="text" class="form-control input-sm budget-qty" data-id="<?=encode($row->material_id)?>" name="budget_qty[]" data-ifs="<?=$outlet_id?>">&nbsp;&nbsp;</div></td>
										<td><label><?=$row->unit_name?></label></td>
										<td class="budget-td"><label class="weight-unit"></label></td>
										<td class="budget-td"><div class="form-group"><input type="text" style="width: 100px; height: 25px;" class="form-control input-sm budget-asp" data-id="<?=encode($row->material_id)?>" name="asp[]" data-ifs="<?=$outlet_id?>" value="<?=$row->previous_data?>"></div></td>
										<td class="budget-td"><label class="asp"></label></td>
										<td class="budget-td"><label class="equivalent-unit"></label></td>
										<!-- <td><a href="" class="remove-brand-material" data-id="<?=encode($row->brand_material_id)?>"><span class="glyphicon glyphicon-remove"></span></a>&nbsp;&nbsp;</td> -->
									</tr>

									<?php endforeach;?>

								</tbody>
							</table>

							<div class="row">
								<div class="col-lg-12 text-right">
									<button class="btn-budget btn btn-success btn-sm">Submit</button>
								</div>
							</div><br /><br />
						</form>

						<div id="modal-confirm" class="modal fade" role="dialog">
							<div class="modal-dialog modal-sm">
							    <div class="modal-content">
							    	<div class="modal-header">
							        	<button type="button" class="close" data-dismiss="modal">&times;</button>
							       		<strong>Confirmation message</strong>
							      	</div>
							      	<div class="modal-body">
							      		<form method="POST" action="<?=base_url('admin/remove-brand-material')?>" enctype="multipart/form-data" id="remove-brand-material">
							      			<input type="hidden" name="id" id="id">
								        	<div id="modal-msg" class="text-center">
								        		<label>Are you sure to remove brand?</label>
								        	</div>
								        	<div id="modal-btn" class="text-center">
								        		<button type="submit" class="btn btn-info btn-sm">Yes</button>&nbsp;&nbsp;
								        		<a href="" data-dismiss="modal" class="btn btn-danger btn-sm">No</a>
								        	</div>
								        </form>
							      	</div>
							    </div>
							</div>
						</div>
					</div>

					<div id="history-tab" class="tab-pane fade">
						<form action="<?=base_url('admin/add-budget')?>" method="POST" enctype="multipart/form-data" id="add-budget">
							<table class="table table-hover" id="tbl-budget-history">
								<thead>
									<tr>
										<th>Date</th>
										<th>User</th>
										<th>Action</th>
									</tr>
								</thead>
								<tbody>

									<?php
										foreach($history as $row):
									?>
									
									<tr>
										<td><?=date('M - Y', strtotime($row->budget_date))?></td>
										<td><?=$row->user_lname . ', '. $row->user_fname?></td>
										<td><a href="<?=base_url('admin/preview-budget/' . encode($row->budget_id))?>" class="btn btn-info btn-xs" data-id="">Preview</a>&nbsp;&nbsp;<a href="" class="remove-budget" data-id="<?=encode($row->budget_id)?>"><span class="glyphicon glyphicon-remove"></span></a>&nbsp;&nbsp;</td>
									</tr>

									<?php endforeach;?>

								</tbody>
							</table>

							<div class="row">
								<div class="col-lg-12 text-right">
									<button class="btn-budget btn btn-success btn-sm">Submit</button>
								</div>
							</div><br /><br />
						</form>

						<div id="modal-confirm" class="modal fade" role="dialog">
							<div class="modal-dialog modal-sm">
							    <div class="modal-content">
							    	<div class="modal-header">
							        	<button type="button" class="close" data-dismiss="modal">&times;</button>
							       		<strong>Confirmation message</strong>
							      	</div>
							      	<div class="modal-body">
							      		<form method="POST" action="<?=base_url('admin/remove-brand-material')?>" enctype="multipart/form-data" id="remove-brand-material">
							      			<input type="hidden" name="id" id="id">
								        	<div id="modal-msg" class="text-center">
								        		<label>Are you sure to remove brand?</label>
								        	</div>
								        	<div id="modal-btn" class="text-center">
								        		<button type="submit" class="btn btn-info btn-sm">Yes</button>&nbsp;&nbsp;
								        		<a href="" data-dismiss="modal" class="btn btn-danger btn-sm">No</a>
								        	</div>
								        </form>
							      	</div>
							    </div>
							</div>
						</div>
					</div>
				</div>
			</div>