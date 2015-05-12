<?php
/*
 * Plugin Name: Restrict Content Pro - Gift Memberships
 * Description: Purchase RCP discount codes as gifts through Easy Digital Downloads
 * Author: Pippin Williamson
 * Version: 1.0
 */

class RCP_Gift_Memberships {

	private $admin;

	private $gifts;

	private $checkout;

	public function __construct() {

		$this->includes();


	}


	public function includes() {

		include dirname( __FILE__ ) . '/includes/class-gifts.php';
		include dirname( __FILE__ ) . '/includes/class-gifts-admin.php';
		include dirname( __FILE__ ) . '/includes/class-gifts-checkout.php';

		$this->admin    = new RCP_Gifts_Admin;
		$this->gifts    = new RCP_Gift_Products;
		$this->checkout = new RCP_Gifts_Checkout;

	}

	public function gift_subscription_level( $download_id = 0 ) {
		return get_post_meta( $download_id, '_rcp_gift_subscription_level', true );

	}

	public function is_gift_product( $download_id = 0 ) {
		$gift = get_post_meta( $download_id, '_rcp_gift_product', true );
		return ! empty( $gift );
	}

	public function is_gift_multiuse( $download_id = 0 ) {
		$gift = get_post_meta( $download_id, '_rcp_gift_multiuse', true );
		return ! empty( $gift );
	}

	public function gift_expires( $download_id = 0 ) {
		return get_post_meta( $download_id, '_rcp_gift_expires', true );
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

		global $edd_options;

		$db = new RCP_Discounts;

		$site_name = get_bloginfo( 'name' );
		$discount  = $db->get_by( 'code', md5( $name . $email . $payment_id ) );

		$subject = sprintf( __( 'Gift Certificate to %s', 'rcp-gifts' ), $site_name );

		$from_name = isset( $edd_options['from_name'] ) ? $edd_options['from_name'] : get_bloginfo('name');
		$from_email = isset( $edd_options['from_email'] ) ? $edd_options['from_email'] : get_option('admin_email');

		$body = '<p>' . __( "Hello!", "rcp-gifts" ) . '</p>';
		$body .= '<p>' . sprintf( __( "Someone has gifted you a membership to %s", "rcp-gifts" ), $site_name ) . '</p>';
		if( ! empty( $gift_message ) && __( 'Enter the a message to send to the recipient', 'rcp-gifts' ) != $gift_message ) {
			$body .= '<p>' . __( "The following message was included with the gift: ", "rcp-gifts" ) . '</p>';
			$body .= '<blockquote>' . $gift_message . '</blockquote>';
		}
		$body .= '<p>' . sprintf( __( "Enter %s during registration to redeem your gift.", "rcp-gifts" ), $discount->code ) . '</p>';
		$body .= '<p>' . sprintf( __( "Visit %s to claim your membership gift.", "rcp-gifts" ), '<a href="' . home_url() . '">' . home_url() . '</a>' ) . '</p>';

		$emails = new EDD_Emails;
		$emails->__set( 'from_address', $from_email );
		$emails->__set( 'from_name', $from_name );
		$emails->send( $email, $subject, $body );

	}

	public function create_discount( $name = '', $email = '', $payment_id = 0, $download_id = '' ) {

		if( ! class_exists( 'RCP_Discounts' ) )
			return false;

		$db = new RCP_Discounts;

		$code     = md5( $name . $email . $payment_id );
		$multiuse = $this->is_gift_multiuse($download_id) ? 0 : 1;
		$expires  = $this->gift_expires($download_id);
		$sublevel = $this->gift_subscription_level($download_id);

		$discount = array(
			'name'            => $name,
			'description'     => sprintf( __( 'Gifted discount for %s', 'rcp-gifts' ), $name ),
			'amount'          => '100',
			'status'          => 'active',
			'unit'            => '%',
			'code'            => $code,
			'max_uses' 	      => $multiuse,
			'expiration'	  => $expires,
			'subscription_id' => $sublevel
		);

		$discount_id = $db->insert( $discount );

		$note = sprintf( __( 'Purchased as gift for %s. Coupon: %s', 'rcp-gifts' ), $name, $code );

		// Store a payment note about this gift
		edd_insert_payment_note( $payment_id, $note );

		// store discount ids for each gifted product
		add_post_meta( $payment_id, '_edd_rcp_gift_id', $discount_id, true );

	}

}
$rcp_gifts = new RCP_Gift_Memberships;