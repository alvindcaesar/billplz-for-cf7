<?php

if (!class_exists("Billplz_CF7_API_Settings")) {

    class Billplz_CF7_API_Settings
    {
        public static $options;

        public function __construct()
        {
            self::$options = get_option("bcf7_api_options");
            add_action('admin_init', array($this, "admin_init"));
        }

        public function admin_init()
        {
            register_setting("bcf7_api", "bcf7_api_options");

            add_settings_section(
                "bcf7_live_section",
                "Live Credentials",
                null,
                "bcf7_live_settings"
            );

            add_settings_field(
                "bcf7_live_secret_key",
                "Secret Key",
                array($this, 'bcf7_live_secret_key_callback'),
                "bcf7_live_settings",
                "bcf7_live_section"
            );

            add_settings_field(
                "bcf7_live_collection_id",
                "Collection ID",
                array($this, 'bcf7_live_collection_id_callback'),
                "bcf7_live_settings",
                "bcf7_live_section",
            );

            add_settings_field(
                "bcf7_live_xsignature_key",
                "X-Signature Key",
                array($this, 'bcf7_live_xsignature_key_callback'),
                "bcf7_live_settings",
                "bcf7_live_section",
            );

            add_settings_section(
                "bcf7_sandbox_section",
                "Sandbox Credentials",
                null,
                "bcf7_sandbox_settings"
            );

            add_settings_field(
                "bcf7_sandbox_secret_key",
                "Secret Key",
                array($this, 'bcf7_sandbox_key_callback'),
                "bcf7_sandbox_settings",
                "bcf7_sandbox_section",
            );

            add_settings_field(
                "bcf7_sandbox_collection_id",
                "Collection ID",
                array($this, 'bcf7_sandbox_collection_id_callback'),
                "bcf7_sandbox_settings",
                "bcf7_sandbox_section",
            );

            add_settings_field(
                "bcf7_sandbox_xsignature_key",
                "X-Signature Key",
                array($this, 'bcf7_sandbox_xsignature_key_callback'),
                "bcf7_sandbox_settings",
                "bcf7_sandbox_section",
            );
        }

        public function bcf7_live_secret_key_callback()
        {
            ?>
      <input class="regular-text" type="text" name="bcf7_api_options[bcf7_live_secret_key]" id="bcf7_live_secret_key"
      value="<?php echo isset(self::$options['bcf7_live_secret_key']) ? self::$options['bcf7_live_secret_key'] : ""; ?>"
      >
      <?php
}

        public function bcf7_live_collection_id_callback()
        {
            ?>
      <input class="regular-text" type="text" name="bcf7_api_options[bcf7_live_collection_id]" id="bcf7_live_collection_id"
      value="<?php echo isset(self::$options['bcf7_live_collection_id']) ? self::$options['bcf7_live_collection_id'] : ""; ?>"
      >
      <?php
}

        public function bcf7_live_xsignature_key_callback()
        {
            ?>
      <input class="regular-text" type="text" name="bcf7_api_options[bcf7_live_xsignature_key]" id="bcf7_live_xsignature_key"
      value="<?php echo isset(self::$options['bcf7_live_xsignature_key']) ? self::$options['bcf7_live_xsignature_key'] : ""; ?>"
      >
      <?php
}

        public function bcf7_sandbox_key_callback()
        {
            ?>
      <input class="regular-text" type="text" name="bcf7_api_options[bcf7_sandbox_secret_key]" id="bcf7_sandbox_secret_key"
      value="<?php echo isset(self::$options['bcf7_sandbox_secret_key']) ? self::$options['bcf7_sandbox_secret_key'] : ""; ?>"
      >
      <?php
}

        public function bcf7_sandbox_collection_id_callback()
        {
            ?>
      <input class="regular-text" type="text" name="bcf7_api_options[bcf7_sandbox_collection_id]" id="bcf7_sandbox_collection_id"
      value="<?php echo isset(self::$options['bcf7_sandbox_collection_id']) ? self::$options['bcf7_sandbox_collection_id'] : ""; ?>"
      >
      <?php
}

        public function bcf7_sandbox_xsignature_key_callback()
        {
            ?>
      <input class="regular-text" type="text" name="bcf7_api_options[bcf7_sandbox_xsignature_key]" id="bcf7_sandbox_xsignature_key"
      value="<?php echo isset(self::$options['bcf7_sandbox_xsignature_key']) ? self::$options['bcf7_sandbox_xsignature_key'] : ""; ?>"
      >
      <?php
}
    }
}