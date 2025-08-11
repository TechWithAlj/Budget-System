			
			<div class="col-lg-12" id="content">
				<div id="breadcrumb-div">
					<ul class="breadcrumb">
					    <li><a href="<?=base_url('unit')?>"><span class="glyphicon glyphicon-home"></span>&nbsp;Dashboard</a></li>
					    <li class="active">OPEX Info</li>
					</ul>
				</div>
				<?php
					if($this->session->flashdata('message') != "" ){
						echo $this->session->flashdata('message');
					}
				?>

				<div class="row">
					<div class="col-lg-3">
						<label class="data-info">Unit: <?=$cost_center_desc?></label><br /><br />
					</div>

					<div class="col-lg-2">
						<label>Budget Year:</label>
						<div class="form-group">
							<div class="date">
		                        <div class="input-group input-append date" id="opex-trans-year">
		                            <input type="text" name="month" id="opex-year" class="form-control input-sm" placeholder="Pick year" value="<?=$year?>">
		                            <span class="input-group-addon add-on"><span class="glyphicon glyphicon-calendar"></span></span>
		                        </div>
		                    </div>
						</div>
					</div>

					<div class="col-lg-7">
						<div class="text-right">
							<a href="<?=base_url('unit/download-opex/' . $year)?>" target="_blank"><span class="glyphicon glyphicon-download-alt"></span>&nbsp;&nbsp; Download OPEX</a>
						</div>
					</div>
				</div>

				<ul class="nav nav-tabs">
   					<li class="active"><a data-toggle="tab" class="tab-letter" href="#opex-tab">OPEX</a></li>
				    <li><a data-toggle="tab" href="#opex-graph-tab" class="opex-graph-letter">Dashboard</a></li>
  				</ul>

  				<div class="tab-content">
  					<input type="hidden" value="<?=$cost_center?>" id="id">
    				<div id="opex-tab" class="tab-pane fade in active"><br/ >
						<div id="add-btn">
							<?php if($budget_status == 1):?>

							<a href="<?=base_url('unit/transac-opex/' . $year)?>" class="btn btn-success btn-xs">+ Add Opex</a>

							<?php endif;?>
						</div>
						<table class="table table-hover table-bordered table-striped" id="tbl-opex-info">
							<thead>
								<tr style="font-size: 15px;">
									<th>GL Group</th>
									<th>Cost Center Desc</th>
									<th>Cost Center Code</th>
									<th>Total</th>
									<th>Action</th>
								</tr>
							</thead>
							<tbody>

								<?php
									foreach($gl_group as $row):
								?>
								
								<tr>
									<td><?=$row->gl_group_name?></td>
									<td><?=$row->cost_center_desc?></td>
									<td><?=$row->cost_center_code?></td>
									<td></td>
									<td><a href="<?=base_url('unit/view-opex/' . encode($row->gl_trans_id))?>" class="btn btn-success btn-xs">View</a>&nbsp;&nbsp;

									<?php if($budget_status == 1):?>

										<a data-id="<?=encode($row->gl_trans_id)?>" class="btn btn-danger btn-xs cancel-opex-btn">Cancel</a></td>

									<?php endif;?>
									
								</tr>

								<?php endforeach;?>

								
								<?php
									foreach($sw as $row_sw):
								?>	
									<tr>
										<td>SALARIES & WAGES</td>
										<td><?=$row_sw->cost_center_desc?></td>
										<td><?=$row_sw->cost_center_code?></td>
										<td></td>
										<td><a href="<?=base_url('unit/sw-view/' . encode($row_sw->emp_salary_trans_id))?>" class="btn btn-success btn-xs">View</a>&nbsp;&nbsp;

										<?php if($budget_status == 1):?>
											
											<a data-id="<?=encode($row_sw->emp_salary_trans_id)?>" class="btn btn-danger btn-xs cancel-sw-btn">Cancel</a></td>

										<?php endif;?>
									</tr>

								<?php endforeach; ?>

							</tbody>
						</table>

						<div id="modal-cancel-opex" class="modal fade" role="dialog">
							<div class="modal-dialog modal-sm">
							    <div class="modal-content">
							    	<div class="modal-header">
							        	<button type="button" class="close" data-dismiss="modal">&times;</button>
							       		<strong>Cancel OPEX</strong>
							      	</div>
							      	<div class="modal-body">
							      		<form method="POST" action="<?=base_url('unit/cancel-opex')?>" enctype="multipart/form-data" id="cancel-opex">
							      			<input type="hidden" name="id" id="id">
								        	<div id="modal-msg" class="text-center">
								        		<label>Are you sure to cancel this OPEX?</label>
								        	</div>
								        	<div id="modal-btn" class="text-center">
								        		<button type="submit" class="btn btn-info btn-sm">Yes</button>&nbsp;&nbsp;
								        		<a href="" data-dismiss="modal" class="btn btn-danger btn-sm">No</a>
								        	</div>
								        </form>
							      	</div>
							    </div>
							</div>
						</div>

						<div id="modal-cancel-sw-opex" class="modal fade" role="dialog">
							<div class="modal-dialog modal-sm">
							    <div class="modal-content">
							    	<div class="modal-header">
							        	<button type="button" class="close" data-dismiss="modal">&times;</button>
							       		<strong>Cancel OPEX</strong>
							      	</div>
							      	<div class="modal-body">
							      		<form method="POST" action="<?=base_url('unit/cancel-sw-opex')?>" enctype="multipart/form-data" id="cancel-sw-opex">
							      			<input type="hidden" name="id" id="id">
								        	<div id="modal-msg" class="text-center">
								        		<label>Are you sure to cancel this OPEX?</label>
								        	</div>
								        	<div id="modal-btn" class="text-center">
								        		<button type="submit" class="btn btn-info btn-sm">Yes</button>&nbsp;&nbsp;
								        		<a href="" data-dismiss="modal" class="btn btn-danger btn-sm">No</a>
								        	</div>
								        </form>
							      	</div>
							    </div>
							</div>
						</div>
					</div>

					<div id="opex-graph-tab" class="tab-pane fade">
						<div class="row"><br/ ><br/ >
							<div class="col-lg-6" id="cost-chart">
								<canvas id="doughnut-chart" width="800"></canvas>
							</div>

							<div class="col-lg-6" id="cost-chart">
								<canvas id="line-chart" width="800" height="500"></canvas>
							</div>
						</div>

						<div class="row">
							<div class="col-lg-12" id="cost-chart">
								<canvas id="bar-chart" height="50" width="200"></canvas>
							</div>
						</div>

						<div class="row">
							<div id="opex-table" class="col-lg-offset-1 col-lg-10">
								<table class="table">
									<thead>
										<tr>
											<td colspan="8" class="text-center"><strong>OPEX <?=$year?> Summary</strong></td>
										</tr>
										<tr>
											<td>GL GROUP</td>
											<td class="text-right"><?=$year?> B</td>
											<td class="text-right"><?=$year - 1?> F</td>
											<td class="text-right"><?=$year - 2?> A</td>
											<td class="text-right"><?=$year?> vs <?=$year - 1?></td>
											<td class="text-right">%</td>
											<td class="text-right"><?=$year?> vs <?=$year - 2?></td>
											<td class="text-right">%</td>
										</tr>
									</thead>

									<tbody>
										<?php
										 	$gl_name = '';
										 	$gl_total = 0;
										 	$gl_total1 = 0;
										 	$gl_total2 = 0;
										 	$grand_total = 0;
										 	$grand_total1 = 0;
										 	$grand_total2 = 0;
										 	$gl_total_dif1 = 0;
										 	$gl_total_dif2 = 0;
										 	$gl_total_per1 = 0;
										 	$gl_total_per2 = 0;
										 	$count = 0;

										 	$data_details = '';
											foreach($opex_gl as $row_opex):
												$grand_total += ($row_opex->opex);
												$grand_total1 += ($row_opex->opex1);
												$grand_total2 += ($row_opex->opex2);
												$gl_old = $gl_name;
												if($gl_name != $row_opex->gl_group_name){
													$gl_name = $row_opex->gl_group_name;
													if($count > 0){

														$gl_total_dif1 = ($gl_total - $gl_total1) * -1;
														$gl_total_dif2 = ($gl_total - $gl_total2) * -1;
														$gl_total_per1 = ($gl_total1 != 0 ? ($gl_total_dif1 / $gl_total1) * 100 : 0);
														$gl_total_per2 = ($gl_total2 != 0 ? ($gl_total_dif2 / $gl_total2) * 100 : 0);

														echo '<tr>';
														echo '<td>' . $gl_old . '</td>';
														echo '<td class="text-right">' . number_format($gl_total) . '</td>';
														echo '<td class="text-right">' . number_format($gl_total1) . '</td>';
														echo '<td class="text-right">' . number_format($gl_total2) . '</td>';
														echo '<td class="text-right">' . number_format($gl_total_dif1) . '</td>';
														echo '<td class="text-right">' . number_format($gl_total_per1) . '%</td>';
														echo '<td class="text-right">' . number_format($gl_total_dif1) . '</td>';
														echo '<td class="text-right">' . number_format($gl_total_per2) . '%</td>';
														echo '</tr>';

														if($gl_total > 0){
															$data_details .= '<tr>';
															$data_details .= '<td style="border-bottom: 1px solid black;" colspan="2" class="text-right"><strong>Total: </strong></td>';
															$data_details .= '<td style="border-bottom: 1px solid black;" class="text-right"><strong>' . number_format($gl_total) . '</strong></td>';

															$data_details .= '</tr>';
															$data_details .= '<tr>';
															$data_details .= '<td><strong>' . $row_opex->gl_group_name . '</strong></td>';
														}

														$gl_total = 0;
														$gl_total1 = 0;
														$gl_total2 = 0;
													}else{
														
														$data_details .= '<tr>';
														$data_details .= '<td><strong>' . $row_opex->gl_group_name . '</strong></td>';
													}

													$gl_total += $row_opex->opex;
													$gl_total1 += $row_opex->opex1;
													$gl_total2 += $row_opex->opex2;
												}else{
													$gl_total += $row_opex->opex;
													$gl_total1 += $row_opex->opex1;
													$gl_total2 += $row_opex->opex2;

													$data_details .= '<tr">';
													$data_details .= '<td></td>';
												}
												$count++;
												
												if($row_opex->opex){
													$data_details .= '<td>' . $row_opex->gl_sub_name . '</td>';
													$data_details .= '<td class="text-right">' . number_format($row_opex->opex) . '</td>';
													$data_details .= '</tr>';
												}

											endforeach;

											$grand_total_dif1 = ($grand_total - $grand_total1) * -1;
											$grand_total_dif2 = ($grand_total - $grand_total2) * -1;
											$grand_total_per1 = ($grand_total1 != 0 ? ($grand_total_dif1 / $grand_total1) * 100 : 0);
											$grand_total_per2 = ($grand_total2 != 0 ? ($grand_total_dif2 / $grand_total2) * 100 : 0);
											

												echo '<tr>';
												echo '<td>' . $gl_name .'</td>';
												echo '<td class="text-right">' . number_format($gl_total) .'</td>';
												echo '<td class="text-right">' . number_format($gl_total1) . '</td>';
												echo '<td class="text-right">' . number_format($gl_total2) . '</td>';
												echo '<td class="text-right">' . number_format($gl_total_dif1) . '</td>';
												echo '<td class="text-right">' . number_format($gl_total_per1) . '%</td>';
												echo '<td class="text-right">' . number_format($gl_total_dif1) . '</td>';
												echo '<td class="text-right">' . number_format($gl_total_per2) . '%</td>';
												echo '</tr>';

												echo '<tr>';
												echo '<tr style="border-top: 2px solid black;">';
												echo '<td><strong>Grand Total:</strong></td>';
												echo '<td class="text-right"><strong>' . number_format($grand_total) . '</strong></td>';
												echo '<td class="text-right"><strong>' . number_format($grand_total1) . '</strong></td>';
												echo '<td class="text-right"><strong>' . number_format($grand_total2) . '</strong></td>';
												echo '<td class="text-right"><strong>' . number_format($grand_total_dif1) . '</strong></
												td>';

												echo '<td class="text-right"><strong>' . number_format($grand_total_per1) . '%</strong></td>';
												echo '<td class="text-right"><strong>' . number_format($grand_total_dif2) . '</strong></td>';
												echo '<td class="text-right"><strong>' . number_format($grand_total_per2) . '%</strong></td>';
												echo '</tr>';
										
											$data_details .= '<tr>';
											$data_details .= '<td style="border-bottom: 1px solid black;" colspan="2" class="text-right"><strong>Total: </strong></td>';
											$data_details .= '<td style="border-bottom: 1px solid black;" class="text-right"><strong>' . number_format($gl_total). '</strong></td>';
											$data_details .= '</tr>';

											$data_details .= '<tr>';
											$data_details .= '<td style="border-bottom: 1px solid black;" colspan="2" class=""><strong>Grand Total: </strong></td>';
											$data_details .= '<td style="border-bottom: 1px solid black;" class="text-right"><strong>' . number_format($grand_total) . '</strong></td>';
											$data_details .= '</tr>';
									?>
									</tbody>
								</table>
							</div>

							<div id="opex-table" class="col-lg-offset-1 col-lg-10">
								<table class="table">
									<thead>
										<tr>
											<td colspan="3" class="text-center"><strong>OPEX <?=$year?> Detailed</strong></td>
										</tr>
										<tr>
											<td>GL GROUP</td>
											<td>GL SUBGROUP</td>
											<td class="text-center">AMOUNT</td>
										</tr>
									</thead>

									<tbody>

										<?php
											$data_details = '';
											$gl_name = '';
											$gl_total = 0;
										 	$grand_total = 0;
										 	$count = 0;
											foreach($opex_gl as $row_opex){
												$gl_old = $gl_name;
												if($row_opex->opex > 0){
													$grand_total += $row_opex->opex;
													if($gl_name != $row_opex->gl_group_name){
														$gl_name = $row_opex->gl_group_name;
														if($count > 0){

															$data_details .= '<tr>';
															$data_details .= '<td style="border-bottom: 1px solid black;" colspan="2" class="text-right"><strong>Total: </strong></td>';
															$data_details .= '<td style="border-bottom: 1px solid black;" class="text-right"><strong>' . number_format($gl_total) . '</strong></td>';

															$data_details .= '</tr>';
															$data_details .= '<tr>';
															$data_details .= '<td><strong>' . $row_opex->gl_group_name . '</strong></td>';
															$gl_total = 0;
														}else{
															$data_details .= '<tr>';
															$data_details .= '<td><strong>' . $row_opex->gl_group_name . '</strong></td>';
														}

														$gl_total += $row_opex->opex;
													}else{
														$gl_total += $row_opex->opex;

														$data_details .= '<tr">';
														$data_details .= '<td></td>';
													}

													$count++;
													
													$data_details .= '<td>' . $row_opex->gl_sub_name . '</td>';
													$data_details .= '<td class="text-right">' . number_format($row_opex->opex) . '</td>';
													$data_details .= '</tr>';
												}
											}


											$data_details .= '<tr>';
											$data_details .= '<td style="border-bottom: 1px solid black;" colspan="2" class="text-right"><strong>Total: </strong></td>';
											$data_details .= '<td style="border-bottom: 1px solid black;" class="text-right"><strong>' . number_format($gl_total). '</strong></td>';
											$data_details .= '</tr>';

											$data_details .= '<tr>';
											$data_details .= '<td style="border-bottom: 1px solid black;" colspan="2" class=""><strong>Grand Total: </strong></td>';
											$data_details .= '<td style="border-bottom: 1px solid black;" class="text-right"><strong>' . number_format($grand_total) . '</strong></td>';
											$data_details .= '</tr>';

										?>

										<?=$data_details?>

										
									</tbody>
								</table>
							</div>

							<div id="opex-table" class="col-lg-12">
								<table class="table table-hover table-bordered table-striped" id="tbl-opex-monthly">
									<thead>
										<tr>
											<td colspan="13" class="text-center"><span style="font-size: 18px;">Monthly OPEX <?=$year?></span></td>
										</tr>
										<tr style="font-size: 15px;">
											<td>GL GROUP</td>
											<td class="text-center">Jan</td>
											<td class="text-center">Feb</td>
											<td class="text-center">Mar</td>
											<td class="text-center">Apr</td>
											<td class="text-center">May</td>
											<td class="text-center">June</td>
											<td class="text-center">July</td>
											<td class="text-center">Aug</td>
											<td class="text-center">Sep</td>
											<td class="text-center">Oct</td>
											<td class="text-center">Nov</td>
											<td class="text-center">Dec</td>
										</tr>
									</thead>

									<tbody>
									

									</tbody>
								</table>
							</div>
						</div>

					</div>

					
				</div>
			</div>

			<script type="text/javascript">
				$(document).ready(function(){
					var base_url = $('#base_url').val();
					var id = $('#id').val();
					var year = $('#opex-year').val();

					Chart.register(ChartDataLabels);

					amount_per_gl();
					amount_per_month();
					amount_per_month_group();

					/*amount_per_gl_capex();
					amount_per_month_capex();*/

					function number_format (number, decimals, dec_point, thousands_sep) {
					    // Strip all characters but numerical ones.
					    number = (number + '').replace(/[^0-9+\-Ee.]/g, '');
					    var n = !isFinite(+number) ? 0 : +number,
					        prec = !isFinite(+decimals) ? 0 : Math.abs(decimals),
					        sep = (typeof thousands_sep === 'undefined') ? ',' : thousands_sep,
					        dec = (typeof dec_point === 'undefined') ? '.' : dec_point,
					        s = '',
					        toFixedFix = function (n, prec) {
					            var k = Math.pow(10, prec);
					            return '' + Math.round(n * k) / k;
					        };
					    // Fix for IE parseFloat(0.55).toFixed(0) = 0;
					    s = (prec ? toFixedFix(n, prec) : '' + Math.round(n)).split('.');
					    if (s[0].length > 3) {
					        s[0] = s[0].replace(/\B(?=(?:\d{3})+(?!\d))/g, sep);
					    }
					    if ((s[1] || '').length < prec) {
					        s[1] = s[1] || '';
					        s[1] += new Array(prec - s[1].length + 1).join('0');
					    }
					    return s.join(dec);
					}

					function amount_per_gl(){
						var url = base_url + 'unit/opex-donut/' + id + '/' + year;
						$.ajax({
					    	url: url,
					    	method: 'GET',
					    	success:function(response){
					    		var parse_data = JSON.parse(response);
					    		var gl_group = [];
					    		var amount = [];
					    		var color = [];
					    		if(parse_data['result'] == 1){
					    			for(var a in parse_data['info']){
										amount.push(parseInt(parse_data['info'][a]['amount']).toFixed(2));
										gl_group.push(parse_data['info'][a]['gl_group']);
										color.push(parse_data['info'][a]['color']);
									}
						    	}else{
						    		console.log('Error please contact your administrator.');
						    	}

						    	if(typeof(gl_chart) != 'undefined'){
									gl_chart.destroy();
								}

								gl_chart = new Chart(document.getElementById("doughnut-chart"), {
								    type: 'doughnut',
								    data: {
								    	labels: gl_group,
								      	datasets: [
								        {
								        	label: "GL Group",
								        	backgroundColor: color,
								        	data: amount
								        }
								      	]
								    },
								    options: {
								    	title: {
								        	display: true,
								        	position: "top",
								        	text: 'OPEX per GL',
								        	fontSize: 17
								      	},
									    legend: {
								            display: true,
								            position: "bottom",
								            labels: {
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
											datalabels: {
				                                borderRadius: 25,
				                                color: 'white',
				                                font: {
				                                    size: 14,
				                                },
				                                padding: 0,
				                                formatter: (value, ctx) => {
									                let sum = 0;
									                let dataArr = ctx.chart.data.datasets[0].data;
									                dataArr.map(data => {
									                    sum += parseInt(data);
									                });
									                let percentage = (value*100 / sum).toFixed(2)+"%";
									                return percentage;
									            }
				                            }
										},
										aspectRatio: 1.6
								    }
								});
					    	}
						});
					}

					function amount_per_month(){
						var url = base_url + 'unit/opex-line/' + id + '/' + year;
						$.ajax({
					    	url: url,
					    	method: 'GET',
					    	success:function(response){
					    		var parse_data = JSON.parse(response);
					    		var budget_date = [];
					    		var amount = [];
					    		var color = [];
					    		if(parse_data['result'] == 1){
					    			for(var a in parse_data['info']){
										amount.push(parseInt(parse_data['info'][a]['amount']).toFixed(2));
										budget_date.push(parse_data['info'][a]['budget_date']);
									}
						    	}else{
						    		console.log('Error please contact your administrator.');
						    	}

						    	if(typeof(month_chart) != 'undefined'){
									month_chart.destroy();
								}

								month_chart = new Chart(document.getElementById("line-chart"), {
								    type: 'line',
								    data: {
								    	labels: budget_date,
								      	datasets: [
								        {
								        	label: "Monthly Cost",
								        	backgroundColor: ["#99d815", "#8e5ea2","#3cba9f","#e8c3b9","#c45850"],
								        	data: amount,
								        	fill: true,
								        	backgroundColor: 'rgb(0,155,106,0.5)',
								        	pointRadius: 5,
								        	pointBackgroundColor: "#1eb0bb",
								        }
								      	]
								    },
								    options: {
								    	title: {
								        	display: true,
								        	position: "top",
								        	text: 'OPEX per month',
								        	fontSize: 17
								        	
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
									   				var main_label = (data.datasets[0].label);
								                    var value = data.datasets[0].data[tooltipItem.index];
								                    var index = tooltipItem.index;
								                    var label = data.labels[index];
								                    return main_label +  ': ' + number_format(value);
							                	}
									        }
									    },

									    scales: {
								            y: {
								                ticks: {
								                    fontColor: "rgba(0,0,0,0.7)",
								                    beginAtZero: true,
								                    userCallback: function(value, index, values) {
														if(value > 1000 && value < 1000000){
															value = value/1000;
															value = number_format(value) + ' K';
														}else if(value >= 1000000 && value < 100000000){
															value = value/1000000;
															value = number_format(value, 1) + ' M';
														}else if(value > 99 && value < 999){
															value = value/1000;
															value = number_format(value, 1) + ' K';
														}else{
															 value = '';
														}
														return value;
													},
								                    padding: 20
								                },
								                gridLines: {
								                    drawTicks: false,
								                }

								            },
								        },

								        plugins: {
				                            datalabels: {
				                                display: false
				                            }
				                        }
								    }
								});
					    	}
						});
					}

					function amount_per_month_group(){
						var id = $('#id').val();
						var url = base_url + 'unit/opex-bar/' + id + '/' + year;

						var budget_date = [];
			    		var group = [];
			    		var amount = [];
			    		var color = [];
			    		var datasets = [];
						$.ajax({
					    	url: url,
					    	method: 'GET',
					    	success:function(response){
					    		var parse_data = JSON.parse(response);
					    		var total = [0,0,0,0,0,0,0,0,0,0,0,0];
					    		if(parse_data['result'] == 1){
					    			group = parse_data['group'];
					    			for(var x in parse_data['month']){
					    				budget_date.push(parse_data['month'][x]);
					    			}
					    			/*color = ["#99d815", "#8e5ea2", "#3cba9f"];*/
					    			var count = 0
					    			var month_tbl = '';
					    			

					    			for(var a in parse_data['group_amount']){
					    				color.push(parse_data['group_amount'][a]['color']);
					    				month_tbl += '<tr>';
					    				month_tbl += '<td>' + parse_data['group_amount'][a]['asset'] + '</td>';

					    				for(var b =0; b < 12; b++){
					    					var gl_amount = parse_data['group_amount'][a]['amount'][b];
					    					total[b] += parseFloat(gl_amount);
					    					month_tbl += '<td class="text-right">' + number_format(gl_amount) + '</td>';
					    				}

					    				month_tbl += '</tr>';

					    				parse_data['group_amount'][a]['amount'];
					    				var sets = {
					    					label: parse_data['group_amount'][a]['asset'],
					    					data : parse_data['group_amount'][a]['amount'],
					    					backgroundColor: color[count],
									        borderWidth: 1
					    				};
					    				count++;
					    				datasets.push(sets);
					    			}

					    			month_tbl += '<tr style="border-top: 2px solid black; font-size: 14px;">';
					    			month_tbl += '<td><strong>Total</strong></td>';
					    			month_tbl += '<td class="text-right"><strong>' + number_format(total[0]) + '</strong></td>';
					    			month_tbl += '<td class="text-right"><strong>' + number_format(total[1]) + '</strong></td>';
					    			month_tbl += '<td class="text-right"><strong>' + number_format(total[2]) + '</strong></td>';
					    			month_tbl += '<td class="text-right"><strong>' + number_format(total[3]) + '</strong></td>';
					    			month_tbl += '<td class="text-right"><strong>' + number_format(total[4]) + '</strong></td>';
					    			month_tbl += '<td class="text-right"><strong>' + number_format(total[5]) + '</strong></td>';
					    			month_tbl += '<td class="text-right"><strong>' + number_format(total[6]) + '</strong></td>';
					    			month_tbl += '<td class="text-right"><strong>' + number_format(total[7]) + '</strong></td>';
					    			month_tbl += '<td class="text-right"><strong>' + number_format(total[8]) + '</strong></td>';
					    			month_tbl += '<td class="text-right"><strong>' + number_format(total[9]) + '</strong></td>';
					    			month_tbl += '<td class="text-right"><strong>' + number_format(total[10]) + '</strong></td>';
					    			month_tbl += '<td class="text-right"><strong>' + number_format(total[11]) + '</strong></td>';
					    			month_tbl + '</tr>';

					    			$('#tbl-opex-monthly > tbody').empty();
					    			$('#tbl-opex-monthly > tbody').append(month_tbl);
						    	}else{
						    		console.log('Error please contact your administrator.');
						    	}

						    	if(typeof(month_group_chart) != 'undefined'){
									month_group_chart.destroy();
								}

								month_group_chart = new Chart(document.getElementById("bar-chart"), {
								    type: 'bar',
								    data: {
								    	labels: budget_date,
								      	datasets: datasets
								    },
								    options: {
    									responsive: true,
    									title: {
      										display: true,
      										position: "top",
      										text: "OPEX per GL and per month",
											fontSize: 18,
											fontColor: "#333"
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
									   		callbacks: {
									   			label: function(tooltipItem, data) {
										   			var index = tooltipItem.index;
										   			var datasetIndex = tooltipItem.datasetIndex;

								                    var value = number_format(data.datasets[datasetIndex].data[index], 2);
								                    var label = data.datasets[datasetIndex].label;
								                    return label + ': ' + value;
							                	}
									        }
									    },

										scales: {
											y: {
								                ticks: {
								                    fontColor: "rgba(0,0,0,0.7)",
								                    beginAtZero: true,
								                    userCallback: function(value, index, values) {
														
														if(value > 1000 && value < 1000000){
															value = value/1000;
															return value + ' K';
														}else if(value > 1000000){
															value = value/10000000;
															return value + ' M';
														}
														
													},
								                }
								            },
										},
										
										plugins: {
				                            datalabels: {
				                                display: false
				                            }
				                        }
					    			}
					    		});
							}
						});
					}

					/*function amount_per_gl_capex(){
						var url = base_url + 'admin/opex-donut-capex/' + id;
						$.ajax({
					    	url: url,
					    	method: 'GET',
					    	success:function(response){
					    		var parse_data = JSON.parse(response);
					    		var gl_group = [];
					    		var amount = [];
					    		var color = [];
					    		var total = 0;
					    		if(parse_data['result'] == 1){
					    			for(var a in parse_data['info']){
					    				total += parseInt(parse_data['info'][a]['opex_ny']);
										amount.push(parseInt(parse_data['info'][a]['opex_ny']).toFixed(2));
										gl_group.push(parse_data['info'][a]['asset_group']);
									}
						    	}else{
						    		console.log('Error please contact your administrator.');
						    	}
						    	console.log('Group: ' + total);

						    	if(typeof(gl_chart2) != 'undefined'){
									gl_chart.destroy();
								}

								gl_chart2 = new Chart(document.getElementById("doughnut-chart2"), {
								    type: 'doughnut',
								    data: {
								    	labels: gl_group,
								      	datasets: [
								        {
								        	label: "Asset Group",
								        	backgroundColor: ["#99d815", "#8e5ea2","#3cba9f","#e8c3b9","#c45850"],
								        	data: amount
								        }
								      	]
								    },
								    options: {
								    	title: {
								        	display: true,
								        	position: "top",
								        	text: 'OPEX per Asset Group',
								        	fontSize: 17
								        	
								      	},
									    legend: {
								            display: true,
								            position: "bottom",
								            labels: {
								                fontColor: "#333",
								                fontSize: 11
								            }
								        },
								    }
								});
					    	}
						});
					}

					function amount_per_month_capex(){
						var url = base_url + 'admin/opex-line-capex/' + id;
						$.ajax({
					    	url: url,
					    	method: 'GET',
					    	success:function(response){
					    		var parse_data = JSON.parse(response);
					    		var budget_date = [];
					    		var amount = [];
					    		var color = [];
					    		var total = 0;
					    		if(parse_data['result'] == 1){
					    			for(var a in parse_data['info']){
					    				total += parseInt(parse_data['info'][a]['amount']); 
										amount.push(parseInt(parse_data['info'][a]['amount']).toFixed(2));
										budget_date.push(parse_data['info'][a]['month']);
									}
						    	}else{
						    		console.log('Error please contact your administrator.');
						    	}

						    	console.log('Monthly: ' + total);

						    	if(typeof(month_chart2) != 'undefined'){
									month_chart2.destroy();
								}

								month_chart2 = new Chart(document.getElementById("line-chart2"), {
								    type: 'line',
								    data: {
								    	labels: budget_date,
								      	datasets: [
								        {
								        	label: "Monthly Cost",
								        	backgroundColor: ["#99d815", "#8e5ea2","#3cba9f","#e8c3b9","#c45850"],
								        	data: amount,
								        	fill: true,
								        	backgroundColor: 'rgb(0,155,106,0.5)',
								        	pointRadius: 5,
								        	pointBackgroundColor: "#1eb0bb",
								        }
								      	]
								    },
								    options: {
								    	title: {
								        	display: true,
								        	position: "top",
								        	text: 'OPEX per month based on CAPEX',
								        	fontSize: 17
								        	
								      	},
									    legend: {
								            display: true,
								            position: "bottom",
								            labels: {
								                fontColor: "#333",
								                fontSize: 11
								            }
								        },
								    }
								});
					    	}
						});
					}*/
				});
			</script>