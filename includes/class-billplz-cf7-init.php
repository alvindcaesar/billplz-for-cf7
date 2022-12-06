<?php

class Billplz_CF7_Init
{
    public function __construct()
    {
        if ( ! function_exists( 'wpcf7') ) {
            add_action( "admin_notices", array( $this, "notice" ) );
        } else {
            $this->include_classes();
            $this->init();
            add_filter("plugin_action_links", array( $this, "add_settings_link"), 10, 2 );
        }

    }

    public function include_classes()
    {
        require_once BCF7_PLUGIN_PATH . "includes/admin/class-admin-menu.php";
        require_once BCF7_PLUGIN_PATH . "includes/payments/class-form-process.php";
        require_once BCF7_PLUGIN_PATH . "includes/helpers/class-shortcodes.php";
        require_once BCF7_PLUGIN_PATH . "includes/settings/class-api-settings.php";
        require_once BCF7_PLUGIN_PATH . "includes/settings/class-general-settings.php";
        require_once BCF7_PLUGIN_PATH . "includes/helpers/billplz-cf7-helper.php";
        require_once BCF7_PLUGIN_PATH . "includes/payments/class-payment-callback-handler.php";
    }

    public function init()
    {
        return array(
            $bcf7_admin_menu       = new Billplz_CF7_Admin_Menu(),
            $bcf7_form_process     = new Billplz_CF7_Form_Process(),
            $bcf7_shortcodes       = Billplz_CF7_Shortcodes::init(),
            $bcf7_api_settings     = new Billplz_CF7_API_Settings(),
            $bcf7_general_settings = new Billplz_CF7_General_Settings(),
            $bcf7_payment_callback = Billplz_CF7_Callback::init()
        );
    }

    public function notice()
    {
    ?>
        <div class="notice notice-error">
            <p><?php _e( '<strong>Billplz for Contact Form 7 -</strong> Contact Form 7 is not installed and activated. Please install and activate the plugin.', BCF7_TEXT_DOMAIN ); ?></p>
        </div>
    <?php
    }

    public function add_settings_link ( $links, $file )
    {
        if ( $file == BCF7_PLUGIN_FILE ) {
            $general_link = '<a href="' . admin_url( 'admin.php?page=billplz-cf7&tab=general-settings' ) . '">' . __( 'General Settings', BCF7_TEXT_DOMAIN ) . '</a>';
            $api_link     = '<a href="' . admin_url( 'admin.php?page=billplz-cf7&tab=api-settings' ) . '">' . __( 'API Settings', BCF7_TEXT_DOMAIN ) . '</a>';
            array_unshift( $links, $general_link, $api_link );
            $deactivate_link = array_pop( $links );
            array_push( $links, $deactivate_link );
        }
        return $links;
    }
}