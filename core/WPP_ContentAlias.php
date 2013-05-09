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
class WPP_ContentAlias {
  private static $_initialized = false;
  private static $_settings = array();
  
  const pluginBaseName    = 'wppContentAlias';
  const metaboxId         = 'wpp-content-alias';
  const metaboxTitle      = 'Content Aliases';
  const metaboxContext    = 'advanced';
  const metaboxPriority   = 'low';
  const metaboxNoncename  = 'wpp_content_alias_noncename';
  const metaboxAliases    = 'wpp_content_alias_aliases';
  const adminPageRoot     = 'wpp-content-alias-';
  const settingsNoncename = 'wpp_content_alias_settings_noncename';
  const postmetaAlias     = '_wpp_content_alias';
  const redirectCode      = 301;
  
  /*
   *  
   */
  public static function init() {
    if(self::$_initialized) return;
    self::$_settings['tracking']=false;
    if (!is_admin()) {
      if(!class_exists('WPP_ContentAliasPublic')) require_once(WPP_CONTENT_ALIAS_PLUGIN_PATH . '/core/WPP_ContentAliasPublic.php');
      WPP_ContentAliasPublic::init();
    } else {
      if(!class_exists('WPP_ContentAliasAdmin')) require_once(WPP_CONTENT_ALIAS_PLUGIN_PATH . '/core/WPP_ContentAliasAdmin.php');
      WPP_ContentAliasAdmin::init();
    }
    self::$_initialized = true;
  }
  
  /*
   *  
   */
  public static function sanitizeUrlPath($urlString) {
    self::init();
    $parsedUrl = parse_url($urlString);
    $parsedPath = $parsedUrl['path'];
    //TODO: add more sanitization here
    if(isset($parsedPath) && !empty($parsedPath)) {
      return $parsedPath;
    } else {
      return '';
    }
  }
  
  /*
   *  
   */
  public static function isTracking() {
    self::init();
    return self::$_settings['tracking'];
  }
  
  /*
   *  
   */
  public static function addAlias($postId, $postAlias) {
    self::init();
    $postAliasPath = self::sanitizeUrlPath($postAlias);
    if(!empty($postAliasPath)) add_post_meta($postId, self::postmetaAlias, $postAliasPath, false);
  }
  
  /*
   *  
   */
  public static function debug($message) {
    if (WP_DEBUG === true) {
        if (is_array($message) || is_object($message)) {
            error_log(print_r($message, true));
        } else {
            error_log($message);
        }
    }
  }
}