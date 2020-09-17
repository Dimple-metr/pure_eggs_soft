<div id="mask" class="hidden-xs" style="height: 681px;">
		<div style="position:fixed;left: 45%;margin-left: -25%px;">
			<img src="<?=ROOT?>img/loading_lg.gif">
			<h1> Loading ... </h1>
		</div>
    </div>

 
<script src="<?=ROOT?>js/canvasjs.min.js"></script>

<script src="<?=ROOT?>js/bootstrap.min.js"></script>
<script class="include" type="text/javascript" src="<?=ROOT?>js/jquery.dcjqaccordion.2.7.js"></script>
<script src="<?=ROOT?>js/jquery.scrollTo.min.js"></script>
<script src="<?=ROOT?>js/jquery.nicescroll.js" type="text/javascript"></script>
<!--form Validation js-->
<script type="text/javascript" src="<?=ROOT?>js/jquery.validate.min.js"></script>
<!--Gallery-->
<script src="<?=ROOT?>assets/fancybox/source/jquery.fancybox.js"></script>
<script src="<?=ROOT?>js/modernizr.custom.js"></script>
<!--For multiselect-->
<script type="text/javascript" src="<?=ROOT?>assets/jquery-multi-select/js/jquery.multi-select.js"></script>
  <script type="text/javascript" src="<?=ROOT?>assets/jquery-multi-select/js/jquery.quicksearch.js"></script>
<!--For Wysihtml editor-->
  <script type="text/javascript" src="<?=ROOT?>assets/fuelux/js/spinner.min.js"></script>
<!--Datatable js-->

<!--<script type="text/javascript" language="javascript" src="<?=ROOT?>assets/advanced-datatable/media/js/jquery.dataTables.js"></script>-->
<script type='text/javascript' src='<?=ROOT?>assets/data-tables/jquery.datatables.min.js'></script>
<script type='text/javascript' src='<?=ROOT?>assets/data-tables/datatables.js'></script>
<!--<script type="text/javascript" src="<?=ROOT?>assets/data-tables/DT_bootstrap.js"></script>
<script type='text/javascript' src='<?=ROOT?>assets/data-tables/jquery.datatables.min.js'></script>-->

<script src="<?=ROOT?>js/respond.min.js" ></script>
<!--Message-->
<script src="<?=ROOT?>assets/toastr-master/toastr.js"></script>

<script type="text/javascript" src="<?=ROOT?>assets/bootstrap-datepicker/js/bootstrap-datepicker.js"></script>
<script type="text/javascript" src="<?=ROOT?>assets/bootstrap-datetimepicker/js/bootstrap-datetimepicker.js"></script>
<script type="text/javascript" src="<?=ROOT?>assets/bootstrap-daterangepicker/moment.min.js"></script>

  <script type="text/javascript" src="<?=ROOT?>assets/bootstrap-daterangepicker/daterangepicker.js"></script>
   <script type="text/javascript" src="<?=ROOT?>assets/bootstrap-timepicker/js/bootstrap-timepicker.js"></script>
   	<script src="<?=ROOT?>js/summernote.min.js"></script>
<!--common script for all pages-->
<script src="<?=ROOT?>js/common-scripts.js?<?=date('dmy');?>"></script>
<script type='text/javascript' src='<?=ROOT?>js/jquery.select2/select2.min.js' ></script>
<script src="<?=ROOT?>js/jquery.cookies.js"></script>
<script type="text/javascript" src="<?=ROOT?>js/moment.js"></script>
<script type="text/javascript" src="<?=ROOT?>js/daterangepicker.js"></script>
<!--<script type="text/javascript" src="<?=ROOT?>js/shortcut.js"></script>-->
<script type="text/javascript" src="<?=ROOT?>js/ckeditor/ckeditor.js"></script>

<script type="text/javascript" src="<?=ROOT?>js/mousetrap/mousetrap.min.js"></script>
<script type="text/javascript" src="<?=ROOT?>js/mousetrap/mousetrap-bind-dictionary.min.js"></script>
		
<script>
function change_company()
{
		var r= confirm(" Are you sure to change company?");
		if(r) {
		open_company_modal(1)
		}
}/*
shortcut.add("Ctrl+i",function() {
	window.location=root_domain+"invoice";
});
shortcut.add("Ctrl+l",function() {
	window.location=root_domain+"invoice_list";
});
shortcut.add("Ctrl+d",function() {
	window.location=root_domain+"dashboard";
});
shortcut.add("Esc",function() {
	$("#show_todotask").modal("hide");
	$("#add_todotask").modal("hide");
});
*/
function open_company_modal(val)
{
	if(val==1)
	{
		$("#company_modal").modal("show");
	}
	else if(val==2)
	{
		$("#company_modal").modal("hide");
	}
}
function create_com()
{
	window.location=root_domain+"create_company";
	
}
function pass_session(company_name,company_id)
{
	$("#login_company").html(company_name);
	$("#logincompany_id").val(company_id);
	$("#company_modal").modal("show");
	$("#companylogin_modal").modal("show");
	
	Loading();
			$.ajax({
				type: "POST",
				url: root_domain+'app/dashboard/',
				data: { mode : "pass_session",  company_name : company_name,company_id:company_id },
				success: function(response)
				{
					//console.log(response);
					var res=jQuery.parseJSON(response);
					if(res.msg=="1")
					{
						window.location=root_domain+'dashboard';
						$("#company_modal").modal("hide");
						$("#companylogin_modal").modal("hide");
					}
					else if(res.msg=="0")
					{
						$("#loginusertype_id").html(res.response);
					}
					Unloading();
				}
	});	
	/*	Loading();
			$.ajax({
				type: "POST",
				url: root_domain+'app/dashboard/',
				data: { mode : "pass_session",  company_name : company_name,company_id:company_id },
				success: function(response)
				{
					console.log(response);
					$("#company_modal").modal("hide");
					$("#session_com").html(response);
					Unloading();
				}
			});	*/
}
function change_top_status(id,todo_status) 
{
	var r= confirm(" Are you want to Change Task Status ?");

		if(r) {
			Loading();
			$.ajax({
				type: "POST",
				url: root_domain+'app/todomst/',
				data: { mode : "change_status", eid : id, todo_status:todo_status },
				success: function(response)
				{
					
					if(response.trim() == "1") {
						toastr.success("TASK SUCCESSFULLY COMPLETED", "SUCCESS");
						Unloading();
						location.reload();
						/*show_todolist();
						datatable.fnReloadAjax();*/
					}
					else if(response.trim() == "0") {
						toastr.warning("SOMETHING WRONG", "WARNING");
					}							
				}
			});	
		}
	
}
</script>
 <?
			include_once("../include/company_modal.php");
			include_once("../include/companylogin_modal.php");
		
			
			if(empty($_SESSION['company_name']))
			{
				if(strtolower(end(explode("/",$_SERVER['REQUEST_URI'])))!="create_company")
				{
					echo "<script>open_company_modal(1)</script>";
				}
			}
		?>