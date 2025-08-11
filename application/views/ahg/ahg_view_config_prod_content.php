			<div class="col-lg-12" id="content">
				<div id="breadcrumb-div">
					<ul class="breadcrumb">
					    <li><a href="<?=base_url('ahg')?>"><span class="glyphicon glyphicon-home"></span>&nbsp;Dashboard</a></li>
					    <li><a href="<?=base_url('ahg/production-cost')?>">Production Cost</a></li>
					    <li class="active">Production Group Details</li>
					</ul>
				</div>
			
				<?php
					if($this->session->flashdata('message') != "" ){
						echo $this->session->flashdata('message');
					}
				?>

				<div id="add-btn">
					<a href="#" data-toggle="modal" data-target="#modal-production-subgroup" class="btn btn-success btn-xs">+ Add Config Item (Material)</a>&nbsp;&nbsp;<a href="#" data-toggle="modal" data-target="#modal-production-subgroup-services" class="btn btn-success btn-xs">+ Add Config Item (Services)</a>

					<div id="modal-production-subgroup" class="modal fade modal-confirm" role="dialog">
						<div class="modal-dialog modal-sm">
						    <div class="modal-content">
						    	<div class="modal-header">
						        	<button type="button" class="close" data-dismiss="modal">&times;</button>
						       		<strong>Add Config Item (Material)</strong>
						      	</div>
						      	<div class="modal-body">
						        	<form method="POST" action="<?=base_url('ahg/add-config-prod-dtl')?>" enctype="multipart/form-data" id="add-material">
						        		<input type="hidden" name="config_prod_id" value="<?=$config_prod_id?>">
						        		<input type="hidden" name="article_type_id" value="<?=encode(1)?>">
						        		
						        		<div class="form-group">
						        			<label>Component Type:</label>
						        			<select class="form-control" name="component_type_id" id="component_type_id">
						        				<option value="">Select...</option>

						        				<?php foreach($component_type as $row):
						        					if($row->component_classification == 1):
						        				?>
						        				<option value="<?=encode($row->component_type_id)?>"><?=$row->component_type?></option>
						        				<?php endif; ?>
						        				<?php endforeach;?>
						        			</select>
						        		</div>

						        		<div class="form-group">
						        			<label>Item:</label>
						        			<select class="form-control" name="article_id[]" id="article_id">
						        				<option value="">Select...</option>

						        			</select>
						        		</div>

						        		<div class="form-group">
						        			<label>Amount Type:</label>
						        			<select class="form-control" name="amount_type_id" id="amount_type_id">
						        				<option value="">Select...</option>

						        				<?php foreach($amount_type as $row):?>

						        				<option value="<?=encode($row->amount_type_id)?>"><?=$row->amount_type_name?></option>

						        				<?php endforeach;?>

						        			</select>
						        		</div>

						        		<div class="form-group">
						        			<label>Show on Trans:</label>
						        			<select class="form-control" name="show_on_trans" id="show_on_trans">
						        				<option value="">Select...</option>
						        				<option value="<?=encode(1)?>">YES</option>
						        				<option value="<?=encode(2)?>">NO</option>

						        			</select>
						        		</div>
						        		
						        		<div class="btn-update">
						        			<button type="submit" class="btn btn-info btn-sm pull-right">Add</button><br>
						        		</div>
						        	</form>
						      	</div>
						    </div>
						</div>
					</div>

					<div id="modal-production-subgroup-services" class="modal fade modal-confirm" role="dialog">
						<div class="modal-dialog modal-sm">
						    <div class="modal-content">
						    	<div class="modal-header">
						        	<button type="button" class="close" data-dismiss="modal">&times;</button>
						       		<strong>Add Config Item (Services)</strong>
						      	</div>
						      	<div class="modal-body">
						        	<form method="POST" action="<?=base_url('ahg/add-config-prod-dtl')?>" enctype="multipart/form-data" id="add-material">
						        		<input type="hidden" name="config_prod_id" value="<?=$config_prod_id?>">
						        		<input type="hidden" name="article_type_id" value="<?=encode(2)?>">

						        		<div class="form-group">
						        			<label>Component Type:</label>
						        			<select class="form-control" name="component_type_id" id="component_type_id_svc">
						        				<option value="">Select...</option>
						        				<?php foreach($component_type as $row):
						        					if($row->component_classification == 2):
						        				?>
						        				<option value="<?=encode($row->component_type_id)?>"><?=$row->component_type?></option>
						        				<?php endif; ?>
						        				<?php endforeach;?>
						        			</select>
						        		</div>

						        		<div class="form-group">
						        			<label>Item:</label>
						        			<select class="form-control" name="article_id[]" id="article_id_svc">
						        				<option value="">Select...</option>

						        			</select>
						        		</div>

						        		<div class="form-group">
						        			<label>Amount Type:</label>
						        			<select class="form-control" name="amount_type_id" id="amount_type_id">
						        				<option value="">Select...</option>

						        				<?php foreach($amount_type as $row):?>

						        				<option value="<?=encode($row->amount_type_id)?>"><?=$row->amount_type_name?></option>

						        				<?php endforeach;?>

						        			</select>
						        		</div>

						        		<div class="form-group">
						        			<label>Show on Trans:</label>
						        			<select class="form-control" name="show_on_trans" id="show_on_trans">
						        				<option value="">Select...</option>
						        				<option value="<?=encode(1)?>">YES</option>
						        				<option value="<?=encode(2)?>">NO</option>

						        			</select>
						        		</div>
						        		
						        		<div class="btn-update">
						        			<button type="submit" class="btn btn-info btn-sm pull-right">Add</button><br>
						        		</div>
						        	</form>
						      	</div>
						    </div>
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
						      		<form method="POST" action="<?=base_url('ahg/remove-config-prod-dtl')?>" enctype="multipart/form-data" id="remove-config-prod-dtl">
						      			<input type="hidden" name="config_prod_dtl_id" id="config_prod_dtl_id">
						      			<input type="hidden" name="config_prod_id" id="config_prod_id">
						      			<input type="hidden" name="material_desc" id="material_desc">
						      			<input type="hidden" name="trans_status" id="trans_status">

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

				<div class="table-responsive">
					<table class="table table-hover table-bordered table-stripe nowrap" id="tbl-broiler-group">
						<thead>
							<tr>
								<th width="1%"></th>
								<th>Item Name</th>
								<th>Component Type</th>
								<th>Valuation Unit</th>
								<th>Amount Type</th>
								<th>Show on Transaction</th>
							</tr>
						</thead>
						<tbody>
						<?php

						foreach($config_prod as $row):
						$show_on_trans = $row->show_on_trans == 1 ? 'YES' : 'NO';
						?>
							
							<tr>
								<?php if($row->config_prod_dtl_status == 1){ ?>
								<td class="text-center"><a href="" class="remove-config-prod-dtl" data-id="<?=encode($row->config_prod_dtl_id)?>" data-config_prod_id="<?=encode($row->config_prod_id)?>" data-mat_desc="<?=encode($row->material_desc)?>"><i class="fa fa-remove"></i></td>
								<?php } ?>
								<th><?=$row->material_desc?></th>
								<td><?=$row->component_type?></td>
								<td><?=$row->unit_name?></td>
								<td><?=$row->amount_type_name?></td>
								<td><?=$show_on_trans?></td>
							</tr>
						<?php endforeach; ?>
						</tbody>
					</table>
				</div>
				<br><br>
			</div>