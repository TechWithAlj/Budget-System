			<div class="col-lg-12" id="content">
				<div id="breadcrumb-div">
					<ul class="breadcrumb">
					    <li><a href="<?=base_url('admin')?>"><span class="glyphicon glyphicon-home"></span>&nbsp;Dashboard</a></li>
					    <li><a href="<?=base_url('admin/broiler-trans/'.encode($bc_id).'/'.$pick_year)?>">Broiler Transaction</a></li>
					    <li class="active">Add Broiler Transaction (<?=$bc->bc_name?>)</li>
					</ul>
				</div>

				<?php
					if($this->session->flashdata('message') != "" ){
						echo $this->session->flashdata('message');
					}
				?>
				<form method="post" action="<?=base_url('admin/add-broiler-trans')?>" id="">
					<input type="hidden" name="bc_id" id="bc_id" value="<?=encode($bc_id)?>">
					<input type="hidden" name="pick_year" id="pick_year" value="<?=$pick_year?>">
					<div class="row">
						<div class="col-lg-2">
							<label>Budget year : <?=$pick_year?></label><br>
							<label>Broiler Group</label>
							<select name="broiler_group" id="broiler_group" class="form-control">
								<option value="">Select...</option>

								<?php foreach($broiler_group as $row):?>

									<option value="<?=encode($row->broiler_group_id)?>"><?=$row->broiler_group_name?></option>

								<?php endforeach;?>
							</select>
						</div>
					</div>

					<div class="table-responsive">
						<table class="table table-hover table-bordered table-stripe nowrap" id="tbl-new-broiler-transaction">
							<thead>
								<tr>
									<th width="auto"></th>
									<th width="30%">Broiler Subgroup Name</th>
									<th class="text-center">Jan</th>
									<th class="text-center">Feb</th>
									<th class="text-center">Mar</th>
									<th class="text-center">Apr</th>
									<th class="text-center">May</th>
									<th class="text-center">Jun</th>
									<th class="text-center">Jul</th>
									<th class="text-center">Aug</th>
									<th class="text-center">Sept</th>
									<th class="text-center">Oct</th>
									<th class="text-center">Nov</th>
									<th class="text-center">Dec</th>
								</tr>
								
							</thead>
							<tbody>
																
							</tbody>
						</table>
					</div>
						
					<div class="text-right" id="expenditures-add-btn">
						<button type="submit" class="btn btn-success btn-sm">Save</button>
					</div>
				</form>

				<div id="modal-slider-broiler" class="modal fade" role="dialog">
					<div class="modal-dialog modal-sm">
					    <div class="modal-content">
					    	<div class="modal-header">
					        	<button type="button" class="close" data-dismiss="modal">&times;</button>
					       		<strong>Budgeting Slider Tool</strong>
					      	</div>
					      	<div class="modal-body">
				      			<input type="hidden" name="id" id="id">
				      			<div class="slider-div">
					      			<label>Qty:&nbsp;&nbsp;</label><input type="number" class="form-control input-sm" id="slider-qty-val"><br />
						        	<input type="range" min="1" max="5000" value="0" class="slider" id="slider-qty">
						        </div>
						        <div class="slider-div">
						        	<label>Month Start:&nbsp;&nbsp;<span id="slider-qty-start-val"></span></label>
						        	<input type="range" min="1" max="12" value="1" class="slider" id="slider-qty-start">
						        </div>

						        <div class="slider-div">
						        	<label>Month End:&nbsp;&nbsp;<span id="slider-qty-end-val"></span></label>
						        	<input type="range" min="1" max="12" value="12" class="slider" id="slider-qty-end">
						        </div>

						        

						        <div class="text-right">
						        	<a href="" class="btn btn-info btn-sm slider-broiler-btn">Apply</a>
						        </div>
					        	
					      	</div>
					    </div>
					</div>
				</div>
			</div>