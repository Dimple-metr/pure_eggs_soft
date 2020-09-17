<?php 

	session_start();
	include_once("../config/config.php");
	include_once("../config/session.php");
	
	include_once("../include/common_functions.php");
	$token = md5(rand(1000,9999));
	$_SESSION['token'] = $token;
	$form="Stock Out";
	$countryid='101';
	$stateid='1';
	$cityid='1';
	if(strpos($_SERVER[REQUEST_URI], "stock_out_edit")==false)
	{
		$mode="Add";
		$date=date('d-m-Y');
		$stock_out_date=date('d-m-Y');
		$stock_out_no=series_no($dbcon,"4");
	}
	else
	{
		$mode="Edit";
		$poid=$dbcon->real_escape_string($_REQUEST['id']);
		$query="select * from  tbl_stock_out where stock_out_id=$poid";
		$rel=mysqli_fetch_assoc($dbcon->query($query));	
		$stock_out_date='';
		if($rel['stock_out_date']!="1970-01-01" && $rel['stock_out_date']!="0000-00-00")
		{
			$stock_out_date=date('d-m-Y',strtotime($rel['stock_out_date']));
		}
		$stock_out_no=$rel['stock_out_no'];
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
										<li><a href="<?=ROOT.'stock_out_list'?>"><?=$form?> List</a></li>
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
													  <label class="col-md-4 control-label">Stock Out No </label>
													  <div class="col-md-6 col-xs-11">
														<input id="stock_out_no" name="stock_out_no" type="text" class="form-control" title="Stock Out No" value="<?=$stock_out_no?>" placeholder="Stock Out No" >
														</div>
													 </div>	
												</div>	
												<div class="col-md-6">  	
													 <div class="form-group">  	
													  <label class="col-md-4 control-label" >Stock Out Date </label>
													  <div class="col-md-5 col-xs-11">
														<input id="stock_out_date" name="stock_out_date" type="text" class="form-control default-date-picker" title="Stock Out Date" value="<? if($mode=='Add'){ echo $stock_out_date;}else if($mode=='Edit'){ echo date('d-m-Y',strtotime($stock_out_date));}?>" placeholder="Stock Out Date">
														</div>
													 </div>	
												</div>	
											</div>
											<div class="col-md-12" style="margin-bottom:10px;">
												<label class="col-md-2 control-label" style="">Select Employee </label>
												<div class="col-md-3 col-xs-10 resclear" >
													<select class="select2" name="employee_id" id="employee_id" required title="Select Employee">
														<!--<option value="">Choose Employee</option>-->
														<?//=getcust($dbcon,$rel['employee_id']);
														?>	
														<?=get_user($dbcon,$rel['employee_id'])?>
													</select>
												</div>
											</div>											
											<div class="col-md-12" style="clear: both;margin-top: 40px;">
												<div class="form-group">
													<div class="col-md-2"></div>
													<div class="col-md-8 col-xs-11">
														<table cellspacing="10" style=" border-spacing:10px;" class="display table  table-striped table12 table-bordered" id="product_list">
															<tr id="field" >
																<th width="20%" class="text-center">
																	<div class="col-md-10">Product Detail</div>
																	<div class="col-md-1">
																		<input type="button" name="addproduct" id="addproduct1" data-toggle="modal" title="Add Product" data-target="#bs-example-modal-addproduct" class="btn btn-xs btn-primary" value="+"/>
																	</div>
																</th>
																<th width="6%" class="text-center">Quantity</th>
																<th width="6%" class="text-center" style="display:none;">Per</th>
																<th width="5%" class="text-center"></th>
															</tr>
															<input type="hidden" value="1" name="fieldcnt" id="fieldcnt"/>
															<tr id="field1">
																<td data-label="Product Detail" style="vertical-align:top;">
																	<select class="select2"  title="Select product" name="product_id" id="product_id" >
																		<?=getproduct($dbcon,0,'0,1,2')?>
																	</select>
																	<br><br>
																	<textarea id="product_des" name="product_des" class="form-control" ></textarea>
																</td>	
																<td data-label="Qty" style="vertical-align:top;">
																	<input type="number"  title="Enter Qty" min="0" id="product_qty" name="product_qty"  class="form-control" />
																</td>
																<td data-label="Per" style="vertical-align:top;display:none;">
																	<select class="select2"  name="unitid" id="unitid"  title="Select Unit">
																		<?=getunit($dbcon,0);?>
																	</select>
																</td>
																<td data-label=""  style="vertical-align:top;"> 
																	<input type="button"  name="addrow" id="addrow" onClick="return add_field();"  class="btn btn-primary" value="Add"/>
																</td>
																<input type='hidden' name='edit_id' id='edit_id' value='' />
															</tr>
														</table>
													</div>
												</div>
												<div class="col-md-12" style="clear: both;margin-top: 40px;">
													<div id="sale_productdata"> </div>
												</div>
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
												<a href="<?=ROOT.'stock_out_list'?>" type="button" class="btn btn-danger">Cancel</a>
											</div>	
											<div class="col-md-3"></div>					
										</div>
										<input type='hidden' name='mode' id='mode' value='<?=$mode?>' />
										<input type='hidden' name='eid' id='eid' value='<?=$rel['stock_out_id']?>' />
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
			<script src="<?=ROOT?>js/app/stock_out.js"></script>
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
				echo "<script>show_data() </script>";
			?>
	</body>
</html>
