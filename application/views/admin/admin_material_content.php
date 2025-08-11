			
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

				<div id="add-btn">
					<a href="" class="btn btn-info btn-xs" data-toggle="modal" data-backdrop="static" data-target="#modal-upload-material">+ Upload Materials</a>

					<div id="modal-upload-material" class="modal fade" role="dialog">
						<div class="modal-dialog modal-sm">
						    <div class="modal-content">
						    	<div class="modal-header">
						        	<button type="button" class="close" data-dismiss="modal">&times;</button>
						       		<strong>Upload Materials</strong>
						      	</div>
						      	<div class="modal-body">
						        	<form method="POST" action="<?=base_url('admin/upload-materials')?>" enctype="multipart/form-data" id="upload-materials">

						        		<div class="form-group">
						        			<label>Choose file:</label>
						        			<input type="file" name="material_file">
						        		</div><br /><br />

						        		<div class="text-right">
						        			<a href="<?=base_url('assets/Materials/Budgeting - Material Upload Templates Revised.xlsx')?>" target="_blank"><span class="glyphicon glyphicon-download-alt"></span>&nbsp;&nbsp;Download Material Templates</a>
						        		</div><br /><br />

						        		<div class="btn-update">
						        			<button type="submit" class="btn btn-info btn-sm pull-right">Upload</button><br>
						        		</div>
						        	</form>
						      	</div>
						    </div>
						</div>
					</div>
				</div>

				<ul class="nav nav-tabs">
   					<li class="active"><a data-toggle="tab" class="tab-letter" href="#material-tab">Materials</a></li>
				    <li><a data-toggle="tab" href="#variable-tab" class="tab-letter">Variable Materials</a></li>
  				</ul>

  				<div class="tab-content">
    				<div id="material-tab" class="tab-pane fade in active">
    					<br>
						<div id="add-btn">
							<a href="" class="btn btn-success btn-xs" data-toggle="modal" data-target="#modal-material">+ Add Material</a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a href="<?=base_url('admin/download-material')?>" target="_blank"><span class="glyphicon glyphicon-download-alt"></span>&nbsp;&nbsp;Download</a>

							<div id="modal-material" class="modal fade modal-confirm" role="dialog">
								<div class="modal-dialog modal-sm">
								    <div class="modal-content">
								    	<div class="modal-header">
								        	<button type="button" class="close" data-dismiss="modal">&times;</button>
								       		<strong>Add Material</strong>
								      	</div>
								      	<div class="modal-body">
								        	<form method="POST" action="<?=base_url('admin/add-material')?>" enctype="multipart/form-data" id="add-material">
								        		<div class="form-group">
								        			<label>Material code:</label>
								        			<input type="text" class="form-control input-sm" name="material_code" id="material_code">
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
								        			<label>Allocation Type:</label>
								        			<select class="form-control" name="allocation[]" id="allocation" multiple="multiple">
								        				<option value="">Select...</option>

								        				<?php foreach($allocation as $row_allocation):?>
								        				
								        				<option value="<?=encode($row_allocation->allocation_type_id)?>"><?=$row_allocation->allocation_type_name?></option>

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
								        			<input type="text" class="form-control input-sm" name="valuation_basis" id="valuation_basis">
								        		</div>

								        		<div class="form-group">
								        			<label>Sales Unit Equivalent:</label>
								        			<input type="text" class="form-control input-sm" name="equivalent_unit" id="equivalent-unit">
								        		</div>
								        		
												<div class="form-group">
								        			<label>Weight per Pack (grams):</label>
								        			<input type="text" class="form-control input-sm" name="material_weight" id="material-weight">
								        		</div>
								        		
								        		<div class="btn-update">
								        			<button type="submit" class="btn btn-info btn-sm pull-right">Add</button><br>
								        		</div>
								        	</form>
								      	</div>
								    </div>
								</div>
							</div>
						</div>

						<table class="table table-hover" id="tbl-material">
							<thead>
								<tr>
									<th>Material Code</th>
									<th>Material Desc</th>
									<th>Material Group</th>
									<th>Allocation Type</th>
									<th>VAT</th>
									<th>Base Unit</th>
									<th>Valuation Unit</th>
									<th>Valuation Basis</th>
									<th>Sales Unit Basis</th>
									<th>Weight<br>(Grams)</th>
									<th>Status</th>
									<th>Action</th>
								</tr>
							</thead>
							<tbody>

								<?php
									foreach($material as $row):
										if($row->material_status == 1){
			                                $badge = '<span class="badge badge-info">Active</span>';
			                                $toggle = '<a href="" class="btn btn-danger btn-xs deactivate-material" data-id="' . encode($row->material_id) . '">Deactivate</a>';
			                            }elseif($row->material_status == 0){
			                                $badge = '<span class="badge badge-warning">Inactive</span>';
			                                $toggle = '<a href="#" class="btn btn-info btn-xs activate-material" data-id="' . encode($row->material_id) . '">Activate</a>';
			                            }

								?>

								<tr>
									<td><?=$row->material_code?></td>
									<td><?=$row->material_desc?></td>
									<td><?=$row->material_group_name?></td>
									<td><?=$row->allocation?></td>
									<td><?=$row->vat_type_name?></td>
									<td><?=$row->unit_name?></td>
									<td><?=$row->valuation_unit?></td>
									<td><?=$row->valuation_basis?></td>
									<td><?=$row->sales_unit_equivalent?></td>
									<td><?=$row->material_weight?></td>
									<td class="text-center"><?=$badge?></td>
									<td><a href="#" class="glyphicon glyphicon-pencil edit-material" data-id="<?=encode($row->material_id)?>"></a></td>
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
								        			<label>Allocation Type:</label>
								        			<select class="form-control" name="allocation[]" id="edit-allocation" multiple="multiple">
								        				<option value="">Select...</option>
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

											<div class="form-group">
												<label>Weight per Pack (grams):</label>
												<input type="text" class="form-control input-sm" name="material_weight" id="material-weight">
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
						<div id="add-btn">
							<a href="" class="btn btn-success btn-xs" data-toggle="modal" data-target="#modal-vmaterial">+ Add Variable Material</a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a href="<?=base_url('admin/download-vmaterial')?>" target="_blank"><span class="glyphicon glyphicon-download-alt"></span>&nbsp;&nbsp;Download</a>

							<div id="modal-vmaterial" class="modal fade modal-confirm" role="dialog">
								<div class="modal-dialog modal-sm">
								    <div class="modal-content">
								    	<div class="modal-header">
								        	<button type="button" class="close" data-dismiss="modal">&times;</button>
								       		<strong>Add Variable Material</strong>
								      	</div>
								      	<div class="modal-body">
								        	<form method="POST" action="<?=base_url('admin/add-vmaterial')?>" enctype="multipart/form-data" id="add-vmaterial">
								        		<div class="form-group">
								        			<label>Material code:</label>
								        			<input type="text" class="form-control input-sm" name="material_code" id="material_code">
								        		</div>

								        		<div class="form-group">
								        			<label>Material description:</label>
								        			<input type="text" class="form-control input-sm" name="description" id="description">
								        		</div>

								        		<div class="form-group">
								        			<label>Material Group:</label>
								        			<select class="form-control" name="type" id="type">
								        				<option value="">Select...</option>

								        				<?php foreach($vtype as $row_type):
								        					if($row_type->material_type_name == 'LIVE SALES' || $row_type->material_type_name == 'SUPERMARKET' || $row_type->material_type_name == 'TRADE DISTRIBUTOR'):
								        				?>

								        				<option value="<?=encode($row_type->material_type_id)?>"><?=$row_type->material_type_name?></option>
								        				<?php endif;?>
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
								        			<label>Weight Unit:</label>
								        			<select class="form-control" name="weight_unit" id="weight-unit">
								        				<option value="">Select...</option>

								        				<?php foreach($unit as $row_unit):?>

								        				<option value="<?=encode($row_unit->unit_id)?>"><?=$row_unit->unit_name?></option>

								        				<?php endforeach;?>

								        			</select>
								        		</div>

								        		<div class="form-group">
								        			<label>Sales Basis:</label>
								        			<select class="form-control" name="sales_basis" id="sales-basis">
								        				<option value="">Select...</option>

								        				<?php foreach($unit as $row_unit):?>

								        				<option value="<?=encode($row_unit->unit_id)?>"><?=$row_unit->unit_name?></option>

								        				<?php endforeach;?>
								        			</select>
								        		</div>

								        		<div class="form-group">
								        			<label>Equivalent Unit</label>
								        			<input type="text" class="form-control input-sm" name="equivalent_unit" id="equivalent-unit">
								        		</div>
								        		
								        		<div class="btn-update">
								        			<button type="submit" class="btn btn-info btn-sm pull-right">Add</button><br>
								        		</div>
								        	</form>
								      	</div>
								    </div>
								</div>
							</div>
						</div>

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
							        			<label>Price</label>
							        			<input type="text" class="form-control input-sm" name="price" id="price">
							        		</div>

							        		<div class="form-group">
							        			<label>Unit of measurement:</label>
							        			<select class="form-control" name="um" id="um">
							        				<option value="">Select...</option>
							        			</select>
							        		</div>

							        		<div class="form-group">
							        			<label>Category:</label>
							        			<select class="form-control" name="category" id="category">
							        				<option value="">Select...</option>
							        			</select>
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
				</div>
			</div>