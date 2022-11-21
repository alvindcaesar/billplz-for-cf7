<?php

class Billplz_CF7_Form_Process
{
    public function __construct()
    {
        add_action('wpcf7_before_send_mail', array($this, 'process_data'));
        // add_action('wp_footer', array($this, 'redirect'));

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

                $this->record_data($form_id, $form_title, $name, $phone, $email, $amount, $transaction_id, $mode, $status);

                $this->process_payment($name, $email, $amount, $status);
            }

        }
    }
/**
 * Undocumented function
 *
 * @param [type] $form_id
 * @param [type] $form_title
 * @param [type] $name
 * @param [type] $phone
 * @param [type] $email
 * @param [type] $amount
 * @param [type] $transaction_id
 * @param [type] $mode
 * @param [type] $status
 * @return void
 */
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
    }

    public function process_payment($name, $email, $amount, $description)
    {
        $url = 'https://www.billplz-sandbox.com/api/v3/bills';
        $api_key = base64_encode(bcf7_api_option("bcf7_sandbox_secret_key"));

        $args = array(
          'headers' => array(
            'Authorization' => 'Basic '. $api_key . ':',
            ),
            'body' => array(
              'collection_id' => bcf7_api_option("bcf7_sandbox_collection_id"),
              'email' => $email,
              'name' => $name,
              'amount' => $amount * 100,
              'redirect_url' => site_url(),
              'callback_url' => 'https://webhook.site/778c289e-0247-4c98-865f-5dc0a922f1e9',
              'description' => $description
            )
         );
      
        $response = wp_remote_post($url, $args);
        $apiBody = json_decode(wp_remote_retrieve_body($response));
        $bill_url = $apiBody->url;
        header("Location: $bill_url");
        die;
    }

    public function redirect($url)
    {
        
    }
}
