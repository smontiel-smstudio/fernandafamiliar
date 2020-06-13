<?php
/**
 *  Contributors: mooveagency
 *  Stable tag:
 *  Plugin Name: Import XML feed
 *  Plugin URI: http://www.mooveagency.com
 *  Description: This plugin adds the ability to import content from an external XML/RSS file, or from an uploaded XML/RSS.
 *  Version: 2.0.1
 *  Author: Moove Agency
 *  Author URI: http://www.mooveagency.com
 *  License: GPLv2
 *  Text Domain: import-xml-feed
 */
define( 'MOOVE_XML_VERSION', '2.0.1' );

if ( ! defined( 'MOOVE_SHOP_URL' ) ) :
  define( 'MOOVE_SHOP_URL', 'https://shop.mooveagency.com' );
endif;

register_activation_hook( __FILE__ , 'moove_importer_activate' );
register_deactivation_hook( __FILE__ , 'moove_importer_deactivate' );

/**
 * Functions on plugin activation, create relevant pages and defaults for settings page.
 */
function moove_importer_activate() {

}

/**
 * Function on plugin deactivation. It removes the pages created before.
 */
function moove_importer_deactivate() {

}

/**
 * Star rating on the plugin listing page
 */
if ( ! function_exists('import_xml_add_plugin_meta_links') ) {
function import_xml_add_plugin_meta_links($meta_fields, $file) {
  if ( plugin_basename(__FILE__) == $file ) :
    $plugin_url = "https://wordpress.org/support/plugin/import-xml-feed/reviews/?rate=5#new-post";
    $meta_fields[] = "<a href='" . esc_url($plugin_url) ."' target='_blank' title='" . esc_html__('Rate', 'import-xml-feed') . "'>
          <i class='import-xml-star-rating'>"
      . "<svg xmlns='http://www.w3.org/2000/svg' width='15' height='15' viewBox='0 0 24 24' fill='none' stroke='currentColor' stroke-width='2' stroke-linecap='round' stroke-linejoin='round' class='feather feather-star'><polygon points='12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2'/></svg>"
      . "<svg xmlns='http://www.w3.org/2000/svg' width='15' height='15' viewBox='0 0 24 24' fill='none' stroke='currentColor' stroke-width='2' stroke-linecap='round' stroke-linejoin='round' class='feather feather-star'><polygon points='12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2'/></svg>"
      . "<svg xmlns='http://www.w3.org/2000/svg' width='15' height='15' viewBox='0 0 24 24' fill='none' stroke='currentColor' stroke-width='2' stroke-linecap='round' stroke-linejoin='round' class='feather feather-star'><polygon points='12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2'/></svg>"
      . "<svg xmlns='http://www.w3.org/2000/svg' width='15' height='15' viewBox='0 0 24 24' fill='none' stroke='currentColor' stroke-width='2' stroke-linecap='round' stroke-linejoin='round' class='feather feather-star'><polygon points='12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2'/></svg>"
      . "<svg xmlns='http://www.w3.org/2000/svg' width='15' height='15' viewBox='0 0 24 24' fill='none' stroke='currentColor' stroke-width='2' stroke-linecap='round' stroke-linejoin='round' class='feather feather-star'><polygon points='12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2'/></svg>"
      . "</i></a>";      
     
  endif;
  return $meta_fields;
  }
}
add_filter('plugin_row_meta' , 'import_xml_add_plugin_meta_links', 10, 2);

include_once( dirname( __FILE__ ).DIRECTORY_SEPARATOR.'moove-view.php' );
include_once( dirname( __FILE__ ).DIRECTORY_SEPARATOR.'moove-content.php' );
include_once( dirname( __FILE__ ).DIRECTORY_SEPARATOR.'moove-options.php' );
include_once( dirname( __FILE__ ).DIRECTORY_SEPARATOR.'moove-controller.php' );
include_once( dirname( __FILE__ ).DIRECTORY_SEPARATOR.'moove-actions.php' );
include_once( dirname( __FILE__ ).DIRECTORY_SEPARATOR.'moove-shortcodes.php' );
