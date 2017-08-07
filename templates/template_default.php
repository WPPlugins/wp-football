<?php
	$groups = get_groups_league($league,'2');
	$phases = get_phases_league($league);
	$id_phase = get_first_phase_league($league);
	$count = 1;
	$template_result = '    <ul id="menu_groups">';
	foreach ($groups as $group) {
		$team = $wpdb->get_var("SELECT fb_team_id FROM {$table_prefix}fb_team WHERE fb_team_id_group = '$group->fb_group_id' LIMIT 1");
		if ($team) {
			if ($count == 1) {
				$template_result .= '         <li><a class="first sel" href="'.$league.','.$group->fb_group_id.','.get_option('home').'">'.$group->fb_group_name_abb."</a></li>\n";
				$id_group = $group->fb_group_id;
				$count++;
			} else {
				$template_result .= '         <li><a href="'.$league.','.$group->fb_group_id.','.get_option('home').'">'.$group->fb_group_name_abb."</a></li>\n";
			}
		}	
	}
	$template_result .= '<li class="wpf_loading"><img src="'. plugins_url('wp-football/images/loading.gif').'" alt="" style="vertical-align:middle;" /></li>';
	$template_result .= '    </ul>';
	$template_result .= "\n<div class='wpf_container'>\n";
	$template_result .= "   <div class='wpf_scrollable wpf_scrollable".$league."2'>\n";
	$template_result .= '      <ul id="wpFootball_ul'.$league.'2" class="wpFootball wpFootball'.$league.'2">'."\n";
	$template_result .= '          <li><a href="#tabTeams" class="wpf'.$league.'2">'.__('Classification','wp-football')."</a></li>\n";
	$template_result .= '          <li><a href="#tabGroup" class="wpf'.$league.'2">'.__('Matches','wp-football')."</a></li>\n";
	$template_result .= "      </ul>\n";
	$template_result .= "   </div>\n";
	$template_result .= '   <div class="wpFootball_divs wpFootball_panes wpFootball_divs'.$league.'2">'."\n";
	$template_result .= '      <div class="tb dtabTeams">'."\n";
	$template_result .= get_groups($league,$id_group,'2');
	$template_result .= '      </div>';         
	$template_result .= '      <div class="tb">'."\n";
	$template_result .= '         <div class="dtabMatches">'."\n";
	$template_result .= get_matches($league,$id_phase,$id_group,'2');
	$template_result .= "         </div>\n";
	$template_result .= "         <div style='clear:both;'>\n";
	$template_result .= "         <h3>".__('Phases','wp-football')."</h3>\n";
	$template_result .= '         <ul class="menu_phases">'."\n";
	foreach ( $phases as $phase ) {
		$dateInitial = $wpdb->get_row("SELECT fb_match_day, fb_match_month, fb_match_year FROM {$table_prefix}fb_match WHERE fb_match_id_phase = '$phase->fb_phase_id' ORDER BY fb_match_year, fb_match_month, fb_match_day LIMIT 1");
		$dti = '';
		if ($dateInitial->fb_match_day) $dti = " (".$dateInitial->fb_match_day."/".$dateInitial->fb_match_month."/".$dateInitial->fb_match_year.")";
		$template_result .= '         <li><a href="'.$league.','.$phase->fb_phase_id.','.get_option('home').'">'.$phase->fb_phase_name.$dti."</a></li>\n";
	}
	$template_result .= "         </ul>\n";
	$template_result .= "         </div>\n";
	$template_result .= '      </div>';         
	$template_result .= "   </div>\n</div>\n";
	$template_result .= get_scripts_default($league,'2');


	return $template_result;


function get_scripts_default($league, $template='1') {
$s = '
<script type="text/javascript">
/* <![CDATA[ */
jQuery(document).ready(function($){	
		var tabInicial'.$league.$template.' = jQuery("ul.wpFootball'.$league.$template.' li a:eq(0)").offset().left
		jQuery("ul.wpFootball'.$league.$template.'").tabs("div.wpFootball_divs'.$league.$template.' > .tb");
});
/* ]]> */
</script>';
return $s;
}	
?>
