            <div class="col-lg-12" id="content">
				<div id="breadcrumb-div">
					<ul class="breadcrumb">
					    <li><a href="<?=base_url('admin')?>"><span class="glyphicon glyphicon-home"></span>&nbsp;Dashboard</a></li>
					    <li><a href="<?=base_url('admin/commi-production-cost#mat-cost-tab')?>">Material Cost Config</a></li>
					    <li class="active">Material Cost Config (<?=$commissary->commissary_name?>)</li>
					</ul>
				</div>

				<?php
					if($this->session->flashdata('message') != "" ){
						echo $this->session->flashdata('message');
					}
				?>
				<ul class="nav nav-tabs">
				    
   					<li class="active"><a data-toggle="tab" class="tab-letter" href="#commi-mat-cost-tab">Material Cost Config</a></li>
  				</ul>
  				<br>
				<div class="tab-content">

					<div id="commi-mat-cost-tab" class="tab-pane fade in active">

						<div id="add-btn">
                            <input type="hidden" name="month" id="prod-trans-date-pick-year" class="form-control input-sm" placeholder="Pick year" value="<?=$year?>">
                            <input type="hidden" id="lock_status" value="<?=$pending_lock_status;?>">
							<a href="#" data-toggle="modal" data-target="#modal-production-group" class="btn btn-success btn-xs" data-toggle="modal" data-target="#modal-vmaterial" id="add_commi_mat_cost">+ Add Material</a>&nbsp;&nbsp;<a href="<?=base_url('admin/new-commi-mat-cost/' .$commissary_id .'/'. $year)?>" class="btn btn-success btn-xs" id="add_commi_mat_cost_trans">+ Add Configuration</a>
							&nbsp;<a href="<?=base_url('admin/view-commi-mat-cost/' .$commissary_id .'/'. $year)?>" class="btn btn-primary btn-xs" id="view_ext_transaction">View All Configuration</a>&nbsp;

							<a href="#" data-toggle="modal" data-target="#modal-upload-ext-prod" class="btn btn-success btn-xs" id="upload_ext_material" data-toggle="modal"><span class="fa fa-upload"></span> Upload Material Cost</a>
							&nbsp;
							<a href="#" data-toggle="modal" data-target="#modal-ext-batch-cancellation" class="btn btn-danger btn-xs" id="commi_mat_cost_batch_cancellation" data-toggle="modal"><span class="fa fa-times-circle"></span> Batch Cancellation</a>

						</div>

						<div id="modal-production-group" class="modal fade modal-confirm" role="dialog">
							<div class="modal-dialog modal-sm">
							    <div class="modal-content">
							    	<div class="modal-header">
							        	<button type="button" class="close" data-dismiss="modal">&times;</button>
							       		<strong>Add Material</strong>
							      	</div>
							      	<div class="modal-body">
							        	<form method="POST" action="<?=base_url('admin/add-commi-mat-cost')?>" enctype="multipart/form-data" id="add-material">
							        		<input type="hidden" name="commissary_id" id="commissary_id" value="<?=$commissary_id?>">
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
							       		<strong>Confirmation messagessss</strong>
							      	</div>
							      	<div class="modal-body">
							      		<form method="POST" action="<?=base_url('admin/cancel-commi-mat-cost')?>" enctype="multipart/form-data" id="remove-ext-prod-trans">
							      			<input type="hidden" name="commi_mat_cost_id" id="commi_mat_cost_id">
							      			<input type="hidden" name="trans_status" id="trans_status">
							      			<input type="hidden" name="commissary_id" id="commissary_id">
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
							        <form method="POST" class="comparative-form" action="<?=base_url('admin/upload-commi-mat-cost')?>" enctype="multipart/form-data">
								    	<div class="modal-header">
								        	<button type="button" class="close" data-dismiss="modal">&times;</button>
								       		<strong>Upload Material Cost</strong>
								      	</div>
								      	<div class="modal-body">

			                                <div class="form-group">
			                                	No template? 
			                                	<a class="card-link" href="<?=base_url('admin/download-commi-mat-cost-temp')?>"><span class="fa fa-download"></span> Download here</a>
			                                	<br>
			                                	<hr>
			                                	<label>Transaction Year:</label>
												<div class="form-group">
													<div class="date">
								                        <div class="input-group input-append date comp-trans-year col-md-4">
								                            <input type="text" name="commi-mat-cost-year" class="form-control input-sm" placeholder="Pick year" value="">
								                            <span class="input-group-addon add-on"><span class="glyphicon glyphicon-calendar"></span></span>
								                        </div>
								                    </div>
							                	</div>
												<input type="hidden" name="commissary_id" id="commissary_id" value="<?=$commissary_id?>">
			                                    <label>Select Excel File</label><br>
			                                    <input type="file" name="commi-mat-cost-file" class="form-contol-md" required accept=".xlsx" />
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
							        	<form method="POST" action="<?=base_url('admin/batch-cancel-commi-mat-cost')?>" id="form-ext-cancellation">
							        		<div class="form-group">
												<label for="">Note : Once cancelled, it cannot be undone</label>
											</div>

											<input type="hidden" id="commissary_id" name="commissary_id">
											
							        		<div class="form-group">
							        			<label>Materials:</label>
							        			<select class="form-control" name="commi_mat_cost_id[]" id="commi_mat_cost_id" required="true">
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
								foreach($commi_mat_cost as $row):
								?>
									<tr>
										<td><?=$row->material_code.' - '.$row->material_desc?></td>
										<td><?=$row->user_fname.' '.$row->user_lname?></td>
										<td><?=time_stamp_display($row->commi_mat_cost_modified_ts)?></td>
										<td class="text-center"><a href="<?=base_url('admin/edit-commi-mat-cost/' . encode($row->commi_mat_cost_id).'/'.$commissary_id .'/'. $year)?>" class="fa fa-pencil"></a>&nbsp;&nbsp;<a href="#" data-id="<?=encode($row->commi_mat_cost_id)?>" data-commissary_id="<?=$commissary_id?>" class="remove-commi-mat-cost fa fa-remove"></a></td>
									</tr>
									
								<?php endforeach; ?>
								</tbody>
							</table>
							<br>
						</div>
					</div>

				</div>
			</div>