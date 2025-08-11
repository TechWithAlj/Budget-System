			<div class="col-lg-12" id="content">
				<div id="breadcrumb-div">
					<ul class="breadcrumb">
					    <li><a href="<?=base_url('admin')?>"><span class="glyphicon glyphicon-home"></span>&nbsp;Dashboard</a></li>
					    <li><a href="<?=base_url('admin/broiler-trans/'.$bc_id.'/'.$pick_year)?>">Broiler Transaction Info</a></li>
					    <li class="active">Edit Industry Data (<?=$bc->bc_name?>)</li>
					</ul>
				</div>

				<?php
					if($this->session->flashdata('message') != "" ){
						echo $this->session->flashdata('message');
					}
				?>

				<form method="post" action="<?=base_url('admin/update-industry-trans')?>" id="">

					<input type="hidden" name="bc_id" value="<?=$bc_id?>">
					<input type="hidden" name="pick_year" value="<?=$pick_year?>">
					<div class="table-responsive">
						<table class="table table-hover nowrap" id="tbl-new-industry-trans" width="100%">
							<thead>
								<tr>
									<th width="1%" rowspan="2"></th>
									<th width="auto" rowspan="2">Integrator</th>
									<th width="auto" class="text-center" colspan="3">CG CAPACITY </th>
								</tr>
								<tr>
									<th class="text-center">Beginning of Year</th>
									
									<th class="text-center">Current</th>
									
									<th class="text-center">End of Year</th>
									
								</tr>
							</thead>
							<tbody>
								<?php foreach($industry_trans as $row): ?>
								<tr>
									<td><a href="#" class="remove-asg"><i class="fa fa-remove"></i></a></td>
									<input type="hidden" name="industry_trans_id[]" value="<?=encode($row->industry_trans_id)?>">
									<td><?=$row->industry_name?></td>
									<td class="text-center">
										<input type="text" size="100" name="beginning[]" value="<?=get_data('industry_trans_dtl_tbl a', array('a.industry_trans_id' => $row->industry_trans_id, 'a.season_id' => 1), true, 'a.industry_capacity') == '' ? 0 : round(get_data('industry_trans_dtl_tbl a', array('a.industry_trans_id' => $row->industry_trans_id, 'a.season_id' => 1), true, 'a.industry_capacity')->industry_capacity, 0)?>">
									</td>
									<td class="text-center">
										<input type="text" size="100" name="current[]" value="<?=get_data('industry_trans_dtl_tbl a', array('a.industry_trans_id' => $row->industry_trans_id, 'a.season_id' => 2), true, 'a.industry_capacity') == '' ? 0 : round(get_data('industry_trans_dtl_tbl a', array('a.industry_trans_id' => $row->industry_trans_id, 'a.season_id' => 2), true, 'a.industry_capacity')->industry_capacity, 0)?>">
									</td>
									<td class="text-center">
										<input type="text" size="100" name="ending[]" value="<?=get_data('industry_trans_dtl_tbl a', array('a.industry_trans_id' => $row->industry_trans_id, 'a.season_id' => 3), true, 'a.industry_capacity') == '' ? 0 : round(get_data('industry_trans_dtl_tbl a', array('a.industry_trans_id' => $row->industry_trans_id, 'a.season_id' => 3), true, 'a.industry_capacity')->industry_capacity, 0)?>">
									</td>
								</tr>
								<?php endforeach; ?>
							</tbody>
						</table>
					</div>
					
					<?php if($pending_lock_status): ?>
					<div class="text-right" id="expenditures-add-btn">
						<button type="submit" class="btn btn-success btn-sm">Save</button>
					</div>
					<?php endif; ?>
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