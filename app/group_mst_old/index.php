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
		$aColumns = array('fmst.group_name','undermst.group_name as under_name','fmst.cdate', 'fmst.group_status', 'fmst.user_id','fmst.group_id');
		$sIndexColumn = "fmst.group_id";
		$isWhere = array("fmst.group_status = 0");
		$sTable = "group_mst as fmst";			
		$isJOIN = array('left join group_mst as undermst on undermst.group_id=fmst.under_id');
		$hOrder = "fmst.group_id desc";
		include('../../include/pagging.php');
		$appData = array();
		$id=1;
		foreach($sqlReturn as $row) {
			$row_data = array();
			if($row['under_name']=="")
			{
				$undername="PRIMARY GROUPS";
			}else
			{
				$undername=$row['under_name'];
			}
			
			$row_data[] = $row['sr'];
			$row_data[] = $row['group_name'];
			$row_data[] = $undername;
			
			
			$edit_btn='';$delete_btn='';
			if($edit_btn_per){
				$edit_btn='<button class="btn btn-xs btn-warning" data-original-title="Edit" data-toggle="tooltip" data-placement="top" onClick="edit_group('.$row['group_id'].');"><i class="fa fa-pencil"></i></button>';
			}
			$query="select group_name from group_mst where group_status = 0 and under_id=".$row['group_id'];
			$rel=mysqli_fetch_assoc($dbcon->query($query));
		
			if($rel['group_name']==""){
				if($delete_btn_per){
					$delete_btn='<button class="btn btn-xs btn-danger" data-original-title="Delete" data-toggle="tooltip" data-placement="top" onClick="delete_unit('.$row['group_id'].')"><i class="fa fa-trash-o"></i></button>';
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
		
		$tr = $dbcon -> query("SELECT `group_id`,`group_name`,`group_status` FROM `group_mst` WHERE `group_name` ='".$POST['group_name']."'");
		
				if($tr->num_rows > 0) {
					$r = $tr -> fetch_assoc();
					if($r['group_status'] != 0) {
						$info['group_status']=0;
						$updateid1=update_record('group_mst', $info,"group_id=".$r['group_id'] , $dbcon);						
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
			$info['group_name']		= $POST['group_name'];							
			$info['under_id']		= $POST['under_id'];							
			$info['cdate']			= date("Y-m-d H:i:s");
			$info['user_id']		= $_SESSION['user_id'];
			$info['usertype_id']	= $_SESSION['user_type'];
			$info['company_id']		= $_SESSION['company_id'];
			if($POST['edit_id']==""){
				$inserid=add_record('group_mst', $info, $dbcon);
			}else{
				$updateid=update_record('group_mst', $info,"group_id=".$POST['edit_id'] , $dbcon);
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
		
		$tr = $dbcon -> query("SELECT `group_id`,`group_name`,`group_status` FROM `group_mst` WHERE group_id!=".$POST['edit_id']." and `group_name` ='".$POST['group_name']."'");
		
				if($tr->num_rows > 0) {
					$r = $tr -> fetch_assoc();
					if($r['group_status'] != 0) {
						$info['group_status']=0;
						$updateid1=update_record('group_mst', $info,"group_id=".$r['group_id'] , $dbcon);						
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
			$info['group_name']		= $POST['group_name'];							
			$info['under_id']		= $POST['under_id'];							
			$info['cdate']			= date("Y-m-d H:i:s");
			$info['user_id']		= $_SESSION['user_id'];
			$info['usertype_id']	= $_SESSION['user_type'];
			$info['company_id']		= $_SESSION['company_id'];
			
				$updateid=update_record('group_mst', $info,"group_id=".$POST['edit_id'] , $dbcon);
			
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
		$q = $dbcon -> query("SELECT * FROM `group_mst` WHERE `group_id` = '$POST[id]'");
		$r = $q->fetch_assoc();
		echo json_encode($r);
	}
	else if(strtolower($POST['mode']) == "delete") {
		$info['group_status']='2';
		$updateid=update_record('group_mst', $info,"group_id=".$POST['eid'] , $dbcon);
		
		if($updateid)
		echo "1";
		else
		echo "0";
		
	}
	
	
?>