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
 * For discussion about corresponding plugins, visit http://www.pluginlotto.com/pg/forums/
 * Follow us on http://facebook.com/PluginLotto and http://twitter.com/PluginLotto
 */
IzapBase::loadLib(array(
            'plugin' => GLOBAL_IZAP_OPENLOGIN_PLUGIN,
            'lib' => 'izap-openid-login',
        ));
$plugin_name = 'izap-open-login';

$openids = func_izap_enabled_openids();
$graphics_path = $vars['url'] . 'mod/' . GLOBAL_IZAP_OPENLOGIN_PLUGIN . '/_graphics/';


$facebook = new Facebook(array(
            'appId' => GLOBAL_IZAP_OPENLOGIN_FB_APPID,
            'secret' => GLOBAL_IZAP_OPENLOGIN_FB_SECID,
            'cookie' => true,
        ));

$fb_session = $facebook->getSession();
// Session based API call.
if ($fb_session) {
  try {
    $me = $facebook->api('/me');
    if (isset ($me['email'])) {
      forward(elgg_get_site_url() . GLOBAL_IZAP_OPENLOGIN_PAGEHANDLER . '/fb');
    }
  } catch (FacebookApiException $e) {
//    register_error($e->getMessage());
  }
}
?>
<h4>
  <?php echo elgg_echo('izap-open-login:login_with_openid'); ?>
</h4>
<?php

  foreach ($openids as $name => $url):

    $link = $vars['url'] . 'openlogin/' . $name . '?izap_id=' . $name;
?>
    <a href="<?php echo $link ?>" title="<?php echo sprintf(elgg_echo('izap-open-login:login_with'), $name); ?>">
      <img src="<?php echo $graphics_path . $name ?>_logo.png" alt="<?php echo sprintf(elgg_echo('izap-open-login:login_with'), $name); ?>" />
    </a><br/>
<?php
    endforeach;
?>



    <div id="fb-root"></div>
    <fb:login-button perms="email"></fb:login-button>
    <script type="text/javascript">
      window.fbAsyncInit = function() {
        FB.init({
          appId   : '<?php echo $facebook->getAppId() ?>',
      session : false, // don't refetch the session when PHP already has it
      status  : true, // check login status
      cookie  : true, // enable cookies to allow the server to access the session
      xfbml   : true // parse XFBML
    });

    // whenever the user logs in, we refresh the page
    FB.Event.subscribe('auth.login', function() {
      window.location.reload();
    });
  };

  (function() {
    var e = document.createElement('script');
    e.src = document.location.protocol + '//connect.facebook.net/en_US/all.js';
    e.async = true;
    document.getElementById('fb-root').appendChild(e);
  }());
</script>
