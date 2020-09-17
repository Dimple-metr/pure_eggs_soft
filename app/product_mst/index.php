<?php
session_start(); //start session
$AJAX = true;
include("../../config/config.php");
//error_reporting(E_ALL);
include("../../config/session.php");
include("../../include/function_database_query.php");
include_once("../../include/common_functions.php");

//print_r($_POST);
if($_POST != NULL) {
	$POST = bulk_filter($dbcon,$_POST);
}
else {
	$POST = bulk_filter($dbcon,$_GET);
}
	
	if(strtolower($POST['mode']) == "fetch") {
		$edit_btn_per=check_permission($_SESSION['page'],$_SESSION['user_type'],'edit',$dbcon);
		$delete_btn_per=check_permission($_SESSION['page'],$_SESSION['user_type'],'delete',$dbcon);
			
		$appData = array();
		$i=1;
		$aColumns = array('product_id','product_name','item_code','product_code','product_mst_rate','product_stock','mrp','minimum_stock','product_purchase_mst_rate','cdate', 'product_status', 'imst.user_id');
		$sIndexColumn = "product_id";
		$isWhere = array("product_status = 0 and product_type in(0,1,2,3) and imst.company_id in (0,$_SESSION[company_id])");
		$sTable = "tbl_product as imst";			
		$isJOIN = array();
		$hOrder = "imst.product_id desc";
		include('../../include/pagging.php');
		$appData = array();
		$id=1;
		foreach($sqlReturn as $row) {
			$row_data = array();
			$row_data[] = $row['sr'];
				$row_data[] = $row['product_name'];
				if(!empty($row['item_code'])){ 
				$row_data[] = $row['item_code'];
				}else{
					$row_data[] = '-';
				}
				if(!empty($row['product_code'])){ 
				$row_data[] = $row['product_code'];
				}else{
					$row_data[] = '-';
				}
				$row_data[] = $row['product_purchase_mst_rate'];
				$row_data[] = $row['product_mst_rate'];
				$row_data[] = $row['mrp'];
				$row_data[] = $row['minimum_stock'];
				$row_data[] = $row['product_stock'];
				
			
			
			$edit_btn='';$delete_btn='';
			if($edit_btn_per){
				$edit_btn='<button class="btn btn-xs btn-warning" data-original-title="Edit" data-toggle="tooltip" data-placement="top" onClick="edit_group('.$row['product_id'].');"><i class="fa fa-pencil"></i></button>';
			}
			if($rel['group_name']==""){
				if($delete_btn_per){
					$delete_btn='<button class="btn btn-xs btn-danger" data-original-title="Delete" data-toggle="tooltip" data-placement="top" onClick="delete_unit('.$row['product_id'].')"><i class="fa fa-trash-o"></i></button>';
				}
			}
			$row_data[] = $edit_btn.' '.$delete_btn; 
			$appData[] = $row_data;
			$id++;
		}
		$output['aaData'] = $appData;
		echo json_encode( $output );
	}
	else if(strtolower($POST['mode']) == "add") {
		
		$tr = $dbcon -> query("SELECT `product_id`,`product_name`,`product_status` FROM `tbl_product` WHERE `product_name` ='".$POST['product_name']."'");
		
				if($tr->num_rows > 0) {
					$r = $tr -> fetch_assoc();
					if($r['product_status'] != 0) {
						$info['product_status']=0;
						$updateid1=update_record('tbl_product', $info,"product_id=".$r['product_id'] , $dbcon);						
						if($updateid1){
							$arr['msg']="1";
						}else{
							$arr['msg']="0";
						}
					}
					else {
						$arr['msg']="-1";
					}
				}
		else {
			if($_POST['proType']=="service"){
			$info['product_type']		= 3;
					}else{
				$info['product_type']		= $POST['product_type'];	
					}
			$info['product_name']				= stripslashes($POST['product_name']);
			$info['product_des']				= stripslashes(text_rnremove($_POST['productdes']));
			$info['ledger_id']					= $POST['ledger_id'];
			$info['product_mst_rate']			= $POST['product_mst_rate'];
			$info['product_purchase_mst_rate']	= $POST['product_purchase_mst_rate'];
			$info['item_code']					= $POST['item_code'];
			$info['product_code']				= $POST['product_code'];
			$info['product_mst_unitid']			= $POST['product_mst_unitid'];
			$info['intra_tax']					= $POST['intra_tax'];
			$info['inter_tax']					= $POST['inter_tax'];
			$info['product_stock']				= $POST['opening_stock'];
			$info['minimum_stock']				= $POST['minimum_stock'];
			$info['catagory_id']				= $POST['catagory_id'];
			$info['mrp']						= $POST['mrp'];
			$info['cdate']						= date("Y-m-d H:i:s");
			$info['user_id']					= $_SESSION['user_id'];
			$info['company_id']					= $_SESSION['company_id'];
			
		$inserid=add_record('tbl_product', $info, $dbcon);
			if($inserid){
				$arr['msg']="1";	
			}else if($updateid){
				$arr['msg']="update";
			}
			else{
				$arr['msg']="0";
			}
		}
		echo json_encode($arr);
	}
	else if(strtolower($POST['mode']) == "edit") {
		
		$tr = $dbcon -> query("SELECT `product_id`,`product_name`,`product_status` FROM `tbl_product` WHERE product_id!=".$POST['edit_id']." and `product_name` ='".$POST['product_name']."'");
		
				if($tr->num_rows > 0) {
					$r = $tr -> fetch_assoc();
					if($r['product_status'] != 0) {
						$info['product_status']=0;
						$updateid1=update_record('tbl_product', $info,"product_id=".$r['product_id'] , $dbcon);						
						if($updateid1){
							$arr['msg']="1";
						}else{
							$arr['msg']="0";
						}
					}
					else {
						$arr['msg']="-1";
					}
				}
		else {
			if($_POST['proType']=="service"){
				$info['product_type']		= 3;
					}else{
				$info['product_type']		= $POST['product_type'];	
					}
			$info['product_name']				= stripslashes($POST['product_name']);
			$info['product_des']				= stripslashes(text_rnremove($_POST['productdes']));
			$info['ledger_id']					= $POST['ledger_id'];
			$info['product_mst_rate']			= $POST['product_mst_rate'];
			$info['product_purchase_mst_rate']	= $POST['product_purchase_mst_rate'];
			$info['item_code']					= $POST['item_code'];
			$info['product_code']				= $POST['product_code'];
			$info['product_mst_unitid']			= $POST['product_mst_unitid'];
			$info['intra_tax']					= $POST['intra_tax'];
			$info['inter_tax']					= $POST['inter_tax'];
			$info['product_stock']				= $POST['opening_stock'];
			$info['minimum_stock']				= $POST['minimum_stock'];
			$info['catagory_id']				= $POST['catagory_id'];
			$info['mrp']						= $POST['mrp'];
			$info['cdate']						= date("Y-m-d H:i:s");
			$info['user_id']					= $_SESSION['user_id'];
			$info['company_id']					= $_SESSION['company_id'];
			
				$updateid=update_record('tbl_product', $info,"product_id=".$POST['edit_id'] , $dbcon);
			
			if($updateid){
				$arr['msg']="update";
			}
			else{
				$arr['msg']="0";
			}
		}
		echo json_encode($arr);
	}
	else if(strtolower($POST['mode']) == "preedit") {			
		$q = $dbcon -> query("SELECT * FROM `tbl_product` WHERE `product_id` = '$POST[id]'");
		$r = $q->fetch_assoc();
		echo json_encode($r);
	}
	else if(strtolower($POST['mode']) == "delete") {
		$info['product_status']='2';
		$updateid=update_record('tbl_product', $info,"product_id=".$POST['eid'] , $dbcon);
		
		if($updateid)
		echo "1";
		else
		echo "0";
		
	}
	
	
?>