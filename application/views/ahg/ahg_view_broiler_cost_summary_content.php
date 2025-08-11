	<?php $year = decode($trans_year); $doctype = encode('trans');?>
	<div class="col-lg-12" id="content">
		<div id="breadcrumb-div">
			<ul class="breadcrumb">
			    <li><a href="<?=base_url('ahg')?>"><span class="glyphicon glyphicon-home"></span>&nbsp;Dashboard</a></li>
			    <li><a href="<?=base_url('ahg/broiler-trans/' . $bc_id)?>">Broiler Transaction</a></li>
			    <li class="active">Broiler Cost Summary</li>
			</ul>
		</div>
		<?php
			if($this->session->flashdata('message') != "" ){
				echo $this->session->flashdata('message');
			}
		?>
		<div class="table-responsive">
			<table class="table table-hover table-bordered table-stripe nowrap" id="tbl-view-broiler-result">
				<thead>
					<tr>
						<th width="30%">Computation Name</th>
					<?php for ($i=1; $i <= 12 ; $i++){ ?>
						<th width="auto" class="text-center"><?=date('M', strtotime($year.'-'.$i.'-01'))?></th>
					<?php } ?>
						<th class="text-center">Total/Ave</th>
					</tr>
				</thead>
				<tbody>
					
					<?php
					$doc_ave = 0;
					$medicine_ave = 0;
					$growers_fee_ave = 0;
					$vaccines_ave = 0;
					$feed_cost_ave = 0;
					foreach($broiler_group as $row):
					?>
						<?php
							$broiler_group_name = $row->broiler_group_name;
						?>
						<tr>

							<td style="text-indent: 3%;"><a href="<?=base_url('ahg/view-broiler-trans/' . $bc_id .'/'. encode($row->broiler_group_id).'/'.encode($row->broiler_group_name) . '/' . $trans_year)?>" class="brn btn-xs"><?=$broiler_group_name?></a></td>
							<?php

							$harvested_kilo = 0;
							$get_harvested_kilo =  harvested_kilo($bc_id, $trans_year, $doctype);

							$growers_fee_amount = 0;
							$get_growers_fee_amount = growers_fee_amount($bc_id, $trans_year, $doctype);
							//$get_growers_fee = growers_fee($bc_id, $trans_year, $doctype);

							$feed_cost_amount = 0;
							$get_feed_cost_amount = feed_cost_amount($bc_id, $trans_year, $doctype);
							//$get_feed_cost = feed_cost($bc_id, $trans_year, $doctype);

							$vaccines_amount = 0;
							$get_vaccines_amount = vaccines_amount($bc_id, $trans_year, $doctype);
							//$get_vaccines = vaccines($bc_id, $trans_year, $doctype);

							$medicine_amount = 0;
							$get_medicine_amount = medicine_amount($bc_id, $trans_year, $doctype);
							
							$disinfectant_amount = 0;
							$get_disinfectant_amount = disinfectant_amount($bc_id, $trans_year, $doctype);
							//$get_medicines = medicines($bc_id, $trans_year, $doctype);

							$doc_cost_amount = 0;
							$get_doc_cost_amount = doc_cost_amount($bc_id, $trans_year, $doctype);
							//$get_doc = doc($bc_id, $trans_year, $doctype);

							for ($i=1; $i <= 12 ; $i++){
								//$broiler_group_amunt = $broiler_group_amunt + $get_broiler_group_amunt[$i];
								$harvested_kilo = $harvested_kilo + $get_harvested_kilo[$i];
								if($row->broiler_group_id == 1){
									$doc_cost_amount = $doc_cost_amount + $get_doc_cost_amount[$i];
									//echo '<td class="text-right">'.number_format($get_doc[$i],dec_places_dis(),'.',',').'</td>';
								}
								if($row->broiler_group_id == 2){
									$growers_fee_amount = $growers_fee_amount + $get_growers_fee_amount[$i];
									//echo '<td class="text-right">'.number_format($get_growers_fee[$i],dec_places_dis(),'.',',').'</td>';
								}
								if($row->broiler_group_id == 3){
									$feed_cost_amount = $feed_cost_amount + $get_feed_cost_amount[$i];
									//echo '<td class="text-right">'.number_format($get_feed_cost[$i],dec_places_dis(),'.',',').'</td>';
								}
								if($row->broiler_group_id == 4){
									$vaccines_amount = $vaccines_amount + $get_vaccines_amount[$i];
									//echo '<td class="text-right">'.number_format($get_vaccines[$i],dec_places_dis(),'.',',').'</td>';
								}
								if($row->broiler_group_id == 5){
									$medicine_amount = $medicine_amount + $get_medicine_amount[$i];
									$disinfectant_amount = $disinfectant_amount + $get_disinfectant_amount[$i];
									//echo '<td class="text-right">'.number_format($get_medicines[$i],dec_places_dis(),'.',',').'</td>';
								}
							?>
								<td class="text-right"><?=number_format(get_broiler_cost_detail($row->broiler_group_id, $bc_id, $trans_year, $i, $doctype),dec_places_dis(),'.',',')?></td>
								<!-- GET THE TOTAL AVERAGES -->
							<?php
								if($i == 12){
									if($row->broiler_group_id == 1){
										$doc_ave = $doc_cost_amount/$harvested_kilo;
							?>
										<td class="text-right"><?=number_format($doc_ave,dec_places_dis(),'.',',')?></td>
							<?php
									}
								}
								if($i == 12){
									if($row->broiler_group_id == 2){
										$growers_fee_ave = $growers_fee_amount/$harvested_kilo;
							?>
										<td class="text-right"><?=number_format($growers_fee_ave,dec_places_dis(),'.',',')?></td>

							<?php
									}
								}
								if($i == 12){
									if($row->broiler_group_id == 3){
										$feed_cost_ave = $feed_cost_amount/$harvested_kilo;
							?>
										<td class="text-right"><?=number_format($feed_cost_ave,dec_places_dis(),'.',',')?></td>
							<?php
									}
								}
								if($i == 12){
									if($row->broiler_group_id == 4){
										$vaccines_ave = $vaccines_amount/$harvested_kilo;
							?>
										<td class="text-right"><?=number_format($vaccines_ave,dec_places_dis(),'.',',')?></td>
							<?php
									}
								}
								if($i == 12){
									if($row->broiler_group_id == 5){
										$total =  $disinfectant_amount + $medicine_amount;
										$medicine_ave = $total/$harvested_kilo;
							?>
										<td class="text-right"><?=number_format($medicine_ave,dec_places_dis(),'.',',')?></td>
							<?php
									}
								}
							}
							?>
							
							<td class="text-right"></td>
						</tr>
						

					<?php endforeach; ?>
				</tbody>
				<tfoot>
					<tr>
						<th>BROILER COST/KILO</th>
					<?php
					
					$broiler_group_amunt = 0;
					for ($i=1; $i <= 12 ; $i++){
						$broiler_group_amunt = $broiler_group_amunt + get_broiler_cost($bc_id, $trans_year, $i, $doctype);
					?>
						<th class="text-right"><?=number_format(get_broiler_cost($bc_id, $trans_year, $i, $doctype),dec_places_dis(),'.',',')?></th>
					<?php } ?>
					<?php 
						$broiler_cost_ave = $medicine_ave + $doc_ave + $growers_fee_ave + $feed_cost_ave + $vaccines_ave;
					?>
						<th class="text-right"><?=number_format($broiler_cost_ave,dec_places_dis(),'.',',')?></th>
					</tr>
				</tfoot>
			</table>
			<br>
			<hr>
		</div>
	</div>