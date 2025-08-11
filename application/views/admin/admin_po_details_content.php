			
			<div class="col-lg-12" id="content">
				<div id="breadcrumb-div">
					<ul class="breadcrumb">
					    <li><a href="<?=base_url('admin')?>"><span class="glyphicon glyphicon-home"></span>&nbsp;Dashboard</a></li>
					    <li><a href="<?=base_url('admin/order')?>">Order</a></li>
					    <li class="active">View Order</li>
					</ul>
				</div>

				<?php
					if($this->session->flashdata('message') != "" ){
						echo $this->session->flashdata('message');
					}
				?>
				<div class="col-lg-12">

					<div class="po-info">
						<div class="col-lg-4">
							<label>Order No.: <?=$po_info->po_no?></label>
						</div>

						<div class="col-lg-4">
							<label>Business Center: <?=$po_info->bc_name?></label>
						</div>

						<div class="col-lg-4">
							<label>Order Date.: <?=date('F Y', strtotime($po_info->po_date))?></label>
						</div>

						<div class="col-lg-4">
							<label>Order Created.: <?=date('F d, Y', strtotime($po_info->po_created))?></label>
						</div>

						<div class="col-lg-4">
							<label>Created By: <?=$po_info->user_lname . ', ' . $po_info->user_fname?></label>
						</div>

						<div class="col-lg-4">
							<label>Remarks: <?=$po_info->po_remarks?></label>
						</div>
					</div>
					
					<table class="table table-hover" id="tbl-po">
						<thead>
							<tr>
								<th>Material Code</th>
								<th>Material Desc</th>
								<th>Price</th>
								<th>QTY</th>
								<th>Place by</th>
								<th>Date added</th>
								<th>Action</th>
							</tr>
						</thead>
						<tbody>

							<?php foreach($po_details as $row):?>

							<tr>
								<td><?=$row->material_code?></td>
								<td><?=$row->material_desc?></td>
								<td><?=$row->po_price?></td>
								<td><?=$row->qty?></td>
								<td><?=$row->user_fname . ', ' . $row->user_fname?></td>
								<td><?=$row->po_details_date?></td>
								<td><a href="#" data-id="<?=encode($row->po_details_id)?>" class="remove-material"><span class="glyphicon glyphicon-remove" id="remove"></span></a>&nbsp;&nbsp;<a href="#" data-id="<?=encode($row->po_details_id)?>" class="edit-material-info"><span class="glyphicon glyphicon-pencil" id="edit"></span></a></td>
							</tr>

							<?php endforeach;?>
						</tbody>
					</table>
				</div>


				<div id="modal-remove" class="modal fade" role="dialog">
					<div class="modal-dialog modal-sm">
					    <div class="modal-content">
					    	<div class="modal-header">
					        	<button type="button" class="close" data-dismiss="modal">&times;</button>
					       		<strong>Remove Material</strong>
					      	</div>
					      	<div class="modal-body">
					      		<form method="POST" action="<?=base_url('admin/remove-material/')?>" id="form-remove">
					      			<input type="hidden" name="id" id="id" value="">
						        	<div id="modal-msg" class="text-center">
						        		<label>Are you sure you want to remove this material?</label>

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

				<div id="modal-po-material" class="modal fade" role="dialog">
					<div class="modal-dialog modal-sm">
					    <div class="modal-content">
					    	<div class="modal-header">
					        	<button type="button" class="close" data-dismiss="modal">&times;</button>
					       		<strong>Update Material</strong>
					      	</div>
					      	<div class="modal-body">
					      		<form method="POST" action="<?=base_url('admin/update-po-material/')?>" id="update-po-material">
					      			<input type="hidden" name="id" id="id" value="">

					      			<div class="form-group">
					      				<label id="">Material Code:&nbsp;</label><span id="material-code"></span>
					      			</div>

					      			<div class="form-group">
					      				<label id="">Material Desc:&nbsp;</label><span id="material-desc"></span>
					      			</div>

						        	<div class="form-group">
						        		<label>QTY</label>
						        		<input type="text" class="form-control" name="qty" id="qty">
						        	</div>

						        	<div class="text-right">
						        		<button type="submit" class="btn btn-success btn-sm">Update</button>
						        	</div>
						        </form>
					      	</div>
					    </div>
					</div>
				</div>
			</div>

			<script type="text/javascript">
				

			</script>