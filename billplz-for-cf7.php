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
     * Main singleton class for the Billplz for Contact Form 7 
     * plugin.
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
                Billplz_CF7_Admin_Notices::init();
            }
            return self::$instance;
        }

        private function define_constants()
        {
            define("BCF7_PLUGIN_PATH", plugin_dir_path(__FILE__));
            define("BCF7_PLUGIN_URL", plugin_dir_url(__FILE__));
            define("BCF7_PLUGIN_FILE", plugin_basename(__FILE__));
            define("BCF7_TEXT_DOMAIN", "billplz-for-cf7");
            define("BCF7_PLUGIN_VERSION", "0.1.0");
        }

        private function includes()
        {
            require_once BCF7_PLUGIN_PATH . "includes/class-billplz-cf7-init.php";
            require_once BCF7_PLUGIN_PATH . "includes/admin/class-admin-notices.php";
        }
    }
}

add_action("plugins_loaded", array("Billplz_CF7", "instance"));

require_once plugin_dir_path(__FILE__) . "includes/class-billplz-cf7-activator.php";
register_activation_hook(__FILE__, array("Billplz_CF7_Activator", "activate"));