			
			<div class="col-lg-12" id="content">
				<div id="breadcrumb-div">
					<ul class="breadcrumb">
					    <li><a href="<?=base_url('region')?>"><span class="glyphicon glyphicon-home"></span>&nbsp;Dashboard</a></li>
					    <li><a href="<?=base_url('region/opex')?>">OPEX</a></li>
					    <li><a href="<?=base_url('region/opex-info/' . $parent . '/' . $year)?>">Info</a></li>
					    <li class="active">View Store Expenses</li>
					</ul>
				</div>
				
				<?php
					if($this->session->flashdata('message') != "" ){
						echo $this->session->flashdata('message');
					}
				?>

				<div class="row">
					<div class="col-lg-3">
						<label class="data-info">GL Group: <?=$gl_group?></label>
					</div>

					<div class="col-lg-3">
						<label class="data-info">Budget Year: <?=$year?></label>
					</div>

					<div class="col-lg-3">
						<label class="data-info">Business Center: <?=$bc?></label>
					</div>

				</div>

				<div class="row">
					<div class="col-lg-12">
						<div class="table-responsive">
							<table class="table table-bordered" id="tbl-view-store-expense">
								<thead>
									<tr>
										
										<th>Outlet Code</th>
										<th>Outlet Name</th>
										<th class="text-center" width="">Action</th>
									</tr>
								</thead>
								<tbody>

									<?php

										foreach($store_expense as $row):

									?>

										<tr>
											<td><?=$row->ifs_code?></td>
											<td><?=$row->outlet_name?></td>
											<td class="text-right"><a href="<?=base_url('region/view-store-expense-item/' . encode($row->cost_center) . '/' . $year)?>" class="btn btn-success btn-xs">View</a><!-- &nbsp;&nbsp;<a data-id="<?=encode($row->cost_center)?>" data-trans="<?=encode($row->gl_trans_id)?>" class="btn btn-danger btn-xs cancel-store-expense-btn">Cancel</a> --></td>

										</tr>

									<?php endforeach;?>

								</tbody>
							</table>

							<div id="modal-cancel-store-expense" class="modal fade modal-confirm" role="dialog">
								<div class="modal-dialog modal-sm">
								    <div class="modal-content">
								    	<div class="modal-header">
								        	<button type="button" class="close" data-dismiss="modal">&times;</button>
								       		<strong>Cancel Store Expense</strong>
								      	</div>
								      	<form method="POST" action="<?=base_url('region/cancel-store-expense')?>" id="cancel-store-expense">
								      		<input type="hidden" id="id" name="id">
								      		<input type="hidden" id="trans_id" name="trans_id">
									      	<div class="modal-body">
								        		<div class="text-center">
								        			<strong>Are you sure to cancel Store Expense?</strong>
								        		</div><br />

								        		<div class="text-center">
								        			<button type=submit class="btn btn-sm btn-success">Yes</button>&nbsp;&nbsp;<a href="" class="btn btn-sm btn-danger" data-dismiss="modal">No</a>
								        		</div>
									      	</div>
								      	</form>
								    </div>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>