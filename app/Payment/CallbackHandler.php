<?php
namespace BillplzCF7\Payment;

class CallbackHandler
{
  private $helpers;

  public function __construct()
  {
    $this->helpers = \BillplzCF7\Helpers\Functions::get_instance();
  }

  public function register()
  {
    add_action("init", array($this, "redirect"));
    add_action("init", array($this, "callback"));
  }

  public function redirect()
  {
    if (empty($_GET) and ( ! isset($query['bcf7-listener']) )) return;

    if (isset($_GET['bcf7-listener']) and ("billplz" == $_GET['bcf7-listener'])) {
      
      global $wpdb;
      
      $table_name = $wpdb->prefix . "bcf7_payment";
  
      $status_query = $wpdb->get_results($wpdb->prepare("SELECT status FROM {$table_name} WHERE id= %d", array($_GET['payment-id'])));
  
      $status = get_object_vars($status_query[0])['status'];
  
      if ("completed" == $status) return;
    }
        
    $x_signature = $this->helpers->get_xsignature();
    $url         = htmlentities($_SERVER['QUERY_STRING']);
    
    parse_str(html_entity_decode($url), $query);

    ksort($query);

    $payment_id     = isset($query['payment-id']) ? $query['payment-id'] : "";
    $transaction_id = isset($query['billplz']['id']) ? $query['billplz']['id'] : "";
    $paid_at        = isset($query['billplz']['paid_at']) ? $query['billplz']['paid_at'] : "";
    $bill_url       = $this->helpers->get_url()."/bills/".$transaction_id;
    $x_sign         = isset($query['billplz']['x_signature']) ? $query['billplz']['x_signature'] : "";

    unset($query['billplz']['x_signature']);
    unset($query['payment-id']);
    unset($query['bcf7-listener']);
    unset($query['page_id']);


    $a = array();
    
    foreach ($query as $key => $value) {
      if ( isset( $value ) && is_array( $value ) ) {
        foreach ($value as $sub_key => $sub_val) {
          array_push($a, ($key . $sub_key . $sub_val));
        }
      }
    }

    sort($a);

    $new     = implode("|", $a);

    $hash    = hash_hmac('sha256', $new, $x_signature);

    if ( isset($_GET['bcf7-listener']) and ($hash == $x_sign) and ('true' == $query['billplz']['paid'])) {
      
      $wpdb->update( $table_name, array( 
          'status' => 'completed', 
          'transaction_id' => $transaction_id, 
          'paid_at' => $paid_at,
          'bill_url' => $bill_url
          ), 
          array( 'ID' => $payment_id ) 
      );

    } elseif ( (isset($_GET['bcf7-listener'])) and ($hash == $x_sign) and ('false' == $query['billplz']['paid']) ) {
      
      $wpdb->update( $table_name, array( 
          'transaction_id' => $transaction_id, 
          'paid_at' => '0000-00-00 00:00:00',
          'bill_url' => $bill_url
          ), 
          array( 'ID' => $payment_id )
      );
    }
  }

  public function callback()
  {
    if (! isset($_SERVER['REQUEST_METHOD'])) return;
    
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {

      $query_string = file_get_contents('php://input');
    
      parse_str($query_string, $query_params);

      if ( ! isset($_GET['bcf7-listener']) and
      empty($_GET['payment-id']) and 
      empty($query_params['x_signature']) and 
      empty($query_params['id']) and 
      empty($query_params['paid']) and 
      empty($query_params['paid_at']) and 
      empty($query_params['id'])
    ) return;
      
      $x_sign         = $query_params['x_signature'];
      $x_sign2        = $this->helpers->get_xsignature();
      $transaction_id = $query_params['id'];
      $paid_at        = $query_params['paid_at'];
      $bill_url       = $this->helpers->get_url()."/bills/".$transaction_id;
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
      
      if ( isset($_GET['bcf7-listener']) and ($hash == $x_sign) and ("true" == $query_params['paid'])) {
        $wpdb->update( $table_name, array( 
          'status' => 'completed', 
          'transaction_id' => $transaction_id, 
          'paid_at' => $paid_at,
          'bill_url' => $bill_url
          ), 
          array( 'ID' => $payment_id ) 
        );

      } elseif ( isset($_GET['bcf7-listener']) and ($hash == $x_sign) and ("false" == $query_params['paid'])) {
      
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