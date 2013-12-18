<?php

class RCP_Gifts_Checkout {
		
	public function __construct() {

		add_action( 'edd_purchase_form_before_cc_form', array( $this, 'fields'          )        );
		add_action( 'edd_checkout_error_checks',        array( $this, 'validate_fields' ), 10, 2 );
		add_action( 'edd_insert_payment',               array( $this, 'store_gift_data' ), 10, 2 );
		add_action( 'edd_complete_purchase',            array( $this, 'complete_gift'   ), 10, 2 );

	}

	private function cart_has_gift_product() {

		global $rcp_gifts;

		$ret  = false;
		$cart = edd_get_cart_contents();
		if( ! empty( $cart ) ) {

			foreach( $cart as $item ) {
				if( $rcp_gifts->is_gift_product( $item['id'] ) ) {
					$ret = true;
					break;
				}
			}
		}

		return $ret;

	}

	public function fields() {

		global $rcp_gifts;

		$items = edd_get_cart_contents();
		if( empty( $items ) )
			return;

		foreach( $items as $key => $item ) {
			
			if( ! $rcp_gifts->is_gift_product( $item['id'] ) )
				continue;

			echo '<fieldset class="rcp_gift_recipient_fields">';
				echo '<p class="rcp_gift_recipient_email_wrap">';
					echo '<label for="edd_rcp_gift[' . $key . '][name]">';
						echo __( 'Gift Recipient\'s Name', 'rcp-gifts' );
						echo '<span class="edd-required-indicator">*</span>';
					echo '</label>';
					echo '<span class="edd_description">' . __( 'Enter the name of the recipient of this gift', 'rcp-gifts' ) . '</label>';
					echo '<input type="text" name="edd_rcp_gift[' . $key . '][name]" id="edd_rcp_gift[' . $key . '][name]" class="edd-input" placeholder="' . __( 'Enter the recipient\'s name', 'rcp-gifts' ) . '"/>';
				echo '</p>';
				echo '<p class="rcp_gift_recipient_email_wrap">';
					echo '<label for="edd_rcp_gift[' . $key . '][email]">';
						echo __( 'Gift Recipient\'s Email', 'rcp-gifts' );
						echo '<span class="edd-required-indicator">*</span>';
					echo '</label>';
					echo '<span class="edd_description">' . __( 'Enter the email address of the recipient of this gift', 'rcp-gifts' ) . '</label>';
					echo '<input type="email" name="edd_rcp_gift[' . $key . '][email]" id="edd_rcp_gift[' . $key . '][email]" class="edd-input" placeholder="' . __( 'Enter the recipient\'s email', 'rcp-gifts' ) . '"/>';
				echo '</p>';
				echo '<p class="rcp_gift_recipient_message_wrap">';
					echo '<label for="edd_rcp_gift[' . $key . '][message]">' . __( 'Message to Gift Recipient', 'rcp-gifts' ) . '</label>';
					echo '<span class="edd_description">' . __( 'Enter a message to send to the recipient of this gift', 'rcp-gifts' ) . '</label>';
					echo '<textarea name="edd_rcp_gift[' . $key . '][message]" id="edd_rcp_gift[' . $key . '][message]" class="edd-input" rows="7">' . __( 'Enter the a message to send to the recipient', 'rcp-gifts' ) . '</textarea>';
				echo '</p>';
				echo '<p class="rcp_gift_recipient_send_wrap">';
					echo '<label for="edd_rcp_gift[' . $key . '][send]">' . __( 'Send Gift to Receipient via Email?', 'rcp-gifts' );
						echo '<input type="checkbox" name="edd_rcp_gift[' . $key . '][send]" id="edd_rcp_gift[' . $key . '][send]" class="edd-checkbox" checked="checked"/>';
					echo '</label>';
					echo '<span class="edd_description">' . __( 'Uncheck this if you would like to deliver the gift yourself', 'rcp-gifts' ) . '</label>';
				echo '</p>';
			echo '</fieldset>';

		}

	}

	public function validate_fields( $valid_data, $post_data ) {

		global $rcp_gifts;

		if( ! $this->cart_has_gift_product() )
			return;

		$items = edd_get_cart_contents();

		$gifts = $_POST[ 'edd_rcp_gift' ];

		foreach( $items as $key => $item ) {
			
			if( ! $rcp_gifts->is_gift_product( $item['id'] ) )
				continue;

			$email = $gifts[ $key ][ 'email' ] ? sanitize_text_field( $gifts[ $key ][ 'email' ] ) : false;
			$name  = $gifts[ $key ][ 'name' ] ? sanitize_text_field( $gifts[ $key ][ 'name' ] ) : false;

			if( empty( $email ) ) {
				edd_set_error( 'empty_gift_email_' . $key, __( 'Please enter an email address for the gift recipient', 'rcp-gifts' ) );
			}

			if( empty( $name ) ) {
				edd_set_error( 'empty_gift_name_' . $key, __( 'Please enter the recipient\'s name', 'rcp-gifts' ) );
			}

			if( ! is_email( $email )  ) {
				edd_set_error( 'invalid_gift_email_' . $key, __( 'Please enter a valid email address for the gift recipient', 'rcp-gifts' ) );
			}
		}

	}	

	public function store_gift_data( $payment_id = 0, $payment_data = array() ) {

		if( ! $this->cart_has_gift_product() )
			return;

		$items = edd_get_cart_contents();

		$gifts = $_POST[ 'edd_rcp_gift' ];

		update_post_meta( $payment_id, '_edd_payment_is_rcp_gift', '1' );
		update_post_meta( $payment_id, '_edd_rcp_gift_data', $gifts );

	}

	public function complete_gift( $payment_id = 0 ) {

		global $rcp_gifts;

		if( ! get_post_meta( $payment_id, '_edd_payment_is_rcp_gift', true ) )
			return;

		$gifts = get_post_meta( $payment_id, '_edd_rcp_gift_data', true );

		if( empty( $gifts ) )
			return;

		foreach( $gifts as $gift ) {

			$name    = $gift['name'];
			$email   = $gift['email'];
			$message = ! empty( $gift['message'] ) ? $gift['message'] : '';
			$send    = isset( $gift['send'] );

			$rcp_gifts->create_discount( $name, $email, $payment_id );

			if( $send ) {
				$rcp_gifts->send_recipient_email( $name, $email, $message, $payment_id );
			}
		}

	}
}