<?php
/**
 * Email Order Items
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/emails/email-order-items.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see     https://woocommerce.com/document/template-structure/
 * @package WooCommerce\Templates\Emails
 * @version 11.0.0
 *
 * Giovanni's override: with the email_improvements feature off, order emails use a
 * four-column warehouse layout (item #, description, average on hand, sold by) that pairs
 * with the headings in emails/email-order-details.php. The email_improvements branch is a
 * verbatim copy of core so this file is cheap to re-sync on the next WooCommerce update.
 */

use Automattic\WooCommerce\Utilities\FeaturesUtil;

defined( 'ABSPATH' ) || exit;

$text_align  = is_rtl() ? 'right' : 'left';
$margin_side = is_rtl() ? 'left' : 'right';

$email_improvements_enabled = FeaturesUtil::feature_is_enabled( 'email_improvements' );
$price_text_align           = $email_improvements_enabled ? 'right' : 'left';
$block_email_editor_enabled = FeaturesUtil::feature_is_enabled( 'block_email_editor' );

foreach ( $items as $item_id => $item ) :
	$product        = $item->get_product();
	$sku            = '';
	$purchase_note  = '';
	$image          = '';
	$item_avg_stock = '';

	/**
	 * Email Order Item Visibility hook.
	 *
	 * @param bool                  $visible Whether the order item is visible.
	 * @param WC_Order_Item_Product $item    The item being displayed.
	 * @since 2.1.0
	 */
	if ( ! apply_filters( 'woocommerce_order_item_visible', true, $item ) ) {
		continue;
	}

	if ( is_object( $product ) ) {
		$sku            = $product->get_sku();
		$purchase_note  = $product->get_purchase_note();
		$image          = $product->get_image( $image_size );
		$item_avg_stock = $product->get_stock_quantity();
	}

	/**
	 * Email Order Item Class hook.
	 *
	 * @param string                $class The order item row class.
	 * @param WC_Order_Item_Product $item  The item being displayed.
	 * @param WC_Order              $order The order object.
	 * @since 2.1.0
	 */
	$order_item_class = apply_filters( 'woocommerce_order_item_class', 'order_item', $item, $order );

	if ( ! $email_improvements_enabled ) :
		?>
	<tr class="<?php echo esc_attr( $order_item_class ); ?>">
		<td class="td" style="text-align:<?php echo esc_attr( $text_align ); ?>; vertical-align: middle; font-family: 'Helvetica Neue', Helvetica, Roboto, Arial, sans-serif; word-wrap:break-word;">
			<?php echo wp_kses_post( ' #' . $sku ); ?>
		</td>
		<td class="td" style="text-align:<?php echo esc_attr( $text_align ); ?>; vertical-align: middle; font-family: 'Helvetica Neue', Helvetica, Roboto, Arial, sans-serif; word-wrap:break-word;">
			<?php
			if ( $show_image ) {
				/**
				 * Email Order Item Thumbnail hook.
				 *
				 * @param string                $image The image HTML.
				 * @param WC_Order_Item_Product $item  The item being displayed.
				 * @since 2.1.0
				 */
				echo wp_kses_post( apply_filters( 'woocommerce_order_item_thumbnail', $image, $item ) );
			}

			/**
			 * Order Item Name hook.
			 *
			 * @param string                $item_name The item name HTML.
			 * @param WC_Order_Item_Product $item      The item being displayed.
			 * @since 2.1.0
			 */
			echo wp_kses_post( apply_filters( 'woocommerce_order_item_name', $item->get_name(), $item, false ) );

			/**
			 * Allow other plugins to add additional product information.
			 *
			 * @param int                   $item_id    The item ID.
			 * @param WC_Order_Item_Product $item       The item object.
			 * @param WC_Order              $order      The order object.
			 * @param bool                  $plain_text Whether the email is plain text or not.
			 * @since 2.3.0
			 */
			do_action( 'woocommerce_order_item_meta_start', $item_id, $item, $order, $plain_text );

			wc_display_item_meta(
				$item,
				array(
					'label_before' => '<strong class="wc-item-meta-label" style="float: ' . esc_attr( $text_align ) . '; margin-' . esc_attr( $margin_side ) . ': .25em; clear: both"> We Sell By ',
				)
			);

			/**
			 * Allow other plugins to add additional product information.
			 *
			 * @param int                   $item_id    The item ID.
			 * @param WC_Order_Item_Product $item       The item object.
			 * @param WC_Order              $order      The order object.
			 * @param bool                  $plain_text Whether the email is plain text or not.
			 * @since 2.3.0
			 */
			do_action( 'woocommerce_order_item_meta_end', $item_id, $item, $order, $plain_text );
			?>
		</td>
		<td class="td" style="text-align:<?php echo esc_attr( $text_align ); ?>; vertical-align:middle; font-family: 'Helvetica Neue', Helvetica, Roboto, Arial, sans-serif;">
			<?php echo wp_kses_post( (string) $item_avg_stock ); ?>
		</td>
		<td class="td" style="text-align:<?php echo esc_attr( $text_align ); ?>; vertical-align:middle; font-family: 'Helvetica Neue', Helvetica, Roboto, Arial, sans-serif;">
			<?php
			$qty          = $item->get_quantity();
			$refunded_qty = $order->get_qty_refunded_for_item( $item_id );

			if ( $refunded_qty ) {
				$qty_display = '<del>' . esc_html( $qty ) . '</del> <ins>' . esc_html( $qty - ( $refunded_qty * -1 ) ) . '</ins>';
			} else {
				$qty_display = esc_html( $qty );
			}
			/**
			 * Email Order Item Quantity hook.
			 *
			 * @since 2.4.0
			 * @param string                $qty_display Item quantity.
			 * @param WC_Order_Item_Product $item        Item object.
			 * @return string
			 */
			echo wp_kses_post( apply_filters( 'woocommerce_email_order_item_quantity', $qty_display, $item ) );
			?>
			#
			<?php
			// Unit of measure again, label suppressed: the column heading already says "Sold by".
			wc_display_item_meta(
				$item,
				array(
					'label_before' => '<strong class="wc-item-meta-label" style="display:none;float: ' . esc_attr( $text_align ) . '; margin-' . esc_attr( $margin_side ) . ': .25em; clear: both">',
				)
			);
			?>
		</td>
	</tr>
		<?php
	else :
		?>
	<tr class="<?php echo esc_attr( $order_item_class ); ?>">
		<td class="td font-family text-align-left" style="vertical-align: <?php echo $block_email_editor_enabled ? 'top' : 'middle'; ?>; word-wrap:break-word;">
			<table class="order-item-data" role="presentation">
				<tr>
					<?php
					// Show title/image etc.
					if ( $show_image ) {
						$image_dimensions = wc_get_image_size( $image_size );
						$image_width      = is_array( $image_dimensions ) && isset( $image_dimensions['width'] ) ? absint( $image_dimensions['width'] ) : 48;
						$thumbnail_width  = $image_width + 24;

						/**
						 * Email Order Item Thumbnail hook.
						 *
						 * @param string                $image The image HTML.
						 * @param WC_Order_Item_Product $item  The item being displayed.
						 * @since 2.1.0
						 */
						echo '<td class="email-order-item-thumbnail" style="width: ' . esc_attr( $thumbnail_width ) . 'px;">' . wp_kses_post( apply_filters( 'woocommerce_order_item_thumbnail', $image, $item ) ) . '</td>';
					}
					?>
					<td>
						<?php
						/**
						 * Order Item Name hook.
						 *
						 * @param string                $item_name The item name HTML.
						 * @param WC_Order_Item_Product $item      The item being displayed.
						 * @since 2.1.0
						 */
						$order_item_name = apply_filters( 'woocommerce_order_item_name', $item->get_name(), $item, false );
						echo wp_kses_post( "<h3 style='font-size: inherit;font-weight: inherit;'>{$order_item_name}</h3>" );

						// SKU.
						if ( $show_sku && $sku ) {
							echo wp_kses_post( ' (#' . $sku . ')' );
						}

						/**
						 * Allow other plugins to add additional product information.
						 *
						 * @param int                   $item_id    The item ID.
						 * @param WC_Order_Item_Product $item       The item object.
						 * @param WC_Order              $order      The order object.
						 * @param bool                  $plain_text Whether the email is plain text or not.
						 * @since 2.3.0
						 */
						do_action( 'woocommerce_order_item_meta_start', $item_id, $item, $order, $plain_text );

						$item_meta = wc_display_item_meta(
							$item,
							array(
								'before'       => '',
								'after'        => '',
								'separator'    => '<br>',
								'echo'         => false,
								'label_before' => '<span>',
								'label_after'  => ':</span> ',
							)
						);
						echo '<div class="email-order-item-meta">';
						// Using wp_kses instead of wp_kses_post to remove all block elements.
						echo wp_kses(
							$item_meta,
							array(
								'br'   => array(),
								'span' => array(),
								'a'    => array(
									'href'   => true,
									'target' => true,
									'rel'    => true,
									'title'  => true,
								),
							)
						);
						echo '</div>';

						/**
						 * Allow other plugins to add additional product information.
						 *
						 * @param int                   $item_id    The item ID.
						 * @param WC_Order_Item_Product $item       The item object.
						 * @param WC_Order              $order      The order object.
						 * @param bool                  $plain_text Whether the email is plain text or not.
						 * @since 2.3.0
						 */
						do_action( 'woocommerce_order_item_meta_end', $item_id, $item, $order, $plain_text );
						?>
					</td>
				</tr>
			</table>
		</td>
		<td class="td font-family text-align-<?php echo esc_attr( $price_text_align ); ?>" style="vertical-align:middle;">
			<?php
			$qty          = $item->get_quantity();
			$refunded_qty = $order->get_qty_refunded_for_item( $item_id );

			if ( $refunded_qty ) {
				$qty_display = '<del>' . esc_html( $qty ) . '</del> <ins>' . esc_html( $qty - ( $refunded_qty * -1 ) ) . '</ins>';
			} else {
				$qty_display = esc_html( $qty );
			}
			/**
			 * Email Order Item Quantity hook.
			 *
			 * @since 2.4.0
			 * @param string                $qty_display Item quantity.
			 * @param WC_Order_Item_Product $item        Item object.
			 * @return string
			 */
			$quantity = apply_filters( 'woocommerce_email_order_item_quantity', $qty_display, $item );
			if ( '' !== $quantity ) {
				echo wp_kses_post( '&times;' . $quantity );
			}
			?>
		</td>
		<td class="td font-family text-align-<?php echo esc_attr( $price_text_align ); ?>" style="vertical-align:middle;">
			<?php echo wp_kses_post( $order->get_formatted_line_subtotal( $item ) ); ?>
		</td>
	</tr>
		<?php
	endif;

	if ( $show_purchase_note && $purchase_note ) {
		?>
		<tr>
			<td colspan="<?php echo $email_improvements_enabled ? '3' : '4'; ?>" class="font-family text-align-left" style="vertical-align:middle;">
				<?php
				echo wp_kses_post( wpautop( do_shortcode( $purchase_note ) ) );
				?>
			</td>
		</tr>
		<?php
	}
	?>

<?php endforeach; ?>
