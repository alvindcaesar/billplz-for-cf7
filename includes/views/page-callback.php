<div class="wrap">
    <h1><?php _e("Billplz for Contact Form 7", BCF7_TEXT_DOMAIN); ?></h1>
  <?php $active_tab = isset($_GET["tab"])
      ? $_GET["tab"]
      : "payments"; ?>
  <h2 class="nav-tab-wrapper">
  <a href="?page=billplz-cf7&tab=payments" class="nav-tab <?php echo $active_tab ==
    "payments"
        ? "nav-tab-active"
        : ""; ?>">Payments</a>
    <a href="?page=billplz-cf7&tab=general-settings" class="nav-tab <?php echo $active_tab ==
    "general-settings"
        ? "nav-tab-active"
        : ""; ?>">General Settings</a>
    <a href="?page=billplz-cf7&tab=api-settings" class="nav-tab <?php echo $active_tab ==
    "api-settings"
        ? "nav-tab-active"
        : ""; ?>">API Settings</a>
  </h2>

  <?php if ($active_tab == "payments") {
    require_once BCF7_PLUGIN_PATH .
    "includes/admin/class-billplz-cf7-payment-list-table.php";
  } elseif ($active_tab == "general-settings") {
      require_once BCF7_PLUGIN_PATH . "includes/views/general-settings-page.php";
  } else {
    require_once BCF7_PLUGIN_PATH . "includes/views/api-settings-page.php";
  } ?>
</div>