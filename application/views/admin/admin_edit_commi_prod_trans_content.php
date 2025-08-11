			
			<div class="col-lg-12" id="content">
				<div id="breadcrumb-div">
					<ul class="breadcrumb">
					    <li><a href="<?=base_url('admin')?>"><span class="glyphicon glyphicon-home"></span>&nbsp;Dashboard</a></li>
					    
                        <li><a href="<?=base_url('admin/view-commi-prod-trans/' . $prod_trans_id.'/'.$commissary_id.'/'.$process_type_id.'/'.$year)?>">Commissary Production Transaction Details </a></li>
					    <li class="active">Edit Commissary Production Transaction (<?=$commissary->commissary_name?>)</li>
					</ul>
				</div>

				<?php
					if($this->session->flashdata('message') != "" ){
						echo $this->session->flashdata('message');
					}
				?>
				<?php foreach($prod_trans as $row){
					
                    echo '
                    <label>Budget Year : '.decode($year).'</label><br>
                    <label>Group Name : '.$row->prod_group_code.' - '.$row->prod_group_name.'</label><br>';
					break;
				}?>
				<form method="post" action="<?=base_url('admin/update-commi-prod-trans')?>" id="">
					<input type="hidden" name="commissary_id" value="<?=$commissary_id?>">
					<input type="hidden" name="prod_trans_id" value="<?=$prod_trans_id?>">
					<input type="hidden" name="year" value="<?=$year?>">
					<input type="hidden" name="process_type_id" value="<?=$process_type_id?>">
					<div class="table-responsive">
						<table class="table table-hover table-bordered table-stripe nowrap" id="tbl-new-prod-transaction">
							<thead id="here">
								<tr>
									<?php if(decode($process_type_id) != 2): ?>
									<th rowspan="2" width="auto" class="text-center"></th>
									<?php endif; ?>
									<th rowspan="2" width="30%">Item Name</th>
									<th rowspan="2" width="30%">Item Code</th>
									<th rowspan="2" width="30%">Val. Unit</th>
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
												<th class="text-center">Cost</th>
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
								$year = decode($year);
								$process_type_id = '';
								foreach($prod_trans as $row){
									$process_type_id = $row->process_type_id;
									$prod_trans_output .= '<tr><input type="hidden" name="config_prod_dtl_id[]" value="' . encode($row->config_prod_dtl_id) . '"><input type="hidden" name="article_id[]" value="' . $row->article_id . '"><input type="hidden" name="component_type_id[]" value="' . $row->component_type_id . '">';
									if($row->process_type_id != 2){
										$prod_trans_output .= '<td class="text-center"><a href="#" class="slider-prod" data-count="<?=$count?>"><span class="fa fa-sliders"></span></td>';
									}
									$prod_trans_output .= '<td>' .$row->material_desc .'</td>';
									$prod_trans_output .= '<td>' .$row->material_code .'</td>';
									$prod_trans_output .= '<td>' .$row->unit_name .'</td>';
									$prod_trans_output .= '<td>' . $row->component_type .'</td>';

                                    $rate_array = get_bulk_yearly_data('prod_trans_dtl_tbl a', array('a.config_prod_dtl_id' => $row->config_prod_dtl_id, 'a.prod_trans_id' => $row->prod_trans_id, 'YEAR(a.prod_trans_dtl_date)' => $year), true, 'rate');
                                    $cost_array = get_bulk_yearly_data('prod_trans_dtl_tbl a', array('a.config_prod_dtl_id' => $row->config_prod_dtl_id, 'a.prod_trans_id' => $row->prod_trans_id, 'YEAR(a.prod_trans_dtl_date)' => $year), true, 'cost');
                                    
                                    

									for ($i=1; $i <=12 ; $i++) {
                                        $rate = @$rate_array[$i] ? number_format($rate_array[$i], 3, '.', ',') : '';
                                        $cost = @$cost_array[$i] ? number_format($cost_array[$i], 3, '.', ',') : '';
										
										$month = date('M', strtotime($year.'-'.$i.'-01'));
										$prod_trans_output .= "<td class='text-center'><input type='text' name='rate[".$i."][]' size='6' class='edit_rate form-control input-sm text-right' value='".$rate."'/>";
										
                                        $prod_trans_output .= "</td>";
                                        $prod_trans_output .= "<td class='text-center'><input type='text' name='cost[".$i."][]' size='6' class='form-control input-sm text-right' value='".$cost."' /></td>"; //MUST BE DYNAMIC BASED ON PROCESS TYPE
										
									}
									$prod_trans_output .= '</tr>';
									$count++;
								}
								?>
								<?=$prod_trans_output?>
							</tbody>
							<tfoot>
							<?php if($process_type_id == 2){ ?>
							
								<?php 
								$prod_trans_footer = '';
								$prod_trans_footer .= '<tr class="span7"><td class="total"></td><td class="total"></td><td class="total"></td><td class="total text-right"></td>';
				                for ($i=1; $i <=12 ; $i++) {
				                	$prod_trans_footer .= '<td class="total text-right"></td>';
				                	$prod_trans_footer .= '<td class="total"></td>';
				                }
				                $prod_trans_footer .= '</tr>';
				                
				                ?>
								
								<?=$prod_trans_footer?>
							<?php } ?>
							</tfoot>
							
						</table>

						<div class="text-right" id="expenditures-add-btn">
							<button type="submit" class="save btn btn-success btn-sm">Save</button>
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
				      			<div class="slider-div">
					      			<label>Rate:&nbsp;&nbsp;</label><input type="number" class="form-control input-sm" id="slider-qty-val"><br />
						        	<input type="range" min="1" max="1000" value="0" class="slider" id="slider-qty">
						        </div>
						        <div class="slider-div">
						        	<label>Month Start:&nbsp;&nbsp;<span id="slider-qty-start-val"></span></label>
						        	<input type="range" min="1" max="12" value="1" class="slider" id="slider-qty-start">
						        </div>

						        <div class="slider-div">
						        	<label>Month End:&nbsp;&nbsp;<span id="slider-qty-end-val"></span></label>
						        	<input type="range" min="1" max="12" value="12" class="slider" id="slider-qty-end">
						        </div>

						        <hr />
						        <div class="slider-div">
						        	<?php $label = $process_type_id == 2 ? 'Ave.Wgt' : 'Cost/Price'; ?>
					      			<label><?=$label?>: </label><input type="number" class="form-control input-sm" id="slider-cost-val"><br />
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