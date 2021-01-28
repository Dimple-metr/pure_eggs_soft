<?php

/* 
 * Author : Dimple Panchal
 * to get P&L value for balance sheet
 */

session_start();
$AJAX = true;
include("../../config/config.php");
//error_reporting(E_ALL);
include("../../config/session.php");
include("../../include/function_database_query.php");
include_once("../../include/profit_loss_functions.php");

if(strtolower($_POST['mode']) == "load_profit_loss") {
    
    $start_date = date('Y-m-d',strtotime($_POST['start_date']));
    $end_date = date('Y-m-d',strtotime($_POST['end_date']));
    $where_date = (isset($end_date) && !empty($end_date)) ? " between '".$start_date."' and '".$end_date."'" : " < '".$start_date."'" ;
		
    $purchase_account = get_purchase_account($dbcon,$start_date,$end_date);
    $sales_account = get_sales_account($dbcon,$start_date,$end_date);
    $opening_stock = get_opening_stock($dbcon, $start_date, $end_date);
    $direct_expence = get_direct_expance($dbcon,$where_date);
    $direct_income_value = direct_income_value($dbcon,$where_date);
    $indirect_expences = get_indirect_expenses($dbcon,$start_date,$end_date);
    $indirect_income_value = indirect_income_value($dbcon,$where_date);
	
    $inventory_management = $dbcon->query("SELECT inventory_management FROM tbl_company as comp WHERE company_id=".$_SESSION['company_id'])
                ->fetch_object()->inventory_management;

    if($inventory_management){
        $closing_balance = $dbcon->query("SELECT sum(closing_balance) as closing_bal
                                FROM tbl_closing_balance 
                                WHERE closing_balance_date = ( SELECT MAX(closing_balance_date) 
                                    FROM tbl_closing_balance 
                                    WHERE status = ".ACTIVE." AND closing_balance_date <= '".$end_date."')")
                                ->fetch_object()->closing_bal;
    
    } else {
        $closing_balance = (float)($purchase_account['value'] - $sales_account['value']);
    }
    $closing_stock = number_format($closing_balance, 2, '.', '');
    $exp = $opening_stock['value'] + $purchase_account['value'] + $direct_expance['value'];
    $incom = $closing_stock + $sales_account['value'] + $direct_income_value;
    $net_incom = $net_exp = $gross_profit = $gross_loss = $net_profit = $net_loss = 0;
    
    if($exp > $incom){
            $gross_loss = $exp - $incom;
            $total_exp = $exp;
            $total_income = $gross_loss + $incom;
            $net_exp = ($gross_loss + $indirect_expences['value']) - $indirect_income_value;
    } else {
            $gross_profit = $incom - $exp;
            $total_exp = $gross_profit + $exp;
            $total_income = $incom;
            $net_incom = ($gross_profit + $indirect_income_value) - $indirect_expences['value'];
    }
				
    if($net_exp>$net_incom){
            $net_loss = $net_exp - $net_incom;
            $gtotal_exp = $gross_loss + $indirect_expences['value'];
            $gtotal_profit = $gross_profit + $indirect_income_value + $net_loss;
    }else{
            $net_profit = $net_incom - $net_exp;
            $gtotal_exp = $gross_loss + $indirect_expences['value'] + $net_profit;
            $gtotal_profit = $gross_profit + $indirect_income_value;
    }
				
    $gross_profit = number_format((float)$gross_profit, 2, '.', '');
    $gross_loss = number_format((float)$gross_loss, 2, '.', '');
    $total_exp = number_format((float)$total_exp, 2, '.', '');
    $total_income = number_format((float)$total_income, 2, '.', '');
    $net_profit = number_format((float)$net_profit, 2, '.', '');
    $net_loss = number_format((float)$net_loss, 2, '.', '');
}
$data = array();
$data['net_profit'] = $net_profit;
$data['net_loss'] = $net_loss;

echo json_encode($data);
