			
			<div class="col-lg-12" id="content">
				<div id="breadcrumb-div">
					<ul class="breadcrumb">
					    <li><a href="<?=base_url('unit/dashboard')?>"><span class="glyphicon glyphicon-home"></span>&nbsp;Dashboard</a></li>
					    <li class="active">GL Group</li>
					</ul>
				</div>
				<?php
					if($this->session->flashdata('message') != "" ){
						echo $this->session->flashdata('message');
					}
				?>

				<div class="row">
					<div class="col-lg-3">
					</div>

					<div class="col-lg-9">
						<div class="text-right">
							<a href="<?=base_url('unit/download-gl/')?>" target="_blank"><span class="glyphicon glyphicon-download-alt"></span>&nbsp;&nbsp; Download GL</a>
						</div>
					</div>
				</div>

				<ul class="nav nav-tabs">
					<li class="active"><a data-toggle="tab" href="#gl-subgroup-tab" class="tab-letter">GL Subgroup</a></li>
   					<li><a data-toggle="tab" class="tab-letter" href="#gl-group-tab">GL Group</a></li>
  				</ul>
  				<div class="tab-content">

  					<div id="gl-subgroup-tab" class="tab-pane fade in active">
    					<br>
				
						<table class="table table-hover" id="tbl-gl-subgroup">
							<thead>
								<tr>
									<th>GL Subgroup</th>
									<th>GL Group</th>
									<th>GL Subgroup Code</th>
									<th>GL Classification</th>
									<th>Status</th>
								</tr>
							</thead>
							<tbody>

								<?php
									foreach($sub_group as $row_sub):

										if($row_sub->gl_sub_status == 1){
			                                $badge_gl_sub = '<span class="badge badge-info">Active</span>';
			                                $toggle_gl_sub = '<a href="" class="btn btn-danger btn-xs deactivate-gl-sub" data-id="' . encode($row_sub->gl_sub_id) . '">Deactivate</a>';
			                            }elseif($row_sub->gl_sub_status == 0){
			                                $badge_gl_sub = '<span class="badge badge-warning">Inactive</span>';
			                                $toggle_gl_sub = '<a href="#" class="btn btn-info btn-xs activate-gl-sub" data-id="' . encode($row_sub->gl_sub_id) . '">Activate</a>';
			                            }
								?>
								
								<tr>
									<td><?=$row_sub->gl_sub_name?></td>
									<td><?=$row_sub->gl_group_name?></td>
									<td><?=$row_sub->gl_code?></td>
									<td><?=$row_sub->gl_class_name?></td>
									<td class="text-center"><?=$badge_gl_sub?></td>
								</tr>

								<?php endforeach;?>

							</tbody>
						</table>
					</div>

    				<div id="gl-group-tab" class="tab-pane fade">
    					<br>
				
						<table class="table table-hover" id="tbl-gl-group">
							<thead>
								<tr>
									<th>GL Group</th>
									<th class="text-center">Status</th>
								</tr>
							</thead>
							<tbody>

								<?php
									foreach($gl_group as $row):
										if($row->gl_group_status == 1){
			                                $badge_gl_group = '<span class="badge badge-info">Active</span>';
			                                $toggle_gl_group = '<a href="" class="btn btn-danger btn-xs deactivate-gl-group" data-id="' . encode($row->gl_group_id) . '">Deactivate</a>';
			                            }elseif($row->gl_group_status == 0){
			                                $badge_gl_group = '<span class="badge badge-warning">Inactive</span>';
			                                $toggle_gl_group = '<a href="#" class="btn btn-info btn-xs activate-gl-group" data-id="' . encode($row->gl_group_id) . '">Activate</a>';
			                            }
								?>
								
								<tr>
									<td><?=$row->gl_group_name?></td>
									<td class="text-center"><?=$badge_gl_group?></td>
								</tr>

								<?php endforeach;?>

							</tbody>
						</table>
					</div>
				</div>
			</div>