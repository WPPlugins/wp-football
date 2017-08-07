<?php
$complete_message =  '<div id="message" class="updated fade"><p>';
$complete_message_error =  '<div id="message" class="error"><p>';
$error_message = '<strong>'.__('Error!','wp-football').'</strong><br>';
$update_message = '<strong>'.__('Teams Match updated successfully.','wp-football').'</strong>';
$path = admin_url() . 'admin.php';

if (!empty($_POST['action'])) $_GET['action'] = $_POST['action'];
if (!isset($_GET['action'])) $_GET['action'] = 'edit';

$id_league = (empty($_GET['id_league'])) ? $_POST['id_league'] : $_GET['id_league'];
$id_match = (empty($_GET['id_match'])) ? $_POST['id_match'] : $_GET['id_match'];
$match = $wpdb->get_row("SELECT * FROM {$table_prefix}fb_match WHERE fb_match_id = '$id_match'");
$league = $wpdb->get_row("SELECT * FROM {$table_prefix}fb_league WHERE fb_league_id = '$id_league'");
$leagues = $wpdb->get_results("SELECT * FROM {$table_prefix}fb_league");

if(!empty($_GET['action'])) {
	switch($_GET['action']) {			
		case 'save':
				$erro = '';
				$message = true;
				$id_league = $_POST['id_league'];
				$id_phase = $_POST['id_phase'];
				$team1 = $_POST['team1'];
				$team2 = $_POST['team2'];
				if ($id_league == 0) $erro .= '<strong>'.__("League field must be filled","wp-football").'</strong><br />'; 
				if ($id_phase == 0) $erro .= '<strong>'.__("Phase to be updated must be filled","wp-football").'</strong><br />'; 
				if ($team1 == 0) $erro .= '<strong>'.__("Team1 field must be filled","wp-football").'</strong><br />'; 
				if ($team2 == 0) $erro .= '<strong>'.__("Team2 field must be filled","wp-football").'</strong><br />'; 
			
				if ($erro == '') {
					$wpdb->query("UPDATE {$table_prefix}fb_match SET  fb_match_team1 = '$team1', fb_match_team2 = '$team2' WHERE fb_match_id = '$id_match'");
					$success = true;
					$success_message = $update_message;
					echo $sucess_message;
				}
				else {
					$error_message = $erro;
					$error = true;
				}		
				$_GET['new'] = 1;	
			
		case 'edit':
			if ($_GET["new"] == false) {
				$id_phase = $match->fb_match_id_phase;
				$tp = explode(',',$match->fb_match_remark);
				$type00 = substr($tp[0],0,1);
				$type01 = substr($tp[0],1);
				$type10 = substr($tp[1],0,1);
				$type11 = substr($tp[1],1);
				if ($type00 == 'W') {
					$match = $wpdb->get_row("SELECT * FROM {$table_prefix}fb_match WHERE fb_match_id_league = '$id_league' AND fb_match_number = '$type01'");
					if ($match->fb_match_score1) $team1 = ($match->fb_match_score1 > $match->fb_match_score2 ? $match->fb_match_team1 : $match->fb_match_team2);
					$match = $wpdb->get_row("SELECT * FROM {$table_prefix}fb_match WHERE fb_match_id_league = '$id_league' AND fb_match_number = '$type11'");
					if ($match->fb_match_score1) $team2 = ($match->fb_match_score1 > $match->fb_match_score2 ? $match->fb_match_team1 : $match->fb_match_team2);
				} else {
					$id_group1 = $wpdb->get_var("SELECT fb_group_id FROM {$table_prefix}fb_group WHERE fb_group_name_abb = '$type01'");
					$team1 = $wpdb->get_var("SELECT fb_team_id FROM {$table_prefix}fb_team WHERE fb_team_id_league = '$id_league' AND fb_team_class = '$type00' AND fb_team_id_group = '$id_group1'");
					$id_group2 = $wpdb->get_var("SELECT fb_group_id FROM {$table_prefix}fb_group WHERE fb_group_name_abb = '$type11'");
					$team2 = $wpdb->get_var("SELECT fb_team_id FROM {$table_prefix}fb_team WHERE fb_team_id_league = '$id_league' AND fb_team_class = '$type10' AND fb_team_id_group = '$id_group2'");
				}
				$title = __("Update Teams Phase","wp-football");
			} else {	
				$score1 = '';
				$score2 = '';
				$team1 = '';
				$team2 = '';
			}	
?>		
		<div class="wrap">	
			<div class="icon-wp-football icon32">
			<br/>
			</div>
			<h2><?php _e('Update Teams Phase','wp-football') ?></h2>
			<div class="tablenav">
				<div class="alignleft">
					<a class="button-highlighted action_buttons" href="?page=wp-football/football-manager.php">&laquo; <?php _e('Back to Manage League','wp-football') ?></a>
					<br class="clear" />
				</div>
			</div>
			<form name="blogform" method="post" action="?page=wp-football/football-update_phase.php&amp;action=save&amp;paged=<?php echo $_GET['paged'] ?>"> 
				<div class="stuffbox metabox-holder" style="padding-top:0;">
					<h3><?php echo $title ?></h3>
					<br class="clear" />
					<table class="form-table">
						<tr valign="top">
							<th scope="row"><?php _e('League','wp-football'); ?> *</th>
							<td>
								<select name="update_phase_select" id="update_phase_select">
									<option value='0'><?php _e('--- Select ---','wp-football') ?></option> 
							<?php foreach ($leagues as $l) { ?>
									<option value="<?php echo $l->fb_league_id; ?>" <?php if ($l->fb_league_id == $id_league) echo 'selected="selected"'; ?>><?php echo $l->fb_league_name; ?></option>
							<?php } ?>	
								</select> 
							</td>
						</tr>	
						<tr>
							<th scope="row"><?php _e('Phase','wp-football') ?> *</th>
							<td>
								<select name='id_phase' tabindex="1" id="phase_u">
									<option value='0'><?php _e('--- Select ---','wp-football') ?></option> 
							<?php $phases = $wpdb->get_results("SELECT * FROM {$table_prefix}fb_phase WHERE fb_phase_id_league = '$id_league'");
								foreach ($phases as $p) { ?>		
									<option value='<?php echo $p->fb_phase_id; ?>' <?php if ($id_phase == $p->fb_phase_id) echo "selected=\"selected\"" ?>><?php echo $p->fb_phase_name; ?></option>  
							<?php } ?>		
								</select>
						    </td>
						</tr>
						<?php if ($_GET["new"] == false && isset($_GET['new'])) { ?>
						<tr>
							<th scope="row"><?php _e('Team 1','wp-football') ?> *</th>
							<td><?php echo $tp[0]; ?>
								<select name='team1' tabindex="4" id="team1">
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
							<td><?php echo $tp[1]; ?>
								<select name='team2' tabindex="5" id="team2">
									<option value='999' <?php if ($team1 == 999) echo "selected=\"selected\"" ?>><?php _e('Not Defined','wp-football') ?></option> 
							<?php $teams = $wpdb->get_results("SELECT fb_team_id, fb_team_name FROM {$table_prefix}fb_team WHERE fb_team_id_league = '$id_league' ORDER BY fb_team_name");
								foreach ($teams as $t) { ?>		
									<option value='<?php echo $t->fb_team_id; ?>' <?php if ($team2 == $t->fb_team_id) echo "selected=\"selected\"" ?>><?php echo $t->fb_team_name; ?></option>  
							<?php } ?>		
								</select>
						    </td>
						</tr>
						<?php } ?>
					</table>
					<br />
				</div>
						<span class="submit"><input type="submit" class="button-primary" name="submit" tabindex="20" value="<?php _e('Update','wp-football') ?> &raquo;" /></span>
						<input type="hidden" name="update" value="1" />
						<input type="hidden" name="results" id="results" value="1" />
						<input type="hidden" name="id_match" id="id_match" value="<?php echo $id_match; ?>" />
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
				<?php require_once("football_matches_phase.php"); 
				?>
			</div>
		<div id="extra" style="display:none;"><div id="loading"><img src="<?php echo plugins_url('wp-football/images/ajax-loader.gif'); ?>" alt="" style="vertical-align:middle;" /> &nbsp;&nbsp;<?php _e('Loading. Wait...','wp-football'); ?></div></div>      
