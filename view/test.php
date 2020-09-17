<?
include_once("../config/config.php");
include("../include/function_database_query.php");

			$query="SELECT * FROM `tbl_receipt` WHERE `status`=2";
			$result=$dbcon->query($query);
			while($rel=mysqli_fetch_assoc($result))
			{
				$info1['genral_book_status']=2;	
				$updateid=update_record("tbl_general_book", $info1," table_name='tbl_payment' and table_id=".$rel['receipt_id'] , $dbcon);
			}

		$query="SELECT * FROM `tbl_journal_trn` WHERE `journal_trn_status`=2";
			$result=$dbcon->query($query);
			while($rel=mysqli_fetch_assoc($result))
			{
				$info1['genral_book_status']=2;	
				$updateid=update_record("tbl_general_book", $info1," table_name='tbl_journal_trn' and table_id=".$rel['journal_trn_id'] , $dbcon);
			}
			
		$query="SELECT * FROM `tbl_pono` WHERE `status`=2";
			$result=$dbcon->query($query);
			while($rel=mysqli_fetch_assoc($result))
			{
				$info1['genral_book_status']=2;	
				$updateid=update_record("tbl_general_book", $info1," table_name='tbl_purchase' and table_id=".$rel['po_id'] , $dbcon);
			}
		

?>