			
			<div class="col-lg-12" id="content">
				<div id="breadcrumb-div">
					<ul class="breadcrumb">
					    <li><a href="<?=base_url('admin')?>"><span class="glyphicon glyphicon-home"></span>&nbsp;Dashboard</a></li>
					    <li><a href="<?=base_url('admin/commi-mat-cost/' .$commissary_id)?>">Material Cost Config </a></li>
					    <li class="active">Edit Config (<?=$commissary->commissary_name?>)</li>
					</ul>
				</div>

				<?php
					if($this->session->flashdata('message') != "" ){
						echo $this->session->flashdata('message');
					}
				?>

				

				<form method="post" action="<?=base_url('admin/update-commi-mat-cost')?>" id="">
					<input type="hidden" name="commissary_id" value="<?=$commissary_id?>">
					<input type="hidden" name="commi_mat_cost_id" value="<?=$commi_mat_cost_id?>">
					<input type="hidden" name="year" value="<?=$year?>">
					<input type="hidden" id="lock_status" value="<?=$pending_lock_status;?>">
					<div class="table-responsive">
						<table class="table table-hover table-bordered table-stripe nowrap" id="tbl-new-broiler-transaction" width="100%">
							<thead id="here">
								<tr>
                                    <th rowspan="2"></th>
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
								$ext_prod_trans_output = '';
								$count = 1;

								foreach($commi_mat_cost as $row){
									
									$ext_prod_trans_output .= '<tr><input type="hidden" name="commi_mat_cost_id[]" value="' . encode($row->commi_mat_cost_id) . '">';
									$ext_prod_trans_output .= '<td class="text-center"><a href="#" class="slider-prod"><span class="fa fa-sliders"></span></td>';
									
									$ext_prod_trans_output .= '<td>' .$row->material_code.' - '.$row->material_desc .'</td>';
									for ($i=1; $i <=12 ; $i++) {
										
										$month = date('M', strtotime($year.'-'.$i.'-01'));
										$ext_prod_trans_output .= "<td class='text-center'><input type='text' name='cost[".$i."][]' size='6' class='form-control input-sm' value='".number_format(get_data('commi_mat_cost_dtl_tbl a', array('a.commi_mat_cost_id' => $row->commi_mat_cost_id, 'MONTH(a.commi_mat_cost_date)'	=>	$i, 'a.commi_mat_cost_dtl_status !=' => 5), true, 'a.commi_mat_cost')->commi_mat_cost,2,'.',',')."'/></td>";

									}
									$ext_prod_trans_output .= '</tr>';
									$count++;
								}
								?>

								<?=$ext_prod_trans_output?>
							</tbody>
						</table>

						<div class="text-right" id="expenditures-add-btn">
							<button type="submit" class="save btn btn-success btn-sm" id="edit_ext_prod_trans_button">Save</button>
						</div>
						<br>
					</div>
				</form>

                <div id="modal-slider-prod" class="modal fade" role="dialog">
                    <div class="modal-dialog modal-sm">
                        <div class="modal-content">
                            <div class="modal-header">
                                <button type="button" class="close" data-dismiss="modal">&times;</button>
                                <strong>Budgeting Slider Tool</strong>
                            </div>
                            <div class="modal-body">
                                <input type="hidden" name="id" id="id">
                                

                                <hr />
                                <div class="slider-div">
                                    <label>Cost:&nbsp;&nbsp;</label><input type="number" class="form-control input-sm" id="slider-cost-val"><br />
                                    <input type="range" min="1" max="5000" value="0" class="slider" id="slider-cost">
                                </div>

                                <div class="slider-div">
                                    <label>Month Start:&nbsp;&nbsp;<span id="slider-cost-start-val"></span></label>
                                    <input type="range" min="1" max="12" value="1" class="slider" id="slider-cost-start">
                                </div>

                                <div class="slider-div">
                                    <label>Month End:&nbsp;&nbsp;<span id="slider-cost-end-val"></span></label>
                                    <input type="range" min="1" max="12" value="12" class="slider" id="slider-cost-end">
                                </div>

                                <div class="text-right">
                                    <a href="" class="btn btn-info btn-sm slider-prod-btn">Apply</a>
                                </div>
                                
                            </div>
                        </div>
                    </div>
                </div>
			</div>