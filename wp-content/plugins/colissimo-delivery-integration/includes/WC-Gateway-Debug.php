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
/* Add Debug actions in the Gateway                                 */
/****************************************************************************************/
class WC_Gateway_debug {
    public static function init() {
      add_action('wp_ajax_cdi_debug_open_view',  __CLASS__ . '::cdi_ajax_debug_open_view');
      add_action('wp_ajax_cdi_debug_close_view',  __CLASS__ . '::cdi_ajax_debug_close_view');
      add_action('wp_ajax_cdi_debug_clear_file',  __CLASS__ . '::cdi_ajax_debug_clear_file');
      add_action('wp_ajax_cdi_debug_refresh_view',  __CLASS__ . '::cdi_ajax_debug_refresh_view');
    }
    /**
     * Manage the Debug function.
     *
     */
    public static function debug_manage() {
      if (WC_function_Colissimo::cdi_isconnected()) {
        eval (WC_function_Colissimo::cdi_eval('7')) ; 
        ?>
        <div id="debugmanagewrap">
          <div id="debugmanage" style="display:none;">
            <h2>CDI Debug</h2>
            <p>
              <input type="button" id="cdi-debug-close" class="button button-primary mode-run" value="<?php _e( 'Close', 'colissimo-delivery-integration' ); ?>" />
              <input type="button" id="cdi-debug-clear" class="button button-primary" value="<?php _e( 'Suppress debug.log', 'colissimo-delivery-integration' ); ?>" />
              <input type="button" id="cdi-debug-refresh" class="button button-primary" value="<?php _e( 'Refresh view', 'colissimo-delivery-integration' ); ?>" />
              <!-- <input type="button" id="cdi-debug-select" class="button button-primary" value="<?php _e( 'Select Debug View', 'colissimo-delivery-integration' ); ?>" /> -->
              <a> </a><?php _e( ' Select Debug View ==>', 'colissimo-delivery-integration' ); ?> 
                  <select name="cdi-debug-select" id="cdi-debug-select">
                    <option value="cdi">CDI - Tous les messages CDI</option>
                    <option value="exp">CDI - Erreurs d'exploitation (exp)</option>
                    <option value="tec">CDI - Incidents techniques (tec)</option>
                    <option value="msg">CDI - Messages de suivi d'exploitation (msg)</option>
                    <option value="log">Tous les messages de debug.log</option>
                  </select> 
              </p>
            <textarea id="cdi-debug-area" style="font-size: 14px;width: 98%; height:calc(100vh - 50px);"></textarea>
          <!-- End of div id="debugmanage" -->
          </div>
        </div> 
        <?php
        eval (WC_function_Colissimo::cdi_eval('12')) ; 
      }
    }
    public static function cdi_debug_open_button() {
    if (WC_function_Colissimo::cdi_isconnected()) {
      eval (WC_function_Colissimo::cdi_eval('7')) ; 
      $cdi_save_init_set = get_option('cdi_save_init_set') ;
      if (!$cdi_save_init_set) {
        $color = '#0085ba' ; // debug is close
      }else{
        $color = 'red' ; // debug is running
      }
      ?><em></em><input type="button" id="cdi-debug-open" class="mode-run" value="<?php _e( 'Debug View', 'colissimo-delivery-integration' ); ?>" style="float: left; background-color: <?php echo $color; ?>; color:white;" title="Debug CDI. Pour ouvrir cliquez !" /><em></em><?php
      eval (WC_function_Colissimo::cdi_eval('19')) ; 
      ?><script>
          jQuery(document).ready(function($){ 
            var cdidebugarea = $( '#cdi-debug-area' );
            var ajaxurl = "<?php echo $ajaxurl; ?>";
            var doopen          = $( '#cdi-debug-open' );
            var doclose          = $( '#cdi-debug-close' );
            var docloseajax      = { 'action': 'cdi_debug_close_view' };
            var doclear          = $( '#cdi-debug-clear' );
            var doclearajax      = { 'action': 'cdi_debug_clear_file' };
            var dorefresh          = $( '#cdi-debug-refresh' );
            var doselect          = $( '#cdi-debug-select' );
            var currentselect ;
            jQuery(doopen).click(function(){
              currentselect = 'cdi' ;
              var doopenajax      = { 'action': 'cdi_debug_open_view', 'select': currentselect };
              jQuery.post(ajaxurl, doopenajax, function(response) {
	        cdidebugarea.html( response );
	        cdidebugarea.scrollTop( cdidebugarea[0].scrollHeight );
              } );
              jQuery( "#debugmanage" ).each(function( index ) { 
                jQuery(this).show();
	      });
              $(".mode-run").css("background-color", "red");
              jQuery('html, body').animate({ scrollTop: jQuery("#debugmanagewrap").offset().top }, 1000);
            } );
            jQuery(doclose).click(function(){
              jQuery.post(ajaxurl, docloseajax, function(response) {
              } );
              jQuery( "#debugmanage" ).each(function( index ) { 
                jQuery(this).hide();
	      });
              $(".mode-run").css("background-color", "#0085ba");
              jQuery('html, body').animate({ scrollTop: jQuery("#wpbody").position().top }, 1000);
            } );
            jQuery(doclear).click(function(){
              jQuery.post(ajaxurl, doclearajax, function(response) {
	        cdidebugarea.html( response );
	        cdidebugarea.scrollTop( cdidebugarea[0].scrollHeight );
              } );
            } );
            jQuery(dorefresh).click(function(){
              var dorefreshajax      = { 'action': 'cdi_debug_refresh_view', 'select': currentselect };
              jQuery.post(ajaxurl, dorefreshajax, function(response) {
	        cdidebugarea.html( response );
	        cdidebugarea.scrollTop( cdidebugarea[0].scrollHeight );
              } );
            } );
            jQuery(doselect).change(function(){
              currentselect = $(this).val() ;
              var dorefreshajax      = { 'action': 'cdi_debug_refresh_view', 'select': currentselect };
              jQuery.post(ajaxurl, dorefreshajax, function(response) {
	        cdidebugarea.html( response );
	        cdidebugarea.scrollTop( cdidebugarea[0].scrollHeight );
              } );
            } );
          });
        </script>
      <?php
      eval (WC_function_Colissimo::cdi_eval('12')) ; 
    }
    }
    public static function cdi_ajax_debug_open_view() {
      $cdi_save_init_set = get_option('cdi_save_init_set') ;
      if (!$cdi_save_init_set) {
        $cdi_save_init_set['error_reporting'] = ini_set( 'error_reporting', E_ALL ) ;
        $cdi_save_init_set['log_errors'] = ini_set( 'log_errors', 1 );
        $cdi_save_init_set['display_errors'] = ini_set( 'display_errors', 0 );
        $cdi_save_init_set['error_log'] = ini_set( 'error_log', WP_CONTENT_DIR . '/debug.log' );
        update_option('cdi_save_init_set', $cdi_save_init_set) ;
        WC_function_Colissimo::cdi_debug(__LINE__ ,__FILE__ , "Debug CDI ouvert !", 'msg');
      }
      self::cdi_ajax_debug_refresh_view() ;
      wp_die();
    }
    public static function cdi_ajax_debug_close_view() {
      $cdi_save_init_set = get_option('cdi_save_init_set') ;
      if ($cdi_save_init_set) {
        WC_function_Colissimo::cdi_debug(__LINE__ ,__FILE__ , "Debug CDI fermé !", 'msg');
        foreach($cdi_save_init_set as $name => $value){
          ini_set($name, $value);
        }
        delete_option('cdi_save_init_set') ;
      }
      wp_die();
    }
    public static function cdi_ajax_debug_clear_file() {
      file_put_contents( WP_CONTENT_DIR . '/debug.log', '' );
      WC_function_Colissimo::cdi_debug(__LINE__ ,__FILE__ , "Debug CDI purgé !", 'msg');
      _e( 'The debug.log has been cleared.', 'colissimo-delivery-integration' );
      wp_die();
    }
    public static function cdi_ajax_debug_refresh_view() {
      //WC_function_Colissimo::cdi_debug(__LINE__ ,__FILE__ , "Debug CDI rafraichi !", 'msg');
      if ( ! file_exists( WP_CONTENT_DIR . '/debug.log' ) ) {
        $file = fopen( WP_CONTENT_DIR . '/debug.log', 'w' ) ;
        if (!$file) {
          WC_function_Colissimo::cdi_debug(__LINE__ ,__FILE__ , 'Cannot create debug.log!', 'tec');
          die( 'Cannot create debug.log!' );
        }
        fwrite( $file, '' );
        fclose( $file );
      }
      $result = '' ;
      $sep = '$!$';
      if ( file_exists( WP_CONTENT_DIR . '/debug.log' ) ) {
        $file = @fopen( WP_CONTENT_DIR . '/debug.log', 'r');
	$entries = array();
        $currentline = '' ;
        while (($line = @fgets($file)) !== false) {
          $lineparts = preg_replace("/^\[([0-9a-zA-Z-]+) ([0-9:]+) ([a-zA-Z_\/]+)\] (.*)$/i", "$1".$sep."$2".$sep."$3".$sep."$4", $line);
          $parts = explode($sep, $lineparts);
          if (count($parts) >= 4) {
            if ($currentline !== '') {
              $entries[] = $currentline;
              $currentline = '' ;             
            }
            $currentline .= $line ;
          }else{
            $currentline .= $line ;
          }
        }
        if ($currentline !== '') {
          $entries[] = $currentline;
        }
        $select = $_POST['select'] ;
        if ($select == 'cdi') {
          $patternesearch = '*** LOG CDI' ;
        }elseif ($select == 'exp') {
          $patternesearch = '*** LOG CDI(exp)' ;
        }elseif ($select == 'tec') {
          $patternesearch = '*** LOG CDI(tec)' ;
        }elseif ($select == 'msg') {
          $patternesearch = '*** LOG CDI(msg)' ;
        }else{
          $patternesearch = ']' ;
        }
        foreach ($entries as $item) {
          if (strpos($item, $patternesearch)) {
            $result .= $item ;
          }
        }
        fclose( $file );
      }
      echo $result;
      wp_die();
    }











}
?>
