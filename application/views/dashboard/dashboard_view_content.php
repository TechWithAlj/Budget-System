			
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


                    <!-- <div class="col-lg-12 text-right">
                        <a href="<?=base_url('business-center/download-pdf/' . $year)?>" target="_blank"><span class="glyphicon glyphicon-download-alt"></span>&nbsp;&nbsp; Download PDF</a>
                    </div> -->

                    <div class="text-center">
                        <h3><strong><?=$dashboard_type?></strong></h3>
                    </div>
                </div>

                <br /><br />

				<ul class="nav nav-tabs">
   					<li class="active"><a data-toggle="tab" class="tab-letter" href="#executive-tab">Executive Summary</a></li>

				    <li><a data-toggle="tab" href="#volume-tab" class="capex-graph-letter">Volume</a></li>

                    <?php if($type != 'NATIONAL'):?>
				            <li><a data-toggle="tab" href="#price-tab" class="capex-graph-letter">Price Assumption</a></li>

                    <?php endif;?>

				    <li><a data-toggle="tab" href="#opex-bc-tab" class="capex-graph-letter">OPEX</a></li>
                    
                    <?php if($user_type == 1):?>
                        <li><a data-toggle="tab" href="#cm-store-tab" class="capex-graph-letter">NOI Per Store</a></li>
                    <?php endif;?>

                    <li><a data-toggle="tab" href="#pnl-bc-tab" class="capex-graph-letter">PNL</a></li>
                    <!-- <li><a data-toggle="tab" href="#price-adjustment-bc-tab" class="capex-graph-letter">Price Adjustment</a></li> -->
				    <!-- <li><a data-toggle="tab" href="#ahg-user-tab" class="capex-graph-letter">OPEX per Department</a></li> -->
  				</ul>

  				<div class="tab-content">
    				<div id="executive-tab" class="tab-pane fade in active">
    					<div class="row">
    						<div class="col-lg-12 dashboard-row">
    							<!-- <div class="col-lg-4">
    								<div class="dashboard-label text-center">
    									<label><strong>Harvestable Birds</strong></label>
    									<canvas id="harvestable-birds-chart" height="150px" width="200px"></canvas>
    									<label class="chart-remarks"><strong><?=$harvest_report?></strong></label>
    								</div>
    							</div> -->

    							<div class="col-lg-6">
    								<div class="dashboard-label text-center">
    									<label><strong>Total Sales Units (In Millions)</strong></label>
    									<canvas id="total-sales-chart" height="100px" width="200px"></canvas>
    									<label class="chart-remarks"><strong><?=$sales_unit_report?></strong></label>
    								</div>
    							</div>

    							<div class="col-lg-6">
    								<div class="dashboard-label text-center">
    									<label><strong>Net Sales (In Millions)</strong></label>
    									<canvas id="net-sales-chart" height="100px" width="200px"></canvas>
    									<label class="chart-remarks"><strong><?=$net_sales_report?></strong></label>
    								</div>
    							</div>
    						</div>

    						<div class="col-lg-12 dashboard-row">
    							<div class="dashboard-label text-center">
    								<label><strong></strong></label>
    							</div>
    							<div class="col-lg-6" style="padding-left: 150px;padding-right: 150px;">
    								<div class="dashboard-label text-center">
    									<label><strong>Sales Mix</strong></label>
    									<canvas id="sales-mix-chart"></canvas>
    								</div>
    							</div>

    							<div class="col-lg-6">
    								<div class="dashboard-label text-center">
    									<label><strong>Sales Mix Comparative</strong></label>
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
    											<?=$sales_mix_tbl?>
    										</tbody>
    									</table>
    								</div>
    							</div>
    						</div>

    						<div class="col-lg-12 dashboard-row">
    							<div class="col-lg-6">
    								<div class="dashboard-label text-center">
    									<label><strong>NOI Comparative (In Millions)</strong></label>
    									<canvas id="noi-chart" height="145px"></canvas>
                                        <label class="chart-remarks"><strong><?=$noi_report?></strong></label>
    								</div>
    							</div>

                                <div class="col-lg-6">
                                    <div class="dashboard-label text-center">
                                        <label><strong>CAPEX (In Millions)</strong></label>
                                        <canvas id="capex-chart" height="145px"></canvas>
                                        <label class="chart-remarks"><strong><?=$capex_report?></strong></label>
                                    </div>
                                </div>
    							
    						</div>

    						<div class="col-lg-12 dashboard-row">
    							<!-- <div class="col-lg-6">
    								<div class="dashboard-label text-center">
    									<label><strong>Broiler Cost</strong></label>
    									<canvas id="broiler-cost-chart" height="145px"></canvas>
    									<label class="chart-remarks"><strong><?=$broiler_cost_report?></strong></label>
    								</div>
    							</div> -->

                                <div class="col-lg-6">
                                    <div class="dashboard-label text-center">
                                        <label><strong>OPEX (In Millions)</strong></label>
                                        <canvas id="opex-chart" height="145px"></canvas>
                                        <label class="chart-remarks"><strong><?=$opex_report?></strong></label>
                                    </div>
                                </div>

    							<div class="col-lg-6">
    								<ul id="manpower-list">
    									
    									<?php 
    										echo '<li><span class="fa fa-plus"></span>' . $employee_new . ' Manpower</li><br/><br/><br/>';
                                            $interval = 1;
                                            if($type == 'NATIONAL'){
                                                $interval = 50;
                                            }
    										for($a = 0; $a < $employee_new; $a += $interval){
    											echo ' <li><span class="fa fa-user-circle"></span></li>';		
    										}
    									?>
    									
    								</ul>
    								<div class="dashboard-label text-center"><br /><br />
	    								<label class="chart-remarks"><strong><?=$employee_report?></strong></label>
    								</div>
    							</div>
    						</div>

    						<div class="col-lg-12 dashboard-row">
    							<div class="col-lg-6">
                                    <div class="dashboard-label text-center"><br /><br /><br />
                                        <img src="<?=base_url('assets/img/store-dashboard.png')?>" class="img-responsive text-center" width="150px" style="display: block;margin-left: auto;margin-right: auto;"><br /><br />

                                        <label class="chart-remarks"><strong><?=$outlet_report?></strong></label>
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
                                            <?=$price_tbl?>
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
                                            <th class="text-center"><?=$year?> vs <?=$year-4?></th>
                                            <th class="text-center">%</th>
                                        </tr>
                                    </thead>

                                    <tbody>

                                        <!-- <?php
                                            $total_opex_gl1 = 0;
                                            foreach($opex_bc_gl as $row):

                                                $total_opex_gl1 += $row->total_amount;
                                        ?>

                                            <tr>
                                                <td><?=$row->gl_code?></td>
                                                <td><?=$row->gl_sub_name?></td>
                                                <td class="text-right"><?=number_format($row->total_amount)?></td>
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                            </tr>

                                        <?php endforeach;?> -->

                                        <?=$opex_tbl?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <div id="cm-store-tab" class="tab-pane fade">
                        <div class="col-lg-12 dashboard-row">
                            <div class="dashboard-label text-center"><br />
                                <label><strong><h4>NOI PER STORE</strong></h4></label><br /><br />
                            </div>

                            <div class="col-lg-12">
                                <table class="table table-hover table-bordered table-striped" id="cm-per-store-tbl">
                                    <thead>
                                        <tr>
                                            <th rowspan="2">Business Center</th>
                                            <th rowspan="2">Store</th>
                                            <th rowspan="2">Brand</th>
                                            <th class="text-center" rowspan="2">Sales Unit</th>
                                            <th class="text-center" colspan="2">Net Sales</th>
                                            <th class="text-center" colspan="2">Variable Cost</th>
                                            <th class="text-center" colspan="2">Contribution Margin</th>
                                            <th class="text-center" rowspan="2">CM %</th>
                                            <th class="text-center" colspan="2">Period Cost</th>
                                            <th class="text-center" colspan="2">NOI</th>
                                            <th class="text-center" rowspan="2">NOI %</th>
                                            <th rowspan="2"></th>
                                        </tr>

                                        <tr>
                                            <td class="text-center">Amount</td>
                                            <td class="text-center">Per Unit</td>
                                            <td class="text-center">Amount</td>
                                            <td class="text-center">Per Unit</td>
                                            <td class="text-center">Amount</td>
                                            <td class="text-center">Per Unit</td>
                                            <td class="text-center">Amount</td>
                                            <td class="text-center">Per Unit</td>
                                            <td class="text-center">Amount</td>
                                            <td class="text-center">Per Unit</td>
                                        </tr>
                                    </thead>

                                    <tbody>
                                        <?=$cm_store_tbl?>
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <div id="modal-cm-store-monthly" class="modal fade modal-confirm" role="dialog">
                            <div class="modal-dialog" style="width:1250px;">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                                        <strong>Store NOI Details</strong>
                                    </div>

                                    <div class="modal-body">
                                        
                                        <br /><br />

                                        <div class="row">
                                            <label class="col-lg-4"><h5><strong>Business Center: <span id="cm-bc"></span></strong></h5></label>
                                            
                                            <label class="col-lg-4"><h5><strong>Outlet: <span id="cm-outlet"></span></strong></h5></label>
                                            
                                            <label class="col-lg-4"><h5><strong>Brand: <span id="cm-brand"></span></strong></h5></label>
                                        </div><br /><br /><br />

                                        <div class="row">
                                            <div class="col-lg-12">
                                                <div class="text-center"><h4><strong>NOI Monthly</strong></h4></div><br /><br />

                                                <div class="table-responsive">
                                                    <table class="table table-bordered" id="tbl-cm-store-monthly">

                                                    </table>
                                                </div>
                                            </div>
                                        </div>

                                        <br /><br />
                                        <div class="row">
                                            <div class="col-lg-12">
                                                <div class="text-center"><h4><strong>Contribution Margin Per Material</strong></h4></div><br /><br />
                                                <div class="table-responsive">
                                                    <table class="table table-bordered" id="tbl-cm-material">
                                                        <thead>
                                                            <tr>
                                                                <th rowspan="2">Material</th>
                                                                <th class="text-center" rowspan="2">Sales Unit</th>
                                                                <th class="text-center" colspan="2">Net Sales</th>
                                                                <th class="text-center" colspan="2">Variable Cost</th>
                                                                <th class="text-center" colspan="2">Contribution Margin</th>
                                                                <th class="text-center" rowspan="2">CM %</th>
                                                            </tr>

                                                            <tr>
                                                                <td class="text-center">Amount</td>
                                                                <td class="text-center">Per Unit</td>
                                                                <td class="text-center">Amount</td>
                                                                <td class="text-center">Per Unit</td>
                                                                <td class="text-center">Amount</td>
                                                                <td class="text-center">Per Unit</td>
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
                            </div>
                        </div>
                    </div>

                    <div id="pnl-bc-tab" class="tab-pane fade">
                        <div class="col-lg-12 dashboard-row">
                            <div class="dashboard-label text-center">
                                <label><strong>Profit & Loss (In thousands)</strong></label>
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

                     <div id="price-adjustment-bc-tab" class="tab-pane fade">
                        <div class="col-lg-12 dashboard-row">
                            <div class="dashboard-label text-center">
                                <label><strong>Price Adjustment</strong></label>
                            </div>
                            
                            <div class="row">
                                <div class="col-lg-12">
                                    <a href="#" class="btn btn-success btn-sm" data-toggle="modal" data-target="#modal-price">Adjust Price</a>
                                    <br /><br />


                                </div>
                            </div>

                            <div id="modal-price" class="modal fade modal-confirm" role="dialog">
                                <div class="modal-dialog" style="width:1250px;">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <button type="button" class="close" data-dismiss="modal">&times;</button>
                                            <strong>Price Adjustment</strong>
                                        </div>
                                        <form method="POST" action="<?=base_url('business-center/add-price-adjustment')?>" id="update-opex-item">

                                            <input type="hidden" name="year" value="<?=$year?>">
                                            <div class="modal-body">
                                                <br /><br />
                                                <div class="row">
                                                    <div class="col-lg-12">
                                                        <div class="table-responsive">
                                                            <table class="table table-hover table-bordered table-striped" id="tbl-price-adjustment">
                                                                <thead>
                                                                    <tr>
                                                                        <th class="text-center"></th>
                                                                        <th class="text-center">Material/Size</th>
                                                                        <th class="text-center">Jan</th>
                                                                        <th class="text-center">Feb</th>
                                                                        <th class="text-center">Mar</th>
                                                                        <th class="text-center">Apr</th>
                                                                        <th class="text-center">May</th>
                                                                        <th class="text-center">Jun</th>
                                                                        <th class="text-center">Jul</th>
                                                                        <th class="text-center">Aug</th>
                                                                        <th class="text-center">Sep</th>
                                                                        <th class="text-center">Oct</th>
                                                                        <th class="text-center">Nov</th>
                                                                        <th class="text-center">Dec</th>
                                                                    </tr>
                                                                </thead>

                                                                <tbody>
                                                                    <?php

                                                                        foreach($size as $row_size):

                                                                    ?>

                                                                    <tr>
                                                                        <td class=""><a href="#" class="slider-item slider-opex"><span class="fa fa-sliders"></span></a></td>

                                                                        <input type="hidden" id="id" name="id[]" value="<?=$row_size->size_name?>">

                                                                        <td><?=$row_size->size_name?></td>

                                                                        <td class=""><input type="text" class="jan-adjustment" name="price[jan][]"></td>
                                                                        <td class=""><input type="text" class="feb-adjustment" name="price[feb][]"></td>
                                                                        <td class=""><input type="text" class="mar-adjustment" name="price[mar][]"></td>
                                                                        <td class=""><input type="text" class="apr-adjustment" name="price[apr][]"></td>
                                                                        <td class=""><input type="text" class="may-adjustment" name="price[may][]"></td>
                                                                        <td class=""><input type="text" class="jun-adjustment" name="price[jun][]"></td>
                                                                        <td class=""><input type="text" class="jul-adjustment" name="price[jul][]"></td>
                                                                        <td class=""><input type="text" class="aug-adjustment" name="price[aug][]"></td>
                                                                        <td class=""><input type="text" class="sep-adjustment" name="price[sep][]"></td>
                                                                        <td class=""><input type="text" class="oct-adjustment" name="price[oct][]"></td>
                                                                        <td class=""><input type="text" class="nov-adjustment" name="price[nov][]"></td>
                                                                        <td class=""><input type="text" class="dec-adjustment" name="price[dec][]"></td>
                                                                    </tr>

                                                                    <?php endforeach;?>
                                                                    
                                                                    <tr>
                                                                        <td class=""><a href="#" class="slider-item slider-opex"><span class="fa fa-sliders"></span></a></td>

                                                                        <input type="hidden" id="id" name="id[]" value="LIEMPO">

                                                                        <td>LIEMPO</td>

                                                                        <td class=""><input type="text" class="jan-adjustment" name="price[jan][]"></td>
                                                                        <td class=""><input type="text" class="feb-adjustment" name="price[feb][]"></td>
                                                                        <td class=""><input type="text" class="mar-adjustment" name="price[mar][]"></td>
                                                                        <td class=""><input type="text" class="apr-adjustment" name="price[apr][]"></td>
                                                                        <td class=""><input type="text" class="may-adjustment" name="price[may][]"></td>
                                                                        <td class=""><input type="text" class="jun-adjustment" name="price[jun][]"></td>
                                                                        <td class=""><input type="text" class="jul-adjustment" name="price[jul][]"></td>
                                                                        <td class=""><input type="text" class="aug-adjustment" name="price[aug][]"></td>
                                                                        <td class=""><input type="text" class="sep-adjustment" name="price[sep][]"></td>
                                                                        <td class=""><input type="text" class="oct-adjustment" name="price[oct][]"></td>
                                                                        <td class=""><input type="text" class="nov-adjustment" name="price[nov][]"></td>
                                                                        <td class=""><input type="text" class="dec-adjustment" name="price[dec][]"></td>
                                                                    </tr>

                                                                    <tr>
                                                                        <td class=""><a href="#" class="slider-item slider-opex"><span class="fa fa-sliders"></span></a></td>

                                                                        <input type="hidden" id="id" name="id[]" value="DRESSED">

                                                                        <td>DRESSED</td>

                                                                       <td class=""><input type="text" class="jan-adjustment" name="price[jan][]"></td>
                                                                        <td class=""><input type="text" class="feb-adjustment" name="price[feb][]"></td>
                                                                        <td class=""><input type="text" class="mar-adjustment" name="price[mar][]"></td>
                                                                        <td class=""><input type="text" class="apr-adjustment" name="price[apr][]"></td>
                                                                        <td class=""><input type="text" class="may-adjustment" name="price[may][]"></td>
                                                                        <td class=""><input type="text" class="jun-adjustment" name="price[jun][]"></td>
                                                                        <td class=""><input type="text" class="jul-adjustment" name="price[jul][]"></td>
                                                                        <td class=""><input type="text" class="aug-adjustment" name="price[aug][]"></td>
                                                                        <td class=""><input type="text" class="sep-adjustment" name="price[sep][]"></td>
                                                                        <td class=""><input type="text" class="oct-adjustment" name="price[oct][]"></td>
                                                                        <td class=""><input type="text" class="nov-adjustment" name="price[nov][]"></td>
                                                                        <td class=""><input type="text" class="dec-adjustment" name="price[dec][]"></td>
                                                                    </tr>

                                                                    <tr>
                                                                        <td class=""><a href="#" class="slider-item slider-opex"><span class="fa fa-sliders"></span></a></td>

                                                                        <input type="hidden" id="id" name="id[]" value="LIVE BROILER">

                                                                        <td>LIVE</td>

                                                                        <td class=""><input type="text" class="jan-adjustment" name="price[jan][]"></td>
                                                                        <td class=""><input type="text" class="feb-adjustment" name="price[feb][]"></td>
                                                                        <td class=""><input type="text" class="mar-adjustment" name="price[mar][]"></td>
                                                                        <td class=""><input type="text" class="apr-adjustment" name="price[apr][]"></td>
                                                                        <td class=""><input type="text" class="may-adjustment" name="price[may][]"></td>
                                                                        <td class=""><input type="text" class="jun-adjustment" name="price[jun][]"></td>
                                                                        <td class=""><input type="text" class="jul-adjustment" name="price[jul][]"></td>
                                                                        <td class=""><input type="text" class="aug-adjustment" name="price[aug][]"></td>
                                                                        <td class=""><input type="text" class="sep-adjustment" name="price[sep][]"></td>
                                                                        <td class=""><input type="text" class="oct-adjustment" name="price[oct][]"></td>
                                                                        <td class=""><input type="text" class="nov-adjustment" name="price[nov][]"></td>
                                                                        <td class=""><input type="text" class="dec-adjustment" name="price[dec][]"></td>
                                                                    </tr>
                                                                </tbody>
                                                            </table>
                                                        </div>

                                                        <div class="text-right">
                                                            <button type="submit" class="btn btn-success">Apply</button>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>

                            <table class="table table-hover table-bordered table-striped" id="price-adjustment-tbl">
                                <thead>
                                    <tr>
                                        <th></th>
                                        <th>Material</th>
                                        <th class="text-center">Jan</th>
                                        <th class="text-center">Feb</th>
                                        <th class="text-center">Mar</th>
                                        <th class="text-center">Apr</th>
                                        <th class="text-center">May</th>
                                        <th class="text-center">Jun</th>
                                        <th class="text-center">Jul</th>
                                        <th class="text-center">Aug</th>
                                        <th class="text-center">Sep</th>
                                        <th class="text-center">Oct</th>
                                        <th class="text-center">Nov</th>
                                        <th class="text-center">Dec</th>
                                    </tr>
                                </thead>
                                <tbody>

                                    <?php foreach($price_adjustment as $row_adjustment):?>

                                        <tr>
                                            <td class="text-center"><a href="#" class="remove-adjustment remove" data-id="<?=encode($row_adjustment->price_adjustment_id)?>"><span class="fa fa-remove"></span></td>
                                            <td><?=$row_adjustment->material_desc?></td>
                                            <td class="text-right"><?=number_format($row_adjustment->jan_price)?></td>
                                            <td class="text-right"><?=number_format($row_adjustment->feb_price)?></td>
                                            <td class="text-right"><?=number_format($row_adjustment->mar_price)?></td>
                                            <td class="text-right"><?=number_format($row_adjustment->apr_price)?></td>
                                            <td class="text-right"><?=number_format($row_adjustment->may_price)?></td>
                                            <td class="text-right"><?=number_format($row_adjustment->jun_price)?></td>
                                            <td class="text-right"><?=number_format($row_adjustment->jul_price)?></td>
                                            <td class="text-right"><?=number_format($row_adjustment->aug_price)?></td>
                                            <td class="text-right"><?=number_format($row_adjustment->sep_price)?></td>
                                            <td class="text-right"><?=number_format($row_adjustment->oct_price)?></td>
                                            <td class="text-right"><?=number_format($row_adjustment->nov_price)?></td>
                                            <td class="text-right"><?=number_format($row_adjustment->dec_price)?></td>
                                        </tr>

                                    <?php endforeach;?>

                                </tbody>
                            </table>

                            <div id="modal-cancel-adjustment" class="modal fade" role="dialog">
                                <div class="modal-dialog modal-sm">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <button type="button" class="close" data-dismiss="modal">&times;</button>
                                            <strong>Cancel Price Adjustment</strong>
                                        </div>
                                        <div class="modal-body">
                                            <form method="POST" action="<?=base_url('business-center/cancel-adjustment')?>" enctype="multipart/form-data" id="cancel-adjustment">
                                                <input type="hidden" name="id" id="id">
                                                <div id="modal-msg" class="text-center">
                                                    <label>Are you sure to cancel this price adjustment?</label>
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


				/*harvestable_birds = new Chart(document.getElementById("harvestable-birds-chart"), {
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
	    		});*/
                Chart.register(ChartDataLabels);
	    		total_sales = new Chart(document.getElementById("total-sales-chart"), {
				    type: 'line',
				    data: {
				    	labels: ['<?=$year?>', '<?=$year - 1?>', '<?=$year - 2?>'],
				      	datasets: [{
				      		label: "",
                            fill: false,
                            tension: 0.1,
          					data: [<?=$sales_unit?>, <?=$sales_unit1?>, <?=$sales_unit2?>],
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

	    		net_sales = new Chart(document.getElementById("net-sales-chart"), {
				    type: 'line',
				    data: {
				    	labels: ['<?=$year?>', '<?=$year-1?>', '<?=$year-2?>'],
				      	datasets: [{
				      		label: "",
                            fill: false,
                            tension: 0.1,
                            borderColor: '#0b7fab',
          					data: [<?=$net_sales?>, <?=$net_sales1?>, <?=$net_sales2?>],
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

                var percentage = [];
                var segment = [];
                <?php foreach($sales_mix as $row_sales_mix):?>
                    var sales_mix_segment = '<?=$row_sales_mix->segment?>';
                    var sales_mix_amount = parseFloat(<?=$row_sales_mix->sales_mix_amount?>);
                    segment.push(sales_mix_segment);
                    percentage.push(sales_mix_amount);
                <?php endforeach;?>

                sales_mix = new Chart(document.getElementById("sales-mix-chart"), {
                    type: 'doughnut',
                    data: {
                        labels: segment,
                        datasets: [
                        {
                            label: "Population (millions)",
                            backgroundColor: ["#3e95cd", "#8e5ea2","#3cba9f","#e8c3b9","#c45850"],
                            data: percentage,
                            datalabels: {
                                anchor: 'center'
                            },
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
                                fontColor: ['#fff', '#fff', '#fff', '#fff', '#fff'],
                                fontSize: 12,
                                textShadow: true,
                                shadowBlur: 10,
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
                                    value = number_format(value) + ' M';
                                    return value;
                                },
                            },

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
                                    size: 16,
                                },
                                padding: 6,
                                formatter: function(value) {
                                    return number_format(value) + '%';
                                }
                            }
                        }
                    }
                });

				noi = new Chart(document.getElementById("noi-chart"), {
				    type: 'line',
				    data: {
				    	labels: ['<?=$year?>', '<?=$year-1?>', '<?=$year-2?>'],
				      	datasets: [{
				      		label: "",
                            fill: false,
                            tension: 0.1,
                            borderColor: '#03c9a9',
          					data: [<?=$noi?>, <?=$noi1?>, <?=$noi2?>],
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

	    		capex_chart = new Chart(document.getElementById("capex-chart"), {
				    type: 'line',
				    data: {
				    	labels: ['<?=$year?>', '<?=$year - 1?>', '<?=$year - 2?>'],
				      	datasets: [{
				      		label: "",
                            fill: false,
                            tension: 0.1,
                            borderColor: '#0b7fab',
          					data: [<?=$capex?>, <?=$capex1?>, <?=$capex2?>],
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
									/*if(value >= 1000000000){
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

                opex_chart = new Chart(document.getElementById("opex-chart"), {
                    type: 'line',
                    data: {
                        labels: ['<?=$year?>', '<?=$year-1?>', '<?=$year-2?>'],
                        datasets: [{
                            label: "",
                            fill: false,
                            tension: 0.1,
                            borderColor: '#03c9a9',
                            data: [<?=$opex?>, <?=$opex1?>, <?=$opex2?>],
                            datalabels: {
                                align: 'end',
                                anchor: 'end'
                            },
                            backgroundColor: '#03c9a9',
                            borderColor: '#03c9a9'
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

	    		/*broiler_cost = new Chart(document.getElementById("broiler-cost-chart"), {
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
	    		});*/
	    		
			</script>