<?php

require_once BCF7_PLUGIN_PATH . "includes/lib/class-wp-list-table.php";

class Billplz_CF7_Payment_List extends WP_List_Table
{
  // Define $table_data property
  private $table_data;

  // The database table name
  private function get_db_name()
  {
    global $wpdb;
    $db = $wpdb->prefix . "bcf7_payment";
    return $db;
  }

  // Get table data
  private function get_table_data( $search_term = "", $status = "" )
  {
    global $wpdb;

    if (! empty($search_term) ) {
      $payment_results = $wpdb->get_results(
        "SELECT * from {$this->get_db_name()} WHERE name LIKE '%$search_term%' OR transaction_id LIKE '%$search_term%'"
      );
    } else {
      if ($status == "completed") {
        $payment_results = $wpdb->get_results(
          "SELECT * from {$this->get_db_name()} WHERE status= '{$status}'"
        );
      } elseif ($status == "pending"){
        $payment_results = $wpdb->get_results(
          "SELECT * from {$this->get_db_name()} WHERE status= '{$status}'"
        );
      } else {
        $payment_results = $wpdb->get_results(
          "SELECT * from {$this->get_db_name()} ORDER BY created_at DESC"
        );
      }
    }

    $payment_array = array();
    if (count($payment_results) > 0) {
      foreach ($payment_results as $index => $payment_data) {
        $payment_array[] = array(
          "id"             => $payment_data->id,
          "customer"       => $payment_data->name,
          "form"           => $payment_data->form_title . " (ID: ".$payment_data->form_id.")",
          "amount"         => $payment_data->amount,
          "transaction_id" => "<a href=".$payment_data->bill_url." target='_blank'>$payment_data->transaction_id</a>",
          "created_at"     => nl2br("Submitted on \n ".date('F j, Y \a\t\ g:i a', strtotime($payment_data->created_at))." "),
          "paid_at"        => ("0000-00-00 00:00:00" != $payment_data->paid_at) ? (nl2br("Paid on \n ".date('F j, Y \a\t\ g:i a', strtotime($payment_data->paid_at))." ")) : "-",
          "status"         => ucfirst($payment_data->status),
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
          "id"             => "Payment ID",
          "customer"       => "Customer",
          "form"           => "Form Name",
          "amount"         => "Amount (RM)",
          "transaction_id" => "Bill ID",
          "created_at"     => "Submitted",
          "paid_at"        => "Paid",
          "status"         => "Payment Status",
      );

      return $columns;
  }

  // Display message when there are no records.
  public function no_items()
  {
    _e( 'No payment records.', BCF7_TEXT_DOMAIN );
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

    $status = isset($_GET['status']) ? trim($_GET['status']) : "";
    $search_term = isset($_POST['s']) ? trim($_POST['s']) : "";
    $this->table_data = $this->get_table_data( $search_term, $status );

    
    $per_page = 50;
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
      case 'created_at':
      case 'paid_at':
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
      "delete" => "Delete",
      "mark_as_completed" => "Mark as Completed"
    );
    return $actions;
  }

  public function process_bulk_action()
  {
    $action = $this->current_action();

    if ("delete" === $action) {
      $list_ids = esc_sql( $_POST['payment_id'] );

      foreach ($list_ids as $id) {
        global $wpdb;
        $wpdb->query("DELETE FROM {$this->get_db_name()} WHERE id= {$id} ");
      }
      $text = (count($list_ids) > 1) ? "payments" : "payment";
      add_action( 'admin_notices', $this->bulk_action_notice( count($list_ids), $text, "deleted" ) );
      $this->table_data;
    }

    if ("mark_as_completed" === $action) {
      $list_ids = esc_sql( $_POST['payment_id'] );

      foreach ($list_ids as $id) {
        global $wpdb;
        $wpdb->update( $this->get_db_name(), array( 'status' => 'completed'), array( 'ID' => $id ) );
      }

      $text = (count($list_ids) > 1) ? "payments" : "payment";
      add_action( 'admin_notices', $this->bulk_action_notice( count($list_ids), $text, "updated" ) );
      $this->table_data;
    }
  }

  public function bulk_action_notice($count, $text, $status)
  {
    printf('<div id="message" class="updated notice is-dismissable"><p>' . __('%d %s %s.', BCF7_TEXT_DOMAIN) . '</p></div>', $count, $text, $status);
  }

  protected function get_views() 
  { 
    $completed = $this->get_status_count("completed");
    $pending   = $this->get_status_count("pending");

    $status_links = array(
        "all"       => __("<a class='".((! isset($_GET['status'])) ? 'current' : '')."' href='".remove_query_arg("status")."'>All <span class='count'>(".($completed + $pending).")</span></a>", BCF7_TEXT_DOMAIN),

        "completed" => __("<a class='".((isset($_GET['status']) && ($_GET['status'] == 'completed')) ? 'current' : '')."' href='".add_query_arg("status", "completed")."'>Completed <span class='count'>(".$completed.")</span></a>", BCF7_TEXT_DOMAIN),

        "pending"   => __("<a class='".((isset($_GET['status']) && ($_GET['status'] == 'pending')) ? 'current' : '')."' href='".add_query_arg("status", "pending")."'>Pending <span class='count'>(".$pending.")</span></a>", BCF7_TEXT_DOMAIN)
    );
    return $status_links;
  }

  public function get_status_count($status)
  {
    global $wpdb;

    if ("completed" === $status) {
      $query = $wpdb->get_results(
        "SELECT * from {$this->get_db_name()} WHERE status= '{$status}' "
      );
    } else {
      $query = $wpdb->get_results(
        "SELECT * from {$this->get_db_name()} WHERE status= '{$status}' "
      );
    }
    return count($query);
  }
}