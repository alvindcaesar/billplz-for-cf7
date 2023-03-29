<?php

namespace BillplzCF7\Settings;

class Email
{
  public static $options;

  public function __construct()
  {
    self::$options = get_option("bcf7_email_settings");
  }

  public function register()
  {
    add_action('admin_init', array($this, "init"));
    add_filter('pre_update_option_bcf7_email_settings', array($this, 'update_email_permission'));
  }

  public function update_email_permission($new_value)
  {
    if (!isset($new_value['bcf7_email_permission'])) {
      $new_value['bcf7_email_permission'] = '';
    }

    return $new_value;
  }

  public function init()
  {
    register_setting(
      'bcf7_email',
      'bcf7_email_settings'
    );

    add_settings_section(
      'bcf7_email_section',
      'Email Settings',
      null,
      'bcf7_email_settings'
    );

    add_settings_field(
      "bcf7_email_permission",
      "Enable",
      array($this, 'send_email_callback'),
      "bcf7_email_settings",
      "bcf7_email_section",
      array(
        "label_for" => "bcf7_email_permission"
      )
    );

    add_settings_field(
      'bcf7_email_subject',
      'Email Subject',
      array($this, 'email_subject_callback'),
      'bcf7_email_settings',
      'bcf7_email_section',
      array(
        'label_for' => 'bcf7_email_subject'
      )
    );

    add_settings_field(
      'bcf7_email_body',
      'Email Body',
      array($this, 'email_body_callback'),
      'bcf7_email_settings',
      'bcf7_email_section',
      array(
        'label_for' => 'bcf7_email_body'
      )
    );
  }

  public function send_email_callback()
  {
    ?>
      <input type="checkbox" name="bcf7_email_settings[bcf7_email_permission]" id="bcf7_email_permission" value="1" <?php isset(self::$options['bcf7_email_permission']) ? (checked("1", self::$options['bcf7_email_permission'], true)) : null ?>>
      <label for="bcf7_email_permission">Send a confirmation email upon a successful transaction</label>
    <?php
  }

  public function email_subject_callback()
  {
  ?>
    <input class="regular-text" type="text" name="bcf7_email_settings[bcf7_email_subject]" id="bcf7_email_subject" value="<?php echo esc_attr(isset(self::$options['bcf7_email_subject']) ? self::$options['bcf7_email_subject'] : ""); ?>">
  <?php
  }

  public function email_body_callback()
  {
    $content = isset(self::$options['bcf7_email_body']) ? self::$options['bcf7_email_body'] : '';

    $settings = array(
      'textarea_name' => 'bcf7_email_settings[bcf7_email_body]',
      'textarea_rows' => 10,
      'teeny' => true,
      'media_buttons' => false,
      'tinymce' => array(
        'plugins' => 'wordpress,wplink,wpautoresize,wpeditimage,wpgallery,wplink,wpdialogs',
        'toolbar1' => 'formatselect,bold,italic,underline,strikethrough,blockquote,hr,alignleft,aligncenter,alignright,link,unlink,undo,redo',
        'toolbar2' => '',
        'toolbar3' => '',
        'toolbar4' => '',
        'wp_autoresize_on' => true,
        'wp_link_text' => __('Insert/edit link'),
        'image_advtab' => true,
      ),
    );

    wp_editor($content, 'bcf7_email_body', $settings);
  }
}
