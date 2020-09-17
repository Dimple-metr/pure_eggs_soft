<?php
session_start(); //start session
$AJAX = true;
include("../../config/config.php");
include("../../config/session.php");
include("../../include/function_database_query.php");
//if(@isset($_SERVER['HTTP_X_REQUESTED_WITH']) && $_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest') 
{ 
    //if(@isset($_SERVER['HTTP_REFERER']) && strpos($_SERVER['HTTP_REFERER'],DOMAIN) !== false) 
	{
		//print_r($_POST);
		if($_POST != NULL) {
			$POST = bulk_filter($dbcon,$_POST);
		}
		else {
			$POST = bulk_filter($dbcon,$_GET);
		}
		
		if(strtolower($POST['mode']) == "fetch") {
			$appData = array();
			$i=1;
			$aColumns = array('usertype_id','usertype_name', 'status', 'cdate', 'user_id');
			$sIndexColumn = "usertype_id";
			$isWhere = array("status = 0 and company_id=".$_SESSION['company_id']);
			$sTable = "tbl_usertype as utype";			
			$isJOIN = array();
			$hOrder = "utype.usertype_id desc";
			include('../../include/pagging.php');
			$appData = array();
			$id=1;
			foreach($sqlReturn as $row) {
				$row_data = array();
				$row_data[] = $row['sr'];
				$row_data[] = $row['usertype_name'];
				if($row['user_id']==$_SESSION['user_id'])
				{
					$row_data[] = '<button class="btn btn-xs btn-warning" data-original-title="Edit" data-toggle="tooltip" data-placement="top" onClick="edit_test('.$row['usertype_id'].');"><i class="fa fa-pencil"></i></button>
					<button class="btn btn-xs btn-danger" data-original-title="Delete" data-toggle="tooltip" data-placement="top" onClick="delete_state('.$row['usertype_id'].')"><i class="fa fa-trash-o"></i></button>
				';
				}
				else
				{
					$row_data[]='';
				}
				$appData[] = $row_data;
				$id++;
			}
			$output['aaData'] = $appData;
			echo json_encode( $output );
		}
		else if(strtolower($POST['mode']) == "add") {
		 	{
				
				$tr = $dbcon -> query("SELECT `type_id`,`usertype_name`,`status` FROM `tbl_type` WHERE `usertype_name` = '$POST[usertype_name]'");
				if($tr->num_rows > 0) {
					$r = $tr -> fetch_assoc();
					if($r['status'] != 0) {
						$info['status']=0;
						$updateid=update_record('tbl_usertype', $info,"usertype_id=".$r['usertype_id'] , $dbcon);						
						if($updateid)
							echo "1";
						else
							echo "0";
					}
					else {
						echo '-1';
					}
				}
				else {
							$info['usertype_name']= $POST['usertype_name'];							
							$info['cdate']		= date("Y-m-d H:i:s");
							$info['user_id']		= $_SESSION[user_id];
							$info['company_id']		= $_SESSION['company_id'];
							$inserid=add_record('tbl_usertype', $info, $dbcon);
					if($inserid)
						echo "1";
					else
						echo "0";
				}
			}
		}
		else if(strtolower($POST['mode']) == "preedit") {			
			$q = $dbcon -> query("SELECT * FROM `tbl_usertype` WHERE `usertype_id` = '$POST[id]'  AND `user_id` = '$_SESSION[user_id]'");
			$r = $q->fetch_assoc();
			echo json_encode($r);
		}
		else if(strtolower($POST['mode']) == "edit") {
		 {
				$info['usertype_name']= $POST['type_name'];				
				$info['cdate']		= date("Y-m-d H:i:s");				
				$updateid=update_record('tbl_usertype', $info,"usertype_id=".$POST['eid'] , $dbcon);
				
				
				if($updateid)
					echo "1";
				else
					echo "0".$dbcon->error;
				
			}
		}
		else if(strtolower($POST['mode']) == "delete") {
		 
				$info['status']='2';
				$updateid=update_record('tbl_usertype', $info,"usertype_id=".$POST['eid'] , $dbcon);
				
				if($updateid)
					echo "1";
				else
					echo "0";
			 
		}
    }
  //  else {
   //     die("Error - 2");
   // }
}

//else {
   // die("Error - 1");
//}
?>