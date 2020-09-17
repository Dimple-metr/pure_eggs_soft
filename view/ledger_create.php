<?php 
	session_start();
	include_once("../config/config.php");
	include_once("../config/session.php");
	include_once("../include/common_functions.php");
	$form="Ledger";
	if(strpos($_SERVER[REQUEST_URI], "ledger_edit")==false) {
		$mode="Add";
		$countryid="101";
		$stateid="1";
		$cityid="1";
	}
	else {
		$mode="Edit";
		
		$ledger_id=$dbcon->real_escape_string($_REQUEST['id']);
		//echo $ledger_id;
		$query="select * from  tbl_ledger where l_id=$ledger_id";
		$rel=mysqli_fetch_assoc($dbcon->query($query));	
		
		$form_type=$rel['l_form'];
		$form_id=$rel['l_form_id'];
		
		//echo $form_type;
		
		$order_date=date('d-m-Y',strtotime($rel['l_po_date']));
		$countryid=$rel['countryid'];
		$stateid=$rel['stateid'];
		$cityid=$rel['cityid'];
	}
?>

<!DOCTYPE html>
<html lang="en">
	<head>
		<?php include_once('../include/include_css_file.php');?>
		<style>
			.head_margin
			{
				padding:10px;
			}
			.form_class
			{
				
			}
			.back_head_color
			{
				background-color:#023d6f !important;
				color:#ffffff !important;
			}
			.row_margin
			{
				margin-top:20px;
			}
			.margin_row
			{
				margin-top:0px;
			}
			
			.ledger_forms
			{
				display:none;
			}
			.control-label {
					padding-left: 0px;
					padding-right: 0px;
				}
			
		</style>
	</head>
	<body>
	<section id="container" >
		<?php include_once('../include/include_top_menu.php');?>
		<?php include_once('../include/left_menu.php');?>
		<form class="form-horizontal" role="form" id="ledger_add" action="javascript:;" method="post" name="ledger_add">	
			<section id="main-content">
				<section class="wrapper">			
					<div class="row">
						<div class="col-lg-12">
							<section class="panel">
								<header class="panel-heading"><h3><?=$mode.' '.$form?></h3></header>	
								<div class="">
									<ul class="breadcrumb">
										<li><a href="<?=ROOT.'dashboard'?>"><i class="fa fa-home"></i> Home</a></li>
										<li><a href="<?=ROOT.'ledger_list'?>"><?=$form?> List</a></li>
									</ul>
								</div>
							</section>
						</div>	
					</div>
					<div class="row">			
						<div class="col-sm-12">
							<section class="panel">
								<header class="panel-heading">New <?=$form?></header>
								<div class="panel-body ">
									<div class="row">
										<div class="col-md-12 margin_row">
											<div class="col-md-6">
												<div class="form-group">
													<label class="col-md-3 control-label">Ledger Name *</label>
													<div class="col-md-8 col-xs-11">
														<input type="text" class="form-control" placeholder="Ledger Name" title="Ledger Name" name="ledger_name" id="ledger_name" value="<?=$rel['l_name']?>" required  />
													</div>
												</div>
											</div>
											<div class="col-md-6">
												<div class="form-group">
												 <label class="col-md-3 control-label">Select Group</label>
													<div class="col-md-8 col-xs-11">
														<select class="select2" name="ledger_grp" id="ledger_grp" required onchange="show_div_ledger(this.value)" >
															<?=get_all_group($dbcon,$rel['l_group'],'','0');?>
														</select>
													</div>
												</div>
											</div>
										</div>
										<div class="col-md-12 margin_row">
											<div class="col-md-6">
												<div class="form-group">
													<label class="col-md-3 control-label">Select Country *</label>
													<div class="col-md-8 col-xs-11">
														<select class="select2" name="countryid" id="countryid" onChange="load_state(this.value,'stateid','')">
															<?=get_country($dbcon,$countryid)?>
														</select>
													</div>
												</div>
											</div>
											
											<div class="col-md-6">
												<div class="form-group">
													<label class="col-md-3 control-label">Select State *</label>
													<div class="col-md-8 col-xs-11">
														<select class="select2" name="stateid" id="stateid" onChange="load_city(this.value,'cityid','')">
															<option value="">Select State</option>	
															<?//=getstate($dbcon,$rel['stateid'])?>				
														</select>
													</div>
													<div class="col-md-1">
														<input type="button"  name="addState" id="addState" data-toggle="modal" data-target="" onclick="add_state();" class="btn btn-primary" value="+"/>
													</div>
												</div>
											</div>
										</div>
										<div class="col-md-12 margin_row">
											<div class="col-md-6">
												<div class="form-group">
													<label class="col-md-3 control-label">Select City *</label>
													<div class="col-md-8 col-xs-11">
														<select class="select2" name="cityid" id="cityid">
															<option value="">Select City</option>	
														</select>
													</div>
													<div class="col-md-1">
													<input type="button" name="addCity" id="addCity" data-toggle="modal" data-target="" onclick="add_city();" class="btn btn-primary" value="+"/>
													</div>
												</div>
											</div>
											
											<div class="col-md-6">
												<div class="form-group">
													<label class="col-md-3 control-label">Pin Code</label>
													<div class="col-md-8 col-xs-11">
														<input type="text" class="form-control" placeholder="Customer Pincode" name="cust_pincode" id="cust_pincode"   value="<?=$rel['cust_pincode']?>"  />
													</div>
												</div>
											</div>
										</div>
										<div class="col-md-12 margin_row">
											<div class="col-md-6">
												<div class="form-group">
													<label class="col-md-3 control-label">PAN / IT No.</label>
													<div class="col-md-8 col-xs-11">
														<input type="text" class="form-control" placeholder="Customer PAN" name="m_pan" id="m_pan"   value="<?=$rel['cust_pincode']?>" style="text-transform:uppercase"  />
													</div>
												</div>
											</div>
										</div>
									</div>
								</div>
							</section>
						</div>
						<!--- Customer Form Start -->
	<div class="col-md-12 ledger_forms" id="customer_form" <?php if($mode=='Edit') { if($form_type=='customer_form') { ?> style="display:block !important" <?php } } ?>>
		<div class="row">
			<div class="col-sm-12">
				<header class="panel-heading breadcrumb text-center back_head_color">
					 <h3>Customer Information</h3>
				</header>	
				<section class="">
					<div class="row">
						<div class="col-md-12 margin_row">
							<div class="col-md-6">
								<div class="form-group">
									<label class="col-md-4 control-label">Company Name *</label>
									<div class="col-md-8 col-xs-11">
										<input type="text" class="form-control" placeholder="Company Name" title="Company Name" name="company_name" id="company_name" value="<?=$rel['company_name']?>"    />
									</div>
								</div>
							</div>
								
							<div class="col-md-6">
								<div class="form-group">
									<label class="col-md-4 control-label">Contact Person Name*</label>
									<div class="col-md-8 col-xs-11">
										<input type="text" class="form-control" placeholder="Contact Person Name" title="Contact Person Name" name="cust_cont_name" id="cust_cont_name" value="<?=$rel['cust_cont_name']?>" required />
									</div>
								</div>
							</div>
						</div>
								
						<div class="col-md-12 margin_row">
							
							<div class="col-md-12">
								<div class="form-group">
									<label class="col-md-2 control-label">Company Address*</label>
									<div class="col-md-10 col-xs-11">
									
										<textarea class="form-control" placeholder="Company Address" title="Contact Person Name" name="m_address" id="m_address" required><?=$rel['m_address']?></textarea>
										
									</div>
								</div>
							</div>
						
						</div>
						
						<div class="col-md-12 margin_row">
							<div class="col-md-6">
								<div class="form-group">
									<label class="col-md-4 control-label">GST Registered *</label>
									<div class="col-md-6 col-xs-11">
										<select class="select2" name="cust_gst_reg" id="cust_gst_reg" onchange="changeGstText(this.value)">
											<option value="">Select GST Reg.</option>
											<option value="0" <?php if($rel['cust_gst_reg']=='0'){ echo "selected"; } ?> >Yes</option>
											<option value="1" <?php if($rel['cust_gst_reg']=='1'){ echo "selected"; } ?>>No</option>
										</select>
									</div>
								</div>
							</div>
							<div class="col-md-6" id="gst_div">
								<div class="form-group">
								 <label class="col-md-4 control-label">GSTIN</label>
									<div class="col-md-8 col-xs-11">
										<input type="text" name="gst_no" class="form-control" placeholder="GSTIN"id="gst_no" value="<?=$rel['gst_no']?>" title="Enter Valid GST No." >
									</div>
								</div>
							</div>
						</div>
						<div class="col-md-12 margin_row">
							<div class="col-md-6">
								<div class="form-group">
									<label class="col-md-4 control-label">Mobile No.*</label>
									<div class="col-md-8 col-xs-11">
										<input type="text" class="form-control" placeholder="Mobile No." name="cust_mobile" id="cust_mobile" value="<?=$rel['cust_mobile']?>" onkeypress="return isNumberKey(event)" maxlength="10"  required  />
									</div>
								</div>
							</div>
							<div class="col-md-6">
								<div class="form-group">
									<label class="col-md-4 control-label">Email*</label>
									<div class="col-md-8 col-xs-11">
										<input type="text" class="form-control" placeholder="Email" title="Email" name="cust_email" id="cust_email" value="<?=$rel['cust_email']?>"  required  />
									</div>	
								</div>
							</div>
						</div>
						<div class="col-md-12 margin_row">
							<div class="col-md-6">
								<div class="form-group">
									<label class="col-md-4 control-label">Website </label>
									<div class="col-md-8 col-xs-11">
										<input type="text" class="form-control" placeholder="Website" title="Website" name="cust_website" id="cust_website" value="<?=$rel['cust_website']?>"  />
									</div>	
								</div>
							</div>
							<div class="col-md-6">
								<div class="form-group">
									<label class="col-md-4 control-label">Zone</label>
									<div class="col-md-6 col-xs-11">
										<select class="select2" name="zone_id" id="zone_id" onchange="get_branch_by_zone(this.value,'branch_id_customer','')">
											<?=get_zone($dbcon,$rel['zone_id'],$rel['zone_id']);?>				
										</select>
									</div>	
									<div class="col-md-2 col-xs-11">
										<input type="button" name="addZone" id="addZone" data-toggle="modal" data-target="#add_zone_modal" class="btn btn-primary" value="+ Add Zone"/>
									</div>
								</div>
							</div>
						</div>
						<div class="col-md-12 margin_row">
							<div class="col-md-6">
								<div class="form-group">
									<label class="col-md-4 control-label">Branch</label>
									<div class="col-md-6 col-xs-11">
										<select class="select2" name="branch_id_customer" id="branch_id_customer">
															
										</select>
									</div>	
									
								</div>
							</div>
						</div>
						<div class="col-md-12 margin_row">
							<div class="col-md-6">
								<div class="form-group">
									<label class="col-md-4 control-label">Payment Terms</label>
									<div class="col-md-8 col-xs-11">
										<select class="select2" name="pay_terms" id="pay_terms">
											<option value="">--Payment Terms--</option>
											<option value="15"  <?php if($rel['pay_terms']=='15'){ echo "selected"; } ?>>15 Days</option>
											<option value="30" <?php if($rel['pay_terms']=='30'){ echo "selected"; } ?>>30 Days</option>
											<option value="45" <?php if($rel['pay_terms']=='45'){ echo "selected"; } ?>>45 Days</option>
											<option value="45" <?php if($rel['pay_terms']=='45'){ echo "selected"; } ?>>60 Days</option>
											<option value="45" <?php if($rel['pay_terms']=='45'){ echo "selected"; } ?>>90 Days</option>
											<option value="45" <?php if($rel['pay_terms']=='45'){ echo "selected"; } ?>>120 Days</option>
										</select>
									</div>	
								</div>
							</div>
							<div class="col-md-6">
								<div class="form-group">
								 <label class="col-md-4 control-label">Bill Type</label>
									<div class="col-md-8 col-xs-11">
										<select class="select2" name="bill_type" id="bill_type">
											<!--<option value="">--Select Bill Method--</option>-->
											<option value="0" <?php if($rel['bill_type']=='0'){ echo "selected"; } ?> >Bill To Bill</option>
											<option value="1"  <?php if($rel['bill_type']=='1'){ echo "selected"; } ?>  >Overall</option>
										</select>
									</div>
								</div>
							</div>
						</div>
						<div class="col-md-12 margin_row">
							<div class="col-md-6 margin_row">
								<div class="form-group">
									<label class="col-md-4 control-label">P.O. No</label>
									<div class="col-md-8 col-xs-11">
										<input id="l_pono" name="l_pono" type="text" class="form-control" title="Enter Order No" value="<?=$rel['l_pono']?>" placeholder="P.O. No">		
									</div>	
								</div>
							</div>
							<div class="col-md-6 margin_row">
								<div class="form-group">
									<label class="col-md-4 control-label">P.O. Date</label>
									<div class="col-md-8 col-xs-11">
										<input id="l_po_date" name="l_po_date" type="text" class="form-control default-date-picker valid" title="Date" value="<?=$order_date?>" placeholder="P.O. Date">
									</div>	
								</div>
							</div>
						</div>

						<div class="col-md-6 margin_row">
							<div class="form-group">
								<label class="col-md-4 control-label">Remark</label>
								<div class="col-md-8 col-xs-11">
									<textarea class="form-control" name="cust_remark" id="cust_remark"><?=$rel['cust_remark']?></textarea>
								</div>	
							</div>
						</div>
						
					</div>	
				</section>		
				<section class="panel" style="padding:20px">
					<div class="row">		
						<div class="col-xs-2"> <!-- required for floating -->
						  <!-- Nav tabs -->
						  <ul class="nav nav-tabs tabs-left">
							<li class="active"><a href="#tbank" data-toggle="tab" id="ltunit">Bank Details</a></li>
							<li><a href="#tcontact" data-toggle="tab" id="ltbopen">Contact Person</a></li>
						  </ul>
						</div>
						<div class="col-xs-10">
							<!-- Tab panes -->
							<div class="tab-content">
								<div class="tab-pane active" id="tbank">
									<div class="row">
										<div class="col-md-12">
											<h3 style="text-align:center;" class="head_margin"><a style="border-bottom:dotted blue thin">Bank Details</a></h3>
										</div>
									</div>
									<div class="row">
										<div class="col-md-12 margin_row">
											<table class="table table-bordered">
												<tr>
													<th width="25%">A/c No</th>
													<th width="25%">Bank Nmae</th>
													<th width="25%">A/C Name</th>
													<th width="15%">IFSC</th>
													<td width="15%">Opening</td>
													<td></td>
												</tr>
												<tr>
													<td>
														<input type="text" class="form-control" name="bank_ac" id="bank_ac" onkeypress="return isNumberKey(event)" />
													</td>
													<td  width="15%">
														<select class="select2" name="bank_name" id="bank_name" >
															<?=get_all_bank($dbcon,0);?>
														</select>
													</td>
													<td>
														<input type="text" class="form-control" name="ac_name" value="" id="ac_name" />
													</td>
													<td>
														<input type="text" class="form-control" name="bank_ifsc" value="" id="bank_ifsc" />
													</td>
													<td>
														<input type="text" class="form-control" name="bank_open" value="" id="bank_open" onkeypress="return isNumberKey(event)" />
													</td>
													<td>
														<input type="button" class="btn btn-primary" value="ADD"  style="box-shadow: 3px 3px #61a642;" onclick="add_bank()" id="add_bank_bt" />
													</td>
														<input type="hidden" id="edit_id" value=""  />
														<input type="hidden" id="eid" value="<?php echo $rel['cust_id']; ?>"  />
												</tr>
											</table>
										</div>
										<div class="col-md-12"  id="table_bank_details"></div>
									</div>
								</div>
								<div class="tab-pane" id="tcontact">
									<div class="row">
										<div class="col-md-12">
											<h3 style="text-align:center;" class="head_margin"><a style="border-bottom:dotted blue thin">Contact Person Details</a></h3>
										</div>
									</div>
									<div class="row">
										<div class="col-md-12 margin_row">
											<table class="table table-bordered">
												<tr>
													<th>Name</th>
													<th>Mobile</th>
													<th>Email</th>
													<td></td>
												</tr>
												<tr>
													<td>
														<input type="text" class="form-control" name="con_name" id="con_name" />
													</td>
													<td>
														<input type="text" class="form-control" name="con_mobile" id="con_mobile" onkeypress="return isNumberKey(event)" />
													</td>
													<td>
														<input type="text" class="form-control" name="con_email" id="con_email" />
													</td>
													<td>
														<input type="button" class="btn btn-primary" value="ADD"  style="box-shadow: 3px 3px #61a642;" onclick="add_contact_person()" id="add_contact_bt" />
													</td>
														<input type="hidden" id="edit_id_contact" value=""  />
												</tr>
											</table>
										</div>
										<div class="col-md-12" id="table_contact_details"></div>
									</div>
								</div>
								<div class="clearfix"></div>
							</div>
						</div>
							<input type='hidden' name='mode' id='mode' value='<?=$mode?>' />
							<input type='hidden' name='eid' id='eid' value='<?=$rel['cust_id']?>' />
					</div>	
				</section>
			</div>
		</div>
	</div>
	<!--- Customer Form End -->
	<!--- Bank Form Start -->
	<div class="col-md-12 ledger_forms" id="bank_form">
		<div class="row">
			<div class="col-sm-12">
				<header class="panel-heading breadcrumb text-center back_head_color">
					<h3>Bank Details</h3>
				</header>	
				<section class="panel">
					<div class="row">
						<div class="col-md-12">
							<div class="col-md-4">
								<div class="form-group">
									<label class="col-md-4 control-label">Select Bank *</label>
									<div class="col-md-8 col-xs-11">
										 <select class="select2" id="bankid" name="bankid" title="Select Bank" required >
											<?=getbank($dbcon,0)?>
										 </select>
									</div>
								</div>
							</div>
							<div class="col-md-4">
								<div class="form-group">
									 <label class="col-md-4 control-label">Branch *</label>
									 <div class="col-md-8 col-xs-11">
										<input type="text"  class="form-control" id="branch_name" name="branch_name" placeholder="" required/>
									 </div>
								</div>
							</div>
							<div class="col-md-4">
								<div class="form-group">
									  <label class="col-md-4 control-label">Account Name *</label>
									  <div class="col-md-8 col-xs-11">
										<input type="text"  class="form-control" id="acc_name" name="acc_name"  required title="Enter Account Name" />
									  </div>
								</div>
							</div>
						</div>
						<div class="col-md-12 row_margin">
							<div class="col-md-4">
								<div class="form-group">
								  <label class="col-md-4 control-label">Account Number *</label>
								  <div class="col-md-8 col-xs-11">
									<input type="text"  class="form-control" id="acc_number" name="acc_number" placeholder="" required title="Enter Account Number" />
								  </div>
								</div>
							</div>
							<div class="col-md-4">
								<div class="form-group">
								   <label class="col-md-4 control-label">Cheque Series Starting Number </label>
								   <div class="col-md-8 col-xs-11">
										<input type="number"  class="form-control" id="acc_chequeno" name="acc_chequeno" placeholder=""  min="0" />
								   </div>
								</div>
							</div>
							<div class="col-md-4">
								<div class="form-group">
									<label class="col-md-4 control-label">Number of Cheques </label>
									<div class="col-md-8 col-xs-11">
										<input type="number"  class="form-control" id="acc_chequeleft" name="acc_chequeleft" placeholder="" min="0" max="1000"/>
									</div>
								</div>
							</div>
						</div>
						<div class="col-md-12 row_margin">
							
						</div>
					</div>
				</section>
			</div>
		</div>
	</div>
	<!--- Bank Form End -->
	<!--- Employee Form Start -->
	<div class="col-md-12 ledger_forms" id="emp_form">
		<div class="row">
			<div class="col-sm-12">
				<header class="panel-heading breadcrumb text-center back_head_color">
					<h3>Employee Details</h3>
				</header>	
				<section class="panel">
					<div class="row">
						<div class="col-md-12">
							<div class="col-md-4">
								<div class="form-group">
									<label class="col-md-4 control-label">Email(User name)</label>
									<div class="col-md-8 col-xs-11">
										<input type="text" class="form-control" placeholder="Email" title="Email" name="emp_email" id="emp_email" value="<?=$rel['emp_email']?>" onkeyup="checkUsername(this.value)" required />
										
										<input type="hidden" class="form-control" placeholder="Email" title="Email" name="" id="emp_email_hid" value="<?=$rel['emp_email']?>"   />
										
										<div id="user_error"></div>
									</div>	
								</div> 
							</div>
							<div class="col-md-4">
								<div class="form-group">
									<label class="col-md-4 control-label">Password</label>
									<div class="col-md-8 col-xs-11">
										<input type="text" class="form-control" placeholder="Password" title="Password" name="emp_password" id="emp_password" required  />
									</div>	
								</div> 
							</div>
							<div class="col-md-4">
								<div class="form-group">
									<label class="col-md-4 control-label">Mobile No. </label>
									<div class="col-md-8 col-xs-11">
										<input type="text" class="form-control" placeholder="Mobile No." name="emp_mobile" id="emp_mobile" value="<?=$rel['emp_mobile']?>" required  />
									</div>
								</div>
							</div>
						</div>
						<div class="col-md-12 margin_row">
							<div class="col-md-4" style="display:none;">
								<div class="form-group">
									<label class="col-md-4 control-label">Zone</label>
									<div class="col-md-8 col-xs-11">
										<select class="select2" name="emp_zone_id" id="emp_zone_id" onchange="get_branch_by_zone(this.value,'branch_id_emp')">
											<?=get_zone($dbcon,$rel['emp_zone_id'])?>				
										</select>
									</div>	
								</div>
							</div>
							<div class="col-md-4" style="display:none;">
								<div class="form-group">
									<label class="col-md-4 control-label">Branch</label>
									<div class="col-md-8 col-xs-11">
										<select class="select2" name="branch_id_emp" id="branch_id_emp">
															
										</select>
									</div>	
								</div>
							</div>
							<div class="col-md-4">
								<div class="form-group">
									<label class="col-md-4 control-label">Vehicle No</label>
									<div class="col-md-8 col-xs-11">
										<input type="text" class="form-control" placeholder="Vehicle No." name="vehicle_no" id="vehicle_no" value="<?=$rel['vehicle_no']?>"  />
									</div>	
								</div>
							</div>
							<div class="col-md-4">
								<div class="form-group">
									<label class="col-md-4 control-label">Type*</label>
									<div class="col-md-8 col-xs-11">
										<select class="select2" name="emp_user_type" id="emp_user_type" title="Select Type" required>
											<option value="">--Select User Type--</option>
											<?=getusertype($dbcon,$rel['emp_user_type']," and (usertype_id!=1 or company_id=".$_SESSION['company_id'].")")?>			
										</select>
									</div>	
								</div>
							</div>
						</div>
					</div>
				</section>
				<div class="col-md-12 col-md-offset-4 row_margin"></div>
			</div>
		</div>
	</div>
	<!--- Employee Form End -->
	<!--- Tax Form Start -->
	<div class="col-md-12 ledger_forms" id="tax_form">
		<div class="row">
			<div class="col-sm-12">
				<header class="panel-heading breadcrumb text-center back_head_color">
					<h3>Tax Details</h3>
				</header>	
				<section class="panel">
					<div class="row">
						<div class="col-md-12">
							<div class="col-md-6">
								<div class="form-group">
									<label class="col-md-3 control-label">Tax Value (in %)</label>
									<div class="col-md-6 col-xs-11">
										<input type="text"  name="tax_value"  id="tax_value" class="form-control"  placeholder="Tax Value(in %)" />
									</div>	
								</div> 
							</div>
						</div>
					</div>
				</section>
			</div>
		</div>
	</div>
	
	
	<!--- Tax Form End -->
	<section  class="panel">
		<div class="panel-body">
			<div class="row">
				<div class="col-md-12 row_margin">
					<header class="panel-heading breadcrumb text-center back_head_color">
						<h3>Opening Balance</h3>
					</header>	
					<div class="col-md-6">
						<div class="form-group">
							<label class="col-md-3 control-label">Opening Balance</label>
							<div class="col-md-8 col-xs-11">
								<input type="number"  class="form-control" id="opn_balance" name="opn_balance" placeholder="" min="0" max="" value="<?php echo $rel['opn_balance']; ?>" />
							</div>
						</div>
					</div>
					<div class="col-md-6">
						<div class="form-group">
							<label class="col-md-3 control-label">Balance Type</label>
							<div class="col-md-8 col-xs-11">
								<select class="select2" name="balance_typeid" id="balance_typeid" title="Select Type">
									<?=getbalance_type($dbcon,$rel['balance_typeid'])?>				
								</select>
							</div>
						</div>
					</div>
				</div>
				<div class="col-md-12 col-md-offset-5 row_margin" >
					<input type="hidden"  value="" id="form_type" name="form_type"  />
					<input type='hidden' name='mode' id='mode' value='<?php if($mode=='Edit') { echo "edit"; } else { echo "add"; } ?>' />
					<input type='hidden' name='ledger_id' id='ledger_id' value='<?php if($mode=='Edit') { echo $ledger_id; } else { echo "0"; } ?>' />				  
					<button type="submit" name="" id="btn_submit" class="btn btn-success">Submit</button>
					<a class="btn btn-danger" href="<?=ROOT.'ledger_list'?>">Cancel</a>
				</div>
			</div>
		</div>
	</section>
</div>
<!--state overview end-->
</section>
</section>

	</form>
<!--main content end-->
<!--footer start-->

<?php include_once('../include/add_zone.php');?>
<?php include_once('../include/add_city.php');?>
<?php include_once('../include/add_state.php');?>
<?php include_once('../include/footer.php');?>
<!--footer end-->
</section>

<!-- js placed at the end of the document so the pages load faster -->
<?php include_once('../include/include_js_file.php');?>   
<script src="<?=ROOT?>js/app/ledger.js?<?=time()?>"></script>
<script src="<?=ROOT?>js/app/customer.js?<?=time()?>"></script>
<script src="<?=ROOT?>js/app/state_mst.js?<?=time()?>"></script>
<script src="<?=ROOT?>js/app/city_mst.js?<?=time()?>"></script>
<script src="<?=ROOT?>js/app/zone_list.js?<?=time()?>"></script>
<script src="<?=ROOT?>js/app/bank_account.js"></script>
<script src="<?=ROOT?>js/app/expense_mst.js?<?=time()?>"></script>
<script src="<?=ROOT?>js/app/expense_head_mst.js?<?=time()?>"></script>
<script src="<?=ROOT?>js/app/income_mst.js?<?=time()?>"></script>
<script>
$(".select2").select2({
	width: '100%'
});
$('.default-date-picker').datepicker({
	format: 'dd-mm-yyyy',
	autoclose: true
});

function show_div_ledger(gid)
{
	//alert(gid);
	Loading();
	$.ajax({
		
		type:'post',
		url: root_domain+'app/ledger/',
		type: "POST",
		data: { mode : "get_open_form", gid : gid },
		success: function(response)
		{
			$("#customer_form").addClass("ledger_forms");
			$("#bank_form").addClass("ledger_forms");
			$("#expense_form").addClass("ledger_forms");
			$("#income_form").addClass("ledger_forms");
			$("#emp_form").addClass("ledger_forms");
			
			$('#'+response).removeClass("ledger_forms");
			$('#form_type').val(response);
		}
	});
	
	Unloading();
}
</script>
<?php
	if($mode=="Edit"){
	 echo "<script>load_state(".$countryid.",'stateid',".$stateid.")</script>";
		echo "<script>load_city(".$stateid.",'cityid',".$cityid.")</script>";
		echo "<script>show_div_ledger(".$rel['l_group'].",)</script>";
		echo "<script>get_branch_by_zone(".$rel['zone_id'].",'branch_id_customer',".$rel['branch_id_customer'].")</script>";
		echo "<script>get_branch_by_zone(".$rel['zone_id'].",'branch_id_emp',".$rel['branch_id_employee'].")</script>";
	}
	else{
		echo "<script>load_state(".$countryid.",'stateid',".$stateid.")</script>";
		echo "<script>load_city(".$stateid.",'cityid',".$cityid.")</script>";
	}
	?>
</body>
</html>
