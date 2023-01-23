<?php

if ( ! function_exists( "am_admin_notice_success" ) ) {
    function am_admin_notice_success( $error_message ) {
        ?>
        <div class="notice notice-success is-dismissible">
            <p><?= $error_message?></p>
        </div>
        <?php
    }
}

if ( ! function_exists( "am_admin_notice_error" ) ) {
    function am_admin_notice_error( $error_message ) {
        ?>
        <div class="notice notice-error is-dismissible">
            <p><?= $error_message ?></p>
        </div>
        <?php
    }
}

if ( ! function_exists( 'is_plugin_inactive' ) ) {
    include_once( ABSPATH . 'wp-admin/includes/plugin.php' );
}

if ( function_exists( "is_plugin_inactive" ) ) {
    if ( is_plugin_inactive( "woocommerce/woocommerce.php" ) ) {
        deactivate_plugins( "wp-plugin-login/index.php" );
        add_action( 'admin_notices', function() {
          global $_AM_MESSAGES;
          am_admin_notice_error( $_AM_MESSAGES["wc_must_activate"] );
        }, 99 );
    }
}

if ( ! function_exists( "random_verfication_code" ) ) {
  function random_verfication_code( $length = 6 ) {
    return substr( str_shuffle( "1234567890" ), 0, $length );
  }
}

add_filter( 'woocommerce_locate_template', 'load_wc_templates_from_plugin', 1, 3 );
function load_wc_templates_from_plugin( $template, $template_name, $template_path ) {
   global $woocommerce;
   $_template = $template;
     
   if ( ! $template_path ) $template_path = $woocommerce->template_url;

   $plugin_path = untrailingslashit( plugin_dir_path( __FILE__ ) )  . '/template/woocommerce/';
   $template = locate_template(array(
      $template_path . $template_name,
      $template_name
   ));
   if( ! $template && file_exists( $plugin_path . $template_name ) ) $template = $plugin_path . $template_name;
 
   if ( ! $template ) $template = $_template;

   return $template;
}

if ( ! function_exists( 'exists' ) ) {
  function exists( $data ) {
    return isset($data) && ! empty($data);
  }
}

if ( ! function_exists( "validate_mobile" ) ) {
  function validate_mobile(string $phone_number) {
    return (
      ! empty( $phone_number ) &&
      is_numeric( $phone_number ) &&
      strlen( $phone_number ) === 11 &&
      $phone_number[ 0 ] === "0" &&
      $phone_number[ 1 ] === "9"
    );
  }
}