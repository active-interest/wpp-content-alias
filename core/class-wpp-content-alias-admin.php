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
 * @author Michael Stutz <michaeljstutz@gmail.com>
 */
class WPP_Content_Alias_Admin {
	/** Used to keep the init state of the class */
	private static $_initialized = false;
	
	/** Used to keep the $post_id permalink for comparison */
	private static $_permalink_compare = array();
	
	/**
	 * 
	 */
	public static function init() {
		if ( self::$_initialized ) 
			return;
		
		wpp_content_alias_init_class( 'WPP_Content_Alias_Admin_Metabox', '/core/class-wpp-content-alias-admin-metabox.php' );
		wpp_content_alias_init_class( 'WPP_Content_Alias_Admin_Options', '/core/class-wpp-content-alias-admin-options.php' );
		
		add_action( 'pre_post_update', array( __CLASS__, 'pre_post_update' ) );
		add_action( 'save_post', array( __CLASS__, 'save_post' ) );
		
		self::$_initialized = true;
	}
	
	/**
	 * 
	 */
	public static function pre_post_update( $post_id ) {
		if ( wp_is_post_revision( $post_id ) ) //Check to make sure it is not a revision
			return;
		
		self::$_permalink_compare[$post_id] = get_permalink( $post_id );
	}
	
	/**
	 * 
	 */
	public static function save_post( $post_id ) {
		if ( ! isset( self::$_permalink_compare[$post_id] ) ) //No need to run compare if there is no old value to check against
			return;
		
		$old_permalink = self::$_permalink_compare[$post_id];
		$new_permalink = get_permalink( $post_id );
		
		if ( $old_permalink !== $new_permalink ) {
			WPP_Content_Alias::add_alias( $post_id, $old_permalink );
		}
	}
}
