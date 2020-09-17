<?php

function add_record($table, $data, $db)
{
	
	foreach(array_keys($data) as $field_name) 
	{
		$data[$field_name] = sc_mysql_escape($data[$field_name],$db);
		if (!isset($field_string)) {
			$field_string = "".$field_name.""; 
			$value_string = "'$data[$field_name]'";
		} else {
			$field_string .= ",".$field_name."";
			$value_string .= ",'$data[$field_name]'";
		}
	}
	 $dbQuery = "INSERT INTO $table ($field_string) VALUES ($value_string)";
	//echo $dbQuery;	
		
	 $db->query($dbQuery);
	//echo $dbQuery;
	$insert_id=mysqli_insert_id($db);
	if(isset($insert_id))
	{
		$_SESSION['msg']='Record Added Successfully';
	}
	return $insert_id;//return record number of the record just added, in case we need it
}
function update_record($table, $data, $where, $db)
{
	foreach(array_keys($data) as $field_name){
		$data[$field_name] = sc_mysql_escape($data[$field_name],$db);
		if (!isset($field_string)) {
			$field_string = " ".$field_name.""; 
			$value_string = "'$data[$field_name]'";
			$querystring=" set ".$field_string."=".$value_string;
		} else {
			$field_string = ",".$field_name."";
			$value_string = "'$data[$field_name]'";
			$querystring.=$field_string."=".$value_string;
		}
	}
	 $dbQuery = "update ".$table.$querystring." Where ".$where;	
 //echo $dbQuery;	 
	$db->query($dbQuery);
	//echo $dbQuery;exit;	
	$update_id=mysqli_affected_rows($db);
	if(isset($update_id))
	{
		$_SESSION['msg']='Record Updated Successfully';
	}
	return $update_id;//return record number of the record just added, in case we need it
}
function delete_record($table, $where, $db)
{
	$dbQuery = "delete from ".$table." Where ".$where;	
	//echo $dbQuery;exit;
	 $db->query($dbQuery);
	$update_id=mysqli_affected_rows($db);
	return $update_id;	
}
function get_update_maxno($table, $db)
{
	$query='select maxno from tbl_maxno where tbl_name="'.$table.'"';
	$rs=($db->query($query));
	$rs=mysqli_fetch_array($rs);
	$max_id=$rs['maxno']+1;
	$query='update tbl_maxno set maxno='.$max_id.' where tbl_name="'.$table.'"';
	$db->query($query);
	return $max_id;
}
function get_fieldname_id($type, $db)
{
	$query='select id,field_name FROM  `field_master` WHERE TYPE ="'.$type.'"';
	$rs=($db->query($query));
	//$field_arr=array();
	$field_arr=array();
	while($rel=mysqli_fetch_array($rs))
	{
		$field_arr[$rel['id']]=$rel['field_name'];		
	}
	return $field_arr;
}
function  getfieldid_fromname($field_name,$fieldname_arr)
{
	return $key= array_search($field_name,$fieldname_arr);
}

function sc_mysql_escape($value,$db) {
	if (is_string($value));
	// strip out slashes IF they exist AND magic_quotes is on
	if (get_magic_quotes_gpc() && (strstr($value,'\"') || strstr($value,"\\'"))) $value = stripslashes($value);	
	// escape string to make it safe for mysql
	return @mysqli_real_escape_string($db,$value);
}

//Purpose: to call addslashes(), stripping slashes before only if necessary
function sc_php_escape($value) {
	if (is_string($value));
	// strip out slashes IF they exist AND magic_quotes is on
	if (get_magic_quotes_gpc() && (strstr($value,'\"') || strstr($value,"\\'"))) $value = stripslashes($value);	
	// escape string to make it safe for mysql
	return addslashes($value);
}
function updateopamount($poid,$oldpoid,$oldamount,$newanount,$dbcon)
{
		if($poid==$oldpoid)
		{
		$query_from = $dbcon->query("UPDATE tbl_pono SET bill_amount =(bill_amount - ".$oldamount.")+ ".$newanount." WHERE po_id = ".$poid);
		}
		else if($poid>0)
		{
			$query_from = $dbcon->query("UPDATE tbl_pono SET bill_amount =bill_amount + ".$newanount." WHERE po_id = ".$poid);
		}
		else
		{
		$query_from = $dbcon->query("UPDATE tbl_pono SET bill_amount =(bill_amount - ".$oldamount.") WHERE po_id = ".$oldpoid);		}	
		
		return $query_from;		
}
function add_tax_record($dbcon,$used_transaction_id,$table_name,$table_id,$formula_id,$taxableamount)
{	
		$info_del['tax_used_status']	= 2;
		$updateid1=update_record("tbl_used_tax",$info_del,"table_name='".$table_name."' and table_id='".$table_id."' and used_transaction_id=".$used_transaction_id, $dbcon);
	$str='';
	 $query="select * from formula_mst as pro where formula_status=0 and formulaid=".$formula_id." order by formulaid";
	$rs_dispatch=$dbcon->query($query);
	$rel=mysqli_fetch_assoc($rs_dispatch);
	
	 $que="select * from tbl_tax as ta where tax_status=0 and tax_id in (".$rel['tax_id'].") order by tax_id";
	$rs_di=$dbcon->query($que);
	while($re=mysqli_fetch_assoc($rs_di))
	{	
		if(!empty($re['tax_value'])){
			$tax_amount=($taxableamount)*$re['tax_value']/100;
			$info1['used_transaction_id']		= $used_transaction_id;
			$info1['tax_id']					= $re['tax_id'];
			$info1['table_name']				= $table_name;
			$info1['table_id']					= $table_id;
			$info1['tax_per']					= $re['tax_value'];
			$info1['ledger_id']					= $re['ledger_id'];
			$info1['tax_amount']				= $tax_amount;
			$info1['cdate']						= date("Y-m-d H:i:s");
			$info1['user_id']					= $_SESSION['user_id'];
			$info1['usertype_id']				= $_SESSION['user_type'];
			$info1['company_id']				= $_SESSION['company_id'];
			//var_dump($info1);
			$inserid=add_record("tbl_used_tax",$info1, $dbcon);
			$totaltax_amount+=$tax_amount;
			$totaltax_per+=$re['tax_value'];
		}
		//var_dump($re['tax_value']);		
	}
		
		$info_main['tax_per']		= $totaltax_per;
		$info_main['tax_per_id']	= $rel['tax_per_id'];
		$info_main['tax_amount']	= $totaltax_amount;
		$info_main['total']			= ($taxableamount+$totaltax_amount);
	$updateid1=update_record($table_name,$info_main,$table_id."=".$used_transaction_id, $dbcon);
	//return 12;
	//var_dump("1234");
}
function add_general_book_entry($dbcon,$table_name,$table_id,$entry_type,$ledger_id,$amount,$general_book_id,$ref_date)
{
	$info_gen['table_name']		= $table_name;
	$info_gen['table_id']		= $table_id;
	$info_gen['entry_type']		= $entry_type;
	$info_gen['ref_date']		= date('Y-m-d',strtotime($ref_date));
	$info_gen['ledger_id']		= $ledger_id;
	$info_gen['amount']			= $amount;
	$info_gen['user_id']		= $_SESSION['user_id'];
	$info_gen['cdate']			= date("Y-m-d H:i:s");
	$info_gen['company_id']		= $_SESSION['company_id'];
	
	if(empty($general_book_id)){
		$inserid_gen=add_record("tbl_general_book", $info_gen, $dbcon);
	}else{
		$updateid=update_record('tbl_general_book', $info_gen,"general_book_id=".$general_book_id , $dbcon);
	}
}

?>
