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

include_once dirname(dirname(dirname(dirname(__FILE__)))) . '/engine/start.php';

$openid_mode = get_input('openid_mode', FALSE);
$open_id = get_input('izap_id', FALSE);

if(!$openid_mode) {
  if($open_id) {
    $openid_url = func_izap_get_openid_url($open_id);

    $izapOpenLogin = new IzapOpenLogin;
    $izapOpenLogin->identity = $openid_url;
    $izapOpenLogin->required = array('contact/email');
    forward($izapOpenLogin->authUrl());
  }
}elseif($openid_mode == 'cancel') {
  register_error(elgg_echo('izap-open-login:auth_cancled'));
}else {
  $izapOpenLogin = new IzapOpenLogin;
  if($izapOpenLogin->validate()) {
    $attribs= $izapOpenLogin->getAttributes();
    if($attribs['contact/email'] == '') {
      register_error(elgg_echo('izap-open-login:email_not_provided'));
      forward();
      exit;
    }
    $new_url = $CONFIG->wwwroot . 'mod/'.GLOBAL_IZAP_OPENLOGIN_PLUGIN.'/pages/validate_login.php?open_id_identity=' . urlencode($izapOpenLogin->identity) . '&id_provider=' . $open_id . '&user_email=' . urlencode($attribs['contact/email']);
    forward($new_url);
    exit;
  }else {
    register_error(elgg_echo('izap-open-login:auth_not_validated'));
  }
}
forward();
exit;
