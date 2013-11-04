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
class WPP_Content_Alias_Admin_Options {
	/** Used to keep the init state of the class */
	private static $_initialized = false;
	
	/** Used to store the option page name */
	private static $_option_page = '';
	
	const STYLE_BASE   = 'wppca_settings';
	const SCRIPT_BASE  = 'wppca_settings';
	const ADMIN_PAGE   = 'wppca_settings';
	const DEFAULT_TAB  = 'primary';
	
	/**
	 * 
	 */
	public static function init() {
		if ( self::$_initialized ) 
			return;
		
		add_filter( 'plugin_action_links_' . WPP_CONTENT_ALIAS_FILTER_FILE, array(__CLASS__, 'plugin_action_links'), 10, 1 );
		add_action( 'admin_enqueue_scripts', array( __CLASS__, 'admin_enqueue_scripts' ) );
		add_action( 'admin_menu', array( __CLASS__, 'admin_menu' ) );
		
		self::$_initialized = true;
	}
	
	/**
	 * Test to see if we are in a valid location
	 * 
	 * This function is used to test to see if the current location is a valid one for the settings page
	 * 
	 * @return boolean Returns true if we are in a valid location
	 */
	public static function valid_location() {
		$screen = get_current_screen();
		
		if ( $screen->id === self::$_option_page )
			return true;
		else 
			return false;
		
	}
	
	/**
	 * 
	 */
	public static function plugin_action_links( $links ) {
		$before_links = array();
		$before_links['settings'] = sprintf( '<a href="%1$s">%2$s</a>', admin_url( 'options-general.php?page=' . self::ADMIN_PAGE . '&tab=' . self::DEFAULT_TAB ), 'Settings' );
		$after_links = array();
		return array_merge( $before_links, $links, $after_links );
	}
	
	/**
	 * 
	 */
	public static function admin_enqueue_scripts() {
		if ( self::valid_location() ) {
			//Register and Enqueue Style
			wp_register_style(
				self::STYLE_BASE, 
				plugins_url( 'css/wpp-content-alias-admin.css', WPP_CONTENT_ALIAS_PLUGIN_FILE ),
				array(),
				WPP_CONTENT_ALIAS_VERSION_NUM . '.' . WPP_CONTENT_ALIAS_BUILD_NUM
				);
			wp_enqueue_style( self::STYLE_BASE );
			
			//Register and Enqueue Script
			wp_register_script(
				self::SCRIPT_BASE, 
				plugins_url( 'js/wpp-content-alias-admin.js', WPP_CONTENT_ALIAS_PLUGIN_FILE ),
				array( 'jquery', 'jquery-ui-core' ), 
				WPP_CONTENT_ALIAS_VERSION_NUM . '.' . WPP_CONTENT_ALIAS_BUILD_NUM
				);
			wp_enqueue_script( self::SCRIPT_BASE );
		}
	}
	
	/**
	 * 
	 */
	public static function admin_menu() {
		self:: $_option_page = add_options_page(
			'Settings',
			'Content Alias',
			'manage_options',
			self::ADMIN_PAGE,
			array( __CLASS__, 'display_settings_page' )
		);
	}
	
	/**
	 * 
	 */
	public static function display_settings_page() {
		if ( isset( $_GET['tab'] ) )
			$current_tab = $_GET['tab'];
		else
			$current_tab = 'primary';
		
		if ( isset( $_POST[ WPP_Content_Alias::SETTINGS_FORM_NONCENAME ] ) && wp_verify_nonce( $_POST[ WPP_Content_Alias::SETTINGS_FORM_NONCENAME ], plugin_basename( __FILE__ ) ) ) {
			//TODO: add the save logic here
			print( '<div id="message" class="updated settings-error"><p><strong>Settings saved</strong></p></div>' . "\n" );
		}
		
		// Start HTML ?>
		<div class="wrap">
			<h2>Content Alias</h2>
			<?php screen_icon(); ?>
			<h2 class="nav-tab-wrapper">
				<a href="<?php echo admin_url( 'options-general.php?page=' . self::ADMIN_PAGE . '&tab=primary' ); ?>" class="nav-tab <?php echo $current_tab == 'primary' ? 'nav-tab-active' : ''; ?>">Options</a>
				<a href="<?php echo admin_url( 'options-general.php?page=' . self::ADMIN_PAGE . '&tab=tracking' ); ?>" class="nav-tab <?php echo $current_tab == 'tracking' ? 'nav-tab-active' : ''; ?>">Tracking</a>
			</h2>
			<form method="post" action="">
			<?php if ( $current_tab == 'primary' ) { ?>
				<h3>Primary Settings</h3>
				<table class="form-table">
					<tr valign="top">
						<th scope="row">Content Types</th>
						<td>Coming Soon...</td>
					</tr>
					<tr valign="top">
						<th scope="row">Type of Redirect</th>
						<td>Coming Soon...</td>
					</tr>
				</table>
			<?php
				wp_nonce_field( plugin_basename( __FILE__ ), WPP_Content_Alias::SETTINGS_FORM_NONCENAME );
				submit_button();
			?>
			<?php } elseif( $current_tab == 'tracking' ) { ?>
				<h3>Tracking Settings</h3>
				<table class="form-table">
					<tr valign="top">
						<th scope="row">Enabled</th>
						<td>Coming Soon...</td>
					</tr>
				</table>
			<?php
				wp_nonce_field( plugin_basename( __FILE__ ), WPP_Content_Alias::SETTINGS_FORM_NONCENAME );
				submit_button();
				
				if ( ! class_exists( 'WPP_Content_Alias_List_Table' ) )
					require_once( WPP_CONTENT_ALIAS_PLUGIN_PATH . '/core/class-wpp-content-alias-list-table.php');
				
				$stats_table = new WPP_Content_Alias_List_Table();
				$stats_table->set_columns( array( 'url' => 'URL Paths', 'count' => 'Hits', 'last' => 'Last used', ) );
				$stats_table->set_data( array( array( 'url'=>'Coming Soon...', ) ) );
				$stats_table->prepare_items();
			?>
			</form>
			<hr />
			<h3>Tracking Stats</h3>
			<p>Top 10 hit URLs...</p>
			<?php $stats_table->display(); ?>
			<?php } ?>
		</div>
		<?php // End HTML
	}
}
