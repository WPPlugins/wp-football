<?php

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
	$template_result .= get_groups($league,'','1');
	$template_result .= '      </div>';         
	foreach ( $phases as $phase ) {
		$template_result .= '      <div class="tb">'."\n";
		$template_result .= get_matches($league,$phase->fb_phase_id);
		$template_result .= "    \n</div>\n\n";
	}
	$template_result .= "   </div>\n</div>\n";
	$template_result .= get_scripts_worldCup($league,'1');

	return $template_result;


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
