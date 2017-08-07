<?php
$complete_message =  '<div id="message" class="updated fade"><p>';
$complete_message_error =  '<div id="message" class="error"><p>';
$save_message = '<strong>'.__('Match save successfully.','wp-football').'</strong>'; 
$error_message = '<strong>'.__('Error!','wp-football').'</strong><br>';
$delete_message = '<strong>'.__('Match(es) deleted successfully.','wp-football').'</strong>';
$update_message = '<strong>'.__('Match changed successfully.','wp-football').'</strong>';
$path = admin_url() . 'admin.php';

if (!empty($_POST['action'])) $_GET['action'] = $_POST['action'];

if (!$_GET['action'] || !isset($_GET['action'])) {
	$_GET['action'] = 'edit';
	$_GET['new'] = 1;
}

$id_league = (empty($_GET['id_league'])) ? $_POST['id_league'] : $_GET['id_league'];
$id_match = (empty($_GET['id_match'])) ? $_POST['id_match'] : $_GET['id_match'];
$match = $wpdb->get_row("SELECT * FROM {$table_prefix}fb_match WHERE fb_match_id = '$id_match'");
$league = $wpdb->get_row("SELECT * FROM {$table_prefix}fb_league WHERE fb_league_id = '$id_league'");
$leagues = $wpdb->get_results("SELECT * FROM {$table_prefix}fb_league");

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
						$id_match = substr($n,4);
						$wpdb->query("DELETE FROM {$table_prefix}fb_match WHERE fb_match_id = '$id_match'");
					}
				}
			}
						
		case 'save':
			if ($_GET['action'] != 'delete') {
				$erro = '';
				$message = true;
				$id_league = $_POST['id_league'];
				$match_number = $_POST['match_number'];
				$id_group = $_POST['id_group'];
				$id_phase = $_POST['id_phase'];
				$team1 = $_POST['team1'];
				$team2 = $_POST['team2'];
				$day_match = $_POST['day_match'];
				$month_match = $_POST['month_match'];
				$year_match = $_POST['year_match'];
				$time = $_POST['time'];
				$stadium = $_POST['stadium'];
				$city = $_POST['city'];
				$score1 = $_POST['score1'];
				$score2 = $_POST['score2'];
				$remark = $_POST['remark'];
				$nt = $_POST['nt'];
				if ($id_group == 0) $erro .= '<strong>'.__("Group field must be filled","wp-football").'</strong><br />'; 
				if ($id_league == 0) $erro .= '<strong>'.__("League field must be filled","wp-football").'</strong><br />'; 
				if ($team1 == 0) $erro .= '<strong>'.__("Team1 field must be filled","wp-football").'</strong><br />'; 
				if ($team2 == 0) $erro .= '<strong>'.__("Team2 field must be filled","wp-football").'</strong><br />'; 
				if ($day_match == '') $erro .= '<strong>'.__("Day field must be filled","wp-football").'</strong><br />'; 
				if ($month_match == '') $erro .= '<strong>'.__("Month field must be filled","wp-football").'</strong><br />'; 
				if ($year_match == '') $erro .= '<strong>'.__("Year field must be filled","wp-football").'</strong><br />'; 
				if ($time == '') $erro .= '<strong>'.__("Time field must be filled","wp-football").'</strong><br />'; 
			
				if ($erro == '') {
					if (!empty($_POST['update'])) {
						$id_match = $_GET['id_match'];
						$wpdb->query("UPDATE {$table_prefix}fb_match SET fb_match_id_league = '$id_league', fb_match_number = '$match_number', fb_match_id_group = '$id_group',  fb_match_id_phase = '$id_phase', fb_match_team1 = '$team1', fb_match_team2 = '$team2', fb_match_day = '$day_match', fb_match_month = '$month_match', fb_match_year = '$year_match', fb_match_time = '$time', fb_match_stadium = '$stadium', fb_match_city = '$city', fb_match_score1 = '$score1', fb_match_score2 = '$score2', fb_match_remark = '$remark', fb_match_nt = '$nt' WHERE fb_match_id = '$id_match'");
						$success = true;
						$success_message = $update_message;
						echo $sucess_message;
					}
					else {
						$wpdb->query("INSERT INTO {$table_prefix}fb_match VALUES (0, '$match_number', '$id_league', '$id_group', '$id_phase', '$team1', '$team2', '$day_match', '$month_match', '$year_match', '$time', '$stadium', '$city', '$score1', '$score2', '$nt', '$remark')");
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
				$id_match = $_GET['id_match'];
				$match_number = $match->fb_match_number;
				$id_group = $match->fb_match_id_group;
				$id_phase = $match->fb_match_id_phase;
				$team1 = $match->fb_match_team1;
				$team2 = $match->fb_match_team2;
				$day_match = $match->fb_match_day;
				$month_match = $match->fb_match_month;
				$year_match = $match->fb_match_year;
				$time = $match->fb_match_time;
				$stadium = $match->fb_match_stadium;
				$city = $match->fb_match_city;
				$score1 = $match->fb_match_score1;
				$score2 = $match->fb_match_score2;
				$remark = $match->fb_match_remark;
				$nt = $match->fb_match_nt;
				$title = __("Edit Match","wp-football");
			}
			else {
				$title = __("New Match","wp-football");
				$id_match = '';
				$match_number = '';
				$id_group = '0';
				$id_phase = '';
				$team1 = '0';
				$team2 = '0';
				$day_match = '';
				$time = '';
				$stadium = '';
				$city = '';
				$score1 = '';
				$score2 = '';
				$remark = '';
				$nt = '';
			}
?>		
		<div class="wrap">	
			<div class="icon-wp-football icon32">
			<br/>
			</div>
			<h2><?php _e('Match','wp-football') ?></h2>
			<div class="tablenav">
				<div class="alignleft">
					<a class="button-highlighted action_buttons" href="?page=wp-football/football-manager.php">&laquo; <?php _e('Back to Manage League','wp-football') ?></a>
					<br class="clear" />
				</div>
			</div>
			<form name="blogform" method="post" action="?page=wp-football/football-matches.php&amp;action=save&amp;id_match=<?php echo $_GET['id_match'] ?>&amp;paged=<?php echo $_GET['paged'] ?>"> 
				<div class="stuffbox metabox-holder" style="padding-top:0;">
					<h3><?php echo $title ?></h3>
					<br class="clear" />
					<table class="form-table">
						<tr valign="top">
							<th scope="row"><?php _e('League','wp-football'); ?></th>
							<td>
								<select name="match_league_select" id="match_league_select">
									<option value='0'><?php _e('--- Select ---','wp-football') ?></option> 
							<?php foreach ($leagues as $l) { ?>
									<option value="<?php echo $l->fb_league_id; ?>" <?php if ($l->fb_league_id == $id_league) echo 'selected="selected"'; ?>><?php echo $l->fb_league_name; ?></option>
							<?php } ?>	
								</select> 
							</td>
						</tr>	
						<tr>
							<th scope="row"><?php _e('Phase','wp-football') ?></th>
							<td>
								<select name='id_phase' tabindex="1" id="phases">
									<option value='0'><?php _e('--- Select ---','wp-football') ?></option> 
							<?php $phases = $wpdb->get_results("SELECT * FROM {$table_prefix}fb_phase WHERE fb_phase_id_league = '$id_league'");
								foreach ($phases as $p) { ?>		
									<option value='<?php echo $p->fb_phase_id; ?>' <?php if ($id_phase == $p->fb_phase_id) echo "selected=\"selected\"" ?>><?php echo $p->fb_phase_name; ?></option>  
							<?php } ?>		
								</select>
						    </td>
						</tr>
						<tr>
							<th scope="row"><?php _e('Group','wp-football') ?> *</th>
							<td>
								<select name='id_group' tabindex="2" id="groups">
									<option value='0'><?php _e('--- Select ---','wp-football') ?></option> 
							<?php $groups = $wpdb->get_results("SELECT * FROM {$table_prefix}fb_group WHERE fb_group_id_league = '$id_league'");
								foreach ($groups as $gr) { ?>		
									<option value='<?php echo $gr->fb_group_id; ?>' <?php if ($id_group == $gr->fb_group_id) echo "selected=\"selected\"" ?>><?php echo $gr->fb_group_name; ?></option>  
							<?php } ?>		
								</select>
						    </td>
						</tr>
						<tr valign="top">
							<th scope="row"><?php _e('Match Number','wp-football') ?> *</th>
							<td>
								<input name="match_number" type="text" value="<?php echo $match_number ?>" size="5" tabindex="3" />
							</td>
						</tr>
						<tr>
							<th scope="row"><?php _e('Team 1','wp-football') ?> *</th>
							<td>
								<select name='team1' tabindex="4" id="team1">
									<option value='0'><?php _e('--- Select ---','wp-football') ?></option> 
									<option value='999' <?php if ($team1 == 999) echo "selected=\"selected\"" ?>><?php _e('Not Defined','wp-football') ?></option> 
							<?php $teams = $wpdb->get_results("SELECT fb_team_id, fb_team_name FROM {$table_prefix}fb_team WHERE fb_team_id_league = '$id_league' ORDER BY fb_team_name");
								foreach ($teams as $t) { ?>		
									<option value='<?php echo $t->fb_team_id; ?>' <?php if ($team1 == $t->fb_team_id) echo "selected=\"selected\"" ?>><?php echo $t->fb_team_name; ?></option>  
							<?php } ?>		
								</select>
						    </td>
						</tr>
						<tr>
							<th scope="row"><?php _e('Team 2','wp-football') ?> *</th>
							<td>
								<select name='team2' tabindex="5" id="team2">
									<option value='0'><?php _e('--- Select ---','wp-football') ?></option> 
									<option value='999' <?php if ($team1 == 999) echo "selected=\"selected\"" ?>><?php _e('Not Defined','wp-football') ?></option> 
							<?php $teams = $wpdb->get_results("SELECT fb_team_id, fb_team_name FROM {$table_prefix}fb_team WHERE fb_team_id_league = '$id_league' ORDER BY fb_team_name");
								foreach ($teams as $t) { ?>		
									<option value='<?php echo $t->fb_team_id; ?>' <?php if ($team2 == $t->fb_team_id) echo "selected=\"selected\"" ?>><?php echo $t->fb_team_name; ?></option>  
							<?php } ?>		
								</select>
						    </td>
						</tr>
						<tr valign="top">
							<th scope="row"><?php _e('Date','wp-football') ?> *</th>
							<td>
								<?php _e('Day','wp-football'); ?> <input name="day_match" type="text" value="<?php echo $day_match ?>" size="5" tabindex="6" />
								<?php _e('Month','wp-football'); ?> <input name="month_match" type="text" value="<?php echo $month_match ?>" size="5" tabindex="7" />
								<?php _e('Year','wp-football'); ?> <input name="year_match" type="text" value="<?php echo $year_match ?>" size="5" tabindex="8" />
							</td>
						</tr>
						<tr valign="top">
							<th scope="row"><?php _e('Time','wp-football') ?> *</th>
							<td>
								<input name="time" type="text" value="<?php echo $time ?>" size="5" tabindex="9" /> <small>(HH:MM)</small>
							</td>
						</tr>
						<tr valign="top">
							<th scope="row"><?php _e('Stadium','wp-football') ?></th>
							<td>
								<input name="stadium" type="text" value="<?php echo $stadium ?>" size="50" tabindex="10" />
							</td>
						</tr>
						<tr valign="top">
							<th scope="row"><?php _e('City','wp-football') ?></th>
							<td>
								<input name="city" type="text" value="<?php echo $city ?>" size="50" tabindex="11" />
							</td>
						</tr>
						<tr valign="top">
							<th scope="row"><?php _e('Score 1','wp-football') ?></th>
							<td>
								<input name="score1" type="text" value="<?php echo $score1 ?>" size="4" tabindex="12" />
							</td>
						</tr>
						<tr valign="top">
							<th scope="row"><?php _e('Score 2','wp-football') ?></th>
							<td>
								<input name="score2" type="text" value="<?php echo $score2 ?>" size="4" tabindex="13" />
							</td>
						</tr>
						<tr valign="top">
							<th scope="row"><?php _e('Remark','wp-football') ?></th>
							<td>
								<textarea name="remark" id="remark" cols="70" rows="3" tabindex="14"><?php echo $remark; ?></textarea>
							</td>
						</tr>
					</table>
					<br />
				</div>
					<?php
					if ($_GET["new"] == true) { ?>
						<span class="submit"><input type="submit" name="submit" class="button-primary" tabindex="20" value="<?php _e('Save','wp-football') ?> &raquo;" /></span>
					<?php } else { ?>
						<span class="submit"><input type="submit" class="button-primary" name="submit" tabindex="20" value="<?php _e('Change','wp-football') ?> &raquo;" /></span>
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
				<?php if ($id_league) require_once("football_matches_list.php"); 
				?>
			</div>
		<div id="extra" style="display:none;"><div id="loading"><img src="<?php echo plugins_url('wp-football/images/ajax-loader.gif'); ?>" alt="" style="vertical-align:middle;" /> &nbsp;&nbsp;<?php _e('Loading. Wait...','wp-football'); ?></div></div>      
