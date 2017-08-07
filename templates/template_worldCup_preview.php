<?php
require_once ('../football-functions.php');
if (!function_exists('add_action')) {
	$wp_root = '../../../..';
	if (file_exists($wp_root.'/wp-load.php')) {
		require_once($wp_root.'/wp-load.php');
	} else {
		require_once($wp_root.'/wp-config.php');
	}
}
require_once( "../football-functions.php");
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<title>Preview Template</title>
<link rel="stylesheet" href="<?php bloginfo('home') ?>/wp-content/plugins/wp-football/css/football.css" type="text/css" media="screen, projection" />
<script type="text/javascript" src="<?php bloginfo('home') ?>/wp-content/plugins/wp-football/js/jquery-1.3.2.min.js"></script>
<script type="text/javascript" src="<?php bloginfo('home') ?>/wp-content/plugins/wp-football/js/jquery.tools.min.js"></script>
<script type="text/javascript" src="<?php bloginfo('home') ?>/wp-content/plugins/wp-football/js/wp-football.js"></script>
</head>

<body>
<?php
	global $wpdb, $table_prefix;
	$league = $_GET['league'];
	$template= $wpdb->get_row("SELECT * FROM {$table_prefix}fb_template WHERE fb_template_id = '1'");

	$stringQuery = '';
	$fc = unserialize($template->fb_template_fields_c);
	$fm = unserialize($template->fb_template_fields_m);
	$i = 0;
	foreach ($fc as $c) {
		if ($i == 0) $stringQuery .= $c;
		else $stringQuery .= ", ".$c;
		$i++;
	}
	
	$phases = get_phases_league($league);
	$template_result = "\n<div class='wpf_container'>\n";
	$template_result .= "   <a class='prev'></a>\n";
	$template_result .= "   <div class='wpf_scrollable wpf_scrollable".$league."'>\n";
	$template_result .= '      <ul id="wpFootball_ul'.$league.'" class="wpFootball wpFootball'.$league.'">'."\n";
	$template_result .= '          <li><a href="#tabTeams" class="wpf'.$league.'">'.__('Teams','wp-football')."</a></li>\n";
	$template_result .= '          <li><a href="#tabGroup" class="wpf'.$league.'">'.__('Groups','wp-football')."</a></li>\n";
	foreach ($phases as $phase) {
		$template_result .= '         <li><a href="#tabPhase'.$phase->fb_phase_id.'" class="wpf'.$phase->fb_phase_id.'" id="wpf'.$phase->fb_phase_id.'" title="'. $phase->fb_phase_name.'">'.$phase->fb_phase_name."</a></li>\n";
	}
	$template_result .= "      </ul>\n";
	$template_result .= "   </div>\n";
	$template_result .= "   <a class='next'></a>\n";
	$template_result .= '   <div class="wpFootball_divs wpFootball_panes wpFootball_divs'.$league.'">'."\n";
	$template_result .= '      <div class="tb tabTeams">'."\n";
	$template_result .= get_teams($league);
	$template_result .= '      </div>';         

	$template_result .= '      <div class="tb">'."\n";
	$where = '';
	if ($group != 0) $where = " AND fb_team_id_group = '$group' "; 
	$teams = $wpdb->get_results("SELECT fb_team_id_group,fb_team_link_info, ".$stringQuery." FROM {$table_prefix}fb_team WHERE fb_team_id_league = '$league'".$where." ORDER BY fb_team_id_group, fb_team_class");
	$dir_icons = $wpdb->get_var("SELECT fb_league_dir_icons FROM {$table_prefix}fb_league WHERE fb_league_id = '$league'");
	$group = '';
	foreach ($teams as $team) {
		if ($group != $team->fb_team_id_group) {
			if ($group != '') {
				$template_result .= "  \n</tbody>\n   </table>\n";
				$count = 0;
			}	
			$group = $team->fb_team_id_group;
			$name_group = $wpdb->get_var("SELECT fb_group_name FROM {$table_prefix}fb_group WHERE fb_group_id = '$team->fb_team_id_group'");
			$template_result .= "\n".'<table class="wcup">'."\n";
			$template_result .= ' <caption>'.$name_group.'</caption>'."\n";
			$template_result .= ' <thead>';
			$template_result .= "   <tr>\n";
			$template_result .= '      <th class="t">'.__('Team','wp-football')."</th>\n";
			if ($fc[3]) $template_result .= '      <th>'.__('P','wp-football')."</th>\n";
			if ($fc[4]) $template_result .= '      <th>'.__('W','wp-football')."</th>\n";
			if ($fc[5]) $template_result .= '      <th>'.__('D','wp-football')."</th>\n";
			if ($fc[6]) $template_result .= '      <th>'.__('L','wp-football')."</th>\n";
			if ($fc[7]) $template_result .= '      <th>'.__('GF','wp-football')."</th>\n";
			if ($fc[8]) $template_result .= '      <th>'.__('GA','wp-football')."</th>\n";
			if ($fc[7] && $fc[8]) $template_result .= '      <th>'.__('GD','wp-football')."</th>\n";
			if ($fc[9]) $template_result .= '      <th>'.__('Pts','wp-football')."</th>\n";
			$template_result .= "   </tr>\n";
			$template_result .= ' </thead>';
			$template_result .= ' <tbody>';
		} 
		$count++;
		if ($count % 2 == 0) $class_t = ' class="even"';
		else $class_t = ' class="odd"';
		$icon = $team->fb_team_symbol;
		$template_result .= '   <tr'.$class_t.'>'."\n";
		if ($fc[1]) {
		$template_result .= '      <td>'.($team->fb_team_class&&$fc[10]?$team->fb_team_class.' ':'').($icon && $fc[1] ? '<img src="'.get_option('home')."/".$dir_icons.$team->fb_team_symbol.'" alt="'.$team->fb_team_name.'" style="border:1px solid #8a8a8a;" />&nbsp;' : '&nbsp;').'<a href="'.$team->fb_team_link_info.'" title="'.$team->fb_team_name.'">'.$team->fb_team_name_abb.'</a></td>'."\n";
		}
		if ($fc[0]) {
		$template_result .= '      <td>'.($team->fb_team_class&&$fc[10]?$team->fb_team_class.' ':'').' '.($icon && $fc[0] ? '<img src="'.get_option('home')."/".$dir_icons.$team->fb_team_symbol.'" alt="'.$team->fb_team_name.'" style="border:1px solid #8a8a8a;" />&nbsp;&nbsp;' : '&nbsp;').'<a href="'.$team->fb_team_link_info.'" title="'.$team->fb_team_name.'">'.$team->fb_team_name.'</a></td>'."\n";
		}
		if ($fc[3]) $template_result .= '      <td>'.$team->fb_team_played.'</td>'."\n";  
		if ($fc[4]) $template_result .= '      <td>'.$team->fb_team_won.'</td>'."\n";  
		if ($fc[5]) $template_result .= '      <td>'.$team->fb_team_draw.'</td>'."\n";  
		if ($fc[6]) $template_result .= '      <td>'.$team->fb_team_loss.'</td>'."\n";  
		if ($fc[7]) $template_result .= '      <td>'.$team->fb_team_gf.'</td>'."\n";  
		if ($fc[8]) $template_result .= '      <td>'.$team->fb_team_ga.'</td>'."\n";  
		if ($fc[7] && $fc[8]) $template_result .= '      <td>'.($team->fb_team_gf-$team->fb_team_ga).'</td>'."\n";  
		if ($fc[9]) $template_result .= '      <td>'.$team->fb_team_pts.'</td>'."\n";  
		$template_result .= "   </tr>\n";
	}
	$template_result .= " </tbody>  \n</table>\n";
	
	$template_result .= '      </div>';         
	foreach ( $phases as $phase ) {
		$template_result .= '      <div class="tb">'."\n";
		$template_result .= get_matches($league,$phase->fb_phase_id);
		$template_result .= "    \n</div>\n\n";
	}
	$template_result .= "   </div>\n</div>\n";
	$template_result .= get_scripts_worldCup($league,'1');

	echo $template_result;


function get_scripts_worldCup($league, $layout_instance='1') {
$s = '
<script type="text/javascript">
/* <![CDATA[ */
jQuery(document).ready(function($){	
		var tabInicial'.$league.' = jQuery("ul.wpFootball'.$league.' li a:eq(0)").offset().left
		jQuery("ul.wpFootball'.$league.'").tabs("div.wpFootball_divs'.$league.' > .tb");
	// enabling scrollable
		jQuery("div.wpf_scrollable'.$league.'").scrollable({
			onSeek: function(e) { 
				var api = jQuery("ul.wpFootball'.$league.'").tabs(0); 
				api.click(e);
			}, 
			size: 1,
			items: "#wpFootball_ul'.$league.'",
			clickable: true,
			keyboard: false
		}); 
});
/* ]]> */
</script>';
return $s;
}	
?>
</body>
</html>