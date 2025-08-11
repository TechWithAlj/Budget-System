			
			<div class="col-lg-12" id="content">
				<div id="breadcrumb-div">
					<ul class="breadcrumb">
					    <li><a href="<?=base_url('admin')?>"><span class="glyphicon glyphicon-home"></span>&nbsp;Dashboard</a></li>
					     <li><a href="<?=base_url('admin/order')?>">Order</a></li>
					    <li class="active">Receive Po</li>
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

					<form method="POST" action="<?=base_url('admin/receive-material')?>">
						<input type="hidden" name="id" value="<?=$id?>">
						<table class="table table-hover" id="tbl-received-po">
							<thead>
								<tr>
									<th>Material Code</th>
									<th>Material Desc</th>
									<th>Price</th>
									<th>QTY</th>
									<th>Received QTY</th>
								</tr>
							</thead>
							<tbody>

								<?php foreach($po_details as $row):?>

								<tr>
									<td><?=$row->material_code?><input type="hidden" name="detail[]" value="<?=encode($row->po_details_id)?>"></td>
									<td><?=$row->material_desc?></td>
									<td><?=$row->po_price?></td>
									<td><?=$row->qty?></td>
									<td><div class="form-group"><input type="text" name="received_qty[]" class="form-control input-sm"></div></td>
								</tr>

								<?php endforeach;?>
							</tbody>
						</table>

						<div class="text-right received-btn">
							<button type="submit" class="btn btn-success btn-sm" id="btn-received">Receive</button>
						</div>
					</form>
				</div>
			</div>

			<script type="text/javascript">
				

			</script>