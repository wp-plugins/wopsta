<?php  
/**
 * wopsta.org web service Client
 * 
 * Every Method of this Client returns an associative Array containing the Key 'success'.
 * If 'success' is false, an 'errormsg' is delivered as well.
 *
 * @package wopsta
 * @since 0.1
 */
class wopstaWebserviceClient {
	
	/**
	 * The IXR-Client
	 *
	 * @since 0.1
	 * @access private
	 * @var Object IXR-Client
	 */
	private $client;
	
	/**
	 * Information about this wopsta Plug-in Instance
	 *
	 * @since 0.1
	 * @access private
	 */
	private $info;
	
	/**
	 * A reference to WordPress' database connection.
	 *
	 * @since 0.1
	 * @access private
	 * @var Object WordPress database connection
	 */
	private $wpdb;
	
	
	
	/**
	 * Constructor.
	 *
	 * @since 0.1
	 */
	function __construct() {
		
		global $wpdb;
		$this->wpdb =& $wpdb;

		include_once("../wp-includes/class-IXR.php");
		$this->client = new IXR_Client('http://127.0.0.1/wopsta_webservice/index.php');
		
		// Some general information
		$this->info = array('wp_version' => $GLOBALS['wp_version'], 'url' => get_option('siteurl'), 'wopsta_version' => WOPSTA_VERSION);

	}
	
	
	
	/**
	 * Update the client's library
	 *
	 * @since 0.1
	 */
	public function update_client_library() {
		
		// Ask for client library updates by providing necessary information
		$latest_change = ($this->wpdb->get_var("SELECT MAX(id_change) FROM ".$this->wpdb->wopsta_lib) > 0 ? $this->wpdb->get_var("SELECT MAX(id_change) FROM ".$this->wpdb->wopsta_lib) : 0);
		if(!$this->client->query('library.update', array('info' => $this->info, 'latest_change' => $latest_change))) {
  			 return array('success' => false, 'errormsg' => 'Error '.$this->client->getErrorCode().': '.$this->client->getErrorMessage());
		}
		$response = $this->client->getResponse();
		if(!$response['success']) { return array('success' => false, 'errormsg' => $response['errormsg']); }
		
		// Get some Information that the service has delivered
		$log = $response['log'];

		// Update user agents, referrer, search phrases, etc.
		$added = 0;
		$updated = 0;
		foreach ($response['changes'] as $entry) {
			if($this->wpdb->get_var("SELECT COUNT(*) FROM ".$this->wpdb->wopsta_lib." WHERE id = '".$entry['id']."'") > 0) {
				$this->wpdb->update($this->wpdb->wopsta_lib, array('id_parent' => $entry['id_parent'],
																   'type' => $entry['type'], 
																   'value' => $entry['value'], 
																   'id_change' => $entry['id_change']),
															 array('id' => $entry['id']));
				$updated++;
			} else {
				$this->wpdb->insert($this->wpdb->wopsta_lib, array('id' => $entry['id'], 
																   'id_parent' => $entry['id_parent'],
																   'type' => $entry['type'], 
																   'value' => $entry['value'], 
																   'id_change' => $entry['id_change']));
				$added++;
			}
		}
		array_push($log, 'Added '.$added.' rows and updated '.$updated.' rows of your client library.');
		
		// If there have been updates, try to update requests with unknown browsers and/or operating systems
		$updated = 0;
		if(($added > 0) || ($udated > 0)) {
			$RequestDao = wopstaDaoFactory::get_factory()->get_request_dao();
			$requests = $RequestDao->get_requests_with_unknown_browser_or_os();
			array_push($log, 'Found '.count($requests).' requests with unknown browser or operating system.');
			foreach($requests as $Request) {
				if(($Request->get_id_browser() == 0) && ($Request->identify_browser() > 0)) {
					$Request->set_id_browser($Request->identify_browser());
					$RequestDao->update($Request);
					$updated++;
				}
				if(($Request->get_id_os() == 0) && ($Request->identify_os() > 0)) {
					$Request->set_id_os($Request->identify_os());
					$RequestDao->update($Request);
					$updated++;
				}
			}
			array_push($log, $updated.' browsers and/or operating systems of existing requests have been updated.');
		}
		
		// There is something bad going on:
		// - Visitor's last used browser and operating system is not updated
		
		return array('success' => true, 'log' => $log);
	}
	
	
	
	/**
	 * Publish user agents with unknown browsers and/or operating systems
	 *
	 * @since 0.1
	 */
	public function publish_user_agents() {
		
		// Default for $log
		$log = 'No unknown User Agents have been published.';
		
		// Identify user agents with unknown browsers and/or operating systems
		$results = $this->wpdb->get_results("SELECT id_browser, id_os, user_agent 
											 FROM ".$this->wpdb->wopsta_request." 
											 WHERE id_browser = 0 OR id_os = 0 
											 GROUP BY user_agent", 'ARRAY_A');

	 	// Send those user agents (if there are some...)
		if(count($results) > 0) {
			if(!$this->client->query('library.publish_auto', array('info' => $this->info, 'unknown' => $results))) {
	  			 return array('success' => false, 'errormsg' => 'Error '.$this->client->getErrorCode().': '.$this->client->getErrorMessage());
			}
			$response = $this->client->getResponse();
			if(!$response['success']) { return array('success' => false, 'errormsg' => $response['errormsg']); }
			$log = 'Published '.count($results).' unknown user agents.';
		}
		
		return array('success' => true, 'log' => $log);
	}
	
	
	
	/**
	 * Publish a manually entered browser
	 *
	 * @since 0.1
	 */
	public function publish_browser_man($args) {
		if(!$this->client->query('library.publish_browser_man', array('info' => $this->info, 'browser' => $args['browser'], 'user_agent' => $args['user_agent']))) {
	  		return array('success' => false, 'errormsg' => 'Error '.$this->client->getErrorCode().': '.$this->client->getErrorMessage());
		}
		return $this->client->getResponse();
	}
}
?>