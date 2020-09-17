//var datatable;
$(document).ready(function() {
	load_stock_out_datatable();
});

function reload_data(){
	load_stock_out_datatable();
}

function load_stock_out_datatable(vender_id){
	//var date=$('#rep_date').val();
	var stock_date=$('#stock_date').val();
	var employee_id=$('#employee_id').val();
	//alert(employee_id);
	Loading();
	$.ajax({
		type: "POST",
		url: root_domain+'app/employee_wise_product_stock/',
		data: { mode : "load_profit_loss", stock_date : stock_date,employee_id:employee_id },
		success: function(response){
				// console.log(response);
				 $('#profitloss_report_id').html(response);
				 //search();
				 Unloading();
			}
			
	});
}
