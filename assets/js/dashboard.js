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

	$('#tbl-dashboard-bc').DataTable({
		"bInfo" : false,
		"order": []
	});

	$('#tbl-dashboard-unit').DataTable({
		"bInfo" : false,
		"order": []
	});

	$('#tbl-dashboard-region').DataTable({
		"bInfo" : false,
		"order": []
	});

	$(document).on('click', '.view-cm-store', function(e){
		e.preventDefault();

		var trans_id = $(this).attr('data-trans-id');
		var outlet_id = $(this).attr('data-outlet-id');

		$.ajax({
	    	url: base_url + 'dashboard/get-cm-store-details/',
	    	data: {trans_id:trans_id, outlet_id:outlet_id},
	    	method: 'POST',
	    	success:function(response){
	    		var parse_response = JSON.parse(response);
	    		
	    		if(parse_response['result'] == 1){
	    			
	    			var bc = parse_response['bc'];
	    			var outlet = parse_response['outlet'];
	    			var ifs = parse_response['ifs'];
	    			var brand = parse_response['brand'];
	    			var cm_monthly_tbl = parse_response['cm_monthly_tbl'];
	    			var cm_material_tbl = parse_response['cm_material_tbl'];

	    			$('#cm-bc').empty();
	    			$('#cm-bc').append(bc);

	    			$('#cm-outlet').empty();
	    			$('#cm-outlet').append(ifs + ' - ' + outlet);

	    			$('#cm-brand').empty();
	    			$('#cm-brand').append(brand);

	    			$('#tbl-cm-store-monthly').empty();
	    			$('#tbl-cm-store-monthly').append(cm_monthly_tbl);

	    			$('#tbl-cm-material > tbody').empty();
	    			$('#tbl-cm-material > tbody').append(cm_material_tbl);
		    		
		    		$('#modal-cm-store-monthly').modal({show:true});
		    	}else{
		    		console.log('Error please contact your administrator');
		    	}
	    	}
		});
	});
});