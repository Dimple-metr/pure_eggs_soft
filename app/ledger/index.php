<?php
session_start(); //start session
$AJAX = true;
include("../../config/config.php");
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
			$aColumns = array('l.l_id', 'l.l_name', 'l.l_group','l.user_id','g.g_name','l.l_form','l.l_status');
			$sIndexColumn = "l_id";
			$isWhere = array("l_status !=2");
			$sTable = " tbl_ledger as l";			
			$isJOIN = array("left join tbl_group as g on g.g_id=l.l_group");
			$hOrder = "l.l_status desc";
			include('../../include/pagging.php');
			$appData = array();
			$id=1;
			foreach($sqlReturn as $row) {
				
				if($row['l_status']=='0')
				{
					$status="<strong style='color:green'>Approved</strong>";
					$change_status="<a class='btn btn-success' onclick='changeStatus(\"".$row['l_id']."\",\"".$row['l_status']."\")'><i class='fa fa-check-square-o'></i></a>";
				} 
				else 
				{  
					$status="<strong style='color:red' >Pending</strong>"; 
					$change_status="<a class='btn btn-danger' onclick='changeStatus(\"".$row['l_id']."\",\"".$row['l_status']."\")'><i class='fa fa-window-close'></i></a>";
				}
				
				//upload documnet only for salary accounts
				if($row['l_group']=='58')
				{
					$upload='<a class="btn btn-success" data-original-title="Upload Document" data-toggle="tooltip" data-placement="top" href="'.ROOT.'upload_document/'.$row['l_id'].'">Upload Documents</a>';
				}
				else
				{
					$upload='';
				}
				
				$row_data = array();
				$row_data[] = $row['sr'];
				$row_data[] = $row['l_name'];
				$row_data[] = $row['g_name'];
				$row_data[] = $status;
				$row_data[] = $upload;
				
				
				$edit_btn=''; $delete_btn=''; 
				if($edit_btn_per){
					$edit_btn='<a class="btn btn-xs btn-warning" data-original-title="Edit" data-toggle="tooltip" data-placement="top" href="'.ROOT.'ledger_edit/'.$row['l_id'].'"><i class="fa fa-pencil"></i></a>';
				}
				if($delete_btn_per){
					$delete_btn=' <button class="btn btn-xs btn-danger" data-original-title="Delete" data-toggle="tooltip" data-placement="top" onClick="delete_ledger('.$row['l_id'].')"><i class="fa fa-trash-o"></i></button>'; 
				} 
				
				if($row['l_form']=='customer_form')
				{
					$sold_btn='<button class="btn btn-xs btn-primary" data-original-title="Allocate Sale Customer Product" data-toggle="tooltip" data-placement="top" onClick="alloc_sold_pro('.$row['l_id'].');"><i class="fa fa-plus"></i></button>';
				}
				else
				{
					$sold_btn='';
				}
				
				
				$row_data[] = $edit_btn.' '.$delete_btn.' '.$sold_btn ; 
				$row_data[] = $change_status;
				$appData[] = $row_data;
				$id++;
			}
			$output['aaData'] = $appData;
			echo json_encode( $output );
		}
		else if(strtolower($POST['mode']) == "get_open_form") {
			
			$gid=$POST['gid'];
			
			$q=$dbcon->query("select * from tbl_group where g_id='$gid'");
			$row=mysqli_fetch_array($q);
			
			echo $row['form_id'];
			//echo $gid;
		}
		else if(strtolower($POST['mode']) == "add") {
			
			$info['l_name']			= $POST['ledger_name'];
			$info['l_group']		= $POST['ledger_grp'];
			$info['m_name']			= $POST['m_name'];
			$info['m_address']		= $POST['m_address'];
			$info['countryid']		= $POST['countryid'];
			$info['stateid']		= $POST['stateid'];
			$info['cityid']			= $POST['cityid'];
			$info['cust_pincode']	= $POST['cust_pincode'];
			$info['m_pan']			= $POST['m_pan'];
			$info['company_name']	= $POST['company_name'];
			$info['cust_cont_name']	= $POST['cust_cont_name'];
			$info['cust_mobile']	= $POST['cust_mobile'];
			$info['cust_email']		= $POST['cust_email'];
			$info['cust_website']	= $POST['cust_website'];
			$info['zone_id']		= $POST['zone_id'];
			$info['cust_remark']	= $POST['cust_remark'];
			$info['gst_no']			= $POST['gst_no'];
			$info['party_type']		= $POST['party_type'];
			$info['cust_gst_reg']	= $POST['cust_gst_reg'];
			$info['pay_terms']		= $POST['pay_terms'];
			$info['pay_method']		= $POST['pay_method'];
			$info['bill_type']		= $POST['bill_type'];
			$info['balance_typeid']	= $POST['balance_typeid'];
			$info['acc_type']		= $POST['acc_type'];
			$info['bankid']			= $POST['bankid'];
			$info['branch_name']	= $POST['branch_name'];
			$info['acc_name']		= $POST['acc_name'];
			$info['acc_number']		= $POST['acc_number'];
			$info['acc_chequeno']	= $POST['acc_chequeno'];
			$info['acc_chequeleft']	= $POST['acc_chequeleft'];
			$info['emp_mobile']		= $POST['emp_mobile'];
			$info['emp_email']		= $POST['emp_email'];
			$info['emp_password']	= $POST['emp_password'];
			$info['emp_zone_id']	= $POST['emp_zone_id'];
			$info['emp_user_type']	= $POST['emp_user_type'];
			$info['tax_value']		= $POST['tax_value'];
			$info['branch_id_customer']	= $POST['branch_id_customer'];
			$info['branch_id_employee']	= $POST['branch_id_emp'];
			$info['vehicle_no']			= $POST['vehicle_no'];
			$info['l_status']	= '1';
			
			$info['l_po_date']	= date('Y-m-d',strtotime($POST['l_po_date']));;
			$info['l_pono']	= $POST['l_pono'];
			
			$info['opn_balance']	= $POST['opn_balance'];
			$info['l_form']	= $POST['form_type'];
			
			$info['cdate']			= date("Y-m-d H:i:s");
			$info['user_id']		= $_SESSION['user_id'];
			$info['company_id']		= $_SESSION['company_id'];
			
			$tr = $dbcon -> query("SELECT `l_id`,`l_name`,`l_status`,`l_group` FROM `tbl_ledger` WHERE l_status!=2 and `l_name` ='".$POST['ledger_name']."' ");
			if($tr->num_rows > 0) {
				$row['res'] ="-1";
			}
			else
			{
				
			$inserid=add_record('tbl_ledger', $info, $dbcon);
			
			if($inserid){
				$ref_date=date("Y-m-d");
				add_general_book_entry($dbcon,"tbl_ledger",$inserid,$POST['balance_typeid'],$inserid,$POST['opn_balance'],$general_book_id,$ref_date);
				
				if($POST['form_type']=='customer_form')
				{
					/* Add Record in customer Person Table Start */
					
					$info1['cust_contact_person_name']			= stripcslashes($POST['cust_cont_name']);
					$info1['cust_contact_person_no']			= $POST['cust_mobile'];
					$info1['cust_contact_person_email']			= strtolower($POST['cust_email']);
					$info1['cust_id']							= $inserid;
					$info1['user_id']							= $_SESSION['user_id'];
					$info1['cust_contact_person_direct_status']	= 1;
					$insercntid=add_record("tbl_cust_contact_person", $info1, $dbcon);
						
					/* Add Record in customer Person Table End */
					
					$dbcon->query("update tbl_customer_bank set b_cust='$inserid' where b_cust='0' and userid='$_SESSION[user_id]'");
					
					$dbcon->query("update tbl_cust_contact_person set cust_id='$inserid' where cust_id='0' and user_id='$_SESSION[user_id]'");
				}
				
				if($POST['form_type']=='emp_form')
				{
					/*Entry in User Table Start*/	
					
					$infousr['vehicle_no']		= $POST['vehicle_no']; 
					$infousr['user_name']		= $POST['ledger_name']; 
					$infousr['user_mail']		= strtolower($POST['emp_email']); 
					$infousr['user_key']		= md5($_POST['emp_password']);
					$infousr['user_type']		= $POST['emp_user_type'];//Fixed Type Employee
					$infousr['user_country']	= $POST['countryid'];
					$infousr['user_stat']		= $POST['stateid'];
					$infousr['user_city']		= $POST['cityid'];
					$infousr['user_phone']		= $POST['emp_mobile'];
					$infousr['user_address']	= $_POST['m_address'];
					$infousr['user_rid']		= $_SESSION['user_id'];
					$infousr['company_id']		= $_SESSION['company_id'];
					$infousr['payment_status'] 	= 1;
					$infousr['employee_id'] 	= $inserid;//Employee ID flag check
					//var_dump($infousr);
					$inserusrid=add_record('users', $infousr, $dbcon);
					
					/*Entry in User Table End*/	
				}
				
				$row['res'] ="1";
				
			}
			else{
				$row['res'] ="0";
			}
			
			}
			
			echo json_encode($row);	
		}
		
		else if(strtolower($POST['mode']) == "edit") {
			
			$info['l_name']	= $POST['ledger_name'];
			$info['l_group']		= $POST['ledger_grp'];
			$info['m_name']		= $POST['m_name'];
			$info['m_address']		= $POST['m_address'];
			$info['countryid']			= $POST['countryid'];
			$info['stateid']			= $POST['stateid'];
			$info['cityid']	= $POST['cityid'];
			$info['cust_pincode']		= $POST['cust_pincode'];
			$info['m_pan']	= $POST['m_pan'];
			$info['company_name']		= $POST['company_name'];
			$info['cust_cont_name']	= $POST['cust_cont_name'];
			$info['cust_mobile']	= $POST['cust_mobile'];
			$info['cust_email']	= $POST['cust_email'];
			$info['cust_website']	= $POST['cust_website'];
			$info['zone_id']	= $POST['zone_id'];
			$info['cust_remark']	= $POST['cust_remark'];
			$info['gst_no']	= $POST['gst_no'];
			$info['party_type']	= $POST['party_type'];
			$info['cust_gst_reg']	= $POST['cust_gst_reg'];
			$info['pay_terms']	= $POST['pay_terms'];
			$info['pay_method']	= $POST['pay_method'];
			$info['bill_type']	= $POST['bill_type'];
			$info['balance_typeid']	= $POST['balance_typeid'];
			$info['acc_type']	= $POST['acc_type'];
			$info['bankid']	= $POST['bankid'];
			$info['branch_name']	= $POST['branch_name'];
			$info['acc_name']	= $POST['acc_name'];
			$info['acc_number']	= $POST['acc_number'];
			$info['acc_chequeno']	= $POST['acc_chequeno'];
			$info['acc_chequeleft']	= $POST['acc_chequeleft'];
			$info['emp_mobile']	= $POST['emp_mobile'];
			$info['emp_email']	= $POST['emp_email'];
			$info['emp_password']	= $POST['emp_password'];
			$info['emp_zone_id']	= $POST['emp_zone_id'];
			$info['emp_user_type']	= $POST['emp_user_type'];
			
			$info['opn_balance']	= $POST['opn_balance'];
			$info['l_form']	= $POST['form_type'];
			$info['vehicle_no']	= $POST['vehicle_no'];
			
			$info['l_po_date']	= date('Y-m-d',strtotime($POST['l_po_date']));
			$info['l_pono']	= $POST['l_pono'];
			
			$info['cdate']			= date("Y-m-d H:i:s");
			$info['user_id']		= $_SESSION['user_id'];
			$info['company_id']		= $_SESSION['company_id'];
			
			$updateid=update_record('tbl_ledger', $info,"l_id=".$POST['ledger_id'] , $dbcon);
			
			$info1['user_name'] = $POST['ledger_name'];
			$info1['user_mail']	= $POST['emp_email'];
			$info1['vehicle_no']	= $POST['vehicle_no'];
			
			update_record('users', $info1,"employee_id=".$POST['ledger_id'] , $dbcon);
			
			if($updateid){
				
				$general_book_id=get_general_book_id($dbcon,'tbl_ledger',$POST['ledger_id'],$POST['ledger_id']);
				$ref_date=date('Y-m-d');
				
				add_general_book_entry($dbcon,"tbl_ledger",$POST['ledger_id'],$POST['balance_typeid'],$POST['ledger_id'],$POST['opn_balance'],$general_book_id,$ref_date);
				
				$row['res'] ="3";
				
			}
			else{
				$row['res'] ="0";
			}
			
			echo json_encode($row);	
		}
		
		else if(strtolower($POST['mode']) == "delete") 
		{
			
			$info['l_status']	= 2;
			$updateid=update_record('tbl_ledger', $info,"l_id=".$POST['eid'] , $dbcon);
			$general_book_id=get_general_book_id($dbcon,'tbl_ledger',$POST['eid'],$POST['eid']);
			
			$info1['genral_book_status']	= 2;
			$updateid11=update_record('tbl_ledger', $info1,"general_book_id=".$general_book_id , $dbcon);
			
			$info_user['active']	= 2;
			$updateid111=update_record('users', $info_user,"employee_id=".$POST['eid'] , $dbcon);
			
			if($updateid)
				echo "1";	
			else
				echo "0";				
		}
		else if(strtolower($POST['mode']) == "change_status") 
		{
			$l_status=$POST['l_status'];
			$lid=$POST['lid'];
			
			if($l_status==0)
			{
				$info['l_status'] = 1;
			}
			else
			{
				$info['l_status'] = 0;
			}
			
			$updateid=update_record('tbl_ledger', $info,"l_id=".$POST['lid'] , $dbcon);		
			
			if($updateid)
				echo "1";	
			else
				echo "0";	
		}
		else if(strtolower($POST['mode']) == "get_branch_by_zone") 
		{
			$zid=$POST['zid'];
			$bid=$POST['bid'];
			$sindex=$POST['sindex'];
			
			echo get_branch_from_zone($dbcon,$zid,$bid,$sindex);
			//echo $zid;
		}
		else if(strtolower($POST['mode']) == "generate_report_ledger") 
		{
			$query="select g.* from tbl_group as g order by g.g_name";
			$qry=$dbcon->query($query);
			
			$cnt=1;$str='';
			while($row=mysqli_fetch_assoc($qry))
			{
				$str.='<tr>
					
					<th>'.$cnt.'</th>
					<th><a  data-original-title="Edit" data-toggle="tooltip" data-placement="top" href="'.ROOT.'ledger_detail/'.$row['g_id'].'">'.$row['g_name'].'</a></th>
					<th>0</th>
					<th>0</th>
					<th>0</th>
					
				</tr>';
				
				$cnt++;
			}
			
			echo $str;
		}
	
		else if(strtolower($POST['mode']) == "generate_report_ledger_detail") 
		{
			$l_id=$POST['l_id'];
			
			$query="select l.* from tbl_ledger as l where l.l_group='$l_id'";
			$qry=$dbcon->query($query);
			
			$cnt=1;$str='';
			while($row=mysqli_fetch_assoc($qry))
			{
				$str.='<tr>
					
					<th>'.$cnt.'</th>
					<th><a  data-original-title="Edit" data-toggle="tooltip" data-placement="top" href="'.ROOT.'ledger_form/'.$row['l_id'].'">'.$row['l_name'].'</a></th>
					<th>0</th>
					<th>0</th>
					<th>0</th>
					
				</tr>';
				
				$cnt++;
			}
			
			echo $str;
		}
		else if(strtolower($POST['mode']) == "ledger_tree") 
		{
			$parentKey = -1;
			//echo $parentKey;
			$sql="select * from tbl_group order by g_name";
			$rs=$dbcon->query($sql);
			$count=mysqli_num_rows($rs);
			
			if($count > 0)
			  {
				  $data = members_Tree($dbcon,$parentKey);
			  }else{
				  $data=["id"=>"0","name"=>"No Members present in list","text"=>"No Members is present in list","nodes"=>[]];
			  }
			  
			  echo json_encode(array_values($data));
			 // print_r($data);
			//echo $count;
		}
		else if(strtolower($POST['mode']) == "check_username") 
		{
			$uname=$POST['uname'];
			
			$sel=$dbcon->query("select emp_email from tbl_ledger where l_status=0 and emp_email='$uname' ");
			$count=mysqli_num_rows($sel);
			
			echo $count;
		}
		else if(strtolower($POST['mode']) == "upload_docs") 
		{
			 $l_id=$POST['l_id'];
			 $docs_id=$POST['docs_id'];
			 
			 $rel=$dbcon->query("select ed_id from tbl_employee_document where ed_lid='$l_id' and ed_doc_type='$docs_id'");
			 $count=mysqli_num_rows($rel);
			 
			 $test = explode('.', $_FILES["file"]["name"]);
			 $ext = end($test);
			 $name = rand(100, 999) . '.' . $ext;
			 $path='../../view/upload/employee_document/';
			 $location = $path . $name;  
			 move_uploaded_file($_FILES["file"]["tmp_name"], $location);
			 
			 $info1['ed_lid']=$l_id;
			 $info1['ed_doc_type']=$docs_id;
			 $info1['ed_path']=$name;
			 $info1['cdate']=date("Y-m-d");
			 $info1['user_id']			= $_SESSION['user_id'];
			 $info1['company_id']			= $_SESSION['company_id'];
			
			 $table='tbl_employee_document';$tableid='ed_id';
			 
			 if($count>0)
			 {
				update_record($table, $info1,"ed_lid='$l_id' and ed_doc_type='$docs_id'", $dbcon);	
				
			 }
			 else
			 {
				 $inserid=add_record($table, $info1, $dbcon);
			 }
			
		}
		else if(strtolower($POST['mode']) == "show_upload_docs") 
		{
			$l_id=$POST['l_id'];
			
			$q="select * from tbl_employee_document where ed_lid='$l_id'";
			
			$str="";
			
			$sel=$dbcon->query($q);
			while($row=mysqli_fetch_array($sel))
			{
				if($row['ed_doc_type']=='1')
				{
					$type='Pan card';
				}
				else if($row['ed_doc_type']=='2')
				{
					$type='Adhar Card Front';
				}
				else if($row['ed_doc_type']=='3')
				{
					$type='Adhar Card Back';
				}
				else
				{
					$type='Passport Size Photo';
				}
				
				$str.="<div class='col-md-3' style='text-align:center;font-size:18px;'>";
				$str.="<strong >".$type."</strong>";
				$str.="<img src='".ROOT.'upload/employee_document/'.$row['ed_path']."' width='100%' height='200' />";
				$str.="</div>";
				
			}
			
			echo $str;
		}
    }
 
}

	  
?>