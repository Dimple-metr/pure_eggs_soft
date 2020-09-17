//var datatable;
$(document).ready(function() {
	load_purchase_datatable();
	
// validate vendor add form on keyup and submit
 $("#journal_add").validate({
	rules: {
		journal_entry_no: {
			required: true			
		},
		journal_entry_date: {
			required: true			
		}
	},
	messages: {
		journal_entry_no: {
			required: "Select Customer"
		},
		journal_entry_date: {
			required: "Enter P.O no"
		}
	}
}); 
});
$("#journal_add").on('submit',function(e) {
	var form = this;
	e.preventDefault();
	e.stopPropagation();	
	if (!$("#journal_add").valid()) {
		return false;
		
	}
	
	if($("#cr_amount").val()!=$("#dr_amount").val()){
		return false;
		toastr.warning("Amount Not Match", "ERROR")
	}
	form.submitted = true;	
	Loading(true);	
	$(this).attr("disabled","disabled");		
	var form_data=new FormData(this);
	//console.log(form_data);
	$.ajax({
		cache:false,
		url: root_domain+'app/journal_entry/',
		type: "POST",
		data: form_data,
		contentType: false,
		processData:false,
		success: function(response)
		{
			//console.log(response);	
			var arr = jQuery.parseJSON(response);
			if(arr.msg == '1') {
				Unloading();
				toastr.success("ADDED SUCCESSFULLY", "SUCCESS");
				window.location=root_domain+'journal_list';
							
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
				toastr.success("UPDATED SUCCESSFULLY", "SUCCESS");		
				Unloading();
				window.location=root_domain+'journal_list';
				
			}
			$('#journal_add').trigger('reset');	
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
				url: root_domain+'app/journal_entry/',
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

function add_field()
{
	if($("#entry_type").val()==="")
	{		
		toastr.warning("Select Type", "ERROR")
		$("#entry_type").select2('focus')
		return false;
	}
	if($("#ledger_id").val()==="")
	{		
		toastr.warning("Enter Ledger", "ERROR")
		$("#ledger_id").focus();
		return false;
	}
	if($("#amount").val()==="")
	{		
		toastr.warning("Select Amount", "ERROR")
		$("#amount").select2('focus');
		return false;
	}
	var conf_form = new FormData();
	conf_form.append('mode', "fieldadd");
	conf_form.append('edit_id',$("#edit_id").val());
	conf_form.append('entry_type',$("#entry_type").val());
	conf_form.append('ledger_id',$("#ledger_id").val());
	conf_form.append('amount',$("#amount").val());
	conf_form.append('journal_id',$("#journal_id").val());

	$.ajax({
			type: "POST",
			url: root_domain+'app/journal_entry/',
			data: conf_form,
			contentType: false,
			processData: false,
			/*data: { mode : "fieldadd",},*/
			success: function(response)
			{
				//console.log(response);
				//$("#product_id option[value='"+$("#product_id").val()+"']").remove();
				$("#entry_type").select2("val","")
				$("#entry_type").select2('focus')
				$("#ledger_id").select2("val","")
				$("#amount").val("")
				$("#edit_id").val("")
				$('#addproduct').show();
				$('#addrow').val('Add');
				Unloading();
				show_data();
				add_genral_book();
			}
		});
}

function reload_data()
{
	//datatable.fnReloadAjax();
	load_purchase_datatable();
}	
function load_purchase_datatable()
{
	//var data=$('input[name=report]:Checked').val();
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
					"sEmptyTable": "NO DATA ADDED YET !",
			},
			"aLengthMenu": [[10, 20, 30, 50], [10, 20, 30, 50]],
			"iDisplayLength": 10,
			"sAjaxSource": root_domain+'app/journal_entry/',
			"fnServerParams": function ( aoData ) {
				aoData.push( { "name": "mode", "value": "fetch" },{ "name": "date", "value": date });
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
	var journal_id=$("#journal_id").val();
	//alert(journal_id);
	Loading()
	$.ajax({
	type: "POST",
	url: root_domain+'app/journal_entry/',
	data: { mode : "load_tempoutward",journal_id:journal_id},
	success: function(data){
				//console.log(data);
				 $('#sale_productdata').html(data);
									 
				 Unloading();
		}		
		
	});
	
}

function edit_data(id)
{
	//alert(id);
	Loading();
			$.ajax({
				type: "POST",
				url: root_domain+'app/journal_entry/',
				data: { mode : "preedit",  id : id},
				success: function(response)
				{
					//console.log(response)
					var data = jQuery.parseJSON(response);
					$("#ledger_id").select2("val",data.ledger_id)
					$("#entry_type").select2("val",data.entry_type)
					$("#amount").val(data.amount)
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
				url: root_domain+'app/journal_entry/',
				data: { mode : "delete_data",  eid : id },
				success: function(response)
				{
					//console.log(response)
					var data=jQuery.parseJSON(response)
					var response=data.res;
					if(response.trim() == "1") {
						toastr.success("DATA DELETE SUCCESSFULLY", "SUCCESS");
							show_data();
							add_genral_book();
							Unloading();
					}
					else if(response.trim() == "0") {
						toastr.warning("SOMETHING WRONG", "WARNING");
					}							
				}
			});	
		}
	
}
function add_genral_book(){
	var journal_id=$("#journal_id").val();
	//Loading()
	if(journal_id){
		$.ajax({
		type: "POST",
		url: root_domain+'app/journal_entry/',
		data: { mode : "add_genral_book",journal_id:journal_id},
		success: function(data){
					//console.log(data);
					// $('#sale_productdata').html(data);				
					 // get_amount()
					// Unloading();
			}		
		});
	}
}

function get_series_no(){
	
	$.ajax({
	type: "POST",
	url: root_domain+'app/journal_entry/',
	data: { mode : "get_series_no"},
	success: function(resp){
				//console.log(resp);
				$('#invoicetype_id').val(resp);	
				load_pono(resp)	
			}		
	});	
}
function load_pono(id)
{
	
	$.ajax({
	type: "POST",
	url: root_domain+'app/journal_entry/',
	data: { mode : "load_invoiceno", typeid : id},
	success: function(data){
				//console.log(data);
				var no = jQuery.parseJSON(data);
				$('#journal_entry_no').val(no.invoiceno);
				
	}
	});
}
