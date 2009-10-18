<?php  
/**
 * wopsta visitor value object
 *
 * @package wopsta
 * @since 0.1
 */
class wopstaVisitor {
	
	/**
	 * Every visitor has his one unique UUID.
	 *
	 * @since 0.1
	 * @access private
	 * @var char(32)
	 */
	private $uuid;
	
	/**
	 * The browser that has been last used by the visitor.
	 * 
	 * @since 0.1
	 * @access private
	 * @var int
	 */
	private $id_browser;
	
	/**
	 * The operating system that has been last used by the visitor.
	 * 
	 * @since 0.1
	 * @access private
	 * @var int
	 */
	private $id_os;
	
	/**
	 * If this visitor is a bot or spider, this should be the reference.
	 *
	 * @since 0.1
	 * @access private
	 * @var int
	 */
	private $id_spider;
	
	/**
	 * This is the datetime of the last request the visitor performed.
	 *
	 * @since 0.1
	 * @access private
	 * @var timestamp
	 */
	private $last_request_timestamp;
	
	/**
	 * Each visitor can be given an own title,  e.g. a real name.
	 * Names might be automatically detected, e.g. for users who write comments.
	 *
	 * @since 0.1
	 * @access private
	 * @var string
	 */
	private $title;
	
	/**
	 * The total amount of requests the visitor has ever performed.
	 * 
	 * @since 0.1
	 * @access private
	 * @var int
	 */
	private $total_requests;
	
	/**
	 * The total amount of visits the visitor had so far.
	 * 
	 * @since 0.1
	 * @access private
	 * @var int
	 */
	private $total_visits;
	
	/**
	 * A visitor can be either a 'human' or a 'spider'.
	 *
	 * @since 0.1
	 * @access private
	 * @var string
	 */
	private $visitor_type;
				
	/**
	 * Constructor for the instantiation of a visitor
	 *
	 * @since 0.1
	 *
	 * @param array The array $args encapsulates all parameters
	 */
	function __construct($args) {
		$this->set_id_browser($args['id_browser']);
		$this->set_id_os($args['id_os']);
		$this->set_id_spider($args['id_spider']);
		$this->set_last_request_timestamp(((isset($args['last_request_timestamp']) && ($args['last_request_timestamp'] > 0)) ? $args['last_request_timestamp'] : null));
		$this->set_title(((isset($args['title']) && (strlen($args['title']) > 0)) ? $args['title'] : 'unknown'));
		$this->set_total_requests($args['total_requests']);
		$this->set_total_visits($args['total_visits']);
		$this->set_uuid($args['uuid']);
		$this->set_visitor_type($args['visitor_type']);
	}
	
	public function get_id_browser() {
		return $this->id_browser;
	}
	
	public function get_id_os() {
		return $this->id_os;
	}
	
	public function get_id_spider() {
		return $this->id_spider;
	}
	
	public function get_last_request_timestamp() {
		return $this->last_request_timestamp;
	}
	
	public function get_title() {
		return $this->title;
	}
	
	public function get_total_requests() {
		return $this->total_requests;
	}
	
	public function get_total_visits() {
		return $this->total_visits;
	}
	
	public function get_uuid() {
		return $this->uuid;
	}
	
	public function get_visitor_type() {
		return $this->visitor_type;
	}
	
	public function set_id_browser($id_browser) {
		$this->id_browser = $id_browser;
	}
	
	public function set_id_os($id_os) {
		$this->id_os = $id_os;
	}
	
	public function set_id_spider($id_spider) {
		$this->id_spider = $id_spider;
	}
	
	public function set_last_request_timestamp($last_request_timestamp) {
		$this->last_request_timestamp = $last_request_timestamp;
	}
	
	public function set_title($title) {
		$this->title = $title;
	}
	
	public function set_total_requests($total_requests) {
		$this->total_requests = $total_requests;
	}
	
	public function set_total_visits($total_visits) {
		$this->total_visits = $total_visits;
	}
	
	public function set_uuid($uuid) {
		$this->uuid = $uuid;
	}
	
	public function set_visitor_type($visitor_type) {
		$this->visitor_type = $visitor_type;
	}
}
?>