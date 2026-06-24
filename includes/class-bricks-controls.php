<?php
defined( 'ABSPATH' ) || exit;

/**
 * Registers the "ActiveCampaign Newsletter" action inside the Bricks form
 * element and adds all per-form configuration controls to the builder sidebar.
 */
class Bricks_AC_Controls {

    public static function init(): void {
        add_filter( 'bricks/elements/form/control_groups', [ __CLASS__, 'add_control_group' ] );
        add_filter( 'bricks/elements/form/controls',       [ __CLASS__, 'add_controls' ] );
    }

    public static function add_control_group( array $groups ): array {
        $groups['activecampaign'] = [
            'title'    => esc_html__( 'ActiveCampaign Newsletter', 'bricks-activecampaign' ),
            'required' => [ 'actions', '=', 'activecampaign' ],
        ];

        return $groups;
    }

    public static function add_controls( array $controls ): array {
        $controls['actions']['options']['activecampaign'] = esc_html__( 'ActiveCampaign Newsletter', 'bricks-activecampaign' );

        // AC form ID — all lists, tags, automations and DOI are configured in AC itself
        $controls['acFormId'] = [
            'group'       => 'activecampaign',
            'label'       => esc_html__( 'AC Form ID', 'bricks-activecampaign' ),
            'type'        => 'text',
            'placeholder' => '1',
            'description' => esc_html__( 'The numeric ID of your ActiveCampaign form. Find it in AC under Forms > [Form] > Integrate > embed code (the number after "_form_"). Lists, tags, double opt-in and automations are handled entirely by the AC form settings.', 'bricks-activecampaign' ),
            'required'    => [ 'actions', '=', 'activecampaign' ],
        ];

        // Field mapping
        $controls['acEmailField'] = [
            'group'      => 'activecampaign',
            'label'      => esc_html__( 'Email field', 'bricks-activecampaign' ),
            'type'       => 'select',
            'map_fields' => true,
            'required'   => [ 'actions', '=', 'activecampaign' ],
        ];

        $controls['acFirstNameField'] = [
            'group'       => 'activecampaign',
            'label'       => esc_html__( 'First name field (optional)', 'bricks-activecampaign' ),
            'type'        => 'select',
            'map_fields'  => true,
            'placeholder' => esc_html__( '– none –', 'bricks-activecampaign' ),
            'required'    => [ 'actions', '=', 'activecampaign' ],
        ];

        $controls['acLastNameField'] = [
            'group'       => 'activecampaign',
            'label'       => esc_html__( 'Last name field (optional)', 'bricks-activecampaign' ),
            'type'        => 'select',
            'map_fields'  => true,
            'placeholder' => esc_html__( '– none –', 'bricks-activecampaign' ),
            'required'    => [ 'actions', '=', 'activecampaign' ],
        ];

        // UX
        $controls['acSuccessMsg'] = [
            'group'       => 'activecampaign',
            'label'       => esc_html__( 'Success message', 'bricks-activecampaign' ),
            'type'        => 'text',
            'placeholder' => esc_html__( 'Thank you! You have been successfully subscribed.', 'bricks-activecampaign' ),
            'required'    => [ 'actions', '=', 'activecampaign' ],
        ];

        return $controls;
    }
}
