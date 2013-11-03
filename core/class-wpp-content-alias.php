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
  
  const pluginBaseName      = 'wppContentAlias';
  const metaboxId           = 'wpp-content-alias';
  const metaboxTitle        = 'Content Aliases';
  const metaboxContext      = 'advanced';
  const metaboxPriority     = 'low';
  const metaboxNoncename    = 'wpp_content_alias_noncename';
  const metaboxAliases      = 'wpp_content_alias_aliases';
  const settingsNoncename   = 'wpp_content_alias_settings_noncename';
  const postmetaAlias       = '_wpp_content_alias';
  const adminPageRoot       = 'wpp-content-alias-';
  const publicRedirectCode  = 301;
  
  // Filter Names
  const filterAddAlias    = 'WPP_Content_Alias_Add_Alias';
  const filterSanitizeUrl = 'WPP_Content_Alias_Sanitize_Url';
  
  // Action Names
  const actionUrlRedirect = 'WPP_Content_Alias_Url_Redirect';
  
  /**
   * Initialization point for the static class
   * 
   * @return void No return value 
   */
  public static function init() {
    if (self::$_initialized) return;
    if (!is_admin()) {
      if (!class_exists('WPP_Content_Alias_Public')) require_once(WPP_CONTENT_ALIAS_PLUGIN_PATH . '/core/class-wpp-content-alias-public.php');
      WPP_Content_Alias_Public::init();
    } else {
      if (!class_exists('WPP_Content_Alias_Admin')) require_once(WPP_CONTENT_ALIAS_PLUGIN_PATH . '/core/class-wpp-content-alias-admin.php');
      WPP_Content_Alias_Admin::init();
    }
    self::$_initialized = true;
  }
  
  /**
   * General use function for sanitizing a url path
   * 
   * @param string $urlString String of the url path to sanitize
   * @return string Returns the sanitized url 
   */
  public static function sanitizeUrlPath($urlString) {
    self::init();
    $urlString = apply_filters(self::filterSanitizeUrl, $urlString);
    //TODO: add more sanitization here
    $parsedUrl = parse_url($urlString);
    $parsedPath = $parsedUrl['path'];
    //If the passedPath is not empty and does not start with / add it to the start
    if(!empty($parsedPath) && strncmp($parsedPath, '/', 1)) $parsedPath = '/' . $parsedPath; 
    if(isset($parsedPath) && !empty($parsedPath)) return $parsedPath;
    else return '';
  }
  
  /**
   * Basic function for adding an alias
   * 
   * @param int $postId The id of the post to add the alias for
   * @param string $postAlias The url path to add as an alias to the post
   * @return void No return value 
   */
  public static function addAlias($postId, $postAlias) {
    self::init();
    $postAlias = apply_filters(self::filterAddAlias, $postAlias);
    $postAliasPath = self::sanitizeUrlPath($postAlias);
    if (!empty($postAliasPath)) add_post_meta($postId, self::postmetaAlias, $postAliasPath, false);
  }
  
  /**
   * Helper function for the debug process
   * 
   * @param string $message The message to send to the error log
   * @return void No return value 
   */
  public static function debug($message) {
    if (WP_DEBUG === true) {
        if (is_array($message) || is_object($message)) error_log(print_r($message, true));
        else error_log($message);
    }
  }
}