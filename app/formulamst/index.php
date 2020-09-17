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
			$aColumns = array('formulaid', 'formula_name','tax.tax_name', 'formula_status', 'fmst.user_id','tax_cat','tp.tax_per_name');
			$sIndexColumn = "formulaid";
			$isWhere = array("formula_status = 0".check_user('fmst'));
			$sTable = "formula_mst as fmst";			
			$isJOIN = array("inner join tbl_tax as tax on tax.tax_id=fmst.tax_id","left join tbl_tax_per as tp on tp.tax_per_id=fmst.tax_per_id");
			$hOrder = "fmst.formulaid desc";
			include('../../include/pagging.php');
			$appData = array();
			$id=1;
			foreach($sqlReturn as $row) {
				$row_data = array();
				$row_data[] = $row['sr'];
				$row_data[] = $row['tax_per_name']."%";
				$row_data[] = $row['tax_cat'];
				$row_data[] = $row['formula_name'];
				 
				$edit_btn=''; $delete_btn=''; 
				if($edit_btn_per){
					$edit_btn=' <button class="btn btn-xs btn-warning" data-original-title="Edit" data-toggle="tooltip" data-placement="top" onClick="edit_test('.$row['formulaid'].');"><i class="fa fa-pencil"></i></button>'; 
				}
				if($delete_btn_per){
					$delete_btn=' <button class="btn btn-xs btn-danger" data-original-title="Delete" data-toggle="tooltip" data-placement="top" onClick="delete_formula('.$row['formulaid'].')"><i class="fa fa-trash-o"></i></button>'; 
				} 
				
				$row_data[] = $edit_btn.' '.$delete_btn;  
				$appData[] = $row_data;
				$id++;
			}
			$output['aaData'] = $appData;
			echo json_encode( $output );
		}
		else if(strtolower($POST['mode']) == "add") {
			//if($_POST['token'] == $_SESSION['token']) 
			{
				$tax=implode(",",$POST['tax_id']);
				$query="select * from tbl_tax where tax_id in (".$tax.") order by tax_value desc";
				$result=$dbcon->query($query);
				while($rel=mysqli_fetch_assoc($result))
				{	
					$formula[]=$rel['tax_name'];
				}
			 $f=implode(" * ",$formula);
							
				$tr = $dbcon -> query("SELECT `formulaid`,`formula_name`,`formula_status` FROM `formula_mst` WHERE `formula_name` ='".$f."' and  company_id =".$_SESSION['company_id']);
							
				if($tr->num_rows > 0) {
					$r = $tr -> fetch_assoc();
					if($r['formula_status'] != 0) {
						$info['formula_status']=0;
						$updateid=update_record('formula_mst', $info,"formulaid=".$r['formulaid'] , $dbcon);						
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
							$info['tax_per_id']			= $POST['tax_per_id'];
							$info['tax_cat']			= $POST['tax_cat'];
							$info['tax_id']			= $tax;
							$info['formula_name']	= $f;							
							$info['cdate']			= date("Y-m-d H:i:s");
							$info['user_id']		= $_SESSION['user_id'];
							$info['usertype_id']	= $_SESSION['user_type'];
							$info['company_id']		= $_SESSION['company_id'];
							$inserid=add_record('formula_mst', $info, $dbcon);
					if($inserid)
						echo "1";
					else
						echo "0";
				}
			}
		}
		else if(strtolower($POST['mode']) == "preedit") {			
			$q = $dbcon -> query("SELECT * FROM `formula_mst` WHERE `formulaid` = '$POST[id]'");
			$r = $q->fetch_assoc();
			echo json_encode($r);
		}
		else if(strtolower($POST['mode']) == "edit") {
			//if($_POST['token'] == $_SESSION['token']) 
			{
			$tax=implode(",",$POST['tax_id']);
				$query="select * from tbl_tax where tax_id in (".$tax.") order by tax_value desc";
				$result=$dbcon->query($query);
				while($rel=mysqli_fetch_assoc($result))
				{	
					$formula[]=$rel['tax_name'];
				}
				$f=implode(" * ",$formula);
				$info['tax_per_id']			= $POST['tax_per_id'];
				$info['tax_cat']			= $POST['tax_cat'];
				$info['tax_id']			= $tax;
				$info['formula_name']	= $f;							
				$info['cdate']			= date("Y-m-d H:i:s");
				$info['user_id']		= $_SESSION['user_id'];
				$info['usertype_id']	= $_SESSION['user_type'];
				$updateid=update_record('formula_mst', $info,"formulaid=".$POST['eid'] , $dbcon);
				if($updateid)
					echo "1";
				else
					echo "0".$dbcon->error;
				
			}
		}
		else if(strtolower($POST['mode']) == "delete") {
			//if($_POST['token'] == $_SESSION['token']) 
			{
				$info['formula_status']='2';
				$updateid=update_record('formula_mst', $info,"formulaid=".$POST['eid'] , $dbcon);
				
				if($updateid)
					echo "1";
				else
					echo "0";
			}
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