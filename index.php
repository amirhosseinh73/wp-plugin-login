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

define( 'AMHNJ_PLUGIN_FILE'       , __FILE__ );
define( 'AMHNJ_PLUGIN_DIR_PATH'   , plugin_dir_path(__FILE__) );
define( 'AMHNJ_PLUGIN_DIR_URL'    , plugin_dir_url(__FILE__) );

define( 'AMHNJ_PLUGIN_PATH_ADMIN' , AMHNJ_PLUGIN_DIR_PATH . 'admin/' );
define( 'AMHNJ_PLUGIN_URL_ADMIN'  , AMHNJ_PLUGIN_DIR_URL . 'admin/' );

if ( ! function_exists( "amhnj_admin_notice__success" ) ) {
    function amhnj_admin_notice__success( $error_message ) {
        ?>
        <div class="notice notice-success is-dismissible">
            <p><?php __( $error_message ); ?></p>
        </div>
        <?php
    }
}

if ( ! function_exists( "amhnj_admin_notice__error" ) ) {
    function amhnj_admin_notice__error( $error_message ) {
        ?>
        <div class="notice notice-warning is-dismissible">
            <p><?php __( $error_message ); ?></p>
        </div>
        <?php
    }
}

if ( ! function_exists( 'is_plugin_inactive' ) ) {
    include_once( ABSPATH . 'wp-admin/includes/plugin.php' );
}

if ( function_exists( "is_plugin_inactive" ) ) {
    if ( is_plugin_inactive( "woocommerce/woocommerce.php" ) ) {
        $error_message = "افزونه ووکامرس باید فعال باشد!";
        add_action( 'admin_notices', function() use( $error_message ) {
            amhnj_admin_notice__error( $error_message );
        }, 10, 1 );
    }
}

if ( is_admin() ) {
	require_once AMHNJ_PLUGIN_PATH_ADMIN . 'admin.php';
}

require_once AMHNJ_PLUGIN_DIR_PATH . "functions.php";
require_once AMHNJ_PLUGIN_DIR_PATH . "sms.ir-send.php";

require_once AMHNJ_PLUGIN_DIR_PATH . "register-form.php";
add_shortcode( 'amhnj_register_form', 'register_form_shortcode' );
function register_form_shortcode() {
    ob_start();
    do_register_form();
    return ob_get_clean();
}

require_once AMHNJ_PLUGIN_DIR_PATH . "login-form.php";
add_shortcode( 'amhnj_login_form', 'login_form_shortcode' );
function login_form_shortcode() {
    ob_start();
    do_login_form();
    return ob_get_clean();
}