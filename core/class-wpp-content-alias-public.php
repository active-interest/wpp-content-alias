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
class WPP_Content_Alias_Public {
	/** Used to keep the init state of the class */
	private static $_initialized = false;

	/**
	 * Initialization point for the static class
	 * 
	 * @return void No return value
	 */
	public static function init() {
		if ( self::$_initialized )
			return;
		
		add_action( 'template_redirect', array( __CLASS__, 'template_redirect' ) );
		self::$_initialized = true;
	}
	
	/**
	 * Primary function for checking to see if a redirect is present
	 * 
	 * @return void No return value
	 */
	public static function template_redirect() {
		if ( is_404() ) {
			$requestPath = WPP_Content_Alias::sanitizeUrlPath( filter_input( INPUT_SERVER, 'REQUEST_URI', FILTER_SANITIZE_URL ) );
			$findPost = array(
				'post_type'					=> 'any',
				'numberposts'				=> '1',
				'suppress_filters'	=> true,
				'fields'						=> 'ids',
				'meta_query'				=> array(
					array(
						'key'			=> WPP_Content_Alias::postmetaAlias,
						'value'		=> $requestPath,
						'compare' => '=',
					),
				),
			);
			$wpQuery = new WP_Query( $findPost );
			if ( $wpQuery->have_posts() ) {
				$wpQuery->next_post();
				$redirectUrl = get_permalink( $wpQuery->post );
				if ( ! empty($redirectUrl) ) {
					do_action( WPP_Content_Alias::actionUrlRedirect, array( 'requestPath' => $requestPath, 'post' => $wpQuery->post) );
					wp_redirect( $redirectUrl, WPP_Content_Alias::publicRedirectCode );
					exit();
				}
			}
		}
	}
}
