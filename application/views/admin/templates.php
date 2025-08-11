
<!DOCTYPE html>
<html>
<head>
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<meta content="text/html;charset=utf-8" http-equiv="Content-Type">
	<meta content="utf-8" http-equiv="encoding">
	<title><?=$title?></title>
	<link rel="stylesheet" type="text/css" href="<?=base_url()?>assets/css/bootstrap.min.css">
	
	<link rel="stylesheet" type="text/css" href="<?=base_url()?>assets/css/dataTables.bootstrap.min.css">
	<link rel="stylesheet" type="text/css" href="<?=base_url()?>assets/css/fixedHeader.dataTables.min.css">
	<link rel="stylesheet" type="text/css" href="<?=base_url()?>assets/css/fixedColumns.bootstrap.min.css">

	<link href="<?=base_url();?>assets/css/select2.min.css" rel="stylesheet" />

	<link rel="stylesheet" type="text/css" href="<?=base_url()?>assets/css/bootstrapValidator.css">
	<link rel="stylesheet" href="<?=base_url()?>assets/css/bootstrap-datepicker3.css">
	<link rel="stylesheet" href="<?=base_url()?>assets/css/admin.css?v=3.1">
	<link rel="stylesheet" href="<?=base_url('assets/css/font-awesome.min.css')?>">
	<link rel="stylesheet" type="text/css" href="<?=base_url();?>assets/css/selectize.bootstrap3.min.css">
	<link href="<?=base_url();?>assets/css/bootstrap-toggle.min.css" rel="stylesheet">

	<script src="<?=base_url()?>assets/js/jquery.js"></script>
	<script type="text/javascript" charset="utf8" src="<?=base_url()?>assets/js/jquery.dataTables.js"></script>
	
	<script type="text/javascript" charset="utf8" src="<?=base_url()?>assets/js/dataTables.bootstrap.min.js"></script>
	<script type="text/javascript" charset="utf8" src="<?=base_url()?>assets/js/dataTables.fixedColumns.min.js"></script>
	<script type="text/javascript" charset="utf8" src="<?=base_url()?>assets/js/dataTables.fixedHeader.min.js"></script>
	<script src="<?=base_url('assets/js/dataTables.checkboxes.min.js')?>"></script>

	<script src="<?=base_url();?>assets/js/select2.min.js"></script>
	<script src="<?=base_url()?>assets/js/selectize.js"></script>
	
	<script src="<?=base_url()?>assets/js/bootstrap.min.js"></script>
	<script type="text/javascript" src="<?=base_url()?>assets/js/bootstrapValidator.min.js"></script>
	<script type="text/javascript" src="<?=base_url()?>assets/js/bootstrap-datepicker.min.js"></script>
	<script src="<?=base_url();?>assets/js/bootstrap-toggle.min.js"></script>
	<script type="text/javascript" src="<?=base_url()?>assets/js/chart.min.js"></script>
	<script type="text/javascript" src="<?=base_url()?>assets/js/chartjs-plugin-datalabels.min.js"></script>
	<script src="<?=base_url()?>assets/js/admin.js?v=3.2"></script>
	<script src="<?=base_url()?>assets/js/dashboard.js?v=3.1"></script>
</head>
<body>
	<div class="main-wrapper">
		<input type="hidden" value="<?=base_url()?>" id="base_url">
		<div class="col-lg-12">
			<div class="row">

				<div id="nav">
				<?php

					$segment = $this->uri->segment(2);
					$login = $this->session->userdata('bavi_purchasing');



					if(isset($login)){
						$user_type = decode($login['user_type_id']);
					}
				?>
					<?php if (!MAINTENANCE_MODE_ADMIN):?>
						<ul>
							

							<?php if($user_type == 1):?>

							<li class="<?=$this->uri->segment(2) == 'dashboard' ? 'active' : ''?>" style="margin:auto; text-align: center"><a href="<?=base_url('dashboard/')?>"><img src="<?=base_url()?>assets/img/icon/dashboard.png" width="25px"><br>Dashboard</a></li>
							
							<?php endif;?>


							<?php if($user_type == 5):?>

								<li class="dropdown <?=
								$segment == 'sales' ||
								$segment == 'outlet-brand' ||
								$segment == 'outlet-budget' ||
								$segment == 'sales-info' ||
								$segment == 'sales-view'
								? 'active' : ''?>" style="margin:auto; text-align: center">
									<a href="#" class="dropdown-toggle" data-toggle="dropdown"><img src="<?=base_url()?>assets/img/icon/sales.png" width="25px"><br>Transactions</a>
									<ul id="products-menu" class="dropdown-menu clearfix" role="menu">
										
										<li><a title="Includes Liempo Prod Cost" href="<?=base_url('admin/commi-production-cost')?>">Commi Prod Cost</a></li>
										
									</ul>

								</li>

							<?php endif;?>

							

							<?php if($user_type == 1):?>

								<li class="dropdown <?=
								$segment == 'sales' ||
								$segment == 'outlet-brand' ||
								$segment == 'outlet-budget' ||
								$segment == 'sales-info' ||
								$segment == 'sales-view'
								? 'active' : ''?>" style="margin:auto; text-align: center">
									<a href="#" class="dropdown-toggle" data-toggle="dropdown"><img src="<?=base_url()?>assets/img/icon/sales.png" width="25px"><br>Transactions</a>
									<ul id="products-menu" class="dropdown-menu clearfix" role="menu">
										<li><a href="<?=base_url('admin/capex')?>">CAPEX</a></li>
										<li><a href="<?=base_url('admin/opex')?>">OPEX</a></li>
										<li><a href="<?=base_url('admin/sales')?>">Sales</a></li>
										<li><a href="<?=base_url('admin/sales-bom')?>">Sales BOM</a></li>
										<li><a href="<?=base_url('admin/broiler-cost')?>">Broiler Cost</a></li>
										<li><a href="<?=base_url('admin/production-cost')?>">Product Cost</a></li>
										<li><a title="Includes Liempo Prod Cost" href="<?=base_url('admin/commi-production-cost')?>">Commi Prod Cost</a></li>
										<li><a href="<?=base_url('admin/tactical-price')?>">Tactical Discount</a></li>
										<li><a href="<?=base_url('admin/live-alw')?>">ALW For Live</a></li>
										<li><a href="<?=base_url('admin/manpower')?>">Manpower</a></li>
									</ul>

								</li>


								<li class="dropdown <?=$this->uri->segment(2) == 'materials' || $this->uri->segment(2) == '' ? 'active' : ''?>" style="margin:auto; text-align: center">
									<a href="#" class="dropdown-toggle" data-toggle="dropdown"><img src="<?=base_url()?>assets/img/icon/material.png" ><br>Budget Config</a>

									<ul id="products-menu" class="dropdown-menu clearfix" role="menu">
										<li><a href="<?=base_url('admin/materials')?>">Materials</a></li>
										<li><a href="<?=base_url('admin/brand-material')?>">Brand Materials</a></li>
										<li><a href="<?=base_url('admin/brand-bc-material')?>">Brand BC Materials</a></li>
										<li><a href="<?=base_url('admin/outlets')?>">Outlets</a></li>
										<li><a href="<?=base_url('admin/asset-group')?>">Assets</a></li>
										<li><a href="<?=base_url('admin/gl-group')?>">GL Group</a></li>
										
										<li><a href="<?=base_url('admin/lock-module')?>">Lock Module</a></li>
										<li><a href="<?=base_url('admin/cost-center')?>">Cost Center</a></li>
										<li><a href="<?=base_url('admin/sales-commission')?>">Sales Commission</a></li>
										<!-- <li><a href="<?=base_url('admin/percent-rent')?>">Percentage Rent</a></li> -->
									</ul>

								</li>
								
								<li class="<?=$this->uri->segment(2) == 'users' ? 'active' : ''?>" style="margin:auto; text-align: center"><a href="<?=base_url('admin/users')?>"><img src="<?=base_url()?>assets/img/icon/user.png"><br>Users</a></li>
								<li class="<?=$this->uri->segment(2) == 'logs' ? 'active' : ''?>" style="margin:auto; text-align: center"><a href="<?=base_url('admin/logs')?>"><img src="<?=base_url()?>assets/img/icon/report.png"><br>Logs</a></li>

								<li class="<?=$this->uri->segment(2) == 'comparative-data-upload' ? 'active' : ''?>" style="margin:auto; text-align: center"><a href="<?=base_url('admin/comparative-data-upload')?>"><img src="<?=base_url()?>assets/img/icon/dashboard.png" width="25px"><br>Comparative Data</a></li>

							<?php endif;?>

							<li class="<?=$this->uri->segment(2) == 'logout' ? 'active' : ''?>" style="margin:auto; text-align: center"><a href="<?=base_url('admin/logout')?>"><img src="<?=base_url()?>assets/img/icon/logout.png" width="24px" height="24px"><br>Logout</a></li>
						</ul>
					<?php else: ?>
						<ul>
							

							<?php if($user_type == 1):?>

							<li class="<?=$this->uri->segment(2) == 'dashboard' ? 'active' : ''?>" style="margin:auto; text-align: center"><a href="<?=base_url('dashboard/')?>"><img src="<?=base_url()?>assets/img/icon/dashboard.png" width="25px"><br>Dashboard</a></li>
							
							<?php endif;?>


							

							

							<?php if($user_type == 1):?>

								


								<li class="dropdown <?=$this->uri->segment(2) == 'materials' || $this->uri->segment(2) == '' ? 'active' : ''?>" style="margin:auto; text-align: center">
									<a href="#" class="dropdown-toggle" data-toggle="dropdown"><img src="<?=base_url()?>assets/img/icon/material.png" ><br>Budget Config</a>

									<ul id="products-menu" class="dropdown-menu clearfix" role="menu">
										<li><a href="<?=base_url('admin/materials')?>">Materials</a></li>
										<li><a href="<?=base_url('admin/brand-material')?>">Brand Materials</a></li>
										<li><a href="<?=base_url('admin/brand-bc-material')?>">Brand BC Materials</a></li>
										<li><a href="<?=base_url('admin/outlets')?>">Outlets</a></li>
										<li><a href="<?=base_url('admin/asset-group')?>">Assets</a></li>
										<li><a href="<?=base_url('admin/gl-group')?>">GL Group</a></li>
										
										<li><a href="<?=base_url('admin/lock-module')?>">Lock Module</a></li>
										<li><a href="<?=base_url('admin/cost-center')?>">Cost Center</a></li>
										<li><a href="<?=base_url('admin/sales-commission')?>">Sales Commission</a></li>
										<li><a href="<?=base_url('admin/percent-rent')?>">Percentage Rent</a></li>
									</ul>

								</li>
								
								<li class="<?=$this->uri->segment(2) == 'users' ? 'active' : ''?>" style="margin:auto; text-align: center"><a href="<?=base_url('admin/users')?>"><img src="<?=base_url()?>assets/img/icon/user.png"><br>Users</a></li>
								<li class="<?=$this->uri->segment(2) == 'logs' ? 'active' : ''?>" style="margin:auto; text-align: center"><a href="<?=base_url('admin/logs')?>"><img src="<?=base_url()?>assets/img/icon/report.png"><br>Logs</a></li>

								

							<?php endif;?>

							<li class="<?=$this->uri->segment(2) == 'logout' ? 'active' : ''?>" style="margin:auto; text-align: center"><a href="<?=base_url('admin/logout')?>"><img src="<?=base_url()?>assets/img/icon/logout.png" width="24px" height="24px"><br>Logout</a></li>
						</ul>

					<?php endif; ?>
				</div>
			</div>

			<?=$content?>

		</div>
	</div>
</body>

</html>