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
		$aColumns = array('fmst.category_name','undermst.category_name as under_name','fmst.cdate', 'fmst.category_status', 'fmst.user_id','fmst.category_id');
		$sIndexColumn = "fmst.category_id";
		$isWhere = array("fmst.category_status = 0");
		$sTable = "category_mst as fmst";			
		$isJOIN = array('left join category_mst as undermst on undermst.category_id=fmst.main_category_id');
		$hOrder = "fmst.category_id desc";
		include('../../include/pagging.php');
		$appData = array();
		$id=1;
		foreach($sqlReturn as $row) {
			$row_data = array();
			$query="select category_name from category_mst where category_status = 0 and main_category_id=".$row['category_id'];
			$rel=mysqli_fetch_assoc($dbcon->query($query));
			if($row['under_name']=="")
			{
				$undername="PRIMARY CATEGORY";
			}else
			{
				$undername=$row['under_name'];
			}
			
			$row_data[] = $row['sr'];
			$row_data[] = $row['category_name'];
			$row_data[] = $undername;
			
			
			$edit_btn='';$delete_btn='';
			if($edit_btn_per){
				$edit_btn='<button class="btn btn-xs btn-warning" data-original-title="Edit" data-toggle="tooltip" data-placement="top" onClick="edit_group('.$row['category_id'].');"><i class="fa fa-pencil"></i></button>';
			}
			
		
			if($rel['category_name']==""){
				if($delete_btn_per){
					$delete_btn='<button class="btn btn-xs btn-danger" data-original-title="Delete" data-toggle="tooltip" data-placement="top" onClick="delete_unit('.$row['category_id'].')"><i class="fa fa-trash-o"></i></button>';
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
		
		$tr = $dbcon -> query("SELECT `category_id`,`category_name`,`category_status` FROM `category_mst` WHERE `category_name` ='".$POST['category_name']."'");
		
				if($tr->num_rows > 0) {
					$r = $tr -> fetch_assoc();
					if($r['category_status'] != 0) {
						$info['category_status']=0;
						$updateid1=update_record('category_mst', $info,"category_id=".$r['category_id'] , $dbcon);						
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
			$info['category_name']		= $POST['category_name'];							
			$info['main_category_id']		= $POST['main_category_id'];							
			$info['cdate']			= date("Y-m-d H:i:s");
			$info['user_id']		= $_SESSION['user_id'];
			$info['usertype_id']	= $_SESSION['user_type'];
			$info['company_id']		= $_SESSION['company_id'];
			if($POST['edit_id']==""){
				$inserid=add_record('category_mst', $info, $dbcon);
			}else{
				$updateid=update_record('category_mst', $info,"category_id=".$POST['edit_id'] , $dbcon);
			}
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
		
		$tr = $dbcon -> query("SELECT `category_id`,`category_name`,`category_status` FROM `category_mst` WHERE category_id!=".$POST['edit_id']." and `category_name` ='".$POST['category_name']."'");
		
				if($tr->num_rows > 0) {
					$r = $tr -> fetch_assoc();
					if($r['category_status'] != 0) {
						$info['category_status']=0;
						$updateid1=update_record('category_mst', $info,"category_id=".$r['category_id'] , $dbcon);						
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
			$info['category_name']		= $POST['category_name'];							
			$info['main_category_id']		= $POST['main_category_id'];							
			$info['cdate']			= date("Y-m-d H:i:s");
			$info['user_id']		= $_SESSION['user_id'];
			$info['usertype_id']	= $_SESSION['user_type'];
			$info['company_id']		= $_SESSION['company_id'];
			
				$updateid=update_record('category_mst', $info,"category_id=".$POST['edit_id'] , $dbcon);
			
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
		$q = $dbcon -> query("SELECT * FROM `category_mst` WHERE `category_id` = '$POST[id]'");
		$r = $q->fetch_assoc();
		echo json_encode($r);
	}
	else if(strtolower($POST['mode']) == "delete") {
		$info['category_status']='2';
		$updateid=update_record('category_mst', $info,"category_id=".$POST['eid'] , $dbcon);
		
		if($updateid)
		echo "1";
		else
		echo "0";
		
	}
	
	
?>