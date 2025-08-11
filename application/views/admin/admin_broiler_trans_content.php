
			<div class="col-lg-12" id="content">
				<div id="breadcrumb-div">
					<ul class="breadcrumb">
					    <li><a href="<?=base_url('admin')?>"><span class="glyphicon glyphicon-home"></span>&nbsp;Dashboard</a></li>
					    <li><a href="<?=base_url('admin/broiler-cost')?>">Broiler Cost</a></li>
					    <li class="active">Broiler Transaction Info (<?=$bc->bc_name?>)</li>
					</ul>
				</div>

				<?php
					if($this->session->flashdata('message') != "" ){
						echo $this->session->flashdata('message');
					}
				?>
				
					

				<ul class="nav nav-tabs">
   					<li class="active"><a data-toggle="tab" class="tab-letter" href="#broiler-trans-tab">Broiler Transaction</a></li>
   					<li><a data-toggle="tab" class="tab-letter" href="#other-trans-tab">Actual Data</a></li>
   					<li><a data-toggle="tab" class="tab-letter" href="#industry-trans-tab">Industry Update</a></li>
   					<li><a data-toggle="tab" class="tab-letter" href="#broiler-dashboard-tab">Dashboard</a></li>
  				</ul>
  				<div class="tab-content">
  					<br>
  					<div class="row">
						<div class="col-lg-2">
							<label>Pick Year:</label>
							<div class="form-group">
								<div class="date">
			                        <div class="input-group input-append date" id="broiler-budget-date-year">
			                            <input type="text" name="month" id="broiler-budget-year" class="form-control input-sm" placeholder="Pick year" value="<?=$year?>">
			                            <span class="input-group-addon add-on"><span class="glyphicon glyphicon-calendar"></span></span>
			                        </div>
			                    </div>
							</div>
						</div>
					</div>
					<div id="broiler-trans-tab" class="tab-pane fade in active">
						<!-- <br>
						<div class="row">
							<div class="col-lg-2">
								<label>Pick Year:</label>
								<div class="form-group">
									<div class="date">
				                        <div class="input-group input-append date" id="broiler-trans-year">
				                            <input type="text" name="month" id="broiler-trans-date-pick-year" class="form-control input-sm" placeholder="Pick year" value="<?=$year?>">
				                            <span class="input-group-addon add-on"><span class="glyphicon glyphicon-calendar"></span></span>
				                        </div>
				                    </div>
								</div>
							</div>
							
						</div> -->

						<div id="add-btn">
							
							<input type="hidden" id="lock_status" value="<?=$pending_lock_status;?>">

							<a href="<?=base_url('admin/new-broiler-trans/' . encode($bc_id).'/'.$year)?>" class="btn btn-success btn-xs" id="add_broiler_trans_button">+ ADD BROILER TRANSACTION </a>
							
							<a href="<?=base_url('admin/view-broiler-summary/' . encode($bc_id) .'/'. $trans_year)?>" id="view_broiler_summary" class="btn btn-info btn-xs">VIEW BROILER COST SUMMARY</a>
							
						</div>

						<div class="table-responsive">
							<table class="table table-hover table-bordered table-stripe nowrap" id="tbl-broiler-trans">
								<thead>
									<tr>
										<th width="auto">Broiler Group Name</th>
										<th width="auto">Business Center</th>
										<th width="auto">Created By</th>
										<th width="20%" class="text-center">Action</th>
									</tr>
								</thead>
								<tbody>
									<?php foreach($broiler_trans as $row): ?>
									<tr>
										<th><?=$row->broiler_group_name?></th>
										<th><?=$row->bc_name?></th>
										<td><?=$row->user_fname.' '.$row->user_lname?></td>

										<?php if($row->status_id == 1){ ?>
										<td class="text-center"><a href="<?=base_url('admin/view-broiler-trans/' . encode($row->bc_id).'/'. encode($row->broiler_group_id).'/'.encode($row->broiler_group_name) . '/' . encode(date( 'Y', strtotime($row->broiler_trans_date))))?>" class="brn btn-xs glyphicon glyphicon-file edit-broiler-config" title="View"></a>&nbsp;&nbsp;<a href="<?=base_url('admin/post-broiler-trans/' . encode($row->broiler_trans_id).'/'.encode($row->bc_id).'/'.encode($row->broiler_group_id).'/'.encode($row->broiler_group_name))?>" class="brn btn-xs btn-xs glyphicon glyphicon-lock edit-broiler-config" title="Post"></a></td>
										<?php  } else { ?>
										<td class="text-center"><a href="<?=base_url('admin/view-broiler-trans/' . encode($row->bc_id).'/'. encode($row->broiler_group_id).'/'.encode($row->broiler_group_name) . '/' . encode(date( 'Y', strtotime($row->broiler_trans_date))))?>" class="btn btn-xs btn-success edit-broiler-config">Compute</a></td>
										<?php } ?>
										<!-- <td>&nbsp;&nbsp;<a href="<?=base_url('admin/remove-broiler-config/' . encode($row->broiler_group_id))?>" class="btn btn-xs glyphicon glyphicon-remove remove-broiler-config" data-id="<?=encode($row->broiler_group_id)?>"></a></td> -->
									</tr>
									<?php endforeach; ?>
									
								</tbody>
							</table>
						</div>
					</div>

					<div id="other-trans-tab" class="tab-pane fade in">
						<!-- <br>
						<div class="row">
							<div class="col-lg-2">
								<label>Pick Year:</label>
								<div class="form-group">
									<div class="date">
				                        <div class="input-group input-append date" id="broiler-actual-data-year">
				                            <input type="text" name="month" id="broiler-actual-date-pick-year" class="form-control input-sm" placeholder="Pick year" value="<?=$year?>">
				                            <span class="input-group-addon add-on"><span class="glyphicon glyphicon-calendar"></span></span>
				                        </div>
				                    </div>
								</div>
							</div>
						</div> -->
						<input type="hidden" name="bc_id" id="bc_id" value="<?=encode($bc_id)?>">

						<div id="add-btn">
							<a href="<?=base_url('admin/new-broiler-amount-summary/' . encode($bc_id).'/'.$year)?>" class="btn btn-success btn-xs" id="add_broiler_actual_data">+ Add Broiler Actual Data</a>
						</div>
						<div class="table-responsive">
							<table class="table table-hover table-bordered nowrap" id="tbl-other-trans">
								<thead>
									<tr>
										<th width="70%">Broiler Item Name</th>
										<th width="auto" class="text-center">Year</th>
										<th width="auto" class="text-center">Action</th>
									</tr>
								</thead>
								<tbody>

									<?php

									foreach($broiler_amount_summary as $row):
										$trans_year_2 = date('Y', strtotime($row->trans_date));
									?>
									<tr>
										<th><?=$row->broiler_line_item?></th>
										<th class="text-center"><?=$trans_year_2?></th>

										<?php if($row->status_id == 3){ ?>
										<td class="text-center"><a href="<?=base_url('admin/edit-broiler-amount-summary/' . encode($row->broiler_line_item_id).'/'.encode($trans_year_2).'/'.encode($bc_id))?>" class="brn btn-xs glyphicon glyphicon-pencil" title="Edit"></a>&nbsp;&nbsp;<a href="#" data-id="<?=encode($row->broiler_line_item_id)?>" data-bc_id="<?=encode($row->bc_id)?>" data-year="<?=encode($trans_year_2)?>" class="brn btn-xs btn-xs glyphicon glyphicon-remove cancel-broiler-amount-summary" title="Cancel"></a></td>
										<?php  } else { ?>
										<td></td>
										<?php } ?>
									</tr>
									<?php endforeach; ?>
								</tbody>
							</table>
						</div>
					</div>

					<div id="industry-trans-tab" class="tab-pane fade in">
						<!-- <br>
						<div class="row">
							<div class="col-lg-2">
								<label>Pick Year:</label>
								<div class="form-group">
									<div class="date">
				                        <div class="input-group input-append date" id="industry-trans-data-year">
				                            <input type="text" name="month" class="form-control input-sm" placeholder="Pick year" id="industry-trans-year" value="<?=$year?>">
				                            <span class="input-group-addon add-on"><span class="glyphicon glyphicon-calendar"></span></span>
				                        </div>
				                    </div>
								</div>
							</div>
						</div> -->
						<div class="row">
							<div class="col-lg-7">
								
								<div id="add-btn">
									<a href="<?=base_url('admin/new-industry-trans/' . encode($bc_id).'/'.encode($year))?>" id="add_industry_trans_button" class="btn btn-success btn-xs">+ Add Industry Data</a>
								</div>
								<div class="table-responsive">
									<table class="table table-hover" id="tbl-industry-trans" width="100%">
										<thead>
											<tr>
												<th width="1%" rowspan="2"></th>
												<th width="auto" rowspan="2">Integrator</th>
												<th width="auto" class="text-center" colspan="6">CG CAPACITY </th>
											</tr>
											<tr>
												<th class="text-center">Beg. of Year</th>
												<th class="text-center">% of Total</th>
												<th class="text-center">Current</th>
												<th class="text-center">% of Total</th>
												<th class="text-center">End of Year</th>
												<th class="text-center">% of Total</th>
											</tr>
										</thead>
										<tbody>
											<?php
											$beginning_total = 0;
											$current_total = 0;
											$ending_total = 0;
											foreach($industry_total as $r):
												if($r->season_id == 1){
													$beginning_total = $r->industry_sum;
												}
												if($r->season_id == 2){
													$current_total = $r->industry_sum;
												}
												if($r->season_id == 3){
													$ending_total = $r->industry_sum;
												}
											endforeach;
											$industry_name_array = array();
											$industry_current_perc = array();
											$beginning_total_perc = 0;
											$current_total_perc = 0;
											$ending_total_perc = 0;
											foreach($industry_trans as $row):
												$beginning_perc = $beginning_total == 0 ? 0 : round($row->beginning_capacity/$beginning_total * 100, 2);
												$beginning_total_perc = $beginning_total_perc + $beginning_perc;

												$current_perc = $current_total == 0 ? 0 : round($row->current_capacity/$current_total * 100, 2);
												$current_total_perc = $current_total_perc + $current_perc;

												$ending_perc = $ending_total == 0 ? 0 : round($row->ending_capacity/$ending_total * 100, 2);
												$ending_total_perc = $ending_total_perc + $ending_perc;

												array_push($industry_name_array, $row->industry_name);
												array_push($industry_current_perc, $current_perc);
											?>
											<tr>
												<td class="text-center"><a href="#" data-id="<?=encode($row->industry_trans_id)?>" data-bc_id="<?=encode($row->bc_id)?>" data-trans-year="<?=$year?>" class="cancel-industry-trans"><i class="fa fa-remove"></i></a>&nbsp;&nbsp;<a href="<?=base_url('admin/edit-industry-trans/' . encode($row->industry_trans_id).'/'.encode($bc_id).'/'.encode($year))?>" class=""><i class="fa fa-pencil"></i></a></td>
												<td><?=$row->industry_name?></td>
												<td class="text-right"><?=number_format($row->beginning_capacity,0,'.',',')?></td>
												<td class="text-right"><?=number_format($beginning_perc,0,'.',',').'%'?></td>
												<td class="text-right"><?=number_format($row->current_capacity,0,'.',',')?></td>
												<td class="text-right"><?=number_format($current_perc,0,'.',',').'%'?></td>
												<td class="text-right"><?=number_format($row->ending_capacity,0,'.',',')?></td>
												<td class="text-right"><?=number_format($ending_perc,0,'.',',').'%'?></td>
											</tr>
											<?php endforeach; ?>
											
										</tbody>
										<tfoot>
											<tr>
												<th></th>
												<th>Total Capacity</th>
												<th class="text-right"><?=number_format($beginning_total,0,'.',',')?></th>
												<th class="text-right"><?=number_format($beginning_total_perc,0,'.',',').'%'?></th>
												<th class="text-right"><?=number_format($current_total,0,'.',',')?></th>
												<th class="text-right"><?=number_format($current_total_perc,0,'.',',').'%'?></th>
												<th class="text-right"><?=number_format($ending_total,0,'.',',')?></th>
												<th class="text-right"><?=number_format($ending_total_perc,0,'.',',').'%'?></th>

											</tr>
										</tfoot>
									</table>
								</div>
							</div>
							<div class="col-lg-5">
								<br>
								<br>
								<br>
								<br>
								<br>
								<br>
								<br>
								<div class="dashboard-label text-center">
									<label>Current CG Capacity</label>
									<canvas id="cg-chart" height="145px"></canvas>
								</div>
							</div>
						</div>
					</div>

					<div id="broiler-dashboard-tab" class="tab-pane fade in">
						<!-- <br>
						<div class="row">
							<div class="col-lg-2">
								<label>Pick Year:</label>
								<div class="form-group">
									<div class="date">
				                        <div class="input-group input-append date" id="broiler-dashboard-date">
				                            <input type="text" name="month" class="form-control input-sm" id="broiler-dashboard-year" placeholder="Pick year" value="<?=$year?>">
				                            <span class="input-group-addon add-on"><span class="glyphicon glyphicon-calendar"></span></span>
				                        </div>
				                    </div>
								</div>
							</div>
							
						</div> -->
						
						<div class="row">
							<div class="col-lg-1">
							</div>
							<div class="col-lg-10">
								<table class="table table-hover table-bordered table-striped" id="tbl-broiler-dashboard" width="100%">

									<thead>
										<tr>
											<th width="auto" class="text-center" rowspan="2">Month</th>
											<th width="auto" class="text-center" colspan="3">Harvestable Birds</th>
											<th width="auto" class="text-center" colspan="3">Broiler Cost</th>
										</tr>
										<tr>
											<td class="text-center"><?=$year?></td>
											<td class="text-center"><?=$year - 1?> Actual & YEE</td>
											<td class="text-center"><?=$year - 2?> Actual</td>

											<td class="text-center"><?=$year?></td>
											<td class="text-center"><?=$year - 1?> Actual & YEE</td>
											<td class="text-center"><?=$year - 2?> Actual</td>									
										</tr>
									</thead>
									<tbody>
											<?php
											$doctype = encode('trans');
											$total_actual_harvested_heads = 0;
											$total_actual_doc = 0;
											$total_actual_feeds = 0;
											$total_actual_cg = 0;
											$total_actual_medicines = 0;
											$total_actual_vaccines = 0;
											$total_actual_harvested_kilo = 0;

											$total_previous_actual_harvested_heads = 0;
											$total_previous_actual_doc = 0;
											$total_previous_actual_feeds = 0;
											$total_previous_actual_cg = 0;
											$total_previous_actual_medicines = 0;
											$total_previous_actual_vaccines = 0;
											$total_previous_actual_harvested_kilo = 0;

											$harvested_heads = 0;
											$harvested_kilo = 0;
											$growers_fee_amount = 0;
											$feed_cost_amount = 0;
											$vaccines_amount = 0;
											$medicine_amount = 0;
											$disinfectant_amount = 0;
											$doc_cost_amount = 0;
											

											for ($i=1; $i <=12 ; $i++) {
												//BUDGET BROILER
												$budgeted_harvested_heads[$i] = isset($budgeted_harvested_heads[$i]) ? $budgeted_harvested_heads[$i] : 0;
												$budgeted_harvested_kilo[$i] = isset($budgeted_harvested_kilo[$i]) ? $budgeted_harvested_kilo[$i] : 0;
												$budgeted_doc_cost_amount[$i] = isset($budgeted_doc_cost_amount[$i]) ? $budgeted_doc_cost_amount[$i] : 0;
												$budgeted_growers_fee_amount[$i] = isset($budgeted_growers_fee_amount[$i]) ? $budgeted_growers_fee_amount[$i] : 0;
												$budgeted_feed_cost_amount[$i] = isset($budgeted_feed_cost_amount[$i]) ? $budgeted_feed_cost_amount[$i] : 0;
												$budgeted_vaccines_amount[$i] = isset($budgeted_vaccines_amount[$i]) ? $budgeted_vaccines_amount[$i] : 0;
												$budgeted_medicine_amount[$i] = isset($budgeted_medicine_amount[$i]) ? $budgeted_medicine_amount[$i] : 0;
												$budgeted_disinfectant_amount[$i] = isset($budgeted_disinfectant_amount[$i]) ? $budgeted_disinfectant_amount[$i] : 0;
												
												$harvested_heads = $harvested_heads + $budgeted_harvested_heads[$i];
												$harvested_kilo = $harvested_kilo + $budgeted_harvested_kilo[$i];
												$doc_cost_amount = $doc_cost_amount + $budgeted_doc_cost_amount[$i];
												$growers_fee_amount = $growers_fee_amount + $budgeted_growers_fee_amount[$i];
												$feed_cost_amount = $feed_cost_amount + $budgeted_feed_cost_amount[$i];
												$vaccines_amount = $vaccines_amount + $budgeted_vaccines_amount[$i];
												$medicine_amount = $medicine_amount + $budgeted_medicine_amount[$i];
												$disinfectant_amount = $disinfectant_amount + $budgeted_disinfectant_amount[$i];

												$doc_ave = $harvested_kilo == 0 ? 0 : $doc_cost_amount/$harvested_kilo;
												$growers_fee_ave = $harvested_kilo == 0 ? 0 : $growers_fee_amount/$harvested_kilo;
												$feed_cost_ave = $harvested_kilo == 0 ? 0 : $feed_cost_amount/$harvested_kilo;
												$vaccines_ave = $harvested_kilo == 0 ? 0 : $vaccines_amount/$harvested_kilo;
												$total = $disinfectant_amount + $medicine_amount;
												$medicine_ave = $harvested_kilo == 0 ? 0 : $total/$harvested_kilo;
												$actual_harvested_heads[$i] = isset($actual_harvested_heads[$i]) ? $actual_harvested_heads[$i] : 0;
												$month = date('F', strtotime($year.'-'.$i.'-01'));

												

												
												$actual_harvested_kilo[$i] = isset($actual_harvested_kilo[$i]) ? $actual_harvested_kilo[$i] : 0;
												$actual_feeds_amount[$i] = isset($actual_feeds_amount[$i]) ? $actual_feeds_amount[$i] : 0;
												$actual_cg_fee_amount[$i] = isset($actual_cg_fee_amount[$i]) ? $actual_cg_fee_amount[$i] : 0;
												$actual_medicines_amount[$i] = isset($actual_medicines_amount[$i]) ? $actual_medicines_amount[$i] : 0;
												$actual_vaccines_amount[$i] = isset($actual_vaccines_amount[$i]) ? $actual_vaccines_amount[$i] : 0;
												$actual_doc_amount[$i] = isset($actual_doc_amount[$i]) ? $actual_doc_amount[$i] : 0;

												$actual_doc[$i] = $actual_harvested_kilo[$i] == 0 ? 0 : $actual_doc_amount[$i]/$actual_harvested_kilo[$i];
												$actual_feeds[$i] = $actual_harvested_kilo[$i] == 0 ? 0 : $actual_feeds_amount[$i]/$actual_harvested_kilo[$i];
												$actual_cg[$i] = $actual_harvested_kilo[$i] == 0 ? 0 : $actual_cg_fee_amount[$i]/$actual_harvested_kilo[$i];
												$actual_medicines[$i] = $actual_harvested_kilo[$i] == 0 ? 0 : $actual_medicines_amount[$i]/$actual_harvested_kilo[$i];
												$actual_vaccines[$i] = $actual_harvested_kilo[$i] == 0 ? 0 : $actual_vaccines_amount[$i]/$actual_harvested_kilo[$i];
												$actual_broiler_cost[$i] = $actual_doc[$i] + $actual_feeds[$i] + $actual_cg[$i] + $actual_medicines[$i] + $actual_vaccines[$i];

												$total_actual_doc = $total_actual_doc + $actual_doc_amount[$i];
												$total_actual_feeds = $total_actual_feeds + $actual_feeds_amount[$i];
												$total_actual_cg = $total_actual_cg + $actual_cg_fee_amount[$i];
												$total_actual_medicines = $total_actual_medicines + $actual_medicines_amount[$i];
												$total_actual_vaccines = $total_actual_vaccines + $actual_vaccines_amount[$i];
												$total_actual_harvested_kilo = $total_actual_harvested_kilo + $actual_harvested_kilo[$i];


												$previous_actual_doc_amount[$i] = isset($previous_actual_doc_amount[$i]) ? $previous_actual_doc_amount[$i] : 0;
												$previous_actual_feeds_amount[$i] = isset($previous_actual_feeds_amount[$i]) ? $previous_actual_feeds_amount[$i] : 0;
												$previous_actual_cg_fee_amount[$i] = isset($previous_actual_cg_fee_amount[$i]) ? $previous_actual_cg_fee_amount[$i] : 0;
												$previous_actual_medicines_amount[$i] = isset($previous_actual_medicines_amount[$i]) ? $previous_actual_medicines_amount[$i] : 0;
												$previous_actual_vaccines_amount[$i] = isset($previous_actual_vaccines_amount[$i]) ? $previous_actual_vaccines_amount[$i] : 0;
												$previous_actual_harvested_kilo[$i] = isset($previous_actual_harvested_kilo[$i]) ? $previous_actual_harvested_kilo[$i] : 0;
												$previous_actual_harvested_heads[$i] = isset($previous_actual_harvested_heads[$i]) ? $previous_actual_harvested_heads[$i] : 0;

												$previous_actual_doc[$i] = $previous_actual_harvested_kilo[$i] == 0 ? 0 : $previous_actual_doc_amount[$i]/$previous_actual_harvested_kilo[$i];
												$previous_actual_feeds[$i] = $previous_actual_harvested_kilo[$i] == 0 ? 0 : $previous_actual_feeds_amount[$i]/$previous_actual_harvested_kilo[$i];
												$previous_actual_cg[$i] = $previous_actual_harvested_kilo[$i] == 0 ? 0 : $previous_actual_cg_fee_amount[$i]/$previous_actual_harvested_kilo[$i];
												$previous_actual_medicines[$i] = $previous_actual_harvested_kilo[$i] == 0 ? 0 : $previous_actual_medicines_amount[$i]/$previous_actual_harvested_kilo[$i];
												$previous_actual_vaccines[$i] = $previous_actual_harvested_kilo[$i] == 0 ? 0 : $previous_actual_vaccines_amount[$i]/$previous_actual_harvested_kilo[$i];
												$previous_actual_broiler_cost[$i] = $previous_actual_doc[$i] + $previous_actual_feeds[$i] + $previous_actual_cg[$i] + $previous_actual_medicines[$i] + $previous_actual_vaccines[$i];

												$total_previous_actual_doc = $total_previous_actual_doc + $previous_actual_doc_amount[$i];
												$total_previous_actual_feeds = $total_previous_actual_feeds + $previous_actual_feeds_amount[$i];
												$total_previous_actual_cg = $total_previous_actual_cg + $previous_actual_cg_fee_amount[$i];
												$total_previous_actual_medicines = $total_previous_actual_medicines + $previous_actual_medicines_amount[$i];
												$total_previous_actual_vaccines = $total_previous_actual_vaccines + $previous_actual_vaccines_amount[$i];
												$total_previous_actual_harvested_kilo = $total_previous_actual_harvested_kilo + $previous_actual_harvested_kilo[$i];

												$total_actual_harvested_heads = $total_actual_harvested_heads + $actual_harvested_heads[$i];
												$total_previous_actual_harvested_heads = $total_previous_actual_harvested_heads + $previous_actual_harvested_heads[$i];

												
											?>
											<tr>
												<td class="text-left"><?=$month?></td>
												<td class="text-right"><?=number_format($budgeted_harvested_heads[$i],0,'.',',')?></td>
												<td class="text-right"><?=number_format($actual_harvested_heads[$i],0,'.',',')?></td>
												<td class="text-right"><?=number_format($previous_actual_harvested_heads[$i],0,'.',',')?></td>

												<th class="text-right"><?=number_format(get_broiler_cost(encode($bc_id), $trans_year, $i, $doctype),dec_places_dis(),'.',',')?></th>
												<td class="text-right"><?=number_format($actual_broiler_cost[$i],dec_places_dis(),'.',',')?></td>
												<td class="text-right"><?=number_format($previous_actual_broiler_cost[$i],dec_places_dis(),'.',',')?></td>

											</tr>
											<?php } ?>
											<tr>
											<?php
												$broiler_cost_ave = $medicine_ave + $doc_ave + $growers_fee_ave + $feed_cost_ave + $vaccines_ave;
												$total_actual_broiler_cost = $total_actual_doc + $total_actual_feeds + $total_actual_cg + $total_actual_vaccines + $total_actual_medicines;
												$total_actual_broiler_cost = $total_actual_harvested_kilo <= 0 ? 0 : $total_actual_broiler_cost/$total_actual_harvested_kilo;

												$total_previous_actual_broiler_cost = $total_previous_actual_doc + $total_previous_actual_feeds + $total_previous_actual_cg + $total_previous_actual_vaccines + $total_previous_actual_medicines;
												$total_previous_actual_broiler_cost = $total_previous_actual_harvested_kilo <= 0 ? 0 : $total_previous_actual_broiler_cost/$total_previous_actual_harvested_kilo;
												?>
												<td class="text-left">FOR THE YEAR</td>
												<td class="text-right"><?=number_format($harvested_heads,0,'.',',')?></td>
												<td class="text-right"><?=number_format($total_actual_harvested_heads,0,'.',',')?></td>
												<td class="text-right"><?=number_format($total_previous_actual_harvested_heads,0,'.',',')?></td>

												<th class="text-right"><?=number_format($broiler_cost_ave,dec_places_dis(),'.',',')?></th>
												<td class="text-right"><?=number_format($total_actual_broiler_cost,dec_places_dis(),'.',',')?></td>
												<td class="text-right"><?=number_format($total_previous_actual_broiler_cost,dec_places_dis(),'.',',')?></td>
											</tr>
									</tbody>
								</table>
							</div>
						</div>

						<div class="row">
							<div class="col-lg-12" id="cost-chart">
								<canvas id="line-chart" width="800"></canvas>
							</div>
						</div>
						<br>
					</div>
				</div>

					<div id="modal-confirm" class="modal fade" role="dialog">
						<div class="modal-dialog modal-sm">
						    <div class="modal-content">
						    	<div class="modal-header">
						        	<button type="button" class="close" data-dismiss="modal">&times;</button>
						       		<strong>Confirmation message</strong>
						      	</div>
						      	<div class="modal-body">
						      		<form method="POST" action="<?=base_url('admin/cancel-broiler-amount-summary')?>" enctype="multipart/form-data" id="cancel-broiler-amount-summary">

						      			<input type="hidden" name="broiler_line_item_id" id="broiler_line_item_id">
						      			<input type="hidden" name="trans_status" id="trans_status">
						      			<input type="hidden" name="bc_id" id="bc_id">
						      			<input type="hidden" name="year" id="year">

							        	<div id="modal-msg" class="text-center">
							        		
							        	</div>
							        	<div id="modal-btn" class="text-center">
							        		<button type="submit" class="btn btn-info btn-sm">Yes</button>&nbsp;&nbsp;
							        		<button data-dismiss="modal" class="btn btn-danger btn-sm">No</button>
							        	</div>
							        </form>
						      	</div>
						    </div>
						</div>
					</div>

					<div id="modal-confirm2" class="modal fade" role="dialog">
						<div class="modal-dialog modal-sm">
						    <div class="modal-content">
						    	<div class="modal-header">
						        	<button type="button" class="close" data-dismiss="modal">&times;</button>
						       		<strong>Confirmation message</strong>
						      	</div>
						      	<div class="modal-body">
						      		<form method="POST" action="<?=base_url('admin/cancel-industry-trans')?>" enctype="multipart/form-data" id="cancel-industry-trans">

						      			<input type="hidden" name="industry_trans_id" id="industry_trans_id">
						      			<input type="hidden" name="trans_status" id="trans_status">
						      			<input type="hidden" name="bc_id" id="bc_id">
										<input type="hidden" name="trans_year" id="trans_year">

							        	<div id="modal-msg" class="text-center">
							        		
							        	</div>
							        	<div id="modal-btn" class="text-center">
							        		<button type="submit" class="btn btn-info btn-sm">Yes</button>&nbsp;&nbsp;
							        		<button data-dismiss="modal" class="btn btn-danger btn-sm">No</button>
							        	</div>
							        </form>
						      	</div>
						    </div>
						</div>
					</div>
			</div>


<script type="text/javascript">
	cg_chart = new Chart(document.getElementById("cg-chart"), {
	    type: 'doughnut',
	    data: {
	    	labels: <?=json_encode($industry_name_array); ?>,
	      	datasets: [
	        {
	        	label: "Current CG Capacity",
		        backgroundColor: ["#3e95cd", "#8e5ea2","#3cba9f","#e8c3b9","#c45850", "#ffa31a", "#99e699", "#00ffcc", "gold", "#00cc66"],
		        hoverBorderColor : ["#3e95cd", "#8e5ea2","#3cba9f","#e8c3b9","#c45850", "#ffa31a", "#99e699", "#00ffcc", "gold", "#00cc66"],
		        data: <?=json_encode($industry_current_perc); ?>
	        }
	      	]
	    },
	    options: {
	    	title: {
	        	display: false
	      	},
		    legend: {
	            display: true,
	            position: "bottom",
	            labels: {
	                fontColor: "#333",
	                fontSize: 11
	            }
	        },
	       	tooltips: {
		    	mode: 'index',
		   		callbacks: {
		   			label: function(tooltipItem, data) {
	                    var value = data.datasets[0].data[tooltipItem.index];
	                    var index = tooltipItem.index;
	                    var label = data.labels[index];
	                    return label +  ': ' + number_format(value);
                	}
		        }
		    },
		    plugins: {
			    labels: {
					render: 'percentage',
					fontColor: ['#fff', '#fff', '#fff', '#fff', '#fff', '#fff', '#111', '#111', '#111', '#fff'],
					fontSize: 12,
					textShadow: true,
					shadowBlur: 10,
				}
			}
	    }
	});
</script>


