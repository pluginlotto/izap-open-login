<?php
/**************************************************
* PluginLotto.com                                 *
* Copyrights (c) 2005-2010. iZAP                  *
* All rights reserved                             *
***************************************************
* @author iZAP Team "<support@izap.in>"
* @link http://www.izap.in/
* @version {version} $Revision: {revision}
* Under this agreement, No one has rights to sell this script further.
* For more information. Contact "Tarun Jangra<tarun@izap.in>"
* For discussion about corresponding plugins, visit http://www.pluginlotto.com/pg/forums/
* Follow us on http://facebook.com/PluginLotto and http://twitter.com/PluginLotto
 */


function func_izap_enabled_openids() {
  global $CONFIG;

$array = $CONFIG->IZAP_openid_providers;
  if(sizeof($array)) {
    return $array;
  }

  return FALSE;
}

function func_izap_get_openid_url($openid_name) {
  $opeid_array = func_izap_enabled_openids();

  return $opeid_array[$openid_name];
}

function createname($identity,$id){

  return $username = substr(md5($identity.'_'.$id),0,10);

  }