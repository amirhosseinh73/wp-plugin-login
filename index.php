<?php
/*
 Plugin Name: user authentication with mobile
 Plugin URI: 
 Description: change user authentiaction from email to mobile using sms.ir
 Author: amirhossein hassani
 Author URI: https://github.com/amirhoseinh73
 Version: 1.2.6
 WC requires at least: 5.5
 WC tested up to: 6.8
 Requires at least: 5.8
 Requires PHP: 7.2
 */

define( 'AHNJ_PLUGIN_FILE'       , __FILE__ );
define( 'AHNJ_PLUGIN_DIR_PATH'   , plugin_dir_path(__FILE__) );
define( 'AHNJ_PLUGIN_DIR_URL'    , plugin_dir_url(__FILE__) );

define( 'AHNJ_PLUGIN_PATH_ADMIN' , AHNJ_PLUGIN_DIR_PATH . 'admin/' );
define( 'AHNJ_PLUGIN_URL_ADMIN'  , AHNJ_PLUGIN_DIR_URL . 'admin/' );

require_once AHNJ_PLUGIN_DIR_PATH . "config.php";
require_once AHNJ_PLUGIN_DIR_PATH . "messages.php";

if ( is_admin() ) {
	require_once AHNJ_PLUGIN_PATH_ADMIN . 'admin.php';
}

require_once AHNJ_PLUGIN_DIR_PATH . "sms.ir-send.php";

require_once AHNJ_PLUGIN_DIR_PATH . "register-form.php";
require_once AHNJ_PLUGIN_DIR_PATH . "login-form.php";