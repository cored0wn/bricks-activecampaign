<?php
/**
 * Plugin Name: Bricks ActiveCampaign Newsletter
 * Description: Connects Bricks Builder Forms with ActiveCampaign for newsletter sign-ups.
 * Version:     1.0.0
 * Author:      wittor.IT
 * License:     AGPL-3.0-or-later
 * Text Domain: bricks-activecampaign
 */

defined( 'ABSPATH' ) || exit;

define( 'BRICKS_AC_VERSION', '1.0.0' );
define( 'BRICKS_AC_FILE', __FILE__ );

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
