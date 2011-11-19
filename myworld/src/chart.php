<?php
function create_chart($params) {
	$limit=50;
	if(array_key_exists('limit',$params)) {
		$limit=$params['limit'];
	}
	$sample=<<<EOD
<div id="chart_id" style="width:260px;height:200px;"></div>
<script>
	dojo.require('dojox.charting.Chart2D');
	dojo.require('dojox.charting.widget.Chart2D');
	dojo.require('dojox.charting.themes.PlotKit.blue');
	dojo.require("dojox.charting.StoreSeries");
	dojo.require("dojo.data.ItemFileReadStore");
	dojo.ready(function() {
		var store=new dojo.data.ItemFileReadStore({url:"/private/GetData.php?type=video_viewing"});
		var storeSeries=new dojox.charting.StoreSeries(store,{ query: { } }, "value"); 
		var chart = new dojox.charting.Chart2D('chart_id');
		chart.addSeries('Visits For February', storeSeries);
		chart.render();
	});
</script>
EOD;
	$res='';
	$res.=$sample;
	return $res;
}
?>
