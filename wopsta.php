<?php
/*
Plugin Name: wopsta - WordPress Visitor Statistics
Plugin URI: http://www.wopsta.org
Description: A WordPress plug-in showing realtime visitor statistics.
Version: 0.1
Author: Andre Kolell, Lewis Dyer, Jan Stracke
Author URI: http://www.wopsta.org

Copyright (c) 2009 Andre Kolell, Lewis Dyer, Jan Stracke
Released under the GNU General Public License (GPL)
http://www.gnu.org/licenses/gpl.txt
*/

// Prohibit direct Call
if(preg_match('#'.basename(__FILE__).'#', $_SERVER['PHP_SELF'])) { 
	die('403 Forbidden'); 
}

// Load wopsta
if(!class_exists('wopstaLoader')) {
	
	/**
	 * Initialize the wopsta plugin
	 *
	 * @package Wopsta
	 * @since 0.1
	 */
	class wopstaLoader {

		/**
		 * This plugin's options.
		 *
		 * @since 0.1
		 * @var array
		 */
		public $options;
	
		/**
		 * Hold an instance of the Script loader Facotry
		 * 
		 * @var
		 * @since 0.1
		 */
		var $scripts_factory_class;
	
	
		/**
		 * Constructor for the instantiation of the wopsta plugin
		 *
		 * @since 0.1
		 */
		function __construct() {
			
			// Initialization
			$this->init_constants();
			$this->init_options();
			$this->init_database();

			// Require initialized constants
			$this->init_language();		
			$this->init_dependencies();
			
			// Register Activation-, Deactivation- and Uninstall-Methods
			register_activation_hook(WOPSTA_FOLDER.'/wopsta.php', array(&$this, 'activate'));
			register_deactivation_hook(WOPSTA_FOLDER.'/wopsta.php', array(&$this, 'deactivate'));	
			
			// Start wopsta
			add_action('plugins_loaded', array(&$this, 'start'));
		}
			
		/**
		 * Initialize some constants
		 *
		 * @since 0.1
		 */
		private function init_constants() {
			define('WOPSTA_FOLDER', plugin_basename(dirname(__FILE__)));		
			define('WOPSTA_ABSOLUTE_PATH', str_replace("\\","/", WP_PLUGIN_DIR.'/'.plugin_basename(dirname(__FILE__)).'/'));
			define('WOPSTA_URL_PATH', WP_PLUGIN_URL.'/'.plugin_basename(dirname(__FILE__)).'/');
			define('WOPSTA_VERSION', '0.1');
		}
		
		/**
		 * Load the language definied in WPLANG in wp-config.php
		 *
		 * @since 0.1
		 */
		private function init_language() {
			load_plugin_textdomain('wopsta', false, WOPSTA_FOLDER.'/i18n');
		}
		
		/**
		 * Make the wopsta settings available
		 *
		 * @since 0.1
		 */
		private function init_options() {
			$this->options = get_option('wopsta_options');
		}
		
		/**
		 * Initialize dependencies
		 *
		 * @since 0.1
		 */
		private function init_dependencies() {
			include_once('lib/functions.php');
			include_once('lib/webservice_client.php');
			include_once('model/dao.php');
			include_once('model/request.php');
			include_once('model/request_dao.php');
			include_once('model/visit.php');
			include_once('model/visit_dao.php');
			include_once('model/visitor.php');
			include_once('model/visitor_dao.php');
			include_once('model/configuration/script_component_factory.php');
			include_once('admin/wopsta_panel_group.php');
		}
		
		/**
		 * Initialize database
		 *
		 * @since 0.1
		 */
		private function init_database() {
			
			global $wpdb;
			
			$wpdb->wopsta_lib = $wpdb->prefix.'wopsta_lib';
			$wpdb->wopsta_request = $wpdb->prefix.'wopsta_request';
			$wpdb->wopsta_visit = $wpdb->prefix.'wopsta_visit';
			$wpdb->wopsta_visitor = $wpdb->prefix.'wopsta_visitor';
		}
		
		/**
		 * Activate the wopsta plugin
		 *
		 * @since 0.1
		 */
		public function activate() {
			include_once(dirname(__FILE__).'/install.php');
			wopsta_install();
		}
	
		/**
		 * Deactivate the wopsta plugin 
		 * (does not delete database contents!)
		 *
		 * @since 0.1
		 */
		function deactivate() { }
	
		/**
		 * Track a Request
		 * 
		 * @since 0.1
		 */
		function track() {
			include_once('tracker.php');
			$wopstaTracker = new wopstaTracker();	
		}
					
		/**
		 * Process the current request
		 *
		 * @since 0.1
		 */
		function start() {
			
			// Track
			if(!is_admin()) {
				add_action('wp_footer', array(&$this, 'track'));
				
			// Administration
			} else {
				
				// Include AJAX
				include_once('admin/ajax.php');
				
				include_once('admin/admin.php');
				$wopstaAdmin = new wopstaAdmin();
			}	
		}
		
		/**
		 * Load an instance of the singlton script factory
		 * 
		 * @return {script_factory} 
		 * @since 0.1
		 */
		function loadScriptFactory() {
			if( $this->scripts_factory_class  instanceof  script_component_factory) {
				return $this->scripts_factory_class;	
			} else {
				$this->scripts_factory_class = new script_component_factory();
			}
			return $this->scripts_factory_class;
		}
	}
	
	// Initialize debugging
	include_once('lib/log.php');
	$GLOBALS['wopstaLog'] = wopstaLog::getInstance();
	
	// Start wopsta
	$GLOBALS['wopsta'] = new wopstaLoader();
	
	$GLOBALS['wopstaOption'] = 'wopsta_options';
}
?>