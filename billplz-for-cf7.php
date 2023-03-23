<?php

/**
 * Plugin Name:     Billplz for Contact Form 7
 * Plugin URI:      https://github.com/alvindcaesar/billplz-for-cf7
 * Description:     Accept payment in Contact Form 7 by using Billplz
 * Author:          Alvind Caesar
 * Author URI:      https://alvindcaesar.com
 * Text Domain:     billplz-for-cf7
 * Domain Path:     /languages
 * Version:         1.2
 *
 * @package         Billplz_For_CF7
 */

defined("WPINC") or die;

if (file_exists( dirname(__FILE__) . '/vendor/autoload.php' ) ) {
    require_once dirname(__FILE__) . '/vendor/autoload.php';
}

define("BCF7_PLUGIN_PATH", plugin_dir_path(__FILE__));
define("BCF7_PLUGIN_URL", plugin_dir_url(__FILE__));
define("BCF7_PLUGIN_FILE", plugin_basename(__FILE__));
define("BCF7_TEXT_DOMAIN", "billplz-for-cf7");
define("BCF7_PLUGIN_VERSION", "1.2");

if (class_exists('BillplzCF7\\Init')) {
    BillplzCF7\Init::register_services();
}

register_activation_hook( __FILE__, array( \BillplzCF7\Base\Activate::class, 'activate' ) );