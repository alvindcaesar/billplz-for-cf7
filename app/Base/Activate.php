<?php
/**
 * Activate class file.
 *
 * @package BillplzCF7
 */

namespace BillplzCF7\Base;

use WP_Query;

/**
 * Activate Class.
 */
class Activate {
	/**
	 * Method to activate the plugin.
	 *
	 * @return void
	 */
	public function activate() {
		\BillplzCF7\Model\PaymentDatabase::create_db();
		ob_start();
		$this->create_confirmation_page();
		$this->create_example_form();
		ob_end_clean();
	}

	/**
	 * Method to create a page.
	 *
	 * @return void
	 */
	private function create_confirmation_page() {
		$page_title   = esc_html__( 'BCF7 Payment Confirmation', BCF7_TEXT_DOMAIN );

		$args = array(
			'post_type' => 'page',
			'post_status' => 'publish',
			'posts_per_page' => 1,
			's' => $page_title
		);

		$query = new WP_Query( $args );
		
		if ( $query->have_posts() ) {
			wp_reset_postdata();
			return;
		} else {
			$redirect_page = $this->page_id();
			$this->save_id( $redirect_page );
		}
	}

	/**
	 * Method to create a BCF7 Example Form.
	 *
	 * @return void
	 */
	private function create_example_form() {

		$args = array(
			'post_type' => 'wpcf7_contact_form',
			'posts_per_page' => 1,
			'post_status' => 'publish',
			's' => 'BCF7 Example Payment Form'
		);
		
		$query = new WP_Query( $args );
		
		if ( $query->have_posts() ) {
			// Contact Form 7 form with the given title exists
			wp_reset_postdata();
			return;
		} else {
			// Contact Form 7 form with the given title does not exist
			$post_data = array(
				'post_title' => 'BCF7 Example Payment Form',
				'post_content' => 'Payment Form Example',
				'post_status' => 'publish',
				'post_author' => get_current_user_id(),
				'post_type' => 'wpcf7_contact_form',
				'comment_status' => 'closed',
			);
		
			// Insert the post into the database
			$post_id = wp_insert_post($post_data);
		
			$form = '
			<label> Name
				[text* bcf7-name] </label>
		
			<label> Your email
				[email* bcf7-email] </label>
		
			<label> Phone
				[tel* bcf7-phone] </label>
		
			<label> Amount (RM)
				[number* bcf7-amount] </label>
		
		
			[submit "Submit"]';
		
			add_post_meta($post_id, '_additional_settings', 'skip_mail: on');
			add_post_meta($post_id, '_form', $form);
		}
	}

	/**
	 * Method to insert a page and return its ID.
	 *
	 * @return int The ID of the inserted page.
	 */
	private function page_id() {
		$post_id = wp_insert_post(
			array(
				'post_title'     => esc_html__( 'BCF7 Payment Confirmation', 'billplz-for-cf7' ),
				'post_name'      => 'bcf7-payment-confirmation',
				'post_content'   => '<!-- wp:shortcode -->[bcf7_payment_confirmation]<!-- /wp:shortcode -->',
				'post_status'    => 'publish',
				'post_author'    => get_current_user_id(),
				'post_type'      => 'page',
				'comment_status' => 'closed',
			)
		);

		return $post_id;
	}

	/**
	 * Method to save the page ID in the options table.
	 *
	 * @param int $id The ID of the page.
	 *
	 * @return void
	 */
	private function save_id( $redirect_page_id ) {
		$options = get_option( 'bcf7_general_settings' );

		if ( $options ) {
			if ( ! empty( $options['bcf7_redirect_page'] ) && $options['bcf7_redirect_page'] === $redirect_page_id ) {
				return;
			} else {
				$options['bcf7_redirect_page'] = $redirect_page_id;
				update_option( 'bcf7_general_settings', $options );
			}
		} else {
			$options = array(
				'bcf7_mode'            => '1',
				'bcf7_form_select'     => '',
				'bcf7_redirect_page'   => $redirect_page_id,
			);
			add_option( 'bcf7_general_settings', $options );
		}
	}
}