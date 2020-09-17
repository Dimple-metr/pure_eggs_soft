//var datatable;
$(document).ready(function() {

    // validate vendor add form on keyup and submit
    $("#a_add").validate({
            rules: {
                    company_name: {
                            required: true			
                    },
                    address: {
                            required: true,
                            minlength: 15
                    },
                    inventory_management: {
                            required: true			
                    }
            },
            messages: {
                    company_name: {
                            required: "Enter Company Name"
                    },
                    address: {
                            required: "Enter Address",
                            minlength: "Your Description must consist of at least 15 characters"
                    },
                    inventory_management: {
                            required: "Select Inventory Management"
                    }

            }

    });
});
$("#a_add").on('submit',function(e) {
								 
	for (instance in CKEDITOR.instances) 
	{
    	CKEDITOR.instances[instance].updateElement();
	}	
	
	var form = this;
	e.preventDefault();
	e.stopPropagation();	
	if (!$("#a_add").valid()) {
		return false;
	}
	form.submitted = true;	
	Loading(true);	
	$(this).attr("disabled","disabled");		
	var form_data=new FormData(this);
	$.ajax({
		cache:false,
		url: root_domain+'app/setting/',
		type: "POST",
		data: form_data,
		contentType: false,
		processData:false,	
		success: function(response)
		{
			//console.log(response);			
			if(response.trim() == 'update') {
				Unloading();
				window.location=root_domain+'setting/'+$("#eid").val();
				toastr.success("UPDATE SUCCESSFULLY", "SUCCESS");		
			}
			else if(response == '0') {
				toastr.warning("SOMETHING WRONG", "ERROR")
				Unloading();
			}
			else if(response == '-1')
			{
				toastr.info("ALREADY EXISTS", "INFO")
				Unloading();				
			}
			$('#serv_add').trigger('reset');	
		},
		error: function(jqXHR, textStatus, errorThrown) {
			console.log(textStatus, errorThrown);
		}
	});
	
});

