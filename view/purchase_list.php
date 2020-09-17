<?php 
	session_start();
	include_once("../config/config.php");
	include_once("../include/common_functions.php");
	include_once("../config/session.php");
	$token = md5(rand(1000,9999));
	$_SESSION['token'] = $token;
	$form="Purchase";
	if(empty($_SESSION['start']))
	{
		$start = date('1-m-Y');
		$end = date("d-m-Y");
	}
	else
	{
		$start = $_SESSION['start'];
		$end = $_SESSION['end'];
	}
	
?>

<!DOCTYPE html>
<html lang="en">
<head>
<?php include_once('../include/include_css_file.php');?>
<style>
.icons{
    width: 14.5%;
    float: left;
    margin: 30px 7px 25px;
    text-align: center;
	position:relative;

}
.icons12{
background-color:#fff;
padding-top:15px;
    border: 8px;
}
 .icons p{
 text-align:center;
 font-size:15px;
 font-weight:600;
 padding-top:5px;
 font-color:white
 
 }
 
 .icon1 fa{

 }
 .icon1.success{background-color: #5cb85c;}
 .icon1.primary{background-color: #0275d8;}
 .icon1.warning{background-color: #f0ad4e;}
 .icon1.info{background-color: #5bc0de;}
 .icon1.danger{background-color: #d9534f;}
 .icon1.terques{background-color: #6ccac9;}
 .icon1.yellow{background-color: #f8d347;}
 .icon1.pink{background-color:#E5649A;}
 .icon1.mustard{background-color:#F0BD23;}
 .icon1.success,.icon1.primary,.icon1.warning,.icon1.danger,.icon1.info,.icon1.terques,.icon1.yellow,.icon1.pink,.icon1.mustard{
    width: 120px;
    height:100px;
    border-radius: 8px;
	text-align:center;
	margin:0 auto
 }
 .icon1.success i,.icon1.primary i,.icon1.warning i,.icon1.danger i,.icon1.info i,.icon1.terques i,.icon1.yellow i,.icon1.pink i,.icon1.mustard i{
 text-align:center;
 color:#fff;
     padding-top: 27%;
	font-size: 37px;
 }
 @media (max-width:767px){
.icons {
    width: 47%;
    float: left;
    margin: 30px 4px 25px;
	position:relative;
}

}
@media (min-width:768px) and (max-width:980px)
 {
 .icons12{
background-color:#fff;
padding-top:20px;
padding-bottom:20px;
   border-radius: 8px;
}
 .icons {
    width: 17%;
    float: left;
    margin: 30px 4px 25px;
    text-align: center;
    position: relative;
}

 }
.icons .badge {
    position: absolute;
    right: 25px;
    top: 0px;
    z-index: 100;
}
</style>
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
			
			<? //include_once('../include/equick_link.php');?>
     		<div class="row">
			  <div class="col-lg-12">
				  <!--breadcrumbs start -->
				  <section class="panel">
					  <header class="panel-heading">
						  <h3><?=$mode.' '.$form?> List</h3>
						</header>	
						<div class="">
						  <ul class="breadcrumb">
							  <li><a href="<?=ROOT.'dashboard'?>"><i class="fa fa-home"></i> Home</a></li>
							  <li ><a href="<?=ROOT.'purchase_list'?>"><?=$form?> list</a></li>
							 
						  </ul>
						 </div>
					</section>
				  <!--breadcrumbs end -->
			  </div>	
             </div>
		 
				  <!--breadcrumbs start -->
			<section class="panel">
			<div class="row">
			  <div class="col-lg-12 centeral-align">
				  <div class="icons">
			
				<div class="icon1 success" >
				<p style="color:white;padding-top:10px;">Total Purchase Amount</p>
					<h3 style="font-size:20px;color:white;padding-top:5px;">Rs.<span id="tpurchaseamount" style="font-size:20px;color:white;"></span> </h3>
				</div>
				
			</div>
				<div class="icons">	 	
				<div class="icon1 info" >
					
						<p style="color:white;padding-top:10px;">Total Purchase<br> Taxable Value</p>
					
						<h3 style="font-size:20px;color:white;padding-top:5px;">Rs.<span id="tpurchasetax" style="font-size:20px;color:white"></span></h3>
				
				</div>
				</div>
			<div class="icons">	 	
				<div class="icon1 danger" >
					
						<p style="color:white;padding-top:10px;">Total Purchase Payment</p>
					
						<h3 style="font-size:20px;color:white;padding-top:5px;">Rs.<span id="tpaidamount" style="font-size:20px;color:white"></span></h3>
				
				</div>
				</div>
				<div class="icons">	 	
				<div class="icon1 warning" >
					
						<p style="color:white;padding-top:10px;">Total Purchase Outstanding</p>
					
						<h3 style="font-size:20px;color:white;padding-top:5px;">Rs.<span id="toutstanding" style="font-size:20px;color:white"></span></h3>
				
				</div>
				</div>
				</div>	
             </div>
					</section>
				  <!--breadcrumbs end -->
	
			 
			 
              <!--state overview start-->
		  <div class="row">			
			<div class="col-sm-12">
				<section class="panel">
				  <header class="panel-heading respadlr0">
				  <div class='col-lg-5 col-md-7 col-xs-12 '>
					<div class="form-group">
                                  <label class="control-label col-lg-4 col-md-4 col-xs-12 respad-l0">Choose Date</label>
                                  <div class=" col-lg-8 col-md-8 col-xs-12 respad-r0">
                                       <div class="input-group date form_datetime-component">
									<?
									  //$start=(date('m')<'04') ? date('01-04-Y',strtotime('-1 year')) : date('01-04-Y');
									?>
                                        <input type="hidden" id="from_date"  value="<?=$start?>">
										 <input type="hidden" id="to_date"  value="<?=$end?>">
         					 		        <input type="text" id="rep_date"  onChange="reload_data();load_value();" class="form-control datepikerdemo" value="">
											<span class="input-group-btn">
											<button type="button" class="btn btn-danger date-set"><i class="fa fa-calendar"></i></button>
											</span>
                                      </div>
                                  </div>
                              </div>
						</div>	
						<!--<div class="col-md-5">
								<div class="col-md-3">
								<div class='external-event label label-primary ui-draggable' style='position: relative;'>All</div>							<input id="report" name="report"  type="radio" checked="checked" onClick="reload_data();" class="" title="All" value="all">
								</div>
								<div class="col-md-3">
								<div class='external-event label label-success ui-draggable' style='position: relative;'>Paid</div>							<input id="report" name="report" onClick="reload_data();" type="radio" class="" title="paid" value="paid">
								</div>
								<div class="col-md-3">
								<div class='external-event label label-warning ui-draggable' style='position: relative;'>DUE</div>
								<input id="report" name="report"  onClick="reload_data();" type="radio" class="" title="due" value="due">
								</div>
								
						</div>-->
				   <span class="tools pull-right respadr_15">
					<a href="<?=ROOT.'purchase_add'?>" ><button class="btn btn-success btn-flat" >Create <?=$form?></button></a>					
				 </span>
				 
					</header>	
					 <div class="panel-body">
					 
				  <div class="adv-table">
				  <table  class="display table table-bordered table-striped" id="purchase-table">
				  <thead>
					<tr>
						<th>Purchase Bill No</th>
						<th>Purchase Bill Date</th>
						<th>Vendor Name</th>
						<th>City </th>
						<th>Grand Total</th>
						<th class="hidden-phone">Action</th>					  
					</tr>
				  </thead>
				  <tbody>
				  </tbody>				 
				  </table>
				    <style>
				  @media screen and (max-width:992px){
					#purchase-table td:before{
							color:red
						}
					#purchase-table td:nth-of-type(1):before { content: "Purchase Bill No:"; }
					#purchase-table td:nth-of-type(2):before { content: "Purchase Bill Date:"; }
					#purchase-table td:nth-of-type(3):before { content: "Vendor Name:"; }
					#purchase-table td:nth-of-type(4):before { content: "City:"; }
					#purchase-table td:nth-of-type(5):before { content: "Grand Total:"; }
					#purchase-table td:nth-of-type(6):before { content: "Action:"; }
					
				}
				  </style>
				  </div>
				  </div>
					</section>
				</div>
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
   <script src="<?=ROOT?>js/app/purchase.js?<?=time()?>"></script>
    <!--<script src="js/count.js"></script>-->
	<script>
	$(document).ready(function() {
 Loading(true);	

  load_value();
 
Unloading();
});
	$('.default-date-picker').datepicker({
            format: 'dd-mm-yyyy',
            autoclose: true
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
		"startDate": $('#from_date').val(),
		"endDate": $('#to_date').val(),	
	    ranges: {
          'Today': [moment(), moment()],
          // 'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
           //'Last 7 Days': [moment().subtract(6, 'days'), moment()],
           //'Last 30 Days': [moment().subtract(29, 'days'), moment()],
           'This Month': [moment().startOf('month'), moment().endOf('month')],
		   'Last 3 Month': [moment().subtract(3, 'months'), moment().endOf('month')],
		   'Last 6 Month': [moment().subtract(6, 'months'), moment().endOf('month')],
		   'Last 1 Year': [moment().subtract(12, 'months'), moment().endOf('month')],
           'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
        }
    }, cb);
$('.date-set').click(function(){
       $('.datepikerdemo').trigger('click')
});
 
 function load_value()
 {
	 		Loading();
	var date=$("#rep_date").val();

	
	$.ajax({
		type: "POST",
		url: root_domain+'app/purchase/',
		data: { mode : "load_val",date :  date},
		success: function(response)
		{
			console.log(response);
			var resp=jQuery.parseJSON(response);
			if(response != "") {
				
				$('#tpurchaseamount').html(resp.g_total);
				$('#tpurchasetax').html(resp.taxable_amt);
				$('#tpaidamount').html(resp.total_paid_amount);
				$('#toutstanding').html(resp.g_total-resp.total_paid_amount);
				Unloading();
			}
										
		}
	});
 }
</script>

  </body>
</html>
