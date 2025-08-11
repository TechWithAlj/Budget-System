			
			<div class="col-lg-12" id="content">
				<div id="breadcrumb-div">
					<ul class="breadcrumb">
					    <li><a href="<?=base_url('admin')?>"><span class="glyphicon glyphicon-home"></span>&nbsp;Dashboard</a></li>
					    <li class="active">Category</li>
					</ul>
				</div>
				<?php
					if($this->session->flashdata('message') != "" ){
						echo $this->session->flashdata('message');
					}
				?>
				<div id="add-btn">
					<a href="" class="btn btn-success btn-xs" data-toggle="modal" data-backdrop="static" data-target="#modal-outlet">+ Add Brand Material</a>

					<div id="modal-outlet" class="modal fade modal-confirm" role="dialog">
						<div class="modal-dialog modal-sm">
						    <div class="modal-content">
						    	<div class="modal-header">
						        	<button type="button" class="close" data-dismiss="modal">&times;</button>
						       		<strong>Add Brand Material</strong>
						      	</div>
						      	<div class="modal-body">
						        	<form method="POST" action="<?=base_url('admin/add-brand-material')?>" enctype="multipart/form-data" id="add-brand-material">
						        		<input type="hidden" name="id" id="id" value="<?=$id?>">

						        		<div class="form-group">
						        			<label>Material:</label>
						        			<select class="form-control" name="material[]" id="material">
						        				<option value="">Select...</option>

						        				<?php foreach($unset_material as $row):?>

						        				<option value="<?=encode($row->material_id)?>"><?=$row->material_desc?></option>

						        				<?php endforeach;?>
						        			</select>
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

				<div class="row">
					<div class="col-lg-12">
						<label id="data-info">Brand Name: <?=$brand_name?></label>
					</div>
				</div>
				
				<table class="table table-hover" id="tbl-category">
					<thead>
						<tr>
							<th>Material Code</th>
							<th>Material Desc</th>
							<th>Action</th>
						</tr>
					</thead>
					<tbody>

						<?php
							foreach($material as $row):
						?>
						
						<tr>
							<td><?=$row->material_code?></td>
							<td><?=$row->material_desc?></td>
							<td><a href="" class="remove-brand-material" data-id="<?=encode($row->brand_material_id)?>"><span class="glyphicon glyphicon-remove"></span></a>&nbsp;&nbsp;</td>
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
					      		<form method="POST" action="<?=base_url('admin/remove-brand-material')?>" enctype="multipart/form-data" id="remove-brand-material">
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

				<div id="modal-edit-category" class="modal fade" role="dialog">
					<div class="modal-dialog modal-sm">
					    <div class="modal-content">
					    	<div class="modal-header">
					        	<button type="button" class="close" data-dismiss="modal">&times;</button>
					       		<strong>Update Category</strong>
					      	</div>
					      	<div class="modal-body">
					        	<form method="POST" action="<?=base_url('admin/update-category')?>" enctype="multipart/form-data" id="update-category">
					        		<input type="hidden" id="id" name="id">
					        		<div class="form-group">
					        			<label>Category</label>
					        			<input type="text" class="form-control input-sm" name="category" id="category">
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