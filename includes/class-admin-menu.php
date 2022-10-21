<?php

defined("WPINC") or die();

class Billplz_CF7_Admin_Menu
{
  public function __construct()
  {
    add_action("admin_menu", [$this, "add_submenu"]);
  }

  /**
   * Adds a submenu page under a Contact Form 7 menu.
   */
  public function add_submenu()
  {
    add_submenu_page(
      "wpcf7",
      __("Billplz for Contact Form 7", "billplz-for-cf7"),
      __("Billplz", "billplz-for-cf7"),
      "manage_options",
      "billplz-cf7",
      [$this, "display_callback"]
    );
  }

  /**
   * Display callback for the submenu page.
   */
  public function display_callback()
  {
    require_once BILLPLZ_CF7_PLUGIN_PATH .
      "includes/views/page-callback.php";
  }
}