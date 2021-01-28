<?php
session_start();
$AJAX = true;
include("../../config/config.php");
//error_reporting(E_ALL);
include("../../config/session.php");
include("../../include/function_database_query.php");
include_once("../../include/common_functions.php");
include("../../view/export/quotation.php");
include('../Mailin.php');

								
//print_r($_POST);
//print_r($_FILES);
if($_POST != NULL) {
	$POST = bulk_filter($dbcon,$_POST);
}
else {
	$POST = bulk_filter($dbcon,$_GET);
}

	if(strtolower($POST['mode']) == "fetch") {
            
                $edit_btn_per=check_permission('invoice_list',$_SESSION['user_type'],'edit',$dbcon);
		$delete_btn_per=check_permission('invoice_list',$_SESSION['user_type'],'delete',$dbcon);
		$s_date=explode(' - ',$POST['date']);
		$_SESSION['start']=$s_date[0];
		$_SESSION['end']=$s_date[1];
                $edit=''; $delete='';
		
		$where='';$where1='';
			if(!empty($POST['type_id']))
			{
				$where .=" and invoice.invoicetype_id=".$POST['type_id'];
			}
			$where.="  and invoice_date >= '".date('Y-m-d',strtotime($s_date[0]))."' AND invoice_date <= '".date('Y-m-d',strtotime($s_date[1]))."'";
			$appData = array();
			$i=1;
			$aColumns = array('invoice_id','invoice_no','cust.company_name','city.city_name','invoice_date','invoicetype.invoice_type','g_total','paid_amount','us.user_name','invoice_status','invoice.cdate','invoice.user_id','invoice.usertype_id','invoice.invoicetype_id','invoice.gst_flag');
			$sIndexColumn = "invoice_id";
			$isWhere = array("invoice_status = 0 and invoice.company_id = ".$_SESSION['company_id']." ".$where.$where1.check_user('invoice'));
			$sTable = "tbl_invoice as invoice";			
			$isJOIN = array('left join  tbl_ledger cust on invoice.cust_id=cust.l_id','left join  city_mst city on cust.cityid=city.cityid','left join tbl_invoicetype invoicetype on invoice.invoicetype_id=invoicetype.invoicetype_id','left join users as us on us.user_id=invoice.user_id');
			$hOrder = "invoice.invoice_id desc";
			include('../../include/pagging.php');
			$appData = array();
			$id=1;
                        $total = 0;
			foreach($sqlReturn as $row) {
				$row_data = array();
				$row_data[] = $row['invoice_type'];
				$row_data[] = $row['invoice_no'];
				$row_data[] = date('d M, Y',strtotime($row['invoice_date']));
				$row_data[] = $row['company_name'];
				//$row_data[] = $row['city_name'];
				$row_data[] = $row['g_total'];
				$row_data[] = $row['user_name'];
				
				
				if($row['g_total']>$row['paid_amount']){
					$cr_btn= '<!--<a class="btn btn-xs btn-primary" data-original-title="Use Credit" data-toggle="tooltip" data-placement="top" href="'.ROOT.'use_cr_note/'.$row['invoice_id'].'"><i class="fa fa-plus"></i></a>-->';
				}
				else{
					$cr_btn= '';
				}
			
				 
					$addpayment='';$delete='';$edit='';
					if($row["g_total"]>$row["paid_amount"]){
						//$addpayment='<a class="btn btn-xs btn-primary" data-original-title="Payable '.($row['g_total']-$row['paid_amount']).' Rs." data-toggle="tooltip" data-placement="top" href="invoicepaymentmode/'.$row['invoice_id'].'"><i class="fa fa-plus"></i></a>';
						
					}
					$print='<a class="btn btn-xs btn-info" data-original-title="Print" data-toggle="tooltip" data-placement="top" href="'.ROOT.'invoicereceipt/'.$row['invoice_id'].'"><i class="fa fa-print"></i></a> ';
					
					$print_new='<a target = "_blank" class="btn btn-xs btn-info" data-original-title="PDF" data-toggle="tooltip" data-placement="top" href="'.ROOT.'export/invoice_print?id='.$row['invoice_id'].'">PDF</a> ';
					
					$letterprint='<!--<a class="btn btn-xs btn-info" data-original-title="Dispatch Form Print" data-toggle="tooltip" data-placement="top" href="'.ROOT.'dispath_form/'.$row['invoice_id'].'"><i class="fa fa-file-text"></i></a>--> ';
					
                                        if($delete_btn_per)
                                            $delete='<button class="btn btn-xs btn-danger" data-original-title="Delete" data-toggle="tooltip" data-placement="top" onClick="delete_invoice('.$row['invoice_id'].')"><i class="fa fa-trash-o"></i></button>';
					
					$send='<button class="btn btn-xs btn-primary" data-original-title="Send Invoice" data-toggle="tooltip" data-placement="top" onClick="send_invoice('.$row['invoice_id'].')"><i class="fa fa-location-arrow"></i></button>';
					
                                        if($edit_btn_per)
                                            $edit='<a class="btn btn-xs btn-warning" data-original-title="Edit" data-toggle="tooltip" data-placement="top" href="'.ROOT.'invoiceedit/'.$row['invoice_id'].'"><i class="fa fa-pencil"></i></a>';
					
					
					$print1='<button class="btn btn-xs btn-info" data-original-title="Print Invoice" data-toggle="tooltip" data-placement="top" onClick="sprint('.$row['invoice_id'].')"><i class="fa fa-print"></i></button>';
					
					//$print1='<a class="btn btn-xs btn-info" data-original-title="Print" data-toggle="tooltip" data-placement="top" href="'.ROOT.'response/'.$row['invoice_id'].'"><i class="fa fa-print"></i></a> ';
					
					$row_data[] = $print.'<!--<a class="btn btn-xs btn-success" data-original-title="Print Chalan" data-toggle="tooltip" data-placement="top" href="'.ROOT.'invoicechalan/'.$row['invoice_id'].'"><i class="fa fa-print"></i></a>-->
					
					  '.$edit.' '.$delete.' '.$addpayment.' '.$letterprint.' '.$cr_btn.' '.$send.' '.$print1.' '.$print_new;
				 
				
				$appData[] = $row_data;
				$id++;
			}
			$output['aaData'] = $appData;
			echo json_encode( $output );
		}
		else if(strtolower($POST['mode']) == "add") {
			  $com="select * from tbl_company where company_id=".$_SESSION['company_id'];
	$comty=mysqli_fetch_assoc($dbcon->query($com));	
		
		$inv_no=load_invoice_no($dbcon,$POST['invoicetype_id']);
		$query_invoicetype = $dbcon->query("UPDATE tbl_invoicetype SET taxinvoice_start = taxinvoice_start +1 WHERE invoicetype_id = ".$POST['invoicetype_id']);
		
			
							$info['invoicetype_id']	= $POST['invoicetype_id'];
							$info['sales_ledger_id']	= $POST['sales_ledger_id'];
							//$info['invoice_no']		= $POST['invoice_no'];
							$info['invoice_no']		= $inv_no;
							$info['invoice_date']	= date('Y-m-d',strtotime($POST['invoice_date']));
							$info['challan_no']		= $POST['challan_no'];
							$info['challan_date']	= date('Y-m-d',strtotime($POST['challan_date']));
							$info['sales_order_id']		= $POST['sales_order_id'];
							$info['num_of_parcel']	= $POST['num_of_parcel'];
							$info['dispatch_doc_no']= text_rnremove($POST['dispatch_doc_no']);
							$info['dispatch_date']  = date('Y-m-d H:i:s',strtotime($POST['dispatch_date']));
							$info['vehicle_no']		= $POST['vehicle_no'];
							$info['e_way_bill_no']		= $POST['e_way_bill_no'];
							$info['order_no']		= $POST['order_no'];
							$info['order_date']	= date('Y-m-d',strtotime($POST['order_date']));
							$info['dispatch_by']	= $POST['dispatch_by'];
							$info['destination']	= $POST['destination'];
							$info['payment_terms']	= $POST['payment_terms'];
							
							$info['docket_no']		= $POST['docket_no'];
							$info['packing_boxes']	= $POST['packing_boxes'];
							$info['total_weight']	= $POST['total_weight'];
							
							$info['cust_id']		= $POST['cust_id'];
							$info['consignee_id']	= $POST['consignee_id'];
							$info['packing']		= $POST['packing'];
							$info['cutting']		= $POST['cutting'];
							$info['freight']		= $POST['freight'];
							$info['g_total']		= $POST['g_total'];
							$info['type_id']		= $POST['type_id'];
							$info['remark']			= text_rnremove($POST['remark']);
							$info['reverse_charge']	= $POST['reverse_charge_check'];
							$info['gst_flag']		= '2';
							$info['cdate']			= date("Y-m-d H:i:s");
							$info['user_id']		= $_SESSION['user_id'];
							$info['company_id']		= $_SESSION['company_id'];
							if(isset($POST['save_print']))
							{
								$info['print_status']	= $POST['print_status'];
							}
							$inserinvoiceid=add_record('tbl_invoice', $info, $dbcon);
						
						$info_trn['invoice_id']			= $inserinvoiceid;
						$info_trn['trancation_status']	= 0;
						$updateid=update_record('tbl_invoicetrn', $info_trn,"trancation_status=3 and user_id=".$_SESSION['user_id'] , $dbcon);
						
					add_general_book_entry($dbcon,"tbl_invoice",$inserinvoiceid,2,$POST['cust_id'],$POST['g_total'],$general_book_id,$POST['invoice_date']);
						
						/* $info1['table_name']	= "tbl_invoice";
						$info1['table_id']		= $inserinvoiceid;
						$info1['entry_type']	= 2;
						$info1['ledger_id']		= $POST['cust_id'];
						$info1['amount']		= $POST['g_total'];
						$info1['user_id']		= $_SESSION['user_id'];
						$info1['cdate']			= date("Y-m-d H:i:s");
						$info1['company_id']	= $_SESSION['company_id'];
						
						$inserid11=add_record("tbl_general_book", $info1, $dbcon); */
						
					general_book_tax_entry($dbcon,$inserinvoiceid);
						
			if(!empty($POST['payment_reminder']) && $POST['payment_reminder']>0)
			{
				$remainder_date=addDayswithdate($info['invoice_date'],$POST['payment_reminder']);//($date,$days)
				
				$query="select `company_name` from tbl_customer WHERE `cust_id`=".$_POST['cust_id'];
				
				$rel=mysqli_fetch_assoc($dbcon->query($query));	
				
				$qry='select inv.*,(select SUM(rtrn.paid_amount) as amuount from tbl_receipt_trn as rtrn where  rtrn.status=0 and rtrn.invoice_id=inv.invoice_id) as paidamo from tbl_invoice as inv
			    left join tbl_customer as cust on cust.cust_id=inv.cust_id where  inv.company_id='.$_SESSION['company_id'].'';
				
			         $result1=$dbcon->query($qry);
				
				
					while($re=mysqli_fetch_assoc($result1))
					{
						$tamount=$re['g_total'];
						$due =$tamount-$re["paidamo"];
				    }
				
				
				$info_remainder['task_detail']		= 'Invoice #'.$rel['company_name'].' - '.$info['invoice_no'].' - RS '.$due.'#Payment On '.date('d-m-Y',strtotime($remainder_date));
				$info_remainder['date']				= $remainder_date;
				
				$info_remainder['ref_id']			= $inserinvoiceid;
				$info_remainder['ref_table']		= 'tbl_invoice';
				
				$info_remainder['user_id']			= $_SESSION['user_id'];
				$info_remainder['company_id']		= $_SESSION['company_id'];
				$inserinvoiceid1=add_record('todo_mst', $info_remainder, $dbcon);
				
			}
		/**Payment Reminder Entry END***/
		
		/*** Payment Entry Start ***/
		if(!empty($POST['paymentmodeid'])){
			if($POST['paymentmodeid']==1)
			{	
				$qry="select acc_id,acc_type from account_mst where acc_type=1 and acc_status=0 and company_id=".$_SESSION['company_id'];
				$rel_acc=mysqli_fetch_assoc($dbcon->query($qry));	
				$acc_id=$rel_acc['acc_id'];
			}
			else
			{
					$acc_id		=	$POST['pur_acc_id'];
			}
		
			$info2['receipt_no'] 			 = $POST['invoice_no'];
			$info2['receipt_date']			 = date("Y-m-d",strtotime($POST['invoice_date']));
			$info2['cust_id']		    	 = $POST['cust_id'];
			$info2['bank_id']	    		 = $POST['bankid'];
			$info2['acc_id']  	 			 = $acc_id;
			$info2['payment_mode_id']	  	 = $POST['paymentmodeid'];
			$info2['cheque_dtl']     	 	 = $POST['cheque_dtl'];
			$info2['ref_date']     			 = date("Y-m-d",strtotime($POST['ref_date']));
			if($POST['full_paid_type']=="DR"){
						$full_paid_type1="2";
						
					}else{
						$full_paid_type1="1";
						
					}
			$info2['payment_type']	   		 = $full_paid_type1;
			$info2['total_paid_amount']	   	 = $POST['paid_amount'];
			//$info2['payment_remark']	   	 = text_rnremove($POST['payment_desc']);
			$info2['cdate']		   	 	 	 = date("Y-m-d H:i:s");
			$info2['user_id']		  		 = $_SESSION['user_id'];
			$info2['company_id']     		 = $_SESSION['company_id'];
		$insertreceiptid=add_record('tbl_receipt', $info2, $dbcon);
		
				$infopaytrn['receipt_id']    		=$insertreceiptid;
				$infopaytrn['invoice_id']        	= $inserinvoiceid;
				$infopaytrn['payment_source']     	=$source;
				$infopaytrn['paid_amount']      	=$POST['paid_amount'];
				$infopaytrn['total_amount']        	=$POST['paid_amount'];
				$infopaytrn['payment_type']        	=$full_paid_type1;
				$infopaytrn['user_id']    		 	=$_SESSION['user_id'];
				$infopaytrn['company_id']    	 	=$_SESSION['company_id'];
				$infopaytrn['usertype_id']   	 	=$_SESSION['user_type'];
			$insertpaytrn=add_record('tbl_receipt_trn', $infopaytrn, $dbcon);
		
		
				$info1['table_name']	= "tbl_payment";
				$info1['table_id']		= $insertreceiptid;
				$info1['ref_date']		= date('Y-m-d',strtotime($POST['invoice_date']));
				$info1['entry_type']	= 1;
				$info1['ledger_id']		= $POST['cust_id'];
				$info1['amount']		= $POST['paid_amount'];
				$info1['user_id']		= $_SESSION['user_id'];
				$info1['cdate']			= date("Y-m-d H:i:s");
				$info1['company_id']	= $_SESSION['company_id'];
				
				$inserid11=add_record("tbl_general_book", $info1, $dbcon);
				
				$info21['table_name']	= "tbl_payment";
				$info21['table_id']		= $insertreceiptid;
				$info21['ref_date']		= date('Y-m-d',strtotime($POST['invoice_date']));
				$info21['entry_type']	= 2;
				$info21['ledger_id']	= $POST['paymentmodeid'];
				$info21['amount']		= $POST['paid_amount'];
				$info21['user_id']		= $_SESSION['user_id'];
				$info21['cdate']		= date("Y-m-d H:i:s");
				$info21['company_id']	= $_SESSION['company_id'];
				
				$inserid121=add_record("tbl_general_book", $info21, $dbcon);
				
				
		}
		
		/*** Payment Entry End ***/
		/** Sales Order Entry Start ***/
		if($POST['sales_order_id']){
			$info_sales_order['invoice_status']  = 1;
			$info_sales_order['used_invoice_id'] = $inserinvoiceid;
			$updatesalesid=update_record('tbl_sales_order', $info_sales_order,"sales_order_id=".$POST['sales_order_id'], $dbcon);
		}
		if(isset($POST['save_print']))
			{
				$arr['printstatus']=$POST['print_status'];
				$arr['msg']="1";
				$arr['eid']=$inserinvoiceid;
			}
			else
			{
				if($inserinvoiceid)
				{	
					$arr['msg']="1";							
				}
				else
				{
					$arr['msg']="0";
				}
			}
			echo json_encode($arr);
			 
		}		
		else if(strtolower($POST['mode']) == "edit") {
			//if($_POST['token'] == $_SESSION['token']) 
			{
							$info['invoicetype_id']	= $POST['invoicetype_id'];
							$info['sales_ledger_id']	= $POST['sales_ledger_id'];
							$info['invoice_no']		= $POST['invoice_no'];
							$info['invoice_date']	= date('Y-m-d',strtotime($POST['invoice_date']));
							$info['challan_no']		= $POST['challan_no'];
							$info['challan_date']	= date('Y-m-d',strtotime($POST['challan_date']));
							$info['vehicle_no']		= $POST['vehicle_no'];
							$info['order_no']		= $POST['order_no'];
							$info['order_date']		= date('Y-m-d',strtotime($POST['order_date']));
							$info['num_of_parcel']	= $POST['num_of_parcel'];
							$info['dispatch_doc_no']= $POST['dispatch_doc_no'];
							$info['dispatch_date']  = date('Y-m-d H:i:s',strtotime($POST['dispatch_date']));
							$info['dispatch_by']	= $POST['dispatch_by'];
							$info['destination']	= $POST['destination'];
							$info['payment_terms']	= $POST['payment_terms'];
							
							$info['docket_no']		= $POST['docket_no'];
							$info['packing_boxes']	= $POST['packing_boxes'];
							$info['total_weight']	= $POST['total_weight'];
							
							$info['cust_id']		= $POST['cust_id'];
							$info['consignee_id']	= $POST['consignee_id'];
							$info['packing']		= $POST['packing'];
							$info['cutting']		= $POST['cutting'];
							$info['freight']		= $POST['freight'];
							$info['g_total']		= $POST['g_total'];
							$info['type_id']		= $POST['type_id'];
							$info['remark']			= text_rnremove($POST['remark']);
							$info['reverse_charge']			= $POST['reverse_charge_check'];
							$info['cdate']			= date("Y-m-d H:i:s");
							$info['user_id']		= $_SESSION['user_id'];
							$info['company_id']		= $_SESSION['company_id'];
							if(isset($POST['save_print']))
							{
								$info['print_status']	= $POST['print_status'];
							}
							$updateid=update_record('tbl_invoice', $info,"invoice_id=".$POST['eid'] , $dbcon);
							
							
						$general_book_id=get_general_book_id($dbcon,'tbl_invoice',$POST['eid'],$POST['cust_id']);
						
						add_general_book_entry($dbcon,"tbl_invoice",$POST['eid'],2,$POST['cust_id'],$POST['g_total'],$general_book_id,$POST['invoice_date']);
						
						/* $info1['table_name']	= "tbl_invoice";
						$info1['table_id']		= $POST['eid'];
						$info1['entry_type']	= 2;
						$info1['ledger_id']		= $POST['cust_id'];
						$info1['amount']		= $POST['g_total'];
						$info1['user_id']		= $_SESSION['user_id'];
						$info1['cdate']			= date("Y-m-d H:i:s");
						$info1['company_id']	= $_SESSION['company_id'];
					if(empty($general_book_id)){
						$inserid11=add_record("tbl_general_book", $info1, $dbcon);
					}else{
						$updateid=update_record('tbl_general_book', $info1,"general_book_id=".$general_book_id , $dbcon);
					} */
							
					general_book_tax_entry($dbcon,$POST['eid']);
			
	
				if(isset($POST['save_print']))
				{
					$arr['printstatus']=$POST['print_status'];
					$arr['msg']="update";
					$arr['eid']=$POST['eid'];
				}
				else
				{
					if($updateid)
					{	
						$arr['msg']="update";
						
					}
					else
						$arr['msg']=0;
				}
			echo json_encode($arr);	
				
			}
		}
		else if(strtolower($POST['mode']) == "delete") {
					 
			$info['invoice_status']	= 2;
			$info1['trancation_status']	= 2;
			$informdr['status'] = 2;
			$updateinvoiceid=update_record('tbl_invoice', $info,"invoice_id=".$POST['eid'] , $dbcon);	
			$updatetrancationid=update_record('tbl_invoicetrn', $info1,"invoice_id=".$POST['eid'] , $dbcon);	
			//Update Payment Reminder
			$updatermdrid=update_record('todo_mst', $informdr,"ref_id=".$POST['eid']." and ref_table='tbl_invoice'" , $dbcon);
			//Update Serial Number
			//$deleteid=delete_record('tbl_serialtrn',"invoice_id=".$POST['eid'], $dbcon);
			
			$info_gen['genral_book_status'] = 2;
			$updateinvoice_gen=update_record('tbl_general_book', $info_gen,"table_name='tbl_invoice' and table_id=".$POST['eid'] , $dbcon);	
			
			
			if($updatetrancationid)
				echo "1";	
			else
				echo "0";			
		}
		else if(strtolower($POST['mode']) == "fieldadd") {
				$info1['product_id']		= $POST['product_id'];
				$info1['description']		= stripcslashes(text_rnremove($_POST['product_des']));
				$info1['product_hsn_code']	= $POST['product_hsn_code'];
				$info1['product_qty']		= $POST['product_qty'];
				$info1['product_rate']		= $POST['product_rate'];
				$info1['product_disc']		= $POST['product_disc'];
				$info1['unit_id']			= $POST['unit_id'];
				$info1['product_discount']	= $POST['product_discount'];
				$info1['discount_per']		= $POST['discount_per'];
				$info1['formulaid']			= $POST['formulaid'];
				$info1['product_amount']	= $POST['taxable_value'];
				$info1['total']				= $POST['product_amount'];
				$info1['mrp']				= $POST['mrp'];
				//$info=get_product_tax($dbcon,$total,$POST['formulaid']);
			
				//$info1=array_merge($info1,$info);
			$table='tbl_invoicetrn';$tableid='trancation_id';
			if(!empty($POST['invoice_id']))
			{
					$info1['invoice_id']= $POST['invoice_id'];
					$info1['user_id']	= $_SESSION['user_id'];
			}
			else
			{
				$info1['user_id']	= $_SESSION['user_id'];
				$info1['trancation_status']	= 3;
			}
			if(empty($POST['edit_id']))
			{
				$inserid=add_record($table, $info1, $dbcon);
				
			}
			else
			{
				$updateid=update_record($table, $info1,$tableid."=".$POST['edit_id'] , $dbcon);
				$inserid=$POST['edit_id'];
			}
			
			 $insert_tax=add_tax_record($dbcon,$inserid,"tbl_invoicetrn","trancation_id",$POST['formulaid'],$POST['taxable_value']);
			//var_dump($insert_tax);
		}
		else if(strtolower($POST['mode']) == "formulavalue") 
		{
			$rate_total=0;$c_total=$POST['c_total'];
			$qry="SELECT formula.*,tax.* FROM `formula_mst` as formula inner join tbl_tax as tax on find_in_set(tax.tax_id,formula.tax_id) WHERE formulaid=".$POST['eid']." order by tax_value desc";
			$row=$dbcon->query($qry);
			$j=0;
			//$dis=$POST['total']*$POST['t_dis']/100;
			$rate_total=$total=$POST['total'];
			while($tax=mysqli_fetch_assoc($row))
			{	
				if(strpos(strtolower(" ".$tax['tax_name']), "excise")==true)
				{
					$rate=$total*$tax['tax_value']/100;
					$total+=$rate;
				}
				else	
				{
					 $rate=($total)*$tax['tax_value']/100;
				}
				echo '<div class="form-group">
								<label class="col-md-5 control-label">'.$tax['tax_name'].'</label>
								<div class="col-md-5 col-xs-12">
								<input id="taxvalue'.$j.'" name="taxvalue'.$j.'" value= "'.$rate.'"type="text" class="form-control" readonly="readonly">
						</div>
					</div>
					<input id="taxname'.$j.'" name="taxname'.$j.'" value= "'.$tax['tax_name'].'" type="hidden" class="form-control">';
					$rate_total=$rate_total+$rate;
					$j++;
			}
			$g_total=$rate_total+$c_total;
			echo '<input id="rate" name="rate" value= "'.$g_total.'" type="hidden" class="form-control" >';
		}
		else if(strtolower($POST['mode'])== "load_productdata")
		{
			//$qry="select * from tbl_product where product_id=".$POST['eid'];
			$qry="select popro.*,com.stateid as com_stateid,cust.stateid as cust_stateid from `tbl_product` as popro left join `tbl_company` as com on com.company_id=".$_SESSION['company_id']." left join tbl_customer as cust on cust.cust_id=".$POST['cust_id']." where product_id=".$POST['eid'];
			$result=$dbcon->query($qry);
			$row=mysqli_fetch_assoc($result);
					
			echo json_encode( $row );
		
		}	
		else if(strtolower($POST['mode'])== "load_productdata_withoutcust")
		{
			$qry="select * from tbl_product where product_id=".$POST['eid'];
			/* $qry="select popro.*,com.stateid as com_stateid,cust.stateid as cust_stateid from `tbl_product` as popro 
			left join `tbl_company` as com on com.company_id=".$_SESSION['company_id']." 
			left join tbl_customer as cust on cust.cust_id=".$POST['cust_id']." 
			where product_id=".$POST['eid']; */
			$result=$dbcon->query($qry);
			$row=mysqli_fetch_assoc($result);
					
			echo json_encode( $row );
		
		}	
		else if(strtolower($POST['mode'])== "load_podata")
		{
				getpono($dbcon,$POST['cust_id']);
		}
		else if(strtolower($POST['mode'])== "load_podate")
		{
			$qry2="select * from tbl_pono where po_id=".$POST['po_id'];
			$result2=mysqli_fetch_assoc($dbcon->query($qry2));
			echo json_encode($result2);	
		}
		else if(strtolower($POST['mode'])== "reminder")
		{
			$qry2="select * from pay_terms where terms_id=".$POST['paymentterms'];
			$result2=mysqli_fetch_assoc($dbcon->query($qry2));
			echo json_encode($result2);	
		}
		else if(strtolower($POST['mode'])== "load_invoiceno")
		{
			$row=array();
			$query1="select * from  tbl_invoicetype where invoicetype_id=".$POST['typeid'];
			$rows=mysqli_fetch_assoc($dbcon->query($query1));
			$id=$rows['taxinvoice_start'];
			$id=$id+1;
			//$start=(date('m')<'04') ? date('y',strtotime(date('y').'-1 year')) : date('y');
			//$end = $start+1;
			if($rows['invoice_format']=='2'){
				$row['invoiceno']= str_pad($id,4,"0",STR_PAD_LEFT).$rows['format_value'];
			}
			else if($rows['invoice_format']=='1'){
				$row['invoiceno']=$rows['format_value'].str_pad($id,3,"0",STR_PAD_LEFT);
			}
			else if($rows['invoice_format']=='3'){
				$row['invoiceno']=$rows['format_value'].str_pad($id,3,"0",STR_PAD_LEFT).$rows['end_format_value'];
			}
			else{
				$row['invoiceno']=str_pad($id,3,"0",STR_PAD_LEFT);
			}
			$row['challanno']=str_pad($id,3,"0",STR_PAD_LEFT);
			$row['type_id']=$rows['type_id'];
			echo json_encode($row);
		}
     	else if(strtolower($POST['mode']) == "load_tempoutward") {
			if(!empty($POST['invoice_id'])){
				 $query="select trancation_id,product_hsn_code,product.product_name,cat.unit_name,product.product_name,product_qty,product_rate,product_disc,mst.*,product_amount 
				 from  tbl_invoicetrn as mst 
				 left join unit_mst as cat on cat.unitid=mst.unit_id 
				 left join tbl_product as product on product.product_id=mst.product_id  
				 where trancation_status=0 and invoice_id=".$POST['invoice_id']." order by trancation_id Desc";
			}else{
				 $query="select trancation_id,product_hsn_code,product.product_name,cat.unit_name,product.product_name,product_qty,product_rate,product_disc,mst.*,product_amount 
				 from  tbl_invoicetrn as mst 
				 left join unit_mst as cat on cat.unitid=mst.unit_id 
				 left join tbl_product as product on product.product_id=mst.product_id  
				 where trancation_status=3 and mst.user_id=".$_SESSION['user_id']." order by trancation_id Desc";
			}
		    $result=$dbcon->query($query);
			echo '<div class="form-group">
						<div class="col-md-12 col-xs-12">
							<table cellspacing="10" class="display table table-striped table-bordered table12">
								<tr id="field">
									<th class="text-center" width="25%">Product Name</th>
									<th class="text-center"width="8%">HSN Code</th>
									<th class="text-center"width="8%">Qty</th>
									<th class="text-center" width="8%">Rate</th>
									<th class="text-center" width="8%">MRP</th>
									<th class="text-center" width="6%">Per</th>
									<th class="text-center" width="8%" style="display:none;">Discount</th>
									<th class="text-center" width="8%" style="display:none;">Taxable value</th>
									<th class="text-center" width="12%" style="display:none;">Tax</th>
									<th class="text-center" width="8%">Amount</th>
									<th class="text-center" width="8%">Action</th>
								</tr>';
		if(mysqli_num_rows($result)>0)
		{
			$i=1;
			while($rel=mysqli_fetch_assoc($result))
			{
			 echo '<tr id="fieldtr'.$id.'" >
					<td data-label="PRODUCT NAME" style="vertical-align:top;text-align:left">
						'.$rel['product_name'].'
						'.(!empty($rel['description'])?'<br/><strong>Desc.</strong> :'.$rel['description']:'').'
					</td>
					
					<td data-label="HSN CODE" style="vertical-align:top;" class="text-center">';
							if(empty($rel['product_hsn_code'])){
								echo '-';
							}else{
								echo $rel['product_hsn_code'];
							}
					echo'</td>
					<td data-label="QTY" style="vertical-align:top;" class="text-center">
						'.$rel['product_qty'].'
					</td>
					<!--<td style="vertical-align:top;" class="text-center">
						'.$rel['sqr_ft'].'
					</td>-->';
					
			 echo '<td data-label="RATE" style="vertical-align:top;" class="text-right">
						'.$rel['product_rate'].'
					</td>
					<td data-label="MRP" style="vertical-align:top;" class="text-right">
						'.$rel['mrp'].'
					</td>					
					<td data-label="PER" style="vertical-align:top" class="text-center">';
							if(empty($rel['unit_name'])){
								echo '-';
							}else{
								echo $rel['unit_name'];
							}
						
					echo'</td>
				<td data-label="DISCOUNT" style="vertical-align:top;display:none;" class="text-right">
						'.$rel['product_discount'].' ('.$rel['discount_per'].'%)
					</td>
					<td data-label="TAXABLE VALUE" style="vertical-align:top;display:none;" class="text-right">
						'.($rel['product_amount']).'
					</td>
					<td data-label="TAX" style="vertical-align:top;display:none;" class="text-left">';
					if(empty($rel['formulaid'])){
						echo '-';
					}else{
						/* echo (empty($rel['tax_name1']) ? " " : $rel['tax_name1'] .' : '. $rel['tax_amount1']).'<br/>';
						echo (empty($rel['tax_name2']) ? " " : $rel['tax_name2'] .' : '. $rel['tax_amount2']).'<br/>';
						echo (empty($rel['tax_name3']) ? " " : $rel['tax_name3'] .' : '. $rel['tax_amount3']).'<br/>'; */
						echo show_tax($dbcon,$rel['trancation_id'],"tbl_invoicetrn","trancation_id");
					}
					echo'</td>
					<td data-label="AMOUNT" style="vertical-align:top" class="text-right">
						'.$rel['total'].'
					</td>
	 <input type="hidden" name="amount[]" id="amount'.$i.'" value="'.$rel['total'].'"/>
															
					 <td data-label="ACTION" style="vertical-align:top">
							<button type="button" class="btn btn-round btn-warning btn-xs" onclick="edit_data('.$rel['trancation_id'].');" id="fieldedit'.$i.'"><i class="fa fa-pencil"></i></button>
							<button type="button" class="btn btn-round btn-danger btn-xs" onclick="delete_data('.$rel['trancation_id'].','.$POST['invoice_id'].');" id="fieldremove'.$i.'"><i class="fa fa-times"></i></button>
					</td>	
			</tr>';
			$i++;
			}
		}
		else{
		echo '<tr><td colspan="10" class="text-center">NO DATA FOUND</td></tr>';
			}
			echo '
	 
		</table>			 
							</div>
                           
							</div>	';
		}
		else if(strtolower($POST['mode'])== "preedit")
		{
			$q = $dbcon -> query("SELECT mst.*,pro.product_name FROM tbl_invoicetrn as mst left join tbl_product as pro on mst.product_id=pro.product_id WHERE trancation_id= '$POST[id]'");
			$r = $q->fetch_assoc();
			/*if(strtolower($POST['table'])=='tbl_invoicetrntemp')
			{
				$row['producthtml']=getproduct($dbcon,0,'0,2');
			}
			else
			{
					$row['producthtml']=getproduct($dbcon,0,'0,2');
			}*/
			echo json_encode($r);
		}
		else if(strtolower($POST['mode'])== "delete_data")
		{
			$row=array();
			$info['trancation_status']=2;	
				
			$updateid=update_record("tbl_invoicetrn", $info,"trancation_id=".$POST['eid'] , $dbcon);
			
			if(!empty($POST['invoice_id'])){
				$uid=general_book_tax_entry($dbcon,$POST['invoice_id']);
			}

			if($updateid)
				$row['res']="1";
			else
				$row['res']="0";
			echo json_encode($row);
		}
		else if(strtolower($POST['mode'])== "last_rate")
		{
			$query="select product_rate,trancation_id,trancation_status,product_id from tbl_invoicetrn as trn left join tbl_invoice as mst on mst.invoice_id=trn.invoice_id where cust_id=".$POST["cust_id"]." and product_id=".$POST["product_id"]." and trancation_status=0 order by trancation_id DESC";
			$prel=mysqli_fetch_assoc($dbcon->query($query));
			echo $prel['product_rate'];
		}
		else if(strtolower($POST['mode'])== "load_consignee")
		{
				echo get_custmer_consignee($dbcon,$POST['cust_id']);
		}
		/* else if(strtolower($POST['mode'])== "load_sales_order")
		{
				echo get_sales_order($dbcon,$POST['cust_id']);
		} */
		else if(strtolower($POST['mode'])== "load_sales_order_data")
		{
			$q = $dbcon -> query("SELECT * from tbl_sales_order where sales_order_id=".$POST['sales_order_id']);
			$rel = $q->fetch_assoc();
			
			$resp['sales_order_no'] = $rel['sales_order_no'];
			$resp['sales_order_date'] = date("d-m-Y",strtotime($rel['sales_order_date']));
			$resp['pro_html'] = get_sales_order_data($dbcon,$POST['sales_order_id']);
			echo json_encode($resp);
		}
		else if(strtolower($POST['mode'])== "load_sales_pro")
		{
			$resp['pro_html']=getproduct($dbcon,0,'0,2,3');
			echo json_encode($resp);
		}
		else if(strtolower($POST['mode'])== "loadsales_productdata")
		{
			$q = $dbcon -> query("SELECT sotrn.*,
				(select IFNULL(sum(product_qty),0)  from tbl_invoicetrn as insub 
					left join tbl_invoice as inv on inv.invoice_id=insub.invoice_id
				where trancation_status=0 and inv.sales_order_id=sotrn.sales_order_id and insub.product_id=sotrn.product_id) as qty
				from tbl_sales_ordertrn as sotrn where sales_order_id=".$POST['sales_order_id']." and sales_ordertrn_status=0 and product_id=".$POST['product_id']." ");
			$resp = $q->fetch_assoc();
			
			echo json_encode($resp);
		}
		else if(strtolower($POST['mode'])== "load_po_no")
		{
		    
				$q = $dbcon -> query("SELECT l_pono,l_po_date from tbl_ledger as sotrn where l_id=".$POST['cust_id']."");
			$resp = $q->fetch_assoc();
			
			$resp["ldate"] = date('d-m-Y',strtotime($resp['l_po_date']));
			echo json_encode($resp);
			
			
		}
		else if(strtolower($POST['mode'])== "load_qty")
		{
		    
				echo getsale_productqty($dbcon,$POST['product_id']);
			
			
		}
		else if(strtolower($POST['mode'])== "load_tax_per")
		{
			echo get_tax_per1($dbcon,$POST['formulaid']);
			//echo $POST['formulaid'];
			//echo "123";
		}
		else if(strtolower($POST['mode'])== "load_rate_hist")
		{
			$resp='';
			$query="select inv.*,cust.company_name,pro.product_name,trn.product_rate from tbl_invoice as inv
					inner join tbl_invoicetrn as trn on inv.invoice_id=trn.invoice_id 
					inner join tbl_customer as cust on cust.cust_id=inv.cust_id
					inner join tbl_product as pro on pro.product_id=trn.product_id
					where inv.invoice_status=0 and trn.trancation_status=0 and inv.cust_id=".$POST["cust_id"]." and trn.product_id=".$POST["product_id"]." order by trn.trancation_id DESC LIMIT 10";
				
			$rs_prel=$dbcon->query($query);
			$rs_prel_num_rows=mysqli_num_rows($rs_prel);
				
			if($rs_prel_num_rows>0){
				while($prel=mysqli_fetch_assoc($rs_prel)){
			
					$resp.='<tr>
								<td data-label="Invoice No." class="text-center">'.$prel['invoice_no'].'</td>
								<td data-label="Invoice Date" class="text-center">'.date('d-m-y',strtotime($prel['invoice_date'])).'</td>
								<td data-label="Product Date" class="text-center">'.$prel['product_rate'].'</td>
							</tr>';
					$row['cust_name']=$prel['company_name'];
					$row['product_name']=$prel['product_name'];		
				}
			}
			else{
				$resp.='<tr>
							<td colspan="3" class="text-center">NO DATA FOUND !!</td>
						</tr>';
					$row['cust_name']="";
					$row['product_name']="";
			}
			
			
			$row['resp']=$resp;
			
			echo json_encode($row);
		}
		else if(strtolower($POST['mode'])== "load_val"){
		$s_date=explode(' - ',$POST['date']);
	
			 $invoice_count="Select SUM(total) as itotal,SUM(product_amount) as taxable_amt from tbl_invoice as invoice
	left join tbl_invoicetrn as invtrn on invtrn.invoice_id=invoice.invoice_id
	where  invoice_date>='".date('Y-m-d',strtotime($s_date['0']))."' AND invoice_date<='".date('Y-m-d',strtotime($s_date['1']))."' AND invoice_status=0 and invtrn.trancation_status=0 and company_id=".$_SESSION['company_id']."";
			$count_invoice=mysqli_fetch_assoc($dbcon->query($invoice_count));
			
			$invoice_paid="Select SUM(res_trn.paid_amount) as ipaid_amount,SUM(res_trn.total_amount) as tpaid_amount from tbl_receipt as rec 
							left join tbl_receipt_trn as res_trn on res_trn.receipt_id=rec.receipt_id
							where  rec.receipt_date>='".date('Y-m-d',strtotime($s_date['0']))."' AND rec.receipt_date<='".date('Y-m-d',strtotime($s_date['1']))."' AND rec.status=0 and res_trn.status=0 and invoice_id!=0 and rec.company_id=".$_SESSION['company_id'];
			
			$count_paid=mysqli_fetch_assoc($dbcon->query($invoice_paid));
			$count['g_total']= intval($count_invoice['itotal']);
			$count['taxable_amt']= intval($count_invoice['taxable_amt']);
			$count['paid_amount']=intval($count_paid['ipaid_amount']);
			$count['total_paid_amount']=intval($count_paid['tpaid_amount']);
			echo json_encode($count);
		}
		else if(strtolower($POST['mode'])== "use_cr"){
			//Delete Old paid Amount from Invoice Table
			$inv_upd = $dbcon->query("UPDATE tbl_invoice INNER JOIN tbl_used_credit ON tbl_invoice.invoice_id = tbl_used_credit.invoice_id SET paid_amount = paid_amount - ( SELECT SUM( inr_cr.used_credit_amt ) 
			FROM tbl_used_credit AS inr_cr WHERE inr_cr.invoice_id =".$POST['invoice_id']." ) 
			WHERE tbl_used_credit.invoice_id =".$POST['invoice_id']);
			
			foreach($POST['used_credit_amt'] as $key => $name)
			{
				if(floatval($POST['used_credit_amt'][$key])){
					//Delete Old paid Amount from Credit Note Table
					$cr_upd = $dbcon->query("UPDATE tbl_credit_note 
					inner join tbl_used_credit on tbl_credit_note.credit_note_id=tbl_used_credit.credit_note_id set paid_amount = paid_amount - used_credit_amt
					where tbl_credit_note.credit_note_id=".$POST['credit_note_id'][$key]." and tbl_used_credit.invoice_id=".$POST['invoice_id']);
				}
			}
			$del_id=delete_record('tbl_used_credit',"invoice_id=".$POST['invoice_id'], $dbcon);	
			
			foreach($POST['used_credit_amt'] as $key => $name)
			{
				if(floatval($POST['used_credit_amt'][$key])){
					//Entry in Used Credit Table
					$info1['invoice_id']		= $POST['invoice_id'];
					$info1['credit_note_id']	= $POST['credit_note_id'][$key];
					$info1['used_credit_amt']	= $POST['used_credit_amt'][$key];
					$info1['user_id']			= $_SESSION['user_id'];
					$info1['company_id']		= $_SESSION['company_id'];
					$info1['cdate']				= date("Y-m-d H:i:s");
					$insertrnid=add_record('tbl_used_credit', $info1, $dbcon);
					
					//Update In Credit Note Table
					$cr_upd = $dbcon->query("UPDATE tbl_credit_note SET paid_amount = paid_amount + ".$POST['used_credit_amt'][$key]." WHERE credit_note_id = ".$POST['credit_note_id'][$key]);
				}
			}
			
			
			//Update In Invoice Table
			$inv_upd =  $dbcon->query("UPDATE tbl_invoice SET paid_amount = paid_amount + ".$POST['total_cr']." WHERE invoice_id = ".$POST['invoice_id']);
			
			if($insertrnid){
				$resp['msg']='1';
			}
			else{
				$resp['msg']='0';
			}
			echo json_encode($resp);
		}
		else if(strtolower($POST['mode'])== "add_genral_book"){
			$uid=general_book_tax_entry($dbcon,$POST['invoice_id']);
			//var_dump($uid);
		}
		else if(strtolower($POST['mode'])== "send_mail"){
			$files=array();
			
			$query="select inv.*,led.l_name,led.cust_email from tbl_invoice as inv
					left join tbl_ledger as led on led.l_id=inv.cust_id
					where inv.invoice_status=0 and invoice_id=".$POST['invoice_id'];
			$rs_prel=$dbcon->query($query);
			//$rs_prel_num_rows=mysqli_num_rows($rs_prel);
				$prel=mysqli_fetch_assoc($rs_prel);
			
			$pdf_upload=quotation_pdf($POST['invoice_id'],"pdf",$dbcon);
			
			$msg=stripslashes("Invoice No : ".$prel['invoice_no']."(".$prel['g_total'].")");
			//$msg="demo";
				$fname=$pdf_upload;
				$send_mail_id=$prel['cust_email'];
				$subject="Invoice No :".$prel['invoice_no'];
				array_push($files,$fname);
				$bcc="";
				$cc="";$files1="";
				
			multi_attach_mail1($send_mail_id, $files,$files1,$bcc,$cc,$subject,$dbcon,$msg);
			//var_dump("122");
		}
		
function multi_attach_mail1($to, $files,$files1,$bcc,$cc,$quotation_subject,$dbcon,$msg){
	//$mailin = new Mailin("https://api.sendinblue.com/v2.0","85rJyNsCpv0hHBbD");
	$mailin = new Mailin("https://api.sendinblue.com/v2.0","c7JbvKzptTHNLw82");
	if(empty($quotation_subject))
	{
		$quotation_subject=" Mail From ".TITLE;
	}
	//var_dump($to);
	//var_dump($files1);
	$attch=array();
	if(0<count($files)){
		for($x=0;$x<count($files);$x++){
			$file = fopen(invoice_A.$files[$x],"rb");
			$data = fread($file,filesize(invoice_A.$files[$x]));
			fclose($file);
			$data = chunk_split(base64_encode($data));
			$attch[$files[$x]]= $data;
		}
	}
	/* if(0<count($files1)){
		for($x=0;$x<count($files1);$x++){
			$file = fopen(qut_pdfA.$files1[$x],"rb");
			$data = fread($file,filesize(qut_pdfA.$files1[$x]));
			fclose($file);
			$data = chunk_split(base64_encode($data));
			$attch[$files1[$x]]= $data;
		}
	} */
	
	//var_dump(array($files[0]=>$data,$files[1]=>$data));

	//exit;
	$c='echo //';
	$c1='echo //';
	if($cc!='')
	{
		$c='';
	}
	if($bcc!='')
	{
		$c1='';
	}
	# Define the campaign settings\
	//var_dump($attch);
	if($attch){
		$data = array( 
		"to" => array($to=>$to),
		$c."cc" => array($cc=>$cc),
		$c1."bcc" => array($bcc=>$bcc),
		"from" => array(ADMIN_EMAIL,ADMIN_EMAIL),
		"subject" => $quotation_subject,
		"html" => $msg,
		//"attachment" => array("https://www.metrtechnology.com/img/logo.png")
		"attachment" => $attch
		);
	}else{
		$data = array( 
		"to" => array($to=>$to),
		$c."cc" => array($cc=>$cc),
		$c1."bcc" => array($bcc=>$bcc),
		"from" => array(ADMIN_EMAIL,ADMIN_EMAIL),
		"subject" => $quotation_subject,
		"html" => $msg
		//"attachment" => array("https://www.metrtechnology.com/img/logo.png")
		//"attachment" => $attch
		);
	}
	//var_dump($data);
	//var_dump($mailin->send_email($data));
	$mailin->send_email($data);
	
	if(0<count($files)){
		for($x=0;$x<count($files);$x++){
			unlink(invoice_A.$files[$x]);
		}
	}
	unlink($dirname."/".$file);
	
}		
function get_product_tax($dbcon,$product_amount,$formulaid)
{
	$qry="SELECT formula.*,tax.* FROM `formula_mst` as formula inner join tbl_tax as tax on find_in_set(tax.tax_id,formula.tax_id) WHERE formulaid=".$formulaid." order by tax_value desc";
	$row=$dbcon->query($qry);
	$rate_total=$total=$product_amount;
	$i=1;
	while($tax=mysqli_fetch_assoc($row))
	{	
		$info['tax_name'.$i]=$tax['tax_name'];
		$info['tax_amount'.$i]=$tax_amount=($total)*$tax['tax_value']/100;
		$rate_total+=$tax_amount;
		$i++;
	}
	for($j=$i;$j<=3;$j++)
	{
		$info['tax_name'.$i]='';
		$info['tax_amount'.$i]='';		
	}
	$info['total']=$rate_total;
	return $info;
}
function general_book_tax_entry($dbcon,$invoice_id,$ref_date){
	$qry1="select group_concat(trancation_id) as tid from tbl_invoicetrn as cert where trancation_status=0 and invoice_id=".$invoice_id;
	$ro=$dbcon->query($qry1);
	$re=mysqli_fetch_assoc($ro);
	
	$qry122="select * from tbl_invoice as cert where invoice_status=0 and invoice_id=".$invoice_id;
	$ro12=$dbcon->query($qry122);
	$rea=mysqli_fetch_assoc($ro12);
	
	$qry="SELECT utax.*,sum(tax_amount) as tamount FROM `tbl_used_tax` as utax WHERE tax_used_status=0 and used_transaction_id in (".$re["tid"].") and table_name='tbl_invoicetrn' group by ledger_id order by tax_used_id desc";
	$row=$dbcon->query($qry);
	while($tax=mysqli_fetch_assoc($row))
	{
		$qry12="select general_book_id from tbl_general_book as cert where genral_book_status=0 and ledger_id=".$tax['ledger_id']." and table_id=".$invoice_id." and table_name='tbl_invoice'";
			$ros=$dbcon->query($qry12);
			$re2=mysqli_fetch_assoc($ros);
		
	
		$info1['table_name']	= "tbl_invoice";
		$info1['table_id']		= $invoice_id;
		$info1['ref_date']		= date("Y-m-d",strtotime($rea['invoice_date']));
		$info1['entry_type']	= 1;
		$info1['ledger_id']		= $tax['ledger_id'];
		$info1['amount']		= $tax['tamount'];
		$info1['user_id']		= $_SESSION['user_id'];
		$info1['cdate']			= date("Y-m-d H:i:s");
		$info1['company_id']	= $_SESSION['company_id'];
		
		if(!empty($re2['general_book_id'])){
			$updateid=update_record("tbl_general_book", $info1,"general_book_id=".$re2['general_book_id'] , $dbcon);
		}else{
			$inserid=add_record("tbl_general_book", $info1, $dbcon);
		}
		//var_dump($re2['general_book_id']);
	}
	
}

?>