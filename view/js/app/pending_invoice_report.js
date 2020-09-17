//var datatable;
$(document).ready(function() {
	load_stock_out_datatable();
});

function reload_data(){
	load_stock_out_datatable();
}

function load_stock_out_datatable(vender_id){
	var customer_id=$('#customer_id').val();
	Loading();
	$.ajax({
		type: "POST",
		url: root_domain+'app/pending_invoice_report/',
		data: { mode : "pending_invoice_report",customer_id:customer_id },
		success: function(response){
                    // console.log(response);
                     $('#profitloss_report_id').html(response);
                     //search();
                     Unloading();
                }
			
	});
}
