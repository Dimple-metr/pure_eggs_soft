<?php 
	session_start();
	include_once("../config/config.php");
	include_once("../config/session.php");
	include_once("../include/common_functions.php");
	$form="Branch ";
	if(strpos($_SERVER[REQUEST_URI], "branchedit")==false) {
		$mode="Add";
		$countryid="101";
		$stateid="1";
		$cityid="1";
	}
	else {
		$mode="Edit";
		$user_id=$dbcon->real_escape_string($_REQUEST['id']);
		$query="select * from tbl_branch where branch_id=$user_id";
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
						<li ><a href="<?=ROOT.'branch_list'?>">Branch List</a></li>
						
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
					<form class="form-horizontal" role="form" id="branch_add" action="javascript:;" method="post" name="branch_add">
						<div class="row">
							<div class="col-md-10">
								<div class="form-group">
									<label class="col-md-3 control-label">Branch Name *</label>
									<div class="col-md-6 col-xs-11">
										<input type="text" class="form-control" placeholder="Branch Name" name="branch_name" id="branch_name"  value="<?=$rel['branch_name']?>"/>
									</div>
								</div>
								<div class="form-group">
									<label class="col-md-3 control-label">Branch Email *</label>
									<div class="col-md-6 col-xs-11">
										<input type="text" class="form-control" placeholder="Branch Email" name="branch_email" id="branch_email" value="<?=$rel['branch_email']?>"/>
									</div>
								</div>
								<?if($mode!="Edit"){ ?>
								<div class="form-group">
									<label class="col-md-3 control-label">Password *</label>
									<div class="col-md-6 col-xs-11">
										<input type="password" class="form-control" placeholder="Password" name="password" id="password" value="" <? if($mode=="Add"){ echo 'required';}?>/>
									</div>
								</div>
								<? } ?>
								<div class="form-group">
									<label class="col-md-3 control-label">Branch Address *</label>
									<div class="col-md-6 col-xs-11">
										<textarea id="branch_address" name="branch_address" class="form-control" rows="5" ><?=$rel['branch_address']?></textarea> 
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
											
										</select>
									</div>
								</div>	
								<div class="form-group">
									<label class="col-md-3 control-label">Select City *</label>
									<div class="col-md-6 col-xs-11">
										<select class="select2" name="cityid" id="cityid"  onChange="">
											
										</select>
									</div>
								</div>	
								<div class="form-group">
									<label class="col-md-3 control-label">Mobile no *</label>
									<div class="col-md-6 col-xs-11">
										<input type="text" class="form-control" placeholder="Branch Mobile" name="branch_mobile" id="branch_mobile" value="<?=$rel['branch_mobile']?>"/>
									</div>	
								</div>
								<div class="form-group">
									<label class="col-md-3 control-label">Pin Code</label>
									<div class="col-md-6 col-xs-11">
										<input type="text" class="form-control" placeholder="Pincode" name="branch_pincode" id="branch_pincode" value="<?=$rel['branch_pincode']?>"/>
									</div>	
								</div>
								<!--<div class="form-group">
									<label class="col-md-3 control-label">User Type *</label>
									<div class="col-md-6 col-xs-11">
										<select class="form-control" id="usertype_id" name="usertype_id">
											<?=getusertype($dbcon,$rel['user_type']," and (usertype_id=2 or company_id=".$_SESSION['company_id'].")")?>
										</select>
									</div>	
								</div>-->
								
								<button type="submit" class="btn btn-success">Submit</button> &nbsp;
							<a href="<?=ROOT.'branch_list'?>" type="button" class="btn btn-danger">Cancel</a><div class="col-md-3"></div>					</div>
						</div><!--Vendor row end-->	
						<input type='hidden' name='mode' id='mode' value='<?=$mode?>' />
						<input type='hidden' name='eid' id='eid' value='<?=$rel['branch_id']?>' />
						<!--<input type='hidden' name='company_id' id='company_id' value='<?//=$rel['company_id']?>' />-->
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
<?php include_once('../include/footer.php');?>
<!--footer end-->
</section>

<!-- js placed at the end of the document so the pages load faster -->
<?php include_once('../include/include_js_file.php');?>   
<script src="<?=ROOT?>js/app/branch_add.js?<?=time()?>"></script>
<script>

$(".select2").select2({
	width: '100%'
});
$('.default-date-picker').datepicker({
	format: 'dd-mm-yyyy',
	autoclose: true
});</script>

<?php
		
		//echo "<script>load_state('stateid',1);</script>";
		echo "<script>load_state(".$countryid.",'stateid',".$stateid.")</script>";
		echo "<script>load_city(".$stateid.",'cityid',".$cityid.")</script>";
?>

</body>
</html>
