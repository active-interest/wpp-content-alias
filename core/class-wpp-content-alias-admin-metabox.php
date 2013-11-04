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
	
	/**
	 * 
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
	 * Test to see if we are in a valid location
	 * 
	 * This function is used to test to see if the current location is a valid one for the metabox
	 * 
	 * @return boolean Returns true if we are in a valid location
	 */
	public static function valid_location() {
		//TODO: add the business logic for doing the location checking
		return true;
	}
	
	/**
	 * 
	 */
	public static function admin_enqueue_scripts() {
		if ( self::valid_location() ) {
			wp_register_style(
				WPP_Content_Alias::PLUGIN_BASE_NAME . 'AdminMetaboxCss', 
				plugins_url( 'css/wpp-content-alias-admin-metabox.css', WPP_CONTENT_ALIAS_PLUGIN_FILE ),
				array(),
				'20130501'
				);
			wp_enqueue_style( WPP_Content_Alias::PLUGIN_BASE_NAME . 'AdminMetaboxCss' );

			wp_register_script(
				WPP_Content_Alias::PLUGIN_BASE_NAME . 'AdminMetaboxJs', 
				plugins_url( 'js/wpp-content-alias-admin-metabox.js', WPP_CONTENT_ALIAS_PLUGIN_FILE ),
				array( 'jquery', 'jquery-ui-core' ), 
				'20130501'
				);
			wp_enqueue_script( WPP_Content_Alias::PLUGIN_BASE_NAME . 'AdminMetaboxJs' );
		}
	}
	
	/**
	 * 
	 */
	public static function save_post( $post_id ) {
		//if(true) return; // Disable the below logic because we are not finished with the interface yet
		
		if ( ! current_user_can( 'edit_page', $post_id ) ) //Check users permissions
			return;
		
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) //Check skip if we are only auto saving
			return;
		
		if ( wp_is_post_revision( $post_id ) ) //Check to make sure it is not a revision
			return;
		
		if ( ! isset( $_POST[ WPP_Content_Alias::METABOX_FORM_NONCENAME ] ) || ! wp_verify_nonce( $_POST[ WPP_Content_Alias::METABOX_FORM_NONCENAME ], plugin_basename( __FILE__ ) ) ) //Verify the form
			return;
		
		delete_post_meta( $post_id, WPP_Content_Alias::POSTMETA_CONTENT_ALIAS ); //Start off by deleting any excisting values
		if ( isset( $_POST[ WPP_Content_Alias::METABOX_FORM_CONTENT_ALIASES ] ) && ! empty( $_POST[ WPP_Content_Alias::METABOX_FORM_CONTENT_ALIASES ] ) ) {
			$post_aliases = $_POST[ WPP_Content_Alias::METABOX_FORM_CONTENT_ALIASES ];
			if ( is_array( $post_aliases ) ) {
				foreach( (array) $post_aliases as $post_alias ) {
					WPP_Content_Alias::add_alias( $post_id, $post_alias );
				}
			}
		}
	}
	
	/**
	 * 
	 */
	public static function add_meta_boxes() {
		//TODO: add admin section for all or selected post types
		$post_types = get_post_types( '','names' );
		foreach( $post_types as $post_type ) {
			add_meta_box(
				WPP_Content_Alias::METABOX_ID,					//$id
				__( WPP_Content_Alias::METABOX_TITLE ),	//$title
				array( __class__, 'display_metabox' ),	//$callback
				$post_type,															//$post_type
				WPP_Content_Alias::METABOX_CONTEXT,			//$context
				WPP_Content_Alias::METABOX_PRIORITY			//priority
			);
		}
	}
	
	/**
	 * 
	 */
	public static function display_metabox( $post ) {
		wp_nonce_field( plugin_basename( __FILE__ ), WPP_Content_Alias::METABOX_FORM_NONCENAME );
		$post_aliases = get_post_meta( $post->ID, WPP_Content_Alias::POSTMETA_CONTENT_ALIAS, false );
		// Start HTML for meta boxes ?>
		<script type="text/javascript">
			/* <![CDATA[ */
			jQuery(document).ready(function($){
				$('.wppca-add-row').on('click', function() {
					console.log('something clicked');
					var row = $('#wppca-empty-row').clone(true);
					row.removeAttr('id');
					row.removeClass('empty-row screen-reader-text');
					row.insertBefore('#wppca-empty-row');
					return false;
				});
				$('.wppca-remove-row').on('click', function() {
					var agree = confirm("Are you sure you want to remove the alias?");
					if(agree) {
						$(this).parents('tr').remove();
					}
					return false;
				});
				$('#wppca-save-button').click(function(e) {
					e.preventDefault();
					$('#publish').click();
				});
			});
			/* ]]> */
		</script>
		<table id="wppca-alias-list" width="100%">
		<thead>
			<tr>
				<th width="90%">Aliases</th>
				<th width="2%"><button class="button button-primary wppca-add-row" style="font-weight: normal;">+</button></th>
			</tr>
		</thead>
		<tbody>
		<?php // Pause HTML
		if ( empty( $post_aliases ) ) {
		// Resume HTML ?>
		<tr>
			<td><input type="text" class="widefat" name="<?php echo WPP_Content_Alias::METABOX_FORM_CONTENT_ALIASES; ?>[]" /></td>
			<td><a class="button wppca-remove-row" href="#">-</a></td>
		</tr>
		<?php // Pause HTML
		} else {
			foreach( $post_aliases as $post_alias ) {
		// Resume HTML ?>
		<tr>
			<td><input type="text" class="widefat" name="<?php echo WPP_Content_Alias::METABOX_FORM_CONTENT_ALIASES; ?>[]" value="<?php echo $post_alias; ?>" readonly/></td>
			<td><a class="button wppca-remove-row" href="#">-</a></td>
		</tr>
		<?php // Pause HTML
			}
		}
		// Resume HTML ?>
		<tr id="wppca-empty-row" class="empty-row screen-reader-text">
			<td><input type="text" class="widefat" name="<?php echo WPP_Content_Alias::METABOX_FORM_CONTENT_ALIASES; ?>[]" /></td>
			<td><button class="button wppca-remove-row">-</button></td>
		</tr>
		</tbody>
		<tfoot>
			<tr id="wppca-bottom-row">
				<td></td>
				<td><button class="button button-primary wppca-add-row">+</button></td>
			</tr>
		</tfoot>
		</table>
		<hr />
		<button id="wppca-save-button" class="button button-primary" value="Save" style="float: right;">Save</button>
		<div style="clear: both;"></div>
		<?php // End HTML for meta boxes
	}
}
