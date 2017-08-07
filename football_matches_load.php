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
	$id_league = ($id_league ? $id_league : $_GET['id_league']);
	$id_phase = ($id_phase ? $id_phase : $_GET['id_phase']);
	$id_group = ($id_group ? $id_group : $_GET['id_group']);
	if ( ! isset( $_GET['paged'] ) || $_GET['paged'] < 1 )
		$_GET['paged'] = 1;
	
	$start = ( $_GET['paged'] - 1 ) * 15;
	$limit = 15;
	$limit_by  = 'LIMIT ' . intval($start) . ',' . intval($limit);
	$matches = $wpdb->get_results("SELECT SQL_CALC_FOUND_ROWS * FROM {$table_prefix}fb_match WHERE fb_match_id_league = '$id_league' AND fb_match_id_phase = '$id_phase' AND fb_match_id_group = '$id_group' ORDER BY fb_match_number {$limit_by}");
	$paged['total_matches'] = intval ( $wpdb->get_var( "SELECT FOUND_ROWS()" ) );
	$paged['matches_per_page'] = 15;
	$paged['max_matches_per_page'] = ( $limit > 0 ) ? ceil( $paged['total_matches'] / intval($limit)) : 1;
	
	$page_links = paginate_links( array(
		'base' => add_query_arg( 'paged', '%#%', $path."?page=wp-football/football-results.php&amp;action=edit&amp;new=1&amp;id_league=".$id_league ),
		'format' => '',
		'prev_text' => __('&laquo;'),
		'next_text' => __('&raquo;'),
		'total' => $paged['max_matches_per_page'],
		'current' => $_GET['paged']
	));
//if (!isset($_GET['action']) || $_GET['action'] != 'edit') {
?>
	<div class="wrap">	
		<div class="icon-wp-football icon32">
		<br/>
		</div>
		<h2><?php _e('List of Matches','wp-football') ?></h2>
		<form id="manage_eventos_form" method="post" action="<?php echo $path ?>?page=wp-football/football-matches.php&amp;action=delete&amp;id_league=<?php echo $id_league; ?>" onsubmit="javascript:check=confirm( '<?php _e('Excluding these Matches ?', 'wp-football'); ?>');if(check==false) return false;"> 
			<div class="tablenav">
				<div class="alignleft">
					<input type="submit" value="<?php _e('Delete','wp-football') ?>" name="delete" class="button-secondary delete action_buttons" />
				</div>
			<?php if ( $page_links ) : ?>
				<div class="tablenav-pages"><?php $page_links_text = sprintf( '<span class="displaying-num">' . __( 'Displaying %s&#8211;%s of %s', 'wp-football' ) . '</span>%s',
					number_format_i18n( ( $_GET['paged'] - 1 ) * $paged['matches_per_page'] + 1 ),
					number_format_i18n( min( $_GET['paged'] * $paged['matches_per_page'], $paged['total_matches'] ) ),
					number_format_i18n( $paged['total_matches'] ),
					$page_links
				); echo $page_links_text; ?></div>
			<?php endif; ?>
			</div>
			<table class="widefat"> 
				<thead>
					<tr> 
						<th scope="col" class="check-column"><input type="checkbox" onclick="toggle_check(this)" /></th> 
						<th scope="col"><?php _e('Match','wp-football') ?></th>
						<th scope="col"><?php _e('Phase','wp-football') ?></th>
						<th scope="col"><?php _e('Group','wp-football') ?></th>
						<th scope="col"><?php _e('Date','wp-football') ?></th>
						<th scope="col"><?php _e('Time','wp-football') ?></th>
						<th scope="col"><?php _e('Team 1','wp-football') ?></th>
						<th scope="col"><?php _e('Team 2','wp-football') ?></th>
						<th scope="col"><?php _e('Stadium','wp-football') ?></th>
						<th scope="col"><?php _e('Score 1','wp-football') ?></th>
						<th scope="col"><?php _e('Score 2','wp-football') ?></th>
						<th scope="col"><?php _e('Remark','wp-football') ?></th>
						<th scope="col"><?php _e('Action','wp-football') ?></th>
					</tr>	
				</thead>	
				<tbody>
				<?php
				$i = 1;
				foreach ( $matches as $match ) {
					if ($i%2) { 
						echo '<tr class="alternate">';
					} else {
						echo '<tr>';
					}
					$editar_icon = plugins_url('wp-football/images/edit.gif');					
					$excluir_icon = plugins_url('wp-football/images/excluir_16.gif');
					$phase_name = $wpdb->get_var("SELECT fb_phase_name FROM {$table_prefix}fb_phase WHERE fb_phase_id = '$match->fb_match_id_phase'");	
					$group_name = $wpdb->get_var("SELECT fb_group_name FROM {$table_prefix}fb_group WHERE fb_group_id = '$match->fb_match_id_group'");	
					$team1_name = $wpdb->get_var("SELECT fb_team_name FROM {$table_prefix}fb_team WHERE fb_team_id = '$match->fb_match_team1'");	
					$team2_name = $wpdb->get_var("SELECT fb_team_name FROM {$table_prefix}fb_team WHERE fb_team_id = '$match->fb_match_team2'");	
					$date_match = $match->fb_match_day.'/'.$match->fb_match_month."/".$match->fb_match_year;
					echo '
						  <th scope="row" class="check-column"><input type="checkbox" name="del_'.$team->fb_team_id.'" /></th>
						  <td>'.$match->fb_match_number.'</td>
						  <td>'.$phase_name.'</td>
						  <td>'.$group_name.'</td>
						  <td>'.$date_match.'</td>
						  <td>'.$match->fb_match_time.'</td>
						  <td>'.$team1_name.'</td>
						  <td>'.$team2_name.'</td>
						  <td>'.$match->fb_match_stadium.'</td>
						  <td class="aligncenter">'.$match->fb_match_score1.'</td>
						  <td class="aligncenter">'.$match->fb_match_score2.'</td>
						  <td>'.$match->fb_match_remark.'</td>
						  <td><a href="'.$path.'?page=wp-football/football-results.php&amp;action=edit&amp;new=0&amp;id_match='.$match->fb_match_id.'&amp;id_league='.$match->fb_match_id_league.'&amp;paged='.$_GET['paged'].'"><img src="'.$editar_icon.'" alt="'.__("Change Team").'" title="'.__("Change Team").'" /></a></td>
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
