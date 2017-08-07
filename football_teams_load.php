<?php
	if (!function_exists('add_action')) {
		$wp_root = '../../..';
		if (file_exists($wp_root.'/wp-load.php')) {
			require_once($wp_root.'/wp-load.php');
		} else {
			require_once($wp_root.'/wp-config.php');
		}
	}
	$id_group = $_GET['id'];
	$teams = $wpdb->get_results("SELECT * FROM {$table_prefix}fb_team WHERE fb_team_id_group = '$id_group' ORDER BY fb_team_id_group, fb_team_name");
	$retorno = '[ ';
	$retorno .= '{id: \'999\', nome: \''.__('Not Defined','wp-football').'\'} ';
	foreach ($teams as $team) {
	$retorno .= ', {id: \''.$team->fb_team_id.'\', nome: \''.$team->fb_team_name.'\'} ';
	}
	$retorno .= ' ]';
	echo $retorno;
?>
