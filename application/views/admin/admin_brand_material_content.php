			
			<div class="col-lg-12" id="content">
				<div id="breadcrumb-div">
					<ul class="breadcrumb">
					    <li><a href="<?=base_url('admin')?>"><span class="glyphicon glyphicon-home"></span>&nbsp;Dashboard</a></li>
					    <li class="active">Brand Material</li>
					</ul>
				</div>
				<?php
					if($this->session->flashdata('message') != "" ){
						echo $this->session->flashdata('message');
					}
				?>
				
				<table class="table table-hover" id="tbl-category">
					<thead>
						<tr>
							<th>Brand</th>
							<th>Action</th>
						</tr>
					</thead>
					<tbody>

						<?php
							foreach($brand as $row):
						?>
						
						<tr>
							<td><?=$row->brand_name?></td>
							<td><a href="<?=base_url('admin/view-material/' . encode($row->brand_id))?>" class="btn btn-xs btn-success" data-id="<?=encode($row->brand_id)?>">Material</a></td>
						</tr>

						<?php endforeach;?>

					</tbody>
				</table>
			</div>