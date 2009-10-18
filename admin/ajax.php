<?php
/**
 * Add Browser via wopsta.org Web Service
 * 
 * @since 0.1
 *
 * @param string $_POST['browser'] The (optional) Browser Title
 *        string $_POST['user_agent'] An Example of an User Agent that contains the Browser
 *
 * @return string JSON-String, containing 'success' = true or false (if false, 'errormsg' is set)
 */
function wopsta_add_browser_via_webservice() {
	$webservice_client = new wopstaWebserviceClient();
	$result = $webservice_client->publish_browser_man(array('browser' => $_POST['browser'], 'user_agent' => $_POST['user_agent']));
	if(!$result['success']) {
		die('{"success": false, "errormsg": "'.$result['errormsg'].'"}');
	} else {
		die('{"success": true}');
	}
}
add_action('wp_ajax_wopsta_add_browser_via_webservice', 'wopsta_add_browser_via_webservice');



/**
 * Returns visits and pageviews for a given timeframe
 * 
 * @since 0.1
 *
 * @param string $_POST['from'] Where the timeframe starts
 *        string $_POST['to'] Where the timeframe ends
 */
function wopsta_get_visits_and_pageviews() {
	
	global $wpdb;
	
	// Validate timeframe and choose output format
	$days = $wpdb->get_var("SELECT DATEDIFF('".$_POST['to']."','".$_POST['from']."')");
	if($days < 0) {
		die('{"success":false, "errormsg": "Invalid timeframe."}');
	}

	// Get visits
	if(!$results = $wpdb->get_results("SELECT DATE(first_request_timestamp) AS date, COUNT(*) AS visits 
								  	   FROM ".$wpdb->wopsta_visit." 
									   WHERE DATE(first_request_timestamp) BETWEEN '".$_POST['from']."' AND '".$_POST['to']."'
									   GROUP BY DATE(first_request_timestamp)", 'ARRAY_A')) {
		die('{"success": false, "errormsg": "Data error."}');
	} else {
		foreach($results as $result) {
			$visits[$result['date']] = $result['visits'];
		}
	}

	// Get pageviews
	if(!$results = $wpdb->get_results("SELECT DATE(request_timestamp) AS date, COUNT(*) AS pageviews 
									   FROM ".$wpdb->wopsta_request." 
									   WHERE DATE(request_timestamp) BETWEEN '".$_POST['from']."' AND '".$_POST['to']."'
									   GROUP BY DATE(request_timestamp)", 'ARRAY_A')) {
		die('{"success": false, "errormsg": "Data error."}');
	} else {
		foreach($results as $result) {
			$pageviews[$result['date']] = $result['pageviews'];
		}
	}
	
	// Return
	$records = array(array('interval' => $_POST['from'], 
						   'visits' => (isset($visits[$_POST['from']]) ? $visits[$_POST['from']] : 0), 
						   'pageviews' => (isset($pageviews[$_POST['from']]) ? $pageviews[$_POST['from']] : 0)));
	$day = $_POST['from'];
	for($i = 1; $i <= $days; $i++) {
		$day = $wpdb->get_var("SELECT DATE_ADD('".$day."', INTERVAL 1 DAY)");
		array_push($records, array('interval' => $day, 
						   'visits' => (isset($visits[$day]) ? $visits[$day] : 0), 
						   'pageviews' => (isset($pageviews[$day]) ? $pageviews[$day] : 0)));
	}
	die('{"success":true, days: '.$days.', records: '.json_encode($records).'}');
}
add_action('wp_ajax_wopsta_get_visits_and_pageviews', 'wopsta_get_visits_and_pageviews');



/**
 * Returns visitors in JSON format
 * 
 * @since 0.1
 *
 * @param string $_POST['start'] Where to start the limit
 *        string $_POST['limit'] The number of results that shall be returned
 *        string $_POST['dir'] The direction a column should be sorted (dir/asc)
 *        string $_POST['sort'] The column to sort
 *
 * @return string Visitors in JSON format and totalCount (total available Visitors)
 */
function wopsta_last_visitors() {
	
	global $wpdb;
	
	// Sorting
	if($_POST['sort'] && $_POST['dir']) {
		if($_POST['sort'] == 'time') { $_POST['sort'] = 'last_action'; }
		$order_by = $_POST['sort'].' '.$_POST['dir'];
	} else {
		$order_by = 'last_action DESC';
	}
	
	// Querying
	$totalCount = $wpdb->get_var("SELECT COUNT(*) AS c FROM ".$wpdb->wopsta_visitor." WHERE visitor_type = 'human'");	
	if(!$rs = $wpdb->get_results("SELECT id, title, last_action FROM ".$wpdb->wopsta_visitor." WHERE visitor_type = 'human' ORDER BY ".$order_by." LIMIT ".$_POST['start'].",".$_POST['limit'], 'ARRAY_A')) {
		die('{"success": false}'.$sql);
	} else {
		$records = array();
		foreach ($rs as $row) {
			array_push($records, array(
				'time' => $row['last_action'],
				'title' => $row['title'],
				'total_requests_during_visit' => 0,
				'previous_visits' => 0));
		}
		die('{"success":true, "totalCount": '.$totalCount.', records: '.json_encode($records).'}');
	}
}
add_action('wp_ajax_wopsta_last_visitors', 'wopsta_last_visitors');



/**
 * Remove all wopsta database tables and options
 * 
 * @since 0.1
 */
function wopsta_uninstall() {

	global $wpdb;
	
	// Does the current user possess the required rights?
	if(!current_user_can('wopsta Administrate')) {
		die('{success:false, errormsg:"'.__('You do not possess the required rights.', 'wopsta').'"}');
	}
	
	// Clean up database
	$wpdb->query('DROP TABLE IF EXISTS '.$wpdb->prefix.'wopsta_lib');
	$wpdb->query('DROP TABLE IF EXISTS '.$wpdb->prefix.'wopsta_request');
	$wpdb->query('DROP TABLE IF EXISTS '.$wpdb->prefix.'wopsta_visit');
	$wpdb->query('DROP TABLE IF EXISTS '.$wpdb->prefix.'wopsta_visitor');
	
	// Remove options
	delete_option('wopsta_options');

	// Remove capabilities
	foreach(array('subscriber', 'contributor', 'author', 'editor', 'administrator') as $role) {
		$role = get_role($role);
		foreach(array('wopsta Administrate', 'wopsta Access statistics') as $capability) {
			$role->remove_cap($capability) ;
		}
	}
	
	// Uninstallation Successful
	die('{success:true}');
}
add_action('wp_ajax_wopsta_uninstall', 'wopsta_uninstall');
?>