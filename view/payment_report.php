<?php 

	session_start();
	include_once("../config/config.php");
	include_once("../config/session.php");
	include_once("../include/common_functions.php");
	$form="Employee Product Stock";
	if(empty($_SESSION['start']))
	{
		$start = date('d-m-Y');
		$end = date("d-m-Y");
	}
	else
	{
		$start = $_SESSION['start'];
		$end = $_SESSION['end'];
	}
		
	$stock_date=date('d-m-Y');
?>

<!DOCTYPE html>
<html lang="en">
	<head>
		<?php include_once('../include/include_css_file1.php');?>
		<style>
			.stop{
				border-top:1px #101010 solid;
			}
			.sbottom{
				border-bottom:1px #101010 solid;
			}
			.sleft{
				border-left:1px #101010 solid;
			}
			.sright{
				border-right:1px #101010 solid;
			}
			.userc{
				background-color: #b9b9b9;
			}
			.titc{
				background-color: #dcd5d5;
				font-size: 18px;
			}
		</style>
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
								<header class="panel-heading"><h3><?=$mode.' '.$form?></h3>
								</header>								
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
								<header class="panel-heading"><?=$form?> Report
									<span class="tools pull-right">
										<a href="javascript:;" onClick="tableToExcel('profitloss_report_id', 'Instalment Collection')" ><button class="btn btn-success btn-flat" >Export Excel</button></a>	
									</span>
									<span class="tools pull-right">
										<button class="btn btn-warning btn-flat" onClick="PrintMe('profitloss_report_id');" style="margin-right:20px;"><i class="fa fa-print"></i> Print Report</button>											
									</span>	
								</header>	
								<div class="panel-body">
									<form class="form-horizontal" role="form" id="po_add" action="javascript:;" method="post" name="po_add">
										<div class="row">
											<div class="col-md-12"  style="margin-top:10px;" >
												<div class='col-md-4'>
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
												<div class='col-md-4'>
													<div class="form-group">
														<label class="control-label col-lg-4 col-md-4 col-xs-4 respad-l0" style="white-space: nowrap;">Payment Mode</label>
														<div class=" col-lg-8 col-md-8 col-xs-8 respad-r0">
															<select class="form-control" name="paymentmodeid" id="paymentmodeid" onChange="reload_data();" title="Select Payment Mode">
																<? if($_SESSION['user_type']==2){ ?>
																	<?=get_ledger_bank($dbcon);?>
																<?}else{ ?>
																<?=get_ledger_cash($dbcon,'',1);?>
																<? } ?>
															</select>
														</div>
													</div>
												</div>
												<? if($_SESSION['user_type']==2){ ?>
												<div class='col-md-4'>
													<div class="form-group">
														<label class="control-label col-lg-4 col-md-4 col-xs-4 respad-l0" style="white-space: nowrap;">Employee</label>
														<div class=" col-lg-8 col-md-8 col-xs-8 respad-r0">
															<select class="select2" name="employee_id" id="employee_id" title="Select Employee" onchange="reload_data();">
																<?=get_user_in($dbcon,$employee_id)?>
															</select>
														</div>
													</div>
												</div>
												<? }else{ ?>
													<input type="hidden" name="employee_id" id="employee_id" value="<?=$_SESSION['user_id']?>" />
												<? } ?>
											</div>
											<div class="col-md-12" style="overflow-x: auto;width:100%;min-height: 70px;">
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
			<script src="<?=ROOT?>js/app/payment_report.js"></script>
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
                        function PrintMe(DivID) {
			$('#logo').css('display','');

			var disp_setting="toolbar=yes,location=no,";
			var content_vlue=$('#report_head').show();
			disp_setting+="directories=yes,menubar=yes,";
			disp_setting+="scrollbars=yes,width=800, height=600, left=100, top=25";
				
			  content_vlue= document.getElementById(DivID).innerHTML;
			  var docprint=window.open("","",disp_setting);
			  docprint.document.open();
			  docprint.document.write('<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"');
			  docprint.document.write('"http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">');
			  docprint.document.write('<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en">');
			  docprint.document.write('<head><title><?=TITLE?></title>');
			  docprint.document.write('<link rel="stylesheet" href="<?php echo ROOT;?>css/style.css" media="all"/>');
			  docprint.document.write('<link rel="stylesheet" href="<?php echo ROOT;?>css/bootstrap.min.css" media="all"/>');

			  docprint.document.write('<style type="text/css">body { margin:20px 10px 10px 35px;');
			  docprint.document.write('font-family:Tahoma;color:#000;');
			  docprint.document.write('font-family:Tahoma,Verdana; font-size:10px;} .dataTables_length, .dataTables_filter , .dataTables_paginate { display:none; }');
			  docprint.document.write('#mainpart table,#mainpart tr,#mainpart td,#mainpart th {border:1px #0a0a0a solid;padding:2px 5px 2px 5px;text-align:center;} td {border:1px #0a0a0a solid;}');
			  docprint.document.write('a{color:#000;text-decoration:none;} h1 {font-size:25px; line-height:5px;} b { font-weight:normal; } div.page { page-break-after: always; page-break-inside: avoid; } </style>');
			  docprint.document.write('</head><body onLoad="self.print()"><center>');
			  docprint.document.write(content_vlue);
			  docprint.document.write('</center></body></html>');
			  docprint.document.close();
			  $('#report_head').hide()
			  docprint.focus();
			$('#logo').css('display','none');
			}
			
			var tableToExcel = (function() {
			 var uri = 'data:application/vnd.ms-excel;base64,'
			   , template = '<html xmlns:o="urn:schemas-microsoft-com:office:office" xmlns:x="urn:schemas-microsoft-com:office:excel" xmlns="http://www.w3.org/TR/REC-html40"><head><!--[if gte mso 9]><xml><x:ExcelWorkbook><x:ExcelWorksheets><x:ExcelWorksheet><x:Name>{worksheet}</x:Name><x:WorksheetOptions><x:DisplayGridlines/></x:WorksheetOptions></x:ExcelWorksheet></x:ExcelWorksheets></x:ExcelWorkbook></xml><![endif]--></head><body><table>{table}</table></body></html>'
			   , base64 = function(s) { return window.btoa(unescape(encodeURIComponent(s))) }
			   , format = function(s, c) { return s.replace(/{(\w+)}/g, function(m, p) { return c[p]; }) }
			 return function(table, name) {
			   if (!table.nodeType) table = document.getElementById(table)
			   var ctx = {worksheet: name || 'Worksheet', table: table.innerHTML}
			   window.location.href = uri + base64(format(template, ctx))
			 }
			})()
 
			</script>
	</body>
</html>
