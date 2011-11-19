<?php
function create_chart($params) {
	if(!array_key_exists('type',$params)) {
		return '<strong>Error, must pass type</strong>';
	}
	if(!array_key_exists('max',$params)) {
		return '<strong>Error, must pass max</strong>';
	}
	$p_type=$params['type'];
	$p_max=$params['max'];
	$p_ticks=$p_max/10;
	static $p_id=1;
	$sample=<<<EOD
		<div id="chart_$p_id"></div>
		<script>
			dojo.require('dojox.charting.Chart2D');
			dojo.require('dojox.charting.widget.Chart2D');
			dojo.require("dojo.data.ItemFileReadStore");
			dojo.require("dojox.charting.DataChart");
			dojo.ready(function() {
				var store=new dojo.data.ItemFileReadStore({url:"/public/GetData.php?type=$p_type"});
				var chart=new dojox.charting.DataChart("chart_$p_id", {
					// this is needed to make nice columns
					type: dojox.charting.plot2d.Columns,
					// this is needed. default is true and it sucks big time...
					scroll:false,
					// this is needed for the labels
					xaxis:{labelFunc:"seriesLabels"},
					// this is needed since y axis is to 10 by default
					yaxis:{max:$p_max, majorTickStep: $p_ticks},
				});
				// the last two arguments are needed
				chart.setStore(store, {}, "value");
				chart.render();
			});
		</script>
EOD;
	$p_id++;
	$res='';
	$res.=$sample;
	return $res;
}
?>
