			
			<div class="col-lg-12" id="content">
				<div id="breadcrumb-div">
					<ul class="breadcrumb">
					    <li><a href="<?=base_url('business-center')?>"><span class="glyphicon glyphicon-home"></span>&nbsp;Dashboard</a></li>
					    <li class="active">Brand BC Info</li>
					</ul>
				</div>
				<?php
					if($this->session->flashdata('message') != "" ){
						echo $this->session->flashdata('message');
					}
				?>

				<div id="add-btn">
					<div class="row">
						<div class="col-lg-1">
							<a href="" class="btn btn-success btn-xs" data-toggle="modal" data-target="#modal-brand-bc">+ Add Brand BC</a>

							<div id="modal-brand-bc" class="modal fade modal-confirm" role="dialog">
								<div class="modal-dialog modal-sm">
								    <div class="modal-content">
								    	<div class="modal-header">
								        	<button type="button" class="close" data-dismiss="modal">&times;</button>
								       		<strong>Add Brand BC</strong>
								      	</div>
								      	<div class="modal-body">
								        	<form method="POST" action="<?=base_url('business-center/add-brand-bc')?>" enctype="multipart/form-data" id="add-employee">
								        		<input type="hidden" name="bc" value="<?=$id?>">
								        		<input type="hidden" name="year" value="<?=$year?>">

								        		<div class="form-group">
								        			<label>Brand</label>
								        			<select name="brand" class="form-control">
								        				<option value="">Select...</option>

									        			<?php foreach($brand as $row_brand):?>

									        				<option value="<?=encode($row_brand->brand_id)?>"><?=$row_brand->brand_name?></option>

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
					</div>
				</div>

				<div class="row">
					<div class="col-lg-3">
						<label class="data-info">Business Center: <?=$bc_name?></label>
					</div>

					<div class="col-lg-2">
						<label>Budget Year:</label>
						<div class="form-group">
							<div class="date">
		                        <div class="input-group input-append date" id="brand-bc-trans-year">
		                            <input type="text" name="month" id="brand-bc-year" class="form-control input-sm" placeholder="Pick year" value="<?=$year?>">
		                            <span class="input-group-addon add-on"><span class="glyphicon glyphicon-calendar"></span></span>
		                        </div>
		                    </div>
						</div>
					</div>

					<input type="hidden" name="bc_id" id="bc_id" value="<?=$id?>">

				</div>
				
				<table class="table table-hover" id="tbl-brand-bc-info">
					<thead>
						<tr>
							<th>Brand</th>
							<th>Action</th>
						</tr>
					</thead>
					<tbody>

						<?php
							foreach($brand_bc as $row):
						?>
						
						<tr>
							<td><?=$row->brand_name?></td>
							<td><a href="<?=base_url('business-center/brand-bc-material-info/' . encode($row->brand_bc_id))?>" class="btn btn-xs btn-success">Material</a></td>
						</tr>

						<?php endforeach;?>

					</tbody>
				</table>
			</div>