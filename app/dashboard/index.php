<?php
session_start(); //start session
$AJAX = true;
include("../../config/config.php");
//error_reporting(E_ALL);
//include("../../config/session.php");
include("../../include/function_database_query.php");

include("../../include/common_functions.php");
//if(@isset($_SERVER['HTTP_X_REQUESTED_WITH']) && $_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest') 
{ 
    //if(@isset($_SERVER['HTTP_REFERER']) && strpos($_SERVER['HTTP_REFERER'],DOMAIN) !== false) 
	{
		//print_r($_POST['mode']);
		if($_POST != NULL) {
			$POST = bulk_filter($dbcon,$_POST);
		}
		else {
			$POST = bulk_filter($dbcon,$_GET);
		}
		
		if(strtolower($POST['mode']) == "dynamic_chart") {
				//var_dump($_REQUEST);
			$date=get_sdate($POST['c_year']);	
			$query="SELECT m.month,(select sum(g_total) from tbl_invoice u 
where MONTH(STR_TO_DATE(m.month,'%M')) = MONTH(u.invoice_date) and invoice_status=0 and company_id=".$_SESSION['company_id']." and u.invoice_date between '".date('Y-m-d',strtotime($date['start_date']))."' and '".date('Y-m-d',strtotime($date['end_date']))."' ) as invoice
     FROM (
          SELECT 'Apr' AS MONTH
           UNION SELECT 'May' AS MONTH
           UNION SELECT 'Jun' AS MONTH
           UNION SELECT 'Jul' AS MONTH
           UNION SELECT 'Aug' AS MONTH
           UNION SELECT 'Sep' AS MONTH
           UNION SELECT 'Oct' AS MONTH
           UNION SELECT 'Nov' AS MONTH
           UNION SELECT 'Dec' AS MONTH
           UNION SELECT 'Jan' AS MONTH
           UNION SELECT 'Feb' AS MONTH
           UNION SELECT 'Mar' AS MONTH
			) AS m
GROUP BY m.month
ORDER BY 1+1";
				$invoice_counter=$dbcon->query($query);
			//	echo $query;
				$row	= array();
				$i=0;
				while($chart=mysqli_fetch_assoc($invoice_counter))
				{	
					$row[$chart['month']][]=intval($chart['invoice']);
					$row[]= $chart['month'];
					$row1[$i]['device']=$chart['month'];
					$row1[$i]['geekbench']=$chart['invoice'];
					$i++;
				}		
				//var_dump($row);	
				echo json_encode($row1);
		}
		else if(strtolower($POST['mode']) == "getyear") {
			$date=get_sdate($POST['c_year']);
			 $invoice_count="Select SUM(g_total) as itotal from tbl_invoice where  invoice_date>='".date('Y-m-d',strtotime($date['start_date']))."' AND invoice_date<='".date('Y-m-d',strtotime($date['end_date']))."' AND invoice_status=0 and company_id=".$_SESSION['company_id'];
			$count_invoice=mysqli_fetch_assoc($dbcon->query($invoice_count));
			
			$invoice_paid="Select SUM(paid_amount) as ipaid_amount from tbl_receipt where  payment_date>='".date('Y-m-d',strtotime($date['start_date']))."' AND payment_date<='".date('Y-m-d',strtotime($date['end_date']))."' AND status=0 and company_id=".$_SESSION['company_id'];
			$count_paid=mysqli_fetch_assoc($dbcon->query($invoice_paid));
			$count['total']= $count_invoice['itotal'];
			$count['paid_amount']=$count_paid['ipaid_amount'];
			echo json_encode($count);
		}
		else if(strtolower($POST['mode']) == "load_saleval") {
			$date=get_sdate($POST['c_year']);
		
	$invoice_count="Select SUM(total) as itotal,SUM(product_amount) as taxable_amt from tbl_invoice as invoice
	left join tbl_invoicetrn as invtrn on invtrn.invoice_id=invoice.invoice_id
	where  invoice_date>='".date('Y-m-d',strtotime($date['start_date']))."' AND invoice_date<='".date('Y-m-d',strtotime($date['end_date']))."' AND invoice_status=0 and invtrn.trancation_status=0 and company_id=".$_SESSION['company_id'];
			$count_invoice=mysqli_fetch_assoc($dbcon->query($invoice_count));
			
			$invoice_paid="Select SUM(total_paid_amount) as ipaid_amount from tbl_receipt where  receipt_date>='".date('Y-m-d',strtotime($date['start_date']))."' AND receipt_date<='".date('Y-m-d',strtotime($date['end_date']))."' AND status=0 and payment_type=1 and company_id=".$_SESSION['company_id'];
			$count_paid=mysqli_fetch_assoc($dbcon->query($invoice_paid));
			$count['total']= intval($count_invoice['itotal']);
			$count['taxable_amt']= intval($count_invoice['taxable_amt']);
			$count['total_paid_amount']=intval($count_paid['ipaid_amount']);
			echo json_encode($count);
		}
		
   else if(strtolower($POST['mode']) == "load_purchaseval") {
			$date=get_sdate($POST['c_year']);
		$invoice_count1="Select SUM(g_total) as itotal from tbl_pono as po 
			where  po_date>='".date('Y-m-d',strtotime($date['start_date']))."' AND po_date<='".date('Y-m-d',strtotime($date['end_date']))."' AND po.status=0 and company_id=".$_SESSION['company_id'];
		$count_invoice1=mysqli_fetch_assoc($dbcon->query($invoice_count1));
		
		$invoice_count="Select SUM(total) as itotal,SUM(product_amount) as taxable_amt from tbl_pono as po 
			left join tbl_potrancation as potrn on potrn.po_id=po.po_id 
			where  po_date>='".date('Y-m-d',strtotime($date['start_date']))."' AND po_date<='".date('Y-m-d',strtotime($date['end_date']))."' AND po.status=0 and potrn.potrancation_status=0 and company_id=".$_SESSION['company_id'];
			$count_invoice=mysqli_fetch_assoc($dbcon->query($invoice_count));
			
			 $invoice_paid="Select SUM(res_trn.paid_amount) as ipaid_amount,SUM(res_trn.total_amount) as tpaid_amount from tbl_receipt as rec 
							left join tbl_receipt_trn as res_trn on res_trn.receipt_id=rec.receipt_id
							where  rec.receipt_date>='".date('Y-m-d',strtotime($date['start_date']))."' AND rec.receipt_date<='".date('Y-m-d',strtotime($date['end_date']))."' AND rec.status=0 and res_trn.status=0 and purchase_id!=0 and rec.company_id=".$_SESSION['company_id'];
			
			$count_paid=mysqli_fetch_assoc($dbcon->query($invoice_paid));
			$count['total']= intval($count_invoice1['itotal']);
			$count['taxable_amt']= intval($count_invoice['taxable_amt']);
			$count['paid_amount']=intval($count_paid['ipaid_amount']);
			$count['total_paid_amount']=intval($count_paid['tpaid_amount']);
			echo json_encode($count);
		}
		else if(strtolower($POST['mode']) == "getcust") {
			$date=get_sdate($POST['c_year']);
			$table1='';
			  $qry="SELECT SUM(invoice.g_total) AS total,cust.company_name as name from tbl_invoice as invoice inner join  tbl_customer as cust on invoice.cust_id=cust.cust_id  where invoice_date>='".date('Y-m-d',strtotime($date['start_date']))."' AND invoice_date<='".date('Y-m-d',strtotime($date['end_date']))."' and invoice_status=0 GROUP BY cust.cust_id ORDER BY total  desc limit 0,5";
				$cat=$dbcon->query($qry);
				$i=1;
				$table1.='<div>
                              <div class="">
                                  <h1 style="padding-top:0px !important">Top 5 Customer OF Year '.$POST['c_year'].'-'.($POST['c_year']+1).'</h1>
                              </div>
                    </div>
					<table class="table table-hover personal-task">
                              <tbody>
							  <tr>
							  <td>Sr No</td>
							  <td>Name</td>
							  <td>Total Business</td>
							  </tr>
                   ';
				while($rel=mysqli_fetch_assoc($cat))
				{
				$table1 .= '<tr>
                                  <td>'.$i.'</td>
                                  <td>
                                      '.$rel['name'].'
                                  </td>
                                  <td>
                                      <span class="badge bg-important">'.$rel['total'].'</span>
                                  </td>
                               
                              </tr>
                           ';
						 $i++;
				}
				$table1 .='</tbody>
                          </table>';
				echo $table1;
		}
		else if(strtolower($POST['mode']) == "paymentremainder") {
		 $payment_remainder="SELECT invoice_no, invoice.invoice_date, cust.company_name,DATE_ADD(invoice_date,INTERVAL cust.terms DAY) as ex_date, invoice_id, cust_address, cust_mobile, cust_email FROM tbl_invoice as invoice inner join tbl_customer as cust on cust.cust_id=invoice.cust_id WHERE invoice_status=0 and invoice_id=".$POST['invoiceid'];
		$result_remainder=mysqli_fetch_assoc($dbcon->query($payment_remainder));
			echo json_encode($result_remainder);
			
		}
		else if(strtolower($POST['mode']) == "pass_session") {
			/*$_SESSION['company_id'] = $POST['company_id'];
			$_SESSION['company_name'] = $POST['company_name'];
			echo $POST['company_name'];*/
			
			if(LOGIN_SETTING=="1" && $_SESSION['LOGGED_IN'])
			{
				if($POST['company_id']>0)
				{
					$where=" and user_type=2 and company_id=".$POST['company_id'];
				}
				else if($POST['company_id']=="0")
				{
					$where=" and user_type=1 and company_id=".$POST['company_id'];
				}
				 $sql = "SELECT `user_id`, `user_name`, `user_mail`,`user_type`, `user_phone`, `user_company`, `user_country`,`user_stat`,  `user_rid`, `user_tmst`, `user_date`, `setup`, `payment_status`,datediff (CURDATE(),user_tmst) as datedif,print_align,`company_id` FROM `users` WHERE active=0  ".$where;
				$result=$dbcon->query($sql);
				$row1 = $result->fetch_assoc();
				$_SESSION['LOGGED_IN'] = true;
				$_SESSION['title'] = TITLE;
				$_SESSION['domain'] = DOMAIN;
				$_SESSION['user_id'] = $row1['user_id'];
				$_SESSION['company_id'] = $row1['company_id'];
				$_SESSION['company_name'] = $row1['user_name'];
				$_SESSION['user_name'] = ucwords(strtolower($row1['user_name']));
				$_SESSION['user_type'] = $row1['user_type'];
				$_SESSION['user_company'] = $row1['user_company'];
				if($row1['print_align']=="0")//center
				{
					$_SESSION['print_page']='print_new';
				}
				else if($row1['print_align']=="2")//right
				{
					$_SESSION['print_page']='print_right';
				}
				else if($row1['print_align']=="1")//left
				{
					$_SESSION['print_page']='print_left';
				}
				$row['msg']=1;
			}
			else
			{
				$row['response']=getusertype($dbcon,0," and (usertype_id=2 or company_id=".$POST['company_id'].")");//usrtype_id=2 Company Admin
				$row['msg']=0;
			}
			echo json_encode($row);
		}
		else if(strtolower($POST['mode']) == "load_emp_stock"){
			
			$stock_date=$POST['dstock_date'];
			$str="";
			$str.='<table style="font-size:15px;border-collapse: collapse;border-top:none;" cellpadding="0" cellspacing="0" width="100%" >';
			
			$query_pro="select * from tbl_product as pro where product_status=0 and company_id = $_SESSION[company_id] and product_type!=3 order by TRIM(product_name) ASC";
			$rs_dis_pro=$dbcon->query($query_pro);
			$pro_cnt=mysqli_num_rows($rs_dis_pro);
			
			$str.='	<tr style="background-color: #FFEB3B;">
							<th rowspan="2" style="font-size:15px;border-top:1px #827d7d  solid;border-left:1px #827d7d  solid;border-right:1px #827d7d  solid;border-bottom:1px #827d7d  solid;color:#251919;">
							<center>
								<strong>Employee Name</strong>
							</center>
						</th>';
					while($rel_pro=mysqli_fetch_assoc($rs_dis_pro))
					{
						$str.='<th colspan="2" style="font-size:15px;border-top:1px #827d7d  solid;border-left:1px #827d7d  solid;border-right:1px #827d7d  solid;border-bottom:1px #827d7d  solid;color:#251919;"><strong>'.$rel_pro["product_name"].'</strong></th>';
					}
					$str.='</tr>';
				$query_pro="select * from tbl_product as pro where product_status=0 and company_id = $_SESSION[company_id] and product_type!=3 order by TRIM(product_name) ASC";
			$rs_dis_pro=$dbcon->query($query_pro);
			$str.='<tr style="background-color: #FFEB3B;">';
			while($rel_pro=mysqli_fetch_assoc($rs_dis_pro))
			{
				$str.='<td style="font-size:15px;border-top:1px #827d7d  solid;border-left:1px #827d7d  solid;border-right:1px #827d7d  solid;border-bottom:1px #827d7d  solid;color:#251919;"><strong>Allocate </strong></td>
				<td  style="font-size:15px;border-top:1px #827d7d  solid;border-left:1px #827d7d  solid;border-right:1px #827d7d  solid;border-bottom:1px #827d7d  solid;color:#251919;"><strong>Pending </strong></td>';
			}
			$str.='</tr>';
			
			if($_SESSION['user_type']=="2"){
				$cond="and user_id!=".$_SESSION['user_id'];
			}else{
				$cond="and user_id=".$_SESSION['user_id'];
			}
			$query1="select * from users as pro where active=0 and user_type!=1 ".$cond." and company_id = $_SESSION[company_id] order by TRIM(user_name) ASC";
			$rs_dispatch=$dbcon->query($query1);	
			while($rel=mysqli_fetch_assoc($rs_dispatch))
			{	
				$str.='	<tr style="background-color: #9e9ea047;">
							<td style="font-size:15px;border-top:1px #827d7d  solid;border-left:1px #827d7d  solid;border-right:1px #827d7d  solid;border-bottom:1px #827d7d  solid;color:#251919;">
							<center>
								<strong>'.$rel["user_name"].'</strong>
							</center>
						</td>';
					$query_pro="select * from tbl_product as pro where product_status=0 and company_id = $_SESSION[company_id] and product_type!=3 order by TRIM(product_name) ASC";
						$rs_dis_pro=$dbcon->query($query_pro);
					$s=1;$pk[$s]="0";$pkpen[$s]="0";$pen_qty1="";$allo_qty1="";
					while($rel_pro=mysqli_fetch_assoc($rs_dis_pro))
					{
						$allo_qtyq=$allo_qty=today_stock_value($dbcon,$stock_date,$rel_pro['product_id'],$rel["user_id"],1);
						$pen_qty=today_stock_value($dbcon,$stock_date,$rel_pro['product_id'],$rel["user_id"],2);
						$allo_qty=number_format($allo_qty, 0, ".", "");
						$pen_qty=number_format($pen_qty, 0, ".", "");
						if($pen_qty=="0"){
							$pen_qty1="";
						}else{
						    $pen_qty1=$pen_qty;
						}
						if($allo_qty=="0"){
							$allo_qty1="";
						}else{
						    $allo_qty1=$allo_qty;
						}
							$str.='<td style="font-size:15px;border-top:1px #827d7d  solid;border-left:1px #827d7d  solid;border-right:1px #827d7d  solid;border-bottom:1px #827d7d  solid;color:#251919;">'.$allo_qty1.'</td>
							<td  style="font-size:15px;border-top:1px #827d7d  solid;border-left:1px #827d7d  solid;border-right:1px #827d7d  solid;border-bottom:1px #827d7d  solid;color:#251919;">'.$pen_qty1.'</td>';
							$pk[$s]+=$allo_qty;
							$pkpen[$s]+=$pen_qty;
						$s++;
						
					}
					$str.='</tr>';
			}	
				$str.='	<tr style="height: 40px;background-color: #FF9800;" >
							<td style="font-size:15px;border-top:1px #827d7d  solid;border-left:1px #827d7d  solid;border-right:1px #827d7d  solid;border-bottom:1px #827d7d  solid;color:#251919;">
								<center>
									<strong>Total</strong>
								</center>
							</td>';
							for($kp=1;$kp<=$pro_cnt;$kp++){
								$str.='<td style="font-size:15px;border-top:1px #827d7d  solid;border-left:1px #827d7d  solid;border-right:1px #827d7d  solid;border-bottom:1px #827d7d  solid;color:#251919;">
									<strong>'.$pk[$kp].'</strong>
								</td>
								<td style="font-size:15px;border-top:1px #827d7d  solid;border-left:1px #827d7d  solid;border-right:1px #827d7d  solid;border-bottom:1px #827d7d  solid;color:#251919;">
									<strong>'.$pkpen[$kp].'</strong>
								</td>';
							}
			//$pro_cnt
			$str.='	</table>';
			
			
			echo $str;
		}
    
    
	}
	/*else {
        die("Error - 2");
    }*/
}
/*
else {
    die("Error - 1");
}*/
function get_sdate($date)
{
	$sdate['start_date']=date('01-04-'.$date);
	$sdate['end_date']=date('31-03-'.($date+1));
	return $sdate;	
}

?>