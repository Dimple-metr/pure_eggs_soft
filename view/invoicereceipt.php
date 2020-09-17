<?php 
	session_start();
	include_once("../config/config.php");
	include_once("../config/session.php");
	include_once("../include/common_functions.php");
	$token = md5(rand(1000,9999));
	$_SESSION['token'] = $token;
	$_SESSION['contents']=''; 
	$form="Invoice";
	$mode="Print";
	$invoiceid=$dbcon->real_escape_string($_REQUEST['id']);
	 $query="select invoice.*,country.country_name,state.state_name,cust.stateid,state.gst_state_code, city.city_name, cust.company_name,cust.m_address as cust_address, type.invoice_type,cust_pincode,cust_mobile,gst_no,usr.user_name from tbl_invoice as invoice 
	left join tbl_ledger as cust on cust.l_id=invoice.cust_id
	left join country_mst as country on country.countryid=cust.countryid
	left join state_mst as state on state.stateid=cust.stateid
	left join city_mst as city on city.cityid=cust.cityid
	left join tbl_invoicetype as type on type.invoicetype_id=invoice.invoicetype_id
	left join users as usr on usr.user_id=invoice.user_id
	where invoice_id=$invoiceid";
	$rel=mysqli_fetch_assoc($dbcon->query($query));
	$cons_gst_no=$rel['gst_no'];
	$cons_pan_no=$rel['pan_no'];
	$cons_state_name=$rel['state_name'];
	$cons_gst_state_code=$rel['gst_state_code'];
	$place_of_supply=$rel['city_name'];
		if(!empty($rel['consignee_id']))//consignee
		{	
			$consignee="select * from tbl_custmer_consignee as cust 
			left join country_mst as country on country.countryid=cust.countryid
			left join state_mst as state on state.stateid=cust.stateid 
			left join city_mst as city on city.cityid=cust.cityid where cust_id=".$rel['consignee_id'];
			$cons_data=mysqli_fetch_assoc($dbcon->query($consignee));	
			$cons_gst_no=$cons_data['gst_no'];
			$cons_pan_no=$cons_data['pan_no'];
			$cons_state_name=$cons_data['state_name'];
			$cons_gst_state_code=$cons_data['gst_state_code'];
			$place_of_supply=$cons_data['city_name'];
		}
		
		$set="select comp.*,state.state_name,state.gst_state_code from tbl_company as comp left join state_mst as state on comp.stateid=state.stateid where company_id=".$rel['company_id'];
		$set_head=mysqli_fetch_assoc($dbcon->query($set));	
		$order_date='';$lr_date='';$dispatch_date='';
		if($rel['order_date']!="1970-01-01" && $rel['order_date']!="0000-00-00")
		{
			$order_date=date('d-m-Y',strtotime($rel['order_date']));
		}
		if($rel['dispatch_date']!="1970-01-01 00:00:00" && $rel['dispatch_date']!="0000-00-00 00:00:00")
		{
			$dispatch_date=date('d-m-Y h:i a',strtotime($rel['dispatch_date']));
		}
		
	/* Check Discount is On or off Start */
		if($set_head['show_disc']=='1'){
			$colspan=5;
			$dynamicwidth=37;
		}else{
			$colspan=6;
			$dynamicwidth=37;
		}
	/* Check Discount is On or off End */
		
?>

<!DOCTYPE html>
<html lang="en">
	<head>
		<?php// include_once('../include/include_print_css_file.php');?>
		<?php include_once('../include/include_css_file1.php');?>
		<style>
			body {color: #000000;}
			.con ul {padding-left:0px;}
			.con ul li { margin-left:22px;list-style: disc !important;}
			/*td, th {padding: 0px 5px !important;}*/
			#print_status, #print_status option { text-overflow: ellipsis;}
			@media(max-width:768px){/*.boderremoveres{border-left:none !important;}.borderleftadd{border-left:1px solid !important
			}*/}
		</style>
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
									  <li><a href="<?=ROOT.'invoice_list'?>">Invoice List</a></li>
									</ul>
								</div>
							</section>
						  <!--breadcrumbs end -->
					    </div>	
					</div>
              <!--state overview start-->
					<div class="row">			
						<div class="col-sm-12 col-md-12 col-lg-8 col-lg-offset-2">
			<!--<div class="col-sm-12">-->
							<section class="panel">
								<div class="panel-body">
									<div class="col-md-12">
										<center>
											<div class="col-sm-5"  style="padding-left:0;">
												<label class="col-md-2 control-label" style="padding-top: 10px;"> Print</label>
												<div class="col-md-10 col-xs-12">
													<form class="form-horizontal" role="form" id="print_form" action="javascript:;" method="post" name="print_form">
														<select class="form-control" name="print_status" id="print_status" <?if($_REQUEST['printstatus']!=''){ echo "readonly";}?>>
															<option value="" >Select Print</option>
															<option value="1" <?if($_REQUEST['printstatus']=='1'){ echo "selected";}?>>ORIGINAL</option>
															<option value="2" <?if($_REQUEST['printstatus']=='2'){ echo "selected";}?>>DUPLICATE</option>
															<option value="3" <?if($_REQUEST['printstatus']=='3'){ echo "selected";}?>>TRIPLICATE</option>
															<option value="4" <?if($_REQUEST['printstatus']=='4'){ echo "selected";}?>>EXTRA</option>
														</select>
													</form>
												</div>
											</div>
											<div class="col-sm-3 resclear">
												<label class=" control-label col-sm-7 " style="
												padding-top: 10px; padding:10px 0 0;">With Logo</label>
												<div class=" resclear col-sm-5">
													<input type="checkbox" class="form-control"  name="logo" id="logo" value="1">
												</div>
											</div>
											<div class="col-sm-4 resclear resspace"  style="text-align:right">
												<button type="submit" class="btn btn-success" onClick="PrintMe('receipt_print');"><i class="fa fa-print"></i> Print</button>
												<a href="<?=ROOT.'invoice_list'?>" type="button" class="btn btn-danger"><i class="fa fa-ban"></i> Cancel</a>
												
												
											</div>
										<!--<div class="col-sm-4 resclear resspace"  style="text-align:center;padding-top:5px;">
											
											<a type="button" class="btn btn-success" href="https://web.whatsapp.com/send?phone=+91<?echo $rel['cust_mobile']?>&text=<?echo $rel['company_name']?>%2C%0aThank you for your purchase.%0aInvoice No:-<? echo $rel['invoice_no']?>%0aDate:-<? echo date('d-m-Y',strtotime($rel['invoice_date']))?>%0aAmount:-<? echo $rel['g_total']?>%0aBest Regards%0a
											<? echo $set_head['company_name']?>" target="_blank"> <i class="fa fa-whatsapp"></i> Whatsapp</a>
 
												
 
										</div>-->
										</center>
									</div>
									<input type="hidden" name="typename" id="typename" value="<?=$rel['invoice_type']?>">
									<?php ob_start(); ?>
									<div class="col-lg-12 " id="receipt_print">	
										<div class="col-md-12 breakout" style=" margin-top:10px;" id="print1">
										<!-- Fixed Logo Table Start -->
										<!--<img src="<?=ROOT.LOGO.$set_head['logo']?>"  style="width:100%"/>-->
<!--border-radius: 10px;border-collapse: separate;border-color: black;margin-top: 23px;    padding: 17px 0 0;border: 1px solid;-->
											<table  class="maintable headermain " style="border-radius: 10px;border-collapse: separate;border-color: black;margin-top: 2px;    padding: 10px 0 0;border: 1px solid;" id="table_head" width="100%">
												
												<tr style="border:none;">
													<td width="30%" style="border:none;">
														<img src="<?=ROOT.LOGO.$set_head['logo']?>"  style="width:100%; height:5%;"/>
													</td>
													<td width="70%" style="border:none;"> 
														<h1 style="margin-right: 10px;margin-top: 10px;" align="right"><?=$set_head['company_name']?></h1>
														<h5 align="left" style="padding:top:8px;margin-right: 10px;"><?=$set_head['logo_content']?></h5>
														<h4 style="font-size:19px; margin-right: 10px;line-height: 135%;" align="right"><?=$set_head['address']?></h3>
														<h4 style="font-size:14px; margin-right: 10px;" align="right"><?if($set_head['website']){?>Email: <?=$set_head['website']?><?}?> 
														<?if($set_head['contact_no']){?>(M) <?=$set_head['contact_no']?><?}?></h4>
														<h4 align="right" style="font-size:14px; margin-right: 10px;"><?if($set_head['company_website']){?>Website: <?=$set_head['company_website']?><?}?></h4>
													</td>
													
												</tr>
											</table>
											<!-- Fixed Logo Table End -->
											<!-- Multipage Table Start -->	
											<table width="100%" class="maintable" style=" font-size:11px" id="invoice_type" >
												<thead id="fiac">
													<tr>
														<th colspan="11" style="padding:0px !important;">
															<table style="font-size:10px;border-collapse: collapse;border-top:none !important;" cellpadding="0" cellspacing="0" width="100%" >
																<tr style="">
																	<td style="border-left:none !important;border-right:none !important;" colspan="2"> </td>
																	<td style="border-left:none !important;border-right:none !important; text-align:center !important;" colspan="3"> 
																		<strong class="typetitle" style="font-size:14px;">
																			<span id=""><?=$rel['invoice_type']?></span>
																		</strong>
																	</td>
																	<td style="border-left:none !important;border-right:none !important; text-align:right !important;"  width="10%"> 
																		<strong style="font-size:9px">
																			<b class="data_title">ORIGINAL FOR RECIPIENT</b>
																		</strong>
																	</td>
																</tr>
																<tr>
																	<td width="12.5%" class="" style="vertical-align:top;border:1px solid; border-right:none !important;white-space:nowrap;"><strong>Invoice No </strong>
																	</td>
																	<td width="12.5%" colspan="" style="vertical-align:top;border-bottom:1px solid; border-right:1px solid;border-top:1px solid">: <strong><?=$rel['invoice_no']?></strong>
																	</td>
																	<td width="12.5%" style="vertical-align:top;border-bottom:1px solid;border-top:1px solid"><strong>Date </strong>
																	</td>
																	<td width="13.40%" style="vertical-align:top;border-bottom:1px solid;border-right:1px solid;border-top:1px solid" colspan="">: <strong><?=date('d-m-Y',strtotime($rel['invoice_date']))?></strong>
																	</td>
																	<!--<td style="vertical-align:top;border-bottom:1px solid;border-top:1px solid;white-space:nowrap;"><strong>Mode of Dispatch</strong>						
																	</td>							
																	<td width="38%" style="vertical-align:top;border-bottom:1px solid;border-top:1px solid;border-right:1px solid;">: <?=$rel['mode_dispatch']?>						
																	</td>-->		
																	<td width="19.15%" style="vertical-align:top;border-bottom:1px solid;border-top:1px solid;white-space:nowrap;"><strong>Vehicle No</strong>						
																	</td>							
																	<td width="30%" style="vertical-align:top;border-bottom:1px solid;border-top:1px solid;border-right:1px solid;">: <?=$rel['vehicle_no']?>						
																	</td>							
																</tr>
																<!--<tr>
																	<td class="" style="vertical-align:top;border-bottom:1px solid;border-left:1px solid;white-space:nowrap;"><strong>Challan No </strong>
																	</td>
																	<td style="vertical-align:top;border-bottom:1px solid;border-right:1px solid" colspan="">: <?=$rel['challan_no']?>
																	</td>
																	<td style="vertical-align:top;border-bottom:1px solid "><strong>Date </strong>
																	</td>
																	<td style="vertical-align:top;border-bottom:1px solid;border-right:1px solid" colspan="">: <?=date('d-m-Y',strtotime($rel['challan_date']))?>
																	</td>
																	
																	<td style="vertical-align:top;border-bottom:1px solid;white-space:nowrap;"><strong>Docket No. </strong>					
																	</td>							
																	<td style="vertical-align:top;border-bottom:1px solid;border-right:1px solid;">: <?=$rel['docket_no']?></td>
																</tr>-->
																<tr>
																	<td class="boderremoveres" style="vertical-align:top;border-bottom:1px solid;border-left:1px solid;"><strong>Po No </strong></td>
																	<td style="vertical-align:top;border-bottom:1px solid;border-right:1px solid" colspan="">: <?=$rel['order_no']?>
																	</td>
																	<td style="vertical-align:top;border-bottom:1px solid "><strong>Po Date </strong>
																	</td>
																	<td style="vertical-align:top;border-bottom:1px solid;border-right:1px solid" colspan="">: <?=$order_date?>
																	</td>
																	<td style="vertical-align:top;border-bottom:1px solid;white-space:nowrap;white-space:nowrap;"><strong>Crates No</strong>					
																	</td>							
																	<td style="vertical-align:top;border-bottom:1px solid;border-right:1px solid;">: <?=$rel['docket_no']?></td>
																	<!--<td style="vertical-align:top;border-bottom:1px solid;white-space:nowrap;"><strong>Place of Supply</strong>					
																	</td>							
																	<td style="vertical-align:top;border-bottom:1px solid;border-right:1px solid;">: <?=$place_of_supply?></td>-->
																</tr>
																<tr>
																	<td class="boderremoveres" style="vertical-align:top;border-bottom:1px solid;border-left:1px solid; "><strong>State</strong></td>
																	<td style="vertical-align:top;border-bottom:1px solid;border-right:1px solid">: <?=$set_head['state_name']?>
																	<td class="boderremoveres" style="vertical-align:top;border-bottom:1px solid "><strong>Code</strong></td>
																	<td style="vertical-align:top;border-bottom:1px solid;border-right:1px solid">: <?=$set_head['gst_state_code']?>
																	
																	</td>
																	
																	
																	<td style="vertical-align:top;border-bottom:1px solid;white-space:nowrap;"><strong>Employee Name</strong></td>							
																	<td style="vertical-align:top;border-bottom:1px solid;border-right:1px solid;">: <?=$rel['user_name']?></td>
																	
																</tr>
																<!--<tr>
																	<td class="boderremoveres" style="vertical-align:top;border-bottom:1px solid;border-left:1px solid;white-space:nowrap;"><strong>Employee Name</strong></td>
																	<td style="vertical-align:top;border-bottom:1px solid;border-right:1px solid">: <?=$rel['user_name']?>
																	<td class="boderremoveres" style="vertical-align:top;border-bottom:1px solid "><strong></strong></td>
																	<td style="vertical-align:top;border-bottom:1px solid;border-right:1px solid">
																	</td>
																	<td style="vertical-align:top;border-bottom:1px solid;white-space:nowrap;"><strong></strong></td>							
																	<td style="vertical-align:top;border-bottom:1px solid;border-right:1px solid;"></td>
																	
																</tr>-->
																<!--<tr>
																	<td class="boderremoveres" style="vertical-align:top;border-bottom:1px solid;border-left:1px solid;white-space:nowrap;"><strong>E-way Bill No.</strong></td>
																	<td style="vertical-align:top;border-bottom:1px solid;border-right:1px solid">: <?=$rel['e_way_bill_no']?>
																	<td class="boderremoveres" style="vertical-align:top;border-bottom:1px solid "><strong>Vehicle No</strong></td>
																	<td style="vertical-align:top;border-bottom:1px solid;border-right:1px solid">: <?=$rel['vehicle_no']?>
																	</td>
																	<td style="vertical-align:top;border-bottom:1px solid"><strong></strong></td>							
																	<td style="vertical-align:top;border-bottom:1px solid;border-right:1px solid;"></td>
																	
																	
																</tr>-->
																<tr id="rawnone">
																	<td colspan="4" width="0%" style="vertical-align:top;border-right:1px solid;border-left:1px solid;">
																		<b>Bill to Party : </b><br/>
																		<strong><?=$rel['company_name']?></strong>
																		<span style="font-weight:normal;">  <br/>
																			<?=$rel['cust_address']?>
																			 <br/>
																			 <?=$rel['city_name']?>, <?=$rel['state_name']?>, <?=$rel['country_name']?>
																			  <? if(!empty($rel['cust_pincode'])){	?>
																				-  <?=$rel['cust_pincode']?>
																				<? } ?>
																		</span>
																			<br>
																			Mobile no : <?=$rel['cust_mobile']?>
																	</td>
																	<? if(empty($rel['consignee_id'])) { ?>
																	<td colspan="2"  style="border-right:1px solid">
																		<b>Shipped to Party : </b><br>
																		<strong><?=$rel['company_name']?></strong>
																		<span style="font-weight:normal;">   
																			<br/>
																			<?=$rel['cust_address']?>
																			 <br/>
																			 <?=$rel['city_name']?>, <?=$rel['state_name']?>, <?=$rel['country_name']?>
																				<? if(!empty($rel['cust_pincode'])){?>
																					-  <?=$rel['cust_pincode']?>
																				<? } ?>
																		</span>
																			<br>
																			Mobile no : <?=$rel['cust_mobile']?>
																	</td>
																	<? } else
																	{?>
																	<td colspan="2"  style="border-right:1px solid">
																		<b>Consignee : </b><br>
																		<strong><?=$cons_data['company_name']?></strong>
																		<span style="font-weight:normal;">   <br/>
																			<?=$cons_data['cust_address']?>
																			 <br/>
																			 <?=$cons_data['city_name']?>, <?=$cons_data['state_name']?>, <?=$cons_data['country_name']?>
																				<? if(!empty($cons_data['cust_pincode'])){?>
																					-  <?=$cons_data['cust_pincode']?>
																				<? } ?>
																		</span>
																			<br>
																			Mobile no : <?=$cons_data['cust_mobile']?>
																	</td>
																<? }?>
																</tr>
																<tr id="rawnone">
																	<td colspan="4" style="border-right:1px solid;border-left:1px solid;"><strong>GSTIN: <?=$rel['gst_no']?> </strong></td>
																	<td colspan="2" style="border-right:1px solid;"><strong>GSTIN: <?=$cons_gst_no?> 
																	</strong></td>
																</tr>
																<tr id="rawnone"> 
																	<td colspan="2" width="25%" style="border-left:1px solid;border-bottom:1px solid;font-weight:normal;">State : <?=$rel['state_name']?></td>
																	<td colspan="2" width="23.5%" style="border-right:1px solid;text-align:left;border-bottom:1px solid;border-right:1px solid;font-weight:normal;">Code : <?=$rel['gst_state_code']?></td>
																	<td style="text-align:left;border-bottom:1px solid;font-weight:normal;">State : <?=$cons_state_name?></td>
																	<td style="text-align:left;border-bottom:1px solid;border-right:1px solid;font-weight:normal;">Code : <?=$cons_gst_state_code?></td>
																</tr>
															</table>
														</th>
													</tr>
													<tr>
														<th width="3%" style="text-align:center;border:1px solid;border-top: none;"><strong>SR.<br/> NO.</strong></th>
														<th width="<?=$dynamicwidth?>%" style="text-align:center !important; border:1px solid;border-top: none;" >
															<strong>Particulars </strong>
														</th>
														<th width="8%" style="text-align:center  !important;border:1px solid;border-top: none;">
															<strong>HSN/SAC <br/>Code</strong>
														</th>
														<th width="7%" style="text-align:center !important;border:1px solid;border-top: none;">
															<strong>QTY.</strong>
														</th>
														<th width="7%" style="text-align:center  !important;border:1px solid;border-top: none;">
															<strong>Rate</strong>
														</th>
														<th width="7%" style="text-align:center  !important;border:1px solid;border-top: none;">
															<strong>MRP</strong>
														</th>
														<? if($set_head['show_disc']=='1'){ ?>
														<!--<th width="6%" style="text-align:center  !important;border:1px solid;border-top: none;">
															<strong>Less:<br/>Disc.</strong>
														</th>-->
														<?}?>
														<!--<th width="9%" style="text-align:center  !important;border:1px solid;border-top: none;">
															<strong>Taxable<br/>Value</strong>
														</th>-->
														<!--<th width="4%" style="text-align:center  !important;border:1px solid;border-top: none;">
															<strong>GST Rate</strong>
														</th>-->
														<!--<th width="6%" style="text-align:center  !important;border:1px solid;border-top: none;">
															<strong>GST Amount</strong>
														</th>-->
														<th width="10%" style="text-align:center  !important;border:1px solid;border-top: none;">
															<strong>Total</strong>
														</th>
													</tr>
												</thead>
												<tbody style="border: none;">
													<? 
													$qry="select trn.*,product.*,unit_name,group_concat(tax.tax_value) as tax_val,group_concat(tax.tax_name) as tax_name FROM `tbl_invoicetrn` as trn left join tbl_product as product on product.product_id=trn.product_id left join unit_mst as per on per.unitid=trn.unit_id 
													left join `formula_mst` as ftax on ftax.formulaid=trn.formulaid left join tbl_tax as tax on find_in_set(tax.tax_id,ftax.tax_id)
													where trancation_status=0 and invoice_id=".$rel['invoice_id']." group by trancation_id order by product_type,trancation_id";
													$result=$dbcon->query($qry);		
													$i=1;$total=0;$discount=0;$totalqty=0;$charges_qty=0;
													$cnt=mysqli_num_rows($result);
													while($row=mysqli_fetch_assoc($result))
													{
														$tax_arr=explode(",",$row['tax_val']);
														//tax summary calculation start
														if(!empty($row['tax_val']))
														{
															$tax_num=explode(",",$row['tax_val']);
															$tax_name=explode(",",$row['tax_name']);
															
															$total_net_rate=($row['product_qty']*$row['product_rate'])-$row['discount'];
															for($j=0;$j<count($tax_num);$j++)
															{
																if(!in_array($tax_name[$j],$tax['per']))
																{
																	$tax['per'][]=$tax_name[$j];
																}
																$tax['per_total'][$tax_name[$j]]+=$total_net_rate*$tax_num[$j]/100;
															}
														}?>
														<tr style="height:35px">
															<td class="borderleftadd" style="text-align:center !important; vertical-align:top;border-right:1px solid;border-left:1px solid;">
																<? if($row['product_type']!='3'){ echo $i;}?>
															</td>
															<td style="border-bottom-color:#FFFFFF; border-right:1px solid;vertical-align:top;<? if($row['product_type']=='3'){ echo 'text-align:right !important;padding-top:5px;vertical-align:top;';}?>" >
																<strong><?=stripcslashes($row['product_name'])?>-<?=$row['item_code']?></strong>
																	<br/><?=nl2br(stripcslashes($row['description']));?>
															</td>
															<td style="border-bottom-color:#FFFFFF; border-right:1px solid;vertical-align:top;text-align:center  !important" >
																<?=stripcslashes($row['product_hsn_code'])?>
															</td>
															<td style="text-align:center  !important; vertical-align:top;border-bottom-color:#FFFFFF; border-right:1px solid;white-space:nowrap;" >
																<? if($row['product_type']!='3'){ ?>
																	<?=$row['product_qty'].' '.$row['unit_name']?>
																<? }else{
																	$charges_qty+=$row['product_qty'];
																} ?>	
															</td>
															<td style="text-align:right  !important;vertical-align:top;border-bottom-color:#FFFFFF; border-right:1px solid;" >
																<?=number_format($row['product_rate'],2,".","")?>
															</td>
															<td style="text-align:right  !important;vertical-align:top;border-bottom-color:#FFFFFF; border-right:1px solid;" >
																<?=number_format($row['mrp'],2,".","")?>
															</td>
															<? if($set_head['show_disc']=='1'){?>
															<!--<td style="text-align:right  !important; vertical-align:top;border-bottom-color:#FFFFFF;border-right:1px solid;">
																<?=number_format($row['discount_per'],2,".","").'%'?>
															</td>-->
															<?}?>
															<!--<td style="text-align:right  !important; vertical-align:top;border-bottom-color:#FFFFFF;border-right:1px solid;">
																<?=number_format($row['product_amount'],2,".","")?>
															</td>-->
															<!--<td style="text-align:right  !important; vertical-align:top;border-bottom-color:#FFFFFF;border-right:1px solid;">
																<?=$tax_arr[0]+$tax_arr[1]?>%
															</td>-->
															<!--<td style="text-align:right;vertical-align:top;border-bottom-color:#FFFFFF;border-right:1px solid;">
																<?=number_format($row['tax_amount1']+$row['tax_amount2'],2,".","")?>
															</td>-->
															<td style="text-align:right  !important; vertical-align:top;border-bottom-color:#FFFFFF;border-right:1px solid;">
																<?=number_format($row['total'],2,".",",")?>
															</td>
														</tr>
		
														<? 
															$i++; 
																$totalqty=$totalqty+$row['product_qty']-$charges_qty;
																$totalsqr=$totalsqr+$row['sqr_ft']-$charges_qty1;
																$total_product_amount+=($row['product_qty']*$row['product_rate']);
																$totaltaxable+=$row['product_amount'];
																$totaltax1+=$row['tax_amount1'];
																$totaltax2+=$row['tax_amount2'];
																$total+=$row['total'];
														}
														$pr=9-$cnt;
			
														for($j=0; $j<$pr; $j++)
														{?>	
															<tr style="height:35px">
																<td class="borderleftadd" style="text-align:center !important; vertical-align:top;border-right:1px solid;border-left:1px solid;">
																
															</td>
															<td style="border-bottom-color:#FFFFFF; border-right:1px solid;vertical-align:top;<? if($row['product_type']=='3'){ echo 'text-align:right;padding-top:5px;vertical-align:top;';}?>" >
																
															</td>
																<? if($set_head['show_disc']=='1'){?>
																<!--<td style="border-right:1px solid;"></td>-->
																<?}?>
																<!--<td style="border-right:1px solid;"></td>-->
																<!--<td style="border-right:1px solid;"></td>
																<td style="border-right:1px solid;"></td>
																<td style="border-right:1px solid;"></td>-->
																<td style="border-right:1px solid;"></td>
																<td style="border-right:1px solid;"></td>
																<td style="border-right:1px solid;"></td>
																<td style="border-right:1px solid;"></td>
																<td style="border-right:1px solid;"></td>
															</tr>
													  <?}?>
														<tr style="height:20px">
															<td class="borderleftadd" style="border-top:1px solid;border-right:1px solid;border-left:1px solid; text-align:right  !important;" colspan="2">
																<strong>Total</strong>
															</td>
															<td style="text-align:center  !important;border-top:1px solid;border-right:1px solid;">
															</td>
															<td style="text-align:center  !important;border-top:1px solid;border-right:1px solid;">
																<strong><?=number_format($totalqty,2,".","")?></strong>
															</td>
															<td style="text-align:center  !important;border-top:1px solid;border-right:1px solid;">
															<td style="text-align:center  !important;border-top:1px solid;border-right:1px solid;">
																<strong></strong>
															</td>
															<? if($set_head['show_disc']=='1'){?>
															<!--<td style="border-top:1px solid;border-right:1px solid;"></td>-->
															<?}?>
															<!--<td style="border-top:1px solid;border-right:1px solid;"></td>
															<td style="border-top:1px solid;border-right:1px solid;text-align:right  !important;">
																<strong><?=number_format($totaltaxable,2,".","")?></strong>
															</td>
															<td style="border-top:1px solid;border-right:1px solid;text-align:right  !important;">
															</td>
															<td style="border-top:1px solid;border-right:1px solid;text-align:right  !important;">
																<strong><?=number_format($totaltax1+$totaltax2,2,".","")?></strong>
															</td>-->
															<td style="border-top:1px solid;border-right:1px solid;text-align:right  !important;">
																<strong><?=number_format($total,2,".",",")?></strong>
															</td>
														</tr>		
														<tr>
															<td class="borderleftadd" colspan="11" style="padding: 0px !important;border:1px solid">
															
																<table class="footer-table" width="100%">
																	<tr height="20px">
																		<td width="50.53%" style="border-right:1px solid;font-size:10px;" colspan="<?=$colspan?>">
																		<? if(!empty($set_head['bank_name'])){?>
																				<strong>Bank Name:</strong> <?=$set_head['bank_name']?>, 
																				<? } ?>
																			<? if(!empty($set_head['ac_no'])){?>
																				<strong>A/c No:</strong> <?=$set_head['ac_no']?>	 
																				<? } ?>
																		</td>
																		<td colspan="5" rowspan="3" width="49.47%" style="vertical-align: baseline;font-size:15px;text-align:center  !important">
																			<?=$set_head['invoice_tax_content']?>
																		</td>
																		<!--<td colspan="2" style="text-align:right  !important;font-size:10px;" width="10%"><?=number_format($totaltaxable,2,".","")?></td>-->	
																	</tr>
																	<tr  height="20px">
																		<td  style="border-right:1px solid;border-top:1px solid; font-size:10px;" colspan="<?=$colspan?>">
																			<? if(!empty($set_head['ifcs'])){ ?>
																					<strong>IFSC:</strong><?=$set_head['ifcs']?>,
																				<? } ?>	
																				<? if(!empty($set_head['branch_name'])){ ?>
																					<strong>Branch :</strong> <?=$set_head['branch_name']?><? } ?>
																		</td>
																	
																		<!--<td colspan="3" style="border-top:1px solid;border-right:1px solid;font-size:10px;text-align:left  !important" >
																			<?=(($rel['stateid']==$set_head['stateid'])?'Add :  CGST':'Add :  IGST');?>
																		</td>-->
																		<!--<td colspan="2" style="text-align:right  !important; border-top:1px solid;font-size:10px;border-right:1px solid; "><?=number_format($totaltax1,2,".","")?></td>-->
																		
																	</tr>
																
																	<?  //if($rel['stateid']==$set_head['stateid']) 
																		if($tax_name[1]) 
																		{ ?>
																	<tr height="20px">
																		<td  style="border-right:1px solid;border-top:1px solid; font-size:10px;" colspan="<?=$colspan?>">
																		</td>
																		<td colspan="3" style="border-top:1px solid;border-right:1px solid;font-size:10px;text-align:left  !important">Add : 
																		<? 
																			
																			$strt=$tax_name[1];
																			$position = strpos($strt, "TCS", 0);
																				if ($position == true){ 
																				echo $tax_name[1];
																				} else{
																				echo 'SGST';	
																				}
																		?></td>
																		<td colspan="2" style="text-align:right  !important; border-top:1px solid;font-size:10px;border-right:1px solid; "><?=number_format($totaltax2,2,".","")?></td>
																	</tr>
																<? } $totaltax=$totaltax1+$totaltax2;?>
																<?php 
																	$total=($total)+$rel['packing']; 
																	if($rel['packing']!="0.00")
																	{ ?>
																		<tr height="20px">
																			<td class="borderleftadd" style="border-right:1px solid;border-top:1px solid;font-size:10px;" colspan="<?=$colspan?>">
																				
																			</td>
																			<td colspan="3" style="border-top:1px solid;border-right:1px solid;font-size:10px;text-align:left  !important">Packing :</td>
																			<td colspan="2" style="text-align:right  !important; border-top:1px solid;font-size:10px;border-right:1px solid; "><?=number_format($rel['packing'],2,".","")?></td>
																		</tr>
																<?  } //$r=round($total)-$total; ?>
																	<tr height="20px">
																		<td style="border-right:1px solid;border-top:1px solid;font-size:12px;" colspan="<?=$colspan?>">
																			<strong>COMPANY GST No. : <?=$set_head['vatno']?> </strong><br>
																		</td>
																		<!--<td colspan="3" style="border-top:1px solid;border-right:1px solid;font-size:10px;text-align:left  !important">Round off :</td>-->
																		<!--<td colspan="2" style="text-align:right  !important; border-top:1px solid;font-size:10px;border-right:1px solid; "><?=number_format($r,2,".",",")?></td>-->
																	</tr>
																	<tr  height="20px">
																		<td width="50.46%" style="border-right:1px solid;border-top:1px solid;font-size:10px;" colspan="<?=$colspan?>">
																			<strong>Rupees:</strong>
																			<?=ucwords(convert_number_to_words($total))?>
																		</td>
																		<td width="26.50%" colspan="3" style="border-top:1px solid;border-right:1px solid;font-size:10px;text-align:right  !important">
																			<strong>Grand Total</strong> 
																		</td>
																		<td width="22%" colspan="2" style="text-align:right  !important; border-top:1px solid;font-size:10px;border-right:1px solid; ">
																			<strong><?=number_format($total,2,".",",")?></strong>
																		</td>
																	</tr>
																<tr>
																	<td colspan="<?=$colspan?>" style="vertical-align:top;border:1px solid;
																	border-right:none;border-left:none;border-bottom:none;font-size:10px;text-align:left  !important"  class="con">
																		
																	<? if(!empty($set_head['conditions'])){ ?>
																			<strong>Remark:</strong> <?=$rel['remark']?>
																			<br/>
																	<? } ?>
																	<? if(!empty($set_head['conditions'])){ ?>
																			<strong>Terms and Conditions:</strong><br> <?=$set_head['conditions']?>
																		<? } ?>	<br/><br/>
																	<!--<span style="vertical-align:bottom;">E & O.E.</span>-->
												
																	</td>
																	<td colspan="5" style=" border-left:none;vertical-align:top;border-top:1px solid black;">
																	<center>
																	For, <strong> <span style="font-size:10px;text-decoration:bold;">
																	<?=$set_head['company_name']?></span></strong>
																	<br/>
																	<img src="<?=ROOT.LOGO.$set_head['f_logo']?>"  style="width:50%;height:90px;"/>
																	<br/>
																	</center>
																	 
																	 <center style="vertical-align:bottom;">Authorised Signatory</center>

																	</td>
																	
																</tr>
															</table>
															</td>
														</tr>		
												</tbody>
				<!--<table width="100%" border="0" style="margin-top: 5px;" id="table_foot">
					<tr>
						<td style="border:none;padding:0px 0px !important;width:100%;"> 
							<img src="<?=ROOT.LOGO.$set_head['f_logo']?>"  style="width:100%;height:100px;"/>
						</td>
					</tr>
				</table>-->
				
	</table>
	<!-- Multipage Table End -->		
				
		<!--<center><span style="float:left;">E.& O. E.</span>This is a Computer Generated Invoice</center>-->
							</div>
								<div id="print2" style="margin-top:0in;"></div>
								<div id="print3" style="margin-top:0in;"></div>
						
</div>
	<?php  
			$contents = ob_get_contents();
			$_SESSION['contents']=$contents;
			$_SESSION['file_name']='invoice-#';
			$_SESSION['invoice_no']=$rel['invoice_no'];
			$_SESSION['page_size']='A4';
			echo "<script> function make_pdf()
			{ window.open('".ROOT."export/print','_blank');
			}</script>";  
	?>
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
    <script src="<?=ROOT?>js/app/invoice.js"></script>
    <!--<script src="js/count.js"></script>-->

<script type="text/javascript"> 
function print_receipt()
{
	var originalContents = document.body.innerHTML;
	//var duplicate = $("#invoiceprint").clone().prepend("<hr style='border-color:#000; border-style:solid; margin:10px 0' />").appendTo("#invoiceprint");
	var printContents = document.getElementById('receipt_print').innerHTML;     
    document.body.innerHTML = printContents;
    window.print();
    document.body.innerHTML = originalContents;
}

function PrintMe(DivID) {

if($('#print_status').val()=='')
{
alert('Select PrintType');
}
else
{


if($('#print_status').val()<=3)
{	
for(var i=1;i<$('#print_status').val();i++)
{	
	if($("#invoice").val()==2)
	{
		$("#print"+i+" .data_title").html('Performance');
		$("#type").html("Performance Invoice");
	}
	if($("#invoice").val()==1)
	{
		$("#print"+i+" .data_title").html('ORIGINAL FOR RECIPIENT');
		$("#type").html($("#typename").val());
	}
	if(i<$('#print_status').val())
	{
		$("#print"+i).after('<div class="page"></div>');
	}
	$("#print"+(i+1)).html($("#print1").clone());
	if((i+1)==2)
	{
		$("#print"+(i+1)+" .data_title").html('DUPLICATE FOR SUPPLIER');
	}
	if((i+1)==3)
	{
		$("#print"+(i+1)+" .data_title").html('TRIPLICATE FOR TRANSPORTER');
	}
	
}
}
else
{
	$("#print1 .data_title").html('EXTRA');
}
  //var duplicate = $("#receipt_data").clone().appendTo("#receipt_duplicate");
  var disp_setting="toolbar=yes,location=no,";
  disp_setting+="directories=yes,menubar=yes,";
  disp_setting+="scrollbars=yes,width=800, height=600, left=100, top=25";
  var content_vlue = document.getElementById(DivID).innerHTML;
  var docprint=window.open("","",disp_setting);
  docprint.document.open();
  docprint.document.write('<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"');
  docprint.document.write('"http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">');
  docprint.document.write('<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en">');
  docprint.document.write('<head><title><? echo TITLE;?></title>');
 // docprint.document.write('<link rel="stylesheet" href="<?php echo ROOT;?>css/style.css" media="all"/>');
  docprint.document.write('<link rel="stylesheet" href="<?php echo ROOT;?>css/bootstrap.min.css" media="all"/>');
  docprint.document.write('<style type="text/css">');
	if ($('input[name=logo]:Checked').val() == "1") {
	    $('#table_head').show();
		$('#table_foot').show();
		docprint.document.write('@media print{ @page { size:A4; margin: 0.2in <?=$set_head['letter_head_right_margin']?>in 0.2in <?=$set_head['letter_head_left_margin']?>in; } } ');
	}
	else
	{
		docprint.document.write('@media print{ @page { size:A4; margin: <?=$set_head['letter_head_top_margin']?>in <?=$set_head['letter_head_right_margin']?>in <?=$set_head['letter_head_bottom_margin']?>in <?=$set_head['letter_head_left_margin']?>in; } }  #table_head, #table_foot ,#texttype { display:none }');
		//$('#invoice_type').css('margin-top','1.7in');	
	}
 
  docprint.document.write('body { font-family:Tahoma;color:#000;font-size:10px;}.breakout table td,.breakout table th {padding: 2px !important;text-align: inherit !important;}');
  docprint.document.write('.breakout table td,.breakout table th {padding: 2px !important;text-align: inherit !important;}a{color:#000;text-decoration:none;} h1 {font-size:25px; line-height:5px;} b { font-weight:normal; } div.page { page-break-after: always; page-break-inside: avoid; } tr { page-break-inside: avoid } .maintable tbody tr { border-bottom:0.5px #ccc solid; }');
  docprint.document.write('.breakout table td,.breakout table th {padding: 2px !important;text-align: inherit !important;} .maintable table { page-break-inside:auto } .maintable tr{ page-break-inside:avoid; page-break-after:auto } .maintable thead { display:table-header-group }  .maintable tfoot tr{ /*display:table-footer-group;*/ page-break-inside:avoid; page-break-before:always; } footer-table{ page-break-inside:avoid; page-break-before:always;  } #table_foot{position:fixed;bottom:0} #rawnone{border:none;}</style>');
  docprint.document.write('</head><body onLoad="self.print()">');
  docprint.document.write(content_vlue);
  docprint.document.write('</body></html>');
  docprint.document.close();
  docprint.focus();
	$('#table_head').show();
	//$('#invoice_type').css('margin-top','0px');

  }
  location.reload();
}
</script>


  </body>
</html>
