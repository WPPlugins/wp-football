<?php
$complete_message =  '<div id="message" class="updated fade"><p>';
$complete_message_error =  '<div id="message" class="error"><p>';
$save_message = '<strong>'.__('Match save successfully.','wp-football').'</strong>'; 
$error_message = '<strong>'.__('Error!','wp-football').'</strong><br>';
$delete_message = '<strong>'.__('Match(es) deleted successfully.','wp-football').'</strong>';
$update_message = '<strong>'.__('Match changed successfully.','wp-football').'</strong>';
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
				$id_group = $_POST['id_group'];
				$id_phase = $_POST['id_phase'];
				$score1 = $_POST['score1'];
				$score2 = $_POST['score2'];
				if ($id_league == 0) $erro .= '<strong>'.__("League field must be filled","wp-football").'</strong><br />'; 
				if ($id_match == 0) $erro .= '<strong>'.__("Match not selected","wp-football").'</strong><br />'; 
				if ($id_phase == 0) $erro .= '<strong>'.__("Phase field must be filled","wp-football").'</strong><br />'; 
				if ($id_group == 0) $erro .= '<strong>'.__("Group field must be filled","wp-football").'</strong><br />'; 
				if ($score1 == '') $erro .= '<strong>'.__("Score1 field must be filled","wp-football").'</strong><br />'; 
				if ($score2 == '') $erro .= '<strong>'.__("Score2 field must be filled","wp-football").'</strong><br />'; 
			
				if ($erro == '') {
					$id_match = $_POST['id_match'];
					$wpdb->query("UPDATE {$table_prefix}fb_match SET  fb_match_score1 = '$score1', fb_match_score2 = '$score2' WHERE fb_match_id = '$id_match'");
					$op = get_option('wpfootball_criteria');
					$op = $op[$id_league];
					$teams = $wpdb->get_results("SELECT * FROM {$table_prefix}fb_team WHERE fb_team_id_league = '$id_league' AND fb_team_id_group = '$id_group'");
					$c_teams = 0;
					foreach ($teams as $key => $team) {
						$c_teams++;
						$matches = $wpdb->get_results("SELECT * FROM {$table_prefix}fb_match WHERE fb_match_id_group = '$id_group' AND (fb_match_team1 = '$team->fb_team_id' OR fb_match_team2 = '$team->fb_team_id')");
						$played = 0; $won = 0; $draw = 0; $loss = 0; $gf = 0; $ga = 0; $pts = 0;
						foreach($matches as $mtch) {
							if ($mtch->fb_match_score1 == '') continue;
							if ($mtch->fb_match_team1 == $team->fb_team_id) { 
								$played += 1;
								if ($mtch->fb_match_score1 > $mtch->fb_match_score2) { 
									$won += 1;
									$pts += $op['pw'];
								} else {
									if ($mtch->fb_match_score1 == $mtch->fb_match_score2) {
										$draw += $op['pd'];
										$pts += 1;
									} else {	
										$loss += 1;
									}	
								}	
								$gf += $mtch->fb_match_score1;
								$ga += $mtch->fb_match_score2;
							} else {	
								$played += 1;
								if ($mtch->fb_match_score2 > $mtch->fb_match_score1) { 
									$won += 1;
									$pts += 3;
								} else {
									if ($mtch->fb_match_score1 == $mtch->fb_match_score2) {
										$draw += 1;
										$pts += 1;
									} else {	
										$loss += 1;
									}	
								}	
								$gf += $mtch->fb_match_score2;
								$ga += $mtch->fb_match_score1;
							}	
						}
						$wpdb->query("UPDATE {$table_prefix}fb_team SET fb_team_played = '$played', fb_team_won = '$won',  fb_team_draw = '$draw', fb_team_loss = '$loss', fb_team_gf = '$gf', fb_team_ga = '$ga', fb_team_pts = '$pts' WHERE fb_team_id = '$team->fb_team_id'");
						$points[$team->fb_team_id] = $pts;
						$ogd[$team->fb_team_id] = $gf - $ga;
						$ogf[$team->fb_team_id] = $gf;
						$onw[$team->fb_team_id] = $won;
					}	
					if ($c_teams) {
						$opc = get_option('wpfootball_criteria');
						$opc = $opc[$id_league];
						if ($opc['nw']) $c[$opc['nw']] = $onw;
						if ($opc['ogd']) $c[$opc['ogd']] = $ogd;
						if ($opc['ogf']) $c[$opc['ogf']] = $ogf;
						$count = count($c);
						echo $opc['onw'];
						if ($count == 1) array_multisort( $points, SORT_DESC, $c[1], SORT_DESC, $teams);
						if ($count == 2) array_multisort( $points, SORT_DESC, $c[1], SORT_DESC, $c[2], SORT_DESC, $teams);
						if ($count == 3) array_multisort( $points, SORT_DESC, $c[1], SORT_DESC, $c[2], SORT_DESC, $c[3], SORT_DESC, $teams);
	//					array_multisort( $points, SORT_DESC, $ogd, SORT_DESC, $ogf, SORT_DESC, $teams);
						$count = 0;
						foreach ($teams as $team) {
							$count++;
							$wpdb->query("UPDATE {$table_prefix}fb_team SET fb_team_class = '$count' WHERE fb_team_id = '$team->fb_team_id'");
						}
					}	
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
				$id_match = $_GET['id_match'];
				$id_group = $match->fb_match_id_group;
				$id_phase = $match->fb_match_id_phase;
				$team1 = $wpdb->get_var("SELECT fb_team_name FROM {$table_prefix}fb_team WHERE fb_team_id = '$match->fb_match_team1'"); 
				$score1 = $match->fb_match_score1;
				$score2 = $match->fb_match_score2;
				$team2 = $wpdb->get_var("SELECT fb_team_name FROM {$table_prefix}fb_team WHERE fb_team_id = '$match->fb_match_team2'"); 
				$title = __("Edit Result","wp-football");
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
			<h2><?php _e('Results','wp-football') ?></h2>
			<div class="tablenav">
				<div class="alignleft">
					<a class="button-highlighted action_buttons" href="?page=wp-football/football-manager.php">&laquo; <?php _e('Back to Manage League','wp-football') ?></a>
					<br class="clear" />
				</div>
			</div>
			<form name="blogform" method="post" action="?page=wp-football/football-results.php&amp;action=save&amp;paged=<?php echo $_GET['paged'] ?>"> 
				<div class="stuffbox metabox-holder" style="padding-top:0;">
					<h3><?php echo $title ?></h3>
					<br class="clear" />
					<table class="form-table">
						<tr valign="top">
							<th scope="row"><?php _e('League','wp-football'); ?> *</th>
							<td>
								<select name="results_league_select" id="results_league_select">
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
								<select name='id_phase' tabindex="1" id="results_phase">
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
								<select name='id_group' tabindex="2" id="results_group">
									<option value='0'><?php _e('--- Select ---','wp-football') ?></option> 
							<?php $groups = $wpdb->get_results("SELECT * FROM {$table_prefix}fb_group WHERE fb_group_id_league = '$id_league'");
								foreach ($groups as $gr) { ?>		
									<option value='<?php echo $gr->fb_group_id; ?>' <?php if ($id_group == $gr->fb_group_id) echo "selected=\"selected\"" ?>><?php echo $gr->fb_group_name; ?></option>  
							<?php } ?>		
								</select>
						    </td>
						</tr>
						<tr>
							<th scope="row"><?php _e('Result','wp-football') ?> *</th>
							<td>
								<span id="team1"><?php echo $team1; ?></span> <input type="text" name="score1" id="score1" tabindex="4" size="4" value="<?php echo $score1; ?>" /> x
								<input type="text" name="score2" id="score2" tabindex="5" size="4" value="<?php echo $score2; ?>" />	<span id="team2"><?php echo $team2; ?></span> 					   							</td>
						</tr>
					</table>
					<br />
				</div>
						<span class="submit"><input type="submit" class="button-primary" name="submit" tabindex="20" value="<?php _e('Change','wp-football') ?> &raquo;" /></span>
						<?php if ($id_group) { ?>
						<span class="submit classification"><a class="button-primary" href="<?php echo plugins_url('wp-football/football_classification.php') ?>?league=<?php echo $id_league ?>&amp;group=<?php echo $id_group; ?>&amp;iframe=true&amp;width=90%&amp;height=90%" rel="prettyPhoto[G<?php echo $id_group; ?>']"><?php _e('Classification','wp-football'); ?></a></span>
						<?php } ?>
						<input type="hidden" name="update" value="1" />
						<input type="hidden" name="results" id="results" value="1" />
						<input type="hidden" name="id_league" id="id_league" value="<?php echo $id_league; ?>" />
						<input type="hidden" name="id_match" id="id_match" value="<?php echo $id_match; ?>" />
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
				<?php require_once("football_matches_load.php"); 
				?>
			</div>
		<div id="extra" style="display:none;"><div id="loading"><img src="<?php echo plugins_url('wp-football/images/ajax-loader.gif'); ?>" alt="" style="vertical-align:middle;" /> &nbsp;&nbsp;<?php _e('Loading. Wait...','wp-football'); ?></div></div>      
