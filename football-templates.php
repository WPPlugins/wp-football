<?php
add_action('admin_enqueue_scripts', 'football_stylesheets_admin');
$complete_message =  '<div id="message" class="updated fade"><p>';
$complete_message_error =  '<div id="message" class="error"><p>';
$save_message = '<strong>'.__('Template save successfully.','wp-football').'</strong>'; 
$error_message = '<strong>'.__('Error!','wp-football').'</strong><br>';
$delete_message = '<strong>'.__('Template(s) deleted successfully.','wp-football').'</strong>';
$update_message = '<strong>'.__('Template changed successfully.','wp-football').'</strong>';
$path = admin_url() . 'admin.php';

if (!empty($_POST['action'])) $_GET['action'] = $_POST['action'];
if (!$_GET['action'] || !isset($_GET['action'])) {
	$_GET['action'] = 'edit';
	$_GET['new'] = 1;
}

$id_template = (empty($_GET['id_template'])) ? $_POST['id_template'] : $_GET['id_template'];
$templates = $wpdb->get_results("SELECT * FROM {$table_prefix}fb_template ORDER BY fb_template_id");
$template= $wpdb->get_row("SELECT * FROM {$table_prefix}fb_template WHERE fb_template_id = '$id_template'");

if(!empty($_GET['action'])) {
	// Decide What To Do
	switch($_GET['action']) {			
		case 'delete':	
			$message = true;
			$complete_message .= $delete_message;
			if (isset($_POST) AND is_array($_POST)) {
				foreach ($_POST as $n => $v) {
					if (substr($n,0,4) == "del_" AND $v == 'on') {
						$id_template = substr($n,4);
						$wpdb->query("DELETE FROM {$table_prefix}fb_template WHERE fb_template_id = '$id_template'");
					}
				}
			}
						
		case 'save':
			if ($_GET['action'] != 'delete') {
				$erro = '';
				$message = true;
				$id_template = $_POST['id_template'];
				$name = $_POST['name'];
				$category = $_POST['category'];
				$program = $_POST['program'];
				$fields_c = serialize($_POST['fc']);
				$fields_m = serialize($_POST['fm']);
				if ($name == '') $erro .= '<strong>'.__("Name field must be filled","wp-football").'</strong><br />'; 
				if ($category == '') $erro .= '<strong>'.__("Category field must be filled","wp-football").'</strong><br />'; 
				if ($program == '' && $category != 1) $erro .= '<strong>'.__("Program field must be filled","wp-football").'</strong><br />'; 
			
				if ($erro == '') {
					if (!empty($_POST['update'])) {
						$id_template = $_GET['id_template'];
						$wpdb->query("UPDATE {$table_prefix}fb_template SET fb_template_name = '$name', fb_template_category = '$category', fb_template_program = '$program', fb_template_fields_c = '$fields_c', fb_template_fields_m = '$fields_m' WHERE fb_template_id = '$id_template'");
						$success = true;
						$success_message = $update_message;
					}
					else {
						$wpdb->query("INSERT INTO {$table_prefix}fb_template VALUES (0, '$name', '$category', '$program','$fields_c', '$fields_m')");
						$success = true;
						$success_message = $save_message;
					}
				}
				else {
					$error_message = $erro;
					$error = true;
				}	
			}			
			$_GET["new"] = 1;	
			
		case 'edit':
			if ($_GET["new"] == false) {
				$id_template = $_GET['id_template'];
				$name = $template->fb_template_name;
				$category = $template->fb_template_category;
				$program = $template->fb_template_program;
				$fc = unserialize($template->fb_template_fields_c);
				$fm = unserialize($template->fb_template_fields_m);
				$title = __("Edit Template","wp-football");
			}
			else {
				$title = __("New Template","wp-football");
				$name = '';
				$category = '';
				$program = '';
				$fields = '';
			}
?>		
		<div class="wrap">	
			<div class="icon-wp-football icon32">
			<br/>
			</div>
			<h2><?php _e('Templates','wp-football') ?></h2>
			<div class="tablenav">
				<div class="alignleft">
					<a class="button-highlighted action_buttons" href="<?php echo $path; ?>?page=wp-football/football-manager.php">&laquo; <?php _e('Back to Manage League','wp-football') ?></a>
					<br class="clear" />
				</div>
			</div>
			<form name="blogform" method="post" action="<?php echo $path; ?>?page=wp-football/football-templates.php&amp;action=save&amp;id_template=<?php echo $_GET['id_template'] ?>&amp;paged=<?php echo $_GET['paged'] ?>"> 
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
							<th scope="row"><?php _e('Category','wp-football'); ?> *</th>
							<td>
								<select name="category" id="category_template_select" tabindex="2" <?php if($id_template < 5 && $_GET["new"] == false) echo 'disabled="disabled"'; ?>>
									<option value='0'><?php _e('--- Select ---','wp-football') ?></option> 
									<option value="1" <?php if ($category == 1) echo 'selected="selected"'; ?>><?php _e('Widget','wp-football'); ?></option>
									<option value="2" <?php if ($category == 2) echo 'selected="selected"'; ?>><?php _e('Page/Post','wp-football'); ?></option>
								</select> 
							</td>
						</tr>	
						<tr valign="top">
							<th scope="row"><?php _e('Program','wp-football'); ?> *</th>
							<td>
								<input type="text" name="program" id="program" value="<?php echo $program ?>" size="70" tabindex="3" <?php if($id_template < 5 && $_GET["new"] == false) echo 'disabled="disabled"'; ?> />
							</td>
						</tr>
						<tr valign="top">
							<th scope="row"><?php _e('Classification: Select fields to be displayed','wp-football') ?></th>
							<td>
							   	<table class="d_fields">
							    	<tr>  
										<td><span class="fields"><input name="fc[0]" type="checkbox" value="fb_team_name" <?php  if ($fc[0] == 'fb_team_name') echo 'checked="checked"'; ?> /> <?php _e('Team name','wp-football'); ?></span></td>
										<td><span class="fields"><input name="fc[1]" type="checkbox" value="fb_team_name_abb" <?php  if ($fc[1] == 'fb_team_name_abb') echo 'checked="checked"'; ?> /> <?php _e('Team name abbreviated','wp-football'); ?></span></td>
										<td><span class="fields"><input name="fc[2]" type="checkbox" value="fb_team_symbol" <?php  if ($fc[2] == 'fb_team_symbol') echo 'checked="checked"'; ?> /> <?php _e('Icon','wp-football'); ?></span></td>
									</tr>
									<tr>	
										<td><span class="fields"><input name="fc[3]" type="checkbox" value="fb_team_played" <?php  if ($fc[3] == 'fb_team_played') echo 'checked="checked"'; ?> /> <?php _e('Played (Pld)','wp-football'); ?></span></td>
										<td><span class="fields"><input name="fc[4]" type="checkbox" value="fb_team_won" <?php  if ($fc[4] == 'fb_team_won') echo 'checked="checked"'; ?> /> <?php _e('Won (W)','wp-football'); ?></span></td>
										<td><span class="fields"><input name="fc[5]" type="checkbox" value="fb_team_draw" <?php  if ($fc[5] == 'fb_team_draw') echo 'checked="checked"'; ?> /> <?php _e('Draw (D)','wp-football'); ?></span></td>
									</tr>
									<tr>	
										<td><span class="fields"><input name="fc[6]" type="checkbox" value="fb_team_loss" <?php  if ($fc[6] == 'fb_team_loss') echo 'checked="checked"'; ?> /> <?php _e('Loss (L)','wp-football'); ?></span></td>
										<td><span class="fields"><input name="fc[7]" type="checkbox" value="fb_team_gf" <?php  if ($fc[7] == 'fb_team_gf') echo 'checked="checked"'; ?> /> <?php _e('Goals For (GF)','wp-football'); ?></span></td>
										<td><span class="fields"><input name="fc[8]" type="checkbox" value="fb_team_ga" <?php  if ($fc[8] == 'fb_team_ga') echo 'checked="checked"'; ?> /> <?php _e('Goals Against (GA)','wp-football'); ?></span></td>
									</tr>
									<tr>
										<td><span class="fields"><input name="fc[9]" type="checkbox" value="fb_team_pts" <?php  if ($fc[9] == 'fb_team_pts') echo 'checked="checked"'; ?> /> <?php _e('Points (Pts)','wp-football'); ?></span></td>
										<td><span class="fields"><input name="fc[10]" type="checkbox" value="fb_team_class" <?php  if ($fc[10] == 'fb_team_class') echo 'checked="checked"'; ?> /> <?php _e('Classification (Clas)','wp-football'); ?></span></td>
										<td>&nbsp;</td>
									</tr>
								</table>		
							</td>
						</tr>
						<tr valign="top">
							<th scope="row"><?php _e('Matches: Select fields to be displayed','wp-football') ?></th>
							<td>
							   	<table class="d_fields">
							    	<tr>  
										<td><span class="fields"><input name="fm[0]" type="checkbox" value="fb_match_number" <?php  if ($fm[0] == 'fb_match_number') echo 'checked="checked"'; ?> /> <?php _e('Match Number','wp-football'); ?></span></td>
										<td><span class="fields"><input name="fm[1]" type="checkbox" value="fb_team_name" <?php  if ($fm[1] == 'fb_team_name') echo 'checked="checked"'; ?> /> <?php _e('Team name','wp-football'); ?></span></td>
										<td><span class="fields"><input name="fm[2]" type="checkbox" value="fb_team_name_abb" <?php  if ($fm[2] == 'fb_team_name_abb') echo 'checked="checked"'; ?> /> <?php _e('Team name abbreviated','wp-football'); ?></span></td>
									</tr>	
									<tr>
										<td><span class="fields"><input name="fm[3]" type="checkbox" value="fb_team_symbol" <?php  if ($fm[3] == 'fb_team_symbol') echo 'checked="checked"'; ?> /> <?php _e('Icon','wp-football'); ?></span></td>
										<td><span class="fields"><input name="fm[4]" type="checkbox" value="fb_match_city" <?php  if ($fm[4] == 'fb_match_city') echo 'checked="checked"'; ?> /> <?php _e('City','wp-football'); ?></span></td>
										<td><span class="fields"><input name="fm[5]" type="checkbox" value="fb_match_stadium" <?php  if ($fm[5] == 'fb_match_stadium') echo 'checked="checked"'; ?> /> <?php _e('Stadium','wp-football'); ?></span></td>
									</tr>
								</table>		
							</td>
						</tr>
					</table>
					<br />
				</div>
					<?php
					if ($_GET["new"] == true) { ?>
						<span class="submit"><input type="submit" name="submit" class="button-primary" tabindex="10" value="<?php _e('Save','wp-football') ?> &raquo;" /></span>
					<?php } else { ?>
						<span class="submit"><input type="submit" class="button-primary" name="submit" tabindex="10" value="<?php _e('Change','wp-football') ?> &raquo;" /></span>
						<input type="hidden" name="update" value="1" />
					<?php }
						if ($id_template < 5 && $_GET["new"] == false) { ?>
						<input type="hidden" name="category" value="<?php echo $category; ?>" />
						<input type="hidden" name="program" value="<?php echo $program; ?>" />
					<?php
						}	
					?>
						<input type="hidden" name="id_template" id="id_template" value="<?php echo $id_template; ?>" />
			</form>
		</div>	
		<hr />
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
	if ( !isset( $_GET['paged'] ) || $_GET['paged'] < 1 )
		$_GET['paged'] = 1;
	
	$start = ( $_GET['paged'] - 1 ) * 15;
	$limit = 15;
	$limit_by  = 'LIMIT ' . intval($start) . ',' . intval($limit);
	$templates = $wpdb->get_results("SELECT SQL_CALC_FOUND_ROWS * FROM {$table_prefix}fb_template ORDER BY fb_template_id {$limit_by}");
	$paged['total_templates'] = intval ( $wpdb->get_var( "SELECT FOUND_ROWS()" ) );
	$paged['templates_per_page'] = 15;
	$paged['max_templates_per_page'] = ( $limit > 0 ) ? ceil( $paged['total_templates'] / intval($limit)) : 1;
	
	$page_links = paginate_links( array(
		'base' => add_query_arg( 'paged', '%#%', $path."?page=wp-football/football-templates.php&amp;action=edit&amp;new=1" ),
		'format' => '',
		'prev_text' => __('&laquo;'),
		'next_text' => __('&raquo;'),
		'total' => $paged['max_templates_per_page'],
		'current' => $_GET['paged']
	));
//if (!isset($_GET['action']) || $_GET['action'] != 'edit') {
?>
	<div class="wrap">	
		<div class="icon-wp-football icon32">
		<br/>
		</div>
		<h2><?php _e('List of Templates','wp-football') ?></h2>
		<form id="manage_templates_form" method="post" action="<?php echo $path ?>?page=wp-football/football-templates.php&amp;action=delete&amp;id_template=<?php echo $id_template; ?>" onsubmit="javascript:check=confirm( '<?php _e('Excluding these Templates ?', 'wp-football'); ?>');if(check==false) return false;"> 
			<div class="tablenav">
				<div class="alignleft">
					<input type="submit" value="<?php _e('Delete','wp-football') ?>" name="delete" class="button-secondary delete action_buttons" />
				</div>
			<?php if ( $page_links ) : ?>
				<div class="tablenav-pages"><?php $page_links_text = sprintf( '<span class="displaying-num">' . __( 'Displaying %s&#8211;%s of %s', 'wp-football' ) . '</span>%s',
					number_format_i18n( ( $_GET['paged'] - 1 ) * $paged['templates_per_page'] + 1 ),
					number_format_i18n( min( $_GET['paged'] * $paged['templates_per_page'], $paged['total_templates'] ) ),
					number_format_i18n( $paged['total_templates'] ),
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
						<th scope="col"><?php _e('Category','wp-football') ?></th>
						<th scope="col"><?php _e('Program','wp-football') ?></th>
						<th scope="col"><?php _e('Classification Fields','wp-football') ?></th>
						<th scope="col"><?php _e('Matches Fields','wp-football') ?></th>
						<th scope="col"><?php _e('Actions','wp-football') ?></th>
					</tr>	
				</thead>	
				<tbody>
				<?php
				$i = 1;
				foreach ( $templates as $template ) {
					if ($i%2) { 
						echo '<tr class="alternate">';
					} else {
						echo '<tr>';
					}
					$preview = '';
					if ($template->fb_template_program) {
						$templ = explode('.',$template->fb_template_program);
						$templ = $templ[0].'_preview.'.$templ[1];
						$preview =  '| <a href="'.plugins_url('wp-football/templates/'.$templ).'?league=1&amp;iframe=true&amp;width=90%&amp;height=90%" rel="prettyPhoto[G'.$template->fb_template_id.']">'.__('Preview','wp-football');
					}	
					if ($id_template < 5) $d_del = ' disabled="disabled"';
					else $d_del = '';					
					echo '
						  <th scope="row" class="check-column"><input type="checkbox" name="del_'.$template->fb_template_id.'"'.$d_del.' /></th>
						  <td>'.$template->fb_template_id.'</td>
						  <td>'.$template->fb_template_name.'</td>
						  <td>'.($template->fb_template_category == 1 ? 'Widget' : 'Page/Post').'</td>
						  <td>'.$template->fb_template_program.'</td>
						  <td>'.substr($template->fb_template_fields_c,0,30).' ...</td>
						  <td>'.substr($template->fb_template_fields_m,0,30).' ...</td>
						  <td><a href="'.$path.'?page=wp-football/football-templates.php&amp;action=edit&amp;new=0&amp;id_template='.$template->fb_template_id.'&amp;paged='.$_GET['paged'].'">'.__("Edit").'</a>'.$preview.'</a></td>
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
