<?php 
/**
 * Renders a Panel Group;
 *
 * @author Lewis Dyer
 * @version 0.1
 */
abstract class Wopsta_panel_group {
    /**
     * List of panel ids or names;
     *
     * @var
     */
    var $panels = array();
    
    /**
     * Path from the plugin root to the folder that holds the script
     *
     * @var
     */
    var $path = 'lib\\jscomponents\\';
    
    /**
     * @constructor
     * @param array(mixed[string|integer]) $panels_list - list of panels
     * @return
     */
    function __construct($panels_list) {
    
        $GLOBALS['wopsta']->loadScriptFactory();
        
        foreach ($panels_list as $panel) {
            if (is_integer($panel)) {
                $GLOBALS['wopstaLog']->log('getComponentById Called');
				$this->panels[] = script_component_factory::getComponentById($panel);
            } else {
               $GLOBALS['wopstaLog']->log('getComponentByName Called');
			   $this->panels[] = script_component_factory::getComponentByName($panel);
            }
        }
    }
    
	 /**
	  * Ouptus the components to the browser
	  * @return void 
	  */
	 abstract protected function render(); 
	
	
	/**
     * Output to the browser the javascript declaration file uing includes_once
     * @return string
     */
    function render_js() {
       ob_start();
	   foreach ($this->panels as $key=>$value) {
            if ($value instanceof script_component) {
                //render dependencies
                $dependicies = $value->getDependicies();
                foreach ($dependicies as $dependent) {
                  //  $this->firephp->log('dependent called');
					include_once (WOPSTA_ABSOLUTE_PATH.$this->path.$dependent['filename']);
                }
                //render normal
                
                //$this->firephp->log('file name called');
                include_once (WOPSTA_ABSOLUTE_PATH.$this->path.$value->getFilename());
                
            } else {
                $GLOBALS['wopstaLog']->error('Component at key - '.$key.'is not and instance of script_component');
            }
            
        }
     $text = ob_get_contents();
	 ob_clean();
	 
	 return $text;
	    
    }
    
    /**
     * Ouputs to the browser the in markup for a panel
     * @return string
     */
    function render_markup() {
       ob_start();
	    foreach ($this->panels as $key=>$value) {
            if ($value instanceof script_component) {
                //render dependencies
                echo $value->getIntmarkup();
            } else {
                $GLOBALS['wopstaLog']->error('Component at key - '.$key.'is not and instance of script_component');
            }
        }
	   $text = ob_get_contents();
	   ob_clean();
	   return $text;
    }
    
    /**
     * Outputs to browser the int js line of a panel
     * @return void
     */
    function render_execution_js() {
    	ob_start();
        foreach ($this->panels as $key=>$value) {
        
            if ($value instanceof script_component) {
                //render dependencies
                echo $value->getIntjs();
            } else {
                $GLOBALS['wopstaLog']->error('Component at key - '.$key.'is not and instance of script_component');
            }
        }
		$text = ob_get_contents();
	 	ob_clean();
	 	return $text;
    }
    
}
?>
