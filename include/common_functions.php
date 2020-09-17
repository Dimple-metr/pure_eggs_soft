<?php
function load_invoice_no($dbcon,$type_id){
	//Load no by Type ID
	$row=array();
	$query1="select * from tbl_invoicetype where status=0 and type_id=2 and company_id=".$_SESSION['company_id'];
	$rows=mysqli_fetch_assoc($dbcon->query($query1));
	$id=$rows['taxinvoice_start'];
	$id=$id+1;
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
	return $row['invoiceno'];
}
function load_led_no($dbcon,$type,$ref_id){
	if($type=="tbl_payment"){
		$qry1="select * from tbl_receipt as cert 
			where receipt_id=".$ref_id;
		$ro=$dbcon->query($qry1);
		$re=mysqli_fetch_assoc($ro);
		$ret=$re['receipt_no'];
	}else if($type=="tbl_journal_trn"){
		$qry1="select * from tbl_journal_trn as cert 
			left join tbl_journal as jou on jou.journal_id=cert.journal_id
			where journal_trn_id=".$ref_id;
		$ro=$dbcon->query($qry1);
		$re=mysqli_fetch_assoc($ro);
		$ret=$re['journal_no'];
	}else if($type=="tbl_invoice"){
		$qry1="select * from tbl_invoice as cert 
			where invoice_id=".$ref_id;
		$ro=$dbcon->query($qry1);
		$re=mysqli_fetch_assoc($ro);
		$ret=$re['invoice_no'];
	}
	else if($type=="tbl_purchase"){
		$qry1="select * from tbl_pono as cert 
			where po_id=".$ref_id;
		$ro=$dbcon->query($qry1);
		$re=mysqli_fetch_assoc($ro);
		$ret=$re['po_no'];
	}
	else if($type=="tbl_contra_trn"){
		$qry1="select * from tbl_contra_trn as cert 
			left join tbl_contra as jou on jou.contra_id=cert.contra_id
			where contra_trn_id=".$ref_id;
		$ro=$dbcon->query($qry1);
		$re=mysqli_fetch_assoc($ro);
		$ret=$re['contra_no'];
	}
	return $ret;
}
function today_stock_value($dbcon,$stock_date,$product_id,$employee_id,$type){
	
			$query="select stock_out_trn_id,sout.stock_out_id,product.product_name,product.product_mst_rate as rate,cat.unit_name,mst.*,(select IFNULL(sum(transfer_qty),0) from tbl_stock_transfer_trn as ptrn
				left join tbl_stock_transfer as strn on strn.stock_transfer_id=ptrn.stock_transfer_id
				 where ptrn.product_id=mst.product_id and stock_transfer_trn_status=0 and ptrn.user_id=".$employee_id." and strn.stock_transfer_date='".date('Y-m-d',strtotime($stock_date))."') as transfer_out,(select IFNULL(sum(transfer_qty),0) from tbl_stock_transfer_trn as ptrn
				 left join tbl_stock_transfer as strn on strn.stock_transfer_id=ptrn.stock_transfer_id
				 where ptrn.product_id=mst.product_id and stock_transfer_trn_status=0 and strn.employee_id=".$employee_id." and strn.stock_transfer_date='".date('Y-m-d',strtotime($stock_date))."') as transfer_in,(select IFNULL(sum(return_qty),0) from tbl_stock_in_trn as ptrn
				 left join tbl_stock_in as strn on strn.stock_in_id=ptrn.stock_in_id
				 where ptrn.product_id=mst.product_id and stock_in_trn_status=0 and strn.employee_id=".$employee_id." and strn.stock_in_date='".date('Y-m-d',strtotime($stock_date))."') as return_in
				from  tbl_stock_out_trn as mst 
				left join tbl_stock_out as sout on sout.stock_out_id=mst.stock_out_id
				left join unit_mst as cat on cat.unitid=mst.unit_id 
				left join tbl_product as product on product.product_id=mst.product_id  
				where stock_out_trn_status=0 and sout.status=0 and product.product_id=".$product_id." and stock_out_date='".date('Y-m-d',strtotime($stock_date))."' and employee_id=".$employee_id;
			$ro=$dbcon->query($query);
			$re=mysqli_fetch_assoc($ro);
		if($type==1){
			$ret_qty=$re['product_qty'];
		//	$ret_qty=$query;
		}else{
			$salesqty=load_sales_qty($dbcon,$stock_date,$product_id,$employee_id,2);
			$replace_qty=load_sales_qty($dbcon,$stock_date,$product_id,$employee_id,7);
			$ret_qty=($re['product_qty']+$re['transfer_in'])-($re['transfer_out']+$re['return_in']+$salesqty+$replace_qty);
			
			
		}
		return $ret_qty;
}
function load_vehicle_no($dbcon){
	$qry1="select * from users as cert 
			where user_id=".$_SESSION['user_id'];
	$ro=$dbcon->query($qry1);
	$re=mysqli_fetch_assoc($ro);
return $re['vehicle_no'];
}
function load_sales_qty($dbcon,$stock_date,$product_id,$user_id,$type_id){
	
	$qry1="select sum(product_qty) as salesqty from tbl_invoice as cert 
			left join tbl_invoicetrn as trn on trn.invoice_id=cert.invoice_id
			where trancation_status=0 and invoice_status=0 and invoice_date='".date('Y-m-d',strtotime($stock_date))."' and cert.type_id=".$type_id." and product_id=".$product_id." and cert.user_id=".$user_id;
	$ro=$dbcon->query($qry1);
	$re=mysqli_fetch_assoc($ro);
return $re['salesqty'];
}

function get_general_book_id($dbcon,$table_name,$table_id,$ledger_id){
	//and ledger_id=".$ledger_id."
	$qry1="select general_book_id from tbl_general_book as cert where genral_book_status=0 and table_id=".$table_id." and table_name='".$table_name."'" ;
	$ro=$dbcon->query($qry1);
	$re=mysqli_fetch_assoc($ro);
	
	return $re['general_book_id'];
}
function get_chequeno($acc_id,$dbcon)
{
	$query="SELECT * from tbl_ledger where l_id=".$acc_id;
	$rel=mysqli_fetch_assoc($dbcon->query($query));	
	return $rel['acc_chequeno'];
	//return $query;
}
function get_ledger_bank($dbcon,$ledger_id){
	$str='';
	if($_SESSION["user_type"]==4){
		$vat=" and l_form in ('cash')";
	}else{
		$vat=" and l_form in ('bank_form','cash')";
		$str .= '<option value="">--Select Payment Mode--</option>';
	}
	$query="select * from tbl_ledger as pro where l_status=0 ".$vat." and company_id = $_SESSION[company_id] order by TRIM(l_name) ASC";
	$rs_dispatch=$dbcon->query($query);	
	
	while($rel=mysqli_fetch_assoc($rs_dispatch))
	{	
		$sel=''; 
		if($rel['l_id']==$ledger_id)
		{$sel ="selected='selected'";}
		$str .= '<option '.$sel.' value="'.$rel['l_id'].'">'.$rel['l_name'].'</option>';
	}
	return $str;
}
function get_ledger_cash($dbcon,$ledger_id,$report_mode){
	$str='';
	
	$query="select * from tbl_ledger as pro where l_status=0 and l_form ='cash' and company_id = $_SESSION[company_id] order by TRIM(l_name) ASC";
	$rs_dispatch=$dbcon->query($query);	
	if($report_mode!="1"){
		$str .= '<option value="">--Select Payment Mode--</option>';
	}
	while($rel=mysqli_fetch_assoc($rs_dispatch))
	{	
		$sel=''; 
		if($rel['l_id']==$ledger_id)
		{$sel ="selected='selected'";}
		$str .= '<option '.$sel.' value="'.$rel['l_id'].'">'.$rel['l_name'].'</option>';
	}
	return $str;
}

function get_ledger_cust($dbcon,$ledger_id){
	$str='';
	
	$query="select * from tbl_ledger as pro where l_status=0 and l_form='customer_form' and company_id = $_SESSION[company_id] order by TRIM(l_name) ASC";
	$rs_dispatch=$dbcon->query($query);	
	$str .= '<option value="">--select ledger--</option>';
	while($rel=mysqli_fetch_assoc($rs_dispatch))
	{	
		$sel=''; 
		if($rel['l_id']==$ledger_id)
		{$sel ="selected='selected'";}
		$str .= '<option '.$sel.' value="'.$rel['l_id'].'">'.$rel['l_name'].'</option>';
	}
	return $str;
}

function get_ledger($dbcon,$ledger_id,$where){
	$str='';
	
	$query="select * from tbl_ledger as pro where l_status=0 ".$where." and company_id = $_SESSION[company_id] order by TRIM(l_name) ASC";
	$rs_dispatch=$dbcon->query($query);	
	$str .= '<option value="">--select ledger--</option>';
	while($rel=mysqli_fetch_assoc($rs_dispatch))
	{	
		$sel=''; 
		if($rel['l_id']==$ledger_id)
		{$sel ="selected='selected'";}
		
		$str .= '<option '.$sel.' value="'.$rel['l_id'].'">'.$rel['l_name'].'</option>';
	}
	return $str;
}
function get_opening_balance($acc_id,$dbcon,$acc_type)
{
    $query="SELECT opn_balance,
	(select sum(tran_amount) from tbl_banktransaction where debit_accid=".$acc_id." and status=0) as debit,
        (SELECT sum(amount)  FROM `tbl_passbookentry` where acc_id=".$acc_id." and status=0 and typeid=1) as pdebit,
        (select sum(tran_amount) from tbl_banktransaction where credit_accid=".$acc_id."  and status=0) as credit,
        (SELECT sum(amount)  FROM `tbl_passbookentry` where acc_id=".$acc_id." and status=0 and typeid=2) as pcredit
	FROM `account_mst` 
        where acc_id=".$acc_id." 
            and acc_status=0 
            and company_id=".$_SESSION['company_id']." 
            and acc_type=".$acc_type;
    
    echo $query;
    
    $rel = mysqli_fetch_assoc($dbcon->query($query));	
    $opn_balance = $rel['opn_balance'] + $rel['credit'] + $rel['pcredit']-($rel['debit'] + $rel['pdebit']);
    return $opn_balance;
}
function getpaymentmode($dbcon)
{	
	$str='';
	$query="select * from tbl_payment_mode where status=0";
	$rs_payment=$dbcon->query($query);	
	echo '<option value="">Choose Mode</option>';
	while($rel=mysqli_fetch_assoc($rs_payment))
	{
			$str .= '<option value="'.$rel['paymentmodeid'].'">'.$rel['payment_mode'].'</option>';
	}
	return $str;
}
function get_user($dbcon,$user_id){
	
	$str='';
	
	$query="select * from users as pro where active=0 and user_type!=1 and user_id!=".$_SESSION['user_id']." and company_id = $_SESSION[company_id] order by TRIM(user_name) ASC";
	$rs_dispatch=$dbcon->query($query);	
	$str .= '<option value="">--select Employee Name--</option>';
	while($rel=mysqli_fetch_assoc($rs_dispatch))
	{	
		$sel=''; 
		if($rel['user_id']==$user_id)
		{$sel ="selected='selected'";}
		$str .= '<option '.$sel.' value="'.$rel['user_id'].'">'.$rel['user_name'].'</option>';
	}
	return $str;
}
function get_user_in($dbcon,$user_id){
	
	$str='';
	
	$query="select * from users as pro where active=0 and user_type!=1 and company_id = $_SESSION[company_id] order by TRIM(user_name) ASC";
	$rs_dispatch=$dbcon->query($query);	
	$str .= '<option value="">--select Employee Name--</option>';
	while($rel=mysqli_fetch_assoc($rs_dispatch))
	{	
		$sel=''; 
		if($rel['user_id']==$user_id)
		{$sel ="selected='selected'";}
		$str .= '<option '.$sel.' value="'.$rel['user_id'].'">'.$rel['user_name'].'</option>';
	}
	return $str;
}
function show_tax($dbcon,$used_transaction_id,$table_name,$table_id){
	
	//SELECT Concat(`table_name`,'-' ,`tax_amount`) AS column3 FROM tbl_used_tax
	
	$query="select GROUP_CONCAT(Concat((select tax_name from tbl_tax as tx where tx.tax_id=pro.tax_id),' - ' ,`tax_amount`) SEPARATOR '<br/>') AS column3 from tbl_used_tax as pro
		where tax_used_status=0 and used_transaction_id=".$used_transaction_id." and table_name='".$table_name."' and table_id='".$table_id."' order by tax_amount";
	$rs_dispatch=$dbcon->query($query);
	$rel=mysqli_fetch_assoc($rs_dispatch);
	return $rel['column3'];
	//return $query;
	//return "123";
	
}
function get_tax_per1($dbcon,$formulaid){
        $query="select tax_per_name from formula_mst as pro
		left join tbl_tax_per as tper on tper.tax_per_id=pro.tax_per_id
		where formula_status=0 and formulaid=".$formulaid." order by formulaid";
	$rs_dispatch=$dbcon->query($query);
	$rel=mysqli_fetch_assoc($rs_dispatch);
	return $rel['tax_per_name'];
	//return $query;
	//return "123";
	
}
function texpermst($dbcon,$id,$type_id)
{	
	$str='';
	
	$query="select * from tbl_tax_per as pro where tax_per_status=0 and company_id in (0,$_SESSION[company_id]) order by tax_per_name";
	$rs_dispatch=$dbcon->query($query);	
	$str .= '<option value="">--select Tax Percentage--</option>';
	while($rel=mysqli_fetch_assoc($rs_dispatch))
	{	
				$sel=''; 
				if($rel['tax_per_id']==$id)
				{$sel ="selected='selected'";}
				$str .= '<option '.$sel.' value="'.$rel['tax_per_id'].'">'.$rel['tax_per_name'].'</option>';
	}
	return $str;
}
function get_tax($dbcon,$id,$type_id)
{	
	$str='';
	
	$query="select * from tbl_tax as pro where tax_status=0 and company_id in (0,$_SESSION[company_id]) order by tax_value";
	$rs_dispatch=$dbcon->query($query);	
	$str .= '<option value="">--select Tax--</option>';
	while($rel=mysqli_fetch_assoc($rs_dispatch))
	{	
				$sel=''; 
				if($rel['tax_id']==$id)
				{$sel ="selected='selected'";}
				$str .= '<option '.$sel.' value="'.$rel['tax_id'].'">'.$rel['tax_name'].'</option>';
	}
	return $str;
}

function getproduct($dbcon,$id,$type_id)
{	
	$str='';
	
	$query="select * from tbl_product as pro where product_status=0 and product_type in($type_id) and company_id in (0,$_SESSION[company_id]) order by product_name";
	$rs_dispatch=$dbcon->query($query);	
	$str .= '<option value="">Choose Product</option>';
	while($rel=mysqli_fetch_assoc($rs_dispatch))
	{	
				$sel=''; 
				if($rel['product_id']==$id)
				{$sel ="selected='selected'";}
				$str .= '<option '.$sel.' value="'.$rel['product_id'].'">'.$rel['product_name'].' - '.$rel['item_code'].'</option>';
	}
	return $str;
}
function get_custmer_consignee($dbcon,$parentid,$id)
{	
	$str='';
	
	$query="select * from tbl_custmer_consignee where cust_status=0 and cust_ref_id=".$parentid." and company_id in (0,$_SESSION[company_id])";
	$rs_dispatch=$dbcon->query($query);	
	$str = '<option value="">Choose Consignee</option>';
	while($rel=mysqli_fetch_assoc($rs_dispatch))
	{	
		$sel=''; 
		if($rel['cust_id']==$id)
		{$sel ="selected='selected'";}
		$str .= '<option '.$sel.' value="'.$rel['cust_id'].'">'.$rel['company_name'].'</option>';
	}
	return $str;
}
function getmodeofdispache($dbcon,$eid){
	 $query="select * from mode_of_dispatch where mode_des_status=0";
	$rs_dispatch=$dbcon->query($query);	
	echo '<option value="">Choose Mode Of Dispatch</option>';
	while($rel=mysqli_fetch_assoc($rs_dispatch))
	{
		$sel='';
		if($rel['mode_dis_id']==$eid)
		{
			$sel='selected="selected"';
		}
			echo '<option '.$sel.' value="'.$rel['mode_dis_id'].'">'.$rel['mode_dispatch'].'</option>';
	}
}

function getpaymentterms($dbcon,$eid){
	 $query="select * from pay_terms where terms_status=0";
	$rs_dispatch=$dbcon->query($query);	
	echo '<option value="">Choose Payment Terms</option>';
	while($rel=mysqli_fetch_assoc($rs_dispatch))
	{
		$sel='';
		if($rel['terms_id']==$eid)
		{
			$sel='selected="selected"';
		}
			echo '<option '.$sel.' value="'.$rel['terms_id'].'">'.$rel['payment_days'].' days'.'</option>';
	}
}

function getinvoicetype($dbcon,$id)
{
	$query="select * from tbl_invoicetype where status=0 and type_id in (2,7) and company_id=".$_SESSION['company_id'];
	$rs_dispatch=$dbcon->query($query);	
	echo '<option value="" selected="selected">Choose Invoice Type</option>';
	while($rel=mysqli_fetch_assoc($rs_dispatch))
	{
		$sel='';
		if($rel['invoicetype_id']==$id)
		{
			$sel='selected="selected"';
		}
		echo '<option '.$sel.' value="'.$rel['invoicetype_id'].'">'.$rel['invoice_type'].'</option>';
	}
}
function get_product_specification($dbcon,$id) {
	$str='';
	$query="select * from mst_material_spec where ms_status='0' order by ms_name";
	$rs_product=$dbcon->query($query);
	$str = '<option value="">Choose Material Specification</option>';
	while($rel=mysqli_fetch_assoc($rs_product))
	{
		$sel='';
		if($rel['ms_id']==$id)
		{ $sel ="selected='selected'"; }
		$str .= '<option '.$sel.' value="'.$rel['ms_id'].'">'.$rel['ms_name'].'</option>';
	}
	return $str;
}

function get_branch($dbcon,$eid){
	$query="select branch_id,branch_name from branch_mst where branch_status=0";
	$rs_dispatch=$dbcon->query($query);	
	$str='<option value="">Choose Branch</option>';
	while($rel=mysqli_fetch_assoc($rs_dispatch))
	{
		$sel='';
		if($rel['branch_id']==$eid){
			$sel='selected="selected"';
		}
		$str.='<option '.$sel.' value="'.$rel['branch_id'].'">'.$rel['branch_name'].'</option>';
	}
	return $str;
}
function get_all_category($dbcon,$id,$where='')
{
	$str='';
	$query="Select * from tbl_category where cat_status=0 ".$where;
	$rs_type=$dbcon->query($query);
    if($id=='0'){ $psel='selected="selected"';}
	$str ='<option value="" >--Choose Category--</option>';
	$str.='<option value="0" '.$psel.' >PRIMARY</option>';
	while($row=mysqli_fetch_assoc($rs_type))
	{	
		$sel='';
		if($row['cat_id']==$id)
		{$sel='selected="selected"';}
		
		$str .= '<option '.$sel.' value="'.$row['cat_id'].'">'.$row['cat_name'].'</option>';
	}
	return $str;
}

function get_tax_percentage($dbcon,$id)
{
	$str='';
	$query="Select * from tbl_tax_per_master where tp_status=0 ";
	$rs_type=$dbcon->query($query);
  
	$str ='<option value="" >--Choose Tax--</option>';
	while($row=mysqli_fetch_assoc($rs_type))
	{	
		$sel='';
		if($row['tp_id']==$id)
		{$sel='selected="selected"';}
		
		$str .= '<option '.$sel.' value="'.$row['tp_id'].'">'.$row['tp_per'].'</option>';
	}
	return $str;
}

function get_product($dbcon,$id,$type) {
	$str='';
	$query="select p.product_id,p.product_name,c.cat_name from product_mst as p left join tbl_category as c on c.cat_id=p.product_category where p.product_status=0 and p.company_id in(0,$_SESSION[company_id]) and p.product_type in($type) ";
	$rs_product=$dbcon->query($query);
	$str = '<option value="">Choose Product</option>';
	while($rel=mysqli_fetch_assoc($rs_product))
	{
		$sel='';
		if($rel['product_id']==$id)
		{ $sel ="selected='selected'"; }
		$str .= '<option '.$sel.' value="'.$rel['product_id'].'">'.$rel['product_name']."-- ( ".$rel['cat_name'].')'.'</option>';
	}
	return $str;
}
function members_Tree($dbcon,$parentKey)
{
 
  $sql = 'SELECT g_id, g_name from tbl_group WHERE g_pid="'.$parentKey.'" order by g_name';

  $result = $dbcon->query($sql);

  while($value = mysqli_fetch_assoc($result)){
	 $id = $value['g_id'];
	 $row1[$id]['id'] = $value['g_id'];
	 $row1[$id]['name'] = $value['g_name'];
	 $row1[$id]['text'] = $value['g_name'];
	 $row1[$id]['nodes'] = array_values(members_Tree($dbcon,$value['g_id']));
  }

  return $row1;
}
function get_branch_from_zone($dbcon,$zone,$id,$sindex) {
	$str='';
	$query="select * from branch_mst where branch_status='0' and zoneid='$zone' order by branch_name ";
	$rs_product=$dbcon->query($query);
	
	$str.= '<option value="">Choose Branch</option>';
	while($rel=mysqli_fetch_assoc($rs_product))
	{
		$sel='';
		if($rel['branch_id_customer']==$id)
		{ $sel ="selected='selected'"; }
		$str .= '<option '.$sel.' value="'.$rel['branch_id'].'">'.$rel['branch_name'].'</option>';
	}
	return $str;
}
function getbank($dbcon,$bankid,$con)
{
	$bank='';
	$qry="select * from bank_mst where bank_status=0".$con;
	$rs_type=$dbcon->query($qry);	
	$bank .='<option value="" selected="selected">Choose Bank</option>';
	while($row=mysqli_fetch_assoc($rs_type))
	{	
		$sel='';
		if($row['bankid']==$bankid)
		{$sel='selected="selected"';}
		$bank .= '<option '.$sel.' value="'.$row['bankid'].'">'.$row['bank_name'].'</option>';
	}
	return $bank;
}
function get_all_bank($dbcon,$id)
{
	$q="select * from bank_mst where bank_status='0' order by bank_name";
	$r=$dbcon->query($q);
	
	$str="";
	$str.= '<option value="">Choose Bank</option>';
	while($rel=mysqli_fetch_assoc($r))
	{
		$sel=''; 
		if($rel['bank_id']==$id)
		{$sel ="selected='selected'";}
		$str.= '<option  value="'.$rel['bankid'].'">'.$rel['bank_name'].'</option>';
	}
	return $str;
	
}

function get_grp_by_id($dbcon,$id)
{
	$query="select * from tbl_group where g_id='$id'";
	$row=$dbcon->query($query);
	$rel=mysqli_fetch_array($row);
	return $rel['g_name'];
} 
function get_all_group_old($dbcon,$id,$where='',$primary)
{
	$str='';
	$query="Select * from group_mst where group_status=0 ".$where;
	$rs_type=$dbcon->query($query);
    if($id=='0'){ $psel='selected="selected"';}
	$str ='<option value="" >--Choose Group--</option>';
	if($primary!='0')
	{
	$str.='<option value="0" '.$psel.' >PRIMARY</option>';
	}
	while($row=mysqli_fetch_assoc($rs_type))
	{	
		$sel='';
		if($row['group_id']==$id)
		{$sel='selected="selected"';}
		
		$str .= '<option '.$sel.' value="'.$row['group_id'].'">'.$row['group_name'].'</option>';
	}
	return $str;
}
function get_all_group($dbcon,$id,$where='',$primary)
{
	$str='';
	$query="Select * from tbl_group where g_status=0 ".$where;
	$rs_type=$dbcon->query($query);
    if($id=='0'){ $psel='selected="selected"';}
	$str ='<option value="" >--Choose Group--</option>';
	if($primary!='0')
	{
		$str.='<option value="0" '.$psel.' >PRIMARY</option>';
	}
	while($row=mysqli_fetch_assoc($rs_type))
	{	
		$sel='';
		if($row['g_id']==$id)
		{$sel='selected="selected"';}
		
		$str .= '<option '.$sel.' value="'.$row['g_id'].'">'.$row['g_name'].'</option>';
	}
	return $str;
}

function get_zone($dbcon,$id) {
	$str='';
	$query="select `zone_id`,`zone_name` from tbl_zone_list where zone_status=0 and company_id in(0,$_SESSION[company_id])";
	$rs_product=$dbcon->query($query);
	$str = '<option value="">Choose Zone</option>';
	while($rel=mysqli_fetch_assoc($rs_product))
	{
		$sel='';
		if($rel['zone_id']==$id)
		{ $sel ="selected='selected'"; }
		$str .= '<option '.$sel.' value="'.$rel['zone_id'].'">'.$rel['zone_name'].'</option>';
	}
	return $str;
}

function getcat($dbcon,$id)
{
	$formula_qry="select category_name,category_id from  category_mst where category_status=0 and company_id=".$_SESSION['company_id'];
	$rs_formula=$dbcon->query($formula_qry);	
	echo '<option value="">Choose Category</option>';
	while($formula=mysqli_fetch_assoc($rs_formula))
	{	
		$sel='';
		if($formula['category_id']==$id)
		{
			$sel="selected='selected'";
		}
		echo '<option '.$sel.' value="'.$formula['category_id'].'">'.$formula['category_name'].'</option>';
	}

}
function getformula($dbcon,$id)
{
	$formula_qry="select * from  formula_mst where formula_status=0 and company_id=".$_SESSION['company_id'];
	$rs_formula=$dbcon->query($formula_qry);	
	echo '<option value="">Choose Formula</option>';
	while($formula=mysqli_fetch_assoc($rs_formula))
	{	
		$sel='';
		if($formula['formulaid']==$id)
		{
			$sel="selected='selected'";
		}
		echo '<option '.$sel.' value="'.$formula['formulaid'].'">'.$formula['formula_name'].'</option>';
	}

}
function getbalance_type($dbcon,$id)
{
	$query="select * from mst_balance_type where status=0";
	$rs_cust=$dbcon->query($query);	
	echo '<option value="">Select Type</option>';
	while($rel=mysqli_fetch_assoc($rs_cust))
	{	
		$sel='';
		if($rel['balance_typeid']==$id)
		{
			$sel="selected='selected'";
		}
		echo '<option '.$sel.' value="'.$rel['balance_typeid'].'">'.$rel['balance_type_name'].'</option>';
	}

	
}
 function get_group($dbcon,$id)
{
	$str='';
	$query="select `group_id`,`group_name` from group_mst where group_status=0 order by group_name";
	$rs_country=$dbcon->query($query);
	$str = '<option value="">Select Group</option>';
	//if(0==$id)
	//{ $sel ="selected='selected'"; }
	
	while($rel=mysqli_fetch_assoc($rs_country))
	{
		$sel='';
		if($rel['group_id']==$id)
		{ $sel ="selected='selected'"; }
		$str .= '<option '.$sel.' value="'.$rel['group_id'].'">'.$rel['group_name'].'</option>';
	}
	return $str;
}
function get_under_category($dbcon,$id)
{
	$str='';
	$query="select `category_id`,`category_name` from category_mst where category_status=0 order by category_name";
	$rs_country=$dbcon->query($query);
	$str = '<option value="">Select Category</option>';
	//if(0==$id)
	//{ $sel ="selected='selected'"; }
	$str .= '<option '.$sel.' value="0">Primary Category</option>';
	while($rel=mysqli_fetch_assoc($rs_country))
	{
		$sel='';
		if($rel['category_id']==$id)
		{ $sel ="selected='selected'"; }
		$str .= '<option '.$sel.' value="'.$rel['category_id'].'">'.$rel['category_name'].'</option>';
	}
	return $str;
}
 function get_under_group($dbcon,$id)
{
	$str='';
	$query="select `group_id`,`group_name` from group_mst where group_status=0 order by group_name";
	$rs_country=$dbcon->query($query);
	$str = '<option value="">Select Group</option>';
	//if(0==$id)
	//{ $sel ="selected='selected'"; }
	$str .= '<option '.$sel.' value="0">Primary Groups</option>';
	while($rel=mysqli_fetch_assoc($rs_country))
	{
		$sel='';
		if($rel['group_id']==$id)
		{ $sel ="selected='selected'"; }
		$str .= '<option '.$sel.' value="'.$rel['group_id'].'">'.$rel['group_name'].'</option>';
	}
	return $str;
}
function getcust_person($dbcon,$id,$cust_id)
{
	$str='';
	$query="select `cust_contact_person_id`,`cust_contact_person_name` from tbl_cust_contact_person where cust_contact_person_status=0 and cust_id=".$cust_id;
	$rs_product=$dbcon->query($query);
	$str = '<option value="">Choose Person</option>';
	while($rel=mysqli_fetch_assoc($rs_product))
	{
		$sel='';
		if($rel['cust_contact_person_id']==$id)
		{ $sel ="selected='selected'"; }
		$str .= '<option '.$sel.' value="'.$rel['cust_contact_person_id'].'">'.$rel['cust_contact_person_name'].'</option>';
	}
	return $str;
}

function get_company_data($dbcon,$company_id)
{
	$query="select * from tbl_company where company_id=".$company_id;
	$rel=mysqli_fetch_assoc($dbcon->query($query));
	return $rel;
}
function get_cust_data_arr($dbcon,$cust_id)
{
	$query="select * from tbl_customer where cust_id=".$cust_id;
	$rel=mysqli_fetch_assoc($dbcon->query($query));
	return $rel;
}
function getunit($dbcon,$id,$unit_id)
{
	if($unit_id){
		$uid=" and unitid!=".$unit_id;
	}
	$str='';
	$query="select `unitid`,`unit_name` from unit_mst where unit_status=0 ".$uid." order by unit_name";
	$rs_country=$dbcon->query($query);	
	$str = '<option value="">Choose Unit</option>';
	while($rel=mysqli_fetch_assoc($rs_country))
	{
		$sel='';
		if($rel['unitid']==$id)
		{ $sel ="selected='selected'"; }
		$str .= '<option '.$sel.' value="'.$rel['unitid'].'">'.$rel['unit_name'].'</option>';
	}
	return $str;
}	
function get_country($dbcon,$id)
{
	$str='';
	$query="select `countryid`,`country_name` from country_mst where country_status=0 order by country_name";
	$rs_country=$dbcon->query($query);	
	$str = '<option value="">Choose Country</option>';
	while($rel=mysqli_fetch_assoc($rs_country))
	{
		$sel='';
		if($rel['countryid']==$id)
		{ $sel ="selected='selected'"; }
		$str .= '<option '.$sel.' value="'.$rel['countryid'].'">'.$rel['country_name'].'</option>';
	}
	return $str;
}
function addDayswithdate($date,$days){
	$date = strtotime("+".$days." days", strtotime($date));
	return  date("Y-m-d", $date);
}
function getquestion($dbcon,$id,$cond)
{
	$query="select * from tbl_question where status=0 ";
	$rs_cust=$dbcon->query($query);	
	$q= '<option value="">Choose Your Security Question </option>';
	while($rel=mysqli_fetch_assoc($rs_cust))
	{	
		$sel='';
		if($rel['question_id']==$id)
		{
			$sel="selected='selected'";
		}
		$q .='<option '.$sel.' value="'.$rel['question_id'].'">'.$rel['question'].'</option>';
	}
	return $q;
}

function getusertype($dbcon,$sid,$con)
{
	$usertype='';
	$qry="select * from tbl_usertype where status=0 ".$con;
	$rs_type=$dbcon->query($qry);	
	//$usertype .='<option value="" selected="selected">Choose User Type</option>';
	while($row=mysqli_fetch_assoc($rs_type))
	{	
		$sel='';
		if($row['usertype_id']==$sid)
		{$sel='selected="selected"';}
		$usertype .= '<option '.$sel.' value="'.$row['usertype_id'].'">'.$row['usertype_name'].'</option>';
	}
	return $usertype;
}
function getmenu($dbcon,$sid)
{
	$menu='';
	$qry="select * from tbl_menu where status=0 and pid=0";
	$rs_menu=$dbcon->query($qry);	
	$menu .='<option value="" selected="selected">Choose Menu</option>';
	while($row=mysqli_fetch_assoc($rs_menu))
	{	
		$sel='';
		if($row['menu_id']==$sid)
		{$sel='selected="selected"';}
		$menu .= '<option '.$sel.' value="'.$row['menu_id'].'">'.$row['menu_name'].'</option>';
	}
	return $menu;
}
function get_state($dbcon,$sid,$cid)
{
	$qry="select * from state_mst where state_status=0 and countryid=".$cid;
	$rs_state=$dbcon->query($qry);		
	$str='';
	$str.= '<option value="">Choose State</option>';
	while($row=mysqli_fetch_assoc($rs_state))
	{	
		$sel='';
		if($row['stateid']==$sid)
		{ $sel='selected="selected"'; }
		$str.='<option '.$sel.' value="'.$row['stateid'].'">'.$row['state_name'].'</option>';
	}
	return $str;
}
function getstate($dbcon,$sid)
{
	$qry="select * from state_mst where state_status=0";
	$rs_state=$dbcon->query($qry);		
	while($row=mysqli_fetch_assoc($rs_state))
	{	
		$sel='';
		if($row['stateid']==$sid)
		{$sel='selected="selected"';}
		echo '<option '.$sel.' value="'.$row['stateid'].'">'.$row['state_name'].'</option>';
	}
}
function getcity($dbcon,$sid,$cid)
{
	$city='';
	$c_qry="select * from city_mst where city_status=0 and stateid=".$sid.' order by city_name';
	$rs_city=$dbcon->query($c_qry);	
	$city.= '<option value="">Choose City</option>';	
	while($r=mysqli_fetch_assoc($rs_city))
	{	
		$sel='';	
		if($r['cityid']==$cid)
		{$sel='selected="selected"';}
		$city .= '<option '.$sel.' value="'.$r['cityid'].'">'.$r['city_name'].'</option>';
	}						
	return $city;								
}

function getcity_all($dbcon,$cid)
{
	$city='';
	$c_qry="select * from city_mst where city_status=0 order by city_name";
	$rs_city=$dbcon->query($c_qry);	
	$city.= '<option value="">Choose City</option>';	
	while($r=mysqli_fetch_assoc($rs_city))
	{	
		$sel='';	
		if($r['cityid']==$cid)
		{$sel='selected="selected"';}
		$city .= '<option '.$sel.' value="'.$r['cityid'].'">'.$r['city_name'].'</option>';
	}						
	return $city;								
}

function getcust($dbcon,$id)
{   
	$str='';
	$query="select cust.cust_id,cust.company_name,country.country_name from tbl_customer as cust left join country_mst as country on country.countryid=cust.countryid where cust_status=0 and cust.company_id in (0,$_SESSION[company_id])";
	$rs_cust=$dbcon->query($query);	
	$str= '<option value="">Choose Company</option>';
	while($rel=mysqli_fetch_assoc($rs_cust))
	{	
		$sel='';
		if($rel['cust_id']==$id) { $sel="selected='selected'"; }
		$str.= '<option '.$sel.' value="'.$rel['cust_id'].'">'.$rel['company_name'].' - '.$rel['country_name'].'</option>';
	}
	return $str;
} 
function getreportcust($dbcon,$id)
{	
	$query="select * from tbl_customer where cust_status=0 and company_id in (0,$_SESSION[company_id])";
	$rs_cust=$dbcon->query($query);	
	echo '<option value="">All Company</option>';
	while($rel=mysqli_fetch_assoc($rs_cust))
	{	
		$sel='';
		if($rel['cust_id']==$id)
		{
			$sel="selected='selected'";
		}
		echo '<option '.$sel.' value="'.$rel['cust_id'].'">'.$rel['company_name'].'</option>';
	}
	
}
function series_no($dbcon,$type_id){
			$query1="select * from  tbl_invoicetype where status=0 and type_id=".$type_id;
			$rows=mysqli_fetch_assoc($dbcon->query($query1));
			$id=$rows['taxinvoice_start'];
			$id=$id+1;
			if($rows['invoice_format']=='2'){
				$series_no= str_pad($id,4,"0",STR_PAD_LEFT).$rows['format_value'];
			}
			else if($rows['invoice_format']=='1'){
				$series_no=$rows['format_value'].str_pad($id,3,"0",STR_PAD_LEFT);
			}
			else if($rows['invoice_format']=='3'){
				$series_no=$rows['format_value'].str_pad($id,3,"0",STR_PAD_LEFT).$rows['end_format_value'];
			}
			else{
				$series_no=str_pad($id,3,"0",STR_PAD_LEFT);
			}
			return $series_no;
}
function update_series_no($dbcon,$type_id){
	$query_invoicetype = $dbcon->query("UPDATE tbl_invoicetype SET taxinvoice_start = taxinvoice_start +1 WHERE status=0 and type_id=".$type_id);
}
?>