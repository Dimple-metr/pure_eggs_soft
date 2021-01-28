<?php

session_start();
$AJAX = true;
include("../../config/config.php");
//error_reporting(E_ALL);
include("../../config/session.php");
include("../../include/function_database_query.php");
include_once("../../include/profit_loss_functions.php");

$POST = ($_POST != NULL)? bulk_filter($dbcon,$_POST) : bulk_filter($dbcon,$_GET); 
		
if(strtolower($POST['mode']) == "load_profit_loss") {
    $companyName = get_company_name($dbcon);
			
    $start_date = date('Y-m-d',strtotime($POST['start_date']));
    $end_date = date('Y-m-d',strtotime($POST['end_date']));
    $where_date = (isset($end_date) && !empty($end_date)) ? " between '".$start_date."' and '".$end_date."'" : " < '".$start_date."'" ;
		
    $opening_stock = get_opening_stock($dbcon, $start_date, $end_date);
    $purchase_account = get_purchase_account($dbcon,$start_date,$end_date);
    $sales_account = get_sales_account($dbcon,$start_date,$end_date);
    $indirect_expances = get_indirect_expenses($dbcon,$start_date,$end_date);
    $direct_expance = get_direct_expance($dbcon,$where_date);
    $direct_income_value = direct_income_value($dbcon,$where_date);
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
        $closing_balance = ($purchase_account['value'] - $sales_account['value']);
    }
    $closing_stock = indian_number($closing_balance, 2);
    $exp = $opening_stock['value'] + $purchase_account['value'] + $direct_expance['value'];
    $incom = $closing_stock + $sales_account['value'] + $direct_income_value;
    $net_incom = $net_exp = $gross_profit = $gross_loss = $net_profit = $net_loss = 0;
    
    if($exp > $incom){
            $gross_loss = $exp - $incom;
            $total_exp = $exp;
            $total_income = $gross_loss + $incom;
            $net_exp = ($gross_loss + $indirect_expances['value']) - $indirect_income_value;
    } else {
            $gross_profit = $incom - $exp;
            $total_exp = $gross_profit + $exp;
            $total_income = $incom;
            $net_incom = ($gross_profit + $indirect_income_value) - $indirect_expances['value'];
    }
				
    if($net_exp>$net_incom){
            $net_loss = $net_exp - $net_incom;
            $gtotal_exp = $gross_loss + $indirect_expances['value'];
            $gtotal_profit = $gross_profit + $indirect_income_value + $net_loss;
    }else{
            $net_profit = $net_incom - $net_exp;
            $gtotal_exp = $gross_loss + $indirect_expances['value'] + $net_profit;
            $gtotal_profit = $gross_profit + $indirect_income_value;
    }
				
    $gross_profit = number_format((float)$gross_profit, 2, '.', '');
    $gross_loss = number_format((float)$gross_loss, 2, '.', '');
    $total_exp = number_format((float)$total_exp, 2, '.', '');
    $total_income = number_format((float)$total_income, 2, '.', '');
    $net_profit = number_format((float)$net_profit, 2, '.', '');
    $net_loss = number_format((float)$net_loss, 2, '.', '');
    $gtotal_exp = number_format((float)$gtotal_exp, 2, '.', '');
    $gtotal_profit = number_format((float)$gtotal_profit, 2, '.', '');
			
    $indirect_income_value_details =
    $direct_income_value_details = 
    $closing_stock_value_details = '';
    
    if(strtolower($POST['show_details']) == "true") {
        $indirect_income_value_details = indirect_income_value_details($dbcon,$where_date);
        $direct_income_value_details = direct_income_value_details($dbcon,$where_date);
        $closing_stock_value_details = closing_stock_value_details($closing_stock);
    }
				
    $str = '';
    $str.='<table style="font-size:15px;border-collapse: collapse;border-top:none;" cellpadding="0" cellspacing="0" width="100%" >
            <tr>
                <td colspan="4" style="font-size:20px;border-top:1px #101010 solid;border-left:1px #101010 solid;border-right:1px #101010 solid;border-bottom:1px #101010 solid;color:#251919;">
                    <center>
                            <strong>Profit And Loss</strong>
                    </center>
                </td>
            </tr>
            <tr>
                <td colspan="2" style="font-size:15px;border-left:1px #101010 solid;border-right:1px #101010 solid;color:#251919;">
                        <center><strong>'.$companyName.'</strong></center>
                </td>
                <td colspan="2" style="font-size:15px;border-left:1px #101010 solid;border-right:1px #101010 solid;color:#251919;">
                        <center><strong>'.$companyName.'</strong></center>
                </td>
            </tr>
            <tr>
                <td style="font-size:15px;border-bottom:1px #101010 solid;border-left:1px #101010 solid;color:#251919;">
                        <strong>Particulars</strong>
                </td>
                <td style="font-size:15px;border-right:1px #101010 solid;border-bottom:1px #101010 solid;color:#251919;">
                        <center>'.$companyName.'</center>
                </td>
                <td style="font-size:15px;border-left:1px #101010 solid;border-bottom:1px #101010 solid;color:#251919;">
                        <strong>Particulars</strong>
                </td>
                <td style="font-size:15px;border-right:1px #101010 solid;border-bottom:1px #101010 solid;color:#251919;">
                        <center>'.$companyName.'</center>
                </td>
            </tr>
            <tr>
                <td colspan="2" style="font-size:15px;border-left:1px #101010 solid;border-right:1px #101010 solid;color:#251919;vertical-align: top;">
                    <table style="font-size:15px;border-collapse: collapse;border-top:none;" cellpadding="0" cellspacing="0" width="100%" >
                        <tr>
                            <td><strong>Opening Stock</strong></td>
                            <td style="text-align: right;">'. indian_number((float)$opening_stock['value'],2).'</td>
                        </tr>
                        <tr class="descripc">
                            <td colspan="2">'.$opening_stock['entries'].'</td>
                        </tr>
                        <tr>
                            <td><strong>Purchase A/C.</strong></td>
                            <td style="text-align: right;">'.indian_number((float)$purchase_account['value'], 2).'</td>
                        </tr>
                        <tr class="descripc">
                            <td colspan="2">'.$purchase_account['entries'].'</td>
                        </tr>
                        <tr>
                            <td><strong>Direct Expense</strong></td>
                            <td style="text-align: right;">'.indian_number((float)$direct_expance['value'], 2).'</td>
                        </tr>
                        <tr class="descripc">
                            <td colspan="2">'.$direct_expance['entries'].'</td>
                        </tr>';
                        if(!empty($gross_profit) && $gross_profit!="0.00"){
                                $str.='<tr>
                                                <td ><strong>Gross Profit<strong></td>
                                                <td style="text-align: right;">'.indian_number((float)$gross_profit, 2).'</td>
                                        </tr>';
                        }else{
                                $str.='<tr height="20px">
                                                <td></td>
                                                <td style="text-align: right;"></td>
                                        </tr>';
                        }
                $str.='</table>
                </td>
                <td colspan="2" style="font-size:15px;border-left:1px #101010 solid;border-right:1px #101010 solid;color:#251919;vertical-align: top;">
                    <table style="font-size:15px;border-collapse: collapse;border-top:none;" cellpadding="0" cellspacing="0" width="100%" >
                        <tr>
                                <td><strong>Sales A/C<strong></td>
                                <td style="text-align: right;">'.indian_number((float)$sales_account['value'], 2).'</td>
                        </tr>
                        <tr class="descripc">
                                <td colspan="2">'.$sales_account['entries'].'</td>
                        </tr>
                        <tr>
                                <td><strong>Direct Income<strong></td>
                                <td style="text-align: right;">'.indian_number((float)$direct_income_value, 2).'</td>
                        </tr>
                        <tr class="descripc">
                                <td colspan="2" >'.$direct_income_value_details.'</td>
                        </tr>
                        <tr>
                                <td><strong>Closing Stock<strong></td>
                                <td style="text-align: right;">'.$closing_stock.'</td>
                        </tr>
                        <tr class="descripc">
                                <td colspan="2">'.$closing_stock_value_details.'</td>
                        </tr>';
                        if(!empty($gross_loss) && $gross_loss!="0.00"){
                                $str.='<tr>
                                            <td><strong>Gross Loss<strong></td>
                                            <td style="text-align: right;">'.indian_number((float)$gross_loss, 2).'</td>
                                        </tr>';
                        }else{
                                $str.='<tr height="20px">
                                            <td></td>
                                            <td style="text-align: right;"></td>
                                        </tr>';
                        }
                $str.='</table>
                </td>
            </tr>';
            $str .= '<tr>
                        <td style="border-left: 1px #101010 solid;"></td>
                        <td style="text-align: right;border-bottom: 2px  #101010 solid;border-top: 2px #101010 solid;color: #101010;">'.indian_number((float)$total_exp, 2).'</td>
                        <td style="border-left: 1px #101010 solid;border-left: 1px #101010 solid;"></td>
                        <td style="text-align: right;border-bottom: 2px  #101010 solid;border-top: 2px #101010 solid;border-right: 1px  #101010 solid;color: #101010;">'.indian_number((float)$total_income, 2).'</td>
                    </tr>';
            $str.='<tr>	
                    <td colspan="2" style="font-size:15px;border-left:1px #101010 solid;border-right:1px #101010 solid;color:#251919;vertical-align: top;">
                        <table style="font-size:15px;border-collapse: collapse;border-top:none;" cellpadding="0" cellspacing="0" width="100%" >';
                            if(!empty($gross_loss) && $gross_loss!="0.00"){
                                $str.='<tr>
                                            <td><strong>Gross Loss<strong></td>
                                            <td style="text-align: right;">'.indian_number((float)$gross_loss, 2).'</td>
                                        </tr>';
                            }
                            $str.='<tr>
                                        <td><strong>Indirect Expense<strong></td>
                                        <td style="text-align: right;">'.indian_number((float)$indirect_expances['value'], 2).'</td>
                                    </tr>
                                    <tr class="descripc">
                                            <td colspan="2">'.$indirect_expances['entries'].'</td>
                                    </tr>';
								
                            if(!empty($net_profit) && $net_profit!="0.00"){
                                $str.='
                                <tr height="20px">
                                        <td></td>
                                        <td style="text-align: right;"></td>
                                </tr>
                                <tr>
                                        <td><strong>Net Profit<strong></td>
                                        <td style="text-align: right;">'.indian_number((float)$net_profit, 2).'</td>
                                </tr>';

                            } else {
                            $str.=' <tr height="20px">
                                        <td></td>
                                        <td style="text-align: right;"></td>
                                    </tr>
                                    <tr height="20px">
                                        <td></td>
                                        <td style="text-align: right;"></td>
                                    </tr>';
                            }
                    $str.=' </table>
                    </td>
                    <td colspan="2" style="font-size:15px;border-left:1px #101010 solid;border-right:1px #101010 solid;color:#251919;vertical-align: top;">
                        <table style="font-size:15px;border-collapse: collapse;border-top:none;" cellpadding="0" cellspacing="0" width="100%" >';
                            if(!empty($gross_profit) && $gross_profit!="0.00"){
                                $str.='<tr>
                                            <td><strong>Gross Profit<strong></td>
                                            <td style="text-align: right;">'.indian_number((float)$gross_profit, 2).'</td>
                                        </tr>';
                            }else{
                                $str.='<tr height="20px">
                                            <td></td>
                                            <td style="text-align: right;"></td>
                                        </tr>';
                            }
                            $str.='<tr>
                                    <td><strong>Indirect Income<strong></td>
                                    <td style="text-align: right;">'.indian_number((float)$indirect_income_value, 2).'</td>
                            </tr>
                            <tr class="descripc">
                                    <td colspan="2">'.$indirect_income_value_details.'</td>
                            </tr>';
                            if(!empty($net_loss) && $net_loss!="0.00"){
                                $str.='<tr height="20px">
                                            <td></td>
                                            <td style="text-align: right;"></td>
                                        </tr>
                                        <tr>
                                            <td><strong>Net Loss<strong></td>
                                            <td style="text-align: right;">'.indian_number((float)$net_loss, 2).'</td>
                                        </tr>';
                            }else{
                                $str.='<tr height="20px">
                                            <td></td>
                                            <td style="text-align: right;"></td>
                                        </tr>
                                        <tr height="20px">
                                                <td></td>
                                                <td style="text-align: right;"></td>
                                        </tr>';
                            }
                        $str.='</table>
                    </td>
                </tr>';
            $str.='<tr height="20px">
                        <td style="border-top: 1px #101010 solid;border-bottom: 1px  #101010 solid;border-left: 1px #101010 solid;color: #101010;">Total</td>
                        <td style="text-align: right;border-top: 1px #101010 solid;border-bottom: 1px #101010 solid;border-right: 1px #101010 solid;color: #101010;">'.indian_number((float)$gtotal_exp, 2).'</td>
                        <td style="border-top: 1px #101010 solid;border-bottom: 1px #101010 solid;border-left: 1px #101010 solid;color: #101010;">Total</td>
                        <td style="text-align: right;border-top: 1px #101010 solid;border-bottom: 1px #101010 solid;border-right: 1px #101010 solid;color: #101010;">'.indian_number((float)$gtotal_profit, 2).'</td>
                    </tr>';
        $str.='</table>';
    echo $str;
}
?>