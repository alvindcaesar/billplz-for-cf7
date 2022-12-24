<?php

class Billplz_CF7_Admin_Notices
{
	public static function init()
	{
		add_action( "admin_notices", array( __CLASS__, "cf7_inactive") );
		add_action( "admin_notices", array( __CLASS__, "keys_check") );

		if ( "1" == bcf7_general_option("bcf7_mode") ) {
			add_action( "admin_bar_menu", array( __CLASS__, "sandbox_active"), 999 );
			add_action( "admin_head", array( __CLASS__, "sandbox_active_style") );
		}
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

	public static function sandbox_active( $admin_bar )
	{
		$args = array(
				'id' => 'bcf7-test-mode-notice',
				'title' => 'BCF7 Sandbox Mode Active', 
				'href' => admin_url("admin.php?page=billplz-cf7&tab=general-settings")
			);
			
		$admin_bar->add_node($args);
	}

	public static function sandbox_active_style()
	{
	?>
		<style>
			#wpadminbar ul li#wp-admin-bar-bcf7-test-mode-notice > a {
				color: #f3bb1b;
			}
		</style>';
	<?php
	}
}