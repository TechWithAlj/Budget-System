
			<div class="col-lg-12" id="content">
				<div id="breadcrumb-div">
					<ul class="breadcrumb">
					    <li><a href="<?=base_url('admin')?>"><span class="glyphicon glyphicon-home"></span>&nbsp;Dashboard</a></li>
					    <li class="active">Comparative Data Upload</li>
					</ul>
				</div>

				<?php
					if($this->session->flashdata('message') != "" ){
						echo $this->session->flashdata('message');
					}
				?>

				<div id="modal-confirm" class="modal fade" role="dialog">
					<div class="modal-dialog modal-sm">
						<div class="modal-content">
							<div class="modal-header">
								<button type="button" class="close" data-dismiss="modal">&times;</button>
								<strong>Confirmation message</strong>
							</div>
							<div class="modal-body">
								<form method="POST" action="<?=base_url('admin/cancel-uploaded-data')?>" id="cancel-comparative-data">
									<input type="hidden" name="trans_year" id="trans_year">
									<input type="hidden" name="bc_id" id="bc_id">
									<input type="hidden" name="table" id="table">

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

				<ul class="nav nav-tabs">
				    <li class="active"><a data-toggle="tab" href="#comp-capex-tab" class="tab-letter">CAPEX (BC)</a></li>
   					<li><a data-toggle="tab" class="tab-letter" href="#comp-capex-unit-tab">CAPEX (Unit)</a></li>
   					<li><a data-toggle="tab" class="tab-letter" href="#comp-opex-gl-tab">OPEX (BC)</a></li>
   					<li><a data-toggle="tab" class="tab-letter" href="#comp-opex-dept-tab">OPEX (Unit)</a></li>
   					<li><a data-toggle="tab" class="tab-letter" href="#comp-net-sales-tab">Net Sales</a></li>
   					<li><a data-toggle="tab" class="tab-letter" href="#comp-pnl-tab">PNL</a></li>
   					<li><a data-toggle="tab" class="tab-letter" href="#comp-price-tab">Price Assumption</a></li>
   					<li><a data-toggle="tab" class="tab-letter" href="#comp-variable-tab">Variable Cost</a></li>
   					<li><a data-toggle="tab" class="tab-letter" href="#comp-volume-tab">Sales Volume</a></li>
					<li><a data-toggle="tab" class="tab-letter" href="#comp-depreciation-bc-tab">Depreciation (BC)</a></li>
					<li><a data-toggle="tab" class="tab-letter" href="#comp-depreciation-unit-tab">Depreciation (Unit)</a></li>
					<li><a data-toggle="tab" class="tab-letter" href="#comp-depreciation-prod-unit-tab">Depreciation (Commi & Liempo Unit)</a></li>
  				</ul>

  				<div class="tab-content">

					<div id="comp-capex-tab" class="tab-pane fade in active">
    					<br>
						<div id="add-btn">
							<a href="#" data-toggle="modal" data-target="#modal-comp-capex" class="btn btn-success btn-xs" data-toggle="modal" data-target="#modal-vmaterial"><span class="fa fa-upload"></span> Upload CAPEX Comparative</a>
						</div>

						<div id="modal-comp-capex" class="modal fade modal-confirm" role="dialog">
							<div class="modal-dialog modal-md">
							    <div class="modal-content">
							        <form method="POST" class="comparative-form" class="comparative-form" action="<?=base_url('admin/upload-comp-capex')?>" enctype="multipart/form-data">
								    	<div class="modal-header">
								        	<button type="button" class="close" data-dismiss="modal">&times;</button>
								       		<strong>Upload CAPEX (BC) Comparative</strong>
								      	</div>
								      	<div class="modal-body">

			                                <div class="form-group">
			                                	No template? 
			                                	<a class="card-link" href="<?=base_url('admin/download-comparative-capex-temp')?>"><span class="fa fa-download"></span> Download here</a>
			                                	<br>
			                                	<hr>
			                                	<label>Trans Year:</label>
												<div class="form-group">
													<div class="date">
								                        <div class="input-group input-append date comp-trans-year col-md-4">
								                            <input type="text" name="comp-capex-year" class="form-control input-sm" placeholder="Pick year" value="">
								                            <span class="input-group-addon add-on"><span class="glyphicon glyphicon-calendar"></span></span>
								                        </div>
								                    </div>
							                	</div>
												
			                                    <label>Select Excel File</label><br>
			                                    <input type="file" name="comp-capex-file" class="form-contol-md" required accept=".xlsx" />
			                                </div>
								      	</div>

								      	<div class="modal-footer">
								      		<div class="btn-update">
							        			<button type="submit" class="btn btn-info btn-sm pull-right">Upload</button><br>
							        		</div>
								      	</div>
							        </form>
							    </div>
							</div>
						</div>

						

						<table class="table table-hover tbl-comparative">
							<thead>
								<tr>
									
									<th>BC</th>
									<th>Year</th>
									
									<th>Created By</th>
									<th>Date Created</th>
									<th>Filename</th>
									<th>Entries</th>
									<th class="text-center">Action</th>
									
								</tr>
							</thead>
							<tbody>
								<?php foreach ($comparative_capex as $r): ?>
								<tr>
									<td><?=$r->bc_name?></td>
									<td><?=$r->trans_year?></td>
									<td><?=$r->creator?></td>
									<td><?=$r->created_ts?></td>
									
									<th><a href="<?=base_url('admin/view-uploaded-file/'.encode($r->filename))?>"><?=$r->filename==''?'':explode('/', $r->filename)[2];?></a></th>
									<th><a href="<?=base_url('admin/view-uploaded-data/'.encode('comparative_capex_tbl').'/'.encode($r->bc_id).'/'.encode($r->trans_year))?>" ><?=$r->trans_count?></a>
									</th>
									<th class="text-center">
										<a href="<?=base_url('admin/view-uploaded-data/'.encode('comparative_capex_tbl').'/'.encode($r->bc_id).'/'.encode($r->trans_year))?>" ><i class="fa fa-file text-success"></i></a>&nbsp;
										<a class="cancel-commparative-data" href="" data-bc-id="<?=encode($r->bc_id)?>" data-trans-year="<?=encode($r->trans_year)?>" data-table="<?=encode('comparative_capex_tbl')?>" ><i class="fa fa-remove text-danger"></i></a>
									</th>
								
								</tr>
									
								<?php endforeach; ?>

							</tbody>
						</table>

					</div>

					<div id="comp-capex-unit-tab" class="tab-pane fade in">
    					<br>
						<div id="add-btn">
							<a href="#" data-toggle="modal" data-target="#modal-comp-capex-unit" class="btn btn-success btn-xs" data-toggle="modal" data-target="#modal-vmaterial"><span class="fa fa-upload"></span> Upload CAPEX Comparative</a>
						</div>

						<div id="modal-comp-capex-unit" class="modal fade modal-confirm" role="dialog">
							<div class="modal-dialog modal-md">
							    <div class="modal-content">
							        <form method="POST" class="comparative-form" class="comparative-form" action="<?=base_url('admin/upload-comp-capex-unit')?>" enctype="multipart/form-data">
								    	<div class="modal-header">
								        	<button type="button" class="close" data-dismiss="modal">&times;</button>
								       		<strong>Upload CAPEX (Unit) Comparative</strong>
								      	</div>
								      	<div class="modal-body">

			                                <div class="form-group">
			                                	No template? 
			                                	<a class="card-link" href="<?=base_url('admin/download-comparative-capex-unit-temp')?>"><span class="fa fa-download"></span> Download here</a>
			                                	<br>			                                	
			                                	<hr>
			                                	<label>Trans Year:</label>
												<div class="form-group">
													<div class="date">
								                        <div class="input-group input-append date comp-trans-year col-md-4">
								                            <input type="text" name="comp-capex-unit-year" class="form-control input-sm" placeholder="Pick year" value="">
								                            <span class="input-group-addon add-on"><span class="glyphicon glyphicon-calendar"></span></span>
								                        </div>
								                    </div>
							                	</div>
												
			                                    <label>Select Excel File</label><br>
			                                    <input type="file" name="comp-capex-unit-file" class="form-contol-md" required accept=".xlsx" />
			                                </div>
								      	</div>

								      	<div class="modal-footer">
								      		<div class="btn-update">
							        			<button type="submit" class="btn btn-info btn-sm pull-right">Upload</button><br>
							        		</div>
								      	</div>
							        </form>
							    </div>
							</div>
						</div>

						<table class="table table-hover tbl-comparative">
							<thead>
								<tr>
									
									<th>Company Unit Name</th>
									<th>Year</th>
									
									<th>Created By</th>
									<th>Date Created</th>
									<th>Filename</th>
									<th>Entries</th>
									<th class="text-center">Action</th>
									
								</tr>
							</thead>
							<tbody>
								<?php foreach ($comparative_capex_unit as $r): ?>
								<tr>
									<td><?=$r->company_unit_name?></td>
									<td><?=$r->trans_year?></td>
									<td><?=$r->creator?></td>
									<td><?=$r->created_ts?></td>
									
									<th><a href="<?=base_url('admin/view-uploaded-file/'.encode($r->filename))?>" target="new"><?=$r->filename==''?'':explode('/', $r->filename)[2];?></a></th>
									<th><a href="<?=base_url('admin/view-uploaded-data/'.encode('comparative_capex_unit_tbl').'/'.encode($r->company_unit_id).'/'.encode($r->trans_year))?>" ><?=$r->trans_count?></a>
									</th>
									<th class="text-center">
										<a href="<?=base_url('admin/view-uploaded-data/'.encode('comparative_capex_unit_tbl').'/'.encode($r->company_unit_id).'/'.encode($r->trans_year))?>" ><i class="fa fa-file text-success"></i></a>&nbsp;
										<a class="cancel-commparative-data" href="" data-bc-id="<?=encode($r->company_unit_id)?>" data-trans-year="<?=encode($r->trans_year)?>" data-table="<?=encode('comparative_capex_unit_tbl')?>" ><i class="fa fa-remove text-danger"></i></a>
									</th>
								</tr>
									
								<?php endforeach; ?>

							</tbody>
						</table>

					</div>

					<div id="comp-opex-dept-tab" class="tab-pane fade in">
    					
    					<br>
						<div id="add-btn">
							<a href="#" data-toggle="modal" data-target="#modal-comp-opex-dept" class="btn btn-success btn-xs" data-toggle="modal" data-target="#modal-vmaterial"><span class="fa fa-upload"></span> Upload OPEX Comparative</a>
						</div>

						<div id="modal-comp-opex-dept" class="modal fade modal-confirm" role="dialog">
							<div class="modal-dialog modal-md">
							    <div class="modal-content">
							        <form method="POST" class="comparative-form" class="comparative-form" action="<?=base_url('admin/upload-comp-opex-dept')?>" enctype="multipart/form-data" id="add-material">
								    	<div class="modal-header">
								        	<button type="button" class="close" data-dismiss="modal">&times;</button>
								       		<strong>Upload OPEX per unit Comparative</strong>
								      	</div>
								      	<div class="modal-body">

			                                <div class="form-group">
			                                	No template? 
			                                	<a class="card-link" href="<?=base_url('admin/download-comparative-opex-dept-temp')?>"><span class="fa fa-download"></span> Download here</a>
			                                	<br>			                                	
			                                	<hr>
			                                	<label>Trans Year:</label>
												<div class="form-group">
													<div class="date">
								                        <div class="input-group input-append date comp-trans-year col-md-4">
								                            <input type="text" name="comp-opex-dept-year" class="form-control input-sm" placeholder="Pick year" value="">
								                            <span class="input-group-addon add-on"><span class="glyphicon glyphicon-calendar"></span></span>
								                        </div>
								                    </div>
							                	</div>
												
			                                    <label>Select Excel File</label><br>
			                                    <input type="file" name="opex-dept-file" class="form-contol-md" required accept=".xlsx" />
			                                </div>
								      	</div>

								      	<div class="modal-footer">
								      		<div class="btn-update">
							        			<button type="submit" class="btn btn-info btn-sm pull-right">Upload</button><br>
							        		</div>
								      	</div>
							        </form>
							    </div>
							</div>
						</div>

						<table class="table table-hover tbl-comparative" >
							<thead>
								<tr>
									<th>Unit/Dept</th>
									<th>Trans Year</th>
									<th>Created By</th>
									<th>Date Created</th>
									<th>Filename</th>
									<th>Entries</th>
									<th class="text-center">Action</th>
									
								</tr>
							</thead>
							<tbody>

								<?php foreach ($comparative_opex_dept as $r): ?>
								<tr>
									<td><?=$r->company_unit_name?></td>
									<td><?=$r->trans_year?></td>
									<td><?=$r->creator?></td>
									<td><?=$r->created_ts?></td>
									<th><a href="<?=base_url('admin/view-uploaded-file/'.encode($r->filename))?>" target="new"><?=$r->filename==''?'':explode('/', $r->filename)[2];?></a></th>
									<th><a href="<?=base_url('admin/view-uploaded-data/'.encode('comparative_opex_dept_tbl').'/'.encode($r->company_unit_id).'/'.encode($r->trans_year))?>"><?=$r->trans_count?></a>
									</th>
									<th class="text-center">
										<a href="<?=base_url('admin/view-uploaded-data/'.encode('comparative_opex_dept_tbl').'/'.encode($r->company_unit_id).'/'.encode($r->trans_year))?>" ><i class="fa fa-file text-success"></i></a>&nbsp;
										<a class="cancel-commparative-data" href="" data-bc-id="<?=encode($r->company_unit_id)?>" data-trans-year="<?=encode($r->trans_year)?>" data-table="<?=encode('comparative_opex_dept_tbl')?>" ><i class="fa fa-remove text-danger"></i></a>
									</th>
									
								</tr>
									
								<?php endforeach; ?>

							</tbody>
						</table>

					</div>

					<div id="comp-opex-gl-tab" class="tab-pane fade in">
    					<br>
						<div id="add-btn">
							<a href="#" data-toggle="modal" data-target="#modal-comp-opex-gl" class="btn btn-success btn-xs" data-toggle="modal""><span class="fa fa-upload"></span> Upload OPEX Comparative</a>
						</div>

						<div id="modal-comp-opex-gl" class="modal fade modal-confirm" role="dialog">
							<div class="modal-dialog modal-md">
							    <div class="modal-content">
							        <form method="POST" class="comparative-form" action="<?=base_url('admin/upload-comp-opex-gl')?>" enctype="multipart/form-data">
								    	<div class="modal-header">
								        	<button type="button" class="close" data-dismiss="modal">&times;</button>
								       		<strong>Upload OPEX per GL Comparative</strong>
								      	</div>
								      	<div class="modal-body">

			                                <div class="form-group">
			                                	No template? 
			                                	<a class="card-link" href="<?=base_url('admin/download-comparative-opex-gl-temp')?>"><span class="fa fa-download"></span> Download here</a>
			                                	<br>			                                	
			                                	<hr>
												<label>Trans Year:</label>
												<div class="form-group">
													<div class="date">
								                        <div class="input-group input-append date comp-trans-year col-md-4">
								                            <input type="text" name="comp-opex-gl-year" class="form-control input-sm" placeholder="Pick year" value="">
								                            <span class="input-group-addon add-on"><span class="glyphicon glyphicon-calendar"></span></span>
								                        </div>
								                    </div>
							                	</div>

			                                    <label>Select Excel File</label><br>
			                                    <input type="file" name="opex-gl-file" class="form-contol-md" required accept=".xlsx" />
			                                </div>
								      	</div>

								      	<div class="modal-footer">
								      		<div class="btn-update">
							        			<button type="submit" class="btn btn-info btn-sm pull-right">Upload</button><br>
							        		</div>
								      	</div>
							        </form>
							    </div>
							</div>
						</div>

						<table class="table table-hover tbl-comparative">
							<thead>
								<tr>
									<th>Business Center</th>
									<th>Trans Year</th>
									<th>Created By</th>
									<th>Date Created</th>
									<th>Filename</th>
									<th>Entries</th>
									<th class="text-center">Action</th>
									
								</tr>
							</thead>
							<tbody>
								<?php foreach ($comparative_opex_gl as $r): ?>
								<tr>
									<td><?=$r->bc_name?></td>
									<td><?=$r->trans_year?></td>
									<td><?=$r->creator?></td>
									<td><?=$r->created_ts?></td>
									<th><a href="<?=base_url('admin/view-uploaded-file/'.encode($r->filename))?>" target="new"><?=$r->filename==''?'':explode('/', $r->filename)[2];?></a></th>
									<th><a href="<?=base_url('admin/view-uploaded-data/'.encode('comparative_opex_gl_tbl').'/'.encode($r->bc_id).'/'.encode($r->trans_year))?>"><?=$r->trans_count?></a>
									</th>
									<th class="text-center">
										<a href="<?=base_url('admin/view-uploaded-data/'.encode('comparative_opex_gl_tbl').'/'.encode($r->bc_id).'/'.encode($r->trans_year))?>" ><i class="fa fa-file text-success"></i></a>&nbsp;
										<a class="cancel-commparative-data" href="" data-bc-id="<?=encode($r->bc_id)?>" data-trans-year="<?=encode($r->trans_year)?>" data-table="<?=encode('comparative_opex_gl_tbl')?>" ><i class="fa fa-remove text-danger"></i></a>
									</th>
								</tr>
									
								<?php endforeach; ?>

							</tbody>
						</table>

					</div>

					<div id="comp-net-sales-tab" class="tab-pane fade in">
    					<br>
						<div id="add-btn">
							<a href="#" data-toggle="modal" data-target="#modal-comp-net-sales" class="btn btn-success btn-xs" data-toggle="modal""><span class="fa fa-upload"></span> Upload Net Sales Comparative</a>
						</div>

						<div id="modal-comp-net-sales" class="modal fade modal-confirm" role="dialog">
							<div class="modal-dialog modal-md">
							    <div class="modal-content">
							        <form method="POST" class="comparative-form" action="<?=base_url('admin/upload-comp-net-sales')?>" enctype="multipart/form-data" id="add-material">
								    	<div class="modal-header">
								        	<button type="button" class="close" data-dismiss="modal">&times;</button>
								       		<strong>Upload Net Sales Comparative</strong>
								      	</div>
								      	<div class="modal-body">

			                                <div class="form-group">
			                                	No template? 
			                                	<a class="card-link" href="<?=base_url('admin/download-comparative-net-sales-temp')?>"><span class="fa fa-download"></span> Download here</a>
			                                	<br>			                                	
			                                	<hr>
												<label>Trans Year:</label>
												<div class="form-group">
													<div class="date">
								                        <div class="input-group input-append date comp-trans-year col-md-4">
								                            <input type="text" name="comp-net-sales-trans-year" class="form-control input-sm" placeholder="Pick year" value="">
								                            <span class="input-group-addon add-on"><span class="glyphicon glyphicon-calendar"></span></span>
								                        </div>
								                    </div>
							                	</div>
			                                    <label>Select Excel File</label><br>
			                                    <input type="file" name="net-sales-file" class="form-contol-md" required accept=".xlsx" />
			                                </div>
								      	</div>

								      	<div class="modal-footer">
								      		<div class="btn-update">
							        			<button type="submit" class="btn btn-info btn-sm pull-right">Upload</button><br>
							        		</div>
								      	</div>
							        </form>
							    </div>
							</div>
						</div>

						<table class="table table-hover tbl-comparative">
							<thead>
								<tr>
									<th>Business Center</th>
									<th>Year</th>
									<th>Created By</th>
									<th>Date Created</th>
									<th>Filename</th>
									<th>Entries</th>
									<th class="text-center">Action</th>
									
								</tr>
							</thead>
							<tbody>

								<?php foreach ($comparative_net_sales as $r): ?>
								<tr>
									<td><?=$r->bc_name?></td>
									<td><?=$r->trans_year?></td>
									<td><?=$r->creator?></td>
									<td><?=$r->created_ts?></td>
									<th><a href="<?=base_url('admin/view-uploaded-file/'.encode($r->filename))?>" target="new"><?=$r->filename==''?'':explode('/', $r->filename)[2];?></a></th>
									<th><a href="<?=base_url('admin/view-uploaded-data/'.encode('comparative_net_sales_tbl').'/'.encode($r->bc_id).'/'.encode($r->trans_year))?>" ><?=$r->trans_count?></a>
									</th>
									<th class="text-center">
										<a href="<?=base_url('admin/view-uploaded-data/'.encode('comparative_net_sales_tbl').'/'.encode($r->bc_id).'/'.encode($r->trans_year))?>" ><i class="fa fa-file text-success"></i></a>&nbsp;
										<a class="cancel-commparative-data" href="" data-bc-id="<?=encode($r->bc_id)?>" data-trans-year="<?=encode($r->trans_year)?>" data-table="<?=encode('comparative_net_sales_tbl')?>" ><i class="fa fa-remove text-danger"></i></a>
									</th>
								</tr>
									
								<?php endforeach; ?>
							</tbody>
						</table>

					</div>

					<div id="comp-pnl-tab" class="tab-pane fade in">
    					<br>
						<div id="add-btn">
							<a href="#" data-toggle="modal" data-target="#modal-comp-pnl" class="btn btn-success btn-xs" data-toggle="modal""><span class="fa fa-upload"></span> Upload PNL Comparative</a>
						</div>

						<div id="modal-comp-pnl" class="modal fade modal-confirm" role="dialog">
							<div class="modal-dialog modal-md">
							    <div class="modal-content">
							        <form method="POST" class="comparative-form" action="<?=base_url('admin/upload-comp-pnl')?>" enctype="multipart/form-data" id="add-material">
								    	<div class="modal-header">
								        	<button type="button" class="close" data-dismiss="modal">&times;</button>
								       		<strong>Upload PNL Comparative</strong>
								      	</div>
								      	<div class="modal-body">

			                                <div class="form-group">
			                                	No template? 
			                                	<a class="card-link" href="<?=base_url('admin/download-comparative-pnl-temp')?>"><span class="fa fa-download"></span> Download here</a>
			                                	<br>			                                	
			                                	<hr>
												<label>Trans Year:</label>
												<div class="form-group">
													<div class="date">
								                        <div class="input-group input-append date comp-trans-year col-md-4">
								                            <input type="text" name="comp-pnl-trans-year" class="form-control input-sm" placeholder="Pick year" value="">
								                            <span class="input-group-addon add-on"><span class="glyphicon glyphicon-calendar"></span></span>
								                        </div>
								                    </div>
							                	</div>
			                                    <label>Select Excel File</label><br>
			                                    <input type="file" name="pnl-file" class="form-contol-md" required accept=".xlsx" />
			                                </div>
								      	</div>

								      	<div class="modal-footer">
								      		<div class="btn-update">
							        			<button type="submit" class="btn btn-info btn-sm pull-right">Upload</button><br>
							        		</div>
								      	</div>
							        </form>
							    </div>
							</div>
						</div>

						<table class="table table-hover tbl-comparative">
							<thead>
								<tr>
									<th>Business Center</th>
									<th>Year</th>
									<th>Created By</th>
									<th>Date Created</th>
									<th>Filename</th>
									<th>Entries</th>
									<th class="text-center">Action</th>
								</tr>
							</thead>
							<tbody>
								<?php foreach ($comparative_pnl as $r): ?>
								<tr>
									<td><?=$r->bc_name?></td>
									<td><?=$r->trans_year?></td>
									<td><?=$r->creator?></td>
									<td><?=$r->created_ts?></td>
									<th><a href="<?=base_url('admin/view-uploaded-file/'.encode($r->filename))?>" target="new"><?=$r->filename==''?'':explode('/', $r->filename)[2];?></a></th>
									<th><a href="<?=base_url('admin/view-uploaded-data/'.encode('comparative_pnl_tbl').'/'.encode($r->bc_id).'/'.encode($r->trans_year))?>" ><?=$r->trans_count?></a>
									</th>
									<th class="text-center">
										<a href="<?=base_url('admin/view-uploaded-data/'.encode('comparative_pnl_tbl').'/'.encode($r->bc_id).'/'.encode($r->trans_year))?>" ><i class="fa fa-file text-success"></i></a>&nbsp;
										<a class="cancel-commparative-data" href="" data-bc-id="<?=encode($r->bc_id)?>" data-trans-year="<?=encode($r->trans_year)?>" data-table="<?=encode('comparative_pnl_tbl')?>" ><i class="fa fa-remove text-danger"></i></a>
									</th>
								</tr>
									
								<?php endforeach; ?>

							</tbody>
						</table>

					</div>

					<div id="comp-price-tab" class="tab-pane fade in">
    					<br>
						<div id="add-btn">
							<a href="#" data-toggle="modal" data-target="#modal-comp-price" class="btn btn-success btn-xs" data-toggle="modal""><span class="fa fa-upload"></span> Upload Price Assumption Comparative</a>
						</div>

						<div id="modal-comp-price" class="modal fade modal-confirm" role="dialog">
							<div class="modal-dialog modal-md">
							    <div class="modal-content">
							        <form method="POST" class="comparative-form" action="<?=base_url('admin/upload-comp-price')?>" enctype="multipart/form-data" id="add-material">
								    	<div class="modal-header">
								        	<button type="button" class="close" data-dismiss="modal">&times;</button>
								       		<strong>Upload Price Assumption Comparative</strong>
								      	</div>
								      	<div class="modal-body">

			                                <div class="form-group">
			                                	No template? 
			                                	<a class="card-link" href="<?=base_url('admin/download-comparative-price-temp')?>"><span class="fa fa-download"></span> Download here</a>
			                                	<br>			                                	
			                                	<hr>
												<label>Trans Year:</label>
												<div class="form-group">
													<div class="date">
								                        <div class="input-group input-append date comp-trans-year col-md-4">
								                            <input type="text" name="comp-price-trans-year" class="form-control input-sm" placeholder="Pick year" value="">
								                            <span class="input-group-addon add-on"><span class="glyphicon glyphicon-calendar"></span></span>
								                        </div>
								                    </div>
							                	</div>
			                                    <label>Select Excel File</label><br>
			                                    <input type="file" name="price-file" class="form-contol-md" required accept=".xlsx" />
			                                </div>
								      	</div>

								      	<div class="modal-footer">
								      		<div class="btn-update">
							        			<button type="submit" class="btn btn-info btn-sm pull-right">Upload</button><br>
							        		</div>
								      	</div>
							        </form>
							    </div>
							</div>
						</div>

						<table class="table table-hover tbl-comparative">
							<thead>
								<tr>
									<th>Business Center</th>
									<th>Year</th>
									<th>Created By</th>
									<th>Date Created</th>
									<th>Filename</th>
									<th>Entries</th>
									<th class="text-center">Action</th>
									
								</tr>
							</thead>
							<tbody>
								<?php foreach ($comparative_price as $r): ?>
								<tr>
									<td><?=$r->bc_name?></td>
									<td><?=$r->trans_year?></td>
									<td><?=$r->creator?></td>
									<td><?=$r->comp_price_added?></td>
									<th><a href="<?=base_url('admin/view-uploaded-file/'.encode($r->filename))?>" target="new"><?=$r->filename==''?'':explode('/', $r->filename)[2];?></a></th>
									<th><a href="<?=base_url('admin/view-uploaded-data/'.encode('comparative_price_tbl').'/'.encode($r->bc_id).'/'.encode($r->trans_year))?>" ><?=$r->trans_count?></a>
									</th>
									<th class="text-center">
										<a href="<?=base_url('admin/view-uploaded-data/'.encode('comparative_price_tbl').'/'.encode($r->bc_id).'/'.encode($r->trans_year))?>" ><i class="fa fa-file text-success"></i></a>&nbsp;
										<a class="cancel-commparative-data" href="" data-bc-id="<?=encode($r->bc_id)?>" data-trans-year="<?=encode($r->trans_year)?>" data-table="<?=encode('comparative_price_tbl')?>" ><i class="fa fa-remove text-danger"></i></a>
									</th>
								</tr>
								<?php endforeach; ?>
							</tbody>
						</table>

					</div>

					<div id="comp-variable-tab" class="tab-pane fade in">
    					<br>
						<div id="add-btn">
							<a href="#" data-toggle="modal" data-target="#modal-comp-variable" class="btn btn-success btn-xs" data-toggle="modal""><span class="fa fa-upload"></span> Upload Variable Cost Comparative</a>
						</div>

						<div id="modal-comp-variable" class="modal fade modal-confirm" role="dialog">
							<div class="modal-dialog modal-md">
							    <div class="modal-content">
							        <form method="POST" class="comparative-form" action="<?=base_url('admin/upload-comp-variable-cost')?>" enctype="multipart/form-data" id="add-material">
								    	<div class="modal-header">
								        	<button type="button" class="close" data-dismiss="modal">&times;</button>
								       		<strong>Upload Variable Cost Comparative</strong>
								      	</div>
								      	<div class="modal-body">

			                                <div class="form-group">
			                                	No template? 
			                                	<a class="card-link" href="<?=base_url('admin/download-comparative-variable-cost-temp')?>"><span class="fa fa-download"></span> Download here</a>
			                                	<br>			                                	
			                                	<hr>
												<label>Trans Year:</label>
												<div class="form-group">
													<div class="date">
								                        <div class="input-group input-append date comp-trans-year col-md-4">
								                            <input type="text" name="comp-variable-cost-trans-year" class="form-control input-sm" placeholder="Pick year" value="">
								                            <span class="input-group-addon add-on"><span class="glyphicon glyphicon-calendar"></span></span>
								                        </div>
								                    </div>
							                	</div>
			                                    <label>Select Excel File</label><br>
			                                    <input type="file" name="variable-cost-file" class="form-contol-md" required accept=".xlsx" />
			                                </div>
								      	</div>

								      	<div class="modal-footer">
								      		<div class="btn-update">
							        			<button type="submit" class="btn btn-info btn-sm pull-right">Upload</button><br>
							        		</div>
								      	</div>
							        </form>
							    </div>
							</div>
						</div>

						<table class="table table-hover tbl-comparative">
							<thead>
								<tr>
									<th>Business Center</th>
									<th>Year</th>
									<th>Created By</th>
									<th>Date Created</th>
									<th>Filename</th>
									<th>Entries</th>
									<th class="text-center">Action</th>
									
								</tr>
							</thead>
							<tbody>
								<?php foreach ($comparative_variable_cost as $r): ?>
								<tr>
									<td><?=$r->bc_name?></td>
									<td><?=$r->trans_year?></td>
									<td><?=$r->creator?></td>
									<td><?=$r->created_ts?></td>
									<th><a href="<?=base_url('admin/view-uploaded-file/'.encode($r->filename))?>" target="new"><?=$r->filename==''?'':explode('/', $r->filename)[2];?></a></th>
									<th><a href="<?=base_url('admin/view-uploaded-data/'.encode('comparative_variable_cost_tbl').'/'.encode($r->bc_id).'/'.encode($r->trans_year))?>" ><?=$r->trans_count?></a>
									</th>
									<th class="text-center">
										<a href="<?=base_url('admin/view-uploaded-data/'.encode('comparative_variable_cost_tbl').'/'.encode($r->bc_id).'/'.encode($r->trans_year))?>" ><i class="fa fa-file text-success"></i></a>&nbsp;
										<a class="cancel-commparative-data" href="" data-bc-id="<?=encode($r->bc_id)?>" data-trans-year="<?=encode($r->trans_year)?>" data-table="<?=encode('comparative_variable_cost_tbl')?>" ><i class="fa fa-remove text-danger"></i></a>
									</th>
								</tr>
									
								<?php endforeach; ?>
							</tbody>
						</table>

					</div>

					<div id="comp-volume-tab" class="tab-pane fade in">
    					<br>
						<div id="add-btn">
							<a href="#" data-toggle="modal" data-target="#modal-comp-volume" class="btn btn-success btn-xs" data-toggle="modal""><span class="fa fa-upload"></span> Upload Sales Volume Comparative</a>
						</div>

						<div id="modal-comp-volume" class="modal fade modal-confirm" role="dialog">
							<div class="modal-dialog modal-md">
							    <div class="modal-content">
							        <form method="POST" class="comparative-form" action="<?=base_url('admin/upload-comp-volume')?>" enctype="multipart/form-data" id="add-material">
								    	<div class="modal-header">
								        	<button type="button" class="close" data-dismiss="modal">&times;</button>
								       		<strong>Upload Sales Volume Comparative</strong>
								      	</div>
								      	<div class="modal-body">

			                                <div class="form-group">
			                                	No template? 
			                                	<a class="card-link" href="<?=base_url('admin/download-comparative-volume-temp')?>"><span class="fa fa-download"></span> Download here</a>
			                                	<br>			                                	
			                                	<hr>
												<label>Trans Year:</label>
												<div class="form-group">
													<div class="date">
								                        <div class="input-group input-append date comp-trans-year col-md-4">
								                            <input type="text" name="comp-sales-volume-year" class="form-control input-sm" placeholder="Pick year" value="">
								                            <span class="input-group-addon add-on"><span class="glyphicon glyphicon-calendar"></span></span>
								                        </div>
								                    </div>
							                	</div>
			                                    <label>Select Excel File</label><br>
			                                    <input type="file" name="sales-volume-file" class="form-contol-md" required accept=".xlsx" />
			                                </div>
								      	</div>

								      	<div class="modal-footer">
								      		<div class="btn-update">
							        			<button type="submit" class="btn btn-info btn-sm pull-right">Upload</button><br>
							        		</div>
								      	</div>
							        </form>
							    </div>
							</div>
						</div>

						<table class="table table-hover tbl-comparative">
							<thead>
								<tr>
									<th>Business Center</th>
									<th>Year</th>
									<th>Created By</th>
									<th>Date Created</th>
									<th>Filename</th>
									<th>Entries</th>
									<th class="text-center">Action</th>
									
								</tr>
							</thead>
							<tbody>
								<?php foreach ($comparative_volume as $r): ?>
								<tr>
									<td><?=$r->bc_name?></td>
									<td><?=$r->trans_year?></td>
									<td><?=$r->creator?></td>
									<td><?=$r->created_ts?></td>
									<th><a href="<?=base_url('admin/view-uploaded-file/'.encode($r->filename))?>" target="new"><?=$r->filename==''?'':explode('/', $r->filename)[2];?></a></th>
									<th><a href="<?=base_url('admin/view-uploaded-data/'.encode('comparative_volume_tbl').'/'.encode($r->bc_id).'/'.encode($r->trans_year))?>" ><?=$r->trans_count?></a>
									</th>
									<th class="text-center">
										<a href="<?=base_url('admin/view-uploaded-data/'.encode('comparative_volume_tbl').'/'.encode($r->bc_id).'/'.encode($r->trans_year))?>" ><i class="fa fa-file text-success"></i></a>&nbsp;
										<a class="cancel-commparative-data" href="" data-bc-id="<?=encode($r->bc_id)?>" data-trans-year="<?=encode($r->trans_year)?>" data-table="<?=encode('comparative_volume_tbl')?>" ><i class="fa fa-remove text-danger"></i></a>
									</th>
								</tr>
									
								<?php endforeach; ?>

							</tbody>
						</table>

					</div>

					<div id="comp-depreciation-bc-tab" class="tab-pane fade in">
    					<br>
						<div id="add-btn">
							<a href="#" data-toggle="modal" data-target="#modal-depreciation-bc" class="btn btn-success btn-xs" data-toggle="modal""><span class="fa fa-upload"></span> Upload Depreciation BC</a>
						</div>

						<div id="modal-depreciation-bc" class="modal fade modal-confirm" role="dialog">
							<div class="modal-dialog modal-md">
							    <div class="modal-content">
							        <form method="POST" class="comparative-form" action="<?=base_url('admin/upload-depreciation-bc')?>" enctype="multipart/form-data" id="add-material">
								    	<div class="modal-header">
								        	<button type="button" class="close" data-dismiss="modal">&times;</button>
								       		<strong>Upload Depreciation BC</strong>
								      	</div>
								      	<div class="modal-body">

			                                <div class="form-group">
			                                	No template? 
			                                	<a class="card-link" href="<?=base_url('admin/download-depreciation-bc-temp')?>"><span class="fa fa-download"></span> Download here</a>
			                                	<br>			                                	
			                                	<hr>
												<label>Trans Year:</label>
												<div class="form-group">
													<div class="date">
								                        <div class="input-group input-append date comp-trans-year col-md-4">
								                            <input type="text" name="comp-depreciation-bc-year" class="form-control input-sm" placeholder="Pick year" value="">
								                            <span class="input-group-addon add-on"><span class="glyphicon glyphicon-calendar"></span></span>
								                        </div>
								                    </div>
							                	</div>
			                                    <label>Select Excel File</label><br>
			                                    <input type="file" name="depreciation-bc-file" class="form-contol-md" required accept=".xlsx" />
			                                </div>
								      	</div>

								      	<div class="modal-footer">
								      		<div class="btn-update">
							        			<button type="submit" class="btn btn-info btn-sm pull-right">Upload</button><br>
							        		</div>
								      	</div>
							        </form>
							    </div>
							</div>
						</div>

						<table class="table table-hover tbl-comparative">
							<thead>
								<tr>
									<th>Business Center</th>
									<th>Year</th>
									<th>Created By</th>
									<th>Date Created</th>
									<th>Filename</th>
									<th>Entries</th>
									<th class="text-center">Action</th>
									
								</tr>
							</thead>
							<tbody>
								<?php foreach ($depreciation_bc as $r): ?>
								<tr>
									<td><?=$r->bc_name?></td>
									<td><?=$r->trans_year?></td>
									<td><?=$r->creator?></td>
									<td><?=$r->depreciation_bc_added?></td>
									<th><a href="<?=base_url('admin/view-uploaded-file/'.encode($r->filename))?>" target="new"><?=$r->filename==''?'':explode('/', $r->filename)[2];?></a></th>
									<th><a href="<?=base_url('admin/view-uploaded-data/'.encode('depreciation_bc_tbl').'/'.encode($r->bc_id).'/'.encode($r->trans_year))?>" ><?=$r->trans_count?></a>
									</th>
									<th class="text-center">
										<a href="<?=base_url('admin/view-uploaded-data/'.encode('depreciation_bc_tbl').'/'.encode($r->bc_id).'/'.encode($r->trans_year))?>" ><i class="fa fa-file text-success"></i></a>&nbsp;
										<a class="cancel-commparative-data" href="" data-bc-id="<?=encode($r->bc_id)?>" data-trans-year="<?=encode($r->trans_year)?>" data-table="<?=encode('depreciation_bc_tbl')?>" ><i class="fa fa-remove text-danger"></i></a>
									</th>
								</tr>
									
								<?php endforeach; ?>

							</tbody>
						</table>

					</div>

					<div id="comp-depreciation-unit-tab" class="tab-pane fade in">
    					<br>
						<div id="add-btn">
							<a href="#" data-toggle="modal" data-target="#modal-depreciation-unit" class="btn btn-success btn-xs" data-toggle="modal""><span class="fa fa-upload"></span> Upload Depreciation Unit</a>
						</div>

						<div id="modal-depreciation-unit" class="modal fade modal-confirm" role="dialog">
							<div class="modal-dialog modal-md">
							    <div class="modal-content">
							        <form method="POST" class="comparative-form" action="<?=base_url('admin/upload-depreciation-unit')?>" enctype="multipart/form-data">
								    	<div class="modal-header">
								        	<button type="button" class="close" data-dismiss="modal">&times;</button>
								       		<strong>Upload Depreciation Unit</strong>
								      	</div>
								      	<div class="modal-body">

			                                <div class="form-group">
			                                	No template? 
			                                	<a class="card-link" href="<?=base_url('admin/download-depreciation-unit-temp')?>"><span class="fa fa-download"></span> Download here</a>
			                                	<br>			                                	
			                                	<hr>
												<label>Trans Year:</label>
												<div class="form-group">
													<div class="date">
								                        <div class="input-group input-append date comp-trans-year col-md-4">
								                            <input type="text" name="comp-depreciation-unit-year" class="form-control input-sm" placeholder="Pick year" value="">
								                            <span class="input-group-addon add-on"><span class="glyphicon glyphicon-calendar"></span></span>
								                        </div>
								                    </div>
							                	</div>
			                                    <label>Select Excel File</label><br>
			                                    <input type="file" name="depreciation-unit-file" class="form-contol-md" required accept=".xlsx" />
			                                </div>
								      	</div>

								      	<div class="modal-footer">
								      		<div class="btn-update">
							        			<button type="submit" class="btn btn-info btn-sm pull-right">Upload</button><br>
							        		</div>
								      	</div>
							        </form>
							    </div>
							</div>
						</div>

						<table class="table table-hover tbl-comparative">
							<thead>
								<tr>
									<th>Company Unit Name</th>
									<th>Year</th>
									<th>Created By</th>
									<th>Date Created</th>
									<th>Filename</th>
									<th>Entries</th>
									<th class="text-center">Action</th>
									
								</tr>
							</thead>
							<tbody>
								<?php foreach ($depreciation_unit as $r): ?>
								<tr>
									<td><?=$r->company_unit_name?></td>
									<td><?=$r->trans_year?></td>
									<td><?=$r->creator?></td>
									<td><?=$r->depreciation_unit_added?></td>
									<th><a href="<?=base_url('admin/view-uploaded-file/'.encode($r->filename))?>" target="new"><?=$r->filename==''?'':explode('/', $r->filename)[2];?></a></th>
									<th><a href="<?=base_url('admin/view-uploaded-data/'.encode('depreciation_unit_tbl').'/'.encode($r->company_unit_id).'/'.encode($r->trans_year))?>" ><?=$r->trans_count?></a>
									</th>
									<th class="text-center">
										<a href="<?=base_url('admin/view-uploaded-data/'.encode('depreciation_unit_tbl').'/'.encode($r->company_unit_id).'/'.encode($r->trans_year))?>" ><i class="fa fa-file text-success"></i></a>&nbsp;
										<a class="cancel-commparative-data" href="" data-bc-id="<?=encode($r->company_unit_id)?>" data-trans-year="<?=encode($r->trans_year)?>" data-table="<?=encode('depreciation_unit_tbl')?>" ><i class="fa fa-remove text-danger"></i></a>
									</th>
								</tr>
									
								<?php endforeach; ?>

							</tbody>
						</table>

					</div>
					
					<div id="comp-depreciation-prod-unit-tab" class="tab-pane fade in">
    					<br>
						<div id="add-btn">
							<a href="#" data-toggle="modal" data-target="#modal-depreciation-prod-unit" class="btn btn-success btn-xs" data-toggle="modal""><span class="fa fa-upload"></span> Upload Depreciation Unit</a>
						</div>

						<div id="modal-depreciation-prod-unit" class="modal fade modal-confirm" role="dialog">
							<div class="modal-dialog modal-md">
							    <div class="modal-content">
							        <form method="POST" class="comparative-form" action="<?=base_url('admin/upload-depreciation-unit')?>" enctype="multipart/form-data">
								    	<div class="modal-header">
								        	<button type="button" class="close" data-dismiss="modal">&times;</button>
								       		<strong>Upload Depreciation of Production Unit</strong>
								      	</div>
								      	<div class="modal-body">

			                                <div class="form-group">
			                                	No template? 
			                                	<a class="card-link" href="<?=base_url('admin/download-depreciation-unit-temp/1')?>"><span class="fa fa-download"></span> Download here</a>
			                                	<br>			                                	
			                                	<hr>
												<label>Trans Year:</label>
												<div class="form-group">
													<div class="date">
								                        <div class="input-group input-append date comp-trans-year col-md-4">
								                            <input type="text" name="comp-depreciation-unit-year" class="form-control input-sm" placeholder="Pick year" value="">
								                            <span class="input-group-addon add-on"><span class="glyphicon glyphicon-calendar"></span></span>
								                        </div>
								                    </div>
							                	</div>
			                                    <label>Select Excel File</label><br>
			                                    <input type="file" name="depreciation-unit-file" class="form-contol-md" required accept=".xlsx" />
			                                </div>
								      	</div>

								      	<div class="modal-footer">
								      		<div class="btn-update">
							        			<button type="submit" class="btn btn-info btn-sm pull-right">Upload</button><br>
							        		</div>
								      	</div>
							        </form>
							    </div>
							</div>
						</div>

						<table class="table table-hover tbl-comparative">
							<thead>
								<tr>
									<th>Company Unit Name</th>
									<th>Year</th>
									<th>Created By</th>
									<th>Date Created</th>
									<th>Filename</th>
									<th>Entries</th>
									<th class="text-center">Action</th>
									
								</tr>
							</thead>
							<tbody>
								<?php foreach ($depreciation_unit_prod as $r): ?>
								<tr>
									<td><?=$r->company_unit_name?></td>
									<td><?=$r->trans_year?></td>
									<td><?=$r->creator?></td>
									<td><?=$r->depreciation_unit_added?></td>
									<th><a href="<?=base_url('admin/view-uploaded-file/'.encode($r->filename))?>" target="new"><?=$r->filename==''?'':explode('/', $r->filename)[2];?></a></th>
									<th><a href="<?=base_url('admin/view-uploaded-data/'.encode('depreciation_unit_tbl').'/'.encode($r->company_unit_id).'/'.encode($r->trans_year))?>" ><?=$r->trans_count?></a>
									</th>
									<th class="text-center">
										<a href="<?=base_url('admin/view-uploaded-data/'.encode('depreciation_unit_tbl').'/'.encode($r->company_unit_id).'/'.encode($r->trans_year))?>" ><i class="fa fa-file text-success"></i></a>&nbsp;
										<a class="cancel-commparative-data" href="" data-bc-id="<?=encode($r->company_unit_id)?>" data-trans-year="<?=encode($r->trans_year)?>" data-table="<?=encode('depreciation_unit_tbl')?>" ><i class="fa fa-remove text-danger"></i></a>
									</th>
								</tr>
									
								<?php endforeach; ?>

							</tbody>
						</table>

					</div>

				</div>
			</div>


			<script>
            var url = document.location.toString();
            if (url.match('#')) {
                $('.nav-tabs a[href="#' + url.split('#')[1] + '"]').tab('show');
            } 

            // Change hash for page-reload
            $('.nav-tabs a').on('shown.bs.tab', function (e) {
                window.location.hash = e.target.hash;
            })
            </script>