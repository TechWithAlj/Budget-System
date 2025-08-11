//ADDITIONAL CODES
function calculateSum(class_name, initial_range, end_range) {
	var totalSum = 0;
	//iterate through each textboxes and add the values
	$(class_name).each(function () {
		//add only if the value is number
		if (!isNaN(this.value) && this.value.length != 0) {
			totalSum += parseFloat(this.value);
		}
	});
	var monthlyTotal = [];
	var flag = [];
	for (var i=initial_range; i<=end_range; i++) {
		
		monthlyTotal[i] = 0;
		$('td:nth-child('+(i+1)+')').find(class_name).each(function () {
			if (!isNaN(this.value) && this.value.length != 0) {
				monthlyTotal[i] += parseFloat(this.value);
			}
		});
		if(monthlyTotal[i].toFixed(2) <= 0){
			$(".span7").find('.total').eq(i-1).html('')
			//$(".save").attr('disabled', true);
		} else {
			$(".span7").find('.total').eq(i-1).html(monthlyTotal[i].toFixed(2));
			if(class_name == '.txt'){
				if(monthlyTotal[i].toFixed(2) != 100){
					flag[i] = true;
				} else {
					flag[i] = false;
				}
			} else if(class_name == '.edit_rate'){
				if(monthlyTotal[i].toFixed(2) > 100){
					flag[i] = true;
				} else {
					flag[i] = false;
				}
			}
		}
	}
	
	var check_index = flag.indexOf(true)
	if(check_index != -1){
		$(".total").css("color", "red");
		$(".save").attr('disabled', true);
	} else {
		$(".total").css("color", "black");
		$(".save").removeAttr('disabled');
	}
}

$(document).ready(function(){
	
	var base_url = $('#base_url').val();

	$(document).on('paste', 'input.broiler-cost-input', function(e){
		 $this = $(this);

	    setTimeout(function(){
	        var columns = $this.val().trim().replace(/,/g, '').split(/\s+/);
	        var i;
	      	var input =  $this;

	        for(i=0	; i < columns.length; i++){

	            input.val(columns[i]);
	            input = input.parent().next().find('input');
	        }
	    }, 0);
	});

	$('#tbl-broiler-group').DataTable({
		"bInfo" : false,
		"order": []
	});

	$('#tbl-broiler-config').DataTable({
		"bInfo" : false,
		"order": []
	});

	$('#tbl-view-broiler-config').DataTable({
		"bInfo" : false,
		"order": []
	});

	$('#tbl-add-broiler-config').DataTable({
		
		"autoWidth": false,
		"scrollX": true,
		"bInfo" : false,
		"paging" : false,
	});

	for (var i = 1 - 1; i <= 12; i++) {
		$('#tbl-view-cost-sheet-'+i).DataTable({
			
			"order": [],
			"autoWidth": false,
			"scrollY": "500px",
			"scrollX": true,
			"bInfo" : false,
			"paging" : false,
		});
	}

	var tbl_transac_prod = $('#tbl-new-prod-transaction').DataTable({
		
		"bInfo" : false,
		"order": [],
		"paging" : false
	});
	$("#here").css({"background-color": "white", "color": "black"});

	$(document).on('click', '.add-broiler-config-item', function(e){
		e.preventDefault();

		$(this).parent().parent().after('<tr><td><a href="#" class="remove-asg"><i class="fa fa-remove"></i></a>&nbsp;&nbsp;&nbsp;<a href="" class="add-broiler-config-item"><i class="fa fa-plus"></i></a></td><td><input type="" name="" class="form-control input-sm" size="40"></td><td><input type="" name="" class="form-control input-sm" size="10"></td><td><input type="" name="" class="form-control input-sm" size="10"></td><td><input type="" name="" class="form-control input-sm" size="10"></td><td><input type="" name="" class="form-control input-sm" size="10"></td><td><input type="" name="" class="form-control input-sm" size="10"></td><td><input type="" name="" class="form-control input-sm" size="10"></td><td><input type="" name="" class="form-control input-sm" size="10"></td><td><input type="" name="" class="form-control input-sm" size="10"></td><td><input type="" name="" class="form-control input-sm" size="10"></td><td><input type="" name="" class="form-control input-sm" size="10"></td><td><input type="" name="" class="form-control input-sm" size="10"></td><td><input type="" name="" class="form-control input-sm" size="10"></td></tr>');
	});

	var tbl_transac_broiler= $('#tbl-new-broiler-transaction').DataTable({
		
		"autoWidth": false,
		"scrollX": true,
		"bInfo" : false,
		"paging" : false,
	});

	$(document).on('change', '#broiler_group', function(){
		var id = $(this).val();
		var bc_id = $('#bc_id').val();
		$.ajax({
	    	url: base_url + 'ahg/get-broiler-subgroup',
	    	data: {id:id, bc_id:bc_id},
	    	method: 'POST',
	    	success:function(response){
	    		
	    		var parse_response = JSON.parse(response);

	    		if(parse_response['result'] == 1){
	    			tbl_transac_broiler.destroy();
	    			$("#tbl-new-broiler-transaction > tbody").empty();
		    		if(parse_response['broiler_group'] == 'No data'){
		    			tbl_transac_broiler = $('#tbl-new-broiler-transaction').DataTable({
							"bInfo" : false,
							"order": [],
							"paging" : false,
						});
		    		} else {
		    			$("#tbl-new-broiler-transaction > tbody").append(parse_response['broiler_group']);
		    			tbl_transac_broiler = $('#tbl-new-broiler-transaction').DataTable({
							"scrollX": true,
							"autoWidth": true,
							"bInfo" : false,
							"paging" : false,
						});
		    		}
		    	}else{
		    		console.log('Error please contact your administrator');
		    	}
	    	}
		});
	});

	$(document).on('change', '#config_prod', function(){
		var id = $(this).val();
		var bc_id = $('#bc_id').val();
		var process_selected = $("#config_prod option:selected").text();
		var process_selected = process_selected.split('-');
		$.ajax({
	    	url: base_url + 'ahg/get-config-prod',
	    	data: {id:id, bc_id : bc_id, process_type_name : process_selected[1]},
	    	method: 'POST',
	    	success:function(response){
	    		/*console.log(response);
	    		return;*/
	    		var parse_response = JSON.parse(response);
	    		
	    		if(parse_response['result'] == 1){
	    			tbl_transac_prod.destroy();
	    			$("#tbl-new-prod-transaction > tbody").empty();
		    		if(parse_response['config_prod'] == 'No data'){
		    			if(process_selected[1].trim() == 'CLASSIFICATION'){
		    				for (var i = 1; i <= 12; i++) {
		    					$('#tbl-new-prod-transaction').find('#dynamic_hdr-'+i).html('Avg. Wgt.');	
		    				}
		    			} else {
		    				for (var i = 1; i <= 12; i++) {
		    					$('#tbl-new-prod-transaction').find('#dynamic_hdr-'+i).html('Cost/Price');	
		    				}
		    			}
		    			tbl_transac_prod = $('#tbl-new-prod-transaction').DataTable({
							"bInfo" : false,
							"order": [],
							"paging" : false,
						});
		    		} else {

		    			if(process_selected[1].trim() == 'CLASSIFICATION'){
		    				for (var i = 1; i <= 12; i++) {
		    					$('#tbl-new-prod-transaction').find('#dynamic_hdr-'+i).html('Avg. Wgt.');	
		    				}
		    			} else {
		    				for (var i = 1; i <= 12; i++) {
		    					$('#tbl-new-prod-transaction').find('#dynamic_hdr-'+i).html('Cost/Price');	
		    				}
		    			}
		    			$("#tbl-new-prod-transaction > tbody").append(parse_response['config_prod']);
		    			tbl_transac_prod = $('#tbl-new-prod-transaction').DataTable({
		    				"order": [],
							"scrollX": true,
							"autoWidth": true,
							"bInfo" : false,
							"paging" : false,
							"scrollY":        "550px",
					        "scrollCollapse": true,
							fixedColumns:   {
					          leftColumns: 2
					        }
						});
		    			$(".save").removeAttr('disabled');
						$("#here").css({"background-color": "white", "color": "black"});
						//iterate through each textboxes and add keyup
						//handler to trigger sum event
						if(process_selected[1].trim() == 'CLASSIFICATION'){
							$(".txt").each(function () {
								$(this).keyup(function () {
									calculateSum('.txt', 4, 27);
								});
							});
						}
		    		}
		    	}else{
		    		console.log('Error please contact your administrator');
		    	}
	    	}
		});
	});

	$(".edit_rate").each(function () {
		$(this).keyup(function () {
			calculateSum('.edit_rate', 3, 26);
		});
	});

	$('#tbl-view-broiler-transaction').DataTable({
		
		"autoWidth": false,
		"scrollX": true,
		"bInfo" : false,
		"paging" : false
	});

	$('#tbl-view-broiler-result').DataTable({
		
		"autoWidth": false,
		"scrollX": true,
		"bInfo" : false,
		"paging" : false,
		"order": false
	});

	var tbl_broiler_trans_2 = $('#tbl-broiler-trans').DataTable({
		"bInfo" : false,
		"order": []
	});

	var tbl_prod_trans = $('#tbl-prod-trans').DataTable({
		"bInfo" : false,
		"order": []
	});

	$('#broiler-trans-year').datepicker({
	    autoclose: true,
	    format: 'yyyy',
	    viewMode: "years",
		minViewMode: "years",
		startDate: '01/2015',
	}).on('changeDate',function(e){
		
		var year = $('#broiler-trans-date-pick-year').val();
		var bc_id = $('#bc_id').val();

		$.ajax({
	    	url: base_url + 'ahg/get-broiler-trans',
	    	data: {broiler_trans_date:year, bc_id:bc_id},
	    	method: 'POST',
	    	success:function(response){
	    		var parse_response = JSON.parse(response);
	    		if(parse_response['result'] == 1){

	    			tbl_broiler_trans_2.destroy();
	    			$("#tbl-broiler-trans > tbody").empty();
		    		$("#tbl-broiler-trans > tbody").append(parse_response['broiler_trans']);
		    		tbl_broiler_trans_2 = $('#tbl-broiler-trans').DataTable({
						"bInfo" : false,
						"order": []
					});
		    	}else{
		    		console.log('Error please contact your administrator');
		    	}
	    	}
		});
	});

	$('#prod-trans-year').datepicker({
	    autoclose: true,
	    format: 'yyyy',
	    viewMode: "years",
		minViewMode: "years",
		startDate: '01/2015',
	}).on('changeDate',function(e){
		
		var year = $('#prod-trans-date-pick-year').val();
		var bc_id = $('#bc_id').val();
		/*alert(bc_id);
		return;*/

		$.ajax({
	    	url: base_url + 'ahg/get-prod-trans',
	    	data: {prod_trans_date:year, bc_id:bc_id},
	    	method: 'POST',
	    	success:function(response){
	    		/*console.log(response);
	    		return;*/
	    		var parse_response = JSON.parse(response);

	    		if(parse_response['result'] == 1){

	    			tbl_prod_trans.destroy();
	    			$("#tbl-prod-trans > tbody").empty();
		    		$("#tbl-prod-trans > tbody").append(parse_response['prod_trans']);
		    		tbl_prod_trans = $('#tbl-prod-trans').DataTable({
						"bInfo" : false,
						"order": []
					});
		    	}else{
		    		console.log('Error please contact your administrator');
		    	}
	    	}
		});
	});

	$(document).on('click', '.post-broiler-trans',function(e){
		e.preventDefault();
		var broiler_trans_id = $(this).attr('data-id');
		var bc_id = $(this).attr('data-bc');
		var broiler_group_id = $(this).attr('data-bgid');
		var broiler_group_name = $(this).attr('data-bgname');
		var trans_year = $(this).attr('data-transyear');

		
		$('#post-broiler-trans').find('#broiler_trans_id').val(broiler_trans_id);
		$('#post-broiler-trans').find('#bc_id').val(bc_id);
		$('#post-broiler-trans').find('#broiler_group_id').val(broiler_group_id);
		$('#post-broiler-trans').find('#broiler_group_name').val(broiler_group_name);
		$('#post-broiler-trans').find('#trans_year').val(trans_year);
		$('#post-broiler-trans').find('#broiler_trans_status').val('post');
		$('#post-broiler-trans').find('#modal-msg').html('Are you sure to post this transaction details?');
		$('#modal-confirm').modal({show:true});
	});

	$(document).on('click', '.remove-config-prod-dtl',function(e){
		e.preventDefault();
		var config_prod_dtl_id = $(this).attr('data-id');
		var material_desc = $(this).attr('data-mat_desc');
		var config_prod_id = $(this).attr('data-config_prod_id');

		
		$('#remove-config-prod-dtl').find('#config_prod_dtl_id').val(config_prod_dtl_id);
		$('#remove-config-prod-dtl').find('#material_desc').val(material_desc);
		$('#remove-config-prod-dtl').find('#config_prod_id').val(config_prod_id);
		$('#remove-config-prod-dtl').find('#modal-msg').html('Are you sure to remove this config item?');
		$('#remove-config-prod-dtl').find('#trans_status').val('remove');
		$('#modal-confirm').modal({show:true});
	});

	$(document).on('click', '.remove-prod-trans',function(e){
		e.preventDefault();
		var prod_trans_id = $(this).attr('data-id');
		var material_desc = $(this).attr('data-mat_desc');
		var bc_id = $(this).attr('data-bc_id');

		
		$('#remove-prod-trans').find('#prod_trans_id').val(prod_trans_id);
		$('#remove-prod-trans').find('#material_desc').val(material_desc);
		$('#remove-prod-trans').find('#bc_id').val(bc_id);
		$('#remove-prod-trans').find('#modal-msg').html('Are you sure to cancel this transaction?');
		$('#remove-prod-trans').find('#trans_status').val('cancel');
		$('#modal-confirm').modal({show:true});
	});


	$(document).on('click', '.cancel-broiler-trans',function(e){
		e.preventDefault();
		var broiler_trans_id = $(this).attr('data-id');
		var bc_id = $(this).attr('data-bc');
		var broiler_group_id = $(this).attr('data-bgid');
		var broiler_group_name = $(this).attr('data-bgname');
		var trans_year = $(this).attr('data-transyear');
		
		$('#post-broiler-trans').find('#broiler_trans_id').val(broiler_trans_id);
		$('#post-broiler-trans').find('#bc_id').val(bc_id);
		$('#post-broiler-trans').find('#broiler_group_id').val(broiler_group_id);
		$('#post-broiler-trans').find('#broiler_group_name').val(broiler_group_name);
		$('#post-broiler-trans').find('#trans_year').val(trans_year);
		$('#post-broiler-trans').find('#broiler_trans_status').val('cancel');
		$('#post-broiler-trans').find('#modal-msg').html('Are you sure to cancel this transaction details?');
		$('#modal-confirm').modal({show:true});
	});

	$(document).on('change', '#type_id', function(e){
		e.preventDefault();
		var id = $(this).val();
		$.ajax({
	    	url: base_url + 'ahg/get-brand/',
	    	data:{id:id},
	    	method: 'POST',
	    	success:function(response){
	    		var parse_response = JSON.parse(response);
	    		
	    		if(parse_response['result'] == 1){
	    			$('#brand_id').empty();
	    			$('#brand_id').append(parse_response['info']);
	    			
	    			/*$('#brand_id').selectize()[0].selectize.destroy();
	    			$('#brand_id').empty();
	    			$('#brand_id').append(parse_response['info']);

	    			$('#brand_id').selectize({
				        maxItems: 100,
				        valueField: 'id',
				        labelField: 'title',
				        searchField: 'title',
				        create: false
				    });*/
		    	}else{
		    		console.log('Error please contact your administrator.');
		    	}
	    	}
		});
	});

	$('#config_prod').selectize({
        maxItems: 1,
        valueField: 'id',
        labelField: 'title',
        searchField: 'title',
        create: false
    });

	$('#prod_id').selectize({
        maxItems: 1,
        valueField: 'id',
        labelField: 'title',
        searchField: 'title',
        create: false
    });

	$('#article_id').selectize({
        maxItems: 100,
        valueField: 'id',
        labelField: 'title',
        searchField: 'title',
        create: false
    });

	$(document).on('change', '#component_type_id', function(e){
		e.preventDefault();
		var id = $(this).val();
		$.ajax({
	    	url: base_url + 'ahg/get-material/',
	    	data:{id:id},
	    	method: 'POST',
	    	success:function(response){
	    		var parse_response = JSON.parse(response);
	    		/*console.log(parse_response);
	    		return;*/
	    		if(parse_response['result'] == 1){
	    			//$('#article_id').empty();
	    			//$('#article_id').append(parse_response['info']);
	    			
	    			$('#article_id').selectize()[0].selectize.destroy();
	    			$('#article_id').empty();
	    			$('#article_id').append(parse_response['info']);

	    			$('#article_id').selectize({
				        maxItems: 100,
				        valueField: 'id',
				        labelField: 'title',
				        searchField: 'title',
				        create: false
				    });
		    	}else{
		    		console.log('Error please contact your administrator.');
		    	}
	    	}
		});
	});

	$('#article_id_svc').selectize({
        maxItems: 100,
        valueField: 'id',
        labelField: 'title',
        searchField: 'title',
        create: false
    });

    $(document).on('change', '#component_type_id_svc', function(e){
		e.preventDefault();
		var id = $(this).val();

		$.ajax({
	    	url: base_url + 'ahg/get-services/',
	    	data:{id:id},
	    	method: 'POST',
	    	success:function(response){
	    		/*console.log(parse_response);
	    		return;*/
	    		var parse_response = JSON.parse(response);
	    		if(parse_response['result'] == 1){
	    			//$('#article_id').empty();
	    			//$('#article_id').append(parse_response['info']);
	    			
	    			$('#article_id_svc').selectize()[0].selectize.destroy();
	    			$('#article_id_svc').empty();
	    			$('#article_id_svc').append(parse_response['info']);

	    			$('#article_id_svc').selectize({
				        maxItems: 100,
				        valueField: 'id',
				        labelField: 'title',
				        searchField: 'title',
				        create: false
				    });
		    	} else if(parse_response['result'] == 0){
		    		$('#article_id_svc').selectize()[0].selectize.destroy();
	    			$('#article_id_svc').empty();
		    	} else{
		    		console.log('Error please contact your administrator.');
		    	}
	    	}
		});
	});

	$('#tbl-cost-sheet-transaction').DataTable({
		
		"autoWidth": false,
		"scrollX": true,
		"bInfo" : false,
		"paging" : false,
	});
	/*my code ends here*/
});