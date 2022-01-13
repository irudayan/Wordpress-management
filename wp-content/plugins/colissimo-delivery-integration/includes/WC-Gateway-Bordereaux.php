<?PHP

/**
 * This file is part of the Colissimo Delivery Integration plugin.
 * (c) Halyra
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

if (!defined('ABSPATH')) exit;
/****************************************************************************************/
/* Add Bordereaux Colissimo actions in the Gateway                                      */
/****************************************************************************************/
class WC_Gateway_bordereaux {
    public static function init() {
      add_action('wp_ajax_cdi_bremise_open_view',  __CLASS__ . '::cdi_ajax_bremise_open_view');
      add_action('wp_ajax_cdi_bremise_close_view',  __CLASS__ . '::cdi_ajax_bremise_close_view');
      add_action('wp_ajax_cdi_bremise_add_select',  __CLASS__ . '::cdi_ajax_bremise_add_select');
      add_action('wp_ajax_cdi_bremise_clear_select',  __CLASS__ . '::cdi_ajax_bremise_clear_select');
      add_action('wp_ajax_cdi_bremise_exec_bordereau',  __CLASS__ . '::cdi_ajax_bremise_exec_bordereau');
      add_action('wp_ajax_cdi_btransport_exec_bordereau',  __CLASS__ . '::cdi_ajax_btransport_exec_bordereau');
      add_action('wp_ajax_cdi_bpreparation_exec_bordereau',  __CLASS__ . '::cdi_ajax_bpreparation_exec_bordereau');
      add_action('wp_ajax_cdi_blivraison_exec_bordereau',  __CLASS__ . '::cdi_ajax_blivraison_exec_bordereau');
      add_action('admin_init',  __CLASS__ . '::cdi_bordereau_voir');
      if (class_exists ('SoapClient')) {
        require_once dirname(__FILE__) . '/ColissimoAF/ColissimoAFAutoload.php';
      }
      if (!class_exists('FPDF')) {
        require_once dirname(__FILE__) . '/../includes/FPDF/fpdf.php';
      }
      if (!class_exists('FPDI')) {
        require_once dirname(__FILE__) . '/../includes/FPDI/fpdi.php';
      }
    }

    /**
     * Manage the Bordereau Remise function.
     *
     */
    public static function bremise_manage() {
      if (WC_function_Colissimo::cdi_isconnected()) {
        eval (WC_function_Colissimo::cdi_eval('7')) ; 
        if (!get_option('cdi_br_select_fromdate') or !get_option('cdi_br_select_todate')) {
         update_option('cdi_br_select_todate', date("Y-m-d")) ;
         update_option('cdi_br_select_fromdate', date('Y-m-d', strtotime("-1 days"))) ;
        }
        if (!get_option('cdi_br_select_numorders')) {
         update_option('cdi_br_select_numorders', "") ;
        }
        if (!get_option('cdi_br_select_codesuivi')) {
         update_option('cdi_br_select_codesuivi', "") ;
        }
        if (!get_option('cdi_br_select_extern')) {
         update_option('cdi_br_select_extern', "") ;
        }
        if (!get_option('cdi_br_select_annulcode')) {
         update_option('cdi_br_select_annulcode', "") ;
        }
        ?>
        <div id="bremisemanagewrap">
          <div id="bremisemanage" style="display:none;">
            <h2>CDI Gestion des documents logistiques (Beta)</h2>
            <p>
              <input type="button" id="cdi-bremise-close" class="button button-primary mode-run-bremise" value="Fermer" />
              <input type="button" id="cdi-bremise-select" class="button button-primary" value="Ajouter sélection(s)" />
              <input type="button" id="cdi-bremise-clear" class="button button-primary" value="Purger sélectés" />
              <input type="button" id="cdi-bremise-exec" class="button button-primary" style="background-color:green;" value="Bordereau de dépôt" Title="Générer un Bordereau de dépôt à La Poste. Ce document est à remettre lors du dépôt des colis à La Poste. Il peut être facultatif selon les modalités de dépôt convenues dans le contrat avec La Poste"/>
              <input type="button" id="cdi-btransport-exec" class="button button-primary" style="background-color:green;" value="Bon de transport" Title="Générer un Bon de transport. Ce document est destiné au service logistique, à un prestataire, ou à un transporteur, qui ont à effectuer l'acheminement des colis. Il contient rassemblés en une feuille uniquement les caractéristiques externes de la sélection de colis, sans détailler le contenu de chacun des colis"/>
              <input type="button" id="cdi-bpreparation-exec" class="button button-primary" style="background-color:green;" value="Listes de préparation" Title="Générer les Listes de préparation. Ces documents sont destinés au service logistique ou un prestataire, qui notamment ont à effectuer la préparation des colis. Elles sont constituées de feuilles indépendantes par colis donnant le détail des articles devant être dans chacun des colis."/>
              <input type="button" id="cdi-blivraison-exec" class="button button-primary" style="background-color:green;" value="Etat des livraisons" Title="Document donnant globalement l'état des livraisons Colissimo pour les colis sélectionnés."/>
            </p>
            <div id="cdi-bremise-message-zone"></div>
            <div style="width:98%; height:calc(100vh - 50px);display:inline-block;">
              <div id="cdi-bremise-selectors" style="font-size:14px; width:25%; height:100%; display:inline-block; float:left; margin:10px; padding:10px; background-color:white;">
                <h2 style="text-align: center;">Sélecteurs</h2>
                <div ><strong>Sélection des Commandes/Colis : </strong></div>
                  <div>
                    <p>
                      <input type="checkbox" id="br_select_gateway" name="br_select_gateway" unchecked style="font-size:14px; margin:10px; padding:10px;">
                      <label for="br_select_gateway"> <strong>+</strong> Colis dans la passerelle</label>
                    </p>
                    <p>                  
                      <input type="checkbox" id="br_select_orders" name="br_select_orders" unchecked style="font-size:14px; margin:10px; padding:10px;">
                      <label for="br_select_orders"> <strong>+</strong> Colis des commandes de date à date (inclues) : </label>
                    </p>
                    <p>
                      <label for="fromdate" style="margin-left:15%;">du </label>
                      <input type="text" class="custom_date" id="cdi_br_select_fromdate" name="cdi_br_select_fromdate" style="width:25%" value="<?php echo get_option('cdi_br_select_fromdate'); ?>"/>
                      <lablel for="todate">au </label>
                      <input type="text" class="custom_date" id="cdi_br_select_todate" name="cdi_br_select_todate" style="width:25%" value="<?php echo get_option('cdi_br_select_todate'); ?>"/>
                    </p>
                    <p>                  
                      <input type="checkbox" id="br_select_numorders" name="br_select_numorders" unchecked style="font-size:14px; margin:10px; padding:10px;">
                      <label for="br_select_numorders"> <strong>+</strong> Liste Id de commandes/colis (virgules de séparation) : </label>
                      <textarea id="br_select_numorders_param1" style="font-size:14px; margin-left:15%; width: 80%; height:70px;"><?php echo get_option('cdi_br_select_numorders'); ?></textarea>
                    </p>
                    <p>                  
                      <input type="checkbox" id="br_select_codesuivi" name="br_select_codesuivi" unchecked style="font-size:14px; margin:10px; padding:10px;">
                      <label for="br_select_codesuivi"> <strong>+</strong> liste de codes suivi (virgules de séparation) : </label>
                      <textarea id="br_select_codesuivi_param1" style="font-size:14px; margin-left:15%; width: 80%; height:70px;"><?php echo get_option('cdi_br_select_codesuivi'); ?></textarea>
                    </p>
                    <p>                  
                      <input type="checkbox" id="br_select_extern" name="br_select_extern" unchecked style="font-size:14px; margin:10px; padding:10px;">
                      <label for="br_select_extern"> <strong>+</strong> Externe - scan, appli, etc (option 'cdi_br_select_extern', syntaxe 'orderid,[codetracking],...') : </label>
                      <textarea id="br_select_extern_param1" style="font-size:14px; margin-left:15%; width: 80%; height:70px;"><?php echo get_option('cdi_br_select_extern'); ?></textarea>
                    </p>
                    <p>                  
                      <input type="checkbox" id="br_select_annulcode" name="br_select_annulcode" unchecked style="font-size:14px; margin:10px; padding:10px;">
                      <label for="br_select_annulcode"> <strong>-</strong> Annulation Commandes/Colis (virgules de séparation, syntaxe 'orderid,[codetracking],...') : </label>
                      <textarea id="br_select_annulcode_param1" style="font-size:14px; margin-left:15%; width: 80%; height:70px;"><?php echo get_option('cdi_br_select_annulcode'); ?></textarea>
                    </p>
                  </div>
              </div>
              <div id="cdi-bremise-selected" style="font-size:14px; width:25%; height:100%; display:inline-block; float:left; margin:10px; padding:10px; background-color:white;">
                <h2 style="text-align: center;">Sélectionnés</h2>
                <div id="cdi-bremise-listeselected" style="margin:5%; height:80%; overflow: scroll;"></div>
              </div>
              <div id="cdi-bremise-cree" style="font-size:14px; width:40%; height:100%; display:inline-block; float:left; margin:10px; padding:10px; background-color:white;">
                <h2 style="text-align: center;">Vos documents logistiques</h2>
                <div title= "BD = Bordereau de dépôt; BT = Bon de transport; LP = Listes de préparation; EL = Etat des livraisons."><strong>Historique des documents générés </strong>(BD = Bordereau de dépôt; BT = Bon de transport; LP = Listes de préparation; EL = Etat des livraisons.) :</div>
                  <div id="cdi-brbordereaux-generes" style="margin:5%; height:80%; overflow: scroll;">
                    <form method="post">
                      <div id="outer" style="position: relative;">
                        <div id="inner" style="overflow: auto; max-height:80%;">
                          <table cellspacing="0" class="wp-list-table widefat fixed">
                            <thead>
                              <tr>
                                <th class="manage-column" id="cdi-br-date" scope="col" style="width:30%;">Date création</th>
                                <th class="manage-column" id="cdi-br-number" scope="col" style="width:30%;">Type et référence</th>
                                <th class="manage-column" id="cdi-br-nbcol" scope="col" style="width:30%;">Commandes/Colis</th>
                                <th class="manage-column" id="cdi-br-voir" scope="col" style="width:10%;">Voir</th>
                              </tr>
                            </thead>
                            <tbody id="cdi-list-bordereau"></tbody>
                            <tfoot>
                              <tr>
                                <th class="manage-column" id="cdi-br-date" scope="col" style="width:30%;">Date création</th>
                                <th class="manage-column" id="cdi-br-number" scope="col" style="width:30%;">Type et référence</th>
                                <th class="manage-column" id="cdi-br-nbcol" scope="col" style="width:30%;">Commandes/Colis</th>
                                <th class="manage-column" id="cdi-br-voir" scope="col" style="width:10%;">Voir</th>
                              </tr>
                            </tfoot>
                          </table>
                        </div>
                      </div>
                    </form>
                  </div>
              </div>
            </div>
            <p> </p>
          <!-- End of div id="bremisemanage" -->
          </div>
        </div> 
        <?php
        eval (WC_function_Colissimo::cdi_eval('12')) ; 
      }
    }
    public static function cdi_bremise_open_button() {
      if (WC_function_Colissimo::cdi_isconnected()) {
        eval (WC_function_Colissimo::cdi_eval('7')) ; 
        $cdi_bremise_set = get_option('cdi_bremise_set') ;
        if ($cdi_bremise_set !== 'yes') {
          $color = '#0085ba' ; // Bordereau de dépôt is close
        }else{
          $color = 'red' ; // Bordereau de dépôt is running
        }
        ?><em></em><input type="button" id="cdi-bremise-open" class="mode-run-bremise" value="Documents logistiques" style="float: left; background-color: <?php echo $color; ?>; color:white;" title="Gestion des Documents logistiques. Pour ouvrir cliquez !" /><em></em><?php
        eval (WC_function_Colissimo::cdi_eval('19')) ; 
        ?><script>
          jQuery(document).ready(function($){ 
            var cdibremiselisteselected = $( '#cdi-bremise-listeselected' );
            var cdilistbordereau = $( '#cdi-list-bordereau' );
            var cdibremisemessagezone = $( '#cdi-bremise-message-zone' );
            var ajaxurl = "<?php echo $ajaxurl; ?>";
            var doopen          = $( '#cdi-bremise-open' );
            var doclose          = $( '#cdi-bremise-close' );
            var doselect          = $( '#cdi-bremise-select' );
            var doclear          = $( '#cdi-bremise-clear' );
            var doexec          = $( '#cdi-bremise-exec' );
            var traexec          = $( '#cdi-btransport-exec' );
            var colexec          = $( '#cdi-bpreparation-exec' );
            var livexec          = $( '#cdi-blivraison-exec' );
            function getcurrentselect() {
              var currentselect = {
                  br_select_gateway:$(document.getElementById("br_select_gateway")).is(':checked'), 
                  br_select_orders:$(document.getElementById("br_select_orders")).is(':checked'), 
                      cdi_br_select_fromdate:document.getElementById("cdi_br_select_fromdate").value,
                      cdi_br_select_todate:document.getElementById("cdi_br_select_todate").value, 
                  br_select_numorders:$(document.getElementById("br_select_numorders")).is(':checked'), 
                      br_select_numorders_param1:document.getElementById("br_select_numorders_param1").value,
                  br_select_codesuivi:$(document.getElementById("br_select_codesuivi")).is(':checked'), 
                      br_select_codesuivi_param1:document.getElementById("br_select_codesuivi_param1").value,
                  br_select_extern:$(document.getElementById("br_select_extern")).is(':checked'), 
                      br_select_extern_param1:document.getElementById("br_select_extern_param1").value,
                  br_select_annulcode:$(document.getElementById("br_select_annulcode")).is(':checked'), 
                      br_select_annulcode_param1:document.getElementById("br_select_annulcode_param1").value
                   };
              return currentselect ;
            }
            jQuery(doopen).click(function(){
              cdibremisemessagezone.html(""); 
              var doopenajax      = { 'action': 'cdi_bremise_open_view', 'select': getcurrentselect() };
              jQuery.post(ajaxurl, doopenajax, function(response) {
                arr = JSON.parse(response) ;
	        cdibremisemessagezone.html(arr['message']);
	        cdibremiselisteselected.html(arr['htmlselected']);
	        cdilistbordereau.html(arr['htmlbordereaux']);
              } );
              jQuery( "#bremisemanage" ).each(function( index ) { 
                jQuery(this).show();
	      });
              $(".mode-run-bremise").css("background-color", "red");
              jQuery('html, body').animate({ scrollTop: jQuery("#bremisemanagewrap").offset().top }, 1000);
            } );
            jQuery(doclose).click(function(){
              cdibremisemessagezone.html(""); 
              var docloseajax      = { 'action': 'cdi_bremise_close_view' };
              jQuery.post(ajaxurl, docloseajax, function(response) {
              } );
              jQuery( "#bremisemanage" ).each(function( index ) { 
                jQuery(this).hide();
	      });
              document.getElementById("br_select_numorders_param1").value = '';
              document.getElementById("br_select_codesuivi_param1").value = '';
              document.getElementById("br_select_extern_param1").value = '';
              document.getElementById("br_select_annulcode_param1").value = '';
              document.getElementById("cdi_br_select_fromdate").value = '';
              document.getElementById("cdi_br_select_todate").value = '';
              $(".mode-run-bremise").css("background-color", "#0085ba");
              jQuery('html, body').animate({ scrollTop: jQuery("#wpbody").position().top }, 1000);
            } );
            jQuery(doselect).click(function(){
              cdibremisemessagezone.html(""); 
              var doselectajax      = { 'action': 'cdi_bremise_add_select', 'select': getcurrentselect() };
              jQuery.post(ajaxurl, doselectajax, function(response) {
                arr = JSON.parse(response) ;
	        cdibremisemessagezone.html(arr['message']);
	        cdibremiselisteselected.html(arr['htmlselected']);
              } );
              document.getElementById("br_select_gateway").checked = false;
              document.getElementById("br_select_orders").checked = false;
              document.getElementById("br_select_numorders").checked = false;
              document.getElementById("br_select_codesuivi").checked = false;
              document.getElementById("br_select_extern").checked = false;
              document.getElementById("br_select_annulcode").checked = false;
            } );
            jQuery(doclear).click(function(){
              cdibremisemessagezone.html(""); 
              var doclearajax      = { 'action': 'cdi_bremise_clear_select'};
              jQuery.post(ajaxurl, doclearajax, function(response) {
                arr = JSON.parse(response) ;
	        cdibremisemessagezone.html(arr['message']);
	        cdibremiselisteselected.html(arr['htmlselected']);
              } );
              document.getElementById("br_select_gateway").checked = false;
              document.getElementById("br_select_orders").checked = false;
              document.getElementById("br_select_numorders").checked = false;
              document.getElementById("br_select_codesuivi").checked = false;
              document.getElementById("br_select_extern").checked = false;
              document.getElementById("br_select_annulcode").checked = false;
            } );
            jQuery(doexec).click(function(){
              cdibremisemessagezone.html(""); 
              var doexecajax      = { 'action': 'cdi_bremise_exec_bordereau'};
              jQuery.post(ajaxurl, doexecajax, function(response) {
                arr = JSON.parse(response) ;
	        cdibremisemessagezone.html(arr['message']);
	        cdibremiselisteselected.html(arr['htmlselected']);
	        cdilistbordereau.html(arr['htmlbordereaux']);
              } );
            } );
            jQuery(traexec).click(function(){
              cdibremisemessagezone.html(""); 
              var doexecajax      = { 'action': 'cdi_btransport_exec_bordereau'};
              jQuery.post(ajaxurl, doexecajax, function(response) {
                arr = JSON.parse(response) ;
	        cdibremisemessagezone.html(arr['message']);
	        cdibremiselisteselected.html(arr['htmlselected']);
	        cdilistbordereau.html(arr['htmlbordereaux']);
              } );
            } );
            jQuery(colexec).click(function(){
              cdibremisemessagezone.html(""); 
              var doexecajax      = { 'action': 'cdi_bpreparation_exec_bordereau'};
              jQuery.post(ajaxurl, doexecajax, function(response) {
                arr = JSON.parse(response) ;
	        cdibremisemessagezone.html(arr['message']);
	        cdibremiselisteselected.html(arr['htmlselected']);
	        cdilistbordereau.html(arr['htmlbordereaux']);
              } );
            } );
            jQuery(livexec).click(function(){
              cdibremisemessagezone.html(""); 
              var doexecajax      = { 'action': 'cdi_blivraison_exec_bordereau'};
              jQuery.post(ajaxurl, doexecajax, function(response) {
                arr = JSON.parse(response) ;
	        cdibremisemessagezone.html(arr['message']);
	        cdibremiselisteselected.html(arr['htmlselected']);
	        cdilistbordereau.html(arr['htmlbordereaux']);
              } );
            } );
          });
        </script><?php
        eval (WC_function_Colissimo::cdi_eval('12')) ; 
      }
    }
    public static function cdi_get_br_selected() {
      $selected = get_option('cdi_selected_bremise') ;
      if (!$selected) {
        $selected = array() ;
        update_option('cdi_selected_bremise', $selected) ;
      }
      return $selected ;
    }
    public static function cdi_get_br_displayselected($selected) {
      $selecteddisplay = "" ;
      foreach ($selected as $sel) {
        $tracking = WC_function_Colissimo::get_string_between($sel, '[', ']') ;
        $orderid = str_replace (' [' . $tracking . ']' , '', $sel) ;
        $order = new WC_Order($orderid);
        $ordernumber = $order->get_order_number();
        $selecteddisplay .= $orderid . '(' . $ordernumber . ') [' . $tracking . ']</br>' ;
      }
      return $selecteddisplay ;
    }
    public static function cdi_ajax_bremise_open_view() {
      update_option('cdi_bremise_set', 'yes') ;
      $selected = self::cdi_get_br_selected() ;
      $selecteddisplay = self::cdi_get_br_displayselected($selected) ;
      $bordereauxdisplay = self::cdi_body_table_bordereau() ;
      echo json_encode(array("message" => "Bienvenue dans le gestionnaire de vos documents logistiques", "htmlselected" => $selecteddisplay, "htmlbordereaux" => $bordereauxdisplay)) ;
      wp_die();
    }
    public static function cdi_ajax_bremise_close_view() {
      delete_option('cdi_br_select_todate') ;
      delete_option('cdi_br_select_fromdate') ;
      delete_option('cdi_br_select_numorders') ;
      delete_option('cdi_br_select_codesuivi') ;
      delete_option('cdi_br_select_extern') ;
      delete_option('cdi_br_select_annulcode') ;
      delete_option('cdi_bremise_set') ;
      wp_die();
    }
    public static function cdi_add_sel($selected, $orderid, $codesuivi) {
      $returnselected = $selected ;
      if ($orderid) {
        $sel = $orderid ;
        if ($codesuivi) {
          $sel = $sel . ' [' . strtoupper(preg_replace("/[^A-Za-z0-9]/", "", $codesuivi)) . ']' ;
        }
        $returnselected[] = $sel ;
      }
      return $returnselected ;
    }
    public static function cdi_ajax_bremise_add_select() {
      global $wpdb;
      global $woocommerce;
      global $message;
      $select = $_POST['select'] ;
      update_option('cdi_br_select_todate', $select['cdi_br_select_todate']) ;
      update_option('cdi_br_select_fromdate', $select['cdi_br_select_fromdate']) ;
      update_option('cdi_br_select_numorders', $select['br_select_numorders_param1']) ;
      update_option('cdi_br_select_codesuivi', $select['br_select_codesuivi_param1']) ;
      update_option('cdi_br_select_extern', $select['br_select_extern_param1']) ;
      update_option('cdi_br_select_annulcode', $select['br_select_annulcode_param1']) ;
      $selected = self::cdi_get_br_selected() ;
      if ($select['br_select_gateway'] == "true"){
        $results = $wpdb->get_results("SELECT * FROM " . $wpdb->prefix . "cdi");
        if (count($results) > 0) {
          foreach ($results as $row) {
            if ($row->cdi_tracking) {
              if ($row->cdi_status == 'open' and wc_get_order($row->cdi_order_id) !== false) {
                $selected = self::cdi_add_sel ($selected, $row->cdi_order_id, $row->cdi_tracking) ;
              }
            }
          }
        }
      }
      if ($select['br_select_orders'] == "true" and $select['cdi_br_select_fromdate'] !== "" and $select['cdi_br_select_todate'] !== ""){
        $datetime1 = new DateTime(str_replace('-', '', $select['cdi_br_select_fromdate']));
        $datetime2 = new DateTime(str_replace('-', '', $select['cdi_br_select_todate']));
        $difference = $datetime1->diff($datetime2);
        //
        // **** Limit of search = 365 days and a max of 500 orders
        //
        if ( ($difference->invert == 0) && ($difference->days < 365)) {
          $customer_orders = get_posts( array(
            'numberposts' => 500,
            'orderby'     => 'date',
            'order'       => 'DESC',
            'post_type'   => wc_get_order_types(),
            'post_status' => array_keys( wc_get_order_statuses() ),
            'date_query' => array(
              'after' => date('Y-m-d', strtotime($select['cdi_br_select_fromdate'] . ' -1 day')),
              'before' => date('Y-m-d', strtotime($select['cdi_br_select_todate'] . ' +1 day')) 
                )
            ) );
          foreach ( $customer_orders as $customer_order ) {
            $order = wc_get_order( $customer_order );
            $orderid = $order->get_id();
            $cdi_tracking = get_post_meta($orderid, '_cdi_meta_tracking', true);
            if ($cdi_tracking) {
              $selected = self::cdi_add_sel ($selected, $orderid, $cdi_tracking) ;
            }
          }
        }
      }
      if ($select['br_select_numorders'] == "true" and $select['br_select_numorders_param1'] !== ""){
        $arraylistenumorders = explode(",", $select['br_select_numorders_param1']) ;
        foreach($arraylistenumorders as $orderid){
          $orderid = preg_replace("/[^0-9]/", "", $orderid);
          if (wc_get_order($orderid) !== false) {
            $cdi_tracking = get_post_meta($orderid, '_cdi_meta_tracking', true);
            if ($cdi_tracking) {
              $selected = self::cdi_add_sel ($selected, $orderid, $cdi_tracking) ;
            }
          }
        }
      }
      if ($select['br_select_codesuivi'] == "true" and $select['br_select_codesuivi_param1'] !== ""){
        $arraylistecodesuivi = explode(",", $select['br_select_codesuivi_param1']) ;
        foreach($arraylistecodesuivi as $codesuivi){
          $codesuivi = preg_replace("/[^A-Z0-9]/", "", $codesuivi);
          if ($codesuivi) {
            $results = $wpdb->get_results("SELECT * FROM " . $wpdb->prefix . "postmeta WHERE `meta_key` LIKE '_cdi_meta_tracking' AND `meta_value` LIKE '" . $codesuivi . "' LIMIT 0 , 1");
            if (count($results) > 0) {
              $orderid = $results[0]->post_id ;
              $selected = self::cdi_add_sel ($selected, $orderid, $codesuivi) ;
            }
          }
        }
      }
      if ($select['br_select_extern'] == "true" and $select['br_select_extern_param1'] !== ""){
        $arraylisteextern = explode(",", $select['br_select_extern_param1']) ;
        foreach($arraylisteextern as $extern){
          if ($extern) {
            $code = WC_function_Colissimo::get_string_between($extern, '[', ']') ;
            $extern = preg_replace("/[^A-Z0-9]/", "", $extern);
            if ($code == "") { // it is an orderid
              if (wc_get_order($extern) !== false) {
                $cdi_tracking = get_post_meta($extern, '_cdi_meta_tracking', true);
                $selected = self::cdi_add_sel ($selected, $extern, $cdi_tracking) ;
              }
            }else{ // it is a tracking code
              $results = $wpdb->get_results("SELECT * FROM " . $wpdb->prefix . "postmeta WHERE `meta_key` LIKE '_cdi_meta_tracking' AND `meta_value` LIKE '" . $extern . "' LIMIT 0 , 1");
              if (count($results) > 0) {
                $orderid = $results[0]->post_id ;
                $selected = self::cdi_add_sel ($selected, $orderid, $extern) ;
              }else{
                $resultscdi = $wpdb->get_results("SELECT * FROM " . $wpdb->prefix . "cdi");
                if (count($resultscdi) > 0) {
                  foreach ($resultscdi as $row) {
                    if ($row->cdi_tracking && ($row->cdi_tracking == $extern)) {
                      if ($row->cdi_status == 'open' and wc_get_order($row->cdi_order_id) !== false) {
                        $selected = self::cdi_add_sel ($selected, $row->cdi_order_id, $row->cdi_tracking) ;
                      }
                    }
                  }
                }
              }
            }
          }
        }
      }
      if ($select['br_select_annulcode'] == "true" and $select['br_select_annulcode_param1'] !== ""){
        $arraylisteannulcode = explode(",", $select['br_select_annulcode_param1']) ;
        foreach($arraylisteannulcode as $annulcode){
          if ($annulcode) {
            $code = WC_function_Colissimo::get_string_between($annulcode, '[', ']') ;
            $annulcode = preg_replace("/[^A-Z0-9]/", "", $annulcode);
            if ($code == "") { // it is an orderid
              if (wc_get_order($annulcode) !== false) {
                $cdi_tracking = get_post_meta($annulcode, '_cdi_meta_tracking', true);
                $selected = array_diff($selected, array($annulcode . ' [' . $cdi_tracking . ']'));
              }
            }else{ // it is a tracking code
              $results = $wpdb->get_results("SELECT * FROM " . $wpdb->prefix . "postmeta WHERE `meta_key` LIKE '_cdi_meta_tracking' AND `meta_value` LIKE '" . $annulcode . "' LIMIT 0 , 1");
              if (count($results) > 0) {
                $orderid = $results[0]->post_id ;
                $selected = array_diff($selected, array($orderid . ' [' . $annulcode . ']'));
              }else{
                $resultscdi = $wpdb->get_results("SELECT * FROM " . $wpdb->prefix . "cdi");
                if (count($resultscdi) > 0) {
                  foreach ($resultscdi as $row) {
                    if ($row->cdi_tracking && ($row->cdi_tracking == $annulcode)) {
                      if ($row->cdi_status == 'open' and wc_get_order($row->cdi_order_id) !== false) {
                        $selected = array_diff($selected, array($row->cdi_order_id . ' [' . $row->cdi_tracking . ']'));
                      }
                    }
                  }
                }
              }
            }
          }
        }
      }
      $maxitemlogistic = get_option('wc_settings_tab_colissimo_maxitemlogistic');
      if (!$maxitemlogistic) {
        $maxitemlogistic = 100 ;
      }
      $selected = array_unique ($selected) ;
      rsort ($selected, SORT_NUMERIC ) ;
      if (count($selected) > $maxitemlogistic) {
        $array = array() ;
        foreach ($selected as $sel) {
          if (count($array) < $maxitemlogistic) {
            $array[] = $sel ;
          }
        }
        $selected = $array ;
        update_option('cdi_selected_bremise', $selected) ;
        $selecteddisplay = self::cdi_get_br_displayselected($selected) ;
        echo json_encode(array("message" => count($selected) . " commandes/colis sélectionnés. <mark>Dépassement de la limite du nombre de colis. Uniquement les commandes les plus récentes sont retenues pour la confection de vos documents.</mark>", "htmlselected" => $selecteddisplay)) ;
        wp_die();
      }
      update_option('cdi_selected_bremise', $selected) ;
      $selecteddisplay = self::cdi_get_br_displayselected($selected) ;
      echo json_encode(array("message" => count($selected) . " commandes/colis sélectionnés, qui sont en attente pour la confection de vos documents.", "htmlselected" => $selecteddisplay)) ;
      wp_die();
    }
    public static function cdi_ajax_bremise_clear_select() {
      delete_option('cdi_selected_bremise') ;
      echo json_encode(array("message" => "La liste des colis sélectionnés a été purgée. Vous pouvez maintenant refaire vos sélections.", "htmlselected" => "")) ;
      wp_die();
    }
    public static function cdi_ajax_bremise_exec_bordereau() {
      global $base64bordereau;
      require_once dirname(__FILE__) . '/ColissimoAF/ColissimoAFAutoload.php';
      $errorws = "" ;
      $selected = self::cdi_get_br_selected() ;
      if (!empty($selected)) {
        $wsdl = array();
        $wsdl[ColissimoAFWsdlClass::WSDL_URL] = 'http://ws.Colissimo.fr/sls-ws/SlsServiceWS?wsdl';
        $wsdl[ColissimoAFWsdlClass::WSDL_CACHE_WSDL] = WSDL_CACHE_NONE;
        $wsdl[ColissimoAFWsdlClass::WSDL_TRACE] = true;
        $wsdlObject = new ColissimoAFStructGenerateBordereauByParcelsNumbers($wsdl);
        $wsdlObject->setContractNumber(get_option('wc_settings_tab_colissimo_ws_ContractNumber')); 
        $wsdlObject->setPassword(get_option('wc_settings_tab_colissimo_ws_Password'));
        $wsdlObject->generateBordereauParcelNumberList = new ColissimoAFStructGenerateBordereauParcelNumberList();
        foreach ($selected as $parcelsnumbers) {
          $tracking = WC_function_Colissimo::get_string_between($parcelsnumbers, '[', ']') ;
          $wsdlObject->generateBordereauParcelNumberList->parcelsNumbers[] = $tracking ;
        }
        $ColissimoAFServiceGenerate = new ColissimoAFServiceGenerate();
        if ($ColissimoAFServiceGenerate->generateBordereauByParcelsNumbers($wsdlObject)) {
          $ok = $ColissimoAFServiceGenerate->getResult();
          $retid = $ok->return->messages[0]->id;
          $retmessageContent = $ok->return->messages[0]->messageContent;
          if ($retid == 0) {
            // process the data
            $address = $ok->return->bordereau->bordereauHeader->address ;
            $bordereauNumber = $ok->return->bordereau->bordereauHeader->bordereauNumber ;
            $clientNumber = $ok->return->bordereau->bordereauHeader->clientNumber ;
            $codeSitePCH = $ok->return->bordereau->bordereauHeader->codeSitePCH ;
            $company = $ok->return->bordereau->bordereauHeader->company ;
            $numberOfParcels = $ok->return->bordereau->bordereauHeader->numberOfParcels ;
            $publishingDate = $ok->return->bordereau->bordereauHeader->publishingDate ;
            $datetimebd = date('Y-m-d H:i:s') ;
            if ($base64bordereau) {
              self::cdi_stockage_bordereau('BD', $bordereauNumber,  '=> ' . $datetimebd, $numberOfParcels, $base64bordereau) ;
            }else{
              $errorws = __(' ===> Error processing Bordereau de dépôt Colissimo', 'colissimo-delivery-integration')  . ' - ' . $retid . ' : ' . $retmessageContent ;
            }
          }else{
            // process the error from soap server
            WC_function_Colissimo::cdi_debug(__LINE__ ,__FILE__ , $retid, 'exp');
            WC_function_Colissimo::cdi_debug(__LINE__ ,__FILE__ , $retmessageContent, 'exp');
            $last = $ColissimoAFServiceGenerate->getLastRequest();
            WC_function_Colissimo::cdi_debug(__LINE__ ,__FILE__ , $last, 'exp');
            $ret = $ColissimoAFServiceGenerate->getLastResponse();
            WC_function_Colissimo::cdi_debug(__LINE__ ,__FILE__ , $ret, 'exp');
            $errorws = __(' ===> Error processing Bordereau de dépôt Colissimo', 'colissimo-delivery-integration')  . ' - ' . $retid . ' : ' . $retmessageContent ;
          }
        }else{
          // process the error from soap client
          $nok = $ColissimoAFServiceGenerate->getLastError();
          $last = $ColissimoAFServiceGenerate->getLastRequest();
          $ret = $ColissimoAFServiceGenerate->getLastResponse();
          $retid = $nok['ColissimoAFServiceGenerate::generateBordereauByParcelsNumbers']->faultcode;
          $retmessageContent = $nok['ColissimoAFServiceGenerate::generateBordereauByParcelsNumbers']->faultstring;
          WC_function_Colissimo::cdi_debug(__LINE__ ,__FILE__ , $retid, 'tec');
          WC_function_Colissimo::cdi_debug(__LINE__ ,__FILE__ , $retmessageContent, 'tec');
          WC_function_Colissimo::cdi_debug(__LINE__ ,__FILE__ , $last, 'tec');
          WC_function_Colissimo::cdi_debug(__LINE__ ,__FILE__ , $ret, 'tec');
          $errorws = __(' ===> Error processing Bordereau de dépôt Colissimo', 'colissimo-delivery-integration') . ' - ' . $retid . ' : ' . $retmessageContent ;
        }
      }
      if ($errorws !== "") {
        $message = $errorws ;
      }else{
        $message = "Votre bordereau a été généré. C'est le premier de votre liste. Cliquez sur \"Voir\" pour le visualiser ou l'imprimer" ;
      }
      $selecteddisplay = self::cdi_get_br_displayselected($selected) ;
      $bordereauxdisplay = self::cdi_body_table_bordereau() ;
      echo json_encode(array("message" => $message, "htmlselected" => $selecteddisplay, "htmlbordereaux" => $bordereauxdisplay)) ;
      wp_die();
    }

    public static function cdi_ajax_btransport_exec_bordereau() {
      $errorws = "" ;
      $selected = self::cdi_get_br_selected() ;
      if (count($selected) > 0) {
        $nbcolis = count($selected) ;
        $refbt =  date('YmdHis') ;
        $datetimebt = date('Y-m-d H:i:s') ;
        $pdf = new FPDI();
        $pdf->AliasNbPages();
        $pdf->AddPage('L','A4'); // Paysage
        $pdf->SetAutoPageBreak('on',10);
        $pdf->SetTextColor(0, 0, 0);
        //$pdf->SetDrawColor(0,0,0) ;
        $pdf->SetFillColor(255,255,255) ;
        // Title
        $pdf->SetFont('Arial','BU',16);
        $pdf->Cell(100);
        $pdf->Cell(0,20,'BON DE TRANSPORT',0,1);
        // Bordereau refs
        $pdf->SetFont('Arial','B',12);
        $x=215; $y=30;
        $pdf->Text($x,$y+0,utf8_decode ('Créé le : ' . $datetimebt));
        $pdf->Text($x,$y+5,utf8_decode ('BT Référence : BT-' . $refbt));
        $pdf->Text($x,$y+10,utf8_decode ('Nombre colis : ' . $nbcolis));
        // Dest
        $pdf->SetFont('Arial','B',12);
        $x=110; $y=40;
        $pdf->Text($x,$y+0,utf8_decode ('................................................'));
        // Merchand
        $pdf->SetFont('Arial','B',12);
        $x=15; $y=30;
        $pdf->Text($x,$y+0,utf8_decode (get_option('wc_settings_tab_colissimo_ws_sa_CompanyName')));
        $pdf->Text($x,$y+5,utf8_decode (get_option('wc_settings_tab_colissimo_ws_sa_Line1')));
        $pdf->Text($x,$y+10,utf8_decode (get_option('wc_settings_tab_colissimo_ws_sa_Line2')));
        $pdf->Text($x,$y+15,utf8_decode (get_option('wc_settings_tab_colissimo_ws_sa_ZipCode') . ' ' . get_option('wc_settings_tab_colissimo_ws_sa_City')));
        // En-tete
        $pdf->SetFont('Arial','B',12);
        $pdf->SetXY(10, 50) ;
        $pdf->Cell(0,1,'',1,1); // Line
        $pdf->Cell(20,10,'',0,0,'C'); // Rang
        $pdf->Cell(30,10,'Id',0,0,'C');
        $pdf->Cell(30,10,'Suivi',0,0,'C');
        $pdf->Cell(80,10,'Adresse(L1)',0,0,'C');
        $pdf->Cell(10,10,'CP',0,0,'C');
        $pdf->Cell(50,10,'Ville',0,0,'C');
        $pdf->Cell(10,10,'Pays',0,0,'C');
        $pdf->Cell(15,10,'kg',0,0,'C');
        $pdf->Cell(15,10,'dm3',0,0,'C');
        $pdf->Cell(10,10,'NS',0,0,'C');
        $pdf->Cell(0,10,'',0,1); // End
        $pdf->Cell(0,1,'',1,1); // Line
        // Parcels
        $pdf->SetFont('Arial','',12);
        $rang = 0 ;
        $totalweight = 0 ;
        $totalvolume = 0 ;
        foreach ($selected as $parcel) {
          $rang = $rang+1 ;
          $tracking = WC_function_Colissimo::get_string_between($parcel, '[', ']') ;
          $orderid = str_replace (' [' . $tracking . ']' , '', $parcel) ;
          $order = new WC_Order($orderid);
          $ordernumber = $order->get_order_number();
          $volorder = 0 ;
          $items_chosen = WC_function_Colissimo::cdi_get_items_chosen($order);
          foreach( $items_chosen as $item ) { 
            $product_id = $item['variation_id'] ;
            if($product_id == 0) { // No variation for that one
              $product_id = $item['product_id'];
            }
            $product = wc_get_product( $product_id);
            //$prodname = $product->get_name();
            //$sku = $product->get_sku();
            $quantity = $item['qty'] ;
            $dimensions = $product->get_dimensions(false) ;
            if ( $dimensions['length'] && $dimensions['width'] && $dimensions['height']) {
              $vol = $dimensions['length']*$dimensions['width']*$dimensions['height'];
              if (get_option( 'woocommerce_dimension_unit' ) == 'cm') {
                $vol = $vol/1000 ; // Convert cm3 to dm3
              }else{
                $vol = $vol*1000 ; // Convert m3 to dm3
              }
            }else{
              $vol = 0 ;
            }
            $vol = $vol* $quantity ;
            $volorder = $volorder + $vol ;
            $totalvolume = $totalvolume + $vol ;
          }
          $array_for_carrier = WC_function_Colissimo::cdi_array_for_carrier($orderid) ;
          $pdf->Cell(20,10,$rang,0,0,'C');
          $pdf->Cell(30,10, $orderid . '(' . $ordernumber .')' ,0,0,'C');
          $pdf->Cell(30,10,$tracking,0,0,'C');
          $pdf->Cell(80,10,$array_for_carrier['shipping_address_1'],0,0,'C');
          $pdf->Cell(10,10,$array_for_carrier['shipping_postcode'],0,0,'C');
          $pdf->Cell(50,10,$array_for_carrier['shipping_city_state'],0,0,'C');
          $pdf->Cell(10,10,$array_for_carrier['shipping_country'],0,0,'C');
          $totalweight = $totalweight + $array_for_carrier['parcel_weight']/1000 ;
          $pdf->Cell(15,10,$array_for_carrier['parcel_weight']/1000,0,0,'C');
          $pdf->Cell(15,10,$volorder,0,0,'C');
          $NonMachinable = str_replace(array('colis-standard', 'colis-volumineux', 'colis-rouleau'), array('', 'volu', 'roul'), $array_for_carrier['parcel_type']);
          $pdf->Cell(10,10,$NonMachinable,0,0,'C');
          $pdf->Cell(0,10,'',0,1); // End
        }
        $pdf->Cell(0,1,'',1,1); // Line
        // Sum
        //$pdf->Cell(0,5,' ',0,1); // Blank line
        $pdf->Cell(30,10,'TOTAUX : ',0,0,'C');
        $pdf->Cell(200,10,' ',0,0,'C');
        $pdf->Cell(15,10,$totalweight,0,0,'C');
        $pdf->Cell(15,10,$totalvolume,0,0,'C');
        $pdf->Cell(0,10,'',0,1); // End
        // Complete the form
        $pdf->Cell(0,5,' ',0,1); // Blank line
        $pdf->Cell(40,15,'Visa ................................................',0,0,'L');
        $pdf->Cell(0,15,'',0,1); // End
        // End write
        $resultpdf = $pdf->Output('S');
        $resultpdf = base64_encode ($resultpdf);
        self::cdi_stockage_bordereau('BT', $refbt, '=> ' . $datetimebt, $nbcolis, $resultpdf) ; // '=> ' only to sure to be the first in sort
      }
      if ($errorws !== "") {
        $message = $errorws ;
      }else{
        $message = "Votre bordereau a été généré. C'est le premier de votre liste. Cliquez sur \"Voir\" pour le visualiser ou l'imprimer" ;
      }
      $selecteddisplay = self::cdi_get_br_displayselected($selected) ;
      $bordereauxdisplay = self::cdi_body_table_bordereau() ;
      echo json_encode(array("message" => $message, "htmlselected" => $selecteddisplay, "htmlbordereaux" => $bordereauxdisplay)) ;
      wp_die();
    }

    public static function cdi_ajax_bpreparation_exec_bordereau() {
      $errorws = "" ;
      $selected = self::cdi_get_br_selected() ;
      if (count($selected) > 0) {
        $nbcolis = count($selected) ;
        $ranglp = 0 ;
        $reflp =  date('YmdHis') ;
        $datetimelp = date('Y-m-d H:i:s') ;
        $pdf = new FPDI();
        $pdf->AliasNbPages();
        $pdf->AddPage('L','A4'); // Landscape
        $pdf->SetAutoPageBreak('on',10);
        $pdf->SetTextColor(0, 0, 0);
        $pdf->SetFillColor(255,255,255) ;
        foreach ($selected as $parcel) { 
          if ($ranglp !== 0) {
            $pdf->AddPage('L','A4'); // Landscape
          }
          $ranglp = $ranglp+1 ;
          // Title
          $pdf->SetFont('Arial','BU',16);
          $pdf->Cell(100);
          $pdf->Cell(0,20,'LISTE DE PREPARATION',0,1);
          // Liste refs
          $pdf->SetFont('Arial','B',12);
          $x=215; $y=30;
          $pdf->Text($x,$y+0,utf8_decode ('Créé le : ' . $datetimelp));
          $pdf->Text($x,$y+5,utf8_decode ('LP Référence : LP-' . $reflp . '-' . $ranglp));
          $pdf->Text($x,$y+10,utf8_decode ('Nombre colis : ' . '1'));
          // Dest
          $pdf->SetFont('Arial','B',12);
          $x=110; $y=40;
          $pdf->Text($x,$y+0,utf8_decode ('........................................................'));
          // Merchand
          $pdf->SetFont('Arial','B',12);
          $x=15; $y=30;
          $pdf->Text($x,$y+0,utf8_decode (get_option('wc_settings_tab_colissimo_ws_sa_CompanyName')));
          $pdf->Text($x,$y+5,utf8_decode (get_option('wc_settings_tab_colissimo_ws_sa_Line1')));
          $pdf->Text($x,$y+10,utf8_decode (get_option('wc_settings_tab_colissimo_ws_sa_Line2')));
          $pdf->Text($x,$y+15,utf8_decode (get_option('wc_settings_tab_colissimo_ws_sa_ZipCode') . ' ' . get_option('wc_settings_tab_colissimo_ws_sa_City')));
          // Customer
          $tracking = WC_function_Colissimo::get_string_between($parcel, '[', ']') ;
          $orderid = str_replace (' [' . $tracking . ']' , '', $parcel) ;
          $array_for_carrier = WC_function_Colissimo::cdi_array_for_carrier($orderid) ;
          $order = new WC_Order($orderid);
          $ordernumber = $order->get_order_number();
          $pdf->SetFont('Arial','B',12);
          $pdf->SetXY(10, 50) ;
          $pdf->Cell(0,1,'',1,1); // Line
          $pdf->Cell(40,10,'Destinataire: ',0,0,'L');
          $pdf->Cell(0,10,$array_for_carrier['shipping_company'] . ' ' . $array_for_carrier['shipping_first_name'] . ' ' . $array_for_carrier['shipping_last_name'] ,0,0,'L');
          $pdf->Cell(0,10,'',0,1); // End
          $pdf->Cell(40,10,'',0,0,'L');
          $pdf->Cell(0,10,$array_for_carrier['shipping_address_1'] . ' ' . $array_for_carrier['shipping_address_2'] . ' ' . $array_for_carrier['shipping_address_3'] . ' ' .  $array_for_carrier['shipping_address_4'],0,0,'L');
          $pdf->Cell(0,10,'',0,1); // End
          $pdf->Cell(40,10,'',0,0,'L');
          $pdf->Cell(0,10,$array_for_carrier['shipping_postcode'] . ' ' . $array_for_carrier['shipping_city_state'] . ' ' . $array_for_carrier['shipping_country'],0,0,'L');
          $pdf->Cell(0,10,'',0,1); // End
          $pdf->Cell(40,10,'',0,0,'L');
          $pdf->Cell(0,10,$array_for_carrier['billing_phone'] . ' / ' . $array_for_carrier['billing_email'],0,0,'L');
          $pdf->Cell(0,10,'',0,1); // End
          // Parcel
          $pdf->Cell(0,1,'',1,1); // Line
          $volorder = 0 ;
          $items_chosen = WC_function_Colissimo::cdi_get_items_chosen($order);
          foreach( $items_chosen as $item ) { 
            $product_id = $item['variation_id'] ;
            if($product_id == 0) { // No variation for that one
              $product_id = $item['product_id'];
            }
            $product = wc_get_product( $product_id);
            //$prodname = $product->get_name();
            //$sku = $product->get_sku();
            $quantity = $item['qty'] ;
            $dimensions = $product->get_dimensions(false) ;
            if ( $dimensions['length'] && $dimensions['width'] && $dimensions['height']) {
              $vol = $dimensions['length']*$dimensions['width']*$dimensions['height'];
              if (get_option( 'woocommerce_dimension_unit' ) == 'cm') {
                $vol = $vol/1000 ; // Convert cm3 to dm3
              }else{
                $vol = $vol*1000 ; // Convert m3 to dm3
              }
            }else{
              $vol = 0 ;
            }
            $vol = $vol* $quantity ;
            $volorder = $volorder + $vol ;
          }
          $pdf->Cell(40,10,'Colis: ',0,0,'L');
          $pdf->Cell(0,10,'Id ' . $orderid . '(' . $ordernumber .') ; Suivi ' . $tracking . '; Poids total ' . $array_for_carrier['parcel_weight']/1000 . 'kg; Volume ' . $volorder . 'dm3; Emballage ' . get_option('wc_settings_tab_colissimo_parcelweight')/1000 . 'kg' . str_replace(array('colis-standard', 'colis-volumineux', 'colis-rouleau'), array('; Standard', '; Volumineux', '; Tube'), $array_for_carrier['parcel_type']),0,0,'L');
          $pdf->Cell(0,10,'',0,1); // End
          // Products
          $pdf->Cell(0,1,'',1,1); // Line
          $pdf->Cell(40,10,utf8_decode ('Articles: '),0,0,'L');
          $pdf->Cell(40,10,utf8_decode ('Référence'),0,0,'L');
          $pdf->Cell(80,10,utf8_decode ('Désignation'),0,0,'L');
          $pdf->Cell(20,10,utf8_decode ('Quantité'),0,0,'L');
          $pdf->Cell(40,10,utf8_decode ('Poids unitaire(kg)'),0,0,'L');
          $pdf->Cell(40,10,utf8_decode ('Volume unitaire(dm3)'),0,0,'L');
          $pdf->Cell(0,10,'',0,1); // End
          foreach( $items_chosen as $item ) { 
            $product_id = $item['variation_id'] ;
            if($product_id == 0) { // No variation for that one
              $product_id = $item['product_id'];
            }
            $product = wc_get_product( $product_id);
            $weight = $product->get_weight();
            if ($weight and is_numeric($weight) and $weight !== 0) { // Supp Virtual and no-weighted products
              $prodname = $product->get_name();
              $ugs = $product->get_sku();
              $quantity = $item['qty'] ;
              if (get_option('woocommerce_weight_unit') == 'g') {
                $weight = $weight/1000 ; // Convert g to kg
              }
              $dimensions = $product->get_dimensions(false) ;
              if ( $dimensions['length'] && $dimensions['width'] && $dimensions['height']) {
                $vol = $dimensions['length']*$dimensions['width']*$dimensions['height'];
                if (get_option( 'woocommerce_dimension_unit' ) == 'cm') {
                  $vol = $vol/1000 ; // Convert cm3 to dm3
                }else{
                  $vol = $vol*1000 ; // Convert m3 to dm3
                }
              }else{
                $vol = 0 ;
              }
              $pdf->Cell(40,10,'',0,0,'L');
              $pdf->Cell(40,10,utf8_decode ($ugs),0,0,'L');
              $pdf->Cell(80,10,utf8_decode ($prodname),0,0,'L');
              $pdf->Cell(20,10,utf8_decode ($quantity),0,0,'L');
              $pdf->Cell(40,10,utf8_decode ($weight),0,0,'L');
              $pdf->Cell(40,10,utf8_decode ($vol),0,0,'L');
              $pdf->Cell(0,10,'',0,1); // End 
            }
          }
          // Complete the form
          $pdf->Cell(0,1,'',1,1); // Line
          $pdf->Cell(0,5,' ',0,1); // Blank line
          $pdf->Cell(40,15,'Visa : ................................................',0,0,'L');
          $pdf->Cell(0,15,'',0,1); // End
        }
        // End write
        $resultpdf = $pdf->Output('S');
        $resultpdf = base64_encode ($resultpdf);
        self::cdi_stockage_bordereau('LP', $reflp, '=> ' . $datetimelp, $nbcolis, $resultpdf) ; // '=> ' only to sure to be the first in sort
      }
      if ($errorws !== "") {
        $message = $errorws ;
      }else{
        $message = "Votre bordereau a été généré. C'est le premier de votre liste. Cliquez sur \"Voir\" pour le visualiser ou l'imprimer" ;
      }
      $selecteddisplay = self::cdi_get_br_displayselected($selected) ;
      $bordereauxdisplay = self::cdi_body_table_bordereau() ;
      echo json_encode(array("message" => $message, "htmlselected" => $selecteddisplay, "htmlbordereaux" => $bordereauxdisplay)) ;
      wp_die();
    }

    public static function cdi_ajax_blivraison_exec_bordereau() {
      $errorws = "" ;
      $selected = self::cdi_get_br_selected() ;
      if (count($selected) > 0) {
        $nbcolis = count($selected) ;
        $refbt =  date('YmdHis') ;
        $datetimebt = date('Y-m-d H:i:s') ;
        $pdf = new FPDI();
        $pdf->AliasNbPages();
        $pdf->AddPage('L','A4'); // Paysage
        $pdf->SetAutoPageBreak('on',10);
        $pdf->SetTextColor(0, 0, 0);
        //$pdf->SetDrawColor(0,0,0) ;
        $pdf->SetFillColor(255,255,255) ;
        // Title
        $pdf->SetFont('Arial','BU',16);
        $pdf->Cell(100);
        $pdf->Cell(0,20,'ETAT DES LIVRAISONS',0,1);
        // Bordereau refs
        $pdf->SetFont('Arial','B',12);
        $x=215; $y=30;
        $pdf->Text($x,$y+0,utf8_decode ('Créé le : ' . $datetimebt));
        $pdf->Text($x,$y+5,utf8_decode ('EL Référence : EL-' . $refbt));
        $pdf->Text($x,$y+10,utf8_decode ('Nombre colis : ' . $nbcolis));
        // Dest
        //$pdf->SetFont('Arial','B',12);
        //$x=110; $y=40;
        //$pdf->Text($x,$y+0,utf8_decode ('................................................'));
        // Merchand
        $pdf->SetFont('Arial','B',12);
        $x=15; $y=30;
        $pdf->Text($x,$y+0,utf8_decode (get_option('wc_settings_tab_colissimo_ws_sa_CompanyName')));
        $pdf->Text($x,$y+5,utf8_decode (get_option('wc_settings_tab_colissimo_ws_sa_Line1')));
        $pdf->Text($x,$y+10,utf8_decode (get_option('wc_settings_tab_colissimo_ws_sa_Line2')));
        $pdf->Text($x,$y+15,utf8_decode (get_option('wc_settings_tab_colissimo_ws_sa_ZipCode') . ' ' . get_option('wc_settings_tab_colissimo_ws_sa_City')));
        // En-tete
        $pdf->SetFont('Arial','B',12);
        $pdf->SetXY(10, 50) ;
        $pdf->Cell(0,1,'',1,1); // Line
        $pdf->Cell(20,10,'',0,0,'C'); // Rang
        $pdf->Cell(30,10,'Id',0,0,'C');
        $pdf->Cell(30,10,'Suivi',0,0,'C');
        $pdf->Cell(80,10,'Adresse(L1)',0,0,'C');
        $pdf->Cell(10,10,'CP',0,0,'C');
        $pdf->Cell(50,10,'Ville',0,0,'C');
        $pdf->Cell(10,10,'Pays',0,0,'C');
        $pdf->Cell(45,10,'Livraison Colis',0,0,'C');
        $pdf->Cell(0,10,'',0,1); // End
        $pdf->Cell(0,1,'',1,1); // Line
        // Parcels
        $pdf->SetFont('Arial','',12);
        $rang = 0 ;
        foreach ($selected as $parcel) {
          $rang = $rang+1 ;
          $tracking = WC_function_Colissimo::get_string_between($parcel, '[', ']') ;
          $orderid = str_replace (' [' . $tracking . ']' , '', $parcel) ;
          $order = new WC_Order($orderid);
          $ordernumber = $order->get_order_number();
          $array_for_carrier = WC_function_Colissimo::cdi_array_for_carrier($orderid) ;
          $pdf->Cell(0,5,'',0,1); 
          $pdf->Cell(20,5,$rang,0,0,'C');
          $pdf->Cell(30,5, $orderid . '(' . $ordernumber .')' ,0,0,'C');
          $pdf->Cell(30,5,$tracking,0,0,'C');
          $pdf->Cell(80,5,$array_for_carrier['shipping_address_1'],0,0,'C');
          $pdf->Cell(10,5,$array_for_carrier['shipping_postcode'],0,0,'C');
          $pdf->Cell(50,5,$array_for_carrier['shipping_city_state'],0,0,'C');
          $pdf->Cell(10,5,$array_for_carrier['shipping_country'],0,0,'C');
          $etat = WC_function_Colissimo::cdi_get_inovert($orderid, $tracking);
          $pdf->MultiCell(48,5,$etat);
        }
        $pdf->Cell(0,1,'',1,1); // Line
        $pdf->Cell(0,10,'',0,1); // End
        // End write
        $resultpdf = $pdf->Output('S');
        $resultpdf = base64_encode ($resultpdf);
        self::cdi_stockage_bordereau('EL', $refbt, '=> ' . $datetimebt, $nbcolis, $resultpdf) ; // '=> ' only to sure to be the first in sort
      }
      if ($errorws !== "") {
        $message = $errorws ;
      }else{
        $message = "Votre bordereau a été généré. C'est le premier de votre liste. Cliquez sur \"Voir\" pour le visualiser ou l'imprimer" ;
      }
      $selecteddisplay = self::cdi_get_br_displayselected($selected) ;
      $bordereauxdisplay = self::cdi_body_table_bordereau() ;
      echo json_encode(array("message" => $message, "htmlselected" => $selecteddisplay, "htmlbordereaux" => $bordereauxdisplay)) ;
      wp_die();
    }

    public static function cdi_stockage_bordereau($type, $reference, $date, $nbcolis, $content) {
      WC_function_Colissimo::cdi_uploads_put_contents ($type . '-' . $reference, 'bordereau', $content) ;
      $listebordereaux = get_option('cdi_liste_borderaux_remise');
      if (empty($listebordereaux)) {
        $listebordereaux = array() ;
      }
      $listebordereaux[] = array('typedocument' => $type,
                                 'bordereauNumber' => $reference,
                                 'publishingDate' => $date,
                                 'numberOfParcels' => $nbcolis,
                                 ) ;
      // Sort and Suppress old Bordereaux
      function cmpbr($a, $b) { return strcmp($b['publishingDate'], $a['publishingDate']); }
      usort($listebordereaux, "cmpbr");
      $newlistbordreaux = array() ;
      $nbbordereaux = 0 ;
      $maxitemlogistic = get_option('wc_settings_tab_colissimo_maxitemlogistic');
      if (!$maxitemlogistic) {
        $maxitemlogistic = 100 ;
      }
      foreach ($listebordereaux as $bordereau) {
        $nbbordereaux = $nbbordereaux + 1 ;
        if ($nbbordereaux <= $maxitemlogistic) { 
          $newlistbordreaux[] = $bordereau ;      
        }else{
          // Suppress old files
          $upload_dir = wp_upload_dir();
          $dircdistore = trailingslashit($upload_dir['basedir']).'cdistore';
          $url = wp_nonce_url('plugins.php?page=colissimo-delivery-integration');
          $creds = request_filesystem_credentials($url, "", false, false, null) ;
          global $wp_filesystem;
          $filename = trailingslashit($dircdistore). 'CDI-' . 'bordereau' . '-' . $bordereau['typedocument'] . '-' . $bordereau['bordereauNumber'] .'.txt';
          $result = $wp_filesystem->delete( $filename) ;
        }
      }
      update_option('cdi_liste_borderaux_remise',$newlistbordreaux);
    }

    public static function cdi_body_table_bordereau() {
      $listebordereaux = get_option('cdi_liste_borderaux_remise');
      $htmlbody = "" ;
      if (empty($listebordereaux)) {
        $htmlbody .= "<tr class='no-items'><td colspan='3' class='colspanchange'>Aucun document n'a été généré.</td></tr>";
      }else{
        foreach ($listebordereaux as $bordereau) {
          $tdtitrebordereau = '' ;
          foreach ($bordereau as $key=>$value) {
            $tdtitrebordereau .= ' | ' . $key . '=>' . $value ;
          }
          $datebordereau = str_replace("T", " ",$bordereau['publishingDate']) ;
          $tdvoirbordereau = '<td style="overflow:hidden; white-space:nowrap; text-overflow: ellipsis;"><form method="post" id="cdi_bordereau_voir" action="" style="display:inline-block;"><input type="hidden" name="cdi_bordereau_voir_post" value="' . $bordereau['typedocument'] . '-' . $bordereau['bordereauNumber'] . '"><input type="submit" name="cdi_bordereau_voir" value="Voir"  title="Print bordereau' . $tdtitrebordereau . '" /></form></td>' ;
          $htmlbody .= '<tr><td>' . $datebordereau . '</td><td>' . $bordereau['typedocument'] . '-' . $bordereau['bordereauNumber'] . '</td><td>' . $bordereau['numberOfParcels'] . '</td>' . $tdvoirbordereau . '</tr>';
        }
      }
      return $htmlbody ;
    }

    public static function cdi_bordereau_voir() {
      if ( isset($_POST['cdi_bordereau_voir']) ) {
        global $woocommerce;
        global $wpdb;
        $bordereau_id = $_POST['cdi_bordereau_voir_post'] ;
        if (current_user_can('cdi_gateway')) {
          $cdi_locbordereau = WC_function_Colissimo::cdi_uploads_get_contents ($bordereau_id, 'bordereau'); 
          if ($cdi_locbordereau) {
            $cdi_locbordereau_pdf = base64_decode ($cdi_locbordereau);
            $out = fopen('php://output', 'w');
            $thepdffile = 'Bordereau-' . $bordereau_id . '-' . date('YmdHis') . '.pdf' ;
            header('Content-Type: application/pdf' );
            header('Content-Disposition: attachment; filename=' . $thepdffile );
            fwrite($out, $cdi_locbordereau_pdf) ;
            fclose($out);
            die ();
          }

        } // End current_user_can
      }
    } 
}


?>
