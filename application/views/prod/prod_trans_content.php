			<div class="col-lg-12" id="content">
				<div id="breadcrumb-div">
					<ul class="breadcrumb">
					    <li><a href="<?=base_url('production')?>"><span class="glyphicon glyphicon-home"></span>&nbsp;Dashboard</a></li>
					    <li><a href="<?=base_url('production/production-cost')?>">Production Cost</a></li>
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
							      		<form method="POST" action="<?=base_url('production/cancel-prod-trans')?>" enctype="multipart/form-data" id="remove-prod-trans">
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

						<div id="add-btn">
							<a href="<?=base_url('production/new-prod-trans/' .$bc_id)?>" class="btn btn-success btn-xs">+ Add Production Transaction</a>&nbsp;&nbsp;
						</div>

						<div class="table-responsive">
							<table class="table table-hover table-bordered table-stripe nowrap" id="tbl-prod-trans">
								<thead>
									<tr>
										<th width="1%"></th>
										<th width="auto">Process Type</th>
										<th width="auto">Production Group Name</th>
										<th width="auto">Created By</th>
										<th width="auto">Date Created</th>
										<th width="20%" class="text-center">Action</th>
									</tr>
								</thead>
								<tbody>
								<?php foreach($prod_trans as $row):?>

									<tr>
										<?php if($row->prod_trans_status != 5){ ?>
										<td class="text-center"><a href="" class="remove-prod-trans" data-id="<?=encode($row->prod_trans_id)?>" data-bc_id="<?=$bc_id?>" data-mat_desc="<?=encode($row->material_desc)?>"><i class="fa fa-remove"></i></td>
										<?php } ?>
										<td><?=$row->process_type_name?></td>
										<td><?=$row->material_desc?></td>
										<td><?=$row->user_fname.' '.$row->user_lname?></td>
										<td><?=date( 'm/d/Y', strtotime($row->created_ts))?></td>
										<td class="text-center"><a href="<?=base_url('production/view-prod-trans/' . encode($row->prod_trans_id).'/'.$bc_id.'/'.encode($row->process_type_id))?>" class="btn btn-xs btn-success edit-broiler-group">View</a>&nbsp;&nbsp;<a href="<?=base_url('production/view-cost-sheet/' . encode($row->prod_trans_id).'/'.$bc_id.'/'.encode(date('Y', strtotime($row->prod_trans_dtl_date))).'/'. encode($row->process_type_id))?>" class="btn btn-xs btn-primary">Cost Sheet</a></td>

									</tr>

								<?php endforeach;?>
									
								</tbody>
							</table>
							<br>
						</div>
					</div>

					<div id="external-prod-trans-tab" class="tab-pane fade in">

						<div id="add-btn">
							<a href="#" data-toggle="modal" data-target="#modal-production-group" class="btn btn-success btn-xs" data-toggle="modal" data-target="#modal-vmaterial">+ Add Material</a>&nbsp;&nbsp;<a href="<?=base_url('production/new-ext-prod-trans/' .$bc_id)?>" class="btn btn-success btn-xs">+ Add Transaction</a>
						</div>

						<div id="modal-production-group" class="modal fade modal-confirm" role="dialog">
							<div class="modal-dialog modal-sm">
							    <div class="modal-content">
							    	<div class="modal-header">
							        	<button type="button" class="close" data-dismiss="modal">&times;</button>
							       		<strong>Add Material</strong>
							      	</div>
							      	<div class="modal-body">
							        	<form method="POST" action="<?=base_url('production/add-ext-prod')?>" enctype="multipart/form-data" id="add-material">
							        		<input type="hidden" name="bc_id" id="bc_id" value="<?=$bc_id?>">
							        		<div class="form-group">
							        			<label>Materials:</label>
							        			<select class="form-control" name="material_id[]" id="article_id">
							        				<option value="">Select...</option>

							        				<?php foreach($material as $row_type):
							        				?>
							        				<option value="<?=encode($row_type->material_id)?>"><?=$row_type->material_desc?></option>
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
							      		<form method="POST" action="<?=base_url('production/cancel-ext-prod-trans')?>" enctype="multipart/form-data" id="remove-ext-prod-trans">
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

						<div class="table-responsive">
							<table class="table table-hover table-bordered table-stripe nowrap" id="tbl-prod-trans-external">
								<thead>
									<tr>
										<th width="auto">Material Name</th>
										<th width="auto">Created By</th>
										<th width="auto">Date Created</th>
										<th width="20%" class="text-center">Action</th>
									</tr>
								</thead>
								<tbody>
								<?php
								foreach($ext_prod_trans as $row):
								?>
									<tr>
										<td><?=$row->material_desc?></td>
										<td><?=$row->user_fname.' '.$row->user_lname?></td>
										<td><?=date( 'm/d/Y', strtotime($row->created_ts))?></td>
										<td class="text-center"><a href="<?=base_url('production/edit-ext-prod-trans/' . encode($row->ext_prod_trans_id).'/'.$bc_id)?>" class="fa fa-pencil"></a>&nbsp;&nbsp;<a href="#" data-id="<?=encode($row->ext_prod_trans_id)?>" data-bc_id="<?=$bc_id?>" class="remove-ext-prod-trans fa fa-remove"></a></td>
									</tr>
									
								<?php endforeach; ?>
								</tbody>
							</table>
							<br>
						</div>
					</div>

				</div>
			</div>