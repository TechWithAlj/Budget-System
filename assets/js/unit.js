
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

	$('#tbl-business').DataTable({
		"bInfo" : false,
		"order": []
	});
	
	$('#tbl-users').DataTable({
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


	var base_url = $('#base_url').val();




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

	//capex
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

		var url = base_url + 'unit/capex-info/' + year;
    	window.location = url;
	});


	$(document).on('change', '#capex-ag', function(){
		var id = $(this).val();
		var cost_center = $('#cost-center').val();
		$.ajax({
	    	url: base_url + 'unit/get-subgroup/',
	    	data: {id:id, cost_center:cost_center},
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
	    	url: base_url + 'unit/get-asset-subgroup/',
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
	    	url: base_url + 'unit/get-capex-item/',
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
	
	$('#opex-trans-year').datepicker({
	    autoclose: true,
	    format: 'yyyy',
	    viewMode: "years",
		minViewMode: "years",
	});

	$(document).on('change', '#opex-trans-year', function(e){
		var year = $('#opex-year').val();

		var url = base_url + 'unit/opex-info/' + year;
    	window.location = url;
	});

	$(document).on('change', '#opex-gl', function(){
		var id = $(this).val();
		var cost_center = $('#cost-center').val();
		var gl_val = $(this).children("option:selected").text();

		$.ajax({
	    	url: base_url + 'unit/get-gl/',
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
						$('#add-opex-form').attr('action', base_url + 'unit/add-store-expense');
					}else{
						$('#store-brand').addClass('hide-info');
						$('#store-outlet').addClass('hide-info');
						$('#store-templates').addClass('hide-info');
						$('#add-opex-form').attr('action', base_url + 'unit/add-opex');
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
		var url = base_url + 'unit/download-store-expense/' + brand;
		$('#templates-store').attr('href', url);
	});

	$('#templates-store').click(function(e){
		var brand = $('#opex-brand').val();
		if(brand == ''){
			e.preventDefault();
			$('#modal-dl-error').modal('show');

		}
	});

	$(document).on('click', '#opex-brand', function(){
		var brand = $(this).val();
		var year = $('#opex-year').val();
		$.ajax({
	    	url: base_url + 'unit/get-store/',
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
	    	url: base_url + 'unit/get-stores/',
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
	    	url: base_url + 'unit/get-gl-sub/',
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

	$('#opex-gl, #opex-brand, #opex-outlet').select2({
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
	    	url: base_url + 'unit/get-opex-item/',
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
	    	url: base_url + 'unit/get-sw-item/',
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
		$('#cancel-store-expense').find('#id').val(id);
		$('#cancel-store-expense').find('#trans_id').val(trans);
		$('#modal-cancel-store-expense').modal('show');
	});

	$(document).on('click', '.update-store-expense-btn', function(e){
		e.preventDefault();
		var id = $(this).attr('data-id');
		var cost_center = $('#cost-center').val();
		$.ajax({
	    	url: base_url + 'unit/get-opex-item/',
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


	/*Employee Module*/

	$('#emp-trans-year').datepicker({
	    autoclose: true,
	    format: 'yyyy',
	    viewMode: "years",
		minViewMode: "years",
	});

	$(document).on('change', '#emp-trans-year', function(e){
		var year = $('#emp-year').val();
		
		var url = base_url + 'unit/employees/' + year;
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
		    	url: base_url + 'unit/get-new-emp/',
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
		    	url: base_url + 'unit/get-emp-cost-center/',
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
	    	url: base_url + 'unit/modal-employee/',
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


	/*Manpower*/

	$('#manpower-trans-year').datepicker({
	    autoclose: true,
	    format: 'yyyy',
	    viewMode: "years",
		minViewMode: "years",
	});

	$(document).on('change', '#manpower-trans-year', function(e){
		var year = $('#manpower-year').val();
		
		var url = base_url + 'unit/manpower/' + year;
    	window.location = url;
	});


	$(document).on('click', '.edit-manpower', function(e){
		e.preventDefault();
		var id = $(this).attr('data-id');
		$.ajax({
	    	url: base_url + 'admin/modal-manpower/',
	    	data: {id:id},
	    	method: 'POST',
	    	success:function(response){
	    		var parse_response = JSON.parse(response);
	    		
	    		if(parse_response['result'] == 1){
	    			
	    			var cost_center = parse_response['info']['cost_center'];
	    			var manpower_position = parse_response['info']['manpower_position'];
	    			var manpower_old = parse_response['info']['manpower_old'];
	    			var manpower_new = parse_response['info']['manpower_new'];
	    			var manpower_bc_old = parse_response['info']['manpower_bc_old'];
	    			var manpower_bc_new = parse_response['info']['manpower_bc_new'];
	    			var rank = parse_response['info']['rank'];

	    			$("#update-manpower").find("#id").val(id);

	    			$("#update-manpower").find("#edit-manpower-cost-center").empty();
		    		$("#update-manpower").find("#edit-manpower-cost-center").append(cost_center);

		    		$("#update-manpower").find("#edit-manpower-rank").empty();
		    		$("#update-manpower").find("#edit-manpower-rank").append(rank);

		    		$("#update-manpower").find("#edit-manpower-old").val(manpower_old);
		    		$("#update-manpower").find("#edit-manpower-new").val(manpower_new);

		    		$("#update-manpower").find("#edit-manpower-bc-old").val(manpower_bc_old);
		    		$("#update-manpower").find("#edit-manpower-bc-new").val(manpower_bc_new);

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

	/*my code starts here*/

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

	/*my code ends here*/
});
