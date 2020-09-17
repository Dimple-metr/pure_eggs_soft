<?php 
	session_start();
	include_once("../config/config.php");
	include_once("../include/common_functions.php");
	include_once("../config/session.php");
	$token = md5(rand(1000,9999));
	$_SESSION['token'] = $token;
	$form="Payment";
	if(empty($_SESSION['start']))
	{
		//$start=(date('m')<'04') ? date('01-04-Y',strtotime('-1 year')) : date('01-04-Y');
		$start=date('d-m-Y');
		$end=date("d-m-Y");
	}
	else
	{
		//$start=$_SESSION['start'];
		//$end=$_SESSION['end'];
		
		$start=date('d-m-Y');
		$end=date("d-m-Y");
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
			<?php 
				include_once('../include/equick_link.php');
			?>
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
							  <li ><a href="<?=ROOT.'payment_list'?>">Payment list</a></li>
							 
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
				<header class="panel-heading respadlr0">
					<div class='col-md-5'>
					<div class="form-group">
						<label class="control-label col-md-4">Choose Date</label>
						<div class="col-md-8">
							<div class="input-group date form_datetime-component">
								<input type="hidden" id="from_date"  value="<?=$start?>">
								<input type="hidden" id="to_date"  value="<?=$end?>">
								<input type="text" id="rep_date"  onChange="reload_data();;" class="form-control datepikerdemo" value="">
								<span class="input-group-btn">
									<button type="button" class="btn btn-danger date-set"><i class="fa fa-calendar"></i></button>
								</span>
							</div>
						</div>
					</div>
					</div>
					<div class="col-md-5" style="display:none;">
							<div class="form-group">
                                  <label class="control-label col-lg-4 col-md-4 col-xs-3">
									Payment Type
								  </label>
                                  <div class="col-md-6 col-xs-11 ">
										<select class="form-control" name="pay_status" id="pay_status"  onchange="load_datatable();">
											<option  value="">All</option>
											<option   value="1">CR</option>
											<option  value="2">DR</option>
										</select>
                                  </div>
							</div>
					</div>
				
					<span class="tools pull-right respadr_15">
						<a href="<?=ROOT.'payment_new'?>" ><button class="btn btn-success btn-flat" >Add <?=$form?></button></a>					
					</span>
				 <br/>
				</header>	
				<div class="panel-body">
				  <div class="adv-table">
				  <table  class="display table table-bordered table-striped" id="dynamic-table">
				  <thead>
				  <tr>
					  <th style="color:blue">Sr. No.</th>
					  <th style="color:blue">Payment No</th>
					  
					  <th style="color:blue">Party Name</th>
					  <th style="color:blue">Payment Mode</th>
					  <th style="color:blue">Paid Amount</th>
					  <th style="color:blue">Payment Type</th>
					  <th style="color:blue">Payment Date</th>
					  <th  style="color:blue" class="hidden-phone">Action</th>					  
				  </tr>
				  </thead>
				  <tbody>
				  </tbody>				 
				  </table>
				    <style>
				  @media screen and (max-width:992px){
					#dynamic-table td:before{
							
						}
					
					#dynamic-table td:nth-of-type(1):before { content: "Sr. NO.:"; }
					#dynamic-table td:nth-of-type(2):before { content: "Receipt No:"; }
					
					#dynamic-table td:nth-of-type(3):before { content: "Supplier Name:"; }
					#dynamic-table td:nth-of-type(4):before { content: "Payment Mode:"; }
					#dynamic-table td:nth-of-type(5):before { content: "Paid Amount:"; }
					#dynamic-table td:nth-of-type(6):before { content: "Payment Date:"; }
					#dynamic-table td:nth-of-type(7):before { content: "Action :"; }
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
   <script src="js/app/payment.js"></script>
    <!--<script src="js/count.js"></script>-->
<script>
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

</script>	

  </body>
</html>
