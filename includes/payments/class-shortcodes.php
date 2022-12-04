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
    if (empty($_GET) or (isset($_GET['post']) && isset($_GET['action']))) return;
        
    $x_signature = bcf7_get_xsignature();
    $url         = htmlentities($_SERVER['QUERY_STRING']);
    
    parse_str(html_entity_decode($url), $query);

    if (empty($query['payment-id']) && empty($query['billplz']['x_signature']) && empty($query['billplz']['id']) && empty($query['billplz']['paid']) && empty($query['billplz']['paid_at']) && empty($query['billplz']['id'])) return;

    ksort($query);

    $payment_id     = $query['payment-id'];
    $transaction_id = $query['billplz']['id'];
    $paid_at        = $query['billplz']['paid_at'];
    $bill_url       = bcf7_get_url()."/bills/".$transaction_id;
    $x_sign         = $query['billplz']['x_signature'];

    unset($query['billplz']['x_signature']);
    unset($query['payment-id']);

    $a = array();
    foreach ($query as $key => $value) {
      foreach ($value as $sub_key => $sub_val) {
        array_push($a, ($key . $sub_key . $sub_val));
      }
    }

    sort($a);

    $new     = implode("|", $a);

    $hash    = hash_hmac('sha256', $new, $x_signature);

    global $wpdb;
    
    $table_name = $wpdb->prefix . "bcf7_payment";

    $info_obj = $wpdb->get_results($wpdb->prepare("SELECT name, email FROM {$table_name} WHERE id= %d", array($payment_id)));

    $info_array = get_object_vars($info_obj[0]);

    if (isset($payment_id) and ($hash == $x_sign) and ('true' == $query['billplz']['paid'])) {
      ?>
          <h2>Thank you for your payment!</h2>
          <p>Payment ID: <?php echo esc_html($payment_id); ?></p>
          <p>Name: <?php echo esc_html($info_array['name']); ?></p>
          <p>Email: <?php echo esc_html($info_array['email']); ?></p>
          <p>Payment Status: Completed</p>
          <p>Bill ID: <a href="<?php echo esc_url(bcf7_get_url().'/bills/'.$transaction_id); ?>" target="_blank"><?php echo esc_html($transaction_id); ?></a></p>
      <?php
      
      $wpdb->update( $table_name, array( 
          'status' => 'completed', 
          'transaction_id' => $transaction_id, 
          'paid_at' => $paid_at,
          'bill_url' => $bill_url
          ), 
          array( 'ID' => $payment_id ) 
      );

    } else {
      ?>
          <h2>Sorry, your payment was unsuccessful</h2>
          <p>Payment Status: Failed</p>
          <p>Please repay the bill <a href="<?php echo esc_url(bcf7_get_url().'/bills/'.$transaction_id); ?>" target="_blank">here</a></p>
      <?php

      $wpdb->update( $table_name, array( 
          'transaction_id' => $transaction_id, 
          'paid_at' => '0000-00-00 00:00:00',
          'bill_url' => $bill_url
          ), 
          array( 'ID' => $payment_id ) 
      );
    }
  }
}