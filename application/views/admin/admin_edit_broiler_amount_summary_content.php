			<div class="col-lg-12" id="content">
				<div id="breadcrumb-div">
					<ul class="breadcrumb">
					    <li><a href="<?=base_url('admin')?>"><span class="glyphicon glyphicon-home"></span>&nbsp;Dashboard</a></li>
					    <li><a href="<?=base_url('admin/broiler-trans/' . $bc_id.'/'.decode($year))?>">Broiler Transaction Info </a></li>
					    <li class="active">Edit Broiler Actual Data - (<?=$bc->bc_name?>)</li>
					</ul>
				</div>

				<?php
					if($this->session->flashdata('message') != "" ){
						echo $this->session->flashdata('message');
					}
				?>

				<form method="post" action="<?=base_url('admin/update-broiler-amount-summary')?>" id="">

					<input type="hidden" name="bc_id" value="<?=$bc_id?>">
					<input type="hidden" name="year" value="<?=$year?>">
					<div class="table-responsive">
						<table class="table table-hover table-bordered table-stripe nowrap" id="tbl-add-broiler-config">
							<thead>
								<tr>
									<th width="auto"></th>
									<th width="30%">Broiler Item</th>
									<?php for ($i=1; $i <= 12 ; $i++){ ?>
										<th class="text-center"><?=date('M', strtotime(decode($year).'-'.$i.'-01'))?></th>
									<?php } ?>
								</tr>
							</thead>
							<tbody>
								<?php
									foreach($broiler_amount_summary as $row):
								?>
								<tr>
									<!-- <td><a href="#"><i class="fa fa-remove"></i></a>&nbsp;&nbsp;&nbsp;<a href="" class="add-broiler-config-item"><i class="fa fa-plus"></i></a></td> -->
									<input type="hidden" name="broiler_line_item_id[]" value="<?=encode($row->broiler_line_item_id)?>">
									<td class="text-center"><a href="#" class="slider-broiler"><span class="fa fa-sliders"></span></td>
									<td><?=$row->broiler_line_item?></td>
									<?php for ($i=1; $i <= 12 ; $i++){
										$month = date('M', strtotime($year.'-'.$i.'-01'));
									?>
										<td><input type="text" name="trans_qty[<?=$i?>][]" class="form-control input-sm" value="<?=get_data('broiler_amount_summary_tbl a', array('a.broiler_line_item_id' => $row->broiler_line_item_id, 'a.bc_id' => $row->bc_id, 'MONTH(a.trans_date)'	=>	$i, ' YEAR(a.trans_date)' => decode($year)), true, 'a.trans_qty')->trans_qty?>" size="9"></td>
									<?php } ?>
								</tr>
								<?php endforeach;?>
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