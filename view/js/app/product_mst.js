var datatable;
$(document).ready(function() {
		load_datatable();
	
$("#product_add").validate({
	rules: {
		product_name: {
			required: true
		}
	},
	messages: {
		product_name: {
			required: "Enter Product Name"			
		}
	}
}); 
		
});
$("#product_add").on('submit',function(e) {
	var form = this;
	e.preventDefault();
	e.stopPropagation();	
	if (!$("#product_add").valid()) {
		return false;
	}
	form.submitted = true;	
	Loading(true);	
	$(this).attr("disabled","disabled");		
	var form_data=new FormData(this);	
	$.ajax({
		cache:false,
		url: root_domain+'app/product_mst/',
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
				$("#product_mst_unitid").select2("val","");
				$("#catagory_id").select2("val","");
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
					$("#product_mst_unitid").select2("val","");
					$("#catagory_id").select2("val","");
				Unloading();
			}
			$('#product_add').trigger('reset');	
			load_datatable();
		},
		error: function(jqXHR, textStatus, errorThrown) {
			console.log(textStatus, errorThrown);
		}
	});
	
});
function delete_unit(id) 
{
	var r= confirm(" Are you want to delete ?");

		if(r) {
			Loading(true);
			$.ajax({
				type: "POST",
				url: root_domain+'app/product_mst/',
				data: { mode : "delete", eid : id },
				success: function(response)
				{
					
					if(response.trim() == "1") {
						toastr.success("DELETE SUCCESSFULLY", "SUCCESS");
						$("#tax_id").select2("val",'');			
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
function edit_group(id)
{
		Loading(true);
		editReq = $.ajax({
			type: "POST",
			url: root_domain+'app/product_mst/',
			data: { mode : "preedit", id : id },
			success: function(response)
			{
				//console.log(response);
				var obj = jQuery.parseJSON(response);
				$("#edit_id").val(id);
				$("#mode").val("Edit");	
					
				if(obj.product_type==0)
				{
					$("#product_type_both").prop('checked',true);
					$("#ember1142").prop('checked',true);  
				}	
				else if(obj.product_type==1)
				{
					$("#product_type_purchase").prop('checked',true);
					$("#ember1142").prop('checked',true);  
				}
				else if(obj.product_type==2)
				{
					$("#product_type_sale").prop('checked',true);
					$("#ember1142").prop('checked',true);  
				}
                  else if(obj.product_type==3){
					 $('.typepro').attr("style","display:none");
					$("#ember1143").prop('checked',true);  
				  }
				
				$("#product_name").val(obj.product_name);
				$("#productdes").val(obj.product_des);
				$("#product_mst_rate").val(obj.product_mst_rate);
				$("#product_purchase_mst_rate").val(obj.product_purchase_mst_rate);
				$("#item_code").val(obj.item_code);
				$("#product_code").val(obj.product_code);
				$("#intra_tax").val(obj.intra_tax);
				$("#inter_tax").val(obj.inter_tax);
				$("#mrp").val(obj.mrp);
				$("#opening_stock").val(obj.product_stock);
				$("#minimum_stock").val(obj.minimum_stock);
				$("#product_mst_unitid").select2("val",obj.product_mst_unitid);
				$("#ledger_id").select2("val",obj.ledger_id);
				$("#catagory_id").select2("val",obj.catagory_id);
				if(obj.product_type==3){
					showtype("service");
				}else{
					showtype("goods");
				}
				
				Unloading();
			}
		});	
	}
function load_datatable(){
	datatable = $("#unit-table").dataTable({
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
			"sAjaxSource": root_domain+'app/product_mst/',
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
function showtype(producttype){
	if(producttype== 'service'){
		$('.typepro').attr("style","display:none");
		$('.typeled').attr("style","display:block");
	}else{
		$('.typepro').attr("style","display:block");
		$('.typeled').attr("style","display:none");
	}
}