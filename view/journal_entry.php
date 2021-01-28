<?php 
	session_start();
	include_once("../config/config.php");
	include_once("../config/session.php");
	include_once("../include/common_functions.php");
		$token = md5(rand(1000,9999));
		$_SESSION['token'] = $token;
		$form="Journal Voucher";
		$countryid='101';
		$stateid='1';
		$cityid='1';
		if(strpos($_SERVER[REQUEST_URI], "journal_entry_edit")==false)
		{
			$mode="Add";
			$date=date('d-m-Y');
			$order_date='';
		}
		else
		{
			$mode="Edit";
			$poid=$dbcon->real_escape_string($_REQUEST['id']);
			$query="select * from  tbl_journal where journal_id=$poid";
			$rel=mysqli_fetch_assoc($dbcon->query($query));	
			$date='';
			if($rel['journal_date']!="1970-01-01" && $rel['journal_date']!="0000-00-00")
			{
				$date=date('d-m-Y',strtotime($rel['journal_date']));
			}
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
								<header class="panel-heading">
								  <h3><?=$mode.' '.$form?></h3>
								</header>	
								<div class="">
									<ul class="breadcrumb">
									  <li><a href="<?=ROOT.'dashboard'?>"><i class="fa fa-home"></i> Home</a></li>
									  <li><a href="<?=ROOT.'journal_list'?>"><?=$form?> List</a></li>
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
									<form class="form-horizontal" role="form" id="journal_add" action="javascript:;" method="post" name="journal_add">
										<div class="row">
											<div class="col-md-12 respclear"  style="margin-top:10px;">
												<div class="col-md-6">
													<div class="form-group">
													  <label class="col-md-4 control-label">Journal Entry No </label>
														<div class="col-md-6 col-xs-11">
															<input id="journal_entry_no" name="journal_entry_no" type="text" class="form-control" title="Journal Entry No" value="<?=$rel['journal_no']?>" placeholder="Journal Entry No" >
														</div>
													 </div>	
												</div>	
												<div class="col-md-6">  	
													 <div class="form-group">  	
													  <label class="col-md-4 control-label" >Journal Entry date </label>
													  <div class="col-md-4 col-xs-11">
														<input id="journal_entry_date" name="journal_entry_date" type="text" class="form-control default-date-picker" title="Date" value="<?=$date;?>" placeholder="Journal Entry Date">
														</div>
													 </div>	
												</div>	
											</div>
											<div class="col-md-12">
												<div class="col-md-1"></div>
												<div class="col-md-10">
													<table cellspacing="10" style=" border-spacing:10px;" class="display table  table-striped table12 table-bordered" id="product_list">
														<tr id="field" >
															<th width="10%" class="text-center">
																Type
															</th>
															<th width="15%" class="text-center">Ledger</th>
															<th width="10%" class="text-center">Amount</th>
															<th width="5%" class="text-center"></th>
														</tr>
														<tr id="field" >
															<td data-label="Type">
																<select class="select2" name="entry_type" id="entry_type" title="Select Entry Type">
																	<?=getbalance_type($dbcon)?>
																</select>
															</td>
															<td data-label="Ledger">
																<select class="select2" name="ledger_id" id="ledger_id" title="Select Ledger">
																	<?=get_ledger($dbcon);?>	
																</select>
															</td>
															<td data-label="Amount">
																<input type="number"  title="Enter Amount" min="0" id="amount" name="amount"  class="form-control" />
															</td>
															<td data-label="">
																<input type="button"  name="addrow" id="addrow" onClick="return add_field();"  class="btn btn-primary" value="Add"/>
															</td>
															<input type='hidden' name='edit_id' id='edit_id' value='' />
														</tr>
													</table>
												</div>
											</div>
											<div class="col-md-12">
												<div class="col-md-1"></div>
												<div class="col-md-10" id="sale_productdata"></div>
											</div>
											<div class="col-md-12">
												<center>
													<button type="submit" class="btn btn-success" id="save" name="save">Submit</button>
													<a href="<?=ROOT.'journal_list'?>" type="button" class="btn btn-danger">Cancel</a>
												</center>
											</div>
										</div>
										<input type="hidden" name="journal_id" id="journal_id" value="<?=$rel['journal_id']?>" />
										<input type="hidden" id="mode" name="mode" value="<?=$mode?>" />
										<input type="hidden" name="invoicetype_id" id="invoicetype_id" value="" />
									</form>
								</div>
							</section>
						</div>
					</div>		
				</section>
			</section>
			<?php// include_once('../include/add_cust.php');?>
		</section>
		<?php include_once('../include/include_js_file.php');?>   
		<script src="<?=ROOT?>js/app/journal_entry.js"></script>
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
		<? echo "<script>show_data() </script>";?>
		<?if($mode=="Add")
		{
			echo "<script>get_series_no() </script>";
		}?>
	</body>
</html>
