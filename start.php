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

define('GLOBAL_IZAP_OPENLOGIN_PLUGIN', 'izap-open-login');
define('GLOBAL_IZAP_OPENLOGIN_PAGEHANDLER', 'userpoints');
define('GLOBAL_IZAP_OPENLOGIN_ACTIONHANDLER', 'izap_open_login');

function init_izap_open_login() {
  if(is_plugin_enabled('izap-elgg-bridge')) {
    func_init_plugin_byizap(array('plugin' => array('name' => GLOBAL_IZAP_OPENLOGIN_PLUGIN)));
  }else{
    register_error('This plugin needs izap-elgg-bridge');
    disable_plugin('izap-open-login');
  }
}

register_elgg_event_handler('init', 'system', 'init_izap_open_login');


