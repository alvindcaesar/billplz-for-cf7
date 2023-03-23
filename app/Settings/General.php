<?php

namespace BillplzCF7\Settings;

class General
{
  public static $options;

  public function __construct()
  {
    self::$options = get_option("bcf7_general_settings");
  }

  public function register()
  {
    add_action( 'admin_init', array( $this, "init" ) );
  }

  public function init()
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
      array( $this, 'mode_callback' ),
      "bcf7_general_settings",
      "bcf7_general_section",
      array(
        "label_for" => "bcf7_mode"
      )
    );

    add_settings_field(
      "bcf7_form_select",
      "Payment Form",
      array( $this, 'form_select_callback' ),
      "bcf7_general_settings",
      "bcf7_general_section",
      array(
        "label_for" => "bcf7_form_select"
      )
    );

    add_settings_field(
      "bcf7_redirect_page",
      "Payment Confirmation / Redirect Page",
      array( $this, 'redirect_page_callback' ),
      "bcf7_general_settings",
      "bcf7_general_section",
      array(
        "label_for" => "bcf7_redirect_page"
      )
    );
  }

  public function mode_callback()
  {
    ?>
      <input type="checkbox" name="bcf7_general_settings[bcf7_mode]" id="bcf7_mode" value="1" <?php isset(self::$options['bcf7_mode']) ? (checked("1", self::$options['bcf7_mode'], true )) : null ?> >
      <label for="bcf7_mode">Activate Test Mode</label>
    <?php
  }

  public function form_select_callback()
  {
    $args        = array('post_type' => 'wpcf7_contact_form', 'posts_per_page' => -1);
    $cf7_forms   = get_posts( $args );
    $form_ids    = wp_list_pluck( $cf7_forms , 'ID' );
    $form_titles = wp_list_pluck( $cf7_forms , 'post_title' );
    $ids_titles  = array_combine($form_ids, $form_titles);
    
    ?>
      <select name='bcf7_general_settings[bcf7_form_select][]' id='bcf7_form_select' multiple="multiple" style="width: 350px; height: 150px;">
        <option value="">------------</option>
        <?php foreach ($ids_titles as $id => $title) {?>
          <?php $selected = in_array( $id, self::$options['bcf7_form_select']) ? ' selected="selected" ' : ''; ?>
          <option value=<?php echo esc_attr($id) ?> <?php echo $selected; ?>>
            <?php echo esc_html($title) ." (ID: ". esc_html($id) .")"; ?>
          </option><?php } ?>
      </select> 
      <p class="description">Choose a Contact Form 7 form to use. Hold CTRL/CMD to select multiple forms. <br> <a href="<?php echo esc_url(admin_url("admin.php?page=wpcf7-new")); ?>">Click here</a> to create a new form.</p>
    <?php
  }

  public function redirect_page_callback()
  {
    $args        = array('post_type' => 'page', 'posts_per_page' => -1);
    $pages       = get_posts( $args );
    $page_ids    = wp_list_pluck( $pages , 'ID' );
    $page_titles = wp_list_pluck( $pages , 'post_title' );
    $ids_titles  = array_combine($page_ids, $page_titles);
    
    ?>
      <select name='bcf7_general_settings[bcf7_redirect_page]' id='bcf7_redirect_page' style="width: 350px;">
        <option value="">-- Select a redirect page --</option>
        <?php foreach ($ids_titles as $id => $title) {
          ?>
          <option value=<?php echo esc_attr($id) ?><?php isset(self::$options['bcf7_redirect_page']) ? selected( $id, self::$options['bcf7_redirect_page'], true ) : "";?>>
            <?php echo esc_html($title); ?>
          </option>
          <?php } ?>
      </select> 
      <p class="description">Choose a page to redirect after payment completed. Default page: <strong>BCF7 Payment Confirmation</strong></p>
      <p class="description">If you want to use a custom redirect page, make sure to add the <code>[bcf7_payment_confirmation]</code> shortcode inside the custom page's content.</p>
    <?php
  }
}