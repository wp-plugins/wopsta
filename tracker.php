<?php  
/**
 * wopsta tracker class
 * 
 * This is the tracker that is called for every single request.
 *
 * @package wopsta
 * @since 0.1
 */
class wopstaTracker {
	
	/**
	 * Constructor for the instantiation to track a new request
	 *
	 * Some typical referer for testing:
	 * http://www.google.com/search?ie=UTF-8&oe=UTF-8&sourceid=navclient&gfns=1&q=http://blog.andrekolell.de
	 *
	 * @since 0.1
	 */
	function __construct() {
		
		global $wpdb;
		
		// Essential data access objects
		$RequestDao = wopstaDaoFactory::get_factory(&$wpdb)->get_request_dao();
		$VisitDao = wopstaDaoFactory::get_factory(&$wpdb)->get_visit_dao();
		$VisitorDao = wopstaDaoFactory::get_factory(&$wpdb)->get_visitor_dao();
		
		// Is this a spider (bot/crawler) or a human?
		$id_spider = $wpdb->get_var("SELECT id_parent FROM ".$wpdb->wopsta_lib." WHERE type = 'spider_pattern' AND '".$_SERVER['HTTP_USER_AGENT']."' LIKE CONCAT('%', value, '%') LIMIT 1");
		if($id_spider) { $visitor_type = 'spider'; } else { $visitor_type = 'human'; }

		// Visitor and visit identification (UUID)
		if($_COOKIE['wopsta']) {
			$uuid = $_COOKIE['wopsta'];
			setcookie('wopsta', $_COOKIE['wopsta'], (time() + 31536000), '/');
			$GLOBALS['wopstaLog']->info('This is a returning visitor. His UUID is '.$uuid.'.');
			$Visitor = $VisitorDao->get_visitor_by_uuid($uuid);
			if(!$Visitor instanceof wopstaVisitor) {
				$GLOBALS['wopstaLog']->error('No visitor found for '.$uuid.'; try to recover visitor.');
				$VisitorDao->recover_visitor_by_uuid($uuid);
				$Visitor = $VisitorDao->get_visitor_by_uuid($uuid);
			}		
			if($Visitor instanceof wopstaVisitor) {
				$Visit = $VisitDao->get_last_visit_for_uuid($uuid);
				if(!$Visit instanceof wopstaVisit) {
					$GLOBALS['wopstaLog']->error('No visit found for visitor '.$uuid.'; try to recover visitor.');
					$VisitorDao->recover_visitor_by_uuid($uuid);
					$Visitor = $VisitorDao->get_visitor_by_uuid($uuid);
				}
				if($Visit instanceof wopstaVisit) {
					if($wpdb->get_var("SELECT TIMEDIFF('".date('Y-m-d H:i:s', $_SERVER['REQUEST_TIME'])."', '".$Visit->get_last_request_timestamp()."')") > 3600) {
						$GLOBALS['wopstaLog']->info('The visitor\'s last action has been longer ago than one hour. It is assumed to be a new visit.');
						$Visitor->set_total_visits($Visitor->get_total_visits() + 1);
						$id_visit = $VisitDao->add(new Visit(
							array('first_request_timestamp' => date('Y-m-d H:i:s', $_SERVER['REQUEST_TIME']),
								  'last_request_timestamp' => date('Y-m-d H:i:s', $_SERVER['REQUEST_TIME']),
								  'uuid' => $uuid)));
					} else {
						$GLOBALS['wopstaLog']->info('The visitor\'s last action has been within the last hour. It is assumed to be one visit.');
						$Visit->set_last_request_timestamp(date('Y-m-d H:i:s', $_SERVER['REQUEST_TIME']));
						$VisitDao->update($Visit);
						$id_visit = $Visit->get_id();
					}
				}
				$Visitor->set_total_requests($Visitor->get_total_requests() + 1);
				$Visitor->set_last_request_timestamp(date('Y-m-d H:i:s', $_SERVER['REQUEST_TIME']));
			}
		} else {	
			$uuid = md5(uniqid(rand(), true));
			setcookie('wopsta', $uuid, (time() + 31536000), '/');
			$GLOBALS['wopstaLog']->info('This is a new visitor. His UUID will be '.$uuid.'.');
			$Visitor = new wopstaVisitor(
				array('id_spider' => $id_spider, 
					  'uuid' => $uuid, 
					  'last_request_timestamp' => date('Y-m-d H:i:s', $_SERVER['REQUEST_TIME']),
					  'total_requests' => 1,
					  'total_visits' => 1,
					  'visitor_type' => $visitor_type));
			$VisitorDao->add($Visitor);
			$id_visit = $VisitDao->add(new wopstaVisit(
				array('first_request_timestamp' => date('Y-m-d H:i:s', $_SERVER['REQUEST_TIME']),
					  'last_request_timestamp' => date('Y-m-d H:i:s', $_SERVER['REQUEST_TIME']),
					  'uuid' => $uuid)));
		}
		
		// What has been requested?
		$request_url = (isset($_SERVER['QUERY_STRING']) ? $_SERVER['QUERY_STRING'] : '');
      	if($request_url == '') { $request_url = (isset($_SERVER['REQUEST_URI']) ? $_SERVER['REQUEST_URI'] : ''); }
		if($request_url == '/') { $request_url = ''; }
		
		// Store the request
		$Request = new wopstaRequest(
			array('uuid' => $uuid, 
				  'id_visit' => $id_visit, 
				  'id_spider' => $id_spider, 
				  'ip' => $_SERVER['REMOTE_ADDR'], 
	  			  'referrer' => (isset($_SERVER['HTTP_REFERER']) ? htmlentities($_SERVER['HTTP_REFERER']) : ''), 
				  'request_timestamp' => date('Y-m-d H:i:s', $_SERVER['REQUEST_TIME']),
  				  'request_url' => $request_url, 
				  'user_agent' => (isset($_SERVER['HTTP_USER_AGENT']) ? htmlentities($_SERVER['HTTP_USER_AGENT']) : '')));
		$RequestDao->add($Request);
		
		// Update visitor's title, browser and operating system
		if($Visitor instanceof wopstaVisitor) {
	    	if(isset($_COOKIE['comment_author_'.COOKIEHASH])) {
	    		$Visitor->set_title(utf8_encode($_COOKIE['comment_author_'.COOKIEHASH]));
	    	}
			$Visitor->set_id_browser($Request->get_id_browser());
			$Visitor->set_id_os($Request->get_id_os());
			$GLOBALS['wopstaLog']->info('Set the visitor\'s browser to ID '.$Request->get_id_browser().' and os to ID '.$Request->get_id_os().'.');
			$VisitorDao->update($Visitor);
		}
		
		// Debug request
		$GLOBALS['wopstaLog']->info($Request);
	}
}
?>