            <div class="col-lg-12" id="content">
				<div id="breadcrumb-div">
					<ul class="breadcrumb">
					    <li><a href="<?=base_url('admin')?>"><span class="glyphicon glyphicon-home"></span>&nbsp;Dashboard</a></li>
					  	
					    <li><a href="<?=base_url('admin/comparative-data-upload')?>">Comparative Data</a></li>
					    <li class="active">Comparative Data Upload Info of <?=$table_title?></li>
					    
					</ul>
				</div>
			
				<?php
					if($this->session->flashdata('message') != "" ){
						echo $this->session->flashdata('message');
					}
				?>

				<div class="row">
                    
                    
                    <?=$table_view?>
                    
                </div>

				<br><br>
			</div>