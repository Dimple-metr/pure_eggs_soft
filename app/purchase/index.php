<?php

session_start();
$AJAX = true;
include("../../config/config.php");
//error_reporting(E_ALL);
include("../../config/session.php");
include("../../include/function_database_query.php");
include_once("../../include/common_functions.php");

$POST = ($_POST != NULL)? bulk_filter($dbcon,$_POST) : bulk_filter($dbcon,$_GET) ;
		
if(strtolower($POST['mode']) == "fetch") {
			
    $s_date=explode(' - ',$POST['date']);
    $_SESSION['start']=$s_date[0];
    $_SESSION['end']=$s_date[1];
		
    $where='';
    $where.="  and po_date>='".date('Y-m-d',strtotime($s_date[0]))."' AND po_date<='".date('Y-m-d',strtotime($s_date[1]))."'";
    $appData = array();
    $i=1;
    $aColumns = array('po_id','po_no','vender.company_name','city.city_name','po_date','g_total','paid_amount','status','po.cdate','po.userid');
    $sIndexColumn = "po_id";
    $isWhere = array("status = 0".$where.check_user('po'));
    $sTable = "tbl_pono as po";			
    $isJOIN = array('left join tbl_ledger as vender on po.vender_id=vender.l_id','left join  city_mst city on vender.cityid=city.cityid');
    $hOrder = "po.po_id desc";
    include('../../include/pagging.php');
    $id=1;
			
    foreach($sqlReturn as $row) {
        $row_data = array();
        $row_data[] = $row['po_no'];
        $row_data[] = date('d M, Y',strtotime($row['po_date']));
        $row_data[] = $row['company_name'];
        $row_data[] = $row['city_name'];
        $row_data[] = $row['g_total'];
        /*if($row['g_total']>$row['paid_amount'])
        {
                $row_data[] = "<div class='external-event label label-warning ui-draggable' style='position: relative;'>DUE (RS. ".($row['g_total']-$row['paid_amount']).")</div>";
        }
        else
        {
                $row_data[]="<div class='external-event label label-success ui-draggable' style='position: relative;'>Paid</div>";
        }*/
			 
        $addpayment='';$delete='';$edit='';
        if($row['g_total']>$row['paid_amount']){
                $dr_btn= '<a class="btn btn-xs btn-primary" data-original-title="Use Debit" data-toggle="tooltip" data-placement="top" href="'.ROOT.'use_dr_note/'.$row['po_id'].'"><i class="fa fa-plus"></i></a>';
        }
        else{
                $dr_btn= '';
        }
					
        $delete='<button class="btn btn-xs btn-danger" data-original-title="Delete" data-toggle="tooltip" data-placement="top" onClick="delete_invoice('.$row['po_id'].')"><i class="fa fa-trash-o"></i></button>';
        $view='<a class="btn btn-xs btn-info" data-original-title="View" data-toggle="tooltip" data-placement="top" href="'.ROOT.'purchase_view/'.$row['po_id'].'"><i class="fa fa-eye"></i></a> ';

        $edit='<a class="btn btn-xs btn-warning" data-original-title="Edit" data-toggle="tooltip" data-placement="top" href="'.ROOT.'purchaseedit/'.$row['po_id'].'"><i class="fa fa-pencil"></i></a>';
        $row_data[] = $edit.' '.$delete.' '.$view;
			 
        $appData[] = $row_data;
        $id++;
        }
        $output['aaData'] = $appData;
        echo json_encode( $output );
}
else if(strtolower($POST['mode']) == "add") {
			 
    $info['po_no']		= $POST['po_no'];
    $info['vender_id']          = $POST['vender_id'];
    $info['purchase_ledger_id']	= $POST['purchase_ledger_id'];
    $info['po_date']            = date('Y-m-d',strtotime($POST['po_date']));
    $info['order_no']           = $POST['order_no'];
    $info['order_date']         = date('Y-m-d',strtotime($POST['order_date']));
    $info['round_off']          = $POST['round_off'];
    $info['packing']            = $POST['paking'];
    $info['remark']		= $POST['remark'];
    $info['g_total']            = $POST['g_total'];
    
    if(isset($POST['save_print']))
    {
        $info['print_status']	= $POST['print_status'];
    }
    $info['cdate']              = date("Y-m-d H:i:s");
    $info['mdate']		= date("Y-m-d H:i:s");
    $info['userid']		= $_SESSION['user_id'];
    $info['company_id']		= $_SESSION['company_id'];
    $inserpoid = add_record('tbl_pono', $info, $dbcon);
				
    $info_trn['po_id']		= $inserpoid;
    $info_trn['potrancation_status']= 0;
    $updateid=update_record('tbl_potrancation', $info_trn,"potrancation_status=3 and user_id=".$_SESSION['user_id'] , $dbcon);

    add_general_book_entry($dbcon,"tbl_purchase",$inserpoid,1,$POST['vender_id'],$POST['g_total'],$general_book_id,$POST['po_date']);

    general_book_tax_entry($dbcon,$inserpoid);
				
    /* $info1['table_name']	= "tbl_purchase";
    $info1['table_id']		= $inserpoid;
    $info1['entry_type']	= 1;
    $info1['ledger_id']		= $POST['vender_id'];
    $info1['amount']		= $POST['g_total'];
    $info1['user_id']		= $_SESSION['user_id'];
    $info1['cdate']			= date("Y-m-d H:i:s");
    $info1['company_id']	= $_SESSION['company_id'];

    $inserid11=add_record("tbl_general_book", $info1, $dbcon); */

    /**Payment Reminder Entry START***/
    if(!empty($POST['payment_reminder']) && $POST['payment_reminder']>0){
        
        $remainder_date=addDayswithdate($info['po_date'],$POST['payment_reminder']);//($date,$days)
				
        $query="SELECT `company_name` FROM tbl_customer WHERE `cust_id` = ".$_POST['vender_id'];
        $rel=mysqli_fetch_assoc($dbcon->query($query));	
				
        $qry='SELECT inv.*,
                (SELECT SUM(rtrn.paid_amount) as amuount 
                FROM tbl_receipt_trn as rtrn 
                WHERE rtrn.status=0 
                    AND rtrn.purchase_id = inv.po_id 
                GROUP BY rtrn.purchase_id ) paidamo 
            FROM tbl_pono as inv
            LEFT JOIN tbl_customer as cust ON cust.cust_id = inv.vender_id 
            WHERE inv.company_id = '.$_SESSION['company_id'].'';
        
        $result1=$dbcon->query($qry);
				
        while($re=mysqli_fetch_assoc($result1)) {
            $tamount=$re['g_total'];
            $due=$tamount-$re['paidamo'];
        }
				
        $info_remainder['task_detail']		= 'Purchase #'.$rel['company_name'].' - '.$info['po_no'].' - RS '.$due.'# Payment On '.date('d-m-Y',strtotime($remainder_date));
        $info_remainder['date']			= $remainder_date;
        $info_remainder['ref_id']		= $inserpoid;
        $info_remainder['ref_table']		= 'tbl_pono';
        $info_remainder['user_id']		= $_SESSION['user_id'];
        $info_remainder['company_id']		= $_SESSION['company_id'];
        $inserinvoiceid1 = add_record('todo_mst', $info_remainder, $dbcon);
				
    }
    /**Payment Reminder Entry END***/
		
    /** Purchase Order Entry Start ***/
    if($POST['purchaseorder_id']){
            $info_purchase_order['purchase_status']  = 1;
            $info_purchase_order['used_purchase_id'] = $inserpoid;
            $updatepurchaseid=update_record('tbl_purchaseorder', $info_purchase_order,"purchaseorder_id=".$POST['purchaseorder_id'], $dbcon);
    }
    /** Purchase Order Entry End ***/		

		
					if(isset($POST['save_print']))
					{
						$arr['printstatus']=$POST['print_status'];
						$arr['msg']="1";
						$arr['eid']=$inserpoeid;
					}
					else
					{
						if($inserpoid)
						{	
							$arr['msg']="1";							
						}
						else
							$arr['msg']="0";
					}
			echo json_encode($arr);					
		 
		}		
		else if(strtolower($POST['mode']) == "edit") {
			 
							$info['po_no']		= $POST['po_no'];
							$info['purchase_ledger_id']	= $POST['purchase_ledger_id'];
							$info['vender_id']	= $POST['vender_id'];
							$info['po_date']	= date('Y-m-d',strtotime($POST['po_date']));
							$info['order_no']	= $POST['order_no'];
							$info['order_date']	= date('Y-m-d',strtotime($POST['order_date']));
							$info['round_off']	= $POST['round_off'];
							$info['packing']	= $POST['paking'];
							$info['remark']		= $POST['remark'];
							$info['g_total']	= $POST['g_total'];
							$info['mdate']		= date("Y-m-d H:i:s");
							$info['userid']		= $_SESSION['user_id'];
							$info['company_id']		= $_SESSION['company_id'];
							if(isset($POST['save_print']))
							{
								$info['print_status']	= $POST['print_status'];
							}
							$info['cdate']				= 	date("Y-m-d H:i:s");
							$info['userid']			= $_SESSION['user_id'];
							$updateid=update_record('tbl_pono', $info,"po_id=".$POST['eid'] , $dbcon);
							
						$general_book_id=get_general_book_id($dbcon,'tbl_purchase',$POST['eid'],$POST['vender_id']);	
						add_general_book_entry($dbcon,"tbl_purchase",$POST['eid'],1,$POST['vender_id'],$POST['g_total'],$general_book_id,$POST['po_date']);
					
						general_book_tax_entry($dbcon,$POST['eid']);
						
							/* $info_gen['table_name']		= "tbl_purchase";
							$info_gen['table_id']		= $POST['eid'];
							$info_gen['entry_type']		= 1;
							$info_gen['ledger_id']		= $POST['vender_id'];
							$info_gen['amount']			= $POST['g_total'];
							$info_gen['user_id']		= $_SESSION['user_id'];
							$info_gen['cdate']			= date("Y-m-d H:i:s");
							$info_gen['company_id']		= $_SESSION['company_id'];
							if(empty($general_book_id)){
								$inserid_gen=add_record("tbl_general_book", $info_gen, $dbcon);
							}else{
								$updateid=update_record('tbl_general_book', $info_gen,"general_book_id=".$general_book_id , $dbcon);
							} */
						
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
		else if(strtolower($POST['mode']) == "delete") {
			$info['status']		= 2;
			$info1['potrancation_status']		= 2;
			$q="select * from tbl_pono where po_id=".$POST['eid'];
			$row=mysqli_fetch_assoc($dbcon->query($q));
			$file=$row['po_pdf'];
			unlink(POPDF_A.$file);
			$updateinvoiceid=update_record('tbl_pono', $info,"po_id=".$POST['eid'] , $dbcon);	
			$updatetrancationid=update_record('tbl_potrancation', $info1,"po_id=".$POST['eid'] , $dbcon);	
			// Update Purchase Order Status
			$info_purchase_order['purchase_status']  = 0;
			$updatepurchaseid=update_record('tbl_purchaseorder', $info_purchase_order,"used_purchase_id=".$POST['eid'], $dbcon);
			
			$info122['genral_book_status']		= 2;
			$updatetd=update_record('tbl_general_book', $info122,"table_name='tbl_purchase' and table_id=".$POST['eid'] , $dbcon);
						
			if($updatetrancationid)
				echo "1";	
			else
				echo "0";			
		}
		else if(strtolower($POST['mode'])== "load_productdata")
		{
			//$qry="select popro.*, from tbl_purchaseproduct as porpo left join tbl_company as com on com.company_id=".$_SESSION['company_id']." where product_id=".$POST['eid'];
			$qry="select popro.*,com.stateid as com_stateid,ven.stateid as ven_stateid from `tbl_product` as popro left join `tbl_company` as com on com.company_id=".$_SESSION['company_id']." left join tbl_customer as ven on ven.cust_id=".$POST['vender_id']." where product_id=".$POST['eid'];
			$result=$dbcon->query($qry);
		
			$row=mysqli_fetch_assoc($result);
					
			echo json_encode( $row );
		
		}
		else if(strtolower($POST['mode']) == "formulavalue") {
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
					$rate=number_format($rate,2,".","");
				}
				else	
				{
					 $rate=($total)*$tax['tax_value']/100;
					 $rate=number_format($rate,2,".","");
				}
				echo '<div class="form-group">
								<label class="col-md-6 control-label">'.$tax['tax_name'].'</label>
								<div class="col-md-4 col-xs-11">
								<input id="taxvalue'.$j.'" name="taxvalue'.$j.'" value= "'.$rate.'"type="text" class="form-control" readonly="readonly">
						</div>
					</div>
					<input id="taxname'.$j.'" name="taxname'.$j.'" value= "'.$tax['tax_name'].'" type="hidden" class="form-control">';
					$rate_total=$rate_total+$rate;
					$j++;
			}
			$g_total=$rate_total+$c_total;
			$g_total=number_format($g_total,2,".","");

			echo '<input id="rate" name="rate" value= "'.$g_total.'" type="hidden" class="form-control" >';
		}
		else if(strtolower($POST['mode']) == "fieldadd") {
		
				$info1['product_id']		= $POST['product_id'];
				$info1['description']		= text_rnremove($_POST['product_des']);
				$info1['product_hsn_code']	= $POST['product_hsn_code'];
				$info1['product_qty']		= $POST['product_qty'];
				$info1['unit_id']			= $POST['unit_id'];
				$info1['product_rate']		= $POST['product_rate'];
				$info1['product_discount']	= $POST['product_discount'];
				$info1['discount_per']		= $POST['discount_per'];
				$info1['formulaid']			= $POST['formulaid'];
				$info1['product_amount']	= $POST['taxable_value'];
				$info1['total']				= $POST['product_amount'];
				//$info=get_product_tax($dbcon,$total,$POST['formulaid']);
				//$info1=array_merge($info1,$info);
			$table='tbl_potrancation';$tableid='potrancation_id';
			if(!empty($POST['po_id']))
			{
					$info1['po_id']= $POST['po_id'];
					$table='tbl_potrancation';
					$tableid='potrancation_id';
			}
			else
			{
				$info1['user_id']				= $_SESSION['user_id'];
				$info1['potrancation_status']	= 3;
			}
			if(empty($POST['edit_id']))
			{
				$inserid=add_record($table, $info1, $dbcon);
				$insert_tax=add_tax_record($dbcon,$inserid,"tbl_potrancation","po_id",$POST['formulaid'],$POST['taxable_value']);
			}
			else
			{
				$updateid=update_record($table, $info1,$tableid."=".$POST['edit_id'] , $dbcon);
				$insert_tax=add_tax_record($dbcon,$POST['edit_id'],"tbl_potrancation","po_id",$POST['formulaid'],$POST['taxable_value']);				
			}
			//var_dump($info1);
		}
		else if(strtolower($POST['mode']) == "load_tempoutward") {
			if(!empty($POST['purchase_id'])){
				$query="select potrancation_id,product_hsn_code,product.product_name,cat.unit_name,product.product_name,mst.*,product_qty,product_rate,product_disc,product_amount 
				from  tbl_potrancation as mst 
				left join unit_mst as cat on cat.unitid=mst.unit_id 
				left join tbl_product as product on product.product_id=mst.product_id  
				where potrancation_status=0 and po_id=".$POST['purchase_id'];
			}else{
				 $query="select potrancation_id,product_hsn_code,product.product_name,cat.unit_name,product.product_name,mst.*,product_qty,product_rate,product_disc,product_amount 
				from  tbl_potrancation as mst 
				left join unit_mst as cat on cat.unitid=mst.unit_id 
				left join tbl_product as product on product.product_id=mst.product_id  
				where potrancation_status=3 and mst.user_id=".$_SESSION['user_id'];
			}
		      // $query="select temppotrn_id,product_hsn_code,product.product_name,cat.unit_name,product.product_name,mst.description,mst.*,product_qty,product_rate,product_disc,product_amount from  tbl_potrntemp as mst left join unit_mst as cat on cat.unitid=mst.unit_id left join tbl_product as product on product.product_id=mst.product_id  where temp_status=0 and mst.user_id=".$_SESSION['user_id'];
			$result=$dbcon->query($query);
			
			echo ' <div class="form-group">
							  <div class="col-md-12 col-xs-12">
						<table cellspacing="10" style="border-spacing:10px;" class="table12 display table  table-striped table-bordered">
						<tr id="field">
							<th class="text-center" width="25%">Product Name</th>
							<th class="text-center"width="8%">HSN Code</th>
							<th class="text-center"width="8%">Qty</th>
							<!--<th class="text-center"width="8%">Sqr/Ft</th>-->
							<th class="text-center"width="10%">Rate</th>
							<th class="text-center"width="6%">Per</th>
							
						<th class="text-center"width="8%">Discount</th>
						<th class="text-center"width="10%">Taxable value</th>
							<th class="text-center"width="15%">Tax</th>
							<th class="text-center"width="12%">Amount</th>
						 	<th class="text-center"width="10%">Action</th>
						</tr>';
		if(mysqli_num_rows($result)>0)
		{
			$i=1;
			while($rel=mysqli_fetch_assoc($result))
			{
			 echo '<tr id="fieldtr'.$id.'" >
					<td data-label="Product Name" style="vertical-align:top;text-align:left">
						'.$rel['product_name'].'
						'.(!empty($rel['description'])?'<br/><strong>Desc.</strong> :'.$rel['description']:'').'
					</td>
					<td data-label="HSN Code" style="vertical-align:top;" class="text-center">';
							if(empty($rel['product_hsn_code'])){
								echo '-';
							}else{
								echo $rel['product_hsn_code'];
							}
					echo'</td>
					<td data-label="Qty" style="vertical-align:top;" class="text-center">
						'.$rel['product_qty'].'
					</td>
                  <!-- <td style="vertical-align:top;" class="text-center">
						'.$rel['sqr_ft'].'
					</td>-->					
					<td data-label="Rate" style="vertical-align:top;" class="text-right">
						'.$rel['product_rate'].'
					</td>				
					<td data-label="Per" style="vertical-align:top" class="text-center">';
							if(empty($rel['unit_name'])){
								echo '-';
							}else{
								echo $rel['unit_name'];
							}
					echo'</td>
					<td data-label="Discount" style="vertical-align:top" class="text-right">
						'.$rel['product_discount'].' ('.$rel['discount_per'].'%)
					</td>
					
				<td data-label="Taxable value" style="vertical-align:top" class="text-right">
						'.($rel['product_amount']).'
					</td>
					<td data-label="Tax" style="vertical-align:top" class="text-left">';
					if(empty($rel['formulaid'])){
						echo '-';
					}else{
						/* echo (empty($rel['tax_name1']) ? " " : $rel['tax_name1'] .' : '. $rel['tax_amount1']).'<br/>';
						echo (empty($rel['tax_name2']) ? " " : $rel['tax_name2'] .' : '. $rel['tax_amount2']).'<br/>';
						echo (empty($rel['tax_name3']) ? " " : $rel['tax_name3'] .' : '. $rel['tax_amount3']).'<br/>'; */
						echo show_tax($dbcon,$rel['potrancation_id'],"tbl_potrancation","po_id");
					}
					echo'</td>
					<td data-label="Amount" style="vertical-align:top" class="text-right">
						'.$rel['total'].'
					</td>
	<input type="hidden" name="amount[]" id="amount'.$i.'" value="'.$rel['total'].'"/>
											
					 <td data-label="Action" style="vertical-align:top">
							<button type="button" class="btn btn-round btn-warning btn-xs" onclick="edit_data('.$rel['potrancation_id'].');" id="fieldremove'.$id.'"><i class="fa fa-pencil"></i></button>
							<button type="button" class="btn btn-round btn-danger btn-xs" onclick="delete_data('.$rel['potrancation_id'].','.$POST['purchase_id'].');" id="fieldremove'.$id.'"><i class="fa fa-times"></i></button>
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
			$q = $dbcon -> query("SELECT mst.*,pro.product_name FROM tbl_potrancation as mst left join tbl_product as pro on mst.product_id=pro.product_id WHERE potrancation_id = '$POST[id]'");
			$r = $q->fetch_assoc();
			echo json_encode($r);
		}
		else if(strtolower($POST['mode'])== "getproduct_amount")
		{
			$arr=get_product_tax($dbcon,$POST['product_amount'],$POST['formulaid']);
			echo json_encode($arr);
		}
		else if(strtolower($POST['mode'])=="delete_data")
		{
			$row=array();
				$info['potrancation_status']=2;	
			$updateid=update_record("tbl_potrancation", $info,"potrancation_id=".$POST['eid'] , $dbcon);
			
			if(!empty($POST['purchase_id'])){
				$uid=general_book_tax_entry($dbcon,$POST['purchase_id']);
			}

			if($updateid)
				$row['res']="1";
			else
				$row['res']="0";
			echo json_encode($row);
		}
		/* else if(strtolower($POST['mode'])== "load_purchase_order")
		{
			echo get_purchase_order($dbcon,$POST['vender_id']);
		} */
		else if(strtolower($POST['mode'])== "load_purhcase_order_data")
		{
			$q = $dbcon -> query("SELECT * from tbl_purchaseorder where purchaseorder_id=".$POST['purchaseorder_id']);
			$rel = $q->fetch_assoc();
			
			$resp['purchaseorder_no']	= $rel['purchaseorder_no'];
			$resp['purchaseorder_date'] = date("d-m-Y",strtotime($rel['purchaseorder_date']));
			$resp['pro_html'] 			= get_purchase_order_data($dbcon,$POST['purchaseorder_id']);
			echo json_encode($resp);
		}
		else if(strtolower($POST['mode'])== "load_purhcase_pro")
		{
			$resp['pro_html'] 			=getproduct($dbcon,0,'0,1,3');
			echo json_encode($resp);
		}
		else if(strtolower($POST['mode'])== "loadpurchase_productdata")
		{
			$q = $dbcon -> query("SELECT * from tbl_purchaseordertrn where purchaseorder_id=".$POST['purchaseorder_id']." and purchaseordertrn_status=0 and product_id=".$POST['product_id']." ");
			$resp = $q->fetch_assoc();
			
			echo json_encode($resp);
		}
		else if(strtolower($POST['mode'])== "last_rate")
		{
			$query="select product_rate,potrancation_id,potrancation_status,product_id from tbl_potrancation as trn left join tbl_pono as mst on mst.po_id=trn.po_id where product_id=".$POST["product_id"]." and potrancation_status=0 order by potrancation_id DESC";
			$prel=mysqli_fetch_assoc($dbcon->query($query));
			echo $prel['product_rate'];
		}
		else if(strtolower($POST['mode'])== "load_rate_hist")
		{
			$resp='';
			$query="select inv.*,ven.vender_name,pro.product_name,trn.product_rate from tbl_pono as inv
					inner join tbl_potrancation as trn on inv.po_id=trn.po_id 
					inner join tbl_vender as ven on ven.vender_id=inv.vender_id
					inner join tbl_product as pro on pro.product_id=trn.product_id
					where inv.status=0 and trn.potrancation_status=0 and inv.vender_id=".$POST["vender_id"]." and trn.product_id=".$POST["product_id"]." order by trn.potrancation_id DESC LIMIT 10";
			$rs_prel=$dbcon->query($query);
			$rs_prel_num_rows=mysqli_num_rows($rs_prel);
			if($rs_prel_num_rows>0){
				while($prel=mysqli_fetch_assoc($rs_prel)){
					$resp.='<tr>
								<td class="text-center">'.$prel['po_no'].'</td>
								<td class="text-center">'.date('d-m-y',strtotime($prel['po_date'])).'</td>
								<td class="text-center">'.$prel['product_rate'].'</td>
							</tr>';
					$row['cust_name']=$prel['vender_name'];
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
				$invoice_count1="Select SUM(g_total) as itotal from tbl_pono as po 
			where  po_date>='".date('Y-m-d',strtotime($s_date['0']))."' AND po_date<='".date('Y-m-d',strtotime($s_date['1']))."' AND po.status=0 and company_id=".$_SESSION['company_id'];
		$count_invoice1=mysqli_fetch_assoc($dbcon->query($invoice_count1));
			
			 $invoice_count="Select SUM(total) as itotal,SUM(product_amount) as taxable_amt,SUM(product_amount) as taxable_amt from tbl_pono as po 
			left join tbl_potrancation as potrn on potrn.po_id=po.po_id 
			where  po_date>='".date('Y-m-d',strtotime($s_date[0]))."' AND po_date<='".date('Y-m-d',strtotime($s_date['1']))."' AND po.status=0 and potrn.potrancation_status=0 and company_id=".$_SESSION['company_id'];
					$count_invoice=mysqli_fetch_assoc($dbcon->query($invoice_count));
					
					$invoice_paid="Select SUM(total_paid_amount) as ipaid_amount from tbl_receipt where  receipt_date>='".date('Y-m-d',strtotime($s_date['0']))."' AND receipt_date<='".date('Y-m-d',strtotime($s_date['1']))."' AND status=0 and payment_type=2 and company_id=".$_SESSION['company_id'];
					$count_paid=mysqli_fetch_assoc($dbcon->query($invoice_paid));
					$count['g_total']= intval($count_invoice1['itotal']);
					$count['taxable_amt']= intval($count_invoice['taxable_amt']);
					$count['total_paid_amount']=intval($count_paid['ipaid_amount']);
					echo json_encode($count);
					
		}
		else if(strtolower($POST['mode'])== "use_dr"){
			//Delete Old paid Amount from Purchase Table
			$inv_upd = $dbcon->query("UPDATE tbl_pono INNER JOIN tbl_used_debit ON tbl_pono.po_id = tbl_used_debit.po_id SET paid_amount = paid_amount - ( SELECT SUM( inr_dr.used_debit_amt ) 
			FROM tbl_used_debit AS inr_dr WHERE inr_dr.po_id =".$POST['po_id']." ) 
			WHERE tbl_used_debit.po_id =".$POST['po_id']);
			
			foreach($POST['used_debit_amt'] as $key => $name)
			{
				if(floatval($POST['debitnote_id'][$key])){
					//Delete Old paid Amount from Debit Note Table
					$cr_upd = $dbcon->query("UPDATE tbl_debitnote 
					inner join tbl_used_debit on tbl_debitnote.debitnote_id=tbl_used_debit.debitnote_id set paid_amount = paid_amount - used_debit_amt
					where tbl_debitnote.debitnote_id=".$POST['debitnote_id'][$key]." and tbl_used_debit.po_id=".$POST['po_id']);
				}
			}
			$del_id=delete_record('tbl_used_debit',"po_id=".$POST['po_id'], $dbcon);	
			
			foreach($POST['used_debit_amt'] as $key => $name)
			{
				if(floatval($POST['used_debit_amt'][$key])){
					//Entry in Used Debit Table
					$info1['po_id']				= $POST['po_id'];
					$info1['debitnote_id']		= $POST['debitnote_id'][$key];
					$info1['used_debit_amt']	= $POST['used_debit_amt'][$key];
					$info1['user_id']			= $_SESSION['user_id'];
					$info1['company_id']		= $_SESSION['company_id'];
					$info1['cdate']				= date("Y-m-d H:i:s");
					$insertrnid=add_record('tbl_used_debit', $info1, $dbcon);
					
					//Update In Credit Note Table
					$cr_upd = $dbcon->query("UPDATE tbl_debitnote SET paid_amount = paid_amount + ".$POST['used_debit_amt'][$key]." WHERE debitnote_id = ".$POST['debitnote_id'][$key]);
				}
			}
			
			//Update In Invoice Table
			$inv_upd = $dbcon->query("UPDATE tbl_pono SET paid_amount = paid_amount + ".$POST['total_dr']." WHERE po_id = ".$POST['po_id']);
			
			if($inv_upd){
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

function general_book_tax_entry($dbcon,$invoice_id){
	$qry1="select group_concat(potrancation_id) as tid from tbl_potrancation as cert where potrancation_status=0 and po_id=".$invoice_id;
	$ro=$dbcon->query($qry1);
	$re=mysqli_fetch_assoc($ro);
	
	$qry2="select * from tbl_pono as cert where po_id=".$invoice_id;
	$ro2=$dbcon->query($qry2);
	$re12=mysqli_fetch_assoc($ro2);
	
	$qry="SELECT utax.*,sum(tax_amount) as tamount FROM `tbl_used_tax` as utax WHERE tax_used_status=0 and used_transaction_id in (".$re["tid"].") and table_name='tbl_potrancation' group by ledger_id order by tax_used_id desc";
	$row=$dbcon->query($qry);
	while($tax=mysqli_fetch_assoc($row))
	{
		$qry12="select general_book_id from tbl_general_book as cert where genral_book_status=0 and ledger_id=".$tax['ledger_id']." and table_id=".$invoice_id." and table_name='tbl_purchase'";
			$ros=$dbcon->query($qry12);
			$re2=mysqli_fetch_assoc($ros);
		
	
		$info1['table_name']	= "tbl_purchase";
		$info1['table_id']		= $invoice_id;
		$info1['ref_date']		= date('Y-m-d',strtotime($re12['po_date']));
		$info1['entry_type']	= 2;
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