			
			<div class="col-lg-12" id="content">
				<div id="breadcrumb-div">
					<ul class="breadcrumb">
					    <li><a href="<?=base_url('admin')?>"><span class="glyphicon glyphicon-home"></span>&nbsp;Dashboard</a></li>
					    <li class="active">Logs</li>
					</ul>
				</div>
				<?php
					if($this->session->flashdata('message') != "" ){
						echo $this->session->flashdata('message');
					}
				?>
				
				<table class="table table-hover" id="tbl-business">
					<thead>
						<tr>
							<th>Name</th>
							<th>Login Date</th>
						</tr>
					</thead>
					<tbody>

						<?php
							foreach($logs as $row):
						?>
						
						<tr>
							<td><?=$row->user_lname . ', ' . $row->user_fname?></td>
							<td><?=$row->user_login_date?></td>
						</tr>

						<?php endforeach;?>

					</tbody>
				</table>
			</div>