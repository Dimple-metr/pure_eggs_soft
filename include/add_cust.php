<div class="modal colored-header info " id="bs-example-modal-lg" role="dialog" data-keyboard="false" data-backdrop="static">
	<div class="modal-dialog modal-lg">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="btn_close close md-close" data-dismiss="modal" aria-hidden="true">Close &times;</button>
				<h3>Add Party</h3>
			</div>
			<div class="modal-body form">
				<div class="row">
					
					<div class="col-md-12">
						
						<form class="form-horizontal" role="form" id="cust_add" action="javascript:;" method="post" name="cust_add">
							
								
							<div class="col-md-12">
								<div class="form-group">
									<label class="col-md-12 control-label" style="text-align:left;line-height:25px">Company Name *</label>
									<div class="col-md-12 col-xs-11">
										<input type="text" class="form-control" placeholder="Company Name" name="company_name" id="company_name"  value="<?=$rel['company_name']?>"/>
									</div>
								</div>
							</div>	 
						<div class="clearfix"></div>
							<div class="col-md-4">
								<div class="form-group">
									<label class="col-md-12 control-label" style="text-align:left;line-height:25px">Select Country *</label>
									<div class="col-md-12 col-xs-11">
										<select class="select2" name="countryid" id="countryid" onChange="load_state(this.value,'stateid','')">
											<?=get_country($dbcon,$countryid)?>
										</select>
									</div>
								</div>	
							</div>
							<div class="col-md-4">
								<div class="form-group">
									<label class="col-md-12 control-label" style="text-align:left;line-height:25px">Select State *</label>
									<div class="col-md-10 col-xs-11">
										<select class="select2" name="stateid" id="stateid" onChange="load_city(this.value,'cityid','')">
											<option value="">Select State</option>			
										</select>
									</div>
									<input type="button" name="addState" id="addState" data-toggle="modal" data-target=""  onclick="add_state()" class="btn btn-primary" value="+"/>
								</div>	
							</div>
							<div class="col-md-4">
								<div class="form-group">
									<label class="col-md-12 control-label" style="text-align:left;line-height:25px">Select City *</label>
									<div class="col-md-10 col-xs-11">
										<select class="select2" name="cityid" id="cityid">
											<option value="">Select City</option>	
										</select>
									</div>
									<input type="button"  name="addCity" id="addCity" data-toggle="modal" data-target=""  onclick="add_city();" class="btn btn-primary" value="+"/>
									
								</div>	
							</div>
						<div class="clearfix"></div>
							<div class="col-md-4">
								<div class="form-group">
									<label class="col-md-12 control-label" style="text-align:left;line-height:25px">Contact Person Name </label>
									<div class="col-md-12 col-xs-11">
										<input type="text" class="form-control" placeholder="Contact Person Name" name="cust_name" id="cust_name"  value="<?=$rel['cust_name']?>"/>
									</div>
								</div>	
							</div> 
							<div class="col-md-4">
								<div class="form-group">
									<label class="col-md-12 control-label" style="text-align:left;line-height:25px">Mobile no </label>
									<div class="col-md-12 col-xs-11">
										<input type="text" class="form-control" placeholder="Customer Mobile" name="cust_mobile" id="cust_mobile" value="<?=$rel['cust_mobile']?>"  />
										
									</div>	
								</div>
							</div>
							<div class="col-md-4">
								<div class="form-group">
									<label class="col-md-12 control-label" style="text-align:left;line-height:25px">Email </label>
									<div class="col-md-12 col-xs-11">
										<input type="text" class="form-control" placeholder="Email" name="cust_email" id="cust_email"   value="<?=$rel['cust_email']?>"  />
									</div>	
								</div>
							</div>
							
							<div class="clearfix"></div>
							<!--<div class="col-md-4">
								<div class="form-group">
									<label class="col-md-12 control-label" style="text-align:left;line-height:25px">Pan No</label>
									<div class="col-md-12 col-xs-11">
										<input type="text" class="form-control" placeholder="Pan No" name="pan_no" id="pan_no"  />
									</div>
								</div>
							</div>-->
							
							
							<div class="form-group col-md-4">
								<div class="checkbox">
									<label class="col-md-offset-1">
										<input type="checkbox" id="multi_company" name="multi_company" <?=($mode=="Add"?'checked':($rel['multi_company']=="1"?'checked':''))?> value="1">  View in all Company
									</label>
								</div>
							</div>
							
						</div>
						<div class="col-md-4"></div>
						<button type="submit" class="btn btn-success">Submit</button> &nbsp;
						<button type="button"  class="btn_close btn btn-danger" data-dismiss="modal" aria-hidden="true">Close</button>
					</div>
					
					<!--Vendor row end-->	
					<input type='hidden' name='cust_mode' id='cust_mode' value='Add' />
					<input type='hidden' name='cust_model' id='cust_model' value='cust_model' />				  
					
				</form>
			</div>
		</div>	
	</div>
</div><!-- /.modal-content -->
</div><!-- /.modal-dialog -->
</div>
