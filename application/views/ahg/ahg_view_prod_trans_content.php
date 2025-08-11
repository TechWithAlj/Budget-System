			
			<div class="col-lg-12" id="content">
				<div id="breadcrumb-div">
					<ul class="breadcrumb">
					    <li><a href="<?=base_url('ahg')?>"><span class="glyphicon glyphicon-home"></span>&nbsp;Dashboard</a></li>
					    <li><a href="<?=base_url('ahg/prod-trans/'.$bc_id)?>">Production Transaction</a></li>
					    <li class="active">View Production Transaction</li>
					</ul>
				</div>

				<?php
					if($this->session->flashdata('message') != "" ){
						echo $this->session->flashdata('message');
					}
				?>

				<div class="table-responsive">
					<table class="table table-hover table-bordered table-stripe nowrap" id="tbl-new-broiler-transaction">
						<thead id="here">
							<tr>
								<th rowspan="2"></th>
								<th rowspan="2" width="30%">Production Group Name</th>
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
								if($row->prod_trans_dtl_status == 1){
									$prod_trans_output .= "<td class='text-center'><a href='".base_url('ahg/edit-prod-trans/' . encode($row->prod_trans_id).'/'.encode($row->config_prod_dtl_id).'/'.$bc_id.'/'.encode($year))."'><i class='fa fa-pencil'></i></a></td>";
								} else {
									$prod_trans_output .= '<td>' .$count .'</td>';
								}
								
								$prod_trans_output .= '<td>' .$row->material_desc .'</td>';
								$prod_trans_output .= '<td>' . $row->component_type .'</td>';
								for ($i=1; $i <=12 ; $i++) {
									$month = date('M', strtotime($year.'-'.$i.'-01'));
									$prod_trans_output .= "<td class='text-center'>".number_format(get_data('prod_trans_dtl_tbl a', array('a.config_prod_dtl_id' => $row->config_prod_dtl_id, 'a.prod_trans_id' => $row->prod_trans_id, 'MONTH(a.prod_trans_dtl_date)'	=>	$i, 'YEAR(a.prod_trans_dtl_date)' => $year), true, 'a.rate')->rate,3,'.',',')."</td>";
									$prod_trans_output .= "<td class='text-center'>".number_format(get_data('prod_trans_dtl_tbl a', array('a.config_prod_dtl_id' => $row->config_prod_dtl_id, 'a.prod_trans_id' => $row->prod_trans_id, 'MONTH(a.prod_trans_dtl_date)'	=>	$i, 'YEAR(a.prod_trans_dtl_date)' => $year), true, 'a.cost')->cost,2,'.',',')."</td>";
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