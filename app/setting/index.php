<?php

session_start(); //start session
$AJAX = true;
include("../../config/config.php");
//error_reporting(E_ALL);
include("../../config/session.php");
include("../../include/function_database_query.php");

//print_r($_POST);
if($_POST != NULL) {
	$POST = bulk_filter($dbcon,$_POST);
}
else {
	$POST = bulk_filter($dbcon,$_GET);
}
if(strtolower($POST['mode']) == "edit") {
    		
		$infousr['user_name'] =	$info['company_name']= $POST['company_name'];
		$infousr['user_address'] = $info['address']	= stripslashes($_POST['address']);	
		$info['contact_no']	= $POST['contact_no'];
		$info['website']	= $_POST['website'];
		$info['company_website']	= $_POST['company_website'];
		$info['bank_name']	= $POST['bank_name'];
		$info['ac_no']		= $POST['ac_no'];
		$info['ifcs']		= $POST['ifcs'];
		$info['branch_name']    = $POST['branch_name'];
		$info['vatno']		= strtoupper($POST['gstno']);
		$info['iec_no']		= strtoupper($POST['iec_no']);
		$info['lut_no']		= strtoupper($POST['lut_no']);
		$filter_valid_till_date 	= explode(" - ",$POST['valid_till_date']);
		$info['valid_till_date_start']= date('Y-m-d',strtotime($filter_valid_till_date[0]));
		$info['valid_till_date_end']= date('Y-m-d',strtotime($filter_valid_till_date[1]));
		$info['pan_no']		= $POST['pan_no'];
		$info['stateid']	= $POST['stateid'];
		/*$info['vat_date']	= date('Y-m-d',strtotime($POST['vat_date']));
			$info['cstno']		= $POST['cstno'];
		$info['cst_date']	= date('Y-m-d',strtotime($POST['cst_date']));*/
		$info['serno']		= $POST['serno'];
		$info['ser_date']	= date('Y-m-d',strtotime($POST['ser_date']));
		$info['pan_no']		= $POST['pan_no'];
		$info['quot_condition']		= $_POST['quot_condition'];
		$info['coverlator_content']	= $_POST['coverlator_content'];
		$info['quot_content']		= $_POST['quot_email_content'];
                $info['inventory_management']	= $_POST['inventory_management'];
		
		if(!empty($_FILES['logo']['tmp_name'])) {
			$q="select * from tbl_company where company_id=".$POST['eid'];
			$row=mysqli_fetch_assoc($dbcon->query($q));
			$file=$row['logo'];
			unlink(LOGO_A.$file);
			unlink(LOGO_A."thumb//".$file);
			$info['logo']	= upload_image($_FILES);
		}
		if(!empty($_FILES['f_logo']['tmp_name'])) {
			$q="select * from tbl_company where company_id=".$POST['eid'];
			$row=mysqli_fetch_assoc($dbcon->query($q));
			$file=$row['f_logo'];
			unlink(LOGO_A.$file);
			unlink(LOGO_A."thumb//".$file);
			$info['f_logo']	= upload_image1($_FILES);
		}
		$info['perfoma_condition']			= stripslashes($_POST['export_condition']);
		$info['conditions']			= stripslashes($_POST['condition']);
		$info['challan_condition']	= stripslashes($_POST['challan_condition']);
		$info['quot_subject']	= stripslashes($_POST['quot_subject']);
		$info['po_condition']		= $_POST['po_condition'];
		$info['invoice_tax_content']	= $POST['invoice_tax_content'];
		$info['logo_content']		= $_POST['logo_content'];
		$info['dispatch_head_content']		= $_POST['dispatch_head_content'];
		$info['dispatch_footer_content']	= $_POST['dispatch_footer_content'];
		$info['lead_email_content']		= $_POST['lead_email_content'];
		$info['inquiry_email_content']	= $_POST['inquiry_email_content'];
		$info['signature']	= $_POST['signature'];
		$info['cdate']		= date("Y-m-d H:i:s");
		$info['user_id']	= $_SESSION['user_id'];		
		//var_dump($info);
		//exit();	
		$updateid=update_record('tbl_company', $info,"company_id=".$POST['eid'] , $dbcon);
		//	$infousr['user_rid']  = $inserid;
		
		$infousr['user_company'] 	= $POST['company_name']; 
		
		$updateuserid=update_record('users', $infousr,"user_type=2 and user_rid=".$POST['eid'] , $dbcon);
		
		
		if($updateid)
			echo "update";
		else
			echo "0".$dbcon->error;
		
	}
	

function upload_image($FILES)
{
	$rand=rand(0,9999);
	if(!empty($FILES['logo']['tmp_name'])) {
		list($width, $height, $type, $attr) = getimagesize($FILES['logo']['tmp_name']);
		if (isset($type) && in_array($type, array(IMAGETYPE_PNG, IMAGETYPE_JPEG, IMAGETYPE_GIF))) {
			$allowedExts = array("gif", "jpeg", "jpg", "png");
			$temp = explode(".", $FILES["logo"]["name"]);
			$extension = strtolower(end($temp));
			if (in_array($extension, $allowedExts)) {
				$File = "header".$rand.".jpg";
				$tmp_name = $FILES["logo"]["tmp_name"];
				move_uploaded_file($tmp_name,LOGO_A.$File);
				smart_resize_image(LOGO_A.$File,792,100);
			}
		}
		return  $File;				
	}
	
}
function upload_image1($FILES)
{
	$rand=rand(0,9999);
	if(!empty($FILES['f_logo']['tmp_name'])) {
		list($width, $height, $type, $attr) = getimagesize($FILES['f_logo']['tmp_name']);
		if (isset($type) && in_array($type, array(IMAGETYPE_PNG, IMAGETYPE_JPEG, IMAGETYPE_GIF))) {
			$allowedExts = array("gif", "jpeg", "jpg", "png");
			$temp = explode(".", $FILES["f_logo"]["name"]);
			$extension = strtolower(end($temp));
			if (in_array($extension, $allowedExts)) {
				$File = "footer".$rand.".jpg";
				$tmp_name = $FILES["f_logo"]["tmp_name"];
				move_uploaded_file($tmp_name,LOGO_A.$File);
				smart_resize_image(LOGO_A.$File,792,80);
			}
		}
		return  $File;				
	}	
}

?>
