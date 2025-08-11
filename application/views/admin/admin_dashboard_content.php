			
			<div class="col-lg-12" id="content">
				<div id="breadcrumb-div">
					<ul class="breadcrumb">
					    <li class="active"><span class="glyphicon glyphicon-home"></span>&nbsp;Dashboard</li>
					</ul>
				</div>

				<?php
					if($this->session->flashdata('message') != "" ){
						echo $this->session->flashdata('message');
					}
				?>

                <div class="row">
                    <!-- <div class="col-lg-2">
                        <div class="date">
                            <div class="input-group input-append date" id="alw-trans-year">
                                <input type="text" name="month" id="alw-year" class="form-control input-sm" placeholder="Pick year" value="<?=$year?>">
                                <span class="input-group-addon add-on"><span class="glyphicon glyphicon-calendar"></span></span>
                            </div>
                        </div>
                    </div> -->

                    <div class="col-lg-12 text-right">
                        <a href="<?=base_url('admin/download-pdf/' . $year)?>" target="_blank"><span class="glyphicon glyphicon-download-alt"></span>&nbsp;&nbsp; Download PDF</a>
                    </div>
                </div>

                <br /><br />

				<ul class="nav nav-tabs">
   					<li class="active"><a data-toggle="tab" class="tab-letter" href="#executive-tab">Executive Summary</a></li>
                    <li><a data-toggle="tab" class="tab-letter" href="#harvested-birds-tab">Harvestable Birds</a></li>
                    <li><a data-toggle="tab" class="tab-letter" href="#industry-tab">Industry Update</a></li>
				    <li><a data-toggle="tab" href="#volume-tab" class="capex-graph-letter">Volume</a></li>
				    <li><a data-toggle="tab" href="#price-tab" class="capex-graph-letter">Price Assumption</a></li>
                    <!-- <li><a data-toggle="tab" href="#netsales-tab" class="capex-graph-letter">Net Sales</a></li> -->
				    <li><a data-toggle="tab" href="#opex-bc-tab" class="capex-graph-letter">OPEX (BC)</a></li>
                    <!-- <li><a data-toggle="tab" href="#opex-dept-tab" class="capex-graph-letter">OPEX (BC & Dept)</a></li> -->
				    <li><a data-toggle="tab" href="#opex-support-tab" class="capex-graph-letter">OPEX (SC)</a></li>
                    <li><a data-toggle="tab" href="#opex-pnl-tab" class="capex-graph-letter">PNL</a></li>
  				</ul>

  				<div class="tab-content">
    				<div id="executive-tab" class="tab-pane fade in active">
    					<div class="row">
    						<div class="col-lg-12 dashboard-row">
    							<div class="col-lg-4">
    								<div class="dashboard-label text-center">
    									<label>Harvestable Birds</label>
    									<canvas id="harvestable-birds-chart" height="150px" width="200px"></canvas>
    									<label class="chart-remarks"><strong><?=$harvest_report?></strong></label>
    								</div>
    							</div>

    							<div class="col-lg-4">
    								<div class="dashboard-label text-center">
    									<label>Total Sales</label>
    									<canvas id="total-sales-chart" height="150px" width="200px"></canvas>
    									<label class="chart-remarks"><strong><?=$sales_unit_report?></strong></label>
    								</div>
    							</div>

    							<div class="col-lg-4">
    								<div class="dashboard-label text-center">
    									<label>Net Sales</label>
    									<canvas id="net-sales-chart" height="150px" width="200px"></canvas>
    									<label class="chart-remarks"><strong><?=$net_sales_report?></strong></label>
    								</div>
    							</div>
    						</div>

    						<div class="col-lg-12 dashboard-row">
    							<div class="dashboard-label text-center">
    								<label><strong>% Sales Mix</strong></label>
    							</div>
    							<div class="col-lg-6">
    								<div class="dashboard-label text-center">
    									<label>2020 % Sales Mix</label>
    									<canvas id="sales-mix-chart" height="145px"></canvas>
    								</div>
    							</div>

    							<div class="col-lg-6">
    								<div class="dashboard-label text-center">
    									<label>Sales Mix Comparative</label>
    									<table class="table table-hover table-bordered table-striped" id="sales-mix-tbl">
                                            <thead>
                                                <tr>
                                                    <th>SEGMENT</th>
                                                    <th class="text-center"><?=$year?> BUDGET</th>
                                                    <th class="text-center"><?=$year - 1?> YEE</th>
                                                    <th class="text-center"><?=$year - 2?></th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                
                                            </tbody>
                                        </table>
    								</div>
    							</div>
    						</div>

    						<div class="col-lg-12 dashboard-row">
    							<div class="col-lg-6">
    								<div class="dashboard-label text-center">
    									<label>NOI Comparative</label>
    									<canvas id="noi-chart" height="145px"></canvas>
                                        <label class="chart-remarks"><strong><?=$noi_report?></strong></label>
    								</div>
    							</div>

    							<div class="col-lg-6">
    								<div class="dashboard-label text-center"><br /><br /><br />
	    								<img src="<?=base_url('assets/img/store-dashboard.png')?>" class="img-responsive text-center" width="150px" style="display: block;margin-left: auto;margin-right: auto;"><br /><br />

	    								<label class="chart-remarks"><strong>Open <span class="dashboard-num"><?=$outlet_new?></span> stores to end with <span class="dashboard-num"><?=number_format($outlet_total, 2)?></span> stores in <span class="dashboard-num"><?=$year?></span></strong></label>
    								</div>
    							</div>
    						</div>

    						<div class="col-lg-12 dashboard-row">
    							<div class="col-lg-6">
    								<div class="dashboard-label text-center">
    									<label>Broiler Cost</label>
    									<canvas id="broiler-cost-chart" height="145px"></canvas>
    									<label class="chart-remarks"><strong><?=$broiler_cost_report?></strong></label>
    								</div>
    							</div>

    							<div class="col-lg-6">
    								<ul id="manpower-list">
    									
    									<?php 
    										echo '<li><span class="fa fa-plus"></span>' . $emp_new . ' Manpower</li><br/><br/><br/>';
    										for($a = 0; $a < $emp_new; $a++){
    											echo ' <li><span class="fa fa-user-circle"></span></li>';		
    										}
    									?>
    									
    								</ul>
    								<div class="dashboard-label text-center"><br /><br />
                                        <label class="chart-remarks"><strong>Manpower will increase by <span class="dashboard-num"><?=$emp_new?></span> <span class="fa fa-long-arrow-up"></span> from <span class="dashboard-num"><?=$emp_old?></span> to <span class="dashboard-num"><?=$emp_new + $emp_old?></span> personnel in <span class="dashboard-num"><?=$year?></span></strong></label>
                                    </div>
    							</div>
    						</div>

    						<div class="col-lg-12 dashboard-row">
    							<div class="col-lg-6">
    								<div class="dashboard-label text-center">
    									<label>CAPEX</label>
    									<canvas id="capex-chart" height="145px"></canvas>
    									<label class="chart-remarks"><strong><?=$capex_report?></strong></label>
    								</div>
    							</div>
    						</div>
    					</div>

					</div>

                    <div id="harvested-birds-tab" class="tab-pane fade in">
                        <br>
                        <div class="row">                           
                            <div class="col-lg-12 dashboard-row">
                                <div class="col-lg-12">
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

                                        <?=$harvest_tbl?>

                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>

                    </div>

                    <div id="industry-tab" class="tab-pane fade in">
                        <div class="row">

                            <div class="col-lg-12 text-center">
                                <br>
                                <label>INDUSTRY UPDATE</label>
                            </div>
                            <div class="col-lg-7">
                                <br>
                                
                                <div class="table-responsive">
                                    <table class="table table-hover table-bordered nowrap" id="tbl-industry-dashboard-trans" width="100%">
                                        <thead>
                                            <tr>
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
                                                
                                                <td><?=$row->industry_name?></td>
                                                <td class="text-right"><?=number_format($row->beginning_capacity,0,'.',',')?></td>
                                                <td class="text-right"><?=$beginning_perc.'%'?></td>
                                                <td class="text-right"><?=number_format($row->current_capacity,0,'.',',')?></td>
                                                <td class="text-right"><?=$current_perc.'%'?></td>
                                                <td class="text-right"><?=number_format($row->ending_capacity,0,'.',',')?></td>
                                                <td class="text-right"><?=$ending_perc.'%'?></td>
                                            </tr>
                                            <?php endforeach; ?>
                                            <tr>
                                                
                                                <td>Total Capacity</td>
                                                <td class="text-right"><?=number_format($beginning_total,0,'.',',')?></td>
                                                <td class="text-right"><?=number_format($beginning_total_perc,0,'.',',').'%'?></td>
                                                <td class="text-right"><?=number_format($current_total,0,'.',',')?></td>
                                                <td class="text-right"><?=number_format($current_total_perc,0,'.',',').'%'?></td>
                                                <td class="text-right"><?=number_format($ending_total,0,'.',',')?></td>
                                                <td class="text-right"><?=number_format($ending_total_perc,0,'.',',').'%'?></td>

                                            </tr>
                                        </tbody>
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
                                    <canvas id="cg-chart" height="300px"></canvas>
                                </div>
                            </div>
                        </div>
                    </div>

					<div id="volume-tab" class="tab-pane fade">
						<div class="col-lg-12 dashboard-row">
    							<div class="dashboard-label text-center">
    								<label><strong>Schedule 1. VOLUME</strong></label>
    							</div>

    							<div class="col-lg-12">
    								<div class="dashboard-label text-center">
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
                                                <?=$volume_tbl?>
                                            </tbody>
                                        </table>
    								</div>
    							</div>
    						</div>
					</div>

					<div id="price-tab" class="tab-pane fade">
						<div class="col-lg-12 dashboard-row">
							<div class="dashboard-label text-center">
								<label><strong>Price Assumption</strong></label>
							</div>

							<div class="col-lg-12">
								<div class="dashboard-label">
                                    <table class="table table-bordered" id="price-assumption-tbl">
                                        <thead>
                                            <tr>
                                                <th>Segment</th>
                                                <th>Product</th>
                                                <th>YEAR</th>
                                                <th>JAN</th>
                                                <th>FEB</th>
                                                <th>MAR</th>
                                                <th>APR</th>
                                                <th>MAY</th>
                                                <th>JUN</th>
                                                <th>JUL</th>
                                                <th>AUG</th>
                                                <th>SEP</th>
                                                <th>OCT</th>
                                                <th>NOV</th>
                                                <th>DEC</th>
                                                <th>AVE</th>
                                                <th>MIN</th>
                                                <th>MAX</th>
                                            </tr>

                                            
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td rowspan="9" class="rowspan-center" style="vertical-align : middle;text-align:center;">COM</td>
                                                <td rowspan="3" class="rowspan-center" style="vertical-align : middle;text-align:center;">LIVE</td>
                                                <td><?=$year - 2?> Actual</td>
                                                <td class="text-right"><?=check_zero(number_format($price['live_prev_year2_jan']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['live_prev_year2_feb']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['live_prev_year2_mar']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['live_prev_year2_apr']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['live_prev_year2_may']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['live_prev_year2_jun']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['live_prev_year2_jul']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['live_prev_year2_aug']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['live_prev_year2_sep']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['live_prev_year2_oct']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['live_prev_year2_nov']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['live_prev_year2_dec']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['live_prev_year2_avg']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['live_prev_year2_min']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['live_prev_year2_max']))?></td>
                                            </tr>

                                            <tr>
                                                <td><?=$year - 1?> YEE</td>
                                                <td class="text-right"><?=check_zero(number_format($price['live_prev_year1_jan']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['live_prev_year1_feb']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['live_prev_year1_mar']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['live_prev_year1_apr']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['live_prev_year1_may']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['live_prev_year1_jun']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['live_prev_year1_jul']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['live_prev_year1_aug']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['live_prev_year1_sep']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['live_prev_year1_oct']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['live_prev_year1_nov']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['live_prev_year1_dec']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['live_prev_year1_avg']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['live_prev_year1_min']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['live_prev_year1_max']))?></td>
                                            </tr>

                                            <tr>
                                                <td><?=$year?> Budget</td>
                                                <td class="text-right"><?=check_zero(number_format($price['live1']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['live2']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['live3']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['live4']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['live5']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['live6']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['live7']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['live8']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['live9']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['live10']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['live11']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['live12']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['live_avg']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['live_min']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['live_max']))?></td>
                                            </tr>

                                            <!-- TDS -->
                                            <tr>
                                                <td rowspan="3" class="rowspan-center" style="vertical-align : middle;text-align:center;">TDs</td>
                                                <td><?=$year - 2?> Actual</td>
                                                <td class="text-right"><?=check_zero(number_format($price['tds_prev_year2_jan']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['tds_prev_year2_feb']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['tds_prev_year2_mar']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['tds_prev_year2_apr']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['tds_prev_year2_may']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['tds_prev_year2_jun']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['tds_prev_year2_jul']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['tds_prev_year2_aug']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['tds_prev_year2_sep']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['tds_prev_year2_oct']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['tds_prev_year2_nov']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['tds_prev_year2_dec']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['tds_prev_year2_avg']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['tds_prev_year2_min']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['tds_prev_year2_max']))?></td>
                                            </tr>

                                            <tr>
                                                <td><?=$year - 1?> YEE</td>
                                                <td class="text-right"><?=check_zero(number_format($price['tds_prev_year1_jan']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['tds_prev_year1_feb']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['tds_prev_year1_mar']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['tds_prev_year1_apr']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['tds_prev_year1_may']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['tds_prev_year1_jun']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['tds_prev_year1_jul']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['tds_prev_year1_aug']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['tds_prev_year1_sep']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['tds_prev_year1_oct']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['tds_prev_year1_nov']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['tds_prev_year1_dec']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['tds_prev_year1_avg']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['tds_prev_year1_min']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['tds_prev_year1_max']))?></td>
                                            </tr>

                                            <tr>
                                                <td><?=$year?> Budget</td>
                                                <td class="text-right"><?=check_zero(number_format($price['tds1']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['tds2']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['tds3']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['tds4']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['tds5']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['tds6']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['tds7']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['tds8']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['tds9']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['tds10']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['tds11']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['tds12']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['tds_avg']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['tds_min']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['tds_max']))?></td>
                                            </tr>

                                            <!-- Supermarket -->

                                            <tr>
                                                <td rowspan="3" class="rowspan-center" style="vertical-align : middle;text-align:center;">SUPERMARKET</td>
                                                <td><?=$year - 2?> Actual</td>
                                                <td class="text-right"><?=check_zero(number_format($price['smkt_prev_year2_jan']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['smkt_prev_year2_feb']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['smkt_prev_year2_mar']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['smkt_prev_year2_apr']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['smkt_prev_year2_may']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['smkt_prev_year2_jun']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['smkt_prev_year2_jul']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['smkt_prev_year2_aug']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['smkt_prev_year2_sep']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['smkt_prev_year2_oct']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['smkt_prev_year2_nov']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['smkt_prev_year2_dec']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['smkt_prev_year2_avg']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['smkt_prev_year2_min']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['smkt_prev_year2_max']))?></td>
                                            </tr>

                                            <tr>
                                                <td><?=$year - 1?> YEE</td>
                                                <td class="text-right"><?=check_zero(number_format($price['smkt_prev_year1_jan']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['smkt_prev_year1_feb']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['smkt_prev_year1_mar']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['smkt_prev_year1_apr']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['smkt_prev_year1_may']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['smkt_prev_year1_jun']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['smkt_prev_year1_jul']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['smkt_prev_year1_aug']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['smkt_prev_year1_sep']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['smkt_prev_year1_oct']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['smkt_prev_year1_nov']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['smkt_prev_year1_dec']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['smkt_prev_year1_avg']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['smkt_prev_year1_min']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['smkt_prev_year1_max']))?></td>
                                            </tr>

                                            <tr>
                                                <td><?=$year?> Budget</td>
                                                <td class="text-right"><?=check_zero(number_format($price['smkt1']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['smkt2']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['smkt3']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['smkt4']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['smkt5']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['smkt6']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['smkt7']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['smkt8']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['smkt9']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['smkt10']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['smkt11']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['smkt12']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['smkt_avg']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['smkt_min']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['smkt_max']))?></td>
                                            </tr>

                                            <!-- CTG ORC REGULAR -->
                                            <tr>
                                                <td rowspan="48" class="rowspan-center" style="vertical-align : middle;text-align:center;">CTG</td>
                                                <td rowspan="3" class="rowspan-center" style="vertical-align : middle;text-align:center;">ORC - REGULAR</td>
                                                <td><?=$year - 2?> Actual</td>
                                                <td class="text-right"><?=check_zero(number_format($price['ctg_reg1_year2']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ctg_reg2_year2']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ctg_reg3_year2']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ctg_reg4_year2']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ctg_reg5_year2']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ctg_reg6_year2']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ctg_reg7_year2']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ctg_reg8_year2']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ctg_reg9_year2']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ctg_reg10_year2']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ctg_reg11_year2']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ctg_reg12_year2']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ctg_reg_avg_year2']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ctg_reg_min_year2']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ctg_reg_max_year2']))?></td>
                                            </tr>

                                            <tr>
                                                <td><?=$year - 1?> YEE</td>
                                                <td class="text-right"><?=check_zero(number_format($price['ctg_reg1_year1']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ctg_reg2_year1']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ctg_reg3_year1']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ctg_reg4_year1']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ctg_reg5_year1']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ctg_reg6_year1']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ctg_reg7_year1']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ctg_reg8_year1']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ctg_reg9_year1']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ctg_reg10_year1']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ctg_reg11_year1']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ctg_reg12_year1']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ctg_reg_avg_year1']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ctg_reg_min_year1']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ctg_reg_max_year1']))?></td>
                                            </tr>

                                            <tr>
                                                <td><?=$year?> Budget</td>
                                                <td class="text-right"><?=check_zero(number_format($price['ctg_reg1']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ctg_reg2']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ctg_reg3']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ctg_reg4']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ctg_reg5']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ctg_reg6']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ctg_reg7']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ctg_reg8']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ctg_reg9']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ctg_reg10']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ctg_reg11']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ctg_reg12']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ctg_reg_avg']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ctg_reg_min']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ctg_reg_max']))?></td>
                                            </tr>

                                            <!-- CTG ORC JUMBO -->
                                            <tr>
                                                <td rowspan="3" class="rowspan-center" style="vertical-align : middle;text-align:center;">ORC - JUMBO</td>
                                                <td><?=$year - 2?> Actual</td>
                                                <td class="text-right"><?=check_zero(number_format($price['ctg_jbo1_year2']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ctg_jbo2_year2']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ctg_jbo3_year2']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ctg_jbo4_year2']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ctg_jbo5_year2']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ctg_jbo6_year2']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ctg_jbo7_year2']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ctg_jbo8_year2']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ctg_jbo9_year2']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ctg_jbo10_year2']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ctg_jbo11_year2']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ctg_jbo12_year2']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ctg_jbo_year2_avg']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ctg_jbo_year2_min']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ctg_jbo_year2_max']))?></td>
                                            </tr>

                                            <tr>
                                                <td><?=$year - 1?> YEE</td>
                                                <td class="text-right"><?=check_zero(number_format($price['ctg_jbo1_year1']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ctg_jbo2_year1']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ctg_jbo3_year1']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ctg_jbo4_year1']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ctg_jbo5_year1']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ctg_jbo6_year1']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ctg_jbo7_year1']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ctg_jbo8_year1']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ctg_jbo9_year1']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ctg_jbo10_year1']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ctg_jbo11_year1']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ctg_jbo12_year1']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ctg_jbo_year1_avg']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ctg_jbo_year1_min']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ctg_jbo_year1_max']))?></td>
                                            </tr>

                                            <tr>
                                                <td><?=$year?> Budget</td>
                                                <td class="text-right"><?=check_zero(number_format($price['ctg_jbo1']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ctg_jbo2']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ctg_jbo3']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ctg_jbo4']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ctg_jbo5']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ctg_jbo6']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ctg_jbo7']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ctg_jbo8']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ctg_jbo9']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ctg_jbo10']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ctg_jbo11']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ctg_jbo12']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ctg_jbo_avg']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ctg_jbo_min']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ctg_jbo_max']))?></td>
                                            </tr>

                                            <!-- CTG ORC SUPERSIZE -->
                                            <tr>
                                                <td rowspan="3" class="rowspan-center" style="vertical-align : middle;text-align:center;">ORC - SUPERSIZE</td>
                                                <td><?=$year - 2?> Actual</td>
                                                <td class="text-right"><?=check_zero(number_format($price['ctg_ss1_year2']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ctg_ss2_year2']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ctg_ss3_year2']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ctg_ss4_year2']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ctg_ss5_year2']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ctg_ss6_year2']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ctg_ss7_year2']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ctg_ss8_year2']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ctg_ss9_year2']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ctg_ss10_year2']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ctg_ss11_year2']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ctg_ss12_year2']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ctg_ss_avg_year2']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ctg_ss_min_year2']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ctg_ss_max_year2']))?></td>
                                            </tr>

                                            <tr>
                                                <td><?=$year - 1?> YEE</td>
                                                <td class="text-right"><?=check_zero(number_format($price['ctg_ss1_year1']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ctg_ss2_year1']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ctg_ss3_year1']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ctg_ss4_year1']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ctg_ss5_year1']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ctg_ss6_year1']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ctg_ss7_year1']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ctg_ss8_year1']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ctg_ss9_year1']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ctg_ss10_year1']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ctg_ss11_year1']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ctg_ss12_year1']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ctg_ss_avg_year1']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ctg_ss_min_year1']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ctg_ss_max_year1']))?></td>
                                            </tr>

                                            <tr>
                                                <td><?=$year?> Budget</td>
                                                <td class="text-right"><?=check_zero(number_format($price['ctg_ss1']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ctg_ss2']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ctg_ss3']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ctg_ss4']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ctg_ss5']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ctg_ss6']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ctg_ss7']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ctg_ss8']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ctg_ss9']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ctg_ss10']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ctg_ss11']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ctg_ss12']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ctg_ss_avg']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ctg_ss_min']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ctg_ss_max']))?></td>
                                            </tr>

                                            <!-- CTG ORC BIGTIME -->
                                            <tr>
                                                <td rowspan="3" class="rowspan-center" style="vertical-align : middle;text-align:center;">ORC - BIGTIME</td>
                                                <td><?=$year - 2?> Actual</td>
                                                <td class="text-right"><?=check_zero(number_format($price['ctg_bt1_year2']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ctg_bt2_year2']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ctg_bt3_year2']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ctg_bt4_year2']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ctg_bt5_year2']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ctg_bt6_year2']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ctg_bt7_year2']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ctg_bt8_year2']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ctg_bt9_year2']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ctg_bt10_year2']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ctg_bt11_year2']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ctg_bt12_year2']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ctg_bt_avg_year2']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ctg_bt_min_year2']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ctg_bt_max_year2']))?></td>
                                            </tr>

                                            <tr>
                                                <td><?=$year - 1?> YEE</td>
                                                <td class="text-right"><?=check_zero(number_format($price['ctg_bt1_year1']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ctg_bt2_year1']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ctg_bt3_year1']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ctg_bt4_year1']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ctg_bt5_year1']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ctg_bt6_year1']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ctg_bt7_year1']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ctg_bt8_year1']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ctg_bt9_year1']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ctg_bt10_year1']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ctg_bt11_year1']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ctg_bt12_year1']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ctg_bt_avg_year1']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ctg_bt_min_year1']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ctg_bt_max_year1']))?></td>
                                            </tr>

                                            <tr>
                                                <td><?=$year?> Budget</td>
                                                <td class="text-right"><?=check_zero(number_format($price['ctg_bt1']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ctg_bt2']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ctg_bt3']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ctg_bt4']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ctg_bt5']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ctg_bt6']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ctg_bt7']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ctg_bt8']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ctg_bt9']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ctg_bt10']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ctg_bt11']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ctg_bt12']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ctg_bt_avg']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ctg_bt_min']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ctg_bt_max']))?></td>
                                            </tr>

                                            <!-- CTG ORC HALF -->
                                            <tr>
                                                <td rowspan="3" class="rowspan-center" style="vertical-align : middle;text-align:center;">ORC - HALF</td>
                                                <td><?=$year - 2?> Actual</td>
                                                <td class="text-right"><?=check_zero(number_format($price['ctg_half1_year2']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ctg_half2_year2']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ctg_half3_year2']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ctg_half4_year2']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ctg_half5_year2']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ctg_half6_year2']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ctg_half7_year2']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ctg_half8_year2']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ctg_half9_year2']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ctg_half10_year2']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ctg_half11_year2']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ctg_half12_year2']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ctg_half_avg_year2']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ctg_half_min_year2']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ctg_half_max_year2']))?></td>
                                            </tr>

                                            <tr>
                                                <td><?=$year - 1?> YEE</td>
                                                <td class="text-right"><?=check_zero(number_format($price['ctg_half1_year1']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ctg_half2_year1']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ctg_half3_year1']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ctg_half4_year1']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ctg_half5_year1']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ctg_half6_year1']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ctg_half7_year1']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ctg_half8_year1']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ctg_half9_year1']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ctg_half10_year1']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ctg_half11_year1']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ctg_half12_year1']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ctg_half_avg_year1']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ctg_half_min_year1']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ctg_half_max_year1']))?></td>
                                            </tr>

                                            <tr>
                                                <td><?=$year?> Budget</td>
                                                <td class="text-right"><?=check_zero(number_format($price['ctg_half1']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ctg_half2']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ctg_half3']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ctg_half4']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ctg_half5']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ctg_half6']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ctg_half7']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ctg_half8']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ctg_half9']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ctg_half10']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ctg_half11']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ctg_half12']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ctg_half_avg']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ctg_half_min']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ctg_half_max']))?></td>
                                            </tr>

                                            <!-- CTG LIEMPO -->
                                            <tr>
                                                <td rowspan="3" class="rowspan-center" style="vertical-align : middle;text-align:center;">LIEMPO</td>
                                                <td><?=$year - 2?> Actual</td>
                                                <td class="text-right"><?=check_zero(number_format($price['ctg_liempo_prev_year2_jan']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ctg_liempo_prev_year2_feb']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ctg_liempo_prev_year2_mar']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ctg_liempo_prev_year2_apr']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ctg_liempo_prev_year2_may']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ctg_liempo_prev_year2_jun']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ctg_liempo_prev_year2_jul']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ctg_liempo_prev_year2_aug']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ctg_liempo_prev_year2_sep']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ctg_liempo_prev_year2_oct']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ctg_liempo_prev_year2_nov']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ctg_liempo_prev_year2_dec']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ctg_liempo_prev_year2_avg']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ctg_liempo_prev_year2_min']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ctg_liempo_prev_year2_max']))?></td>
                                            </tr>

                                            <tr>
                                                <td><?=$year - 1?> YEE</td>
                                                <td class="text-right"><?=check_zero(number_format($price['ctg_liempo_prev_year1_jan']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ctg_liempo_prev_year1_feb']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ctg_liempo_prev_year1_mar']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ctg_liempo_prev_year1_apr']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ctg_liempo_prev_year1_may']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ctg_liempo_prev_year1_jun']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ctg_liempo_prev_year1_jul']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ctg_liempo_prev_year1_aug']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ctg_liempo_prev_year1_sep']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ctg_liempo_prev_year1_oct']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ctg_liempo_prev_year1_nov']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ctg_liempo_prev_year1_dec']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ctg_liempo_prev_year1_avg']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ctg_liempo_prev_year1_min']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ctg_liempo_prev_year1_max']))?></td>
                                            </tr>

                                            <tr>
                                                <td><?=$year?> Budget</td>
                                                <td class="text-right"><?=check_zero(number_format($price['liempo1']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['liempo2']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['liempo3']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['liempo4']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['liempo5']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['liempo6']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['liempo7']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['liempo8']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['liempo9']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['liempo10']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['liempo11']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['liempo12']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['liempo_avg']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['liempo_min']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['liempo_max']))?></td>
                                            </tr>

                                            <!-- CTG DRESSED CHICKEN -->
                                            <tr>
                                                <td rowspan="3" class="rowspan-center" style="vertical-align : middle;text-align:center;">DRESSED</td>
                                                <td><?=$year - 2?> Actual</td>
                                                <td class="text-right"><?=check_zero(number_format($price['ctg_dressed_prev_year2_jan']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ctg_dressed_prev_year2_feb']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ctg_dressed_prev_year2_mar']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ctg_dressed_prev_year2_apr']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ctg_dressed_prev_year2_may']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ctg_dressed_prev_year2_jun']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ctg_dressed_prev_year2_jul']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ctg_dressed_prev_year2_aug']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ctg_dressed_prev_year2_sep']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ctg_dressed_prev_year2_oct']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ctg_dressed_prev_year2_nov']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ctg_dressed_prev_year2_dec']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ctg_dressed_prev_year2_avg']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ctg_dressed_prev_year2_min']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ctg_dressed_prev_year2_max']))?></td>
                                            </tr>

                                            <tr>
                                                <td><?=$year - 1?> YEE</td>
                                                <td class="text-right"><?=check_zero(number_format($price['ctg_dressed_prev_year1_jan']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ctg_dressed_prev_year1_feb']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ctg_dressed_prev_year1_mar']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ctg_dressed_prev_year1_apr']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ctg_dressed_prev_year1_may']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ctg_dressed_prev_year1_jun']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ctg_dressed_prev_year1_jul']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ctg_dressed_prev_year1_aug']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ctg_dressed_prev_year1_sep']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ctg_dressed_prev_year1_oct']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ctg_dressed_prev_year1_nov']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ctg_dressed_prev_year1_dec']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ctg_dressed_prev_year1_avg']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ctg_dressed_prev_year1_min']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ctg_dressed_prev_year1_max']))?></td>
                                            </tr>

                                            <tr>
                                                <td><?=$year?> Budget</td>
                                                <td class="text-right"><?=check_zero(number_format($price['ctg_dressed1']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ctg_dressed2']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ctg_dressed3']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ctg_dressed4']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ctg_dressed5']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ctg_dressed6']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ctg_dressed7']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ctg_dressed8']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ctg_dressed9']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ctg_dressed10']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ctg_dressed11']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ctg_dressed12']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ctg_dressed_avg']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ctg_dressed_min']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ctg_dressed_max']))?></td>
                                            </tr>


                                            <!-- CTG CHOOKSIES MARINADO -->
                                            <tr>
                                                <td rowspan="3" class="rowspan-center" style="vertical-align : middle;text-align:center;">CHOOKSIES MARINADO</td>
                                                <td><?=$year - 2?> Actual</td>
                                                <td class="text-right"><?=check_zero(number_format($price['ctg_chooksies_prev_year2_jan']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ctg_chooksies_prev_year2_feb']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ctg_chooksies_prev_year2_mar']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ctg_chooksies_prev_year2_apr']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ctg_chooksies_prev_year2_may']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ctg_chooksies_prev_year2_jun']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ctg_chooksies_prev_year2_jul']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ctg_chooksies_prev_year2_aug']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ctg_chooksies_prev_year2_sep']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ctg_chooksies_prev_year2_oct']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ctg_chooksies_prev_year2_nov']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ctg_chooksies_prev_year2_dec']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ctg_chooksies_prev_year2_avg']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ctg_chooksies_prev_year2_min']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ctg_chooksies_prev_year2_max']))?></td>
                                            </tr>

                                            <tr>
                                                <td><?=$year - 1?> YEE</td>
                                                <td class="text-right"><?=check_zero(number_format($price['ctg_chooksies_prev_year1_jan']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ctg_chooksies_prev_year1_feb']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ctg_chooksies_prev_year1_mar']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ctg_chooksies_prev_year1_apr']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ctg_chooksies_prev_year1_may']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ctg_chooksies_prev_year1_jun']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ctg_chooksies_prev_year1_jul']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ctg_chooksies_prev_year1_aug']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ctg_chooksies_prev_year1_sep']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ctg_chooksies_prev_year1_oct']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ctg_chooksies_prev_year1_nov']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ctg_chooksies_prev_year1_dec']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ctg_chooksies_prev_year1_avg']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ctg_chooksies_prev_year1_min']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ctg_chooksies_prev_year1_max']))?></td>
                                            </tr>

                                            <tr>
                                                <td><?=$year?> Budget</td>
                                                <td class="text-right"><?=check_zero(number_format($price['ctg_chooksies1']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ctg_chooksies2']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ctg_chooksies3']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ctg_chooksies4']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ctg_chooksies5']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ctg_chooksies6']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ctg_chooksies7']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ctg_chooksies8']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ctg_chooksies9']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ctg_chooksies10']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ctg_chooksies11']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ctg_chooksies12']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ctg_chooksies_avg']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ctg_chooksies_min']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ctg_chooksies_max']))?></td>
                                            </tr>

                                            <!-- CTG MARINADO FRIED -->
                                            <tr>
                                                <td rowspan="3" class="rowspan-center" style="vertical-align : middle;text-align:center;">MARINADO FRIED</td>
                                                <td><?=$year - 2?> Actual</td>
                                                <td class="text-right"><?=check_zero(number_format($price['ctg_marinado_prev_year2_jan']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ctg_marinado_prev_year2_feb']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ctg_marinado_prev_year2_mar']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ctg_marinado_prev_year2_apr']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ctg_marinado_prev_year2_may']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ctg_marinado_prev_year2_jun']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ctg_marinado_prev_year2_jul']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ctg_marinado_prev_year2_aug']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ctg_marinado_prev_year2_sep']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ctg_marinado_prev_year2_oct']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ctg_marinado_prev_year2_nov']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ctg_marinado_prev_year2_dec']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ctg_marinado_prev_year2_avg']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ctg_marinado_prev_year2_min']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ctg_marinado_prev_year2_max']))?></td>
                                            </tr>

                                            <tr>
                                                <td><?=$year - 1?> YEE</td>
                                                <td class="text-right"><?=check_zero(number_format($price['ctg_marinado_prev_year1_jan']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ctg_marinado_prev_year1_feb']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ctg_marinado_prev_year1_mar']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ctg_marinado_prev_year1_apr']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ctg_marinado_prev_year1_may']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ctg_marinado_prev_year1_jun']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ctg_marinado_prev_year1_jul']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ctg_marinado_prev_year1_aug']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ctg_marinado_prev_year1_sep']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ctg_marinado_prev_year1_oct']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ctg_marinado_prev_year1_nov']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ctg_marinado_prev_year1_dec']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ctg_marinado_prev_year1_avg']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ctg_marinado_prev_year1_min']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ctg_marinado_prev_year1_max']))?></td>
                                            </tr>

                                            <tr>
                                                <td><?=$year?> Budget</td>
                                                <td class="text-right"><?=check_zero(number_format($price['ctg_marinado1']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ctg_marinado2']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ctg_marinado3']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ctg_marinado4']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ctg_marinado5']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ctg_marinado6']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ctg_marinado7']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ctg_marinado8']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ctg_marinado9']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ctg_marinado10']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ctg_marinado11']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ctg_marinado12']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ctg_marinado_avg']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ctg_marinado_min']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ctg_marinado_max']))?></td>
                                            </tr>

                                            <!-- CTG SPICY NECK -->
                                            <tr>
                                                <td rowspan="3" class="rowspan-center" style="vertical-align : middle;text-align:center;">SPICY NECK</td>
                                                <td><?=$year - 2?> Actual</td>
                                                <td class="text-right"><?=check_zero(number_format($price['ctg_spicy_prev_year2_jan']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ctg_spicy_prev_year2_feb']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ctg_spicy_prev_year2_mar']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ctg_spicy_prev_year2_apr']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ctg_spicy_prev_year2_may']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ctg_spicy_prev_year2_jun']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ctg_spicy_prev_year2_jul']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ctg_spicy_prev_year2_aug']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ctg_spicy_prev_year2_sep']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ctg_spicy_prev_year2_oct']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ctg_spicy_prev_year2_nov']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ctg_spicy_prev_year2_dec']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ctg_spicy_prev_year2_avg']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ctg_spicy_prev_year2_min']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ctg_spicy_prev_year2_max']))?></td>
                                            </tr>

                                            <tr>
                                                <td><?=$year - 1?> YEE</td>
                                                <td class="text-right"><?=check_zero(number_format($price['ctg_spicy_prev_year1_jan']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ctg_spicy_prev_year1_feb']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ctg_spicy_prev_year1_mar']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ctg_spicy_prev_year1_apr']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ctg_spicy_prev_year1_may']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ctg_spicy_prev_year1_jun']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ctg_spicy_prev_year1_jul']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ctg_spicy_prev_year1_aug']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ctg_spicy_prev_year1_sep']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ctg_spicy_prev_year1_oct']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ctg_spicy_prev_year1_nov']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ctg_spicy_prev_year1_dec']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ctg_spicy_prev_year1_avg']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ctg_spicy_prev_year1_min']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ctg_spicy_prev_year1_max']))?></td>
                                            </tr>

                                            <tr>
                                                <td><?=$year?> Budget</td>
                                                <td class="text-right"><?=check_zero(number_format($price['ctg_spicy1']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ctg_spicy2']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ctg_spicy3']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ctg_spicy4']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ctg_spicy5']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ctg_spicy6']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ctg_spicy7']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ctg_spicy8']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ctg_spicy9']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ctg_spicy10']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ctg_spicy11']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ctg_spicy12']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ctg_spicy_avg']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ctg_spicy_min']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ctg_spicy_max']))?></td>
                                            </tr>

                                            <!-- CTG NUGGETS -->
                                            <tr>
                                                <td rowspan="3" class="rowspan-center" style="vertical-align : middle;text-align:center;">VAP-Nuggets</td>
                                                <td><?=$year - 2?> Actual</td>
                                                <td class="text-right"><?=check_zero(number_format($price['ctg_nuggets_prev_year2_jan']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ctg_nuggets_prev_year2_feb']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ctg_nuggets_prev_year2_mar']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ctg_nuggets_prev_year2_apr']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ctg_nuggets_prev_year2_may']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ctg_nuggets_prev_year2_jun']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ctg_nuggets_prev_year2_jul']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ctg_nuggets_prev_year2_aug']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ctg_nuggets_prev_year2_sep']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ctg_nuggets_prev_year2_oct']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ctg_nuggets_prev_year2_nov']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ctg_nuggets_prev_year2_dec']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ctg_nuggets_prev_year2_avg']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ctg_nuggets_prev_year2_min']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ctg_nuggets_prev_year2_max']))?></td>
                                            </tr>

                                            <tr>
                                                <td><?=$year - 1?> YEE</td>
                                                <td class="text-right"><?=check_zero(number_format($price['ctg_nuggets_prev_year1_jan']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ctg_nuggets_prev_year1_feb']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ctg_nuggets_prev_year1_mar']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ctg_nuggets_prev_year1_apr']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ctg_nuggets_prev_year1_may']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ctg_nuggets_prev_year1_jun']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ctg_nuggets_prev_year1_jul']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ctg_nuggets_prev_year1_aug']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ctg_nuggets_prev_year1_sep']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ctg_nuggets_prev_year1_oct']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ctg_nuggets_prev_year1_nov']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ctg_nuggets_prev_year1_dec']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ctg_nuggets_prev_year1_avg']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ctg_nuggets_prev_year1_min']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ctg_nuggets_prev_year1_max']))?></td>
                                            </tr>

                                            <tr>
                                                <td><?=$year?> Budget</td>
                                                <td class="text-right"><?=check_zero(number_format($price['ctg_nuggets1']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ctg_nuggets2']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ctg_nuggets3']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ctg_nuggets4']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ctg_nuggets5']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ctg_nuggets6']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ctg_nuggets7']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ctg_nuggets8']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ctg_nuggets9']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ctg_nuggets10']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ctg_nuggets11']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ctg_nuggets12']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ctg_nuggets_avg']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ctg_nuggets_min']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ctg_nuggets_max']))?></td>
                                            </tr>

                                            <!-- CTG 11 PC -->
                                            <tr>
                                                <td rowspan="3" class="rowspan-center" style="vertical-align : middle;text-align:center;">11 PC</td>
                                                <td><?=$year - 2?> Actual</td>
                                                <td class="text-right"><?=check_zero(number_format($price['ctg_pica_prev_year2_jan']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ctg_pica_prev_year2_feb']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ctg_pica_prev_year2_mar']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ctg_pica_prev_year2_apr']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ctg_pica_prev_year2_may']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ctg_pica_prev_year2_jun']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ctg_pica_prev_year2_jul']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ctg_pica_prev_year2_aug']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ctg_pica_prev_year2_sep']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ctg_pica_prev_year2_oct']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ctg_pica_prev_year2_nov']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ctg_pica_prev_year2_dec']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ctg_pica_prev_year2_avg']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ctg_pica_prev_year2_min']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ctg_pica_prev_year2_max']))?></td>
                                            </tr>

                                            <tr>
                                                <td><?=$year - 1?> YEE</td>
                                                <td class="text-right"><?=check_zero(number_format($price['ctg_pica_prev_year1_jan']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ctg_pica_prev_year1_feb']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ctg_pica_prev_year1_mar']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ctg_pica_prev_year1_apr']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ctg_pica_prev_year1_may']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ctg_pica_prev_year1_jun']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ctg_pica_prev_year1_jul']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ctg_pica_prev_year1_aug']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ctg_pica_prev_year1_sep']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ctg_pica_prev_year1_oct']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ctg_pica_prev_year1_nov']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ctg_pica_prev_year1_dec']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ctg_pica_prev_year1_avg']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ctg_pica_prev_year1_min']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ctg_pica_prev_year1_max']))?></td>
                                            </tr>

                                            <tr>
                                                <td><?=$year?> Budget</td>
                                                <td class="text-right"><?=check_zero(number_format($price['ctg_pica1']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ctg_pica2']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ctg_pica3']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ctg_pica4']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ctg_pica5']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ctg_pica6']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ctg_pica7']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ctg_pica8']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ctg_pica9']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ctg_pica10']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ctg_pica11']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ctg_pica12']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ctg_pica_avg']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ctg_pica_min']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ctg_pica_max']))?></td>
                                            </tr>

                                            <!-- CTG 5 PC -->
                                            <tr>
                                                <td rowspan="3" class="rowspan-center" style="vertical-align : middle;text-align:center;">5 PC</td>
                                                <td><?=$year - 2?> Actual</td>
                                                <td class="text-right"><?=check_zero(number_format($price['ctg_bossing_prev_year2_jan']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ctg_bossing_prev_year2_feb']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ctg_bossing_prev_year2_mar']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ctg_bossing_prev_year2_apr']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ctg_bossing_prev_year2_may']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ctg_bossing_prev_year2_jun']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ctg_bossing_prev_year2_jul']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ctg_bossing_prev_year2_aug']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ctg_bossing_prev_year2_sep']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ctg_bossing_prev_year2_oct']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ctg_bossing_prev_year2_nov']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ctg_bossing_prev_year2_dec']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ctg_bossing_prev_year2_avg']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ctg_bossing_prev_year2_min']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ctg_bossing_prev_year2_max']))?></td>
                                            </tr>

                                            <tr>
                                                <td><?=$year - 1?> YEE</td>
                                                <td class="text-right"><?=check_zero(number_format($price['ctg_bossing_prev_year1_jan']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ctg_bossing_prev_year1_feb']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ctg_bossing_prev_year1_mar']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ctg_bossing_prev_year1_apr']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ctg_bossing_prev_year1_may']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ctg_bossing_prev_year1_jun']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ctg_bossing_prev_year1_jul']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ctg_bossing_prev_year1_aug']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ctg_bossing_prev_year1_sep']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ctg_bossing_prev_year1_oct']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ctg_bossing_prev_year1_nov']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ctg_bossing_prev_year1_dec']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ctg_bossing_prev_year1_avg']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ctg_bossing_prev_year1_min']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ctg_bossing_prev_year1_max']))?></td>
                                            </tr>

                                            <tr>
                                                <td><?=$year?> Budget</td>
                                                <td class="text-right"><?=check_zero(number_format($price['ctg_bossing1']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ctg_bossing2']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ctg_bossing3']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ctg_bossing4']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ctg_bossing5']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ctg_bossing6']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ctg_bossing7']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ctg_bossing8']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ctg_bossing9']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ctg_bossing10']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ctg_bossing11']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ctg_bossing12']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ctg_bossing_avg']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ctg_bossing_min']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ctg_bossing_max']))?></td>
                                            </tr>

                                            <!-- CTG MARINATED CHICKEN RAW -->
                                            <tr>
                                                <td rowspan="3" class="rowspan-center" style="vertical-align : middle;text-align:center;">MARINATED CHICKEN RAW</td>
                                                <td><?=$year - 2?> Actual</td>
                                                <td class="text-right"><?=check_zero(number_format($price['ctg_raw_prev_year2_jan']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ctg_raw_prev_year2_feb']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ctg_raw_prev_year2_mar']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ctg_raw_prev_year2_apr']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ctg_raw_prev_year2_may']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ctg_raw_prev_year2_jun']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ctg_raw_prev_year2_jul']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ctg_raw_prev_year2_aug']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ctg_raw_prev_year2_sep']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ctg_raw_prev_year2_oct']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ctg_raw_prev_year2_nov']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ctg_raw_prev_year2_dec']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ctg_raw_prev_year2_avg']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ctg_raw_prev_year2_min']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ctg_raw_prev_year2_max']))?></td>
                                            </tr>

                                            <tr>
                                                <td><?=$year - 1?> YEE</td>
                                                <td class="text-right"><?=check_zero(number_format($price['ctg_raw_prev_year1_jan']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ctg_raw_prev_year1_feb']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ctg_raw_prev_year1_mar']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ctg_raw_prev_year1_apr']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ctg_raw_prev_year1_may']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ctg_raw_prev_year1_jun']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ctg_raw_prev_year1_jul']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ctg_raw_prev_year1_aug']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ctg_raw_prev_year1_sep']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ctg_raw_prev_year1_oct']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ctg_raw_prev_year1_nov']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ctg_raw_prev_year1_dec']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ctg_raw_prev_year1_avg']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ctg_raw_prev_year1_min']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ctg_raw_prev_year1_max']))?></td>
                                            </tr>

                                            <tr>
                                                <td><?=$year?> Budget</td>
                                                <td class="text-right"><?=check_zero(number_format($price['ctg_raw1']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ctg_raw2']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ctg_raw3']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ctg_raw4']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ctg_raw5']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ctg_raw6']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ctg_raw7']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ctg_raw8']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ctg_raw9']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ctg_raw10']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ctg_raw11']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ctg_raw12']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ctg_raw_avg']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ctg_raw_min']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ctg_raw_max']))?></td>
                                            </tr>

                                            <!-- CHOOKSIES CUT UPS -->
                                            <tr>
                                                <td rowspan="3" class="rowspan-center" style="vertical-align : middle;text-align:center;">CHOOKSIES CUT UPS</td>
                                                <td><?=$year - 2?> Actual</td>
                                                <td class="text-right"><?=check_zero(number_format($price['ctg_cutups_prev_year2_jan']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ctg_cutups_prev_year2_feb']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ctg_cutups_prev_year2_mar']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ctg_cutups_prev_year2_apr']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ctg_cutups_prev_year2_may']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ctg_cutups_prev_year2_jun']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ctg_cutups_prev_year2_jul']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ctg_cutups_prev_year2_aug']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ctg_cutups_prev_year2_sep']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ctg_cutups_prev_year2_oct']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ctg_cutups_prev_year2_nov']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ctg_cutups_prev_year2_dec']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ctg_cutups_prev_year2_avg']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ctg_cutups_prev_year2_min']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ctg_cutups_prev_year2_max']))?></td>
                                            </tr>

                                            <tr>
                                                <td><?=$year - 1?> YEE</td>
                                                <td class="text-right"><?=check_zero(number_format($price['ctg_cutups_prev_year1_jan']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ctg_cutups_prev_year1_feb']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ctg_cutups_prev_year1_mar']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ctg_cutups_prev_year1_apr']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ctg_cutups_prev_year1_may']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ctg_cutups_prev_year1_jun']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ctg_cutups_prev_year1_jul']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ctg_cutups_prev_year1_aug']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ctg_cutups_prev_year1_sep']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ctg_cutups_prev_year1_oct']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ctg_cutups_prev_year1_nov']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ctg_cutups_prev_year1_dec']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ctg_cutups_prev_year1_avg']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ctg_cutups_prev_year1_min']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ctg_cutups_prev_year1_max']))?></td>
                                            </tr>

                                            <tr>
                                                <td><?=$year?> Budget</td>
                                                <td class="text-right"><?=check_zero(number_format($price['ctg_cutups1']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ctg_cutups2']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ctg_cutups3']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ctg_cutups4']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ctg_cutups5']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ctg_cutups6']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ctg_cutups7']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ctg_cutups8']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ctg_cutups9']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ctg_cutups10']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ctg_cutups11']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ctg_cutups12']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ctg_cutups_avg']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ctg_cutups_min']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ctg_cutups_max']))?></td>
                                            </tr>

                                            <!-- CTG GIZZARD / LIVER -->
                                            <tr>
                                                <td rowspan="3" class="rowspan-center" style="vertical-align : middle;text-align:center;">GIZZARD / LIVER</td>
                                                <td><?=$year - 2?> Actual</td>
                                                <td class="text-right"><?=check_zero(number_format($price['ctg_liver_prev_year2_jan']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ctg_liver_prev_year2_feb']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ctg_liver_prev_year2_mar']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ctg_liver_prev_year2_apr']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ctg_liver_prev_year2_may']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ctg_liver_prev_year2_jun']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ctg_liver_prev_year2_jul']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ctg_liver_prev_year2_aug']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ctg_liver_prev_year2_sep']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ctg_liver_prev_year2_oct']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ctg_liver_prev_year2_nov']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ctg_liver_prev_year2_dec']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ctg_liver_prev_year2_avg']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ctg_liver_prev_year2_min']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ctg_liver_prev_year2_max']))?></td>
                                            </tr>

                                            <tr>
                                                <td><?=$year - 1?> YEE</td>
                                                <td class="text-right"><?=check_zero(number_format($price['ctg_liver_prev_year1_jan']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ctg_liver_prev_year1_feb']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ctg_liver_prev_year1_mar']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ctg_liver_prev_year1_apr']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ctg_liver_prev_year1_may']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ctg_liver_prev_year1_jun']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ctg_liver_prev_year1_jul']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ctg_liver_prev_year1_aug']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ctg_liver_prev_year1_sep']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ctg_liver_prev_year1_oct']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ctg_liver_prev_year1_nov']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ctg_liver_prev_year1_dec']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ctg_liver_prev_year1_avg']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ctg_liver_prev_year1_min']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ctg_liver_prev_year1_max']))?></td>
                                            </tr>

                                            <tr>
                                                <td><?=$year?> Budget</td>
                                                <td class="text-right"><?=check_zero(number_format($price['ctg_liver1']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ctg_liver2']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ctg_liver3']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ctg_liver4']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ctg_liver5']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ctg_liver6']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ctg_liver7']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ctg_liver8']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ctg_liver9']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ctg_liver10']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ctg_liver11']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ctg_liver12']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ctg_liver_avg']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ctg_liver_min']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ctg_liver_max']))?></td>
                                            </tr>

                                            <!-- UR -->

                                            <!-- UR LARGO -->
                                            <tr>
                                                <td rowspan="36" class="rowspan-center" style="vertical-align : middle;text-align:center;">UR</td>
                                                <td rowspan="3" class="rowspan-center" style="vertical-align : middle;text-align:center;">LARGO</td>
                                                <td><?=$year - 2?> Actual</td>
                                                <td class="text-right"><?=check_zero(number_format($price['ur_largo_prev_year2_jan']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ur_largo_prev_year2_feb']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ur_largo_prev_year2_mar']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ur_largo_prev_year2_apr']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ur_largo_prev_year2_may']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ur_largo_prev_year2_jun']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ur_largo_prev_year2_jul']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ur_largo_prev_year2_aug']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ur_largo_prev_year2_sep']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ur_largo_prev_year2_oct']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ur_largo_prev_year2_nov']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ur_largo_prev_year2_dec']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ur_largo_prev_year2_avg']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ur_largo_prev_year2_min']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ur_largo_prev_year2_max']))?></td>
                                            </tr>

                                            <tr>
                                                <td><?=$year - 1?> YEE</td>
                                                <td class="text-right"><?=check_zero(number_format($price['ur_largo_prev_year1_jan']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ur_largo_prev_year1_feb']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ur_largo_prev_year1_mar']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ur_largo_prev_year1_apr']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ur_largo_prev_year1_may']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ur_largo_prev_year1_jun']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ur_largo_prev_year1_jul']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ur_largo_prev_year1_aug']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ur_largo_prev_year1_sep']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ur_largo_prev_year1_oct']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ur_largo_prev_year1_nov']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ur_largo_prev_year1_dec']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ur_largo_prev_year1_avg']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ur_largo_prev_year1_min']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ur_largo_prev_year1_max']))?></td>
                                            </tr>

                                            <tr>
                                                <td><?=$year?> Budget</td>
                                                <td class="text-right"><?=check_zero(number_format($price['ur_largo1']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ur_largo2']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ur_largo3']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ur_largo4']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ur_largo5']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ur_largo6']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ur_largo7']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ur_largo8']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ur_largo9']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ur_largo10']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ur_largo11']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ur_largo12']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ur_largo_avg']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ur_largo_min']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ur_largo_max']))?></td>
                                            </tr>

                                            <!-- UR Half -->
                                            <tr>
                                                <td rowspan="3" class="rowspan-center" style="vertical-align : middle;text-align:center;">HALF</td>
                                                <td><?=$year - 2?> Actual</td>
                                                <td class="text-right"><?=check_zero(number_format($price['ur_half_prev_year2_jan']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ur_half_prev_year2_feb']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ur_half_prev_year2_mar']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ur_half_prev_year2_apr']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ur_half_prev_year2_may']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ur_half_prev_year2_jun']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ur_half_prev_year2_jul']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ur_half_prev_year2_aug']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ur_half_prev_year2_sep']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ur_half_prev_year2_oct']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ur_half_prev_year2_nov']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ur_half_prev_year2_dec']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ur_half_prev_year2_avg']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ur_half_prev_year2_min']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ur_half_prev_year2_max']))?></td>
                                            </tr>

                                            <tr>
                                                <td><?=$year - 1?> YEE</td>
                                                <td class="text-right"><?=check_zero(number_format($price['ur_half_prev_year1_jan']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ur_half_prev_year1_feb']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ur_half_prev_year1_mar']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ur_half_prev_year1_apr']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ur_half_prev_year1_may']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ur_half_prev_year1_jun']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ur_half_prev_year1_jul']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ur_half_prev_year1_aug']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ur_half_prev_year1_sep']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ur_half_prev_year1_oct']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ur_half_prev_year1_nov']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ur_half_prev_year1_dec']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ur_half_prev_year1_avg']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ur_half_prev_year1_min']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ur_half_prev_year1_max']))?></td>
                                            </tr>

                                            <tr>
                                                <td><?=$year?> Budget</td>
                                                <td class="text-right"><?=check_zero(number_format($price['ur_half1']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ur_half2']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ur_half3']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ur_half4']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ur_half5']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ur_half6']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ur_half7']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ur_half8']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ur_half9']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ur_half10']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ur_half11']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ur_half12']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ur_half_avg']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ur_half_min']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ur_half_max']))?></td>
                                            </tr>

                                            <!-- UR LIEMPO -->
                                            <tr>
                                                <td rowspan="3" class="rowspan-center" style="vertical-align : middle;text-align:center;">LIEMPO</td>
                                                <td><?=$year - 2?> Actual</td>
                                                <td class="text-right"><?=check_zero(number_format($price['ur_liempo_prev_year2_jan']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ur_liempo_prev_year2_feb']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ur_liempo_prev_year2_mar']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ur_liempo_prev_year2_apr']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ur_liempo_prev_year2_may']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ur_liempo_prev_year2_jun']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ur_liempo_prev_year2_jul']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ur_liempo_prev_year2_aug']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ur_liempo_prev_year2_sep']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ur_liempo_prev_year2_oct']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ur_liempo_prev_year2_nov']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ur_liempo_prev_year2_dec']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ur_liempo_prev_year2_avg']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ur_liempo_prev_year2_min']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ur_liempo_prev_year2_max']))?></td>
                                            </tr>

                                            <tr>
                                                <td><?=$year - 1?> YEE</td>
                                                <td class="text-right"><?=check_zero(number_format($price['ur_liempo_prev_year1_jan']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ur_liempo_prev_year1_feb']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ur_liempo_prev_year1_mar']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ur_liempo_prev_year1_apr']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ur_liempo_prev_year1_may']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ur_liempo_prev_year1_jun']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ur_liempo_prev_year1_jul']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ur_liempo_prev_year1_aug']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ur_liempo_prev_year1_sep']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ur_liempo_prev_year1_oct']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ur_liempo_prev_year1_nov']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ur_liempo_prev_year1_dec']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ur_liempo_prev_year1_avg']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ur_liempo_prev_year1_min']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ur_liempo_prev_year1_max']))?></td>
                                            </tr>

                                            <tr>
                                                <td><?=$year?> Budget</td>
                                                <td class="text-right"><?=check_zero(number_format($price['ur_liempo1']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ur_liempo2']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ur_liempo3']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ur_liempo4']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ur_liempo5']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ur_liempo6']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ur_liempo7']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ur_liempo8']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ur_liempo9']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ur_liempo10']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ur_liempo11']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ur_liempo12']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ur_liempo_avg']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ur_liempo_min']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ur_liempo_max']))?></td>
                                            </tr>

                                            <!-- UR DRESSED -->
                                            <tr>
                                                <td rowspan="3" class="rowspan-center" style="vertical-align : middle;text-align:center;">DRESSED</td>
                                                <td><?=$year - 2?> Actual</td>
                                                <td class="text-right"><?=check_zero(number_format($price['ur_dressed_prev_year2_jan']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ur_dressed_prev_year2_feb']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ur_dressed_prev_year2_mar']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ur_dressed_prev_year2_apr']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ur_dressed_prev_year2_may']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ur_dressed_prev_year2_jun']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ur_dressed_prev_year2_jul']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ur_dressed_prev_year2_aug']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ur_dressed_prev_year2_sep']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ur_dressed_prev_year2_oct']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ur_dressed_prev_year2_nov']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ur_dressed_prev_year2_dec']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ur_dressed_prev_year2_avg']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ur_dressed_prev_year2_min']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ur_dressed_prev_year2_max']))?></td>
                                            </tr>

                                            <tr>
                                                <td><?=$year - 1?> YEE</td>
                                                <td class="text-right"><?=check_zero(number_format($price['ur_dressed_prev_year1_jan']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ur_dressed_prev_year1_feb']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ur_dressed_prev_year1_mar']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ur_dressed_prev_year1_apr']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ur_dressed_prev_year1_may']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ur_dressed_prev_year1_jun']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ur_dressed_prev_year1_jul']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ur_dressed_prev_year1_aug']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ur_dressed_prev_year1_sep']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ur_dressed_prev_year1_oct']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ur_dressed_prev_year1_nov']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ur_dressed_prev_year1_dec']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ur_dressed_prev_year1_avg']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ur_dressed_prev_year1_min']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ur_dressed_prev_year1_max']))?></td>
                                            </tr>

                                            <tr>
                                                <td><?=$year?> Budget</td>
                                                <td class="text-right"><?=check_zero(number_format($price['ur_dressed1']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ur_dressed2']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ur_dressed3']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ur_dressed4']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ur_dressed5']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ur_dressed6']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ur_dressed7']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ur_dressed8']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ur_dressed9']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ur_dressed10']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ur_dressed11']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ur_dressed12']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ur_dressed_avg']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ur_dressed_min']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ur_dressed_max']))?></td>
                                            </tr>

                                            <!-- UR CHOOKSIES MARINADO -->
                                            <tr>
                                                <td rowspan="3" class="rowspan-center" style="vertical-align : middle;text-align:center;">CHOOKSIES MARINADO</td>
                                                <td><?=$year - 2?> Actual</td>
                                                <td class="text-right"><?=check_zero(number_format($price['ur_chooksies_prev_year2_jan']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ur_chooksies_prev_year2_feb']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ur_chooksies_prev_year2_mar']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ur_chooksies_prev_year2_apr']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ur_chooksies_prev_year2_may']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ur_chooksies_prev_year2_jun']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ur_chooksies_prev_year2_jul']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ur_chooksies_prev_year2_aug']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ur_chooksies_prev_year2_sep']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ur_chooksies_prev_year2_oct']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ur_chooksies_prev_year2_nov']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ur_chooksies_prev_year2_dec']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ur_chooksies_prev_year2_avg']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ur_chooksies_prev_year2_min']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ur_chooksies_prev_year2_max']))?></td>
                                            </tr>

                                            <tr>
                                                <td><?=$year - 1?> YEE</td>
                                                <td class="text-right"><?=check_zero(number_format($price['ur_chooksies_prev_year1_jan']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ur_chooksies_prev_year1_feb']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ur_chooksies_prev_year1_mar']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ur_chooksies_prev_year1_apr']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ur_chooksies_prev_year1_may']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ur_chooksies_prev_year1_jun']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ur_chooksies_prev_year1_jul']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ur_chooksies_prev_year1_aug']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ur_chooksies_prev_year1_sep']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ur_chooksies_prev_year1_oct']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ur_chooksies_prev_year1_nov']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ur_chooksies_prev_year1_dec']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ur_chooksies_prev_year1_avg']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ur_chooksies_prev_year1_min']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ur_chooksies_prev_year1_max']))?></td>
                                            </tr>

                                            <tr>
                                                <td><?=$year?> Budget</td>
                                                <td class="text-right"><?=check_zero(number_format($price['ur_chooksies1']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ur_chooksies2']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ur_chooksies3']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ur_chooksies4']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ur_chooksies5']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ur_chooksies6']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ur_chooksies7']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ur_chooksies8']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ur_chooksies9']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ur_chooksies10']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ur_chooksies11']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ur_chooksies12']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ur_chooksies_avg']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ur_chooksies_min']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ur_chooksies_max']))?></td>
                                            </tr>

                                            <!-- UR MARINADO FRIED -->
                                            <tr>
                                                <td rowspan="3" class="rowspan-center" style="vertical-align : middle;text-align:center;">MARINADO FRIED</td>
                                                <td><?=$year - 2?> Actual</td>
                                                <td class="text-right"><?=check_zero(number_format($price['ur_marinado_prev_year2_jan']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ur_marinado_prev_year2_feb']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ur_marinado_prev_year2_mar']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ur_marinado_prev_year2_apr']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ur_marinado_prev_year2_may']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ur_marinado_prev_year2_jun']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ur_marinado_prev_year2_jul']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ur_marinado_prev_year2_aug']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ur_marinado_prev_year2_sep']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ur_marinado_prev_year2_oct']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ur_marinado_prev_year2_nov']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ur_marinado_prev_year2_dec']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ur_marinado_prev_year2_avg']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ur_marinado_prev_year2_min']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ur_marinado_prev_year2_max']))?></td>
                                            </tr>

                                            <tr>
                                                <td><?=$year - 1?> YEE</td>
                                                <td class="text-right"><?=check_zero(number_format($price['ur_marinado_prev_year1_jan']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ur_marinado_prev_year1_feb']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ur_marinado_prev_year1_mar']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ur_marinado_prev_year1_apr']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ur_marinado_prev_year1_may']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ur_marinado_prev_year1_jun']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ur_marinado_prev_year1_jul']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ur_marinado_prev_year1_aug']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ur_marinado_prev_year1_sep']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ur_marinado_prev_year1_oct']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ur_marinado_prev_year1_nov']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ur_marinado_prev_year1_dec']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ur_marinado_prev_year1_avg']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ur_marinado_prev_year1_min']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ur_marinado_prev_year1_max']))?></td>
                                            </tr>

                                            <tr>
                                                <td><?=$year?> Budget</td>
                                                <td class="text-right"><?=check_zero(number_format($price['ur_marinado1']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ur_marinado2']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ur_marinado3']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ur_marinado4']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ur_marinado5']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ur_marinado6']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ur_marinado7']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ur_marinado8']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ur_marinado9']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ur_marinado10']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ur_marinado11']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ur_marinado12']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ur_marinado_avg']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ur_marinado_min']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ur_marinado_max']))?></td>
                                            </tr>

                                            <!-- UR SPICY NECK -->
                                            <tr>
                                                <td rowspan="3" class="rowspan-center" style="vertical-align : middle;text-align:center;">SPICY NECK</td>
                                                <td><?=$year - 2?> Actual</td>
                                                <td class="text-right"><?=check_zero(number_format($price['ur_spicy_prev_year2_jan']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ur_spicy_prev_year2_feb']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ur_spicy_prev_year2_mar']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ur_spicy_prev_year2_apr']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ur_spicy_prev_year2_may']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ur_spicy_prev_year2_jun']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ur_spicy_prev_year2_jul']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ur_spicy_prev_year2_aug']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ur_spicy_prev_year2_sep']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ur_spicy_prev_year2_oct']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ur_spicy_prev_year2_nov']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ur_spicy_prev_year2_dec']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ur_spicy_prev_year2_avg']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ur_spicy_prev_year2_min']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ur_spicy_prev_year2_max']))?></td>
                                            </tr>

                                            <tr>
                                                <td><?=$year - 1?> YEE</td>
                                                <td class="text-right"><?=check_zero(number_format($price['ur_spicy_prev_year1_jan']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ur_spicy_prev_year1_feb']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ur_spicy_prev_year1_mar']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ur_spicy_prev_year1_apr']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ur_spicy_prev_year1_may']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ur_spicy_prev_year1_jun']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ur_spicy_prev_year1_jul']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ur_spicy_prev_year1_aug']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ur_spicy_prev_year1_sep']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ur_spicy_prev_year1_oct']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ur_spicy_prev_year1_nov']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ur_spicy_prev_year1_dec']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ur_spicy_prev_year1_avg']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ur_spicy_prev_year1_min']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ur_spicy_prev_year1_max']))?></td>
                                            </tr>

                                            <tr>
                                                <td><?=$year?> Budget</td>
                                                <td class="text-right"><?=check_zero(number_format($price['ur_spicy1']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ur_spicy2']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ur_spicy3']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ur_spicy4']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ur_spicy5']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ur_spicy6']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ur_spicy7']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ur_spicy8']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ur_spicy9']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ur_spicy10']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ur_spicy11']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ur_spicy12']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ur_spicy_avg']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ur_spicy_min']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ur_spicy_max']))?></td>
                                            </tr>

                                            <!-- UR NUGGETS -->
                                            <tr>
                                                <td rowspan="3" class="rowspan-center" style="vertical-align : middle;text-align:center;">VAP-NUGGETS</td>
                                                <td><?=$year - 2?> Actual</td>
                                                <td class="text-right"><?=check_zero(number_format($price['ur_nuggets_prev_year2_jan']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ur_nuggets_prev_year2_feb']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ur_nuggets_prev_year2_mar']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ur_nuggets_prev_year2_apr']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ur_nuggets_prev_year2_may']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ur_nuggets_prev_year2_jun']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ur_nuggets_prev_year2_jul']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ur_nuggets_prev_year2_aug']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ur_nuggets_prev_year2_sep']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ur_nuggets_prev_year2_oct']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ur_nuggets_prev_year2_nov']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ur_nuggets_prev_year2_dec']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ur_nuggets_prev_year2_avg']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ur_nuggets_prev_year2_min']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ur_nuggets_prev_year2_max']))?></td>
                                            </tr>

                                            <tr>
                                                <td><?=$year - 1?> YEE</td>
                                                <td class="text-right"><?=check_zero(number_format($price['ur_nuggets_prev_year1_jan']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ur_nuggets_prev_year1_feb']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ur_nuggets_prev_year1_mar']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ur_nuggets_prev_year1_apr']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ur_nuggets_prev_year1_may']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ur_nuggets_prev_year1_jun']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ur_nuggets_prev_year1_jul']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ur_nuggets_prev_year1_aug']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ur_nuggets_prev_year1_sep']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ur_nuggets_prev_year1_oct']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ur_nuggets_prev_year1_nov']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ur_nuggets_prev_year1_dec']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ur_nuggets_prev_year1_avg']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ur_nuggets_prev_year1_min']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ur_nuggets_prev_year1_max']))?></td>
                                            </tr>

                                            <tr>
                                                <td><?=$year?> Budget</td>
                                                <td class="text-right"><?=check_zero(number_format($price['ur_nuggets1']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ur_nuggets2']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ur_nuggets3']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ur_nuggets4']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ur_nuggets5']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ur_nuggets6']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ur_nuggets7']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ur_nuggets8']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ur_nuggets9']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ur_nuggets10']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ur_nuggets11']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ur_nuggets12']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ur_nuggets_avg']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ur_nuggets_min']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ur_nuggets_max']))?></td>
                                            </tr>

                                            <!-- UR 11 PC -->
                                            <tr>
                                                <td rowspan="3" class="rowspan-center" style="vertical-align : middle;text-align:center;">11 PC</td>
                                                <td><?=$year - 2?> Actual</td>
                                                <td class="text-right"><?=check_zero(number_format($price['ur_pica_prev_year2_jan']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ur_pica_prev_year2_feb']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ur_pica_prev_year2_mar']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ur_pica_prev_year2_apr']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ur_pica_prev_year2_may']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ur_pica_prev_year2_jun']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ur_pica_prev_year2_jul']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ur_pica_prev_year2_aug']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ur_pica_prev_year2_sep']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ur_pica_prev_year2_oct']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ur_pica_prev_year2_nov']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ur_pica_prev_year2_dec']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ur_pica_prev_year2_avg']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ur_pica_prev_year2_min']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ur_pica_prev_year2_max']))?></td>
                                            </tr>

                                            <tr>
                                                <td><?=$year - 1?> YEE</td>
                                                <td class="text-right"><?=check_zero(number_format($price['ur_pica_prev_year1_jan']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ur_pica_prev_year1_feb']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ur_pica_prev_year1_mar']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ur_pica_prev_year1_apr']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ur_pica_prev_year1_may']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ur_pica_prev_year1_jun']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ur_pica_prev_year1_jul']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ur_pica_prev_year1_aug']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ur_pica_prev_year1_sep']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ur_pica_prev_year1_oct']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ur_pica_prev_year1_nov']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ur_pica_prev_year1_dec']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ur_pica_prev_year1_avg']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ur_pica_prev_year1_min']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ur_pica_prev_year1_max']))?></td>
                                            </tr>

                                            <tr>
                                                <td><?=$year?> Budget</td>
                                                <td class="text-right"><?=check_zero(number_format($price['ur_pica1']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ur_pica2']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ur_pica3']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ur_pica4']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ur_pica5']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ur_pica6']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ur_pica7']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ur_pica8']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ur_pica9']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ur_pica10']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ur_pica11']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ur_pica12']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ur_pica_avg']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ur_pica_min']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ur_pica_max']))?></td>
                                            </tr>

                                            <!-- UR 5 PC -->
                                            <tr>
                                                <td rowspan="3" class="rowspan-center" style="vertical-align : middle;text-align:center;">5 PC</td>
                                                <td><?=$year - 2?> Actual</td>
                                                <td class="text-right"><?=check_zero(number_format($price['ur_bossing_prev_year2_jan']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ur_bossing_prev_year2_feb']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ur_bossing_prev_year2_mar']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ur_bossing_prev_year2_apr']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ur_bossing_prev_year2_may']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ur_bossing_prev_year2_jun']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ur_bossing_prev_year2_jul']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ur_bossing_prev_year2_aug']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ur_bossing_prev_year2_sep']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ur_bossing_prev_year2_oct']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ur_bossing_prev_year2_nov']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ur_bossing_prev_year2_dec']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ur_bossing_prev_year2_avg']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ur_bossing_prev_year2_min']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ur_bossing_prev_year2_max']))?></td>
                                            </tr>

                                            <tr>
                                                <td><?=$year - 1?> YEE</td>
                                                <td class="text-right"><?=check_zero(number_format($price['ur_bossing_prev_year1_jan']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ur_bossing_prev_year1_feb']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ur_bossing_prev_year1_mar']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ur_bossing_prev_year1_apr']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ur_bossing_prev_year1_may']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ur_bossing_prev_year1_jun']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ur_bossing_prev_year1_jul']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ur_bossing_prev_year1_aug']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ur_bossing_prev_year1_sep']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ur_bossing_prev_year1_oct']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ur_bossing_prev_year1_nov']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ur_bossing_prev_year1_dec']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ur_bossing_prev_year1_avg']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ur_bossing_prev_year1_min']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ur_bossing_prev_year1_max']))?></td>
                                            </tr>

                                            <tr>
                                                <td><?=$year?> Budget</td>
                                                <td class="text-right"><?=check_zero(number_format($price['ur_bossing1']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ur_bossing2']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ur_bossing3']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ur_bossing4']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ur_bossing5']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ur_bossing6']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ur_bossing7']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ur_bossing8']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ur_bossing9']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ur_bossing10']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ur_bossing11']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ur_bossing12']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ur_bossing_avg']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ur_bossing_min']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ur_bossing_max']))?></td>
                                            </tr>

                                            <!-- UR CHOOKSIES CUT UPS -->
                                            <tr>
                                                <td rowspan="3" class="rowspan-center" style="vertical-align : middle;text-align:center;">CHOOKSIES CUTUPS</td>
                                                <td><?=$year - 2?> Actual</td>
                                                <td class="text-right"><?=check_zero(number_format($price['ur_cutups_prev_year2_jan']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ur_cutups_prev_year2_feb']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ur_cutups_prev_year2_mar']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ur_cutups_prev_year2_apr']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ur_cutups_prev_year2_may']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ur_cutups_prev_year2_jun']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ur_cutups_prev_year2_jul']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ur_cutups_prev_year2_aug']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ur_cutups_prev_year2_sep']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ur_cutups_prev_year2_oct']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ur_cutups_prev_year2_nov']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ur_cutups_prev_year2_dec']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ur_cutups_prev_year2_avg']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ur_cutups_prev_year2_min']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ur_cutups_prev_year2_max']))?></td>
                                            </tr>

                                            <tr>
                                                <td><?=$year - 1?> YEE</td>
                                                <td class="text-right"><?=check_zero(number_format($price['ur_cutups_prev_year1_jan']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ur_cutups_prev_year1_feb']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ur_cutups_prev_year1_mar']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ur_cutups_prev_year1_apr']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ur_cutups_prev_year1_may']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ur_cutups_prev_year1_jun']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ur_cutups_prev_year1_jul']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ur_cutups_prev_year1_aug']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ur_cutups_prev_year1_sep']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ur_cutups_prev_year1_oct']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ur_cutups_prev_year1_nov']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ur_cutups_prev_year1_dec']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ur_cutups_prev_year1_avg']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ur_cutups_prev_year1_min']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ur_cutups_prev_year1_max']))?></td>
                                            </tr>

                                           <tr>
                                                <td><?=$year?> Budget</td>
                                                <td class="text-right"><?=check_zero(number_format($price['ur_cutups1']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ur_cutups2']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ur_cutups3']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ur_cutups4']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ur_cutups5']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ur_cutups6']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ur_cutups7']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ur_cutups8']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ur_cutups9']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ur_cutups10']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ur_cutups11']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ur_cutups12']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ur_cutups_avg']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ur_cutups_min']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ur_cutups_max']))?></td>
                                            </tr>

                                            <!-- UR GIZZARD / LIVER -->
                                            <tr>
                                                <td rowspan="3" class="rowspan-center" style="vertical-align : middle;text-align:center;">GIZZARD / LIVER</td>
                                                <td><?=$year - 2?> Actual</td>
                                                <td class="text-right"><?=check_zero(number_format($price['ur_liver_prev_year2_jan']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ur_liver_prev_year2_feb']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ur_liver_prev_year2_mar']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ur_liver_prev_year2_apr']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ur_liver_prev_year2_may']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ur_liver_prev_year2_jun']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ur_liver_prev_year2_jul']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ur_liver_prev_year2_aug']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ur_liver_prev_year2_sep']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ur_liver_prev_year2_oct']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ur_liver_prev_year2_nov']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ur_liver_prev_year2_dec']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ur_liver_prev_year2_avg']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ur_liver_prev_year2_min']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ur_liver_prev_year2_max']))?></td>
                                            </tr>

                                            <tr>
                                                <td><?=$year - 1?> YEE</td>
                                                <td class="text-right"><?=check_zero(number_format($price['ur_liver_prev_year1_jan']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ur_liver_prev_year1_feb']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ur_liver_prev_year1_mar']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ur_liver_prev_year1_apr']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ur_liver_prev_year1_may']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ur_liver_prev_year1_jun']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ur_liver_prev_year1_jul']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ur_liver_prev_year1_aug']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ur_liver_prev_year1_sep']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ur_liver_prev_year1_oct']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ur_liver_prev_year1_nov']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ur_liver_prev_year1_dec']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ur_liver_prev_year1_avg']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ur_liver_prev_year1_min']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ur_liver_prev_year1_max']))?></td>
                                            </tr>

                                            <tr>
                                                <td><?=$year?> Budget</td>
                                                <td class="text-right"><?=check_zero(number_format($price['ur_liver1']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ur_liver2']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ur_liver3']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ur_liver4']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ur_liver5']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ur_liver6']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ur_liver7']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ur_liver8']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ur_liver9']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ur_liver10']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ur_liver11']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ur_liver12']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ur_liver_avg']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ur_liver_min']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['ur_liver_max']))?></td>
                                            </tr>

                                            <!-- SUPERMARKET LIVER / GIZZARD -->
                                            <tr>
                                                <td rowspan="15" class="rowspan-center" style="vertical-align : middle;text-align:center;">COM</td>
                                                <td rowspan="3" class="rowspan-center" style="vertical-align : middle;text-align:center;">SMKT - LIVER / GIZZARD</td>
                                                <td><?=$year - 2?> Actual</td>
                                                <td class="text-right"><?=check_zero(number_format($price['smkt_liver_prev_year2_jan']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['smkt_liver_prev_year2_feb']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['smkt_liver_prev_year2_mar']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['smkt_liver_prev_year2_apr']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['smkt_liver_prev_year2_may']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['smkt_liver_prev_year2_jun']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['smkt_liver_prev_year2_jul']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['smkt_liver_prev_year2_aug']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['smkt_liver_prev_year2_sep']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['smkt_liver_prev_year2_oct']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['smkt_liver_prev_year2_nov']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['smkt_liver_prev_year2_dec']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['smkt_liver_prev_year2_avg']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['smkt_liver_prev_year2_min']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['smkt_liver_prev_year2_max']))?></td>
                                            </tr>

                                            <tr>
                                                <td><?=$year - 1?> YEE</td>
                                                <td class="text-right"><?=check_zero(number_format($price['smkt_liver_prev_year1_jan']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['smkt_liver_prev_year1_feb']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['smkt_liver_prev_year1_mar']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['smkt_liver_prev_year1_apr']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['smkt_liver_prev_year1_may']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['smkt_liver_prev_year1_jun']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['smkt_liver_prev_year1_jul']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['smkt_liver_prev_year1_aug']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['smkt_liver_prev_year1_sep']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['smkt_liver_prev_year1_oct']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['smkt_liver_prev_year1_nov']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['smkt_liver_prev_year1_dec']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['smkt_liver_prev_year1_avg']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['smkt_liver_prev_year1_min']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['smkt_liver_prev_year1_max']))?></td>
                                            </tr>

                                            <tr>
                                                <td><?=$year?> Budget</td>
                                                <td class="text-right"><?=check_zero(number_format($price['smkt_liver1']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['smkt_liver2']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['smkt_liver3']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['smkt_liver4']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['smkt_liver5']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['smkt_liver6']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['smkt_liver7']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['smkt_liver8']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['smkt_liver9']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['smkt_liver10']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['smkt_liver11']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['smkt_liver12']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['smkt_liver_avg']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['smkt_liver_min']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['smkt_liver_max']))?></td>
                                            </tr>

                                            <!-- SUPERMARKET MARINATED CHICKEN RAW -->
                                            <tr>
                                                <td rowspan="3" class="rowspan-center" style="vertical-align : middle;text-align:center;">SMKT - MARINATED CHICKEN RAW</td>
                                                <td><?=$year - 2?> Actual</td>
                                                <td class="text-right"><?=check_zero(number_format($price['smkt_raw_prev_year2_jan']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['smkt_raw_prev_year2_feb']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['smkt_raw_prev_year2_mar']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['smkt_raw_prev_year2_apr']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['smkt_raw_prev_year2_may']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['smkt_raw_prev_year2_jun']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['smkt_raw_prev_year2_jul']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['smkt_raw_prev_year2_aug']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['smkt_raw_prev_year2_sep']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['smkt_raw_prev_year2_oct']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['smkt_raw_prev_year2_nov']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['smkt_raw_prev_year2_dec']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['smkt_raw_prev_year2_avg']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['smkt_raw_prev_year2_min']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['smkt_raw_prev_year2_max']))?></td>
                                            </tr>

                                            <tr>
                                                <td><?=$year - 1?> YEE</td>
                                                <td class="text-right"><?=check_zero(number_format($price['smkt_raw_prev_year1_jan']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['smkt_raw_prev_year1_feb']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['smkt_raw_prev_year1_mar']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['smkt_raw_prev_year1_apr']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['smkt_raw_prev_year1_may']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['smkt_raw_prev_year1_jun']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['smkt_raw_prev_year1_jul']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['smkt_raw_prev_year1_aug']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['smkt_raw_prev_year1_sep']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['smkt_raw_prev_year1_oct']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['smkt_raw_prev_year1_nov']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['smkt_raw_prev_year1_dec']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['smkt_raw_prev_year1_avg']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['smkt_raw_prev_year1_min']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['smkt_raw_prev_year1_max']))?></td>
                                            </tr>

                                            <tr>
                                                <td><?=$year?> Budget</td>
                                                <td class="text-right"><?=check_zero(number_format($price['smkt_raw1']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['smkt_raw2']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['smkt_raw3']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['smkt_raw4']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['smkt_raw5']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['smkt_raw6']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['smkt_raw7']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['smkt_raw8']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['smkt_raw9']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['smkt_raw10']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['smkt_raw11']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['smkt_raw12']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['smkt_raw_avg']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['smkt_raw_min']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['smkt_raw_max']))?></td>
                                            </tr>

                                            <!-- SUPERMARKET MARINATED CUT UPS -->
                                            <tr>
                                                <td rowspan="3" class="rowspan-center" style="vertical-align : middle;text-align:center;">SMKT - MAR CUT UPS</td>
                                                <td><?=$year - 2?> Actual</td>
                                                <td class="text-right"><?=check_zero(number_format($price['smkt_marinated_prev_year2_jan']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['smkt_marinated_prev_year2_feb']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['smkt_marinated_prev_year2_mar']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['smkt_marinated_prev_year2_apr']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['smkt_marinated_prev_year2_may']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['smkt_marinated_prev_year2_jun']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['smkt_marinated_prev_year2_jul']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['smkt_marinated_prev_year2_aug']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['smkt_marinated_prev_year2_sep']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['smkt_marinated_prev_year2_oct']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['smkt_marinated_prev_year2_nov']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['smkt_marinated_prev_year2_dec']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['smkt_marinated_prev_year2_avg']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['smkt_marinated_prev_year2_min']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['smkt_marinated_prev_year2_max']))?></td>
                                            </tr>

                                            <tr>
                                                <td><?=$year - 1?> YEE</td>
                                                <td class="text-right"><?=check_zero(number_format($price['smkt_marinated_prev_year1_jan']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['smkt_marinated_prev_year1_feb']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['smkt_marinated_prev_year1_mar']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['smkt_marinated_prev_year1_apr']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['smkt_marinated_prev_year1_may']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['smkt_marinated_prev_year1_jun']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['smkt_marinated_prev_year1_jul']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['smkt_marinated_prev_year1_aug']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['smkt_marinated_prev_year1_sep']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['smkt_marinated_prev_year1_oct']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['smkt_marinated_prev_year1_nov']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['smkt_marinated_prev_year1_dec']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['smkt_marinated_prev_year1_avg']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['smkt_marinated_prev_year1_min']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['smkt_marinated_prev_year1_max']))?></td>
                                            </tr>

                                            <tr>
                                                <td><?=$year?> Budget</td>
                                                <td class="text-right"><?=check_zero(number_format($price['smkt_marinated1']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['smkt_marinated2']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['smkt_marinated3']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['smkt_marinated4']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['smkt_marinated5']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['smkt_marinated6']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['smkt_marinated7']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['smkt_marinated8']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['smkt_marinated9']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['smkt_marinated10']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['smkt_marinated11']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['smkt_marinated12']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['smkt_marinated_avg']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['smkt_marinated_min']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['smkt_marinated_max']))?></td>
                                            </tr>

                                            <!-- TRADE DISTRIBUTOR LIVER / GIZZARD -->
                                            <tr>
                                                <td rowspan="3" class="rowspan-center" style="vertical-align : middle;text-align:center;">TDs - LIVER / GIZZARD</td>
                                                <td><?=$year - 2?> Actual</td>
                                                <td class="text-right"><?=check_zero(number_format($price['tds_liver_prev_year2_jan']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['tds_liver_prev_year2_feb']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['tds_liver_prev_year2_mar']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['tds_liver_prev_year2_apr']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['tds_liver_prev_year2_may']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['tds_liver_prev_year2_jun']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['tds_liver_prev_year2_jul']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['tds_liver_prev_year2_aug']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['tds_liver_prev_year2_sep']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['tds_liver_prev_year2_oct']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['tds_liver_prev_year2_nov']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['tds_liver_prev_year2_dec']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['tds_liver_prev_year2_avg']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['tds_liver_prev_year2_min']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['tds_liver_prev_year2_max']))?></td>
                                            </tr>

                                            <tr>
                                                <td><?=$year - 1?> YEE</td>
                                                <td class="text-right"><?=check_zero(number_format($price['tds_liver_prev_year1_jan']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['tds_liver_prev_year1_feb']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['tds_liver_prev_year1_mar']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['tds_liver_prev_year1_apr']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['tds_liver_prev_year1_may']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['tds_liver_prev_year1_jun']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['tds_liver_prev_year1_jul']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['tds_liver_prev_year1_aug']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['tds_liver_prev_year1_sep']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['tds_liver_prev_year1_oct']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['tds_liver_prev_year1_nov']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['tds_liver_prev_year1_dec']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['tds_liver_prev_year1_avg']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['tds_liver_prev_year1_min']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['tds_liver_prev_year1_max']))?></td>
                                            </tr>

                                            <tr>
                                                <td><?=$year?> Budget</td>
                                                <td class="text-right"><?=check_zero(number_format($price['tds_liver1']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['tds_liver2']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['tds_liver3']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['tds_liver4']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['tds_liver5']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['tds_liver6']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['tds_liver7']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['tds_liver8']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['tds_liver9']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['tds_liver10']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['tds_liver11']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['tds_liver12']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['tds_liver_avg']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['tds_liver_min']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['tds_liver_max']))?></td>
                                            </tr>

                                            <!-- TRADE DISTRIBUTOR MARINATED CHICKEN RAW -->
                                            <tr>
                                                <td rowspan="3" class="rowspan-center" style="vertical-align : middle;text-align:center;">TDs - MARINATED CHICKEN RAW</td>
                                                <td><?=$year - 2?> Actual</td>
                                                <td class="text-right"><?=check_zero(number_format($price['tds_raw_prev_year2_jan']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['tds_raw_prev_year2_feb']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['tds_raw_prev_year2_mar']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['tds_raw_prev_year2_apr']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['tds_raw_prev_year2_may']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['tds_raw_prev_year2_jun']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['tds_raw_prev_year2_jul']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['tds_raw_prev_year2_aug']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['tds_raw_prev_year2_sep']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['tds_raw_prev_year2_oct']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['tds_raw_prev_year2_nov']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['tds_raw_prev_year2_dec']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['tds_raw_prev_year2_avg']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['tds_raw_prev_year2_min']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['tds_raw_prev_year2_max']))?></td>
                                            </tr>

                                            <tr>
                                                <td><?=$year - 1?> YEE</td>
                                                <td class="text-right"><?=check_zero(number_format($price['tds_raw_prev_year1_jan']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['tds_raw_prev_year1_feb']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['tds_raw_prev_year1_mar']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['tds_raw_prev_year1_apr']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['tds_raw_prev_year1_may']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['tds_raw_prev_year1_jun']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['tds_raw_prev_year1_jul']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['tds_raw_prev_year1_aug']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['tds_raw_prev_year1_sep']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['tds_raw_prev_year1_oct']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['tds_raw_prev_year1_nov']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['tds_raw_prev_year1_dec']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['tds_raw_prev_year1_avg']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['tds_raw_prev_year1_min']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['tds_raw_prev_year1_max']))?></td>
                                            </tr>

                                            <tr>
                                                <td><?=$year?> Budget</td>
                                                <td class="text-right"><?=check_zero(number_format($price['tds_raw1']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['tds_raw2']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['tds_raw3']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['tds_raw4']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['tds_raw5']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['tds_raw6']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['tds_raw7']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['tds_raw8']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['tds_raw9']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['tds_raw10']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['tds_raw11']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['tds_raw12']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['tds_raw_avg']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['tds_raw_min']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['tds_raw_max']))?></td>
                                            </tr>

                                            <!-- RSL -->

                                            <!-- RSL ORC REGULAR -->
                                            <tr>
                                                <td rowspan="48" class="rowspan-center" style="vertical-align : middle;text-align:center;">RSL</td>
                                                <td rowspan="3" class="rowspan-center" style="vertical-align : middle;text-align:center;">ORC - REGULAR</td>
                                                <td><?=$year - 2?> Actual</td>
                                                <td align="center"><?=check_zero(number_format($price['rsl_reg1_year2']))?></td>
                                                <td align="center"><?=check_zero(number_format($price['rsl_reg2_year2']))?></td>
                                                <td align="center"><?=check_zero(number_format($price['rsl_reg3_year2']))?></td>
                                                <td align="center"><?=check_zero(number_format($price['rsl_reg4_year2']))?></td>
                                                <td align="center"><?=check_zero(number_format($price['rsl_reg5_year2']))?></td>
                                                <td align="center"><?=check_zero(number_format($price['rsl_reg6_year2']))?></td>
                                                <td align="center"><?=check_zero(number_format($price['rsl_reg7_year2']))?></td>
                                                <td align="center"><?=check_zero(number_format($price['rsl_reg8_year2']))?></td>
                                                <td align="center"><?=check_zero(number_format($price['rsl_reg9_year2']))?></td>
                                                <td align="center"><?=check_zero(number_format($price['rsl_reg10_year2']))?></td>
                                                <td align="center"><?=check_zero(number_format($price['rsl_reg11_year2']))?></td>
                                                <td align="center"><?=check_zero(number_format($price['rsl_reg12_year2']))?></td>
                                                <td align="center"><?=check_zero(number_format($price['rsl_reg_avg_year2']))?></td>
                                                <td align="center"><?=check_zero(number_format($price['rsl_reg_min_year2']))?></td>
                                                <td align="center"><?=check_zero(number_format($price['rsl_reg_max_year2']))?></td>
                                            </tr>
                                            <tr>
                                                <td><?=$year - 1?> YEE</td>
                                                <td align="center"><?=check_zero(number_format($price['rsl_reg1_year1']))?></td>
                                                <td align="center"><?=check_zero(number_format($price['rsl_reg2_year1']))?></td>
                                                <td align="center"><?=check_zero(number_format($price['rsl_reg3_year1']))?></td>
                                                <td align="center"><?=check_zero(number_format($price['rsl_reg4_year1']))?></td>
                                                <td align="center"><?=check_zero(number_format($price['rsl_reg5_year1']))?></td>
                                                <td align="center"><?=check_zero(number_format($price['rsl_reg6_year1']))?></td>
                                                <td align="center"><?=check_zero(number_format($price['rsl_reg7_year1']))?></td>
                                                <td align="center"><?=check_zero(number_format($price['rsl_reg8_year1']))?></td>
                                                <td align="center"><?=check_zero(number_format($price['rsl_reg9_year1']))?></td>
                                                <td align="center"><?=check_zero(number_format($price['rsl_reg10_year1']))?></td>
                                                <td align="center"><?=check_zero(number_format($price['rsl_reg11_year1']))?></td>
                                                <td align="center"><?=check_zero(number_format($price['rsl_reg12_year1']))?></td>
                                                <td align="center"><?=check_zero(number_format($price['rsl_reg_avg_year1']))?></td>
                                                <td align="center"><?=check_zero(number_format($price['rsl_reg_min_year1']))?></td>
                                                <td align="center"><?=check_zero(number_format($price['rsl_reg_max_year1']))?></td>
                                            </tr>
                                            <tr>
                                                <td><?=$year?> Budget</td>
                                                <td align="center"><?=check_zero(number_format($price['rsl_reg1']))?></td>
                                                <td align="center"><?=check_zero(number_format($price['rsl_reg2']))?></td>
                                                <td align="center"><?=check_zero(number_format($price['rsl_reg3']))?></td>
                                                <td align="center"><?=check_zero(number_format($price['rsl_reg4']))?></td>
                                                <td align="center"><?=check_zero(number_format($price['rsl_reg5']))?></td>
                                                <td align="center"><?=check_zero(number_format($price['rsl_reg6']))?></td>
                                                <td align="center"><?=check_zero(number_format($price['rsl_reg7']))?></td>
                                                <td align="center"><?=check_zero(number_format($price['rsl_reg8']))?></td>
                                                <td align="center"><?=check_zero(number_format($price['rsl_reg9']))?></td>
                                                <td align="center"><?=check_zero(number_format($price['rsl_reg10']))?></td>
                                                <td align="center"><?=check_zero(number_format($price['rsl_reg11']))?></td>
                                                <td align="center"><?=check_zero(number_format($price['rsl_reg12']))?></td>
                                                <td align="center"><?=check_zero(number_format($price['rsl_reg_avg']))?></td>
                                                <td align="center"><?=check_zero(number_format($price['rsl_reg_min']))?></td>
                                                <td align="center"><?=check_zero(number_format($price['rsl_reg_max']))?></td>
                                            </tr>

                                            <!-- RSL ORC JUMBO -->
                                            <tr>
                                                <td rowspan="3" class="rowspan-center" style="vertical-align : middle;text-align:center;">ORC - JUMBO</td>
                                                <td><?=$year - 2?> Actual</td>
                                                <td align="center"><?=check_zero(number_format($price['rsl_jbo1_year2']))?></td>
                                                <td align="center"><?=check_zero(number_format($price['rsl_jbo2_year2']))?></td>
                                                <td align="center"><?=check_zero(number_format($price['rsl_jbo3_year2']))?></td>
                                                <td align="center"><?=check_zero(number_format($price['rsl_jbo4_year2']))?></td>
                                                <td align="center"><?=check_zero(number_format($price['rsl_jbo5_year2']))?></td>
                                                <td align="center"><?=check_zero(number_format($price['rsl_jbo6_year2']))?></td>
                                                <td align="center"><?=check_zero(number_format($price['rsl_jbo7_year2']))?></td>
                                                <td align="center"><?=check_zero(number_format($price['rsl_jbo8_year2']))?></td>
                                                <td align="center"><?=check_zero(number_format($price['rsl_jbo9_year2']))?></td>
                                                <td align="center"><?=check_zero(number_format($price['rsl_jbo10_year2']))?></td>
                                                <td align="center"><?=check_zero(number_format($price['rsl_jbo11_year2']))?></td>
                                                <td align="center"><?=check_zero(number_format($price['rsl_jbo12_year2']))?></td>
                                                <td align="center"><?=check_zero(number_format($price['rsl_jbo_year2_avg']))?></td>
                                                <td align="center"><?=check_zero(number_format($price['rsl_jbo_year2_min']))?></td>
                                                <td align="center"><?=check_zero(number_format($price['rsl_jbo_year2_max']))?></td>
                                            </tr>

                                            <tr>
                                                <td><?=$year - 1?> YEE</td>
                                                <td align="center"><?=check_zero(number_format($price['rsl_jbo1_year1']))?></td>
                                                <td align="center"><?=check_zero(number_format($price['rsl_jbo2_year1']))?></td>
                                                <td align="center"><?=check_zero(number_format($price['rsl_jbo3_year1']))?></td>
                                                <td align="center"><?=check_zero(number_format($price['rsl_jbo4_year1']))?></td>
                                                <td align="center"><?=check_zero(number_format($price['rsl_jbo5_year1']))?></td>
                                                <td align="center"><?=check_zero(number_format($price['rsl_jbo6_year1']))?></td>
                                                <td align="center"><?=check_zero(number_format($price['rsl_jbo7_year1']))?></td>
                                                <td align="center"><?=check_zero(number_format($price['rsl_jbo8_year1']))?></td>
                                                <td align="center"><?=check_zero(number_format($price['rsl_jbo9_year1']))?></td>
                                                <td align="center"><?=check_zero(number_format($price['rsl_jbo10_year1']))?></td>
                                                <td align="center"><?=check_zero(number_format($price['rsl_jbo11_year1']))?></td>
                                                <td align="center"><?=check_zero(number_format($price['rsl_jbo12_year1']))?></td>
                                                <td align="center"><?=check_zero(number_format($price['rsl_jbo_year1_avg']))?></td>
                                                <td align="center"><?=check_zero(number_format($price['rsl_jbo_year1_min']))?></td>
                                                <td align="center"><?=check_zero(number_format($price['rsl_jbo_year1_max']))?></td>
                                            </tr>

                                            <tr>
                                                <td><?=$year?> Budget</td>
                                                <td align="center"><?=check_zero(number_format($price['rsl_jbo1']))?></td>
                                                <td align="center"><?=check_zero(number_format($price['rsl_jbo2']))?></td>
                                                <td align="center"><?=check_zero(number_format($price['rsl_jbo3']))?></td>
                                                <td align="center"><?=check_zero(number_format($price['rsl_jbo4']))?></td>
                                                <td align="center"><?=check_zero(number_format($price['rsl_jbo5']))?></td>
                                                <td align="center"><?=check_zero(number_format($price['rsl_jbo6']))?></td>
                                                <td align="center"><?=check_zero(number_format($price['rsl_jbo7']))?></td>
                                                <td align="center"><?=check_zero(number_format($price['rsl_jbo8']))?></td>
                                                <td align="center"><?=check_zero(number_format($price['rsl_jbo9']))?></td>
                                                <td align="center"><?=check_zero(number_format($price['rsl_jbo10']))?></td>
                                                <td align="center"><?=check_zero(number_format($price['rsl_jbo11']))?></td>
                                                <td align="center"><?=check_zero(number_format($price['rsl_jbo12']))?></td>
                                                <td align="center"><?=check_zero(number_format($price['rsl_jbo_avg']))?></td>
                                                <td align="center"><?=check_zero(number_format($price['rsl_jbo_min']))?></td>
                                                <td align="center"><?=check_zero(number_format($price['rsl_jbo_max']))?></td>
                                            </tr>

                                            <!-- RSL ORC SUPERSIZE -->
                                            <tr>
                                                <td rowspan="3" class="rowspan-center" style="vertical-align : middle;text-align:center;">ORC - SUPERSIZE</td>
                                                <td><?=$year - 2?> Actual</td>
                                                <td align="center"><?=check_zero(number_format($price['rsl_ss1_year2']))?></td>
                                                <td align="center"><?=check_zero(number_format($price['rsl_ss2_year2']))?></td>
                                                <td align="center"><?=check_zero(number_format($price['rsl_ss3_year2']))?></td>
                                                <td align="center"><?=check_zero(number_format($price['rsl_ss4_year2']))?></td>
                                                <td align="center"><?=check_zero(number_format($price['rsl_ss5_year2']))?></td>
                                                <td align="center"><?=check_zero(number_format($price['rsl_ss6_year2']))?></td>
                                                <td align="center"><?=check_zero(number_format($price['rsl_ss7_year2']))?></td>
                                                <td align="center"><?=check_zero(number_format($price['rsl_ss8_year2']))?></td>
                                                <td align="center"><?=check_zero(number_format($price['rsl_ss9_year2']))?></td>
                                                <td align="center"><?=check_zero(number_format($price['rsl_ss10_year2']))?></td>
                                                <td align="center"><?=check_zero(number_format($price['rsl_ss11_year2']))?></td>
                                                <td align="center"><?=check_zero(number_format($price['rsl_ss12_year2']))?></td>
                                                <td align="center"><?=check_zero(number_format($price['rsl_ss_avg_year2']))?></td>
                                                <td align="center"><?=check_zero(number_format($price['rsl_ss_min_year2']))?></td>
                                                <td align="center"><?=check_zero(number_format($price['rsl_ss_max_year2']))?></td>
                                            </tr>

                                            <tr>
                                                <td><?=$year - 1?> YEE</td>
                                                <td align="center"><?=check_zero(number_format($price['rsl_ss1_year1']))?></td>
                                                <td align="center"><?=check_zero(number_format($price['rsl_ss2_year1']))?></td>
                                                <td align="center"><?=check_zero(number_format($price['rsl_ss3_year1']))?></td>
                                                <td align="center"><?=check_zero(number_format($price['rsl_ss4_year1']))?></td>
                                                <td align="center"><?=check_zero(number_format($price['rsl_ss5_year1']))?></td>
                                                <td align="center"><?=check_zero(number_format($price['rsl_ss6_year1']))?></td>
                                                <td align="center"><?=check_zero(number_format($price['rsl_ss7_year1']))?></td>
                                                <td align="center"><?=check_zero(number_format($price['rsl_ss8_year1']))?></td>
                                                <td align="center"><?=check_zero(number_format($price['rsl_ss9_year1']))?></td>
                                                <td align="center"><?=check_zero(number_format($price['rsl_ss10_year1']))?></td>
                                                <td align="center"><?=check_zero(number_format($price['rsl_ss11_year1']))?></td>
                                                <td align="center"><?=check_zero(number_format($price['rsl_ss12_year1']))?></td>
                                                <td align="center"><?=check_zero(number_format($price['rsl_ss_avg_year1']))?></td>
                                                <td align="center"><?=check_zero(number_format($price['rsl_ss_min_year1']))?></td>
                                                <td align="center"><?=check_zero(number_format($price['rsl_ss_max_year1']))?></td>
                                            </tr>

                                            <tr>
                                                <td><?=$year?> Budget</td>
                                                <td align="center"><?=check_zero(number_format($price['rsl_ss1']))?></td>
                                                <td align="center"><?=check_zero(number_format($price['rsl_ss2']))?></td>
                                                <td align="center"><?=check_zero(number_format($price['rsl_ss3']))?></td>
                                                <td align="center"><?=check_zero(number_format($price['rsl_ss4']))?></td>
                                                <td align="center"><?=check_zero(number_format($price['rsl_ss5']))?></td>
                                                <td align="center"><?=check_zero(number_format($price['rsl_ss6']))?></td>
                                                <td align="center"><?=check_zero(number_format($price['rsl_ss7']))?></td>
                                                <td align="center"><?=check_zero(number_format($price['rsl_ss8']))?></td>
                                                <td align="center"><?=check_zero(number_format($price['rsl_ss9']))?></td>
                                                <td align="center"><?=check_zero(number_format($price['rsl_ss10']))?></td>
                                                <td align="center"><?=check_zero(number_format($price['rsl_ss11']))?></td>
                                                <td align="center"><?=check_zero(number_format($price['rsl_ss12']))?></td>
                                                <td align="center"><?=check_zero(number_format($price['rsl_ss_avg']))?></td>
                                                <td align="center"><?=check_zero(number_format($price['rsl_ss_min']))?></td>
                                                <td align="center"><?=check_zero(number_format($price['rsl_ss_max']))?></td>
                                            </tr>

                                            <!-- RSL ORC BIGTIME -->
                                            <tr>
                                                <td rowspan="3" class="rowspan-center" style="vertical-align : middle;text-align:center;">ORC - BIGTIME</td>
                                                <td><?=$year - 2?> Actual</td>
                                                <td align="center"><?=check_zero(number_format($price['rsl_bt1_year2']))?></td>
                                                <td align="center"><?=check_zero(number_format($price['rsl_bt2_year2']))?></td>
                                                <td align="center"><?=check_zero(number_format($price['rsl_bt3_year2']))?></td>
                                                <td align="center"><?=check_zero(number_format($price['rsl_bt4_year2']))?></td>
                                                <td align="center"><?=check_zero(number_format($price['rsl_bt5_year2']))?></td>
                                                <td align="center"><?=check_zero(number_format($price['rsl_bt6_year2']))?></td>
                                                <td align="center"><?=check_zero(number_format($price['rsl_bt7_year2']))?></td>
                                                <td align="center"><?=check_zero(number_format($price['rsl_bt8_year2']))?></td>
                                                <td align="center"><?=check_zero(number_format($price['rsl_bt9_year2']))?></td>
                                                <td align="center"><?=check_zero(number_format($price['rsl_bt10_year2']))?></td>
                                                <td align="center"><?=check_zero(number_format($price['rsl_bt11_year2']))?></td>
                                                <td align="center"><?=check_zero(number_format($price['rsl_bt12_year2']))?></td>
                                                <td align="center"><?=check_zero(number_format($price['rsl_bt_avg_year2']))?></td>
                                                <td align="center"><?=check_zero(number_format($price['rsl_bt_min_year2']))?></td>
                                                <td align="center"><?=check_zero(number_format($price['rsl_bt_max_year2']))?></td>
                                            </tr>

                                            <tr>
                                                <td><?=$year - 1?> YEE</td>
                                                <td align="center"><?=check_zero(number_format($price['rsl_bt1_year1']))?></td>
                                                <td align="center"><?=check_zero(number_format($price['rsl_bt2_year1']))?></td>
                                                <td align="center"><?=check_zero(number_format($price['rsl_bt3_year1']))?></td>
                                                <td align="center"><?=check_zero(number_format($price['rsl_bt4_year1']))?></td>
                                                <td align="center"><?=check_zero(number_format($price['rsl_bt5_year1']))?></td>
                                                <td align="center"><?=check_zero(number_format($price['rsl_bt6_year1']))?></td>
                                                <td align="center"><?=check_zero(number_format($price['rsl_bt7_year1']))?></td>
                                                <td align="center"><?=check_zero(number_format($price['rsl_bt8_year1']))?></td>
                                                <td align="center"><?=check_zero(number_format($price['rsl_bt9_year1']))?></td>
                                                <td align="center"><?=check_zero(number_format($price['rsl_bt10_year1']))?></td>
                                                <td align="center"><?=check_zero(number_format($price['rsl_bt11_year1']))?></td>
                                                <td align="center"><?=check_zero(number_format($price['rsl_bt12_year1']))?></td>
                                                <td align="center"><?=check_zero(number_format($price['rsl_bt_avg_year1']))?></td>
                                                <td align="center"><?=check_zero(number_format($price['rsl_bt_min_year1']))?></td>
                                                <td align="center"><?=check_zero(number_format($price['rsl_bt_max_year1']))?></td>
                                            </tr>

                                            <tr>
                                                <td><?=$year?> Budget</td>
                                                <td align="center"><?=check_zero(number_format($price['rsl_bt1']))?></td>
                                                <td align="center"><?=check_zero(number_format($price['rsl_bt2']))?></td>
                                                <td align="center"><?=check_zero(number_format($price['rsl_bt3']))?></td>
                                                <td align="center"><?=check_zero(number_format($price['rsl_bt4']))?></td>
                                                <td align="center"><?=check_zero(number_format($price['rsl_bt5']))?></td>
                                                <td align="center"><?=check_zero(number_format($price['rsl_bt6']))?></td>
                                                <td align="center"><?=check_zero(number_format($price['rsl_bt7']))?></td>
                                                <td align="center"><?=check_zero(number_format($price['rsl_bt8']))?></td>
                                                <td align="center"><?=check_zero(number_format($price['rsl_bt9']))?></td>
                                                <td align="center"><?=check_zero(number_format($price['rsl_bt10']))?></td>
                                                <td align="center"><?=check_zero(number_format($price['rsl_bt11']))?></td>
                                                <td align="center"><?=check_zero(number_format($price['rsl_bt12']))?></td>
                                                <td align="center"><?=check_zero(number_format($price['rsl_bt_avg']))?></td>
                                                <td align="center"><?=check_zero(number_format($price['rsl_bt_min']))?></td>
                                                <td align="center"><?=check_zero(number_format($price['rsl_bt_max']))?></td>
                                            </tr>

                                            <!-- RSL ORC HALF -->
                                            <tr>
                                                <td rowspan="3" class="rowspan-center" style="vertical-align : middle;text-align:center;">ORC - HALF</td>
                                                <td><?=$year - 2?> Actual</td>
                                                <td align="center"><?=check_zero(number_format($price['rsl_half1_year2']))?></td>
                                                <td align="center"><?=check_zero(number_format($price['rsl_half2_year2']))?></td>
                                                <td align="center"><?=check_zero(number_format($price['rsl_half3_year2']))?></td>
                                                <td align="center"><?=check_zero(number_format($price['rsl_half4_year2']))?></td>
                                                <td align="center"><?=check_zero(number_format($price['rsl_half5_year2']))?></td>
                                                <td align="center"><?=check_zero(number_format($price['rsl_half6_year2']))?></td>
                                                <td align="center"><?=check_zero(number_format($price['rsl_half7_year2']))?></td>
                                                <td align="center"><?=check_zero(number_format($price['rsl_half8_year2']))?></td>
                                                <td align="center"><?=check_zero(number_format($price['rsl_half9_year2']))?></td>
                                                <td align="center"><?=check_zero(number_format($price['rsl_half10_year2']))?></td>
                                                <td align="center"><?=check_zero(number_format($price['rsl_half11_year2']))?></td>
                                                <td align="center"><?=check_zero(number_format($price['rsl_half12_year2']))?></td>
                                                <td align="center"><?=check_zero(number_format($price['rsl_half_avg_year2']))?></td>
                                                <td align="center"><?=check_zero(number_format($price['rsl_half_min_year2']))?></td>
                                                <td align="center"><?=check_zero(number_format($price['rsl_half_max_year2']))?></td>
                                            </tr>

                                            <tr>
                                                <td><?=$year - 1?> YEE</td>
                                                <td align="center"><?=check_zero(number_format($price['rsl_half1_year1']))?></td>
                                                <td align="center"><?=check_zero(number_format($price['rsl_half2_year1']))?></td>
                                                <td align="center"><?=check_zero(number_format($price['rsl_half3_year1']))?></td>
                                                <td align="center"><?=check_zero(number_format($price['rsl_half4_year1']))?></td>
                                                <td align="center"><?=check_zero(number_format($price['rsl_half5_year1']))?></td>
                                                <td align="center"><?=check_zero(number_format($price['rsl_half6_year1']))?></td>
                                                <td align="center"><?=check_zero(number_format($price['rsl_half7_year1']))?></td>
                                                <td align="center"><?=check_zero(number_format($price['rsl_half8_year1']))?></td>
                                                <td align="center"><?=check_zero(number_format($price['rsl_half9_year1']))?></td>
                                                <td align="center"><?=check_zero(number_format($price['rsl_half10_year1']))?></td>
                                                <td align="center"><?=check_zero(number_format($price['rsl_half11_year1']))?></td>
                                                <td align="center"><?=check_zero(number_format($price['rsl_half12_year1']))?></td>
                                                <td align="center"><?=check_zero(number_format($price['rsl_half_avg_year1']))?></td>
                                                <td align="center"><?=check_zero(number_format($price['rsl_half_min_year1']))?></td>
                                                <td align="center"><?=check_zero(number_format($price['rsl_half_max_year1']))?></td>
                                            </tr>

                                            <tr>
                                                <td><?=$year?> Budget</td>
                                                <td align="center"><?=check_zero(number_format($price['rsl_half1']))?></td>
                                                <td align="center"><?=check_zero(number_format($price['rsl_half2']))?></td>
                                                <td align="center"><?=check_zero(number_format($price['rsl_half3']))?></td>
                                                <td align="center"><?=check_zero(number_format($price['rsl_half4']))?></td>
                                                <td align="center"><?=check_zero(number_format($price['rsl_half5']))?></td>
                                                <td align="center"><?=check_zero(number_format($price['rsl_half6']))?></td>
                                                <td align="center"><?=check_zero(number_format($price['rsl_half7']))?></td>
                                                <td align="center"><?=check_zero(number_format($price['rsl_half8']))?></td>
                                                <td align="center"><?=check_zero(number_format($price['rsl_half9']))?></td>
                                                <td align="center"><?=check_zero(number_format($price['rsl_half10']))?></td>
                                                <td align="center"><?=check_zero(number_format($price['rsl_half11']))?></td>
                                                <td align="center"><?=check_zero(number_format($price['rsl_half12']))?></td>
                                                <td align="center"><?=check_zero(number_format($price['rsl_half_avg']))?></td>
                                                <td align="center"><?=check_zero(number_format($price['rsl_half_min']))?></td>
                                                <td align="center"><?=check_zero(number_format($price['rsl_half_max']))?></td>
                                            </tr>

                                            <!-- RSL LIEMPO -->
                                            <tr>
                                                <td rowspan="3" class="rowspan-center" style="vertical-align : middle;text-align:center;">LIEMPO</td>
                                                <td><?=$year - 2?> Actual</td>
                                                <td align="center"><?=check_zero(number_format($price['rsl_liempo_prev_year2_jan']))?></td>
                                                <td align="center"><?=check_zero(number_format($price['rsl_liempo_prev_year2_feb']))?></td>
                                                <td align="center"><?=check_zero(number_format($price['rsl_liempo_prev_year2_mar']))?></td>
                                                <td align="center"><?=check_zero(number_format($price['rsl_liempo_prev_year2_apr']))?></td>
                                                <td align="center"><?=check_zero(number_format($price['rsl_liempo_prev_year2_may']))?></td>
                                                <td align="center"><?=check_zero(number_format($price['rsl_liempo_prev_year2_jun']))?></td>
                                                <td align="center"><?=check_zero(number_format($price['rsl_liempo_prev_year2_jul']))?></td>
                                                <td align="center"><?=check_zero(number_format($price['rsl_liempo_prev_year2_aug']))?></td>
                                                <td align="center"><?=check_zero(number_format($price['rsl_liempo_prev_year2_sep']))?></td>
                                                <td align="center"><?=check_zero(number_format($price['rsl_liempo_prev_year2_oct']))?></td>
                                                <td align="center"><?=check_zero(number_format($price['rsl_liempo_prev_year2_nov']))?></td>
                                                <td align="center"><?=check_zero(number_format($price['rsl_liempo_prev_year2_dec']))?></td>
                                                <td align="center"><?=check_zero(number_format($price['rsl_liempo_prev_year2_avg']))?></td>
                                                <td align="center"><?=check_zero(number_format($price['rsl_liempo_prev_year2_min']))?></td>
                                                <td align="center"><?=check_zero(number_format($price['rsl_liempo_prev_year2_max']))?></td>
                                            </tr>

                                            <tr>
                                                <td><?=$year - 1?> YEE</td>
                                                <td align="center"><?=check_zero(number_format($price['rsl_liempo_prev_year1_jan']))?></td>
                                                <td align="center"><?=check_zero(number_format($price['rsl_liempo_prev_year1_feb']))?></td>
                                                <td align="center"><?=check_zero(number_format($price['rsl_liempo_prev_year1_mar']))?></td>
                                                <td align="center"><?=check_zero(number_format($price['rsl_liempo_prev_year1_apr']))?></td>
                                                <td align="center"><?=check_zero(number_format($price['rsl_liempo_prev_year1_may']))?></td>
                                                <td align="center"><?=check_zero(number_format($price['rsl_liempo_prev_year1_jun']))?></td>
                                                <td align="center"><?=check_zero(number_format($price['rsl_liempo_prev_year1_jul']))?></td>
                                                <td align="center"><?=check_zero(number_format($price['rsl_liempo_prev_year1_aug']))?></td>
                                                <td align="center"><?=check_zero(number_format($price['rsl_liempo_prev_year1_sep']))?></td>
                                                <td align="center"><?=check_zero(number_format($price['rsl_liempo_prev_year1_oct']))?></td>
                                                <td align="center"><?=check_zero(number_format($price['rsl_liempo_prev_year1_nov']))?></td>
                                                <td align="center"><?=check_zero(number_format($price['rsl_liempo_prev_year1_dec']))?></td>
                                                <td align="center"><?=check_zero(number_format($price['rsl_liempo_prev_year1_avg']))?></td>
                                                <td align="center"><?=check_zero(number_format($price['rsl_liempo_prev_year1_min']))?></td>
                                                <td align="center"><?=check_zero(number_format($price['rsl_liempo_prev_year1_max']))?></td>
                                            </tr>

                                            <tr>
                                                <td><?=$year?> Budget</td>
                                                <td align="center"><?=check_zero(number_format($price['rsl_liempo1']))?></td>
                                                <td align="center"><?=check_zero(number_format($price['rsl_liempo2']))?></td>
                                                <td align="center"><?=check_zero(number_format($price['rsl_liempo3']))?></td>
                                                <td align="center"><?=check_zero(number_format($price['rsl_liempo4']))?></td>
                                                <td align="center"><?=check_zero(number_format($price['rsl_liempo5']))?></td>
                                                <td align="center"><?=check_zero(number_format($price['rsl_liempo6']))?></td>
                                                <td align="center"><?=check_zero(number_format($price['rsl_liempo7']))?></td>
                                                <td align="center"><?=check_zero(number_format($price['rsl_liempo8']))?></td>
                                                <td align="center"><?=check_zero(number_format($price['rsl_liempo9']))?></td>
                                                <td align="center"><?=check_zero(number_format($price['rsl_liempo10']))?></td>
                                                <td align="center"><?=check_zero(number_format($price['rsl_liempo11']))?></td>
                                                <td align="center"><?=check_zero(number_format($price['rsl_liempo12']))?></td>
                                                <td align="center"><?=check_zero(number_format($price['rsl_liempo_avg']))?></td>
                                                <td align="center"><?=check_zero(number_format($price['rsl_liempo_min']))?></td>
                                                <td align="center"><?=check_zero(number_format($price['rsl_liempo_max']))?></td>
                                            </tr>

                                            <!-- RSL DRESSED CHICKEN -->
                                            <tr>
                                                <td rowspan="3" class="rowspan-center" style="vertical-align : middle;text-align:center;">DRESSED</td>
                                                <td><?=$year - 2?> Actual</td>
                                                <td align="center"><?=check_zero(number_format($price['rsl_dressed_prev_year2_jan']))?></td>
                                                <td align="center"><?=check_zero(number_format($price['rsl_dressed_prev_year2_feb']))?></td>
                                                <td align="center"><?=check_zero(number_format($price['rsl_dressed_prev_year2_mar']))?></td>
                                                <td align="center"><?=check_zero(number_format($price['rsl_dressed_prev_year2_apr']))?></td>
                                                <td align="center"><?=check_zero(number_format($price['rsl_dressed_prev_year2_may']))?></td>
                                                <td align="center"><?=check_zero(number_format($price['rsl_dressed_prev_year2_jun']))?></td>
                                                <td align="center"><?=check_zero(number_format($price['rsl_dressed_prev_year2_jul']))?></td>
                                                <td align="center"><?=check_zero(number_format($price['rsl_dressed_prev_year2_aug']))?></td>
                                                <td align="center"><?=check_zero(number_format($price['rsl_dressed_prev_year2_sep']))?></td>
                                                <td align="center"><?=check_zero(number_format($price['rsl_dressed_prev_year2_oct']))?></td>
                                                <td align="center"><?=check_zero(number_format($price['rsl_dressed_prev_year2_nov']))?></td>
                                                <td align="center"><?=check_zero(number_format($price['rsl_dressed_prev_year2_dec']))?></td>
                                                <td align="center"><?=check_zero(number_format($price['rsl_dressed_prev_year2_avg']))?></td>
                                                <td align="center"><?=check_zero(number_format($price['rsl_dressed_prev_year2_min']))?></td>
                                                <td align="center"><?=check_zero(number_format($price['rsl_dressed_prev_year2_max']))?></td>
                                            </tr>

                                            <tr>
                                                <td><?=$year - 1?> YEE</td>
                                                <td align="center"><?=check_zero(number_format($price['rsl_dressed_prev_year1_jan']))?></td>
                                                <td align="center"><?=check_zero(number_format($price['rsl_dressed_prev_year1_feb']))?></td>
                                                <td align="center"><?=check_zero(number_format($price['rsl_dressed_prev_year1_mar']))?></td>
                                                <td align="center"><?=check_zero(number_format($price['rsl_dressed_prev_year1_apr']))?></td>
                                                <td align="center"><?=check_zero(number_format($price['rsl_dressed_prev_year1_may']))?></td>
                                                <td align="center"><?=check_zero(number_format($price['rsl_dressed_prev_year1_jun']))?></td>
                                                <td align="center"><?=check_zero(number_format($price['rsl_dressed_prev_year1_jul']))?></td>
                                                <td align="center"><?=check_zero(number_format($price['rsl_dressed_prev_year1_aug']))?></td>
                                                <td align="center"><?=check_zero(number_format($price['rsl_dressed_prev_year1_sep']))?></td>
                                                <td align="center"><?=check_zero(number_format($price['rsl_dressed_prev_year1_oct']))?></td>
                                                <td align="center"><?=check_zero(number_format($price['rsl_dressed_prev_year1_nov']))?></td>
                                                <td align="center"><?=check_zero(number_format($price['rsl_dressed_prev_year1_dec']))?></td>
                                                <td align="center"><?=check_zero(number_format($price['rsl_dressed_prev_year1_avg']))?></td>
                                                <td align="center"><?=check_zero(number_format($price['rsl_dressed_prev_year1_min']))?></td>
                                                <td align="center"><?=check_zero(number_format($price['rsl_dressed_prev_year1_max']))?></td>
                                            </tr>

                                            <tr>
                                                <td><?=$year?> Budget</td>
                                                <td align="center"><?=check_zero(number_format($price['rsl_dressed1']))?></td>
                                                <td align="center"><?=check_zero(number_format($price['rsl_dressed2']))?></td>
                                                <td align="center"><?=check_zero(number_format($price['rsl_dressed3']))?></td>
                                                <td align="center"><?=check_zero(number_format($price['rsl_dressed4']))?></td>
                                                <td align="center"><?=check_zero(number_format($price['rsl_dressed5']))?></td>
                                                <td align="center"><?=check_zero(number_format($price['rsl_dressed6']))?></td>
                                                <td align="center"><?=check_zero(number_format($price['rsl_dressed7']))?></td>
                                                <td align="center"><?=check_zero(number_format($price['rsl_dressed8']))?></td>
                                                <td align="center"><?=check_zero(number_format($price['rsl_dressed9']))?></td>
                                                <td align="center"><?=check_zero(number_format($price['rsl_dressed10']))?></td>
                                                <td align="center"><?=check_zero(number_format($price['rsl_dressed11']))?></td>
                                                <td align="center"><?=check_zero(number_format($price['rsl_dressed12']))?></td>
                                                <td align="center"><?=check_zero(number_format($price['rsl_dressed_avg']))?></td>
                                                <td align="center"><?=check_zero(number_format($price['rsl_dressed_min']))?></td>
                                                <td align="center"><?=check_zero(number_format($price['rsl_dressed_max']))?></td>
                                            </tr>

                                            <!-- RSL CHOOKSIES MARINADO -->
                                            <tr>
                                                <td rowspan="3" class="rowspan-center" style="vertical-align : middle;text-align:center;">CHOOKSIES MARINADO</td>
                                                <td><?=$year - 2?> Actual</td>
                                                <td align="center"><?=check_zero(number_format($price['rsl_chooksies_prev_year2_jan']))?></td>
                                                <td align="center"><?=check_zero(number_format($price['rsl_chooksies_prev_year2_feb']))?></td>
                                                <td align="center"><?=check_zero(number_format($price['rsl_chooksies_prev_year2_mar']))?></td>
                                                <td align="center"><?=check_zero(number_format($price['rsl_chooksies_prev_year2_apr']))?></td>
                                                <td align="center"><?=check_zero(number_format($price['rsl_chooksies_prev_year2_may']))?></td>
                                                <td align="center"><?=check_zero(number_format($price['rsl_chooksies_prev_year2_jun']))?></td>
                                                <td align="center"><?=check_zero(number_format($price['rsl_chooksies_prev_year2_jul']))?></td>
                                                <td align="center"><?=check_zero(number_format($price['rsl_chooksies_prev_year2_aug']))?></td>
                                                <td align="center"><?=check_zero(number_format($price['rsl_chooksies_prev_year2_sep']))?></td>
                                                <td align="center"><?=check_zero(number_format($price['rsl_chooksies_prev_year2_oct']))?></td>
                                                <td align="center"><?=check_zero(number_format($price['rsl_chooksies_prev_year2_nov']))?></td>
                                                <td align="center"><?=check_zero(number_format($price['rsl_chooksies_prev_year2_dec']))?></td>
                                                <td align="center"><?=check_zero(number_format($price['rsl_chooksies_prev_year2_avg']))?></td>
                                                <td align="center"><?=check_zero(number_format($price['rsl_chooksies_prev_year2_min']))?></td>
                                                <td align="center"><?=check_zero(number_format($price['rsl_chooksies_prev_year2_max']))?></td>
                                            </tr>

                                            <tr>
                                                <td><?=$year - 1?> YEE</td>
                                                <td align="center"><?=check_zero(number_format($price['rsl_chooksies_prev_year1_jan']))?></td>
                                                <td align="center"><?=check_zero(number_format($price['rsl_chooksies_prev_year1_feb']))?></td>
                                                <td align="center"><?=check_zero(number_format($price['rsl_chooksies_prev_year1_mar']))?></td>
                                                <td align="center"><?=check_zero(number_format($price['rsl_chooksies_prev_year1_apr']))?></td>
                                                <td align="center"><?=check_zero(number_format($price['rsl_chooksies_prev_year1_may']))?></td>
                                                <td align="center"><?=check_zero(number_format($price['rsl_chooksies_prev_year1_jun']))?></td>
                                                <td align="center"><?=check_zero(number_format($price['rsl_chooksies_prev_year1_jul']))?></td>
                                                <td align="center"><?=check_zero(number_format($price['rsl_chooksies_prev_year1_aug']))?></td>
                                                <td align="center"><?=check_zero(number_format($price['rsl_chooksies_prev_year1_sep']))?></td>
                                                <td align="center"><?=check_zero(number_format($price['rsl_chooksies_prev_year1_oct']))?></td>
                                                <td align="center"><?=check_zero(number_format($price['rsl_chooksies_prev_year1_nov']))?></td>
                                                <td align="center"><?=check_zero(number_format($price['rsl_chooksies_prev_year1_dec']))?></td>
                                                <td align="center"><?=check_zero(number_format($price['rsl_chooksies_prev_year1_avg']))?></td>
                                                <td align="center"><?=check_zero(number_format($price['rsl_chooksies_prev_year1_min']))?></td>
                                                <td align="center"><?=check_zero(number_format($price['rsl_chooksies_prev_year1_max']))?></td>
                                            </tr>

                                            <tr>
                                                <td><?=$year?> Budget</td>
                                                <td align="center"><?=check_zero(number_format($price['rsl_chooksies1']))?></td>
                                                <td align="center"><?=check_zero(number_format($price['rsl_chooksies2']))?></td>
                                                <td align="center"><?=check_zero(number_format($price['rsl_chooksies3']))?></td>
                                                <td align="center"><?=check_zero(number_format($price['rsl_chooksies4']))?></td>
                                                <td align="center"><?=check_zero(number_format($price['rsl_chooksies5']))?></td>
                                                <td align="center"><?=check_zero(number_format($price['rsl_chooksies6']))?></td>
                                                <td align="center"><?=check_zero(number_format($price['rsl_chooksies7']))?></td>
                                                <td align="center"><?=check_zero(number_format($price['rsl_chooksies8']))?></td>
                                                <td align="center"><?=check_zero(number_format($price['rsl_chooksies9']))?></td>
                                                <td align="center"><?=check_zero(number_format($price['rsl_chooksies10']))?></td>
                                                <td align="center"><?=check_zero(number_format($price['rsl_chooksies11']))?></td>
                                                <td align="center"><?=check_zero(number_format($price['rsl_chooksies12']))?></td>
                                                <td align="center"><?=check_zero(number_format($price['rsl_chooksies_avg']))?></td>
                                                <td align="center"><?=check_zero(number_format($price['rsl_chooksies_min']))?></td>
                                                <td align="center"><?=check_zero(number_format($price['rsl_chooksies_max']))?></td>
                                            </tr>

                                            <!-- RSL MARINADO FRIED -->
                                            <tr>
                                                <td rowspan="3" class="rowspan-center" style="vertical-align : middle;text-align:center;">MARINADO FRIED</td>
                                                <td><?=$year - 2?> Actual</td>
                                                <td align="center"><?=check_zero(number_format($price['rsl_marinado_prev_year2_jan']))?></td>
                                                <td align="center"><?=check_zero(number_format($price['rsl_marinado_prev_year2_feb']))?></td>
                                                <td align="center"><?=check_zero(number_format($price['rsl_marinado_prev_year2_mar']))?></td>
                                                <td align="center"><?=check_zero(number_format($price['rsl_marinado_prev_year2_apr']))?></td>
                                                <td align="center"><?=check_zero(number_format($price['rsl_marinado_prev_year2_may']))?></td>
                                                <td align="center"><?=check_zero(number_format($price['rsl_marinado_prev_year2_jun']))?></td>
                                                <td align="center"><?=check_zero(number_format($price['rsl_marinado_prev_year2_jul']))?></td>
                                                <td align="center"><?=check_zero(number_format($price['rsl_marinado_prev_year2_aug']))?></td>
                                                <td align="center"><?=check_zero(number_format($price['rsl_marinado_prev_year2_sep']))?></td>
                                                <td align="center"><?=check_zero(number_format($price['rsl_marinado_prev_year2_oct']))?></td>
                                                <td align="center"><?=check_zero(number_format($price['rsl_marinado_prev_year2_nov']))?></td>
                                                <td align="center"><?=check_zero(number_format($price['rsl_marinado_prev_year2_dec']))?></td>
                                                <td align="center"><?=check_zero(number_format($price['rsl_marinado_prev_year2_avg']))?></td>
                                                <td align="center"><?=check_zero(number_format($price['rsl_marinado_prev_year2_min']))?></td>
                                                <td align="center"><?=check_zero(number_format($price['rsl_marinado_prev_year2_max']))?></td>
                                            </tr>

                                            <tr>
                                                <td><?=$year - 1?> YEE</td>
                                                <td align="center"><?=check_zero(number_format($price['rsl_marinado_prev_year1_jan']))?></td>
                                                <td align="center"><?=check_zero(number_format($price['rsl_marinado_prev_year1_feb']))?></td>
                                                <td align="center"><?=check_zero(number_format($price['rsl_marinado_prev_year1_mar']))?></td>
                                                <td align="center"><?=check_zero(number_format($price['rsl_marinado_prev_year1_apr']))?></td>
                                                <td align="center"><?=check_zero(number_format($price['rsl_marinado_prev_year1_may']))?></td>
                                                <td align="center"><?=check_zero(number_format($price['rsl_marinado_prev_year1_jun']))?></td>
                                                <td align="center"><?=check_zero(number_format($price['rsl_marinado_prev_year1_jul']))?></td>
                                                <td align="center"><?=check_zero(number_format($price['rsl_marinado_prev_year1_aug']))?></td>
                                                <td align="center"><?=check_zero(number_format($price['rsl_marinado_prev_year1_sep']))?></td>
                                                <td align="center"><?=check_zero(number_format($price['rsl_marinado_prev_year1_oct']))?></td>
                                                <td align="center"><?=check_zero(number_format($price['rsl_marinado_prev_year1_nov']))?></td>
                                                <td align="center"><?=check_zero(number_format($price['rsl_marinado_prev_year1_dec']))?></td>
                                                <td align="center"><?=check_zero(number_format($price['rsl_marinado_prev_year1_avg']))?></td>
                                                <td align="center"><?=check_zero(number_format($price['rsl_marinado_prev_year1_min']))?></td>
                                                <td align="center"><?=check_zero(number_format($price['rsl_marinado_prev_year1_max']))?></td>
                                            </tr>

                                            <tr>
                                                <td><?=$year?> Budget</td>
                                                <td align="center"><?=check_zero(number_format($price['rsl_marinado1']))?></td>
                                                <td align="center"><?=check_zero(number_format($price['rsl_marinado2']))?></td>
                                                <td align="center"><?=check_zero(number_format($price['rsl_marinado3']))?></td>
                                                <td align="center"><?=check_zero(number_format($price['rsl_marinado4']))?></td>
                                                <td align="center"><?=check_zero(number_format($price['rsl_marinado5']))?></td>
                                                <td align="center"><?=check_zero(number_format($price['rsl_marinado6']))?></td>
                                                <td align="center"><?=check_zero(number_format($price['rsl_marinado7']))?></td>
                                                <td align="center"><?=check_zero(number_format($price['rsl_marinado8']))?></td>
                                                <td align="center"><?=check_zero(number_format($price['rsl_marinado9']))?></td>
                                                <td align="center"><?=check_zero(number_format($price['rsl_marinado10']))?></td>
                                                <td align="center"><?=check_zero(number_format($price['rsl_marinado11']))?></td>
                                                <td align="center"><?=check_zero(number_format($price['rsl_marinado12']))?></td>
                                                <td align="center"><?=check_zero(number_format($price['rsl_marinado_avg']))?></td>
                                                <td align="center"><?=check_zero(number_format($price['rsl_marinado_min']))?></td>
                                                <td align="center"><?=check_zero(number_format($price['rsl_marinado_max']))?></td>
                                            </tr>

                                            <!-- RSL SPICY NECK -->
                                            <tr>
                                                <td rowspan="3" class="rowspan-center" style="vertical-align : middle;text-align:center;">SPICY NECK</td>
                                                <td><?=$year - 2?> Actual</td>
                                                <td align="center"><?=check_zero(number_format($price['rsl_spicy_prev_year2_jan']))?></td>
                                                <td align="center"><?=check_zero(number_format($price['rsl_spicy_prev_year2_feb']))?></td>
                                                <td align="center"><?=check_zero(number_format($price['rsl_spicy_prev_year2_mar']))?></td>
                                                <td align="center"><?=check_zero(number_format($price['rsl_spicy_prev_year2_apr']))?></td>
                                                <td align="center"><?=check_zero(number_format($price['rsl_spicy_prev_year2_may']))?></td>
                                                <td align="center"><?=check_zero(number_format($price['rsl_spicy_prev_year2_jun']))?></td>
                                                <td align="center"><?=check_zero(number_format($price['rsl_spicy_prev_year2_jul']))?></td>
                                                <td align="center"><?=check_zero(number_format($price['rsl_spicy_prev_year2_aug']))?></td>
                                                <td align="center"><?=check_zero(number_format($price['rsl_spicy_prev_year2_sep']))?></td>
                                                <td align="center"><?=check_zero(number_format($price['rsl_spicy_prev_year2_oct']))?></td>
                                                <td align="center"><?=check_zero(number_format($price['rsl_spicy_prev_year2_nov']))?></td>
                                                <td align="center"><?=check_zero(number_format($price['rsl_spicy_prev_year2_dec']))?></td>
                                                <td align="center"><?=check_zero(number_format($price['rsl_spicy_prev_year2_avg']))?></td>
                                                <td align="center"><?=check_zero(number_format($price['rsl_spicy_prev_year2_min']))?></td>
                                                <td align="center"><?=check_zero(number_format($price['rsl_spicy_prev_year2_max']))?></td>
                                            </tr>
                                            
                                            <tr>
                                                <td><?=$year - 1?> YEE</td>
                                                <td align="center"><?=check_zero(number_format($price['rsl_spicy_prev_year1_jan']))?></td>
                                                <td align="center"><?=check_zero(number_format($price['rsl_spicy_prev_year1_feb']))?></td>
                                                <td align="center"><?=check_zero(number_format($price['rsl_spicy_prev_year1_mar']))?></td>
                                                <td align="center"><?=check_zero(number_format($price['rsl_spicy_prev_year1_apr']))?></td>
                                                <td align="center"><?=check_zero(number_format($price['rsl_spicy_prev_year1_may']))?></td>
                                                <td align="center"><?=check_zero(number_format($price['rsl_spicy_prev_year1_jun']))?></td>
                                                <td align="center"><?=check_zero(number_format($price['rsl_spicy_prev_year1_jul']))?></td>
                                                <td align="center"><?=check_zero(number_format($price['rsl_spicy_prev_year1_aug']))?></td>
                                                <td align="center"><?=check_zero(number_format($price['rsl_spicy_prev_year1_sep']))?></td>
                                                <td align="center"><?=check_zero(number_format($price['rsl_spicy_prev_year1_oct']))?></td>
                                                <td align="center"><?=check_zero(number_format($price['rsl_spicy_prev_year1_nov']))?></td>
                                                <td align="center"><?=check_zero(number_format($price['rsl_spicy_prev_year1_dec']))?></td>
                                                <td align="center"><?=check_zero(number_format($price['rsl_spicy_prev_year1_avg']))?></td>
                                                <td align="center"><?=check_zero(number_format($price['rsl_spicy_prev_year1_min']))?></td>
                                                <td align="center"><?=check_zero(number_format($price['rsl_spicy_prev_year1_max']))?></td>
                                            </tr>
                                            
                                            <tr>
                                                <td><?=$year?> Budget</td>
                                                <td align="center"><?=check_zero(number_format($price['rsl_spicy1']))?></td>
                                                <td align="center"><?=check_zero(number_format($price['rsl_spicy2']))?></td>
                                                <td align="center"><?=check_zero(number_format($price['rsl_spicy3']))?></td>
                                                <td align="center"><?=check_zero(number_format($price['rsl_spicy4']))?></td>
                                                <td align="center"><?=check_zero(number_format($price['rsl_spicy5']))?></td>
                                                <td align="center"><?=check_zero(number_format($price['rsl_spicy6']))?></td>
                                                <td align="center"><?=check_zero(number_format($price['rsl_spicy7']))?></td>
                                                <td align="center"><?=check_zero(number_format($price['rsl_spicy8']))?></td>
                                                <td align="center"><?=check_zero(number_format($price['rsl_spicy9']))?></td>
                                                <td align="center"><?=check_zero(number_format($price['rsl_spicy10']))?></td>
                                                <td align="center"><?=check_zero(number_format($price['rsl_spicy11']))?></td>
                                                <td align="center"><?=check_zero(number_format($price['rsl_spicy12']))?></td>
                                                <td align="center"><?=check_zero(number_format($price['rsl_spicy_avg']))?></td>
                                                <td align="center"><?=check_zero(number_format($price['rsl_spicy_min']))?></td>
                                                <td align="center"><?=check_zero(number_format($price['rsl_spicy_max']))?></td>
                                            </tr>
                                            
                                            <!-- RSL NUGGETS -->
                                            <tr>
                                                <td rowspan="3" class="rowspan-center" style="vertical-align : middle;text-align:center;">VAP-Nuggets</td>
                                                <td><?=$year - 2?> Actual</td>
                                                <td align="center"><?=check_zero(number_format($price['rsl_nuggets_prev_year2_jan']))?></td>
                                                <td align="center"><?=check_zero(number_format($price['rsl_nuggets_prev_year2_feb']))?></td>
                                                <td align="center"><?=check_zero(number_format($price['rsl_nuggets_prev_year2_mar']))?></td>
                                                <td align="center"><?=check_zero(number_format($price['rsl_nuggets_prev_year2_apr']))?></td>
                                                <td align="center"><?=check_zero(number_format($price['rsl_nuggets_prev_year2_may']))?></td>
                                                <td align="center"><?=check_zero(number_format($price['rsl_nuggets_prev_year2_jun']))?></td>
                                                <td align="center"><?=check_zero(number_format($price['rsl_nuggets_prev_year2_jul']))?></td>
                                                <td align="center"><?=check_zero(number_format($price['rsl_nuggets_prev_year2_aug']))?></td>
                                                <td align="center"><?=check_zero(number_format($price['rsl_nuggets_prev_year2_sep']))?></td>
                                                <td align="center"><?=check_zero(number_format($price['rsl_nuggets_prev_year2_oct']))?></td>
                                                <td align="center"><?=check_zero(number_format($price['rsl_nuggets_prev_year2_nov']))?></td>
                                                <td align="center"><?=check_zero(number_format($price['rsl_nuggets_prev_year2_dec']))?></td>
                                                <td align="center"><?=check_zero(number_format($price['rsl_nuggets_prev_year2_avg']))?></td>
                                                <td align="center"><?=check_zero(number_format($price['rsl_nuggets_prev_year2_min']))?></td>
                                                <td align="center"><?=check_zero(number_format($price['rsl_nuggets_prev_year2_max']))?></td>
                                            </tr>
                                            
                                            <tr>
                                                <td><?=$year - 1?> YEE</td>
                                                <td align="center"><?=check_zero(number_format($price['rsl_nuggets_prev_year1_jan']))?></td>
                                                <td align="center"><?=check_zero(number_format($price['rsl_nuggets_prev_year1_feb']))?></td>
                                                <td align="center"><?=check_zero(number_format($price['rsl_nuggets_prev_year1_mar']))?></td>
                                                <td align="center"><?=check_zero(number_format($price['rsl_nuggets_prev_year1_apr']))?></td>
                                                <td align="center"><?=check_zero(number_format($price['rsl_nuggets_prev_year1_may']))?></td>
                                                <td align="center"><?=check_zero(number_format($price['rsl_nuggets_prev_year1_jun']))?></td>
                                                <td align="center"><?=check_zero(number_format($price['rsl_nuggets_prev_year1_jul']))?></td>
                                                <td align="center"><?=check_zero(number_format($price['rsl_nuggets_prev_year1_aug']))?></td>
                                                <td align="center"><?=check_zero(number_format($price['rsl_nuggets_prev_year1_sep']))?></td>
                                                <td align="center"><?=check_zero(number_format($price['rsl_nuggets_prev_year1_oct']))?></td>
                                                <td align="center"><?=check_zero(number_format($price['rsl_nuggets_prev_year1_nov']))?></td>
                                                <td align="center"><?=check_zero(number_format($price['rsl_nuggets_prev_year1_dec']))?></td>
                                                <td align="center"><?=check_zero(number_format($price['rsl_nuggets_prev_year1_avg']))?></td>
                                                <td align="center"><?=check_zero(number_format($price['rsl_nuggets_prev_year1_min']))?></td>
                                                <td align="center"><?=check_zero(number_format($price['rsl_nuggets_prev_year1_max']))?></td>
                                            </tr>
                                            
                                            <tr>
                                                <td><?=$year?> Budget</td>
                                                <td align="center"><?=check_zero(number_format($price['rsl_nuggets1']))?></td>
                                                <td align="center"><?=check_zero(number_format($price['rsl_nuggets2']))?></td>
                                                <td align="center"><?=check_zero(number_format($price['rsl_nuggets3']))?></td>
                                                <td align="center"><?=check_zero(number_format($price['rsl_nuggets4']))?></td>
                                                <td align="center"><?=check_zero(number_format($price['rsl_nuggets5']))?></td>
                                                <td align="center"><?=check_zero(number_format($price['rsl_nuggets6']))?></td>
                                                <td align="center"><?=check_zero(number_format($price['rsl_nuggets7']))?></td>
                                                <td align="center"><?=check_zero(number_format($price['rsl_nuggets8']))?></td>
                                                <td align="center"><?=check_zero(number_format($price['rsl_nuggets9']))?></td>
                                                <td align="center"><?=check_zero(number_format($price['rsl_nuggets10']))?></td>
                                                <td align="center"><?=check_zero(number_format($price['rsl_nuggets11']))?></td>
                                                <td align="center"><?=check_zero(number_format($price['rsl_nuggets12']))?></td>
                                                <td align="center"><?=check_zero(number_format($price['rsl_nuggets_avg']))?></td>
                                                <td align="center"><?=check_zero(number_format($price['rsl_nuggets_min']))?></td>
                                                <td align="center"><?=check_zero(number_format($price['rsl_nuggets_max']))?></td>
                                            </tr>

                                            <!-- RSL 11 PC -->
                                            <tr>
                                                <td rowspan="3" class="rowspan-center" style="vertical-align : middle;text-align:center;">11 PC</td>
                                                <td><?=$year - 2?> Actual</td>
                                                <td class="text-right"><?=check_zero(number_format($price['rsl_pica_prev_year2_jan']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['rsl_pica_prev_year2_feb']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['rsl_pica_prev_year2_mar']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['rsl_pica_prev_year2_apr']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['rsl_pica_prev_year2_may']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['rsl_pica_prev_year2_jun']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['rsl_pica_prev_year2_jul']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['rsl_pica_prev_year2_aug']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['rsl_pica_prev_year2_sep']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['rsl_pica_prev_year2_oct']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['rsl_pica_prev_year2_nov']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['rsl_pica_prev_year2_dec']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['rsl_pica_prev_year2_avg']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['rsl_pica_prev_year2_min']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['rsl_pica_prev_year2_max']))?></td>
                                            </tr>

                                            <tr>
                                                <td><?=$year - 1?> YEE</td>
                                                <td class="text-right"><?=check_zero(number_format($price['rsl_pica_prev_year1_jan']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['rsl_pica_prev_year1_feb']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['rsl_pica_prev_year1_mar']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['rsl_pica_prev_year1_apr']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['rsl_pica_prev_year1_may']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['rsl_pica_prev_year1_jun']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['rsl_pica_prev_year1_jul']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['rsl_pica_prev_year1_aug']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['rsl_pica_prev_year1_sep']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['rsl_pica_prev_year1_oct']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['rsl_pica_prev_year1_nov']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['rsl_pica_prev_year1_dec']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['rsl_pica_prev_year1_avg']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['rsl_pica_prev_year1_min']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['rsl_pica_prev_year1_max']))?></td>
                                            </tr>

                                            <tr>
                                                <td><?=$year?> Budget</td>
                                                <td class="text-right"><?=check_zero(number_format($price['rsl_pica1']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['rsl_pica2']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['rsl_pica3']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['rsl_pica4']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['rsl_pica5']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['rsl_pica6']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['rsl_pica7']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['rsl_pica8']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['rsl_pica9']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['rsl_pica10']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['rsl_pica11']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['rsl_pica12']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['rsl_pica_avg']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['rsl_pica_min']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['rsl_pica_max']))?></td>
                                            </tr>

                                            <!-- RSL 5 PC -->
                                            <tr>
                                                <td rowspan="3" class="rowspan-center" style="vertical-align : middle;text-align:center;">5 PC</td>
                                                <td><?=$year - 2?> Actual</td>
                                                <td class="text-right"><?=check_zero(number_format($price['rsl_bossing_prev_year2_jan']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['rsl_bossing_prev_year2_feb']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['rsl_bossing_prev_year2_mar']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['rsl_bossing_prev_year2_apr']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['rsl_bossing_prev_year2_may']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['rsl_bossing_prev_year2_jun']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['rsl_bossing_prev_year2_jul']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['rsl_bossing_prev_year2_aug']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['rsl_bossing_prev_year2_sep']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['rsl_bossing_prev_year2_oct']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['rsl_bossing_prev_year2_nov']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['rsl_bossing_prev_year2_dec']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['rsl_bossing_prev_year2_avg']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['rsl_bossing_prev_year2_min']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['rsl_bossing_prev_year2_max']))?></td>
                                            </tr>

                                            <tr>
                                                <td><?=$year - 1?> YEE</td>
                                                <td class="text-right"><?=check_zero(number_format($price['rsl_bossing_prev_year1_jan']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['rsl_bossing_prev_year1_feb']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['rsl_bossing_prev_year1_mar']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['rsl_bossing_prev_year1_apr']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['rsl_bossing_prev_year1_may']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['rsl_bossing_prev_year1_jun']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['rsl_bossing_prev_year1_jul']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['rsl_bossing_prev_year1_aug']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['rsl_bossing_prev_year1_sep']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['rsl_bossing_prev_year1_oct']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['rsl_bossing_prev_year1_nov']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['rsl_bossing_prev_year1_dec']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['rsl_bossing_prev_year1_avg']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['rsl_bossing_prev_year1_min']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['rsl_bossing_prev_year1_max']))?></td>
                                            </tr>

                                            <tr>
                                                <td><?=$year?> Budget</td>
                                                <td class="text-right"><?=check_zero(number_format($price['rsl_bossing1']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['rsl_bossing2']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['rsl_bossing3']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['rsl_bossing4']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['rsl_bossing5']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['rsl_bossing6']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['rsl_bossing7']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['rsl_bossing8']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['rsl_bossing9']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['rsl_bossing10']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['rsl_bossing11']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['rsl_bossing12']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['rsl_bossing_avg']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['rsl_bossing_min']))?></td>
                                                <td class="text-right"><?=check_zero(number_format($price['rsl_bossing_max']))?></td>
                                            </tr>
                                            
                                            <!-- RSL MARINATED CHICKEN RAW -->
                                            <tr>
                                                <td rowspan="3" class="rowspan-center" style="vertical-align : middle;text-align:center;">MARINATED CHICKEN RAW</td>
                                                <td><?=$year - 2?> Actual</td>
                                                <td align="center"><?=check_zero(number_format($price['rsl_raw_prev_year2_jan']))?></td>
                                                <td align="center"><?=check_zero(number_format($price['rsl_raw_prev_year2_feb']))?></td>
                                                <td align="center"><?=check_zero(number_format($price['rsl_raw_prev_year2_mar']))?></td>
                                                <td align="center"><?=check_zero(number_format($price['rsl_raw_prev_year2_apr']))?></td>
                                                <td align="center"><?=check_zero(number_format($price['rsl_raw_prev_year2_may']))?></td>
                                                <td align="center"><?=check_zero(number_format($price['rsl_raw_prev_year2_jun']))?></td>
                                                <td align="center"><?=check_zero(number_format($price['rsl_raw_prev_year2_jul']))?></td>
                                                <td align="center"><?=check_zero(number_format($price['rsl_raw_prev_year2_aug']))?></td>
                                                <td align="center"><?=check_zero(number_format($price['rsl_raw_prev_year2_sep']))?></td>
                                                <td align="center"><?=check_zero(number_format($price['rsl_raw_prev_year2_oct']))?></td>
                                                <td align="center"><?=check_zero(number_format($price['rsl_raw_prev_year2_nov']))?></td>
                                                <td align="center"><?=check_zero(number_format($price['rsl_raw_prev_year2_dec']))?></td>
                                                <td align="center"><?=check_zero(number_format($price['rsl_raw_prev_year2_avg']))?></td>
                                                <td align="center"><?=check_zero(number_format($price['rsl_raw_prev_year2_min']))?></td>
                                                <td align="center"><?=check_zero(number_format($price['rsl_raw_prev_year2_max']))?></td>
                                            </tr>
                                            
                                            <tr>
                                                <td><?=$year - 1?> YEE</td>
                                                <td align="center"><?=check_zero(number_format($price['rsl_raw_prev_year1_jan']))?></td>
                                                <td align="center"><?=check_zero(number_format($price['rsl_raw_prev_year1_feb']))?></td>
                                                <td align="center"><?=check_zero(number_format($price['rsl_raw_prev_year1_mar']))?></td>
                                                <td align="center"><?=check_zero(number_format($price['rsl_raw_prev_year1_apr']))?></td>
                                                <td align="center"><?=check_zero(number_format($price['rsl_raw_prev_year1_may']))?></td>
                                                <td align="center"><?=check_zero(number_format($price['rsl_raw_prev_year1_jun']))?></td>
                                                <td align="center"><?=check_zero(number_format($price['rsl_raw_prev_year1_jul']))?></td>
                                                <td align="center"><?=check_zero(number_format($price['rsl_raw_prev_year1_aug']))?></td>
                                                <td align="center"><?=check_zero(number_format($price['rsl_raw_prev_year1_sep']))?></td>
                                                <td align="center"><?=check_zero(number_format($price['rsl_raw_prev_year1_oct']))?></td>
                                                <td align="center"><?=check_zero(number_format($price['rsl_raw_prev_year1_nov']))?></td>
                                                <td align="center"><?=check_zero(number_format($price['rsl_raw_prev_year1_dec']))?></td>
                                                <td align="center"><?=check_zero(number_format($price['rsl_raw_prev_year1_avg']))?></td>
                                                <td align="center"><?=check_zero(number_format($price['rsl_raw_prev_year1_min']))?></td>
                                                <td align="center"><?=check_zero(number_format($price['rsl_raw_prev_year1_max']))?></td>
                                            </tr>
                                            
                                            <tr>
                                                <td><?=$year?> Budget</td>
                                                <td align="center"><?=check_zero(number_format($price['rsl_raw1']))?></td>
                                                <td align="center"><?=check_zero(number_format($price['rsl_raw2']))?></td>
                                                <td align="center"><?=check_zero(number_format($price['rsl_raw3']))?></td>
                                                <td align="center"><?=check_zero(number_format($price['rsl_raw4']))?></td>
                                                <td align="center"><?=check_zero(number_format($price['rsl_raw5']))?></td>
                                                <td align="center"><?=check_zero(number_format($price['rsl_raw6']))?></td>
                                                <td align="center"><?=check_zero(number_format($price['rsl_raw7']))?></td>
                                                <td align="center"><?=check_zero(number_format($price['rsl_raw8']))?></td>
                                                <td align="center"><?=check_zero(number_format($price['rsl_raw9']))?></td>
                                                <td align="center"><?=check_zero(number_format($price['rsl_raw10']))?></td>
                                                <td align="center"><?=check_zero(number_format($price['rsl_raw11']))?></td>
                                                <td align="center"><?=check_zero(number_format($price['rsl_raw12']))?></td>
                                                <td align="center"><?=check_zero(number_format($price['rsl_raw_avg']))?></td>
                                                <td align="center"><?=check_zero(number_format($price['rsl_raw_min']))?></td>
                                                <td align="center"><?=check_zero(number_format($price['rsl_raw_max']))?></td>
                                            </tr>
                                            
                                            <!-- CHOOKSIES CUT UPS -->
                                            <tr>
                                                <td rowspan="3" class="rowspan-center" style="vertical-align : middle;text-align:center;">CHOOKSIES CUT UPS</td>
                                                <td><?=$year - 2?> Actual</td>
                                                <td align="center"><?=check_zero(number_format($price['rsl_cutups_prev_year2_jan']))?></td>
                                                <td align="center"><?=check_zero(number_format($price['rsl_cutups_prev_year2_feb']))?></td>
                                                <td align="center"><?=check_zero(number_format($price['rsl_cutups_prev_year2_mar']))?></td>
                                                <td align="center"><?=check_zero(number_format($price['rsl_cutups_prev_year2_apr']))?></td>
                                                <td align="center"><?=check_zero(number_format($price['rsl_cutups_prev_year2_may']))?></td>
                                                <td align="center"><?=check_zero(number_format($price['rsl_cutups_prev_year2_jun']))?></td>
                                                <td align="center"><?=check_zero(number_format($price['rsl_cutups_prev_year2_jul']))?></td>
                                                <td align="center"><?=check_zero(number_format($price['rsl_cutups_prev_year2_aug']))?></td>
                                                <td align="center"><?=check_zero(number_format($price['rsl_cutups_prev_year2_sep']))?></td>
                                                <td align="center"><?=check_zero(number_format($price['rsl_cutups_prev_year2_oct']))?></td>
                                                <td align="center"><?=check_zero(number_format($price['rsl_cutups_prev_year2_nov']))?></td>
                                                <td align="center"><?=check_zero(number_format($price['rsl_cutups_prev_year2_dec']))?></td>
                                                <td align="center"><?=check_zero(number_format($price['rsl_cutups_prev_year2_avg']))?></td>
                                                <td align="center"><?=check_zero(number_format($price['rsl_cutups_prev_year2_min']))?></td>
                                                <td align="center"><?=check_zero(number_format($price['rsl_cutups_prev_year2_max']))?></td>
                                            </tr>
                                            
                                            <tr>
                                                <td><?=$year - 1?> YEE</td>
                                                <td align="center"><?=check_zero(number_format($price['rsl_cutups_prev_year1_jan']))?></td>
                                                <td align="center"><?=check_zero(number_format($price['rsl_cutups_prev_year1_feb']))?></td>
                                                <td align="center"><?=check_zero(number_format($price['rsl_cutups_prev_year1_mar']))?></td>
                                                <td align="center"><?=check_zero(number_format($price['rsl_cutups_prev_year1_apr']))?></td>
                                                <td align="center"><?=check_zero(number_format($price['rsl_cutups_prev_year1_may']))?></td>
                                                <td align="center"><?=check_zero(number_format($price['rsl_cutups_prev_year1_jun']))?></td>
                                                <td align="center"><?=check_zero(number_format($price['rsl_cutups_prev_year1_jul']))?></td>
                                                <td align="center"><?=check_zero(number_format($price['rsl_cutups_prev_year1_aug']))?></td>
                                                <td align="center"><?=check_zero(number_format($price['rsl_cutups_prev_year1_sep']))?></td>
                                                <td align="center"><?=check_zero(number_format($price['rsl_cutups_prev_year1_oct']))?></td>
                                                <td align="center"><?=check_zero(number_format($price['rsl_cutups_prev_year1_nov']))?></td>
                                                <td align="center"><?=check_zero(number_format($price['rsl_cutups_prev_year1_dec']))?></td>
                                                <td align="center"><?=check_zero(number_format($price['rsl_cutups_prev_year1_avg']))?></td>
                                                <td align="center"><?=check_zero(number_format($price['rsl_cutups_prev_year1_min']))?></td>
                                                <td align="center"><?=check_zero(number_format($price['rsl_cutups_prev_year1_max']))?></td>
                                            </tr>
                                            
                                            <tr>
                                                <td><?=$year?> Budget</td>
                                                <td align="center"><?=check_zero(number_format($price['rsl_cutups1']))?></td>
                                                <td align="center"><?=check_zero(number_format($price['rsl_cutups2']))?></td>
                                                <td align="center"><?=check_zero(number_format($price['rsl_cutups3']))?></td>
                                                <td align="center"><?=check_zero(number_format($price['rsl_cutups4']))?></td>
                                                <td align="center"><?=check_zero(number_format($price['rsl_cutups5']))?></td>
                                                <td align="center"><?=check_zero(number_format($price['rsl_cutups6']))?></td>
                                                <td align="center"><?=check_zero(number_format($price['rsl_cutups7']))?></td>
                                                <td align="center"><?=check_zero(number_format($price['rsl_cutups8']))?></td>
                                                <td align="center"><?=check_zero(number_format($price['rsl_cutups9']))?></td>
                                                <td align="center"><?=check_zero(number_format($price['rsl_cutups10']))?></td>
                                                <td align="center"><?=check_zero(number_format($price['rsl_cutups11']))?></td>
                                                <td align="center"><?=check_zero(number_format($price['rsl_cutups12']))?></td>
                                                <td align="center"><?=check_zero(number_format($price['rsl_cutups_avg']))?></td>
                                                <td align="center"><?=check_zero(number_format($price['rsl_cutups_min']))?></td>
                                                <td align="center"><?=check_zero(number_format($price['rsl_cutups_max']))?></td>
                                            </tr>
                                            
                                            <!-- RSL GIZZARD / LIVER -->
                                            <tr>
                                                <td rowspan="3" class="rowspan-center" style="vertical-align : middle;text-align:center;">GIZZARD / LIVER</td>
                                                <td><?=$year - 2?> Actual</td>
                                                <td align="center"><?=check_zero(number_format($price['rsl_liver_prev_year2_jan']))?></td>
                                                <td align="center"><?=check_zero(number_format($price['rsl_liver_prev_year2_feb']))?></td>
                                                <td align="center"><?=check_zero(number_format($price['rsl_liver_prev_year2_mar']))?></td>
                                                <td align="center"><?=check_zero(number_format($price['rsl_liver_prev_year2_apr']))?></td>
                                                <td align="center"><?=check_zero(number_format($price['rsl_liver_prev_year2_may']))?></td>
                                                <td align="center"><?=check_zero(number_format($price['rsl_liver_prev_year2_jun']))?></td>
                                                <td align="center"><?=check_zero(number_format($price['rsl_liver_prev_year2_jul']))?></td>
                                                <td align="center"><?=check_zero(number_format($price['rsl_liver_prev_year2_aug']))?></td>
                                                <td align="center"><?=check_zero(number_format($price['rsl_liver_prev_year2_sep']))?></td>
                                                <td align="center"><?=check_zero(number_format($price['rsl_liver_prev_year2_oct']))?></td>
                                                <td align="center"><?=check_zero(number_format($price['rsl_liver_prev_year2_nov']))?></td>
                                                <td align="center"><?=check_zero(number_format($price['rsl_liver_prev_year2_dec']))?></td>
                                                <td align="center"><?=check_zero(number_format($price['rsl_liver_prev_year2_avg']))?></td>
                                                <td align="center"><?=check_zero(number_format($price['rsl_liver_prev_year2_min']))?></td>
                                                <td align="center"><?=check_zero(number_format($price['rsl_liver_prev_year2_max']))?></td>
                                            </tr>
                                            
                                            <tr>
                                                <td><?=$year - 1?> YEE</td>
                                                <td align="center"><?=check_zero(number_format($price['rsl_liver_prev_year1_jan']))?></td>
                                                <td align="center"><?=check_zero(number_format($price['rsl_liver_prev_year1_feb']))?></td>
                                                <td align="center"><?=check_zero(number_format($price['rsl_liver_prev_year1_mar']))?></td>
                                                <td align="center"><?=check_zero(number_format($price['rsl_liver_prev_year1_apr']))?></td>
                                                <td align="center"><?=check_zero(number_format($price['rsl_liver_prev_year1_may']))?></td>
                                                <td align="center"><?=check_zero(number_format($price['rsl_liver_prev_year1_jun']))?></td>
                                                <td align="center"><?=check_zero(number_format($price['rsl_liver_prev_year1_jul']))?></td>
                                                <td align="center"><?=check_zero(number_format($price['rsl_liver_prev_year1_aug']))?></td>
                                                <td align="center"><?=check_zero(number_format($price['rsl_liver_prev_year1_sep']))?></td>
                                                <td align="center"><?=check_zero(number_format($price['rsl_liver_prev_year1_oct']))?></td>
                                                <td align="center"><?=check_zero(number_format($price['rsl_liver_prev_year1_nov']))?></td>
                                                <td align="center"><?=check_zero(number_format($price['rsl_liver_prev_year1_dec']))?></td>
                                                <td align="center"><?=check_zero(number_format($price['rsl_liver_prev_year1_avg']))?></td>
                                                <td align="center"><?=check_zero(number_format($price['rsl_liver_prev_year1_min']))?></td>
                                                <td align="center"><?=check_zero(number_format($price['rsl_liver_prev_year1_max']))?></td>
                                            </tr>
                                            
                                            <tr>
                                                <td><?=$year?> Budget</td>
                                                <td align="center"><?=check_zero(number_format($price['rsl_liver1']))?></td>
                                                <td align="center"><?=check_zero(number_format($price['rsl_liver2']))?></td>
                                                <td align="center"><?=check_zero(number_format($price['rsl_liver3']))?></td>
                                                <td align="center"><?=check_zero(number_format($price['rsl_liver4']))?></td>
                                                <td align="center"><?=check_zero(number_format($price['rsl_liver5']))?></td>
                                                <td align="center"><?=check_zero(number_format($price['rsl_liver6']))?></td>
                                                <td align="center"><?=check_zero(number_format($price['rsl_liver7']))?></td>
                                                <td align="center"><?=check_zero(number_format($price['rsl_liver8']))?></td>
                                                <td align="center"><?=check_zero(number_format($price['rsl_liver9']))?></td>
                                                <td align="center"><?=check_zero(number_format($price['rsl_liver10']))?></td>
                                                <td align="center"><?=check_zero(number_format($price['rsl_liver11']))?></td>
                                                <td align="center"><?=check_zero(number_format($price['rsl_liver12']))?></td>
                                                <td align="center"><?=check_zero(number_format($price['rsl_liver_avg']))?></td>
                                                <td align="center"><?=check_zero(number_format($price['rsl_liver_min']))?></td>
                                                <td align="center"><?=check_zero(number_format($price['rsl_liver_max']))?></td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
							</div>
						</div>
					</div>

                    <div id="opex-bc-tab" class="tab-pane fade">
                        <div class="col-lg-12 dashboard-row">
                            <div class="dashboard-label text-center">
                                <label><strong>OPEX PER GL Account</strong></label>
                            </div>

                            <div class="col-lg-12">
                                <table class="table table-hover table-bordered table-striped" id="opex-per-gl-tbl">
                                    <thead>
                                        <tr>
                                            <th>GL Account</th>
                                            <th class="text-center"><?=$year?> B</th>
                                            <th class="text-center"><?=$year - 1?> F</th>
                                            <th class="text-center"><?=$year - 2?> A</th>
                                            <th class="text-center"><?=$year?> vs <?=$year-1?></th>
                                            <th class="text-center">%</th>
                                            <th class="text-center"><?=$year?> vs <?=$year-2?></th>
                                            <th class="text-center">%</th>
                                        </tr>
                                    </thead>

                                    <tbody>

                                        <?=$opex_tbl?>

                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <div id="opex-support-tab" class="tab-pane fade">
                        <div class="col-lg-12 dashboard-row">
                            <div class="dashboard-label text-center">
                                <label><strong>OPEX Support</strong></label>
                            </div>

                            <div class="col-lg-12">
                                <table class="table table-bordered" id="opex-dept-tbl">
                                    <thead>
                                        <tr>
                                            <th>GL Account</th>
                                            <th class="text-center"><?=$year?> B</th>
                                            <th class="text-center"><?=$year - 1?> F</th>
                                            <th class="text-center"><?=$year - 2?> A</th>
                                            <th class="text-center"><?=$year?> vs <?=$year-1?></th>
                                            <th class="text-center">%</th>
                                            <th class="text-center"><?=$year?> vs <?=$year-2?></th>
                                            <th class="text-center">%</th>
                                        </tr>
                                    </thead>

                                    <tbody>

                                        <?=$opex_unit_tbl?>

                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <div id="opex-pnl-tab" class="tab-pane fade">
                        <div class="col-lg-12 dashboard-row">
                            <div class="dashboard-label text-center">
                                <label><strong>Profit & Loss</strong></label>
                            </div>

                            <div class="col-lg-12">
                                 <table class="table table-hover table-bordered table-striped" id="pnl-tbl">
                                    <thead>
                                        <tr>
                                            <th></th>
                                            <th>BUDGET <?=$year?></th>
                                            <th>YEE <?=$year - 1?></th>
                                            <th>Actual <?=$year - 2?></th>
                                            <th>Budget vs YEE <?=$year - 1?></th>
                                            <th>%</th>
                                            <th>Budget vs <?=$year - 2?></th>
                                            <th>%</th>
                                        </tr>
                                    </thead>

                                    <tbody>
                                        <?=$pnl_tbl?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
				</div>
			</div>

			<script type="text/javascript">

				var base_url = $('#base_url').val();
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


				harvestable_birds = new Chart(document.getElementById("harvestable-birds-chart"), {
				    type: 'bar',
				    data: {
                        labels: ['<?=$year?>', '<?=$year - 1?>', '<?=$year - 2?>'],
                        datasets: [{
                            label: "",
                            backgroundColor: ["#3e95cd", "#8e5ea2", "#3cba9f", "#e8c3b9", "#c45850"],
                            data: [<?=$harvested_heads?>, <?=$harvested_heads1?>, <?=$harvested_heads2?>]
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
							xAxes: [{
			                    barPercentage: 0.4,
			                    categoryPercentage: 1
			                }],
			                yAxes: [{
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
				                }
				            }],
						},

						plugins: {
						    labels: {
								render: 'value',
								textShadow: true,
								render: function (args) {
									var value = args.value;
									if(value > 1000 && value < 1000000){
										value = value/1000;
										value = number_format(value, 2) + ' K';
									}else if(value >= 1000000 && value < 100000000){
										value = value/1000000;
										value = number_format(value, 2) + ' M';
									}else if(value > 99 && value < 999){
										value = value/1000;
										value = number_format(value, 2) + ' K';
									}else{
										 value = '';
									}
									return value;
								},
							},
						}
	    			}
	    		});

	    		total_sales = new Chart(document.getElementById("total-sales-chart"), {
				    type: 'bar',
				    data: {
                        labels: ['<?=$year?>', '<?=$year - 1?>', '<?=$year - 2?>'],
                        datasets: [{
                            label: "",
                            backgroundColor: ["#3cba9f","#e8c3b9","#c45850"],
                            data: [<?=$total_sales_unit?>, <?=$sales_unit1?>, <?=$sales_unit2?>]
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
							xAxes: [{
			                    barPercentage: 0.4,
			                    categoryPercentage: 1
			                }],
			                yAxes: [{
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
				                }
				            }],
						},

						plugins: {
						    labels: {
								render: 'value',
								textShadow: true,
								render: function (args) {
									var value = args.value;
									if(value > 1000 && value < 1000000){
										value = value/1000;
										value = number_format(value, 2) + ' K';
									}else if(value >= 1000000 && value < 100000000){
										value = value/1000000;
										value = number_format(value, 2) + ' M';
									}else if(value > 99 && value < 999){
										value = value/1000;
										value = number_format(value, 2) + ' K';
									}else{
										 value = '';
									}
									return value;
								},
							},
						}
	    			}
	    		});

	    		net_sales = new Chart(document.getElementById("net-sales-chart"), {
				    type: 'bar',
				    data: {
                        labels: ['<?=$year?>', '<?=$year-1?>', '<?=$year-1?>'],
                        datasets: [{
                            label: "",
                            backgroundColor: ["#c9a2f2","#a4d9d6"],
                            data: [<?=$net_sales?>, <?=$net_sales1?>, <?=$net_sales2?>]
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
							xAxes: [{
			                    barPercentage: 0.4,
			                    categoryPercentage: 1
			                }],
			                yAxes: [{
				                ticks: {
				                    fontColor: "rgba(0,0,0,0.7)",
				                    beginAtZero: true,
				                    userCallback: function(value, index, values) {
										
										if(value >= 1000000000){
											value = value/1000000000;
											value = number_format(value, 2) + ' B';
										}else if(value >= 1000000 && value < 1000000000){

											value = value/1000000;
											value = number_format(value, 1) + ' M';
										}else if(value > 1000 && value < 1000000){
											value = value/1000;
											value = number_format(value) + ' K';
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
				            }],
						},

						plugins: {
						    labels: {
								render: 'value',
								textShadow: true,
								render: function (args) {
									var value = args.value;
									if(value >= 1000000000){
										value = value/1000000000;
										value = number_format(value, 2) + ' B';
									}else if(value >= 1000000 && value < 100000000){
										value = value/1000000;
										value = number_format(value, 2) + ' M';
									}else if(value > 1000 && value < 1000000){
										value = value/1000;
										value = number_format(value, 2) + ' K';
									}else if(value > 99 && value < 999){
										value = value/1000;
										value = number_format(value, 2) + ' K';
									}else{
										 value = '';
									}
									return value;
								},
							},
						}
	    			}
	    		});

	    		$.ajax({
                    url:  base_url + 'admin/sales-mix/<?=$year?>',
                    method: 'GET',
                    success:function(response){
                        var parse_response = JSON.parse(response);
                        if(parse_response['result'] == 1){
                            var total = 0;
                            var total1 = 0;
                            var total2 = 0;
                            var segment = [];
                            var sales_unit = [];
                            var percentage = [];
                            var sales_mix_tbl = '';

                            for(var b in parse_response['info']){
                                segment.push(parse_response['info'][b].report_sales_mix_name);
                                total  += parseFloat(parse_response['info'][b].total_sales_mix);
                                total1  += parseFloat(parse_response['info'][b].total_sales_mix1);
                                total2  += parseFloat(parse_response['info'][b].total_sales_mix2);
                                sales_unit.push(parseFloat(parse_response['info'][b].total_sales_mix));
                            }

                            var length = segment.length;

                            for(var c = 0; c < length; c++){
                                var segment_percent = number_format((parseFloat(sales_unit[c]) / total) * 100);

                                percentage.push(segment_percent);

                                sales_mix_tbl += '<tr>';
                                sales_mix_tbl +='<td>' + segment[c] + '</td>';
                                sales_mix_tbl +='<td>' + segment_percent + '%</td>';
                                sales_mix_tbl +='<td>' + total1  + '%</td>';
                                sales_mix_tbl +='<td>' + total2  + '%</td>';
                                sales_mix_tbl +='</tr>';
                            }

                            $('#sales-mix-tbl > tbody').empty();
                            $('#sales-mix-tbl > tbody').append(sales_mix_tbl);
                        }else{
                            console.log('Error please contact your administrator.');
                        }

                        sales_mix = new Chart(document.getElementById("sales-mix-chart"), {
                            type: 'doughnut',
                            data: {
                                labels: segment,
                                datasets: [
                                {
                                    label: "Population (millions)",
                                    backgroundColor: ["#3e95cd", "#8e5ea2","#3cba9f","#e8c3b9","#c45850"],
                                    data: percentage
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
                                        fontColor: ['#fff', '#fff', '#fff', '#fff', '#fff'],
                                        fontSize: 12,
                                        textShadow: true,
                                        shadowBlur: 10,
                                    }
                                }
                            }
                        });
                    }

                });

				noi = new Chart(document.getElementById("noi-chart"), {
				    type: 'bar',
				    data: {
				    	labels: ['2020', '2019'],
				      	datasets: [{
				      		label: "",
          					backgroundColor: ["#3e95cd", "#8e5ea2", "#3cba9f","#e8c3b9","#c45850"],
          					data: [<?=$noi?>, 200400000]
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
							xAxes: [{
			                    barPercentage: 0.4,
			                    categoryPercentage: 1
			                }],
			                yAxes: [{
				                ticks: {
				                    fontColor: "rgba(0,0,0,0.7)",
				                    beginAtZero: true,
				                    userCallback: function(value, index, values) {
										
										if(value >= 1000000000){
											value = value/1000000000;
											value = number_format(value, 2) + ' B';
										}else if(value >= 1000000 && value < 1000000000){

											value = value/1000000;
											value = number_format(value, 1) + ' M';
										}else if(value > 1000 && value < 1000000){
											value = value/1000;
											value = number_format(value) + ' K';
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
				            }],
						},

						plugins: {
						    labels: {
								render: 'value',
								textShadow: true,
								render: function (args) {
									var value = args.value;
									if(value >= 1000000000){
										value = value/1000000000;
										value = number_format(value, 2) + ' B';
									}else if(value >= 1000000 && value < 100000000){
										value = value/1000000;
										value = number_format(value, 2) + ' M';
									}else if(value > 1000 && value < 1000000){
										value = value/1000;
										value = number_format(value, 2) + ' K';
									}else if(value > 99 && value < 999){
										value = value/1000;
										value = number_format(value, 2) + ' K';
									}else{
										 value = '';
									}
									return value;
								},
							},
						}
	    			}
	    		});

	    		capex_chart = new Chart(document.getElementById("capex-chart"), {
				    type: 'bar',
				    data: {
                        labels: ['<?=$year?>', '<?=$year - 1?>', '<?=$year - 2?>'],
                        datasets: [{
                            label: "",
                            backgroundColor: ["#3e95cd", "#8e5ea2", "#3cba9f","#e8c3b9","#c45850"],
                            data: [<?=$capex?>, <?=$capex1?>, <?=$capex2?>]
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
							xAxes: [{
			                    barPercentage: 0.4,
			                    categoryPercentage: 1
			                }],
			                yAxes: [{
				                ticks: {
				                    fontColor: "rgba(0,0,0,0.7)",
				                    beginAtZero: true,
				                    userCallback: function(value, index, values) {
										
										if(value >= 1000000000){
											value = value/1000000000;
											value = number_format(value, 2) + ' B';
										}else if(value >= 1000000 && value < 1000000000){

											value = value/1000000;
											value = number_format(value, 1) + ' M';
										}else if(value > 1000 && value < 1000000){
											value = value/1000;
											value = number_format(value) + ' K';
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
				            }],
						},

						plugins: {
						    labels: {
								render: 'value',
								textShadow: true,
								render: function (args) {
									var value = args.value;
									if(value >= 1000000000){
										value = value/1000000000;
										value = number_format(value, 2) + ' B';
									}else if(value >= 1000000 && value < 100000000){
										value = value/1000000;
										value = number_format(value, 2) + ' M';
									}else if(value > 1000 && value < 1000000){
										value = value/1000;
										value = number_format(value, 2) + ' K';
									}else if(value > 99 && value < 999){
										value = value/1000;
										value = number_format(value, 2) + ' K';
									}else{
										 value = '';
									}
									return value;
								},
							},
						}
	    			}
	    		});

	    		broiler_cost = new Chart(document.getElementById("broiler-cost-chart"), {
				    type: 'bar',
				    data: {
                        labels: ['<?=$year?>', '<?=$year - 1?>', '<?=$year - 2?>'],
                        datasets: [{
                            label: "",
                            backgroundColor: ["#3e95cd", "#3cba9f", "#8e5ea2", "#e8c3b9","#c45850"],
                            data: [<?=$broiler_cost?>, <?=$broiler_cost1?>, <?=$broiler_cost2?>]
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
							xAxes: [{
			                    barPercentage: 0.4,
			                    categoryPercentage: 1
			                }],
			                yAxes: [{
				                ticks: {
				                    fontColor: "rgba(0,0,0,0.7)",
				                    beginAtZero: true,
				                    userCallback: function(value, index, values) {
										
										if(value >= 1000000000){
											value = value/1000000000;
											value = number_format(value, 2) + ' B';
										}else if(value >= 1000000 && value < 1000000000){

											value = value/1000000;
											value = number_format(value, 1) + ' M';
										}else if(value > 1000 && value < 1000000){
											value = value/1000;
											value = number_format(value) + ' K';
										}else if(value > 99 && value < 999){
											value = value/1000;
											value = number_format(value, 1) + ' K';
										}else{
											value = number_format(value, 2) + '/kg';
										}
										return value;
									},
				                    padding: 20
				                }
				            }],
						},

						plugins: {
						    labels: {
								render: 'value',
								textShadow: true,
								render: function (args) {
									var value = args.value;
									if(value >= 1000000000){
										value = value/1000000000;
										value = number_format(value, 2) + ' B';
									}else if(value >= 1000000 && value < 100000000){
										value = value/1000000;
										value = number_format(value, 2) + ' M';
									}else if(value > 1000 && value < 1000000){
										value = value/1000;
										value = number_format(value, 2) + ' K';
									}else if(value > 99 && value < 999){
										value = value/1000;
										value = number_format(value, 2) + ' K';
									}else{
										value = number_format(value, 2) + '/kg';
									}
									return value;
								},
							},
						}
	    			}
	    		});
	    		

                cg_chart = new Chart(document.getElementById("cg-chart"), {
                    type: 'horizontalBar',
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