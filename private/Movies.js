Ext.onReady(function(){
	Ext.define('MovieModel', {
		extend: 'Ext.data.Model',
			fields: [
				'id',
				'name',
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
				sortable: true,
			},
	    		{
				text: 'Name',
				dataIndex: 'name',
				flex: 30,
				sortable: true
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
		renderTo: 'movie-grid'
	});
	// trigger the data store load, we must do it or no data is displayed
	w_store.loadPage(1);
});
