<?php

class Billplz_CF7_Admin_Notices
{
	public static function init()
	{
		add_action( "admin_notices", array( __CLASS__, "cf7_inactive") );
		add_action( "admin_notices", array( __CLASS__, "keys_check") );
		add_action( "admin_bar_menu", array( __CLASS__, "status_notice"), 999 );
	}

	public static function cf7_inactive()
	{
		if (! function_exists( 'wpcf7')) {
			echo __(sprintf(
					'<div class="notice notice-error">
							<p><strong>Billplz for Contact Form 7 -</strong> Contact Form 7 is not installed and activated. Please install and activate the plugin.</p>
					</div>',
			), BCF7_TEXT_DOMAIN );
		}
	}

	public static function keys_check()
	{
		if ( function_exists( 'wpcf7')) { 
			if ( "1" == bcf7_general_option("bcf7_mode") and ((empty(bcf7_api_option("bcf7_sandbox_secret_key"))) or empty(bcf7_api_option("bcf7_sandbox_collection_id")) or empty(bcf7_api_option("bcf7_sandbox_xsignature_key"))) ) {
				echo __(sprintf(
					'<div class="notice notice-warning">
								<p><strong>Billplz for Contact Form 7 -</strong>Billplz Sandbox Credentials is not set. Enter your Secret Key, Collection ID and X-Signature Key in order to use Billplz service. <a href="' . get_admin_url() . 'admin.php?page=billplz-cf7&tab=api-settings">Set Credential</a></p>
						</div>',
				), BCF7_TEXT_DOMAIN);
			} elseif ( "0" == bcf7_general_option("bcf7_mode") and (empty(bcf7_api_option("bcf7_live_secret_key")) or empty(bcf7_api_option("bcf7_live_collection_id")) or empty(bcf7_api_option("bcf7_live_xsignature_key"))) ) {
				echo __(sprintf(
					'<div class="notice notice-warning">
								<p><strong>Billplz for Contact Form 7 -</strong>Billplz Live Credentials is not set. Enter your Secret Key, Collection ID and X-Signature Key in order to use Billplz service. <a href="' . get_admin_url() . 'admin.php?page=billplz-cf7&tab=api-settings">Set Credential</a></p>
						</div>',
				), BCF7_TEXT_DOMAIN);
			}
		}
	}

	public static function status_notice( $admin_bar )
	{
		$color = ( "1" == bcf7_general_option("bcf7_mode") ) ? "#f3bb1b" : "#90EE90";
		$args = array(
				'id' => 'bcf7-mode-status',
				'title' => "BCF7 Mode Status: <span style='color:{$color};'>".strtoupper(bcf7_get_mode())."</span>",
				'href' => admin_url("admin.php?page=billplz-cf7&tab=general-settings")
			);
			
		$admin_bar->add_menu($args);
	}
}