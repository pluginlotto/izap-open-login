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
IzapBase::loadLib(array(
          'plugin' => GLOBAL_IZAP_OPENLOGIN_PLUGIN,
          'lib' => 'izap-openid-login',
  ));
$plugin_name = 'izap-open-login';

$openids = func_izap_enabled_openids();
$graphics_path = $vars['url'] . 'mod/'.GLOBAL_IZAP_OPENLOGIN_PLUGIN.'/_graphics/';

?>
<h4>
  <?php echo elgg_echo('izap-open-login:login_with_openid');?>
</h4>
<?php
foreach($openids as $name => $url):

  $link = $vars['url'] . 'openlogin/' . $name.'?izap_id=' . $name;
?>
<a href="<?php echo $link?>" title="<?php echo sprintf(elgg_echo('izap-open-login:login_with'), $name);?>">
  <img src="<?php echo $graphics_path . $name?>_logo.png" alt="<?php echo sprintf(elgg_echo('izap-open-login:login_with'), $name);?>" />
</a>
<?php
endforeach;
?>
