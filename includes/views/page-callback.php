<div class="wrap">
  <h1><?php _e("Billplz for Contact Form 7", "billplz-for-cf7"); ?></h1>
  <?php $active_tab = isset($_GET["tab"])
      ? $_GET["tab"]
      : "general_settings"; ?>
  <h2 class="nav-tab-wrapper">
    <a href="?page=billplz-cf7&tab=general_settings" class="nav-tab <?php echo $active_tab ==
    "general_settings"
        ? "nav-tab-active"
        : ""; ?>">General Settings</a>
    <a href="?page=billplz-cf7&tab=api_settings" class="nav-tab <?php echo $active_tab ==
    "api_settings"
        ? "nav-tab-active"
        : ""; ?>">API Settings</a>
  </h2>

  <?php if ($active_tab == "general_settings") {
      echo "<p>General Settings goes here</p>";
  } else {
      echo "<p>API Settings goes here</p>";
  } ?>
</div>