<?php  
/**
 * wopsta request value object
 *
 * @package wopsta
 * @since 0.1
 */
class wopstaRequest {

	/**
	 * Every request must be assigned to a vistor
	 *
	 * @since 0.1
	 * @access private
	 * @var int
	 */
	private $uuid;

	/**
	 * The visit the request belongs to
	 *
	 * @since 0.1
	 * @access private
	 * @var int
	 */
	private $id_visit;

	/**
	 * The operating system that is running on the IP's host
	 *
	 * @since 0.1
	 * @access private
	 * @var int
	 */
	private $id_os;

	/**
	 * A searchengine might have been used to come to this site
	 *
	 * @since 0.1
	 * @access private
	 * @var int
	 */
	private $id_searchengine;

	/**
	 * The request might have been automatically performed by a spider
	 *
	 * @since 0.1
	 * @access private
	 * @var int
	 */
	private $id_spider;
	
	/**
	 * The IP the request origins from
	 *
	 * @since 0.1
	 * @access private
	 * @var string
	 */
	private $ip;

	/**
	 * The request's referrer
	 *
	 * @since 0.1
	 * @access private
	 * @var string
	 */
	private $referrer;

	/**
	 * The requested WordPress resource/object
	 *
	 * @since 0.1
	 * @access private
	 * @var string
	 */
	private $request_object;

	/**
	 * The timestamp of the request
	 *
	 * @since 0.1
	 * @access private
	 * @var timestamp
	 */
	private $request_timestamp;

	/**
	 * The requested URL
	 *
	 * @since 0.1
	 * @access private
	 * @var string
	 */
	private $request_url;

	/**
	 * A searchphrase that might have been used
	 *
	 * @since 0.1
	 * @access private
	 * @var string
	 */
	private $search_phrase;

	/**
	 * The complete user's user agent
	 *
	 * @since 0.1
	 * @access private
	 * @var string
	 */
	private $user_agent;
	
	/**
	 * Constructor for the instantiation of a request
	 *
	 * @since 0.1
	 * @param array The array encapsulates all parameters
	 */
	function __construct($args) {
		
		// the user agent must be set before browser, os and spider can be identified!
		$this->set_user_agent($args['user_agent']);

		$this->set_id_browser((isset($args['id_browser']) ? $args['id_browser'] : $this->identify_browser()));
		$this->set_id_os((isset($args['id_os']) ? $args['id_os'] : $this->identify_os()));
		$this->set_id_spider((isset($args['id_spider']) ? $args['id_spider'] : $this->identify_spider()));
		$this->set_id_visit($args['id_visit']);
		$this->set_ip($args['ip']);
		$this->set_referrer($args['referrer']);
		$this->set_request_object((isset($args['request_object']) ? $args['request_object'] : $this->identify_request_object()));
		$this->set_request_timestamp($args['request_timestamp']);
		$this->set_request_url($args['request_url']);
		$this->set_search_phrase((isset($args['search_phrase']) ? $args['search_phrase'] : null));
		$this->set_uuid($args['uuid']);
		
		// might overwrite the current search phrase
		$this->set_id_searchengine((isset($args['id_searchengine']) ? $args['id_searchengine'] : $this->identify_searchengine())); 
	}
	
	public function get_id_browser() {
		return $this->id_browser;
	}
	
	public function get_id_os() {
		return $this->id_os;
	}
	
	public function get_id_searchengine() {
		return $this->id_searchengine;
	}
	
	public function get_id_spider() {
		return $this->id_spider;
	}
	
	public function get_id_visit() {
		return $this->id_visit;
	}
	
	public function get_ip() {
		return $this->ip;
	}
	
	public function get_referrer() {
		return $this->referrer;
	}
	
	public function get_request_object() {
		return $this->request_object;
	}

	public function get_request_timestamp() {
		return $this->request_timestamp;
	}

	public function get_request_url() {
		return $this->request_url;
	}
	
	public function get_search_phrase() {
		return $this->search_phrase;
	}
	
	public function get_user_agent() {
		return $this->user_agent;
	}
	
	public function get_uuid() {
		return $this->uuid;
	}
	
	
	/**
	 * Identify the browser of this request
	 *
	 * @since 0.1
	 * @return int The id of the identified browser or null
	 */
	public function identify_browser() {
		global $wpdb;
		return $wpdb->get_var("SELECT id_parent
							   FROM ".$wpdb->wopsta_lib." 
							   WHERE type = 'browser_pattern' AND '".str_replace(' ', '', $this->user_agent)."' LIKE CONCAT('%', value, '%')
							   LIMIT 1");
	}
	
	/**
	 * Identify the operating system of this request
	 *
	 * @since 0.1
	 * @return int The id of the identified operating system or null
	 */
	public function identify_os() {
		global $wpdb;
		return $wpdb->get_var("SELECT id_parent
							   FROM ".$wpdb->wopsta_lib." 
							   WHERE type = 'operating_system_pattern' AND '".str_replace(' ', '', $this->user_agent)."' LIKE CONCAT('%', value, '%')
							   LIMIT 1");
	}
	
	/**
	 * Identify the requested Object (e.g. Post, Page, Category)
	 * 
	 * @since 0.1
	 * @return String A description of the requested Object
	 */
	public function identify_request_object() {
		global $wp_query;
//		$GLOBALS['wopstaLog']->info('Requested Object '.print_r($wp_query).'.');
		if(is_front_page()) {
			return  'Front Page';
		} else if(is_404()) {
			return  '404';
		} else if(is_attachment()) {
			return  'Attachment';
		} else if(is_author()) {
			return  'Author Archive';
		} else if(is_category()) {
			return  'Category "'.$wp_query->queried_object->cat_name.'"';
		} else if(is_comments_popup()) {
			return  'Comments PopUp';
		} else if(is_day()) {
			return  'Day Archive';
		} else if(is_feed()) {
			return  'Feed';
		} else if(is_month()) {
			return  'Month Archive';
		} else if(is_page()) {
			return  'Page "'.$wp_query->queried_object->post_title.'"';
		} else if(is_preview()) {
			return  'Preview';
		} else if(is_search()) {
			global $wp_query;
			if(strlen($wp_query->query_vars['s']) > 0) {
				return  'Internal Search for: '.$wp_query->query_vars['s'];
			} else {
				return  'Internal Search';
			}
		} else if(is_single()) {
			return  'Post "'.$wp_query->queried_object->post_title.'"';
		} else if(is_tag()) {
			return  'Tag Archive "'.$wp_query->queried_object->name.'"';
		} else if(is_tax()) {
			return  'Tax';
		} else if(is_trackback()) {
			return  'Trackback';
		} else if(is_year()) {
			return  'Year '.the_time('Y');
		} else {
			return 'unknown';
		}	
	}
	
	/**
	 * Identify the searchengine of this request.
	 * If a searchengine is identified, it is looked for a search phrase.
	 *
	 * @since 0.1
	 * @return int The id of the identified operating system or null
	 */
	public function identify_searchengine() {
		global $wpdb;
		$id_searchengine = $wpdb->get_var("SELECT id_parent
										   FROM ".$wpdb->wopsta_lib." 
										   WHERE type = 'searchengine_pattern' AND 
										         '".$this->referrer."' LIKE CONCAT('%', value, '%')
										   LIMIT 1");
		if($id_searchengine > 0) {
			$this->search_phrase = $this->identify_search_phrase($id_searchengine);
		}
		return $id_searchengine;
	}
	
	/**
	 * Identify a search phrase from a given referrer for a given searchengine.
	 *
	 * @since 0.1
	 * @param int $id_searchengine The id of the searchengine the query comes from
	 * @return string The search phrase that had been used or null
	 */
	public function identify_search_phrase($id_searchengine) {
		global $wpdb;
		$parameter = $wpdb->get_var("SELECT value
									 FROM ".$wpdb->wopsta_lib." 
									 WHERE type = 'searchengine_search_phrase_parameter' AND 
									 	   id_parent = '".$args['id_searchengine']."'");
		if(strlen($parameter) > 0) {
			$var = html_entity_decode(parse_url($this->referrer, PHP_URL_QUERY));
			$var = explode('&', $var);
			foreach($var as $val) {
				$x = explode('=', $val);
				if($x[0] == $parameter) {
					$GLOBALS['wopstaLog']->info('A search phrase has been identified ("'.$x[1].'").');
					return $x[1];
				}
			}
		}
		return null;
	}
	
	/**
	 * Identify the spider of this request
	 *
	 * @since 0.1
	 * @return int The id of the identified spider or null
	 */
	public function identify_spider() {
		global $wpdb;
		return $wpdb->get_var("SELECT id_parent
							   FROM ".$wpdb->wopsta_lib." 
							   WHERE type = 'spider_pattern' AND '".str_replace(' ', '', $this->user_agent)."' LIKE CONCAT('%', value, '%')
							   LIMIT 1");
	}
	
	public function set_id_browser($id_browser) {
		$this->id_browser = $id_browser;
	}
	
	public function set_id_os($id_os) {
		$this->id_os = $id_os;
	}
	
	public function set_id_searchengine($id_searchengine) {
		$this->id_searchengine = $id_searchengine;
	}
	
	public function set_id_spider($id_spider) {
		$this->id_spider = $id_spider;
	}
	
	public function set_id_visit($id_visit) {
		$this->id_visit = $id_visit;
	}

	public function set_ip($ip) {
		$this->ip = $ip;
	}

	public function set_referrer($referrer) {
		$this->referrer = $referrer;
	}

	public function set_request_timestamp($request_timestamp) {
		$this->request_timestamp = $request_timestamp;
	}

	public function set_request_url($request_url) {
		$this->request_url = $request_url;
	}
	
	public function set_request_object($request_object) {
		$this->request_object = $request_object;
	}
	
	public function set_search_phrase($search_phrase) {
		$this->search_phrase = $search_phrase;
	}
	
	public function set_user_agent($user_agent) {
		$this->user_agent = $user_agent;
	}
	
	public function set_uuid($uuid) {
		$this->uuid = $uuid;
	}
}
?>