<?php

/* * ************************************************
 * PluginLotto.com                                 *
 * Copyrights (c) 2005-2010. iZAP                  *
 * All rights reserved                             *
 * **************************************************
 * @author iZAP Team "<support@izap.in>"
 * @link http://www.izap.in/
 * @version {version} $Revision: {revision}
 * Under this agreement, No one has rights to sell this script further.
 * For more information. Contact "Tarun Jangra<tarun@izap.in>"
 * For discussion about corresponding plugins, visit http://www.pluginlotto.com/forum/
 * Follow us on http://facebook.com/PluginLotto and http://twitter.com/PluginLotto
 *///PRINT_R($_SESSION);

include_once(dirname(dirname(__FILE__)) . '/izap-elgg-bridge/vendors/Facebook/SDK/facebook.php');
define('GLOBAL_IZAP_OPENLOGIN_PLUGIN', 'izap-open-login');
define('GLOBAL_IZAP_OPENLOGIN_PAGEHANDLER', 'openlogin');
define('GLOBAL_IZAP_OPENLOGIN_ACTIONHANDLER', 'izap_open_login');

// This will escape us from fatal error occurance when izap-bridge got deactivated after forum plugin activation
if (elgg_is_active_plugin(GLOBAL_IZAP_ELGG_BRIDGE)) {
  elgg_register_event_handler('init', 'system', 'init_izap_open_login');
} else {
  register_error('This plugin needs izap-elgg-bridge');
  disable_plugin(GLOBAL_IZAP_OPENLOGIN_PLUGIN);
}
define('GLOBAL_IZAP_OPENLOGIN_FB_APPID', IzapBase::pluginSetting(array('name' => 'izap_fb_app_id', 'plugin' => GLOBAL_IZAP_OPENLOGIN_PLUGIN))); //111450135615310
define('GLOBAL_IZAP_OPENLOGIN_FB_SECID', IzapBase::pluginSetting(array('name' => 'izap_fb_app_secid', 'plugin' => GLOBAL_IZAP_OPENLOGIN_PLUGIN))); //'a1b1444fd7410f3b4648ab76aac902ff');

function init_izap_open_login() {
  global $CONFIG;

  izap_plugin_init(GLOBAL_IZAP_OPENLOGIN_PLUGIN);
  elgg_register_page_handler(GLOBAL_IZAP_OPENLOGIN_PAGEHANDLER, GLOBAL_IZAP_PAGEHANDLER);
  if ($_SESSION['LOGIN_VIA_FACEBOOK'])
    elgg_register_event_handler('logout', 'user', 'izap_facebook_logout');

  $CONFIG->IZAP_openid_providers = array(
      'google' => 'google.com/accounts/o8/id',
      'yahoo' => 'me.yahoo.com',
      'myopenid' => 'myopenid.com',
  );
  elgg_extend_view('login/extend', GLOBAL_IZAP_OPENLOGIN_PLUGIN . '/forms/login_form');

  $message = elgg_echo('izap-openid-login:add_facebook_api');
  if (elgg_is_admin_logged_in()) {
    elgg_add_admin_notice('add_fb_app_id', $message);
    if (GLOBAL_IZAP_OPENLOGIN_FB_APPID != '' && GLOBAL_IZAP_OPENLOGIN_FB_SECID != '') {
      elgg_delete_admin_notice('add_fb_app_id');
    }
  }
}

function izap_facebook_logout() {
  // check the logout url for FB
  $logout_url = $_SESSION['FB_LOGOUT_URL'];

  // manually logout the user
  $_SESSION['user']->code = "";
  $_SESSION['user']->save();

  unset($_SESSION['username']);
  unset($_SESSION['name']);
  unset($_SESSION['code']);
  unset($_SESSION['guid']);
  unset($_SESSION['id']);
  unset($_SESSION['user']);

  setcookie("elggperm", "", (time() - (86400 * 30)), "/");
  if (!isset($logout_url) || empty($logout_url)) {
    $facebook = new Facebook(array(
                'appId' => GLOBAL_IZAP_OPENLOGIN_FB_APPID,
                'secret' => GLOBAL_IZAP_OPENLOGIN_FB_SECID
            ));
    $logout_url = $facebook->getLogoutUrl(array('next' => elgg_get_site_url() . GLOBAL_IZAP_OPENLOGIN_PAGEHANDLER . '/fblogout'));
  }
  header("Location: {$logout_url}");
  exit;
}
