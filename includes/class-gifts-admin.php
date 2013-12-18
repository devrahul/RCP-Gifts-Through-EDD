<?php

class RCP_Gifts_Admin {

	public function __construct() {

		add_action( 'edd_meta_box_fields',     array( $this, 'metabox'     ), 999 );
		add_filter( 'edd_metabox_fields_save', array( $this, 'save_fields' )      );

	}

	public function metabox( $post_id = 0 ) {
		global $rcp_gifts;

		$is_gift = $rcp_gifts->is_gift_product( $post_id );

		echo '<p>';
			echo '<strong>' . __( 'Gift Creation', 'rcp-gifts' ) . '</strong><br/>';
		echo '</p>';
		echo '<p>';	
			echo '<input type="checkbox" name="_rcp_gift_product" id="_rcp_gift_product" value="1"' . checked( true, $is_gift, false ) . '/>';
			echo '<label for="_rcp_gift_product">' . __( 'Enable RCP Gift creation for this product', 'rcp-gifts' ) . '</label>';
		echo '</p>';
	}

	public function save_fields( $fields = array() ) {
		$fields[] = '_rcp_gift_product';
		return $fields;
	}
}