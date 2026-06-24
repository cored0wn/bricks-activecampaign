<?php
defined( 'ABSPATH' ) || exit;

/**
 * Injects the ActiveCampaign API URL field into Bricks' own "API keys" settings tab.
 *
 * Only the account URL is needed — no API key required for proc.php form submission.
 *
 * Setting key stored in BRICKS_DB_GLOBAL_SETTINGS:
 *   apiUrlActiveCampaign  – the account API base URL (e.g. https://myaccount.api-us1.com)
 */
class Bricks_AC_Admin_Settings {

    public static function init(): void {
        add_action( 'admin_footer', [ __CLASS__, 'inject_settings_rows' ] );
    }

    // -------------------------------------------------------------------------
    // Inject row into Bricks' API keys tab
    // -------------------------------------------------------------------------

    public static function inject_settings_rows(): void {
        $screen = get_current_screen();

        if ( ! $screen || $screen->id !== 'bricks_page_bricks-settings' ) {
            return;
        }

        $api_url     = \Bricks\Database::get_setting( 'apiUrlActiveCampaign', '' );
        $api_url_esc = esc_attr( $api_url );

        $label_url = esc_html__( 'ActiveCampaign: Account URL', 'bricks-activecampaign' );
        $desc_url  = esc_html__( 'E.g. https://youraccount.api-us1.com — find it in AC under Settings > Developer. No API key needed.', 'bricks-activecampaign' );

        // language=HTML
        $rows = <<<HTML
<tr>
    <th><label for="apiUrlActiveCampaign">{$label_url}</label></th>
    <td>
        <input type="text" name="apiUrlActiveCampaign" id="apiUrlActiveCampaign"
               value="{$api_url_esc}" spellcheck="false" class="regular-text"
               placeholder="https://youraccount.api-us1.com">
        <p class="description">{$desc_url}</p>
    </td>
</tr>
HTML;

        $rows_json = wp_json_encode( $rows );
        ?>
        <script>
        (function () {
            var tbody = document.querySelector('#tab-api-keys tbody');
            if (!tbody) return;

            var tmp = document.createElement('tbody');
            tmp.innerHTML = <?php echo $rows_json; ?>;
            while (tmp.firstChild) tbody.appendChild(tmp.firstChild);
        })();
        </script>
        <?php
    }
}
