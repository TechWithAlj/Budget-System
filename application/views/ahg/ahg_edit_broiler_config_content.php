			<div class="col-lg-12" id="content">
				<div id="breadcrumb-div">
					<ul class="breadcrumb">
					    <li><a href="<?=base_url('ahg')?>"><span class="glyphicon glyphicon-home"></span>&nbsp;Dashboard</a></li>
					    <li><a href="<?=base_url('ahg/view-broiler-config/'.$bc_id)?>">Broiler Config</a></li>
					    <li class="active">Edit Broiler Config</li>
					</ul>
				</div>

				<?php
					if($this->session->flashdata('message') != "" ){
						echo $this->session->flashdata('message');
					}
				?>
				<form method="post" action="<?=base_url('ahg/update-broiler-config')?>" id="">
					<input type="hidden" name="bc_id" value="<?=$bc_id?>">
					<div class="table-responsive">
						<table class="table table-hover table-bordered table-stripe nowrap" id="tbl-add-broiler-config">
							<thead>
								<tr>
									<th rowspan="2" width="30%">Config Name</th>
									<th style="width: 10px;" class="text-center">Jan</th>
									<th style="width: 10px;" class="text-center">Feb</th>
									<th style="width: 10px;" class="text-center">Mar</th>
									<th style="width: 10px;" class="text-center">Apr</th>
									<th style="width: 10px;" class="text-center">May</th>
									<th style="width: 10px;" class="text-center">Jun</th>
									<th style="width: 10px;" class="text-center">Jul</th>
									<th style="width: 10px;" class="text-center">Aug</th>
									<th style="width: 10px;" class="text-center">Sept</th>
									<th style="width: 10px;" class="text-center">Oct</th>
									<th style="width: 10px;" class="text-center">Nov</th>
									<th style="width: 10px;" class="text-center">Dec</th>
								</tr>
								<tr>
									<th class="text-center">Qty</th>
									<th class="text-center">Qty</th>
									<th class="text-center">Qty</th>
									<th class="text-center">Qty</th>
									<th class="text-center">Qty</th>
									<th class="text-center">Qty</th>
									<th class="text-center">Qty</th>
									<th class="text-center">Qty</th>
									<th class="text-center">Qty</th>
									<th class="text-center">Qty</th>
									<th class="text-center">Qty</th>
									<th class="text-center">Qty</th>
								</tr>
							</thead>
							<tbody>
								<?php
									foreach($config_subgroup as $row):
								?>
								<tr>
									<!-- <td><a href="#"><i class="fa fa-remove"></i></a>&nbsp;&nbsp;&nbsp;<a href="" class="add-broiler-config-item"><i class="fa fa-plus"></i></a></td> -->
									<input type="hidden" name="broiler_subgroup_name[]" value="<?=encode($row->broiler_subgroup_name)?>">
									<td><?=$row->broiler_subgroup_name?></td>
									<td><input type="text" name="config_qty[jan][]" class="form-control input-sm" required="true" value="<?=$row->jan?>" size="6"></td>
									<td><input type="text" name="config_qty[feb][]" class="form-control input-sm" required="true" value="<?=$row->feb?>" size="6"></td>
									<td><input type="text" name="config_qty[mar][]" class="form-control input-sm" required="true" value="<?=$row->mar?>" size="6"></td>
									<td><input type="text" name="config_qty[apr][]" class="form-control input-sm" required="true" value="<?=$row->apr?>" size="6"></td>
									<td><input type="text" name="config_qty[may][]" class="form-control input-sm" required="true" value="<?=$row->may?>" size="6"></td>
									<td><input type="text" name="config_qty[jun][]" class="form-control input-sm" required="true" value="<?=$row->jun?>" size="6"></td>
									<td><input type="text" name="config_qty[jul][]" class="form-control input-sm" required="true" value="<?=$row->jul?>" size="6"></td>
									<td><input type="text" name="config_qty[aug][]" class="form-control input-sm" required="true" value="<?=$row->aug?>" size="6"></td>
									<td><input type="text" name="config_qty[sep][]" class="form-control input-sm" required="true" value="<?=$row->sep?>" size="6"></td>
									<td><input type="text" name="config_qty[oct][]" class="form-control input-sm" required="true" value="<?=$row->oct?>" size="6"></td>
									<td><input type="text" name="config_qty[nov][]" class="form-control input-sm" required="true" value="<?=$row->nov?>" size="6"></td>
									<td><input type="text" name="config_qty[dec][]" class="form-control input-sm" required="true" value="<?=$row->decem?>" size="6"></td>
								</tr>
								<?php endforeach;?>
							</tbody>
						</table>
					</div>
						
					<div class="text-right" id="expenditures-add-btn">
						<button type="submit" class="btn btn-success btn-sm">Save</button>
					</div>
				</form>
			</div>