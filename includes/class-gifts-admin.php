<?php

class RCP_Gifts_Admin {

	public function __construct() {
		add_action( 'edd_meta_box_fields',     array( $this, 'metabox'     ), 999 );
		add_filter( 'edd_metabox_fields_save', array( $this, 'save_fields' )      );
	}


	public function metabox( $post_id = 0 ) {
		global $rcp_gifts;

		$is_gift     = $rcp_gifts->is_gift_product( $post_id );
		$is_multiuse = $rcp_gifts->is_gift_multiuse( $post_id );
		$expires     = $rcp_gifts->gift_expires( $post_id );

		$gift_level  = $rcp_gifts->gift_subscription_level( $post_id );
		$levels      = rcp_get_subscription_levels();

		echo '<p>';
			echo '<strong>' . __( 'Gift Creation', 'rcp-gifts' ) . '</strong><br/>';
		echo '</p>';
		echo '<p>';	
			echo '<input type="checkbox" name="_rcp_gift_product" id="_rcp_gift_product" value="1"' . checked( true, $is_gift, false ) . '/>';
			echo '<label for="_rcp_gift_product">' . __( 'Enable RCP Gift creation for this product', 'rcp-gifts' ) . '</label>';
		echo '</p>';
		
		// enable multi-use
		echo '<p>';	
			echo '<input type="checkbox" name="_rcp_gift_multiuse" id="_rcp_gift_multiuse" value="1"' . checked( true, $is_multiuse, false ) . '/>';
			echo '<label for="_rcp_gift_multiuse">' . __( 'Enable coupon to be used multiple times.', 'rcp-gifts' ) . '</label>';
		echo '</p>';
		
		// set expiration date
		echo '<p>';	
			echo '<input type="date" class="datepicker" name="_rcp_gift_expires" id="_rcp_gift_expires" value="' . esc_attr( $expires ) . '">';
			echo '<label for="_rcp_gift_expires">' . __( 'Select optional expiration date.', 'rcp-gifts' ) . '</label>';
		echo '</p>';
		
		// choose subscription level
		echo '<p>';
			echo '<select name="_rcp_gift_subscription_level" id="_rcp_gift_subscription_level"/>&nbsp;';
	            foreach ( $levels as $level ) {

			       echo '<option value="' . absint( $level->id ) . '"' . selected( $gift_level, $level->id, false ) . '>' . $level->name . '</option>';

	            }
            echo '</select>';
		echo '</p>';

	}

	public function save_fields( $fields = array() ) {
		$fields[] = '_rcp_gift_product';
		$fields[] = '_rcp_gift_multiuse';
		$fields[] = '_rcp_gift_expires';
		$fields[] = '_rcp_gift_subscription_level';
		return $fields;
	}
}