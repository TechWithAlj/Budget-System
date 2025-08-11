			
			<div class="col-lg-12" id="content">
				<div id="breadcrumb-div">
					<ul class="breadcrumb">
					    <li><a href="<?=base_url('admin')?>"><span class="glyphicon glyphicon-home"></span>&nbsp;Dashboard</a></li>
					    <li><a href="<?=base_url('admin/prod-trans/' .$bc_id)?>">Production Transaction </a></li>
					    <li class="active">Edit Transaction (<?=$bc->bc_name?>)</li>
					</ul>
				</div>

				<?php
					if($this->session->flashdata('message') != "" ){
						echo $this->session->flashdata('message');
					}
				?>

				<div class="row"><br />
					<div class="col-lg-12">
    					<label class="checkbox-inline">
							<input type="checkbox" data-toggle="toggle" id="toggle-cost" value="COST" data-size="mini"> Cost Only
						</label>
					</div>
				</div>

				<form method="post" action="<?=base_url('admin/update-ext-prod-trans')?>" id="">
					<input type="hidden" name="bc_id" value="<?=$bc_id?>">
					<input type="hidden" name="ext_prod_trans_id" value="<?=$ext_prod_trans_id?>">
					<input type="hidden" name="year" value="<?=$year?>">
					<div class="table-responsive">
						<table class="table table-hover table-bordered table-stripe nowrap" id="tbl-new-ext-prod" width="100%">
							<thead id="here">
								<tr>
									
									<th rowspan="2" width="30%">Material Name</th>
									<?php for ($i=1; $i <= 12 ; $i++){ ?>
									<th class="text-center" colspan="2"><?=date('M', strtotime($year.'-'.$i.'-01'))?></th>
									<?php } ?>
								</tr>
								<tr>
									
									<?php
									for ($i=1; $i <= 12 ; $i++){
									?>
										<th class="text-center">Cost</th>
										<th class="text-center">Ave.Wgt</th>
									<?php
									}
									?>
								</tr>
							</thead>
							<tbody>

								<?php
								$ext_prod_trans_output = '';
								$count = 1;

								foreach($ext_prod_trans as $row){
									
									$ext_prod_trans_output .= '<tr><input type="hidden" name="ext_prod_trans_id[]" value="' . encode($row->ext_prod_trans_id) . '">';
									
									$ext_prod_trans_output .= '<td>' .$row->material_code.' - '.$row->material_desc .'</td>';
									for ($i=1; $i <=12 ; $i++) {
										
										$month = date('M', strtotime($year.'-'.$i.'-01'));
										$ext_prod_trans_output .= "<td class='text-center'><input type='text' name='cost[".$i."][]' size='6' class='form-control input-sm' value='".number_format(get_data('ext_prod_trans_dtl_tbl a', array('a.ext_prod_trans_id' => $row->ext_prod_trans_id, 'MONTH(a.trans_dtl_date)'	=>	$i, 'a.ext_prod_trans_dtl_status !=' => 5), true, 'a.cost')->cost,2,'.',',')."'/></td>";

										$ext_prod_trans_output .= "<td class='text-center'><input type='text' name='ave_wgt[".$i."][]' size='6' class='form-control input-sm' value='".number_format(get_data('ext_prod_trans_dtl_tbl a', array('a.ext_prod_trans_id' => $row->ext_prod_trans_id, 'MONTH(a.trans_dtl_date)'	=>	$i, 'a.ext_prod_trans_dtl_status !=' => 5), true, 'a.ave_wgt')->ave_wgt,3,'.',',')."'/>";
									}
									$ext_prod_trans_output .= '</tr>';
									$count++;
								}
								?>

								<?=$ext_prod_trans_output?>
							</tbody>
						</table>

						<div class="text-right" id="expenditures-add-btn">
							<button type="submit" class="save btn btn-success btn-sm">Save</button>
						</div>
						<br>
					</div>
				</form>
			</div>