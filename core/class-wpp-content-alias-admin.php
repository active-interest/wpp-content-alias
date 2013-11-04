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
	
	/**
	 * 
	 */
	public static function init() {
		if ( self::$_initialized ) 
			return;
		
		if ( ! class_exists( 'WPP_Content_Alias_Admin_Metabox' ) )
			require_once( WPP_CONTENT_ALIAS_PLUGIN_PATH . '/core/class-wpp-content-alias-admin-metabox.php' );
		
		WPP_Content_Alias_Admin_Metabox::init();
		
		if ( ! class_exists( 'WPP_Content_Alias_Admin_Settings' ) )
			require_once( WPP_CONTENT_ALIAS_PLUGIN_PATH . '/core/class-wpp-content-alias-admin-settings.php' );
		
		WPP_Content_Alias_Admin_Settings::init();
		
		self::$_initialized = true;
	}
}
