<?php

session_start();
$AJAX = true;
include("../../config/config.php");
//error_reporting(E_ALL);
include("../../config/session.php");
include("../../include/function_database_query.php");
//include_once("../../include/profit_loss_functions.php");
include_once("../../include/common_functions.php");

if($_POST != NULL) {
	$POST = bulk_filter($dbcon,$_POST);
}
else {
	$POST = bulk_filter($dbcon,$_GET);
}
	
		if(strtolower($POST['mode']) == "load_profit_loss") {
			/* $set="select * from tbl_company as comp where company_id=".$_SESSION['company_id'];
			$set_head=mysqli_fetch_assoc($dbcon->query($set));
			
		$s_date=explode(' - ',$POST['date']);
		$start_date=date('Y-m-d',strtotime($s_date[0]));
		$end_date=date('Y-m-d',strtotime($s_date[1])); */
			$userid1="";$uty="";
			if(!empty($POST['employee_id'])){
				$userid1= " and pro.user_id=".$POST['employee_id'];
			}
			if($_SESSION['user_type']==2){
				$uty=" and user_id!=".$_SESSION['user_id'];
			}
			$total_array = array();
			 $query1="select * from users as pro where active=0 and user_type!=1 ".$uty." ".$userid1." and company_id = $_SESSION[company_id] order by TRIM(user_name) ASC";
				$rs_dispatch=$dbcon->query($query1);	
				$str="";
				$str.='<table style="font-size:15px;border-collapse: collapse;border-top:none;" cellpadding="0" cellspacing="0" width="100%" >';
				$k=0;
				while($rel=mysqli_fetch_assoc($rs_dispatch))
				{	
					
                                    $query="select mst.stock_out_trn_id,sout.stock_out_id,product.product_name,product.product_mst_rate as rate,cat.unit_name,mst.*,(select IFNULL(sum(transfer_qty),0) from tbl_stock_transfer_trn as ptrn
                                        left join tbl_stock_transfer as strn on strn.stock_transfer_id=ptrn.stock_transfer_id
                                         where ptrn.product_id=mst.product_id and stock_transfer_trn_status=0 and ptrn.user_id=".$rel['user_id']." and strn.stock_transfer_date='".date('Y-m-d',strtotime($POST['stock_date']))."') as transfer_out,(select IFNULL(sum(transfer_qty),0) from tbl_stock_transfer_trn as ptrn
                                         left join tbl_stock_transfer as strn on strn.stock_transfer_id=ptrn.stock_transfer_id
                                         where ptrn.product_id=mst.product_id and stock_transfer_trn_status=0 and strn.employee_id=".$rel['user_id']." and strn.stock_transfer_date='".date('Y-m-d',strtotime($POST['stock_date']))."') as transfer_in,sitrn.return_qty,sitrn.sales_qty,sitrn.replace_qty,sout.done_status
                                    from  tbl_stock_out_trn as mst 
                                    left join tbl_stock_out as sout on sout.stock_out_id=mst.stock_out_id
                                    left join unit_mst as cat on cat.unitid=mst.unit_id 
                                    left join tbl_product as product on product.product_id=mst.product_id 
                                    left join tbl_stock_in_trn as sitrn on sitrn.stock_out_trn_id=mst.stock_out_trn_id
                                    where stock_out_trn_status=0  and sout.status=0 and stock_out_date='".date('Y-m-d',strtotime($POST['stock_date']))."' and employee_id=".$rel['user_id'];
                                    $result=$dbcon->query($query);
						
				if(mysqli_num_rows($result)>0){
					$str.='	<tr class="userc" >
								<td colspan="9" style="font-size:20px;border-top:1px #101010 solid;border-left:1px #101010 solid;border-right:1px #101010 solid;border-bottom:1px #101010 solid;color:#251919;">
									<center>
										<strong>'.$rel["user_name"].'</strong>
									</center>
								</td>
							</tr>
						<tr>
								<th class="text-center stop sbottom sleft sright titc" width="25%" >Product Name</th>
								<th class="text-center stop sbottom sleft sright titc"width="8%" > Allocate Qty</th>
								<th class="text-center stop sbottom sleft sright titc" width="8%" >Transfer Out</th>
								<th class="text-center stop sbottom sleft sright titc" width="8%" ">Transfer In</th>
								<!--<th class="text-center stop sbottom sleft sright titc" width="6%" >Per</th>-->
								<th class="text-center stop sbottom sleft sright titc" width="8%" >Return Qty</th>
								<th class="text-center stop sbottom sleft sright titc" width="8%" >Sales Qty</th>
								<th class="text-center stop sbottom sleft sright titc" width="8%" >Replace Qty</th>
								<th class="text-center stop sbottom sleft sright titc" width="8%" >Pending Qty</th>
							</tr>';
					while($re=mysqli_fetch_assoc($result))
					{	
                                            $product_name = $re['product_name'];
						if($re['done_status']==1){
							$return_qty=$re['return_qty'];
							$sales_qty=$re['sales_qty'];
							$replace_qty=$re['replace_qty'];
						}else{
							//$sales_qty=load_sales_qty($dbcon,$POST['stock_date'],$re['product_id'],$rel['user_id'],2);
							
							$sales_qty=load_sales_qty($dbcon,$POST['stock_date'],$re['product_id'],$rel['user_id'],2);
							$replace_qty=load_sales_qty($dbcon,$POST['stock_date'],$re['product_id'],$rel['user_id'],7);
					
							$return_qty=$re['return_qty'];
						}
					
						$pending_qty=((($re['product_qty']+$re['transfer_in'])-$re['transfer_out'])-($return_qty+$sales_qty+$replace_qty));
						
						if($pending_qty=="0.00"){$pending_qty="";}
						if($replace_qty=="0.00"){$replace_qty="";}
						if($sales_qty=="0.00"){$sales_qty="";}
						if($return_qty=="0.00"){$return_qty="";}
						if($re['transfer_in']=="0.00"){$transfer_in="";}else{$transfer_in=$re['transfer_in'];}
						if($re['transfer_out']=="0.00"){$transfer_out="";}else{$transfer_out=$re['transfer_out'];}
						if($re['product_qty']=="0.00"){$product_qty="";}else{$product_qty=$re['product_qty'];}
						$str.='<tr>
                                                        <td class="text-center stop sbottom sleft sright" width="25%">
                                                                '.$product_name.'
                                                                '.(!empty($re['description'])?'<br/><strong>Desc.</strong> :'.$re['description']:'').'
                                                        </td>
                                                        <td class="text-center stop sbottom sleft sright" width="8%">'.number_format($product_qty, 0, '.', '').'</td>
                                                        <td class="text-center stop sbottom sleft sright" width="8%">'.number_format($transfer_out, 0, '.', '').'</td>
                                                        <td class="text-center stop sbottom sleft sright" width="8%">'.number_format($transfer_in, 0, '.', '').'</td>
                                                        <!--<td class="text-center stop sbottom sleft sright" width="6%">'.$re['unit_name'].'</td>-->
                                                        <td class="text-center stop sbottom sleft sright" width="8%">'.number_format($return_qty, 0, '.', '').'</td>
                                                        <td class="text-center stop sbottom sleft sright" width="8%">'.number_format($sales_qty, 0, '.', '').'</td>
                                                        <td class="text-center stop sbottom sleft sright" width="8%">'.number_format($replace_qty, 0, '.', '').'</td>
                                                        <td class="text-center stop sbottom sleft sright" width="8%">'.number_format($pending_qty, 0, '.', '').'</td>
                                                </tr>';
                                                $total_array[$product_name]['allocate_qty'] += $product_qty; 
                                                $total_array[$product_name]['transfer_out'] += $transfer_out;
                                                $total_array[$product_name]['transfer_in'] += $transfer_in;
                                                $total_array[$product_name]['return_qty'] += $return_qty;
                                                $total_array[$product_name]['sales_qty'] += $sales_qty;
                                                $total_array[$product_name]['replace_qty'] += $replace_qty;
                                                $total_array[$product_name]['pending_qty'] += $pending_qty;
					}
                                        
                                        
					
						
						$k++;
					}
				
				}
                                //p($total_array);
                                if($total_array){
                                   $str.='<tr class="userc" >
                                                <td colspan="9" style="font-size:20px;border-top:1px #101010 solid;border-left:1px #101010 solid;border-right:1px #101010 solid;border-bottom:1px #101010 solid;color:#251919;">
                                                        <center>
                                                                <strong>Product Wise Summary</strong>
                                                        </center>
                                                </td>
                                        </tr>
                                        <tr>
                                                <th class="text-center stop sbottom sleft sright titc" width="25%" >Product Name</th>
                                                <th class="text-center stop sbottom sleft sright titc"width="8%" > Allocate Qty</th>
                                                <th class="text-center stop sbottom sleft sright titc" width="8%" >Transfer Out</th>
                                                <th class="text-center stop sbottom sleft sright titc" width="8%" ">Transfer In</th>
                                                <th class="text-center stop sbottom sleft sright titc" width="8%" >Return Qty</th>
                                                <th class="text-center stop sbottom sleft sright titc" width="8%" >Sales Qty</th>
                                                <th class="text-center stop sbottom sleft sright titc" width="8%" >Replace Qty</th>
                                                <th class="text-center stop sbottom sleft sright titc" width="8%" >Pending Qty</th>
                                        </tr>'; 
                                foreach($total_array as $productname => $total){
                                    $str.='<tr>
                                            <td class="text-center stop sbottom sleft sright" width="25%">
                                                    '.$productname.'
                                            </td>
                                            <td class="text-center stop sbottom sleft sright" width="8%">'.number_format($total['allocate_qty'], 0, '.', '').'</td>
                                            <td class="text-center stop sbottom sleft sright" width="8%">'.number_format($total['transfer_out'], 0, '.', '').'</td>
                                            <td class="text-center stop sbottom sleft sright" width="8%">'.number_format($total['transfer_in'], 0, '.', '').'</td>
                                            <td class="text-center stop sbottom sleft sright" width="8%">'.number_format($total['return_qty'], 0, '.', '').'</td>
                                            <td class="text-center stop sbottom sleft sright" width="8%">'.number_format($total['sales_qty'], 0, '.', '').'</td>
                                            <td class="text-center stop sbottom sleft sright" width="8%">'.number_format($total['replace_qty'], 0, '.', '').'</td>
                                            <td class="text-center stop sbottom sleft sright" width="8%">'.number_format($total['pending_qty'], 0, '.', '').'</td>
                                    </tr>';
                                }
                                }
				if($k==0){
					$str.='<tr>
								<td class="text-center stop sbottom sleft sright" width="25%">
									No Data Found.......
								</td>
							</tr>';
				}
			$str.='</table>';
			echo $str;
		}
		
		
   

?>