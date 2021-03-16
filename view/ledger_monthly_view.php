<?php
session_start();
include_once("../config/config.php");
include_once("../config/session.php");
include("../include/function_database_query.php");
include_once("../include/common_functions.php");
$form="Ledger Details";

$ledger_id = $_REQUEST['ledger_id'];
$ledger_name = $dbcon->query("select l_name from tbl_ledger where l_id=".$ledger_id)
                    ->fetch_object()->l_name;
$dates = get_financial_year();
extract($dates);
$where_date = (isset($end_date) && !empty($end_date)) ? " between '".$start_date."' and '".$end_date."'" : " < '".$start_date."'" ;

$ca_entries = array();
if($ledger_id){
        $ca_qry = "select sum(opn_balance) as opening_balance,balance_typeid,sum(debitamount) as debitamount ,
                sum(creditamount) as creditamount,l_name as ledger_name, l_id as ledger_id
                from tbl_ledger as cust 
                left join (select sum(amount) as debitamount,invoice.ledger_id 
                        from tbl_general_book as invoice 
                        where genral_book_status=0 and table_name!='tbl_ledger' 
                            and entry_type= 2 and invoice.company_id=".$_SESSION['company_id']." 
                            and ref_date < '".$start_date."' 
                        group by invoice.ledger_id) as debitinvoice on debitinvoice.ledger_id=cust.l_id 
                left join (select sum(amount) as creditamount,rec.ledger_id 
                        from tbl_general_book as rec 
                        where genral_book_status= 0 and table_name!='tbl_ledger' 
                            and entry_type= 1 and company_id=".$_SESSION['company_id']."
                            and ref_date < '".$start_date."' 
                        group by rec.ledger_id) as creditcust on creditcust.ledger_id = cust.l_id 
                where l_status = 0 AND company_id = ".$_SESSION['company_id']." 
                    AND cust.l_id IN (".$ledger_id.")
                    group by cust.l_id
                    Order by l_name ASC ";
        
                $result = mysqli_query($dbcon, $ca_qry);
                $ca_result = mysqli_fetch_all($result,MYSQLI_ASSOC);

                //echo '<pre>';        print_r($ca_result); exit;
                if($ca_result){
                    foreach ($ca_result as $value) {
                        $balance_type = $value['balance_typeid'];
                        //$balance_type = ($sub_group_id == SUNDRY_DEBTORS) ? '2' : $value['balance_typeid'];
                        $op_balance = ($balance_type=="2" ? ($value['opening_balance']) :-$value['opening_balance']);
                        $balance = $op_balance + ($value['debitamount']-$value['creditamount']);
                        
                        $payment_qry = 'SELECT entry_type,sum(amount) as amount,MONTHNAME(payment.ref_date) month_name, MONTH(payment.ref_date) as month
                                FROM tbl_general_book as payment
                                WHERE payment.genral_book_status=0 and payment.company_id='.$_SESSION['company_id'].' 
                                    and ref_date>="'.date('Y-m-d',strtotime($start_date)).'" 
                                    and ref_date<="'.date('Y-m-d',strtotime($end_date)).'" 
                                    and table_name!="tbl_ledger" and payment.ledger_id='.$value['ledger_id'].'
                                GROUP BY month,entry_type
                                ORDER BY payment.ref_date
                                ';
                        $result = mysqli_query($dbcon, $payment_qry);
                        $payment_result = mysqli_fetch_all($result,MYSQLI_ASSOC);
                        
                        if($payment_result){
                            foreach ($payment_result as $payment) {
                                $ca_entries[$payment['month_name']]['month'] = $payment['month'];
                                if($payment['entry_type']==2){
                                    $ca_entries[$payment['month_name']]['debit'] = $payment['amount'];

                                }else{
                                    $ca_entries[$payment['month_name']]['credit'] = $payment['amount'];
                                }
                            }
                        }
                    }
                }
                //echo '<pre>'; print_r($ca_entries);
        }

?>
<!DOCTYPE html>
<html lang="en">
    <head>
            <?php include_once('../include/include_css_file.php');?>
    </head>
    <body>
        <section id="container">
        <?php include_once('../include/include_top_menu.php');?>
        <?php include_once('../include/left_menu.php');?>
            <section id="main-content">
                <section class="wrapper">
                    <div class="row">
                        <div class="col-lg-12">
                            <section class="panel">
                                <header class="panel-heading"><h3><?=$mode.' '.$form?></h3></header>	
                                <div class="">
                                    <ul class="breadcrumb">
                                        <li><a href="<?=ROOT.'dashboard'?>"><i class="fa fa-home"></i> Home</a></li>
                                        <li><?=$form?> Report</li>
                                    </ul>
                                </div>
                            </section>
                        </div>	
                    </div>
                    <div class="row">			
                        <div class="col-sm-12">
                            <section class="panel">
                                <header class="panel-heading"><strong><?=$ledger_name?></strong></header>	
                                <div class="panel-body">
                                    <div class="row">
                                        <div class="col-md-12"  style="margin-top:10px;">
                                            <table  class="display table table-bordered table-striped">
                                                <thead>
<!--                                                    <tr>
                                                        <td colspan="3" style="text-align: right;"><b>Opening Balance</b></td>
                                                        <td><?= $balance; ?></td>
                                                    </tr>-->
                                                    <tr>
							<th width="55%" style="text-align:center">Month</th>
                                                        <th width="15%" style="text-align:center">Debit</th>
							<th width="15%" style="text-align:center">Credit</th>
                                                        <th width="15%" style="text-align:center">Closing Balance</th>
                                                    </tr>
                                                </thead>
                                            <?php
                                            $ca_value = 0; $opening_balance = 0; $closing_bal = 0;
                                            if($ca_entries && !empty($ca_entries)){ 
                                                foreach ($ca_entries as $month => $amount) {
                                                    if($opening_balance == 0){
                                                        $opening_balance = $balance;
                                                    }
                                                    $closing_bal = $opening_balance + ($amount['debit'] - $amount['credit']);
                                                    ?>
                                                    <tr>
                                                        <td><a style="color: inherit;" href="ledger_detail_view.php?ledger_id=<?= $ledger_id?>&month=<?= $amount['month']?>" target="_blank"><?= $month ?></a></td>
                                                        <td style="text-align: right;"> <?= indian_number($amount['debit'],2) ?></td>
                                                        <td style="text-align: right;"> <?= indian_number($amount['credit'],2) ?></td>
                                                        <td style="text-align: right;"> <?= indian_number(abs($closing_bal),2) ?></td>
                                                    </tr>
                                                <?php
                                                    $opening_balance = $closing_bal;
                                                    $debit_total += $amount['debit'];
                                                    $credit_total += $amount['credit'];
                                                }
                                            } ?>
                                                <tr>
                                                    <td style="text-align: left;"><strong>Grand Total</strong></td>
                                                    <td style="text-align: right;"><strong><?= indian_number($debit_total,2) ?></strong></td>
                                                    <td style="text-align: right;"><strong><?= indian_number($credit_total,2) ?></strong></td>
                                                    <td style="text-align: right;"><strong><?= indian_number(abs($closing_bal),2) ?></strong></td>
                                                </tr>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </section>
                        </div>
                    </div>
                </section>
            </section>
        <?php include_once('../include/footer.php');?>
        </section>
        <?php include_once('../include/include_js_file.php');?>  
    </body>
</html>

