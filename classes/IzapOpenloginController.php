<?php

/* * ************************************************
 * PluginLotto.com                                 *
 * Copyrights (c) 2005-2010. iZAP                  *
 * All rights reserved                             *
 * **************************************************
 * @author iZAP Team "<support@izap.in>"
 * @link http://www.izap.in/
 * Under this agreement, No one has rights to sell this script further.
 * For more information. Contact "Tarun Jangra<tarun@izap.in>"
 * For discussion about corresponding plugins, visit http://www.pluginlotto.com/pg/forums/
 * Follow us on http://facebook.com/PluginLotto and http://twitter.com/PluginLotto
 */

class IzapOpenloginController extends IzapController {

  public function __construct($page) {
    parent::__construct($page);
  }

  private function actionAll() {
    IzapBase::loadlib(array(
                'plugin' => GLOBAL_IZAP_OPENLOGIN_PLUGIN,
                'lib' => 'izap-openid-login'
            ));
    $openid_mode = get_input('openid_mode', FALSE);
    $open_id = get_input('izap_id', FALSE);

    if (!$openid_mode) {
      if ($open_id) {
        $openid_url = func_izap_get_openid_url($open_id);

        $izapOpenLogin = new IzapOpenLogin;
        $izapOpenLogin->identity = $openid_url;
        $izapOpenLogin->required = array('contact/email');
        forward($izapOpenLogin->authUrl());
              }
    } elseif ($openid_mode == 'cancel') {
      register_error(elgg_echo('izap-open-login:auth_cancled'));
    } else {

      $izapOpenLogin = new IzapOpenLogin;
      if ($izapOpenLogin->validate()) {
        $attribs = $izapOpenLogin->getAttributes();
        if ($attribs['contact/email'] == '') {
          register_error(elgg_echo('izap-open-login:email_not_provided'));
          forward();
          exit;
        }
        $new_url = $CONFIG->wwwroot . 'openlogin/validate?open_id_identity=' . urlencode($izapOpenLogin->identity) . '&id_provider=' . $open_id . '&user_email=' . urlencode($attribs['contact/email']);
        forward($new_url);
        exit;
      } else {
        register_error(elgg_echo('izap-open-login:auth_not_validated'));
      }
    }
    forward();
    exit;
  }

  public function actionGoogle() {
    $this->actionAll();
  }

  public function actionYahoo() {
    $this->actionAll();
  }

  public function actionMyopenid() {
    $this->actionAll();
  }

  public function actionValidate() {
    $user_identity = get_input('open_id_identity');
    $id_provider = get_input('id_provider');
    $user_email = get_input('user_email', 'unknown@please_change_this.com');
    $user = get_user_by_email($user_email);

// if user exists then try to login
    if ($user[0] instanceof ElggUser) {
      if (login($user[0])) {
        $user->identity = $user_identity;
        system_message(elgg_echo('izap-open-login:user_loggedin'));
      } else {
        register_error(elgg_echo('izap-open-login:unable_to_login'));
      }
    } else { // if no user then register and login
      $email_array = explode('@', $user_email);
      $user_name = $email_array[0] . '_' . rand(time(), (time() + 1000));
      $name = $email_array[0];
      $user_pass = substr(md5($user_name . rand(1000, 10000)), 1, 8);
      try {
        $guid = register_user($user_name, $user_pass, $user_name, $user_email);
      } catch (Exception $e) {
        register_error(elgg_echo('izap-open-login:unable_to_register'));
      }
      $new_user = get_user($guid);
      if ($new_user) {
        set_user_validation_status($new_user->guid, TRUE, 'openid|' . $user_identity);
        set_user_notification_setting($new_user->getGUID(), 'site', true);
        if (login($new_user)) {
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
        } else {
          register_error(elgg_echo('izap-open-login:unable_to_login'));
        }
      }
    }
    forward();
  }

  public function actionFb() {
    global $CONFIG;
    $facebook = new Facebook(array(
                'appId' => GLOBAL_IZAP_OPENLOGIN_FB_APPID,
                'secret' => GLOBAL_IZAP_OPENLOGIN_FB_SECID,
                'cookie' => true,
            ));
    $fb_session = $facebook->getSession();
    if ($fb_session) {
      try {
        $me = $facebook->api('/me');
        $this->actionUser($me);
      } catch (FacebookApiException $e) {
        register_error($e->getMessage());
        $this->actionFblogout();
      }
    }
    else
      forward();
  }

  public function actionFblogout() {
    $facebook = new Facebook(array(
                'appId' => GLOBAL_IZAP_OPENLOGIN_FB_APPID,
                'secret' => GLOBAL_IZAP_OPENLOGIN_FB_SECID,
                'cookie' => true,
            ));
    $old_session = $facebook->getSession();
    $old_session['expires'] = time() - (86400 * 30);
    $facebook->setSession($old_session);
    forward();
    exit;
  }

  /**
   * logins or register & login the facebook user
   * @param array $fb_user facebook user array
   */
  public function actionUser($fb_user) {
    // start login and register
    //c($fb_user);exit;
    $user_name = $fb_user['id'];
    $user_email = ($fb_user['email']);
    $user_identity = 'facebook';

    $user = get_user_by_username($user_name);
    // if user exists then try to login
    if ($user instanceof ElggUser) {
      if (login($user)) {
        $guid = $user->guid;
        $_SESSION['LOGIN_VIA_FACEBOOK'] = 'YES';
        system_message(elgg_echo('login:ok'));
        forward();
        exit;
      } else {
        register_error(elgg_echo('login:baduser'));
      }
    } else { // if no user then register and login
      $user->{$user_identity . '_id'} = $fb_user['id'];
      $user->{$user_identity . '_username'} = $fb_user['username'];
      $name = $fb_user['name'];
      $user_pass = substr(md5($user_name . rand(1000, 10000)), 1, 8);
      try {
        $guid = register_user($user_name, $user_pass, $name, $user_email);
      } catch (Exception $e) {
        register_error(elgg_echo("registerbad") . '<br />' . $e->getMessage());
        forward();
        exit;
      }
      $new_user = get_user($guid);
      $new_user->briefdescription = $fb_user['bio'];

      // for the new user, get the profile picture
      if ($new_user) {
        $guid = $new_user->guid;
        $profile_icon_url = 'https://graph.facebook.com/' . $fb_user['id'] . '/picture?type=large';
        $content = file_get_contents($profile_icon_url);

        $file = IzapBase::saveImageFile(array(
                    'destination' => 'profile/' . $guid,
                    'content' => $content,
                    'owner_guid' => $guid,
                    'create_thumbs' => TRUE
                ));

        $new_user->icontime = time();

        set_user_validation_status($new_user->guid, TRUE, 'openid|' . $user_identity);
        set_user_notification_setting($new_user->guid, 'email', true);
        if ($_SESSION['fb_registraion']) {
          $subject = elgg_echo('useradd:subject');
          $body = elgg_echo('useradd:body', array(
                      $name,
                      elgg_get_site_entity()->name,
                      elgg_get_site_entity()->url,
                      $user_email,
                      $user_pass,
                  ));

          notify_user($new_user->guid, elgg_get_site_entity()->guid, $subject, $body);
          forward();
          exit;
        } else {
          if (login($new_user)) {
            $_SESSION['LOGIN_VIA_FACEBOOK'] = 'YES';
            system_message(elgg_echo('login:ok'));
            forward();
          } else {
            register_error(elgg_echo('login:baduser'));
          }
        }
      }
    }
    // end login/register
    // if reached here
    forward();
    exit;
  }

}