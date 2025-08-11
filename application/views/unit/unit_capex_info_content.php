			
			<div class="col-lg-12" id="content">
				<div id="breadcrumb-div">
					<ul class="breadcrumb">
					    <li><a href="<?=base_url('unit')?>"><span class="glyphicon glyphicon-home"></span>&nbsp;Dashboard</a></li>
					    <li class="active">CAPEX Info</li>
					</ul>
				</div>
				<?php
					if($this->session->flashdata('message') != "" ){
						echo $this->session->flashdata('message');
					}
				?>

				<div class="row">
					<div class="col-lg-3">
						<strong><label class="data-info">Business Center: <?=$cost_center_desc?></label></strong>
					</div>

					<div class="col-lg-2">
						<label>Budget Year:</label>
						<div class="form-group">
							<div class="date">
		                        <div class="input-group input-append date" id="capex-trans-year">
		                            <input type="text" name="month" id="capex-year" class="form-control input-sm" placeholder="Pick year" value="<?=$year?>">
		                            <span class="input-group-addon add-on"><span class="glyphicon glyphicon-calendar"></span></span>
		                        </div>
		                    </div>
						</div>
					</div>

					<div class="col-lg-7">
						<div class="text-right">
							<a href="<?=base_url('unit/download-capex/' . $year)?>" target="_blank"><span class="glyphicon glyphicon-download-alt"></span>&nbsp;&nbsp; Download CAPEX</a>
						</div>
					</div>

				</div>

				<ul class="nav nav-tabs">
   					<li class="active"><a data-toggle="tab" class="tab-letter" href="#capex-tab">CAPEX</a></li>
				    <li><a data-toggle="tab" href="#capex-graph-tab" class="capex-graph-letter">Graph</a></li>
  				</ul>

  				<div class="tab-content">
  					<input type="hidden" value="<?=$cost_center?>" id="id">
    				<div id="capex-tab" class="tab-pane fade in active">
    					<br>

    					<?php if($budget_status == 1):?>

	    					<div id="add-btn">
								<a href="<?=base_url('unit/transac-capex/' . $year)?>" class="btn btn-success btn-xs">+ Add Capex</a>
							</div>

						<?php endif;?>

						<table class="table table-hover table-bordered table-striped" id="tbl-capex-info">
							<thead>
								<tr style="font-size: 15px;">
									<th>Asset Group</th>
									<th>Cost Center Desc</th>
									<th>Cost Center Code</th>
									<th>Total</th>
									<th>Action</th>
								</tr>
							</thead>
							<tbody>

								<?php
									foreach($asset_group as $row):
								?>
								
								<tr>
									<td><?=$row->ag_name?></td>
									<td><?=$row->cost_center_desc?></td>
									<td><?=$row->cost_center_code?></td>
									<td></td>
									<td><a href="<?=base_url('unit/view-capex/' . encode($row->ag_trans_id))?>" class="btn btn-success btn-xs">View</a>&nbsp;&nbsp;

									<?php if($budget_status):?>

										<a data-id="<?=encode($row->ag_trans_id)?>" class="btn btn-danger btn-xs cancel-capex-btn">Cancel</a>

									<?php endif;?>

									</td>
								</tr>

								<?php endforeach;?>

							</tbody>
						</table>

						<div id="modal-cancel-capex" class="modal fade" role="dialog">
							<div class="modal-dialog modal-sm">
							    <div class="modal-content">
							    	<div class="modal-header">
							        	<button type="button" class="close" data-dismiss="modal">&times;</button>
							       		<strong>Cancel CAPEX</strong>
							      	</div>
							      	<div class="modal-body">
							      		<form method="POST" action="<?=base_url('unit/cancel-capex')?>" enctype="multipart/form-data" id="cancel-capex">
							      			<input type="hidden" name="id" id="id">
								        	<div id="modal-msg" class="text-center">
								        		<label>Are you sure to cancel this CAPEX?</label>
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

					<div id="capex-graph-tab" class="tab-pane fade">
						<div class="row">
							<div class="col-md-6" id="cost-chart">
								<canvas id="doughnut-chart"></canvas>
							</div>

							<div class="col-md-6" id="cost-chart">
								<canvas id="line-chart" width="800"></canvas>
							</div>
						</div>

						<div class="row">
							<div class="col-lg-12" id="cost-chart">
								<canvas id="bar-chart" height="50" width="200"></canvas>
								
							</div>
						</div>

						<div class="row"><br /><br /><br /><br /><br />
							<div id="" class="col-lg-offset-1 col-lg-10">
								<table class="table">
									<thead>
										<tr>
											<td colspan="8" class="text-center"><strong>CAPEX <?=$year?> Summary</strong></td>
										</tr>
										<tr>
											<td>ASSET GROUP</td>
											<td class="text-center"><?=$year?></td>
											<td class="text-center"><?=$year - 1?></td>
											<td class="text-center"><?=$year - 2?></td>
											<td class="text-center"><?=$year ?> vs <?=$year - 1?></td>
											<td class="text-center">%</td>
											<td class="text-center"><?=$year ?> vs <?=$year - 2?></td>
											<td class="text-center">%</td>
										</tr>
									</thead>

									<tbody>
										<?php
										 	$capex_name = '';
										 	$ag_total = 0;
										 	$ag_total1 = 0;
										 	$ag_total2 = 0;
										 	$grand_total = 0;
										 	$grand_total1 = 0;
										 	$grand_total2 = 0;
										 	$count = 0;
										 	$capex_total = 0;
										 	$capex_total1 = 0;
										 	$capex_total2 = 0;

										 	$data_details = '';
											foreach($capex_asset as $row_capex):
												$grand_total += $row_capex->capex;

												$capex_total = $row_capex->capex;
												$capex_total1 = $row_capex->capex1;
												$capex_total2 = $row_capex->capex2;
												$capex_dif1 = ($capex_total - $capex_total1) * -1;
												$capex_dif2 = ($capex_total - $capex_total2) * -1;
												$capex_per1 = $capex_total1 != 0 ? ($capex_dif1 / $capex_total1) * 100 : 0;
												$capex_per2 = $capex_total2 != 0 ? ($capex_dif2 / $capex_total2) * 100 : 0;

												$grand_total1 += $capex_total1;
												$grand_total2 += $capex_total2;

												/*$data_details .= '<tr>';
												$data_details .= '<td style="border-bottom: 1px solid black;" colspan="4" class="text-right"><strong>Total: </strong></td>';
												$data_details .= '<td style="border-bottom: 1px solid black;" class="text-right"><strong>' . number_format($capex_total) . '</strong></td>';
												$data_details .= '</tr>';
												$data_details .= '<tr>';
												$data_details .= '<td><strong>' . $row_capex->ag_name . '</strong></td>';*/

														echo '<tr>';
														echo '<td>' . $row_capex->ag_name . '</td>';
														echo '<td class="text-right">' . number_format($capex_total) . '</td>';
														echo '<td class="text-right">' . number_format($capex_total1) . '</td>';
														echo '<td class="text-right">' . number_format($capex_total2) . '</td>';
														echo '<td class="text-right">' . number_format($capex_dif1) . '</td>';
														echo '<td class="text-right">' . number_format($capex_per1) . '%</td>';
														echo '<td class="text-right">' . number_format($capex_dif2) . '</td>';
														echo '<td class="text-right">' . number_format($capex_per2) . '%</td>';
														echo '</tr>';
													/*
														$ag_total = 0;
														$ag_total += ($row_capex->total_qty * $row_capex->capex_price);*/
													/*}else{
														$ag_total += ($row_capex->total_qty * $row_capex->capex_price);
														$data_details .= '<tr>';
														$data_details .= '<td><strong>' . $row_capex->ag_name . '</strong></td>';
													}*/

													/*$ag_total += ($row_capex->total_qty * $row_capex->capex_price);*/
												/*}else{
													$ag_total += ($row_capex->total_qty * $row_capex->capex_price);

													$data_details .= '<tr">';
													$data_details .= '<td></td>';
												}
												$count++;

												$data_details .= '<td>' . $row_capex->asg_name . '</td>';
												$data_details .= '<td>' . $row_capex->total_qty . '</td>';
												$data_details .= '<td class="text-right">' . number_format($row_capex->capex_price) . '</td>';
												$data_details .= '<td class="text-right">' . number_format($row_capex->total_qty * $row_capex->capex_price) . '</td>';
												
												
												$data_details .= '</tr>';*/
											endforeach;?>
										
										<?php
											if(count($capex_asset) > 0){
												$capex_total = $ag_total;
												$capex_total1 = $row_capex->capex1;
												$capex_total2 = $row_capex->capex2;
											}

											$capex_dif1 = ($capex_total - $capex_total1) * -1;
											$capex_dif2 = ($capex_total - $capex_total2) * -1;
											$capex_per1 = $capex_total1 != 0 ? ($capex_dif1 / $capex_total1) * 100 : 0;
											$capex_per2 = $capex_total2 != 0 ? ($capex_dif2 / $capex_total2) * 100 : 0;


											/*$grand_total1 += $capex_total1;
											$grand_total2 += $capex_total2;*/

											$grand_total_dif1 = ($grand_total - $grand_total1) * -1;
											$grand_total_dif2 = ($grand_total - $grand_total2) * -1;
											$grand_total_per1 = ($grand_total1 != 0 ? ($grand_total_dif1/$grand_total1)  * 100 : 0);
											$grand_total_per2 = ($grand_total2 != 0 ? ($grand_total_dif2/$grand_total2) * 100 : 0);

											/*echo '<tr>';
											echo '<td>' . $capex_name . '</td>';
											echo '<td class="text-right">' . number_format($ag_total) . '</td>';
											echo '<td class="text-right">' . number_format($capex_total1) . '</td>';
											echo '<td class="text-right">' . number_format($capex_total2) . '</td>';
											echo '<td class="text-right">' . number_format($capex_dif1) . '</td>';
											echo '<td class="text-right">' . number_format($capex_per1) . '%</td>';
											echo '<td class="text-right">' . number_format($capex_dif2) . '</td>';
											echo '<td class="text-right">' . number_format($capex_per2) . '%</td>';
											echo '</tr>';*/

											echo '<tr>';
											echo '<td class="text-right" style="border-top: 1px solid black;"><strong>Total:</strong></td>';
											echo '<td class="text-right" style="border-top: 1px solid black;"><strong>' . number_format($grand_total) . '</strong></td>';
											echo '<td class="text-right" style="border-top: 1px solid black;"><strong>' . number_format($grand_total1) . '</strong></td>';
											echo '<td class="text-right" style="border-top: 1px solid black;"><strong>' . number_format($grand_total2) . '</strong></td>';
											echo '<td class="text-right" style="border-top: 1px solid black;"><strong>' . number_format($grand_total_dif1) . '</strong></td>';
											echo '<td class="text-right" style="border-top: 1px solid black;"><strong>' . number_format($grand_total_per1) . '</strong>%</td>';
											echo '<td class="text-right" style="border-top: 1px solid black;"><strong>' . number_format($grand_total_dif2) . '</strong></td>';
											echo '<td class="text-right" style="border-top: 1px solid black;"><strong>' . number_format($grand_total_per2) . '</strong>%</td>';
											echo '</tr>';

											/*$data_details .= '<tr>';
											$data_details .= '<td style="border-bottom: 1px solid black;" colspan="4" class="text-right"><strong>Total: </strong></td>
											<td style="border-bottom: 1px solid black;" class="text-right"><strong>' . number_format($ag_total) . '</strong></td>';
											$data_details .= '</tr>';

											$data_details .= '<tr>';
											$data_details .= '<td style="border-bottom: 1px solid black;" colspan="4" class=""><strong>Grand Total: </strong></td>';
											$data_details .= '<td style="border-bottom: 1px solid black;" class="text-right"><strong>' . number_format($grand_total) . '</strong></td>';
											$data_details .= '</tr>';*/
										?>
									</tbody>
								</table>
								<br /> <br /><br /> <br />
							</div>

							<div id="" class="col-lg-offset-1 col-lg-10"><br /> <br/>
								<table class="table table-hover table-bordered table-striped">
									<thead>
										<tr>
											<td colspan="7" class="text-center"><span style="font-size: 18px;">CAPEX <?=$year?> Detailed</span></td>
										</tr>
										<tr style="font-size: 15px;">
											<td>ASSET GROUP</td>
											<td>ITEM DESCRIPTION</td>
											<td>QTY</td>
											<td class="text-center">COST</td>
											<td class="text-center">AMOUNT</td>
										</tr>
									</thead>

									<tbody>
										
										<?php
										 	$capex_name = '';
										 	$ag_total = 0;
										 	$grand_total = 0;
										 	$count = 0;
											foreach($capex_details as $row_capex):
												$grand_total += ($row_capex->total_qty * $row_capex->capex_price);
												if($row_capex->capex_price > 0):
													if($capex_name != $row_capex->ag_name){
														$capex_name = $row_capex->ag_name;

														if($count > 0){

															echo '<tr>';
															echo '<td style="border-bottom: 1px solid black;" colspan="4" class="text-right"><strong>Total: </strong></td>';
															echo '<td style="border-bottom: 1px solid black;" class="text-right"><strong>' . number_format($ag_total) . '</strong></td>';
															echo '</tr>';
															echo '<tr>';
															echo '<td><strong>' . $row_capex->ag_name . '</strong></td>';

															$ag_total = 0;
														}else{
															echo '<tr>';
															echo '<td><strong>' . $row_capex->ag_name . '</strong></td>';
														}

														$ag_total += ($row_capex->total_qty * $row_capex->capex_price);
													}else{
														$ag_total += ($row_capex->total_qty * $row_capex->capex_price);
														echo '<tr">';
														echo '<td></td>';
													}
												
												$count++;

										?>

												<td><?=$row_capex->asg_name?></td>
												<td class="text-right"><?=number_format($row_capex->total_qty)?></td>
												<td class="text-right"><?=number_format($row_capex->capex_price)?></td>
												<td class="text-right"><?=number_format($row_capex->total_qty * $row_capex->capex_price, 2)?></td>
											</tr>
										
										<?php
											endif;
										endforeach;
										?>
										
										<tr>
											<td style="border-bottom: 1px solid black;" colspan="4" class="text-right"><strong>Total: </strong></td>
											<td style="border-bottom: 1px solid black;" class="text-right"><strong><?=number_format($ag_total)?></strong></td>
										</tr>

										<tr>
											<td style="border-bottom: 1px solid black; font-size: 18px;" colspan="4" class=""><strong>Grand Total: </strong></td>
											<td style="border-bottom: 1px solid black; font-size: 18px;" class="text-right"><strong><?=number_format($grand_total)?></strong></td>
										</tr>

									</tbody>
								</table>
							</div>

							<div id="capex-table" class="col-lg-12">
								<table class="table table-hover table-bordered table-striped" id="tbl-capex-monthly">
									<thead>
										<tr>
											<td colspan="13" class="text-center"><span style="font-size: 18px;">Monthly CAPEX <?=$year?></span></td>
										</tr>
										<tr style="font-size: 15px;">
											<td>ASSET GROUP</td>
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
					var year = $("#capex-year").val();

					Chart.register(ChartDataLabels);

					amount_per_group();
					amount_per_month();

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

					function amount_per_group(){
						var id = $('#id').val();
						var url = base_url + 'unit/capex-donut/' + id + '/' + year;
						$.ajax({
					    	url: url,
					    	method: 'GET',
					    	success:function(response){
					    		var parse_data = JSON.parse(response);
					    		var asset_group = [];
					    		var amount = [];
					    		var color = [];
					    		if(parse_data['result'] == 1){
					    			for(var a in parse_data['info']){
										amount.push(parseInt(parse_data['info'][a]['amount']).toFixed(2));
										asset_group.push(parse_data['info'][a]['asset_group']);
										color.push(parse_data['info'][a]['color']);
									}
						    	}else{
						    		console.log('Error please contact your administrator.');
						    	}

						    	if(typeof(group_chart) != 'undefined'){
									group_chart.destroy();
								}

								group_chart = new Chart(document.getElementById("doughnut-chart"), {
								    type: 'doughnut',
								    data: {
								    	labels: asset_group,
								      	datasets: [
								        {
								        	label: "Asset Group",
								        	backgroundColor: color,
								        	data: amount
								        }
								      	]
								    },
								    options: {
								    	title: {
								        	display: true,
								        	position: "top",
								        	text: 'CAPEX per Asset Group',
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
				                                display: function(context) {
				                                    var dataset = context.dataset;
				                                    var count = dataset.data.length;
				                                    var value = dataset.data[context.dataIndex];
				                                    return value > count * 1.5;
				                                },
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
						var id = $('#id').val();
						var url = base_url + 'unit/capex-line/' + id + '/' + year;
						$.ajax({
					    	url: url,
					    	method: 'GET',
					    	success:function(response){
					    		var parse_data = JSON.parse(response);
					    		var budget_date = [];
					    		var amount1 = [];
					    		var amount2 = [];
					    		var color = [];
					    		if(parse_data['result'] == 1){
					    			for(var a in parse_data['info']['first_data']){
										amount1.push(parseInt(parse_data['info']['first_data'][a]['amount']).toFixed(2));
										budget_date.push(parse_data['info']['first_data'][a]['budget_date']);
									}

									for(var b in parse_data['info']['second_data']){
										amount2.push(parseInt(parse_data['info']['second_data'][b]['amount']).toFixed(2));
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
								      	datasets: [{
								        	label: year + " Budget",
								        	data: amount1,
								        	fill: true,
								        	backgroundColor: 'rgb(0,155,106,0.5)',
								        	pointRadius: 5,
								        	pointBackgroundColor: "#1eb0bb",
								        }],

								        datalabels: {
			                                align: 'top',
			                                anchor: 'top'
			                            },

			                            backgroundColor: '#0b7fab',
                            			borderColor: '#0b7fab',
								    },
								    options: {
								    	title: {
								        	display: true,
								        	position: "top",
								        	text: 'CAPEX per month',
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
								                    callback: function(value, index, values) {
														
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
					amount_per_month_group();
					function amount_per_month_group(){
						var id = $('#id').val();
						var url = base_url + 'unit/capex-bar/' + id + '/' + year;

						var budget_date = [];
			    		var group = [];
			    		var amount = [];
			    		var color = [];
			    		var datasets = [];
			    		var month_tbl = '';
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

					    			for(var y in parse_data['color']){
					    				color.push(parse_data['color'][y]);
					    			}

					    			var count = 0
					    			for(var a in parse_data['group_amount']){
					    				var asset_name = parse_data['group_amount'][a]['asset'];

					    				month_tbl += '<tr>';
					    				month_tbl += '<td>' + asset_name + '</td>';

					    				for(var b =0; b < 12; b++){
					    					var asset_amount = parse_data['group_amount'][a]['amount'][b];
					    					total[b] += parseFloat(asset_amount);
					    					month_tbl += '<td class="text-right">' + number_format(asset_amount) + '</td>';
					    				}

					    				month_tbl += '</tr>';
					    				
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
					    			month_tbl += '<td><strong>' + number_format(total[0]) + '</strong></td>';
					    			month_tbl += '<td><strong>' + number_format(total[1]) + '</strong></td>';
					    			month_tbl += '<td><strong>' + number_format(total[2]) + '</strong></td>';
					    			month_tbl += '<td><strong>' + number_format(total[3]) + '</strong></td>';
					    			month_tbl += '<td><strong>' + number_format(total[4]) + '</strong></td>';
					    			month_tbl += '<td><strong>' + number_format(total[5]) + '</strong></td>';
					    			month_tbl += '<td><strong>' + number_format(total[6]) + '</strong></td>';
					    			month_tbl += '<td><strong>' + number_format(total[7]) + '</strong></td>';
					    			month_tbl += '<td><strong>' + number_format(total[8]) + '</strong></td>';
					    			month_tbl += '<td><strong>' + number_format(total[9]) + '</strong></td>';
					    			month_tbl += '<td><strong>' + number_format(total[10]) + '</strong></td>';
					    			month_tbl += '<td><strong>' + number_format(total[11]) + '</strong></td>';
					    			month_tbl + '</tr>';

					    			$('#tbl-capex-monthly > tbody').empty();
					    			$('#tbl-capex-monthly > tbody').append(month_tbl);
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
      										text: "CAPEX per group and month",
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
											x: {
							                    barPercentage: 0.4,
							                    categoryPercentage: 1
							                },
							                y: {
								                ticks: {
								                    fontColor: "rgba(0,0,0,0.7)",
								                    beginAtZero: true,
								                    callback: function(value, index, values) {
														
														if(value > 1000 && value < 1000000){
															value = value/1000;
															value = number_format(value) + 'K';
														}else if(value >= 1000000 && value < 100000000){
															value = value/1000000;
															value = number_format(value, 1) + 'M';
														}else if(value > 99 && value < 999){
															value = value/1000;
															value = number_format(value, 1) + 'K';
														}else{
															 value = '';
														}
														return value;
													},
								                    padding: 20
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
				});
			</script>