<?php

class Billplz_CF7_Shortcodes
{
  public static function init()
  {
    $self = new self();
    add_shortcode("bcf7_payment_confirmation", array($self, "payment_confirmation"));
  }

  public function payment_confirmation()
  {
    if ( (empty($_GET) and (isset($_GET['bcf7-listener']) and "billplz" != $_GET['bcf7-listener'])) or (isset($_GET['post']) and isset($_GET['action']))) return;

    if (isset($_GET['payment-id']) and "billplz" == $_GET['bcf7-listener']) {
      $payment_id = $_GET['payment-id'];

      global $wpdb;

      $table_name = $wpdb->prefix . "bcf7_payment";

      $data = $wpdb->get_results($wpdb->prepare("SELECT name, email, transaction_id, bill_url, status FROM {$table_name} WHERE id= %d", array($payment_id)));

      $data_array = get_object_vars($data[0]);

      $name   = $data_array['name'];
      $email  = $data_array['email'];
      $trx_id = $data_array['transaction_id'];
      $bill   = $data_array['bill_url'];
      $status = $data_array['status'];

      sleep(3); // Give it a few seconds and wait for the payment to be updated in the background.

      if ("completed" == $status) {
        ?>
          <h2>Thank you for your payment!</h2>
          <p>Payment ID: <?php echo esc_html($payment_id); ?></p>
          <p>Name: <?php echo esc_html($name); ?></p>
          <p>Email: <?php echo esc_html($email); ?></p>
          <p>Payment Status: <strong>Completed</strong></p>
          <p>Bill ID: <a href="<?php echo esc_url($bill); ?>" target="_blank"><?php echo esc_html($trx_id); ?></a></p>
        <?php

      } elseif (("pending" == $status) and ("true" == $_GET['billplz']['paid'])) {
        $bill_url = bcf7_get_url().'/bills/'.($_GET['billplz']['id']);
        ?>
          <h2>Something wrong. please contact site owner.</h2>
          <p>Payment Status: Unknown</p>
          <p>Please check your bill <a href="<?php echo esc_url($bill_url); ?>" target="_blank">here</a></p>
        <?php

      } else {
        ?>
          <h2>Sorry, your payment was unsuccessful</h2>
          <p>Payment Status: Failed</p>
          <p>Please repay the bill <a href="<?php echo esc_url($bill); ?>" target="_blank">here</a></p>
        <?php
      }
    }
  }
}