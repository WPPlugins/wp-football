<?php
$complete_message =  '<div id="message" class="updated fade"><p>';
$complete_message_error =  '<div id="message" class="error"><p>';
$save_message = '<strong>'.__('Team save successfully.','wp-football').'</strong>'; 
$error_message = '<strong>'.__('Error!','wp-football').'</strong><br>';
$delete_message = '<strong>'.__('Team(s) deleted successfully.','wp-football').'</strong>';
$update_message = '<strong>'.__('Team changed successfully.','wp-football').'</strong>';
$path = admin_url() . 'admin.php';

if (!empty($_POST['action'])) $_GET['action'] = $_POST['action'];
if (!$_GET['action'] || !isset($_GET['action'])) {
	$_GET['action'] = 'edit';
	$_GET['new'] = 1;
}

$id_league = (empty($_GET['id_league'])) ? $_POST['id_league'] : $_GET['id_league'];
$id_team = (empty($_GET['id_team'])) ? $_POST['id_team'] : $_GET['id_team'];
$team = $wpdb->get_row("SELECT * FROM {$table_prefix}fb_team WHERE fb_team_id = '$id_team'");
$league = $wpdb->get_row("SELECT * FROM {$table_prefix}fb_league WHERE fb_league_id = '$id_league'");
$leagues = $wpdb->get_results("SELECT * FROM {$table_prefix}fb_league");

if(!empty($_GET['action'])) {
	// Decide What To Do
	switch($_GET['action']) {			
		case 'delete':	
			$message = true;
			$complete_message .= $delete_message;
			if (isset($_POST) AND is_array($_POST)) {
				foreach ($_POST as $n => $v) {
					if (substr($n,0,4) == "del_" AND $v == 'on') {
						$id_team = substr($n,4);
						$wpdb->query("DELETE FROM {$table_prefix}fb_team WHERE fb_team_id = '$id_team'");
					}
				}
			}
						
		case 'save':
			if ($_GET['action'] != 'delete') {
				$erro = '';
				$message = true;
				$id_league = $_POST['id_league'];
				$name = $_POST['name'];
				$name_abb = $_POST['name_abb'];
				$symbol = $_POST['symbol'];
				$link_info = $_POST['link_info'];
				$continent = $_POST['continent'];
				$group = $_POST['group'];
				if ($name == '') $erro .= '<strong>'.__("Name field must be filled","wp-football").'</strong><br />'; 
				if ($id_league == 0) $erro .= '<strong>'.__("League field must be filled","wp-football").'</strong><br />'; 
			
				if ($erro == '') {
					if (!empty($_POST['update'])) {
						$id_team = $_GET['id_team'];
						$wpdb->query("UPDATE {$table_prefix}fb_team SET fb_team_id_league = '$id_league', fb_team_id_group = '$group',  fb_team_name = '$name', fb_team_name_abb = '$name_abb', fb_team_symbol = '$symbol', fb_team_link_info = '$link_info', fb_team_continent = '$continent' WHERE fb_team_id = '$id_team'");
						$success = true;
						$success_message = $update_message;
						echo $sucess_message;
					}
					else {
						$wpdb->query("INSERT INTO {$table_prefix}fb_team VALUES (0, '$id_league', '$group', '$name', '$name_abb', '$symbol', '$link_info', '$continent', '0', '0', '0', '0', '0', '0', '0', '0')");
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
				$id_team = $_GET['id_team'];
				$name = $team->fb_team_name;
				$name_abb = $team->fb_team_name_abb;
				$symbol = $team->fb_team_symbol;
				$link_info = $team->fb_team_link_info;
				$continent = $team->fb_team_continent;
				$group = $team->fb_team_id_group;
				$title = __("Edit Team","wp-football");
			}
			else {
				$title = __("New Team","wp-football");
				$id_team = '';
				$name = '';
				$name_abb = '';
				$symbol = '';
				$link_info = '';
				$continent = '0';
				$group = '0';
			}
?>		
		<div class="wrap">	
			<div class="icon-wp-football icon32">
			<br/>
			</div>
			<h2><?php _e('Teams','wp-football') ?></h2>
			<div class="tablenav">
				<div class="alignleft">
					<a class="button-highlighted action_buttons" href="?page=wp-football/football-manager.php">&laquo; <?php _e('Back to Manage League','wp-football') ?></a>
					<br class="clear" />
				</div>
			</div>
			<form name="blogform" method="post" action="<?php echo $path ?>?page=wp-football/football-teams.php&amp;action=save&amp;id_team=<?php echo $_GET['id_team'] ?>&amp;paged=<?php echo $_GET['paged'] ?>"> 
				<div class="stuffbox metabox-holder" style="padding-top:0;">
					<h3><?php echo $title ?></h3>
					<br class="clear" />
					<table class="form-table">
						<tr valign="top">
							<th scope="row"><?php _e('League','wp-football'); ?></th>
							<td>
								<select name="league_select" id="league_select">
									<option value='0'><?php _e('--- Select ---','wp-football') ?></option> 
							<?php foreach ($leagues as $l) { ?>
									<option value="<?php echo $l->fb_league_id; ?>" <?php if ($l->fb_league_id == $id_league) echo 'selected="selected"'; ?>><?php echo $l->fb_league_name; ?></option>
							<?php } ?>	
								</select> 
							</td>
						</tr>	
						<tr valign="top">
							<th scope="row"><?php _e('Name','wp-football'); ?> *</th>
							<td>
								<input type="text" name="name" id="name" value="<?php echo $name ?>" size="70" tabindex="1" />
							</td>
						</tr>
						<tr valign="top">
							<th scope="row"><?php _e('Name Abbreviated','wp-football') ?></th>
							<td>
								<input name="name_abb" type="text" value="<?php echo $name_abb ?>" size="30" tabindex="2" />
							</td>
						</tr>
						<tr valign="top">
							<th scope="row"><?php _e('Symbol','wp-football') ?></th>
							<td>
								<input name="symbol" type="text" value="<?php echo $symbol ?>" size="70" tabindex="3" />
							</td>
						</tr>
						<tr valign="top">
							<th scope="row"><?php _e('Information Link','wp-football') ?></th>
							<td>
								<input name="link_info" type="text" value="<?php echo $link_info ?>" size="70" tabindex="4" />
							</td>
						</tr>
						<tr>
							<th scope="row"><?php _e('Continent','wp-football') ?></th>
							<td>
								<select name='continent' tabindex="5">
									<option value='0'><?php _e('--- Select ---','wp-football') ?></option> 
							<?php $continents = $wpdb->get_results("SELECT * FROM {$table_prefix}fb_continent");
								foreach ($continents as $cont) { ?>		
									<option value='<?php echo $cont->fb_continent_id; ?>' <?php if ($continent == $cont->fb_continent_id) echo "selected=\"selected\"" ?>><?php echo $cont->fb_continent_name; ?></option>  
							<?php } ?>		
								</select>
						    </td>
						</tr>
						<tr>
							<th scope="row"><?php _e('Group','wp-football') ?></th>
							<td>
								<select name='group' id="teamGroups" tabindex="6">
									<option value='0'><?php _e('--- Select ---','wp-football') ?></option> 
							<?php $groups = $wpdb->get_results("SELECT * FROM {$table_prefix}fb_group WHERE fb_group_id_league = '$id_league'");
								foreach ($groups as $gr) { ?>		
									<option value='<?php echo $gr->fb_group_id; ?>' <?php if ($group == $gr->fb_group_id) echo "selected=\"selected\"" ?>><?php echo $gr->fb_group_name; ?></option>  
							<?php } ?>		
								</select>
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
//if (!isset($_GET['action']) || $_GET['action'] != 'edit') {	
?>			
			<div id="resposta">
				<?php if ($id_league) require_once("football_teams_list.php"); 
				?>
			</div>
		<div id="extra" style="display:none;"><div id="loading"><img src="<?php echo plugins_url('wp-football/images/ajax-loader.gif'); ?>" alt="" style="vertical-align:middle;" /> &nbsp;&nbsp;<?php _e('Loading. Wait...','wp-football'); ?></div></div>      
