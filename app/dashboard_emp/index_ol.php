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
				where MONTH(STR_TO_DATE(m.month,'%M')) = MONTH(u.invoice_date) and invoice_status=0 and u.company_id=".$_SESSION['company_id']." and u.invoice_date between '".date('Y-m-d',strtotime($date['start_date']))."' and '".date('Y-m-d',strtotime($date['end_date']))."' ) as invoice
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
				//echo $query;
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
		
	$invoice_count="Select SUM(g_total) as itotal,SUM(product_amount) as taxable_amt from tbl_invoice as invoice
	left join tbl_invoicetrn as invtrn on invtrn.invoice_id=invoice.invoice_id
	where  invoice_date>='".date('Y-m-d',strtotime($date['start_date']))."' AND invoice_date<='".date('Y-m-d',strtotime($date['end_date']))."' AND invoice_status=0 and invtrn.trancation_status=0 and company_id=".$_SESSION['company_id'];
			$count_invoice=mysqli_fetch_assoc($dbcon->query($invoice_count));
			
			$invoice_paid="Select SUM(paid_amount) as ipaid_amount from tbl_receipt where  payment_date>='".date('Y-m-d',strtotime($date['start_date']))."' AND payment_date<='".date('Y-m-d',strtotime($date['end_date']))."' AND status=0 and company_id=".$_SESSION['company_id'];
			$count_paid=mysqli_fetch_assoc($dbcon->query($invoice_paid));
			$count['total']= intval($count_invoice['itotal']);
			$count['taxable_amt']= intval($count_invoice['taxable_amt']);
			$count['paid_amount']=intval($count_paid['ipaid_amount']);
			echo json_encode($count);
		}
		
   else if(strtolower($POST['mode']) == "load_purchaseval") {
			$date=get_sdate($POST['c_year']);
	
$invoice_count="Select SUM(g_total) as itotal,SUM(product_amount) as taxable_amt,SUM(product_amount) as taxable_amt from tbl_pono as po 
	left join tbl_potrancation as potrn on potrn.po_id=po.po_id 
	where  po_date>='".date('Y-m-d',strtotime($date['start_date']))."' AND po_date<='".date('Y-m-d',strtotime($date['end_date']))."' AND po.status=0 and potrn.potrancation_status=0 and company_id=".$_SESSION['company_id'];
			$count_invoice=mysqli_fetch_assoc($dbcon->query($invoice_count));
			
			$invoice_paid="Select SUM(paid_amount) as ipaid_amount from tbl_purchasereceipt where  payment_date>='".date('Y-m-d',strtotime($date['start_date']))."' AND payment_date<='".date('Y-m-d',strtotime($date['end_date']))."' AND status=0 and company_id=".$_SESSION['company_id'];
			$count_paid=mysqli_fetch_assoc($dbcon->query($invoice_paid));
			$count['total']= intval($count_invoice['itotal']);
			$count['taxable_amt']= intval($count_invoice['taxable_amt']);
			$count['paid_amount']=intval($count_paid['ipaid_amount']);
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
		else if(strtolower($POST['mode']) == "fetch_followup_status") {
			$s_date=explode(' - ',$POST['date']);
			
			$where.=" and followup_date>='".date('Y-m-d',strtotime($s_date[0]))."' AND followup_date<='".date('Y-m-d',strtotime($s_date[1]))."'";
			
		$appData = array();
		$i=1;
		$aColumns = array('flp.followup_id', 'lead.lead_no','flp.cdate', 'followup_status', 'sts.status_name','inq.inquiry_no','quo.quotation_no','type.type_name','cust.company_name','flp.followup_date','flp.inquiry_id','flp.start_lead_id','flp.quotation_id');
		$sIndexColumn = "followup_id";
		$isWhere = array("followup_status = 0 and followup_id in(select max(followup_id) from tbl_followup where followup_status=0   group by start_lead_id) and flp.statusid=1 ".$where.check_user('flp'));
		$sTable = "tbl_followup as flp";			
		$isJOIN = array('left join status_mst as sts on sts.statusid=flp.statusid','left join tbl_lead as lead on lead.lead_id=flp.start_lead_id','left join tbl_customer cust on lead.cust_id=cust.cust_id','left join tbl_inquiry as inq on inq.inquiry_id=flp.inquiry_id','left join tbl_quotation as quo on quo.quotation_id=flp.quotation_id','left join type_mst as type on type.typeid=flp.typeid');
		$hOrder = "flp.start_lead_id desc";
		include('../../include/pagging.php');
		$appData = array();
		$id=1;
		foreach($sqlReturn as $row) {
			$row_data = array();
			$row_data[] = $row['sr'];
			$row_data[] = $row['lead_no'];
			$row_data[] = $row['inquiry_no'];
			$row_data[] = $row['quotation_no'];
			$row_data[] = $row['company_name'];
			$row_data[] = date('d, M y',strtotime($row['followup_date']));
			$row_data[] = $row['type_name'];
			$row_data[] = $row['status_name'];
				if(empty($row['quotation_id']) AND (empty($row['inquiry_id']))){
					$row_data[] = '<a class="btn btn-xs btn-primary" title="Add Lead Follow-Up" data-toggle="tooltip" data-placement="top" href="'.ROOT.'add_lead_followup/'.$row['start_lead_id'].'"><i class="fa fa-plus"></i></a> ';
				}else if(empty($row['quotation_id'])){
					$row_data[] = ' <a class="btn btn-xs btn-primary" title="Add Inquiry Follow-Up" data-toggle="tooltip" data-placement="top" href="'. ROOT.'add_inq_followup/'.$row['inquiry_id'].'"><i class="fa fa-plus"></i></a>';
				}
				else{
					$row_data[] = '  <a class="btn btn-xs btn-primary" title="Add Quotation Follow-Up" data-toggle="tooltip" data-placement="top" href="'.ROOT.'add_quotation_followup/'.$row['quotation_id'].'"><i class="fa fa-plus"></i></a>';
								
				}
			
			$appData[] = $row_data;
			$id++;
		}
		$output['aaData'] = $appData;
		echo json_encode( $output );
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