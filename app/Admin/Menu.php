<?php

namespace BillplzCF7\Admin;

class Menu
{
  public function register()
  {
    add_action("admin_menu", array($this, "add_menu"));
  }

  public function add_menu()
  {
    add_submenu_page(
      "wpcf7",
      __("Billplz for Contact Form 7", "billplz-for-cf7"),
      __("Billplz", "billplz-for-cf7"),
      "manage_options",
      "billplz-cf7",
      array($this, "callback")
    );
  }

  public function callback()
  {
    require_once BCF7_PLUGIN_PATH .
      "app/views/page-callback.php";
  }
}
