<?php 

	session_start();
	include_once("../config/config.php");
	include_once("../config/session.php");
	include_once("../include/common_functions.php");
	$form="Profit And Loss";
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
	$start = date('01-04-Y');
    $end = date("d-m-Y");	
	
?>

<!DOCTYPE html>
<html lang="en">
	<head>
		<?php include_once('../include/include_css_file.php');?>
	</head>
	<body>
		<section id="container">
			<?php include_once('../include/include_top_menu.php');?>
			<?php include_once('../include/left_menu.php');?>
			<section id="main-content">
				<section class="wrapper">
					<div class="row">
						<div class="col-lg-12">
							<section class="panel">
								<header class="panel-heading"><h3><?=$mode.' '.$form?></h3></header>	
								<div class="">
									<ul class="breadcrumb">
										<li><a href="<?=ROOT.'dashboard'?>"><i class="fa fa-home"></i> Home</a></li>
										<li><a href="#"><?=$form?> Report</a></li>
									</ul>
								</div>
							</section>
						</div>	
					</div>
					<div class="row">			
                                            <div class="col-sm-12">
                                                <section class="panel">
                                                    <header class="panel-heading"><?=$form?> Report</header>	
                                                        <div class="panel-body">
                                                            <form class="form-horizontal" role="form" id="po_add" action="javascript:;" method="post" name="po_add">
                                                                <div class="row">
                                                                    <div class="col-md-12"  style="margin-top:10px;">
                                                                        <div class='col-lg-5 col-md-7 col-xs-12 '>
                                                                            <div class="form-group">
                                                                                <label class="control-label col-lg-4 col-md-4 col-xs-4 respad-l0">Choose Date</label>
                                                                                <div class=" col-lg-8 col-md-8 col-xs-8 respad-r0">
                                                                                    <div class="input-group date form_datetime-component">
                                                                                        <input type="hidden" id="from_date"  value="<?=$start?>">
                                                                                        <input type="hidden" id="to_date"  value="<?=$end?>">
                                                                                        <input type="text" id="rep_date"  onChange="reload_data();" class="form-control datepikerdemo" value="">
                                                                                        <span class="input-group-btn">
                                                                                            <button type="button" class="btn btn-danger date-set"><i class="fa fa-calendar"></i></button>
                                                                                        </span>
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    </div>
											<div class="col-md-12">
												<div id="profitloss_report_id" ></div>
											</div>
										</div>
									</form>
								</div>
							</section>
						</div>
					</div>		
				</section>
			</section>
				<?php include_once('../include/footer.php');?>
		</section>
			<?php include_once('../include/include_js_file.php');?>  
	<!--<script src="//cdnjs.cloudflare.com/ajax/libs/mousetrap/1.4.6/mousetrap.min.js"></script>
    <script src="//cdnjs.cloudflare.com/ajax/libs/mousetrap/1.4.6/mousetrap-bind-dictionary.min.js"></script>	-->		
			<script src="<?=ROOT?>js/app/profit_loss_report.js"></script>
			<script>
				//$('#container').addClass('sidebar-closed');
				$(".select2").select2({
                                    width: '100%'
                                });
                                $('.default-date-picker').datepicker({
                                                format: 'dd-mm-yyyy',
                                                autoclose: true
                                        });
				$(".form_datetime").datetimepicker({
					format: 'dd-mm-yyyy hh:ii',
					autoclose: true,
					todayBtn: true,
					pickerPosition: "bottom-left"

				});
			</script>
			<script>
					$(document).ready(function() {
				 Loading(true);	

				  //load_value();
				 
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
 
			</script>
	</body>
</html>
