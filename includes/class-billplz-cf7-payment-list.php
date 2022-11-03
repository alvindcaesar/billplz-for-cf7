<?php

require_once BILLPLZ_CF7_PLUGIN_PATH . "includes/lib/class-wp-list-table.php";

class Billplz_CF7_Payment_List extends WP_List_Table
{
  // Define $table_data property
  private $table_data;

  // The database table name
  private function get_db_name()
  {
    global $wpdb;
    $db = $wpdb->prefix . "billplz_cf7_payment";
    return $db;
  }

  // Get table data
  private function get_table_data( $search_term = "" )
  {
    global $wpdb;

    if (! empty($search_term) ) {
      $payment_results = $wpdb->get_results(
        "SELECT * from {$this->get_db_name()} WHERE name LIKE '%$search_term%' OR transaction_id LIKE '%$search_term%'"
      );
    } else {
      $payment_results = $wpdb->get_results(
        "SELECT * from {$this->get_db_name()}"
      );
    }

    $payment_array = array();
    if (count($payment_results) > 0) {
      foreach ($payment_results as $index => $payment_data) {
        $payment_array[] = array(
          "id"             => $payment_data->id,
          "customer"       => $payment_data->name,
          "form"           => $payment_data->form_title . " (ID: ".$payment_data->form_id.")",
          "amount"         => $payment_data->amount,
          "transaction_id" => $payment_data->transaction_id,
          "date"           => date('F j, Y \a\t\ g:i a', strtotime($payment_data->created_at)),
          "status"         => $payment_data->status
        );
      }
    }
    return $payment_array;
  }

  // Define table columns
  public function get_columns()
  {
      $columns = array(
          "cb"             => "<input type='checkbox' />",
          "id"             => "ID",
          "customer"       => "Customer",
          "form"           => "Form Name",
          "amount"         => "Amount (RM)",
          "transaction_id" => "Bill ID",
          "date"           => "Date",
          "status"         => "Payment Status",
      );

      return $columns;
  }

  // Display message when there are no records.
  public function no_items()
  {
    _e( 'No payment records.', BILLPLZ_CF7_TEXT_DOMAIN );
  }

  // Bind table with columns, data and etc
  public function prepare_items()
  {

    $this->process_bulk_action();
    $columns  = $this->get_columns();
    $hidden   = array();
    $sortable = array();
    $primary  = 'id';
    $this->_column_headers = array( $columns, $hidden, $sortable, $primary );

    $search_term = isset($_POST['s']) ? trim($_POST['s']) : "";
    $this->table_data = $this->get_table_data( $search_term );

    
    $per_page = 4;
    $current_page = $this->get_pagenum();
    $total_items = count($this->table_data);
    $this->set_pagination_args( array(
      "total_items" => $total_items,
      "per_page"    => $per_page
    ));
    $this->items = array_slice($this->table_data, (($current_page - 1) * $per_page), $per_page);
  }

  // Set value for each column
  public function column_default($item, $column_name)
  {
    switch ($column_name) {
      case 'id':
      case 'customer':
      case 'form':
      case 'amount':
      case 'transaction_id':
      case 'date':
      case 'status':
        return $item[$column_name];
    }
  }

  // Add a checkbox in the first column
  public function column_cb($item)
  {
    return sprintf(
      '<input type="checkbox" name="payment_id[]" value="%s" />',
      $item['id']
    );
  }

  // Show bulk action dropdown
  public function get_bulk_actions()
  {
    $actions = array(
      "delete" => "Delete"
    );
    return $actions;
  }

  public function process_bulk_action()
  {
    $action = $this->current_action();
    if ("delete" === $action) {
      $delete_ids = esc_sql( $_POST['payment_id'] );

      foreach ($delete_ids as $delete_id) {
        global $wpdb;
        $wpdb->query("DELETE FROM {$this->get_db_name()} WHERE id= {$delete_id} ");
      }
      $text = (count($delete_ids) > 1) ? "payments" : "payment";
      add_action( 'admin_notices', $this->bulk_action_notice( count($delete_ids), $text ) );
      $this->table_data;
    }
  }

  public function bulk_action_notice($arg, $count)
  {
    printf('<div id="message" class="updated notice is-dismissable"><p>' . __('%d %s deleted.', BILLPLZ_CF7_TEXT_DOMAIN) . '</p></div>', $arg, $count);
  }
}