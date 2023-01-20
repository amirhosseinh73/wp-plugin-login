<?php

function random_verficiaion_code( $length = 6 ) {
	return substr( str_shuffle( "1234567890" ), 0, $length );
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