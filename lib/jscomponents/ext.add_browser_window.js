function add_browser_via_webservice() {
	new Ext.Window({
		id: 'add_browser_via_webservice_window',
		title: 'Add Browser to wopsta.org Library',
		resizable: false,
		constrain: true,
		modal: true,
		height: 210,
		width: 350,
		border: false,
		layout: 'fit',
		items: new Ext.form.FormPanel({
			id: 'add_browser_via_webservice_form_panel',
			buttonAlign: 'center',
			frame: true,
			items: [{
				xtype: 'textfield',
				width: 200,
				fieldLabel: 'Browser',
				name: 'browser',
				msgTarget: 'side',
				allowBlank: true
			}, {
				xtype: 'panel',
				frame: false,
				html: '<div style="padding:0 0 8px 0; font-size: 11px;">You can propose a title for the missing Browser, e.g. <i>Firefox 3.5</i>.</div>'
			}, {
				xtype: 'textarea',
				width: 200,
				fieldLabel: 'User Agent',
				name: 'user_agent',
				msgTarget: 'side',
				allowBlank: false,
				blankText: 'You need to provide an User Agent.'
			}, {
				xtype: 'panel',
				frame: false,
				html: '<div style="padding:0 0 8px 0; font-size: 11px;">You need to provide an User Agent as an Example.</div>'
			}],
			buttons: [{
				text: 'Add',
				handler: function(){
					if (Ext.getCmp('add_browser_via_webservice_form_panel').getForm().isValid()) {
						var tmp = Ext.getCmp('add_browser_via_webservice_form_panel');
						Ext.getCmp('add_browser_via_webservice_form_panel').getForm().submit({
							url: '<?php bloginfo('wpurl'); ?>/wp-admin/admin-ajax.php?action=wopsta_add_browser_via_webservice',
							waitMsg: 'Uploading and Verifying your Data.',
							success: function(tmp, o){
								Ext.getCmp('add_browser_via_webservice_window').destroy();
								Ext.Msg.show({
									title: 'Success!',
									msg:  'The Browser has been successfully added to the wopsta.org Library.',
									buttons: Ext.Msg.OK,
									icon: Ext.MessageBox.INFO
								});
							},
							failure: function(tmp, o){
								Ext.Msg.show({
									title: 'An Error occured!',
									msg: o.result.errormsg,
									buttons: Ext.Msg.OK,
									icon: Ext.MessageBox.ERROR
								});
							}
						});
					}
				}
			}, {
				text: 'Reset',
				handler: function(){
					Ext.getCmp('add_browser_via_webservice_panel').getForm().reset();
				}
			}, {
				text: 'Abort',
				handler: function(){
					Ext.getCmp('add_browser_via_webservice_window').destroy();
				}
			}]
		})
	}).show()
}
add_browser_via_webservice();