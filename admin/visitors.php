<?php 
/**
 * Show the visitors page in the wopsta administration panel
 *
 * @since 0.1
 */
class wopsta_admin_visitors extends Wopsta_panel_group {

    /**
     * List of Panel Id's
     *
     * @var
     */
    var $panels_list = array('WOPSTA.panels.visits_and_pageviews_panel','WOPSTA.panels.gdpanel');

    
    public function __construct($dashboard) {
    
        // if dashboard load object from config
        
        //if config is empty get default dashboards
        
        //call parent
        parent::__construct($this->panels_list);

        
    }

    
    public function render() {
        echo '<div class="wrap">';
        echo '<h2>'.__('Visitor Statistics', 'wopsta').'</h2>';
        echo $this->render_markup();
        echo '</div>';
        echo '<script type="text/javascript">';
        echo $this->render_js();
        echo 'Ext.onReady(function() {';
        echo $this->render_execution_js();
        echo '});</script>';
    }

    
}
/* End of file  dashboard.php */