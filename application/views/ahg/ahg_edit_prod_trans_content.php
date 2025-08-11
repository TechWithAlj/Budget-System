			
			<div class="col-lg-12" id="content">
				<div id="breadcrumb-div">
					<ul class="breadcrumb">
					    <li><a href="<?=base_url('ahg')?>"><span class="glyphicon glyphicon-home"></span>&nbsp;Dashboard</a></li>
					    <li><a href="<?=base_url('ahg/view-prod-trans/' . $prod_trans_id.'/'.$bc_id)?>">Production Transaction Details </a></li>
					    <li class="active">Edit Production Transaction</li>
					</ul>
				</div>

				<?php
					if($this->session->flashdata('message') != "" ){
						echo $this->session->flashdata('message');
					}
				?>

				<form method="post" action="<?=base_url('ahg/update-prod-trans')?>" id="">
					<input type="hidden" name="bc_id" value="<?=$bc_id?>">
					<input type="hidden" name="prod_trans_id" value="<?=$prod_trans_id?>">
					<input type="hidden" name="year" value="<?=$year?>">
					<div class="table-responsive">
						<table class="table table-hover table-bordered table-stripe nowrap" id="tbl-new-broiler-transaction">
							<thead id="here">
								<tr>
									
									<th rowspan="2" width="30%">Production Group Name</th>
									<th rowspan="2" width="10%">Component</th>
									<?php for ($i=1; $i <= 12 ; $i++){ ?>
									<th class="text-center" colspan="2"><?=date('M', strtotime(decode($year).'-'.$i.'-01'))?></th>
									<?php } ?>
								</tr>
								<tr>
									
									<?php
									foreach($prod_trans as $row){
										for ($i=1; $i <= 12 ; $i++){
											if($row->process_type_id == 2){
									?>
												<th class="text-center">Rate</th>
												<th class="text-center">Ave. Wgt</th>
									<?php
											} else {
									?>
												<th class="text-center">Rate</th>
												<th class="text-center">Cost/Price</th>
									<?php
											}
										}
										break;
									}
									?>
								</tr>
							</thead>
							<tbody>

								<?php
								$prod_trans_output = '';
								$prod_trans_footer = '';
								$count = 1;
								$year = decode($year);
								$process_type_id = '';
								foreach($prod_trans as $row){
									$process_type_id = $row->process_type_id;
									$prod_trans_output .= '<tr><input type="hidden" name="config_prod_dtl_id[]" value="' . encode($row->config_prod_dtl_id) . '">';
									
									$prod_trans_output .= '<td>' .$row->material_desc .'</td>';
									$prod_trans_output .= '<td>' . $row->component_type .'</td>';
									for ($i=1; $i <=12 ; $i++) {
										
										$month = date('M', strtotime($year.'-'.$i.'-01'));
										$prod_trans_output .= "<td class='text-center'><input type='text' name='rate[".$i."][]' size='6' class='edit_rate form-control input-sm' value='".number_format(get_data('prod_trans_dtl_tbl a', array('a.config_prod_dtl_id' => $row->config_prod_dtl_id, 'a.prod_trans_id' => $row->prod_trans_id, 'MONTH(a.prod_trans_dtl_date)'	=>	$i, 'YEAR(a.prod_trans_dtl_date)' => $year), true, 'a.rate')->rate,3,'.',',')."'/>";
										if($row->process_type_id == 2){
											$prod_trans_output .= "<input type='hidden' name='initial_rate[".$i."][]' size='6' class='edit_rate form-control input-sm' value='".number_format(get_data('prod_trans_dtl_tbl a', array('a.prod_trans_id' => $row->prod_trans_id, 'a.config_prod_dtl_id !=' => $row->config_prod_dtl_id, 'MONTH(a.prod_trans_dtl_date)'	=>	$i, 'YEAR(a.prod_trans_dtl_date)' => $year), true, 'sum(a.rate) as initial_rate')->initial_rate,3,'.',',')."'/></td>";
										} else {
											$prod_trans_output .= "</td>";
										}
										$prod_trans_output .= "<td class='text-center'><input type='text' name='cost[".$i."][]' size='6' class='form-control input-sm' value='".number_format(get_data('prod_trans_dtl_tbl a', array('a.config_prod_dtl_id' => $row->config_prod_dtl_id, 'a.prod_trans_id' => $row->prod_trans_id, 'MONTH(a.prod_trans_dtl_date)'	=>	$i, 'YEAR(a.prod_trans_dtl_date)' => $year), true, 'a.cost')->cost,2,'.',',')."'/></td>";
									}
									$prod_trans_output .= '</tr>';
									$count++;
								}
								?>

								<?=$prod_trans_output?>
							</tbody>
							<?php if($process_type_id == 2){ ?>
							<tfoot>
								<?php 
								$prod_trans_footer .= '<tr class="span7">
					                          <th class="total"></th>
					                          <th class="total text-right"></th>';
				                for ($i=1; $i <=12 ; $i++) {
				                	$prod_trans_footer .= '<th class="total text-right"></th>';
				                	$prod_trans_footer .= '<th class="total"></th>';
				                }
				                $prod_trans_footer .= '</tr>';
				                ?>
								<?=$prod_trans_footer?>
							</tfoot>
							<?php } ?>
						</table>

						<div class="text-right" id="expenditures-add-btn">
							<button type="submit" class="save btn btn-success btn-sm">Save</button>
						</div>
						<br>
					</div>
				</form>
			</div>