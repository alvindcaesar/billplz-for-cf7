<?php
/**
 * Column neeed to create:
 * id, data,amount, transaction_id,email, mode, status, completed_at, created_at, updated at
 */
class Billplz_CF7_Payment_DB
{
    public static function create_db()
    {
        global $wpdb;

        $table_name = $wpdb->prefix . "bcf7_payment";

        $charsetCollate = $wpdb->get_charset_collate();

        if ($wpdb->get_var("SHOW TABLES LIKE '$table_name'") != $table_name) {

            $query = "CREATE TABLE $table_name(
            id bigint (20) unsigned NOT NULL AUTO_INCREMENT,
            form_id bigint (20) NOT NULL DEFAULT 0,
            form_title varchar (60) NOT NULL DEFAULT '',
            name varchar (60) NOT NULL DEFAULT '',
            phone varchar (60) NOT NULL DEFAULT '',
            amount decimal (6,2) unsigned NOT NULL DEFAULT 0,
            transaction_id varchar (60) NOT NULL DEFAULT '',
            email varchar (60) NOT NULL DEFAULT '',
            mode varchar (60) NOT NULL DEFAULT '',
            status varchar (60) NOT NULL DEFAULT '',
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY  (id)
          ) $charsetCollate";

            require_once ABSPATH . 'wp-admin/includes/upgrade.php';
            dbDelta($query);

        }
    }
}
