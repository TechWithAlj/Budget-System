			<div class="col-lg-12" id="content">
				<div id="breadcrumb-div">
					<ul class="breadcrumb">
					    <li><a href="<?=base_url('production')?>"><span class="glyphicon glyphicon-home"></span>&nbsp;Dashboard</a></li>
					    <li><a href="<?=base_url('production/prod-trans/'.$bc_id)?>">Production Transaction</a></li>
					    <li class="active">Add Transaction</li>
					</ul>
				</div>

				<?php
					if($this->session->flashdata('message') != "" ){
						echo $this->session->flashdata('message');
					}
				?>
				<form method="post" action="<?=base_url('production/add-ext-prod-trans-dtl')?>" id="">
					<input type="hidden" name="bc_id" value="<?=$bc_id?>">
					<input type="hidden" name="year" value="<?=encode($year)?>">
					<div class="table-responsive">
						<table class="table table-hover table-bordered table-stripe nowrap" id="tbl-new-ext-prod">
							<thead id="here">
								<tr>
									<th rowspan="2" width="50%">Material Name</th>
									<?php for ($i=1; $i <= 12 ; $i++){ ?>
									<th class="text-center" colspan="2"><?=date('M', strtotime($year.'-'.$i.'-01'))?></th>
									<?php } ?>
								</tr>
								<tr>
									<?php for ($i=1; $i <= 12 ; $i++){ ?>
									<th class="text-center">Ave.Wgt</th>
									<th class="text-center">Cost</th>
									<?php } ?>
								</tr>
							</thead>
							<tbody>
							<?php
							foreach($ext_prod_trans as $row):
							?>
								<tr>
									<input type="hidden" name="ext_prod_trans_id[]" value="<?=encode($row->ext_prod_trans_id)?>">
									<td><?=$row->material_desc?></td>
									<?php for ($i=1; $i <= 12 ; $i++){
										$month = date('M', strtotime($year.'-'.$i.'-01'));
									?>
										<td>
											<input type="text" class="form-control input-sm" size="6" name="ave_wgt[<?=$month?>][]">
										</td>
										<td>
											<input type="text" class="form-control input-sm" size="6" name="cost[<?=$month?>][]">
										</td>
									<?php } ?>
								</tr>

							<?php endforeach; ?>
							</tbody>
						</table>
					</div>
						
					<div class="text-right" id="expenditures-add-btn">
						<button type="submit" class="btn btn-success btn-sm">Save</button>
					</div>
				</form>
			</div>