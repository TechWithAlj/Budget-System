<!DOCTYPE html>
<html>
<head>
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<meta content="text/html;charset=utf-8" http-equiv="Content-Type">
	<meta content="utf-8" http-equiv="encoding">
	<title>BAVI <?=$year?> Budget System</title>
	<link rel="stylesheet" type="text/css" href="<?=base_url()?>assets/css/bootstrap.min.css">
	<link rel="stylesheet" type="text/css" href="<?=base_url()?>assets/css/dataTables.bootstrap.min.css">
	<link rel="stylesheet" href="<?=base_url()?>assets/css/login.css">
 	
	<script src="<?=base_url()?>assets/js/jquery.js"></script>
	<script src="<?=base_url()?>assets/js/bootstrap.min.js"></script>

</head>
<body>
	<div class="main-wrapper">
		<input type="hidden" value="<?=base_url()?>" id="base_url">
		<div class="col-lg-12">
			<div class="row">	
				<div class="col-lg-4 col-md-4" id="login-div">

					<img src="<?=base_url()?>assets/img/CTGI-LOGO-2.png" id="logo" style="display:block;margin: auto;padding-top: 5px;" width="80%" height="80%">
					<div id="login-title">
						<!-- <h4>Chooks To Go, Inc.</h4> -->
						Welcome to Budget System <br/ ><strong><?=$year?></strong>
					</div>

					<div id="login-form">
						<?php
							if($this->session->flashdata('message') != "" ){
								echo $this->session->flashdata('message');
							}
						?>
						<form action="<?=base_url()?>login/login-process" method="POST">
							<label>Email:</label>
							<div class="form-group">
								<input type="text" class="form-control" name="email">
							</div>

							<label>Password:</label>
							<div class="form-group">
								<input type="password" class="form-control" name="password">
							</div>

							<div class="form-group text-right">
								<button type="submit" id="login-btn" class="btn btn-success">Login</button>
							</div>
						</form>
					</div>
				</div>
				<div class="col-lg-8 col-md-8" id="login-background">
				</div>
			</div>
		</div>
	</div>
</body>

</html>