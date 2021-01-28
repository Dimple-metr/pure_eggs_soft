<?php

session_start();
$AJAX = true;
include("../../config/config.php");
//error_reporting(E_ALL);
include("../../config/session.php");
include("../../include/function_database_query.php");
include_once("../../include/common_functions.php");

if($_POST != NULL) {
	$POST = bulk_filter($dbcon,$_POST);
}
else {
	$POST = bulk_filter($dbcon,$_GET);
}
		
		if(strtolower($POST['mode']) == "fetch") {
			
		$s_date=explode(' - ',$POST['date']);
		$_SESSION['start']=$s_date[0];
		$_SESSION['end']=$s_date[1];
		
		$where='';
			
			$where.="  and contra_date>='".date('Y-m-d',strtotime($s_date[0]))."' AND contra_date<='".date('Y-m-d',strtotime($s_date[1]))."'";
			$appData = array();
			$i=1;
			$aColumns = array('contra_id','contra_no','contra_date','remark','contra_status','po.cdate','po.user_id');
			$sIndexColumn = "contra_id";
			$isWhere = array("po.contra_status = 0 and po.company_id = ".$_SESSION['company_id']." ".$where.check_user('po'));
			$sTable = "tbl_contra as po";			
			$isJOIN = array();
			$hOrder = "po.contra_date desc";
			include('../../include/pagging.php');
			$appData = array();
			$id=1;
			foreach($sqlReturn as $row) {
				$row_data = array();
				$row_data[] = $row['contra_no'];
				$row_data[] = date('d M, Y',strtotime($row['contra_date']));
				$addpayment='';$delete='';$edit='';
					
					$delete='<button class="btn btn-xs btn-danger" data-original-title="Delete" data-toggle="tooltip" data-placement="top" onClick="delete_invoice('.$row['contra_id'].')"><i class="fa fa-trash-o"></i></button>';
					//$view='<a class="btn btn-xs btn-info" data-original-title="View" data-toggle="tooltip" data-placement="top" href="'.ROOT.'purchase_view/'.$row['po_id'].'"><i class="fa fa-eye"></i></a> ';
					
					$edit='<a class="btn btn-xs btn-warning" data-original-title="Edit" data-toggle="tooltip" data-placement="top" href="'.ROOT.'contra_entry_edit/'.$row['contra_id'].'"><i class="fa fa-pencil"></i></a>';
					$row_data[] = $edit.' '.$delete.' '.$view;
			 
			$appData[] = $row_data;
			$id++;
			}
			$output['aaData'] = $appData;
			echo json_encode( $output );
		}
		else if(strtolower($POST['mode']) == "add") {
			$query_invoicetype = $dbcon->query("UPDATE tbl_invoicetype SET taxinvoice_start = taxinvoice_start +1 WHERE invoicetype_id = ".$POST['invoicetype_id']);
			
				$info['contra_no']		= $POST['contra_entry_no'];
				$info['contra_date']	= date('Y-m-d',strtotime($POST['contra_entry_date']));
				$info['remark']			= $POST['remark'];
				$info['cdate']			= date("Y-m-d H:i:s");
				$info['user_id']		= $_SESSION['user_id'];
				$info['company_id']		= $_SESSION['company_id'];
				//var_dump($info);
			$inserpoid=add_record('tbl_contra', $info, $dbcon);
				
				$info_trn['contra_id']				= $inserpoid;
				$info_trn['contra_trn_status']= 0;
			$updateid=update_record('tbl_contra_trn', $info_trn,"contra_trn_status=3 and user_id=".$_SESSION['user_id'] , $dbcon);
			
			insert_general_book1($dbcon,$inserpoid);
				
				if(isset($POST['save_print']))
				{
					$arr['printstatus']=$POST['print_status'];
					$arr['msg']="1";
					$arr['eid']=$inserpoeid;
				}
				else
				{
					if($inserpoid)
					{	
						$arr['msg']="1";							
					}
					else
						$arr['msg']="0";
				}
			echo json_encode($arr);					
		 
		}		
		else if(strtolower($POST['mode']) == "edit") {
			 
				$info['contra_no']		= $POST['contra_entry_no'];
				$info['contra_date']	= date('Y-m-d',strtotime($POST['contra_entry_date']));
				$info['remark']			= $POST['remark'];
				$info['cdate']			= date("Y-m-d H:i:s");
				$info['user_id']		= $_SESSION['user_id'];
				$info['company_id']		= $_SESSION['company_id'];
			$updateid=update_record('tbl_contra', $info,"contra_id=".$POST['contra_id'] , $dbcon);
			
				insert_general_book1($dbcon,$POST['contra_id']);	
				if(isset($POST['save_print']))
				{
					$arr['printstatus']=$POST['print_status'];
					$arr['msg']="update";
					$arr['eid']=$POST['eid'];
				}
				else
				{
					if($updateid)
					{	
						$arr['msg']="update";
						
					}
					else{
						$arr['msg']=0;
					}
				}
			echo json_encode($arr);	
			 
		}
		else if(strtolower($POST['mode']) == "delete") {
			
			$query="select * from  tbl_contra_trn as mst 
			where contra_trn_status=0 and mst.contra_id=".$POST['eid'];
		    $result=$dbcon->query($query);
			$table_name="tbl_contra_trn";
			while($rel=mysqli_fetch_assoc($result))
			{
				$general_book_id=get_general_book_id($dbcon,$table_name,$rel['contra_trn_id'],$rel['ledger_id']);
				
				$info12['genral_book_status']		= 2;
				$update=update_record('tbl_general_book', $info12,"general_book_id=".$general_book_id, $dbcon);
				
			}
			
			$info['contra_status']				= 2;
			$info1['contra_trn_status']		= 2;
			$update=update_record('tbl_contra', $info,"contra_id=".$POST['eid'], $dbcon);	
			$updatepurchaseid=update_record('tbl_contra_trn', $info1,"contra_id=".$POST['eid'], $dbcon);
			
			if($update)
				echo "1";	
			else
				echo "0";			
		}
		else if(strtolower($POST['mode']) == "fieldadd") {
		
				$info1['entry_type']	= $POST['entry_type'];
				//$info1['description']		= text_rnremove($_POST['product_des']);
				$info1['ledger_id']		= $POST['ledger_id'];
				$info1['amount']		= $POST['amount'];
				$info1['user_id']		= $_SESSION['user_id'];
				$info1['cdate']			= date("Y-m-d H:i:s");
				$info1['company_id']	= $_SESSION['company_id'];
			$table='tbl_contra_trn';$tableid='contra_trn_id';
			if(!empty($POST['contra_id']))
			{
				$info1['contra_id']= $POST['contra_id'];
			}
			else
			{
				$info1['contra_trn_status']	= 3;
			}
			if(empty($POST['edit_id']))
			{
				$inserid=add_record($table, $info1, $dbcon);
			}
			else
			{
				$updateid=update_record($table, $info1,$tableid."=".$POST['edit_id'] , $dbcon);
			}
			//var_dump($info1);
		}
		else if(strtolower($POST['mode']) == "load_tempoutward") {
			if(!empty($POST['contra_id'])){
				$query="select mst.*,led.l_name as ledger_name,bt.balance_type_name
				from  tbl_contra_trn as mst 
				left join tbl_ledger as led on led.l_id=mst.ledger_id
				left join mst_balance_type as bt on bt.balance_typeid=mst.entry_type
				where contra_trn_status=0 and contra_id=".$POST['contra_id'];
			}else{
				$query="select mst.*,led.l_name as ledger_name,bt.balance_type_name
				from  tbl_contra_trn as mst 
				left join tbl_ledger as led on led.l_id=mst.ledger_id
				left join mst_balance_type as bt on bt.balance_typeid=mst.entry_type
				where contra_trn_status=3 and mst.user_id=".$_SESSION['user_id'];
			}
		    $result=$dbcon->query($query);
			
			echo ' <div class="form-group">
					<div class="col-md-12 col-xs-12">
						<table cellspacing="10" style="border-spacing:10px;" class="table12 display table  table-striped table-bordered">
						<tr id="field">
							<th class="text-center" width="15%">Entry Type</th>
							<th class="text-center"width="25%">Ledger Name</th>
							<th class="text-center"width="15%">Cr Amount</th>
							<th class="text-center"width="15%">Dr Amount</th>
							<th class="text-center"width="10%">Action</th>
						</tr>';
		if(mysqli_num_rows($result)>0)
		{
			$i=1;$dr_amount=0;$cr_amount=0;
			while($rel=mysqli_fetch_assoc($result))
			{
			 echo '<tr id="fieldtr'.$id.'" >
					<td data-label="Entry Type" style="vertical-align:top;text-align:left">
						'.$rel['balance_type_name'].'
					</td>
					<td data-label="Ledger Name" style="vertical-align:top;" class="text-center">
						'.$rel['ledger_name'].'
							
					</td>';
					if($rel['entry_type']=="1"){
						echo '<td data-label="Cr Amount" style="vertical-align:top;"class="text-center">
						'.$rel['amount'].'
						</td>';
					}else{
						echo '<td data-label="Cr Amount" style="vertical-align:top;height: 35px;"class="text-center">
						</td>';
					}
					if($rel['entry_type']=="2"){
						echo '<td data-label="Dr Amount" style="vertical-align:top;" class="text-center">
						'.$rel['amount'].'
						</td>';
					}else{
						echo '<td data-label="Dr Amount" style="vertical-align:top;height: 35px;" class="text-center">
						</td>';
					}
					echo '<td data-label="Action" style="vertical-align:top">
							<button type="button" class="btn btn-round btn-warning btn-xs" onclick="edit_data('.$rel['contra_trn_id'].');" id="fieldr'.$i.'"><i class="fa fa-pencil"></i></button>
							<button type="button" class="btn btn-round btn-danger btn-xs" onclick="delete_data('.$rel['contra_trn_id'].');" id="fieldremove'.$i.'"><i class="fa fa-times"></i></button>
						</td>	
					</tr>';
					if($rel['entry_type']=="1"){
						$cr_amount+=$rel['amount'];
					}else{
						$cr_amount=$cr_amount;
					}
					if($rel['entry_type']=="2"){
						$dr_amount+=$rel['amount'];
					}else{
						$dr_amount=$dr_amount;
					}
					
			$i++;
			}
			echo '<tr>
					<td data-label="" colspan="2" style="vertical-align:top;"class="text-center"></td>
					<td  data-label="Total Cr Amount" style="vertical-align:top;"class="text-center">'.$cr_amount.'</td>
					<td data-label="Total Dr Amount" style="vertical-align:top;"class="text-center">'.$dr_amount.'</td>
					<td style="vertical-align:top;"class="text-center"></td>
				</tr>
				<input type="hidden" name="cr_amount" id="cr_amount" value="'.$cr_amount.'" />
				<input type="hidden" name="dr_amount" id="dr_amount" value="'.$dr_amount.'" />
			';
		}
		else{
		echo '<tr><td colspan="10" class="text-center">NO DATA FOUND</td></tr>';
			}
			echo '
	 
		</table>			 
							</div>
                           
							</div>	';
		}
		else if(strtolower($POST['mode'])== "preedit")
		{
			
			$q = $dbcon -> query("SELECT * FROM tbl_contra_trn WHERE contra_trn_id = '$POST[id]'");
			$r = $q->fetch_assoc();
			echo json_encode($r);
			//var_dump($r);
		}
		else if(strtolower($POST['mode'])=="delete_data")
		{
			$row=array();
				$info['contra_trn_status']=2;	
			$updateid=update_record("tbl_contra_trn", $info,"contra_trn_id=".$POST['eid'] , $dbcon);
			
			$info1['genral_book_status']=2;	
			$updateid1=update_record("tbl_general_book", $info1," table_name='tbl_contra_trn' and table_id=".$POST['eid'] , $dbcon);
			
			if($updateid)
				$row['res']="1";
			else
				$row['res']="0";
			echo json_encode($row);
		}
		else if(strtolower($POST['mode'])== "add_genral_book"){
			$uid=insert_general_book1($dbcon,$POST['contra_id']);
			//var_dump($uid);
		}else if(strtolower($POST['mode'])== "get_series_no")
		{
			$query="select * from tbl_invoicetype where status=0 and type_id=10 and company_id=".$_SESSION['company_id'];
			$result=$dbcon->query($query);
			$row=mysqli_fetch_assoc($result);
			echo $row['invoicetype_id'];
		
		}
		else if(strtolower($POST['mode'])== "load_invoiceno")
		{
			$row=array();
			$query1="select * from  tbl_invoicetype where invoicetype_id=".$POST['typeid'];
			$rows=mysqli_fetch_assoc($dbcon->query($query1));
			$id=$rows['taxinvoice_start'];
			$id=$id+1;
			//$start=(date('m')<'04') ? date('y',strtotime(date('y').'-1 year')) : date('y');
			//$end = $start+1;
			if($rows['invoice_format']=='2')
			{
				$row['invoiceno']= str_pad($id,4,"0",STR_PAD_LEFT).$rows['format_value'];
			}
			else if($rows['invoice_format']=='1')
			{
				$row['invoiceno']=$rows['format_value'].str_pad($id,3,"0",STR_PAD_LEFT);
			}
			else if($rows['invoice_format']=='3'){
				$row['invoiceno']=$rows['format_value'].str_pad($id,3,"0",STR_PAD_LEFT).$rows['end_format_value'];
			}
			else{
				$row['invoiceno']=str_pad($id,3,"0",STR_PAD_LEFT);
			}
			$row['challanno']=str_pad($id,3,"0",STR_PAD_LEFT);
			echo json_encode($row);
		}


function insert_general_book1($dbcon,$contra_id)
{
	$qry122="select * from tbl_contra as cert where contra_status=0 and contra_id=".$contra_id;
	$ro12=$dbcon->query($qry122);
	$rea=mysqli_fetch_assoc($ro12);
	
	$query="select * from  tbl_contra_trn as mst 
			where contra_trn_status=0 and mst.contra_id=".$contra_id;
		    $result=$dbcon->query($query);
			$table_name="tbl_contra_trn";
			while($rel=mysqli_fetch_assoc($result))
			{
				$general_book_id=get_general_book_id($dbcon,$table_name,$rel['contra_trn_id'],$rel['ledger_id']);
				
				$info1['ref_date']		= date("Y-m-d",strtotime($rea['contra_date']));
				$info1['table_name']	= $table_name;
				$info1['table_id']		= $rel['contra_trn_id'];
				$info1['entry_type']	= $rel['entry_type'];
				$info1['ledger_id']		= $rel['ledger_id'];
				$info1['amount']		= $rel['amount'];
				$info1['user_id']		= $_SESSION['user_id'];
				$info1['cdate']			= date("Y-m-d H:i:s");
				$info1['company_id']	= $_SESSION['company_id'];
				
				if(empty($general_book_id)){
					$inserid_gen=add_record("tbl_general_book", $info1, $dbcon);
				}else{
					$updateid=update_record('tbl_general_book', $info1,"general_book_id=".$general_book_id , $dbcon);
				}
				
				//$inserid=add_record("tbl_general_book", $info1, $dbcon);
			}
}		
?>