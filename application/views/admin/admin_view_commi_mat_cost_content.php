			
			<div class="col-lg-12" id="content">
				<div id="breadcrumb-div">
					<ul class="breadcrumb">
					    <li><a href="<?=base_url('admin')?>"><span class="glyphicon glyphicon-home"></span>&nbsp;Dashboard</a></li>
					    <li><a href="<?=base_url('admin/commi-mat-cost/' .$commissary_id)?>">Material Cost Config</a></li>
					    <li class="active">View All Transaction (<?=$commissary->commissary_name?>)</li>
					</ul>
				</div>

				<?php
					if($this->session->flashdata('message') != "" ){
						echo $this->session->flashdata('message');
					}
				?>

				

				<form method="post" action="<?=base_url('admin/update-ext-prod-trans')?>" id="">
					<div class="table-responsive">
						<table class="table table-hover table-bordered table-stripe nowrap" id="tbl-new-ext-prod" width="100%">
							<thead id="here">
								<tr>
									
									<th rowspan="2" width="30%">Material Name</th>
									<?php for ($i=1; $i <= 12 ; $i++){ ?>
									<th class="text-center"><?=date('M', strtotime($year.'-'.$i.'-01'))?></th>
									<?php } ?>
								</tr>
								<tr>
									
									<?php
									for ($i=1; $i <= 12 ; $i++){
									?>
										<th class="text-center">Cost</th>
										
									<?php
									}
									?>
								</tr>
							</thead>
							<tbody>

								<?php
								$commi_mat_cost_output = '';
								$count = 1;
								if(!empty($commi_mat_cost)){
									foreach($commi_mat_cost as $row){
										
										$commi_mat_cost_output .= '<tr>';
										
										$commi_mat_cost_output .= '<td>' .$row->material_code.' - '.$row->material_desc .'</td>';
										for ($i=1; $i <=12 ; $i++) {

											$td_value_1 = get_data('commi_mat_cost_dtl_tbl a', array('a.commi_mat_cost_id' => $row->commi_mat_cost_id, 'MONTH(a.commi_mat_cost_date)'	=>	$i, 'a.commi_mat_cost_dtl_status !=' => 5), true, 'a.commi_mat_cost') ? number_format(get_data('commi_mat_cost_dtl_tbl a', array('a.commi_mat_cost_id' => $row->commi_mat_cost_id, 'MONTH(a.commi_mat_cost_date)'	=>	$i, 'a.commi_mat_cost_dtl_status !=' => 5), true, 'a.commi_mat_cost')->commi_mat_cost,2,'.',',') : '';
											

											$month = date('M', strtotime($year.'-'.$i.'-01'));
											$commi_mat_cost_output .= "<td class='text-center'>".$td_value_1."</td>";

											
										}
										$commi_mat_cost_output .= '</tr>';
										$count++;
									}
								}
								?>

								<?=$commi_mat_cost_output?>
							</tbody>
						</table>
						<br>
					</div>
				</form>
			</div>