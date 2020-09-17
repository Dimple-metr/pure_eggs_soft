<div class="modal colored-header info" id="alloc_sold_pro_modal" role="dialog" data-keyboard="false" data-backdrop="static">
<div class="modal-dialog modal-lg" id="custom_sold_modal">
	<div class="modal-content">
		<div class="modal-header">
			<button type="button" class="close md-close" data-dismiss="modal" aria-hidden="true">Close &times;</button>
			<h3>Choose Sale Products</h3>				
		</div>
		<div class="modal-body form">
			<form id="allocate_sold_product_form" name="allocate_sold_product_form" role="form" method="post" novalidate>	
				<div class="form-group"> 
					<table cellspacing="10" style="border-spacing:10px;" class="display table table-bordered table-striped">
						<tr id="field">
							
							<th class="text-center" width="20%">Product Detail</th> 
							<th class="text-center" width="10%">Invoice Date</th> 
							<th class="text-center" width="5%"></th>
						</tr>
						<tr>
							
							<td style="vertical-align:top;">
								<select class="select2" title="Select Product" name="product_id" id="product_id">
									<?=get_product($dbcon,'','0')?>
								</select>
							</td> 
							
							<td style="vertical-align:top;">
								<input id="sold_inv_foc_date" name="sold_inv_foc_date" type="text" class="form-control default-date-picker required valid" title="Date" value="" placeholder="Invoice Date">
							</td> 
							<td style="vertical-align:top;"> 
								<input type="button" name="addcustrow" id="addcustrow" onClick="return add_sold_pro_field();"  class="btn btn-primary" value="Add"/>	
							</td>
							<input type='hidden' name='edit_id1' id='edit_id1' value='' />
						</tr> 
					</table>				
				</div>
				<div class="col-md-12"></div>
				
				<!--<div id="trn_res"></div>-->
				<div class="panel-body">
					<div class="adv-table">
						<table class="display table table-bordered table-striped" id="sold-pro-table">
							<thead>
								<tr>
									<!--<th>Sr. NO.</th>-->
									<th>Product Name</th> 
									<th>FOC Date</th> 
									<th class="hidden-phone">Action</th>					  
								</tr>
							</thead>
							<tbody>
							</tbody>				 
						</table>
					</div>
				</div>
				
			</div>
			<div class="modal-footer" style="margin-top:25px;">
				<input type="hidden" name="alloc_cust_id" id="alloc_cust_id" value="" /> 
				<!--<button type="button" class="btn btn-default btn-flat md-close" data-dismiss="modal">Close</button>--> 
			</div>
		</form>
	</div><!-- /.modal-content -->
</div><!-- /.modal-dialog -->
</div><!-- /.modal -->