<?php 
	session_start();
	include_once("../config/config.php");
	include_once("../config/session.php");
	include_once("../include/common_functions.php");
	$token = md5(rand(1000,9999));
	$_SESSION['token'] = $token;
	$form="Item";
	$com="select * from tbl_company where company_id=".$_SESSION['company_id'];
	$comty=mysqli_fetch_assoc($dbcon->query($com));	
	//echo $_SESSION['branch_id'];
	//echo $_SERVER[REQUEST_URI];
	if(strpos($_SERVER[REQUEST_URI], "product_edit")==false) {
		$mode="Add";
	
	}
	else {
		$mode="Edit";
		$pro_id=$dbcon->real_escape_string($_REQUEST['id']);
		$query="select * from product_mst where product_id=$pro_id";
		$rel=mysqli_fetch_assoc($dbcon->query($query));	
		$check_array=explode(",",$rel['product_check']);
		$check_array_setting=explode(",",$rel['product_setting_check']);
		//print_r($check_array_setting);
	}
	

?>
<!DOCTYPE html>
<html lang="en">
<head>
	<?php include_once('../include/include_css_file.php');?>
	
	<style>
		
		.margin_row{
			
			margin-top:10px !important;
		}
		
		.margin_span{
			margin-left:10px !important;
			font-size:16px;
			vertical-align:middle;
		}
		
		.container input {
		  position: absolute;
		  opacity: 0;
		  cursor: pointer;
		  height: 0;
		  width: 0;
		}
				
				.checkmark {
		  position: absolute;
		  top: 0;
		  left: 0;
		  height: 25px;
		  width: 25px;
		  background-color: #eee;
		}

		/* On mouse-over, add a grey background color */
		.container:hover input ~ .checkmark {
		  background-color: #ccc;
		}

		/* When the checkbox is checked, add a blue background */
		.container input:checked ~ .checkmark {
		  background-color: #2196F3;
		}

		/* Create the checkmark/indicator (hidden when not checked) */
		.checkmark:after {
		  content: "";
		  position: absolute;
		  display: none;
		}

		/* Show the checkmark when checked */
		.container input:checked ~ .checkmark:after {
		  display: block;
		}

		/* Style the checkmark/indicator */
		.container .checkmark:after {
		  left: 9px;
		  top: 5px;
		  width: 5px;
		  height: 10px;
		  border: solid white;
		  border-width: 0 3px 3px 0;
		  -webkit-transform: rotate(45deg);
		  -ms-transform: rotate(45deg);
		  transform: rotate(45deg);
		}
		
		.img-wrap {
			position: relative;
		}
		.img-wrap .close {
			position: absolute;
			top: 2px;
			right: 2px;
			z-index: 100;
		}
		.head_margin
		{
			margin-bottom:10px;
		}
	</style>
	<script type="text/javascript" src="js/jquery.form.min.js"></script>
</head>
	<body>
		<section id="container" class="sidebar-closed">
			<?php include_once('../include/include_top_menu.php');?>
			<?php include_once('../include/left_menu.php');?>
			<section id="main-content">
				<section class="wrapper">
					<div class="row">
						<div class="col-lg-12">
							<section class="panel">
								<header class="panel-heading"><h3>New <?=$form?> </h3></header>	
								<div class="">
								  <ul class="breadcrumb">
									  <li><a href="<?=ROOT.'dashboard'?>"><i class="fa fa-home"></i> Home</a></li>
									  <li class="active"><a href="<?=ROOT.'product_list'?>"><?=$form?> List </a></li>
								  </ul>
								</div>
							</section>
						</div>	
					</div>
					<form role="form" id="product_add" action="javascript:;" method="post" name="product_add">
					<div class="row">
						<div class="col-sm-12">
							<section class="panel">
								<header class="panel-heading">
								  New <?=$form?> 
									<span class="tools pull-right">
										<a href="javascript:;" class="fa fa-chevron-down"></a>
									</span>
								</header>	
								<div class="panel-body">
									<div class="col-md-12" style="padding-top: 25px;">
										<div class="col-md-12 margin_row">
											<div class="col-md-4">
												<div class="form-group">
													<label for="Product Type" class="col-md-4 control-label">Product Type*</label>
													<div class="col-md-8 col-xs-11">
														<select class="select2" id="product_type" name="product_type" onchange="get_product_code(this.value);">
															<option value="">--Select Product Type--</option>
															<option value="0" <?php if($mode=='Edit'){ if($rel['product_type']=='0'){ echo "selected"; } } ?>>FINISH PRODUCT</option>
															
															<option value="1" <?php if($mode=='Edit'){ if($rel['product_type']=='1'){ echo "selected"; } } ?>>ASSEMBLY PRODUCT</option>
															
															<option value="2" <?php if($mode=='Edit'){ if($rel['product_type']=='2'){ echo "selected"; } } ?>>SUB ASSEMBLY</option>
															
															<option value="3" <?php if($mode=='Edit'){ if($rel['product_type']=='3'){ echo "selected"; } } ?>>RAW MATERIAL</option>
															
															<option value="4" <?php if($mode=='Edit'){ if($rel['product_type']=='4'){ echo "selected"; } } ?>>FINISH PART</option>
															
															<option value="5" <?php if($mode=='Edit'){ if($rel['product_type']=='5'){ echo "selected"; } } ?>>BOI</option>
															
															<option value="6" <?php if($mode=='Edit'){ if($rel['product_type']=='6'){ echo "selected"; } } ?>>CAPITAL GOODS</option>
															
															<option value="7" <?php if($mode=='Edit'){ if($rel['product_type']=='7'){ echo "selected"; } } ?>>CONSUMABLE</option>
														</select>
													</div>
												</div>							 
											</div>
										</div>
										<div class="col-md-12 margin_row">
											<div class="col-md-4">
												<div class="form-group">
												  <label for="Product Type" class="col-md-4 control-label">Product Name*</label>
												  <div class="col-md-8 col-xs-11">
													<input type="text"  class="form-control" id="product_name" name="product_name" placeholder="Product Name"  value="<?=$rel['product_name']?>" />
												  </div>
												</div>							 
											</div>
											<div class="col-md-4">
												<div class="form-group">
													<label for="Product Description" class="col-md-4 control-label">Description</label>
													<div class="col-md-8 col-xs-11">
														<textarea class="form-control" id="product_desc" name="product_desc" placeholder="Enter Product Description"><?=$rel['product_desc']?></textarea>
													</div>
												</div>
											</div>
											<div class="col-md-4">
												<div class="form-group">
													<label for="Product Type" class="col-md-4 control-label">Item Code</label>
													<div class="col-md-8 col-xs-11">
														<input type="text" class="form-control" id="product_icode" name="product_icode" placeholder="Item Code" value="<?=$rel['product_icode'];?>" readonly />
										
														<input type="hidden" class="form-control" id="product_icode_code" name="product_icode_code"  value="" readonly />
													</div>
												</div>
											</div>
										</div> 
										<div class="col-md-12 margin_row">
											<div class="col-md-4">
											  <div class="form-group">
												  <label for="Product Type" class="col-md-4 control-label">HSN Code</label>
												  <div class="col-md-8 col-xs-11">
													<input type="text" class="form-control" id="product_hsn" name="product_hsn" placeholder="HSN Code" value="<?=$rel['product_hsn']?>" required />
												  </div>
											  </div>
											</div>
											<div class="col-md-4">
												<div class="form-group">
													<label for="Product Type" class="col-md-4 control-label">Sale Rate</label>
													<div class="col-md-8 col-xs-11">
													  <input type="number" min="0" class="form-control" id="product_sale_rate" name="product_sale_rate" placeholder="Product Sale Rate" value="<?=$rel['product_sale_rate']?>" onkeypress="return isNumberKey(event)"  />
													</div>
												</div>
											</div>
											<div class="col-md-4">
											  <div class="form-group">
												  <label for="Product Type" class="col-md-4 control-label">Purchase Rate</label>
												  <div class="col-md-8 col-xs-11">
												  <input type="number" min="0" class="form-control" id="product_purchase_rate" name="product_purchase_rate" placeholder="Product Purchase Rate" value="<?=$rel['product_purchase_rate']?>" onkeypress="return isNumberKey(event)"  />
												  </div>
											  </div>
											</div>
										</div>
										<div class="col-md-12 margin_row">
											<div class="col-md-4">
												<div class="form-group">
													<label for="Product Type" class="col-md-4 control-label">GST Type</label>
													<div class="col-md-8 col-xs-11">
													<select class="select2" name="product_gst" id="product_gst"  title="Select Unit" required>
														<option value="">--Select GST Type--</option>
														<option value="including" <?php if($rel['product_gst']=='including'){ echo "selected"; }?>>Including</option>
														<option value="excluding" <?php if($mode=='Edit'){ if($rel['product_gst']=='excluding'){ echo "selected"; } } else { echo "selected"; } ?>>Excluding</option>
													</select>
													</div>
												</div>
											</div>
											<div class="col-md-4">
												<div class="form-group">
													<label for="Product Type" class="col-md-4 control-label">Sale GST</label>
													<div class="col-md-8 col-xs-11">
														<select class="select2" name="product_sale_gst" id="product_sale_gst"  title="Select Unit" required>
															<?=get_tax_percentage($dbcon,$rel['product_sale_gst']);?>
														</select>
													</div>
												</div>
											</div>
											<div class="col-md-4">
												<div class="form-group">
													<label for="Product Type" class="col-md-4 control-label">Purchase GST</label>
													<div class="col-md-8 col-xs-11">
														<select class="select2" name="product_purchase_gst" id="product_purchase_gst"  title="Select Unit" required>
															<?=get_tax_percentage($dbcon,$rel['product_purchase_gst']);?>
														</select>
													</div>
												</div>
											</div>									
										</div>								
										<div class="col-md-12 margin_row">
											<div class="col-md-4">  
												<div class="form-group">
													<label for="opening stock" class="col-md-4 control-label">Opening Stock</label>
													<div class="col-md-8 col-xs-11">
														<input type="number" name="product_opening" min="0" id="product_opening" class="form-control" placeholder="Opening Stock" value="<?=$mode=='Edit'?$rel['product_opening']:0?>" required />
													</div>
												</div>
											</div>
											<div class="col-md-4">  
												<div class="form-group">
													<label for="opening stock" class="col-md-4 control-label">Minimum Stock</label>
													<div class="col-md-8 col-xs-11">
														<input type="number" name="product_min_stock" min="0" id="product_min_stock" class="form-control" placeholder="Minimum Stock" value="<?=$mode=='Edit'?$rel['product_min_stock']:''?>" required />
													</div>
												</div>
											</div>
											<div class="col-md-4">  
												<div class="form-group">
													<label for="opening stock" class="col-md-4 control-label">Maximum Stock</label>
													<div class="col-md-8 col-xs-11">
														<input type="number" name="product_max_stock" min="0" id="product_max_stock" class="form-control" placeholder="Minimum Stock" value="<?=$mode=='Edit'?$rel['product_max_stock']:''?>" required />
													</div>
												</div>
											</div>
										</div>
										<div class="col-md-12 margin_row">
											<div class="col-md-4">  
												<div class="form-group">
													<label for="opening stock" class="col-md-4 control-label">Select Category</label>
													<div class="col-md-8 col-xs-11">
														 <select class="select2" name="product_category" id="product_category">
															<?=get_all_category($dbcon,$rel['product_category']);?>
														 </select>
													</div>
												</div>
											</div>
											<div class="col-md-4">  
												<div class="form-group">
													<label for="opening stock" class="col-md-4 control-label">Product Barcode</label>
													<div class="col-md-8 col-xs-11">
														<input type="text" class="form-control" name="product_barcode" id="product_barcode" value="<?=$rel['product_barcode']?>" />
													</div>
												</div>
											</div>	
											<div class="col-md-4"> 
												<?php if($_SESSION['user_type']=='1' || $_SESSION['user_type']=='2') { ?>
												<div class="form-group">
													<label class="col-md-4 control-label" style="">Select Branch </label>
													<div class="col-md-6 col-xs-11">
													
														<select class="select2" name="branchid" id="branchid">
														
															<?=get_branch($dbcon,$rel['branch_id'])?>	
															<option value="0" selected>Admin</option>							
														</select>
													</div>
													<?php } else {  ?>
														<input type="hidden" name="branchid" id="branchid" value="<?=$_SESSION['branch_id']; ?>" />	
													<?php } ?>
													
												</div>
												
											</div>
										</div>
										<div class="col-md-12 margin_row">
											<div class="col-md-4">
											  <div class="form-group">
													<label for="Product Type" class="col-md-4 control-label">Making Time</label>
													<div class="col-md-8 col-xs-11">
													<input type="text" class="form-control" name="product_making_time" id="product_making_time" value="<?=$mode=='Edit'?$rel['product_making_time']:0?>" /> ( In Minute..)
													</div>
											  </div>
											</div>
											<div class="col-md-4">
											  <div class="form-group">
													<label for="Product Type" class="col-md-4 control-label">Product Specifaication</label>
													<div class="col-md-8 col-xs-11">
														<select class="form-control" name="product_specification" id="product_specification">
															<?= get_product_specification($dbcon,$rel['product_specification']); ?>
														</select>
													</div>
											  </div>
											</div>
											<div class="col-md-4">  
												<div class="form-group">
													<label for="opening stock" class="col-md-4 control-label">Product Valuation</label>
													<div class="col-md-8 col-xs-11">
														<input type="text" class="form-control" name="product_opening_valuation" id="product_opening_valuation" value="<?=$rel['product_opening_valuation']?>" onkeypress="return isNumberKey(event)"  />
													</div>
												</div>
											</div>	
										</div>
										<div class="col-md-12 margin_row" style="margin-top:25px !important;">
											<div class="col-md-3">
											  <div class="form-group">
													<label for="Product Type" class="col-md-4 control-label">Base Unit</label>
													<div class="col-md-8 col-xs-11">
													<select class="select2" name="product_base_unit" id="product_base_unit"  title="Select Unit" onchange="get_product_unit(this.value)" required>
														<?php if($mode=='Edit') { echo getunit($dbcon,$rel['product_base_unit']); } else { echo getunit($dbcon,3); } ?>
													</select>
													
													</div>
											  </div>
											</div>
											<div class="col-md-3">
												<div class="form-group">
													<label for="Product Type" class="col-md-4 control-label">Qty</label>
													<div class="col-md-8 col-xs-11">
														<input type="text" class="form-control" name="product_base_qty" id="product_base_qty" value="<?php if($mode=='Edit'){ echo $rel['product_base_qty'];  } else { ?> 1 <?php } ?>" required  />
													</div>
												</div>
											</div>
											<div class="col-md-3">
											  <div class="form-group">
													<label for="Product Type" class="col-md-4 control-label">Conv. Unit</label>
													<div class="col-md-8 col-xs-11">
													<select class="select2" name="product_conv_unit" id="product_conv_unit"  title="Select Unit"  required>
														<?php if($mode=='Edit') { echo getunit($dbcon,$rel['product_conv_unit']); } else { echo getunit($dbcon,3); } ?>
													</select>
													
													</div>
											  </div>
											</div>
											<div class="col-md-3">
											  <div class="form-group">
													<label for="Product Type" class="col-md-4 control-label">Qty</label>
													<div class="col-md-8 col-xs-11">
														<input type="text" class="form-control" name="product_conv_qty" id="product_conv_qty" value="<?php if($mode=='Edit'){ echo $rel['product_conv_qty'];  } else { ?> 1 <?php } ?>" required  />
													</div>
											  </div>
											</div>
											<input type="hidden" name="mode" id="mode" value="<?php if($mode=='Add'){ echo "add"; } else { echo "edit"; } ?>" />
											<input type="hidden" name="eid_main" id="eid_main" value="<?php if($mode=='Edit'){ echo $rel['product_id']; } ?>" />
										</div>
										<div class="clearfix" style="margin-bottom:10px;"></div>	
										<div class="col-md-5"></div>
									</div>
									<div class="row" style="background-color:white !important;padding:10px;">
										<div class="col-md-4 col-md-offset-5">	
											<input type='hidden' name='token' id='token' value='<?php echo $token; ?>' />				  
											<input type='hidden' name='form_mode' id='form_mode' value='<?php echo $mode; ?>' />				  
											<input type='hidden' name='pid' id='pid' value='<?php if($mode=='Edit'){ echo $rel['product_id']; } else { echo "0"; } ?>' />				  
											<input type='hidden' name='product_model' id='product_model' value='' />				  
											<button type="submit" class="btn btn-shadow btn-success" style="box-shadow: 3px 3px #61a642;">Submit</button>
										</div>
									</div>
										
								</div>
							</section>
							</form>
						</div>
					</div>
					

  
		  <!-- End Tab View -->
		  
		  <!--Customer overview end-->
          </section>
		  
		 
      </section>
      <!--main content end-->
      <!--footer start-->
	<?php include_once('../include/footer.php');?>
      <!--footer end-->
  </section>
<!-- Modal -->

	</div><!-- /.modal-dialog -->
</div><!-- /.modal -->
<?php include_once('../include/add_productinpro.php');?>  
    <!-- js placed at the end of the document so the pages load faster -->
	<?php include_once('../include/include_js_file.php');?>   
	<script src="<?=ROOT?>js/app/product_mst.js?<?php echo time(); ?>"></script>
<script>
$(".select2").select2({
	width: '100%'
});
$('.default-date-picker').datepicker({
	format: 'dd-mm-yyyy',
	autoclose: true
});
 var tableToExcel = (function() {
 var uri = 'data:application/vnd.ms-excel;base64,'
   , template = '<html xmlns:o="urn:schemas-microsoft-com:office:office" xmlns:x="urn:schemas-microsoft-com:office:excel" xmlns="http://www.w3.org/TR/REC-html40"><head><!--[if gte mso 9]><xml><x:ExcelWorkbook><x:ExcelWorksheets><x:ExcelWorksheet><x:Name>{worksheet}</x:Name><x:WorksheetOptions><x:DisplayGridlines/></x:WorksheetOptions></x:ExcelWorksheet></x:ExcelWorksheets></x:ExcelWorkbook></xml><![endif]--></head><body><table>{table}</table></body></html>'
   , base64 = function(s) { return window.btoa(unescape(encodeURIComponent(s))) }
   , format = function(s, c) { return s.replace(/{(\w+)}/g, function(m, p) { return c[p]; }) }
 return function(table, name) {
   if (!table.nodeType) table = document.getElementById(table)
   var ctx = {worksheet: name || 'Worksheet', table: table.innerHTML}
   window.location.href = uri + base64(format(template, ctx))
 }
})()
</script>
</body>
</html>