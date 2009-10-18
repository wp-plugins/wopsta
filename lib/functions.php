<?php
/**
 * This is a function to add pattern to identify browsers, operating systems (os), 
 * searchengines and spiders to the wopsta database. 
 *
 * @since 0.1
 * 
 * @param array $args The $args-array encapsulates all parameters
 *				$pattern_type Either "browser", "os", "searchengine" or "spider"
 *				$title The name of the browser, os, etc.
 *				$pattern The pattern that is part of the request
 *				$search_phrase_parameter For a searchengine pattern the parameter that contains the query string
 */
function wopsta_add_pattern($args) {
	
	global $wpdb;
	
	// Browser, OS, Searchengine, Spider exists?
	switch($args['pattern_type']) {
		case 'browser':
			$wpdb->insert($wpdb->wopsta_browser, array('title' => $args['title']));
			$id = $wpdb->get_var("SELECT id FROM ".$wpdb->wopsta_browser." WHERE title = '".$args['title']."'");
			break;
		case 'os':
			$wpdb->insert($wpdb->wopsta_os, array('title' => $args['title']));
			$id = $wpdb->get_var("SELECT id FROM ".$wpdb->wopsta_os." WHERE title = '".$args['title']."'");
			break;
		case 'searchengine':
			$wpdb->insert($wpdb->wopsta_searchengine, array('title' => $args['title'], 'search_phrase_parameter' => $args['search_phrase_parameter']));
			$id = $wpdb->get_var("SELECT id FROM ".$wpdb->wopsta_searchengine." WHERE title = '".$args['title']."'");
			break;
		case 'spider':
			$wpdb->insert($wpdb->wopsta_spider, array('title' => $args['title']));
			$id = $wpdb->get_var("SELECT id FROM ".$wpdb->wopsta_spider." WHERE title = '".$args['title']."'");
			break;
		default:
			break;	
	}
	
	// Add Pattern to Database
	$wpdb->insert($wpdb->wopsta_pattern, array('id' => $id, 'pattern_type' => $args['pattern_type'], 'pattern' => $args['pattern']));
}
?>