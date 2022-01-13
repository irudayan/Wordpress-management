<?php
/*
 * Plugin Name: Colissimo Delivery Integration 
 * Description: Easy Colissimo Services with WooCommerce.
 * Version: 3.7.18
 * Author: Halyra
 *
 * Text Domain: colissimo-delivery-integration
 * Domain Path: /languages/
 *
 * Requires At Least: 5.2.0
 * Tested Up To: 5.7.2
 * WC requires at least: 3.6.5
 * WC tested up to: 5.3.0
 * Requires PHP: 7.0
 *
 * License: GPLv3
 * License URI: https://www.gnu.org/licenses/gpl-3.0.html
 * 
 * Copyright: (c) 2016  Halyra 
 
 This program is free software; you can redistribute it and/or modify
 it under the terms of the GNU General Public License, version 3, as 
 published by the Free Software Foundation.
 
 This program is distributed in the hope that it will be useful,
 but WITHOUT ANY WARRANTY; without even the implied warranty of
 MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 GNU General Public License for more details.
 
 You should have received a copy of the GNU General Public License
 along with this program; if not, write to the Free Software
 Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
 */
__( 'Colissimo Delivery Integration', 'colissimo-delivery-integration' ) ;
__( 'Easy Colissimo Services with WooCommerce.', 'colissimo-delivery-integration' ) ;

/**
 * This file is part of the Colissimo Delivery Integration plugin.
 * (c) Halyra
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

if (!defined('ABSPATH')) exit;
require_once( ABSPATH . 'wp-admin/includes/plugin.php' );


/**
 * Display admin notices
 */
function cdi_general_admin_notice(){
    global $noticestodisplay ;
      if ($noticestodisplay) {
        foreach ($noticestodisplay as $noticetodisplay){
          echo $noticetodisplay ;
        }
        $noticestodisplay = null ;
      }
}
add_action('admin_notices', 'cdi_general_admin_notice');


/**
 * Check dependency
 */
global $noticestodisplay ;
function cdi_in_array_any($needles, $haystack){
  $return = array_intersect($needles, $haystack);
  return $return ;
}
function cdi_in_array_all($needles, $haystack){
  $return = array_diff($needles, $haystack);
  return $return ;
}
$active_plugins = (array) get_option('active_plugins', array());
if (is_multisite()) {
  $active_plugins = array_merge($active_plugins, get_site_option('active_sitewide_plugins', array()));
}
$must_include = array('woocommerce/woocommerce.php') ;
$must_exclude = array('colissimo-shipping-methods-for-woocommerce/index.php', 'cdi/cdi.php') ; 
$obligplugins = cdi_in_array_all($must_include, $active_plugins);
$excluplugins = cdi_in_array_any($must_exclude, $active_plugins);
$returnprocess = true ;
if (count($obligplugins) !== 0 ) {
  $messobligplugin = '<div class="notice notice-error is-dismissible"> <p><strong>Error : Colissimo Delivery integration</strong> requires that plugin(s) <strong> ' . implode ( " ; " , $obligplugins ) . ' </strong> to be active!. For your security Colissimo Delivery integration has been stopped. Please review the plugins you need active. </p></div>' ;
  $noticestodisplay[] = $messobligplugin ;
  $returnprocess = false ;
  deactivate_plugins ( plugin_basename( __FILE__ ) ) ;
}
if (count($excluplugins) !== 0 ) {
  $messexcluplugin = '<div class="notice notice-error is-dismissible"> <p><strong>Error : Colissimo Delivery integration</strong> notices a conflict with other(s) active plugin(s) : <strong> ' . implode ( " ; " , $excluplugins ) . ' </strong>. Colissimo Delivery integration must be activated alone !. For your security Colissimo Delivery Integration has been stopped. Please review the plugins you need active. </p></div>' ;
  $noticestodisplay[] = $messexcluplugin ;
  $returnprocess = false ;
  deactivate_plugins ( plugin_basename( __FILE__ ) ) ;
}
if ($returnprocess) {


/**
 * Add the styles
 */
function cdi_add_styles_css() {
  wp_enqueue_style( 'colissimo-delivery-integration-admin', plugin_dir_url( __FILE__ ) . 'css/admincdi.css' );
}
add_action( 'admin_enqueue_scripts', 'cdi_add_styles_css' );


/**
 * Plugin Activation
 */
function cdi_install($networkwide) {
  global $wpdb;
  if (function_exists('is_multisite') && is_multisite()) {
    // check if it is a network activation - if so, run the activation function for each blog id
    if ($networkwide) {
      $old_blog = $wpdb->blogid;
      // Get all blog ids
      $blogids = $wpdb->get_col("SELECT blog_id FROM $wpdb->blogs");
      foreach ($blogids as $blog_id) {
        switch_to_blog($blog_id);
        cdi_install_db();
      }
      switch_to_blog($old_blog);
      return;
    }   
  } 
  cdi_install_db();      
}
register_activation_hook(__FILE__, 'cdi_install');


/**
 * Activation New blog
 */      
function cdi_new_blog($blog_id, $user_id, $domain, $path, $site_id, $meta ) {
  global $wpdb;
  $old_blog = $wpdb->blogid;
  switch_to_blog($blog_id);
  cdi_install_db();
  switch_to_blog($old_blog);
}
add_action( 'wpmu_new_blog', 'cdi_new_blog', 10, 6);  


/**
 * Plugin Deactivation
 */
function cdi_uninstall() {
  // Nothing done here - See uninstall.php file
  global $wpdb;
  // Remove capability cdi_gateway
  $roles = get_editable_roles();
  foreach ($GLOBALS['wp_roles']->role_objects as $key => $role) {
    if (isset($roles[$key]) && $role->has_cap('cdi_gateway')) {
      $role->remove_cap('cdi_gateway');
    }
  }
}
register_deactivation_hook(__FILE__, 'cdi_uninstall');


/**
 * DB install
 */
function cdi_install_db() {
  global $wpdb;
  $table     = $wpdb->prefix . "cdi";
  $structure = "CREATE TABLE IF NOT EXISTS $table (
        id INT(9) NOT NULL AUTO_INCREMENT,
        cdi_order_id VARCHAR(9) NOT NULL,
        cdi_tracking VARCHAR(200) NOT NULL,
        cdi_parcelNumberPartner VARCHAR(200) NOT NULL,
        cdi_hreflabel VARCHAR(200) NOT NULL,
        cdi_status VARCHAR(200) NOT NULL, 
        cdi_reserve VARCHAR(200) NOT NULL,
	UNIQUE KEY id (id),
        UNIQUE KEY cdi_order_id (cdi_order_id)
     );";
  $wpdb->query($structure);
}


/**
 * Update version and db
 */
function cdi_update_version() {
  global $wpdb;
  $plugin_data = get_plugin_data( __FILE__ );
  $plugin_version = $plugin_data['Version'];
  $options_version = get_option('cdi_options_version');
  $x = strnatcasecmp ( $plugin_version , $options_version );
  if (!$options_version or $x > 0) {
    $table = $wpdb->prefix . "cdi";
    if (strnatcasecmp ( '2.0.2' , $options_version ) > 0) { // Update (again) for 2.0.2
          // Nothing to do
    }
    if (strnatcasecmp ( '3.0.0' , $options_version ) > 0) { // Update (again) for 3.0.0
          // Nothing to do
    }
    if (strnatcasecmp ( '3.7.18' , $options_version ) > 0) { // Update (again) for 3.7.18
      // For compat with old debug function
      if (get_option('wc_settings_tab_colissimo_log') !== 'yes') {
        $default = array ('no debug') ;
        update_option('wc_settings_tab_colissimo_moduletolog', $default) ;
        update_option('wc_settings_tab_colissimo_log', 'yes') ;
        update_option('cdi_checksettings', WC_function_Colissimo::cdi_checksettings());
      }
      // Pre fill of InternationalWithoutSignContryCodes
      if (null == get_option('wc_settings_tab_colissimo_ws_InternationalWithoutSignContryCodes') or get_option('wc_settings_tab_colissimo_ws_InternationalWithoutSignContryCodes') == "") {
        update_option('wc_settings_tab_colissimo_ws_InternationalWithoutSignContryCodes', wc_settings_tab_colissimo_ws_InternationalWithoutSignContryCodes) ;
        update_option('cdi_checksettings', WC_function_Colissimo::cdi_checksettings());
      }
      if (null == get_option('wc_settings_tab_colissimo_companyandorderref')) {
        update_option('wc_settings_tab_colissimo_companyandorderref', "yes") ;
        update_option('cdi_checksettings', WC_function_Colissimo::cdi_checksettings());
      }

    }
    // Update version in options table
    delete_option('cdi_options_version' );
    add_option('cdi_options_version', $plugin_version);
  }
  update_option ('cdi_modai', 'eyJNMDEiOnsic2VjdGlvbi1nZW5lcmFsIjoiUlx1MDBlOWdsYWdlcyBnXHUwMGU5blx1MDBlOXJhdXgiLCJzZWN0aW9uLXRyYWNraW5nIjoiSW50ZXJmYWNlcyBjbGllbnQiLCJzZWN0aW9uLXNoaXBwaW5nIjoiTVx1MDBlOXRob2RlcyBkZSBsaXZyYWlzb24gQ0RJIiwic2VjdGlvbi1wcmludGxhYmVsIjoiRXRpcXVldHRlcyBsZXR0cmVzIn0sIk0wMiI6W10sIk0wMyI6eyJzdzEiOiIxIiwic3cyIjoiMSIsInN3MyI6IjEiLCJzdzQiOiIxIiwic3c1IjoiMCIsInN3NiI6IjAiLCJzdzciOiIwIiwic3c4IjoiMCJ9fQ==') ;
}
add_action( 'admin_init', 'cdi_update_version' );

/**
 * Set sub link in  plugins extension admin panel
 */
function cdi_plugin_row_meta( $links, $file ) {
  if ( $file ==  plugin_basename( __FILE__ ) ) {
    $row_meta = array(
      'support' => '<a href="https://wordpress.org/plugins/colissimo-delivery-integration/faq/" title="Do you need some help?" onclick="window.open(this.href); return false;" target="_self">' . __( 'Do you need some help?', 'colissimo-delivery-integration' ) . '</a>');
    return array_merge( $links, $row_meta );
  }
  return (array) $links;
}
add_filter( 'plugin_row_meta', 'cdi_plugin_row_meta', 10, 2 );

/**
 * Set locale and load plugin textdomain
 */
function cdi_load_plugin_textdomain() {
$locale = apply_filters( 'plugin_locale', get_locale(), 'colissimo-delivery-integration' );
load_textdomain( 'colissimo-delivery-integration', WP_LANG_DIR . '/colissimo-delivery-integration/colissimo-delivery-integration-' . $locale . '.mo' );
load_plugin_textdomain( 'colissimo-delivery-integration', false, plugin_basename( dirname( __FILE__ ) ) . '/languages' );
}
add_action( 'init', 'cdi_load_plugin_textdomain' ) ;


/**
 * Define "cdi_gateway" capability for admin roles (which can manage_options) and for role names chosen in settings.
 */
function cdi_add_caps_gateway() {
  $arrrolename = get_option('wc_settings_tab_colissimo_rolename_gateway') ;
  if ($arrrolename && $arrrolename !== '') {
    $roles = get_editable_roles();
    foreach ($GLOBALS['wp_roles']->role_objects as $key => $role) {
      if (isset($roles[$key])) {
        if ($role->has_cap('manage_options') OR in_array($role->name, $arrrolename)) {
          $role->add_cap('cdi_gateway');
        }else{
          $role->remove_cap('cdi_gateway');
        }
      }
    }
  }
}
add_action( 'admin_init','cdi_add_caps_gateway');


/**
 * Define link to settings.
 */
add_filter( 'plugin_action_links_' . plugin_basename( __FILE__ ), 'cdi_plugin_action_links' );
function cdi_plugin_action_links( $links ) {
  $setting_link = 'admin.php?page=wc-settings&tab=settings_tab_colissimo' ;
  $plugin_links = array(
                    '<a href="' . $setting_link . '">' . __( 'Settings', 'colissimo-delivery-integration' ) . '</a>',
		  );
  return array_merge( $plugin_links, $links );
}

/**
 * Force again the debug settings.
 */
$cdi_save_init_set = get_option('cdi_save_init_set') ;
if ($cdi_save_init_set) {
  ini_set( 'error_reporting', E_ALL ) ;
  ini_set( 'log_errors', 1 );
  ini_set( 'display_errors', 0 );
  ini_set( 'error_log', WP_CONTENT_DIR . '/debug.log' );
}

/**
 * Including Classes
 */

include_once ('includes/WC-cdi-class-wc3.php'); 

include_once ('includes/WC-function-Colissimo.php'); 
WC_function_Colissimo::init();

include_once ('includes/WC-Metabox-Colissimo.php');
WC_Metabox_Colissimo::init();

include_once ('includes/WC-Settings-Tab-Colissimo.php'); 
WC_Settings_Tab_Colissimo::init();

include_once ('includes/WC-Action-Orderlist-Colissimo.php'); 
WC_Action_Orderlist_Colissimo::init();
		
include_once ('includes/WC-Action-Bulk-Colissimo.php'); 
WC_Action_Bulk_Colissimo::init();

include_once ('includes/WC-Gateway-Tab-Colissimo.php'); 
WC_Gateway_Tab_Colissimo::init();

include_once ('includes/WC-gateway-colissimo-manual.php');
WC_gateway_colissimo_manual::init();

include_once ('includes/WC-gateway-colissimo-printlabel.php');
WC_gateway_colissimo_printlabel::init();

include_once ('includes/WC-gateway-colissimo-online.php');
WC_gateway_colissimo_online::init();

include_once ('includes/WC-gateway-colissimo-auto.php');
WC_gateway_colissimo_auto::init();

include_once ('includes/WC-gateway-colissimo-custom.php');
WC_gateway_colissimo_custom::init();

include_once ('includes/WC-Frontend-Colissimo.php');
WC_Frontend_Colissimo::init();

include_once ('includes/WC-print-localpdf-labelandcn23.php');
WC_print_localpdf_labelandcn23::init();

include_once ('includes/WC-colissimo-shipping-zone.php');
WC_colissimo_shipping::init();

include_once ('includes/WC-colissimo-choix-livraison.php');
WC_colissimo_choix_livraison::init();

include_once ('includes/WC-colissimo-retourcolis.php');
WC_colissimo_retourcolis::init();

include_once ('includes/WC-gateway-colissimo-coliship.php');
WC_gateway_colissimo_coliship::init();

include_once ('includes/WC-Gateway-Tab-Printbulkpdf.php');
WC_Gateway_Tab_Printbulkpdf::init();

include_once ('includes/WC-Gateway-Bordereaux.php');
WC_Gateway_bordereaux::init();

include_once ('includes/WC-Gateway-Debug.php');
WC_Gateway_debug::init();

}
?>
