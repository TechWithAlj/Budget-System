			
			<div class="col-lg-12" id="content">
				<div id="breadcrumb-div">
					<ul class="breadcrumb">
					    <li><a href="<?=base_url('admin')?>"><span class="glyphicon glyphicon-home"></span>&nbsp;Dashboard</a></li>
					    <li class="active">Materials</li>
					</ul>
				</div>

				<?php
					if($this->session->flashdata('message') != "" ){
						echo $this->session->flashdata('message');
					}
				?>

				<ul class="nav nav-tabs">
   					<li class="active"><a data-toggle="tab" class="tab-letter" href="#material-tab">Materials</a></li>
				    <li><a data-toggle="tab" href="#variable-tab" class="tab-letter">Variable Materials</a></li>
  				</ul>

  				<div class="tab-content">
    				<div id="material-tab" class="tab-pane fade in active">
    					<br>
						<table class="table table-hover" id="tbl-material">
							<thead>
								<tr>
									<th>Material Code</th>
									<th>Material Desc</th>
									<th>Material Group</th>
									<th>VAT</th>
									<th>Base Unit</th>
									<th>Valuation Unit</th>
									<th>Valuation Basis</th>
									<th>Sales Unit Basis</th>
								</tr>
							</thead>
							<tbody>

								<?php foreach($material as $row):?>

								<tr>
									<td><?=$row->material_code?></td>
									<td><?=$row->material_desc?></td>
									<td><?=$row->material_group_name?></td>
									<td><?=$row->vat_type_name?></td>
									<td><?=$row->unit_name?></td>
									<td><?=$row->valuation_unit?></td>
									<td><?=$row->valuation_basis?></td>
									<td><?=$row->sales_unit_equivalent?></td>
								</tr>

								<?php endforeach;?>
							</tbody>
						</table>

						<div id="modal-edit-material" class="modal fade modal-confirm" role="dialog">
							<div class="modal-dialog modal-sm">
							    <div class="modal-content">
							    	<div class="modal-header">
							        	<button type="button" class="close" data-dismiss="modal">&times;</button>
							       		<strong>Edit Material</strong>
							      	</div>
							      	<div class="modal-body">
							        	<form method="POST" action="<?=base_url('admin/update-material')?>" enctype="multipart/form-data" id="update-material">

							        		<input type="hidden" name="id" id="id">

							        		<div class="form-group">
							        			<label>Material code:</label>
							        			<input type="text" class="form-control input-sm" name="material_code" id="material-code">
							        		</div>

							        		<div class="form-group">
							        			<label>Material description:</label>
							        			<input type="text" class="form-control input-sm" name="description" id="description">
							        		</div>

							        		<div class="form-group">
							        			<label>Material Group:</label>
							        			<select class="form-control" name="group" id="group">
							        				<option value="">Select...</option>

							        				<?php foreach($group as $row_group):?>
							        				
							        				<option value="<?=encode($row_group->material_group_id)?>"><?=$row_group->material_group_name?></option>

							        				<?php endforeach;?>

							        			</select>
							        		</div>

							        		<div class="form-group">
							        			<label>VAT:</label>
							        			<select class="form-control" name="vat" id="vat">
							        				<option value="">Select...</option>

							        				<?php foreach($vat as $row_vat):
							        					
							        				?>
							        				<option value="<?=encode($row_vat->vat_type_id)?>"><?=$row_vat->vat_type_name?></option>
							        				<?php endforeach;?>
							        			</select>
							        		</div>

							        		<div class="form-group">
							        			<label>Base Unit:</label>
							        			<select class="form-control" name="base_unit" id="base-unit">
							        				<option value="">Select...</option>

							        				<?php foreach($unit as $row_unit):?>

							        				<option value="<?=encode($row_unit->unit_id)?>"><?=$row_unit->unit_name?></option>

							        				<?php endforeach;?>

							        			</select>
							        		</div>

							        		<div class="form-group">
							        			<label>Valuation Unit:</label>
							        			<select class="form-control" name="valuation_unit" id="valuation-unit">
							        				<option value="">Select...</option>

							        				<?php foreach($unit as $row_unit):?>

							        				<option value="<?=encode($row_unit->unit_id)?>"><?=$row_unit->unit_name?></option>

							        				<?php endforeach;?>

							        			</select>
							        		</div>

							        		<div class="form-group">
							        			<label>Valuation Basis:</label>
							        			<input type="text" class="form-control input-sm" name="valuation_basis" id="valuation-basis">
							        		</div>

							        		<div class="form-group">
							        			<label>Sales Unit Equivalent:</label>
							        			<input type="text" class="form-control input-sm" name="equivalent_unit" id="equivalent-unit">
							        		</div>

							        		<div class="btn-update">
							        			<button type="submit" class="btn btn-info btn-sm pull-right">Update</button><br>
							        		</div>
							        	</form>
							      	</div>
							    </div>
							</div>
						</div>
					</div>

					<div id="variable-tab" class="tab-pane fade">
    					<br>
						<table class="table table-hover" id="tbl-vmaterial">
							<thead>
								<tr>
									<th>Material Code</th>
									<th>Material Desc</th>
									<th>Material Group</th>
									<th>VAT</th>
									<th>Base Unit</th>
									<th>Weight Unit</th>
									<th>Sales Basis</th>
									<th>Equivalent Unit</th>
									<th>Action</th>
								</tr>
							</thead>
							<tbody>

								<!-- <?php foreach($vmaterial as $row):?>

								<tr>
									<td><?=$row->material_code?></td>
									<td><?=$row->material_desc?></td>
									<td><?=$row->material_group_name?></td>
									<td><?=$row->vat_type_name?></td>
									<td><?=$row->base_unit?></td>
									<td><?=$row->weight_unit?></td>
									<td><?=$row->sales_unit?></td>
									<td><?=$row->equivalent_unit?></td>
									<td><a href="#" class="glyphicon glyphicon-pencil edit-material" data-id="<?=encode($row->material_id)?>"></a></td>
								</tr>

								<?php endforeach;?> -->
							</tbody>
						</table>
					</div>
				</div>
			</div>