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
      new Billplz_CF7_Admin_Menu()
    );
  }
}
