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
	$_SESSION['invoice_no']=$rel['invoice_no'];
	
	if($rel['order_date']!="1970-01-01" && $rel['order_date']!="0000-00-00")
		{
			$order_date=date('d-m-Y',strtotime($rel['order_date']));
		}
	
	//$set="select comp.* from users as comp where user_id=".$rel['user_id'];
		//$set_head=mysqli_fetch_assoc($dbcon->query($set));	
		
	$set="select comp.*,state.state_name,state.gst_state_code from tbl_company as comp left join state_mst as state on comp.stateid=state.stateid where company_id=".$rel['company_id'];
		$set_head=mysqli_fetch_assoc($dbcon->query($set));	
		

?>

<!DOCTYPE html>
<html lang="en">
<head>
<?php include_once('../include/include_css_file.php');?>
<style>
 
</style>

</head>
<body >
  <section id="container" >
      <?php //include_once('../include/include_top_menu.php');?>
      <!--sidebar start-->
      <?php //include_once('../include/left_menu.php');?>
      <!--sidebar end-->
      <!--main content start-->
           <section id="main-content1">
			<div class="col-md-12" style=" margin-top:10px;width:2in;" id="receipt_print">
							
							<table width="100%" border="0" style="font-size:35px;line-height:35px;" id="table_head">
							<tr>
								
								<td   style="border-left:none;border-top:none;border-bottom:none;padding-left:0px;text-align:center;line-height:35px;"> 
								        ***************************************
								</td>
							</tr>
							<tr>
								
								<td   style="border-left:none;border-top:none;border-bottom:none;padding-left:0px;text-align:center;line-height:35px;"> 
									<span style="font-size:35px;font-weight: 900;"><?=$set_head['company_name']?></span>
								</td>
							</tr>
							<tr>
								<td   style="border-left:none;border-top:none;border-bottom:none;padding-left:0px;text-align:center;line-height:35px;"> 
									
									<span style="font-size:35px;line-height: 30px;">
									<?=$set_head['address']?></span>
									
								</td>
							</tr>
							<tr>
								<td   style="border-left:none;border-top:none;border-bottom:none;padding-left:0px;text-align:center;line-height:35px;"> 
									<strong>COMPANY GST No.:<?=$set_head['vatno']?></strong></span>
								</td>
							</tr>
							</table>
							
						<!--<table width="2in" style="font-size:25px;border-collapse:separate;" cellpadding="0"  cellspacing="0" id="invoice_type" >-->
						<table width="100%" border="0" style="font-size:35px;font-weight: 500;" id="invoice_type">
							
							<tr>
								<td colspan="5" style="text-align:center;padding:5px 0px;"> 
								<strong class="typetitle" style="font-size:30px;">
									<span id=""><?=$rel['invoice_type']?></span>
								</strong>
								</td>
							</tr>
							<tr> 
								<td colspan="2" style="vertical-align:top;border-top:1px dashed black;padding-top:5px;">
									<strong>Invoice No : <?=$rel['invoice_no']?></strong>
								</td>
								<td colspan="3" style="vertical-align:top;border-top:1px dashed black;text-align:right;padding-top:5px;">
									<strong>Date. : <?=date('d-m-Y',strtotime($rel['invoice_date'])) ?></strong>
								</td>
							</tr>
							<tr> 
								<td colspan="2" style="vertical-align:top;">
									PO No : <?=$rel['order_no']?>
								</td>
								<td colspan="3" style="vertical-align:top;text-align:right;">
									PO Date. : <?=$order_date ?>
								</td>
							</tr>
							<tr> 
								<td colspan="2" style="vertical-align:top;">
									Vehicle No : <?=$rel['vehicle_no'] ?>
                                                                        <!--Emp. Name : <?//=$rel['user_name']?>-->
								</td>
								<td colspan="3" style="vertical-align:top;text-align:right;">
									<!--Vehicle No : <?//=$rel['vehicle_no'] ?>-->
								</td>
							</tr>
							<tr> 
								<td colspan="2" style="vertical-align:top;">
									State : <?=$set_head['state_name']?>
								</td>
								<td colspan="3" style="vertical-align:top;text-align:right;">
									State Code : <?=$set_head['gst_state_code'] ?>
								</td>
							</tr>
							<tr> 
								<td colspan="5" style="vertical-align:top;border-bottom:1px dashed black;border-top:1px dashed black;padding-bottom:5px;">
									<strong>Bill to Party : </strong> <?//=$rel['company_name']?>
									<?=$rel['company_name']?>
									<!--<span style="font-weight:normal;">  <br/>
										<?if(!empty($rel['cust_address'])){ ?>
											<?//=$rel['cust_address']?>
										 <br/>
									<?	} ?>
									 <?=$rel['city_name']?>, <?=$rel['state_name']?>, <?=$rel['country_name']?>
										  <? if(!empty($rel['cust_pincode'])){	?>
											-  <?=$rel['cust_pincode']?>
											<? } ?>
									</span>-->
									<?if(!empty($rel['cust_mobile'])){ ?>
										<br/>
											Mobile No : <?=$rel['cust_mobile']?>
										 
									<?	} ?>
									<?if(!empty($rel['gst_no'])){ ?>
										<br/>
											GSTN No : <?=$rel['gst_no']?>
										 
									<?	} ?>
								</td>
								<!--<td colspan="2" style="vertical-align:top;border-bottom:1px dashed black;text-align:right;padding-top:5px;">
									Exec.: <?=strtoupper($rel['user_name'])?>
								</td>-->
							</tr>
							<tr> 
								<td style="vertical-align:top;text-align:center;font-weight:900;;border-bottom:1px dashed black;padding:5px 0px;">
									PARTICULARS
								</td>
								<td style="vertical-align:top;text-align:center;font-weight:900;;border-bottom:1px dashed black;padding:5px 0px;">
									QTY
								</td>
								<td style="vertical-align:top;text-align:center;font-weight:900;;border-bottom:1px dashed black;padding:5px 0px;">
									RATE
								</td>
								<td style="vertical-align:top;text-align:center;font-weight:900;;border-bottom:1px dashed black;padding:5px 0px;">
									MRP
								</td>
								<td style="vertical-align:top;text-align:center;font-weight:900;;border-bottom:1px dashed black;padding:5px 0px;">
									TOTAL
								</td>
							</tr>
							<? 
								$qry="select trn.*,product.*,unit_name FROM `tbl_invoicetrn` as trn left join tbl_product as product on product.product_id=trn.product_id left join unit_mst as per on per.unitid=trn.unit_id  where trancation_status=0 and invoice_id=".$rel['invoice_id']." group by trancation_id";
								$result=$dbcon->query($qry);		
								$i=1;$total=0;$discount=0;$totalqty=0;
								$cnt=mysqli_num_rows($result);
								while($row=mysqli_fetch_assoc($result))
								{
									$tax_num=explode(",",$row['tax_num']);
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
								?>
									<tr> 
										<td style="vertical-align:top;text-align:left;padding-top:5px;">
											<?=$row['product_name']?>
										</td>
										<td style="vertical-align:top;text-align:center;padding-top:5px;">
											<?=$row['product_qty']?>
										</td>
										<td style="vertical-align:top;text-align:center;padding-top:5px;">
											<?=$row['product_rate']?>
										</td>
										<td style="vertical-align:top;text-align:center;padding-top:5px;">
											<?=$row['mrp']?>
										</td>
										<td style="vertical-align:top;text-align:right;padding-top:5px;">
											<?=$row['product_amount']?>
										</td>
									</tr>
									<tr> 
										<td style="vertical-align:top;text-align:left;border-bottom:1px dashed black;padding-bottom:5px;" colspan="2">
											<!--Code : <?=$row['product_hsn_code']?> -->
											<?=$row['item_code']?>
											<? if(!empty($row['description'])){ ?>
												<br/>
											<?=nl2br(stripcslashes($row['description']));?>
											<? } ?>
										</td>
										<td style="vertical-align:top;text-align:left;border-bottom:1px dashed black;padding-bottom:5px;" colspan="3">
											<!--GST Rate : <?=$row['tax_val'].'%'?>-->
										</td>
									</tr>
								
								<? 
									$totalqty+=$row['product_qty'];
									$totaltaxable+=$row['product_amount'];
									$totaltax1+=$row['tax_amount1'];
									$totaltax2+=$row['tax_amount2'];
									$total+=$row['product_amount'];
								}
								?>
							<tr> 
								<td style="vertical-align:top;text-align:left;" >
									<strong>Total</strong>
								</td>
								<td style="vertical-align:top;text-align:left;text-align:center;	" >
									<strong><?=$totalqty?></strong>
								</td>
								<td style="vertical-align:top;text-align:left;" >
									
								</td>
								<td style="vertical-align:top;text-align:left;" >
								</td>
								<td style="vertical-align:top;text-align:left;text-align:right;" >
									<strong><?=number_format($totaltaxable,2,".","")?></strong>
								</td>
							</tr>
							<? 
							if($rel['discount']!="0.00") { ?>
							<tr> 
								<td style="vertical-align:top;text-align:left;" colspan="2" >
									Discount
								</td>
								
								<td style="vertical-align:top;text-align:left;" >
									
								</td>
								<td style="vertical-align:top;text-align:right;padding:0px 5px;" >
									<?=number_format($rel['discount'],2,".","")?>
								</td>
							</tr>
							<?
							$total=($total)-$rel['discount']; 
							}
							if(!empty($rel['tax1_name'])) { ?>
							<tr> 
								<td style="vertical-align:top;text-align:left;" colspan="2" >
									<?=$rel['tax1_name']?>
								</td>
								
								<td style="vertical-align:top;text-align:left;" >
									
								</td>
								<td style="vertical-align:top;text-align:right;padding:0px 5px;" >
									<?=number_format($rel['taxvalue1'],2,".","")?>
								</td>
							</tr>
							<?
							$total=($total)+$rel['taxvalue1']; 
							}
							?>
							<? if(!empty($rel['tax2_name'])) { ?>
							<tr> 
								<td style="vertical-align:top;text-align:left;" colspan="2" >
									<?=$rel['tax2_name']?>
								</td>
								
								<td style="vertical-align:top;text-align:left;" >
									
								</td>
								<td style="vertical-align:top;text-align:right;padding:0px 5px;" >
									<?=number_format($rel['taxvalue2'],2,".","")?>
								</td>
							</tr>
							<?
							$total=($total)+$rel['taxvalue2']; 
							}
							if(!empty($rel['tax3_name'])) { ?>
							<tr> 
								<td style="vertical-align:top;text-align:left;" colspan="2" >
									<?=$rel['tax3_name']?>
								</td>
								
								<td style="vertical-align:top;text-align:left;" >
									
								</td>
								<td style="vertical-align:top;text-align:right;padding:0px 5px;" >
									<?=number_format($rel['taxvalue3'],2,".","")?>
								</td>
							</tr>
							<?
							$total=($total)+$rel['taxvalue3']; 
							}
							 
							$total=($total)+$rel['packing']; 
							if($rel['packing']!="0.00")
							{ ?>
							<tr> 
								<td style="vertical-align:top;text-align:left;" colspan="2" >
									Packing :
								</td>
								
								<td style="vertical-align:top;text-align:left;" >
									
								</td>
								<td style="vertical-align:top;text-align:right;padding:0px 5px;" >
									<?=number_format($rel['packing'],2,".","")?>
								</td>
							</tr>
							<? }?>
							<!--<tr> 
								<td style="vertical-align:top;text-align:left;" >
									Grand Total 
								</td>
								<td style="vertical-align:top;text-align:left;" >
									
								</td>
								<td style="vertical-align:top;text-align:left;" >
									
								</td>
								<td style="vertical-align:top;text-align:right;padding:0px 5px;" >
									<?=number_format($rel['g_total'],0,".","").'.00'?>
								</td>
							</tr>-->
							<? /* if($rel['paid_amount']!="0.00")
							{ ?>
							<tr> 
								<td style="vertical-align:top;text-align:left;" >
									Paid Amount
								</td>
								<td style="vertical-align:top;text-align:left;" >
									
								</td>
								<td style="vertical-align:top;text-align:left;" >
									
								</td>
								<td style="vertical-align:top;text-align:right;padding:0px 5px;" >
									<?=number_format($rel['paid_amount'],0,".","").'.00'?>
								</td>
							</tr>
							<? } */?>
							<tr>
								<td colspan="5" style="border-bottom:1px dashed black;border-top:1px dashed black;">
								<strong>Rupees :</strong>
								<?=ucwords(convert_number_to_words($total))?></td>
							</tr>
							
						<? 
							$qry1="select trn.*,led.l_name FROM `tbl_receipt_trn` as trn 
								left join tbl_receipt as rec on rec.receipt_id=trn.receipt_id
								left join tbl_ledger as led on led.l_id=rec.payment_mode_id
							  where trn.status=0 and invoice_id=".$rel['invoice_id']."";
								$result1=$dbcon->query($qry1);
								$p=1;
								while($row1=mysqli_fetch_assoc($result1))
								{
						?>
							<tr>
								<td colspan="2">Paid by : <?=($row1['l_name'])?></td>
								<td colspan="3" style="text-align:right;">Amount : <?=number_format($row1['paid_amount'],0,".","").'.00'?></td>
							</tr>
							
						<? $paidt=$paidt+$row1['paid_amount'];
							$p++;
						} ?>
						
						<? if($p!="2"){ ?>
							<? if($p!="1"){ ?>
							<tr>
								<td colspan="2">TOTAL</td>
								<td colspan="3" style="text-align:right;"><?=number_format($paidt,0,".","").'.00'?></td>
							</tr>
						<? } }?>
							<tr style="">
								<td colspan="5" style="text-align:center;padding-top: 30px;">
								<strong>As Per GST Rule, Tax exempted on Eggs
								</strong>
								</td>
							</tr>
                                                        <tr>
								<td colspan="5" style="text-align:left;white-space:nowrap;">
								<strong>Employee Name : <?=$rel['user_name']?></strong>
								</td>
							</tr>
							<tr>
								<td colspan="5" style="text-align:left;white-space:nowrap;">
								<strong>Remark : </strong> Goods once sold will not be taken back or exchanged.
									
								</td>
							</tr>

							<tr>
								<td colspan="5" style="text-align:center;padding-top: 15px;">
									<strong>***Thank you***</strong>
								</td>
							</tr>
							
							</table>
							</div>
								
			  <!--state overview end-->
          </section>
      </section>
      <!--main content end-->
      <!--footer start-->
	<?php //include_once('../include/footer.php');?>
      <!--footer end-->
  </section>

    <!-- js placed at the end of the document so the pages load faster -->
	<?php include_once('../include/include_js_file.php');?>   
	<!--<script src="<?=ROOT?>js/app/invoice.js"></script>-->
    <!--<script src="js/count.js"></script>-->
		
<script type="text/javascript"> 
//alert("dsk");
function print_receipt()
{
	//alert("dsa");
	var originalContents = document.body.innerHTML;
	//var duplicate = $("#invoiceprint").clone().prepend("<hr style='border-color:#000; border-style:dashed; margin:10px 0' />").appendTo("#invoiceprint");
	 var printContents = document.getElementById('receipt_print').innerHTML;     
     document.body.innerHTML = printContents;
     window.print();
     document.body.innerHTML = originalContents;
}

function PrintMe(DivID) {
//alert(DivID);
//alert("da");
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
  docprint.document.write('<link rel="stylesheet" href="<?php echo ROOT;?>css/style.css" media="all"/>');
  docprint.document.write('<link rel="stylesheet" href="<?php echo ROOT;?>css/bootstrap.min.css" media="all"/>');

  docprint.document.write('<style type="text/css">@media print{ @page { size:LETTER; } } body {');
  docprint.document.write('font-family:Verdana;color:#000;');
  docprint.document.write('font-family:Verdana; font-size:35px} .dataTables_length, .dataTables_filter , .dataTables_paginate { display:none; }');
  docprint.document.write('ul li {list-style: disc !important;} .dtl-data td,th { padding: 0 0px;}');
  docprint.document.write('a{color:#000;text-decoration:none;} h1 {font-size:30px; line-height:15px;} b { font-weight:normal; } div.page { page-break-after: always; page-break-inside: avoid; } .con ul {padding-left:0px !important;}.con ul li {margin-left:50px !important;} </style>');
  docprint.document.write('</head><body onLoad="self.print()">');
  docprint.document.write(content_vlue);
  docprint.document.write('</body></html>');
  docprint.document.close();
  docprint.focus();
	$('#table_head').show();
	$('#invoice_type').css('margin-top','0px');
  //location.reload();
}
PrintMe('receipt_print');
//alert("dsa");

</script>


  </body>
</html>
