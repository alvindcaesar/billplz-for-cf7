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
    "includes/class-billplz-cf7-payment-list.php";

    $bcf7_table = new Billplz_CF7_Payment_List();
    echo "<br>";
    $bcf7_table->views();
    // Search function
    echo "<form method='post' name='billplz_search_payment' action='".$_SERVER['PHP_SELF']. "?page=billplz-cf7&tab=payments'>";
    $bcf7_table->prepare_items();
    $bcf7_table->search_box( "Search Customer or Bill ID", "payment-search-id");
    $bcf7_table->display();
    echo "</form>";
  
  } elseif ($active_tab == "general-settings") {
      require_once BCF7_PLUGIN_PATH . "includes/views/general-settings-page.php";
  } else {
    require_once BCF7_PLUGIN_PATH . "includes/views/api-settings-page.php";
  } ?>
</div>