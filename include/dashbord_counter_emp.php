  <link href="assets/morris.js-0.4.3/morris.css" rel="stylesheet" />
  
<div class="">
	<div class="col-lg-12">
		<div class="row ">
            <div class="col-sm-12 ">
				<section class="panel" style="height:1000px;">
						<div class="col-lg-12 col-sm-12">
							<div class="">
								<div class="row">
									<div class="col-md-12" style="padding-bottom: 15px;">
										<div class="col-md-2">Stock Date</div>
										<div class="col-md-2">
											<input id="dstock_date1" name="dstock_date1" type="text" class="form-control default-date-picker" title="Stock Date" onchange="load_target_chart();" value="<?=$dstock_date?>" placeholder="Stock Date">
										</div>
									</div>
									<div class="col-md-12" style="overflow-x: scroll;width:100%;min-height: 70px;">
										<div id="emp_stock_div"></div>
									</div>
									<div class="col-md-12 overflow-auto" style="overflow-x: scroll;width:100%;min-height: 70px;">
										<div id="chart-5" ></div>
									</div>
									
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
									<div class="col-lg-12 overflow-auto" >
										<div id="chart-3"></div>
									</div>
								</div>
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
load_graph(); 
 Unloading();
}
$(document).ready(function() {
 Loading(true);	
 $('#chart-5').html('');
 load_graph();
 load_target_chart();
	Unloading();
});

function load_graph()
{
	Loading(true);	
	var c_year=$('#c_year').val();
	var mainurl = root_domain+'app/dashboard_emp/index.php?mode=dynamic_chart&c_year='+c_year;
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
function load_target_chart()
{
	$('#chart-5').html('');
	$('.title_chart1').html('');
	Loading();
	var dstock_date=$('#dstock_date1').val();
	//alert(dstock_date);
	$('.chart-5').html('');
	$('.title_chart1').html('');
	var mainurl = root_domain+'app/dashboard_emp/index.php?mode=load_target_chart&dstock_date='+dstock_date;
	
	$.getJSON(mainurl, function(json) {
		//console.log(json);
		if(!json){
			$('#chart-5').html('<strong>No Pending Dispatch !!</strong>');
		}
		else{
			
			var arr=new Array();
			
			//console.log(json["count"]);
			var count=json["count"];
			for(var i=0;i<count;i++)
			{	
				arr[i]=[json[json[i]],json[i]];	
			}
			fil_arr=arr;
			$('#chart-5').jqBarGraph({
				data: fil_arr,
				colors: ['#6883a3','#3fc343',''],
				legends: ['Allocate','Pending',''],
				legend: true,
				width: 1600,
				color: '#ffffff',
				type: 'multi',
				postfix: '',
				showValues: true,
				title: '<h3 class="title_chart1">'+json["user_name"]+' PENDING PRODUCT CHART</h3>'
			});
			
		}
	});
	Unloading();
	load_emp_stock_dash();
}
function load_emp_stock_dash(){
	var dstock_date=$('#dstock_date1').val();
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
 <script type="text/javascript">


</script>