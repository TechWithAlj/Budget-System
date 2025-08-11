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
	<script src="<?=base_url()?>assets/js/ahg.js?v=2.5"></script>
</head>
<body>
	<div class="main-wrapper">
		<input type="hidden" value="<?=base_url()?>" id="base_url">
		<div class="col-lg-12">
			<div class="row">

				<div id="nav">
					<ul>
						<?php $segment = $this->uri->segment(2);?>
						<li class="<?=$this->uri->segment(2) == 'dashboard' ? 'active' : ''?>" style="margin:auto; text-align: center"><a href="<?=base_url('ahg/dashboard')?>"><img src="<?=base_url()?>assets/img/icon/dashboard.png" width="25px"><br>Dashboard</a></li>
						<li class="<?=$this->uri->segment(2) == 'broiler-cost' || $this->uri->segment(2) == 'add-broiler-config' ? 'active' : ''?>" style="margin:auto; text-align: center"><a href="<?=base_url('ahg/broiler-cost')?>"><img height="25" width="25" src="<?=base_url()?>assets/img/icon/broiler.png" ><br>Broiler Cost</a></li>

						<li class="<?=$this->uri->segment(2) == 'logout' ? 'active' : ''?>" style="margin:auto; text-align: center"><a href="<?=base_url('ahg/logout')?>"><img src="<?=base_url()?>assets/img/icon/logout.png" width="24px" height="24px"><br>Logout</a></li>
					</ul>
				</div>
			</div>

			<?=$content?>

		</div>
	</div>
</body>

</html>