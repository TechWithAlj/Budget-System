			
			<div class="col-lg-12" id="content">
				<div id="breadcrumb-div">
					<ul class="breadcrumb">
					    <li><a href="<?=base_url('admin')?>"><span class="glyphicon glyphicon-home"></span>&nbsp;Dashboard</a></li>
					    <li><a href="<?=base_url('admin/report')?>">Report</a></li>
					    <li class="active">Order History</li>
					</ul>
				</div>

				<?php
					if($this->session->flashdata('message') != "" ){
						echo $this->session->flashdata('message');
					}
				?>
				<div class="col-lg-12">
					<div class="row">
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
						</div>
					</div>

					<ul class="nav nav-tabs">
	   					<li class="active"><a data-toggle="tab" class="tab-letter" href="#per-po">Order</a></li>
					    <li><a data-toggle="tab" href="#per-remarks" class="tab-letter">Remarks</a></li>
	  				</ul>
	  				<div class="tab-content">
    					<div id="per-po" class="tab-pane fade in active">
							<table class="table table-hover" id="tbl-history-po">
								<thead>
									<tr>
										<th>Material Code</th>
										<th>Material Desc</th>
										<th>Price</th>
										<th>Order QTY</th>
										<th>Received QTY</th>
										<th>Total Amount</th>
									</tr>
								</thead>
								<tbody>

									<?php 
										$total = 0;
										foreach($po_details as $row):
											$total += $row->po_price * $row->total_received_qty;
									?>

									<tr>
										<td><?=$row->material_code?><input type="hidden" name="detail[]" value="<?=encode($row->po_details_id)?>"></td>
										<td><?=$row->material_desc?></td>
										<td><?=$row->po_price?></td>
										<td><?=$row->qty?></td>
										<td><?=$row->total_received_qty?></td>
										<td><?=(number_format($row->po_price * $row->total_received_qty, 2))?></td>
									</tr>

									<?php endforeach;?>
								</tbody>
							</table>

							<div class="text-right overall">
								<label>Overall Total: <?=number_format($total, 2)?></label>
							</div>
						</div>

						<div id="per-remarks" class="tab-pane fade">
							<table class="table table-hover" id="tbl-remarks-po">
								<thead>
									<tr>
										<th>Remarks</th>
										<th>Date</th>
									</tr>
								</thead>
								<tbody>

									<?php foreach($po_remarks as $row):?>

									<tr>
										<td><?=$row->po_remarks?></td>
										<td><?=$row->remarks_date?></td>
									</tr>

									<?php endforeach;?>
								</tbody>
							</table>
						</div>
					</div>
					
				</div>
				
			</div>

			<script type="text/javascript">
				

			</script>