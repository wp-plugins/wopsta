<?php
/**
 * wopsta factory for data access object (dao) generation
 *
 * @package wopsta
 * @since 0.1
 */
class wopstaDaoFactory
{

	/**
	 * The factory is unique.
	 */
	private static $_instance;

	
	
	/**
	 *  Constructor.
	 */
	function __construct() { }
	
	/**
	 * Set the factory instance
	 * 
	 * @param wopstaDaoFactory $f
	 * @since 0.1
	 */
	public static function set_factory(wopstaDaoFactory $f) {
		self::$_instance = $f;
	}
 
	/**
	 * Get a factory instance. 
	 * 
	 * @return wopstaDaoFactory
	 * @since 0.1
	 */
	public static function get_factory() {
		if(!self::$_instance)
			self::$_instance = new self();
		return self::$_instance;
	}
 
	/**
	 * Get a request data access object
	 * 
	 * @return wopstaRequestDao
	 * @since 0.1
	 */
	public function get_request_dao() {
		return new wopstaRequestDao();
	}
 
	/**
	 * Get a visit data access object
	 * 
	 * @return wopstaVisitDao
	 * @since 0.1
	 */
	public function get_visit_dao() {
		return new wopstaVisitDao();
	}
 
	/**
	 * Get a visitor data access object
	 * 
	 * @return wopstaVisitorDao
	 * @since 0.1
	 */
	public function get_visitor_dao() {
		return new wopstaVisitorDao();
	}
}
?>