<?php
/*
Plugin Name: Paid Memberships Pro - Generic Payment Gateway Add On
Plugin URI: https://brasdrive.com.br/add-ons/pmpro-generic-gateway-add-on/
Description: A collection of customizations useful when allowing users to pay by check for Paid Memberships Pro levels.
Version: 1.1.3
Author: David Camejo
Author URI: https://brasdrive.com.br
Text Domain: pmpro-generic-gateway
Domain Path: /languages
*/
/*
        Use case: To be used as any other PMPro payment gateways.

        1. Change your Payment Settings to the "Generic Payment Gateway" gateway and make sure to set the "Instructions" with instructions for how to "pay" with it. Save.
        2. Change the Payment Settings back to use your gateway of choice. Behind the scenes the Generic Payment Gateway settings are still stored.

        * Users who choose to "pay" with the Generic Payment Gateway will have no changes, this is jut a template plugin.
*/

/*
        Settings, Globals and Constants
*/
define( 'PMPRO_GENERIC_GATEWAY_DIR', dirname(__FILE__) );
define( 'PMPRO_GENERIC_GATEWAY_BASE_FILE', __FILE__ );
define( 'PMPROGPG_VER', '1.0' );

require_once PMPRO_GENERIC_GATEWAY_DIR . '/includes/admin.php';
require_once PMPRO_GENERIC_GATEWAY_DIR . '/includes/checkout.php';
require_once PMPRO_GENERIC_GATEWAY_DIR . '/includes/crons.php';
require_once PMPRO_GENERIC_GATEWAY_DIR . '/includes/emails.php';
require_once PMPRO_GENERIC_GATEWAY_DIR . '/includes/frontend.php';
require_once PMPRO_GENERIC_GATEWAY_DIR . '/includes/functions.php';
require_once PMPRO_GENERIC_GATEWAY_DIR . '/includes/member-pending-deprecated.php';

/*
        Load plugin textdomain.
*/
function pmprogpg_load_textdomain() {
  load_plugin_textdomain( 'pmpro-generic-gateway', false, plugin_basename( dirname( __FILE__ ) ) . '/languages' );
}
add_action( 'plugins_loaded', 'pmprogpg_load_textdomain' );

/*
Function to add links to the plugin row meta
*/
function pmprogpg_plugin_row_meta($links, $file) {
        if(strpos($file, 'pmpro-generic-gateway.php') !== false)
        {
                $new_links = array(
                        '<a href="' . esc_url('https://brasdrive.com.br/add-ons/pmpro-generic-gateway-add-on/')  . '" title="' . esc_attr( __( 'View Documentation', 'pmpro-generic-gateway' ) ) . '">' . __( 'Docs', 'pmpro-generic-gateway' ) . '</a>',
                        '<a href="' . esc_url('httsp://brasdrive.com.br/support/') . '" title="' . esc_attr( __( 'Visit Customer Support Forum', 'pmpro-generic-gateway' ) ) . '">' . __( 'Support', 'pmpro-generic-gateway' ) . '</a>',
                );
                $links = array_merge($links, $new_links);
        }
        return $links;
}
add_filter('plugin_row_meta', 'pmprogpg_plugin_row_meta', 10, 2);
