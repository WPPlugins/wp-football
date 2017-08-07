<?php
require_once (dirname (__FILE__) . '/football-functions.php');
if (!function_exists('add_action')) {
	$wp_root = '../../..';
	if (file_exists($wp_root.'/wp-load.php')) {
		require_once($wp_root.'/wp-load.php');
	} else {
		require_once($wp_root.'/wp-config.php');
	}
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<title>Preview Template</title>
<link rel="stylesheet" href="<?php bloginfo('home') ?>/wp-content/plugins/wp-football/css/football.css" type="text/css" media="screen, projection" />
</head>

<body>
<?php
	$league = ($_GET['league'] ? $_GET['league'] : $_POST['league']);
	$group = ($_GET['group'] ? $_GET['group'] : $_POST['group']);
	global $wpdb, $table_prefix;
	if ($_POST['submit']) {
		$message = '';
		if (is_array ($_POST['played']) && count($_POST['played']) > 0) {
			foreach ($_POST['played'] as $key => $val) {
				$played = (is_numeric($val) ? $val : 0);
				$won = (is_numeric($_POST['won'][$key]) ? $_POST['won'][$key] : 0);
				$draw = (is_numeric($_POST['draw'][$key]) ? $_POST['draw'][$key] : 0);
				$loss = (is_numeric($_POST['loss'][$key]) ? $_POST['loss'][$key] : 0);
				$gf = (is_numeric($_POST['gf'][$key]) ? $_POST['gf'][$key] : 0);
				$ga = (is_numeric($_POST['ga'][$key]) ? $_POST['ga'][$key] : 0);
				$pts = (is_numeric($_POST['pts'][$key]) ? $_POST['pts'][$key] : 0);
				$class = (is_numeric($_POST['class'][$key]) ? $_POST['class'][$key] : 0);
				$wpdb->query("UPDATE {$table_prefix}fb_team SET fb_team_played = '$played', fb_team_won = '$won',  fb_team_draw = '$draw', fb_team_loss = '$loss', fb_team_gf = '$gf', fb_team_ga = '$ga', fb_team_pts = '$pts', fb_team_class = '$class' WHERE fb_team_id = '$key'");
			}
			$message =  '<div id="message" class="updated fade"><p>'.'<strong>'.__('Classification changed successfully.','wp-football').'</strong></p></div>'; 
		} 
	}		 	 

	$where = '';
	if ($group != 0) $where = " AND fb_team_id_group = '$group' "; 
	$teams = $wpdb->get_results("SELECT * FROM {$table_prefix}fb_team WHERE fb_team_id_league = '$league'".$where." ORDER BY fb_team_id_group, fb_team_class, fb_team_name");
	$dir_icons = $wpdb->get_var("SELECT fb_league_dir_icons FROM {$table_prefix}fb_league WHERE fb_league_id = '$league'");
	$group = '';
	$temp .= '<form name="class" action="" method="post">';
	foreach ($teams as $team) {
		if ($group != $team->fb_team_id_group) {
			if ($group != '') {
				$temp .= "  \n</tbody>\n   </table>\n";
				$count = 0;
			}	
			$group = $team->fb_team_id_group;
			$name_group = $wpdb->get_var("SELECT fb_group_name FROM {$table_prefix}fb_group WHERE fb_group_id = '$team->fb_team_id_group'");
			$temp .= "\n".'<table class="wcup">'."\n";
			$temp .= ' <caption>'.__('CLASSIFICATION ','wp-football').$name_group.'</caption>'."\n";
			$temp .= ' <thead>';
			$temp .= "   <tr>\n";
			$temp .= '      <th class="o">'.__('Clas','wp-football')."</th>\n";
			$temp .= '      <th class="t">'.__('Team','wp-football')."</th>\n";
			$temp .= '      <th class="o">'.__('Pts','wp-football')."</th>\n";
			$temp .= '      <th class="o">'.__('Pld','wp-football')."</th>\n";
			$temp .= '      <th class="o">'.__('W','wp-football')."</th>\n";
			$temp .= '      <th class="o">'.__('D','wp-football')."</th>\n";
			$temp .= '      <th class="o">'.__('L','wp-football')."</th>\n";
			$temp .= '      <th class="o">'.__('GF','wp-football')."</th>\n";
			$temp .= '      <th class="o">'.__('GA','wp-football')."</th>\n";
			$temp .= '      <th class="o">'.__('GD','wp-football')."</th>\n";
			$temp .= "   </tr>\n";
			$temp .= ' </thead>';
			$temp .= ' <tbody>';
		} 
		$count++;
		if ($count % 2 == 0) $class_t = ' class="even"';
		else $class_t = ' class="odd"';
		$icon = $team->fb_team_symbol;
		$temp .= '   <tr'.$class_t.'>'."\n";
		$temp .= '      <td class="o"><input type="text" name="class['.$team->fb_team_id.']" size="3" value="'.$team->fb_team_class.'" /></td>'."\n";  
		$temp .= '      <td class="t">'.($icon ? '<img src="'.get_option('home')."/".$dir_icons.$team->fb_team_symbol.'" alt="'.$team->fb_team_name.'" style="border:1px solid #8a8a8a;" />&nbsp;&nbsp;' : '&nbsp;').'<a href="'.$team->fb_team_link_info.'" title="'.$team->fb_team_name.'">'.$team->fb_team_name.'</a></td>'."\n";
		$temp .= '      <td class="o"><input type="text" name="pts['.$team->fb_team_id.']" size="3" value="'.$team->fb_team_pts.'" /></td>'."\n";  
		$temp .= '      <td class="o"><input type="text" name="played['.$team->fb_team_id.']" size="3" value="'.$team->fb_team_played.'" /></td>'."\n";  
		$temp .= '      <td class="o"><input type="text" name="won['.$team->fb_team_id.']" size="3" value="'.$team->fb_team_won.'" /></td>'."\n";  
		$temp .= '      <td class="o"><input type="text" name="draw['.$team->fb_team_id.']" size="3" value="'.$team->fb_team_draw.'" /></td>'."\n";  
		$temp .= '      <td class="o"><input type="text" name="loss['.$team->fb_team_id.']" size="3" value="'.$team->fb_team_loss.'" /></td>'."\n";  
		$temp .= '      <td class="o"><input type="text" name="gf['.$team->fb_team_id.']" size="3" value="'.$team->fb_team_gf.'" /></td>'."\n";  
		$temp .= '      <td class="o"><input type="text" name="ga['.$team->fb_team_id.']" size="3" value="'.$team->fb_team_ga.'" /></td>'."\n";  
		$temp .= '      <td class="o">'.($team->fb_team_gf-$team->fb_team_ga).'</td>'."\n";  
		$temp .= "   </tr>\n";
	}
	$temp .= " </tbody>  \n</table>\n<br />";
	$temp .= '  <input type="hidden" name="league" value="'.$league.'" />';
	$temp .= '  <input type="hidden" name="group" value="'.$group.'" />';
	$temp .= '  <input type="submit" name="submit" value="'.__('Change','wp-football').'" />';
	$temp .= "</form>";
	$temp .= $message;
	echo $temp;
?>
</body>
</html>