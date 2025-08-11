			
			<div class="col-lg-12" id="content">
				<div id="breadcrumb-div">
					<ul class="breadcrumb">
					    <li><a href="<?=base_url('admin')?>"><span class="glyphicon glyphicon-home"></span>&nbsp;Dashboard</a></li>
					    <li class="active"><span class="fa fa-bar-chart"></span>&nbsp;Report</li>
					</ul>
				</div>

				<?php
					if($this->session->flashdata('message') != "" ){
						echo $this->session->flashdata('message');
					}
				?>
				<div class="col-lg-5">
					<table class="table table-hover" id="tbl-purchase">
						<thead>
							<tr>
								<th>Material Code</th>
								<th>Material Desc</th>
								<th>UM</th>
								<th>Action</th>
							</tr>
						</thead>
						<tbody>

							<?php foreach($material as $row):?>

							<tr>
								<input type="hidden" value="<?=encode($row->material_id)?>" class="id">
								<td class="code"><?=$row->material_code?></td>
								<td class="desc"><?=$row->material_desc?></td>
								<td class="um"><?=$row->um_name?></td>
								<td><a href="#" class="glyphicon glyphicon-plus item-add" data-id="<?=encode($row->material_id)?>"></a></td>
							</tr>

							<?php endforeach;?>
						</tbody>
					</table>
				</div>

				<div class="col-lg-7">
					<div id="order">
						<div id="order-title">
							<img src="<?=base_url()?>assets/img/icon/purchase.png"><strong>&nbsp;PLACE YOUR ORDER HERE!!!</strong>
						</div>

						<div id="order-content">
							<form method="POST" action="<?=base_url('admin/add-purchase')?>" id="add-purchase" class="form-inline">
								
								<div id="order-info">
									<div class="col-lg-6">
										<div class="form-group">
											<label>Business center:</label>
											<select name="business_center" class="form-control" id="select-bc">
												<option value="">Select...</option>

												<?php foreach($business as $row):?>

												<option value="<?=encode($row->bc_id)?>"><?=$row->bc_name?></option>

												<?php endforeach;?>
											</select>
										</div>
									</div>

									<div class="col-lg-6">
										<label>Month:</label>
										<div class="form-group">
											<div class="date">
			                                    <div class="input-group input-append date" id="month">
			                                        <input type="text" name="month" id="date-pick-month" class="form-control input-sm" placeholder="Pick month" value="">
			                                        <span class="input-group-addon add-on"><span class="glyphicon glyphicon-calendar"></span></span>
			                                    </div>
			                                </div>
										</div>
									</div>
								</div>

								<div class="div-order">
									<table class="table table-hover" id="table-order">
										<thead>
											<tr>
												<th></th>
												<th>Code</th>
												<th>Desc</th>
												<th>QTY</th>
												<th>UM</th>
											</tr>
										</thead>

										<tbody></tbody>
									</table>
								</div>

								<div class="text-right">
									<a class="btn btn-info btn-sm btn-po" data-toggle="modal" data-target="#modal-confirm">Submit</a>
								</div>

								<div id="modal-confirm" class="modal fade" role="dialog">
									<div class="modal-dialog modal-sm">
									    <div class="modal-content">
									    	<div class="modal-header">
									        	<button type="button" class="close" data-dismiss="modal">&times;</button>
									       		<strong>Confirmation message</strong>
									      	</div>
									      	<div class="modal-body">
									        	<div id="modal-msg" class="text-center">
									        		<label>Are you sure to submit order?</label>
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
						</div>
					</div>
				</div>

				<div id="modal-exist" class="modal fade" role="dialog">
					<div class="modal-dialog modal-sm">
					    <div class="modal-content">
					    	<div class="modal-header">
					        	<button type="button" class="close" data-dismiss="modal">&times;</button>
					       		<strong>Exist</strong>
					      	</div>
					      	<div class="modal-body">
					        	<div id="modal-msg" class="text-center">
					        		<label>Sorry material already exist.</label>
					        	</div>
					      	</div>
					    </div>
					</div>
				</div>
			</div>
