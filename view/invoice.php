<?php 
	session_start();
	include_once("../config/config.php");
	include_once("../config/session.php");
	include_once("../include/common_functions.php");
	$token = md5(rand(1000,9999));
	$_SESSION['token'] = $token;
	$form="Invoice";
	$countryid='101';
	$stateid='1';
	$cityid='1';
	if(strpos($_SERVER[REQUEST_URI], "invoiceedit")==false)
	{
		$mode="Add";
		$date=date('d-m-Y');
		$load_inv_type='';
		$query="select l_id from  tbl_ledger where l_group=25 and company_id=".$_SESSION['company_id'];
		$rel=mysqli_fetch_assoc($dbcon->query($query));	
		$sales_ledger=$rel['l_id'];
		
		$query_type="select * from tbl_invoicetype where status=0 and type_id=2 and deletable=1 and company_id=".$_SESSION['company_id'];
		$rel_type=mysqli_fetch_assoc($dbcon->query($query_type));	
		$load_inv_type=$rel_type['invoicetype_id'];
		$vehicle_no=load_vehicle_no($dbcon);
	}
	else
	{
		$mode="Edit";
		$invoiceid=$dbcon->real_escape_string($_REQUEST['id']);
		$query="select * from tbl_invoice where invoice_id=$invoiceid";
		$rel=mysqli_fetch_assoc($dbcon->query($query));
		$order_date='';$dispatch_date='';
		if($rel['order_date']!="1970-01-01" && $rel['order_date']!="0000-00-00")
		{
			$order_date=date('d-m-Y',strtotime($rel['order_date']));
		}
		if($rel['dispatch_date']!="1970-01-01 00:00:00" && $rel['dispatch_date']!="0000-00-00 00:00:00")
		{
			$dispatch_date=date('d-m-Y h:i a',strtotime($rel['dispatch_date']));
		}
		$invoice_no=$rel['invoice_no'];
		$challan_no=$rel['challan_no'];
		$load_inv_type=$rel['invoicetype_id'];
		$sales_ledger=$rel['sales_ledger_id'];
		$vehicle_no=$rel['vehicle_no'];
	}
	$set="select * from tbl_company where company_id=".$_SESSION['company_id'];
	$set_head=mysqli_fetch_assoc($dbcon->query($set));
	//$com="select * from tbl_company where company_id=".$_SESSION['company_id'];
	//$comty=mysqli_fetch_assoc($dbcon->query($com));	
?>

<!DOCTYPE html>
<html lang="en">
	<head>
	<?php include_once('../include/include_css_file.php');?>
	</head>
	<body>
		<section id="container" class="sidebar-closed">
			<?php include_once('../include/include_top_menu.php');?>
			<?php include_once('../include/left_menu.php');?>
			<section id="main-content">
				<section class="wrapper">
					<div class="row">
						<div class="col-lg-12">
							<section class="panel">
								<header class="panel-heading">
									<h3 style="float:left;"> <?=$mode .' '.$form?></h3>
									<? include_once("../include/head_menu.php") ?>
								</header>	
								<div class="">
									<div class="col-xs-12"></div>
									<ul class="breadcrumb">
										<li><a href="<?=ROOT.'dashboard'?>"><i class="fa fa-home"></i> Home</a></li>
										<li><a href="<?=ROOT.'invoice_list'?>">Invoice List</a></li>
									</ul>
								</div>
							</section>
						</div>	
					</div>
				
					<div class="row">			
						<div class="col-sm-12">
							<section class="panel">
								<header class="panel-heading">
								  New <?=$form?>
								</header>	
								<div class="panel-body">
									<form class="form-horizontal" role="form" id="invoice_add" action="javascript:;" method="post" name="invoice_add">
										<div class="col-md-12 col-xs-12" style="margin-bottom:10px;">
											<div class="col-md-5 col-xs-12">
												<label class="col-md-3 control-label" style="">Select Sales ledger </label>
												<div class="col-md-6 col-xs-12 resclear">
													<select class="select2" name="sales_ledger_id" id="sales_ledger_id" required title="Select sales Ledger">
														<?=get_ledger($dbcon,$sales_ledger);?>
													</select>
												</div>
											</div>
										</div>	
										<div style="clear: both;"></div> 
										<div class="col-md-12">
											<div class="col-md-4">
												<div class="form-group">
													<label class="col-md-4 control-label"> Invoice type </label>
													<div class="col-md-6 col-xs-12">
														<select style="padding-right: 0px;" class="form-control" name="invoicetype_id" id="invoicetype_id" onChange="load_invoiceno(this.value)" <? if($mode=='Edit'){?> readonly="readonly"<? }?> >
															<?=getinvoicetype($dbcon,$load_inv_type);?>
														</select>
													</div>
												</div>
											</div>
											<div class="col-md-4">
												<div class="form-group">
													<label class="col-md-4 control-label">Invoice No *</label>
													<div class="col-md-6 col-xs-12">
														<input id="invoice_no" name="invoice_no" type="text" class="form-control" title="Enter Invoice No" placeholder="Invoice No" value="<?=$invoice_no?>" placeholder="Invoice No" required>		
													</div>
												</div>
											</div>
											<div class="col-md-4">
												<div class="form-group">
												  <label class="col-md-4 control-label">Invoice Date*</label>
													<div class="col-md-6 col-xs-12">
														<input id="invoice_date" name="invoice_date" type="text" class="form-control default-date-picker required valid" title="Invoice Date" placeholder="Invoice Date" value="<? if($mode=='Add'){echo $date;}else if($mode=='Edit'){echo date('d-m-Y',strtotime($rel['invoice_date']));}?>" placeholder="Invoice Date">
													</div>
												</div>	
											</div>
										</div>
										<div class="col-md-12" style="display:none;">
											<div class="col-md-4" style="display:none;">
												<div class="form-group">
													<label class="col-md-4 control-label">D.C. No *</label>
													<div class="col-md-6 col-xs-12">
														<input id="challan_no" name="challan_no" type="text" class="form-control" title="Enter Challan No" value="<?=$challan_no?>" placeholder="Challan No" required>
													</div>
												</div>
											</div>
											<div class="col-md-4" style="display:none;">
												<div class="form-group">
													<label class="col-md-4 control-label">D.C. Date*</label>
													<div class="col-md-6 col-xs-12">
														<input id="challan_date" name="challan_date" type="text" class="form-control default-date-picker required valid" title="Date" value="<? if($mode=='Add'){echo $date;}else if($mode=='Edit'){echo date('d-m-Y',strtotime($rel['challan_date']));}?>" placeholder="Challan Date">
													</div>
												</div>	
											</div>
											
										</div>
										<div class="col-md-12" >
											<div class="col-md-4">
												<div class="form-group">
													<label class="col-md-4 control-label">P.O. No </label>
													<div class="col-md-6 col-xs-12">
														<input id="order_no" name="order_no" type="text" class="form-control" title="Enter Order No" value="<?=$rel['order_no']?>" placeholder="P.O. No">		
													</div>
												</div>
											</div>
											<div class="col-md-4">
												<div class="form-group">
													<label class="col-md-4 control-label">P.O. Date</label>
													<div class="col-md-6 col-xs-12">
														<input id="order_date" name="order_date" type="text" class="form-control default-date-picker valid" title="Date" value="<?=$order_date?>" placeholder="P.O. Date">
													</div>
												</div>	
											</div>
											<div class="col-md-4">
												<div class="form-group">
													<label class="col-md-4 control-label">Crates No</label>
													<div class="col-md-6 col-xs-12">
														<input type="text" id="docket_no" name="docket_no" class="form-control" title="Crates No"   placeholder="Crates No" value="<?=$rel['docket_no']?>" />
													</div>
												</div>	
											</div>
											<div class="col-md-4" style="display:none;">				   
												<div class="form-group">
													<label class="col-md-4 control-label" style="white-space:nowrap;"> Mode/Payment Terms</label>
													<div class="col-md-6 col-xs-10">
														<select style="padding-right: 0px;" class="form-control" name="payment_terms" id="payment_terms" onChange="demo();" placeholder="Days">
															<?=getpaymentterms($dbcon,$rel['payment_days']);?>
														</select>
													</div>
													<div class="col-md-2">
														<input type="button" name="addproduct2" id="addproduct2" data-toggle="modal" data-target="#bs-payterms-modal-lg" class="btn btn-primary col-xs-2" value="+"/>
													</div>
												</div>	
											</div>	
											<div class="col-md-4" style="display:none;">
												<div class="form-group">
													<label class="col-md-4 control-label">Mode of Dispatch</label>
													<div class="col-md-6 col-xs-10">
														<select style="padding-right: 0px;" class="form-control" name="dispatch_doc_no" id="dispatch_doc_no" >
															<?=getmodeofdispache($dbcon,$rel['dispatch_doc_no']);?>
														</select>
													</div>
													<div class="col-md-2">
														<input type="button" name="addproduct4" id="addproduct4" data-toggle="modal" data-target="#bs-dispatch-modal" class="btn btn-primary col-xs-2" value="+"/>
													</div>
												</div>	
											</div>
										</div>
										<div class="col-md-12">
											
											<div class="col-md-4">
												<div class="form-group">
													<label class="col-md-4 control-label">Vehicle No</label>
													<div class="col-md-6 col-xs-12">
														<input type="text" id="vehicle_no" name="vehicle_no" class="form-control" title="Vehicle No" placeholder="Vehicle No" value="<?=$vehicle_no?>" />
													</div>
												</div>	
										   </div>
										   <? if($mode=="Edit") {?>
												<div class="col-md-4">
													<div class="form-group">
														<label class="col-md-4 control-label">E-Way Bill No</label>
														<div class="col-md-6 col-xs-12">
															<input type="text" id="e_way_bill_no" name="e_way_bill_no" class="form-control" title="E-Way Bill No" placeholder="E-Way Bill No" value="<?=$rel['e_way_bill_no']?>" />
														</div>
													</div>	
												</div>
										   <? } ?>
										   <? if($mode=="Add") {?>
												<div class="col-md-4"  >
													<div class="form-group">
														<label class="col-md-4 control-label" style="white-space:nowrap;">Payment Reminder</label>
														<div class="col-md-6 col-xs-12">
															<input type="number" id="payment_reminder"  name="payment_reminder" class="form-control" title="Payment Notification"  value="<?=$rel['payment_reminder']?>" placeholder=" in Days">
														</div>
													</div>	
												</div>
										   <? } ?>
										</div>
										<div class="col-md-12" style="display:none;">
											<div class="col-md-4">
												<div class="form-group">
													<label class="col-md-4 control-label">Consignee Same *</label>
													<div class="col-md-6 col-xs-12">
														<? $ck=''; if(empty($rel['consignee_id'])){ $ck='checked="checked"'; } ?>
														<input id="same_as" name="same_as" type="checkbox" class="" title="Other Name"  <?=$ck?> value="1" style="width:20%;height:25px;" onChange="consinee_change(this.checked);">
													</div>
												</div>
											</div>
										</div>
										<div class="col-md-12">								
											<div class="col-md-6">
												<div class="form-group">
													<label class="col-md-3 control-label">Company *</label>
													<div class="col-md-6 col-xs-12">
														<select tabindex="1" class="select2" name="cust_id" id="cust_id" onChange="load_consignee(this.value);load_po_no(this.value);" >
															<?//=getcust($dbcon,$rel['cust_id']);?>	
															<?=get_ledger_cust($dbcon,$rel['cust_id']);?>
														</select>
													</div>
													<div class="col-md-3 salesobtn resclear">
													
														<input type="button" style="display:none;"  name="addcust" id="addcust" data-toggle="modal" data-target="#bs-example-modal-lg"  class="btn btn-primary" value="New Company"/>
													</div>
												</div>									
											</div>
											<div class="col-md-6" id="consignee" <?if(empty($rel['consignee_id'])){echo "style='display:none'";}?>>
												<div class="form-group">
													<label class="col-md-3 control-label">Consignee *</label>
													<div class="col-md-6 col-xs-10">
														<select class="select2" name="consignee_id" id="consignee_id">
															<?=get_custmer_consignee($dbcon,$rel['cust_id'],$rel['consignee_id'])?>
														</select>
													</div>
													<div class="col-md-3">
														<input type="button" style="display:none;" class="btn btn-primary col-xs-10" name="addcust" id="addcust" onClick="open_consignee_click();" value="New Consignee"/>
													</div>
												</div>									
											</div>
										</div>
										<div class="col-md-12" style="display:none;">
											<div class="col-md-6" id="sales_order_div" style="display:none;">
												<div class="form-group">
													<label class="col-md-3 control-label">Choose Sales Order</label>
													<div class="col-md-6 col-xs-12">
														<select class="select2" name="sales_order_id" id="sales_order_id"  >
															<option value="">Choose Sales Order</option>
															<?//=getcust($dbcon,$rel['sales_order_id']);?>	
														</select>
													</div>
												</div>									
											</div>
										</div>
										<div class="col-md-12">
											<div class="form-group">
												<table cellspacing="10" style="border-spacing:10px;" id="product_list" class="display table table12 table-striped table-bordered  " >
													<tr id="field">
														<th width="20%" class="text-center">	
														<div class="col-md-10">
															Product Detail
														</div>
														<div class="col-md-1">
															<input type="button" style="display:none;" name="addproduct" title="Add Product" id="addproduct1" data-toggle="modal" data-target="#bs-example-modal-addproduct" class="btn btn-xs btn-primary" value="+"/>
														</div>
														</th>
														<th width="8%" class="text-center" >HSN Code</th>
														<th width="6%" class="text-center">Quantity</th>
														<th width="7%" class="text-center">Rate</th>
														<th width="7%" class="text-center">MRP</th>
														<th width="7%" class="text-center" style="display:none;">Per</th>
														<th width="6%" style="display:none;">Discount</th>
														<th width="10%" style="display:none;">Taxable Value</th>
														<th width="13%" style="display:none;">Tax</th>
														<th width="10%" class="text-center">Amount</th>
														<th width="5%" class="text-center"></th>
													</tr>
													<input type="hidden" value="1" name="fieldcnt" id="fieldcnt"/>
													<tr id="field1">
														<td data-label="PRODUCT NAME" class="resclear" style="vertical-align:top;">
															<select class="select2" tabindex="2" title="Select product" name="product_id" id="product_id" <?if($set_head['software_type']=="3"){?>onChange="load_productdetail(this.value);load_qty(this.value,0);"<?} else{?>onChange="load_productdetail(this.value);"<?}?>>
																<?=getproduct($dbcon,0,'0,2,3');?>
															</select>
															<!--<br/><br/>-->
															<textarea id="product_des" name="product_des" style="display:none;" class="form-control" placeholder="Product Description"></textarea>
														</td>	
														<td data-label="HSN CODE" style="vertical-align:top;">
															<input type="text"  title="Enter HSN Code" placeholder="HSN Code" id="product_hsn_code" name="product_hsn_code" class="form-control" readonly />
														</td>
														<td data-label="QTY"  style="vertical-align:top;">
															<input type="number" tabindex="3"  title="Enter Qty" min="0" id="product_qty" name="product_qty"  class="form-control" onKeyUp="get_amount();get_discount('per');"/><br/>
														</td>
														<td data-label="RATE" style="vertical-align:top;">
															<input type="number"  title="Enter Rate" min="0" id="product_rate" name="product_rate"  placeholder="Rate" onKeyUp="get_amount();get_discount('per');" class="form-control"/>
															<br/>
															
															<!--<button type="button" title="Show Previous Rate History" name="rate_history" id="rate_history" onclick="load_rate_hist()" style="display:none;" class="btn btn-info"><i class="fa fa-eye"></i> show</button>-->
														</td>
														<td data-label="MRP" style="vertical-align:top;">
															<input type="number"  title="Enter MRP" min="0" id="mrp" name="mrp"  placeholder="Product MRP" class="form-control"/>
															<br/>
														</td>
														<td data-label="PER" style="vertical-align:top;display:none">
															<select class="select2"  title="Select Unit" name="unit_id" id="unit_id">
																<?=getunit($dbcon,0);?>
															</select>
														</td>
														<td data-label="DISCOUNT" style="vertical-align:top; display:none;" >
															<input type="number" title="Enter Discount" min="0" id="product_discount" name="product_discount" onkeyup="get_discount('amt');" class="form-control" placeholder="in Rs."/>
															<br/>
															<input type="number"  title="Enter Discount Percentage" min="0" id="discount_per" name="discount_per" onkeyup="get_discount('per');" class="form-control" placeholder="in %" max="100"/>
														</td>
														<td data-label="TAXABLE VALUE" style="vertical-align:top;display:none;">
															<input type="number" title="Taxable Value" placeholder="Taxable Value" min="0" id="taxable_value" name="taxable_value" class="form-control" readonly />
														</td>
														<td data-label="TAX" style="vertical-align:top;display:none;">
															<select class="form-control" name="formulaid" id="formulaid" onChange="get_amount();">
																	<?=getformula($dbcon,$rel['formulaid']);?>
															</select>
														</td>
														<td data-label="AMOUNT" style="vertical-align:top;"> 
															<input type="number" min="0" id="product_amount" readonly="readonly" name="product_amount" class="form-control" onmouseover="this.title=this.value"/>
														</td>
														<td data-label="ACTION" style="vertical-align:top;"> 
															<input tabindex="4" type="button"  name="addrow" id="addrow" onClick="return add_field();"  class="btn btn-primary" value="Add"/>	
														</td>
														<input type='hidden' name='edit_id' id='edit_id' value='' />
													</tr>
												</table>								
											</div>
										</div>
										<div id="sale_productdata"></div>
										<div class="col-md-12">
											<div class="col-md-6">
												<div class="form-group">
													<label class="col-md-4 control-label">Remarks </label>
													<div class="col-md-6 col-xs-11">
														<textarea id="remark" name="remark" placeholder="Remarks" class="form-control" rows="3"><?=$rel['remark']?></textarea> 
													</div>
												</div>
												<div class="form-group">
													<label class="col-md-4 control-label">Reverse Charge  </label>
													<div class="col-md-1 col-xs-11">
														<input id="reverse_charge_check"  name="reverse_charge_check" type="checkbox" class="" title="Reverse Charge" placeholder="Reverse Charge" <?=(empty($rel['reverse_charge'])?'':'checked="checked"')?>  value="1">
													</div>								
												</div>
											</div>	
											<?if($set_head['show_charges']=='1'){$ttl_display="display:block";}else{$ttl_display="display:none";}	?>	
											<div class="col-md-6">
												<div class="form-group" style="<?=$ttl_display?>">
													<label class="col-md-5 control-label">Total *</label>
													<div class="col-md-5 col-xs-11">
														<input id="total" name="Total" type="text" readonly="readonly" class="form-control" title="dispatch_no" max="0"  value="<? if($mode=="Add"){echo '0';}else if($mode=='Edit'){ echo $e_total;}?>" placeholder="Total">
													</div>
												</div>	
												<div class="form-group">
													<label class="col-md-5 control-label">Net Amount *</label>
													<div class="col-md-5 col-xs-11">
														<input id="g_total" name="g_total" type="text" class="form-control" title="Net Amount" value="<?=$rel['g_total']?>" placeholder="Grand Total" readonly="readonly">
													</div>
												</div>	
												<div class="form-group">
													<label class="col-md-5 control-label">Select Print</label>
													<div class="col-md-5 col-xs-11">
														<select class="form-control" name="print_status" id="print_status">
															<option value="1">ORIGINAL</option>
															<option value="2">DUPLICATE</option>
															<option value="3">TRIPLICATE</option>
															<option value="4">EXTRA</option>
														</select>
													</div>
												</div>
											<? if($mode==="Add1") { ?>
												<input type="hidden" name="full_paid_type" id="full_paid_type" value="CR" />
												<div class="form-group">
													<label class="col-md-5 control-label">Payment Mode </label>
													<div class="col-md-5 col-xs-11">
														<select class="form-control" name="paymentmodeid" id="paymentmodeid" onChange="paymentmode(this.value);get_cash_opening_bal(this.value,'max_paid_amount','tran_amounterr')" title="Select Payment Mode">
																<?//=getpaymentmode($dbcon);?>	
																<?=get_ledger_cash($dbcon);?>	
																<?//=get_ledger($dbcon,$rel['vender_id']);?>	
														</select>
													</div>
												</div>
												<div style="display:none" id="cheque_data">
													<div class="form-group">
														<label class="col-md-5 control-label">Reference No </label>
														<div class="col-md-5 col-xs-11">
															<input id="cheque_dtl" name="cheque_dtl" type="text" class="form-control" title="cheque_dtl" value="<?=$rel['cheque_dtl']?>" placeholder="Cheque No. / NEFT No. / RTGS No." >
														</div>
													</div>
													<div class="form-group">  	
														<label class="col-md-5 control-label" >Reference date </label>
														<div class="col-md-5 col-xs-11">
															<input id="ref_date" name="ref_date" type="text" class="form-control default-date-picker" title="Reference Date" value="<?=$date?>" placeholder="Cheque Date/NEFT Date">
														</div>
													</div>
													<div class="form-group">
														<label class="col-md-5 control-label">Paid Amount</label>
														<div class="col-md-5 col-xs-11">
															<input id="paid_amount" name="paid_amount" type="number" min='0' class="form-control" title="" required value="" max="<? echo $due; ?>" placeholder="Amount">
																		
															<br/><span class="amtbalance" style="display:none"><span class="label label-danger"  >NOTE!</span><span style="font-size:14px;padding-left:10px" id="tran_amounterr"> </span></span>
														</div>
														<div class="col-md-2 col-xs-11"  style="font-size:14px;display:none;">
															<select class="select2" name="paid_typeid"  id="paid_typeid" title="Select Type">
																<?=getbalance_type($dbcon,1)?>
															</select>
														</div>
													</div>
												</div>
											<? } ?>
											</div>
										</div>
										<div class="col-md-12">
											<button type="submit" class="btn btn-success" id="save" name="save">Save</button>
											<button type="button" onClick="invoice_submit();" class="btn btn-success" tabindex="5" id="saveprint" name="saveprint">Save and Print</button> &nbsp;
											<a href="<?=ROOT.'invoice_list'?>" type="button" class="btn btn-danger">Cancel</a>
											<div class="col-md-3"></div>			
										</div>		
								
										<input type='hidden' name='mode' id='mode' value='<?=$mode?>' />
										<input type='hidden' name='o_total' id='o_total' value='<?=$rel['g_total']?>' />
										<input type='hidden' name='save_print' id='save_print' value='' />
										<input type='hidden' name='eid' id='eid' value='<?=$rel['invoice_id']?>' />
										<? $receiptno= 'rec/'.$invoiceid;?>
										<input type='hidden' name='receipt_no' id='receipt_no' value='<?=$receiptno?>' />
										<input type='hidden' name='type_id' id='type_id' value='<?=$rel["type_id"]?>' />
										<input type='hidden' name='token' id='token' value='<?php echo $token; ?>' />				  
									</form>
								</div>
							</section>
						</div><!--Vendor row end-->	
					</div>	
				</section>
			</section>
					<?php //include_once('../include/add_cust.php');?>
					<?php //include_once('../include/add_product.php');?>
					<?php //include_once('../include/add_city.php');?>
					<?php //include_once('../include/add_state.php');?>
					<?php //include_once('../include/add_payterms.php');?>
					<?php //include_once('../include/footer.php');?>
					<?php //include_once('../include/add_placesupally.php');?>
					<?php //include_once('../include/add_modedispatch.php');?>
					<?php //include_once('../include/add_worktype.php');?>
					<?php //include_once('../include/add_invdescription.php');?>
		</section>

		<!-- js placed at the end of the document so the pages load faster -->
		<?php include_once('../include/include_js_file.php');
		include_once('../include/add_consignee.php');
		//include_once('../include/serial_number_add.php');
		include_once('../include/include_show_history.php');
		?>   
		<script src="<?=ROOT?>js/app/invoice.js"></script>
		<script src="<?=ROOT?>js/app/customer.js"></script>
		<!--<script src="<?=ROOT?>js/app/product_mst.js"></script>
		<script src="<?=ROOT?>js/app/city_mst.js"></script>
		<script src="<?=ROOT?>js/app/payment_terms.js"></script>
		<script src="<?=ROOT?>js/app/invoice_consignee.js"></script>
		<script src="<?=ROOT?>js/app/state_mst.js"></script>
		<script src="<?=ROOT?>js/app/place_supply.js"></script>
		<script src="<?=ROOT?>js/app/mode_disptch.js"></script>
		<script src="<?=ROOT?>js/app/work_type.js"></script>
		<script src="<?=ROOT?>js/app/description_mst.js"></script>-->
		
		<!--<script src="js/count.js"></script>-->
		<script>
			$(".select2").select2({ width: '100%' });
			$('.default-date-picker').datepicker({ format: 'dd-mm-yyyy', autoclose: true });
			$(".form_datetime-meridian").datetimepicker({
				format: "dd-mm-yyyy HH:ii P",
				showMeridian: true,
				autoclose: true,
				todayBtn: true,
				pickerPosition: "bottom-left"
			});
			
			function consinee_change(val){
				if(val=='1'){
					$('#consignee_id').select2("val","");
					$('#consignee').hide();
				}
				else{
					$('#consignee').show();
				}
			}
		</script>
		<?
			echo "<script>load_state(".$countryid.",'stateid',".$stateid.")</script>";
			echo "<script>load_city(".$stateid.",'cityid',".$cityid.")</script>";
			echo "<script>load_state(".$countryid.",'con_stateid',".$stateid.")</script>";
			echo "<script>load_city(".$stateid.",'con_cityid',".$cityid.")</script>";
			
				echo "<script>show_data();</script>";
				if($mode=="Add"){
					echo "<script>load_invoiceno(".$load_inv_type.");</script>";
					
				}
		?>
	</body>
</html>
