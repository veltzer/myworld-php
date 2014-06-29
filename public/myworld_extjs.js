/*jsl:import myworld_utils.js*/

/*jsl:ignore*/
'use strict';
/*jsl:end*/

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
  Ext.Loader.setConfig({
    enabled: true
  });
  Ext.Loader.setPath('Ext.ux', '/public/ux');
  Ext.require(['Ext.state.CookieProvider', 'Ext.data.Model', 'Ext.data.Store',
    'Ext.grid.feature.Grouping',
    // 'Ext.ux.LiveSearchGridPanel',
    // 'Ext.ux.RowExpander',
    'Ext.grid.Panel'], function() {
    var useCookie = false;
    // next line is needed for tooltips to work...
    Ext.QuickTips.init();
    // custom function used for length rendering...
    function render_length(val, lim, precision) {
      if (val === null) {
        return 'Length not known';
      }
      var units = ['secs', 'mins', 'hrs', 'days', 'months', 'years'];
      var mults = [60, 60, 24, 30, 12];
      var i = 0;
      while (val > mults[i]) {
        val /= mults[i];
        i++;
        if (units[i] == lim) {
          break;
        }
      }
      return val.toFixed(precision) + ' ' + units[i];
    }
    function render_movie_length(val) {
      return render_length(val, 'mins', 0);
    }
    /*
     * function render_size(val) { if(val==null) { return 'Size not known'; }
     * return val; } function render_chapters(val) { if(val==null) { return
     * 'Chapter number not known'; } return val; }
     */
    function render_imdb(val) {
      if (val === null) {
        return 'Not available';
      }
      return '<a href="http://www.imdb.com/title/tt' + val + '/">' + val +
          '</a>';
    }
    if (useCookie) {
      // next line causes state to be stored in a cookie...
      Ext.state.Manager.setProvider(Ext.create('Ext.state.CookieProvider'));
    }
    // here comes the model...
    var w_model = Ext.define('MovieModel', {
      extend: 'Ext.data.Model',
      fields: [{
        name: 'viewId',
        type: 'number'
      }, {
        name: 'name',
        type: 'string'
      }, {
        name: 'length',
        type: 'auto'
      },
      // {name: 'size', type: 'auto'},
      // {name: 'chapters', type: 'auto'},
      {
        name: 'typeName',
        type: 'string'
      },
      // {name: 'startViewDate', type: 'date', dateFormat: 'timestamp'},
      {
        name: 'endViewDate',
        type: 'date',
        dateFormat: 'timestamp'
      }, {
        name: 'locationName',
        type: 'string'
      }, {
        name: 'deviceName',
        type: 'string'
      }, {
        name: 'languageName',
        type: 'string'
      }, {
        name: 'imdbId',
        type: 'auto'
      }],
      idProperty: 'id'
    });
    var w_store = Ext.create('Ext.data.Store', {
      autoLoad: false,
      pageSize: 20,
      // model: 'MovieModel',
      model: w_model,
      // groupField: 'deviceId',
      proxy: {
        type: 'ajax',
        url: '/public/GetMovies.php',
        reader: {
          type: 'json',
          root: 'views',
          totalProperty: 'total'
        }
      },
      // this means we will sort on the server side...
      remoteSort: true,
      // these are the sorters we will send by default...
      sorters: [{
        property: 'endViewDate',
        direction: 'DESC'
      }]
      // This listener is here to alert the parent window of our size
      // once the data is loaded. This is only needed if we are in an iframe
      /*
     * listeners: { load: { fn:function() {
     * window.parent.alertsize(document.body.scrollHeight) } } },
     */
    });
    /*
     * var groupingFeature=Ext.create('Ext.grid.feature.Grouping',{
     * groupHeaderTpl: 'DeviceId: {name} ({rows.length}
     * Item{[values.rows.length >
     * 1 ? 's' : '']})', disabled: true, // this feature doesnt work right
     * //startCollapsed: true, });
     */
    Ext.create('Ext.grid.Panel', {
      title: 'Movies that I have seen',
      store: w_store,
      frame: false,
      border: true,
      // collapsible: true,
      iconCls: 'icon-grid',
      // name of cookie to store the grid state in... remove to get code
      // generated state...
      // stateId: 'stateGridExample',
      columns: [{
        text: 'ViewId',
        dataIndex: 'viewId',
        flex: 1,
        hidden: true,
        sortable: true
      }, {
        text: 'Name',
        dataIndex: 'name',
        flex: 30,
        hidden: false,
        sortable: true
      }, {
        text: 'EndViewDate',
        dataIndex: 'endViewDate',
        flex: 30,
        hidden: false,
        sortable: true
      }, {
        text: 'Length',
        dataIndex: 'length',
        flex: 8,
        hidden: false,
        sortable: true,
        renderer: render_movie_length
      },
      /*
       * { text: 'Size', dataIndex: 'size', flex: 30, hidden: true, sortable:
       * true, renderer: render_size }, { text: 'Chapters', dataIndex:
       * 'chapters', flex: 30, hidden: true, sortable: true, renderer:
       * render_chapters },
       */
      {
        text: 'TypeName',
        dataIndex: 'typeName',
        flex: 15,
        hidden: true,
        sortable: true
      }, {
        text: 'LocationName',
        dataIndex: 'locationName',
        flex: 8,
        hidden: false,
        sortable: true
      }, {
        text: 'DeviceName',
        dataIndex: 'deviceName',
        flex: 8,
        hidden: false,
        sortable: true
      }, {
        text: 'ImdbId',
        dataIndex: 'imdbId',
        flex: 5,
        hidden: false,
        sortable: true,
        renderer: render_imdb
      }],
      dockedItems: [{
        xtype: 'pagingtoolbar',
        store: w_store,
        dock: 'bottom',
        displayInfo: true,
        displayMsg: 'Displaying movies {0} - {1} of {2}',
        emptyMsg: 'No movies to display'
      }],
      /*
       * bbar: [ { text: 'toggle grouping', tooltip: 'bla bla', enableToggle:
       * true, //iconCls: 'icon-clear-group', handler: function() {
       * if(groupingFeature.disabled) { groupingFeature.enable(); } else {
       * groupingFeature.disable(); } }, }, ],
       */
      /*
       * plugins: [{ ptype: 'rowexpander', rowBodyTpl: [ '<p><b>Review:</b>
       * {review}</p>', '<p><b>Review Date:</b> {reviewDate}</p>', ] }],
       */
      // features: [groupingFeature],
      renderTo: loc
    });
    // trigger the data store load, we must do it or no data is displayed
    w_store.loadPage(1);
  });
}

function create_movies_here() {
  var loc = get_my_location();
  // the onReady is needed because inside the innet function we don't do it
  Ext.onReady(function() {
    create_movies(loc);
  });
}

function create_chart(loc, type, model_x_label, model_y_label,
    box_title, count_title, field_title) {
  Ext.require(['Ext.data.Model', 'Ext.chart.Chart', 'Ext.panel.Panel',
    'Ext.data.Store'], function() {
    var w_model = Ext.define('MyModel', {
      extend: 'Ext.data.Model',
      fields: [{
        name: model_x_label,
        type: 'string'
      }, {
        name: model_y_label,
        type: 'number'
      }]
    });
    var w_store = Ext.create('Ext.data.Store', {
      autoLoad: true,
      model: w_model,
      proxy: {
        type: 'ajax',
        url: '/public/GetData.php?type=' + type,
        reader: {
          type: 'json',
          root: 'items'
        }
      }
    });
    var w_chart = Ext.create('Ext.chart.Chart',
        {
          animate: true,
          shadow: true,
          store: w_store,
          axes: [{
            type: 'Numeric',
            position: 'left',
            fields: [model_y_label],
            label: {
              renderer: Ext.util.Format.numberRenderer('0,0')
            },
            title: count_title,
            grid: true,
            minimum: 0
          }, {
            type: 'Category',
            position: 'bottom',
            fields: [model_x_label],
            title: field_title
          }],
          series: [{
            type: 'column',
            axis: 'left',
            highlight: true,
            tips: {
              trackMouse: true,
              width: 100,
              height: 28,
              renderer: function(storeItem, item) {
                fake_use(item);
                this.setTitle(storeItem.get(model_x_label) + ': ' +
                    storeItem.get(model_y_label));
              }
            },
            label: {
              display: 'insideEnd',
              'text-anchor': 'middle',
              field: model_y_label,
              renderer: Ext.util.Format.numberRenderer('0'),
              orientation: 'horizonal'
              // color: '#333'
            },
            xField: model_x_label,
            yField: model_y_label
          }]
          // renderTo: loc,
        });
    Ext.create('Ext.panel.Panel', {
      title: box_title, // title of the panel
      height: 400, // without this the panel will have height of 0 which is not
      // good
      border: true, // lets have a border to see where the panel deliniates
      layout: 'fit', // this causes the chart to be displayed in the entire
      // area of the panel
      items: [w_chart], // the chart inside the panel
      renderTo: loc
      // where to put the panel
    });
  });
}

function create_chart_here(type, model_x_label, model_y_label,
    box_title, count_title, field_title) {
  var loc = get_my_location();
  // the onReady is needed because inside the inner function we don't do it
  Ext.onReady(function() {
    create_chart(loc, type, model_x_label, model_y_label,
        box_title, count_title, field_title);
  });
}
