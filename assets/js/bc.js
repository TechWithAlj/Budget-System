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
	function number_format (number, decimals, dec_point, thousands_sep) {
	    // Strip all characters but numerical ones.
	    number = (number + '').replace(/[^0-9+\-Ee.]/g, '');
	    var n = !isFinite(+number) ? 0 : +number,
	        prec = !isFinite(+decimals) ? 0 : Math.abs(decimals),
	        sep = (typeof thousands_sep === 'undefined') ? ',' : thousands_sep,
	        dec = (typeof dec_point === 'undefined') ? '.' : dec_point,
	        s = '',
	        toFixedFix = function (n, prec) {
	            var k = Math.pow(10, prec);
	            return '' + Math.round(n * k) / k;
	        };
	    // Fix for IE parseFloat(0.55).toFixed(0) = 0;
	    s = (prec ? toFixedFix(n, prec) : '' + Math.round(n)).split('.');
	    if (s[0].length > 3) {
	        s[0] = s[0].replace(/\B(?=(?:\d{3})+(?!\d))/g, sep);
	    }
	    if ((s[1] || '').length < prec) {
	        s[1] = s[1] || '';
	        s[1] += new Array(prec - s[1].length + 1).join('0');
	    }
	    return s.join(dec);
	}

	$('#tbl-purchase').DataTable({
		"bInfo" : false,
		"order": []
	});

	$('#tbl-material').DataTable({
		"bInfo" : false,
		"order": []
	});

	$('#tbl-business').DataTable({
		"bInfo" : false,
		"order": []
	});

	$('#tbl-warehouse').DataTable({
		"bInfo" : false,
		"order": []
	});

	$('#tbl-category').DataTable({
		"bInfo" : false,
		"order": []
	});
	
	$('#tbl-users').DataTable({
		"bInfo" : false,
		"order": []
	});

	$('#tbl-po').DataTable({
		"bInfo" : false,
		"order": []
	});

	$('#tbl-per-material').DataTable({
		"bInfo" : false,
		"order": []
	});

	$('#tbl-received-po').DataTable({
		"bInfo" : false,
		"paging" : false,
		"order": []
	});

	$('#tbl-per-bc').DataTable({
		"bInfo" : false,
		"order": []
	});

	$('#tbl-remarks').DataTable({
		"bInfo" : false,
		"order": []
	});

	$('#tbl-history-po').DataTable({
		"bInfo" : false,
		"paging": false,
		"order": []
	});

	$('#tbl-remarks-po').DataTable({
		"bInfo" : false,
		"order": []
	});

	var tbl_budget = $('#tbl-budget').DataTable({
        "bInfo" : false,
        "paging": false,
		"order": [],
		"scrollX": true,
		"scrollY": "300px",
		/*tbl-sales*/
	});

	$('#tbl-sales').DataTable({
		"bInfo" : false,
		"order": []
	});
	$('#tbl-vmaterial').DataTable({
		"bInfo" : false,
		"order": []
	});

	$('#tbl-asset-subgroup').DataTable({
		"bInfo" : false,
		"order": []
	});

	$('#tbl-cost-center').DataTable({
		"bInfo" : false,
		"order": []
	});

	$('#tbl-gl-subgroup').DataTable({
		"bInfo" : false,
		"order": []
	});

	$('#tbl-gl-group').DataTable({
		"bInfo" : false,
		"order": []
	});

	$('#tbl-user-unit').DataTable({
		"bInfo" : false,
		"order": []
	});

	$('#tbl-user-bc').DataTable({
		"bInfo" : false,
		"order": []
	});
	

	var base_url = $('#base_url').val();

	$(document).on('click', '.reset-user', function(e){
		e.preventDefault();

		var user_id = $(this).attr('data-id');
		$('#update-password').find('#id').val(user_id);
		$('#modal-reset-user').modal({show:true});
		$("#update-password").bootstrapValidator('resetForm');
	});

	$("#update-password").bootstrapValidator({
		framework: 'bootstrap',
		excluded: [':disabled'],
		fields:{
			password: {
				validators: {
					notEmpty: {
						message: 'Password is required!'
					},

					identical: {
						field: 'password2',
						message: 'Password not match!'
					}
				}
			},

			password2: {
				validators: {
					notEmpty: {
						message: 'Retype password is required!'
					},

					identical: {
						field: 'password',
						message: 'Password not match!'
					}
				}
			}
		}
	});


	//measurement
	$(document).on('click', '.edit-measurement', function(e){
		e.preventDefault();

		var id = $(this).attr('data-id');

		$.ajax({
	    	url: base_url + 'business-center/modal-measurement/' + id,
	    	method: 'GET',
	    	success:function(response){
	    		var parse_response = JSON.parse(response);
	    		
	    		if(parse_response['result'] == 1){
	    			$('#update-measurement').find('#id').val(parse_response['info'].id);
		    		$('#update-measurement').find('#measurement').val(parse_response['info'].measurement);
		    		$('#modal-edit-measurement').modal({show:true});
		    		$('#update-measurement').bootstrapValidator('resetForm');
		    	}else{
		    		console.log('Error please contact your administrator.');
		    	}
	    	}
		});
	});

	$("#add-measurement").bootstrapValidator({
		framework: 'bootstrap',
		excluded: [':disabled'],
		fields:{
			measurement: {
				validators: {
					notEmpty: {
						message: 'Measurement is required!'
					}
				}
			}
		}
	});

	$("#update-measurement").bootstrapValidator({
		framework: 'bootstrap',
		excluded: [':disabled'],
		fields:{
			measurement: {
				validators: {
					notEmpty: {
						message: 'Measurement is required!'
					}
				}
			}
		}
	});


	/*Brand BC Material*/

    $('#tbl-brand-bc-info').DataTable({
		"order": []
	});

	$('#brand-bc-trans-year').datepicker({
	    autoclose: true,
	    format: 'yyyy',
	    viewMode: "years",
		minViewMode: "years",
	});

	$(document).on('change', '#brand-bc-trans-year', function(e){
		var year = $('#brand-bc-year').val();
		var bc_id = $('#bc_id').val();

		var url = base_url + 'business-center/brand-bc-info/' + year;
    	window.location = url;
	});


	$('#tbl-brand-bc-material').DataTable({
		"order": []
	});

	$(document).on('click', '.remove-brand-bc-material',function(e){
		e.preventDefault();
		var id = $(this).attr('data-id');
		$('#remove-brand-bc-material').find('#id').val(id);
		$('#modal-confirm').modal({show:true});
	});


	/*Materials*/

	$(document).on('click', '.edit-material', function(e){
		e.preventDefault();

		var id = $(this).attr('data-id');

		$.ajax({
	    	url: base_url + 'business-center/modal-material/' + id,
	    	method: 'GET',
	    	success:function(response){
	    		var parse_response = JSON.parse(response);
	    		
	    		if(parse_response['result'] == 1){
	    			$('#update-material').find('#id').val(id);
		    		$('#update-material').find('#material-code').val(parse_response['info'].material_code);
		    		console.log(parse_response['info'].material_code);
		    		$('#update-material').find('#description').val(parse_response['info'].material_desc);
		    		$('#update-material').find('#group').empty();
		    		$('#update-material').find('#group').append(parse_response['info'].group);
		    		$('#update-material').find('#vat').empty();
		    		$('#update-material').find('#vat').append(parse_response['info'].vat);
		    		$('#update-material').find('#base-unit').empty();
		    		$('#update-material').find('#base-unit').append(parse_response['info'].base_unit);
		    		
		    		$('#update-material').find('#valuation-unit').append(parse_response['info'].valuation_unit);
		    		
		    		$('#update-material').find('#valuation-basis').val(parse_response['info'].valuation_basis);
		    		
		    		$('#update-material').find('#equivalent-unit').val(parse_response['info'].sales_equivalent);
		    		
		    		$('#modal-edit-material').modal({show:true});
		    		$('#update-material').bootstrapValidator('resetForm');
		    	}else{
		    		console.log('Error please contact your administrator.');
		    	}
	    	}
		});
	});

	$(document).on('keyup', '[name="qty[]"]', function(){
    	var received = $(this).val();

    	if($.isNumeric(received)){
    		if(Math.floor(received) > 0){
    			
	    		$(this).parent('.form-group').removeClass('has-feedback');
	    		$(this).parent('.form-group').removeClass('has-success');
	    		$(this).parent('.form-group').removeClass('has-error');
	    		$(this).parent('.form-group').addClass('has-feedback');
	    		$(this).parent('.form-group').addClass('has-success');

	    		$('#month').parent('.date').parent('.form-group').addClass('has-feedback');
	    		$('#month').parent('.date').parent('.form-group').addClass('has-success');
	    	}else{
	    		$(this).parent('.form-group').addClass('has-feedback');
    			$(this).parent('.form-group').addClass('has-error');
	    	}
    	}else{
    		$(this).parent('.form-group').addClass('has-feedback');
    		$(this).parent('.form-group').addClass('has-error');
    	}
    	check_form();
    });

	//outlet

	$('#outlet-trans-year').datepicker({
	    autoclose: true,
	    format: 'yyyy',
	    viewMode: "years",
		minViewMode: "years",
	});

	$(document).on('change', '#outlet-trans-year', function(e){
		var year = $('#outlet-year').val();
		
		var url = base_url + 'business-center/outlets/' + year;
    	window.location = url;
	});

	$('#tbl-budgeted').DataTable({
		"bInfo" : false,
		"order": []
	});

	$('#tbl-unbudgeted').DataTable({
		"bInfo" : false,
		"order": []
	});
		
	$('#tbl-sales-view').DataTable({
		"scrollX": true,
		"scrollY": "300px",
		"fixedHeader": true,
		"bInfo": false,
		"paging": false,
	});

	$('#sales-trans-year').datepicker({
	    autoclose: true,
	    format: 'yyyy',
	    viewMode: "years",
		minViewMode: "years",
	});

	$(document).on('change', '#sales-trans-year', function(e){
		var year = $('#sales-year').val();

		var url = base_url + 'business-center/sales-info/' + year;
    	window.location = url;
	});

	$('#status, #bc, #brand').change(function(){
		var brand = $('#brand').val();
		if(brand !== ''){
			$.ajax({
		    	url: base_url + 'business-center/get-new-outlet/' + brand,
		    	method: 'GET',
		    	success:function(response){
		    		var parse_response = JSON.parse(response);
		    		if(parse_response['result'] == 1){
		    			var format = parse_response['info'];
		    			$('#outlet').val(format);
		    			$('#outlet').attr('readonly', true);

		    			$('#ifs').val(format);
						$('#ifs').attr('readonly', true);

						$('#add-outlet').bootstrapValidator('revalidateField', 'outlet');
						$('#add-outlet').bootstrapValidator('revalidateField', 'ifs');

			    	}else{
			    		console.log('Error please contact your administrator');
			    	}
		    	}
			});
		}
	});

	$(document).on('click', '.remove-sales-item', function(e){
		e.preventDefault();
		var id = $(this).attr('data-id');
		$('#remove-sales-item').find('#id').val(id);
		$('#modal-remove-sales-item').modal('show');
	});

	$(document).on('click', '.update-sales-item', function(e){
		e.preventDefault();
		var id = $(this).attr('data-id');
		$.ajax({
	    	url: base_url + 'business-center/get-sales-item/',
	    	data: {id:id},
	    	method: 'POST',
	    	success:function(response){
	    		var parse_response = JSON.parse(response);
	    		if(parse_response['result'] == 1){
	    			var code = parse_response['info']['code'];
	    			var desc = parse_response['info']['desc'];
	    			var qty = parse_response['info']['qty'];
	    			var asp = parse_response['info']['asp'];

	    			var jan_qty = qty['January'];
	    			var jan_asp = asp['January'];

	    			var feb_qty = qty['February'];
	    			var feb_asp= asp['February'];

	    			var mar_qty = qty['March'];
	    			var mar_asp = asp['March'];

	    			var apr_qty = qty['April'];
	    			var apr_asp = asp['April'];

	    			var may_qty = qty['May'];
	    			var may_asp = asp['May'];

	    			var jun_qty = qty['June'];
	    			var jun_asp = asp['June'];

	    			var jul_qty = qty['July'];
	    			var jul_asp = asp['July'];

	    			var aug_qty = qty['August'];	
	    			var aug_asp = asp['August'];

	    			var sep_qty = qty['September'];
	    			var sep_asp = asp['September'];

	    			var oct_qty = qty['October'];
	    			var oct_asp = asp['October'];

	    			var nov_qty = qty['November'];
	    			var nov_asp = asp['November'];

	    			var dec_qty = qty['December'];
	    			var dec_asp = asp['December'];

	    			$('.jan-qty').val(jan_qty);
	    			$('.jan-asp').val(jan_asp);

	    			$('.feb-qty').val(feb_qty);
	    			$('.feb-asp').val(feb_asp);

	    			$('.mar-qty').val(mar_qty);
	    			$('.mar-asp').val(mar_asp);

	    			$('.apr-qty').val(apr_qty);
	    			$('.apr-asp').val(apr_asp);

	    			$('.may-qty').val(may_qty);
	    			$('.may-asp').val(may_asp);

	    			$('.jun-qty').val(jun_qty);
	    			$('.jun-asp').val(jun_asp);

	    			$('.jul-qty').val(jul_qty);
	    			$('.jul-asp').val(jul_asp);

	    			$('.aug-qty').val(aug_qty);
	    			$('.aug-asp').val(aug_asp);

	    			$('.sep-qty').val(sep_qty);
	    			$('.sep-asp').val(sep_asp);

	    			$('.oct-qty').val(oct_qty);
	    			$('.oct-asp').val(oct_asp);

	    			$('.nov-qty').val(nov_qty);
	    			$('.nov-asp').val(nov_asp);

	    			$('.dec-qty').val(dec_qty);
	    			$('.dec-asp').val(dec_asp);

	    			$('#update-sales-item > #id').empty();
	    			$('#update-sales-item > #id').val(id);

	    			$('#label-code > .val').empty();
	    			$('#label-code > .val').text(code);

	    			$('#label-desc > .val').empty();
	    			$('#label-desc > .val').text(desc);

		    	}else{
		    		console.log('Error please contact your administrator');
		    	}
	    	}
		});

		$('#modal-update-sales-item').modal('show');
	});

	$(document).on('change', '#brand-templates', function(){
		var bc_id = $('#bc_id').val();
		var brand_id = $(this).val();
		var year = $('#sales-year').val();
		var url = base_url + 'business-center/download-sales-templates/' + bc_id + '/' + brand_id + '/' + year;
		$('#templates-link').attr('href', url);
	});

	$(document).on('click', '.cancel-sales-btn', function(e){
		e.preventDefault();
		var id = $(this).attr('data-id');
		$('#cancel-sales').find('#id').val(id);
		$('#modal-cancel-sales').modal('show');
	});

	$(document).on('click', '.remove-material-item', function(e){
		e.preventDefault();
		var num_row = $(this).parent().attr('data-dt-row');
		$(this).parent().parent().remove();
		console.log((parseInt(num_row)) + 1);
		$('.row-' + (parseInt(num_row) + 1)).remove();
	});

	$(document).on('keyup', '.update-item-qty', function(){
		var val = $(this).val();
		if($.isNumeric(val) || val == ''){
			if(Math.floor(val) >= 0){
				
				$(this).removeClass('error-encode');
				$(this).addClass('success-encode');

				var check = validate_sales_update_qty();
				if(check){
					$(".btn-update-sales").removeAttr('disabled', true);
				}
				console.log('ok');
			}else{
				$(".btn-update-sales").attr('disabled', true);
				$(this).removeClass('success-encode');
				$(this).addClass('error-encode');
			}
		}else{
			$(".btn-update-sales").attr('disabled', true);
			$(this).removeClass('success-encode');
			$(this).addClass('error-encode');
		}
	});

	$(document).on('paste', 'input.sales-qty', function(e){
		 $this = $(this);

	    setTimeout(function(){
	        var columns = $this.val().trim().replace(/,/g, '').split(/\s+/);
	        var i;
	      	var input =  $this;

	        for(i=0	; i < columns.length; i++){

	            input.val(columns[i]);
	            input = input.parent().parent().next().find('input');
	        }
	    }, 0);
	});

	$(document).on('keyup', '.sales-qty', function(){
		var val = $(this).val();
		var total_qty = 0;
		if($.isNumeric(val) || val == ''){
			if(Math.floor(val) >= 0){
				
				$(this).removeClass('error-encode');
				$(this).addClass('success-encode');

				total_qty += parseFloat($(this).parent().parent().parent().find('.budget-qty-jan').val()) || 0;
				total_qty += parseFloat($(this).parent().parent().parent().find('.budget-qty-feb').val()) || 0;
				total_qty += parseFloat($(this).parent().parent().parent().find('.budget-qty-mar').val()) || 0;
				total_qty += parseFloat($(this).parent().parent().parent().find('.budget-qty-apr').val()) || 0;
				total_qty += parseFloat($(this).parent().parent().parent().find('.budget-qty-may').val()) || 0;
				total_qty += parseFloat($(this).parent().parent().parent().find('.budget-qty-jun').val()) || 0;
				total_qty += parseFloat($(this).parent().parent().parent().find('.budget-qty-jul').val()) || 0;
				total_qty += parseFloat($(this).parent().parent().parent().find('.budget-qty-aug').val()) || 0;
				total_qty += parseFloat($(this).parent().parent().parent().find('.budget-qty-sep').val()) || 0;
				total_qty += parseFloat($(this).parent().parent().parent().find('.budget-qty-oct').val()) || 0;
				total_qty += parseFloat($(this).parent().parent().parent().find('.budget-qty-nov').val()) || 0;
				total_qty += parseFloat($(this).parent().parent().parent().find('.budget-qty-dec').val()) || 0;

				$(this).parent().parent().parent().find('.sales-qty-total').text(total_qty);
				console.log(total_qty);
				var check_qty = validate_sales_qty();
				var check_asp = validate_sales_asp();

				if(check_qty && check_asp){
					$(".btn-add-sales").removeAttr('disabled', true);
				}
			}else{
				//$(".btn-add-sales").attr('disabled', true);
				$(this).removeClass('success-encode');
				$(this).addClass('error-encode');
			}
		}else{
			//$(".btn-add-sales").attr('disabled', true);
			$(this).removeClass('success-encode');
			$(this).addClass('error-encode');
		}
	});

	$(document).on('keyup', '.sales-asp', function(){
		var val = $(this).val();
		if($.isNumeric(val) || val == ''){
			if(Math.floor(val) >= 0){
				
				$(this).removeClass('error-encode');
				$(this).addClass('success-encode');

				var check_qty = validate_sales_qty();
				var check_asp = validate_sales_asp();
				
				if(check_qty && check_asp){
					$(".btn-add-sales").removeAttr('disabled', true);
				}else{
					//$(".btn-add-sales").attr('disabled', true);
				}

			}else{
				//$(".btn-add-sales").attr('disabled', true);
				$(this).removeClass('success-encode');
				$(this).addClass('error-encode');
			}
		}else{
			//$(".btn-add-sales").attr('disabled', true);
			$(this).removeClass('success-encode');
			$(this).addClass('error-encode');
		}
	});

	$('#add-sales').submit(function(e){
		
		var check_qty = validate_sales_qty();
		var check_asp = validate_sales_asp();
		if(check_qty && check_asp){
			var check_modal = ($("#modal-confirm-sales").data('bs.modal') || {}).isShown;
			if(!check_modal){
				e.preventDefault();
				$('#modal-confirm-sales').modal('show');
			}
		}else{
			$('#modal-confirm-error').modal('show');
			e.preventDefault();
		}
	});

	function validate_sales_qty(){
    	var a = 0;
    	var btn_counter = 0;
    	$('input[name^="budget_qty"]').each(function(){
    		var qty = $(this).val();
    		a += 1;
    		if($.isNumeric(qty) || qty == ''){
	    		if(Math.floor(qty) >= 0){
		    		btn_counter += 1;
		    	}
	    	}else{
	    		
	    	}
    	});

    	if(btn_counter < a){
    		return false;
    	}else{
    		return true;
    	}
    }

    function validate_sales_asp(){
    	var a = 0;
    	var btn_counter = 0;
    	$('input[name^="asp"]').each(function(){
    		var qty = $(this).val();
    		a += 1;
    		if($.isNumeric(qty) || qty == ''){
	    		if(Math.floor(qty) >= 0){
		    		btn_counter += 1;
		    	}
	    	}else{
	    		
	    	}
    	});

    	if(btn_counter < a){
    		return false;
    	}else{
    		return true;
    	}
    }

	$('#update-sales-item').submit(function(e){
		var check_qty = validate_sales_update_qty();
		//var check_asp = validate_sales_update_asp();
		
		if(check_qty){

		}else{
			$(".btn-update-sales").attr('disabled', true);
			e.preventDefault();
		}
	});

	function validate_sales_update_qty(){
    	var a = 0;
    	var btn_counter = 0;
    	$('input[name^="qty"]').each(function(){
    		var qty = $(this).val();
    		a += 1;
    		if($.isNumeric(qty) || qty == ''){
	    		if(Math.floor(qty) >= 0){
		    		btn_counter += 1;
		    	}
	    	}else{
	    		
	    	}
    	});

    	if(btn_counter < a){
    		return false;
    	}else{
    		return true;
    	}
    }

    $(document).on('click', '.slider-item', function(e){
    	e.preventDefault();
    	var row_tr = $(this).closest('tr').index();
    	$('#modal-slider-sales').find('#id').val(row_tr);
    	$('#modal-slider-sales').modal('show');
    });

    $(document).on('change', '#slider-qty', function(){
    	var qty = $(this).val();
    	$('#slider-qty-val').empty();
    	$('#slider-qty-val').val(qty);
    });

    $(document).on('keyup', '#slider-qty-val', function(){
    	var val = $(this).val();
    	$('#slider-qty').val(val);
    });

    $(document).on('change', '#slider-qty-start', function(){
    	var start = parseInt($(this).val());
    	var end = parseInt($('#slider-qty-end').val());

    	if(start <= end){
	    	$('#slider-qty-start-val').empty();
	    	$('#slider-qty-start-val').text(number_format(start));
	    }else{
	    	$('#slider-qty-end-val').empty();
	    	$('#slider-qty-end-val').text(number_format(start));
	    	$('#slider-qty-end').val(start);

	    	$('#slider-qty-start-val').empty();
	    	$('#slider-qty-start-val').text(number_format(start));
	    }
    });

    $(document).on('change', '#slider-qty-end', function(){
    	var end = parseInt($(this).val());
    	var start = parseInt($('#slider-qty-start').val());

    	if(end >= start){
	    	$('#slider-qty-end-val').empty();
	    	$('#slider-qty-end-val').text(number_format(end));
	    }else{
	    	$('#slider-qty-start-val').empty();
	    	$('#slider-qty-start-val').text(number_format(end));
	    	$('#slider-qty-start').val(end);

	    	$('#slider-qty-end-val').empty();
	    	$('#slider-qty-end-val').text(number_format(end));
	    }
    });
	
	$(document).on('change', '#slider-asp', function(){
    	var qty = $(this).val();
    	$('#slider-asp-val').empty();
    	$('#slider-asp-val').val(qty);
    });

    $(document).on('keyup', '#slider-asp-val', function(){
    	var val = $(this).val();
    	$('#slider-asp').val(val);
    });

    $(document).on('change', '#slider-asp-start', function(){
    	var start = parseInt($(this).val());
    	var end = parseInt($('#slider-asp-end').val());

    	if(start <= end){
	    	$('#slider-asp-start-val').empty();
	    	$('#slider-asp-start-val').text(number_format(start));
	    }else{
	    	$('#slider-asp-end-val').empty();
	    	$('#slider-asp-end-val').text(number_format(start));
	    	$('#slider-asp-end').val(start);

	    	$('#slider-asp-start-val').empty();
	    	$('#slider-asp-start-val').text(number_format(start));
	    }
    });

    $(document).on('change', '#slider-asp-end', function(){
    	var end = $(this).val();
    	var start = parseInt($('#slider-asp-start').val());

    	if(end >= start){
	    	$('#slider-asp-end-val').empty();
	    	$('#slider-asp-end-val').text(number_format(end));
	    }else{
	    	$('#slider-asp-start-val').empty();
	    	$('#slider-asp-start-val').text(number_format(end));
	    	$('#slider-asp-start').val(end);

	    	$('#slider-asp-end-val').empty();
	    	$('#slider-asp-end-val').text(number_format(end));
	    }
    });

    $(document).on('click', '.slider-sales-btn', function(e){
		e.preventDefault();
		
		var qty = $('#slider-qty-val').val();
		var qty_start = parseInt($('#slider-qty-start').val());
		var qty_end = parseInt($('#slider-qty-end').val());
		
		var count = parseInt($('#modal-slider-sales').find('#id').val()) + 2;
		var counter_qty = 1;

		var doc = $(document).find('#tbl-budget tr').eq(count)
		console.log(qty + ' - ' + qty_start + ' - ' + qty_end + ' - ' + count + ' - ' + counter_qty);
		$(document).find('#tbl-budget tr').eq(count).find('input[name^="budget_qty"]').each(function(){

			if(qty_start <= counter_qty  && qty_end >= counter_qty){
				$(this).val(qty);
			}

			var val = parseInt($(this).val());
			if(isNaN(val)){
				//total += 0;
			}else{
				//total += val;
			}
			counter_qty++;
		});

		var total_qty = 0;
		total_qty += parseFloat(doc.find('.budget-qty-jan').val()) || 0;
		total_qty += parseFloat(doc.find('.budget-qty-feb').val()) || 0;
		total_qty += parseFloat(doc.find('.budget-qty-mar').val()) || 0;
		total_qty += parseFloat(doc.find('.budget-qty-apr').val()) || 0;
		total_qty += parseFloat(doc.find('.budget-qty-may').val()) || 0;
		total_qty += parseFloat(doc.find('.budget-qty-jun').val()) || 0;
		total_qty += parseFloat(doc.find('.budget-qty-jul').val()) || 0;
		total_qty += parseFloat(doc.find('.budget-qty-aug').val()) || 0;
		total_qty += parseFloat(doc.find('.budget-qty-sep').val()) || 0;
		total_qty += parseFloat(doc.find('.budget-qty-oct').val()) || 0;
		total_qty += parseFloat(doc.find('.budget-qty-nov').val()) || 0;
		total_qty += parseFloat(doc.find('.budget-qty-dec').val()) || 0;

		doc.find('.sales-qty-total').text(total_qty);

		var asp = $('#slider-asp-val').val();
		var asp_start = parseInt($('#slider-asp-start').val());
		var asp_end = parseInt($('#slider-asp-end').val());
		
		var counter_asp = 1;

		$(document).find('#tbl-budget tr').eq(count).find('input[name^="asp"]').each(function(){
			if(asp_start <= counter_asp  && asp_end >= counter_asp){
				$(this).val(asp);
			}

			var val = parseInt($(this).val());
			if(isNaN(val)){
				//total += 0;
			}else{
				//total += val;
			}
			counter_asp++;
		});
		$('#modal-slider-sales').modal('hide');
	});

    /*Opex Slider*/
	$(document).on('click', '.slider-opex', function(e){
    	e.preventDefault();
    	var row_tr = $(this).closest('tr').index();
    	$('#modal-slider-opex').find('#id').val(row_tr);
    	$('#modal-slider-opex').modal('show');
    });

    $(document).on('change', '#slider-opex', function(){
    	var qty = $(this).val();
    	var row_tr = $(this).closest('tr').index();
    	$('#slider-opex-val').val(qty);
    });

    $(document).on('keyup', '#slider-opex-val', function(){
    	var qty = $(this).val();
    	$('#slider-opex').val(qty);
    });

    $(document).on('change', '#slider-opex-start', function(){
    	var start = parseInt($(this).val());
    	var end = parseInt($('#slider-opex-end').val());
    	
    	if(start <= end){
	    	$('#slider-opex-start-val').empty();
	    	$('#slider-opex-start-val').text(number_format(start));
	    }else{
	    	$('#slider-opex-end-val').empty();
	    	$('#slider-opex-end-val').text(number_format(start));
	    	$('#slider-opex-end').val(start);

	    	$('#slider-opex-start-val').empty();
	    	$('#slider-opex-start-val').text(number_format(start));
	    }
    });

    $(document).on('change', '#slider-opex-end', function(){
    	var end = parseInt($(this).val());
    	var start = parseInt($('#slider-opex-start').val());
    	if(end >= start){
    		
	    	$('#slider-opex-end-val').empty();
	    	$('#slider-opex-end-val').text(number_format(end));
	    }else{
	    	$('#slider-opex-start-val').empty();
	    	$('#slider-opex-start-val').text(number_format(end));
	    	$('#slider-opex-start').val(end);

	    	$('#slider-opex-end-val').empty();
	    	$('#slider-opex-end-val').text(number_format(end));
	    }
    });

    $(document).on('click', '.slider-opex-btn', function(e){
		e.preventDefault();
		var opex = $('#slider-opex-val').val();
		var opex_start = parseInt($('#slider-opex-start').val());
		var opex_end = parseInt($('#slider-opex-end').val());
		
		var count = parseInt($('#modal-slider-opex').find('#id').val()) + 1;
		var counter = 1;
		var total = 0;
		var total_qty = 0;
		$(document).find('#tbl-transac-opex tr').eq(count).find('input[name^="opex"]').each(function(){
			if(opex_start <= counter  && opex_end >= counter){
				$(this).val(opex);
			}

			var val = parseInt($(this).val());
			if(isNaN(val)){
				total += 0;
			}else{
				total += val;
			}
			counter++;
		});


		$(document).find('#tbl-transac-opex tr').eq(count).find('.opex-total-qty').text(number_format(total));

		grand_total = 0; 
		$('#tbl-transac-opex').find('input[name^="id"]').each(function(){
			var total = $(this).parent().find('.opex-total-qty').text();
			grand_total += parseFloat(total.replace(/,/g,""));
    	});

	    $('.opex-grand-total').text(number_format(grand_total));
		$('#modal-slider-opex').modal('hide');
	});

    $(document).on('click', '.slider-add-item', function(e){
    	e.preventDefault();
    	var row_tr = $(this).closest('tr').index();
    	$('#modal-slider-opex').modal('show');

    });

    $(document).on('click', '.slider-opex-item-btn', function(e){
		e.preventDefault();
		var opex = $('#slider-opex-val').val();
		var opex_start = parseInt($('#slider-opex-start').val());
		var opex_end = parseInt($('#slider-opex-end').val());
		
		var count = parseInt($('#modal-slider-opex').find('#id').val()) + 1;
		var counter = 1;
		var total = 0;
		var total_qty = 0;
		
		$('#tbl-opex-item tr').eq(count).find('input[name^="opex"]').each(function(){
			if(opex_start <= counter  && opex_end >= counter){
				$(this).val(opex);
			}
			var val = parseInt($(this).val());
			if(isNaN(val)){
				total += 0;
			}else{
				total += val;
			}
			counter++;
		});

		$('#tbl-opex-item tr').eq(count).find('.opex-total-qty').text(number_format(total, 2));

		grand_total = 0; 
		$('#tbl-opex-item tr').eq(count).find('input[name^="id"]').each(function(){
			var total = $(this).parent().find('.opex-total-qty').text();
			grand_total += parseFloat(total.replace(/,/g,""));
    	});

	    $('.opex-grand-total').text(number_format(grand_total, 2));
		$('#modal-slider-opex').modal('hide');
	});


    //CAPEX Slider

    $(document).on('click', '.show-slider-capex', function(e){
    	e.preventDefault();
    	var row_tr = $(this).closest('tr').index();
    	$('#modal-slider-capex').modal('show');
    	$('#modal-slider-capex').find('#id').val(row_tr);
    });

    $(document).on('change', '#slider-capex', function(){
    	var qty = $(this).val();
    	var row_tr = $(this).closest('tr').index();
    	$('#slider-capex-val').val(qty);
    });

     $(document).on('keyup', '#slider-capex-val', function(){
    	var qty = $(this).val();
    	var row_tr = $(this).closest('tr').index();
    	$('#slider-capex').val(qty);
    });

    $(document).on('change', '#slider-capex-start', function(){
    	var start = parseInt($(this).val());
    	var end = parseInt($('#slider-capex-end').val());
    	
    	if(start <= end){
	    	$('#slider-capex-start-val').empty();
	    	$('#slider-capex-start-val').text(number_format(start));
	    }else{
	    	$('#slider-capex-end-val').empty();
	    	$('#slider-capex-end-val').text(number_format(start));
	    	$('#slider-capex-end').val(start);

	    	$('#slider-capex-start-val').empty();
	    	$('#slider-capex-start-val').text(number_format(start));
	    }
    });

    $(document).on('change', '#slider-capex-end', function(){
    	var end = parseInt($(this).val());
    	var start = parseInt($('#slider-capex-start').val());
    	if(end >= start){
    		
	    	$('#slider-capex-end-val').empty();
	    	$('#slider-capex-end-val').text(number_format(end));
	    }else{
	    	$('#slider-capex-start-val').empty();
	    	$('#slider-capex-start-val').text(number_format(end));
	    	$('#slider-capex-start').val(end);

	    	$('#slider-capex-end-val').empty();
	    	$('#slider-capex-end-val').text(number_format(end));
	    }
    });

    $(document).on('click', '.slider-capex-btn', function(e){
		e.preventDefault();

		var capex = $('#slider-capex-val').val();
		var capex_start = parseInt($('#slider-capex-start').val());
		var capex_end = parseInt($('#slider-capex-end').val());
		
		var count = parseInt($('#modal-slider-capex').find('#id').val()) + 1;
		var counter = 1;

		var price = $(document).find('#tbl-transac-capex tr').eq(count).find('.asg-price').val();
		var total = 0;
		var total_qty = 0;

		$(document).find('#tbl-transac-capex tr').eq(count).find('input[name^="capex"]').each(function(){
			if(capex_start <= counter  && capex_end >= counter){
				$(this).val(capex);
			}

			var val = parseInt($(this).val());
			if(isNaN(val)){
				total += 0;
			}else{
				total += val;
			}
			counter++;
			console.log(counter);
		});


		$(document).find('#tbl-transac-capex tr').eq(count).find('.capex-total-qty').text(number_format(total));
		$(document).find('#tbl-transac-capex tr').eq(count).find('.capex-total-price').text(number_format(total * price));

		grand_total = 0; 
		$('#tbl-transac-capex').find('input[name^="id"]').each(function(){
			var total = $(this).parent().find('.capex-total-price').text();
			grand_total += parseFloat(total.replace(/,/g,""));
    	});

	    $('.capex-grand-total').text(number_format(grand_total));
		$('#modal-slider-capex').modal('hide');
	});

	$(document).on('click', '.slider-capex-item-btn', function(e){
		e.preventDefault();
		var capex = $('#slider-capex').val();
		var capex_start = parseInt($('#slider-capex-start').val());
		var capex_end = parseInt($('#slider-capex-end').val());
		
		var count = parseInt($('#modal-slider-capex').find('#id').val()) + 1;
		var counter = 1;

		var price = $(document).find('#tbl-capex-item tr').eq(count).find('.asg-price').val();
		var total = 0;
		var total_qty = 0;

		$(document).find('#tbl-capex-item tr').eq(count).find('input[name^="capex"]').each(function(){
			if(capex_start <= counter  && capex_end >= counter){
				$(this).val(capex);
			}

			var val = parseInt($(this).val());
			if(isNaN(val)){
				total += 0;
			}else{
				total += val;
			}
			counter++;
		});


		$(document).find('#tbl-capex-item tr').eq(count).find('.capex-total-qty').text(number_format(total));
		$(document).find('#tbl-capex-item tr').eq(count).find('.capex-total-price').text(number_format(total * price));

		grand_total = 0; 
		$('#tbl-capex-item').find('input[name^="id"]').each(function(){
			var total = $(this).parent().find('.capex-total-price').text();
			grand_total += parseFloat(total.replace(/,/g,""));
    	});

	    $('.capex-grand-total').text(number_format(grand_total));
		$('#modal-slider-capex').modal('hide');
	});




	$(document).on('change', '#add-outlet > .form-group > #region', function(e){
		e.preventDefault();
		var id = $(this).val();

		$.ajax({
	    	url: base_url + 'business-center/get-bc/' + id,
	    	method: 'GET',
	    	success:function(response){
	    		var parse_response = JSON.parse(response);
	    		
	    		if(parse_response['result'] == 1){
	    			$("#add-outlet").find('#bc').empty()
	    			$('#add-outlet').find('#bc').append(parse_response['info']);
		    	}else{
		    		console.log('Error please contact your administrator.');
		    	}
	    	}
		});
	});

	$(document).on('change', '#add-outlet > .form-group > #type', function(e){
		e.preventDefault();
		var id = $(this).val();
		$.ajax({
	    	url: base_url + 'business-center/get-brand/',
	    	data:{id:id},
	    	method: 'POST',
	    	success:function(response){
	    		var parse_response = JSON.parse(response);
	    		
	    		if(parse_response['result'] == 1){
	    			$('#add-outlet > .form-group > #brand').selectize()[0].selectize.destroy();
	    			$("#add-outlet").find('#brand').empty();
	    			$('#add-outlet').find('#brand').append(parse_response['info']);

	    			$('#add-outlet > .form-group > #brand').selectize({
				        maxItems: 1,
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

	var $select = $('#add-outlet > .form-group > #brand').selectize({
        maxItems: 100,
        valueField: 'id',
        labelField: 'title',
        searchField: 'title',
        create: false
    });

	$(document).on('change', '#add-brand-outlet > .form-group > #type', function(e){
		e.preventDefault();
		var id = $(this).val();
		var outlet_id = $('#add-brand-outlet').find('#id').val();
		$.ajax({
	    	url: base_url + 'business-center/get-brand-outlet/',
	    	data:{id:id, outlet_id:outlet_id},
	    	method: 'POST',
	    	success:function(response){
	    		var parse_response = JSON.parse(response);
	    		
	    		if(parse_response['result'] == 1){
	    			$('#add-brand-outlet > .form-group > #brand').selectize()[0].selectize.destroy();
	    			$("#add-brand-outlet").find('#brand').empty();
	    			$('#add-brand-outlet').find('#brand').append(parse_response['info']);

	    			$('#add-brand-outlet > .form-group > #brand').selectize({
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

	var $selects = $('#add-brand-outlet > .form-group > #brand').selectize({
        maxItems: 100,
        valueField: 'id',
        labelField: 'title',
        searchField: 'title',
        create: false
    });    

    $(document).on('click', '.remove-brand-outlet',function(e){
		e.preventDefault();
		var id = $(this).attr('data-id');
		$('#remove-brand-outlet').find('#id').val(id);
		$('#modal-confirm').modal({show:true});
	});

	$('#add-brand-material > .form-group > #material').selectize({
        maxItems: 100,
        valueField: 'id',
        labelField: 'title',
        searchField: 'title',
        create: false
    });

    $(document).on('click', '.remove-brand-material',function(e){
		e.preventDefault();
		var id = $(this).attr('data-id');
		$('#remove-brand-material').find('#id').val(id);
		$('#modal-confirm').modal({show:true});
	});

	$('#toggle-qty').change(function() {
		var toogle_status = $(this).prop('checked');
		var value = $(this).val();

		var count = 5;
		var counter = 0;

		for(var a=5; a <= 27; a++){
			if(a%2 == 0){
				var column = tbl_budget.column(a);
				column.visible(!column.visible());
			}
		}
		
		/*for(var a=5; a < 64; a++){
			if(counter >= 0 && counter < 2){
				var column = tbl_budget.column(count++);
				column.visible(!column.visible());
				counter++;
			}else{
				count += 2;
				counter = 0;
			}	
		}*/
    });

	$('#toggle-asp').change(function() {
		var toogle_status = $(this).prop('checked');
		var value = $(this).val();

		var count = 5;
		var counter = 0;

		for(var a=5; a <= 27; a++){
			if(a%2 == 1){
				var column = tbl_budget.column(a);
				column.visible(!column.visible());
			}
		}
    })
    
    /*January sales budget*/
	$(document).on('keyup', '.budget-qty-jan', function(){
		var id = $(this).attr('data-id');
		var ifs_code = $(this).attr('data-ifs');
		var qty = $(this).val();
		var loc = $(this).parent().parent().siblings('.budget-td').find('.weight-unit-jan');
		var loc_equivalent = $(this).parent().parent().siblings('.budget-td').find('.equivalent-unit-jan');
		var asp = $(this).parent().parent().siblings('.budget-td').find('.budget-asp-jan').val();
		$.ajax({
	    	url: base_url + 'business-center/material-info/',
	    	data:{id:id, qty:qty, asp:asp, ifs:ifs_code},
	    	method: 'POST',	
	    	success:function(response){
	    		var parse_response = JSON.parse(response);
	    		
	    		if(parse_response['result'] == 1){
	    			var weight_unit = parse_response['info'];
	    			var equivalent_unit = parse_response['equivalent_unit'];
	    			//$('.weight-unit').append(weight_unit);
	    			loc.empty();
	    			loc.append(number_format(weight_unit));

	    			loc_equivalent.empty();
	    			loc_equivalent.append(number_format(equivalent_unit));
		    	}else{
		    		console.log('Error please contact your administrator.');
		    	}
	    	}
		});
	});

	$(document).on('keyup', '.budget-asp-jan', function(){
		var id = $(this).attr('data-id');
		var ifs_code = $(this).attr('data-ifs');
		var asp = $(this).val();
		var loc = $(this).parent().parent().siblings('.budget-td').find('.asp-jan');
		var loc_equivalent = $(this).parent().parent().siblings('.budget-td').find('.equivalent-unit-jan');
		var qty = $(this).parent().parent().siblings('.budget-td').find('.budget-qty-jan').val();
		$.ajax({
	    	url: base_url + 'business-center/asp-info/',
	    	data:{id:id, asp:asp, qty:qty, ifs:ifs_code},
	    	method: 'POST',
	    	success:function(response){
	    		var parse_response = JSON.parse(response);
	    		
	    		if(parse_response['result'] == 1){
	    			var sales = parse_response['info'];
	    			var equivalent_unit = parse_response['equivalent_unit'];
	    			//$('.weight-unit').append(weight_unit);
	    			loc.empty();
	    			loc.append(number_format(sales));

	    			loc_equivalent.empty();
	    			loc_equivalent.append(number_format(equivalent_unit));
		    	}else{
		    		console.log('Error please contact your administrator.');
		    	}
	    	}
		});
	});

	/*February sales budget*/
	$(document).on('keyup', '.budget-qty-feb', function(){
		var id = $(this).attr('data-id');
		var ifs_code = $(this).attr('data-ifs');
		var qty = $(this).val();
		var loc = $(this).parent().parent().siblings('.budget-td').find('.weight-unit-feb');
		var loc_equivalent = $(this).parent().parent().siblings('.budget-td').find('.equivalent-unit-feb');
		var asp = $(this).parent().parent().siblings('.budget-td').find('.budget-asp-feb').val();
		$.ajax({
	    	url: base_url + 'business-center/material-info/',
	    	data:{id:id, qty:qty, asp:asp, ifs:ifs_code},
	    	method: 'POST',	
	    	success:function(response){
	    		var parse_response = JSON.parse(response);
	    		
	    		if(parse_response['result'] == 1){
	    			var weight_unit = parse_response['info'];
	    			var equivalent_unit = parse_response['equivalent_unit'];
	    			//$('.weight-unit').append(weight_unit);
	    			loc.empty();
	    			loc.append(number_format(weight_unit));

	    			loc_equivalent.empty();
	    			loc_equivalent.append(number_format(equivalent_unit));
		    	}else{
		    		console.log('Error please contact your administrator.');
		    	}
	    	}
		});
	});

	$(document).on('keyup', '.budget-asp-feb', function(){
		var id = $(this).attr('data-id');
		var ifs_code = $(this).attr('data-ifs');
		var asp = $(this).val();
		var loc = $(this).parent().parent().siblings('.budget-td').find('.asp-feb');
		var loc_equivalent = $(this).parent().parent().siblings('.budget-td').find('.equivalent-unit-feb');
		var qty = $(this).parent().parent().siblings('.budget-td').find('.budget-qty-feb').val();
		$.ajax({
	    	url: base_url + 'business-center/asp-info/',
	    	data:{id:id, asp:asp, qty:qty, ifs:ifs_code},
	    	method: 'POST',
	    	success:function(response){
	    		var parse_response = JSON.parse(response);
	    		
	    		if(parse_response['result'] == 1){
	    			var sales = parse_response['info'];
	    			var equivalent_unit = parse_response['equivalent_unit'];
	    			//$('.weight-unit').append(weight_unit);
	    			loc.empty();
	    			loc.append(number_format(sales));

	    			loc_equivalent.empty();
	    			loc_equivalent.append(number_format(equivalent_unit));
		    	}else{
		    		console.log('Error please contact your business-centeristrator.');
		    	}
	    	}
		});
	});

	/*March sales budget*/
	$(document).on('keyup', '.budget-qty-mar', function(){
		var id = $(this).attr('data-id');
		var ifs_code = $(this).attr('data-ifs');
		var qty = $(this).val();
		var loc = $(this).parent().parent().siblings('.budget-td').find('.weight-unit-mar');
		var loc_equivalent = $(this).parent().parent().siblings('.budget-td').find('.equivalent-unit-mar');
		var asp = $(this).parent().parent().siblings('.budget-td').find('.budget-asp-mar').val();
		$.ajax({
	    	url: base_url + 'business-center/material-info/',
	    	data:{id:id, qty:qty, asp:asp, ifs:ifs_code},
	    	method: 'POST',	
	    	success:function(response){
	    		var parse_response = JSON.parse(response);
	    		
	    		if(parse_response['result'] == 1){
	    			var weight_unit = parse_response['info'];
	    			var equivalent_unit = parse_response['equivalent_unit'];
	    			//$('.weight-unit').append(weight_unit);
	    			loc.empty();
	    			loc.append(number_format(weight_unit));

	    			loc_equivalent.empty();
	    			loc_equivalent.append(number_format(equivalent_unit));
		    	}else{
		    		console.log('Error please contact your administrator.');
		    	}
	    	}
		});
	});

	$(document).on('keyup', '.budget-asp-mar', function(){
		var id = $(this).attr('data-id');
		var ifs_code = $(this).attr('data-ifs');
		var asp = $(this).val();
		var loc = $(this).parent().parent().siblings('.budget-td').find('.asp-mar');
		var loc_equivalent = $(this).parent().parent().siblings('.budget-td').find('.equivalent-unit-mar');
		var qty = $(this).parent().parent().siblings('.budget-td').find('.budget-qty-mar').val();
		$.ajax({
	    	url: base_url + 'business-center/asp-info/',
	    	data:{id:id, asp:asp, qty:qty, ifs:ifs_code},
	    	method: 'POST',
	    	success:function(response){
	    		var parse_response = JSON.parse(response);
	    		
	    		if(parse_response['result'] == 1){
	    			var sales = parse_response['info'];
	    			var equivalent_unit = parse_response['equivalent_unit'];
	    			//$('.weight-unit').append(weight_unit);
	    			loc.empty();
	    			loc.append(number_format(sales));

	    			loc_equivalent.empty();
	    			loc_equivalent.append(number_format(equivalent_unit));
		    	}else{
		    		console.log('Error please contact your administrator.');
		    	}
	    	}
		});
	});

	/*April sales budget*/
	$(document).on('keyup', '.budget-qty-apr', function(){
		var id = $(this).attr('data-id');
		var ifs_code = $(this).attr('data-ifs');
		var qty = $(this).val();
		var loc = $(this).parent().parent().siblings('.budget-td').find('.weight-unit-apr');
		var loc_equivalent = $(this).parent().parent().siblings('.budget-td').find('.equivalent-unit-apr');
		var asp = $(this).parent().parent().siblings('.budget-td').find('.budget-asp-apr').val();
		$.ajax({
	    	url: base_url + 'business-center/material-info/',
	    	data:{id:id, qty:qty, asp:asp, ifs:ifs_code},
	    	method: 'POST',	
	    	success:function(response){
	    		var parse_response = JSON.parse(response);
	    		
	    		if(parse_response['result'] == 1){
	    			var weight_unit = parse_response['info'];
	    			var equivalent_unit = parse_response['equivalent_unit'];
	    			//$('.weight-unit').append(weight_unit);
	    			loc.empty();
	    			loc.append(number_format(weight_unit));

	    			loc_equivalent.empty();
	    			loc_equivalent.append(number_format(equivalent_unit));
		    	}else{
		    		console.log('Error please contact your administrator.');
		    	}
	    	}
		});
	});

	$(document).on('keyup', '.budget-asp-apr', function(){
		var id = $(this).attr('data-id');
		var ifs_code = $(this).attr('data-ifs');
		var asp = $(this).val();
		var loc = $(this).parent().parent().siblings('.budget-td').find('.asp-apr');
		var loc_equivalent = $(this).parent().parent().siblings('.budget-td').find('.equivalent-unit-apr');
		var qty = $(this).parent().parent().siblings('.budget-td').find('.budget-qty-apr').val();
		$.ajax({
	    	url: base_url + 'business-center/asp-info/',
	    	data:{id:id, asp:asp, qty:qty, ifs:ifs_code},
	    	method: 'POST',
	    	success:function(response){
	    		var parse_response = JSON.parse(response);
	    		
	    		if(parse_response['result'] == 1){
	    			var sales = parse_response['info'];
	    			var equivalent_unit = parse_response['equivalent_unit'];
	    			//$('.weight-unit').append(weight_unit);
	    			loc.empty();
	    			loc.append(number_format(sales));

	    			loc_equivalent.empty();
	    			loc_equivalent.append(number_format(equivalent_unit));
		    	}else{
		    		console.log('Error please contact your administrator.');
		    	}
	    	}
		});
	});

	/*May sales budget*/
	$(document).on('keyup', '.budget-qty-may', function(){
		var id = $(this).attr('data-id');
		var ifs_code = $(this).attr('data-ifs');
		var qty = $(this).val();
		var loc = $(this).parent().parent().siblings('.budget-td').find('.weight-unit-may');
		var loc_equivalent = $(this).parent().parent().siblings('.budget-td').find('.equivalent-unit-may');
		var asp = $(this).parent().parent().siblings('.budget-td').find('.budget-asp-may').val();
		$.ajax({
	    	url: base_url + 'business-center/material-info/',
	    	data:{id:id, qty:qty, asp:asp, ifs:ifs_code},
	    	method: 'POST',	
	    	success:function(response){
	    		var parse_response = JSON.parse(response);
	    		
	    		if(parse_response['result'] == 1){
	    			var weight_unit = parse_response['info'];
	    			var equivalent_unit = parse_response['equivalent_unit'];
	    			//$('.weight-unit').append(weight_unit);
	    			loc.empty();
	    			loc.append(number_format(weight_unit));

	    			loc_equivalent.empty();
	    			loc_equivalent.append(number_format(equivalent_unit));
		    	}else{
		    		console.log('Error please contact your administrator.');
		    	}
	    	}
		});
	});

	$(document).on('keyup', '.budget-asp-may', function(){
		var id = $(this).attr('data-id');
		var ifs_code = $(this).attr('data-ifs');
		var asp = $(this).val();
		var loc = $(this).parent().parent().siblings('.budget-td').find('.asp-may');
		var loc_equivalent = $(this).parent().parent().siblings('.budget-td').find('.equivalent-unit-may');
		var qty = $(this).parent().parent().siblings('.budget-td').find('.budget-qty-may').val();
		$.ajax({
	    	url: base_url + 'business-center/asp-info/',
	    	data:{id:id, asp:asp, qty:qty, ifs:ifs_code},
	    	method: 'POST',
	    	success:function(response){
	    		var parse_response = JSON.parse(response);
	    		
	    		if(parse_response['result'] == 1){
	    			var sales = parse_response['info'];
	    			var equivalent_unit = parse_response['equivalent_unit'];
	    			//$('.weight-unit').append(weight_unit);
	    			loc.empty();
	    			loc.append(number_format(sales));

	    			loc_equivalent.empty();
	    			loc_equivalent.append(number_format(equivalent_unit));
		    	}else{
		    		console.log('Error please contact your administrator.');
		    	}
	    	}
		});
	});

	/*June sales budget*/
	$(document).on('keyup', '.budget-qty-jun', function(){
		var id = $(this).attr('data-id');
		var ifs_code = $(this).attr('data-ifs');
		var qty = $(this).val();
		var loc = $(this).parent().parent().siblings('.budget-td').find('.weight-unit-jun');
		var loc_equivalent = $(this).parent().parent().siblings('.budget-td').find('.equivalent-unit-jun');
		var asp = $(this).parent().parent().siblings('.budget-td').find('.budget-asp-jun').val();
		$.ajax({
	    	url: base_url + 'business-center/material-info/',
	    	data:{id:id, qty:qty, asp:asp, ifs:ifs_code},
	    	method: 'POST',	
	    	success:function(response){
	    		var parse_response = JSON.parse(response);
	    		
	    		if(parse_response['result'] == 1){
	    			var weight_unit = parse_response['info'];
	    			var equivalent_unit = parse_response['equivalent_unit'];
	    			//$('.weight-unit').append(weight_unit);
	    			loc.empty();
	    			loc.append(number_format(weight_unit));

	    			loc_equivalent.empty();
	    			loc_equivalent.append(number_format(equivalent_unit));
		    	}else{
		    		console.log('Error please contact your administrator.');
		    	}
	    	}
		});
	});

	$(document).on('keyup', '.budget-asp-jun', function(){
		var id = $(this).attr('data-id');
		var ifs_code = $(this).attr('data-ifs');
		var asp = $(this).val();
		var loc = $(this).parent().parent().siblings('.budget-td').find('.asp-jun');
		var loc_equivalent = $(this).parent().parent().siblings('.budget-td').find('.equivalent-unit-jun');
		var qty = $(this).parent().parent().siblings('.budget-td').find('.budget-qty-jun').val();
		$.ajax({
	    	url: base_url + 'business-center/asp-info/',
	    	data:{id:id, asp:asp, qty:qty, ifs:ifs_code},
	    	method: 'POST',
	    	success:function(response){
	    		var parse_response = JSON.parse(response);
	    		
	    		if(parse_response['result'] == 1){
	    			var sales = parse_response['info'];
	    			var equivalent_unit = parse_response['equivalent_unit'];
	    			//$('.weight-unit').append(weight_unit);
	    			loc.empty();
	    			loc.append(number_format(sales));

	    			loc_equivalent.empty();
	    			loc_equivalent.append(number_format(equivalent_unit));
		    	}else{
		    		console.log('Error please contact your administrator.');
		    	}
	    	}
		});
	});

	/*July sales budget*/
	$(document).on('keyup', '.budget-qty-jul', function(){
		var id = $(this).attr('data-id');
		var ifs_code = $(this).attr('data-ifs');
		var qty = $(this).val();
		var loc = $(this).parent().parent().siblings('.budget-td').find('.weight-unit-jul');
		var loc_equivalent = $(this).parent().parent().siblings('.budget-td').find('.equivalent-unit-jul');
		var asp = $(this).parent().parent().siblings('.budget-td').find('.budget-asp-jul').val();
		$.ajax({
	    	url: base_url + 'business-center/material-info/',
	    	data:{id:id, qty:qty, asp:asp, ifs:ifs_code},
	    	method: 'POST',	
	    	success:function(response){
	    		var parse_response = JSON.parse(response);
	    		
	    		if(parse_response['result'] == 1){
	    			var weight_unit = parse_response['info'];
	    			var equivalent_unit = parse_response['equivalent_unit'];
	    			//$('.weight-unit').append(weight_unit);
	    			loc.empty();
	    			loc.append(number_format(weight_unit));

	    			loc_equivalent.empty();
	    			loc_equivalent.append(number_format(equivalent_unit));
		    	}else{
		    		console.log('Error please contact your administrator.');
		    	}
	    	}
		});
	});

	$(document).on('keyup', '.budget-asp-jul', function(){
		var id = $(this).attr('data-id');
		var ifs_code = $(this).attr('data-ifs');
		var asp = $(this).val();
		var loc = $(this).parent().parent().siblings('.budget-td').find('.asp-jul');
		var loc_equivalent = $(this).parent().parent().siblings('.budget-td').find('.equivalent-unit-jul');
		var qty = $(this).parent().parent().siblings('.budget-td').find('.budget-qty-jul').val();
		$.ajax({
	    	url: base_url + 'business-center/asp-info/',
	    	data:{id:id, asp:asp, qty:qty, ifs:ifs_code},
	    	method: 'POST',
	    	success:function(response){
	    		var parse_response = JSON.parse(response);
	    		
	    		if(parse_response['result'] == 1){
	    			var sales = parse_response['info'];
	    			var equivalent_unit = parse_response['equivalent_unit'];
	    			//$('.weight-unit').append(weight_unit);
	    			loc.empty();
	    			loc.append(number_format(sales));

	    			loc_equivalent.empty();
	    			loc_equivalent.append(number_format(equivalent_unit));
		    	}else{
		    		console.log('Error please contact your administrator.');
		    	}
	    	}
		});
	});

	/*August sales budget*/
	$(document).on('keyup', '.budget-qty-aug', function(){
		var id = $(this).attr('data-id');
		var ifs_code = $(this).attr('data-ifs');
		var qty = $(this).val();
		var loc = $(this).parent().parent().siblings('.budget-td').find('.weight-unit-aug');
		var loc_equivalent = $(this).parent().parent().siblings('.budget-td').find('.equivalent-unit-aug');
		var asp = $(this).parent().parent().siblings('.budget-td').find('.budget-asp-aug').val();
		$.ajax({
	    	url: base_url + 'business-center/material-info/',
	    	data:{id:id, qty:qty, asp:asp, ifs:ifs_code},
	    	method: 'POST',	
	    	success:function(response){
	    		var parse_response = JSON.parse(response);
	    		
	    		if(parse_response['result'] == 1){
	    			var weight_unit = parse_response['info'];
	    			var equivalent_unit = parse_response['equivalent_unit'];
	    			//$('.weight-unit').append(weight_unit);
	    			loc.empty();
	    			loc.append(number_format(weight_unit));

	    			loc_equivalent.empty();
	    			loc_equivalent.append(number_format(equivalent_unit));
		    	}else{
		    		console.log('Error please contact your administrator.');
		    	}
	    	}
		});
	});

	$(document).on('keyup', '.budget-asp-aug', function(){
		var id = $(this).attr('data-id');
		var ifs_code = $(this).attr('data-ifs');
		var asp = $(this).val();
		var loc = $(this).parent().parent().siblings('.budget-td').find('.asp-aug');
		var loc_equivalent = $(this).parent().parent().siblings('.budget-td').find('.equivalent-unit-aug');
		var qty = $(this).parent().parent().siblings('.budget-td').find('.budget-qty-aug').val();
		$.ajax({
	    	url: base_url + 'business-center/asp-info/',
	    	data:{id:id, asp:asp, qty:qty, ifs:ifs_code},
	    	method: 'POST',
	    	success:function(response){
	    		var parse_response = JSON.parse(response);
	    		
	    		if(parse_response['result'] == 1){
	    			var sales = parse_response['info'];
	    			var equivalent_unit = parse_response['equivalent_unit'];
	    			//$('.weight-unit').append(weight_unit);
	    			loc.empty();
	    			loc.append(number_format(sales));

	    			loc_equivalent.empty();
	    			loc_equivalent.append(number_format(equivalent_unit));
		    	}else{
		    		console.log('Error please contact your administrator.');
		    	}
	    	}
		});
	});

	/*September sales budget*/
	$(document).on('keyup', '.budget-qty-sep', function(){
		var id = $(this).attr('data-id');
		var ifs_code = $(this).attr('data-ifs');
		var qty = $(this).val();
		var loc = $(this).parent().parent().siblings('.budget-td').find('.weight-unit-sep');
		var loc_equivalent = $(this).parent().parent().siblings('.budget-td').find('.equivalent-unit-sep');
		var asp = $(this).parent().parent().siblings('.budget-td').find('.budget-asp-sep').val();
		$.ajax({
	    	url: base_url + 'business-center/material-info/',
	    	data:{id:id, qty:qty, asp:asp, ifs:ifs_code},
	    	method: 'POST',	
	    	success:function(response){
	    		var parse_response = JSON.parse(response);
	    		
	    		if(parse_response['result'] == 1){
	    			var weight_unit = parse_response['info'];
	    			var equivalent_unit = parse_response['equivalent_unit'];
	    			//$('.weight-unit').append(weight_unit);
	    			loc.empty();
	    			loc.append(number_format(weight_unit));

	    			loc_equivalent.empty();
	    			loc_equivalent.append(number_format(equivalent_unit));
		    	}else{
		    		console.log('Error please contact your administrator.');
		    	}
	    	}
		});
	});

	$(document).on('keyup', '.budget-asp-sep', function(){
		var id = $(this).attr('data-id');
		var ifs_code = $(this).attr('data-ifs');
		var asp = $(this).val();
		var loc = $(this).parent().parent().siblings('.budget-td').find('.asp-sep');
		var loc_equivalent = $(this).parent().parent().siblings('.budget-td').find('.equivalent-unit-sep');
		var qty = $(this).parent().parent().siblings('.budget-td').find('.budget-qty-sep').val();
		$.ajax({
	    	url: base_url + 'business-center/asp-info/',
	    	data:{id:id, asp:asp, qty:qty, ifs:ifs_code},
	    	method: 'POST',
	    	success:function(response){
	    		var parse_response = JSON.parse(response);
	    		
	    		if(parse_response['result'] == 1){
	    			var sales = parse_response['info'];
	    			var equivalent_unit = parse_response['equivalent_unit'];
	    			//$('.weight-unit').append(weight_unit);
	    			loc.empty();
	    			loc.append(number_format(sales));

	    			loc_equivalent.empty();
	    			loc_equivalent.append(number_format(equivalent_unit));
		    	}else{
		    		console.log('Error please contact your administrator.');
		    	}
	    	}
		});
	});

	/*October sales budget*/
	$(document).on('keyup', '.budget-qty-oct', function(){
		var id = $(this).attr('data-id');
		var ifs_code = $(this).attr('data-ifs');
		var qty = $(this).val();
		var loc = $(this).parent().parent().siblings('.budget-td').find('.weight-unit-oct');
		var loc_equivalent = $(this).parent().parent().siblings('.budget-td').find('.equivalent-unit-oct');
		var asp = $(this).parent().parent().siblings('.budget-td').find('.budget-asp-oct').val();
		$.ajax({
	    	url: base_url + 'business-center/material-info/',
	    	data:{id:id, qty:qty, asp:asp, ifs:ifs_code},
	    	method: 'POST',	
	    	success:function(response){
	    		var parse_response = JSON.parse(response);
	    		
	    		if(parse_response['result'] == 1){
	    			var weight_unit = parse_response['info'];
	    			var equivalent_unit = parse_response['equivalent_unit'];
	    			//$('.weight-unit').append(weight_unit);
	    			loc.empty();
	    			loc.append(number_format(weight_unit));

	    			loc_equivalent.empty();
	    			loc_equivalent.append(number_format(equivalent_unit));
		    	}else{
		    		console.log('Error please contact your administrator.');
		    	}
	    	}
		});
	});

	$(document).on('keyup', '.budget-asp-oct', function(){
		var id = $(this).attr('data-id');
		var ifs_code = $(this).attr('data-ifs');
		var asp = $(this).val();
		var loc = $(this).parent().parent().siblings('.budget-td').find('.asp-oct');
		var loc_equivalent = $(this).parent().parent().siblings('.budget-td').find('.equivalent-unit-oct');
		var qty = $(this).parent().parent().siblings('.budget-td').find('.budget-qty-oct').val();
		$.ajax({
	    	url: base_url + 'business-center/asp-info/',
	    	data:{id:id, asp:asp, qty:qty, ifs:ifs_code},
	    	method: 'POST',
	    	success:function(response){
	    		var parse_response = JSON.parse(response);
	    		
	    		if(parse_response['result'] == 1){
	    			var sales = parse_response['info'];
	    			var equivalent_unit = parse_response['equivalent_unit'];
	    			//$('.weight-unit').append(weight_unit);
	    			loc.empty();
	    			loc.append(number_format(sales));

	    			loc_equivalent.empty();
	    			loc_equivalent.append(number_format(equivalent_unit));
		    	}else{
		    		console.log('Error please contact your administrator.');
		    	}
	    	}
		});
	});

	/*November sales budget*/
	$(document).on('keyup', '.budget-qty-nov', function(){
		var id = $(this).attr('data-id');
		var ifs_code = $(this).attr('data-ifs');
		var qty = $(this).val();
		var loc = $(this).parent().parent().siblings('.budget-td').find('.weight-unit-nov');
		var loc_equivalent = $(this).parent().parent().siblings('.budget-td').find('.equivalent-unit-nov');
		var asp = $(this).parent().parent().siblings('.budget-td').find('.budget-asp-nov').val();
		$.ajax({
	    	url: base_url + 'business-center/material-info/',
	    	data:{id:id, qty:qty, asp:asp, ifs:ifs_code},
	    	method: 'POST',	
	    	success:function(response){
	    		var parse_response = JSON.parse(response);
	    		
	    		if(parse_response['result'] == 1){
	    			var weight_unit = parse_response['info'];
	    			var equivalent_unit = parse_response['equivalent_unit'];
	    			//$('.weight-unit').append(weight_unit);
	    			loc.empty();
	    			loc.append(number_format(weight_unit));

	    			loc_equivalent.empty();
	    			loc_equivalent.append(number_format(equivalent_unit));
		    	}else{
		    		console.log('Error please contact your administrator.');
		    	}
	    	}
		});
	});

	$(document).on('keyup', '.budget-asp-nov', function(){
		var id = $(this).attr('data-id');
		var ifs_code = $(this).attr('data-ifs');
		var asp = $(this).val();
		var loc = $(this).parent().parent().siblings('.budget-td').find('.asp-nov');
		var loc_equivalent = $(this).parent().parent().siblings('.budget-td').find('.equivalent-unit-nov');
		var qty = $(this).parent().parent().siblings('.budget-td').find('.budget-qty-nov').val();
		$.ajax({
	    	url: base_url + 'business-center/asp-info/',
	    	data:{id:id, asp:asp, qty:qty, ifs:ifs_code},
	    	method: 'POST',
	    	success:function(response){
	    		var parse_response = JSON.parse(response);
	    		
	    		if(parse_response['result'] == 1){
	    			var sales = parse_response['info'];
	    			var equivalent_unit = parse_response['equivalent_unit'];
	    			//$('.weight-unit').append(weight_unit);
	    			loc.empty();
	    			loc.append(number_format(sales));

	    			loc_equivalent.empty();
	    			loc_equivalent.append(number_format(equivalent_unit));
		    	}else{
		    		console.log('Error please contact your administrator.');
		    	}
	    	}
		});
	});	

	/*December sales budget*/
	$(document).on('keyup', '.budget-qty-dec', function(){
		var id = $(this).attr('data-id');
		var ifs_code = $(this).attr('data-ifs');
		var qty = $(this).val();
		var loc = $(this).parent().parent().siblings('.budget-td').find('.weight-unit-dec');
		var loc_equivalent = $(this).parent().parent().siblings('.budget-td').find('.equivalent-unit-dec');
		var asp = $(this).parent().parent().siblings('.budget-td').find('.budget-asp-dec').val();
		$.ajax({
	    	url: base_url + 'business-center/material-info/',
	    	data:{id:id, qty:qty, asp:asp, ifs:ifs_code},
	    	method: 'POST',	
	    	success:function(response){
	    		var parse_response = JSON.parse(response);
	    		
	    		if(parse_response['result'] == 1){
	    			var weight_unit = parse_response['info'];
	    			var equivalent_unit = parse_response['equivalent_unit'];
	    			//$('.weight-unit').append(weight_unit);
	    			loc.empty();
	    			loc.append(number_format(weight_unit));

	    			loc_equivalent.empty();
	    			loc_equivalent.append(number_format(equivalent_unit));
		    	}else{
		    		console.log('Error please contact your administrator.');
		    	}
	    	}
		});
	});

	$(document).on('keyup', '.budget-asp-dec', function(){
		var id = $(this).attr('data-id');
		var ifs_code = $(this).attr('data-ifs');
		var asp = $(this).val();
		var loc = $(this).parent().parent().siblings('.budget-td').find('.asp-dec');
		var loc_equivalent = $(this).parent().parent().siblings('.budget-td').find('.equivalent-unit-dec');
		var qty = $(this).parent().parent().siblings('.budget-td').find('.budget-qty-dec').val();
		$.ajax({
	    	url: base_url + 'business-center/asp-info/',
	    	data:{id:id, asp:asp, qty:qty, ifs:ifs_code},
	    	method: 'POST',
	    	success:function(response){
	    		var parse_response = JSON.parse(response);
	    		
	    		if(parse_response['result'] == 1){
	    			var sales = parse_response['info'];
	    			var equivalent_unit = parse_response['equivalent_unit'];
	    			//$('.weight-unit').append(weight_unit);
	    			loc.empty();
	    			loc.append(number_format(sales));

	    			loc_equivalent.empty();
	    			loc_equivalent.append(number_format(equivalent_unit));
		    	}else{
		    		console.log('Error please contact your administrator.');
		    	}
	    	}
		});
	});

	$('#budget-month').datepicker({
	    autoclose: true,
	    format: 'mm/yyyy',
	    viewMode: "months",
		minViewMode: "months",
		startDate: '01/2019',
		endDate: '12/2019',
	}).on('changeDate',function(e){
		check_form();
		$(this).parent('.date').parent('.form-group').addClass('has-feedback');
	    $(this).parent('.date').parent('.form-group').addClass('has-success');
	});

	$(document).on('keyup', '[name="budget_qty[]"]', function(){
    	var received = $(this).val();

    	if($.isNumeric(received)){
    		if(Math.floor(received) > 0){
    			
	    		$(this).parent('.form-group').removeClass('has-feedback');
	    		$(this).parent('.form-group').removeClass('has-success');
	    		$(this).parent('.form-group').removeClass('has-error');
	    		$(this).parent('.form-group').addClass('has-feedback');
	    		$(this).parent('.form-group').addClass('has-success');

	    		$('#month').parent('.date').parent('.form-group').addClass('has-feedback');
	    		$('#month').parent('.date').parent('.form-group').addClass('has-success');
	    	}else{
	    		$(this).parent('.form-group').addClass('has-feedback');
    			$(this).parent('.form-group').addClass('has-error');
	    	}
    	}else{
    		$(this).parent('.form-group').addClass('has-feedback');
    		$(this).parent('.form-group').addClass('has-error');
    	}
    	//check_form();
    });

    $(document).on('keyup', '[name="asp[]"]', function(){
    	var received = $(this).val();

    	if($.isNumeric(received)){
    		if(Math.floor(received) > 0){
    			
	    		$(this).parent('.form-group').removeClass('has-feedback');
	    		$(this).parent('.form-group').removeClass('has-success');
	    		$(this).parent('.form-group').removeClass('has-error');
	    		$(this).parent('.form-group').addClass('has-feedback');
	    		$(this).parent('.form-group').addClass('has-success');

	    		$('#month').parent('.date').parent('.form-group').addClass('has-feedback');
	    		$('#month').parent('.date').parent('.form-group').addClass('has-success');
	    	}else{
	    		$(this).parent('.form-group').addClass('has-feedback');
    			$(this).parent('.form-group').addClass('has-error');
	    	}
    	}else{
    		$(this).parent('.form-group').addClass('has-feedback');
    		$(this).parent('.form-group').addClass('has-error');
    	}
    	//check_form();
    });

     function check_budget(){
    	var a = 0;
    	var btn_counter = 0;
    	$('input[name^="qty"]').each(function(){
    		var qty = $(this).val();
    		a += 1;
    		if($.isNumeric(qty)){
	    		if(Math.floor(qty) > 0){
	    			
		    		btn_counter += 1;
		    	}else{
		    		
		    	}
	    	}else{
	    		
	    	}
    	});

    	var month = $('#date-pick-month').val()
    	if(btn_counter < a ||  !(bc) || !(month)){
    		$(".btn-budget").attr('disabled', true);
    	}else{
    		$(".btn-budget").removeAttr('disabled', true);
    	}
    }

    $("#add-material").bootstrapValidator({
		framework: 'bootstrap',
		excluded: [':disabled'],
		fields:{
			material_code: {
				validators: {
					notEmpty: {
						message: 'Material code is required!'
					}
				}
			},

			description: {
				validators: {
					notEmpty: {
						message: 'Material description is required!'
					}
				}
			},

			type: {
				validators: {
					notEmpty: {
						message: 'Material Group is required!'
					}
				}
			},

			vat: {
				validators: {
					notEmpty: {
						message: 'VAT is required!'
					}
				}
			},

			base_unit: {
				validators: {
					notEmpty: {
						message: 'Base Unit is required!'
					}
				}
			},

			weight_basis: {
				validators: {
					numeric: {
                        message: 'The value is not a number',
                        thousandsSeparator: '',
                        decimalSeparator: '.'
                    }
				}
			},

			sales_basis: {
				validators: {
					notEmpty: {
						message: 'Sales Basis is required!'
					}
				}
			},

			equivalent_unit: {
				validators: {
					notEmpty: {
						message: 'Equivalent Unit is required!'
					},

					numeric: {
                        message: 'The value is not a number',
                        thousandsSeparator: '',
                        decimalSeparator: '.'
                    }
				}
			},

			um:{
				validators: {
					notEmpty: {
						message: 'Unit of measurement is required!'
					}
				}
			},

			category:{
				validators: {
					notEmpty: {
						message: 'Category is required!'
					}
				}
			},
		}
	});

	$("#update-material").bootstrapValidator({
		framework: 'bootstrap',
		excluded: [':disabled'],
		fields:{
			material_code: {
				validators: {
					notEmpty: {
						message: 'Material code is required!'
					}
				}
			},

			description: {
				validators: {
					notEmpty: {
						message: 'Material description is required!'
					}
				}
			},

			type: {
				validators: {
					notEmpty: {
						message: 'Material Group is required!'
					}
				}
			},

			vat: {
				validators: {
					notEmpty: {
						message: 'VAT is required!'
					}
				}
			},

			base_unit: {
				validators: {
					notEmpty: {
						message: 'Base Unit is required!'
					}
				}
			},

			weight_basis: {
				validators: {
					numeric: {
                        message: 'The value is not a number',
                        thousandsSeparator: '',
                        decimalSeparator: '.'
                    }
				}
			},

			sales_basis: {
				validators: {
					notEmpty: {
						message: 'Sales Basis is required!'
					}
				}
			},

			equivalent_unit: {
				validators: {
					notEmpty: {
						message: 'Equivalent Unit is required!'
					},

					numeric: {
                        message: 'The value is not a number',
                        thousandsSeparator: '',
                        decimalSeparator: '.'
                    }
				}
			}
		}
	});

	$("#add-vmaterial").bootstrapValidator({
		framework: 'bootstrap',
		excluded: [':disabled'],
		fields:{
			material_code: {
				validators: {
					notEmpty: {
						message: 'Material code is required!'
					}
				}
			},

			description: {
				validators: {
					notEmpty: {
						message: 'Material description is required!'
					}
				}
			},

			type: {
				validators: {
					notEmpty: {
						message: 'Material Group is required!'
					}
				}
			},

			vat: {
				validators: {
					notEmpty: {
						message: 'VAT is required!'
					}
				}
			},

			base_unit: {
				validators: {
					notEmpty: {
						message: 'Base Unit is required!'
					}
				}
			},

			weight_basis: {
				validators: {
					numeric: {
                        message: 'The value is not a number',
                        thousandsSeparator: '',
                        decimalSeparator: '.'
                    }
				}
			},

			sales_basis: {
				validators: {
					notEmpty: {
						message: 'Sales Basis is required!'
					}
				}
			},

			equivalent_unit: {
				validators: {
					notEmpty: {
						message: 'Equivalent Unit is required!'
					},

					numeric: {
                        message: 'The value is not a number',
                        thousandsSeparator: '',
                        decimalSeparator: '.'
                    }
				}
			}
		}
	});

	$("#update-vmaterial").bootstrapValidator({
		framework: 'bootstrap',
		excluded: [':disabled'],
		fields:{
			material_code: {
				validators: {
					notEmpty: {
						message: 'Material code is required!'
					}
				}
			},

			description: {
				validators: {
					notEmpty: {
						message: 'Material description is required!'
					}
				}
			},

			type: {
				validators: {
					notEmpty: {
						message: 'Material Group is required!'
					}
				}
			},

			vat: {
				validators: {
					notEmpty: {
						message: 'VAT is required!'
					}
				}
			},

			base_unit: {
				validators: {
					notEmpty: {
						message: 'Base Unit is required!'
					}
				}
			},

			weight_basis: {
				validators: {
					numeric: {
                        message: 'The value is not a number',
                        thousandsSeparator: '',
                        decimalSeparator: '.'
                    }
				}
			},

			sales_basis: {
				validators: {
					notEmpty: {
						message: 'Sales Basis is required!'
					}
				}
			},

			equivalent_unit: {
				validators: {
					notEmpty: {
						message: 'Equivalent Unit is required!'
					},

					numeric: {
                        message: 'The value is not a number',
                        thousandsSeparator: '',
                        decimalSeparator: '.'
                    }
				}
			}
		}
	});

	$("#add-group").bootstrapValidator({
		framework: 'bootstrap',
		excluded: [':disabled'],
		fields:{
			group: {
				validators: {
					notEmpty: {
						message: 'Material Group is required!'
					}
				}
			},

			ads: {
				validators: {
					notEmpty: {
						message: 'ADS is required!'
					},
					numeric: {
                        message: 'The value is not a number',
                        thousandsSeparator: '',
                        decimalSeparator: '.'
                    }
				}
			}
		}
	});

	$("#update-group").bootstrapValidator({
		framework: 'bootstrap',
		excluded: [':disabled'],
		fields:{
			group: {
				validators: {
					notEmpty: {
						message: 'Material Group is required!'
					}
				}
			},

			ads: {
				validators: {
					notEmpty: {
						message: 'ADS is required!'
					},
					numeric: {
                        message: 'The value is not a number',
                        thousandsSeparator: '',
                        decimalSeparator: '.'
                    }
				}
			}
		}
	});

	$("#add-outlet").bootstrapValidator({
		framework: 'bootstrap',
		excluded: [':disabled'],
		fields:{
			outlet: {
				validators: {
					notEmpty: {
						message: 'Outlet Name is required!'
					}
				}
			},

			ifs: {
				validators: {
					notEmpty: {
						message: 'IFS Code is required!'
					}
				}
			},

			status: {
				validators: {
					notEmpty: {
						message: 'Status is required!'
					}
				}
			},

			region: {
				validators: {
					notEmpty: {
						message: 'Region is required!'
					}
				}
			},

			bc: {
				validators: {
					notEmpty: {
						message: 'Business Center is required!'
					}
				}
			},

			type: {
				validators: {
					notEmpty: {
						message: 'Type is required!'
					}
				}
			},

			'brand[]': {
				validators: {
					notEmpty: {
						message: 'Brand is required!'
					}
				}
			}
		}
	});

	$("#add-brand-material").bootstrapValidator({
		framework: 'bootstrap',
		excluded: [':disabled'],
		fields:{
			'material[]': {
				validators: {
					notEmpty: {
						message: 'Material is required!'
					}
				}
			}
		}
	});


	//group
	$(document).on('click', '.edit-group', function(e){
		e.preventDefault();
		var id = $(this).attr('data-id');
		$.ajax({
	    	url: base_url + 'business-center/modal-group/',
	    	data: {id:id},
	    	method: 'POST',
	    	success:function(response){
	    		var parse_response = JSON.parse(response);
	    		
	    		if(parse_response['result'] == 1){
		    		$("#update-group").find("#group").val(parse_response['info'].group);
		    		$("#update-group").find("#id").val(parse_response['info'].id);
		    		/*$("#update-group").find("#ads").val(parse_response['info'].ads);*/
		    		$('#modal-edit-group').modal({show:true});
		    		$("#update-group").bootstrapValidator('resetForm');
		    	}else{
		    		console.log('Error please contact your administrator');
		    	}
	    	}
		});
	});


	// Capex
	var tbl_transac_capex = $('#tbl-transac-capex').DataTable({
		"bInfo" : false,
		"paging" : false,
		fixedHeader: true
	});

	$('#tbl-capex').DataTable({
		"bInfo" : false,
		"order": []
	});

	$('#tbl-capex-info').DataTable({
		"bInfo" : false,
		"order": []
	});

	$('#tbl-view-capex').DataTable({
		"bInfo" : false,
		"order": []
	});

	$('.capex-cost-center').select2({
		"placeholder": 'Select Cost Center'
	});

	$('#capex-ag').select2({
		"placeholder": 'Select Asset Group'
	});

	$('#capex-trans-year').datepicker({
	    autoclose: true,
	    format: 'yyyy',
	    viewMode: "years",
		minViewMode: "years",
	});

	$(document).on('change', '#capex-trans-year', function(e){
		var year = $('#capex-year').val();
		var bc_id = $('#bc_id').val();

		var url = base_url + 'business-center/capex-info/' + year;
    	window.location = url;
	});

	$(document).on('change', '#capex-ag', function(){
		var id = $(this).val();
		var cost_center = $('#cost-center').val();
		var year = $('#capex-year').val();
		$.ajax({
	    	url: base_url + 'business-center/get-subgroup/',
	    	data: {id:id, cost_center:cost_center, year:year},
	    	method: 'POST',
	    	success:function(response){
	    		var parse_response = JSON.parse(response);
	    		if(parse_response['result'] == 1){
	    			tbl_transac_capex.destroy();
	    			$("#tbl-transac-capex > tbody").empty();
		    		$("#tbl-transac-capex > tbody").append(parse_response['assets']);

		    		$("#tbl-transac-capex > thead").empty();
		    		$("#tbl-transac-capex > thead").append(parse_response['header']);
		    		tbl_transac_capex = $('#tbl-transac-capex').DataTable({
						"scrollX": true,
						"scrollY": "300px",
						"fixedHeader": true,
						"bInfo": false,
						"paging": false,
						/*"fixedColumns": {
            				"leftColumns": 4,
            			}*/
					});
					$('.capex-cost-center').select2({
						"placeholder": 'Select Cost Center'
					});
		    	}else{
		    		console.log('Error please contact your administrator');
		    	}
	    	}
		});
	});


	$(document).on('click', '.add-asset-sub', function(e){
		e.preventDefault();
		var id = $(this).attr('data-id');
		var cost_center = $('#cost-center').val();
		var path = $(this).parent().parent();
		$.ajax({
	    	url: base_url + 'business-center/get-asset-subgroup/',
	    	data: {id:id, cost_center:cost_center},
	    	method: 'POST',
	    	success:function(response){
	    		var parse_response = JSON.parse(response);
	    		if(parse_response['result'] == 1){
		    		path.after(parse_response['asset']);
		    	}else{
		    		console.log('Error please contact your administrator');
		    	}

		    	$('.capex-cost-center').select2({
					"placeholder": 'Select Cost Center'
				});
	    	}
		});
	});

	$(document).on('click', '.remove-asg', function(e){
		e.preventDefault();

		$(this).parent().parent().remove();
	});

	$(document).on('paste', 'input.capex-qty', function(e){
		 $this = $(this);

	    setTimeout(function(){
	        var columns = $this.val().trim().replace(/,/g, '').split(/\s+/);
	        var i;
	      	var input =  $this;

	        for(i=0	; i < columns.length; 
	        	i++){

	            input.val(columns[i]);
	            input = input.parent().next().find('.capex-qty');
	        }
	    }, 0);
	});

	$(document).on('keyup', '.capex-qty', function(e){
		e.preventDefault();
		
		var val = $(this).val();
		var qty = parseFloat($(this).val());
		var price = parseFloat($(this).parent().parent().find('.asg-price').val());
		var total = parseFloat($(this).parent().parent().find('.capex-total-price').text());
		//check if valid numbers
		if($.isNumeric(val) || val == ''){
			if(Math.floor(val) >= 0){
				var total_qty = 0;
				total_qty += parseFloat($(this).parent().parent().find('.jan-qty').val()) || 0;
				total_qty += parseFloat($(this).parent().parent().find('.feb-qty').val()) || 0;
				total_qty += parseFloat($(this).parent().parent().find('.mar-qty').val()) || 0;
				total_qty += parseFloat($(this).parent().parent().find('.apr-qty').val()) || 0;
				total_qty += parseFloat($(this).parent().parent().find('.may-qty').val()) || 0;
				total_qty += parseFloat($(this).parent().parent().find('.jun-qty').val()) || 0;
				total_qty += parseFloat($(this).parent().parent().find('.jul-qty').val()) || 0;
				total_qty += parseFloat($(this).parent().parent().find('.aug-qty').val()) || 0;
				total_qty += parseFloat($(this).parent().parent().find('.sep-qty').val()) || 0;
				total_qty += parseFloat($(this).parent().parent().find('.oct-qty').val()) || 0;
				total_qty += parseFloat($(this).parent().parent().find('.nov-qty').val()) || 0;
				total_qty += parseFloat($(this).parent().parent().find('.dec-qty').val()) || 0;

				var total_price = total_qty * price;
				$(this).parent().parent().find('.capex-total-price').text(number_format(total_price));
				$(this).parent().parent().find('.capex-total-qty').text(total_qty);
				
				$(this).removeClass('error-encode');
				$(this).addClass('success-encode');


				var check = validate_capex();
				if(check){
					$(".btn-save-capex").removeAttr('disabled', true);
				}

				var a = 0;
				var total_price = 0;
				$('#tbl-transac-capex').find('input[name^="id"]').each(function(){
		    		var total = $(this).parent().find('.capex-total-price').text();
		    		total_price += parseFloat(total.replace(/,/g,""));
		    		a++;
		    	});
				
				$('.capex-grand-total').text(number_format(total_price, 2));
			}else{
				$(".btn-save-capex").attr('disabled', true);
				$(this).removeClass('success-encode');
	    		$(this).addClass('error-encode');
			}
		}else{
			$(".btn-save-capex").attr('disabled', true);
			$(this).removeClass('success-encode');
    		$(this).addClass('error-encode');
		}
	});

	$(document).on('change', '.capex-cost-center', function(){
		var val = $(this).val();
		if(val){
			$(this).parent().find('.select2-selection').removeClass('error-select');
			$(this).parent().find('.select2-selection').addClass('success-select');
			$(".btn-save-opex").attr('disabled', false);
		}
	});

	function validate_capex(){
    	var a = 0;
    	var btn_counter = 0;
    	$('input[name^="capex"]').each(function(){
    		var qty = $(this).val();
    		a += 1;
    		if($.isNumeric(qty) || qty == ''){
	    		if(Math.floor(qty) >= 0){
		    		btn_counter += 1;
		    	}
	    	}else{
	    		
	    	}
    	});

    	if(btn_counter < a){
    		return false;
    	}else{
    		return true;
    	}
    }

    function validate_capex_cc(){
    	var b = 0;
    	var cc_counter = 0;
    	$('select[name^="cost_center"]').each(function(){
    		var cc = $(this).val();
    		b++;

    		if(cc){
    			cc_counter++;
    		}else{
    			$(this).parent().find('.select2-selection').addClass('error-select');
    		}
    	});

    	if(cc_counter < b){
    		return false;
    	}else{
    		return true;
    	}
    }

	$('#add-capex-form').submit(function(e){
		var check = validate_capex();
		var check_cc = validate_capex_cc();
		
		if(check && check_cc){
			//check of modal is already open allow submit form
			var check_modal = ($("#modal-confirm-capex").data('bs.modal') || {}).isShown;
			if(!check_modal){
				e.preventDefault();
				$('#modal-confirm-capex').modal('show');
			}
		}else{
			$('#modal-confirm-error').modal('show');	
			e.preventDefault();
		}
	});

	$('#add-trans-capex-item').submit(function(e){
		var check = validate_capex();
		var check_cc = validate_capex_cc();
		
		if(check && check_cc){
			//check of modal is already open allow submit form
			var check_modal = ($("#modal-confirm-capex").data('bs.modal') || {}).isShown;
			if(!check_modal){
				e.preventDefault();
				$('#modal-confirm-capex').modal('show');
			}
		}else{
			$('#modal-confirm-error').modal('show');	
			e.preventDefault();
		}
	});

	$(document).on('click', '.remove-trans-item', function(e){
		e.preventDefault();
		var id = $(this).attr('data-id');
		$('#remove-capex-item').find('#id').val(id);
		$('#modal-remove-trans-item').modal('show');
	});

	$(document).on('click', '.update-trans-item', function(e){
		e.preventDefault();
		var id = $(this).attr('data-id');

		$.ajax({
	    	url: base_url + 'business-center/get-capex-item/',
	    	data: {id:id},
	    	method: 'POST',
	    	success:function(response){
	    		var parse_response = JSON.parse(response);
	    		if(parse_response['result'] == 1){
	    			var asset_group = parse_response['info']['asset_group'];
	    			var asset_name = parse_response['info']['asset_name'];
	    			var price = parse_response['info']['price'];
	    			var total = parse_response['info']['total'];
	    			var cost_center = parse_response['info']['cost_center'];
	    			var month = parse_response['info']['month'];
	    			var rank = parse_response['info']['rank'];
	    			var remarks = parse_response['info']['remarks'];

	    			var jan = parse_response['info']['month']['January'];
	    			var feb = parse_response['info']['month']['February'];
	    			var mar = parse_response['info']['month']['March'];
	    			var apr = parse_response['info']['month']['April'];
	    			var may = parse_response['info']['month']['May'];
	    			var jun = parse_response['info']['month']['June'];
	    			var jul = parse_response['info']['month']['July'];
	    			var aug = parse_response['info']['month']['August'];
	    			var sep = parse_response['info']['month']['September'];
	    			var oct = parse_response['info']['month']['October'];
	    			var nov = parse_response['info']['month']['November'];
	    			var dec = parse_response['info']['month']['December'];

	    			$('.asset-price').empty();
	    			$('.asset-price').append(number_format(price));

	    			$('.jan-qty').val(jan);
	    			$('.feb-qty').val(feb);
	    			$('.mar-qty').val(mar);
	    			$('.apr-qty').val(apr);
	    			$('.may-qty').val(may);
	    			$('.jun-qty').val(jun);
	    			$('.jul-qty').val(jul);
	    			$('.aug-qty').val(aug);
	    			$('.sep-qty').val(sep);
	    			$('.oct-qty').val(oct);
	    			$('.nov-qty').val(nov);
	    			$('.dec-qty').val(dec);

	    			$('#update-capex-item > #id').empty();
	    			$('#update-capex-item > #id').val(id);

	    			$('#label-asset > .val').empty();
	    			$('#label-asset > .val').text(asset_name);

	    			$('#label-price > .val').empty();
	    			$('#label-price > .val').text(number_format(price));

	    			$('#label-total-qty > .val').empty();
	    			$('#label-total-qty > .val').text(number_format(total));

	    			var grand_total = price * total;
	    			$('#label-total-amount > .val').empty();
	    			$('#label-total-amount > .val').text(number_format(grand_total, 2));

	    			$('#item-cost-center').empty();
	    			$('#item-cost-center').append(cost_center);

	    			if(asset_group == 'TRANSPORTATION EQUIPMENT'){
	    				$('#edit-capex-rank').empty();
	    				$('#edit-capex-rank').append('<label>Rank: </label>' + rank);

	    				$('#edit-capex-remarks').empty();
	    				$('#edit-capex-remarks').append('<label>Remarks: </label><input type="text" name="remarks" class="form-control input-sm" value="' + remarks + '">');
	    			}

	    			/*$("#tbl-transac-opex > tbody").empty();
		    		$("#tbl-transac-opex > tbody").append(parse_response['gl']);*/
		    		
		    	}else{
		    		console.log('Error please contact your administrator');
		    	}
	    	}
		});

		$('#add-capex-item').find('#id').val(id);
		$('#modal-update-trans-item').modal('show');
	});

	$(document).on('click', '.cancel-capex-btn', function(e){
		e.preventDefault();
		var id = $(this).attr('data-id');
		$('#cancel-capex').find('#id').val(id);
		$('#modal-cancel-capex').modal('show');
	});

	//OPEX

	$('#tbl-opex').DataTable({
		"bInfo" : false,
		"order": []
	});

	var tbl_transac_opex = $('#tbl-transac-opex').DataTable({
		"autoWidth": false,
		"scrollX": true,
		"bInfo" : false,
		"paging" : false,
	});

	var tbl_opex_item = $('#tbl-opex-item').DataTable({
		"scrollX": true,
		"scrollY": "300px",
		"fixedHeader": true,
		"bInfo": false,
		"paging": false,
	});

	$('.opex-cost-center').select2({
		"placeholder": 'Select Cost Center'
	});

	$('#tbl-opex-info').DataTable({
		"bInfo" : false,
		"order": []
	});

	$('#tbl-opex-info-direct-labor').DataTable({
		"bInfo" : false,
		"order": []
	});

	$('#opex-trans-year').datepicker({
	    autoclose: true,
	    format: 'yyyy',
	    viewMode: "years",
		minViewMode: "years",
	});

	$(document).on('change', '#opex-trans-year', function(e){
		var year = $('#opex-year').val();

		var url = base_url + 'business-center/opex-info/' + year;
    	window.location = url;
	});
	
	$('#tbl-view-store-expense').DataTable({
		"bInfo" : false,
		"order": []
	});



	$(document).on('change', '#opex-gl', function(){
		var id = $(this).val();
		var cost_center = $('#cost-center').val();
		var gl_val = $(this).children("option:selected").text();

		$.ajax({
	    	url: base_url + 'business-center/get-gl/',
	    	data: {id:id, cost_center:cost_center},
	    	method: 'POST',
	    	success:function(response){
	    		var parse_response = JSON.parse(response);
	    		if(parse_response['result'] == 1){
	    			tbl_transac_opex.destroy();
	    			$("#tbl-transac-opex > tbody").empty();
		    		$("#tbl-transac-opex > tbody").append(parse_response['gl']);
		    		tbl_transac_opex = $('#tbl-transac-opex').DataTable({
						"scrollX": true,
						"scrollY": "300px",
						"fixedHeader": true,
						"bInfo": false,
						"paging": false,
					});

					if(gl_val == 'STORE EXPENSES'){
						tbl_transac_opex.column(2).visible(false);
						$('#store-brand').removeClass('hide-info');
						$('#store-outlet').removeClass('hide-info');
						$('#store-templates').removeClass('hide-info');
						$('#add-opex-form').attr('action', base_url + 'business-center/add-store-expense');
					}else{
						$('#store-brand').addClass('hide-info');
						$('#store-outlet').addClass('hide-info');
						$('#store-templates').addClass('hide-info');
						$('#add-opex-form').attr('action', base_url + 'business-center/add-opex');
					}

					$('.opex-cost-center').select2({
						"placeholder": 'Select Cost Center'
					});
		    	}else{
		    		console.log('Error please contact your administrator');
		    	}
	    	}
		});
	});

	$(document).on('change', '#opex-brand', function(){
		var brand = $('#opex-brand').val();
		var gl_class_Id = $('#opex-gl-class').val();
		var year = $('#opex-year').val();
		var url = base_url + 'business-center/download-store-expense/' + brand + '/' + year + '/' + gl_class_Id;
		$('#templates-store').attr('href', url);
	});

	$(document).on('change', '#opex-brand-specific-group', function(){
		var brand = $('#opex-brand-specific-group').val();
		var gl_group_Id = $('#gl-group-id').val();
		var gl_class_Id = 0;
		var year = $('#opex-year').val();
		var url = base_url + 'business-center/download-store-expense/' + brand + '/' + year + '/' + gl_class_Id + '/' + gl_group_Id;
		$('#templates-store-specific').attr('href', url);
	});

	$('#templates-store').click(function(e){
		var brand = $('#opex-brand').val();
		if(brand == ''){
			e.preventDefault();
			$('#modal-dl-error').modal('show');

		}
	});

	$('#templates-store-specific').click(function(e){
		var brand = $('#opex-brand-specific-group').val();
		
		if(brand == ''){
			e.preventDefault();
			$('#modal-dl-error').modal('show');

		}
	});

	$(document).on('click', '#opex-brand', function(){
		var brand = $(this).val();
		var year = $('#opex-year').val();
		$.ajax({
	    	url: base_url + 'business-center/get-store/',
	    	data: {id:id, cost_center:cost_center, year:year},
	    	method: 'POST',
	    	success:function(response){
	    		var parse_response = JSON.parse(response);
	    		if(parse_response['result'] == 1){
	    			tbl_transac_opex.destroy();
	    			$("#tbl-transac-opex > tbody").empty();
		    		$("#tbl-transac-opex > tbody").append(parse_response['gl']);
		    		tbl_transac_opex = $('#tbl-transac-opex').DataTable({
						"scrollX": true,
						"scrollY": "300px",
						"fixedHeader": true,
						"bInfo": false,
						"paging": false,
					});
		    		
					$('.opex-cost-center').select2({
						"placeholder": 'Select Cost Center'
					});
		    	}else{
		    		console.log('Error please contact your administrator');
		    	}
	    	}
		});
	});

	$('#add-opex-form').find('#opex-brand').change(function(){

		var id = $(this).val();
		var cost_center = $('#cost-center').val();
		var year = $('#opex-year').val();
		$.ajax({
	    	url: base_url + 'business-center/get-stores/',
	    	data:{id:id, cost_center:cost_center, year:year},
	    	method: 'POST',
	    	success:function(response){
	    		var parse_response = JSON.parse(response);
	    		
	    		if(parse_response['result'] == 1){
	    			$('#add-opex-form').find('#opex-outlet').selectize()[0].selectize.destroy();
	    			$("#add-opex-form").find('#opex-outlet').empty();
	    			$('#add-opex-form').find('#opex-outlet').append(parse_response['info']);

		    	}else{
		    		console.log('Error please contact your administrator.');
		    	}
	    	}
		});
	});

	$('#add-opex-form').find('#opex-brand-specific-group').change(function(){

		var id = $(this).val();
		var cost_center = $('#cost-center').val();;
		var year = $('#opex-year').val();
		$.ajax({
	    	url: base_url + 'business-center/get-stores/',
	    	data:{id:id, cost_center:cost_center, year:year},
	    	method: 'POST',
	    	success:function(response){
	    		var parse_response = JSON.parse(response);
	    		
	    		if(parse_response['result'] == 1){
	    			$('#add-opex-form').find('#opex-outlet').selectize()[0].selectize.destroy();
	    			$("#add-opex-form").find('#opex-outlet').empty();
	    			$('#add-opex-form').find('#opex-outlet').append(parse_response['info']);

		    	}else{
		    		console.log('Error please contact your administrator.');
		    	}
	    	}
		});
	});

	$(document).on('paste', 'input.opex-qty', function(e){
		 $this = $(this);

	    setTimeout(function(){
	        var columns = $this.val().trim().replace(/,/g, '').split(/\s+/);
	        var i;
	      	var input =  $this;

	        for(i=0	; i < columns.length; i++){

	            input.val(columns[i]);
	            input = input.parent().next().find('.opex-qty');
	        }
	    }, 0);
	});

	$(document).on('keyup', '.opex-qty', function(e){
		var qty = $(this).val();
		e.preventDefault();
		if($.isNumeric(qty) || qty == ''){
			if(Math.floor(qty) >= 0){
				var total_qty = 0;
				total_qty += parseFloat($(this).parent().parent().find('.jan-qty').val()) || 0;
				total_qty += parseFloat($(this).parent().parent().find('.feb-qty').val()) || 0;
				total_qty += parseFloat($(this).parent().parent().find('.mar-qty').val()) || 0;
				total_qty += parseFloat($(this).parent().parent().find('.apr-qty').val()) || 0;
				total_qty += parseFloat($(this).parent().parent().find('.may-qty').val()) || 0;
				total_qty += parseFloat($(this).parent().parent().find('.jun-qty').val()) || 0;
				total_qty += parseFloat($(this).parent().parent().find('.jul-qty').val()) || 0;
				total_qty += parseFloat($(this).parent().parent().find('.aug-qty').val()) || 0;
				total_qty += parseFloat($(this).parent().parent().find('.sep-qty').val()) || 0;
				total_qty += parseFloat($(this).parent().parent().find('.oct-qty').val()) || 0;
				total_qty += parseFloat($(this).parent().parent().find('.nov-qty').val()) || 0;
				total_qty += parseFloat($(this).parent().parent().find('.dec-qty').val()) || 0;

				$(this).parent().parent().find('.opex-total-qty').text(number_format(total_qty, 2));

				grand_total = 0; 
				$('#tbl-transac-opex').find('input[name^="id"]').each(function(){
	    			var total = $(this).parent().find('.opex-total-qty').text();
	    			grand_total += parseFloat(total.replace(/,/g,""));
		    	});

		    	$('.opex-grand-total').text(number_format(grand_total, 2));

		    	$(this).removeClass('error-encode');
		    	$(this).addClass('success-encode');
		    	$(".btn-save-opex").attr('disabled', false);
		    }else{    	
		    	$(this).removeClass('success-encode');
		    	$(this).addClass('error-encode');
		    	$(".btn-save-opex").attr('disabled', true);
		    }
		}else{
			$(this).removeClass('success-encode');
	    	$(this).addClass('error-encode');
	    	$(".btn-save-opex").attr('disabled', true);
		}


	});

	$(document).on('change', '.opex-cost-center', function(){
		var val = $(this).val();
		if(val){
			$(this).parent().find('.select2-selection').removeClass('error-select');
			$(this).parent().find('.select2-selection').addClass('success-select');
			$(".btn-save-opex").attr('disabled', false);
		}
	});

	$('#add-opex-form').submit(function(e){
		var check = validate_opex();
		var check_cc = validate_opex_cc();
		
		if(check && check_cc){
			//check of modal is already open allow submit form
			var check_modal = ($("#modal-confirm-opex").data('bs.modal') || {}).isShown;
			if(!check_modal){
				e.preventDefault();
				$('#modal-confirm-opex').modal('show');
			}
		}else{
			console.log('error');
			$('#modal-confirm-error').modal('show');	
			e.preventDefault();
		}
	});

	$('#add-trans-opex-item').submit(function(e){
		var check = validate_opex();
		var check_cc = validate_opex_cc();
		
		if(check && check_cc){
			//check of modal is already open allow submit form
			var check_modal = ($("#modal-confirm-opex").data('bs.modal') || {}).isShown;
			if(!check_modal){
				e.preventDefault();
				$('#modal-confirm-opex').modal('show');
			}
		}else{
			$('#modal-confirm-error').modal('show');	
			e.preventDefault();
		}
	});

	$('#add-trans-opex-item').submit(function(e){
		var check = validate_capex();
		var check_cc = validate_capex_cc();
		
		if(check && check_cc){
			//check of modal is already open allow submit form
			var check_modal = ($("#modal-confirm-opex").data('bs.modal') || {}).isShown;
			if(!check_modal){
				e.preventDefault();
				$('#modal-confirm-opex').modal('show');
			}
		}else{
			$('#modal-confirm-error').modal('show');	
			e.preventDefault();
		}
	});

	function validate_opex(){
    	var a = 0;
    	var btn_counter = 0;
    	$('input[name^="opex"]').each(function(){
    		var qty = $(this).val();
    		a += 1;
    		if($.isNumeric(qty) || qty == ''){
	    		if(Math.floor(qty) >= 0){
		    		btn_counter += 1;
		    	}
	    	}else{
	    		
	    	}
    	});

    	if(btn_counter < a){
    		return false;
    	}else{
    		return true;
    	}
    }

    function validate_opex_cc(){
    	var b = 0;
    	var cc_counter = 0;
    	$('select[name^="cost_center"]').each(function(){
    		var cc = $(this).val();
    		b++;

    		if(cc){
    			cc_counter++;
    		}else{
    			$(this).parent().find('.select2-selection').addClass('error-select');
    		}
    	});

    	if(cc_counter < b){
    		return false;
    	}else{
    		return true;
    	}
    }

	$(document).on('click', '.add-gl-sub', function(e){
		e.preventDefault();
		var id = $(this).attr('data-id');
		var cost_center = $('#cost-center').val();
		var path = $(this).parent().parent();
		$.ajax({
	    	url: base_url + 'business-center/get-gl-sub/',
	    	data: {id:id, cost_center:cost_center},
	    	method: 'POST',
	    	success:function(response){
	    		var parse_response = JSON.parse(response);
	    		if(parse_response['result'] == 1){
		    		path.after(parse_response['gl']);
		    	}else{
		    		console.log('Error please contact your administrator');
		    	}

		    	$('.opex-cost-center').select2({
					"placeholder": 'Select Cost Center'
				});
	    	}
		});
	});

	$('#opex-gl, #opex-brand, #opex-outlet, #opex-brand-specific-group').select2({
		"placeholder": 'Select Cost Center'
	});

	$(document).on('click', '.remove-gl-sub', function(e){
		e.preventDefault();

		$(this).parent().parent().remove();
	});

	$(document).on('click', '.remove-opex-item', function(e){
		e.preventDefault();
		var id = $(this).attr('data-id');
		$('#remove-opex-item').find('#id').val(id);

		var direct_labor = $(this).attr('data-direct-labor');
		$('#remove-opex-item').find('#direct-labor').val(direct_labor);
		$('#modal-remove-opex-item').modal('show');
	});

	$(document).on('click', '.remove-sw-item', function(e){
		e.preventDefault();
		var id = $(this).attr('data-id');
		$('#remove-sw-item').find('#id').val(id);
		$('#modal-remove-sw-item').modal('show');
	});

	$(document).on('click', '.update-opex-item', function(e){
		e.preventDefault();
		var id = $(this).attr('data-id');
		var cost_center = $('#cost-center').val();
		$.ajax({
	    	url: base_url + 'business-center/get-opex-item/',
	    	data: {id:id, cost_center:cost_center},
	    	method: 'POST',
	    	success:function(response){
	    		var parse_response = JSON.parse(response);
	    		if(parse_response['result'] == 1){
	    			var gl_group = parse_response['info']['gl_group'];
	    			var total = parse_response['info']['total'];
	    			var cost_center = parse_response['info']['cost_center'];
	    			var month = parse_response['info']['month'];

	    			var jan = parse_response['info']['month']['January'];
	    			var feb = parse_response['info']['month']['February'];
	    			var mar = parse_response['info']['month']['March'];
	    			var apr = parse_response['info']['month']['April'];
	    			var may = parse_response['info']['month']['May'];
	    			var jun = parse_response['info']['month']['June'];
	    			var jul = parse_response['info']['month']['July'];
	    			var aug = parse_response['info']['month']['August'];
	    			var sep = parse_response['info']['month']['September'];
	    			var oct = parse_response['info']['month']['October'];
	    			var nov = parse_response['info']['month']['November'];
	    			var dec = parse_response['info']['month']['December'];

	    			$('.jan-qty').val(jan);
	    			$('.feb-qty').val(feb);
	    			$('.mar-qty').val(mar);
	    			$('.apr-qty').val(apr);
	    			$('.may-qty').val(may);
	    			$('.jun-qty').val(jun);
	    			$('.jul-qty').val(jul);
	    			$('.aug-qty').val(aug);
	    			$('.sep-qty').val(sep);
	    			$('.oct-qty').val(oct);
	    			$('.nov-qty').val(nov);
	    			$('.dec-qty').val(dec);

	    			$('#update-opex-item > #id').empty();
	    			$('#update-opex-item > #id').val(id);

	    			$('#label-gl > .val').empty();
	    			$('#label-gl > .val').text(gl_group);

	    			$('#label-total-amount > .val').text(number_format(total, 2));

	    			$('#item-cost-center').empty();
	    			$('#item-cost-center').append(cost_center);tbl-unbudgeted
		    	}else{
		    		console.log('Error please contact your administrator');
		    	}
	    	}
		});

		$('#modal-update-opex-item').modal('show');
	});

	$(document).on('click', '.update-sw-item', function(e){
		e.preventDefault();
		var id = $(this).attr('data-id');
		$.ajax({
	    	url: base_url + 'business-center/get-sw-item/',
	    	data: {id:id},
	    	method: 'POST',
	    	success:function(response){
	    		var parse_response = JSON.parse(response);
	    		if(parse_response['result'] == 1){
	    			var gl_group = parse_response['info']['gl_group'];
	    			var total = parse_response['info']['total'];
	    			var emp_name = parse_response['info']['name'];
	    			var month = parse_response['info']['month'];

	    			var jan = parse_response['info']['month']['January'];
	    			var feb = parse_response['info']['month']['February'];
	    			var mar = parse_response['info']['month']['March'];
	    			var apr = parse_response['info']['month']['April'];
	    			var may = parse_response['info']['month']['May'];
	    			var jun = parse_response['info']['month']['June'];
	    			var jul = parse_response['info']['month']['July'];
	    			var aug = parse_response['info']['month']['August'];
	    			var sep = parse_response['info']['month']['September'];
	    			var oct = parse_response['info']['month']['October'];
	    			var nov = parse_response['info']['month']['November'];
	    			var dec = parse_response['info']['month']['December'];

	    			$('.jan-qty').val(jan);
	    			$('.feb-qty').val(feb);
	    			$('.mar-qty').val(mar);
	    			$('.apr-qty').val(apr);
	    			$('.may-qty').val(may);
	    			$('.jun-qty').val(jun);
	    			$('.jul-qty').val(jul);
	    			$('.aug-qty').val(aug);
	    			$('.sep-qty').val(sep);
	    			$('.oct-qty').val(oct);
	    			$('.nov-qty').val(nov);
	    			$('.dec-qty').val(dec);

	    			$('#update-sw-item > #id').empty();
	    			$('#update-sw-item > #id').val(id);

	    			$('#label-gl > .val').empty();
	    			$('#label-gl > .val').text(gl_group);

	    			$('#label-total-amount > .val').text(number_format(total, 2));

	    			$('.label-emp').empty();
	    			$('.label-emp').append(emp_name);
		    	}else{
		    		console.log('Error please contact your administrator');
		    	}
	    	}
		});

		$('#modal-update-opex-item').modal('show');
	});

	$(document).on('click', '.cancel-direct-labor-btn', function(e){
		e.preventDefault();
		var id = $(this).attr('data-id');
		$('#cancel-direct-labor').find('#id').val(id);
		
		$('#modal-cancel-direct-labor').modal('show');
	});

	$(document).on('click', '.cancel-opex-btn', function(e){
		e.preventDefault();
		var id = $(this).attr('data-id');
		$('#cancel-opex').find('#id').val(id);
		
		$('#modal-cancel-opex').modal('show');
	});

	$(document).on('click', '.cancel-store-expense-btn', function(e){
		e.preventDefault();
		var id = $(this).attr('data-id');
		var trans = $(this).attr('data-trans');
		var gl_group_id = $(this).attr('data-gl-group-id');
		$('#cancel-store-expense').find('#gl-group-id').val(gl_group_id);

		$('#cancel-store-expense').find('#id').val(id);
		$('#cancel-store-expense').find('#trans_id').val(trans);
		$('#modal-cancel-store-expense').modal('show');
	});

	$(document).on('click', '.update-store-expense-btn', function(e){
		e.preventDefault();
		var id = $(this).attr('data-id');
		var direct_labor = $(this).attr('data-direct-labor');
		var cost_center = $('#cost-center').val();
		$.ajax({
	    	url: base_url + 'business-center/get-opex-item/',
	    	data: {id:id, cost_center:cost_center, direct_labor: direct_labor},
	    	method: 'POST',
	    	success:function(response){
	    		var parse_response = JSON.parse(response);
	    		if(parse_response['result'] == 1){
	    			var gl_group = parse_response['info']['gl_group'];
	    			var total = parse_response['info']['total'];
	    			var cost_center = parse_response['info']['cost_center'];
	    			var month = parse_response['info']['month'];

	    			var jan = parse_response['info']['month']['January'];
	    			var feb = parse_response['info']['month']['February'];
	    			var mar = parse_response['info']['month']['March'];
	    			var apr = parse_response['info']['month']['April'];
	    			var may = parse_response['info']['month']['May'];
	    			var jun = parse_response['info']['month']['June'];
	    			var jul = parse_response['info']['month']['July'];
	    			var aug = parse_response['info']['month']['August'];
	    			var sep = parse_response['info']['month']['September'];
	    			var oct = parse_response['info']['month']['October'];
	    			var nov = parse_response['info']['month']['November'];
	    			var dec = parse_response['info']['month']['December'];

	    			$('.jan-qty').val(jan);
	    			$('.feb-qty').val(feb);
	    			$('.mar-qty').val(mar);
	    			$('.apr-qty').val(apr);
	    			$('.may-qty').val(may);
	    			$('.jun-qty').val(jun);
	    			$('.jul-qty').val(jul);
	    			$('.aug-qty').val(aug);
	    			$('.sep-qty').val(sep);
	    			$('.oct-qty').val(oct);
	    			$('.nov-qty').val(nov);
	    			$('.dec-qty').val(dec);

	    			$('#update-store-expense-item > #id').empty();
	    			$('#update-store-expense-item > #id').val(id);

	    			$('#label-gl > .val').empty();
	    			$('#label-gl > .val').text(gl_group);

	    			$('#label-total-amount > .val').text(number_format(total, 2));

	    			$('#item-cost-center').empty();
	    			$('#item-cost-center').append(cost_center);
		    	}else{
		    		console.log('Error please contact your administrator');
		    	}
	    	}
		});

		$('#modal-update-opex-item').modal('show');
	});

	$(document).on('click', '.cancel-sw-btn', function(e){
		e.preventDefault();
		var id = $(this).attr('data-id');
		$('#cancel-sw-opex').find('#id').val(id);
		$('#modal-cancel-sw-opex').modal('show');
	});


	/*Manpower*/

	$('#manpower-trans-year').datepicker({
	    autoclose: true,
	    format: 'yyyy',
	    viewMode: "years",
		minViewMode: "years",
	});

	$(document).on('change', '#manpower-trans-year', function(e){
		var year = $('#manpower-year').val();
		
		var url = base_url + 'business-center/manpower/' + year;
    	window.location = url;
	});

	$(document).on('click', '.edit-manpower', function(e){
		e.preventDefault();
		var id = $(this).attr('data-id');
		$.ajax({
	    	url: base_url + 'business-center/modal-manpower/',
	    	data: {id:id},
	    	method: 'POST',
	    	success:function(response){
	    		var parse_response = JSON.parse(response);
	    		
	    		if(parse_response['result'] == 1){
	    			
	    			var cost_center = parse_response['info']['cost_center'];
	    			var manpower_position = parse_response['info']['manpower_position'];
	    			var manpower_old = parse_response['info']['manpower_old'];
	    			var manpower_new = parse_response['info']['manpower_new'];
	    			var rank = parse_response['info']['rank'];

	    			$("#update-manpower").find("#id").val(id);

	    			$("#update-manpower").find("#edit-manpower-cost-center").empty();
		    		$("#update-manpower").find("#edit-manpower-cost-center").append(cost_center);

		    		$("#update-manpower").find("#edit-manpower-rank").empty();
		    		$("#update-manpower").find("#edit-manpower-rank").append(rank);

		    		$("#update-manpower").find("#edit-manpower-old").val(manpower_old);
		    		$("#update-manpower").find("#edit-manpower-new").val(manpower_new);

		    		$("#update-manpower").find("#edit-manpower-position").val(manpower_position);

		    		
		    		$('#modal-edit-manpower').modal({show:true});
		    	}else{
		    		console.log('Error please contact your administrator');
		    	}
	    	}
		});
	});

	$(document).on('click', '.remove-manpower', function(e){
		e.preventDefault();
		var id = $(this).attr('data-id');
		$("#modal-remove-manpower").find("#id").val(id);
		$('#modal-remove-manpower').modal({show:true});
	});



	/*Employee Module*/

	$('#emp-trans-year').datepicker({
	    autoclose: true,
	    format: 'yyyy',
	    viewMode: "years",
		minViewMode: "years",
	});

	$(document).on('change', '#emp-trans-year', function(e){
		var year = $('#emp-year').val();
		
		var url = base_url + 'business-center/employees/' + year;
    	window.location = url;
	});
	
	$('#tbl-employee').DataTable({
		"bInfo" : false,
		"order": []
	});

	$(document).on('change', '#emp-type', function(){
		var  type = $("#emp-type option:selected").text();
		if(type == 'NEW'){
			
			$.ajax({
		    	url: base_url + 'business-center/get-new-emp/',
		    	method: 'GET',
		    	success:function(response){
		    		var parse_response = JSON.parse(response);
		    		if(parse_response['result'] == 1){
		    			var format = parse_response['info'];
		    			$('#emp-no').val(format);
		    			$('#emp-no').attr('readonly', true);
		    			$('#outlet').val(format);
		    			$('#outlet').attr('readonly', true);
						$('#add-employee').bootstrapValidator('revalidateField', 'emp_no');
						
			    	}else{
			    		console.log('Error please contact your administrator');
			    	}
		    	}
			});
		}else{
			$('#emp-no').attr('readonly', false);
		}
	});

	$(document).on('change', '#emp-unit', function(){
		var unit = $(this).val();
		if(unit !== ''){
			$.ajax({
		    	url: base_url + 'business-center/get-emp-cost-center/',
		    	method: 'POST',
		    	data: {unit:unit},
		    	success:function(response){
		    		var parse_response = JSON.parse(response);
		    		if(parse_response['result'] == 1){
		    			var cost_center = parse_response['info'];
		    			$('#emp-cost-center').empty();
		    			$('#emp-cost-center').append(cost_center);
			    	}else{
			    		console.log('Error please contact your administrator');
			    	}
		    	}
			});
		}
	});

	$("#add-employee").bootstrapValidator({
		framework: 'bootstrap',
		excluded: [':disabled'],
		fields:{
			fname: {
				validators: {
					notEmpty: {
						message: 'First Name is required!'
					}
				}
			},

			lname: {
				validators: {
					notEmpty: {
						message: 'Last Name is required!'
					}
				}
			},

			type: {
				validators: {
					notEmpty: {
						message: 'Type is required!'
					}
				}
			},

			emp_no: {
				validators: {
					notEmpty: {
						message: 'Employee no. is required!'
					}
				}	
			},

			salary: {
				validators: {
					notEmpty: {
						message: 'Salary is required!'
					},

					integer:{
						message: 'Invalid Number!'
					}
				}	
			},

			rank: {
				validators: {
					notEmpty: {
						message: 'Rank is required!'
					}
				}	
			},

			unit: {
				validators: {
					notEmpty: {
						message: 'Unit is required!'
					}
				}	
			},

			cost_center: {
				validators: {
					notEmpty: {
						message: 'Cost center is required!'
					}
				}	
			},
		}
	});

	$(document).on('click', '.edit-emp', function(e){
		e.preventDefault();
		var id = $(this).attr('data-id');
		$.ajax({
	    	url: base_url + 'business-center/modal-employee/',
	    	data: {id:id},
	    	method: 'POST',
	    	success:function(response){
	    		var parse_response = JSON.parse(response);
	    		
	    		if(parse_response['result'] == 1){
	    			var fname = parse_response['info']['fname'];
	    			var lname = parse_response['info']['lname'];
	    			var emp_no = parse_response['info']['emp_no'];
	    			var unit = parse_response['info']['unit'];
	    			var cost_center = parse_response['info']['cost_center'];
	    			var type = parse_response['info']['type'];
	    			var rank = parse_response['info']['rank'];
	    			var salary = parse_response['info']['salary'];

	    			$("#update-employee").find("#id").val(id);
		    		$("#update-employee").find("#edit-emp-fname").val(fname);
		    		$("#update-employee").find("#edit-emp-lname").val(lname);
		    		$("#update-employee").find("#edit-emp-no").val(emp_no);
		    		$("#update-employee").find("#edit-emp-salary").val(salary);

		    		$("#update-employee").find("#edit-emp-type").empty();
		    		$("#update-employee").find("#edit-emp-type").append(type);

		    		$("#update-employee").find("#edit-emp-rank").empty();
		    		$("#update-employee").find("#edit-emp-rank").append(rank);

		    		$("#update-employee").find("#edit-emp-unit").empty();
		    		$("#update-employee").find("#edit-emp-unit").append(unit);

		    		$("#update-employee").find("#edit-emp-cost-center").empty();
		    		$("#update-employee").find("#edit-emp-cost-center").append(cost_center);

		    		if(type == 'NEW'){
		    			$('#edit-emp-no').attr('disabled', true);
		    		}else{
		    			$('#edit-emp-no').attr('disabled', false);
		    		}
		    		
		    		$('#modal-edit-employee').modal({show:true});
		    	}else{
		    		console.log('Error please contact your administrator');
		    	}
	    	}
		});
	});

	$(document).on('change', '#edit-emp-unit', function(){
		var unit = $(this).val();
		if(unit !== ''){
			$.ajax({
		    	url: base_url + 'business-center/get-emp-cost-center/',
		    	method: 'POST',
		    	data: {unit:unit},
		    	success:function(response){
		    		var parse_response = JSON.parse(response);
		    		if(parse_response['result'] == 1){
		    			var cost_center = parse_response['info'];
		    			$('#edit-emp-cost-center').empty();
		    			$('#edit-emp-cost-center').append(cost_center);
			    	}else{
			    		console.log('Error please contact your administrator');
			    	}
		    	}
			});
		}
	});

	$(document).on('click', '.deactivate-emp', function(e){
		e.preventDefault();
		var id = $(this).attr('data-id');
		$("#modal-deactivate-employee").find("#id").val(id);
		$('#modal-deactivate-employee').modal({show:true});
	});

	$(document).on('click', '.activate-emp', function(e){
		e.preventDefault();
		var id = $(this).attr('data-id');
		$("#modal-activate-employee").find("#id").val(id);
		$('#modal-activate-employee').modal({show:true});
	});

	/*ALW for Live Sales*/

	$('#tbl-view-alw').DataTable({
		"bInfo" : false,
		"order": []
	});

	$('#alw-trans-year').datepicker({
	    autoclose: true,
	    format: 'yyyy',
	    viewMode: "years",
		minViewMode: "years",
	});

	$(document).on('change', '#alw-trans-year', function(e){
		var year = $('#alw-year').val();

		var url = base_url + 'business-center/live-alw/' + year;
    	window.location = url;
	});

	$("#add-live-alw, #update-live-alw").bootstrapValidator({
		framework: 'bootstrap',
		excluded: [':disabled'],
		fields:{
			bc: {
				validators: {
					notEmpty: {
						message: '&nbsp;'
					}
				}
			},

			jan_qty: {
				validators: {
					notEmpty: {
						message: 'Required'
					},

					numeric:{
						message: 'Invalid!'
					}
				}
			},

			feb_qty: {
				validators: {
					notEmpty: {
						message: 'Required'
					},

					numeric:{
						message: 'Invalid!'
					}
				}
			},

			mar_qty: {
				validators: {
					notEmpty: {
						message: 'Required'
					},

					numeric:{
						message: 'Invalid!'
					}
				}	
			},

			apr_qty: {
				validators: {
					notEmpty: {
						message: 'Required'
					},

					numeric:{
						message: 'Invalid!'
					}
				}	
			},

			may_qty: {
				validators: {
					notEmpty: {
						message: 'Required'
					},

					numeric:{
						message: 'Invalid!'
					}
				}	
			},

			jun_qty: {
				validators: {
					notEmpty: {
						message: 'Required'
					},

					numeric:{
						message: 'Invalid!'
					}
				}	
			},

			jul_qty: {
				validators: {
					notEmpty: {
						message: 'Required'
					},

					numeric:{
						message: 'Invalid!'
					}
				}	
			},

			aug_qty: {
				validators: {
					notEmpty: {
						message: 'Required'
					},

					numeric:{
						message: 'Invalid!'
					}
				}	
			},

			sep_qty: {
				validators: {
					notEmpty: {
						message: 'Required'
					},

					numeric:{
						message: 'Invalid!'
					}
				}	
			},

			oct_qty: {
				validators: {
					notEmpty: {
						message: 'Required;'
					},

					numeric:{
						message: 'Invalid!'
					}
				}	
			},

			nov_qty: {
				validators: {
					notEmpty: {
						message: 'Required;'
					},

					numeric:{
						message: 'Invalid!'
					}
				}	
			},

			dec_qty: {
				validators: {
					notEmpty: {
						message: 'Required'
					},

					numeric:{
						message: 'Invalid!'
					}
				}	
			},
		}
	});

	$(document).on('click', '.edit-alw', function(e){
		e.preventDefault();
		var id = $(this).attr('data-id');
		if(id !== ''){
			$.ajax({
		    	url: base_url + 'business-center/get-alw/',
		    	method: 'POST',
		    	data: {id:id},
		    	success:function(response){
		    		var parse_response = JSON.parse(response);
		    		if(parse_response['result'] == 1){
		    			$('#update-live-alw').find('#id').val(id);
		    			var alw = parse_response['info'];

		    			var jan = alw['alw_jan'];
		    			var feb = alw['alw_feb'];
		    			var mar = alw['alw_mar'];
		    			var apr = alw['alw_apr'];
		    			var may = alw['alw_may'];
		    			var jun = alw['alw_jun'];
		    			var jul = alw['alw_jul'];
		    			var aug = alw['alw_aug'];
		    			var sep = alw['alw_sep'];
		    			var oct = alw['alw_oct'];
		    			var nov = alw['alw_nov'];
		    			var dec = alw['alw_dec'];

		    			$('#update-live-alw').find('#jan_qty').val(jan);
		    			$('#update-live-alw').find('#feb_qty').val(feb);
		    			$('#update-live-alw').find('#mar_qty').val(mar);
		    			$('#update-live-alw').find('#apr_qty').val(apr);
		    			$('#update-live-alw').find('#may_qty').val(may);
		    			$('#update-live-alw').find('#jun_qty').val(jun);
		    			$('#update-live-alw').find('#jul_qty').val(jul);
		    			$('#update-live-alw').find('#aug_qty').val(aug);
		    			$('#update-live-alw').find('#sep_qty').val(sep);
		    			$('#update-live-alw').find('#oct_qty').val(oct);
		    			$('#update-live-alw').find('#nov_qty').val(nov);
		    			$('#update-live-alw').find('#dec_qty').val(dec);
		    			$("#update-live-alw").bootstrapValidator('resetForm');
		    			$('#modal-update-alw').modal('show');
			    	}else{
			    		console.log('Error please contact your administrator');
			    	}
		    	}
			});
		}
	});

	$(document).on('click', '.cancel-alw', function(e){
		e.preventDefault();
		var id = $(this).attr('data-id');
		$('#cancel-live-alw').find('#id').val(id);
		$('#modal-confirm-alw').modal('show');
	});

	/*Tactical Price Module*/

	$('#tactical-trans-year').datepicker({
	    autoclose: true,
	    format: 'yyyy',
	    viewMode: "years",
		minViewMode: "years",
	});

	$(document).on('change', '#tactical-trans-year', function(e){
		var year = $('#tactical-year').val();
		
		var url = base_url + 'business-center/tactical-info/' + year;
    	window.location = url;
	});

	$('#add-tactical-price #outlet').select2({
		"placeholder": 'Select Outlet'
	});

	$('#tbl-add-tactical').DataTable({
		paging: false
	});

	$(document).on('change', '#add-tactical-price #brand', function(){
		var brand = $(this).val();
		var bc = $('#add-tactical-price #id').val();
		if(brand !== ''){
			$.ajax({
		    	url: base_url + 'business-center/get-tactical-price-store/',
		    	method: 'POST',
		    	data: {brand:brand, bc:bc},
		    	success:function(response){
		    		var parse_response = JSON.parse(response);
		    		if(parse_response['result'] == 1){
		    			$('#add-tactical-price #outlet').selectize()[0].selectize.destroy();
		    			var outlet = parse_response['info']['outlet'];
		    			var material = parse_response['info']['material'];
		    			$('#add-tactical-price #outlet').empty();
		    			$('#add-tactical-price #outlet').append(outlet);

		    			$('#tbl-add-tactical').dataTable().fnDestroy();
		    			$('#tbl-add-tactical > tbody').empty();
		    			$('#tbl-add-tactical > tbody').append(material);
		    			
		    			$('#tbl-add-tactical').DataTable({
		    				"scrollX": true,
							"scrollY": "300px",
							"fixedHeader": true,
							"bInfo": false,
							"paging": false,
		    			});

		    			$('#add-tactical-price #outlet').select2({
							"placeholder": 'Select Outlet'
		    			});
			    	}else{
			    		console.log('Error please contact your administrator');
			    	}
		    	}
			});
		}
	});


	$(document).on('click', '.slider-tactical', function(e){
    	e.preventDefault();
    	var row_tr = $(this).closest('tr').index();
    	$('#modal-slider-tactical').find('#id').val(row_tr);
    	$('#modal-slider-tactical').modal('show');
    });

    $(document).on('change', '#slider-tactical', function(){
    	var qty = $(this).val();
    	$('#slider-tactical-val').val(qty);
    });

    $(document).on('change', '#slider-tactical-start', function(){
    	var start = parseInt($(this).val());
    	var end = parseInt($('#slider-tactical-end').val());
    	
    	if(start <= end){
	    	$('#slider-tactical-start-val').empty();
	    	$('#slider-tactical-start-val').text(number_format(start));
	    }else{
	    	$('#slider-tactical-end-val').empty();
	    	$('#slider-tactical-end-val').text(number_format(start));
	    	$('#slider-tactical-end').val(start);

	    	$('#slider-tactical-start-val').empty();
	    	$('#slider-tactical-start-val').text(number_format(start));
	    }
    });

    $(document).on('change', '#slider-tactical-end', function(){
    	var end = parseInt($(this).val());
    	var start = parseInt($('#slider-tactical-start').val());
    	if(end >= start){
    		
	    	$('#slider-tactical-end-val').empty();
	    	$('#slider-tactical-end-val').text(number_format(end));
	    }else{
	    	$('#slider-tactical-start-val').empty();
	    	$('#slider-tactical-start-val').text(number_format(end));
	    	$('#slider-tactical-start').val(end);

	    	$('#slider-tactical-end-val').empty();
	    	$('#slider-tactical-end-val').text(number_format(end));
	    }
    });

    $(document).on('click', '.slider-tactical-btn', function(e){
		e.preventDefault();
		var opex = $('#slider-tactical-val').val();
		var opex_start = parseInt($('#slider-tactical-start').val());
		var opex_end = parseInt($('#slider-tactical-end').val());
		
		var count = parseInt($('#modal-slider-tactical').find('#id').val()) + 1;
		var counter = 0;
		var total = 0;
		var total_qty = 0;
		$(document).find('#tbl-add-tactical tr').eq(count).find('input').each(function(){
			if(opex_start <= counter  && opex_end >= counter){
				$(this).val(opex);

			}

			var val = parseInt($(this).val());
			if(isNaN(val)){
				total += 0;
			}else{
				total += val;
			}
			counter++;
		});
		$('#modal-slider-tactical').modal('hide');
		$('#add-tactical-price').bootstrapValidator('revalidateField', 'jan_qty');
		$('#add-tactical-price').bootstrapValidator('revalidateField', 'feb_qty');
		$('#add-tactical-price').bootstrapValidator('revalidateField', 'mar_qty');
		$('#add-tactical-price').bootstrapValidator('revalidateField', 'apr_qty');
		$('#add-tactical-price').bootstrapValidator('revalidateField', 'may_qty');
		$('#add-tactical-price').bootstrapValidator('revalidateField', 'jun_qty');
		$('#add-tactical-price').bootstrapValidator('revalidateField', 'jul_qty');
		$('#add-tactical-price').bootstrapValidator('revalidateField', 'aug_qty');
		$('#add-tactical-price').bootstrapValidator('revalidateField', 'sep_qty');
		$('#add-tactical-price').bootstrapValidator('revalidateField', 'oct_qty');
		$('#add-tactical-price').bootstrapValidator('revalidateField', 'nov_qty');
		$('#add-tactical-price').bootstrapValidator('revalidateField', 'dec_qty');
		counter = 1;
		$(document).find('#tbl-update-tactical tr').eq(count).find('input').each(function(){
			if(opex_start <= counter  && opex_end >= counter){
				$(this).val(opex);
			}

			var val = parseInt($(this).val());
			if(isNaN(val)){
				total += 0;
			}else{
				total += val;
			}
			counter++;
		});

		$('#modal-slider-tactical').modal('hide');
		$('#update-tactical-price').bootstrapValidator('revalidateField', 'jan_qty');
		$('#update-tactical-price').bootstrapValidator('revalidateField', 'feb_qty');
		$('#update-tactical-price').bootstrapValidator('revalidateField', 'mar_qty');
		$('#update-tactical-price').bootstrapValidator('revalidateField', 'apr_qty');
		$('#update-tactical-price').bootstrapValidator('revalidateField', 'may_qty');
		$('#update-tactical-price').bootstrapValidator('revalidateField', 'jun_qty');
		$('#update-tactical-price').bootstrapValidator('revalidateField', 'jul_qty');
		$('#update-tactical-price').bootstrapValidator('revalidateField', 'aug_qty');
		$('#update-tactical-price').bootstrapValidator('revalidateField', 'sep_qty');
		$('#update-tactical-price').bootstrapValidator('revalidateField', 'oct_qty');
		$('#update-tactical-price').bootstrapValidator('revalidateField', 'nov_qty');
		$('#update-tactical-price').bootstrapValidator('revalidateField', 'dec_qty');
	});

    $(document).on('click', '.remove-tactical', function(e){
		e.preventDefault();
		$(this).parent().parent().remove();
	});

    $(document).on('click', '.cancel-tactical', function(e){
		e.preventDefault();
		var id = $(this).attr('data-id');
		$('#cancel-tactical-price').find('#id').val(id);
		$('#modal-confirm-tactical').modal('show');
	});

	$("#add-tactical-price, #update-tactical-price").bootstrapValidator({
		framework: 'bootstrap',
		excluded: [':disabled'],
		fields:{
			brand: {
				validators: {
					notEmpty: {
						message: '&nbsp;'
					}
				}
			},

			outlet: {
				validators: {
					notEmpty: {
						message: '&nbsp;'
					}
				}
			},

			'tactical[jan]': {
				validators: {
					notEmpty: {
						message: 'Required'
					},

					numeric:{
						message: 'Invalid!'
					}
				}
			}
		}
	});

	$(document).on('click', '.edit-tactical', function(e){
		e.preventDefault();
		var id = $(this).attr('data-id');
		if(id !== ''){
			$.ajax({
		    	url: base_url + 'business-center/get-tactical/',
		    	method: 'POST',
		    	data: {id:id},
		    	success:function(response){
		    		var parse_response = JSON.parse(response);
		    		if(parse_response['result'] == 1){
		    			$('#update-tactical-price').find('#id').val(id);
		    			var tactical = parse_response['info'];
		    			var outlet = parse_response['info']['outlet'];
		    			var material = parse_response['info']['material'];

		    			var jan = tactical['tactical_jan'];
		    			var feb = tactical['tactical_feb'];
		    			var mar = tactical['tactical_mar'];
		    			var apr = tactical['tactical_apr'];
		    			var may = tactical['tactical_may'];
		    			var jun = tactical['tactical_jun'];
		    			var jul = tactical['tactical_jul'];
		    			var aug = tactical['tactical_aug'];
		    			var sep = tactical['tactical_sep'];
		    			var oct = tactical['tactical_oct'];
		    			var nov = tactical['tactical_nov'];
		    			var dec = tactical['tactical_dec'];

		    			$('#update-tactical-price').find('#jan_qty').val(jan);
		    			$('#update-tactical-price').find('#feb_qty').val(feb);
		    			$('#update-tactical-price').find('#mar_qty').val(mar);
		    			$('#update-tactical-price').find('#apr_qty').val(apr);
		    			$('#update-tactical-price').find('#may_qty').val(may);
		    			$('#update-tactical-price').find('#jun_qty').val(jun);
		    			$('#update-tactical-price').find('#jul_qty').val(jul);
		    			$('#update-tactical-price').find('#aug_qty').val(aug);
		    			$('#update-tactical-price').find('#sep_qty').val(sep);
		    			$('#update-tactical-price').find('#oct_qty').val(oct);
		    			$('#update-tactical-price').find('#nov_qty').val(nov);
		    			$('#update-tactical-price').find('#dec_qty').val(dec);

		    			$('#update-tactical-price').find('.outlet-name').empty();
		    			$('#update-tactical-price').find('.outlet-name').text(outlet);
		    			$('#update-tactical-price').find('.material-name').empty();
		    			$('#update-tactical-price').find('.material-name').text(material);

		    			$("#update-tactical-price").bootstrapValidator('resetForm');
		    			$('#modal-update-tactical').modal('show');
			    	}else{
			    		console.log('Error please contact your administrator');
			    	}
		    	}
			});
		}
	});


	/*Dashboard*/

	$(document).on('click', '.remove-adjustment', function(e){
		e.preventDefault();
		var id = $(this).attr('data-id');
		$('#cancel-adjustment').find('#id').val(id);
		$('#modal-cancel-adjustment').modal('show');
	});

	$('#tbl-dashboard-bc').DataTable({
		"bInfo" : false,
		"order": []
	});

	/*Boiler Cost JS*/

	/*my code starts here*/

	$('#opex-gl-class').select2({
		"placeholder": 'Select Class'
	});

	$(document).on('change', '#opex-gl-class', function(){
		var id = $(this).val();
		var cost_center = $('#cost-center').val();
		var gl_val = $(this).children("option:selected").text();


		$.ajax({
	    	url: base_url + 'business-center/get-gl-base-on-class/'+id+'/'+cost_center,
	    	// data: {id:id, cost_center:cost_center},
	    	method: 'GET',
			beforeSend: function() {
				// setting a timeout
				var loading_table = '<tr><td colspan="16">Loading, Please wait...</td></td>';
				$("#tbl-transac-opex > tbody").empty();
				$("#tbl-transac-opex > tbody").append(loading_table);
			},
	    	success:function(response){
	    		var parse_response = JSON.parse(response);
				
	    		if(parse_response['result'] == 1){
	    			// tbl_transac_opex.destroy();
	    			$("#tbl-transac-opex > tbody").empty();
					var loading_table = '<tr><td colspan="16">Too many data, Please use the upload function.</td></td>';
		    		// $("#tbl-transac-opex > tbody").append(parse_response['gl']);
		    		$("#tbl-transac-opex > tbody").append(loading_table);
		    		// tbl_transac_opex = $('#tbl-transac-opex').DataTable({
					// 	"scrollX": true,
					// 	"scrollY": "300px",
					// 	"fixedHeader": true,
					// 	"bInfo": false,
					// 	"paging": false,
					// });

					tbl_transac_opex.column(2).visible(false);
					$('#store-brand').removeClass('hide-info');
					$('#store-outlet').removeClass('hide-info');
					$('#store-templates').removeClass('hide-info');
					$('#add-opex-form').attr('action', base_url + 'admin/add-store-expense');
					$('.btn-save-opex').addClass('hide-info');
					// $('#add-opex-form').prop('disabled', TRUE);
					

					$('.opex-cost-center').select2({
						"placeholder": 'Select Cost Center'
					});
		    	}else{
		    		console.log('Error please contact your administrator');
		    	}
	    	}
		});

		
	});

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

	var broiler_config = $('#tbl-view-broiler-config').DataTable({
		"bInfo" : false,
		"order": []
	});

	var add_broiler_config = $('#tbl-add-broiler-config').DataTable({
		
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
		
		"order": [],
		"scrollX": true,
		"autoWidth": true,
		"bInfo" : false,
		"paging" : false,
		"scrollY":        "550px",
        "scrollCollapse": true,
		fixedColumns:   {
          leftColumns: 5
        }
	});


	var tbl_transac_prod_edit = $('#tbl-edit-prod-transaction').DataTable({
		
		"order": [],
		"scrollX": true,
		"autoWidth": true,
		"bInfo" : false,
		"paging" : false,
		"scrollY":        "550px",
        "scrollCollapse": true,
		fixedColumns:   {
          leftColumns: 4
        }
	});

	$(document).on('paste', '#tbl-new-prod-transaction input', function(e){
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
		var pick_year = $('#pick_year').val();
		$.ajax({
	    	url: base_url + 'admin/get-broiler-subgroup',
	    	data: {id:id, bc_id : bc_id, pick_year : pick_year},
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
		var pick_year = $('#pick_year').val();
		var brand_id = $('#brand_id').val();
		var process_selected = $("#config_prod option:selected").text();
		var process_selected = process_selected.split('~');
		$('#process_type_name'). val(process_selected[1]);
		$.ajax({
	    	url: base_url + 'business-center/get-config-prod',
	    	data: {id:id, bc_id : bc_id, pick_year : pick_year, process_type_name : process_selected[1], brand_id : brand_id},
	    	method: 'POST',
	    	success:function(response){
	    		//console.log(response);
	    		
	    		var parse_response = JSON.parse(response);
	    		//console.log(parse_response);
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
		    				$('#modal-slider-prod').find('#dynamic-label').html('Ave.Wgt');
		    			} else {
		    				for (var i = 1; i <= 12; i++) {
		    					$('#tbl-new-prod-transaction').find('#dynamic_hdr-'+i).html('Cost/Price');	
		    				}
		    				$('#modal-slider-prod').find('#dynamic-label').html('Cost/Price');
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
					          leftColumns: 5
					        }
						});
		    			$(".save").removeAttr('disabled');
						$("#here").css({"background-color": "white", "color": "black"});
						//iterate through each textboxes and add keyup
						//handler to trigger sum event
						if(process_selected[1].trim() == 'CLASSIFICATION'){
							$(".txt").each(function () {
								$(this).keyup(function () {
									calculateSum('.txt', 6, 29);
								});
								calculateSum('.txt', 6, 29);
							});
						}
		    		}
		    	}else{
		    		console.log('Error please contact your administrator');
		    	}
	    	}
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

	var tbl_prod_trans_external = $('#tbl-prod-trans-external').DataTable({
		"bInfo" : false,
		"order": []
	});

	var cost_sheet_report = $('.tbl-cost-sheet-report').DataTable({
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

	var cost_sheet_report = $('.tbl-sales-bom-summary').DataTable({
		"order": [],
		"scrollX": true,
		"autoWidth": true,
		"bInfo" : false,
		"paging" : false,
		"scrollY": "550px",
        "scrollCollapse": true,
		fixedColumns:   {
          leftColumns: 2
        }
	});

	var new_ext_prod = $('#tbl-new-ext-prod').DataTable({
		"order": [],
		"scrollX": true,
		"autoWidth": true,
		"bInfo" : false,
		"paging" : false,
		"scrollY":        "550px",
        "scrollCollapse": true,
		fixedColumns:   {
          leftColumns: 1
        }
	});
	$("#here").css({"background-color": "white", "color": "black"});

	$(document).on('paste', '#tbl-new-ext-prod input', function(e){
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

	$('#toggle-cost').change(function() {
		var toogle_status = $(this).prop('checked');

		for(var a=2; a <= 25; a++){
			if(a%2 == 0){
				var column = new_ext_prod.column(a);
				column.visible(!column.visible());
			}
		}
    });

    $('#toggle-wgt').change(function() {
		var toogle_status = $(this).prop('checked');
		
		for(var a=1; a <= 25; a++){
			if(a%2 == 1){
				var column = new_ext_prod.column(a);
				column.visible(!column.visible());
			}
		}
    });

	var tbl_broiler_trans_2 = $('#tbl-broiler-trans').DataTable({
		"bInfo" : false,
		"order": []
	});

	var tbl_other_trans = $('#tbl-other-trans').DataTable({
		"bInfo" : false,
		"order": []
	});

	var tbl_industry_trans = $('#tbl-industry-trans').DataTable({
		"bInfo" : false,
		"order": [],
		"paging" : false
	});

	var tbl_industry_dashboard_trans = $('#tbl-industry-dashboard-trans').DataTable({
		"bInfo" : false,
		"order": [],
		"paging" : false,
		"bFilter" : false
	});

	var tbl_new_industry_trans = $('#tbl-new-industry-trans').DataTable({
		"bInfo" : false,
		"order": [],
		"autoWidth": true,
		"bInfo" : false,
		"paging" : false
	});

	var tbl_prod_trans = $('#tbl-prod-trans').DataTable({
		"bInfo" : false,
		"order": []
	});

	var tbl_sales_commission = $('#tbl-sales-commission').DataTable({
		"bInfo" : false,
		"order": []
	});
	
	var tbl_sales_commission_maintenance = $('#tbl-sales-commission-maintenance').DataTable({
		"bInfo" : false,
		"order": []
	});
	
	var tbl_percent_rent = $('#tbl-percent-rent').DataTable({
		"bInfo" : false,
		"order": []
	});
	
	var tbl_percent_rent_maintenance = $('#tbl-percent-rent-maintenance').DataTable({
		"bInfo" : false,
		"order": []
	});

	if($('#lock_status').val()){
		$("#add_industry_trans_button").show();
		//$("#add_broiler_actual_data").show();
		//$("#view_broiler_summary").show();
		//$("#add_broiler_config_button").show();
		//$("#add_broiler_trans_button").show();
		$("#add_prod_trans_button").show();
		$("#copy_prod_trans_button").show();
		//$("#add_ext_material").show();
		//$("#add_ext_transaction").show();
		
		
		$(".remove-prod-trans").show();
		$("#add_sales_bom_trans_button").show();
		$("#copy_sales_bom_trans_button").show();
		$("#create_by_product_on_sales_bom").show();
		$(".cancel-industry-trans").show();
		$(".cancel-broiler-amount-summary").show();
		
	} else {
		$("#add_industry_trans_button").hide();
		//$("#add_broiler_actual_data").hide();
		//$("#view_broiler_summary").hide();
		//$("#add_broiler_config_button").hide();
		//$("#add_broiler_trans_button").hide();
		$("#add_prod_trans_button").hide();
		$("#copy_prod_trans_button").hide();
		//$("#add_ext_material").hide();
		//$("#add_ext_transaction").hide();
		//$(".remove-ext-prod-trans").hide();
		
		$(".remove-prod-trans").hide();
		$("#add_sales_bom_trans_button").hide();
		$("#copy_sales_bom_trans_button").hide();
		$("#create_by_product_on_sales_bom").hide();
		$(".cancel-industry-trans").hide();
		$(".cancel-broiler-amount-summary").hide();
		
	}
	if($('#lock_status_2').val()){
		$(".remove-ext-prod-trans").show();
		$("#add_ext_material").show();
		$("#add_ext_transaction").show();
		$("#upload_ext_material").show();
		$("#edit_ext_prod_trans_button").show();
	} else {
		$(".remove-ext-prod-trans").hide();
		$("#add_ext_material").hide();
		$("#add_ext_transaction").hide();
		$("#upload_ext_material").hide();
		$("#edit_ext_prod_trans_button").hide();
	}

	$("#add_broiler_actual_data").hide();
	$("#add_broiler_config_button").hide();
	$("#add_broiler_trans_button").hide();
	

	$('#broiler-budget-date-year').datepicker({
	    autoclose: true,
	    format: 'yyyy',
	    viewMode: "years",
		minViewMode: "years",
		startDate: '2018',
	}).on('changeDate',function(e){
		
		var year = $('#broiler-budget-year').val();
		var bc_id = $('#bc_id').val();
		
		var url = base_url + 'business-center/broiler-trans/'+bc_id+'/'+year;
    	window.location.replace( url );
	});


	$('#broiler-actual-data-year').datepicker({
	    autoclose: true,
	    format: 'yyyy',
	    viewMode: "years",
		minViewMode: "years",
		startDate: '2018',
	}).on('changeDate',function(e){
		
		var year = $('#broiler-actual-date-pick-year').val();
		var bc_id = $('#bc_id').val();
		

		$.ajax({
	    	url: base_url + 'admin/get-broiler-amount-summary',
	    	data: {year:year, bc_id:bc_id},
	    	method: 'POST',
	    	success:function(response){
	    		var parse_response = JSON.parse(response);
	    		if(parse_response['result'] == 1){
	    			tbl_other_trans.destroy();
	    			
	    			$("#tbl-other-trans > tbody").empty();
		    		$("#tbl-other-trans > tbody").append(parse_response['broiler_amount_item']);
		    		tbl_other_trans = $('#tbl-other-trans').DataTable({
						"bInfo" : false,
						"order": []
					});
					if(parse_response['pending_lock_status']){
						$("#add_broiler_actual_data").show();
						$("#add_broiler_actual_data").attr("href", base_url + 'admin/new-broiler-amount-summary/'+bc_id+'/'+year);
						
					} else {
						
						$("#add_broiler_actual_data").hide();
					}
		    	} else{
		    		console.log('Error please contact your administrator');
		    	}
	    	}
		});
	});

	$('#broiler-config-year').datepicker({
	    autoclose: true,
	    format: 'yyyy',
	    viewMode: "years",
		minViewMode: "years",
		startDate: '2018',
	}).on('changeDate',function(e){
		
		var year = $('#broiler-config-date-pick-year').val();
		var bc_id = $('#bc_id').val();

		$.ajax({
	    	url: base_url + 'admin/get-broiler-config',
	    	data: {broiler_config_date:year, bc_id:bc_id},
	    	method: 'POST',
	    	success:function(response){
	    		var parse_response = JSON.parse(response);
	    		if(parse_response['result'] == 1){

	    			broiler_config.destroy();
	    			$("#tbl-view-broiler-config > tbody").empty();
		    		$("#tbl-view-broiler-config > tbody").append(parse_response['broiler_config_item']);
		    		broiler_config = $('#tbl-view-broiler-config').DataTable({
						"bInfo" : false,
						"order": []
					});
					if(parse_response['pending_lock_status']){
						$("#add_broiler_config_button").show();
						$("#add_broiler_config_button").attr("href", base_url + 'admin/new-broiler-config/'+bc_id+'/'+year);
						
					} else {
						
						$("#add_broiler_config_button").hide();
					}
		    	} else{
		    		console.log('Error please contact your administrator');
		    	}
	    	}
		});
	});

	$('#broiler-trans-year').datepicker({
	    autoclose: true,
	    format: 'yyyy',
	    viewMode: "years",
		minViewMode: "years",
		startDate: '2018',
	}).on('changeDate',function(e){
		
		var year = $('#broiler-trans-date-pick-year').val();
		var bc_id = $('#bc_id').val();

		$.ajax({
	    	url: base_url + 'admin/get-broiler-trans',
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
					if(parse_response['pending_lock_status']){
						$("#add_broiler_trans_button").show();
						$("#add_broiler_trans_button").attr("href", base_url + 'business-center/new-broiler-trans/'+bc_id+'/'+year);

						//$("#view_broiler_summary").show();
						$("#view_broiler_summary").attr("href", base_url + 'business-center/view-broiler-summary/'+bc_id+'/0/'+year);
					} else {
						$("#add_broiler_trans_button").hide();
						//$("#view_broiler_summary").hide();
						$("#view_broiler_summary").attr("href", base_url + 'business-center/view-broiler-summary/'+bc_id+'/0/'+year);
					}
		    	} else{
		    		console.log('Error please contact your administrator');
		    	}
	    	}
		});
	});

	$('#broiler-amount-summary-year').datepicker({
	    autoclose: true,
	    format: 'yyyy',
	    viewMode: "years",
		minViewMode: "years",
		startDate: '2018'
	}).on('changeDate',function(e){
		
		var year = $('#broiler-amount-date-pick-year').val();
		var bc_id = $('#bc_id').val();

		$.ajax({
	    	url: base_url + 'admin/get-broiler-amount',
	    	data: {trans_date:year, bc_id:bc_id},
	    	method: 'POST',
	    	success:function(response){
	    		
	    		var parse_response = JSON.parse(response);
	    		if(parse_response['result'] == 1){

	    			add_broiler_config.destroy();
	    			$("#tbl-add-broiler-config > tbody").empty();
		    		$("#tbl-add-broiler-config > tbody").append(parse_response['broiler_line_item']);
		    		add_broiler_config = $('#tbl-add-broiler-config').DataTable({
						"autoWidth": false,
						"scrollX": true,
						"bInfo" : false,
						"paging" : false,
					});
		    	}else{
		    		console.log('Error please contact your administrator');
		    	}
	    	}
		});
	});

	$('#prod-copy-year').datepicker({
	    autoclose: true,
	    format: 'yyyy',
	    viewMode: "years",
		minViewMode: "years",
		startDate: '2018',
	});

	$('#prod-dest-year').datepicker({
	    autoclose: true,
	    format: 'yyyy',
	    viewMode: "years",
		minViewMode: "years",
		startDate: '2018',
	});

	$('#prod-trans-year').datepicker({
	    autoclose: true,
	    format: 'yyyy',
	    viewMode: "years",
		minViewMode: "years",
		startDate: '2018',
	}).on('changeDate',function(e){
		
		var year = $('#prod-trans-date-pick-year').val();
		var bc_id = $('#bc_id').val();
		/*alert(bc_id);
		return;*/

		$.ajax({
	    	url: base_url + 'business-center/get-prod-trans',
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
					if(parse_response['pending_lock_status']){
						$("#add_prod_trans_button").show();
						$("#copy_prod_trans_button").show();
						$(".remove-prod-trans").show();
						$("#add_prod_trans_button").attr("href", base_url + 'admin/new-prod-trans/'+bc_id+'/'+year);
						$("#view_cost_sheet_button").attr("href", base_url + 'admin/view-cost-sheet-report/'+bc_id+'/'+year);
					} else {
						$("#add_prod_trans_button").hide();
						$("#copy_prod_trans_button").hide();
						$(".remove-prod-trans").hide();
						
					}
		    	} else {
		    		console.log('Error please contact your administrator');
		    	}


		    	if(parse_response['result_2'] == 1){
		    		tbl_prod_trans_external.destroy();
	    			$("#tbl-prod-trans-external > tbody").empty();
		    		$("#tbl-prod-trans-external > tbody").append(parse_response['ext_prod_trans']);
		    		tbl_prod_trans_external = $('#tbl-prod-trans-external').DataTable({
						"bInfo" : false,
						"order": []
					});
					if(parse_response['pending_lock_status_2']){
						$("#add_ext_material").show();
						$("#upload_ext_material").show();
						$("#add_ext_transaction").show();
						$(".remove-ext-prod-trans").show();
						$("#edit_ext_prod_trans_button").show();
						$("#add_ext_transaction").attr("href", base_url + 'admin/new-ext-prod-trans/'+bc_id+'/'+year);
						$("#view_ext_transaction").attr("href", base_url + 'admin/view-ext-prod-trans/'+bc_id+'/'+year);
					} else {
						$("#add_ext_material").hide();
						$("#upload_ext_material").hide();
						$("#add_ext_transaction").hide();
						$(".remove-ext-prod-trans").hide();
						$("#edit_ext_prod_trans_button").hide();
						$("#view_ext_transaction").attr("href", base_url + 'admin/view-ext-prod-trans/'+bc_id+'/'+year);
					}
		    	} else {
		    		console.log('Error please contact your administrator');
		    	}
	    	}
		});
	});




	$('#sales-bom-trans-year').datepicker({
	    autoclose: true,
	    format: 'yyyy',
	    viewMode: "years",
		minViewMode: "years",
		startDate: '2018',
	}).on('changeDate',function(e){
		
		var year = $('#prod-trans-date-pick-year').val();
		var bc_id = $('#bc_id').val();
		/*alert(bc_id);
		return;*/

		$.ajax({
	    	url: base_url + 'business-center/get-sales-bom-trans',
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
					if(parse_response['pending_lock_status']){
						$("#add_sales_bom_trans_button").show();
						$("#copy_sales_bom_trans_button").show();
						$("#create_by_product_on_sales_bom").show();
						$(".remove-prod-trans").show();
						$("#add_sales_bom_trans_button").attr("href", base_url + 'business-center/new-sales-bom-trans/'+bc_id+'/'+year);
						$("#view_sales_bom_summary_button").attr("href", base_url + 'business-center/view-sales-bom-summary/'+bc_id+'/'+year);
						$("#create_by_product_on_sales_bom").attr("href", base_url + 'business-center/create-by-product-on-sales-bom/'+bc_id+'/'+year);
					} else {
						$("#add_sales_bom_trans_button").hide();
						$("#copy_sales_bom_trans_button").hide();
						$("#create_by_product_on_sales_bom").hide();
						$(".remove-prod-trans").hide();
						
					}
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

	$(document).on('click', '.remove-sales-commission',function(e){
		e.preventDefault();
		var sales_commission_id = $(this).attr('data-id');
		var material_desc = $(this).attr('data-mat-desc');
		var outlet_name = $(this).attr('data-outlet-name');
		var bc_id = $(this).attr('data-bc-id');

		
		$('#remove-sales-commission').find('#bc_id').val(bc_id);
		$('#remove-sales-commission').find('#sales_commission_id').val(sales_commission_id);
		$('#remove-sales-commission').find('#modal-msg').html('Are you sure to remove this item '+outlet_name+' <strong>'+material_desc+'</strong>?');
		$('#remove-sales-commission').find('#trans_status').val('remove');
		$('#modal-confirm').modal({show:true});
	});
	
	$(document).on('click', '.remove-percent-rent',function(e){
		e.preventDefault();
		var percent_rent_id = $(this).attr('data-id');
		var material_desc = $(this).attr('data-mat-desc');
		var outlet_name = $(this).attr('data-outlet-name');
		var bc_id = $(this).attr('data-bc-id');

		
		$('#remove-percent-rent').find('#bc_id').val(bc_id);
		$('#remove-percent-rent').find('#percent_rent_id').val(percent_rent_id);
		$('#remove-percent-rent').find('#modal-msg').html('Are you sure to remove this item '+outlet_name+' <strong>'+material_desc+'</strong>?');
		$('#remove-percent-rent').find('#trans_status').val('remove');
		$('#modal-confirm').modal({show:true});
	});

	$(document).on('click', '.remove-prod-trans',function(e){
		e.preventDefault();
		if(!$('#lock_status').val()){
			return;
		}
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

	$(document).on('click', '.cancel-commparative-data',function(e){
		e.preventDefault();
		
		
		var bc_id = $(this).attr('data-bc-id');
		var table = $(this).attr('data-table');
		var year = $(this).attr('data-trans-year');

		$('#cancel-comparative-data').find('#bc_id').val(bc_id);
		$('#cancel-comparative-data').find('#table').val(table);
		$('#cancel-comparative-data').find('#trans_year').val(year);
		$('#cancel-comparative-data').find('#modal-msg').html('Are you sure to cancel this upload?');
		
		$('#modal-confirm').modal({show:true});
	});

	$(document).on('click', '.cancel-config-prod',function(e){
		e.preventDefault();
		var config_prod_id = $(this).attr('data-id');
		
		$('#cancel-config-prod').find('#config_prod_id').val(config_prod_id);
		$('#cancel-config-prod').find('#modal-msg').html('Are you sure to cancel this transaction?');
		$('#cancel-config-prod').find('#trans_status').val('cancel');
		$('#modal-confirm').modal({show:true});
	});

	$(document).on('click', '.cancel-broiler-amount-summary',function(e){
		e.preventDefault();
		var broiler_line_item_id = $(this).attr('data-id');
		var bc_id = $(this).attr('data-bc_id');
		var year = $(this).attr('data-year');
		
		$('#cancel-broiler-amount-summary').find('#broiler_line_item_id').val(broiler_line_item_id);
		$('#cancel-broiler-amount-summary').find('#bc_id').val(bc_id);
		$('#cancel-broiler-amount-summary').find('#year').val(year);
		$('#cancel-broiler-amount-summary').find('#modal-msg').html('Are you sure to cancel this transaction?');
		$('#cancel-broiler-amount-summary').find('#trans_status').val('cancel');
		$('#modal-confirm').modal({show:true});
	});

	$(document).on('click', '.cancel-industry-trans',function(e){
		e.preventDefault();
		var industry_trans_id = $(this).attr('data-id');
		var bc_id = $(this).attr('data-bc_id');
		var trans_year = $(this).attr('data-trans-year');
		
		
		$('#cancel-industry-trans').find('#industry_trans_id').val(industry_trans_id);
		$('#cancel-industry-trans').find('#bc_id').val(bc_id);
		$('#cancel-industry-trans').find('#trans_year').val(trans_year);
		$('#cancel-industry-trans').find('#trans_status').val('cancel');
		$('#cancel-industry-trans').find('#modal-msg').html('Are you sure to cancel this transaction?');
		$('#modal-confirm2').modal({show:true});
	});

	$(document).on('click', '.remove-ext-prod-trans',function(e){
		e.preventDefault();
		var ext_prod_trans_id = $(this).attr('data-id');
		var bc_id = $(this).attr('data-bc_id');
		
		
		$('#remove-ext-prod-trans').find('#ext_prod_trans_id').val(ext_prod_trans_id);
		$('#remove-ext-prod-trans').find('#bc_id').val(bc_id);
		$('#remove-ext-prod-trans').find('#modal-msg').html('Are you sure to cancel this transaction?');
		$('#remove-ext-prod-trans').find('#trans_status').val('cancel');
		$('#modal-confirm_2').modal({show:true});
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
	    	url: base_url + 'business-center/get-brand/',
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

    $('#sales_bom_id').selectize({
        maxItems: 150,
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
	    	url: base_url + 'admin/get-material/',
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

	$('#ext_prod_trans_id').selectize({
        maxItems: 1,
        valueField: 'id',
        labelField: 'title',
        searchField: 'title',
        create: false
    });

    $(document).on('change', '#component_type_id_svc', function(e){
		e.preventDefault();
		var id = $(this).val();

		$.ajax({
	    	url: base_url + 'admin/get-services/',
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

	$(document).on('click', '#ext_batch_cancellation', function(e){
		e.preventDefault();
		//var id = $(this).val();
		var bc_id = $('#bc_id').val();
		var year = $('#prod-trans-date-pick-year').val();
		var formID = '#form-ext-cancellation';
        var modalID = '#modal-ext-batch-cancellation';
		//alert(bc_id);
		//alert(year);
		
		$.ajax({
	    	url: base_url + 'business-center/get-ext-prod-materials/',
	    	data:{bc_id: bc_id, year: year},
	    	method: 'POST',
	    	success:function(response){
	    		/*console.log(parse_response);
	    		return;*/
	    		var parse_response = JSON.parse(response);
	    		if(parse_response['result'] == 1){

					//alert('hello');
	    			//$('#article_id').empty();
	    			//$('#article_id').append(parse_response['info']);
					
					$(formID).find('#bc_id').val(bc_id);
					$(formID).find('#ext_prod_trans_id').selectize()[0].selectize.destroy();
					$(formID).find('#ext_prod_trans_id').empty();
					$(formID).find('#ext_prod_trans_id').append(parse_response['info']);
					$(formID).find('#ext_prod_trans_id').selectize({
				        maxItems: 30,
				        valueField: 'id',
				        labelField: 'title',
				        searchField: 'title',
				        create: false
				    });
	    			
	    			$(formID).find('#submit-cancel-ext-batch').attr('disabled', false);

	    			
		    	} else if(parse_response['result'] == 0){
		    		$(formID).find('#ext_prod_trans_id').selectize()[0].selectize.destroy();
	    			$(formID).find('#ext_prod_trans_id').empty();
					$(formID).find('#submit-cancel-ext-batch').attr('disabled', true);
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


	/*PRODUCTION SLIDER*/
	$(document).on('click', '.slider-prod', function(e){
    	e.preventDefault();
    	var row_tr = $(this).closest('tr').index();
    	$('#modal-slider-prod').find('#id').val(row_tr);
    	$('#modal-slider-prod').modal('show');
    });

    $(document).on('change', '#slider-qty', function(){
    	var qty = $(this).val();
    	$('#slider-qty-val').empty();
    	$('#slider-qty-val').val(qty);
    });

    $(document).on('keyup', '#slider-qty-val', function(){
    	var val = $(this).val();
    	$('#slider-qty').val(val);
    });

    $(document).on('change', '#slider-qty-start', function(){
    	var start = parseInt($(this).val());
    	var end = parseInt($('#slider-qty-end').val());

    	if(start <= end){
	    	$('#slider-qty-start-val').empty();
	    	$('#slider-qty-start-val').text(number_format(start));
	    }else{
	    	$('#slider-qty-end-val').empty();
	    	$('#slider-qty-end-val').text(number_format(start));
	    	$('#slider-qty-end').val(start);

	    	$('#slider-qty-start-val').empty();
	    	$('#slider-qty-start-val').text(number_format(start));
	    }
    });

    $(document).on('change', '#slider-qty-end', function(){
    	var end = parseInt($(this).val());
    	var start = parseInt($('#slider-qty-start').val());

    	if(end >= start){
	    	$('#slider-qty-end-val').empty();
	    	$('#slider-qty-end-val').text(number_format(end));
	    }else{
	    	$('#slider-qty-start-val').empty();
	    	$('#slider-qty-start-val').text(number_format(end));
	    	$('#slider-qty-start').val(end);

	    	$('#slider-qty-end-val').empty();
	    	$('#slider-qty-end-val').text(number_format(end));
	    }
    });
	
	$(document).on('change', '#slider-cost', function(){
    	var qty = $(this).val();
    	$('#slider-cost-val').empty();
    	$('#slider-cost-val').val(qty);
    });

    $(document).on('keyup', '#slider-cost-val', function(){
    	var val = $(this).val();
    	$('#slider-cost').val(val);
    });

    $(document).on('change', '#slider-cost-start', function(){
    	var start = parseInt($(this).val());
    	var end = parseInt($('#slider-cost-end').val());

    	if(start <= end){
	    	$('#slider-cost-start-val').empty();
	    	$('#slider-cost-start-val').text(number_format(start));
	    }else{
	    	$('#slider-cost-end-val').empty();
	    	$('#slider-cost-end-val').text(number_format(start));
	    	$('#slider-cost-end').val(start);

	    	$('#slider-cost-start-val').empty();
	    	$('#slider-cost-start-val').text(number_format(start));
	    }
    });

    $(document).on('change', '#slider-cost-end', function(){
    	var end = $(this).val();
    	var start = parseInt($('#slider-cost-start').val());

    	if(end >= start){
	    	$('#slider-cost-end-val').empty();
	    	$('#slider-cost-end-val').text(number_format(end));
	    }else{
	    	$('#slider-cost-start-val').empty();
	    	$('#slider-cost-start-val').text(number_format(end));
	    	$('#slider-cost-start').val(end);

	    	$('#slider-cost-end-val').empty();
	    	$('#slider-cost-end-val').text(number_format(end));
	    }
    });

    $(document).on('click', '.slider-prod-btn', function(e){
		e.preventDefault();
		
		var qty = $('#slider-qty-val').val();
		var qty_start = parseInt($('#slider-qty-start').val());
		var qty_end = parseInt($('#slider-qty-end').val());
		
		var count = parseInt($('#modal-slider-prod').find('#id').val()) + 2;
		var counter_qty = 1;

		//var doc = $(document).find('#tbl-budget tr').eq(count)
		console.log(qty + ' - ' + qty_start + ' - ' + qty_end + ' - ' + count + ' - ' + counter_qty);
		$(document).find('#tbl-new-broiler-transaction tr').eq(count).find('input[name^="rate"]').each(function(){
			
			if(qty_start <= counter_qty  && qty_end >= counter_qty){
				$(this).val('');
				$(this).val(qty);

			}

			var val = parseInt($(this).val());
			if(isNaN(val)){
				//total += 0;
			}else{
				//total += val;
			}
			counter_qty++;
		});
		$(document).find('#tbl-new-prod-transaction tr').eq(count).find('input[name^="rate"]').each(function(){

			if(qty_start <= counter_qty  && qty_end >= counter_qty){
				$(this).val('');
				$(this).val(qty);
			}

			var val = parseInt($(this).val());
			if(isNaN(val)){
				//total += 0;
			}else{
				//total += val;
			}
			counter_qty++;
		});

		var cost = $('#slider-cost-val').val();
		var cost_start = parseInt($('#slider-cost-start').val());
		var cost_end = parseInt($('#slider-cost-end').val());
		
		var counter_cost = 1;

		$(document).find('#tbl-new-broiler-transaction tr').eq(count).find('input[name^="cost"]').each(function(){
			if(cost_start <= counter_cost  && cost_end >= counter_cost){
				$(this).val('');
				$(this).val(cost);
			}

			var val = parseInt($(this).val());
			if(isNaN(val)){
				//total += 0;
			}else{
				//total += val;
			}
			counter_cost++;
		});

		$(document).find('#tbl-new-prod-transaction tr').eq(count).find('input[name^="cost"]').each(function(){
			if(cost_start <= counter_cost  && cost_end >= counter_cost){
				$(this).val('');
				$(this).val(cost);
			}

			var val = parseInt($(this).val());
			if(isNaN(val)){
				//total += 0;
			}else{
				//total += val;
			}
			counter_cost++;
		});
		$('#modal-slider-prod').modal('hide');
	});


    //BROILER SLIDER
	$(document).on('click', '.slider-broiler', function(e){
    	e.preventDefault();
    	var row_tr = $(this).closest('tr').index();
    	

    	$('#modal-slider-broiler').find('#id').val(row_tr);
    	$('#modal-slider-broiler').modal('show');
    });

    $(document).on('change', '#slider-qty', function(){
    	var qty = $(this).val();
    	$('#slider-qty-val').empty();
    	$('#slider-qty-val').val(qty);
    });

    $(document).on('keyup', '#slider-qty-val', function(){
    	var val = $(this).val();
    	$('#slider-qty').val(val);
    });

    $(document).on('change', '#slider-qty-start', function(){
    	var start = parseInt($(this).val());
    	var end = parseInt($('#slider-qty-end').val());

    	if(start <= end){
	    	$('#slider-qty-start-val').empty();
	    	$('#slider-qty-start-val').text(number_format(start));
	    }else{
	    	$('#slider-qty-end-val').empty();
	    	$('#slider-qty-end-val').text(number_format(start));
	    	$('#slider-qty-end').val(start);

	    	$('#slider-qty-start-val').empty();
	    	$('#slider-qty-start-val').text(number_format(start));
	    }
    });

    $(document).on('change', '#slider-qty-end', function(){
    	var end = parseInt($(this).val());
    	var start = parseInt($('#slider-qty-start').val());

    	if(end >= start){
	    	$('#slider-qty-end-val').empty();
	    	$('#slider-qty-end-val').text(number_format(end));
	    }else{
	    	$('#slider-qty-start-val').empty();
	    	$('#slider-qty-start-val').text(number_format(end));
	    	$('#slider-qty-start').val(end);

	    	$('#slider-qty-end-val').empty();
	    	$('#slider-qty-end-val').text(number_format(end));
	    }
    });

    $(document).on('click', '.slider-broiler-btn', function(e){
		e.preventDefault();
		
		var qty = $('#slider-qty-val').val();
		var qty_start = parseInt($('#slider-qty-start').val());
		var qty_end = parseInt($('#slider-qty-end').val());
		
		var count = parseInt($('#modal-slider-broiler').find('#id').val()) + 1;
		var counter_qty = 1;

		//var doc = $(document).find('#tbl-budget tr').eq(count)
		console.log(qty + ' - ' + qty_start + ' - ' + qty_end + ' - ' + count + ' - ' + counter_qty);
		
		$(document).find('#tbl-add-broiler-config tr').eq(count).find('input[name^="config_qty"]').each(function(){

			if(qty_start <= counter_qty  && qty_end >= counter_qty){
				$(this).val('');
				$(this).val(qty);
			}

			var val = parseInt($(this).val());
			if(isNaN(val)){
				//total += 0;
			}else{
				//total += val;
			}
			counter_qty++;
		});

		$(document).find('#tbl-add-broiler-config tr').eq(count).find('input[name^="trans_qty"]').each(function(){

			if(qty_start <= counter_qty  && qty_end >= counter_qty){
				$(this).val('');
				$(this).val(qty);
			}

			var val = parseInt($(this).val());
			if(isNaN(val)){
				//total += 0;
			}else{
				//total += val;
			}
			counter_qty++;
		});

		$(document).find('#tbl-view-broiler-transaction tr').eq(count).find('input[name^="broiler_budget_qty"]').each(function(){

			if(qty_start <= counter_qty  && qty_end >= counter_qty){
				$(this).val('');
				$(this).val(qty);
			}

			var val = parseInt($(this).val());
			if(isNaN(val)){
				//total += 0;
			}else{
				//total += val;
			}
			counter_qty++;
		});

		$(document).find('#tbl-new-broiler-transaction tr').eq(count).find('input[name^="broiler_budget_qty"]').each(function(){

			if(qty_start <= counter_qty  && qty_end >= counter_qty){
				$(this).val('');
				$(this).val(qty);
			}

			var val = parseInt($(this).val());
			if(isNaN(val)){
				//total += 0;
			}else{
				//total += val;
			}
			counter_qty++;
		});

		$(document).find('#tbl-edit-sales-commission tr').eq(count).find('input[name^="sales_commission_det_value"]').each(function(){

			if(qty_start <= counter_qty  && qty_end >= counter_qty){
				$(this).val('');
				$(this).val(qty);
			}

			var val = parseInt($(this).val());
			if(isNaN(val)){
				//total += 0;
			}else{
				//total += val;
			}
			counter_qty++;
		});
		
		$(document).find('#tbl-edit-percent-rent tr').eq(count).find('input[name^="percent_rent_det_value"]').each(function(){

			if(qty_start <= counter_qty  && qty_end >= counter_qty){
				$(this).val('');
				$(this).val(qty);
			}

			var val = parseInt($(this).val());
			if(isNaN(val)){
				//total += 0;
			}else{
				//total += val;
			}
			counter_qty++;
		});
		$('#modal-slider-broiler').modal('hide');
	});


	$(".edit_rate").each(function () {
		$(this).keyup(function () {
			calculateSum('.edit_rate', 5, 28);
		});
	});


	$('.comp-trans-year').datepicker({
	    autoclose: true,
	    format: 'yyyy',
	    viewMode: "years",
		minViewMode: "years",
		startDate: '2018',
	});

	$('.tbl-comparative').DataTable({
		"bInfo" : false,
		"order": []
	});

	$(".comparative-form").bootstrapValidator({
		framework: 'bootstrap',
		excluded: [':disabled'],
		
	});
	
	/*my code ends here*/
});