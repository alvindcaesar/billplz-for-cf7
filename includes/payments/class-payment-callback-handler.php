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

    if (isset($_GET['bcf7-listener']) and ("billplz" == $_GET['bcf7-listener'])) {
      
      global $wpdb;
      
      $table_name = $wpdb->prefix . "bcf7_payment";
  
      $status_query = $wpdb->get_results($wpdb->prepare("SELECT status FROM {$table_name} WHERE id= %d", array($_GET['payment-id'])));
  
      $status = get_object_vars($status_query[0])['status'];
  
      if ("completed" == $status) return;
    }
        
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
    unset($query['page_id']);


    $a = array();
    foreach ($query as $key => $value) {
      foreach ($value as $sub_key => $sub_val) {
        array_push($a, ($key . $sub_key . $sub_val));
      }
    }

    sort($a);

    $new     = implode("|", $a);

    $hash    = hash_hmac('sha256', $new, $x_signature);

    if (("billplz" == $_GET['bcf7-listener']) and ($hash == $x_sign) and ('true' == $query['billplz']['paid'])) {
      
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

/**
 * Process a callback from Billplz server
 *
 * @return void
 */
  public static function post()
  {
    if (! isset($_SERVER['REQUEST_METHOD'])) return;
    
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {

      $query_string = file_get_contents('php://input');
    
      parse_str($query_string, $query_params);
      
      $x_sign         = $query_params['x_signature'];
      $x_sign2        = bcf7_get_xsignature();
      $transaction_id = $query_params['id'];
      $paid_at        = $query_params['paid_at'];
      $bill_url       = bcf7_get_url()."/bills/".$transaction_id;
      $payment_id     = $_GET['payment-id'];

        
      ksort($query_params);
      unset($query_params['x_signature']);
      
      $a = array();
      foreach ($query_params as $key => $value) {
        array_push($a, ($key . $value));
      }

      sort($a);

      $new = implode('|', $a);
      
      $hash = hash_hmac('sha256', $new, $x_sign2);

      global $wpdb;
      $table_name = $wpdb->prefix . "bcf7_payment";
      
      if (("billplz" == $_GET['bcf7-listener']) and ($hash == $x_sign) and ("true" == $query_params['paid'])) {
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
  }
}