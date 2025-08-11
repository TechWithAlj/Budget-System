			<div class="col-lg-12" id="content">
				<div id="breadcrumb-div">
					<ul class="breadcrumb">
					    <li><a href="<?=base_url('admin')?>"><span class="glyphicon glyphicon-home"></span>&nbsp;Dashboard</a></li>
					    <li><a href="<?=base_url('admin/production-cost')?>">Production Cost</a></li>
					    <li class="active">Production Transaction Info (<?=$bc->bc_name?>)</li>
					</ul>
				</div>

				<?php
					if($this->session->flashdata('message') != "" ){
						echo $this->session->flashdata('message');
					}
				?>
				<ul class="nav nav-tabs">
				    <li class="active"><a data-toggle="tab" href="#internal-prod-trans-tab" class="tab-letter">Internal</a></li>
   					<li><a data-toggle="tab" class="tab-letter" href="#external-prod-trans-tab">External</a></li>
  				</ul>
  				<br>
				<div class="tab-content">
					<div id="internal-prod-trans-tab" class="tab-pane fade in active">
						
						<div class="row">
							<div class="col-lg-2">
								<label>Pick Year:</label>
								<div class="form-group">
									<div class="date">
				                        <div class="input-group input-append date" id="prod-trans-year">
				                            <input type="text" name="month" id="prod-trans-date-pick-year" class="form-control input-sm" placeholder="Pick year" value="<?=$year?>">
				                            <span class="input-group-addon add-on"><span class="glyphicon glyphicon-calendar"></span></span>
				                        </div>
				                    </div>
								</div>
							</div>
							<input type="hidden" name="bc_id" id="bc_id" value="<?=$bc_id?>">
						</div>
						
						<div id="modal-confirm" class="modal fade" role="dialog">
							<div class="modal-dialog modal-sm">
							    <div class="modal-content">
							    	<div class="modal-header">
							        	<button type="button" class="close" data-dismiss="modal">&times;</button>
							       		<strong>Confirmation message</strong>
							      	</div>
							      	<div class="modal-body">
							      		<form method="POST" action="<?=base_url('admin/cancel-prod-trans')?>" enctype="multipart/form-data" id="remove-prod-trans">
							      			<input type="hidden" name="prod_trans_id" id="prod_trans_id">
							      			<input type="hidden" name="bc_id" id="bc_id">
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
							      	<form method="POST" action="<?=base_url('admin/copy-prod-trans')?>" enctype="multipart/form-data" id="remove-prod-trans">
								      	<div class="modal-body">
							      			
							      			<input type="hidden" name="bc_id" value="<?=$bc_id?>">
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
							      	<form method="POST" class="comparative-form" action="<?=base_url('admin/cost-sheet-batch-computation')?>" id="">
								      	<div class="modal-body">
							      			
							      			<input type="hidden" name="bc_id" value="<?=$bc_id?>">
							      			<input type="hidden" name="year" value="<?=$year?>">
											<label>Type:</label>
											<select class="form-control" name="" id="cost-sheet-stat">
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

							<a href="<?=base_url('admin/new-prod-trans/' .$bc_id.'/'.$year)?>" class="btn btn-success btn-xs" id="add_prod_trans_button">+ Add Production Transaction</a>

							<a href="#" class="btn btn-primary btn-xs" data-toggle="modal" data-target="#modal-copy" id="copy_prod_trans_button"> <span class="fa fa-copy"></span> Copy Transaction</a>

							<a href="#" class="btn btn-danger btn-xs" data-toggle="modal" data-target="#modal-batch-cost-sheet" id="batch_cost_sheet_trans_button"> Cost Sheet Batching</a>

							<a href="<?=base_url('admin/view-cost-sheet-report/' .$bc_id.'/'.$year)?>" class="btn btn-info btn-xs" id="view_cost_sheet_button"> View All Processed Cost Sheet</a>
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
										<td class="text-center"><a href="" class="remove-prod-trans" data-id="<?=encode($row->prod_trans_id)?>" data-bc_id="<?=$bc_id?>" data-mat_desc="<?=encode($row->material_desc)?>"><i class="fa fa-remove"></i></a></td>
										<?php } ?>
										<td><?=$row->process_type_name?></td>
										<td><?=$row->material_code.' - '.$row->material_desc?></td>
									
										<td><?=$row->user_fname.' '.$row->user_lname?></td>
										<td><?=time_stamp_display($row->created_ts)?></td>
										<td><?=$cost_sheet_stat;?></td>
										<td class="text-center"><a href="<?=base_url('admin/view-prod-trans/' . encode($row->prod_trans_id).'/'.$bc_id.'/'.encode($row->process_type_id).'/'.encode($year).'')?>" class="btn btn-xs btn-success edit-broiler-group">View</a>&nbsp;&nbsp;<a href="<?=base_url('admin/view-cost-sheet/' . encode($row->prod_trans_id).'/'.$bc_id.'/'.encode(date('Y', strtotime($row->prod_trans_dtl_date))).'/'. encode($row->process_type_id).'/'.encode($row->material_id))?>" class="btn btn-xs btn-primary">Cost Sheet</a></td>

									</tr>

								<?php endforeach;?>
									
								</tbody>
							</table>
							<br>
						</div>
					</div>

					<div id="external-prod-trans-tab" class="tab-pane fade in">

						<div id="add-btn">
							<a href="#" data-toggle="modal" data-target="#modal-production-group" class="btn btn-success btn-xs" data-toggle="modal" data-target="#modal-vmaterial" id="add_ext_material">+ Add Material</a>&nbsp;&nbsp;<a href="<?=base_url('admin/new-ext-prod-trans/' .$bc_id .'/'. $year)?>" class="btn btn-success btn-xs" id="add_ext_transaction">+ Add Transaction</a>
							&nbsp;<a href="<?=base_url('admin/view-ext-prod-trans/' .$bc_id .'/'. $year)?>" class="btn btn-primary btn-xs" id="view_ext_transaction">View All Transaction</a>&nbsp;

							<a href="#" data-toggle="modal" data-target="#modal-upload-ext-prod" class="btn btn-success btn-xs" id="upload_ext_material" data-toggle="modal"><span class="fa fa-upload"></span> Upload External Materials</a>
							
							<a href="#" data-toggle="modal" data-target="#modal-ext-batch-cancellation" class="btn btn-danger btn-xs" id="ext_batch_cancellation" data-toggle="modal"><span class="fa fa-times-circle"></span> Batch Cancellation</a>

						</div>

						<div id="modal-production-group" class="modal fade modal-confirm" role="dialog">
							<div class="modal-dialog modal-sm">
							    <div class="modal-content">
							    	<div class="modal-header">
							        	<button type="button" class="close" data-dismiss="modal">&times;</button>
							       		<strong>Add Material</strong>
							      	</div>
							      	<div class="modal-body">
							        	<form method="POST" action="<?=base_url('admin/add-ext-prod')?>" enctype="multipart/form-data" id="add-material">
							        		<input type="hidden" name="bc_id" id="bc_id" value="<?=$bc_id?>">
							        		<div class="form-group">
							        			<label>Materials:</label>
							        			<select class="form-control" name="material_id[]" id="article_id">
							        				<option value="">Select...</option>

							        				<?php foreach($material as $row_type):
							        				?>
							        				<option value="<?=encode($row_type->material_id)?>"><?=$row_type->material_code.' - '.$row_type->material_desc?></option>
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

						<div id="modal-confirm_2" class="modal fade" role="dialog">
							<div class="modal-dialog modal-sm">
							    <div class="modal-content">
							    	<div class="modal-header">
							        	<button type="button" class="close" data-dismiss="modal">&times;</button>
							       		<strong>Confirmation message</strong>
							      	</div>
							      	<div class="modal-body">
							      		<form method="POST" action="<?=base_url('admin/cancel-ext-prod-trans')?>" enctype="multipart/form-data" id="remove-ext-prod-trans">
							      			<input type="hidden" name="ext_prod_trans_id" id="ext_prod_trans_id">
							      			<input type="hidden" name="trans_status" id="trans_status">
							      			<input type="hidden" name="bc_id" id="bc_id">
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

						<div id="modal-upload-ext-prod" class="modal fade modal-confirm" role="dialog">
							<div class="modal-dialog modal-md">
							    <div class="modal-content">
							        <form method="POST" class="comparative-form" action="<?=base_url('admin/upload-ext-prod')?>" enctype="multipart/form-data">
								    	<div class="modal-header">
								        	<button type="button" class="close" data-dismiss="modal">&times;</button>
								       		<strong>Upload External Materials</strong>
								      	</div>
								      	<div class="modal-body">

			                                <div class="form-group">
			                                	No template? 
			                                	<a class="card-link" href="<?=base_url('admin/download-ext-prod-temp')?>"><span class="fa fa-download"></span> Download here</a>
			                                	<br>
			                                	<hr>
			                                	<label>Transaction Year:</label>
												<div class="form-group">
													<div class="date">
								                        <div class="input-group input-append date comp-trans-year col-md-4">
								                            <input type="text" name="ext-prod-year" class="form-control input-sm" placeholder="Pick year" value="">
								                            <span class="input-group-addon add-on"><span class="glyphicon glyphicon-calendar"></span></span>
								                        </div>
								                    </div>
							                	</div>
												<input type="hidden" name="bc_id" id="bc_id" value="<?=$bc_id?>">
			                                    <label>Select Excel File</label><br>
			                                    <input type="file" name="ext-prod-file" class="form-contol-md" required accept=".xlsx" />
			                                </div>
								      	</div>

								      	<div class="modal-footer">
								      		<div class="btn-update">
							        			<button type="submit" class="btn btn-info btn-sm pull-right">Upload</button><br>
							        		</div>
								      	</div>
							        </form>
							    </div>
							</div>
						</div>

						<div id="modal-ext-batch-cancellation" class="modal fade modal-confirm" role="dialog">
							<div class="modal-dialog modal-sm">
							    <div class="modal-content">
							    	<div class="modal-header">
							        	<button type="button" class="close" data-dismiss="modal">&times;</button>
							       		<strong>Batch Cancellation</strong>
							      	</div>
							      	<div class="modal-body">
							        	<form method="POST" action="<?=base_url('admin/batch-cancel-ext-prod-trans')?>" id="form-ext-cancellation">
							        		<div class="form-group">
												<label for="">Note : Once cancelled, it cannot be undo</label>
											</div>

											<input type="hidden" id="bc_id" name="bc_id">
											
							        		<div class="form-group">
							        			<label>External Materials:</label>
							        			<select class="form-control" name="ext_prod_trans_id[]" id="ext_prod_trans_id" required="true">
							        				<option value="">Select...</option>

							        			</select>
							        		</div>
							        		
							        		<div class="btn-update">
							        			<button type="submit" id="submit-cancel-ext-batch" class="btn btn-info btn-sm pull-right">Submit</button><br>
							        		</div>
							        	</form>
							      	</div>
							    </div>
							</div>
						</div>

						<div class="table-responsive">
							<table class="table table-hover table-bordered table-stripe nowrap" id="tbl-prod-trans-external">
								<thead>
									<tr>
										<th width="auto">Material Name</th>
										<th width="auto">Created By</th>
										<th width="auto">Timestamp</th>
										<th width="20%" class="text-center">Action</th>
									</tr>
								</thead>
								<tbody>
								<?php
								foreach($ext_prod_trans as $row):
								?>
									<tr>
										<td><?=$row->material_code.' - '.$row->material_desc?></td>
										<td><?=$row->user_fname.' '.$row->user_lname?></td>
										<td><?=time_stamp_display($row->modified_ts)?></td>
										<td class="text-center"><a href="<?=base_url('admin/edit-ext-prod-trans/' . encode($row->ext_prod_trans_id).'/'.$bc_id .'/'. $year)?>" class="fa fa-pencil"></a>&nbsp;&nbsp;<a href="#" data-id="<?=encode($row->ext_prod_trans_id)?>" data-bc_id="<?=$bc_id?>" class="remove-ext-prod-trans fa fa-remove"></a></td>
									</tr>
									
								<?php endforeach; ?>
								</tbody>
							</table>
							<br>
						</div>
					</div>

				</div>
			</div>