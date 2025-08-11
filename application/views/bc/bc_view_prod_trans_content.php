			
			<div class="col-lg-12" id="content">
				<div id="breadcrumb-div">
					<ul class="breadcrumb">
					    <li><a href="<?=base_url('business-center')?>"><span class="glyphicon glyphicon-home"></span>&nbsp;Dashboard</a></li>
					    <?php if(decode($process_type_id) == 5){ ?>
					    <li><a href="<?=base_url('business-center/sales-bom-trans/'.$bc_id)?>">Sales BOM Transaction</a></li>
					    <li class="active">View Sales BOM Transaction (<?=$bc->bc_name?>)</li>
					    <?php } else { ?>
					    <li><a href="<?=base_url('business-center/prod-trans/'.$bc_id)?>">Production Transaction</a></li>
					    <li class="active">View Production Transaction (<?=$bc->bc_name?>)</li>
					    <?php } ?>
					</ul>
				</div>

				<?php
					if($this->session->flashdata('message') != "" ){
						echo $this->session->flashdata('message');
					}
				?>
				<?php foreach($prod_trans as $row){
					if(decode($process_type_id) == 5){
						echo '
						<label>Budget Year : '.$year.'</label><br>
						<label>Group Name : '.$row->prod_group_code.' - '.$row->prod_group_name.'</label><br>
						<label>Brand Name : '.$row->brand_name.'</label><br>';
					} else {
						echo '
						<label>Budget Year : '.$year.'</label><br>
						<label>Group Name : '.$row->prod_group_code.' - '.$row->prod_group_name.'</label><br>';
					}
					
					break;
				}?>
				<div class="table-responsive">
					<input type="hidden" id="lock_status" value="<?=$pending_lock_status;?>">
					<table class="table table-hover table-bordered table-stripe nowrap" id="tbl-new-prod-transaction">
						<thead id="here">
							<tr>
								<th rowspan="2"></th>
								<th rowspan="2" width="30%">Item Name</th>
								<th rowspan="2" width="30%">Item Code</th>
								<th rowspan="2" width="30%">Val. Unit</th>
								<th rowspan="2" width="10%">Component</th>
								<?php for ($i=1; $i <= 12 ; $i++){ ?>
								<th class="text-center" colspan="2"><?=date('M', strtotime($year.'-'.$i.'-01'))?></th>
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
							$count = 1;
							foreach($prod_trans as $row){
								$prod_trans_output .= '<tr><input type="hidden" name="prod_trans_output_dtl_id[]" value="' . encode($row->prod_trans_dtl_id) . '">';
								if($row->prod_trans_dtl_status == 1 && $pending_lock_status){
									$prod_trans_output .= "<td class='text-center'><a href='".base_url('business-center/edit-prod-trans/' . encode($row->prod_trans_id).'/'.encode($row->config_prod_dtl_id).'/'.$bc_id.'/'.encode($year).'/'.$process_type_id)."'><i class='fa fa-pencil'></i></a></td>";
								} else {
									$prod_trans_output .= '<td>' .$count .'</td>';
								}
								
								$prod_trans_output .= '<td>' .$row->material_desc .'</td>';
								$prod_trans_output .= '<td>' .$row->material_code .'</td>';
								$prod_trans_output .= '<td>' .$row->unit_name .'</td>';
								$prod_trans_output .= '<td>' . $row->component_type .'</td>';
								for ($i=1; $i <=12 ; $i++) {
									$month = date('M', strtotime($year.'-'.$i.'-01'));
									$prod_trans_output .= "<td class='text-center'>".number_format(get_data('prod_trans_dtl_tbl a', array('a.config_prod_dtl_id' => $row->config_prod_dtl_id, 'a.prod_trans_id' => $row->prod_trans_id, 'MONTH(a.prod_trans_dtl_date)'	=>	$i, 'YEAR(a.prod_trans_dtl_date)' => $year), true, 'a.rate')->rate,3,'.',',')."</td>";
									if ($row->process_type_id == 2) { //CLASSIFICATION
										$prod_trans_output .= "<td class='text-center'>".number_format(get_data('prod_trans_dtl_tbl a', array('a.config_prod_dtl_id' => $row->config_prod_dtl_id, 'a.prod_trans_id' => $row->prod_trans_id, 'MONTH(a.prod_trans_dtl_date)'	=>	$i, 'YEAR(a.prod_trans_dtl_date)' => $year), true, 'a.ave_wgt')->ave_wgt,2,'.',',')."</td>";
									} else {

										$prod_trans_output .= "<td class='text-center'>".number_format(get_data('prod_trans_dtl_tbl a', array('a.config_prod_dtl_id' => $row->config_prod_dtl_id, 'a.prod_trans_id' => $row->prod_trans_id, 'MONTH(a.prod_trans_dtl_date)'	=>	$i, 'YEAR(a.prod_trans_dtl_date)' => $year), true, 'a.cost')->cost,2,'.',',')."</td>";
									}
								}
								
								$prod_trans_output .= '</tr>';
								$count++;
							}
							?>
							<?=$prod_trans_output?>
						</tbody>
					</table>
				</div>
				<br><br>
			</div>