<?php

class Billplz_CF7_Callback
{
  public static function init()
  {
    $self = new self();
    add_action( "init", array( $self, "get" ) );
    add_action( "init", array( $self, "post" ) );
  }

  public static function get()
  {
    if (empty($_GET) or (isset($_GET['post']) and isset($_GET['action']))) return;
        
    $x_signature = bcf7_get_xsignature();
    $url         = htmlentities($_SERVER['QUERY_STRING']);
    
    parse_str(html_entity_decode($url), $query);

    if ( ! isset($query['bcf7-listener']) and
      empty($query['payment-id']) and 
      empty($query['billplz']['x_signature']) and 
      empty($query['billplz']['id']) and 
      empty($query['billplz']['paid']) and 
      empty($query['billplz']['paid_at']) and 
      empty($query['billplz']['id'])
    ) return;

    ksort($query);

    $payment_id     = $query['payment-id'];
    $transaction_id = $query['billplz']['id'];
    $paid_at        = $query['billplz']['paid_at'];
    $bill_url       = bcf7_get_url()."/bills/".$transaction_id;
    $x_sign         = $query['billplz']['x_signature'];

    unset($query['billplz']['x_signature']);
    unset($query['payment-id']);
    unset($query['bcf7-listener']);

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

    $status_query = $wpdb->get_results($wpdb->prepare("SELECT status FROM {$table_name} WHERE id= %d", array($payment_id)));
    $status = get_object_vars($status_query[0])['status'];

    if ("completed" == $status) return;

    if (isset($payment_id) and ($hash == $x_sign) and ('true' == $query['billplz']['paid'])) {
      
      $wpdb->update( $table_name, array( 
          'status' => 'completed', 
          'transaction_id' => $transaction_id, 
          'paid_at' => $paid_at,
          'bill_url' => $bill_url
          ), 
          array( 'ID' => $payment_id ) 
      );

    } else {
      
      $wpdb->update( $table_name, array( 
          'transaction_id' => $transaction_id, 
          'paid_at' => '0000-00-00 00:00:00',
          'bill_url' => $bill_url
          ), 
          array( 'ID' => $payment_id )
      );
    }
  }

  public static function post()
  {
    // TODO: Finish this method!
  }
}