<?php
/**
 * wopsta Administration Class
 * 
 * This is the administration panel of the wopsta plugin.
 *
 * @package wopsta
 * @since 0.1
 */
class wopstaAdmin {
	
	/**
	 * Constructor for the instantiation of the wopsta administration panel
	 *
	 * @since 0.1
	 */
	function __construct() {
		
		// Add required Ext JS Stuff - WHEN NECESSARY
		if($this->inside_wopsta()) {
			function init_css() {
		    	wp_enqueue_style('ext-all', WOPSTA_URL_PATH.'lib/ext/resources/css/ext-all.css');
		    	wp_enqueue_style('ext-xtheme-blue', WOPSTA_URL_PATH.'lib/ext/resources/css/xtheme-blue.css');
		    	wp_enqueue_style('wopsta', WOPSTA_URL_PATH.'wopsta.css');
	  			wp_enqueue_style('ext-portal-css', WOPSTA_URL_PATH.'lib/ext/portal/portal.css');
			}
			add_action('admin_print_styles', 'init_css');
			
		    function init_js() {
				wp_enqueue_script('ext-jquery-adapter', WOPSTA_URL_PATH.'lib/ext/adapter/jquery/ext-jquery-adapter.js', array('jquery'));
				wp_enqueue_script('ext-all-debug', WOPSTA_URL_PATH.'lib/ext/ext-all-debug.js', array('jquery'));
				wp_enqueue_script('ext-portal-js', WOPSTA_URL_PATH.'lib/ext/portal/Portal.js');
				wp_enqueue_script('ext-portallet-js', WOPSTA_URL_PATH.'lib/ext/portal/Portlet.js');
				wp_enqueue_script('ext-portalcolumn-js', WOPSTA_URL_PATH.'lib/ext/portal/PortalColumn.js');
			}
			add_action('admin_print_scripts', 'init_js');
			
			add_filter('contextual_help', array(&$this, 'show_help'), 10, 2);
			
		}
		
		// Add the wopsta menu to the admnisistration panel menu
		add_action('admin_menu', array(&$this, 'add_menu'));
	}

	/**
	 * The constructor of this class initializes the generation of the menu in the administration panel.
	 *
	 * @since 0.1
	 */
	public function add_menu()  {
		add_menu_page(__('Statistics', 'wopsta'), __('Statistics', 'wopsta'), 'wopsta Access statistics', WOPSTA_FOLDER, array (&$this, 'show'), WOPSTA_URL_PATH.'icons/chart.png');
		add_submenu_page(WOPSTA_FOLDER, __('Dashboard', 'wopsta'), __('Dashboard', 'wopsta'), 'wopsta Access statistics', WOPSTA_FOLDER, array (&$this, 'show'));
		add_submenu_page(WOPSTA_FOLDER, __('Visitors', 'wopsta'), __('Visitors', 'wopsta'), 'wopsta Access statistics', 'wopsta_visitors', array (&$this, 'show'));
		add_submenu_page(WOPSTA_FOLDER, __('Origin', 'wopsta'), __('Origin', 'wopsta'), 'wopsta Access statistics', 'wopsta_origin', array (&$this, 'show'));
		add_submenu_page(WOPSTA_FOLDER, __('Contents', 'wopsta'), __('Contents', 'wopsta'), 'wopsta Access statistics', 'wopsta_contents', array (&$this, 'show'));
		add_submenu_page(WOPSTA_FOLDER, __('Configuration', 'wopsta'), __('Configuration', 'wopsta'), 'wopsta Administrate', 'wopsta_configuration', array (&$this, 'show'));
		add_submenu_page(WOPSTA_FOLDER, __('About wopsta', 'wopsta'), __('About wopsta', 'wopsta'), 'wopsta Access statistics', 'wopsta_about', array (&$this, 'show'));
	}

	/**
	 * Are we inside wopsta AND do we need Ext JS?
	 *
	 * @since 0.1
	 * 
	 * @return boolean Returns true, if this request requires to display a wopsta page
	 */
	private function inside_wopsta() {
		if(strstr($_GET['page'], 'wopsta')) {
			if(!in_array($_GET['page'], array('wopsta_configuration'))) {
				return true;
			}
		}
		return false;
	}

	/**
	 * This method is used to display each of the pages of the wopsta administration panel.
	 *
	 * @since 0.1
	 */
	public function show() {

		// Display the requested page
  		switch($_GET['page']) {
			case 'wopsta_about':
				include_once('about.php');
				wopsta_admin_about(); 
				break;
			case 'wopsta_authorization':
				include_once('authorization.php');
				wopsta_admin_authorization(); 
				break;
			case 'wopsta_configuration':
				include_once('configuration.php');
				wopsta_admin_configuration(); 
				break;
			case 'wopsta_visitors':
				include_once('visitors.php');
				$wopsta_admin_visitors = new wopsta_admin_visitors(false);
				$wopsta_admin_visitors->render();  
				break;
			case 'wopsta_dashboard':
			default:
				include_once('dashboard.php');
				$wopsta_admin_dashboard = new wopsta_admin_dashboard(true);
				$wopsta_admin_dashboard->render();
				break;
		}
	}

	/**
	 * This method is used to display appropriate help information inside the wopsta administration panel.
	 *
	 * @since 0.1
	 */	
	function show_help($help, $screen) {
		$prefix = strtolower(__('Statistics', 'wopsta'));
		$help  = '<h5>'.__('wopsta Help', 'wopsta').'</h5><div class="metabox-prefs">';
		switch($screen) {
			case 'toplevel_page_'.WOPSTA_FOLDER:
				$help .= 'main page help...';
			break;
			case $prefix.'_page_wopsta_authorization':
				$help .= 'about rights and roles in wopsta...';
			break;
			case $prefix.'_page_wopsta_configuration':
				$help .= __('On this page you can configure the wopsta plugin.', 'wopsta');
				$help .= '<br/>';
				$help .= sprintf(_c('You can also %1$suninstall wopsta%2$s.|this is a link to the uninstall page'), '<a href="admin.php?page=wopsta_uninstall">', '</a>');
			break;
			default:
				$help .= __('Sorry, but there\'s no help available for this page', 'wopsta').'.';
			break;
		}
		$help .= '<div style="margin-top:8px;">';
		$help .= sprintf(_c('For further assistance, please visit our website %1$swopsta.org%2$s or join our %3$smailing list%4$s at Google Groups.|these are links'), '<a href="http://www.wopsta.org" target="_blank">', '</a>', '<a href="http://groups.google.de/group/wopsta" target="_blank">', '</a>');
		$help .= '</div></div>';
		return $help;
	}
}
?>