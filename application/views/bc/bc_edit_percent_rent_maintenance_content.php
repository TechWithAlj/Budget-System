<div class="col-lg-12" id="content">
    <div id="breadcrumb-div">
        <ul class="breadcrumb">
            <li><a href="<?=base_url('business-center')?>"><span class="glyphicon glyphicon-home"></span>&nbsp;Dashboard</a></li>
            <li><a href="<?=base_url('business-center/percent-rent-maintenance/'.$bc_id)?>">Percentage Rent Maintenance</a></li>
            <li class="active">Edit Percentage Rent Maintenance (<?=$bc->bc_name?>)</li>
        </ul>
    </div>

    <?php
        if($this->session->flashdata('message') != "" ){
            echo $this->session->flashdata('message');
        }
    ?>

    <form method="post" action="<?=base_url('business-center/update-percent-rent')?>" id="">

        <input type="hidden" name="bc_id" value="<?=$bc_id?>">

        <label for="">Brand: <?=$percent_rent[0]->brand_name?></label><br>
        <label for="">Amount Type: <?=$percent_rent[0]->amount_type_name?></label><br>
        <label for="">Last Modification: <?=time_stamp_display($percent_rent[0]->percent_rent_det_added)?></label>
        <div class="table-responsive">
            <table class="table table-hover nowrap" id="tbl-edit-percent-rent">
                <thead>
                    <tr>
                        <th width="auto" nowrap="true" class="text-center"></th>
                        <th width="1%" nowrap="true" class="text-center">Outlet | Customer</th>
                        <th width="auto" nowrap="true" class="text-center">Material</th>
                        <th width="auto" class="text-center">Jan</th>
                        <th width="auto" class="text-center">Feb</th>
                        <th width="auto" class="text-center">Mar</th>
                        <th width="auto" class="text-center">Apr</th>
                        <th width="auto" class="text-center">May</th>
                        <th width="auto" class="text-center">Jun</th>
                        <th width="auto" class="text-center">Jul</th>
                        <th width="auto" class="text-center">Aug</th>
                        <th width="auto" class="text-center">Sep</th>
                        <th width="auto" class="text-center">Oct</th>
                        <th width="auto" class="text-center">Nov</th>
                        <th width="auto" class="text-center">Dec</th>
                    </tr>
                    
                </thead>
                <?php
                $i = 0;
                $table = '<tbody>';
                foreach($percent_rent as $row){
                    if($i == 0){
                        $table .= '<tr>';
                        $table .= '<input type="hidden" name="percent_rent_id" value="'.encode($row->percent_rent_id).'">';
                        $table .= '<input type="hidden" name="percent_rent_year" value="'.encode($row->percent_rent_year).'">';
                        
                        $table .= '<td class="text-center"><a href="#" class="slider-broiler"><span class="fa fa-sliders"></span></td>';
                        $table .= '<td>'.$row->outlet_name.'</td>';
                        $table .= '<td>'.$row->material_desc.'</td>';
                    }

                    $table .= '<td><input type="text" class="form-control form-control-md text-right" name="percent_rent_det_value[]" value="'.$row->percent_rent_det_value.'" /></td>';
                    $i++;
                }
                $table .= '</tr></tbody>';

                echo $table;
                ?>

            </table>
        </div>
        
        
        <div class="text-right" id="expenditures-add-btn">
            <button type="submit" class="btn btn-success btn-sm">Save</button>
        </div>
        
    </form>

    <div id="modal-slider-broiler" class="modal fade" role="dialog">
        <div class="modal-dialog modal-sm">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <strong>Budgeting Slider Tool</strong>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="id" id="id">
                    <div class="slider-div">
                        <label>Qty:&nbsp;&nbsp;</label><input type="number" class="form-control input-sm" id="slider-qty-val"><br />
                        <input type="range" min="1" max="5000" value="0" class="slider" id="slider-qty">
                    </div>
                    <div class="slider-div">
                        <label>Month Start:&nbsp;&nbsp;<span id="slider-qty-start-val"></span></label>
                        <input type="range" min="1" max="12" value="1" class="slider" id="slider-qty-start">
                    </div>

                    <div class="slider-div">
                        <label>Month End:&nbsp;&nbsp;<span id="slider-qty-end-val"></span></label>
                        <input type="range" min="1" max="12" value="12" class="slider" id="slider-qty-end">
                    </div>

                    

                    <div class="text-right">
                        <a href="" class="btn btn-info btn-sm slider-broiler-btn">Apply</a>
                    </div>
                    
                </div>
            </div>
        </div>
    </div>
</div>