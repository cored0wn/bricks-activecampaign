<?php
defined( 'ABSPATH' ) || exit;

/**
 * Thin wrapper around the ActiveCampaign form submission endpoint (proc.php).
 *
 * No API key required — proc.php is the same public endpoint that AC's own
 * embedded forms use. Lists, tags, double opt-in and automations are all
 * handled by the AC form configuration.
 */
class Bricks_AC_API {

    private string $api_url;

    public function __construct( string $api_url ) {
        $clean_url     = esc_url_raw( $api_url );
        $this->api_url = filter_var( $clean_url, FILTER_VALIDATE_URL ) ? rtrim( $clean_url, '/' ) : '';
    }

    // -------------------------------------------------------------------------
    // Form submission (proc.php)
    // -------------------------------------------------------------------------

    /**
     * Posts contact data to the AC form processor (proc.php), which behaves
     * identically to a native AC form submission.
     *
     * @param int   $form_id  The numeric AC form ID
     * @param array $fields   ['email' => ..., 'firstname' => ..., 'lastname' => ...]
     *
     * @return true|WP_Error
     */
    public function submit_form( int $form_id, array $fields ) {
        $proc_url = $this->get_proc_url();

        if ( ! $proc_url ) {
            return new WP_Error(
                'ac_not_configured',
                __( 'ActiveCampaign account URL is not configured. Set it under Bricks > Settings > API keys.', 'bricks-activecampaign' )
            );
        }

        $body = array_merge(
            [
                'u'   => $form_id,
                'f'   => $form_id,
                's'   => '',
                'c'   => '0',
                'm'   => '0',
                'act' => 'sub',
                'v'   => '2',
            ],
            array_filter( $fields, fn( $v ) => $v !== '' )
        );

        $response = wp_remote_post( $proc_url, [
            'body'        => $body,
            'timeout'     => 15,
            'redirection' => 5,
        ] );

        if ( is_wp_error( $response ) ) {
            return $response;
        }

        $status = wp_remote_retrieve_response_code( $response );

        if ( $status >= 400 ) {
            return new WP_Error( 'ac_form_error', "HTTP {$status}" );
        }

        return true;
    }

    // -------------------------------------------------------------------------
    // Helpers
    // -------------------------------------------------------------------------

    /**
     * Derives the proc.php URL from the stored API URL.
     * https://myaccount.api-us1.com  →  https://myaccount.activehosted.com/proc.php
     */
    private function get_proc_url(): string {
        if ( ! $this->api_url ) {
            return '';
        }

        $host    = (string) wp_parse_url( $this->api_url, PHP_URL_HOST );
        $account = strstr( $host, '.', true );

        return $account ? "https://{$account}.activehosted.com/proc.php" : '';
    }
}
