//var datatable;
$(document).ready(function() {
	load_stock_out_datatable();
	get_amount();	
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
		url: root_domain+'app/stock_out/',
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
				window.location=root_domain+'stock_out_list';
							
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
				window.location=root_domain+'stock_out_list';
				
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
				url: root_domain+'app/stock_out/',
				data: { mode : "delete",  eid : id },
				success: function(response)
				{
					console.log(response)
					if(response.trim() == "1") {
						toastr.success("PO DELETE SUCCESSFULLY", "SUCCESS");
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
function get_discount(type)
{
	var qty=parseFloat($('#product_qty').val());
	var rate=parseFloat($('#product_rate').val());
	var disc=0;
	if(qty!="" && rate !="")
	{	
		if(type=="amt")
		{
			disc=100*parseFloat($('#product_discount').val())/(qty*rate);
          var  disc1=disc.toFixed(2);			
			$('#discount_per').val(disc1);
		}
		else if(type=="per")
		{
			disc=((qty*rate)*parseFloat($('#discount_per').val()))/100;	
		var	disc1=disc.toFixed(2);
			$('#product_discount').val(disc1);
			
		}
	}
	else
	{
		$('#product_discount').val('');
		$('#discount_per').val('');
	}
	get_amount();
}

function get_amount()
{	

		var id=parseInt($('#fieldcnt').val())+1;
		if($("#product_qty").val()!="" && $("#product_rate").val()!="")
		{
			var q=$("#product_qty").val();
			var rate=$("#product_rate").val();
			var a=q*rate;

			if($("#product_discount").val()!="" )//discount calculation
			{	
				var discount=parseFloat($("#product_discount").val());
				a=a-discount; 
			}
			$("#product_amount").val(parseFloat(a));
			$("#taxable_value").val(parseFloat(a));
			/*if($("#formulaid").val()!="")//tax calculation
			{
				var total=a;
				var formulaid=$("#formulaid").val();
				$.ajax({
					type: "POST",
					url: root_domain+'app/stock_out/',
					data: { mode : "getproduct_amount",  product_amount : total ,formulaid:formulaid},
					success: function(response)
					{
						var obj=jQuery.parseJSON(response);
						$('#product_amount').val(obj.total);
					}
				});
			}*/
			if($("#formulaid").val()!="")//tax calculation
			{
				var formulaid=$("#formulaid").val();
				 var total=a;
				//Loading()
				$.ajax({
				type: "POST",
				url: root_domain+'app/invoice/',
				data: { mode : "load_tax_per",formulaid:formulaid},
				success: function(resp){
					
							//console.log(resp);
						 if(resp!=""){
						var total_amount=(parseFloat(total)*parseFloat(resp))/parseFloat(100);
						 total_amount=total_amount+total;
							$('#product_amount').val(total_amount);
						} 
						//Unloading();
					}		
					
				}); 
			}
		}
		else
		{
			$("#product_amount").val(0);
		}
	get_gtotal();
}
function get_gtotal(eid)
{	
	var id=parseInt($('#fieldcnt').val());
	var t=0;
	var p=parseFloat($('#paking').val());
	var d=parseFloat($('#discount').val());
	var r=parseFloat($('#round_off').val());
	 if (isNaN(r)) {
       r=0;
    }
	var input_amount=(document.getElementsByName('amount[]'));
	var cnt=input_amount.length;
	var total=0;var c_total=0;
	if(total=="")
	{
		total=0;
	}
	for(var i=0;i<cnt;i++)
	{	
		var t=input_amount[i].value;
		if(t>0)
			total=parseFloat(total)+parseFloat(t);
	}
	$("#total").val(parseFloat(total).toFixed(2));
	if(p>0)
	{
		total=total+p;
	}
	if(d>0)
	{
		total=parseFloat(total)-parseFloat(d);
	}
	if(r!=0)
	{
		total=parseFloat(total)+r;
	}
	$("#g_total").val(total);
	/*$.ajax({
			type: "POST",
			url: root_domain+'app/stock_out/',
			data: { mode : "formulavalue",eid :eid,total : total,paking:p, c_total:c_total},
			success: function(response)
			{
				//console.log(response);
				$('#showformulatextbox').html(response);
				$("#g_total").val($('#rate').val());			
			}
	});	*/
	
}
function load_productdetail(pro_id) {
	var stock_outorder_id = $("#stock_outorder_id").val();
	if(stock_outorder_id){
		$('#addproduct').hide();
		$.ajax({
					type: "POST",
					url: root_domain+'app/stock_out/',
					data: { mode : "loadstock_out_productdata",product_id :pro_id, stock_outorder_id:stock_outorder_id },
					success: function(response)
					{
						console.log(response);
						var obj =jQuery.parseJSON(response)
						$('#product_des').val(obj.description);				
						$('#product_hsn_code').val(obj.product_hsn_code);
						$('#product_qty').val(obj.product_qty);
						//$('#sqr_ft').val(obj.sqr_ft);
						$('#product_rate').val(obj.product_rate);	
						$('#unitid').select2("val",obj.unit_id);
						$('#product_discount').val(obj.product_discount);
						$('#discount_per').val(obj.discount_per);
						//$('#product_amount').val(obj.product_amount);	
						$('#formulaid').val(obj.formulaid);
						get_amount();	
					}
				});
		
	}
	else{
		
		if(pro_id!=0)
		{
			$('#addproduct').hide();
		}
		else
		{
			$('#addproduct').show();
		}
		var vender_id = $('#vender_id').val();
		if(vender_id==''){
			toastr.warning("Please Select Vender First","ERROR");
			$('#vender_id').select2('focus');
			return false;
		}
		$.ajax({
				type: "POST",
				url: root_domain+'app/stock_out/',
				data: { mode : "load_productdata",eid :pro_id, vender_id : vender_id },
				success: function(response)
				{
					console.log(response);
					$("#rate_history").show();
					var obj =jQuery.parseJSON(response)
					$('#product_des').val(obj.product_des);				
					$('#product_hsn_code').val(obj.product_code);				
					//$('#product_rate').val(obj.product_stock_out_mst_rate);				
					$('#unitid').select2("val",obj.product_mst_unitid);
					if(obj.com_stateid==obj.ven_stateid){
						$('#formulaid').val(obj.intra_tax);
					}else{
						$('#formulaid').val(obj.inter_tax);
					}
					load_last_rate(pro_id,obj.product_stock_out_mst_rate);	
				}
			});
	}
	
}
function add_field()
{
	if($("#product_id").val()==="")
	{		
		toastr.warning("Select Product", "ERROR")
		$("#product_id").select2('focus')
		return false;
	}
	if($("#product_qty").val()==="")
	{		
		toastr.warning("Enter Qty", "ERROR")
		$("#product_qty").focus();
		return false;
	}
	/* if($("#unitid").val()==="")
	{		
		toastr.warning("Select Unit", "ERROR")
		$("#unitid").select2('focus');
		return false;
	} */
	var conf_form = new FormData();
	/*
		Image Upload
		var ins = document.getElementById('quo_pdf').files.length;
		
		for (var x = 0; x < ins; x++) {
			conf_form.append("quo_pdf"+x, document.getElementById('quo_pdf').files[x]);
			var ins11 = document.getElementById('quo_pdf').files[x].size;
		}
		conf_form.append('ins',ins);
	*/
	
	//check box
	//conf_form.append('show_part',$("#show_part").prop("checked") ? 1 : 0);
	
	
	conf_form.append('mode', "fieldadd");
	conf_form.append('edit_id',$("#edit_id").val());
	conf_form.append('product_id',$("#product_id").val());
	conf_form.append('product_des',$("#product_des").val());
	conf_form.append('product_qty',$("#product_qty").val());
	conf_form.append('unit_id',$("#unitid").val());
	conf_form.append('stock_out_id',$("#eid").val());

	$.ajax({
			type: "POST",
			url: root_domain+'app/stock_out/',
			data: conf_form,
			contentType: false,
			processData: false,
			success: function(response)
			{
				//console.log(response);
				$("#product_id").select2("val","")
				$("#product_id").select2('focus')
				$("#product_des").val("")
				$("#product_qty").val("")
				$("#unit_id").select2('val',"")
				$("#edit_id").val('')
				$('#addproduct').show();
				$('#addrow').val('Add');
				Unloading();
				show_data()
				
			}
		});
}
function field_remove(id)
{
	$("#fieldtr"+id).html('');
	var t=get_amount();
}
function reload_data()
{
	//datatable.fnReloadAjax();
	load_stock_out_datatable();
}	
function load_stock_out_datatable()
{
	var data=$('input[name=report]:Checked').val();
	var date=$('#rep_date').val();
	
	datatable = $("#stock_out-table").dataTable({
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
			"sAjaxSource": root_domain+'app/stock_out/',
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

function show_data()
{
	var stock_out_id=$("#eid").val();
	Loading()
	$.ajax({
	type: "POST",
	url: root_domain+'app/stock_out/',
	data: { mode : "load_tempoutward",stock_out_id:stock_out_id},
	success: function(data){
				//console.log(data);
				 $('#sale_productdata').html(data);				
				 Unloading();
		}		
		
	});
	
}

function edit_data(id)
{
	Loading();
			$.ajax({
				type: "POST",
				url: root_domain+'app/stock_out/',
				data: { mode : "preedit",  id : id},
				success: function(response)
				{
					console.log(response)
					var data = jQuery.parseJSON(response);
					$("#product_id").select2("val",data.product_id)
					$("#product_des").val(data.description)
					$("#product_qty").val(data.product_qty)
					$("#unitid").select2("val",data.unit_id)
					$("#edit_id").val(id)
					$('#addrow').val('Update');
					Unloading();
				}
			});
}
function delete_data(id)
{
	var r= confirm(" Are you want to delete ?");

		if(r) {
			Loading();
			$.ajax({
				type: "POST",
				url: root_domain+'app/stock_out/',
				data: { mode : "delete_data",  eid : id },
				success: function(response)
				{
					//console.log(response)
					var data=jQuery.parseJSON(response)
					var response=data.res;
					if(response.trim() == "1") {
						toastr.success("DATA DELETE SUCCESSFULLY", "SUCCESS");
							show_data()
							Unloading();
					}
					else if(response.trim() == "0") {
						toastr.warning("SOMETHING WRONG", "WARNING");
					}							
				}
			});	
		}
	
}
function load_last_rate(pro_id,mst_rate){
	
	Loading()
	$.ajax({
			type: "POST",
			url: root_domain+'app/stock_out/',
			data: { mode : "last_rate", product_id:pro_id},
			success: function(resp){
					//console.log(resp);
					if(resp){
						$('#product_rate').val(resp);
					}
					else{
						$('#product_rate').val(mst_rate);
					}
									
					Unloading();
			}		
	});
}
function load_stock_out_order(vender_id){
	if(vender_id){
	$('#stock_out_order_div').attr("style","display:block");
	Loading();
	$.ajax({
		type: "POST",
		url: root_domain+'app/stock_out/',
		data: { mode : "load_stock_out_order", vender_id : vender_id },
		success: function(response){
				 console.log(response);
				 $('#stock_outorder_id').html(response);
				 $('#stock_outorder_id').select2('val','');
				 Unloading();
			}
			
	});
	}else{
		$('#stock_out_order_div').attr("style","display:none");
	}
}
function load_purhcase_order_data(stock_outorder_id){
	if(stock_outorder_id){
		Loading();
		$.ajax({
			type: "POST",
			url: root_domain+'app/stock_out/',
			data: { mode : "load_purhcase_order_data", stock_outorder_id : stock_outorder_id },
			success: function(response){
					//console.log(response);
					var resp = 	JSON.parse(response);
					$('#order_no').val(resp.stock_outorder_no);
					$('#order_date').val(resp.stock_outorder_date);
					$('#product_id').html(resp.pro_html);
					$('#product_id').select2('val','');
					Unloading();
				}
				
		});
	}else{
		Loading();
		$.ajax({
			type: "POST",
			url: root_domain+'app/stock_out/',
			data: { mode : "load_purhcase_pro"},
			success: function(response){
					console.log(response);
					var resp = 	JSON.parse(response);
					$('#order_no').val('');
					$('#order_date').val('');
					$('#product_id').html(resp.pro_html);
					$('#product_id').select2('val','');
					Unloading();
				}
				
		});
		
	}
	
}
function load_rate_hist(){
	
	var vender_id = $("#vender_id").val();
	var product_id = $("#product_id").val();
	if(vender_id==''){
		toastr.warning("Please Select Vendor", "WARNING");
		return false;
	}
	else if(product_id==''){
		toastr.warning("Please Select Product", "WARNING");
		return false;
	}
	else{
		
		Loading();
		$.ajax({
			type: "POST",
			url: root_domain+'app/stock_out/',
			data: { mode : "load_rate_hist", vender_id : vender_id, product_id : product_id },
			success: function(response){
					console.log(response);
					var arr = JSON.parse(response);
					$('#hist_tbl tbody').html(arr.resp);
					$('#cust_hist').html(arr.cust_name);
					$('#pro_hist').html(arr.product_name);
					$('#bs-example-modal-rate_history').modal();
					Unloading();
			}
		});
		
	}	

}

$("#use_dr_add").on('submit',function(e) {
	var form = this;
	e.preventDefault();
	e.stopPropagation();	
	if (!$("#use_dr_add").valid()){
		return false;
	}
	/*else if(parseInt($('#total_dr').val())<=0){
		toastr.warning("Enter Debit Amount", "ERROR");
		return false;
	}*/
	else if(parseInt($('#total_dr').val()) > parseInt($('#stock_out_balance').val())){
		toastr.warning("Debit Amount Should be less than Purchase Balance", "ERROR");
		$('#total_dr').focus();
		return false;
	}
	
	form.submitted = true;
	Loading(true);
	$(this).attr("disabled","disabled");
	
	var form_data=new FormData(this);	
	$.ajax({
		cache:false,
		url: root_domain+'app/stock_out/',
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
				toastr.success("DEBITS APPLIED SUCCESSFULLY", "SUCCESS");
				window.location=root_domain+'stock_out_list';
			}
			else if(arr.msg == '0') {
				toastr.warning("SOMETHING WRONG", "ERROR")
				Unloading();
			}
			$('#use_dr_add').trigger('reset');	
		},
		error: function(jqXHR, textStatus, errorThrown) {
			console.log(textStatus, errorThrown);
		}
	});
	
});

function load_total_dr(){
	var input_amount=(document.getElementsByName('used_debit_amt[]'));
	var cnt=input_amount.length;
	var total=0;
	if(total==""){
		total=0;
	}
	for(var i=0;i<cnt;i++)
	{
		var t=input_amount[i].value;
		if(t>0)
			total=parseFloat(total)+parseFloat(t);
	}
	$("#total_dr").val(parseFloat(total));
}