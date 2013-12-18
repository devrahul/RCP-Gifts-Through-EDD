<?php
/*
 * Plugin Name: Restrict Content Pro - Gift Memberships
 * Description: Purchase RCP discount codes as gifts through Easy Digital Downloads
 * Author: Pippin Williamson
 * Version: 1.0
 */

class RCP_Gift_Memberships {

	private $admin;

	private $checkout;

	public function __construct() {

		$this->includes();

	}

	public function includes() {

		include dirname( __FILE__ ) . '/includes/class-gifts-admin.php';
		include dirname( __FILE__ ) . '/includes/class-gifts-checkout.php';

		$this->admin    = new RCP_Gifts_Admin;
		$this->checkout = new RCP_Gifts_Checkout;

	}

	public function is_gift_product( $download_id = 0 ) {
		$gift = get_post_meta( $download_id, '_rcp_gift_product', true );
		return ! empty( $gift );
	}

	public function payment_was_gift( $payment_id = 0 ) {
		$gift = get_post_meta( $payment_id, '_edd_payment_is_rcp_gift', true );
		return ! empty( $gift );
	}

	public function get_gifts_of_payment( $payment_id = 0 ) {
		return get_post_meta( $payment_id, '_edd_rcp_gift_data', true );
	}

	public function send_recipient_email( $name = '', $email = '', $gift_message = '', $payment_id = 0 ) {

		if( ! class_exists( 'RCP_Discounts' ) )
			return false;

		$db = new RCP_Discounts;

		$site_name = get_bloginfo( 'name' );
		$discount  = $db->get_by( 'code', md5( $name . $email . $payment_id ) );

		$subject = sprintf( __( 'Gift Certificate to %s', 'rcp-gifts' ), $site_name );

		$message  = __( "Hello!\n\n", "rcp-gifts" );
		$message .= sprintf( __( "Someone has gifted you a membership to %s\n\n", "rcp-gifts" ), $site_name );
		
		if( ! empty( $gift_message ) && __( 'Enter the a message to send to the recipient', 'rcp-gifts' ) != $gift_message ) {
			$message .= __( "The following message was included with the gift: \n\n", "rcp-gifts" );
			$message .= $gift_message . "\n\n";
		}

		$message .= sprintf( __( "Enter %s during registration to redeem your gift.\n\n", "rcp-gifts" ), $discount->code );
		
		$message .= sprintf( __( "Visit %s to claim your membership gift.", "rcp-gifts" ), home_url() );

		wp_mail( $email, $subject, $message );

	}

	public function create_discount( $name = '', $email = '', $payment_id = 0 ) {

		if( ! class_exists( 'RCP_Discounts' ) )
			return false;

		$db = new RCP_Discounts;

		$code = md5( $name . $email . $payment_id );

		$discount = array(
			'name'           => $name,
			'description'    => sprintf( __( 'Gifted discount for %s', 'rcp-gifts' ), $name ),
			'amount'         => '100',
			'status'         => 'active',
			'unit'           => '%',
			'code'           => $code,
			'max_uses' 	     => 1
		);

		$discount_id = $db->insert( $discount );

		$note = sprintf( __( 'Purchased as gift for %s. Coupon: %s', 'rcp-gifts' ), $name, $code );

		// Store a payment note about this gift
		edd_insert_payment_note( $payment_id, $note );

	}

}
$rcp_gifts = new RCP_Gift_Memberships;