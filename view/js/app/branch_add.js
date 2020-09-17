//var datatable;
$(document).ready(function() {
	datatable = $("#dynamic-table").dataTable({
		"bAutoWidth" : false,
		"bFilter" : true,
		"bSort" : true,
		"bProcessing": true,
		"bServerSide" : true,
		"oLanguage": {
			"sLengthMenu": "_MENU_",
			"sProcessing": "<img src='"+root_domain+"img/loading.gif'/> Loading ...",
			"sEmptyTable": "NO DATA ADDED YET !",
		},
		"aLengthMenu": [[10, 20, 30, 50], [10, 20, 30, 50]],
		"iDisplayLength": 10,
		"sAjaxSource": root_domain+'app/branch_add/',
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
	// validate the comment form when it is submitted        

// validate vendor add form on keyup and submit
$("#branch_add").validate({
	rules: {
		branch_name: {
			required: true			
		},
		branch_email:{
			required:true
		},
		password:{
			minlength:5
		},
		branch_address:{
			required: true
		},
		stateid: {
			required: true
		},
		cityid: {
			required: true
		},
		branch_mobile: {
			required: true,
			number:true
		}
	},
	messages: {
		branch_name: {
			required: "Enter Branch Name"
		},
		branch_email:{
			required:"Enter Email"
		},
		password:{
			minlength:"Enter more than 5 Character"
		},
		branch_address: {
			required: "Enter Address"
		},
		stateid: {
			required: "State must be select"
		},
		cityid: {
			required: "City must be select"
		},
		branch_mobile: {
			required: "Enter Mobile no",
			number:"Enter Only number "
		}
	}
}); 

});

$(".btn_close").click(function() {
    $("label.error").hide();
});

$("#branch_add").on('submit',function(e) {
	var form = this;
	e.preventDefault();
	e.stopPropagation();	
	if (!$("#branch_add").valid()) {
		return false;
	}
	form.submitted = true;	
	Loading(true);	
	$(this).attr("disabled","disabled");		
	
	var form_data=new FormData(this);	
	$.ajax({
		cache:false,
		url: root_domain+'app/branch_add/',
		type: "POST",
		data: form_data,
		contentType: false,
		processData:false,
		success: function(response)
		{	
			//console.log(response);
			var data = JSON.parse(response);
			var responsevalue=data.res;
			if(responsevalue.trim() == '1') {
				Unloading();
				toastr.success("ADDED SUCCESSFULLY", "SUCCESS");	
				window.location=root_domain+'branch_list';
			}
			else if(responsevalue.trim() == '2') {
				toastr.success("ADDED SUCCESSFULLY", "SUCCESS");
				$("#bs-example-modal-lg").modal("hide");
				$('#cust_id').append('<option value='+data.cust_id+'>'+data.company_name+'</option>');				
				$("#cust_id").trigger('change')
				$('#cust_id').select2("val",data.cust_id);
				$('#user_add').trigger('reset');
				Unloading();
			}
			else if(responsevalue.trim() == '-1') {
				toastr.info("ALREADY EXISTS", "INFO");
				$("#bs-example-modal-lg").modal("hide");
				$('#user_add').trigger('reset');
				Unloading();				
			}
			else if(responsevalue.trim() == 'update') {	
				toastr.success("UPDATED SUCCESSFULLY", "SUCCESS");		
				Unloading();
				window.location=root_domain+'branch_list';	
			}
			$('#branch_add').trigger('reset');	
		},
		error: function(jqXHR, textStatus, errorThrown) {
			console.log(textStatus, errorThrown);
		}
	});
	
});

function delete_branch(id) 
{
	var r= confirm(" Are you want to delete ?");
	
	if(r) {
		Loading(true);
		$.ajax({
			type: "POST",
			url: root_domain+'app/branch_add/',
			data: { mode:"delete", eid:id },
			success: function(response)
			{
				console.log(response);
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
function load_state(parentid,control,val1)
{	
	$.ajax({
		type: "POST",
		url: root_domain+'app/branch_add/',
		data: { mode : "load_state",  id : parentid},
		success: function(responce){
			//console.log(responce);
			$('#'+control).html(responce);
			$("#"+control).select2("val",val1);
		}
	});
	
}

function load_city(parentid,control,val1)
{	
	$.ajax({
		type: "POST",
		url: root_domain+'app/branch_add/',
		data: { mode : "load_city",  id : parentid},
		success: function(responce){
			//console.log(responce);
			$('#'+control).html(responce);
			$("#"+control).select2("val",val1);
		}
	});
	
}
