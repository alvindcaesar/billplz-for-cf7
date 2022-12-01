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
        
    $payment_id = $_GET['payment-id'];

    $url         = htmlentities($_SERVER['QUERY_STRING']);
    parse_str(html_entity_decode($url), $query);

    $transaction_id = $query['billplz']['id'];
    $paid_at = $query['billplz']['paid_at'];
    $bill_url = bcf7_get_url()."/bills/".$transaction_id;

    global $wpdb;
    $table_name = $wpdb->prefix . "bcf7_payment";

    if (isset($_GET['payment-id']) and ('true' == $query['billplz']['paid'])) {
        ?>
            <h2>Thank you for your payment!</h2>
            <p>Payment Status: Completed</p>
            <p>Bill ID: <a href="<?php echo esc_url(bcf7_get_url().'/bills/'.$transaction_id); ?>" target="_blank"><?php echo sanitize_text_field($transaction_id); ?></a></p>
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