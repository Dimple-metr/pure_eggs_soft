<!--<<link href="assets/morris.js-0.4.3/morris.css" rel="stylesheet" />-->
<div class="">
	<div class="col-lg-12">
		<section class="panel">
			<div class="panel-body" >
				<div class="row state-overview">
					<label class="col-md-3 control-label" style="font-weight: bold;font-size: 20px;color: black;">Select Financial Year</label>
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
									
								<? }?>
							</select>
						</form>
					</div>
				</div>
				<div class="col-lg-3 col-sm-6">
					
				</div>
				
				<div class="col-lg-3 col-sm-6">
					
				</div>
				<div class="col-lg-3 col-sm-6">
				</div>
				<div class="col-lg-3 col-sm-6">
					
				</div>
				<div class="col-lg-12 col-sm-12">
					<div class="">
						<div class="row">
							<div class="col-lg-12" style="">
								<div id="chart-3"></div>
							</div>
						</div>
					</div>
					
				</div>
			</div>
		</section>
	</div>
</div>

<script type="text/javascript">

function get_value()
{
	Loading(true);	
	$('#title_chart').html('');
	$('#chart-3').html('');
	load_graph(); 
	//load_excisepichart();
	Unloading();
}
$(document).ready(function() {
	Loading(true);	
	load_graph();
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
/*function load_graph()
{
	Loading(true);	
	var c_year=$('#c_year').val();
	var currency_id=$('#currency_id').val();
	var mainurl = root_domain+'app/dashboard/index.php?mode=dynamic_chart&c_year='+c_year+'&currency_id='+currency_id;
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
}*/
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

</script>	