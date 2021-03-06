<?php
$complete_message =  '<div id="message" class="updated fade"><p>';
$complete_message_error =  '<div id="message" class="error"><p>';
$save_message = '<strong>'.__('Group save successfully.','wp-football').'</strong>'; 
$error_message = '<strong>'.__('Error!','wp-football').'</strong><br>';
$delete_message = '<strong>'.__('Group(s) deleted successfully.','wp-football').'</strong>';
$update_message = '<strong>'.__('Group changed successfully.','wp-football').'</strong>';
$path = admin_url() . 'admin.php';

if (!empty($_POST['action'])) $_GET['action'] = $_POST['action'];
if (!$_GET['action'] || !isset($_GET['action'])) {
	$_GET['action'] = 'edit';
	$_GET['new'] = 1;
}

$id_league = (empty($_GET['id_league'])) ? $_POST['id_league'] : $_GET['id_league'];
$leagues = $wpdb->get_results("SELECT * FROM {$table_prefix}fb_league ORDER BY fb_league_id DESC");
$id_group = (empty($_GET['id_group'])) ? $_POST['id_group'] : $_GET['id_group'];
$group= $wpdb->get_row("SELECT * FROM {$table_prefix}fb_group WHERE fb_group_id = '$id_group'");

if (!isset($_GET['action'])) $_GET['action'] = 'edit';

if(!empty($_GET['action'])) {
	// Decide What To Do
	switch($_GET['action']) {			
		case 'delete':	
			$message = true;
			$success_message .= $delete_message;
			$success = true;
			if (isset($_POST) AND is_array($_POST)) {
				foreach ($_POST as $n => $v) {
					if (substr($n,0,4) == "del_" AND $v == 'on') {
						$id_group = substr($n,4);
						$wpdb->query("DELETE FROM {$table_prefix}fb_match WHERE fb_match_id_group = '$id_group'");
						$wpdb->query("DELETE FROM {$table_prefix}fb_team WHERE fb_team_id_group = '$id_group'");
						$wpdb->query("DELETE FROM {$table_prefix}fb_group WHERE fb_group_id = '$id_group'");
					}
				}
			}
						
		case 'save':
			if ($_GET['action'] != 'delete') {
				$erro = '';
				$message = true;
				$id_league = $_POST['id_league'];
				$order = $_POST['order'];
				$name = $_POST['name'];
				$name_abb = $_POST['name_abb'];
				if ($order == '') $erro .= '<strong>'.__("Order field must be filled","wp-football").'</strong><br />'; 
				if ($name == '') $erro .= '<strong>'.__("Name field must be filled","wp-football").'</strong><br />'; 
			
				if ($erro == '') {
					if (!empty($_POST['update'])) {
						$id_group = $_GET['id_group'];
						$wpdb->query("UPDATE {$table_prefix}fb_group SET fb_group_id_league = '$id_league', fb_group_order = '$order', fb_group_name = '$name', fb_group_name_abb = '$name_abb' WHERE fb_group_id = '$id_group'");
						$success = true;
						$success_message = $update_message;
					}
					else {
						$wpdb->query("INSERT INTO {$table_prefix}fb_group VALUES (0, '$id_league', '$order', '$name','$name_abb')");
						$success = true;
						$success_message = $save_message;
					}
				}
				else {
					$error_message = $erro;
					$error = true;
				}	
			}			
			$_GET["new"] = 1;	
			
		case 'edit':
			if ($_GET["new"] == false) {
				$id_group = $_GET['id_group'];
				$order = $group->fb_group_order;
				$name = $group->fb_group_name;
				$name_abb = $group->fb_group_name_abb;
				$title = __("Edit Group","wp-football");
			}
			else {
				$title = __("New Group","wp-football");
				$id_group = '';
				$order = '';
				$name = '';
				$name_abb = '';
			}
?>		
		<div class="wrap">	
			<div class="icon-wp-football icon32">
			<br/>
			</div>
			<h2><?php _e('Groups','wp-football') ?></h2>
			<div class="tablenav">
				<div class="alignleft">
					<a class="button-highlighted action_buttons" href="<?php echo $path; ?>?page=wp-football/football-manager.php">&laquo; <?php _e('Back to Manage League','wp-football') ?></a>
					<br class="clear" />
				</div>
			</div>
			<form name="blogform" method="post" action="<?php echo $path; ?>?page=wp-football/football-groups.php&amp;action=save&amp;id_group=<?php echo $_GET['id_group'] ?>&amp;paged=<?php echo $_GET['paged'] ?>"> 
				<div class="stuffbox metabox-holder" style="padding-top:0;">
					<h3><?php echo $title ?></h3>
					<br class="clear" />
					<table class="form-table">
						<tr valign="top">
							<th scope="row"><?php _e('League','wp-football'); ?></th>
							<td>
								<select name="group_league_select" id="group_league_select" <?php if (!$_GET["new"]) echo 'disabled="disabled"'; ?> tabindex="1">
									<option value='0'><?php _e('--- Select ---','wp-football') ?></option> 
							<?php foreach ($leagues as $l) { ?>
									<option value="<?php echo $l->fb_league_id; ?>" <?php if ($l->fb_league_id == $id_league) echo 'selected="selected"'; ?>><?php echo $l->fb_league_name; ?></option>
							<?php } ?>	
								</select> 
							</td>
						</tr>	
						<tr valign="top">
							<th scope="row"><?php _e('Order','wp-football'); ?> *</th>
							<td>
								<input type="text" name="order" id="order" value="<?php echo $order ?>" size="10" tabindex="2" />
							</td>
						</tr>
						<tr valign="top">
							<th scope="row"><?php _e('Name','wp-football'); ?> *</th>
							<td>
								<input type="text" name="name" id="name" value="<?php echo $name ?>" size="70" tabindex="3" />
							</td>
						</tr>
						<tr valign="top">
							<th scope="row"><?php _e('Name Abbreviated','wp-football') ?></th>
							<td>
								<input name="name_abb" type="text" value="<?php echo $name_abb ?>" size="30" tabindex="4" />
							</td>
						</tr>
					</table>
					<br />
				</div>
					<?php
					if ($_GET["new"] == true) { ?>
						<span class="submit"><input type="submit" name="submit" class="button-primary" tabindex="10" value="<?php _e('Save','wp-football') ?> &raquo;" /></span>
					<?php } else { ?>
						<span class="submit"><input type="submit" class="button-primary" name="submit" tabindex="10" value="<?php _e('Change','wp-football') ?> &raquo;" /></span>
						<input type="hidden" name="update" value="1" />
					<?php } ?>
						<input type="hidden" name="id_league" id="id_league" value="<?php echo $id_league; ?>" />
			</form>
		</div>	
		<hr />
			<?php	
		    break;
	}	
	if ( $message == true ) {
		if  ( $error == true ) {
			$complete_message_error .= $error_message;
			$complete_message_error .= '</p></div>';
		    echo $complete_message_error;
		} 
		if ( $success == true ) {
			$complete_message .= $success_message;
			$complete_message .= '</p></div>';
		    echo $complete_message;
		}		
	}
}	
?>
		<div id="resposta">
			<?php if ($id_league) require_once("football_groups_list.php"); 
			?>
		</div>
		<div id="extra" style="display:none;"><div id="loading"><img src="<?php echo plugins_url('wp-football/images/ajax-loader.gif'); ?>" alt="" style="vertical-align:middle;" /> &nbsp;&nbsp;<?php _e('Loading. Wait...','wp-football'); ?></div></div>      
