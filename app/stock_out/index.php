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
			
			$where.="  and stock_out_date>='".date('Y-m-d',strtotime($s_date[0]))."' AND stock_out_date<='".date('Y-m-d',strtotime($s_date[1]))."'";
			$appData = array();
			$i=1;
			$aColumns = array('stock_out_id','us.user_name','stock_out_no','stock_out_date','po.employee_id','status','po.cdate','po.user_id');
			$sIndexColumn = "stock_out_id";
			$isWhere = array("status = 0".$where.check_user('po'));
			$sTable = "tbl_stock_out as po";			
			$isJOIN = array("left join users as us on us.user_id=po.employee_id");
			$hOrder = "po.stock_out_id desc";
			include('../../include/pagging.php');
			$appData = array();
			$id=1;
			foreach($sqlReturn as $row) {
				$row_data = array();
				$row_data[] = $row['stock_out_no'];
				$row_data[] = date('d M, Y',strtotime($row['stock_out_date']));
				$row_data[] = $row['user_name'];
				$addpayment='';$delete='';$edit='';
				
					
					$delete='<button class="btn btn-xs btn-danger" data-original-title="Delete" data-toggle="tooltip" data-placement="top" onClick="delete_invoice('.$row['stock_out_id'].')"><i class="fa fa-trash-o"></i></button>';
					//$view='<a class="btn btn-xs btn-info" data-original-title="View" data-toggle="tooltip" data-placement="top" href="'.ROOT.'purchase_view/'.$row['po_id'].'"><i class="fa fa-eye"></i></a> ';
					
					$edit='<a class="btn btn-xs btn-warning" data-original-title="Edit" data-toggle="tooltip" data-placement="top" href="'.ROOT.'stock_out_edit/'.$row['stock_out_id'].'"><i class="fa fa-pencil"></i></a>';
					$row_data[] = $edit.' '.$delete.' '.$view;
			 
			$appData[] = $row_data;
			$id++;
			}
			$output['aaData'] = $appData;
			echo json_encode( $output );
		}
		else if(strtolower($POST['mode']) == "add") {
						update_series_no($dbcon,"4");
					$info['stock_out_no']		= $POST['stock_out_no'];
					$info['employee_id']	= $POST['employee_id'];
					$info['stock_out_date']	= date('Y-m-d',strtotime($POST['stock_out_date']));
					$info['cdate']				= date("Y-m-d H:i:s");
					$info['user_id']			= $_SESSION['user_id'];
					$info['company_id']		= $_SESSION['company_id'];
					$inserpoid=add_record('tbl_stock_out', $info, $dbcon);
				
				$info_trn['stock_out_id']		= $inserpoid;
				$info_trn['stock_out_trn_status']= 0;
				$updateid=update_record('tbl_stock_out_trn', $info_trn,"stock_out_trn_status=3 and user_id=".$_SESSION['user_id'] , $dbcon);
				
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
							$info['stock_out_no']		= $POST['stock_out_no'];
							$info['employee_id']	= $POST['employee_id'];
							$info['stock_out_date']	= date('Y-m-d',strtotime($POST['stock_out_date']));
							$info['cdate']				= date("Y-m-d H:i:s");
							$info['user_id']			= $_SESSION['user_id'];
							$info['company_id']		= $_SESSION['company_id'];
							$updateid=update_record('tbl_stock_out_trn', $info,"stock_out_id=".$POST['eid'] , $dbcon);
							
					
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
					else
						$arr['msg']=0;
				}
			echo json_encode($arr);	
			 
		}
		else if(strtolower($POST['mode']) == "delete") {
			$info['status']		= 2;
			$info1['stock_out_trn_status']		= 2;
			$updateinvoiceid=update_record('tbl_stock_out', $info,"stock_out_id=".$POST['eid'] , $dbcon);	
			$updatetrancationid=update_record('tbl_stock_out_trn', $info1,"stock_out_id=".$POST['eid'] , $dbcon);	
			
			if($updateinvoiceid)
				echo "1";	
			else
				echo "0";			
		}
		
		else if(strtolower($POST['mode']) == "fieldadd") {
		
				$info1['product_id']		= $POST['product_id'];
				$info1['description']		= text_rnremove($_POST['product_des']);
				$info1['product_qty']		= $POST['product_qty'];
				$info1['unit_id']			= $POST['unit_id'];
				$info1['cdate']				= date("Y-m-d H:i:s");
				$info1['user_id']				= $_SESSION['user_id'];
				$table='tbl_stock_out_trn';$tableid='stock_out_trn_id';
			if(!empty($POST['stock_out_id']))
			{
				$info1['stock_out_id']= $POST['stock_out_id'];
			}
			else
			{
				$info1['stock_out_trn_status']	= 3;
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
			if(!empty($POST['stock_out_id'])){
				$query="select stock_out_trn_id,product.product_name,cat.unit_name,mst.* 
				from  tbl_stock_out_trn as mst 
				left join unit_mst as cat on cat.unitid=mst.unit_id 
				left join tbl_product as product on product.product_id=mst.product_id  
				where stock_out_trn_status=0 and stock_out_id=".$POST['stock_out_id'];
			}else{
				$query="select stock_out_trn_id,product.product_name,cat.unit_name,mst.* 
				from  tbl_stock_out_trn as mst 
				left join unit_mst as cat on cat.unitid=mst.unit_id 
				left join tbl_product as product on product.product_id=mst.product_id  
				where stock_out_trn_status=3 and mst.user_id=".$_SESSION['user_id'];
				
			}
				$result=$dbcon->query($query);
			
			echo ' <div class="form-group">
						<!--<div class="col-md-12 col-xs-12">-->
						<div class="col-md-2"></div>
							<div class="col-md-8 col-xs-11">
							<table cellspacing="10" style="border-spacing:10px;" class="table12 display table  table-striped table-bordered">
								<tr id="field">
									<th class="text-center" width="25%">Product Name</th>
							<th class="text-center"width="8%">Qty</th>
							<th class="text-center"width="6%" style="display:none;">Per</th>
							<th class="text-center"width="10%">Action</th>
						</tr>';
		if(mysqli_num_rows($result)>0)
		{
			$i=1;
			while($rel=mysqli_fetch_assoc($result))
			{
			 echo '<tr id="fieldtr'.$id.'" >
					<td data-label="Product Name" style="vertical-align:top;text-align:left">
						'.$rel['product_name'].'
						'.(!empty($rel['description'])?'<br/><strong>Desc.</strong> :'.$rel['description']:'').'
					</td>
					<td data-label="Qty" style="vertical-align:top;" class="text-center">
						'.$rel['product_qty'].'
					</td>
					<td data-label="Per" style="vertical-align:top;display:none;" class="text-center">';
							if(empty($rel['unit_name'])){
								echo '-';
							}else{
								echo $rel['unit_name'];
							}
					echo'</td>
					<!--<input type="hidden" name="amount[]" id="amount'.$i.'" value="'.$rel['total'].'"/>-->
											
					 <td data-label="Action" style="vertical-align:top">
							<button type="button" class="btn btn-round btn-warning btn-xs" onclick="edit_data('.$rel['stock_out_trn_id'].');" id="fieldremove'.$id.'"><i class="fa fa-pencil"></i></button>
							<button type="button" class="btn btn-round btn-danger btn-xs" onclick="delete_data('.$rel['stock_out_trn_id'].');" id="fieldremove'.$id.'"><i class="fa fa-times"></i></button>
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
                           
							</div>	';
		}
		else if(strtolower($POST['mode'])== "preedit")
		{
			$q = $dbcon -> query("SELECT mst.*,pro.product_name FROM tbl_stock_out_trn as mst left join tbl_product as pro on mst.product_id=pro.product_id WHERE stock_out_trn_id = '$POST[id]'");
			$r = $q->fetch_assoc();
			echo json_encode($r);
		}
		else if(strtolower($POST['mode'])=="delete_data")
		{
			$row=array();
				$info['stock_out_trn_status']=2;	
			$updateid=update_record("tbl_stock_out_trn", $info,"stock_out_trn_id=".$POST['eid'] , $dbcon);

			if($updateid)
				$row['res']="1";
			else
				$row['res']="0";
			echo json_encode($row);
		}
		
   

?>