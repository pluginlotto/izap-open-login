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

return array(

        'includes' => array(
                dirname(__FILE__) . '/classes' => array('class.IzapOpenLogin.php'),
                dirname(__FILE__) . '/functions' => array('func.core.php'),
        ),

        'plugin' => array(

                'custom' => array(
                        'openid_providers' => array(
                                'google' => 'google.com/accounts/o8/id',
                                'yahoo' => 'flickr.com',
                                'myopenid' => 'myopenid.com',

                        ),
                ),

                'name' => GLOBAL_IZAP_OPENLOGIN_PLUGIN,

                'url_title' => GLOBAL_IZAP_OPENLOGIN_PAGEHANDLER,

                'actions' => array(
                        GLOBAL_IZAP_OPENLOGIN_ACTIONHANDLER . '/send_login' => array(
                                'file' => 'send_login.php',
                                'public' => TRUE,
                        ),

                        GLOBAL_IZAP_OPENLOGIN_ACTIONHANDLER . '/validate_login' => array(
                                'file' => 'validate_login.php',
                                'public' => TRUE,
                        ),

                ),

                'extend' => array(
                        'account/forms/login' => array(
                                'izap-open-login/forms/login_form' => array(
                                        'priority' => 500
                                ),
                        ),
                ),

        ),

        'path' => array(

                'www' => array(
                        'page' => $CONFIG->wwwroot . 'pg/'.GLOBAL_IZAP_OPENLOGIN_PAGEHANDLER.'/',
                        'images' => $CONFIG->wwwroot . 'mod/'.GLOBAL_IZAP_OPENLOGIN_PLUGIN.'/_graphics/',
                        'action' => $CONFIG->wwwroot . 'action/' . GLOBAL_IZAP_OPENLOGIN_ACTIONHANDLER . '/',
                ),

                'dir' => array(
                        'plugin' => dirname(dirname(__FILE__)) . '/',
                        'actions' => dirname(dirname(__FILE__)) . '/actions/',
                        'class' => dirname(__FILE__) . '/classes/',
                        'functions' => dirname(__FILE__) . '/functions/',
                        'lib' => dirname(__FILE__) . '/',
                        'vendors' => dirname(dirname(__FILE__)) . '/vendors/',
                        'views' => array(
                                'home' => 'izap-open-login/',
                                'forms' => 'izap-open-login/forms/',
                        ),
                ),
        ),

);

