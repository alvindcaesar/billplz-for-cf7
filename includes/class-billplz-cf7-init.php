<?php

class Billplz_CF7_Init
{
    public function __construct()
    {
        return array(
            $bcf7_admin_menu = new Billplz_CF7_Admin_Menu(),
            $bcf7_payment_db = new Billplz_CF7_Payment_DB(),
            $bcf7_form_process = new Billplz_CF7_Form_Process(),
            $bcf7_general_settings = new Billplz_CF7_General_Settings(),
            $bcf7_api_settings = new Billplz_CF7_API_Settings(),
        );
    }
}
