<div class="col-lg-12" id="content">
    <div id="breadcrumb-div">
        <ul class="breadcrumb">
            <li><a href="<?=base_url('admin')?>"><span class="glyphicon glyphicon-home"></span>&nbsp;Dashboard</a></li>
            
            <li><a href="<?=base_url('admin/commi-prod-trans/'.$commissary_id)?>">Commissary Production Transaction</a></li>
            <li class="active">View All Processed Cost Sheet (<?=$commissary->commissary_name?>)</li>
        </ul>
    </div>

    <?php
        if($this->session->flashdata('message') != "" ){
            echo $this->session->flashdata('message');
        }
    ?>
    
    <div class="row">
        <div class="col-lg-12">
            <a href="<?=base_url('admin/download-commi-cost-sheet-report/' .$commissary_id.'/'.$year)?>" class="btn btn-info btn-xs" id=""><span class="fa fa-download"></span>&nbsp;&nbsp;Download</a>		
            <!-- <a href="<?=base_url('admin/download-cost-sheet-report-customized/' .$commissary_id.'/'.$year)?>" class="btn btn-primary btn-xs" id=""><span class="fa fa-download"></span>&nbsp;&nbsp;Download Customized</a> -->
        </div>
        <?=$report?>
    </div>
</div>