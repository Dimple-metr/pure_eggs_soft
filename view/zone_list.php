<?php 
	session_start();
	include_once("../config/config.php");
	include_once("../config/session.php");
	include_once("../include/common_functions.php");
	$form="Zone";
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
		<section id="container" >
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
									<form role="form" id="zone_add" action="javascript:;" method="post" name="zone_add">
										<div class="col-md-12">
											<div class="col-md-5">
												<div class="form-group">
													<div class="col-md-4"><strong>Zone Name</strong></div>
													<div class="col-md-8">
														<input class="form-control" type='text' name='zone_name' id='zone_name' placeholder="Zone Name" value='' autocomplete="off" />
													</div>
												</div>
											</div>
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
									Zone List
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
													<th>Zone Name</th>
													<th class="hidden-phone">Action</th>
												</tr>
											</thead>
											<tbody></tbody>
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
		<script src="<?=ROOT?>js/app/zone_list.js"></script>
		<script>
			$(".select2").select2({
				width: '100%'
			});
		</script>
	</body>
</html>
