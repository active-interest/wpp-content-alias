<?php
/**
 * Plugin Name: WPP Redirect with Content Alias
 * Plugin URI: http://wppoets.com/plugins/content-alias.html
 * Description: Adds content alias 301 redirect functionality to all the WordPress content types for your site. This helps to reduce 404 errors when moving content and or migrated to WordPress.
 * Version: 0.9
 * Author: WP Poets <plugins@wppoets.com>
 * Author URI: http://wppoets.com
 * License: GPLv2 (dual-licensed)
 * License URI: http://www.gnu.org/licenses/gpl-2.0.html
 */
/**
 * Copyright (c) 2013, WP Poets and/or its affiliates <plugins@wppoets.com>
 * Portions of this distribution are copyrighted by:
 *   Copyright (c) 2013 Michael Stutz <michaeljstutz@gmail.com>
 * All rights reserved.
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License, version 2, as
 * published by the Free Software Foundation.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
 */
/**
 * @author Michael Stutz <michaeljstutz@gmail.com>
 */
if ( ! defined( 'ABSPATH' ) ) // We should not be loading this outside of wordpress
	die();

if ( ! defined( 'WPP_CONTENT_ALIAS_VERSION_NUM' ) )
	define( 'WPP_CONTENT_ALIAS_VERSION_NUM', '0.9.0' );

if ( ! defined( 'WPP_CONTENT_ALIAS_BUILD_NUM' ) )
	define( 'WPP_CONTENT_ALIAS_BUILD_NUM', '1' );

if ( ! defined( 'WPP_CONTENT_ALIAS_PLUGIN_FILE' ) )
	define( 'WPP_CONTENT_ALIAS_PLUGIN_FILE', __FILE__ );

if ( ! defined( 'WPP_CONTENT_ALIAS_PLUGIN_PATH' ) )
	define( 'WPP_CONTENT_ALIAS_PLUGIN_PATH', dirname(__FILE__ ) );

if ( ! defined( 'WPP_CONTENT_ALIAS_FILTER_FILE' ) )
	define( 'WPP_CONTENT_ALIAS_FILTER_FILE', 'wpp-content-alias/wpp-content-alias.php' );

wpp_content_alias_init_class( 'WPP_Content_Alias', '/core/class-wpp-content-alias.php' );

/**
 * Helper function for the checking is a class has been included
 * 
 * First the function checks to see if the class exists, if not it requires the file,
 * once that is complete it calls the static class function init()
 * 
 * @since 0.9.0
 * @param string $class_name The name of the class to check for
 * @param string $class_path The relitive path to the file to include if the class does not exists
 * @return void No return value
 */
function wpp_content_alias_init_class( $class_name, $class_path ) {
	if ( ! class_exists( $class_name ) )
		require_once( WPP_CONTENT_ALIAS_PLUGIN_PATH . $class_path );
	
	if ( method_exists( $class_name, 'init' ) )
		call_user_func( array( $class_name, 'init' ) );
}

/**
 * Helper function for the debug process
 * 
 * @since 0.9.0
 * @param string $message The message to send to the error log
 * @return void No return value
 */
function wpp_content_alias_debug( $message ) {
	if ( WP_DEBUG === true ) {
		if ( is_array( $message ) || is_object( $message ) )
			error_log( print_r( $message, true ) );
		else
			error_log( $message );
	}
}