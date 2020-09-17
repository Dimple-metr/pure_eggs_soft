//var datatable;
$(document).ready(function() {
	/*$('#product_amount').hover(function(){
       var pro_amt = $('#product_amount').val();
		$('#product_amount').attr("title",pro_amt);
    });*/
	show_upload_docs();
// validate vendor add form on keyup and submit
 $("#purchaseorder_add").validate({
	rules: {
		vender_id: {
			required: true			
		},
		purchaseorder_no: {
			required: true			
		},
		purchaseorder_date:{
			required : true	
		}
	},
	messages: {
		vender_id: {
			required: "Select Vendor"
		},
		purchaseorder_no: {
			required: "Enter P.O no"
		},
		purchaseorder_date:{
			required : "Enter P.O date"
		}
	}
}); 
});


function upload_docs(id) 
{ 
	   var data = new FormData();
	   data.append('file', $('#file').prop('files')[0]);
	   data.append("mode",$('#img_mode').val());
	   data.append("l_id",$('#l_id').val());
	   data.append("docs_id",$('#docs_id').val());
	 
	  // alert(form_data);
	   $.ajax({
	   url: root_domain+'app/ledger/',
		method:"POST",
		data: data,
		contentType: false,
		cache: false,
		processData: false,
		beforeSend:function(){
		 //$('#uploaded_image').html("<label class='text-success'>Image Uploading...</label>");
		 Loading(true);	
		},   
		success:function(data)
		{
			Unloading();
			show_upload_docs();
		}
	   });
}

function show_upload_docs()
{
	var l_id = $('#l_id').val();
	//alert(l_id);
	
	 $.ajax({
		 
	    url: root_domain+'app/ledger/',
		method:"POST",
		data: { mode : "show_upload_docs", l_id:l_id },
		success: function(data){
			//console.log(data);
			$('#show_document').html(data);				
			Unloading();
		}		
		
	});
}