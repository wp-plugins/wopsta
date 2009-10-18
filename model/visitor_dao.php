<?php  
/**
 * wopsta visitor data access object
 *
 * @package wopsta
 * @since 0.1
 */
class wopstaVisitorDao {
	
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
	 * Add a visitor to the database
	 *
	 * @since 0.1
	 * @param wopstaVisitor The visitor that shall be added
	 * @return char(32) The visitor's id
	 */
	public function add(wopstaVisitor $Visitor) {
		$this->wpdb->insert($this->wpdb->wopsta_visitor, 
			array('id_browser' => $Visitor->get_id_browser(), 
				  'id_os' => $Visitor->get_id_os(),
				  'id_spider' => $Visitor->get_id_spider(), 
				  'last_request_timestamp' => $Visitor->get_last_request_timestamp(), 
				  'title' => $Visitor->get_title(), 
				  'total_requests' => $Visitor->get_total_requests(), 
				  'total_visits' => $Visitor->get_total_visits(), 
				  'uuid' => $Visitor->get_uuid(), 
				  'visitor_type' => $Visitor->get_visitor_type()));
		$GLOBALS['wopstaLog']->info('Added visitor '.$Visitor->get_uuid().' to the database.');
		return $Visitor->get_uuid();
	}
	
	/**
	 * Returns a visitor instance for a given UUID
	 * 
	 * @since 0.1
	 * @param int $uuid The unique id that is assigned to the visitor
	 * @return wopstaVisitor The visitor who is mapped to the given UUID or null, if no visitor is found
	 */
	public function get_visitor_by_uuid($uuid) {
		$row = $this->wpdb->get_row("SELECT * FROM ".$this->wpdb->wopsta_visitor." WHERE uuid = '".$uuid."'", ARRAY_A);
		if($row['uuid']) {
			return new wopstaVisitor(
				array('id_browser' => $row['id_browser'], 
					  'id_os' => $row['id_os'], 
					  'id_spider' => $row['id_spider'], 
					  'last_request_timestamp' => $row['last_request_timestamp'], 
				  	  'title' => $row['title'], 
					  'total_requests' => $row['total_requests'], 
					  'total_visits' => $row['total_visits'], 
				  	  'uuid' => $row['uuid'], 
				  	  'visitor_type' => $row['visitor_type']));
		}
		$GLOBALS['wopstaLog']->error('No visitor found for UUID '.$uuid.'.');
		return null;
	}
	
	/**
	 * Recover a missing visitor (and its visits)
	 * 
	 * This method is called whenever someone requests a resource who has got a valid cookie but 
	 * cannot be found in the visitors table. The method then tries to recover as much as possible 
	 * of this visitor (and insure the database's integrity); if no requests are there for that 
	 * visitor, a new visitor will be created.
	 * 
	 * @since 0.1
	 * @param char(32) The id of the visitor who should be recovered
	 * @return boolean true
	 */
	public function recover_visitor_by_uuid($uuid) {
		
		// Ensure there's nothing left but the visitor's requests
		$VisitDao = wopstaDaoFactory::get_factory(&$wpdb)->get_visit_dao();
		$VisitDao->delete_visits_by_uuid($uuid);
		
		// Recover visitor from its requests
		$RequestDao = wopstaDaoFactory::get_factory(&$wpdb)->get_request_dao();
		$Request = $RequestDao->get_latest_request_by_uuid($uuid);
		if($Request instanceof wopstaRequest) {
			$Visitor = new wopstaVisitor(
				array('id_browser' => $Request->get_id_browser(),
					  'id_os' => $Request->get_id_os(),
					  'id_spider' => $Request->get_id_spider(), 
					  'last_request_timestamp' => date('Y-m-d H:i:s', $_SERVER['REQUEST_TIME']),
					  'title' => 'unknown',
					  'total_requests' => 0,
					  'total_visits' => 1,
					  'uuid' => $Request->get_uuid(), 
					  'visitor_type' => (($Request->get_id_spider() > 0) ? 'spider' : 'human')));
			$RequestDao->delete_requests_by_uuid($uuid);
		} else {
			$Visitor = new wopstaVisitor(
				array('id_spider' => 0,
					  'last_request_timestamp' => date('Y-m-d H:i:s', $_SERVER['REQUEST_TIME']),
					  'title' => 'unknown',
					  'total_requests' => 0,
					  'total_visits' => 1,
					  'uuid' => $uuid, 
					  'visitor_type' => 'human'));
		}
		$VisitorDao = wopstaDaoFactory::get_factory(&$wpdb)->get_visitor_dao();
		$VisitorDao->add($Visitor);
		$VisitDao->add(new wopstaVisit(
			array('first_request_timestamp' => date('Y-m-d H:i:s', $_SERVER['REQUEST_TIME']),
				  'last_request_timestamp' => date('Y-m-d H:i:s', $_SERVER['REQUEST_TIME']),
				  'uuid' => $uuid)));
		return true;
	}
	
	/**
	 * Update a given visitor
	 * 
 	 * @since 0.1
	 * @param wopstaVisitor The visitor that shall be updated
	 * @return boolean true
	 */
	public function update(wopstaVisitor $Visitor) {
		$this->wpdb->update($this->wpdb->wopsta_visitor, 
			array('id_browser' => $Visitor->get_id_browser(), 
				  'id_os' => $Visitor->get_id_os(),
				  'id_spider' => $Visitor->get_id_spider(), 
				  'last_request_timestamp' => $Visitor->get_last_request_timestamp(), 
				  'title' => $Visitor->get_title(), 
				  'total_requests' => $Visitor->get_total_requests(), 
				  'total_visits' => $Visitor->get_total_visits(), 
				  'uuid' => $Visitor->get_uuid(), 
				  'visitor_type' => $Visitor->get_visitor_type()),
			array('uuid' => $Visitor->get_uuid()));
		$GLOBALS['wopstaLog']->info('Updated visitor '.$Visitor->get_uuid().'.');
		return true;
	}
}
?>