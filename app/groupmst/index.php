<?php
session_start(); //start session
$AJAX = true;
include("../../config/config.php");
//error_reporting(E_ALL);
include("../../config/session.php");
include("../../include/function_database_query.php");
include("../../include/common_functions.php");
//if(@isset($_SERVER['HTTP_X_REQUESTED_WITH']) && $_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest') 
{ 
  //  if(@isset($_SERVER['HTTP_REFERER']) && strpos($_SERVER['HTTP_REFERER'],DOMAIN) !== false) 
	{
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
			$aColumns = array('g_id', 'g_name','g_pid', 'g_status','user_id','is_deletable');
			$sIndexColumn = "g_id";
			$isWhere = array("g_status = 0");
			$sTable = "tbl_group";			
			$isJOIN = array();
			$hOrder = "g_id desc";
			include('../../include/pagging.php');
			$appData = array();
			$id=1;
			foreach($sqlReturn as $row) {
				$row_data = array();
				$row_data[] = $row['sr'];
				$row_data[] = $row['g_name'];
				$row_data[] = get_grp_by_id($dbcon,$row['g_pid']);
				
				$edit_btn=''; $delete_btn='';  
				if($edit_btn_per){ 
					$edit_btn=' <button class="btn btn-xs btn-warning" data-original-title="Edit" data-toggle="tooltip" data-placement="top" onClick="edit_group('.$row['g_id'].');"><i class="fa fa-pencil"></i></button>'; 
				}
				if($delete_btn_per && $row['is_deletable']=='0'){
					$delete_btn=' <button class="btn btn-xs btn-danger" data-original-title="Delete" data-toggle="tooltip" data-placement="top" onClick="delete_category('.$row['g_id'].')"><i class="fa fa-trash-o"></i></button>'; 
				}
				$row_data[] = $edit_btn.' '.$delete_btn; 
				$appData[] = $row_data;
				$id++;
			}
			$output['aaData'] = $appData;
			echo json_encode( $output );
		}
		else if(strtolower($POST['mode']) == "add" || strtolower($POST['mode']) == "add_model") {
			
			$tr = $dbcon -> query("SELECT `g_id`,`g_name`,`g_status` FROM `tbl_group` WHERE `g_name` ='".$POST['g_name']."'");
			if($tr->num_rows > 0) {
				
				$resp['msg'] = "-1";
				
			}
			else {
						$info['g_name']	= $POST['g_name'];							
						$info['g_pid']	= $POST['g_parent'];							
						$info['g_open_balance']	= $POST['g_opening'];							
						$info['form_id']	= $_POST['g_form'];							
						$info['cdate']		= date("Y-m-d H:i:s");
						$info['user_id']	= $_SESSION['user_id'];
						$info['company_id']	= $_SESSION['company_id'];
						$inserid=add_record('tbl_group', $info, $dbcon);
				if($inserid)
				{
					if(strtolower($POST['mode']) == "add")
					{
						$resp['msg'] = "1";
					}
					else
					{
						$zone_qry="select * from tbl_group where g_id=".$inserid; 
						$zone_rel=mysqli_fetch_assoc($dbcon->query($zone_qry));
						$resp=$zone_rel;
						$resp['msg'] = "2";
					}
				}
				else
				{
					$resp['msg'] = "0";
				}
			}
			
			echo json_encode($resp);
		
		}
		else if(strtolower($POST['mode']) == "preedit") {			
			$q = $dbcon -> query("SELECT * FROM `tbl_group` WHERE `g_id` = '$POST[id]'");
			$r = $q->fetch_assoc();
			echo json_encode($r);
		}
		else if(strtolower($POST['mode']) == "edit") {
			
				$info['g_name']	= $POST['e_g_name'];							
				$info['g_pid']	= $POST['e_g_parent'];							
				$info['g_open_balance']	= $POST['e_g_opening'];							
				$info['cdate']		= date("Y-m-d H:i:s");
				$info['user_id']	= $_SESSION['user_id'];
				$info['company_id']	= $_SESSION['company_id'];
				$updateid=update_record('tbl_group', $info,"g_id=".$POST['eid'] , $dbcon);
			if($updateid)
				echo "1";
			else
				echo "0".$dbcon->error;
			
		}
		else if(strtolower($POST['mode']) == "delete") {
			
				$info['g_status']='2';
				$updateid=update_record('tbl_group', $info,"g_id=".$POST['eid'] , $dbcon);
				
				if($updateid)
					echo "1";
				else
					echo "0";
			
		}
		else if(strtolower($POST['mode']) == "get_group_dropdown_data") {
			echo get_all_group($dbcon,$POST['id']);
		}
		else if(strtolower($POST['mode']) == "get_form_type") {
			$gid=$POST['gid'];
			$q=$dbcon->query("select form_id from tbl_group where g_id='$gid'");
			$row=mysqli_fetch_array($q);
			echo $row['form_id'];
			//echo $gid;
		}
    }
  //  else {
    //    die("Error - 2");
    //}
}

//else {
   // die("Error - 1");
//}
?>