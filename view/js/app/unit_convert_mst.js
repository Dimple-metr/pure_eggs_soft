var datatable;
$(document).ready(function() {
		load_datatable();
	
$("#unit_convert_add").validate({
	rules: {
		unit_id: {
			required: true
		},
		unit_qty: {
			required: true
		},
		new_unit_convert_id: {
			required: true
		}
	},
	messages: {
		unit_id: {
			required: "Select Unit Name"			
		},
		unit_qty: {
			required: "Enter Unit qtyantity"			
		},
		new_unit_convert_id: {
			required: "Select Convert Unit Name"			
		}
	}
}); 
		
});
$("#unit_convert_add").on('submit',function(e) {
	var form = this;
	e.preventDefault();
	e.stopPropagation();	
	if (!$("#unit_convert_add").valid()) {
		return false;
	}
	form.submitted = true;	
	Loading(true);	
	$(this).attr("disabled","disabled");		
	var form_data=new FormData(this);	
	$.ajax({
		cache:false,
		url: root_domain+'app/unit_convert_mst/',
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
				$("#unit_id").select2("val","");
				$("#new_unit_convert_id").select2("val","");
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
			else if(arr.msg == 'update')
			{	
				toastr.success("UPDATED SUCCESSFULLY", "SUCCESS");	
					$("#unit_id").select2("val","");
					$("#new_unit_convert_id").select2("val","");
				Unloading();
			}
				$("#unit_id").select2("val","");
				$("#new_unit_convert_id").select2("val","");
$('#unit_convert_add').trigger('reset');					
			load_datatable();
		},
		error: function(jqXHR, textStatus, errorThrown) {
			console.log(textStatus, errorThrown);
		}
	});
	
});
function delete_unit_convert(id) 
{
	var r= confirm(" Are you want to delete ?");

		if(r) {
			Loading(true);
			$.ajax({
				type: "POST",
				url: root_domain+'app/unit_convert_mst/',
				data: { mode : "delete", eid : id },
				success: function(response)
				{
					
					if(response.trim() == "1") {
						toastr.success("DELETE SUCCESSFULLY", "SUCCESS");
						$("#unit_id").select2("val","");
						$("#new_unit_convert_id").select2("val","");	
						load_datatable();
						Unloading();
					}
					else if(response.trim() == "0") {
						$("#tax_id").select2("val",'');			
		
					toastr.warning("SOMETHING WRONG", "WARNING");
					}							
				}
			});	
		}
	
}
function edit_unit_convert(id)
{
		Loading(true);
		editReq = $.ajax({
			type: "POST",
			url: root_domain+'app/unit_convert_mst/',
			data: { mode : "preedit", id : id },
			success: function(response)
			{
				//console.log(response);
				var obj = jQuery.parseJSON(response);
				$("#edit_id").val(id);				
				$("#unit_convert_qty").val(obj.unit_convert_qty);
				$("#unit_id").select2("val",obj.unit_id);
				$("#new_unit_convert_id").select2("val",obj.new_unit_convert_id);
				$("#mode").val("Edit");
				load_new_unit(obj.unit_id,obj.new_unit_convert_id);
				Unloading();
			}
		});	
	}
function load_datatable(){
	datatable = $("#unit_convert-table").dataTable({
			"bAutoWidth" : false,
			"bFilter" : true,
			"bSort" : true,
			"bProcessing": true,
			"bServerSide" : true,
			"bDestroy": true,
			"oLanguage": {
					"sLengthMenu": "_MENU_",
					"sProcessing": "<img src='"+root_domain+"img/loading.gif'/> Loading ...",
					"sEmptyTable": "NO DATA ADDED YET !",
			},
			"aLengthMenu": [[10, 20, 30, 50], [10, 20, 30, 50]],
			"iDisplayLength": 10,
			"sAjaxSource": root_domain+'app/unit_convert_mst/',
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
function load_new_unit(unit_id,new_unit_convert_id){
	//var unit_id=$('#unit_id').val();
	if(unit_id){
	Loading();
	$.ajax({
		type: "POST",
		url: root_domain+'app/unit_convert_mst/',
		data: { mode : "load_new_unit", unit_id : unit_id,new_unit_convert_id:new_unit_convert_id },
		success: function(data){
				//console.log(data);
				 $('#new_unit_convert_id').html(data);
				 $('#new_unit_convert_id').select2('val',new_unit_convert_id);
				 Unloading();
			}
			
	});
	}else {
		toastr.info("Select Unit Name", "INFO")
	}
}