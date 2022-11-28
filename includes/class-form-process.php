<?php

class Billplz_CF7_Form_Process
{
    public function __construct()
    {
        add_action('wpcf7_before_send_mail', array($this, 'process_data'));
        add_filter( 'wpcf7_load_js', '__return_false' );
        add_shortcode('bcf7_confirm', array($this, 'success_page'));

    }

    public function process_data($contact_form)
    {
        $id = $contact_form->id();

        if (bcf7_general_option("bcf7_form_select") == $id) {
            $submission = WPCF7_Submission::get_instance();

            if ($submission) {
                $form_id        = $submission->get_contact_form()->id();
                $form_title     = $submission->get_contact_form()->title();
                $posted_data    = $submission->get_posted_data();
                $name           = $posted_data['bcf7-name'];
                $email          = $posted_data['bcf7-email'];
                $amount         = $posted_data['bcf7-amount'];
                $phone          = $posted_data['bcf7-phone'];
                $transaction_id = '';
                $mode           = bcf7_get_mode();
                $status         = 'Pending';

                $payment_id = $this->record_data($form_id, $form_title, $name, $phone, $email, $amount, $transaction_id, $mode, $status);

                $this->process_payment($name, $email, $amount, $status, $payment_id);
            }

        }
    }

    public function record_data($form_id, $form_title, $name, $phone, $email, $amount, $transaction_id, $mode, $status)
    {
        global $wpdb;

        $table_name = $wpdb->prefix . "bcf7_payment";

        $wpdb->insert($table_name, array(
            'form_id'        => $form_id,
            'form_title'     => $form_title,
            'name'           => $name,
            'phone'          => $phone,
            'amount'         => $amount,
            'transaction_id' => $transaction_id,
            'email'          => $email,
            'mode'           => $mode,
            'status'         => $status,
            'created_at'     => current_time('mysql'),
            ),
        );

        return $wpdb->insert_id;
    }

    public function process_payment($name, $email, $amount, $description, $payment_id)
    {
        $args = array(
          'headers' => array(
            'Authorization' => 'Basic '. bcf7_get_api_key() . ':',
            ),
            'body' => array(
              'collection_id' => bcf7_get_collection_id(),
              'email' => $email,
              'name' => $name,
              'amount' => $amount * 100,
              'redirect_url' => add_query_arg(array('payment-id' => $payment_id), site_url("?page_id=".bcf7_general_option('bcf7_redirect_page')."") ),
              'callback_url' => 'https://webhook.site/778c289e-0247-4c98-865f-5dc0a922f1e9',
              'description' => $description
            )
         );
      
        $response = wp_remote_post( bcf7_get_url() . "/api/v3/bills", $args );
        $apiBody = json_decode( wp_remote_retrieve_body($response) );
        $bill_url = $apiBody->url;
    
        $content  = '<div>';
        $content .= '<p class="text-center">Redirecting to Billplz...</p>';
        $content .= '</div>';
        $content .= '<script>window.location.replace("' . $bill_url . '");</script>';

        $allowed_tags = array('div' => array(), 'p' => array(), 'script' => array());

        echo wp_kses($content, $allowed_tags);
    }

    public function success_page()
    {
        if (isset($_GET['post']) && isset($_GET['action'])) return;
        
        $payment_id = $_GET['payment-id'];

        $url         = htmlentities($_SERVER['QUERY_STRING']);
        parse_str(html_entity_decode($url), $query);

        $transaction_id = $query['billplz']['id'];
        $paid_at = $query['billplz']['paid_at'];
        $bill_url = bcf7_get_url()."/bills/".$transaction_id;

        global $wpdb;
        $table_name = $wpdb->prefix . "bcf7_payment";

        if (isset($_GET['payment-id']) and ('true' == $query['billplz']['paid'])) {
            echo "<h2>Thank you for your payment!</h2>";
            echo "<p>Payment Status: Completed</p>";
            echo "<p>Transaction ID: $transaction_id</p>";

            
            $wpdb->update( $table_name, array( 
                'status' => 'completed', 
                'transaction_id' => $transaction_id, 
                'paid_at' => $paid_at,
                'bill_url' => $bill_url
                ), 
                array( 'ID' => $payment_id ) 
            );

        } else {
            echo "<h2>Sorry, your payment was unsuccessful</h2>";
            echo "<p>Payment Status: Failed.</p>";
            echo "<p>Please repay the bill <a href=".bcf7_get_url().'/bills/'.$transaction_id.">here</a></p>";

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
