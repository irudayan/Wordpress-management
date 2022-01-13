<?php

/**
 * This file is part of the Colissimo Delivery Integration plugin.
 * (c) Halyra
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

if (!defined('ABSPATH')) exit;
/****************************************************************************************/
/* Cdi settings in a tab panel added in the woocommerce settings                        */
/****************************************************************************************/

// Colissimo official constants
define('wc_settings_tab_colissimo_ws_FranceCountryCodes', 'FR,MC,AD');
define('wc_settings_tab_colissimo_ws_FranceProductCodes', 'DOM,DOS,A2P') ;
define('wc_settings_tab_colissimo_ws_OutreMerCountryCodes', 'MQ,GP,RE,GF,YT,PM,MF,BL,PF,NC,WF,TF') ;
define('wc_settings_tab_colissimo_ws_OutreMerProductCodes', 'COM,CDS,') ;
define('wc_settings_tab_colissimo_ws_EuropeCountryCodes', 'BE,DE,NL,ES,GB,LU,PT,AT,CZ,HU,SK,SI,LT,LV,EE,BG,CY,HR,DK,FI,GR,IE,IS,IT,MT,NO,PL,RO,SE,CH') ; // Change in 3.0.0
define('wc_settings_tab_colissimo_ws_EuropeProductCodes', 'DOM,DOS,CMT') ;
define('wc_settings_tab_colissimo_ws_InternationalCountryCodes', '*') ;
define('wc_settings_tab_colissimo_ws_InternationalProductCodes', 'COLI,COLI,') ;
define('wc_settings_tab_colissimo_ws_ExceptionProductCodes', 'ACP=BPR,CDI=BPR') ;
define('wc_settings_tab_colissimo_ws_InternationalPickupLocationContryCodes', 'FR,MC,AD,BE,DE,NL,ES,GB,LU,AT,EE,LV,LT,PL,PT,SE') ; // Change in 3.0.0
define('wc_settings_tab_colissimo_ws_InternationalWithoutSignContryCodes', 'FR,AD,MC,MQ,GP,RE,GF,YT,PM,MF,BL,BE,CH') ; // Change in 3.5.1
define('wc_settings_tab_colissimo_trackingheaders_parcelreturn', '6A,9L,6C,9V,6H,6M,8R,7R,8Q,7Q,9W,5R,CP,EY,EN,CM,CA,CB,CI') ; // Change in 3.0.0
define('wc_settings_tab_colissimo_country_Nochoiceparcelreturn', 'US,AU,JP,DE,AT,BE,BG,CY,DK,ES,EE,FI,FR,GR,HU,IE,IT,LV,LT,LU,MT,NL,PL,PT,CZ,RO,GB,IE,SK,SI,SE') ; //New in 3.7.13
define('wc_settings_tab_colissimo_returnproduct_code', 'CORE=FR,MC,AD;CORI=DE,AT,BE,ES,FI,IE,IT,LU,NL,PL,CZ,GB,SK,SI,CH,PT,EE,HU,LT,HR,GR,MT,RO,AU') ; // Change in 3.0.0
define('wc_settings_tab_colissimo_ws_Nocn23ContryCodes', 'DE,AT,BE,BG,CY,DK,ES,EE,FI,FR,GR,HU,IE,IT,LV,LT,LU,MT,NL,PL,PT,CZ,RO,GB,IE,SK,SI,SE');//New in 3.6.0
define('wc_settings_tab_colissimo_Cn23ZipcodeExemptions', 'DE=27498,78266;IT=23030,22060;GR=63086;ES=35001,35002,35003,35004,35005,35006,35007,35008,35009,35010,35011,35012,35013,35014,35015,35016,35017,35018,35019,38001,38002,38003,38004,38005,38006,38007,38008,38009,38010,38107,38108,38110,38160,38320,38617') ; // New in 3.6.0

class WC_Settings_Tab_Colissimo {
    /**
     * Bootstraps the class and hooks required actions & filters.
     *
     */
    public static function init() {
        add_filter( 'woocommerce_settings_tabs_array', __CLASS__ . '::add_settings_tab', 50 );
        add_action( 'woocommerce_settings_tabs_settings_tab_colissimo', __CLASS__ . '::settings_tab' );
        add_action( 'woocommerce_sections_settings_tab_colissimo',  __CLASS__ . '::cdi_settings_page' );
        add_action( 'woocommerce_update_options_settings_tab_colissimo', __CLASS__ . '::update_settings' );
        add_action( 'woocommerce_settings_saved', __CLASS__ . '::cdi_woocommerce_settings_saved' );
    }

    public static function add_settings_tab( $settings_tabs ) {
        $settings_tabs['settings_tab_colissimo'] = __( 'CDI', 'colissimo-delivery-integration' );
        return $settings_tabs;
    }

    public static function settings_tab() {
        $return = woocommerce_admin_fields( self::get_settings() );
        return $return ;
    }

    public static function update_settings() {
        if (get_option('cdi_checksettings_error') !== 'yes') {
          update_option('cdi_checksettings_error', 'no') ;
        }
        $return = woocommerce_update_options( self::get_settings() );
        return $return ;
    }

    public static function cdi_woocommerce_settings_saved() {
        if (get_option('cdi_checksettings_error') == 'no') {
          update_option('cdi_checksettings', WC_function_Colissimo::cdi_checksettings());
          delete_option('cdi_checksettings_error') ;
        }
    }


    public static function cdi_settings_page() {
        global $current_section;
        $returnmsg = WC_function_Colissimo::cdi_cdiplus_credential() ;
        if ($returnmsg) {
          WC_Admin_Settings::add_message($returnmsg);
        }
        $result = get_option('WC_settings_tab_colissimo_domain') ;
        if (!$result){
          update_option('WC_settings_tab_colissimo_domain', 'https://colissimodeliveryintegration.com');
          update_option('cdi_checksettings', WC_function_Colissimo::cdi_checksettings());
        }
        $lastchecksettings = get_option('cdi_checksettings') ;
        $checksettings =  WC_function_Colissimo::cdi_checksettings();
        if ($lastchecksettings && $checksettings && $lastchecksettings !== $checksettings && !WC_function_Colissimo::cdi_isconnected()) {
          WC_Admin_Settings::add_message('CDI error : inconsistency in settings data. You must restart with a new CDI : uninstall CDI after ticking the "Clean CDI data when plugin is uninstalled"  box in the CDI settings. Unfortunately you will need to reinstall your settings.');
          if (WC_function_Colissimo::cdi_sanitize_pil('sw1') == '1') {
            update_option('cdi_checksettings_error', 'yes') ; 
            return ;
          }
        }else{
          update_option('cdi_checksettings', $checksettings) ;
        }
        $sec = WC_function_Colissimo::cdi_sanitize_sec() ;
        echo '<ul class="subsubsub">';
        $array_keys = array_keys( $sec );
        foreach ( $sec as $id => $label ) {
            echo '<li><a href="' . admin_url( 'admin.php?page=wc-settings&tab=' . 'settings_tab_colissimo' . '&section=' . sanitize_title( $id ) ) . '" class="' . ( $current_section == $id ? 'current' : '' ) . '">' . $label . '</a> ' . ( end( $array_keys ) == $id ? '' : '|' ) . ' </li>';
        }
        echo '</ul><br class="clear" />';
        echo '<p>' ;
        // To reinstall the official Colissimo settings
        echo '<em></em><input name="cdi_reset_settings_colissimo" type="submit" value="' . __( 'Reset the official Colissimo settings', 'colissimo-delivery-integration' ) . '" style="float: left; color:red;" title="' . __( 'Warning : To reset your Colissimo settings (shown with red font)  to official settings if you have inadvertently changed them. Your own website settings (font not in red) will not be modified.', 'colissimo-delivery-integration' ) . '" /><em></em>' ;
        if ($_SERVER['REQUEST_METHOD'] == "POST" and ISSET($_POST['cdi_reset_settings_colissimo'])) {
          update_option('wc_settings_tab_colissimo_ws_FranceCountryCodes', wc_settings_tab_colissimo_ws_FranceCountryCodes) ;
          update_option('wc_settings_tab_colissimo_ws_FranceProductCodes', wc_settings_tab_colissimo_ws_FranceProductCodes) ;
          update_option('wc_settings_tab_colissimo_ws_OutreMerCountryCodes', wc_settings_tab_colissimo_ws_OutreMerCountryCodes) ;
          update_option('wc_settings_tab_colissimo_ws_OutreMerProductCodes', wc_settings_tab_colissimo_ws_OutreMerProductCodes) ;
          update_option('wc_settings_tab_colissimo_ws_EuropeCountryCodes', wc_settings_tab_colissimo_ws_EuropeCountryCodes) ;
          update_option('wc_settings_tab_colissimo_ws_EuropeProductCodes', wc_settings_tab_colissimo_ws_EuropeProductCodes) ;
          update_option('wc_settings_tab_colissimo_ws_InternationalCountryCodes', wc_settings_tab_colissimo_ws_InternationalCountryCodes) ;
          update_option('wc_settings_tab_colissimo_ws_InternationalProductCodes', wc_settings_tab_colissimo_ws_InternationalProductCodes) ;
          update_option('wc_settings_tab_colissimo_ws_ExceptionProductCodes', wc_settings_tab_colissimo_ws_ExceptionProductCodes) ;
          update_option('wc_settings_tab_colissimo_ws_InternationalPickupLocationContryCodes', wc_settings_tab_colissimo_ws_InternationalPickupLocationContryCodes) ;
          update_option('wc_settings_tab_colissimo_ws_InternationalWithoutSignContryCodes', wc_settings_tab_colissimo_ws_InternationalWithoutSignContryCodes) ;
          update_option('wc_settings_tab_colissimo_trackingheaders_parcelreturn', wc_settings_tab_colissimo_trackingheaders_parcelreturn) ;
          update_option('wc_settings_tab_colissimo_country_Nochoiceparcelreturn', wc_settings_tab_colissimo_country_Nochoiceparcelreturn) ;        
          update_option('wc_settings_tab_colissimo_returnproduct_code', wc_settings_tab_colissimo_returnproduct_code) ;
          update_option('wc_settings_tab_colissimo_ws_Nocn23ContryCodes', wc_settings_tab_colissimo_ws_Nocn23ContryCodes) ;
          update_option('wc_settings_tab_colissimo_Cn23ZipcodeExemptions', wc_settings_tab_colissimo_Cn23ZipcodeExemptions) ;
          update_option('cdi_checksettings', WC_function_Colissimo::cdi_checksettings());
        }
        if (WC_function_Colissimo::cdi_isconnected()) {
          echo '<em></em><input name="cdi_reinstall_cdidb_colissimo" type="submit" value="' . __( 'Reinstall the CDI Gateway', 'colissimo-delivery-integration' ) . '" style="float: left; color:red;" title="' . __( 'Warning : To reinstall the CDI gateway when something is wrong in CDI process or in an order. The gateway will be recreate empty of parcels. Existing pending parcels in gateway will be deleted, but corresponding orders will be inchanged. All others CDI settings will stay.', 'colissimo-delivery-integration' ) . '" /><em></em>' ;
          if ($_SERVER['REQUEST_METHOD'] == "POST" and ISSET($_POST['cdi_reinstall_cdidb_colissimo'])) {
            global $wpdb;
            eval (WC_function_Colissimo::cdi_eval('6')) ;
            $results = $wpdb->query("DROP TABLE $table") ;
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
        }

        WC_function_Colissimo::cdi_button_connected() ;
        $return = WC_function_Colissimo::cdi_button_adhesioncdiplus() ;
        if ($return !== null) {
          WC_Admin_Settings::add_message($return);
        }
        WC_function_Colissimo::cdi_button_informationcdi() ;
        if (WC_function_Colissimo::cdi_isconnected()) {
          WC_function_Colissimo::cdi_button_support() ;
        }
        echo '</p><br class="clear">' ;

        //*********************** For compatibility with anterior control - to delete after some versions
        $x = get_option('wc_settings_tab_colissimo_autoclean_gateway') ;
        if ($x) {
          update_option('wc_settings_tab_colissimo_' . 'section-general', '1') ;
          update_option('cdi_checksettings', WC_function_Colissimo::cdi_checksettings());
        }
        $x = get_option('wc_settings_tab_colissimo_cn23_category') ;
        if ($x) {
          update_option('wc_settings_tab_colissimo_' . 'section-cn23', '1') ;
          update_option('cdi_checksettings', WC_function_Colissimo::cdi_checksettings());
        }
        $x = get_option('wc_settings_tab_colissimo_inserttrackingcode') ;
        if ($x) {
          update_option('wc_settings_tab_colissimo_' . 'section-tracking', '1') ;
          update_option('cdi_checksettings', WC_function_Colissimo::cdi_checksettings());
        }
        $x = get_option('wc_settings_tab_colissimo_ws_OffsetDepositDate') ;
        if ($x) {
          update_option('wc_settings_tab_colissimo_' . 'section-automatic', '1') ;
          update_option('cdi_checksettings', WC_function_Colissimo::cdi_checksettings());
        }
        $x = get_option('wc_settings_tab_colissimo_methodreferal') ;
        if ($x) {
          update_option('wc_settings_tab_colissimo_' . 'section-referrals', '1') ;
          update_option('cdi_checksettings', WC_function_Colissimo::cdi_checksettings());
        }
        $x = get_option('wc_settings_tab_colissimo_methodshipping') ;
        if ($x) {
          update_option('wc_settings_tab_colissimo_' . 'section-shipping', '1') ;
          update_option('cdi_checksettings', WC_function_Colissimo::cdi_checksettings());
        }
        $x = get_option('wc_settings_tab_colissimo_parcelreturn') ;
        if ($x) {
          update_option('wc_settings_tab_colissimo_' . 'section-parcelreturn', '1') ;
          update_option('cdi_checksettings', WC_function_Colissimo::cdi_checksettings());
        }
        $x = get_option('wc_settings_tab_colissimo_pagesize') ;
        if ($x) {
          update_option('wc_settings_tab_colissimo_' . 'section-printlabel', '1') ;
          update_option('cdi_checksettings', WC_function_Colissimo::cdi_checksettings());
        }

        // Warning if settings has not been saved
        $translate = array(__('Settings tab section-general', 'colissimo-delivery-integration'),__('Settings tab section-cn23', 'colissimo-delivery-integration'),__('Settings tab section-tracking', 'colissimo-delivery-integration'),__('Settings tab section-automatic', 'colissimo-delivery-integration'),__('Settings tab section-referrals', 'colissimo-delivery-integration'),__('Settings tab section-shipping', 'colissimo-delivery-integration'),__('Settings tab section-parcelreturn', 'colissimo-delivery-integration'),__('Settings tab section-printlabel', 'colissimo-delivery-integration')) ;
        $warning = '' ;
        foreach ($sec as $key => $s) {
          $x = get_option('wc_settings_tab_colissimo_' . $key ) ;
          if (null == get_option('wc_settings_tab_colissimo_' . $key )) {
              $warning .= __( 'Settings tab ' . $key, 'colissimo-delivery-integration' ) . ' | ' ;
          }
        }
        if ($warning !== '') {
          WC_Admin_Settings::add_message( __( 'It seems yours CDI panels | ', 'colissimo-delivery-integration' ) . $warning . __( ' have not been registered. So your plugin will not work correctly. Please click on "Register changes" for each of theses panels to have your settings correctly registered.', 'colissimo-delivery-integration' ));
        }

        // Warnings if use of old packages
        $wcversion = get_bloginfo('version');
        if (version_compare($wcversion, '4.7.0') < 0) {
          WC_Admin_Settings::add_message( __( 'The Wordpress version is less than 4.7.0. You should upgrade it to fully use Colissimo Delivery Integration', 'colissimo-delivery-integration' ));
        }
        $wooversion = WC_function_Colissimo::cdi_get_woo_version_number();
        if (version_compare($wooversion, '3.0.0') < 0) {
          WC_Admin_Settings::add_message( __( 'The Woocommerce version is less than 3.0.0. You should upgrade it to fully use Colissimo Delivery Integration', 'colissimo-delivery-integration' ));
        }
        if (version_compare(phpversion(), '5.4.0') < 0) {
          WC_Admin_Settings::add_message( __( 'The PHP version is less than 5.4.0. You should upgrade it to fully use Colissimo Delivery Integration', 'colissimo-delivery-integration' ));
        }
        if (!extension_loaded('openssl')) {
          WC_Admin_Settings::add_message( __( 'The OpenSSL extension is not installed. You should install it to fully use Colissimo Delivery Integration', 'colissimo-delivery-integration' ));
        }else{
          $opensslcurrent = WC_function_Colissimo::get_openssl_version_number($patch_as_number=true) ;
          if (version_compare($opensslcurrent, '1.0.1') < 0) {
            WC_Admin_Settings::add_message( __( 'The Openssl version is less than 1.0.1. You should upgrade it to fully use Colissimo Delivery Integration', 'colissimo-delivery-integration' ));
          }
        }
        if (!ini_get('allow_url_fopen') && !function_exists('curl_init')) {
          WC_Admin_Settings::add_message( __( 'Your installation has not allow_url_fopen authorized, and moreover Curl is not setup. We can be afraid that CDI will not fully work.', 'colissimo-delivery-integration' ));
        }
        if (!class_exists ('SoapClient')) {
          WC_Admin_Settings::add_message( __( 'Your installation has not Soap extension installed. We can be afraid that CDI will not fully work.', 'colissimo-delivery-integration' ));
        }
    }


//  Declare of Settings according to the Section called
    public static function get_settings() {
        global $settings ;
        if (isset($_GET["section"])) {
          $section = $_GET["section"];
        }else{
          $section = 'section-general'; // default section
        }
        switch( $section ){
            case 'section-general' :
                $settings = WC_Settings_Tab_Colissimo::get_settings_section_general() ;
            break;
            case 'section-cn23' :
                $settings = WC_Settings_Tab_Colissimo::get_settings_section_cn23() ;
            break;
            case 'section-tracking' :
                $settings = WC_Settings_Tab_Colissimo::get_settings_section_tracking() ;
            break;
            case 'section-automatic' :
                $settings = WC_Settings_Tab_Colissimo::get_settings_section_automatic() ;
            break;
            case 'section-referrals' :
                $settings = WC_Settings_Tab_Colissimo::get_settings_section_referrals() ;
            break;
            case 'section-shipping' :
                $settings = WC_Settings_Tab_Colissimo::get_settings_section_shipping() ;
            break;
            case 'section-parcelreturn' :
                $settings = WC_Settings_Tab_Colissimo::get_settings_section_parcelreturn() ;
            break;
            case 'section-printlabel' :
                $settings = WC_Settings_Tab_Colissimo::get_settings_section_printlabel() ;
            break;
        }
        return $settings ;
    }


//  ref : https://github.com/woothemes/woocommerce/blob/5dcd19f5fa133a25c7e025d7c73e04516bcf90da/includes/admin/class-wc-admin-settings.php#L195
//  Get the settings of Section general
    public static function get_settings_section_general() {
        $selectroles = array() ;
        if ( ! function_exists( 'get_editable_roles' ) ) {
          require_once ABSPATH . 'wp-admin/includes/user.php';
        }
        $roles = get_editable_roles();
        foreach ($GLOBALS['wp_roles']->role_objects as $key => $role) {
          if (isset($roles[$key])) {
            $selectroles[$role->name] =  $role->name ;
          }
        }
        $return = array(
            'section_title' => array(
                'name'     => __( 'Colissimo Delivery Integration - General settings', 'colissimo-delivery-integration' ),
                'type'     => 'title',
                'desc'     => '',
                'id'       => 'wc_settings_tab_colissimo_section_general'
            ),
            'General section mark' => array(
                'css'  => 'display:none;',
                'type' => 'number',
                'default' => '1',
                'id'   => 'wc_settings_tab_colissimo_section-general'
            ),
            'Type Parcel' => array(
                'name' => __( 'Parcel default settings', 'colissimo-delivery-integration' ),
                'type' => 'select',
                'options' => array (
                  'colis-standard'   => __('Standard', 'colissimo-delivery-integration'),
                  'colis-volumineux' => __('Cumbersome', 'colissimo-delivery-integration'),
                  'colis-rouleau   ' => __('Tube', 'colissimo-delivery-integration'),
                  ),
                'default' => 'Standard',
                'desc' => __( 'Default Type Parcel', 'colissimo-delivery-integration' ),
                'id'   => 'wc_settings_tab_colissimo_defaulttypeparcel'
            ),
            'Parcel empty weight' => array(
                'type' => 'number',
                'default' => '250',
                'desc' => __( 'Default weight (in grams) of empty Colissimo package (net weight of products will be added)', 'colissimo-delivery-integration' ),
                'desc_tip' => __( "If products have not weight, this tare weight will be the default weight of the parcel. Anyway, the total computed weight of the parcel can be change manually in the order meta box before processing the parcel.", "colissimo-delivery-integration" ),
                'id'   => 'wc_settings_tab_colissimo_parcelweight'
            ),
            'Contre signature' => array(
                'type' => 'checkbox',
                'default' => 'no',
                'desc' => __( 'Default Delivery "contre-signature avec option recommandation"', 'colissimo-delivery-integration' ),
                'id'   => 'wc_settings_tab_colissimo_signature'
            ),
            'Additional Compensation' => array(
                'type' => 'checkbox',
                'default' => 'no',
                'desc' => __( 'Default Additional Compensation ? :', 'colissimo-delivery-integration' ),
                'id'   => 'wc_settings_tab_colissimo_additionalcompensation'
            ),
            'Amount Compensation' => array(
                'type' => 'number',
                'default' => '50',
                'desc' => __( 'Default Total Amount Compensation in euros', 'colissimo-delivery-integration' ),
                'desc_tip' => __( "Is significant only if additional compensation is checked. It is the default insurance amount in €.", "colissimo-delivery-integration" ),
                'id'   => 'wc_settings_tab_colissimo_amountcompensation'
            ),
            'Type Return' => array(
                'type' => 'select',
                'options' => array (
                  'no-return'      => __('No return', 'colissimo-delivery-integration'),
                  'pay-for-return' => __('Pay for return', 'colissimo-delivery-integration'),
                  ),
                'default' => 'No return',
                'desc' => __( 'Default Type Return', 'colissimo-delivery-integration' ),
                'desc_tip' => __( "Mandatory for some abroad shipments. Consult Colissimo help to known which countries need that data.", "colissimo-delivery-integration" ),
                'id'   => 'wc_settings_tab_colissimo_defaulttypereturn'
            ),
            'French departure city' => array(
                'type' => 'text',
                'default' => '75001 PARIS',
                'desc' => __( 'French departure city - ZIP code & City.', 'colissimo-delivery-integration' ),
                'desc_tip' => __( "Mandatory for Colissimo online services. The syntax zipcode espace city is strict.", "colissimo-delivery-integration" ),
                'id'   => 'wc_settings_tab_colissimo_departure'
            ),
            'Départ Boite aux lettres' => array(
                'type' => 'checkbox',
                'default' => 'no',
                'desc' => __( 'Departure from your own letter box', 'colissimo-delivery-integration' ),
                'id'   => 'wc_settings_tab_colissimo_fromletterbox'
            ),
            'Clean on suppress' => array(
                'name' => __( 'Plugin features', 'colissimo-delivery-integration' ),
                'type' => 'checkbox',
                'default' => 'no',
                'desc' => __( 'Clean CDI datas when plugin is uninstalled', 'colissimo-delivery-integration' ),
                'desc_tip' => __( "CDI parameters are normally keep saved in the database when the plugin is uninstalled. Thus, these parameters are operational when the plugin is reinstalled. By cons, when the ckeck is checked, all CDI datas will be cleaned when the plugin is uninstall.", "colissimo-delivery-integration" ),
                'id'   => 'wc_settings_tab_colissimo_cleanonsuppress'
            ),
            'Module to log' => array(
                'type' => 'multiselect',
                'options' => array (
                  'no debug' => 'no debug' ,
                  '/includes/WC-Action-Bulk-Colissimo.php' => '/includes/WC-Action-Bulk-Colissimo.php' ,
                  '/includes/WC-Action-Orderlist-Colissimo.php' => '/includes/WC-Action-Orderlist-Colissimo.php' ,
                  '/includes/WC-cdi-class-wc3.php' => '/includes/WC-cdi-class-wc3.php' ,
                  '/includes/WC-colissimo-choix-livraison.php' => '/includes/WC-colissimo-choix-livraison.php' ,
                  '/includes/WC-colissimo-retourcolis.php' => '/includes/WC-colissimo-retourcolis.php' ,
                  '/includes/WC-colissimo-shipping-zone.php' => '/includes/WC-colissimo-shipping-zone.php' ,
                  '/includes/WC-Frontend-Colissimo.php' => '/includes/WC-Frontend-Colissimo.php' ,
                  '/includes/WC-function-Colissimo.php' => '/includes/WC-function-Colissimo.php' ,
                  '/includes/WC-Gateway-Bordereaux.php' => '/includes/WC-Gateway-Bordereaux.php' ,
                  '/includes/WC-gateway-colissimo-auto.php' => '/includes/WC-gateway-colissimo-auto.php' ,
                  '/includes/WC-gateway-colissimo-coliship.php' => '/includes/WC-gateway-colissimo-coliship.php' ,
                  '/includes/WC-gateway-colissimo-custom.php' => '/includes/WC-gateway-colissimo-custom.php' ,
                  '/includes/WC-gateway-colissimo-manual.php' => '/includes/WC-gateway-colissimo-manual.php' ,
                  '/includes/WC-gateway-colissimo-online.php' => '/includes/WC-gateway-colissimo-online.php' ,
                  '/includes/WC-gateway-colissimo-printlabel.php' => '/includes/WC-gateway-colissimo-printlabel.php' ,
                  '/includes/WC-Gateway-Debug.php' => '/includes/WC-Gateway-Debug.php' ,
                  '/includes/WC-Gateway-Tab-Colissimo.php' => '/includes/WC-Gateway-Tab-Colissimo.php' ,
                  '/includes/WC-Gateway-Tab-Printbulkpdf.php' => '/includes/WC-Gateway-Tab-Printbulkpdf.php' ,
                  '/includes/WC-Metabox-Colissimo.php' => '/includes/WC-Metabox-Colissimo.php' ,
                  '/includes/WC-print-localpdf-labelandcn23.php' => '/includes/WC-print-localpdf-labelandcn23.php' ,
                  '/includes/WC-Settings-Tab-Colissimo.php' => '/includes/WC-Settings-Tab-Colissimo.php' ,
                  '/colissimo-delivery-integration.php' => '/colissimo-delivery-integration.php' ,
                  '/uninstall.php' => '/uninstall.php' ,
                  ),
                'default' => array (
                  '/includes/WC-Action-Bulk-Colissimo.php' => '/includes/WC-Action-Bulk-Colissimo.php' ,
                  '/includes/WC-Action-Orderlist-Colissimo.php' => '/includes/WC-Action-Orderlist-Colissimo.php' ,
                  '/includes/WC-cdi-class-wc3.php' => '/includes/WC-cdi-class-wc3.php' ,
                  '/includes/WC-colissimo-choix-livraison.php' => '/includes/WC-colissimo-choix-livraison.php' ,
                  '/includes/WC-colissimo-retourcolis.php' => '/includes/WC-colissimo-retourcolis.php' ,
                  '/includes/WC-colissimo-shipping-zone.php' => '/includes/WC-colissimo-shipping-zone.php' ,
                  '/includes/WC-Frontend-Colissimo.php' => '/includes/WC-Frontend-Colissimo.php' ,
                  '/includes/WC-function-Colissimo.php' => '/includes/WC-function-Colissimo.php' ,
                  '/includes/WC-Gateway-Bordereaux.php' => '/includes/WC-Gateway-Bordereaux.php' ,
                  '/includes/WC-gateway-colissimo-auto.php' => '/includes/WC-gateway-colissimo-auto.php' ,
                  '/includes/WC-gateway-colissimo-coliship.php' => '/includes/WC-gateway-colissimo-coliship.php' ,
                  '/includes/WC-gateway-colissimo-custom.php' => '/includes/WC-gateway-colissimo-custom.php' ,
                  '/includes/WC-gateway-colissimo-manual.php' => '/includes/WC-gateway-colissimo-manual.php' ,
                  '/includes/WC-gateway-colissimo-online.php' => '/includes/WC-gateway-colissimo-online.php' ,
                  '/includes/WC-gateway-colissimo-printlabel.php' => '/includes/WC-gateway-colissimo-printlabel.php' ,
                  '/includes/WC-Gateway-Debug.php' => '/includes/WC-Gateway-Debug.php' ,
                  '/includes/WC-Gateway-Tab-Colissimo.php' => '/includes/WC-Gateway-Tab-Colissimo.php' ,
                  '/includes/WC-Gateway-Tab-Printbulkpdf.php' => '/includes/WC-Gateway-Tab-Printbulkpdf.php' ,
                  '/includes/WC-Metabox-Colissimo.php' => '/includes/WC-Metabox-Colissimo.php' ,
                  '/includes/WC-print-localpdf-labelandcn23.php' => '/includes/WC-print-localpdf-labelandcn23.php' ,
                  '/includes/WC-Settings-Tab-Colissimo.php' => '/includes/WC-Settings-Tab-Colissimo.php' ,
                  '/colissimo-delivery-integration.php' => '/colissimo-delivery-integration.php' ,
                  '/uninstall.php' => '/uninstall.php' ,
                 ),
                'desc' => __( 'Multi select the modules for which you need a debugging log.', 'colissimo-delivery-integration' ),
                'desc_tip' => __( "Traces apply to all CDI modules. They are stored in the wp-content/debug.log file when the config.php setting authorize the debug mode with : define( 'WP_DEBUG', true ); define( 'WP_DEBUG_LOG', true ); define( 'WP_DEBUG_DISPLAY', false ); . A selection of only some modules will give you a smaller log file easier to be analysed. To analyse the log, you can directly edit the wp-content/debug.log file, or use ad-hoc plugin to view and manage the file as for instance 'WP Log Viewer Plugin'. CDI+ offers a debug view fonction.", "colissimo-delivery-integration" ),
                'id'   => 'wc_settings_tab_colissimo_moduletolog'
            ),
            'Role using Gateway' => array(
                'type' => 'multiselect',
                'options' => $selectroles , 
                'default' => array ('shop_manager'),
                'desc' => __( 'Choose the WP role you want to have access to CDI-Gateway.', 'colissimo-delivery-integration' ),
                'desc_tip' => __( 'Administrator have already an access to CDI-Gateway. But you can choose to give also access to CDI-Gateway to another role which is not administrator .', "colissimo-delivery-integration" ),
                'id'   => 'wc_settings_tab_colissimo_rolename_gateway'
            ),
            'Get content mode' => array(
                'type' => 'select',
                'options' => array (
                  'curl'      => __('Curl', 'colissimo-delivery-integration'),
                  'filegetcontents' => __('File get contents', 'colissimo-delivery-integration'),
                  ),
                'default' => 'Curl',
                'desc' => __( 'Choose the external access mode that will take priority: curl or file_get_content.', 'colissimo-delivery-integration' ),
                'desc_tip' => __( 'Choose the external access mode that will take priority: curl or file_get_contents. Curl is the default choice, but some installations may be unstable with this choice.', "colissimo-delivery-integration" ),
                'id'   => 'wc_settings_tab_colissimo_getcontentmode'
            ),
        ) ;
        if (WC_function_Colissimo::cdi_isconnected()) {
          eval (WC_function_Colissimo::cdi_eval('7')) ;
          $returnext = array (
           'Encryption cdistore' => array(
                'name' => __( 'Cdistore storage','colissimo-delivery-integration' ), 
                'type' => 'checkbox',
                'default' => 'no',
                'desc' => __( 'Encryption of file when inserted in cdistore for additional security','colissimo-delivery-integration' ),
                'id'   => 'wc_settings_tab_colissimo_encryptioncdistore'
            ),
            'Max items logistics documents' => array(
                'type' => 'number',
                'default' => '100',
                'desc' => __( 'Maximum number for logistics documents and for processed items in logistics documents', 'colissimo-delivery-integration' ),
                'desc_tip' => __( "The default value is 100 (for parcels selected and for historic logistic documents). Too many could unnecessarily overload the merchant site and the server sites it calls.", "colissimo-delivery-integration" ),
                'id'   => 'wc_settings_tab_colissimo_maxitemlogistic'
            ),
            'Parcel reference' => array(
                'name' => __( 'Parcel references', 'colissimo-delivery-integration' ),
                'type' => 'select',
                'options' => array (
                  'orderid'      => __('Order Id', 'colissimo-delivery-integration'),
                  'ordernumber' => __('Order Number', 'colissimo-delivery-integration'),
                  ),
                'default' => 'Order Id',
                'desc' => __( 'Choice of the reference to apply to package labels: by default "Order Id" which is the standard numbering of Woocommerce orders, or "Order Number" which is a personalization of the numbering of WC orders (by a specialized plugin).', 'colissimo-delivery-integration' ),
                'id'   => 'wc_settings_tab_colissimo_parcelreference'
            ),
            'Parcel ref in adresse' => array(
                'type' => 'checkbox',
                'default' => 'yes',
                'desc' => __( 'Check to add the parcel reference to the name of the company addressed in the parcel label.', 'colissimo-delivery-integration' ),
                'desc_tip' => __( 'The package reference and the company name in the label can also be configured using CDI filters.', "colissimo-delivery-integration" ),
                'id'   => 'wc_settings_tab_colissimo_companyandorderref'
            ),
            'Parcel to gateway' => array(
                'name' => __( 'Automated sequences', 'colissimo-delivery-integration' ),
                'type' => 'checkbox',
                'default' => 'no',
                'desc' => __( 'Automatically insert a parcel into the gateway for each order (performed during Woocommerce order list display).', 'colissimo-delivery-integration' ),
                'id'   => 'wc_settings_tab_colissimo_autoparcel_gateway'
            ),
           'Parcel to gateway Shipping Method list' => array(
                'type' => 'text',
                'css'  => 'width:70%;',
                'desc' => __( 'Optional when parcel to gateway is selected- Comma separated list of shipping method names for which orders must automatically produce a parcel in gateway. May be methods of Colissimo shipping or external methods. Ex: "colissimo_shippingzone_method_home5, free_shipping". When blank, all shipping methods will be elligible to create a parcel.', 'colissimo-delivery-integration' ),
                'desc_tip' => __( "Here, define the shipping methods for which orders will generate a parcel in the gateway, or blank to select all shipping methods", "colissimo-delivery-integration" ),
                'id'   => 'wc_settings_tab_colissimo_autoparcel_shippinglist'
            ) ,
           'Nettoyage automatique' => array(
                'type' => 'checkbox',
                'default' => 'yes',
                'desc' => __( 'Auto clean of Colissimo parcels in gateway when "In truck" status', 'colissimo-delivery-integration' ),
                'id'   => 'wc_settings_tab_colissimo_autoclean_gateway'
            ),
            'Order completed when parcel intruck' => array(
                'type' => 'checkbox',
                'default' => 'no',
                'desc' => __( 'Automatically pass orders in "completed" status when parcel is in "intruck" status (performed during Woocommerce order list display).', 'colissimo-delivery-integration' ),
                'id'   => 'wc_settings_tab_colissimo_autocompleted_intruck'
            ),
          ) ;
          eval (WC_function_Colissimo::cdi_eval('13')) ;
          eval (WC_function_Colissimo::cdi_eval('12')) ;
        }else{
          $return = array_merge ($return ,array (
            'Nettoyage automatique' => array(
                'name' => __( 'Automated sequences', 'colissimo-delivery-integration' ),
                'type' => 'checkbox',
                'default' => 'yes',
                'desc' => __( 'Auto clean of Colissimo parcels in gateway when "In truck" status', 'colissimo-delivery-integration' ),
                'id'   => 'wc_settings_tab_colissimo_autoclean_gateway'
            ),
          ) ) ;
        }
        return array_merge ($return , array(
            'Contract Number' => array(
                'name' => __( 'Réservé aux adhérents CDI+', 'colissimo-delivery-integration' ),
                'type' => 'text',
                'custom_attributes' => array (
                  'pattern' => '[0-9]{12,12}',
                  ),
                'default' => '',
                'desc' => __( 'CDI+ contract number (12 numerics)', 'colissimo-delivery-integration' ),
                'desc_tip' => __( "Si vous êtes adhérent à CDI+, indiquez ici votre numéro de contrat.", "colissimo-delivery-integration" ),
                'id'   => 'wc_settings_tab_colissimo_cdiplus_ContractNumber'
            ),
            'Authentification' => array(
                'type' => 'text',
                'default' => '',
                'desc' => __( 'CDI+ authentification', 'colissimo-delivery-integration' ), 
                'desc_tip' => __( "Si vous êtes abonné à CDI+, indiquez ici votre clé authentification CDI+", "colissimo-delivery-integration" ),
                'id'   => 'wc_settings_tab_colissimo_cdiplus_Password'
            ),
            'section_end' => array(
                 'type' => 'sectionend',
                 'id' => 'wc_settings_tab_colissimo_section_general_end'
            )
          ) ) ;
    }

//  Get the settings of Section cn23
    public static function get_settings_section_cn23() {
        return array(
            'section_title' => array(
                'name'     => __( 'Colissimo Delivery Integration - CN23 default settings', 'colissimo-delivery-integration' ),
                'type'     => 'title',
                'desc'     => '',
                'id'       => 'wc_settings_tab_colissimo_section_cn23'
            ),
            'CN23 section mark' => array(
                'css'  => 'display:none;',
                'type' => 'number',
                'default' => '1',
                'id'   => 'wc_settings_tab_colissimo_section-cn23'
            ),
            'CN23 default' => array(
                'type' => 'select',
                'options' => array (
                  '1' => __('Gift', 'colissimo-delivery-integration'),
                  '2' => __('Sample', 'colissimo-delivery-integration'),
                  '3' => __('Commercial', 'colissimo-delivery-integration'),
                  '4' => __('Documents', 'colissimo-delivery-integration'),
                  '5' => __('Other', 'colissimo-delivery-integration'),
                  '6' => __('Returned goods', 'colissimo-delivery-integration'),
                  ),
                'default' => '3',
                'desc' => __( 'Default CN23 Category', 'colissimo-delivery-integration' ),
                'desc_tip' => __( "Give the nature of the contents of the package in CN23 codification. CN23 is an internationally recognized standard, to codify customs declarations. In France, a CN23 is required for all shipments abroad and the TOM-DOM except: DE, AT, BE, BG, CY, DK ES  EE, FI, E, GR, HU, IE, IT, LV, LT, LU, MT, NL,  PL , PT , CZ , RO , GB , IE , SK , SI , SE.", "colissimo-delivery-integration" ),
                'id'   => 'wc_settings_tab_colissimo_cn23_category'
            ),
            'CN23 Article Description' => array(
                'type' => 'text',
                'desc' => __( 'Default CN23 Description of article (blank = copied from product order)', 'colissimo-delivery-integration' ),
                'desc_tip' => __( "Give the the default description of articles in the parcel. Up to 10 items/products can be included in a CDI CN23.
If null or 0,  product title, product weight, product quantity, and product price in the order will be considered. ", "colissimo-delivery-integration" ),
                'id'   => 'wc_settings_tab_colissimo_cn23_article_description'
            ),
            'CN23 Article Weight' => array(
                'type' => 'number',
                'default' => '0',
                'desc' => __( 'Default CN23 Net weight in grams of one article (0 = copied from product order)', 'colissimo-delivery-integration' ),
                'id'   => 'wc_settings_tab_colissimo_cn23_article_weight'
            ),
            'CN23 Article Quantity' => array(
                'type' => 'number',
                'default' => '0',
                'desc' => __( 'Default CN23 Number of articles in the parcel (0 = copied from product order)', 'colissimo-delivery-integration' ),
                'id'   => 'wc_settings_tab_colissimo_cn23_article_quantity'
            ),
            'CN23 Article Value' => array(
                'type' => 'number',
                'default' => '0',
                'desc' => __( 'Default CN23 ex VAT Value in € of one article (0 = copied from product order)', 'colissimo-delivery-integration' ),
                'id'   => 'wc_settings_tab_colissimo_cn23_article_value'
            ),
            'CN23 Article HStariffnumber' => array(
                'type' => 'text',
                'default' => '620630',
                'custom_attributes' => array (
                  'pattern' => '[0-9]{4,6}',
                  ),
                'desc' => __( 'Default CN23 HS tariff code- 4 to 6 digits (only if "Commercial" category)', 'colissimo-delivery-integration' ) . ' - <a href="https://pro.douane.gouv.fr/" target="_blank">HS Tariff code</a>',
                'desc_tip' => __( "HS Tariff is an internationally recognized standard, to codify customs declarations. This code is required only for commercial shipment", "colissimo-delivery-integration" ),
                'id'   => 'wc_settings_tab_colissimo_cn23_article_hstariffnumber'
            ),
            'CN23 Article OriginCountry' => array(
                'type' => 'single_select_country',
                'default' => 'FR',
                'desc' => __( 'Default CN23 ISO code of origine country (for Customs)', 'colissimo-delivery-integration' ),
                'desc_tip' => __( "Required by some customs : the 2 letters ISO code of origine country of the item", "colissimo-delivery-integration" ),
                'id'   => 'wc_settings_tab_colissimo_cn23_article_origincountry'
            ),
            'Max items cn23' => array(
                'type' => 'number',
                'default' => '100',
                'desc' => __( 'Maximum number for cn3 articles in metabox', 'colissimo-delivery-integration' ),
                'desc_tip' => __( "The default value is 100. You must choose a raisonnable value, lower than 100 if possible. Too many could unnecessarily overload the merchant site at any change inside metabox.", "colissimo-delivery-integration" ),
                'id'   => 'wc_settings_tab_colissimo_maxitemcn23'
            ),
            'CN23 EORI' => array(
                'name' => __( 'Sender EORI code', 'colissimo-delivery-integration' ),
                'type' => 'text',
                'default' => '',
                'custom_attributes' => array (
                  'pattern' => '^FR[0-9]{14,14}',
                  ),
                'desc' => __( 'Optionnal : Sender EORI (Economic Operator Registration and Identification) to be marked in cn23 according to some regulations', 'colissimo-delivery-integration' ),
                'desc_tip' => __( "EORI : European regulations provide a unique Community identifier number for use by international economic operators.", "colissimo-delivery-integration" ),
                'id'   => 'wc_settings_tab_colissimo_cn23_eori'
            ),
           'EU countries without cn23' => array(
                'name' => __( 'EU Countries exempted', 'colissimo-delivery-integration' ),
                'type' => 'text',
                'css'  => 'width:70%; color:red;',
                'default' => wc_settings_tab_colissimo_ws_Nocn23ContryCodes,
                'desc' => __( 'Comma separated list of EU country codes exempted of cn23 documents. Be aware that this list is from the European rules.', 'colissimo-delivery-integration' ),
                'desc_tip' => __( "Here, is the list of EU country codes exempted of cn23 documents. Be aware that this list is from the European rules.", "colissimo-delivery-integration" ),
                'id'   => 'wc_settings_tab_colissimo_ws_Nocn23ContryCodes'
            ) ,
           'Cn23 zipcode exemptions' => array(
                'name' => __( 'EU zipcode exemptions', 'colissimo-delivery-integration' ),
                'type' => 'text',
                'css'  => 'width:70%; color:red;',
                'default' => wc_settings_tab_colissimo_Cn23ZipcodeExemptions,
                'desc' => __( 'Semicolon separated list of relations "country-code=list of zip-code" for which some territories, imbedded in a EU state, do not get the EU cn23 exemption. This list is useful only for the territories without an ISO country code. Be aware that this list is from the European rules.', 'colissimo-delivery-integration' ),
                'desc_tip' => __( 'Here, semicolon separated list of relations "country-code=list of zip-code" for which some territories, imbedded in a EU state, do not get the EU cn23 exemption. This list is useful only for the territories without an ISO country code. Be aware that this list is from the European rules.', "colissimo-delivery-integration" ),
                'id'   => 'wc_settings_tab_colissimo_Cn23ZipcodeExemptions'
            ) ,
            'Country code return' => array(
                'name' => __( 'Country without no delivery choice', 'colissimo-delivery-integration' ),           
                'type' => 'text',
                'css'  => 'width:70%; color:red;',
                'default' => wc_settings_tab_colissimo_country_Nochoiceparcelreturn,
                'desc' => __( 'Comma separated list of 2 digits country codes which cant let a choice for a parcel return in case of no delivery.', 'colissimo-delivery-integration' ),
                'desc_tip' => __( "Here, the list of country codes (2 digits) which cant let a choice for a parcel return in case of no delivery. The  standard list can be updated for future new countries.", "colissimo-delivery-integration" ),
                'id'   => 'wc_settings_tab_colissimo_country_Nochoiceparcelreturn'
            ), 
            'section_end' => array(
                 'type' => 'sectionend',
                 'id' => 'wc_settings_tab_colissimo_section_cn23_end'
            )
        );
    }

//  Get the settings of Section tracking
    public static function get_settings_section_tracking() {
        $return = array(
            'section_title' => array(
                'name'     => __( 'Colissimo Delivery Integration - Tracking settings', 'colissimo-delivery-integration' ),
                'type'     => 'title',
                'desc'     => '',
                'id'       => 'wc_settings_tab_colissimo_section_tracking'
            ),
            'Tracking section mark' => array(
                'css'  => 'display:none;',
                'type' => 'number',
                'default' => '1',
                'id'   => 'wc_settings_tab_colissimo_section-tracking'
            ),
            'Tracking code to customer' => array(
                'name' => __( 'Tracking code to customer', 'colissimo-delivery-integration' ),
                'type' => 'select',
                'options' => array (
                  'no'                     => __( 'nothing', 'colissimo-delivery-integration' ),
                  'emails'                 => __( 'emails', 'colissimo-delivery-integration' ),
                  'order-views'            => __( 'order-views', 'colissimo-delivery-integration' ),
                  'emails and order-views' => __( 'emails and order-views', 'colissimo-delivery-integration' ),
                  ),
                'default' => 'emails and order-views',
                'desc' => __( 'Insert tracking code and pickup location in Customer emails and/or Customer order views', 'colissimo-delivery-integration' ),
                'desc_tip' => __( "You can choose to insert customer informations in the customer  mails and / or the customer order views. Information shown are the tracking code and the pickup location the customer has choosen.", "colissimo-delivery-integration" ),
                'id'   => 'wc_settings_tab_colissimo_inserttrackingcode'
            ),
            'Text preceding tracking code' => array(
                'type' => 'text',
                'css'  => 'width:70%;',
                'default' => __( 'Order shipped. Your tracking code is : ', 'colissimo-delivery-integration' ),
                'desc' => __( 'Text preceding tracking code', 'colissimo-delivery-integration' ),
                'desc_tip' => __( "Here, the text you want the customer to see just before the tracking code", "colissimo-delivery-integration" ),
                'id'   => 'wc_settings_tab_colissimo_text_preceding_trackingcode'
            ),
            'Url tracking code' => array(
                'type' => 'text',
                'css'  => 'width:70%;',
                'default' => 'http://www.colissimo.fr/portail_colissimo/suivre.do?colispart=',
                'desc' => __( 'Url for tracking code', 'colissimo-delivery-integration' ),
                'desc_tip' => __( "Here, the standard url of your carrier. Don't change it if you don't known its url.", "colissimo-delivery-integration" ),
                'id'   => 'wc_settings_tab_colissimo_url_trackingcode'
            )
        );
        if (WC_function_Colissimo::cdi_isconnected()) {
          eval (WC_function_Colissimo::cdi_eval('7')) ;
          $returnext =  array(
            'Tracking email location' => array(
                'type' => 'select',
                'options' => array (
                  'after'       => __( 'After the order details', 'colissimo-delivery-integration' ),
                  'before'      => __( 'Before the order details', 'colissimo-delivery-integration' ),
                  ),
                'default' => 'after',
                'desc' => __( 'Location in emails where the tracking infos must be', 'colissimo-delivery-integration' ),
                'desc_tip' => __( "You can choose the location in emails where the tracking infos must be. The defaut is 'after' the order details because it is more consistant in display. The choice 'before'  is to be immediatly seen by the consumer.", "colissimo-delivery-integration" ),
                'id'   => 'wc_settings_tab_colissimo_trackingemaillocation'
              ), 
            'Add S1 Hub Armees country' => array(
                'name' => __( 'Extend WC countries list with "S1 - Envoi vers les Armées" country', 'colissimo-delivery-integration' ),
                'type' => 'checkbox',
                'default' => 'no',
                'desc' => __( 'Extend the WC countries list with "S1 - Envoi vers les Armées" country. This S1 country must be refered to in a WC internationnal shipping zone. It must be used according to LaPoste specifications for army hub.', 'colissimo-delivery-integration' ),
                'id'   => 'wc_settings_tab_colissimo_extentS1contry'
              ), 
            'Extented WC address' => array(
                'name' => __( 'Extend WC addresses to postal standarts', 'colissimo-delivery-integration' ),
                'type' => 'checkbox',
                'default' => 'no',
                'desc' => __( 'Extend the Woocommerce addresses to 4 lines as the standarts used by LaPoste-Colissimo', 'colissimo-delivery-integration' ),
                'id'   => 'wc_settings_tab_colissimo_extentedaddress'
              ), 
            'La Poste ControlAdresse API key' => array(
                'type' => 'text',
                'css'  => 'width:70%;',
                'default' => '',
                'desc' => __( 'La Poste ControlAdresse API key. Key to get at https://developer.laposte.fr/', 'colissimo-delivery-integration' ),
                'desc_tip' => __( "Here, the La Poste ControlAdresse API key to be authorize to ask address control.", "colissimo-delivery-integration" ),
                'id'   => 'wc_settings_tab_colissimo_apikeylaposte'
              ), 
          ) ;
          eval (WC_function_Colissimo::cdi_eval('13')) ;
          eval (WC_function_Colissimo::cdi_eval('12')) ;
        }
        return array_merge ($return , array(
            'section_end' => array(
                 'type' => 'sectionend',
                 'id' => 'wc_settings_tab_colissimo_section_tracking_end'
              )
         ) ) ;
    }


//  Get the settings of Section automatic
    public static function get_settings_section_automatic() {
        return array(
            'section_title' => array(
                'name'     => __( 'Colissimo Delivery Integration - Automatic Mode (Web Service) settings', 'colissimo-delivery-integration' ),
                'type'     => 'title',
                'desc'     => '',
                'id'       => 'wc_settings_tab_colissimo_section_automatic'
            ),
            'Automatic section mark' => array(
                'css'  => 'display:none;',
                'type' => 'number',
                'default' => '1',
                'id'   => 'wc_settings_tab_colissimo_section-automatic'
            ),
            'Contract Number' => array(
                'name' => __( 'General', 'colissimo-delivery-integration' ),
                'type' => 'text',
                'default' => '123456',
                'desc' => __( 'Web Service - Contract Number', 'colissimo-delivery-integration' ),
                'desc_tip' => __( "Your Colissimo contrat number including the option 'Web Service (Flexibilité) pour livraison et étiquetage'.", "colissimo-delivery-integration" ),
                'id'   => 'wc_settings_tab_colissimo_ws_ContractNumber'
            ),
            'Password' => array(
                'type' => 'text',
                'default' => 'PWD123',
                'desc' => __( 'Web Service - Password', 'colissimo-delivery-integration' ), 
                'desc_tip' => __( "Your password at your Colissimo contrat", "colissimo-delivery-integration" ),
                'id'   => 'wc_settings_tab_colissimo_ws_Password'
            ),
            'Output Format - Offset X' => array(
                'type' => 'number',
                'default' => '0',
                'desc' => __( 'Web Service - Output Format - Offset X in pixels', 'colissimo-delivery-integration' ),
                'desc_tip' => __( "The 3 following datas are Colissimo settings for the printing.", "colissimo-delivery-integration" ),
                'id'   => 'wc_settings_tab_colissimo_ws_X'
            ),
            'Output Format - Offset Y' => array(
                'type' => 'number',
                'default' => '0',
                'desc' => __( 'Web Service - Output Format - Offset Y in pixels', 'colissimo-delivery-integration' ),
                'id'   => 'wc_settings_tab_colissimo_ws_Y'
            ),
            'Output Format - Printing Type' => array(
                'type' => 'select',
                'options' => array (
                  'PDF_10x15_300dpi' => 'PDF_10x15_300dpi',
                  'PDF_A4_300dpi' => 'PDF_A4_300dpi',
                  ),
                'default' => 'PDF_A4_300dpi',
                'desc' => __( 'Web Service - Output Format - Printing Type (DPL & ZPL not supported)', 'colissimo-delivery-integration' ),
                'id'   => 'wc_settings_tab_colissimo_ws_OutputPrintingType'
            ),
            'Offset Deposit Date' => array(
                'type' => 'number',
                'default' => '2',
                'custom_attributes' => array (
                  'min'=>'1',
                  'max'=>'10'
                  ),
                'desc' => __( 'Web Service - Offset Deposit Date (estimate in days after running the web service - Must be > 0)', 'colissimo-delivery-integration' ),
                'desc_tip' => __( "Estimate period in days to deposit yours parcels. Useful for La Poste.", "colissimo-delivery-integration" ),
                'id'   => 'wc_settings_tab_colissimo_ws_OffsetDepositDate'
            ),
            'return CN23 labels' => array(
                'type' => 'checkbox',
                'default' => 'yes',
                'desc' => __( 'Include CN23 customs declarations with returned labels', 'colissimo-delivery-integration' ),
                'id'   => 'wc_settings_tab_colissimo_IncludeCustomsDeclarations'
            ),
            'Sender Address - Company Name' => array(
                'name' => __( 'Sender address', 'colissimo-delivery-integration' ),
                'type' => 'text',
                'default' => 'The CDI Company',
                'desc' => __( 'Web Service - Sender Address - Company Name (May be same as website name)', 'colissimo-delivery-integration' ),
                'desc_tip' => __( "The 6 following datas define your address as sender. This address will be on the parcels.", "colissimo-delivery-integration" ),
                'id'   => 'wc_settings_tab_colissimo_ws_sa_CompanyName'
            ),
            'Sender Address - Line 1' => array(
                'type' => 'text',
                'default' => 'Mandatory - num and street',
                'desc' => __( 'Web Service - Sender Address - Mandatory num and street', 'colissimo-delivery-integration' ),
                'id'   => 'wc_settings_tab_colissimo_ws_sa_Line1'
            ),
            'Sender Address - Line 2' => array(
                'type' => 'text',
                'default' => 'Optionnal - other infos',
                'desc' => __( 'Web Service - Sender Address - Optionnal other infos', 'colissimo-delivery-integration' ),
                'id'   => 'wc_settings_tab_colissimo_ws_sa_Line2'
            ),
            'Sender Address - ZipCode' => array(
                'type' => 'text',
                'custom_attributes' => array (
                  'pattern' => '[0-9]{5,5}',
                  ),
                'default' => '75001',
                'desc' => __( 'Web Service - Sender Address - ZipCode', 'colissimo-delivery-integration' ),
                'id'   => 'wc_settings_tab_colissimo_ws_sa_ZipCode'
            ),
            'Sender Address - City' => array(
                'type' => 'text',
                'default' => 'PARIS',
                'desc' => __( 'Web Service - Sender Address - City', 'colissimo-delivery-integration' ),
                'id'   => 'wc_settings_tab_colissimo_ws_sa_City'
            ),
            'Sender Address - Country Code' => array(
                'type' => 'single_select_country',
                'default' => 'FR',
                'desc' => __( 'Web Service - Sender Address - Country Code', 'colissimo-delivery-integration' ),
                'id'   => 'wc_settings_tab_colissimo_ws_sa_CountryCode'
            ),
            'Sender Address - Email' => array(
                'type' => 'email',
                'default' => 'a@b.fr',
                'desc' => __( 'Web Service - Sender Address - Email', 'colissimo-delivery-integration' ),
                'desc_tip' => __( "Your email address as sender of the parcel.", "colissimo-delivery-integration" ),
                'id'   => 'wc_settings_tab_colissimo_ws_sa_Email'
            ),
            'France Country Codes' => array(
                'name' => __( 'France Zone', 'colissimo-delivery-integration' ),
                'type' => 'text',
                'css'  => 'width:50%; color:red;',
                'default' => wc_settings_tab_colissimo_ws_FranceCountryCodes,
                'desc' => __( 'ISO country codes for Colissimo France zone', 'colissimo-delivery-integration' ),
                'desc_tip' => __( "This defines 1- the list of ISO country codes for Colissimo France zone, and 2- the Colissimo product codes with France as destination, for the services : without signature, with signature, pickup location. Theses codes are used only if no product code is set in the order meta box.", "colissimo-delivery-integration" ),
                'id'   => 'wc_settings_tab_colissimo_ws_FranceCountryCodes'
            ),
            'France Product Codes' => array(
                'type' => 'text',
                'css'  => 'width:20%; margin-left:30%; color:red;',
                'default' => wc_settings_tab_colissimo_ws_FranceProductCodes,
                'desc' => __( 'Product Codes for France zone', 'colissimo-delivery-integration' ),
                'id'   => 'wc_settings_tab_colissimo_ws_FranceProductCodes'
            ),
            'Outre-mer Country Codes' => array(
                'name' => __( 'Outre-mer Zone', 'colissimo-delivery-integration' ),
                'type' => 'text',
                'css'  => 'width:50%; color:red;',
                'default' => wc_settings_tab_colissimo_ws_OutreMerCountryCodes,
                'desc' => __( 'ISO country codes for Colissimo Outre-mer zone', 'colissimo-delivery-integration' ),
                'desc_tip' => __( "This defines 1- the list of ISO country codes for Colissimo Outre-mer zone, and 2- the Colissimo product codes with Outre Mer as destination, for the services : without signature, with signature, pickup location. Theses codes are used only if no product code is set in the order meta box.", "colissimo-delivery-integration" ),
                'id'   => 'wc_settings_tab_colissimo_ws_OutreMerCountryCodes'
            ),
            'Outre Mer Product Codes' => array(
                'type' => 'text',
                'css'  => 'width:20%; margin-left:30%; color:red;',
                'default' => wc_settings_tab_colissimo_ws_OutreMerProductCodes,
                'desc' => __( 'Product Codes for Outre-mer zone', 'colissimo-delivery-integration' ),
                'id'   => 'wc_settings_tab_colissimo_ws_OutreMerProductCodes'
            ),
            'Europe Country Codes' => array(
                'name' => __( 'Europe Zone', 'colissimo-delivery-integration' ),
                'type' => 'text',
                'css'  => 'width:50%; color:red;',
                'default' => wc_settings_tab_colissimo_ws_EuropeCountryCodes,
                'desc' => __( 'ISO country codes for Colissimo Europe zone', 'colissimo-delivery-integration' ),
                'desc_tip' => __( "This defines 1- the list of ISO country codes for Colissimo Europe zone, and 2- the Colissimo product codes with Europe as destination, for the services : without signature, with signature, pickup location. Theses codes are used only if no product code is set in the order meta box.", "colissimo-delivery-integration" ),
                'id'   => 'wc_settings_tab_colissimo_ws_EuropeCountryCodes'
            ),
            'Europe Product Codes' => array(
                'type' => 'text',
                'css'  => 'width:20%; margin-left:30%; color:red;',
                'default' => wc_settings_tab_colissimo_ws_EuropeProductCodes,
                'desc' => __( 'Product Codes for Europe zone', 'colissimo-delivery-integration' ),
                'id'   => 'wc_settings_tab_colissimo_ws_EuropeProductCodes'
            ),
            'International Country Codes' => array(
                'name' => __( 'International Zone', 'colissimo-delivery-integration' ),
                'type' => 'text',
                'css'  => 'width:50%; color:red;',
                'default' => wc_settings_tab_colissimo_ws_InternationalCountryCodes,
                'desc' => __( 'ISO country codes for Colissimo International zone', 'colissimo-delivery-integration' ),
                'desc_tip' => __( "This defines 1- the list of ISO country codes for Colissimo International zone, and 2- the Colissimo product codes with International as destination, for the services : without signature, with signature, pickup location. Theses codes are used only if no product code is set in the order meta box.", "colissimo-delivery-integration" ),
                'id'   => 'wc_settings_tab_colissimo_ws_InternationalCountryCodes'
            ),
            'International Product Codes' => array(
                'type' => 'text',
                'css'  => 'width:20%; margin-left:30%; color:red;',
                'default' => wc_settings_tab_colissimo_ws_InternationalProductCodes,
                'desc' => __( 'Product Codes for International zone', 'colissimo-delivery-integration' ),
                'id'   => 'wc_settings_tab_colissimo_ws_InternationalProductCodes'
            ),
            'Exception Product Codes' => array(
                'name' => __( 'Exception product codes', 'colissimo-delivery-integration' ),
                'type' => 'text',
                'css'  => 'color:red;',
                'default' => wc_settings_tab_colissimo_ws_ExceptionProductCodes,
                'desc' => __( 'Web Service - Comma separated list of "code_to_replace=new_code_to_use" just before the call of Colissimo WS (ex DOM=COLD,DOS=COL)', 'colissimo-delivery-integration' ),
                'desc_tip' => __( "This defines Colissimo product codes in exception which have to be replace just before the call of Colissimo Web Service. Generally speaking, the choice of a product code is done in this order : 1) the code for a shipping method as defined in referal, 2) the code given by Colissimo for a pickup location, 3) the code manually forced in meta box order, 4) optionnally if still null in meta box, the code defines in settings for France, Outre mer, International, 5) and finally the exception code rule which has the greatest priority. ", "colissimo-delivery-integration" ),
                'id'   => 'wc_settings_tab_colissimo_ws_ExceptionProductCodes'
            ),
            'section_end' => array(
                 'type' => 'sectionend',
                 'id' => 'wc_settings_tab_colissimo_section_automatic_end'
            )
        );
    }

//  Get the settings of Section referrals
    public static function get_settings_section_referrals() {
        if (WC_function_Colissimo::cdi_isconnected()) {
          eval (WC_function_Colissimo::cdi_eval('7')) ;
          $return = array(
            'section_title' => array(
                'name'     => __( 'Colissimo Delivery Integration - Referrals to shipping methods', 'colissimo-delivery-integration' ),
                'type'     => 'title',
                'desc'     => '',
                'id'       => 'wc_settings_tab_colissimo_section_referrals'
            ),
            'Referrals section mark' => array(
                'css'  => 'display:none;',
                'type' => 'number',
                'default' => '1',
                'id'   => 'wc_settings_tab_colissimo_section-referrals'
            ),
            'Method Referal' => array(
                'type' => 'checkbox',
                'default' => 'yes',
                'desc' => __( 'Global enable of referrals to shipping method.', 'colissimo-delivery-integration' ),
                'desc_tip' => __( "Shipping method referrals enables to set 3 qualifications to shipping methods presented to customer : - shipping methods for which the Pickup locations function must be activate, - the Colissimo product codes to associate to shipping methods, - shipping methods which are exclusive.", "colissimo-delivery-integration" ),
                'id'   => 'wc_settings_tab_colissimo_methodreferal'
            ) ,
            'pickup_method_name' => array(
                'name' => __( 'Pickup locations', 'colissimo-delivery-integration' ),
                'type' => 'text',
                'css'  => 'width:70%;',
                'default' => 'colissimo_shippingzone_method_pick1=1, colissimo_shippingzone_method_pick2=0',
                'desc' => __( 'Optional - Comma separated list of "Shipping-Method-names = filter-relay" which activate the Colissimo pickup location choice process. Filter-relay = 0 or 1 to define type of Colissimo list. May be methods of Colissimo shipping or external methods. Selection may focus on a specific instance as "flat_rate:25=1" ', 'colissimo-delivery-integration' ),
                'desc_tip' => __( "Here, define the shipping methods for which the Pickup locations function must be activate. They must be shipping zone methods (WC >=2.6)", "colissimo-delivery-integration" ),
                'id'   => 'wc_settings_tab_colissimo_pickupmethodnames'
            ) ,
            'Pickup excluded when offline' => array(
                'type' => 'checkbox',
                'default' => 'yes',
                'desc' => __( 'Not show pickup tariffs when offline (no access to outgoing IP, LaPoste server, or CDI server).', 'colissimo-delivery-integration' ),
                'id'   => 'wc_settings_tab_colissimo_pickupoffline'
            ) ,
            'International pickup location - Country Codes' => array(
                'type' => 'text',
                'css'  => 'color:red;',
                'default' => wc_settings_tab_colissimo_ws_InternationalPickupLocationContryCodes,
                'desc' => __( 'Pickup location country codes (excluding X00 network)', 'colissimo-delivery-integration' ),
                'desc_tip' => __( "This defines the list of destination countries for which Colissimo runs its pickup location service.", "colissimo-delivery-integration" ),
                'id'   => 'wc_settings_tab_colissimo_ws_InternationalPickupLocationContryCodes'
            ),
            'International home without signature - Country Codes' => array(
                'type' => 'text',
                'css'  => 'color:red;',
                'default' => wc_settings_tab_colissimo_ws_InternationalWithoutSignContryCodes,
                'desc' => __( 'Country codes for which is authorized a without signature Colissimo.', 'colissimo-delivery-integration' ),
                'desc_tip' => __( "This defines the list of country codes for which is authorized a without signature Colissimo.", "colissimo-delivery-integration" ),
                'id'   => 'wc_settings_tab_colissimo_ws_InternationalWithoutSignContryCodes'
            ),
            'Pickup location map open' => array(
                'type' => 'checkbox',
                'default' => 'yes',
                'desc' => __( 'Pickup location map shown open at entry of selected method.', 'colissimo-delivery-integration' ),
                'id'   => 'wc_settings_tab_colissimo_mapopen'
            ) ,
            'Pickup location map refresh' => array(
                'type' => 'checkbox',
                'default' => 'no',
                'desc' => __( 'Map periodic refresh for themes with multi views in woocommerce checkout page (to use only when necessary).', 'colissimo-delivery-integration' ),
                'id'   => 'wc_settings_tab_colissimo_maprefresh'
            ) ,
            'Pickup location mode selectclickonmap' => array(
                'type' => 'checkbox',
                'default' => 'no',
                'desc' => __( 'Pickup location mode to select location by click on map. Warning, this option may not work with some themes, plugins, and/or browser.', 'colissimo-delivery-integration' ),
                'id'   => 'wc_settings_tab_colissimo_selectclickonmap'
            ) ,
            'Pickup location mode wheremustbeemap' => array(
                'type' => 'select',
                'options' => array (
                  'insertBefore( ".shop_table" )' => __('Before shop box', 'colissimo-delivery-integration'),
                  'insertAfter( ".shop_table" )'  => __('After shop box', 'colissimo-delivery-integration'),
                  'insertBefore( "#payment" )' => __('Before payment', 'colissimo-delivery-integration'),
                  'insertAfter( "#payment" )'  => __('After payment', 'colissimo-delivery-integration'),
                  'insertBefore( "#order_review" )' => __('Before order review', 'colissimo-delivery-integration'),
                  'insertAfter( "#order_review" )'  => __('After order review', 'colissimo-delivery-integration'),

                  ),
                'default' => 'Before shop box',
                'desc' => __( 'Pickup location to choose where must be map in checkout page. Warning, this option may not work with some themes, plugins, and/or browser.', 'colissimo-delivery-integration' ),
                'id'   => 'wc_settings_tab_colissimo_wheremustbeemap'
            ) ,
            'Pickup location map engine' => array(
                'type' => 'select',
                'options' => array (
                  'gm' => __('Google Maps', 'colissimo-delivery-integration'),
                  'om'  => __('Open Map', 'colissimo-delivery-integration'),
                  ),
                'default' => 'om',
                'desc' => __( 'Pickup location map engine to choose. Google Maps needs an API key that you must place in field below. The alternative called Open Map is build with 3 services : Open Layers, Open Street Map, and Nominatim, which are open and free.', 'colissimo-delivery-integration' ),
                'id'   => 'wc_settings_tab_colissimo_mapengine'
            ) ,
            'googlemap api key' => array(
                'type' => 'text',
                'css'  => 'width:70%;',
                'default' => '',
                'desc' => __( 'Optional - Google maps API key', 'colissimo-delivery-integration' ),
                'desc_tip' => __( "Here, set your Google maps API key. Without a key, you will have a restricted use of Google maps.", "colissimo-delivery-integration" ),
                'id'   => 'wc_settings_tab_colissimo_googlemapsapikey'
            ) ,
           'forced_product_code' => array(
                'name' => __( 'Associated product codes', 'colissimo-delivery-integration' ),
                'type' => 'text',
                'css'  => 'width:70%;',
                'default' => 'colissimo_shippingzone_method_home1=DOM, colissimo_shippingzone_method_home2=DOS',
                'desc' => __( 'Optional - Comma separated list of relations "Method-name = Colissimo-product-code" to be use for Colissimo. May be methods of Colissimo shipping or external methods.', 'colissimo-delivery-integration' ),
                'desc_tip' => __( "Here, define the Colissimo product codes to associate to shipping methods.", "colissimo-delivery-integration" ),
                'id'   => 'wc_settings_tab_colissimo_forcedproductcodes'
            ) ,
           'mandatory phone number' => array(
                'name' => __( 'Mandatory phone number', 'colissimo-delivery-integration' ),
                'type' => 'text',
                'css'  => 'width:70%;',
                'default' => 'colissimo_shippingzone_method_pick1, colissimo_shippingzone_method_pick2, colissimo_shippingzone_method_pick3, colissimo_shippingzone_method_pick4, colissimo_shippingzone_method_pick5',
                'desc' => __( 'Optional - Comma separated list of "Method-name" to be use for mandatory phone number. May be methods of Colissimo shipping or external methods.', 'colissimo-delivery-integration' ),
                'desc_tip' => __( "Here, define the Colissimo shipping methods for which you need a mandatory phone number. An * means all shipping methods", "colissimo-delivery-integration" ),
                'id'   => 'wc_settings_tab_colissimo_phonemandatory'
            ) ,
           'Exclusive Shipping Method' => array(
                'name' => __( 'Exclusive shipping methods', 'colissimo-delivery-integration' ),
                'type' => 'text',
                'css'  => 'width:70%;',
                'desc' => __( 'Optional - Comma separated list of shipping method names which are exclusive of others. The priority is given to the first method matching in the original woocommerce package list. May be methods of Colissimo shipping or external methods. Ex: "colissimo_shippingzone_method_home5, free_shipping"', 'colissimo-delivery-integration' ),
                'desc_tip' => __( "Here, define the shipping methods which are exclusive (i.e. which will be alone when presented to customer).", "colissimo-delivery-integration" ),
                'id'   => 'wc_settings_tab_colissimo_exclusiveshippingmethod'
            ) ,
          ) ;
          $returnext =   array(
            'section_end' => array(
                 'type' => 'sectionend',
                 'id' => 'wc_settings_tab_colissimo_section_referrals_end'
            )
          ) ;
          eval (WC_function_Colissimo::cdi_eval('13')) ;
          eval (WC_function_Colissimo::cdi_eval('12')) ;
       }else{
         $return = '' ;
       }
      return $return;  
    }

//  Get the settings of Section shipping
    public static function get_settings_section_shipping() {
        $return = array(
            'section_title' => array(
                'name'     => __( 'Colissimo Delivery Integration - Colissimo method global settings', 'colissimo-delivery-integration' ),
                'type'     => 'title',
                'desc'     => '',
                'id'       => 'wc_settings_tab_colissimo_section_shipping'
            ),
            'Shipping section mark' => array(
                'css'  => 'display:none;',
                'type' => 'number',
                'default' => '1',
                'id'   => 'wc_settings_tab_colissimo_section-shipping'
            ),
            'Shipping Method' => array(
                'type' => 'checkbox',
                'default' => 'yes',
                'desc' => __( 'Global enabling of Colissimo shipping method (Shipping zone mode)', 'colissimo-delivery-integration' ),
                'desc_tip' => __( "You must have installed WooCommerce 2.6 or further to run this shipping method.  Details of settings must be done in each methods [WooCommerce -> Settings -> Shipping].", "colissimo-delivery-integration" ),
                'id'   => 'wc_settings_tab_colissimo_methodshipping'
            ) ,
            'Icon Shipping Method' => array(
                'type' => 'checkbox',
                'default' => 'yes',
                'desc' => __( 'Set shipping method icons in front end.', 'colissimo-delivery-integration' ),
                'desc_tip' => __( "Customization of icons are to do in plugin images directory (refer to examples for size and type) or trought a filter to keep the images in your own directory.", "colissimo-delivery-integration" ),
                'id'   => 'wc_settings_tab_colissimo_methodshippingicon'
            )
        );
        if (WC_function_Colissimo::cdi_isconnected()) {
          eval (WC_function_Colissimo::cdi_eval('7')) ;
          $returnext =  array(
              'Shipping package-in-cart' => array(
                'type' => 'select',
                'options' => array (
                  'first' => __('First shipping package', 'colissimo-delivery-integration'),
                  'last' => __('Last shipping package', 'colissimo-delivery-integration'),
                  'cart' => __('Whole cart', 'colissimo-delivery-integration'),
                  ),
                'default' => 'first',
                'desc' => __( 'If multi shipping packages, select what CDI Gateway must process.', 'colissimo-delivery-integration' ),
                'desc_tip' => __( "If WC multi shipping packages (e.g. a market places activated), you can choose which package is to be process by the CDI Gateway : first package, last package, or whole cart. The defaut is first package.", "colissimo-delivery-integration" ),
                'id'   => 'wc_settings_tab_colissimo_shippingpackageincart'
              ) ,
              'Extend Termid Shipping Method' => array(
                'name' => __( 'Extend Termid list', 'colissimo-delivery-integration' ),
                'type' => 'text',
                'css'  => 'width:70%;',
                'desc' => __( 'Optional - Comma separated list of shipping method Termid which will be added to the standard list. Ex: "home6, home7, shop6, autre1, autre2"', 'colissimo-delivery-integration' ),
                'desc_tip' => __( "Here, extend the shipping methods Termid for your special customization.", "colissimo-delivery-integration" ),
                'id'   => 'wc_settings_tab_colissimo_methodshipping_extendtermid'
              ) 
          ) ;
          eval (WC_function_Colissimo::cdi_eval('13')) ;
          eval (WC_function_Colissimo::cdi_eval('12')) ;
        }
        return array_merge ($return , array(
              'section_end' => array(
                 'type' => 'sectionend',
                 'id' => 'wc_settings_tab_colissimo_section_shipping_end'
              )
         ) ) ;
    }

//  Get the settings of Parcels Return
    public static function get_settings_section_parcelreturn() {
        return array(
            'section_title' => array(
                'name'     => __( 'Colissimo Delivery Integration - Parcel return settings', 'colissimo-delivery-integration' ),
                'type'     => 'title',
                'desc'     => '',
                'id'       => 'wc_settings_tab_colissimo_section_parcelreturn'
            ),
            'Parcelreturn section mark' => array(
                'css'  => 'display:none;',
                'type' => 'number',
                'default' => '1',
                'id'   => 'wc_settings_tab_colissimo_section-parcelreturn'
            ),
            'Parcels return' => array(
                'type' => 'checkbox',
                'default' => 'no',
                'desc' => __( 'Global enabling of Colissimo parcel return function.', 'colissimo-delivery-integration' ),
                'desc_tip' => __( "Logged customers will have the capacity, from their order view, to create and print a Colissimo return label. This feature requires a Bussiness contract with Colissimo to access the Web Service Affranchissement.", "colissimo-delivery-integration" ),
                'id'   => 'wc_settings_tab_colissimo_parcelreturn'
            ) ,
            'Text preceding parcel return request' => array(
                'type' => 'text',
                'css'  => 'width:70%;',
                'default' => __( 'In case you need to return your parcel, request for a  printable Colissimo return label : ', 'colissimo-delivery-integration' ),
                'desc' => __( 'Text preceding customer parcel return label request in their order view', 'colissimo-delivery-integration' ),
                'desc_tip' => __( "Here, the text your customer will see in its order view to invite him to post a request to get a parcel return label. These information will be seen by the customer only if it is inside the authorized period defined below.", "colissimo-delivery-integration" ),
                'id'   => 'wc_settings_tab_colissimo_text_preceding_parcelreturn'
            ),
            'Text preceding parcel return print' => array(
                'type' => 'text',
                'css'  => 'width:70%;',
                'default' => __( 'Your Colissimo return label is available. Paste it on your parcel. After printing, you can choose the type of postal deposit you want at :', 'colissimo-delivery-integration' ),
                'desc' => __( 'Text accompanying the parcel return label print button', 'colissimo-delivery-integration' ),
                'desc_tip' => __( "Here, the text your customer will see to invite him to print its parcel return label.", "colissimo-delivery-integration" ),
                'id'   => 'wc_settings_tab_colissimo_text_preceding_printreturn'
            ),
            'Url following text parcel return print' => array(
                'type' => 'text',
                'css'  => 'width:70%;',
                'default' => 'https://www.colissimo.fr/retourbal/index.htm', 'colissimo-delivery-integration',
                'desc' => __( 'Url following the text parcel return print (when necessary)', 'colissimo-delivery-integration' ),
                'desc_tip' => __( "Here, when necessary, the url your need to follow the parcel return print text. When blank, no url will be shown", "colissimo-delivery-integration" ),
                'id'   => 'wc_settings_tab_colissimo_url_following_printreturn'
            ),
            'Tracking code headers' => array(
                'type' => 'text',
                'css'  => 'width:70%; color:red;',
                'default' => wc_settings_tab_colissimo_trackingheaders_parcelreturn,
                'desc' => __( 'Comma separated list of 2 digits headers of Colissimo tracking codes allowed for a customer parcel return label request.', 'colissimo-delivery-integration' ),
                'desc_tip' => __( "Here, the list of Colissimo tracking codes headers (2 digits) which are allowed for a customer parcel return label request. The  standard list can be updated for future new Colissimo products.", "colissimo-delivery-integration" ),
                'id'   => 'wc_settings_tab_colissimo_trackingheaders_parcelreturn'
            ),
           'Return_product_code' => array(
                'type' => 'text',
                'css'  => 'width:70%; color:red;',
                'default' => wc_settings_tab_colissimo_returnproduct_code,
                'desc' => __( 'Semicolon separated list of relations "Return-product-code=ISO-countrycode list" to be use for parcel returns.', 'colissimo-delivery-integration' ),
                'desc_tip' => __( "Here, define the Colissimo return product codes and associated countries.", "colissimo-delivery-integration" ),
                'id'   => 'wc_settings_tab_colissimo_returnproduct_code'
            ) ,
            'Nb day parcel return' => array(
                'type' => 'number',
                'default' => '+14',
                'desc' => __( 'Number of day after the order creation date+time during whitch a customer parcel return label request is permitted. Setting this number to 0 will allowed customers to use this function only on a case by case basis after the admin has changed this value for the order in the Colissimo metabox.', 'colissimo-delivery-integration' ),
                'desc_tip' => __( "Here, the number of days you allow to your customer to retrieve and print a return parcel label. This number must not be confused which the validity period Colissimo give for a label to be deposit. The default 14 days correspond to the European regulation e-commerce for returns if you offer this facility to your customers.", "colissimo-delivery-integration" ),
                'id'   => 'wc_settings_tab_colissimo_nbdayparcelreturn'
            ),
            'Return Parcel Service' => array(
                'type' => 'text',
                'default' => 'Service des retours',
                'desc' => __( 'Name of the Company service to whitch the parcels must be returned', 'colissimo-delivery-integration' ),
                'desc_tip' => __( "Here, the name of the service in your company that must receice the returned parcel. The rest of the return address is the sender address you set in Web Service Affranchissement settings.", "colissimo-delivery-integration" ),
                'id'   => 'wc_settings_tab_colissimo_returnparcelservice'
            ),
            'section_end' => array(
                 'type' => 'sectionend',
                 'id' => 'wc_settings_tab_colissimo_section_parcelreturn_end'
            )
        );
    }

//  Get the settings of Section printlabel
    public static function get_settings_section_printlabel() {
        return array(
            'section_title' => array(
                'name'     => __( 'Colissimo Delivery Integration - Printing of address labels', 'colissimo-delivery-integration' ),
                'type'     => 'title',
                'desc'     => '',
                'id'       => 'wc_settings_tab_colissimo_section_printlabel'
            ),
            'Printlabel section mark' => array(
                'css'  => 'display:none;',
                'type' => 'number',
                'default' => '1',
                'id'   => 'wc_settings_tab_colissimo_section-printlabel'
            ),
            'Page size' => array(
                'type' => 'text',
                'default' => '210x297',
                'desc' => __( 'Page size width x height in mm', 'colissimo-delivery-integration' ),
                'desc_tip' => __( "Here the size of the page in your printer. Syntax = width x height in mm (ex 210x297 for a portrait A4 format).", "colissimo-delivery-integration" ),
                'id'   => 'wc_settings_tab_colissimo_pagesize'
            ),
            'Labels layout' => array(
                'type' => 'text',
                'default' => '3x5',
                'desc' => __( 'Layout of labels in the page: width  x height', 'colissimo-delivery-integration' ),
                'desc_tip' => __( "Here, the layout of the labels in the page: nb-in-width x nb-in-height (ex 3x5 which will give a total of 15 labels in the page).", "colissimo-delivery-integration" ),
                'id'   => 'wc_settings_tab_colissimo_labellayout'
            ),
            'Address show size' => array(
                'type' => 'text',
                'default' => '80%',
                'desc' => __( 'Width of the viewing area of the address: in %', 'colissimo-delivery-integration' ),
                'desc_tip' => __( "Here, the width of the display area of the address on the label, expressed in % of the width of the label (ex 80%).", "colissimo-delivery-integration" ),
                'id'   => 'wc_settings_tab_colissimo_addresswidth'
            ),
            'Font size' => array(
                'type' => 'text',
                'default' => '12px',
                'desc' => __( 'Address font size : in css unit', 'colissimo-delivery-integration' ),
                'desc_tip' => __( "Here, the address font size in css unit (ex 12px).", "colissimo-delivery-integration" ),
                'id'   => 'wc_settings_tab_colissimo_fontsize'
            ),
            'Start rank' => array(
                'type' => 'text',
                'default' => '1',
                'custom_attributes' => array (
                  'pattern' => '[1-9][0-9]{0,1}',
                  'required' => 'yes',
                  ),
                'desc' => __( 'Position of the first label to be printed.', 'colissimo-delivery-integration' ),
                'desc_tip' => __( "Here, the position of the first label to be printed. This number must be between 1 and the total number of labels in the page.", "colissimo-delivery-integration" ),
                'id'   => 'wc_settings_tab_colissimo_startrank'
            ),
            'Start rank manage' => array(
                'type' => 'select',
                'default' => 'fix',
                'options' => array (
                  'fix'     => 'fix',
                  'forward' => 'forward',
                  ),
                'desc' => __( 'Mode for managing the position of the first label', 'colissimo-delivery-integration' ),
                'desc_tip' => __( "Here, the mode of management of the position of the 1st label. fix to always start at the same location in the pages; forward to automatically advance the position and thus allow to use all the labels of the pages.", "colissimo-delivery-integration" ),
                'id'   => 'wc_settings_tab_colissimo_managerank'
            ),
            'Test grid' => array(
                'type' => 'checkbox',
                'default' => 'yes',
                'desc' => __( 'Test grid mode to help the setting of the printer', 'colissimo-delivery-integration' ),
                'desc_tip' => __( "Here, the test grid mode is used to assist in the setting of the printer. In this mode, the positions of the labels are delimited by a grid.", "colissimo-delivery-integration" ),
                'id'   => 'wc_settings_tab_colissimo_testgrid'
            ) ,
            'Css page layout' => array(
                'type' => 'textarea',
                'css'  => 'width:70%; height:4em;',
                'default' => 'padding: 0 !important; margin-top: 5mm !important; height:96% !important; margin-left: 12mm !important; width:99% !important;',
                'desc' => __( 'Area specifying the layout in the sheet, in css code', 'colissimo-delivery-integration' ),
                'desc_tip' => __( "Here, the zone specifying the layout in the sheet, in css code (Ex: padding: 0 !important; margin-top: 5mm !important; height:96% !important; margin-left: 12mm !important; width:99% !important;).", "colissimo-delivery-integration" ),
                'id'   => 'wc_settings_tab_colissimo_miseenpage'
            ),
            'Custom css' => array(
                'type' => 'textarea',
                'css'  => 'width:70%; height:10em;',
                'default' => '',
                'desc' => __( 'Zone specifying formatting of addresses, in css  code', 'colissimo-delivery-integration' ),
                'desc_tip' => __( "Here, the area specifying the formatting of the addresses, in css code.", "colissimo-delivery-integration" ),
                'id'   => 'wc_settings_tab_colissimo_customcss'
            ),
            'section_end' => array(
                 'type' => 'sectionend',
                 'id' => 'wc_settings_tab_colissimo_section_printlabel_end'
            )
        );
    }

}
?>
