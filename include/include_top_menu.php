<!--header start-->

	 <header class="header white-bg" style="min-height:70px;/*padding:0px 12px;*/">
              <div class="sidebar-toggle-box">
                  <div class="fa fa-bars tooltips" data-placement="right" data-original-title="Toggle Navigation"></div>
              </div>
            <!--logo start-->
            <a href="<?=ROOT.'dashboard'?>" class="logo hidden-phone"><span>Billing 360</span> </a>			
            <!--logo end-->   

<!-- Notification Tab Start -->
<!--
		<div class="nav notify-row" id='top_menu' style="margin-left:0px;">
			<ul class="nav top-menu">
			<li id="header_inbox_bar" style="float:left;margin-left:50px;" class="dropdown">
		    <?
			/*
			$todoqry='select * from todo_mst where status=0 and date >= CURDATE() and date <= DATE_ADD(CURDATE(),INTERVAL 3 DAY) and company_id='.$_SESSION['company_id'].' order by date ASC';
			 $result_todo=$dbcon->query($todoqry);
			 $notify=mysqli_num_rows($result_todo);
			
			?>
			<a data-toggle="dropdown" <? if($notify!='0'){?>id="pulsate-regular"<?}?> class="dropdown-toggle" href="#" aria-expanded="false">
				<i class="fa fa-bell-o"></i>
                <span class="badge bg-important"><?=$notify?></span>
            </a>
             <ul class="dropdown-menu extended inbox" style="min-width: 300px !important;">
             <div class="notify-arrow notify-arrow-red"></div>
                <li>
                 <p class="red">You have <?=$notify?> new notification</p>
				</li>
			<?	
			 if(mysqli_num_rows($result_todo)>0)
			 {
				  $i=1;
				  while($rel_todo=mysqli_fetch_assoc($result_todo))
				  {	
			?>
				<li>
                    <a href="javascript:;" onclick="change_top_status(<?=$rel_todo['todo_id']?>,1)" title="Click to Clear Notification">
							<span class="subject">
								<span class="from"><?=date('d-m-Y',strtotime($rel_todo['date']))?></span>
								<span class="time"></span>
                            </span>
                            <span class="message">
							<?=$rel_todo['task_detail']?>.
							</span>
                    </a>
                </li> 
			<?
					}
			 }*/
			?>
        
                 </ul>
            </li>
		
		</ul>
	</div>
	-->
<!-- Notification Tab End -->		

			
            <div class="top-nav ">
                <!--search & user info start-->
				<?php
					$setting='';
					$support='<li><a class="" href="'.ROOT.'support">
									<i class="fa fa-handshake-o "></i> Support</a>
								</li>';
					if($_SESSION['user_type']=="2") {
						$setting ='<li><a class="" href="'.ROOT.'setting/'.$_SESSION['company_id'].'">
									<i class="fa fa-cog"></i> Setting</a>
								</li>';
					}
			$top_lead_btn_view='';$top_inq_btn_view='';
			$top_led_btn_per=check_permission('lead_add',$_SESSION['user_type'],'edit',$dbcon);	
			if($top_led_btn_per){
				$top_lead_btn_view='
					<li style=" margin-top: 7px;">
						<button class="btn btn-round btn-primary tooltips" data-original-title="Create Lead" data-toggle="tooltip" data-placement="bottom" onclick="open_lead();"><i class="fa fa-plus"></i> <span class="hidden-phone">&nbsp;Lead</span></button>
                    </li>';
			}
			$top_inq_btn_per=check_permission('inquiry_add',$_SESSION['user_type'],'edit',$dbcon);	
			if($top_inq_btn_per){
				$top_inq_btn_view='
					<li style=" margin-top: 7px;">
						<button class="btn btn-round btn-success tooltips" data-original-title="Create Inquiry" data-toggle="tooltip" data-placement="bottom" onclick="open_inquiry();"><i class="fa fa-plus"></i> <span class="hidden-phone">&nbsp;Inquiry</span></button>
                    </li>';
			}
			
			if(!empty($_SESSION['company_name'])) {
                $com="select * from tbl_company where company_id=".$_SESSION['company_id'];
	            $comty=mysqli_fetch_assoc($dbcon->query($com));						
                 echo'				 
				 <ul class="nav pull-right top-menu">
				 	<li class=""><a class="tooltips" data-original-title="Change Company" data-toggle="tooltip" data-placement="bottom" style="margin-top: 5px;border: none !important;" href="javascript:;" onclick="change_company()"><i class="fa fa-sign-in"></i></a></li>';
					
				  echo'	<li class="hidden-phone"><a class="logo" style="margin-top: 5px;border: none !important;">'.date("d-m-Y").'</a></li>'.$top_lead_btn_view.$top_inq_btn_view.'
					 
                    <!-- user login dropdown start-->
					&nbsp;
                   <li class="dropdown">
                        <a data-toggle="dropdown" class="dropdown-toggle" href="#">
                            <img alt="" src="'.ROOT.'img/admin.jpg">
                            <span class="username hidden-phone">'.$_SESSION['user_name'].'</span>
                            <b class="caret"></b>
                        </a>
						    <ul role="menu" class="dropdown-menu ">
                                       '.$setting.$support.'
                                      <li><a class="" href="'.ROOT.'changepassword/'.$_SESSION['user_id'].'">
										 <i class="fa fa-user"></i>
										  <span style="font-size:14px">Change Password</span>
										</a>
										</li>
										<li><a class="" href="'.ROOT.'backup/2" target="_blank">
										<i class="fa fa-copy"></i> Database Backup</a>
										</li> 
                                      <li class="divider "></li>
                                      <li><a href="'.ROOT.'backup/5" style="background: #a9d96c;"><i class="fa fa-key"></i> Log Out</a></li>
                                  </ul>
                              </div>
                        <ul class="dropdown-menu extended logout">
                            <div class="log-arrow-up"></div>                            
                      		<li><a href="'.ROOT.'logout"><i class="fa fa-key"></i> Log Out</a></li>
                        	
						</ul>
                    </li>';}
					?>					
                    <!-- user login dropdown end -->
                </ul>
                <!--search & user info end-->
            </div>
        </header>
		    
      <!--header end-->
<script>
function paymentremander(id)
{
	Loading(true);		
	
	$.ajax({
		type: "POST",
		url: root_domain+'app/dashboard/',
		data: { mode : "paymentremainder", invoiceid :id},
		success: function(response)
		{
			console.log(response);
			$('#ModalPaymentRemainder').modal();
			var obj = jQuery.parseJSON(response);
			if(response != "") {				
				$("#ModalPaymentRemainder").modal("show");
				$("#cust_name").html(obj.company_name);
				$("#cust_address").html(obj.cust_address);
				$("#city").html(obj.city);
				$("#mobile").html(obj.cust_mobile);
				$("#email").html(obj.cust_email);
				$("#ex_date").html(obj.ex_date);
				$("#exciseinvoice_date").html(obj.exciseinvoice_date);
				$("#exciseinvoice_no").html(obj.exciseinvoice_no);
				$("#message").html(obj.message);
			}
			Unloading();
	
		}
	});	
}
function open_inquiry() {
	window.location=root_domain+'inquiry_add';
}
function open_lead() {
	window.location=root_domain+'lead_add';
}
function open_purchase() {
	window.location=root_domain+'purchase_add';
}	   </script>