			
			<div class="col-lg-12" id="content">
				<div id="breadcrumb-div">
					<ul class="breadcrumb">
					    <li><a href="<?=base_url('business-center')?>"><span class="glyphicon glyphicon-home"></span>&nbsp;Dashboard</a></li>
					    <li class="active">Purchase</li>
					</ul>
				</div>

				<?php
					if($this->session->flashdata('message') != "" ){
						echo $this->session->flashdata('message');
					}
				?>
				<div class="col-lg-12">
					<ul class="nav nav-tabs">
	   					<li class="active"><a data-toggle="tab" class="tab-letter" href="#per-po">Per Order</a></li>
					    <li><a data-toggle="tab" href="#per-material" class="tab-letter">Per Material</a></li>
	  				</ul><br><br><br>
	  				<div class="tab-content">
    					<div id="per-po" class="tab-pane fade in active">
							<div id="add-btn">
								<a href="<?=base_url('business-center/purchase-order')?>" class="btn btn-success btn-xs<?=$order_status?>">+ Place Order</a>
							</div>

							<table class="table table-hover" id="tbl-po">
								<thead>
									<tr>
										<th>Order No.</th>
										<th>Business Center</th>
										<th>Order Date</th>
										<th>Order Created</th>
										<th>Order by</th>
										<th>Remarks</th>
										<th>Action</th>
									</tr>
								</thead>
								<tbody>

									<?php foreach($po as $row):?>

									<tr>
										<td><?=$row->po_no?></td>
										<td><?=$row->bc_name?></td>
										<td><?=$row->po_date?></td>
										<td><?=$row->po_created?></td>
										<td><?=$row->user_lname . ', ' . $row->user_fname?></td>
										<td><?=$row->po_remarks?></td>
										<td><a href="<?=base_url('business-center/view-po/' .encode($row->po_id))?>"><span class="glyphicon glyphicon-search" id="view"></span></a>&nbsp;&nbsp;<a href="<?=base_url('business-center/receive-po/' .encode($row->po_id))?>"><span class="glyphicon glyphicon-ok" id="ok"></span></a></td>
									</tr>

									<?php endforeach;?>
								</tbody>
							</table>
						</div>

						<div id="per-material" class="tab-pane fade">
							<div id="add-btn">
								<a href="<?=base_url('business-center/purchase-order')?>" class="btn btn-success btn-xs">+ Place PO</a>&nbsp;&nbsp;<a href="<?=base_url('business-center/po-material-excel')?>" target="_blank"><span class="fa fa-file-pdf-o"></span> Download PDF</a>
							</div>

							<table class="table table-hover" id="tbl-per-material">
								<thead>
									<tr>
										<th>Code</th>
										<th>Description</th>
										<th>UOM</th>
										<th>QTY</th>
									</tr>
								</thead>
								<tbody>

									<?php foreach($po_material as $row_material):?>
									<tr>
										<td><?=$row_material->material_code?></td>
										<td><?=$row_material->material_desc?></td>
										<td><?=$row_material->um_name?></td>
										<td><?=$row_material->po_qty?></td>
									</tr>
									<?php endforeach;?>
								</tbody>
							</table>
						</div>
					</div>
				</div>

				<div id="modal-po-remarks" class="modal fade" role="dialog">
					<div class="modal-dialog modal-sm">
					    <div class="modal-content">
					    	<div class="modal-header">
					        	<button type="button" class="close" data-dismiss="modal">&times;</button>
					       		<strong>Update remarks</strong>
					      	</div>
					      	<div class="modal-body">
					      		<form method="POST" action="<?=base_url('business-center/update-remarks/')?>" id="update-remarks">
					      			<input type="hidden" name="id" id="id" value="">
						        	
						        	<div class="form-group">
						        		<label>Remarks:</label>
						        		<select class="form-control" name="remarks">
						        			<option value="">Select...</option>

						        			<?php foreach($remarks as $row_remarks):?>
						        			<option value="<?=encode($row_remarks->po_remarks_id)?>"><?=$row_remarks->po_remarks?></option>
						        			<?php endforeach;?>
						        		</select>

						        	</div>
						        	<div id="modal-btn" class="text-right">
						        		<button type="submit" class="btn btn-success btn-sm">Update</button>&nbsp;&nbsp;
						        	</div>
						        </form>
					      	</div>
					    </div>
					</div>
				</div>

				<div id="modal-disable" class="modal fade" role="dialog">
					<div class="modal-dialog modal-sm">
					    <div class="modal-content">
					    	<div class="modal-header">
					        	<button type="button" class="close" data-dismiss="modal">&times;</button>
					       		<strong>Order Unavailable</strong>
					      	</div>
					      	<div class="modal-body">
					      		<div class="text-center">
					      			<label>Sorry order unavailable. Order only available in 1st month until the 7th day of the month.</label>
					      		</div>
					      	</div>
					    </div>
					</div>
				</div>

				<div id="modal-po-ok" class="modal fade" role="dialog">
					<div class="modal-dialog modal-sm">
					    <div class="modal-content">
					    	<div class="modal-header">
					        	<button type="button" class="close" data-dismiss="modal">&times;</button>
					       		<strong>Received PO</strong>
					      	</div>
					      	<div class="modal-body">
					      		<form method="POST" action="<?=base_url('admin/received-po/')?>" id="received-po">
					      			<input type="hidden" name="id" id="id" value="">
						        	<div id="modal-msg" class="text-center">
						        		<label>Are you sure you want to received this PO?</label>
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

			<script type="text/javascript">
				

			</script>