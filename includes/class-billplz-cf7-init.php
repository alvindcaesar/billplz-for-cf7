<?php

class Billplz_CF7_Init
{
    public function __construct()
    {
        $this->include_classes();
        $this->init();
    }

    public function include_classes()
    {
        require_once BCF7_PLUGIN_PATH . "includes/admin/class-admin-menu.php";
        require_once BCF7_PLUGIN_PATH . "includes/payments/class-form-process.php";
        require_once BCF7_PLUGIN_PATH . "includes/helpers/class-shortcodes.php";
        require_once BCF7_PLUGIN_PATH . "includes/settings/class-api-settings.php";
        require_once BCF7_PLUGIN_PATH . "includes/settings/class-general-settings.php";
        require_once BCF7_PLUGIN_PATH . "includes/helpers/billplz-cf7-helper.php";
        require_once BCF7_PLUGIN_PATH . "includes/payments/class-payment-callback.php";
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
}