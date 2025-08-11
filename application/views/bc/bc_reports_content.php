			
			<div class="col-lg-12" id="content">
				<div id="breadcrumb-div">
					<ul class="breadcrumb">
					    <li><a href="<?=base_url('business-center')?>"><span class="glyphicon glyphicon-home"></span>&nbsp;Dashboard</a></li>
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
										<td><a href="<?=base_url('business-center/order-history/' .encode($row->po_id))?>"><span class="glyphicon glyphicon-search" id="view"></span></a></td>
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