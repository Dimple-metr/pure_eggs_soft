<?php
//you can print text,image, barcode and QR code by sending request from your website. You just need to send data in JSON format
	error_reporting(0);
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
	
//$i="demo";
$a = array();
$obj6->type = 0;//text
$obj6->content = $set_head['company_name'];//multiple lines text
$obj6->bold = 0;
$obj6->align = 0;
array_push($a,$obj6);
$address=text_rnremove($set_head['address']);
$address=text_divremove($address);
$obj7->type = 0;//text
$obj7->content = $address;//multiple lines text
$obj7->bold = 0;
$obj7->align = 0;

array_push($a,$obj7);
$obj8->type = 0;//text
$obj8->content = 'COMPANY GST No.:'.$set_head['vatno'];//multiple lines text
$obj8->bold = 2;
$obj8->align = 2;
array_push($a,$obj8);

echo json_encode($a,JSON_FORCE_OBJECT); 
file_put_contents('myfile1.json', $json_data);
//Note that same sequence will be used for printing data
?>
