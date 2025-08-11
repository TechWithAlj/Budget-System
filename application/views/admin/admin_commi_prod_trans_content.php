            <div class="col-lg-12" id="content">
				<div id="breadcrumb-div">
					<ul class="breadcrumb">
					    <li><a href="<?=base_url('admin')?>"><span class="glyphicon glyphicon-home"></span>&nbsp;Dashboard</a></li>
					    <li><a href="<?=base_url('admin/commi-production-cost')?>">Commissary Prod Cost</a></li>
					    <li class="active">Commissary Production Transaction Info (<?=$commissary->commissary_name?>)</li>
					</ul>
				</div>

				<?php
					if($this->session->flashdata('message') != "" ){
						echo $this->session->flashdata('message');
					}
				?>
				<ul class="nav nav-tabs">
				    <li class="active"><a data-toggle="tab" href="#internal-prod-trans-tab" class="tab-letter">Transactions</a></li>
  				</ul>
  				<br>
				<div class="tab-content">
					<div id="internal-prod-trans-tab" class="tab-pane fade in active">
						
						<div class="row">
							<div class="col-lg-2">
								<label>Pick Year:</label>
								<div class="form-group">
									<div class="date">
				                        <div class="input-group input-append date" id="commi-prod-trans-year">
				                            <input type="text" name="month" id="prod-trans-date-pick-year" class="form-control input-sm" placeholder="Pick year" value="<?=$year?>">
				                            <span class="input-group-addon add-on"><span class="glyphicon glyphicon-calendar"></span></span>
				                        </div>
				                    </div>
								</div>
							</div>
							<input type="hidden" name="commissary_id" id="commissary_id" value="<?=$commissary_id?>">
						</div>
						
						<div id="modal-confirm" class="modal fade" role="dialog">
							<div class="modal-dialog modal-sm">
							    <div class="modal-content">
							    	<div class="modal-header">
							        	<button type="button" class="close" data-dismiss="modal">&times;</button>
							       		<strong>Confirmation message</strong>
							      	</div>
							      	<div class="modal-body">
							      		<form method="POST" action="<?=base_url('admin/cancel-commi-prod-trans')?>" enctype="multipart/form-data" id="remove-prod-trans">
							      			<input type="hidden" name="prod_trans_id" id="prod_trans_id">
							      			<input type="hidden" name="commissary_id" id="commissary_id">
							      			<input type="hidden" name="material_desc" id="material_desc">
							      			<input type="hidden" name="trans_status" id="trans_status">

								        	<div id="modal-msg" class="text-center">
								        		
								        	</div>
								        	<div id="modal-btn" class="text-center">
								        		<button type="submit" class="btn btn-info btn-sm">Yes</button>&nbsp;&nbsp;
								        		<button data-dismiss="modal" class="btn btn-danger btn-sm">No</button>
								        	</div>
								        </form>
							      	</div>
							    </div>
							</div>
						</div>

						<div id="modal-copy" class="modal fade" role="dialog">
							<div class="modal-dialog modal-sm">
							    <div class="modal-content">
							    	<div class="modal-header">
							        	<button type="button" class="close" data-dismiss="modal">&times;</button>
							       		<strong>Copy Transaction</strong>
							      	</div>
							      	<form method="POST" action="<?=base_url('admin/copy-commi-prod-trans')?>" enctype="multipart/form-data" id="remove-prod-trans">
								      	<div class="modal-body">
							      			
							      			<input type="hidden" name="commissary_id" value="<?=$commissary_id?>">
							      			<input type="hidden" name="module" value="<?=encode('prod_module')?>">
									        <label>Copy From Year:</label>
											<div class="form-group">
												<div class="date">
							                        <div class="input-group input-append date" id="prod-copy-year">
							                            <input type="text" name="copy-year-from" class="form-control input-sm" placeholder="Pick year" value="<?=$year?>">
							                            <span class="input-group-addon add-on"><span class="glyphicon glyphicon-calendar"></span></span>
							                        </div>
							                    </div>
											</div>
											<label>Copy To Year:</label>
											<div class="form-group">
												<div class="date">
							                        <div class="input-group input-append date" id="prod-dest-year">
							                            <input type="text" name="copy-year-to" class="form-control input-sm" placeholder="Pick year" value="<?=$year?>">
							                            <span class="input-group-addon add-on"><span class="glyphicon glyphicon-calendar"></span></span>
							                        </div>
							                    </div>
											</div>
											
								      	</div>
								      	<div class="modal-footer">
								      		<input type="submit" class="btn btn-success btn-xs" value="Save">
								      	</div>
								    </form>
							    </div>
							</div>

						</div>

						<div id="modal-batch-cost-sheet" class="modal fade" role="dialog">
							<div class="modal-dialog modal-lg">
							    <div class="modal-content">
							    	<div class="modal-header">
							        	<button type="button" class="close" data-dismiss="modal">&times;</button>
							       		<strong>Cost Sheet Batching</strong>
							      	</div>
							      	<form method="POST" class="comparative-form" action="<?=base_url('admin/commi-cost-sheet-batch-computation')?>" id="">
								      	<div class="modal-body">
							      			
							      			<input type="hidden" name="commissary_id" value="<?=$commissary_id?>">
							      			<input type="hidden" name="year" value="<?=$year?>">
							      			<input type="hidden" name="company_unit_id" value="<?=encode($commissary->company_unit_id)?>">
											<label>Type:</label>
											<select class="form-control" name="" id="commi-cost-sheet-stat">
												<option value="">Select...</option>
												<option value="<?=encode(1)?>">Processed</option>
												<option value="<?=encode(2)?>">Not Processed</option>
											</select>
							      			
											<label>Material:</label>
											<select class="form-control" name="prod_trans[]" id="prod_trans_list">
												<option value="">Select...</option>

											</select>
											
								      	</div>
								      	<div class="modal-footer">
								      		<input type="submit" class="btn btn-success btn-xs" value="Load">
								      	</div>
								    </form>
							    </div>
							</div>

						</div>

						<div id="add-btn">
							<input type="hidden" id="lock_status" value="<?=$pending_lock_status;?>">

							<a href="<?=base_url('admin/new-commi-prod-trans/' .$commissary_id.'/'.$year)?>" class="btn btn-success btn-xs" id="add_prod_trans_button">+ Add Production Transaction</a>

							<a href="#" class="btn btn-primary btn-xs" data-toggle="modal" data-target="#modal-copy" id="copy_prod_trans_button"> <span class="fa fa-copy"></span> Copy Transaction</a>

							<a href="#" class="btn btn-danger btn-xs" data-toggle="modal" data-target="#modal-batch-cost-sheet" id="batch_cost_sheet_trans_button"> Cost Sheet Batching</a>

							<a href="<?=base_url('admin/view-commi-cost-sheet-report/' .$commissary_id.'/'.$year)?>" class="btn btn-info btn-xs" id="view_cost_sheet_button"> View All Processed Cost Sheet</a>
						</div>

						<div class="table-responsive">
							<table class="table table-hover table-bordered table-stripe nowrap" id="tbl-prod-trans">
								<thead>
									<tr>
										<th width="1%"></th>
										<th width="auto">Process Type</th>
										<th width="auto">Production Group Name</th>
										<!-- <th width="auto">Brand Name</th> -->
										<th width="auto">Created By</th>
										<th width="auto">Date Created</th>
										<th width="auto">Cost Sheet Stat</th>
										<th width="20%" class="text-center">Action</th>
									</tr>
								</thead>
								<tbody>
								<?php foreach($prod_trans as $row):
									$cost_sheet_stat = $row->cost_sheet_stat == 1 ? time_stamp_display($row->modified_ts).' | PROCESSED' : 'NOT PROCESSED';
								?>

									<tr>
										<?php if($row->prod_trans_status != 5){ ?>
										<td class="text-center"><a href="" class="remove-prod-trans" data-id="<?=encode($row->prod_trans_id)?>" data-commissary_id="<?=$commissary_id?>" data-mat_desc="<?=encode($row->material_desc)?>"><i class="fa fa-remove"></i></a></td>
										<?php } ?>
										<td><?=$row->process_type_name?></td>
										<td><?=$row->material_code.' - '.$row->material_desc?></td>
									
										<td><?=$row->user_fname.' '.$row->user_lname?></td>
										<td><?=time_stamp_display($row->created_ts)?></td>
										<td><?=$cost_sheet_stat;?></td>
										<td class="text-center"><a href="<?=base_url('admin/view-commi-prod-trans/' . encode($row->prod_trans_id).'/'.$commissary_id.'/'.encode($row->process_type_id).'/'.encode($year).'')?>" class="btn btn-xs btn-success edit-broiler-group">View</a>&nbsp;&nbsp;<a href="<?=base_url('admin/view-commi-cost-sheet/' . encode($row->prod_trans_id).'/'.$commissary_id.'/'.encode(date('Y', strtotime($row->prod_trans_dtl_date))).'/'. encode($row->process_type_id).'/'.encode($row->material_id).'/'.encode($commissary->company_unit_id))?>" class="btn btn-xs btn-primary">Cost Sheet</a></td>

									</tr>

								<?php endforeach;?>
									
								</tbody>
							</table>
							<br>
						</div>
					</div>

				</div>
			</div>