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
/* Colissimo Choix de Livraison (Pickup Location / Point de Retrait)                    */
/****************************************************************************************/

class WC_colissimo_choix_livraison {
  public static function init()  {
    if (get_option('wc_settings_tab_colissimo_methodreferal') == 'yes') {
      add_action('woocommerce_cart_calculate_fees', __CLASS__ . '::cdi_woocommerce_cart_calculate_fees' ); 
      add_action('woocommerce_review_order_after_cart_contents' ,  __CLASS__ . '::cdi_woocommerce_review_order_after_cart_contents');
      add_filter('woocommerce_checkout_posted_data',  __CLASS__ . '::cdi_woocommerce_checkout_posted_data');      
      add_action('woocommerce_checkout_update_order_meta',  __CLASS__ . '::cdi_woocommerce_checkout_update_order_meta', 10, 2 );
      add_action('wp_ajax_set_pickuplocation',  __CLASS__ . '::cdi_callback_set_pickuplocation');
      add_action('wp_ajax_nopriv_set_pickuplocation',  __CLASS__ . '::cdi_callback_set_pickuplocation');
      add_action('wp_ajax_set_pickupgooglemaps',  __CLASS__ . '::cdi_callback_show_pickupgooglemaps');
      add_action('wp_ajax_nopriv_set_pickupgooglemaps',  __CLASS__ . '::cdi_callback_show_pickupgooglemaps');
      add_action('wp_head',   __CLASS__ . '::cdi_choix_livraison_header');
      add_action('wp_footer',   __CLASS__ . '::cdi_wp_footer_googlemaps_refreshiddentheme',100);
      add_filter('woocommerce_package_rates',  __CLASS__ . '::cdi_woocommerce_package_rates', 100, 2 );
      add_filter('cdi_filterbool_tobeornottobe_shipping_rate',  __CLASS__ . '::cdi_ex_filterbool_tobeornottobe_shipping_rate', 10, 2 );
      require_once dirname(__FILE__) . '/ColissimoPR/ColissimoPRAutoload.php';
    }
  }

  public static function cdi_woocommerce_package_rates( $rates, $package ) { 
    $newrates = array();
    $chosen_shipping = WC()->session->get('chosen_shipping_methods')[0] ;
    if (isset( $package['recurring_cart_key'] ) && WC_function_Colissimo::cdi_isconnected()) {
      // Force only one package $rate if recurring_cart_key package (Case WC subscription)
      //error_log('*** LOG CDI - LINE:' . __LINE__ . ' FILE:' . __FILE__ . ' ***: ' . print_R($rates , TRUE));
      foreach ((array) $rates as $rate_id => $rate ) { // Priority is to try to get the rate having the same id
        if ($rate_id == $chosen_shipping) { 
          $newrates[ $rate_id ] = apply_filters( 'cdi_filterarray_wcs_reccuring_rate', $rate, $rates, $package ) ;
          break;
        }
      }
      if (count($newrates) == 0) { // If no rate has been found, the first in $rates is taken
        foreach ((array) $rates as $rate_id => $rate ) {
          $newrates[ $rate_id ] = apply_filters( 'cdi_filterarray_wcs_reccuring_rate', $rate, $rates, $package ) ;
          break;
        }
      }
    }else{
      // To select in package rates only the first exclusive method found
      $arrayexclusivemethodoption = explode(',', get_option('wc_settings_tab_colissimo_exclusiveshippingmethod')) ;
      $arrayexclusivemethod = array_map("trim", $arrayexclusivemethodoption);
      foreach ((array) $rates as $rate_id => $rate ) {
        $startofid = explode(':', $rate->id) ;
        //WC_function_Colissimo::cdi_debug(__LINE__ ,__FILE__ , $startofid[0] . ' - ' . get_option('wc_settings_tab_colissimo_exclusiveshippingmethod'), 'msg') ;
        if (in_array($startofid[0], $arrayexclusivemethod)) { // Is it a racine-name ?
          $newrates[ $rate_id ] = $rate;
          break;
        }
        if (isset($startofid[1]) && is_numeric($startofid[1]) ) { // Is it a shipping zone method 2.6 ?
          if (in_array($startofid[0] . ':' . $startofid[1], $arrayexclusivemethod)) { // So now test if it is racine-name:instance ?
            $newrates[ $rate_id ] = $rate;
            break;
          }
        }
      }
    }
    return ! empty( $newrates ) ? $newrates : $rates;
  }

  public static function cdi_choix_livraison_header() {
    if (is_checkout()) { // No useful to do this if not the checkout page
      if ((get_option('wc_settings_tab_colissimo_mapengine') == "om") && WC_function_Colissimo::cdi_isconnected()) {
        ?>
         <link rel="stylesheet" href="<?php echo plugin_dir_url( __FILE__ ) . '../css/ol.css' ; ?>" type="text/css">
         <script src="<?php echo plugin_dir_url( __FILE__ ) . '../js/ol.js' ; ?>"></script>
        <?php 
      }else{
        $key = get_option('wc_settings_tab_colissimo_googlemapsapikey') ;
        if ($key == null or $key == '') { // Google maps API depending if key exists
          ?><script type="text/javascript" src="https://maps.google.com/maps/api/js"></script><?php 
        }else{
          ?><script type="text/javascript" src="https://maps.googleapis.com/maps/api/js?key=<?php echo $key; ?>" async defer ></script><?php
        }
      }
    }
  }

  public static function cdi_control_pickup_list($chosen_shipping) { // Verify if in pickup list
    global $woocommerce;
    $pickuplist = str_replace (" ", "", get_option('wc_settings_tab_colissimo_pickupmethodnames')) ;
    $arraypickuplist = explode(',', $pickuplist) ;
    $arraypickuplist = array_map("trim", $arraypickuplist);
    $arraychosen = explode(':', $chosen_shipping); // explode = method : instance : suffixe
    $inpickup = null ;
    $filterrelay = null ;

    if ($woocommerce->customer->get_shipping_address() && $woocommerce->customer->get_shipping_postcode() && $woocommerce->customer->get_shipping_city() && $woocommerce->customer->get_shipping_country()  // An address seems exist
    && $chosen_shipping // and a method exists
    && isset($arraychosen[1]) && is_numeric($arraychosen[1]) ) { // and is a shipping zone method 2.6    
      // Test if in the pickup list and extract filterrelay
      if (in_array($woocommerce->customer->get_shipping_country(), explode(',', get_option('wc_settings_tab_colissimo_ws_InternationalPickupLocationContryCodes')))) { // and is in the pickup country list
        foreach ($arraypickuplist as $x ) {
          $arrx = explode('=', $x) ;
          $arry = explode(':', $arrx[0]) ;
          if (!isset($arry[1]) or !is_numeric($arry[1])) {
            if ($arry[0] == $arraychosen[0]){ // Default without instance num
              if (isset($arrx[1])) {
                $filterrelay = $arrx[1] ;
              }else{
                $filterrelay = '1' ; // Default without the "=0 or 1"
              }
              $inpickup = 1 ;
              break ;
            }
          }else{
            if ($arry[0] . ':' . $arry[1] == $arraychosen[0] . ':' . $arraychosen[1]){ // Ok with instance num
              if (isset($arrx[1])) {
                $filterrelay = $arrx[1] ;
              }else{
                $filterrelay = '1' ; // Default without the "=0 or 1"
              }
              $inpickup = 1 ;
              break ;
            }
          }
        }
      }
    }
    return array($inpickup, $filterrelay) ;
  }

  public static function cdi_get_shipping_and_product() { 
    global $woocommerce;
    // We must consider only one package (or whole cart) for CDI process in WC orders and gateway. The default is cart. But this can be change with a filter
    $chosen_methods = WC()->session->get( 'chosen_shipping_methods' ); // Warning, may not have been updated by WC
    $shiptype_option = get_option('wc_settings_tab_colissimo_shippingpackageincart') ;
    if ($shiptype_option == 'first' ) {
      $rank_method = 0 ; 
    }elseif ($shiptype_option == 'last' ) {
      $rank_method = count($chosen_methods) -1 ; 
    }else{
      $rank_method = -1 ; // cart  
    }
    $rank_method = apply_filters('cdi_filterstring_chosen_shipping', $rank_method, $chosen_methods);
    // Set array of shipping packages => id product to pass to WC process. WC dont do that !
    // Search chosen_shipping to consider
    $packages = WC()->shipping->get_packages() ;
    if (count($packages) == 0 ) return ; // Seems to be a WC bug (erroneous call without packages returned when in the before checkout filter)
    $chosen_shipping = '' ;
    if ($rank_method !== -1) {
      $i = 0 ;
      foreach ( $packages as $package ) {
        $chosen_products = array() ;
        foreach ( $package['contents'] as $item ) {
          $chosen_products[] = $item['product_id'] ;
        }
        if ($i == $rank_method) break ;
        $i = $i + 1 ;
      }
      $i = 0 ;
      foreach ($chosen_methods as $chosen_method ) {
        $chosen_shipping = $chosen_method ; // $chosen_shipping = method : instance : suffixe
        if ($i == $rank_method) break ;
        $i = $i + 1 ;
      }
    }else{ // Case of whole cart for CDI
      $chosen_products = array() ;
      foreach ( $packages as $package ) {
        foreach ( $package['contents'] as $item ) {
          $chosen_products[] = $item['product_id'] ;
        }
      }
      foreach ($chosen_methods as $chosen_method ) {
        $chosen_shipping = $chosen_method ; // $chosen_shipping = method : instance : suffixe - Only the first chosen_method is selected (named Expedition)
        break ;
      }
    }
    // search shipping method label
    $shipping_method_name = '' ;
    $needs_shipping = WC()->cart->needs_shipping();
    if ($needs_shipping) { // Because WC()->session->get( 'chosen_shipping_methods' ) don't work for all cases
      foreach ( $packages as $package ) {
        foreach ( $package['rates'] as $rate_id => $shipping_rate ) {
          if ($rate_id == $chosen_shipping) {
            $shipping_method_name = $shipping_rate->label ;
            break ;
          }
        }
      } 
    }else{
      $chosen_shipping = '';
      $shipping_method_name = '' ;
    }
    WC()->session->set( 'cdi_refshippingmethod' , $chosen_shipping);
    WC()->session->set( 'cdi_chosen_products' , $chosen_products);
    WC()->session->set( 'cdi_shipping_method_name' , $shipping_method_name);
  }

  public static function cdi_woocommerce_cart_calculate_fees($cart) { // Activate when calculate_fees
    global $woocommerce;
    global $msgtofrontend;
    if (is_checkout() /*&& is_ajax()*/) { // No useful to do this if not the checkout page AND only if Ajax
      // Suppress of Ajax condition starting from CDI 3.7.8 (no more rebound)
      //error_log('*** LOG CDI - LINE:' . __LINE__ . ' FILE:' . __FILE__ . ' ***: ' . print_R($cart , TRUE));
      if (! empty( $cart->recurring_cart_key ) && WC_function_Colissimo::cdi_isconnected()) return ; // Case of WC subscription plugin processing 
      // Initial shipping settings for all shipping method
      $needs_shipping = WC()->cart->needs_shipping();
      if ($needs_shipping) {
        self::cdi_get_shipping_and_product() ;
        $chosen_shipping = WC()->session->get( 'cdi_refshippingmethod');
        $chosen_products = WC()->session->get( 'cdi_chosen_products');
        $shipping_method_name = WC()->session->get( 'cdi_shipping_method_name');
      }else{
        $chosen_shipping = null ;
        WC()->session->set( 'cdi_refshippingmethod' , $chosen_shipping);
        $chosen_products = null ;
        WC()->session->set( 'cdi_chosen_products' , $chosen_products);
        $shipping_method_name = null ;
        WC()->session->set( 'cdi_shipping_method_name' , $shipping_method_name);
      }
      // Verify if nothing has been done in last 300s (to avoid multiple calls)
      $tokentimereplay = time() ;
      $oldtokentimereplay = WC()->session->get('cdi_tokentimereplay') ;
      if (!$oldtokentimereplay OR (($oldtokentimereplay + 300) < $tokentimereplay)) { 
        $tokentimereplaypass = 1;
      }else{
        $tokentimereplaypass = 0;
      }
      WC()->session->set('cdi_tokentimereplay', $tokentimereplay) ;

      // Verify if a change in shipping method or shipping data or if nothing has been done from a long time
      $unikkeydisplpickup = $chosen_shipping . '-' . $woocommerce->customer->get_shipping_country() . '-' . $woocommerce->customer->get_shipping_city() . '-' . $woocommerce->customer->get_shipping_postcode() . '-' . $woocommerce->customer->get_shipping_address() ;
      $lastunikkeydisplpickup = WC()->session->get( 'cdi_unikkeydisplpickup' );
      if (!isset($lastunikkeydisplpickup) or $lastunikkeydisplpickup == '' or $lastunikkeydisplpickup !== $unikkeydisplpickup or $tokentimereplaypass == 1) {
        WC()->session->set( 'cdi_forcedproductcode' , '' );
        WC()->session->set( 'cdi_pickuplocationid' , '');
        WC()->session->set( 'cdi_pickuplocationlabel' , '');
        WC()->session->set( 'cdi_return_ws_liste_points_livraison' , '' );
        WC()->session->set( 'cdi_unikkeydisplpickup' , $unikkeydisplpickup );
      }else{
        return;
      }
      WC_function_Colissimo::cdi_debug(__LINE__ ,__FILE__ , $chosen_shipping, 'msg');
      WC_function_Colissimo::cdi_debug(__LINE__ ,__FILE__ , $chosen_products, 'msg');
      $arraychosen = explode(':', $chosen_shipping); // explode = method : instance : suffixe
      $testpickup = self::cdi_control_pickup_list($chosen_shipping) ;
      $inpickup = $testpickup['0'] ;
      $filterrelay = $testpickup['1'] ;
      if ($inpickup == 1) {  // We are in the pickup list
        $cdi_return_ws_liste_points_livraison = WC_colissimo_choix_livraison::cdi_get_wscolissimo_points_livraison($filterrelay);
        if ($cdi_return_ws_liste_points_livraison == false) { // Check if WS Colissimo is correct ?!
          // it seems as if an error as Web service. The address seems invalid
          wc_add_notice( __('Colissimo does not recognize this address. Please try again.', 'colissimo-delivery-integration' ) . $msgtofrontend, $notice_type = 'error' ) ;
        }else{
          WC()->session->set( 'cdi_return_ws_liste_points_livraison' , $cdi_return_ws_liste_points_livraison );
        }
      }else{
        // Not in the pickup list, so test if in the forced product code list
        $forcedproductcode = get_option('wc_settings_tab_colissimo_forcedproductcodes') ;
        $arrayforcedproductcode = explode(',', $forcedproductcode) ;
        $arrayforcedproductcode = array_map("trim", $arrayforcedproductcode);
        $codeproductfound = '';
        foreach ($arrayforcedproductcode as $relation) {
          $arrayrelation = explode('=', $relation) ;
          if (isset($arraychosen[1])){ // test case for legacy shipping method non WC 2.6
            $arraychosenun = $arraychosen[0] . ':' . $arraychosen[1] ;
          }else{
            $arraychosenun = $arraychosen[0] ;
          }
          if ($arrayrelation[0] && (($arrayrelation[0] == $arraychosen[0]) OR ($arrayrelation[0] == $arraychosenun))) {
            $codeproductfound = $arrayrelation[1] ;
          }
        }
        WC()->session->set( 'cdi_forcedproductcode' , $codeproductfound );
      }
    } //End if checkout
  }

public static function cdi_test_laposte_status () { 
    // Test if server ssl and Colissimo Website are ok  - Only every 2 mn to avoid Colissimo servers
    global $msgtofrontend;
    $currenttimer = time() ;
    $oldtimercolissimo = get_option('cdi_testcarriercolissimo') ;
    if (!$oldtimercolissimo or ($currenttimer > ($oldtimercolissimo + 120))) {
      update_option('cdi_testcarriercolissimo', $currenttimer) ;
      $urlsupervision = 'http://ws.colissimo.fr/supervision-wspudo/supervision.jsp' ;
      $etat = WC_function_Colissimo::cdi_url_get_contents($urlsupervision) ;
      if (!strpos('x' . $etat, "[OK]") > 0) {
        $msgtofrontend = ' CDI : Colissimo urlsupervision access denied.' ;
        WC_function_Colissimo::cdi_debug(__LINE__ ,__FILE__ , 'Colissimo urlsupervision access denied.', 'tec');
        $return = false;
      }else{
        $return = true;
      }
      update_option('cdi_testcarriercolissimo', $currenttimer) ;      
      update_option('cdi_testcarriercolissimoresult', $return) ;
    }else{
      $return = get_option('cdi_testcarriercolissimoresult') ;
    }
    return $return ;    
}

public static function cdi_get_wscolissimo_points_livraison($filterrelay) {
    global $woocommerce;
    global $msgtofrontend;

    if (self::cdi_test_laposte_status() === false) {
      return false;
    }

    $wsdl = array();
    $wsdl[ColissimoPRWsdlClass::WSDL_URL] = 'https://ws.colissimo.fr/pointretrait-ws-cxf/PointRetraitServiceWS/2.0?wsdl';
    $wsdl[ColissimoPRWsdlClass::WSDL_CACHE_WSDL] = WSDL_CACHE_NONE;
    $wsdl[ColissimoPRWsdlClass::WSDL_TRACE] = true;

    $wsdlObject = new ColissimoPRStructFindRDVPointRetraitAcheminement($wsdl);

    $wsdlObject->setAccountNumber(get_option('wc_settings_tab_colissimo_ws_ContractNumber')); 
    $wsdlObject->setPassword(get_option('wc_settings_tab_colissimo_ws_Password'));
    $calc = WC_function_Colissimo::cdi_sanitize_laposte_voie( $woocommerce->customer->get_shipping_address() . ' ' . $woocommerce->customer->get_shipping_address_2() ) ;
    $wsdlObject->setAddress($calc);
    $wsdlObject->setZipCode($woocommerce->customer->get_shipping_postcode());
    $wsdlObject->setCity(WC_function_Colissimo::cdi_sanitize_laposte_voie($woocommerce->customer->get_shipping_city()));
    $wsdlObject->setCountryCode($woocommerce->customer->get_shipping_country());
    // Here, CDI considers only the total cart weight (all packages) and not the current shipping package weight which would be smaller
    $weightrelay = (float)$woocommerce->cart->cart_contents_weight;
    if (get_option( 'woocommerce_weight_unit' ) == 'kg') { // Convert kg to g
      $weightrelay = $weightrelay * 1000 ;
    }
    $weightrelay = round($weightrelay + get_option('wc_settings_tab_colissimo_parcelweight')) ; 
    if (!$weightrelay or $weightrelay == 0) {
      $weightrelay = 100; // 0g is not good but 1g would be enought to not break the Colissimo WS
    }
    $wsdlObject->setWeight($weightrelay); 
    $calc = get_option('wc_settings_tab_colissimo_ws_OffsetDepositDate');
    $wsdlObject->setShippingDate(date('d/m/Y',strtotime("+$calc day")));
    $wsdlObject->setFilterRelay($filterrelay); 
    $wsdlObject->setRequestId('CDI-' . date('YmdHis'));
    //$wsdlObject->setLang($woocommerce->customer->get_shipping_country()); 
    $wsdlObject->setOptionInter('1');

    $colissimoPRServiceFind = new ColissimoPRServiceFind();
    if($colissimoPRServiceFind->findRDVPointRetraitAcheminement(new ColissimoPRStructFindRDVPointRetraitAcheminement($wsdlObject))) {
      $ok = $colissimoPRServiceFind->getResult();
      $retid = $ok->return->errorCode;
      $retmessageContent = $ok->return->errorMessage;
      if ($retid == 0) {
        return $ok ;
      }else{
        // process the error from soap server
        WC_function_Colissimo::cdi_debug(__LINE__ ,__FILE__ , $retid, 'exp');
        WC_function_Colissimo::cdi_debug(__LINE__ ,__FILE__ , $retmessageContent, 'exp');
        $last = $colissimoPRServiceFind->getLastRequest();
        WC_function_Colissimo::cdi_debug(__LINE__ ,__FILE__ , $last, 'exp');
        $ret = $colissimoPRServiceFind->getLastResponse();
        WC_function_Colissimo::cdi_debug(__LINE__ ,__FILE__ , $ret, 'exp');
        $msgtofrontend = ' (' . WC_colissimo_choix_livraison::get_string_between($ret, '<errorCode>', '</errorCode>') . ' - ' . WC_colissimo_choix_livraison::get_string_between($ret, '<errorMessage>', '</errorMessage>')  .  ')' ;
        return false ;
      }
    }else{
      // process the error from soap client
      $nok = $colissimoPRServiceFind->getLastError();
      $last = $colissimoPRServiceFind->getLastRequest();
      $ret = $colissimoPRServiceFind->getLastResponse();
      WC_function_Colissimo::cdi_debug(__LINE__ ,__FILE__ , $last, 'tec');
      WC_function_Colissimo::cdi_debug(__LINE__ ,__FILE__ , $ret, 'tec');
      return false ;
    }
  }

  public static function cdi_woocommerce_review_order_after_cart_contents() { // When choice shipping done, display the pickup box
    global $woocommerce;
    if (true /*is_ajax()*/) {
      $token = time() ; // To view only the newest token div in js
      $cdi_return_ws_liste_points_livraison = WC()->session->get('cdi_return_ws_liste_points_livraison') ;
      $urlglobeopen = plugins_url( 'images/globeopen.png', dirname(__FILE__)) ;
      $urlglobeclose = plugins_url( 'images/globeclose.png', dirname(__FILE__)) ;
      if($cdi_return_ws_liste_points_livraison) {
        $listePointRetraitAcheminement = $cdi_return_ws_liste_points_livraison->return->listePointRetraitAcheminement;
        $arrayabstract = array() ;
        $nbpointretrait = 0;
        foreach ($listePointRetraitAcheminement as $PointRetrait) {
          if ($PointRetrait->reseau !== 'X00' && $nbpointretrait < 30) { // Exclude X00 networks
            $nbpointretrait = $nbpointretrait +1;
            $arrayabstract[] = WC_function_Colissimo::cdi_sanitize_laposte_voie($PointRetrait->nom) . ' =&gt; ' .
                               $PointRetrait->adresse1 . ' ' .
                               $PointRetrait->adresse2 . ' ' .
                               $PointRetrait->codePostal . ' ' .
                               $PointRetrait->localite . ' =&gt; Distance: ' .
                               $PointRetrait->distanceEnMetre . 'm =&gt; Id: ' .
                               $PointRetrait->identifiant ;
          }
        }
        if (get_option('wc_settings_tab_colissimo_mapopen') == 'yes') {
          $htmlmap = WC_colissimo_choix_livraison::cdi_calculate_js_googlemaps() ;
          $htmlurlglobe = $urlglobeclose ;
        }else{
          $htmlmap = '' ;
          $htmlurlglobe = $urlglobeopen ;
        }
        $insertmsg = '' ;
        $insertmsg .= '<div id="popupmap" style="width:100%;">' . '</div>' ; // Place reserved for the popup google maps
        $insertmsg = $insertmsg . '<div id="zoneiconmap" style="width:100%;">' ;
        $insertmsg = $insertmsg . '<span>' . __('Select your pickup locations :', 'colissimo-delivery-integration') . '</span>' ;
        $insertmsg = $insertmsg . '<span id="iconpopupmap">' ; 
        $insertmsg = $insertmsg . '<a title="Pickup locations map" style="float:right;"> ' ;
        $insertmsg = $insertmsg . '<input type="image" id="pickupgooglemaps" name="pickupgooglemaps" value="pickupgooglemaps" src="' . $htmlurlglobe . '"> ' ; 
        $insertmsg = $insertmsg . '</a></span>' ;
        $insertselect = '<div style="width:100%; overflow:hidden"><select id="pickupselect" name="pickupselect" style="width:100%; overflow:hidden;">' . '<option value="">' . __('Choose a location', 'colissimo-delivery-integration') . '</option>' ;
        foreach ($arrayabstract as $abstract) {
          $idpt = stristr($abstract, " Id: ") ;
          $idpt = str_replace(" Id: ", '', $idpt);
          $insertselect = $insertselect . '<option style="overflow:hidden;" value=' . $idpt . '>' . $abstract . '</option>' ;
        }
        $insertselect = $insertselect . '</select></div></div>' ;
        $insertmsg = $insertmsg . apply_filters( 'cdi_filterhtml_retrait_selectoptions', $insertselect, $listePointRetraitAcheminement) ;
      }else{
        $insertmsg = '' ;
      }
      $wheremap =  get_option('wc_settings_tab_colissimo_wheremustbeemap') ;
      if (!$wheremap or $wheremap == '') {
        $wheremap =  'insertBefore( jQuery( ".shop_table" ) )' ;
      }
      $whereselectorpickup = apply_filters ('cdi_filterjava_retrait_whereselectorpickup', $wheremap) ; 
      ?><script>
        // In the future, deprecated DOMNodeInserted (Mutation Events) will have to be replaced by a MutationObserver procedure or something better
        jQuery("#order_review").on('DOMNodeInserted', function(cleancdiselectlocation){
          var higher = undefined ;
          jQuery( ".cdiselectlocation" ).each(function( index ) { 
            if (typeof(higher) == "undefined") {
              higher = 0 ;
            }
            var currentID = this.id ;
	    if (higher < currentID) { 
              higher = this.id ;
  	    }
	  });
          jQuery( ".cdiselectlocation" ).each(function( index ) { 
            var currentID = this.id ;
	    if (higher > currentID) { 
             jQuery(this).remove();
  	    }
	  });
          cleancdiselectlocation.preventDefault(); // to prevent woocommerce to trigger checkout
        });
      </script><?php
      ?>
        <div id='<?php echo $token ; ?>' class="cdiselectlocation">
          <?php echo $insertmsg ; ?>
        </div>
      <?php
        $ajaxurl = admin_url('admin-ajax.php');
      ?><script>
        jQuery(document).ready(function(){
          function openclosemap(){
            if (jQuery('#googlemapsopen').length){
              var urlglobeopen = '<?php echo $urlglobeopen; ?>';
              jQuery("#popupmap").html(' ') ;
              jQuery("#pickupgooglemaps" ).attr('src', urlglobeopen) ; 
            }else{
              var data = { 'action': 'set_pickupgooglemaps', 'pickupgooglemaps': 'pickupgooglemaps' };
              var ajaxurl = '<?php echo $ajaxurl; ?>';
              jQuery.post(ajaxurl, data, function(response) {
                var urlglobeclose = '<?php echo $urlglobeclose; ?>';
                jQuery("#popupmap").html(response) ;
                jQuery("#pickupgooglemaps" ).attr('src', urlglobeclose) ; 
              });
            }
          }
          jQuery( ".cdiselectlocation" ).<?php echo $whereselectorpickup; ?>; // insert where the pickupselect will be
          openclosemap()
          jQuery("#pickupgooglemaps").click(function(googlemapevent){ 
            openclosemap()
            googlemapevent.preventDefault(); // to prevent woocommerce to trigger checkout
          });
        });
      </script><?php
      // call ajax for storage of pickupselect
      $jsselectorpickup = 'var pickupselect = document.getElementById("pickupselect").options[document.getElementById("pickupselect").selectedIndex].value;' ;
      $jsselectorpickup = apply_filters ('cdi_filterjava_retrait_selectorpickup', $jsselectorpickup) ;
      ?><script>  
        jQuery(document).ready(function(){
          jQuery("#pickupselect").change(function(){
            <?php echo $jsselectorpickup; ?> // insert here the var pickupselect
            var data = { "action": "set_pickuplocation", "postpickupselect": pickupselect };
            var ajaxurl = "<?php echo $ajaxurl; ?>";
            jQuery.post(ajaxurl, data, function(response) {
              var arrresponse = jQuery.parseJSON(response);
              if (arrresponse[0].length){ // No display if no return
                var html = arrresponse[0].includes("<", 0);
                if (arrresponse[0].includes("</", 0)) { // Is return a html code ?
                  var para = document.createElement("DIV"); 
                  para.setAttribute("id", "customselect");
                  para.style.position = "fixed"; 
                  para.style.width = "80vw";
                  para.style.height = "80vh";
                  para.style.right = "10vw";
                  para.style.top = "10vh";
                  document.body.appendChild(para);    
                  jQuery("#customselect").html(arrresponse[0]) ;
                }else{ // Not html, so display with alert
                  alert(arrresponse[0]);
                }
              }
              if (jQuery("#googlemapsopen").length){ // Refresh google maps if open
                var urlglobeclose = "<?php echo $urlglobeclose; ?>";
                jQuery("#popupmap").html(arrresponse[1]) ;
                jQuery("#pickupgooglemaps" ).attr("src", urlglobeclose) ; 
              }
            });
          });
        });
      </script><?php
     }
   }

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

   public static function cdi_html_retrait_descpickup ($PointRetrait) { 
     $return = '<div id="selretrait" data-value="' . $PointRetrait->identifiant . '" class="cdiselretrait' . $PointRetrait->identifiant . '">' ;
     $return .= '<p style="width:100%; display:inline-block;"><em>(' . $PointRetrait->identifiant . ')</em><a class="selretrait button" style="float: right; border:1px solid black; border-radius: 5px;" id="selretraitshown" >Sélectionner</a></p>' ;
     $return .= '<div id="selretraithidden" style="display:none;"><p style="text-align:center;"><a class="button">Point Retrait sélectionné</a></p></div>' ;
     $return .= '<p style="margin-bottom:0px;"><mark>' . WC_function_Colissimo::cdi_sanitize_laposte_voie($PointRetrait->nom) . '</mark></p>' ;
     $return .= '<p style="margin-bottom:0px;"><mark>' .  addslashes($PointRetrait->adresse1 . ' ' . $PointRetrait->adresse2) . '</mark></p>' ;
     $return .= '<p style=""><mark>' . addslashes($PointRetrait->codePostal . ' ' . $PointRetrait->localite) .  '</mark></p>' ;
     if ($PointRetrait->indiceDeLocalisation) {
       $return .= '<p style=""><mark>' . addslashes($PointRetrait->indiceDeLocalisation) .  '</mark></p>' ;
     }
     $return .= '<p style=""><em>Distance: ' . $PointRetrait->distanceEnMetre . 'm</em></p>' ;
     $return .= '<p style="margin-bottom:0px;"> Lundi ' . $PointRetrait->horairesOuvertureLundi . '</p>' ;
     $return .= '<p style="margin-bottom:0px;"> Mardi ' . $PointRetrait->horairesOuvertureMardi . '</p>' ;
     $return .= '<p style="margin-bottom:0px;"> Mercredi ' . $PointRetrait->horairesOuvertureMercredi . '</p>' ;
     $return .= '<p style="margin-bottom:0px;"> Jeudi ' . $PointRetrait->horairesOuvertureJeudi . '</p>' ;
     $return .= '<p style="margin-bottom:0px;"> Vendredi ' . $PointRetrait->horairesOuvertureVendredi . '</p>' ;
     $return .= '<p style="margin-bottom:0px;"> Samedi ' . $PointRetrait->horairesOuvertureSamedi . '</p>' ;
     $return .= '<p style=""> Dimanche ' . $PointRetrait->horairesOuvertureDimanche . '</p>' ;
     $return .= '<p style="">GPS: ' . $PointRetrait->coordGeolocalisationLatitude . ' ' . $PointRetrait->coordGeolocalisationLongitude .  '</p>' ;
     if ($PointRetrait->parking) $return .= '<p style="margin-bottom:0px;">Parking: ' . $PointRetrait->parking . '</p>' ;
     if ($PointRetrait->accesPersonneMobiliteReduite) $return .= '<p style="margin-bottom:0px;">Mobilité réduite: ' . $PointRetrait->accesPersonneMobiliteReduite . '</p>' ;
     if ($PointRetrait->langue) $return .= '<p style="margin-bottom:0px;">Langue: ' . $PointRetrait->langue . '</p>' ;
     if ($PointRetrait->poidsMaxi) $return .= '<p style="margin-bottom:0px;">Poids maxi: ' . $PointRetrait->poidsMaxi . '</p>' ;
     if ($PointRetrait->loanOfHandlingTool) $return .= '<p style="margin-bottom:0px;">Equipements de manipulation: ' . $PointRetrait->loanOfHandlingTool . '</p>' ;
     $return .= '</div>' ;
     return $return ;
   }

   public static function cdi_calculate_js_googlemaps() { 
     global $woocommerce;
     $urliconmarker = apply_filters( 'cdi_filterurl_retrait_iconmarker', plugins_url( 'images/iconmarker.png', dirname(__FILE__))) ;
     $urliconmarkerselect = apply_filters( 'cdi_filterurl_retrait_iconmarkerselect',plugins_url( 'images/iconmarkerselect.png', dirname(__FILE__))) ;
     $urliconcustomer = apply_filters( 'cdi_filterurl_retrait_iconcustomer',plugins_url( 'images/iconcustomer.png', dirname(__FILE__))) ;
     $cdi_return_ws_liste_points_livraison = WC()->session->get('cdi_return_ws_liste_points_livraison') ;
     if (!is_object($cdi_return_ws_liste_points_livraison) or !property_exists ($cdi_return_ws_liste_points_livraison, 'return' )) {
       return '' ; // Error in $cdi_return_ws_liste_points_livraison
     }
     $listePointRetraitAcheminement = $cdi_return_ws_liste_points_livraison->return->listePointRetraitAcheminement;
     $listmarks = array ();
     $nbpointretrait = 0;
     $latfallback = 0 ;
     $lonfallback = 0 ;
     foreach ($listePointRetraitAcheminement as $PointRetrait) {
       if ($PointRetrait->reseau !== 'X00' && $nbpointretrait < 30) { // Exclude X00 networks
         $nbpointretrait = $nbpointretrait + 1;
         $urlicon = $urliconmarker ;
         $pickuplocationid = WC()->session->get( 'cdi_pickuplocationid') ;
         if ($pickuplocationid !== null and $pickuplocationid !== '' and $pickuplocationid == $PointRetrait->identifiant) {
           $urlicon = $urliconmarkerselect ;
         }
         $viewselected = WC_function_Colissimo::cdi_sanitize_laposte_voie($PointRetrait->nom) . ' =&gt; ' .
                         WC_function_Colissimo::cdi_sanitize_laposte_voie($PointRetrait->adresse1) . ' ' .
                         WC_function_Colissimo::cdi_sanitize_laposte_voie($PointRetrait->adresse2) . ' ' .
                         $PointRetrait->codePostal . ' ' .
                         WC_function_Colissimo::cdi_sanitize_laposte_voie($PointRetrait->localite) . ' =&gt; Distance: ' .
                         $PointRetrait->distanceEnMetre . 'm =&gt; Id: ' .
                         $PointRetrait->identifiant ;
         if ('yes' == get_option('wc_settings_tab_colissimo_selectclickonmap') && WC_function_Colissimo::cdi_isconnected()) {
           eval (WC_function_Colissimo::cdi_eval('18')) ;
         }
         $listmarks[] = array (
               'lati' => $PointRetrait->coordGeolocalisationLatitude ,
               'long' => $PointRetrait->coordGeolocalisationLongitude ,
               'desc' => apply_filters( 'cdi_filterhtml_retrait_descpickup', $viewselected, $PointRetrait  )  ,
               'icon' => $urlicon
               ) ; 
         $latfallback = $latfallback + $PointRetrait->coordGeolocalisationLatitude;
         $lonfallback = $lonfallback + $PointRetrait->coordGeolocalisationLongitude;
       }
     }
     $latfallback = $latfallback / $nbpointretrait ;
     $lonfallback = $lonfallback / $nbpointretrait ;
     if ((get_option('wc_settings_tab_colissimo_mapengine') == "om") && WC_function_Colissimo::cdi_isconnected()) {
       // Calc geolocate of customer
       eval (WC_function_Colissimo::cdi_eval('7')) ;
       $addresscustomer = WC_function_Colissimo::cdi_sanitize_laposte_voie( $woocommerce->customer->get_shipping_address()) . ', '
              . $woocommerce->customer->get_shipping_postcode() . ' '
              . WC_function_Colissimo::cdi_sanitize_laposte_voie($woocommerce->customer->get_shipping_city()) . ', '
              . $woocommerce->customer->get_shipping_country() ;
       $address = str_replace ('  ', ' ', $addresscustomer) ;
       $address = str_replace ('  ', ' ', $address) ;
       $postdata = array ('q' => $address, 'format' => 'json') ; 
       eval (WC_function_Colissimo::cdi_eval('24')) ;
       // Extract lat and lon
       $lat = WC_colissimo_choix_livraison::get_string_between($result, '"lat":"', '"') ;
       $lon = WC_colissimo_choix_livraison::get_string_between($result, '"lon":"', '"') ;
       if (!$lat or !$lon) { // Process fallback or error
         $lat = $latfallback ;
         $lon = $lonfallback ;
         $urliconcustomer = plugins_url( 'images/iconcustomerfallback.png', dirname(__FILE__)) ;
         WC_function_Colissimo::cdi_debug(__LINE__ ,__FILE__ , 'Customer address fallback.', 'tec');
         //wc_add_notice( __( 'Open Map can not geolocate this address. Please try again.', 'colissimo-delivery-integration' ) . ' (' . $address . ')', $notice_type = 'error' );
         //return '' ;
       }
       // Add marker for customer location
       $listmarks[] = array (
               'lati' => $lat ,
               'long' => $lon ,
               'desc' => apply_filters( 'cdi_filterhtml_retrait_desccustomer', $addresscustomer, $woocommerce->customer) ,  
               'icon' => $urliconcustomer
               ) ; 
       $parammap = apply_filters( 'cdi_filterarray_retrait_mapparam', array('z'=>"13", 'w'=>"100%", 'h'=>"400px", 'maptype' => 'ROADMAP', 'styles' => '[]', 'style' => 'border:1px solid gray; margin: 0 auto; position:relative; overflow:auto; ') );
       // 'maptype' and 'styles' not used with OM
       $parammap = array_merge( array( 'id'=>"googlemapsopen", 'lat'=> $lat, 'lon'=>$lon ), $parammap );
       if (is_numeric($parammap['w'])) {
         $parammap['w'] = $parammap['w'] . 'px' ;
       }
       if (is_numeric($parammap['h'])) {
         $parammap['h'] = $parammap['h'] . 'px' ;
       }

       $listsites = '[';
       foreach ($listmarks as $mark) {
         $listsites .= '[' . $mark['lati'] . ',' . $mark['long'] . ',\'' . $mark['desc'] . '\',\'' . $mark['icon'] .  '\'],';
       }
       $listsites = substr($listsites, 0, strlen($listsites) - 1);
       $listsites.= ']';
       eval (WC_function_Colissimo::cdi_eval('23')) ;
       eval (WC_function_Colissimo::cdi_eval('12')) ;
     }else{
       // Calc geolocate of customer
       $addresscustomer = WC_function_Colissimo::cdi_sanitize_laposte_voie( $woocommerce->customer->get_shipping_address()) . ', '
              . $woocommerce->customer->get_shipping_postcode() . ' '
              . WC_function_Colissimo::cdi_sanitize_laposte_voie($woocommerce->customer->get_shipping_city()) . ', '
              . $woocommerce->customer->get_shipping_country() ;
       $address = str_replace ('  ', ' ', $addresscustomer) ;
       $address = str_replace ('  ', ' ', $address) ;
       $address = str_replace (' ', '+', $address) ;
       $key = get_option('wc_settings_tab_colissimo_googlemapsapikey') ;
       if ($key == null or $key == '') { // Google maps API depending if key exists
         $result = WC_function_Colissimo::cdi_url_get_contents('https://maps.googleapis.com/maps/api/geocode/xml?address=' .  $address) ;
       }else{
         $result = WC_function_Colissimo::cdi_url_get_contents('https://maps.googleapis.com/maps/api/geocode/xml?address=' .  $address . '&key=' . $key) ;
       }
       $status = WC_colissimo_choix_livraison::get_string_between($result, '<status>', '</status>') ;
       if ($status !== 'OK') {
         wc_add_notice( __( 'Google Maps can not geolocate this address. Please try again.', 'colissimo-delivery-integration' ) . ' (' . $status . ')', $notice_type = 'error' );
         return '' ;
       }
       // Extract lat and lon
       $latlng = WC_colissimo_choix_livraison::get_string_between($result, '<location>', '</location>') ;
       $lat = WC_colissimo_choix_livraison::get_string_between($latlng, '<lat>', '</lat>') ;
       $lon = WC_colissimo_choix_livraison::get_string_between($latlng, '<lng>', '</lng>') ;
       // Add marker for customer location
       $listmarks[] = array (
               'lati' => $lat ,
               'long' => $lon ,
               'desc' => apply_filters( 'cdi_filterhtml_retrait_desccustomer', $addresscustomer, $woocommerce->customer) ,  // Last argument customer is now an objet
               'icon' => $urliconcustomer
               ) ; 
       $paramgooglemapcss = apply_filters( 'cdi_filterarray_retrait_mapparam', array('z'=>"13", 'w'=>"100%", 'h'=>"400px", 'maptype' => 'ROADMAP', 'styles' => '[]', 'style' => 'border:1px solid gray; margin: 0 auto;') );
       $paramgooglemap = array_merge( array( 'id'=>"googlemapsopen", 'lat'=> $lat, 'lon'=>$lon ), $paramgooglemapcss );
       if (is_numeric($paramgooglemap['w'])) {
         $paramgooglemap['w'] = $paramgooglemap['w'] . 'px' ;
       }
       if (is_numeric($paramgooglemap['h'])) {
         $paramgooglemap['h'] = $paramgooglemap['h'] . 'px' ;
       }
       $jsmap = '';
       $jsmap .= ' <div id="' . $paramgooglemap['id'] . '" style="width:' . $paramgooglemap['w'] . ';height:' . $paramgooglemap['h'] . ';' . $paramgooglemap['style'] . ' "></div><br /> ' ;
       $jsmap .= ' <script type="text/javascript"> ' ;
       $jsmap .= ' var infowindow = null; var latlng = new google.maps.LatLng(' . $paramgooglemap['lat'] . ', ' . $paramgooglemap['lon'] . '); var myOptions = {zoom: ' . $paramgooglemap['z'] . ', center: latlng, mapTypeId: google.maps.MapTypeId.' . $paramgooglemap['maptype'] . ', styles: ' . $paramgooglemap['styles'] . ' }; var ' . $paramgooglemap['id'] . ' = new google.maps.Map(document.getElementById("' . $paramgooglemap['id'] . '"), myOptions); ';
       $jsmap .= ' var sites = [';
       foreach ($listmarks as $mark) {
         $jsmap .= '[' . $mark['lati'] . ',' . $mark['long'] . ',\'' . $mark['desc'] . '\',\'' . $mark['icon'] .  '\'],';
       }
       $jsmap = substr($jsmap, 0, strlen($jsmap) - 1);
       $jsmap.= '];';
       $jsmap.= ' ';
       $jsmap.= ' for (var i = 0; i < sites.length; i++) {';
       $jsmap.= ' var site = sites[i]; ';
       $jsmap.= ' var siteLatLng = new google.maps.LatLng(site[0], site[1]); ';
       $jsmap.= ' if(site[3]!=null) { ';
       $jsmap.= ' var markerimage  = site[3]; ';
       $jsmap.= ' var marker = new google.maps.Marker({ ';
       $jsmap.= ' position: siteLatLng, ';
       $jsmap.= ' map: ' . $paramgooglemap['id'] . ', ';
       $jsmap.= ' icon: markerimage, ';
       $jsmap.= ' html: site[2] }); ';
       $jsmap.= ' } else { ';
       $jsmap.= ' var marker = new google.maps.Marker({ ';
       $jsmap.= ' position: siteLatLng, ';
       $jsmap.= ' map: ' . $paramgooglemap['id'] . ', ';
       $jsmap.= ' html: site[2] }); ';
       $jsmap.= ' } ';
       $jsmap.= ' var contentString = "Some content";';
       $jsmap.= 'google.maps.event.addListener(marker, "click", function () { ';
       $jsmap.= 'infowindow.setContent(this.html); ';
       $jsmap.= ' infowindow.open(' . $paramgooglemap['id'] . ', this); ';
       $jsmap.= '}); ';
       $jsmap.= '} ';
       $jsmap.= ' infowindow = new google.maps.InfoWindow({ content: "loading..." }); ';
       $jsmap.= '</script>';
     }
     return $jsmap ;
   }

   public static function cdi_callback_show_pickupgooglemaps() { // callback for show the pickup locations on google map
     global $woocommerce;
     echo (WC_colissimo_choix_livraison::cdi_calculate_js_googlemaps()) ;
     wp_die();
   }

   public static function cdi_callback_set_pickuplocation() { // callback for storage of pickupselect and display of full info
     global $woocommerce;
     if ($_SERVER['REQUEST_METHOD'] == "POST" and ISSET($_POST['postpickupselect'])) {
       $pickupchosen = $_POST['postpickupselect'];
       WC()->session->set( 'cdi_pickuplocationid' , $pickupchosen);
       // *********
       $cdi_return_ws_liste_points_livraison = WC()->session->get('cdi_return_ws_liste_points_livraison') ;
       $pickupdetail = '' ;
       $eol = "\x0a" ;
       if($cdi_return_ws_liste_points_livraison) {
         $listePointRetraitAcheminement = $cdi_return_ws_liste_points_livraison->return->listePointRetraitAcheminement;
         $nbpointretrait = 0;
         foreach ($listePointRetraitAcheminement as $PointRetrait) {
           if ($PointRetrait->reseau !== 'X00' && $nbpointretrait < 30 && $PointRetrait->identifiant == $pickupchosen) { 
             $nbpointretrait = $nbpointretrait + 1;
             WC()->session->set( 'cdi_pickuplocationlabel' , 
                               WC_function_Colissimo::cdi_sanitize_laposte_voie($PointRetrait->nom) . ' => ' .
                               $PointRetrait->adresse1 . ' ' .
                               $PointRetrait->adresse2 . ' ' .
                               $PointRetrait->codePostal . ' ' .
                               $PointRetrait->localite . ' => Distance: ' .
                               $PointRetrait->distanceEnMetre . 'm => Id: ' .
                               $PointRetrait->identifiant) ;

             $pickupdetail .= 'Id: ' . $PointRetrait->identifiant . $eol ;
             $pickupdetail .= 'Distance: ' . $PointRetrait->distanceEnMetre . 'm' . $eol ;
             $pickupdetail .= $eol ;
             $pickupdetail .= WC_function_Colissimo::cdi_sanitize_laposte_voie($PointRetrait->nom) . $eol ;
             $pickupdetail .= $PointRetrait->adresse1 . $eol ;
             if ($PointRetrait->adresse2) $pickupdetail .= $PointRetrait->adresse2 . $eol ;
             if ($PointRetrait->adresse3) $pickupdetail .= $PointRetrait->adresse3 . $eol ;
             $pickupdetail .= $PointRetrait->codePostal . ' ' . $PointRetrait->localite .  $eol ;
             $pickupdetail .= $PointRetrait->libellePays . $eol ;

             if ($PointRetrait->indiceDeLocalisation) $pickupdetail .= $eol . $PointRetrait->indiceDeLocalisation . $eol ;
             $pickupdetail .= $eol ;

             $pickupdetail .= '    Lundi    ' . $PointRetrait->horairesOuvertureLundi . $eol ;
             $pickupdetail .= '    Mardi    ' . $PointRetrait->horairesOuvertureMardi . $eol ;
             $pickupdetail .= '    Mercredi ' . $PointRetrait->horairesOuvertureMercredi . $eol ;
             $pickupdetail .= '    Jeudi    ' . $PointRetrait->horairesOuvertureJeudi . $eol ;
             $pickupdetail .= '    Vendredi ' . $PointRetrait->horairesOuvertureVendredi . $eol ;
             $pickupdetail .= '    Samedi   ' . $PointRetrait->horairesOuvertureSamedi . $eol ;
             $pickupdetail .= '    Dimanche ' . $PointRetrait->horairesOuvertureDimanche . $eol ;
             $pickupdetail .= $eol ;

             $pickupdetail .= 'GPS: ' . $PointRetrait->coordGeolocalisationLatitude . ' ' . $PointRetrait->coordGeolocalisationLongitude .  $eol ;
             if ($PointRetrait->parking) $pickupdetail .= 'Parking: ' . $PointRetrait->parking . $eol ;
             if ($PointRetrait->accesPersonneMobiliteReduite) $pickupdetail .= 'Mobilité réduite: ' . $PointRetrait->accesPersonneMobiliteReduite . $eol ;
             if ($PointRetrait->langue) $pickupdetail .= 'Langue: ' . $PointRetrait->langue . $eol ;
             if ($PointRetrait->poidsMaxi) $pickupdetail .= 'Poids maxi: ' . $PointRetrait->poidsMaxi . $eol ;
             if ($PointRetrait->loanOfHandlingTool) $pickupdetail .= 'Equipements de manipulation: ' . $PointRetrait->loanOfHandlingTool . $eol ;
             if ('yes' == get_option('wc_settings_tab_colissimo_selectclickonmap') && WC_function_Colissimo::cdi_isconnected()) {
               $pickupdetail = '' ;
             }
             $pickupdetail = apply_filters( 'cdi_filterhtml_retrait_displayselected', $pickupdetail, $PointRetrait) ;
             break ;
           }
         }
       }
       // *********
       $response = array() ;
       $response[] = $pickupdetail ; // Pickup details in array 0
       $response[] = WC_colissimo_choix_livraison::cdi_calculate_js_googlemaps() ; // Refresh js google maps scrip in array 1
       echo json_encode($response); 
       wp_die();
     }
   }

   public static function cdi_check_exist_phonenumber() { // Check if phone must exist
     $chosen_shipping = WC()->session->get( 'cdi_refshippingmethod') ;
     $arraychosen = explode(':', $chosen_shipping); // explode = method : instance : suffixe
     $phonemandatory = get_option('wc_settings_tab_colissimo_phonemandatory') ;
     if ($phonemandatory === false) { // option not exist
       $phonemandatory = 'colissimo_shippingzone_method_pick1, colissimo_shippingzone_method_pick2, colissimo_shippingzone_method_pick3, colissimo_shippingzone_method_pick4, colissimo_shippingzone_method_pick5' ;
       update_option('wc_settings_tab_colissimo_phonemandatory', $phonemandatory);
     }
     $arrayphonemandatory = explode(',', $phonemandatory) ;
     $arrayphonemandatory = array_map("trim", $arrayphonemandatory);
     $billing_phone = '' ;
     if ($_POST['billing_phone']) {
       $billing_phone =  $_POST['billing_phone'] ;
     }
     foreach ($arrayphonemandatory as $relation) {
       if (isset($arraychosen[1])){ // test case for legacy shipping method non WC 2.6
         $arraychosenun = $arraychosen[0] . ':' . $arraychosen[1] ;
       }else{
         $arraychosenun = $arraychosen[0] ;
       }
       if ($relation && (($relation == '*') OR ($relation == $arraychosen[0]) OR ($relation == $arraychosenun))) {
         if ($billing_phone == null or $billing_phone == '') {
           throw new Exception( __( 'You must fill your billing phone number.' . $billing_phone, 'colissimo-delivery-integration' ) );
         }
       }
     }
   }

   public static function cdi_check_pickup_isset() { // Check if pickup location is set
     global $woocommerce;
     $cdi_return_ws_liste_points_livraison = WC()->session->get( 'cdi_return_ws_liste_points_livraison') ;
     if ($cdi_return_ws_liste_points_livraison) {
       $cdipickuplocationid = WC()->session->get( 'cdi_pickuplocationid') ;
       if (!$cdipickuplocationid) {
         throw new Exception( __( 'You must select a pickup location. Please try again.', 'colissimo-delivery-integration' ) );
       }else{
         // check that pickup code product has not changed
         $listePointRetraitAcheminement = $cdi_return_ws_liste_points_livraison->return->listePointRetraitAcheminement;
         $codeproductfound = '' ;
         foreach ($listePointRetraitAcheminement as $PointRetrait) {
           if ($cdipickuplocationid ==  $PointRetrait->identifiant &&  $PointRetrait->typeDePoint ) {
             $codeproductfound =  $PointRetrait->typeDePoint ;
             break;
           }
         }
       }
       if (empty($codeproductfound)) { // error to catch
         WC_function_Colissimo::cdi_debug(__LINE__ ,__FILE__ , $codeproductfound, 'tec');
         WC_function_Colissimo::cdi_debug(__LINE__ ,__FILE__ , $cdipickuplocationid, 'tec');
         WC_function_Colissimo::cdi_debug(__LINE__ ,__FILE__ , $listePointRetraitAcheminement, 'tec');
         $msg = __( 'Pickup location - Technical error on product code. Please try again.', 'colissimo-delivery-integration' ) ;
         WC_function_Colissimo::cdi_debug(__LINE__ ,__FILE__ , $msg, 'tec');
         throw new Exception( $msg );
       }
       WC()->session->set('cdi_forcedproductcode', $codeproductfound) ;  
       // Check if WC bug : WC Address has been erased here in some cases     
       if (!$woocommerce->customer->get_shipping_address() or !$woocommerce->customer->get_shipping_postcode() or !$woocommerce->customer->get_shipping_city() or !$woocommerce->customer->get_shipping_country()) { //Adr inexistante in WC. Seems tobe a WC bug that may happens
         $msg = __( 'Pickup location - Technical error : WC adress not present. Please try again.', 'colissimo-delivery-integration' ) ;
         WC_function_Colissimo::cdi_debug(__LINE__ ,__FILE__ , $msg, 'tec');
         throw new Exception( $msg );
       }
     }
   }

   public static function cdi_check_pickup_product_and_location() { // Check pickup product code but no pickup location
     $codeproductfound = WC()->session->get( 'cdi_forcedproductcode') ;
     $cdipickuplocationid = WC()->session->get( 'cdi_pickuplocationid') ;
     if (in_array($codeproductfound, array('BPR', 'ACP', 'CDI', 'BDP', 'A2P', 'CMT', 'PCS')) and empty($cdipickuplocationid)) { // error to catch
       WC_function_Colissimo::cdi_debug(__LINE__ ,__FILE__ , $codeproductfound, 'tec');
       throw new Exception( __( 'Pickup location - Technical error on pickup product code vs location id. Please try again.', 'colissimo-delivery-integration' ));
     }
   }

   public static function cdi_check_pickup_method_and_nolocation() { // Check if pickup method but no no pickup location
     $chosen_shipping = WC()->session->get( 'cdi_refshippingmethod');
     $cdipickuplocationid = WC()->session->get( 'cdi_pickuplocationid') ;
     $testpickup = self::cdi_control_pickup_list($chosen_shipping) ;
     if ($testpickup['0'] == 1 and null == $cdipickuplocationid) { // Technical error after this sequence : data missing,checkout,refresh,checkout 
       WC_function_Colissimo::cdi_debug(__LINE__ ,__FILE__ , $chosen_shipping, 'msg');
       throw new Exception( __( 'Pickup location - Technical error on pickup method. Please try again.', 'colissimo-delivery-integration' ));
     }
   }

   public static function cdi_woocommerce_checkout_posted_data($data) { // Action when checkout button is pressed
     global $woocommerce; 
     // set again shipping and products as seen by WC before the order is created
     self::cdi_get_shipping_and_product() ;
     $chosen_shipping = WC()->session->get( 'cdi_refshippingmethod');
     $chosen_products = WC()->session->get( 'cdi_chosen_products');
     $shipping_method_name = WC()->session->get( 'cdi_shipping_method_name');

     $cdipickuplocationid = WC()->session->get( 'cdi_pickuplocationid') ;
     $cdipickuplocationlabel = WC()->session->get( 'cdi_pickuplocationlabel') ;
     $codeproductfound = WC()->session->get( 'cdi_forcedproductcode') ;

     $cdi_return_ws_liste_points_livraison = WC()->session->get( 'cdi_return_ws_liste_points_livraison') ;

     // Mandatory phone number check here
     self::cdi_check_exist_phonenumber() ;

     // Check if pickup location is set
     self::cdi_check_pickup_isset() ;
     $codeproductfound = WC()->session->get( 'cdi_forcedproductcode') ;

     $debug = '*** At ckeckout, data passed to WC before order : ' . 'Product: ' . $codeproductfound . ' Location: ' . $cdipickuplocationid . ' Label: ' . $cdipickuplocationlabel . ' Method: ' . $chosen_shipping . ' ***' ;
     WC_function_Colissimo::cdi_debug(__LINE__ ,__FILE__ , $debug, 'msg');

     // Check if pickup product code but no pickup location
     self::cdi_check_pickup_product_and_location() ;

     // Check if pickup method but no no pickup location
     self::cdi_check_pickup_method_and_nolocation() ;     
     
     // Now pass cdi datas with WC custom fields
     $data[ 'cdi_refshippingmethod' ] =  $chosen_shipping ; 
     $data[ 'cdi_chosen_products' ] =  $chosen_products ;      
     $data[ 'cdi_shipping_method_name' ] =  $shipping_method_name ;      
     $data[ 'cdi_pickuplocationid' ] =  $cdipickuplocationid ; 
     $data[ 'cdi_pickuplocationlabel' ] =  $cdipickuplocationlabel ;      
     $data[ 'cdi_forcedproductcode' ] =  $codeproductfound ;      
     return $data ;
   }
   
   public static function cdi_woocommerce_checkout_update_order_meta($order_id, $data) { 
     global $woocommerce; 
     // Here the order exist. So we can store data in meta
     update_post_meta($order_id, '_cdi_meta_productCode', $data['cdi_forcedproductcode']);
     update_post_meta($order_id, '_cdi_meta_pickupLocationId', $data['cdi_pickuplocationid']);
     update_post_meta($order_id, '_cdi_meta_pickupLocationlabel', $data['cdi_pickuplocationlabel']);
     update_post_meta($order_id, '_cdi_refshippingmethod', $data['cdi_refshippingmethod']); 
     update_post_meta($order_id, '_cdi_chosen_products', $data[ 'cdi_chosen_products']); 
     update_post_meta($order_id, '_cdi_meta_shippingmethod_name', $data['cdi_shipping_method_name']); 
     $debug = '*** At ckeckout, data passed to WC after order : ' . 'Product: ' . $data['cdi_forcedproductcode'] . ' Location: ' . $data['cdi_pickuplocationid'] . ' Label: ' . $data['cdi_pickuplocationlabel'] . ' Method: ' . $data['cdi_refshippingmethod'] . ' ***' ;
     WC_function_Colissimo::cdi_debug(__LINE__ ,__FILE__ , $debug, 'msg');
   }

  public static function cdi_wp_footer_googlemaps_refreshiddentheme() {
    if (is_checkout()) { // No useful to do this if not the checkout page
      ?><!-- CDI version : <?php echo get_option('cdi_options_version'); ?> --><?php
      if (get_option('wc_settings_tab_colissimo_maprefresh') == 'yes') {
        $ajaxurl = admin_url('admin-ajax.php');
        ?><script>
          Element.prototype.isVisible=function(){"use strict";function e(f,i,n,r,d,l,s){var u=f.parentNode;return!!o(f)&&(9===u.nodeType||"0"!==t(f,"opacity")&&"none"!==t(f,"display")&&"hidden"!==t(f,"visibility")&&(void 0!==i&&void 0!==n&&void 0!==r&&void 0!==d&&void 0!==l&&void 0!==s||(i=f.offsetTop,d=f.offsetLeft,r=i+f.offsetHeight,n=d+f.offsetWidth,l=f.offsetWidth,s=f.offsetHeight),!u||("hidden"!==t(u,"overflow")&&"scroll"!==t(u,"overflow")||!(d+2>u.offsetWidth+u.scrollLeft||d+l-2<u.scrollLeft||i+2>u.offsetHeight+u.scrollTop||i+s-2<u.scrollTop))&&(f.offsetParent===u&&(d+=u.offsetLeft,i+=u.offsetTop),e(u,i,n,r,d,l,s))))}function t(e,t){return window.getComputedStyle?document.defaultView.getComputedStyle(e,null)[t]:e.currentStyle?e.currentStyle[t]:void 0}function o(e){for(;e=e.parentNode;)if(e==document)return!0;return!1}return e(this)};
          var refreshdone = 0 ;
          jQuery(document).ready(function(){ // call ajax for pickup google maps
            jQuery(".woocommerce-checkout").click(function(refreshmapifdivhidden){ 
              setTimeout(function() {
                var elmtotest = document.getElementById('googlemapsopen');
                if (!refreshdone && elmtotest && elmtotest.isVisible(elmtotest)) {
                  refreshdone = 1 ;
                  var data = { 'action': 'set_pickupgooglemaps', 'pickupgooglemaps': 'pickupgooglemaps' };
                  var ajaxurl = '<?php echo $ajaxurl; ?>';
                  jQuery.post(ajaxurl, data, function(response) {
                    jQuery("#popupmap").html(response) ;
                  });
                }
              }, 1500);
            });
          });
        </script><?php
      }
      if ('yes' == get_option('wc_settings_tab_colissimo_selectclickonmap') && WC_function_Colissimo::cdi_isconnected()) {
        ?><script type="text/javascript"> 
        jQuery(document).on("click", "a.selretrait.button", function(detailselret){
          document.getElementById("selretraithidden").style.display = "inline";
          document.getElementById("selretraitshown").style.display = "none";
          var selretrait_id = document.getElementById('selretrait');
          var idret = selretrait_id.className;
          idret = idret.substring(13); // sup begin of class name "cdiselretrait"
          var options = document.querySelector("#pickupselect").options; 
          for (var i = 0; i < options.length; i++) { 
            if (options[i].value == idret) {
              var pickupselectvalue =  options[i].value;
              var pickupselecttext =  options[i].text;
              options[i].selected = true;
              var sel = document.getElementById('pickupselect');
              fireEvent(sel,'change'); 
              break;
            }
          }
          function fireEvent(element,event){
            if (document.createEventObject){ 
              var evt = document.createEventObject();
              return element.fireEvent('on'+event,evt)
            }else{ 
              var evt = document.createEvent("HTMLEvents");
              evt.initEvent(event, true, true ); 
              return !element.dispatchEvent(evt);
            }
          }
          //suppopover();
        }); 
        </script><?php
        ?> <style>
          .ol-attribution {font-size: xx-small;} 
          /* #zoneiconmap {display: none;} */
          /* #pickupselect {display: none;} */
        </style><?php
      }
    }
  }

  public static function cdi_ex_filterbool_tobeornottobe_shipping_rate($eligible, $rateid) { // Must we show pickup shipping tariff ?
    $new_eligible = $eligible ;
    if ($new_eligible === true and get_option('wc_settings_tab_colissimo_pickupoffline') !== 'no') {
      $array_return = self::cdi_control_pickup_list($rateid) ;
      if ($array_return[0] == '1') {
        if (!WC_function_Colissimo::cdi_isconnected()) { // Test if outgoing IP and CDI are OK
          return false ;
        }
        if (self::cdi_test_laposte_status() === false) { // Test if LaPoste is in line
          return false;
        }
      }
    }
    return $new_eligible ;
  }
}

// Adapt format of Soap request (for ns1) and format of Soap response (for Mtom/xop)
class ColissimoPRSoapClient extends SoapClient {
  function __doRequest($request, $location, $action, $version, $one_way = NULL) {
    if (strpos($location, "ws.colissimo.fr/pointretrait-ws-cxf/PointRetraitServiceWS/2.0") == false) {
      return $response = parent::__doRequest($request, $location, $action, $version, $one_way);
    }else{
      // correct text generated by soap
      //$request = str_replace( '<ns1:findRDVPointRetraitAcheminement><accountNumber xsi:type="ns1:findRDVPointRetraitAcheminement">', '<ns1:findRDVPointRetraitAcheminement>', $request ); // WS generated @date 2016-05-24
      //$request = str_replace( '</accountNumber></ns1:findRDVPointRetraitAcheminement>', '</ns1:findRDVPointRetraitAcheminement>', $request ); // WS generated @date 2016-05-24
      $request = str_replace( '<ns1:findRDVPointRetraitAcheminement><accountNumber xsi:type="ns1:findRDVPointRetraitAcheminement">', '<ns1:findRDVPointRetraitAcheminement>', $request ); // WS generated @date 2018-10-26
      $request = str_replace( '</accountNumber><apikey xsi:nil="true"/><codTiersPourPartenaire xsi:nil="true"/></ns1:findRDVPointRetraitAcheminement>', '</ns1:findRDVPointRetraitAcheminement>', $request ); // WS generated @date 2018-10-26
      $response = parent::__doRequest($request, $location, $action, $version, $one_way);
      $this->__last_request = $request;
      // if response content type is Mtom, strip away everything but the xml
      if (strpos($response, "Content-Type: application/xop+xml") !== false) {
        // Keep only soap Envelope
        $tempstr = stristr($response, "<soap:Envelope");
        $response = substr($tempstr, 0, strpos($tempstr, "/soap:Envelope>")) . "/soap:Envelope>";
      }
      $response = str_replace(array("\r\n","\r","\n"),"",$response);
      $response = str_replace("  "," ",$response);
      return $response;
    }
  }
}


?>
