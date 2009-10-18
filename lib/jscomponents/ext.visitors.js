Ext.namespace('WOPSTA.panels');

WOPSTA.lastVisitorsRecordFields = [
			{name: 'time', mapping: 'time', type: 'date', dateFormat: 'Y-m-d H:i:s'},
			{name: 'title', mapping: 'title'},
			{name: 'total_requests_during_visit', mapping: 'total_requests_during_visit'},
			{name: 'previous_visits', mapping: 'previous_visits'}
		];
			
		WOPSTA.lastVisitorsRemoteJsonStore = new Ext.data.JsonStore({
			fields: WOPSTA.lastVisitorsRecordFields,
			url: '<?php bloginfo('wpurl'); ?>/wp-admin/admin-ajax.php?action=wopsta_last_visitors',
			totalProperty: 'totalCount',
			root: 'records',
			id: 'lastVisitorsRemoteStore',
			autoLoad: false,
			remoteSort: true
		});
		
		WOPSTA.lastVisitorsColumnModel = [
			{
				header: 'Time',
				dataIndex: 'time',
				sortable: true,
				width: 120,
				resizable: false,
				hideable: false,
				renderer: Ext.util.Format.dateRenderer('Y-m-d H:i:s')
			},{
				header: 'Title',
				dataIndex: 'title',
				sortable: true,
				width: 160,
				resizable: true,
				hideable: true
			},{
				header: 'Total Requests during Visit',
				dataIndex: 'total_requests_during_visit',
				sortable: false,
				width: 160,
				resizable: false,
				hideable: true
			},{
				header: 'Previous Visits',
				dataIndex: 'previous_visits',
				id: 'lastVisitorsPreviousVisitsColumn',
				sortable: false,
				width: 100,
				resizable: false,
				hideable: true
			}
		];
		
		WOPSTA.lastVisitorsPagingToolbar = {
			xtype: 'paging',
			store: WOPSTA.lastVisitorsRemoteJsonStore,
			pageSize: 5,
			displayInfo: true
		};
		
		WOPSTA.panels.gdpanel = new Ext.grid.GridPanel({
			height: 187,
			columns: WOPSTA.lastVisitorsColumnModel,
			store: WOPSTA.lastVisitorsRemoteJsonStore,
			loadMask: true,
			bbar: WOPSTA.lastVisitorsPagingToolbar,
			autoExpandColumn: 'lastVisitorsPreviousVisitsColumn'
		});
		
		Ext.StoreMgr.get('lastVisitorsRemoteStore').load({
			params: {
				start: 0,
				limit: 5
				}
			});