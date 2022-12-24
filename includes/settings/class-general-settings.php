<?php

if ( ! class_exists("Billplz_CF7_General_Settings") ) {
  
  class Billplz_CF7_General_Settings
  {

    public static $options;

    public function __construct()
    {
      self::$options = get_option("bcf7_general_settings");
      add_action( 'admin_init', array( $this, "admin_init" ) );
    }

    public function admin_init()
    {
      register_setting( "bcf7_general", "bcf7_general_settings");

      add_settings_section(
        "bcf7_general_section",
        null,
        null,
        "bcf7_general_settings"
      );

      add_settings_field(
        "bcf7_mode",
        "Test Mode",
        array( $this, 'bcf7_mode_callback' ),
        "bcf7_general_settings",
        "bcf7_general_section",
        array(
          "label_for" => "bcf7_mode"
        )
      );

      add_settings_field(
        "bcf7_form_select",
        "Choose a payment form to use",
        array( $this, 'bcf7_form_select_callback' ),
        "bcf7_general_settings",
        "bcf7_general_section",
        array(
          "label_for" => "bcf7_form_select"
        )
      );

      add_settings_field(
        "bcf7_redirect_page",
        "Payment Confirmation Page",
        array( $this, 'bcf7_redirect_page_callback' ),
        "bcf7_general_settings",
        "bcf7_general_section",
        array(
          "label_for" => "bcf7_redirect_page"
        )
      );
    }

    public function bcf7_mode_callback()
    {
      ?>
      <input type="checkbox" name="bcf7_general_settings[bcf7_mode]" id="bcf7_mode" value="1" <?php isset(self::$options['bcf7_mode']) ? (checked("1", self::$options['bcf7_mode'], true )) : null ?> >
      <label for="bcf7_mode">Activate Test Mode</label>
      <?php
    }

    public function bcf7_form_select_callback()
    {
      $args        = array('post_type' => 'wpcf7_contact_form', 'posts_per_page' => -1);
	    $cf7_forms   = get_posts( $args );
      $form_ids    = wp_list_pluck( $cf7_forms , 'ID' );
	    $form_titles = wp_list_pluck( $cf7_forms , 'post_title' );
	    $ids_titles  = array_combine($form_ids, $form_titles);
      
      ?>
      
      <select name='bcf7_general_settings[bcf7_form_select]' id='bcf7_form_select'>
        <option value="">--Select a form--</option>
        <?php foreach ($ids_titles as $id => $title) {?>
          <option value=<?php echo esc_attr($id) ?><?php isset(self::$options['bcf7_form_select']) ? selected( $id, self::$options['bcf7_form_select'], true ) : "";?>>
            <?php echo esc_html($title) ." (ID: ". esc_html($id) .")"; ?>
          </option><?php } ?>
      </select> 

      <?php
    }

    public function bcf7_redirect_page_callback()
    {
      $args        = array('post_type' => 'page', 'posts_per_page' => -1);
	    $pages       = get_posts( $args );
      $page_ids    = wp_list_pluck( $pages , 'ID' );
	    $page_titles = wp_list_pluck( $pages , 'post_title' );
	    $ids_titles  = array_combine($page_ids, $page_titles);
      
      ?>
      
      <select name='bcf7_general_settings[bcf7_redirect_page]' id='bcf7_redirect_page'>
        <option value="">--Select a redirect page--</option>
        <?php foreach ($ids_titles as $id => $title) {
          ?>
          <option value=<?php echo esc_attr($id) ?><?php isset(self::$options['bcf7_redirect_page']) ? selected( $id, self::$options['bcf7_redirect_page'], true ) : "";?>>
            <?php echo esc_html($title); ?>
          </option>
          <?php } ?>
      </select> 

      <?php
    }
  }
}