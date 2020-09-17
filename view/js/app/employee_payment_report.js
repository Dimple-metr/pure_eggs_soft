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
		url: root_domain+'app/employee_payment_report/',
		data: { mode : "load_employee_payment_report", date : date,paymentmodeid:paymentmodeid,employee_id:employee_id },
		success: function(response){
				// console.log(response);
				 $('#employee_payment_report_id').html(response);
				 //show_employee_payment_total(date, paymentmodeid, employee_id);
				 Unloading();
			}
			
	});
}
//function show_employee_payment_total(date, paymentmodeid, employee_id){
//    $.ajax({
//        type: "POST",
//        dataType: "json",
//        url: root_domain+'app/employee_payment_report/',
//        data: { mode : "load_employee_payment_report",show_total : true, date : date,paymentmodeid:paymentmodeid,employee_id:employee_id },
//        success: function(response){
//                response.forEach(function(entry) {
//                    //console.log(entry); 
//                    var html = '<tr>';
//                    html += '<td colspan="5" class="text-right stop sbottom sleft sright " style="font-size:16px" width="10%" ><strong>Total</strong></td>';
//                    html += '<td class="text-right stop sbottom sleft sright " style="font-size:16px" width="10%" ><strong>'+entry.group_amount+'</strong></td>';
//                    html += '</tr>';
//                    $(".user_"+entry.user_id).last().after(html);
//                });
//            }
//			
//    });
//}
