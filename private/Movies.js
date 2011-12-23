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
	Ext.define('MovieModel', {
		extend: 'Ext.data.Model',
			fields: [
				'id',
				'name',
				'length',
				'size',
				'chapters',
				'typeId',
				'languageId',
				'startViewDate',
				'endViewDate',
				'viewerId',
				'locationId',
				'deviceId',
				'langId',
				'ratingId',
				'review',
				'reviewDate',
			],
		idProperty: 'id'
	});
	var w_store=Ext.create('Ext.data.Store',{
		autoLoad: false,
		pageSize: 20,
		model: 'MovieModel',
		proxy: {
			type: 'ajax',
			url: 'Movies.php',
			reader: {
				type: 'json',
				root: 'views',
				totalProperty: 'total'
			},
		},
	});
	var w_grid=Ext.create('Ext.grid.Panel',{
		title: 'Movies that I have seen',
		store: w_store,
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
	    	dockedItems: [{
			xtype: 'pagingtoolbar',
			store: w_store,
	    		dock: 'bottom',
			displayInfo: true,
			displayMsg: 'Displaying movies {0} - {1} of {2}',
			emptyMsg: 'No movies to display',
		}],
		plugins: [{
			ptype: 'rowexpander',
			rowBodyTpl: [
				'<p><b>Review:</b> {review}</p>',
				'<p><b>Review Date:</b> {reviewDate}</p>',
			]
		}],
		renderTo: 'movie-grid'
	});
	// trigger the data store load, we must do it or no data is displayed
	w_store.loadPage(1);
});
