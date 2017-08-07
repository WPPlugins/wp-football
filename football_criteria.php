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
	global $wpdb, $table_prefix;
	$league_name = $wpdb->get_var("SELECT fb_league_name FROM {$table_prefix}fb_league WHERE fb_league_id = '$league'");

	$opc = get_option('wpfootball_criteria');
	$op = $opc[$league];

	if ($_POST['submit']) {
		$message = '';
		$league = $_POST['league'];
		$pw = $_POST['pw'];
		$pd = $_POST['pd'];
		$nw = $_POST['nw'];
		$ogd = $_POST['ogd'];
		$ogf= $_POST['ogf'];
		$opc[$league] = array('pw' => $pw, 'pd' => $pd, 'nw' => $nw, 'ogd' => $ogd, 'ogf' => $ogf);
		update_option("wpfootball_criteria", $opc);
		$opc = get_option('wpfootball_criteria');
	    $op = $opc[$league];
		$message =  '<div id="message" class="updated fade"><p>'.'<strong>'.__('Classification criteria changed successfully.','wp-football').'</strong></p></div>'; 
	} 
	$temp = '';
	$temp .= '<form name="criteria" action="" method="post">';
	$temp .= "\n".'<table class="wcup">'."\n";
	$temp .= ' <caption>'.__('Classification Criteria - League: ','wp-football').$league_name.'</caption>'."\n";
	$temp .= ' <thead>';
	$temp .= "   <tr>\n";
	$temp .= '      <th class="ttitle" colspan="2">'.__('Settings Points','wp-football')."</th>\n";
	$temp .= "   </tr>\n";
	$temp .= ' </thead>'."\n";
	$temp .= ' <tbody>'."\n";
	$temp .= '   <tr valign="top">'."\n";
	$temp .= '      <th class="cl">'.__('Points for win','wp-football').'</th>'."\n";
	$temp .= '      <td class="tleft ci"><input type="text" name="pw" tabindex="1" size="5" value="'.$op['pw'].'" /></td>'."\n";
	$temp .= '   </tr>'."\n";
	$temp .= '   <tr valign="top">'."\n";
	$temp .= '      <th class="cl">'.__('Points for draw','wp-football').'</th>'."\n";
	$temp .= '      <td class="ci"><input type="text" name="pd" tabindex="2" size="5" value="'.$op['pd'].'" /></td>'."\n";
	$temp .= '   </tr>'."\n";
	$temp .= "   <tr>\n";
	$temp .= '      <th class="ttitle" colspan="2">'.__('Order of tie breaker criteria ','wp-football')."</th>\n";
	$temp .= "   </tr>\n";
	$temp .= "   <tr>\n";
	$temp .= '      <td colspan="2"><strong>'.__('Do not fill the order field, if the criterion is not used in the league','wp-football')."</strong></td>\n";
	$temp .= "   </tr>\n";
	$temp .= '   <tr valign="top">'."\n";
	$temp .= '      <th class="cl">'.__('Number of wins','wp-football').'</th>'."\n";
	$temp .= '      <td class="ci"><input type="text" name="nw" tabindex="3" size="5" value="'.$op['nw'].'" /></td>'."\n";
	$temp .= '   </tr>'."\n";
	$temp .= '   <tr valign="top">'."\n";
	$temp .= '      <th class="cl">'.__('Goals difference','wp-football').'</th>'."\n";
	$temp .= '      <td class="ci"><input type="text" name="ogd" tabindex="3" size="5" value="'.$op['ogd'].'" /></td>'."\n";
	$temp .= '   </tr>'."\n";
	$temp .= "   <tr>\n";
	$temp .= '   <tr valign="top">'."\n";
	$temp .= '      <th class="cl">'.__('Goals for','wp-football').'</th>'."\n";
	$temp .= '      <td class="ci"><input type="text" name="ogf" tabindex="4" size="5" value="'.$op['ogf'].'" /></td>'."\n";
	$temp .= '   </tr>'."\n";
	$temp .= "   <tr>\n";
	$temp .= " </tbody>  \n</table>\n<br />";
	$temp .= '  <input type="hidden" name="league" value="'.$league.'" />';
	$temp .= '  <input type="submit" name="submit" value="'.__('Change','wp-football').'" />';
	$temp .= "</form>";
	$temp .= $message;
	echo $temp;
?>
</body>
</html>