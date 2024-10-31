<?php
/**
Plugin Name: oik-nivo-slider
Requires Plugins: oik
Plugin URI: https://oik-plugins.com/oik-plugins/oik-nivo-slider/
Description: [nivo] shortcode for the Nivo slider using oik
Version: 1.16.4
Author: bobbingwide
Author URI: https://bobbingwide.com/about-bobbing-wide
Text Domain: oik-nivo-slider
Domain Path: /languages/
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

    Copyright 2012-2024 Bobbing Wide (email : herb@bobbingwide.com )

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License version 2,
    as published by the Free Software Foundation.

    You may NOT assume that you can use any other version of the GPL.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    The license for this software can likely be found here:
    http://www.gnu.org/licenses/gpl-2.0.html

*/

/**
 * Implement "oik_loaded" action for the oik-nivo-slider 
 */
function oik_nivo_add_shortcodes() {
  bw_add_shortcode( "nivo", "bw_nivo_slider", oik_path( "nivo.php", "oik-nivo-slider" ), false ); 
}

/**
 * Implement "admin_menu" action for the oik-nivo-slider 
 */
function oik_nivo_admin_menu() {
  require_once( "admin/oik-nivo-slider.php" );
  oik_nivo_lazy_admin_menu();
}

/**
 * Implements "admin_notices" action for oik-nivo-slider 
 *
 * This code will produce a message when oik-nivo-slider is activated but oik isn't.
 *
 * Version | Dependency
 * ------- | --------------
 * v1.13   | oik v2.1
 * v1.14.0 | oik v2.4
 * v1.14.2 | oik v3.0.0
 * v1.15.0 | oik v3.2.1
 * v1.15.2 | oik v3.2.8
 *
 */ 
function oik_nivo_activation() {
  static $plugin_basename = null;
  if ( !$plugin_basename ) {
    $plugin_basename = plugin_basename(__FILE__);
    add_action( "after_plugin_row_oik-nivo-slider/oik-nivo-slider.php" , "oik_nivo_activation" );   
    if ( !function_exists( "oik_plugin_lazy_activation" ) ) { 
      require_once( "admin/oik-activation.php" );
    }
  }  
  $depends = "oik:3.2.8";
  oik_plugin_lazy_activation( __FILE__, $depends, "oik_plugin_plugin_inactive" );
}

/**
 * function to invoke when oik-nivo-slider loaded 
 */                  
function oik_nivo_loaded() {
  add_action( "admin_notices", "oik_nivo_activation", 12 );
  add_action( "oik_loaded", "oik_nivo_add_shortcodes" );
  add_action( "oik_admin_menu", "oik_nivo_admin_menu" );
}

oik_nivo_loaded();