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

$openid_mode = get_input('openid_mode', FALSE);
if(!$openid_mode) {
  $open_id = get_input('openid');
  $openid_url = func_izap_get_openid_url($open_id);

  $izapOpenLogin = new IzapOpenLogin();
  $izapOpenLogin->identity = $openid_url;
  forward($izapOpenLogin->authUrl());
}elseif($openid_mode == 'cancel') {
  register_error(elgg_echo('izap-open-login:auth_cancled'));
}else {
  $izapOpenLogin = new IzapOpenLogin();
  if($izapOpenLogin->validate()) {
    $new_url = elgg_add_action_tokens_to_url(func_get_actions_path_byizap(array('plugin' => 'izap-open-login')) . 'validate_login');
    forward($new_url . '&open_id_identity=' . urlencode($izapOpenLogin->identity));
  }else {
    register_error(elgg_echo('izap-open-login:auth_not_validated'));
  }
}
forward();
exit;
