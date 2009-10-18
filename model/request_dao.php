<?php  
/**
 * wopsta request data access object
 *
 * @package wopsta
 * @since 0.1
 */
class wopstaRequestDao {
	
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
	 * @param Object WordPress database connection
	 */
	function __construct() {
		global $wpdb;
		$this->wpdb =& $wpdb;
	}
	
	/**
	 * Add a request to the database
	 *
	 * @since 0.1
	 * @return boolean true
	 */
	public function add(wopstaRequest $Request) {
		$this->wpdb->insert($this->wpdb->wopsta_request, 
			array('id_browser' => $Request->get_id_browser(), 
				  'id_os' => $Request->get_id_os(),
				  'id_searchengine' => $Request->get_id_searchengine(), 
				  'id_spider' => $Request->get_id_spider(), 
				  'id_visit' => $Request->get_id_visit(), 
				  'ip' => $Request->get_ip(), 
				  'request_url' => $Request->get_request_url(), 
				  'request_timestamp' => $Request->get_request_timestamp(), 
				  'request_object' => $Request->get_request_object(), 
				  'referrer' => $Request->get_referrer(), 
				  'uuid' => $Request->get_uuid(), 
				  'user_agent' => $Request->get_user_agent(), 
				  'search_phrase' => $Request->get_search_phrase()));
		$GLOBALS['wopstaLog']->info('Added a request to the database.');
		return true;
	}
	
	/**
	 * The number of requests that match a given UUID
	 * 
	 * @since 0.1
	 * @return int 
	 */
	public function count_requests_by_uuid($uuid) {
		return $this->wpdb->get_var("SELECT COUNT(*) FROM ".$this->wpdb->wopsta_request." WHERE uuid = '".$uuid."'");
	}
	
	/**
	 * Delete all requests that match a given UUID
	 * 
	 * @since 0.1
	 * @param char(32) The UUID of the visitor who's requests shall be deleted
	 */
	public function delete_requests_by_uuid($uuid) {
		$this->wpdb->query("DELETE FROM ".$this->wpdb->wopsta_request." WHERE uuid = ".$uuid);
	}
	
	/**
	 * Returns the latest request of a given visitor (UUID)
	 * 
	 * @since 0.1
	 * @param char(32) The UUID of the visitor who's latest request shall be returned
	 * @return wopstaRequest The latest request of the given visitor or null if no request is found
	 */ 
	public function get_latest_request_by_uuid($uuid) {
		$row = $this->wpdb->get_row("SELECT * FROM ".$this->wpdb->wopsta_request." WHERE uuid = '".$uuid."' ORDER BY request_timestamp DESC LIMIT 1", ARRAY_A);
		if($row['uuid']) {
			return new wopstaRequest(
				array('id_browser' => $row['id_browser'], 
					  'id_os' => $row['id_os'],
					  'id_searchengine' => $row['id_searchengine'], 
					  'id_spider' => $row['id_spider'], 
					  'id_visit' => $row['id_visit'], 
					  'ip' => $row['ip'], 
					  'request_url' => $row['request_url'], 
					  'request_timestamp' => $row['request_timestamp'], 
					  'request_object' => $row['request_object'], 
					  'referrer' => $row['referrer'], 
					  'uuid' => $row['uuid'], 
					  'user_agent' => $row['user_agent'], 
					  'search_phrase' => $row['search_phrase']));
		}
		return null;
	}
	
	/**
	 * Returns all requests with unknown browser or operating system
	 * 
	 * @since 0.1
	 */
	public function get_requests_with_unknown_browser_or_os() {
		$results = $this->wpdb->get_results("SELECT * FROM ".$this->wpdb->wopsta_request." WHERE id_browser = '0' OR id_os = '0'", "ARRAY_A");		
		$requests = array();
		if($results) {
			foreach($results as $row) {
				array_push($requests, new wopstaRequest(
					array('id_browser' => $row['id_browser'], 
						  'id_os' => $row['id_os'],
						  'id_searchengine' => $row['id_searchengine'], 
						  'id_spider' => $row['id_spider'], 
						  'id_visit' => $row['id_visit'], 
						  'ip' => $row['ip'], 
						  'request_url' => $row['request_url'], 
						  'request_timestamp' => $row['request_timestamp'], 
						  'request_object' => $row['request_object'], 
						  'referrer' => $row['referrer'], 
						  'uuid' => $row['uuid'], 
						  'user_agent' => $row['user_agent'], 
						  'search_phrase' => $row['search_phrase'])));
			}
		}
		return $requests;
	}
	
	/**
	 * Update a given request
	 * 
	 * @since 0.1
	 * @param wopstaRequest The request that shall be updated
	 * @return boolean true
	 */
	public function update(wopstaRequest $Request) {
		$this->wpdb->update($this->wpdb->wopsta_request, 
			array('id_browser' => $Request->get_id_browser(), 
				  'id_os' => $Request->get_id_os(),
				  'id_searchengine' => $Request->get_id_searchengine(), 
				  'id_spider' => $Request->get_id_spider(), 
				  'id_visit' => $Request->get_id_visit(), 
				  'ip' => $Request->get_ip(), 
				  'request_url' => $Request->get_request_url(), 
				  'request_timestamp' => $Request->get_request_timestamp(), 
				  'request_object' => $Request->get_request_object(), 
				  'referrer' => $Request->get_referrer(), 
				  'uuid' => $Request->get_uuid(), 
				  'user_agent' => $Request->get_user_agent(), 
				  'search_phrase' => $Request->get_search_phrase()),
			array('uuid' => $Request->get_uuid(), 
				  'request_timestamp' => $Request->get_request_timestamp()));
		return true;
	}
}
?>