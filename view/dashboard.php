<?php 
	session_start();
	include_once("../config/config.php");
	include_once("../config/session.php");
	include_once("../include/common_functions.php");
	$frmdt=date('d-m-Y');
	$todt=date('d-m-Y');
	$dstock_date=date('d-m-Y');
	
?>
<!DOCTYPE html>
<html lang="en">
	<head>
		<?php include_once('../include/include_css_file1.php');?>
		<style>
		
		.graphLabelchart-5{
			    height: 60px;
		}
		.icons{
			width: 14.5%;
			float: left;
			margin: 30px 7px 5px;
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
			width: 110px;
			height:90px;
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
			width: 265px;
			float: left;
			margin: 30px 4px 25px;
			text-align: center;
			position:relative;
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
			<?php include_once('../include/left_menu.php');?>
			<section id="main-content">
				<section class="wrapper">			
					<?php 
						if(!empty($_SESSION['company_id']))
						{
							if($_SESSION['user_type']==2){
								include_once('../include/dashbord_counter.php');
							}else{
								include_once('../include/dashbord_counter_emp.php');
							}
							
						}
					?>
				</section>
			</section>
				<?php include_once('../include/footer.php');?>
		</section>
		<?php include_once('../include/include_js_file.php');?>   
		<!--<script src="<?=ROOT?>js/app/todo_mst.js"></script>-->
		
  </body>
  <script>
	$('.default-date-picker').datepicker({
							format: 'dd-mm-yyyy',
							autoclose: true
						});
  </script>
 
</html>
