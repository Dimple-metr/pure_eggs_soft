<?php
session_start();
include_once("../config/config.php");
include_once("../config/session.php");
include("../include/function_database_query.php");
include_once("../include/common_functions.php");
$form="Group Details";

$sub_group_id = $_REQUEST['group_id'];
//$start_date = date('Y-04-01');
//$end_date = date("Y-m-d");
$dates = get_financial_year();
extract($dates);
//echo $start_date.'-'.$end_date;
$where_date = (isset($end_date) && !empty($end_date)) ? " between '".$start_date."' and '".$end_date."'" : " < '".$start_date."'" ;

$ca_entries = array();
if($sub_group_id){
        $group_array = array();
        $group_name = $dbcon->query("SELECT g_name as ledger_name FROM `tbl_group` WHERE g_status = 0 AND `g_id` = ".$sub_group_id)->fetch_object()->ledger_name;
        
        $sub_groups_qry = "SELECT g_id,g_name AS ca_sub_group FROM `tbl_group` WHERE g_status = 0 And `g_pid`= ".$sub_group_id;
        $result = mysqli_query($dbcon,$sub_groups_qry);
        $sub_groups = mysqli_fetch_all($result,MYSQLI_ASSOC);
        
        if($sub_groups){
            foreach ($sub_groups as $value) {
                $group_str = get_group_legder($dbcon,$value['g_id'],$start_date,$end_date);
                array_push($group_array, $group_str);
            }
        } 
        
        $sub_ledger_qry = "SELECT l_id FROM `tbl_ledger` WHERE l_status = 0 AND l_group IN (".$sub_group_id.")";
        //$sub_ledger = $dbcon->query($sub_ledger_qry)->fetch_object()->sub_ledger;
        $result = mysqli_query($dbcon, $sub_ledger_qry);
        $sub_ledger_array = mysqli_fetch_all($result,MYSQLI_ASSOC);
              
        $amount = 0;
        foreach ($sub_ledger_array as $sub_ledger) {
            $ca_qry = "select sum(opn_balance) as opening_balance,balance_typeid,sum(debitamount) as debitamount ,
                sum(creditamount) as creditamount,l_name as ledger_name, l_id as ledger_id
                from tbl_ledger as cust 
                left join (select sum(amount) as debitamount,invoice.ledger_id 
                        from tbl_general_book as invoice 
                        where genral_book_status=0 and table_name!='tbl_ledger' 
                            and entry_type= 2 and invoice.company_id=".$_SESSION['company_id']." 
                            and ref_date < '".date('Y-m-d',strtotime($start_date))."' 
                        group by invoice.ledger_id) as debitinvoice on debitinvoice.ledger_id=cust.l_id 
                left join (select sum(amount) as creditamount,rec.ledger_id 
                        from tbl_general_book as rec 
                        where genral_book_status= 0 and table_name!='tbl_ledger' 
                            and entry_type= 1 and company_id=".$_SESSION['company_id']."
                            and ref_date < '".date('Y-m-d',strtotime($start_date))."' 
                        group by rec.ledger_id) as creditcust on creditcust.ledger_id = cust.l_id 
                where l_status = 0 AND company_id = ".$_SESSION['company_id']." 
                    AND cust.l_id IN (".$sub_ledger['l_id'].")
                    group by cust.l_id
                    Order by l_name ASC ";
            
                $result = mysqli_query($dbcon, $ca_qry);
                $ca_result = mysqli_fetch_all($result,MYSQLI_ASSOC);

                //echo '<pre>';        print_r($ca_result); exit;
                if($ca_result){
                    foreach ($ca_result as $value) {
                        $balance_type = $value['balance_typeid'];
                        //$balance_type = ($sub_group_id == SUNDRY_DEBTORS) ? '2' : $value['balance_typeid'];
                        $op_balance = ($balance_type=="2" ? ($value['opening_balance']) : -$value['opening_balance']);
                        $balance = $op_balance + ($value['debitamount']-$value['creditamount']);
                        
                        
                        $payment_qry = 'select sum(amount) as amount, entry_type from tbl_general_book as payment
				where payment.genral_book_status=0 and payment.company_id='.$_SESSION['company_id'].' 
                                    and ref_date>="'.date('Y-m-d',strtotime($start_date)).'" 
                                    and ref_date<="'.date('Y-m-d',strtotime($end_date)).'" 
                                    and table_name!="tbl_ledger" and payment.ledger_id='.$value['ledger_id'].' 
                                GROUP BY payment.entry_type
                                ORDER BY payment.ref_date
                                ';
                        $result = mysqli_query($dbcon, $payment_qry);
                        $payment_result = mysqli_fetch_all($result,MYSQLI_ASSOC);
                        
                        if($payment_result){
                            foreach ($payment_result as $payment) {
                                //echo '<br/>'.$payment['entry_type'].':'.$balance.'---'.$payment['amount'];
                                if($payment['entry_type']==2){
                                    $balance += $payment['amount'];

                                }else{
                                    $balance -= $payment['amount'];
                                }
                            }
                        }
                        $ca_value['group_id'] = $sub_group_id;
                        $ca_value['ledger_id'] = $value['ledger_id'];
                        $ca_value['ledger_name'] = $value['ledger_name'];
                        $ca_value['ca_value'] = abs($balance);
                        array_push($ca_entries, $ca_value);
                    }
                }
        }
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
                                <header class="panel-heading"><strong><?=$group_name?></strong></header>	
                                <div class="panel-body">
                                    <div class="row">
                                        <div class="col-md-12"  style="margin-top:10px;">
                                            <table  class="display table table-bordered table-striped">
                                                <thead>
                                                    <tr>
                                                        <th>Ledger Name</th>
                                                        <th style="text-align: right;">Balance</th>
                                                    </tr>
                                                </thead>
                                            <?php
                                            $ca_value = $group_value = 0;
                                            if($group_array && !empty($group_array)){
                                                foreach ($group_array as $group_entry) { 
                                                    $amount = $group_entry["amount"];
                                                    ?>
                                                    <tr>
                                                        <td><a style="color: inherit;" href="group_detail_view.php?group_id=<?= $group_entry["group_id"]?>" target="_blank"><?= $group_entry["group_name"] ?></a></td>
                                                        <td style="text-align: right;"> <?= indian_number($amount,2); ?></td>
                                                    </tr>
                                                <?php 
                                                $group_value += $amount;
                                                }
                                            }
                                            //exit;
                                            if($ca_entries && !empty($ca_entries)){ 
                                                foreach ($ca_entries as $ca_entry) { 
                                                        $ca_amount = $ca_entry["ca_value"];
                                                        $style = ($ca_amount < 0) ? 'style="color: red;"' : ''; 
                                                        if($ca_amount > 0){ ?>
                                                                <tr>
                                                                    <td><a style="color: inherit;" href="ledger_monthly_view.php?ledger_id=<?= $ca_entry["ledger_id"]?>" target="_blank"><?= $ca_entry["ledger_name"] ?></a></td>
                                                                    <td style="text-align: right;" <?= $style ?>> <?= indian_number($ca_amount,2) ?></td>
                                                                </tr>
                                                    <?php  
                                                        }
                                                    $ca_value += $ca_amount;
                                                }
                                            } 
                                            $total_amount = ($ca_value + $group_value); 
                                            ?>
                                                <tr>
                                                    <td style="text-align: right;">Total</td>
                                                    <td style="text-align: right;"><b><?= indian_number($total_amount,2) ?></b></td>
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

