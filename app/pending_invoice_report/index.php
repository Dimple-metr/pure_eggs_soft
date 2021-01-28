<?php

session_start();
$AJAX = true;
include("../../config/config.php");
//error_reporting(E_ALL);
include("../../config/session.php");
include("../../include/function_database_query.php");
//include_once("../../include/profit_loss_functions.php");
include_once("../../include/common_functions.php");
$start_date = date('01-m-Y');
$end_date = date('t-m-Y');

if($_POST != NULL) {
	$POST = bulk_filter($dbcon,$_POST);
}
else {
	$POST = bulk_filter($dbcon,$_GET);
}
	
if(strtolower($POST['mode']) == "pending_invoice_report") {
    $set="select * from tbl_company as comp where company_id=".$_SESSION['company_id'];
    $set_head=mysqli_fetch_assoc($dbcon->query($set));
		
    $condition = '';
    if(!empty($POST['customer_id'])){
        $customer_name = $dbcon->query("Select l_name as customer_name from tbl_ledger WHERE l_id =".$POST['customer_id'])->fetch_object()->customer_name;
    }
    $start_date = date('Y-m-d',strtotime($_POST['start_date']));
    $end_date = date('Y-m-d',strtotime($_POST['end_date']));
    $where_date = (isset($end_date) && !empty($end_date)) ? " between '".$start_date."' and '".$end_date."'" : " < '".date('Y-m-d')."'" ;
    //echo $condition;
    $query='Select * from ( (select "Invoice" as type,1 as ref_type,invoice_date as ref_date,invoice_no as ref_no,
        invoice_id as ref_id,g_total as ref_amount, 
        (select IFNULL(sum(total_amount),0) as qty from tbl_receipt_trn as trn where status=0 and inv.invoice_id=trn.invoice_id) as pay_amount, 
        inv.cdate from tbl_invoice as inv where inv.cdate '.$where_date.' and invoice_status=0 AND cust_id='.$POST['customer_id'].' 
        and inv.g_total>(select IFNULL(sum(total_amount),0) as qty from tbl_receipt_trn as trn where status=0 and inv.invoice_id=trn.invoice_id)) ) as data order by ref_date,ref_type DESC';

    $result = mysqli_query($dbcon,$query);
    $pending_invoices = mysqli_fetch_all($result,MYSQLI_ASSOC);
    //echo '<pre>'; print_r($pending_invoices); exit;
    
    $str="";
    $str.='<table style="font-size:15px;border-collapse: collapse;border-top:none;" cellpadding="0" cellspacing="0" width="100%" >';
    $str.='<tr>
                <th colspan="3" class="text-center stop sbottom sleft sright titc" width="20%" >'.$customer_name.'</th>
                <th colspan="2" class="text-center stop sbottom sleft sright titc" width="20%" >Date : '.date('d-m-Y', strtotime($start_date)).' To '.date('d-m-Y', strtotime($end_date)).'</th>
        </tr>';
    $str.='<tr>
                <th class="text-center stop sbottom sleft sright titc" width="20%" >Invoice No</th>
                <th class="text-center stop sbottom sleft sright titc"width="20%" > Invoice Date</th>
                <th class="text-center stop sbottom sleft sright titc" width="20%" >Amount</th>
                <th class="text-center stop sbottom sleft sright titc" width="20%" >Paid Amount</th>
                <th class="text-center stop sbottom sleft sright titc" width="20%" >Due amount</th>
        </tr>';
    $k=0;$tamount=0;
    if(count($pending_invoices)>0){

        foreach ($pending_invoices as $pending_invoice)
            {	
                $due_amount = ($pending_invoice["ref_amount"]-$pending_invoice["pay_amount"]);
                $str.='<tr>
                        <td class="text-center stop sbottom sleft sright " width="10%" >'.$pending_invoice["ref_no"].'</td>
                        <td class="text-center stop sbottom sleft sright "width="10%" >'.date('d-m-Y',strtotime($pending_invoice["ref_date"])).'</td>
                        <td class="text-right stop sbottom sleft sright " width="25%" >'.number_format((float)$pending_invoice["ref_amount"],2).'</td>
                        <td class="text-right stop sbottom sleft sright " width="12%" >'.number_format((float)$pending_invoice["pay_amount"],2).'</td>
                        <td class="text-right stop sbottom sleft sright " width="12%" >'.number_format((float)$due_amount,2).'</td>
                </tr>';
						
                $total_amount = $total_amount + $pending_invoice["ref_amount"];
                $total_paid = $total_paid + $pending_invoice["pay_amount"];
                $total_due = $total_due + $due_amount;
            }
            $str.='<tr>
                            <td colspan="2" class="text-right stop sbottom sleft sright " style="font-size:16px" width="10%" ><strong>Total</strong></td>
                            <td class="text-right stop sbottom sleft sright " style="font-size:16px" width="10%" ><strong>'.number_format((float)$total_amount,2).'</strong></td>
                            <td class="text-right stop sbottom sleft sright " style="font-size:16px" width="10%" ><strong>'.number_format((float)$total_paid,2).'</strong></td>
                            <td class="text-right stop sbottom sleft sright " style="font-size:16px" width="10%" ><strong>'.number_format((float)$total_due,2).'</strong></td>
                    </tr>';
    } else {
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