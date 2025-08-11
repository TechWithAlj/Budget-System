			
			<div class="col-lg-12" id="content">
				<div id="breadcrumb-div">
					<ul class="breadcrumb">
					    <li><a href="<?=base_url('business-center')?>"><span class="glyphicon glyphicon-home"></span>&nbsp;Dashboard</a></li>
					    <li class="active">Sales Info</li>
					</ul>
				</div>
				<?php
					if($this->session->flashdata('message') != "" ){
						echo $this->session->flashdata('message');
					}
				?>

				<div class="row">
					<div class="col-lg-3">
						<label class="data-info">Business Center: <?=$bc_name?></label>
					</div>

					<div class="col-lg-2">
						<label>Budget Year:</label>
						<div class="form-group">
							<div class="date">
		                        <div class="input-group input-append date" id="sales-trans-year">
		                            <input type="text" name="month" id="sales-year" class="form-control input-sm" placeholder="Pick year" value="<?=$year?>">
		                            <span class="input-group-addon add-on"><span class="glyphicon glyphicon-calendar"></span></span>
		                        </div>
		                    </div>
						</div>
					</div>

					<div class="col-lg-7">
						<div class="text-right">
							<a href="<?=base_url('business-center/download-sales/' . $year)?>" target="_blank"><span class="glyphicon glyphicon-download-alt"></span>&nbsp;&nbsp; Download Sales</a>
						</div>
					</div>
				</div>

				<div id="add-btn">
					<?php if($budget_status == 1):?>
					
						<a href="" class="btn btn-info btn-xs" data-toggle="modal" data-backdrop="static" data-target="#modal-budget">+ Upload Budget</a>&nbsp;&nbsp;

						<div id="modal-budget" class="modal fade modal-confirm" role="dialog">
							<div class="modal-dialog modal-sm">
							    <div class="modal-content">
							    	<div class="modal-header">
							        	<button type="button" class="close" data-dismiss="modal">&times;</button>
							       		<strong>Upload Sales</strong>
							      	</div>
							      	<div class="modal-body">
							        	<form method="POST" action="<?=base_url('business-center/upload-sales')?>" enctype="multipart/form-data" id="add-outlet">

							        		<input type="hidden" name="year" value="<?=$year?>">
							        		
							        		<div class="form-group">
							        			<label>Choose file:</label>
							        			<input type="file" name="budget_file">
							        		</div>


							        		<div class="btn-update">
							        			<button type="submit" class="btn btn-info btn-sm pull-right">Upload</button><br>
							        		</div>
							        	</form>
							      	</div>
							    </div>
							</div>
						</div>
					<?php endif;?>

					
				</div>

				<ul class="nav nav-tabs">
   					<li class="active"><a data-toggle="tab" class="tab-letter" href="#budgeted-tab">Budgeted</a></li>
   					<li><a data-toggle="tab" href="#unbudgeted-tab" class="opex-graph-letter">Unbudgeted</a></li>
				    <li><a data-toggle="tab" href="#sales-dashboard-tab" class="opex-graph-letter">Dashboard</a></li>
  				</ul>
				<div class="tab-content">
  					<!-- <input type="hidden" value="<?=$cost_center?>" id="id"> -->
    				<div id="budgeted-tab" class="tab-pane fade in active"><br/ >
						<table class="table table-hover" id="tbl-category">
							<thead>
								<tr>
									<th>Outlet Code</th>
									<th>Outlet</th>
									<th>Brand</th>
									<th>Region</th>
									<th>BC</th>
									<th>Type</th>
									<th>Action</th>
								</tr>
							</thead>
							<tbody>

								<?php
									foreach($budgeted_outlet as $row):
								?>
								
								<tr>
									<td><?=$row->ifs_code?></td>
									<td><?=$row->outlet_name?></td>
									<td><?=$row->brand_name?></td>
									<td><?=$row->region_name?></td>
									<td><?=$row->bc_name?></td>
									<td><?=$row->outlet_type_name?></td>
									<td><a href="<?=base_url('business-center/sales-view/' . encode($row->sales_id))?>" class="btn btn-xs btn-success" data-id="<?=encode($row->outlet_id)?>">View</a>&nbsp;&nbsp;
										<?php if($budget_status == 1):?>
										
										<a data-id="<?=encode($row->sales_id)?>" class="btn btn-danger btn-xs cancel-sales-btn">Cancel</a></td>

										<?php endif;?>
								</tr>

								<?php endforeach;?>

							</tbody>
						</table>

						<div id="modal-cancel-sales" class="modal fade" role="dialog">
							<div class="modal-dialog modal-sm">
							    <div class="modal-content">
							    	<div class="modal-header">
							        	<button type="button" class="close" data-dismiss="modal">&times;</button>
							       		<strong>Cancel Outlet</strong>
							      	</div>
							      	<div class="modal-body">
							      		<form method="POST" action="<?=base_url('business-center/cancel-sales')?>" enctype="multipart/form-data" id="cancel-sales">
							      			<input type="hidden" name="id" id="id">
								        	<div id="modal-msg" class="text-center">
								        		<label>Are you sure to cancel this outlet?</label>
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

					<div id="unbudgeted-tab" class="tab-pane fade"><br/ >
						<div id="add-btn">
							<input type="hidden" id="bc_id" name="id" value="<?=$id?>">
							<div class="col-lg-2">
								<select id="brand-templates" class="form-control input-sm">

									<?php
										$count = 0;
										$first = '';
										foreach($brand as $row_brand):
											if($count == 0){
												$first = encode($row_brand->brand_id);
											}

											$count++;
									?>

										<option value="<?=encode($row_brand->brand_id)?>"><?=$row_brand->brand_name?></option>

									<?php endforeach;?>

								</select>
							</div>
							<a href="<?=base_url('business-center/download-sales-templates/' . $id . '/' . $first . '/' . $year)?>" id="templates-link" class="btn btn-info" target="_blank"><span class="fa fa-download"></span>&nbsp;&nbsp;Download Templates</a>
						</div>
						<table class="table table-hover" id="tbl-unbudgeted">
							<thead>
								<tr>
									<th>IFS Code</th>
									<th>Outlet</th>
									<th>Brand Type</th>
									<th>Region</th>
									<th>BC</th>
									<th>Status</th>
									<th>Action</th>
								</tr>
							</thead>
							<tbody>

								<?php
									foreach($unbudgeted_outlet as $row):
								?>
								
								<tr>
									<td><?=$row->ifs_code?></td>
									<td><?=$row->outlet_name?></td>
									<td><?=$row->brand_name?></td>
									<td><?=$row->region_name?></td>
									<td><?=$row->bc_name?></td>
									<td><?=$row->outlet_type_name?></td>
									<td><a href="<?=base_url('business-center/outlet-budget/' . encode($row->outlet_id) . '/' . $year)?>" class="btn btn-xs btn-info" data-id="<?=encode($row->outlet_id)?>">Budget</a></td>
								</tr>

								<?php endforeach;?>

							</tbody>
						</table>
					</div>

					<div id="sales-dashboard-tab" class="tab-pane fade"><br/ >
						<div class="row">
							<div class="col-lg-6" id="cost-chart">
								
								<div class="text-center"><h3><strong>Sales Unit</strong></h3></div>

								<canvas id="sales-unit-chart" width="800"></canvas>
							</div>

							<div class="col-lg-6" id="cost-chart">

								<div class="text-center"><h3><strong>Net Sales</strong></h3></div>

								<canvas id="net-sales-chart" width="800"></canvas>
							</div>

							<br /><br />
							<div class="col-lg-12" id="cost-chart">

								<div class="text-center"><h3><strong>Volume</strong></h3></div>

								<table class="table table-hover table-bordered table-striped" id="volume-tbl">
                                    <thead>
                                        <tr>
                                            <th rowspan="2">SALES VOLUME COMPARISON</th>
                                            <th class="text-center" colspan="7">UNITS</th>
                                            <th class="text-center" colspan="7">HEADS</th>
                                        </tr>

                                        <tr>
                                            <th><?=$year?></th>
                                            <th><?=$year - 1?></th>
                                            <th><?=$year - 2?></th>
                                            <th><?=$year?> vs <?=$year - 1?></th>
                                            <th>%</th>
                                            <th><?=$year?> vs <?=$year - 2?></th>
                                            <th>%</th>
                                            <th><?=$year?></th>
                                            <th><?=$year - 1?></th>
                                            <th><?=$year - 2?></th>
                                            <th><?=$year?> vs <?=$year - 1?></th>
                                            <th>%</th>
                                            <th><?=$year?> vs <?=$year - 2?></th>
                                            <th>%</th>
                                        </tr>
                                    </thead>
                                    <tbody>

                                    </tbody>
                                </table>
							</div>
						</div>

						<div class="row">
							<div class="col-lg-12" id="cost-chart">
								<canvas id="bar-chart" height="50" width="200"></canvas>
							</div>
						</div>
					</div>
				</div>
			</div>

			<script type="text/javascript">
				$(document).ready(function(){
					var base_url = $('#base_url').val();
					var year = $("#sales-year").val();

					Chart.register(ChartDataLabels);

					sales_dashboard();

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


					function sales_dashboard(){
						var url = base_url + 'business-center/get-sales-info-net-sales/';
						$.ajax({
					    	url: url,
					    	data:{year:year},
					    	method: 'POST',
					    	success:function(response){
					    		var parse_data = JSON.parse(response);
					    		var net_sales = parseInt(parse_data['net_sales']);
					    		var net_sales1 = parseInt(parse_data['net_sales1']);
					    		var net_sales2 = parseInt(parse_data['net_sales2']);

					    		var sales_unit = parseInt(parse_data['sales_unit']);
					    		var sales_unit1 = parseInt(parse_data['sales_unit1']);
					    		var sales_unit2 = parseInt(parse_data['sales_unit2']);

					    		var volume_tbl = parse_data['volume_tbl'];

					    		$('#volume-tbl > tbody').empty();
					    		$('#volume-tbl > tbody').append(volume_tbl);

						    	if(typeof(net_sales_chart) != 'undefined'){
									net_sales_chart.destroy();
								}

								net_sales = new Chart(document.getElementById("net-sales-chart"), {
								    type: 'line',
								    data: {
								    	labels: ['<?=$year?>', '<?=$year-1?>', '<?=$year-2?>'],
								      	datasets: [{
								      		label: "",
				                            fill: false,
				                            tension: 0.1,
				                            borderColor: '#0b7fab',
				          					data: [net_sales, net_sales1, net_sales2],
				                            datalabels: {
				                                align: 'end',
				                                anchor: 'end'
				                            },
				                            backgroundColor: '#0b7fab',
				                            borderColor: '#0b7fab',
								      	}],
								    },
								    options: {
										responsive: true,
										title: {
												display: true,
												position: "top",
												text: "",
											fontSize: 16,
											fontColor: "#9e9e9e"
										},
										legend: {
											display: false,
										},

										tooltips: {
									   		callbacks: {
									   			label: function(tooltipItem, data) {
										   			var index = tooltipItem.index;
										   			var datasetIndex = tooltipItem.datasetIndex;

								                    var value = number_format(data.datasets[datasetIndex].data[index]);
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
				                                        
				                                        /*if(value >= 1000 && value <= 999999){
				                                            value = value/1000;
				                                            value = number_format(value) + ' K';
				                                        }else if(value >= 1000000 && value <= 999999999){
				                                            value = value/1000000;
				                                            value = number_format(value, 1) + ' M';
				                                        }else if(value >= 1000000000 && value <= 999999999999){
				                                            value = value/1000000000;
				                                            value = number_format(value, 1) + ' B';

				                                        }else if(value >= 1000000000000 && value <= 999999999999999){
				                                            value = value/1000000000000;
				                                            value = number_format(value, 1) + ' T';
				                                        }else{
				                                             value = '';
				                                        }*/

				                                        value = parseInt(value)/1000000;
				                                        value = number_format(value);
				                                        return value;
				                                    },
				                                    padding: 20
				                                }
				                            }
				                        },

										plugins: {
										    labels: {
												render: 'value',
												textShadow: true,
												render: function (args) {
													var value = args.value;
													/*if(value >= 1000 && value <= 999999){
				                                        value = value/1000;
				                                        value = number_format(value) + ' K';
				                                    }else if(value >= 1000000 && value <= 999999999){
				                                        value = value/1000000;
				                                        value = number_format(value, 1) + ' M';
				                                    }else if(value >= 1000000000 && value <= 999999999999){
				                                        value = value/1000000000;
				                                        value = number_format(value, 1) + ' B';

				                                    }else if(value >= 1000000000000 && value <= 999999999999999){
				                                        value = value/1000000000000;
				                                        value = number_format(value, 1) + ' T';
				                                    }else{
				                                         value = '';
				                                    }*/

				                                    value = value/1000000;
				                                    value = number_format(value);
				                                    return value;
												},
											},

				                            datalabels: {
				                                backgroundColor: function(context) {
				                                    return context.dataset.backgroundColor;
				                                },
				                            
				                                borderRadius: 4,
				                                color: 'white',
				                                font: {
				                                    weight: 'bold'
				                                },
				                                formatter: function(value) {
				                                    return number_format(Math.round(value / 1000000))
				                                },
				                                padding: 6
				                            }
										}
					    			}
					    		});

					    		

						    	if(typeof(sales_unit_chart) != 'undefined'){
									sales_unit_chart.destroy();
								}

								total_sales = new Chart(document.getElementById("sales-unit-chart"), {
								    type: 'line',
								    data: {
								    	labels: ['<?=$year?>', '<?=$year - 1?>', '<?=$year - 2?>'],
								      	datasets: [{
								      		label: "",
				                            fill: false,
				                            tension: 0.1,
				          					data: [sales_unit, sales_unit1, sales_unit2],
				                            datalabels: {
				                                align: 'end',
				                                anchor: 'end'
				                            },
				                            backgroundColor: '#03c9a9',
				                            borderColor: '#03c9a9',
								      	}],
								    },
								    options: {
										responsive: true,
										title: {
											display: true,
											position: "top",
											text: "",
											fontSize: 16,
											fontColor: "#9e9e9e"
										},
										legend: {
											display: false,
										},

										tooltips: {
									   		callbacks: {
									   			label: function(tooltipItem, data) {
										   			var index = tooltipItem.index;
										   			var datasetIndex = tooltipItem.datasetIndex;

								                    var value = number_format(data.datasets[datasetIndex].data[index]);
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
				                                        value = parseInt(value)/1000000;
				                                        value = number_format(value);
														return value;
													},
								                    padding: 20
								                }
								            }
										},

				                        plugins: {
				                            labels: {
				                                render: 'value',
				                                textShadow: true,
				                                render: function (args) {
				                                    var value = args.value;

				                                    value = value/1000000;
				                                    value = number_format(value);
				                                    return value;
				                                },
				                            },

				                            datalabels: {
				                                backgroundColor: function(context) {
				                                    return context.dataset.backgroundColor;
				                                },
				                            
				                                borderRadius: 4,
				                                color: 'white',
				                                font: {
				                                    weight: 'bold'
				                                },
				                                formatter: function(value) {
				                                    return number_format(Math.round(value / 1000000))
				                                },
				                                padding: 6
				                            }
				                        }
					    			}
					    		});
					    	}
						});
					}
				});
			</script>