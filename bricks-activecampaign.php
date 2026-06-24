<?php
/**
 * Plugin Name: Bricks ActiveCampaign Newsletter
 * Description: Verbindet Bricks Builder Forms mit ActiveCampaign für Newsletter-Anmeldungen.
 * Version:     1.0.0
 * Author:      wittor.IT
 * License:     GPL-2.0+
 * Text Domain: bricks-activecampaign
 */

defined( 'ABSPATH' ) || exit;

define( 'BRICKS_AC_VERSION', '1.0.0' );
define( 'BRICKS_AC_FILE', __FILE__ );

add_action( 'init', function () {
    load_plugin_textdomain(
        'bricks-activecampaign',
        false,
        dirname( plugin_basename( BRICKS_AC_FILE ) ) . '/languages'
    );
}, 1 );

require_once __DIR__ . '/includes/class-ac-api.php';
require_once __DIR__ . '/includes/class-admin-settings.php';
require_once __DIR__ . '/includes/class-bricks-controls.php';
require_once __DIR__ . '/includes/class-form-handler.php';

add_action( 'init', function () {
    if ( ! defined( 'BRICKS_VERSION' ) ) {
        return;
    }

    Bricks_AC_Admin_Settings::init();
    Bricks_AC_Controls::init();
    Bricks_AC_Form_Handler::init();
}, 20 );
