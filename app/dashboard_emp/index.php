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
where MONTH(STR_TO_DATE(m.month,'%M')) = MONTH(u.invoice_date) and invoice_status=0 and user_id=".$_SESSION['user_id']." and company_id=".$_SESSION['company_id']." and u.invoice_date between '".date('Y-m-d',strtotime($date['start_date']))."' and '".date('Y-m-d',strtotime($date['end_date']))."' ) as invoice
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
		else if(strtolower($POST['mode']) == "load_target_chart") {
		$row = array();
		$stock_date=$POST['dstock_date'];
		$query1="select * from users as pro where active=0 and user_type!=1 and user_id=".$_SESSION['user_id']." and company_id = $_SESSION[company_id] order by TRIM(user_name) ASC";
			$rs_dispatch=$dbcon->query($query1);	
			$rel=mysqli_fetch_assoc($rs_dispatch);
				$row["user_name"]=$rel["user_name"];		
		$query_pro="select * from tbl_product as pro where product_status=0 and company_id = $_SESSION[company_id] and product_type!=3 order by TRIM(product_name) ASC";
			$rs_dis_pro=$dbcon->query($query_pro);
			$pro_cnt=mysqli_num_rows($rs_dis_pro);
				
				$i=0;
				$row["count"]=$pro_cnt;		
			while($rel_pro=mysqli_fetch_assoc($rs_dis_pro))
			{
				$allo_qty=today_stock_value($dbcon,$stock_date,$rel_pro['product_id'],$rel["user_id"],1);
				$pen_qty=today_stock_value($dbcon,$stock_date,$rel_pro['product_id'],$rel["user_id"],2);
				
				$row[$rel_pro['product_name']][]=intval($allo_qty);
				$row[$rel_pro['product_name']][]=intval($pen_qty);
				$row[]= $rel_pro['product_name'];				
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
			
			$str.='	<tr >
							<td rowspan="2" style="font-size:15px;border-top:1px #827d7d  solid;border-left:1px #827d7d  solid;border-right:1px #827d7d  solid;border-bottom:1px #827d7d  solid;color:#251919;">
							<center>
								<strong>Employee Name</strong>
							</center>
						</td>';
					while($rel_pro=mysqli_fetch_assoc($rs_dis_pro))
					{
						$str.='<td colspan="2" style="font-size:15px;border-top:1px #827d7d  solid;border-left:1px #827d7d  solid;border-right:1px #827d7d  solid;border-bottom:1px #827d7d  solid;color:#251919;"><strong>'.$rel_pro["product_name"].'</strong></td>';
					}
					$str.='</tr>';
				$query_pro="select * from tbl_product as pro where product_status=0 and company_id = $_SESSION[company_id] and product_type!=3 order by TRIM(product_name) ASC";
			$rs_dis_pro=$dbcon->query($query_pro);
			$str.='<tr>';
			while($rel_pro=mysqli_fetch_assoc($rs_dis_pro))
			{
				$str.='<td style="font-size:15px;border-top:1px #827d7d  solid;border-left:1px #827d7d  solid;border-right:1px #827d7d  solid;border-bottom:1px #827d7d  solid;color:#251919;"><strong>Allocate Qty</strong></td>
				<td  style="font-size:15px;border-top:1px #827d7d  solid;border-left:1px #827d7d  solid;border-right:1px #827d7d  solid;border-bottom:1px #827d7d  solid;color:#251919;"><strong>Pending Qty</strong></td>';
			}
			$str.='</tr>';
			
			$query1="select * from users as pro where active=0 and user_type!=1 and user_id!=".$_SESSION['user_id']." and company_id = $_SESSION[company_id] order by TRIM(user_name) ASC";
			$rs_dispatch=$dbcon->query($query1);	
			while($rel=mysqli_fetch_assoc($rs_dispatch))
			{	
				$str.='	<tr >
							<td style="font-size:15px;border-top:1px #827d7d  solid;border-left:1px #827d7d  solid;border-right:1px #827d7d  solid;border-bottom:1px #827d7d  solid;color:#251919;">
							<center>
								<strong>'.$rel["user_name"].'</strong>
							</center>
						</td>';
					
					$str.='</tr>';
			}	
			$str.='	</table>';
			
			
			echo $str;
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
}
function get_sdate($date)
{
	$sdate['start_date']=date('01-04-'.$date);
	$sdate['end_date']=date('31-03-'.($date+1));
	return $sdate;	
}

?>