//var datatable;
$(document).ready(function() {
	load_cust_datatable();
	
	// validate vendor add form on keyup and submit
	$("#cust_add").validate({
		rules: {
			company_name: {
				required: true			
			},
			cust_name: {
				required: true
			},
			stateid: {
				required: true
			},
			cityid: {
				required: true
			},
			cust_mobile: {
				number:true,
				maxlength:10,
				minlength:10
			},
			cust_email:{
				email:true
			}
		},
		messages: {
			company_name: {
				required: "Enter Company Name"
			},
			cust_name: {
				required: "Enter Customer Name"
			},
			stateid: {
				required: "State must be select"
			},
			cityid: {
				required: "City must be select"
			},
			cust_mobile: {
				number:"Enter Only number ",
				maxlength:"Mobile No. Should consist only 10 digits",
				minlength:"Mobile No. Should consist at least 10 digits"
			},
			cust_email:{
				email:"Enter Valid Email"
			}
		}
	});
	
});

$(".btn_close").click(function() {
	$("label.error").hide();
});
$("#cust_add").on('submit',function(e) {
	var form = this;
	e.preventDefault();
	e.stopPropagation();	
	if (!$("#cust_add").valid()) {
		return false;
	}
	form.submitted = true;	
	Loading(true);	
	$(this).attr("disabled","disabled");		
	
	var form_data=new FormData(this);	
	
	$.ajax({
		cache:false,
		url: root_domain+'app/customer/',
		type: "POST",
		data: form_data,
		contentType: false,
		processData:false,
		success: function(response)
		{	
			console.log(response);
			var data = JSON.parse(response);
			var responsevalue=data.res;
			if(responsevalue.trim() == '1') {
				Unloading();
				toastr.success("CUSTOMER ADDED SUCCESSFULLY", "SUCCESS");	
				window.location=root_domain+'customer_list';
			}
			else if(responsevalue.trim() == '2') {
				toastr.success("CUSTOMER ADDED SUCCESSFULLY", "SUCCESS");
				$("#bs-example-modal-lg").modal("hide");
				$('#cust_id').append('<option value='+data.cust_id+'>'+data.company_name+'</option>');
				$("#cust_id").trigger('change');
				$('#cust_id').select2("val",data.cust_id);
				$("#cust_id").trigger('change');
				$('#cust_add').trigger('reset');
				Unloading();
			}
			else if(responsevalue.trim() == '-1')
			{
				toastr.info("ALREADY EXISTS", "INFO")
				$("#bs-example-modal-lg").modal("hide");
				$('#cust_add').trigger('reset');
				Unloading();				
			}
			else if(responsevalue.trim() == 'update')
			{	
				toastr.success("CUSTOMER UPDATED SUCCESSFULLY", "SUCCESS");		
				Unloading();
				window.location=root_domain+'customer_list';		
			}
			$('#cust_add').trigger('reset');	
		},
		error: function(jqXHR, textStatus, errorThrown) {
			console.log(textStatus, errorThrown);
		}
	});
	
});

function delete_cust(id) 
{
	var r= confirm(" Are you want to delete ?");
	
	if(r) {
		Loading(true);
		$.ajax({
			type: "POST",
			url: root_domain+'app/customer/',
			data: { mode : "delete",  eid : id },
			success: function(response)
			{
				console.log(response);
				if(response.trim() == "1") {
					toastr.success("CUSTOMER DELETE SUCCESSFULLY", "SUCCESS");
					load_cust_datatable();
					Unloading();
				}
				else if(response.trim() == "0") {
					toastr.warning("SOMETHING WRONG", "WARNING");
				}							
			}
		});	
	}
}
function load_state(parentid,control,val1)
{	
	$.ajax({
		type: "POST",
		url: root_domain+'app/customer/',
		data: { mode : "load_state",  id : parentid},
		success: function(responce){
			//console.log(responce);
			$('#'+control).html(responce);
			$("#"+control).select2("val",val1);
		}
	});
	
}
function add_state()
{
	if($("#countryid").val()=='')
	{
		toastr.warning("Please Select the Country", "WARNING");
	}
	else{
		$("#bs-example-modal-state").modal("show");
		$("#countryid").val($("#countryid").val());
	}
}
function load_city(parentid,control,val1)
{	
	$.ajax({
		type: "POST",
		url: root_domain+'app/customer/',
		data: { mode : "load_city",  id : parentid},
		success: function(responce){
			//console.log(responce);
			$('#'+control).html(responce);
			$("#"+control).select2("val",val1);
		}
	});
	
}
function add_city()
{
	if($("#stateid").val()=='')
	{
		toastr.warning("Please Select the State", "WARNING");
	}
	else{
		$("#bs-example-modal-city").modal("show");
		$("#state_id").val($("#stateid").val());
	}
}


	
function load_cust_datatable(){
	datatable = $("#customer-table").dataTable({
		"bAutoWidth" : false,
		"bFilter" : true,
		"bSort" : true,
		"bProcessing": true,
		"bServerSide" : true,
		"bDestroy" : true,
		"oLanguage": {
			"sLengthMenu": "_MENU_",
			"sProcessing": "<img src='"+root_domain+"img/loading.gif'/> Loading ...",
			"sEmptyTable": "NO DATA ADDED YET !",
		},
		"aLengthMenu": [[10, 30, 50, 250], [10, 30, 50, 250]],
		"iDisplayLength": 10,
		"sAjaxSource": root_domain+'app/customer/',
		"fnServerParams": function ( aoData ) {
			aoData.push( { "name": "mode", "value": "fetch" } );
		},
		"fnDrawCallback": function( oSettings ) {
			$('.ttip, [data-toggle="tooltip"]').tooltip();
		}
	}).fnSetFilteringDelay();
	
	//Search input style
	$('.dataTables_filter input').addClass('form-control').attr('placeholder','Search');
	$('.dataTables_length select').addClass('form-control');
}	

function open_person_data(cust_id){
	if(!cust_id){
		toastr.info("Please Select Customer First !!!", "INFO");
		return false;
	}
	
	$('#modal-contact-person-view').modal('show');
	$('#cust_id').val(cust_id);
	show_cust_person_datatable();
}
function add_cust_person_field(){
	if(!$("#cust_contact_person_name").val()){		
		toastr.warning("Enter Person Name", "ERROR");
		$("#cust_contact_person_name").focus();
		return false;
	}
	
	var form_data = {
		mode:"add_cust_person_field", 
		edit_cust_contact_person_id:$("#edit_cust_contact_person_id").val(), 
		cust_contact_person_name:$("#cust_contact_person_name").val(),  
		cust_contact_person_no:$("#cust_contact_person_no").val(), 
		cust_contact_person_email:$("#cust_contact_person_email").val(), 
		cust_id:$("#cust_id").val()
	};
	
	Loading();	
	$.ajax({
		type: "POST",
		url: root_domain+'app/customer/',
		data: form_data,
		success: function(response)
		{
			//console.log(response);
			$("#edit_cust_contact_person_id").val("");
			$("#cust_contact_person_name").val("");
			$("#cust_contact_person_no").val("");
			$("#cust_contact_person_email").val("");
			$('#cust_per_addrow').val('Add');
			Unloading();
			show_cust_person_datatable();
		}
	});
}
function show_cust_person_datatable(){
	var cust_id=$('#cust_id').val();
	datatable = $("#table-cust-person").dataTable({
		"bAutoWidth" : true,
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
		"aLengthMenu": [[5, 10, 20, 30, 50], [5, 10, 20, 30, 50]],
		"iDisplayLength": 5,
		"sAjaxSource": root_domain+'app/customer/',
		"fnServerParams": function ( aoData ) {
			aoData.push( { "name":"mode", "value":"show_cust_person_datatable" },{ "name":"cust_id", "value":cust_id } );
		},
		"fnDrawCallback": function( oSettings ) {
			$('.ttip, [data-toggle="tooltip"]').tooltip();
		}
	}).fnSetFilteringDelay();
	
	//Search input style
	$('.dataTables_filter input').addClass('form-control').attr('placeholder','Search');
	$('.dataTables_length select').addClass('form-control');
}
function edit_cust_person(cust_contact_person_id)
{
	Loading();
	$.ajax({
		type: "POST",
		url: root_domain+'app/customer/',
		data: { mode:"edit_cust_person", cust_contact_person_id:cust_contact_person_id },
		success: function(response)
		{
			//console.log(response)
			var data = jQuery.parseJSON(response);
			$("#cust_contact_person_name").val(data.cust_contact_person_name);
			$("#cust_contact_person_designation").val(data.cust_contact_person_designation);
			$("#cust_contact_person_no").val(data.cust_contact_person_no);
			$("#cust_contact_person_email").val(data.cust_contact_person_email);
			$("#cust_contact_person_skype").val(data.cust_contact_person_skype);
			$("#edit_cust_contact_person_id").val(cust_contact_person_id);
			$('#cust_per_addrow').val('Update');
			Unloading();
		}
	});
}
function delete_cust_person(cust_contact_person_id)
{
	var r= confirm(" Are you want to delete ?");
	
	if(r) {
		Loading();
		$.ajax({
			type: "POST",
			url: root_domain+'app/customer/',
			data: { mode:"delete_cust_person", cust_contact_person_id:cust_contact_person_id },
			success: function(response)
			{
				//console.log(response);
				var data=jQuery.parseJSON(response);
				var response=data.res;
				if(response.trim() == "1") {
					toastr.success("DATA DELETE SUCCESSFULLY", "SUCCESS");
					Unloading();
					show_cust_person_datatable();
				}
				else if(response.trim() == "0") {
					toastr.warning("SOMETHING WRONG", "WARNING");
				}							
			}
		});	
	}
}

function direct_add_cust_person_field(){
	if(!$("#cust_contact_person_name").val()){		
		toastr.warning("Enter Person Name", "ERROR");
		$("#cust_contact_person_name").focus();
		return false;
	}
	
	var form_data = {
		mode:"add_cust_person_field", 
		cust_contact_person_name:$("#cust_contact_person_name").val(), 
		cust_contact_person_no:$("#cust_contact_person_no").val(), 
		cust_contact_person_email:$("#cust_contact_person_email").val(), 
		cust_id:$("#cust_id").val()
	};
	
	$('#cust_per_addrow').prop("disabled", true);
	
	Loading();	
	$.ajax({
		type: "POST",
		url: root_domain+'app/customer/',
		data: form_data,
		success: function(response)
		{
			//console.log(response);
			$('#cust_per_addrow').prop("disabled", false);
			$('#direct-contact-person-add').modal('hide');
$('#cust_contact_person_id').append('<option value='+response+'>'+$("#cust_contact_person_name").val()+'</option>');
$('#cust_contact_person_id').select2("val",response);
$("#cust_contact_person_id").trigger('change');
			$("#cust_contact_person_name").val("");
			$("#cust_contact_person_no").val("");
			$("#cust_contact_person_email").val("");
			$('#cust_per_addrow').val('Add');
			Unloading();
		}
	});
}