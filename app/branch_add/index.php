<?php
session_start();
$AJAX = true;
include("../../config/config.php");
//error_reporting(E_ALL);
include("../../config/session.php");
include("../../include/function_database_query.php");
include("../../include/common_functions.php");

//print_r($_POST);
//print_r($_FILES);
if($_POST != NULL) {
	$POST = bulk_filter($dbcon,$_POST);
}
else {
	$POST = bulk_filter($dbcon,$_GET);
}
	if(strtolower($POST['mode']) == "fetch") {
		$appData = array();
		$i=1;
		$aColumns = array('bran.branch_id','branch_name','branch_email','state.state_name','city.city_name','branch_mobile','bran.company_id');
		$sIndexColumn = "bran.branch_id";
		$isWhere = array("bran.branch_status=0 and bran.company_id=".$_SESSION['company_id']);
		$sTable = "tbl_branch as bran";			
		$isJOIN = array('left join state_mst state on bran.stateid=state.stateid','left join city_mst city on bran.cityid=city.cityid');
		$hOrder = "bran.branch_id desc";
		include('../../include/pagging.php');
		$appData = array();
		$id=1;
		foreach($sqlReturn as $row) {
			$row_data = array();
			$row_data[] = $row['sr'];
			$row_data[] = $row['branch_name'];
			$row_data[] = $row['branch_email'];
			$row_data[] = $row['city_name'];
			$row_data[] = $row['state_name'];
			$row_data[] = $row['branch_mobile'];
			
			$edit_btn='<a class="btn btn-xs btn-warning" data-original-title="Edit" data-toggle="tooltip" data-placement="top" href="'.ROOT.'branchedit/'.$row['branch_id'].'"><i class="fa fa-pencil"></i></a>';
			
			$del_btn='<button class="btn btn-xs btn-danger" data-original-title="Delete" data-toggle="tooltip" data-placement="top" onClick="delete_branch('.$row['branch_id'].')"><i class="fa fa-trash-o"></i></button>';
			
			$row_data[] = $edit_btn.' '.$del_btn;
			$appData[] = $row_data;
			$id++;
		}
		$output['aaData'] = $appData;
		echo json_encode( $output );
	}
	else if(strtolower($POST['mode']) == "add") {
		
		$tr = $dbcon -> query("SELECT `branch_id`,`branch_email` FROM `tbl_branch` WHERE `branch_email` = '$POST[branch_email]'");
		if($tr->num_rows > 0) {
			$row['res']='-1';
			echo json_encode($row);
		}
		else {
			$info['countryid']			= $POST['countryid'];
			$info['stateid']			= $POST['stateid'];
			$info['cityid']				= $POST['cityid'];
			$info['branch_name']		= $POST['branch_name'];
			$info['branch_email']		= $POST['branch_email'];
			$info['branch_address']		= $POST['branch_address'];
			$info['branch_mobile']		= $POST['branch_mobile'];
			$info['branch_pincode']		= $POST['branch_pincode'];
			$info['cdate']				= date("Y-m-d H:i:s");
			$info['company_id']			= $_SESSION['company_id'];
			$inserid=add_record('tbl_branch', $info, $dbcon);
			
			$info1['branch_id']		= $inserid; 
			$info1['user_name']		= $POST['branch_name']; 
			$info1['user_mail']		= strtolower($POST['branch_email']);
			$info1['user_key']		= md5($_POST['password']);
			$info1['user_type']		= 3;
			$info1['user_stat']		= $POST['stateid'];
			$info1['user_city']		= $POST['cityid'];
			$info1['user_phone']	= $POST['branch_mobile'];
			$info1['user_country']	= $POST['countryid'];
			$info1['user_address']	= $POST['branch_address'];
			$info1['main_branch']	= 1;
			$info1['user_rid']		= $_SESSION['user_id'];
			$info1['company_id']	= $_SESSION['company_id'];
			$info1['payment_status'] = 1;
			$inserid1=add_record('users', $info1, $dbcon);
			
			$row['res']='';
			if($inserid) {
				$row['res'] ="1";
			}
			else {
				$row['res'] ="0";
			}
			echo json_encode($row);	
		}
		
	}		
	else if(strtolower($POST['mode']) == "edit") {
		
			$info['countryid']			= $POST['countryid'];
			$info['stateid']			= $POST['stateid'];
			$info['cityid']				= $POST['cityid'];
			$info['branch_name']		= $POST['branch_name'];
			$info['branch_email']		= $POST['branch_email'];
			$info['branch_address']		= $POST['branch_address'];
			$info['branch_mobile']		= $POST['branch_mobile'];
			$info['branch_pincode']		= $POST['branch_pincode'];
			$info['cdate']				= date("Y-m-d H:i:s");
			$info['company_id']			= $_SESSION['company_id'];
		$updateid=update_record('tbl_branch', $info,"branch_id=".$POST['eid'] , $dbcon);
		$row['res']='';
		if($updateid) {
			$row['res']='update';
		}
		else {
			$row['res']='0';
		}
		//$row['res']='update';
		echo json_encode($row);
		
	}
	else if(strtolower($POST['mode']) == "delete") {
		$info['branch_status']		= 2;
		$updateid=update_record('tbl_branch', $info,"branch_id=".$POST['eid'] , $dbcon);				
		
		if($updateid){
			echo "1";	
		}else{
			echo "0";
		}
	}
	else if(strtolower($POST['mode']) == "load_state") {
		//echo getstate($dbcon,0);	
		$countryid=$POST['id'];				
		echo get_state($dbcon,'',$countryid);
	}
	else if(strtolower($POST['mode']) == "load_city") {
		$cityid=$POST['id'];				
		echo $str=getcity($dbcon,$cityid,0);
	}
	
	
?>