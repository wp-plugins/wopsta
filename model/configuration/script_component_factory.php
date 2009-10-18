<?php 
/**
 * @author Lewis Dyer
 * @version 0.1
 * @classDescription Data Factory for Loading Javascript Components Script
 */
Class script_component_factory {

    private static $xml_Dom;
    
    private static $xml_file = 'jsComponents.xml';
    
    /**
     * @constructor
     * @return
     * @param file name to the config object
     */
    function __construct($filename = '') {
    
        // load the dataclass
        require_once ("script_component.php");
        
        if (! empty($filename)) {
            self::$xml_Dom = simplexml_load_file(dirname(__FILE__).'\\'.$filename);
            self::$xml_file = $filename;
        } else {
            self::$xml_Dom = simplexml_load_file(dirname(__FILE__).'\\'.self::$xml_file);
        }
        
    }
    
	/**
	 * Create a new component
	 * @param simplexml object $component
	 * @return 
	 */
	private static function newComponent($component){
		
	   $return = new script_component(self::$xml_file);
                $return->setFilename($component->jsfile);
                $return->setTitle($component->title);
				$return->setIntjs($component->intjs);
                $return->setDashboard($component['dashboard']);
                $return->setId($component['id']);
                $return->setIntmarkup($component->intmarkup);
                $return->setName($component->name);
                
                $ary = array();
                
                if (count($return->dependenicies) > 0) {
                    foreach ($return->dependenicies->dependent as $dependent) {
                        array_push($ary, array('filename'=>$dependent->filename, 'name'=>$dependent->name));
                    }
                    $return->dependicies($ary);
                }
		return $return;
		
	}
	
    /**
     * Return a component by id
     *
     * @param  {integer} $id of the component
     * @return {ScriptComponent}
     * @method getComponentById
     * @memberOf ScriptComponentFactory
     */
    public static function getComponentById($id) {
    
        $return = false;
        
        foreach (self::$xml_Dom->component as $component) {
            $test = $component['id'];
            if ($test == $id) {
              return self::newComponent($component);
            }
        }
        
        return $return;
    }
    
    /**
     * Return a component by name
     *
     * @param  {integer} $id of the component
     * @return {ScriptComponent}
     * @method getComponentByName
     * @memberOf ScriptComponentFactory
     */
    public static function getComponentByName($name) {
    	$return = false;
        foreach (self::$xml_Dom->component as $component) {
            $test = (string)$component->name;
           
		   if (strcasecmp(trim($test), trim($name)) == 0){
             
			 return self::newComponent($component);
            }
        }
        
        return $return;
	}
    
    /**
     * Create an empty ScriptComponent
     *
     * @return {ScriptComponent}
     * @method  getNewComponent
     * @memberOf ScriptComponentFactory
     */
    public static function getNewComponent() {
    
        return new script_component(self::$xml_file);
    }
    
    /**
     * Return an array of ScriptComponent that are default for the dashboard
     *
     * @return array(ScriptComponent)
     * @memberOf ScriptComponentFactory
     * @method getDashBoardComponents
     */
    public static function getDashBoardComponents_Names() {
    	$return = array();
        foreach (self::$xml_Dom->component as $component) {
            $test = (bool)$component['dashboard'];
            if ($test == true) {
             $return[] = (string)$component->name;
            }
        }
        
		return $return;
		
    }
}
?>
