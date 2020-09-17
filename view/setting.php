<?php 
    session_start();
    include_once("../config/config.php");
    include_once("../config/session.php");
    include_once("../include/common_functions.php");
    if(strpos($_SERVER[REQUEST_URI], "setting")==false) {
        $mode="Add";
        $valid_till_start_date=date('1-m-Y');
        $valid_till_end_date=date("d-m-Y");
    }
    else {
        $mode="Edit";
        $eid=$dbcon->real_escape_string($_REQUEST['id']);
        $query="select * from tbl_company where company_id=$eid";
        $rel=mysqli_fetch_assoc($dbcon->query($query));
    }
?>
<!DOCTYPE html>
<html lang="en">
<head>
<?php include_once('../include/include_css_file.php');?>
</head>
<body>
<section id="container" >
    <?php include_once('../include/include_top_menu.php');?>
    <!--sidebar start-->
    <?php include_once('../include/left_menu.php');?>
    <!--sidebar end-->
    <!--main content start-->
    <section id="main-content">
        <section class="wrapper">
            <div class="row">
                <div class="col-lg-12">
                    <!--breadcrumbs start -->
                    <section class="panel">
                        <header class="panel-heading" style="padding-bottom: 20px;">
                            <h3><?=$mode?> Billing Content
                            </h3>
                        </header>

                        <div class="">
                            <ul class="breadcrumb no_padding">
                                    <li><a href="<?=ROOT.'dashboard'?>"><i class="fa fa-home"></i> Home</a></li>
                                    <li><a href="javascript:;">Setting</a></li>
                            </ul>
                        </div>
                    </section>
                    <!--breadcrumbs end -->
                </div>	
            </div>
            <!--state overview start-->
            <div class="row">			
                <div class="col-sm-12">
                    <section class="panel">
                        <header class="panel-heading">
					  Edit Billing Content
					</header>	
					<div class="panel-body ">
						<form class="form-horizontal" role="form" id="a_add" action="javascript:;" method="post" name="a_add">
							<div class="row">
							<div class="col-md-10">
							<div class="form-group">
							  <label class="col-md-3 control-label">Company Name *</label>
							  <div class="col-md-6 col-xs-11">
									<input type="text" class="form-control" placeholder="Company Name" name="company_name" id="company_name"  value="<?=$rel['company_name']?>" required title="Enter Company Name" /> 
								</div>
                             </div>						 
							 <div class="form-group">
								<label class="col-md-3 control-label">Address *</label>
								<div class="col-md-9 col-xs-11">
									<textarea id="address" name="address" class="form-control" rows="10"><?=stripslashes($rel['address'])?></textarea> 
								</div>
                             </div>
							 <div class="form-group">
								<label class="col-md-3 control-label">Logo Content</label>
								<div class="col-md-9 col-xs-11">
									<textarea id="logo_content" name="logo_content" class="form-control" rows="10"><?=stripslashes($rel['logo_content'])?></textarea> 
								</div>
                             </div>
							 <div class="form-group">
								<label class="col-md-3 control-label">State</label>
								<div class="col-md-6 col-xs-11">
									<select class="select2" name="stateid" id="stateid">
										<?=getstate($dbcon,$rel['stateid'])?>				
									</select>
								</div>
                             </div>
							
							<div class="form-group">
							  <label class="col-md-3 control-label">Mobile No.</label>
									<div class="col-md-6 col-xs-11">
									<input type="text" id="contact_no" name="contact_no" placeholder="Mobile No." class="form-control" value="<?=$rel['contact_no']?>" />
								</div>
                             </div>		
							 <div class="form-group">
							  <label class="col-md-3 control-label">Email</label>
									<div class="col-md-6 col-xs-11">
									<input type="text" id="website" name="website" placeholder="Email" class="form-control" value="<?=$rel['website']?>" />
								</div>
                             </div>	
							 <div class="form-group">
							  <label class="col-md-3 control-label">Website</label>
									<div class="col-md-6 col-xs-11">
									<input type="text" id="company_website" name="company_website" placeholder="Website" class="form-control" value="<?=$rel['company_website']?>" />
								</div>
                             </div>	
							<div class="form-group">
							  <label class="col-md-3 control-label">Head Logo</label>
							  <div class="col-md-6 col-xs-11">
							  	<input type="file" class="form-control" placeholder="Logo" name="logo" id="logo" accept="image/*" <? if($mode=="Add") { echo 'required';}?> title="logo" />
								
								</div>
								<div class="col-md-3 col-xs-11">
								<?
									if($mode=="Edit")
									{
										echo '<img src="'.ROOT.LOGO.$rel['logo'].'" style="width:120px"/>';
									}
								?>
								</div>
							</div>
							<div class="form-group">
							  <label class="col-md-3 control-label">Footer Logo</label>
								<div class="col-md-6 col-xs-11">
									<input type="file" class="form-control" placeholder="Logo" name="f_logo" id="f_logo" accept="image/*" <? if($mode=="Add") { echo 'required';}?> title="Footer Logo" />
								</div>
								<div class="col-md-3 col-xs-11">
									<?  if($mode=="Edit")
										{
											echo '<img src="'.ROOT.LOGO.$rel['f_logo'].'" style="width:120px"/>';
										}
									?>
								</div>
							</div>
				
							  <div class="form-group">
							  <label class="col-md-3 control-label">Bank Name</label>
							  <div class="col-md-6 col-xs-11">
									<input type="text" class="form-control" placeholder="Bank Name" name="bank_name" id="bank_name"  value="<?=$rel['bank_name']?>" />
								</div>
                             </div>
							 <div class="form-group">
							  <label class="col-md-3 control-label">A/c No</label>
							  <div class="col-md-6 col-xs-11">
									<input type="text" class="form-control" placeholder="A/c No" name="ac_no" id="ac_no"  value="<?=$rel['ac_no']?>" />
								</div>
                             </div>
							 <div class="form-group">
							  <label class="col-md-3 control-label">IFCS </label>
							  <div class="col-md-6 col-xs-11">
									<input type="text" class="form-control" placeholder="IFCS" name="ifcs" id="ifcs"  value="<?=$rel['ifcs']?>" />
								</div>
                             </div>
							 <div class="form-group">
							  <label class="col-md-3 control-label">Branch Name</label>
							  <div class="col-md-6 col-xs-11">
									<input type="text" class="form-control" placeholder="Branch Name" name="branch_name" id="branch_name"  value="<?=$rel['branch_name']?>" />
								</div>
                             </div>
							 <div class="form-group">
							  <label class="col-md-3 control-label">GSTIN</label>
							  <div class="col-md-6 col-xs-11">
									<input type="text" class="form-control" placeholder="GSTIN" name="gstno" id="gstno"  value="<?=$rel['vatno']?>" />
								</div>
                             </div>
							<div class="form-group">
							  <label class="col-md-3 control-label">IEC No</label>
							  <div class="col-md-6 col-xs-11">
									<input type="text" class="form-control" placeholder="IEC No" name="iec_no" id="iec_no"  value="<?=$rel['iec_no']?>" />
								</div>
                             </div>	
							
				<div class="form-group">
					<label class="col-md-3 control-label">Lut No.</label>
					<div class="col-md-6 col-xs-11">
						<input type="text" id="lut_no" name="lut_no" class="form-control" title="Lut No." placeholder="Lut No." value="<?=$rel['lut_no']?>" />
					</div>
				</div>	
			
			
				<!--<div class="form-group">
					<label class="col-md-3 control-label">Valid Till</label>
					<div class="col-md-6 col-xs-11">
						<div class="input-group date form_datetime-component">
							<input type="hidden" id="valid_till_start_date"  value="<?=$valid_till_start_date?>">
							<input type="hidden" id="valid_till_end_date"  value="<?=$valid_till_end_date?>">
							<input type="text" id="valid_till_date" name="valid_till_date" class="form-control datepikerdemo" value="">
							<span class="input-group-btn">
								<button type="button" class="btn btn-danger date-set"><i class="fa fa-calendar"></i></button>
							</span>
						</div>
					</div>	
				</div>-->
							 <div class="form-group">
							  <label class="col-md-3 control-label">PAN No</label>
							  <div class="col-md-6 col-xs-11">
									<input type="text" class="form-control" placeholder="PAN Card No." name="pan_no" id="pan_no"  value="<?=$rel['pan_no']?>" />
								</div>
                             </div> 
					<!--		 
							 <div class="form-group">
							  <label class="col-md-3 control-label">Quotation Subject</label>
							  <div class="col-md-6 col-xs-11">
									<textarea class="form-control" placeholder="Quotation Subject" name="quot_subject" id="quot_greeting" rows="3" ><?=$rel['quot_subject']?></textarea>
								</div>
                             </div>
							  <div class="form-group">
							  <label class="col-md-3 control-label">Lead Email Content</label>
							  <div class="col-md-9 col-xs-11">
									<textarea class="form-control" placeholder="Lead Email Content" name="lead_email_content" id="lead_email_content" ><?=$rel['lead_email_content']?></textarea>
								</div>
                             </div>
							<div class="form-group">
							  <label class="col-md-3 control-label">Inquiry Email Content</label>
							  <div class="col-md-9 col-xs-11">
									<textarea class="form-control" placeholder="Inquiry Email Content" name="inquiry_email_content" id="inquiry_email_content" ><?=$rel['inquiry_email_content']?></textarea>
								</div>
                             </div>
							<div class="form-group">
							  <label class="col-md-3 control-label">Quotation Email Content</label>
							  <div class="col-md-9 col-xs-11">
									<textarea class="form-control" placeholder="greeting" name="quot_email_content" id="quot_email_content" ><?=$rel['quot_content']?></textarea>
								</div>
                             </div>
							 <div class="form-group">
							  <label class="col-md-3 control-label">Coverlator Content</label>
							  <div class="col-md-9 col-xs-11">
									<textarea class="form-control" placeholder="greeting" name="coverlator_content" id="coverlator_content" ><?=$rel['coverlator_content']?></textarea>
								</div>
                             </div>
							 <div class="form-group">
							  <label class="col-md-3 control-label">Signature</label>
							  <div class="col-md-9 col-xs-11">
									<textarea class="form-control" placeholder="Signature" name="signature" id="signature" ><?=$rel['signature']?></textarea>
								</div>
                             </div>
						-->	 
							 <!--<div class="form-group">
							  <label class="col-md-3 control-label">VAT/TIN Date </label>
							  <div class="col-md-6 col-xs-11">
								<input id="vat_date" name="vat_date" type="text" class="form-control default-date-picker valid" title="Date" value="<?=$vat_date?>"  placeholder="Vat Date">
								</div>
                             </div>						
							<div class="form-group">
							  <label class="col-md-3 control-label">CST/TIN NO</label>
							  <div class="col-md-6 col-xs-11">
									<input type="text" class="form-control" placeholder="Cst No" name="cstno" id="cstno"   value="<?=$rel['cstno']?>" />
								</div>
                             </div>
							
							<div class="form-group">
							  <label class="col-md-3 control-label">CST/TIN Date </label>
							  <div class="col-md-6 col-xs-11">
								<input id="cst_date" name="cst_date" type="text" class="form-control default-date-picker valid" title="Date" value="<?=$cst_date?>"  placeholder="CST Date">
								</div>
                             </div>-->
							<!--<div class="form-group">
							  <label class="col-md-3 control-label">Ser.Tax NO</label>
							  <div class="col-md-6 col-xs-11">
									<input type="text" class="form-control" placeholder="Ser.Tax NO" name="serno" id="serno"  value="<?=$rel['serno']?>" />
								</div>
                             </div>
							 
							<div class="form-group">
							  <label class="col-md-3 control-label">Ser. Tax Date </label>
							  <div class="col-md-6 col-xs-11">
								<input id="ser_date" name="ser_date" type="text" class="form-control default-date-picker valid" title="Date" value="<?=$ser_date?>"  placeholder="Ser. Tax Date">
								</div>
                             </div>

							  <div class="form-group">
							  <label class="col-md-3 control-label">Pan Card No</label>
							  <div class="col-md-6 col-xs-11">
									<input type="text" class="form-control" placeholder="Pan Card No" name="pan_no" id="pan_no"  value="<?=$rel['pan_no']?>" />
								</div>
                             </div>-->
							<!--
							<div class="form-group">
							  <label class="col-md-3 control-label">Perfoma Invoice Condition Content</label>
							  <div class="col-md-9 col-xs-11">
									<textarea class="form-control" placeholder="Export Invoice Condition" name="export_condition" id="export_condition" ><?=$rel['perfoma_condition']?></textarea>
								</div>
                             </div>-->
							 <div class="form-group">
							  <label class="col-md-3 control-label">Invoice Condition Content</label>
							  <div class="col-md-9 col-xs-11">
									<textarea class="form-control" placeholder="Invoice Condition" name="condition" id="condition" ><?=$rel['conditions']?></textarea>
								</div>
                             </div>
                            <div class="form-group">
                                <label class="col-md-3 control-label">Invoice Tax Content</label>
                                <div class="col-md-9 col-xs-11">
                                        <input type="text" class="form-control" placeholder="Invoice Tax Content" name="invoice_tax_content" id="invoice_tax_content" value="<?=$rel['invoice_tax_content']?>" >
                                </div>
                             </div>
                            <div class="form-group">
                                <label class="col-md-3 control-label">Manage Inventory *</label>
                                <div class="col-md-6 col-xs-11">
                                    <select class="select2" name="inventory_management" id="inventory_management" required>
                                        <option value="">Select Option</option>
                                        <option value="1" <?=(($rel['inventory_management'] == 1)? "selected='selected'" : '')?>>Yes</option>
                                        <option value="0" <?=(($rel['inventory_management'] == 0)? "selected='selected'" : '')?>>No</option>
                                    </select>
                                </div>
                             </div>
							 <!--<div class="form-group">
							  <label class="col-md-3 control-label">Challan Condition Content</label>
							  <div class="col-md-9 col-xs-11">
									<textarea class="form-control" placeholder="Challan Condition" name="challan_condition" id="challan_condition" ><?=$rel['challan_condition']?></textarea>
								</div>
                             </div>
							 <div class="form-group">
							  <label class="col-md-3 control-label">PO Condition Content</label>
							  <div class="col-md-9 col-xs-11">
									<textarea class="form-control" placeholder="PO Condition" name="po_condition" id="po_condition" ><?=$rel['po_condition']?></textarea>
								</div>
                             </div>
							<div class="form-group">
							  <label class="col-md-3 control-label">Quotation Condition Content</label>
							  <div class="col-md-9 col-xs-11">
									<textarea class="form-control" placeholder="Quotation Condition" name="quot_condition" id="quot_condition" ><?=$rel['quot_condition']?></textarea>
								</div>
                             </div>
							 <div class="form-group">
							  <label class="col-md-3 control-label">Quotation Content</label>
							  <div class="col-md-9 col-xs-11">
									<textarea class="form-control" placeholder="greeting" name="quot_greeting" id="quot_greeting" ><?=$rel['quot_content']?></textarea>
								</div>
                             </div>
							--> 
							 <!--<div class="form-group">
							  <label class="col-md-3 control-label">Dispatch Heading Content</label>
							  <div class="col-md-9 col-xs-11">
									<textarea class="form-control" placeholder="Dispatch Heading Content" name="dispatch_head_content" id="dispatch_head_content" ><?=$rel['dispatch_head_content']?></textarea>
								</div>
                             </div>
							 <div class="form-group">
							  <label class="col-md-3 control-label">Dispatch Footer Content</label>
							  <div class="col-md-9 col-xs-11">
									<textarea class="form-control" placeholder="Dispatch Footer Content" name="dispatch_footer_content" id="dispatch_footer_content" ><?=$rel['dispatch_footer_content']?></textarea>
								</div>
                             </div>-->
							 <div class="col-md-3"></div>
							 <button type="submit" class="btn btn-success">Submit</button> &nbsp;
							<div class="col-md-3"></div>					 						 	
							</div>
						</div><!--Vendor row end-->	
							<input type='hidden' name='mode' id='mode' value='edit' />
							<input type='hidden' name='eid' id='eid' value='<?=$rel['company_id']?>' />				  
							
						  </form>
					</div>
				</section>
			</div>	
		  
		  <!--state overview end-->
          </section>
      </section>
      <!--main content end-->
      <!--footer start-->
	<?php include_once('../include/footer.php');?>
      <!--footer end-->
  </section>
    <!-- js placed at the end of the document so the pages load faster -->
	<?php include_once('../include/include_js_file.php');?>   
	<script src="<?=ROOT?>js/app/setting.js?<?=time()?>"></script>
<script>
$(".select2").select2({
	width: '100%'
});
$('.default-date-picker').datepicker({
	format: 'dd-mm-yyyy',
	autoclose: true
});
$(".form_datetime-meridian").datetimepicker({
	format: "dd-mm-yyyy HH:ii P",
	showMeridian: true,
	autoclose: true,
	todayBtn: true,
	pickerPosition: "bottom-left"
});
function cb(start, end) {
	$('.datepikerdemo span').html(start.format('MMMM D, YYYY') + ' - ' + end.format('MMMM D, YYYY'));
}
cb(moment().subtract(29, 'days'), moment());
 
$('.datepikerdemo').daterangepicker({       
	locale: {
		format: 'DD-MM-YYYY'
	},
	"autoApply": true,	
	"startDate": $('#valid_till_start_date').val(),
	"endDate": $('#valid_till_end_date').val(),	
	ranges: {
	   'Today': [moment(), moment()],
	   'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
	   'Last 7 Days': [moment().subtract(6, 'days'), moment()],
	   'Last 30 Days': [moment().subtract(29, 'days'), moment()],
	   'This Month': [moment().startOf('month'), moment().endOf('month')],
	   'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
	}
}, cb);
$('.date-set').click(function(){
	$('.datepikerdemo').trigger('click')
});
function trancate_tables(val)
{
	var r= confirm(" Are you want to Remove Data ?");
	if(r) {
		Loading(true);	
		window.location=root_domain+'backup/'+val;
	}
}
CKEDITOR.replace( 'address', {
	enterMode: CKEDITOR.ENTER_BR
});
CKEDITOR.replace( 'logo_content', {
	enterMode: CKEDITOR.ENTER_BR
});
CKEDITOR.replace( 'condition', {
	enterMode: CKEDITOR.ENTER_BR
});
/*CKEDITOR.replace( 'lead_email_content', {
	enterMode: CKEDITOR.ENTER_BR
});
CKEDITOR.replace( 'signature', {
	enterMode: CKEDITOR.ENTER_BR
});
CKEDITOR.replace('quot_email_content', {
	enterMode: CKEDITOR.ENTER_BR
});
CKEDITOR.replace( 'coverlator_content', {
	enterMode: CKEDITOR.ENTER_BR
});
/*CKEDITOR.replace( 'challan_condition', {
	enterMode: CKEDITOR.ENTER_BR
});
CKEDITOR.replace( 'quot_condition', {
	enterMode: CKEDITOR.ENTER_BR
});
CKEDITOR.replace( 'po_condition', {
	enterMode: CKEDITOR.ENTER_BR
});*/
/*CKEDITOR.replace( 'dispatch_head_content', {
	enterMode: CKEDITOR.ENTER_BR
});
CKEDITOR.replace('dispatch_footer_content', {
	enterMode: CKEDITOR.ENTER_BR
});*

CKEDITOR.replace('export_condition', {
	enterMode: CKEDITOR.ENTER_BR
});*/
</script>
</body>
</html>