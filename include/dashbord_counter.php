  <link href="assets/morris.js-0.4.3/morris.css" rel="stylesheet" />
<div class="">
	<div class="col-lg-12">
		<div class="row ">
            <div class="col-sm-12 ">
				<section class="panel" style="height:1000px;">
					<!--<div class="col-md-12 style-12" style="overflow-x: scroll;width:100%;min-height: 70px;">-->
					<div class="col-md-12 style-12" style="overflow-x: auto;width:100%;min-height: 70px;">
						<div class="col-md-12" style="padding-bottom: 15px;">
							<div class="col-md-2">Stock Date</div>
							<div class="col-md-2">
								<input id="dstock_date" name="dstock_date" type="text" class="form-control default-date-picker" title="Stock Date" onchange="load_emp_stock_dash();" value="<?=$dstock_date?>" placeholder="Stock Date">
							</div>
						</div>
						<div id="emp_stock_div"></div>
					</div>
					<div class="col-lg-12 padding_lr_0">
						<div class="col-lg-6">
							<p class="pheading" style="padding-bottom:0px;">Sales Summary</p>
							<div class="icons" style="width:100px;margin-top:0px;">
								<div class="icon1 success" >
									<p style="color:white;">Total Amount</p><br/>
									<h3 style="font-size:20px;color:white;"><span id="tsaleamount" style="font-size:20px;color:white;"></span> </h3>
								</div>
							</div>
							<div class="icons" style="width:100px;margin-top:0px;">	 	
								<div class="icon1  warning" >
									<p style="color:white;">Total Taxable Value</p>
									<h3 style="font-size:20px;color:white;"><span id="tsaletax" style="font-size:20px;color:white"></span></h3>
								</div>
							</div>
							<div class="icons" style="width:100px;margin-top:0px;">	 	
								<div class="icon1 info" >
									<p style="color:white;">Total Payment</p><br/>
									<h3 style="font-size:20px;color:white;"><span id="tsalepaidamount" style="font-size:20px;color:white"></span></h3>
								</div>
							</div>
							<div class="icons" style="width:100px;margin-top:0px;">	 	
								<div class="icon1 danger" >
									<p style="color:white;">Total  Due</p><br/>
									<h3 style="font-size:20px;color:white;"><span id="tsaleoutstanding" style="font-size:20px;color:white"></span></h3>
								</div>
							</div>
						</div>
						<div class="col-lg-6 margin_bottom_3">
							<p class="pheading" style="padding-bottom:0px;">Purchase Summary</p>
							<div class="icons" style="width:100px;margin-top:0px;">
								<div class="icon1 success" >
									<p style="color:white;">Total Amount</p><br/>
									<h3 style="font-size:20px;color:white;"><span id="tpurchaseamount" style="font-size:20px;color:white;"></span> </h3>
								</div>
							</div>
							<div class="icons" style="width:100px;margin-top:0px;">	 	
								<div class="icon1 warning" >
									<p style="color:white;">Total Taxable Value</p>
									<h3 style="font-size:20px;color:white;"><span id="tpurchasetax" style="font-size:20px;color:white"></span></h3>
								</div>
							</div>
							<div class="icons" style="width:100px;margin-top:0px;">	 	
								<div class="icon1 info" >
									<p style="color:white;">Total Payment</p><br/>
									<h3 style="font-size:20px;color:white;"><span id="tpurchasepaidamount" style="font-size:20px;color:white"></span></h3>
								</div>
							</div>
							<div class="icons" style="width:100px;margin-top:0px;">	 	
								<div class="icon1 danger" >
									<p style="color:white;padding-bottom:20px;">Total Due</p>
									<h3 style="font-size:20px;color:white;"><span id="tpurchaseoutstanding" style="font-size:20px;color:white"></span></h3>
								</div>
							</div>
						</div>
						<div class="col-lg-12 col-sm-12">
							<div class="">
								<!--<div class="row">-->
									<div class="col-lg-12">
										<label class="col-md-12 control-label" style="font-weight: bold;font-size: 20px;color: black;">Select Financial Year</label>
										<div class="col-md-3 col-xs-11">
											<?
												$minyear= 2016;
												$maxyear=(date('m')<'04') ? date('Y',strtotime('-1 year')) : date('Y');
												$end = $start+1;
											?>
											<form>
												<select class="form-control" name="c_year" id="c_year" onchange="get_value();" >
													<?
														for($y=$minyear;$y<=$maxyear;$y++)
														{
															$sel='';
															if($maxyear==$y)
															{
																$sel='selected="selected"';
															}
															?>
															<option <?=$sel?> value="<?=$y?>"><? echo $y.'-'.($y+1)?></option>	
													
													<?  }?>
												</select>
											</form>
										</div>
									</div>
									<div class="col-lg-12 col-sm-12" style="">
										<div id="chart-3"></div>
									</div>
								<!--</div>-->
							</div>
						</div>
					</div>
				</section>
			</div>
		</div>
	</div>
</div>
 
<script type="text/javascript">
function get_value()
{
 Loading(true);	

$('#title_chart').html('');
$('#chart-3').html('');
load_salevalue();
load_purchasevalue();
load_graph(); 

//load_excisepichart();
 
 Unloading();
}
$(document).ready(function() {
 Loading(true);	
 load_salevalue();
 load_graph();
  load_purchasevalue();
  load_emp_stock_dash();
 
Unloading();
});

function load_fivecust()
{
	var c_year=$('#c_year').val();
  $.ajax({
	type: "POST",
	url: root_domain+'app/dashboard/',
	data: { mode : "getcust", c_year : c_year},
	success: function(response){
				$('#top_5_cust').html(response);
	}
	});
}  
/*function load_value()
{
 var c_year=$('#c_year').val();
  $.ajax({
	type: "POST",
	url: root_domain+'app/dashboard/',
	data: { mode : "getyear", c_year : c_year},
	success: function(response){
		console.log(response);
		var data = JSON.parse(response);
		$('#bussiness').html(data.total);
		$('#turnover').html(data.paid_amount);
		$('#outstanding').html(data.total-data.paid_amount);
	}
	});
Unloading();
}  
*/
 function load_salevalue()
 {
	 		Loading();
	var c_year=$('#c_year').val();

	
	$.ajax({
		type: "POST",
		url: root_domain+'app/dashboard/',
		data: { mode : "load_saleval",c_year :  c_year},
		success: function(response)
		{
			console.log(response);
			var resp=jQuery.parseJSON(response);
			if(response != "") {
				
				$('#tsaleamount').html(resp.total);
				$('#tsaletax').html(resp.taxable_amt);
				$('#tsalepaidamount').html(resp.total_paid_amount);
				$('#tsaleoutstanding').html(resp.total-resp.total_paid_amount);
				Unloading();
			}
										
		}
	});
 }
  function load_purchasevalue()
 {
	 		Loading();
var c_year=$('#c_year').val();

	
	$.ajax({
		type: "POST",
		url: root_domain+'app/dashboard/',
		data: { mode : "load_purchaseval",c_year :  c_year},
		success: function(response)
		{
			console.log(response);
			var resp=jQuery.parseJSON(response);
			if(response != "") {
				
				$('#tpurchaseamount').html(resp.total);
				$('#tpurchasetax').html(resp.taxable_amt);
				$('#tpurchasepaidamount').html(resp.total_paid_amount);
				$('#tpurchaseoutstanding').html(resp.total-resp.total_paid_amount);
				Unloading();
			}
										
		}
	});
 }
function load_graph()
{
	Loading(true);	
	var c_year=$('#c_year').val();
	var mainurl = root_domain+'app/dashboard/index.php?mode=dynamic_chart&c_year='+c_year;
	$.getJSON(mainurl, function(json) {
	var arr=new Array();
		for(var i=0;i<12;i++)
		{	
			arr[i]=json[i];	
		}
		Morris.Bar({
        element: 'chart-3',
        data: arr,
		barSizeRatio:0.55,
        xkey: 'device',
        ykeys: ['geekbench'],
        labels: ['Bussiness'],
        barRatio: 0.4,
        xLabelAngle: 35,
        hideHover: 'auto',
        barColors: ['#6883a3'],
		lineWidth:25
      });
	});
Unloading();
}
 </script>
 <script type="text/javascript">
function load_pichart()
{
  var c_year=$('#c_year').val();
  var mainurl = root_domain+'app/dashboard/index.php?mode=taxinvoice_circle&c_year='+c_year;
 
	$.getJSON(mainurl, function(json) {
			var chart = new CanvasJS.Chart("chartContainer",
			{
		title:{
			text: "TurnOver "+c_year+"-"+(parseInt(c_year)+parseInt(1))
		},
                animationEnabled: true,
		legend:{
			verticalAlign: "bottom",
			horizontalAlign: "center"
		},
		data: [
		{        
			indexLabelFontSize: 20,
			indexLabelFontFamily: "Monospace",       
			indexLabelFontColor: "darkgrey", 
			indexLabelLineColor: "darkgrey",        
			indexLabelPlacement: "outside",
			type: "pie",       
			showInLegend: true,
			toolTipContent: "{y} - <strong>#percent%</strong>",
			dataPoints: [
				  json[0],json[1],json[2],json[3],json[4],json[5],json[6],json[7],json[8],json[9],json[10],json[11] 
			]
		}
		]
	});
	chart.render();
			
	});
}
function load_excisepichart()
{
  var c_year=$('#c_year').val();
  var mainurl = root_domain+'app/dashboard/index.php?mode=exciseinvoice_circle&c_year='+c_year;
 
$.getJSON(mainurl, function(json) {
			var chart = new CanvasJS.Chart("chartContainer_exciseinvoice",
			{
		title:{
			text: "Excise Invoice TurnOver "+c_year+"-"+(parseInt(c_year)+parseInt(1))
		},
                animationEnabled: true,
		legend:{
			verticalAlign: "bottom",
			horizontalAlign: "center"
		},
		data: [
		{        
			indexLabelFontSize: 20,
			indexLabelFontFamily: "Monospace",       
			indexLabelFontColor: "darkgrey", 
			indexLabelLineColor: "darkgrey",        
			indexLabelPlacement: "outside",
			type: "pie",       
			showInLegend: true,
			toolTipContent: "{y} - <strong>#percent%</strong>",
			dataPoints: [
				  json[0],json[1],json[2],json[3],json[4],json[5],json[6],json[7],json[8],json[9],json[10],json[11] 
			]
		}
		]
	});
	chart.render();
			
	});
}
function load_emp_stock_dash(){
	var dstock_date=$('#dstock_date').val();
	Loading();
	$.ajax({
		type: "POST",
		url: root_domain+'app/dashboard/',
		data: { mode : "load_emp_stock", dstock_date : dstock_date},
		success: function(response){
				// console.log(response);
				 $('#emp_stock_div').html(response);
				 //search();
				 Unloading();
			}
			
	});
}
</script>