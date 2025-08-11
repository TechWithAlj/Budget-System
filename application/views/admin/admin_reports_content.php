			
			<div class="col-lg-12" id="content">
				<div id="breadcrumb-div">
					<ul class="breadcrumb">
					    <li><a href="<?=base_url('admin')?>"><span class="glyphicon glyphicon-home"></span>&nbsp;Dashboard</a></li>
					    <li class="active">Report</li>
					</ul>
				</div>

				<?php
					if($this->session->flashdata('message') != "" ){
						echo $this->session->flashdata('message');
					}
				?>
				<div class="col-lg-12">
					<ul class="nav nav-tabs">
	   					<li class="active"><a data-toggle="tab" class="tab-letter" href="#per-po">Received Order</a></li>
					    <!-- <li><a data-toggle="tab" href="#per-material" class="tab-letter">Per Material</a></li>
					    <li><a data-toggle="tab" href="#per-branch" class="tab-letter">Per Branch</a></li> -->
	  				</ul><br><br><br>
	  				<div class="tab-content">
    					<div id="per-po" class="tab-pane fade in active">

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
										<td><a href="<?=base_url('admin/order-history/' .encode($row->po_id))?>"><span class="glyphicon glyphicon-search" id="view"></span></a></td>
									</tr>

									<?php endforeach;?>
								</tbody>
							</table>
						</div>

						<!-- <div id="per-material" class="tab-pane fade">
							<div id="add-btn">
								<a href="<?=base_url('admin/purchase-order')?>" class="btn btn-success btn-xs">+ Place PO</a>&nbsp;&nbsp;<a href="<?=base_url('admin/po-material-excel')?>" target="_blank"><span class="fa fa-file-pdf-o"></span> Download PDF</a>
							</div>

							<table class="table table-hover" id="tbl-per-material">
								<thead>
									<tr>
										<th>Code</th>
										<th>Description</th>
										<th>UM</th>
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

						<div id="per-branch" class="tab-pane fade">
							<div id="add-btn">
								<a href="<?=base_url('admin/purchase-order')?>" class="btn btn-success btn-xs">+ Place PO</a>&nbsp;&nbsp;<a href="<?=base_url('admin/po-material-bc-pdf')?>" target="_blank"><span class="fa fa-file-pdf-o"></span> Download PDF</a>
							</div>

							<table class="table table-hover" id="tbl-per-bc">
								<thead>
									<tr>
										<th>Branch</th>
										<th>Code</th>
										<th>Description</th>
										<th>UM</th>
										<th>QTY</th>
									</tr>
								</thead>
								<tbody>

									<?php foreach($po_branch as $row_branch):?>
									<tr>
										<td><?=$row_branch->bc_name?></td>
										<td><?=$row_branch->material_code?></td>
										<td><?=$row_branch->material_desc?></td>
										<td><?=$row_branch->um_name?></td>
										<td><?=$row_branch->po_qty?></td>
									</tr>
									<?php endforeach;?>
								</tbody>
							</table>
						</div> -->
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