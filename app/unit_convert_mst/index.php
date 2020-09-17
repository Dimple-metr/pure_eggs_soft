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
		$aColumns = array('munt.unit_name as main_unit','fmst.unit_convert_qty','cunt.unit_name as convert_unit','fmst.cdate', 'fmst.unit_convert_status', 'fmst.user_id','fmst.unit_convert_id');
		$sIndexColumn = "fmst.unit_convert_id";
		$isWhere = array("fmst.unit_convert_status = 0 and fmst.company_id=".$_SESSION['company_id']);
		$sTable = "unit_convert_mst as fmst";			
		$isJOIN = array('left join unit_mst as munt on munt.unitid=fmst.unit_id','left join unit_mst as cunt on cunt.unitid=fmst.new_unit_convert_id');
		$hOrder = "fmst.unit_convert_id desc";
		include('../../include/pagging.php');
		$appData = array();
		$id=1;
		foreach($sqlReturn as $row) {
			$row_data = array();
			$row_data[] = $row['sr'];
			$row_data[] = $row['main_unit'];
			$row_data[] = $row['unit_convert_qty'];
			$row_data[] = $row['convert_unit'];
			$row_data[] = "1".$row['main_unit']."=".$row['unit_convert_qty'].$row['convert_unit'];
			$edit_btn='';$delete_btn='';
			if($edit_btn_per){
				$edit_btn='<button class="btn btn-xs btn-warning" data-original-title="Edit" data-toggle="tooltip" data-placement="top" onClick="edit_unit_convert('.$row['unit_convert_id'].');"><i class="fa fa-pencil"></i></button>';
			}
			
			if($delete_btn_per){
				$delete_btn='<button class="btn btn-xs btn-danger" data-original-title="Delete" data-toggle="tooltip" data-placement="top" onClick="delete_unit_convert('.$row['unit_convert_id'].')"><i class="fa fa-trash-o"></i></button>';
			}
			
			$row_data[] = $edit_btn.' '.$delete_btn; 
			$appData[] = $row_data;
			$id++;
		}
		$output['aaData'] = $appData;
		echo json_encode( $output );
	}
	else if(strtolower($POST['mode']) == "add") {
		
		$tr = $dbcon -> query("SELECT `unit_convert_id`,`unit_id`,`unit_convert_status` FROM `unit_convert_mst` WHERE `unit_id` ='".$POST['unit_id']."' and new_unit_convert_id='".$POST['unit_id']."' and company_id=".$_SESSION['company_id']);
		
				if($tr->num_rows > 0) {
					$r = $tr -> fetch_assoc();
					if($r['unit_convert_status'] != 0) {
						$info['unit_convert_status']=0;
						$updateid1=update_record('unit_convert_mst', $info,"unit_convert_id=".$r['unit_convert_id'] , $dbcon);						
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
			$info['unit_id']		= $POST['unit_id'];							
			$info['new_unit_convert_id']	= $POST['new_unit_convert_id'];							
			$info['unit_convert_qty']	= $POST['unit_convert_qty'];							
			$info['cdate']			= date("Y-m-d H:i:s");
			$info['user_id']		= $_SESSION['user_id'];
			$info['usertype_id']	= $_SESSION['user_type'];
			$info['company_id']		= $_SESSION['company_id'];
			
		$inserid=add_record('unit_convert_mst', $info, $dbcon);
			
			if($inserid){
				$arr['msg']="1";	
			}
			else{
				$arr['msg']="0";
			}
		}
		echo json_encode($arr);
	}
	else if(strtolower($POST['mode']) == "edit") {
		
		$tr = $dbcon -> query("SELECT `unit_convert_id`,`unit_id`,`unit_convert_status` FROM `unit_convert_mst` WHERE unit_convert_id!=".$POST['edit_id']." and `unit_id` ='".$POST['unit_id']."' and new_unit_convert_id='".$POST['new_unit_convert_id']."' and company_id=".$_SESSION['company_id']);
		
				if($tr->num_rows > 0) {
					$r = $tr -> fetch_assoc();
					if($r['unit_convert_status'] != 0) {
						$info['unit_convert_status']=0;
						$updateid1=update_record('unit_convert_mst', $info,"unit_convert_id=".$r['unit_convert_id'] , $dbcon);						
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
			$info['unit_id']		= $POST['unit_id'];							
			$info['new_unit_convert_id']	= $POST['new_unit_convert_id'];							
			$info['unit_convert_qty']	= $POST['unit_convert_qty'];							
			$info['cdate']			= date("Y-m-d H:i:s");
			$info['user_id']		= $_SESSION['user_id'];
			$info['usertype_id']	= $_SESSION['user_type'];
			$info['company_id']		= $_SESSION['company_id'];
			
				$updateid=update_record('unit_convert_mst', $info,"unit_convert_id=".$POST['edit_id'] , $dbcon);
			
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
		$q = $dbcon -> query("SELECT * FROM `unit_convert_mst` WHERE `unit_convert_id` = '$POST[id]'");
		$r = $q->fetch_assoc();
		echo json_encode($r);
	}
	else if(strtolower($POST['mode']) == "delete") {
		$info['unit_convert_status']='2';
		$updateid=update_record('unit_convert_mst', $info,"unit_convert_id=".$POST['eid'] , $dbcon);
		
		if($updateid)
		echo "1";
		else
		echo "0";
		
	}
	else if(strtolower($POST['mode'])== "load_new_unit")
		{
				echo getunit($dbcon,$POST['new_unit_convert_id'],$POST['unit_id']);
		}
	
	
?>