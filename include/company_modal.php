<div class="modal colored-header info " id="company_modal" role="dialog" data-keyboard="false" data-backdrop="static">
	<div class="modal-dialog custom-width">
		<div class="modal-content">
			<div class="modal-header row" style="padding: 15px 15px 7px 15px;margin: 0 0px;">
			<div class="col-sm-6" style="padding:0px;"> 
			<h3 style="margin:0; padding:0">Billing360</h3>
			<h4 style="margin:0; padding:0">Company</h4></div>
			<div class=" col-sm-6 text-right" style="padding:0px;">
			<!--<a class="tools" href="<?=ROOT.'create_company'?>" ><button class="btn btn-success btn-flat" style="margin-right:10px"><i class="fa fa-plus"></i> Company</button></a>-->
			<?
			 if(!empty($_SESSION['company_id']) || $_SESSION['company_id']=="0")
			{?>
			<button type="button"  class="btn btn-danger close btn-flat closemain" data-dismiss="modal" aria-hidden="true">Close</button>
			<?}?>
			</div>
				
				
			</div>
			<div class="modal-body form" style="clear:both">
			<div class="row">

				 <div class="col-md-12">
					<table class="display table table-bordered table-striped">
						<tr>
							<td class="comptd"><label class="comp" onclick="pass_session('Admin',0)">Admin</label></td>
						</tr>
						
						<?
						$q='';
						if(!empty($_SESSION['company_id']))
						{
							$q=' and company_id!='.$_SESSION['company_id'];
						}
						$qry='Select company_name,company_id from tbl_company  where com_status=0'.$q;
						$result=$dbcon->query($qry);
						if(mysqli_num_rows($result)>0)
						{
							while($row=mysqli_fetch_array($result))
							{
							?>
							<tr>
								<td class="comptd"><label class="comp" onclick="pass_session('<?=$row['company_name']?>',<?=$row['company_id']?>)"><?=$row['company_name']?></label></td>
							</tr>
							<?
							}
						}
						else
						{
							if(strtolower(end(explode("/",$_SERVER['REQUEST_URI'])))!="create_company" && empty($_SESSION))
							{
						//		echo '<script>create_com()</script>';
							}
							else
							{
								echo '<script>open_company_modal(2)</script>';
							}
						}
						?>
					</table>
						
					</div>
			 
				</div>
			</div>	
			</div>
		</div><!-- /.modal-content -->
	</div><!-- /.modal-dialog -->
</div>
