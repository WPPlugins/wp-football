<?php

$ajax = '';
if (isset($_GET['ajax'])) $ajax = $_GET['ajax'];
if ($ajax) {
	if (!function_exists('add_action')) {
		$wp_root = '../../..';
		if (file_exists($wp_root.'/wp-load.php')) {
			require_once($wp_root.'/wp-load.php');
		} else {
			require_once($wp_root.'/wp-config.php');
		}
	}
	$id_league = $_GET['id_league'];
	$id_group = $_GET['id_group'];
	$id_phase = (isset($_GET['id_phase']) ? $_GET['id_phase'] : get_first_phase_league($id_league));
	$id_template = 2;
	$f = $_GET['f'];
	if ($f == 'get_groups') {
		echo call_user_func_array ($f, array($id_league, $id_group, $id_template));
	} else {
		echo call_user_func_array ($f, array($id_league, $id_phase, $id_group, $id_template));
	}	
?>
<script type="text/javascript">
jQuery().ready(function(){	
	jQuery('.tzcLocal').bind('click', tzcLocal); 
	jQuery('.tzcClient').bind('click', tzcClient); 
	tzcInit('dd/MM HH:mm');
});
</script>
<?php
}

### Function: Get Leagues
if(!function_exists('get_leagues')) {
	function get_leagues() {
		global $wpdb, $table_prefix;
		return $wpdb->get_results("SELECT * FROM {$table_prefix}fb_league ORDER BY fb_league_name");
	}
}
### Function: Get Groups League
if(!function_exists('get_groups_league')) {
	function get_groups_league($league,$order=1) {
		global $wpdb, $table_prefix;
		if ($order == 1) $orderGroup = 'fb_group_name';
		else $orderGroup = 'fb_group_id';
		return $wpdb->get_results("SELECT * FROM {$table_prefix}fb_group WHERE fb_group_id_league = '$league' ORDER BY ".$orderGroup);
	}
}
### Function: Get Groups Phase
if(!function_exists('get_groups_phase')) {
	function get_groups_phase($league,$phase) {
		global $wpdb, $table_prefix;
		return $wpdb->get_results("SELECT DISTINCT fb_group_name_abb FROM {$table_prefix}fb_group g, {$table_prefix}fb_match m WHERE fb_match_id_league = '$league' AND fb_match_id_group = fb_group_id AND fb_match_id_phase = '$phase' ORDER BY fb_group_id");
	}
}
### Function: Get Phases League
if(!function_exists('get_phases_league')) {
	function get_phases_league($league) {
		global $wpdb, $table_prefix;
		return $wpdb->get_results("SELECT * FROM {$table_prefix}fb_phase WHERE fb_phase_id_league = '$league' ORDER BY fb_phase_order");
	}
}
### Function: Get Id First Phase of League
//if(!function_exists('get_first_phase_league')) {
	function get_first_phase_league($league) {
		global $wpdb, $table_prefix;
		return $wpdb->get_var("SELECT fb_phase_id FROM {$table_prefix}fb_phase WHERE fb_phase_id_league = '$league' ORDER BY fb_phase_order LIMIT 1");
	}
//}
### Function: Get Phase League
//if(!function_exists('get_phase_league')) {
	function get_phase_league($league,$phase) {
		global $wpdb, $table_prefix;
		return $wpdb->get_row("SELECT * FROM {$table_prefix}fb_phase WHERE fb_phase_id_league = '$league' AND fb_phase_id ='$phase'");
	}
//}
### Function: Get Templates
if(!function_exists('get_widget_templates')) {
	function get_widget_templates() {
		global $wpdb, $table_prefix;
		return $wpdb->get_results("SELECT * FROM {$table_prefix}fb_template WHERE fb_template_category = '1' ORDER BY fb_template_name");
	}
}
### Function: Get Teams
//if(!function_exists('get_teams')) {
	function get_teams($league) { 
		global $wpdb, $table_prefix;
		$teams = $wpdb->get_results("SELECT * FROM {$table_prefix}fb_team WHERE fb_team_id_league = '$league' ORDER BY fb_team_continent, fb_team_name");
		$dir_icons = $wpdb->get_var("SELECT fb_league_dir_icons FROM {$table_prefix}fb_league WHERE fb_league_id = '$league'");
		$continent = '';
		$temp = '<ul style="margin-bottom: 10px;">'."\n";
		foreach ($teams as $team) {
			if ($continent != $team->fb_team_continent) {
				if ($continent != '') $temp .= "   </ul>\n</li>\n";
				if ($team->fb_team_continent != $continent) {
					$continent = $team->fb_team_continent;
					$name_continent = $wpdb->get_var("SELECT fb_continent_name FROM {$table_prefix}fb_continent WHERE fb_continent_id = '$team->fb_team_continent'");
					$temp .= '<li style="clear:left;"><strong>'.$name_continent.'</strong>'."\n".'    <ul style="margin: 5px 0 0 10px; list-style: none;">'."\n";
				}
			} 
			$temp .= '      <li style="float:left; margin-right: 5px; font-size: 11px;"><img src="'.get_option('home')."/".$dir_icons.$team->fb_team_symbol.'" alt="'.$team->fb_team_name.'" style="border:1px solid #8a8a8a; margin: 0;" /><br /><a href="'.$team->fb_team_link_info.'" title="'.$team->fb_team_name.'">'.$team->fb_team_name_abb.'</a></li>'."\n";  
		}
		$temp .= "   </ul>\n</li>\n";
		$temp .= "</ul>\n";
		$temp .= '<br style="clear:both;" />';
		return $temp;
	}
//}
### Function: Get Groups
//if(!function_exists('get_groups')) {
	function get_groups($league, $group, $id_template=1) { 
		global $wpdb, $table_prefix;
		$template= $wpdb->get_row("SELECT * FROM {$table_prefix}fb_template WHERE fb_template_id = '$id_template'");
	
		$fc = unserialize($template->fb_template_fields_c);
		
		
		$where = '';
		if ($group != 0) $where = " AND fb_team_id_group = '$group' "; 
		$teams = $wpdb->get_results("SELECT * FROM {$table_prefix}fb_team WHERE fb_team_id_league = '$league'".$where." ORDER BY fb_team_id_group, fb_team_class");
		$dir_icons = $wpdb->get_var("SELECT fb_league_dir_icons FROM {$table_prefix}fb_league WHERE fb_league_id = '$league'");
		$group = '';
		foreach ($teams as $team) {
			if ($group != $team->fb_team_id_group) {
				if ($group != '') {
					$temp.= "  \n</tbody>\n   </table>\n";
					$count = 0;
				}	
				$group = $team->fb_team_id_group;
				$name_group = $wpdb->get_var("SELECT fb_group_name FROM {$table_prefix}fb_group WHERE fb_group_id = '$team->fb_team_id_group'");
				$temp.= "\n".'<table class="wcup">'."\n";
				if ($id_template != 2) $temp.= ' <caption>'.$name_group.'</caption>'."\n";
				$temp.= ' <thead>';
				$temp.= "   <tr>\n";
				$temp.= '      <th class="t">'.__('Team','wp-football')."</th>\n";
				if ($fc[9]) $temp.= '      <th class="o">'.__('Pts','wp-football')."</th>\n";
				if ($fc[3]) $temp.= '      <th class="o">'.__('P','wp-football')."</th>\n";
				if ($fc[4]) $temp.= '      <th class="o">'.__('W','wp-football')."</th>\n";
				if ($fc[5]) $temp.= '      <th class="o">'.__('D','wp-football')."</th>\n";
				if ($fc[6]) $temp.= '      <th class="o">'.__('L','wp-football')."</th>\n";
				if ($fc[7]) $temp.= '      <th class="o">'.__('GF','wp-football')."</th>\n";
				if ($fc[8]) $temp.= '      <th class="o">'.__('GA','wp-football')."</th>\n";
				if ($fc[7] && $fc[8]) $temp.= '      <th class="o">'.__('GD','wp-football')."</th>\n";
				$temp.= "   </tr>\n";
				$temp.= ' </thead>';
				$temp.= ' <tbody>';
			} 
			$count++;
			if ($count % 2 == 0) $class_t = ' class="even"';
			else $class_t = ' class="odd"';
			$icon = $team->fb_team_symbol;
			$temp.= '   <tr'.$class_t.'>'."\n";
			if ($fc[1]) {
			$temp.= '      <td class="t"><span class="stg">'.($team->fb_team_class&&$fc[10]?$team->fb_team_class.' ':'').'</span>'.($icon && $fc[1] ? '<img src="'.get_option('home')."/".$dir_icons.$team->fb_team_symbol.'" alt="'.$team->fb_team_name.'" title="'.$team->fb_team_name.'" style="border:1px solid #8a8a8a;" />&nbsp;' : '&nbsp;').'<a href="'.$team->fb_team_link_info.'" title="'.$team->fb_team_name.'">'.$team->fb_team_name_abb.'</a></td>'."\n";
			}
			if ($fc[0]) {
			$temp.= '      <td class="t"><span class="stg">'.($team->fb_team_class&&$fc[10]?$team->fb_team_class.' ':'').'</span> '.($icon && $fc[0] ? '<img src="'.get_option('home')."/".$dir_icons.$team->fb_team_symbol.'" alt="'.$team->fb_team_name.'" title="'.$team->fb_team_name.'" style="border:1px solid #8a8a8a;" />&nbsp;&nbsp;' : '&nbsp;').'<a href="'.$team->fb_team_link_info.'" title="'.$team->fb_team_name.'">'.$team->fb_team_name.'</a></td>'."\n";
			}
			if ($fc[9]) $temp.= '      <td class="o stg">'.$team->fb_team_pts.'</td>'."\n";  
			if ($fc[3]) $temp.= '      <td class="o">'.$team->fb_team_played.'</td>'."\n";  
			if ($fc[4]) $temp.= '      <td class="o">'.$team->fb_team_won.'</td>'."\n";  
			if ($fc[5]) $temp.= '      <td class="o">'.$team->fb_team_draw.'</td>'."\n";  
			if ($fc[6]) $temp.= '      <td class="o">'.$team->fb_team_loss.'</td>'."\n";  
			if ($fc[7]) $temp.= '      <td class="o">'.$team->fb_team_gf.'</td>'."\n";  
			if ($fc[8]) $temp.= '      <td class="o">'.$team->fb_team_ga.'</td>'."\n";  
			if ($fc[7] && $fc[8]) $temp.= '      <td class="o">'.($team->fb_team_gf-$team->fb_team_ga).'</td>'."\n";  
			$temp.= "   </tr>\n";
		}
		$temp.= " </tbody>  \n</table>\n";
		return $temp;
	}
//}
### Function: Get Matches
//if(!function_exists('get_matches')) {
	function get_matches($league,$phase,$group=0,$id_template=1) { 
		global $wpdb, $table_prefix;
		$template= $wpdb->get_row("SELECT * FROM {$table_prefix}fb_template WHERE fb_template_id = '$id_template'");
	
		$fm = unserialize($template->fb_template_fields_m);
		
		$where = '';
		if ($group != 0) $where = " AND fb_match_id_group = '$group' "; 
		$matches = $wpdb->get_results("SELECT * FROM {$table_prefix}fb_match WHERE fb_match_id_league = '$league' AND fb_match_id_phase = '$phase'".$where." ORDER BY fb_match_id_phase, fb_match_id_group, fb_match_number");
		$dir_icons = $wpdb->get_var("SELECT fb_league_dir_icons FROM {$table_prefix}fb_league WHERE fb_league_id = '$league'");
		$timezone = $wpdb->get_var("SELECT fb_league_timezone FROM {$table_prefix}fb_league WHERE fb_league_id = '$league'");
		$group = '';
		$temp = '';
		$count_m = 0;
		$temp .= '<div class="tzcContainer"><div class="tzcLocal"><p>'.__('Match times are currently set to local time, please click here to convert to your time zone.','wp-football').' <img src="'. plugins_url('wp-football/images/tzclocal.gif').'" width="19" height="12" alt="" /></p></div><div class="tzcClient" style="display:none;"><p>'.__('Match times are currently set to your time zone, please click here to revert to local time.','wp-football').' <img src="'. plugins_url('wp-football/images/tzclocal.gif').'" width="19" height="12" alt="" /></p></div></div>';
		$temp .= "\n".'<table class="wcup">'."\n";
		foreach ($matches as $match) {
			$count_m++;
			if ($group != $match->fb_match_id_group) {
				if ($group != '') {
					$temp .= "  \n</tbody>\n</table>\n<table class='wcup'>\n";
				}	
				$group = $match->fb_match_id_group;
				$name_group = $wpdb->get_var("SELECT fb_group_name FROM {$table_prefix}fb_group WHERE fb_group_id = '$match->fb_match_id_group'");
				if ($id_template == 2) {
					$pha = get_phase_league($league,$phase);
					$name_group = $pha->fb_phase_name;
				}
				$temp .= ' <caption>'.$name_group.'</caption>'."\n";
				$temp .= ' <thead>'."\n";
				$temp .= "   <tr>\n";
				if ($fm[0]) $temp .= '      <th class="n">'.__('Match','wp-football')."</th>\n";
				$temp .= '      <th>'.__('Date-Time','wp-football')."</th>\n";
				$temp .= '      <th colspan="5" class="tcenter">'.__('Results','wp-football')."</th>\n";
				if ($fm[4]) $temp .= '      <th class="v">'.__('City','wp-football')."</th>\n";
				if ($fm[5]) $temp .= '      <th class="v">'.__('Stadium','wp-football')."</th>\n";
				$temp .= "   </tr>\n";
				$temp .= " </thead>\n";
				$temp .= " <tbody>\n";
			} 
			if ($count_m % 2 == 0) $class_m = ' class="even"';
			else $class_m = ' class="odd"';
			$team1 = $wpdb->get_row("SELECT * FROM {$table_prefix}fb_team WHERE fb_team_id = '$match->fb_match_team1'");	
			$team2 = $wpdb->get_row("SELECT * FROM {$table_prefix}fb_team WHERE fb_team_id = '$match->fb_match_team2'");
			$hm = split(":",$match->fb_match_time);
			$timestamp = mktime(date($hm[0])+$timezone, date($hm[1]), 0, date($match->fb_match_month), date($match->fb_match_day), date($match->fb_match_year));
			$dt = date("YmdHi", $timestamp);
			$t1 = $t2 = '';
			if ($match->fb_match_team1 == 999) {
				$teams = split(",",$match->fb_match_remark);
				$t1 = $teams[0];
				$t2 = $teams[1];
			}
			$icon1 = $team1->fb_team_symbol;
			$icon2 = $team2->fb_team_symbol;
			$date_time = $match->fb_match_day."/".$match->fb_match_month." ".$match->fb_match_time;
			$temp .= '   <tr'.$class_m.'>'."\n";
			if ($fm[0]) $temp .= '      <td class="n tcenter">'.$match->fb_match_number.'</td>'."\n";
			$temp .= '      <td><span class="datetime" title="'.$date_time.','.$dt.'">'.$date_time.'</span></td>'."\n";
			if ($fm[3]) $temp .= '      <td class="i">'.($icon1 ? '<img src="'.get_option('home')."/".$dir_icons.$team1->fb_team_symbol.'" alt="'.($t1 ? $t1 : $team1->fb_team_name).'" />' : '&nbsp;').'</td>'."\n";
			if ($fm[1]) $temp .= '      <td class="s"><a href="'.($t1 ? "#" : $team1->fb_team_link_info).'" title="'.($t1 ? $t1 : $team1->fb_team_name).'">'.($t1 ? $t1 : $team1->fb_team_name).'</a></td>'."\n";  
			if ($fm[2])	$temp .= '      <td><a href="'.($t1 ? "#" : $team1->fb_team_link_info).'" title="'.($t1 ? $t1 : $team1->fb_team_name).'">&nbsp;'.($t1 ? $t1 : $team1->fb_team_name_abb).'</a></td>'."\n";  
			$temp .= '      <td class="tcenter">'.($match->fb_match_score1 != '' ? $match->fb_match_score1 : '--').' x '.($match->fb_match_score2 != '' ? $match->fb_match_score2 : '--').'</td>'."\n";
			if ($fm[1]) $temp .= '      <td class="tright s"><a href="'.($t2 ? "#" : $team2->fb_team_link_info).'" title="'.($t2 ? $t2 : $team2->fb_team_name).'">'.($t2 ? $t2 : $team2->fb_team_name).'</a>&nbsp;</td>'."\n";  
			if ($fm[2]) $temp .= '      <td class="tright"><a href="'.($t2 ? "#" : $team2->fb_team_link_info).'" title="'.($t2 ? $t2 : $team2->fb_team_name).'">'.($t2 ? $t2 : $team2->fb_team_name_abb).'</a></td>'."\n";  
			if ($fm[3]) $temp .= '      <td class="i">'.($icon2 ? '<img src="'.get_option('home')."/".$dir_icons.$team2->fb_team_symbol.'" alt="'.($t2 ? $t2 : $team2->fb_team_name).'" />' : '&nbsp;').'</td>'."\n";
			if ($fm[4]) $temp .= '      <td class="v">'.$match->fb_match_city.'</td>'."\n";
			if ($fm[5]) $temp .= '      <td class="v">'.$match->fb_match_stadium.'</td>'."\n";
			$temp .= "   </tr>\n";
		}
		$temp .= " </tbody>\n  </table>\n";
		return $temp;
	}
//}	

### Function: Get Next Matches
if(!function_exists('get_next_matches')) {
	function get_next_matches($league,$id_template=1) { 
		global $wpdb, $table_prefix;
		$template= $wpdb->get_row("SELECT * FROM {$table_prefix}fb_template WHERE fb_template_id = '$id_template'");
	
		$fm = unserialize($template->fb_template_fields_m);

		$next_match = $wpdb->get_row("SELECT fb_match_year, fb_match_month, fb_match_day from {$table_prefix}fb_match 
				WHERE fb_match_id_league = '$league' AND fb_match_score1 = '' 
				AND (fb_match_day >= DATE_FORMAT(curdate(), '%d') AND fb_match_month >= DATE_FORMAT(curdate(), '%m')
				AND fb_match_year >= DATE_FORMAT(curdate(), '%Y')) LIMIT 1");
				
		$dnext = $next_match->fb_match_year.'-'.$next_match->fb_match_month.'-'.$next_match->fb_match_day;
		$dnext1 = date('Y-m-d',mktime(24, 0, 0, $next_match->fb_match_month, $next_match->fb_match_day, $next_match->fb_match_year)); 
		$dnext2 = date('Y-m-d',mktime(48, 0, 0, $next_match->fb_match_month, $next_match->fb_match_day, $next_match->fb_match_year)); 
			
		$matches = $wpdb->get_results("SELECT * FROM {$table_prefix}fb_match WHERE fb_match_id_league = '$league' AND fb_match_score1 = ''
				AND (fb_match_day = DATE_FORMAT('$dnext', '%d') AND fb_match_month = DATE_FORMAT('$dnext', '%m')
				AND fb_match_year = DATE_FORMAT('$dnext', '%Y'))
				OR (fb_match_day = DATE_FORMAT('$dnext1', '%d') AND fb_match_month = DATE_FORMAT('$dnext1', '%m')
				AND fb_match_year = DATE_FORMAT('$dnext1', '%Y'))
				OR (fb_match_day = DATE_FORMAT('$dnext2', '%d') AND fb_match_month = DATE_FORMAT('$dnext2', '%m')
				AND fb_match_year = DATE_FORMAT('$dnext2', '%Y')) ORDER BY fb_match_year, fb_match_month, fb_match_day, fb_match_time");

		$dir_icons = $wpdb->get_var("SELECT fb_league_dir_icons FROM {$table_prefix}fb_league WHERE fb_league_id = '$league'");
		$timezone = $wpdb->get_var("SELECT fb_league_timezone FROM {$table_prefix}fb_league WHERE fb_league_id = '$league'");
		$day_match = '';
		$temp = '';
		$count_m = 0;
		$temp .= '<div class="tzcContainer"><div class="tzcLocal"><p>'.__('Match times are currently set to local time, please click here to convert to your time zone.','wp-football').' <img src="'. plugins_url('wp-football/images/tzclocal.gif').'" width="19" height="12" alt="" /></p></div><div class="tzcClient" style="display:none;"><p>'.__('Match times are currently set to your time zone, please click here to revert to local time.','wp-football').' <img src="'. plugins_url('wp-football/images/tzclocal.gif').'" width="19" height="12" alt="" /></p></div></div>';
		$temp .= "\n".'<table class="wcup">'."\n";
		foreach ($matches as $match) {
			$count_m++;
			if ($day_match != $match->fb_match_day) {
				if ($day_match != '') {
					$temp .= "  \n</tbody>\n</table>\n<table class='wcup'>\n";
				}	
				$day_match = $match->fb_match_day;
				$temp .= ' <caption>'.__('Day: ','wp-football').$match->fb_match_day.'/'.$match->fb_match_month.'/'.$match->fb_match_year.'</caption>'."\n";
				$temp .= ' <thead>'."\n";
				$temp .= "   <tr>\n";
				if ($fm[0]) $temp .= '      <th class="n">'.__('Match','wp-football')."</th>\n";
//				$temp .= '      <th>'.__('G','wp-football')."</th>\n";
				$temp .= '      <th>'.__('Date-Time','wp-football')."</th>\n";
				$temp .= '      <th colspan="5" class="tcenter">'.__('Matches','wp-football')."</th>\n";
				if ($fm[4]) $temp .= '      <th class="v">'.__('City','wp-football')."</th>\n";
				if ($fm[5]) $temp .= '      <th class="v">'.__('Stadium','wp-football')."</th>\n";
				$temp .= "   </tr>\n";
				$temp .= " </thead>\n";
				$temp .= " <tbody>\n";
			} 
			if ($count_m % 2 == 0) $class_m = ' class="even"';
			else $class_m = ' class="odd"';
			$team1 = $wpdb->get_row("SELECT * FROM {$table_prefix}fb_team WHERE fb_team_id = '$match->fb_match_team1'");	
			$team2 = $wpdb->get_row("SELECT * FROM {$table_prefix}fb_team WHERE fb_team_id = '$match->fb_match_team2'");
			$name_group_abb = $wpdb->get_var("SELECT fb_group_name_abb FROM {$table_prefix}fb_group WHERE fb_group_id = '$match->fb_match_id_group'");
			$hm = split(":",$match->fb_match_time);
			$timestamp = mktime(date($hm[0])+$timezone, date($hm[1]), 0, date($match->fb_match_month), date($match->fb_match_day), date($match->fb_match_year));
			$dt = date("YmdHi", $timestamp);
			$t1 = $t2 = '';
			if ($match->fb_match_team1 == 999) {
				$teams = split(",",$match->fb_match_remark);
				$t1 = $teams[0];
				$t2 = $teams[1];
			}
			$icon1 = $team1->fb_team_symbol;
			$icon2 = $team2->fb_team_symbol;
			$date_time = $match->fb_match_day."/".$match->fb_match_month." ".$match->fb_match_time;
			$temp .= '   <tr'.$class_m.'>'."\n";
			if ($fm[0]) $temp .= '      <td class="n tcenter">'.$match->fb_match_number.'</td>'."\n";
//			$temp .= '      <td class="n tcenter">'.$name_group_abb.'</td>'."\n";
			$temp .= '      <td><span class="datetime" title="'.$date_time.','.$dt.'">'.$date_time.'</span></td>'."\n";
			if ($fm[3]) $temp .= '      <td class="i">'.($icon1 ? '<img src="'.get_option('home')."/".$dir_icons.$team1->fb_team_symbol.'" alt="'.($t1 ? $t1 : $team1->fb_team_name).'" />' : '&nbsp;').'</td>'."\n";
			if ($fm[1]) $temp .= '      <td class="s"><a href="'.($t1 ? "#" : $team1->fb_team_link_info).'" title="'.($t1 ? $t1 : $team1->fb_team_name).'">'.($t1 ? $t1 : $team1->fb_team_name).'</a></td>'."\n";  
			if ($fm[2])	$temp .= '      <td><a href="'.($t1 ? "#" : $team1->fb_team_link_info).'" title="'.($t1 ? $t1 : $team1->fb_team_name).'">&nbsp;'.($t1 ? $t1 : $team1->fb_team_name_abb).'</a></td>'."\n";  
			$temp .= '      <td class="tcenter">'.($match->fb_match_score1 != '' ? $match->fb_match_score1 : '--').' x '.($match->fb_match_score2 != '' ? $match->fb_match_score2 : '--').'</td>'."\n";
			if ($fm[1]) $temp .= '      <td class="tright s"><a href="'.($t2 ? "#" : $team2->fb_team_link_info).'" title="'.($t2 ? $t2 : $team2->fb_team_name).'">'.($t2 ? $t2 : $team2->fb_team_name).'</a>&nbsp;</td>'."\n";  
			if ($fm[2]) $temp .= '      <td class="tright"><a href="'.($t2 ? "#" : $team2->fb_team_link_info).'" title="'.($t2 ? $t2 : $team2->fb_team_name).'">'.($t2 ? $t2 : $team2->fb_team_name_abb).'</a></td>'."\n";  
			if ($fm[3]) $temp .= '      <td class="i">'.($icon2 ? '<img src="'.get_option('home')."/".$dir_icons.$team2->fb_team_symbol.'" alt="'.($t2 ? $t2 : $team2->fb_team_name).'" />' : '&nbsp;').'</td>'."\n";
			if ($fm[4]) $temp .= '      <td class="v">'.$match->fb_match_city.'</td>'."\n";
			if ($fm[5]) $temp .= '      <td class="v">'.$match->fb_match_stadium.'</td>'."\n";
			$temp .= "   </tr>\n";
		}
		$temp .= " </tbody>\n  </table>\n";
		return $temp;
	}
	if (!$count_m) {
		$temp  = "\n".'<table class="wcup">'."\n";
		$temp .= '   <tr><td>No match over the next two days.</td></tr>';
		$temp  = "\n".'</table>'."\n";	
		return $temp;	
	}
}	

?>