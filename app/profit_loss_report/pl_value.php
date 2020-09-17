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
    $companyName = get_company_name($dbcon);
			
    //$s_date = explode(' - ',$_POST['date']);
    //$start_date = date('Y-m-d',strtotime($s_date[0]));
//    $end_date = date('Y-m-d',strtotime($s_date[1]));
//    $year = date('Y', strtotime($end_date));
//    $start_date = date($year.'-04-01',strtotime($end_date));
    
    $start_date = date('Y-m-d',strtotime($_POST['start_date']));
    $end_date = date('Y-m-d',strtotime($_POST['end_date']));
    $where_date = (isset($end_date) && !empty($end_date)) ? " between '".$start_date."' and '".$end_date."'" : " < '".$start_date."'" ;
		
    $purchase_ac_value = purchase_ac_value($dbcon,$where_date);
    $sales_ac_value = sales_ac_value($dbcon,$where_date);
    $opening_stock_value = opening_stock_value($dbcon,$start_date);
    $direct_expance_value = direct_expance_value($dbcon,$where_date);
    $direct_income_value = direct_income_value($dbcon,$where_date);
    $indirect_expances_value = indirect_expances_value($dbcon,$where_date);
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
        $closing_balance = (float)($purchase_ac_value - $sales_ac_value);
    }
    $closing_stock = number_format($closing_balance, 2, '.', '');
    $exp = $opening_stock_value + $purchase_ac_value + $direct_expance_value;
    $incom = $closing_stock + $sales_ac_value + $direct_income_value;
    $net_incom = $net_exp = $gross_profit = $gross_loss = $net_profit = $net_loss = 0;
    
    if($exp > $incom){
            $gross_loss = $exp - $incom;
            $total_exp = $exp;
            $total_income = $gross_loss + $incom;
            $net_exp = ($gross_loss + $indirect_expances_value) - $indirect_income_value;
    } else {
            $gross_profit = $incom - $exp;
            $total_exp = $gross_profit + $exp;
            $total_income = $incom;
            $net_incom = ($gross_profit + $indirect_income_value) - $indirect_expances_value;
    }
				
    if($net_exp>$net_incom){
            $net_loss = $net_exp - $net_incom;
            $gtotal_exp = $gross_loss + $indirect_expances_value;
            $gtotal_profit = $gross_profit + $indirect_income_value + $net_loss;
    }else{
            $net_profit = $net_incom - $net_exp;
            $gtotal_exp = $gross_loss + $indirect_expances_value + $net_profit;
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
$data['net_profit'] = $gross_profit;
$data['net_loss'] = $net_loss;

echo json_encode($data);
