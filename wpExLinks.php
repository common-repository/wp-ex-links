<?php
/*
Plugin Name:wpExLinks (Wordpress Exchange Links)
Plugin URI: http://blog.smileylover.com/wordpress-exchange-links-plugin/
Description: Allow you and your visitor to automated exchange link and display the result in available widget.  Just create a new page with &lsaquo;!---wpExLinks---&rsaquo; in content.  Manage all income link in administration page.  You can replace standard blogroll widget with this one.  Widget options are limit display link and order by name or ID.  This is BETA version.  Support is always available at <a href="http://blog.smileylover.com/wordpress-exchange-links-plugin/">Wordpress Exchange Links Page</a>.
Author: Ferri Sutanto
Version: 1.4
Author URI: http://blog.smileylover.com/
*/

/*  Copyright 2008 Ferri Sutanto  (email: greenhouseprod@gmail.com)
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

require_once('wpExLinks.inc.php');


if (class_exists("wpExLinksPlugin")) {
	$wpExLinks_pluginSeries = new wpExLinksPlugin();
}

//Initialize the admin and users panel
if (!function_exists("wpExLinksPlugin_ap")) {
	function wpExLinksPlugin_ap() {
		global $wpExLinks_pluginSeries;
		if (!isset($wpExLinks_pluginSeries)) {
			return;
		}
		if (function_exists('add_options_page')) {
	add_options_page('Wp Exchange Links', 'Wp Exchange Links', 9, basename(__FILE__), array(&$wpExLinks_pluginSeries, 'printAdminPage'));
		}
	}	
}


//Actions and Filters
if(isset($wpExLinks_pluginSeries)) {
	//Actions
	add_action('admin_menu','wpExLinksPlugin_ap');
	add_action('wp_head', array(&$wpExLinks_pluginSeries, 'xcss'));
	add_action('activate_wpExLinks/wpExLinks.php',  array(&$wpExLinks_pluginSeries, 'init'));
	
	//Filters
	add_filter('the_content', array(&$wpExLinks_pluginSeries, 'xreplacewords'));
	
	register_activation_hook(__FILE__,array(&$wpExLinks_pluginSeries,'wpxlink_install'));

}
?>