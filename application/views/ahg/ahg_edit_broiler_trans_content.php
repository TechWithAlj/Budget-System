			<?php
			foreach($broiler_trans as $row):
				$year = date( 'Y', strtotime($row->broiler_trans_date));
				break;
			endforeach;
			?>
			<div class="col-lg-12" id="content">
				<div id="breadcrumb-div">
					<ul class="breadcrumb">
					    <li><a href="<?=base_url('ahg')?>"><span class="glyphicon glyphicon-home"></span>&nbsp;Dashboard</a></li>
					    <li><a href="<?=base_url('ahg/view-broiler-trans/' . $bc_id.'/'.$broiler_group_id.'/'.$broiler_group_name . '/'. encode($year))?>">Broiler Transaction Details </a></li>
					    <li class="active">Edit Broiler Transaction (<?=$broiler_subgroup_name?>)</li>
					</ul>
				</div>

				<?php
					if($this->session->flashdata('message') != "" ){
						echo $this->session->flashdata('message');
					}
				?>

				<form method="post" action="<?=base_url('ahg/update-broiler-trans')?>" id="">
					<input type="hidden" name="bc_id" value="<?=$bc_id?>">
					<input type="hidden" name="broiler_group_id" value="<?=$broiler_group_id?>">
					<input type="hidden" name="broiler_group_name" value="<?=$broiler_group_name?>">
					<div class="table-responsive">
						<table class="table table-hover table-bordered table-stripe nowrap" id="tbl-view-broiler-transaction">
							<thead>
								<tr>
									<th rowspan="2" width="30%">Broiler Subgroup Name</th>
									<th width="auto" class="text-center">Jan</th>
									<th width="auto" class="text-center">Feb</th>
									<th width="auto" class="text-center">Mar</th>
									<th width="auto" class="text-center">Apr</th>
									<th width="auto" class="text-center">May</th>
									<th width="auto" class="text-center">Jun</th>
									<th width="auto" class="text-center">Jul</th>
									<th width="auto" class="text-center">Aug</th>
									<th width="auto" class="text-center">Sept</th>
									<th width="auto" class="text-center">Oct</th>
									<th width="auto" class="text-center">Nov</th>
									<th width="auto" class="text-center">Dec</th>
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
								
								foreach($broiler_trans as $row):
									$year = date( 'Y', strtotime($row->broiler_trans_date));
								?>
								<tr>
									<input type="hidden" name="broiler_trans_id[]" value="<?=encode($row->broiler_trans_id)?>">
									<input type="hidden" name="broiler_trans_year" value="<?=encode(date( 'Y', strtotime($row->broiler_trans_date)))?>">
									<th><?=$row->broiler_subgroup_name?></th>
									<td><input type="text" name="broiler_budget_qty[jan][]" class="form-control input-sm" required="true" value="<?=get_data('broiler_trans_dtl_tbl a', array('a.broiler_trans_id' => $row->broiler_trans_id, 'MONTH(a.broiler_trans_date)'	=>	1, 'YEAR(a.broiler_trans_date)' => $year), true, 'a.broiler_budget_qty')->broiler_budget_qty;?>" size="6"></td>
									<td><input type="text" name="broiler_budget_qty[feb][]" class="form-control input-sm" required="true" value="<?=get_data('broiler_trans_dtl_tbl a', array('a.broiler_trans_id' => $row->broiler_trans_id, 'MONTH(a.broiler_trans_date)'	=>	2, 'YEAR(a.broiler_trans_date)' => $year), true, 'a.broiler_budget_qty')->broiler_budget_qty;?>" size="6"></td>
									<td><input type="text" name="broiler_budget_qty[mar][]" class="form-control input-sm" required="true" value="<?=get_data('broiler_trans_dtl_tbl a', array('a.broiler_trans_id' => $row->broiler_trans_id, 'MONTH(a.broiler_trans_date)'	=>	3, 'YEAR(a.broiler_trans_date)' => $year), true, 'a.broiler_budget_qty')->broiler_budget_qty;?>" size="6"></td>
									<td><input type="text" name="broiler_budget_qty[apr][]" class="form-control input-sm" required="true" value="<?=get_data('broiler_trans_dtl_tbl a', array('a.broiler_trans_id' => $row->broiler_trans_id, 'MONTH(a.broiler_trans_date)'	=>	4, 'YEAR(a.broiler_trans_date)' => $year), true, 'a.broiler_budget_qty')->broiler_budget_qty;?>" size="6"></td>
									<td><input type="text" name="broiler_budget_qty[may][]" class="form-control input-sm" required="true" value="<?=get_data('broiler_trans_dtl_tbl a', array('a.broiler_trans_id' => $row->broiler_trans_id, 'MONTH(a.broiler_trans_date)'	=>	5, 'YEAR(a.broiler_trans_date)' => $year), true, 'a.broiler_budget_qty')->broiler_budget_qty;?>" size="6"></td>
									<td><input type="text" name="broiler_budget_qty[jun][]" class="form-control input-sm" required="true" value="<?=get_data('broiler_trans_dtl_tbl a', array('a.broiler_trans_id' => $row->broiler_trans_id, 'MONTH(a.broiler_trans_date)'	=>	6, 'YEAR(a.broiler_trans_date)' => $year), true, 'a.broiler_budget_qty')->broiler_budget_qty;?>" size="6"></td>
									<td><input type="text" name="broiler_budget_qty[jul][]" class="form-control input-sm" required="true" value="<?=get_data('broiler_trans_dtl_tbl a', array('a.broiler_trans_id' => $row->broiler_trans_id, 'MONTH(a.broiler_trans_date)'	=>	7, 'YEAR(a.broiler_trans_date)' => $year), true, 'a.broiler_budget_qty')->broiler_budget_qty;?>" size="6"></td>
									<td><input type="text" name="broiler_budget_qty[aug][]" class="form-control input-sm" required="true" value="<?=get_data('broiler_trans_dtl_tbl a', array('a.broiler_trans_id' => $row->broiler_trans_id, 'MONTH(a.broiler_trans_date)'	=>	8, 'YEAR(a.broiler_trans_date)' => $year), true, 'a.broiler_budget_qty')->broiler_budget_qty;?>" size="6"></td>
									<td><input type="text" name="broiler_budget_qty[sep][]" class="form-control input-sm" required="true" value="<?=get_data('broiler_trans_dtl_tbl a', array('a.broiler_trans_id' => $row->broiler_trans_id, 'MONTH(a.broiler_trans_date)'	=>	9, 'YEAR(a.broiler_trans_date)' => $year), true, 'a.broiler_budget_qty')->broiler_budget_qty;?>" size="6"></td>
									<td><input type="text" name="broiler_budget_qty[oct][]" class="form-control input-sm" required="true" value="<?=get_data('broiler_trans_dtl_tbl a', array('a.broiler_trans_id' => $row->broiler_trans_id, 'MONTH(a.broiler_trans_date)'	=>	10, 'YEAR(a.broiler_trans_date)' => $year), true, 'a.broiler_budget_qty')->broiler_budget_qty;?>" size="6"></td>
									<td><input type="text" name="broiler_budget_qty[nov][]" class="form-control input-sm" required="true" value="<?=get_data('broiler_trans_dtl_tbl a', array('a.broiler_trans_id' => $row->broiler_trans_id, 'MONTH(a.broiler_trans_date)'	=>	11, 'YEAR(a.broiler_trans_date)' => $year), true, 'a.broiler_budget_qty')->broiler_budget_qty;?>" size="6"></td>
									<td><input type="text" name="broiler_budget_qty[dec][]" class="form-control input-sm" required="true" value="<?=get_data('broiler_trans_dtl_tbl a', array('a.broiler_trans_id' => $row->broiler_trans_id, 'MONTH(a.broiler_trans_date)'	=>	12, 'YEAR(a.broiler_trans_date)' => $year), true, 'a.broiler_budget_qty')->broiler_budget_qty;?>" size="6"></td>
									
									<!-- <td>&nbsp;&nbsp;<a href="<?=base_url('ahg/remove-broiler-config/' . encode($row->broiler_group_id))?>" class="btn btn-xs glyphicon glyphicon-remove remove-broiler-config" data-id="<?=encode($row->broiler_group_id)?>"></a></td> -->
								</tr>
								<?php endforeach; ?>							
							</tbody>
						</table>

						<div class="text-right" id="expenditures-add-btn">
							<button type="submit" class="btn btn-success btn-sm">Save</button>
						</div>
						<br>
					</div>
				</form>
			</div>