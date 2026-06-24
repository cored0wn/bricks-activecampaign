<?php
defined( 'ABSPATH' ) || exit;

/**
 * Handles Bricks form submissions for the "activecampaign" action.
 *
 * Submits the contact data directly to the AC form processor (proc.php),
 * which triggers the exact same flow as a native AC form: double opt-in,
 * automations, list/tag assignments — all configured in AC itself.
 *
 * Error details are only shown to logged-in admins; all other users receive
 * a generic message.
 */
class Bricks_AC_Form_Handler {

    public static function init(): void {
        add_action( 'bricks/form/action/activecampaign', [ __CLASS__, 'handle' ] );
    }

    public static function handle( $form ): void {
        $settings = $form->get_settings();
        $fields   = $form->get_fields();

        // --- Per-form settings (admin-controlled) ---

        $form_id       = absint( $settings['acFormId'] ?? 0 );
        $email_key     = 'form-field-' . sanitize_key( $settings['acEmailField']     ?? '' );
        $firstname_key = 'form-field-' . sanitize_key( $settings['acFirstNameField'] ?? '' );
        $lastname_key  = 'form-field-' . sanitize_key( $settings['acLastNameField']  ?? '' );
        $success_msg   = sanitize_text_field(
            $settings['acSuccessMsg']
            ?? __( 'Thank you! You have been successfully subscribed.', 'bricks-activecampaign' )
        );

        // --- Validate form ID ---

        if ( $form_id < 1 ) {
            $form->set_result( [
                'action'  => 'activecampaign',
                'type'    => 'error',
                'message' => esc_html__( 'Your subscription could not be processed. Please try again later.', 'bricks-activecampaign' ),
            ] );
            self::log( 'handle', 'No AC Form ID configured.' );
            return;
        }

        // --- Sanitize user-submitted values ---

        $email     = isset( $fields[ $email_key ] )     ? sanitize_email( $fields[ $email_key ] )         : '';
        $firstname = isset( $fields[ $firstname_key ] ) ? sanitize_text_field( $fields[ $firstname_key ] ) : '';
        $lastname  = isset( $fields[ $lastname_key ] )  ? sanitize_text_field( $fields[ $lastname_key ] )  : '';

        if ( ! is_email( $email ) ) {
            $form->set_result( [
                'action'  => 'activecampaign',
                'type'    => 'error',
                'message' => esc_html__( 'Please provide a valid email address.', 'bricks-activecampaign' ),
            ] );
            return;
        }

        // --- Submit to AC form processor ---

        $api    = new Bricks_AC_API(
            \Bricks\Database::get_setting( 'apiUrlActiveCampaign', '' )
        );
        $result = $api->submit_form( $form_id, [
            'email'     => $email,
            'firstname' => $firstname,
            'lastname'  => $lastname,
        ] );

        if ( is_wp_error( $result ) ) {
            self::log( 'submit_form', $result->get_error_message() );
            $form->set_result( [
                'action'  => 'activecampaign',
                'type'    => 'error',
                'message' => self::user_error_message( $result ),
            ] );
            return;
        }

        $form->set_result( [
            'action'  => 'activecampaign',
            'type'    => 'success',
            'message' => esc_html( $success_msg ),
        ] );
    }

    // -------------------------------------------------------------------------
    // Helpers
    // -------------------------------------------------------------------------

    /**
     * Admins see the actual API error; all other users get a generic message.
     */
    private static function user_error_message( WP_Error $error ): string {
        if ( current_user_can( 'manage_options' ) ) {
            return esc_html( sprintf(
                /* translators: %s: technical error detail shown to admins only */
                __( 'ActiveCampaign error (admin only): %s', 'bricks-activecampaign' ),
                $error->get_error_message()
            ) );
        }

        return esc_html__( 'Your subscription could not be processed. Please try again later.', 'bricks-activecampaign' );
    }

    private static function log( string $context, string $message ): void {
        if ( defined( 'WP_DEBUG_LOG' ) && WP_DEBUG_LOG ) {
            // phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_error_log
            error_log( "[Bricks ActiveCampaign] {$context}: {$message}" );
        }
    }
}
