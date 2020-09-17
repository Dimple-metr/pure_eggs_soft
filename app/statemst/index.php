<?php
session_start(); //start session
$AJAX = true;
include("../../config/config.php");
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
		$aColumns = array('stateid', 'country_name', 'state_initial', 'state_name','gst_state_code', 'state_status');
		$sIndexColumn = "stateid";
		$isWhere = array("state_status = 0");
		$sTable = "state_mst as state";
		$isJOIN = array("left join country_mst as country on country.countryid=state.countryid");
		$hOrder = "state.stateid desc";
		include('../../include/pagging.php');
		$appData = array();
		$id=1;
		foreach($sqlReturn as $row){
			$row_data = array();
			$row_data[] = $row['sr'];
			$row_data[] = $row['country_name'];
			$row_data[] = $row['state_initial'];
			$row_data[] = $row['state_name'];
			$row_data[] = $row['gst_state_code'];
			
			$edit_btn='';$delete_btn='';
			if($edit_btn_per){
				$edit_btn='<button class="btn btn-xs btn-warning" data-original-title="Edit" data-toggle="tooltip" data-placement="top" onClick="edit_test('.$row['stateid'].');"><i class="fa fa-pencil"></i></button>';
			}
			if($delete_btn_per){
				$delete_btn='<button class="btn btn-xs btn-danger" data-original-title="Delete" data-toggle="tooltip" data-placement="top" onClick="delete_state('.$row['stateid'].')"><i class="fa fa-trash-o"></i></button>';
			}
			
			$row_data[] = $edit_btn.' '.$delete_btn;
			
			$appData[] = $row_data;
			$id++;
		}
		$output['aaData'] = $appData;
		echo json_encode( $output );
	}
	else if(strtolower($POST['mode']) == "add") {
		
		$row['res']='';
		
		$tr = $dbcon -> query("SELECT `stateid`,`state_name`,`state_status` FROM `state_mst` WHERE `state_name` = '$POST[state_name]' and `countryid` = '$POST[countryid]'");
		if($tr->num_rows > 0) {
			$r = $tr -> fetch_assoc();
			if($r['state_status'] != 0) {
				$info['state_status']=0;
				$updateid=update_record('state_mst', $info,"stateid=".$r['stateid'] , $dbcon);						
				if($updateid){
					$row['res']='1';
				}
				else{
					$row['res']='0';
				}
			}
			else {
				$row['res']='-1';
			}
		}
		else {
			$info['countryid']		= $POST['countryid'];
			$info['state_name']		= $POST['state_name'];
			$info['state_initial']		= $POST['state_initial'];
			$info['gst_state_code']	= $POST['gst_state_code'];							
			$info['cdate']			= date("Y-m-d H:i:s");
			$info['user_id']		= $_SESSION['user_id'];
			$info['usertype_id']	= $_SESSION['user_type'];
			$inserid=add_record('state_mst', $info, $dbcon);
			
			if($inserid){
				if(strtolower($POST['state_model'])=="state_model"){
					$query="select * from state_mst where stateid=".$inserid;
					$rel=mysqli_fetch_assoc($dbcon->query($query));		
					$row = $rel;
					$row['res']="2"; 
				}
				else{
					$row['res'] ="1";
				}
			}
			else{
				$row['res'] ="0";
			}
		}
		echo json_encode($row);
		
	}
	else if(strtolower($POST['mode']) == "preedit") {			
		$q = $dbcon -> query("SELECT * FROM `state_mst` WHERE `stateid` = '$POST[id]' ");
		$r = $q->fetch_assoc();
		echo json_encode($r);
	}
	else if(strtolower($POST['mode']) == "edit") {
		$info['countryid']		= $POST['countryid'];
		$info['state_name']		= $POST['state_name'];
		$info['state_initial']		= $POST['state_initial'];
		$info['gst_state_code']	= $POST['gst_state_code'];
		$info['cdate']			= date("Y-m-d H:i:s");
		$info['user_id']		= $_SESSION['user_id'];
		$info['usertype_id']	= $_SESSION['user_type'];
		$updateid=update_record('state_mst', $info,"stateid=".$POST['eid'] , $dbcon);
		
		if($updateid)
			echo "1";
		else
			echo "0".$dbcon->error;
		
	}
	else if(strtolower($POST['mode']) == "delete") {
		$info['state_status']='2';
		$updateid=update_record('state_mst', $info,"stateid=".$POST['eid'] , $dbcon);
		
		if($updateid)
			echo "1";
		else
			echo "0";
	}
?>		