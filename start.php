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
define('GLOBAL_IZAP_OPENLOGIN_PAGEHANDLER', 'openlogin');
define('GLOBAL_IZAP_OPENLOGIN_ACTIONHANDLER', 'izap_open_login');

function init_izap_open_login() {
  global $CONFIG;

  izap_plugin_init( GLOBAL_IZAP_OPENLOGIN_PLUGIN);
  elgg_register_page_handler(GLOBAL_IZAP_OPENLOGIN_PAGEHANDLER, GLOBAL_IZAP_PAGEHANDLER);

  $CONFIG->IZAP_openid_providers = array(
          'google' => 'google.com/accounts/o8/id',
          'yahoo' => 'flickr.com',
          'myopenid' => 'myopenid.com',
  );
  elgg_extend_view('forms/login',GLOBAL_IZAP_OPENLOGIN_PLUGIN.'/forms/login_form');
}

register_elgg_event_handler('init', 'system', 'init_izap_open_login');


