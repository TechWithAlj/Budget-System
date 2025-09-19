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
	<link rel="stylesheet" href="<?=base_url()?>assets/css/admin.css?v=2.5">
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
	<script src="<?=base_url()?>assets/js/unit.js?v=2.9"></script>
	<script src="<?=base_url()?>assets/js/dashboard.js?v=3.1"></script>
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
							<!-- <li class="<?=$this->uri->segment(2) == 'dashboard' ? 'active' : ''?>" style="margin:auto; text-align: center"><a href="<?=base_url('admin/dashboard')?>"><img src="<?=base_url()?>assets/img/icon/dashboard.png" width="25px"><br>Dashboard</a></li> -->

							<li class="<?=$this->uri->segment(2) == 'dashboard' ? 'active' : ''?>" style="margin:auto; text-align: center"><a href="<?=base_url('unit/dashboard/')?>"><img src="<?=base_url()?>assets/img/icon/dashboard.png" width="25px"><br>Dashboard</a></li>

							<li class="<?=$segment == 'capex' || $segment == 'capex-info' || $segment == 'view-capex' || $segment == 'transac-capex' ||  $segment == 'add-capex-item' ? 'active' : ''?>" style="margin:auto; text-align: center"><a href="<?=base_url('unit/capex-info')?>"><img src="<?=base_url()?>assets/img/icon/capex.png" width="25px"><br>CAPEX</a></li>

							<li class="<?=$segment == 'opex' || $segment == 'opex-info' || $segment == 'transac-opex' ||  $segment == 'view-opex' ||  $segment == 'add-opex-item' || $segment == 'sw-view' || $segment == 'add-sw-item' ? 'active' : ''?>" style="margin:auto; text-align: center"><a href="<?=base_url('unit/opex-info')?>"><img src="<?=base_url()?>assets/img/icon/opex.png" width="25px"><br>OPEX</a></li>

							<li class="<?=$this->uri->segment(2) == 'manpower' ? 'active' : '' ?>" style="margin:auto; text-align: center"><a href="<?=base_url('unit/manpower')?>"><img src="<?=base_url()?>assets/img/icon/employee.png" width="25px"><br>Manpower</a></li>

							

							<li class="dropdown <?=$this->uri->segment(2) == 'materials' || $this->uri->segment(2) == '' ? 'active' : ''?>" style="margin:auto; text-align: center">
								<a href="#" class="dropdown-toggle" data-toggle="dropdown"><img src="<?=base_url()?>assets/img/icon/material.png" ><br>Budget Config</a>

								<ul id="products-menu" class="dropdown-menu clearfix" role="menu">
									<li><a href="<?=base_url('unit/asset-group')?>">Assets</a></li>
									<li><a href="<?=base_url('unit/gl-group')?>">GL Group</a></li>
								</ul>

							</li>



							<li class="<?=$this->uri->segment(2) == 'comparative-data-upload' ? 'active' : ''?>" style="margin:auto; text-align: center"><a href="<?=base_url('unit/comparative-data-upload')?>"><img src="<?=base_url()?>assets/img/icon/dashboard.png" width="25px"><br>Comparative Data</a></li>

							<li class="<?=$this->uri->segment(2) == 'logout' ? 'active' : ''?>" style="margin:auto; text-align: center"><a href="<?=base_url('unit/logout')?>"><img src="<?=base_url()?>assets/img/icon/logout.png" width="24px" height="24px"><br>Logout</a></li>
						</ul>
					
					<?php else: ?>
						<ul>
							<?php $segment = $this->uri->segment(2);?>
							<!-- <li class="<?=$this->uri->segment(2) == 'dashboard' ? 'active' : ''?>" style="margin:auto; text-align: center"><a href="<?=base_url('admin/dashboard')?>"><img src="<?=base_url()?>assets/img/icon/dashboard.png" width="25px"><br>Dashboard</a></li> -->
	
							<li class="<?=$this->uri->segment(2) == 'dashboard' ? 'active' : ''?>" style="margin:auto; text-align: center"><a href="<?=base_url('unit/dashboard/')?>"><img src="<?=base_url()?>assets/img/icon/dashboard.png" width="25px"><br>Dashboard</a></li>
							
	
							<li class="dropdown <?=$this->uri->segment(2) == 'materials' || $this->uri->segment(2) == '' ? 'active' : ''?>" style="margin:auto; text-align: center">
								<a href="#" class="dropdown-toggle" data-toggle="dropdown"><img src="<?=base_url()?>assets/img/icon/material.png" ><br>Budget Config</a>
	
								<ul id="products-menu" class="dropdown-menu clearfix" role="menu">
									<li><a href="<?=base_url('unit/asset-group')?>">Assets</a></li>
									<li><a href="<?=base_url('unit/gl-group')?>">GL Group</a></li>
								</ul>
	
							</li>
	
	
	
							<li class="<?=$this->uri->segment(2) == 'logout' ? 'active' : ''?>" style="margin:auto; text-align: center"><a href="<?=base_url('unit/logout')?>"><img src="<?=base_url()?>assets/img/icon/logout.png" width="24px" height="24px"><br>Logout</a></li>
						</ul>

					<?php endif; ?>
				</div>
			</div>

			<?=$content?>

		</div>
	</div>
</body>

</html>
