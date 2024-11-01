<?php
/*
Plugin Name: wpExLinks (Wordpress Exchange Links) - Widget
Plugin URI: http://blog.smileylover.com/wordpress-exchange-links-plugin/
Description: Widget for wpExLinks Plugin with limit display links and order by options.  Remember to activated WpExLinks Plugin first!
Author: Ferri Sutanto
Version: 1.4
Author URI: http://blog.smileylover.com/
*/

/*  Copyright 2008 Ferri, http://blog.smileylover.com/
**
**  This program is free software; you can redistribute it and/or modify
**  it under the terms of the GNU General Public License as published by
**  the Free Software Foundation; either version 2 of the License, or
**  (at your option) any later version.
**
**  This program is distributed in the hope that it will be useful,
**  but WITHOUT ANY WARRANTY; without even the implied warranty of
**  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
**  GNU General Public License for more details.
**
**  You should have received a copy of the GNU General Public License
**  along with this program; if not, write to the Free Software
**  Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
*/


### Function: Init WP-ExLinks Widget
function widget_exlinks_init() {	
	
	if (!function_exists('register_sidebar_widget')) {
		return;
	}
	
	//Viewer for widget
	function listExLinks() {
	$options = get_option('wpExLinksAdminOptions');	
	$limit = $options['web_limit_widget'];
	$order_by = $options['widget_order_by'];
	global $wpdb;
	$pf = $wpdb->prefix."wpxlink";
	$query = "SELECT web_url, web_title, web_desc FROM $pf WHERE web_approve='Approve' ORDER BY $order_by ASC";
	if($limit > 0)
	$query .= " LIMIT $limit";
	
	$dbLinks = $wpdb->get_results($query);
		foreach ($dbLinks as $Links){
			echo '
			<ul>
				<li><a href="'.stripslashes($Links->web_url).'" title="'.stripslashes($Links->web_desc).'">'.stripslashes(ucwords($Links->web_title)).'</a></li>
			</ul> 
			';
		}
	}
	
	//Single function viewer without widget
	function wpExLinksList($limit=10, $orderby='name', $order='ASC') {	
	global $wpdb;
	$pf = $wpdb->prefix."wpxlink";
	$dbLinks = $wpdb->get_results("
				SELECT web_url, web_title, web_desc FROM $pf WHERE web_approve='Approve' ORDER BY $orderby $order LIMIT $limit
				");
	echo '<div id="wpExLinks">
		<ul>
		';
		foreach ($dbLinks as $Links){
			echo '			
				<li><a href="'.stripslashes($Links->web_url).'" title="'.stripslashes($Links->web_desc).'">'.stripslashes(ucwords($Links->web_title)).'</a></li>			
			';
		}
	echo '</ul> 
		</div><!--wpExLinks-->
		';
	}
	
	//Display widget
	function exLinks($args){
		extract($args);
		$options = get_option('wpExLinksAdminOptions');	
		$title = $options['widget_exLinks_title'];
		echo $before_widget.$before_title.$title.$after_title;
			if(function_exists('listExLinks')){
				listExLinks();
			}
		echo $after_widget.'<br /><a href="'.$options['permalink_expage'].'">&raquo; Submit your link!</a><br /><br /><small>Powered by <a href="http://blog.smileylover.com/" target="_blank" title="Wordpress Exchange Links Plugin" rel="dofollow">wpExLinks</a></small>';
	}
	
	//Wigdet options
	function widget_exLinks_options() {
	$options = get_option('wpExLinksAdminOptions');
	if ($_POST['widget_exLinks-submit']) {
		$options['widget_exLinks_title'] = strip_tags($_POST['widget_exLinks_title']);
		$options['web_limit_widget'] = intval($_POST['web_limit_widget']);
		$options['widget_order_by'] = strip_tags($_POST['widget_order_by']);
		update_option('wpExLinksAdminOptions', $options);
	}
	
	//widget title
	echo '<p style="text-align: left;"><label for="widget_exLinks_title">';
		_e('Widget Title', 'wpExLinksPlugin');
	echo '&nbsp;: </label><br /><input type="text" id="widget_exLinks_title" name="widget_exLinks_title" value="'.htmlspecialchars(stripslashes($options['widget_exLinks_title'])).'" /></p>'."\n";
	
	//limit links to be display
	echo '<p style="text-align: left;"><label for="web_limit_widget">';
		_e('Limit display links', 'wpExLinksPlugin');
	echo '&nbsp;: </label><br /><input type="text" id="web_limit_widget" name="web_limit_widget" value="'.htmlspecialchars(stripslashes($options['web_limit_widget'])).'" /><br /><small>Default is 0 to display all links</small></p>'."\n";
	
	//set order by name or id
	echo '<p style="text-align: left;"><label for="widget_order_by">';
		_e('Order by', 'wpExLinksPlugin');
	echo '&nbsp;: </label><br /><input type="radio" id="widget_order_by" name="widget_order_by" value="name"';
		if($options['widget_order_by'] == "name"){echo 'checked';}
	echo '/> Name</label>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type="radio" id="widget_order_by" name="widget_order_by" value="id"';
		if($options['widget_order_by'] == "id"){echo 'checked';}
	echo '/> ID</label><br /><small>Default is order by NAME</small></p>'."\n";
	
	//Hidden submit button
	echo '<input type="hidden" id="widget_exLinks-submit" name="widget_exLinks-submit" value="1" />'."\n";
	echo '<small>Powered by <a href="http://blog.smileylover.com/" target="_blank" title="Wordpress Exchange Links PLugin" rel="dofollow">wpExLinks</a></small><br />';
	}	

	// Register Widgets
	register_sidebar_widget('Wp Ex Links','exLinks');
	register_widget_control('Wp Ex Links', 'widget_exLinks_options');
	

}
### Function: Load The WP-ExLinks Widget
add_action('plugins_loaded', 'widget_exlinks_init')
?>