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
 * @author Michael Stutz <michaeljstutz@gmail.com>
 * 
 * @since 0.1.0
 */
class WPP_Content_Alias_Admin {
	/** 
	 * Used to keep the init state of the class 
	 * 
	 * @since 0.1.0
	 */
	private static $_initialized = false;
	
	/** 
	 * Used to keep the $post_id permalink for comparison
	 * 
	 * @since 0.1.0
	 */
	private static $_permalink_compare = array();
	
	/** 
	 * Used to keep the add_alias function cache 
	 * 
	 * @since 0.1.0
	 */
	private static $_add_alias_cache = array();
	
	/**
	 * Initialization point for the static class
	 * 
	 * @since 0.1.0
	 * @return void No return value
	 */
	public static function init() {
		if ( self::$_initialized ) 
			return;
		
		wpp_content_alias_init_class( 'WPP_Content_Alias_Admin_Metabox', '/core/class-wpp-content-alias-admin-metabox.php' );
		wpp_content_alias_init_class( 'WPP_Content_Alias_Admin_Options', '/core/class-wpp-content-alias-admin-options.php' );
		
		add_action( 'pre_post_update', array( __CLASS__, 'pre_post_update' ) );
		add_action( 'save_post', array( __CLASS__, 'save_post' ) );
		add_action( 'add_term_relationship', array( __CLASS__, 'before_change_term' ) );
		add_action( 'added_term_relationship', array( __CLASS__, 'after_change_term' ) );
		add_action( 'delete_term_relationships', array( __CLASS__, 'before_change_term' ) );
		add_action( 'deleted_term_relationships', array( __CLASS__, 'after_change_term' ) );
		
		
		
		self::$_initialized = true;
	}
	
	/**
	 * Action hook function for pre_post_update
	 * 
	 * @since 0.1.0
	 * @param int $post_id The id of the post
	 * @return void No return value
	 */
	public static function pre_post_update( $post_id ) {
		//wpp_content_alias_debug( 'pre_post_update: ' . $post_id );
		if ( wp_is_post_revision( $post_id ) ) //Check to make sure it is not a revision
			return;
		
		//TODO: add post type checking to not waste time if we are not using aliase for the post type
		
		if ( ! isset( self::$_permalink_compare[ $post_id ] ) ) //Check to see if the post id has already been created
			self::$_permalink_compare[ $post_id ] = array();
		
		self::$_permalink_compare[ $post_id ]['pre_post_update'] = TRUE;
		self::$_permalink_compare[ $post_id ]['permalink'] = get_permalink( $post_id );
		self::$_permalink_compare[ $post_id ]['status'] = get_post_status( $post_id );
	}
	
	/**
	 * Action hook function for add_term_relationship
	 * 
	 * @since 0.1.0
	 * @param int $object_id The id of the object
	 * @return void No return value
	 */
	public static function before_change_term( $object_id ) {
		//wpp_content_alias_debug( 'before_change_term: ' . $object_id );
		if ( isset( self::$_permalink_compare[ $object_id ]['pre_post_update'] ) ) //If we have pre_post_update data then we dont need to do anything here
			return;
		
		//TODO: add post type checking to not waste time if we are not using aliase for the post type
		
		if ( ! isset( self::$_permalink_compare[ $object_id ] ) ) //Check to see if the object id has already been created
			self::$_permalink_compare[ $object_id ] = array();
		
		self::$_permalink_compare[ $object_id ]['permalink'] = get_permalink( $object_id );
		self::$_permalink_compare[ $object_id ]['status'] = get_post_status( $object_id );
	}
	
	/**
	 * Action hook function for added_term_relationship
	 * 
	 * @since 0.1.0
	 * @param int $object_id The id of the object
	 * @return void No return value
	 */
	public static function after_change_term( $object_id ) {
		//wpp_content_alias_debug( 'after_change_term: ' . $object_id );
		if ( isset( self::$_permalink_compare[ $object_id ]['pre_post_update'] ) ) //If we have pre_post_update data then we dont need to do anything here
			return;
		
		//TODO: add post type checking to not waste time if we are not using aliase for the post type
		
		self::save_post( $object_id );
	}
	
	/**
	 * Action hook function for save_post
	 * 
	 * @since 0.1.0
	 * @param int $post_id The id of the post
	 * @return void No return value
	 */
	public static function save_post( $post_id ) {
		//wpp_content_alias_debug( 'save_post: ' . $post_id );		
		if ( ! isset( self::$_permalink_compare[ $post_id ] ) ) //No need to run compare if there is no old value to check against
			return;
		
		$old_permalink = self::$_permalink_compare[ $post_id ][ 'permalink' ];
		$old_status = self::$_permalink_compare[ $post_id ][ 'status' ];
		
		if ( in_array( $old_status, array( 'draft', 'pending', 'auto-draft' ) ) ) //Permalinks from the following statuses are not created correctly so do not compare
			return;
		
		$new_status = get_post_status( $post_id );
		$new_permalink = get_permalink( $post_id );
		
		if ( $old_permalink !== $new_permalink ) {
			self::add_alias( $post_id, WPP_Content_Alias::sanitize_url_path( $old_permalink ), TRUE );
			self::clean_sync( $post_id );
		}
		unset( $old_permalink, $old_status, $new_status, $new_permalink ); //Clean up
	}
	
	/**
	 * Basic function for adding an alias
	 * 
	 * @since 0.1.0
	 * @param int $post_id The id of the post to add the alias for
	 * @param string $post_alias The url path to add as an alias to the post
	 * @return boolean Boolean true.
	 */
	public static function add_alias( $post_id, $post_alias, $dup_check = FALSE ) {
		if ( isset( self::$_add_alias_cache[ $post_id ][ $post_alias ] ) ) 
			return true; //we already added it
		
		if ( $dup_check ) {
			$current_aliases = get_post_meta( $post_id, WPP_Content_Alias::POSTMETA_CONTENT_ALIAS );
			if ( in_array( $post_alias, $current_aliases) )
				return true;
		}
		
		$return_value = add_post_meta( $post_id, WPP_Content_Alias::POSTMETA_CONTENT_ALIAS, $post_alias, false );
		
		if ( ! $return_value )
			return $return_value;
		
		if ( ! isset(	self::$_add_alias_cache[ $post_id ] ) ) 
			self::$_add_alias_cache[ $post_id ] = array();

		self::$_add_alias_cache[ $post_id ][ $post_alias ] = TRUE;
		return $return_value;
	}
	
	/**
	 * Basic function for removing an alias
	 * 
	 * @since 0.1.0
	 * @param int $post_id The id of the post
	 * @param string $post_alias The alias to remove from the post
	 * @return boolean False for failure. True for success.
	 */
	public static function remove_alias( $post_id, $post_alias ) {
		$return_value = delete_post_meta( $post_id, WPP_Content_Alias::POSTMETA_CONTENT_ALIAS, $post_alias );
		
		if ( ! $return_value ) 
			return $return_value;
		
		if ( isset( self::$_add_alias_cache[ $post_id ][ $post_alias ] ) )
			unset( self::$_add_alias_cache[ $post_id ][ $post_alias ] ); //We have a chached value that needs to removed

		return $return_value;
	}
	
	/**
	 * Basic function for syncing aliases
	 * 
	 * @since 0.1.0
	 * @param int $post_id The id of the post
	 * @param array $new_aliases An array of all the new aliases to sync
	 * @return void No return value
	 */
	public static function sync_aliases( $post_id, $new_aliases ) {
		$process_queue = array();
		$old_aliases = get_post_meta( $post_id, WPP_Content_Alias::POSTMETA_CONTENT_ALIAS );
		$post_sanitized_permalink = WPP_Content_Alias::sanitize_url_path( get_permalink( $post_id ) );
		//Add the current sanitized url to the new_aliases 
		$new_aliases[] = $post_sanitized_permalink;
		foreach( $new_aliases as &$new_alias ) {
			$new_alias = WPP_Content_Alias::sanitize_url_path( $new_alias ); //Need to sanitize the new aliases
			//If the new alias is not empty, is not already in the queue, and is not already an alias
			if ( ! empty( $new_alias ) && ! isset( $process_queue[ $new_alias ] ) &&  ! in_array( $new_alias, $old_aliases ) ) {
				$process_queue[ $new_alias ] = TRUE; //Add to the queue to be added
			}
		}
		foreach( $old_aliases as $old_alias ) {
			//If the old alias is not empty, is not already in the queue, and is not already a new_alias
			if ( ! empty( $old_alias ) && ! isset( $process_queue[ $old_alias ] ) &&  ! in_array( $old_alias, $new_aliases ) ) {
				$process_queue[ $old_alias ] = FALSE; //Add to the queue to be removed
			}
		}
		foreach( $process_queue as $post_alias => $is_add ) {
			if ( $is_add ) { //If the value is true add the alias
				self::add_alias( $post_id, $post_alias );
			} else { //Else we remove the alias
				self::remove_alias( $post_id, $post_alias );
			}
		}
		unset( $process_queue, $old_aliases, $post_sanitized_permalink, $new_alias, $old_alias, $post_alias, $is_add); //Clean up
	}
	
	/**
	 * Function for doing a clean sync agains the current aliases cleaning up any issues
	 * 
	 * @since 0.1.0
	 * @param int $post_id The id of the post
	 * @return void No return value
	 */
	public static function clean_sync( $post_id ) {
		$aliases = get_post_meta( $post_id, WPP_Content_Alias::POSTMETA_CONTENT_ALIAS );
		self::sync_aliases( $post_id, $aliases);
		unset( $aliases ); //Clean up
	}
}