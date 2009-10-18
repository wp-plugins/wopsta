<?php 
function wopsta_install() {

	global $wpdb;

	// Capability-Check
	if(!current_user_can('activate_plugins')) { return; } 

	// Set administrator's capabilities
	$role = get_role('administrator');
	if(empty($role)) { return; } 
	$role->add_cap('wopsta Administrate');
	$role->add_cap('wopsta Access statistics');
	
	// WordPress upgrade function (since 2.3)
	require_once(ABSPATH.'wp-admin/includes/upgrade.php');

	// Charset & collate like wp core
	$charset_collate = '';
	if(version_compare(mysql_get_server_info(), '4.1.0', '>=')) {
		if (!empty($wpdb->charset))
			$charset_collate = "DEFAULT CHARACTER SET $wpdb->charset";
		if (!empty($wpdb->collate))
			$charset_collate .= " COLLATE $wpdb->collate";
	}
	
	// Lib
	if($wpdb->get_var("SHOW TABLES LIKE '".$wpdb->wopsta_lib."'") != $wpdb->wopsta_lib) {
	$sql = "CREATE TABLE ".$wpdb->wopsta_lib." (
				id INT,
				id_parent INT,
				type VARCHAR(255),
				value VARCHAR(255),
				id_change INT,
				UNIQUE KEY uk (id),
				UNIQUE KEY uk2 (id_change)
				) $charset_collate;";
		dbDelta($sql);
	}
				
	// Requests
	if($wpdb->get_var("SHOW TABLES LIKE '".$wpdb->wopsta_request."'") != $wpdb->wopsta_request) {
	$sql = "CREATE TABLE ".$wpdb->wopsta_request." (
				uuid CHAR(32),
				id_visit INT,
				id_browser INT,
				id_os INT,
				id_searchengine INT,
				id_spider INT,
				ip VARCHAR(39),
				request_timestamp TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
				request_url TEXT,
				request_object TEXT,
				referrer TEXT,
				user_agent TEXT,
				search_phrase TEXT,
				PRIMARY KEY pk (uuid, request_timestamp)
				) $charset_collate;";
		dbDelta($sql);
	}

	// Visit
	if($wpdb->get_var("SHOW TABLES LIKE '".$wpdb->wopsta_visit."'") != $wpdb->wopsta_visit) {
	$sql = "CREATE TABLE ".$wpdb->wopsta_visit." (
				id INT NOT NULL AUTO_INCREMENT,
				uuid CHAR(32),
				first_request_timestamp TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
				last_request_timestamp TIMESTAMP,
				PRIMARY KEY pk (id)
				) $charset_collate;";
		dbDelta($sql);
	}
				
	// Visitors
	if($wpdb->get_var("SHOW TABLES LIKE '".$wpdb->wopsta_visitor."'") != $wpdb->wopsta_visitor) {
	$sql = "CREATE TABLE ".$wpdb->wopsta_visitor." (
				uuid CHAR(32),
				id_browser INT,
				id_os INT,
				id_spider INT,
				visitor_type ENUM('human', 'spider') NOT NULL DEFAULT 'human',
				title VARCHAR(255) NOT NULL DEFAULT 'unknown',
				last_request_timestamp TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
				total_requests INT NOT NULL DEFAULT 0,
				total_visits INT NOT NULL DEFAULT 0,
				UNIQUE KEY uk (uuid)
				) $charset_collate;";
		dbDelta($sql);
	}

	// Tables really created?
	if($wpdb->get_var("SHOW TABLES LIKE '".$wpdb->wopsta_request."'") != $wpdb->wopsta_request) {
		die('AT LEAST ONE TABLE HAS NOT BEEN CREATED...');
	}

	// Set default Settings, if this is not an Upgrade
	$options = get_option('wopsta_options');
	if(empty($options)) {
		$wopsta_options['webservice_usage'] = 'full';				# full/read-only/none
		update_option('wopsta_options', $wopsta_options);
	}	
}
?>