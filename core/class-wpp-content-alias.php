<?php
/**
 * Copyright (c) 2013, WP Poets and/or its affiliates <plugins@wppoets.com>
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
 * Starting point for the plugin
 * 
 * Everything about the plugin starts here.
 * 
 * @author Michael Stutz <michaeljstutz@gmail.com>
 * 
 */
class WPP_Content_Alias {
	/** Used to keep the init state of the class */
	private static $_initialized = false;
	/** Used to store the plugin settings */
	private static $_settings = array();
	
	const POSTMETA_CONTENT_ALIAS  = '_wpp_content_alias';
	const DEFAULT_REDIRECT_CODE   = 301;
	const FILTER_ADD_ALIAS        = 'WPP_Content_Alias_Add_Alias';
	const FILTER_SANITIZE_URL     = 'WPP_Content_Alias_Sanitize_Url';
	const ACTION_URL_REDIRECT     = 'WPP_Content_Alias_Url_Redirect';
	const ACTION_OPTION_BUILD_TAB = 'wpp_content_alias_build_tab';
	const ACTION_OPTION_SAVE_TAB  = 'wpp_content_alias_save_tab';
	
	/**
	 * Initialization point for the static class
	 * 
	 * @return void No return value
	 */
	public static function init() {
		if ( self::$_initialized )
			return;
		
		if ( ! is_admin() ) {
			if ( ! class_exists( 'WPP_Content_Alias_Public' ) )
				require_once( WPP_CONTENT_ALIAS_PLUGIN_PATH . '/core/class-wpp-content-alias-public.php' );
			
			WPP_Content_Alias_Public::init();
		} else {
			if ( ! class_exists( 'WPP_Content_Alias_Admin' ) )
				require_once( WPP_CONTENT_ALIAS_PLUGIN_PATH . '/core/class-wpp-content-alias-admin.php' );
			
			WPP_Content_Alias_Admin::init();
		}
		self::$_initialized = true;
	}
	
	/**
	 * General use function for sanitizing a url path
	 * 
	 * @param string $url_string String of the url path to sanitize
	 * @return string Returns the sanitized url
	 */
	public static function sanitize_url_path( $url_string ) {
		self::init();
		$url_string = apply_filters( self::FILTER_SANITIZE_URL, $url_string );
		//TODO: add more sanitization here
		$parsed_url = parse_url( $url_string );
		$parsed_path = $parsed_url['path'];
		//If the parsed_path is not empty and does not start with / add it to the start
		if ( !empty( $parsed_path ) && strncmp( $parsed_path, '/', 1 ) )
			$parsed_path = '/' . $parsed_path;
		
		if ( isset( $parsed_path ) && ! empty( $parsed_path ) )
			return $parsed_path;
		
		else
			return '';
	}
	
	/**
	 * Basic function for adding an alias
	 * 
	 * @param int $post_id The id of the post to add the alias for
	 * @param string $post_alias The url path to add as an alias to the post
	 * @return void No return value
	 */
	public static function add_alias( $post_id, $post_alias ) {
		self::init();
		$post_alias = apply_filters( self::FILTER_ADD_ALIAS, $post_alias );
		$post_alias_path = self::sanitize_url_path( $post_alias );
		if ( ! empty( $post_alias_path ) )
			add_post_meta( $post_id, self::POSTMETA_CONTENT_ALIAS, $post_alias_path, false );
	}
	
	/**
	 * Helper function for the debug process
	 * 
	 * @param string $message The message to send to the error log
	 * @return void No return value
	 */
	public static function debug( $message ) {
		if ( WP_DEBUG === true ) {
				if ( is_array( $message ) || is_object( $message ) )
					error_log( print_r( $message, true ) );
				else
					error_log( $message );
		}
	}
}