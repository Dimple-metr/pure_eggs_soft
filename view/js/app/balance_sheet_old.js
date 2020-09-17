//var datatable;
$(document).ready(function() {
	load_balance_sheet();
});

function reload_data(){
	load_balance_sheet();
}

function load_balance_sheet(){
	var date=$('#rep_date').val();
	Loading();
	$.ajax({
		type: "POST",
		url: root_domain+'app/balance_sheet/',
		data: { mode : "load_balance_sheet",show_details :false, date : date },
		success: function(response){
				 //console.log(response);
				$('#balance_sheet_id').html(response);
                                get_pl_value();
                                hide_details();
				Unloading();
			}
			
	});
}
Mousetrap.bind({
    'shift+v': show_details
});
Mousetrap.bind({
    'shift+c': hide_details
}); 
function show_details() {
    var date=$('#rep_date').val();
	Loading();
	$.ajax({
		type: "POST",
		url: root_domain+'app/balance_sheet/',
		data: { mode : "load_balance_sheet",show_details :true, date : date },
		success: function(response){
				 $('#balance_sheet_id').html(response);
                                 $(".descripc").show();
                                 get_pl_value();
				 Unloading();
			}
			
	});
}
function hide_details() {
    $(".descripc").hide();
}

function get_pl_value(){
    var date=$('#rep_date').val();
    $.ajax({
        type: "POST",
        dataType: "json",
        url: root_domain + 'app/profit_loss_report/pl_value',
        data: { mode : "load_profit_loss",show_details :false, date : date },
        success: function(response){
                    var html = '';
                    var grand_total = 0;
                    if(response.net_profit > 0){
                        total_liability = parseFloat($('#total_liability').val());
                        html += '<td><strong>Profit & Loss A/C<strong></td>';
                        html += '<td style="text-align: right;">'+ response.net_profit +'</td>';
                        $(".net_profit").html(html);
                        $(".net_profit").show();
                        grand_total = parseFloat(response.net_profit) + total_liability;
                    }
                    if(response.net_loss > 0){
                        total_assets = parseFloat($('#total_assets').val());
                        html += '<td><strong>Profit & Loss A/C<strong></td>';
                        html += '<td style="text-align: right;">'+ response.net_loss +'</td>';
                        $(".net_loss").html(html);
                        $(".net_loss").show();
                        grand_total = parseFloat(response.net_loss) + total_assets;
                    }
                    $(".grand_total").html(parseFloat(Math.abs(grand_total)).toFixed(2));
                }
    });
}