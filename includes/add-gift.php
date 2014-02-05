<?php
/**
 * Add Discount Page
 *
 * @package     EDD
 * @subpackage  Admin/Discounts
 * @copyright   Copyright (c) 2013, Pippin Williamson
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       1.0
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;
$downloads = get_posts( array( 'post_type' => 'download', 'nopaging' => true ) );
?>
<h2><?php _e( 'Add New Gift', 'edd' ); ?> - <a href="<?php echo admin_url( 'admin.php?page=rcp-gifts' ); ?>" class="button-secondary"><?php _e( 'Go Back', 'edd' ); ?></a></h2>
<form id="rcp-add-gift" action="" method="POST">
	<table class="form-table">
		<tbody>
			<tr class="form-field">
				<th scope="row" valign="top">
					<label for="edd-name"><?php _e( 'Name', 'edd' ); ?></label>
				</th>
				<td>
					<input name="name" id="edd-name" type="text" value="" style="width: 300px;"/>
					<p class="description"><?php _e( 'The name of this discount', 'edd' ); ?></p>
				</td>
			</tr>
			<tr class="form-field">
				<th scope="row" valign="top">
					<label for="edd-code"><?php _e( 'Code', 'edd' ); ?></label>
				</th>
				<td>
					<input type="text" id="edd-code" name="code" value="" style="width: 300px;"/>
					<p class="description"><?php _e( 'Enter a code for this discount, such as 10PERCENT', 'edd' ); ?></p>
				</td>
			</tr>
			<tr class="form-field">
				<th scope="row" valign="top">
					<label for="edd-type"><?php _e( 'Type', 'edd' ); ?></label>
				</th>
				<td>
					<select name="type" id="edd-type">
						<option value="percent"><?php _e( 'Percentage', 'edd' ); ?></option>
						<option value="flat"><?php _e( 'Flat amount', 'edd' ); ?></option>
					</select>
					<p class="description"><?php _e( 'The kind of discount to apply for this discount.', 'edd' ); ?></p>
				</td>
			</tr>
			<tr class="form-field">
				<th scope="row" valign="top">
					<label for="edd-amount"><?php _e( 'Amount', 'edd' ); ?></label>
				</th>
				<td>
					<input type="text" id="edd-amount" name="amount" value="" style="width: 40px;"/>
					<p class="description"><?php _e( 'The amount of this discount code.', 'edd' ); ?></p>
				</td>
			</tr>


			<tr class="form-field">
				<th scope="row" valign="top">
					<label for="edd-expiration"><?php _e( 'Expiration date', 'edd' ); ?></label>
				</th>
				<td>
					<input name="expiration" id="edd-expiration" type="text" style="width: 300px;" class="edd_datepicker"/>
					<p class="description"><?php _e( 'Enter the expiration date for this discount code in the format of mm/dd/yyyy. For no expiration, leave blank', 'edd' ); ?></p>
				</td>
			</tr>
			<tr class="form-field">
				<th scope="row" valign="top">
					<label for="edd-max-uses"><?php _e( 'Max Uses', 'edd' ); ?></label>
				</th>
				<td>
					<input type="text" id="edd-max-uses" name="max" value="" style="width: 40px;"/>
					<p class="description"><?php _e( 'The maximum number of times this discount can be used. Leave blank for unlimited.', 'edd' ); ?></p>
				</td>
			</tr>
		</tbody>
	</table>
	<p class="submit">
		<input type="hidden" name="edd-action" value="add_gift"/>
		<input type="hidden" name="edd-redirect" value="<?php echo esc_url( admin_url( 'admin.php?page=rcp-gifts' ) ); ?>"/>
		<input type="hidden" name="edd-discount-nonce" value="<?php echo wp_create_nonce( 'edd_discount_nonce' ); ?>"/>
		<input type="submit" value="<?php _e( 'Add Gift Code', 'edd' ); ?>" class="button-primary"/>
	</p>
</form>
