<?php 

	session_start();
	include_once("../config/config.php");
	include_once("../config/session.php");
	
	include_once("../include/common_functions.php");
	$token = md5(rand(1000,9999));
	$_SESSION['token'] = $token;
	$form="Stock In";
	$countryid='101';
	$stateid='1';
	$cityid='1';
	if(strpos($_SERVER[REQUEST_URI], "stock_in_edit")==false)
	{
		$mode="Add";
		$date=date('d-m-Y');
		$stock_in_date=date('d-m-Y');
		$employee_id=$_SESSION['user_id'];
		$stock_in_no=series_no($dbcon,"6");
		
	}
	else
	{
		$mode="Edit";
		$poid=$dbcon->real_escape_string($_REQUEST['id']);
		 $query="select * from  tbl_stock_in where stock_in_id=$poid";
		$rel=mysqli_fetch_assoc($dbcon->query($query));	
		$stock_in_date='';
		if($rel['stock_in_date']!="1970-01-01" && $rel['stock_in_date']!="0000-00-00")
		{
			$stock_in_date=date('d-m-Y',strtotime($rel['stock_in_date']));
		}
		$employee_id=$rel['employee_id'];
		$stock_in_no=$rel['stock_in_no'];
	}
		$set="select * from tbl_company where company_id=".$_SESSION['company_id'];
		$set_head=mysqli_fetch_assoc($dbcon->query($set));	
?>

<!DOCTYPE html>
<html lang="en">
	<head>
		<?php include_once('../include/include_css_file.php');?>
		<style>
			.wideInput1{
				text-align: left;
				padding: 0.4em 0.4em 0.4em 0;
				width: 400px;
				height: 200px;
			}
			.g_tax{
				background-color: #100648;
				color: #f5f5f5;
				border: 3px solid #100648;
			}
		</style>
	</head>
	<body>
		<section id="container">
			<?php include_once('../include/include_top_menu.php');?>
			<?php include_once('../include/left_menu.php');?>
			<section id="main-content">
				<section class="wrapper">
					<div class="row">
						<div class="col-lg-12">
							<section class="panel">
								<header class="panel-heading"><h3><?=$mode.' '.$form?></h3></header>	
								<div class="">
									<ul class="breadcrumb">
										<li><a href="<?=ROOT.'dashboard'?>"><i class="fa fa-home"></i> Home</a></li>
										<li><a href="<?=ROOT.'stock_in_list'?>"><?=$form?> List</a></li>
									</ul>
								</div>
							</section>
						</div>	
					</div>
					<div class="row">			
						<div class="col-sm-12">
							<section class="panel">
								<header class="panel-heading">New <?=$form?></header>	
								<div class="panel-body">
									<form class="form-horizontal" role="form" id="po_add" action="javascript:;" method="post" name="po_add">
										<div class="row">
											<div class="col-md-12"  style="margin-top:10px;">
												<div class="col-md-6">
													<div class="form-group">
													  <label class="col-md-4 control-label">Stock In No </label>
													  <div class="col-md-6 col-xs-11">
														<input id="stock_in_no" name="stock_in_no" type="text" class="form-control" title="Stock In No" value="<?=$stock_in_no?>" placeholder="Stock In No" >
														</div>
													 </div>	
												</div>	
												<div class="col-md-6">  	
													 <div class="form-group">  	
													  <label class="col-md-4 control-label" >Stock In Date </label>
													  <div class="col-md-5 col-xs-11">
														<input id="stock_in_date" name="stock_in_date" type="text" class="form-control default-date-picker" title="Stock In Date" onchange="get_user_out();" value="<? if($mode=='Add'){ echo $stock_in_date;}else if($mode=='Edit'){ echo date('d-m-Y',strtotime($stock_in_date));}?>" placeholder="Stock In Date">
														</div>
													 </div>	
												</div>	
											</div>
											<?if($_SESSION['user_type']==2) { ?>
											<div class="col-md-12" style="margin-bottom:10px;">
												<label class="col-md-2 control-label" style="">Select Employee </label>
												<div class="col-md-3 col-xs-10 resclear" >
													<select class="select2" name="employee_id" id="employee_id" required title="Select Employee" onchange="get_user_out();">
														<?=get_user_in($dbcon,$employee_id)?>
													</select>
												</div>
											</div>	
											<? }else {  ?>
											<input type="hidden" name="employee_id" id="employee_id" value="<?=$employee_id?>" />
											<? } ?>
											<div class="col-md-12" style="clear: both;margin-top: 45px !important;">
												<div id="sale_productdata"> </div>	
												<div class="col-md-6">
													<div class="form-group">
														<label class="col-md-3 control-label">Remarks </label>
														<div class="col-md-9 col-xs-11">
															<textarea id="remark" name="remark" placeholder="Remarks" class="form-control" rows="3"><?=$rel['remark']?></textarea> 
														</div>
													</div> 
												</div>
											</div>
											<div class="col-md-12 text-center">	
												<button type="submit" class="btn btn-success" id="save" name="save">Submit</button>
												<a href="<?=ROOT.'stock_in_list'?>" type="button" class="btn btn-danger">Cancel</a>
											</div>	
											<div class="col-md-3"></div>					
										</div>
										<input type='hidden' name='mode' id='mode' value='<?=$mode?>' />
										<input type='hidden' name='eid' id='eid' value='<?=$rel['stock_in_id']?>' />
									</form>
								</div>
							</section>
						</div>
					</div>		
				</section>
			</section>
				<?php include_once('../include/footer.php');?>
		</section>
			<?php include_once('../include/include_js_file.php');?>   
			<script src="<?=ROOT?>js/app/stock_in.js"></script>
			<script>
				//$('#container').addClass('sidebar-closed');
				$(".select2").select2({
						width: '100%'
					});
					$('.default-date-picker').datepicker({
							format: 'dd-mm-yyyy',
							autoclose: true
						});
				$(".form_datetime").datetimepicker({
					format: 'dd-mm-yyyy hh:ii',
					autoclose: true,
					todayBtn: true,
					pickerPosition: "bottom-left"

				});
			</script>
			<?
			
				echo "<script>get_user_out() </script>";
			
			?>
	</body>
</html>
