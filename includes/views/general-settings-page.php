<form action="options.php" method="POST">
  <?php
    settings_fields( "billplz_cf7_group" );
    do_settings_sections( "billplz_cf7_general_settings" );
    submit_button();
  ?>
</form>


