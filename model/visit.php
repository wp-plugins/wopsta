<?php  
/**
 * wopsta Visit Class
 *
 * @package Wopsta
 * @since 0.1
 */
class wopstaVisit {
	
	/**
	 * Every visit has its one unique ID.
	 *
	 * @since 0.1
	 * @access private
	 * @var int
	 */
	private $id;
	
	/**
	 * The UUID (visitor) the visit belongs to.
	 *
	 * @since 0.1
	 * @access private
	 * @var char(32)
	 */
	private $uuid;
	
	/**
	 * The time of the first request of the visit.
	 *
	 * @since 0.1
	 * @access private
	 * @var timestamp
	 */
	private $first_request_timestamp;
	
	/**
	 * The time of the last request of the visit.
	 *
	 * @since 0.1
	 * @access private
	 * @var timestamp
	 */
	private $last_request_timestamp;

	/**
	 * Constructor for the instantiation of a visit
	 *
	 * @since 0.1
	 * @param array $args Array, encapsulating all parameters
	 */
	function __construct($args) {
		$this->set_first_request_timestamp(((isset($args['first_request_timestamp']) && ($args['first_request_timestamp'] > 0)) ? $args['first_request_timestamp'] : null));
		$this->set_id(((isset($args['id']) && ($args['id'] > 0)) ? $args['id'] : null));
		$this->set_last_request_timestamp(((isset($args['last_request_timestamp']) && ($args['last_request_timestamp'] > 0)) ? $args['last_request_timestamp'] : null));
		$this->set_uuid($args['uuid']);
	}
			
	public function get_first_request_timestamp() {
		return $this->first_request_timestamp;
	}

	public function get_id() {
		return $this->id;
	}
			
	public function get_last_request_timestamp() {
		return $this->last_request_timestamp;
	}

	public function get_uuid() {
		return $this->uuid;
	}
			
	public function set_first_request_timestamp($first_request_timestamp) {
		$this->first_request_timestamp = $first_request_timestamp;
	}

	public function set_id($id) {
		$this->id = $id;
	}
			
	public function set_last_request_timestamp($last_request_timestamp) {
		$this->last_request_timestamp = $last_request_timestamp;
	}

	public function set_uuid($uuid) {
		$this->uuid = $uuid;
	}
}
?>