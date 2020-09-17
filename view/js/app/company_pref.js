//var datatable;
$(document).ready(function() {

});
$("#company_pref_add").on('submit',function(e) {

	var form = this;
	e.preventDefault();
	e.stopPropagation();	
	if (!$("#company_pref_add").valid()) {
		return false;
	}
	form.submitted = true;	
	Loading(true);	
	$(this).attr("disabled","disabled");		
	var form_data=new FormData(this);
	$.ajax({
		cache:false,
		url: root_domain+'app/company_pref/',
		type: "POST",
		data: form_data,
		contentType: false,
		processData:false,	
		success: function(response)
		{
			console.log(response);			
			if(response.trim() == 'update') {
				Unloading();
				window.location=root_domain+'company_list';
				toastr.success("COMPANY PREFERENCE UPDATE SUCCESSFULLY", "SUCCESS");		
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

