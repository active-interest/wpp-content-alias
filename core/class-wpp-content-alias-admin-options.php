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
	
	/** Used to store the option tabs */
	private static $_option_tabs = NULL;
	
	const STYLE_BASE            = 'wppca_options';
	const SCRIPT_BASE           = 'wppca_options';
	const ADMIN_PAGE            = 'wppca_options';
	const DEFAULT_TAB           = 'primary';
	const OPTION_FORM_TAB       = 'tab';
	const OPTION_FORM_NONCENAME = 'wppca_options_noncename';
	
	/**
	 * 
	 */
	public static function init() {
		if ( self::$_initialized ) 
			return;
		
		self::$_option_tabs = array(
			array( //Primary Tab
				't' => 'Options', //$title
				'i' => 'primary', //$id
				'b' => array( __class__, 'build_tab_primary' ), //$build_action
				's' => array( __class__, 'save_tab_primary' ), //$save_ation
				'p' => 10, //$priority
			),
			array( //Tracking Tab
				't' => 'Tracking', //$title
				'i' => 'tracking', //$id
				'b' => array( __class__, 'build_tab_tracking' ), //$build_action
				's' => array( __class__, 'save_tab_tracking' ), //$save_ation
				'p' => 10, //$priority
			),
		);
		
		add_filter( 'plugin_action_links_' . WPP_CONTENT_ALIAS_FILTER_FILE, array(__CLASS__, 'plugin_action_links'), 10, 1 );
		add_action( 'admin_enqueue_scripts', array( __CLASS__, 'admin_enqueue_scripts' ) );
		add_action( 'admin_menu', array( __CLASS__, 'admin_menu' ) );
		
		foreach( self::$_option_tabs as $tab ) {
			add_action( WPP_Content_Alias::ACTION_OPTION_BUILD_TAB . '-' . $tab['i'], $tab['b'], $tab['p'] ); //Build Hook
			add_action( WPP_Content_Alias::ACTION_OPTION_SAVE_TAB . '-' . $tab['i'], $tab['s'], $tab['p'] ); //Save Hook
		}
		
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
			array( __CLASS__, 'build_options_page' )
		);
	}
	
	/**
	 * 
	 */
	public static function save_options_page( $selected_tab ) {
		//TODO: remove this, its only for debuging
		print( '<div id="message" class="updated settings-error"><p><strong>Settings saved for ' . $selected_tab . '</strong></p></div>' . "\n" );
		do_action( WPP_Content_Alias::ACTION_OPTION_SAVE_TAB );
		do_action( WPP_Content_Alias::ACTION_OPTION_SAVE_TAB . '-' . $selected_tab );
	}
	
	/**
	 * 
	 */
	public static function build_options_page() {
		$active_tab = filter_input( INPUT_GET, self::OPTION_FORM_TAB, FILTER_SANITIZE_STRING );
		if ( empty( $active_tab ) )
			$active_tab = self::DEFAULT_TAB;
		
		if ( wp_verify_nonce( filter_input( INPUT_POST, self::OPTION_FORM_NONCENAME, FILTER_SANITIZE_STRING ), plugin_basename( __FILE__ ) ) )
			self::save_options_page( $active_tab );
		
		// Start HTML ?>
		<div class="wrap">
			<h2>Content Alias</h2>
			<?php screen_icon(); ?>
			<h2 class="nav-tab-wrapper">
			<?php foreach( self::$_option_tabs as $tab ) : ?>	
				<a href="<?php echo admin_url( 'options-general.php?page=' . self::ADMIN_PAGE . '&tab=' . $tab['i'] ); ?>" class="nav-tab <?php echo $active_tab === $tab['i'] ? 'nav-tab-active' : ''; ?>"><?php echo $tab['t']; ?></a>
			<?php endforeach; ?>
			</h2>
			<form method="post" action="">
			<?php wp_nonce_field( plugin_basename( __FILE__ ), self::OPTION_FORM_NONCENAME ); ?>
			<?php do_action( WPP_Content_Alias::ACTION_OPTION_BUILD_TAB ) ?>
			<?php do_action( WPP_Content_Alias::ACTION_OPTION_BUILD_TAB . '-' . $active_tab ) ?>
			<?PHP submit_button(); ?>
			</form>
		</div>
		<?php // End HTML
	}
	
	/**
	 * 
	 */
	public static function build_tab_primary() {
		// Start HTML ?>
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
		<?php // End HTML
	}
	
	/**
	 * 
	 */
	public static function save_tab_primary() {
		
	}
	
	/**
	 * 
	 */
	public static function build_tab_tracking() {
		if ( ! class_exists( 'WPP_Content_Alias_List_Table' ) )
			require_once( WPP_CONTENT_ALIAS_PLUGIN_PATH . '/core/class-wpp-content-alias-list-table.php');
		
		$stats_table = new WPP_Content_Alias_List_Table();
		$stats_table->set_columns( array( 'url' => 'URL Paths', 'count' => 'Hits', 'last' => 'Last used', ) );
		$stats_table->set_data( array( array( 'url'=>'Coming Soon...', ) ) );
		$stats_table->prepare_items();
		
		// Start HTML ?>
			<h3>Tracking Settings</h3>
			<table class="form-table">
				<tr valign="top">
					<th scope="row">Enabled</th>
					<td>Coming Soon...</td>
				</tr>
			</table>
			<hr />
			<h3>Tracking Stats</h3>
			<p>Top 10 hit URLs...</p>
			<?php $stats_table->display(); ?>
		<?php // End HTML
	}
	
	/**
	 * 
	 */
	public static function save_tab_tracking() {
		
	}
}
