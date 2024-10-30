<?php
/*
  Plugin Name: Checks for Administrators
  Description: Checks for Administrators is an open source solution built for Wordpress to display several check lists puts for the administrator.
  Version: 1.0.0
  Author: Javier Glez
  Author URI: http://javierglez.es/
  Plugin URI: http://javierglez.es/
 */

// Definición de Constantes
if ( ! defined( 'CA_PLUGIN' ) )
	define( 'CA_PLUGIN', plugin_basename(__FILE__));

if ( ! defined( 'CA_CHECKS_URL' ) )
	define( 'CA_CHECKS_URL', plugins_url( 'img/checks/' , __FILE__) );

if ( ! defined( 'CA_MAX_DESC' ) )
	define( 'CA_MAX_DESC', 40);

//
$aChecks = array();
require_once 'inc/checks.php';

function caMenuPlugin(){
   add_options_page( __("Checks Settings", 'checks-for-admin'), __("Checks for Admin", 'checks-for-admin'), 10, "checks_settings", "caShowSettings");
}

function caShowSettings(){
    if (!current_user_can('manage_options')){
        wp_die( __('Small padawan ... you must use the force to enter here.', 'checks-for-admin') );
    }
    
    global $aChecks;
 
    // Guardado de los datos
    if( isset($_POST[ "ca_submit" ])) { caSave(); }
    
    // Inicialización de los Valores
    $opt_ca_descChecked = get_option("ca_descChecked", "");
    $opt_ca_descUnchecked = get_option("ca_descUnchecked", "");
    $opt_ca_type = get_option("ca_type", "NCP");
    
    // Visualización de la parte del Administrador
    ?>
        <div class="wrap">
            <div style="width: 70%; float: left;">
                <div class="icon32" id="icon-options-general"><br></div>
                <h2><?php _e( 'Checks for Administrators', 'checks-for-admin' ); ?></h2>
                <form name="ca_form" method="post" action="">
                    <table class="form-table">
                        <tbody>
                            <tr valign="top">
                                <th scope="row"><label for="ca_descChecked"><?php _e( 'Description when is checked', 'checks-for-admin' ); ?></label></th>
                                <td>
                                    <fieldset>
                                        <legend class="screen-reader-text"><span><?php _e( 'Description when is checked', 'checks-for-admin' ); ?></span></legend>
                                        <label for="ca_descChecked"><input type="text" value="<?php echo $opt_ca_descChecked; ?>" name="ca_descChecked" id="ca_descChecked" size="55" maxlength="<?php echo CA_MAX_DESC; ?>" /></label>
                                    </fieldset>
                                </td>
                            </tr>
                            <tr valign="top">
                                <th scope="row"><label for="ca_descUnchecked"><?php _e( 'Description when is unchecked', 'checks-for-admin' ); ?></label></th>
                                <td>
                                    <fieldset>
                                        <legend class="screen-reader-text"><span><?php _e( 'Description when is unchecked', 'checks-for-admin' ); ?></span></legend>
                                        <label for="ca_descUnchecked"><input type="text" value="<?php echo $opt_ca_descUnchecked; ?>" name="ca_descUnchecked" id="ca_descUnchecked" size="55" maxlength="<?php echo CA_MAX_DESC; ?>" /></label>
                                    </fieldset>
                                </td>
                            </tr>
                            <tr valign="top">
                                <th scope="row"><?php _e('Type of checks', 'checks-for-admin' ); ?></th>
                                <td>
                                    <fieldset>
                                        <legend class="screen-reader-text"><span><?php _e('Type of checks', 'checks-for-admin'); ?></span></legend>
                                        <?php
                                            for($i=0; $i < count($aChecks); $i++) {
                                              ?>
                                                <label title="<?php echo $aChecks[$i]['name']; ?>">
                                                    <input type="radio" name="ca_type" value="<?php echo $aChecks[$i]['key'] ?>" style="vertical-align: top;" <?php if($opt_ca_type == $aChecks[$i]['key']){ echo "checked "; }?>/>
                                                    <div style="display: inline-block;">
                                                        <img src="<?php echo CA_CHECKS_URL . $aChecks[$i]['images']['c'] ?>" alt="<?php _e('Check', 'checks-for-admin'); ?>" width="<?php echo $aChecks[$i]["size"]["w"]; ?>" height="<?php echo $aChecks[$i]["size"]["h"]; ?>" />
                                                        <img src="<?php echo CA_CHECKS_URL . $aChecks[$i]['images']['u'] ?>" alt="<?php _e('Uncheck', 'checks-for-admin'); ?>" width="<?php echo $aChecks[$i]["size"]["w"]; ?>" height="<?php echo $aChecks[$i]["size"]["h"]; ?>" />
                                                        <span class="description">(<?php echo __("Type: ", "checks-for-admin") . $aChecks[$i]["key"]; ?>, <?php echo __("Size: ", "checks-for-admin") . $aChecks[$i]["size"]["w"] . 'x' . $aChecks[$i]["size"]["h"] . ' px'; ?>)</span>
                                                    </div>
                                                </label>
                                                <div style="margin-bottom: 5px;"></div>
                                                <?php
                                            }
                                        ?>
                                    </fieldset>
                                </td>
                            </tr>
                        </tbody>
                    </table>

                    <p class="submit"><input type="submit" value="<?php esc_attr_e('Save Changes') ?>" class="button button-primary" id="submit" name="ca_submit"></p>
                </form>
            </div>

            <div id="poststuff" class="metabox-holder has-right-sidebar" style="float: right; width: 24%; margin: 38px 10px 0px 0px;"> 
                <div class="postbox" id="sm_pnres">
                    <h3 class="hndle"><span><?php _e('How to use', 'checks-for-admin' ); ?></span></h3>
                    <div class="inside">
                      <?php _e("<p align=\"justify\">To display the checks in the post, you have to write the following pattern directly on the content:</p><center><b style=\"font-size:17px;\">[CA value=\"x\" OPTIONS]</b></center><p align=\"justify\">where \"x\" represents 1 for check or 0 for uncheck. For example: <b>[CA value=\"1\"]</b>.</p>", "checks-for-admin"); ?>
                      <?php _e("<h6 style=\"font-size:1.2em; margin-bottom:0px; text-align:center;\">Options</h6><ul style=\"text-indent:10px; margin-top:5px;\"><li><b>type</b>: Represents a specific type of check (e.g.: [CA value=\"1\" type=\"3CP\"]).</li><li><b>description</b>: Represents the description of the check. The value can not be more than 40 caracters (e.g.: [CA value=\"1\" description=\"We visited this place.\"]).</li></ul>", "checks-for-admin"); ?>
                    </div>
                </div>
                <div class="postbox" id="sm_pnres">
                    <h3 class="hndle"><span><?php _e('About this plugin', 'checks-for-admin' ); ?></span></h3>
                    <div class="inside">
                        <a href="http://www.javierglez.es" class="sm_button sm_pluginHome"><?php _e('PlugIn Web Site', 'checks-for-admin' ); ?></a>
                        <br />
                        <a href="http://www.javierglez.es" class="sm_button sm_pluginHome"><?php _e('Request new features', 'checks-for-admin' ); ?></a>
                        <br />
                        <a href="http://www.javierglez.es" class="sm_button sm_pluginList"><?php _e('Report a error', 'checks-for-admin' ); ?></a>
                    </div>
                </div>
            </div>
        </div>
    <?
}

/**
 * Devuelve un array con el Tipo de Estrella Necesario
 * @param String $sType Tipo de Estrella elegido para que te devuelva
 * @return Array Array con los contenidos de ese tipo (Key, Nombre, Descripción, Imagenes)
*/
function caCheckSearch ($sType="OS") {
  global $aChecks;
  $aReturn = array();
  
  foreach ($aChecks as $sKey => $aItem) {
    if ($aItem['key'] === $sType) {
      $aReturn = $aItem;
      break;
    }
  }
  return $aReturn;
}

/**
 * Guarda las configuraciones Necesarias
*/
function caSave () {
    $aError = array();

    if(isset($_POST["ca_descChecked"]) && caCheck("ca_descChecked" , $_POST["ca_descChecked"])) {
        update_option("ca_descChecked", $_POST["ca_descChecked"]);
    } else { $aError["ca_descChecked"] = "Error al guardar la ca_descChecked"; }
    
    if(isset($_POST["ca_descUnchecked"]) && caCheck("ca_descUnchecked" , $_POST["ca_descUnchecked"])) {
        update_option("ca_descUnchecked", $_POST["ca_descUnchecked"]);
    } else { $aError["ca_descUnchecked"] = "Error al guardar la ca_descUnchecked"; }
    
    if(isset($_POST["ca_type"]) && caCheck("ca_type" , $_POST["ca_type"])) {
        update_option("ca_type", $_POST["ca_type"]);
    } else { $aError["ca_type"] = "Error al guardar el ca_type"; }
    
    if(empty($aError)) {
        ?><div class="updated"><p><strong><?php _e('Settings saved.', 'checks-for-admin' ); ?></strong></p></div><?php
    } else {
        ?><div class="error"><p><strong><?php _e('Settings NOT saved. There are error(s).', 'checks-for-admin' ); ?></strong></p></div><?php
    }
}

/**
 * Checkea los campos venidos por POST, para ver si estan dentro de los parámetros
 * @param String $sField Nombre del campo que tiene que checkear
 * @param String $sValue Valor del campo que tiene que checkear
*/
function caCheck ($sField, $sValue) {
    $bResult = true;

    switch ($sField):
        case "ca_descChecked":
            if(strlen($sValue) > CA_MAX_DESC) { $bResult = false; }
            break;
        case "ca_descUnchecked":
            if(strlen($sValue) > CA_MAX_DESC) { $bResult = false; }
            break;
        case "ca_type":
            $aux = caCheckSearch($sValue);
            if(empty($aux)) { $bResult = false; }
            break;
        case "ca_value":
            if($sValue != "0" && $sValue != "1") { $bResult = false; }
            break;
        
        default :
            break;
    endswitch;

    return $bResult;
}

function caShortcode ($atts, $sContent=NULL) {
    $aOptCA = array('type'=>'NCP', 'descChecked'=>'', 'descUnchecked'=>'');
    $sValue = '0';
    
    // Recuperar las opciones Guardadas
    $aOptCA['type'] = get_option("ca_type", $aOptCA['type']);
    $aOptCA['descChecked'] = get_option("ca_descChecked", $aOptCA['descChecked']);
    $aOptCA['descUnchecked'] = get_option("ca_descUnchecked", $aOptCA['descUnchecked']);
    
    // Recuperar datos del ShorCode (Valor, Tipo, etc)
    $aShortCodeValues = shortcode_atts( array(
        'value' => $sValue,
        'type' => $aOptCA['type'],
        'description' => '',
        ), $atts);
    $aOptCA['type'] = (caCheck("ca_type" , $aShortCodeValues['type'])) ? $aShortCodeValues['type'] : $aOptCA['type'];
    $aType = caCheckSearch($aOptCA['type']);
    $aOptCA['descChecked'] = ($aShortCodeValues['description'] != '') ? substr($aShortCodeValues['description'], 0, CA_MAX_DESC) : $aOptCA['descChecked'];
    $aOptCA['descUnchecked'] = ($aShortCodeValues['description'] != '') ? substr($aShortCodeValues['description'], 0, CA_MAX_DESC) : $aOptCA['descUnchecked'];
    $sValue = (!caCheck("ca_value" , $aShortCodeValues['value'])) ? $sValue : $aShortCodeValues['value'];
    
    // Componer los Checks
    $sCheckType = (empty($sValue)) ? 'u' : 'c';
    $sCheckDesc = (empty($sValue)) ? $aOptCA['descUnchecked'] : $aOptCA['descChecked'];
    $sChecks = '<img src="' . CA_CHECKS_URL . $aType['images'][$sCheckType] . '" width="' . $aType["size"]["w"] . '" height="' . $aType["size"]["h"] . '" />';

    // Componer el Contenido y Devolverlo
    $sContent .= '<label title="' . $sCheckDesc . '">' . $sChecks . '</label>';
    return $sContent;
}

// Añadir el enlace de "Ajustes" al panel de administración de los PlugIns
function caSettingsLink($links) { 
    $settings_link = '<a href="options-general.php?page=checks_settings">' . __('Settings') . '</a>'; 
    array_push($links, $settings_link); 
    return $links; 
}

// Cargar funciones Iniciales
function caInit() {
    // Carga los idiomas
    load_plugin_textdomain( 'checks-for-admin', false, dirname( plugin_basename( __FILE__ ) ) . '/lang/' );
}
 
// Inicializar en Admin
add_action( 'init', 'caInit' );

// Para añadir el Menu
add_action("admin_menu","caMenuPlugin");

// Para añadir un manejador de Codigos Cortos (del estilo a "[CA x.x]")
add_shortcode('CA', 'caShortcode');

// Añadir el enlace de "Ajustes" al panel de administración de los PlugIns
add_filter("plugin_action_links_".CA_PLUGIN, 'caSettingsLink' );
