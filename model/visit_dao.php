<?php  
/**
 * wopsta visit data access object
 *
 * @package wopsta
 * @since 0.1
 */
class wopstaVisitDao {
	
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
	 * Add a visit to the database
	 *
	 * @since 0.1
	 * @param wopstaVisit The visit that shall be added
	 * @return int The visit's id
	 */
	public function add(wopstaVisit $Visit) {
		$this->wpdb->insert($this->wpdb->wopsta_visit, 
			array(
				'uuid' => $Visit->get_uuid(), 
				'first_request_timestamp' => $Visit->get_first_request_timestamp(), 
				'last_request_timestamp' => $Visit->get_last_request_timestamp()));
		$id = mysql_insert_id();
		$GLOBALS['wopstaLog']->info('Added visit ID '.$id.' for visitor '.$Visit->get_uuid().' to the database.');
		return $id;
	}
	
	/**
	 * Delete all visits that match a given UUID
	 * 
	 * @since 0.1
	 * @param char(32) The UUID of the visitor who's visits shall be deleted
	 */
	public function delete_visits_by_uuid($uuid) {
		$this->wpdb->query("DELETE FROM ".$this->wpdb->wopsta_visit." WHERE uuid = ".$uuid);
	}
	
	/**
	 * Return the last visit of a given visitor
	 * 
	 * @since 0.1
	 * @param char(32) The UUID of the visitor
	 * @return wopstaVisit The last visit of the given visitor or null if no visit is found
	 */
	public function get_last_visit_for_uuid($uuid) {
		$row = $this->wpdb->get_row("SELECT * FROM ".$this->wpdb->wopsta_visit." WHERE uuid = '".$uuid."' ORDER BY last_request_timestamp DESC LIMIT 1", ARRAY_A);
		if($row['uuid']) {
			return new wopstaVisit(
				array(
					'first_request_timestamp' => $row['first_request_timestamp'],
					'id' => $row['id'], 
					'last_request_timestamp' => $row['last_request_timestamp'],
					'uuid' => $row['uuid']));
		} else {
			$GLOBALS['wopstaLog']->warn('No (last) visit has been found for visitor '.$uuid.'.');
			return null;
		}		
	}
	
	/**
	 * Return a visit for a given ID
	 * 
	 * @since 0.1
	 * @param int $id The unique id that is assigned to the visit
	 * @return wopstaVisit The visit that is mapped to the given id or null if no visit is found
	 */
	public function get_visit_by_id($id) {
		$row = $this->wpdb->get_row("SELECT * FROM ".$this->wpdb->wopsta_visit." WHERE id = '".$id."'", ARRAY_A);
		if($row['uuid']) {
			return new wopstaVisit(
				array(
					'first_request_timestamp' => $row['first_request_timestamp'],
					'id' => $row['id'], 
					'last_request_timestamp' => $row['last_request_timestamp'],
					'uuid' => $row['uuid']));
		} else {
			$GLOBALS['wopstaLog']->warn('No visit found for visit ID '.$id.'.');
			return null;
		}
	}
	
	/**
	 * Update a given visit
	 * 
	 * @since 0.1
	 * @param wopstaVisit The visit that shall be updated
	 * @return boolean true
	 */
	public function update(wopstaVisit $Visit) {
		$this->wpdb->update($this->wpdb->wopsta_visit, 
			array('uuid' => $Visit->get_uuid(), 
				  'first_request_timestamp' => $Visit->get_first_request_timestamp(), 
				  'last_request_timestamp' => $Visit->get_last_request_timestamp()),
			array('id' => $Visit->get_id()));
		$GLOBALS['wopstaLog']->info('Updated visit '.$Visit->get_id().' for visitor '.$Visit->get_uuid().'.');
		return true;
	}
}
?>