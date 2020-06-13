<?php
if ( ! defined( 'ABSPATH' ) ) { exit; } // Exit if accessed directly
/**
 * Moove_XML_License_Manager File Doc Comment
 *
 * @category Moove_XML_License_Manager
 * @package   import-xml-feed
 * @author    Gaspar Nemes
 */

/**
 * Moove_XML_License_Manager Class Doc Comment
 *
 * @category Class
 * @package  Moove_XML_License_Manager
 * @author   Gaspar Nemes
 */
class Moove_XML_License_Manager {
  /**
   * Construct function
   */
  public function __construct() {
    
  }   

  public function validate_license( $license_key = false, $type, $action ) {
    $content        = new Moove_Importer_Content();
    $license_token  = $content->get_license_token();
    $curl           = curl_init();    

    curl_setopt_array( $curl, array(
      CURLOPT_URL             => MOOVE_SHOP_URL."/wp-json/license-manager/v1/validate_licence/?license_key=$license_key&license_token=$license_token&license_type=$type&license_action=$action",
      CURLOPT_RETURNTRANSFER  => true,
      CURLOPT_ENCODING        => "",
      CURLOPT_MAXREDIRS       => 10,
      CURLOPT_TIMEOUT         => 30,
      CURLOPT_HTTP_VERSION    => CURL_HTTP_VERSION_1_1,
      CURLOPT_CUSTOMREQUEST   => "GET",
      CURLOPT_HTTPHEADER      => array(
        "Accept: application/json",
        "Content-Type: application/json",
        "Content-length: 0",
      ),
    ));

    $response = curl_exec( $curl );


    $err = curl_error( $curl );

    curl_close( $curl );

    if ( $err || ! json_decode( $response, true ) ) {
      $error = $err ? $err : $response;
      return array(
        'valid'     => false,
        'message'   => array( 
          'Our Activation Server appears to be temporarily unavailable, please try again later or <a href="mailto:plugins@mooveagency.com" class="'.$type.'_admin_link">contact support</a>.',
        )
      );
    } else {
      return json_decode( $response, true );
    }
  }

  public function get_add_on_plugin_slug() {
    $slug = false;
    if ( function_exists( 'moove_xml_addon_get_plugin_dir' ) ) :
      $slug = str_replace( WP_PLUGIN_URL . '/', '', moove_xml_addon_get_plugin_dir() ) . '/import-xml-feed-addon.php';
    else :
      if ( ! function_exists( 'get_plugins' ) ) :
        require_once ABSPATH . 'wp-admin/includes/plugin.php';
      endif;
      $all_plugins = get_plugins();
      foreach ( $all_plugins as $plugin_slug => $plugin_details ) :
        if ( $plugin_details['TextDomain'] === 'import-xml-feed-addon' && is_plugin_active( $plugin_slug ) ) :
          $slug = $plugin_slug;
        endif;
      endforeach;
      $slug = $slug ? $slug : 'import-xml-feed-addon/import-xml-feed-addon.php';
    endif;
    return $slug;
  }

  public function get_premium_add_on( $license_key = false, $action = 'check' ) {
    $validate_license = $this->validate_license( $license_key, 'xml', $action );
    if ( $validate_license && isset( $validate_license['valid'] ) && $validate_license['valid'] === true ) :
      $plugin_token = isset( $validate_license['data'] ) && isset( $validate_license['data']['download_token'] ) && $validate_license['data']['download_token'] ? $validate_license['data']['download_token'] : false;
      $is_valid_license = true;
      if ( $plugin_token && $action === 'activate' ) :
        $plugin_slug        = $this->get_add_on_plugin_slug();
        add_filter( 'upgrader_source_selection', array( &$this, 'upgrader_source_selection' ), 10, 4 );
        if ( $plugin_slug ) :
          if ( $this->is_plugin_installed( $plugin_slug ) ) {
            $this->upgrade_plugin( $plugin_slug );
            $installed = true;
          } else {
            $installed = $this->install_plugin( $plugin_token );
            $installed = true;
          }
          if ( ! is_wp_error( $installed ) && $installed ) {
            $activate = activate_plugin( $plugin_slug );
          } else {
            // Could not install the new plugin;
          }
        endif;
        remove_filter( 'upgrader_source_selection', array( &$this, 'upgrader_source_selection' ), 10, 4 );
      endif;
    endif;
    return $validate_license;
  }

  /**
   * Rename the plugin folder
   */
  function upgrader_source_selection( $source, $remote_source, $upgrader, $hook_extra = null ) {
      global $wp_filesystem;
      $plugin       = isset( $hook_extra['plugin'] ) ? $hook_extra['plugin'] : false;

      $plugin_slug  = $this->get_add_on_plugin_slug();
      $temp_slug    = basename( trailingslashit( $source ) );
      $plugin_slug  = explode('/', $plugin_slug);
      $plugin_slug  = isset( $plugin_slug[0] ) && $plugin_slug[0] ? $plugin_slug[0] : 'import-xml-feed-addon';

      if ( $temp_slug !== $plugin_slug ) :
          $new_source = trailingslashit( $remote_source );
          $new_source = str_replace( $temp_slug , $plugin_slug, $new_source );
          $wp_filesystem->move( $source, $new_source );
          return trailingslashit( $new_source );
      endif;
      return $source;
  }

  public function premium_deactivate( $license_key = false ) {
    $validate_license = $this->validate_license( $license_key, 'xml', 'deactivate' );
    if ( $validate_license && isset( $validate_license['valid'] ) && $validate_license['valid'] === true ) :
      $plugin_slug    = $this->get_add_on_plugin_slug();
      if ( $plugin_slug ) :
        if ( $this->is_plugin_installed( $plugin_slug ) ) :
          deactivate_plugins( plugin_basename( $plugin_slug ) );
          $deactivated = true;
        endif; 
      endif;
    endif;
    return $validate_license;
  }

  public function is_plugin_installed( $slug = false ) {
    if ( function_exists( 'moove_xml_addon_get_plugin_dir' ) ) :
      return true;
    endif;

    if ( $slug ) :

      if ( ! function_exists( 'get_plugins' ) ) {
        require_once ABSPATH . 'wp-admin/includes/plugin.php';
      }
      $all_plugins = get_plugins();
       
      if ( ! empty( $all_plugins[ $slug ] ) ) :
        return true;
      else :
        return false;
      endif;
    endif;
    return false;
  }

  public function install_plugin( $plugin_token ) {
    include_once ABSPATH . 'wp-admin/includes/class-wp-upgrader.php';
    wp_cache_flush();
    $upgrader = new Plugin_Upgrader();
    $installed = $upgrader->install( $plugin_token );
   
    return $installed;
  }

  public function upgrade_plugin( $plugin_slug ) {
    include_once ABSPATH . 'wp-admin/includes/class-wp-upgrader.php';
    wp_cache_flush();
   
    $upgrader = new Plugin_Upgrader();
    $upgraded = $upgrader->upgrade( $plugin_slug );
   
    return $upgraded;
  }


}
new Moove_XML_License_Manager();
