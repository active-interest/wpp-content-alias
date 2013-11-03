<?php
/**
 * Plugin Name: WPP Redirect with Content Alias
 * Plugin URI: http://wppoets.com/plugins/content-alias.html
 * Description: Adds content alias 301 redirect functionality to all the WordPress content types for your site. This helps to reduce 404 errors when moving content and or migrated to WordPress.
 * Version: 0.9
 * Author: WP Poets <plugins@wppoets.com>
 * Author URI: http://wppoets.com
 * License: GPLv2 (dual-licensed)
 * License URI: http://www.gnu.org/licenses/gpl-2.0.html
 */
/**  
 * Copyright (c) 2013, WP Poets and/or its affiliates <plugins@wppoets.com>
 * Portions of this distribution are copyrighted by:
 *   Copyright (c) 2013 Michael Stutz <michaeljstutz@gmail.com>
 * All rights reserved.
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
if (!defined('ABSPATH')) die(); // We should not be loading this outside of wordpress
if (!defined('WPP_CONTENT_ALIAS_VERSION_NUM')) define('WPP_CONTENT_ALIAS_VERSION_NUM', '0.9');
if (!defined('WPP_CONTENT_ALIAS_PLUGIN_FILE')) define('WPP_CONTENT_ALIAS_PLUGIN_FILE', __FILE__);
if (!defined('WPP_CONTENT_ALIAS_PLUGIN_PATH')) define('WPP_CONTENT_ALIAS_PLUGIN_PATH', dirname(__FILE__));
if (!defined('WPP_CONTENT_ALIAS_FILTER_FILE')) define('WPP_CONTENT_ALIAS_FILTER_FILE', 'wpp-content-alias/wpp-content-alias.php');

if(!class_exists('WPP_Content_Alias')) require_once(WPP_CONTENT_ALIAS_PLUGIN_PATH . '/core/class-wpp-content-alias.php');
WPP_Content_Alias::init();