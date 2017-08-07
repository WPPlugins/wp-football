<?php
/*
Plugin Name: wp-football
Plugin URI: http://www.blogviche.com.br/plugin-wp-football
Description: Registro e acompanhamento de campeonatos de futebol.
Author: Newton Horta
Version: 1.1
Author URI: http://www.blogviche.com.br
*/


/*  
	Copyright 2009  Newton Horta  (email : nghorta@gmail.com)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation; either version 2 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
*/

if (!function_exists('add_action')) {
	$wp_root = '../../..';
	if (file_exists($wp_root.'/wp-load.php')) {
		require_once($wp_root.'/wp-load.php');
	} else {
		require_once($wp_root.'/wp-config.php');
	}
}

### Football Tables Name
global $wpdb;
$wpdb->league = $wpdb->prefix.'fb_league';
$wpdb->team = $wpdb->prefix.'fb_team';
$wpdb->continent = $wpdb->prefix.'fb_continent';
$wpdb->phase = $wpdb->prefix.'fb_phase';
$wpdb->group = $wpdb->prefix.'fb_group';
$wpdb->match = $wpdb->prefix.'fb_match';
$wpdb->template = $wpdb->prefix.'fb_template';

require_once (dirname (__FILE__) . '/football-functions.php');

### Function: Football Administration Menu
add_action('admin_menu', 'football_menu');
function football_menu() {
	if (function_exists('add_menu_page')) {
		add_menu_page(__('Football', 'wp-football'), __('Football', 'wp-football'), 'manage_football', 'wp-football/football-manager.php', '', plugins_url('wp-football/images/wp-football_icon.png'));
	}
	if (function_exists('add_submenu_page')) {
		add_submenu_page('wp-football/football-manager.php', __('Manage League', 'wp-football'),  __('Manage League', 'wp-football'), 'manage_football', 'wp-football/football-manager.php');
		add_submenu_page('wp-football/football-manager.php', __('Groups', 'wp-football'), __('Groups', 'wp-football'),  'manage_football', 'wp-football/football-groups.php');
		add_submenu_page('wp-football/football-manager.php', __('Phases', 'wp-football'), __('Phases', 'wp-football'),  'manage_football', 'wp-football/football-phases.php');
		add_submenu_page('wp-football/football-manager.php', __('Teams', 'wp-football'),  __('Teams', 'wp-football'), 'manage_football', 'wp-football/football-teams.php');
		add_submenu_page('wp-football/football-manager.php', __('Matches', 'wp-football'), __('Matches', 'wp-football'),  'manage_football', 'wp-football/football-matches.php');
		add_submenu_page('wp-football/football-manager.php', __('Results', 'wp-football'), __('Results', 'wp-football'),  'manage_football', 'wp-football/football-results.php');
		add_submenu_page('wp-football/football-manager.php', __('Templates', 'wp-football'), __('Templates', 'wp-football'),  'manage_football', 'wp-football/football-templates.php');
		add_submenu_page('wp-football/football-manager.php', __('Update Teams Phase', 'wp-football'), __('Update Teams Phase', 'wp-football'),  'manage_football', 'wp-football/football-update_phase.php');
		add_submenu_page('wp-football/football-manager.php', __('Uninstall wp-football', 'wp-football'), __('Uninstall wp-football', 'wp-football'),  'manage_football', 'wp-football/football-uninstall.php');
	}
}


add_action('init', 'wpfootball_textdomain');
function wpfootball_textdomain() {
	load_plugin_textdomain('wp-football', false, dirname( plugin_basename(__FILE__) ) . '/langs');
}

function getLeagueTable($atts) {
    extract(shortcode_atts(array(
	"id_league" => '',
	"id_template" => ''
    ), $atts));
    return get_templateLeague($id_league, $id_template);
}
add_shortcode('wpfootball', 'getLeagueTable');

### Function: Enqueue Football Stylesheet In WP-Admin
add_action('admin_enqueue_scripts', 'football_scripts_admin');
function football_scripts_admin($hook_suffix) {
	$football_admin_pages = array('wp-football/football-manager.php', 'wp-football/football-teams.php',  'wp-football/football-groups.php','wp-football/football-matches.php','wp-football/football-phases.php','wp-football/football-templates.php','wp-football/football-results.php','wp-football/football-uninstall.php','wp-football/football-update_phase.php');
	if(in_array($hook_suffix, $football_admin_pages)) {
		wp_enqueue_style('wp-football-admin-css', plugins_url('wp-football/css/football-admin.css'), false, '1.0', 'all');
        wp_enqueue_style('wp-football-pretty-css', plugins_url('wp-football/css/prettyPhoto.css'), false, '1,0', 'all');
	    wp_enqueue_script('pretty', plugins_url('wp-football/js/jquery.prettyPhoto.js'), array('jquery'), '2.4.3');
	    wp_enqueue_script('wpfootball', plugins_url('wp-football/js/wp-football_admin.js'), array('jquery'), '1.0', true);
	}
}

add_action('wp_enqueue_scripts','wpfootball_addHeader');

function wpfootball_addHeader(){
	global $text_direction;
	if ($text_direction == 'rtl') {
		wp_enqueue_style('wp-football', plugins_url('wp-football/css/football-rtl.css'), false, '1.0', 'all');
	} else {
		wp_enqueue_style('wp-football', plugins_url('wp-football/css/football.css'), false, '1.0', 'all');
	}
	wp_enqueue_script('wpfootball', plugins_url('wp-football/js/wp-football.js'), array('jquery'), '1.0');
	wp_enqueue_script('jquery');
    wp_enqueue_script('tools', plugins_url('wp-football/js/jquery.tools.min.js'), array('jquery'), '1.1.0');
}

### Class: WP-Football Widget
 class WP_Widget_Football extends WP_Widget {
	// Constructor
	function WP_Widget_Football() {
		$widget_ops = array('description' => __('Championship Football', 'wp-football'));
		$this->WP_Widget('football', __('Football League', 'wp-football'), $widget_ops);
	}

	// Display Widget
	function widget($args, $instance) {
		extract($args);
		$title = apply_filters('widget_title', esc_attr($instance['title']));
		$league = esc_attr($instance['league']);
		$type = esc_attr($instance['type']);
		$phase = esc_attr($instance['phase']);
		$group = esc_attr($instance['group']);
		$template = esc_attr($instance['template']);

		echo $before_widget.$before_title.$title.$after_title;
		switch($type) {
			case 'teams':
				$teams = get_teams($league);
				echo $teams;
				break;
			case 'matches':
				$matches = get_matches($league,$phase,$group,$template);
				echo $matches;
				break;
			case 'groups':
				$groups = get_groups($league,$group,$template);
				echo $groups;
				break;
			case 'next':
				$matches = get_next_matches($league,$template);
				echo $matches;
				break;
		}
//		if ($type == 'teams' || type == 'groups') echo '<br style="clear:both;" />';
		echo $after_widget;
	}

	// When Widget Control Form Is Posted
	function update($new_instance, $old_instance) {
		if (!isset($new_instance['submit'])) {
			return false;
		}
		$instance = $old_instance;
		$instance['title'] = strip_tags($new_instance['title']);
		$instance['type'] = strip_tags($new_instance['type']);
		$instance['league'] = strip_tags($new_instance['league']);
		$instance['group'] = strip_tags($new_instance['group']);
		$instance['phase'] = strip_tags($new_instance['phase']);
		$instance['template'] = strip_tags($new_instance['template']);
		return $instance;
	}

	// DIsplay Widget Control Form
	function form($instance) {
		global $wpdb;
		$instance = wp_parse_args((array) $instance, array('title' => __('Football', 'wp-football'), 'league' => 'World Cup 2010', 'type' => 'teams'));
		$title = esc_attr($instance['title']);
		$league = esc_attr($instance['league']);
		$type = esc_attr($instance['type']);
		$group = esc_attr($instance['group']);
		$phase = esc_attr($instance['phase']);
		$template = esc_attr($instance['template']);
		$leagues = get_leagues();
		$groups = get_groups_league($league);
		$phases = get_phases_league($league);
		$templates = get_widget_templates();
?>
		<p>
			<label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title:', 'wp-football'); ?> <input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo $title; ?>" /></label>
		</p>
		<p>
			<label for="<?php echo $this->get_field_id('type'); ?>"><?php _e('Type:', 'wp-football'); ?>
				<select name="<?php echo $this->get_field_name('type'); ?>" id="<?php echo $this->get_field_id('type'); ?>" class="widefat">
					<option value="teams"<?php selected('teams', $type); ?>><?php _e('Teams', 'wp-football'); ?></option>
					<option value="groups"<?php selected('groups', $type); ?>><?php _e('Groups', 'wp-football'); ?></option>
					<option value="matches"<?php selected('matches', $type); ?>><?php _e('Matches', 'wp-football'); ?></option>
					<option value="next"<?php selected('next', $type); ?>><?php _e('Next Matches', 'wp-football'); ?></option>
				</select>
			</label>
		</p>
		<p class="league">
			<label for="<?php echo $this->get_field_id('league'); ?>"><?php _e('League:', 'wp-football'); ?>
				<select name="<?php echo $this->get_field_name('league'); ?>" id="<?php echo $this->get_field_id('league'); ?>" class="widefat" onchange="javascript:pop_selects(this.id, this.value)">
					<option value="0"><?php _e('--- Select ---','wp-football'); ?></option>
				<?php foreach ($leagues as $lg) { ?>
					<option value="<?php echo $lg->fb_league_id; ?>"<?php selected($lg->fb_league_id, $league); ?>><?php echo $lg->fb_league_name; ?></option>
				<?php } ?>	
				</select>
			</label>
		</p>
		<p>
			<label for="<?php echo $this->get_field_id('phase'); ?>"><?php _e('Phase:', 'wp-football'); ?>
				<select name="<?php echo $this->get_field_name('phase'); ?>" id="<?php echo $this->get_field_id('phase'); ?>" class="widefat">
				<?php foreach ($phases as $ph) { ?>
					<option value="<?php echo $ph->fb_phase_id; ?>"<?php selected($ph->fb_phase_id, $phase); ?>><?php echo $ph->fb_phase_name; ?></option>
				<?php } ?>	
				</select>
			</label>
		</p>
		<p>
			<label for="<?php echo $this->get_field_id('group'); ?>"><?php _e('Group:', 'wp-football'); ?>
				<select name="<?php echo $this->get_field_name('group'); ?>" id="<?php echo $this->get_field_id('group'); ?>" class="widefat groups">
					<option value="0"><?php _e('All','wp-football'); ?></option>
				<?php foreach ($groups as $gr) { ?>
					<option value="<?php echo $gr->fb_group_id; ?>"<?php selected($gr->fb_group_id, $group); ?>><?php echo $gr->fb_group_name; ?></option>
				<?php } ?>	
				</select>
			</label>
		</p>
		<p>
			<label for="<?php echo $this->get_field_id('template'); ?>"><?php _e('Template:', 'wp-football'); ?>
				<select name="<?php echo $this->get_field_name('template'); ?>" id="<?php echo $this->get_field_id('template'); ?>" class="widefat">
				<?php foreach ($templates as $templ) { ?>
					<option value="<?php echo $templ->fb_template_id; ?>"<?php selected($templ->fb_template_id, $template); ?>><?php echo $templ->fb_template_name; ?></option>
				<?php } ?>	
				</select>
			</label>
		</p>
		<input type="hidden" id="<?php echo $this->get_field_id('submit'); ?>" name="<?php echo $this->get_field_name('submit'); ?>" value="1" />
<?php
	}
}


### Function: Init wp-football Widget
add_action('widgets_init', 'widget_football_init');
function widget_football_init() {
	register_widget('WP_Widget_Football');
}

// Create Football Tables
register_activation_hook( __FILE__, 'create_football_tables' );
function create_football_tables() {
	global $wpdb;
	if(@is_file(ABSPATH.'/wp-admin/upgrade-functions.php')) {
		include_once(ABSPATH.'/wp-admin/upgrade-functions.php');
	} elseif(@is_file(ABSPATH.'/wp-admin/includes/upgrade.php')) {
		include_once(ABSPATH.'/wp-admin/includes/upgrade.php');
	} else {
		die('We have problem finding your \'/wp-admin/upgrade-functions.php\' and \'/wp-admin/includes/upgrade.php\'');
	}
	$charset_collate = '';
	if($wpdb->supports_collation()) {
		if(!empty($wpdb->charset)) {
			$charset_collate = "DEFAULT CHARACTER SET $wpdb->charset";
		}
		if(!empty($wpdb->collate)) {
			$charset_collate .= " COLLATE $wpdb->collate";
		}
	}
	// Create Football Tables
	$create_table_league = "CREATE TABLE $wpdb->league (".
							"fb_league_id int(10) NOT NULL auto_increment,".
							"fb_league_name varchar(200) NOT NULL default '',".
							"fb_league_description longtext NOT NULL,".
							"fb_league_continent varchar(100) NOT NULL default '',".
							"fb_league_country varchar(100) NOT NULL default '',".
							"fb_league_timezone varchar(100) NOT NULL default '',".
							"fb_league_season varchar(20) NOT NULL default '',".
							"fb_league_dir_icons varchar(200) NOT NULL default '',".
							"fb_league_active char(1) NOT NULL default '1',".
							"PRIMARY KEY (fb_league_id)) $charset_collate;";

	$create_table_team = "CREATE TABLE $wpdb->team (".
							"fb_team_id int(10) NOT NULL auto_increment,".
							"fb_team_id_league int(10) NOT NULL default 0,".
							"fb_team_id_group int(10) NOT NULL default 0,".
							"fb_team_name varchar(200) NOT NULL default '',".
							"fb_team_name_abb varchar(30) NOT NULL default '',".
							"fb_team_symbol varchar(100) NOT NULL,".
							"fb_team_link_info varchar(200) NOT NULL,".
							"fb_team_continent int(1) NOT NULL default '0',".
							"fb_team_played int(4) NOT NULL default '0',".
							"fb_team_won int(4) NOT NULL default '0',".
							"fb_team_draw int(4) NOT NULL default '0',".
							"fb_team_loss int(4) NOT NULL default '0',".
							"fb_team_gf int(4) NOT NULL default '0',".
							"fb_team_ga int(4) NOT NULL default '0',".
							"fb_team_pts int(4) NOT NULL default '0',".
							"fb_team_class int(4) NOT NULL default '0',".
							"PRIMARY KEY (fb_team_id)) $charset_collate;";

	$create_table_continent = "CREATE TABLE $wpdb->continent (".
							"fb_continent_id int(1) NOT NULL auto_increment,".
							"fb_continent_name varchar(200) NOT NULL default '',".
							"PRIMARY KEY (fb_continent_id)) $charset_collate;";

	$create_table_phase = "CREATE TABLE $wpdb->phase (".
							"fb_phase_id int(2) NOT NULL auto_increment,".
							"fb_phase_id_league int(10) NOT NULL default 0,".
							"fb_phase_order int(10) NOT NULL default 0,".
							"fb_phase_name varchar(200) NOT NULL default '',".
							"fb_phase_name_abb varchar(30) NOT NULL default '',".
							"PRIMARY KEY (fb_phase_id)) $charset_collate;";

	$create_table_group = "CREATE TABLE $wpdb->group (".
							"fb_group_id int(2) NOT NULL auto_increment,".
							"fb_group_id_league int(10) NOT NULL default 0,".
							"fb_group_order int(10) NOT NULL default 0,".
							"fb_group_name varchar(200) NOT NULL default '',".
							"fb_group_name_abb varchar(30) NOT NULL default '',".
							"PRIMARY KEY (fb_group_id)) $charset_collate;";

	$create_table_match = "CREATE TABLE $wpdb->match (".
							"fb_match_id int(10) NOT NULL auto_increment,".
							"fb_match_number int(10) NOT NULL default 0,".
							"fb_match_id_league int(10) NOT NULL default 0,".
							"fb_match_id_group int(10) NOT NULL default 0,".
							"fb_match_id_phase int(10) NOT NULL default 0,".
							"fb_match_team1 int(10) NOT NULL default '0',".
							"fb_match_team2 int(10) NOT NULL default '0',".
							"fb_match_day varchar(02) NOT NULL default '',".
							"fb_match_month varchar(02) NOT NULL,".
							"fb_match_year varchar(4) NOT NULL,".
							"fb_match_time varchar(5) NOT NULL default '',".
							"fb_match_stadium varchar(100) NOT NULL default '',".
							"fb_match_city varchar(100) NOT NULL default '',".
							"fb_match_score1 char(2) NOT NULL default '',".
							"fb_match_score2 char(2) NOT NULL default '',".
							"fb_match_nt char(1) NOT NULL default '',".
							"fb_match_remark longtext character set utf8 NOT NULL,".
							"PRIMARY KEY (fb_match_id)) $charset_collate;";

	$create_table_template = "CREATE TABLE $wpdb->template (".
							"fb_template_id int(2) NOT NULL auto_increment,".
							"fb_template_name varchar(40) NOT NULL default '',".
							"fb_template_category int(2) NOT NULL default '0',".
							"fb_template_program varchar(40) NOT NULL default '',".
							"fb_template_fields_c longtext character set utf8 NOT NULL,".
							"fb_template_fields_m longtext character set utf8 NOT NULL,".
							"PRIMARY KEY (fb_template_id)) $charset_collate;";
							
	maybe_create_table($wpdb->league, $create_table_league);
	maybe_create_table($wpdb->team, $create_table_team);
	maybe_create_table($wpdb->continent, $create_table_continent);
	maybe_create_table($wpdb->phase, $create_table_phase);
	maybe_create_table($wpdb->group, $create_table_group);
	maybe_create_table($wpdb->match, $create_table_match);
	maybe_create_table($wpdb->template, $create_table_template);
	
	if ( $wpdb->get_var("SELECT COUNT(*) FROM {$wpdb->continent}") ==  0) {
		$wpdb->insert( $wpdb->continent, array('fb_continent_name' => 'Africa') );
		$wpdb->insert( $wpdb->continent, array('fb_continent_name' => 'Asia') );
		$wpdb->insert( $wpdb->continent, array('fb_continent_name' => 'Europe') );
		$wpdb->insert( $wpdb->continent, array('fb_continent_name' => 'North, Central America and Caribbean') );
		$wpdb->insert( $wpdb->continent, array('fb_continent_name' => 'Oceania') );
		$wpdb->insert( $wpdb->continent, array('fb_continent_name' => 'South America') );
	}
	load_plugin_textdomain('wp-football', false, dirname( plugin_basename(__FILE__) ) . '/langs');

	if ( $wpdb->get_var("SELECT COUNT(*) FROM {$wpdb->league} WHERE fb_league_id = '1'") ==  0) {
		require_once('football_import_wc2010.php');
    }	
	
	if ( $wpdb->get_var("SELECT COUNT(*) FROM {$wpdb->template}") ==  0) {
		$wpdb->query("INSERT INTO {$table_prefix}fb_template VALUES (1,'World Cup',2,'template_worldCup.php','a:10:{i:0;s:12:\"fb_team_name\";i:2;s:14:\"fb_team_symbol\";i:3;s:14:\"fb_team_played\";i:4;s:11:\"fb_team_won\";i:5;s:12:\"fb_team_draw\";i:6;s:12:\"fb_team_loss\";i:7;s:10:\"fb_team_gf\";i:8;s:10:\"fb_team_ga\";i:9;s:11:\"fb_team_pts\";i:10;s:13:\"fb_team_class\";}','a:4:{i:0;s:15:\"fb_match_number\";i:1;s:12:\"fb_team_name\";i:3;s:14:\"fb_team_symbol\";i:4;s:13:\"fb_match_city\";}')");
		$wpdb->query("INSERT INTO {$table_prefix}fb_template VALUES (2,'Template Default',2,'template_default.php','a:10:{i:0;s:12:\"fb_team_name\";i:2;s:14:\"fb_team_symbol\";i:3;s:14:\"fb_team_played\";i:4;s:11:\"fb_team_won\";i:5;s:12:\"fb_team_draw\";i:6;s:12:\"fb_team_loss\";i:7;s:10:\"fb_team_gf\";i:8;s:10:\"fb_team_ga\";i:9;s:11:\"fb_team_pts\";i:10;s:13:\"fb_team_class\";}','a:4:{i:0;s:15:\"fb_match_number\";i:1;s:12:\"fb_team_name\";i:3;s:14:\"fb_team_symbol\";i:4;s:13:\"fb_match_city\";}')");
		$wpdb->query("INSERT INTO {$table_prefix}fb_template VALUES (3,'Compact version',1,'','a:10:{i:1;s:16:\"fb_team_name_abb\";i:2;s:14:\"fb_team_symbol\";i:3;s:14:\"fb_team_played\";i:4;s:11:\"fb_team_won\";i:5;s:12:\"fb_team_draw\";i:6;s:12:\"fb_team_loss\";i:7;s:10:\"fb_team_gf\";i:8;s:10:\"fb_team_ga\";i:9;s:11:\"fb_team_pts\";i:10;s:13:\"fb_team_class\";}','a:2:{i:2;s:16:\"fb_team_name_abb\";i:3;s:14:\"fb_team_symbol\";}')");
		$wpdb->query("INSERT INTO {$table_prefix}fb_template VALUES (4,'Extended version',1,'','a:10:{i:0;s:12:\"fb_team_name\";i:2;s:14:\"fb_team_symbol\";i:3;s:14:\"fb_team_played\";i:4;s:11:\"fb_team_won\";i:5;s:12:\"fb_team_draw\";i:6;s:12:\"fb_team_loss\";i:7;s:10:\"fb_team_gf\";i:8;s:10:\"fb_team_ga\";i:9;s:11:\"fb_team_pts\";i:10;s:13:\"fb_team_class\";}','a:4:{i:0;s:15:\"fb_match_number\";i:1;s:12:\"fb_team_name\";i:3;s:14:\"fb_team_symbol\";i:4;s:13:\"fb_match_city\";}')");
    }	

	if(!get_option("wpfootball_version")) {
		$options["version"] = "beta";
		update_option("wpfootball_version", $options);
	}

	if (!get_option("wpfootball_criteria")) {
		$opc['pw'] = 3;
		$opc['pd'] = 1;
		$opc['nw'] = '';
		$opc['ogd'] = 1;
		$opc['ogf'] = 2;
		$criteria[1] = $opc;
		update_option("wpfootball_criteria", $criteria);
	}

	$role = get_role('administrator');
	if(!$role->has_cap('manage_football')) {
		$role->add_cap('manage_football');
	}
}	

add_action('admin_head', 'javascript_wpfootball');

function javascript_wpfootball() {
?>
<script type="text/javascript">
function pop_selects(id_select,id_league) {
	if (id_league == 0) return;
	var data = {
		action: 'my_action_phases',
		id: id_league
	};
	var id_select_phase = id_select;
	id_select_phase = id_select_phase.replace('league','phase');
	jQuery("#"+id_select_phase).html('<option value="Wait..."><?php _e('Wait...','wp-football'); ?></option>');

	jQuery.ajax({url: "admin-ajax.php", type: "POST", data: data, success: function(results) {
		results = eval(results);
		var options = '';
		for (var i = 0; i < results.length; i++) {
			options += '<option value="' + results[i].id + '">' + results[i].nome + '</option>';
		}
		jQuery("#"+id_select_phase).html(options);
		}
	})

	var data = {
		action: 'my_action_groups',
		id: id_league
	};
	var id_select_group = id_select;
	id_select_group = id_select_group.replace('league','group');
	jQuery("#"+id_select_group).html('<option value="Wait..."><?php _e('Wait...','wp-football'); ?></option>');

	jQuery.ajax({url: "admin-ajax.php", type: "POST", data: data, success: function(results) {
		results = eval(results);
		var options = '';
		for (var i = 0; i < results.length; i++) {
			options += '<option value="' + results[i].id + '">' + results[i].nome + '</option>';
		}
		jQuery("#"+id_select_group).html(options);
		}
	})

} 
</script>
<?php
}

add_action('wp_ajax_my_action_groups', 'my_callback_groups');

function my_callback_groups() {
	$id = $_POST['id'];
	$groups =  get_groups_league($id); 
	$retorno = '[ ';
	$retorno .= '{id: \'0\', nome: \''.__('--- Select ---','wp-football').'\'} ';
	foreach ($groups as $gr) {
	$retorno .= ', {id: \''.$gr->fb_group_id.'\', nome: \''.$gr->fb_group_name.'\'} ';
	}
	$retorno .= ' ]';
	echo $retorno;
	die();
}

add_action('wp_ajax_my_action_phases', 'my_callback_phases');

function my_callback_phases() {
	$id = $_POST['id'];
	$phases =  get_phases_league($id); 
	$retorno = '[ ';
	$retorno .= '{id: \'0\', nome: \''.__('--- Select ---','wp-football').'\'} ';
	foreach ($phases as $ph) {
	$retorno .= ', {id: \''.$ph->fb_phase_id.'\', nome: \''.$ph->fb_phase_name.'\'} ';
	}
	$retorno .= ' ]';
	echo $retorno;
	die();
}

function get_templateLeague($league, $id_template) {
	global $wpdb, $table_prefix;
	$template = $wpdb->get_var("SELECT fb_template_program FROM {$table_prefix}fb_template WHERE fb_template_id = '$id_template'");
	require_once(ABSPATH.'/wp-content/plugins/wp-football/templates/'.$template);
	return $template_result;
}
?>
