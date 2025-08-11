			<div class="col-lg-12" id="content">
				<div id="breadcrumb-div">
					<ul class="breadcrumb">
					    <li><a href="<?=base_url('ahg')?>"><span class="glyphicon glyphicon-home"></span>&nbsp;Dashboard</a></li>
					    <li><a href="<?=base_url('ahg/production-cost')?>">Production Cost</a></li>
					    <li class="active">Production Transaction Info</li>
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
					      		<form method="POST" action="<?=base_url('ahg/cancel-prod-trans')?>" enctype="multipart/form-data" id="remove-prod-trans">
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
					<a href="<?=base_url('ahg/new-prod-trans/' .$bc_id)?>" class="btn btn-success btn-xs">+ Add Production Transaction</a>&nbsp;&nbsp;
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
								<td class="text-center"><a href="<?=base_url('ahg/view-prod-trans/' . encode($row->prod_trans_id).'/'.$bc_id)?>" class="btn btn-xs btn-success edit-broiler-group">View</a>&nbsp;&nbsp;<a href="<?=base_url('ahg/view-cost-sheet/' . encode($row->prod_trans_id).'/'.$bc_id.'/'.encode(date('Y', strtotime($row->prod_trans_dtl_date))))?>" class="btn btn-xs btn-primary">Cost Sheet</a></td>
								<!--&nbsp;&nbsp;<a href="<?=base_url('ahg/view-broiler-group/' . encode($row->broiler_group_id))?>" class="btn btn-danger btn-xs glyphicon glyphicon-remove cancel-broiler-group" data-id="<?=encode($row->broiler_group_id)?>"></a> -->

							</tr>

						<?php endforeach;?>
							
						</tbody>
					</table>
					<br>
				</div>
			</div>