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
class WPP_Content_Alias_Admin_Metabox {
	/** Used to keep the init state of the class */
	private static $_initialized = false;
	
	const STYLE_BASE                    = 'wppca_metabox';
	const SCRIPT_BASE                   = 'wppca_metabox';
	const METABOX_ID                    = 'wppca_metabox';
	const METABOX_TITLE                 = 'Content Aliases';
	const METABOX_CONTEXT               = 'advanced';
	const METABOX_PRIORITY              = 'low';
	const METABOX_FORM_NONCENAME        = 'wppca_noncename';
	const METABOX_FORM_CONTENT_ALIASES  = 'wppca_aliases';
	
	/**
	 * 
	 * @return void No return value
	 */
	public static function init() {
		if ( self::$_initialized ) 
			return;
		
		add_action( 'admin_enqueue_scripts', array( __CLASS__, 'admin_enqueue_scripts' ) );
		add_action( 'save_post', array( __CLASS__, 'save_post' ) );
		add_action( 'add_meta_boxes', array( __CLASS__, 'add_meta_boxes' ) );

		self::$_initialized = true;
	}
	
	/**
	 * Function to value of initialized
	 * 
	 * This will return the current value of initialised, will be 
	 * used for testing to see if init() has been run
	 * 
	 * @return boolean Returns the value of $_initialized
	 */
	public static function isInit() {
		return self::$_initialized;
	}
	
	/**
	 * Test to see if we are in a valid location
	 * 
	 * This function is used to test to see if the current location 
	 * is a valid one for the metabox
	 * 
	 * @return boolean Returns true if we are in a valid location
	 */
	public static function valid_location() {
		$screen = get_current_screen();
		
		if ( 'post' !== $screen->base )
			return false;
		
		//TODO: add the business logic for doing the location checking
		echo "<!-- FINDMENOW \n";
		var_dump($screen);
		echo "\n-->\n\n";
		
		return true;
	}
	
	/**
	 * WordPress admin_enqueue_scripts action hook
	 * 
	 * This hook is in place to allow use to add styles and scripts to
	 * pages that need it
	 * 
	 * @return void No return value
	 */
	public static function admin_enqueue_scripts() {
		if ( ! self::isInit() ) //Function can not be called before init
			return;
		
		if ( self::valid_location() ) { //Check to see if it is a valid location
			//Register and Enqueue Style
			wp_register_style(
				self::STYLE_BASE, 
				plugins_url( 'css/wpp-content-alias-admin-metabox.css', WPP_CONTENT_ALIAS_PLUGIN_FILE ),
				array(),
				WPP_CONTENT_ALIAS_VERSION_NUM . '.' . WPP_CONTENT_ALIAS_BUILD_NUM
			);
			wp_enqueue_style( self::STYLE_BASE );
			
			//Register and Enqueue Script
			wp_register_script(
				self::SCRIPT_BASE, 
				plugins_url( 'js/wpp-content-alias-admin-metabox.js', WPP_CONTENT_ALIAS_PLUGIN_FILE ),
				array( 'jquery', 'jquery-ui-core' ), 
				WPP_CONTENT_ALIAS_VERSION_NUM . '.' . WPP_CONTENT_ALIAS_BUILD_NUM
			);
			wp_enqueue_script( self::SCRIPT_BASE );
		}
	}
	
	/**
	 * 
	 * @return void No return value
	 */
	public static function save_post( $post_id ) {
		if ( ! self::isInit() ) //Function can not be called before init
			return;
		
		if ( ! current_user_can( 'edit_page', $post_id ) ) //Check users permissions
			return;
		
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) //Check skip if we are only auto saving
			return;
		
		if ( wp_is_post_revision( $post_id ) ) //Check to make sure it is not a revision
			return;
		
		if ( ! wp_verify_nonce( filter_input( INPUT_POST, self::METABOX_FORM_NONCENAME, FILTER_SANITIZE_STRING ), plugin_basename( __FILE__ ) ) ) //Verify the form
			return;
		
		delete_post_meta( $post_id, WPP_Content_Alias::POSTMETA_CONTENT_ALIAS ); //Start off by deleting any excisting values
		$post_aliases = filter_input( INPUT_POST, self::METABOX_FORM_CONTENT_ALIASES, FILTER_SANITIZE_STRING, FILTER_FORCE_ARRAY );
		if ( ! empty( $post_aliases ) ) {
			foreach( (array) $post_aliases as $post_alias ) {
				WPP_Content_Alias::add_alias( $post_id, $post_alias );
			}
		}
	}
	
	/**
	 * 
	 * @return void No return value
	 */
	public static function add_meta_boxes() {
		if ( ! self::isInit() ) //Function can not be called before init
			return;
				
		//TODO: add admin section for all or selected post types
		
		$post_types = get_post_types( '','names' );
		foreach( $post_types as $post_type ) {
			add_meta_box(
				self::METABOX_ID,												//$id
				__( self::METABOX_TITLE ),							//$title
				array( __class__, 'display_metabox' ),	//$callback
				$post_type,															//$post_type
				self::METABOX_CONTEXT,									//$context
				self::METABOX_PRIORITY									//priority
			);
		}
	}
	
	/**
	 * Display function for the metabox
	 * 
	 * @param Object $post WordPress post object
	 * @return void No return value
	 */
	public static function display_metabox( $post ) {
		if ( ! self::isInit() ) //Function can not be called before init
			return;
		
		wp_nonce_field( plugin_basename( __FILE__ ), self::METABOX_FORM_NONCENAME );
		$post_aliases = get_post_meta( $post->ID, WPP_Content_Alias::POSTMETA_CONTENT_ALIAS, false );
		// Start HTML for meta boxes ?>
		<table id="wppca-alias-list" width="100%">
		<thead><tr>
				<th width="90%">Aliases</th>
				<th width="2%"><button class="button button-primary wppca-add-row" style="font-weight: normal;">+</button></th>
		</tr></thead>
		<tbody>
		<?php if ( empty( $post_aliases ) ) : ?>
		<tr>
			<td><input type="text" class="widefat" name="<?php echo self::METABOX_FORM_CONTENT_ALIASES; ?>[]" /></td>
			<td><a class="button wppca-remove-row" href="#">-</a></td>
		</tr>
		<?php else : ?>
			<?php foreach( (array) $post_aliases as $post_alias ) : ?>
		<tr>
			<td><input type="text" class="widefat" name="<?php echo self::METABOX_FORM_CONTENT_ALIASES; ?>[]" value="<?php echo $post_alias; ?>" readonly/></td>
			<td><a class="button wppca-remove-row" href="#">-</a></td>
		</tr>
			<?php endforeach; ?>
		<?php endif; ?>
		<tr id="wppca-empty-row" class="empty-row screen-reader-text">
			<td><input type="text" class="widefat" name="<?php echo self::METABOX_FORM_CONTENT_ALIASES; ?>[]" /></td>
			<td><button class="button wppca-remove-row">-</button></td>
		</tr>
		</tbody>
		<tfoot><tr id="wppca-bottom-row">
				<td></td>
				<td><button class="button button-primary wppca-add-row">+</button></td>
		</tr></tfoot>
		</table>
		<hr />
		<button id="wppca-save-button" class="button button-primary" value="Save" style="float: right;">Save</button><div style="clear: both;"></div>
		<?php // End HTML for meta boxes
	}
}
