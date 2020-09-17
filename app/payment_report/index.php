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
    $set="select * from tbl_company as comp where company_id=".$_SESSION['company_id'];
    $set_head=mysqli_fetch_assoc($dbcon->query($set));
			
    $s_date=explode(' - ',$POST['date']);
    $start_date=date('Y-m-d',strtotime($s_date[0]));
    $end_date=date('Y-m-d',strtotime($s_date[1])); 
    $payment_mode_id="";
    if(!empty($POST['paymentmodeid'])){
            $payment_mode_id= " and rec.payment_mode_id=".$POST['paymentmodeid'];
    }
    if(!empty($POST['employee_id'])){
            $user_id= " and rec.user_id=".$POST['employee_id'];
    }
			
    $query1="select rec.receipt_no,rec.receipt_date,inv.invoice_no,inv.invoice_date,led.l_name as cust_name,
            pled.l_name as pmode,rec.total_paid_amount,us.user_name as emp_name from tbl_receipt as rec
                left join tbl_receipt_trn as rtrn on rtrn.receipt_id=rec.receipt_id
                left join tbl_invoice as inv on inv.invoice_id=rtrn.invoice_id
                left join tbl_ledger as led on led.l_id=rec.cust_id
                left join tbl_ledger as pled on pled.l_id=rec.payment_mode_id
                left join users as us on us.user_id=rec.user_id
                where rec.status=0 ".$payment_mode_id." ".$user_id." 
                    and rec.payment_type=1 and rec.company_id = $_SESSION[company_id] 
                    and rec.receipt_date between '".date('Y-m-d',strtotime($start_date))."' and '".date('Y-m-d',strtotime($end_date))."' 
                group by rec.receipt_id 
                order by TRIM(rec.receipt_id) ASC";
    $rs_dispatch=$dbcon->query($query1);	
    $str="";
				$str.='<table style="font-size:15px;border-collapse: collapse;border-top:none;" cellpadding="0" cellspacing="0" width="100%" >';
				$str.='<tr>
							<th class="text-center stop sbottom sleft sright titc" width="10%" >Recipt No</th>
							<th class="text-center stop sbottom sleft sright titc"width="10%" > Recipt Date</th>
							<th class="text-center stop sbottom sleft sright titc" width="25%" >Party Name</th>
							<th class="text-center stop sbottom sleft sright titc" width="12%" >Payment Mode</th>
							<th class="text-center stop sbottom sleft sright titc" width="15%" >Employee Name</th>
							<th class="text-center stop sbottom sleft sright titc" width="10%" >Amount</th>
						</tr>';
				$k=0;$tamount=0;
				if(mysqli_num_rows($rs_dispatch)>0){
					
					while($rel=mysqli_fetch_assoc($rs_dispatch))
					{	
						$str.='<tr>
							<td class="text-center stop sbottom sleft sright " width="10%" >'.$rel["receipt_no"].'</td>
							<td class="text-center stop sbottom sleft sright "width="10%" >'.date('d-m-Y',strtotime($rel["receipt_date"])).'</td>
							<td class="text-center stop sbottom sleft sright " width="25%" >'.$rel["cust_name"].'</td>
							<td class="text-center stop sbottom sleft sright " width="12%" >'.$rel["pmode"].'</td>
							<td class="text-center stop sbottom sleft sright " width="12%" >'.$rel["emp_name"].'</td>
							<td class="text-right stop sbottom sleft sright " width="10%" >'.$rel["total_paid_amount"].'</td>
						</tr>';
						
						$tamount=$tamount+$rel["total_paid_amount"];
					}
					$str.='<tr>
							<td colspan="5" class="text-right stop sbottom sleft sright " style="font-size:16px" width="10%" ><strong>Total</strong></td>
							<td class="text-right stop sbottom sleft sright " style="font-size:16px" width="10%" ><strong>'.$tamount.'</strong></td>
						</tr>';
				}else{
					$str.='<tr>
								<td colspan="6" class="text-center stop sbottom sleft sright" width="25%">
									No Data Found.......
								</td>
							</tr>';
				}
			$str.='</table>';
			echo $str;
		}
		
		
   

?>