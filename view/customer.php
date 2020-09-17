<?php 
	session_start();
	include_once("../config/config.php");
	include_once("../config/session.php");
	include_once("../include/common_functions.php");
	$form="Party";
	if(strpos($_SERVER[REQUEST_URI], "customer_edit")==false) {
		$mode="Add";
		$countryid="101";
		$stateid="1";
		$cityid="1";
	}
	else {
		$mode="Edit";
		$cust_id=$dbcon->real_escape_string($_REQUEST['id']);
		$query="select * from tbl_customer where cust_id=$cust_id";
		$rel=mysqli_fetch_assoc($dbcon->query($query));	
		$countryid=$rel['countryid'];
		$stateid=$rel['stateid'];
		$cityid=$rel['cityid'];
	}
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
				<h3><?=$mode.' '.$form?></h3>
			</header>	
			<div class="">
				<ul class="breadcrumb">
					<li><a href="<?=ROOT.'dashboard'?>"><i class="fa fa-home"></i> Home</a></li>
					<li><a href="<?=ROOT.'customer_list'?>"><?=$form?> List</a></li>
				</ul>
			</div>
		</section>
		<!--breadcrumbs end -->
	</div>	
</div>
<!--state overview start-->
<div class="row">			
	<div class="col-sm-12">
		<section class="panel">
			<header class="panel-heading">
				New <?=$form?>
			</header>	
			<div class="panel-body ">
				<form class="form-horizontal" role="form" id="cust_add" action="javascript:;" method="post" name="cust_add">
					<div class="row">
						<div class="col-md-10">
							
							<div class="form-group">
								<label class="col-md-3 control-label">Company Name *</label>
								<div class="col-md-6 col-xs-11">
									<input type="text" class="form-control" placeholder="Company Name" title="Company Name" name="company_name" id="company_name" value="<?=$rel['company_name']?>"/>
								</div>
							</div>
							<div class="form-group">
								<label class="col-md-3 control-label">Contact Person Name </label>
								<div class="col-md-6 col-xs-11">
									<input type="text" class="form-control" placeholder="Contact Person Name" title="Contact Person Name" name="cust_name" id="cust_name" value="<?=$rel['cust_name']?>"/>
								</div>
							</div>
							<div class="form-group">
								<label class="col-md-3 control-label">Address *</label>
								<div class="col-md-6 col-xs-11">
									<textarea id="cust_address" name="cust_address" class="form-control" rows="5" placeholder="Address"><?=text_rnrremove_disp($rel['cust_address'])?></textarea> 
								</div>
							</div>
							
							<div class="form-group">
								<label class="col-md-3 control-label">Select Country *</label>
								<div class="col-md-6 col-xs-11">
									<select class="select2" name="countryid" id="countryid" onChange="load_state(this.value,'stateid','')">
										<?=get_country($dbcon,$countryid)?>				
									</select>
								</div>
							</div>
							<div class="form-group">
								<label class="col-md-3 control-label">Select State *</label>
								<div class="col-md-6 col-xs-11">
									<select class="select2" name="stateid" id="stateid" onChange="load_city(this.value,'cityid','')">
										<option value="">Select State</option>	
										<?//=getstate($dbcon,$rel['stateid'])?>				
									</select>
								</div>
								<input type="button"  name="addState" id="addState" data-toggle="modal" data-target="" onclick="add_state();" class="btn btn-primary" value="+ Add State"/>
							</div>	
							<div class="form-group">
								<label class="col-md-3 control-label">Select City *</label>
								<div class="col-md-6 col-xs-11">
									<select class="select2" name="cityid" id="cityid">
										<option value="">Select City</option>	
									</select>
								</div>
								<input type="button" name="addCity" id="addCity" data-toggle="modal" data-target="" onclick="add_city();" class="btn btn-primary" value="+ Add city"/>
							</div>	
							
							<div class="form-group">
								<label class="col-md-3 control-label">Mobile No. </label>
								<div class="col-md-6 col-xs-11">
									<input type="text" class="form-control" placeholder="Mobile No." name="cust_mobile" id="cust_mobile" value="<?=$rel['cust_mobile']?>"  />
								</div>
							</div>
							<div class="form-group">
								<label class="col-md-3 control-label">Email </label>
								<div class="col-md-6 col-xs-11">
									<input type="text" class="form-control" placeholder="Email" title="Email" name="cust_email" id="cust_email" value="<?=$rel['cust_email']?>"  />
								</div>	
							</div>
							<div class="form-group">
								<label class="col-md-3 control-label">Pin Code</label>
								<div class="col-md-6 col-xs-11">
									<input type="text" class="form-control" placeholder="Customer Pincode" name="cust_pincode" id="cust_pincode"   value="<?=$rel['cust_pincode']?>"  />
								</div>
							</div>
							<div class="form-group">
							 <label class="col-md-3 control-label">PAN No</label>
								<div class="col-md-6 col-xs-11">
									<input type="text" class="form-control" placeholder="PAN  No" name="pan_no" id="pan_no"   value="<?=$rel['pan_no']?>"  />
								</div>
							</div>
							<div class="form-group">
							 <label class="col-md-3 control-label">GSTIN</label>
								<div class="col-md-6 col-xs-11">
									<input type="text" name="gst_no" class="form-control" placeholder="GSTIN"id="gst_no" value="<?=$rel['gst_no']?>" title="Enter Valid GST No." >
								</div>
							</div>
							<div class="form-group">
								<label class="col-md-3 col-xs-12 control-label">Opening Balance *</label>
								<div class="col-md-3 col-xs-6">
									<input type="number" class="form-control" placeholder="Amount" name="opening_balance" id="opening_balance"   value="<?=$rel['opening_balance']?>" min="0" title="Enter Opening Balance" placeholder="Enter Opening Balance"/>
								</div>
								<div class="col-md-3 col-xs-6">
									<select class="select2" name="balance_typeid" id="balance_typeid" title="Select Type">
										<?=getbalance_type($dbcon,$rel['balance_typeid'])?>				
									</select>
								</div>
							</div>
							<div class="form-group">
								<label class="col-md-3 control-label">Group</label>
								<div class="col-md-6 col-xs-11">
									<select class="select2" name="group_id" id="group_id" >
										<?=get_group($dbcon,$rel['group_id'])?>				
									</select>
								</div>
							</div>
							
							<!--<div class="form-group">
								<div class="checkbox">
									<label class="col-md-offset-3">
										<input type="checkbox" id="multi_company" name="multi_company" <?=($mode=="Add"?'checked':($rel['multi_company']=="1"?'checked':''))?> value="1">  View in all Company
									</label>
								</div>
							</div>-->
							<button type="submit" class="btn btn-success">Submit</button> &nbsp;
						<a href="<?=ROOT.'customer_list'?>" type="button" class="btn btn-danger">Cancel</a><div class="col-md-3"></div>					</div>
					</div><!--Vendor row end-->	
					<input type='hidden' name='mode' id='mode' value='<?=$mode?>' />
					<input type='hidden' name='eid' id='eid' value='<?=$rel['cust_id']?>' />
					
				</form>
			</div>	
		</section>
	</div>
</div>
<!--state overview end-->
</section>
</section>
<!--main content end-->
<!--footer start-->

<?php include_once('../include/add_city.php');?>
<?php include_once('../include/add_state.php');?>
<?php include_once('../include/footer.php');?>
<!--footer end-->
</section>

<!-- js placed at the end of the document so the pages load faster -->
<?php include_once('../include/include_js_file.php');?>   
<script src="<?=ROOT?>js/app/customer.js?<?=time()?>"></script>
<script src="<?=ROOT?>js/app/state_mst.js"></script>
<script src="<?=ROOT?>js/app/city_mst.js"></script>
<script>
$(".select2").select2({
	width: '100%'
});
$('.default-date-picker').datepicker({
	format: 'dd-mm-yyyy',
	autoclose: true
});
</script>
<?php
	
		echo "<script>load_state(".$countryid.",'stateid',".$stateid.")</script>";
		echo "<script>load_city(".$stateid.",'cityid',".$cityid.")</script>";
	
?>
</body>
</html>
