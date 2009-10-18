<?php
global $wpdb;

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
?>