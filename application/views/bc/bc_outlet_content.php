			
			<div class="col-lg-12" id="content">
				<div id="breadcrumb-div">
					<ul class="breadcrumb">
					    <li><a href="<?=base_url('business-center')?>"><span class="glyphicon glyphicon-home"></span>&nbsp;Dashboard</a></li>
					    <li class="active">Outlets</li>
					</ul>
				</div>
				<?php
					if($this->session->flashdata('message') != "" ){
						echo $this->session->flashdata('message');
					}
				?>
				<div id="add-btn">
					<div class="row">
						<?php if($budget_status == 1):?>
							<div class="col-lg-2">
								<a href="" class="btn btn-success btn-xs" data-toggle="modal" data-backdrop="static" data-target="#modal-outlet">+ Add Outlet</a>&nbsp;&nbsp;<!-- <a href="" class="btn btn-info btn-xs" data-toggle="modal" data-backdrop="static" data-target="#modal-budget">+ Upload Budget</a> -->

								<div id="modal-outlet" class="modal fade modal-confirm" role="dialog">
									<div class="modal-dialog modal-sm">
									    <div class="modal-content">
									    	<div class="modal-header">
									        	<button type="button" class="close" data-dismiss="modal">&times;</button>
									       		<strong>Add Outlet</strong>
									      	</div>
									      	<div class="modal-body">
									        	<form method="POST" action="<?=base_url('business-center/add-outlet')?>" enctype="multipart/form-data" id="add-outlet">
									        		
									        		<input type="hidden" name="year" value="<?=$year?>">

									        		<div class="form-group">
									        			<label>Type:</label>
									        			<select class="form-control" name="type" id="type">
									        				<option value="">Select...</option>
									        				<?php foreach($type as $row_type):?>

									        				<option value="<?=encode($row_type->brand_type_id)?>"><?=$row_type->brand_type_name?></option>

									        				<?php endforeach;?>
									        			</select>
									        		</div>

									        		<div class="form-group">
									        			<label>Brand:</label>
									        			<select class="form-control" name="brand[]" id="brand">
									        				<option value="">Select...</option>
									        			</select>
									        		</div>

									        		<div class="form-group">
									        			<label>Outlet Name</label>
									        			<input type="text" class="form-control input-sm" name="outlet" id="outlet" readonly="true">
									        		</div>

									        		<div class="form-group">
									        			<label>IFS Code</label>
									        			<input type="text" class="form-control input-sm" name="ifs" id="ifs" readonly="true">
									        		</div>

									        		<!-- <div class="form-group">
									        			<label>Region:</label>
									        			<select class="form-control" name="region" id="region">
									        				<option value="">Select...</option>

									        				<?php foreach($region as $row_region):?>

									        				<option value="<?=encode($row_region->region_id)?>"><?=$row_region->region_name?></option>

									        				<?php endforeach;?>

									        			</select>
									        		</div> -->

									        		

									        		

									        		<div class="btn-update">
									        			<button type="submit" class="btn btn-info btn-sm pull-right">Add</button><br>
									        		</div>
									        	</form>
									      	</div>
									    </div>
									</div>
								</div>
							</div>

							<div class="col-lg-2">
								<div class="form-group">
									<div class="date">
				                        <div class="input-group input-append date" id="outlet-trans-year">
				                            <input type="text" name="month" id="outlet-year" class="form-control input-sm" placeholder="Pick year" value="<?=$year?>">
				                            <span class="input-group-addon add-on"><span class="glyphicon glyphicon-calendar"></span></span>
				                        </div>
				                    </div>
								</div>
							</div>

							<div class="col-lg-8">
								<div class="text-right">
									<a href="<?=base_url('business-center/download-outlets/' . $year)?>" target="_blank"><span class="glyphicon glyphicon-download-alt"></span>&nbsp;&nbsp; Download outlets</a>
								</div>
							</div>

						<?php else:?>
							<div class="col-lg-2">
								<div class="form-group">
									<div class="date">
				                        <div class="input-group input-append date" id="outlet-trans-year">
				                            <input type="text" name="month" id="outlet-year" class="form-control input-sm" placeholder="Pick year" value="<?=$year?>">
				                            <span class="input-group-addon add-on"><span class="glyphicon glyphicon-calendar"></span></span>
				                        </div>
				                    </div>
								</div>
							</div>


							<div class="col-lg-10">
								<div class="text-right">
									<a href="<?=base_url('business-center/download-outlets/' . $year)?>" target="_blank"><span class="glyphicon glyphicon-download-alt"></span>&nbsp;&nbsp; Download outlets</a>
								</div>
							</div>
						<?php endif;?>
					</div>
				</div>
				
				<table class="table table-hover" id="tbl-sales">
					<thead>
						<tr>
							<th>Outlet Code</th>
							<th>Outlet Name</th>
							<th>Brand</th>
							<th>Region</th>
							<th>BC</th>
							<th>Type</th>
						</tr>
					</thead>
					<tbody>

						<?php
							foreach($outlets as $row):
						?>
						
						<tr>
							<td><?=$row->ifs_code?></td>
							<td><?=$row->outlet_name?></td>
							<td><?=$row->brand_name?></td>
							<td><?=$row->region_name?></td>
							<td><?=$row->bc_name?></td>
							<td><?=$row->outlet_type_name?></td>
						</tr>

						<?php endforeach;?>

					</tbody>
				</table>
			</div>