<div class="col-lg-12" id="content">
    <div id="breadcrumb-div">
        <ul class="breadcrumb">
            <li><a href="<?=base_url('admin')?>"><span class="glyphicon glyphicon-home"></span>&nbsp;Dashboard</a></li>
            <li><a href="<?=base_url('admin/percent-rent')?>">Percentage Rent</a></li>
            <li class="active">Percentage Rent Maintenance (<?=$bc->bc_name?>)</li>
        </ul>
    </div>

    <?php
        if($this->session->flashdata('message') != "" ){
            echo $this->session->flashdata('message');
        }
    ?>
    <ul class="nav nav-tabs">
        <li class="active"><a data-toggle="tab" href="#percent-rent-maintenance-tab" class="tab-letter">Percentage Rent Maintenance</a></li>
        
    </ul>
    <br>
    <div class="tab-content">
        <div id="percent-rent-maintenance-tab" class="tab-pane fade in active">
            
            
            
            <div id="modal-confirm" class="modal fade" role="dialog">
                <div class="modal-dialog modal-sm">
                    <div class="modal-content">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal">&times;</button>
                            <strong>Confirmation message</strong>
                        </div>
                        <div class="modal-body">
                            <form method="POST" action="<?=base_url('admin/cancel-percent-rent')?>" enctype="multipart/form-data" id="remove-percent-rent">
                                <input type="hidden" name="percent_rent_id" id="percent_rent_id">
                                <input type="hidden" name="bc_id" id="bc_id">
                                
                                

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

            <div id="modal-percent-rent" class="modal fade modal-confirm" role="dialog">
                <div class="modal-dialog modal-md">
                    <div class="modal-content">
                        <form method="POST" class="comparative-form" action="<?=base_url('admin/upload-percent-rent')?>" enctype="multipart/form-data">
                            <div class="modal-header">
                                <button type="button" class="close" data-dismiss="modal">&times;</button>
                                <strong>Upload Percentage Rent of <?=$bc->bc_name?></strong>
                            </div>
                            <div class="modal-body">

                                <div class="form-group">
                                    No template? 
                                    <a class="card-link" href="<?=base_url('admin/download-percent-rent-temp/'.$bc_id)?>"><span class="fa fa-download"></span> Download here</a>
                                    <br>
                                    <hr>
                                    <label>Year:</label>
                                    <div class="form-group">
                                        <div class="date">
                                            <div class="input-group input-append date comp-trans-year col-md-4">
                                                <input type="text" name="percent-rent-year" class="form-control input-sm" placeholder="Pick year" value="">
                                                <span class="input-group-addon add-on"><span class="glyphicon glyphicon-calendar"></span></span>
                                            </div>
                                        </div>
                                    </div>

                                    <input type="hidden" name="bc-id" value="<?=$bc_id?>">
                                    
                                    <label>Select Excel File</label><br>
                                    <input type="file" name="percent-rent-file" class="form-contol-md" required accept=".xlsx" />
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

            

            <div id="add-btn">

                <a href="#" class="btn btn-success btn-xs" data-toggle="modal" data-target="#modal-percent-rent"><span class="fa fa-upload"></span> Upload Percentage Rent</a>
            </div>

            <div class="table-responsive">
                <table class="table table-hover table-bordered table-stripe nowrap" id="tbl-percent-rent-maintenance">
                    <thead>
                        <tr>
                            <th width="1%"></th>
                            <th width="auto">Brand</th>
                            <th width="auto">Outlet | Customer</th>
                            <th width="auto">Material</th>
                            <th width="auto">Year</th>
                            <th width="auto">Created By</th>
                            <th width="auto">Date Created</th>
                            <th width="20%" class="text-center">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php foreach($percent_rent as $row):?>

                        <tr>
                            <?php if($row->percent_rent_status != 2){ ?>
                            <td class="text-center"><a href="" class="remove-percent-rent" data-id="<?=encode($row->percent_rent_id)?>" data-bc-id="<?=$bc_id?>" data-mat-desc="<?=$row->material_desc?>" data-outlet-name="<?=$row->outlet_name?>"><i class="fa fa-remove"></i></a></td>
                            <?php } ?>
                            <td><?=$row->brand_name?></td>
                            <td><?=$row->ifs_code.' - '.$row->outlet_name?></td>
                            <td><?=$row->material_code.' - '.$row->material_desc?></td>
                            <td><?=$row->percent_rent_year?></td>
                            <td><?=$row->user_fname.' '.$row->user_lname?></td>
                            <td><?=time_stamp_display($row->percent_rent_added)?></td>
                            <td class="text-center"><a href="<?=base_url('admin/edit-percent-rent/' . encode($row->percent_rent_id).'/'.$bc_id)?>" class="btn btn-xs btn-success edit-broiler-group">Edit</a></td>

                        </tr>

                    <?php endforeach;?>
                        
                    </tbody>
                </table>
                <br>
            </div>
        </div>

    </div>
</div>