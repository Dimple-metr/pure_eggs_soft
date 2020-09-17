//var datatable;
$(document).ready(function() {
	load_stock_in_datatable();
		
// validate vendor add form on keyup and submit
 $("#po_add").validate({
	rules: {
		cust_id: {
			required: true			
		},
		po_no: {
			required: true			
		},
		po_date:{
			required : true	
		}
	},
	messages: {
		cust_id: {
			required: "Select Customer"
		},
		po_no: {
			required: "Enter P.O no"
		},
		po_date:{
			required : "Enter P.O date"
		}
	}
}); 
});
$("#po_add").on('submit',function(e) {
	
	var form = this;
	e.preventDefault();
	e.stopPropagation();	
	if (!$("#po_add").valid()) {
		return false;
	}
	form.submitted = true;	
	Loading(true);	
	$(this).attr("disabled","disabled");		
	var token=  $("#token").val();	
	
	var form_data=new FormData(this);
	//console.log(form_data);
	$.ajax({
		cache:false,
		url: root_domain+'app/stock_in/',
		type: "POST",
		data: form_data,
		contentType: false,
		processData:false,
		success: function(response)
		{
			console.log(response);	
			var arr = jQuery.parseJSON(response);
			if(arr.msg == '1') {
				Unloading();
				toastr.success("PURCHASE ADDED SUCCESSFULLY", "SUCCESS");
				window.location=root_domain+'stock_in_list';
							
			}
			else if(arr.msg == '0') {
				toastr.warning("SOMETHING WRONG", "ERROR")
				Unloading();
			}
			else if(arr.msg == '-1')
			{
				toastr.info("ALREADY EXISTS", "INFO")
				Unloading();				
			}
			else if(arr.msg== 'update')
			{	
				toastr.success("BILL UPDATED SUCCESSFULLY", "SUCCESS");		
				Unloading();
				window.location=root_domain+'stock_in_list';
				
			}
			$('#po_add').trigger('reset');	
		},
		error: function(jqXHR, textStatus, errorThrown) {
			console.log(textStatus, errorThrown);
		}
	});
	
});

function delete_invoice(id) 
{
	var r= confirm(" Are you want to delete ?");

		if(r) {
			Loading(true);
			$.ajax({
				type: "POST",
				url: root_domain+'app/stock_in/',
				data: { mode : "delete",  eid : id },
				success: function(response)
				{
					console.log(response)
					if(response.trim() == "1") {
						toastr.success("DELETE SUCCESSFULLY", "SUCCESS");
						datatable.fnReloadAjax();
						Unloading();
					}
					else if(response.trim() == "0") {
						toastr.warning("SOMETHING WRONG", "WARNING");
					}							
				}
			});	
		}
	
}
function reload_data()
{
	//datatable.fnReloadAjax();
	load_stock_in_datatable();
}	
function load_stock_in_datatable()
{
	var data=$('input[name=report]:Checked').val();
	var date=$('#rep_date').val();
	
	datatable = $("#stock_in-table").dataTable({
			"bAutoWidth" : false,
			"bFilter" : true,
			"bSort" : true,
			"bProcessing": true,
			"bDestroy": true,
			"bServerSide" : true,
			"oLanguage": {
					"sLengthMenu": "_MENU_",
					"sProcessing": "<img src='"+root_domain+"img/loading.gif'/> Loading ...",
					"sEmptyTable": "NO DATA ADDED YET !",
			},
			"aLengthMenu": [[10, 20, 30, 50], [10, 20, 30, 50]],
			"iDisplayLength": 10,
			"sAjaxSource": root_domain+'app/stock_in/',
			"fnServerParams": function ( aoData ) {
				aoData.push( { "name": "mode", "value": "fetch" },{ "name": "report", "value": data },{ "name": "date", "value": date });
			},
			"fnDrawCallback": function( oSettings ) {
				$('.ttip, [data-toggle="tooltip"]').tooltip();
			}
		}).fnSetFilteringDelay();

		//Search input style
		$('.dataTables_filter input').addClass('form-control').attr('placeholder','Search');
		$('.dataTables_length select').addClass('form-control');
}
function get_user_out(){
	var employee_id=$("#employee_id").val();
	var stock_date=$("#stock_in_date").val();
	var stock_in_id=$("#eid").val();
	if(employee_id !=""){
		if(stock_date!=""){
			$.ajax({
				type: "POST",
				url: root_domain+'app/stock_in/',
				data: { mode : "load_tempoutward",employee_id:employee_id,stock_date:stock_date,stock_in_id:stock_in_id},
				success: function(data){
							//console.log(data);
							 $('#sale_productdata').html(data);				
							// Unloading();
							cal_gtotal();
							cal_first_sales_qty();
						}		
					
				});
			
		}else{
			toastr.error("Select Date", "ERROR")
		}
	}else{
		toastr.error("Select Employee", "ERROR")
	}
	
}
function cal_first_sales_qty(){
	var p=document.getElementsByName('i[]');
	var cnt=p.length;
	var k=0;
	for(var i=0;i<cnt;i++)
		{
			 k=p[i].value;
			 //alert(k);
			cal_sales_qty(k)
		}
}
function cal_sales_qty(i){
	var sales_qty1=$("#sales_qty"+i).val();
	
	var replace_qty=$("#replace_qty"+i).val();
	var product_qty1=$("#product_qty"+i).val();
	var transfer_out=$("#transfer_out"+i).val();
	var transfer_in=$("#transfer_in"+i).val();
	
if(replace_qty==""){replace_qty=0;}
if(sales_qty1==""){sales_qty1=0;}
if(product_qty1==""){product_qty1=0;}
if(transfer_out==""){transfer_out=0;}
if(transfer_in==""){transfer_in=0;}

	var sales_qty=parseFloat(sales_qty1)+parseFloat(replace_qty);
	var product_qty=(parseFloat(product_qty1)+parseFloat(transfer_in))-parseFloat(transfer_out);
	if(isNaN(sales_qty) ||  sales_qty==""){
		sales_qty=0;
		//$("#return_qty"+i).val("0");
	}
	var return_qty=parseFloat(product_qty)-parseFloat(sales_qty);
	//alert(return_qty);
	if(isNaN(return_qty)){
		return_qty=0;
	}

	$("#return_qty"+i).val(return_qty);
	cal_collection_amount(i)
}
function cal_collection_amount(i){
	
	var sales_qty=$("#sales_qty"+i).val();
	var rate=$("#rate"+i).val();
	var col_amount=parseFloat(sales_qty)*parseFloat(rate);
	if(isNaN(col_amount)){
		col_amount=0;
	}
	$("#amount"+i).val(col_amount);
	cal_gtotal();
	
}
function cal_gtotal(){
	var product_qty=document.getElementsByName('product_qty[]');
	var return_qty=document.getElementsByName('return_qty[]');
	var replace_qty=document.getElementsByName('replace_qty[]');
	var sales_qty=document.getElementsByName('sales_qty[]');
	var amount=document.getElementsByName('amount[]');
	var cnt=product_qty.length;
	var grandtotal_product_qty=0;
	var grandtotal_return_qty=0;
	var grandtotal_sales_qty=0;
	var grandtotal_amount=0;
	var grandtotal_replace_qty=0;
	for(var i=0;i<cnt;i++)
		{	
			grandtotal_product_qty+=parseFloat(product_qty[i].value);
			grandtotal_return_qty+=parseFloat(return_qty[i].value);
			grandtotal_sales_qty+=parseFloat(sales_qty[i].value);
			grandtotal_amount+=parseFloat(amount[i].value);
			grandtotal_replace_qty+=parseFloat(replace_qty[i].value);
		}
		if(isNaN(grandtotal_product_qty)){grandtotal_product_qty=0;}
		if(isNaN(grandtotal_return_qty)){grandtotal_return_qty=0;}
		if(isNaN(grandtotal_sales_qty)){grandtotal_sales_qty=0;}
		if(isNaN(grandtotal_amount)){grandtotal_amount=0;}
		if(isNaN(grandtotal_replace_qty)){grandtotal_replace_qty=0;}
	$('#grandtotal_product_qty').val(parseFloat(grandtotal_product_qty).toFixed(0));
	$('#grandtotal_return_qty').val(parseFloat(grandtotal_return_qty).toFixed(0));
	$('#grandtotal_sales_qty').val(parseFloat(grandtotal_sales_qty).toFixed(0));
	$('#grandtotal_amount').val(parseFloat(grandtotal_amount).toFixed(0));
	$('#grandtotal_replace_qty').val(parseFloat(grandtotal_replace_qty).toFixed(0));
}