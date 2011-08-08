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

define('GLOBAL_IZAP_OPENLOGIN_FB_APPID', IzapBase::pluginSetting(array('name' => 'izap_fb_app_id','plugin' =>GLOBAL_IZAP_OPENLOGIN_PLUGIN)));//111450135615310
define('GLOBAL_IZAP_OPENLOGIN_FB_SECID', IzapBase::pluginSetting(array('name' => 'izap_fb_app_secid','plugin' => GLOBAL_IZAP_OPENLOGIN_PLUGIN)));//'a1b1444fd7410f3b4648ab76aac902ff');

function init_izap_open_login() {
  global $CONFIG;

  izap_plugin_init( GLOBAL_IZAP_OPENLOGIN_PLUGIN);
  elgg_register_page_handler(GLOBAL_IZAP_OPENLOGIN_PAGEHANDLER, GLOBAL_IZAP_PAGEHANDLER);
  if($_SESSION['LOGIN_VIA_FACEBOOK'])
  elgg_register_event_handler('logout', 'user', 'izap_facebook_logout');

  $CONFIG->IZAP_openid_providers = array(
          'google' => 'google.com/accounts/o8/id',
          'yahoo' => 'me.yahoo.com',
          'myopenid' => 'myopenid.com',
  );
  elgg_extend_view('login/extend',GLOBAL_IZAP_OPENLOGIN_PLUGIN.'/forms/login_form');

  $message = 'add_fb_id';
  elgg_add_admin_notice('add_fb_app_id', $message);
  if(GLOBAL_IZAP_OPENLOGIN_FB_APPID != '' && GLOBAL_IZAP_OPENLOGIN_FB_SECID != '' ){
    elgg_delete_admin_notice('add_fb_app_id');
  }
}

register_elgg_event_handler('init', 'system', 'init_izap_open_login');

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

  // pass along any messages
  $old_msg = $_SESSION['msg'];

  session_destroy();

  // starting a default session to store any post-logout messages.
  session_init(NULL, NULL, NULL);
  $_SESSION['msg'] = $old_msg;

  // send user to facebook for logout
  if (!isset($logout_url) || empty($logout_url)) {
    $facebook = new Facebook(array(
                'appId' => GLOBAL_IZAP_OPENLOGIN_FB_APPID,
                'secret' => GLOBAL_IZAP_OPENLOGIN_FB_SECID,
                'cookie' => true,
            ));
    $logout_url = $facebook->getLogoutUrl(array('next' => elgg_get_site_url() . GLOBAL_IZAP_OPENLOGIN_PAGEHANDLER . '/fblogout'));
  }
  header("Location: {$logout_url}");
  exit;
}
