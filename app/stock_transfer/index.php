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
			if($_SESSION['user_type']!=2){
				$where.=" and po.user_id=".$_SESSION['user_id'];
			}
			$where.="  and stock_transfer_date>='".date('Y-m-d',strtotime($s_date[0]))."' AND stock_transfer_date<='".date('Y-m-d',strtotime($s_date[1]))."'";
			$appData = array();
			$i=1;
			$aColumns = array('stock_transfer_id','us.user_name as to_emp','us1.user_name as from_emp','po.employee_id','stock_transfer_no','stock_transfer_date','status','po.cdate','po.user_id');
			$sIndexColumn = "stock_transfer_id";
			$isWhere = array("status = 0 and po.company_id=".$_SESSION['company_id']."".$where);
			$sTable = "tbl_stock_transfer as po";			
			$isJOIN = array("left join users as us1 on us1.user_id=po.employee_id","left join users as us on us.user_id=po.user_id");
			$hOrder = "po.stock_transfer_id desc";
			include('../../include/pagging.php');
			$appData = array();
			$id=1;
			foreach($sqlReturn as $row) {
				$row_data = array();
				$row_data[] = $row['stock_transfer_no'];
				$row_data[] = date('d M, Y',strtotime($row['stock_transfer_date']));
				if($_SESSION['user_type']=="2"){
					$row_data[] = $row['from_emp'];
				}
				$row_data[] = $row['to_emp'];
				
				$addpayment='';$delete='';$edit='';
				
					
					$delete='<button class="btn btn-xs btn-danger" data-original-title="Delete" data-toggle="tooltip" data-placement="top" onClick="delete_invoice('.$row['stock_transfer_id'].')"><i class="fa fa-trash-o"></i></button>';
					//$view='<a class="btn btn-xs btn-info" data-original-title="View" data-toggle="tooltip" data-placement="top" href="'.ROOT.'purchase_view/'.$row['po_id'].'"><i class="fa fa-eye"></i></a> ';
					
					//$edit='<a class="btn btn-xs btn-warning" data-original-title="Edit" data-toggle="tooltip" data-placement="top" href="'.ROOT.'stock_in_edit/'.$row['stock_transfer_id'].'"><i class="fa fa-pencil"></i></a>';
					$row_data[] = $edit.' '.$delete.' '.$view;
			 
			$appData[] = $row_data;
			$id++;
			}
			$output['aaData'] = $appData;
			echo json_encode( $output );
		}
		else if(strtolower($POST['mode']) == "add") {
					update_series_no($dbcon,"5");
							$info['stock_transfer_no']	= $POST['stock_transfer_no'];
							$info['employee_id']	= $POST['employee_id'];
							$info['stock_transfer_date']	= date('Y-m-d',strtotime($POST['stock_transfer_date']));
							$info['remark']			= text_rnremove($_POST['remark']);
							$info['cdate']			= date("Y-m-d H:i:s");
							$info['user_id']		= $_SESSION['user_id'];
							$info['company_id']		= $_SESSION['company_id'];
							$inserpoid=add_record('tbl_stock_transfer', $info, $dbcon);
				
					foreach ($POST['stock_out_trn_id'] as $key => $name)
						{
							//if($POST['transfer_qty'][$key]>0 || $POST['transfer_qty'][$key]!=""){
								$info1['stock_transfer_id']	= $inserpoid;
								$info1['stock_out_id']		= $POST['stock_out_id'][$key];
								$info1['stock_out_trn_id']	= $POST['stock_out_trn_id'][$key];
								$info1['product_id']		= $POST['product_id'][$key];
								$info1['product_qty']		= $POST['product_qty'][$key];
								$info1['unit_id']			= $POST['unit_id'][$key];
								$info1['transfer_qty']		= $POST['transfer_qty'][$key];
								$info1['cdate']				= date("Y-m-d H:i:s");
								$info1['user_id']			= $_SESSION['user_id'];
								//var_dump($info1);
								$insertrnid=add_record('tbl_stock_transfer_trn', $info1, $dbcon);
							//}
						}
				
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
							$info['stock_transfer_no']	= $POST['stock_transfer_no'];
							$info['employee_id']	= $POST['employee_id'];
							$info['stock_transfer_date']	= date('Y-m-d',strtotime($POST['stock_transfer_date']));
							$info['remark']			= text_rnremove($_POST['remark']);
							$info['cdate']				= date("Y-m-d H:i:s");
							$info['user_id']			= $_SESSION['user_id'];
							$info['company_id']		= $_SESSION['company_id'];
							$updateid=update_record('tbl_stock_transfer', $info,"stock_transfer_id=".$POST['eid'] , $dbcon);
					$deleteid=delete_record('tbl_stock_transfer_trn',"stock_transfer_id=".$POST['eid'], $dbcon);
						foreach ($POST['stock_out_trn_id'] as $key => $name)
						{
							if($POST['transfer_qty'][$key]>0 || $POST['transfer_qty'][$key]!=""){
								$info1['stock_transfer_id']		= $POST['eid'];
								$info1['stock_out_id']		= $POST['stock_out_id'][$key];
								$info1['stock_out_trn_id']	= $POST['stock_out_trn_id'][$key];
								$info1['product_id']		= $POST['product_id'][$key];
								$info1['product_qty']		= $POST['product_qty'][$key];
								$info1['unit_id']			= $POST['unit_id'][$key];
								$info1['transfer_qty']		= $POST['transfer_qty'][$key];
								$info1['cdate']				= date("Y-m-d H:i:s");
								$info1['user_id']			= $_SESSION['user_id'];
								
								$insertrnid=add_record('tbl_stock_transfer_trn', $info1, $dbcon);
							
							}
						}
							
					
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
			$info1['stock_transfer_trn_status']		= 2;
			
			$updateinvoiceid=update_record('tbl_stock_transfer', $info,"stock_transfer_id=".$POST['eid'] , $dbcon);	
			$updatetrancationid=update_record('tbl_stock_transfer_trn', $info1,"stock_transfer_id=".$POST['eid'] , $dbcon);	
			
			
			if($updateinvoiceid)
				echo "1";	
			else
				echo "0";			
		}
		else if(strtolower($POST['mode']) == "load_tempoutward") {
			//$employee_id=$POST['employee_id'];
			$stock_date=$POST['stock_date'];
			if(!empty($POST['stock_transfer_id'])){
				 $query="select stock_transfer_trn_id,product.product_name,cat.unit_name,sotrn.*,mst.stock_transfer_id,mst.transfer_qty,(select IFNULL(sum(transfer_qty),0) from tbl_stock_transfer_trn as ptrn where ptrn.stock_out_trn_id=mst.stock_out_trn_id and stock_transfer_trn_status=0 and stock_transfer_id!=".$POST['stock_transfer_id'].") as usedqty
				from  tbl_stock_transfer_trn as mst 
				left join tbl_stock_out_trn as sotrn on sotrn.stock_out_trn_id=mst.stock_out_trn_id
				left join unit_mst as cat on cat.unitid=sotrn.unit_id 
				left join tbl_product as product on product.product_id=sotrn.product_id  
				where stock_transfer_trn_status=0 and stock_transfer_id='".$POST['stock_transfer_id']."'";
			}else{
				  $query="select stock_out_trn_id,sout.stock_out_id,product.product_name,product.product_mst_rate as rate,cat.unit_name,mst.*,(select IFNULL(sum(transfer_qty),0) from tbl_stock_transfer_trn as ptrn
				left join tbl_stock_transfer as strn on strn.stock_transfer_id=ptrn.stock_transfer_id
				 where ptrn.product_id=mst.product_id and stock_transfer_trn_status=0 and ptrn.user_id=".$_SESSION['user_id']." and strn.stock_transfer_date='".date('Y-m-d',strtotime($stock_date))."') as usedqty,(select IFNULL(sum(transfer_qty),0) from tbl_stock_transfer_trn as ptrn
				 left join tbl_stock_transfer as strn on strn.stock_transfer_id=ptrn.stock_transfer_id
				 where ptrn.product_id=mst.product_id and stock_transfer_trn_status=0 and strn.employee_id=".$_SESSION['user_id']." and strn.stock_transfer_date='".date('Y-m-d',strtotime($stock_date))."') as transfer_in,(select IFNULL(sum(transfer_qty),0) from tbl_stock_transfer_trn as ptrn 
				  left join tbl_stock_transfer as strn on strn.stock_transfer_id=ptrn.stock_transfer_id
				  where ptrn.stock_out_trn_id=mst.stock_out_trn_id and stock_transfer_trn_status=0) as usedqty1
				from  tbl_stock_out_trn as mst 
				left join tbl_stock_out as sout on sout.stock_out_id=mst.stock_out_id
				left join unit_mst as cat on cat.unitid=mst.unit_id 
				left join tbl_product as product on product.product_id=mst.product_id  
				where stock_out_trn_status=0 and sout.done_status=0 and sout.status=0 and stock_out_date='".date('Y-m-d',strtotime($stock_date))."' and employee_id=".$_SESSION['user_id'];
			}
				$result=$dbcon->query($query);
			
			echo ' <div class="form-group">
						<div class="col-md-12 col-xs-12">
							<table cellspacing="10" style="border-spacing:10px;" class="table12 display table  table-striped table-bordered">
								<tr id="field" style="background-color: #acaeb1;color: #000d21;">
									<th class="text-center" width="25%">Product Name</th>
							<th class="text-center"width="8%">Allocate Qty</th>
							<th class="text-center"width="6%" style="display:none;">Per</th>
							<th class="text-center"width="8%">Pending Qty</th>
							<th class="text-center"width="8%">Transfer Qty</th>
						</tr>';
		if(mysqli_num_rows($result)>0)
		{
			$i=1;
			while($rel=mysqli_fetch_assoc($result))
			{
				//$salesqty=load_sales_qty($dbcon,$stock_date,$rel['product_id'],$_SESSION['user_id']);
				
				$salesqty=load_sales_qty($dbcon,$stock_date,$rel['product_id'],$_SESSION['user_id'],2);
				$replace_qty=load_sales_qty($dbcon,$stock_date,$rel['product_id'],$_SESSION['user_id'],7);
				$salesqty=$salesqty+$replace_qty;
				$usedqty=$rel['usedqty']+$salesqty;
				$maxtransfer=($rel['product_qty']+$rel['transfer_in'])-$usedqty;
			 echo '<tr id="fieldtr'.$i.'" >
					<td data-label="Product Name" style="vertical-align:top;text-align:left">
						'.$rel['product_name'].'
						'.(!empty($rel['description'])?'<br/><strong>Desc.</strong> :'.$rel['description']:'').'
						<input type="hidden" name="product_id[]" id="product_id'.$i.'" value="'.$rel['product_id'].'" />
					</td>
					<td data-label="Allocate Qty" style="vertical-align:top;" class="text-center">
						'.$rel['product_qty'].'
						<input type="hidden" name="product_qty[]" id="product_qty'.$i.'" value="'.$rel['product_qty'].'" />
					</td>
					<td data-label="Per" style="vertical-align:top;display:none;" class="text-center">';
							if(empty($rel['unit_name'])){
								echo '-';
								echo '<input type="hidden" name="unit_id[]" id="unit_id'.$i.'" value="'.$rel['unit_id'].'" />';
							}else{
								echo $rel['unit_name'];
								echo '<input type="hidden" name="unit_id[]" id="unit_id'.$i.'" value="'.$rel['unit_id'].'" />';
							}
							
					echo'</td>
					<td data-label="Pending Qty" style="vertical-align:top;" class="text-center">
						'.$maxtransfer.'
					</td>
					<td data-label="Transfer Qty" style="vertical-align:top;" class="text-center">
						<input id="transfer_qty'.$i.'" name="transfer_qty[]" type="text" class="form-control" title="Transfer Qty" value="'.$rel['transfer_qty'].'" placeholder="Transfer Qty" max="'.$maxtransfer.'" >
					</td>
					<input type="hidden" name="stock_out_trn_id[]" id="stock_out_trn_id'.$i.'" value="'.$rel['stock_out_trn_id'].'" />
					<input type="hidden" name="stock_out_id[]" id="stock_out_id'.$i.'" value="'.$rel['stock_out_id'].'" />
					<input type="hidden" name="i[]" id="i'.$i.'" value="'.$i.'" />
			</tr>';
			$i++;
			}
			/* echo '
				<tr style="background-color: #acaeb1;">
					<td style="text-align: right;color: black;vertical-align: middle;font-weight: 600;"> Total</td>
					<td>
						<input  type="text" id="grandtotal_product_qty" class="form-control g_tax" style="background-color: #acaeb1;color: #020000;border:3px solid #acaeb1;" title="Total" placeholder="Total" readonly >
						
						
					</td>
					<td></td>
					<td>
						<input  type="text" id="grandtotal_return_qty" class="form-control g_tax" title="Total" style="background-color: #acaeb1;color: #020000;border:3px solid #acaeb1;" placeholder="Total" readonly >
					</td>
					<td>
						<input  type="text" id="grandtotal_sales_qty" class="form-control g_tax" title="Total" style="background-color: #acaeb1;color: #020000;border:3px solid #acaeb1;" placeholder="Total" readonly >
 					</td>
					<td></td>
					<td>
						<input  type="text" id="grandtotal_amount" class="form-control g_tax" title="Total" style="background-color: #acaeb1;color: #020000;border:3px solid #acaeb1;" placeholder="Total" readonly >
					</td>
				</tr>
			'; */
		}
		else{
		echo '<tr><td colspan="10" class="text-center">NO DATA FOUND</td></tr>';
			}
			echo '
	 
		</table>			 
							</div>
                           
							</div>	';
		}
?>