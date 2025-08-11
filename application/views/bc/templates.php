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

	<script src="<?=base_url();?>assets/js/select2.min.js"></script>
	<script src="<?=base_url()?>assets/js/selectize.js"></script>
	
	<script src="<?=base_url()?>assets/js/bootstrap.min.js"></script>
	<script type="text/javascript" src="<?=base_url()?>assets/js/bootstrapValidator.min.js"></script>
	<script type="text/javascript" src="<?=base_url()?>assets/js/bootstrap-datepicker.min.js"></script>
	<script src="<?=base_url();?>assets/js/bootstrap-toggle.min.js"></script>
	<script type="text/javascript" src="<?=base_url()?>assets/js/chart.min.js"></script>
	<script type="text/javascript" src="<?=base_url()?>assets/js/chartjs-plugin-datalabels.min.js"></script>
	<script src="<?=base_url()?>assets/js/bc.js?v=3.2"></script>
</head>
<body>
	<div class="main-wrapper">
		<input type="hidden" value="<?=base_url()?>" id="base_url">
		<div class="col-lg-12">
			<div class="row">

				<div id="nav">
					<?php if (!MAINTENANCE_MODE):?>
						<ul>
							<?php $segment = $this->uri->segment(2);?>
							<li class="<?=$this->uri->segment(2) == 'dashboard' ? 'active' : ''?>" style="margin:auto; text-align: center"><a href="<?=base_url('business-center/dashboard')?>"><img src="<?=base_url()?>assets/img/icon/dashboard.png" width="25px"><br>Dashboard</a></li>

							<li class="dropdown <?=$this->uri->segment(2) == 'sales' || $this->uri->segment(2) == 'outlet-brand' || $this->uri->segment(2) == 'outlet-budget' || $segment == 'sales-info' || $segment == 'sales-view' ? 'active' : ''?>" style="margin:auto; text-align: center">
								<a href="#" class="dropdown-toggle" data-toggle="dropdown"><img src="<?=base_url()?>assets/img/icon/sales.png" width="25px"><br>Transactions</a>
								<ul id="products-menu" class="dropdown-menu clearfix" role="menu">
									<li><a href="<?=base_url('business-center/capex-info')?>">CAPEX</a></li>
									<li><a href="<?=base_url('business-center/opex-info')?>">OPEX</a></li>
									<li><a href="<?=base_url('business-center/sales-info')?>">Sales</a></li>
									<li><a href="<?=base_url('business-center/sales-bom')?>">Sales BOM</a></li>
									<li><a href="<?=base_url('business-center/broiler-cost')?>">Broiler Cost</a></li>
									<li><a href="<?=base_url('business-center/production-cost')?>">Product Cost</a></li>
									<li><a href="<?=base_url('business-center/tactical-info')?>">Tactical Discount</a></li>
									<li><a href="<?=base_url('business-center/live-alw')?>">ALW For Live</a></li>
									<li><a href="<?=base_url('business-center/manpower')?>">Manpower</a></li>
								</ul>

							</li>

							<li class="dropdown <?=$this->uri->segment(2) == 'materials' || $this->uri->segment(2) == '' ? 'active' : ''?>" style="margin:auto; text-align: center">
								<a href="#" class="dropdown-toggle" data-toggle="dropdown"><img src="<?=base_url()?>assets/img/icon/material.png" ><br>Budget Config</a>

								<ul id="products-menu" class="dropdown-menu clearfix" role="menu">
									<li><a href="<?=base_url('business-center/materials')?>">Materials</a></li>
									<li><a href="<?=base_url('business-center/brand-bc-info')?>">Brand BC Materials</a></li>
									<li><a href="<?=base_url('business-center/outlets')?>">Outlets</a></li>
									<li><a href="<?=base_url('business-center/asset-group')?>">Assets</a></li>
									<li><a href="<?=base_url('business-center/gl-group')?>">GL Group</a></li>
									<li><a href="<?=base_url('business-center/sales-commission')?>">Sales Commission</a></li>
									<li><a href="<?=base_url('business-center/percent-rent')?>">Percentage Rent</a></li>
								</ul>

							</li>

							<li class="<?=$this->uri->segment(2) == 'comparative-data-upload' ? 'active' : ''?>" style="margin:auto; text-align: center"><a href="<?=base_url('business-center/comparative-data-upload')?>"><img src="<?=base_url()?>assets/img/icon/dashboard.png" width="25px"><br>Comparative Data</a></li>

							<!-- <li class="<?=$this->uri->segment(2) == 'broiler-cost' || $this->uri->segment(2) == 'new-broiler-config' || $segment == 'view-broiler-config' || $segment == 'view-broiler-group' || $segment == 'broiler-trans' || $segment == 'new-broiler-trans' || $segment == 'view-broiler-summary' || $segment == 'view-broiler-trans' || $segment == 'edit-broiler-config' || $segment == 'new-industry-trans' || $segment == 'edit-industry-trans' || $segment == 'compute-broiler-summary' ? 'active' : ''?>" style="margin:auto; text-align: center"><a href="<?=base_url('business-center/broiler-cost')?>"><img height="25" width="25" src="<?=base_url()?>assets/img/icon/broiler.png" ><br>Broiler Cost</a></li>

							<li class="<?=$this->uri->segment(2) == 'production-cost' || $this->uri->segment(2) == 'add-broiler-config' || ($segment == 'view-config-prod' && decode($this->uri->segment(4)) != 5) || $segment == 'prod-trans' || ($segment == 'view-prod-trans' && decode($this->uri->segment(5)) != 5) || ($segment == 'view-cost-sheet' && decode($this->uri->segment(6)) != 5) || $segment == 'new-prod-trans' || ($segment == 'edit-prod-trans' && decode($this->uri->segment(7)) != 5) || $segment == 'new-ext-prod-trans' || $segment == 'edit-ext-prod-trans' ? 'active' : ''?>" style="margin:auto; text-align: center"><a href="<?=base_url('business-center/production-cost')?>"><img height="25" width="25" src="<?=base_url()?>assets/img/icon/production.png" ><br>Production Cost</a></li>


							<li class="<?=$this->uri->segment(2) == 'sales-bom' || ($segment == 'view-config-prod' && decode($this->uri->segment(4)) == 5) || $segment == 'sales-bom-trans' || $segment == 'new-sales-bom-trans' || ($segment == 'view-prod-trans' && decode($this->uri->segment(5)) == 5) || ($segment == 'view-cost-sheet' && decode($this->uri->segment(6)) == 5) || ($segment == 'edit-prod-trans' && decode($this->uri->segment(7)) == 5) ? 'active' : ''?>" style="margin:auto; text-align: center"><a href="<?=base_url('business-center/sales-bom')?>"><img height="25" width="25" src="<?=base_url()?>assets/img/icon/bom.png" ><br>Sales BOM</a></li>

							<li class="<?=$this->uri->segment(2) == 'sales' || $this->uri->segment(2) == 'outlet-brand' || $this->uri->segment(2) == 'outlet-budget' || $segment == 'sales-info' || $segment == 'sales-view' ? 'active' : ''?>" style="margin:auto; text-align: center"><a href="<?=base_url('business-center/sales-info')?>"><img src="<?=base_url()?>assets/img/icon/sales.png" width="25px"><br>Sales</a></li>

							<li class="<?=$segment == 'capex' || $segment == 'capex-info' || $segment == 'view-capex' || $segment == 'transac-capex' ||  $segment == 'add-capex-item' ? 'active' : ''?>" style="margin:auto; text-align: center"><a href="<?=base_url('business-center/capex-info')?>"><img src="<?=base_url()?>assets/img/icon/capex.png" width="25px"><br>CAPEX</a></li>

							<li class="<?=$segment == 'opex' || $segment == 'opex-info' || $segment == 'transac-opex' ||  $segment == 'view-opex' ||  $segment == 'add-opex-item' || $segment == 'sw-view' || $segment == 'add-sw-item' || $segment == 'view-store-expense' || $segment == 'view-store-expense-item' || $segment == 'add-store-expense-item' ? 'active' : ''?>" style="margin:auto; text-align: center"><a href="<?=base_url('business-center/opex-info')?>"><img src="<?=base_url()?>assets/img/icon/opex.png" width="25px"><br>OPEX</a></li>


							<li class="<?=$this->uri->segment(2) == 'materials' || $this->uri->segment(2) == '' ? 'active' : ''?>" style="margin:auto; text-align: center"><a href="<?=base_url('business-center/materials')?>"><img src="<?=base_url()?>assets/img/icon/material.png" ><br>Materials</a></li>

							<li class="<?=$this->uri->segment(2) == 'outlets' ? 'active' : ''?>" style="margin:auto; text-align: center"><a href="<?=base_url('business-center/outlets')?>"><img src="<?=base_url()?>assets/img/icon/material.png" ><br>Outlets</a></li>

							<li class="<?=$this->uri->segment(2) == 'tactical-info' || $this->uri->segment(2) == 'view-tactical-price' ? 'active' : ''?>" style="margin:auto; text-align: center"><a href="<?=base_url('business-center/tactical-info')?>"><img src="<?=base_url()?>assets/img/icon/tactical.png" width="24px" height="24px"><br>Tactical Discount</a></li>

							<li class="<?=$this->uri->segment(2) == 'live-alw' ? 'active' : ''?>" style="margin:auto; text-align: center"><a href="<?=base_url('business-center/live-alw')?>"><img src="<?=base_url()?>assets/img/icon/weight.png" width="25px"><br>ALW For Live</a></li> -->

							<!-- <li class="<?=$this->uri->segment(2) == 'employees' ? 'active' : ''?>" style="margin:auto; text-align: center"><a href="<?=base_url('business-center/employees')?>"><img src="<?=base_url()?>assets/img/icon/employee.png" width="25px"><br>Employees</a></li> -->

							<li class="<?=$this->uri->segment(2) == 'logout' ? 'active' : ''?>" style="margin:auto; text-align: center"><a href="<?=base_url('business-center/logout')?>"><img src="<?=base_url()?>assets/img/icon/logout.png" width="24px" height="24px"><br>Logout</a></li>
						</ul>
					<?php else: ?>
						<ul>
							<?php $segment = $this->uri->segment(2);?>
							<li class="<?=$this->uri->segment(2) == 'dashboard' ? 'active' : ''?>" style="margin:auto; text-align: center"><a href="<?=base_url('business-center/dashboard')?>"><img src="<?=base_url()?>assets/img/icon/dashboard.png" width="25px"><br>Dashboard</a></li>

							

							<li class="dropdown <?=$this->uri->segment(2) == 'materials' || $this->uri->segment(2) == '' ? 'active' : ''?>" style="margin:auto; text-align: center">
								<a href="#" class="dropdown-toggle" data-toggle="dropdown"><img src="<?=base_url()?>assets/img/icon/material.png" ><br>Budget Config</a>

								<ul id="products-menu" class="dropdown-menu clearfix" role="menu">
									<li><a href="<?=base_url('business-center/materials')?>">Materials</a></li>
									<li><a href="<?=base_url('business-center/brand-bc-info')?>">Brand BC Materials</a></li>
									<li><a href="<?=base_url('business-center/outlets')?>">Outlets</a></li>
									<li><a href="<?=base_url('business-center/asset-group')?>">Assets</a></li>
									<li><a href="<?=base_url('business-center/gl-group')?>">GL Group</a></li>
									<li><a href="<?=base_url('business-center/sales-commission')?>">Sales Commission</a></li>
									<!-- <li><a href="<?=base_url('business-center/percent-rent')?>">Percentage Rent</a></li> -->
								</ul>

							</li>


							<li class="<?=$this->uri->segment(2) == 'logout' ? 'active' : ''?>" style="margin:auto; text-align: center"><a href="<?=base_url('business-center/logout')?>"><img src="<?=base_url()?>assets/img/icon/logout.png" width="24px" height="24px"><br>Logout</a></li>
						</ul>
					<?php endif; ?>
				</div>
			</div>

			<?=$content?>

		</div>
	</div>
</body>

</html>
