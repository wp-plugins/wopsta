function show_visitor() {
	new Ext.Window({
		id: 'show_visitor_window',
		title: 'Visitor Details',
		resizable: false,
		constrain: true,
		modal: true,
		height: 400,
		width: 550,
		border: false,
		frame: true,
		layout: 'table',
		layoutConfig: {
			columns: 2
		},
		items:[{
			width: 269,
			height: 180,
			frame: true,
			border: false,
			items: [new Ext.form.FormPanel({
				id: 'visitor_name_form_panel',
				border: false,
				labelWidth: 70,
				items: {
					xtype:'fieldset',
			        title: 'About the Visitor',
					border: false,
			        autoHeight:true,
					items: [{
						xtype: 'textfield',
						width: 170,
						fieldLabel: 'Name',
						name: 'name',
						msgTarget: 'side',
						enableKeyEvents: true,
						listeners: {
							specialkey: function(field, el){
								if (el.getKey() == Ext.EventObject.ENTER) 
									update_visitor_name();
							}
						}
					}, {
						xtype: 'textfield',
						width: 170,
						fieldLabel: 'Browser',
						name: 'browser',
						disabled: 'true'
					}, {
						xtype: 'textfield',
						width: 170,
						fieldLabel: 'Platform',
						name: 'operating_system',
						disabled: 'true'
					}]
				}
			})]
		}, {
			width: 269,
			height: 180,
			frame: true,
			border: false,
			items: [new Ext.form.FormPanel({
				id: 'visitor_statistics_form_panel',
				border: false,
				labelWidth: 95,
				items: [{
					xtype:'fieldset',
			        title: 'General Statistics',
					border: false,
			        autoHeight:true,
					items: [{
						xtype: 'textfield',
						width: 145,
						fieldLabel: 'Total Visits',
						name: 'total_visits',
						disabled: 'true'
					}, {
						xtype: 'textfield',
						width: 145,
						fieldLabel: 'Requests/Visit',
						name: 'request_rate',
						disabled: 'true'
					}]
				}, {
					xtype:'fieldset',
			        title: 'WordPress-Interaction',
					border: false,
			        autoHeight:true,
					items: [{
						xtype: 'textfield',
						width: 145,
						fieldLabel: 'Total Comments',
						name: 'total_comments',
						disabled: 'true'
					}, {
						xtype: 'textfield',
						width: 145,
						fieldLabel: 'Last Comment',
						name: 'last_comment',
						disabled: 'true'
					}]
				}]
			})]
		}, {
			colspan: 2,
			width: 538,
			height: 190,
			border: false,
			items: new Ext.grid.GridPanel({
				title: 'Last Visits',
				width: '100%',
				height: 190,
				store: new Ext.data.Store({}),
				columns: [
					{header: 'Time', dataIndex: 'request_date', width: 120, sortable: true, renderer: Ext.util.Format.dateRenderer('d.m.Y H:i:s')},
					{header: 'Total Requests', dataIndex: 'uuid', width: 80, sortable: true},
					{header: 'Total Requests', dataIndex: 'uuid', width: 80, sortable: true},
					{header: 'Total Requests', dataIndex: 'uuid', width: 80, sortable: true}
				]
			})
		}]
	}).show()
}
show_visitor();	