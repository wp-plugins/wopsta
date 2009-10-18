Ext.namespace('WOPSTA.panels');

WOPSTA.visits_and_pageviews_fields = [
	{name: 'interval', mapping: 'interval'},
	{name: 'visits', mapping: 'visits', type: 'int'},
	{name: 'pageviews', mapping: 'pageviews', type: 'int'}
];
			
WOPSTA.visits_and_pageviews_remote_json_store = new Ext.data.JsonStore({
	id: 'visits_and_pageviews_remote_json_store',
	fields: WOPSTA.visits_and_pageviews_fields,
	url: '<?php bloginfo('wpurl'); ?>/wp-admin/admin-ajax.php?action=wopsta_get_visits_and_pageviews',
	root: 'records',
	autoLoad: {
		params: {
			from: '<?php echo date("Y-m-d", mktime(0, 0, 0, date("m")-1, date("d"), date("Y"))); ?>',
			to: '<?php echo date("Y-m-d"); ?>'
		}		
	}
});

WOPSTA.panels.visits_and_pageviews_panel = new Ext.Panel({
    width: '100%',
    height: 300,
    layout: 'fit',
    items: {
		xtype: 'columnchart',
		url: '<?php echo WOPSTA_URL_PATH; ?>/lib/ext/resources/charts.swf',
		store: WOPSTA.visits_and_pageviews_remote_json_store,
		yField: 'pageviews',
		xField: 'interval',
		tipRenderer : function(chart, record, index, series){
		    if(series.yField == 'visits'){
		        return Ext.util.Format.number(record.data.visits, '0,0') + ' Visits';
		    } else {
		        return Ext.util.Format.number(record.data.pageviews, '0,0') + ' Pageviews';
		    }
		},
		yAxis: new Ext.chart.NumericAxis({
            labelRenderer : Ext.util.Format.numberRenderer('0,0')
        }),
		chartStyle: {
		    padding: 10,
		    animationEnabled: true,
		    font: {
		        name: 'Tahoma',
		        color: 0x444444,
		        size: 11
		    },
		    dataTip: {
		        padding: 5,
		        border: {
		            color: 0x99bbe8,
		            size:1
		        },
		        background: {
		            color: 0xDAE7F6,
		            alpha: .9
		        },
		        font: {
		            name: 'Tahoma',
		            color: 0x15428B,
		            size: 10,
		            bold: true
		        }
		    },
		    xAxis: {
		        color: 0x69aBc8,
		        majorTicks: {color: 0x69aBc8, length: 4},
		        minorTicks: {color: 0x69aBc8, length: 2},
		        majorGridLines: {size: 1, color: 0xeeeeee},
                labelRotation: -90
		    },
		    yAxis: {
		        color: 0x69aBc8,
		        majorTicks: {color: 0x69aBc8, length: 4},
		        minorTicks: {color: 0x69aBc8, length: 2},
		        majorGridLines: {size: 1, color: 0xdfe8f6}
		    }
		},
		series: [{
		    type: 'column',
		    displayName: 'Pageviews',
		    yField: 'pageviews',
		    style: {
		        image: '<?php echo WOPSTA_URL_PATH; ?>/icons/bar.gif',
		        mode: 'stretch',
		        color:0x99BBE8
		    }
		},{
		    type:'line',
		    displayName: 'Visits',
		    yField: 'visits',
		    style: {
		        color: 0x15428B
		    }
		}]
    }
});