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
	if ( !isset( $_GET['paged'] ) || $_GET['paged'] < 1 )
		$_GET['paged'] = 1;
	
	$start = ( $_GET['paged'] - 1 ) * 15;
	$limit = 15;
	$limit_by  = 'LIMIT ' . intval($start) . ',' . intval($limit);
	$phases = $wpdb->get_results("SELECT SQL_CALC_FOUND_ROWS * FROM {$table_prefix}fb_phase WHERE fb_phase_id_league = '$id_league' ORDER BY fb_phase_order {$limit_by}");
	$paged['total_phases'] = intval ( $wpdb->get_var( "SELECT FOUND_ROWS()" ) );
	$paged['phases_per_page'] = 15;
	$paged['max_phases_per_page'] = ( $limit > 0 ) ? ceil( $paged['total_phases'] / intval($limit)) : 1;
	
	$page_links = paginate_links( array(
		'base' => add_query_arg( 'paged', '%#%', $path."?page=wp-football/football-phases.php&amp;action=edit&amp;new=1" ),
		'format' => '',
		'prev_text' => __('&laquo;'),
		'next_text' => __('&raquo;'),
		'total' => $paged['max_phases_per_page'],
		'current' => $_GET['paged']
	));
//if (!isset($_GET['action']) || $_GET['action'] != 'edit') {
?>
	<div class="wrap">	
		<div class="icon-wp-football icon32">
		<br/>
		</div>
		<h2><?php _e('List of phases','wp-football') ?></h2>
		<form id="manage_phases_form" method="post" action="<?php echo $path ?>?page=wp-football/football-phases.php&amp;action=delete&amp;id_league=<?php echo $id_league; ?>" onSubmit="javascript:check=confirm( '<?php _e('Delete this(these) phase(s) and matches related ?', 'wp-football'); ?>');if(check==false) return false;"> 
			<div class="tablenav">
				<div class="alignleft">
					<input type="submit" value="<?php _e('Delete','wp-football') ?>" name="delete" class="button-secondary delete action_buttons" />
				</div>
			<?php if ( $page_links ) : ?>
				<div class="tablenav-pages"><?php $page_links_text = sprintf( '<span class="displaying-num">' . __( 'Displaying %s&#8211;%s of %s', 'wp-football' ) . '</span>%s',
					number_format_i18n( ( $_GET['paged'] - 1 ) * $paged['phases_per_page'] + 1 ),
					number_format_i18n( min( $_GET['paged'] * $paged['phases_per_page'], $paged['total_phases'] ) ),
					number_format_i18n( $paged['total_phases'] ),
					$page_links
				); echo $page_links_text; ?></div>
			<?php endif; ?>
			</div>
			<table class="widefat"> 
				<thead>
					<tr> 
						<th scope="col" class="check-column"><input type="checkbox" onClick="toggle_check(this)" /></th> 
						<th scope="col"><?php _e('Order','wp-football') ?></th>
						<th scope="col"><?php _e('Name','wp-football') ?></th>
						<th scope="col"><?php _e('Name Abbreviated','wp-football') ?></th>
						<th scope="col"><?php _e('Action','wp-football') ?></th>
					</tr>	
				</thead>	
				<tbody>
				<?php
				$i = 1;
				foreach ( $phases as $phase ) {
					if ($i%2) { 
						echo '<tr class="alternate">';
					} else {
						echo '<tr>';
					}
					$editar_icon = plugins_url('wp-football/images/edit.gif');					
					echo '
						  <th scope="row" class="check-column"><input type="checkbox" name="del_'.$phase->fb_phase_id.'" /></th>
						  <td>'.$phase->fb_phase_order.'</td>
						  <td>'.$phase->fb_phase_name.'</td>
						  <td>'.$phase->fb_phase_name_abb.'</td>
						  <td><a href="'.$path.'?page=wp-football/football-phases.php&amp;action=edit&amp;new=0&amp;id_league='.$phase->fb_phase_id_league.'&amp;id_phase='.$phase->fb_phase_id.'&amp;paged='.$_GET['paged'].'"><img src="'.$editar_icon.'" alt="'.__("Change Phase").'" title="'.__("Change Phase").'" /></a></td>
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
