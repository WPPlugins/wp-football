<?php
	if (!function_exists('add_action')) {
		$wp_root = '../../..';
		if (file_exists($wp_root.'/wp-load.php')) {
			require_once($wp_root.'/wp-load.php');
		} else {
			require_once($wp_root.'/wp-config.php');
		}
	}
	$path = admin_url() . 'admin.php';
	if (!$id_league) $id_league = $_GET['id'];
	if ( ! isset( $_GET['paged'] ) || $_GET['paged'] < 1 )	$_GET['paged'] = 1;
	
	$start = ( $_GET['paged'] - 1 ) * 15;
	$limit = 15;
	$limit_by  = 'LIMIT ' . intval($start) . ',' . intval($limit);
	$teams = $wpdb->get_results("SELECT SQL_CALC_FOUND_ROWS * FROM {$table_prefix}fb_team WHERE fb_team_id_league = '$id_league' ORDER BY fb_team_id_group, fb_team_name {$limit_by}");
	$paged['total_teams'] = intval ( $wpdb->get_var( "SELECT FOUND_ROWS()" ) );
	$paged['teams_per_page'] = 15;
	$paged['max_teams_per_page'] = ( $limit > 0 ) ? ceil( $paged['total_teams'] / intval($limit)) : 1;
	
	$page_links = paginate_links( array(
		'base' => add_query_arg( 'paged', '%#%', $path."?page=wp-football/football-teams.php&amp;action=edit&amp;new=1&amp;id_league=".$id_league ),
		'format' => '',
		'prev_text' => __('&laquo;'),
		'next_text' => __('&raquo;'),
		'total' => $paged['max_teams_per_page'],
		'current' => $_GET['paged']
	));
?>
	<div class="wrap">	
		<div class="icon-wp-football icon32">
		<br/>
		</div>
		<h2><?php _e('List of Teams','wp-football') ?></h2>
		<form id="manage_eventos_form" method="post" action="<?php echo $path ?>?page=wp-football/football-teams.php&amp;action=delete" onsubmit="javascript:check=confirm( '<?php _e('Excluding these Teams ?', 'wp-football'); ?>');if(check==false) return false;"> 
			<div class="tablenav">
				<div class="alignleft">
					<input type="submit" value="<?php _e('Delete','wp-football') ?>" name="delete" class="button-secondary delete action_buttons" />
				</div>
			<?php if ( $page_links ) : ?>
				<div class="tablenav-pages"><?php $page_links_text = sprintf( '<span class="displaying-num">' . __( 'Displaying %s&#8211;%s of %s', 'wp-football' ) . '</span>%s',
					number_format_i18n( ( $_GET['paged'] - 1 ) * $paged['teams_per_page'] + 1 ),
					number_format_i18n( min( $_GET['paged'] * $paged['teams_per_page'], $paged['total_teams'] ) ),
					number_format_i18n( $paged['total_teams'] ),
					$page_links
				); echo $page_links_text; ?></div>
			<?php endif; ?>
			</div>
			<table class="widefat"> 
				<thead>
					<tr> 
						<th scope="col" class="check-column"><input type="checkbox" onclick="toggle_check(this)" /></th> 
						<th scope="col"><?php _e('Name','wp-football') ?></th>
						<th scope="col"><?php _e('Name Abbreviated','wp-football') ?></th>
						<th scope="col"><?php _e('Group','wp-football') ?></th>
						<th scope="col"><?php _e('Symbol','wp-football') ?></th>
						<th scope="col"><?php _e('Link Information','wp-football') ?></th>
						<th scope="col"><?php _e('Action','wp-football') ?></th>
					</tr>	
				</thead>	
				<tbody>
				<?php
				$i = 1;
				foreach ( $teams as $team ) {
					if ($i%2) { 
						echo '<tr class="alternate">';
					} else {
						echo '<tr>';
					}
					$editar_icon = plugins_url('wp-football/images/edit.gif');					
					$excluir_icon = plugins_url('wp-football/images/excluir_16.gif');
					$group_name = $wpdb->get_var("SELECT fb_group_name FROM {$table_prefix}fb_group WHERE fb_group_id = '$team->fb_team_id_group'");	
					echo '
						  <th scope="row" class="check-column"><input type="checkbox" name="del_'.$team->fb_team_id.'" /></th>
						  <td>'.$team->fb_team_name.'</td>
						  <td>'.$team->fb_team_name_abb.'</td>
						  <td>'.$group_name.'</td>
						  <td>'.$team->fb_team_symbol.'</td>
						  <td>'.$team->fb_team_link_info.'</td>
						  <td><a href="'.$path.'?page=wp-football/football-teams.php&amp;action=edit&amp;new=0&amp;id_team='.$team->fb_team_id.'&amp;id_league='.$team->fb_team_id_league.'&amp;paged='.$_GET['paged'].'"><img src="'.$editar_icon.'" alt="'.__("Change Team").'" title="'.__("Change Team").'" /></a></td>
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