			<div class="col-lg-12" id="content">
				<div id="breadcrumb-div">
					<ul class="breadcrumb">
					    <li><a href="<?=base_url('admin')?>"><span class="glyphicon glyphicon-home"></span>&nbsp;Dashboard</a></li>
					    <li><a href="<?=base_url('admin/broiler-trans/'.$bc_id.'/'.$year)?>">Broiler Transaction Info</a></li>
					    <li class="active">Add Broiler Actual Data</li>
					</ul>
				</div>

				<?php
					if($this->session->flashdata('message') != "" ){
						echo $this->session->flashdata('message');
					}
				?>

				<form method="post" action="<?=base_url('admin/add-broiler-amount-summary')?>" id="">
					<div class="row">
						<div class="col-lg-2">
							<label>Pick Year:</label>
							<div class="form-group">
								<div class="date">
			                        <div class="input-group input-append date" id="broiler-amount-summary-year">
			                            <input type="text" name="year" id="broiler-amount-date-pick-year" class="form-control input-sm" placeholder="Pick year" value="<?=$year?>">
			                            <span class="input-group-addon add-on"><span class="glyphicon glyphicon-calendar"></span></span>
			                        </div>
			                    </div>
							</div>
						</div>
						<input type="hidden" name="bc_id" id="bc_id" value="<?=$bc_id?>">
					</div>

					<input type="hidden" name="bc_id" value="<?=$bc_id?>">
					<div class="table-responsive">
						<table class="table table-hover table-bordered table-stripe nowrap" id="tbl-add-broiler-config">
							<thead>
								<tr>
									<th width="auto"></th>
									<th width="30%">Broiler Item</th>
									<?php for ($i=1; $i <= 12 ; $i++){ ?>
										<th class="text-center"><?=date('M', strtotime($year.'-'.$i.'-01'))?></th>
									<?php } ?>
								</tr>
							</thead>
							<tbody>
								<?php
									foreach($broiler_line_item as $row):
								?>
								<tr>
									<!-- <td><a href="#"><i class="fa fa-remove"></i></a>&nbsp;&nbsp;&nbsp;<a href="" class="add-broiler-config-item"><i class="fa fa-plus"></i></a></td> -->
									<input type="hidden" name="broiler_line_item_id[]" value="<?=encode($row->broiler_line_item_id)?>">
									<td class="text-center"><a href="#" class="slider-broiler"><span class="fa fa-sliders"></span></td>
									<td><?=$row->broiler_line_item?></td>
									<?php for ($i=1; $i <= 12 ; $i++){
										$month = date('M', strtotime($year.'-'.$i.'-01'));
									?>
										<td><input type="text" name="trans_qty[<?=$month?>][]" class="form-control input-sm" size="10"></td>
									<?php } ?>
								</tr>
								<?php endforeach;?>
							</tbody>
						</table>
					</div>
						
					<div class="text-right" id="expenditures-add-btn">
						<button type="submit" class="btn btn-success btn-sm">Save</button>
					</div>
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