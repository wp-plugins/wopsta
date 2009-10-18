<?php 
class wopsta_admin_dashboard extends Wopsta_panel_group {
    /**
     * List of Panel Id's
     *
     * @var
     */
    var $panels_list = array();

    
    public function __construct($dashboard) {
    
        $GLOBALS['wopsta']->loadScriptFactory();
        $option = 'dashboard_plugins';
        $option_ary = get_option($GLOBALS['wopstaOption']);
        
        // if dashboard and config object exists load object from config
        if ($dashboard && isset($option_ary[$options])) {
        
            $this->panels_list = maybe_unserialize($option_ary[$option]);
            
        } else {
        
            $this->panels_list = script_component_factory::getDashBoardComponents_Names();
            //save object into
            $option_ary[$option] = maybe_serialize($this->panels_list);
            update_option($GLOBALS['wopstaOption'], $option_ary);
        }
        
        //call parent
        parent::__construct($this->panels_list);
        
    }

    
    public function render() {
    
	//turn on output buffering
	ob_start();
	
        echo '<div class="wrap"><h2>'.__('wopsta Statistiken', 'wopsta').'</h2></div>';
        
		echo '<div id="dashboard_container" style="width:99%; border:0px;"></div>';
		
        echo '<script type="text/javascript">';
        echo  $this->render_js();
        echo '</script>';
        
    ?>
    <script type="text/javascript">    
	Ext.onReady(function(){

    // NOTE: This is an example showing simple state management. During development,
    // it is generally best to disable state management as dynamically-generated ids
    // can change across page loads, leading to unpredictable results.  The developer
    // should ensure that stable state ids are set for stateful components in real apps.
    Ext.state.Manager.setProvider(new Ext.state.CookieProvider());
	
    // create some portlet tools using built in Ext tool ids
    var tools = [{
        id:'gear',
        handler: function(){
            Ext.Msg.alert('Message', 'The Settings tool was clicked.');
        }
    },{
        id:'close',
        handler: function(e, target, panel){
        	//Ext.Msg.alert('before destroy called');
			//panel.ownerCt.remove(panel, true);
			
			console.log(panel.initialConfig.itemId);
			
			panel.hide(panel,true); 
        }
    }];
	
	<?php $count = count($this->panels)?>
	
	var mainPanel = new Ext.Panel({
        renderTo:'dashboard_container',
		layout:'fit',
        autoHeight:true,
		border:false,
		items:[{
            xtype:'portal',
            region:'center',
            border:false,
			margins:'15 5 5 0',
		    items:[{
                columnWidth:1,
                items:[<?php foreach($this->panels as $panel): 	
				   echo '{';
				   echo "title: '".__($panel->getTitle(), 'wopsta')."',"; 
				   echo "itemId:'".$panel->getName()."',";
				   echo 'tools: tools,';
                   echo 'items:['.$panel->getName().']'; 
			 	   if($count > 1)
				   {
				    echo '},';	
				   }
				   else
				   {
				   	echo '}';
				   }
				   $count = $count -1;
				endforeach;?>
				]
		    }]
            
            /*
             * Uncomment this block to test handling of the drop event. You could use this
             * to save portlet position state for example. The event arg e is the custom 
             * event defined in Ext.ux.Portal.DropZone.
             */
            ,listeners: {
                'drop': function(e){
                    Ext.Msg.alert('Portlet Dropped', e.panel.title + '<br />Column: ' + 
                        e.columnIndex + '<br />Position: ' + e.position);
                }
		    }
			
        }]
    });
});
		
</script>
<?php
   
   ob_end_flush();
   
   }//end render
}
/* End of file  dashboard.php */
