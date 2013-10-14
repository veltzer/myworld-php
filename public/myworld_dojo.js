function create_chart_dojo(loc, max, ticks, type) {
	require([
		'dojox/charting/Chart2D',
		'dojox/charting/widget/Chart2D',
		'dojox/charting/DataChart',
		'dojox/layout/ScrollPane',
		'dojo/data/ItemFileReadStore'
	], function() {
		var scroll=new dojox.layout.ScrollPane({
			orientation: "horizontal",
		},loc);
		scroll.startup();
		var store=new dojo.data.ItemFileReadStore({url:"/public/GetData.php?type=".type});
		var chart=new dojox.charting.DataChart(loc, {
			// this is needed to make nice columns
			type: dojox.charting.plot2d.Columns,
			//type: dojox.charting.plot2d.Lines,
			// this is needed. default is true and it sucks big time...
			//scroll:true,
			//stretchToFit:false,
			scroll:false,
			// this is needed for the labels
			xaxis:{labelFunc:"seriesLabels"},
			// this is needed since y axis is to 10 by default
			yaxis:{max:max, majorTickStep: ticks},
		});
		// the last two arguments are needed
		chart.setStore(store, {}, "value");
		chart.render();
	});
}

function create_chart_dojo_here(max, ticks, type) {
	var loc=get_my_location();
	create_chart_dojo(loc, max, ticks, type);
}
