<?php 

include("../../config/config.php");
$get=$_GET['id'];
$pdf_upload=quotation_pdf($get,"pdf",$dbcon);
//error_reporting(E_ALL);
function quotation_pdf($id,$type,$dbcon)
{
	ob_start();
	$id = $dbcon->real_escape_string($id);	
	$query="select invoice.*,country.country_name,state.state_name,cust.stateid,state.gst_state_code, city.city_name, cust.company_name,cust.m_address as cust_address, type.invoice_type,cust_pincode,cust_mobile,gst_no,usr.user_name from tbl_invoice as invoice 
	left join tbl_ledger as cust on cust.l_id=invoice.cust_id
	left join country_mst as country on country.countryid=cust.countryid
	left join state_mst as state on state.stateid=cust.stateid
	left join city_mst as city on city.cityid=cust.cityid
	left join tbl_invoicetype as type on type.invoicetype_id=invoice.invoicetype_id
	left join users as usr on usr.user_id=invoice.user_id
	where invoice_id=$id";
	$rel=mysqli_fetch_assoc($dbcon->query($query));
	//$subject=$rel['quotation_subject'];
	$type="pdf";
	$order_date='';
		if($rel['order_date']!="1970-01-01" && $rel['order_date']!="0000-00-00")
		{
			$order_date=date('d-m-Y',strtotime($rel['order_date']));
		}
    if(isset($type)== "pdf") {
		$type = $dbcon -> real_escape_string($type);
	}
	else {
		die('<h1> ERROR </h1>');
	}
      if(strtolower($type) == 'pdf') 
	{
		$id_num = 1;
		$query2="select comp.*,state.state_name,state.gst_state_code from tbl_company as comp left join state_mst as state on comp.stateid=state.stateid where company_id=".$rel['company_id'];
		
		//$query2="select * from tbl_company where company_id=".$rel['company_id'];
		$rel2=mysqli_fetch_assoc($dbcon->query($query2));
			$pro_terms_data='';
			 if(!empty($rel['cust_pincode'])){	
				$pinco="- ".$rel['cust_pincode'];
			 } 
	$bill_party="<strong>".$rel['company_name']."</strong><br/>".$rel['cust_address']."<br/>".$rel['city_name'].",".$rel['state_name'].",".$rel['country_name']."".$pinco."<br/> Mobile no : ".$rel['cust_mobile']."<br/> GSTIN ".$rel['gst_no']."<br/>State : ".$rel['state_name']."  Code :".$rel['gst_state_code'] ;
			
$header ='<div style="width:100%;text-align:left;margin-bottom:0px;margin-bottom:0px;">
			<table style="border-collapse:collapse;width:100%;text-align:left;border:none;font-size:15px;">
				<tr>
					<td style="width:50%;margin-top:0px;vertical-align: top;text-align:left;" rowspan="4">
						<img src="'.DOMAIN_F.LOGO.$rel2['logo'].'" style="width:1.8in;height:1.2in;" />
					</td>
					<td style="width:50%;text-align:right;font-size:20px;"><b>'.$rel2['company_name'].'</b></td>
				</tr>
				<tr>
					<td style="text-align:right;">'.$rel2['address'].'</td>
				</tr>
				<tr>
					<td style="text-align:right;">Email:'.$rel2['website'].'</td>
				</tr>
				<tr>
					<td style="text-align:right;">Phone No:'.$rel2['contact_no'].'</td>
				</tr>
			</table>
			<hr style="border-style: double; border-width: 12px;margin-top:0px;">
</div>';  
//$footer ='<img src="'.DOMAIN_F.LOGO.$rel2["f_logo"].'" style="width:8.27in" />';  
//$footer ='<div class="col-md-12" style="text-align: center !important;"><center>'.$rel2['address'].'</center></div>';  
$footer ='<div style="width:100%;text-align:center;margin-bottom:5px;margin-bottom:5px;">
				<span style="font-weight:bold;font-size:12px;">'.$rel2['address'].'</span>
				<br/>
				<span style="font-weight:bold;font-size:12px;">Email:'.$rel2['website'].' </span>
				<span style="font-weight:bold;font-size:12px;"> Phone No:'.$rel2['contact_no'].'</span>
		</div>';  
$footer ='';
		$html ='<html>
					<head>					
						<title>Invoice - '.$rel['company_name'].'</title>
							<style type="text/css">
							.page{
								width:8.27in;
								height:10.69in;
							}

							table {
							width:100%;
							}
							th {
							font-weight:bold;
							}

							td ul
							{
							  margin: 0px 0px;
							}

							p {margin: 10px 0px;}

						</style>
					</head>
				<body>
					<htmlpageheader name="otherpages" style="display:none">
						<div style="text-align:center">'.$header.'</div>
					</htmlpageheader>
					<sethtmlpageheader name="otherpages" value="on" show-this-page="0"/>
						<div class="page">
							<!--<div style="clear:both;"></div>
								<div style="margin:0px 0px 0px 0px;font-size:20px;position: relative;width: 100%;" >
								<div style="width:100%;text-align:center;margin-bottom:0px;">
									<h3 align="left">'.$rel["user_name"].' ('.$rel2["company_name"].')</h3>
									<hr style="border-style: double; border-width: 12px; margin-top: -0.5em;">
								</div>
							</div>-->
					<div style="clear:both;"></div>
						<div style="width:100%; float:left;">
							<div style="margin-left:0px;margin-top:10px;font-size:17px;width:100%;float:left">
								<table style="border-collapse:collapse;width:100%;text-align:left;border:none;font-size:15px;">
									<tr>
										<td style="width:5%;"></td>
										<td style="width:8%;"></td>
										<td style="width:13%;"></td>
										<td style="width:11%;"></td>
										<td style="width:13%;"></td>
										
										<td style="width:10%;"></td>
										<td style="width:10%;"></td>
										<td style="width:10%;"></td>
										<td style="width:10%;"></td>
										<td style="width:10%;"></td>
									</tr>
									<tr>
										<td ></td>
										<td ></td>
										<td ></td>
										<td ></td>
										<td colspan="2" style="text-align:center;font-size:20px;"><strong>'.$rel['invoice_type'].'</strong></td>
										<td ></td>
										<td colspan="3"  style="text-align:right;" >ORIGINAL FOR RECIPIENT</td>
										
									</tr>
									<tr>
										<td colspan="2" style="border-left:1px solid #0e0e0e;border-top:1px solid #0e0e0e;">Invoice No</td>
										<td style="border-top:1px solid #0e0e0e;border-right:1px solid #0e0e0e;" >: '.$rel["invoice_no"].'</td>
										<td style="border-top:1px solid #0e0e0e;">Date</td>
										<td style="border-top:1px solid #0e0e0e;border-right:1px solid #0e0e0e;" >: '.date('d-m-Y',strtotime($rel['invoice_date'])).'</td>
										<td colspan="2" style="border-top:1px solid #0e0e0e;" >Vehicle No</td>
										<td colspan="3" style="border-top:1px solid #0e0e0e;border-right:1px solid #0e0e0e;" >: '.$rel['vehicle_no'].'</td>
									</tr>
									<tr>
										<td colspan="2" style="border-left:1px solid #0e0e0e;border-top:1px solid #0e0e0e;">Po No</td>
										<td style="border-top:1px solid #0e0e0e;border-right:1px solid #0e0e0e;" >: '.$rel["order_no"].'</td>
										<td style="border-top:1px solid #0e0e0e;">Date</td>
										<td style="border-top:1px solid #0e0e0e;border-right:1px solid #0e0e0e;" >: '.$order_date.'</td>
										<td colspan="2" style="border-top:1px solid #0e0e0e;" >Crates No</td>
										<td colspan="3" style="border-top:1px solid #0e0e0e;border-right:1px solid #0e0e0e;" >: '.$rel['docket_no'].'</td>
									</tr>
									<tr>
										<td colspan="2" style="border-left:1px solid #0e0e0e;border-top:1px solid #0e0e0e;">State</td>
										<td style="border-top:1px solid #0e0e0e;border-right:1px solid #0e0e0e;" >: '.$rel2['state_name'].'</td>
										<td style="border-top:1px solid #0e0e0e;">Code</td>
										<td style="border-top:1px solid #0e0e0e;border-right:1px solid #0e0e0e;" >: '.$rel2['gst_state_code'].'</td>
										<td colspan="2" style="border-top:1px solid #0e0e0e;" >Reverse Charge</td>
										<td colspan="3" style="border-top:1px solid #0e0e0e;border-right:1px solid #0e0e0e;" >: '.(!empty($rel['reverse_charge'])?'Yes':'No').'</td>
									</tr>
									<tr>
										<td colspan="2" style="border-left:1px solid #0e0e0e;border-top:1px solid #0e0e0e;white-space:nowrap;">Employee Name</td>
										<td colspan="3" style="border-top:1px solid #0e0e0e;border-right:1px solid #0e0e0e;" >: '.$rel['user_name'].'</td>
									
										
										<td colspan="2" style="border-top:1px solid #0e0e0e;" ></td>
										<td colspan="3" style="border-top:1px solid #0e0e0e;border-right:1px solid #0e0e0e;" ></td>
									</tr>
									<tr>
										<td colspan="5" style="border-left:1px solid #0e0e0e;border-top:1px solid #0e0e0e;white-space:nowrap;">Bill to Party : <br/>
										'.$bill_party.'</td>
										<td colspan="5" style="border-top:1px solid #0e0e0e;border-right:1px solid #0e0e0e;border-left:1px solid #0e0e0e;" >Shipped to Party : <br/>
										'.$bill_party.'</td>
									</tr>
									<tr>
										<td style="border-left:1px solid #0e0e0e;border-top:1px solid #0e0e0e;text-align:center;">SR.<br/>NO.</td>
										<td colspan="4" style="border-left:1px solid #0e0e0e;border-top:1px solid #0e0e0e;text-align:center;">Particulars</td>
										<td style="border-left:1px solid #0e0e0e;border-top:1px solid #0e0e0e;text-align:center;">HSN/SAC<br/>Code</td>
										<td style="border-left:1px solid #0e0e0e;border-top:1px solid #0e0e0e;text-align:center;">QTY.</td>
										<td style="border-left:1px solid #0e0e0e;border-top:1px solid #0e0e0e;text-align:center;">Rate</td>
										<td style="border-left:1px solid #0e0e0e;border-top:1px solid #0e0e0e;text-align:center;">MRP</td>
										
										<td style="border-left:1px solid #0e0e0e;border-top:1px solid #0e0e0e;border-right:1px solid #0e0e0e;text-align:center;">Total</td>
									</tr>';
									
									$qry="select trn.*,product.*,unit_name,group_concat(tax.tax_value) as tax_val,group_concat(tax.tax_name) as tax_name FROM `tbl_invoicetrn` as trn left join tbl_product as product on product.product_id=trn.product_id left join unit_mst as per on per.unitid=trn.unit_id 
									left join `formula_mst` as ftax on ftax.formulaid=trn.formulaid left join tbl_tax as tax on find_in_set(tax.tax_id,ftax.tax_id)
									where trancation_status=0 and invoice_id=".$rel['invoice_id']." group by trancation_id order by product_type,trancation_id";
									$result=$dbcon->query($qry);		
									$i=1;$total=0;$discount=0;$totalqty=0;$charges_qty=0;
									$cnt=mysqli_num_rows($result);
									while($row=mysqli_fetch_assoc($result))
									{
										$html .='<tr>
										<td style="border-left:1px solid #0e0e0e;border-top:1px solid #0e0e0e;text-align:center;">'.$i.'</td>
										<td colspan="4" style="border-left:1px solid #0e0e0e;border-top:1px solid #0e0e0e;text-align:left;">
										'.stripcslashes($row['product_name']).'-'.$row['item_code'].'</strong>
										<br/>'.nl2br(stripcslashes($row['description'])).'
										</td>
										<td style="border-left:1px solid #0e0e0e;border-top:1px solid #0e0e0e;text-align:center;">'.$row['product_hsn_code'].'</td>
										<td style="border-left:1px solid #0e0e0e;border-top:1px solid #0e0e0e;text-align:center;">
										'.$row["product_qty"].' '.$row["unit_name"].'
										</td>
										<td style="border-left:1px solid #0e0e0e;border-top:1px solid #0e0e0e;text-align:center;">
										'.number_format($row['product_rate'],2,".","").'
										</td>
										<td style="border-left:1px solid #0e0e0e;border-top:1px solid #0e0e0e;text-align:center;">
										'.number_format($row['mrp'],2,".","").'
										</td>
										
										<td style="border-left:1px solid #0e0e0e;border-top:1px solid #0e0e0e;border-right:1px solid #0e0e0e;text-align:right;">
										'.number_format($row['total'],2,".","").'
										</td>
									</tr>';
									
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
									$html .='<tr>
										<td style="border-left:1px solid #0e0e0e;text-align:center;height:25px;"></td>
										<td colspan="4" style="border-left:1px solid #0e0e0e;text-align:left;">
										</td>
										<td style="border-left:1px solid #0e0e0e;text-align:center;"></td>
										<td style="border-left:1px solid #0e0e0e;text-align:center;">
										</td>
										<td style="border-left:1px solid #0e0e0e;text-align:center;">
										<td style="border-left:1px solid #0e0e0e;text-align:center;">
										</td>
										
										<td style="border-left:1px solid #0e0e0e;border-right:1px solid #0e0e0e;text-align:right;">
										</td>
									</tr>';
									}
								$html .='
								<tr>
									<td colspan="6" style="border-left:1px solid #0e0e0e;border-top:1px solid #0e0e0e;text-align:right;">Total</td>
									<td style="border-left:1px solid #0e0e0e;border-top:1px solid #0e0e0e;text-align:center;">
										'.number_format($totalqty,2,".","").'
										</td>
										<td style="border-left:1px solid #0e0e0e;border-top:1px solid #0e0e0e;text-align:center;">
										<td style="border-left:1px solid #0e0e0e;border-top:1px solid #0e0e0e;text-align:center;">
										</td>
										
										<td style="border-left:1px solid #0e0e0e;border-top:1px solid #0e0e0e;border-right:1px solid #0e0e0e;text-align:right;">
										'.number_format($total,2,".","").'
										</td>
									</tr>
									<tr>
									<td colspan="6"  style="border-left:1px solid #0e0e0e;border-top:1px solid #0e0e0e;text-align:left;">Bank Name: '.$rel2['bank_name'].' ,A/c No: '.$rel2['ac_no'].'</td>
									<td colspan="4" rowspan="3" style="border-left:1px solid #0e0e0e;border-top:1px solid #0e0e0e;border-right:1px solid #0e0e0e;text-align:center;">
										'.$rel2['invoice_tax_content'].'
									</td>
									</tr>
									<tr>
									<td colspan="6" style="border-left:1px solid #0e0e0e;border-top:1px solid #0e0e0e;text-align:left;">IFSC: '.$rel2['ifcs'].' ,Branch : '.$rel2['branch_name'].'</td>
									
									</tr>
									<tr>
									<td colspan="6" style="border-left:1px solid #0e0e0e;border-top:1px solid #0e0e0e;text-align:left;">COMPANY GST No. : '.$rel2['vatno'].'</td>
									</tr>
									<tr>
										<td colspan="6" style="border-left:1px solid #0e0e0e;border-top:1px solid #0e0e0e;text-align:left;">Rupees. : '.ucwords(convert_number_to_words($total)).'</td>
										<td colspan="2" style="border-left:1px solid #0e0e0e;border-top:1px solid #0e0e0e;border-right:1px solid #0e0e0e;text-align:left;">Grand Total.</td>
										<td colspan="2" style="border-left:1px solid #0e0e0e;border-top:1px solid #0e0e0e;border-right:1px solid #0e0e0e;text-align:right;">'.number_format($total,0,".","").'</td>
									</tr>
									<tr>
										<td colspan="6" style="border-left:1px solid #0e0e0e;border-top:1px solid #0e0e0e;border-bottom:1px solid #0e0e0e;text-align:left;">
										<strong>Remark:</strong> '.$rel['remark'].' <br/>
										<strong>Terms and Conditions:</strong><br/> '.$rel2["conditions"].'
										</td>
										<td colspan="4" style="border-top:1px solid #0e0e0e;border-right:1px solid #0e0e0e;border-bottom:1px solid #0e0e0e;text-align:center;">For, '.$rel2['company_name'].'<br/>
											<img src="'.ROOT.LOGO.$rel2['f_logo'].'"  style="width:20%;height:90px;"/>
											<br/>
										Authorised Signatory
										</td>
										
									</tr>
								</table>
							</div>
						</div>
						<div style="clear:both;"></div>
					</div><!--page1 end-->
					</body>
				</html>';


		//echo $html;
		//exit;
	$file_name = $rel['invoice_id'].'_'.$rel['invoice_no'].'_'.date("d/m/Y",strtotime($rel['invoice_date'])).'.pdf';
	$file_name=str_ireplace("/","-",$file_name);	
		
		/*ob_end_clean();
		include("../../view/export/mpdf/mpdf.php");*/
		ob_end_clean();
		include("mpdf/mpdf.php");
		
		
		$mpdf=new mPDF('','A4','0','calibri','10','10','48','5');
		$mpdf->SetImportUse();
		$mpdf->defaultheaderfontsize = 10; /* in pts */
		$mpdf->defaultheaderfontstyle = B; /* blank, B, I, or BI */
		$mpdf->defaultheaderline = 1; /* 1 to include line below header/above footer */
		$mpdf->defaultfooterfontsize = 10; /* in pts */
		$mpdf->defaultfooterfontstyle = B; /* blank, B, I, or BI */
		$mpdf->defaultfooterline = 1; /* 1 to include line below header/above footer */
		
		$mpdf->showWatermarkImage = true;
		$mpdf->allow_charset_conversion= true;
		$mpdf->charset_in='UTF-8';
		$mpdf->showWatermarkText = true;
		$html = mb_convert_encoding($html, 'UTF-8', 'UTF-8');
		
		$mpdf->SetHTMLHeader($header);
		$mpdf->SetHTMLFooter($footer);
		//$mpdf->SetWatermarkText();
		//$mpdf->showWatermarkText = true;
		$mpdf->AddPage();
		$mpdf->WriteHTML($html);
			
		$mpdf->Output();
		//$mpdf->Output('../../view/upload/invoice_mail_file/'.$file_name,'f');
		ob_clean();
		return $file_name;
		
	}//pdf creation end
}//function end	

?>
