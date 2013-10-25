/*
 * TODO:
 * - add grid icon at the top
 * - add multi sort.
 * - add search.
 * - handle grouping (client side and server side).
 * - add new field type to models (int or null). use it for length.
 * - handle resize of the browser nicely.
 * - make the component always take the same size vertically.
 * - show sums of various types (length, avg of rating and more).
 */
function create_movies(loc) {
	// next two lines are needed for the Ext.ux.RowExpander class which is not
	// in the standard Extjs installation.
	Ext.Loader.setConfig({enabled: true});
	//Ext.Loader.setPath('Ext.ux','/public/ux');
	Ext.require([
		'Ext.state.CookieProvider',
		'Ext.data.Model',
		'Ext.data.Store',
		'Ext.grid.feature.Grouping',
		//'Ext.ux.RowExpander',
		'Ext.grid.Panel'
	], function() {
		var useCookie=false;
		// next line is needed for tooltips to work...
		Ext.QuickTips.init();
		// custom function used for length rendering...
		function render_length(val,lim,precision) {
			if(val==null) {
				return 'Length not known';
			}
			var units=['secs','mins','hrs','days','months','years'];
			var mults=[60,60,24,30,12];
			var i=0;
			while(val>mults[i]) {
				val/=mults[i];
				i++;
				if(units[i]==lim) {
					break;
				}
			}
			return val.toFixed(precision)+' '+units[i];
		}
		function render_movie_length(val) {
			return render_length(val,'mins',0);
		}
		function render_size(val) {
			if(val==null) {
				return 'Size not known';
			}
			return val;
		}
		function render_chapters(val) {
			if(val==null) {
				return 'Chapter number not known';
			}
			return val;
		}
		function render_imdb(val) {
			if(val==null) {
				return 'Not available';
			}
			return '<a href="http://www.imdb.com/title/tt'+val+'/">'+val+'</a>';
		}
		if(useCookie) {
			// next line causes state to be stored in a cookie...
			Ext.state.Manager.setProvider(Ext.create('Ext.state.CookieProvider'));
		}
		// here comes the model...
		Ext.define('MovieModel', {
			extend: 'Ext.data.Model',
			fields: [
				{name: 'viewId', type: 'number'},
				{name: 'name', type: 'string'},
				{name: 'length', type: 'auto'},
				//{name: 'size', type: 'auto'},
				//{name: 'chapters', type: 'auto'},
				{name: 'typeName', type: 'string'},
				//{name: 'startViewDate', type: 'date', dateFormat: 'timestamp'},
				{name: 'endViewDate', type: 'date', dateFormat: 'timestamp'},
				{name: 'locationName', type: 'string'},
				{name: 'deviceName', type: 'string'},
				{name: 'languageName', type: 'string'},
				{name: 'imdbId', type: 'auto'},
			],
			idProperty: 'id',
		});
		var w_store=Ext.create('Ext.data.Store',{
			autoLoad: false,
			pageSize: 20,
			model: 'MovieModel',
			//groupField: 'deviceId',
			proxy: {
				type: 'ajax',
				url: '/public/GetMovies.php',
				reader: {
					type: 'json',
					root: 'views',
					totalProperty: 'total'
				},
			},
			// this means we will sort on the server side...
			remoteSort: true,
			// these are the sorters we will send by default...
			sorters: [
				{
					property: 'endViewDate',
					direction: 'DESC',
				}
			],
			// This listener is here to alert the parent window of our size
			// once the data is loaded. This is only needed if we are in an iframe
			/*
			listeners: {
				load: {
					fn:function() {
						window.parent.alertsize(document.body.scrollHeight)
					}
				}
			},
			*/
		});
		/*
		var groupingFeature=Ext.create('Ext.grid.feature.Grouping',{
			groupHeaderTpl: 'DeviceId: {name} ({rows.length} Item{[values.rows.length > 1 ? 's' : '']})',
			disabled: true,
			// this feature doesnt work right
			//startCollapsed: true,
		});
		*/
		var w_grid=Ext.create('Ext.grid.Panel',{
			title: 'Movies that I have seen',
			store: w_store,
			frame: false,
			border: true,
			//collapsible: true,
			iconCls: 'icon-grid',
			// name of cookie to store the grid state in... remove to get code generated state...
			//stateId: 'stateGridExample',
			columns:[
				{
					text: 'ViewId',
					dataIndex: 'viewId',
					flex: 1,
					hidden: true,
					sortable: true,
				},
				{
					text: 'Name',
					dataIndex: 'name',
					flex: 30,
					hidden: false,
					sortable: true,
				},
				{
					text: 'EndViewDate',
					dataIndex: 'endViewDate',
					flex: 30,
					hidden: false,
					sortable: true,
				},
				{
					text: 'Length',
					dataIndex: 'length',
					flex: 8,
					hidden: false,
					sortable: true,
					renderer: render_movie_length,
				},
				/*
				{
					text: 'Size',
					dataIndex: 'size',
					flex: 30,
					hidden: true,
					sortable: true,
					renderer: render_size,
				},
				{
					text: 'Chapters',
					dataIndex: 'chapters',
					flex: 30,
					hidden: true,
					sortable: true,
					renderer: render_chapters,
				},
				*/
				{
					text: 'TypeName',
					dataIndex: 'typeName',
					flex: 15,
					hidden: true,
					sortable: true,
				},
				{
					text: 'LocationName',
					dataIndex: 'locationName',
					flex: 8,
					hidden: false,
					sortable: true,
				},
				{
					text: 'DeviceName',
					dataIndex: 'deviceName',
					flex: 8,
					hidden: false,
					sortable: true,
				},
				{
					text: 'ImdbId',
					dataIndex: 'imdbId',
					flex: 5,
					hidden: false,
					sortable: true,
					renderer: render_imdb,
				},
			],
			dockedItems: [
				{
					xtype: 'pagingtoolbar',
					store: w_store,
					dock: 'bottom',
					displayInfo: true,
					displayMsg: 'Displaying movies {0} - {1} of {2}',
					emptyMsg: 'No movies to display',
				},
			],
			/*
			bbar: [
				{
					text: 'toggle grouping',
					tooltip: 'bla bla',
					enableToggle: true,
					//iconCls: 'icon-clear-group',
					handler: function() {
						if(groupingFeature.disabled) {
							groupingFeature.enable();
						} else {
							groupingFeature.disable();
						}
					},
				},
			],
			*/
			/*
			plugins: [{
				ptype: 'rowexpander',
				rowBodyTpl: [
					'<p><b>Review:</b> {review}</p>',
					'<p><b>Review Date:</b> {reviewDate}</p>',
				]
			}],
			*/
			//features: [groupingFeature],
			renderTo: loc,
		});
		// trigger the data store load, we must do it or no data is displayed
		w_store.loadPage(1);
	});
}

function create_movies_here() {
	var loc=get_my_location();
	// the onReady is needed because inside the innet function we don't do it
	Ext.onReady(function() {
		create_movies(loc);
	});
}

function create_chart(loc, type) {
	Ext.require([
		'Ext.data.JsonStore',
		'Ext.chart.Chart',
		'Ext.Window',
		'Ext.layout.container.Fit',
		'Ext.fx.target.Sprite',
		'Ext.window.MessageBox'
	]
	, function() {
		window.generateData = function(n, floor) {
			var data = [];
			var p = (Math.random() * 11) + 1;
			floor = (!floor && floor !== 0)? 20 : floor;
			for (var i = 0; i < (n || 12); i++) {
				data.push({
					name: Ext.Date.monthNames[i % 12],
					data1: Math.floor(Math.max((Math.random() * 100), floor)),
					data2: Math.floor(Math.max((Math.random() * 100), floor)),
					data3: Math.floor(Math.max((Math.random() * 100), floor)),
					data4: Math.floor(Math.max((Math.random() * 100), floor)),
					data5: Math.floor(Math.max((Math.random() * 100), floor)),
					data6: Math.floor(Math.max((Math.random() * 100), floor)),
					data7: Math.floor(Math.max((Math.random() * 100), floor)),
					data8: Math.floor(Math.max((Math.random() * 100), floor)),
					data9: Math.floor(Math.max((Math.random() * 100), floor))
				});
			}
			return data;
		};
		window.mystore = Ext.create('Ext.data.JsonStore', {
			fields: ['name', 'data1', 'data2', 'data3', 'data4', 'data5', 'data6', 'data7', 'data9', 'data9'],
			data: generateData()
		});
		var chart = Ext.create('Ext.chart.Chart', {
			style: 'background:#fff',
			animate: true,
			shadow: true,
			store: mystore,
			axes: [{
				type: 'Numeric',
				position: 'left',
				fields: ['data1'],
				label: {
					renderer: Ext.util.Format.numberRenderer('0,0')
				},
				title: 'Number of Hits',
				grid: true,
				minimum: 0
			}, {
				type: 'Category',
				position: 'bottom',
				fields: ['name'],
				title: 'Month of the Year'
			}],
			series: [{
				type: 'column',
				axis: 'left',
				highlight: true,
				tips: {
					trackMouse: true,
					width: 140,
					height: 28,
					renderer: function(storeItem, item) {
						this.setTitle(storeItem.get('name') + ': ' + storeItem.get('data1') + ' $');
					}
				},
				label: {
					display: 'insideEnd',
					'text-anchor': 'middle',
					field: 'data1',
					renderer: Ext.util.Format.numberRenderer('0'),
					orientation: 'vertical',
					color: '#333'
				},
				xField: 'name',
				yField: 'data1'
			}]
		});
		var win = Ext.create('Ext.window.Window', {
			width: 800,
			height: 600,
			minHeight: 400,
			minWidth: 550,
			hidden: false,
			maximizable: true,
			title: 'Column Chart',
			autoShow: true,
			layout: 'fit',
			tbar: [{
				text: 'Save Chart',
				handler: function() {
				Ext.MessageBox.confirm('Confirm Download', 'Would you like to download the chart as an image?', function(choice){
					if(choice == 'yes') {
						chart.save({
						type: 'image/png'
						});
					}
				});
			}
			}, {
				text: 'Reload Data',
				handler: function() {
				}
			}],
			items: chart,
			//renderTo: loc,
		});
	});
}

function create_chart_here(type) {
	var loc=get_my_location();
	// the onReady is needed because inside the innet function we don't do it
	Ext.onReady(function() {
		create_chart(loc, type);
	});
}
