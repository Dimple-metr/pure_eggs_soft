<?php 
	session_start();
	include_once("../config/config.php");
	include_once("../config/session.php");
	include_once("../include/common_functions.php");
	$form="Product";
	$infopage = pathinfo( __FILE__ );
	$_SESSION['page']=$infopage['filename'];
	//echo $_SESSION['company_id'];
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
			<section id="main-content" >
				<section class="wrapper">
					<div class="row">
						<div class="col-lg-12">
							<section class="panel">
								<header class="panel-heading">
									<h3>New <?=$form?></h3>
								</header>	
								<div class="">
									<ul class="breadcrumb">
										<li><a href="<?=ROOT.'dashboard'?>"><i class="fa fa-home"></i> Home</a></li>
										<li class="active"><?=$form?> List</li>
									</ul>
									
								</div>
								<div class="panel-body">
									<form role="form" id="product_add" action="javascript:;" method="post" name="product_add">
										<div class="col-md-12">
											<div class="col-xs-1 col-md-4"></div>
												<div class="col-xs-5 col-md-2">
													<input id="ember1142" type="radio" value="goods" name="proType" class="ember-view" onchange="showtype(this.value);" checked />
													<label class="lable_radio" for="ember1142">&nbsp;&nbsp;Goods </label>
												</div>
												<div class="col-xs-5 col-md-2">
													<input id="ember1143" type="radio" value="service" name="proType"  class="ember-view" onclick="showtype(this.value);" />
													<label class="lable_radio" for="ember1143">&nbsp;&nbsp;Service </label>
												</div>
										</div>
										<div class="col-md-12 " style="margin-bottom:10px;">
											<div class="col-md-4 typepro">
												<div class="col-xs-1 col-md-4"><strong>Product Type *</strong></div>
												<div class="col-xs-8" style="">
													<input id="product_type_both" type="radio" value="0" name="product_type"  checked />
													<label class="with-gap" for="product_type_both" title="Use for Sale and Purchase" style="font-size:13px">&nbsp;Both </label>
													<input id="product_type_purchase" type="radio" value="1" name="product_type"  />
													<label class="lable_radio" for="product_type_purchase" title="Use for Purchase only" style="font-size:13px">&nbsp;Purchase </label>
													<input id="product_type_sale" type="radio" value="2" name="product_type"   />
													<label class="lable_radio" for="product_type_sale" title="Use for Sale only" style="font-size:13px;">&nbsp;Sale </label>
												</div>
											</div>
											<div class="col-md-4 typeled" style="display:none;">
												<div class="form-group">
													<div class="col-md-4" style="white-space:nowrap;"><strong>Select Ledger*</strong></div>
													<div class="col-md-8">
														<select class="select2" name="ledger_id" id="ledger_id" required title="Select Ledger">
															<?=get_ledger($dbcon,''," and l_group in (16,17)");?>
														</select>
													</div>
												</div>
											</div>
										</div>
										<div class="col-md-12" >
											<div class="col-md-4" style="height: 70px;">
												<div class="form-group">
													<div class="col-md-4" style="white-space:nowrap;"><strong>Product Name*</strong></div>
													<div class="col-md-8">
														<input type="text"  class="form-control" id="product_name" name="product_name" placeholder="Product Name" />
													</div>
												</div>
											</div>
											<div class="col-md-4" style="height: 70px;">
												<div class="form-group">
													<div class="col-md-4" style="white-space:nowrap;"><strong>Description</strong></div>
													<div class="col-md-8">
														 <textarea class="form-control" id="productdes" name="productdes" placeholder="Enter Product Description"></textarea>
													</div>
												</div>
											</div>
											<div class="col-md-4" style="height: 70px;">
												<div class="form-group">
													<div class="col-md-4" style="white-space:nowrap;"><strong>Bar Code</strong></div>
													<div class="col-md-8">
														  <input type="text" class="form-control" id="item_code" name="item_code" placeholder="Item Code" />
													</div>
												</div>
											</div>
										</div>
										<div class="col-md-12" >
											<div class="col-md-4" >
												<div class="form-group" style="height: 35px;" >
													<div class="col-md-4" style="white-space:nowrap;"><strong>HSN Code</strong></div>
													<div class="col-md-8">
														 <input type="text" class="form-control" id="product_code" name="product_code" placeholder="HSN Code" />
													</div>
												</div>
											</div>
											<div class="col-md-4 typepro" >
												<div class="form-group" style="height: 35px;">
													<div class="col-md-4" style="white-space:nowrap;"><strong>Sale Rate</strong></div>
													<div class="col-md-8">
														 <input type="number" min="0" class="form-control" id="product_mst_rate" name="product_mst_rate" placeholder="Product Sale Rate" />
													</div>
												</div>
											</div>
											<div class="col-md-4 typepro" >
												<div class="form-group" style="height: 35px;">
													<div class="col-md-4" style="white-space:nowrap;"><strong>Purchase Rate</strong></div>
													<div class="col-md-8">
														  <input type="number" min="0" class="form-control" id="product_purchase_mst_rate" name="product_purchase_mst_rate" placeholder="Product Purchase Rate" />
													</div>
												</div>
											</div>
											<div class="col-md-4" >
												<div class="form-group" style="height: 35px;">
													<div class="col-md-4" style="white-space:nowrap;"><strong>Product Unit</strong></div>
													<div class="col-md-8">
														  <select class="select2" name="product_mst_unitid" id="product_mst_unitid"  title="Select Unit">
																<?=getunit($dbcon,0);?>
															</select>
													</div>
												</div>
											</div>
											<div class="col-md-4" >
												<div class="form-group" style="height: 35px;">
													<div class="col-md-4" style="white-space:nowrap;"><strong>Intra Tax<br/>(CGST+SGST)</strong></div>
													<div class="col-md-8">
														 <select class="form-control" name="intra_tax" id="intra_tax">
															<?=getformula($dbcon,'');?>
														</select>
													</div>
												</div>
											</div>
											<div class="col-md-4" >
												<div class="form-group" style="height: 35px;">
													<div class="col-md-4" style="white-space:nowrap;"><strong>Inter Tax<br/>(IGST)</strong></div>
													<div class="col-md-8">
														 <select class="form-control" name="inter_tax" id="inter_tax">
															<?=getformula($dbcon,'');?>
														</select>
													</div>
												</div>
											</div>
											<div class="col-md-4 typepro" >
												<div class="form-group" style="height: 35px;">
													<div class="col-md-4" style="white-space:nowrap;"><strong>Opening Stock</strong></div>
													<div class="col-md-8">
														 <input type="number" name="opening_stock" min="0" id="opening_stock" class="form-control" placeholder="Opening Stock" />
													</div>
												</div>
											</div>
											<div class="col-md-4 typepro"  >
												<div class="form-group" style="height: 35px;">
													<div class="col-md-4" style="white-space:nowrap;"><strong>Minimum Stock</strong></div>
													<div class="col-md-8">
														 <input type="number" name="minimum_stock" min="0" id="minimum_stock" class="form-control" placeholder="Minimum Stock" />
													</div>
												</div>
											</div>
											<div class="col-md-4 typepro"  >
												<div class="form-group" style="height: 35px;">
													<div class="col-md-4" style="white-space:nowrap;"><strong>Catagory</strong></div>
													<div class="col-md-8">
														  <select class="select2" name="catagory_id" id="catagory_id"  title="Select catagory">
																<?=getcat($dbcon,0);?>
															</select>
													</div>
												</div>
											</div>
										</div>
										<div class="col-md-12">
											<div class="col-md-4 typepro" >
												<div class="form-group" style="height: 35px;">
													<div class="col-md-4" style="white-space:nowrap;"><strong>MRP</strong></div>
													<div class="col-md-8">
														 <input type="number" name="mrp" min="0" id="mrp" class="form-control" placeholder="Product MRP" />
													</div>
												</div>
											</div>
										</div>
										<div class="col-md-12" style="">
											<div class="col-md-5"></div>
											<div class="col-md-2">
												<input type="hidden" id="edit_id" name="edit_id" value="" />
												<input type="hidden" name="mode" id="mode" value="Add" /> 
												<button type="submit" class="btn btn-info">Submit</button>
											</div>
										</div>
									</form>
								</div>
								
							</section>
						</div>	
					</div>
					<div class="row">
						
						<div class="col-lg-12">
							<section class="panel">
								<header class="panel-heading">
								Product List
									<span class="tools pull-right">
										<a href="javascript:;" class="fa fa-chevron-down"></a>
									</span>
								</header>
								<div class="panel-body">
									<div class="adv-table">
										<table  class="display table table-bordered table-striped" id="unit-table">
											<thead>
												<tr>
													<th>Sr. NO.</th>
													<th>Product Name</th>
													<th>Bar Code</th>
													<th>HSN Code</th>
													<th>Purchase Rate</th>
													<th>Sale Rate</th>
													<th>MRP</th>
													<th>Minimum Stock</th>
													<th>Opening Stock</th>
													<th class="hidden-phone">Action</th>
												</tr>
											</thead>
											<tbody>
											</tbody>
										</table>
									</div>
								</div>
							</section>
						</div>
					</div>
				</section>
			</section>
			<?php include_once('../include/footer.php');?>
		</section>
		<?php include_once('../include/include_js_file.php');?>   
		<script src="<?=ROOT?>js/app/product_mst.js"></script>
		<script>
			$(".select2").select2({
				width: '100%'
			});
		</script>
	</body>
</html>
