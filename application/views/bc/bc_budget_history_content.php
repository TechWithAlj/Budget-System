			
			<div class="col-lg-12" id="content">
				<div id="breadcrumb-div">
					<ul class="breadcrumb">
					    <li><a href="<?=base_url('business-center')?>"><span class="glyphicon glyphicon-home"></span>&nbsp;Dashboard</a></li>
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

					<div class="col-lg-4">
						<label id="data-info">Date: <?=$budget_date?></label>
					</div>
				</div>
				
				
				<table class="table table-hover" id="tbl-budget">
					<thead>
						<tr>
							<th>Material Code</th>
							<th>Material Desc</th>
							<th class="text-right">QTY</th>
							<th class="text-right">Weight</th>
							<th class="text-right">ASP</th>
							<th class="text-right">Sales</th>
							<th class="text-right">Equivalent Unit</th>
							<th>Action</th>
						</tr>
					</thead>
					<tbody>

						<?php
							foreach($material as $row):
								if($row->checker == "YES"){
									$weight_unit = $row->broiler * $row->budget_qty;
								}else{
									$weight_unit = $row->unit_conversation* $row->budget_qty;
								}

								if($row->sales_unit_id == $row->base_unit){
									$sales = $row->asp_qty * $row->budget_qty;
								}elseif($row->sales_unit_id == $row->second_unit){
									$sales = $row->asp_qty * $weight_unit;
								}
						?>
						
						<tr>
							<td><?=$row->material_code?></td>
							<td><?=$row->material_desc?></td>
							<td class="budget-td text-right"><?=number_format($row->budget_qty, 2)?></td>
							<td class="budget-td text-right"><?=number_format($weight_unit, 2)?></td>
							<td class="budget-td text-right"><?=number_format($row->asp_qty, 2)?></td>
							<td class="budget-td text-right"><?=number_format($sales, 2)?></td>
							<td class="budget-td text-right"><?=number_format($row->budget_qty * $row->equivalent_unit, 2)?></td>
							<td><a href="" class="remove-material-budget" data-id="<?=encode($row->budget_material_id)?>"><span class="glyphicon glyphicon-remove"></span></a>&nbsp;&nbsp;</td>
						</tr>

						<?php endforeach;?>

					</tbody>
				</table>

				<div id="modal-confirm" class="modal fade" role="dialog">
					<div class="modal-dialog modal-sm">
					    <div class="modal-content">
					    	<div class="modal-header">
					        	<button type="button" class="close" data-dismiss="modal">&times;</button>
					       		<strong>Confirmation message</strong>
					      	</div>
					      	<div class="modal-body">
					      		<form method="POST" action="<?=base_url('business-center/remove-brand-material')?>" enctype="multipart/form-data" id="remove-brand-material">
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