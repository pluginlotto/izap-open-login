<?php
/**************************************************
* PluginLotto.com                                 *
* Copyrights (c) 2005-2010. iZAP                  *
* All rights reserved                             *
***************************************************
* @author iZAP Team "<support@izap.in>"
* @link http://www.izap.in/
* Under this agreement, No one has rights to sell this script further.
* For more information. Contact "Tarun Jangra<tarun@izap.in>"
* For discussion about corresponding plugins, visit http://www.pluginlotto.com/pg/forums/
* Follow us on http://facebook.com/PluginLotto and http://twitter.com/PluginLotto
 */


class IzapOpenloginController extends IzapController {
  public function  __construct($page) {
    parent::__construct($page);
  }

  private function actionAll(){
    IzapBase::loadlib(array(
            'plugin'=>GLOBAL_IZAP_OPENLOGIN_PLUGIN,
            'lib'=>'izap-openid-login'
    ));
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
        $new_url = $CONFIG->wwwroot . 'openlogin/validate?open_id_identity=' . urlencode($izapOpenLogin->identity) . '&id_provider=' . $open_id . '&user_email=' . urlencode($attribs['contact/email']);
        forward($new_url);
        exit;
      }else {
        register_error(elgg_echo('izap-open-login:auth_not_validated'));
      }
    }
    forward();
    exit;
  }

  public function actionGoogle() {
    $this->actionAll();
  }

  public function actionYahoo(){
    $this->actionAll();
  }

  public function actionMyopenid(){
    $this->actionAll();
  }

  public function actionValidate() {
    $user_identity = get_input('open_id_identity');
    $id_provider = get_input('id_provider');
    $user_email = get_input('user_email', 'unknown@please_change_this.com');
    $user = get_user_by_email($user_email);

// if user exists then try to login
    if($user[0] instanceof ElggUser) {
      if(login($user[0])) {
        $user->identity = $user_identity;
        system_message(elgg_echo('izap-open-login:user_loggedin'));
      }else {
        register_error(elgg_echo('izap-open-login:unable_to_login'));
      }
    }else { // if no user then register and login
      $email_array = explode('@', $user_email);
      $user_name = $email_array[0] . '_' . rand(time(), (time() + 1000));
      $name = $email_array[0];
      $user_pass = substr(md5($user_name . rand(1000, 10000)), 1, 8);
      try {
        $guid = register_user($user_name, $user_pass, $user_name, $user_email);
      }catch (Exception $e) {
        register_error(elgg_echo('izap-open-login:unable_to_register'));
      }
      $new_user = get_user($guid);
      if($new_user) {
        set_user_validation_status($new_user->guid, TRUE, 'openid|' . $user_identity);
        set_user_notification_setting($new_user->getGUID(), 'site', true);
        if(login($new_user)) {
          // send notificaiton for the newly created user
          $params['to'] = $new_user->email;
          $params['from_username'] = $CONFIG->site->name;
          $params['from'] = $CONFIG->site->email;
          $params['subject'] = elgg_echo('izap-open-login:notify:new_user:subject');
          $params['msg'] = sprintf(elgg_echo('izap-open-login:notify:new_user:message'), $user_name, $user_pass);
          //func_send_mail_byizap($params);

          $user->identity = $user_identity;
          system_message(elgg_echo('izap-open-login:user_loggedin'));
          forward($CONFIG->wwwroot . 'pg/settings/user/' . $new_user->username . '/');
        }else {
          register_error(elgg_echo('izap-open-login:unable_to_login'));
        }
      }
    }
    forward();
  }
}