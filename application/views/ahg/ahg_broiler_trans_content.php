			<div class="col-lg-12" id="content">
				<div id="breadcrumb-div">
					<ul class="breadcrumb">
					    <li><a href="<?=base_url('ahg')?>"><span class="glyphicon glyphicon-home"></span>&nbsp;Dashboard</a></li>
					    <li><a href="<?=base_url('ahg/broiler-cost')?>">Broiler Cost</a></li>
					    <li class="active">Broiler Transaction Info</li>
					</ul>
				</div>

				<?php
					if($this->session->flashdata('message') != "" ){
						echo $this->session->flashdata('message');
					}
				?>
				<?php $year = date('Y') + 1; ?>
				<div class="row">
					<div class="col-lg-2">
						<label>Pick Year:</label>
						<div class="form-group">
							<div class="date">
		                        <div class="input-group input-append date" id="broiler-trans-year">
		                            <input type="text" name="month" id="broiler-trans-date-pick-year" class="form-control input-sm" placeholder="Pick year" value="<?=$year?>">
		                            <span class="input-group-addon add-on"><span class="glyphicon glyphicon-calendar"></span></span>
		                        </div>
		                    </div>
						</div>
					</div>
					<input type="hidden" name="bc_id" id="bc_id" value="<?=encode($bc_id)?>">
				</div>

				<div id="add-btn">
					<a href="<?=base_url('ahg/new-broiler-trans/' . encode($bc_id))?>" class="btn btn-success btn-xs">+ Add Broiler Transaction</a>&nbsp;&nbsp;<a href="<?=base_url('ahg/view-broiler-summary/' . encode($bc_id) .'/'. $trans_year)?>" class="btn btn-info btn-xs">View Broiler Cost Summary</a>
				</div>

				<div class="table-responsive">
					<table class="table table-hover table-bordered table-stripe nowrap" id="tbl-broiler-trans">
						<thead>
							<tr>
								<th width="auto">Broiler Group Name</th>
								<th width="auto">Business Center</th>
								<th width="auto">Created By</th>
								<th width="20%" class="text-center">Action</th>
							</tr>
						</thead>
						<tbody>
							<?php foreach($broiler_trans as $row): ?>
							<tr>
								<th><?=$row->broiler_group_name?></th>
								<th><?=$row->bc_name?></th>
								<td><?=$row->user_fname.' '.$row->user_lname?></td>

								<?php if($row->status_id == 1){ ?>
								<td class="text-center"><a href="<?=base_url('ahg/view-broiler-trans/' . encode($row->bc_id).'/'. encode($row->broiler_group_id).'/'.encode($row->broiler_group_name) . '/' . encode(date( 'Y', strtotime($row->broiler_trans_date))))?>" class="brn btn-xs glyphicon glyphicon-file edit-broiler-config" title="View"></a>&nbsp;&nbsp;<a href="<?=base_url('ahg/post-broiler-trans/' . encode($row->broiler_trans_id).'/'.encode($row->bc_id).'/'.encode($row->broiler_group_id).'/'.encode($row->broiler_group_name))?>" class="brn btn-xs btn-xs glyphicon glyphicon-lock edit-broiler-config" title="Post"></a></td>
								<?php  } else { ?>
								<td class="text-center"><a href="<?=base_url('ahg/view-broiler-trans/' . encode($row->bc_id).'/'. encode($row->broiler_group_id).'/'.encode($row->broiler_group_name) . '/' . encode(date( 'Y', strtotime($row->broiler_trans_date))))?>" class="btn btn-xs btn-success edit-broiler-config">Compute</a></td>
								<?php } ?>
								<!-- <td>&nbsp;&nbsp;<a href="<?=base_url('ahg/remove-broiler-config/' . encode($row->broiler_group_id))?>" class="btn btn-xs glyphicon glyphicon-remove remove-broiler-config" data-id="<?=encode($row->broiler_group_id)?>"></a></td> -->
							</tr>
							<?php endforeach; ?>
							
						</tbody>
					</table>
				</div>
			</div>