			<div class="col-lg-12" id="content">
				<div id="breadcrumb-div">
					<ul class="breadcrumb">
					    <li><a href="<?=base_url('ahg')?>"><span class="glyphicon glyphicon-home"></span>&nbsp;Dashboard</a></li>
					    <li><a href="<?=base_url('ahg/broiler-trans/'.encode($bc_id))?>">Broiler Transaction</a></li>
					    <li class="active">Add Broiler Transaction</li>
					</ul>
				</div>

				<?php
					if($this->session->flashdata('message') != "" ){
						echo $this->session->flashdata('message');
					}
				?>
				<form method="post" action="<?=base_url('ahg/add-broiler-trans')?>" id="">
					<input type="hidden" name="bc_id" id="bc_id" value="<?=encode($bc_id)?>">
					<div class="row">
						<div class="col-lg-2">
							
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
			</div>