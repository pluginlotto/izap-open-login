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
?>
<p>
  <label>
    <?php echo elgg_echo('izap-open-login:fb_appid');?>
    <br />
    <?php echo elgg_view('input/text', array(
    'internalname' => 'params[izap_fb_app_id]',
    'value' => IzapBase::pluginSetting(array(
        'name' => 'izap_fb_app_id',
        'plugin' => GLOBAL_IZAP_OPENLOGIN_PLUGIN,
        'value' => ''
    )),

    ));?>
  </label>
</p>

<p>
  <label>
    <?php echo elgg_echo('izap-open-login:fb_app_secid');?>
    <br />
    <?php echo elgg_view('input/text', array(
    'internalname' => 'params[izap_fb_app_secid]',
    'value' => IzapBase::pluginSetting(array(
        'name' => 'izap_fb_app_secid',
        'plugin' => GLOBAL_IZAP_OPENLOGIN_PLUGIN,
        'value' => ''
    )),

    ));?>
  </label>
</p>

