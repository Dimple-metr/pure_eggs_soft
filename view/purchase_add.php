<?php 

	session_start();
	include_once("../config/config.php");
	include_once("../config/session.php");
	
	include_once("../include/common_functions.php");
	$token = md5(rand(1000,9999));
	$_SESSION['token'] = $token;
	$form="Purchase";
	$countryid='101';
	$stateid='1';
	$cityid='1';
	
	if(strpos($_SERVER[REQUEST_URI], "purchaseedit")==false)
	{
		$mode="Add";
		$date=date('d-m-Y');
		$order_date='';
                $query="select l_id from  tbl_ledger where l_group=24 and company_id=".$_SESSION['company_id'];
		$rel=mysqli_fetch_assoc($dbcon->query($query));	
		$purchase_ledger=$rel['l_id'];
	}
	else
	{
		$mode="Edit";
		$poid=$dbcon->real_escape_string($_REQUEST['id']);
		$query="select * from  tbl_pono where po_id=$poid";
		$rel=mysqli_fetch_assoc($dbcon->query($query));	
		$order_date='';
		if($rel['order_date']!="1970-01-01" && $rel['order_date']!="0000-00-00")
		{
			$order_date=date('d-m-Y',strtotime($rel['order_date']));
		}
		$purchase_ledger=$rel['purchase_ledger_id'];
	}
	$set="select * from tbl_company where company_id=".$_SESSION['company_id'];
		$set_head=mysqli_fetch_assoc($dbcon->query($set));	
?>

<!DOCTYPE html>
<html lang="en">
<head>
<?php include_once('../include/include_css_file.php');?>
</head>
<body>
  <section id="container" class="sidebar-closed">
      <?php include_once('../include/include_top_menu.php');?>
      <!--sidebar start-->
      <?php include_once('../include/left_menu.php');?>
      <!--sidebar end-->
      <!--main content start-->
           <section id="main-content">
          <section class="wrapper">
			<? //include_once('../include/equick_link.php');?>
    		<div class="row">
			  <div class="col-lg-12">
				  <!--breadcrumbs start -->
					<section class="panel">
						<header class="panel-heading">
						  <h3><?=$mode.' '.$form?></h3>
						</header>	
							<div class="">
								<ul class="breadcrumb">
								  <li><a href="<?=ROOT.'dashboard'?>"><i class="fa fa-home"></i> Home</a></li>
								  <li><a href="<?=ROOT.'purchase_list'?>"><?=$form?> List</a></li>
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
					  New <?=$form?>
					</header>	
				<div class="panel-body">
				<form class="form-horizontal" role="form" id="po_add" action="javascript:;" method="post" name="po_add">
						<div class="row">
					 
					 <div class="col-md-12">
						<div class="col-md-6 col-xs-12">
							<label class="col-md-3 control-label" style="">Purchase Ledger*</label>
							<div class="col-md-6 col-xs-10 resclear" >
								<select class="select2" name="purchase_ledger_id" id="purchase_ledger_id" required title="Select Purchase Ledger">
									<?=get_ledger($dbcon,$purchase_ledger);?>
								</select>
							</div>
						</div>
						<div class="col-md-6 col-xs-12">
								<label class="col-md-4 control-label" style="">Select Vendor </label>
								<div class="col-md-6 col-xs-10 resclear" >
								<select class="select2" name="vender_id" id="vender_id" required title="Select Vender" >
									<!--<option value="">Choose Vendor</option>-->
									<?//=getcust($dbcon,$rel['vender_id']);?>	
									<?=get_ledger_cust($dbcon,$rel['vender_id']);?>
								</select>
								</div>
								<!--<input type="button"  name="addcust" id="addcust" onclick="add_customer_purchase();"  class="btn btn-primary" value="+"/>-->
								<div class="col-md-2  col-xs-1" style="padding-left:6px;display:none;"><input type="button"  name="addcust" id="addcust" data-toggle="modal" data-target="#bs-example-modal-lg"  class="btn btn-primary" value="Add Vendor"/></div>
						</div>
						
						
	    			</div>		
					<div style="clear: both;"></div>
				<div class="col-md-12 respclear"  style="margin-top:10px;">
						<div class="col-md-6">
							<div class="form-group">
							  <label class="col-md-4 control-label">Purchase Bill No </label>
							  <div class="col-md-6 col-xs-11">
								<input id="po_no" name="po_no" type="text" class="form-control" title="Date" value="<?=$rel['po_no']?>" placeholder="Purchase No" >
								</div>
                             </div>	
                        </div>	
						<div class="col-md-6">  	
							 <div class="form-group">  	
							  <label class="col-md-3 control-label" >Purchase Bill date </label>
							  <div class="col-md-5 col-xs-11">
								<input id="po_date" name="po_date" type="text" class="form-control default-date-picker" title="Date" value="<? if($mode=='Add'){ echo $date;}else if($mode=='Edit'){ echo date('d-m-Y',strtotime($rel['po_date']));}?>" placeholder="Purchase Date">
								</div>
                             </div>	
                        </div>	
					</div>		 
					<div class="col-md-12" style="display:none;" >
						<div class="col-md-6">
							<div class="form-group">  	
							  <label class="col-md-4 control-label" >Order No</label>
							  <div class="col-md-6 col-xs-11">
								<input type="text" id="order_no" name="order_no" class="form-control" title="Order No" value="<?=$rel['order_no']?>" placeholder="Order No">
								</div>
                             </div>	
                        </div>
						<div class="col-md-6">
							<div class="form-group">  	
							  <label class="col-md-4 control-label" >Order Date</label>
							  <div class="col-md-3 col-xs-11">
								<input id="order_date" name="order_date" type="text" class="form-control default-date-picker" title="Date" value="<?=$order_date?>" placeholder="Order Date">
							  </div>
                             </div>	
                        </div>	
					</div>		 	
					<div class="col-md-12" style="display:none;">
					<div class="col-md-6">
						<div class="form-group" id="purchase_order_div" style="display:none;">
							<label class="col-md-4 control-label">Choose Purchase Order</label>
							<div class="col-md-6 col-xs-11">
								<select class="select2" name="purchaseorder_id" id="purchaseorder_id" onChange="load_purhcase_order_data(this.value)" >
									<option value="">Choose Purchase Order</option>	
								</select>
							</div>
						</div>	</div>	
						<? if($mode=="Add") {?>
												<div class="col-md-6"  style="display:none;">
													<div class="form-group">
														<label class="col-md-4 control-label" style="white-space:nowrap;">Payment Reminder</label>
														<div class="col-md-6 col-xs-12">
															<input type="number" id="payment_reminder"  name="payment_reminder" class="form-control" title="Payment Notification"  value="<?=$rel['payment_reminder']?>" placeholder=" in Days">
														</div>
													</div>	
												</div>
										   <? } ?>
					</div>	
			<div class="col-md-12">
							 				 	
				<div class="form-group">
					<div class="col-md-12 col-xs-12">
						<table cellspacing="10" style=" border-spacing:10px;" class="display table  table-striped table12 table-bordered" id="product_list">
						<tr id="field" >
							<th width="20%" class="text-center">
								<div class="col-md-10">Product Detail</div>
								<div class="col-md-1">
									<input type="button" style="display:none;" name="addproduct" id="addproduct1" data-toggle="modal" title="Add Product" data-target="#bs-example-modal-addproduct" class="btn btn-xs btn-primary" value="+"/>
								</div>
							</th>
							<th width="8%" class="text-center">HSN Code</th>
							<th width="6%" class="text-center">Quantity</th>
							<th width="6%" class="text-center">Rate</th>
							<th width="6%" class="text-center">Per</th>
							<th width="6%">Discount</th>
							<th width="9%">Taxable Value</th>
							<th width="15%">Tax</th>
							<th width="9%" class="text-center">Amount</th>
							<th width="5%" class="text-center"></th>
						</tr>
					<input type="hidden" value="1" name="fieldcnt" id="fieldcnt"/>
					<tr id="field1">
						<td data-label="Product Detail" style="vertical-align:top;">
							<select class="select2"  title="Select product" name="product_id" id="product_id" onChange="load_productdetail(this.value)">
								<?=getproduct($dbcon,0,'0,1,3')?>
							</select>
							<br><br>
							<textarea id="product_des" name="product_des" class="form-control" ></textarea>
						</td>	
						<td data-label="HSN Code"  style="vertical-align:top;">
							<input type="text"  title="Enter HSN Code" placeholder="HSN Code" id="product_hsn_code" name="product_hsn_code" class="form-control"/>
						</td>
						<td data-label="Qty" style="vertical-align:top;">
							<input type="number"  title="Enter Qty" min="0" id="product_qty" name="product_qty"  class="form-control" onkeyup="get_amount();get_discount('per');"/>
						</td>
						<td data-label="Rate" style="vertical-align:top;">
							<input type="number"  title="Enter Rate" placeholder="Rate" min="0" id="product_rate" name="product_rate" onkeyup="get_amount();get_discount('per');" class="form-control"/><br/>
							<button type="button" title="Show Previous Rate History" name="rate_history" id="rate_history" onclick="load_rate_hist()" style="display:none;" class="btn btn-info"><i class="fa fa-eye"></i> show</button>
						</td>
						<td data-label="Per" style="vertical-align:top;">
							<select class="select2"  name="unitid" id="unitid"  title="Select Unit">
								<?=getunit($dbcon,0);?>
							</select>
						</td>
						<td data-label="Discount" style="vertical-align:top;">
							<input type="number" title="Enter Discount" min="0" id="product_discount" name="product_discount" onkeyup="get_discount('amt');" class="form-control" placeholder="in Rs."/><br/>
							<input type="number"  title="Enter Discount Percentage" min="0" id="discount_per" name="discount_per" onkeyup="get_discount('per');" class="form-control" placeholder="in %" max="100"/>
						</td>
						<td data-label="Taxable Value" style="vertical-align:top;">
							<input type="number" title="Taxable Value" placeholder="Taxable Value" min="0" id="taxable_value" name="taxable_value" class="form-control" readonly />
						</td>
						<td data-label="Tax"  style="vertical-align:top;">
							<select class="form-control" name="formulaid" id="formulaid" onChange="get_amount();">
									<?=getformula($dbcon,$rel['formulaid']);?>
							</select>
						</td>
						<td data-label="Amount"  style="vertical-align:top;"> 
							<input type="number"  min="0" id="product_amount" readonly="readonly" name="product_amount" class="form-control"/>
						</td>
						<td data-label=""  style="vertical-align:top;"> 
							<input type="button"  name="addrow" id="addrow" onClick="return add_field();"  class="btn btn-primary" value="Add"/>
						</td>
						<input type='hidden' name='edit_id' id='edit_id' value='' />
					
					   </tr>
					</table>
						</div>
                             </div>
							 
							 
	<div id="sale_productdata">
				<?if($mode=="Edit"){
					
					$query="select potrancation_id,product_hsn_code,product.product_name,cat.unit_name,product.product_name,mst.description,mst.*,product_qty,product_rate,product_disc,product_amount from  tbl_potrancation as mst left join unit_mst as cat on cat.unitid=mst.unit_id left join tbl_product as product on product.product_id=mst.product_id  where potrancation_status=0 and po_id=".$rel['po_id'];
					$result=$dbcon->query($query);
			
				?>
				<div class="form-group">
					  <div class="col-md-12 col-xs-12">
				<table cellspacing="10" style="border-spacing:10px;" class="table12 display table table-bordered table-striped">
				<tr id="field">
						<th class="text-center" width="25%">Product Name</th>
						<th class="text-center" width="8%">HSN Code</th>
						<th class="text-center" width="8%">Qty</th>
						<!--<th class="text-center" width="8%">Sqr/Ft</th>-->
						<th class="text-center" width="10%">Rate</th>
						<th class="text-center" width="6%">Per</th>
						
						<th class="text-center" width="8%">Discount</th>
					
						<th class="text-center" width="10%">Taxable value</th>
						<th class="text-center" width="15%">Tax</th>
						<th class="text-center" width="12%">Amount</th>
						<th class="text-center" width="10%">Action</th>
				</tr>
			<?$i=1;
			while($rel_trn=mysqli_fetch_assoc($result))
			{?>
				<tr>
					<td data-label="Product Name" style="vertical-align:top;text-align:left">
						<?=$rel_trn['product_name']?>
						<?=(!empty($rel_trn['description'])?'<br/><strong>Desc.</strong> :'.$rel_trn['description']:'')?>
					</td>
					<td data-label="HSN Code" style="vertical-align:top;" class="text-center">
						<?
							if(empty($rel_trn['product_hsn_code'])){
								echo '-';
							}else{
								echo $rel_trn['product_hsn_code'];
							}
						?>
					</td>
					<td data-label="Qty" style="vertical-align:top;" class="text-center">
						<?=$rel_trn['product_qty']?>
					</td>
                    <!--<td style="vertical-align:top;" class="text-center">
						<?=$rel_trn['sqr_ft']?>
					</td>	-->				
					<td data-label="Rate" style="vertical-align:top;" class="text-right">
						<?=$rel_trn['product_rate']?>
					</td>				
					<td data-label="Per"  style="vertical-align:top" class="text-center">
						<?
							if(empty($rel_trn['unit_name'])){
								echo '-';
							}else{
								echo $rel_trn['unit_name'];
							}
						?>
					</td>
					
					<td data-label="Discount" style="vertical-align:top" class="text-left">
						<?=$rel_trn['product_discount'].'('.$rel_trn['discount_per'].'%)';?>
					</td>
				
					<td data-label="Taxable value" style="vertical-align:top" class="text-right">
						<?=$rel_trn['product_amount']?>
					</td>
					<td data-label="Tax" style="vertical-align:top" class="text-left">
						<?
						if(empty($rel_trn['formulaid'])){
							echo '-';
						}else{
							echo (empty($rel_trn['tax_name1']) ? " " : $rel_trn['tax_name1'] .' : '. $rel_trn['tax_amount1']).'<br/>';
							echo (empty($rel_trn['tax_name2']) ? " " : $rel_trn['tax_name2'] .' : '. $rel_trn['tax_amount2']).'<br/>';
							echo (empty($rel_trn['tax_name3']) ? " " : $rel_trn['tax_name3'] .' : '. $rel_trn['tax_amount3']).'<br/>';
						}
						?>
						
					</td>
					<td data-label="Amount" style="vertical-align:top" class="text-right">
						<?=$rel_trn['total']?>
					</td>
					<input type="hidden" name="amount[]" id="amount<?=$i?>" value="<?=$rel_trn['total']?>"/>
											
					 <td  data-label="Action"  style="vertical-align:top">
							<button type="button" class="btn btn-round btn-warning btn-xs" onclick="edit_data(<?=$rel_trn['potrancation_id']?>,'tbl_potrancation','potrancation_id');"  ><i class="fa fa-pencil"></i></button>
							<button type="button" class="btn btn-round btn-danger btn-xs" onclick="delete_data(<?=$rel_trn['potrancation_id']?>,'tbl_potrancation','potrancation_id');" ><i class="fa fa-times"></i></button>
					</td>	
			</tr>
			<?
			$i++;
			}?>
			</table>
			</div>
                           
							</div>	
			<?}?>
							 </div>	
					 <div class="col-md-6">
							
							<div class="form-group">
							  <label class="col-md-3 control-label">Remarks </label>
									<div class="col-md-9 col-xs-11">
									<textarea id="remark" name="remark" placeholder="Remarks" class="form-control" rows="3"><?=$rel['remark']?></textarea> 
								</div>
                             </div> 
					</div>
					 <div class="col-md-6">
							<div class="form-group">
								<label class="col-md-6 control-label">Total *</label>
								<div class="col-md-4 col-xs-11">
									<input id="total" name="total" type="text" readonly="readonly" class="form-control" title="dispatch_no" max="0"  value="<? if($mode=="Add"){echo '0';}else if($mode=='Edit'){ echo $e_total;}?>" placeholder="total">
					
								</div>
							</div>	
							<div class="form-group">
								<label class="col-md-6 control-label">Transport charges </label>
								<div class="col-md-4 col-xs-11">
								<input id="paking" name="paking" type="number"  min="0"  class="form-control" title="Transport" value="<? if($mode=="Add"){echo 0;}else if($mode="Edit"){echo $rel['packing'];}?>" onKeyUp="get_amount();" placeholder="Transport">
					
								</div>
							</div>	
							<!-- 
							<div class="form-group">
								<label class="col-md-6 control-label">Select Formula</label>
								<div class="col-md-4 col-xs-11">
								<select class="form-control" name="formulaid" id="formulaid" onChange="get_gtotal(this.value);">
									<?
									echo getformula($dbcon,$rel['formulaid']);
									 ?>
								</select>
								</div>
							</div>							
							<div class="col-md-12">
							<div id="showformulatextbox">
							<?
							if($mode=='Edit')
							{
							if(!empty($rel['tax1_name']))
							{
							?>
							<div class="form-group">
								<label class="col-md-6 control-label" ><?=$rel['tax1_name']?></label>
								<div class="col-md-4 col-xs-11" style="padding-right:5px;">
								<input id="taxvalue0" name="taxvalue0" value= "<?=$rel['taxvalue1']?>"type="text" class="form-control" readonly="readonly">
						</div>
					</div>
					<input id="taxname0" name="taxname0" value= "<?=$rel['tax1_name']?>" type="hidden" class="form-control">
							<?
							}
							if(!empty($rel['tax2_name']))
							{
							?>
							<div class="form-group">
								<label class="col-md-6 control-label" ><?=$rel['tax2_name']?></label>
								<div class="col-md-4 col-xs-11" style="padding-right:5px;">
								<input id="taxvalue1" name="taxvalue1" value= "<?=$rel['taxvalue2']?>"type="text" class="form-control" readonly="readonly">
						</div>
					</div>
					<input id="taxname1" name="taxname1" value= "<?=$rel['tax2_name']?>" type="hidden" class="form-control">
							<?
							}if(!empty($rel['tax3_name']))
							{
							?>
							<div class="form-group">
								<label class="col-md-6 control-label" ><?=$rel['tax3_name']?></label>
								<div class="col-md-4 col-xs-11" style="padding-right:5px;">
								<input id="taxvalue2" name="taxvalue2" value= "<?=$rel['taxvalue3']?>"type="text" class="form-control" readonly="readonly">
						</div>
					</div>
					<input id="taxname2" name="taxname2" value= "<?=$rel['tax3_name']?>" type="hidden" class="form-control">
							<?
							}} 
							?>
							</div>
							</div>
							-->
							<div class="form-group">
								<label class="col-md-6 control-label">Round Off</label>
								<div class="col-md-4 col-xs-11">
								<input id="round_off" name="round_off" type="number" class="form-control" title="Round Off" value="<? if($mode=="Add"){echo 0;}else if($mode="Edit"){echo $rel['round_off'];}?>" onKeyUp="get_amount();" placeholder="Round Off">
					
								</div>
							</div>
							
							<div class="form-group">
								<label class="col-md-6 control-label">Grand Total *</label>
								<div class="col-md-4 col-xs-11">
								
								<input id="g_total" name="g_total" type="text"  class="form-control" title="total" value="<? if($mode=="Add"){echo '0';}else if($mode=='Edit'){ echo $rel['g_total'];}?>" placeholder="total"readonly="readonly">
							<input id="total" name="total" type="hidden" value="<? if($mode=="Add"){echo '0';}else if($mode=='Edit'){ echo $e_total;} ?>" placeholder="total"readonly="readonly">
							
								</div>
							</div>
							
							</div>	
					</div>
						<div class="col-md-12 text-center">	<button type="submit" class="btn btn-success" id="save" name="save">Submit</button>
							
							<a href="<?=ROOT.'purchase_list'?>" type="button" class="btn btn-danger">Cancel</a></div>	<div class="col-md-3"></div>					</div>
							<!--Vendor row end-->	
							<input type='hidden' name='mode' id='mode' value='<?=$mode?>' />
							<input type='hidden' name='eid' id='eid' value='<?=$rel['po_id']?>' />
							<input type='hidden' name='token' id='token' value='<?php echo $token; ?>' />				  
							
						  </form>
</div>
						
					</section>
			 </div>
		 </div>		
			 
			  <!--state overview end-->
          </section>
      </section>
      <!--main content end-->
      <!--footer start-->
	  <?php include_once('../include/add_cust.php');?>
	<?php include_once('../include/add_vender.php'); ?>
	<?php include_once('../include/add_product.php'); ?>
	<?php include_once('../include/include_show_purchase_history.php'); ?>
	<?php include_once('../include/add_city.php');?>
	<?php include_once('../include/add_state.php');?>
	<?php include_once('../include/footer.php');?>
      <!--footer end-->
  </section>
	<!-- js placed at the end of the document so the pages load faster -->
	<?php include_once('../include/include_js_file.php');?>   
	<script src="<?=ROOT?>js/app/purchase.js"></script>
	<!-- <script src="<?=ROOT?>js/app/customer.js"></script>
	<script src="<?=ROOT?>js/app/vender.js"></script>
	<script src="<?=ROOT?>js/app/product_mst.js"></script>
	<script src="<?=ROOT?>js/app/state_mst.js"></script>
	<script src="<?=ROOT?>js/app/city_mst.js"></script>-->

<!--<script src="js/count.js"></script>-->
<script>
//$('#container').addClass('sidebar-closed');
$(".select2").select2({
		width: '100%'
	});
	$('.default-date-picker').datepicker({
            format: 'dd-mm-yyyy',
            autoclose: true
        });
function paymentmode(id)
{
	if(id=="2")
	{	
		$('#cheque_dtl').val('');
		$('#cheque_data').show();
	}
	else
		$('#cheque_data').hide();
}
$(".form_datetime").datetimepicker({
    format: 'dd-mm-yyyy hh:ii',
    autoclose: true,
    todayBtn: true,
    pickerPosition: "bottom-left"

});
function add_customer_purchase()
{
	$("#bs-example-modal-lg").modal("show");
	$("#cat_id").val('1');
}
</script>
<?
//echo "<script>load_state(".$countryid.",'stateid',".$stateid.")</script>";
//echo "<script>load_city(".$stateid.",'cityid',".$cityid.")</script>";
	echo "<script>show_data() </script>";
?>
  </body>
</html>
