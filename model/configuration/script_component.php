<?php 
/**
 * @author Lewis Dyer
 * @version 0.1
 * @classDescription Data object for a javascript script Component.
 */
class script_component {    
    /**
     * File name of the javascript file
     * 
     * @var {string} 
     */
    var $filename;
    
    /**
     * Unique Id of the component
     * 
     * @var {int} 
     */
    var $id = 0;
    
    /**
     * String that will initiate the javascript
     * @var {string} 
     */
    var $intjs;
    
    /**
     * Unique name of the component
     * 
     * @var {string} 
     */
    var $name;
    
    /**
     * HTML Markup that will hold the component
     * 
     * @var {string} 
     *
     */
    var $intmarkup;
    
    /**
     * Is the component on the dashboard
     * 
     * @var {boolean} 
     */
    var $dashboard = false;
    
	/**
	 * Location of xml file
	 * 
	 * @var {string} 
	 */
	var $xmlfile = false;
	
	/**
	 * Array of dependent files
	 * 
	 * @var {array(string)} 
	 */
	var $dependicies = array();
	
	/**
	 * Title for the component
	 * 
	 * @var {string}
	 */
	var $title;
	
	
	/**
     * Returns $title.
     *
     * @see script_component::$title
     */
    public function getTitle() {
        return trim($this->title);
    }
    
    /**
     * Sets $title.
     *
     * @param object $title
     * @see script_component::$title
     */
    public function setTitle($title) {
        $this->title = trim($title);
    }
	
		
	  /**
     * Returns $dashboard.
     *
     * @see ScriptComponent::$dashboard
     */
    public function getDashboard() {
        return $this->dashboard;
    }
    
    /**
     * Sets $dashboard.
     *
     * @param object $dashboard
     * @see ScriptComponent::$dashboard
     */
    public function setDashboard($dashboard) {
        $this->dashboard = $dashboard;
    }
    
    /**
     * Returns $filename.
     *
     * @see ScriptComponent::$filename
     */
    public function getFilename() {
        return $this->filename;
    }
    
    /**
     * Sets $filename.
     *
     * @param object $filename
     * @see ScriptComponent::$filename
     */
    public function setFilename($filename) {
        $this->filename =  trim($filename);
    	
	}
    
    /**
     * Returns $id.
     *
     * @see ScriptComponent::$id
     */
    public function getId() {
        return $this->id;
    }
    
    /**
     * Sets $id.
     *
     * @param object $id
     * @see ScriptComponent::$id
     */
    public function setId($id) {
        $this->id = $id;
    }
    
    /**
     * Returns $intjs.
     *
     * @see ScriptComponent::$intjs
     */
    public function getIntjs() {
        return stripcslashes($this->intjs);
    }
    
    /**
     * Sets $intjs.
     *
     * @param object $intjs
     * @see ScriptComponent::$intjs
     */
    public function setIntjs($intjs) {
        $this->intjs = trim(htmlentities($intjs));
    }
    
    /**
     * Returns $intmarkup.
     *
     * @see ScriptComponent::$intmarkup
     */
    public function getIntmarkup() {
        return   stripcslashes(html_entity_decode($this->intmarkup,ENT_QUOTES));
    }
    
    /**
     * Sets $intmarkup.
     *
     * @param object $intmarkup
     * @see ScriptComponent::$intmarkup
     */
    public function setIntmarkup($intmarkup) {
        $this->intmarkup = trim(htmlentities($intmarkup));
    }
    
    /**
     * Returns $name.
     *
     * @see ScriptComponent::$name
     */
    public function getName() {
        return trim($this->name);
    }
    
    /**
     * Sets $name.
     *
     * @param object $name
     * @see ScriptComponent::$name
     */
    public function setName($name) {
        $this->name = trim($name);
    }
    
    /**
     * Returns $xmlfile.
     *
     * @see ScriptComponent::$xmlfile
     */
    public function getXmlfile() {
        return html_entity_decode(($this->xmlfile));
    }
    
    /**
     * Sets $xmlfile.
     *
     * @param object $xmlfile
     * @see ScriptComponent::$xmlfile
     */
    public function setXmlfile($xmlfile) {
        $this->xmlfile = trim(htmlentities($xmlfile));
    }
	
	
	/**
     * Returns $dependicies.
     *
     * @see ScriptComponent::$dependicies
     */
    public function getDependicies() {
        return $this->dependicies;
    }
    
    /**
     * Sets $dependicies.
     *
     * @param object $dependicies
     * @see ScriptComponent::$dependicies
     * @return boolean true if varaible st false if error
     */
    public function setDependicies($dependicies) {
        $set = false;
		if(is_array($dependicies))
		{
			$this->dependicies = $dependicies;
			$set = true;	
		}
		
		return $set;
    }
    
	
	
    /**
     * @constructor
     */
    function __construct($xmlfile) {
    	$this->xmlfile = $xmlfile;
    }
    
	/*
	function __get($propName) {
        $vars = array('dashboard','id','intjs','intmarkup','name','filename','xmlfile');
        if (in_array($propName, $vars)) {
            return $this->$propName;
        } else {
        	$error = "Invalid Property $propName";
            $GLOBALS['wopstaLog']->error($error);
			throw new Exception($error);
	    }
    }  */ 
	
	function save($id = 0)
	{
		if($id === 0)
		{
			//new object
			$output = '<component id="'.$this->id.'" dashboard="'.$this->dashboard.'">';//root node start 
			$ouptut .= "<name>$this->name</name>";
			$output .= "<title>$this->title</title>";
			$output .= "<intjs>$this->intjs</intjs>";
			$output .= "<jsfile>$this->filename</jsfile>";
			$output .= "<intmarkup>$this->intmarkup</intmarkup>";
			
			if(count($this->dependicies) > 0 )
			{
				$output .= '<dependicies>';
				
				
				foreach($this->dependicies as $dependent)
				{
					
					$output .= '<dependent>';
					$output .='<filename>';
					$output .= $dependent['filename'];
					$output .= '</filename>';
					$output .= '<name>';
					$output .= $dependent['name'];
					$output .= '</name>';
					$output .= '</dependent>';
				}
			
				$output .= '</dependicies>';
				
			} 
			else
			{
				$output .= '<dependicies />';	
			}
			
			$output .= "</component>"; //root node end
			
			$xmldocument = new DOMDocument();
			$xmldocument->load($this->xmlfile);
			
			$component = $xmldocument->createDocumentFragment();
			$component->appendXML($output);
			
			$xmldocument->documentElement->appendChild($component);
			$xmldocument->save($this->xmlfile);
				
		}
		else
		{
			//update the object
			
			$document = new DOMDocument();
			$document->load($this->xmlfile);
			
			$updated = false;
			
			$components = $document->$doc->getElementsByTagName("component");
			foreach ($components AS $component)
			  {
					$attributes = $component->attributes;
					if($attributes['id'] === $id)
					{
						
						
						
						
						break; // node found
						$updated = true;
					}  	
			  }
			
		}
	}
	
	/**
	 * out the data as html
	 * @return  {string}
	 */
	function __toString()
	{
		$fileds = get_object_vars($this);
		$output = '<p>';
		
		foreach ($fileds as $key => $value)
		{
			$output .= "$key: $value <br />";	
		}
		
		$output .= '</p>';
		
		return (string)$output;
		
	}
	
	
}
?>
