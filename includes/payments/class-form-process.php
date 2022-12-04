<?php

class Billplz_CF7_Form_Process
{
    public function __construct()
    {
        add_action('wpcf7_before_send_mail', array($this, 'process_data'));
        add_filter( 'wpcf7_load_js', '__return_false' );
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
                $status         = 'pending';

                $payment_id = $this->record_data($form_id, $form_title, $name, $phone, $email, $amount, $transaction_id, $mode, $status);

                $description = "Payment for $form_title";
                $this->process_payment($name, $email, $phone, $amount, $description, $payment_id);
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

    public function process_payment($name, $email, $phone, $amount, $description, $payment_id)
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
              'mobile' => (isset($phone) ? $phone : ""),
              'redirect_url' => add_query_arg(array('bcf7-listener' => 'billplz', 'payment-id' => $payment_id), site_url("?page_id=".bcf7_general_option('bcf7_redirect_page')."") ),
              'callback_url' => add_query_arg(array('bcf7-listener' => 'billplz', 'payment-id' => $payment_id), 'https://webhook.site/1a7bc4cd-01b8-4027-a9c5-34ad74e89fcc'),
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
}