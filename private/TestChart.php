<html>
	<head>
		<script src="http://ajax.googleapis.com/ajax/libs/dojo/1.6.1/dojo/dojo.xd.js" type="text/javascript"></script>
		<script>
			dojo.require('dojox.charting.Chart2D');
			dojo.require('dojox.charting.widget.Chart2D');
			dojo.require("dojo.data.ItemFileReadStore");
			dojo.require("dojox.charting.DataChart");
			dojo.ready(function() {
				var store=new dojo.data.ItemFileReadStore({url:"GetData.php?type=video_viewing"});
				var chart=new dojox.charting.DataChart("chart_id", {
					// this is needed to make nice columns
					type: dojox.charting.plot2d.Columns,
					// this is needed. default is true and it sucks big time...
					scroll:false,
					// this is needed for the labels
					xaxis:{labelFunc:"seriesLabels"},
					// this is needed since y axis is to 10 by default
					yaxis:{max:60},
				});
				// the last two arguments are needed
				chart.setStore(store, {}, "value");
				chart.render();
			});
		</script>
	</head>
	<body>
		<div id="chart_id"></div>
	</body>
</html>
