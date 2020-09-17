<?php
session_start();
$AJAX = true;
include("../../config/config.php");
//error_reporting(E_ALL);
include("../../config/session.php");
include("../../include/common_functions.php");
include("../../include/function_database_query.php");

//print_r($_POST);
//print_r($_FILES);
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
		$aColumns = array('cust_id', 'company_name', 'city.city_name', 'state.state_name', 'cust_name', 'cust_email', 'cust_mobile','cust_status','cust.cdate','cust.user_id','gro.group_name');
		$sIndexColumn = "cust_id";
		$isWhere = array("cust_status = 0 and cust.company_id in (0,$_SESSION[company_id])");
		$sTable = " tbl_customer as cust";			
		$isJOIN = array('left join city_mst city on cust.cityid=city.cityid', 'left join state_mst state on cust.stateid=state.stateid','left join group_mst as gro on gro.group_id=cust.group_id');
		$hOrder = "cust.cust_id desc";
		include('../../include/pagging.php');
		$appData = array();
		$id=1;
		foreach($sqlReturn as $row) {
			$row_data = array();
			$row_data[] = $row['sr'];
			$row_data[] = $row['company_name'];
			$row_data[] = $row['cust_name'];
			$row_data[] = $row['city_name'];
			$row_data[] = $row['state_name'];
			$row_data[] = $row['cust_mobile'];
			$row_data[] = $row['cust_email'];
			$row_data[] = $row['group_name'];
			 
			$edit_btn='';$delete_btn='';$add_per_btn='';
			
			if($edit_btn_per){
				$edit_btn=' <a class="btn btn-xs btn-warning" data-original-title="Edit" data-toggle="tooltip" data-placement="top" href="'.ROOT.'customer_edit/'.$row['cust_id'].'"><i class="fa fa-pencil"></i></a>';
				//$add_per_btn='<button type="button" class="btn btn-round btn-success btn-xs" data-original-title="ADD Contact Person" data-toggle="tooltip" data-placement="top" onclick="open_person_data('.$row['cust_id'].');"><i class="fa fa-plus"></i></button>';
				
			}
			if($delete_btn_per){
				$delete_btn=' <button class="btn btn-xs btn-danger" data-original-title="Delete" data-toggle="tooltip" data-placement="top" onClick="delete_cust('.$row['cust_id'].')"><i class="fa fa-trash-o"></i></button>';
				
			}
			
			$row_data[] = $edit_btn.' '.$delete_btn.' '.$add_per_btn; 
			$appData[] = $row_data;
			$id++;
		}
		$output['aaData'] = $appData;
		echo json_encode( $output );
	}
	else if(strtolower($POST['mode']) == "add" || strtolower($POST['cust_mode']) == "add") {
		$tr = $dbcon -> query("SELECT `cust_id`,`company_name`,`cust_status` FROM `tbl_customer` WHERE `company_name` = '$POST[company_name]' and `cust_email` = '$POST[cust_email]' and cust_status=0 and company_id=".$_SESSION['company_id'] );
		if($tr->num_rows > 0) {
			$row['res']='-1';
		}
		else {
			
			$info['company_name']	= stripcslashes($POST['company_name']);
			$info['cust_name']		= stripcslashes($POST['cust_name']);
			$info['cust_address']	= text_rnremove($_POST['cust_address']);
			$info['countryid']		= $POST['countryid'];
			$info['stateid']		= $POST['stateid'];
			$info['cityid']			= $POST['cityid'];
			$info['cust_mobile']	= $POST['cust_mobile'];
			$info['cust_email']		= strtolower($POST['cust_email']);
			$info['opening_balance']= $POST['opening_balance'];
			$info['balance_typeid']	= $POST['balance_typeid'];
			$info['cust_pincode']	= $POST['cust_pincode'];
			$info['gst_no']			= strtoupper($POST['gst_no']);
			$info['pan_no']			= $POST['pan_no'];
			$info['group_id']		= $POST['group_id'];
			$info['cdate']			= date("Y-m-d H:i:s");
			$info['user_id']		= $_SESSION['user_id'];
			$info['usertype_id']	= $_SESSION['user_type'];
			$info['multi_company']	= $POST['multi_company'];
			if(!$POST['multi_company'])
				$info['company_id']			= $_SESSION['company_id'];
			else
				$info['company_id']			= 0;
			$inserid=add_record('tbl_customer', $info, $dbcon);
			
			
		/* Add Record in customer Person Table Start */
			$info1['cust_contact_person_name']			= stripcslashes($POST['cust_name']);
			$info1['cust_contact_person_no']			= $country_code.$POST['cust_mobile'];
			$info1['cust_contact_person_email']			= strtolower($POST['cust_email']);
			$info1['cust_id']							= $inserid;
			$info1['user_id']							= $_SESSION['user_id'];
			$info1['cust_contact_person_direct_status']	= 1;
			$insercntid=add_record("tbl_cust_contact_person", $info1, $dbcon);
		/* Add Record in customer Person Table End */
	
		
			$row['res']='';
			if($inserid){
				if(strtolower($POST['cust_model'])=="cust_model"){
					$query="select * from tbl_customer where cust_id=".$inserid;
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
	else if(strtolower($POST['mode']) == "edit") {
			$info['company_name']	= stripcslashes($POST['company_name']);
			$info['cust_name']		= stripcslashes($POST['cust_name']);
			$info['cust_address']	= text_rnremove($_POST['cust_address']);
			$info['countryid']		= $POST['countryid'];
			$info['stateid']		= $POST['stateid'];
			$info['cityid']			= $POST['cityid'];
			$info['cust_mobile']	= $POST['cust_mobile'];
			$info['cust_email']		= strtolower($POST['cust_email']);
			$info['opening_balance']= $POST['opening_balance'];
			$info['balance_typeid']	= $POST['balance_typeid'];
			$info['cust_pincode']	= $POST['cust_pincode'];
			$info['pan_no']			= $POST['pan_no'];
			$info['group_id']		= $POST['group_id'];
			$info['gst_no']			= strtoupper($POST['gst_no']);
			$info['cdate']			= date("Y-m-d H:i:s");
			$info['user_id']		= $_SESSION['user_id'];
			$info['usertype_id']	= $_SESSION['user_type'];
			$info['multi_company']	= $POST['multi_company'];
			if(!$POST['multi_company'])
				$info['company_id']			= $_SESSION['company_id'];
			else
				$info['company_id']			= 0;
		
		$updateid=update_record('tbl_customer', $info,"cust_id=".$POST['eid'] , $dbcon);
		 
		/* Add Record in customer Person Table Start */
			$info1['cust_contact_person_name']			= stripcslashes($POST['cust_name']);
			$info1['cust_contact_person_no']			= $POST['cust_mobile'];
			$info1['cust_contact_person_email']			= strtolower($POST['cust_email']);
			$info1['user_id']							= $_SESSION['user_id'];
			$updatecntid=update_record('tbl_cust_contact_person', $info1,"cust_id=".$POST['eid']." and cust_contact_person_direct_status=1" , $dbcon);	
		/* Add Record in customer Person Table End */
		
		$row['res']='';
		
		if($updateid){
			$row['res']='update';
		}
		else{
			$row['res']='0';
		}
		echo json_encode($row);
	}
	else if(strtolower($POST['mode']) == "delete") {
		$info['cust_status']		= 2;
		$updateid=update_record('tbl_customer', $info,"cust_id=".$POST['eid'] , $dbcon);				
		if($updateid)
			echo "1";	
		else
			echo "0";			
	}
	else if(strtolower($POST['mode']) == "load_state") {
		$countryid=$POST['id'];				
		echo get_state($dbcon,'',$countryid);
	}
	else if(strtolower($POST['mode']) == "load_city") {
		$cityid=$POST['id'];				
		echo $str=getcity($dbcon,$cityid,'');
	}
	else if(strtolower($POST['mode']) == "add_cust_person_field") {
	
		$info1['cust_contact_person_name']			= $POST['cust_contact_person_name'];
		$info1['cust_contact_person_no']			= $POST['cust_contact_person_no'];
		$info1['cust_contact_person_email']			= strtolower($POST['cust_contact_person_email']);
		$info1['cust_id']							= $POST['cust_id'];
		$info1['user_id']							= $_SESSION['user_id'];
		$table='tbl_cust_contact_person';$tableid='cust_contact_person_id';
		
		if(empty($POST['edit_cust_contact_person_id'])){
			echo $inserid=add_record($table, $info1, $dbcon);
		}
		else{
			$updateid=update_record($table, $info1, $tableid."=".$POST['edit_cust_contact_person_id'] , $dbcon);	
		}
	}
	else if(strtolower($POST['mode']) == "show_cust_person_datatable") {
		
		$where='';
		$appData = array();
		$i=1;
		$aColumns = array('cust_contact_person_id', 'cust_contact_person_name', 'cust_contact_person_no', 'cust_contact_person_email', 'cust_contact_person_status', 'sub_trn.user_id','cust_contact_person_direct_status');
		$sIndexColumn = "cust_contact_person_id";
		$isWhere = array("cust_contact_person_status = 0 and cust_id=".$POST['cust_id']." ".$where);
		$sTable = "tbl_cust_contact_person as sub_trn";			
		$isJOIN = array();
		$hOrder = "sub_trn.cust_contact_person_id desc";
		include('../../include/pagging.php');
		$appData = array();
		$id=1;
		foreach($sqlReturn as $row) {
			$row_data 	= array();
			$row_data[] = $row['sr'];
			$row_data[] = $row['cust_contact_person_name'];
			$row_data[] = $row['cust_contact_person_no'];
			$row_data[] = $row['cust_contact_person_email'];
			
			$edit='';$delete='';
			if(!$row['cust_contact_person_direct_status']){
				$edit='<button class="btn btn-xs btn-warning" data-original-title="Edit" data-toggle="tooltip" data-placement="top" onClick="edit_cust_person('.$row['cust_contact_person_id'].')"><i class="fa fa-pencil"></i></button>';
				$delete='<button class="btn btn-xs btn-danger" data-original-title="Delete" data-toggle="tooltip" data-placement="top" onClick="delete_cust_person('.$row['cust_contact_person_id'].')"><i class="fa fa-trash-o"></i></button>';
			}
			
			$row_data[] = $edit.' '.$delete;
			
			$appData[] = $row_data;
			$id++;
		}
		$output['aaData'] = $appData;
		echo json_encode( $output );
	}
	else if(strtolower($POST['mode'])== "edit_cust_person") {
		$q = $dbcon -> query("SELECT mst.* FROM tbl_cust_contact_person as mst WHERE cust_contact_person_id = '$POST[cust_contact_person_id]'");
		$r = $q->fetch_assoc();
		echo json_encode($r);
	}
	else if(strtolower($POST['mode'])== "delete_cust_person") {
		$row=array();
		$info['cust_contact_person_status']=2;
		
		$updateid=update_record("tbl_cust_contact_person", $info, "cust_contact_person_id=".$POST['cust_contact_person_id'], $dbcon);
		
		if($updateid)
			$row['res']="1";
		else
			$row['res']="0";
		
		echo json_encode($row);
	}
	else if(strtolower($POST['mode'])== "view_cust_det") {
		$str='';
		$cust_qry="select cust.*,country.country_name,state.state_name,city.city_name from tbl_customer as cust 
		left join country_mst as country on country.countryid=cust.countryid
		left join state_mst as state on state.stateid=cust.stateid
		left join city_mst as city on city.cityid=cust.cityid
		where cust.cust_id=".$POST['cust_id'];
		$cust_rel=mysqli_fetch_assoc($dbcon->query($cust_qry));
		$str='<table cellspacing="10" style="border-collapse:inherit;" id="product_list" class="display table table-bordered table-striped">
			<tr>
				<td colspan="3">Company Name : <strong>'.$cust_rel['company_name'].'</strong></td> 
			</tr>
			<tr>
				<td>Country : '.$cust_rel['country_name'].'</td>
				<td>State : '.$cust_rel['state_name'].'</td>
				<td>City : '.$cust_rel['city_name'].'</td>
			</tr> 
		</table>';
		
		$str.='<strong class="text-center">Contact Person List : </strong>';
		
		$str.='<table cellspacing="10" style="border-collapse:inherit;" class="display table table-bordered table-striped">
			<thead>
			<tr>
				<th class="text-center">Person Name</th>
				<th class="text-center">Mobile No.</th>
				<th class="text-center">Email</th>
			</tr>
			</thead>';
		$per_qry="select * from tbl_cust_contact_person where cust_contact_person_status = 0 and cust_id=".$cust_rel['cust_id']." ";
		$per_qry_rs=$dbcon->query($per_qry);
		if(mysqli_num_rows($per_qry_rs)){
			while($per_rel=mysqli_fetch_assoc($per_qry_rs)){
				$str.='<tr>
					<td>'.$per_rel['cust_contact_person_name'].'</td>
					<td>'.$per_rel['cust_contact_person_no'].'</td>
					<td>'.$per_rel['cust_contact_person_email'].'</td>
				</tr>'; 
			}
		}
		else{
			$str.='<tr> 
					<td colspan="3">NO DATA FOUND !!!</td> 
				</tr>';
		}
		$str.='</table>';
		
		$resp['cust_det_html_resp']=$str;
		echo json_encode($resp);
	}
	else if(strtolower($POST['mode']) == "add_bank_name") {
			
			
			$info1['bank_ac']= $POST['bank_ac'];
			$info1['b_name']= $POST['bank_name'];
			$info1['ac_name']= $POST['ac_name'];
			$info1['bank_ifsc']= $POST['bank_ifsc'];
			$info1['bank_open']= $POST['bank_open'];
			$info1['b_cust']= $POST['cust_id'];
			$info1['userid']		= $_SESSION['user_id'];
			
			$info1['cdate'] = date("Y-m-d");
			
			$table='tbl_customer_bank';$tableid='b_id';
		
			if(empty($POST['edit_id']))
			{
				$inserid=add_record($table, $info1, $dbcon);
			}
			else
			{
				$updateid=update_record($table, $info1,$tableid."=".$POST['edit_id'] , $dbcon);	
			}
			
			echo "1";
		}
		else if(strtolower($POST['mode']) == "load_bank_detail") {
			
			if(strtolower($POST['form_mode']) == "edit"){
				$query="select mst.*,b.bank_name from tbl_customer_bank as mst 
				left join bank_mst as b on b.bankid=mst.b_name
				where mst.b_cust='$_POST[cust_id]' order by b_id Desc";
			}
			else{
				$query="select mst.*,b.bank_name from tbl_customer_bank as mst 
				left join bank_mst as b on b.bankid=mst.b_name
				where mst.b_cust='0' order by b_id Desc";
			}
		    
			$result=$dbcon->query($query);
			echo '<div class="clearfix"></div>
					
					<div class="col-md-12 col-xs-11">
					  <div class="form-group">
						<table cellspacing="10" style="border-spacing:10px;" class="display table table-bordered table-striped">
						<tr id="field">
							<th class="text-center">A/c No</th>
							<th class="text-center">Bank Name</th>
							<th class="text-center">A/C Name</th>
							<th class="text-center">IFSC</th>
							<th class="text-center">Opening</th>
							<th class="text-center"></th>
						</tr>';
			if(mysqli_num_rows($result)>0)
			{
				$i=1;
				while($rel=mysqli_fetch_assoc($result))
				{
					echo '<tr id="fieldtr'.$id.'" >
						<td style="vertical-align:top;">
							'.$rel['bank_ac'].'
						</td>
						<td style="vertical-align:top;" class="text-center hide_act_add">
							'.$rel['bank_name'].'
						</td>
						<td style="vertical-align:top;" class="text-right">
							'.$rel['ac_name'].'
						</td>
						<td style="vertical-align:top;" class="text-right">
							'.$rel['bank_ifsc'].'
						</td>
						<td style="vertical-align:top;" class="text-right">
							'.$rel['bank_open'].'
						</td>
						
						<td style="vertical-align:top" class="text-center">
							<button type="button" class="btn btn-round btn-warning btn-xs" onclick="edit_data_bank('.$rel['b_id'].');" id="fieldtrnedit'.$i.'"><i class="fa fa-pencil"></i></button>
							<button type="button" class="btn btn-round btn-danger btn-xs" onclick="delete_data_bank('.$rel['b_id'].');" id="fieldtrnremove'.$i.'"><i class="fa fa-times"></i></button>
						</td>	
					
					</tr>';
					$i++;
				}
			}
			else{
				echo '<tr><td colspan="7" class="text-center">NO DATA FOUND</td></tr>';
			}
				echo '
					</table>			 
				</div>
			</div>';
		}
		else if(strtolower($POST['mode'])== "preedit_bank")
		{
			$q = $dbcon -> query("SELECT * FROM tbl_customer_bank WHERE b_id='$POST[id]'");
			$r = $q->fetch_assoc();
			
			//$r['producthtml'] = getrequiredproduct($dbcon,$r['raw_product_id'],' and product_type='.$r["product_type"].'');
			echo json_encode($r);
			//echo $POST['mode'];
		}
		else if(strtolower($POST['mode'])== "delete_data_bank")
		{
			
			$deleteid=delete_record('tbl_customer_bank', "b_id=$POST[eid]", $dbcon);

			if($deleteid)
				$row['res']="1";
			else
				$row['res']="0";
			echo json_encode($row);
		}
		else if(strtolower($POST['mode']) == "add_contact_person") {
			
			
			$info1['cust_contact_person_name']= $POST['con_name'];
			$info1['cust_contact_person_no']= $POST['con_mobile'];
			$info1['cust_contact_person_email']= $POST['con_email'];
			$info1['cust_id']= $POST['cust_id'];
			$info1['user_id']		= $_SESSION['user_id'];
			$info1['cdate'] = date("Y-m-d h:i:s");
			
			$table='tbl_cust_contact_person';$tableid='cust_contact_person_id';
		
			if(empty($POST['edit_id']))
			{
				$inserid=add_record($table, $info1, $dbcon);
			}
			else
			{
				$updateid=update_record($table, $info1,$tableid."=".$POST['edit_id'] , $dbcon);	
			}
			
			echo "1";
		}
		
		else if(strtolower($POST['mode']) == "load_contact_detail") {
			
			if(strtolower($POST['form_mode']) == "edit"){
				$query="select * from tbl_cust_contact_person where cust_id='$_POST[cust_id]' and user_id='$_SESSION[user_id]' order by cust_contact_person_id Desc";
			}
			else{
				$query="select * from tbl_cust_contact_person where cust_id='0' and user_id='$_SESSION[user_id]' order by cust_contact_person_id Desc";
			}
		    
			$result=$dbcon->query($query);
			echo '<div class="clearfix"></div>
					<div class="col-md-12 col-xs-11">
					  <div class="form-group">
						<table cellspacing="10" style="border-spacing:10px;" class="display table table-bordered table-striped">
						<tr id="field">
							<th class="text-center">Name</th>
							<th class="text-center">Mobile</th>
							<th class="text-center">Email</th>
							<td class="text-center"></td>
						</tr>';
			if(mysqli_num_rows($result)>0)
			{
				$i=1;
				while($rel=mysqli_fetch_assoc($result))
				{
					echo '<tr id="fieldtr'.$id.'" >
						<td style="vertical-align:top;">
							'.$rel['cust_contact_person_name'].'
						</td>
						<td style="vertical-align:top;" class="text-center hide_act_add">
							'.$rel['cust_contact_person_no'].'
						</td>
						<td style="vertical-align:top;" class="text-right">
							'.$rel['cust_contact_person_email'].'
						</td>
						
						<td style="vertical-align:top" class="text-center">
							<button type="button" class="btn btn-round btn-warning btn-xs" onclick="edit_data_contact('.$rel['cust_contact_person_id'].');" id="fieldtrnedit'.$i.'"><i class="fa fa-pencil"></i></button>
							<button type="button" class="btn btn-round btn-danger btn-xs" onclick="delete_data_contact('.$rel['cust_contact_person_id'].');" id="fieldtrnremove'.$i.'"><i class="fa fa-times"></i></button>
						</td>	
					
					</tr>';
					$i++;
				}
			}
			else{
				echo '<tr><td colspan="10" class="text-center">NO DATA FOUND</td></tr>';
			}
				echo '
					</table>			 
				</div>
			</div>';
		}
		
		else if(strtolower($POST['mode'])== "preedit_contact")
		{
			$q = $dbcon -> query("SELECT * FROM tbl_cust_contact_person WHERE cust_contact_person_id='$POST[id]'");
			$r = $q->fetch_assoc();
			
			//$r['producthtml'] = getrequiredproduct($dbcon,$r['raw_product_id'],' and product_type='.$r["product_type"].'');
			echo json_encode($r);
			//echo $POST['mode'];
		}
		else if(strtolower($POST['mode'])== "delete_data_contact")
		{
			
			$deleteid=delete_record('tbl_cust_contact_person', "cust_contact_person_id=$POST[eid]", $dbcon);

			if($deleteid)
				$row['res']="1";
			else
				$row['res']="0";
			echo json_encode($row);
		}
		else if(strtolower($POST['mode']) == "show_sold_pro") {
		if($POST['cust_id']!=""){
		  $where ="and imst.cust_id =".$POST['cust_id'];
		}
		$appData = array();
		$i=1;
		$aColumns = array('cust_sold_pro_id','sold_inv_no','sold_inv_date','sold_inv_rate','sold_inv_foc_date','pro.product_name','model.model_name');
		$sIndexColumn = "cust_sold_pro_id";
		$isWhere = array("cust_sold_pro_status=0 ".$where." and imst.company_id in(0,$_SESSION[company_id])");
		$sTable = "tbl_cust_sold_pro as imst";
		$isJOIN = array("left join product_mst as pro on pro.product_id=imst.product_id", "left join model_mst as model on model.model_id=imst.model_id");
		$hOrder = "imst.cust_sold_pro_id desc";
		include('../../include/pagging.php');
		$appData = array();
		$id=1;
		foreach($sqlReturn as $row) {
			$row_data = array();
			//$row_data[] = $row['sr'];
			
			$row_data[] = $row['product_name'];
			$row_data[] = date("d-m-Y",strtotime($row['sold_inv_foc_date']));
			
			$row_data[] = '<button type="button" class="btn btn-xs btn-warning" data-original-title="Edit" data-toggle="tooltip" data-placement="top" onClick="edit_sold_pro('.$row['cust_sold_pro_id'].');"><i class="fa fa-pencil"></i></button>
				<button type="button" class="btn btn-xs btn-danger" data-original-title="Delete" data-toggle="tooltip" data-placement="top" onClick="delete_sold_pro('.$row['cust_sold_pro_id'].')"><i class="fa fa-trash-o"></i></button>';
			$appData[] = $row_data;
			$id++;
		}
		$output['aaData'] = $appData;
		echo json_encode( $output );
	}
	else if(strtolower($POST['mode'])== "add_sold_pro_field") {
		$tr = $dbcon -> query("SELECT `cust_sold_pro_id` FROM `tbl_cust_sold_pro` WHERE `cust_id` = '$POST[cust_id]' and `product_id` = '$POST[product_id]' and cust_sold_pro_status=0 and company_id=".$_SESSION['company_id'] );
		if($tr->num_rows > 0 && !$POST['edit_id']) {
			$row['res']='-1';
		}
		else{
			$info1['cust_id']				= $POST['cust_id'];
			$info1['sold_inv_foc_date']		= date("Y-m-d",strtotime($POST['sold_inv_foc_date']));
			$info1['product_id']			= $POST['product_id'];
			$info1['cdate']					= date("Y-m-d H:i:s");
			$info1['user_id']				= $_SESSION['user_id'];
			$info1['company_id']			= $_SESSION['company_id'];
			$table='tbl_cust_sold_pro';$tableid='cust_sold_pro_id';
			
			if(empty($POST['edit_id'])) {
				$inserid=add_record($table, $info1, $dbcon);
			}
			else {
				$updateid=update_record($table, $info1, $tableid."=".$POST['edit_id'], $dbcon);	
			}
			$row['res']='1';
		}
		echo json_encode($row);
	}
	else if(strtolower($POST['mode']) == "edit_sold_pro") {	
		$q = $dbcon -> query("SELECT * FROM `tbl_cust_sold_pro` WHERE cust_sold_pro_status=0 and `cust_sold_pro_id` = '$POST[cust_sold_pro_id]'");
		$r = $q->fetch_assoc(); 
		$r['model_resp_html'] = get_prowise_model($dbcon,$r['model_id'],$r['product_id']);
		$r['sold_inv_date'] = date("d-m-Y",strtotime($r['sold_inv_date']));
		$r['sold_inv_foc_date'] = date("d-m-Y",strtotime($r['sold_inv_foc_date']));
		echo json_encode($r);
	}
	else if(strtolower($POST['mode']) == "delete_sold_pro") {
		$info['cust_sold_pro_status']='2';
		$updateid=update_record('tbl_cust_sold_pro', $info, "cust_sold_pro_id=".$POST['cust_sold_pro_id'], $dbcon);
		
		if($updateid)
			echo "1";
		else
			echo "0";
	}
	
?>