<?php

session_start(); //start session
$AJAX = true;
include("../../config/config.php");
//error_reporting(E_ALL);
include("../../config/session.php");
include("../../include/function_database_query.php");
 
		if($_POST != NULL) {
			$POST = bulk_filter($dbcon,$_POST);
		}
		else {
			$POST = bulk_filter($dbcon,$_GET);
		}
		if(strtolower($POST['mode']) == "edit") {
						
				$info['show_disc']					= $POST['show_disc'];
				$info['series_same']			= $POST['series_same'];
				$info['show_charges']				= $POST['show_charges'];
				$info['letter_head_top_margin']		= $POST['letter_head_top_margin'];
				$info['letter_head_bottom_margin']	= $POST['letter_head_bottom_margin'];
				$info['letter_head_left_margin']	= $POST['letter_head_left_margin'];
				$info['letter_head_right_margin']	= $POST['letter_head_right_margin'];
				$info['software_type']	= $POST['soft_type'];
				$info['cdate']			= date("Y-m-d H:i:s");
				/*	if($POST['soft_type']=="0"){
						$menuid=array("1","5","15","28","29","30","31","36","37","38","51","80","91","100","101","7","17","54","60","77","85","87","90","102","74","75","92","97","98","73","95","96","32","33","34","46","6","23","24","25","26");
						
					}else if($POST['soft_type']=="1"){
						$menuid=array("59","61","78","79","86","88","103","55","57","58","76","81","93","94","1","5","15","28","29","30","31","36","37","38","51","80","91","100","101","7","17","54","60","77","85","87","90","102","74","75","92","97","98","73","95","96","32","33","34","46","6","23","24","25","26");
					}
					else if($POST['soft_type']=="2"){
						$menuid=array("65","63","62","64","66","1","5","15","28","29","30","31","36","37","38","51","80","91","100","101","7","17","54","60","77","85","87","90","102","74","75","92","97","98","73","95","96","32","33","34","46","6","23","24","25","26","59","61","78","79","86","88","103","55","57","58","76","81","93","94");
					}else if($POST['soft_type']=="3"){
						$menuid=array("65","63","62","64","66","1","5","15","28","29","30","31","36","37","38","51","80","91","100","101","7","17","54","60","77","85","87","90","102","74","75","92","97","98","73","95","96","32","33","34","46","6","23","24","25","26","59","61","78","79","86","88","103","55","57","58","76","81","93","94");
					}
					$deleteid=delete_record('tbl_permission',"usertype_id=2", $dbcon);
				foreach ($menuid as $key => $menuid) 
							{	
					  $info4['usertype_id'] =2;
                      $info4['menu_id'] = $menuid;
					   $info4['permission'] = 1;
					    $info4['user_id'] = $POST['user_id'];

				//$updateid=update_record('tbl_permission', $info4,"menu_id=".$menuid , $dbcon);
				$inserid=add_record('tbl_permission', $info4, $dbcon);
							}	*/	
				$updateid=update_record('tbl_company', $info,"company_id=".$POST['eid'] , $dbcon);
				
				$info2['print_align']	=	$POST['print_align'];
				$updateid2=update_record('users', $info2,"company_id=".$POST['eid'] , $dbcon);
			
				if($updateid)
					echo "update";
				else
					echo "0".$dbcon->error;
				
				
		}
		 
?>
