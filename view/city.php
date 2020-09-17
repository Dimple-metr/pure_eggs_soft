<?php 
	session_start();
	include_once("../config/config.php");
	include_once("../config/session.php");
	include_once("../include/common_functions.php");
	$form="City";
	$infopage = pathinfo( __FILE__ );
	$_SESSION['page']=$infopage['filename'];
	$end = date("d-m-Y");
?>
<!DOCTYPE html>
<html lang="en">
<head>
<?php include_once('../include/include_css_file.php');?>
</head>
<body>
<section id="container" >
<?php include_once('../include/include_top_menu.php');?>
<!--sidebar start-->
<?php include_once('../include/left_menu.php');?>
<!--sidebar end-->
<!--main content start-->
<section id="main-content">
	<section class="wrapper">
		<div class="row">
			<div class="col-lg-12">
				<!--breadcrumbs start -->
				<section class="panel">
					<header class="panel-heading">
						<h3>New <?=$form?></h3>
					</header>	
					<div class="">
						<ul class="breadcrumb">
							<li><a href="<?=ROOT.'dashboard'?>"><i class="fa fa-home"></i> Home</a></li>
							<li class="active"><?=$form?> List</li>
						</ul>
					</div>
				</section>
				<!--breadcrumbs end -->
			</div>	
		</div>
		<!--state overview start-->
		<?php //include_once('../include/country_state_city.php');?>
		<div class="row">
			<div class="col-sm-3">
				<section class="panel">
					<header class="panel-heading">
						New <?=$form?>
					</header>	
					<div class="panel-body">
						<form role="form" id="city_add" action="javascript:;" method="post" name="city_add">
							<div class="form-group">
								<label for="countryid">Country</label>
								<select class="select2" id="countryid" name="countryid" onchange="load_state_by_country(this.value,'state_id','');">
									<?=get_country($dbcon,'')?>
								</select>
							</div>
							<div class="form-group">
								<label for="stateid">State</label>
								<select id="state_id" class="select2" name="state_id">
									<option selected disabled value="">SELECT STATE</option>
									<?//getstate($dbcon,'')?>
								</select>
							</div>
							<div class="form-group">
								<label for="city_initial">City Initial</label>
								<input type="text" class="form-control" id="city_initial" name="city_initial" placeholder="City Initial" />
							</div>
							<div class="form-group">
								<label for="catalog_name">City Name</label>
								<input type="text" class="form-control" id="city_name" name="city_name" placeholder="City Name" />
							</div>							  				  
							<button type="submit" class="btn btn-info">Submit</button>
						</form>
						
					</div>
				</section>
			</div>
			<div class="col-sm-9">
				<section class="panel">
					<header class="panel-heading">
						City List
						<span class="pull-right">
							<a href="<?=ROOT.'state'?>" type="button" class="btn btn-success">State List</a> 
							<?if($_SESSION['user_type'] == 2){?>
							<a href="javascript:;" onClick="tableToExcel('city-table', 'Instalment Collection')" ><button class="btn btn-info btn-flat" >Export Excel</button></a>	
							<?}?>
						</span>
						<div class="col-md-12" style="height:20px;"></div>
						<div class="col-md-6">
							<!--<div class="col-md-4" style="text-align:left;">Choose Country</div>-->
							<div class="col-md-8">
								<select class="select2" name="filter_country_id" id="filter_country_id" onchange="load_city_datatable();load_state_by_country(this.value,'filter_state_id','');">
									<?=get_country($dbcon,'');?>
								</select>
							</div>
						</div>
						<div class="col-md-6">
							<!--<div class="col-md-4" style="text-align:left;">Choose Country</div>-->
							<div class="col-md-8">
								<select class="select2" name="filter_state_id" id="filter_state_id" onchange="load_city_datatable();">
									<option value="">All</option>
									<?//=get_country($dbcon,'');?>	
								</select>
							</div>
						</div>
					</header>
					<div class="panel-body">
						<div class="adv-table">
							<table class="display table table-bordered table-striped" id="city-table">
								<thead>
									<tr>
										<th>Sr. NO.</th>
										<th>City Initial</th>					  
										<th>City Name</th>					  
										<th>State Name</th>
										<th>Country Name</th>
										<th class="hidden-phone">Action</th>					  
									</tr>
								</thead>
								<tbody>
								</tbody>				 
							</table>
						</div>
					</div>
				</section>
			</div>
		</div>
		<input type="hidden" name="ctid" id="ctid" value="<?=$end?>">
		<!--state overview end-->
	</section>
</section>
<!--main content end-->
<!--footer start-->
<?php include_once('../include/footer.php');?>
<!--footer end-->
</section>
<!-- Modal -->
<div class="modal colored-header info" id="ModalEditAccount" role="dialog" data-keyboard="false" data-backdrop="static">
<div class="modal-dialog custom-width">
	<div class="modal-content">
		<div class="modal-header">
			<button type="button" class="close md-close" data-dismiss="modal" aria-hidden="true">&times;</button>
			<h3>Edit City</h3>				
		</div>
		<div class="modal-body form">
			<form id="FormEditCity" role="form" method="post" novalidate>				
				<div class="form-group">
					<label class="control-label">City Initial</label>
					<input type="text" class="form-control" id="edit_city_initial" name="edit_city_initial" placeholder="City Initial" />
				</div>
				<div class="form-group">
					<label class="control-label">City Name</label>
					<input type="text" name="edit_city_name" id="edit_city_name" class="form-control" required >
				</div>				
				<div class="form-group">
					<label class="control-label">State</label>
					<select id="edit_stateid" class="select2" name="edit_stateid" required>
						<option selected disabled value="">SELECT STATE</option>
						<?getstate($dbcon,'')?>
					</select>
				</div>				
			</div>
			<div class="modal-footer">
				<input type="hidden" name="edit_id" id="edit_id" value="" />
				<button type="button" class="btn btn-default btn-flat md-close" data-dismiss="modal">Cancel</button>
				<button class="btn btn-info btn-flat" type="submit">Update City</button>
			</div>
		</form>
	</div><!-- /.modal-content -->
</div><!-- /.modal-dialog -->
</div><!-- /.modal -->

<!-- js placed at the end of the document so the pages load faster -->
<?php include_once('../include/include_js_file.php');?>   
<script src="<?=ROOT?>js/app/city_mst.js"></script>
<script>
$(".select2").select2({
	width: '100%'
});
$('#edit_stateid').select2({
	width: '100%',
	minimumInputLength: 3 // only start searching when the user has input 3 or more characters
});
var tableToExcel = (function() {
	var uri = 'data:application/vnd.ms-excel;base64,'
	, template = '<html xmlns:o="urn:schemas-microsoft-com:office:office" xmlns:x="urn:schemas-microsoft-com:office:excel" xmlns="http://www.w3.org/TR/REC-html40"><head><!--[if gte mso 9]><xml><x:ExcelWorkbook><x:ExcelWorksheets><x:ExcelWorksheet><x:Name>{worksheet}</x:Name><x:WorksheetOptions><x:DisplayGridlines/></x:WorksheetOptions></x:ExcelWorksheet></x:ExcelWorksheets></x:ExcelWorkbook></xml><![endif]--></head><body><table>{table}</table></body></html>'
	, base64 = function(s) { return window.btoa(unescape(encodeURIComponent(s))) }
	, format = function(s, c) { return s.replace(/{(\w+)}/g, function(m, p) { return c[p]; }) }
	return function(table, name) {
		if (!table.nodeType) table = document.getElementById(table)
		var ctx = {worksheet: name || 'Worksheet', table: table.innerHTML}
		//window.location.href = uri + base64(format(template, ctx))
		var ctid= $('#ctid').val();
	var link = document.createElement("a");
    link.download = "city-list-# "+ctid + ".xls";
    link.href = uri + base64(format(template, ctx));
    link.click();
	}
})()
</script>
</body>
</html>
