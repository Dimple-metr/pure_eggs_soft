<?php 

	session_start();
	include_once("../config/config.php");
	include_once("../config/session.php");
	
	include_once("../include/common_functions.php");
	$token = md5(rand(1000,9999));
	$_SESSION['token'] = $token;
	$form="Stock Transfer";
	$countryid='101';
	$stateid='1';
	$cityid='1';
	if(strpos($_SERVER[REQUEST_URI], "stock_transfer_edit")==false)
	{
		$mode="Add";
		$date=date('d-m-Y');
		$stock_transfer_date=date('d-m-Y');
		$stock_transfer_no=series_no($dbcon,"5");
	}
	else
	{
		$mode="Edit";
		$poid=$dbcon->real_escape_string($_REQUEST['id']);
		 $query="select * from  tbl_stock_in where stock_in_id=$poid";
		$rel=mysqli_fetch_assoc($dbcon->query($query));	
		$stock_transfer_date='';
		if($rel['stock_transfer_date']!="1970-01-01" && $rel['stock_transfer_date']!="0000-00-00")
		{
			$stock_transfer_date=date('d-m-Y',strtotime($rel['stock_transfer_date']));
		}
		$stock_transfer_no=$rel['stock_transfer_no'];
	}
	$set="select * from tbl_company where company_id=".$_SESSION['company_id'];
		$set_head=mysqli_fetch_assoc($dbcon->query($set));	
		
?>

<!DOCTYPE html>
<html lang="en">
	<head>
		<?php include_once('../include/include_css_file.php');?>
		
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
										<li><a href="<?=ROOT.'stock_transfer_list'?>"><?=$form?> List</a></li>
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
													  <label class="col-md-4 control-label">Stock Transfer No </label>
													  <div class="col-md-6 col-xs-11">
														<input id="stock_transfer_no" name="stock_transfer_no" type="text" class="form-control" title="Stock Transfer No" value="<?=$stock_transfer_no?>" placeholder="Stock Transfer No" >
														</div>
													 </div>	
												</div>	
												<div class="col-md-6">  	
													 <div class="form-group">  	
													  <label class="col-md-4 control-label" >Stock Tarnsfer Date </label>
													  <div class="col-md-5 col-xs-11">
														<input id="stock_transfer_date" name="stock_transfer_date" type="text" class="form-control default-date-picker" title="Stock Transfer Date" onchange="get_user_out();" value="<? if($mode=='Add'){ echo $stock_transfer_date;}else if($mode=='Edit'){ echo date('d-m-Y',strtotime($stock_transfer_date));}?>" placeholder="Stock Trasnfer Date">
														</div>
													 </div>	
												</div>	
											</div>
											<div class="col-md-12" style="margin-bottom:10px;">
												<label class="col-md-2 control-label" style="">Select Employee </label>
												<div class="col-md-3 col-xs-10 resclear" >
													<select class="select2" name="employee_id" id="employee_id" required title="Select Employee" onchange="get_user_out();">
														<?=get_user($dbcon,$rel['employee_id'])?>
													</select>
												</div>
											</div>											
											<div class="col-md-12" style="clear: both;margin-top: 40px;">
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
												<a href="<?=ROOT.'stock_transfer_list'?>" type="button" class="btn btn-danger">Cancel</a>
											</div>	
											<div class="col-md-3"></div>					
										</div>
										<input type='hidden' name='mode' id='mode' value='<?=$mode?>' />
										<input type='hidden' name='eid' id='eid' value='<?=$rel['stock_transfer_id']?>' />
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
			<script src="<?=ROOT?>js/app/stock_transfer.js"></script>
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
			if($mode=="Add"){
				echo "<script>get_user_out() </script>";
				
			}
			?>
	</body>
</html>
