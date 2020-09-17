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
			
		$whr='';
		if($POST['filter_country_id']){
			$whr.=' and country.countryid='.$POST['filter_country_id'];
		}
		if($POST['filter_state_id']){
			$whr.=' and smst.stateid='.$POST['filter_state_id'];
		}
		
		$appData = array();
		$i=1;
		$aColumns = array('cityid', 'city_initial', 'city_name', 'state_name',  'country_name', 'city_status', 'cmst.userid');
		$sIndexColumn = "cityid";
		$isWhere = array("city_status = 0 ".$whr);
		$sTable = "city_mst as cmst";			
		$isJOIN = array("left join state_mst as smst on cmst.stateid=smst.stateid","left join country_mst as country on country.countryid=smst.countryid");
		$hOrder = "cmst.city_name ASC";
		include('../../include/pagging.php');
		$appData = array();
		$id=1;
		foreach($sqlReturn as $row) {
			$row_data = array();
			$row_data[] = $row['sr'];
			$row_data[] = $row['city_initial'];
			$row_data[] = $row['city_name'];
			$row_data[] = $row['state_name'];
			$row_data[] = $row['country_name'];
			
			$edit_btn='';$delete_btn='';
			if($edit_btn_per){
				$edit_btn='<button class="btn btn-xs btn-warning" data-original-title="Edit" data-toggle="tooltip" data-placement="top" onClick="edit_test('.$row['cityid'].');"><i class="fa fa-pencil"></i></button>';
			}
			if($delete_btn_per){
				$delete_btn='<button class="btn btn-xs btn-danger" data-original-title="Delete" data-toggle="tooltip" data-placement="top" onClick="delete_catalog('.$row['cityid'].')"><i class="fa fa-trash-o"></i></button>';
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
		$tr = $dbcon -> query("SELECT `cityid`,`city_name`,`city_status` FROM `city_mst` WHERE `city_name` = '$POST[city_name]'");
		if($tr->num_rows > 0) {
			$r = $tr -> fetch_assoc();
			if($r['city_status'] != 0) {
				$info['city_status']=0;
				$updateid=update_record('city_mst', $info,"cityid=".$r['cityid'] , $dbcon);
				if($updateid) {
					$row['res']='1';
				}
				else {
					$row['res']='0';
				}
			}
			else {
				$row['res']='-1';
			}	
		}
		else {
			$info['city_initial']	= $POST['city_initial'];
			$info['city_name']	= $POST['city_name'];
			$info['stateid']	= $POST['stateid'];
			$info['cdate']		= date("Y-m-d H:i:s");
			$info['userid']		= $_SESSION['user_id'];
			$inserid=add_record('city_mst', $info, $dbcon);
			
			if($inserid) {
				if(strtolower($POST['city_model'])=="city_model") {
					$query="select * from city_mst where cityid=".$inserid;
					$rel=mysqli_fetch_assoc($dbcon->query($query));		
					$row = $rel;
					$row['res']="2"; 
				}
				else {
					$row['res'] ="1";
				}
			}
			else {
				$row['res'] ="0";
			}
		}
		echo json_encode($row);
		
	}
	else if(strtolower($POST['mode']) == "preedit") {			
		$q = $dbcon -> query("SELECT * FROM `city_mst` WHERE `cityid` = '$POST[id]'");
		$r = $q->fetch_assoc();
		echo json_encode($r);
	}
	else if(strtolower($POST['mode']) == "edit") {
		$info['stateid']= $POST['stateid'];
		$info['city_initial']	= $POST['city_initial'];
		$info['city_name']		= $POST['city_name'];
		$info['cdate']			= date("Y-m-d H:i:s");				
		$updateid=update_record('city_mst', $info,"cityid=".$POST['eid'] , $dbcon);
		 
		if($updateid)
			echo "1";
		else
			echo "0".$dbcon->error;
		
	}
	else if(strtolower($POST['mode']) == "delete") {
		$info['city_status']='2';
		$updateid=update_record('city_mst', $info,"cityid=".$POST['eid'] , $dbcon);
		
		if($updateid)
			echo "1";
		else
			echo "0";
		
	}
?>