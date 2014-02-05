<?php

class RCP_Gift_Products {

	public function __construct(){

	}

	public function menu_page(){
		add_submenu_page( 'rcp-members', __( 'Gifts', 'rcp' ), __( 'Gifts', 'rcp' ),'manage_options', 'rcp-gifts', array($this,'draw_page') );
	}

	public function draw_page(){

		global $wpdb;

		$codes 	= rcp_get_discounts();
		$args 	= array( 'post_type' => 'download', 'meta_key' => '_rcp_gift_product' );
		$gifts  = get_posts( $args );

		if ( isset( $_GET['rcp-action'] ) && $_GET['rcp-action'] == 'add_gift' ) {
			require_once dirname( __FILE__ ) . '/includes/add-gift.php';
		}

		?>
		<div class="wrap">
			<h2><?php _e( 'Gifts', 'edd' ); ?><a href="<?php echo add_query_arg( array( 'rcp-action' => 'add_gift' ) ); ?>" class="add-new-h2">Add New</a></h2>
			<?php

				// get all discounts ids
				$getdiscountids = $wpdb->get_col( "SELECT meta_value FROM $wpdb->postmeta WHERE meta_key = '_edd_rcp_gift_id';");
				$discount_ids 	= implode( ',',$getdiscountids);
				$discounts 		= $wpdb->get_results( "SELECT * FROM rcp_discounts WHERE id IN(".$discount_ids.");");

				var_dump($discounts);


			?>
		</div>
	<?php
	}
}