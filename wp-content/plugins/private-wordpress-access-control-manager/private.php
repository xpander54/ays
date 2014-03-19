<?php
/*
Plugin Name: Private! Wordpress Access Control Manager
Plugin URI: http://plugins.x-blogs.org/private/
Description: Manage easily, who can access your blog or certain parts of your blog
Version: 1.7.0
Author: Oliver Sperke
Author URI: http://x-blogs.org/

Copyright 2010 Oliver Sperke <plugins@x-blogs.org>

This program is free software; you can redistribute it and/or
modify it under the terms of the GNU General Public License
version 2 as published by the Free Software Foundation.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program. If not, see <http://www.gnu.org/licenses/>.
*/

/*
This configuration value should be set here, to prevent not authorized
users to edit them. If you don't want users to associated with your
main blog, you should edit this variable to false.
*/
// define('PRIVATE_ALLOW_DIRECT_SUBSCRIPTION', true);
// define('PRIVATE_FORCE_MAIN_BLOG', true);

/* ----- Do not edit below this line, unless you know, what your are doing ----- */

if(phpversion()<'5.1')
{
	function private_fatal_php()
	{
		if(!current_user_can('manage_options'))
			return;

		echo '<div id="message" class="updated fade"><strong><p>Sorry, Private! needs PHP 5.1 or newer to work correctly. Besides that, your PHP Version is at least 5 years old. Please update for security reasons!</p></strong></div>';
	}
	add_action('admin_notices', 'private_fatal_php');
	return;
}

// Load the required framework
define('XFW_REL_PATH', PLUGINDIR.'/'.dirname(plugin_basename( __FILE__ )));
define('XFW_PATH', ABSPATH . XFW_REL_PATH);
define('XFW_URL', get_bloginfo('url') . '/' . PLUGINDIR.'/'.dirname(plugin_basename( __FILE__ )));
define('XFW_TMP_PATH', XFW_PATH . '/_' . sha1(AUTH_KEY));
define('XFW_TMP_URL', XFW_URL . '/_' . sha1(AUTH_KEY));
define('PRIVATE_VERSION', '170');

if(!defined('XFW_LOADED'))
	require_once('lib/xfw.class.php');

require_once('lib/xfw_private.class.php');

// .... and go!
$xfw = new xfw_private(
			'users.php',
			'private',
			'Private! Wordpress Access Control Manager',
			'manage_options',
			'Private! Settings',
			'private_wordpress_access_manager',
			array(
				'enable' => '',
				'norestrictions' => false,
				'relation' => false,
				'directsubscription' => false,
				'locksecurity' => false,
				'noapprove' => false,
				'capabilities' => 'administrator',
				'editauth' => 'edit_users',
				'page' => false,
				'home' => false,
				'front' => false,
				'category' => false,
				'catsingle' => false,
				'tag' => false,
				'tagsingle' => false,
				'categories' => false,
				'post' => false,
				'attachment' => false,
				'tags' => false,
				'posts' => false,
				'hidecat' => false,
				'hidetag' => false,
				'hidepost' => false,
				'cathidden' => false,
				'taghidden' => false,
				'archive' => false,
				'search' => false,
				'feed' => false,
				'showfeed' => false,
				'feedkey' => false,
				'requestfilter' => false,
				'throw404' => false,
				'filterlong' => false,
				'filterxss' => false,
				'filtersql' => false,
				'filterprefix' => false,
				'filterdirtraversal' => false,
				'filtertruncation' => false,
				'filterfiles' => false,
				'filterlog' => false,
				'strongpass' => false,
				'norealerror' => false,
				'customerror' => '<strong>ERROR</strong>: Username or password are not correct. Please check your data.',
				'loginjs' => false,
				'bruteforce' => false,
				'tarpit' => false,
				'maxtimeout' => 30,
				'maxattempts' => 5,
				'lockout' => 600,
				'keeplog' => 3600,
				'lockoutmessage' => 'Sorry, you are locked out until <strong>%until%</strong>.',
				'protectplugin' => false,
				'protectthemes' => false,
				'protectincludes' => false,
				'restrictip' => false,
				'headerswp' => 0,
				'removereadme' => false,
				'headerswlw' => 0,
				'headersrsd' => 0,
				'redirect' => '',
				'denynoredirect' => false,
				'denymessage' => 'You do not have enough rights to view this page. Please contact the site administrator.',
				'message' => '<p class="message">Sorry, you must be logged in, to see this page.</p>',
				'link' => false,
				'version' => PRIVATE_VERSION
			));