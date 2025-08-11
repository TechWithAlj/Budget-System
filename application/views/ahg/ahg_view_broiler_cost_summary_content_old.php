	<?php $year = decode($trans_year); $doctype = encode('report');?>
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
						<th rowspan="2" width="30%">Computation Name</th>
					<?php for ($i=1; $i <= 12 ; $i++){ ?>
						<th width="auto" class="text-center"><?=date('M', strtotime($year.'-'.$i.'-01'))?></th>
					<?php } ?>
						<th rowspan="2" class="text-center">Total</th>
					</tr>
					<tr>
					<?php for ($i=1; $i <= 12 ; $i++){ ?>
						<th class="text-center">Amount</th>
					<?php } ?>
					</tr>
				</thead>
				<tbody>
					<tr>
						<th>BROILER COST/KILO</th>
					<?php
						$broiler_cost = 0;
						for ($i=1; $i <= 12 ; $i++){
							$broiler_cost = $broiler_cost + broiler_cost($bc_id, $trans_year, $i, $doctype);
					?>
						<th class="text-right"><?=number_format(broiler_cost($bc_id, $trans_year, $i, $doctype),dec_places_dis(),'.',',')?></th>
					<?php } ?>
						<th class="text-right"><?=number_format($broiler_cost,dec_places_dis(),'.',',')?></th>
					</tr>
					<?php foreach($broiler_group as $row): ?>
						<?php
						if($row->broiler_group_id == 1):
							$broiler_group_name = $row->broiler_group_name;
						?>
						<tr>

							<th style="text-indent: 10%;"><a href="<?=base_url('ahg/view-broiler-trans/' . $bc_id .'/'. encode($row->broiler_group_id).'/'.encode($row->broiler_group_name) . '/' . $trans_year)?>" class="brn btn-xs"><?=$broiler_group_name?></a></th>
							<?php
							$broiler_group_amunt = 0;
							for ($i=1; $i <= 12 ; $i++){
								$broiler_group_amunt = $broiler_group_amunt + doc($bc_id, $trans_year, $i, $doctype);
							?>
								<th class="text-right"><?=number_format(doc($bc_id, $trans_year, $i, $doctype),dec_places_dis(),'.',',')?></th>
							<?php } ?>
							<th class="text-right"><?=number_format($broiler_group_amunt,dec_places_dis(),'.',',')?></th>
						</tr>
						<?php endif; ?>
						<?php
						if($row->broiler_group_id == 2):
							$broiler_group_name = $row->broiler_group_name;
						?>
						<tr>
							<th style="text-indent: 10%;"><a href="<?=base_url('ahg/view-broiler-trans/' . $bc_id .'/'. encode($row->broiler_group_id).'/'.encode($row->broiler_group_name) . '/' . $trans_year)?>" class="brn btn-xs"><?=$broiler_group_name?></a></th>
							<?php
							$broiler_group_amunt = 0;
							for ($i=1; $i <= 12 ; $i++){
								$broiler_group_amunt = $broiler_group_amunt + growers_fee($bc_id, $trans_year, $i, $doctype);
							?>
								<th class="text-right"><?=number_format(growers_fee($bc_id, $trans_year, $i, $doctype),dec_places_dis(),'.',',')?></th>
							<?php } ?>
							<th class="text-right"><?=number_format($broiler_group_amunt,dec_places_dis(),'.',',')?></th>
						</tr>
						<?php endif; ?>
						<?php
						if($row->broiler_group_id == 3):
							$broiler_group_name = $row->broiler_group_name;
						?>
						<tr>
							<th style="text-indent: 10%;"><a href="<?=base_url('ahg/view-broiler-trans/' . $bc_id .'/'. encode($row->broiler_group_id).'/'.encode($row->broiler_group_name) . '/' . $trans_year)?>" class="brn btn-xs"><?=$broiler_group_name?></a></th>
							<?php
							$broiler_group_amunt = 0;
							for ($i=1; $i <= 12 ; $i++){
								$broiler_group_amunt = $broiler_group_amunt + feed_cost($bc_id, $trans_year, $i, $doctype);
							?>
								<th class="text-right"><?=number_format(feed_cost($bc_id, $trans_year, $i, $doctype),dec_places_dis(),'.',',')?></th>
							<?php } ?>
							<th class="text-right"><?=number_format($broiler_group_amunt,dec_places_dis(),'.',',')?></th>
						</tr>
						<?php endif; ?>
						<?php
						if($row->broiler_group_id == 4):
							$broiler_group_name = $row->broiler_group_name;
						?>
						<tr>
							<th style="text-indent: 10%;"><a href="<?=base_url('ahg/view-broiler-trans/' . $bc_id .'/'. encode($row->broiler_group_id).'/'.encode($row->broiler_group_name) . '/' . $trans_year)?>" class="brn btn-xs"><?=$broiler_group_name?></a></th>
							<?php
							$broiler_group_amunt = 0;
							for ($i=1; $i <= 12 ; $i++){
								$broiler_group_amunt = $broiler_group_amunt + vaccines($bc_id, $trans_year, $i, $doctype);
							?>
								<th class="text-right"><?=number_format(vaccines($bc_id, $trans_year, $i, $doctype),dec_places_dis(),'.',',')?></th>
							<?php } ?>
							<th class="text-right"><?=number_format($broiler_group_amunt,dec_places_dis(),'.',',')?></th>
						</tr>
						<?php endif; ?>
						<?php
						if($row->broiler_group_id == 5):
							$broiler_group_name = $row->broiler_group_name;
						?>
						<tr>
							<th style="text-indent: 10%;"><a href="<?=base_url('ahg/view-broiler-trans/' . $bc_id .'/'. encode($row->broiler_group_id).'/'.encode($row->broiler_group_name) . '/' . $trans_year)?>" class="brn btn-xs"><?=$broiler_group_name?></a></th>
							<?php
							$broiler_group_amunt = 0;
							for ($i=1; $i <= 12 ; $i++){
								$broiler_group_amunt = $broiler_group_amunt + medicines($bc_id, $trans_year, $i, $doctype);
							?>
								<th class="text-right"><?=number_format(medicines($bc_id, $trans_year, $i, $doctype),dec_places_dis(),'.',',')?></th>
							<?php } ?>
							<th class="text-right"><?=number_format($broiler_group_amunt,dec_places_dis(),'.',',')?></th>
						</tr>
						<?php endif; ?>

					<?php endforeach; ?>
				</tbody>
			</table>
			<br>
			<hr>
		</div>
	</div>