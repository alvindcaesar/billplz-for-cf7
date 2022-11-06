<?php

if ( ! class_exists("Billplz_CF7_General_Settings") ) {
  
  class Billplz_CF7_General_Settings
  {

    public static $options;

    public function __construct()
    {
      self::$options = get_option("billplz_cf7_general_settings");
      add_action( 'admin_init', array( $this, "admin_init" ) );
    }

    public function admin_init()
    {
      register_setting( "billplz_cf7_group", "billplz_cf7_general_settings");

      add_settings_section(
        "billplz_cf7_general_section",
        null,
        null,
        "billplz_cf7_general_settings"
      );

      add_settings_field(
        "billplz_cf7_mode",
        "Test Mode",
        array( $this, 'billplz_cf7_mode_callback' ),
        "billplz_cf7_general_settings",
        "billplz_cf7_general_section"
      );

      add_settings_field(
        "billplz_cf7_form_select",
        "Choose a payment form to use",
        array( $this, 'billplz_cf7_form_callback' ),
        "billplz_cf7_general_settings",
        "billplz_cf7_general_section"
      );
    }

    public function billplz_cf7_mode_callback()
    {
      ?>
      <input type="checkbox" name="billplz_cf7_general_settings[billplz_cf7_mode]" id="billplz_cf7_mode" value="1" <?php isset(self::$options['billplz_cf7_mode']) ? (checked("1", self::$options['billplz_cf7_mode'], true )) : null ?> >
      <label for="billplz_cf7_mode">Activate Test Mode</label>
      <?php
    }

    public function billplz_cf7_form_callback()
    {
      $args        = array('post_type' => 'wpcf7_contact_form', 'posts_per_page' => -1);
	    $cf7_forms   = get_posts( $args );
      $form_ids    = wp_list_pluck( $cf7_forms , 'ID' );
	    $form_titles = wp_list_pluck( $cf7_forms , 'post_title' );
	    $total       = array_combine($form_ids, $form_titles);
      
      ?>
      
      <select name='billplz_cf7_general_settings[billplz_cf7_form_select]' id='billplz_cf7_form_select'>
        <?php foreach ($total as $id => $title) {?>
          <option value=<?php echo $id ?><?php isset(self::$options['billplz_cf7_form_select']) ? selected( $id, self::$options['billplz_cf7_form_select'], true ) : "";?>>
            <?php echo $title ." (ID: ". $id .")"; ?>
          </option><?php } ?>
      </select> 

      <?php


    }
  }
}