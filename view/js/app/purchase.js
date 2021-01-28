//var datatable;
$(document).ready(function() {
	load_purchase_datatable();
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
		url: root_domain+'app/purchase/',
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
				window.location=root_domain+'purchase_list';
							
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
				window.location=root_domain+'purchase_list';
				
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
				url: root_domain+'app/purchase/',
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
					url: root_domain+'app/purchase/',
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
			url: root_domain+'app/purchase/',
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
	var purchaseorder_id = $("#purchaseorder_id").val();
	if(purchaseorder_id){
		$('#addproduct').hide();
		$.ajax({
					type: "POST",
					url: root_domain+'app/purchase/',
					data: { mode : "loadpurchase_productdata",product_id :pro_id, purchaseorder_id:purchaseorder_id },
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
				url: root_domain+'app/purchase/',
				data: { mode : "load_productdata",eid :pro_id, vender_id : vender_id },
				success: function(response)
				{
					console.log(response);
					$("#rate_history").show();
					var obj =jQuery.parseJSON(response)
					$('#product_des').val(obj.product_des);				
					$('#product_hsn_code').val(obj.product_code);				
					//$('#product_rate').val(obj.product_purchase_mst_rate);				
					$('#unitid').select2("val",obj.product_mst_unitid);
					if(obj.com_stateid==obj.ven_stateid){
						$('#formulaid').val(obj.intra_tax);
					}else{
						$('#formulaid').val(obj.inter_tax);
					}
					load_last_rate(pro_id,obj.product_purchase_mst_rate);	
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
	/*if($("#sqr_ft").val()==="")
	{		
		toastr.warning("Enter Sqr/Ft", "ERROR")
		$("#sqr_ft").focus();
		return false;
	}*/
	if($("#unitid").val()==="")
	{		
		toastr.warning("Select Unit", "ERROR")
		$("#unitid").select2('focus');
		return false;
	}
	if($("#product_rate").val()==="")
	{		
		toastr.warning("Enter Rate", "ERROR")
		$("#product_rate").focus();
		return false;
	}
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
	conf_form.append('product_hsn_code',$("#product_hsn_code").val());
	conf_form.append('product_qty',$("#product_qty").val());
	conf_form.append('product_rate',$("#product_rate").val());
	conf_form.append('product_disc',$("#product_disc").val());
	conf_form.append('unit_id',$("#unitid").val());
	conf_form.append('formulaid',$("#formulaid").val());
	conf_form.append('product_discount',$("#product_discount").val());
	conf_form.append('discount_per',$("#discount_per").val());
	conf_form.append('taxable_value',$("#taxable_value").val());
	conf_form.append('product_amount',$("#product_amount").val());
	conf_form.append('po_id',$("#eid").val());

	$.ajax({
			type: "POST",
			url: root_domain+'app/purchase/',
			data: conf_form,
			contentType: false,
			processData: false,
			/*data: { mode : "fieldadd",edit_id:$("#edit_id").val(),product_id:$("#product_id").val(),product_des:$("#product_des").val(),product_hsn_code:$("#product_hsn_code").val(),product_qty:$("#product_qty").val(),product_rate:$("#product_rate").val(),product_disc:$("#product_disc").val(),unit_id:$("#unitid").val(),formulaid:$("#formulaid").val(),product_discount:$("#product_discount").val(),discount_per:$("#discount_per").val(),product_amount:$("#product_amount").val(),po_id:$("#eid").val()},*/
			success: function(response)
			{
				//console.log(response);
				//$("#product_id option[value='"+$("#product_id").val()+"']").remove();
				$("#product_id").select2("val","")
				$("#product_id").select2('focus')
				$("#product_des").val("")
				$("#product_hsn_code").val("")
				$("#formulaid").val("")
				$("#discount_per").val("")
				$("#product_discount").val("")
				$("#product_qty").val("")
			//	$("#sqr_ft").val("")
				$("#unit_id").select2('val',"")
				$("#product_rate").val('');
				$("#product_disc").val('')
				$("#taxable_value").val('')
				$("#product_amount").val('')
				$("#edit_id").val('')
				$('#addproduct').show();
				$('#addrow').val('Add');
				Unloading();
				show_data();
				add_genral_book();
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
	load_purchase_datatable();
}	
function load_purchase_datatable()
{
	var data=$('input[name=report]:Checked').val();
	var date=$('#rep_date').val();
	
	datatable = $("#purchase-table").dataTable({
			"bAutoWidth" : false,
			"bFilter" : true,
			"bSort" : true,
			"bProcessing": true,
			"bDestroy": true,
			"bServerSide" : true,
			"oLanguage": {
					"sLengthMenu": "_MENU_",
					"sProcessing": "<img src='"+root_domain+"img/loading.gif'/> Loading ...",
					"sEmptyTable": "NO DATA ADDED YET !"
			},
			"aLengthMenu": [[-1,10, 20, 30, 50], ["All",10, 20, 30, 50]],
			"iDisplayLength": -1,
			"sAjaxSource": root_domain+'app/purchase/',
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
	var purchase_id=$("#eid").val();
	Loading()
	$.ajax({
	type: "POST",
	url: root_domain+'app/purchase/',
	data: { mode : "load_tempoutward",purchase_id:purchase_id},
	success: function(data){
				//console.log(data);
				 $('#sale_productdata').html(data);				
				  get_amount()
				 Unloading();
		}		
		
	});
	
}

function edit_data(id)
{
	Loading();
			$.ajax({
				type: "POST",
				url: root_domain+'app/purchase/',
				data: { mode : "preedit",  id : id},
				success: function(response)
				{
					console.log(response)
					var data = jQuery.parseJSON(response);
					//$('#product_id').html(data.producthtml);
					//$('#product_id').append('<option value="'+data.product_id+'">'+data.product_name+'</option>');
					$("#product_id").select2("val",data.product_id)
					$("#product_des").val(data.description)
					$("#product_hsn_code").val(data.product_hsn_code)
					$("#product_qty").val(data.product_qty)
					//$("#sqr_ft").val(data.sqr_ft)
					$("#product_rate").val(data.product_rate)
					$("#product_disc").val(data.product_disc)
					$("#unitid").select2("val",data.unit_id)
					$("#formulaid").val(data.formulaid)
					$("#product_amount").val(data.total)
					$("#product_discount").val(data.product_discount)
					$("#discount_per").val(data.discount_per)
					$("#taxable_value").val(data.product_amount)
					$("#edit_id").val(id)
					$('#addrow').val('Update');
					Unloading();
				}
			});
}
function delete_data(id,purchase_id)
{
	var r= confirm(" Are you want to delete ?");

		if(r) {
			Loading();
			$.ajax({
				type: "POST",
				url: root_domain+'app/purchase/',
				data: { mode : "delete_data",  eid : id,purchase_id:purchase_id },
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
			url: root_domain+'app/purchase/',
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
/* function load_purchase_order(vender_id){
	if(vender_id){
	$('#purchase_order_div').attr("style","display:block");
	Loading();
	$.ajax({
		type: "POST",
		url: root_domain+'app/purchase/',
		data: { mode : "load_purchase_order", vender_id : vender_id },
		success: function(response){
				 console.log(response);
				 $('#purchaseorder_id').html(response);
				 $('#purchaseorder_id').select2('val','');
				 Unloading();
			}
			
	});
	}else{
		$('#purchase_order_div').attr("style","display:none");
	}
} */
function load_purhcase_order_data(purchaseorder_id){
	if(purchaseorder_id){
		Loading();
		$.ajax({
			type: "POST",
			url: root_domain+'app/purchase/',
			data: { mode : "load_purhcase_order_data", purchaseorder_id : purchaseorder_id },
			success: function(response){
					//console.log(response);
					var resp = 	JSON.parse(response);
					$('#order_no').val(resp.purchaseorder_no);
					$('#order_date').val(resp.purchaseorder_date);
					$('#product_id').html(resp.pro_html);
					$('#product_id').select2('val','');
					Unloading();
				}
				
		});
	}else{
		Loading();
		$.ajax({
			type: "POST",
			url: root_domain+'app/purchase/',
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
			url: root_domain+'app/purchase/',
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
	else if(parseInt($('#total_dr').val()) > parseInt($('#purchase_balance').val())){
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
		url: root_domain+'app/purchase/',
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
				window.location=root_domain+'purchase_list';
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
function add_genral_book(){
	var invoice_id=$("#eid").val();
	//Loading()
	if(invoice_id){
		$.ajax({
		type: "POST",
		url: root_domain+'app/purchase/',
		data: { mode : "add_genral_book",invoice_id:invoice_id},
		success: function(data){
					//console.log(data);
					// $('#sale_productdata').html(data);				
					 // get_amount()
					// Unloading();
			}		
		});
	}
}