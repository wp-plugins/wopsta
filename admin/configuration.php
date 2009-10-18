<?php
/**
 * Show the configuration page in the wopsta administration panel
 *
 * @since 0.1
 */	
function wopsta_admin_configuration() { 

	/**
	 * Sync with wopsta.org web service
	 */
	if(isset($_GET['action']) && ($_GET['action'] == "sync")) {

		// Check 
		$wopsta_options = get_option('wopsta_options');
		if(!in_array($wopsta_options['webservice_usage'], array('full', 'read-only'))) {
			echo '<div id="message" class="updated fade"><p><strong>Synchronization failed:</strong><br/><ul><li>Your current settings do not allow synchronization.</li></ul></p></div>';
		} else {

			// Logging
			$log = array();
	
			// Initialize webservice client
			$webservice_client = new wopstaWebserviceClient();
	
			// Update client's library
			$result = $webservice_client->update_client_library();
			if(!$result['success']) {
				echo '<div id="message" class="updated fade"><p><strong>Synchronization failed:</strong><br/><ul><li>'.$result['errormsg'].'</li><li>The Client\'s library has not been updated.</li></ul></p></div>';
			
			// If update was successful and publishing is allowed, publish unknown user agents 
			} else {
				foreach($result['log'] as $entry) {
					array_push($log, $entry);
				}
				
				// Publishing!?
				if($wopsta_options['webservice_usage'] != 'full') {
					array_push($log, 'Publishing unknown browsers and operating systems is currently not allowed by your settings.');
				} else {
					$result = $webservice_client->publish_user_agents();
		
					if(!$result['success']) {
						echo '<div id="message" class="updated fade"><p><strong>Synchronization failed:</strong><br/><ul><li>'.$result['errormsg'].'</li><li>The Client\'s library was updated but no unknown user agents could have been published.</li></ul></p></div>';
					} else {
						array_push($log, $result['log']);
					}
				}
		
				// Output
				echo '<div id="message" class="updated fade"><p><strong>Synchronization successful:</strong><br/><ul>';
				foreach ($log as $entry) {
					echo '<li>'.$entry.'</li>';
				}
				echo '</ul></p></div>';
		
			}
		}
	}
	
	/**
	 * Update Settings 
	 */
	if(isset($_POST['update'])) {	
		if(current_user_can('wopsta Administrate')) {
			
			// Update Synchronization
			$wopsta_options = get_option('wopsta_options');
			$wopsta_options['webservice_usage'] = $_POST['wopsta_webservice_usage'];
			update_option('wopsta_options', $wopsta_options);
			
			// Update Authorization
			wopsta_update_capability($_POST['wopsta_administration'], 'wopsta Administrate');
			wopsta_update_capability($_POST['wopsta_statistic_access'], 'wopsta Access statistics');
			echo '<div id="message" class="updated fade"><p><strong>Configuration updated.</strong></p></div>';
		}
	} ?>
<div class="wrap">
	<h2><?php _e('Configuration', 'wopsta'); ?></h2>

	<h3><?php _e('Synchronization', 'wopsta'); ?></h3>
	<p>
		<?php _e('A webservice is offered on wopsta.org, that allows you to automatically publish those user agents that cannot be interpreted by the current status of wopsta\'s client library.', 'wopsta'); ?> <?php _e('Publishing these unknown user agents helps to improve the client libraries.', 'wopsta'); ?> <?php _e('The usage of the wopsta.org webservice allows you to automatically update your client library, which contains all the different browsers, operating systems, searchengines and spiders.', 'wopsta'); ?>
	</p>
	<table class="form-table">
		<form method="post">
		<tr>
			<th scope="row"><?php _e('Usage of wopsta.org Webservice', 'wopsta'); ?></th>
			<td>
				<fieldset><legend class="screen-reader-text"><span>Usage of wopsta.org Webservice</span></legend>
				<?php $wopsta_options = get_option('wopsta_options'); ?>
				<label title="<?php _e('Send unknown browsers and operating systems and receive client library updates', 'wopsta'); ?>"><input type="radio" name="wopsta_webservice_usage" value="full"<?php if($wopsta_options['webservice_usage'] == "full") echo ' checked="checked"'; ?>/> <?php _e('Send unknown browsers and operating systems and receive client library updates', 'wopsta'); ?></label><br/>
				<label title="<?php _e('Receive client library updates only', 'wopsta'); ?>"><input type="radio" name="wopsta_webservice_usage" value="read-only"<?php if($wopsta_options['webservice_usage'] == "read-only") echo ' checked="checked"'; ?>/> <?php _e('Receive Client Library Updates only', 'wopsta'); ?></label><br/>
				<label title="<?php _e('Do not use wopsta.org webservice', 'wopsta'); ?>"><input type="radio" name="wopsta_webservice_usage" value="none"<?php if($wopsta_options['webservice_usage'] == "none") echo ' checked="checked"'; ?>/> <?php _e('Do not use wopsta.org Webservice', 'wopsta'); ?></label><br/>
				</fieldset>
				<span>There is no automatic synchronization available yet. <a href="admin.php?page=wopsta_configuration&action=sync">Synchronize now</a>.</span>

			</td>
		</tr>
	</table>

	<h3><?php _e('Authorization', 'wopsta'); ?></h3>
	<p>
		<?php _e('For each capability select the lowest role that should be able to perform it.', 'wopsta'); ?> <?php _e('wopsta only supports WordPress\' standard roles.', 'wopsta'); ?><br/>
		<?php _e('You need to possess the role "wopsta Admninistrate" to change authorization settings.', 'wopsta'); ?>
	</p>
	<table class="form-table">
		<tr valign="top">
			<th scope="row"><label for="wopsta_administration"><?php _e('Administrate wopsta', 'wopsta'); ?></label></th>
			<td><select name="wopsta_administration" id="wopsta_administration"><?php wp_dropdown_roles(wopsta_get_role('wopsta Administrate')); ?></select></td>
		</tr>
		<tr valign="top">
			<th scope="row"><label for="wopsta_statistic_access"><?php _e('Access wopsta Statistics', 'wopsta'); ?></label></th>
			<td><select name="wopsta_statistic_access" id="wopsta_statistic_access"><?php wp_dropdown_roles(wopsta_get_role('wopsta Access statistics')); ?></select></td>
		</tr>
	</table>
	<p class="submit">
		<input type="submit" class="button-primary" name="update" value="<?php _e('Save Changes', 'wopsta'); ?>"/>
	</p>
	</form>
</div>
<?php } 

/**
 * Return the lowest role for a given capability
 *
 * @since 0.1
 * 
 * @param string $capability The name of the capability for which the lowest role should be returned
 * 
 * @return string Returns the name of the role or false if no role is found
 */
function wopsta_get_role($capability){
	$args = array_slice(func_get_args(), 1);
	foreach (array('subscriber', 'contributor', 'author', 'editor', 'administrator') as $role) {
		$check_role = get_role($role);
		if(empty($check_role))
			return false;
		if(call_user_func_array(array(&$check_role, 'has_cap'), array_merge(array($capability), $args)))
			return $role;
	}
	return false;
}

/**
 * Assign/remove a capability to/from a given role
 *
 * @since 0.1
 * 
 * @param string $lowest_role The role the given capability should be assigned to or removed from
 * 		  string $capability The capability that should be assigned to or removed from the role
 */
function wopsta_update_capability($lowest_role, $capability){
	$add_capability = false;
	foreach (array('subscriber', 'contributor', 'author', 'editor', 'administrator') as $role) {
		if($lowest_role == $role)
			$add_capability = true;
		$the_role = get_role($role);
		if(empty($the_role) )
			continue;
		$add_capability ? $the_role->add_cap($capability) : $the_role->remove_cap($capability);
	}	
}
?>