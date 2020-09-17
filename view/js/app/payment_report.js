//var datatable;
$(document).ready(function() {
	load_stock_out_datatable();
});

function reload_data(){
	load_stock_out_datatable();
}

function load_stock_out_datatable(vender_id){
	var date=$('#rep_date').val();
	var paymentmodeid=$('#paymentmodeid').val();
	var employee_id=$('#employee_id').val();
	Loading();
	$.ajax({
		type: "POST",
		url: root_domain+'app/payment_report/',
		data: { mode : "load_profit_loss", date : date,paymentmodeid:paymentmodeid,employee_id:employee_id },
		success: function(response){
				// console.log(response);
				 $('#profitloss_report_id').html(response);
				 //search();
				 Unloading();
			}
			
	});
}
