<?php
// TODO: wpdb->prepare()
class Billplz_CF7_Form_Process
{
    public function __construct()
    {
        add_action('wpcf7_before_send_mail', array($this, 'process_data'));
    }

    public function process_data($contact_form)
    {
        $id = $contact_form->id;

        if (get_option("billplz_cf7_general_settings")["billplz_cf7_form_select"] == $id) {
            $submission = WPCF7_Submission::get_instance();

            if ($submission) {
                $form_id        = $submission->get_contact_form()->id;
                $form_title     = $submission->get_contact_form()->title;
                $posted_data    = $submission->get_posted_data();
                $name           = $posted_data['payment-name'];
                $email          = $posted_data['payment-email'];
                $amount         = $posted_data['payment-amount'];
                $phone          = $posted_data['phone-num'];
                $transaction_id = 'billplz-bill-id';
                $mode           = 'Live';
                $status         = 'Pending';

                $this->record_data($form_id, $form_title, $name, $phone, $email, $amount, $transaction_id, $mode, $status);
            }

        }
    }

    public function record_data($form_id, $form_title, $name, $phone, $email, $amount, $transaction_id, $mode, $status)
    {
        global $wpdb;

        $table_name = $wpdb->prefix . "billplz_cf7_payment";

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
}
