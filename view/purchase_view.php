<?php 

	session_start();
	include_once("../config/config.php");
	include_once("../config/session.php");
	include_once("../include/coman_function.php");
	$token = md5(rand(1000,9999));
	$_SESSION['token'] = $token;
	$_SESSION['contents']=''; 
	$form="Purchase";
		$mode="Print";
		$purchaseorder_id=$dbcon->real_escape_string($_REQUEST['id']);
	$query="select po.*,state.state_name,ven.company_name as vender_name,country.country_name,ven.m_address as vender_address,ven.gst_no as tin_no,ven.cust_mobile as vender_mobile,ven.stateid,state.gst_state_code,city.city_name from tbl_pono as po 
		inner join tbl_ledger as ven on ven.l_id=po.vender_id
		left join country_mst as country on country.countryid=ven.countryid
		left join state_mst as state on state.stateid=ven.stateid
		left join city_mst as city on city.cityid=ven.cityid
		where po.po_id=$purchaseorder_id";
		$rel=mysqli_fetch_assoc($dbcon->query($query));
		$_SESSION['invoice_no']=$rel['invoice_no'];		
		
		$set="select comp.*,state.state_name,state.gst_state_code from tbl_company as comp left join state_mst as state on comp.stateid=state.stateid where company_id=".$rel['company_id'];
		$set_head=mysqli_fetch_assoc($dbcon->query($set));	
		$order_date='';
		if($rel['order_date']!="1970-01-01" && $rel['order_date']!="0000-00-00")
		{
			$order_date=date('d-m-Y',strtotime($rel['order_date']));
		}
		
		
		/* Check Discount is On or off Start */
		if($set_head['show_disc']=='1'){
			$colspan=5;
			$dynamicwidth=40;
		}else{
			$colspan=6;
			$dynamicwidth=46;
		}
		/* Check Discount is On or off End */
	
?>

<!DOCTYPE html>
<html lang="en">
<head>
<?php include_once('../include/include_css_file1.php');?>
 <style>
body {
    color: #000000;
	}

.con ul 
{
	padding-left:0px;
}
.con ul li 
{
	margin-left:22px;
	list-style: disc !important;
}


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
							  <li ><a href="<?=ROOT.'purchase_list'?>"><?=$form?> List</a></li>
							</ul>
						</div>
					</section>
				  <!--breadcrumbs end -->
			  </div>	
            </div>
			 
              <!--state overview start-->
	<div class="row">			
		<div class="col-sm-8 col-md-offset-2">
			<section class="panel">
				  	
		<div class="panel-body">
	<!--<center>-->
			<div class="col-md-4" style="display:none;"> With Logo</div>
						<br/>
				<label class="col-md-4 control-label"> </label>
				<div class="col-md-4 col-xs-11" style="display:none;">
				 <form class="form-horizontal" role="form" id="print_form" action="javascript:;" method="post" name="print_form">
					<select class="form-control" name="print_status" id="print_status" <?if($_REQUEST['printstatus']!=''){ echo "readonly";}?>>
						<option value="">Select Print</option>
						<option value="1" <?if($_REQUEST['printstatus']=='1'){ echo "selected";}?> selected>ORIGINAL</option>
						<option value="2" <?if($_REQUEST['printstatus']=='2'){ echo "selected";}?>>DUPLICATE</option>
						<option value="3" <?if($_REQUEST['printstatus']=='3'){ echo "selected";}?>>TRIPLICATE</option>
						<option value="4" <?if($_REQUEST['printstatus']=='4'){ echo "selected";}?>>EXTRA</option>
					</select>
				 </form>
				
				<div class="col-md-1">
					<input type="checkbox" class="form-control"  name="logo" id="logo" value="1">
				</div>
			<div class="col-md-4 resspace">
				<button type="submit" class="btn btn-success" onClick="PrintMe('receipt_print');"><i class="fa fa-print"></i> Print</button>
				<a href="<?=ROOT.'po_list'?>" type="button" class="btn btn-danger"><i class="fa fa-ban"></i> Cancel</a>
				<!--<input type="button" name="printpdf" id="printpdf" class="btn btn-default" value="Export to Pdf" onClick="make_pdf()" />-->
			</div>
			</div>
<!--</center>	-->	
				
			<div class="col-md-12"></div>
				<label class="col-md-3 control-label"></label>
			<div class="col-lg-4">
			</div>
<input type="hidden" name="typename" id="typename" value="<?=$rel['invoice_type']?>">
					<?php ob_start(); ?>
							<div class="col-lg-12" id="receipt_print">	<div class="col-md-12 breakout" style=" margin-top:10px;" id="print1">
				<!-- Fixed Logo Table Start -->
				<!--<img src="<?=ROOT.LOGO.$set_head['logo']?>"  style="width:100%"/>-->
				<table  class="maintable headermain " border="2" style="border-radius: 10px;
border-collapse: separate;
border-width: 2px;
border-color: black;
margin-top: 23px;
border: 1px solid; padding:17px 0 0;
" id="table_head" width="100%">
					<tr style="border:none;">
						<td width="100%" style="border:none;padding:0px 0px !important;"> 
							<!--<img src="<?=ROOT.LOGO.$set_head['logo']?>"  style="width:100%"/>-->
							
							<h1 style="margin-bottom:0px;" align="center"><?=$set_head['company_name']?></h1>
							<h5 align="center" style="padding:top:8px;"><?=$set_head['logo_content']?></h5>
							<h4 style="font-size:19px; margin-bottom:0px;" align="center"><?=$set_head['address']?></h3>
							
							<h4 style="font-size:14px; margin-top:0px;" align="center"><?if($set_head['website']){?>Email: <?=$set_head['website']?><?}?> 
							<?if($set_head['contact_no']){?>(M) <?=$set_head['contact_no']?><?}?></h4>
											<h4 align="center" style="margin-top:0px;"><?if($set_head['company_website']){?>Website: <?=$set_head['company_website']?><?}?></h4>
											
						</td>
					</tr>
				</table>
				<!-- Fixed Logo Table End -->
	<!-- Multipage Table Start -->	
	<table width="100%" class="maintable" style="font-size: 11px;   border-right: 0px solid !important; border-left: 0px solid !important;" id="invoice_type" >
	<thead>
		<tr>
			<th colspan="11" style="padding:0px !important;">
				<table style="font-size:12px;border-collapse: collapse;border-top:none;" cellpadding="0" cellspacing="0" width="100%" >
					<thead>	
					
						<tr>
							<td colspan="3" style="text-align:center;width:100%;"> 
								<strong style="font-size:14px;">
									<?=$form?>
								</strong>
							</td>
						</tr>
						<tr>
							<td width="53.7%" rowspan="4" style="vertical-align:top;border:1px solid;">
							<b>To, </b><br/>
								<strong><?=$rel['vender_name']?></strong>
								<span style="font-weight:normal;">   <br/>
								<?=$rel['vender_address']?>
								 <br>
								 <?=$rel['city_name']?>, <?=$rel['state_name']?>, <?=$rel['country_name']?></span>
									<!--<br>
									Mobile no : <?=$rel['cust_mobile']?>-->
									<br/>Vendor GST No. : <?=$rel['tin_no']?>
							</td>
						</tr>
						<tr>
							<td width="15%"  style="vertical-align:top;border:1px solid;border-right:none;"><strong>Purchase No </strong>
							</td>
							<td colspan="" style="vertical-align:top;border:1px solid;border-left:none;"> : <strong><?=$rel['po_no']?></strong>
							</td>
						</tr>
						<tr>
							<td style="vertical-align:top;border:1px solid;border-right:none;white-space:nowrap;"><strong>Purchase Date </strong>
							</td>
							<td style="vertical-align:top;border:1px solid;border-left:none;"> : <strong><?=date('d-m-Y',strtotime($rel['po_date']))?></strong>
							</td>
						</tr>
						
						</thead>
				</table>
			
			
			</th>
		</tr>
		<tr>
			<th width="3%" style="text-align:center !important;border:1px solid;border-top: none;"><strong>SR.<br/> NO.</strong></th>
			<th width="<?=$dynamicwidth?>%" style="text-align:center !important;border:1px solid;border-top: none;" colspan="2">
				<strong>Particulars </strong>
			</th>
			<th width="8%" style="text-align:center !important;border:1px solid;border-top: none;">
				<strong>HSN/SAC <br/>Code</strong>
			</th>
			<th width="7%" style="text-align:center !important;border:1px solid;border-top: none;">
				<strong>QTY.</strong>
			</th>
			<!--<th width="7%" style="text-align:center;border:1px solid;border-top: none;">
				<strong>Sqr/</br>Ft</strong>
			</th>-->
			<th width="7%" style="text-align:center !important;border:1px solid;border-top: none;">
				<strong>Rate</strong>
			</th>
			<? if($set_head['show_disc']=='1'){ ?>
			<th width="6%" style="text-align:center !important;border:1px solid;border-top: none;">
				<strong>Less:<br/>Disc.</strong>
			</th>
			<?}?>
			<th width="9%" style="text-align:center !important;border:1px solid;border-top: none;">
				<strong>Taxable<br/>Value</strong>
			</th>
			<th width="4%" style="text-align:center !important;border:1px solid;border-top: none;">
				<strong>Rate</strong>
			</th>
			<th width="6%" style="text-align:center !important;border:1px solid;border-top: none;">
				<strong>Amount</strong>
			</th>
			<th width="10%" style="text-align:center !important;border:1px solid;border-top: none;">
				<strong>Total</strong>
			</th>
		</tr>
	</thead>
	<tbody style="border: 1px solid;">
		<? 
	$qry="select trn.*,product.*,unit_name,group_concat(tax.tax_value) as tax_val,group_concat(tax.tax_name) as tax_name FROM `tbl_potrancation` as trn 
			left join tbl_product as product on product.product_id=trn.product_id 
			left join unit_mst as per on per.unitid=trn.unit_id 
			left join `formula_mst` as ftax on ftax.formulaid=trn.formulaid left join tbl_tax as tax on find_in_set(tax.tax_id,ftax.tax_id)
			where potrancation_status=0 and po_id=".$rel['po_id']." group by potrancation_id order by potrancation_id";
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
				}
				//tax summary calculation end
		?>
		<tr style="">
					<td style="text-align:center !important;vertical-align:top;border-right:1px solid;border-left:1px solid;">
							<?=$i?>
					</td>
					<td style="border-bottom-color:#FFFFFF; border-right:1px solid;" colspan="2">
						<?if($row['product_alias_name']){?>
							<strong><?=stripcslashes($row['product_alias_name'])?></strong>
							<br/><?=nl2br(stripcslashes($row['description']));?>
						<? }else{ ?>
							<strong><?=stripcslashes($row['product_name'])?></strong>
							<br/><?=nl2br(stripcslashes($row['description']));?>
						<?}?>
					</td>
					<td style="border-bottom-color:#FFFFFF; border-right:1px solid;vertical-align:top;text-align:center !important" >
					<?=stripcslashes($row['product_hsn_code'])?>
					</td>
					
					<td style="text-align:center !important; vertical-align:top;border-bottom-color:#FFFFFF; border-right:1px solid;white-space:nowrap;" >
						<? if($row['product_type']!='3'){ ?>
							<?=$row['product_qty'].' '.$row['unit_name']?>
						<? }else{
							$charges_qty+=$row['product_qty'];
						} ?>	
					</td>
					<!--<td style="text-align:center;vertical-align:top;border-bottom-color:#FFFFFF; border-right:1px solid;white-space:nowrap;" >
						<? if($row['product_type']!='3'){ ?>
							<?=$row['sqr_ft']?>
						<? }else{
							$charges_qty1+=$row['sqr_ft'];
						} ?>	
					</td>-->
					<td style="text-align:right !important; vertical-align:top;border-bottom-color:#FFFFFF; border-right:1px solid;" >
						<?=number_format($row['product_rate'],2,".","")?>
					</td>
					<? if($set_head['show_disc']=='1'){?>
					<td style="text-align:right !important; vertical-align:top;border-bottom-color:#FFFFFF;border-right:1px solid;">
						<?=number_format($row['discount_per'],2,".","").'%'?>
					</td>
					<?}?>
					<td style="text-align:right !important; vertical-align:top; border-bottom-color:#FFFFFF; border-right:1px solid;">
						<?=number_format($row['product_amount'],2,".","")?>
					</td>
					<td style="text-align:right !important; vertical-align:top;border-bottom-color:#FFFFFF;border-right:1px solid;">
						<?=$tax_arr[0]+$tax_arr[1]?>%
					</td>
					<td style="text-align:right !important; vertical-align:top;border-bottom-color:#FFFFFF;border-right:1px solid;">
						<?=number_format($row['tax_amount1']+$row['tax_amount2'],2,".","")?>
					</td>
					
					<td style="text-align:right !important; vertical-align:top;border-bottom-color:#FFFFFF;border-right:1px solid;">
						<?=number_format($row['total'],2,".","")?>
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
			$pr=12-$cnt;
			
			for($j=0; $j<$pr; $j++)
			{
		?>	
			<tr style="height:40px">
					<td style="border-right:1px solid;border-left:1px solid;">&nbsp;</td>
					<td style="border-right:1px solid;" colspan="2">&nbsp;</td>
					<td style="border-right:1px solid;">&nbsp;</td>
					<? if($set_head['show_disc']=='1'){?>
					<td style="border-right:1px solid;">&nbsp;</td>
					<?}?>
					<!--<td style="border-right:1px solid;"></td>-->
					<td style="border-right:1px solid;">&nbsp;</td>
					<td style="border-right:1px solid;">&nbsp;</td>
					<td style="border-right:1px solid;">&nbsp;</td>
					<td style="border-right:1px solid;">&nbsp;</td>
					<td style="border-right:1px solid;">&nbsp;</td>
					<td style="border-right:1px solid;">&nbsp;</td>
			</tr>
			
		<?  } ?>
			<tr style="height:20px">
				<td style="border-top:1px solid;border-right:1px solid;border-left:1px solid; text-align:right !important;" colspan="4"><strong>Total</strong></td>
				
				<td style="text-align:center !important;border-top:1px solid;border-right:1px solid;"><strong><?=number_format($totalqty,2,".","")?></strong>&nbsp;</td>
				<!--<td style="text-align:center;border-top:1px solid;border-right:1px solid;"><strong><?=number_format($totalsqr,2,".","")?></strong></td>-->
				<? if($set_head['show_disc']=='1'){?>
				<td style="border-top:1px solid;border-right:1px solid;">&nbsp;</td>
				<?}?>
				<td style="border-top:1px solid;border-right:1px solid;">&nbsp;</td>
				<td style="border-top:1px solid;border-right:1px solid;text-align:right;"><strong><?=number_format($totaltaxable,2,".","")?></strong>&nbsp;</td>
				<td style="border-top:1px solid;border-right:1px solid;text-align:right;">&nbsp;</td>
				<td style="border-top:1px solid;border-right:1px solid;text-align:right;"><strong><?=number_format($totaltax1+$totaltax2,2,".","")?></strong>&nbsp;</td>
				
				<td style="border-top:1px solid;border-right:1px solid;text-align:right;"><strong><?=number_format($total,2,".","")?></strong>&nbsp;</td>
							
			</tr>		
			<tr>
				<td colspan="11" style="padding: 0px !important;border:1px solid">
				<table class="footer-table" width="100%">
					<tr width="61.6%" height="20px">
							<td style="border-right:1px solid;" colspan="<?=$colspan?>">
							<!--<? if(!empty($set_head['bank_name'])){?>
									<strong>Bank Name:</strong> <?=$set_head['bank_name']?>, 
									<? } ?>
								<? if(!empty($set_head['ac_no'])){?>
									<strong>A/c No:</strong> <?=$set_head['ac_no']?>	 
									<? } ?>-->
							</td>
						<td width="28.9%" colspan="3" style="border-right:1px solid;text-align:left !important">
							Taxable Amount
						</td>
						<td colspan="2" style="text-align:right !important;" width="10%"><?=number_format($totaltaxable,2,".","")?></td>	
					</tr>
					<tr height="20px">
						<td style="border-right:1px solid;border-top:1px solid;" colspan="<?=$colspan?>">
							<!--<? if(!empty($set_head['ifcs'])){ ?>
									<strong>IFCS:</strong><?=$set_head['ifcs']?>,
								<? } ?>	
								<? if(!empty($set_head['branch_name'])){ ?>
									<strong>Branch :</strong> <?=$set_head['branch_name']?><? } ?>-->
						</td>
					
						<td colspan="3" style="border-top:1px solid;border-right:1px solid;text-align:left !important" >
						
							<?=(($rel['stateid']==$set_head['stateid'])?'Add :  CGST':'Add :  IGST');?>
						</td>
						<td colspan="2" style="text-align:right; border-top:1px solid;border-right:1px solid; "><?=number_format($totaltax1,2,".","")?></td>
						
					</tr>
					
					<? if($rel['stateid']==$set_head['stateid']) { ?>
					<tr height="20px">
						<td style="border-right:1px solid;border-top:1px solid;" colspan="<?=$colspan?>">
						
						</td>
						<td colspan="3" style="border-top:1px solid;border-right:1px solid;text-align:left !important">Add : SGST</td>
						<td colspan="2" style="text-align:right !important; border-top:1px solid;border-right:1px solid; "><?=number_format($totaltax2,2,".","")?></td>
					</tr>
					<? }
						$totaltax=$totaltax1+$totaltax2;
					?>
					<!--<tr height="20px">
						<td style="border-right:1px solid;border-top:1px solid;border-left:1px solid; font-size:10px;" colspan="<?=$colspan?>">
										
						</td>
						<td colspan="3" style="border-top:1px solid;border-right:1px solid;font-size:10px;text-align:left">
						Tax Amount :  GST
						</td>
						<?php
						$totaltax
						?>
						<td colspan="2" style="text-align:right; border-top:1px solid;font-size:10px;border-right:1px solid; "><?=number_format($totaltax,2,".","")?></td>
					</tr>-->
		<?php 
				$total=($total)+$rel['packing']; 
				if($rel['packing']!="0.00"){ ?>
					<tr height="20px">
						<td style="border-right:1px solid;border-top:1px solid;" colspan="<?=$colspan?>">
							
						</td>
						<td colspan="3" style="border-top:1px solid;border-right:1px solid;text-align:left !important">Transport :</td>
						<td colspan="2" style="text-align:right !important; border-top:1px solid;border-right:1px solid; "><?=number_format($rel['packing'],2,".","")?></td>
					</tr>
					<? }
					$r=round($total)-$total; 
					?>
					<tr height="20px">
						<td style="border-right:1px solid;border-top:1px solid;" colspan="<?=$colspan?>">
							<strong>COMPANY GST No. : <?=$set_head['vatno']?> </strong><br>
						</td>
						<td colspan="3" style="border-top:1px solid;border-right:1px solid;text-align:left !important">Round off</td>
						<td colspan="2" style="text-align:right !important; border-top:1px solid;border-right:1px solid; "><?=number_format($r+$rel['round_off'],2,".","")?></td>
					</tr>
					
					<tr height="20px">
						<td style="border-right:1px solid;border-top:1px solid;" colspan="<?=$colspan?>">
							<strong>Rupees:</strong>
									<?=ucwords(convert_number_to_words($rel['g_total']))?>
						</td>
						<td colspan="3" style="border-top:1px solid;border-right:1px solid;text-align:left !important"><strong>Grand Total</strong> </td>
						<td colspan="2" style="text-align:right !important; border-top:1px solid;font-size:13px;border-right:1px solid; "><strong><?=number_format($rel['g_total'],0,".","").'.00'?></strong></td>
					</tr>
					<!--<tr height="35px">
						<td colspan="<?=5+$colspan?>" style="border:1px solid;border-bottom:none;"></td>
					</tr>
					<tr>
					<td style="border-right:1px solid;border-top:1px solid;border-left:1px solid; font-size:10px;padding:0px !important;" 	colspan="<?=5+$colspan?>">
					<?
								
								if($rel['stateid']==$set_head['stateid'])
								{
									echo '<table border="0" style="font-size:10px;text-align:right;" width="100%"><tr> 
										<td style="vertical-align:top;text-align:center;border-bottom:1px solid;border-right:1px solid;" >
										<strong>HSN Code</strong>
										</td>
										<td style="vertical-align:top;text-align:center;border-bottom:1px solid;border-right:1px solid;" >
										<strong>Total Amt.</strong>
										</td>
										<td style="vertical-align:top;text-align:center;border-bottom:1px solid;border-right:1px solid;" >
											<strong>CGST Rate</strong>
										</td>
										<td style="vertical-align:top;text-align:center;border-bottom:1px solid;border-right:1px solid;" >
											<strong>CGST Amt.</strong>
										</td>
										<td style="vertical-align:top;text-align:center;border-bottom:1px solid;border-right:1px solid;" >
											<strong>SGST Rate</strong>
										</td>
										<td style="vertical-align:top;text-align:center;border-bottom:1px solid;border-right:1px solid;" >
											<strong>SGST Amt.</strong>
										</td>
										<td style="vertical-align:top;text-align:center;border-bottom:1px solid;"><strong>Total Tax Amount<strong></td>
									</tr>';
								}
								else if($rel['stateid']!=$set_head['stateid'])
								{
									echo '<table border="0" style="font-size:10px;text-align:right;" width="100%"><tr> 
										<td style="vertical-align:top;text-align:center;border-bottom:1px solid;border-right:1px solid;" >
										<strong>HSN Code</strong>
										</td>
										<td style="vertical-align:top;text-align:center;border-bottom:1px solid;border-right:1px solid;" >
										<strong>Taxable Amt.</strong>
										</td>
										<td style="vertical-align:top;text-align:center;border-bottom:1px solid;border-right:1px solid;" >
											<strong>IGST Rate</strong>
										</td>
										<td style="vertical-align:top;text-align:center;border-bottom:1px solid;border-right:1px solid;" >
											<strong>IGST Amt.</strong>
										</td>
									<td style="vertical-align:top;text-align:center;border-bottom:1px solid;"><strong>Total Tax Amount<strong></td>
									</tr>';
								}
					$query="select sum(total) as amount,sum(tax_amount1) as tax_amt1,trn.product_hsn_code,sum(tax_amount2) as tax_amt2,tax_name1,tax_name2 
					FROM `tbl_invoicetrn` as trn where trancation_status=0 and purchaseorder_id=".$rel['purchaseorder_id']." group by trn.formulaid, trn.product_hsn_code";
					$rs_tax=$dbcon->query($query);
							while($rel_tax=mysqli_fetch_assoc($rs_tax))
							{	
								$total1+=$row_total=$rel_tax['tax_amt1']+$rel_tax['tax_amt2'];
								echo '<tr> 
										<td style="vertical-align:top;text-align:right;border-right:1px solid;border-bottom:1px solid;" >
										'.$rel_tax['product_hsn_code'].'
										</td>
										<td style="vertical-align:top;text-align:right;border-right:1px solid;border-bottom:1px solid;" >
										'.$rel_tax['amount'].'
										</td>';
								if($rel['stateid']==$set_head['stateid'])
								{
									echo '<td style="vertical-align:top;text-align:right;border-right:1px solid;border-bottom:1px solid;" >
											'.str_replace("CGST","",$rel_tax['tax_name1']).'
										</td>
										<td style="vertical-align:top;text-align:right;border-right:1px solid;border-bottom:1px solid;" >
											'.$rel_tax['tax_amt1'].'
										</td>
										<td style="vertical-align:top;text-align:right;border-right:1px solid;border-bottom:1px solid;" >
											'.str_replace("SGST","",$rel_tax['tax_name2']).'
										</td>
										<td style="vertical-align:top;text-align:right;border-right:1px solid;border-bottom:1px solid;" >
											'.$rel_tax['tax_amt2'].'
										</td>';
								}
								else if($rel['stateid']!=$set_head['stateid'])
								{
									echo '<td style="vertical-align:top;text-align:center;border-right:1px solid;border-bottom:1px solid;" >
											'.str_replace("IGST","",$rel_tax['tax_name1']).'
										</td>
										<td style="vertical-align:top;text-align:right;border-right:1px solid;border-bottom:1px solid;" >
											'.$rel_tax['tax_amt1'].'
										</td>';
								}
								echo '<td style="vertical-align:top;text-align:right;border-bottom:1px solid;" >
											'.number_format($row_total,2).'
										</td>';
								
								echo '</tr>';
							$totalamt+=$rel_tax['amount'];
							$totaltaxamt1+=$rel_tax['tax_amt1'];
							$totaltaxamt2+=$rel_tax['tax_amt2'];
							}
							echo '<tr> 
										<td></td>
										<td style="vertical-align:top;text-align:right;border-top:1px solid;border-right:1px solid;" >
										'.number_format($totalamt,2).'
										</td>
										<td style="vertical-align:top;text-align:right;border-top:1px solid;border-right:1px solid;" >
											
										</td>
										<td style="vertical-align:top;text-align:right;border-top:1px solid;border-right:1px solid;" >
											'.number_format($totaltaxamt1,2).'
										</td>';
								if($rel['stateid']==$set_head['stateid'])
								{
									echo '<td style="vertical-align:top;text-align:right;border-top:1px solid;border-right:1px solid;" >
											
										</td>
										<td style="vertical-align:top;text-align:right;border-top:1px solid;border-right:1px solid;" >
											'.number_format($totaltaxamt2,2).'
										</td>';
								}
								echo '<td style="vertical-align:top;text-align:right;border-top:1px solid;">'.number_format($total1,2).'</td></tr></table>';
							?>
							</td>
					</tr>
					<tr height="35px">
						<td colspan="<?=5+$colspan?>" style="border-top:1px solid;b"></td>
					</tr>	-->	
					<tr>
						<td colspan="<?=$colspan?>" style="vertical-align:top;font-size:10px;text-align:left !important;border-top:1px solid;" class="con">
							
						<? if(!empty($set_head['po_condition'])){ ?>
								<strong>Terms and Conditions:</strong><br> <?=$set_head['po_condition']?>
							<? } ?>	
	
						</td>
						<td colspan="5" style="vertical-align:top;border-top:1px solid;">
						<center>
						For, <strong> <span style="font-size:11px;text-decoration:bold;">
						<?=$set_head['company_name']?></span></strong>
						
						</center>
						 <br><br><br><br>
						 <center style="vertical-align:bottom;">Authorised Signatory</center>

						</td>
						
						</tr>
				</table>
					</tr>		
	</tbody>
				<!--<table width="100%" border="0" style="margin-top: 5px;" id="table_foot">
					<tr>
						<td style="border:none;padding:0px 0px !important;width:100%;"> 
							<img src="<?=ROOT.LOGO.$set_head['f_logo']?>"  style="width:100%"/>
						</td>
					</tr>
				</table>-->
		
	</table>
	<!-- Multipage Table End -->		
		<!--<table width="100%" border="0" style="" id="table_foot">
	<tr>
	<td colspan="7"  style="border-left:none;border-top:none;border-bottom:none;padding-left:0px;"> 
	<img src="<?=ROOT.LOGO.$set_head['f_logo']?>"  style="width:100%"/>
	
	</td>
	</tr>
	</table>	-->			
		<!--<center><span style="float:left;">E.& O. E.</span>This is a Computer Generated Invoice</center>-->
							</div>
								<div id="print2" style="margin-top:0in;"></div>
								<div id="print3" style="margin-top:0in;"></div>
						
</div>
	<?php  
			$contents = ob_get_contents();
			$_SESSION['contents']=$contents;
			$_SESSION['file_name']='invoice-#';
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
    
<script type="text/javascript"> 
function print_receipt()
{
	var originalContents = document.body.innerHTML;
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
		//$("#print"+i+" .data_title").html('Performance');
		$("#type").html("Performance Invoice");
	}
	if($("#invoice").val()==1)
	{
		//$("#print"+i+" .data_title").html('ORIGINAL FOR RECIPIENT');
		$("#type").html($("#typename").val());
	}
	if(i<$('#print_status').val())
	{
		$("#print"+i).after('<div class="page"></div>');
	}
	$("#print"+(i+1)).html($("#print1").clone());
	if((i+1)==2)
	{
		//$("#print"+(i+1)+" .data_title").html('DUPLICATE FOR SUPPLIER');
	}
	if((i+1)==3)
	{
		//$("#print"+(i+1)+" .data_title").html('TRIPLICATE FOR TRANSPORTER');
	}
	
}
}
else
{
	//$("#print1 .data_title").html('EXTRA');
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
		docprint.document.write(' @media print{ @page { size:A4; margin: 0.2in <?=$set_head['letter_head_right_margin']?>in 0.2in <?=$set_head['letter_head_left_margin']?>in; } }   ');
		
	}
	else
	{
		docprint.document.write(' @media print{ @page { size:A4; margin: <?=$set_head['letter_head_top_margin']?>in <?=$set_head['letter_head_right_margin']?>in <?=$set_head['letter_head_bottom_margin']?>in <?=$set_head['letter_head_left_margin']?>in; }  }  #table_head, #table_foot { display:none }');
		//$('#invoice_type').css('margin-top','1.7in');	
		
	}
 
  docprint.document.write('body { font-family:Tahoma;color:#000;');
  docprint.document.write('font-family:Tahoma,Verdana; font-size:10px;}.breakout table td,.breakout table th {padding: inherit !important;text-align: inherit !important;} .dataTables_length, .dataTables_filter , .dataTables_paginate { display:none; }');
  docprint.document.write('.breakout table td,.breakout table th {padding: inherit !important;text-align: inherit !important;}a{color:#000;text-decoration:none;} h1 {font-size:25px; line-height:5px;} b { font-weight:normal; } div.page { page-break-after: always; page-break-inside: avoid; } tr { page-break-inside: avoid } .maintable tbody tr { border-bottom:1px solid; }');
  docprint.document.write('.breakout table td,.breakout table th {padding: 2px !important;text-align: inherit !important;} .maintable table { page-break-inside:auto } .maintable tr{ page-break-inside:avoid; page-break-after:auto } .maintable thead { display:table-header-group }  .maintable tfoot tr{ /*display:table-footer-group;*/ page-break-inside:avoid; page-break-before:always; } footer-table{ page-break-inside:avoid; page-break-before:always;  } #table_foot{position:fixed;bottom:0}</style>');
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
