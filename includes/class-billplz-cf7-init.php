<?php

/**
 * Main Init Class
 */

defined("WPINC") or die();

class Billplz_CF7_Init
{
    public function __construct()
    {
        return array(
            $billplz_cf7_admin_menu       = new Billplz_CF7_Admin_Menu(),
            $billplz_cf7_payment_db       = new Billplz_CF7_Payment_DB(),
            $billplz_cf7_form_process     = new Billplz_CF7_Form_Process(),
            $billplz_cf7_general_settings = new Billplz_CF7_General_Settings()
        );
    }
}
