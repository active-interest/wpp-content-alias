<?php
/**
 * Copyright (c) 2013, WP Poets and/or its affiliates <wppoets@gmail.com>
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
		
		if ( ! is_admin() )
			wpp_content_alias_init_class( 'WPP_Content_Alias_Public', '/core/class-wpp-content-alias-public.php' );
		else
			wpp_content_alias_init_class( 'WPP_Content_Alias_Admin', '/core/class-wpp-content-alias-admin.php' );
		
		self::$_initialized = true;
	}
	
	/**
	 * General use function for sanitizing a url path
	 * 
	 * @param string $url_string String of the url path to sanitize
	 * @return string Returns the sanitized url
	 */
	public static function sanitize_url_path( $url_string ) {
		$url_string = apply_filters( self::FILTER_SANITIZE_URL, $url_string );
		//TODO: add more sanitization here
		
		
		
		$parsed_url = parse_url( $url_string );
		$parsed_path = $parsed_url['path'];
		//If the parsed_path is not empty and does not start with / add it to the start
		if ( !empty( $parsed_path ) && strncmp( $parsed_path, '/', 1 ) )
			$parsed_path = '/' . $parsed_path;
		
		if ( '/' === $parsed_path) //We do not want a plan value of /
			$parsed_path = '';
		
		if ( isset( $parsed_path ) && ! empty( $parsed_path ) )
			return $parsed_path;
		else
			return '';
	}	
}