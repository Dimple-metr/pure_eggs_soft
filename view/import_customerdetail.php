<?php 
	session_start();
	include_once("../config/config.php");
	include_once("../config/session.php");
	include_once("../include/coman_function.php");
	$token = md5(rand(1000,9999));
	$_SESSION['token'] = $token;
	$form="Import Customer";
	if(strpos($_SERVER[REQUEST_URI], "festivaledit")==false)
	{
		$mode="Add";
	}
	else
	{
		$mode="Edit";
		$festivalid=$dbcon->real_escape_string($_REQUEST['id']);
		$query="select * from festival_mst where festival_id=$festivalid";
		$rel=mysqli_fetch_assoc($dbcon->query($query));		
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
			<div class="row">
			  <div class="col-lg-12">
				  <!--breadcrumbs start -->
				  <section class="panel">
					  <header class="panel-heading">
						  <h3><?=$form?></h3>
						</header>	
							<div class="">
						  <ul class="breadcrumb">
							  <li><a href="<?=ROOT.'dashboard'?>"><i class="fa fa-home"></i> Home</a></li>
							  <li><a href="<?=ROOT.'customer_list'?>">Customer List</a></li>
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
					<div class="panel-body ">
					<form class="form-horizontal" role="form" id="import_customer" action="javascript:;" method="post" name="import_customer">
							<div class="row">
							<div class="col-md-10">
							<div class="form-group">
							  <label class="col-md-3 control-label">Import Customer .csv File</label>
									<div class="col-md-4 col-xs-11">
									<input type="file" id="excel_file" name="excel_file" class="form-control"  accept=".csv" required title="Select File"/>
									 <div id="msg"></div>
								</div>
								<?
									if($mode=="Edit")
									{
									echo '<a href"'.ROOT.RESULT_VWING.$rel['excel_file'].'" target="_blank">View Excel </a>';
									}
								?>	
							 </div>
							 <div class="form-group">
							  <label class="col-md-3 control-label">File Formate</label>
									<div class="col-md-6 col-xs-11">
					<a href="<?=ROOT.CUSTOMER_VWING.'demo_customer_import.csv'?>" target="_blank" class="btn btn-info">Click to View Csv File Formate  </a>
								</div>
								
							 </div>
							
							<button type="submit" class="btn btn-success">Submit</button> &nbsp;
							<a href="<?=ROOT.'customer_list'?>" type="button" class="btn btn-danger">Cancel</a>
							<div class="col-md-3"></div>	
							</div>
						</div><!--Vendor row end-->	
							<input type='hidden' name='mode' id='mode' value='check_data' />
							<input type='hidden' name='eid' id='eid' value='<?=$rel['festival_id']?>' />
							<input type='hidden' name='token' id='token' value='<?php echo $token; ?>' />				  
							
						  </form>
</div>	
					</section>
					<section class="panel" id="imported_data_section" style="display:none">
						<header class="panel-heading">
							 Error In Import Data Record
							</header>
							<div class="panel-body">
								 
								<div id="temp_custdata">
								
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
   <script src="<?=ROOT?>js/app/customer.js?<?=time()?>"></script>
    <script>
	
	$(".select2").select2({
		width: '100%'
	});
	$('.default-date-picker').datepicker({
            format: 'dd-mm-yyyy',
            autoclose: true
        });
</script>
	

  </body>
</html>
