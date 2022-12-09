<?php

class Billplz_CF7_Admin_Notices
{
  public static function init()
    {
        add_action( "admin_notices", array( __CLASS__, "bwpsp_test_mode_warning") );
    }

  public static function cf7_inactive()
    {
    ?>
        <div class="notice notice-error">
            <p><?php _e( '<strong>Billplz for Contact Form 7 -</strong> Contact Form 7 is not installed and activated. Please install and activate the plugin.', BCF7_TEXT_DOMAIN ); ?></p>
        </div>
    <?php
    }
}

public static function bwpsp_test_mode_warning()
{
  if (! function_exists( 'wpcf7')) {
    echo __(sprintf(
      '<div class="notice notice-warning">
            <p><strong>WPSmartPay: You are using Billplz for WPSmartPay in Test Mode. </strong> Make sure to switch to <a href="' . get_admin_url() . 'admin.php?page=smartpay-setting&tab=gateways">Live Mode</a> when you\'re ready to accept real payments.</p>
        </div>',
    ), 'billplz-for-smartpay');
  }
}