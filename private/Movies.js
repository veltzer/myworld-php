/*
 * TODO:
 * - add grid icon at the top
 * - add multi sort.
 * - add search.
 * - add new field type to models (int or null). use it for length.
 */

// we use the ext-all so we require only stuff that does not exist there...
Ext.Loader.setConfig({
	enabled: true
});
Ext.Loader.setPath('Ext.ux','ux');
Ext.require([
	'Ext.ux.RowExpander',
]);
// now for the real code
Ext.onReady(function(){
	var useCookie=false;
	// next line is needed for tooltips to work...
	Ext.QuickTips.init();
	// custom function used for length rendering...
	function render_length(val) {
		if(val==null) {
			return "Length not known";
		}
		var units=['secs','mins','hrs','days','months','years'];
		var mults=[60,60,24,30,12];
		var i=0;
		while(val>mults[i]) {
			val/=mults[i];
			i++;
		}
		return val.toFixed(2)+' '+units[i];
	}
	function render_size(val) {
		if(val==null) {
			return "Size not known";
		}
		return val;
	}
	if(useCookie) {
		// next line causes state to be stored in a cookie...
		Ext.state.Manager.setProvider(Ext.create('Ext.state.CookieProvider'));
	}
	// here comes the model...
	Ext.define('MovieModel', {
		extend: 'Ext.data.Model',
		fields: [
			{name: 'id', type: 'number'},
			{name: 'name', type: 'string'},
			{name: 'length', type: 'auto'},
			{name: 'size', type: 'auto'},
			{name: 'chapters', type: 'number'},
			{name: 'typeName', type: 'string'},
			{name: 'startViewDate', type: 'date', dateFormat: 'timestamp'},
			{name: 'endViewDate', type: 'date', dateFormat: 'timestamp'},
			{name: 'personFirstname', type: 'string'},
			{name: 'personSurname', type: 'string'},
			{name: 'locationName', type: 'string'},
			{name: 'deviceName', type: 'string'},
			{name: 'languageName', type: 'string'},
			{name: 'ratingName', type: 'string'},
			{name: 'review', type: 'string'},
			{name: 'reviewDate', type: 'date', dateFormat: 'timestamp'},
			{name: 'fullname',
				convert: function(value,record) {
					return record.get('personFirstname')+' '+record.get('personSurname');
				}
			}
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
			url: 'Movies.php',
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
	});
	/*
	var groupingFeature=Ext.create('Ext.grid.feature.Grouping',{
		groupHeaderTpl: 'DeviceId: {name} ({rows.length} Item{[values.rows.length > 1 ? "s" : ""]})',
		disabled: true,
		// this feature doesnt work right
		//startCollapsed: true,
	});
	*/
	var w_grid=Ext.create('Ext.grid.Panel',{
		title: 'Movies that I have seen',
		store: w_store,
		frame: false,
		border: false,
		collapsible: true,
		iconCls: 'icon-grid',
		// name of cookie to store the grid state in... remove to get code generated state...
		//stateId: 'stateGridExample',
		columns:[
			{
				text: 'Id',
				dataIndex: 'id',
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
				text: 'Length',
				dataIndex: 'length',
				flex: 30,
				hidden: false,
				sortable: true,
				renderer: render_length,
			},
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
			},
			{
				text: 'Type name',
				dataIndex: 'typeName',
				flex: 30,
				hidden: true,
				sortable: true,
			},
			{
				text: 'View Date',
				dataIndex: 'endViewDate',
				flex: 30,
				hidden: false,
				sortable: true,
			},
			{
				text: 'Location',
				dataIndex: 'locationName',
				flex: 30,
				hidden: false,
				sortable: true,
			},
			{
				text: 'Device',
				dataIndex: 'deviceName',
				flex: 30,
				hidden: false,
				sortable: true,
			},
			{
				text: 'Rating',
				dataIndex: 'ratingName',
				flex: 30,
				hidden: false,
				sortable: true,
			},
			{
				text: 'Viewer',
				dataIndex: 'fullname',
				flex: 30,
				hidden: true,
				sortable: false,
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
		plugins: [{
			ptype: 'rowexpander',
			rowBodyTpl: [
				'<p><b>Review:</b> {review}</p>',
				'<p><b>Review Date:</b> {reviewDate}</p>',
			]
		}],
		//features: [groupingFeature],
		renderTo: 'movie-grid'
	});
	// trigger the data store load, we must do it or no data is displayed
	w_store.loadPage(1);
});
