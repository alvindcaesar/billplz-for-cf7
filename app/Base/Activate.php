<?php

namespace BillplzCF7\Base;

class Activate 
{
  public static function activate()
  {
    \BillplzCF7\Model\PaymentDatabase::create_db();
    self::create_pages();
  }

  private static function create_pages()
  {
    $page_title = "BCF7 Payment Confirmation";
    $page_content = "<!-- wp:shortcode -->[bcf7_payment_confirmation]<!-- /wp:shortcode -->";

    $page = get_page_by_title( $page_title );

    $instance = new self();

    if (! empty( $page ) and ($page->post_content == $page_content) ) {
      return;
    } else {
      $id = $instance->page_id();
      $instance->save_id($id);
    }
  }

  private function page_id()
  {
     return wp_insert_post(
      array(
        'post_title'     => __( 'BCF7 Payment Confirmation', 'billplz-for-cf7' ),
        'post_name'      => 'bcf7-payment-confirmation',
        'post_content'   => "<!-- wp:shortcode -->[bcf7_payment_confirmation]<!-- /wp:shortcode -->",
        'post_status'    => 'publish',
        'post_author'    => get_current_user_id(),
        'post_type'      => 'page',
        'comment_status' => 'closed'
      )
    );
  }

  private function save_id( $id )
  {
    $options = get_option("bcf7_general_settings");

    if ( $options ) {
      if ( ! empty( $options["bcf7_redirect_page"] )) {
        return;
      } else {
        $options["bcf7_redirect_page"] = $id;
        update_option( "bcf7_general_settings", $options );
      }
    } else {
      $options = array(
          "bcf7_mode" => "",
          "bcf7_form_select" => "",
          "bcf7_redirect_page" => $id
        );
        add_option( "bcf7_general_settings", $options );
    }
  }
}

