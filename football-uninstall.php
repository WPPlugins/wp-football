<?php
/*
+----------------------------------------------------------------+
|							                                     |
|	Plugin: wp-footbal version beta								 |
|	Copyright (c) 2009 Lester "GaMerZ" Chan						 |
|																 |
|	Based in:													 |
|	- Lester "GaMerZ" Chan										 |
|	- http://lesterchan.net										 |
|																 |
|	File Information:											 |
|	- Uninstall wp-football										 |
|	- wp-content/plugins/wp-football/football-uninstall.php		 |
|																 |
+----------------------------------------------------------------+
*/


### Check Whether User Can Manage football
if(!current_user_can('manage_football')) {
	die('Access Denied');
}


### Variables Variables Variables
$base_name = plugin_basename('wp-football/football-manager.php');
$base_page = admin_url() . 'admin.php?page='.$base_name;
$mode = trim($_GET['mode']);
$football_tables = array($wpdb->prefix.'fb_league',$wpdb->prefix.'fb_team',$wpdb->prefix.'fb_continent',$wpdb->prefix.'fb_phase',$wpdb->prefix.'fb_group',$wpdb->prefix.'fb_match',$wpdb->prefix.'fb_template');
$football_settings = array('wpfootball_version','wpfootball_criteria');


### Form Processing 
if(!empty($_POST['do'])) {
	// Decide What To Do
	switch($_POST['do']) {
		//  Uninstall WP-EMail
		case __('UNINSTALL wp-football', 'wp-football') :
			if(trim($_POST['uninstall_football_yes']) == 'yes') {
				echo '<div id="message" class="updated fade">';
				echo '<p>';
				foreach($football_tables as $table) {
					$wpdb->query("DROP TABLE {$table}");
					echo '<font style="color: green;">';
					printf(__('Table \'%s\' has been deleted.', 'wp-football'), "<strong><em>{$table}</em></strong>");
					echo '</font><br />';
				}
				echo '</p>';
				echo '<p>';
				foreach($football_settings as $setting) {
					$delete_setting = delete_option($setting);
					if($delete_setting) {
						echo '<font color="green">';
						printf(__('Setting Key \'%s\' has been deleted.', 'wp-football'), "<strong><em>{$setting}</em></strong>");
						echo '</font><br />';
					} else {
						echo '<font color="red">';
						printf(__('Error deleting Setting Key \'%s\'.', 'wp-football'), "<strong><em>{$setting}</em></strong>");
						echo '</font><br />';
					}
				}
				echo '</p>';
				echo '</div>'; 
				$mode = 'end-UNINSTALL';
			}
			break;
	}
}


### Determines Which Mode It Is
switch($mode) {
		//  Deactivating WP-EMail
		case 'end-UNINSTALL':
			$deactivate_url = 'plugins.php?action=deactivate&amp;plugin=wp-football/wp-football.php';
			if(function_exists('wp_nonce_url')) { 
				$deactivate_url = wp_nonce_url($deactivate_url, 'deactivate-plugin_wp-football/wp-football.php');
			}
			echo '<div class="wrap">';
			echo '<h2>'.__('Uninstall wp-football', 'wp-footbal').'</h2>';
			echo '<p><strong>'.sprintf(__('<a href="%s">Click Here</a> To Finish The Uninstallation And wp-football Will Be Deactivated Automatically.', 'wp-football'), $deactivate_url).'</strong></p>';
			echo '</div>';
			break;
	// Main Page
	default:
?>
<!-- Uninstall WP-EMail -->
<form method="post" action="<?php echo admin_url() . 'admin.php'; ?>?page=<?php echo plugin_basename(__FILE__); ?>">
<div class="wrap">
	<div class="icon-wp-football icon32"><br /></div>
	<h2><?php _e('Uninstall wp-football', 'wp-football'); ?></h2>
	<p>
		<?php _e('Deactivating wp-football plugin does not remove any data that may have been created, such as the wp-football options and the wp-football tables. To completely remove this plugin, you can uninstall it here.', 'wp-football'); ?>
	</p>
	<p style="color: red">
		<strong><?php _e('WARNING:', 'wp-football'); ?></strong><br />
		<?php _e('Once uninstalled, this cannot be undone. You should use a Database Backup plugin of WordPress to back up all the data first.', 'wp-football'); ?>
	</p>
	<p style="color: red">
		<strong><?php _e('The following WordPress Options/Tables will be DELETED:', 'wp-football'); ?></strong><br />
	</p>
	<table class="widefat">
		<thead>
			<tr>
				<th><?php _e('WordPress Options', 'wp-football'); ?></th>
				<th><?php _e('WordPress Tables', 'wp-football'); ?></th>
			</tr>
		</thead>
		<tr>
			<td valign="top">
				<ol>
				<?php
					foreach($football_settings as $settings) {
						echo '<li>'.$settings.'</li>'."\n";
					}
				?>
				</ol>
			</td>
			<td valign="top" class="alternate">
				<ol>
				<?php
					foreach($football_tables as $tables) {
						echo '<li>'.$tables.'</li>'."\n";
					}
				?>
				</ol>
			</td>
		</tr>
	</table>
	<p>&nbsp;</p>
	<p style="text-align: center;">
		<input type="checkbox" name="uninstall_football_yes" value="yes" />&nbsp;<?php _e('Yes', 'wp-football'); ?><br /><br />
		<input type="submit" name="do" value="<?php _e('UNINSTALL wp-football', 'wp-football'); ?>" class="button" onclick="return confirm('<?php _e('You Are About To Uninstall wp-football From WordPress.\nThis Action Is Not Reversible.\n\n Choose [Cancel] To Stop, [OK] To Uninstall.', 'wp-football'); ?>')" />
	</p>
	<p>Credit: <a href="http://lesterchan.net/">Lester 'GaMerZ' Chan</a></p>
</div>
</form>
<?php
} // End switch($mode)
?>