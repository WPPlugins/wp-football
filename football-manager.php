<?php
$complete_message =  '<div id="message" class="updated fade"><p>';
$complete_message_error =  '<div id="message" class="error"><p>';
$save_message_league = '<strong>'.__('League save successfully.','wp-football').'</strong>'; 
$error_message_league = '<strong>'.__('Error!','wp-football').'</strong><br>';
$delete_message_league = '<strong>'.__('League deleted successfully.','wp-football').'</strong>';
$update_message_league = '<strong>'.__('League changed successfully.','wp-football').'</strong>';
$path = admin_url() . 'admin.php';

if (!empty($_POST['action'])) $_GET['action'] = $_POST['action'];

$id_league = (empty($_GET['id_league'])) ? $_POST['id_league'] : $_GET['id_league'];
$league = $wpdb->get_row("SELECT * FROM {$table_prefix}fb_league WHERE fb_league_id = '$id_league'");

if(!empty($_GET['action'])) {
	// Decide What To Do
	switch($_GET['action']) {			
		case 'delete':	
			$message = true;
			$id_league = $_GET['id_league'];
			$complete_message .= $delete_message_league;
			if (isset($_POST) AND is_array($_POST)) {
				foreach ($_POST as $n => $v) {
					if (substr($n,0,4) == "del_" AND $v == 'on') {
						$id_league = substr($n,4);
						// deletar demais arquivos ou active = 0 ?
						$wpdb->query("DELETE FROM {$table_prefix}fb_league WHERE fb_league_id = '$id_league'");
					}
				}
			}
			break;
			
		case 'save':
			$erro = '';
			$message = true;
			$name = $_POST['name'];
			$description = $_POST['description'];
			$continent = $_POST['continent'];
			$country = $_POST['country'];
			$timezone = $_POST['timezone'];
			$season = $_POST['season'];
			$dir_icons = $_POST['dir_icons'];
			if (substr($dir_icons, -1) != '/') $dir_icons = $dir_icons . '/';
			$active = $_POST['active'];
			if ($name == '') $erro .= '<strong>'.__("Name field must be filled","wp-football").'</strong><br />'; 
			if ($country == '') $erro .= '<strong>'.__("Country field must be filled","wp-football").'</strong><br />'; 
			if ($season == '') $erro .= '<strong>'.__("Season field must be filled","wp-football").'</strong><br />'; 
			
			if ($erro == '') {
				if (!empty($_POST['update'])) {
					$id_league = $_GET['id_league'];
					$wpdb->query("UPDATE {$table_prefix}fb_league SET fb_league_name = '$name', fb_league_description = '$description', fb_league_continent = '$continent', fb_league_country = '$country', fb_league_timezone = '$timezone', fb_league_season = '$season', fb_league_dir_icons = '$dir_icons', fb_league_active = '$active'  WHERE fb_league_id = '$id_league'");
					$success = true;
					$success_message = $update_message_league;
				}
				else {
					$wpdb->query("INSERT INTO {$table_prefix}fb_league VALUES (0,'$name','$description','$continent','$country', '$timezone', '$season', '$dir_icons', '$active')");
					$success = true;
					$success_message = $save_message_league;
					$new_league = mysql_insert_id();
				}
			}
			else {
				$error_message = $erro;
				$error = true;
			}			
			$_GET["new"] = 0;
			
		case 'edit':
			if ($_GET["new"] == false) {
				$id_league = ($_GET['id_league'] ? $_GET['id_league'] : $new_league);
				$league = $wpdb->get_row("SELECT * FROM {$table_prefix}fb_league WHERE fb_league_id = '$id_league'");
				$name = $league->fb_league_name;
				$description = $league->fb_league_description;
				$continent = $league->fb_league_continent;
				$country = $league->fb_league_country;
				$timezone = $league->fb_league_timezone;
				$season = $league->fb_league_season;
				$dir_icons = $league->fb_league_dir_icons;
				$active = $league->fb_league_active;
				$title = __("Edit League","wp-football");
			}
			else {
				$title = __("New League","wp-football");
			}
?>		
		<div class="wrap">	
			<div class="icon32 icon-wp-football">
			<br/>
			</div>
			<h2><?php _e('League','wp-football') ?></h2>
			<div class="tablenav">
				<div class="alignleft">
					<a class="button-highlighted action_buttons" href="<?php echo $path ?>?page=wp-football/football-manager.php">&laquo; <?php _e('Back to Manage League','wp-football') ?></a>
					<br class="clear" />
				</div>
			</div>
			<form name="blogform" method="post" action="<?php echo $path ?>?page=wp-football/football-manager.php&amp;action=save&amp;id_league=<?php echo $_GET['id_league'] ?>&amp;paged=<?php echo $_GET['paged'] ?>"> 
				<div class="stuffbox metabox-holder" style="padding-top:0;">
					<h3><?php echo $title ?></h3>
					<br class="clear" />

					<table class="form-table">
						<tr valign="top">
							<th scope="row"><?php _e('Name','wp-football'); ?> *</th>
							<td>
								<input type="text" name="name" id="name" value="<?php echo $name ?>" size="70" tabindex="1" />
							</td>
						</tr>
						<tr valign="top">
							<th scope="row"><?php _e('Description','wp-football') ?></th>
							<td>
								<textarea cols="57" rows="5" name="description" tabindex="2"><?php echo $description ?></textarea>
							</td>
						</tr>
						<tr valign="top">
							<th scope="row"><?php _e('Continent','wp-football') ?></th>
							<td>
								<input name="continent" type="text" value="<?php echo $continent ?>" size="70" tabindex="3" />
							</td>
						</tr>
						<tr valign="top">
							<th scope="row"><?php _e('Country','wp-football') ?></th>
							<td>
								<input name="country" type="text" value="<?php echo $country ?>" size="70" tabindex="4" /> 
							</td>
						</tr>
						<tr valign="top">
							<th scope="row"><?php _e('Matches Timezone','wp-football') ?></th>
							<td>
								<select name="timezone" id="gmt_offset" tabindex="5">
								<?php
								$current_offset = $timezone;
								$offset_range = array (-12, -11.5, -11, -10.5, -10, -9.5, -9, -8.5, -8, -7.5, -7, -6.5, -6, -5.5, -5, -4.5, -4, -3.5, -3, -2.5, -2, -1.5, -1, -0.5,
									0, 0.5, 1, 1.5, 2, 2.5, 3, 3.5, 4, 4.5, 5, 5.5, 5.75, 6, 6.5, 7, 7.5, 8, 8.5, 8.75, 9, 9.5, 10, 10.5, 11, 11.5, 12, 12.75, 13, 13.75, 14);
								foreach ( $offset_range as $offset ) {
									if ( 0 < $offset )
										$offset_name = '+' . $offset;
									elseif ( 0 == $offset )
										$offset_name = '';
									else
										$offset_name = (string) $offset;
								
									$offset_name = str_replace(array('.25','.5','.75'), array(':15',':30',':45'), $offset_name);
								
									$selected = '';
									if ( $current_offset == $offset ) {
										$selected = " selected='selected'";
										$current_offset_name = $offset_name;
									}
									echo "<option value=\"" . esc_attr($offset) . "\"$selected>" . sprintf(__('UTC %s'), $offset_name) . '</option>';
								}
								?>
								</select>
							</td>
						</tr>
						<tr valign="top">
							<th scope="row"><?php _e('Season','wp-football') ?></th>
							<td>
								<input name="season" type="text" value="<?php echo $season ?>" size="15" tabindex="6" />
							</td>
						</tr>
						<tr valign="top">
							<th scope="row"><?php _e('Icons Directory','wp-football') ?></th>
							<td>
								<input name="dir_icons" type="text" value="<?php echo $dir_icons ?>" size="70" tabindex="7" /><br /><small><?php _e('Attention: Except for the World Cup 2010 set the icons directory outside the directory of the plugin','wp-football'); ?>
							</td>
						</tr>
						<tr>
							<th scope="row"><?php _e('Active','wp-football') ?> *</th>
							<td>
								<label><input type="radio" tabindex="8" value="0" name="active" <?php if (!$active) echo "checked=\"checked\""; ?> /> <?php _e('No','wp-football') ?> &nbsp;&nbsp;</label>
								<label><input type="radio" value="1" name="active" <?php if ($active) echo "checked=\"checked\""; ?> /> <?php _e('Yes','wp-football') ?>  &nbsp;&nbsp;</label>
							</td>
						</tr>
					</table>
					<br />
				</div>
					<?php
					if ($_GET["new"] == true) { ?>
						<span class="submit"><input type="submit" name="submit" class="button-primary" tabindex="10" value="<?php _e('Save','wp-football') ?>" /></span>
					<?php } else { ?>
						<span class="submit"><input type="submit" class="button-primary" name="submit" tabindex="10" value="<?php _e('Change','wp-football') ?>" /></span> <span><a class="button-primary" href="<?php echo plugins_url('wp-football/football_criteria.php').'?league='.$id_league.'&amp;group=1&amp;iframe=true&amp;width=90%&amp;height=90%'; ?>" rel="prettyPhoto[]"><?php _e('Classification Criteria','wp-football'); ?></a></span>

						<input type="hidden" name="update" value="1" />
					<?php } ?>
						<input type="hidden" name="id_league" value="<?php echo $id_league; ?>" />
			</form>
					<br class="clear" />
		</div>	
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
//if (!isset($_GET['action']) || $_GET['action'] != 'edit') {	
	if ( ! isset( $_GET['paged'] ) || $_GET['paged'] < 1 )
		$_GET['paged'] = 1;
	
	$start = ( $_GET['paged'] - 1 ) * 10;
	$limit = 10;
	$limit_by  = 'LIMIT ' . intval($start) . ',' . intval($limit);
	$leagues = $wpdb->get_results("SELECT SQL_CALC_FOUND_ROWS * FROM {$table_prefix}fb_league ORDER BY fb_league_season, fb_league_name {$limit_by}");

	$paged['total_leagues'] = intval ( $wpdb->get_var( "SELECT FOUND_ROWS()" ) );
	$paged['leagues_per_page'] = 10;
	$paged['max_leagues_per_page'] = ( $limit > 0 ) ? ceil( $paged['total_leagues'] / intval($limit)) : 1;
	
	$page_links = paginate_links( array(
		'base' => add_query_arg( 'paged', '%#%', $_SERVER['PHP_SELF']."?page=wp-football/football-manager.php&amp;action=edit&amp;new=1&amp;id_league=".$id_league ),
		'format' => '',
		'prev_text' => __('&laquo;'),
		'next_text' => __('&raquo;'),
		'total' => $paged['max_leagues_per_page'],
		'current' => $_GET['paged']
	));
if (!isset($_GET['action'])) {
?>
	<div class="wrap">	
		<div class="icon32 icon-wp-football">
		<br/>
		</div>
		<h2><?php _e('Manage Leagues','wp-football') ?></h2>
		<form id="manage_eventos_form" method="post" action="<?php echo $path ?>?page=wp-football/football-manager.php&amp;action=delete&amp;id_league=<?php echo $id_league ?>" onsubmit="javascript:check=confirm( '<?php _e('Excluding these League ?', 'wp-football'); ?>');if(check==false) return false;"> 
			<div class="tablenav">
				<div class="alignleft">
					<input type="submit" value="<?php _e('Delete','wp-football') ?>" name="delete" class="button-secondary delete action_buttons" />
					<a class="button-highlighted action_buttons" href="<?php echo $path ?>?page=wp-football/football-manager.php&amp;action=edit&amp;new=1"><?php _e('Add new League','wp-football') ?></a>
				</div>
			<?php if ( $page_links ) : ?>
				<div class="tablenav-pages"><?php $page_links_text = sprintf( '<span class="displaying-num">' . __( 'Displaying %s&#8211;%s of %s', 'wp-football' ) . '</span>%s',
					number_format_i18n( ( $_GET['paged'] - 1 ) * $paged['leagues_per_page'] + 1 ),
					number_format_i18n( min( $_GET['paged'] * $paged['leagues_per_page'], $paged['total_leagues'] ) ),
					number_format_i18n( $paged['total_leagues'] ),
					$page_links
				); echo $page_links_text; ?></div>
			<?php endif; ?>
			</div>
			<table class="widefat"> 
				<thead>
					<tr> 
						<th scope="col" class="check-column"><input type="checkbox" onclick="toggle_check(this)" /></th> 
						<th scope="col"><?php _e('Id','wp-football') ?></th>
						<th scope="col"><?php _e('Name','wp-football') ?></th>
						<th scope="col"><?php _e('Continent','wp-football') ?></th>
						<th scope="col"><?php _e('Country','wp-football') ?></th>
						<th scope="col"><?php _e('Timezone','wp-football') ?></th>
						<th scope="col"><?php _e('Season','wp-football') ?></th>
						<th scope="col"><?php _e('Icons Directory','wp-football') ?></th>
						<th scope="col"><?php _e('Active','wp-football') ?></th>
					</tr>	
				</thead>	
				<tbody>
				<?php
				$i = 1;
				foreach ( $leagues as $league ) {
					if ($i%2) { 
						echo '<tr class="alternate">';
					} else {
						echo '<tr>';
					}
					$editar_icon = plugins_url('wp-football/images/edit.gif');					
					$excluir_icon = plugins_url('wp-football/images/excluir_16.gif');	
					$url_e = $path.'?page=wp-football/football-manager.php&amp;action=edit&amp;new=0&amp;id_league='.$league->fb_league_id.'">'.__('Edit','wp-football');
					$url_t = $path.'?page=wp-football/football-teams.php&amp;action=edit&amp;new=1&amp;id_league='.$league->fb_league_id.'">'.__('Teams','wp-football');
					$url_m = $path.'?page=wp-football/football-matches.php&amp;action=edit&amp;new=1&amp;id_league='.$league->fb_league_id.'">'.__('Matches','wp-football');
					$url_g = $path.'?page=wp-football/football-groups.php&amp;action=edit&amp;new=1&amp;id_league='.$league->fb_league_id.'">'.__('Groups','wp-football');
					$url_f = $path.'?page=wp-football/football-phases.php&amp;action=edit&amp;new=1&amp;id_league='.$league->fb_league_id.'">'.__('Phases','wp-football');
					$url_r = $path.'?page=wp-football/football-results.php&amp;action=edit&amp;new=1&amp;id_league='.$league->fb_league_id.'">'.__('Results','wp-football');
					$f_ativa = ($league->fb_league_active == '1') ? __('Yes','wp-football') : __('No','wp-football');
					echo '
						  <th scope="row" class="check-column"><input type="checkbox" name="del_'.$league->fb_league__id.'" /></th>
						  <td><a href="'.$path.'?page=wp-football/football-manager.php&amp;action=edit&amp;new=0&amp;id_league='.$league->fb_league_id.'">'.$league->fb_league_id.'</a></td>
						  <td>'.$league->fb_league_name.'<div class="row-actions"><span class="edit"><a href="'.$url_e.'</a> | </span><span class="edit"><a href="'.$url_f.'</a> | </span><span class="edit"><a href="'.$url_g.'</a> | </span><span class="edit"><a href="'.$url_t.'</a> | </span><span class="edit"><a href="'.$url_m.'</a> | </span><span class="edit"><a href="'.$url_r.'</a></span></div></td>
						  <td>'.$league->fb_league_continent.'</td>
						  <td>'.$league->fb_league_country.'</td>
						  <td>UTC '.$league->fb_league_timezone.'</td>
						  <td>'.$league->fb_league_season.'</td>
						  <td>'.$league->fb_league_dir_icons.'</td>
						  <td>'.$f_ativa.'</td>
					  </tr>';  
					  $i++;
				}
				?>
				</tbody>
			</table>	
		</form>
		<br class="clear" />
		<div class="tablenav">
		<?php
		if ( $page_links )
			echo "<div class='tablenav-pages'>$page_links_text</div>";
		?>
		</div>
	</div>
<?php
}
?>