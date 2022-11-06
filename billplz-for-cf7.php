<?php

/**
 * Plugin Name:     Billplz for Contact Form 7
 * Plugin URI:      https://github.com/alvindcaesar/billplz-for-cf7
 * Description:     Accept payment in Contact Form 7 by using Billplz
 * Author:          Alvind Caesar
 * Author URI:      https://alvindcaesar.com
 * Text Domain:     billplz-for-cf7
 * Domain Path:     /languages
 * Version:         0.1.0
 *
 * @package         Billplz_For_CF7
 */

defined("WPINC") or die();

if (!class_exists("Billplz_CF7")) {
    /**
     * Main singleton class for the Billplz for Contact Form 7 plugin.
     */
    class Billplz_CF7
    {
        private static $instance;

        public static function instance()
        {
            if (
                !isset(self::$instance) and
                !(self::$instance instanceof Billplz_CF7)
            ) {
                self::$instance = new Billplz_CF7();
                self::$instance->define_constants();
                self::$instance->includes();
                self::$instance->init = new Billplz_CF7_Init();
            }
            return self::$instance;
        }

        private function define_constants()
        {
            define("BILLPLZ_CF7_PLUGIN_PATH", plugin_dir_path(__FILE__));
            define("BILLPLZ_CF7_PLUGIN_URL", plugin_dir_url(__FILE__));
            define("BILLPLZ_CF7_PLUGIN_FILE", plugin_basename(__FILE__));
            define("BILLPLZ_CF7_TEXT_DOMAIN", "billplz-for-cf7");
            define("BILLPLZ_CF7_PLUGIN_VERSION", "0.1.0");
        }

        private function includes()
        {
            require_once BILLPLZ_CF7_PLUGIN_PATH . "includes/class-admin-menu.php";
            require_once BILLPLZ_CF7_PLUGIN_PATH . "includes/class-form-process.php";
            require_once BILLPLZ_CF7_PLUGIN_PATH . "includes/class-general-settings.php";
            require_once BILLPLZ_CF7_PLUGIN_PATH . "includes/class-billplz-cf7-init.php";

        }
    }
}

add_action("plugins_loaded", ["Billplz_CF7", "instance"]);

require_once plugin_dir_path(__FILE__) . "includes/class-payment-database.php";
register_activation_hook(__FILE__, array("Billplz_CF7_Payment_DB", "create_db"));
