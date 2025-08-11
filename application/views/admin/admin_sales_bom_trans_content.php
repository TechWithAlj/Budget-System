			<div class="col-lg-12" id="content">
				<div id="breadcrumb-div">
					<ul class="breadcrumb">
					    <li><a href="<?=base_url('admin')?>"><span class="glyphicon glyphicon-home"></span>&nbsp;Dashboard</a></li>
					    <li><a href="<?=base_url('admin/sales-bom')?>">Sales BOM</a></li>
					    <li class="active">Sales BOM Transaction Info (<?=$bc->bc_name?>)</li>
					</ul>
				</div>

				<?php
					if($this->session->flashdata('message') != "" ){
						echo $this->session->flashdata('message');
					}
				?>
				
				<div class="row">
					<div class="col-lg-2">
						<label>Pick Year:</label>
						<div class="form-group">
							<div class="date">
		                        <div class="input-group input-append date" id="sales-bom-trans-year">
		                            <input type="text" name="month" id="prod-trans-date-pick-year" class="form-control input-sm" placeholder="Pick year" value="<?=$year?>">
		                            <span class="input-group-addon add-on"><span class="glyphicon glyphicon-calendar"></span></span>
		                        </div>
		                    </div>
						</div>
					</div>

					<div class="col-lg-10 text-right">
						<a href="<?=base_url('admin/download-sales-bom/' . $bc_id . '/' . $year)?>" target="_blank"><span class="glyphicon glyphicon-download-alt"></span>&nbsp;&nbsp;Download Sales BOM</a>
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
					      		<form method="POST" action="<?=base_url('admin/cancel-sales-bom-trans')?>" enctype="multipart/form-data" id="remove-prod-trans">
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
					      			<input type="hidden" name="module" value="<?=encode('sales_bom')?>">
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

				<div id="add-btn">
					<input type="hidden" id="lock_status" value="<?=$pending_lock_status;?>">

					<a href="<?=base_url('admin/new-sales-bom-trans/' .$bc_id. '/'. $year)?>" class="btn btn-success btn-xs" id="add_sales_bom_trans_button">+ Add Sales BOM Transaction</a>

					<a href="#" class="btn btn-primary btn-xs" data-toggle="modal" data-target="#modal-copy" id="copy_sales_bom_trans_button"><span class="fa fa-copy"></span> Copy Transaction</a>

					<a href="<?=base_url('admin/view-sales-bom-summary/' .$bc_id.'/'.$year)?>" class="btn btn-info btn-xs" id="view_sales_bom_summary_button"> View Sales BOM Summary</a>
					
					<a href="<?=base_url('admin/create-by-product-on-sales-bom/' .$bc_id.'/'.$year)?>" class="btn btn-primary btn-xs" id="create_by_product_on_sales_bom"> Sync By Products</a>

					<a href="<?=base_url('admin/resync-bom-summary/' .$bc_id.'/'.$year)?>" class="btn btn-warning btn-xs text-right" id="resync_bom_summary"><span class="fa fa-refresh"></span></a>
				</div>

				<div class="table-responsive">
					<table class="table table-hover table-bordered table-stripe nowrap" id="tbl-prod-trans">
						<thead>
							<tr>
								<th width="1%"></th>
								<th width="auto">Process Type</th>
								<th width="auto">Sales BOM Group Name</th>
								<th width="auto">Brand Name</th>
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
								<td><?=$row->material_code.' - '.$row->material_desc?></td>
								<td><?=$row->brand_name?></td>
								<td><?=$row->user_fname.' '.$row->user_lname?></td>
								<td><?=time_stamp_display($row->created_ts)?></td>
								<td class="text-center"><a href="<?=base_url('admin/view-prod-trans/' . encode($row->prod_trans_id).'/'.$bc_id.'/'.encode($row->process_type_id).'/'.encode($year))?>" class="btn btn-xs btn-success edit-broiler-group">View</a></td>
								
								<!-- &nbsp;&nbsp;<a href="<?=base_url('admin/view-cost-sheet/' . encode($row->prod_trans_id).'/'.$bc_id.'/'.encode(date('Y', strtotime($row->prod_trans_dtl_date))).'/'. encode($row->process_type_id).'/'.$row->material_id)?>" class="btn btn-xs btn-primary">Cost Sheet</a>&nbsp;&nbsp;<a href="<?=base_url('admin/view-broiler-group/' . encode($row->broiler_group_id))?>" class="btn btn-danger btn-xs glyphicon glyphicon-remove cancel-broiler-group" data-id="<?=encode($row->broiler_group_id)?>"></a> -->

							</tr>

						<?php endforeach;?>

						</tbody>
					</table>
					<br>
				</div>
			</div>