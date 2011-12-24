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
			{name: 'length', type: 'number'},
			{name: 'size', type: 'number'},
			{name: 'chapters', type: 'number'},
			{name: 'typeId', type: 'number'},
			{name: 'languageId', type: 'number'},
			{name: 'startViewDate', type: 'date', dateFormat: 'timestamp'},
			{name: 'endViewDate', type: 'date', dateFormat: 'timestamp'},
			{name: 'viewerId', type: 'number'},
			{name: 'locationId', type: 'number'},
			{name: 'deviceId', type: 'number'},
			{name: 'langId', type: 'number'},
			{name: 'ratingId', type: 'number'},
			{name: 'review', type: 'string'},
			{name: 'reviewDate', type: 'date', dateFormat: 'timestamp'},
		],
		idProperty: 'id',
	});
	var w_store=Ext.create('Ext.data.Store',{
		autoLoad: false,
		pageSize: 20,
		model: 'MovieModel',
		//groupField: 'deviceId',
		remoteSort: true,
		proxy: {
			type: 'ajax',
			url: 'Movies.php',
			reader: {
				type: 'json',
				root: 'views',
				totalProperty: 'total'
			},
		},
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
				text: 'View Date',
				dataIndex: 'endViewDate',
				flex: 30,
				hidden: false,
				sortable: true,
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
