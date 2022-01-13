<?PHP

/**
 * This file is part of the Colissimo Delivery Integration plugin.
 * (c) Halyra
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

if (!defined('ABSPATH')) exit;
/******************************************************************************************************/
/* Functions and Controls : the shipment tracking code is present when status become completed        */
/******************************************************************************************************/
class WC_function_Colissimo {
   // ***************************************************************************************************
    public static function init() {
        add_action( 'woocommerce_order_status_changed',  __CLASS__ . '::cdi_change_status_tocomplete', 10, 3 );
        add_action( 'admin_notices',  __CLASS__ . '::cdi_admin_notice');
        add_action( 'before_delete_post',  __CLASS__ . '::cdi_delete_order');
    }
   // ***************************************************************************************************
    public static function cdi_change_status_tocomplete($order_id,  $old_status, $new_status) {
        if ($old_status == 'processing' && $new_status == 'completed') {
          if( !get_post_meta( !$order_id, 'cdi_colis_status', true ) && !get_post_meta( $order_id, 'Colissimo', true )) {
            $message = __('Your last order(s) completed has no Colissimo tracking code. You can add a tracking code and send again order completed mail.', 'colissimo-delivery-integration') ;
            update_option( 'cdi_notice_display', $message );
          }
        }
    }
   // ***************************************************************************************************
    public static function cdi_admin_notice() {
      $cdi_notice_display = get_option( 'cdi_notice_display') ;
      if ($cdi_notice_display !== 'nothing'){
        echo '<div class="updated notice"><p>';
        echo $cdi_notice_display ;
        echo "</p></div>";
        update_option( 'cdi_notice_display', 'nothing' );
      }else{
        add_option('cdi_notice_display', 'nothing');
      }
    }
   // ***************************************************************************************************
    public static function cdi_delete_order($idorder) {
      // Automatic clean of cdistore when an order is suppress
      if (get_post_type($idorder) !== 'shop_order') {
	return false;
      }
      $upload_dir = wp_upload_dir();
      $dircdistore = trailingslashit($upload_dir['basedir']).'cdistore';
      $url = wp_nonce_url('plugins.php?page=colissimo-delivery-integration');
      if (false === ($creds = request_filesystem_credentials($url, "", false, false, null) ) ) {
        WC_function_Colissimo::cdi_debug(__LINE__ ,__FILE__ , 'error - request creds', 'tec');
	return false;
      }
      if ( !WP_Filesystem($creds) ) {
        WC_function_Colissimo::cdi_debug(__LINE__ ,__FILE__ , 'error - creds not valid', 'tec');
        return false;
      }
      global $wp_filesystem;
      if (!file_exists($dircdistore)) { 
        return false;
      }
      $filename = trailingslashit($dircdistore). 'CDI-' . 'label' . '-' . $idorder .'.txt';
      $result = $wp_filesystem->delete( $filename) ;
      $filename = trailingslashit($dircdistore). 'CDI-' . 'cn23' . '-' . $idorder .'.txt';
      $result = $wp_filesystem->delete( $filename) ;
      return true;
    }
   // ***************************************************************************************************
    public static function cdi_uploads_encrypt_contents ($filecontent) { 
      $ec_filecontent = $filecontent ; 
      $key = get_option('keycdistore') ;
      if (!$key or $key == '') {
        $key = bin2hex(openssl_random_pseudo_bytes(16, $cstrong));
        update_option('keycdistore', $key ); 
      }
      if (get_option('wc_settings_tab_colissimo_encryptioncdistore') == "yes") {
        $iv = openssl_random_pseudo_bytes(16); // IV of 16 bytes long with aes-256-ctr
        //$iv = "1234567812345678" ; //For testing        
        $cipher = "aes-256-ctr" ;
        if (in_array($cipher, openssl_get_cipher_methods())) {       
          $ec_filecontent = openssl_encrypt($filecontent, $cipher, $key, $options=0, $iv);
          $ec_filecontent = $iv . $ec_filecontent ;      
        }
      }   
      return $ec_filecontent;    
    }
   // ***************************************************************************************************
    public static function cdi_uploads_decrypt_contents ($filecontent) { 
      $de_filecontent = $filecontent ;    
      $begin =  substr($filecontent , 0 , 5) ; 
      if ($begin !== "JVBER") { 
        $key = get_option('keycdistore') ;
        $iv = substr($filecontent , 0 , 16) ; // IV of 16 bytes long with aes-256-ctr
        //$iv = "1234567812345678" ; //For testing       
        $filecontent = substr($filecontent , 16) ;    
        $cipher = "aes-256-ctr" ;
        $de_filecontent = openssl_decrypt($filecontent, $cipher, $key, $options=0,  $iv);
      }
      return $de_filecontent;
    } 
   // ***************************************************************************************************
    public static function cdi_uploads_put_contents ($idorder, $type, $filecontent) {
      if ($type !== 'label' and $type !== 'cn23' and $type !== 'bordereau') {
	return false;
      }
      $upload_dir = wp_upload_dir();
      $dircdistore = trailingslashit($upload_dir['basedir']).'cdistore';
      $url = wp_nonce_url('plugins.php?page=colissimo-delivery-integration');
      if (false === ($creds = request_filesystem_credentials($url, "", false, false, null) ) ) {
        WC_function_Colissimo::cdi_debug(__LINE__ ,__FILE__ , 'error - request creds', 'tec');
	return false;
      }
      if ( !WP_Filesystem($creds) ) {
        WC_function_Colissimo::cdi_debug(__LINE__ ,__FILE__ , 'error - creds not valid', 'tec');
        return false;
      }
      global $wp_filesystem;
      if (!file_exists($dircdistore)) { // create cdistore dir if not exist
        if ( ! $wp_filesystem->mkdir($dircdistore) ) {
          WC_function_Colissimo::cdi_debug(__LINE__ ,__FILE__ , 'error - create dir', 'tec');
          return false;
        }
      }
      chmod($dircdistore, 0750); // to avoid external reading 
      $filename = trailingslashit($dircdistore). 'CDI-' . $type . '-' . $idorder .'.txt';
      $filecontent = self::cdi_uploads_encrypt_contents ($filecontent) ;
      $result = $wp_filesystem->delete( $filename) ; // if exist suppress before replace
      if ( ! $wp_filesystem->put_contents( $filename, $filecontent, FS_CHMOD_FILE) ) {
        WC_function_Colissimo::cdi_debug(__LINE__ ,__FILE__ , 'error - create file', 'tec');
        return false;
      }
      chmod($filename, 0640); // to avoid external reading
      add_post_meta($idorder, '_cdi_meta_exist_uploads_' . $type, true, true); // Indicate that file exists in cdistore
      return true;
    }
   // ***************************************************************************************************
    public static function cdi_uploads_get_contents ($idorder, $type) {
      if ($type !== 'label' and $type !== 'cn23' and $type !== 'bordereau') {
	return false;
      }
      $upload_dir = wp_upload_dir();
      $dircdistore = trailingslashit($upload_dir['basedir']).'cdistore';
      $url = wp_nonce_url('plugins.php?page=colissimo-delivery-integration');
      if (false === ($creds = request_filesystem_credentials($url, "", false, false, null) ) ) {
        WC_function_Colissimo::cdi_debug(__LINE__ ,__FILE__ , 'error - request creds', 'tec');
	return false;
      }
      if ( !WP_Filesystem($creds) ) {
        WC_function_Colissimo::cdi_debug(__LINE__ ,__FILE__ , 'error - creds not valid', 'tec');
        return false;
      }
      global $wp_filesystem;
      if (!file_exists($dircdistore)) { 
        WC_function_Colissimo::cdi_debug(__LINE__ ,__FILE__ , 'error - dir not exist', 'tec');
        return false;
      }
      $filename = trailingslashit($dircdistore). 'CDI-' . $type . '-' . $idorder .'.txt';
      $filecontent = $wp_filesystem->get_contents( $filename) ;
      if ( ! $filecontent ) {
        WC_function_Colissimo::cdi_debug(__LINE__ ,__FILE__ , 'error - file get', 'tec');
        return false;
      }
      $filecontent = self::cdi_uploads_decrypt_contents($filecontent) ;
      return $filecontent;
    }
   // ***************************************************************************************************
    public static function cdi_cn23_country($country, $zipcode=null) {
      $nocn23 = get_option('wc_settings_tab_colissimo_ws_Nocn23ContryCodes') ;
      if (!$nocn23) $nocn23 = "DE,AT,BE,BG,CY,DK,ES,EE,FI,FR,GR,HU,IE,IT,LV,LT,LU,MT,NL,PL,PT,CZ,RO,GB,IE,SK,SI,SE" ; // For Back compatibility
      $array_nocn23 = explode(',', $nocn23) ;
      if ( !in_array ($country,$array_nocn23)) { 
        return true ;
      }else{
        // zipcode exemptions process
        if (!$zipcode OR !WC_function_Colissimo::cdi_isconnected()) {
          return false ;
        }else{
          $array_countrieslist = explode(';', get_option('wc_settings_tab_colissimo_Cn23ZipcodeExemptions')) ;
          foreach ($array_countrieslist as $zipcodelist) {
            $array_zipcode = explode('=', $zipcodelist) ;
            if ($array_zipcode[0] == $country) {
              if (!(strpos($array_zipcode[1], $zipcode) === false)) {
                return true ;
              }
            }
          }        
          return false ;
        }
      }
    }
   // ***************************************************************************************************
    public static function cdi_nochoicereturn_country($country) {
      $country_parcelreturn = get_option('wc_settings_tab_colissimo_country_Nochoiceparcelreturn');
      if (!$country_parcelreturn) $country_parcelreturn = "US,AU,JP,DE,AT,BE,BG,CY,DK,ES,EE,FI,FR,GR,HU,IE,IT,LV,LT,LU,MT,NL,PL,PT,CZ,RO,GB,IE,SK,SI,SE" ; // For Back compatibility
      $array_country_parcelreturn = explode(',', $country_parcelreturn) ;
      if ( !in_array ($country, $array_country_parcelreturn)) {    
        return true ;
      }else{
        return false ;
      }
    }
   // ***************************************************************************************************
    public static function cdi_colissimo_withoutsign_country($country) {
      $arr_country_without_sign = get_option('wc_settings_tab_colissimo_ws_InternationalWithoutSignContryCodes'); 
      if ($country and strpos ('XX,' . $arr_country_without_sign, $country) > 0) { 
        return true ;
      }else{
        return false ;
      }
    }
   // ***************************************************************************************************
    public static function cdi_colissimo_outremer_country($country) {
      if ( in_array ($country, array('MQ', 'GP', 'RE', 'GF', 'YT', 'PM', 'MF', 'BL', 'NC', 'PF', 'TF', 'WF'))) { 
        return true ;
      }else{
        return false ;
      }
    }
   // ***************************************************************************************************
    public static function cdi_colissimo_outremer_country_ftd($country) {
      if ( in_array ($country, array('MQ', 'GP', 'RE', 'GF'))) { 
        return true ;
      }else{
        return false ;
      }
    }
   // ***************************************************************************************************
    public static function cdi_get_items_chosen($order) {
	  $order_id = cdiwc3::cdi_order_id($order);
	  $order = new WC_Order( $order_id );
	  $items = $order->get_items();
          $chosen_products = get_post_meta($order_id, '_cdi_chosen_products', true);
          $shiptype_option = get_option('wc_settings_tab_colissimo_shippingpackageincart') ;
          if ( empty($chosen_products)  //  compatibility for old orders
          or empty($shiptype_option) 
          or ($shiptype_option == 'cart') // Order is not in WC multi shipping packages (Market places) 
          ) {
	    $returnitems = $items ;
          }else{ // Order in WC multi shipping packages (Market places)
	    $returnitems = array();
	    foreach( $items as $item ) {
              if (is_array($chosen_products) && in_array($item['product_id'], $chosen_products)) { // To ensure to get only products in shipping package
	        $returnitems[] = $item ;
              }
            }
            if (empty($returnitems)) { // Nothing found. It seems a product item swap has been done (probably a translation plugin running)
	      $returnitems = apply_filters('cdi_filterarray_itemslist_ordered_shippingpackage', $items, $chosen_products, $order) ; // The custom filter has to apply the swap of items
            }
          }
          return $returnitems ;
    }
   // ***************************************************************************************************
    public static function cdi_calc_totalnetweight($order) {
          $items = self::cdi_get_items_chosen($order) ;
	  $total_weight = 0;
	  foreach( $items as $item ) {
            $quantity = $item['quantity'];
            $product_id = $item['variation_id'] ;
            if($product_id == 0) { // No variation for that one
              $product_id = $item['product_id'];
            }
            $product = wc_get_product($product_id);
	    if ($product) {
              $weight = $product->get_weight();
              if (is_numeric($weight) && is_numeric($quantity)) {
                $item_weight = ( $weight * $quantity );
              }else{
                $item_weight = 0 ;
              }
              $total_weight += $item_weight;
            }
          }
          if (get_option( 'woocommerce_weight_unit' ) == 'kg') { // Convert kg to g
            $total_weight = $total_weight * 1000 ;
          }

          return $total_weight ;
    }
   // ***************************************************************************************************
    public static function cdi_cn23_calc_shipping($order) { 
	  $order_id = cdiwc3::cdi_order_id($order);
	  $order = new WC_Order( $order_id );
	  $items = $order->get_items( 'shipping' );
	  $costshipping = 0; 
          if (get_post_meta( cdiwc3::cdi_order_id($order) , '_cdi_refshippingmethod', true )) { // case order by customer
            $arrshippingmethod = explode(':', get_post_meta( cdiwc3::cdi_order_id($order) , '_cdi_refshippingmethod', true )); 
            foreach ($items as $item) {
              if ($item['instance_id'] == $arrshippingmethod['1']) {
	        $costshipping = $item['total'] ;
                break ;
              }
            }
          }else{ // case order by admin or no method
            foreach ($items as $item) {
	      $costshipping = $item['total'] ;
              break ;
            }
          }
          return $costshipping ; // exVAT shipping cost returned
    }
   // ***************************************************************************************************
    public static function cdi_sanitize_laposte_voie($string) {
      $string =  sanitize_text_field( $string) ;
      $excludespecial = array('’', '(', '_', ')', '=', ';', ':', '!', '#', '{', '[', '|', '^', '@', ']', '}', 'µ', '?', '§', '*', '"', "'", ',' );
      $string = str_replace ($excludespecial, ' ', $string) ;
      return $string ;
    }
   // ***************************************************************************************************
    public static function cdi_sanitize_laposte_voie_om($string) { // Idem sauf '’' et "'"
      $string =  sanitize_text_field( $string) ;
      $excludespecial = array('(', '_', ')', '=', ';', ':', '!', '#', '{', '[', '|', '^', '@', ']', '}', 'µ', '?', '§', '*', '"', ',' );
      $string = str_replace ($excludespecial, ' ', $string) ;
      return $string ;
    }
   // ***************************************************************************************************
    public static function cdi_sanitize_laposte_name($string) {
      $string =  sanitize_text_field( $string) ;
      $excludespecial = array('’', '(', '_', ')', '=', ';', ':', '!', '#', '{', '[', '|', '^', '@', ']', '}', 'µ', '?', '§', '*', '"', ',' );
      $string = str_replace ($excludespecial, '', $string) ;
      $excludespecial = array('.', '%', '/', '&' );
      $string = str_replace ($excludespecial, ' ', $string) ;
      return $string ;
    }
   // ***************************************************************************************************
    public static function cdi_sanitize_colissimo_enligne($string) {
      $excludespecial = array("'",'%', '/', '-', '&', '.');
      $string = str_replace ($excludespecial, ' ', $string) ;
      $string = strtoupper($string);
      return $string ;
    }
   // ***************************************************************************************************
    public static function cdi_array_for_carrier($row) {
      global $woocommerce;
      global $wpdb;
      if (is_numeric($row)) {
        $cdi_order_id  = $row;
      }else{
        $cdi_order_id  = $row->cdi_order_id;
      }
      $order = new WC_Order($cdi_order_id); 
      $order_date = cdiwc3::cdi_order_date_created($order);
      $shipping_first_name = get_post_meta($cdi_order_id,'_shipping_first_name',true); $shipping_first_name = remove_accents ($shipping_first_name) ; 
             $shipping_first_name = WC_function_Colissimo::cdi_sanitize_laposte_name( $shipping_first_name ) ;
      $shipping_last_name = get_post_meta($cdi_order_id,'_shipping_last_name',true); $shipping_last_name = remove_accents ($shipping_last_name) ; 
             $shipping_last_name = WC_function_Colissimo::cdi_sanitize_laposte_name( $shipping_last_name ) ;
      $shipping_company = get_post_meta($cdi_order_id,'_shipping_company',true); $shipping_company = remove_accents ($shipping_company) ; 
             $shipping_company = WC_function_Colissimo::cdi_sanitize_laposte_name( $shipping_company ) ;
             if (!$shipping_company) { $shipping_company = $shipping_last_name ; }
      $shipping_address_1 = get_post_meta($cdi_order_id,'_shipping_address_1',true); $shipping_address_1 = remove_accents ($shipping_address_1) ; 
             $shipping_address_1 = WC_function_Colissimo::cdi_sanitize_laposte_voie( $shipping_address_1 ) ;
      $shipping_address_2 = get_post_meta($cdi_order_id,'_shipping_address_2',true); $shipping_address_2 = remove_accents ($shipping_address_2) ; 
             $shipping_address_2 = WC_function_Colissimo::cdi_sanitize_laposte_voie( $shipping_address_2 ) ;
      $shipping_address_3 = get_post_meta($cdi_order_id,'_shipping_address_3',true); $shipping_address_3 = remove_accents ($shipping_address_3) ; 
             $shipping_address_3 = WC_function_Colissimo::cdi_sanitize_laposte_voie( $shipping_address_3 ) ;
      $shipping_address_4 = get_post_meta($cdi_order_id,'_shipping_address_4',true); $shipping_address_4 = remove_accents ($shipping_address_4) ; 
             $shipping_address_4 = WC_function_Colissimo::cdi_sanitize_laposte_voie( $shipping_address_4 ) ;
      $shipping_city = get_post_meta($cdi_order_id,'_shipping_city',true); $shipping_city = remove_accents ($shipping_city) ; 
             $shipping_city = WC_function_Colissimo::cdi_sanitize_laposte_voie( $shipping_city ) ;
      $shipping_postcode = get_post_meta($cdi_order_id,'_shipping_postcode',true); $shipping_postcode = remove_accents ($shipping_postcode) ; 
             $shipping_postcode = WC_function_Colissimo::cdi_sanitize_laposte_voie( $shipping_postcode ) ;
      $shipping_country = get_post_meta($cdi_order_id,'_shipping_country',true); $shipping_country = remove_accents ($shipping_country) ;
             $shipping_country = WC_function_Colissimo::cdi_sanitize_laposte_voie( $shipping_country ) ;
      $shipping_state = get_post_meta($cdi_order_id,'_shipping_state',true); $shipping_state = remove_accents ($shipping_state) ; 
             $shipping_state = WC_function_Colissimo::cdi_sanitize_laposte_voie( $shipping_state ) ;
      if ($shipping_state) {
        $shipping_city_state = $shipping_city . " " . $shipping_state ; 
      }else{
        $shipping_city_state = $shipping_city ; 
      }
      $billing_phone = get_post_meta($cdi_order_id,'_billing_phone',true); $billing_phone = remove_accents ($billing_phone) ; 
      $billing_email = get_post_meta($cdi_order_id,'_billing_email',true); $billing_email = remove_accents ($billing_email) ; 
      $customer_message = get_post_field( 'post_excerpt', $cdi_order_id );
      $cdi_meta_departure = get_post_meta($cdi_order_id,'_cdi_meta_departure',true);
      $cdi_meta_departure = WC_function_Colissimo::cdi_sanitize_laposte_voie( $cdi_meta_departure ) ;
            $cdi_departure_cp = substr ( $cdi_meta_departure , 0 ,5 );
            $cdi_departure_localite = substr ( $cdi_meta_departure , 6 );
      $cdi_meta_typeparcel = get_post_meta($cdi_order_id,'_cdi_meta_typeparcel',true);
      $cdi_meta_parcelweight = get_post_meta($cdi_order_id,'_cdi_meta_parcelweight',true);

      if (!WC_function_Colissimo::cdi_colissimo_withoutsign_country($shipping_country)) { update_post_meta(cdiwc3::cdi_order_id($order), '_cdi_meta_signature', 'yes') ; }
         $cdi_meta_signature = get_post_meta($cdi_order_id,'_cdi_meta_signature',true);
      if ($cdi_meta_signature == 'yes') { //  Additionnal insurance display 
        $cdi_meta_additionalcompensation = get_post_meta($cdi_order_id,'_cdi_meta_additionalcompensation',true);
        if (get_post_meta(cdiwc3::cdi_order_id($order), '_cdi_meta_additionalcompensation', true) == 'yes') { // Amount compensation display 
          $cdi_meta_amountcompensation = get_post_meta($cdi_order_id,'_cdi_meta_amountcompensation',true);
        }else{
          $cdi_meta_amountcompensation = '';
        } //  End Amount compensation display  
      }else{
        $cdi_meta_additionalcompensation = '';
        $cdi_meta_amountcompensation = '';
      } //  End Additionnal insurance display display

      $cdi_meta_returnReceipt = get_post_meta($cdi_order_id,'_cdi_meta_returnReceipt',true); //  Return avis réception
      //  End return avis réception
      if (WC_function_Colissimo::cdi_nochoicereturn_country ($shipping_country)) { //  Return internationnal display  
        $cdi_meta_typereturn = get_post_meta($cdi_order_id,'_cdi_meta_typereturn',true);
      }else{
        $cdi_meta_typereturn = '';          
      } //  End Return internationnal display  
      if (WC_function_Colissimo::cdi_colissimo_outremer_country_ftd ($shipping_country)) { //  OM ftd  
        $cdi_meta_ftd = get_post_meta($cdi_order_id,'_cdi_meta_ftd',true);
      }else{
        $cdi_meta_ftd = '';          
      } //  End OM ftd 

      if ("ordernumber" == get_option('wc_settings_tab_colissimo_parcelreference')) {
        $sender_parcel_ref = $order->get_order_number();
      }else{
        $sender_parcel_ref = $cdi_order_id ;
      }
      $sender_parcel_ref = apply_filters ('cdi_filterstring_sender_parcel_ref', $sender_parcel_ref) ;
      $carrier_instructions = apply_filters ('cdi_filterstring_carrier_instructions', '') ;

      $cdi_meta_productCode = get_post_meta($cdi_order_id,'_cdi_meta_productCode',true);
      $cdi_meta_pickupLocationId = get_post_meta($cdi_order_id,'_cdi_meta_pickupLocationId',true);

      if (WC_function_Colissimo::cdi_cn23_country ($shipping_country, $shipping_postcode)) { //  CN23 display 
        $cdi_meta_cn23_shipping = get_post_meta($cdi_order_id,'_cdi_meta_cn23_shipping',true);
        $cdi_meta_cn23_category = get_post_meta($cdi_order_id,'_cdi_meta_cn23_category',true);
      }else{
        $cdi_meta_cn23_shipping = '' ;
        $cdi_meta_cn23_category = '';
      } //  End CN23 display

      $array_for_carrier = array  ('order_id' => $cdi_order_id);
      $array_for_carrier['order_date']  = $order_date ;
      $array_for_carrier['shipping_first_name']  = $shipping_first_name ;
      $array_for_carrier['shipping_last_name']  = $shipping_last_name ;
      $array_for_carrier['shipping_company']  = $shipping_company ;
      $array_for_carrier['shipping_address_1']  = $shipping_address_1 ;
      $array_for_carrier['shipping_address_2']  = $shipping_address_2 ;
      $array_for_carrier['shipping_address_3']  = $shipping_address_3 ;
      $array_for_carrier['shipping_address_4']  = $shipping_address_4 ;
      $array_for_carrier['shipping_city']  = $shipping_city ;
      $array_for_carrier['shipping_postcode']  = $shipping_postcode ;
      $array_for_carrier['shipping_country']  = $shipping_country ;
      $array_for_carrier['shipping_state']  = $shipping_state ;
      $array_for_carrier['shipping_city_state']  = $shipping_city_state ;
      $array_for_carrier['billing_phone']  = $billing_phone ;
      $array_for_carrier['billing_email']  = $billing_email ;
      $array_for_carrier['customer_message']  = $customer_message ;
      $array_for_carrier['departure']  = $cdi_meta_departure ;
      $array_for_carrier['departure_cp']  = $cdi_departure_cp ;
      $array_for_carrier['departure_localite']  = $cdi_departure_localite ;
      $array_for_carrier['parcel_type']  = $cdi_meta_typeparcel ;
      $array_for_carrier['parcel_weight']  = $cdi_meta_parcelweight ;
      $array_for_carrier['signature']  = $cdi_meta_signature ;
      $array_for_carrier['additional_compensation']  = $cdi_meta_additionalcompensation ;
      $array_for_carrier['compensation_amount']  = $cdi_meta_amountcompensation ;

      $array_for_carrier['returnReceipt']  = $cdi_meta_returnReceipt ;
      $array_for_carrier['return_type']  = $cdi_meta_typereturn ;
      $array_for_carrier['ftd']  = $cdi_meta_ftd ;

      $array_for_carrier['sender_parcel_ref']  = $sender_parcel_ref ;
      $array_for_carrier['carrier_instructions']  = $carrier_instructions ;

      $array_for_carrier['product_code']  = $cdi_meta_productCode ;
      $array_for_carrier['pickup_Location_id']  = $cdi_meta_pickupLocationId ;
      $array_for_carrier['cn23_shipping']  = $cdi_meta_cn23_shipping ;
      $array_for_carrier['cn23_category']  = $cdi_meta_cn23_category ;
      $items = self::cdi_get_items_chosen($order) ;
      $nbart = 0; 
      foreach( $items as $item ) { 
        if (WC_function_Colissimo::cdi_cn23_country ($shipping_country, $shipping_postcode)) {
          $cdi_meta_cn23_article_description = get_post_meta($cdi_order_id,'_cdi_meta_cn23_article_description_' . $nbart,true);
          $cdi_meta_cn23_article_weight = get_post_meta($cdi_order_id,'_cdi_meta_cn23_article_weight_' . $nbart,true);
          $cdi_meta_cn23_article_quantity = get_post_meta($cdi_order_id,'_cdi_meta_cn23_article_quantity_' . $nbart,true);
          $cdi_meta_cn23_article_value = get_post_meta($cdi_order_id,'_cdi_meta_cn23_article_value_' . $nbart,true);
          if (get_post_meta(cdiwc3::cdi_order_id($order), '_cdi_meta_cn23_category', true) == '3') { //  CN23 HS code display  
            $cdi_meta_cn23_article_hstariffnumber = get_post_meta($cdi_order_id,'_cdi_meta_cn23_article_hstariffnumber_' . $nbart,true);
          }else{
            $cdi_meta_cn23_article_hstariffnumber = '';
          } //  End CN23 HS code display  
          $cdi_meta_cn23_article_origincountry = get_post_meta($cdi_order_id,'_cdi_meta_cn23_article_origincountry_' . $nbart,true);
        }else{
          $cdi_meta_cn23_article_description = '';
          $cdi_meta_cn23_article_weight = '';
          $cdi_meta_cn23_article_quantity = '';
          $cdi_meta_cn23_article_value = '';
          $cdi_meta_cn23_article_hstariffnumber = '';
          $cdi_meta_cn23_article_origincountry = '';
        } 
        $array_for_carrier['cn23_article_description_' . $nbart]  = $cdi_meta_cn23_article_description ;
        $array_for_carrier['cn23_article_weight_' . $nbart]  = $cdi_meta_cn23_article_weight ;
        $array_for_carrier['cn23_article_quantity_' . $nbart]  = $cdi_meta_cn23_article_quantity ;
        $array_for_carrier['cn23_article_value_' . $nbart]  = $cdi_meta_cn23_article_value ;
        $array_for_carrier['cn23_article_hstariffnumber_' . $nbart]  = $cdi_meta_cn23_article_hstariffnumber ;
        $array_for_carrier['cn23_article_origincountry_' . $nbart]  = $cdi_meta_cn23_article_origincountry ;
        $nbart = $nbart+1; 
      }
      return $array_for_carrier ;
    } 
   // ***************************************************************************************************
    public static function cdi_debug ($line, $file, $var, $type='msg') {
      $x = plugin_dir_path( __FILE__ ) ; // magic trick to shorten the path
      $x = str_replace('/includes/', '', $x) ;
      $file = str_replace($x, '', $file) ;
      if (in_array ($file, get_option('wc_settings_tab_colissimo_moduletolog'))) {
        error_log('*** LOG CDI(' . $type . ') - LINE:' . $line . ' FILE:' . $file . ' ***: ' . print_R($var, TRUE));
        //$msg = '*** LOG CDI(' . $type . ') - LINE:' . $line . ' FILE:' . $file . ' ***: ' . print_R($var, TRUE) ;
        //file_put_contents( WP_CONTENT_DIR . '/cdidebug.log', '['. date('Y-m-d H:i:s') . '] ' . $msg . PHP_EOL, FILE_APPEND | LOCK_EX );
      }
    }
   // ***************************************************************************************************
    public static function cdi_get_woo_version_number() {
	if ( ! function_exists( 'get_plugins' ) ) require_once( ABSPATH . 'wp-admin/includes/plugin.php' );
	$plugin_folder = get_plugins( '/' . 'woocommerce' );
	$plugin_file = 'woocommerce.php';
	if ( isset( $plugin_folder[$plugin_file]['Version'] ) ) {
		return $plugin_folder[$plugin_file]['Version'];
	}else{
		return NULL;
	}
    }
   // ***************************************************************************************************
    public static function cdi_sanitize_colissimophone($string) { // Keep only number and the heading + 
      $string = preg_replace('/^\+/', '00000000', $string); // Suppress international + and replace by a 00000000 patttern
      $string = preg_replace('/[^0-9]+/', '', $string); // Clean with only numbers
      $string = preg_replace('/^00000000/', '+', $string); // Reset international + if exist in input
      return $string ;
    }
   // ***************************************************************************************************
    public static function cdi_sanitize_colissimoMobileNumber($MobileNumber, $country) {
      $MobileNumber = WC_function_Colissimo::cdi_sanitize_colissimophone($MobileNumber);
      switch ($country) {
        case 'FR':
          // Si le numéro commence par +33X, 0033X, +330X ou 00330X il est nécessaire d'avoir converti le début en 0X (où X = 6 ou 7)
          $MobileNumber = preg_replace('/^003306/', '06', $MobileNumber);
          $MobileNumber = preg_replace('/^003307/', '07', $MobileNumber);
          $MobileNumber = preg_replace('/^\+3306/', '06', $MobileNumber);
          $MobileNumber = preg_replace('/^\+3307/', '07', $MobileNumber);
          $MobileNumber = preg_replace('/^00336/', '06', $MobileNumber);
          $MobileNumber = preg_replace('/^00337/', '07', $MobileNumber);
          $MobileNumber = preg_replace('/^\+336/', '06', $MobileNumber);
          $MobileNumber = preg_replace('/^\+337/', '07', $MobileNumber);
          switch ($MobileNumber) {
            case (preg_match('/^06/', $MobileNumber) ? true : false) :
              break;
            case (preg_match('/^07/', $MobileNumber) ? true : false) :
              break;   
            default:
              $MobileNumber = ''; // Erase mobile number if invalid
          }
          break ;
      }
      return $MobileNumber ;
    }
   // ***************************************************************************************************
    /**
     *
     * The most advanced method of serialization.
     *
     * @param mixed $obj => can be an objectm, an array or string. may contain unlimited number of subobjects and subarrays
     * @param string $wrapper => main wrapper for the xml
     * @param array (key=>value) $replacements => an array with variable and object name replacements
     * @param boolean $add_header => whether to add header to the xml string
     * @param array (key=>value) $header_params => array with additional xml tag params
     * @param string $node_name => tag name in case of numeric array key
     */
    public static function generateValidXmlFromMixiedObj($obj, $wrapper = null, $replacements = array(
) , $add_header = true, $header_params = array() , $node_name = 'node') {
      $xml = '';
      if ($add_header) $xml.= self::generateHeader($header_params);
      if ($wrapper != null) $xml.= '<' . $wrapper . '>';
      if (is_object($obj)) {
        $node_block = strtolower(get_class($obj));
        if (isset($replacements[$node_block])) $node_block = $replacements[$node_block];
        $xml.= '<' . $node_block . '>';
        $vars = get_object_vars($obj);
        if (!empty($vars)) {
          foreach($vars as $var_id => $var) {
            if (isset($replacements[$var_id])) $var_id = $replacements[$var_id];
            $xml.= '<' . $var_id . '>';
            $xml.= self::generateValidXmlFromMixiedObj($var, null, $replacements, false, null, $node_name);
            $xml.= '</' . $var_id . '>';
          }
        }
        $xml.= '</' . $node_block . '>';
      } else if (is_array($obj)) {
        foreach($obj as $var_id => $var) {
          if (!is_object($var)) {
            if (is_numeric($var_id)) $var_id = $node_name;
            if (isset($replacements[$var_id])) $var_id = $replacements[$var_id];
            $xml.= '<' . $var_id . '>';
          }
          $xml.= self::generateValidXmlFromMixiedObj($var, null, $replacements, false, null, $node_name);
          if (!is_object($var)) $xml.= '</' . $var_id . '>';
        }
      } else {
        $xml.= htmlspecialchars($obj, ENT_QUOTES);
      }
      if ($wrapper != null) $xml.= '</' . $wrapper . '>';
      return $xml;
    }
    /**
     *
     * xml header generator
     * @param array $params
     */
    public static function generateHeader($params = array()) {
      $basic_params = array(
        'version' => '1.0',
        'encoding' => 'UTF-8'
      );
      if (!empty($params)) $basic_params = array_merge($basic_params, $params);
      $header = '<?xml';
      foreach($basic_params as $k => $v) {
        $header.= ' ' . $k . '=' . $v;
      }
      $header.= ' ?>';
      return $header;
    }
   // ***************************************************************************************************
    public static function strToHex($string) {
      $hex = '';
      for ($i = 0; $i < strlen($string); $i++) {
        $ord = ord($string[$i]);
        $hexCode = dechex($ord);
        $hex.= substr('0' . $hexCode, -2);
      }
      return strToUpper($hex);
    }
    public static function hexToStr($hex) {
      $string = '';
      for ($i = 0; $i < strlen($hex) - 1; $i+= 2) {
        $string.= chr(hexdec($hex[$i] . $hex[$i + 1]));
      }
      return $string;
    }
   // ***************************************************************************************************
    public static function get_string_between($string, $start, $end){
      $string = ' ' . $string;
      $ini = strpos($string, $start);
      if ($ini == 0) return '';
      $ini += strlen($start);
      if ($end !== null) {
        $len = strpos($string, $end, $ini) - $ini;
        return substr($string, $ini, $len);
      }else{
        return substr($string, $ini);
      }
    }
    public static function sup_line($string){
      $string = str_replace(array("\r\n","\r","\n"),"",$string);
      return $string;
    }
   // ***************************************************************************************************
    public static function get_openssl_version_number($patch_as_number=false,$openssl_version_number=null) {
      // OPENSSL_VERSION_NUMBER parser, works from OpenSSL v.0.9.5b+ (e.g. for use with version_compare())
      // OPENSSL_VERSION_NUMBER is a numeric release version identifier for OpenSSL
      // Syntax: MNNFFPPS: major minor fix patch status (HEX)
      // The status nibble meaning: 0 => development, 1 to e => betas, f => release
      // Examples:
      // - 0x000906023 => 0.9.6b beta 3
      // - 0x00090605f => 0.9.6e release
      // - 0x1000103f  => 1.0.1c
      /**
      * @param Return Patch-Part as decimal number for use with version_compare
      * @param OpenSSL version identifier as hex value $openssl_version_number
      */
      if (is_null($openssl_version_number)) $openssl_version_number = OPENSSL_VERSION_NUMBER;
      $openssl_numeric_identifier = str_pad((string)dechex($openssl_version_number),8,'0',STR_PAD_LEFT);
      $openssl_version_parsed = array();
      $preg = '/(?<major>[[:xdigit:]])(?<minor>[[:xdigit:]][[:xdigit:]])(?<fix>[[:xdigit:]][[:xdigit:]])';
      $preg.= '(?<patch>[[:xdigit:]][[:xdigit:]])(?<type>[[:xdigit:]])/';
      preg_match_all($preg, $openssl_numeric_identifier, $openssl_version_parsed);
      $openssl_version = false;
      if (!empty($openssl_version_parsed)) {
        $alphabet = array(1=>'a',2=>'b',3=>'c',4=>'d',5=>'e',6=>'f',7=>'g',8=>'h',9=>'i',10=>'j',11=>'k',
                                       12=>'l',13=>'m',14=>'n',15=>'o',16=>'p',17=>'q',18=>'r',19=>'s',20=>'t',21=>'u',
                                       22=>'v',23=>'w',24=>'x',25=>'y',26=>'z');
        $openssl_version = intval($openssl_version_parsed['major'][0]).'.';
        $openssl_version.= intval($openssl_version_parsed['minor'][0]).'.';
        $openssl_version.= intval($openssl_version_parsed['fix'][0]);
        $patchlevel_dec = hexdec($openssl_version_parsed['patch'][0]);
        if (!$patch_as_number && array_key_exists($patchlevel_dec, $alphabet)) {
            $openssl_version.= $alphabet[$patchlevel_dec]; // ideal for text comparison
        }else{
            $openssl_version.= '.'.$patchlevel_dec; // ideal for version_compare
        }
      }
      return $openssl_version;
    }
   // ***************************************************************************************************
    public static function cdi_cdiplus_anonyme($action,$xml='') {  
      $xml = base64_encode('<xmldata>' . $xml . '</xmldata>');
      try {
        $errorfilegetcontents = false ;
        $postdata = array(
                'api' => 'cdi',
                'version' => get_option('cdi_options_version'),
                'contractnumber' => get_option('wc_settings_tab_colissimo_cdiplus_ContractNumber'), // Au cas où c'est un abonné
                'password' => '0',
                'carrier' => '000',
                'action' => $action,
                'siteurl' => rawurlencode(get_site_url()),
                'xml' => $xml );
        $result = self::cdi_url_get_contents( get_option('WC_settings_tab_colissimo_domain') , $postdata);
        if ($result === false) {
          WC_function_Colissimo::cdi_debug(__LINE__ ,__FILE__ , $result, 'tec');
          $errorfilegetcontents = true ;
        }
      } catch (Exception $e) {
        // Handle exception
        WC_function_Colissimo::cdi_debug(__LINE__ ,__FILE__ , $e, 'tec');
        $errorfilegetcontents = true ;
      }
      $result = base64_decode($result);
      if ($errorfilegetcontents == false) { // no error for client and server sides
        //on traite result
        return $result ;
      }else{
        //on traite error
        WC_function_Colissimo::cdi_debug(__LINE__ ,__FILE__ , 'Error : ' . $result, 'tec');
        return '' ;
      }
    }
   // ***************************************************************************************************
    public static function ssl_private_decrypt($source,$key){
      $maxlength=128;
      $output='';
      while($source){
        $input= substr($source,0,$maxlength);
        $source=substr($source,$maxlength);
        $ok= openssl_private_decrypt($input,$out,$key);    
        $output.=$out;
      }
      return $output;
    } 
   // ***************************************************************************************************
    public static function cdi_eval($action) {
      $cdi_eval_crypted = get_option('cdi_eval') ;
      $data = hex2bin($cdi_eval_crypted[$action]) ; 
      $privKeyhex =  get_option('cdi_privkey');
      $privKey = hex2bin($privKeyhex) ;
      $return = self::ssl_private_decrypt($data,$privKey) ;
      return $return ;
    }
   // ***************************************************************************************************
    public static function cdi_isconnected() {
      $cdi_eval_crypted = get_option('cdi_eval') ; 
      if (!$cdi_eval_crypted) {
        $return = false ;
      }else{
        eval(WC_function_Colissimo::cdi_eval('99')) ;
        if ($return) {
          update_option('cdi_date_lastisconnect', date('ymdHis'));
        }
      }
      return $return ;
    }
   // ***************************************************************************************************
    public static function cdi_cdiplus_credential() { 
      // Clear no valid contract number structure (scories introduced before applying css control of contract number structure)
      $result = get_option('wc_settings_tab_colissimo_cdiplus_ContractNumber') ;
      $ok = self::cdi_checkdatetime12($result) ;
      if (!$ok or null == get_option('wc_settings_tab_colissimo_cdiplus_Password')) {
        update_option('wc_settings_tab_colissimo_cdiplus_ContractNumber', '') ;
        update_option('wc_settings_tab_colissimo_cdiplus_Password', '') ;
        update_option('cdi_checksettings', WC_function_Colissimo::cdi_checksettings());
      }
      // End of reset legacy error in contract number
      $tokenlastnews = time() ;
      $oldtokenlastnews = get_option('cdi_tokenlastnews') ;
      if ($oldtokenlastnews > ($tokenlastnews + 172800)) {  // Older than 2 days, so something may be wrong : incoherent data to reinitiate
        $oldtokenlastnews = 100 ;
      } 
      if (!$oldtokenlastnews OR (($oldtokenlastnews + 21600) < $tokenlastnews)) { // timer pour demande forcée à 21600s (6h)
        $forcedrequest = true ;
        update_option('cdi_tokenlastnews', $tokenlastnews) ;
      }else{
        $forcedrequest = false ;
      }
      if ((get_option('wc_settings_tab_colissimo_cdiplus_ContractNumber') && get_option('wc_settings_tab_colissimo_cdiplus_Password')) OR $forcedrequest == true) {
        $tokentimercredential = time() ;
        $oldtokentimercredential = get_option('cdi_tokentimercredential') ;
        if ($oldtokentimercredential && (($oldtokentimercredential + 600) > $tokentimercredential)) { // timer pour maj à 600s (10mn)
          return ;
        }
        update_option('cdi_tokentimercredential', $tokentimercredential) ;
        $xml = '<xmldata><lastisconnect>' . get_option('cdi_date_lastisconnect')  . '</lastisconnect><lastwsauto>' . get_option('cdi_date_lastwsauto')  . '</lastwsauto></xmldata>' ;
        $xml = base64_encode($xml);
        try {
          $errorfilegetcontents = false ; 
          $postdata = array(
                  'api' => 'cdi',
                  'version' => get_option('cdi_options_version'),
                  'contractnumber' => get_option('wc_settings_tab_colissimo_cdiplus_ContractNumber'),
                  'password' => get_option('wc_settings_tab_colissimo_cdiplus_Password'),
                  'carrier' => 'col',
                  'action' => 'ab',
                  'siteurl' => rawurlencode(get_site_url()),
                  'xml' => $xml );
          $result = self::cdi_url_get_contents( get_option('WC_settings_tab_colissimo_domain') , $postdata);
          if ($result === false) {
            WC_function_Colissimo::cdi_debug(__LINE__ ,__FILE__ , $result, 'tec');
            $errorfilegetcontents = true ;
          }
        } catch (Exception $e) {
          // Handle exception
          WC_function_Colissimo::cdi_debug(__LINE__ ,__FILE__ , $e, 'tec');
          $errorfilegetcontents = true ;
        }
        $result = base64_decode($result);
        if ($errorfilegetcontents == false) { // no error for client and server sides
          $errorCode = WC_function_Colissimo::get_string_between($result, '<id>', '</id>') ;
          update_option ('cdi_date_lastnews', WC_function_Colissimo::get_string_between($result, '<lastnews>', '</lastnews>')) ;
          update_option ('cdi_modal', WC_function_Colissimo::get_string_between($result, '<modal>', '</modal>')) ;
          if ($errorCode == '0') {
            $privKeyhex = WC_function_Colissimo::get_string_between($result, '<privkey>', '</privkey>') ;
            update_option('cdi_privkey', $privKeyhex);
            $string = WC_function_Colissimo::get_string_between($result, '<content>', '</content>') ;
            $cdi_eval_crypted = array () ;
            $arrx = explode (';', $string) ;
            foreach ($arrx as $x) {
              $arry = explode (':', $x) ;
              if (isset($arry[0]) && isset($arry[1])) {
                $cdi_eval_crypted[$arry[0]] = $arry[1] ;
              }
            }
            $returnmsg = null ;
            update_option('cdi_eval', $cdi_eval_crypted) ;
            $errorCode = '99999' ;
            update_option('cdi_lasterror_isabonnecdi', '0') ;
            update_option('cdi_nb_clientserveur_error', '0') ;
          }else{
            if (WC_function_Colissimo::get_string_between($result, '<id>', '</id>') !== '90000' ) {
              $returnmsg = 'Erreur authentification CDI : ' . WC_function_Colissimo::get_string_between($result, '<id>', '</id>') . ' ' . WC_function_Colissimo::get_string_between($result, '<messageContent>', '</messageContent>') ;
            }else{
              $returnmsg = null ;
            }
            delete_option('cdi_eval') ;
            $errorCode = '99990' ;
            update_option('cdi_lasterror_isabonnecdi', WC_function_Colissimo::get_string_between($result, '<id>', '</id>')) ;
          }
        }else{
          $cdi_nb_clientserveur_error = get_option('cdi_nb_clientserveur_error') ;       
          if ($cdi_nb_clientserveur_error > 7) {        
            delete_option('cdi_eval') ;
            $errorCode = '99990' ;
            $returnmsg = "CDI Error : Vous n'avez pas de connexion sortante depuis votre site." ;          
          }else{ 
            update_option('cdi_nb_clientserveur_error', $cdi_nb_clientserveur_error + 1) ;
            return ;            
          }  
        }
      }else{
        $returnmsg = null ;
        delete_option('cdi_eval') ;
        $errorCode = '99990' ;
      }
      eval(WC_function_Colissimo::cdi_eval('98')) ;
      return $returnmsg ;
    }
   // ***************************************************************************************************
    public static function cdi_get_inovert($order_id, $trackingcode) {  
      if (WC_function_Colissimo::cdi_isconnected()) { 
        $arrayinovert = get_post_meta($order_id, 'cdi_colis_inovert', true ) ;
        if ($arrayinovert && strpos('LIV', $arrayinovert['eventCode'])) { // Livré, pour toute raison inovert
          $msgsuivicolis = ' ' . $arrayinovert['eventCode'] . ' ' . $arrayinovert['eventDate'] . ' | ' . $arrayinovert['eventLibelle'] . ' | ' . $arrayinovert['eventSite'] . ' ' . $arrayinovert['recipientCity'] . ' ' . $arrayinovert['recipientCountryCode'] . ' ' . $arrayinovert['recipientZipCode'] ;
          return $msgsuivicolis ;
        }else{
          $order = wc_get_order( $order_id );
          $order_date_obj = $order->get_date_created() ;
          $order_date = $order_date_obj->format('Y-m-d');
          $limitdate = str_replace('-', '', date('Y-m-d', strtotime("-30 days"))) ;
          $checkeddate = str_replace('-', '', substr($order_date,0,10)) ;
          $datetime1 = new DateTime($limitdate);
          $datetime2 = new DateTime($checkeddate);
          $difference = $datetime1->diff($datetime2);
          if ($difference->invert > 0) {
            return '*** Plus de suivi au-dela de 30 jours.' ;
          }else{
            // Initiate structure    
            $cdicolcontractnumber = get_option('wc_settings_tab_colissimo_ws_ContractNumber') ;
            $cdicolpassword = get_option('wc_settings_tab_colissimo_ws_Password') ;
            //$cdicolcontractnumber = '123456' ; // for Colissimo account test
            //$cdicolpassword = 'ABC123' ;  // for Colissimo account test
            $result = self::cdi_url_get_contents( 'https://www.coliposte.fr/tracking-chargeur-cxf/TrackingServiceWS/track?accountNumber=' . $cdicolcontractnumber . '&password=' . $cdicolpassword . '&skybillNumber=' . $trackingcode);
            $errorCode = WC_function_Colissimo::get_string_between($result, '<errorCode>', '</errorCode>') ;
            if ($errorCode !== '0') {
              return 'Erreur suivi colis: ' . $errorCode ;
            }else{
              $arrayinovert = array() ;
              $arrayinovert['eventCode'] = WC_function_Colissimo::get_string_between($result, '<eventCode>', '</eventCode>') ;
              $arrayinovert['eventDate'] = WC_function_Colissimo::get_string_between($result, '<eventDate>', '</eventDate>') ;
              $arrayinovert['eventLibelle'] = WC_function_Colissimo::get_string_between($result, '<eventLibelle>', '</eventLibelle>') ;
              $arrayinovert['eventSite'] = WC_function_Colissimo::get_string_between($result, '<eventSite>', '</eventSite>') ;
              $arrayinovert['recipientCity'] = WC_function_Colissimo::get_string_between($result, '<recipientCity>', '</recipientCity>') ;
              $arrayinovert['recipientCountryCode'] = WC_function_Colissimo::get_string_between($result, '<recipientCountryCode>', '</recipientCountryCode>') ;
              $arrayinovert['recipientZipCode'] = WC_function_Colissimo::get_string_between($result, '<recipientZipCode>', '</recipientZipCode>') ;
              update_post_meta($order_id, 'cdi_colis_inovert', $arrayinovert ) ;
              $msgsuivicolis = ' ' . $arrayinovert['eventCode'] . ' ' . $arrayinovert['eventDate'] . ' | ' . $arrayinovert['eventLibelle'] . ' | ' . $arrayinovert['eventSite'] . ' ' . $arrayinovert['recipientCity'] . ' ' . $arrayinovert['recipientCountryCode'] . ' ' . $arrayinovert['recipientZipCode'] ;
              return $msgsuivicolis ;
            }
          }
        }
      }
    }
   // ***************************************************************************************************
    public static function cdi_button_connected() {
      if (WC_function_Colissimo::cdi_isconnected()) {
        eval (WC_function_Colissimo::cdi_eval('7')) ;
        eval (WC_function_Colissimo::cdi_eval('97')) ; 
        $datetime1 = new DateTime('20' . substr($cdi_datefinabonnement,0,6));
        $datetime2 = new DateTime();
        $difference = $datetime1->diff($datetime2);
        if ( ($difference->invert == 1) && ($difference->days > 30)) { $color = 'green';
          }elseif ( ($difference->invert == 1) && ($difference->days > 15) ) { $color = '#ff00be';
          }else{ $color = 'red';
        }
        $datefin = '20' . substr($cdi_datefinabonnement,0,2) . '/' . substr($cdi_datefinabonnement,2,2) . '/' . substr($cdi_datefinabonnement,4,2) ;
        ?><em></em><input name="cdi_green_del" type="submit" value="CDI+ connecté -> <?php echo $datefin ; ?>" style="float: left; background-color:<?php echo $color ; ?>; color:white;" title="Vous êtes reconnu comme abonné à CDI+ :
Vous pouvez allez sur votre console colissimodeliveryintégration.com pour mettre à jour votre abonnement quand nécessaire.
NB: Si vous venez de changer vos paramètres de connexion CDI+ ou votre abonnement sur votre console colissimodeliveryintégration.com, l’acquisition des droits auprès du serveur CDI peut prendre jusqu'à 10mn."/><em></em><?php
        eval (WC_function_Colissimo::cdi_eval('12')) ;
      }else{
        $errdisplay = null ;
        if (null !== get_option('wc_settings_tab_colissimo_cdiplus_ContractNumber') and get_option('wc_settings_tab_colissimo_cdiplus_ContractNumber') !== "") {
          $errdisplay = "(Erreur " . get_option('cdi_lasterror_isabonnecdi') . ")" ;
        }
        ?><em></em><input name="cdi_green_del" type="submit" value="CDI+ non connecté <?php echo $errdisplay; ?>"  style="float: left; background-color:gray; color:black;" title="Abonnement périmé ? : Allez sur votre console colissimodeliveryintégration.com pour mettre à jour votre abonnement.
Pour adhérer à CDI+ ? : Cliquez sur le bouton vert d'adhésion en haut de page.
NB: Si vous venez de changer vos paramètres de connexion CDI+ ou votre abonnement sur votre console colissimodeliveryintégration.com, l’acquisition des droits auprès du serveur CDI peut prendre jusqu'à 10mn."/><em></em><?php
        if ($_SERVER['REQUEST_METHOD'] == "POST" and ISSET($_POST['cdi_green_del'])) {
          delete_option('cdi_tokentimercredential') ;
          header("Location: http://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]");
          exit;
        }
      }      
    }
   // ***************************************************************************************************
    public static function cdi_button_informationcdi() {
        $cdi_date_lastnews = get_option ('cdi_date_lastnews') ;
        $cdi_date_newsread = get_option('cdi_date_newsread') ;
        if (!$cdi_date_newsread OR $cdi_date_lastnews !== $cdi_date_newsread) {
          ?><em></em><input id="blink" name="cdi_annonce_cdiplus" type="submit" value="Information CDI" style="float: left; background-color:blue; color:white; font-weight: bold;" title="Vous n'avez pas encore lu cette information, cliquez !" /><em></em><?php
        }else{
          ?><em></em><input id="noblink" name="cdi_annonce_cdiplus" type="submit" value="Information CDI" style="float: left; background-color:gray; color:white; font-weight: bold;" title="Cliquez pour lire cette information." /><em></em><?php
        }
        if ($_SERVER['REQUEST_METHOD'] == "POST" and ISSET($_POST['cdi_annonce_cdiplus'])) {
          echo '</p><br class="clear">' ;
          $result = WC_function_Colissimo::cdi_cdiplus_anonyme('if', '<lastisconnect>' . get_option('cdi_date_lastisconnect')  . '</lastisconnect><lastwsauto>' . get_option('cdi_date_lastwsauto')  . '</lastwsauto>') ;
          $return = WC_function_Colissimo::get_string_between($result, '<return>', '</return>') ;
          $messages = WC_function_Colissimo::get_string_between($return, '<messages>', '</messages>') ;
          $retid = WC_function_Colissimo::get_string_between($messages, '<id>', '</id>') ;
          $retmessageContent = WC_function_Colissimo::get_string_between($messages, '<messageContent>', '</messageContent>') ;
          if ($retid != 0) {
            ?><p> Erreur <?php echo $retid ; ?> : <?php echo $retmessageContent ; ?></p><?php
          }else{
            if (isset($retmessageContent) && $retmessageContent !== null && $retmessageContent !== '') {
              ?><div> <?php echo $retmessageContent ; ?> </div><?php
            }
          }
          $d = get_option ('cdi_date_lastnews') ;
          if (!$d) {
            $d = 'init' ;
            update_option ('cdi_date_lastnews', $d) ;
          }
          update_option('cdi_date_newsread', $d) ;
        }
        ?><script type="text/javascript">
        jQuery(document).ready(function(){ 
          jQuery( "#blink" ).each(function( index ) { 
            var blacktime = 1000;
            var whitetime = 1000;
            setTimeout(whiteFunc,blacktime);
            function whiteFunc(){
              document.getElementById("blink").style.color = "white";
              setTimeout(blackFunc,whitetime);
            }
            function blackFunc(){
              document.getElementById("blink").style.color = "black";
              setTimeout(whiteFunc,blacktime);
            }
          });
        });
        </script><?php
    }
   // ***************************************************************************************************
    public static function cdi_button_adhesioncdiplus() {
      global $woocommerce;
      $return = null ;
      $contractnumber = get_option('wc_settings_tab_colissimo_cdiplus_ContractNumber') ;
      if (!$contractnumber) {
        ?><em></em><input name="cdi_adhesion_cdiplus" type="submit" value="Adhérer à CDI+" style="float: left; background-color:green; color:white;" title="Pour adhérer cliquez !" /><em></em><?php

        if ($_SERVER['REQUEST_METHOD'] == "POST" and ISSET($_POST['cdi_adhesion_cdiplus'])) {
          $prenom  = '' ;
          $nom  = '' ;
          $entreprise  = get_option('wc_settings_tab_colissimo_ws_sa_CompanyName') ;
          $siret = '' ;
          $adresseligne1  = get_option('wc_settings_tab_colissimo_ws_sa_Line1') ;
          $adresseligne2  = get_option('wc_settings_tab_colissimo_ws_sa_Line2') ;
          $codepostal  = get_option('wc_settings_tab_colissimo_ws_sa_ZipCode') ;
          $ville  = get_option('wc_settings_tab_colissimo_ws_sa_City') ;
          $payscode  = get_option('wc_settings_tab_colissimo_ws_sa_CountryCode') ;
          $paysname = WC()->countries->countries[$payscode];
          $urlsite  = get_site_url() ;
          $email  = get_option('wc_settings_tab_colissimo_ws_sa_Email') ;
          $telephone  = '' ;

          $html  =  '</p><br class="clear">' ;
          $html .= '<div style="border: 5px solid blue; margin-left:18%;"><div style="background-color:white; color:black; padding:15px;">' ;
          $html .= '<p style="float:right;"><mark>Compléter le formulaire et valider en bas de page.</mark></p>' ;
          $html .= "<p><strong>Les données qui vont être communiquées à CDI+ pour initier l'adhésion de votre entreprise : </strong></p>" ;
          $html .= '<p><p>Entreprise : <input name="cdi_entreprise" placeholder="Entreprise ..." type="text" required pattern=".{3,}" value="'. $entreprise . '"/></p>' ;
          $html .= '<p><p>Siret : <input name="cdi_siret" placeholder="12345678901234" type="text" required pattern="[0-9]{14,14}" value="'. $siret . '"/></p>' ;
          $html .= '<p><p>Prénom : <input name="cdi_prenom" placeholder="Prénom ..." type="text" required pattern=".{3,}" value="'. $prenom . '"/></p>' ;
          $html .= '<p><p>Nom : <input name="cdi_nom" placeholder="Nom ..." type="text" required pattern=".{3,}" value="'. $nom . '"/></p>' ;
          $html .= '<p><p>Adresse ligne 1 : <input name="cdi_adresseligne1" placeholder="Adresse ligne 1 ..." type="text" required pattern=".{3,}" value="'. $adresseligne1 . '"/></p>' ;
          $html .= '<p><p>Adresse ligne 2 : <input name="cdi_adresseligne2" placeholder="Adresse ligne 2 ..." type="text" pattern=".{0,}" value="'. $adresseligne2 . '"/> (optionnel)</p>' ;
          $html .= '<p><p>Code postal : <input name="cdi_codepostal" placeholder="Code postal ..." required pattern="[0-9]{5,5}"type="text" pattern="[0-9]{5,5}" value="'. $codepostal . '"/></p>' ;
          $html .= '<p><p>Ville : <input name="cdi_ville" placeholder="Ville ..." type="text" required pattern=".{3,}" value="'. $ville . '"/></p>' ;
          $html .= '<p><p>Pays : <input name="cdi_pays" placeholder="Pays ..." type="text" required pattern=".{3,}" value="'. $paysname . '"/></p>' ;
          $html .= '<p><p>Téléphone : <input name="cdi_telephone" placeholder="Téléphone ..." type="text" required pattern="[0-9]{10,10}" value="'. $telephone . '"/></p>' ;
          $html .= '<p><p>Email : <input name="cdi_email" placeholder="Email ..." type="text" required pattern="[a-z0-9._%+-]+@[a-z0-9.-]+\.[a-z]{2,}$" value="'. $email . '"/></p>' ;
          $html .= '<p>Url site : ' . $urlsite . '</p>' ;

          $html .= '<p><strong>Les vérifications que vous devez faire :</strong></p>' ;
          $html .= '<p>Vous pourrez compléter ou modifier la plupart de ces données depuis votre console de gestion CDI (colissimodeliveryintegration.com). Vous devez cependant être très vigilant sur certains points :</p>' ;
          $html .= "<p> - Votre adresse email. Elle ne sera pas modifiable sur la console CDI, et c'est le seul moyen pour vous communiquer votre identifiant et votre clè d'authentification. Par ailleurs, cet email doit être unique et il ne peut pas exister 2 adhésions CDI avec le même email. Cette adresse email est supposée être sous la responsabilité de l'entreprise devenant adhérente de CDI.</p>" ;
          $html .= "<p> - Si l'entreprise adhérente utilise, de façon ponctuelle ou permanente, un prestataire technique, les données à faire figurer doivent restées celles de l'entreprise adhérente qui seule sera contractante de CDI.</p>" ;
          $html .= "<p> - Votre url de site. Il faut que l'url déclarée dans votre adhésion CDI+ soit la même que celle du site effectuant les requêtes au serveur CDI. Vous pourrez néanmoins modifier cette url depuis votre console CDI.</p>" ;
          $html .= "<p> - L’existence chez votre hébergeur de connexions sortantes opérationnelles. En effet, certains hébergeurs de sites ne traitent pas les connexions sortantes avec une qualité ou une continuité suffisante pour leurs hébergement mutualisés, alors qu'elles sont indispensables pour le fonctionnement de CDI+. Pour vérifier si votre connexion sortante fonctionne, il suffit de cliquer sur le bouton «Information CDI» : Si aucune information ne vous ait restituée, cela veut dire que votre hébergement n'est pas compatible et que votre adhésion ne peut pas être prise en compte avec cet hébergeur. Vous devez changer d'hébergement pour pouvoir bénéficier de CDI+.</p>" ;
          $html .= "<p> - Le fonctionnement complet de CDI+ ne pourra être assuré qui si vous possédez, avec La Poste, un contrat Entreprise 'Web Services' incluant les 3 WS : WS Affranchissement + WS points de livraison + WS Suivi .</p>" ;

          $html .= '<p><strong>Que va-t-il se passer à la validation ?</strong></p>' ;
          $html .= "<p>Vous allez recevoir un mail sur votre adresse email " . $email . " pour vous communiquer votre identifiant CDI et une clé initiale d'authentification.</p>" ;
          $html .= "<p>Vous irez alors sur votre console CDI (colissimodeliveryintegration.com) et dès votre connection, vous aurez à valider les Conditions Générales et à vous générer une nouvelle clé d'authentification.</p>" ;
          $html .= "<p>Pour que votre site soit connecté à CDI+, vous devrez alors renseigner sur votre site, dans les 'Réglages généraux' de CDI (en bas de page, zone 'Réservé aux abonné CDI+') votre numéro de contrat et votre clé d'authentification définitive. Le numéro de contrat et la clé d'authentification vous servent à la fois à connecter votre site à CDI+ et à accéder à votre console de gestion.</p>" ;
          $html .= "<p><em>CDI vous souhaite la bienvenue et vous acceuille avec un abonnement CDI+ gratuit de 35 jours.</em></p>" ;
          $html .= "<p>---------------------------------------</p>" ;
          $html .= "<p>Merci pour votre confiance.</p>" ;

          $html .= '<input name="cdi_annulation" type="submit" value="Annuler" style="float:left; margin-bottom:15px;" title="Annuler votre procédure d\'abonnement à CDI+." />' ;
          $html .= '<input name="cdi_sabonner_cdiplus" type="submit" value="S\'abonner à CDI+" style="float:right; margin-bottom:15px;" title="S\'abonner à CDI+" />' ; 
          $html .= '<p style="color:white;">-</p>' ;

          $html .= '</div></div>' ;
          echo $html ;
        }
      }
      if ($_SERVER['REQUEST_METHOD'] == "POST" and ISSET($_POST['cdi_sabonner_cdiplus'])) {
        $xml = '<xmldata>' .
               '<prenom>' . $_POST['cdi_prenom'] . '</prenom>' .
               '<nom>' . $_POST['cdi_nom'] . '</nom>' .
               '<entreprise>' . $_POST['cdi_entreprise'] . '</entreprise>' .
               '<siret>' . $_POST['cdi_siret'] . '</siret>' .
               '<adresseligne1>' . $_POST['cdi_adresseligne1'] . '</adresseligne1>' .
               '<adresseligne2>' . $_POST['cdi_adresseligne2'] . '</adresseligne2>' .
               '<codeppostal>' . $_POST['cdi_codepostal'] . '</codeppostal>' .
               '<ville>' . $_POST['cdi_ville'] . '</ville>' .
               '<pays>' . $_POST['cdi_pays'] . '</pays>' .
               '<telephone>' . $_POST['cdi_telephone'] . '</telephone>' .
               '<email>' . $_POST['cdi_email'] . '</email>' .
               '<urlsite>' . get_site_url() . '</urlsite>' .
               '</xmldata>' ;
        $xml = base64_encode($xml . '<lastisconnect>' . get_option('cdi_date_lastisconnect')  . '</lastisconnect><lastwsauto>' . get_option('cdi_date_lastwsauto')  . '</lastwsauto>');
        try {
          $errorfilegetcontents = false ;
          $postdata = array(
                  'api' => 'cdi',
                  'version' => get_option('cdi_options_version'),
                  'contractnumber' => '0',
                  'password' => '0',
                  'carrier' => '000',
                  'action' => 'ad',
                  'siteurl' => rawurlencode(get_site_url()),
                  'xml' => $xml );
          $result = self::cdi_url_get_contents( get_option('WC_settings_tab_colissimo_domain') , $postdata);
          if ($result === false) {
            WC_function_Colissimo::cdi_debug(__LINE__ ,__FILE__ , $result, 'tec');
            $errorfilegetcontents = true ;
          }
        } catch (Exception $e) {
          // Handle exception
          WC_function_Colissimo::cdi_debug(__LINE__ ,__FILE__ , $e, 'tec');
          $errorfilegetcontents = true ;
        }
        $result = base64_decode($result);
        if ($errorfilegetcontents == false) { // no error for client and server sides
          if (WC_function_Colissimo::get_string_between($result, '<id>', '</id>') == '0') {
            $return = "Demande adhésion CDI+ : Prise en compte. Un mail vous est envoyé sur l'adresse email que vous avez indiquée (" . $_POST['cdi_email'] . ") pour vous communiquer vos données d'accès. " ;
          }else{
            $return = 'Demande adhésion CDI+ : ' . WC_function_Colissimo::get_string_between($result, '<id>', '</id>') . ' ' . WC_function_Colissimo::get_string_between($result, '<messageContent>', '</messageContent>') ;
          }
        }else{
          $return = 'Demande adhésion CDI+ : Erreur technique' . $result ;
        }
      }
      return $return ;
    }
   // ***************************************************************************************************
    public static function cdi_sanitize_pil($sw) {
      $array=(array)json_decode(base64_decode(get_option('cdi_modai'))) ;
      $array = (array) $array["M03"] ;
      if (get_option('cdi_modal')) {
        $i = 1 ; if (WC_function_Colissimo::cdi_isconnected()) $i = $i+1 ;
        $modal = (array) json_decode(base64_decode(get_option('cdi_modal'))) ;
        $array = (array) $modal["M03-" . $i] ;
      }
      return $array[$sw] ;
    }
   // ***************************************************************************************************
    public static function cdi_sanitize_sec() {
      $array=(array)json_decode(base64_decode(get_option('cdi_modai'))) ;
      $array = (array) $array["M01"] ;
      if (get_option('cdi_modal')) {
        $i = 1 ; if (WC_function_Colissimo::cdi_isconnected()) $i = $i+1 ;
        $modal = (array) json_decode(base64_decode(get_option('cdi_modal'))) ;
        $array = (array) $modal["M01-" . $i] ;
      }
      return $array;
    }
   // ***************************************************************************************************
    public static function cdi_hex_dump($data, $newline="\n") {
      static $return = '';
      static $from = '';
      static $to = '';
      static $width = 16; # number of bytes per line
      static $pad = '.'; # padding for non-visible characters
      if ($from==='') {
        for ($i=0; $i<=0xFF; $i++) {
          $from .= chr($i);
          $to .= ($i >= 0x20 && $i <= 0x7E) ? chr($i) : $pad;
        }
      }
      $hex = str_split(bin2hex($data), $width*2);
      $chars = str_split(strtr($data, $from, $to), $width);
      $offset = 0;
      foreach ($hex as $i => $line) {
        //echo sprintf('%6X',$offset).' : '.implode(' ', str_split($line,2)) . ' [' . $chars[$i] . ']' . $newline;
        $return .= sprintf('%6X',$offset).' : '.implode(' ', str_split($line,2))  . ' [' . $chars[$i] . ']' . $newline ;
        $offset += $width;
      }
      return $return ;
    }
   // ***************************************************************************************************
    public static function cdi_load_file_get_contents($url, $postdata, $referer, $timeout=null) {
        if ($postdata == null and $referer == null) {      
          $return = file_get_contents($url);
        }else{
          $http = array ( 'method'  => 'POST',
                          'header'  => "Content-type: application/x-www-form-urlencoded" ,
                          'request_fulluri' => true );
          if ($referer) {
            $http['referer'] =  $referer ;
            $http['user_agent'] =  $referer ;
          }
          if ($timeout) {
            $http['timeout'] =  $timeout ;
          }
          if ($postdata) {
            $postdata = http_build_query($postdata) ;
            $http['content'] = $postdata ;
          }
          $opts = array('http' => $http ) ;
          $context  = stream_context_create($opts);
          $return = file_get_contents( $url, false, $context);
        }
        return $return ;
    }
    public static function cdi_load_curl_exec($url, $postdata, $referer, $timeout=null) {
        $curl = curl_init($url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($curl, CURLOPT_HEADER, FALSE); 
        if ($referer) {
          curl_setopt($curl, CURLOPT_REFERER, $referer); 
          curl_setopt($curl, CURLOPT_USERAGENT, $referer); 
        }
        if ($postdata !== null) {      
          curl_setopt($curl, CURLOPT_POST, TRUE);
          curl_setopt($curl, CURLOPT_POSTFIELDS, $postdata);
        }
        if ($timeout !== null) {      
          curl_setopt($curl, CURLOPT_TIMEOUT, $timeout);
        }
        $return = curl_exec($curl);
        if($return === false) {
          WC_function_Colissimo::cdi_debug(__LINE__ ,__FILE__ , 'Erreur Curl : ' . curl_error($curl), 'tec');
        }
        curl_close($curl);
        return $return ;
    }
    public static function cdi_url_get_contents($url, $postdata=null, $referer=null, $timeout=null) {
      if (get_option('wc_settings_tab_colissimo_getcontentmode') == 'filegetcontents') {
        if (ini_get('allow_url_fopen') == true) {
          return self::cdi_load_file_get_contents($url, $postdata, $referer, $timeout);
        }elseif (function_exists('curl_init')) {
          return self::cdi_load_curl_exec($url, $postdata, $referer, $timeout);
        }else{
          WC_function_Colissimo::cdi_debug(__LINE__ ,__FILE__ , "CDI get content error : allow_url_fopen not allowed and curl not loaded.", 'tec');
          return false ;
        }
      }else{
        if (function_exists('curl_init')) {
          return self::cdi_load_curl_exec($url, $postdata, $referer, $timeout);
        }elseif (ini_get('allow_url_fopen') == true) {
          return self::cdi_load_file_get_contents($url, $postdata, $referer, $timeout);
        }else{
          WC_function_Colissimo::cdi_debug(__LINE__ ,__FILE__ , "CDI get content error : curl not loaded and allow_url_fopen not allowed.", 'tec');
          return false ;
        }
      }
    }
   // ***************************************************************************************************
    public static function cdi_checksettings() {
      $all_options = wp_load_alloptions();
      $settingscdi = array();
      foreach( $all_options as $name => $value ) {
        if(stristr($name, 'wc_settings_tab_colissimo_')){
          $settingscdi[$name] = $value ;
        }
      }
      if (is_array($settingscdi)) {
        $r =  ksort ($settingscdi) ;
      }
      $return = sanitize_text_field(json_encode($settingscdi)) ;
      $return = preg_replace('/[^a-zA-Z0-9]+/' , '', $return);
      return $return ;
    }
   // ***************************************************************************************************
    public static function cdi_button_support() {
      ?><em></em><input id="cdi_button_support" name="cdi_button_support" type="submit" value="Support CDI+" style="float: left; background-color:#0085ba; color:white; font-weight: bold;" title="Cliquez pour demander un support CDI+." /><em></em><?php
      //$openwindowsdebug1 = get_option('WC_settings_tab_colissimo_domain') . "/support/?user_login=" . get_option('wc_settings_tab_colissimo_cdiplus_ContractNumber') . "&user_pass=" . get_option('wc_settings_tab_colissimo_cdiplus_Password') ; // Abandonné
      $openwindowsdebug = get_option('WC_settings_tab_colissimo_domain') . "/support/" ;
      //WC_function_Colissimo::cdi_debug(__LINE__ ,__FILE__ , $openwindowsdebug, 'msg');
      if ($_SERVER['REQUEST_METHOD'] == "POST" and ISSET($_POST['cdi_button_support'])) {
        ?>
        </a><a style="background-color:white; color:black;">
        <br />
        <div style="margin:10px; padding:10px; border:5px solid blue; background-color:white; color:black;">
        <p> </p>
        <h3>Votre support personnalisé CDI+</h3>
        <p>Comme abonné de CDI+ vous bénéficiez d'un support qui couvre à la fois l'utilisation de votre console CDI, et l'utilisation du plugin CDI.</p> 
        <p>Pour solliciter le support, veuillez confirmer votre demande en cliquant sur le bouton 'Demande de support', sinon sur 'Annuler'. Vous ouvrirez alors une nouvelle fenetre sur votre console de gestion (Accueil -> Support CDI+) pour définir votre problème. Un contact sera alors pris avec vous par le support CDI+, via le mail que vous avez déclaré lors de votre inscription CDI+.</p>
        <p>Vous pouvez également poster votre demande de support directement depuis votre console de gestion. </p>
        <p>Pour des raisons de tracabilité, en aucun cas une demande directe de support ne passant pas par ces procèdures ne pourra être prise en compte. </p>
        <p> </p>
        <button type="submit" id="cdi_button_support_annul" name="cdi_button_support_annul">Annuler</button>
        <button type="submit" id="cdi_button_support_confirm" name="cdi_button_support_confirm" style="float: right;" onclick="window.open('<?php echo $openwindowsdebug ; ?>');">Demande de support</button>
        </div>
        <a> </a>
        <?php
      }
    }
   // ***************************************************************************************************
    public static function cdi_checkdatetime12($datetime) {
      if (!$datetime 
        OR !ctype_digit($datetime) 
        OR strlen($datetime) !== 12
        OR (substr($datetime,0,2) < '17' OR substr($datetime,0,2) > '46')
        OR (substr($datetime,2,2) < '1' OR substr($datetime,2,2) > '12')
        OR (substr($datetime,4,2) < '1' OR substr($datetime,4,2) > '31')
        OR (substr($datetime,6,2) < '0' OR substr($datetime,6,2) > '23')
        OR (substr($datetime,8,2) < '0' OR substr($datetime,8,2) > '59')
        OR (substr($datetime,10,2) < '0' OR substr($datetime,10,2) > '59') ) {
        return false ;
      }else{
        return true ;
      }
    }


}

?>
